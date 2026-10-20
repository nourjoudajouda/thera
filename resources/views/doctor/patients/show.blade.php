<!DOCTYPE html><html dir="rtl" lang="ar" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>تفاصيل المريض - THERA</title>
<!-- Google Fonts: Tajawal (Arabic) & Inter (Numbers/Latin) -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
<!-- Google Material Symbols Outlined -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<!-- Tailwind CSS v3 with Plugins -->


@include('doctor.partials.assets')
<style data-purpose="custom-utilities">
    /* Global styling and custom scrollbar adjustments */
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #F6F9FC;
      color: #0B1020;
      -webkit-font-smoothing: antialiased;
    }
    
    .font-inter {
      font-family: 'Inter', sans-serif;
    }

    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    /* Directional helpers */
    .dir-ltr {
      direction: ltr;
      display: inline-block;
    }
  </style>

</head>
<body class="bg-bodyBg text-textMain min-h-screen overflow-x-hidden">
<!-- BEGIN: Topbar -->
<header class="fixed top-0 left-0 right-[268px] h-16 bg-white/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-6 transition-all duration-300 border-b border-[#E2E8F0]" id="top-header">
<!-- Topbar Right Side: Quick Add + Global Search -->
<div class="flex items-center gap-4">
<!-- Quick Add Container -->
<div class="relative" id="quick-add-container" onmouseenter="window.handleQuickAddEnter()" onmouseleave="window.handleQuickAddLeave()">
<button class="flex items-center gap-1.5 h-10 px-4 rounded-lg bg-[#0495F6] text-white font-semibold text-[14px] hover:bg-[#0466F1] active:bg-[#1035DB] transition-all shadow-sm" id="quick-add-btn" onclick="window.toggleQuickAddMenu(event)" type="button">
<span class="material-symbols-outlined text-[20px]">add</span>
<span class="">إضافة</span>
<span class="material-symbols-outlined text-[18px] transition-transform duration-200" id="quick-add-arrow">expand_more</span>
</button>
<!-- Quick Add Dropdown Menu -->
<div class="hidden absolute top-12 right-0 w-64 bg-white rounded-xl shadow-xl border border-[#E2E8F0] p-2 z-50" id="quick-add-dropdown">
<div class="px-2.5 py-1 text-[11px] text-[#64748B] font-semibold">تشغيلي</div>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">person_add</span>
<span class="">إضافة مريض</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">event</span>
<span class="">موعد جديد</span>
</a>
<div class="h-px bg-[#E2E8F0] my-1.5"></div>
<div class="px-2.5 py-1 text-[11px] text-[#64748B] font-semibold">سريري</div>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-emerald-600">stethoscope</span>
<span class="">زيارة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-violet-600">prescriptions</span>
<span class="">وصفة طبية</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-indigo-600">science</span>
<span class="">طلب مختبر</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-cyan-600">radiology</span>
<span class="">طلب أشعة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-teal-600">sync_alt</span>
<span class="">إحالة / متابعة</span>
</a>
</div>
</div>
<!-- Global Quick Search -->
<div class="relative hidden sm:block w-72 md:w-96">
<span class="material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-[#64748B] pointer-events-none">search</span>
<input class="w-full h-10 pr-10 pl-4 bg-[#F8FAFC] rounded-lg border border-[#E2E8F0] text-[13px] text-[#0B1020] placeholder-[#64748B]/70 focus:outline-none focus:border-[#0495F6] focus:bg-white focus:ring-2 focus:ring-[#0495F6]/20 transition-all" placeholder="ابحث عن مريض أو موعد... (⌘K)" type="text">
</div>
</div>
<!-- Topbar Left Side: Notifications + Language + Theme -->
<div class="flex items-center gap-2">
<button class="relative p-2 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors" title="الإشعارات" type="button">
<span class="material-symbols-outlined text-[22px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
</button>
<button class="flex items-center gap-1.5 px-2.5 py-1.5 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors text-[12px] font-semibold" title="تغيير اللغة" type="button">
<span class="material-symbols-outlined text-[18px]">language</span>
<span class="">عربي (AR)</span>
</button>
<button class="p-2 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors" id="theme-toggle-btn" title="تبديل المظهر" type="button">
<span class="material-symbols-outlined text-[20px]">light_mode</span>
</button>
</div>
</header>
<!-- END: Topbar -->
<!-- BEGIN: Sidebar -->
<aside class="fixed top-0 right-0 h-screen z-50 bg-[#16243E] text-white flex flex-col transition-all duration-300 shadow-[0_4px_20px_rgba(11,16,32,0.15)] w-[268px]" id="app-sidebar"><style>#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-header { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; } #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-wrapper { width: 100%; justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; } #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-pin-btn { display: none !important; }</style>
<!-- Sidebar Brand Header -->
<div class="h-16 px-4 flex items-center justify-between border-b border-[#223456] bg-[#132038] shrink-0 sidebar-header">
<div class="flex items-center gap-3 overflow-hidden select-none sidebar-brand-wrapper">
<img alt="THERA Logo" class="h-9 max-w-[130px] object-contain flex-shrink-0 sidebar-brand-text" src="{{ asset('images/doctor/thera-logo.png') }}"/>
</div>
<button class="p-1.5 text-[#0495F6] bg-white/10 rounded-lg hover:bg-white/20 transition-colors sidebar-pin-btn" id="sidebar-pin-toggle" onclick="toggleSidebarPin()" title="طي القائمة الجانبية" type="button">
<span class="material-symbols-outlined text-[20px]" id="sidebar-pin-icon">push_pin</span>
</button>
</div>
<!-- Navigation Links -->
<div class="sidebar-scroll flex-1 min-h-0 overflow-y-auto py-3 px-3 flex flex-col gap-6">
<!-- Group 1: العمل اليومي -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">العمل اليومي</div>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="daily-overview" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">grid_view</span>
<span class="sidebar-label truncate">الرئيسية</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="appointments" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">calendar_today</span>
<span class="sidebar-label truncate">المواعيد</span>
</a>
<a aria-current="page" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all bg-[#0495F6] text-white font-semibold shadow-sm text-[14px]" data-path="patients" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">person_search</span>
<span class="sidebar-label truncate">المرضى</span>
</a>
</nav>
<!-- Group 2: العمل الطبي -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">العمل الطبي</div>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="clinical-visits" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">stethoscope</span>
<span class="sidebar-label truncate">الزيارات</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="prescriptions" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">prescriptions</span>
<span class="sidebar-label truncate">الوصفات الطبية</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="lab-orders" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">science</span>
<span class="sidebar-label truncate">طلبات المختبر</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="radiology-orders" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">radiology</span>
<span class="sidebar-label truncate">طلبات الأشعة</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="diagnostic-results" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">assignment_turned_in</span>
<span class="sidebar-label truncate">النتائج</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="referrals-and-follow-ups" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">sync_alt</span>
<span class="sidebar-label truncate">الإحالات والمتابعات</span>
</a>
</nav>
<!-- Group 3: الإدارة -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">الإدارة</div>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="services-and-pricing" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">receipt_long</span>
<span class="sidebar-label truncate">الخدمات والأسعار</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="shifts-and-schedule" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">schedule</span>
<span class="sidebar-label truncate">الدوام والجدول</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="billing-and-claims" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">account_balance_wallet</span>
<span class="sidebar-label truncate">التحصيل والمستحقات</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" data-path="settings" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">settings</span>
<span class="sidebar-label truncate">الإعدادات</span>
</a>
</nav>
</div>
<!-- Doctor Profile Footer Card -->
<div class="sidebar-doctor-card p-3 border-t border-[#223456] bg-[#132038] relative shrink-0">
<div class="flex items-center justify-between gap-2">
<div class="flex items-center gap-3 min-w-0">
<div class="relative shrink-0">
<div aria-label="د. محمد أحمد" class="w-9 h-9 rounded-full bg-[#203254] text-white flex items-center justify-center font-bold text-[11px] ring-2 ring-[#0495F6]/40" title="د. محمد أحمد">م أ</div>
<span class="absolute bottom-0 left-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-[#132038]"></span>
</div>
<div class="min-w-0 sidebar-label">
<div class="text-[13px] font-semibold text-white truncate">د. محمد أحمد</div>
<div class="text-[11px] text-gray-400 truncate flex items-center gap-1.5">
<span class="">استشاري طب باطني</span>
<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
<span class="text-emerald-400 font-medium">متاح الآن</span>
</div>
</div>
</div>
<button class="sidebar-label p-1 text-gray-400 hover:text-white rounded hover:bg-[#203254] transition-colors shrink-0" id="doctor-menu-btn" onclick="toggleDoctorMenu(event)" title="خيارات الحساب" type="button">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</div>
<!-- Doctor Dropdown Menu -->
<div class="hidden absolute bottom-16 right-3 left-3 bg-[#1A2C4B] rounded-xl shadow-xl border border-[#2B4066] p-1.5 flex flex-col gap-1 z-50" id="doctor-account-menu">
<a class="flex items-center gap-2 px-3 py-2 text-gray-200 hover:bg-[#243B63] hover:text-white rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] text-gray-400">badge</span>
<span class="">الملف الشخصي</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-gray-200 hover:bg-[#243B63] hover:text-white rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] text-gray-400">settings</span>
<span class="">إعدادات الحساب</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-gray-200 hover:bg-[#243B63] hover:text-white rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] text-emerald-400">toggle_on</span>
<span class="">تغيير حالة التوفر</span>
</a>
<div class="h-px bg-[#2B4066] my-1"></div>
<a class="flex items-center gap-2 px-3 py-2 text-rose-400 hover:bg-rose-500/10 rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px]">logout</span>
<span class="">تسجيل الخروج</span>
</a>
</div>
</div>
<style id="sidebar-collapse-rules">
.sidebar-scroll {
  min-height: 0;
  overflow-y: auto;
  overflow-x: hidden;
  overscroll-behavior: contain;
  scrollbar-width: thin;
  scrollbar-color: #415775 transparent;
}
.sidebar-scroll::-webkit-scrollbar {
  width: 6px;
}
.sidebar-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
  background: #415775;
  border-radius: 999px;
}
.sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: #5B7190;
}
#app-sidebar.sidebar-collapsed {
  width: 72px !important;
}
#app-sidebar.sidebar-collapsed .sidebar-label {
  display: none !important;
}
#app-sidebar.sidebar-hovered {
  width: 268px !important;
}
#app-sidebar.sidebar-hovered .sidebar-label {
  display: inline !important;
}
</style></aside>
<!-- END: Sidebar -->
<!-- BEGIN: Main Content Layout -->
<main class="transition-all duration-300 mr-[268px] pt-16 px-6 lg:px-8 pb-12" id="main-content-layout">
<!-- PAGE HEADER -->
<div class="flex flex-row justify-between items-center mb-5" data-purpose="page-header">
<div>
<h1 class="text-2xl font-bold text-textMain tracking-tight">ملف المريض</h1>
<p class="text-sm text-textSecondary mt-0.5">مراجعة بيانات المريض والسجل الطبي المتاح</p>
</div>
<div>
<a class="border border-borderLight bg-white hover:bg-softSurface text-textMain text-sm font-semibold px-3.5 py-2 rounded-lg flex items-center gap-1.5 shadow-sm transition-colors" href="#">
<span class="material-symbols-outlined text-lg">arrow_forward</span>
<span class="">العودة للمرضى</span>
</a>
</div>
</div>
<!-- PATIENT IDENTITY HEADER CARD -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4" data-purpose="patient-identity-card">
<!-- Right Side: Patient Identification Details -->
<div class="flex items-center gap-4">
<!-- Initials Avatar -->
<div class="w-12 h-12 rounded-full bg-blue-100 text-primary font-bold text-base flex items-center justify-center shrink-0">
          أ م
        </div>
