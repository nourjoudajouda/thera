<!DOCTYPE html><html dir="rtl" lang="ar" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>النتائج - THERA</title>
<!-- Google Fonts: Tajawal & Inter -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;700;800&amp;family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&amp;display=swap" rel="stylesheet">
<!-- Tailwind CSS v3 with forms and container-queries -->

<!-- Tailwind Configuration -->

<!-- Typography and Material Symbols Styles -->
@include('doctor.partials.assets')
<style data-purpose="typography">
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #F6F9FC;
      color: #1E293B;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
    }
    .font-num {
      font-family: 'Inter', sans-serif;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
      user-select: none;
    }
    .material-symbols-outlined.fill-1 {
      font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
<!-- Layout & Sidebar Transitions -->
<style data-purpose="layout-sidebar">
    .sidebar-scroll {
      overflow-y: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    .sidebar-scroll::-webkit-scrollbar {
      width: 0;
      height: 0;
      display: none;
    }
    
    #app-sidebar {
      transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #top-header {
      transition: right 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #main-content-layout {
      transition: margin-right 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Canonical pinned / collapsed Sidebar states */
    #app-sidebar {
      width: 268px;
      transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s ease;
    }

    #app-sidebar.sidebar-collapsed {
      width: 72px;
    }

    #app-sidebar.sidebar-collapsed.sidebar-hovered {
      width: 268px;
      box-shadow: -8px 0 25px rgba(0, 0, 0, 0.25);
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-label,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .group-heading,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-text,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-meta,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) #sidebar-pin-toggle {
      display: none !important;
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-item {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-card-body {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    /* Custom select arrow styling for RTL */
    .rtl-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
      background-position: left 0.75rem center;
      background-repeat: no-repeat;
      background-size: 1.25em 1.25em;
      padding-left: 2.5rem;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }
  </style>

<style>
/* THERA unified controls — frozen cross-page visual language */
.thera-major-btn { border-radius: 8px !important; min-height: 40px; }
.thera-icon-btn { border-radius: 8px !important; }
.thera-custom-select { position: relative; width: 100%; }
.thera-custom-select.compact { width: 100%; min-width: 0; }
.thera-custom-select-trigger {
  width: 100%; height: 40px; padding: 0 12px; background: #fff;
  border: 1px solid #E2E8F0; border-radius: 8px; color: #334155;
  font-size: 12px; font-weight: 600; display: flex; align-items: center;
  justify-content: space-between; gap: 10px; text-align: right;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  white-space: nowrap;
}
.thera-custom-select-trigger:hover { background:#F8FAFC; border-color:#CBD5E1; }
.thera-custom-select.open .thera-custom-select-trigger {
  border-color:#0495F6; box-shadow:0 0 0 2px rgba(4,149,246,.12); background:#fff;
}
.thera-custom-select-arrow { font-size:16px; color:#94A3B8; transition:transform .15s ease; }
.thera-custom-select.open .thera-custom-select-arrow { transform:rotate(180deg); color:#0495F6; }
.thera-custom-select-menu {
  position:absolute; top:calc(100% + 6px); right:0; left:0; z-index:120;
  padding:6px; background:#fff; border:1px solid #E2E8F0; border-radius:10px;
  box-shadow:0 10px 28px rgba(15,23,42,.12); max-height:260px; overflow:auto;
  min-width:max(100%, 180px);
}
.thera-custom-select-option {
  width:100%; min-height:34px; padding:7px 10px; border-radius:7px; display:flex;
  align-items:center; justify-content:space-between; gap:8px; color:#334155;
  font-size:12px; text-align:right; transition:background .12s ease,color .12s ease;
}
.thera-custom-select-option:hover { background:#F8FAFC; color:#0B1020; }
.thera-custom-select-option.active { background:#EFF6FF; color:#0369A1; font-weight:700; }
.thera-custom-select-option .check { font-size:15px; opacity:0; color:#0495F6; }
.thera-custom-select-option.active .check { opacity:1; }
.thera-native-select-hidden { position:absolute !important; opacity:0 !important; pointer-events:none !important; width:1px !important; height:1px !important; }
#reset-results-filters-btn { width:40px !important; height:40px !important; padding:0 !important; justify-content:center !important; border:1px solid #E2E8F0; background:#fff; border-radius:8px !important; color:#475569 !important; flex:0 0 40px; }
#reset-results-filters-btn:hover { background:#F8FAFC !important; border-color:#CBD5E1; }
html, body { overflow-x: hidden; }
</style>

</head>
<body class="select-none bg-[#F6F9FC] text-slate-800 antialiased min-h-screen">
<!-- BEGIN: Sidebar -->
<aside class="fixed top-0 right-0 bottom-0 z-50 bg-[#16243E] border-l border-[rgba(255,255,255,0.08)] flex flex-col select-none" data-purpose="application-sidebar" id="app-sidebar">
<!-- Sidebar Header & Official Logo -->
<header class="h-16 flex items-center justify-between px-4 bg-[#132038] border-b border-[rgba(255,255,255,0.08)] flex-shrink-0">
<div class="flex items-center gap-3 overflow-hidden">
<img alt="THERA Logo" class="h-9 max-w-[130px] object-contain flex-shrink-0 sidebar-brand-text" src="{{ asset('images/doctor/thera-logo.png') }}"/>
</div>
<button class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors" id="sidebar-pin-toggle" title="تثبيت / طي القائمة" type="button">
<span class="material-symbols-outlined text-xl fill-1" id="sidebar-pin-icon">push_pin</span>
</button>
</header>
<!-- Scrollable Navigation Items -->
<div class="sidebar-scroll flex-1 min-h-0 py-4 px-3 space-y-6 overflow-y-auto overflow-x-hidden" id="sidebar-nav-container">
<!-- Group 1: العمل اليومي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">العمل اليومي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">grid_view</span>
<span class="nav-label">الرئيسية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">calendar_today</span>
<span class="nav-label">المواعيد</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">person_search</span>
<span class="nav-label">المرضى</span>
</a>
</div>
<!-- Group 2: العمل الطبي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">العمل الطبي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">stethoscope</span>
<span class="nav-label">الزيارات</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">prescriptions</span>
<span class="nav-label">الوصفات الطبية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label">طلبات المختبر</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined flex-shrink-0 text-[20px]">radiology</span>
<span class="nav-label">طلبات الأشعة</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">assignment_turned_in</span>
<span class="nav-label">النتائج</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">sync_alt</span>
<span class="nav-label">الإحالات والمتابعات</span>
</a>
</div>
<!-- Group 3: الإدارة -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">الإدارة</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined flex-shrink-0 text-[22px] text-white">receipt_long</span>
<span class="nav-label">الخدمات والأسعار</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">schedule</span>
<span class="nav-label">الدوام والجدول</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">account_balance_wallet</span>
<span class="nav-label">التحصيل والمستحقات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">settings</span>
<span class="nav-label">الإعدادات</span>
</a>
</div>
</div>
<!-- Fixed Doctor Footer Profile -->
<footer class="relative bg-[#132038] border-t border-[rgba(255,255,255,0.08)] p-3 flex-shrink-0" id="sidebar-doctor-card">
<div class="doctor-card-body flex items-center justify-between cursor-pointer rounded-xl p-1.5 hover:bg-white/5 transition-all" id="doctor-card-trigger">
<div class="flex items-center gap-2.5 overflow-hidden">
<div class="relative flex-shrink-0">
<div class="w-10 h-10 rounded-full bg-[#203254] text-white flex items-center justify-center font-bold text-xs border border-white/20">م أ</div>
<span class="absolute bottom-0 left-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-[#132038] rounded-full"></span>
</div>
<div class="doctor-meta flex flex-col text-right overflow-hidden">
<span class="text-white text-sm font-bold truncate">د. محمد أحمد</span>
<span class="text-xs text-slate-400 truncate">استشاري طب باطني</span>
<span class="text-[11px] text-emerald-400 font-medium">متاح الآن</span>
</div>
</div>
<button class="doctor-meta text-slate-400 hover:text-white p-1" type="button">
<span class="material-symbols-outlined text-lg">more_vert</span>
</button>
</div>
<!-- Doctor Menu Dropdown Popover -->
<div class="hidden absolute bottom-full mb-2 right-3 w-56 bg-[#132038] rounded-xl shadow-dropdown border border-[rgba(255,255,255,0.08)] py-1.5 z-50 text-slate-200 text-sm" id="doctor-account-menu">
<button class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-slate-400">person</span>
<span class="">الملف الشخصي</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-slate-400">manage_accounts</span>
<span class="">إعدادات الحساب</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-emerald-400">radio_button_checked</span>
<span class="">تغيير حالة التوفر</span>
</button>
<div class="my-1 border-t border-[rgba(255,255,255,0.08)]"></div>
<button class="w-full text-right px-4 py-2 hover:bg-red-500/10 text-red-400 flex items-center gap-2" type="button">
<span class="material-symbols-outlined text-lg text-red-400">logout</span>
<span class="">تسجيل الخروج</span>
</button>
</div>
</footer>
</aside>
<!-- END: Sidebar -->
<!-- BEGIN: Topbar -->
<header class="fixed top-0 left-0 h-16 bg-white border-b border-[#E2E8F0] z-40 px-6 flex items-center justify-between" data-purpose="top-navigation" id="top-header" style="right:268px;">
<!-- Right Section: Quick Add and Search -->
<div class="flex items-center gap-4">
<!-- Quick Add Dropdown Container -->
<div class="relative" id="quick-add-container">
<button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold flex items-center gap-2 shadow-sm transition-colors text-sm" id="quick-add-btn" type="button">
<span class="material-symbols-outlined text-xl">add</span>
<span class="">إضافة</span>
<span class="material-symbols-outlined text-base" id="quick-add-arrow">expand_more</span>
</button>
<!-- Dropdown Menu -->
<div class="hidden absolute top-full right-0 mt-2 w-64 bg-white rounded-2xl shadow-dropdown border border-slate-200 py-2 z-50 text-slate-700 text-sm" id="quick-add-dropdown">
<div class="px-3 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">تشغيلي</div>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">person_add</span>
<span class="">إضافة مريض</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">calendar_today</span>
<span class="">موعد جديد</span>
</button>
<div class="my-1.5 border-t border-slate-100"></div>
<div class="px-3 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">سريري</div>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">stethoscope</span>
<span class="">زيارة</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">prescriptions</span>
<span class="">وصفة طبية</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" id="quick-add-lab-order-btn" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">science</span>
<span class="">طلب مختبر</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" id="quick-add-rad-order-btn" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">radiology</span>
<span class="">طلب أشعة</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">sync_alt</span>
<span class="">إحالة / متابعة</span>
</button>
</div>
</div>
<!-- Quick Search Input -->
<div class="relative w-80">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
<input class="w-full h-10 pr-10 pl-4 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand placeholder-slate-400" placeholder="ابحث عن مريض أو موعد... (⌘K)" type="text"/>
</div>
</div>
<!-- Left Section: Notifications, Language, Theme — FROZEN APPROVED SHELL -->
<div class="flex items-center gap-3">
<!-- Notifications -->
<div class="relative">
<button class="w-10 h-10 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors" title="التنبيهات" type="button">
<span class="material-symbols-outlined text-xl">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
</button>
</div>
<!-- Language Selector -->
<button class="h-10 px-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 text-sm font-medium transition-colors" title="تغيير اللغة" type="button">
<span class="material-symbols-outlined text-base">language</span>
<span class="">عربي (AR)</span>
</button>
<!-- Theme Switcher -->
<button class="w-10 h-10 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors" title="الوضع الليلي / النهاري" type="button">
<span class="material-symbols-outlined text-xl">light_mode</span>
</button>
</div>
</header>
<!-- END: Topbar -->
<!-- BEGIN: Main Content Layout -->
<main class="pt-20 px-6 pb-6 min-h-screen" data-purpose="workspace-content" id="main-content-layout">
  <!-- Page Header -->
  <section class="mt-4 mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1 font-medium" aria-label="Breadcrumb">
        <span>العمل الطبي</span>
        <span class="material-symbols-outlined text-xs">chevron_left</span>
        <span class="text-brand font-bold">النتائج</span>
      </nav>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">النتائج</h1>
      <p class="text-xs text-slate-500 mt-1">مراجعة نتائج المختبر وتقارير الأشعة ومتابعة حالة مراجعة الطبيب</p>
    </div>
    <button class="h-10 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm" id="refresh-results-btn" type="button">
      <span class="material-symbols-outlined text-xl text-brand">refresh</span>
      <span>تحديث العرض</span>
    </button>
  </section>

  <!-- Compact Summary -->
  <section class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-5" aria-label="ملخص النتائج">
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">بحاجة للمراجعة</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-needs-review">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">rate_review</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">النتيجة جاهزة</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-ready">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">task_alt</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">قيد التنفيذ</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-in-progress">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">hourglass_top</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">تمت المراجعة</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-reviewed">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl">verified</span></div>
    </div>
  </section>

  <!-- Tabs -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 mb-4" id="results-tabs">
    <button class="result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm" data-tab="needs_review" type="button"><span>بحاجة للمراجعة</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-num" id="tab-count-needs-review">0</span></button>
    <button class="result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="all" type="button"><span>الكل</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-all-results">0</span></button>
    <button class="result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="in_progress" type="button"><span>قيد التنفيذ</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-in-progress">0</span></button>
    <button class="result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="reviewed" type="button"><span>تمت المراجعة</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-reviewed">0</span></button>
  </div>

  <!-- Filters -->
  <section class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 overflow-visible">
    <div class="grid grid-cols-[minmax(185px,0.9fr)_minmax(115px,0.55fr)_minmax(145px,0.68fr)_minmax(150px,0.72fr)_minmax(165px,0.76fr)_40px] items-center gap-2 w-full min-w-0">
      <div class="relative min-w-0">
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
        <input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="results-filter-search" placeholder="ابحث باسم المريض أو رقم المريض أو الطلب..." type="text">
      </div>
      <div class="min-w-0"><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="results-filter-source"><option value="">المصدر: الكل</option><option value="lab">مختبر</option><option value="radiology">أشعة</option></select></div>
      <div class="min-w-0"><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="results-filter-state"><option value="">حالة النتيجة: الكل</option><option value="in_progress">قيد التنفيذ</option><option value="result_ready">النتيجة جاهزة</option></select></div>
      <div class="min-w-0"><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="results-filter-review"><option value="">مراجعة الطبيب: الكل</option><option value="pending_review">بانتظار المراجعة</option><option value="reviewed">تمت المراجعة</option><option value="unavailable">غير متاح</option></select></div>
      <div class="min-w-0"><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="results-filter-sort"><option value="newest">الترتيب: الأحدث أولاً</option><option value="oldest">الترتيب: الأقدم أولاً</option><option value="needs_review">الترتيب: أولوية المراجعة</option></select></div>
      <button class="flex items-center justify-center transition-colors" id="reset-results-filters-btn" aria-label="إعادة ضبط الفلاتر" title="إعادة ضبط الفلاتر" type="button"><span class="material-symbols-outlined text-[18px]">restart_alt</span></button>
    </div>
    <div class="text-[11px] text-slate-400 pt-3 mt-3 border-t border-slate-100"><span id="results-filter-summary">يعرض النتائج التي وصلت إلى مرحلة التنفيذ أو المراجعة</span></div>
  </section>

  <!-- Results Table: header always visible -->
  <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="results-table-card">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs min-w-[1120px]">
        <thead class="bg-slate-50 text-slate-500 font-semibold text-xs border-b border-slate-200">
          <tr class="whitespace-nowrap">
            <th class="py-3 px-4">المريض</th>
            <th class="py-3 px-4">النتيجة / الطلب</th>
            <th class="py-3 px-4">المصدر</th>
            <th class="py-3 px-4">تاريخ الطلب</th>
            <th class="py-3 px-4">جهة التنفيذ</th>
            <th class="py-3 px-4">حالة النتيجة</th>
            <th class="py-3 px-4">حالة التقرير</th>
            <th class="py-3 px-4">مراجعة الطبيب</th>
            <th class="py-3 px-4 text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100" id="results-table-body"></tbody>
      </table>
    </div>
  </section>

  <!-- Mobile -->
  <div class="md:hidden space-y-3 mt-4" id="results-mobile-container"></div>
</main>
<!-- END: Main Content Layout -->

<!-- Details Drawer -->
<aside aria-labelledby="result-details-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawer-result-details" role="dialog">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" id="result-details-backdrop"></div>
  <div class="fixed inset-y-0 left-0 max-w-full flex">
    <div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
      <header class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl" id="result-details-icon">assignment_turned_in</span></div>
          <div><div class="flex items-center gap-2"><h2 class="text-base font-bold text-slate-900 font-num" id="result-details-title">—</h2><span class="px-2 py-0.5 rounded-md text-[10px] font-bold" id="result-details-source-badge">—</span></div><p class="text-xs text-slate-500" id="result-details-subtitle">—</p></div>
        </div>
        <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center" id="close-result-details-btn" type="button"><span class="material-symbols-outlined text-xl">close</span></button>
      </header>
      <div class="flex-1 overflow-y-auto p-6 space-y-5 text-xs">
        <section class="bg-slate-50 rounded-xl p-4 border border-slate-200">
          <div class="text-[11px] font-bold text-slate-400 uppercase mb-2">بيانات المريض</div>
          <div class="flex items-center justify-between gap-4"><div><div class="text-sm font-bold text-slate-900" id="result-details-patient-name">—</div><div class="flex items-center gap-1.5 text-slate-500 font-num mt-0.5"><span class="bg-white border border-slate-200 text-slate-700 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="result-details-patient-id">—</span><span>•</span><span id="result-details-patient-meta">—</span></div></div><div class="text-left"><span class="text-[11px] text-slate-400 block">الطبيب</span><span class="font-bold text-slate-800" id="result-details-doctor">—</span></div></div>
        </section>

        <section class="grid grid-cols-3 gap-3">
          <div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">حالة النتيجة</span><div id="result-details-result-state"></div></div>
          <div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">حالة التقرير</span><div id="result-details-report-state"></div></div>
          <div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">مراجعة الطبيب</span><div id="result-details-review-state"></div></div>
        </section>

        <section class="space-y-3" id="result-details-order-info">
          <div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">الفحص / التصوير</span><div class="text-sm font-bold text-slate-900" id="result-details-service">—</div></div>
          <div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">جهة التنفيذ</span><div class="text-slate-800 font-medium" id="result-details-provider">—</div></div>
          <div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">الزيارة المرتبطة</span><div class="text-slate-800" id="result-details-visit">—</div></div>
          <div><span class="text-slate-400 block mb-1">السياق السريري</span><div class="text-slate-800" id="result-details-context">—</div></div>
        </section>

        <section class="bg-slate-50 border border-slate-200 rounded-xl p-4">
          <div class="flex items-start gap-3"><span class="material-symbols-outlined text-slate-400 text-xl">description</span><div><h4 class="font-bold text-slate-800 text-xs mb-1">تفاصيل النتيجة / التقرير</h4><p class="text-[11px] text-slate-500" id="result-details-report-copy">لا توجد بيانات تقرير مفصلة ضمن النموذج الحالي.</p></div></div>
        </section>

        <section class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden" id="result-doctor-review-box">
          <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-brand text-2xl">rate_review</span>
            <div class="flex-1">
              <h4 class="font-bold text-slate-900 text-xs mb-1">النتيجة متاحة لمراجعة الطبيب</h4>
              <p class="text-[11px] text-slate-600 mb-3">فتح النتيجة أو قراءتها لا يسجل مراجعة تلقائيًا. بعد الانتهاء اضغط «تمت المراجعة».</p>
              <textarea class="w-full min-h-[72px] px-3 py-2 bg-white border border-blue-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0495F6]/15 focus:border-[#0495F6] mb-3 resize-y" id="result-review-note" placeholder="ملاحظة الطبيب (اختياري)"></textarea>
              <button class="h-9 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold text-xs flex items-center gap-1.5" id="perform-result-review-btn" type="button"><span class="material-symbols-outlined text-[17px]">done_all</span><span>تمت المراجعة</span></button>
            </div>
          </div>
        </section>
      </div>
      <footer class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
        <button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-lg font-bold" id="close-result-details-footer-btn" type="button">إغلاق</button>
        <div class="flex items-center gap-2"><button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-lg font-bold flex items-center gap-2" id="open-source-order-btn" type="button"><span class="material-symbols-outlined text-base">open_in_new</span><span>فتح الطلب الأصلي</span></button><button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-lg font-bold flex items-center gap-2" id="print-result-btn" type="button"><span class="material-symbols-outlined text-base">print</span><span>طباعة</span></button></div>
      </footer>
    </div>
  </div>
</aside>

<!-- Context Menu + Toasts -->
<div class="hidden fixed bg-white rounded-xl shadow-lg border border-[#E2E8F0] p-1.5 z-[80] text-slate-700 text-xs w-[220px] font-medium" dir="rtl" id="result-row-menu"></div>
<div class="fixed bottom-6 left-6 z-[90] flex flex-col gap-2 pointer-events-none" id="results-toast-container"></div>

<script data-purpose="results-review-app">
(() => {
  'use strict';

  const LAB_STORAGE_KEY = 'thera_lab_orders_doctor_demo';
  const RAD_STORAGE_KEY = 'thera_radiology_orders_doctor_demo';
  const SIDEBAR_PIN_KEY = 'thera_sidebar_pinned';

  let isPinned = true;
  let resultsList = [];
  let currentTab = 'needs_review';
  let activeResultKey = null;

  const IMAGING_TYPES = {
    xray: 'أشعة سينية X-Ray',
    ultrasound: 'ألتراساوند Ultrasound',
    ct: 'تصوير طبقي CT',
    mri: 'رنين مغناطيسي MRI',
    mammography: 'ماموغرام Mammography',
    other: 'تصوير آخر'
  };

  function safeString(value){ return typeof value === 'string' ? value : ''; }
  function safeArrayFromStorage(key){
    try { const value = JSON.parse(localStorage.getItem(key) || '[]'); return Array.isArray(value) ? value : []; }
    catch { return []; }
  }
  function patientMeta(item){
    const age = item.patientAge ? `${item.patientAge} سنة` : '—';
    const gender = safeString(item.patientGender) || '—';
    return `${age} • ${gender}`;
  }
  function providerName(source, item){
    if(item.destinationMode === 'unassigned') return 'غير محددة';
    if(source === 'lab') return safeString(item.laboratoryName) || '—';
    return safeString(item.radiologyCenterName) || safeString(item.providerName) || '—';
  }
  function serviceName(source, item){
    if(source === 'lab') return safeString(item.testName) || '—';
    const type = IMAGING_TYPES[item.imagingType] || IMAGING_TYPES[item.modality] || safeString(item.imagingType) || safeString(item.modality) || '—';
    const area = safeString(item.bodyArea) || safeString(item.bodyPart);
    return area ? `${type} — ${area}` : type;
  }
  function canReview(item){
    return (item.resultState === 'result_ready' || item.providerReportState === 'published' || item.providerReportState === 'approved') && item.doctorReviewState !== 'reviewed';
  }
  function shouldAppear(item){
    return item.resultState === 'in_progress' || item.resultState === 'result_ready' || item.providerReportState === 'published' || item.providerReportState === 'approved' || item.doctorReviewState === 'pending_review' || item.doctorReviewState === 'reviewed';
  }
  function normalizeResult(source, item){
    if(!item || typeof item !== 'object' || !shouldAppear(item)) return null;
    const id = safeString(item.id); const orderNumber = safeString(item.orderNumber);
    if(!id || !orderNumber) return null;
    return {
      key: `${source}:${id}`, source, id, orderNumber,
      patientId: safeString(item.patientId), patientName: safeString(item.patientName), patientInitials: safeString(item.patientInitials),
      patientAge: safeString(String(item.patientAge ?? '')), patientGender: safeString(item.patientGender), doctorName: safeString(item.doctorName) || 'د. محمد أحمد',
      orderDate: safeString(item.orderDate), isoDate: safeString(item.isoDate), visitTitle: safeString(item.visitTitle),
      service: serviceName(source,item), provider: providerName(source,item), clinicalContext: safeString(item.clinicalContext),
      resultState: ['none','in_progress','result_ready'].includes(item.resultState) ? item.resultState : 'none',
      providerReportState: ['unavailable','published','approved'].includes(item.providerReportState) ? item.providerReportState : 'unavailable',
      doctorReviewState: ['unavailable','pending_review','reviewed'].includes(item.doctorReviewState) ? item.doctorReviewState : 'unavailable',
      reviewedByDoctorId: safeString(item.reviewed_by_doctor_id), reviewedAt: safeString(item.reviewed_at), reviewNote: safeString(item.doctor_review_note),
      original: item
    };
  }
  function loadResults(){
    const labs = safeArrayFromStorage(LAB_STORAGE_KEY).map(x => normalizeResult('lab',x)).filter(Boolean);
    const radiology = safeArrayFromStorage(RAD_STORAGE_KEY).map(x => normalizeResult('radiology',x)).filter(Boolean);
    resultsList = [...labs, ...radiology];
  }
  function writeBackResult(result){
    const key = result.source === 'lab' ? LAB_STORAGE_KEY : RAD_STORAGE_KEY;
    const list = safeArrayFromStorage(key);
    const idx = list.findIndex(x => x && x.id === result.id);
    if(idx < 0) return false;
    list[idx].doctorReviewState = result.doctorReviewState;
    if(result.doctorReviewState === 'reviewed'){
      list[idx].reviewed_by_doctor_id = result.reviewedByDoctorId || 'doctor-demo-001';
      list[idx].reviewed_at = result.reviewedAt || new Date().toISOString();
      list[idx].doctor_review_note = result.reviewNote || '';
    }
    localStorage.setItem(key, JSON.stringify(list));
    return true;
  }

  function resultStateLabel(s){ return ({none:'لا توجد نتيجة',in_progress:'قيد التنفيذ',result_ready:'النتيجة جاهزة'})[s] || 'لا توجد نتيجة'; }
  function reportStateLabel(s){ return ({unavailable:'غير متاح',published:'منشور من جهة التنفيذ',approved:'معتمد من جهة التنفيذ'})[s] || 'غير متاح'; }
  function reviewStateLabel(s){ return ({unavailable:'غير متاح',pending_review:'بانتظار المراجعة',reviewed:'تمت المراجعة'})[s] || 'غير متاح'; }

  function resultBadge(s){
    const el=document.createElement('span'); el.className='inline-flex px-2 py-0.5 rounded-md font-bold text-[10px] border whitespace-nowrap'; el.textContent=resultStateLabel(s);
    if(s==='result_ready') el.className+=' bg-emerald-50 text-emerald-700 border-emerald-200';
    else if(s==='in_progress') el.className+=' bg-sky-50 text-sky-700 border-sky-200';
    else el.className+=' bg-slate-100 text-slate-500 border-slate-200'; return el;
  }
  function reportBadge(s){
    const el=document.createElement('span'); el.className='inline-flex px-2 py-0.5 rounded-md font-medium text-[10px] border whitespace-nowrap'; el.textContent=reportStateLabel(s);
    if(s==='approved') el.className+=' bg-emerald-50 text-emerald-700 border-emerald-200';
    else if(s==='published') el.className+=' bg-blue-50 text-blue-700 border-blue-200';
    else el.className+=' bg-slate-100 text-slate-500 border-slate-200'; return el;
  }
  function reviewBadge(s){
    const el=document.createElement('span'); el.className='inline-flex px-2 py-0.5 rounded-md font-bold text-[10px] border whitespace-nowrap'; el.textContent=reviewStateLabel(s);
    if(s==='reviewed') el.className+=' bg-emerald-50 text-emerald-700 border-emerald-200';
    else if(s==='pending_review') el.className+=' bg-amber-50 text-amber-700 border-amber-200';
    else el.className+=' bg-slate-100 text-slate-500 border-slate-200'; return el;
  }
  function sourceBadge(source){
    const el=document.createElement('span'); el.className='inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border';
    const icon=document.createElement('span'); icon.className='material-symbols-outlined text-[13px]'; icon.textContent=source==='lab'?'science':'radiology';
    const text=document.createElement('span'); text.textContent=source==='lab'?'مختبر':'أشعة';
    el.className += source==='lab' ? ' bg-cyan-50 text-cyan-700 border-cyan-200' : ' bg-purple-50 text-purple-700 border-purple-200'; el.append(icon,text); return el;
  }

  function updateSummary(){
    const needs=resultsList.filter(canReview).length;
    const ready=resultsList.filter(x=>x.resultState==='result_ready').length;
    const progress=resultsList.filter(x=>x.resultState==='in_progress').length;
    const reviewed=resultsList.filter(x=>x.doctorReviewState==='reviewed').length;
    document.getElementById('summary-needs-review').textContent=needs;
    document.getElementById('summary-ready').textContent=ready;
    document.getElementById('summary-in-progress').textContent=progress;
    document.getElementById('summary-reviewed').textContent=reviewed;
    document.getElementById('tab-count-needs-review').textContent=needs;
    document.getElementById('tab-count-all-results').textContent=resultsList.length;
    document.getElementById('tab-count-in-progress').textContent=progress;
    document.getElementById('tab-count-reviewed').textContent=reviewed;
  }

  function getFilteredResults(){
    const q=safeString(document.getElementById('results-filter-search').value).toLowerCase();
    const source=document.getElementById('results-filter-source').value;
    const state=document.getElementById('results-filter-state').value;
    const review=document.getElementById('results-filter-review').value;
    const sort=document.getElementById('results-filter-sort').value;
    let rows=resultsList.filter(item=>{
      if(currentTab==='needs_review' && !canReview(item)) return false;
      if(currentTab==='in_progress' && item.resultState!=='in_progress') return false;
      if(currentTab==='reviewed' && item.doctorReviewState!=='reviewed') return false;
      if(source && item.source!==source) return false;
      if(state && item.resultState!==state) return false;
      if(review && item.doctorReviewState!==review) return false;
      if(q){ const hay=[item.patientName,item.patientId,item.orderNumber,item.service,item.provider].join(' ').toLowerCase(); if(!hay.includes(q)) return false; }
      return true;
    });
    rows.sort((a,b)=>{
      if(sort==='oldest') return (a.isoDate||a.id).localeCompare(b.isoDate||b.id);
      if(sort==='needs_review') { const ar=canReview(a)?0:1, br=canReview(b)?0:1; if(ar!==br)return ar-br; }
      return (b.isoDate||b.id).localeCompare(a.isoDate||a.id);
    });
    return rows;
  }

  function emptyRow(title, copy){
    const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=9; td.className='px-6 py-14 text-center';
    const wrap=document.createElement('div'); wrap.className='flex flex-col items-center max-w-md mx-auto';
    const iconBox=document.createElement('div'); iconBox.className='w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3';
    const icon=document.createElement('span'); icon.className='material-symbols-outlined text-2xl'; icon.textContent='assignment_turned_in'; iconBox.append(icon);
    const h=document.createElement('div'); h.className='font-bold text-slate-700 text-sm'; h.textContent=title;
    const p=document.createElement('div'); p.className='text-xs text-slate-400 mt-1'; p.textContent=copy; wrap.append(iconBox,h,p); td.append(wrap); tr.append(td); return tr;
  }

  function render(){
    updateSummary();
    const tbody=document.getElementById('results-table-body'); const mobile=document.getElementById('results-mobile-container');
    tbody.replaceChildren(); mobile.replaceChildren();
    const rows=getFilteredResults();
    const summary=document.getElementById('results-filter-summary');
    if(resultsList.length===0){ tbody.append(emptyRow('لا توجد نتائج متاحة حاليًا','ستظهر هنا نتائج المختبر وتقارير الأشعة عندما تصل إلى مرحلة التنفيذ أو المراجعة.')); summary.textContent='لا توجد نتائج متاحة حاليًا'; renderMobileEmpty(mobile,'لا توجد نتائج متاحة حاليًا'); return; }
    if(rows.length===0){ tbody.append(emptyRow('لا توجد نتائج مطابقة للفلاتر الحالية','راجع البحث أو عوامل التصفية لعرض نتائج أخرى.')); summary.textContent='لا توجد نتائج مطابقة للفلاتر الحالية'; renderMobileEmpty(mobile,'لا توجد نتائج مطابقة للفلاتر'); return; }
    summary.textContent=`يعرض ${rows.length} من أصل ${resultsList.length} نتيجة`;
    rows.forEach(item=>{ renderDesktopRow(item,tbody); renderMobileCard(item,mobile); });
  }

  function renderDesktopRow(item,tbody){
    const tr=document.createElement('tr'); tr.className='hover:bg-slate-50/80 transition-colors';
    const tdPatient=document.createElement('td'); tdPatient.className='py-3.5 px-4';
    const pWrap=document.createElement('div'); pWrap.className='flex items-center gap-2.5'; const avatar=document.createElement('div'); avatar.className='w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] flex-shrink-0'; avatar.textContent=item.patientInitials||'—';
    const pInfo=document.createElement('div'); const pName=document.createElement('div'); pName.className='font-bold text-slate-900'; pName.textContent=item.patientName||'—'; const meta=document.createElement('div'); meta.className='flex items-center gap-1.5 text-[11px] text-slate-400'; const id=document.createElement('span'); id.className='bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] font-num'; id.dir='ltr'; id.textContent=item.patientId||'—'; const m=document.createElement('span'); m.textContent=patientMeta(item); meta.append(id,m); pInfo.append(pName,meta); pWrap.append(avatar,pInfo); tdPatient.append(pWrap); tr.append(tdPatient);
    const tdOrder=document.createElement('td'); tdOrder.className='py-3.5 px-4 max-w-[240px]'; const orderId=document.createElement('button'); orderId.type='button'; orderId.className='block font-bold font-num text-brand text-xs hover:underline'; orderId.textContent=item.orderNumber; orderId.addEventListener('click',()=>openDetails(item.key)); const service=document.createElement('div'); service.className='text-slate-700 font-medium truncate mt-0.5'; service.textContent=item.service; tdOrder.append(orderId,service); tr.append(tdOrder);
    const tdSource=document.createElement('td'); tdSource.className='py-3.5 px-4'; tdSource.append(sourceBadge(item.source)); tr.append(tdSource);
    const tdDate=document.createElement('td'); tdDate.className='py-3.5 px-4 text-slate-600 whitespace-nowrap'; tdDate.textContent=item.orderDate||'—'; tr.append(tdDate);
    const tdProvider=document.createElement('td'); tdProvider.className='py-3.5 px-4 text-slate-700 max-w-[150px] truncate'; tdProvider.textContent=item.provider; tr.append(tdProvider);
    const tdResult=document.createElement('td'); tdResult.className='py-3.5 px-4'; tdResult.append(resultBadge(item.resultState)); tr.append(tdResult);
    const tdReport=document.createElement('td'); tdReport.className='py-3.5 px-4'; tdReport.append(reportBadge(item.providerReportState)); tr.append(tdReport);
    const tdReview=document.createElement('td'); tdReview.className='py-3.5 px-4'; tdReview.append(reviewBadge(item.doctorReviewState)); tr.append(tdReview);
    const tdActions=document.createElement('td'); tdActions.className='py-3.5 px-4 text-center'; const btn=document.createElement('button'); btn.type='button'; btn.className='w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center mx-auto'; const icon=document.createElement('span'); icon.className='material-symbols-outlined text-lg'; icon.textContent='more_vert'; btn.append(icon); btn.addEventListener('click',e=>{e.stopPropagation();openMenu(e,item.key);}); tdActions.append(btn); tr.append(tdActions);
    tbody.append(tr);
  }

  function renderMobileCard(item,container){
    const card=document.createElement('div'); card.className='bg-white border border-slate-200 rounded-xl p-4 shadow-sm space-y-3';
    const top=document.createElement('div'); top.className='flex items-center justify-between gap-2'; const left=document.createElement('div'); left.className='flex items-center gap-2'; left.append(sourceBadge(item.source)); const num=document.createElement('button'); num.type='button'; num.className='font-bold font-num text-brand text-xs hover:underline'; num.textContent=item.orderNumber; num.addEventListener('click',()=>openDetails(item.key)); left.append(num); const more=document.createElement('button'); more.type='button'; more.className='w-7 h-7 rounded-lg text-slate-400 hover:bg-slate-100 flex items-center justify-center'; const mi=document.createElement('span'); mi.className='material-symbols-outlined text-base'; mi.textContent='more_vert'; more.append(mi); more.addEventListener('click',e=>{e.stopPropagation();openMenu(e,item.key);}); top.append(left,more);
    const patient=document.createElement('div'); patient.className='flex items-center gap-2.5'; const av=document.createElement('div'); av.className='w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs'; av.textContent=item.patientInitials||'—'; const info=document.createElement('div'); const pn=document.createElement('div'); pn.className='font-bold text-slate-900 text-xs'; pn.textContent=item.patientName||'—'; const pm=document.createElement('div'); pm.className='text-[11px] text-slate-400'; pm.textContent=`${item.patientId||'—'} • ${patientMeta(item)}`; info.append(pn,pm); patient.append(av,info);
    const service=document.createElement('div'); service.className='bg-slate-50 rounded-lg p-2.5'; const sl=document.createElement('div'); sl.className='text-[10px] text-slate-400 mb-0.5'; sl.textContent='الفحص / التصوير'; const sv=document.createElement('div'); sv.className='font-bold text-slate-800 text-xs'; sv.textContent=item.service; service.append(sl,sv);
    const badges=document.createElement('div'); badges.className='flex flex-wrap items-center gap-1.5 pt-1'; badges.append(resultBadge(item.resultState),reportBadge(item.providerReportState),reviewBadge(item.doctorReviewState)); card.append(top,patient,service,badges); container.append(card);
  }
  function renderMobileEmpty(container,text){ const box=document.createElement('div'); box.className='bg-white border border-slate-200 rounded-xl p-8 text-center text-sm text-slate-500'; box.textContent=text; container.append(box); }

  function openMenu(event,key){
    const menu=document.getElementById('result-row-menu'); const item=resultsList.find(x=>x.key===key); if(!item)return; menu.replaceChildren();
    const add=(iconText,label,fn,highlight=false)=>{ const btn=document.createElement('button'); btn.type='button'; btn.className=`w-full text-right px-3 py-2 hover:bg-slate-50 rounded-lg flex items-center gap-2.5 transition-colors ${highlight?'text-brand font-bold':'text-slate-700'}`; const ic=document.createElement('span'); ic.className=`material-symbols-outlined text-lg ${highlight?'text-brand':'text-slate-400'}`; ic.textContent=iconText; const tx=document.createElement('span'); tx.textContent=label; btn.append(ic,tx); btn.addEventListener('click',()=>{closeMenu();fn();}); menu.append(btn); };
    add('visibility','عرض التفاصيل',()=>openDetails(key)); add('open_in_new','فتح الطلب الأصلي',()=>showToast('فتح الطلب الأصلي جاهز للربط مع التنقل','info')); if(item.visitTitle) add('stethoscope','فتح الزيارة المرتبطة',()=>showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل','info')); if(canReview(item)){ const div=document.createElement('div'); div.className='my-1 border-t border-slate-100'; menu.append(div); add('verified','تسجيل مراجعة الطبيب',()=>reviewResult(key),true); }
    positionMenu(menu,event.currentTarget);
  }
  function positionMenu(menu,trigger){ const rect=trigger.getBoundingClientRect(), w=220, pad=8, gap=6; menu.classList.remove('hidden'); const h=menu.offsetHeight||150; let top=rect.bottom+gap; if(top+h>window.innerHeight-pad) top=rect.top-h-gap; top=Math.max(pad,Math.min(top,window.innerHeight-h-pad)); let left=rect.right-w; left=Math.max(pad,Math.min(left,window.innerWidth-w-pad)); menu.style.top=`${Math.round(top)}px`; menu.style.left=`${Math.round(left)}px`; }
  function closeMenu(){ const menu=document.getElementById('result-row-menu'); menu.classList.add('hidden'); menu.replaceChildren(); }

  function openDetails(key){
    const item=resultsList.find(x=>x.key===key); if(!item)return; activeResultKey=key;
    document.getElementById('result-details-title').textContent=item.orderNumber;
    document.getElementById('result-details-icon').textContent=item.source==='lab'?'science':'radiology';
    const sb=document.getElementById('result-details-source-badge'); sb.replaceWith(sourceBadgeForDetails(item.source,sb.id));
    document.getElementById('result-details-subtitle').textContent=`تاريخ الطلب: ${item.orderDate||'—'}`;
    document.getElementById('result-details-patient-name').textContent=item.patientName||'—'; document.getElementById('result-details-patient-id').textContent=item.patientId||'—'; document.getElementById('result-details-patient-meta').textContent=patientMeta(item); document.getElementById('result-details-doctor').textContent=item.doctorName||'—';
    setBadgeContainer('result-details-result-state',resultBadge(item.resultState)); setBadgeContainer('result-details-report-state',reportBadge(item.providerReportState)); setBadgeContainer('result-details-review-state',reviewBadge(item.doctorReviewState));
    document.getElementById('result-details-service').textContent=item.service; document.getElementById('result-details-provider').textContent=item.provider; document.getElementById('result-details-visit').textContent=item.visitTitle||'—'; document.getElementById('result-details-context').textContent=item.clinicalContext||'—';
    document.getElementById('result-details-report-copy').textContent=item.providerReportState==='unavailable'?'لا يوجد تقرير منشور من جهة التنفيذ ضمن البيانات الحالية.':'حالة التقرير متاحة، لكن محتوى التقرير التفصيلي غير موجود ضمن بيانات النموذج الحالي.';
    document.getElementById('result-doctor-review-box').classList.toggle('hidden',!canReview(item));
    const reviewNoteInput=document.getElementById('result-review-note'); if(reviewNoteInput) reviewNoteInput.value='';
    document.getElementById('drawer-result-details').classList.remove('hidden'); syncBodyScrollLock();
  }
  function sourceBadgeForDetails(source,id){ const el=sourceBadge(source); el.id=id; return el; }
  function setBadgeContainer(id,badge){ const el=document.getElementById(id); el.replaceChildren(badge); }
  function closeDetails(){ document.getElementById('drawer-result-details').classList.add('hidden'); activeResultKey=null; syncBodyScrollLock(); }

  function reviewResult(key){
    const item=resultsList.find(x=>x.key===key); if(!item||!canReview(item))return;
    item.doctorReviewState='reviewed';
    item.reviewedByDoctorId='doctor-demo-001';
    item.reviewedAt=new Date().toISOString();
    item.reviewNote=(document.getElementById('result-review-note')?.value||'').trim();
    if(!writeBackResult(item)){ showToast('تعذر تحديث المصدر المحلي للنتيجة','warning'); return; }
    loadResults(); render(); showToast('تم تسجيل مراجعة الطبيب','info'); if(activeResultKey===key) openDetails(key);
  }

  function showToast(message,type='info'){
    const holder=document.getElementById('results-toast-container'); const el=document.createElement('div'); el.className='pointer-events-auto min-w-[260px] max-w-sm bg-white border rounded-xl shadow-lg px-4 py-3 flex items-start gap-2 text-xs';
    const icon=document.createElement('span'); icon.className='material-symbols-outlined text-lg'; icon.textContent=type==='warning'?'warning':'info'; icon.classList.add(type==='warning'?'text-amber-500':'text-brand'); const text=document.createElement('div'); text.className='text-slate-700 font-medium'; text.textContent=message; el.append(icon,text); holder.append(el); setTimeout(()=>el.remove(),3200);
  }
  function syncBodyScrollLock(){ const drawer=document.getElementById('drawer-result-details'); document.body.classList.toggle('overflow-hidden',drawer&&!drawer.classList.contains('hidden')); }

  function initSidebarState(){ const sidebar=document.getElementById('app-sidebar'),pinBtn=document.getElementById('sidebar-pin-toggle'); if(!sidebar||!pinBtn)return; const saved=localStorage.getItem(SIDEBAR_PIN_KEY); isPinned=saved!==null?saved==='true':true; updateSidebarState(); pinBtn.addEventListener('click',e=>{e.stopPropagation();isPinned=!isPinned;localStorage.setItem(SIDEBAR_PIN_KEY,isPinned?'true':'false');updateSidebarState();}); sidebar.addEventListener('mouseenter',()=>{if(!isPinned)sidebar.classList.add('sidebar-hovered');}); sidebar.addEventListener('mouseleave',()=>{if(!isPinned){sidebar.classList.remove('sidebar-hovered');document.getElementById('doctor-account-menu')?.classList.add('hidden');}}); }
  function updateSidebarState(){ const sidebar=document.getElementById('app-sidebar'),main=document.getElementById('main-content-layout'),top=document.getElementById('top-header'),pinBtn=document.getElementById('sidebar-pin-toggle'),pinIcon=document.getElementById('sidebar-pin-icon'); if(!sidebar||!main||!top)return; sidebar.classList.remove('sidebar-hovered'); if(isPinned){sidebar.classList.remove('sidebar-collapsed');main.style.marginRight='268px';top.style.right='268px';pinBtn?.classList.add('text-[#0495F6]','bg-white/10');pinIcon?.classList.add('fill-1');if(pinBtn)pinBtn.title='طي القائمة الجانبية';}else{sidebar.classList.add('sidebar-collapsed');main.style.marginRight='72px';top.style.right='72px';pinBtn?.classList.remove('text-[#0495F6]','bg-white/10');pinIcon?.classList.remove('fill-1');if(pinBtn)pinBtn.title='تثبيت القائمة الجانبية';} if(pinIcon)pinIcon.textContent='push_pin'; }
  function initDoctorMenu(){ const trigger=document.getElementById('doctor-card-trigger'),menu=document.getElementById('doctor-account-menu'); if(!trigger||!menu)return; trigger.addEventListener('click',e=>{e.stopPropagation();menu.classList.toggle('hidden');}); document.addEventListener('click',e=>{if(!menu.contains(e.target)&&!trigger.contains(e.target))menu.classList.add('hidden');}); }
  function initQuickAdd(){ const container=document.getElementById('quick-add-container'),btn=document.getElementById('quick-add-btn'),dropdown=document.getElementById('quick-add-dropdown'),arrow=document.getElementById('quick-add-arrow'); if(!container||!btn||!dropdown||!arrow)return; let timer=null; const setOpen=open=>{if(open){if(timer)clearTimeout(timer);dropdown.classList.remove('hidden');arrow.textContent='expand_less';}else{dropdown.classList.add('hidden');arrow.textContent='expand_more';}}; btn.addEventListener('click',e=>{e.stopPropagation();setOpen(dropdown.classList.contains('hidden'));}); container.addEventListener('mouseenter',()=>setOpen(true)); container.addEventListener('mouseleave',()=>{timer=setTimeout(()=>setOpen(false),200);}); document.addEventListener('click',e=>{if(!container.contains(e.target))setOpen(false);}); dropdown.querySelectorAll('button').forEach(x=>x.addEventListener('click',()=>{setOpen(false);showToast('هذا الإجراء جاهز للربط مع الشاشة المخصصة له','info');})); }

  function resetFilters(){ document.getElementById('results-filter-search').value=''; document.getElementById('results-filter-source').value=''; document.getElementById('results-filter-state').value=''; document.getElementById('results-filter-review').value=''; document.getElementById('results-filter-sort').value='newest'; window.refreshTheraSelects?.(); render(); }
  function setActiveTab(btn){ document.querySelectorAll('.result-tab-btn').forEach(b=>{b.className='result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]'; const c=b.querySelector('.tab-count'); if(c)c.className='tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num';}); btn.className='result-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm'; const c=btn.querySelector('.tab-count'); if(c)c.className='tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-num'; }

  document.addEventListener('DOMContentLoaded',()=>{
    initSidebarState(); initDoctorMenu(); initQuickAdd(); loadResults(); render();
    document.querySelectorAll('.result-tab-btn').forEach(btn=>btn.addEventListener('click',()=>{currentTab=btn.dataset.tab;setActiveTab(btn);render();}));
    ['results-filter-search'].forEach(id=>document.getElementById(id).addEventListener('input',render)); ['results-filter-source','results-filter-state','results-filter-review','results-filter-sort'].forEach(id=>document.getElementById(id).addEventListener('change',render));
    document.getElementById('reset-results-filters-btn').addEventListener('click',resetFilters);
    document.getElementById('refresh-results-btn').addEventListener('click',()=>{loadResults();render();showToast('تم تحديث العرض من البيانات المحلية','info');});
    document.getElementById('close-result-details-btn').addEventListener('click',closeDetails); document.getElementById('close-result-details-footer-btn').addEventListener('click',closeDetails); document.getElementById('result-details-backdrop').addEventListener('click',closeDetails);
    document.getElementById('perform-result-review-btn').addEventListener('click',()=>{if(activeResultKey)reviewResult(activeResultKey);});
    document.getElementById('open-source-order-btn').addEventListener('click',()=>showToast('فتح الطلب الأصلي جاهز للربط مع التنقل','info')); document.getElementById('print-result-btn').addEventListener('click',()=>window.print());
    document.addEventListener('click',e=>{const menu=document.getElementById('result-row-menu');if(menu&&!menu.contains(e.target))closeMenu();}); window.addEventListener('resize',closeMenu); window.addEventListener('scroll',closeMenu,true); document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeMenu();closeDetails();}});
  });
})();
</script>

<script>
(function(){
  const registry = new Map();
  function closeAll(except){
    document.querySelectorAll('.thera-custom-select.open').forEach(w=>{ if(w!==except) w.classList.remove('open'); });
    document.querySelectorAll('.thera-custom-select-menu').forEach(m=>{ if(!except || m.parentElement!==except) m.classList.add('hidden'); });
  }
  function build(select){
    if(!select || registry.has(select)) return;
    const originalParent = select.parentElement;
    const wrapper = document.createElement('div'); wrapper.className = 'thera-custom-select';
    originalParent.insertBefore(wrapper, select); wrapper.appendChild(select); select.classList.add('thera-native-select-hidden');
    const trigger = document.createElement('button'); trigger.type='button'; trigger.className='thera-custom-select-trigger'; trigger.setAttribute('aria-haspopup','listbox');
    trigger.innerHTML='<span class="thera-custom-select-label"></span><span class="material-symbols-outlined thera-custom-select-arrow">expand_more</span>';
    const menu=document.createElement('div'); menu.className='thera-custom-select-menu hidden'; menu.setAttribute('role','listbox'); wrapper.append(trigger,menu);
    function refresh(){
      const selected=select.options[select.selectedIndex]||select.options[0]; wrapper.querySelector('.thera-custom-select-label').textContent=selected?selected.textContent.trim():''; menu.replaceChildren();
      Array.from(select.options).forEach(opt=>{ const b=document.createElement('button'); b.type='button'; b.className='thera-custom-select-option'; if(opt.value===select.value)b.classList.add('active'); b.setAttribute('role','option'); b.setAttribute('aria-selected',opt.value===select.value?'true':'false'); const t=document.createElement('span');t.textContent=opt.textContent.trim();const c=document.createElement('span');c.className='material-symbols-outlined check';c.textContent='check';b.append(t,c);b.addEventListener('click',e=>{e.stopPropagation();if(select.value!==opt.value){select.value=opt.value;select.dispatchEvent(new Event('input',{bubbles:true}));select.dispatchEvent(new Event('change',{bubbles:true}));}refresh();wrapper.classList.remove('open');menu.classList.add('hidden');});menu.appendChild(b); });
    }
    trigger.addEventListener('click',e=>{e.stopPropagation();const willOpen=menu.classList.contains('hidden');closeAll(willOpen?wrapper:null);wrapper.classList.toggle('open',willOpen);menu.classList.toggle('hidden',!willOpen);});
    select.addEventListener('change',refresh); registry.set(select,{refresh}); refresh();
  }
  window.refreshTheraSelects=()=>registry.forEach(x=>x.refresh());
  document.querySelectorAll('select').forEach(build); document.addEventListener('click',()=>closeAll()); document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAll();});
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>