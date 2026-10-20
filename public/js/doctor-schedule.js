window.initDoctorSchedule = () => {
    'use strict';
    const state = JSON.parse(document.getElementById('practice-page-state').textContent);
    if (!state || !window.bootstrap) return;
    const $ = id => document.getElementById(id);
    const clone = value => JSON.parse(JSON.stringify(value));
    const clean = rows => Array.isArray(rows) ? rows.filter(row => row && typeof row === 'object' && typeof row.start === 'string' && typeof row.end === 'string' && Array.isArray(row.service_ids) && Array.isArray(row.breaks)).map(row => ({weekday:Number(row.weekday),start:row.start,end:row.end,service_ids:row.service_ids.map(Number),breaks:row.breaks.filter(b => b && typeof b.start === 'string' && typeof b.end === 'string').map(b => ({start:b.start,end:b.end}))})) : [];
    let periods = clean(state.periods);
    let specialPeriods = clean(state.exceptionPeriods);
    let editingIndex = null;
    let editorSerial = 0;
    const periodDrawer = window.bootstrap.Offcanvas.getOrCreateInstance($('period-drawer'));
    const exceptionDrawer = window.bootstrap.Offcanvas.getOrCreateInstance($('exception-drawer'));
    const minutes = time => { const [h,m] = time.split(':').map(Number); return h * 60 + m; };
    const element = (tag,text,className = '') => { const node = document.createElement(tag); if(text !== undefined) node.textContent = text; node.className = className; return node; };
    const initSelects = root => {
        if (!window.jQuery?.fn.select2) return;
        root.querySelectorAll('[data-schedule-select], [data-period-services]').forEach(select => {
            if (window.jQuery(select).hasClass('select2-hidden-accessible')) return;
            window.jQuery(select).select2({dir:'rtl',width:'100%',minimumResultsForSearch:0,placeholder:select.multiple?'ابحث واختر الخدمات...':undefined,dropdownParent:window.jQuery(select.closest('.offcanvas') || document.body),language:{noResults:()=>'لا توجد نتائج',searching:()=>'جارٍ البحث...'}});
        });
    };
    const signature = () => JSON.stringify([periods,$('thera-capacity').value,$('schedule-active').checked]);
    const initialSignature = signature();
    const dirty = () => Boolean(state.hasUnsavedInput) || signature() !== initialSignature;
    window.doctorScheduleIsDirty = dirty;
    const markDirty = () => { $('schedule-unsaved').classList.toggle('d-none',!dirty()); refreshGuide(); };
    function guideState() {
        const allPeriods=[...periods,...clean(state.specialPeriods)];
        const steps = {periods:allPeriods.length>0,capacity:Number.isInteger(Number($('thera-capacity').value)) && Number($('thera-capacity').value)>0 && Number($('thera-capacity').value)<=500,active:$('schedule-active').checked,saved:state.hasSavedSchedule && !dirty()};
        const linkedActiveService = allPeriods.some(period=>period.service_ids.some(id=>state.services.some(service=>service.id===id && service.is_active)));
        if(!state.membershipId)return{steps,message:'أضف منشأة أو أرسل طلب انضمام أولًا، ثم جهّز خدماتها ودوامها.',target:'facilities',action:'إدارة المنشآت المرتبطة'};
        if(!state.canEdit)return{steps,message:'اعتماد العضوية وتفعيل المنشأة مطلوبان قبل إعداد الدوام.',target:'facilities',action:'مراجعة المنشأة'};
        if(!state.services.length)return{steps,message:'الخطوة التالية: أضف خدمة لهذه المنشأة من صفحة الخدمات والأسعار.',target:'services',action:'إضافة الخدمات'};
        if(!steps.periods)return{steps,message:'الخطوة التالية: اضغط «إضافة فترة» بجانب يوم العمل، وحدد الساعات واختر الخدمة.',target:'weekly-schedule-section',action:'أضف فترة العمل'};
        if(!steps.capacity)return{steps,message:'الخطوة التالية: أدخل السعة اليومية من 1 إلى 500 مريض. السعة صفر تمنع ظهور أوقات المعاينة.',target:'thera-capacity',action:'تحديد السعة اليومية'};
        if(!steps.active)return{steps,message:'الخطوة التالية: شغّل «تفعيل الجدول»، ثم اضغط «حفظ الجدول».',target:'schedule-active',action:'الانتقال إلى تفعيل الجدول'};
        if(!steps.saved)return{steps,message:'الخطوة التالية: اضغط «حفظ الجدول» لتطبيق الفترات والسعة والتفعيل قبل المعاينة.',target:'schedule-save-button',action:'الانتقال إلى حفظ الجدول'};
        if(!state.bookingPrerequisites)return{steps,message:'الجدول محفوظ، لكن المعاينة تحتاج اكتمال توثيق الطبيب واعتماد العضوية وتفعيل حجوزات المنشأة. راجع المتطلبات أعلاه.',target:'schedule-guide',action:'مراجعة المتطلبات'};
        if(!linkedActiveService)return{steps,message:'الجدول محفوظ، لكن لا توجد خدمة مفعّلة مرتبطة بفتراته. فعّل الخدمة واربطها بالفترة.',target:'services',action:'مراجعة الخدمات'};
        return{steps,message:'الجدول محفوظ وجاهز لمعاينة مبدئية. اختر التاريخ والخدمة ثم اضغط «معاينة».',target:'schedule-preview-panel',action:'معاينة أيام التوفر',ready:true};
    }
    function refreshGuide() {
        const current=guideState();
        for(const [key,done] of Object.entries(current.steps)) {
            const icon=document.querySelector(`[data-setup-icon="${key}"]`),label=document.querySelector(`[data-setup-state="${key}"]`);
            if(icon){icon.textContent=done?'check_circle':'radio_button_unchecked';icon.closest('.setup-step').classList.toggle('is-complete',done);}
            if(label)label.textContent=done?(key==='saved'?'تم الحفظ':'مكتمل'):(key==='saved'?'يحتاج حفظ الجدول':'لم يكتمل بعد');
        }
        if($('schedule-next-step'))$('schedule-next-step').textContent=current.message;
        if($('schedule-guide-action'))$('schedule-guide-action').textContent=current.action;
        const note=$('preview-readiness-note');
        if(note){
            const selected=state.services.find(service=>service.id===Number($('preview-service').value));
            note.textContent=current.ready && selected && !selected.is_active?'الخدمة المختارة متوقفة. فعّلها من صفحة الخدمات والأسعار، أو اختر خدمة مفعّلة.':current.message;
            note.classList.toggle('alert-success',Boolean(current.ready && (!selected || selected.is_active)));
            note.classList.toggle('alert-info',!current.ready || Boolean(selected && !selected.is_active));
        }
        const result=$('schedule-preview-result');
        if(result){
            let warning=result.querySelector('[data-preview-stale]');
            if(!warning){warning=document.createElement('p');warning.dataset.previewStale='';warning.className='alert alert-warning rounded-4 small';warning.textContent='هذه النتيجة للجدول المحفوظ قبل التغييرات الحالية. احفظ الجدول ثم أعد المعاينة لتحديث الأوقات.';result.prepend(warning);}
            warning.hidden=!dirty();
        }
    }
    function goToTarget(id) {
        if(id==='facilities'){window.location.assign(window.doctorRoutes.profile+'?tab=facilities');return;}
        if(id==='services'){window.location.assign(window.doctorRoutes.services+'?membership='+state.membershipId);return;}
        const target=$(id);if(!target)return;
        const reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        target.scrollIntoView({behavior:reduced?'instant':'smooth',block:'center'});
        target.focus({preventScroll:true});
    }
    const serializeWeekly = () => { $('weekly-periods-payload').value = JSON.stringify(periods); markDirty(); };
    const serviceNames = ids => ids.map(id => state.services.find(service=>service.id===Number(id))?.name || 'خدمة غير متاحة').join('، ');
    const breakSummary = breaks => breaks.length ? breaks.map(b=>`${b.start} – ${b.end}`).join('، ') : 'لا توجد استراحات';
    const actionButton = (label,handler,tone='light') => { const button=element('button',label,`btn btn-${tone} border btn-sm`); button.type='button'; button.disabled=!state.canEdit; button.addEventListener('click',handler); return button; };
    const removePeriod = async index => {
        const result = await window.TheraAlerts.confirm({title:'حذف فترة الدوام؟',text:'ستُحذف من تغييراتك الحالية. اضغط حفظ الجدول لتطبيق التغيير.'});
        if (result.isConfirmed) { periods.splice(index,1); renderWeekly(); }
    };
    const renderWeekly = () => {
        const tbody=$('weekly-schedule-body'), mobile=$('schedule-mobile-list'); tbody.replaceChildren(); mobile.replaceChildren();
        state.days.forEach((label,day)=>{
            const rows=periods.map((period,index)=>({period,index})).filter(row=>row.period.weekday===day).sort((a,b)=>a.period.start.localeCompare(b.period.start));
            const card=element('article',undefined,'schedule-day-card');
            const heading=element('div',undefined,'d-flex justify-content-between align-items-center gap-2 mb-2');
            heading.append(element('h3',label,'h6 fw-bold mb-0'),actionButton('إضافة فترة',()=>openPeriod(null,day))); card.append(heading);
            if (!rows.length) card.append(element('p','عطلة — لا يوجد دوام','small text-secondary mb-0'));
            for (const {period,index} of rows) {
                const row=element('tr'); const dayCell=element('td',label,'fw-bold text-nowrap');
                const times=element('td',`${period.start} – ${period.end}`,'schedule-time text-nowrap');
                const details=element('td'); details.append(element('p',serviceNames(period.service_ids),'small fw-bold mb-1 text-break'),element('p',breakSummary(period.breaks),'schedule-break-label mb-0'));
                const actions=element('td'); const box=element('div',undefined,'schedule-actions');
                box.append(actionButton('تعديل',()=>openPeriod(index,day)),actionButton('حذف',()=>removePeriod(index),'outline-danger')); actions.append(box); row.append(dayCell,times,details,actions); tbody.append(row);
                const periodCard=element('div',undefined,'border-top pt-3 mt-3'); periodCard.append(element('p',`${period.start} – ${period.end}`,'schedule-time fw-bold small mb-2'),element('p',serviceNames(period.service_ids),'small text-break mb-2'),element('p',breakSummary(period.breaks),'schedule-break-label mb-3'));
                const mobileActions=element('div',undefined,'schedule-actions'); mobileActions.append(actionButton('تعديل',()=>openPeriod(index,day)),actionButton('حذف',()=>removePeriod(index),'outline-danger')); periodCard.append(mobileActions); card.append(periodCard);
            }
            const tr=element('tr'); tr.append(element('td',rows.length?'':label,'fw-bold text-nowrap'),element('td',rows.length?'':'عطلة','small text-secondary'),element('td','')); const td=element('td'); td.append(actionButton('إضافة فترة',()=>openPeriod(null,day))); tr.append(td); tbody.append(tr); mobile.append(card);
        });
        let total=0; for(const period of periods) total+=minutes(period.end)-minutes(period.start)-period.breaks.reduce((sum,b)=>sum+minutes(b.end)-minutes(b.start),0);
        $('summary-days').textContent = new Set(periods.map(period=>period.weekday)).size;
        $('summary-hours').textContent = Math.round(total / 6) / 10;
        $('summary-breaks').textContent = periods.reduce((sum,period)=>sum+period.breaks.length,0);
        serializeWeekly();
    };
    const addBreakRow = (list,periodBreak={start:'',end:''}) => {
        const row=element('div',undefined,'schedule-break-row');
        for (const field of ['start','end']) { const input=document.createElement('input'); input.type='time'; input.className='form-control schedule-time'; input.dataset.breakField=field; input.value=periodBreak[field]; input.setAttribute('aria-label',field==='start'?'بداية الاستراحة':'نهاية الاستراحة'); row.append(input); }
        const remove=actionButton('حذف',()=>row.remove(),'outline-danger'); remove.setAttribute('aria-label','حذف الاستراحة'); row.append(remove); list.append(row);
    };
    const renderEditor = (container,period,{special=false,index=0}={}) => {
        const editor=$('schedule-editor-template').content.firstElementChild.cloneNode(true); editor.dataset.editor=''; const serial=++editorSerial;
        for(const field of ['start','end']) { const input=editor.querySelector(`[data-${field}]`); input.value=period[field]; input.id=`schedule-editor-${serial}-${field}`; editor.querySelector(`[data-${field}-label]`).htmlFor=input.id; }
        const select=editor.querySelector('[data-period-services]'); select.id=`schedule-editor-${serial}-services`; editor.querySelector('[data-services-label]').htmlFor=select.id;
        Array.from(select.options).forEach(option=>option.selected=period.service_ids.includes(Number(option.value)));
        const breaks=editor.querySelector('[data-break-list]'); period.breaks.forEach(b=>addBreakRow(breaks,b));
        editor.querySelector('[data-add-break]').addEventListener('click',()=>{if(breaks.children.length>=10){window.TheraAlerts.info('الحد الأقصى 10 استراحات في الفترة.');return;}addBreakRow(breaks);});
        const remove=editor.querySelector('[data-remove-special]'); remove.classList.toggle('d-none',!special); remove.addEventListener('click',()=>{specialPeriods=readEditors($('exception-period-editors'));specialPeriods.splice(index,1);renderSpecial();});
        container.append(editor); initSelects(editor);
    };
    const readEditors = container => Array.from(container.querySelectorAll('[data-editor]')).map(editor=>({weekday:0,start:editor.querySelector('[data-start]').value,end:editor.querySelector('[data-end]').value,service_ids:Array.from(editor.querySelector('[data-period-services]').selectedOptions).map(option=>Number(option.value)),breaks:Array.from(editor.querySelectorAll('.schedule-break-row')).map(row=>({start:row.querySelector('[data-break-field="start"]').value,end:row.querySelector('[data-break-field="end"]').value}))}));
    const validateEditors = rows => {
        for(const period of rows) {
            if(!period.start || !period.end || period.end<=period.start) { window.TheraAlerts.error('حدد بداية ونهاية صحيحتين للفترة ضمن اليوم نفسه.'); return false; }
            if(!period.service_ids.length) { window.TheraAlerts.error('اختر خدمة واحدة على الأقل للفترة.'); return false; }
            let last=period.start; for(const b of [...period.breaks].sort((a,b)=>a.start.localeCompare(b.start))) { if(!b.start || !b.end || b.end<=b.start || b.start<last || b.end>period.end) {window.TheraAlerts.error('تحقق من الاستراحات: يجب أن تكون داخل الدوام وبدون تداخل.');return false;} last=b.end; }
        }
        return true;
    };
    const openPeriod = (index,day) => {
        editingIndex=index; $('period-drawer-title').textContent=index===null?'إضافة فترة دوام':'تعديل فترة دوام';
        const period=index===null?{weekday:day,start:'',end:'',service_ids:[],breaks:[]}:clone(periods[index]);
        $('period-weekday').value=period.weekday; window.jQuery?.($('period-weekday')).trigger('change.select2');
        $('weekly-period-editor').replaceChildren();renderEditor($('weekly-period-editor'),period);periodDrawer.show();
    };
    const applyPeriod = () => {
        const rows=readEditors($('weekly-period-editor')); if(!validateEditors(rows))return;
        const period=rows[0];period.weekday=Number($('period-weekday').value);
        if(periods.some((p,index)=>index!==editingIndex && p.weekday===period.weekday && p.start<period.end && p.end>period.start)) {window.TheraAlerts.error('لا يمكن تداخل فترتين في اليوم نفسه.');return;}
        if(editingIndex===null) {if(periods.length>=28){window.TheraAlerts.info('الحد الأقصى 28 فترة أسبوعية.');return;} periods.push(period);}else periods[editingIndex]=period;
        renderWeekly();periodDrawer.hide();
    };
    const renderSpecial = () => { const container=$('exception-period-editors');container.replaceChildren();specialPeriods.forEach((period,index)=>renderEditor(container,period,{special:true,index})); };
    const refreshException = () => { const special=$('exception-type').value==='special_hours';$('exception-special-fields').classList.toggle('d-none',!special); if(special && !specialPeriods.length) {specialPeriods.push({weekday:0,start:'',end:'',service_ids:[],breaks:[]});renderSpecial();} };
    initSelects(document);renderWeekly();renderSpecial();refreshException();
    document.querySelectorAll('[data-guide-target]').forEach(button=>button.addEventListener('click',()=>goToTarget(button.dataset.guideTarget)));
    $('schedule-guide-action')?.addEventListener('click',()=>goToTarget(guideState().target));
    $('preview-service').addEventListener('change',refreshGuide);window.jQuery?.($('preview-service')).on('select2:select',refreshGuide);
    $('apply-period-button').addEventListener('click',applyPeriod);
    $('thera-capacity').addEventListener('input',markDirty);$('schedule-active').addEventListener('change',markDirty);
    $('schedule-form').addEventListener('submit',serializeWeekly);
    $('exception-type').addEventListener('change',refreshException);window.jQuery?.($('exception-type')).on('select2:select',refreshException);
    $('add-special-period').addEventListener('click',()=>{specialPeriods=readEditors($('exception-period-editors'));if(specialPeriods.length>=4){window.TheraAlerts.info('الحد الأقصى أربع فترات خاصة في اليوم.');return;}specialPeriods.push({weekday:0,start:'',end:'',service_ids:[],breaks:[]});renderSpecial();});
    $('exception-form').addEventListener('submit',event=>{if(dirty()){event.preventDefault();window.TheraAlerts.info('احفظ تغييرات الجدول أولًا قبل حفظ الاستثناءات.');return;}const rows=$('exception-type').value==='special_hours'?readEditors($('exception-period-editors')):[];if($('exception-type').value==='special_hours' && (!rows.length || !validateEditors(rows))){event.preventDefault();return;}$('exception-periods-payload').value=JSON.stringify(rows);});
    let switchingMembership=false;
    const switchMembership=async()=>{if(switchingMembership)return;switchingMembership=true;try{if(dirty()){const result=await window.TheraAlerts.confirm({title:'الانتقال إلى منشأة أخرى؟',text:'ستفقد تغييرات الجدول غير المحفوظة.'});if(!result.isConfirmed){$('schedule-membership').value=state.membershipId;window.jQuery?.($('schedule-membership')).trigger('change.select2');return;}}await window.TheraPractice.navigate(window.doctorRoutes.schedule+'?membership='+encodeURIComponent($('schedule-membership').value),{discardChanges:true});}finally{switchingMembership=false;}};
    $('schedule-membership').addEventListener('change',switchMembership);window.jQuery?.($('schedule-membership')).on('select2:select',switchMembership);
    $('add-exception-button').addEventListener('click',event=>{if(dirty()){event.preventDefault();event.stopPropagation();window.TheraAlerts.info('احفظ تغييرات الجدول أولًا قبل فتح الاستثناءات.');return;}if(state.editingException){event.preventDefault();event.stopPropagation();window.TheraPractice.navigate(window.doctorRoutes.schedule+'?membership='+state.membershipId+'&create_exception=1');}});
    document.querySelector('input[name="preview"]')?.closest('form').addEventListener('submit',event=>{if(dirty()){event.preventDefault();window.TheraAlerts.info('احفظ تغييرات الجدول أولًا حتى تظهر في معاينة التوفر.');}});
    if(state.openException || new URLSearchParams(window.location.search).has('create_exception'))exceptionDrawer.show();
};
window.initDoctorSchedule();
