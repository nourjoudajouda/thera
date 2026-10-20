(() => {
    'use strict';

    if (window.TheraAlerts) return;

    const popup = options => {
        if (!window.Swal) {
            return Promise.reject(new Error('تعذر تحميل نافذة التأكيد. حدّث الصفحة وحاول مجددًا.'));
        }

        return Promise.resolve(window.Swal.fire({
            width: 460,
            heightAuto: false,
            buttonsStyling: false,
            confirmButtonText: 'حسنًا',
            cancelButtonText: 'إلغاء',
            returnFocus: true,
            customClass: {
                container: 'thera-alert-container',
                popup: 'thera-alert-popup',
                title: 'thera-alert-title',
                htmlContainer: 'thera-alert-text',
                actions: 'thera-alert-actions',
                confirmButton: 'thera-alert-button thera-alert-confirm',
                cancelButton: 'thera-alert-button thera-alert-cancel',
            },
            ...options,
        }));
    };

    window.TheraAlerts = {
        confirm: ({ title = 'تأكيد الإجراء', text = '', confirmButtonText = 'نعم، تأكيد', icon = 'question' } = {}) =>
            popup({ titleText: title, text, icon, confirmButtonText, showCancelButton: true, focusCancel: true, allowOutsideClick: false }),
        success: (text, title = 'تم بنجاح') => popup({ titleText: title, text, icon: 'success' }),
        error: (text, title = 'تعذر إكمال الإجراء') => popup({ titleText: title, text, icon: 'error' }),
        info: (text, title = 'تنبيه') => popup({ titleText: title, text, icon: 'info' }),
    };

    const pendingForms = new WeakSet();
    const confirmedForms = new WeakSet();

    document.addEventListener('submit', async event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('[data-thera-confirm], [data-approval-confirm]')) return;
        if (form.dataset.theraConfirmIntent && event.submitter?.value !== form.dataset.theraConfirmIntent) return;
        if (confirmedForms.delete(form)) return;

        event.preventDefault();
        if (pendingForms.has(form)) return;
        pendingForms.add(form);
        const submitter = event.submitter;

        try {
            const result = await window.TheraAlerts.confirm({
                title: form.dataset.theraConfirm || 'اعتماد هذا الطلب؟',
                text: form.dataset.theraConfirmText || 'سيتم حفظ القرار وتحديث البيانات المرتبطة بالطلب.',
                confirmButtonText: form.dataset.theraConfirmButton || 'نعم، تأكيد',
                icon: form.dataset.theraConfirmIcon || 'question',
            });
            if (result.isConfirmed && form.isConnected) {
                confirmedForms.add(form);
                if (submitter && submitter.form === form) form.requestSubmit(submitter);
                else form.requestSubmit();
                confirmedForms.delete(form);
            }
        } catch (error) {
            // Keep the operation unsubmitted when the dialog cannot load.
            let message = form.querySelector('[data-thera-alert-error]');
            if (!message) {
                message = document.createElement('p');
                message.dataset.theraAlertError = '';
                message.setAttribute('role', 'alert');
                message.style.color = '#dc2626';
                form.append(message);
            }
            message.textContent = error.message;
        } finally {
            pendingForms.delete(form);
        }
    }, true);

    const showMessages = () => {
        const messages = window.theraAlertMessages || {};
        if (messages.errors?.length) window.TheraAlerts.error(messages.errors.join('\n')).catch(() => {});
        else if (messages.status) window.TheraAlerts.success(messages.status).catch(() => {});
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', showMessages, { once: true });
    else showMessages();
})();
