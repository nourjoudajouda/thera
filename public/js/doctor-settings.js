(() => {
    'use strict';
    window.initDoctorPaymentPolicy = form => {
        const editor = form.querySelector?.('[data-payment-policy-editor]');
        if (!editor || editor.dataset.paymentBound) return;
        editor.dataset.paymentBound = '1';
        const percent = editor.querySelector('#deposit-percent');
        const row = editor.querySelector('#deposit-percent-row');
        const previewDeposit = editor.querySelector('#payment-example-deposit');
        const previewRemaining = editor.querySelector('#payment-example-remaining');
        const previewTiming = editor.querySelector('#payment-example-timing');
        const timingLabels = {before_visit:'قبل الزيارة', at_arrival:'عند الوصول', after_visit:'بعد الزيارة'};
        const update = () => {
            const policy = editor.querySelector('input[name="payment_policy[type]"]:checked')?.value || 'none';
            const timing = editor.querySelector('input[name="payment_policy[remaining_timing]"]:checked')?.value || 'before_visit';
            percent.disabled = policy !== 'percentage';
            row.classList.toggle('opacity-50', percent.disabled);
            const deposit = policy === 'none' ? 0 : policy === 'full' ? 100 : Math.max(1, Math.min(99, Number(percent.value) || 1));
            previewDeposit.textContent = deposit.toFixed(2);
            previewRemaining.textContent = (100 - deposit).toFixed(2);
            previewTiming.textContent = policy === 'full' ? 'مدفوع بالكامل' : timingLabels[timing];
            editor.querySelectorAll('input[name="payment_policy[remaining_timing]"]').forEach(input => { input.disabled = policy === 'full'; });
            const timingBlock = editor.querySelector('#payment-remaining-before_visit')?.closest('.setting-row');
            timingBlock?.classList.toggle('opacity-50', policy === 'full');
            editor.querySelectorAll('input[name="payment_policy[allow_early_payment]"]').forEach(input => { input.disabled = policy === 'full'; });
            editor.querySelector('#early-payment-row')?.classList.toggle('opacity-50', policy === 'full');
        };
        editor.addEventListener('input', update);
        editor.addEventListener('change', update);
        update();
    };
    window.initDoctorSettings = () => {
        const form = document.getElementById('doctor-settings-form');
        if (!form || form.dataset.settingsBound) return;
        form.dataset.settingsBound = '1';
        window.initDoctorPaymentPolicy(form);
        const main = document.getElementById('main-content-layout');
        const status = document.getElementById('settings-save-state');
        const dirty = event => { if (event?.target?.getAttribute?.('form') === 'doctor-stamp-upload' || ['setting-language','setting-theme'].includes(event?.target?.id)) return; status.textContent = 'توجد تغييرات لم تُحفظ بعد.'; form.dataset.settingsDirty = '1'; };
        form.addEventListener('input', dirty);
        form.addEventListener('change', dirty);
        if (window.jQuery) window.jQuery(form).on('change.doctorSettings', 'select', dirty);
        document.querySelectorAll('[data-settings-tab]').forEach(button => button.addEventListener('click', () => {
            const tab = button.dataset.settingsTab;
            document.querySelectorAll('[data-settings-tab]').forEach(node => { node.classList.toggle('active', node === button); if (node === button) node.setAttribute('aria-current', 'page'); else node.removeAttribute('aria-current'); });
            document.querySelectorAll('.settings-section').forEach(section => { section.hidden = section.id !== 'section-' + tab; });
            form.elements.namedItem('tab').value = tab;
            const url = new URL(location.href); url.searchParams.set('tab', tab); history.replaceState(null, '', url);
        }));
        const labels = {unspecified:'لم تُحدد الحالة', available:'متاح الآن', busy:'مشغول مؤقتًا', unavailable:'غير متاح مؤقتًا'};
        const availability = main.dataset.settingsAvailability;
        document.querySelectorAll('[data-doctor-availability-label]').forEach(node => { const label = labels[availability] || labels.unspecified; node.dataset.i18n = label; node.textContent = window.doctorUiText?.(label) || label; });
        document.querySelectorAll('[data-doctor-availability-dot]').forEach(node => { node.style.backgroundColor = {available:'#10b981',busy:'#f59e0b',unavailable:'#94a3b8',unspecified:'#94a3b8'}[availability]; });
    };
    window.addEventListener('beforeunload', event => {
        if (document.getElementById('doctor-settings-form')?.dataset.settingsDirty === '1') { event.preventDefault(); event.returnValue = ''; }
    });
    window.initDoctorSettings();
})();
