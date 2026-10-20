<!DOCTYPE html>

<html dir="rtl" lang="ar"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الزيارة الطبية - THERA</title>
<!-- Google Fonts: Tajawal (Arabic) & Inter (Numbers/Latin) -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<!-- Google Material Symbols Outlined -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<!-- Tailwind CSS v3 with Plugins -->


@include('doctor.partials.assets')
<style data-purpose="custom-utilities">
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #F6F9FC;
      color: #0B1020;
      -webkit-font-smoothing: antialiased;
    }
    .font-num {
      font-family: 'Inter', sans-serif;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
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
      box-shadow: -4px 0 24px rgba(0, 0, 0, 0.25);
    }
    #app-sidebar.sidebar-hovered .sidebar-label {
      display: inline !important;
    }
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-header {
      justify-content: center !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-wrapper {
      width: 100%;
      justify-content: center !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-pin-btn {
      display: none !important;
    }
    #toast-notification {
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }
    #toast-notification.toast-hidden {
      transform: translateY(100px);
      opacity: 0;
      pointer-events: none;
    }
    #toast-notification.toast-visible {
      transform: translateY(0);
      opacity: 1;
      pointer-events: auto;
    }
    #action-drawer {
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    #action-drawer.drawer-closed {
      transform: translateX(-100%);
    }
    #action-drawer.drawer-open {
      transform: translateX(0);
    }
  </style>

