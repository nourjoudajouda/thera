const routes = window.doctorRoutes ?? {};

const sidebarRouteKeys = [
    'dashboard',
    'appointments',
    'patients',
    'visits',
    'prescriptions',
    'labOrders',
    'radiologyOrders',
    'results',
    'referrals',
    'services',
    'schedule',
    'collections',
    'settings',
];

const dataPathRouteKeys = {
    dashboard: 'dashboard',
    'daily-overview': 'dashboard',
    appointments: 'appointments',
    'new-appointment': 'appointmentCreate',
    patients: 'patients',
    'new-patient': 'patients',
    'clinical-visits': 'visits',
    prescriptions: 'prescriptions',
    'lab-orders': 'labOrders',
    'radiology-orders': 'radiologyOrders',
    'diagnostic-results': 'results',
    'referrals-and-follow-ups': 'referrals',
    'services-and-pricing': 'services',
    'shifts-and-schedule': 'schedule',
    'billing-and-claims': 'collections',
    verification: 'verification',
    'professional-profile': 'profile',
    settings: 'settings',
};

function connectSidebarNavigation() {
    const sidebar = document.querySelector('#app-sidebar');

    if (!sidebar) {
        return;
    }

    sidebar.querySelectorAll('a[href="#"]').forEach((link, index) => {
        const routeKey = dataPathRouteKeys[link.dataset.path] ?? sidebarRouteKeys[index];

        if (routeKey && routes[routeKey]) {
            link.href = routes[routeKey];
        }
    });
}

function connectDataPathNavigation() {
    document.querySelectorAll('[data-path]').forEach((element) => {
        const routeKey = dataPathRouteKeys[element.dataset.path];

        if (routeKey && routes[routeKey] && element.matches('a')) {
            element.href = routes[routeKey];
        }
    });
}

function findActionRoute(text) {
    const normalizedText = text.replace(/\s+/g, ' ').trim();
    const actions = [
        [['إنشاء حساب', 'حساب جديد'], 'register'],
        [['نسيت كلمة المرور'], 'forgotPassword'],
        [['تسجيل الدخول', 'دخول'], 'login'],
        [['حفظ كلمة المرور', 'تعيين كلمة المرور'], 'login'],
        [['موعد جديد', 'حجز موعد'], 'appointmentCreate'],
        [['تفاصيل الموعد'], 'appointmentShow'],
        [['تفاصيل المريض', 'عرض الملف'], 'patientShow'],
        [['فتح زيارة', 'بدء الزيارة'], 'visit'],
        [['الملف المهني'], 'profile'],
        [['التحقق من الطبيب', 'استكمال التحقق'], 'verification'],
    ];

    return actions.find(([labels]) => labels.some((label) => normalizedText.includes(label)))?.[1];
}

function connectActionLinks() {
    document.querySelectorAll('a[href="#"]').forEach((link) => {
        const routeKey = findActionRoute(link.textContent);

        if (routeKey && routes[routeKey]) {
            link.href = routes[routeKey];
        }
    });
}

function connectAuthenticationForms() {
    const destinationByPage = {
        'doctor.login': 'dashboard',
        'doctor.register': 'verification',
        'doctor.password.request': 'resetPassword',
        'doctor.password.reset': 'login',
    };
    const destination = destinationByPage[window.doctorPage ?? ''];

    if (!destination || !routes[destination]) {
        return;
    }

    document.querySelectorAll('form').forEach((form) => {
        if (form.hasAttribute('data-server-auth')) {
            return;
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if (form.reportValidity()) {
                window.location.assign(routes[destination]);
            }
        });
    });
}

function connectLogoutActions() {
    document.addEventListener('click', (event) => {
        const action = event.target.closest('[data-path="logout"], button, a');
        const hasLogoutIcon = action?.querySelector('.material-symbols-outlined')?.textContent.trim() === 'logout';

        if (!action || !hasLogoutIcon) {
            return;
        }

        event.preventDefault();
        document.getElementById('doctor-logout-form')?.requestSubmit();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    connectSidebarNavigation();
    connectDataPathNavigation();
    connectActionLinks();
    connectAuthenticationForms();
    connectLogoutActions();
});
