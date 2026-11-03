(() => {
    'use strict';
    window.initDoctorDiagnosticOrder = () => {
        const main = document.getElementById('main-content-layout');
        const navigation = main?.dataset.diagnosticNav;
        if (navigation) document.querySelectorAll('#app-sidebar a.nav-item').forEach(link => {
            const active = new URL(link.href, location.href).pathname === new URL(navigation, location.href).pathname;
            link.classList.toggle('workspace-nav-active', active);
            if (active) link.setAttribute('aria-current', 'page'); else link.removeAttribute('aria-current');
        });
        const form = document.getElementById('diagnostic-order-form');
        if (!form || form.dataset.diagnosticBound) return;
        form.dataset.diagnosticBound = 'true';
        const container = document.getElementById('diagnostic-items');
        const template = document.getElementById('diagnostic-item-template');
        const add = document.getElementById('diagnostic-add-item');
        let index = Math.max(-1, ...Array.from(form.querySelectorAll('[name^="items["]')).map(input => Number(input.name.match(/^items\[(\d+)\]/)?.[1] ?? -1))) + 1;
        const changed = () => form.dispatchEvent(new Event('input', {bubbles:true}));
        add?.addEventListener('click', () => {
            if (container.querySelectorAll('[data-diagnostic-item]').length >= 20) {window.TheraAlerts.info('الحد الأقصى 20 فحصًا في الطلب.'); return;}
            container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index++)));
            changed();
        });
        container.addEventListener('click', event => {
            const button = event.target.closest('[data-diagnostic-remove]');
            if (!button) return;
            if (container.querySelectorAll('[data-diagnostic-item]').length <= 1) {window.TheraAlerts.info('احتفظ بفحص واحد على الأقل في المسودة.'); return;}
            button.closest('[data-diagnostic-item]').remove();
            changed();
        });
    };
    window.initDoctorDiagnosticOrder();
})();
