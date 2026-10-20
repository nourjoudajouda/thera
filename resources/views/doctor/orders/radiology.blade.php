<!DOCTYPE html><html dir="rtl" lang="ar" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>طلبات الأشعة - THERA</title>
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
    .font-numeric-ui {
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
/* THERA unified controls — approved cross-page visual language */
.thera-major-btn { border-radius: 8px !important; min-height: 40px; }
.thera-icon-btn { border-radius: 8px !important; }
.thera-custom-select { position: relative; width: 100%; min-width: 0; }
.thera-custom-select-trigger {
  width: 100%; height: 40px; padding: 0 12px; background: #fff;
  border: 1px solid #E2E8F0; border-radius: 8px; color: #334155;
  font-size: 12px; font-weight: 600; display: flex; align-items: center;
  justify-content: space-between; gap: 8px; text-align: right; min-width: 0;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.thera-custom-select-trigger:hover { background:#F8FAFC; border-color:#CBD5E1; }
.thera-custom-select.open .thera-custom-select-trigger { border-color:#0495F6; box-shadow:0 0 0 2px rgba(4,149,246,.12); background:#fff; }
.thera-custom-select-label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.thera-custom-select-arrow { font-size:16px; color:#94A3B8; transition:transform .15s ease; flex-shrink:0; }
.thera-custom-select.open .thera-custom-select-arrow { transform:rotate(180deg); color:#0495F6; }
.thera-custom-select-menu {
  position:absolute; top:calc(100% + 6px); right:0; left:0; z-index:120;
  padding:6px; background:#fff; border:1px solid #E2E8F0; border-radius:10px;
  box-shadow:0 10px 28px rgba(15,23,42,.12); max-height:260px; overflow:auto; min-width:180px;
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
#rad-filter-search,#rad-filter-order-status,#rad-filter-priority,#rad-filter-provider,#rad-filter-result-status,#rad-filter-sort { min-width:0; width:100%; }
</style>

</head>
<body class="overflow-x-hidden select-none bg-[#F6F9FC] text-slate-800 antialiased min-h-screen">
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
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label">طلبات المختبر</span>
</a>
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
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
<main class="pt-16 p-6 min-h-screen bg-[#F6F9FC] transition-all duration-300" data-purpose="workspace-content" id="main-content-layout" style="margin-right:268px;">
  <!-- Page Header -->
  <section class="mt-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1 font-medium" aria-label="Breadcrumb">
        <span>العمل الطبي</span>
        <span class="material-symbols-outlined text-xs">chevron_left</span>
        <span class="text-brand font-bold">طلبات الأشعة</span>
      </nav>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">طلبات الأشعة</h1>
      <p class="text-xs text-slate-500 mt-1">إدارة ومتابعة طلبات الأشعة والتصوير الطبي المرتبطة بزيارات المرضى</p>
    </div>
    <button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm thera-major-btn" id="open-create-rad-order-btn" type="button">
      <span class="material-symbols-outlined text-xl">add</span>
      <span>طلب أشعة جديد</span>
    </button>
  </section>

    <!-- Status Tabs -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 mb-4" id="rad-order-tabs">
      <button class="rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm" data-tab="all" type="button">
        <span>الكل</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-numeric-ui" id="rad-tab-count-all">0</span>
      </button>
      <button class="rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="draft" type="button">
        <span>مسودات</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-ui" id="rad-tab-count-draft">0</span>
      </button>
      <button class="rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="documented" type="button">
        <span>موثقة</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-ui" id="rad-tab-count-documented">0</span>
      </button>
      <button class="rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="route_ready" type="button">
        <span>جاهزة للتوجيه</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-ui" id="rad-tab-count-route-ready">0</span>
      </button>
      <button class="rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="route_requested" type="button">
        <span>طلب توجيه</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-ui" id="rad-tab-count-route-requested">0</span>
      </button>
  </div>

<!-- Filters -->
  <section class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 shadow-soft overflow-visible">
    <div class="grid grid-cols-[minmax(180px,0.85fr)_minmax(112px,0.62fr)_minmax(100px,0.55fr)_minmax(120px,0.68fr)_minmax(118px,0.66fr)_minmax(135px,0.72fr)_40px] items-center gap-2 w-full min-w-0">
      <div class="relative min-w-0">
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
        <input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand focus:bg-white transition-all" id="rad-filter-search" placeholder="ابحث باسم المريض أو رقم المريض أو رقم الطلب..." type="text">
      </div>
      <div class="min-w-0">
        <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="rad-filter-order-status">
          <option value="">حالة الطلب: الكل</option><option value="draft">مسودة</option><option value="documented">موثق</option><option value="route_ready">جاهز للتوجيه</option><option value="route_requested">طلب توجيه</option><option value="cancelled">ملغى</option>
        </select>
      </div>
      <div class="min-w-0">
        <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="rad-filter-priority">
          <option value="">الأولوية: الكل</option><option value="normal">عادي</option><option value="urgent">عاجل</option>
        </select>
      </div>
      <div class="min-w-0">
        <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="rad-filter-provider">
          <option value="">جهة التنفيذ: الكل</option><option value="unassigned">غير محددة</option><option value="thera_network">شبكة THERA</option><option value="external">مركز خارجي</option>
        </select>
      </div>
      <div class="min-w-0">
        <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="rad-filter-result-status">
          <option value="">حالة النتيجة: الكل</option><option value="none">لا توجد نتيجة</option><option value="in_progress">قيد التنفيذ</option><option value="result_ready">النتيجة جاهزة</option>
        </select>
      </div>
      <div class="min-w-0">
        <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700" id="rad-filter-sort">
          <option value="newest">الترتيب: الأحدث أولاً</option><option value="oldest">الترتيب: الأقدم أولاً</option><option value="urgent">الترتيب: الأولوية العاجلة أولاً</option>
        </select>
      </div>
      <button aria-label="إعادة ضبط الفلاتر" class="h-10 w-10 text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold flex items-center justify-center transition-colors shrink-0" id="rad-reset-filters-btn" title="إعادة ضبط الفلاتر" type="button"><span class="material-symbols-outlined text-[18px]">restart_alt</span></button>
    </div>
    <span class="hidden" id="rad-filter-summary">يعرض كافة الطلبات الحالية</span>
  </section>

  <!-- Desktop Table: the header remains visible even when there are no orders -->
  <section class="hidden md:block bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="rad-table-card">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs min-w-[1280px]">
        <thead class="bg-slate-50 text-slate-500 font-bold text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">المريض</th>
            <th class="py-3 px-4">الطلب</th>
            <th class="py-3 px-4">المنطقة / العضو</th>
            <th class="py-3 px-4">التاريخ / الزيارة</th>
            <th class="py-3 px-4">الأولوية</th>
            <th class="py-3 px-4">حالة الطلب</th>
            <th class="py-3 px-4">جهة التنفيذ</th>
            <th class="py-3 px-4">النتيجة / التقرير</th>
            <th class="py-3 px-4">مراجعة الطبيب</th>
            <th class="py-3 px-4 text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100" id="rad-orders-table-body"></tbody>
      </table>
    </div>
  </section>

  <!-- Mobile empty states / cards -->
  <section class="md:hidden bg-white border border-slate-200 rounded-2xl p-10 text-center shadow-sm" id="rad-initial-empty-state">
    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand flex items-center justify-center mx-auto mb-4"><span class="material-symbols-outlined text-3xl">radiology</span></div>
    <h3 class="text-base font-bold text-slate-900 mb-1">لا توجد طلبات أشعة</h3>
    <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">لم يتم إنشاء أي طلب أشعة حتى الآن.</p>
    <button class="h-10 px-5 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold text-xs inline-flex items-center gap-2 shadow-sm" id="rad-empty-create-btn" type="button"><span class="material-symbols-outlined text-lg">add</span><span>طلب أشعة جديد</span></button>
  </section>

  <section class="hidden md:hidden bg-white border border-slate-200 rounded-2xl p-10 text-center shadow-sm" id="rad-filter-empty-state">
    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-2xl">search_off</span></div>
    <h3 class="text-sm font-bold text-slate-900 mb-1">لا توجد طلبات أشعة مطابقة للفلاتر الحالية</h3>
    <p class="text-xs text-slate-500 mb-4">يرجى مراجعة كلمات البحث أو إعادة ضبط خيارات التصفية لعرض النتائج.</p>
    <button class="h-8 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs" id="rad-filter-empty-reset-btn" type="button">إعادة ضبط الفلاتر</button>
  </section>

  <div class="md:hidden space-y-3" id="rad-mobile-orders-container"></div>
</main>

<!-- Create / Edit Drawer -->
<aside aria-labelledby="drawer-create-rad-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawer-create-rad-order" role="dialog">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" id="drawer-create-rad-backdrop"></div>
  <div class="fixed inset-y-0 left-0 max-w-full flex">
    <div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
      <header class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl">radiology</span></div>
          <div><h2 class="text-base font-bold text-slate-900" id="drawer-create-rad-title">طلب أشعة جديد</h2><p class="text-xs text-slate-500">إنشاء وتوثيق طلب تصوير طبي للمريض</p></div>
        </div>
        <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center" id="drawer-create-rad-close" type="button"><span class="material-symbols-outlined text-xl">close</span></button>
      </header>
      <form class="flex-1 overflow-y-auto p-6 space-y-5 text-xs" id="rad-order-form" onsubmit="return false;">
        <input id="rad-form-order-id" type="hidden" value="">
        <section class="space-y-2">
          <label class="block font-bold text-slate-700">المريض *</label>
          <div class="relative" id="rad-patient-search-box">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rad-patient-search-input" placeholder="ابحث عن مريض بالاسم أو رقم المريض..." type="text">
            <div class="hidden absolute top-full right-0 left-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-dropdown z-30 py-1 max-h-48 overflow-y-auto" id="rad-patient-suggestions"></div>
          </div>
          <div class="hidden bg-blue-50/70 border border-blue-100 rounded-xl p-3 flex items-center justify-between" id="rad-selected-patient-card">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs" id="rad-patient-avatar-badge">أ م</div><div><div class="font-bold text-slate-900 text-xs" id="rad-patient-name-display">أحمد محمود</div><div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-numeric-ui"><span class="bg-white border border-blue-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="rad-patient-id-badge">P-10482</span><span>•</span><span id="rad-patient-meta-display">42 سنة • ذكر</span></div></div></div>
            <button class="text-xs text-brand font-bold hover:underline" id="rad-change-patient-btn" type="button">تغيير</button>
          </div>
        </section>

        <section class="space-y-1.5">
          <label class="block font-bold text-slate-700">الزيارة المرتبطة</label>
          <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rad-form-visit">
            <option value="visit_2024_10_18">كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد</option>
            <option value="visit_2024_10_12">متابعة سريرية — 12 أكتوبر 2024 — د. محمد أحمد</option>
            <option value="none">بدون ربط بزيارة محددة</option>
          </select>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">نوع التصوير *</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rad-form-imaging-type"><option value="">اختر نوع التصوير</option><option value="xray">أشعة سينية X-Ray</option><option value="ultrasound">ألتراساوند Ultrasound</option><option value="ct">تصوير طبقي CT</option><option value="mri">رنين مغناطيسي MRI</option><option value="mammography">ماموغرام Mammography</option><option value="other">أخرى</option></select></div>
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">المنطقة / العضو المطلوب تصويره *</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rad-form-body-area" placeholder="أدخل المنطقة أو العضو المطلوب تصويره..." type="text"></div>
        </section>

        <section class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">الأولوية</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rad-form-priority"><option value="normal">عادي</option><option value="urgent">عاجل</option></select></div>
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">الطبيب الطالب</label><input class="w-full h-10 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-600 cursor-not-allowed" readonly type="text" value="د. محمد أحمد - استشاري طب باطني"></div>
        </section>

        <section class="space-y-1.5"><label class="block font-bold text-slate-700">السياق السريري</label><textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="rad-form-clinical-context" placeholder="أدخل السياق السريري المرتبط بطلب الأشعة..." rows="2"></textarea></section>
        <section class="space-y-1.5"><label class="block font-bold text-slate-700">السؤال السريري</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rad-form-clinical-question" placeholder="ما المطلوب تقييمه أو استبعاده من خلال التصوير؟" type="text"></section>
        <section class="space-y-1.5"><label class="block font-bold text-slate-700">تعليمات إضافية</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rad-form-instructions" placeholder="أدخل تعليمات إضافية لجهة التنفيذ..." type="text"></section>

        <section class="space-y-2 pt-2 border-t border-slate-100">
          <label class="block font-bold text-slate-800">جهة التنفيذ والإرسال</label>
          <p class="text-[11px] text-slate-500 mb-2">يمكن حفظ الطلب دون جهة تنفيذ، بينما الإرسال يتطلب تحديد مركز أشعة.</p>
          <div class="space-y-2">
            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input checked class="text-brand focus:ring-brand" name="rad_destination_mode" type="radio" value="unassigned"><div><span class="font-bold text-slate-800">بدون تحديد جهة الآن</span><p class="text-[11px] text-slate-500">حفظ الطلب وتوجيهه لاحقًا عند الحاجة</p></div></label>
            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input class="text-brand focus:ring-brand" name="rad_destination_mode" type="radio" value="thera_network"><div><span class="font-bold text-slate-800">مركز أشعة من شبكة THERA</span><p class="text-[11px] text-slate-500">اختر مركزًا محددًا من شبكة THERA عند توفر بيانات مزودي الخدمة.</p></div></label>
            <div class="hidden pr-6 pl-2 space-y-1.5" id="rad-thera-network-options">
              <div class="relative"><span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span><input class="w-full h-9 pr-8 pl-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="rad-network-search" placeholder="ابحث عن مركز أشعة..." type="text" autocomplete="off"></div>
              <p class="text-[11px] text-slate-500">اختر مركزًا من القائمة التجريبية أدناه. سيتم استبدالها بمزودي الخدمة الحقيقيين عند ربط الـAPI.</p>
              <div class="hidden max-h-44 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-sm divide-y divide-slate-100" id="rad-network-results"></div>
              <div class="hidden p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 text-center text-xs" id="rad-network-empty-state">لا توجد مراكز مطابقة للبحث</div>
            </div>
            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input class="text-brand focus:ring-brand" name="rad_destination_mode" type="radio" value="external"><div><span class="font-bold text-slate-800">مركز أشعة خارجي</span><p class="text-[11px] text-slate-500">جهة خارج شبكة THERA</p></div></label>
            <div class="hidden pr-6 pl-2" id="rad-external-options"><input class="w-full h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="rad-form-external-name" placeholder="أدخل اسم مركز الأشعة الخارجي..." type="text"></div>
          </div>
        </section>
      </form>
      <footer class="px-4 sm:px-6 py-4 bg-slate-50 border-t border-slate-200">
        <div class="flex items-center gap-2 flex-nowrap w-full overflow-x-auto no-scrollbar">
          <button class="h-10 px-3 text-slate-600 hover:bg-slate-200/60 rounded-xl font-bold text-[11px] sm:text-xs whitespace-nowrap shrink-0" id="drawer-create-rad-cancel" type="button">إلغاء</button>
          <button class="h-10 px-3 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl font-bold text-[11px] sm:text-xs whitespace-nowrap shrink-0" id="rad-save-as-draft-btn" type="button">حفظ كمسودة</button>
          <button class="h-10 px-3 bg-blue-50 border border-blue-200 text-[#0369A1] hover:bg-blue-100 rounded-xl font-bold text-[11px] sm:text-xs whitespace-nowrap shrink-0" id="rad-save-as-documented-btn" type="button">حفظ كموثق</button>
          <button class="h-10 px-3.5 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold text-[11px] sm:text-xs shadow-sm whitespace-nowrap flex-1 min-w-[142px]" id="rad-save-and-send-btn" type="button">إرسال إلى مركز الأشعة</button>
        </div>
      </footer>
    </div>
  </div>
</aside>

<!-- Details Drawer -->
<aside aria-labelledby="rad-details-id-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawer-details-rad-order" role="dialog">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" id="drawer-details-rad-backdrop"></div>
  <div class="fixed inset-y-0 left-0 max-w-full flex">
    <div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
      <header class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-3"><div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl">radiology</span></div><div><div class="flex items-center gap-2"><h2 class="text-base font-bold text-slate-900 font-numeric-ui" id="rad-details-id-title">RAD-0000</h2><span class="px-2 py-0.5 rounded-md text-[10px] font-bold" id="rad-details-priority-badge">عادي</span></div><p class="text-xs text-slate-500" id="rad-details-order-date">تاريخ الطلب: --</p></div></div>
        <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center" id="drawer-details-rad-close" type="button"><span class="material-symbols-outlined text-xl">close</span></button>
      </header>
      <div class="flex-1 overflow-y-auto p-6 space-y-6 text-xs">
        <section class="bg-slate-50 rounded-xl p-4 border border-slate-200"><div class="text-[11px] font-bold text-slate-400 uppercase mb-2">بيانات المريض</div><div class="flex items-center justify-between"><div><div class="text-sm font-bold text-slate-900" id="rad-details-patient-name">--</div><div class="flex items-center gap-1.5 text-slate-500 font-numeric-ui mt-0.5"><span class="bg-white border border-slate-200 text-slate-700 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="rad-details-patient-id-badge">--</span><span>•</span><span id="rad-details-patient-meta">--</span></div></div><div class="text-left"><span class="text-[11px] text-slate-400 block">الطبيب المعالج</span><span class="font-bold text-slate-800" id="rad-details-doctor-name">--</span></div></div></section>
        <section class="grid grid-cols-2 gap-3"><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">حالة الطلب</span><div id="rad-details-status-pill"></div></div><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">جهة التنفيذ</span><div class="font-bold text-slate-800" id="rad-details-provider-text">—</div></div></section>
        <section class="grid grid-cols-3 gap-3"><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">حالة النتيجة</span><div id="rad-details-result-pill"></div></div><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">حالة التقرير</span><div id="rad-details-report-pill"></div></div><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">مراجعة الطبيب</span><div id="rad-details-review-pill"></div></div></section>
        <section class="space-y-3"><div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">نوع التصوير</span><div class="text-sm font-bold text-slate-900" id="rad-details-imaging-type">—</div></div><div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">المنطقة / العضو</span><div class="text-slate-800 font-medium" id="rad-details-body-area">—</div></div><div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">الزيارة المرتبطة</span><div class="text-slate-800 font-medium" id="rad-details-visit-text">—</div></div><div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">السياق السريري</span><div class="text-slate-800" id="rad-details-clinical-text">—</div></div><div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">السؤال السريري</span><div class="text-slate-800" id="rad-details-question-text">—</div></div><div><span class="text-slate-400 block mb-1">تعليمات إضافية</span><div class="text-slate-800" id="rad-details-instructions-text">—</div></div></section>
        <section class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden" id="rad-doctor-review-box"><div class="flex items-start gap-3"><span class="material-symbols-outlined text-brand text-2xl">verified</span><div class="flex-1"><h4 class="font-bold text-slate-900 text-xs mb-1">النتيجة متاحة للمراجعة</h4><p class="text-[11px] text-slate-600 mb-3">يمكن تسجيل مراجعة الطبيب بعد الاطلاع على البيانات المتاحة.</p><button class="h-8 px-4 bg-brand hover:bg-brand-hover text-white rounded-lg font-bold text-xs" id="rad-perform-doctor-review-btn" type="button">تسجيل مراجعة الطبيب</button></div></div></section>
        <section class="bg-slate-50 border border-slate-200 rounded-xl p-4 hidden" id="rad-routing-action-box"><h4 class="font-bold text-slate-800 text-xs mb-1">تحديد جهة التنفيذ</h4><p class="text-[11px] text-slate-500 mb-3">يمكن تحديد مركز الأشعة وتجهيز الطلب للإرسال.</p><div class="space-y-3"><select class="rtl-select w-full h-9 bg-white border border-slate-200 rounded-lg text-xs text-slate-700" id="rad-details-route-destination-mode"><option value="unassigned">بدون تحديد جهة الآن</option><option value="thera_network">مركز أشعة من شبكة THERA</option><option value="external">مركز أشعة خارجي</option></select><div class="hidden space-y-1.5" id="rad-details-route-network-box"><div class="relative"><span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span><input class="w-full h-9 pr-8 pl-3 bg-white border border-slate-200 rounded-lg text-xs" id="rad-details-route-network-search" placeholder="ابحث عن مركز أشعة..." type="text" autocomplete="off"></div><p class="text-[11px] text-slate-500">اختر مركزًا من القائمة التجريبية أدناه.</p><div class="hidden max-h-44 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-sm divide-y divide-slate-100" id="rad-details-route-network-results"></div><div class="hidden p-3 bg-white border border-slate-200 rounded-lg text-slate-400 text-center text-xs" id="rad-details-route-empty-state">لا توجد مراكز مطابقة للبحث</div></div><div class="hidden space-y-1.5" id="rad-details-route-external-box"><input class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs" id="rad-details-route-external-input" placeholder="أدخل اسم مركز الأشعة الخارجي..." type="text"></div><button class="h-8 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold text-xs shadow-sm" id="rad-confirm-routing-btn" type="button">تجهيز للإرسال</button></div></section>
      </div>
      <footer class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between"><button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-xl font-bold" id="drawer-details-rad-cancel" type="button">إغلاق</button><button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-bold flex items-center gap-2" id="rad-print-order-btn" type="button"><span class="material-symbols-outlined text-base">print</span><span>طباعة طلب الأشعة</span></button></footer>
    </div>
  </div>
</aside>

<div class="hidden fixed bg-white rounded-xl shadow-lg border border-[#E2E8F0] p-1.5 z-[80] text-slate-700 text-xs w-[220px] font-medium" dir="rtl" id="rad-row-menu"></div>
<div class="fixed bottom-6 left-6 z-[90] flex flex-col gap-2 pointer-events-none" id="rad-toast-container"></div>

<script data-purpose="radiology-orders-app">
(() => {
  'use strict';

  const STORAGE_KEY = 'thera_radiology_orders_doctor_demo';
  const VERSION_KEY = 'thera_radiology_orders_doctor_demo_version';
  const CURRENT_STORAGE_VERSION = '1';
  const SIDEBAR_PIN_KEY = 'thera_sidebar_pinned';

  const DEMO_PATIENTS = [
    { id: 'P-10482', name: 'أحمد محمود', initials: 'أ م', age: '42', gender: 'ذكر' },
    { id: 'P-10483', name: 'سارة خالد', initials: 'س خ', age: '29', gender: 'أنثى' },
    { id: 'P-10484', name: 'عمر ياسين', initials: 'ع ي', age: '58', gender: 'ذكر' }
  ];

  // Demo providers used only in this standalone prototype until the real provider API is connected.
  const DEMO_RADIOLOGY_CENTERS = [
    { id: 'RAD-DEMO-001', name: 'مركز الأشعة التشخيصية التجريبي', city: 'نابلس' },
    { id: 'RAD-DEMO-002', name: 'مركز التصوير المتقدم التجريبي', city: 'رام الله' },
    { id: 'RAD-DEMO-003', name: 'مركز الرنين والطبقي التجريبي', city: 'الخليل' },
    { id: 'RAD-DEMO-004', name: 'مركز التصوير الطبي التجريبي', city: 'جنين' }
  ];

  const VISITS = {
    visit_2024_10_18: 'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد',
    visit_2024_10_12: 'متابعة سريرية — 12 أكتوبر 2024 — د. محمد أحمد'
  };

  const IMAGING_TYPES = {
    xray: 'أشعة سينية X-Ray',
    ultrasound: 'ألتراساوند Ultrasound',
    ct: 'تصوير طبقي CT',
    mri: 'رنين مغناطيسي MRI',
    mammography: 'ماموغرام Mammography',
    other: 'أخرى'
  };

  let radOrdersList = [];
  let currentFilterTab = 'all';
  let selectedPatient = null;
  let selectedTheraRadiologyCenter = null;
  let selectedDetailsTheraRadiologyCenter = null;
  let activeDetailsOrderId = null;
  let routingRadOrderId = null;
  let isPinned = true;

  document.addEventListener('DOMContentLoaded', () => {
    initSidebarState();
    initDoctorMenu();
    initQuickAdd();
    initStorage();
    initEvents();
    renderOrders();
  });

  function safeString(value) { return typeof value === 'string' ? value : ''; }
  function getPatientMeta(order) {
    const age = order.patientAge ? `${order.patientAge} سنة` : '—';
    return `${age} • ${order.patientGender || '—'}`;
  }
  function normalizeRadiologyOrder(raw) {
    if (!raw || typeof raw !== 'object') return null;
    const id = safeString(raw.id);
    const orderNumber = safeString(raw.orderNumber);
    if (!id || !orderNumber) return null;
    return {
      id,
      orderNumber,
      patientId: safeString(raw.patientId),
      patientName: safeString(raw.patientName),
      patientInitials: safeString(raw.patientInitials),
      patientAge: safeString(String(raw.patientAge ?? '')),
      patientGender: safeString(raw.patientGender),
      visitTitle: safeString(raw.visitTitle),
      doctorName: safeString(raw.doctorName),
      orderDate: safeString(raw.orderDate),
      isoDate: safeString(raw.isoDate),
      imagingType: Object.prototype.hasOwnProperty.call(IMAGING_TYPES, raw.imagingType) ? raw.imagingType : '',
      bodyArea: safeString(raw.bodyArea),
      clinicalContext: safeString(raw.clinicalContext),
      clinicalQuestion: safeString(raw.clinicalQuestion),
      priority: raw.priority === 'urgent' ? 'urgent' : 'normal',
      instructions: safeString(raw.instructions),
      workflowState: ['draft','documented','route_ready','route_requested','cancelled'].includes(raw.workflowState) ? raw.workflowState : 'draft',
      destinationMode: ['unassigned','thera_network','external'].includes(raw.destinationMode) ? raw.destinationMode : 'unassigned',
      radiologyCenterId: safeString(raw.radiologyCenterId),
      radiologyCenterName: safeString(raw.radiologyCenterName),
      resultState: ['none','in_progress','result_ready'].includes(raw.resultState) ? raw.resultState : 'none',
      providerReportState: ['unavailable','published','approved'].includes(raw.providerReportState) ? raw.providerReportState : 'unavailable',
      doctorReviewState: ['unavailable','pending_review','reviewed'].includes(raw.doctorReviewState) ? raw.doctorReviewState : 'unavailable'
    };
  }

  function createEmptyTableRow(title, description, actionLabel = '', actionHandler = null) {
    const tr = document.createElement('tr');
    const td = document.createElement('td');
    td.colSpan = 10;
    td.className = 'px-6 py-14 text-center';
    const wrap = document.createElement('div');
    wrap.className = 'max-w-md mx-auto flex flex-col items-center';
    const iconBox = document.createElement('div');
    iconBox.className = 'w-12 h-12 rounded-xl bg-blue-50 text-brand flex items-center justify-center mb-3';
    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined text-2xl';
    icon.textContent = actionHandler ? 'radiology' : 'search_off';
    iconBox.append(icon);
    const heading = document.createElement('div');
    heading.className = 'text-sm font-bold text-slate-800';
    heading.textContent = title;
    const text = document.createElement('div');
    text.className = 'text-xs text-slate-500 mt-1';
    text.textContent = description;
    wrap.append(iconBox, heading, text);
    if (actionLabel && actionHandler) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mt-4 h-9 px-4 rounded-xl bg-[#0495F6] hover:bg-[#0466F1] text-white text-xs font-bold inline-flex items-center gap-1.5';
      const plus = document.createElement('span');
      plus.className = 'material-symbols-outlined text-base';
      plus.textContent = 'add';
      const label = document.createElement('span');
      label.textContent = actionLabel;
      btn.append(plus, label);
      btn.addEventListener('click', actionHandler);
      wrap.append(btn);
    }
    td.append(wrap);
    tr.append(td);
    return tr;
  }

  function renderRadiologyCenterResults(inputEl, resultsEl, emptyEl, onSelect) {
    const query = inputEl.value.trim().toLowerCase();
    const matches = DEMO_RADIOLOGY_CENTERS.filter(center =>
      !query || center.name.toLowerCase().includes(query) || center.city.toLowerCase().includes(query)
    );
    resultsEl.replaceChildren();
    emptyEl.classList.toggle('hidden', matches.length > 0);
    resultsEl.classList.toggle('hidden', matches.length === 0);
    matches.forEach(center => {
      const row = document.createElement('button');
      row.type = 'button';
      row.className = 'w-full px-3 py-2.5 text-right hover:bg-slate-50 flex items-center justify-between gap-3';
      const info = document.createElement('div');
      const name = document.createElement('div');
      name.className = 'text-xs font-bold text-slate-800';
      name.textContent = center.name;
      const meta = document.createElement('div');
      meta.className = 'text-[10px] text-slate-400 mt-0.5';
      meta.textContent = `${center.city} • مزود تجريبي`;
      info.append(name, meta);
      const arrow = document.createElement('span');
      arrow.className = 'material-symbols-outlined text-brand text-lg';
      arrow.textContent = 'chevron_left';
      row.append(info, arrow);
      row.addEventListener('click', () => {
        inputEl.value = center.name;
        onSelect({ id: center.id, name: center.name });
        resultsEl.classList.add('hidden');
        emptyEl.classList.add('hidden');
      });
      resultsEl.append(row);
    });
  }

  function initSidebarState() {
    const sidebar = document.getElementById('app-sidebar');
    const pinBtn = document.getElementById('sidebar-pin-toggle');
    if (!sidebar || !pinBtn) return;
    const savedPin = localStorage.getItem(SIDEBAR_PIN_KEY);
    isPinned = savedPin !== null ? savedPin === 'true' : true;
    updateSidebarState();
    pinBtn.addEventListener('click', e => { e.stopPropagation(); toggleSidebarPin(); });
    sidebar.addEventListener('mouseenter', () => { if (!isPinned) sidebar.classList.add('sidebar-hovered'); });
    sidebar.addEventListener('mouseleave', () => { if (!isPinned) { sidebar.classList.remove('sidebar-hovered'); document.getElementById('doctor-account-menu')?.classList.add('hidden'); } });
  }
  function updateSidebarState() {
    const sidebar = document.getElementById('app-sidebar');
    const main = document.getElementById('main-content-layout');
    const top = document.getElementById('top-header');
    const pinBtn = document.getElementById('sidebar-pin-toggle');
    const pinIcon = document.getElementById('sidebar-pin-icon');
    if (!sidebar || !main || !top) return;
    sidebar.classList.remove('sidebar-hovered');
    if (isPinned) {
      sidebar.classList.remove('sidebar-collapsed'); main.style.marginRight='268px'; top.style.right='268px';
      pinBtn?.classList.add('text-[#0495F6]','bg-white/10'); if(pinBtn) pinBtn.title='طي القائمة الجانبية'; pinIcon?.classList.add('fill-1');
    } else {
      sidebar.classList.add('sidebar-collapsed'); main.style.marginRight='72px'; top.style.right='72px';
      pinBtn?.classList.remove('text-[#0495F6]','bg-white/10'); if(pinBtn) pinBtn.title='تثبيت القائمة الجانبية'; pinIcon?.classList.remove('fill-1');
    }
    if (pinIcon) pinIcon.textContent='push_pin';
  }
  function toggleSidebarPin(){ isPinned=!isPinned; localStorage.setItem(SIDEBAR_PIN_KEY,isPinned?'true':'false'); updateSidebarState(); }

  function initDoctorMenu(){
    const trigger=document.getElementById('doctor-card-trigger'), menu=document.getElementById('doctor-account-menu');
    if(!trigger||!menu)return;
    trigger.addEventListener('click',e=>{e.stopPropagation();menu.classList.toggle('hidden');});
    document.addEventListener('click',e=>{if(!menu.contains(e.target)&&!trigger.contains(e.target))menu.classList.add('hidden');});
  }
  function initQuickAdd(){
    const container=document.getElementById('quick-add-container'),btn=document.getElementById('quick-add-btn'),dropdown=document.getElementById('quick-add-dropdown'),arrow=document.getElementById('quick-add-arrow'),radBtn=document.getElementById('quick-add-rad-order-btn');
    if(!container||!btn||!dropdown||!arrow)return; let timer=null;
    const setOpen=open=>{if(open){if(timer)clearTimeout(timer);dropdown.classList.remove('hidden');arrow.textContent='expand_less';}else{dropdown.classList.add('hidden');arrow.textContent='expand_more';}};
    btn.addEventListener('click',e=>{e.stopPropagation();setOpen(dropdown.classList.contains('hidden'));});
    container.addEventListener('mouseenter',()=>setOpen(true)); container.addEventListener('mouseleave',()=>{timer=setTimeout(()=>setOpen(false),200);});
    document.addEventListener('click',e=>{if(!container.contains(e.target))setOpen(false);});
    radBtn?.addEventListener('click',()=>{setOpen(false);openCreateDrawer();});
  }

  function initStorage(){
    const version=localStorage.getItem(VERSION_KEY), data=localStorage.getItem(STORAGE_KEY);
    if(version!==CURRENT_STORAGE_VERSION){radOrdersList=[];saveToStorage();localStorage.setItem(VERSION_KEY,CURRENT_STORAGE_VERSION);return;}
    if(!data){radOrdersList=[];return;}
    try{const parsed=JSON.parse(data);radOrdersList=Array.isArray(parsed)?parsed.map(normalizeRadiologyOrder).filter(Boolean):[];}catch{radOrdersList=[];}
  }
  function saveToStorage(){radOrdersList=radOrdersList.map(normalizeRadiologyOrder).filter(Boolean);localStorage.setItem(STORAGE_KEY,JSON.stringify(radOrdersList));}

  function getWorkflowLabel(s){return ({draft:'مسودة',documented:'موثق',route_ready:'جاهز للتوجيه',route_requested:'طلب توجيه',cancelled:'ملغى'})[s]||'مسودة';}
  function getResultLabel(s){return ({none:'لا توجد نتيجة',in_progress:'قيد التنفيذ',result_ready:'النتيجة جاهزة'})[s]||'لا توجد نتيجة';}
  function getReportLabel(s){return ({unavailable:'غير متاح',published:'منشور من جهة التنفيذ',approved:'معتمد من جهة التنفيذ'})[s]||'غير متاح';}
  function getReviewLabel(s){return ({unavailable:'غير متاح',pending_review:'بانتظار المراجعة',reviewed:'راجع الطبيب'})[s]||'غير متاح';}
  function getProviderDisplayName(o){return o.destinationMode==='unassigned'?'غير محددة':(o.radiologyCenterName||'—');}
  function getImagingTypeLabel(k){return IMAGING_TYPES[k]||'—';}

  function workflowBadge(state){
    const el=document.createElement('span'); el.className='px-2 py-0.5 rounded-md font-bold text-[10px] border '; el.textContent=getWorkflowLabel(state);
    if(state==='draft')el.className+='bg-amber-50 text-amber-700 border-amber-200';
    else if(state==='documented')el.className+='bg-blue-50 text-blue-700 border-blue-200';
    else if(state==='route_ready')el.className+='bg-purple-50 text-purple-700 border-purple-200';
    else if(state==='route_requested')el.className+='bg-sky-50 text-sky-700 border-sky-200';
    else el.className+='bg-slate-100 text-slate-600 border-slate-200'; return el;
  }
  function resultBadge(state){
    const el=document.createElement('span'); el.className='px-2 py-0.5 rounded-md font-medium text-[10px] border '; el.textContent=getResultLabel(state);
    if(state==='result_ready')el.className+='bg-emerald-50 text-emerald-700 border-emerald-200';
    else if(state==='in_progress')el.className+='bg-sky-50 text-sky-700 border-sky-200';
    else el.className+='bg-slate-100 text-slate-500 border-slate-200'; return el;
  }

  function renderOrders(){
    const tbody=document.getElementById('rad-orders-table-body');
    const mobile=document.getElementById('rad-mobile-orders-container');
    const initial=document.getElementById('rad-initial-empty-state');
    const filteredEmpty=document.getElementById('rad-filter-empty-state');
    tbody.replaceChildren();
    mobile.replaceChildren();
    updateCounts();

    if(radOrdersList.length===0){
      tbody.appendChild(createEmptyTableRow('لا توجد طلبات أشعة','لم يتم إنشاء أي طلب أشعة حتى الآن.','طلب أشعة جديد',()=>openCreateDrawer()));
      initial.classList.remove('hidden');
      filteredEmpty.classList.add('hidden');
      mobile.classList.add('hidden');
      document.getElementById('rad-filter-summary').textContent='لا توجد طلبات أشعة حتى الآن';
      return;
    }

    initial.classList.add('hidden');
    const q=document.getElementById('rad-filter-search').value.trim().toLowerCase(), status=document.getElementById('rad-filter-order-status').value, priority=document.getElementById('rad-filter-priority').value, provider=document.getElementById('rad-filter-provider').value, result=document.getElementById('rad-filter-result-status').value, sort=document.getElementById('rad-filter-sort').value;
    let list=radOrdersList.filter(o=>{
      if(currentFilterTab!=='all'&&o.workflowState!==currentFilterTab)return false;
      if(q&&!([o.patientName,o.patientId,o.orderNumber,o.bodyArea,getImagingTypeLabel(o.imagingType)].some(v=>safeString(v).toLowerCase().includes(q))))return false;
      if(status&&o.workflowState!==status)return false; if(priority&&o.priority!==priority)return false; if(provider&&o.destinationMode!==provider)return false; if(result&&o.resultState!==result)return false; return true;
    });
    list.sort((a,b)=>{if(sort==='oldest')return (a.isoDate||a.id).localeCompare(b.isoDate||b.id);if(sort==='urgent'){if(a.priority==='urgent'&&b.priority!=='urgent')return -1;if(b.priority==='urgent'&&a.priority!=='urgent')return 1;}return (b.isoDate||b.id).localeCompare(a.isoDate||a.id);});
    if(list.length===0){
      tbody.appendChild(createEmptyTableRow('لا توجد طلبات مطابقة','لا توجد طلبات أشعة مطابقة للفلاتر الحالية.'));
      filteredEmpty.classList.remove('hidden');
      mobile.classList.add('hidden');
      document.getElementById('rad-filter-summary').textContent=`0 من ${radOrdersList.length} طلب`;
      return;
    }
    filteredEmpty.classList.add('hidden');
    mobile.classList.remove('hidden');
    list.forEach(o=>{tbody.appendChild(createDesktopRow(o));mobile.appendChild(createMobileCard(o));});
    document.getElementById('rad-filter-summary').textContent=list.length===radOrdersList.length?'يعرض كافة الطلبات الحالية':`يعرض ${list.length} من ${radOrdersList.length} طلب`;
  }

  function createDesktopRow(o){
    const tr=document.createElement('tr');tr.className='hover:bg-slate-50/80 transition-colors';
    const tdPatient=document.createElement('td');tdPatient.className='py-3.5 px-4';const pw=document.createElement('div');pw.className='flex items-center gap-2.5';const av=document.createElement('div');av.className='w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0';av.textContent=o.patientInitials;const pi=document.createElement('div');const pn=document.createElement('div');pn.className='font-bold text-slate-900';pn.textContent=o.patientName;const pm=document.createElement('div');pm.className='flex items-center gap-1.5 text-[11px] text-slate-400 font-numeric-ui';const pid=document.createElement('span');pid.className='bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px]';pid.textContent=o.patientId;const dot=document.createElement('span');dot.textContent='•';const meta=document.createElement('span');meta.textContent=getPatientMeta(o);pm.append(pid,dot,meta);pi.append(pn,pm);pw.append(av,pi);tdPatient.append(pw);tr.append(tdPatient);
    const tdOrder=document.createElement('td');tdOrder.className='py-3.5 px-4';const num=document.createElement('button');num.type='button';num.className='font-bold font-numeric-ui text-brand text-xs hover:underline block';num.textContent=o.orderNumber;num.addEventListener('click',()=>openDetailsDrawer(o.id));const type=document.createElement('div');type.className='text-slate-700 font-medium mt-0.5';type.textContent=getImagingTypeLabel(o.imagingType);tdOrder.append(num,type);tr.append(tdOrder);
    const area=document.createElement('td');area.className='py-3.5 px-4 text-slate-700 font-medium max-w-[150px] truncate';area.textContent=o.bodyArea||'—';tr.append(area);
    const tdDate=document.createElement('td');tdDate.className='py-3.5 px-4';const date=document.createElement('div');date.className='text-slate-700 font-numeric-ui text-[11px]';date.textContent=o.orderDate||'—';const visit=document.createElement('div');visit.className='text-slate-400 mt-0.5 max-w-[150px] truncate';visit.textContent=o.visitTitle?o.visitTitle.split('—')[0].trim():'—';tdDate.append(date,visit);tr.append(tdDate);
    const pri=document.createElement('td');pri.className='py-3.5 px-4';const p=document.createElement('span');p.className=o.priority==='urgent'?'px-2 py-0.5 rounded-md font-bold text-[10px] bg-red-50 text-red-700 border border-red-200':'px-2 py-0.5 rounded-md font-medium text-[10px] bg-slate-100 text-slate-600';p.textContent=o.priority==='urgent'?'عاجل':'عادي';pri.append(p);tr.append(pri);
    const st=document.createElement('td');st.className='py-3.5 px-4';st.append(workflowBadge(o.workflowState));tr.append(st);
    const prov=document.createElement('td');prov.className='py-3.5 px-4 text-slate-700 font-medium max-w-[150px] truncate';prov.textContent=getProviderDisplayName(o);tr.append(prov);
    const res=document.createElement('td');res.className='py-3.5 px-4';const rw=document.createElement('div');rw.className='flex flex-col items-start gap-1.5';rw.append(resultBadge(o.resultState));const rep=document.createElement('span');rep.className=o.providerReportState==='approved'?'text-[11px] font-bold text-emerald-700':o.providerReportState==='published'?'text-[11px] font-medium text-sky-700':'text-[11px] text-slate-400';rep.textContent=getReportLabel(o.providerReportState);rw.append(rep);res.append(rw);tr.append(res);
    const rev=document.createElement('td');rev.className='py-3.5 px-4';const rv=document.createElement('span');rv.className=o.doctorReviewState==='reviewed'?'text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded':o.doctorReviewState==='pending_review'?'text-[11px] font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded':'text-[11px] text-slate-400';rv.textContent=getReviewLabel(o.doctorReviewState);rev.append(rv);tr.append(rev);
    const act=document.createElement('td');act.className='py-3.5 px-4 text-center';const btn=document.createElement('button');btn.type='button';btn.className='w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center mx-auto';const icon=document.createElement('span');icon.className='material-symbols-outlined text-lg';icon.textContent='more_vert';btn.append(icon);btn.addEventListener('click',e=>{e.stopPropagation();openContextMenu(e,o.id);});act.append(btn);tr.append(act);return tr;
  }

  function createMobileCard(o){
    const card=document.createElement('div');card.className='bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3';const top=document.createElement('div');top.className='flex items-center justify-between';const left=document.createElement('div');left.className='flex items-center gap-2';const num=document.createElement('button');num.type='button';num.className='font-bold font-numeric-ui text-brand text-xs hover:underline';num.textContent=o.orderNumber;num.addEventListener('click',()=>openDetailsDrawer(o.id));const pri=document.createElement('span');pri.className=o.priority==='urgent'?'px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700':'px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600';pri.textContent=o.priority==='urgent'?'عاجل':'عادي';left.append(num,pri);const more=document.createElement('button');more.className='w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-100 flex items-center justify-center';more.type='button';const mi=document.createElement('span');mi.className='material-symbols-outlined text-lg';mi.textContent='more_vert';more.append(mi);more.addEventListener('click',e=>{e.stopPropagation();openContextMenu(e,o.id);});top.append(left,more);
    const patient=document.createElement('div');patient.className='flex items-center gap-2.5';const av=document.createElement('div');av.className='w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs';av.textContent=o.patientInitials;const pd=document.createElement('div');const name=document.createElement('div');name.className='font-bold text-slate-900 text-xs';name.textContent=o.patientName;const meta=document.createElement('div');meta.className='text-[11px] text-slate-400';meta.textContent=`${o.patientId} • ${getPatientMeta(o)}`;pd.append(name,meta);patient.append(av,pd);
    const info=document.createElement('div');info.className='bg-slate-50 rounded-xl p-3';const t=document.createElement('div');t.className='font-bold text-slate-800 text-xs';t.textContent=`${getImagingTypeLabel(o.imagingType)} — ${o.bodyArea||'—'}`;const d=document.createElement('div');d.className='text-[11px] text-slate-400 mt-1';d.textContent=o.orderDate||'—';info.append(t,d);
    const foot=document.createElement('div');foot.className='flex items-center justify-between pt-1';const badges=document.createElement('div');badges.className='flex items-center gap-1.5';badges.append(workflowBadge(o.workflowState),resultBadge(o.resultState));const rv=document.createElement('span');rv.className='text-[11px] text-slate-500';rv.textContent=getReviewLabel(o.doctorReviewState);foot.append(badges,rv);card.append(top,patient,info,foot);return card;
  }

  function updateCounts(){
    const set=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=v;};
    set('rad-tab-count-all',radOrdersList.length); set('rad-tab-count-draft',radOrdersList.filter(o=>o.workflowState==='draft').length); set('rad-tab-count-documented',radOrdersList.filter(o=>o.workflowState==='documented').length); set('rad-tab-count-route-ready',radOrdersList.filter(o=>o.workflowState==='route_ready').length); set('rad-tab-count-route-requested',radOrdersList.filter(o=>o.workflowState==='route_requested').length);
  }

  function openCreateDrawer(editId=null){
    const drawer=document.getElementById('drawer-create-rad-order'),form=document.getElementById('rad-order-form');form.reset();selectedPatient=null;selectedTheraRadiologyCenter=null;document.getElementById('rad-form-order-id').value='';document.getElementById('rad-patient-search-box').classList.remove('hidden');document.getElementById('rad-selected-patient-card').classList.add('hidden');document.getElementById('rad-thera-network-options').classList.add('hidden');document.getElementById('rad-external-options').classList.add('hidden');document.getElementById('rad-network-empty-state').classList.add('hidden');document.getElementById('rad-network-results').classList.add('hidden');
    if(editId){const o=radOrdersList.find(x=>x.id===editId);if(o){document.getElementById('drawer-create-rad-title').textContent=`تعديل طلب الأشعة (${o.orderNumber})`;document.getElementById('rad-form-order-id').value=o.id;setChosenPatient({id:o.patientId,name:o.patientName,initials:o.patientInitials,age:o.patientAge,gender:o.patientGender});document.getElementById('rad-form-visit').value=Object.entries(VISITS).find(([,label])=>label===o.visitTitle)?.[0]||'none';document.getElementById('rad-form-imaging-type').value=o.imagingType;document.getElementById('rad-form-body-area').value=o.bodyArea;document.getElementById('rad-form-priority').value=o.priority;document.getElementById('rad-form-clinical-context').value=o.clinicalContext;document.getElementById('rad-form-clinical-question').value=o.clinicalQuestion;document.getElementById('rad-form-instructions').value=o.instructions;const radio=document.querySelector(`input[name="rad_destination_mode"][value="${o.destinationMode}"]`);if(radio){radio.checked=true;if(o.destinationMode==='thera_network'){document.getElementById('rad-thera-network-options').classList.remove('hidden');document.getElementById('rad-network-search').value=o.radiologyCenterName||'';selectedTheraRadiologyCenter=(o.radiologyCenterId&&o.radiologyCenterName)?{id:o.radiologyCenterId,name:o.radiologyCenterName}:null;}else if(o.destinationMode==='external'){document.getElementById('rad-external-options').classList.remove('hidden');document.getElementById('rad-form-external-name').value=o.radiologyCenterName||'';}}}}
    else document.getElementById('drawer-create-rad-title').textContent='طلب أشعة جديد';drawer.classList.remove('hidden');syncBodyScrollLock();
  }
  function closeCreateDrawer(){document.getElementById('drawer-create-rad-order').classList.add('hidden');syncBodyScrollLock();}
  function setChosenPatient(p){selectedPatient=p;document.getElementById('rad-patient-name-display').textContent=p.name;document.getElementById('rad-patient-avatar-badge').textContent=p.initials;document.getElementById('rad-patient-id-badge').textContent=p.id;document.getElementById('rad-patient-meta-display').textContent=`${p.age} سنة • ${p.gender}`;document.getElementById('rad-patient-suggestions').classList.add('hidden');document.getElementById('rad-patient-search-box').classList.add('hidden');document.getElementById('rad-selected-patient-card').classList.remove('hidden');}

  function handleSave(targetStatus){
    if(!selectedPatient){showToast('يرجى اختيار مريض للطلب','warning');document.getElementById('rad-patient-search-input').focus();return;}
    const imagingType=document.getElementById('rad-form-imaging-type').value,bodyArea=document.getElementById('rad-form-body-area').value.trim();
    if(targetStatus!=='draft'&&(!imagingType||!bodyArea)){showToast('حدد نوع التصوير والمنطقة المطلوبة قبل توثيق أو إرسال الطلب','warning');if(!imagingType)document.getElementById('rad-form-imaging-type').focus();else document.getElementById('rad-form-body-area').focus();return;}
    const destinationMode=document.querySelector('input[name="rad_destination_mode"]:checked')?.value||'unassigned';let radiologyCenterId='',radiologyCenterName='';
    if(destinationMode==='thera_network'){if(targetStatus==='route_requested'&&!selectedTheraRadiologyCenter){showToast('اختر مركز أشعة من شبكة THERA قبل الإرسال','warning');return;}if(selectedTheraRadiologyCenter){radiologyCenterId=selectedTheraRadiologyCenter.id;radiologyCenterName=selectedTheraRadiologyCenter.name;}}
    if(destinationMode==='external'){radiologyCenterName=document.getElementById('rad-form-external-name').value.trim();if(targetStatus==='route_requested'&&!radiologyCenterName){showToast('أدخل اسم مركز الأشعة الخارجي قبل الإرسال','warning');return;}}
    if(targetStatus==='route_requested'&&destinationMode==='unassigned'){showToast('لا يمكن إرسال الطلب بدون تحديد جهة التنفيذ','warning');return;}
    const visitTitle=VISITS[document.getElementById('rad-form-visit').value]||'',priority=document.getElementById('rad-form-priority').value,clinicalContext=document.getElementById('rad-form-clinical-context').value.trim(),clinicalQuestion=document.getElementById('rad-form-clinical-question').value.trim(),instructions=document.getElementById('rad-form-instructions').value.trim(),editId=document.getElementById('rad-form-order-id').value;
    if(editId){const i=radOrdersList.findIndex(o=>o.id===editId);if(i<0)return;const existing=radOrdersList[i];if(existing.workflowState!=='draft'){showToast('يمكن تعديل المسودات فقط','warning');return;}const updated=normalizeRadiologyOrder({...existing,patientId:selectedPatient.id,patientName:selectedPatient.name,patientInitials:selectedPatient.initials,patientAge:selectedPatient.age,patientGender:selectedPatient.gender,visitTitle,imagingType,bodyArea,clinicalContext,clinicalQuestion,priority,instructions,workflowState:targetStatus,destinationMode,radiologyCenterId,radiologyCenterName});if(updated)radOrdersList[i]=updated;}
    else{const now=new Date(),patientSuffix=selectedPatient.id.replace(/\D/g,''),uniqueSuffix=String(Date.now()).slice(-4),orderNumber=`RAD-${patientSuffix}-${uniqueSuffix}`,orderDate=now.toLocaleDateString('ar-EG-u-ca-gregory',{day:'numeric',month:'long',year:'numeric'}),isoDate=now.toISOString().slice(0,10);const order=normalizeRadiologyOrder({id:`rad_${Date.now()}`,orderNumber,patientId:selectedPatient.id,patientName:selectedPatient.name,patientInitials:selectedPatient.initials,patientAge:selectedPatient.age,patientGender:selectedPatient.gender,visitTitle,doctorName:'د. محمد أحمد',orderDate,isoDate,imagingType,bodyArea,clinicalContext,clinicalQuestion,priority,instructions,workflowState:targetStatus,destinationMode,radiologyCenterId,radiologyCenterName,resultState:'none',providerReportState:'unavailable',doctorReviewState:'unavailable'});if(order)radOrdersList.unshift(order);}
    saveToStorage();renderOrders();closeCreateDrawer();
    if(targetStatus==='draft')showToast('تم حفظ مسودة طلب الأشعة محليًا على هذا الجهاز','info');else if(targetStatus==='documented')showToast('تم حفظ طلب الأشعة محليًا بحالة موثقة','info');else showToast('تم تجهيز طلب الأشعة للإرسال إلى الجهة المحددة','info');
  }

  function openDetailsDrawer(id){
    activeDetailsOrderId=id;routingRadOrderId=id;const o=radOrdersList.find(x=>x.id===id);if(!o)return;document.getElementById('rad-details-id-title').textContent=o.orderNumber;document.getElementById('rad-details-order-date').textContent=`تاريخ الطلب: ${o.orderDate||'—'}`;const pri=document.getElementById('rad-details-priority-badge');pri.textContent=o.priority==='urgent'?'عاجل':'عادي';pri.className=o.priority==='urgent'?'px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-50 text-red-700 border border-red-200':'px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600';document.getElementById('rad-details-patient-name').textContent=o.patientName;document.getElementById('rad-details-patient-id-badge').textContent=o.patientId;document.getElementById('rad-details-patient-meta').textContent=getPatientMeta(o);document.getElementById('rad-details-doctor-name').textContent=o.doctorName||'—';
    const sp=document.getElementById('rad-details-status-pill');sp.replaceChildren(workflowBadge(o.workflowState));document.getElementById('rad-details-provider-text').textContent=getProviderDisplayName(o);const rp=document.getElementById('rad-details-result-pill');rp.replaceChildren(resultBadge(o.resultState));const report=document.getElementById('rad-details-report-pill');report.textContent=getReportLabel(o.providerReportState);report.className=o.providerReportState==='approved'?'text-[11px] font-bold text-emerald-700':o.providerReportState==='published'?'text-[11px] font-medium text-sky-700':'text-[11px] text-slate-400';const rev=document.getElementById('rad-details-review-pill');rev.textContent=getReviewLabel(o.doctorReviewState);rev.className=o.doctorReviewState==='reviewed'?'text-[11px] font-bold text-emerald-700':o.doctorReviewState==='pending_review'?'text-[11px] font-medium text-amber-700':'text-[11px] text-slate-400';document.getElementById('rad-details-imaging-type').textContent=getImagingTypeLabel(o.imagingType);document.getElementById('rad-details-body-area').textContent=o.bodyArea||'—';document.getElementById('rad-details-visit-text').textContent=o.visitTitle||'—';document.getElementById('rad-details-clinical-text').textContent=o.clinicalContext||'—';document.getElementById('rad-details-question-text').textContent=o.clinicalQuestion||'—';document.getElementById('rad-details-instructions-text').textContent=o.instructions||'—';
    const canReview=(o.resultState==='result_ready'||o.providerReportState==='published'||o.providerReportState==='approved')&&o.doctorReviewState!=='reviewed';document.getElementById('rad-doctor-review-box').classList.toggle('hidden',!canReview);const canRoute=o.workflowState==='documented'||o.workflowState==='route_ready';const box=document.getElementById('rad-routing-action-box');box.classList.toggle('hidden',!canRoute);if(canRoute){const sel=document.getElementById('rad-details-route-destination-mode');sel.value=o.destinationMode;document.getElementById('rad-details-route-network-box').classList.toggle('hidden',o.destinationMode!=='thera_network');document.getElementById('rad-details-route-external-box').classList.toggle('hidden',o.destinationMode!=='external');document.getElementById('rad-details-route-network-search').value=o.destinationMode==='thera_network'?(o.radiologyCenterName||''):'';document.getElementById('rad-details-route-external-input').value=o.destinationMode==='external'?(o.radiologyCenterName||''):'';selectedDetailsTheraRadiologyCenter=(o.destinationMode==='thera_network'&&o.radiologyCenterId&&o.radiologyCenterName)?{id:o.radiologyCenterId,name:o.radiologyCenterName}:null;}
    document.getElementById('drawer-details-rad-order').classList.remove('hidden');syncBodyScrollLock();
  }
  function closeDetailsDrawer(){document.getElementById('drawer-details-rad-order').classList.add('hidden');activeDetailsOrderId=null;routingRadOrderId=null;selectedDetailsTheraRadiologyCenter=null;syncBodyScrollLock();}

  function openContextMenu(e,id){
    const menu=document.getElementById('rad-row-menu'),o=radOrdersList.find(x=>x.id===id);if(!o)return;menu.replaceChildren();
    const item=(icon,label,fn,primary=false)=>{const b=document.createElement('button');b.type='button';b.className=`w-full min-h-10 px-3 rounded-lg flex items-center gap-2.5 text-right text-[13px] font-medium transition-colors hover:bg-slate-50 ${primary?'text-brand':'text-slate-700'}`;const i=document.createElement('span');i.className=`material-symbols-outlined text-[18px] shrink-0 ${primary?'text-brand':'text-slate-400'}`;i.textContent=icon;const t=document.createElement('span');t.textContent=label;b.append(i,t);b.addEventListener('click',ev=>{ev.stopPropagation();closeContextMenu();fn();});return b;};
    menu.append(item('visibility','عرض تفاصيل الطلب',()=>openDetailsDrawer(o.id)));
    if(o.workflowState==='draft')menu.append(item('edit','تعديل المسودة',()=>openCreateDrawer(o.id)));
    menu.append(item('stethoscope','فتح الزيارة المرتبطة',()=>showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل','info')));
    if(o.workflowState==='documented'||o.workflowState==='route_ready'){const div=document.createElement('div');div.className='my-1 border-t border-slate-100';menu.append(div,item('near_me','تحديد جهة التنفيذ',()=>{routingRadOrderId=o.id;openDetailsDrawer(o.id);document.getElementById('rad-routing-action-box')?.scrollIntoView({behavior:'smooth',block:'center'});},true));}
    positionContextMenu(menu,e.currentTarget);
  }
  function positionContextMenu(menu,trigger){const rect=trigger.getBoundingClientRect(),w=220,p=8,g=6;menu.classList.remove('hidden');const h=menu.offsetHeight||160;let top=rect.bottom+g;if(top+h>window.innerHeight-p)top=rect.top-h-g;top=Math.max(p,Math.min(top,window.innerHeight-h-p));let left=rect.right-w;left=Math.max(p,Math.min(left,window.innerWidth-w-p));menu.style.position='fixed';menu.style.top=`${Math.round(top)}px`;menu.style.left=`${Math.round(left)}px`;}
  function closeContextMenu(){const menu=document.getElementById('rad-row-menu');if(menu){menu.classList.add('hidden');menu.replaceChildren();}}

  function syncBodyScrollLock(){const c=!document.getElementById('drawer-create-rad-order').classList.contains('hidden'),d=!document.getElementById('drawer-details-rad-order').classList.contains('hidden');document.body.classList.toggle('overflow-hidden',c||d);}
  function showToast(message,type='info'){const container=document.getElementById('rad-toast-container');const toast=document.createElement('div');const semantic=type==='warning'?'border-amber-200 bg-amber-50 text-amber-800':type==='success'?'border-emerald-200 bg-emerald-50 text-emerald-800':'border-blue-200 bg-blue-50 text-blue-800';toast.className=`pointer-events-auto min-w-[280px] max-w-sm px-4 py-3 rounded-xl border shadow-lg text-xs font-medium ${semantic}`;toast.textContent=message;container.append(toast);setTimeout(()=>toast.remove(),3200);}

  function resetFilters(){document.getElementById('rad-filter-search').value='';document.getElementById('rad-filter-order-status').value='';document.getElementById('rad-filter-priority').value='';document.getElementById('rad-filter-provider').value='';document.getElementById('rad-filter-result-status').value='';document.getElementById('rad-filter-sort').value='newest';currentFilterTab='all';syncTabVisuals();renderOrders();}
  function syncTabVisuals(){document.querySelectorAll('.rad-tab-btn').forEach(btn=>{const active=btn.dataset.tab===currentFilterTab;btn.className=`rad-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 ${active?'bg-[#0495F6] text-white shadow-sm':'bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]'}`;const c=btn.querySelector('.tab-count');if(c)c.className=`tab-count px-2 py-0.5 rounded-full text-xs font-numeric-ui ${active?'bg-white/20 text-white':'bg-[#DDE8FB] text-[#3F4752]'}`;});}

  function initEvents(){
    document.getElementById('open-create-rad-order-btn').addEventListener('click',()=>openCreateDrawer());document.getElementById('rad-empty-create-btn').addEventListener('click',()=>openCreateDrawer());document.getElementById('drawer-create-rad-close').addEventListener('click',closeCreateDrawer);document.getElementById('drawer-create-rad-cancel').addEventListener('click',closeCreateDrawer);document.getElementById('drawer-create-rad-backdrop').addEventListener('click',closeCreateDrawer);document.getElementById('drawer-details-rad-close').addEventListener('click',closeDetailsDrawer);document.getElementById('drawer-details-rad-cancel').addEventListener('click',closeDetailsDrawer);document.getElementById('drawer-details-rad-backdrop').addEventListener('click',closeDetailsDrawer);
    document.querySelectorAll('input[name="rad_destination_mode"]').forEach(r=>r.addEventListener('change',e=>{
      const v=e.target.value;
      document.getElementById('rad-thera-network-options').classList.toggle('hidden',v!=='thera_network');
      document.getElementById('rad-external-options').classList.toggle('hidden',v!=='external');
      if(v==='thera_network'){const input=document.getElementById('rad-network-search');renderRadiologyCenterResults(input,document.getElementById('rad-network-results'),document.getElementById('rad-network-empty-state'),center=>{selectedTheraRadiologyCenter=center;});}
    }));
    const net=document.getElementById('rad-network-search');
    const netResults=document.getElementById('rad-network-results');
    const netEmpty=document.getElementById('rad-network-empty-state');
    const refreshCreateCenters=()=>{selectedTheraRadiologyCenter=null;renderRadiologyCenterResults(net,netResults,netEmpty,center=>{selectedTheraRadiologyCenter=center;});};
    net.addEventListener('input',refreshCreateCenters);
    net.addEventListener('focus',()=>renderRadiologyCenterResults(net,netResults,netEmpty,center=>{selectedTheraRadiologyCenter=center;}));
    const pInput=document.getElementById('rad-patient-search-input'),suggest=document.getElementById('rad-patient-suggestions');pInput.addEventListener('input',e=>{const q=e.target.value.trim().toLowerCase();suggest.replaceChildren();if(!q){suggest.classList.add('hidden');return;}const matches=DEMO_PATIENTS.filter(p=>p.name.toLowerCase().includes(q)||p.id.toLowerCase().includes(q));if(!matches.length){const n=document.createElement('div');n.className='px-3 py-2 text-slate-400 text-xs';n.textContent='لا توجد نتائج مطابقة';suggest.append(n);}else matches.forEach(p=>{const row=document.createElement('button');row.type='button';row.className='w-full px-3 py-2 hover:bg-slate-50 flex justify-between items-center text-xs';const n=document.createElement('span');n.className='font-bold text-slate-800';n.textContent=p.name;const id=document.createElement('span');id.className='text-slate-400 font-numeric-ui';id.textContent=p.id;row.append(n,id);row.addEventListener('click',()=>setChosenPatient(p));suggest.append(row);});suggest.classList.remove('hidden');});
    document.getElementById('rad-change-patient-btn').addEventListener('click',()=>{selectedPatient=null;document.getElementById('rad-selected-patient-card').classList.add('hidden');document.getElementById('rad-patient-search-box').classList.remove('hidden');pInput.value='';pInput.focus();});
    document.querySelectorAll('.rad-tab-btn').forEach(btn=>btn.addEventListener('click',()=>{currentFilterTab=btn.dataset.tab;syncTabVisuals();renderOrders();}));
    ['rad-filter-search','rad-filter-order-status','rad-filter-priority','rad-filter-provider','rad-filter-result-status','rad-filter-sort'].forEach(id=>{const el=document.getElementById(id);el.addEventListener(id==='rad-filter-search'?'input':'change',renderOrders);});document.getElementById('rad-reset-filters-btn').addEventListener('click',resetFilters);document.getElementById('rad-filter-empty-reset-btn').addEventListener('click',resetFilters);
    document.getElementById('rad-save-as-draft-btn').addEventListener('click',()=>handleSave('draft'));document.getElementById('rad-save-as-documented-btn').addEventListener('click',()=>handleSave('documented'));document.getElementById('rad-save-and-send-btn').addEventListener('click',()=>handleSave('route_requested'));
    const routeMode=document.getElementById('rad-details-route-destination-mode');
    routeMode.addEventListener('change',e=>{
      const v=e.target.value;
      document.getElementById('rad-details-route-network-box').classList.toggle('hidden',v!=='thera_network');
      document.getElementById('rad-details-route-external-box').classList.toggle('hidden',v!=='external');
      if(v==='thera_network'){const input=document.getElementById('rad-details-route-network-search');renderRadiologyCenterResults(input,document.getElementById('rad-details-route-network-results'),document.getElementById('rad-details-route-empty-state'),center=>{selectedDetailsTheraRadiologyCenter=center;});}
    });
    const detailNet=document.getElementById('rad-details-route-network-search');
    const detailResults=document.getElementById('rad-details-route-network-results');
    const detailEmpty=document.getElementById('rad-details-route-empty-state');
    const refreshDetailCenters=()=>{selectedDetailsTheraRadiologyCenter=null;renderRadiologyCenterResults(detailNet,detailResults,detailEmpty,center=>{selectedDetailsTheraRadiologyCenter=center;});};
    detailNet.addEventListener('input',refreshDetailCenters);
    detailNet.addEventListener('focus',()=>renderRadiologyCenterResults(detailNet,detailResults,detailEmpty,center=>{selectedDetailsTheraRadiologyCenter=center;}));
    document.getElementById('rad-confirm-routing-btn').addEventListener('click',()=>{const id=routingRadOrderId||activeDetailsOrderId,o=radOrdersList.find(x=>x.id===id);if(!o)return;const mode=routeMode.value;if(mode==='unassigned'){o.destinationMode='unassigned';o.radiologyCenterId='';o.radiologyCenterName='';saveToStorage();renderOrders();openDetailsDrawer(o.id);return;}if(mode==='thera_network'){if(!selectedDetailsTheraRadiologyCenter){showToast('اختر مركز أشعة من شبكة THERA قبل الإرسال','warning');return;}o.destinationMode='thera_network';o.radiologyCenterId=selectedDetailsTheraRadiologyCenter.id;o.radiologyCenterName=selectedDetailsTheraRadiologyCenter.name;}else{const name=document.getElementById('rad-details-route-external-input').value.trim();if(!name){showToast('أدخل اسم مركز الأشعة الخارجي قبل الإرسال','warning');return;}o.destinationMode='external';o.radiologyCenterId='';o.radiologyCenterName=name;}o.workflowState='route_requested';saveToStorage();renderOrders();openDetailsDrawer(o.id);showToast('تم تجهيز طلب الأشعة للإرسال إلى الجهة المحددة','info');});
    document.getElementById('rad-perform-doctor-review-btn').addEventListener('click',()=>{const o=radOrdersList.find(x=>x.id===activeDetailsOrderId);if(!o)return;o.doctorReviewState='reviewed';saveToStorage();renderOrders();openDetailsDrawer(o.id);showToast('تم تسجيل مراجعة الطبيب محليًا','info');});document.getElementById('rad-print-order-btn').addEventListener('click',()=>window.print());
    document.addEventListener('click',e=>{const menu=document.getElementById('rad-row-menu');if(menu&&!menu.contains(e.target))closeContextMenu();});window.addEventListener('resize',closeContextMenu);window.addEventListener('scroll',closeContextMenu,true);document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeContextMenu();if(!document.getElementById('drawer-create-rad-order').classList.contains('hidden'))closeCreateDrawer();if(!document.getElementById('drawer-details-rad-order').classList.contains('hidden'))closeDetailsDrawer();}});
  }
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
    const parent=select.parentElement;
    const wrapper=document.createElement('div'); wrapper.className='thera-custom-select';
    parent.insertBefore(wrapper,select); wrapper.appendChild(select); select.classList.add('thera-native-select-hidden');
    const trigger=document.createElement('button'); trigger.type='button'; trigger.className='thera-custom-select-trigger'; trigger.setAttribute('aria-haspopup','listbox');
    trigger.innerHTML='<span class="thera-custom-select-label"></span><span class="material-symbols-outlined thera-custom-select-arrow">expand_more</span>';
    const menu=document.createElement('div'); menu.className='thera-custom-select-menu hidden'; menu.setAttribute('role','listbox');
    wrapper.append(trigger,menu);
    function refresh(){
      const selected=select.options[select.selectedIndex]||select.options[0];
      wrapper.querySelector('.thera-custom-select-label').textContent=selected?selected.textContent.trim():'';
      menu.replaceChildren();
      Array.from(select.options).forEach(opt=>{
        const b=document.createElement('button'); b.type='button'; b.className='thera-custom-select-option';
        if(opt.value===select.value)b.classList.add('active');
        const t=document.createElement('span');t.textContent=opt.textContent.trim();
        const c=document.createElement('span');c.className='material-symbols-outlined check';c.textContent='check';
        b.append(t,c);
        b.addEventListener('click',e=>{e.stopPropagation();if(select.value!==opt.value){select.value=opt.value;select.dispatchEvent(new Event('input',{bubbles:true}));select.dispatchEvent(new Event('change',{bubbles:true}));}refresh();wrapper.classList.remove('open');menu.classList.add('hidden');});
        menu.appendChild(b);
      });
    }
    trigger.addEventListener('click',e=>{e.stopPropagation();const open=menu.classList.contains('hidden');closeAll(open?wrapper:null);wrapper.classList.toggle('open',open);menu.classList.toggle('hidden',!open);});
    select.addEventListener('change',refresh); registry.set(select,{refresh}); refresh();
  }
  window.refreshTheraSelects=()=>registry.forEach(v=>v.refresh());
  document.querySelectorAll('select').forEach(build);
  document.addEventListener('click',()=>closeAll());
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAll();});
  document.querySelectorAll('form').forEach(f=>f.addEventListener('reset',()=>setTimeout(window.refreshTheraSelects,0)));
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>