</head>
<body class="min-h-screen bg-[#F6F9FC] text-[#0B1020] antialiased">
<!-- BEGIN: AppSidebar (Doctor App Shell) -->
<aside class="fixed top-0 right-0 h-screen z-50 bg-[#16243E] text-white flex flex-col transition-all duration-300 shadow-[0_4px_20px_rgba(11,16,32,0.15)] w-[268px] select-none" data-purpose="main-navigation-sidebar" id="app-sidebar">
<!-- Sidebar Brand Header -->
<div class="h-16 px-4 flex items-center justify-between border-b border-[#223456] bg-[#132038] shrink-0 sidebar-header">
<div class="flex items-center gap-3 overflow-hidden select-none sidebar-brand-wrapper">
<img alt="THERA Logo" class="h-9 max-w-[130px] object-contain shrink-0" src="{{ asset('images/doctor/thera-logo.png') }}"/>
</div>
<button class="p-1.5 text-[#0495F6] bg-white/10 rounded-lg hover:bg-white/20 transition-colors sidebar-pin-btn" id="sidebar-pin-toggle" onclick="toggleSidebarPin()" title="طي القائمة الجانبية" type="button">
<span class="material-symbols-outlined text-[20px]" id="sidebar-pin-icon">push_pin</span>
</button>
</div>
<!-- Middle Scrollable Navigation -->
<div class="sidebar-scroll flex-1 min-h-0 overflow-y-auto py-3 px-3 flex flex-col gap-6">
<!-- Group 1: العمل اليومي -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">العمل اليومي</div>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">grid_view</span>
<span class="sidebar-label truncate">الرئيسية</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">calendar_today</span>
<span class="sidebar-label truncate">المواعيد</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">person_search</span>
<span class="sidebar-label truncate">المرضى</span>
</a>
</nav>
<!-- Group 2: العمل الطبي -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">العمل الطبي</div>
<a aria-current="page" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all bg-[#0495F6] text-white font-semibold shadow-sm text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">stethoscope</span>
<span class="sidebar-label truncate">الزيارات</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">prescriptions</span>
<span class="sidebar-label truncate">الوصفات الطبية</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">science</span>
<span class="sidebar-label truncate">طلبات المختبر</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">radiology</span>
<span class="sidebar-label truncate">طلبات الأشعة</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">assignment_turned_in</span>
<span class="sidebar-label truncate">النتائج</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">sync_alt</span>
<span class="sidebar-label truncate">الإحالات والمتابعات</span>
</a>
</nav>
<!-- Group 3: الإدارة -->
<nav class="flex flex-col gap-1">
<div class="px-3 pb-1 pt-2 text-[11px] text-gray-400 tracking-wider font-semibold uppercase sidebar-label">الإدارة</div>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">receipt_long</span>
<span class="sidebar-label truncate">الخدمات والأسعار</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">schedule</span>
<span class="sidebar-label truncate">الدوام والجدول</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">account_balance_wallet</span>
<span class="sidebar-label truncate">التحصيل والمستحقات</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-[#203254] hover:text-white transition-all text-[14px]" href="#">
<span class="material-symbols-outlined text-[22px] shrink-0">settings</span>
<span class="sidebar-label truncate">الإعدادات</span>
</a>
</nav>
</div>
<!-- Doctor Profile Footer Card -->
<div class="sidebar-doctor-card p-3 border-t border-[#223456] bg-[#132038] relative shrink-0" data-purpose="user-profile-menu">
<div class="flex items-center justify-between gap-2">
<div class="flex items-center gap-3 min-w-0">
<div class="relative shrink-0">
<div aria-label="د. محمد أحمد" class="w-9 h-9 rounded-full bg-[#203254] text-white flex items-center justify-center font-bold text-[11px] ring-2 ring-[#0495F6]/40" title="د. محمد أحمد">م أ</div>
<span class="absolute bottom-0 left-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-[#132038]"></span>
</div>
<div class="min-w-0 sidebar-label">
<div class="text-[13px] font-semibold text-white truncate">د. محمد أحمد</div>
<div class="text-[11px] text-gray-400 truncate flex items-center gap-1.5">
<span>استشاري طب باطني</span>
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
<span>الملف الشخصي</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-gray-200 hover:bg-[#243B63] hover:text-white rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] text-gray-400">settings</span>
<span>إعدادات الحساب</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 text-gray-200 hover:bg-[#243B63] hover:text-white rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] text-emerald-400">toggle_on</span>
<span>تغيير حالة التوفر</span>
</a>
<div class="h-px bg-[#2B4066] my-1"></div>
<a class="flex items-center gap-2 px-3 py-2 text-rose-400 hover:bg-rose-500/10 rounded-lg text-[13px] transition-colors" href="#">
<span class="material-symbols-outlined text-[18px]">logout</span>
<span>تسجيل الخروج</span>
</a>
</div>
</div>
</aside>
<!-- END: AppSidebar -->
<!-- BEGIN: MainContentWrapper -->
<div class="transition-all duration-300 mr-[268px] min-h-screen flex flex-col" id="main-content-layout">
<!-- BEGIN: TopBar -->
<header class="fixed top-0 left-0 right-[268px] h-16 bg-white/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-6 transition-all duration-300 border-b border-[#E2E8F0]" data-purpose="topbar-navigation" id="top-header">
<!-- RTL Right: Quick Add + Global Search -->
<div class="flex items-center gap-4">
<!-- Quick Add Container -->
<div class="relative" id="quick-add-container" onmouseenter="window.handleQuickAddEnter()" onmouseleave="window.handleQuickAddLeave()">
<button class="flex items-center gap-1.5 h-10 px-4 rounded-lg bg-[#0495F6] text-white font-semibold text-[14px] hover:bg-[#0466F1] active:bg-[#1035DB] transition-all shadow-sm" id="quick-add-btn" onclick="window.toggleQuickAddMenu(event)" type="button">
<span class="material-symbols-outlined text-[20px]">add</span>
<span>إضافة</span>
<span class="material-symbols-outlined text-[18px] transition-transform duration-200" id="quick-add-arrow">expand_more</span>
</button>
<!-- Quick Add Dropdown Menu -->
<div class="hidden absolute top-12 right-0 w-64 bg-white rounded-xl shadow-xl border border-[#E2E8F0] p-2 z-50" id="quick-add-dropdown">
<div class="px-2.5 py-1 text-[11px] text-[#64748B] font-semibold uppercase tracking-wider">تشغيلي</div>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">person_add</span>
<span>إضافة مريض</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu()">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">event</span>
<span>موعد جديد</span>
</a>
<div class="h-px bg-[#E2E8F0] my-1.5"></div>
<div class="px-2.5 py-1 text-[11px] text-[#64748B] font-semibold uppercase tracking-wider">سريري</div>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu(); openContextualDrawer('prescription');">
<span class="material-symbols-outlined text-[18px] text-violet-600">prescriptions</span>
<span>وصفة طبية</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu(); openContextualDrawer('lab');">
<span class="material-symbols-outlined text-[18px] text-indigo-600">science</span>
<span>طلب مختبر</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu(); openContextualDrawer('radiology');">
<span class="material-symbols-outlined text-[18px] text-cyan-600">radiology</span>
<span>طلب أشعة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] text-[#0B1020] hover:bg-[#F8FAFC] hover:text-[#0495F6] transition-colors" href="#" onclick="window.closeQuickAddMenu(); openContextualDrawer('referral');">
<span class="material-symbols-outlined text-[18px] text-teal-600">sync_alt</span>
<span>إحالة / متابعة</span>
</a>
</div>
</div>
<!-- Global Quick Search Input -->
<div class="relative hidden sm:block w-72 md:w-96">
<span class="material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-[#64748B] pointer-events-none">search</span>
<input class="w-full h-10 pr-10 pl-4 bg-[#F8FAFC] rounded-lg border border-[#E2E8F0] text-[13px] text-[#0B1020] placeholder-[#64748B]/70 focus:outline-none focus:border-[#0495F6] focus:bg-white focus:ring-2 focus:ring-[#0495F6]/20 transition-all" placeholder="ابحث عن مريض أو موعد... (⌘K)" type="text"/>
</div>
</div>
<!-- RTL Left: Notifications + Language + Theme -->
<div class="flex items-center gap-2">
<button class="relative p-2 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors" title="الإشعارات" type="button">
<span class="material-symbols-outlined text-[22px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
</button>
<button class="flex items-center gap-1.5 px-2.5 py-1.5 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors text-[12px] font-semibold" title="تغيير اللغة" type="button">
<span class="material-symbols-outlined text-[18px]">language</span>
<span>عربي (AR)</span>
</button>
<button class="p-2 text-[#64748B] hover:text-[#0B1020] hover:bg-[#F8FAFC] rounded-lg transition-colors" id="theme-toggle-btn" title="تبديل المظهر" type="button">
<span class="material-symbols-outlined text-[20px]">light_mode</span>
</button>
</div>
</header>
<!-- END: TopBar -->
<!-- BEGIN: MainClinicalVisitBody -->
<main class="flex-1 pt-20 px-6 lg:px-8 pb-16">
<!-- PAGE HEADER -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6" data-purpose="page-title-actions">
<div>
<div class="flex items-center gap-2 text-[12px] text-[#64748B] mb-1">
<a class="hover:text-[#0495F6] transition-colors" href="#">العمل الطبي</a>
<span>/</span>
<a class="hover:text-[#0495F6] transition-colors" href="#">الزيارات</a>
<span>/</span>
<span class="text-[#0B1020] font-medium">توثيق الزيارة</span>
</div>
<h1 class="text-2xl font-bold text-[#0B1020]">الزيارة الطبية</h1>
<p class="text-[14px] text-[#64748B] mt-0.5">توثيق الزيارة والإجراءات السريرية للمريض</p>
</div>
<!-- Top Action -->
<div class="flex items-center gap-3">
<button class="h-9 px-4 bg-white border border-[#CBD5E1] hover:bg-[#F8FAFC] text-[#0B1020] rounded-lg text-[13px] font-medium flex items-center gap-1.5 transition-colors shadow-sm cursor-pointer" onclick="handleSaveDraft()">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">save</span>
<span>حفظ كمسودة</span>
</button>
</div>
</div>
<!-- BEGIN: PatientMiniCard -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-4 mb-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4" data-purpose="patient-summary-header">
<!-- Patient Identity -->
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-blue-100 text-[#0495F6] flex items-center justify-center font-bold text-base shrink-0">
  أ م
</div>
<div>
<div class="flex items-center gap-3">
<span class="text-lg font-bold text-[#0B1020]">أحمد محمود</span>
<span class="font-num text-[12px] bg-[#F1F5F9] text-[#0495F6] font-semibold px-2 py-0.5 rounded dir-ltr">P-10482</span>
<span class="text-[13px] text-[#64748B]">42 سنة • ذكر</span>
</div>
<div class="flex flex-wrap items-center gap-3 text-[12px] text-[#64748B] mt-1">
<span class="font-num dir-ltr text-slate-700">0599 123 456</span>
<span class="inline-flex items-center gap-1 text-[#10B981] bg-[#10B981]/10 px-2 py-0.5 rounded text-[11px] font-medium">
<span class="material-symbols-outlined text-[14px]">verified_user</span>
  وصول متاح — وفق إعدادات خصوصية المريض
</span>
</div>
</div>
</div>
<!-- Current Appointment Context & Status Selector -->
<div class="flex flex-wrap items-center gap-4 lg:border-r lg:border-[#E2E8F0] lg:pr-6">
<div class="flex flex-col text-right">
<span class="text-[11px] font-semibold text-[#64748B]">سياق الموعد</span>
<span class="text-[13px] font-medium text-[#0B1020]">اليوم، <span class="font-num">10:30</span> ص • كشفية طب باطني</span>
<span class="text-[11px] text-[#64748B]">د. محمد أحمد • عيادة د. محمد أحمد</span>
</div>
<div class="h-8 w-px bg-[#E2E8F0] hidden sm:block"></div>
<!-- Visit Status Selector -->
<div class="flex flex-col text-right min-w-[170px]">
<span class="text-[11px] font-semibold text-[#64748B] mb-1.5 block">حالة الزيارة</span>
<div class="relative flex items-center">
<select class="w-full h-9 pr-8 pl-8 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg text-[#0495F6] focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors cursor-pointer appearance-none shadow-xs text-right" id="visit-status-select" onchange="handleVisitStatusChange(this.value)">
<option selected="" value="draft">مسودة قيد التوثيق</option>
<option value="initial">تقييم مبدئي</option>
<option value="pending_results">بانتظار نتيجة الفحوصات</option>
</select>
<span class="material-symbols-outlined text-[18px] text-[#0495F6] absolute right-2.5 pointer-events-none select-none">edit_note</span>
<span class="material-symbols-outlined text-[16px] text-[#64748B] absolute left-2 pointer-events-none select-none">expand_more</span>
</div>
</div>
</div>
</div>
<!-- END: PatientMiniCard -->
<!-- BEGIN: WorkspaceGrid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" data-purpose="workspace-main-grid">
<!-- BEGIN: ClinicalDocumentationColumn (8 Cols) -->
<div class="lg:col-span-8 flex flex-col gap-6" data-purpose="documentation-column">
<!-- CLINICAL DOCUMENTATION CARD -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between border-b border-[#F1F5F9] pb-4 mb-6">
<div>
<h2 class="text-base font-bold text-[#0B1020] flex items-center gap-2">
<span class="material-symbols-outlined text-[#0495F6] text-[20px]">clinical_notes</span>
  التوثيق السريري
</h2>
<p class="text-[12px] text-[#64748B] mt-0.5">توثيق تفاصيل الزيارة الحالية</p>
</div>
<div class="px-2.5 py-1 bg-[#0495F6]/10 text-[#0495F6] text-[11px] font-semibold rounded-full flex items-center gap-1" id="status-pill-display">
<span class="w-1.5 h-1.5 rounded-full bg-[#0495F6]"></span>
<span id="status-pill-text">مسودة قيد التوثيق</span>
</div>
</div>
<form class="flex flex-col gap-6" id="clinical-doc-form" onsubmit="event.preventDefault()">
<!-- CURRENT VISIT VITALS -->
<div class="border border-[#E2E8F0] rounded-xl p-4 bg-[#F8FAFC]" id="visit-vitals-card">
<div class="flex items-center justify-between gap-3">
<div>
<div class="text-[13px] font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">vital_signs</span>
<span>العلامات الحيوية</span>
</div>
<p class="text-[11px] text-[#64748B] mt-0.5">اختيارية لهذه الزيارة ويمكن تسجيلها عند توفر القياسات.</p>
</div>
<button class="h-8 px-3 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[11px] font-semibold flex items-center gap-1 shrink-0" onclick="openVitalsModal()" type="button">
<span class="material-symbols-outlined text-[15px]">add</span>
<span id="vitals-action-label">إضافة علامات حيوية</span>
</button>
</div>
<div class="mt-3 text-[12px] text-[#64748B] bg-white border border-dashed border-[#CBD5E1] rounded-lg p-3" id="vitals-empty-state">لم يتم تسجيل علامات حيوية لهذه الزيارة</div>
<div class="hidden mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2" id="vitals-values-grid"></div>
</div>
<!-- SECTION 1: الشكوى الرئيسية -->
<div>
<label class="block text-[13px] font-bold text-[#0B1020] mb-1.5" for="main-complaint">
  الشكوى الرئيسية
</label>
<textarea class="w-full p-3 text-[14px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors placeholder:text-[#94A3B8] resize-y" id="main-complaint" oninput="persistLocalDraft()" placeholder="اكتب سبب الزيارة أو الشكوى الرئيسية كما يذكرها المريض..." rows="3"></textarea>
</div>
<!-- SECTION 2: الملاحظات السريرية -->
<div>
<label class="block text-[13px] font-bold text-[#0B1020] mb-1.5" for="clinical-notes">
  الملاحظات السريرية
</label>
<textarea class="w-full p-3 text-[14px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors placeholder:text-[#94A3B8] resize-y" id="clinical-notes" oninput="persistLocalDraft()" placeholder="أضف أي ملاحظات سريرية إضافية مرتبطة بهذه الزيارة..." rows="2"></textarea>
</div>
<!-- SECTION 3: التقييم الطبي -->
<div>
<label class="block text-[13px] font-bold text-[#0B1020] mb-1.5" for="medical-assessment">
  التقييم الطبي
</label>
<textarea class="w-full p-3 text-[14px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors placeholder:text-[#94A3B8] resize-y" id="medical-assessment" oninput="persistLocalDraft()" placeholder="دوّن التقييم أو الانطباع السريري عن الحالة..." rows="4"></textarea>
</div>
<!-- SECTION 4: التشخيص -->
<div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4">
<div class="flex items-center justify-between mb-3">
<label class="text-[13px] font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-[#783BF7]">stethoscope_check</span>
  التشخيص
</label>
<span class="text-[11px] text-[#64748B]">غير إلزامي في مرحلة المسودة أو انتظار النتائج</span>
</div>
<!-- Search/Add Diagnosis Input -->
<div class="flex items-center gap-2 mb-3">
<div class="relative flex-1">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-[#94A3B8]">search</span>
<input class="w-full h-9 pr-9 pl-3 text-[13px] bg-white border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6]" id="diag-input" onkeydown="if(event.key==='Enter'){event.preventDefault(); handleAddDiagnosis();}" placeholder="ابحث عن تشخيص..." type="text"/>
</div>
<button class="h-9 px-3 bg-white border border-[#CBD5E1] hover:bg-[#F1F5F9] text-[#0B1020] rounded-lg text-[12px] font-semibold flex items-center gap-1 transition-colors shrink-0" onclick="handleAddDiagnosis()" type="button">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">add</span>
<span>إضافة تشخيص</span>
</button>
</div>
<!-- Diagnosis List Area -->
<div class="border border-dashed border-[#CBD5E1] bg-white rounded-lg p-3 text-center" id="diagnosis-list-container">
<span class="text-[13px] text-[#64748B]" id="diagnosis-empty-msg">لم تتم إضافة تشخيص لهذه الزيارة بعد</span>
</div>
</div>
<!-- SECTION 5: خطة المتابعة -->
<div>
<label class="block text-[13px] font-bold text-[#0B1020] mb-1.5" for="followup-plan">
  خطة المتابعة
</label>
<textarea class="w-full p-3 text-[14px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors placeholder:text-[#94A3B8] resize-y" id="followup-plan" oninput="persistLocalDraft()" placeholder="دوّن خطة المتابعة أو الخطوات اللاحقة..." rows="3"></textarea>
</div>
</form>
</div>
<!-- VISIT-RELATED ACTIONS SECTION (2x2 Clean Grid) -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm" data-purpose="clinical-actions">
<div class="border-b border-[#F1F5F9] pb-3 mb-4">
<h2 class="text-base font-bold text-[#0B1020] flex items-center gap-2">
<span class="material-symbols-outlined text-[#783BF7] text-[20px]">assignment</span>
  إجراءات مرتبطة بالزيارة
</h2>
<p class="text-[12px] text-[#64748B] mt-0.5">إنشاء الوصفات، الفحوصات، والإحالات المرتبطة بملف المريض</p>
<p class="text-[11px] text-[#0369A1] mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">link</span><span>العناصر المنشأة هنا هي نفس السجلات الأصلية التي تظهر في الوصفات والطلبات والإحالات، وليست نسخًا منفصلة.</span></p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
<!-- Action Card 1: وصفة طبية -->
<div class="p-4 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] hover:border-[#0495F6]/40 hover:bg-white transition-all flex flex-col justify-between gap-4 shadow-xs">
<div class="flex flex-col gap-2">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[#783BF7] text-[20px] p-2 bg-[#783BF7]/10 rounded-lg shrink-0">prescriptions</span>
<h3 class="text-[14px] font-bold text-[#0B1020]">وصفة طبية</h3>
</div>
<p class="text-[12px] text-[#64748B] leading-relaxed">إنشاء وإدارة وصفة علاجية مرتبطة بالزيارة</p>
</div>
<button class="w-full h-10 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[13px] font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer whitespace-nowrap" onclick="openContextualDrawer('prescription')" type="button">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">add</span>
<span>إنشاء وصفة</span>
</button>
</div>
<!-- Action Card 2: طلب مختبر -->
<div class="p-4 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] hover:border-[#0495F6]/40 hover:bg-white transition-all flex flex-col justify-between gap-4 shadow-xs">
<div class="flex flex-col gap-2">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[#0495F6] text-[20px] p-2 bg-[#0495F6]/10 rounded-lg shrink-0">science</span>
<h3 class="text-[14px] font-bold text-[#0B1020]">طلب مختبر</h3>
</div>
<p class="text-[12px] text-[#64748B] leading-relaxed">طلب فحوصات مخبرية وتحديد جهة التحليل</p>
</div>
<button class="w-full h-10 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[13px] font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer whitespace-nowrap" onclick="openContextualDrawer('lab')" type="button">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">add</span>
<span>إنشاء طلب</span>
</button>
</div>
<!-- Action Card 3: طلب أشعة -->
<div class="p-4 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] hover:border-[#0495F6]/40 hover:bg-white transition-all flex flex-col justify-between gap-4 shadow-xs">
<div class="flex flex-col gap-2">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-cyan-600 text-[20px] p-2 bg-cyan-50 rounded-lg shrink-0">radiology</span>
<h3 class="text-[14px] font-bold text-[#0B1020]">طلب أشعة</h3>
</div>
<p class="text-[12px] text-[#64748B] leading-relaxed">طلب تصوير سريري مع تحديد جهة الفحص</p>
</div>
<button class="w-full h-10 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[13px] font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer whitespace-nowrap" onclick="openContextualDrawer('radiology')" type="button">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">add</span>
<span>إنشاء طلب</span>
</button>
</div>
<!-- Action Card 4: إحالة / متابعة -->
<div class="p-4 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] hover:border-[#0495F6]/40 hover:bg-white transition-all flex flex-col justify-between gap-4 shadow-xs">
<div class="flex flex-col gap-2">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-emerald-600 text-[20px] p-2 bg-emerald-50 rounded-lg shrink-0">sync_alt</span>
<h3 class="text-[14px] font-bold text-[#0B1020]">إحالة / متابعة</h3>
</div>
<p class="text-[12px] text-[#64748B] leading-relaxed">إضافة إحالة أو توثيق قرار متابعة</p>
</div>
<button class="w-full h-10 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[13px] font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer whitespace-nowrap" onclick="openContextualDrawer('referral')" type="button">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">add</span>
<span>إنشاء إحالة أو متابعة</span>
</button>
</div>
</div>
<!-- CURRENT VISIT RELATED ITEMS -->
<div class="mt-6 border-t border-[#F1F5F9] pt-4">
<h3 class="text-[13px] font-bold text-[#0B1020] mb-2.5">عناصر الزيارة الحالية</h3>
<div class="border border-dashed border-[#CBD5E1] bg-[#F8FAFC] rounded-xl p-4 text-center transition-all" id="visit-items-container">
<p class="text-[13px] text-[#64748B]" id="visit-items-empty">لا توجد عناصر مضافة لهذه الزيارة بعد</p>
<div class="hidden flex flex-col gap-2 text-right" id="visit-items-list"></div>
</div>
</div>
</div>
<div class="hidden items-start gap-2 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[12px] leading-relaxed" id="end-visit-validation">
<span class="material-symbols-outlined text-[18px] shrink-0">error</span>
<span>يجب تسجيل تقييم طبي أو تشخيص واحد على الأقل قبل إنهاء الزيارة.</span>
</div>
<!-- VISIT SAVE / COMPLETION FOOTER (CORRECTED LAYOUT) -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
<div class="flex-1 min-w-0 flex items-center gap-2 text-[12px] text-[#64748B]">
<span class="material-symbols-outlined text-[#0495F6] text-[18px] shrink-0">info</span>
<span class="truncate">يمكن حفظ الزيارة كمسودة والعودة إليها لاحقًا دون إلزام بإنهاء الزيارة فورياً</span>
</div>
<div class="shrink-0 flex items-center gap-3 flex-nowrap self-end sm:self-auto">
<button class="h-10 min-w-[122px] px-4 whitespace-nowrap bg-white border border-[#CBD5E1] hover:bg-[#F8FAFC] text-[#0B1020] rounded-lg text-[13px] font-semibold transition-colors flex items-center justify-center gap-1.5 cursor-pointer" onclick="handleSaveDraft()" type="button">
<span class="material-symbols-outlined text-[17px] text-[#64748B]">save</span>
<span>حفظ كمسودة</span>
</button>
<button class="h-10 min-w-[132px] px-5 whitespace-nowrap bg-[#0495F6] hover:bg-[#0466F1] active:bg-[#1035DB] text-white rounded-lg text-[13px] font-bold shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer" onclick="openEndVisitModal()" type="button">
<span class="material-symbols-outlined text-[18px]">check_circle</span>
<span>إنهاء الزيارة</span>
</button>
</div>
</div>
</div>
<!-- END: ClinicalDocumentationColumn -->
<!-- BEGIN: AuthorizedMedicalContextColumn (4 Cols) -->
<div class="lg:col-span-4 flex flex-col gap-4 sticky top-24" data-purpose="authorized-medical-context">
<div class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-sm">
<div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3 mb-3">
<div>
<h2 class="text-sm font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[#0495F6] text-[18px]">verified_user</span>
  السياق الطبي المتاح
</h2>
<span class="text-[11px] text-[#64748B]">وصول متاح — وفق إعدادات خصوصية المريض</span>
</div>
</div>
<div class="mb-3 flex items-start gap-2 p-2.5 rounded-lg bg-[#F0F7FF] border border-[#BAE6FD] text-[11px] text-[#0369A1] leading-relaxed">
<span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">privacy_tip</span>
<span>بعض معلومات السجل قد لا تكون متاحة وفق إعدادات الخصوصية الحالية ونطاق الرعاية المصرح به.</span>
</div>
<div class="flex flex-col gap-3.5">
<!-- 0. التاريخ الطبي -->
<div class="border border-[#E2E8F0] rounded-lg p-3 bg-[#F8FAFC]">
<div class="flex items-center justify-between mb-1.5">
<span class="text-[12px] font-bold text-[#0B1020] flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">medical_information</span>
  التاريخ الطبي
</span>
<span class="text-[10px] text-[#64748B]">السجل الطبي</span>
</div>
<p class="text-[12px] text-[#64748B] bg-white p-2.5 rounded border border-[#E2E8F0] leading-relaxed">
  لا توجد معلومات في التاريخ الطبي ضمن السجل المتاح
</p>
<button class="mt-2 w-full h-8 px-3 rounded-lg bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] text-[11px] font-semibold transition-colors flex items-center justify-center gap-1.5" onclick="openMedicalRecordModal()" type="button">
<span class="material-symbols-outlined text-[15px]">folder_shared</span>
<span>عرض الملف الطبي الكامل</span>
</button>
</div>
<!-- 1. الحساسية -->
<div class="border border-[#E2E8F0] rounded-lg p-3 bg-[#F8FAFC]">
<div class="flex items-center justify-between mb-1.5">
<span class="text-[12px] font-bold text-[#0B1020] flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-amber-500">warning</span>
  الحساسية
</span>
<span class="text-[10px] text-[#64748B]">السجل الطبي</span>
</div>
<p class="text-[12px] text-[#64748B] bg-white p-2.5 rounded border border-[#E2E8F0]">
  لا توجد معلومات حساسية ظاهرة ضمن السجل المتاح
</p>
</div>
<!-- 2. الأدوية الحالية -->
<div class="border border-[#E2E8F0] rounded-lg p-3 bg-[#F8FAFC]">
<div class="flex items-center justify-between mb-1.5">
<span class="text-[12px] font-bold text-[#0B1020] flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">medication</span>
  الأدوية الحالية
</span>
<span class="text-[10px] text-[#64748B]">السجل الطبي</span>
</div>
<p class="text-[12px] text-[#64748B] bg-white p-2.5 rounded border border-[#E2E8F0]">
  لا توجد أدوية حالية ظاهرة ضمن السجل المتاح
</p>
</div>
<!-- 3. زيارات سابقة -->
<div class="border border-[#E2E8F0] rounded-lg p-3 bg-[#F8FAFC]">
<div class="flex items-center justify-between mb-1.5">
<span class="text-[12px] font-bold text-[#0B1020] flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-violet-600">history</span>
  زيارات سابقة
</span>
<span class="text-[10px] font-num text-[#64748B]">1 زيارة</span>
</div>
<div class="bg-white p-2.5 rounded border border-[#E2E8F0] flex items-center justify-between text-[12px]">
<div>
<span class="font-bold text-[#0B1020] block">18 أكتوبر 2024</span>
<span class="text-[#64748B] text-[11px]">كشفية طب باطني • د. محمد أحمد</span>
</div>
<button class="text-[#0495F6] hover:underline text-[11px] font-semibold" onclick="openPreviousVisitModal()" type="button">عرض التفاصيل</button>
</div>
</div>
<!-- 4. نتائج سابقة ذات صلة (ICON FIXED TO 'science') -->
<div class="border border-[#E2E8F0] rounded-lg p-3 bg-[#F8FAFC]">
<div class="flex items-center justify-between mb-2">
<span class="text-[12px] font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[20px] text-[#0495F6]">science</span>
  نتائج سابقة ذات صلة
</span>
<span class="text-[10px] text-[#64748B]">المختبر والأشعة</span>
</div>
<div class="bg-white p-3 rounded-lg border border-[#E2E8F0] text-center flex flex-col items-center justify-center gap-1.5">
<span class="material-symbols-outlined text-[20px] text-[#94A3B8]">folder_open</span>
<p class="text-[12px] text-[#64748B]">لا توجد نتائج سابقة ظاهرة ضمن السجل المتاح</p>
</div>
</div>
</div>
</div>
</div>
<!-- END: AuthorizedMedicalContextColumn -->
</div>
<!-- END: WorkspaceGrid -->
</main>
<!-- END: MainClinicalVisitBody -->
</div>
<!-- END: MainContentWrapper -->
<!-- BEGIN: Contextual Drawer Engine -->
<div class="fixed inset-0 z-50 overflow-hidden pointer-events-none" id="drawer-wrapper">
<!-- Backdrop -->
<div class="fixed inset-0 bg-[#0B1020]/40 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none" id="drawer-backdrop" onclick="closeContextualDrawer()"></div>
<!-- Drawer Panel -->
<aside class="fixed inset-y-0 left-0 max-w-full flex drawer-closed pointer-events-auto" id="action-drawer">
<div class="w-screen max-w-md bg-white border-r border-[#E2E8F0] shadow-2xl flex flex-col justify-between">
<!-- Drawer Header -->
<div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC]">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[#0495F6] text-[24px]" id="drawer-icon">assignment</span>
<div>
<h3 class="text-base font-bold text-[#0B1020]" id="drawer-title">إجراء سريري</h3>
<p class="text-[11px] text-[#64748B]">أحمد محمود • <span class="font-num">P-10482</span></p>
</div>
</div>
<button class="p-1.5 rounded-lg text-[#64748B] hover:text-[#0B1020] hover:bg-slate-200 transition-colors" onclick="closeContextualDrawer()" title="إغلاق" type="button">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Drawer Body with Two Structured Sections -->
<div class="flex-1 p-5 overflow-y-auto flex flex-col gap-6" id="drawer-dynamic-content">
<!-- Section A: تفاصيل الطلب -->
<div class="flex flex-col gap-4" id="drawer-section-a">
<div class="flex items-center justify-between border-b border-[#F1F5F9] pb-2">
<span class="text-[13px] font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">article</span>
  تفاصيل الطلب
</span>
</div>
<div class="flex flex-col gap-3.5" id="drawer-fields-container-a"></div>
</div>
<!-- Section B: جهة التنفيذ والإرسال -->
<div class="flex flex-col gap-4 pt-4 border-t border-[#E2E8F0]" id="drawer-section-b">
<div class="flex items-center justify-between border-b border-[#F1F5F9] pb-2">
<span class="text-[13px] font-bold text-[#0B1020] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-teal-600">near_me</span>
<span id="drawer-section-b-title">جهة التنفيذ والإرسال</span>
</span>
</div>
<div class="flex flex-col gap-3.5" id="drawer-fields-container-b"></div>
<!-- Validation Message Container -->
<div class="hidden items-center gap-1.5 text-[12px] text-rose-600 bg-rose-50 border border-rose-200 p-2.5 rounded-lg" id="drawer-validation-msg">
<span class="material-symbols-outlined text-[16px]">error</span>
<span id="drawer-validation-text">اختر جهة التنفيذ قبل الإرسال</span>
</div>
</div>
</div>
<!-- Drawer Footer (Clean Single-Line Buttons) -->
<div class="p-4 bg-white border-t border-[#E2E8F0] flex items-center gap-2 flex-nowrap">
<button class="shrink-0 whitespace-nowrap h-10 px-3 rounded-lg border border-[#CBD5E1] text-[#0B1020] hover:bg-slate-50 text-[13px] font-semibold transition-colors cursor-pointer" onclick="closeContextualDrawer()" type="button">
  إلغاء
</button>
<button class="shrink-0 whitespace-nowrap h-10 px-3 rounded-lg bg-white border border-[#CBD5E1] hover:bg-[#F1F5F9] text-[#0B1020] text-[13px] font-semibold transition-colors flex items-center gap-1 cursor-pointer" id="drawer-save-local-btn" onclick="submitDrawerAction('local')" type="button">
<span class="material-symbols-outlined text-[16px] text-[#64748B]">save</span>
<span>حفظ في الزيارة</span>
</button>
<button class="flex-1 min-w-0 whitespace-nowrap justify-center h-10 px-2 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] active:bg-[#1035DB] text-white text-[12px] sm:text-[13px] font-bold shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer" id="drawer-send-btn" onclick="submitDrawerAction('send')" type="button">
<span class="material-symbols-outlined text-[18px]" id="drawer-send-btn-icon">send</span>
<span class="truncate" id="drawer-send-btn-label">إرسال إلى الجهة</span>
</button>
</div>
</div>
</aside>
</div>
<!-- END: Contextual Drawer Engine -->
<!-- BEGIN: VitalsModal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0B1020]/50 backdrop-blur-sm" id="vitals-modal">
<div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-[#E2E8F0] text-right">
<div class="flex items-center justify-between mb-5">
<div><h3 class="text-lg font-bold text-[#0B1020]">العلامات الحيوية</h3><p class="text-[12px] text-[#64748B] mt-0.5">تسجيل قياسات الزيارة الحالية — جميع الحقول اختيارية.</p></div>
<button class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B]" onclick="closeVitalsModal()" type="button"><span class="material-symbols-outlined">close</span></button>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
<label class="text-[12px] font-semibold text-[#0B1020]">الضغط الانقباضي<input id="vital-sys" inputmode="numeric" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="120"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">الضغط الانبساطي<input id="vital-dia" inputmode="numeric" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="80"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">النبض /دقيقة<input id="vital-pulse" inputmode="numeric" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="72"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">الحرارة °C<input id="vital-temp" inputmode="decimal" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="36.8"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">SpO₂ %<input id="vital-spo2" inputmode="numeric" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="98"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">الوزن كغ<input id="vital-weight" inputmode="decimal" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="78"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">الطول سم<input id="vital-height" inputmode="decimal" class="mt-1 w-full h-10 px-3 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:border-[#0495F6]" placeholder="175"/></label>
<label class="text-[12px] font-semibold text-[#0B1020]">BMI<input id="vital-bmi" readonly class="mt-1 w-full h-10 px-3 rounded-lg border border-[#E2E8F0] bg-[#F1F5F9] text-[#64748B]" placeholder="يُحسب تلقائيًا"/></label>
</div>
<div class="flex items-center justify-end gap-2 mt-6"><button class="h-9 px-4 rounded-lg border border-[#CBD5E1] hover:bg-[#F8FAFC] text-[13px] font-semibold" onclick="closeVitalsModal()" type="button">إلغاء</button><button class="h-9 px-5 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-[13px] font-bold" onclick="saveVitals()" type="button">حفظ العلامات الحيوية</button></div>
</div>
</div>
<!-- END: VitalsModal -->

<!-- BEGIN: MedicalRecordModal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0B1020]/50 backdrop-blur-sm" id="medical-record-modal">
<div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-[#E2E8F0] text-right">
<div class="flex items-center justify-between mb-4"><div><h3 class="text-lg font-bold text-[#0B1020]">الملف الطبي المتاح</h3><p class="text-[12px] text-[#64748B]">يظهر فقط ما تسمح به صلاحياتك وإعدادات خصوصية المريض.</p></div><button class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B]" onclick="closeMedicalRecordModal()" type="button"><span class="material-symbols-outlined">close</span></button></div>
<div class="p-3 rounded-xl bg-[#F0F7FF] border border-[#BAE6FD] text-[12px] text-[#0369A1] mb-4 flex gap-2"><span class="material-symbols-outlined text-[18px]">privacy_tip</span><span>بعض معلومات السجل غير متاحة وفق إعدادات الخصوصية الحالية.</span></div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><div class="font-bold text-[13px]">التاريخ الطبي</div><div class="text-[12px] text-[#64748B] mt-1">لا توجد معلومات في التاريخ الطبي ضمن السجل المتاح</div></div>
<div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><div class="font-bold text-[13px]">الحساسية</div><div class="text-[12px] text-[#64748B] mt-1">لا توجد معلومات حساسية ظاهرة ضمن السجل المتاح</div></div>
<div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><div class="font-bold text-[13px]">الأدوية الحالية</div><div class="text-[12px] text-[#64748B] mt-1">لا توجد أدوية حالية ظاهرة ضمن السجل المتاح</div></div>
<div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><div class="font-bold text-[13px]">زيارات سابقة</div><div class="text-[12px] text-[#64748B] mt-1">زيارة واحدة ضمن العلاقة العلاجية الحالية</div></div>
</div>
<div class="flex justify-end mt-5"><button class="h-9 px-4 rounded-lg bg-[#0495F6] text-white text-[13px] font-bold" onclick="closeMedicalRecordModal()" type="button">إغلاق</button></div>
</div>
</div>
<!-- END: MedicalRecordModal -->

<!-- BEGIN: PreviousVisitModal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0B1020]/50 backdrop-blur-sm" id="previous-visit-modal">
<div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] text-right">
<div class="flex items-center justify-between mb-4"><div><h3 class="text-lg font-bold text-[#0B1020]">تفاصيل الزيارة السابقة</h3><p class="text-[12px] text-[#64748B]">18 أكتوبر 2024 • كشفية طب باطني</p></div><button class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B]" onclick="closePreviousVisitModal()" type="button"><span class="material-symbols-outlined">close</span></button></div>
<div class="space-y-3 text-[13px]"><div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><b>الطبيب:</b> د. محمد أحمد</div><div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC]"><b>السياق المتاح:</b> ملخص الزيارة المرتبط بعلاقتك العلاجية بالمريض.</div><div class="p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] text-[#64748B]">لا يتم عرض أي معلومات إضافية مخفية أو خارج نطاق الصلاحية الحالية.</div></div>
<div class="flex justify-end mt-5"><button class="h-9 px-4 rounded-lg bg-[#0495F6] text-white text-[13px] font-bold" onclick="closePreviousVisitModal()" type="button">إغلاق</button></div>
</div>
</div>
<!-- END: PreviousVisitModal -->

<!-- BEGIN: EndVisitModal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0B1020]/50 backdrop-blur-sm" data-purpose="confirmation-modal" id="end-visit-modal">
<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#E2E8F0] text-right">
<div class="w-12 h-12 rounded-full bg-[#0495F6]/10 text-[#0495F6] flex items-center justify-center mb-4">
<span class="material-symbols-outlined text-[28px]">check_circle</span>
</div>
<h3 class="text-lg font-bold text-[#0B1020] mb-2">إنهاء الزيارة؟</h3>
<p class="text-[14px] text-[#64748B] mb-6 leading-relaxed">
  سيتم حفظ التوثيق الحالي وإنهاء الزيارة. الطلبات أو النتائج المعلقة ستبقى مفتوحة ويمكن متابعتها لاحقًا.
</p>
<div class="flex items-center justify-end gap-3">
<button class="h-9 px-4 rounded-lg border border-[#CBD5E1] text-[#0B1020] hover:bg-[#F8FAFC] text-[13px] font-semibold transition-colors cursor-pointer" onclick="closeEndVisitModal()" type="button">
  رجوع
</button>
<button class="h-9 px-5 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] active:bg-[#1035DB] text-white text-[13px] font-bold transition-colors shadow-sm cursor-pointer" onclick="confirmEndVisit()" type="button">
  تأكيد وإنهاء الزيارة
</button>
</div>
</div>
</div>
<!-- END: EndVisitModal -->
<!-- BEGIN: In-App Toast Notification -->
<div class="fixed bottom-6 left-6 z-50 toast-hidden" id="toast-notification">
<div class="bg-[#0B1020] text-white text-[13px] font-medium px-4 py-3 rounded-xl shadow-2xl border border-[#1F2C45] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[#0495F6] text-[20px]" id="toast-icon">check_circle</span>
<span id="toast-message">إشعار</span>
</div>
</div>
<!-- END: In-App Toast Notification -->
<!-- BEGIN: Application Shell & Workflow Scripts -->
<script data-purpose="app-shell-and-visit-logic">
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
      pinBtn.classList.remove('text-gray-400');
      pinBtn.title = 'طي القائمة الجانبية';
    }
    if (pinIcon) {
      pinIcon.textContent = 'push_pin';
    }
  } else {
    sidebar.classList.add('sidebar-collapsed');
    mainLayout.style.marginRight = '72px';
    topHeader.style.right = '72px';
    if (pinBtn) {
      pinBtn.classList.remove('text-[#0495F6]', 'bg-white/10');
      pinBtn.classList.add('text-gray-400');
      pinBtn.title = 'تثبيت القائمة الجانبية';
    }
    if (pinIcon) {
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
      if (menu) menu.classList.add('hidden');
    }
  });
}