<div>
<h2 class="text-lg font-bold text-textMain">أحمد محمود</h2>
<div class="flex flex-wrap items-center gap-2 text-xs text-textSecondary mt-1">
<span class="font-semibold text-primary bg-blue-50 px-2 py-0.5 rounded dir-ltr font-inter">P-10482</span>
<span class="">•</span>
<span class="">42 سنة</span>
<span class="">•</span>
<span class="">ذكر</span>
<span class="">•</span>
<span class="dir-ltr font-mono text-slate-700">0599 123 456</span>
<span class="">•</span>
<button type="button" id="access-details-btn" class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full text-[11px] font-semibold hover:bg-emerald-100 transition-colors">
<span class="material-symbols-outlined text-[14px]">verified_user</span>
<span>وصول متاح</span>
<span class="material-symbols-outlined text-[13px]">info</span>
</button>
</div>
</div>
</div>
<!-- Left Side: Contextual Actions -->
<div class="flex items-center gap-2">
<button class="bg-primary hover:bg-primary-hover active:bg-primary-active text-white px-3.5 py-2 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-sm transition-colors">
<span class="material-symbols-outlined text-lg">stethoscope</span>
<span class="">فتح زيارة</span>
</button>
<button class="border border-borderLight bg-white hover:bg-softSurface text-textMain px-3.5 py-2 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-sm transition-colors">
<span class="material-symbols-outlined text-lg text-textSecondary">calendar_today</span>
<span class="">موعد جديد</span>
</button>
<!-- More Dropdown -->
<div class="relative" id="patient-actions-more-container">
<button class="border border-borderLight bg-white hover:bg-softSurface text-textMain px-3 py-2 rounded-lg text-sm font-semibold flex items-center gap-1 shadow-sm transition-colors" id="patient-actions-more-btn">
<span class="">المزيد</span>
<span class="material-symbols-outlined text-lg">expand_more</span>
</button>
<div class="hidden absolute left-0 top-full mt-1.5 w-44 bg-white rounded-xl shadow-lg border border-borderLight p-1.5 z-40 animate-fade-in" id="patient-more-menu">
<a class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-textMain hover:bg-softSurface rounded-lg transition-colors" href="#">
<span class="material-symbols-outlined text-base text-textSecondary">description</span>
<span class="">وصفة طبية</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-textMain hover:bg-softSurface rounded-lg transition-colors" href="#">
<span class="material-symbols-outlined text-base text-textSecondary">science</span>
<span class="">طلب مختبر</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-textMain hover:bg-softSurface rounded-lg transition-colors" href="#">
<span class="material-symbols-outlined text-base text-textSecondary">radiology</span>
<span class="">طلب أشعة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-textMain hover:bg-softSurface rounded-lg transition-colors" href="#">
<span class="material-symbols-outlined text-base text-textSecondary">sync_alt</span>
<span class="">إحالة / متابعة</span>
</a>
</div>
</div>
</div>
</div>
<!-- ACCESS & PRIVACY CONTEXT -->
<div class="mb-5 rounded-xl border border-blue-200 bg-blue-50/70 px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3" id="access-context-banner">
  <div class="flex items-start gap-2.5">
    <span class="material-symbols-outlined text-primary text-[20px] mt-0.5">shield_person</span>
    <div>
      <div class="text-[13px] font-bold text-[#0B1020]">الوصول الحالي للسجل الطبي</div>
      <p class="text-[12px] text-[#475569] mt-0.5 leading-5">يمكنك الاطلاع على المعلومات المصرح بها ضمن سياق الرعاية الحالي ووفق إعدادات خصوصية المريض. بعض البيانات قد لا تكون متاحة.</p>
    </div>
  </div>
  <button type="button" id="access-context-more-btn" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-primary text-[12px] font-semibold hover:bg-blue-50 transition-colors">
    <span>تفاصيل الوصول</span>
    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
  </button>
