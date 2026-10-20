<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الإعدادات - THERA</title>
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
<style data-purpose="doctor-settings-page">
  .settings-nav-btn[aria-selected="true"] { background:#EFF6FF; color:#0495F6; border-color:#BFDBFE; font-weight:800; }
  .settings-nav-btn[aria-selected="false"] { background:transparent; color:#475569; border-color:transparent; }
  .settings-nav-btn:hover { background:#F8FAFC; }
  .settings-section { display:none; }
  .settings-section.active { display:block; }
  .setting-row + .setting-row { border-top:1px solid #E2E8F0; }
  .toggle-track { width:42px; height:24px; border-radius:9999px; background:#CBD5E1; position:relative; transition:.2s ease; flex-shrink:0; }
  .toggle-track::after { content:""; width:18px; height:18px; border-radius:9999px; background:#fff; position:absolute; top:3px; right:3px; transition:.2s ease; box-shadow:0 1px 2px rgba(15,23,42,.18); }
  input.peer:checked + .toggle-track { background:#0495F6; }
  input.peer:checked + .toggle-track::after { transform:translateX(-18px); }
  .payment-choice input:checked + div { border-color:#0495F6; background:#EFF6FF; box-shadow:0 0 0 1px #0495F6 inset; }
  .compact-field { height:42px; }
  @media (max-width: 900px) {
    .settings-shell { grid-template-columns:1fr !important; }
    .settings-local-nav { display:flex; overflow-x:auto; padding-bottom:4px; border-left:0 !important; border-bottom:1px solid #E2E8F0; }
    .settings-nav-btn { min-width:max-content; }
  }
</style>
<style id="thera-settings-unified">
  html, body { overflow-x:hidden; }

  .thera-dropdown-wrap { position:relative; }
  .thera-dropdown-btn {
    width:100%; height:42px; padding:0 12px;
    border:1px solid #E2E8F0; background:#fff; color:#334155;
    border-radius:10px; display:flex; align-items:center; justify-content:space-between;
    gap:10px; font-family:'Tajawal',sans-serif; font-size:13px; font-weight:500;
    box-shadow:0 1px 2px rgba(15,23,42,.03); transition:.18s ease; cursor:pointer;
  }
  .thera-dropdown-btn:hover { background:#F8FAFC; border-color:#CBD5E1; }
  .thera-dropdown-btn[aria-expanded="true"],
  .thera-dropdown-btn:focus-visible {
    outline:none; border-color:#0495F6; box-shadow:0 0 0 3px rgba(4,149,246,.10);
  }
  .thera-dropdown-label { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; text-align:right; }
  .thera-dropdown-arrow { font-size:17px!important; color:#64748B; transition:transform .18s ease; flex:none; }
  .thera-dropdown-btn[aria-expanded="true"] .thera-dropdown-arrow { transform:rotate(180deg); }
  .thera-dropdown-menu {
    position:absolute; top:calc(100% + 6px); right:0; width:100%; min-width:180px;
    padding:6px; background:#fff; border:1px solid #E2E8F0; border-radius:12px;
    box-shadow:0 10px 28px rgba(15,23,42,.12); z-index:160;
  }
  .thera-dropdown-option {
    width:100%; border:0; background:transparent; color:#334155; border-radius:8px;
    padding:8px 10px; text-align:right; font-family:'Tajawal',sans-serif;
    font-size:12px; font-weight:500; line-height:18px; cursor:pointer;
  }
  .thera-dropdown-option:hover { background:#F8FAFC; color:#0B1020; }
  .thera-dropdown-option.is-selected { background:#F0F7FF; color:#0495F6; font-weight:600; }

  @media (max-width: 900px) {
    .settings-shell { grid-template-columns:1fr!important; }
    .settings-local-nav {
      border-left:0!important; border-bottom:1px solid #E2E8F0;
      display:flex; overflow-x:auto; gap:6px;
    }
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
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">stethoscope</span>
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
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0 text-white">settings</span>
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


<main class="pt-16 p-6 min-h-screen" data-purpose="workspace-content" id="main-content-layout">
  <div class="max-w-[1500px] mx-auto">
    <section class="mt-4 mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
          <span>الإدارة</span>
          <span class="material-symbols-outlined text-[14px]">chevron_left</span>
          <span class="text-brand font-bold">الإعدادات</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-slate-900">الإعدادات</h1>
        <p class="text-sm text-slate-500 mt-1">إدارة تفضيلات حساب الطبيب، الملف المهني، حالة التوفر، الإشعارات، وسياسة الحجز والدفع.</p>
      </div>
      <div class="flex items-center gap-2">
        <button class="h-10 px-5 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold flex items-center gap-2 text-sm shadow-sm transition-colors" id="save-settings-btn" type="button">
          <span class="material-symbols-outlined text-lg">save</span>
          <span>حفظ الإعدادات</span>
        </button>
      </div>
    </section>

    <section class="settings-shell bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden grid grid-cols-[245px_minmax(0,1fr)] min-h-[680px]">
      <nav class="settings-local-nav border-l border-slate-200 p-3 space-y-1 bg-slate-50/60" aria-label="أقسام الإعدادات">
        <button class="settings-nav-btn w-full h-11 px-3 rounded-xl border flex items-center gap-3 text-sm text-right transition-colors" data-section="general" aria-selected="true" type="button">
          <span class="material-symbols-outlined text-[20px]">tune</span><span>الإعدادات العامة</span>
        </button>
        <button class="settings-nav-btn w-full h-11 px-3 rounded-xl border flex items-center gap-3 text-sm text-right transition-colors" data-section="profile" aria-selected="false" type="button">
          <span class="material-symbols-outlined text-[20px]">badge</span><span>الملف المهني والتحقق</span>
        </button>
        <button class="settings-nav-btn w-full h-11 px-3 rounded-xl border flex items-center gap-3 text-sm text-right transition-colors" data-section="availability" aria-selected="false" type="button">
          <span class="material-symbols-outlined text-[20px]">radio_button_checked</span><span>حالة التوفر</span>
        </button>
        <button class="settings-nav-btn w-full h-11 px-3 rounded-xl border flex items-center gap-3 text-sm text-right transition-colors" data-section="notifications" aria-selected="false" type="button">
          <span class="material-symbols-outlined text-[20px]">notifications</span><span>الإشعارات</span>
        </button>
        <button class="settings-nav-btn w-full h-11 px-3 rounded-xl border flex items-center gap-3 text-sm text-right transition-colors" data-section="payment" aria-selected="false" type="button">
          <span class="material-symbols-outlined text-[20px]">payments</span><span>سياسة الحجز والدفع</span>
        </button>
      </nav>

      <div class="p-5 lg:p-7">
        <section class="settings-section active" id="section-general">
          <div class="mb-5">
            <h2 class="text-lg font-extrabold text-slate-900">الإعدادات العامة</h2>
            <p class="text-xs text-slate-500 mt-1">تفضيلات واجهة الطبيب وسياق المنشأة المستخدم في المساحة الحالية.</p>
          </div>
          <div class="border border-slate-200 rounded-xl overflow-hidden">
            <div class="setting-row p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div><p class="text-sm font-bold text-slate-900">المنشأة الحالية</p><p class="text-xs text-slate-500 mt-1">تستخدم كسياق تشغيلي عند العمل داخل THERA.</p></div>
              <select class="rtl-select compact-field min-w-[250px] bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700" id="setting-facility"><option value="facility_main">العيادة الرئيسية</option></select>
            </div>
            <div class="setting-row p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div><p class="text-sm font-bold text-slate-900">لغة الواجهة</p><p class="text-xs text-slate-500 mt-1">يمكن التبديل أيضًا من الشريط العلوي.</p></div>
              <select class="rtl-select compact-field min-w-[180px] bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700" id="setting-language"><option value="ar">العربية</option><option value="en">English</option></select>
            </div>
            <div class="setting-row p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div><p class="text-sm font-bold text-slate-900">مظهر الواجهة</p><p class="text-xs text-slate-500 mt-1">يؤثر على واجهة الحساب فقط.</p></div>
              <select class="rtl-select compact-field min-w-[180px] bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700" id="setting-theme"><option value="light">فاتح</option><option value="dark">داكن</option><option value="system">حسب الجهاز</option></select>
            </div>
          </div>
        </section>

        <section class="settings-section" id="section-profile">
          <div class="mb-5">
            <h2 class="text-lg font-extrabold text-slate-900">الملف المهني والتحقق</h2>
            <p class="text-xs text-slate-500 mt-1">ملخص حالة ملف الطبيب والبيانات المهنية المرتبطة به.</p>
          </div>
          <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-emerald-600">verified</span>
            <div><p class="text-sm font-extrabold text-emerald-900">تم التحقق من الطبيب</p><p class="text-xs text-emerald-800 mt-1">حالة المعاينة الحالية: Verified / Active.</p></div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
            <div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-500">الاسم</p><p class="text-sm font-bold text-slate-900 mt-1">د. محمد أحمد</p></div>
            <div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-500">التخصص</p><p class="text-sm font-bold text-slate-900 mt-1">استشاري طب باطني</p></div>
            <div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-500">المنشآت المرتبطة</p><p class="text-sm font-bold text-slate-900 mt-1">1 منشأة</p></div>
            <div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-500">الخدمات</p><p class="text-sm font-bold text-slate-900 mt-1">الاستشارة، المتابعة</p></div>
          </div>
          <div class="border border-slate-200 rounded-xl divide-y divide-slate-200">
            <div class="p-4 flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-slate-900">الملف الشخصي والمعلومات المهنية</p><p class="text-xs text-slate-500 mt-1">البيانات الشخصية والمهنية والتخصصات.</p></div><button class="h-9 px-3 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50" data-placeholder-action="profile" type="button">إدارة الملف المهني</button></div>
            <div class="p-4 flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-slate-900">التحقق المهني</p><p class="text-xs text-slate-500 mt-1">حالة التحقق والوثائق المهنية المطلوبة.</p></div><button class="h-9 px-3 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50" data-placeholder-action="credentials" type="button">إدارة التحقق</button></div>
            <div class="p-4 flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-slate-900">الإنجازات المهنية</p><p class="text-xs text-slate-500 mt-1">الإنجازات والمعلومات المهنية المنشورة في الملف.</p></div><button class="h-9 px-3 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50" data-placeholder-action="achievements" type="button">إدارة الإنجازات</button></div>
          </div>
        </section>

        <section class="settings-section" id="section-availability">
          <div class="mb-5"><h2 class="text-lg font-extrabold text-slate-900">حالة التوفر</h2><p class="text-xs text-slate-500 mt-1">حالة تشغيلية سريعة للطبيب، ولا تستبدل جدول الدوام أو سعة حجوزات THERA.</p></div>
          <div class="border border-slate-200 rounded-xl p-5 max-w-2xl">
            <div class="flex items-start gap-3 mb-5"><div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined">radio_button_checked</span></div><div><p class="text-sm font-extrabold text-slate-900">الحالة الحالية</p><p class="text-xs text-slate-500 mt-1" id="availability-current-label">متاح الآن</p></div></div>
            <label class="block text-xs font-bold text-slate-700 mb-2" for="setting-availability">تغيير حالة التوفر</label>
            <select class="rtl-select compact-field w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700" id="setting-availability"><option value="available">متاح الآن</option><option value="busy">مشغول مؤقتًا</option><option value="unavailable">غير متاح مؤقتًا</option></select>
            <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-3 text-xs text-blue-800 leading-6">تغيير هذه الحالة لا يعدّل أيام الدوام أو الفترات الزمنية أو الاستثناءات. إدارة الجدول تبقى من صفحة «الدوام والجدول».</div>
          </div>
        </section>

        <section class="settings-section" id="section-notifications">
          <div class="mb-5"><h2 class="text-lg font-extrabold text-slate-900">الإشعارات</h2><p class="text-xs text-slate-500 mt-1">حدد أنواع التنبيهات التشغيلية التي تريد متابعتها في مساحة الطبيب.</p></div>
          <div class="border border-slate-200 rounded-xl overflow-hidden max-w-3xl" id="notification-settings">
            <div class="setting-row p-4 flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-slate-900">الحجوزات وتحديثات المواعيد</p><p class="text-xs text-slate-500 mt-1">الحجز، الإلغاء وإعادة الجدولة.</p></div><label class="cursor-pointer"><input class="peer sr-only notification-toggle" data-key="appointments" type="checkbox"><span class="toggle-track block"></span></label></div>
            <div class="setting-row p-4 flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-slate-900">وصول المريض</p><p class="text-xs text-slate-500 mt-1">تنبيه عند تسجيل الوصول والانتظار.</p></div><label class="cursor-pointer"><input class="peer sr-only notification-toggle" data-key="arrival" type="checkbox"><span class="toggle-track block"></span></label></div>
            <div class="setting-row p-4 flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-slate-900">جاهزية النتائج</p><p class="text-xs text-slate-500 mt-1">نتائج المختبر وتقارير الأشعة الجاهزة للمراجعة.</p></div><label class="cursor-pointer"><input class="peer sr-only notification-toggle" data-key="results" type="checkbox"><span class="toggle-track block"></span></label></div>
            <div class="setting-row p-4 flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-slate-900">الإحالات والمتابعات</p><p class="text-xs text-slate-500 mt-1">التغييرات المرتبطة بالإحالة والمتابعة.</p></div><label class="cursor-pointer"><input class="peer sr-only notification-toggle" data-key="referrals" type="checkbox"><span class="toggle-track block"></span></label></div>
            <div class="setting-row p-4 flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-slate-900">الدفع والتحصيل</p><p class="text-xs text-slate-500 mt-1">التنبيهات المتعلقة بالدفع والتحصيل عند توفرها.</p></div><label class="cursor-pointer"><input class="peer sr-only notification-toggle" data-key="payments" type="checkbox"><span class="toggle-track block"></span></label></div>
          </div>
        </section>

        <section class="settings-section" id="section-payment">
          <div class="mb-5">
            <h2 class="text-lg font-extrabold text-slate-900">سياسة الحجز والدفع</h2>
            <p class="text-xs text-slate-500 mt-1">السياسة الافتراضية للطبيب. يمكن لكل خدمة استخدام هذه السياسة أو تعيين سياسة خاصة بها.</p>
          </div>

          <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 mb-4 max-w-3xl">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#0495F6]">info</span>
              <div>
                <p class="text-sm font-extrabold text-blue-950">سياسة الطبيب هي الـ Default</p>
                <p class="text-xs text-blue-800 leading-6 mt-1">يمكن تعديلها على مستوى خدمة محددة من صفحة «الخدمات والأسعار». عند إنشاء الحجز تُحفظ نسخة Snapshot من السعر وسياسة الدفع، لذلك لا تتأثر الحجوزات السابقة بأي تعديل لاحق.</p>
              </div>
            </div>
          </div>

          <div class="border border-slate-200 rounded-xl overflow-hidden max-w-3xl">
            <div class="setting-row p-4">
              <p class="text-sm font-bold text-slate-900">المبلغ المطلوب عند الحجز</p>
              <p class="text-xs text-slate-500 mt-1 mb-3">حدد السياسة الافتراضية للحجوزات الجديدة.</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment-policy-type" value="none" type="radio">
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">بدون دفعة مقدمة</p>
                    <p class="text-[11px] text-slate-500 mt-1">لا يطلب دفع عند إنشاء الحجز.</p>
                  </div>
                </label>
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment-policy-type" value="percentage" type="radio">
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">دفعة مقدمة بنسبة</p>
                    <p class="text-[11px] text-slate-500 mt-1">يحدد الطبيب النسبة المطلوبة.</p>
                  </div>
                </label>
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment-policy-type" value="full" type="radio">
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">دفع كامل 100%</p>
                    <p class="text-[11px] text-slate-500 mt-1">يطلب سعر الخدمة كاملًا عند الحجز.</p>
                  </div>
                </label>
              </div>
            </div>

            <div class="setting-row p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3" id="deposit-percent-row">
              <div>
                <p class="text-sm font-bold text-slate-900">نسبة الدفعة المقدمة</p>
                <p class="text-xs text-slate-500 mt-1">تستخدم فقط عند اختيار «دفعة مقدمة بنسبة».</p>
              </div>
              <div class="relative w-full md:w-[180px]">
                <input class="compact-field w-full pl-10 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900" id="deposit-percent" min="1" max="99" step="1" type="number">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-bold">%</span>
              </div>
            </div>

            <div class="setting-row p-4">
              <p class="text-sm font-bold text-slate-900">موعد دفع الرصيد المتبقي</p>
              <p class="text-xs text-slate-500 mt-1 mb-3">ينطبق عندما يبقى جزء غير مدفوع من قيمة الخدمة.</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="remaining-timing" value="before_visit" type="radio"><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">قبل الزيارة</p><p class="text-[11px] text-slate-500 mt-1">يجب إكمال الرصيد قبل بدء الزيارة.</p></div></label>
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="remaining-timing" value="at_arrival" type="radio"><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">عند الوصول</p><p class="text-[11px] text-slate-500 mt-1">يتم تحصيله عند الاستقبال.</p></div></label>
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="remaining-timing" value="after_visit" type="radio"><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">بعد الزيارة</p><p class="text-[11px] text-slate-500 mt-1">يسمح بإكمال الرصيد بعد انتهاء الزيارة.</p></div></label>
              </div>
            </div>

            <div class="setting-row p-4 flex items-center justify-between gap-4" id="early-payment-row">
              <div>
                <p class="text-sm font-bold text-slate-900">السماح بالدفع المبكر للرصيد</p>
                <p class="text-xs text-slate-500 mt-1">يسمح للمريض بإكمال المبلغ المتبقي قبل الموعد المحدد للدفع.</p>
              </div>
              <label class="cursor-pointer">
                <input class="peer sr-only" id="allow-early-payment" type="checkbox">
                <span class="toggle-track block"></span>
              </label>
            </div>
          </div>
          <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 max-w-3xl">
            <p class="text-sm font-extrabold text-blue-900">مثال معاينة</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-xs">
              <div><span class="text-blue-700">سعر الخدمة</span><p class="font-extrabold text-blue-950 mt-1">100.00</p></div>
              <div><span class="text-blue-700">المطلوب عند الحجز</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-deposit">10.00</p></div>
              <div><span class="text-blue-700">المتبقي</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-remaining">90.00</p></div>
              <div><span class="text-blue-700">موعد المتبقي</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-timing">قبل الزيارة</p></div>
            </div>
          </div>
        </section>
      </div>
    </section>
  </div>
</main>
<div class="fixed bottom-6 left-6 z-[120] flex flex-col gap-2 pointer-events-none" id="toast-container"></div>
<script>
(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const STORAGE_KEY = 'thera_doctor_settings_v1';
  const SIDEBAR_KEY = 'thera_doctor_sidebar_pinned';
  const defaults = {
    facility:'facility_main', language:'ar', theme:'light', availability:'available',
    notifications:{appointments:true,arrival:true,results:true,referrals:true,payments:true},
    payment:{policyType:'percentage',depositPercent:30,remainingTiming:'before_visit',allowEarlyPayment:true}
  };
  const clone = x => JSON.parse(JSON.stringify(x));
  function load(){
      try{
        const raw=localStorage.getItem(STORAGE_KEY);
        if(!raw)return clone(defaults);
        const p=JSON.parse(raw);
        const payment={...defaults.payment,...(p.payment||{})};
        if(!payment.policyType && typeof payment.depositEnabled==='boolean'){
          payment.policyType = payment.depositEnabled ? (Number(payment.depositPercent)===100?'full':'percentage') : 'none';
        }
        delete payment.depositEnabled;
        return {...clone(defaults),...p,notifications:{...defaults.notifications,...(p.notifications||{})},payment};
      }catch(_){ return clone(defaults); }
    }
  let state=load(); let dirty=false;
  function toast(msg,type='info'){ const colors={success:'bg-emerald-600',warning:'bg-amber-500',info:'bg-slate-800'}; const el=document.createElement('div'); el.className=`${colors[type]||colors.info} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-bold pointer-events-auto flex items-center gap-2`; const ic=document.createElement('span'); ic.className='material-symbols-outlined text-lg'; ic.textContent=type==='success'?'check_circle':type==='warning'?'warning':'info'; const tx=document.createElement('span'); tx.textContent=msg; el.append(ic,tx); $('toast-container').appendChild(el); setTimeout(()=>el.remove(),2800); }
  function setDirty(v=true){ dirty=v; }
  function sync(){
    $('setting-facility').value=state.facility; $('setting-language').value=state.language; $('setting-theme').value=state.theme; $('setting-availability').value=state.availability;
    document.querySelectorAll('.notification-toggle').forEach(x=>x.checked=!!state.notifications[x.dataset.key]);
    const policyRadio=document.querySelector(`input[name="payment-policy-type"][value="${state.payment.policyType}"]`);
    if(policyRadio) policyRadio.checked=true;
    $('deposit-percent').value=String(state.payment.depositPercent);
    $('allow-early-payment').checked=state.payment.allowEarlyPayment!==false;
    const radio=document.querySelector(`input[name="remaining-timing"][value="${state.payment.remainingTiming}"]`); if(radio)radio.checked=true;
    updateAvailability(); updatePaymentUI();
  }
  function updateAvailability(){ const map={available:'متاح الآن',busy:'مشغول مؤقتًا',unavailable:'غير متاح مؤقتًا'}; $('availability-current-label').textContent=map[$('setting-availability').value]||'—'; }
  function updatePaymentUI(){
      const policy=document.querySelector('input[name="payment-policy-type"]:checked')?.value||'percentage';
      const percentageMode=policy==='percentage';
      $('deposit-percent').disabled=!percentageMode;
      $('deposit-percent-row').classList.toggle('opacity-50',!percentageMode);
      const pct=policy==='none'?0:policy==='full'?100:Math.min(99,Math.max(1,Number($('deposit-percent').value)||1));
      const dep=pct;
      $('payment-example-deposit').textContent=dep.toFixed(2);
      $('payment-example-remaining').textContent=(100-dep).toFixed(2);
      const timing=document.querySelector('input[name="remaining-timing"]:checked')?.value||'before_visit';
      $('payment-example-timing').textContent=policy==='full'?'مدفوع بالكامل':{before_visit:'قبل الزيارة',at_arrival:'عند الوصول',after_visit:'بعد الزيارة'}[timing];
      document.querySelectorAll('input[name="remaining-timing"]').forEach(r=>r.disabled=policy==='full');
      const timingBlock=document.querySelector('input[name="remaining-timing"]')?.closest('.setting-row');
      if(timingBlock) timingBlock.classList.toggle('opacity-50',policy==='full');
      $('allow-early-payment').disabled=policy==='full';
      $('early-payment-row').classList.toggle('opacity-50',policy==='full');
    }
  function collect(){
      state.facility=$('setting-facility').value;
      state.language=$('setting-language').value;
      state.theme=$('setting-theme').value;
      state.availability=$('setting-availability').value;
      document.querySelectorAll('.notification-toggle').forEach(x=>state.notifications[x.dataset.key]=x.checked);
      state.payment.policyType=document.querySelector('input[name="payment-policy-type"]:checked')?.value||'percentage';
      state.payment.depositPercent=Math.min(99,Math.max(1,Number($('deposit-percent').value)||1));
      state.payment.remainingTiming=document.querySelector('input[name="remaining-timing"]:checked')?.value||'before_visit';
      state.payment.allowEarlyPayment=$('allow-early-payment').checked;
    }
  function save(){ collect(); try{localStorage.setItem(STORAGE_KEY,JSON.stringify(state));dirty=false;toast('تم حفظ الإعدادات','success');}catch(_){toast('تعذر حفظ الإعدادات محليًا','warning');} }
  function switchSection(name){ document.querySelectorAll('.settings-section').forEach(s=>s.classList.toggle('active',s.id===`section-${name}`)); document.querySelectorAll('.settings-nav-btn').forEach(b=>b.setAttribute('aria-selected',String(b.dataset.section===name))); }
  document.querySelectorAll('.settings-nav-btn').forEach(b=>b.addEventListener('click',()=>switchSection(b.dataset.section)));
  ['setting-facility','setting-language','setting-theme','setting-availability','deposit-percent'].forEach(id=>$(id).addEventListener('change',()=>{setDirty(); if(id==='setting-availability')updateAvailability(); if(id==='deposit-percent')updatePaymentUI();}));
  document.querySelectorAll('.notification-toggle,input[name="remaining-timing"],input[name="payment-policy-type"]').forEach(x=>x.addEventListener('change',()=>{setDirty();updatePaymentUI();}));
  $('allow-early-payment').addEventListener('change',()=>{setDirty();updatePaymentUI();});
  $('save-settings-btn').addEventListener('click',save);
  document.querySelectorAll('[data-placeholder-action]').forEach(b=>b.addEventListener('click',()=>{
      const action=b.dataset.placeholderAction;
      const msg=action==='profile'?'فتح شاشة الملف المهني':action==='credentials'?'فتح شاشة التحقق المهني':action==='achievements'?'فتح شاشة الملف المهني — الإنجازات':'فتح الشاشة المرتبطة';
      toast(msg,'info');
    }));

  function setupShell(){
    const sidebar=$('app-sidebar'),main=$('main-content-layout'),top=$('top-header'),pin=$('sidebar-pin-toggle'),pinIcon=$('sidebar-pin-icon'); let pinned=true;
    try{const raw=localStorage.getItem(SIDEBAR_KEY);pinned=raw===null?true:raw==='true';}catch(_){pinned=true;}
    const apply=()=>{sidebar.classList.toggle('sidebar-collapsed',!pinned);sidebar.classList.remove('sidebar-hovered');const width=pinned?268:72;main.style.marginRight=`${width}px`;top.style.right=`${width}px`;if(pinIcon)pinIcon.textContent=pinned?'push_pin':'keep';};
    pin?.addEventListener('click',()=>{pinned=!pinned;try{localStorage.setItem(SIDEBAR_KEY,String(pinned));}catch(_){}apply();}); sidebar.addEventListener('mouseenter',()=>{if(!pinned)sidebar.classList.add('sidebar-hovered');}); sidebar.addEventListener('mouseleave',()=>{if(!pinned)sidebar.classList.remove('sidebar-hovered');}); apply();
    const doctorTrigger=$('doctor-card-trigger'),doctorMenu=$('doctor-account-menu'); doctorTrigger?.addEventListener('click',e=>{e.stopPropagation();doctorMenu?.classList.toggle('hidden');});
    const qBtn=$('quick-add-btn'),qDrop=$('quick-add-dropdown'),qWrap=$('quick-add-container'),qArrow=$('quick-add-arrow');let timer=null;const openQ=()=>{clearTimeout(timer);qDrop?.classList.remove('hidden');if(qArrow)qArrow.textContent='expand_less';};const closeQ=()=>{qDrop?.classList.add('hidden');if(qArrow)qArrow.textContent='expand_more';};qBtn?.addEventListener('click',e=>{e.stopPropagation();qDrop?.classList.contains('hidden')?openQ():closeQ();});qWrap?.addEventListener('mouseenter',()=>clearTimeout(timer));qWrap?.addEventListener('mouseleave',()=>timer=setTimeout(closeQ,200));document.addEventListener('click',e=>{if(qWrap&&!qWrap.contains(e.target))closeQ();if(doctorMenu&&doctorTrigger&&!doctorTrigger.contains(e.target)&&!doctorMenu.contains(e.target))doctorMenu.classList.add('hidden');}); const search=top.querySelector('input[type="text"]');document.addEventListener('keydown',e=>{if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){e.preventDefault();search?.focus();}});qDrop?.querySelectorAll('button').forEach(btn=>btn.addEventListener('click',()=>{closeQ();toast('هذا الإجراء يُفتح من صفحته التشغيلية المناسبة','info');}));
  }
  setupShell(); sync(); setDirty(false);
})();
</script>

<script id="thera-settings-custom-dropdowns">
(function(){
  const configs={
    "setting-facility":"min-w-[250px]",
    "setting-language":"min-w-[180px]",
    "setting-theme":"min-w-[180px]",
    "setting-availability":"w-full"
  };

  function closeAll(except){
    document.querySelectorAll('.thera-dropdown-menu[data-dd-id]').forEach(menu=>{
      if(menu.dataset.ddId===except)return;
      menu.classList.add('hidden');
      const btn=document.querySelector('.thera-dropdown-btn[data-dd-button="'+menu.dataset.ddId+'"]');
      if(btn)btn.setAttribute('aria-expanded','false');
    });
  }

  function sync(id){
    const sel=document.getElementById(id);
    const btn=document.querySelector('.thera-dropdown-btn[data-dd-button="'+id+'"]');
    const menu=document.querySelector('.thera-dropdown-menu[data-dd-id="'+id+'"]');
    if(!sel||!btn||!menu)return;
    const label=btn.querySelector('.thera-dropdown-label');
    if(label)label.textContent=sel.selectedOptions[0]?.textContent||'';
    menu.querySelectorAll('.thera-dropdown-option').forEach(x=>x.classList.toggle('is-selected',x.dataset.value===sel.value));
  }

  function enhance(id){
    const sel=document.getElementById(id);
    if(!sel||sel.dataset.customized==='1')return;
    const wrap=document.createElement('div');
    wrap.className='thera-dropdown-wrap '+(configs[id]||'w-full');
    const btn=document.createElement('button');
    btn.type='button'; btn.className='thera-dropdown-btn'; btn.dataset.ddButton=id; btn.setAttribute('aria-expanded','false');
    btn.innerHTML='<span class="thera-dropdown-label"></span><span class="material-symbols-outlined thera-dropdown-arrow">expand_more</span>';
    const menu=document.createElement('div');
    menu.className='thera-dropdown-menu hidden'; menu.dataset.ddId=id;
    Array.from(sel.options).forEach(opt=>{
      const item=document.createElement('button');
      item.type='button'; item.className='thera-dropdown-option'; item.dataset.value=opt.value; item.textContent=opt.textContent;
      menu.appendChild(item);
    });
    sel.dataset.customized='1'; sel.classList.add('hidden'); sel.setAttribute('aria-hidden','true'); sel.tabIndex=-1;
    sel.parentNode.insertBefore(wrap,sel); wrap.append(sel,btn,menu);

    btn.addEventListener('click',e=>{
      e.stopPropagation();
      const opening=menu.classList.contains('hidden');
      closeAll(opening?id:null);
      menu.classList.toggle('hidden',!opening);
      btn.setAttribute('aria-expanded',opening?'true':'false');
    });
    menu.addEventListener('click',e=>{
      const item=e.target.closest('.thera-dropdown-option'); if(!item)return;
      e.stopPropagation(); sel.value=item.dataset.value; sel.dispatchEvent(new Event('change',{bubbles:true}));
      sync(id); menu.classList.add('hidden'); btn.setAttribute('aria-expanded','false');
    });
    sel.addEventListener('change',()=>sync(id));
    sync(id);
  }

  function init(){
    Object.keys(configs).forEach(enhance);
    document.addEventListener('click',()=>closeAll(null));
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>

@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>