let quickAddTimer = null;
window.handleQuickAddEnter = function() {
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (menu) menu.classList.remove('hidden');
  if (arrow) arrow.textContent = 'expand_less';
};

window.handleQuickAddLeave = function() {
  if (quickAddTimer) clearTimeout(quickAddTimer);
  quickAddTimer = setTimeout(() => {
    window.closeQuickAddMenu();
    quickAddTimer = null;
  }, 200);
};

window.toggleQuickAddMenu = function(e) {
  if (e) e.stopPropagation();
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (!menu) return;
  const isHidden = menu.classList.toggle('hidden');
  if (arrow) arrow.textContent = isHidden ? 'expand_more' : 'expand_less';
};

window.closeQuickAddMenu = function() {
  if (quickAddTimer) {
    clearTimeout(quickAddTimer);
    quickAddTimer = null;
  }
  const menu = document.getElementById('quick-add-dropdown');
  const arrow = document.getElementById('quick-add-arrow');
  if (menu) menu.classList.add('hidden');
  if (arrow) arrow.textContent = 'expand_more';
};

function toggleDoctorMenu(e) {
  if (e) e.stopPropagation();
  const menu = document.getElementById('doctor-account-menu');
  if (menu) {
    menu.classList.toggle('hidden');
  }
}

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
});

