(() => {
    const sidebar = document.getElementById('app-sidebar');
    const header = document.getElementById('top-header');
    const desktop = window.matchMedia('(min-width: 992px)');
    let collapsed = localStorage.getItem('thera_sidebar_pinned') === 'false';
    const update = () => {
        const main = document.getElementById('main-content-layout');
        sidebar?.classList.toggle('sidebar-collapsed', collapsed && desktop.matches);
        if (main) main.style.marginRight = desktop.matches ? (collapsed ? '72px' : '268px') : '0';
        if (header) header.style.right = desktop.matches ? (collapsed ? '72px' : '268px') : '0';
    };
    window.refreshDoctorWorkspaceLayout = update;
    document.getElementById('sidebar-pin-toggle')?.addEventListener('click', () => {
        collapsed = !collapsed;
        localStorage.setItem('thera_sidebar_pinned', collapsed ? 'false' : 'true');
        update();
    });
    sidebar?.addEventListener('mouseenter', () => { if (collapsed && desktop.matches) sidebar.classList.add('sidebar-hovered'); });
    sidebar?.addEventListener('mouseleave', () => sidebar.classList.remove('sidebar-hovered'));
    desktop.addEventListener('change', update);
    update();
    const menu = document.getElementById('doctor-account-menu');
    document.getElementById('doctor-card-trigger')?.addEventListener('click', () => menu?.classList.toggle('hidden'));
    const quickMenu = document.getElementById('quick-add-dropdown');
    document.getElementById('quick-add-btn')?.addEventListener('click', () => quickMenu?.classList.toggle('hidden'));
    document.addEventListener('click', event => {
        if (!event.target.closest('#quick-add-container')) quickMenu?.classList.add('hidden');
        if (!event.target.closest('#sidebar-doctor-card')) menu?.classList.add('hidden');
    });
    document.querySelectorAll('#doctor-account-menu button').forEach(button => {
        if (button.textContent.includes('الملف الشخصي')) button.addEventListener('click', () => window.location.assign(window.doctorRoutes.profile));
    });
    const destinations = {'إضافة مريض':'patients','موعد جديد':'appointmentCreate','زيارة':'visits','وصفة طبية':'prescriptions','طلب مختبر':'labOrders','طلب أشعة':'radiologyOrders','إحالة / متابعة':'referrals'};
    quickMenu?.querySelectorAll('button').forEach(button => {
        const label = button.querySelector('span:last-child')?.textContent.trim();
        if (destinations[label]) button.addEventListener('click', () => window.location.assign(window.doctorRoutes[destinations[label]]));
    });
    document.addEventListener('DOMContentLoaded', () => {
        sidebar?.querySelectorAll('a.nav-item').forEach(link => {
            const path = new URL(link.href).pathname;
            const visitDetail = window.doctorPage === 'doctor.clinical.visit' && window.doctorRoutes?.visits && path === new URL(window.doctorRoutes.visits).pathname;
            const active = visitDetail || path === window.location.pathname || (path !== '/doctor' && window.location.pathname.startsWith(path + '/'));
            link.classList.toggle('workspace-nav-active', active);
            if (active) link.setAttribute('aria-current', 'page');
        });
    });
})();
