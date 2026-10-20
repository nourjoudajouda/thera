<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>المرضى - THERA</title>
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
<style data-purpose="professional-profile-page">
  .profile-tab { white-space: nowrap; border:1px solid #E2E8F0; background:#fff; color:#475569; transition:.18s ease; }
  .profile-tab:hover { background:#F8FAFC; color:#0F172A; }
  .profile-tab[aria-selected="true"] { background:#0495F6; border-color:#0495F6; color:#fff; box-shadow:0 3px 10px rgba(4,149,246,.18); }
  .profile-panel { display:none; }
  .profile-panel.active { display:block; }
  .profile-card { background:#fff; border:1px solid #E2E8F0; border-radius:18px; box-shadow:0 2px 10px rgba(15,23,42,.035); }
  .field-label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:7px; }
  .field-help { font-size:12px; color:#94A3B8; margin-top:6px; }
  .input-base { width:100%; height:44px; border:1px solid #CBD5E1; border-radius:12px; background:#fff; padding:0 13px; font-size:14px; transition:.16s ease; }
  .input-base:focus, .textarea-base:focus { outline:none; border-color:#0495F6; box-shadow:0 0 0 3px rgba(4,149,246,.10); }
  .textarea-base { width:100%; min-height:118px; border:1px solid #CBD5E1; border-radius:12px; background:#fff; padding:12px 13px; font-size:14px; resize:vertical; transition:.16s ease; }
  .status-pill { display:inline-flex; align-items:center; gap:5px; height:28px; padding:0 10px; border-radius:999px; font-size:12px; font-weight:700; }
  .summary-row + .summary-row { border-top:1px solid #F1F5F9; }
  .progress-track { height:7px; border-radius:999px; background:#E2E8F0; overflow:hidden; }
  .progress-fill { height:100%; border-radius:999px; background:#0495F6; }
  .avatar-drop { border:1px dashed #CBD5E1; transition:.18s ease; }
  .avatar-drop:hover { border-color:#0495F6; background:#F8FCFF; }
  .credential-card, .facility-card, .achievement-card, .specialty-card { border:1px solid #E2E8F0; border-radius:14px; background:#fff; }
  .public-preview-shell { background:linear-gradient(180deg,#F7FBFF 0,#FFFFFF 180px); }
  .soft-grid { background-image:radial-gradient(circle at 1px 1px, rgba(148,163,184,.16) 1px, transparent 0); background-size:18px 18px; }
  .toggle { position:relative; width:44px; height:24px; flex:0 0 auto; }
  .toggle input { opacity:0; width:0; height:0; }
  .toggle-track { position:absolute; inset:0; border-radius:999px; background:#CBD5E1; transition:.18s ease; cursor:pointer; }
  .toggle-track::after { content:""; position:absolute; top:3px; right:3px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.22); transition:.18s ease; }
  .toggle input:checked + .toggle-track { background:#0495F6; }
  .toggle input:checked + .toggle-track::after { transform:translateX(-20px); }
  @media (max-width: 1023px) { .profile-tabs-wrap { overflow-x:auto; scrollbar-width:none; } .profile-tabs-wrap::-webkit-scrollbar { display:none; } }
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
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
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
<span class="material-symbols-outlined flex-shrink-0 text-[20px]">radiology</span>
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
<span class="material-symbols-outlined flex-shrink-0 text-[22px] text-white">receipt_long</span>
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
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
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
<!-- Left Section: Language, Theme, Notifications -->
<div class="flex items-center gap-3">
<!-- Theme Switcher -->
<button class="w-10 h-10 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors" title="الوضع الليلي / النهاري" type="button">
<span class="material-symbols-outlined text-xl">light_mode</span>
</button>
<!-- Language Selector -->
<button class="h-10 px-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 text-sm font-medium transition-colors" type="button">
<span class="material-symbols-outlined text-base">translate</span>
<span class="">عربي (AR)</span>
</button>
<!-- Notifications -->
<div class="relative">
<button class="w-10 h-10 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors" title="التنبيهات" type="button">
<span class="material-symbols-outlined text-xl">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
</button>
</div>
</div>
</header>
<main class="pt-16 p-6 min-h-screen" data-purpose="workspace-content" id="main-content-layout" style="margin-right:268px;">
<div class="max-w-[1500px] mx-auto pt-3">
<div class="flex flex-col gap-1 mb-5">
<div class="flex items-center gap-2 text-sm text-slate-400 mb-1.5">
<span>العمل اليومي</span><span class="material-symbols-outlined text-[16px]">chevron_left</span><span>المرضى</span>
</div>
<div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
<div>
<h1 class="text-[28px] leading-tight font-extrabold text-[#0B1020]">المرضى</h1>
<p class="mt-1 text-sm text-slate-500">ابحث عن مرضاك واعرض المعلومات المتاحة لك حسب سياق الوصول الحالي.</p>
</div>
<div class="inline-flex self-start lg:self-auto p-1 rounded-xl bg-slate-100 border border-slate-200" id="scope-tabs">
<button class="scope-tab px-4 h-9 rounded-lg text-sm font-bold bg-white text-[#0B1020] shadow-sm" data-scope="mine" type="button">مرضاي</button>
<button class="scope-tab px-4 h-9 rounded-lg text-sm font-semibold text-slate-500" data-scope="authorized" type="button">وصول مصرح حاليًا</button>
</div>
</div>
</div>
<div class="mb-4 flex items-start gap-2.5 rounded-xl border border-sky-200 bg-sky-50/70 px-4 py-3 text-[13px] text-sky-900">
<span class="material-symbols-outlined text-[19px] text-sky-600 mt-0.5">shield_lock</span>
<p class="leading-5"><strong>خصوصية السجل:</strong> ظهور المريض في هذه القائمة لا يعني وصولًا كاملًا لملفه الطبي. المعلومات المتاحة تعتمد على سياق الرعاية والصلاحيات وإعدادات خصوصية المريض.</p>
</div>
<section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-visible">
<div class="p-4 border-b border-slate-200 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
<div class="relative flex-1 max-w-2xl">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
<input class="w-full h-11 rounded-xl border border-slate-200 bg-slate-50 pr-10 pl-10 text-sm outline-none focus:bg-white focus:border-[#0495F6] focus:ring-2 focus:ring-[#0495F6]/10" id="patient-search" placeholder="ابحث بالاسم أو رقم المريض أو الهاتف..." type="text"/>
<button class="hidden absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400" id="clear-search" type="button"><span class="material-symbols-outlined text-[18px]">close</span></button>
</div>
<div class="flex items-center gap-2 flex-wrap">
<!-- Access Filter: same compact visual language as the original Patients List -->
<div class="relative" id="access-filter-wrap">
<button class="h-10 px-3.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#F1F5F9] text-[#0B1020] text-[13px] font-medium inline-flex items-center gap-1.5 transition-colors" id="access-filter-btn" type="button">
<span class="material-symbols-outlined text-[18px] text-[#64748B]">filter_list</span>
<span id="access-filter-label">كل حالات الوصول</span>
<span class="material-symbols-outlined text-[16px] text-[#64748B]">expand_more</span>
</button>
<div class="hidden absolute top-11 right-0 w-48 bg-white rounded-xl shadow-lg border border-[#E2E8F0] p-1.5 z-30 flex flex-col gap-0.5" id="access-filter-menu">
<button class="access-filter-opt flex items-center justify-between w-full px-3 py-2 rounded-lg text-right text-[13px] text-[#0495F6] font-semibold bg-[#F0F7FF]" data-access-filter="all" data-label="كل حالات الوصول" type="button"><span>كل حالات الوصول</span><span class="material-symbols-outlined text-[16px]">check</span></button>
<button class="access-filter-opt flex items-center justify-between w-full px-3 py-2 rounded-lg text-right text-[13px] text-[#0B1020] hover:bg-[#F8FAFC]" data-access-filter="authorized" data-label="وصول مصرح" type="button"><span>وصول مصرح</span><span class="material-symbols-outlined text-[16px] opacity-0">check</span></button>
<button class="access-filter-opt flex items-center justify-between w-full px-3 py-2 rounded-lg text-right text-[13px] text-[#0B1020] hover:bg-[#F8FAFC]" data-access-filter="limited" data-label="وصول محدود" type="button"><span>وصول محدود</span><span class="material-symbols-outlined text-[16px] opacity-0">check</span></button>
<button class="access-filter-opt flex items-center justify-between w-full px-3 py-2 rounded-lg text-right text-[13px] text-[#0B1020] hover:bg-[#F8FAFC]" data-access-filter="expired" data-label="انتهت الصلاحية" type="button"><span>انتهت الصلاحية</span><span class="material-symbols-outlined text-[16px] opacity-0">check</span></button>
<button class="access-filter-opt flex items-center justify-between w-full px-3 py-2 rounded-lg text-right text-[13px] text-[#0B1020] hover:bg-[#F8FAFC]" data-access-filter="denied" data-label="لا يوجد وصول" type="button"><span>لا يوجد وصول</span><span class="material-symbols-outlined text-[16px] opacity-0">check</span></button>
</div>
</div>
<!-- Custom compact checkbox -->
<label class="h-10 inline-flex items-center gap-2 px-3.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#F1F5F9] text-[#0B1020] text-[13px] font-medium cursor-pointer transition-colors select-none">
<span class="relative w-4 h-4 shrink-0">
<input class="peer sr-only" id="appointment-filter" type="checkbox"/>
<span class="absolute inset-0 rounded-[4px] border border-[#CBD5E1] bg-white peer-checked:bg-[#0495F6] peer-checked:border-[#0495F6] transition-colors"></span>
<span class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-[13px] leading-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
</span>
<span>لديهم موعد قادم</span>
</label>
<div class="h-10 inline-flex items-center gap-2 px-3.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-[13px] text-[#64748B]">
<span>النتائج</span><strong class="font-['Inter'] text-[#0B1020]" id="result-count">6</strong>
</div>
</div>
</div>
<div class="hidden lg:block overflow-x-auto">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-[12px] font-bold text-slate-500">
<th class="px-4 py-3">المريض</th>
<th class="px-4 py-3">العمر</th>
<th class="px-4 py-3">تنبيهات مهمة</th>
<th class="px-4 py-3">آخر زيارة</th>
<th class="px-4 py-3">الموعد القادم</th>
<th class="px-4 py-3">حالة الوصول</th>
<th class="px-4 py-3 w-[150px]">إجراء</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100" id="patients-body">
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="authorized" data-appt="true" data-id="P-10482" data-key="ahmad" data-name="أحمد محمود" data-phone="0599123456" data-scope="mine,authorized">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-blue-100 text-[#0495F6] flex items-center justify-center font-bold text-xs">أم</div><div><div class="font-bold text-sm text-[#0B1020]">أحمد محمود</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-10482</span> · <span dir="ltr">0599 123 456</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">42 سنة</td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 text-[12px] font-bold text-rose-700 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-full"><span class="material-symbols-outlined text-[15px]">warning</span>حساسية بنسلين</span></td>
<td class="px-4 py-3.5"><div class="text-sm text-slate-700">28 سبتمبر 2026</div><div class="text-[11px] text-slate-400 mt-0.5">زيارة متابعة</div></td>
<td class="px-4 py-3.5"><div class="text-sm font-bold text-sky-700">اليوم، 10:30 ص</div><div class="text-[11px] text-slate-400 mt-0.5">استشارة باطنية</div></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[12px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>وصول مصرح</span></td>
<td class="px-4 py-3.5"><button class="patient-action px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#E2E8F0] text-[#0B1020] text-[13px] font-semibold inline-flex items-center gap-1 transition-colors" data-key="ahmad" type="button"><span>عرض المريض</span><span class="material-symbols-outlined text-[16px] text-[#64748B]">open_in_new</span></button></td>
</tr>
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="limited" data-appt="true" data-id="P-10831" data-key="sara" data-name="سارة خالد" data-phone="0598654321" data-scope="mine,authorized">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center font-bold text-xs">سخ</div><div><div class="font-bold text-sm text-[#0B1020]">سارة خالد</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-10831</span> · <span dir="ltr">0598 654 321</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">34 سنة</td>
<td class="px-4 py-3.5"><span class="text-[12px] text-slate-500">المتاح ضمن نطاق الوصول فقط</span></td>
<td class="px-4 py-3.5"><div class="text-sm text-slate-700">12 أغسطس 2026</div><div class="text-[11px] text-slate-400 mt-0.5">ضمن سجلك العلاجي</div></td>
<td class="px-4 py-3.5"><div class="text-sm font-bold text-sky-700">24 أكتوبر، 02:00 م</div><div class="text-[11px] text-slate-400 mt-0.5">متابعة</div></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-[12px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>وصول محدود</span></td>
<td class="px-4 py-3.5"><button class="patient-action px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#E2E8F0] text-[#0B1020] text-[13px] font-semibold inline-flex items-center gap-1 transition-colors" data-key="sara" type="button"><span>عرض المريض</span><span class="material-symbols-outlined text-[16px] text-[#64748B]">open_in_new</span></button></td>
</tr>
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="authorized" data-appt="false" data-id="P-10902" data-key="mohammad" data-name="محمد علي" data-phone="0569332110" data-scope="mine,authorized">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-xs">مع</div><div><div class="font-bold text-sm text-[#0B1020]">محمد علي</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-10902</span> · <span dir="ltr">0569 332 110</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">51 سنة</td>
<td class="px-4 py-3.5"><span class="text-[12px] text-slate-400">لا توجد تنبيهات معروضة</span></td>
<td class="px-4 py-3.5"><div class="text-sm text-slate-700">15 يوليو 2026</div><div class="text-[11px] text-slate-400 mt-0.5">زيارة سابقة</div></td>
<td class="px-4 py-3.5"><span class="text-sm text-slate-400">لا يوجد</span></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[12px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>وصول مصرح</span></td>
<td class="px-4 py-3.5"><button class="patient-action px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#E2E8F0] text-[#0B1020] text-[13px] font-semibold inline-flex items-center gap-1 transition-colors" data-key="mohammad" type="button"><span>عرض المريض</span><span class="material-symbols-outlined text-[16px] text-[#64748B]">open_in_new</span></button></td>
</tr>
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="expired" data-appt="true" data-id="P-10943" data-key="layan" data-name="ليان يوسف" data-phone="0597421770" data-scope="mine">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">لي</div><div><div class="font-bold text-sm text-[#0B1020]">ليان يوسف</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-10943</span> · <span dir="ltr">0597 421 770</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">29 سنة</td>
<td class="px-4 py-3.5"><span class="text-[12px] text-slate-400">—</span></td>
<td class="px-4 py-3.5"><div class="text-sm text-slate-700">03 يونيو 2026</div><div class="text-[11px] text-slate-400 mt-0.5">زيارة سابقة</div></td>
<td class="px-4 py-3.5"><div class="text-sm font-bold text-sky-700">28 أكتوبر، 11:00 ص</div><div class="text-[11px] text-slate-400 mt-0.5">موعد جديد</div></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[12px] font-bold"><span class="material-symbols-outlined text-[14px]">schedule</span>انتهت صلاحية الوصول</span></td>
<td class="px-4 py-3.5"><button class="patient-action h-9 px-3 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-[12px] font-bold" data-key="layan" type="button">عرض حالة الوصول</button></td>
</tr>
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="denied" data-appt="false" data-id="P-11016" data-key="omar" data-name="عمر حسن" data-phone="0568221449" data-scope="mine">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">عح</div><div><div class="font-bold text-sm text-[#0B1020]">عمر حسن</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-11016</span> · <span dir="ltr">0568 221 449</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">47 سنة</td>
<td class="px-4 py-3.5"><span class="text-[12px] text-slate-400">—</span></td>
<td class="px-4 py-3.5"><span class="text-sm text-slate-400">بيانات غير متاحة</span></td>
<td class="px-4 py-3.5"><span class="text-sm text-slate-400">لا يوجد</span></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100 text-[12px] font-bold"><span class="material-symbols-outlined text-[14px]">lock</span>لا يوجد وصول</span></td>
<td class="px-4 py-3.5"><button class="patient-action h-9 px-3 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-[12px] font-bold" data-key="omar" type="button">عرض حالة الوصول</button></td>
</tr>
<tr class="patient-row hover:bg-slate-50/70 transition-colors" data-access="authorized" data-appt="true" data-id="P-11057" data-key="noor" data-name="نور أحمد" data-phone="0595662301" data-scope="mine,authorized">
<td class="px-4 py-3.5"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">نأ</div><div><div class="font-bold text-sm text-[#0B1020]">نور أحمد</div><div class="text-[11px] text-slate-400 mt-0.5"><span class="font-['Inter']" dir="ltr">P-11057</span> · <span dir="ltr">0595 662 301</span></div></div></div></td>
<td class="px-4 py-3.5 text-sm text-slate-700">38 سنة</td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 text-[12px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full"><span class="material-symbols-outlined text-[15px]">info</span>سكري</span></td>
<td class="px-4 py-3.5"><div class="text-sm text-slate-700">20 سبتمبر 2026</div><div class="text-[11px] text-slate-400 mt-0.5">استشارة</div></td>
<td class="px-4 py-3.5"><div class="text-sm font-bold text-sky-700">30 أكتوبر، 09:30 ص</div><div class="text-[11px] text-slate-400 mt-0.5">متابعة</div></td>
<td class="px-4 py-3.5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[12px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>وصول مصرح</span></td>
<td class="px-4 py-3.5"><button class="patient-action px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#E2E8F0] text-[#0B1020] text-[13px] font-semibold inline-flex items-center gap-1 transition-colors" data-key="noor" type="button"><span>عرض المريض</span><span class="material-symbols-outlined text-[16px] text-[#64748B]">open_in_new</span></button></td>
</tr>
</tbody>
</table>
</div>
<div class="lg:hidden p-3 space-y-3" id="patient-mobile-list"></div>
<div class="hidden py-16 px-4 text-center" id="empty-state">
<div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-[30px]">person_search</span></div>
<h3 class="font-bold text-[#0B1020]">لا توجد نتائج مطابقة</h3>
<p class="text-sm text-slate-500 mt-1">غيّر البحث أو عوامل التصفية وحاول مرة أخرى.</p>
</div>
<div class="px-4 py-3.5 border-t border-slate-200 flex items-center justify-between text-[12px] text-slate-500">
<div>عرض <strong class="font-['Inter'] text-[#0B1020]" id="footer-count">6</strong> مرضى في هذه المعاينة</div>
<div class="hidden sm:flex items-center gap-1"><button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-400" disabled="">السابق</button><button class="w-8 h-8 rounded-lg bg-[#0495F6] text-white font-bold">1</button><button class="w-8 h-8 rounded-lg hover:bg-slate-100">2</button><button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50">التالي</button></div>
</div>
</section>
</div>
</main>


<div aria-hidden="true" class="fixed inset-0 z-[200] hidden" id="patient-modal">
<div class="absolute inset-0 bg-[#0B1020]/45 backdrop-blur-[1px]" data-close-modal=""></div>
<section class="absolute left-0 top-0 bottom-0 w-full max-w-[520px] bg-white shadow-2xl border-r border-slate-200 flex flex-col" dir="rtl">
<header class="h-16 px-5 flex items-center justify-between border-b border-slate-200 shrink-0">
<div><div class="text-[11px] font-bold text-[#0495F6] mb-0.5" id="modal-kicker">سياق المريض</div><h2 class="text-lg font-extrabold text-[#0B1020]" id="modal-title">تفاصيل المريض</h2></div>
<button class="w-9 h-9 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center" data-close-modal="" type="button"><span class="material-symbols-outlined">close</span></button>
</header>
<div class="flex-1 overflow-y-auto p-5 custom-scroll" id="modal-body"></div>
<footer class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-2 shrink-0" id="modal-footer"></footer>
</section>
</div><div class="fixed bottom-6 left-6 z-[220] flex flex-col gap-2 pointer-events-none" id="toast-container"></div><script>
(function(){
  const $ = (id) => document.getElementById(id);
  const sidebar=$('app-sidebar'), topHeader=$('top-header'), main=$('main-content-layout'), pinIcon=$('sidebar-pin-icon');
  const KEY='thera_doctor_sidebar_pinned';
  function applyLayout(collapsed){
    if(!sidebar||!topHeader||!main)return;
    sidebar.classList.toggle('sidebar-collapsed',collapsed);
    topHeader.style.right=collapsed?'72px':'268px';
    main.style.marginRight=collapsed?'72px':'268px';
    if(pinIcon)pinIcon.textContent=collapsed?'push_pin':'keep';
  }
  const pinned=localStorage.getItem(KEY)!=='false'; applyLayout(!pinned);
  window.toggleSidebarPin=function(){const collapsed=!sidebar.classList.contains('sidebar-collapsed');localStorage.setItem(KEY,collapsed?'false':'true');applyLayout(collapsed)};
  if(sidebar){sidebar.addEventListener('mouseenter',()=>{if(sidebar.classList.contains('sidebar-collapsed'))sidebar.classList.add('sidebar-hovered')});sidebar.addEventListener('mouseleave',()=>sidebar.classList.remove('sidebar-hovered'))}
  const qBtn=$('quick-add-btn'), qMenu=$('quick-add-dropdown');
  if(qBtn&&qMenu){qBtn.addEventListener('click',e=>{e.stopPropagation();qMenu.classList.toggle('hidden')});document.addEventListener('click',()=>qMenu.classList.add('hidden'))}

  const patientData={
    ahmad:{name:'أحمد محمود',id:'P-10482',age:'42 سنة',access:'authorized',accessLabel:'وصول مصرح',context:'موعد مؤكد اليوم — سياق رعاية نشط',note:'الوصول الحالي يسمح بعرض السجل المصرح به وفق إعدادات خصوصية المريض.',available:['ملخص السجل المصرح','زياراتك السابقة مع المريض','الأدوية والحساسية المتاحة','طلباتك ونتائجها المرتبطة','المواعيد والإحالات والمتابعات المرتبطة']},
    sara:{name:'سارة خالد',id:'P-10831',age:'34 سنة',access:'limited',accessLabel:'وصول محدود',context:'متابعة قادمة — نطاق وصول محدود',note:'بعض معلومات السجل غير متاحة وفق إعدادات الخصوصية الحالية.',available:['المعلومات المسموح بها فقط','الزيارات المرتبطة بك','الطلبات والنتائج المرتبطة بك','بيانات الموعد الحالي']},
    mohammad:{name:'محمد علي',id:'P-10902',age:'51 سنة',access:'authorized',accessLabel:'وصول مصرح',context:'علاقة علاجية سابقة',note:'بعد انتهاء سياق الرعاية الواسع، يبقى الوصول إلى السجلات المرتبطة بعلاقتك العلاجية مع المريض.',available:['زياراتك السابقة','تشخيصاتك وملاحظاتك','الوصفات والطلبات الصادرة منك','النتائج المرتبطة بطلباتك','الإحالات والمتابعات التي أنشأتها']},
    layan:{name:'ليان يوسف',id:'P-10943',age:'29 سنة',access:'expired',accessLabel:'انتهت صلاحية الوصول',context:'لا يوجد سياق رعاية نشط حاليًا',note:'انتهت صلاحية الوصول الواسع للسجل. لا يتم عرض بيانات سريرية حساسة من هذه الشاشة.',available:[]},
    omar:{name:'عمر حسن',id:'P-11016',age:'47 سنة',access:'denied',accessLabel:'لا يوجد وصول',context:'لا يوجد تفويض أو سياق رعاية يسمح بفتح السجل',note:'معرفة المريض أو وجود علاقة سابقة لا تمنح وصولًا تلقائيًا لسجله الطبي.',available:[]},
    noor:{name:'نور أحمد',id:'P-11057',age:'38 سنة',access:'authorized',accessLabel:'وصول مصرح',context:'متابعة قادمة — سياق رعاية نشط',note:'الوصول الحالي يسمح بعرض السجل المصرح به وفق إعدادات خصوصية المريض.',available:['ملخص السجل المصرح','زياراتك السابقة مع المريضة','الأدوية والحساسية المتاحة','طلباتك ونتائجها المرتبطة','المواعيد والمتابعات المرتبطة']}
  };

  function toast(msg){const el=document.createElement('div');el.className='pointer-events-auto min-w-[280px] max-w-sm bg-[#0B1020] text-white rounded-xl shadow-xl px-4 py-3 text-sm flex items-center gap-2';el.innerHTML='<span class="material-symbols-outlined text-emerald-400 text-[19px]">check_circle</span><span>'+msg+'</span>';$('toast-container').appendChild(el);setTimeout(()=>el.remove(),2600)}

  const modal=$('patient-modal');
  function closeModal(){modal.classList.add('hidden');modal.setAttribute('aria-hidden','true')}
  document.querySelectorAll('[data-close-modal]').forEach(x=>x.addEventListener('click',closeModal));
  window.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});

  function openPatient(key){
    const p=patientData[key]; if(!p)return;
    $('modal-title').textContent=p.name;
    $('modal-kicker').textContent=p.id+' · '+p.age;
    const colors=p.access==='authorized'?'bg-emerald-50 text-emerald-700 border-emerald-100':p.access==='limited'?'bg-amber-50 text-amber-700 border-amber-100':p.access==='expired'?'bg-slate-100 text-slate-700 border-slate-200':'bg-rose-50 text-rose-700 border-rose-100';
    let html='<div class="rounded-2xl border border-slate-200 p-4 mb-4"><div class="flex items-center justify-between gap-3 mb-3"><div><div class="text-xs text-slate-400 mb-1">حالة الوصول</div><span class="inline-flex px-2.5 py-1 rounded-full border text-xs font-bold '+colors+'">'+p.accessLabel+'</span></div><span class="material-symbols-outlined text-[28px] text-slate-300">shield_person</span></div><div class="text-sm font-bold text-[#0B1020]">'+p.context+'</div><p class="text-[13px] leading-6 text-slate-500 mt-2">'+p.note+'</p></div>';
    if(p.available.length){html+='<div class="mb-4"><h3 class="font-extrabold text-sm text-[#0B1020] mb-2.5">المعلومات المتاحة في هذا السياق</h3><div class="space-y-2">'+p.available.map(x=>'<div class="flex items-center gap-2 text-[13px] text-slate-700"><span class="material-symbols-outlined text-[17px] text-emerald-600">check_circle</span><span>'+x+'</span></div>').join('')+'</div></div><div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-[12px] leading-5 text-slate-500">فتح ملف المريض لا يلغي قيود الوصول؛ الخادم يرسل فقط البيانات المسموح بها في السياق الحالي.</div>'}
    else {html+='<div class="rounded-xl bg-rose-50 border border-rose-100 p-4"><div class="flex items-start gap-2"><span class="material-symbols-outlined text-rose-600 text-[19px]">lock</span><div><div class="font-bold text-sm text-rose-800">السجل الطبي غير متاح</div><p class="text-[12px] leading-5 text-rose-700 mt-1">لن يتم كشف محتوى مخفي أو عدد العناصر أو تفاصيل سريرية منتهية/غير مصرح بها.</p></div></div></div>'}
    $('modal-body').innerHTML=html;
    if(p.access==='authorized'||p.access==='limited') $('modal-footer').innerHTML='<button class="px-4 h-10 rounded-lg border border-slate-300 bg-white text-sm font-bold text-slate-700" data-modal-close-btn>إغلاق</button><button class="px-4 h-10 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-sm font-bold" id="open-record-btn">فتح ملف المريض</button>';
    else if(p.access==='expired') $('modal-footer').innerHTML='<button class="px-4 h-10 rounded-lg border border-slate-300 bg-white text-sm font-bold text-slate-700" data-modal-close-btn>إغلاق</button><button class="px-4 h-10 rounded-lg bg-[#0495F6] text-white text-sm font-bold" id="restore-access-btn">استعادة/طلب الوصول</button>';
    else $('modal-footer').innerHTML='<button class="px-4 h-10 rounded-lg border border-slate-300 bg-white text-sm font-bold text-slate-700" data-modal-close-btn>إغلاق</button><span class="text-xs text-slate-400">لا توجد إجراءات سريرية متاحة</span>';
    $('modal-footer').querySelectorAll('[data-modal-close-btn]').forEach(x=>x.addEventListener('click',closeModal));
    const open=$('open-record-btn'); if(open)open.addEventListener('click',()=>toast('سيتم فتح Patient Detail مع تطبيق نطاق الوصول الحالي'));
    const restore=$('restore-access-btn'); if(restore)restore.addEventListener('click',()=>toast('تم بدء مسار استعادة الوصول التجريبي'));
    modal.classList.remove('hidden'); modal.setAttribute('aria-hidden','false');
  }
  document.querySelectorAll('.patient-action').forEach(b=>b.addEventListener('click',()=>openPatient(b.dataset.key)));

  const rows=[...document.querySelectorAll('.patient-row')];
  const search=$('patient-search'), clear=$('clear-search'), appt=$('appointment-filter'), empty=$('empty-state');
  const accessBtn=$('access-filter-btn'), accessMenu=$('access-filter-menu'), accessLabel=$('access-filter-label');
  let accessValue='all';
  let scope='mine';
  function apply(){const term=search.value.trim().toLowerCase().replace(/\s+/g,'');let count=0;rows.forEach(r=>{const hay=(r.dataset.name+r.dataset.id+r.dataset.phone).toLowerCase().replace(/\s+/g,'');const okSearch=!term||hay.includes(term);const okAccess=accessValue==='all'||r.dataset.access===accessValue;const okAppt=!appt.checked||r.dataset.appt==='true';const scopes=(r.dataset.scope||'').split(',');const okScope=scopes.includes(scope);const show=okSearch&&okAccess&&okAppt&&okScope;r.classList.toggle('hidden',!show);if(show)count++});clear.classList.toggle('hidden',!search.value);$('result-count').textContent=count;$('footer-count').textContent=count;empty.classList.toggle('hidden',count!==0);renderMobile();}
  function renderMobile(){const container=$('patient-mobile-list');container.innerHTML='';rows.filter(r=>!r.classList.contains('hidden')).forEach(r=>{const key=r.dataset.key,p=patientData[key];const card=document.createElement('div');card.className='rounded-xl border border-slate-200 bg-slate-50 p-3.5';card.innerHTML='<div class="flex items-start justify-between gap-3"><div><div class="font-bold text-sm text-[#0B1020]">'+p.name+'</div><div class="text-[11px] text-slate-400 mt-1">'+p.id+' · '+p.age+'</div></div><span class="text-[11px] font-bold px-2 py-1 rounded-full bg-white border border-slate-200">'+p.accessLabel+'</span></div><div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between gap-2"><span class="text-[12px] text-slate-500">'+p.context+'</span><button class="mobile-open px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] hover:bg-[#E2E8F0] text-[#0B1020] text-[12px] font-semibold transition-colors" data-key="'+key+'">عرض</button></div>';container.appendChild(card)});container.querySelectorAll('.mobile-open').forEach(b=>b.addEventListener('click',()=>openPatient(b.dataset.key)))}
  search.addEventListener('input',apply); clear.addEventListener('click',()=>{search.value='';apply();search.focus()}); appt.addEventListener('change',apply);
  if(accessBtn&&accessMenu){
    accessBtn.addEventListener('click',e=>{e.stopPropagation();accessMenu.classList.toggle('hidden')});
    document.querySelectorAll('.access-filter-opt').forEach(opt=>opt.addEventListener('click',()=>{
      accessValue=opt.dataset.accessFilter||'all';
      accessLabel.textContent=opt.dataset.label||opt.textContent.trim();
      document.querySelectorAll('.access-filter-opt').forEach(x=>{
        const on=x===opt;
        x.classList.toggle('text-[#0495F6]',on);x.classList.toggle('font-semibold',on);x.classList.toggle('bg-[#F0F7FF]',on);x.classList.toggle('text-[#0B1020]',!on);
        const check=x.querySelector('.material-symbols-outlined');if(check)check.classList.toggle('opacity-0',!on);
      });
      accessMenu.classList.add('hidden');apply();
    }));
    document.addEventListener('click',e=>{if(!$('access-filter-wrap').contains(e.target))accessMenu.classList.add('hidden')});
  }
  document.querySelectorAll('.scope-tab').forEach(btn=>btn.addEventListener('click',()=>{scope=btn.dataset.scope;document.querySelectorAll('.scope-tab').forEach(x=>{const on=x===btn;x.classList.toggle('bg-white',on);x.classList.toggle('shadow-sm',on);x.classList.toggle('text-[#0B1020]',on);x.classList.toggle('text-slate-500',!on);x.classList.toggle('font-bold',on);x.classList.toggle('font-semibold',!on)});apply()}));
  apply();
})();
</script>@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>