let toastTimeout = null;
function showToast(message, iconName = 'check_circle') {
  const toast = document.getElementById('toast-notification');
  const msgEl = document.getElementById('toast-message');
  const iconEl = document.getElementById('toast-icon');
  if (!toast || !msgEl) return;
  msgEl.textContent = message;
  if (iconEl) iconEl.textContent = iconName;

  toast.classList.remove('toast-hidden');
  toast.classList.add('toast-visible');

  if (toastTimeout) clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => {
    toast.classList.remove('toast-visible');
    toast.classList.add('toast-hidden');
  }, 3500);
}

// -------------------------------------------------------------
// REQUIRED CORE WORKFLOW ARCHITECTURE & GLOBALS
// -------------------------------------------------------------
const DRAFT_KEY = 'thera_visit_draft_P-10482';
let isRestoringDraft = false;
let visitItems = [];
let currentDrawerType = 'prescription';
let activeEditingItemId = null;

const drawerConfig = {
  prescription: {
    title: 'إضافة وصفة طبية',
    itemLabel: 'وصفة طبية',
    icon: 'prescriptions',
    sendBtnLabel: 'إرسال إلى الصيدلية',
    sectionBTitle: 'جهة التنفيذ والإرسال',
    sectionA: [
      { id: 'rx-search', label: 'البحث عن الدواء', type: 'input', placeholder: 'ابحث عن دواء...' },
      { id: 'rx-name', label: 'الاسم العلمي / التجاري', type: 'input', placeholder: 'أدخل الاسم...' },
      { id: 'rx-form', label: 'الشكل الدوائي', type: 'input', placeholder: 'أدخل الشكل الدوائي...' },
      { id: 'rx-strength', label: 'التركيز', type: 'input', placeholder: 'أدخل التركيز...' },
      { id: 'rx-dose', label: 'الجرعة', type: 'input', placeholder: 'أدخل الجرعة...' },
      { id: 'rx-frequency', label: 'التكرار', type: 'input', placeholder: 'أدخل التكرار...' },
      { id: 'rx-duration', label: 'المدة', type: 'input', placeholder: 'أدخل المدة...' },
      { id: 'rx-quantity', label: 'الكمية', type: 'input', placeholder: 'أدخل الكمية...' },
      { id: 'rx-instructions', label: 'التعليمات', type: 'textarea', placeholder: 'أدخل تعليمات الاستخدام...' }
    ]
  },
  lab: {
    title: 'إضافة طلب مختبر',
    itemLabel: 'طلب مختبر',
    icon: 'science',
    sendBtnLabel: 'إرسال إلى المختبر',
    sectionBTitle: 'جهة التنفيذ والإرسال',
    sectionA: [
      { id: 'lab-test', label: 'الفحص', type: 'input', placeholder: 'ابحث عن فحص أو أدخل اسم الفحص...' },
      { id: 'lab-context', label: 'السياق السريري', type: 'textarea', placeholder: 'أدخل السياق السريري...' },
      { id: 'lab-priority', label: 'الأولوية', type: 'select', options: ['عادي', 'عاجل'], defaultValue: 'عادي' },
      { id: 'lab-instructions', label: 'التعليمات', type: 'textarea', placeholder: 'أدخل تعليمات إضافية...' }
    ]
  },
  radiology: {
    title: 'إضافة طلب أشعة',
    itemLabel: 'طلب أشعة',
    icon: 'radiology',
    sendBtnLabel: 'إرسال إلى مركز الأشعة',
    sectionBTitle: 'جهة التنفيذ والإرسال',
    sectionA: [
      { id: 'rad-type', label: 'نوع التصوير', type: 'input', placeholder: 'أدخل نوع التصوير...' },
      { id: 'rad-area', label: 'المنطقة / الخدمة', type: 'input', placeholder: 'أدخل المنطقة أو الخدمة...' },
      { id: 'rad-context', label: 'السياق السريري', type: 'textarea', placeholder: 'أدخل السياق السريري...' },
      { id: 'rad-question', label: 'السؤال السريري', type: 'textarea', placeholder: 'أدخل السؤال السريري...' },
      { id: 'rad-priority', label: 'الأولوية', type: 'select', options: ['عادي', 'عاجل'], defaultValue: 'عادي' },
      { id: 'rad-instructions', label: 'التعليمات', type: 'textarea', placeholder: 'أدخل تعليمات إضافية...' }
    ]
  },
  referral: {
    title: 'إضافة إحالة / متابعة',
    itemLabel: 'إحالة / متابعة',
    icon: 'sync_alt',
    sendBtnLabel: 'إرسال الإحالة',
    sectionBTitle: 'جهة الإحالة',
    sectionA: [
      { id: 'ref-kind', label: 'نوع الإجراء', type: 'select', options: ['إحالة', 'متابعة'], defaultValue: 'إحالة' },
      { id: 'ref-reason', label: 'السبب *', type: 'textarea', placeholder: 'اكتب سبب الإحالة أو المتابعة...' },
      { id: 'ref-summary', label: 'الملخص السريري *', type: 'textarea', placeholder: 'اكتب ملخصًا سريريًا موجزًا للحالة...' },
      { id: 'ref-question', label: 'السؤال السريري للطبيب المحال إليه', type: 'textarea', placeholder: 'ما السؤال أو الهدف المحدد من الإحالة؟' },
      { id: 'ref-relevant', label: 'المعلومات ذات الصلة من السجل', type: 'textarea', placeholder: 'التشخيصات، الأدوية، نتائج المختبر أو الأشعة ذات الصلة...' },
      { id: 'ref-attachments', label: 'المرفقات', type: 'file', accept: '.pdf,.jpg,.jpeg,.png' },
      { id: 'followup-period', label: 'التاريخ أو الفترة المقترحة *', type: 'input', placeholder: 'مثال: بعد أسبوعين أو 20 أكتوبر 2026' },
      { id: 'followup-instructions', label: 'تعليمات للمريض', type: 'textarea', placeholder: 'اكتب التعليمات التي يحتاجها المريض قبل المتابعة...' },
      { id: 'followup-internal-notes', label: 'ملاحظات داخلية', type: 'textarea', placeholder: 'ملاحظات داخلية اختيارية...' }
    ]
  }
};