</div>
<!-- MAIN PATIENT TABS -->
<nav class="border-b border-borderLight mb-6 flex items-center gap-1 overflow-x-auto no-scrollbar" data-purpose="patient-nav-tabs">
<button class="patient-tab-btn border-b-2 border-primary text-primary font-semibold pb-3 px-3 text-sm flex items-center gap-1.5 whitespace-nowrap cursor-pointer transition-colors" data-tab="summary">
<span class="">الملخص</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="timeline">
<span>الخط الزمني</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="visits">
<span class="">الزيارات</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="procedures">
<span>الإجراءات الطبية</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="diagnoses">
<span class="">التشخيصات</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="medications">
<span class="">الأدوية الحالية</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="allergies">
<span class="">الحساسية</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="prescriptions">
<span class="">الوصفات</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="lab-results">
<span class="">نتائج المختبر</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="imaging">
<span class="">تقارير الأشعة</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="referrals">
<span class="">الإحالات</span>
</button>
<button class="patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors" data-tab="followups">
<span class="">المتابعات</span>
</button>
</nav>
<!-- TAB PANELS CONTAINER -->
<div data-purpose="tab-panels">
<!-- Panel 1: الملخص -->
<section class="patient-tab-panel grid grid-cols-1 lg:grid-cols-12 gap-5" id="tab-panel-summary">
<!-- Main 8-column column -->
<div class="lg:col-span-8 space-y-4">
<!-- Card: آخر زيارة -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="flex justify-between items-center pb-3 border-b border-borderLight mb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">history_edu</span>
<h3 class="font-bold text-sm text-textMain">آخر زيارة</h3>
</div>
<a class="text-xs text-primary hover:text-primary-hover font-semibold" href="#">عرض الزيارة</a>
</div>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm">
<div>
<div class="font-semibold text-textMain">18 أكتوبر 2024 • كشفية طب باطني</div>
<div class="text-xs text-textSecondary mt-0.5">الطبيب المعالج: د. محمد أحمد</div>
</div>
<span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md font-medium self-start sm:self-center">
<span class="material-symbols-outlined text-sm">check_circle</span>
<span class="">تمت الزيارة وإغلاقها</span>
</span>
</div>
</div>
<!-- Card: التشخيصات -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="flex justify-between items-center mb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">clinical_notes</span>
<h3 class="font-bold text-sm text-textMain">التشخيصات</h3>
</div>
</div>
<div class="py-6 text-center text-sm text-textSecondary bg-softSurface rounded-lg border border-dashed border-slate-300">
<span class="material-symbols-outlined text-2xl text-textMuted block mb-1">clinical_notes</span>
<span class="">لا توجد تشخيصات ظاهرة ضمن السجل المتاح</span>
</div>
</div>
<!-- Card: الأدوية الحالية -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="flex justify-between items-center mb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">medication</span>
<h3 class="font-bold text-sm text-textMain">الأدوية الحالية</h3>
</div>
</div>
<div class="py-6 text-center text-sm text-textSecondary bg-softSurface rounded-lg border border-dashed border-slate-300">
<span class="material-symbols-outlined text-2xl text-textMuted block mb-1">medication</span>
<span class="">لا توجد أدوية حالية ظاهرة ضمن السجل المتاح</span>
</div>
</div>
<!-- Card: الحساسية -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="flex justify-between items-center mb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-amber-500 text-xl">warning_amber</span>
<h3 class="font-bold text-sm text-textMain">الحساسية</h3>
</div>
</div>
<div class="py-6 text-center text-sm text-textSecondary bg-softSurface rounded-lg border border-dashed border-slate-300">
<span class="material-symbols-outlined text-2xl text-textMuted block mb-1">warning_amber</span>
<span class="">لا توجد معلومات حساسية ظاهرة ضمن السجل المتاح</span>
</div>
</div>
</div>
<!-- Side 4-column column -->
<div class="lg:col-span-4 space-y-4">
<!-- Card: الموعد القادم -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="flex justify-between items-center pb-3 border-b border-borderLight mb-3">
<h3 class="font-bold text-sm text-textMain">الموعد القادم</h3>
<a class="text-xs text-primary hover:text-primary-hover font-semibold" href="#">عرض الموعد</a>
</div>
<div class="space-y-3">
<div class="text-sm font-bold text-textMain flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-primary">calendar_today</span>
<span class="">اليوم، 10:30 ص</span>
</div>
<div class="text-xs text-textSecondary space-y-1">
<div class=""><span class="font-medium text-slate-700">الخدمة:</span> كشفية طب باطني</div>
<div class=""><span class="font-medium text-slate-700">الطبيب:</span> د. محمد أحمد</div>
</div>
<!-- Status chips -->
<div class="flex flex-wrap gap-2 pt-2 border-t border-borderLight">
<span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-primary font-medium border border-blue-100 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                  موعد: مؤكد
                </span>
