(() => {
    let dirty = false;
    let leaving = false;
    const setDirty = value => {
        dirty = value;
        const indicator = document.getElementById('visit-save-state');
        if (indicator) {
            indicator.textContent = value ? 'تغييرات غير محفوظة — احفظ المسودة قبل المغادرة' : indicator.dataset.savedLabel;
            indicator.classList.toggle('visit-unsaved', value);
        }
    };
    window.markDoctorVisitSaved = () => setDirty(false);
    window.doctorVisitIsDirty = () => dirty;
    window.confirmDoctorVisitLeave = async () => {
        if (!dirty) return true;
        const result = await window.TheraAlerts.confirm({title:'تعديلات غير محفوظة',text:'التعديلات الحالية لم تُحفظ على الخادم. ارجع لحفظ المسودة، أو غادر دون حفظ.',confirmButtonText:'مغادرة دون حفظ',icon:'warning'});
        return result.isConfirmed;
    };
    window.initDoctorVisit = () => {
        const form = document.getElementById('visit-documentation-form') || document.getElementById('prescription-form');
        if (form?.dataset.dirtyBound) return;
        dirty = false;
        if (!form || form.querySelector('fieldset')?.disabled) return;
        form.dataset.dirtyBound = 'true';
        form.addEventListener('input', () => setDirty(true));
        form.addEventListener('change', () => setDirty(true));
        window.jQuery?.(form).find('select').on('change.visitDirty', () => setDirty(true));
    };
    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!dirty || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download')) return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || !['http:', 'https:'].includes(url.protocol) || (url.pathname === location.pathname && (url.hash || link.closest('#main-content-layout')))) return;
        event.preventDefault();
        if (leaving) return;
        leaving = true;
        try {
            if (await window.confirmDoctorVisitLeave()) { dirty = false; window.location.assign(url.href); }
        } finally { leaving = false; }
    });
    window.addEventListener('beforeunload', event => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
    window.initDoctorVisit();
})();