function updateVisitStatusPill(status) {
  const pillDisplay = document.getElementById('status-pill-display');
  const pillText = document.getElementById('status-pill-text');
  if (!pillDisplay || !pillText) return;
  if (status === 'draft') {
    pillDisplay.className = 'px-2.5 py-1 bg-[#0495F6]/10 text-[#0495F6] text-[11px] font-semibold rounded-full flex items-center gap-1';
    pillText.textContent = 'مسودة قيد التوثيق';
  } else if (status === 'initial') {
    pillDisplay.className = 'px-2.5 py-1 bg-violet-50 text-violet-700 text-[11px] font-semibold rounded-full flex items-center gap-1';
    pillText.textContent = 'تقييم مبدئي';
  } else if (status === 'pending_results') {
    pillDisplay.className = 'px-2.5 py-1 bg-amber-50 text-amber-700 text-[11px] font-semibold rounded-full flex items-center gap-1';
    pillText.textContent = 'بانتظار نتيجة الفحوصات';
  }
}

function handleVisitStatusChange(status) {
  updateVisitStatusPill(status);
  if (!isRestoringDraft) {
    persistLocalDraft();
  }
}

function resetDiagnosisUI() {
  const container = document.getElementById('diagnosis-list-container');
  if (!container) return;
  container.replaceChildren();
  container.className = 'border border-dashed border-[#CBD5E1] bg-white rounded-lg p-3 text-center';
  const empty = document.createElement('span');
  empty.id = 'diagnosis-empty-msg';
  empty.className = 'text-[13px] text-[#64748B]';
  empty.textContent = 'لم تتم إضافة تشخيص لهذه الزيارة بعد';
  container.appendChild(empty);
}

