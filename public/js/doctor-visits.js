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
        const mirror = document.querySelector('[data-visit-save-mirror]');
        if (mirror && indicator) mirror.textContent = indicator.textContent;
    };
    window.markDoctorVisitSaved = () => setDirty(false);
    window.doctorVisitIsDirty = () => dirty;
    window.confirmDoctorVisitLeave = async () => {
        if (!dirty) return true;
        const result = await window.TheraAlerts.confirm({title:'تعديلات غير محفوظة',text:'التعديلات الحالية لم تُحفظ على الخادم. ارجع لحفظ المسودة، أو غادر دون حفظ.',confirmButtonText:'مغادرة دون حفظ',icon:'warning'});
        return result.isConfirmed;
    };
    window.initDoctorVisit = () => {
        const form = document.getElementById('visit-documentation-form') || document.getElementById('prescription-form') || document.getElementById('diagnostic-order-form') || document.getElementById('referral-form');
        if (form?.dataset.dirtyBound) return;
        dirty = false;
        if (!form || form.querySelector('fieldset')?.disabled) return;
        form.dataset.dirtyBound = 'true';
        setDirty(false);
        const reviewButton = document.getElementById('visit-review-open');
        const review = document.getElementById('visit-review');
        const reviewError = document.getElementById('visit-review-error');
        if (reviewButton && review) {
            const clearReview = () => {
                review.hidden = true;
                reviewError.hidden = true;
                ['visit-assessment', 'visit-diagnosis'].forEach(id => document.getElementById(id)?.removeAttribute('aria-invalid'));
                form.querySelectorAll('[data-visit-section-state]').forEach(state => {
                    const field = state.closest('details').querySelector('textarea');
                    state.textContent = field.value.trim() ? 'توجد معلومات مدخلة · افتح للمراجعة' : 'اختياري · افتح عند الحاجة';
                });
            };
            form.addEventListener('input', clearReview);
            form.addEventListener('change', clearReview);
            window.jQuery?.(form).find('select').on('change.visitReview', clearReview);
            form.addEventListener('invalid', event => {
                for (let parent = event.target.parentElement; parent && parent !== form; parent = parent.parentElement) {
                    if (parent.tagName === 'DETAILS') parent.open = true;
                }
            }, true);
            reviewButton.addEventListener('click', () => {
                if (!form.reportValidity()) return;
                const assessment = document.getElementById('visit-assessment');
                const diagnosis = document.getElementById('visit-diagnosis');
                if (!assessment.value.trim() && !diagnosis.value.trim()) {
                    reviewError.hidden = false;
                    assessment.setAttribute('aria-invalid', 'true');
                    diagnosis.setAttribute('aria-invalid', 'true');
                    assessment.focus();
                    return;
                }
                const content = document.getElementById('visit-review-content');
                content.replaceChildren();
                const fields = form.elements ? Array.from(form.elements).filter(field => ['TEXTAREA', 'SELECT'].includes(field.tagName) || (field.tagName === 'INPUT' && field.type === 'number')) : form.querySelectorAll('textarea, input[type="number"], select');
                fields.forEach(field => {
                    const label = form.querySelector(`label[for="${field.id}"]`) || document.querySelector(`label[for="${field.id}"]`);
                    if (!label || !field.value.trim()) return;
                    const title = document.createElement('dt');
                    const value = document.createElement('dd');
                    const labelCopy = label.cloneNode ? label.cloneNode(true) : label;
                    labelCopy.querySelectorAll?.('[aria-hidden="true"]').forEach(icon => icon.remove());
                    title.textContent = labelCopy.textContent.trim();
                    value.textContent = field.tagName === 'SELECT' ? field.selectedOptions[0].textContent : field.value;
                    content.append(title, value);
                });
                review.hidden = false;
                document.getElementById('visit-review-heading').focus();
                review.scrollIntoView({block:'start', behavior:'smooth'});
            });
            document.getElementById('visit-review-edit').addEventListener('click', () => {
                review.hidden = true;
                document.getElementById('visit-complaint').focus();
            });
            document.getElementById('visit-review-confirm').addEventListener('click', () => {
                if (!review.hidden) form.requestSubmit(document.getElementById('visit-close-submit'));
            });
        }
        form.addEventListener('input', () => setDirty(true));
        form.addEventListener('change', () => setDirty(true));
        window.jQuery?.(form).find('select').on('change.visitDirty', () => setDirty(true));
    };
    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!dirty || !link || link.hasAttribute('data-inline-open') || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download')) return;
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