<span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium border border-emerald-100 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  الطابور: جاهز للطبيب
                </span>
</div>
</div>
</div>
<!-- Card: نشاط حديث -->
<div class="bg-white rounded-xl border border-borderLight p-5 shadow-sm">
<div class="pb-3 border-b border-borderLight mb-3">
<h3 class="font-bold text-sm text-textMain">نشاط حديث</h3>
</div>
<div class="relative pl-2 pr-3 space-y-4">
<!-- Timeline Item 1 -->
<div class="relative flex items-start gap-3">
<span class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0 ring-4 ring-blue-50"></span>
<div class="text-xs">
<div class="font-semibold text-textMain">18 أكتوبر 2024</div>
<div class="text-textSecondary mt-0.5">زيارة مكتملة (كشفية طب باطني)</div>
</div>
</div>
<!-- Timeline Item 2 -->
<div class="relative flex items-start gap-3">
<span class="w-2 h-2 rounded-full bg-slate-300 mt-1.5 shrink-0"></span>
<div class="text-xs">
<div class="font-semibold text-textMain">12 أكتوبر 2024</div>
<div class="text-textSecondary mt-0.5">وصفة طبية (صادرة عن العيادة)</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Panel: الخط الزمني -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight shadow-sm overflow-hidden" id="tab-panel-timeline">
  <div class="p-4 border-b border-borderLight flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h3 class="font-bold text-sm text-textMain">الخط الزمني الطبي</h3>
      <p class="text-[12px] text-textSecondary mt-0.5">عرض موحد للأحداث الطبية المسموح لك بالوصول إليها، بدون إنشاء نسخ مكررة من السجلات.</p>
    </div>
    <div class="flex items-center gap-2">
      <select id="timeline-filter" class="h-9 px-3 rounded-lg border border-borderLight bg-white text-[12px] text-textMain focus:outline-none focus:border-primary">
        <option value="all">كل الأحداث</option>
        <option value="visit">الزيارات</option>
        <option value="prescription">الوصفات</option>
        <option value="result">النتائج</option>
        <option value="procedure">الإجراءات</option>
        <option value="referral">الإحالات والمتابعات</option>
      </select>
    </div>
  </div>
  <div class="p-5">
    <div class="relative pr-7 space-y-5 before:absolute before:right-[9px] before:top-2 before:bottom-2 before:w-px before:bg-[#DCE6F2]" id="timeline-list">
      <div class="timeline-item relative" data-type="visit">
        <span class="absolute -right-7 top-1.5 w-[18px] h-[18px] rounded-full bg-blue-50 border-2 border-primary flex items-center justify-center"></span>
        <div class="border border-borderLight rounded-xl p-4 hover:border-primary/40 transition-colors">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[18px]">stethoscope</span><span class="font-bold text-[13px]">زيارة طبية مكتملة</span></div>
              <p class="text-[12px] text-textSecondary mt-1">18 أكتوبر 2024 • د. محمد أحمد • كشفية طب باطني</p>
            </div>
            <button class="timeline-source-btn text-[12px] font-semibold text-primary border border-primary/20 px-2.5 py-1.5 rounded-lg hover:bg-blue-50" data-title="الزيارة الطبية" data-detail="سجل الزيارة الأصلي المرتبط بهذا الحدث. فتحه يعرض نفس سجل الزيارة، وليس نسخة منفصلة.">عرض المصدر</button>
          </div>
        </div>
      </div>
      <div class="timeline-item relative" data-type="prescription">
        <span class="absolute -right-7 top-1.5 w-[18px] h-[18px] rounded-full bg-violet-50 border-2 border-violet-500"></span>
        <div class="border border-borderLight rounded-xl p-4 hover:border-violet-300 transition-colors">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-violet-600 text-[18px]">prescriptions</span><span class="font-bold text-[13px]">وصفة طبية</span></div>
              <p class="text-[12px] text-textSecondary mt-1">18 أكتوبر 2024 • مرتبطة بزيارة كشفية الباطني</p>
            </div>
            <button class="timeline-source-btn text-[12px] font-semibold text-primary border border-primary/20 px-2.5 py-1.5 rounded-lg hover:bg-blue-50" data-title="الوصفة الطبية" data-detail="هذه هي الوصفة الأساسية المرتبطة بالزيارة، ويمكن الوصول إليها أيضًا من تبويب الوصفات.">عرض المصدر</button>
          </div>
        </div>
      </div>
      <div class="timeline-item relative" data-type="procedure">
        <span class="absolute -right-7 top-1.5 w-[18px] h-[18px] rounded-full bg-emerald-50 border-2 border-emerald-500"></span>
        <div class="border border-borderLight rounded-xl p-4 hover:border-emerald-300 transition-colors">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600 text-[18px]">medical_services</span><span class="font-bold text-[13px]">إجراء طبي مسجل</span></div>
              <p class="text-[12px] text-textSecondary mt-1">12 سبتمبر 2024 • الحالة: مكتمل • تفاصيل العرض ضمن صلاحيات الوصول الحالية</p>
            </div>
            <button class="timeline-source-btn text-[12px] font-semibold text-primary border border-primary/20 px-2.5 py-1.5 rounded-lg hover:bg-blue-50" data-title="الإجراء الطبي" data-detail="سجل إجراء طبي تجريبي ضمن النموذج. في الإنتاج يرتبط بالسجل الأصلي للإجراء والزيارة والمنشأة.">عرض المصدر</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Panel: الإجراءات الطبية -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight shadow-sm overflow-hidden" id="tab-panel-procedures">
  <div class="p-4 border-b border-borderLight flex items-center justify-between">
    <div>
      <h3 class="font-bold text-sm text-textMain">الإجراءات الطبية</h3>
      <p class="text-[12px] text-textSecondary mt-0.5">الإجراءات والعمليات المرتبطة بالسجل الطبي المسموح عرضه.</p>
    </div>
    <span class="text-[11px] text-textSecondary bg-softSurface px-2.5 py-1 rounded-full">1 سجل ظاهر</span>
  </div>
  <div class="p-4">
    <div class="border border-borderLight rounded-xl p-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined">medical_services</span></div>
          <div>
            <div class="font-bold text-[13px] text-textMain">إجراء طبي مسجل</div>
            <div class="text-[12px] text-textSecondary mt-1">12 سبتمبر 2024 • مقدم الخدمة: جهة علاجية ضمن السجل المتاح</div>
            <div class="flex flex-wrap gap-2 mt-2">
              <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">مكتمل</span>
              <span class="text-[11px] text-[#475569] bg-[#F1F5F9] px-2 py-0.5 rounded">مرتبط بزيارة</span>
            </div>
          </div>
        </div>
        <button type="button" id="procedure-details-btn" class="text-[12px] font-semibold text-primary border border-borderLight bg-white hover:bg-softSurface px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5">
          <span>عرض التفاصيل</span><span class="material-symbols-outlined text-[16px]">open_in_new</span>
        </button>
      </div>
    </div>
  </div>
