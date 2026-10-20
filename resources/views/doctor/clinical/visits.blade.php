<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الزيارات - THERA</title>
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
<style data-purpose="doctor-visits-page">
  .visits-tab[aria-selected="true"] { background:#0495F6; color:#fff; border-color:#0495F6; }
  .visits-tab[aria-selected="false"] { background:#fff; color:#475569; border-color:#E2E8F0; }
  .visit-row:hover { background:#F8FAFC; }
  .empty-table-cell { padding:52px 24px !important; }
  .drawer-scroll { scrollbar-width:thin; scrollbar-color:#CBD5E1 transparent; }
  .drawer-scroll::-webkit-scrollbar { width:6px; }
  .drawer-scroll::-webkit-scrollbar-thumb { background:#CBD5E1; border-radius:999px; }
  .status-dot { width:7px; height:7px; border-radius:9999px; display:inline-block; }
  .visit-kpi { min-height:92px; }
  .patient-avatar { background:linear-gradient(135deg,#E0F2FE 0%,#EEF2FF 100%); }
  .info-chip { background:#F8FAFC; border:1px solid #E2E8F0; }
  @media (max-width: 767px) {
    #desktop-visits-table { display:none !important; }
    #mobile-visits-list { display:block !important; }
    #visit-details-panel { width:100vw !important; max-width:100vw !important; }
  }
</style><style id="thera-doctor-visits-v2">
html,body{overflow-x:hidden}
#app-sidebar{width:268px!important}
#app-sidebar.sidebar-collapsed{width:72px!important}
#app-sidebar.sidebar-collapsed.sidebar-hovered{width:268px!important}
#top-header{right:268px}
#main-content-layout{margin-right:268px}
#doctor-visits-main{overflow-x:hidden;background:#F6F9FC}

.visits-tab[aria-selected="true"]{
  background:#0495F6!important;color:#fff!important;border-color:#0495F6!important;
  box-shadow:0 1px 3px rgba(4,149,246,.16)
}
.visits-tab[aria-selected="false"]{
  background:#EEF4FF!important;color:#3F4752!important;border-color:transparent!important
}
.visits-tab[aria-selected="false"]:hover{background:#E5EEFF!important}
.visits-tab[aria-selected="true"] .visit-tab-count{background:rgba(255,255,255,.2);color:#fff}
.visits-tab[aria-selected="false"] .visit-tab-count{background:#DDE8FB;color:#3F4752}

.thera-dropdown-wrap{position:relative}
.thera-dropdown-btn{
  width:100%;height:40px;padding:0 12px;border:1px solid #E2E8F0;background:#fff;color:#334155;
  border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:10px;
  font-family:'Tajawal',sans-serif;font-size:12px;font-weight:500;cursor:pointer;
  transition:.18s ease;box-shadow:0 1px 2px rgba(15,23,42,.03)
}
.thera-dropdown-btn:hover{background:#F8FAFC;border-color:#CBD5E1}
.thera-dropdown-btn[aria-expanded="true"]{border-color:#0495F6;box-shadow:0 0 0 3px rgba(4,149,246,.10)}
.thera-dropdown-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:right}
.thera-dropdown-arrow{font-size:17px!important;color:#64748B;transition:transform .18s ease}
.thera-dropdown-btn[aria-expanded="true"] .thera-dropdown-arrow{transform:rotate(180deg)}
.thera-dropdown-menu{
  position:absolute;top:calc(100% + 6px);right:0;width:100%;min-width:190px;padding:6px;background:#fff;
  border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 10px 28px rgba(15,23,42,.12);z-index:170
}
.thera-dropdown-option{
  width:100%;border:0;background:transparent;color:#334155;border-radius:8px;padding:8px 10px;text-align:right;
  font-family:'Tajawal',sans-serif;font-size:12px;font-weight:500;cursor:pointer
}
.thera-dropdown-option:hover{background:#F8FAFC;color:#0B1020}
.thera-dropdown-option.is-selected{background:#F0F7FF;color:#0495F6;font-weight:600}

@media(max-width:1200px){
  #doctor-visits-main{padding-right:12px!important;padding-left:16px!important}
}
</style>
</head>
<body class="select-none bg-[#F6F9FC] text-slate-800 antialiased min-h-screen">
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
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">grid_view</span>
<span class="nav-label">الرئيسية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">calendar_today</span>
<span class="nav-label">المواعيد</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">person_search</span>
<span class="nav-label">المرضى</span>
</a>
</div>
<!-- Group 2: العمل الطبي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">العمل الطبي</p>
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0 text-white">stethoscope</span>
<span class="nav-label">الزيارات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">prescriptions</span>
<span class="nav-label">الوصفات الطبية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label">طلبات المختبر</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">radiology</span>
<span class="nav-label">طلبات الأشعة</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">assignment_turned_in</span>
<span class="nav-label">النتائج</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">sync_alt</span>
<span class="nav-label">الإحالات والمتابعات</span>
</a>
</div>
<!-- Group 3: الإدارة -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">الإدارة</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">receipt_long</span>
<span class="nav-label">الخدمات والأسعار</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">schedule</span>
<span class="nav-label">الدوام والجدول</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">account_balance_wallet</span>
<span class="nav-label">التحصيل والمستحقات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
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
<div class="transition-all duration-300 min-h-screen" id="main-content-layout" style="margin-right:268px;"><header class="fixed top-0 left-0 h-16 bg-white border-b border-[#E2E8F0] z-40 px-6 flex items-center justify-between" data-purpose="top-navigation" id="top-header" style="right:268px;">
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
</header><main class="pt-20 pb-12" data-purpose="workspace-content" id="doctor-visits-main" style="padding-top:72px; padding-right:14px; padding-left:24px; padding-bottom:48px;">
<div class="w-full">
<section class="mt-4 mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
<div>
<nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
<span>العمل الطبي</span>
<span class="material-symbols-outlined text-[14px]">chevron_left</span>
<span class="text-brand font-bold">الزيارات</span>
</nav>
<div class="flex items-center gap-2 flex-wrap">
<h1 class="text-2xl font-extrabold text-slate-900">الزيارات</h1>

</div>
<p class="text-sm text-slate-500 mt-1">متابعة الزيارات السريرية المرتبطة بالمواعيد، وفتح مساحة الزيارة أو مراجعة الزيارات المكتملة.</p>
</div>
<button class="h-10 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg font-bold flex items-center gap-2 text-sm transition-colors shadow-sm" id="open-today-appointments" type="button">
<span class="material-symbols-outlined text-xl">calendar_today</span>
<span>عرض مواعيد اليوم</span>
</button>
</section>
<section aria-label="ملخص الزيارات" class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-5">
<article class="visit-kpi bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center justify-between">
<div>
<p class="text-xs text-slate-500">زيارات اليوم</p>
<p class="text-2xl font-extrabold text-slate-900 mt-1" id="kpi-today">0</p>
</div>
<div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined">event_note</span></div>
</article>
<article class="visit-kpi bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center justify-between">
<div>
<p class="text-xs text-slate-500">قيد الزيارة</p>
<p class="text-2xl font-extrabold text-slate-900 mt-1" id="kpi-active">0</p>
</div>
<div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><span class="material-symbols-outlined">stethoscope</span></div>
</article>
<article class="visit-kpi bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center justify-between">
<div>
<p class="text-xs text-slate-500">مكتملة اليوم</p>
<p class="text-2xl font-extrabold text-slate-900 mt-1" id="kpi-completed">0</p>
</div>
<div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined">task_alt</span></div>
</article>
<article class="visit-kpi bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center justify-between">
<div>
<p class="text-xs text-slate-500">إجمالي السجل المعروض</p>
<p class="text-2xl font-extrabold text-slate-900 mt-1" id="kpi-total">0</p>
</div>
<div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center"><span class="material-symbols-outlined">history</span></div>
</article>
</section>
<section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
<div class="p-4 border-b border-slate-200">
<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
<div aria-label="تصفية الزيارات" class="flex items-center gap-2 flex-wrap" role="tablist">
<button aria-selected="true" class="visits-tab px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2" data-tab="all" type="button"><span>الكل</span><span class="visit-tab-count min-w-[22px] h-[20px] px-1.5 rounded-full inline-flex items-center justify-center text-[11px] font-bold" id="tab-count-all">0</span></button>
<button aria-selected="false" class="visits-tab px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2" data-tab="active" type="button"><span>قيد الزيارة</span><span class="visit-tab-count min-w-[22px] h-[20px] px-1.5 rounded-full inline-flex items-center justify-center text-[11px] font-bold" id="tab-count-active">0</span></button>
<button aria-selected="false" class="visits-tab px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2" data-tab="completed" type="button"><span>المكتملة</span><span class="visit-tab-count min-w-[22px] h-[20px] px-1.5 rounded-full inline-flex items-center justify-center text-[11px] font-bold" id="tab-count-completed">0</span></button>
</div>
<div class="flex flex-wrap items-center gap-2"><div class="relative w-[290px] max-w-full"><span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span><input class="w-full h-10 pr-9 pl-3 bg-white border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-[#0495F6] focus:border-[#0495F6]" id="visits-search" placeholder="ابحث بالمريض أو رقم الموعد أو الزيارة..." type="search"/></div><input class="h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700" id="visits-date-filter" type="date"/><div class="thera-dropdown-wrap w-[190px]"><select class="hidden" id="visits-sort"><option value="newest">الترتيب: الأحدث أولاً</option><option value="oldest">الترتيب: الأقدم أولاً</option></select><button aria-expanded="false" class="thera-dropdown-btn" data-dd-button="visits-sort" type="button"><span class="thera-dropdown-label">الترتيب: الأحدث أولاً</span><span class="material-symbols-outlined thera-dropdown-arrow">expand_more</span></button><div class="thera-dropdown-menu hidden" data-dd-id="visits-sort"><button class="thera-dropdown-option" data-value="newest" type="button">الترتيب: الأحدث أولاً</button><button class="thera-dropdown-option" data-value="oldest" type="button">الترتيب: الأقدم أولاً</button></div></div><button aria-label="إعادة ضبط الفلاتر" class="w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 flex items-center justify-center transition-colors" id="visits-reset-filters" title="إعادة ضبط الفلاتر" type="button"><span class="material-symbols-outlined text-[18px]">restart_alt</span></button></div>
</div>
</div>
<div class="overflow-x-auto" id="desktop-visits-table">
<table class="w-full text-right text-xs min-w-[980px]">
<thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold">
<tr>
<th class="py-3 px-4">المريض</th>
<th class="py-3 px-4">الموعد</th>
<th class="py-3 px-4">التاريخ / الوقت</th>
<th class="py-3 px-4">الشكوى الرئيسية</th>
<th class="py-3 px-4">حالة الزيارة</th>
<th class="py-3 px-4">آخر تحديث</th>
<th class="py-3 px-4 text-center">الإجراء</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100" id="visits-table-body"></tbody>
</table>
</div>
<div class="hidden p-3 space-y-3" id="mobile-visits-list"></div>
</section>
<section class="mt-5 bg-blue-50 border border-blue-100 rounded-xl p-4">
<div class="flex items-start gap-3">
<div class="w-9 h-9 rounded-xl bg-white text-brand border border-blue-100 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-xl">info</span>
</div>
<div>
<p class="text-sm font-extrabold text-slate-900">الزيارة السريرية تبدأ من موعد مرتبط بالمريض</p>
<p class="text-xs text-slate-600 leading-6 mt-1">مساحة الزيارة نفسها تكون صفحة كاملة للتوثيق السريري. هذه الشاشة مخصصة للوصول إلى الزيارات المفتوحة ومراجعة الزيارات السابقة، ولا تنشئ زيارة سريرية منفصلة عن سياق المريض والموعد.</p>
</div>
</div>
</section>
</div>
</main></div>

<div class="fixed bottom-6 left-6 z-[120] flex flex-col gap-2 pointer-events-none" id="toast-container"></div>
<aside class="fixed inset-0 z-[100] hidden" id="visit-details-drawer">
<div class="absolute inset-0 bg-slate-950/35 backdrop-blur-[1px]" data-close-visit-details=""></div>
<section class="absolute top-0 left-0 h-full w-[560px] max-w-[92vw] bg-white shadow-drawer flex flex-col" id="visit-details-panel">
<header class="h-16 px-5 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
<div>
<h2 class="text-base font-extrabold text-slate-900">تفاصيل الزيارة</h2>
<p class="text-xs text-slate-500 mt-0.5" id="drawer-visit-ref">—</p>
</div>
<button class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 flex items-center justify-center" data-close-visit-details="" type="button">
<span class="material-symbols-outlined">close</span>
</button>
</header>
<div class="drawer-scroll flex-1 overflow-y-auto p-5 space-y-5">
<section class="rounded-xl border border-slate-200 bg-slate-50 p-4">
<div class="flex items-center gap-3">
<div class="patient-avatar w-11 h-11 rounded-xl flex items-center justify-center text-brand font-extrabold text-sm" id="drawer-patient-initials">—</div>
<div class="min-w-0">
<p class="text-sm font-extrabold text-slate-900 truncate" id="drawer-patient-name">—</p>
<p class="text-xs text-slate-500 mt-0.5" id="drawer-patient-meta">—</p>
</div>
<div class="mr-auto" id="drawer-status"></div>
</div>
</section>
<section>
<h3 class="text-sm font-extrabold text-slate-900 mb-3">بيانات الزيارة</h3>
<div class="grid grid-cols-2 gap-3">
<div class="info-chip rounded-xl p-3">
<p class="text-[11px] text-slate-500">الموعد المرتبط</p>
<p class="text-sm font-bold text-slate-900 mt-1" id="drawer-appointment">—</p>
</div>
<div class="info-chip rounded-xl p-3">
<p class="text-[11px] text-slate-500">التاريخ والوقت</p>
<p class="text-sm font-bold text-slate-900 mt-1" id="drawer-date-time">—</p>
</div>
</div>
</section>
<section>
<h3 class="text-sm font-extrabold text-slate-900 mb-2">الشكوى الرئيسية</h3>
<div class="rounded-xl border border-slate-200 p-4 text-sm leading-7 text-slate-700" id="drawer-chief-complaint">—</div>
</section>
<section id="drawer-completed-summary-section">
<h3 class="text-sm font-extrabold text-slate-900 mb-2">ملخص الزيارة</h3>
<div class="rounded-xl border border-slate-200 p-4 space-y-3">
<div>
<p class="text-[11px] text-slate-500">التقييم</p>
<p class="text-sm text-slate-800 mt-1 leading-6" id="drawer-assessment">—</p>
</div>
<div>
<p class="text-[11px] text-slate-500">التشخيص / النتيجة السريرية</p>
<p class="text-sm text-slate-800 mt-1 leading-6" id="drawer-diagnosis">—</p>
</div>
</div>
<div class="mt-3 rounded-xl border border-amber-100 bg-amber-50 px-3.5 py-3 text-[11px] text-amber-800 leading-5" data-amendment-note="1">بعد إنهاء الزيارة لا يتم تعديل السجل المكتمل بصمت؛ أي تغيير سريري لاحق يُسجّل كتعديل Amendment/Version مع الاحتفاظ بالنسخة السابقة.</div></section>
<section class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 leading-6">
        عرض التفاصيل السريرية يعتمد على صلاحية الوصول إلى سجل المريض والسياق العلاجي المصرح به.
      </section>
</div>
<footer class="p-4 border-t border-slate-200 flex items-center gap-2 bg-white">
<button class="h-10 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-bold" data-close-visit-details="" type="button">إغلاق</button>
<button class="h-10 px-5 rounded-xl bg-[#0495F6] hover:bg-[#0466F1] text-white text-sm font-bold flex items-center gap-2" id="drawer-open-workspace-btn" type="button">
<span class="material-symbols-outlined text-lg">open_in_new</span>
<span id="drawer-open-workspace-label">فتح مساحة الزيارة</span>
</button>
</footer>
</section>
</aside>

<script id="thera-doctor-visits-runtime">
(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const STORAGE_KEY='thera_doctor_visits_v2';
  const PIN_KEY='thera_sidebar_pinned';

  const defaultVisits = [
    {
      id:'enc_1042',
      visitNo:'VIS-1042',
      appointmentNo:'APT-1042',
      patientId:'pat_1001',
      patientName:'سارة أحمد',
      age:34,
      gender:'أنثى',
      date:'2026-10-04',
      time:'09:30',
      chiefComplaint:'ألم متكرر أعلى البطن منذ عدة أيام.',
      status:'active',
      updatedAt:'2026-10-04T09:48:00',
      assessment:'',
      diagnosis:''
    },
    {
      id:'enc_1038',
      visitNo:'VIS-1038',
      appointmentNo:'APT-1038',
      patientId:'pat_1004',
      patientName:'خالد سمير',
      age:51,
      gender:'ذكر',
      date:'2026-10-04',
      time:'08:45',
      chiefComplaint:'متابعة ضغط الدم ومراجعة القياسات الأخيرة.',
      status:'completed',
      updatedAt:'2026-10-04T09:20:00',
      assessment:'تمت مراجعة الأعراض والقياسات المسجلة ضمن الزيارة.',
      diagnosis:'تم توثيق التقييم السريري في الزيارة.'
    },
    {
      id:'enc_1021',
      visitNo:'VIS-1021',
      appointmentNo:'APT-1021',
      patientId:'pat_1008',
      patientName:'ليان محمود',
      age:28,
      gender:'أنثى',
      date:'2026-10-03',
      time:'15:10',
      chiefComplaint:'مراجعة نتيجة فحص سابق وأعراض إجهاد عام.',
      status:'completed',
      updatedAt:'2026-10-03T15:42:00',
      assessment:'تمت مراجعة السياق السريري والنتائج المتاحة.',
      diagnosis:'تم حفظ ملخص الزيارة ضمن السجل الطبي.'
    },
    {
      id:'enc_1017',
      visitNo:'VIS-1017',
      appointmentNo:'APT-1017',
      patientId:'pat_1011',
      patientName:'أحمد يوسف',
      age:43,
      gender:'ذكر',
      date:'2026-10-02',
      time:'11:00',
      chiefComplaint:'سعال مستمر مع شعور بضيق بسيط عند الجهد.',
      status:'completed',
      updatedAt:'2026-10-02T11:31:00',
      assessment:'تم توثيق التقييم والملاحظات السريرية.',
      diagnosis:'تم حفظ التشخيص السريري في الزيارة.'
    }
  ];

  const clone=v=>JSON.parse(JSON.stringify(v));
  function loadVisits(){
    try{
      const raw=localStorage.getItem(STORAGE_KEY);
      if(!raw)return clone(defaultVisits);
      const p=JSON.parse(raw);
      return Array.isArray(p)?p:clone(defaultVisits);
    }catch(_){return clone(defaultVisits);}
  }

  let visits=loadVisits();
  let activeTab='all';
  let activeVisitId=null;
  let sortOrder='newest';

  const safe=v=>String(v??'').trim();
  const todayIso=()=>new Date().toISOString().slice(0,10);
  const initials=name=>safe(name).split(/\s+/).filter(Boolean).slice(0,2).map(x=>x[0]).join('')||'—';

  function formatDate(date){
    if(!date)return '—';
    try{return new Intl.DateTimeFormat('ar-EG-u-ca-gregory',{day:'2-digit',month:'2-digit',year:'numeric'}).format(new Date(date+'T12:00:00'));}
    catch(_){return date;}
  }

  function formatUpdate(value){
    if(!value)return '—';
    try{return new Intl.DateTimeFormat('ar-EG-u-ca-gregory',{day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'}).format(new Date(value));}
    catch(_){return value;}
  }

  function toast(message,type='info'){
    const tone={info:'bg-slate-800',success:'bg-emerald-600',warning:'bg-amber-500'}[type]||'bg-slate-800';
    const el=document.createElement('div');
    el.className=`${tone} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-bold max-w-sm pointer-events-auto flex items-center gap-2`;
    const i=document.createElement('span');
    i.className='material-symbols-outlined text-[18px]';
    i.textContent=type==='success'?'check_circle':type==='warning'?'warning':'info';
    const t=document.createElement('span');
    t.textContent=message;
    el.append(i,t);
    $('toast-container')?.appendChild(el);
    setTimeout(()=>el.remove(),2800);
  }

  function statusBadge(status){
    const span=document.createElement('span');
    if(status==='active'){
      span.className='inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-violet-200 bg-violet-50 text-violet-700 text-[11px] font-bold';
      span.innerHTML='<span class="status-dot bg-violet-500"></span><span>قيد الزيارة</span>';
    }else{
      span.className='inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 text-[11px] font-bold';
      span.innerHTML='<span class="status-dot bg-emerald-500"></span><span>مكتملة</span>';
    }
    return span;
  }

  function filteredVisits(){
    const q=safe($('visits-search')?.value).toLowerCase();
    const date=$('visits-date-filter')?.value||'';
    const rows=visits.filter(v=>{
      const tabOk=activeTab==='all'||v.status===activeTab;
      const dateOk=!date||v.date===date;
      const hay=`${v.patientName} ${v.appointmentNo} ${v.visitNo} ${v.chiefComplaint}`.toLowerCase();
      return tabOk&&dateOk&&(!q||hay.includes(q));
    });
    rows.sort((a,b)=>{
      const aa=new Date(a.updatedAt||`${a.date}T${a.time||'00:00'}`).getTime();
      const bb=new Date(b.updatedAt||`${b.date}T${b.time||'00:00'}`).getTime();
      return sortOrder==='oldest'?aa-bb:bb-aa;
    });
    return rows;
  }

  function renderCounts(){
    const all=visits.length;
    const active=visits.filter(v=>v.status==='active').length;
    const completed=visits.filter(v=>v.status==='completed').length;
    $('tab-count-all').textContent=String(all);
    $('tab-count-active').textContent=String(active);
    $('tab-count-completed').textContent=String(completed);
  }

  function renderKpis(){
    const today=todayIso();
    $('kpi-today').textContent=String(visits.filter(v=>v.date===today).length);
    $('kpi-active').textContent=String(visits.filter(v=>v.status==='active').length);
    $('kpi-completed').textContent=String(visits.filter(v=>v.status==='completed'&&v.date===today).length);
    $('kpi-total').textContent=String(visits.length);
  }

  function emptyRow(){
    const tr=document.createElement('tr');
    const td=document.createElement('td');
    td.colSpan=7;
    td.className='empty-table-cell text-center';
    td.innerHTML='<div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-2xl">clinical_notes</span></div><p class="text-sm font-extrabold text-slate-800">لا توجد زيارات مطابقة</p><p class="text-xs text-slate-500 mt-1">غيّر البحث أو التاريخ أو التبويب لعرض زيارات أخرى.</p>';
    tr.appendChild(td);
    return tr;
  }

  function render(){
    document.querySelectorAll('.visits-tab').forEach(btn=>btn.setAttribute('aria-selected',String(btn.dataset.tab===activeTab)));
    renderCounts();

    const rows=filteredVisits();
    const body=$('visits-table-body');
    const mobile=$('mobile-visits-list');
    body.replaceChildren();
    mobile.replaceChildren();

    if(!rows.length){
      body.appendChild(emptyRow());
      const card=document.createElement('div');
      card.className='py-10 text-center text-sm text-slate-500';
      card.textContent='لا توجد زيارات مطابقة';
      mobile.appendChild(card);
      renderKpis();
      return;
    }

    rows.forEach(v=>{
      const tr=document.createElement('tr');
      tr.className='visit-row';

      const patient=document.createElement('td');
      patient.className='py-3 px-4';
      patient.innerHTML=`<div class="flex items-center gap-3"><div class="patient-avatar w-9 h-9 rounded-xl flex items-center justify-center text-[#0495F6] font-extrabold text-[11px]">${initials(v.patientName)}</div><div><p class="font-extrabold text-slate-900">${v.patientName}</p><p class="text-[11px] text-slate-500 mt-0.5">${v.gender} · ${v.age} سنة</p></div></div>`;

      const appointment=document.createElement('td');
      appointment.className='py-3 px-4';
      appointment.innerHTML=`<p class="font-bold text-slate-800">${v.appointmentNo}</p><p class="text-[11px] text-slate-500 mt-0.5">${v.visitNo}</p>`;

      const date=document.createElement('td');
      date.className='py-3 px-4';
      date.innerHTML=`<p class="font-bold text-slate-800">${formatDate(v.date)}</p><p class="text-[11px] text-slate-500 mt-0.5">${v.time}</p>`;

      const complaint=document.createElement('td');
      complaint.className='py-3 px-4 max-w-[300px]';
      const cp=document.createElement('p');
      cp.className='text-slate-700 truncate';
      cp.title=v.chiefComplaint;
      cp.textContent=v.chiefComplaint;
      complaint.appendChild(cp);

      const status=document.createElement('td');
      status.className='py-3 px-4';
      status.appendChild(statusBadge(v.status));

      const updated=document.createElement('td');
      updated.className='py-3 px-4 text-slate-500';
      updated.textContent=formatUpdate(v.updatedAt);

      const action=document.createElement('td');
      action.className='py-3 px-4 text-center';
      const btn=document.createElement('button');
      btn.className=v.status==='active'
        ? 'h-9 px-3 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-[11px] font-bold transition-colors'
        : 'h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-[11px] font-bold transition-colors';
      btn.textContent=v.status==='active'?'متابعة الزيارة':'عرض الزيارة';
      btn.dataset.visit=v.id;
      action.appendChild(btn);

      tr.append(patient,appointment,date,complaint,status,updated,action);
      body.appendChild(tr);

      const card=document.createElement('article');
      card.className='border border-slate-200 rounded-xl p-4 bg-white';
      const top=document.createElement('div');
      top.className='flex items-start justify-between gap-3';
      const info=document.createElement('div');
      info.innerHTML=`<div class="flex items-center gap-2"><div class="patient-avatar w-9 h-9 rounded-xl flex items-center justify-center text-[#0495F6] font-extrabold text-[11px]">${initials(v.patientName)}</div><div><p class="text-sm font-extrabold text-slate-900">${v.patientName}</p><p class="text-xs text-slate-500 mt-0.5">${v.appointmentNo} · ${formatDate(v.date)} · ${v.time}</p></div></div>`;
      top.append(info,statusBadge(v.status));

      const complaintBox=document.createElement('div');
      complaintBox.className='mt-3 rounded-lg bg-slate-50 p-3';
      complaintBox.innerHTML=`<p class="text-[11px] text-slate-500">الشكوى الرئيسية</p><p class="text-xs text-slate-700 mt-1 leading-6">${v.chiefComplaint}</p>`;

      const mobileBtn=document.createElement('button');
      mobileBtn.className=v.status==='active'
        ? 'mt-3 w-full h-9 rounded-lg bg-[#0495F6] text-white text-xs font-bold'
        : 'mt-3 w-full h-9 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-bold';
      mobileBtn.textContent=v.status==='active'?'متابعة الزيارة':'عرض الزيارة';
      mobileBtn.dataset.visit=v.id;

      card.append(top,complaintBox,mobileBtn);
      mobile.appendChild(card);
    });

    renderKpis();
  }

  function openDetails(id){
    const v=visits.find(x=>x.id===id);
    if(!v)return;
    activeVisitId=id;
    $('drawer-visit-ref').textContent=`${v.visitNo} · ${v.appointmentNo}`;
    $('drawer-patient-initials').textContent=initials(v.patientName);
    $('drawer-patient-name').textContent=v.patientName;
    $('drawer-patient-meta').textContent=`${v.gender} · ${v.age} سنة`;
    const statusHost=$('drawer-status');
    statusHost.replaceChildren();
    statusHost.appendChild(statusBadge(v.status));
    $('drawer-appointment').textContent=v.appointmentNo;
    $('drawer-date-time').textContent=`${formatDate(v.date)} · ${v.time}`;
    $('drawer-chief-complaint').textContent=v.chiefComplaint||'—';
    $('drawer-assessment').textContent=v.assessment||'لم يتم إكمال التقييم بعد.';
    $('drawer-diagnosis').textContent=v.diagnosis||'لم يتم إكمال التشخيص بعد.';
    $('drawer-completed-summary-section').classList.toggle('hidden',v.status!=='completed');
    $('drawer-open-workspace-label').textContent=v.status==='active'?'متابعة الزيارة':'عرض الزيارة';
    $('visit-details-drawer').classList.remove('hidden');
    document.body.style.overflow='hidden';
  }

  function closeDetails(){
    $('visit-details-drawer').classList.add('hidden');
    document.body.style.overflow='';
    activeVisitId=null;
  }

  document.querySelectorAll('.visits-tab').forEach(btn=>btn.addEventListener('click',()=>{
    activeTab=btn.dataset.tab;
    render();
  }));

  $('visits-search')?.addEventListener('input',render);
  $('visits-date-filter')?.addEventListener('change',render);

  $('visits-reset-filters')?.addEventListener('click',()=>{
    $('visits-search').value='';
    $('visits-date-filter').value='';
    sortOrder='newest';
    $('visits-sort').value='newest';
    const label=document.querySelector('[data-dd-button="visits-sort"] .thera-dropdown-label');
    if(label)label.textContent='الترتيب: الأحدث أولاً';
    document.querySelectorAll('.thera-dropdown-option').forEach(x=>x.classList.toggle('is-selected',x.dataset.value==='newest'));
    activeTab='all';
    render();
    toast('تمت إعادة ضبط فلاتر الزيارات','info');
  });

  const ddBtn=document.querySelector('[data-dd-button="visits-sort"]');
  const ddMenu=document.querySelector('[data-dd-id="visits-sort"]');

  ddBtn?.addEventListener('click',e=>{
    e.stopPropagation();
    const opening=ddMenu.classList.contains('hidden');
    ddMenu.classList.toggle('hidden',!opening);
    ddBtn.setAttribute('aria-expanded',opening?'true':'false');
  });

  ddMenu?.addEventListener('click',e=>{
    const opt=e.target.closest('.thera-dropdown-option');
    if(!opt)return;
    sortOrder=opt.dataset.value;
    $('visits-sort').value=sortOrder;
    ddBtn.querySelector('.thera-dropdown-label').textContent=opt.textContent;
    ddMenu.querySelectorAll('.thera-dropdown-option').forEach(x=>x.classList.toggle('is-selected',x===opt));
    ddMenu.classList.add('hidden');
    ddBtn.setAttribute('aria-expanded','false');
    render();
  });

  $('open-today-appointments')?.addEventListener('click',()=>{
    window.location.href='THERA_Doctor_Appointments_Unified_v5.html?view=day';
  });

  document.addEventListener('click',e=>{
    const target=e.target.closest('[data-visit]');
    if(target)openDetails(target.dataset.visit);

    if(!e.target.closest('.thera-dropdown-wrap')){
      ddMenu?.classList.add('hidden');
      ddBtn?.setAttribute('aria-expanded','false');
    }
  });

  document.querySelectorAll('[data-close-visit-details]').forEach(el=>el.addEventListener('click',closeDetails));

  $('drawer-open-workspace-btn')?.addEventListener('click',()=>{
    const v=visits.find(x=>x.id===activeVisitId);
    if(!v)return;
    const mode=v.status==='active'?'active':'readonly';
    window.location.href=`THERA_Clinical_Visit_Interactive_v2.html?visit=${encodeURIComponent(v.id)}&mode=${mode}`;
  });

  const sidebar=$('app-sidebar');
  const top=$('top-header');
  const layout=$('main-content-layout');

  function applyShell(collapsed){
    if(!sidebar||!top||!layout)return;
    sidebar.classList.toggle('sidebar-collapsed',collapsed);
    sidebar.classList.remove('sidebar-hovered');
    top.style.right=collapsed?'72px':'268px';
    layout.style.marginRight=collapsed?'72px':'268px';
  }

  applyShell(localStorage.getItem(PIN_KEY)==='false');

  window.toggleSidebarPin=function(){
    const collapsed=!sidebar.classList.contains('sidebar-collapsed');
    localStorage.setItem(PIN_KEY,collapsed?'false':'true');
    applyShell(collapsed);
  };

  $('sidebar-pin-toggle')?.addEventListener('click',window.toggleSidebarPin);
  sidebar?.addEventListener('mouseenter',()=>{if(sidebar.classList.contains('sidebar-collapsed'))sidebar.classList.add('sidebar-hovered');});
  sidebar?.addEventListener('mouseleave',()=>sidebar.classList.remove('sidebar-hovered'));

  window.toggleDoctorMenu=function(){$('doctor-account-menu')?.classList.toggle('hidden');};

  let qTimer=null;
  window.handleQuickAddEnter=function(){
    if(qTimer)clearTimeout(qTimer);
    $('quick-add-dropdown')?.classList.remove('hidden');
    if($('quick-add-arrow'))$('quick-add-arrow').textContent='expand_less';
  };
  window.handleQuickAddLeave=function(){qTimer=setTimeout(window.closeQuickAddMenu,180);};
  window.toggleQuickAddMenu=function(e){
    e?.stopPropagation();
    const q=$('quick-add-dropdown');
    if(!q)return;
    const hidden=q.classList.toggle('hidden');
    if($('quick-add-arrow'))$('quick-add-arrow').textContent=hidden?'expand_more':'expand_less';
  };
  window.closeQuickAddMenu=function(){
    $('quick-add-dropdown')?.classList.add('hidden');
    if($('quick-add-arrow'))$('quick-add-arrow').textContent='expand_more';
  };

  document.addEventListener('click',e=>{
    const q=$('quick-add-container');
    if(q&&!q.contains(e.target))window.closeQuickAddMenu();
    const doctorMenu=$('doctor-account-menu');
    const doctorCard=document.querySelector('.sidebar-doctor-card');
    if(doctorMenu&&!doctorMenu.classList.contains('hidden')&&!doctorMenu.contains(e.target)&&!(doctorCard&&doctorCard.contains(e.target))){
      doctorMenu.classList.add('hidden');
    }
  });

  document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){
      ddMenu?.classList.add('hidden');
      ddBtn?.setAttribute('aria-expanded','false');
      if(!$('visit-details-drawer')?.classList.contains('hidden'))closeDetails();
    }
    if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){
      const search=top?.querySelector('input[type="text"]');
      if(search){e.preventDefault();search.focus();}
    }
  });

  render();
})();
</script>@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>
