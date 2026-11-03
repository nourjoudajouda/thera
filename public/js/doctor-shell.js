(() => {
    'use strict';
    const sidebar = document.getElementById('app-sidebar');
    if (!sidebar || window.doctorShellInitialized) return;
    window.doctorShellInitialized = true;
    const key = 'thera_doctor_ui_' + sidebar.dataset.doctorId;
    let preferences = {theme:'light',language:'ar'};
    try { preferences = {...preferences,...JSON.parse(localStorage.getItem(key) || '{}')}; } catch {}
    const deviceTheme = window.matchMedia('(prefers-color-scheme: dark)');
    const t = text => preferences.language === 'en' ? (window.doctorTranslations?.[text] || text) : text;
    window.doctorUiText = t;
    const textNode = (tag, text) => { const node = document.createElement(tag); node.textContent = text; return node; };
    const menus = [['doctor-card-trigger','doctor-account-menu'],['quick-add-btn','quick-add-dropdown'],['doctor-notifications-toggle','doctor-notifications-menu'],['doctor-language-toggle','doctor-language-menu']];
    const closeMenus = except => menus.forEach(([trigger,id]) => {
        if (id !== except) { document.getElementById(id)?.classList.add('hidden'); document.getElementById(trigger)?.setAttribute('aria-expanded','false'); }
    });
    menus.forEach(([trigger,id]) => document.getElementById(trigger)?.addEventListener('click', () => {
        const menu = document.getElementById(id);
        const opening = menu?.classList.contains('hidden');
        closeMenus();
        if (opening) { menu?.classList.remove('hidden'); document.getElementById(trigger).setAttribute('aria-expanded','true'); if (id === 'doctor-notifications-menu') loadNotifications(); }
    }));
    document.addEventListener('click', event => {
        if (!event.target.closest('#sidebar-doctor-card,#quick-add-container,[data-shell-menu-root]')) closeMenus();
        if (!event.target.closest('.header-search')) document.getElementById('doctor-search-menu')?.classList.add('hidden');
        const destination = event.target.closest('[data-doctor-destination]')?.dataset.doctorDestination;
        if (destination) {
            const routeKey = destination === 'availability' ? 'settings' : destination;
            const url = new URL(window.doctorRoutes[routeKey],location.href);
            if (destination === 'availability') url.searchParams.set('tab','availability');
            location.assign(url);
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') { const current = menus.find(([,id]) => !document.getElementById(id)?.classList.contains('hidden')); closeMenus(); if (current) document.getElementById(current[0])?.focus(); document.getElementById('doctor-search-menu')?.classList.add('hidden'); }
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); document.getElementById('doctor-global-search')?.focus(); }
    });
    function applyTheme() {
        const dark = preferences.theme === 'dark' || (preferences.theme === 'system' && deviceTheme.matches);
        document.documentElement.dataset.doctorTheme = dark ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme',dark ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark',dark);
        const button = document.getElementById('doctor-theme-toggle');
        button?.setAttribute('aria-pressed',String(dark));
        if (button) button.querySelector('span').textContent = dark ? 'dark_mode' : 'light_mode';
        const select = document.getElementById('setting-theme');
        if (select) select.value = preferences.theme;
    }
    function applyLanguage() {
        document.documentElement.lang = preferences.language;
        // The approved clinical workspace remains RTL; names and authored records are never translated.
        document.querySelectorAll('[data-i18n]').forEach(node => { node.textContent = t(node.dataset.i18n); });
        const label = document.getElementById('doctor-language-label');
        if (label) label.textContent = preferences.language === 'en' ? 'English (EN)' : 'عربي (AR)';
        const select = document.getElementById('setting-language');
        if (select) select.value = preferences.language;
        document.querySelectorAll('[data-doctor-language]').forEach(node => node.setAttribute('aria-pressed',String(node.dataset.doctorLanguage === preferences.language)));
        if (window.jQuery?.fn?.select2) document.querySelectorAll('select.select2-hidden-accessible').forEach(select => window.jQuery(select).trigger('change.select2'));
    }
    function savePreference(type,value) {
        if (!(type === 'theme' ? ['light','dark','system'] : ['ar','en']).includes(value)) return;
        preferences[type] = value;
        try { localStorage.setItem(key,JSON.stringify(preferences)); } catch {}
        applyTheme(); applyLanguage(); closeMenus();
    }
    document.getElementById('doctor-theme-toggle')?.addEventListener('click', () => savePreference('theme',document.documentElement.dataset.doctorTheme === 'dark' ? 'light' : 'dark'));
    document.querySelectorAll('[data-doctor-language]').forEach(node => node.addEventListener('click', () => savePreference('language',node.dataset.doctorLanguage)));
    deviceTheme.addEventListener('change',applyTheme);
    window.refreshDoctorShell = () => {
        ['theme','language'].forEach(type => {
            const select = document.getElementById('setting-' + type);
            if (select && !select.dataset.shellBound) { select.dataset.shellBound = '1'; select.addEventListener('change',() => savePreference(type,select.value)); }
        });
        applyTheme(); applyLanguage(); loadNotifications();
    };
    const search = document.getElementById('doctor-global-search');
    search?.addEventListener('input', () => {
        const menu = document.getElementById('doctor-search-menu');
        const query = search.value.trim().slice(0,100);
        menu.replaceChildren(); menu.classList.toggle('hidden',!query);
        if (!query) return;
        [['patients','البحث في المرضى'],['appointments','البحث في مواعيد اليوم']].forEach(([route,label]) => {
            const url = new URL(window.doctorRoutes[route],location.href); url.searchParams.set('search',query);
            if (route === 'appointments') url.searchParams.set('view','list');
            const link = textNode('a',t(label)); link.href = url; menu.append(link);
        });
    });
    search?.addEventListener('keydown',event => { if (event.key === 'Enter') { event.preventDefault(); document.querySelector('#doctor-search-menu a')?.click(); } });
    let notificationRequest = 0;
    async function loadNotifications() {
        const toggle = document.getElementById('doctor-notifications-toggle');
        const list = document.getElementById('doctor-notifications-list');
        if (!toggle || !list) return;
        const request = ++notificationRequest;
        list.replaceChildren(textNode('p',t('جارٍ تحميل التنبيهات…')));
        try {
            const response = await fetch(toggle.dataset.notificationsUrl,{credentials:'same-origin',headers:{Accept:'application/json'}});
            if (!response.ok || response.redirected) throw new Error('notifications');
            const data = await response.json();
            if (request !== notificationRequest) return;
            list.replaceChildren();
            if (!data.items.length) list.append(textNode('p',t('لا توجد تنبيهات ضمن تفضيلاتك الحالية.')));
            data.items.forEach(item => {
                const url = new URL(item.url,location.href); if (url.origin !== location.origin) return;
                const link = document.createElement('a'); link.href = url;
                const icon = textNode('span',item.icon); icon.className = 'material-symbols-outlined'; icon.setAttribute('aria-hidden','true');
                link.append(icon,textNode('span',t(item.label)),textNode('strong',String(item.count))); list.append(link);
            });
            document.getElementById('doctor-notifications-dot').hidden = data.total === 0;
        } catch { if (request === notificationRequest) { list.replaceChildren(textNode('p',t('تعذر تحميل التنبيهات. أعد المحاولة.'))); const retry=textNode('button',t('إعادة المحاولة')); retry.type='button'; retry.addEventListener('click',loadNotifications); list.append(retry); } }
    }
    applyTheme();
    document.addEventListener('DOMContentLoaded', () => queueMicrotask(window.refreshDoctorShell));
})();