</section>
<!-- Panel 2: الزيارات -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight overflow-hidden shadow-sm" id="tab-panel-visits">
<div class="p-4 border-b border-borderLight flex justify-between items-center">
<h3 class="font-bold text-sm text-textMain">سجل الزيارات</h3>
<span class="text-xs text-textSecondary">عدد الزيارات المسجلة: 1</span>
</div>
<div class="overflow-x-auto">
<table class="w-full text-right text-sm">
<thead class="bg-softSurface text-textSecondary text-xs uppercase border-b border-borderLight">
<tr>
<th class="py-3 px-4 font-semibold">التاريخ</th>
<th class="py-3 px-4 font-semibold">الطبيب</th>
<th class="py-3 px-4 font-semibold">الخدمة</th>
<th class="py-3 px-4 font-semibold">الحالة</th>
<th class="py-3 px-4 font-semibold text-left">الإجراء</th>
</tr>
</thead>
<tbody class="divide-y border-borderLight text-xs">
<tr class="hover:bg-softSurface transition-colors">
<td class="py-3.5 px-4 font-medium text-textMain">18 أكتوبر 2024</td>
<td class="py-3.5 px-4 text-textSecondary">د. محمد أحمد</td>
<td class="py-3.5 px-4 text-textSecondary">كشفية طب باطني</td>
<td class="py-3.5 px-4">
<span class="text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded text-[11px] font-semibold">مكتملة</span>
</td>
<td class="py-3.5 px-4 text-left">
<button class="text-xs text-primary hover:text-primary-hover font-semibold">عرض الزيارة</button>
</td>
</tr>
</tbody>
</table>
</div>
</section>
<!-- Panel 3: التشخيصات -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-diagnoses">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">clinical_notes</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد تشخيصات ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد تشخيصات ظاهرة ضمن السجل المتاح حالياً لهذا المريض.</p>
</div>
</section>
<!-- Panel 4: الأدوية الحالية -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-medications">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">medication</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد أدوية حالية ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد أدوية حالية ظاهرة ضمن السجل المتاح للمريض.</p>
</div>
</section>
<!-- Panel 5: الحساسية -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-allergies">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">warning_amber</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد معلومات حساسية ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد معلومات حساسية ظاهرة ضمن السجل المتاح.</p>
</div>
</section>
<!-- Panel 6: الوصفات -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-5 shadow-sm space-y-3" id="tab-panel-prescriptions">
<div class="flex items-center justify-between border-b border-borderLight pb-3">
<h3 class="font-bold text-sm text-textMain">الوصفات الطبية</h3>
<span class="text-xs text-textSecondary">وصفة واحدة</span>
</div>
<div class="border border-borderLight rounded-lg p-4 flex items-center justify-between hover:border-primary/50 transition-colors">
<div class="flex items-center gap-3">
<div class="p-2.5 rounded-lg bg-blue-50 text-primary">
<span class="material-symbols-outlined text-xl">description</span>
</div>
<div>
<div class="text-sm font-semibold text-textMain">وصفة طبية - كشفية باطني</div>
<div class="text-xs text-textSecondary mt-0.5">18 أكتوبر 2024 • الطبيب: د. محمد أحمد</div>
</div>
</div>
<button class="text-xs font-semibold text-primary hover:text-primary-hover border border-primary/20 bg-blue-50/50 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
            عرض الوصفة
          </button>
