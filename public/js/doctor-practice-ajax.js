(() => {
    'use strict';
    let busy = false;
    const regions = ['main-content-layout', 'service-drawer', 'period-drawer', 'exception-drawer', 'schedule-editor-template', 'practice-page-state', 'patient-access-modal'];
    const belongs = node => node.closest('#main-content-layout, #service-drawer, #exception-drawer');
    const cleanUrl = value => {
        const url = new URL(value, location.href);
        for (const key of ['edit', 'create', 'edit_exception', 'create_exception']) url.searchParams.delete(key);
        return url;
    };
    const readError = async response => {
        const data = response.headers.get('content-type')?.includes('application/json') ? await response.json() : {};
        const error = new Error(({401:'انتهت جلسة الدخول. سجّل دخولك مجددًا.',403:'لا تملك صلاحية تنفيذ هذا الإجراء.',419:'انتهت صلاحية الجلسة. حدّث الصفحة قبل المحاولة مجددًا.'}[response.status]) || (response.status < 500 && data.message) || 'تعذر إكمال الطلب. تحقق من الاتصال وحاول مجددًا.');
        error.errors = data.errors;
        throw error;
    };
    const showError = (error, form) => {
        if (error instanceof TypeError) error.message = 'تعذر تأكيد نتيجة الطلب بسبب الاتصال. تحقق من البيانات المحفوظة قبل إعادة الإرسال.';
        const messages = Object.values(error.errors || {}).flat();
        if (form) {
            const box = document.createElement('div');
            box.dataset.ajaxErrors = '';
            box.className = 'alert alert-danger rounded-4 small';
            box.setAttribute('role', 'alert');
            for (const message of messages.length ? messages : [error.message]) {
                const line = document.createElement('p'); line.className = 'mb-1'; line.textContent = message; box.append(line);
            }
            form.prepend(box);
            for (const name of Object.keys(error.errors || {})) {
                const field = Array.from(form.elements).find(input => input.name.replace(/\[([^\]]+)\]/g, '.$1') === name && input.type !== 'hidden');
                field?.classList.add('is-invalid');
                if (form.id === 'visit-documentation-form' && field) {
                    for (let parent = field.parentElement; parent && parent !== form; parent = parent.parentElement) {
                        if (parent.tagName === 'DETAILS') parent.open = true;
                    }
                }
            }
            box.scrollIntoView({block:'nearest'});
        }
        window.TheraAlerts.error(messages.length ? messages.join('\n') : error.message);
    };
    const closeDrawers = async () => {
        for (const drawer of document.querySelectorAll('.offcanvas')) {
            const instance = window.bootstrap.Offcanvas.getInstance(drawer);
            if (!instance) continue;
            if (drawer.classList.contains('show') || drawer.classList.contains('showing')) {
                await new Promise(resolve => { drawer.addEventListener('hidden.bs.offcanvas', resolve, {once:true}); instance.hide(); });
            }
            instance.dispose();
        }
    };
    const render = async (url, {filterDrawer} = {}) => {
        const response = await fetch(url, {headers:{Accept:'application/json', 'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin', cache:'no-store'});
        if (!response.ok) await readError(response);
        const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
        if (!doc.getElementById('practice-page-state')) throw new Error('تعذر تحديث البيانات؛ ربما انتهت جلسة الدخول. أعد تسجيل الدخول عند الحاجة.');
        if (filterDrawer?.isConnected) doc.getElementById(filterDrawer.id)?.replaceWith(filterDrawer);
        await closeDrawers();
        for (const id of regions) {
            const previous = document.getElementById(id), next = doc.getElementById(id);
            if (!previous && next) { document.body.append(next); continue; }
            if (!previous || !next) continue;
            if (window.jQuery?.fn.select2) window.jQuery(previous).find('.select2-hidden-accessible').each(function () { window.jQuery(this).select2('destroy'); });
            previous.replaceWith(next);
        }
        history.replaceState(null, '', url);
        document.title = doc.title;
        document.querySelectorAll('#app-sidebar a.nav-item').forEach(link => {
            const path = new URL(link.href).pathname;
            const active = path === url.pathname || (path !== '/doctor' && url.pathname.startsWith(path + '/')) || (url.pathname.endsWith('/clinical/visit') && path.endsWith('/clinical/visits'));
            link.classList.toggle('workspace-nav-active', active);
            if (active) link.setAttribute('aria-current', 'page'); else link.removeAttribute('aria-current');
        });
        window.refreshDoctorWorkspaceLayout?.();
        window.refreshDoctorShell?.();
        if (window.initDoctorAppointments) window.initDoctorAppointments();
        else if (window.initDoctorServices) window.initDoctorServices();
        else window.initDoctorSchedule();
        window.initDoctorVisit?.();
        window.initDoctorVisitTemplate?.();
        window.initDoctorPrescription?.();
        window.initDoctorDiagnosticOrder?.();
        window.initDoctorPatients?.();
        window.initDoctorSettings?.();
        window.TheraFilters?.init();
    };
    const lock = submitter => {
        busy = true;
        const main = document.getElementById('main-content-layout');
        main.setAttribute('aria-busy', 'true');
        const controls = Array.from(document.querySelectorAll('#main-content-layout button, #main-content-layout input, #main-content-layout select, #main-content-layout textarea, .offcanvas button, .offcanvas input, .offcanvas select, .offcanvas textarea')).filter(control => !control.disabled);
        controls.forEach(control => control.disabled = true);
        const label = submitter?.innerHTML;
        if (submitter) { submitter.textContent = 'جارٍ الحفظ…'; submitter.setAttribute('aria-busy', 'true'); }
        return () => {
            busy = false; main.removeAttribute('aria-busy'); controls.forEach(control => control.disabled = false);
            if (submitter) { submitter.innerHTML = label; submitter.removeAttribute('aria-busy'); }
        };
    };
    const navigate = async (value, {discardChanges = false, filterDrawer} = {}) => {
        if (busy) return;
        if (!discardChanges && window.doctorScheduleIsDirty?.()) { window.TheraAlerts.info('احفظ تغييرات الجدول أولًا قبل متابعة هذا الإجراء.'); return; }
        if (!discardChanges && window.confirmDoctorVisitLeave && !await window.confirmDoctorVisitLeave()) return;
        const unlock = lock();
        try { await render(new URL(value, location.href), {filterDrawer}); } catch (error) { showError(error); } finally { unlock(); }
    };
    window.TheraPractice = {navigate};
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (event.defaultPrevented || !link || !belongs(link) || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download')) return;
        const url = new URL(link.href);
        if (url.origin !== location.origin || url.pathname !== location.pathname || url.hash) return;
        event.preventDefault();
        if (url.searchParams.has('edit') || url.searchParams.has('edit_exception')) {
            for (const [key, value] of cleanUrl(location.href).searchParams) {
                if (!url.searchParams.has(key)) url.searchParams.set(key, value);
            }
        }
        navigate(url);
    });
    document.addEventListener('submit', async event => {
        const form = event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement) || form.hasAttribute('data-inline-create') || form.hasAttribute('data-inline-editor') || !belongs(form) || form.id === 'membership-switch-form') return;
        event.preventDefault();
        if (busy) return;
        if (window.doctorScheduleIsDirty?.() && form.id !== 'schedule-form') { window.TheraAlerts.info('احفظ تغييرات الجدول أولًا قبل متابعة هذا الإجراء.'); return; }
        if (window.doctorVisitIsDirty?.() && !['visit-documentation-form', 'prescription-form', 'diagnostic-order-form', 'referral-form'].includes(form.id)) { window.TheraAlerts.info('احفظ المسودة الحالية أولًا قبل تنفيذ هذا الإجراء.'); return; }
        form.querySelectorAll('[data-ajax-errors]').forEach(node => node.remove());
        form.querySelectorAll('.is-invalid').forEach(node => node.classList.remove('is-invalid'));
        const data = new FormData(form);
        if (event.submitter?.name) data.append(event.submitter.name, event.submitter.value);
        const action = form.getAttribute('action') || location.href;
        if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') {
            const url = new URL(action, location.href); url.search = new URLSearchParams(data).toString();
            await navigate(url); return;
        }
        const unlock = lock(event.submitter);
        let saved = false;
        try {
            const response = await fetch(action, {method:'POST', body:data, credentials:'same-origin', headers:{Accept:'application/json', 'X-Requested-With':'XMLHttpRequest'}});
            if (!response.ok) await readError(response);
            if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('تعذر تأكيد نتيجة الحفظ. تحقق من الجلسة قبل المحاولة مجددًا.');
            const result = await response.json(); saved = true;
            if (['visit-documentation-form', 'prescription-form', 'diagnostic-order-form', 'referral-form'].includes(form.id)) window.markDoctorVisitSaved?.();
            if (form.id === 'visit-documentation-form' && result.redirect && result.url) { window.location.assign(result.url); return; }
            if (form.hasAttribute('data-ajax-redirect') && result.url) { window.location.assign(result.url); return; }
            await render(form.hasAttribute('data-ajax-follow') && result.url ? new URL(result.url, location.href) : cleanUrl(location.href));
            window.TheraAlerts.success(result.message);
        } catch (error) {
            if (saved) showError(new Error('تم الحفظ، لكن تعذر تحديث العرض. حدّث الصفحة لرؤية النتيجة، ولا تعِد إرسال العملية.'));
            else showError(error, form);
        } finally { unlock(); }
    });
})();
