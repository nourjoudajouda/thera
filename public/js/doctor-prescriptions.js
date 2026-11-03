(() => {
    'use strict';
    window.initDoctorPrescription = () => {
        const form = document.getElementById('prescription-form');
        if (!form || form.dataset.rxBound) return;
        form.dataset.rxBound = 'true';
        const container = document.getElementById('rx-items');
        const template = document.getElementById('rx-item-template');
        const add = document.getElementById('rx-add-item');
        let index = Math.max(-1, ...Array.from(form.querySelectorAll('[name^="items["]')).map(input => Number(input.name.match(/^items\[(\d+)\]/)?.[1] ?? -1))) + 1;
        const changed = () => form.dispatchEvent(new Event('input', {bubbles:true}));
        const bindRow = row => {
            const source = row.querySelector('[data-rx-source]');
            if (!source) return;
            const sync = () => {
                const custom = source.value === 'custom';
                for (const [selector, active] of [['[data-rx-custom]',custom], ['[data-rx-catalog]',!custom]]) {
                    const group = row.querySelector(selector);
                    group.hidden = !active;
                    group.querySelectorAll('input, select').forEach(input => input.disabled = !active);
                }
            };
            sync();
            if (window.jQuery) window.jQuery(source).on('change.rxSource', () => {sync(); changed();});
            else source.addEventListener('change', () => {sync(); changed();});
        };
        container.querySelectorAll('[data-rx-item]').forEach(bindRow);
        add?.addEventListener('click', () => {
            if (container.querySelectorAll('[data-rx-item]').length >= 30) { window.TheraAlerts.info('الحد الأقصى 30 دواءً في الوصفة.'); return; }
            container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index++)));
            const row = container.lastElementChild;
            if (window.jQuery?.fn.select2) window.jQuery(row).find('select').each(function () {const modal = window.jQuery(this).closest('.modal'); window.jQuery(this).select2({dir:'rtl', width:'100%', dropdownParent:modal.length?modal:window.jQuery(document.body)}).on('change.rxDirty', changed);});
            bindRow(row);
            changed();
        });
        container.addEventListener('click', event => {
            const button = event.target.closest('[data-rx-remove]');
            if (!button) return;
            if (container.querySelectorAll('[data-rx-item]').length <= 1) { window.TheraAlerts.info('احتفظ بسطر دواء واحد على الأقل في المسودة.'); return; }
            const row = button.closest('[data-rx-item]');
            if (window.jQuery?.fn.select2) window.jQuery(row).find('.select2-hidden-accessible').each(function () {window.jQuery(this).select2('destroy');});
            row.remove(); changed();
        });
    };
    window.initDoctorPrescription();
})();
