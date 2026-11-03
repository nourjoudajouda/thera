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
    const quickMenu = document.getElementById('quick-add-dropdown');
    sidebar?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (!desktop.matches) window.bootstrap?.Offcanvas.getInstance(sidebar)?.hide();
    }));
    const destinations = {'إضافة مريض':'patients','موعد جديد':'appointmentCreate','زيارة':'visits','وصفة طبية':'prescriptions','طلب مختبر':'labOrders','طلب أشعة':'radiologyOrders','إحالة / متابعة':'referrals'};
    quickMenu?.querySelectorAll('button').forEach(button => {
        const label = button.querySelector('span:last-child')?.textContent.trim();
        if (destinations[label]) button.addEventListener('click', () => window.location.assign(window.doctorRoutes[destinations[label]]));
    });
    document.addEventListener('DOMContentLoaded', () => queueMicrotask(() => {
        const diagnosticNavigation = document.getElementById('main-content-layout')?.dataset.diagnosticNav;
        const currentPath = diagnosticNavigation ? new URL(diagnosticNavigation, window.location.href).pathname : window.location.pathname;
        sidebar?.querySelectorAll('a.nav-item').forEach(link => {
            const path = new URL(link.href).pathname;
            const visitDetail = window.doctorPage === 'doctor.clinical.visit' && window.doctorRoutes?.visits && path === new URL(window.doctorRoutes.visits).pathname;
            const active = visitDetail || path === currentPath || (path !== '/doctor' && currentPath.startsWith(path + '/'));
            link.classList.toggle('workspace-nav-active', active);
            if (active) link.setAttribute('aria-current', 'page');
        });
    }));
})();
