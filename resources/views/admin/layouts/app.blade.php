<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'لوحة الإدارة') - THERA</title>
    <link rel="stylesheet" href="{{ asset('css/thera-filters.css') }}">
    <link rel="stylesheet" href="{{ asset('css/thera-brand.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        :root{--thera-blue:#0495f6;--thera-blue-hover:#0466f1;--thera-navy:#16243e;--thera-navy-dark:#132038;--thera-bg:#f6f9fc;--thera-border:#e2e8f0;--thera-control-border:#cbd5e1;--sidebar-width:268px;--sidebar-collapsed-width:72px}
        *{box-sizing:border-box}html,body{min-height:100%;font-family:'Tajawal',sans-serif}body{margin:0;background:var(--thera-bg);color:#0b1020}
        button,input,select,textarea{font-family:'Tajawal',sans-serif}.material-symbols-outlined{font-size:21px;line-height:1;vertical-align:middle}
        html:not(.material-symbols-ready) .material-symbols-outlined{visibility:hidden;width:1em;overflow:hidden}
        #admin-sidebar.admin-sidebar{width:var(--sidebar-width);background-color:var(--thera-navy)!important;color:#fff;border-left:1px solid rgba(255,255,255,.08);transition:width .28s cubic-bezier(.4,0,.2,1),box-shadow .28s ease}
        #admin-sidebar.admin-sidebar nav{background-color:transparent!important}
        .sidebar-brand{height:64px;background:var(--thera-navy-dark);border-bottom:1px solid rgba(255,255,255,.08);flex:0 0 auto}
        .sidebar-brand img{width:auto;max-width:180px;height:56px;object-fit:contain;filter:brightness(1.15)}
        .sidebar-pin{width:34px;height:34px;border:0;border-radius:10px;background:transparent;color:#94a3b8;display:inline-flex;align-items:center;justify-content:center;transition:.18s}
        .sidebar-pin:hover{background:rgba(255,255,255,.08);color:#fff}
        .sidebar-group-title{padding:0 12px;color:#8ea3ba;font-size:13px;font-weight:700}
        .sidebar-link{display:flex;align-items:center;gap:12px;min-height:48px;padding:0 16px;border-radius:13px;color:#d7e3f1;text-decoration:none;font-weight:700;transition:.2s}
        .sidebar-link:hover{background:rgba(255,255,255,.07);color:#fff}.sidebar-link.active{background:var(--thera-blue);color:#fff;box-shadow:0 8px 20px rgba(4,149,246,.2)}
        .sidebar-link .material-symbols-outlined{color:#afc3d9}.sidebar-link.active .material-symbols-outlined{color:#fff}
        .admin-shell{margin-right:var(--sidebar-width);min-height:100vh;transition:margin-right .28s cubic-bezier(.4,0,.2,1)}.admin-topbar{height:64px;background:#fff;border-bottom:1px solid var(--thera-border);position:sticky;top:0;z-index:1020}
        .topbar-control{height:40px;min-width:40px;border:1px solid #dce4ec;border-radius:12px;background:#fff;color:#334155;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:0 12px;text-decoration:none;font-size:.82rem;font-weight:700;transition:.18s ease;position:relative}
        .topbar-control:hover{background:#f8fafc;border-color:#cbd5e1;color:#0495f6}.notification-count{position:absolute;top:-6px;left:-5px;min-width:19px;height:19px;padding:0 5px;border-radius:999px;background:#ef4444;color:#fff;border:2px solid #fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center}
        .quick-add-button{height:40px;border:0;border-radius:12px;background:var(--thera-blue);color:#fff;padding:0 16px;font-size:.875rem;font-weight:800;display:inline-flex;align-items:center;gap:8px;box-shadow:0 2px 5px rgba(4,149,246,.18)}
        .quick-add-button:hover,.quick-add-button:focus{background:var(--thera-blue-hover);color:#fff}
        .quick-add-menu{right:0!important;left:auto!important;width:260px;margin-top:10px!important;padding:8px;border:1px solid var(--thera-border)!important;border-radius:16px;box-shadow:0 14px 34px rgba(15,23,42,.16)}.quick-add-menu .dropdown-header{padding:8px 10px;color:#94a3b8;font-size:11px;font-weight:800}.quick-add-menu .dropdown-item{min-height:42px;border-radius:10px;color:#334155;font-size:14px;font-weight:700}.quick-add-menu .dropdown-item:hover{background:#f8fafc;color:var(--thera-blue)}
        .header-search{position:relative;width:min(32vw,320px)}.header-search .material-symbols-outlined{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none}.header-search .form-control{height:40px;min-height:40px;padding-right:40px;background:#f8fafc;font-size:.875rem}
        .admin-search-field{position:relative}.admin-search-field>.material-symbols-outlined{position:absolute;right:13px;top:50%;z-index:2;transform:translateY(-50%);color:#94a3b8;pointer-events:none}.admin-search-field>.form-control{padding-right:42px;padding-left:13px}
        body.admin-dim{--thera-bg:#eaf0f7}body.admin-dim .admin-topbar,body.admin-dim .page-card,body.admin-dim .approval-item,body.admin-dim .topbar-control{background:#f8fafc}
        .content-wrap{padding:28px}.page-card{background:#fff;border:1px solid var(--thera-border);border-radius:20px;box-shadow:0 2px 10px rgba(16,27,53,.035)}
        .metric-card{height:100%;padding:22px}.metric-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center}
        .table>:not(caption)>*>*{padding:1rem .9rem;border-bottom-color:#edf1f6;vertical-align:middle}.table thead th{color:#64748b;font-size:.78rem;font-weight:800;background:#f8fafc;white-space:nowrap}
        .status-badge{display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:999px;font-size:.76rem;font-weight:800;white-space:nowrap}
        .btn-thera{background:var(--thera-blue);border-color:var(--thera-blue);color:#fff}.btn-thera:hover,.btn-thera:focus{background:#0466f1;border-color:#0466f1;color:#fff}
        .form-control,.form-select{width:100%;min-height:44px;border:1px solid var(--thera-control-border);border-radius:12px;background-color:#fff;color:#334155;padding:10px 13px;font-size:14px;font-weight:500;line-height:1.5;box-shadow:none;transition:border-color .16s ease,box-shadow .16s ease,background-color .16s ease}.form-select{padding-right:13px;padding-left:44px;background-position:left 13px center;background-size:18px 18px;cursor:pointer}.form-control:hover:not(:disabled):not(:focus),.form-select:hover:not(:disabled):not(:focus){border-color:#94a3b8}.form-control:focus,.form-select:focus{border-color:var(--thera-blue);box-shadow:0 0 0 3px rgba(4,149,246,.1)}.form-control::placeholder{color:#94a3b8;opacity:1}.form-control:disabled,.form-select:disabled{border-color:#e2e8f0;background-color:#f1f5f9;color:#94a3b8;opacity:1}textarea.form-control{min-height:118px;resize:vertical}
        .thera-tabs-wrap{width:100%;max-width:100%;overflow:hidden}.thera-tabs{display:grid;grid-template-columns:repeat(var(--thera-tab-count,5),minmax(0,1fr));gap:8px;margin:0;padding:0;list-style:none}.thera-tabs .nav-item{min-width:0}.thera-tabs .nav-link{width:100%;height:40px;padding:0 12px;border:1px solid transparent;border-radius:999px;background:#eef4ff;color:#3f4752;font-size:13px;font-weight:800;white-space:nowrap;transition:.18s ease}.thera-tabs .nav-link:hover{background:#e5eeff;color:#0f172a}.thera-tabs .nav-link.active{background:var(--thera-blue);border-color:var(--thera-blue);color:#fff;box-shadow:0 1px 3px rgba(4,149,246,.16)}
        .modal-dialog{padding-inline:12px}.modal-content{overflow:hidden;border:1px solid var(--thera-border)!important;border-radius:18px!important;box-shadow:0 22px 60px rgba(15,23,42,.22)!important}.modal-header{min-height:64px;padding:16px 20px;border-bottom:1px solid var(--thera-border);background:#fff}.modal-header .btn-close{width:34px;height:34px;flex:0 0 34px;margin:0!important;padding:9px!important;background-size:12px;border-radius:10px;opacity:.55}.modal-header .btn-close:hover,.modal-header .btn-close:focus{background-color:#f1f5f9;opacity:.8;box-shadow:none}.modal-body{padding:22px 20px 24px}.modal-footer{min-height:76px;padding:14px 20px 16px;gap:10px;border-top:1px solid var(--thera-border);background:#f8fafc}.modal-footer>*{margin:0!important}.modal-footer .btn{min-height:44px;padding-inline:18px;border-radius:12px;font-weight:700}
        .approval-item{border:1px solid var(--thera-border);border-radius:16px;padding:18px;background:#fff}.empty-state{padding:48px 20px;text-align:center;color:#64748b}
        @media(min-width:992px){body.admin-sidebar-collapsed #admin-sidebar{width:var(--sidebar-collapsed-width)}body.admin-sidebar-collapsed .admin-shell{margin-right:var(--sidebar-collapsed-width)}body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-label,body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-group-title,body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-logo,body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-admin-meta,body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-pin{display:none!important}body.admin-sidebar-collapsed #admin-sidebar:not(:hover) .sidebar-link{justify-content:center;padding-inline:0}body.admin-sidebar-collapsed #admin-sidebar:hover{width:var(--sidebar-width);box-shadow:-8px 0 25px rgba(0,0,0,.25)}}
        @media(max-width:1199.98px){.thera-tabs-wrap{overflow-x:auto;scrollbar-width:none}.thera-tabs-wrap::-webkit-scrollbar{display:none}.thera-tabs{display:flex;width:max-content;min-width:max-content}.thera-tabs .nav-link{width:auto;min-width:max-content;padding-inline:18px}}
        @media(max-width:991.98px){#admin-sidebar.admin-sidebar{width:min(86vw,320px)}.admin-shell{margin-right:0}.content-wrap{padding:18px}.admin-topbar{height:64px}.sidebar-brand{height:64px}.sidebar-pin{display:none}.header-search{width:min(42vw,280px)}}
        @media(max-width:767.98px){.header-search{display:none}.quick-add-label{display:none}.quick-add-button{width:40px;padding:0;justify-content:center}.quick-add-button .quick-add-arrow{display:none}}
        @media(max-width:575.98px){.content-wrap{padding:14px}.page-card{border-radius:16px}.metric-card{padding:18px}.table-responsive{border-radius:0 0 16px 16px}.admin-page-title{font-size:1.35rem}.admin-topbar{padding-inline:12px!important}.topbar-control{padding-inline:10px}.modal-dialog{padding-inline:0}.modal-content{border-radius:0!important}.modal-body{padding:20px 16px}.modal-header,.modal-footer{padding-inline:16px}.modal-footer .btn-danger,.modal-footer .btn-primary{flex:1}}
    </style>
    @stack('styles')
</head>
<body>
<aside class="admin-sidebar offcanvas-lg offcanvas-end fixed-top h-100 d-flex flex-column" tabindex="-1" id="admin-sidebar" aria-labelledby="admin-sidebar-label">
    <div class="sidebar-brand d-flex align-items-center justify-content-between px-3">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center overflow-hidden" aria-label="THERA">
            <img class="sidebar-logo" src="{{ asset('images/doctor/thera-logo.png') }}" alt="THERA">
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#admin-sidebar" aria-label="إغلاق"></button>
        <button type="button" class="sidebar-pin d-none d-lg-inline-flex" id="admin-sidebar-pin" aria-label="تثبيت أو طي القائمة" title="تثبيت أو طي القائمة"><span class="material-symbols-outlined">push_pin</span></button>
    </div>
    <div class="sidebar-group-title pt-4 pb-2">إدارة THERA</div>
    <nav class="d-grid gap-2 px-3">
        <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined">grid_view</span><span class="sidebar-label">الرئيسية</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}">
            <span class="material-symbols-outlined">stethoscope</span><span class="sidebar-label">الأطباء</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}" href="{{ route('admin.approvals.index') }}">
            <span class="material-symbols-outlined">approval</span><span class="sidebar-label">طلبات الاعتماد</span>
        </a>
        <a class="sidebar-link" href="{{ route('admin.patient-delegations.index') }}"><span class="material-symbols-outlined">family_restroom</span><span class="sidebar-label">تفويضات الأسرة</span></a>
        <a class="sidebar-link" href="{{ route('admin.patient-reviews.index') }}"><span class="material-symbols-outlined">rate_review</span><span class="sidebar-label">تقييمات المرضى</span></a>
    </nav>
    <div class="mt-auto p-3" style="background:#132038;border-top:1px solid rgba(255,255,255,.08)">
        <div class="d-flex align-items-center gap-2 mb-3 px-2">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;background:#0495f6">{{ mb_substr(auth('admin')->user()->name, 0, 1) }}</div>
            <div class="sidebar-admin-meta min-w-0"><div class="text-white fw-bold small text-truncate">{{ auth('admin')->user()->name }}</div><div class="text-white-50 small">مدير النظام</div></div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">@csrf
            <button class="sidebar-link border-0 bg-transparent w-100" type="submit"><span class="material-symbols-outlined text-danger">logout</span><span class="sidebar-label">تسجيل الخروج</span></button>
        </form>
    </div>
</aside>

<div class="admin-shell">
    <header class="admin-topbar d-flex align-items-center px-3 px-lg-4">
        <div class="d-flex align-items-center justify-content-between gap-3 w-100">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none d-inline-flex align-items-center justify-content-center p-0" style="width:42px;height:42px;border-radius:12px" type="button" data-bs-toggle="offcanvas" data-bs-target="#admin-sidebar" aria-controls="admin-sidebar" aria-label="فتح القائمة"><span class="material-symbols-outlined">menu</span></button>
                <div class="dropdown">
                    <button class="quick-add-button" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"><span class="material-symbols-outlined">add</span><span class="quick-add-label">إضافة</span><span class="material-symbols-outlined quick-add-arrow" style="font-size:17px">expand_more</span></button>
                    <ul class="dropdown-menu quick-add-menu">
                        <li><h6 class="dropdown-header">إضافة جديدة</h6></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('doctor.register') }}" target="_blank" rel="noopener"><span class="material-symbols-outlined text-secondary">person_add</span><span>تسجيل طبيب جديد</span></a></li>
                    </ul>
                </div>
                <form data-thera-filter class="header-search" action="{{ route('admin.doctors.index') }}" method="GET" role="search">
                    <span class="material-symbols-outlined">search</span>
                    <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="ابحث عن طبيب بالاسم أو البريد..." aria-label="البحث عن طبيب">
                </form>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.approvals.index') }}" class="topbar-control p-0" aria-label="الإشعارات: {{ $adminPendingApprovalCount }} طلب معلق" title="الإشعارات" data-admin-pending-count="{{ $adminPendingApprovalCount }}">
                    <span class="material-symbols-outlined">notifications</span>
                    @if ($adminPendingApprovalCount > 0)<span class="notification-count">{{ $adminPendingApprovalCount > 99 ? '99+' : $adminPendingApprovalCount }}</span>@endif
                </a>
                <button class="topbar-control" type="button" aria-label="اللغة الحالية: العربية" title="اللغة الحالية"><span class="material-symbols-outlined">language</span><span class="d-none d-md-inline">عربي (AR)</span></button>
                <button class="topbar-control p-0" id="admin-theme-toggle" type="button" aria-label="تغيير المظهر" title="تغيير المظهر"><span class="material-symbols-outlined" id="admin-theme-icon">light_mode</span></button>
            </div>
        </div>
    </header>
    <main class="content-wrap" id="admin-main-content">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('alerts')
<script src="{{ asset('js/thera-filters.js') }}" defer></script>
<script>
    (() => {
        const ready = () => document.documentElement.classList.add('material-symbols-ready');
        if (!document.fonts?.load) { window.addEventListener('load', ready, {once:true}); return; }
        document.fonts.load('21px "Material Symbols Outlined"').then(ready).catch(() => {});
    })();

    (() => {
        const button = document.getElementById('admin-theme-toggle');
        const icon = document.getElementById('admin-theme-icon');
        const applyTheme = isDim => {
            document.body.classList.toggle('admin-dim', isDim);
            if (icon) icon.textContent = isDim ? 'dark_mode' : 'light_mode';
        };
        applyTheme(localStorage.getItem('thera_admin_theme') === 'dim');
        button?.addEventListener('click', () => {
            const isDim = !document.body.classList.contains('admin-dim');
            applyTheme(isDim);
            localStorage.setItem('thera_admin_theme', isDim ? 'dim' : 'light');
        });
    })();

    (() => {
        const button = document.getElementById('admin-sidebar-pin');
        const storageKey = 'thera_admin_sidebar_collapsed';
        const applyState = isCollapsed => document.body.classList.toggle('admin-sidebar-collapsed', isCollapsed);

        applyState(localStorage.getItem(storageKey) === 'true');
        button?.addEventListener('click', () => {
            const isCollapsed = !document.body.classList.contains('admin-sidebar-collapsed');
            applyState(isCollapsed);
            localStorage.setItem(storageKey, String(isCollapsed));
        });
    })();

</script>
@stack('scripts')
</body>
</html>