</div>
</section>
<!-- Panel 7: نتائج المختبر -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-lab-results">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">science</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد نتائج مختبر ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد نتائج مختبر ظاهرة ضمن السجل المتاح.</p>
</div>
</section>
<!-- Panel 8: تقارير الأشعة -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-imaging">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">radiology</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد تقارير أشعة ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد تقارير أشعة ظاهرة ضمن السجل المتاح.</p>
</div>
</section>
<!-- Panel 9: الإحالات -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-referrals">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">sync_alt</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد إحالات ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد إحالات ظاهرة ضمن السجل المتاح.</p>
</div>
</section>
<!-- Panel 10: المتابعات -->
<section class="patient-tab-panel hidden bg-white rounded-xl border border-borderLight p-8 text-center shadow-sm" id="tab-panel-followups">
<div class="max-w-md mx-auto">
<div class="w-12 h-12 rounded-full bg-softSurface flex items-center justify-center mx-auto mb-3 text-textMuted">
<span class="material-symbols-outlined text-2xl">event_repeat</span>
</div>
<h4 class="text-sm font-bold text-textMain mb-1">لا توجد متابعات ظاهرة ضمن السجل المتاح</h4>
<p class="text-xs text-textSecondary">لا توجد متابعات ظاهرة ضمن السجل المتاح.</p>
</div>
</section>
</div>
</main>
<!-- END: Main Content Layout -->
<!-- Scripts -->
<script>
// Sidebar State Management
let isPinned = true;
const sidebar = document.getElementById('app-sidebar');
const mainLayout = document.getElementById('main-content-layout');
const topHeader = document.getElementById('top-header');
const pinBtn = document.getElementById('sidebar-pin-toggle');
const pinIcon = document.getElementById('sidebar-pin-icon');