function appendDiagnosisTag(val, shouldPersist = true) {
  const cleanVal = typeof val === 'string' ? val.trim() : '';
  if (!cleanVal) return;
  const container = document.getElementById('diagnosis-list-container');
  if (!container) return;

  const existingTags = container.querySelectorAll('.diag-tag span');
  for (let span of existingTags) {
    if (span.textContent === cleanVal) return;
  }

  const emptyMsg = document.getElementById('diagnosis-empty-msg');
  if (emptyMsg) {
    emptyMsg.remove();
    container.className = 'flex flex-wrap gap-2 text-right p-2 border border-dashed border-[#CBD5E1] bg-white rounded-lg';
  }

  const tag = document.createElement('div');
  tag.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-[#F1F5F9] border border-[#CBD5E1] rounded-lg text-[13px] font-medium text-[#0B1020] diag-tag';

  const textSpan = document.createElement('span');
  textSpan.textContent = cleanVal;

  const removeBtn = document.createElement('button');
  removeBtn.type = 'button';
  removeBtn.className = 'text-rose-500 hover:text-rose-700 text-[16px] leading-none cursor-pointer';
  removeBtn.setAttribute('aria-label', 'حذف');
  removeBtn.textContent = '×';
  removeBtn.onclick = function() {
    tag.remove();
    const remaining = container.querySelectorAll('.diag-tag');
    if (remaining.length === 0) {
      resetDiagnosisUI();
    }
    if (!isRestoringDraft) {
      persistLocalDraft();
    }
  };

  tag.appendChild(textSpan);
  tag.appendChild(removeBtn);
  container.appendChild(tag);

  if (shouldPersist && !isRestoringDraft) {
    persistLocalDraft();
  }
}

function handleAddDiagnosis() {
  const input = document.getElementById('diag-input');
  if (!input) return;
  const val = input.value.trim();
  if (!val) return;
  appendDiagnosisTag(val, true);
  input.value = '';
}

function persistLocalDraft() {
  if (typeof canEndVisit === 'function' && canEndVisit()) hideEndVisitValidation();
  if (isRestoringDraft) return;
  const draft = {
    status: document.getElementById('visit-status-select')?.value || 'draft',
    mainComplaint: document.getElementById('main-complaint')?.value || '',
    assessment: document.getElementById('medical-assessment')?.value || '',
    followup: document.getElementById('followup-plan')?.value || '',
    notes: document.getElementById('clinical-notes')?.value || '',
    diagnoses: Array.from(document.querySelectorAll('#diagnosis-list-container .diag-tag span')).map(s => s.textContent),
    visitItems: visitItems.map(item => ({
      id: item.id,
      type: item.type,
      title: item.title,
      name: item.name,
      data: item.data || {},
      workflowState: item.workflowState || 'local_only',
      destinationType: item.destinationType || '',
      destinationName: item.destinationName || ''
    }))
  };
  localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
}

function normalizeStoredVisitItem(item) {
  if (!item || typeof item !== 'object') return null;
  const validTypes = ['prescription', 'lab', 'radiology', 'referral'];
  if (!validTypes.includes(item.type)) return null;

  const rawName = typeof item.name === 'string' ? item.name.trim() : (typeof item.title === 'string' ? item.title.trim() : '');
  if (!rawName) return null;

  const cfg = drawerConfig[item.type];
  const derivedTitle = cfg ? cfg.itemLabel : 'إجراء سريري';

  return {
    id: item.id || `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
    type: item.type,
    title: derivedTitle,
    name: rawName,
    data: (item.data && typeof item.data === 'object') ? item.data : {},
    workflowState: item.workflowState === 'send_requested' ? 'send_requested' : 'local_only',
    destinationType: item.destinationType || '',
    destinationName: item.destinationName || ''
  };
}

function renderVisitItems() {
  const list = document.getElementById('visit-items-list');
  const empty = document.getElementById('visit-items-empty');
  if (!list || !empty) return;

  list.replaceChildren();

  if (!visitItems || visitItems.length === 0) {
    list.classList.add('hidden');
    empty.textContent = 'لا توجد عناصر مضافة لهذه الزيارة بعد';
    empty.classList.remove('hidden');
    return;
  }

  empty.classList.add('hidden');
  list.classList.remove('hidden');

  visitItems.forEach((item) => {
    const row = document.createElement('div');
    row.className = 'flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-white border border-[#E2E8F0] rounded-xl shadow-xs gap-3 transition-colors';

    const infoWrap = document.createElement('div');
    infoWrap.className = 'flex items-start sm:items-center gap-3 min-w-0';

    const cfg = drawerConfig[item.type];
    const iconSpan = document.createElement('span');
    iconSpan.className = 'material-symbols-outlined text-[20px] text-[#0495F6] p-2 bg-[#0495F6]/10 rounded-lg shrink-0 mt-0.5 sm:mt-0';
    iconSpan.textContent = cfg ? cfg.icon : 'assignment';

    const textCol = document.createElement('div');
    textCol.className = 'flex flex-col min-w-0';

    const titleSpan = document.createElement('span');
    titleSpan.className = 'text-[13px] font-bold text-[#0B1020] truncate';
    titleSpan.textContent = item.name;

    const subWrap = document.createElement('div');
    subWrap.className = 'flex items-center gap-2 flex-wrap text-[11px] text-[#64748B] mt-0.5';

    const typeSpan = document.createElement('span');
    typeSpan.className = 'font-semibold';
    typeSpan.textContent = item.title || (cfg ? cfg.itemLabel : 'إجراء سريري');
    subWrap.appendChild(typeSpan);
    const canonicalSep = document.createElement('span');
    canonicalSep.textContent = '•';
    const canonicalSpan = document.createElement('span');
    canonicalSpan.className = 'text-[#0369A1] font-medium';
    canonicalSpan.textContent = 'سجل أصلي مرتبط بالزيارة';
    subWrap.appendChild(canonicalSep);
    subWrap.appendChild(canonicalSpan);

    if (item.destinationName) {
      const sep = document.createElement('span');
      sep.textContent = '•';
      const destSpan = document.createElement('span');
      destSpan.className = 'text-[#64748B]';
      destSpan.textContent = `جهة التنفيذ: ${item.destinationName}`;
      subWrap.appendChild(sep);
      subWrap.appendChild(destSpan);
    }

    textCol.appendChild(titleSpan);
    textCol.appendChild(subWrap);
    infoWrap.appendChild(iconSpan);
    infoWrap.appendChild(textCol);

    const actionWrap = document.createElement('div');
    actionWrap.className = 'flex items-center gap-2 self-end sm:self-auto shrink-0';

    const statusBadge = document.createElement('span');
    if (item.workflowState === 'send_requested') {
      statusBadge.className = 'px-2 py-0.5 bg-[#0495F6]/10 text-[#0495F6] rounded text-[11px] font-semibold';
      statusBadge.textContent = 'جاهز للإرسال';
    } else {
      statusBadge.className = 'px-2 py-0.5 bg-slate-100 text-[#64748B] rounded text-[11px] font-semibold';
      statusBadge.textContent = 'داخل الزيارة';
    }
    actionWrap.appendChild(statusBadge);

    if (item.workflowState === 'local_only' && !item.destinationName && item.type !== 'referral') {
      const setDestBtn = document.createElement('button');
      setDestBtn.type = 'button';
      setDestBtn.className = 'px-2.5 py-1 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[11px] font-bold transition-colors cursor-pointer flex items-center gap-1';
      
      const sendIcon = document.createElement('span');
      sendIcon.className = 'material-symbols-outlined text-[14px]';
      sendIcon.textContent = 'near_me';

      const btnText = document.createElement('span');
      btnText.textContent = 'تحديد جهة وإرسال';

      setDestBtn.appendChild(sendIcon);
      setDestBtn.appendChild(btnText);

      setDestBtn.onclick = function() {
        openContextualDrawer(item.type, item);
      };
      actionWrap.appendChild(setDestBtn);
    } else if (item.workflowState === 'local_only' && item.type === 'referral' && item.data && item.data['ref-kind'] === 'إحالة' && !item.destinationName) {
      const setDestBtn = document.createElement('button');
      setDestBtn.type = 'button';
      setDestBtn.className = 'px-2.5 py-1 bg-white border border-[#CBD5E1] hover:border-[#0495F6] hover:text-[#0495F6] text-[#0B1020] rounded-lg text-[11px] font-bold transition-colors cursor-pointer flex items-center gap-1';
      
      const sendIcon = document.createElement('span');
      sendIcon.className = 'material-symbols-outlined text-[14px]';
      sendIcon.textContent = 'near_me';

      const btnText = document.createElement('span');
      btnText.textContent = 'تحديد جهة وإرسال';

      setDestBtn.appendChild(sendIcon);
      setDestBtn.appendChild(btnText);

      setDestBtn.onclick = function() {
        openContextualDrawer(item.type, item);
      };
      actionWrap.appendChild(setDestBtn);
    }

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'p-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors cursor-pointer';
    delBtn.setAttribute('aria-label', 'حذف');
    const delIcon = document.createElement('span');
    delIcon.className = 'material-symbols-outlined text-[18px]';
    delIcon.textContent = 'delete';
    delBtn.appendChild(delIcon);

    delBtn.onclick = function() {
      visitItems = visitItems.filter(v => v.id !== item.id);
      renderVisitItems();
      persistLocalDraft();
    };
    actionWrap.appendChild(delBtn);

    row.appendChild(infoWrap);
    row.appendChild(actionWrap);
    list.appendChild(row);
  });
}

function restoreLocalDraft() {
  try {
    isRestoringDraft = true;
    const raw = localStorage.getItem(DRAFT_KEY);
    if (!raw) {
      resetDiagnosisUI();
      renderVisitItems();
      return;
    }
    const draft = JSON.parse(raw);

    if (draft && typeof draft === 'object') {
      if (typeof draft.mainComplaint === 'string') {
        const el = document.getElementById('main-complaint');
        if (el) el.value = draft.mainComplaint;
      }
      if (typeof draft.assessment === 'string') {
        const el = document.getElementById('medical-assessment');
        if (el) el.value = draft.assessment;
      }
      if (typeof draft.followup === 'string') {
        const el = document.getElementById('followup-plan');
        if (el) el.value = draft.followup;
      }
      if (typeof draft.notes === 'string') {
        const el = document.getElementById('clinical-notes');
        if (el) el.value = draft.notes;
      }

      resetDiagnosisUI();
      if (Array.isArray(draft.diagnoses)) {
        draft.diagnoses.forEach(diag => {
          if (diag) appendDiagnosisTag(diag, false);
        });
      }

      if (Array.isArray(draft.visitItems)) {
        visitItems = draft.visitItems
          .map(normalizeStoredVisitItem)
          .filter(Boolean);
        renderVisitItems();
      } else {
        visitItems = [];
        renderVisitItems();
      }

      if (typeof draft.status === 'string' && draft.status) {
        const select = document.getElementById('visit-status-select');
        if (select) {
          select.value = draft.status;
          updateVisitStatusPill(draft.status);
        }
      }
    }
  } catch (e) {
    console.warn('Could not restore draft:', e);
  } finally {
    isRestoringDraft = false;
  }
}

function handleSaveDraft() {
  persistLocalDraft();
  showToast('تم حفظ المسودة محليًا على هذا الجهاز', 'save');
}

// -------------------------------------------------------------
// CONTEXTUAL DRAWER: CUSTOM RTL SELECT HELPER
// -------------------------------------------------------------
function createRtlSelectWrapper(selectElement) {
  const relDiv = document.createElement('div');
  relDiv.className = 'relative';

  selectElement.className = 'appearance-none w-full h-10 pr-3 pl-10 text-right text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors cursor-pointer';

  const arrow = document.createElement('span');
  arrow.className = 'material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-[#64748B] pointer-events-none select-none';
  arrow.textContent = 'expand_more';

  relDiv.appendChild(selectElement);
  relDiv.appendChild(arrow);
  return relDiv;
}

function buildDrawerField(field, existingValue = '') {
  const wrap = document.createElement('div');
  wrap.dataset.fieldId = field.id;
  const label = document.createElement('label');
  label.className = 'block text-[13px] font-bold text-[#0B1020] mb-1.5';
  label.setAttribute('for', field.id);
  label.textContent = field.label;
  wrap.appendChild(label);

  if (field.type === 'select') {
    const select = document.createElement('select');
    select.id = field.id;
    if (Array.isArray(field.options)) {
      field.options.forEach(optText => {
        const opt = document.createElement('option');
        opt.value = optText;
        opt.textContent = optText;
        if (existingValue ? existingValue === optText : optText === field.defaultValue) {
          opt.selected = true;
        }
        select.appendChild(opt);
      });
    }
    wrap.appendChild(createRtlSelectWrapper(select));
  } else if (field.type === 'file') {
    const input = document.createElement('input');
    input.id = field.id;
    input.type = 'file';
    if (field.accept) input.accept = field.accept;
    input.className = 'w-full text-[12px] text-[#64748B] file:ml-3 file:h-9 file:px-3 file:rounded-lg file:border-0 file:bg-[#F1F5F9] file:text-[#0B1020] file:font-semibold hover:file:bg-[#E2E8F0] cursor-pointer';
    wrap.appendChild(input);
    const hint = document.createElement('span');
    hint.className = 'text-[10px] text-[#64748B] mt-1 block';
    hint.textContent = 'PDF / JPG / JPEG / PNG';
    wrap.appendChild(hint);
  } else if (field.type === 'textarea') {
    const textarea = document.createElement('textarea');
    textarea.id = field.id;
    textarea.placeholder = field.placeholder || '';
    textarea.rows = field.rows || 3;
    textarea.value = existingValue || '';
    textarea.className = 'w-full p-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors resize-y';
    wrap.appendChild(textarea);
  } else {
    const input = document.createElement('input');
    input.id = field.id;
    input.type = 'text';
    input.placeholder = field.placeholder || '';
    input.value = existingValue || '';
    input.className = 'w-full h-10 px-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors';
    wrap.appendChild(input);
  }

  return wrap;
}

function updateDestinationFields(type, existingData = {}) {
  const containerB = document.getElementById('drawer-fields-container-b');
  const sectionB = document.getElementById('drawer-section-b');
  const sendBtnLabel = document.getElementById('drawer-send-btn-label');
  const sendBtnIcon = document.getElementById('drawer-send-btn-icon');
  if (!containerB || !sectionB) return;

  containerB.replaceChildren();
  hideDrawerValidation();

  if (type === 'referral') {
    const kindSelect = document.getElementById('ref-kind');
    const isFollowup = kindSelect && kindSelect.value === 'متابعة';

    if (isFollowup) {
      sectionB.classList.add('hidden');
      if (sendBtnLabel) sendBtnLabel.textContent = 'إضافة المتابعة';
      if (sendBtnIcon) sendBtnIcon.textContent = 'check';
      return;
    } else {
      sectionB.classList.remove('hidden');
      if (sendBtnLabel) sendBtnLabel.textContent = 'إرسال الإحالة';
      if (sendBtnIcon) sendBtnIcon.textContent = 'send';

      const destTypeWrap = document.createElement('div');
      const label = document.createElement('label');
      label.className = 'block text-[13px] font-bold text-[#0B1020] mb-1.5';
      label.textContent = 'طريقة الإحالة';
      destTypeWrap.appendChild(label);

      const destSelect = document.createElement('select');
      destSelect.id = 'ref-dest-type';
      
      const options = ['طبيب من شبكة THERA', 'منشأة من شبكة THERA', 'جهة خارج THERA'];
      options.forEach(optText => {
        const opt = document.createElement('option');
        opt.value = optText;
        opt.textContent = optText;
        if (existingData['ref-dest-type'] === optText) opt.selected = true;
        destSelect.appendChild(opt);
      });
      destTypeWrap.appendChild(createRtlSelectWrapper(destSelect));
      containerB.appendChild(destTypeWrap);

      const dynamicSubContainer = document.createElement('div');
      dynamicSubContainer.id = 'ref-dynamic-sub-container';
      containerB.appendChild(dynamicSubContainer);

      function renderReferralSubTarget() {
        dynamicSubContainer.replaceChildren();
        hideDrawerValidation();
        const curMode = destSelect.value;
        const subWrap = document.createElement('div');
        const subLabel = document.createElement('label');
        subLabel.className = 'block text-[13px] font-bold text-[#0B1020] mb-1.5';
        
        const subInput = document.createElement('input');
        subInput.type = 'text';
        subInput.className = 'w-full h-10 px-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors';

        const hint = document.createElement('span');
        hint.className = 'text-[11px] text-[#64748B] mt-1 block';

        if (curMode === 'طبيب من شبكة THERA') {
          subLabel.textContent = 'الطبيب المستلم';
          subInput.id = 'ref-target-doc';
          subInput.placeholder = 'ابحث عن طبيب...';
          subInput.value = existingData['ref-target-doc'] || '';
          hint.textContent = 'ابدأ الكتابة للبحث في شبكة THERA';
        } else if (curMode === 'منشأة من شبكة THERA') {
          subLabel.textContent = 'المنشأة المستلمة';
          subInput.id = 'ref-target-fac';
          subInput.placeholder = 'ابحث عن منشأة...';
          subInput.value = existingData['ref-target-fac'] || '';
          hint.textContent = 'ابدأ الكتابة للبحث في شبكة THERA';
        } else {
          subLabel.textContent = 'اسم الجهة أو الطبيب الخارجي *';
          subInput.id = 'ref-target-ext';
          subInput.placeholder = 'اكتب اسم الطبيب أو المنشأة...';
          subInput.value = existingData['ref-target-ext'] || '';
          hint.textContent = 'توثيق نصي فقط — لا يتم إرسال إحالة إلكترونية خارج THERA';
        }

        subWrap.appendChild(subLabel);
        subWrap.appendChild(subInput);
        subWrap.appendChild(hint);
        dynamicSubContainer.appendChild(subWrap);

        if (sendBtnLabel && sendBtnIcon) {
          if (curMode === 'جهة خارج THERA') {
            sendBtnLabel.textContent = 'حفظ الإحالة';
            sendBtnIcon.textContent = 'save';
          } else {
            sendBtnLabel.textContent = 'إرسال الإحالة';
            sendBtnIcon.textContent = 'send';
          }
        }
      }

      destSelect.addEventListener('change', renderReferralSubTarget);
      renderReferralSubTarget();
      return;
    }
  }

  // Prescription, Lab, Radiology
  sectionB.classList.remove('hidden');
  const cfg = drawerConfig[type];
  if (sendBtnLabel && cfg) sendBtnLabel.textContent = cfg.sendBtnLabel;
  if (sendBtnIcon) sendBtnIcon.textContent = 'send';

  let selectId = '';
  let selectLabel = '';
  let defaultSelectVal = 'بدون تحديد جهة الآن';
  let options = [];

  if (type === 'prescription') {
    selectId = 'rx-dest-type';
    selectLabel = 'طريقة الصرف';
    options = ['بدون تحديد جهة الآن', 'صيدلية من شبكة THERA', 'صيدلية خارجية'];
  } else if (type === 'lab') {
    selectId = 'lab-dest-type';
    selectLabel = 'طريقة التنفيذ';
    options = ['بدون تحديد جهة الآن', 'مختبر من شبكة THERA', 'مختبر خارجي'];
  } else if (type === 'radiology') {
    selectId = 'rad-dest-type';
    selectLabel = 'طريقة التنفيذ';
    options = ['بدون تحديد جهة الآن', 'مركز أشعة من شبكة THERA', 'مركز أشعة خارجي'];
  }

  const methodWrap = document.createElement('div');
  const mLabel = document.createElement('label');
  mLabel.className = 'block text-[13px] font-bold text-[#0B1020] mb-1.5';
  mLabel.textContent = selectLabel;
  methodWrap.appendChild(mLabel);

  const mSelect = document.createElement('select');
  mSelect.id = selectId;

  options.forEach(optText => {
    const opt = document.createElement('option');
    opt.value = optText;
    opt.textContent = optText;
    if (existingData[selectId] ? existingData[selectId] === optText : optText === defaultSelectVal) {
      opt.selected = true;
    }
    mSelect.appendChild(opt);
  });
  methodWrap.appendChild(createRtlSelectWrapper(mSelect));
  containerB.appendChild(methodWrap);

  const dynamicWrap = document.createElement('div');
  dynamicWrap.id = 'dynamic-dest-subfield-wrapper';
  containerB.appendChild(dynamicWrap);

  function renderSubDestinationField() {
    dynamicWrap.replaceChildren();
    hideDrawerValidation();
    const selVal = mSelect.value;
    if (selVal === 'بدون تحديد جهة الآن') {
      return;
    }

    const fieldWrap = document.createElement('div');
    const fLabel = document.createElement('label');
    fLabel.className = 'block text-[13px] font-bold text-[#0B1020] mb-1.5';

    const fInput = document.createElement('input');
    fInput.type = 'text';
    fInput.className = 'w-full h-10 px-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#0495F6] focus:bg-white transition-colors';

    const hint = document.createElement('span');
    hint.className = 'text-[11px] text-[#64748B] mt-1 block';

    if (type === 'prescription') {
      if (selVal === 'صيدلية من شبكة THERA') {
        fLabel.textContent = 'الصيدلية';
        fInput.id = 'rx-pharmacy-network';
        fInput.placeholder = 'ابحث عن صيدلية...';
        fInput.value = existingData['rx-pharmacy-network'] || '';
        hint.textContent = 'ابدأ الكتابة للبحث في شبكة THERA';
      } else {
        fLabel.textContent = 'اسم الصيدلية الخارجية';
        fInput.id = 'rx-pharmacy-external';
        fInput.placeholder = 'أدخل اسم الصيدلية...';
        fInput.value = existingData['rx-pharmacy-external'] || '';
        hint.textContent = 'جهة خارج شبكة THERA';
      }
    } else if (type === 'lab') {
      if (selVal === 'مختبر من شبكة THERA') {
        fLabel.textContent = 'المختبر';
        fInput.id = 'lab-network';
        fInput.placeholder = 'ابحث عن مختبر...';
        fInput.value = existingData['lab-network'] || '';
        hint.textContent = 'ابدأ الكتابة للبحث في شبكة THERA';
      } else {
        fLabel.textContent = 'اسم المختبر الخارجي';
        fInput.id = 'lab-external';
        fInput.placeholder = 'أدخل اسم المختبر...';
        fInput.value = existingData['lab-external'] || '';
        hint.textContent = 'جهة خارج شبكة THERA';
      }
    } else if (type === 'radiology') {
      if (selVal === 'مركز أشعة من شبكة THERA') {
        fLabel.textContent = 'مركز الأشعة';
        fInput.id = 'rad-network';
        fInput.placeholder = 'ابحث عن مركز أشعة...';
        fInput.value = existingData['rad-network'] || '';
        hint.textContent = 'ابدأ الكتابة للبحث في شبكة THERA';
      } else {
        fLabel.textContent = 'اسم مركز الأشعة الخارجي';
        fInput.id = 'rad-external';
        fInput.placeholder = 'أدخل اسم الجهة...';
        fInput.value = existingData['rad-external'] || '';
        hint.textContent = 'جهة خارج شبكة THERA';
      }
    }

    fieldWrap.appendChild(fLabel);
    fieldWrap.appendChild(fInput);
    fieldWrap.appendChild(hint);
    dynamicWrap.appendChild(fieldWrap);
  }

  mSelect.addEventListener('change', renderSubDestinationField);
  renderSubDestinationField();
}

function showDrawerValidation(message, focusInputId = null) {
  const msgBox = document.getElementById('drawer-validation-msg');
  const msgText = document.getElementById('drawer-validation-text');
  if (msgBox && msgText) {
    msgText.textContent = message;
    msgBox.classList.remove('hidden');
    msgBox.classList.add('flex');
  }
  if (focusInputId) {
    const el = document.getElementById(focusInputId);
    if (el) el.focus();
  }
}

function hideDrawerValidation() {
  const msgBox = document.getElementById('drawer-validation-msg');
  if (msgBox) {
    msgBox.classList.add('hidden');
    msgBox.classList.remove('flex');
  }
}

function openContextualDrawer(type, existingItem = null) {
  currentDrawerType = type;
  activeEditingItemId = existingItem ? existingItem.id : null;
  const cfg = drawerConfig[type];
  if (!cfg) return;

  const drawer = document.getElementById('action-drawer');
  const backdrop = document.getElementById('drawer-backdrop');
  const title = document.getElementById('drawer-title');
  const icon = document.getElementById('drawer-icon');
  const sectionBTitle = document.getElementById('drawer-section-b-title');
  const containerA = document.getElementById('drawer-fields-container-a');

  title.textContent = cfg.title;
  icon.textContent = cfg.icon;
  if (sectionBTitle) sectionBTitle.textContent = 'جهة التنفيذ والإرسال';

  containerA.replaceChildren();
  hideDrawerValidation();

  const itemData = (existingItem && existingItem.data) ? existingItem.data : {};

  // Build Section A
  cfg.sectionA.forEach(field => {
    const fieldWrap = buildDrawerField(field, itemData[field.id]);
    containerA.appendChild(fieldWrap);
  });

  // Referral and follow-up share one action, with fields changing by type.
  if (type === 'referral') {
    const kindSelect = containerA.querySelector('#ref-kind');
    const referralOnly = ['ref-summary', 'ref-question', 'ref-relevant', 'ref-attachments'];
    const followupOnly = ['followup-period', 'followup-instructions', 'followup-internal-notes'];

    function setFieldVisibility(fieldId, visible) {
      const wrap = containerA.querySelector(`[data-field-id="${fieldId}"]`);
      if (wrap) wrap.classList.toggle('hidden', !visible);
    }

    function handleKindChange() {
      const isFollowup = kindSelect && kindSelect.value === 'متابعة';
      referralOnly.forEach(id => setFieldVisibility(id, !isFollowup));
      followupOnly.forEach(id => setFieldVisibility(id, isFollowup));
      updateDestinationFields('referral', itemData);
    }
    if (kindSelect) {
      kindSelect.addEventListener('change', handleKindChange);
      handleKindChange();
    }
  } else {
    updateDestinationFields(type, itemData);
  }

  drawer.classList.remove('drawer-closed');
  drawer.classList.add('drawer-open');
  backdrop.classList.remove('opacity-0', 'pointer-events-none');
  backdrop.classList.add('opacity-100', 'pointer-events-auto');

  setTimeout(() => {
    const firstInput = containerA.querySelector('input, textarea, select');
    if (firstInput) firstInput.focus();
  }, 150);
}

function closeContextualDrawer() {
  const drawer = document.getElementById('action-drawer');
  const backdrop = document.getElementById('drawer-backdrop');
  if (drawer) {
    drawer.classList.remove('drawer-open');
    drawer.classList.add('drawer-closed');
  }
  if (backdrop) {
    backdrop.classList.remove('opacity-100', 'pointer-events-auto');
    backdrop.classList.add('opacity-0', 'pointer-events-none');
  }
  hideDrawerValidation();
  activeEditingItemId = null;
}

function getDestinationData(type) {
  if (type === 'referral') {
    const kind = document.getElementById('ref-kind')?.value || 'إحالة';
    if (kind === 'متابعة') {
      return { destinationType: '', destinationName: '' };
    }
    const destType = document.getElementById('ref-dest-type')?.value || '';
    let destName = '';
    if (destType === 'طبيب من شبكة THERA') {
      destName = document.getElementById('ref-target-doc')?.value.trim() || '';
    } else if (destType === 'منشأة من شبكة THERA') {
      destName = document.getElementById('ref-target-fac')?.value.trim() || '';
    } else if (destType === 'جهة خارج THERA') {
      destName = document.getElementById('ref-target-ext')?.value.trim() || '';
    }
    return { destinationType: destType, destinationName: destName };
  }

  if (type === 'prescription') {
    const destType = document.getElementById('rx-dest-type')?.value || 'بدون تحديد جهة الآن';
    let destName = '';
    if (destType === 'صيدلية من شبكة THERA') {
      destName = document.getElementById('rx-pharmacy-network')?.value.trim() || '';
    } else if (destType === 'صيدلية خارجية') {
      destName = document.getElementById('rx-pharmacy-external')?.value.trim() || '';
    }
    return { destinationType: destType, destinationName: destName };
  }

  if (type === 'lab') {
    const destType = document.getElementById('lab-dest-type')?.value || 'بدون تحديد جهة الآن';
    let destName = '';
    if (destType === 'مختبر من شبكة THERA') {
      destName = document.getElementById('lab-network')?.value.trim() || '';
    } else if (destType === 'مختبر خارجي') {
      destName = document.getElementById('lab-external')?.value.trim() || '';
    }
    return { destinationType: destType, destinationName: destName };
  }

  if (type === 'radiology') {
    const destType = document.getElementById('rad-dest-type')?.value || 'بدون تحديد جهة الآن';
    let destName = '';
    if (destType === 'مركز أشعة من شبكة THERA') {
      destName = document.getElementById('rad-network')?.value.trim() || '';
    } else if (destType === 'مركز أشعة خارجي') {
      destName = document.getElementById('rad-external')?.value.trim() || '';
    }
    return { destinationType: destType, destinationName: destName };
  }

  return { destinationType: '', destinationName: '' };
}

function validateDestinationForSend(type) {
  if (type === 'referral') {
    const kind = document.getElementById('ref-kind')?.value || 'إحالة';
    if (kind === 'متابعة') return { isValid: true, destinationType: '', destinationName: '' };

    const destType = document.getElementById('ref-dest-type')?.value || '';
    let destName = '';
    let targetId = '';
    if (destType === 'طبيب من شبكة THERA') {
      targetId = 'ref-target-doc';
      destName = document.getElementById(targetId)?.value.trim() || '';
    } else if (destType === 'منشأة من شبكة THERA') {
      targetId = 'ref-target-fac';
      destName = document.getElementById(targetId)?.value.trim() || '';
    } else {
      targetId = 'ref-target-ext';
      destName = document.getElementById(targetId)?.value.trim() || '';
    }

    if (!destName) {
      return { isValid: false, message: 'اختر جهة التنفيذ قبل الإرسال', focusId: targetId };
    }
    return { isValid: true, destinationType: destType, destinationName: destName };
  }

  let selectId = type === 'prescription' ? 'rx-dest-type' : (type === 'lab' ? 'lab-dest-type' : 'rad-dest-type');
  const destType = document.getElementById(selectId)?.value || 'بدون تحديد جهة الآن';

  if (destType === 'بدون تحديد جهة الآن') {
    return { isValid: false, message: 'اختر جهة التنفيذ قبل الإرسال', focusId: selectId };
  }

  let subInputId = '';
  if (type === 'prescription') {
    subInputId = destType === 'صيدلية من شبكة THERA' ? 'rx-pharmacy-network' : 'rx-pharmacy-external';
  } else if (type === 'lab') {
    subInputId = destType === 'مختبر من شبكة THERA' ? 'lab-network' : 'lab-external';
  } else if (type === 'radiology') {
    subInputId = destType === 'مركز أشعة من شبكة THERA' ? 'rad-network' : 'rad-external';
  }

  const subVal = document.getElementById(subInputId)?.value.trim() || '';
  if (!subVal) {
    return { isValid: false, message: 'اختر جهة التنفيذ قبل الإرسال', focusId: subInputId };
  }

  return { isValid: true, destinationType: destType, destinationName: subVal };
}

function readAllDrawerData(type) {
  const cfg = drawerConfig[type];
  const data = {};
  if (cfg && cfg.sectionA) {
    cfg.sectionA.forEach(f => {
      const el = document.getElementById(f.id);
      if (el) data[f.id] = el.value.trim();
    });
  }
  if (type === 'referral') {
    const destType = document.getElementById('ref-dest-type');
    if (destType) data['ref-dest-type'] = destType.value;
    const doc = document.getElementById('ref-target-doc');
    if (doc) data['ref-target-doc'] = doc.value.trim();
    const fac = document.getElementById('ref-target-fac');
    if (fac) data['ref-target-fac'] = fac.value.trim();
    const ext = document.getElementById('ref-target-ext');
    if (ext) data['ref-target-ext'] = ext.value.trim();
  } else if (type === 'prescription') {
    const dt = document.getElementById('rx-dest-type');
    if (dt) data['rx-dest-type'] = dt.value;
    const net = document.getElementById('rx-pharmacy-network');
    if (net) data['rx-pharmacy-network'] = net.value.trim();
    const ext = document.getElementById('rx-pharmacy-external');
    if (ext) data['rx-pharmacy-external'] = ext.value.trim();
  } else if (type === 'lab') {
    const dt = document.getElementById('lab-dest-type');
    if (dt) data['lab-dest-type'] = dt.value;
    const net = document.getElementById('lab-network');
    if (net) data['lab-network'] = net.value.trim();
    const ext = document.getElementById('lab-external');
    if (ext) data['lab-external'] = ext.value.trim();
  } else if (type === 'radiology') {
    const dt = document.getElementById('rad-dest-type');
    if (dt) data['rad-dest-type'] = dt.value;
    const net = document.getElementById('rad-network');
    if (net) data['rad-network'] = net.value.trim();
    const ext = document.getElementById('rad-external');
    if (ext) data['rad-external'] = ext.value.trim();
  }
  return data;
}

function submitDrawerAction(mode) {
  if (!currentDrawerType) return;
  const cfg = drawerConfig[currentDrawerType];
  if (!cfg) return;

  hideDrawerValidation();
  const allData = readAllDrawerData(currentDrawerType);

  let resolvedName = '';
  let mainFocusTarget = null;

  if (currentDrawerType === 'prescription') {
    resolvedName = allData['rx-name'] || allData['rx-search'] || '';
    if (!resolvedName) {
      mainFocusTarget = document.getElementById('rx-name') || document.getElementById('rx-search');
    }
  } else if (currentDrawerType === 'lab') {
    resolvedName = allData['lab-test'] || '';
    if (!resolvedName) {
      mainFocusTarget = document.getElementById('lab-test');
    }
  } else if (currentDrawerType === 'radiology') {
    resolvedName = allData['rad-type'] || '';
    if (!resolvedName) {
      mainFocusTarget = document.getElementById('rad-type');
    }
  } else if (currentDrawerType === 'referral') {
    const kind = allData['ref-kind'] || 'إحالة';
    if (kind === 'متابعة') {
      resolvedName = allData['ref-reason'] || 'متابعة سريرية';
      if (!allData['ref-reason']) mainFocusTarget = document.getElementById('ref-reason');
      if (!allData['followup-period']) {
        showDrawerValidation('حدد التاريخ أو الفترة المقترحة للمتابعة', 'followup-period');
        return;
      }
    } else {
      resolvedName = allData['ref-target-doc'] || allData['ref-target-fac'] || allData['ref-target-ext'] || allData['ref-reason'] || 'إحالة طبية';
      if (!allData['ref-reason']) mainFocusTarget = document.getElementById('ref-reason');
      if (!allData['ref-summary']) {
        showDrawerValidation('اكتب الملخص السريري قبل حفظ الإحالة', 'ref-summary');
        return;
      }
    }
  }

  if (currentDrawerType === 'referral' && !allData['ref-reason']) {
    showDrawerValidation('اكتب سبب الإحالة أو المتابعة', 'ref-reason');
    return;
  }

  if (!resolvedName) {
    if (mainFocusTarget) mainFocusTarget.focus();
    showDrawerValidation('يرجى استكمال البيانات الأساسية للإجراء');
    return;
  }

  let finalWorkflowState = 'local_only';
  let finalDestType = '';
  let finalDestName = '';

  const isFollowupAction = currentDrawerType === 'referral' && (allData['ref-kind'] === 'متابعة');

  if (isFollowupAction) {
    finalWorkflowState = 'local_only';
    finalDestType = '';
    finalDestName = '';
  } else if (mode === 'send') {
    const validation = validateDestinationForSend(currentDrawerType);
    if (!validation.isValid) {
      showDrawerValidation(validation.message, validation.focusId);
      return;
    }
    const externalReferral = currentDrawerType === 'referral' && validation.destinationType === 'جهة خارج THERA';
    finalWorkflowState = externalReferral ? 'local_only' : 'send_requested';
    finalDestType = validation.destinationType;
    finalDestName = validation.destinationName;
  } else {
    finalWorkflowState = 'local_only';
    const destInfo = getDestinationData(currentDrawerType);
    finalDestType = destInfo.destinationType;
    finalDestName = destInfo.destinationName;
  }

  if (activeEditingItemId) {
    const idx = visitItems.findIndex(v => v.id === activeEditingItemId);
    if (idx !== -1) {
      visitItems[idx] = {
        ...visitItems[idx],
        name: resolvedName,
        data: allData,
        workflowState: finalWorkflowState,
        destinationType: finalDestType,
        destinationName: finalDestName
      };
    }
  } else {
    const newItem = {
      id: `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
      type: currentDrawerType,
      title: isFollowupAction ? 'متابعة سريرية' : cfg.itemLabel,
      name: resolvedName,
      data: allData,
      workflowState: finalWorkflowState,
      destinationType: finalDestType,
      destinationName: finalDestName
    };
    visitItems.push(newItem);
  }

  renderVisitItems();
  persistLocalDraft();
  closeContextualDrawer();

  if (isFollowupAction) {
    showToast('تمت إضافة المتابعة إلى الزيارة', 'check_circle');
  } else if (currentDrawerType === 'referral' && finalDestType === 'جهة خارج THERA') {
    showToast('تم حفظ الإحالة الخارجية كتسجيل نصي دون إرسال إلكتروني', 'save');
  } else if (mode === 'send') {
    showToast('تم تجهيز الطلب للإرسال إلى الجهة المحددة', 'send');
  } else {
    showToast('تم حفظ العنصر داخل الزيارة', 'save');
  }
}


