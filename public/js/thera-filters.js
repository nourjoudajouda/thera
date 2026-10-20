(() => {
    'use strict';
    let adminRequest;
    const selector = 'form[data-thera-filter], form[data-appointment-auto-filter]';
    const restoreFocus = (name, start, end, scope) => {
        if (!name) return;
        const root = scope ? document.getElementById(scope) : document.querySelector('header');
        const input = [...(root || document).querySelectorAll('form[data-thera-filter] input, form[data-appointment-auto-filter] input')].find(item => item.name === name && item.type !== 'hidden');
        if (!input) return;
        input.focus({preventScroll:true});
        if (typeof start === 'number' && ['text', 'search', 'tel'].includes(input.type)) input.setSelectionRange(start, end);
    };
    const adminNavigate = async url => {
        adminRequest?.abort();
        const request = new AbortController(); adminRequest = request;
        const main = document.getElementById('admin-main-content');
        main?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, {signal:request.signal, credentials:'same-origin', cache:'no-store', headers:{'X-Requested-With':'XMLHttpRequest'}});
            if (!response.ok) throw new Error('تعذر تحديث النتائج. حاول مجددًا.');
            const next = new DOMParser().parseFromString(await response.text(), 'text/html');
            const region = next.getElementById('admin-main-content');
            if (!region || !main) throw new Error('انتهت الجلسة أو تعذر عرض النتائج. حدّث الصفحة.');
            if (request !== adminRequest) return;
            main.replaceWith(region); document.title = next.title;
            history.replaceState(null, '', url);
            document.querySelectorAll('#admin-sidebar a[href]').forEach(link => link.classList.toggle('active', new URL(link.href).pathname === url.pathname));
            init();
        } finally { if (request === adminRequest) main?.removeAttribute('aria-busy'); }
    };
    const navigate = async (url, form) => {
        const active = document.activeElement;
        const name = active?.matches('input') && active.form === form ? active.name : null;
        const start = name ? active.selectionStart : null, end = name ? active.selectionEnd : null;
        const scope = form?.closest('main')?.id;
        const drawer = form?.closest('.offcanvas.show');
        try {
            if (window.TheraPractice) await window.TheraPractice.navigate(url, {filterDrawer:drawer});
            else await adminNavigate(url);
            restoreFocus(name, start, end, scope);
        } catch (error) {
            if (error.name !== 'AbortError') window.TheraAlerts?.error(error.message || 'تعذر تحديث النتائج.');
        }
    };
    const submit = form => {
        if (!form.isConnected || !form.checkValidity()) return;
        const url = new URL(form.getAttribute('action') || location.pathname, location.href);
        if (url.origin !== location.origin) return;
        const data = new URLSearchParams(new FormData(form));
        for (const [name, value] of [...data]) if (value === '') data.delete(name);
        data.delete('page'); url.search = data.toString();
        return navigate(url, form);
    };
    const init = () => {
        document.querySelectorAll(selector).forEach(form => {
            if (form.dataset.autoFilterBound) return;
            form.dataset.autoFilterBound = 'true';
            let timer;
            form.addEventListener('submit', event => { event.preventDefault(); clearTimeout(timer); submit(form); });
            form.querySelectorAll('select').forEach(select => {
                if (window.jQuery) window.jQuery(select).on('change.theraFilters', () => submit(form));
                else select.addEventListener('change', () => submit(form));
            });
            form.querySelectorAll('input:not([type="hidden"])').forEach(input => {
                if (['checkbox', 'radio', 'date', 'number'].includes(input.type)) input.addEventListener('change', () => submit(form));
                else input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => submit(form), 500); });
            });
        });
    };
    document.addEventListener('click', event => {
        const link = event.target.closest('[data-filter-reset]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin) return;
        event.preventDefault();
        navigate(url, link.closest('form'));
    });
    window.TheraFilters = {init, submit};
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