function updateSidebarState() {
  if (!sidebar || !mainLayout || !topHeader) return;
  sidebar.classList.remove('sidebar-hovered');
  if (isPinned) {
    sidebar.classList.remove('sidebar-collapsed');
    mainLayout.style.marginRight = '268px';
    topHeader.style.right = '268px';
    if (pinBtn) {
      pinBtn.classList.add('text-[#0495F6]', 'bg-white/10');
      pinBtn.classList.remove('text-[#AFC3D9]');
      pinBtn.title = 'طي القائمة الجانبية';
    }
    if (pinIcon) {
      pinIcon.classList.add('text-[#0495F6]');
      pinIcon.classList.remove('text-[#AFC3D9]');
      pinIcon.textContent = 'push_pin';
    }
  } else {
    sidebar.classList.add('sidebar-collapsed');
    mainLayout.style.marginRight = '72px';
    topHeader.style.right = '72px';
    if (pinBtn) {
      pinBtn.classList.remove('text-[#0495F6]', 'bg-white/10');
      pinBtn.classList.add('text-[#AFC3D9]');
      pinBtn.title = 'تثبيت القائمة الجانبية';
    }
    if (pinIcon) {
      pinIcon.classList.remove('text-[#0495F6]');
      pinIcon.classList.add('text-[#AFC3D9]');
      pinIcon.textContent = 'push_pin';
    }
  }
}

function toggleSidebarPin() {
  isPinned = !isPinned;
  localStorage.setItem('thera_sidebar_pinned', isPinned ? 'true' : 'false');
  updateSidebarState();
}

if (sidebar) {
  sidebar.addEventListener('mouseenter', () => {
    if (!isPinned) {
      sidebar.classList.add('sidebar-hovered');
    }
  });
  sidebar.addEventListener('mouseleave', () => {
    if (!isPinned) {
      sidebar.classList.remove('sidebar-hovered');
      const menu = document.getElementById('doctor-account-menu');
      if (menu) {
        menu.classList.add('hidden');
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const savedPin = localStorage.getItem('thera_sidebar_pinned');
  isPinned = savedPin !== null ? savedPin === 'true' : true;
  updateSidebarState();
});

// Quick Add Menu Management
let quickAddTimer = null;
window.handleQuickAddEnter = function() {
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (menu) {
    menu.classList.remove('hidden');
  }
  if (arrow) {
    arrow.textContent = 'expand_less';
  }
};

window.handleQuickAddLeave = function() {
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
  }
  quickAddTimer = setTimeout(() => {
    window.closeQuickAddMenu();
    quickAddTimer = null;
  }, 200);
};

window.toggleQuickAddMenu = function(e) {
  if (e) {
    e.stopPropagation();
  }
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (!menu) return;
  const isHidden = menu.classList.toggle('hidden');
  if (arrow) {
    arrow.textContent = isHidden ? 'expand_more' : 'expand_less';
  }
};

window.closeQuickAddMenu = function() {
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (menu) {
    menu.classList.add('hidden');
  }
  if (arrow) {
    arrow.textContent = 'expand_more';
  }
};

// Doctor Account Menu
function toggleDoctorMenu(e) {
  if (e) e.stopPropagation();
  const menu = document.getElementById('doctor-account-menu');
  if (menu) {
    menu.classList.toggle('hidden');
  }
}

// Patient Contextual Actions "المزيد" Menu
const patientMoreBtn = document.getElementById('patient-actions-more-btn');
const patientMoreMenu = document.getElementById('patient-more-menu');
if (patientMoreBtn && patientMoreMenu) {
  patientMoreBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    patientMoreMenu.classList.toggle('hidden');
  });
}

// Unified Outside Click Listener
document.addEventListener('click', function(e) {
  const quickAddContainer = document.getElementById('quick-add-container');
  if (quickAddContainer && !quickAddContainer.contains(e.target)) {
    window.closeQuickAddMenu();
  }
  const doctorMenu = document.getElementById('doctor-account-menu');
  const doctorCard = document.querySelector('.sidebar-doctor-card');
  if (doctorMenu && doctorCard && !doctorMenu.contains(e.target) && !doctorCard.contains(e.target)) {
    doctorMenu.classList.add('hidden');
  }
  const patientMoreContainer = document.getElementById('patient-actions-more-container');
  const patientMoreMenu = document.getElementById('patient-more-menu');
  if (patientMoreContainer && patientMoreMenu && !patientMoreContainer.contains(e.target)) {
    patientMoreMenu.classList.add('hidden');
  }
});

// Switching patient profile navigation tabs
const tabButtons = document.querySelectorAll('.patient-tab-btn');
const tabPanels = document.querySelectorAll('.patient-tab-panel');

const panelMap = {
  'summary': 'tab-panel-summary',
  'visits': 'tab-panel-visits',
  'diagnoses': 'tab-panel-diagnoses',
  'medications': 'tab-panel-medications',
  'allergies': 'tab-panel-allergies',
  'prescriptions': 'tab-panel-prescriptions',
  'lab-results': 'tab-panel-lab-results',
  'imaging': 'tab-panel-imaging',
  'referrals': 'tab-panel-referrals',
  'followups': 'tab-panel-followups'
};

tabButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.getAttribute('data-tab');

    // Reset all buttons style
    tabButtons.forEach(b => {
      b.className = 'patient-tab-btn border-b-2 border-transparent text-textSecondary hover:text-textMain pb-3 px-3 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors';
    });

    // Set active button style
    btn.className = 'patient-tab-btn border-b-2 border-primary text-primary font-semibold pb-3 px-3 text-sm flex items-center gap-1.5 whitespace-nowrap cursor-pointer transition-colors';

    // Hide all panels
    tabPanels.forEach(panel => {
      panel.classList.add('hidden');
    });

    // Show target panel
    const activePanelId = panelMap[target];
    const activePanel = document.getElementById(activePanelId);
    if (activePanel) {
      activePanel.classList.remove('hidden');
    }
  });
});
</script>