let currentVitals = {};

function openVitalsModal() {
  const modal = document.getElementById('vitals-modal');
  if (!modal) return;
  modal.classList.remove('hidden');
  ['sys','dia','pulse','temp','spo2','weight','height'].forEach(k => {
    const el = document.getElementById(`vital-${k}`);
    if (el) el.value = currentVitals[k] || '';
  });
  recalcBMI();
}
function closeVitalsModal() { document.getElementById('vitals-modal')?.classList.add('hidden'); }
function recalcBMI() {
  const w = parseFloat(document.getElementById('vital-weight')?.value || '');
  const hcm = parseFloat(document.getElementById('vital-height')?.value || '');
  const bmi = document.getElementById('vital-bmi');
  if (!bmi) return;
  bmi.value = (w > 0 && hcm > 0) ? (w / Math.pow(hcm/100, 2)).toFixed(1) : '';
}
function saveVitals() {
  const keys = ['sys','dia','pulse','temp','spo2','weight','height'];
  const next = {};
  keys.forEach(k => next[k] = document.getElementById(`vital-${k}`)?.value.trim() || '');
  const hasAny = Object.values(next).some(Boolean);
  currentVitals = next;
  const empty = document.getElementById('vitals-empty-state');
  const grid = document.getElementById('vitals-values-grid');
  const label = document.getElementById('vitals-action-label');
  if (hasAny && grid) {
    const bmi = (parseFloat(next.weight) > 0 && parseFloat(next.height) > 0) ? (parseFloat(next.weight) / Math.pow(parseFloat(next.height)/100, 2)).toFixed(1) : '';
    const vals = [
      ['ضغط الدم', next.sys || next.dia ? `${next.sys || '—'}/${next.dia || '—'} mmHg` : '—'],
      ['النبض', next.pulse ? `${next.pulse} /دقيقة` : '—'],
      ['الحرارة', next.temp ? `${next.temp} °C` : '—'],
      ['SpO₂', next.spo2 ? `${next.spo2}%` : '—'],
      ['الوزن', next.weight ? `${next.weight} كغ` : '—'],
      ['الطول', next.height ? `${next.height} سم` : '—'],
      ['BMI', bmi || '—']
    ];
    grid.innerHTML = vals.map(([k,v]) => `<div class="p-2.5 rounded-lg bg-white border border-[#E2E8F0]"><div class="text-[10px] text-[#64748B]">${k}</div><div class="text-[12px] font-bold text-[#0B1020] mt-0.5" dir="auto">${v}</div></div>`).join('');
    grid.classList.remove('hidden');
    empty?.classList.add('hidden');
    if (label) label.textContent = 'تعديل العلامات الحيوية';
    showToast('تم حفظ العلامات الحيوية للزيارة', 'vital_signs');
  } else {
    grid?.classList.add('hidden');
    if (grid) grid.innerHTML = '';
    empty?.classList.remove('hidden');
    if (label) label.textContent = 'إضافة علامات حيوية';
  }
  closeVitalsModal();
}
['vital-weight','vital-height'].forEach(id => document.addEventListener('input', e => { if (e.target && e.target.id === id) recalcBMI(); }));

