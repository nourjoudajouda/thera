<!DOCTYPE html><html dir="rtl" lang="ar" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>الإحالات والمتابعات - THERA</title>
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

<style id="thera-unified-controls">
html,body{overflow-x:hidden;}
.thera-major-btn{border-radius:8px!important;min-height:40px;}
.thera-icon-btn{border-radius:8px!important;}
.thera-custom-select{position:relative;width:100%;min-width:0;}
.thera-custom-select-trigger{width:100%;height:40px;padding:0 12px;background:#fff;border:1px solid #E2E8F0;border-radius:8px;color:#334155;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:space-between;gap:10px;text-align:right;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;}
.thera-custom-select-trigger:hover{background:#F8FAFC;border-color:#CBD5E1;}
.thera-custom-select.open .thera-custom-select-trigger{border-color:#0495F6;box-shadow:0 0 0 2px rgba(4,149,246,.12);background:#fff;}
.thera-custom-select-arrow{font-size:16px;color:#94A3B8;transition:transform .15s ease;}
.thera-custom-select.open .thera-custom-select-arrow{transform:rotate(180deg);color:#0495F6;}
.thera-custom-select-menu{position:absolute;top:calc(100% + 6px);right:0;left:0;z-index:120;padding:6px;background:#fff;border:1px solid #E2E8F0;border-radius:10px;box-shadow:0 10px 28px rgba(15,23,42,.12);max-height:260px;overflow:auto;min-width:170px;}
.thera-custom-select-option{width:100%;min-height:34px;padding:7px 10px;border-radius:7px;display:flex;align-items:center;justify-content:space-between;gap:8px;color:#334155;font-size:12px;text-align:right;transition:background .12s ease,color .12s ease;}
.thera-custom-select-option:hover{background:#F8FAFC;color:#0B1020;}
.thera-custom-select-option.active{background:#EFF6FF;color:#0369A1;font-weight:700;}
.thera-custom-select-option .check{font-size:15px;opacity:0;color:#0495F6;}
.thera-custom-select-option.active .check{opacity:1;}
.thera-native-select-hidden{position:absolute!important;opacity:0!important;pointer-events:none!important;width:1px!important;height:1px!important;}
</style>

</head>
<body class="select-none bg-[#F6F9FC] text-slate-800 antialiased min-h-screen">
<!-- BEGIN: Sidebar -->
<aside class="fixed top-0 right-0 bottom-0 z-50 bg-[#16243E] border-l border-[rgba(255,255,255,0.08)] flex flex-col select-none" data-purpose="application-sidebar" id="app-sidebar">
<!-- Sidebar Header & Official Logo -->
<header class="h-16 flex items-center justify-between px-4 bg-[#132038] border-b border-[rgba(255,255,255,0.08)] flex-shrink-0">
<div class="flex items-center gap-3 overflow-hidden">
<img alt="THERA Logo" class="h-9 max-w-[130px] object-contain flex-shrink-0 sidebar-brand-text" src="{{ asset('images/doctor/thera-logo.png') }}">
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
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">grid_view</span>
<span class="nav-label">الرئيسية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">calendar_today</span>
<span class="nav-label">المواعيد</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">person_search</span>
<span class="nav-label">المرضى</span>
</a>
</div>
<!-- Group 2: العمل الطبي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">العمل الطبي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">stethoscope</span>
<span class="nav-label">الزيارات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">prescriptions</span>
<span class="nav-label">الوصفات الطبية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label">طلبات المختبر</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">radiology</span>
<span class="nav-label">طلبات الأشعة</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">assignment_turned_in</span>
<span class="nav-label">النتائج</span>
</a>
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20 transition-all text-base" href="#">
<span class="material-symbols-outlined text-[22px] flex-shrink-0 text-white">sync_alt</span>
<span class="nav-label">الإحالات والمتابعات</span>
</a>
</div>
<!-- Group 3: الإدارة -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2">الإدارة</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">receipt_long</span>
<span class="nav-label">الخدمات والأسعار</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">schedule</span>
<span class="nav-label">الدوام والجدول</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">account_balance_wallet</span>
<span class="nav-label">التحصيل والمستحقات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-all text-base font-semibold" href="#">
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
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">science</span>
<span class="">طلب مختبر</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" id="quick-add-rad-order-btn" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">radiology</span>
<span class="">طلب أشعة</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-brand-light text-brand font-bold flex items-center gap-2.5" id="quick-add-ref-follow-btn" type="button">
<span class="material-symbols-outlined text-brand text-lg">sync_alt</span>
<span class="">إحالة / متابعة</span>
</button>
</div>
</div>
<!-- Quick Search Input -->
<div class="relative w-80">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
<input class="w-full h-10 pr-10 pl-4 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand placeholder-slate-400" placeholder="ابحث عن مريض أو موعد... (⌘K)" type="text">
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
<span>عربي (AR)</span>
</button>
<!-- Theme Switcher -->
<button class="w-10 h-10 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors" title="الوضع الليلي / النهاري" type="button">
<span class="material-symbols-outlined text-xl">light_mode</span>
</button>
</div>
</header>
<!-- END: Topbar -->
<!-- BEGIN: Main Content Layout -->
<main class="pt-16 px-6 pb-6 min-h-screen" data-purpose="workspace-content" id="main-content-layout">
  <!-- Page Header -->
  <section class="mt-4 mb-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
      <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1 font-medium" aria-label="Breadcrumb">
        <span>العمل الطبي</span>
        <span class="material-symbols-outlined text-xs">chevron_left</span>
        <span class="text-brand font-bold">الإحالات والمتابعات</span>
      </nav>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">الإحالات والمتابعات</h1>
      <p class="text-xs text-slate-500 mt-1">إدارة إحالات المرضى وخطط المتابعة دون خلطها بمسار حجز المواعيد</p>
    </div>
    <div class="flex items-center gap-2">
      <button class="h-10 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm thera-major-btn" id="open-followup-btn" type="button">
        <span class="material-symbols-outlined text-lg text-brand">event_repeat</span>
        <span>متابعة جديدة</span>
      </button>
      <button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-lg font-bold flex items-center gap-2 shadow-sm transition-colors text-sm thera-major-btn" id="open-referral-btn" type="button">
        <span class="material-symbols-outlined text-lg">add</span>
        <span>إحالة جديدة</span>
      </button>
    </div>
  </section>

  <!-- Summary -->
  <section class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-5" aria-label="ملخص الإحالات والمتابعات">
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">إجمالي السجلات</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-total">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">folder_open</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">إحالات نشطة</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-referrals">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl">sync_alt</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">بحاجة لإجراء</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-action-needed">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">priority_high</span></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center justify-between gap-3">
      <div><p class="text-[11px] text-slate-400 font-medium">مكتملة</p><p class="text-xl font-bold text-slate-900 font-num mt-0.5" id="summary-completed">0</p></div>
      <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">task_alt</span></div>
    </div>
  </section>

  <!-- Tabs -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 mb-4" id="ref-follow-tabs">
    <button class="rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm" data-tab="all" type="button"><span>الكل</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-num" id="tab-count-all">0</span></button>
    <button class="rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="referral" type="button"><span>الإحالات</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-referral">0</span></button>
    <button class="rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="followup" type="button"><span>المتابعات</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-followup">0</span></button>
    <button class="rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="action_needed" type="button"><span>بحاجة لإجراء</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-action-needed">0</span></button>
    <button class="rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]" data-tab="completed" type="button"><span>مكتملة</span><span class="tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num" id="tab-count-completed">0</span></button>
  </div>

  <!-- Filters -->
  <section class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 overflow-visible">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-[minmax(190px,1.15fr)_minmax(110px,.62fr)_minmax(125px,.72fr)_minmax(110px,.62fr)_minmax(160px,.86fr)_40px] items-center gap-2 w-full min-w-0">
      <div class="relative min-w-0">
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
        <input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rf-filter-search" placeholder="ابحث بالمريض أو رقم السجل أو السبب..." type="text">
      </div>
      <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 px-2.5" id="rf-filter-type"><option value="">النوع: الكل</option><option value="referral">إحالة</option><option value="followup">متابعة</option></select>
      <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 px-2.5" id="rf-filter-status"><option value="">الحالة: الكل</option><option value="draft">مسودة</option><option value="documented">موثقة</option><option value="route_requested">طلب توجيه</option><option value="required">مطلوبة</option><option value="appointment_created">تم إنشاء موعد</option><option value="completed">مكتملة</option><option value="cancelled">ملغاة</option></select>
      <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 px-2.5" id="rf-filter-priority"><option value="">الأولوية: الكل</option><option value="normal">عادي</option><option value="urgent">عاجل</option></select>
      <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 px-2.5" id="rf-filter-sort"><option value="newest">الترتيب: الأحدث أولاً</option><option value="oldest">الترتيب: الأقدم أولاً</option><option value="due_first">الترتيب: الأقرب استحقاقًا</option><option value="urgent">الترتيب: الأولوية العاجلة</option></select>
      <button aria-label="إعادة ضبط الفلاتر" class="w-10 h-10 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg flex items-center justify-center transition-colors thera-icon-btn" id="reset-rf-filters-btn" title="إعادة ضبط الفلاتر" type="button"><span class="material-symbols-outlined text-[18px]">restart_alt</span></button>
    </div>
  </section>

  <!-- Table: header stays visible even when empty -->
  <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="rf-table-card">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs min-w-[1120px]">
        <thead class="bg-slate-50 text-slate-500 font-bold text-[11px]">
          <tr>
            <th class="py-3 px-4">المريض</th>
            <th class="py-3 px-4">السجل</th>
            <th class="py-3 px-4">التفاصيل</th>
            <th class="py-3 px-4">الزيارة</th>
            <th class="py-3 px-4">الجهة / موعد المتابعة</th>
            <th class="py-3 px-4">الأولوية</th>
            <th class="py-3 px-4">الحالة</th>
            <th class="py-3 px-4">آخر تحديث</th>
            <th class="py-3 px-4 text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100" id="rf-table-body"></tbody>
      </table>
    </div>
  </section>

  <div class="md:hidden space-y-3 mt-4" id="rf-mobile-container"></div>
</main>

<!-- Create / Edit Drawer -->
<aside aria-labelledby="rf-create-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawer-rf-create" role="dialog">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" id="rf-create-backdrop"></div>
  <div class="fixed inset-y-0 left-0 max-w-full flex">
    <div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
      <header class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl" id="rf-create-icon">sync_alt</span></div>
          <div><h2 class="text-base font-bold text-slate-900" id="rf-create-title">إحالة جديدة</h2><p class="text-xs text-slate-500" id="rf-create-subtitle">إنشاء إحالة مرتبطة بالمريض والزيارة</p></div>
        </div>
        <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center" id="rf-create-close" type="button"><span class="material-symbols-outlined text-xl">close</span></button>
      </header>

      <form class="flex-1 overflow-y-auto p-6 space-y-5 text-xs" id="rf-form" onsubmit="return false;">
        <input id="rf-form-id" type="hidden" value="">
        <input id="rf-form-type" type="hidden" value="referral">

        <!-- Type switch -->
        <section class="bg-slate-100 rounded-xl p-1 grid grid-cols-2 gap-1" id="rf-type-switch">
          <button class="h-9 rounded-lg bg-white text-brand shadow-sm font-bold flex items-center justify-center gap-2" data-record-type="referral" type="button"><span class="material-symbols-outlined text-base">sync_alt</span><span>إحالة</span></button>
          <button class="h-9 rounded-lg text-slate-600 font-bold flex items-center justify-center gap-2" data-record-type="followup" type="button"><span class="material-symbols-outlined text-base">event_repeat</span><span>متابعة</span></button>
        </section>

        <!-- Patient -->
        <section class="space-y-2">
          <label class="block font-bold text-slate-700">المريض *</label>
          <div class="relative" id="rf-patient-search-box">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input class="w-full h-10 pr-9 pl-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rf-patient-search-input" placeholder="ابحث عن مريض بالاسم أو رقم المريض..." type="text">
            <div class="hidden absolute top-full right-0 left-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-dropdown z-30 py-1 max-h-48 overflow-y-auto" id="rf-patient-suggestions"></div>
          </div>
          <div class="hidden bg-blue-50/70 border border-blue-100 rounded-xl p-3 flex items-center justify-between" id="rf-selected-patient-card">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs" id="rf-patient-avatar">أ م</div><div><div class="font-bold text-slate-900 text-xs" id="rf-patient-name">أحمد محمود</div><div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-num"><span class="bg-white border border-blue-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="rf-patient-id">P-10482</span><span>•</span><span id="rf-patient-meta">42 سنة • ذكر</span></div></div></div>
            <button class="text-xs text-brand font-bold hover:underline" id="rf-change-patient" type="button">تغيير</button>
          </div>
        </section>

        <section class="space-y-1.5">
          <label class="block font-bold text-slate-700">الزيارة المرتبطة</label>
          <select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rf-form-visit">
            <option value="visit_2024_10_18">كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد</option>
            <option value="visit_2024_10_12">متابعة سريرية — 12 أكتوبر 2024 — د. محمد أحمد</option>
            <option value="none">بدون ربط بزيارة محددة</option>
          </select>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">الأولوية</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rf-form-priority"><option value="normal">عادي</option><option value="urgent">عاجل</option></select></div>
          <div class="space-y-1.5"><label class="block font-bold text-slate-700">الطبيب</label><input class="w-full h-10 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-600 cursor-not-allowed" readonly type="text" value="د. محمد أحمد - استشاري طب باطني"></div>
        </section>

        <section class="space-y-1.5">
          <label class="block font-bold text-slate-700" id="rf-reason-label">سبب الإحالة *</label>
          <textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="rf-form-reason" placeholder="اكتب السبب السريري للإحالة..." rows="2"></textarea>
        </section>

        <!-- Referral fields -->
        <div class="space-y-4" id="referral-fields">
          <section class="space-y-1.5"><label class="block font-bold text-slate-700">التخصص المطلوب *</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rf-form-specialty"><option value="">اختر التخصص</option><option>طب القلب</option><option>جراحة العظام</option><option>طب الأعصاب</option><option>الجراحة العامة</option><option>الأنف والأذن والحنجرة</option><option>طب العيون</option><option>أخرى</option></select></section>

          <section class="space-y-1.5"><label class="block font-bold text-slate-700">الملخص السريري *</label><textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="rf-form-clinical-summary" placeholder="لخّص الحالة والمعلومات السريرية الضرورية للطبيب أو الجهة المستقبلة..." rows="3"></textarea></section>
          <section class="space-y-1.5"><label class="block font-bold text-slate-700">السؤال السريري المحدد</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rf-form-clinical-question" placeholder="مثال: تقييم سبب الخفقان والحاجة لفحوص إضافية" type="text"></section>
          <section class="space-y-2"><label class="block font-bold text-slate-700">معلومات مرتبطة من السجل</label><div class="grid grid-cols-2 gap-2">
            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white"><input class="rounded text-brand focus:ring-brand" name="rf_relevant_info" type="checkbox" value="visit"><span>ملخص الزيارة الحالية</span></label>
            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white"><input class="rounded text-brand focus:ring-brand" name="rf_relevant_info" type="checkbox" value="labs"><span>نتائج مختبر ذات صلة</span></label>
            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white"><input class="rounded text-brand focus:ring-brand" name="rf_relevant_info" type="checkbox" value="imaging"><span>تقارير أشعة ذات صلة</span></label>
            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white"><input class="rounded text-brand focus:ring-brand" name="rf_relevant_info" type="checkbox" value="medications"><span>الأدوية الحالية</span></label>
          </div><p class="text-[11px] text-slate-500">يتم إرفاق السجلات الأصلية المصرح بها، وليس إنشاء نسخ منها.</p></section>
          <section class="space-y-1.5"><label class="block font-bold text-slate-700">مرفقات إضافية</label><input class="w-full text-xs text-slate-600 file:ml-3 file:border-0 file:bg-slate-100 file:text-slate-700 file:rounded-lg file:px-3 file:py-2 file:font-bold" id="rf-form-attachments" multiple type="file"><p class="text-[11px] text-slate-500">في النموذج التجريبي يتم حفظ أسماء الملفات فقط.</p></section>

          <section class="space-y-2 pt-2 border-t border-slate-100">
            <label class="block font-bold text-slate-800">جهة الإحالة</label>
            <p class="text-[11px] text-slate-500">يمكن حفظ الإحالة دون جهة محددة، بينما تجهيزها للتوجيه يتطلب اختيار طبيب أو منشأة.</p>
            <div class="space-y-2">
              <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input checked class="text-brand focus:ring-brand" name="referral_destination_mode" type="radio" value="unassigned"><div><span class="font-bold text-slate-800">بدون تحديد جهة الآن</span><p class="text-[11px] text-slate-500">توثيق الإحالة وتحديد الجهة لاحقًا</p></div></label>
              <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input class="text-brand focus:ring-brand" name="referral_destination_mode" type="radio" value="thera_network"><div><span class="font-bold text-slate-800">طبيب أو منشأة من شبكة THERA</span><p class="text-[11px] text-slate-500">اختر جهة محددة من دليل THERA</p></div></label>
              <div class="hidden pr-6 pl-2 space-y-1.5" id="rf-network-options">
                <div class="relative"><span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span><input class="w-full h-9 pr-8 pl-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="rf-network-search" placeholder="ابحث باسم الطبيب أو المنشأة..." type="text"></div>
                <p class="text-[11px] text-slate-500">جهات تجريبية للمعاينة إلى حين ربط دليل THERA الفعلي.</p>
                <div class="space-y-1.5" id="rf-network-results"></div>
              </div>
              <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"><input class="text-brand focus:ring-brand" name="referral_destination_mode" type="radio" value="external"><div><span class="font-bold text-slate-800">جهة خارجية</span><p class="text-[11px] text-slate-500">طبيب أو منشأة خارج شبكة THERA</p></div></label>
              <div class="hidden pr-6 pl-2 space-y-1.5" id="rf-external-options"><input class="w-full h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs" id="rf-form-external-name" placeholder="أدخل اسم الطبيب أو المنشأة الخارجية..." type="text"><p class="text-[11px] text-slate-500">توثيق نصي فقط؛ لا يتم إرسال الإحالة إلكترونيًا إلى جهة خارج شبكة THERA.</p></div>
            </div>
          </section>

          <section class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
            <span class="material-symbols-outlined text-amber-600 text-lg">info</span>
            <p class="text-[11px] leading-5 text-amber-800">إنشاء الإحالة أو تحديد جهة الإحالة لا ينشئ موعدًا تلقائيًا. حجز الموعد يتم ضمن مسار المواعيد بشكل مستقل.</p>
          </section>
        </div>

        <!-- Follow-up fields -->
        <div class="hidden space-y-4" id="followup-fields">
          <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="space-y-1.5"><label class="block font-bold text-slate-700">التاريخ المقترح *</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rf-followup-date" type="date"></div>
            <div class="space-y-1.5"><label class="block font-bold text-slate-700">الفترة المقترحة</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rf-followup-window"><option value="any">أي وقت</option><option value="morning">صباحًا</option><option value="afternoon">بعد الظهر</option><option value="evening">مساءً</option></select></div>
            <div class="space-y-1.5"><label class="block font-bold text-slate-700">نوع المتابعة</label><select class="rtl-select w-full h-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" id="rf-followup-kind"><option value="clinical">متابعة سريرية</option><option value="after_result">بعد ظهور نتيجة</option><option value="medication">متابعة علاج / دواء</option><option value="procedure">بعد إجراء طبي</option><option value="other">أخرى</option></select></div>
          </section>
          <section class="space-y-1.5"><label class="block font-bold text-slate-700">المطلوب قبل المتابعة</label><input class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand" id="rf-followup-requirement" placeholder="مثال: إحضار نتيجة المختبر أو تقرير الأشعة..." type="text"></section>
          <section class="space-y-1.5"><label class="block font-bold text-slate-700">تعليمات المتابعة</label><textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="rf-followup-instructions" placeholder="تعليمات للمريض قبل المتابعة أو خلالها..." rows="2"></textarea></section>
          <section class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex gap-2.5 items-start"><span class="material-symbols-outlined text-brand text-lg">event_note</span><p class="text-[11px] leading-5 text-slate-600">خطة المتابعة لا تنشئ موعدًا تلقائيًا. يمكن استخدام تاريخ المتابعة كمرجع، بينما إنشاء الموعد الفعلي يتم من شاشة المواعيد.</p></section>
        </div>

        <section class="space-y-1.5"><label class="block font-bold text-slate-700">ملاحظة داخلية (اختياري)</label><textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-brand focus:border-brand resize-none" id="rf-form-notes" placeholder="أضف أي ملاحظات مهمة..." rows="2"></textarea></section>
      </form>

      <footer class="px-6 py-4 bg-slate-50 border-t border-slate-200">
        <div class="flex items-center justify-between gap-2 whitespace-nowrap">
          <button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-xl font-bold shrink-0" id="rf-create-cancel" type="button">إلغاء</button>
          <div class="flex items-center gap-2 min-w-0">
            <button class="h-10 px-3.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl font-bold shrink-0" id="rf-save-draft" type="button">حفظ كمسودة</button>
            <button class="h-10 px-4 bg-blue-50 border border-blue-200 text-[#0369A1] hover:bg-blue-100 rounded-xl font-bold shrink-0" id="rf-save-documented" type="button">حفظ كموثق</button>
            <button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold shadow-sm shrink-0" id="rf-primary-action" type="button">تجهيز الإحالة</button>
          </div>
        </div>
      </footer>
    </div>
  </div>
</aside>

<!-- Details Drawer -->
<aside aria-labelledby="rf-details-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawer-rf-details" role="dialog">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" id="rf-details-backdrop"></div>
  <div class="fixed inset-y-0 left-0 max-w-full flex">
    <div class="w-screen max-w-xl bg-white shadow-drawer flex flex-col h-full">
      <header class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand flex items-center justify-center"><span class="material-symbols-outlined text-xl" id="rf-details-icon">sync_alt</span></div>
          <div><div class="flex items-center gap-2"><h2 class="text-base font-bold text-slate-900 font-num" id="rf-details-title">—</h2><span class="px-2 py-0.5 rounded-md text-[10px] font-bold" id="rf-details-type-badge">—</span></div><p class="text-xs text-slate-500" id="rf-details-subtitle">—</p></div>
        </div>
        <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center" id="rf-details-close" type="button"><span class="material-symbols-outlined text-xl">close</span></button>
      </header>
      <div class="flex-1 overflow-y-auto p-6 space-y-5 text-xs">
        <section class="bg-slate-50 rounded-xl p-4 border border-slate-200"><div class="text-[11px] font-bold text-slate-400 uppercase mb-2">بيانات المريض</div><div class="flex items-center justify-between gap-4"><div><div class="text-sm font-bold text-slate-900" id="rf-details-patient-name">—</div><div class="flex items-center gap-1.5 text-slate-500 font-num mt-0.5"><span class="bg-white border border-slate-200 text-slate-700 px-1.5 py-0.5 rounded text-[10px] font-medium" dir="ltr" id="rf-details-patient-id">—</span><span>•</span><span id="rf-details-patient-meta">—</span></div></div><div class="text-left"><span class="text-[11px] text-slate-400 block">الطبيب</span><span class="font-bold text-slate-800">د. محمد أحمد</span></div></div></section>

        <section class="grid grid-cols-2 gap-3"><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">الحالة</span><div id="rf-details-status"></div></div><div class="bg-white border border-slate-200 rounded-xl p-3"><span class="text-slate-400 block mb-1">الأولوية</span><div id="rf-details-priority"></div></div></section>

        <section class="space-y-3">
          <div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1" id="rf-details-reason-label">سبب الإحالة</span><div class="text-sm font-bold text-slate-900" id="rf-details-reason">—</div></div>
          <div class="border-b border-slate-100 pb-2"><span class="text-slate-400 block mb-1">الزيارة المرتبطة</span><div class="text-slate-800" id="rf-details-visit">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-clinical-summary-row"><span class="text-slate-400 block mb-1">الملخص السريري</span><div class="text-slate-800" id="rf-details-clinical-summary">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-clinical-question-row"><span class="text-slate-400 block mb-1">السؤال السريري</span><div class="text-slate-800" id="rf-details-clinical-question">—</div></div>
          <div class="border-b border-slate-100 pb-2" id="rf-details-target-row"><span class="text-slate-400 block mb-1">جهة الإحالة</span><div class="text-slate-800 font-medium" id="rf-details-target">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-followup-date-row"><span class="text-slate-400 block mb-1">موعد المتابعة</span><div class="text-slate-800 font-medium" id="rf-details-followup-date">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-requirement-row"><span class="text-slate-400 block mb-1">المطلوب قبل المتابعة</span><div class="text-slate-800" id="rf-details-requirement">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-instructions-row"><span class="text-slate-400 block mb-1">تعليمات المتابعة</span><div class="text-slate-800" id="rf-details-instructions">—</div></div>
          <div class="border-b border-slate-100 pb-2 hidden" id="rf-details-related-row"><span class="text-slate-400 block mb-1">معلومات مرتبطة / مرفقات</span><div class="text-slate-800" id="rf-details-related">—</div></div>
          <div><span class="text-slate-400 block mb-1">ملاحظات إضافية</span><div class="text-slate-800" id="rf-details-notes">—</div></div>
        </section>

        <section class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start" id="rf-details-appointment-note"><span class="material-symbols-outlined text-amber-600 text-lg">info</span><p class="text-[11px] leading-5 text-amber-800" id="rf-details-appointment-copy">—</p></section>

        <section class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden" id="rf-details-action-box">
          <div class="flex items-start gap-3"><span class="material-symbols-outlined text-brand text-xl" id="rf-details-action-icon">task_alt</span><div class="flex-1"><h4 class="font-bold text-slate-900 text-xs mb-1" id="rf-details-action-title">—</h4><p class="text-[11px] text-slate-600 mb-3" id="rf-details-action-copy">—</p><button class="h-8 px-4 bg-brand hover:bg-brand-hover text-white rounded-lg font-bold text-xs" id="rf-details-action-btn" type="button">—</button></div></div>
        </section>
      </div>
      <footer class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between"><button class="h-10 px-4 text-slate-600 hover:bg-slate-200/60 rounded-xl font-bold" id="rf-details-close-footer" type="button">إغلاق</button><div class="flex items-center gap-2"><button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-bold flex items-center gap-2" id="rf-open-visit-btn" type="button"><span class="material-symbols-outlined text-base">stethoscope</span><span>فتح الزيارة</span></button><button class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-bold flex items-center gap-2" id="rf-print-btn" type="button"><span class="material-symbols-outlined text-base">print</span><span>طباعة</span></button></div></footer>
    </div>
  </div>
</aside>

<!-- Context Menu + Toasts -->
<div class="hidden fixed bg-white rounded-xl shadow-lg border border-[#E2E8F0] p-1.5 z-[80] text-slate-700 text-xs w-[220px] font-medium" dir="rtl" id="rf-row-menu"></div>
<div class="fixed bottom-6 left-6 z-[90] flex flex-col gap-2 pointer-events-none" id="rf-toast-container"></div>

<script data-purpose="referrals-followups-app">
(() => {
  'use strict';

  const STORAGE_KEY = 'thera_referrals_followups_doctor_demo';
  const VERSION_KEY = 'thera_referrals_followups_doctor_demo_version';
  const STORAGE_VERSION = '2';
  const SIDEBAR_PIN_KEY = 'thera_sidebar_pinned';

  const DEMO_PATIENTS = [
    {id:'P-10482',name:'أحمد محمود',initials:'أ م',age:'42',gender:'ذكر'},
    {id:'P-10483',name:'سارة خالد',initials:'س خ',age:'29',gender:'أنثى'},
    {id:'P-10484',name:'عمر ياسين',initials:'ع ي',age:'58',gender:'ذكر'}
  ];

  const DEMO_NETWORK_TARGETS = [
    {id:'DR-201',name:'د. سارة الخطيب',meta:'استشارية قلب — طبيب ضمن شبكة THERA'},
    {id:'CL-102',name:'عيادة THERA للقلب',meta:'منشأة طبية — شبكة THERA'},
    {id:'DR-305',name:'د. يوسف منصور',meta:'استشاري جراحة عظام — طبيب ضمن شبكة THERA'},
    {id:'CL-211',name:'مركز THERA للتخصصات',meta:'منشأة متعددة التخصصات — شبكة THERA'}
  ];

  const PREVIEW_RECORDS = [
    {id:'ref_preview_1',recordNumber:'REF-10482-3812',recordType:'referral',patientId:'P-10482',patientName:'أحمد محمود',patientInitials:'أ م',patientAge:'42',patientGender:'ذكر',visitTitle:'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد',reason:'خفقان متكرر مع حاجة لتقييم تخصصي',priority:'urgent',status:'documented',specialty:'طب القلب',destinationMode:'unassigned',targetId:'',targetName:'',followupDate:'',followupKind:'',followupRequirement:'',notes:'يرجى مراجعة تخطيط القلب والنتائج السابقة.',createdAt:'2026-10-04T10:10:00',updatedAt:'2026-10-04T10:10:00'},
    {id:'ref_preview_2',recordNumber:'REF-10484-7410',recordType:'referral',patientId:'P-10484',patientName:'عمر ياسين',patientInitials:'ع ي',patientAge:'58',patientGender:'ذكر',visitTitle:'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد',reason:'ألم ركبة مزمن مع محدودية حركة',priority:'normal',status:'route_requested',specialty:'جراحة العظام',destinationMode:'thera_network',targetId:'DR-305',targetName:'د. يوسف منصور',followupDate:'',followupKind:'',followupRequirement:'',notes:'',createdAt:'2026-10-03T09:20:00',updatedAt:'2026-10-04T08:30:00'},
    {id:'fup_preview_1',recordNumber:'FUP-10483-1942',recordType:'followup',patientId:'P-10483',patientName:'سارة خالد',patientInitials:'س خ',patientAge:'29',patientGender:'أنثى',visitTitle:'متابعة سريرية — 12 أكتوبر 2024 — د. محمد أحمد',reason:'مراجعة الاستجابة للعلاج بعد أسبوعين',priority:'normal',status:'scheduled',specialty:'',destinationMode:'unassigned',targetId:'',targetName:'',followupDate:'2026-10-12',followupKind:'medication',followupRequirement:'إحضار قائمة الأدوية الحالية',notes:'',createdAt:'2026-10-02T12:00:00',updatedAt:'2026-10-02T12:00:00'},
    {id:'fup_preview_2',recordNumber:'FUP-10482-5609',recordType:'followup',patientId:'P-10482',patientName:'أحمد محمود',patientInitials:'أ م',patientAge:'42',patientGender:'ذكر',visitTitle:'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد',reason:'متابعة بعد ظهور نتيجة المختبر',priority:'normal',status:'due',specialty:'',destinationMode:'unassigned',targetId:'',targetName:'',followupDate:'2026-10-04',followupKind:'after_result',followupRequirement:'مراجعة نتيجة CBC',notes:'المتابعة مستحقة اليوم.',createdAt:'2026-10-01T11:20:00',updatedAt:'2026-10-04T07:45:00'}
  ];

  let records = [];
  let currentTab = 'all';
  let selectedPatient = null;
  let selectedNetworkTarget = null;
  let currentRecordType = 'referral';
  let activeRecordId = null;
  let isPinned = true;

  function safeString(v){ return typeof v === 'string' ? v : ''; }
  function validStatus(type,status){
    const referral = ['draft','documented','route_requested','completed','cancelled'];
    const followup = ['draft','required','appointment_created','completed','cancelled'];
    if(type === 'followup' && (status === 'scheduled' || status === 'due')) status = 'required';
    return (type === 'followup' ? followup : referral).includes(status) ? status : 'draft';
  }
  function normalizeRecord(raw){
    if(!raw || typeof raw !== 'object') return null;
    const type = raw.recordType === 'followup' ? 'followup' : 'referral';
    const id = safeString(raw.id); const recordNumber = safeString(raw.recordNumber);
    if(!id || !recordNumber) return null;
    return {
      id, recordNumber, recordType:type,
      patientId:safeString(raw.patientId), patientName:safeString(raw.patientName), patientInitials:safeString(raw.patientInitials), patientAge:safeString(String(raw.patientAge ?? '')), patientGender:safeString(raw.patientGender),
      visitTitle:safeString(raw.visitTitle), reason:safeString(raw.reason), priority:raw.priority === 'urgent' ? 'urgent':'normal', status:validStatus(type,raw.status), specialty:safeString(raw.specialty), destinationMode:['unassigned','thera_network','external'].includes(raw.destinationMode)?raw.destinationMode:'unassigned', targetId:safeString(raw.targetId), targetName:safeString(raw.targetName),
      clinicalSummary:safeString(raw.clinicalSummary), clinicalQuestion:safeString(raw.clinicalQuestion), relevantInfo:Array.isArray(raw.relevantInfo)?raw.relevantInfo.map(safeString).filter(Boolean):[], attachments:Array.isArray(raw.attachments)?raw.attachments.map(safeString).filter(Boolean):[],
      followupDate:safeString(raw.followupDate), followupWindow:safeString(raw.followupWindow)||'any', followupKind:safeString(raw.followupKind), followupRequirement:safeString(raw.followupRequirement), followupInstructions:safeString(raw.followupInstructions), appointmentId:safeString(raw.appointmentId), notes:safeString(raw.notes), createdAt:safeString(raw.createdAt), updatedAt:safeString(raw.updatedAt)
    };
  }
  function initStorage(){
    const ver = localStorage.getItem(VERSION_KEY);
    if(ver !== STORAGE_VERSION){ localStorage.setItem(STORAGE_KEY,'[]'); localStorage.setItem(VERSION_KEY,STORAGE_VERSION); records=[]; return; }
    try { const parsed=JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); records=Array.isArray(parsed)?parsed.map(normalizeRecord).filter(Boolean):[]; } catch { records=[]; }
    if(location.hash === '#preview' && records.length === 0){ records = PREVIEW_RECORDS.map(normalizeRecord).filter(Boolean); }
  }
  function save(){
    if(location.hash === '#preview') return;
    records=records.map(normalizeRecord).filter(Boolean); localStorage.setItem(STORAGE_KEY,JSON.stringify(records));
  }
  function patientMeta(r){ return `${r.patientAge ? r.patientAge+' سنة':'—'} • ${r.patientGender || '—'}`; }
  function formatArabicDate(iso){ if(!iso) return '—'; const d=new Date(iso+'T12:00:00'); if(Number.isNaN(d.getTime())) return iso; return d.toLocaleDateString('ar-EG-u-ca-gregory',{day:'numeric',month:'long',year:'numeric'}); }
  function formatDateTime(iso){ if(!iso) return '—'; const d=new Date(iso); if(Number.isNaN(d.getTime())) return '—'; return d.toLocaleDateString('ar-EG-u-ca-gregory',{day:'numeric',month:'short',year:'numeric'}); }
  function visitLabel(value){ if(value==='visit_2024_10_18') return 'كشفية طب باطني — 18 أكتوبر 2024 — د. محمد أحمد'; if(value==='visit_2024_10_12') return 'متابعة سريرية — 12 أكتوبر 2024 — د. محمد أحمد'; return ''; }
  function visitValue(label){ if(label.includes('18 أكتوبر 2024')) return 'visit_2024_10_18'; if(label.includes('12 أكتوبر 2024')) return 'visit_2024_10_12'; return 'none'; }
  function isActionNeeded(r){ return (r.recordType==='referral' && (r.status==='documented' || r.status==='route_requested')) || (r.recordType==='followup' && r.status==='required'); }
  function statusLabel(r){ const m={draft:'مسودة',documented:'موثقة',route_requested:'طلب توجيه',required:'مطلوبة',appointment_created:'تم إنشاء موعد',completed:'مكتملة',cancelled:'ملغاة'}; return m[r.status]||'مسودة'; }
  function statusBadge(r){
    const s=document.createElement('span'); s.className='inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border ';
    const cls={draft:'bg-amber-50 text-amber-700 border-amber-200',documented:'bg-blue-50 text-blue-700 border-blue-200',route_requested:'bg-sky-50 text-sky-700 border-sky-200',required:'bg-amber-50 text-amber-800 border-amber-200',appointment_created:'bg-blue-50 text-blue-700 border-blue-200',completed:'bg-emerald-50 text-emerald-700 border-emerald-200',cancelled:'bg-slate-100 text-slate-600 border-slate-200'};
    s.className += cls[r.status] || cls.draft; s.textContent=statusLabel(r); return s;
  }
  function typeBadge(r){ const s=document.createElement('span'); s.className=r.recordType==='referral'?'inline-flex px-2 py-0.5 rounded-md bg-blue-50 text-brand border border-blue-200 text-[10px] font-bold':'inline-flex px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold'; s.textContent=r.recordType==='referral'?'إحالة':'متابعة'; return s; }
  function priorityBadge(r){ const s=document.createElement('span'); s.className=r.priority==='urgent'?'inline-flex px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-200 text-[10px] font-bold':'inline-flex px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium'; s.textContent=r.priority==='urgent'?'عاجل':'عادي'; return s; }
  function targetText(r){ if(r.recordType==='followup') return formatArabicDate(r.followupDate); if(r.destinationMode==='unassigned') return 'غير محددة'; return r.targetName || '—'; }
  function detailsText(r){ if(r.recordType==='referral') return r.specialty ? `${r.specialty} — ${r.reason || '—'}` : (r.reason || '—'); const req=r.followupRequirement ? ` • ${r.followupRequirement}`:''; return `${r.reason || '—'}${req}`; }

  function updateSummary(){
    document.getElementById('summary-total').textContent=records.length;
    document.getElementById('summary-referrals').textContent=records.filter(r=>r.recordType==='referral'&&!['completed','cancelled'].includes(r.status)).length;
    document.getElementById('summary-action-needed').textContent=records.filter(isActionNeeded).length;
    document.getElementById('summary-completed').textContent=records.filter(r=>r.status==='completed').length;
    document.getElementById('tab-count-all').textContent=records.length;
    document.getElementById('tab-count-referral').textContent=records.filter(r=>r.recordType==='referral').length;
    document.getElementById('tab-count-followup').textContent=records.filter(r=>r.recordType==='followup').length;
    document.getElementById('tab-count-action-needed').textContent=records.filter(isActionNeeded).length;
    document.getElementById('tab-count-completed').textContent=records.filter(r=>r.status==='completed').length;
  }

  function filteredRecords(){
    const q=safeString(document.getElementById('rf-filter-search').value).toLowerCase();
    const type=document.getElementById('rf-filter-type').value; const status=document.getElementById('rf-filter-status').value; const priority=document.getElementById('rf-filter-priority').value; const sort=document.getElementById('rf-filter-sort').value;
    let list=records.filter(r=>{
      if(currentTab==='referral'&&r.recordType!=='referral') return false; if(currentTab==='followup'&&r.recordType!=='followup') return false; if(currentTab==='action_needed'&&!isActionNeeded(r)) return false; if(currentTab==='completed'&&r.status!=='completed') return false;
      if(type&&r.recordType!==type) return false; if(status&&r.status!==status) return false; if(priority&&r.priority!==priority) return false;
      if(q){ const hay=[r.patientName,r.patientId,r.recordNumber,r.reason,r.specialty,r.targetName,r.followupRequirement].join(' ').toLowerCase(); if(!hay.includes(q)) return false; }
      return true;
    });
    list.sort((a,b)=>{
      if(sort==='oldest') return (a.updatedAt||a.createdAt).localeCompare(b.updatedAt||b.createdAt);
      if(sort==='due_first') return (a.followupDate||'9999-12-31').localeCompare(b.followupDate||'9999-12-31');
      if(sort==='urgent'){ if(a.priority==='urgent'&&b.priority!=='urgent') return -1; if(b.priority==='urgent'&&a.priority!=='urgent') return 1; }
      return (b.updatedAt||b.createdAt).localeCompare(a.updatedAt||a.createdAt);
    });
    return list;
  }

  function appendEmptyRow(tbody, filtered){
    const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=9; td.className='py-12 px-4 text-center';
    const wrap=document.createElement('div'); wrap.className='flex flex-col items-center justify-center';
    const icon=document.createElement('span'); icon.className='material-symbols-outlined text-4xl text-slate-300 mb-2'; icon.textContent=filtered?'search_off':'sync_alt';
    const h=document.createElement('div'); h.className='text-sm font-bold text-slate-700'; h.textContent=filtered?'لا توجد سجلات مطابقة للفلاتر الحالية':'لا توجد إحالات أو متابعات مسجلة';
    const p=document.createElement('div'); p.className='text-xs text-slate-400 mt-1'; p.textContent=filtered?'جرّب تعديل البحث أو إعادة ضبط الفلاتر.':'يمكنك إنشاء إحالة أو متابعة جديدة من الأزرار أعلى الصفحة.';
    wrap.append(icon,h,p); td.append(wrap); tr.append(td); tbody.append(tr);
  }

  function render(){
    updateSummary(); const tbody=document.getElementById('rf-table-body'); const mobile=document.getElementById('rf-mobile-container'); tbody.replaceChildren(); mobile.replaceChildren();
    const list=filteredRecords(); const anyFilter=currentTab!=='all'||document.getElementById('rf-filter-search').value||document.getElementById('rf-filter-type').value||document.getElementById('rf-filter-status').value||document.getElementById('rf-filter-priority').value;
    if(list.length===0){ appendEmptyRow(tbody,Boolean(anyFilter)); return; }
    list.forEach(r=>{
      const tr=document.createElement('tr'); tr.className='hover:bg-slate-50/80 transition-colors';
      const tdP=document.createElement('td'); tdP.className='py-3.5 px-4'; const pw=document.createElement('div'); pw.className='flex items-center gap-2.5'; const av=document.createElement('div'); av.className='w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0'; av.textContent=r.patientInitials||'—'; const pi=document.createElement('div'); const pn=document.createElement('div'); pn.className='font-bold text-slate-900'; pn.textContent=r.patientName||'—'; const pm=document.createElement('div'); pm.className='text-[11px] text-slate-400 font-num'; pm.textContent=`${r.patientId||'—'} • ${patientMeta(r)}`; pi.append(pn,pm); pw.append(av,pi); tdP.append(pw);
      const tdR=document.createElement('td'); tdR.className='py-3.5 px-4'; const rn=document.createElement('div'); rn.className='font-bold font-num text-brand cursor-pointer hover:underline'; rn.textContent=r.recordNumber; rn.addEventListener('click',()=>openDetails(r.id)); const rb=document.createElement('div'); rb.className='mt-1'; rb.append(typeBadge(r)); tdR.append(rn,rb);
      const tdD=document.createElement('td'); tdD.className='py-3.5 px-4 max-w-[260px]'; const d1=document.createElement('div'); d1.className='font-medium text-slate-800 truncate'; d1.textContent=detailsText(r); tdD.append(d1);
      const tdV=document.createElement('td'); tdV.className='py-3.5 px-4 text-slate-500 max-w-[170px] truncate'; tdV.textContent=r.visitTitle?r.visitTitle.split('—')[0].trim():'—';
      const tdT=document.createElement('td'); tdT.className='py-3.5 px-4 text-slate-700 max-w-[170px]'; const t1=document.createElement('div'); t1.className='font-medium truncate'; t1.textContent=targetText(r); const t2=document.createElement('div'); t2.className='text-[10px] text-slate-400 mt-0.5'; t2.textContent=r.recordType==='referral'?(r.destinationMode==='thera_network'?'شبكة THERA':r.destinationMode==='external'?'جهة خارجية':'يُحدد لاحقًا'):(r.followupKind==='after_result'?'بعد نتيجة':r.followupKind==='medication'?'علاج / دواء':'متابعة سريرية'); tdT.append(t1,t2);
      const tdPr=document.createElement('td'); tdPr.className='py-3.5 px-4'; tdPr.append(priorityBadge(r));
      const tdS=document.createElement('td'); tdS.className='py-3.5 px-4'; tdS.append(statusBadge(r));
      const tdU=document.createElement('td'); tdU.className='py-3.5 px-4 text-slate-500 font-num whitespace-nowrap'; tdU.textContent=formatDateTime(r.updatedAt||r.createdAt);
      const tdA=document.createElement('td'); tdA.className='py-3.5 px-4 text-center'; const btn=document.createElement('button'); btn.type='button'; btn.className='w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center mx-auto'; const ic=document.createElement('span'); ic.className='material-symbols-outlined text-lg'; ic.textContent='more_vert'; btn.append(ic); btn.addEventListener('click',e=>{e.stopPropagation();openMenu(e,r.id);}); tdA.append(btn);
      tr.append(tdP,tdR,tdD,tdV,tdT,tdPr,tdS,tdU,tdA); tbody.append(tr);
      renderMobile(r,mobile);
    });
  }

  function renderMobile(r,container){
    const card=document.createElement('div'); card.className='bg-white border border-slate-200 rounded-xl p-4 shadow-sm space-y-3';
    const top=document.createElement('div'); top.className='flex items-center justify-between'; const left=document.createElement('div'); left.className='flex items-center gap-2'; const num=document.createElement('span'); num.className='font-bold font-num text-brand text-xs'; num.textContent=r.recordNumber; left.append(num,typeBadge(r)); const btn=document.createElement('button'); btn.className='w-7 h-7 rounded-lg text-slate-400 hover:bg-slate-100 flex items-center justify-center'; btn.type='button'; const i=document.createElement('span'); i.className='material-symbols-outlined text-base'; i.textContent='more_vert'; btn.append(i); btn.addEventListener('click',e=>{e.stopPropagation();openMenu(e,r.id);}); top.append(left,btn);
    const p=document.createElement('div'); p.className='font-bold text-slate-900 text-sm'; p.textContent=r.patientName; const meta=document.createElement('div'); meta.className='text-[11px] text-slate-400'; meta.textContent=`${r.patientId} • ${patientMeta(r)}`;
    const details=document.createElement('div'); details.className='bg-slate-50 rounded-lg p-3'; const dh=document.createElement('div'); dh.className='text-xs font-bold text-slate-800'; dh.textContent=detailsText(r); const ds=document.createElement('div'); ds.className='text-[11px] text-slate-500 mt-1'; ds.textContent=r.recordType==='referral'?`الجهة: ${targetText(r)}`:`موعد المتابعة: ${targetText(r)}`; details.append(dh,ds);
    const foot=document.createElement('div'); foot.className='flex items-center justify-between pt-1'; const badges=document.createElement('div'); badges.className='flex items-center gap-1.5'; badges.append(statusBadge(r),priorityBadge(r)); const upd=document.createElement('span'); upd.className='text-[10px] text-slate-400 font-num'; upd.textContent=formatDateTime(r.updatedAt||r.createdAt); foot.append(badges,upd); card.append(top,p,meta,details,foot); container.append(card);
  }

  function showToast(message,type='info'){
    const c=document.getElementById('rf-toast-container'); const t=document.createElement('div'); const cls=type==='warning'?'border-amber-200 bg-amber-50 text-amber-800':type==='success'?'border-emerald-200 bg-emerald-50 text-emerald-800':'border-blue-200 bg-blue-50 text-blue-800'; t.className=`pointer-events-auto min-w-[280px] max-w-sm border ${cls} rounded-xl shadow-lg px-4 py-3 text-xs font-medium`; t.textContent=message; c.append(t); setTimeout(()=>t.remove(),2800);
  }

  function syncBodyScrollLock(){ const open=!document.getElementById('drawer-rf-create').classList.contains('hidden')||!document.getElementById('drawer-rf-details').classList.contains('hidden'); document.body.classList.toggle('overflow-hidden',open); }

  function setRecordType(type){
    currentRecordType=type==='followup'?'followup':'referral'; document.getElementById('rf-form-type').value=currentRecordType;
    document.querySelectorAll('#rf-type-switch button').forEach(b=>{ const active=b.dataset.recordType===currentRecordType; b.className=active?'h-9 rounded-lg bg-white text-brand shadow-sm font-bold flex items-center justify-center gap-2':'h-9 rounded-lg text-slate-600 font-bold flex items-center justify-center gap-2'; });
    const isRef=currentRecordType==='referral'; document.getElementById('referral-fields').classList.toggle('hidden',!isRef); document.getElementById('followup-fields').classList.toggle('hidden',isRef);
    document.getElementById('rf-reason-label').textContent=isRef?'سبب الإحالة *':'سبب المتابعة *'; document.getElementById('rf-form-reason').placeholder=isRef?'اكتب السبب السريري للإحالة...':'اكتب سبب المتابعة وما المطلوب تقييمه...'; document.getElementById('rf-create-icon').textContent=isRef?'sync_alt':'event_repeat'; document.getElementById('rf-create-title').textContent=isRef?'إحالة جديدة':'متابعة جديدة'; document.getElementById('rf-create-subtitle').textContent=isRef?'إنشاء إحالة مرتبطة بالمريض والزيارة':'إنشاء خطة متابعة مرتبطة بالمريض والزيارة'; document.getElementById('rf-save-documented').textContent='حفظ كموثق'; document.getElementById('rf-save-documented').classList.toggle('hidden',!isRef); document.getElementById('rf-primary-action').textContent=isRef?'تجهيز الإحالة':'حفظ المتابعة';
  }

  function choosePatient(p){ selectedPatient=p; document.getElementById('rf-patient-name').textContent=p.name; document.getElementById('rf-patient-avatar').textContent=p.initials; document.getElementById('rf-patient-id').textContent=p.id; document.getElementById('rf-patient-meta').textContent=`${p.age} سنة • ${p.gender}`; document.getElementById('rf-patient-search-box').classList.add('hidden'); document.getElementById('rf-selected-patient-card').classList.remove('hidden'); document.getElementById('rf-patient-suggestions').classList.add('hidden'); }

  function renderNetworkTargets(query=''){
    const box=document.getElementById('rf-network-results'); box.replaceChildren(); const q=query.trim().toLowerCase(); const list=DEMO_NETWORK_TARGETS.filter(x=>!q||`${x.name} ${x.meta}`.toLowerCase().includes(q));
    list.forEach(x=>{ const b=document.createElement('button'); b.type='button'; const selected=selectedNetworkTarget&&selectedNetworkTarget.id===x.id; b.className=`w-full text-right p-2.5 rounded-lg border flex items-center justify-between gap-3 ${selected?'border-brand bg-blue-50':'border-slate-200 bg-white hover:bg-slate-50'}`; const d=document.createElement('div'); const n=document.createElement('div'); n.className='font-bold text-slate-800 text-xs'; n.textContent=x.name; const m=document.createElement('div'); m.className='text-[10px] text-slate-400 mt-0.5'; m.textContent=x.meta; d.append(n,m); const check=document.createElement('span'); check.className=`material-symbols-outlined text-lg ${selected?'text-brand':'text-slate-300'}`; check.textContent=selected?'check_circle':'radio_button_unchecked'; b.append(d,check); b.addEventListener('click',()=>{selectedNetworkTarget={id:x.id,name:x.name}; document.getElementById('rf-network-search').value=x.name; renderNetworkTargets(x.name);}); box.append(b); });
  }

  function openCreate(type='referral',editId=''){
    const form=document.getElementById('rf-form'); form.reset(); selectedPatient=null; selectedNetworkTarget=null; document.getElementById('rf-form-id').value=''; document.getElementById('rf-patient-search-box').classList.remove('hidden'); document.getElementById('rf-selected-patient-card').classList.add('hidden'); document.getElementById('rf-network-options').classList.add('hidden'); document.getElementById('rf-external-options').classList.add('hidden'); renderNetworkTargets(); setRecordType(type);
    if(editId){ const r=records.find(x=>x.id===editId); if(r){ setRecordType(r.recordType); document.getElementById('rf-form-id').value=r.id; document.getElementById('rf-create-title').textContent=`تعديل ${r.recordType==='referral'?'الإحالة':'المتابعة'} (${r.recordNumber})`; choosePatient({id:r.patientId,name:r.patientName,initials:r.patientInitials,age:r.patientAge,gender:r.patientGender}); document.getElementById('rf-form-visit').value=visitValue(r.visitTitle); document.getElementById('rf-form-priority').value=r.priority; document.getElementById('rf-form-reason').value=r.reason; document.getElementById('rf-form-notes').value=r.notes; if(r.recordType==='referral'){ document.getElementById('rf-form-specialty').value=r.specialty; document.getElementById('rf-form-clinical-summary').value=r.clinicalSummary||''; document.getElementById('rf-form-clinical-question').value=r.clinicalQuestion||''; document.querySelectorAll('input[name="rf_relevant_info"]').forEach(x=>x.checked=(r.relevantInfo||[]).includes(x.value)); const radio=document.querySelector(`input[name="referral_destination_mode"][value="${r.destinationMode}"]`); if(radio) radio.checked=true; if(r.destinationMode==='thera_network'){document.getElementById('rf-network-options').classList.remove('hidden'); if(r.targetId&&r.targetName) selectedNetworkTarget={id:r.targetId,name:r.targetName}; document.getElementById('rf-network-search').value=r.targetName; renderNetworkTargets(r.targetName);} if(r.destinationMode==='external'){document.getElementById('rf-external-options').classList.remove('hidden');document.getElementById('rf-form-external-name').value=r.targetName;} } else { document.getElementById('rf-followup-date').value=r.followupDate; document.getElementById('rf-followup-window').value=r.followupWindow||'any'; document.getElementById('rf-followup-kind').value=r.followupKind||'clinical'; document.getElementById('rf-followup-requirement').value=r.followupRequirement; document.getElementById('rf-followup-instructions').value=r.followupInstructions||''; } }}
    document.getElementById('drawer-rf-create').classList.remove('hidden'); syncBodyScrollLock();
  }
  function closeCreate(){ document.getElementById('drawer-rf-create').classList.add('hidden'); syncBodyScrollLock(); }

  function saveRecord(mode){
    if(!selectedPatient){ showToast('يرجى اختيار المريض أولاً','warning'); return; }
    const reason=document.getElementById('rf-form-reason').value.trim();
    const visit=visitLabel(document.getElementById('rf-form-visit').value);
    const priority=document.getElementById('rf-form-priority').value;
    const notes=document.getElementById('rf-form-notes').value.trim();
    const editId=document.getElementById('rf-form-id').value;
    if(mode!=='draft'&&!reason){ showToast(currentRecordType==='referral'?'أدخل سبب الإحالة قبل الحفظ':'أدخل سبب المتابعة قبل الحفظ','warning'); return; }

    let status='draft', specialty='', destinationMode='unassigned', targetId='', targetName='';
    let clinicalSummary='', clinicalQuestion='', relevantInfo=[], attachments=[];
    let followupDate='', followupWindow='any', followupKind='', followupRequirement='', followupInstructions='', appointmentId='';

    if(currentRecordType==='referral'){
      specialty=document.getElementById('rf-form-specialty').value;
      clinicalSummary=document.getElementById('rf-form-clinical-summary').value.trim();
      clinicalQuestion=document.getElementById('rf-form-clinical-question').value.trim();
      relevantInfo=Array.from(document.querySelectorAll('input[name="rf_relevant_info"]:checked')).map(x=>x.value);
      attachments=Array.from(document.getElementById('rf-form-attachments').files||[]).map(f=>f.name);
      destinationMode=document.querySelector('input[name="referral_destination_mode"]:checked')?.value||'unassigned';
      if(destinationMode==='thera_network'&&selectedNetworkTarget){targetId=selectedNetworkTarget.id;targetName=selectedNetworkTarget.name;}
      if(destinationMode==='external') targetName=document.getElementById('rf-form-external-name').value.trim();

      if(mode!=='draft'){
        if(!specialty){showToast('اختر التخصص المطلوب قبل الحفظ','warning');return;}
        if(!clinicalSummary){showToast('أدخل الملخص السريري قبل الحفظ','warning');return;}
        if(destinationMode==='unassigned'){showToast('حدد جهة الإحالة قبل توثيقها','warning');return;}
        if(destinationMode==='thera_network'&&!selectedNetworkTarget){showToast('اختر طبيبًا أو منشأة من شبكة THERA','warning');return;}
        if(destinationMode==='external'&&!targetName){showToast('أدخل اسم الجهة الخارجية','warning');return;}
      }
      status=mode==='primary'?'route_requested':mode==='documented'?'documented':'draft';
    } else {
      followupDate=document.getElementById('rf-followup-date').value;
      followupWindow=document.getElementById('rf-followup-window').value;
      followupKind=document.getElementById('rf-followup-kind').value;
      followupRequirement=document.getElementById('rf-followup-requirement').value.trim();
      followupInstructions=document.getElementById('rf-followup-instructions').value.trim();
      if(mode!=='draft'&&!followupDate){showToast('حدد التاريخ المقترح للمتابعة قبل الحفظ','warning');return;}
      status=mode==='draft'?'draft':'required';
    }

    const now=new Date(); const suffix=selectedPatient.id.replace(/\D/g,''); const unique=String(Date.now()).slice(-4); const prefixNum=currentRecordType==='referral'?'REF':'FUP';
    const payload={patientId:selectedPatient.id,patientName:selectedPatient.name,patientInitials:selectedPatient.initials,patientAge:selectedPatient.age,patientGender:selectedPatient.gender,visitTitle:visit,reason,priority,status,specialty,destinationMode,targetId,targetName,clinicalSummary,clinicalQuestion,relevantInfo,attachments,followupDate,followupWindow,followupKind,followupRequirement,followupInstructions,appointmentId,notes,updatedAt:now.toISOString()};
    if(editId){
      const idx=records.findIndex(x=>x.id===editId); if(idx<0)return; const old=records[idx];
      if(!attachments.length) payload.attachments=old.attachments||[];
      payload.appointmentId=old.appointmentId||'';
      records[idx]=normalizeRecord({...old,...payload});
    } else {
      const rec=normalizeRecord({id:`${currentRecordType==='referral'?'ref':'fup'}_${Date.now()}`,recordNumber:`${prefixNum}-${suffix}-${unique}`,recordType:currentRecordType,...payload,createdAt:now.toISOString()});
      if(rec) records.unshift(rec);
    }
    save(); render(); closeCreate();
    showToast(currentRecordType==='referral'?(status==='route_requested'?'تم تجهيز الإحالة للتوجيه دون إنشاء موعد تلقائي':'تم حفظ الإحالة'):'تم حفظ المتابعة كمتابعة مطلوبة؛ لم يتم إنشاء موعد تلقائيًا','info');
  }

  function openDetails(id){
    const r=records.find(x=>x.id===id); if(!r)return; activeRecordId=id;
    document.getElementById('rf-details-title').textContent=r.recordNumber;
    const tb=document.getElementById('rf-details-type-badge'); tb.textContent=r.recordType==='referral'?'إحالة':'متابعة'; tb.className=r.recordType==='referral'?'px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-brand border border-blue-200':'px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200';
    document.getElementById('rf-details-icon').textContent=r.recordType==='referral'?'sync_alt':'event_repeat';
    document.getElementById('rf-details-subtitle').textContent=`آخر تحديث: ${formatDateTime(r.updatedAt||r.createdAt)}`;
    document.getElementById('rf-details-patient-name').textContent=r.patientName; document.getElementById('rf-details-patient-id').textContent=r.patientId; document.getElementById('rf-details-patient-meta').textContent=patientMeta(r);
    document.getElementById('rf-details-status').replaceChildren(statusBadge(r)); document.getElementById('rf-details-priority').replaceChildren(priorityBadge(r));
    document.getElementById('rf-details-reason-label').textContent=r.recordType==='referral'?'سبب الإحالة':'سبب المتابعة'; document.getElementById('rf-details-reason').textContent=r.reason||'—'; document.getElementById('rf-details-visit').textContent=r.visitTitle||'—'; document.getElementById('rf-details-notes').textContent=r.notes||'—';

    const isRef=r.recordType==='referral';
    document.getElementById('rf-details-target-row').classList.toggle('hidden',!isRef);
    document.getElementById('rf-details-clinical-summary-row').classList.toggle('hidden',!isRef);
    document.getElementById('rf-details-clinical-question-row').classList.toggle('hidden',!isRef || !r.clinicalQuestion);
    document.getElementById('rf-details-followup-date-row').classList.toggle('hidden',isRef);
    document.getElementById('rf-details-requirement-row').classList.toggle('hidden',isRef);
    document.getElementById('rf-details-instructions-row').classList.toggle('hidden',isRef || !r.followupInstructions);
    document.getElementById('rf-details-related-row').classList.toggle('hidden',!isRef);
    document.getElementById('rf-details-target').textContent=isRef?`${r.targetName||'غير محددة'}${r.specialty?' — '+r.specialty:''}`:'—';
    document.getElementById('rf-details-clinical-summary').textContent=r.clinicalSummary||'—';
    document.getElementById('rf-details-clinical-question').textContent=r.clinicalQuestion||'—';
    const windowLabels={any:'أي وقت',morning:'صباحًا',afternoon:'بعد الظهر',evening:'مساءً'};
    document.getElementById('rf-details-followup-date').textContent=`${formatArabicDate(r.followupDate)}${r.followupWindow&&r.followupWindow!=='any'?' • '+(windowLabels[r.followupWindow]||r.followupWindow):''}`;
    document.getElementById('rf-details-requirement').textContent=r.followupRequirement||'—';
    document.getElementById('rf-details-instructions').textContent=r.followupInstructions||'—';
    const relLabels={visit:'ملخص الزيارة',labs:'نتائج المختبر',imaging:'تقارير الأشعة',medications:'الأدوية الحالية'};
    const related=[...(r.relevantInfo||[]).map(x=>relLabels[x]||x),...(r.attachments||[]).map(x=>'مرفق: '+x)];
    document.getElementById('rf-details-related').textContent=related.length?related.join(' • '):'لا توجد إضافات';
    document.getElementById('rf-details-appointment-copy').textContent=isRef?'الإحالة لا تنشئ موعدًا تلقائيًا. إذا كانت الجهة خارج THERA فهي موثقة نصيًا فقط دون إرسال إلكتروني.':(r.status==='appointment_created'?`تم إنشاء موعد متابعة مرتبط${r.appointmentId?' — '+r.appointmentId:''}.`:'المتابعة سجل سريري مستقل وليست موعدًا. إنشاء الموعد يتم بإجراء منفصل.');

    const action=document.getElementById('rf-details-action-box'); const btn=document.getElementById('rf-details-action-btn'); action.classList.add('hidden'); btn.onclick=null;
    if(r.recordType==='referral'&&r.status==='route_requested'){
      action.classList.remove('hidden'); document.getElementById('rf-details-action-title').textContent='إغلاق الإحالة بعد اكتمال الإجراء'; document.getElementById('rf-details-action-copy').textContent='سجّل الإحالة كمكتملة فقط بعد التأكد من اكتمال الإجراء المطلوب.'; btn.textContent='تسجيل الإحالة كمكتملة'; btn.onclick=()=>completeRecord(r.id);
    } else if(r.recordType==='followup'&&r.status==='required'){
      action.classList.remove('hidden'); document.getElementById('rf-details-action-title').textContent='إنشاء موعد متابعة'; document.getElementById('rf-details-action-copy').textContent='هذا إجراء منفصل يحول خطة المتابعة إلى موعد فعلي مرتبط بها.'; btn.textContent='إنشاء موعد متابعة'; btn.onclick=()=>createFollowupAppointment(r.id);
    } else if(r.recordType==='followup'&&r.status==='appointment_created'){
      action.classList.remove('hidden'); document.getElementById('rf-details-action-title').textContent='تسجيل اكتمال المتابعة'; document.getElementById('rf-details-action-copy').textContent='استخدم هذا الإجراء بعد تنفيذ المتابعة السريرية فعليًا.'; btn.textContent='تسجيل المتابعة كمكتملة'; btn.onclick=()=>completeRecord(r.id);
    }
    document.getElementById('drawer-rf-details').classList.remove('hidden'); syncBodyScrollLock();
  }

  function closeDetails(){ document.getElementById('drawer-rf-details').classList.add('hidden'); activeRecordId=null; syncBodyScrollLock(); }
  function completeRecord(id){ const r=records.find(x=>x.id===id); if(!r)return; r.status='completed'; r.updatedAt=new Date().toISOString(); save(); render(); openDetails(id); showToast(r.recordType==='referral'?'تم تسجيل الإحالة كمكتملة':'تم تسجيل المتابعة كمكتملة','success'); }
  function createFollowupAppointment(id){ const r=records.find(x=>x.id===id); if(!r||r.recordType!=='followup')return; r.status='appointment_created'; r.appointmentId=`APT-${r.patientId.replace(/\D/g,'')}-${String(Date.now()).slice(-4)}`; r.updatedAt=new Date().toISOString(); save(); render(); openDetails(id); showToast('تم إنشاء موعد متابعة مرتبط بالسجل','success'); }

  function closeMenu(){ const m=document.getElementById('rf-row-menu'); m.classList.add('hidden'); m.replaceChildren(); }
  function openMenu(event,id){
    const r=records.find(x=>x.id===id); if(!r)return; const m=document.getElementById('rf-row-menu'); m.replaceChildren();
    const item=(icon,label,fn,blue=false)=>{const b=document.createElement('button'); b.type='button'; b.className=`w-full text-right px-3 py-2 hover:bg-slate-50 rounded-lg flex items-center gap-2.5 ${blue?'text-brand font-bold':'text-slate-700'}`; const i=document.createElement('span'); i.className='material-symbols-outlined text-lg'; i.textContent=icon; const t=document.createElement('span');t.textContent=label;b.append(i,t);b.addEventListener('click',e=>{e.stopPropagation();closeMenu();fn();});return b;};
    m.append(item('visibility','عرض التفاصيل',()=>openDetails(id)));
    if(r.status==='draft') m.append(item('edit','تعديل المسودة',()=>openCreate(r.recordType,id)));
    m.append(item('stethoscope','فتح الزيارة المرتبطة',()=>showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل','info')));
    if(r.recordType==='referral'&&r.status==='documented') m.append(item('near_me','تحديد / تعديل جهة الإحالة',()=>openCreate('referral',id),true));
    if(r.recordType==='followup'&&r.status==='required') m.append(item('calendar_add_on','إنشاء موعد متابعة',()=>createFollowupAppointment(id),true)); if((r.recordType==='followup'&&r.status==='appointment_created')||r.status==='route_requested') m.append(item('task_alt','تسجيل كمكتملة',()=>completeRecord(id),true));
    const rect=event.currentTarget.getBoundingClientRect(); m.classList.remove('hidden'); const w=220,h=m.offsetHeight||160,g=6,pad=8; let top=rect.bottom+g; if(top+h>innerHeight-pad)top=rect.top-h-g; top=Math.max(pad,Math.min(top,innerHeight-h-pad)); let left=rect.right-w; left=Math.max(pad,Math.min(left,innerWidth-w-pad)); m.style.top=`${Math.round(top)}px`; m.style.left=`${Math.round(left)}px`;
  }

  function initSidebar(){
    const sidebar=document.getElementById('app-sidebar'),main=document.getElementById('main-content-layout'),top=document.getElementById('top-header'),pin=document.getElementById('sidebar-pin-toggle'); if(!sidebar||!main||!top)return; const saved=localStorage.getItem(SIDEBAR_PIN_KEY); isPinned=saved===null?true:saved==='true';
    const update=()=>{sidebar.classList.remove('sidebar-hovered'); if(isPinned){sidebar.classList.remove('sidebar-collapsed');main.style.marginRight='268px';top.style.right='268px';}else{sidebar.classList.add('sidebar-collapsed');main.style.marginRight='72px';top.style.right='72px';}}; update();
    pin?.addEventListener('click',e=>{e.stopPropagation();isPinned=!isPinned;localStorage.setItem(SIDEBAR_PIN_KEY,isPinned?'true':'false');update();}); sidebar.addEventListener('mouseenter',()=>{if(!isPinned)sidebar.classList.add('sidebar-hovered');}); sidebar.addEventListener('mouseleave',()=>{if(!isPinned)sidebar.classList.remove('sidebar-hovered');});
  }
  function initDoctorMenu(){ const tr=document.getElementById('doctor-card-trigger'),m=document.getElementById('doctor-account-menu'); if(!tr||!m)return; tr.addEventListener('click',e=>{e.stopPropagation();m.classList.toggle('hidden');}); document.addEventListener('click',e=>{if(!m.contains(e.target)&&!tr.contains(e.target))m.classList.add('hidden');}); }
  function initQuickAdd(){ const c=document.getElementById('quick-add-container'),b=document.getElementById('quick-add-btn'),d=document.getElementById('quick-add-dropdown'),a=document.getElementById('quick-add-arrow'); if(!c||!b||!d)return; let timer=null; const set=open=>{if(open){clearTimeout(timer);d.classList.remove('hidden');if(a)a.textContent='expand_less';}else{d.classList.add('hidden');if(a)a.textContent='expand_more';}}; b.addEventListener('click',e=>{e.stopPropagation();set(d.classList.contains('hidden'));});c.addEventListener('mouseenter',()=>set(true));c.addEventListener('mouseleave',()=>timer=setTimeout(()=>set(false),200));document.addEventListener('click',e=>{if(!c.contains(e.target))set(false);});document.getElementById('quick-add-ref-follow-btn')?.addEventListener('click',()=>{set(false);openCreate('referral');}); }

  function initEvents(){
    document.getElementById('open-referral-btn').addEventListener('click',()=>openCreate('referral')); document.getElementById('open-followup-btn').addEventListener('click',()=>openCreate('followup')); document.getElementById('rf-create-close').addEventListener('click',closeCreate); document.getElementById('rf-create-cancel').addEventListener('click',closeCreate); document.getElementById('rf-create-backdrop').addEventListener('click',closeCreate); document.getElementById('rf-details-close').addEventListener('click',closeDetails); document.getElementById('rf-details-close-footer').addEventListener('click',closeDetails); document.getElementById('rf-details-backdrop').addEventListener('click',closeDetails);
    document.querySelectorAll('#rf-type-switch button').forEach(b=>b.addEventListener('click',()=>setRecordType(b.dataset.recordType)));
    const pInput=document.getElementById('rf-patient-search-input'), pBox=document.getElementById('rf-patient-suggestions'); pInput.addEventListener('input',()=>{ const q=pInput.value.trim().toLowerCase(); pBox.replaceChildren(); if(!q){pBox.classList.add('hidden');return;} const list=DEMO_PATIENTS.filter(p=>p.name.toLowerCase().includes(q)||p.id.toLowerCase().includes(q)); list.forEach(p=>{const b=document.createElement('button');b.type='button';b.className='w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center justify-between';const n=document.createElement('span');n.className='font-bold text-slate-800';n.textContent=p.name;const id=document.createElement('span');id.className='font-num text-slate-400';id.textContent=p.id;b.append(n,id);b.addEventListener('click',()=>choosePatient(p));pBox.append(b);}); if(!list.length){const x=document.createElement('div');x.className='px-3 py-2 text-slate-400';x.textContent='لا توجد نتائج مطابقة';pBox.append(x);} pBox.classList.remove('hidden'); });
    document.getElementById('rf-change-patient').addEventListener('click',()=>{selectedPatient=null;document.getElementById('rf-selected-patient-card').classList.add('hidden');document.getElementById('rf-patient-search-box').classList.remove('hidden');pInput.value='';pInput.focus();});
    document.querySelectorAll('input[name="referral_destination_mode"]').forEach(r=>r.addEventListener('change',()=>{const v=document.querySelector('input[name="referral_destination_mode"]:checked')?.value||'unassigned';document.getElementById('rf-network-options').classList.toggle('hidden',v!=='thera_network');document.getElementById('rf-external-options').classList.toggle('hidden',v!=='external');if(v!=='thera_network')selectedNetworkTarget=null;})); document.getElementById('rf-network-search').addEventListener('input',e=>{selectedNetworkTarget=null;renderNetworkTargets(e.target.value);});
    document.getElementById('rf-save-draft').addEventListener('click',()=>saveRecord('draft')); document.getElementById('rf-save-documented').addEventListener('click',()=>saveRecord('documented')); document.getElementById('rf-primary-action').addEventListener('click',()=>saveRecord('primary'));
    document.querySelectorAll('.rf-tab-btn').forEach(btn=>btn.addEventListener('click',()=>{document.querySelectorAll('.rf-tab-btn').forEach(b=>{b.className='rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#EEF4FF] text-[#3F4752] hover:bg-[#E5EEFF]';const c=b.querySelector('.tab-count');if(c)c.className='tab-count px-2 py-0.5 rounded-full text-xs bg-[#DDE8FB] text-[#3F4752] font-num';});btn.className='rf-tab-btn status-tab-btn px-4 py-2 rounded-full text-[13px] font-medium transition-all flex items-center gap-2 bg-[#0495F6] text-white shadow-sm';const c=btn.querySelector('.tab-count');if(c)c.className='tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-num';currentTab=btn.dataset.tab;render();}));
    ['rf-filter-search','rf-filter-type','rf-filter-status','rf-filter-priority','rf-filter-sort'].forEach(id=>document.getElementById(id).addEventListener(id==='rf-filter-search'?'input':'change',render)); document.getElementById('reset-rf-filters-btn').addEventListener('click',()=>{document.getElementById('rf-filter-search').value='';document.getElementById('rf-filter-type').value='';document.getElementById('rf-filter-status').value='';document.getElementById('rf-filter-priority').value='';document.getElementById('rf-filter-sort').value='newest';window.refreshTheraSelects?.();render();showToast('تمت إعادة ضبط الفلاتر','info');});
    document.getElementById('rf-open-visit-btn').addEventListener('click',()=>showToast('فتح الزيارة المرتبطة جاهز للربط مع التنقل','info')); document.getElementById('rf-print-btn').addEventListener('click',()=>window.print());
    document.addEventListener('click',e=>{const m=document.getElementById('rf-row-menu');if(!m.contains(e.target))closeMenu();});window.addEventListener('resize',closeMenu);window.addEventListener('scroll',closeMenu,true);document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeMenu();closeCreate();closeDetails();}});
  }

  initStorage(); initSidebar(); initDoctorMenu(); initQuickAdd(); initEvents(); renderNetworkTargets(); render();
})();
</script>

<script id="thera-custom-selects">
(function(){
  const registry=new Map();
  function closeAll(except){document.querySelectorAll('.thera-custom-select.open').forEach(w=>{if(w!==except)w.classList.remove('open');});document.querySelectorAll('.thera-custom-select-menu').forEach(m=>{if(!except||m.parentElement!==except)m.classList.add('hidden');});}
  function build(select){
    if(!select||registry.has(select))return; const parent=select.parentElement; const wrapper=document.createElement('div');wrapper.className='thera-custom-select';parent.insertBefore(wrapper,select);wrapper.appendChild(select);select.classList.add('thera-native-select-hidden');
    const trigger=document.createElement('button');trigger.type='button';trigger.className='thera-custom-select-trigger';trigger.setAttribute('aria-haspopup','listbox');trigger.innerHTML='<span class="thera-custom-select-label"></span><span class="material-symbols-outlined thera-custom-select-arrow">expand_more</span>';
    const menu=document.createElement('div');menu.className='thera-custom-select-menu hidden';menu.setAttribute('role','listbox');wrapper.append(trigger,menu);
    function refresh(){const selected=select.options[select.selectedIndex]||select.options[0];wrapper.querySelector('.thera-custom-select-label').textContent=selected?selected.textContent.trim():'';menu.replaceChildren();Array.from(select.options).forEach(opt=>{const b=document.createElement('button');b.type='button';b.className='thera-custom-select-option';if(opt.value===select.value)b.classList.add('active');const tx=document.createElement('span');tx.textContent=opt.textContent.trim();const c=document.createElement('span');c.className='material-symbols-outlined check';c.textContent='check';b.append(tx,c);b.addEventListener('click',e=>{e.stopPropagation();if(select.value!==opt.value){select.value=opt.value;select.dispatchEvent(new Event('input',{bubbles:true}));select.dispatchEvent(new Event('change',{bubbles:true}));}refresh();wrapper.classList.remove('open');menu.classList.add('hidden');});menu.appendChild(b);});}
    trigger.addEventListener('click',e=>{e.stopPropagation();const willOpen=menu.classList.contains('hidden');closeAll(willOpen?wrapper:null);wrapper.classList.toggle('open',willOpen);menu.classList.toggle('hidden',!willOpen);});select.addEventListener('change',refresh);registry.set(select,{refresh});refresh();
  }
  window.refreshTheraSelects=()=>registry.forEach(x=>x.refresh()); document.querySelectorAll('select').forEach(build);document.addEventListener('click',()=>closeAll());document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAll();});document.querySelectorAll('form').forEach(f=>f.addEventListener('reset',()=>setTimeout(window.refreshTheraSelects,0)));
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>
