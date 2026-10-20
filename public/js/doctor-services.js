window.initDoctorServices = () => {
    const state = JSON.parse(document.getElementById('practice-page-state').textContent);
    const drawer = document.getElementById('service-drawer');
    if (!drawer || !window.bootstrap) return;
    if (window.jQuery?.fn.select2) {
        window.jQuery('[data-service-select]').each(function () {
            window.jQuery(this).select2({
                dir: 'rtl', width: '100%', minimumResultsForSearch: 0,
                dropdownParent: window.jQuery(this.closest('.offcanvas') || document.body),
                language: { noResults: () => 'لا توجد نتائج' },
            });
        });
    }
    const currency = document.getElementById('service-currency');
    const price = document.getElementById('service-price');
    const membership = document.getElementById('service-membership');
    const active = document.getElementById('service-active');
    const refresh = () => {
        const decimals = Number(currency.selectedOptions[0]?.dataset.decimals || 2);
        price.step = decimals === 3 ? '0.001' : '0.01';
        price.max = decimals === 3 ? '999999.999' : '999999.99';
        const canActivate = state.verified && membership.selectedOptions[0]?.dataset.bookings === '1';
        active.disabled = !canActivate;
        if (!canActivate) active.checked = false;
        document.getElementById('service-activation-note').textContent = !membership.value
            ? 'اختر المنشأة أولًا لمعرفة إمكانية تفعيل الخدمة.'
            : (canActivate
                ? 'فعّل هذا الخيار ثم احفظ الخدمة. بعدها اربطها بفترة دوام وحدد سعة الجدول وفعّله. عند إيقاف الخدمة تبقى بياناتها محفوظة.'
                : 'التفعيل يتطلب طبيبًا موثقًا ومنشأة مفعلة للحجز. يمكنك حفظ الخدمة متوقفة ثم تفعيلها بعد اكتمال المتطلبات.');
    };
    currency.addEventListener('change', refresh);
    membership.addEventListener('change', refresh);
    window.jQuery?.(currency).on('select2:select', refresh);
    window.jQuery?.(membership).on('select2:select', refresh);
    refresh();
    if (state.openDrawer) window.bootstrap.Offcanvas.getOrCreateInstance(drawer).show();
    document.getElementById('add-service-button')?.addEventListener('click', event => {
        if (state.hasEditingService) {
            event.preventDefault();
            event.stopPropagation();
            window.TheraPractice.navigate(window.doctorRoutes.services + '?create=1');
        }
    });
    if (new URLSearchParams(window.location.search).get('create') === '1') window.bootstrap.Offcanvas.getOrCreateInstance(drawer).show();
};
window.initDoctorServices();
