<!DOCTYPE html>
<html dir="rtl" lang="ar" style=""><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>طلبات المختبر - THERA</title>
<!-- Google Fonts: Tajawal & Inter -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;700;800&amp;family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&amp;display=swap" rel="stylesheet"/>
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
    .font-numeric-uieric-ui {
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
.thera-custom-select { position: relative; width: 100%; }
.thera-custom-select.compact { width: auto; min-width: 150px; }
.thera-custom-select-trigger {
  width: 100%; height: 40px; padding: 0 12px; background: #fff;
  border: 1px solid #E2E8F0; border-radius: 8px; color: #334155;
  font-size: 12px; font-weight: 600; display: flex; align-items: center;
  justify-content: space-between; gap: 10px; text-align: right;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.thera-custom-select.compact .thera-custom-select-trigger { height: 32px; padding: 0 10px; min-width: 150px; }
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
}
.thera-custom-select.compact .thera-custom-select-menu { min-width:190px; left:auto; }
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
#reset-filters-btn { width:32px !important; height:32px !important; padding:0 !important; justify-content:center !important; border:1px solid #E2E8F0; background:#fff; border-radius:8px !important; }
#reset-filters-btn:hover { background:#F8FAFC !important; border-color:#CBD5E1; }
#reset-filters-btn .reset-label { display:none !important; }
</style><style>
/* Compact one-line Lab Orders toolbar: keeps all filters in-row without widening the page. */
#filter-search{min-width:0;}
#filter-order-status,#filter-priority,#filter-provider,#filter-result-status,#filter-sort{min-width:0;width:100%;padding-inline-start:.65rem;padding-inline-end:2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
@media (max-width: 1180px){
  #filter-search{font-size:11px;}
}
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
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
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
<main class="pt-16 p-6 min-h-screen bg-[#F6F9FC] transition-all duration-300" data-purpose="workspace-content" id="main-content-layout" style="margin-right:268px;">
<!-- Top Breadcrumb & Actions Header -->
<section class="mt-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
<div>
<nav class="flex items-center gap-2 text-xs text-slate-400 mb-1 font-medium">
<span class="">العمل الطبي</span>
<span class="material-symbols-outlined text-xs">chevron_left</span>
<span class="text-brand font-bold">طلبات المختبر</span>
</nav>
<div class="flex items-center gap-3">
<h1 class="text-2xl font-bold text-slate-900 tracking-tight">طلبات المختبر</h1>
</div>
<p class="text-xs text-slate-500 mt-1">إدارة ومتابعة طلبات المختبر المرتبطة بزيارات المرضى في العيادة</p>
</div>
<div class="flex items-center gap-3">
<button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm thera-major-btn" id="open-create-order-btn" type="button">
<span class="material-symbols-outlined text-xl">add</span>
<span class="">طلب مختبر جديد</span>
</button>
</div>
</section>
<!-- Status Filter Tabs -->
<div class="flex items-center gap-2 overflow-x-auto pb-1 mb-6" id="order-tabs">
  <button class="tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm" data-tab="all" type="button">
    <span>الكل</span>
    <span class="tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-numeric-uieric-ui" id="tab-count-all">0</span>
  </button>
  <button class="tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="draft" type="button">
    <span>مسودات</span>
    <span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-uieric-ui" id="tab-count-draft">0</span>
  </button>
  <button class="tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="documented" type="button">
    <span>موثقة</span>
    <span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-uieric-ui" id="tab-count-documented">0</span>
  </button>
  <button class="tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="route_ready" type="button">
    <span>جاهزة للتوجيه</span>
    <span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-uieric-ui" id="tab-count-route-ready">0</span>
  </button>
  <button class="tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="route_requested" type="button">
    <span>طلب توجيه</span>
    <span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-numeric-uieric-ui" id="tab-count-route-requested">0</span>
  </button>
</div>

<!-- Toolbar Filters Card -->
<section class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-soft overflow-visible">
<div class="grid grid-cols-[minmax(180px,0.85fr)_minmax(112px,0.62fr)_minmax(100px,0.55fr)_minmax(120px,0.68fr)_minmax(118px,0.66fr)_minmax(135px,0.72fr)_40px] items-center gap-2 w-full min-w-0">
<!-- Search -->
<div class="relative min-w-0">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
<input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand focus:bg-white transition-all" id="filter-search" placeholder="ابحث باسم المريض أو رقم المريض أو رقم الطلب أو الفحص..." type="text"/>
</div>
<!-- Filter 1: Order Status -->
<div class="min-w-0">
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-1 focus:ring-brand focus:border-brand" id="filter-order-status">
<option value="">حالة الطلب: الكل</option>
<option value="draft">مسودة</option>
<option value="documented">موثق</option>
<option value="route_ready">جاهز للتوجيه</option>
<option value="route_requested">طلب توجيه</option>
<option value="cancelled">ملغى</option>
</select>
</div>
<!-- Filter 2: Priority -->
<div class="min-w-0">
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-1 focus:ring-brand focus:border-brand" id="filter-priority">
<option value="">الأولوية: الكل</option>
<option value="normal">عادي</option>
<option value="urgent">عاجل</option>
</select>
</div>
<!-- Filter 3: Execution Provider -->
<div class="min-w-0">
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-1 focus:ring-brand focus:border-brand" id="filter-provider">
<option value="">جهة التنفيذ: الكل</option>
<option value="unassigned">غير محددة</option>
<option value="thera_network">شبكة THERA</option>
<option value="external">مختبر خارجي</option>
</select>
</div>
<!-- Filter 4: Result Status -->
<div class="min-w-0">
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-1 focus:ring-brand focus:border-brand" id="filter-result-status">
<option value="">حالة النتيجة: الكل</option>
<option value="none">لا توجد نتيجة</option>
<option value="in_progress">قيد التنفيذ</option>
<option value="result_ready">النتيجة جاهزة</option>
</select>
</div>
<!-- Sort -->
<div class="flex items-center gap-1.5 min-w-0">

<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-1 focus:ring-brand focus:border-brand" id="filter-sort">
<option value="newest">الترتيب: الأحدث أولاً</option>
<option value="oldest">الترتيب: الأقدم أولاً</option>
<option value="urgent">الترتيب: الأولوية العاجلة أولاً</option>
</select>
</div>
<!-- Reset -->
<button aria-label="إعادة ضبط الفلاتر" class="h-10 w-10 text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold flex items-center justify-center transition-colors shrink-0" id="reset-filters-btn" title="إعادة ضبط الفلاتر" type="button"><span class="material-symbols-outlined text-[18px]">restart_alt</span></button>
</div>
</section>
<!-- Data Table & Mobile Cards Container -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-soft overflow-hidden">
<!-- Desktop Table View -->
<div class="overflow-x-auto hidden md:block">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold">
<th class="py-3.5 px-4">المريض</th>
<th class="py-3.5 px-4">الطلب</th>
<th class="py-3.5 px-4">تاريخ الطلب</th>
<th class="py-3.5 px-4">الزيارة</th>
<th class="py-3.5 px-4">الأولوية</th>
<th class="py-3.5 px-4">حالة الطلب</th>
<th class="py-3.5 px-4">جهة التنفيذ</th>
<th class="py-3.5 px-4">حالة النتيجة</th>
<th class="py-3.5 px-4">حالة التقرير</th>
<th class="py-3.5 px-4">مراجعة الطبيب</th>
<th class="py-3.5 px-4 text-center">إجراءات</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs" id="orders-table-body">
<!-- Dynamically populated via JS safe methods -->
</tbody>
</table>
</div>
<!-- Mobile Cards View -->
<div class="md:hidden p-4 space-y-3" id="mobile-orders-container">
<!-- Mobile cards populated via renderMobileCards() -->
</div>
<!-- State 1: Completely Empty State (0 orders in system) -->
<div class="py-16 px-4 text-center flex flex-col items-center justify-center" id="initial-empty-state">
<div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand flex items-center justify-center mb-4">
<span class="material-symbols-outlined text-3xl">science</span>
</div>
<h3 class="text-base font-bold text-slate-800 mb-1">لا توجد طلبات مختبر ظاهرة ضمن البيانات الحالية</h3>
<p class="text-xs text-slate-500 max-w-md mb-6 leading-relaxed">يمكن إنشاء طلب مختبر جديد أو تعديل عوامل التصفية.</p>
<button class="h-10 px-5 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm thera-major-btn" id="empty-state-create-btn" type="button">
<span class="material-symbols-outlined text-lg">add</span>
<span class="">طلب مختبر جديد</span>
</button>
</div>
<!-- State 2: Filter Empty State (Records exist, but filters match zero) -->
<div class="py-14 px-4 text-center flex flex-col items-center justify-center hidden" id="filter-empty-state">
<div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-2xl">filter_alt_off</span>
</div>
<h3 class="text-sm font-bold text-slate-700 mb-1">لا توجد طلبات مختبر مطابقة للفلاتر الحالية</h3>
<p class="text-xs text-slate-400 max-w-sm mb-4">يرجى مراجعة كلمات البحث أو إعادة ضبط خيارات التصفية لعرض النتائج.</p>
<button class="h-8 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors" id="filter-empty-reset-btn" type="button">
        إعادة ضبط الفلاتر
      </button>
</div>
</div>
</main>
<!-- END: Main Content Layout -->
<!-- BEGIN: Drawers & Modals -->
<!-- Drawer 1: Create / Edit Lab Order -->
<aside aria-labelledby="drawer-create-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" data-purpose="create-lab-order-drawer" id="drawer-create-order" role="dialog">
<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" id="drawer-create-backdrop"></div>
<div class="fixed inset-y-0 left-0 max-w-full flex pl-0">
<div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
<!-- Drawer Header -->
<div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center">
<span class="material-symbols-outlined text-xl">science</span>
</div>
<div>
<h2 class="text-base font-bold text-slate-900" id="drawer-create-title">طلب مختبر جديد</h2>
<p class="text-xs text-slate-500">إنشاء وتوثيق طلب فحص مخبري للمريض</p>
</div>
</div>
<button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center thera-icon-btn" id="drawer-create-close" type="button">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<!-- Drawer Body Form -->
<form class="flex-1 overflow-y-auto p-6 space-y-5 text-xs" id="lab-order-form" onsubmit="return false;">
<input id="form-order-id" type="hidden" value=""/>
<!-- Patient Selection -->
<div class="space-y-2">
<label class="block font-bold text-slate-700">المريض *</label>
<div class="relative" id="patient-search-box">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="patient-search-input" placeholder="ابحث عن مريض بالاسم أو رقم المريض..." type="text"/>
<div class="hidden absolute top-full right-0 left-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-dropdown z-30 py-1 max-h-48 overflow-y-auto" id="patient-suggestions"></div>
</div>
<!-- Selected Patient Card -->
<div class="hidden bg-blue-50/70 border border-blue-100 rounded-xl p-3 flex items-center justify-between" id="selected-patient-card"><div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs" id="patient-avatar-badge">أ م</div>
<div>
<div class="font-bold text-slate-900 text-xs" id="patient-name-display">أحمد محمود</div>
<div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-numeric-uieric-ui">
<span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="patient-id-badge">P-10482</span>
<span class="">•</span>
<span class="" id="patient-age-gender-display">—</span>
</div>
</div>
</div>
<button class="text-xs text-brand font-bold hover:underline" id="change-patient-btn" type="button">تغيير</button></div>
</div>
<!-- Associated Visit -->
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">الزيارة المرتبطة</label>
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="form-visit"><option class="" value="visit_2024_10_18">كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد</option><option class="" value="none">بدون ربط بزيارة محددة</option></select>
</div>
<!-- Test Name -->
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">الفحص المطلوب *</label>
<input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-1 focus:ring-brand focus:border-brand" id="form-test-name" placeholder="ابحث عن فحص مختبري أو أدخل اسم الفحص..." type="text"/>
</div>
<!-- Priority & Doctor -->
<div class="grid grid-cols-2 gap-4">
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">الأولوية</label>
<select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="form-priority"><option class="" value="normal">عادي</option><option class="" value="urgent">عاجل</option></select>
</div>
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">الطبيب الطالب</label>
<input class="w-full h-10 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-600 cursor-not-allowed" readonly="" type="text" value="د. محمد أحمد - استشاري طب باطني"/>
</div>
</div>
<!-- Context & Instructions -->
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">السياق السريري</label>
<textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="form-clinical-context" placeholder="أدخل السياق السريري المرتبط بالطلب..." rows="2"></textarea>
</div>
<div class="space-y-1.5">
<label class="block font-bold text-slate-700">تعليمات إضافية</label>
<input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="form-instructions" placeholder="أدخل تعليمات إضافية لجهة التنفيذ..." type="text"/>
</div>
<!-- Destination Mode Configuration -->
<div class="space-y-2 pt-2 border-t border-slate-100"><label class="block font-bold text-slate-800">جهة التنفيذ والإرسال</label>
<p class="text-[11px] text-slate-500 mb-2">يمكن حفظ الطلب دون جهة تنفيذ، بينما الإرسال يتطلب تحديد مختبر.</p>
<div class="space-y-2">
<label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
<input checked="" class="text-brand focus:ring-brand" name="destination_mode_choice" type="radio" value="unassigned"/>
<div>
<span class="font-bold text-slate-800">بدون تحديد جهة الآن</span>
<p class="text-[11px] text-slate-500">حفظ الطلب وتوجيهه لاحقاً عند الحاجة</p>
</div>
</label>
<label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
<input class="text-brand focus:ring-brand" name="destination_mode_choice" type="radio" value="thera_network"/>
<div>
<span class="font-bold text-slate-800">مختبر من شبكة THERA</span>
<p class="text-[11px] text-slate-500">اختر مختبرًا محددًا من شبكة THERA عند توفر بيانات مزودي الخدمة.</p>
</div>
</label>
<div class="hidden pr-6 pl-2 space-y-1.5" id="thera-network-options">
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
<input class="w-full h-9 pr-8 pl-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="form-network-lab-search" placeholder="ابحث عن مختبر..." type="text"/>
</div>
<p class="text-[11px] text-slate-500" id="form-network-lab-helper">اختر مختبرًا محددًا من شبكة THERA عند توفر بيانات مزودي الخدمة.</p>
<div class="hidden p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 text-center text-xs" id="form-network-empty-state">
                نتائج شبكة THERA ستظهر هنا بعد ربط مزودي الخدمة
              </div>
</div>
<label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
<input class="text-brand focus:ring-brand" name="destination_mode_choice" type="radio" value="external"/>
<div>
<span class="font-bold text-slate-800">مختبر خارجي</span>
<p class="text-[11px] text-slate-500">جهة خارج شبكة THERA</p>
</div>
</label>
<div class="hidden pr-6 pl-2" id="external-provider-input">
<input class="w-full h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="form-external-name" placeholder="أدخل اسم المختبر الخارجي..." type="text"/>
</div>
</div></div>
</form>
<!-- Drawer Footer Actions -->
<div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse md:flex-row md:items-center md:justify-between gap-3">
<button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-lg font-bold transition-colors thera-major-btn" id="drawer-create-cancel" type="button">
          إلغاء
        </button>
<div class="flex flex-wrap items-center gap-2">
<button class="h-10 px-3.5 bg-[#F8FAFC] border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-lg font-bold transition-colors thera-major-btn" id="save-as-draft-btn" type="button">
            حفظ كمسودة
          </button>
<button class="h-10 px-4 bg-[#EFF6FF] border border-blue-200 text-[#0369A1] hover:bg-blue-100 rounded-lg font-bold transition-colors thera-major-btn" id="save-as-verified-btn" type="button">
            حفظ كموثق
          </button>
<button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold transition-colors shadow-sm thera-major-btn" id="save-and-send-btn" type="button">
            إرسال إلى المختبر
          </button>
</div>
</div>
</div>
</div>
</aside>
<!-- Drawer 2: Details & Actions View -->
<aside aria-labelledby="details-order-id" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" data-purpose="details-lab-order-drawer" id="drawer-details-order" role="dialog">
<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" id="drawer-details-backdrop"></div>
<div class="fixed inset-y-0 left-0 max-w-full flex pl-0">
<div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
<!-- Header -->
<div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
<span class="material-symbols-outlined text-xl">receipt_long</span>
</div>
<div>
<div class="flex items-center gap-2">
<h2 class="text-base font-bold text-slate-900 font-numeric-uieric-ui" id="details-order-id">LAB-0000</h2>
<span class="px-2 py-0.5 rounded-md text-[10px] font-bold" id="details-priority-badge">عادي</span>
</div>
<p class="text-xs text-slate-500" id="details-order-date">تاريخ الطلب: --</p>
</div>
</div>
<button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center thera-icon-btn" id="drawer-details-close" type="button">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<!-- Content -->
<div class="flex-1 overflow-y-auto p-6 space-y-6 text-xs">
<!-- Patient Summary Card -->
<div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
<div class="text-[11px] font-bold text-slate-400 uppercase mb-2">بيانات المريض</div>
<div class="flex items-center justify-between">
<div><div class="text-sm font-bold text-slate-900" id="details-patient-name">--</div>
<div class="flex items-center gap-1.5 text-slate-500 font-numeric-uieric-ui mt-0.5">
<span class="bg-white border border-slate-200 text-slate-700 px-1.5 py-0.2 rounded text-[10px] font-medium" dir="ltr" id="details-patient-id-badge">P-10482</span>
<span class="">•</span>
<span class="" id="details-patient-age-gender">—</span>
</div></div>
<div class="text-left">
<span class="text-[11px] text-slate-400 block">الطبيب المعالج</span>
<span class="font-bold text-slate-800" id="details-doctor-name">د. محمد أحمد</span>
</div>
</div>
</div>
<!-- Status Grid: Row 1 -->
<div class="grid grid-cols-2 gap-3">
<div class="bg-white border border-slate-200 rounded-xl p-3">
<span class="text-slate-400 block mb-1">حالة الطلب</span>
<div class="inline-block px-2.5 py-1 rounded-lg font-bold text-xs" id="details-status-pill">--</div>
</div>
<div class="bg-white border border-slate-200 rounded-xl p-3">
<span class="text-slate-400 block mb-1">جهة التنفيذ</span>
<div class="font-bold text-slate-800" id="details-provider-text">--</div>
</div>
</div>
<!-- Status Grid: Row 2 -->
<div class="grid grid-cols-3 gap-3">
<div class="bg-white border border-slate-200 rounded-xl p-3">
<span class="text-slate-400 block mb-1">حالة النتيجة</span>
<div class="inline-block px-2 py-0.5 rounded-lg font-bold text-[11px]" id="details-result-pill">--</div>
</div>
<div class="bg-white border border-slate-200 rounded-xl p-3">
<span class="text-slate-400 block mb-1">حالة التقرير</span>
<div class="inline-block px-2 py-0.5 rounded-lg font-bold text-[11px]" id="details-report-pill">--</div>
</div>
<div class="bg-white border border-slate-200 rounded-xl p-3">
<span class="text-slate-400 block mb-1">مراجعة الطبيب</span>
<div class="inline-block px-2 py-0.5 rounded-lg font-bold text-[11px]" id="details-review-pill">--</div>
</div>
</div>
<!-- Test Details -->
<div class="space-y-3">
<div class="border-b border-slate-100 pb-2">
<span class="text-slate-400 block mb-1">الفحص المخبري المطلوب</span>
<div class="text-sm font-bold text-slate-900" id="details-test-name">—</div>
</div>
<div class="border-b border-slate-100 pb-2">
<span class="text-slate-400 block mb-1">الزيارة المرتبطة</span>
<div class="text-slate-800 font-medium" id="details-visit-text">—</div>
</div>
<div class="border-b border-slate-100 pb-2">
<span class="text-slate-400 block mb-1">السياق السريري</span>
<div class="text-slate-800" id="details-clinical-text">—</div>
</div>
<div>
<span class="text-slate-400 block mb-1">تعليمات إضافية</span>
<div class="text-slate-800" id="details-instructions-text">—</div>
</div>
</div>
<!-- Doctor Review Box (Conditional) -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden" id="doctor-review-box">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-brand text-2xl">verified</span>
<div class="flex-1">
<h4 class="font-bold text-slate-900 text-xs mb-1">النتيجة متاحة للمراجعة</h4>
<p class="text-[11px] text-slate-600 mb-3">يمكن تسجيل مراجعة الطبيب بعد الاطلاع على البيانات المتاحة.</p>
<button class="h-8 px-4 bg-brand hover:bg-brand-hover text-white rounded-lg font-bold text-xs transition-colors shadow-sm thera-icon-btn" id="perform-doctor-review-btn" type="button">
                تسجيل مراجعة الطبيب
              </button>
</div>
</div>
</div>
<!-- Routing Section Box (Conditional) -->
<div class="bg-slate-50 border border-slate-200 rounded-xl p-4 hidden" id="routing-action-box">
<h4 class="font-bold text-slate-800 text-xs mb-1">تحديد جهة التنفيذ</h4>
<p class="text-[11px] text-slate-500 mb-3">يمكن تحديد المختبر وتجهيز الطلب للإرسال.</p>
<div class="space-y-3">
<select class="rtl-select w-full h-9 bg-white border border-slate-200 rounded-lg text-xs text-slate-700" id="details-route-destination-mode">
<option value="unassigned">بدون تحديد جهة الآن</option>
<option value="thera_network">مختبر من شبكة THERA</option>
<option value="external">مختبر خارجي</option>
</select>
<div class="hidden space-y-1.5" id="details-route-network-box">
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
<input class="w-full h-9 pr-8 pl-3 bg-white border border-slate-200 rounded-lg text-xs" id="details-route-network-search" placeholder="ابحث عن مختبر..." type="text"/>
</div>
<p class="text-[11px] text-slate-500" id="details-route-network-helper">ابدأ الكتابة للبحث في شبكة THERA</p>
<div class="hidden p-3 bg-white border border-slate-200 rounded-lg text-slate-400 text-center text-xs" id="details-route-empty-state">
                نتائج شبكة THERA ستظهر هنا بعد ربط مزودي الخدمة
              </div>
</div>
<div class="hidden space-y-1.5" id="details-route-external-box">
<input class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs" id="details-route-external-input" placeholder="أدخل اسم المختبر الخارجي..." type="text"/>
</div>
<button class="h-8 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold text-xs shadow-sm transition-colors thera-icon-btn" id="confirm-routing-btn" type="button">
              تجهيز للإرسال
            </button>
</div>
</div>
</div>
<!-- Footer -->
<div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
<button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-lg font-bold transition-colors thera-major-btn" id="drawer-details-cancel" type="button">
          إغلاق
        </button>
<button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-lg font-bold flex items-center gap-2 transition-colors thera-major-btn" id="print-order-btn" type="button">
<span class="material-symbols-outlined text-base">print</span>
<span class="">طباعة استمارة الفحص</span>
</button>
</div>
</div>
</div>
</aside>
<!-- Context Menu Container -->
<div class="hidden fixed bg-white rounded-xl shadow-lg border border-[#E2E8F0] p-1.5 z-[80] text-slate-700 text-xs w-[220px] font-medium" dir="rtl" id="table-row-menu"></div>
<!-- Toast Notification Container -->
<div class="fixed bottom-6 left-6 z-50 flex flex-col gap-2 pointer-events-none" id="toast-container"></div>
<!-- BEGIN: Application Scripts -->
<script data-purpose="lab-orders-app">
  const DEMO_PATIENTS = [
    { id: 'P-10482', name: 'أحمد محمود', initials: 'أ م', age: '42', gender: 'ذكر' }
  ];

  const STORAGE_KEY = 'thera_lab_orders_doctor_demo';
  const VERSION_KEY = 'thera_lab_orders_doctor_demo_version';
  const CURRENT_STORAGE_VERSION = '8';
  const SIDEBAR_PIN_KEY = 'thera_sidebar_pinned';

  let ordersList = [];
  let currentFilterTab = 'all';
  let activeMenuOrderId = null;
  let selectedPatientForCreate = null;
  let activeDetailsOrderId = null;
  let routingLabOrderId = null;

  let selectedTheraLab = null;
  let selectedDetailsTheraLab = null;
  let isPinned = true;

  document.addEventListener('DOMContentLoaded', () => {
    initSidebarState();
    initDoctorMenu();
    initQuickAdd();
    initStorage();
    initEventListeners();
    renderOrders();
  });

  function initSidebarState() {
    const sidebar = document.getElementById('app-sidebar');
    const pinBtn = document.getElementById('sidebar-pin-toggle');

    if (!sidebar || !pinBtn) return;

    const savedPin = localStorage.getItem(SIDEBAR_PIN_KEY);
    isPinned = savedPin !== null ? savedPin === 'true' : true;
    updateSidebarState();

    pinBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleSidebarPin();
    });

    sidebar.addEventListener('mouseenter', () => {
      if (!isPinned) sidebar.classList.add('sidebar-hovered');
    });

    sidebar.addEventListener('mouseleave', () => {
      if (!isPinned) {
        sidebar.classList.remove('sidebar-hovered');
        document.getElementById('doctor-account-menu')?.classList.add('hidden');
      }
    });
  }

  function updateSidebarState() {
    const sidebar = document.getElementById('app-sidebar');
    const mainLayout = document.getElementById('main-content-layout');
    const topHeader = document.getElementById('top-header');
    const pinBtn = document.getElementById('sidebar-pin-toggle');
    const pinIcon = document.getElementById('sidebar-pin-icon');

    if (!sidebar || !mainLayout || !topHeader) return;

    sidebar.classList.remove('sidebar-hovered');

    if (isPinned) {
      sidebar.classList.remove('sidebar-collapsed');
      mainLayout.style.marginRight = '268px';
      topHeader.style.right = '268px';
      if (pinBtn) {
        pinBtn.classList.add('text-[#0495F6]', 'bg-white/10');
        pinBtn.title = 'طي القائمة الجانبية';
      }
      if (pinIcon) pinIcon.classList.add('fill-1');
    } else {
      sidebar.classList.add('sidebar-collapsed');
      mainLayout.style.marginRight = '72px';
      topHeader.style.right = '72px';
      if (pinBtn) {
        pinBtn.classList.remove('text-[#0495F6]', 'bg-white/10');
        pinBtn.title = 'تثبيت القائمة الجانبية';
      }
      if (pinIcon) pinIcon.classList.remove('fill-1');
    }

    if (pinIcon) pinIcon.textContent = 'push_pin';
  }

  function toggleSidebarPin() {
    isPinned = !isPinned;
    localStorage.setItem(SIDEBAR_PIN_KEY, isPinned ? 'true' : 'false');
    updateSidebarState();
  }

  function initDoctorMenu() {
    const trigger = document.getElementById('doctor-card-trigger');
    const menu = document.getElementById('doctor-account-menu');

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && !trigger.contains(e.target)) {
        menu.classList.add('hidden');
      }
    });
  }

  function initQuickAdd() {
    const container = document.getElementById('quick-add-container');
    const btn = document.getElementById('quick-add-btn');
    const dropdown = document.getElementById('quick-add-dropdown');
    const arrow = document.getElementById('quick-add-arrow');
    const labOrderBtn = document.getElementById('quick-add-lab-order-btn');
    let leaveTimeout = null;

    function setOpen(open) {
      if (open) {
        if (leaveTimeout) clearTimeout(leaveTimeout);
        dropdown.classList.remove('hidden');
        arrow.textContent = 'expand_less';
      } else {
        dropdown.classList.add('hidden');
        arrow.textContent = 'expand_more';
      }
    }

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = !dropdown.classList.contains('hidden');
      setOpen(!isOpen);
    });

    container.addEventListener('mouseenter', () => {
      setOpen(true);
    });

    container.addEventListener('mouseleave', () => {
      leaveTimeout = setTimeout(() => {
        setOpen(false);
      }, 200);
    });

    document.addEventListener('click', (e) => {
      if (!container.contains(e.target)) {
        setOpen(false);
      }
    });

    labOrderBtn.addEventListener('click', () => {
      setOpen(false);
      openCreateOrderDrawer();
    });
  }

  function safeString(value) {
    return typeof value === 'string' ? value : '';
  }

  function normalizeLabOrder(raw) {
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
      testName: safeString(raw.testName),
      clinicalContext: safeString(raw.clinicalContext),
      priority: raw.priority === 'urgent' ? 'urgent' : 'normal',
      instructions: safeString(raw.instructions),
      workflowState: ['draft', 'documented', 'route_ready', 'route_requested', 'cancelled'].includes(raw.workflowState)
        ? raw.workflowState : 'draft',
      destinationMode: ['unassigned', 'thera_network', 'external'].includes(raw.destinationMode)
        ? raw.destinationMode : 'unassigned',
      laboratoryId: safeString(raw.laboratoryId),
      laboratoryName: safeString(raw.laboratoryName),
      resultState: ['none', 'in_progress', 'result_ready'].includes(raw.resultState)
        ? raw.resultState : 'none',
      providerReportState: ['unavailable', 'published', 'approved'].includes(raw.providerReportState)
        ? raw.providerReportState : 'unavailable',
      doctorReviewState: ['unavailable', 'pending_review', 'reviewed'].includes(raw.doctorReviewState)
        ? raw.doctorReviewState : 'unavailable'
    };
  }

  function getPatientMeta(order) {
    const age = order.patientAge ? `${order.patientAge} سنة` : '—';
    const gender = order.patientGender || '—';
    return `${age} • ${gender}`;
  }

  function initStorage() {
    const storedVersion = localStorage.getItem(VERSION_KEY);
    const storedData = localStorage.getItem(STORAGE_KEY);

    if (storedVersion !== CURRENT_STORAGE_VERSION) {
      ordersList = [];
      localStorage.setItem(STORAGE_KEY, JSON.stringify([]));
      localStorage.setItem(VERSION_KEY, CURRENT_STORAGE_VERSION);
      return;
    }

    if (!storedData) {
      ordersList = [];
      return;
    }

    try {
      const parsed = JSON.parse(storedData);
      ordersList = Array.isArray(parsed)
        ? parsed.map(normalizeLabOrder).filter(Boolean)
        : [];
    } catch {
      ordersList = [];
    }
  }

  function saveOrdersToStorage() {
    ordersList = ordersList.map(normalizeLabOrder).filter(Boolean);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ordersList));
  }

  function initEventListeners() {
    document.getElementById('open-create-order-btn').addEventListener('click', () => openCreateOrderDrawer());
    document.getElementById('empty-state-create-btn').addEventListener('click', () => openCreateOrderDrawer());

    document.getElementById('drawer-create-close').addEventListener('click', closeCreateOrderDrawer);
    document.getElementById('drawer-create-cancel').addEventListener('click', closeCreateOrderDrawer);
    document.getElementById('drawer-create-backdrop').addEventListener('click', closeCreateOrderDrawer);

    document.getElementById('drawer-details-close').addEventListener('click', closeDetailsOrderDrawer);
    document.getElementById('drawer-details-cancel').addEventListener('click', closeDetailsOrderDrawer);
    document.getElementById('drawer-details-backdrop').addEventListener('click', closeDetailsOrderDrawer);

    const radios = document.querySelectorAll('input[name="destination_mode_choice"]');
    radios.forEach(radio => {
      radio.addEventListener('change', (e) => {
        const val = e.target.value;
        document.getElementById('thera-network-options').classList.toggle('hidden', val !== 'thera_network');
        document.getElementById('external-provider-input').classList.toggle('hidden', val !== 'external');
      });
    });

    // Network search in create drawer
    const netInput = document.getElementById('form-network-lab-search');
    const netEmpty = document.getElementById('form-network-empty-state');
    netInput.addEventListener('input', (e) => {
      const q = e.target.value.trim();
      selectedTheraLab = null;
      if (q.length > 0) {
        netEmpty.classList.remove('hidden');
      } else {
        netEmpty.classList.add('hidden');
      }
    });

    document.querySelectorAll('#order-tabs .tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#order-tabs .tab-btn').forEach(b => {
          b.className = `tab-btn status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface`;
          const counter = b.querySelector('.tab-count');
          if (counter) {
            counter.className = 'tab-count px-2 py-0.5 rounded-full text-xs bg-surface-container-highest text-on-surface-variant font-numeric-uieric-ui';
          }
        });

        btn.className = `tab-btn status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-primary-container text-white shadow-sm`;
        const activeCounter = btn.querySelector('.tab-count');
        if (activeCounter) {
          activeCounter.className = 'tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-numeric-uieric-ui';
        }

        currentFilterTab = btn.getAttribute('data-tab');
        renderOrders();
      });
    });

    document.getElementById('filter-search').addEventListener('input', renderOrders);
    document.getElementById('filter-order-status').addEventListener('change', renderOrders);
    document.getElementById('filter-priority').addEventListener('change', renderOrders);
    document.getElementById('filter-provider').addEventListener('change', renderOrders);
    document.getElementById('filter-result-status').addEventListener('change', renderOrders);
    document.getElementById('filter-sort').addEventListener('change', renderOrders);

    document.getElementById('reset-filters-btn').addEventListener('click', resetFilters);
    document.getElementById('filter-empty-reset-btn').addEventListener('click', resetFilters);

    document.getElementById('save-as-draft-btn').addEventListener('click', () => handleSaveOrder('draft'));
    document.getElementById('save-as-verified-btn').addEventListener('click', () => handleSaveOrder('documented'));
    document.getElementById('save-and-send-btn').addEventListener('click', () => handleSaveOrder('route_requested'));

    const pSearch = document.getElementById('patient-search-input');
    const pSuggest = document.getElementById('patient-suggestions');
    pSearch.addEventListener('input', (e) => {
      const val = e.target.value.trim().toLowerCase();
      pSuggest.replaceChildren();
      if (!val) {
        pSuggest.classList.add('hidden');
        return;
      }
      const matches = DEMO_PATIENTS.filter(p => p.name.toLowerCase().includes(val) || p.id.toLowerCase().includes(val));
      if (matches.length === 0) {
        const noRes = document.createElement('div');
        noRes.className = 'px-3 py-2 text-slate-400 text-xs';
        noRes.textContent = 'لا توجد نتائج مطابقة';
        pSuggest.appendChild(noRes);
      } else {
        matches.forEach(p => {
          const row = document.createElement('div');
          row.className = 'px-3 py-2 hover:bg-slate-50 cursor-pointer flex justify-between items-center';
          const nameSpan = document.createElement('span');
          nameSpan.className = 'font-bold text-slate-800';
          nameSpan.textContent = p.name;
          const idSpan = document.createElement('span');
          idSpan.className = 'text-slate-400 font-numeric-uieric-ui';
          idSpan.textContent = p.id;
          row.appendChild(nameSpan);
          row.appendChild(idSpan);
          row.addEventListener('click', () => {
            selectPatientForCreate(p);
          });
          pSuggest.appendChild(row);
        });
      }
      pSuggest.classList.remove('hidden');
    });

    document.getElementById('change-patient-btn').addEventListener('click', () => {
      selectedPatientForCreate = null;
      document.getElementById('selected-patient-card').classList.add('hidden');
      document.getElementById('patient-search-box').classList.remove('hidden');
      pSearch.value = '';
      pSearch.focus();
    });

    const routeModeSelect = document.getElementById('details-route-destination-mode');
    routeModeSelect.addEventListener('change', (e) => {
      const mode = e.target.value;
      document.getElementById('details-route-network-box').classList.toggle('hidden', mode !== 'thera_network');
      document.getElementById('details-route-external-box').classList.toggle('hidden', mode !== 'external');
    });

    const detailsNetSearch = document.getElementById('details-route-network-search');
    const detailsNetEmpty = document.getElementById('details-route-empty-state');
    detailsNetSearch.addEventListener('input', (e) => {
      const q = e.target.value.trim();
      selectedDetailsTheraLab = null;
      if (q.length > 0) {
        detailsNetEmpty.classList.remove('hidden');
      } else {
        detailsNetEmpty.classList.add('hidden');
      }
    });

    document.getElementById('confirm-routing-btn').addEventListener('click', () => {
      const targetId = routingLabOrderId || activeDetailsOrderId;
      if (!targetId) return;

      const order = ordersList.find(item => item.id === targetId);
      if (!order) return;

      const mode = document.getElementById('details-route-destination-mode').value;

      if (mode === 'unassigned') {
        order.destinationMode = 'unassigned';
        order.laboratoryId = '';
        order.laboratoryName = '';
        saveOrdersToStorage();
        renderOrders();
        openDetailsOrderDrawer(order.id);
        return;
      }

      if (mode === 'thera_network') {
        if (!selectedDetailsTheraLab) {
          showToast('اختر مختبرًا من شبكة THERA قبل الإرسال', 'warning');
          return;
        }
        order.destinationMode = 'thera_network';
        order.laboratoryId = safeString(selectedDetailsTheraLab.id);
        order.laboratoryName = safeString(selectedDetailsTheraLab.name);
      }

      if (mode === 'external') {
        const extName = document.getElementById('details-route-external-input').value.trim();
        if (!extName) {
          showToast('أدخل اسم المختبر الخارجي قبل الإرسال', 'warning');
          return;
        }
        order.destinationMode = 'external';
        order.laboratoryId = '';
        order.laboratoryName = extName;
      }

      order.workflowState = 'route_requested';
      saveOrdersToStorage();
      renderOrders();
      openDetailsOrderDrawer(order.id);
      showToast('تم تجهيز طلب المختبر للإرسال إلى الجهة المحددة', 'info');
    });

    document.getElementById('perform-doctor-review-btn').addEventListener('click', () => {
      if (!activeDetailsOrderId) return;
      const order = ordersList.find(o => o.id === activeDetailsOrderId);
      if (!order) return;
      order.doctorReviewState = 'reviewed';
      saveOrdersToStorage();
      renderOrders();
      openDetailsOrderDrawer(order.id);
      showToast('تم تسجيل مراجعة الطبيب محليًا', 'info');
    });

    document.getElementById('print-order-btn').addEventListener('click', () => {
      window.print();
    });

    // Close context menu on events
    window.addEventListener('resize', closeContextMenu);
    window.addEventListener('scroll', closeContextMenu, true);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeContextMenu();
      }
    });
    document.addEventListener('click', (e) => {
      const menu = document.getElementById('table-row-menu');
      if (!menu.contains(e.target)) {
        closeContextMenu();
      }
    });
  }

  function closeContextMenu() {
    const menu = document.getElementById('table-row-menu');
    if (menu) {
      menu.classList.add('hidden');
      menu.replaceChildren();
    }
    activeMenuOrderId = null;
  }

  function positionContextMenu(menu, trigger) {
    const rect = trigger.getBoundingClientRect();
    const menuWidth = 220;
    const viewportPadding = 8;
    const gap = 6;

    menu.classList.remove('hidden');
    const menuHeight = menu.offsetHeight || 160;

    let top = rect.bottom + gap;
    if (top + menuHeight > window.innerHeight - viewportPadding) {
      top = rect.top - menuHeight - gap;
    }
    top = Math.max(viewportPadding, Math.min(top, window.innerHeight - menuHeight - viewportPadding));

    // Align to RTL (prefer right alignment to trigger rect.right)
    let left = rect.right - menuWidth;
    if (left < viewportPadding) {
      left = viewportPadding;
    }
    if (left + menuWidth > window.innerWidth - viewportPadding) {
      left = window.innerWidth - menuWidth - viewportPadding;
    }

    menu.style.position = 'fixed';
    menu.style.top = `${Math.round(top)}px`;
    menu.style.left = `${Math.round(left)}px`;
  }

  function selectPatientForCreate(p) {
    selectedPatientForCreate = p;
    document.getElementById('patient-name-display').textContent = p.name;
    document.getElementById('patient-avatar-badge').textContent = p.initials;
    document.getElementById('patient-id-badge').textContent = p.id;
    const patientMeta = document.getElementById('patient-age-gender-display');
    if (patientMeta) patientMeta.textContent = `${p.age} سنة • ${p.gender}`;
    document.getElementById('patient-suggestions').classList.add('hidden');
    document.getElementById('patient-search-box').classList.add('hidden');
    document.getElementById('selected-patient-card').classList.remove('hidden');
  }

  function resetFilters() {
    document.getElementById('filter-search').value = '';
    document.getElementById('filter-order-status').value = '';
    document.getElementById('filter-priority').value = '';
    document.getElementById('filter-provider').value = '';
    document.getElementById('filter-result-status').value = '';
    document.getElementById('filter-sort').value = 'newest';
    window.refreshTheraSelects?.();
    renderOrders();
    showToast('تمت إعادة ضبط الفلاتر', 'info');
  }

  function getWorkflowLabel(state) {
    switch (state) {
      case 'draft': return 'مسودة';
      case 'documented': return 'موثق';
      case 'route_ready': return 'جاهز للتوجيه';
      case 'route_requested': return 'طلب توجيه';
      case 'cancelled': return 'ملغى';
      default: return 'مسودة';
    }
  }

  function getProviderDisplayName(order) {
    if (order.destinationMode === 'unassigned') return 'غير محددة';
    return order.laboratoryName || '—';
  }

  function getResultStateLabel(state) {
    switch (state) {
      case 'none': return 'لا توجد نتيجة';
      case 'in_progress': return 'قيد التنفيذ';
      case 'result_ready': return 'النتيجة جاهزة';
      default: return 'لا توجد نتيجة';
    }
  }

  function getReportStateLabel(state) {
    switch (state) {
      case 'published': return 'منشور من جهة التنفيذ';
      case 'approved': return 'معتمد من جهة التنفيذ';
      case 'unavailable':
      default: return 'غير متاح';
    }
  }

  function getDoctorReviewLabel(state) {
    switch (state) {
      case 'reviewed': return 'راجع الطبيب';
      case 'pending_review': return 'بانتظار المراجعة';
      case 'unavailable':
      default: return 'غير متاح';
    }
  }

  function renderOrders() {
    const tbody = document.getElementById('orders-table-body');
    const mobileContainer = document.getElementById('mobile-orders-container');
    tbody.replaceChildren();
    mobileContainer.replaceChildren();

    const initialEmptyBox = document.getElementById('initial-empty-state');
    const filterEmptyBox = document.getElementById('filter-empty-state');

    updateCounts();

    if (ordersList.length === 0) {
      initialEmptyBox.classList.remove('hidden');
      filterEmptyBox.classList.add('hidden');
      return;
    } else {
      initialEmptyBox.classList.add('hidden');
    }

    const searchTerm = document.getElementById('filter-search').value.trim().toLowerCase();
    const statusFilter = document.getElementById('filter-order-status').value;
    const priorityFilter = document.getElementById('filter-priority').value;
    const providerFilter = document.getElementById('filter-provider').value;
    const resultFilter = document.getElementById('filter-result-status').value;
    const sortChoice = document.getElementById('filter-sort').value;

    let filtered = ordersList.filter(item => {
      if (currentFilterTab === 'draft' && item.workflowState !== 'draft') return false;
      if (currentFilterTab === 'documented' && item.workflowState !== 'documented') return false;
      if (currentFilterTab === 'route_ready' && item.workflowState !== 'route_ready') return false;
      if (currentFilterTab === 'route_requested' && item.workflowState !== 'route_requested') return false;

      if (searchTerm) {
        const matchPatientName = (item.patientName || '').toLowerCase().includes(searchTerm);
        const matchPatientId = (item.patientId || '').toLowerCase().includes(searchTerm);
        const matchId = (item.orderNumber || '').toLowerCase().includes(searchTerm);
        const matchTest = (item.testName || '').toLowerCase().includes(searchTerm);
        if (!matchPatientName && !matchPatientId && !matchId && !matchTest) return false;
      }

      if (statusFilter && item.workflowState !== statusFilter) return false;
      if (priorityFilter && item.priority !== priorityFilter) return false;
      if (providerFilter && item.destinationMode !== providerFilter) return false;
      if (resultFilter && item.resultState !== resultFilter) return false;

      return true;
    });

    filtered.sort((a, b) => {
      if (sortChoice === 'newest') return (b.isoDate || b.id).localeCompare(a.isoDate || a.id);
      if (sortChoice === 'oldest') return (a.isoDate || a.id).localeCompare(b.isoDate || b.id);
      if (sortChoice === 'urgent') {
        if (a.priority === 'urgent' && b.priority !== 'urgent') return -1;
        if (b.priority === 'urgent' && a.priority !== 'urgent') return 1;
        return (b.isoDate || b.id).localeCompare(a.isoDate || a.id);
      }
      return 0;
    });

    if (filtered.length === 0) {
      filterEmptyBox.classList.remove('hidden');
      return;
    } else {
      filterEmptyBox.classList.add('hidden');
    }

    filtered.forEach(order => {
      // Desktop Row
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-slate-50/80 transition-colors border-b border-slate-100';

      // Patient
      const tdPatient = document.createElement('td');
      tdPatient.className = 'py-3.5 px-4';
      const pWrapper = document.createElement('div');
      pWrapper.className = 'flex items-center gap-2.5';
      const pAvatar = document.createElement('div');
      pAvatar.className = 'w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] flex-shrink-0';
      pAvatar.textContent = order.patientInitials;
      const pInfo = document.createElement('div');
      const pName = document.createElement('div');
      pName.className = 'font-bold text-slate-900';
      pName.textContent = order.patientName;
      const pMeta = document.createElement('div');
      pMeta.className = 'flex items-center gap-1.5 text-[11px] text-slate-400 font-numeric-uieric-ui';
      const pIdBadge = document.createElement('span');
      pIdBadge.className = 'bg-slate-100 text-slate-600 px-1 py-0.2 rounded text-[10px] font-medium';
      pIdBadge.setAttribute('dir', 'ltr');
      pIdBadge.textContent = order.patientId;
      const pBullet = document.createElement('span');
      pBullet.textContent = '•';
      const pAgeGen = document.createElement('span');
      pAgeGen.textContent = getPatientMeta(order);
      pMeta.append(pIdBadge, pBullet, pAgeGen);

      pInfo.append(pName, pMeta);
      pWrapper.append(pAvatar, pInfo);
      tdPatient.appendChild(pWrapper);
      tr.appendChild(tdPatient);

      // Order
      const tdOrder = document.createElement('td');
      tdOrder.className = 'py-3.5 px-4';
      const oId = document.createElement('div');
      oId.className = 'font-bold font-numeric-uieric-ui text-brand text-xs cursor-pointer hover:underline';
      oId.textContent = order.orderNumber;
      oId.addEventListener('click', () => openDetailsOrderDrawer(order.id));
      const oName = document.createElement('div');
      oName.className = 'text-slate-700 font-medium truncate max-w-[180px]';
      oName.textContent = order.testName || '—';
      tdOrder.append(oId, oName);
      tr.appendChild(tdOrder);

      // Date
      const tdDate = document.createElement('td');
      tdDate.className = 'py-3.5 px-4 font-numeric-uieric-ui text-slate-600 whitespace-nowrap';
      tdDate.textContent = order.orderDate;
      tr.appendChild(tdDate);

      // Visit
      const tdVisit = document.createElement('td');
      tdVisit.className = 'py-3.5 px-4 text-slate-500 max-w-[140px] truncate';
      const visitVal = order.visitTitle || '—';
      tdVisit.textContent = visitVal.includes('—') ? visitVal.split('—')[0].trim() : visitVal;
      tr.appendChild(tdVisit);

      // Priority
      const tdPriority = document.createElement('td');
      tdPriority.className = 'py-3.5 px-4';
      const pBadge = document.createElement('span');
      pBadge.className = order.priority === 'urgent' ? 'px-2 py-0.5 rounded-md font-bold text-[10px] bg-red-100 text-red-700 border border-red-200' : 'px-2 py-0.5 rounded-md font-medium text-[10px] bg-slate-100 text-slate-600';
      pBadge.textContent = order.priority === 'urgent' ? 'عاجل' : 'عادي';
      tdPriority.appendChild(pBadge);
      tr.appendChild(tdPriority);

      // Status
      const tdStatus = document.createElement('td');
      tdStatus.className = 'py-3.5 px-4';
      const sBadge = document.createElement('span');
      sBadge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] ';
      if (order.workflowState === 'draft') sBadge.className += 'bg-amber-100 text-amber-800';
      else if (order.workflowState === 'documented') sBadge.className += 'bg-blue-100 text-blue-800';
      else if (order.workflowState === 'route_ready') sBadge.className += 'bg-purple-100 text-purple-800';
      else if (order.workflowState === 'route_requested') sBadge.className += 'bg-blue-50 text-blue-700 border border-blue-200';
      else sBadge.className += 'bg-slate-100 text-slate-700';
      sBadge.textContent = getWorkflowLabel(order.workflowState);
      tdStatus.appendChild(sBadge);
      tr.appendChild(tdStatus);

      // Provider
      const tdProvider = document.createElement('td');
      tdProvider.className = 'py-3.5 px-4 text-slate-700 font-medium truncate max-w-[140px]';
      tdProvider.textContent = getProviderDisplayName(order);
      tr.appendChild(tdProvider);

      // Result State
      const tdResult = document.createElement('td');
      tdResult.className = 'py-3.5 px-4';
      const rBadge = document.createElement('span');
      rBadge.className = 'px-2 py-0.5 rounded-md font-medium text-[10px] ';
      if (order.resultState === 'result_ready') rBadge.className += 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold';
      else if (order.resultState === 'in_progress') rBadge.className += 'bg-sky-50 text-sky-700 border border-sky-200';
      else rBadge.className += 'bg-slate-100 text-slate-500';
      rBadge.textContent = getResultStateLabel(order.resultState);
      tdResult.appendChild(rBadge);
      tr.appendChild(tdResult);

      // Report State
      const tdReport = document.createElement('td');
      tdReport.className = 'py-3.5 px-4';
      const repBadge = document.createElement('span');
      if (order.providerReportState === 'approved') {
        repBadge.className = 'text-emerald-600 font-bold flex items-center gap-1 text-xs';
        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined text-sm';
        icon.textContent = 'check';
        const txt = document.createElement('span');
        txt.textContent = 'معتمد من جهة التنفيذ';
        repBadge.append(icon, txt);
      } else if (order.providerReportState === 'published') {
        repBadge.className = 'text-sky-600 font-medium text-xs';
        repBadge.textContent = 'منشور من جهة التنفيذ';
      } else {
        repBadge.className = 'text-slate-400 text-xs';
        repBadge.textContent = 'غير متاح';
      }
      tdReport.appendChild(repBadge);
      tr.appendChild(tdReport);

      // Doctor Review
      const tdDocReview = document.createElement('td');
      tdDocReview.className = 'py-3.5 px-4';
      const docBadge = document.createElement('span');
      if (order.doctorReviewState === 'reviewed') {
        docBadge.className = 'text-emerald-700 font-bold text-[11px] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200';
        docBadge.textContent = 'راجع الطبيب';
      } else if (order.doctorReviewState === 'pending_review') {
        docBadge.className = 'text-amber-700 font-medium text-[11px] bg-amber-50 px-2 py-0.5 rounded border border-amber-200';
        docBadge.textContent = 'بانتظار المراجعة';
      } else {
        docBadge.className = 'text-slate-400 text-[11px]';
        docBadge.textContent = 'غير متاح';
      }
      tdDocReview.appendChild(docBadge);
      tr.appendChild(tdDocReview);

      // Actions
      const tdActions = document.createElement('td');
      tdActions.className = 'py-3.5 px-4 text-center';
      const actionBtn = document.createElement('button');
      actionBtn.type = 'button';
      actionBtn.className = 'w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center mx-auto transition-colors';
      const dots = document.createElement('span');
      dots.className = 'material-symbols-outlined text-lg';
      dots.textContent = 'more_vert';
      actionBtn.appendChild(dots);
      actionBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openContextMenu(e, order.id);
      });
      tdActions.appendChild(actionBtn);
      tr.appendChild(tdActions);

      tbody.appendChild(tr);
    });

    renderMobileCards(filtered);
  }

  function renderMobileCards(list) {
    const mobileContainer = document.getElementById('mobile-orders-container');
    mobileContainer.replaceChildren();

    list.forEach(order => {
      const card = document.createElement('div');
      card.className = 'bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3';

      const header = document.createElement('div');
      header.className = 'flex items-center justify-between';

      const idWrapper = document.createElement('div');
      idWrapper.className = 'flex items-center gap-2';
      const oId = document.createElement('span');
      oId.className = 'font-bold font-numeric-uieric-ui text-brand text-xs cursor-pointer hover:underline';
      oId.textContent = order.orderNumber;
      oId.addEventListener('click', () => openDetailsOrderDrawer(order.id));

      const pBadge = document.createElement('span');
      pBadge.className = order.priority === 'urgent' ? 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700' : 'px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600';
      pBadge.textContent = order.priority === 'urgent' ? 'عاجل' : 'عادي';
      idWrapper.append(oId, pBadge);

      const moreBtn = document.createElement('button');
      moreBtn.type = 'button';
      moreBtn.className = 'w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors';
      const moreIcon = document.createElement('span');
      moreIcon.className = 'material-symbols-outlined text-base';
      moreIcon.textContent = 'more_vert';
      moreBtn.appendChild(moreIcon);
      moreBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openContextMenu(e, order.id);
      });

      header.append(idWrapper, moreBtn);

      const patientBox = document.createElement('div');
      patientBox.className = 'flex items-center gap-2.5';
      const avatar = document.createElement('div');
      avatar.className = 'w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0';
      avatar.textContent = order.patientInitials;
      const patDetails = document.createElement('div');
      const pName = document.createElement('div');
      pName.className = 'font-bold text-slate-900 text-xs';
      pName.textContent = order.patientName;
      const pMeta = document.createElement('div');
      pMeta.className = 'flex items-center gap-1.5 text-[11px] text-slate-400 font-numeric-uieric-ui';
      const idTag = document.createElement('span');
      idTag.className = 'bg-slate-100 text-slate-600 px-1 py-0.2 rounded text-[10px]';
      idTag.setAttribute('dir', 'ltr');
      idTag.textContent = order.patientId;
      const dot = document.createElement('span');
      dot.textContent = '•';
      const ageTxt = document.createElement('span');
      ageTxt.textContent = getPatientMeta(order);
      pMeta.append(idTag, dot, ageTxt);
      patDetails.append(pName, pMeta);
      patientBox.append(avatar, patDetails);

      const testInfo = document.createElement('div');
      testInfo.className = 'bg-slate-50 rounded-lg p-2.5 space-y-1';
      const tLabel = document.createElement('div');
      tLabel.className = 'text-[11px] text-slate-400';
      tLabel.textContent = 'الفحص المطلوب:';
      const tName = document.createElement('div');
      tName.className = 'text-xs font-bold text-slate-800';
      tName.textContent = order.testName || '—';
      testInfo.append(tLabel, tName);

      const footer = document.createElement('div');
      footer.className = 'pt-2 border-t border-slate-100 flex items-center justify-between text-xs';

      const badgesWrapper = document.createElement('div');
      badgesWrapper.className = 'flex items-center gap-1.5';
      const sBadge = document.createElement('span');
      sBadge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] ';
      if (order.workflowState === 'draft') sBadge.className += 'bg-amber-100 text-amber-800';
      else if (order.workflowState === 'documented') sBadge.className += 'bg-blue-100 text-blue-800';
      else if (order.workflowState === 'route_ready') sBadge.className += 'bg-purple-100 text-purple-800';
      else if (order.workflowState === 'route_requested') sBadge.className += 'bg-blue-50 text-blue-700 border border-blue-200';
      else sBadge.className += 'bg-slate-100 text-slate-700';
      sBadge.textContent = getWorkflowLabel(order.workflowState);

      const rBadge = document.createElement('span');
      rBadge.className = 'px-2 py-0.5 rounded-md font-medium text-[10px] ';
      if (order.resultState === 'result_ready') rBadge.className += 'bg-emerald-50 text-emerald-700 border border-emerald-200';
      else if (order.resultState === 'in_progress') rBadge.className += 'bg-sky-50 text-sky-700 border border-sky-200';
      else rBadge.className += 'bg-slate-100 text-slate-500';
      rBadge.textContent = getResultStateLabel(order.resultState);
      badgesWrapper.append(sBadge, rBadge);

      const dateSpan = document.createElement('span');
      dateSpan.className = 'font-numeric-uieric-ui text-slate-400 text-[11px]';
      dateSpan.textContent = order.orderDate;

      footer.append(badgesWrapper, dateSpan);

      card.append(header, patientBox, testInfo, footer);
      mobileContainer.appendChild(card);
    });
  }

  function updateCounts() {
    const total = ordersList.length;
    const allEl = document.getElementById('tab-count-all');
    const draftEl = document.getElementById('tab-count-draft');
    const documentedEl = document.getElementById('tab-count-documented');
    const readyEl = document.getElementById('tab-count-route-ready');
    const requestedEl = document.getElementById('tab-count-route-requested');

    if (allEl) allEl.textContent = total;
    if (draftEl) draftEl.textContent = ordersList.filter(o => o.workflowState === 'draft').length;
    if (documentedEl) documentedEl.textContent = ordersList.filter(o => o.workflowState === 'documented').length;
    if (readyEl) readyEl.textContent = ordersList.filter(o => o.workflowState === 'route_ready').length;
    if (requestedEl) requestedEl.textContent = ordersList.filter(o => o.workflowState === 'route_requested').length;
  }

  function openContextMenu(event, orderId) {
    activeMenuOrderId = orderId;
    const menu = document.getElementById('table-row-menu');
    const order = ordersList.find(o => o.id === orderId);
    if (!order) return;

    menu.replaceChildren();

    function createMenuItem(iconText, labelText, onClick, extraClass = '') {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `w-full text-right px-3 py-2 hover:bg-slate-50 rounded-lg flex items-center gap-2.5 transition-colors ${extraClass}`;
      const icon = document.createElement('span');
      icon.className = 'material-symbols-outlined text-lg text-slate-400';
      icon.textContent = iconText;
      const text = document.createElement('span');
      text.textContent = labelText;
      btn.append(icon, text);
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeContextMenu();
        onClick();
      });
      return btn;
    }

    function createDivider() {
      const div = document.createElement('div');
      div.className = 'my-1 border-t border-[#F1F5F9]';
      return div;
    }

    // Common action: عرض تفاصيل الطلب
    menu.appendChild(createMenuItem('visibility', 'عرض تفاصيل الطلب', () => {
      openDetailsOrderDrawer(order.id);
    }));

    if (order.workflowState === 'draft') {
      menu.appendChild(createMenuItem('edit', 'تعديل المسودة', () => {
        openCreateOrderDrawer(order.id);
      }));
      menu.appendChild(createMenuItem('stethoscope', 'فتح الزيارة المرتبطة', () => {
        showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل', 'info');
      }));
    } else if (order.workflowState === 'documented' || order.workflowState === 'route_ready') {
      menu.appendChild(createMenuItem('stethoscope', 'فتح الزيارة المرتبطة', () => {
        showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل', 'info');
      }));
      menu.appendChild(createDivider());
      const routeItem = createMenuItem('near_me', 'تحديد جهة التنفيذ', () => {
        routingLabOrderId = order.id;
        openDetailsOrderDrawer(order.id);
        const routingBox = document.getElementById('routing-action-box');
        if (routingBox) {
          routingBox.scrollIntoView({ behavior: 'smooth' });
        }
      });
      routeItem.querySelector('.material-symbols-outlined').style.color = '#0495F6';
      routeItem.querySelector('span:last-child').style.color = '#0495F6';
      routeItem.querySelector('span:last-child').style.fontWeight = '700';
      menu.appendChild(routeItem);
    } else {
      // route_requested or cancelled
      menu.appendChild(createMenuItem('stethoscope', 'فتح الزيارة المرتبطة', () => {
        showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل', 'info');
      }));
    }

    positionContextMenu(menu, event.currentTarget);
  }

  function openCreateOrderDrawer(editId = null) {
    const drawer = document.getElementById('drawer-create-order');
    const titleEl = document.getElementById('drawer-create-title');
    const idInput = document.getElementById('form-order-id');
    const form = document.getElementById('lab-order-form');

    form.reset();
    selectedPatientForCreate = null;
    selectedTheraLab = null;
    document.getElementById('patient-search-box').classList.remove('hidden');
    document.getElementById('selected-patient-card').classList.add('hidden');
    document.getElementById('patient-search-input').value = '';
    document.getElementById('thera-network-options').classList.add('hidden');
    document.getElementById('external-provider-input').classList.add('hidden');
    document.getElementById('form-network-empty-state').classList.add('hidden');

    if (editId) {
      const item = ordersList.find(o => o.id === editId);
      if (item) {
        titleEl.textContent = `تعديل طلب المختبر (${item.orderNumber})`;
        idInput.value = item.id;
        selectPatientForCreate({
          id: item.patientId,
          name: item.patientName,
          initials: item.patientInitials,
          age: item.patientAge,
          gender: item.patientGender
        });
        document.getElementById('form-test-name').value = item.testName || '';
        document.getElementById('form-visit').value = item.visitTitle === 'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد' ? 'visit_2024_10_18' : 'none';
        document.getElementById('form-priority').value = item.priority || 'normal';
        window.refreshTheraSelects?.();
        document.getElementById('form-clinical-context').value = item.clinicalContext || '';
        document.getElementById('form-instructions').value = item.instructions || '';

        const radio = document.querySelector(`input[name="destination_mode_choice"][value="${item.destinationMode}"]`);
        if (radio) {
          radio.checked = true;
          if (item.destinationMode === 'thera_network') {
            document.getElementById('thera-network-options').classList.remove('hidden');
            document.getElementById('form-network-lab-search').value = item.laboratoryName || '';
            selectedTheraLab = (item.laboratoryId && item.laboratoryName) ? { id: item.laboratoryId, name: item.laboratoryName } : null;
          } else if (item.destinationMode === 'external') {
            document.getElementById('external-provider-input').classList.remove('hidden');
            document.getElementById('form-external-name').value = item.laboratoryName || '';
          }
        }
      }
    } else {
      titleEl.textContent = 'طلب مختبر جديد';
      idInput.value = '';
    }

    drawer.classList.remove('hidden');
    syncBodyScrollLock();
  }

  function closeCreateOrderDrawer() {
    document.getElementById('drawer-create-order').classList.add('hidden');
    syncBodyScrollLock();
  }

  function handleSaveOrder(targetStatus) {
    if (!selectedPatientForCreate) {
      showToast('يرجى اختيار مريض للطلب', 'warning');
      document.getElementById('patient-search-input')?.focus();
      return;
    }

    const testName = document.getElementById('form-test-name').value.trim();
    if (targetStatus !== 'draft' && !testName) {
      showToast('يرجى كتابة اسم الفحص المطلوب لتوثيق أو إرسال الطلب', 'warning');
      document.getElementById('form-test-name')?.focus();
      return;
    }

    const destinationChoice = document.querySelector('input[name="destination_mode_choice"]:checked')?.value || 'unassigned';
    let laboratoryId = '';
    let laboratoryName = '';

    if (destinationChoice === 'thera_network') {
      if (targetStatus === 'route_requested' && !selectedTheraLab) {
        showToast('اختر مختبرًا من شبكة THERA قبل الإرسال', 'warning');
        return;
      }
      if (selectedTheraLab) {
        laboratoryId = safeString(selectedTheraLab.id);
        laboratoryName = safeString(selectedTheraLab.name);
      }
    }

    if (destinationChoice === 'external') {
      laboratoryName = document.getElementById('form-external-name').value.trim();
      if (targetStatus === 'route_requested' && !laboratoryName) {
        showToast('أدخل اسم المختبر الخارجي قبل الإرسال', 'warning');
        return;
      }
    }

    if (targetStatus === 'route_requested' && destinationChoice === 'unassigned') {
      showToast('لا يمكن إرسال الطلب بدون تحديد جهة التنفيذ', 'warning');
      return;
    }

    const visitValue = document.getElementById('form-visit').value;
    const visitTitle = visitValue === 'visit_2024_10_18'
      ? 'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد'
      : '';
    const priority = document.getElementById('form-priority').value;
    const clinicalContext = document.getElementById('form-clinical-context').value.trim();
    const instructions = document.getElementById('form-instructions').value.trim();
    const editId = document.getElementById('form-order-id').value;

    if (editId) {
      const index = ordersList.findIndex(item => item.id === editId);
      if (index < 0) return;
      const existing = ordersList[index];

      if (existing.workflowState !== 'draft') {
        showToast('يمكن تعديل المسودات فقط', 'warning');
        return;
      }

      const updated = normalizeLabOrder({
        ...existing,
        patientId: selectedPatientForCreate.id,
        patientName: selectedPatientForCreate.name,
        patientInitials: selectedPatientForCreate.initials,
        patientAge: selectedPatientForCreate.age,
        patientGender: selectedPatientForCreate.gender,
        visitTitle,
        testName,
        clinicalContext,
        priority,
        instructions,
        workflowState: targetStatus,
        destinationMode: destinationChoice,
        laboratoryId,
        laboratoryName
      });

      if (!updated) return;
      ordersList[index] = updated;
    } else {
      const now = new Date();
      const patientSuffix = selectedPatientForCreate.id.replace(/\D/g, '');
      const uniqueSuffix = String(Date.now()).slice(-4);
      const orderNumber = `LAB-${patientSuffix}-${uniqueSuffix}`;
      const orderDate = now.toLocaleDateString('ar-EG-u-ca-gregory', {
        day: 'numeric', month: 'long', year: 'numeric'
      });
      const isoDate = now.toISOString().slice(0, 10);

      const newOrder = normalizeLabOrder({
        id: `lab_${Date.now()}`,
        orderNumber,
        patientId: selectedPatientForCreate.id,
        patientName: selectedPatientForCreate.name,
        patientInitials: selectedPatientForCreate.initials,
        patientAge: selectedPatientForCreate.age,
        patientGender: selectedPatientForCreate.gender,
        visitTitle,
        doctorName: 'د. محمد أحمد',
        orderDate,
        isoDate,
        testName,
        clinicalContext,
        priority,
        instructions,
        workflowState: targetStatus,
        destinationMode: destinationChoice,
        laboratoryId,
        laboratoryName,
        resultState: 'none',
        providerReportState: 'unavailable',
        doctorReviewState: 'unavailable'
      });

      if (!newOrder) return;
      ordersList.unshift(newOrder);
    }

    saveOrdersToStorage();
    renderOrders();
    closeCreateOrderDrawer();

    if (targetStatus === 'draft') {
      showToast('تم حفظ مسودة طلب المختبر محليًا على هذا الجهاز', 'info');
    } else if (targetStatus === 'documented') {
      showToast('تم حفظ طلب المختبر محليًا بحالة موثقة', 'info');
    } else {
      showToast('تم تجهيز طلب المختبر للإرسال إلى الجهة المحددة', 'info');
    }
  }

  function openDetailsOrderDrawer(orderId) {
    activeDetailsOrderId = orderId;
    routingLabOrderId = orderId;
    const order = ordersList.find(o => o.id === orderId);
    if (!order) return;

    document.getElementById('details-order-id').textContent = order.orderNumber;
    document.getElementById('details-order-date').textContent = `تاريخ الطلب: ${order.orderDate}`;

    const pBadge = document.getElementById('details-priority-badge');
    pBadge.textContent = order.priority === 'urgent' ? 'عاجل' : 'عادي';
    pBadge.className = order.priority === 'urgent' ? 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-100 text-red-700' : 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700';

    document.getElementById('details-patient-name').textContent = order.patientName;
    document.getElementById('details-patient-id-badge').textContent = order.patientId;
    const detailsPatientMeta = document.getElementById('details-patient-age-gender');
    if (detailsPatientMeta) detailsPatientMeta.textContent = getPatientMeta(order);
    document.getElementById('details-doctor-name').textContent = order.doctorName;

    const sPill = document.getElementById('details-status-pill');
    sPill.textContent = getWorkflowLabel(order.workflowState);
    if (order.workflowState === 'draft') sPill.className = 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-amber-100 text-amber-800';
    else if (order.workflowState === 'documented') sPill.className = 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-blue-100 text-blue-800';
    else if (order.workflowState === 'route_ready') sPill.className = 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-purple-100 text-purple-800';
    else if (order.workflowState === 'route_requested') sPill.className = 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-blue-50 text-blue-700 border border-blue-200';
    else sPill.className = 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-700';

    document.getElementById('details-provider-text').textContent = getProviderDisplayName(order);

    const rPill = document.getElementById('details-result-pill');
    rPill.textContent = getResultStateLabel(order.resultState);
    rPill.className = order.resultState === 'result_ready' ? 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-emerald-100 text-emerald-800' : 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-600';

    const repPill = document.getElementById('details-report-pill');
    repPill.textContent = getReportStateLabel(order.providerReportState);
    repPill.className = order.providerReportState === 'approved' ? 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-emerald-100 text-emerald-800' : 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-600';

    const revPill = document.getElementById('details-review-pill');
    revPill.textContent = getDoctorReviewLabel(order.doctorReviewState);
    revPill.className = order.doctorReviewState === 'reviewed' ? 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-emerald-100 text-emerald-800' : 'inline-block px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-600';

    document.getElementById('details-test-name').textContent = order.testName || '—';
    document.getElementById('details-visit-text').textContent = order.visitTitle || '—';
    document.getElementById('details-clinical-text').textContent = order.clinicalContext || '—';
    document.getElementById('details-instructions-text').textContent = order.instructions || '—';

    // Doctor review condition
    const canReview = (order.resultState === 'result_ready' || order.providerReportState === 'published' || order.providerReportState === 'approved') && order.doctorReviewState !== 'reviewed';
    const docBox = document.getElementById('doctor-review-box');
    if (canReview) {
      docBox.classList.remove('hidden');
    } else {
      docBox.classList.add('hidden');
    }

    // Routing condition
    const routingBox = document.getElementById('routing-action-box');
    if (order.workflowState === 'documented' || order.workflowState === 'route_ready' || (order.workflowState !== 'draft' && order.destinationMode === 'unassigned')) {
      routingBox.classList.remove('hidden');
      const routeModeSelect = document.getElementById('details-route-destination-mode');
      routeModeSelect.value = order.destinationMode;
      window.refreshTheraSelects?.();
      document.getElementById('details-route-network-box').classList.toggle('hidden', order.destinationMode !== 'thera_network');
      document.getElementById('details-route-external-box').classList.toggle('hidden', order.destinationMode !== 'external');
      document.getElementById('details-route-empty-state').classList.add('hidden');
      document.getElementById('details-route-network-search').value = order.laboratoryName || '';
      document.getElementById('details-route-external-input').value = order.destinationMode === 'external' ? (order.laboratoryName || '') : '';
      selectedDetailsTheraLab = (order.destinationMode === 'thera_network' && order.laboratoryId && order.laboratoryName) ? { id: order.laboratoryId, name: order.laboratoryName } : null;
    } else {
      routingBox.classList.add('hidden');
    }

    document.getElementById('drawer-details-order').classList.remove('hidden');
    syncBodyScrollLock();
  }

  function closeDetailsOrderDrawer() {
    document.getElementById('drawer-details-order').classList.add('hidden');
    activeDetailsOrderId = null;
    routingLabOrderId = null;
    syncBodyScrollLock();
  }

  function syncBodyScrollLock() {
    const createDrawer = document.getElementById('drawer-create-order');
    const detailsDrawer = document.getElementById('drawer-details-order');
    const createOpen = createDrawer && !createDrawer.classList.contains('hidden');
    const detailsOpen = detailsDrawer && !detailsDrawer.classList.contains('hidden');
    document.body.classList.toggle('overflow-hidden', Boolean(createOpen || detailsOpen));
  }

  function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-dropdown text-xs font-bold text-white transition-all transform translate-y-2 opacity-0 ';

    if (type === 'success') {
      toast.className += 'bg-slate-900 border-l-4 border-emerald-500';
    } else if (type === 'warning') {
      toast.className += 'bg-slate-900 border-l-4 border-amber-500';
    } else {
      toast.className += 'bg-slate-900 border-l-4 border-brand';
    }

    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined text-lg';
    icon.textContent = type === 'success' ? 'check_circle' : (type === 'warning' ? 'warning' : 'info');

    const text = document.createElement('span');
    text.textContent = message;

    toast.append(icon, text);
    container.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.remove('translate-y-2', 'opacity-0');
    });

    setTimeout(() => {
      toast.classList.add('translate-y-2', 'opacity-0');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }
</script>
<!-- END: Application Scripts -->
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
    const wrapper = document.createElement('div');
    wrapper.className = 'thera-custom-select';
    if(select.id === 'filter-sort' || select.classList.contains('h-8') || select.classList.contains('h-9')) wrapper.classList.add('compact');

    originalParent.insertBefore(wrapper, select);
    wrapper.appendChild(select);
    select.classList.add('thera-native-select-hidden');

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'thera-custom-select-trigger';
    trigger.setAttribute('aria-haspopup','listbox');
    trigger.innerHTML = '<span class="thera-custom-select-label"></span><span class="material-symbols-outlined thera-custom-select-arrow">expand_more</span>';

    const menu = document.createElement('div');
    menu.className = 'thera-custom-select-menu hidden';
    menu.setAttribute('role','listbox');

    wrapper.appendChild(trigger);
    wrapper.appendChild(menu);

    function refresh(){
      const selected = select.options[select.selectedIndex] || select.options[0];
      const label = wrapper.querySelector('.thera-custom-select-label');
      if(label) label.textContent = selected ? selected.textContent.trim() : '';
      menu.replaceChildren();
      Array.from(select.options).forEach(opt=>{
        const b=document.createElement('button');
        b.type='button';
        b.className='thera-custom-select-option';
        if(opt.value===select.value) b.classList.add('active');
        b.setAttribute('role','option');
        b.setAttribute('aria-selected', opt.value===select.value ? 'true':'false');
        const t=document.createElement('span'); t.textContent=opt.textContent.trim();
        const c=document.createElement('span'); c.className='material-symbols-outlined check'; c.textContent='check';
        b.append(t,c);
        b.addEventListener('click',e=>{
          e.stopPropagation();
          if(select.value!==opt.value){
            select.value=opt.value;
            select.dispatchEvent(new Event('input',{bubbles:true}));
            select.dispatchEvent(new Event('change',{bubbles:true}));
          }
          refresh();
          wrapper.classList.remove('open'); menu.classList.add('hidden');
        });
        menu.appendChild(b);
      });
    }

    trigger.addEventListener('click',e=>{
      e.stopPropagation();
      const willOpen=menu.classList.contains('hidden');
      closeAll(willOpen?wrapper:null);
      wrapper.classList.toggle('open',willOpen);
      menu.classList.toggle('hidden',!willOpen);
    });
    select.addEventListener('change',refresh);
    registry.set(select,{wrapper,menu,refresh});
    refresh();
  }

  window.refreshTheraSelects = function(){ registry.forEach(x=>x.refresh()); };
  document.querySelectorAll('select').forEach(build);
  document.addEventListener('click',()=>closeAll());
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeAll(); });
  document.querySelectorAll('form').forEach(f=>f.addEventListener('reset',()=>setTimeout(window.refreshTheraSelects,0)));
})();
</script>@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>