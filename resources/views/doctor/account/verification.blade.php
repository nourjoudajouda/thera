<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>التحقق من الطبيب - THERA</title>
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
<style data-purpose="doctor-verification-page">
  .verify-card{background:#fff;border:1px solid #E2E8F0;border-radius:18px;box-shadow:0 2px 10px rgba(15,23,42,.035)}
  .verify-tab{white-space:nowrap;border:1px solid #E2E8F0;background:#fff;color:#475569;transition:.18s ease}
  .verify-tab:hover{background:#F8FAFC;color:#0F172A}
  .verify-tab[aria-selected="true"]{background:#0495F6;border-color:#0495F6;color:#fff;box-shadow:0 3px 10px rgba(4,149,246,.18)}
  .verify-panel{display:none}.verify-panel.active{display:block}
  .status-pill{display:inline-flex;align-items:center;gap:5px;height:28px;padding:0 10px;border-radius:999px;font-size:12px;font-weight:700}
  .step-line{position:absolute;top:19px;right:9%;left:9%;height:2px;background:#E2E8F0;z-index:0}
  .step-progress{position:absolute;top:19px;right:9%;width:82%;height:2px;background:#10B981;z-index:1}
  .step-item{position:relative;z-index:2;min-width:0;text-align:center}
  .step-dot{width:38px;height:38px;border-radius:999px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border:2px solid #E2E8F0;background:#fff;color:#94A3B8}
  .step-item.done .step-dot{background:#ECFDF5;border-color:#A7F3D0;color:#059669}
  .step-item.current .step-dot{background:#EFF6FF;border-color:#93C5FD;color:#0466F1;box-shadow:0 0 0 4px rgba(4,149,246,.08)}
  .document-row+.document-row{border-top:1px solid #F1F5F9}
  .soft-note{border:1px solid #DBEAFE;background:#F8FCFF;border-radius:14px}
  .danger-note{border:1px solid #FECACA;background:#FFF7F7;border-radius:14px}
  .input-base{width:100%;height:44px;border:1px solid #CBD5E1;border-radius:12px;background:#fff;padding:0 13px;font-size:14px;transition:.16s ease}
  .input-base:focus,.textarea-base:focus{outline:none;border-color:#0495F6;box-shadow:0 0 0 3px rgba(4,149,246,.10)}
  .textarea-base{width:100%;min-height:110px;border:1px solid #CBD5E1;border-radius:12px;background:#fff;padding:12px 13px;font-size:14px;resize:vertical;transition:.16s ease}
  .field-label{display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:7px}
  @media(max-width:1023px){.verify-tabs-wrap{overflow-x:auto;scrollbar-width:none}.verify-tabs-wrap::-webkit-scrollbar{display:none}.step-line,.step-progress{display:none}}
</style>
<style id="thera-verification-onboarding-v3">
.verification-onboarding-banner{
  border:1px solid #CFE8FB;
  background:linear-gradient(90deg,#F5FBFF 0%,#F8FAFF 100%);
  border-radius:16px;
  padding:16px 18px;
}
.onboarding-step-pill{
  height:26px;padding:0 9px;border-radius:999px;
  display:inline-flex;align-items:center;
  background:#EEF2F7;color:#64748B;font-size:11px;font-weight:800;
}
.onboarding-step-pill.active{background:#E0F2FE;color:#0369A1}
.onboarding-step-pill.done{background:#ECFDF5;color:#047857}
.onboarding-empty-state{
  border:1px dashed #CBD5E1;background:#F8FAFC;border-radius:16px;
  padding:28px 20px;text-align:center;color:#64748B;
}
.onboarding-empty-state .material-symbols-outlined{
  width:46px;height:46px;border-radius:14px;background:#EEF4FF;color:#0495F6;
  display:flex;align-items:center;justify-content:center;margin:0 auto 10px;
}
.onboarding-check{
  display:flex;align-items:flex-start;gap:10px;padding:12px 0;border-bottom:1px solid #F1F5F9;
}
.onboarding-check:last-child{border-bottom:0}
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
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
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
<div class="mb-5">
<div class="flex items-center gap-2 text-sm text-slate-400 mb-1.5">
<span>الإدارة</span><span class="material-symbols-outlined text-[16px]">chevron_left</span><span>التحقق من الطبيب</span>
</div>
<h1 class="text-[28px] leading-tight font-extrabold text-[#0B1020]">التحقق من الطبيب</h1>
<p class="mt-1 text-sm text-slate-500">إدارة حالة التحقق المهني، المستندات، الطلبات وسجل المراجعات.</p>
</div>
<section class="verify-card p-5 mb-4" id="verification-summary-card">
<div class="flex flex-col xl:flex-row xl:items-center gap-5">
<div class="flex items-center gap-4 min-w-0 flex-1">
<div class="w-[68px] h-[68px] rounded-2xl bg-gradient-to-br from-[#E6F4FE] to-[#EEF2FF] border border-[#D8E8F6] flex items-center justify-center text-[#0466F1] text-xl font-extrabold shrink-0">م أ</div>
<div class="min-w-0">
<div class="flex flex-wrap items-center gap-2 mb-1">
<h2 class="text-xl font-extrabold text-[#0B1020]" id="verification-doctor-name">د. محمد أحمد</h2>
<span class="status-pill bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="material-symbols-outlined text-[16px]">verified</span>موثّق</span>
<span class="status-pill bg-blue-50 text-blue-700 border border-blue-200">الحساب نشط</span>
</div>
<p class="text-sm font-semibold text-slate-600">استشاري طب باطني</p>
<p class="text-xs text-slate-400 mt-2">رقم آخر طلب تحقق: <span class="font-num">VR-2026-00418</span> • آخر قرار: 18 سبتمبر 2026</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-2">
<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold flex items-center gap-2" id="view-current-request" type="button"><span class="material-symbols-outlined text-[18px]">description</span>عرض الطلب الحالي</button>
<button class="h-10 px-4 rounded-xl border border-[#0495F6] text-[#0495F6] hover:bg-blue-50 text-sm font-bold flex items-center gap-2" id="open-profile-link" type="button"><span class="material-symbols-outlined text-[18px]">badge</span>إدارة الملف المهني</button>
</div>
</div>
</section>
<section class="verify-card p-5 mb-4" id="verification-steps-card">
<div class="relative grid grid-cols-2 md:grid-cols-5 gap-4">
<div class="step-line"></div><div class="step-progress"></div>
<div class="step-item done"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">check</span></div><p class="text-xs font-bold text-slate-700">البيانات المهنية</p></div>
<div class="step-item done"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">check</span></div><p class="text-xs font-bold text-slate-700">المستندات</p></div>
<div class="step-item done"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">check</span></div><p class="text-xs font-bold text-slate-700">تم الإرسال</p></div>
<div class="step-item done"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">check</span></div><p class="text-xs font-bold text-slate-700">المراجعة</p></div>
<div class="step-item current"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">verified</span></div><p class="text-xs font-bold text-[#0466F1]">تم التحقق</p></div>
</div>
</section>
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-4" id="verification-kpis">
<div class="verify-card p-4"><div class="flex items-center justify-between"><div><p class="text-xs text-slate-400">المستندات المطلوبة</p><p class="text-xl font-extrabold text-slate-800 mt-1"><span class="font-num">5/5</span></p></div><div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0466F1] flex items-center justify-center"><span class="material-symbols-outlined">folder_open</span></div></div><p class="text-xs text-emerald-600 font-bold mt-3">مكتملة</p></div>
<div class="verify-card p-4"><div class="flex items-center justify-between"><div><p class="text-xs text-slate-400">المؤهلات الموثقة</p><p class="text-xl font-extrabold text-slate-800 mt-1 font-num">3</p></div><div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined">workspace_premium</span></div></div><p class="text-xs text-slate-400 mt-3">لا توجد عناصر معلقة</p></div>
<div class="verify-card p-4"><div class="flex items-center justify-between"><div><p class="text-xs text-slate-400">الإنجازات الموثقة</p><p class="text-xl font-extrabold text-slate-800 mt-1 font-num">1</p></div><div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center"><span class="material-symbols-outlined">military_tech</span></div></div><p class="text-xs text-amber-600 font-bold mt-3">1 قيد التحقق</p></div>
<div class="verify-card p-4"><div class="flex items-center justify-between"><div><p class="text-xs text-slate-400">الحالة التشغيلية</p><p class="text-xl font-extrabold text-emerald-700 mt-1">نشط</p></div><div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><span class="material-symbols-outlined">check_circle</span></div></div><p class="text-xs text-slate-400 mt-3">منفصلة عن حالة التحقق</p></div>
</div>
<div class="verify-tabs-wrap mb-4"><div aria-label="أقسام التحقق" class="flex gap-2 min-w-max" role="tablist">
<button aria-selected="true" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="overview">نظرة عامة</button>
<button aria-selected="false" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="documents">المستندات</button>
<button aria-selected="false" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="credentials">المؤهلات</button>
<button aria-selected="false" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="achievements">الإنجازات</button>
<button aria-selected="false" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="submission">حالة الطلب</button>
<button aria-selected="false" class="verify-tab h-10 px-4 rounded-xl text-sm font-bold" data-tab="history">سجل المراجعات</button>
</div></div>
<section class="verify-panel active" data-panel="overview">
<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4">
<div class="verify-card p-5 md:p-6">
<div class="flex items-start justify-between gap-4 mb-5"><div><h3 class="text-lg font-extrabold text-[#0B1020]">حالة التحقق الحالية</h3><p class="text-sm text-slate-500 mt-1">ملخص آخر طلب تحقق معتمد.</p></div><span class="status-pill bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="material-symbols-outlined text-[16px]">verified</span>تم التحقق</span></div>
<div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 flex items-start gap-4"><div class="w-11 h-11 rounded-xl bg-white border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined">verified_user</span></div><div><p class="font-extrabold text-emerald-900">تم التحقق من الملف المهني</p><p class="text-sm text-emerald-800/80 mt-1 leading-7">تمت مراجعة البيانات المهنية والمستندات المطلوبة واتخاذ قرار التحقق بتاريخ 18 سبتمبر 2026.</p><button class="mt-3 text-sm font-bold text-emerald-700 hover:text-emerald-900" id="view-decision" type="button">عرض تفاصيل القرار</button></div></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
<div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">رقم الطلب</p><p class="font-num font-bold text-slate-800 mt-1">VR-2026-00418</p></div>
<div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">تاريخ الإرسال</p><p class="font-bold text-slate-800 mt-1">14 سبتمبر 2026</p></div>
<div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">تاريخ القرار</p><p class="font-bold text-slate-800 mt-1">18 سبتمبر 2026</p></div>
<div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">حالة الحساب</p><p class="font-bold text-emerald-700 mt-1">نشط</p></div>
</div><section class="hidden verification-onboarding-banner mb-4" id="verification-onboarding-banner">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<div class="flex items-start gap-3">
<div class="w-11 h-11 rounded-xl bg-white border border-blue-100 text-[#0495F6] flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">verified_user</span>
</div>
<div>
<div class="flex flex-wrap items-center gap-2">
<span class="onboarding-step-pill done">1 الملف المهني</span>
<span class="material-symbols-outlined text-[15px] text-slate-300">chevron_left</span>
<span class="onboarding-step-pill active">2 التحقق المهني</span>
</div>
<p class="font-extrabold text-[#0B1020] mt-2">جهّز طلب التحقق المهني</p>
<p class="text-xs text-slate-500 mt-1 leading-6">ارفع المستندات والمعلومات الداعمة، ثم أرسل الطلب عندما تستوفي قائمة التحقق المعتمدة.</p>
</div>
</div>
<button class="h-10 px-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold flex items-center gap-2" id="back-to-onboarding-profile" type="button">
<span class="material-symbols-outlined text-[18px]">badge</span>
<span>العودة إلى الملف المهني</span>
</button>
</div>
</section>
</div>
<aside class="space-y-4">
<div class="verify-card p-5"><h4 class="font-extrabold text-slate-800">تنبيه مهم</h4><p class="text-sm text-slate-500 leading-7 mt-2">تغيير بيانات مهنية موثّقة أو استبدال مستند معتمد قد يفتح مراجعة جديدة للعنصر المتأثر.</p><button class="mt-3 text-sm font-bold text-[#0495F6]" id="what-triggers-review">ما الذي يعيد المراجعة؟</button></div>
<div class="verify-card p-5"><div class="flex items-center justify-between"><div><p class="text-xs text-slate-400">الملف المهني</p><p class="font-extrabold text-slate-800 mt-1">مكتمل بنسبة 85%</p></div><span class="material-symbols-outlined text-[#0495F6]">badge</span></div><button class="w-full h-10 mt-4 rounded-xl border border-[#0495F6] text-[#0495F6] hover:bg-blue-50 font-bold text-sm" id="open-profile-link-2">إدارة الملف المهني</button></div>
</aside>
</div>
</section>
<section class="verify-panel" data-panel="documents">
<div class="verify-card overflow-hidden">
<div class="p-5 md:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3"><div><h3 class="text-lg font-extrabold text-[#0B1020]">المستندات</h3><p class="text-sm text-slate-500 mt-1">المستندات المطلوبة والاختيارية مع حالة كل مستند.</p></div><button class="h-10 px-4 rounded-xl bg-[#0495F6] text-white text-sm font-bold flex items-center gap-2" id="upload-extra-doc"><span class="material-symbols-outlined text-[18px]">upload_file</span>رفع مستند إضافي</button></div>
<div id="doc-list">
<div class="document-row p-5 flex flex-col lg:flex-row lg:items-center gap-4 justify-between"><div class="flex items-start gap-3 min-w-0"><div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0466F1] flex items-center justify-center shrink-0"><span class="material-symbols-outlined">description</span></div><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800">ترخيص مزاولة المهنة</p><span class="text-[11px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">مطلوب</span><span class="status-pill h-6 bg-emerald-50 text-emerald-700 border border-emerald-200">موثّق</span></div><p class="text-xs text-slate-500 mt-1.5">الجهة المصدرة: الجهة المهنية المختصة • الإصدار: 2024 • الانتهاء: 2027</p></div></div><div class="flex items-center gap-2"><button class="doc-view text-xs font-bold text-[#0495F6] px-3 py-2 rounded-lg hover:bg-blue-50" data-doc="license">عرض المستند</button><button class="doc-replace text-xs font-bold text-slate-600 px-3 py-2 rounded-lg hover:bg-slate-50" data-doc="license">استبدال</button></div></div>
<div class="document-row p-5 flex flex-col lg:flex-row lg:items-center gap-4 justify-between"><div class="flex items-start gap-3 min-w-0"><div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0466F1] flex items-center justify-center shrink-0"><span class="material-symbols-outlined">description</span></div><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800">إثبات التخصص الرئيسي</p><span class="text-[11px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">مطلوب</span><span class="status-pill h-6 bg-emerald-50 text-emerald-700 border border-emerald-200">موثّق</span></div><p class="text-xs text-slate-500 mt-1.5">المجلس الطبي الأردني • 2018 • بدون تاريخ انتهاء</p></div></div><div class="flex items-center gap-2"><button class="doc-view text-xs font-bold text-[#0495F6] px-3 py-2 rounded-lg hover:bg-blue-50" data-doc="specialty">عرض المستند</button></div></div>
<div class="document-row p-5 flex flex-col lg:flex-row lg:items-center gap-4 justify-between"><div class="flex items-start gap-3 min-w-0"><div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined">history</span></div><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800">شهادة زمالة مهنية</p><span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">اختياري</span><span class="status-pill h-6 bg-amber-50 text-amber-700 border border-amber-200">قيد المراجعة</span></div><p class="text-xs text-slate-500 mt-1.5">Royal College • 2021 • رُفعت نسخة محدثة في 3 أكتوبر 2026</p></div></div><div class="flex items-center gap-2"><button class="doc-view text-xs font-bold text-[#0495F6] px-3 py-2 rounded-lg hover:bg-blue-50" data-doc="fellowship">عرض المستند</button></div></div>
<div class="document-row p-5 flex flex-col lg:flex-row lg:items-center gap-4 justify-between"><div class="flex items-start gap-3 min-w-0"><div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined">error</span></div><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800">شهادة دورة دعم الحياة المتقدم</p><span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">اختياري</span><span class="status-pill h-6 bg-red-50 text-red-700 border border-red-200">منتهي</span></div><p class="text-xs text-slate-500 mt-1.5">انتهت في 30 سبتمبر 2026 • لا تؤثر على حالة التحقق الأساسية</p></div></div><div class="flex items-center gap-2"><button class="doc-view text-xs font-bold text-[#0495F6] px-3 py-2 rounded-lg hover:bg-blue-50" data-doc="als">عرض المستند</button><button class="doc-replace text-xs font-bold text-red-600 px-3 py-2 rounded-lg hover:bg-red-50" data-doc="als">رفع نسخة محدثة</button></div></div>
</div>
</div>
</section>
<section class="verify-panel" data-panel="credentials">
<div class="verify-card p-5 md:p-6"><div class="flex items-start justify-between gap-4 mb-5"><div><h3 class="text-lg font-extrabold text-[#0B1020]">المؤهلات المهنية</h3><p class="text-sm text-slate-500 mt-1">نفس عناصر الملف المهني، لكن هنا يظهر مسار التحقق لكل مؤهل.</p></div><button class="text-sm font-bold text-[#0495F6]" id="credential-review-info">كيف يتم التحقق؟</button></div>
<div class="space-y-3"><div class="rounded-xl border border-slate-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-3"><div><div class="flex flex-wrap gap-2 items-center"><p class="font-extrabold">البورد الأردني في الطب الباطني</p><span class="status-pill h-6 bg-emerald-50 text-emerald-700 border border-emerald-200">موثّق</span></div><p class="text-xs text-slate-500 mt-1">المجلس الطبي الأردني • 2018</p></div><button class="cred-detail text-xs font-bold text-[#0495F6]" data-cred="board">عرض سجل التحقق</button></div>
<div class="rounded-xl border border-slate-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-3"><div><div class="flex flex-wrap gap-2 items-center"><p class="font-extrabold">زمالة الرعاية الباطنية المتقدمة</p><span class="status-pill h-6 bg-amber-50 text-amber-700 border border-amber-200">قيد المراجعة</span></div><p class="text-xs text-slate-500 mt-1">Royal College • 2021</p></div><button class="cred-detail text-xs font-bold text-[#0495F6]" data-cred="fellowship">عرض سجل التحقق</button></div></div></div>
</section>
<section class="verify-panel" data-panel="achievements">
<div class="verify-card p-5 md:p-6"><div class="mb-5"><h3 class="text-lg font-extrabold text-[#0B1020]">الإنجازات المهنية</h3><p class="text-sm text-slate-500 mt-1">الإنجازات المرسلة للتحقق وحالة كل منها.</p></div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4"><div class="rounded-2xl border border-slate-200 p-5"><div class="flex items-start justify-between gap-3"><div><span class="text-[11px] font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">اعتماد</span><h4 class="font-extrabold mt-3">اعتماد التميز في إدارة الأمراض المزمنة</h4><p class="text-xs text-slate-500 mt-1">الجمعية الطبية الإقليمية • 2025</p></div><span class="status-pill bg-emerald-50 text-emerald-700 border border-emerald-200">موثّق</span></div><button class="achievement-review mt-4 text-xs font-bold text-[#0495F6]" data-ach="chronic">عرض سجل التحقق</button></div>
<div class="rounded-2xl border border-slate-200 p-5"><div class="flex items-start justify-between gap-3"><div><span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg">جائزة</span><h4 class="font-extrabold mt-3">جائزة التميز في التثقيف الصحي</h4><p class="text-xs text-slate-500 mt-1">ملتقى الرعاية الصحية • 2026</p></div><span class="status-pill bg-amber-50 text-amber-700 border border-amber-200">قيد التحقق</span></div><button class="achievement-review mt-4 text-xs font-bold text-[#0495F6]" data-ach="education">عرض سجل التحقق</button></div></div></div>
</section>
<section class="verify-panel" data-panel="submission">
<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4"><div class="verify-card p-5 md:p-6"><div class="flex items-center justify-between gap-3 mb-5"><div><h3 class="text-lg font-extrabold">حالة الطلب الحالي</h3><p class="text-sm text-slate-500 mt-1">آخر طلب تحقق معتمد.</p></div><span class="status-pill bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span></div>
<div class="space-y-0"><div class="flex gap-3 pb-5"><div class="w-9 h-9 rounded-full bg-blue-50 text-[#0495F6] flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]">send</span></div><div><p class="font-bold">تم إرسال الطلب</p><p class="text-xs text-slate-500 mt-1">14 سبتمبر 2026 • 10:42 ص</p></div></div><div class="flex gap-3 pb-5"><div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]">manage_search</span></div><div><p class="font-bold">بدأت المراجعة</p><p class="text-xs text-slate-500 mt-1">16 سبتمبر 2026 • 09:15 ص</p></div></div><div class="flex gap-3"><div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]">verified</span></div><div><p class="font-bold">تم التحقق من الطلب</p><p class="text-xs text-slate-500 mt-1">18 سبتمبر 2026 • 02:20 م</p></div></div></div></div>
<aside class="verify-card p-5"><h4 class="font-extrabold">إرسال طلب جديد</h4><p class="text-sm text-slate-500 leading-7 mt-2">لا تحتاج لطلب جديد الآن. عند تعديل معلومة مهنية موثقة أو استبدال مستند مطلوب، سيظهر مسار إرسال مراجعة جديدة للعناصر المتأثرة.</p><button class="w-full h-10 mt-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50" id="demo-new-review">معاينة حالة تحتاج إعادة مراجعة</button></aside></div>
</section>
<section class="verify-panel" data-panel="history">
<div class="verify-card overflow-hidden"><div class="p-5 md:p-6 border-b border-slate-100"><h3 class="text-lg font-extrabold">سجل المراجعات</h3><p class="text-sm text-slate-500 mt-1">تاريخ الطلبات والقرارات السابقة محفوظ ولا يتم استبداله.</p></div>
<div class="divide-y divide-slate-100"><div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4"><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold">VR-2026-00418</p><span class="status-pill h-6 bg-emerald-50 text-emerald-700 border border-emerald-200">تم التحقق</span></div><p class="text-xs text-slate-500 mt-1.5">أرسل 14 سبتمبر 2026 • القرار 18 سبتمبر 2026</p></div><button class="history-detail text-xs font-bold text-[#0495F6]" data-history="current">عرض التفاصيل</button></div>
<div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4"><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold">VR-2026-00372</p><span class="status-pill h-6 bg-red-50 text-red-700 border border-red-200">مرفوض</span><span class="text-[11px] font-bold text-slate-500">تم التصحيح لاحقًا</span></div><p class="text-xs text-slate-500 mt-1.5">أرسل 5 سبتمبر 2026 • القرار 8 سبتمبر 2026</p></div><button class="history-detail text-xs font-bold text-red-600" data-history="rejected">عرض سبب الرفض</button></div></div></div>
</section>
</div>
</main>
<div aria-hidden="true" class="fixed inset-0 z-[200] hidden" id="demo-modal">
<div class="absolute inset-0 bg-slate-950/45 backdrop-blur-[1px]" data-modal-close=""></div>
<div class="absolute inset-0 flex items-center justify-center p-3 md:p-6 pointer-events-none">
<section aria-labelledby="demo-modal-title" aria-modal="true" class="pointer-events-auto w-full max-w-3xl max-h-[90vh] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" role="dialog">
<header class="h-16 px-5 md:px-6 flex items-center justify-between border-b border-slate-200 bg-white shrink-0"><div class="min-w-0"><h3 class="font-extrabold text-lg text-[#0B1020] truncate" id="demo-modal-title">التفاصيل</h3><p class="text-xs text-slate-400 mt-0.5" id="demo-modal-subtitle">معاينة تفاعلية</p></div><button class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-500 flex items-center justify-center" data-modal-close="" type="button"><span class="material-symbols-outlined text-[20px]">close</span></button></header>
<div class="p-5 md:p-6 overflow-y-auto" id="demo-modal-body"></div>
<footer class="px-5 md:px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-end gap-2 shrink-0" id="demo-modal-footer"><button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-bold" data-modal-close="" type="button">إغلاق</button></footer>
</section>
</div>
</div>
<div class="fixed bottom-6 left-6 z-[220] flex flex-col gap-2 pointer-events-none" id="toast-container"></div>
<script>
(() => {
  const $=id=>document.getElementById(id);
  const sidebar=$('app-sidebar'), topHeader=$('top-header'), main=$('main-content-layout');
  const pin=$('sidebar-pin-toggle'), pinIcon=$('sidebar-pin-icon'); let collapsed=false;
  function setLayout(){ sidebar.classList.toggle('sidebar-collapsed',collapsed); topHeader.style.right=collapsed?'72px':'268px'; main.style.marginRight=collapsed?'72px':'268px'; if(pinIcon) pinIcon.textContent=collapsed?'keep_off':'push_pin'; }
  pin?.addEventListener('click',()=>{collapsed=!collapsed;setLayout()});
  sidebar?.addEventListener('mouseenter',()=>{if(collapsed)sidebar.classList.add('sidebar-hovered')});
  sidebar?.addEventListener('mouseleave',()=>sidebar.classList.remove('sidebar-hovered'));

  const qa=$('quick-add-btn'), qd=$('quick-add-dropdown'); qa?.addEventListener('click',e=>{e.stopPropagation();qd.classList.toggle('hidden')}); document.addEventListener('click',e=>{if(!e.target.closest('#quick-add-container'))qd?.classList.add('hidden')});

  document.querySelectorAll('.verify-tab').forEach(btn=>btn.addEventListener('click',()=>{document.querySelectorAll('.verify-tab').forEach(b=>b.setAttribute('aria-selected','false'));btn.setAttribute('aria-selected','true');document.querySelectorAll('.verify-panel').forEach(p=>p.classList.remove('active'));document.querySelector(`[data-panel="${btn.dataset.tab}"]`)?.classList.add('active')}));

  const modal=$('demo-modal'), mt=$('demo-modal-title'), ms=$('demo-modal-subtitle'), mb=$('demo-modal-body'), mf=$('demo-modal-footer');
  function openModal(title,subtitle,body,footer=''){mt.textContent=title;ms.textContent=subtitle||'معاينة تفاعلية';mb.innerHTML=body;mf.innerHTML=footer||'<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-bold" type="button" data-modal-close>إغلاق</button>';modal.classList.remove('hidden');modal.setAttribute('aria-hidden','false');bindClose();}
  function closeModal(){modal.classList.add('hidden');modal.setAttribute('aria-hidden','true')}
  function bindClose(){modal.querySelectorAll('[data-modal-close]').forEach(x=>x.onclick=closeModal)}
  bindClose(); document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
  function toast(msg){const el=document.createElement('div');el.className='pointer-events-auto bg-slate-900 text-white text-sm font-bold px-4 py-3 rounded-xl shadow-lg';el.textContent=msg;$('toast-container').appendChild(el);setTimeout(()=>el.remove(),2600)}

  const docNames={license:'ترخيص مزاولة المهنة',specialty:'إثبات التخصص الرئيسي',fellowship:'شهادة زمالة مهنية',als:'شهادة دورة دعم الحياة المتقدم'};
  document.querySelectorAll('.doc-view').forEach(b=>b.addEventListener('click',()=>openModal(docNames[b.dataset.doc]||'المستند','معاينة بيانات المستند',`<div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center"><span class="material-symbols-outlined text-5xl text-slate-400">picture_as_pdf</span><p class="font-extrabold text-slate-800 mt-3">${docNames[b.dataset.doc]}</p><p class="text-sm text-slate-500 mt-2">ملف تجريبي للمعاينة فقط</p></div><div class="grid grid-cols-2 gap-3 mt-4"><div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">الجهة المصدرة</p><p class="font-bold mt-1">جهة مهنية معتمدة</p></div><div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">حالة المستند</p><p class="font-bold text-emerald-700 mt-1">موثّق / محفوظ</p></div></div>`)));

  function replacementForm(doc){return `<div class="space-y-4"><div class="soft-note p-4 text-sm text-slate-600">سيتم الاحتفاظ بالنسخة السابقة في سجل التدقيق، والنسخة الجديدة تدخل مراجعة عند الحاجة.</div><div><label class="field-label">المستند</label><div class="h-28 rounded-xl border border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center text-slate-500"><span class="material-symbols-outlined">upload_file</span><span class="text-sm font-bold mt-2">اختر ملفًا للرفع</span></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-3"><div><label class="field-label">تاريخ الإصدار</label><input class="input-base" type="date"></div><div><label class="field-label">تاريخ الانتهاء</label><input class="input-base" type="date"></div></div></div>`}
  document.querySelectorAll('.doc-replace').forEach(b=>b.addEventListener('click',()=>openModal(`تحديث ${docNames[b.dataset.doc]}`,'رفع نسخة جديدة',replacementForm(b.dataset.doc),'<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold" data-modal-close>إلغاء</button><button id="save-replacement" class="h-10 px-4 rounded-xl bg-[#0495F6] text-white font-bold">حفظ النسخة الجديدة</button>')));
  document.addEventListener('click',e=>{if(e.target.id==='save-replacement'){closeModal();toast('تم حفظ النسخة الجديدة كمسودة مراجعة')}});

  $('upload-extra-doc')?.addEventListener('click',()=>openModal('رفع مستند إضافي','مستند اختياري',`<div class="space-y-4"><div><label class="field-label">اسم المستند</label><input class="input-base" placeholder="مثال: شهادة دورة مهنية"></div><div><label class="field-label">الجهة المصدرة</label><input class="input-base" placeholder="اسم الجهة"></div><div><label class="field-label">الملف</label><div class="h-28 rounded-xl border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-sm font-bold text-slate-500">اضغط لاختيار ملف</div></div></div>`,'<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold" data-modal-close>إلغاء</button><button id="save-extra-doc" class="h-10 px-4 rounded-xl bg-[#0495F6] text-white font-bold">حفظ المستند</button>'));
  document.addEventListener('click',e=>{if(e.target.id==='save-extra-doc'){closeModal();toast('تمت إضافة المستند التجريبي')}});

  $('view-current-request')?.addEventListener('click',()=>document.querySelector('[data-tab="submission"]')?.click());
  $('open-profile-link')?.addEventListener('click',()=>toast('يفتح صفحة إدارة الملف المهني في التطبيق الفعلي'));
  $('open-profile-link-2')?.addEventListener('click',()=>toast('يفتح صفحة إدارة الملف المهني في التطبيق الفعلي'));
  $('view-decision')?.addEventListener('click',()=>openModal('تفاصيل قرار التحقق','الطلب VR-2026-00418',`<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"><p class="font-extrabold text-emerald-900">تم التحقق من الطلب</p><p class="text-sm text-emerald-800 mt-2">اكتملت مراجعة البيانات والمستندات المطلوبة بدون عناصر مرفوضة في النسخة النهائية.</p></div><div class="grid grid-cols-2 gap-3 mt-4"><div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">وقت القرار</p><p class="font-bold mt-1">18 سبتمبر 2026 • 02:20 م</p></div><div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">النتيجة</p><p class="font-bold text-emerald-700 mt-1">Verified</p></div></div>`));
  $('what-triggers-review')?.addEventListener('click',()=>openModal('متى تعاد المراجعة؟','بيانات مهنية موثقة',`<div class="space-y-3 text-sm text-slate-600 leading-7"><p>• تغيير تخصص رئيسي موثّق.</p><p>• استبدال مستند مطلوب أو منتهي.</p><p>• تعديل بيانات مهنية مرتبطة بقرار التحقق.</p><p>• إضافة مؤهل أو إنجاز تريد إظهاره بصفة موثّقة.</p></div><div class="soft-note p-4 mt-4 text-sm text-slate-600">لا يعني أي تعديل بسيط إعادة التحقق الكامل من الحساب؛ تتم مراجعة العناصر المتأثرة حسب قواعد النظام.</div>`));

  document.querySelectorAll('.cred-detail').forEach(b=>b.addEventListener('click',()=>openModal('سجل تحقق المؤهل',b.dataset.cred==='board'?'البورد الأردني في الطب الباطني':'زمالة الرعاية الباطنية المتقدمة',`<div class="space-y-3"><div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between"><span class="font-bold">تم إرسال الإثبات</span><span class="text-xs text-slate-400">12 سبتمبر 2026</span></div><div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between"><span class="font-bold">المراجعة</span><span class="text-xs ${b.dataset.cred==='board'?'text-emerald-600':'text-amber-600'} font-bold">${b.dataset.cred==='board'?'موثّق':'قيد المراجعة'}</span></div></div>`)));
  document.querySelectorAll('.achievement-review').forEach(b=>b.addEventListener('click',()=>openModal('سجل تحقق الإنجاز',b.dataset.ach==='chronic'?'اعتماد التميز في إدارة الأمراض المزمنة':'جائزة التميز في التثقيف الصحي',`<div class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">الحالة الحالية</p><p class="font-extrabold mt-1 ${b.dataset.ach==='chronic'?'text-emerald-700':'text-amber-700'}">${b.dataset.ach==='chronic'?'موثّق':'قيد التحقق'}</p></div><div class="mt-3 rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-400">الإثبات</p><p class="font-bold mt-1">مرفوع ومحفوظ ضمن الطلب</p></div>`)));

  document.querySelectorAll('.history-detail').forEach(b=>b.addEventListener('click',()=>{
    if(b.dataset.history==='rejected') openModal('سبب رفض الطلب السابق','VR-2026-00372',`<div class="danger-note p-5"><div class="flex items-start gap-3"><span class="material-symbols-outlined text-red-600">error</span><div><p class="font-extrabold text-red-800">تم رفض مستند واحد</p><p class="text-sm text-red-700 mt-2 leading-7">ترخيص مزاولة المهنة: الصورة المرفوعة لم تكن واضحة بما يكفي للتحقق من بيانات المستند.</p></div></div></div><div class="mt-4 rounded-xl border border-slate-200 p-4"><p class="font-bold">الإجراء الذي تم</p><p class="text-sm text-slate-500 mt-1">تم استبدال المستند في 10 سبتمبر، ثم إنشاء طلب جديد VR-2026-00418 وإعادة إرساله للمراجعة.</p></div>`);
    else $('view-decision')?.click();
  }));
  $('demo-new-review')?.addEventListener('click',()=>openModal('معاينة إعادة الإرسال','مثال عند تعديل عنصر موثّق',`<div class="soft-note p-4 text-sm text-slate-600">لديك تغييرات مهنية تحتاج مراجعة قبل اعتمادها للعامة.</div><div class="mt-4 space-y-3"><div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between"><div><p class="font-bold">التخصص الرئيسي</p><p class="text-xs text-slate-500 mt-1">تم تغييره من الطب الباطني إلى تخصص جديد</p></div><span class="status-pill bg-amber-50 text-amber-700 border border-amber-200">يحتاج مراجعة</span></div></div>`,`<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold" data-modal-close>إلغاء</button><button id="demo-resubmit" class="h-10 px-4 rounded-xl bg-[#0495F6] text-white font-bold">إعادة إرسال طلب التحقق</button>`));
  document.addEventListener('click',e=>{if(e.target.id==='demo-resubmit'){closeModal();toast('تم إرسال طلب التحقق التجريبي')}});
})();
</script>
<script id="thera-verification-onboarding-runtime">
(() => {
  'use strict';
  const params = new URLSearchParams(window.location.search);
  if (params.get('onboarding') !== '1') return;

  const $ = id => document.getElementById(id);
  $('verification-onboarding-banner')?.classList.remove('hidden');

  let registration = {};
  try {
    registration = JSON.parse(localStorage.getItem('thera_doctor_registration_demo') || '{}');
  } catch (_) {}

  const rawName = (registration.fullName || '').trim();
  const displayName = rawName ? (/^د\./.test(rawName) ? rawName : `د. ${rawName}`) : 'طبيب جديد';
  if ($('verification-doctor-name')) $('verification-doctor-name').textContent = displayName;

  const profileComplete = localStorage.getItem('thera_doctor_onboarding_profile_complete') === 'true';

  let uploadedDocs = [];
  try {
    uploadedDocs = JSON.parse(localStorage.getItem('thera_doctor_onboarding_docs') || '[]');
    if (!Array.isArray(uploadedDocs)) uploadedDocs = [];
  } catch (_) { uploadedDocs = []; }

  let verificationState = localStorage.getItem('thera_doctor_onboarding_verification_status') || 'registered';

  function empty(icon,title,text) {
    return `<div class="onboarding-empty-state"><span class="material-symbols-outlined">${icon}</span><p class="font-extrabold text-slate-800">${title}</p><p class="text-xs text-slate-500 mt-1.5 leading-6">${text}</p></div>`;
  }

  function renderTop() {
    const summary = $('verification-summary-card');
    if (summary) {
      const initials = displayName.replace('د. ','').split(/\s+/).slice(0,2).map(x=>x[0]||'').join('');
      summary.innerHTML = `
        <div class="flex flex-col xl:flex-row xl:items-center gap-5">
          <div class="flex items-center gap-4 min-w-0 flex-1">
            <div class="w-[68px] h-[68px] rounded-2xl bg-gradient-to-br from-[#E6F4FE] to-[#EEF2FF] border border-[#D8E8F6] flex items-center justify-center text-[#0466F1] text-xl font-extrabold shrink-0">${initials || '—'}</div>
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2 mb-1">
                <h2 class="text-xl font-extrabold text-[#0B1020]">${displayName}</h2>
                <span class="status-pill ${verificationState==='submitted'?'bg-blue-50 text-blue-700 border border-blue-200':'bg-amber-50 text-amber-700 border border-amber-200'}">
                  <span class="material-symbols-outlined text-[16px]">${verificationState==='submitted'?'send':'how_to_reg'}</span>
                  ${verificationState==='submitted'?'تم إرسال الطلب':'مسجل'}
                </span>
              </div>
              <p class="text-sm font-semibold text-slate-600">${profileComplete ? 'تم استكمال البيانات المهنية الأساسية' : 'الملف المهني يحتاج استكمال'}</p>
              <p class="text-xs text-slate-400 mt-2">${verificationState==='submitted' ? 'الطلب محفوظ كنسخة Submission ولا يتم تعديل محتواه أثناء المراجعة.' : 'لم يتم إرسال طلب تحقق مهني بعد.'}</p>
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button id="onboarding-edit-profile" class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold flex items-center gap-2" type="button">
              <span class="material-symbols-outlined text-[18px]">badge</span>إدارة الملف المهني
            </button>
          </div>
        </div>`;
    }

    const steps = $('verification-steps-card');
    if (steps) {
      steps.innerHTML = `
        <div class="relative grid grid-cols-2 md:grid-cols-5 gap-4">
          <div class="step-line"></div>
          <div class="step-item ${profileComplete?'done':'current'}"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">${profileComplete?'check':'edit'}</span></div><p class="text-xs font-bold ${profileComplete?'text-slate-700':'text-[#0466F1]'}">البيانات المهنية</p></div>
          <div class="step-item ${verificationState==='submitted'?'done':'current'}"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">${verificationState==='submitted'?'check':'upload_file'}</span></div><p class="text-xs font-bold ${verificationState==='submitted'?'text-slate-700':'text-[#0466F1]'}">المستندات</p></div>
          <div class="step-item ${verificationState==='submitted'?'done':''}"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">${verificationState==='submitted'?'check':'send'}</span></div><p class="text-xs font-bold text-slate-700">تم الإرسال</p></div>
          <div class="step-item"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">manage_search</span></div><p class="text-xs font-bold text-slate-500">المراجعة</p></div>
          <div class="step-item"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">verified</span></div><p class="text-xs font-bold text-slate-500">تم التحقق</p></div>
        </div>`;
    }

    const kpis = $('verification-kpis');
    if (kpis) {
      kpis.innerHTML = `
        <div class="verify-card p-4"><p class="text-xs text-slate-400">الملف المهني</p><p class="text-xl font-extrabold mt-1 ${profileComplete?'text-emerald-700':'text-amber-700'}">${profileComplete?'مكتمل':'غير مكتمل'}</p><p class="text-xs text-slate-400 mt-3">البيانات الأساسية المطلوبة</p></div>
        <div class="verify-card p-4"><p class="text-xs text-slate-400">مستندات مرفوعة</p><p class="text-xl font-extrabold text-slate-800 mt-1 font-num">${uploadedDocs.length}</p><p class="text-xs text-slate-400 mt-3">قبل الإرسال للمراجعة</p></div>
        <div class="verify-card p-4"><p class="text-xs text-slate-400">حالة الطلب</p><p class="text-xl font-extrabold mt-1 ${verificationState==='submitted'?'text-blue-700':'text-slate-800'}">${verificationState==='submitted'?'تم الإرسال':'لم يُرسل'}</p><p class="text-xs text-slate-400 mt-3">مستقلة عن الحالة التشغيلية</p></div>
        <div class="verify-card p-4"><p class="text-xs text-slate-400">حالة التحقق</p><p class="text-xl font-extrabold text-amber-700 mt-1">${verificationState==='submitted'?'قيد الانتظار':'مسجل'}</p><p class="text-xs text-slate-400 mt-3">حتى صدور قرار THERA</p></div>`;
    }

    document.getElementById('onboarding-edit-profile')?.addEventListener('click', () => {
      window.location.href = 'THERA_Professional_Profile_Interactive_v4_Onboarding.html?onboarding=1';
    });
  }

  function renderPanels() {
    const overview = document.querySelector('[data-panel="overview"]');
    if (overview) {
      overview.innerHTML = `
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4">
          <div class="verify-card p-5 md:p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
              <div><h3 class="text-lg font-extrabold text-[#0B1020]">حالة التحقق الحالية</h3><p class="text-sm text-slate-500 mt-1">جهّز المتطلبات قبل إرسال أول طلب تحقق.</p></div>
              <span class="status-pill ${verificationState==='submitted'?'bg-blue-50 text-blue-700 border border-blue-200':'bg-amber-50 text-amber-700 border border-amber-200'}">${verificationState==='submitted'?'تم الإرسال':'Registered / مسجل'}</span>
            </div>
            <div class="rounded-2xl border ${verificationState==='submitted'?'border-blue-200 bg-blue-50/60':'border-amber-200 bg-amber-50/60'} p-5">
              <p class="font-extrabold ${verificationState==='submitted'?'text-blue-900':'text-amber-900'}">${verificationState==='submitted'?'تم إرسال طلب التحقق':'لم يتم إرسال طلب تحقق بعد'}</p>
              <p class="text-sm ${verificationState==='submitted'?'text-blue-800':'text-amber-800'} mt-1 leading-7">${verificationState==='submitted'?'الطلب أصبح نسخة Submission مستقلة وسيظهر هنا تقدم المراجعة عند بدء THERA بمراجعته.':'أكمل الملف المهني وارفع المستندات المطلوبة ثم أرسل الطلب للمراجعة.'}</p>
            </div>

            <div class="mt-5">
              <div class="onboarding-check"><span class="material-symbols-outlined ${profileComplete?'text-emerald-600':'text-amber-600'}">${profileComplete?'check_circle':'pending'}</span><div><p class="font-bold text-sm">الملف المهني</p><p class="text-xs text-slate-500 mt-1">${profileComplete?'تم استكمال البيانات المهنية الأساسية.':'يحتاج استكمال قبل إرسال الطلب.'}</p></div></div>
              <div class="onboarding-check"><span class="material-symbols-outlined ${uploadedDocs.length?'text-emerald-600':'text-slate-400'}">${uploadedDocs.length?'check_circle':'upload_file'}</span><div><p class="font-bold text-sm">المستندات</p><p class="text-xs text-slate-500 mt-1">${uploadedDocs.length?`تم رفع ${uploadedDocs.length} مستند/مستندات في المعاينة.`:'لم يتم رفع مستندات بعد.'}</p></div></div>
              <div class="onboarding-check"><span class="material-symbols-outlined text-slate-400">rule</span><div><p class="font-bold text-sm">قائمة التحقق</p><p class="text-xs text-slate-500 mt-1">التنفيذ الفعلي يتحقق من قائمة المستندات والمتطلبات القانونية المعتمدة قبل قبول الإرسال.</p></div></div>
            </div>
          </div>
          <aside class="verify-card p-5 h-fit">
            <h4 class="font-extrabold text-slate-800">الخطوة التالية</h4>
            <p class="text-sm text-slate-500 leading-7 mt-2">${verificationState==='submitted'?'تابع حالة الطلب من تبويب «حالة الطلب».':'ابدأ برفع المستندات الداعمة، ثم راجع حالة الطلب قبل الإرسال.'}</p>
            <button id="go-documents-onboarding" class="w-full h-10 mt-4 rounded-xl bg-[#0495F6] hover:bg-[#0466F1] text-white font-bold text-sm">${verificationState==='submitted'?'عرض حالة الطلب':'الانتقال إلى المستندات'}</button>
          </aside>
        </div>`;
    }

    const documents = document.querySelector('[data-panel="documents"]');
    if (documents) {
      documents.innerHTML = `
        <div class="verify-card overflow-hidden">
          <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div><h3 class="text-lg font-extrabold text-[#0B1020]">المستندات</h3><p class="text-sm text-slate-500 mt-1">ارفع ملفات الإثبات المطلوبة لمسار التحقق المهني.</p></div>
            <label class="h-10 px-4 rounded-xl bg-[#0495F6] text-white text-sm font-bold flex items-center gap-2 cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">upload_file</span>رفع مستند
              <input id="onboarding-doc-upload" type="file" class="hidden" accept=".pdf,image/*">
            </label>
          </div>
          <div class="p-5">
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 text-xs text-blue-800 leading-6 mb-4">
              أسماء وقائمة المستندات القانونية النهائية لم تُحسم في التحليل. في التنفيذ تُحمّل هنا قائمة التحقق المعتمدة، والـBackend يمنع الإرسال إذا كانت المتطلبات ناقصة.
            </div>
            <div id="onboarding-doc-list"></div>
          </div>
        </div>`;
    }

    const credentials = document.querySelector('[data-panel="credentials"]');
    if (credentials) {
      credentials.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold text-[#0B1020] mb-4">المؤهلات المهنية</h3>${empty('workspace_premium','لا توجد مؤهلات مضافة للتحقق','أضف المؤهلات من الملف المهني، ثم تظهر هنا حالة التحقق لكل مؤهل ومستند الإثبات المرتبط به.')}<button id="verification-manage-profile-credentials" class="h-10 px-4 rounded-xl border border-[#0495F6] text-[#0495F6] hover:bg-blue-50 font-bold text-sm mt-4">إدارة الملف المهني</button></div>`;
    }

    const achievements = document.querySelector('[data-panel="achievements"]');
    if (achievements) {
      achievements.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold text-[#0B1020] mb-4">الإنجازات المهنية</h3>${empty('military_tech','لا توجد إنجازات مرسلة للتحقق','الإنجازات اختيارية، وعند إضافتها مع إثبات مناسب تظهر هنا حالة المراجعة والتحقق.')}</div>`;
    }

    const submission = document.querySelector('[data-panel="submission"]');
    if (submission) {
      submission.innerHTML = `
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4">
          <div class="verify-card p-5 md:p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
              <div><h3 class="text-lg font-extrabold">حالة الطلب الحالي</h3><p class="text-sm text-slate-500 mt-1">أول طلب تحقق للطبيب.</p></div>
              <span class="status-pill ${verificationState==='submitted'?'bg-blue-50 text-blue-700 border border-blue-200':'bg-slate-100 text-slate-600 border border-slate-200'}">${verificationState==='submitted'?'Submitted':'لم يُرسل'}</span>
            </div>
            ${verificationState==='submitted'
              ? `<div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-5"><p class="font-extrabold text-blue-900">تم إرسال الطلب</p><p class="text-sm text-blue-800 mt-2 leading-7">سيظهر انتقال الطلب إلى «قيد المراجعة» ثم القرار النهائي عند تحديث حالته من THERA.</p></div>`
              : empty('send','الطلب لم يُرسل بعد','راجع الملف المهني والمستندات. عند الإرسال يصبح محتوى الطلب Snapshot مستقل للمراجعة.')
            }
          </div>
          <aside class="verify-card p-5 h-fit">
            <h4 class="font-extrabold">إرسال طلب التحقق</h4>
            <p class="text-sm text-slate-500 leading-7 mt-2">${verificationState==='submitted'?'تم إرسال الطلب ولا يمكن إرسال نسخة أخرى أثناء المراجعة.':'قبل الإرسال يتحقق النظام فعليًا من قائمة المتطلبات المعتمدة.'}</p>
            <button id="submit-onboarding-verification" class="w-full h-10 mt-4 rounded-xl bg-[#0495F6] text-white font-bold text-sm disabled:opacity-45 disabled:cursor-not-allowed" ${verificationState==='submitted'?'disabled':''}>${verificationState==='submitted'?'تم إرسال الطلب':'إرسال طلب التحقق'}</button>
          </aside>
        </div>`;
    }

    const history = document.querySelector('[data-panel="history"]');
    if (history) {
      history.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold mb-4">سجل المراجعات</h3>${verificationState==='submitted'
        ? `<div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between gap-4"><div><p class="font-extrabold">أول طلب تحقق</p><p class="text-xs text-slate-500 mt-1">تم الإرسال في هذه المعاينة</p></div><span class="status-pill bg-blue-50 text-blue-700 border border-blue-200">Submitted</span></div>`
        : empty('history','لا توجد طلبات سابقة','يبدأ سجل المراجعات بعد إرسال أول طلب تحقق، وتبقى الطلبات والقرارات السابقة محفوظة.')
      }</div>`;
    }

    bindOnboardingPanelActions();
    renderDocList();
  }

  function renderDocList() {
    const host = $('onboarding-doc-list');
    if (!host) return;

    if (!uploadedDocs.length) {
      host.innerHTML = empty('upload_file','لم يتم رفع مستندات بعد','استخدم زر «رفع مستند» لإضافة ملفات الإثبات إلى طلب التحقق.');
      return;
    }

    host.innerHTML = uploadedDocs.map((doc,index)=>`
      <div class="document-row p-4 mb-2 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0466F1] flex items-center justify-center shrink-0"><span class="material-symbols-outlined">description</span></div>
          <div class="min-w-0"><p class="font-bold text-sm text-slate-800 truncate">${doc.name}</p><p class="text-xs text-slate-500 mt-1">مرفوع • بانتظار التحقق بعد إرسال الطلب</p></div>
        </div>
        ${verificationState==='submitted'?'':'<button class="remove-onboarding-doc text-xs font-bold text-red-600" data-index="'+index+'">حذف</button>'}
      </div>`).join('');

    host.querySelectorAll('.remove-onboarding-doc').forEach(btn=>btn.addEventListener('click',()=>{
      uploadedDocs.splice(Number(btn.dataset.index),1);
      localStorage.setItem('thera_doctor_onboarding_docs',JSON.stringify(uploadedDocs));
      renderTop();
      renderDocList();
    }));
  }

  function bindOnboardingPanelActions() {
    $('go-documents-onboarding')?.addEventListener('click',()=>{
      document.querySelector(`[data-tab="${verificationState==='submitted'?'submission':'documents'}"]`)?.click();
    });

    $('verification-manage-profile-credentials')?.addEventListener('click',()=>{
      window.location.href='THERA_Professional_Profile_Interactive_v4_Onboarding.html?onboarding=1';
    });

    $('onboarding-doc-upload')?.addEventListener('change',e=>{
      const file=e.target.files?.[0];
      if(!file || verificationState==='submitted') return;
      uploadedDocs.push({name:file.name,size:file.size,type:file.type,addedAt:new Date().toISOString()});
      localStorage.setItem('thera_doctor_onboarding_docs',JSON.stringify(uploadedDocs));
      renderTop();
      renderDocList();
      e.target.value='';
    });

    $('submit-onboarding-verification')?.addEventListener('click',()=>{
      if(!profileComplete){
        window.location.href='THERA_Professional_Profile_Interactive_v4_Onboarding.html?onboarding=1';
        return;
      }
      if(!uploadedDocs.length){
        document.querySelector('[data-tab="documents"]')?.click();
        return;
      }

      verificationState='submitted';
      localStorage.setItem('thera_doctor_onboarding_verification_status','submitted');
      renderTop();
      renderPanels();
      document.querySelector('[data-tab="submission"]')?.click();
    });
  }

  $('back-to-onboarding-profile')?.addEventListener('click',()=>{
    window.location.href='THERA_Professional_Profile_Interactive_v4_Onboarding.html?onboarding=1';
  });

  renderTop();
  renderPanels();
})();
</script>
@php
  $verificationPageState = [
    'doctor' => [
      'name' => $doctor->name,
      'professional_title' => $doctor->professional_title,
      'credentials_count' => $doctor->credentials_count,
      'achievements_count' => $doctor->achievements_count,
    ],
    'profileComplete' => $profileIsComplete,
    'request' => $verificationRequest ? [
      'id' => $verificationRequest->id,
      'reference' => $verificationRequest->reference,
      'status' => $verificationRequest->status,
      'submitted_at' => $verificationRequest->submitted_at?->format('Y/m/d - H:i'),
      'decided_at' => $verificationRequest->decided_at?->format('Y/m/d - H:i'),
      'rejection_reason' => $verificationRequest->rejection_reason,
    ] : null,
    'documents' => $verificationDocuments,
    'history' => $verificationHistory->map(fn ($item) => [
      'id' => $item->id,
      'reference' => $item->reference,
      'status' => $item->status,
      'submitted_at' => $item->submitted_at?->format('Y/m/d - H:i'),
      'decided_at' => $item->decided_at?->format('Y/m/d - H:i'),
      'decided_by' => $item->decidedBy?->name,
      'rejection_reason' => $item->rejection_reason,
      'documents' => collect($item->documents ?? [])->map(fn ($document, $type) => [
        'label' => \App\Models\DoctorVerificationRequest::requiredDocumentLabels()[$type] ?? 'مستند',
        'url' => route('doctor.account.verification.documents.download', [$item, $type]),
      ])->values()->all(),
    ])->values()->all(),
    'routes' => [
      'profile' => route('doctor.account.professional-profile', ['onboarding' => 1]),
      'upload' => route('doctor.account.verification.documents.store'),
      'submit' => route('doctor.account.verification.submit'),
      'restart' => route('doctor.account.verification.restart'),
    ],
    'csrf' => csrf_token(),
    'statusMessage' => session('status'),
    'errors' => $errors->all(),
  ];
@endphp
<script id="thera-verification-live-runtime">
(() => {
  'use strict';

  const state = {{ Illuminate\Support\Js::from($verificationPageState) }};

  const $ = id => document.getElementById(id);
  const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
  })[character]);
  const status = state.request?.status || 'registered';
  const isSubmitted = status === 'submitted';
  const isApproved = status === 'approved';
  const isRejected = status === 'rejected';
  const isLocked = isSubmitted || isApproved;
  const uploadedCount = state.documents.filter(document => document.uploaded).length;
  const allDocumentsUploaded = uploadedCount === state.documents.length;
  const canSubmit = state.profileComplete && allDocumentsUploaded && status === 'draft';
  const initials = state.doctor.name.replace(/^د\.\s*/u, '').split(/\s+/).slice(0, 2).map(part => part[0] || '').join('');

  const statusMeta = {
    registered: ['مسجل', 'how_to_reg', 'bg-amber-50 text-amber-700 border border-amber-200'],
    draft: ['مسودة', 'edit_document', 'bg-slate-100 text-slate-700 border border-slate-200'],
    submitted: ['قيد المراجعة', 'send', 'bg-blue-50 text-blue-700 border border-blue-200'],
    approved: ['تم التحقق', 'verified', 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
    rejected: ['يحتاج تعديل', 'error', 'bg-red-50 text-red-700 border border-red-200'],
  }[status];

  const emptyState = (icon, title, description) => `
    <div class="onboarding-empty-state">
      <span class="material-symbols-outlined">${icon}</span>
      <p class="font-extrabold text-slate-800">${title}</p>
      <p class="text-xs text-slate-500 mt-1.5 leading-6">${description}</p>
    </div>`;

  function renderTop() {
    const summary = $('verification-summary-card');
    if (summary) {
      summary.innerHTML = `
        <div class="flex flex-col xl:flex-row xl:items-center gap-5">
          <div class="flex items-center gap-4 min-w-0 flex-1">
            <div class="w-[68px] h-[68px] rounded-2xl bg-gradient-to-br from-[#E6F4FE] to-[#EEF2FF] border border-[#D8E8F6] flex items-center justify-center text-[#0466F1] text-xl font-extrabold shrink-0">${escapeHtml(initials || 'ط')}</div>
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2 mb-1">
                <h2 class="text-xl font-extrabold text-[#0B1020]">${escapeHtml(state.doctor.name)}</h2>
                <span class="status-pill ${statusMeta[2]}"><span class="material-symbols-outlined text-[16px]">${statusMeta[1]}</span>${statusMeta[0]}</span>
              </div>
              <p class="text-sm font-semibold text-slate-600">${escapeHtml(state.doctor.professional_title || 'لم يحدد المسمى المهني')}</p>
              <p class="text-xs text-slate-400 mt-2">${state.request?.reference ? `رقم الطلب: <span class="font-num">${escapeHtml(state.request.reference)}</span>` : 'لم يتم إرسال طلب تحقق مهني بعد.'}</p>
            </div>
          </div>
          <a href="${state.routes.profile}" class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">badge</span>إدارة الملف المهني
          </a>
        </div>`;
    }

    const steps = $('verification-steps-card');
    if (steps) {
      const documentsComplete = allDocumentsUploaded;
      steps.innerHTML = `
        <div class="relative grid grid-cols-2 md:grid-cols-5 gap-4">
          <div class="step-line"></div>
          ${step('البيانات المهنية', state.profileComplete, !state.profileComplete, 'edit')}
          ${step('المستندات', documentsComplete, state.profileComplete && !documentsComplete, 'upload_file')}
          ${step('تم الإرسال', isSubmitted || isApproved || isRejected, documentsComplete && !isSubmitted && !isApproved && !isRejected, 'send')}
          ${step('المراجعة', isApproved || isRejected, isSubmitted, 'manage_search')}
          ${step('تم التحقق', isApproved, false, 'verified')}
        </div>`;
    }

    const kpis = $('verification-kpis');
    if (kpis) {
      kpis.innerHTML = `
        ${kpi('الملف المهني', state.profileComplete ? 'مكتمل' : 'غير مكتمل', state.profileComplete ? 'text-emerald-700' : 'text-amber-700', 'البيانات الأساسية والتخصص')}
        ${kpi('المستندات المطلوبة', `${uploadedCount}/${state.documents.length}`, allDocumentsUploaded ? 'text-emerald-700' : 'text-slate-800', allDocumentsUploaded ? 'جاهزة للإرسال' : 'أكمل الملفات المطلوبة')}
        ${kpi('حالة الطلب', state.request ? statusMeta[0] : 'لم يُرسل', isSubmitted ? 'text-blue-700' : (isApproved ? 'text-emerald-700' : 'text-slate-800'), state.request?.reference || 'لا يوجد رقم طلب')}
        ${kpi('حالة التحقق', isApproved ? 'موثّق' : (isRejected ? 'مرفوض' : 'غير موثّق'), isApproved ? 'text-emerald-700' : (isRejected ? 'text-red-700' : 'text-amber-700'), 'حتى صدور قرار الإدارة')}`;
    }
  }

  function step(label, done, current, icon) {
    return `<div class="step-item ${done ? 'done' : (current ? 'current' : '')}"><div class="step-dot"><span class="material-symbols-outlined text-[19px]">${done ? 'check' : icon}</span></div><p class="text-xs font-bold ${current ? 'text-[#0466F1]' : 'text-slate-700'}">${label}</p></div>`;
  }

  function kpi(label, value, color, description) {
    return `<div class="verify-card p-4"><p class="text-xs text-slate-400">${label}</p><p class="text-xl font-extrabold mt-1 ${color}">${escapeHtml(value)}</p><p class="text-xs text-slate-400 mt-3">${escapeHtml(description)}</p></div>`;
  }

  function renderPanels() {
    const overview = document.querySelector('[data-panel="overview"]');
    if (overview) {
      const message = isApproved
        ? ['تم التحقق من الملف المهني', 'اعتمدت الإدارة طلب التحقق ويمكن إظهار صفة الطبيب الموثق.', 'border-emerald-200 bg-emerald-50/60 text-emerald-900']
        : isRejected
          ? ['يحتاج الطلب إلى تعديل', state.request.rejection_reason || 'راجع المستندات المطلوبة ثم أعد الإرسال.', 'border-red-200 bg-red-50/60 text-red-900']
          : isSubmitted
            ? ['تم إرسال الطلب للمراجعة', 'الطلب محفوظ ولا يمكن تعديل مستنداته حتى صدور قرار الإدارة.', 'border-blue-200 bg-blue-50/60 text-blue-900']
            : ['جهّز طلب التحقق المهني', 'أكمل الملف المهني وارفع المستندين المطلوبين ثم أرسل الطلب.', 'border-amber-200 bg-amber-50/60 text-amber-900'];
      overview.innerHTML = `
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4">
          <div class="verify-card p-5 md:p-6">
            <div class="flex items-start justify-between gap-4 mb-5"><div><h3 class="text-lg font-extrabold text-[#0B1020]">حالة التحقق الحالية</h3><p class="text-sm text-slate-500 mt-1">الحالة مرتبطة بقاعدة البيانات وطلب الإدارة.</p></div><span class="status-pill ${statusMeta[2]}">${statusMeta[0]}</span></div>
            <div class="rounded-2xl border p-5 ${message[2]}"><p class="font-extrabold">${message[0]}</p><p class="text-sm mt-2 leading-7">${escapeHtml(message[1])}</p></div>
            <div class="mt-5">
              ${checkRow('الملف المهني', state.profileComplete, state.profileComplete ? 'مكتمل وجاهز.' : 'أكمل المعلومات المهنية والتخصص الرئيسي.')}
              ${checkRow('المستندات المطلوبة', allDocumentsUploaded, allDocumentsUploaded ? 'تم رفع جميع المستندات المطلوبة.' : `تم رفع ${uploadedCount} من ${state.documents.length}.`)}
              ${checkRow('الإرسال إلى الإدارة', isSubmitted || isApproved || isRejected, state.request?.reference ? `رقم الطلب ${state.request.reference}` : 'لم يتم إرسال الطلب بعد.')}
            </div>
          </div>
          <aside class="verify-card p-5 h-fit"><h4 class="font-extrabold text-slate-800">الخطوة التالية</h4><p class="text-sm text-slate-500 leading-7 mt-2">${isSubmitted ? 'تابع حالة الطلب حتى يصدر قرار الإدارة.' : (isApproved ? 'تم إغلاق مسار التحقق بنجاح.' : 'انتقل إلى المستندات ثم راجع الطلب وأرسله.')}</p><button id="live-next-step" class="w-full h-10 mt-4 rounded-xl bg-[#0495F6] hover:bg-[#0466F1] text-white font-bold text-sm">${isSubmitted || isApproved ? 'عرض حالة الطلب' : 'الانتقال إلى المستندات'}</button></aside>
        </div>`;
    }

    renderDocuments();
    renderRelatedPanels();
    renderSubmission();
    renderHistory();

    $('live-next-step')?.addEventListener('click', () => document.querySelector(`[data-tab="${isSubmitted || isApproved ? 'submission' : 'documents'}"]`)?.click());
  }

  function checkRow(title, completed, description) {
    return `<div class="onboarding-check"><span class="material-symbols-outlined ${completed ? 'text-emerald-600' : 'text-amber-600'}">${completed ? 'check_circle' : 'pending'}</span><div><p class="font-bold text-sm">${title}</p><p class="text-xs text-slate-500 mt-1">${escapeHtml(description)}</p></div></div>`;
  }

  function renderDocuments() {
    const panel = document.querySelector('[data-panel="documents"]');
    if (!panel) return;

    panel.innerHTML = `
      <div class="verify-card overflow-hidden">
        <div class="p-5 md:p-6 border-b border-slate-100"><h3 class="text-lg font-extrabold text-[#0B1020]">المستندات المطلوبة</h3><p class="text-sm text-slate-500 mt-1">PDF أو JPG أو PNG، بحد أقصى 5 ميجابايت لكل ملف.</p></div>
        <div class="divide-y divide-slate-100">${state.documents.map(documentRow).join('')}</div>
      </div>`;
  }

  function documentRow(document) {
    const action = document.uploaded
      ? `<div class="flex items-center gap-2"><a class="h-10 px-3 rounded-xl border border-slate-200 text-[#0495F6] text-sm font-bold flex items-center" href="${document.download_url}">عرض</a>${isLocked ? '' : uploadLabel(document.type, 'استبدال')}</div>`
      : isLocked ? '' : uploadLabel(document.type, 'رفع المستند');
    return `<div class="document-row p-5 flex flex-col md:flex-row md:items-center justify-between gap-4"><div class="flex items-center gap-3 min-w-0"><div class="w-10 h-10 rounded-xl ${document.uploaded ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-[#0466F1]'} flex items-center justify-center shrink-0"><span class="material-symbols-outlined">${document.uploaded ? 'task_alt' : 'description'}</span></div><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800">${escapeHtml(document.label)}</p><span class="text-[11px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">مطلوب</span></div><p class="text-xs text-slate-500 mt-1.5 truncate">${document.uploaded ? escapeHtml(document.original_name) : 'لم يتم رفع الملف بعد'}</p></div></div>${action}</div>`;
  }

  function uploadLabel(type, label) {
    return `<form action="${state.routes.upload}" method="POST" enctype="multipart/form-data" class="verification-upload-form"><input type="hidden" name="_token" value="${state.csrf}"><input type="hidden" name="document_type" value="${type}"><label class="h-10 px-4 rounded-xl bg-[#0495F6] hover:bg-[#0466F1] text-white text-sm font-bold flex items-center gap-2 cursor-pointer"><span class="material-symbols-outlined text-[18px]">upload_file</span>${label}<input class="hidden verification-file-input" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required></label></form>`;
  }

  function renderRelatedPanels() {
    const credentials = document.querySelector('[data-panel="credentials"]');
    if (credentials) credentials.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold text-[#0B1020]">المؤهلات المهنية</h3><p class="text-sm text-slate-500 mt-2">لديك <strong class="text-slate-800">${state.doctor.credentials_count}</strong> مؤهل/شهادة مضافة. تتم مراجعتها من قسم المؤهلات في الملف المهني.</p><a href="${state.routes.profile}&tab=credentials" class="inline-flex h-10 px-4 mt-4 rounded-xl border border-[#0495F6] text-[#0495F6] items-center font-bold text-sm">إدارة المؤهلات</a></div>`;
    const achievements = document.querySelector('[data-panel="achievements"]');
    if (achievements) achievements.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold text-[#0B1020]">الإنجازات المهنية</h3><p class="text-sm text-slate-500 mt-2">لديك <strong class="text-slate-800">${state.doctor.achievements_count}</strong> إنجاز مضاف. الإنجازات اختيارية وتراجع بصورة مستقلة.</p><a href="${state.routes.profile}&tab=achievements" class="inline-flex h-10 px-4 mt-4 rounded-xl border border-[#0495F6] text-[#0495F6] items-center font-bold text-sm">إدارة الإنجازات</a></div>`;
  }

  function renderSubmission() {
    const panel = document.querySelector('[data-panel="submission"]');
    if (!panel) return;

    const details = state.request?.reference
      ? `<div class="rounded-2xl border border-slate-200 p-5"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><p class="text-xs text-slate-400">رقم الطلب</p><p class="font-num font-bold mt-1">${escapeHtml(state.request.reference)}</p></div><div><p class="text-xs text-slate-400">تاريخ الإرسال</p><p class="font-bold mt-1">${escapeHtml(state.request.submitted_at || '—')}</p></div></div>${isRejected ? `<div class="mt-4 p-4 rounded-xl bg-red-50 border border-red-200"><p class="font-bold text-red-800">سبب الرفض</p><p class="text-sm text-red-700 mt-1">${escapeHtml(state.request.rejection_reason || 'لم يحدد سبب.')}</p></div>` : ''}</div>`
      : emptyState('send', 'الطلب لم يُرسل بعد', 'أكمل الملف والمستندات ثم أرسل الطلب للإدارة.');
    panel.innerHTML = `<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-4"><div class="verify-card p-5 md:p-6"><div class="flex items-center justify-between gap-3 mb-5"><div><h3 class="text-lg font-extrabold">حالة الطلب</h3><p class="text-sm text-slate-500 mt-1">الحالة الفعلية المسجلة في النظام.</p></div><span class="status-pill ${statusMeta[2]}">${statusMeta[0]}</span></div>${details}</div><aside class="verify-card p-5 h-fit"><h4 class="font-extrabold">إرسال طلب التحقق</h4><p class="text-sm text-slate-500 leading-7 mt-2">${isRejected ? 'عدّل المستندات المطلوبة ثم أعد تقديم الطلب.' : canSubmit ? 'جميع المتطلبات مكتملة ويمكن إرسال الطلب الآن.' : (isLocked ? 'لا يمكن إرسال طلب آخر في الحالة الحالية.' : 'أكمل الملف وارفع المستندات المطلوبة أولًا.')}</p><form action="${state.routes.submit}" method="POST" id="live-verification-submit-form"><input type="hidden" name="_token" value="${state.csrf}"><button class="w-full h-10 mt-4 rounded-xl bg-[#0495F6] text-white font-bold text-sm disabled:opacity-45 disabled:cursor-not-allowed" type="submit" ${canSubmit ? '' : 'disabled'}>${isSubmitted ? 'قيد المراجعة' : (isApproved ? 'تم التحقق' : 'إرسال طلب التحقق')}</button></form>${isRejected ? `<form action="${state.routes.restart}" method="POST" class="mt-3"><input type="hidden" name="_token" value="${state.csrf}"><button class="w-full h-10 rounded-xl bg-[#0495F6] text-white font-bold text-sm" type="submit">تعديل وإعادة تقديم الطلب</button></form>` : ''} </aside></div>`;
  }

  function renderHistory() {
    const panel = document.querySelector('[data-panel="history"]');
    if (!panel) return;
    panel.innerHTML = `<div class="verify-card p-5 md:p-6"><h3 class="text-lg font-extrabold mb-4">سجل المراجعات</h3>${state.history.length ? state.history.map(item => { const labels = {submitted: 'قيد المراجعة', approved: 'تم الاعتماد', rejected: 'مرفوض'}; return `<article class="rounded-xl border border-slate-200 p-4 mb-3"><div class="flex flex-wrap justify-between gap-3"><div><p class="font-extrabold font-num">${escapeHtml(item.reference)}</p><p class="text-xs text-slate-500 mt-1">أُرسل في ${escapeHtml(item.submitted_at)}</p></div><span class="status-pill bg-slate-100 text-slate-700">${labels[item.status] || '—'}</span></div>${item.decided_at ? `<p class="text-xs text-slate-500 mt-2">القرار: ${escapeHtml(item.decided_at)} · ${escapeHtml(item.decided_by || 'الإدارة')}</p>` : ''}${item.rejection_reason ? `<p class="text-sm text-red-700 mt-2">${escapeHtml(item.rejection_reason)}</p>` : ''}<div class="flex flex-wrap gap-3 mt-3">${item.documents.map(document => `<a class="text-sm text-[#0495F6] font-bold" href="${escapeHtml(document.url)}">${escapeHtml(document.label)}</a>`).join('')}</div></article>`; }).join('') : emptyState('history', 'لا توجد طلبات سابقة', 'سيظهر أول طلب هنا بعد إرساله إلى الإدارة.')}</div>`;
  }

  renderTop();
  renderPanels();

  document.querySelectorAll('.verification-file-input').forEach(input => input.addEventListener('change', () => {
    if (input.files?.length) input.closest('form')?.submit();
  }));

  const submitForm = $('live-verification-submit-form');
  if (submitForm) {
    submitForm.dataset.theraConfirm = 'إرسال طلب التحقق؟';
    submitForm.dataset.theraConfirmText = 'سيُرسل الطلب إلى الإدارة، ولن تتمكن من تعديل المستندات أثناء المراجعة.';
    submitForm.dataset.theraConfirmButton = 'نعم، إرسال الطلب';
  }

  const requestedTab = new URLSearchParams(window.location.search).get('tab');
  if (requestedTab) document.querySelector(`[data-tab="${requestedTab}"]`)?.click();
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>
