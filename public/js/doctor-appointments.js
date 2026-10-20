window.initDoctorAppointments = () => {
    'use strict';
    const $ = id => document.getElementById(id);
    const state = JSON.parse($('practice-page-state').textContent);
    const jq = window.jQuery;
    const financeDrawer = document.querySelector('[data-finance-drawer]');
    if (financeDrawer) {
        if (!financeDrawer.dataset.closeBound) {
            financeDrawer.dataset.closeBound = 'true';
            const selectedAppointment = new URL(location.href).searchParams.get('appointment');
            financeDrawer.addEventListener('hidden.bs.offcanvas', () => {
                const url = new URL(location.href);
                if (url.searchParams.get('appointment') !== selectedAppointment) return;
                url.searchParams.delete('appointment');
                history.replaceState(null, '', url);
            });
        }
        window.bootstrap.Offcanvas.getOrCreateInstance(financeDrawer).show();
    }
    if (jq?.fn.select2) jq('[data-appointment-select]').each(function () {
        if (jq(this).hasClass('select2-hidden-accessible')) return;
        jq(this).select2({dir:'rtl',width:'100%',dropdownParent:jq(this).closest('.offcanvas').length?jq(this).closest('.offcanvas'):jq(document.body),minimumResultsForSearch:0,language:{noResults:()=>'لا توجد نتائج'}});
    });
    window.TheraFilters?.init();
    if (!$('appointment-form')) return;
    const service = $('appointment-service'), facility = $('appointment-facility'), date = $('appointment-date'), time = $('appointment-time'), slots = $('appointment-slots'), save = $('appointment-save'), note = $('appointment-availability-note');
    let serial = 0;
    let chosen = time.value;
    const summary = () => {
        const item = state.services.find(item => item.id === Number(service.value));
        const original = item && state.originalService === item.id;
        $('appointment-summary-service').textContent = item?.name || '—';
        $('appointment-summary-facility').textContent = item?.facility || '—';
        $('appointment-summary-price').textContent = item ? `${original ? state.originalPrice : item.price} ${original ? state.originalCurrency : item.currency}` : '—';
        $('appointment-summary-duration').textContent = item ? `${original ? state.originalDuration : item.duration} دقيقة` : '—';
        $('appointment-summary-time').textContent = time.value ? `${date.value} ${time.value}` : '—';
    };
    const refresh = async () => {
        const current = ++serial;
        chosen = time.value || chosen;
        time.value = ''; slots.replaceChildren(); save.disabled = true; summary();
        if (!service.value || !date.value) { note.textContent = 'اختر المنشأة والخدمة والتاريخ لعرض الأوقات المتاحة.'; return; }
        note.textContent = 'جارٍ حساب الأوقات المتاحة…'; slots.setAttribute('aria-busy','true');
        try {
            const url = new URL(state.availabilityUrl);
            url.searchParams.set('doctor_service_id', service.value); url.searchParams.set('date',date.value);
            if (state.appointmentId) url.searchParams.set('appointment',state.appointmentId);
            const response = await fetch(url, {credentials:'same-origin',headers:{Accept:'application/json'},cache:'no-store'});
            const result = await response.json();
            if (current !== serial || !slots.isConnected) return;
            if (!response.ok) throw new Error(response.status===401?'انتهت جلسة الدخول. سجل دخولك مجددًا.':Object.values(result.errors || {}).flat().join('\n') || 'تعذر حساب الأوقات. حاول مجددًا.');
            note.textContent = `${result.status} — السعة المتبقية: ${result.capacity}. ${result.note}`;
            for (const value of result.slots) {
                const button = document.createElement('button'); button.type='button'; button.className='btn btn-outline-primary font-num'; button.textContent=value;
                const select = () => { time.value=value; chosen=value; save.disabled=false; slots.querySelectorAll('button').forEach(item=>{const selected=item===button;item.classList.toggle('btn-primary',selected);item.classList.toggle('btn-outline-primary',!selected);item.setAttribute('aria-pressed',String(selected));});summary(); };
                button.setAttribute('aria-pressed','false');button.addEventListener('click',select); slots.append(button);
                if (value===chosen) select();
            }
        } catch (error) { if(current===serial && slots.isConnected) note.textContent=error instanceof TypeError?'تعذر الاتصال. اضغط تحديث الأوقات لإعادة المحاولة.':error.message; }
        finally { if(current===serial) slots.removeAttribute('aria-busy'); }
    };
    const filterServices = (initial = false) => {
        const selected = service.value;
        service.replaceChildren(new Option('اختر الخدمة',''));
        for (const item of state.services.filter(item=>String(item.facilityId)===facility.value)) service.add(new Option(item.name,String(item.id)));
        service.value = initial ? selected : '';
        jq?.(service).trigger('change.select2');
        if (!initial) chosen='';
        refresh();
    };
    if (service.value && !facility.value) facility.value=String(state.services.find(item=>item.id===Number(service.value))?.facilityId || '');
    jq?.(facility).trigger('change.select2');
    facility.addEventListener('change',()=>filterServices());jq?.(facility).on('select2:select',()=>filterServices());
    service.addEventListener('change',()=>{chosen='';time.value='';refresh();});jq?.(service).on('select2:select',()=>{chosen='';time.value='';refresh();});
    date.addEventListener('change',()=>{chosen='';time.value='';refresh();});
    $('refresh-appointment-slots').addEventListener('click',refresh);
    $('appointment-form').addEventListener('submit',event=>{if(!time.value){event.preventDefault();window.TheraAlerts.info('اختر وقتًا متاحًا أولًا.');}});
    if ($('appointment-contact') && jq?.fn.select2) {
        jq($('appointment-contact')).select2({dir:'rtl',width:'100%',minimumInputLength:2,language:{inputTooShort:()=>'اكتب حرفين للبحث',noResults:()=>'لا يوجد مراجع سابق مطابق؛ أدخل الاسم والهاتف أدناه.',searching:()=>'جارٍ البحث…'},ajax:{url:state.contactsUrl,dataType:'json',delay:300,data:params=>({q:params.term})}})
            .on('select2:select',event=>{$('appointment-patient').value=event.params.data.name;$('appointment-phone').value=event.params.data.phone;});
    }
    filterServices(true);
};
window.initDoctorAppointments();
