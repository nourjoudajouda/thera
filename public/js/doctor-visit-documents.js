(() => {
    'use strict';
    const modal = document.getElementById('clinical-document-modal');
    if (!modal) return;
    const body = modal.querySelector('[data-inline-document-body]');
    const buttons = modal.querySelector('[data-inline-document-buttons]');
    const status = modal.querySelector('[data-inline-document-state]');
    const title = document.getElementById('clinical-document-modal-title');
    const dialog = window.bootstrap.Modal.getOrCreateInstance(modal, {backdrop:'static', keyboard:false, focus:false});
    const visitId = modal.dataset.visitId;
    let busy = false, dirty = false, allowHide = false, activeUrl = null, opener = null;
    const localUrl = value => {
        const url = new URL(value, location.href);
        if (url.origin !== location.origin || !url.pathname.startsWith('/doctor/')) throw new Error('رابط المستند غير صالح.');
        return url;
    };
    const fail = async response => {
        const data = response.headers.get('content-type')?.includes('application/json') ? await response.json() : {};
        const error = new Error(({401:'انتهت جلسة الدخول. سجّل الدخول مجددًا.',419:'انتهت صلاحية الجلسة. احفظ عملك ثم أعد فتح الصفحة.'}[response.status]) || (response.status < 500 && data.message) || 'تعذر إكمال الطلب. حاول مجددًا.');
        error.errors = data.errors; throw error;
    };
    const setBusy = value => {
        busy = value; modal.setAttribute('aria-busy', String(value)); body.inert = value;
        modal.querySelectorAll('button').forEach(button => {
            if (value) { button.dataset.inlineWasDisabled = button.disabled ? '1' : '0'; button.disabled = true; }
            else if (button.dataset.inlineWasDisabled === '0') { button.disabled = false; delete button.dataset.inlineWasDisabled; }
        });
    };
    const changed = () => { dirty = true; status.textContent = 'تعديلات غير محفوظة على المستند.'; };
    const refreshLinks = async () => {
        const response = await fetch(location.href, {credentials:'same-origin', cache:'no-store', headers:{Accept:'text/html', 'X-Requested-With':'XMLHttpRequest'}});
        if (!response.ok) await fail(response);
        const page = new DOMParser().parseFromString(await response.text(),'text/html');
        const next = page.getElementById('visit-linked-actions');
        if (!next) throw new Error('تعذر تحديث عناصر الزيارة. أعد فتح الصفحة بعد حفظ توثيقك.');
        document.getElementById('visit-linked-actions')?.replaceWith(next);
        const currentForm = document.getElementById('visit-documentation-form');
        const freshForm = page.getElementById('visit-documentation-form');
        if (currentForm && freshForm) currentForm.dataset.theraConfirmText = freshForm.dataset.theraConfirmText;
        const freshStatus=page.querySelector('[data-system-visit-status]');
        if(freshStatus) document.querySelectorAll('[data-system-visit-status]').forEach(node=>{node.textContent=freshStatus.textContent;node.className=freshStatus.className;});
    };
    const destroySelects = () => {
        if (window.jQuery?.fn.select2) window.jQuery(body).find('.select2-hidden-accessible').each(function () { window.jQuery(this).select2('destroy'); });
    };
    const loadEditor = async value => {
        const url = localUrl(value); url.searchParams.set('inline','1'); url.searchParams.set('inline_visit',visitId);
        const response = await fetch(url, {credentials:'same-origin', cache:'no-store', headers:{Accept:'text/html', 'X-Requested-With':'XMLHttpRequest'}});
        if (!response.ok) await fail(response);
        const page = new DOMParser().parseFromString(await response.text(),'text/html');
        const editor = page.querySelector('[data-inline-document]');
        if (!editor || editor.dataset.inlineVisit !== visitId) throw new Error('تعذر فتح المستند المرتبط بهذه الزيارة. تحقق من الجلسة.');
        destroySelects(); body.replaceChildren(editor); buttons.replaceChildren();
        title.textContent = editor.dataset.inlineTitle; modal.dataset.documentKind=editor.dataset.inlineKind; activeUrl = url;
        modal.querySelector('[data-inline-document-icon]').textContent=({prescription:'assignment_add',lab:'science',radiology:'radiology',referral:'sync_alt',followup:'event_repeat'})[editor.dataset.inlineKind] || 'description';
        const form = editor.querySelector('form');
        if (form) {
            form.dataset.inlineEditor = '1';
            form.removeAttribute('data-ajax-follow');
            form.addEventListener('input', changed); form.addEventListener('change', changed);
            const footer = form.querySelector('.visit-form-footer');
            footer?.querySelectorAll('button[type="submit"]').forEach(button => { button.setAttribute('form',form.id); buttons.append(button); });
            footer?.remove();
        }
        if (window.jQuery?.fn.select2) window.jQuery(editor).find('[data-appointment-select]').each(function () {
            window.jQuery(this).select2({dir:'rtl',width:'100%',dropdownParent:window.jQuery(modal),minimumResultsForSearch:0,language:{noResults:()=>'لا توجد نتائج'}}).on('change.inlineDocument',changed);
        });
        window.initDoctorPrescription?.(); window.initDoctorDiagnosticOrder?.();
        dirty = false; status.textContent = editor.dataset.inlineReadonly === '1' ? 'للقراءة فقط — السجل الأصلي محفوظ.' : 'المسودة مرتبطة بالزيارة الحالية. الحفظ على الخادم.';
        body.scrollTop = 0;
    };
    const showFailure = (error, retry) => {
        status.textContent = error.message;
        if (retry) {
            const box = document.createElement('div'); box.className='alert alert-warning'; box.textContent=error.message;
            const button = document.createElement('button'); button.type='button'; button.className='btn btn-light border'; button.textContent='إعادة فتح المستند';
            button.addEventListener('click', async () => { if (busy) return; setBusy(true); try { await loadEditor(retry); await refreshLinks(); } catch (next) { status.textContent=next.message; } finally { setBusy(false); } });
            destroySelects(); body.replaceChildren(box,button); buttons.replaceChildren();
        }
    };
    const open = async (value, creationForm) => {
        if (busy) return;
        opener = document.activeElement;
        setBusy(true); dirty=false; activeUrl=null; buttons.replaceChildren(); destroySelects();
        const loading=document.createElement('p'); loading.className='inline-document-loading'; loading.textContent='جارٍ فتح المستند…'; body.replaceChildren(loading);
        title.textContent='إجراء مرتبط بالزيارة'; status.textContent=''; dialog.show();
        let createdUrl=null;
        try {
            if (creationForm) {
                const response=await fetch(creationForm.getAttribute('action'),{method:'POST',body:new FormData(creationForm),credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
                if (!response.ok) await fail(response);
                if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('تعذر تأكيد إنشاء المستند؛ تحقق من جلسة الدخول.');
                const result=await response.json(); createdUrl=localUrl(result.url); value=createdUrl;
            }
            await loadEditor(value); await refreshLinks();
        } catch (error) {
            showFailure(error,createdUrl || value);
            if (!createdUrl && !value) { body.textContent=error.message; const retry=document.createElement('button'); retry.type='button'; retry.className='btn btn-light border'; retry.textContent='إعادة المحاولة'; retry.addEventListener('click',()=>open(null,creationForm)); body.append(retry); }
        } finally { setBusy(false); title.setAttribute('tabindex','-1'); title.focus(); }
    };
    const close = async () => {
        if (busy) return;
        if (dirty) {
            const decision=await window.TheraAlerts.confirm({title:'تعديلات غير محفوظة',text:'توجد تعديلات على هذا المستند لم تُحفظ. إغلاق النافذة لا يحذف المسودة المحفوظة ولا يمس نص الزيارة.',confirmButtonText:'إغلاق دون حفظ',icon:'warning'});
            if (!decision.isConfirmed) return;
        }
        allowHide=true; dialog.hide();
    };
    modal.addEventListener('hide.bs.modal',event=>{ if (busy || (dirty && !allowHide)) event.preventDefault(); });
    modal.addEventListener('hidden.bs.modal',()=>{destroySelects();body.replaceChildren();buttons.replaceChildren();dirty=false;allowHide=false;activeUrl=null;if(opener?.isConnected) opener.focus();else document.querySelector('#visit-linked-actions h2')?.focus();});
    document.addEventListener('click',event=>{
        const closing=event.target.closest('[data-inline-close]');
        if (closing?.closest('#clinical-document-modal')) { event.preventDefault(); close(); return; }
        const link=event.target.closest('a[data-inline-open]');
        if (!link || event.defaultPrevented || event.button!==0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target) return;
        event.preventDefault(); open(link.href);
    });
    document.addEventListener('submit',async event=>{
        const form=event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement)) return;
        if (form.hasAttribute('data-inline-create')) { event.preventDefault(); open(null,form); return; }
        if (!form.hasAttribute('data-inline-editor') || !modal.contains(form)) return;
        event.preventDefault(); if (busy) return;
        const data=new FormData(form); if(event.submitter?.name) data.append(event.submitter.name,event.submitter.value);
        form.querySelectorAll('[data-inline-errors]').forEach(node=>node.remove());
        form.querySelectorAll('.is-invalid').forEach(node=>node.classList.remove('is-invalid'));
        setBusy(true); let saved=false;
        try {
            const response=await fetch(form.getAttribute('action'),{method:'POST',body:data,credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
            if(!response.ok) await fail(response);
            if(!response.headers.get('content-type')?.includes('application/json')) throw new Error('تعذر تأكيد الحفظ. تحقق من جلسة الدخول.');
            const result=await response.json(); saved=true;dirty=false;
            await loadEditor(result.url || activeUrl); await refreshLinks();
            status.textContent=result.message; window.TheraAlerts.success(result.message);
        } catch(error) {
            if(saved) showFailure(new Error('تم الحفظ؛ تعذر تحديث النافذة. أعد فتح المستند، ولا تعِد إرسال العملية.'),activeUrl);
            else {
                const messages=Object.values(error.errors||{}).flat(); const box=document.createElement('div');box.dataset.inlineErrors='';box.className='alert alert-danger small';box.setAttribute('role','alert');box.textContent=messages.join('\n')||error.message;form.prepend(box);
                for(const key of Object.keys(error.errors||{})) {const field=Array.from(form.elements).find(field=>field.name.replace(/\[([^\]]+)\]/g,'.$1')===key);field?.classList.add('is-invalid');}
                box.scrollIntoView({block:'nearest'});status.textContent='لم يتم تأكيد الحفظ؛ راجع الحقول والرسالة.';
            }
        } finally { setBusy(false); }
    });
    document.addEventListener('keydown',event=>{
        if(!modal.classList.contains('show') || document.querySelector('.swal2-container')) return;
        if(event.key==='Escape'){event.preventDefault();close();}
        if(event.key==='Tab'){
            const focusable=Array.from(modal.querySelectorAll('a[href],button,input,select,textarea,[tabindex="0"]')).filter(node=>!node.disabled && node.getClientRects().length && !node.closest('[inert]') && !node.classList.contains('select2-hidden-accessible'));
            const first=focusable[0],last=focusable.at(-1);
            if(!first) {event.preventDefault();return;}
            if(event.shiftKey && (document.activeElement===first || !focusable.includes(document.activeElement))){event.preventDefault();last.focus();}
            else if(!event.shiftKey && (document.activeElement===last || !modal.contains(document.activeElement))){event.preventDefault();first.focus();}
        }
    });
    window.addEventListener('beforeunload',event=>{if(dirty){event.preventDefault();event.returnValue='';}});
})();