function openMedicalRecordModal() { document.getElementById('medical-record-modal')?.classList.remove('hidden'); }
function closeMedicalRecordModal() { document.getElementById('medical-record-modal')?.classList.add('hidden'); }
function openPreviousVisitModal() { document.getElementById('previous-visit-modal')?.classList.remove('hidden'); }
function closePreviousVisitModal() { document.getElementById('previous-visit-modal')?.classList.add('hidden'); }

function hideEndVisitValidation() {
  const box = document.getElementById('end-visit-validation');
  if (box) { box.classList.add('hidden'); box.classList.remove('flex'); }
}
function canEndVisit() {
  const assessment = document.getElementById('medical-assessment')?.value.trim() || '';
  const diagnosisCount = document.querySelectorAll('#diagnosis-list-container .diag-tag').length;
  return assessment.length > 0 || diagnosisCount > 0;
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeContextualDrawer();
    closeEndVisitModal();
    closeVitalsModal();
    closeMedicalRecordModal();
    closePreviousVisitModal();
  }
});

function openEndVisitModal() {
  hideEndVisitValidation();
  if (!canEndVisit()) {
    const box = document.getElementById('end-visit-validation');
    if (box) { box.classList.remove('hidden'); box.classList.add('flex'); box.scrollIntoView({behavior:'smooth', block:'center'}); }
    document.getElementById('medical-assessment')?.focus();
    showToast('يجب تسجيل تقييم طبي أو تشخيص واحد على الأقل قبل إنهاء الزيارة', 'error');
    return;
  }
  const modal = document.getElementById('end-visit-modal');
  if (modal) modal.classList.remove('hidden');
}

function closeEndVisitModal() {
  const modal = document.getElementById('end-visit-modal');
  if (modal) modal.classList.add('hidden');
}

function confirmEndVisit() {
  if (!canEndVisit()) { closeEndVisitModal(); openEndVisitModal(); return; }
  closeEndVisitModal();
  persistLocalDraft();
  const select = document.getElementById('visit-status-select');
  if (select) {
    if (!select.querySelector('option[value="completed"]')) {
      const opt = document.createElement('option'); opt.value='completed'; opt.textContent='مكتملة'; select.appendChild(opt);
    }
    select.value = 'completed';
    select.disabled = true;
  }
  const pill = document.getElementById('status-pill-display');
  const pillText = document.getElementById('status-pill-text');
  if (pill) pill.className = 'px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-full flex items-center gap-1';
  if (pillText) pillText.textContent = 'مكتملة';
  showToast('تم إنهاء الزيارة وحفظ السجل السريري', 'check_circle');
}

document.addEventListener('DOMContentLoaded', () => {
  const savedPin = localStorage.getItem('thera_sidebar_pinned');
  isPinned = savedPin !== null ? savedPin === 'true' : true;
  updateSidebarState();
  restoreLocalDraft();
});
</script>
<!-- END: Application Shell & Workflow Scripts -->
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>