<!-- GENERIC DETAILS MODAL -->
<div id="patient-detail-modal" class="hidden fixed inset-0 z-[100] items-center justify-center p-4">
  <div class="absolute inset-0 bg-[#0B1020]/45" data-close-patient-modal></div>
  <div class="relative w-full max-w-lg bg-white rounded-2xl border border-[#E2E8F0] shadow-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center justify-between">
      <div>
        <h3 id="patient-modal-title" class="font-bold text-[16px] text-[#0B1020]">تفاصيل</h3>
        <p id="patient-modal-subtitle" class="text-[12px] text-[#64748B] mt-0.5"></p>
      </div>
      <button type="button" data-close-patient-modal class="w-8 h-8 rounded-lg hover:bg-[#F1F5F9] flex items-center justify-center text-[#64748B]"><span class="material-symbols-outlined text-[20px]">close</span></button>
    </div>
    <div id="patient-modal-body" class="p-5 text-[13px] text-[#334155] leading-6"></div>
    <div class="px-5 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] flex justify-end">
      <button type="button" data-close-patient-modal class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white text-[13px] font-semibold hover:bg-[#F1F5F9]">إغلاق</button>
    </div>
  </div>
</div>

<script>
(function(){
  const modal=document.getElementById('patient-detail-modal');
  const title=document.getElementById('patient-modal-title');
  const subtitle=document.getElementById('patient-modal-subtitle');
  const body=document.getElementById('patient-modal-body');
  function openModal(t,s,html){
    title.textContent=t; subtitle.textContent=s||''; body.innerHTML=html;
    modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow='hidden';
  }
  function closeModal(){modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow='';}
  document.querySelectorAll('[data-close-patient-modal]').forEach(el=>el.addEventListener('click',closeModal));
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});

  const accessHtml=`
    <div class="space-y-3">
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
        <div class="font-bold text-emerald-800">وصول متاح — سياق رعاية حالي</div>
        <div class="text-[12px] text-emerald-700 mt-1">يتم تطبيق صلاحيات الدور + سياق الرعاية + موافقات المريض + إعدادات الخصوصية.</div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <div class="border border-[#E2E8F0] rounded-lg p-3"><div class="text-[11px] text-[#64748B]">السياق الحالي</div><div class="font-semibold mt-1">رعاية نشطة / موعد قائم</div></div>
        <div class="border border-[#E2E8F0] rounded-lg p-3"><div class="text-[11px] text-[#64748B]">نطاق العرض</div><div class="font-semibold mt-1">المعلومات المصرح بها فقط</div></div>
      </div>
      <div class="rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] p-3 text-[12px]">بعد انتهاء سياق الرعاية الواسع، لا يستمر الوصول الكامل تلقائيًا. يبقى للطبيب الوصول إلى السجلات المرتبطة بعلاقته العلاجية مثل زياراته ووصفاته وطلباته ونتائجها المرتبطة، حسب الصلاحيات والخصوصية.</div>
      <div class="text-[11px] text-[#64748B]">الوصول إلى البيانات الحساسة وأي محاولة وصول مرفوضة يتم تسجيلها في سجل التدقيق.</div>
    </div>`;
  ['access-details-btn','access-context-more-btn'].forEach(id=>{const el=document.getElementById(id); if(el) el.addEventListener('click',()=>openModal('تفاصيل الوصول والخصوصية','سياق الوصول الحالي للمريض',accessHtml));});

  const proc=document.getElementById('procedure-details-btn');
  if(proc) proc.addEventListener('click',()=>openModal('تفاصيل الإجراء الطبي','سجل تجريبي لعرض تجربة المستخدم',`
    <div class="space-y-3">
      <div class="grid grid-cols-2 gap-3">
        <div><div class="text-[11px] text-[#64748B]">التاريخ</div><div class="font-semibold">12 سبتمبر 2024</div></div>
        <div><div class="text-[11px] text-[#64748B]">الحالة</div><div class="font-semibold text-emerald-700">مكتمل</div></div>
        <div><div class="text-[11px] text-[#64748B]">مقدم الخدمة</div><div class="font-semibold">جهة علاجية ضمن السجل المتاح</div></div>
        <div><div class="text-[11px] text-[#64748B]">الزيارة المرتبطة</div><div class="font-semibold">زيارة مرتبطة بالسجل</div></div>
      </div>
      <div class="border-t border-[#E2E8F0] pt-3"><div class="text-[11px] text-[#64748B]">الملخص</div><div class="mt-1">تفاصيل الإجراء تظهر فقط إذا كانت ضمن نطاق الوصول الحالي. المرفقات والسجلات المصدرية تظل مرتبطة بالسجل الأصلي.</div></div>
    </div>`));

  document.querySelectorAll('.timeline-source-btn').forEach(btn=>btn.addEventListener('click',()=>openModal(btn.dataset.title||'تفاصيل السجل','السجل الأصلي المرتبط بالخط الزمني',`<div>${btn.dataset.detail||''}</div><div class="mt-3 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] p-3 text-[12px]">الخط الزمني لا ينشئ نسخة جديدة؛ فتح المصدر ينتقل للسجل الأساسي نفسه.</div>`)));

  const filter=document.getElementById('timeline-filter');
  if(filter) filter.addEventListener('change',()=>{document.querySelectorAll('.timeline-item').forEach(item=>{item.style.display=(filter.value==='all'||item.dataset.type===filter.value)?'':'none';});});
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>