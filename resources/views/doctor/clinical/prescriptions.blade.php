<!DOCTYPE html>

<html dir="rtl" lang="ar"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>@include('doctor.partials.assets')
<style>@layer base{html,body{margin:0;padding:0;font-family:'Tajawal',sans-serif;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
/* Refined legacy-style controls */
.thera-select{height:40px;background:#fff;border:1px solid #E2E8F0;border-radius:8px;color:#334155;font-size:13px;font-family:'Tajawal',sans-serif;box-shadow:0 1px 2px rgba(15,23,42,.02);transition:border-color .18s ease,box-shadow .18s ease,background-color .18s ease;}
.thera-select:hover{border-color:#CBD5E1;background:#fff;}
.thera-select:focus{outline:none;border-color:#0495F6;box-shadow:0 0 0 3px rgba(4,149,246,.10);background:#fff;}
.thera-filter-wrap .material-symbols-outlined{font-size:17px;}
.thera-reset-icon{width:40px;height:40px;padding:0;border:1px solid #E2E8F0;background:#fff;color:#64748B;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;transition:all .18s ease;}
.thera-reset-icon:hover{background:#F8FAFC;color:#0B1020;border-color:#CBD5E1;}
.thera-reset-icon:active{background:#F1F5F9;}
/* Custom filter dropdowns — same visual language as Patient Detail "المزيد" menu */
.thera-dropdown-btn{width:100%;height:40px;padding:0 12px;border:1px solid #E2E8F0;background:#fff;color:#334155;border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:10px;font-family:'Tajawal',sans-serif;font-size:13px;font-weight:500;box-shadow:0 1px 2px rgba(15,23,42,.03);transition:background-color .18s ease,border-color .18s ease,box-shadow .18s ease;cursor:pointer;}
.thera-dropdown-btn:hover{background:#F8FAFC;border-color:#CBD5E1;}
.thera-dropdown-btn:focus-visible,.thera-dropdown-btn[aria-expanded="true"]{outline:none;border-color:#0495F6;box-shadow:0 0 0 3px rgba(4,149,246,.10);background:#fff;}
.thera-dropdown-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:right;}
.thera-dropdown-arrow{font-size:17px!important;color:#64748B;transition:transform .18s ease;flex:none;}
.thera-dropdown-btn[aria-expanded="true"] .thera-dropdown-arrow{transform:rotate(180deg);}
.thera-dropdown-menu{position:absolute;top:calc(100% + 6px);right:0;width:100%;min-width:170px;padding:6px;background:#fff;border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 10px 28px rgba(15,23,42,.12);z-index:80;}
.thera-dropdown-option{width:100%;border:0;background:transparent;color:#334155;border-radius:8px;padding:8px 10px;text-align:right;font-family:'Tajawal',sans-serif;font-size:12px;font-weight:500;line-height:18px;cursor:pointer;transition:background-color .15s ease,color .15s ease;}
.thera-dropdown-option:hover{background:#F8FAFC;color:#0B1020;}
.thera-dropdown-option.is-selected{background:#F0F7FF;color:#0495F6;font-weight:600;}

</style>
<style>
@media print {
  body * { visibility: hidden !important; }
  #printPreviewModal, #printPreviewModal * { visibility: visible !important; }
  #printPreviewModal { position: absolute !important; inset: 0 !important; background: white !important; }
  #printPreviewActions { display: none !important; }
  #printPreviewSheet { box-shadow: none !important; border: none !important; max-width: none !important; width: 100% !important; }
}
</style><style>
.sidebar-scroll{overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none}.sidebar-scroll::-webkit-scrollbar{width:0;height:0;display:none}
#app-sidebar{width:268px;transition:width .28s cubic-bezier(.4,0,.2,1),box-shadow .28s ease}
#top-header{transition:right .28s cubic-bezier(.4,0,.2,1)}
#main-content-layout{transition:margin-right .28s cubic-bezier(.4,0,.2,1)}
#app-sidebar.sidebar-collapsed{width:72px}
#app-sidebar.sidebar-collapsed.sidebar-hovered{width:268px;box-shadow:-8px 0 25px rgba(0,0,0,.25)}
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-label,
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .group-heading,
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-text,
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-meta,
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) #sidebar-pin-toggle{display:none!important}
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-item{justify-content:center;padding-left:0;padding-right:0}
#app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-card-body{justify-content:center;padding-left:0;padding-right:0}
</style>
</head><body class="bg-[#F6F9FC] font-body-md text-on-surface antialiased"><aside class="fixed top-0 right-0 bottom-0 z-50 bg-[#16243E] border-l border-[rgba(255,255,255,0.08)] flex flex-col select-none" data-purpose="application-sidebar" id="app-sidebar">
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
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="العمل اليومي">العمل اليومي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">grid_view</span>
<span class="nav-label" data-i18n="الرئيسية">الرئيسية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">calendar_today</span>
<span class="nav-label" data-i18n="المواعيد">المواعيد</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">person_search</span>
<span class="nav-label" data-i18n="المرضى">المرضى</span>
</a>
</div>
<!-- Group 2: العمل الطبي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="العمل الطبي">العمل الطبي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">stethoscope</span>
<span class="nav-label" data-i18n="الزيارات">الزيارات</span>
</a>
<a aria-current="page" class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold bg-[#0495F6] text-white font-bold shadow-md shadow-[#0495F6]/20" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">prescriptions</span>
<span class="nav-label" data-i18n="الوصفات الطبية">الوصفات الطبية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label" data-i18n="طلبات المختبر">طلبات المختبر</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined flex-shrink-0 text-[20px]">radiology</span>
<span class="nav-label" data-i18n="طلبات الأشعة">طلبات الأشعة</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">assignment_turned_in</span>
<span class="nav-label" data-i18n="النتائج">النتائج</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">sync_alt</span>
<span class="nav-label" data-i18n="الإحالات والمتابعات">الإحالات والمتابعات</span>
</a>
</div>
<!-- Group 3: الإدارة -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="الإدارة">الإدارة</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined flex-shrink-0 text-[22px] text-white">receipt_long</span>
<span class="nav-label" data-i18n="الخدمات والأسعار">الخدمات والأسعار</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">schedule</span>
<span class="nav-label" data-i18n="الدوام والجدول">الدوام والجدول</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base font-semibold text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">account_balance_wallet</span>
<span class="nav-label" data-i18n="التحصيل والمستحقات">التحصيل والمستحقات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5" href="#">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">settings</span>
<span class="nav-label" data-i18n="الإعدادات">الإعدادات</span>
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
<span class="" data-i18n="الملف الشخصي">الملف الشخصي</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-slate-400">manage_accounts</span>
<span class="" data-i18n="إعدادات الحساب">إعدادات الحساب</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-emerald-400">radio_button_checked</span>
<span class="" data-i18n="تغيير حالة التوفر">تغيير حالة التوفر</span>
</button>
<div class="my-1 border-t border-[rgba(255,255,255,0.08)]"></div>
<button class="w-full text-right px-4 py-2 hover:bg-red-500/10 text-red-400 flex items-center gap-2" type="button">
<span class="material-symbols-outlined text-lg text-red-400">logout</span>
<span class="" data-i18n="تسجيل الخروج">تسجيل الخروج</span>
</button>
</div>
</footer>
</aside><header class="fixed top-0 left-0 h-16 bg-white border-b border-[#E2E8F0] z-40 px-6 flex items-center justify-between" data-purpose="top-navigation" id="top-header" style="right:268px;">
<!-- Right Section: Quick Add and Search -->
<div class="flex items-center gap-4">
<!-- Quick Add Dropdown Container -->
<div class="relative" id="quick-add-container">
<button class="h-10 px-4 bg-[#0495F6] hover:bg-[#0466F1] text-white rounded-xl font-bold flex items-center gap-2 shadow-sm transition-colors text-sm" id="quick-add-btn" type="button">
<span class="material-symbols-outlined text-xl">add</span>
<span class="" data-i18n="إضافة">إضافة</span>
<span class="material-symbols-outlined text-base" id="quick-add-arrow">expand_more</span>
</button>
<!-- Dropdown Menu -->
<div class="hidden absolute top-full right-0 mt-2 w-64 bg-white rounded-2xl shadow-dropdown border border-slate-200 py-2 z-50 text-slate-700 text-sm" id="quick-add-dropdown">
<div class="px-3 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">تشغيلي</div>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">person_add</span>
<span class="" data-i18n="إضافة مريض">إضافة مريض</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">calendar_today</span>
<span class="" data-i18n="موعد جديد">موعد جديد</span>
</button>
<div class="my-1.5 border-t border-slate-100"></div>
<div class="px-3 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">سريري</div>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">stethoscope</span>
<span class="" data-i18n="زيارة">زيارة</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">prescriptions</span>
<span class="" data-i18n="وصفة طبية">وصفة طبية</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">science</span>
<span class="" data-i18n="طلب مختبر">طلب مختبر</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" id="quick-add-rad-order-btn" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">radiology</span>
<span class="" data-i18n="طلب أشعة">طلب أشعة</span>
</button>
<button class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium" type="button">
<span class="material-symbols-outlined text-slate-500 text-lg">sync_alt</span>
<span class="" data-i18n="إحالة / متابعة">إحالة / متابعة</span>
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
</header><main class="pt-16 min-h-screen bg-[#F6F9FC]" data-purpose="workspace-content" id="main-content-layout" style="margin-right:268px;"><div class="flex flex-col w-full">
<title>الوصفات الطبية - THERA</title>
<!-- Toast Notification Container -->
<div aria-live="polite" class="fixed bottom-6 left-6 z-[999] flex flex-col gap-2 pointer-events-none" id="toastContainer"></div>
<!-- Main Viewport Content -->
<div class="p-space-lg max-w-[1600px] mx-auto w-full flex flex-col gap-space-lg">
<!-- Breadcrumb & Header Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<nav aria-label="مسار التصفح" class="flex items-center gap-1.5 font-metadata text-metadata text-outline">
<span class="hover:text-primary transition-colors cursor-pointer" data-i18n="العمل الطبي">العمل الطبي</span>
<span class="material-symbols-outlined text-[14px]">chevron_left</span>
<span class="text-on-surface font-semibold" data-i18n="الوصفات الطبية">الوصفات الطبية</span>
</nav>
<div class="flex items-baseline gap-3">
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight" data-i18n="الوصفات الطبية">الوصفات الطبية</h1>
<span class="font-label-sm text-label-sm text-outline hidden sm:inline-block">نظام إدارة الوصفات السريرية الآمنة</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">إدارة ومتابعة الوصفات الطبية المرتبطة بزيارات المرضى والتحقق السريري</p>
</div>
<div class="flex items-center gap-3">
<button class="h-10 px-5 bg-primary-container text-white font-label-md text-label-md rounded-lg flex items-center gap-2 hover:bg-primary transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container" id="btnOpenNewPrescription" type="button">
<span class="material-symbols-outlined text-[20px]">add</span>
<span>وصفة جديدة</span>
</button>
</div>
</div>
<!-- Quick Status Filter Pills (Distinct workflow states) -->
<div class="flex items-center gap-2 overflow-x-auto pb-1" id="statusTabsContainer">
<button class="status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-primary-container text-white shadow-sm" data-filter="all" type="button">
<span>الكل</span>
<span class="tab-count px-2 py-0.5 rounded-full text-xs bg-white/20 text-white font-numeric-ui">1</span>
</button>
<button class="status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-filter="draft" type="button">
<span>مسودات</span>
<span class="tab-count px-2 py-0.5 rounded-full text-xs bg-surface-container-highest text-on-surface-variant font-numeric-ui">0</span>
</button>
<button class="status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-filter="documented" type="button">
<span>موثقة</span>
<span class="tab-count px-2 py-0.5 rounded-full text-xs bg-surface-container-highest text-on-surface-variant font-numeric-ui">1</span>
</button>
<button class="status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-filter="send_ready" type="button">
<span>جاهزة للإرسال</span>
<span class="tab-count px-2 py-0.5 rounded-full text-xs bg-surface-container-highest text-on-surface-variant font-numeric-ui">0</span>
</button>
<button class="status-tab-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all flex items-center gap-2 bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-filter="send_requested" type="button">
<span>طلب إرسال</span>
<span class="tab-count px-2 py-0.5 rounded-full text-xs bg-surface-container-highest text-on-surface-variant font-numeric-ui">0</span>
</button>
</div>
<!-- Canonical record guidance -->
<div class="flex items-start gap-2.5 px-4 py-3 rounded-xl bg-blue-50/70 border border-blue-100 text-slate-600 text-[12px]">
<span class="material-symbols-outlined text-[18px] text-[#0495F6] mt-0.5">link</span>
<div><span class="font-semibold text-slate-700">السجل الأصلي للوصفة:</span> الوصفة التي تُنشأ من الزيارة أو من هذه الصفحة هي نفس السجل الطبي، ويؤدي فتح الزيارة إلى نفس السياق المرتبط دون إنشاء نسخة أخرى.</div>
</div>
<!-- Toolbar & Search Card -->
<div class="bg-surface-container-lowest rounded-xl p-4 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
<div class="relative flex-1">
<span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-outline">
<span class="material-symbols-outlined text-[20px]">search</span>
</span>
<input class="w-full h-10 pr-10 pl-4 font-body-md text-body-md text-on-surface bg-surface-container-low rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container transition-all placeholder:text-outline placeholder:font-body-md" id="searchInput" placeholder="ابحث باسم المريض أو رقم المريض... (P-10482)" type="text"/>
</div>
<div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
<!-- Status Filter Dropdown -->
<div class="thera-filter-wrap relative min-w-[150px] flex-1 sm:flex-initial">
<select class="hidden" id="filterStatus" aria-hidden="true" tabindex="-1">
<option value="all">حالة الوصفة (الكل)</option>
<option value="draft" data-i18n="مسودة">مسودة</option>
<option value="documented">موثقة</option>
<option value="send_ready">جاهزة للإرسال</option>
<option value="send_requested">طلب إرسال</option>
<option value="cancelled">ملغاة</option>
</select>
<button class="thera-dropdown-btn" type="button" data-dropdown-select="filterStatus" aria-expanded="false">
<span class="thera-dropdown-label">حالة الوصفة (الكل)</span>
<span class="material-symbols-outlined thera-dropdown-arrow">expand_more</span>
</button>
<div class="thera-dropdown-menu hidden" data-dropdown-menu="filterStatus">
<button type="button" class="thera-dropdown-option is-selected" data-value="all">حالة الوصفة (الكل)</button>
<button type="button" class="thera-dropdown-option" data-value="draft" data-i18n="مسودة">مسودة</button>
<button type="button" class="thera-dropdown-option" data-value="documented">موثقة</button>
<button type="button" class="thera-dropdown-option" data-value="send_ready">جاهزة للإرسال</button>
<button type="button" class="thera-dropdown-option" data-value="send_requested">طلب إرسال</button>
<button type="button" class="thera-dropdown-option" data-value="cancelled">ملغاة</button>
</div>
</div>
<!-- Destination Filter Dropdown -->
<div class="thera-filter-wrap relative min-w-[150px] flex-1 sm:flex-initial">
<select class="hidden" id="filterDestination" aria-hidden="true" tabindex="-1">
<option value="all">جهة الصرف (الكل)</option>
<option value="thera_network">شبكة THERA</option>
<option value="external">صيدلية خارجية</option>
<option value="unassigned">غير محددة</option>
</select>
<button class="thera-dropdown-btn" type="button" data-dropdown-select="filterDestination" aria-expanded="false">
<span class="thera-dropdown-label">جهة الصرف (الكل)</span>
<span class="material-symbols-outlined thera-dropdown-arrow">expand_more</span>
</button>
<div class="thera-dropdown-menu hidden" data-dropdown-menu="filterDestination">
<button type="button" class="thera-dropdown-option is-selected" data-value="all">جهة الصرف (الكل)</button>
<button type="button" class="thera-dropdown-option" data-value="thera_network">شبكة THERA</button>
<button type="button" class="thera-dropdown-option" data-value="external">صيدلية خارجية</button>
<button type="button" class="thera-dropdown-option" data-value="unassigned">غير محددة</button>
</div>
</div>
<!-- Date Filter Dropdown -->
<div class="thera-filter-wrap relative min-w-[138px] flex-1 sm:flex-initial">
<select class="hidden" id="filterDate" aria-hidden="true" tabindex="-1">
<option value="newest">الأحدث أولاً</option>
<option value="oldest">الأقدم أولاً</option>
<option value="last30">آخر 30 يوم</option>
</select>
<button class="thera-dropdown-btn" type="button" data-dropdown-select="filterDate" aria-expanded="false">
<span class="thera-dropdown-label">الأحدث أولاً</span>
<span class="material-symbols-outlined thera-dropdown-arrow">expand_more</span>
</button>
<div class="thera-dropdown-menu hidden" data-dropdown-menu="filterDate">
<button type="button" class="thera-dropdown-option is-selected" data-value="newest">الأحدث أولاً</button>
<button type="button" class="thera-dropdown-option" data-value="oldest">الأقدم أولاً</button>
<button type="button" class="thera-dropdown-option" data-value="last30">آخر 30 يوم</button>
</div>
</div>
<!-- Reset Button -->
<button aria-label="إعادة ضبط الفلاتر" class="thera-reset-icon" id="btnResetFilters" title="إعادة ضبط الفلاتر" type="button">
<span class="material-symbols-outlined text-[19px]">restart_alt</span>
</button>
</div>
</div>
<!-- Desktop Prescriptions Table Card (Hidden on Mobile) -->
<div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-right divide-y-0 text-on-surface">
<thead class="bg-surface-container-low font-label-md text-label-md text-outline">
<tr>
<th class="py-3.5 px-4 font-semibold" scope="col" data-i18n="المريض">المريض</th>
<th class="py-3.5 px-4 font-semibold" scope="col">الوصفة</th>
<th class="py-3.5 px-4 font-semibold" scope="col">تاريخ الوصفة</th>
<th class="py-3.5 px-4 font-semibold" scope="col">الزيارة</th>
<th class="py-3.5 px-4 font-semibold" scope="col">حالة الوصفة</th>
<th class="py-3.5 px-4 font-semibold" scope="col">جهة الصرف</th>
<th class="py-3.5 px-4 font-semibold" scope="col">حالة الصرف</th>
<th class="py-3.5 px-4 font-semibold text-center w-28" scope="col">إجراءات</th>
</tr>
</thead>
<tbody class="font-body-md text-body-md" id="prescriptionsTableBody">
<!-- Dynamic rows injected safely via script -->
</tbody>
</table>
</div>
<div class="hidden p-12 flex-col items-center justify-center text-center" id="desktopEmptyState">
<div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-outline mb-3">
<span class="material-symbols-outlined text-[32px]">prescriptions</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">لا توجد وصفات مطابقة للفلاتر الحالية</h3>
<p class="font-body-md text-body-md text-outline mt-1 mb-4">جرب تعديل مصطلح البحث أو إعادة تعيين عوامل التصفية المعروضة.</p>
<button class="h-9 px-4 bg-surface-container text-primary font-label-md text-label-md rounded-lg hover:bg-surface-container-high transition-colors" id="btnEmptyReset" type="button">إعادة ضبط الفلاتر</button>
</div>
</div>
<!-- Mobile Viewport Cards (Visible only on small screens) -->
<div class="md:hidden flex flex-col gap-3" id="mobileCardsContainer">
<!-- Dynamic cards injected via script -->
</div>
<div class="hidden md:hidden bg-surface-container-lowest p-8 rounded-xl text-center shadow-sm flex-col items-center justify-center" id="mobileEmptyState">
<span class="material-symbols-outlined text-[36px] text-outline mb-2">prescriptions</span>
<p class="font-headline-sm text-headline-sm font-semibold text-on-surface">لا توجد نتائج</p>
<p class="font-label-sm text-label-sm text-outline mt-1 mb-3">لا توجد وصفات طبية مطابقة لخيارات البحث الحالية</p>
<button class="h-9 px-4 bg-surface-container text-primary font-label-md text-label-md rounded-lg" id="btnMobileEmptyReset" type="button">إعادة ضبط الفلاتر</button>
</div>
</div>
<!-- DRAWER 1: Details Drawer (Prescription Details) -->
<div aria-labelledby="detailsDrawerTitle" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawerDetails" role="dialog">
<div class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm transition-opacity opacity-0" id="drawerDetailsBackdrop"></div>
<div class="fixed inset-y-0 left-0 max-w-full flex">
<div class="w-screen max-w-xl bg-surface-container-lowest shadow-2xl flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out" id="drawerDetailsPanel">
<!-- Drawer Header -->
<div class="px-6 py-4 bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[24px]">prescriptions</span>
<div>
<h2 class="font-headline-md text-headline-md font-bold text-on-surface" id="detailsDrawerTitle">تفاصيل الوصفة الطبية</h2>
<p class="font-metadata text-metadata text-outline" id="detailsPrescriptionCode">RX-10482-01</p>
</div>
</div>
<button class="p-2 text-outline hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors" id="btnCloseDetails" title="إغلاق" type="button">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Drawer Content Body -->
<div class="p-6 overflow-y-auto flex-1 flex flex-col gap-6 text-on-surface">
<!-- Patient Summary Card -->
<div class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-12 h-12 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-headline-sm" id="detailPatientAvatar">
                أ م
              </div>
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface" id="detailPatientName">أحمد محمود</span>
<span class="px-2 py-0.5 rounded font-numeric-ui text-label-sm bg-surface-container text-on-surface-variant" id="detailPatientId">P-10482</span>
</div>
<span class="font-label-sm text-label-sm text-outline" id="detailPatientDemographics">42 سنة • ذكر</span>
</div>
</div>
<div class="text-left font-metadata text-metadata text-outline">
<span id="detailVisitType">كشفية طب باطني</span>
<div id="detailPrescriptionDate">18 أكتوبر 2024</div>
</div>
</div>
<!-- Canonical Linkage -->
<div class="rounded-xl border border-blue-100 bg-blue-50/60 p-3.5 flex items-center justify-between gap-3">
<div class="flex items-start gap-2.5 min-w-0">
<span class="material-symbols-outlined text-[#0495F6] text-[19px] mt-0.5">link</span>
<div class="min-w-0">
<div class="text-[12px] font-semibold text-slate-700">الربط بالسجل الطبي</div>
<div class="text-[12px] text-slate-600 mt-0.5">الوصفة <span class="font-numeric-ui font-semibold text-slate-800" id="detailCanonicalRxId">--</span> مرتبطة بالزيارة <span class="font-numeric-ui font-semibold text-slate-800" id="detailVisitId">--</span></div>
<div class="text-[11px] text-slate-500 mt-0.5" id="detailVersionInfo">نفس السجل يظهر في الزيارة وملف المريض.</div>
</div>
</div>
<button class="shrink-0 h-8 px-3 rounded-lg bg-white border border-blue-200 text-[#0495F6] hover:bg-blue-50 text-[12px] font-semibold inline-flex items-center gap-1.5" id="btnOpenLinkedVisit" type="button">
<span class="material-symbols-outlined text-[16px]">open_in_new</span><span>فتح الزيارة</span>
</button>
</div>
<!-- Status Strip: Strict Separated Domains -->
<div class="grid grid-cols-3 gap-2">
<div class="p-3 bg-surface-container-low rounded-xl flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-outline">حالة الوصفة السريرية</span>
<div class="font-label-md text-label-md font-semibold" id="detailBadgeStatus">--</div>
</div>
<div class="p-3 bg-surface-container-low rounded-xl flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-outline">جهة الصرف</span>
<div class="font-label-md text-label-md font-semibold text-on-surface" id="detailPharmacyDestination">غير محددة</div>
</div>
<div class="p-3 bg-surface-container-low rounded-xl flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-outline">حالة الصرف (الصيدلية)</span>
<div class="font-label-md text-label-md text-outline" id="detailDispenseStatus">غير متوفر</div>
</div>
</div>
<!-- Medication Items Section -->
<div class="flex flex-col gap-3">
<div class="flex items-center justify-between">
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary">medication</span>
<span>الأدوية الموصوفة</span>
</h3>
<span class="font-label-sm text-label-sm text-outline" id="detailMedsCount">1 دواء</span>
</div>
<!-- Medication List Container -->
<div class="flex flex-col gap-3" id="detailMedsList">
<!-- Rendered dynamically -->
</div>
</div>
<!-- Clinical Safety Context Banner -->
<div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-2">
<span class="font-label-sm text-label-sm font-semibold text-outline">السياق الدوائي والحساسية (المسجل)</span>
<div class="flex flex-col gap-1.5 text-body-md text-on-surface-variant font-body-md">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">shield</span>
<span>الحساسية: لا توجد حساسية دوائية موثقة ضمن السجل المتاح للمريض.</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">history</span>
<span>الأدوية الحالية: لا توجد تعارضات نشطة ضمن سجل المريض الحالي.</span>
</div>
</div>
</div>
<!-- General Prescription Notes -->
<div class="flex flex-col gap-1.5 bg-surface-container-low rounded-xl p-4">
<span class="font-label-sm text-label-sm font-semibold text-outline">ملاحظات الطبيب وتوجيهات الصرف</span>
<p class="font-body-md text-body-md text-on-surface" id="detailNotes">لا توجد ملاحظات سريرية إضافية مسجلة لهذه الوصفة.</p>
</div>
<!-- Pharmacy Routing Configuration In Drawer -->
<div class="p-4 bg-primary-fixed/20 rounded-xl flex flex-col gap-2" id="detailPharmacySection">
<div class="flex items-center gap-2 text-on-primary-fixed font-headline-sm text-headline-sm font-semibold">
<span class="material-symbols-outlined text-[20px]">local_pharmacy</span>
<span>توجيه الصرف والإرسال</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">يمكنك الآن اختيار صيدلية معتمدة من شبكة THERA أو إدخال صيدلية خارجية وإرسال الطلب بشكل فوري.</p>
<div class="pt-2 flex gap-2">
<button class="h-9 px-4 bg-primary-container text-white rounded-lg font-label-md text-label-md hover:bg-primary transition-colors flex items-center gap-1.5 shadow-sm" id="btnDirectSendFromDetails" type="button">
<span class="material-symbols-outlined text-[18px]">send</span>
<span>تحديد جهة وإرسال</span>
</button>
</div>
</div>
</div>
<!-- Drawer Footer -->
<div class="px-6 py-4 bg-surface-container-low flex items-center justify-between">
<button class="h-10 px-4 bg-surface-container text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-container-high transition-colors" id="btnCloseDetailsBottom" type="button">
            إغلاق
          </button>
<div class="flex items-center gap-2">
<button class="h-10 px-4 bg-white border border-slate-200 text-slate-700 font-label-md text-label-md rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1.5" id="btnPrintPrescription" type="button">
<span class="material-symbols-outlined text-[18px]">print</span>
<span>معاينة وطباعة</span>
</button>
<button class="h-10 px-4 bg-surface-container text-primary font-label-md text-label-md rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1.5" id="btnEditInDrawer" type="button">
<span class="material-symbols-outlined text-[18px]">edit</span>
<span>تعديل نفس الوصفة</span>
</button>
</div>
</div>
</div>
</div>
</div>
<!-- DRAWER 2: New Prescription Builder -->
<div aria-labelledby="newPrescriptionTitle" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden hidden" id="drawerNewPrescription" role="dialog">
<div class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm transition-opacity opacity-0" id="drawerNewBackdrop"></div>
<div class="fixed inset-y-0 left-0 max-w-full flex">
<div class="w-screen max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out" id="drawerNewPanel">
<!-- Header -->
<div class="px-6 py-4 bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[24px]">assignment_add</span>
<div>
<h2 class="font-headline-md text-headline-md font-bold text-on-surface" id="newPrescriptionTitle">وصفة طبية جديدة</h2>
<p class="font-metadata text-metadata text-outline">إنشاء وصفة وربطها بالملف السريري للمريض</p>
</div>
</div>
<button class="p-2 text-outline hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors" id="btnCloseNewPrescription" title="إغلاق" type="button">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Scrollable Form Body -->
<form class="p-6 overflow-y-auto flex-1 flex flex-col gap-6 text-on-surface" id="newPrescriptionForm" onsubmit="return false;">
<!-- Patient Selection -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md font-semibold text-on-surface flex items-center gap-1">
<span data-i18n="المريض">المريض</span>
<span class="text-error">*</span>
</label>
<div class="relative">
<select class="thera-select w-full h-10 pr-3 pl-9 font-body-md text-body-md text-on-surface appearance-none cursor-pointer" id="inputPatientSelect">
<option data-age="42" data-gender="ذكر" data-name="أحمد محمود" value="P-10482">أحمد محمود (P-10482) - 42 سنة</option>
<option data-age="29" data-gender="أنثى" data-name="سارة خالد" value="P-10483">سارة خالد (P-10483) - 29 سنة</option>
<option data-age="56" data-gender="ذكر" data-name="عمر عبد العزيز" value="P-10484">عمر عبد العزيز (P-10484) - 56 سنة</option>
</select>
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8]">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<!-- Patient Safety Context Pill Banner -->
<div class="mt-1 bg-surface-container-low rounded-xl p-3 flex flex-col gap-1 text-on-surface-variant font-label-sm text-label-sm">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-tertiary">check_circle</span>
<span>الحساسية: لا توجد معلومات حساسية سلبية ظاهرة ضمن السجل المتاح للمريض.</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">info</span>
<span>الأدوية الحالية: لا توجد أدوية حالية مزمنة متضاربة مسجلة.</span>
</div>
</div>
</div>
<!-- Associated Visit Selection -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md font-semibold text-on-surface flex items-center gap-1">
<span>الزيارة المرتبطة</span>
<span class="font-metadata text-metadata text-outline font-normal">(اختياري)</span>
</label>
<div class="relative">
<select class="thera-select w-full h-10 pr-3 pl-9 font-body-md text-body-md text-on-surface appearance-none cursor-pointer" id="inputVisitSelect">
<option data-visit-id="VST-10521" value="كشفية طب باطني - اليوم">كشفية طب باطني - اليوم (VST-10521) — د. محمد أحمد</option>
<option data-visit-id="VST-10482" value="كشفية طب باطني - 18 أكتوبر 2024">كشفية طب باطني - 18 أكتوبر 2024 (VST-10482) — د. محمد أحمد</option>
<option data-visit-id="VST-10391" value="متابعة دورية - 10 أكتوبر 2024">متابعة دورية - 10 أكتوبر 2024 (VST-10391)</option>
<option data-visit-id="" value="زيارة منفصلة (بدون ربط بزيارة محددة)">بدون ربط بزيارة محددة</option>
</select>
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8]">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<p class="font-metadata text-metadata text-outline">إذا تم فتح مُنشئ الوصفة من داخل زيارة، يثبت النظام المريض والزيارة تلقائيًا ولا يسمح بتغييرهما أثناء تعديل نفس السجل.</p>
</div>
<div class="h-[1px] bg-surface-container-high"></div>
<!-- SECTION A: Medications Builder (تفاصيل الطلب) -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between">
<div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">أ. قائمة الأدوية</h3>
<p class="font-metadata text-metadata text-outline">أدخل تفاصيل الأدوية والجرعات بوضوح سريري</p>
</div>
<button class="h-8 px-3 bg-surface-container text-primary hover:bg-surface-container-high rounded-lg font-label-md text-label-md flex items-center gap-1.5 transition-colors" id="btnAddMedicationRow" type="button">
<span class="material-symbols-outlined text-[18px]">add</span>
<span>إضافة دواء آخر</span>
</button>
</div>
<div class="flex flex-col gap-4" id="medicationsContainer">
<!-- Medication rows injected here -->
</div>
<!-- Notes Field -->
<div class="flex flex-col gap-2 mt-2">
<label class="font-label-md text-label-md font-semibold text-on-surface" for="inputPrescriptionNotes">ملاحظات الوصفة السريرية</label>
<textarea class="w-full p-3 font-body-md text-body-md text-on-surface bg-surface-container-low rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container transition-all placeholder:text-outline" id="inputPrescriptionNotes" placeholder="أدخل أي ملاحظات خاصة للمريض أو الصيدلاني..." rows="2"></textarea>
</div>
</div>
<div class="h-[1px] bg-surface-container-high"></div>
<!-- SECTION B: Pharmacy Destination & Dispensing Routing (جهة الصرف والإرسال) -->
<div class="flex flex-col gap-4">
<div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">ب. جهة الصرف والإرسال</h3>
<p class="font-metadata text-metadata text-outline">حدد كيفية وتوجيه صرف الوصفة الطبية</p>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md font-semibold text-on-surface">طريقة الصرف</label>
<div class="relative">
<select class="thera-select w-full h-10 pr-3 pl-9 font-body-md text-body-md text-on-surface appearance-none cursor-pointer" id="inputDispenseMode">
<option value="none">بدون تحديد جهة الآن</option>
<option value="thera_network">صيدلية من شبكة THERA</option>
<option value="external">صيدلية خارجية</option>
</select>
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8]">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
</div>
<!-- THERA Network Pharmacy Option Dynamic Card -->
<div class="hidden flex-col gap-2 bg-surface-container-low p-4 rounded-xl" id="theraPharmacySection">
<label class="font-label-md text-label-md font-semibold text-on-surface flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-primary">store</span>
<span>اختر الصيدلية من شبكة THERA</span>
</label>
<div class="relative">
<select class="thera-select w-full h-10 pr-3 pl-9 font-body-md text-body-md text-on-surface appearance-none cursor-pointer" id="selectTheraPharmacy">
<option value="">اختر صيدلية من القائمة...</option>
<option value="صيدلية THERA المركزية - برج الشفاء">صيدلية THERA المركزية - برج الشفاء (متاح فوراً)</option>
<option value="صيدلية الأمل التخصصية - THERA">صيدلية الأمل التخصصية - فرع التخصصي</option>
<option value="صيدلية النور الرقمية - THERA Network">صيدلية النور الرقمية (خدمة التوصيل السريع)</option>
</select>
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8]">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<span class="font-metadata text-metadata text-outline">سيتم إرسال الوصفة إلكترونياً مع تنبيه لحظي لفريق الصيدلية.</span>
</div>
<!-- External Pharmacy Option Dynamic Card -->
<div class="hidden flex-col gap-2 bg-surface-container-low p-4 rounded-xl" id="externalPharmacySection">
<label class="font-label-md text-label-md font-semibold text-on-surface" for="inputExternalPharmacyName">اسم الصيدلية الخارجية</label>
<input class="w-full h-10 px-3 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container transition-all placeholder:text-outline" id="inputExternalPharmacyName" placeholder="مثال: صيدلية النهضة العامة - شارع الستين" type="text"/>
<span class="font-metadata text-metadata text-outline">جهة خارج شبكة THERA - يُتاح طباعة الوصفة أو إرسال باركود موثق.</span>
</div>
<div class="hidden text-error font-label-sm text-label-sm flex items-center gap-1" id="pharmacyValidationMessage">
<span class="material-symbols-outlined text-[16px]">error</span>
<span>يجب تحديد صيدلية قبل محاولة إرسال الوصفة.</span>
</div>
</div>
</form>
<!-- Builder Drawer Actions Footer -->
<div class="px-6 py-4 bg-surface-container-low flex flex-wrap items-center justify-between gap-3">
<button class="h-10 px-4 bg-surface-container text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-container-high transition-colors" id="btnCancelNewPrescription" type="button">
            إلغاء
          </button>
<div class="flex flex-wrap items-center gap-2">
<button class="h-10 px-3.5 bg-surface-container text-on-surface-variant font-label-md text-label-md rounded-lg hover:bg-surface-container-high hover:text-on-surface transition-colors flex items-center gap-1.5" id="btnSaveDraft" type="button">
<span class="material-symbols-outlined text-[18px]">save_as</span>
<span>حفظ كمسودة</span>
</button>
<button class="h-10 px-4 bg-tertiary-container text-white font-label-md text-label-md rounded-lg hover:opacity-95 transition-all flex items-center gap-1.5 shadow-sm" id="btnSaveDocumented" type="button">
<span class="material-symbols-outlined text-[18px]">verified</span>
<span>حفظ كـ موثقة</span>
</button>
<button class="h-10 px-4 bg-primary-container text-white font-label-md text-label-md rounded-lg hover:bg-primary transition-all flex items-center gap-1.5 shadow-sm" id="btnSendPrescription" type="button">
<span class="material-symbols-outlined text-[18px]">send</span>
<span>إرسال إلى الصيدلية</span>
</button>
</div>
</div>
</div>
</div>
</div>
<!-- PRINT PREVIEW MODAL -->
<div aria-modal="true" class="fixed inset-0 z-[100] hidden bg-[#091D30]/45 backdrop-blur-[2px] p-4 sm:p-6 overflow-y-auto" id="printPreviewModal" role="dialog">
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden relative" id="printPreviewSheet">
<div class="hidden absolute inset-0 pointer-events-none items-center justify-center z-0" id="printDraftWatermark"><div class="-rotate-12 text-[92px] font-bold text-slate-200/60">مسودة</div></div>
<div class="relative z-10">
<div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50" id="printPreviewActions">
<div><div class="text-[14px] font-bold text-slate-800">معاينة الوصفة قبل الطباعة</div><div class="text-[11px] text-slate-500">الطباعة من السجل الأصلي للوصفة</div></div>
<div class="flex items-center gap-2">
<button class="h-9 px-3.5 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-[12px] font-semibold inline-flex items-center gap-1.5" id="btnExecutePrint" type="button"><span class="material-symbols-outlined text-[17px]">print</span>طباعة</button>
<button class="w-9 h-9 rounded-lg hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center" id="btnClosePrintPreview" type="button"><span class="material-symbols-outlined text-[19px]">close</span></button>
</div>
</div>
<div class="p-8 sm:p-10">
<div class="flex items-start justify-between gap-6 pb-5 border-b border-slate-200">
<div class="flex items-center gap-3"><div class="w-12 h-12 rounded-xl bg-[#0495F6] text-white flex items-center justify-center"><span class="material-symbols-outlined text-[27px]">medical_services</span></div><div><div class="text-[20px] font-bold text-slate-900">THERA</div><div class="text-[12px] text-slate-500">وصفة طبية</div></div></div>
<div class="text-left text-[12px] text-slate-600"><div>رقم الوصفة: <span class="font-semibold text-slate-900" id="printRxId">--</span></div><div>التاريخ: <span id="printRxDate">--</span></div><div>الزيارة: <span id="printVisitId">--</span></div></div>
</div>
<div class="grid grid-cols-2 gap-4 py-5 border-b border-slate-200 text-[13px]"><div><div class="text-slate-500 text-[11px]">المريض</div><div class="font-bold text-slate-900 mt-1" id="printPatientName">--</div><div class="text-slate-600 mt-0.5" id="printPatientMeta">--</div></div><div><div class="text-slate-500 text-[11px]">الطبيب</div><div class="font-bold text-slate-900 mt-1">د. محمد أحمد</div><div class="text-slate-600 mt-0.5">استشاري طب باطني</div></div></div>
<div class="py-5"><div class="font-bold text-slate-800 mb-3">الأدوية والتعليمات</div><div class="space-y-3" id="printMedsList"></div></div>
<div class="mt-2 p-4 rounded-xl bg-slate-50 border border-slate-200"><div class="text-[11px] text-slate-500">ملاحظات الطبيب</div><div class="text-[13px] text-slate-700 mt-1" id="printNotes">--</div></div>
<div class="mt-8 pt-5 border-t border-slate-200 flex items-end justify-between"><div class="text-[11px] text-slate-500">تم إنشاء هذه الوثيقة من سجل THERA الأصلي. الختم/التوقيع يظهر فقط إذا كان معتمدًا ومهيأً في النظام.</div><div class="text-center min-w-[160px]"><div class="h-10 border-b border-slate-300"></div><div class="text-[11px] text-slate-500 mt-1">توقيع / ختم الطبيب</div></div></div>
</div>
</div>
</div>
</div>
<!-- CONTEXT MENU DROPDOWN (Global Context Target) -->
<div class="fixed hidden z-[90] w-56 bg-surface-container-lowest rounded-xl shadow-[0px_4px_16px_rgba(11,16,32,0.12)] py-1.5 text-on-surface font-body-md text-body-md select-none" id="rowContextMenu">
<button class="w-full text-right px-4 py-2 hover:bg-surface-container-low flex items-center gap-2.5 transition-colors text-on-surface" data-action="view-details" type="button">
<span class="material-symbols-outlined text-[18px] text-outline">visibility</span>
<span>عرض التفاصيل</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-surface-container-low flex items-center gap-2.5 transition-colors text-on-surface" data-action="open-visit" type="button">
<span class="material-symbols-outlined text-[18px] text-outline">stethoscope</span>
<span>فتح الزيارة المرتبطة</span>
</button>
<button class="w-full text-right px-4 py-2 hover:bg-surface-container-low flex items-center gap-2.5 transition-colors text-on-surface" data-action="print" type="button">
<span class="material-symbols-outlined text-[18px] text-outline">print</span>
<span>معاينة وطباعة</span>
</button>
<div class="h-[1px] bg-surface-container-high my-1"></div>
<button class="w-full text-right px-4 py-2 hover:bg-surface-container-low flex items-center gap-2.5 transition-colors text-primary font-semibold" data-action="assign-pharmacy" type="button">
<span class="material-symbols-outlined text-[18px] text-primary">local_pharmacy</span>
<span>تحديد جهة صرف وإرسال</span>
</button>
</div>
</div>
<script>
(function() {
  'use strict';

  // Standard Demo Seed
  const STORAGE_KEY = 'thera_prescriptions_doctor_demo_v2';

  const DEFAULT_PRESCRIPTIONS = [
    {
      id: 'RX-10482-01',
      patientId: 'P-10482',
      patientName: 'أحمد محمود',
      patientAge: '42',
      patientGender: 'ذكر',
      visitTitle: 'كشفية طب باطني',
      visitId: 'VST-10482',
      encounterId: 'ENC-10482',
      version: 1,
      doctorName: 'د. محمد أحمد',
      date: '18 أكتوبر 2024',
      isoDate: '2024-10-18',
      workflowState: 'documented', // 'draft' | 'documented' | 'send_ready' | 'send_requested' | 'cancelled'
      destinationMode: 'unassigned', // 'unassigned' | 'thera_network' | 'external'
      pharmacyName: 'غير محددة',
      dispenseStatus: 'غير متوفر', // Dispensing status is strictly managed by pharmacy domain
      notes: 'المريض يعاني من أعراض عرضية مستقرة، يوصى بالالتزام التام بالجرعات والمتابعة عند اللزوم.',
      medications: [
        {
          name: 'باراسيتامول (Paracetamol)',
          scientificName: 'Paracetamol',
          form: 'أقراص فموية',
          strength: '500 mg',
          dose: 'قرص واحد',
          frequency: 'كل 8 ساعات عند اللزوم',
          duration: '5 أيام',
          quantity: '20 قرص',
          instructions: 'يؤخذ بعد الطعام مع كوب ماء وفير.'
        }
      ]
    }
  ];

  // In-memory application state
  let prescriptions = [];
  let activeTabFilter = 'all';
  let activeSearchTerm = '';
  let activeStatusFilter = 'all';
  let activeDestinationFilter = 'all';
  let activeDateFilter = 'newest';
  let activeMenuPrescriptionId = null;
  let viewingPrescriptionId = null;
  let editingPrescriptionId = null;

  // DOM Elements cache
  const tableBody = document.getElementById('prescriptionsTableBody');
  const mobileContainer = document.getElementById('mobileCardsContainer');
  const desktopEmpty = document.getElementById('desktopEmptyState');
  const mobileEmpty = document.getElementById('mobileEmptyState');
  const statusTabsContainer = document.getElementById('statusTabsContainer');
  const searchInput = document.getElementById('searchInput');
  const filterStatus = document.getElementById('filterStatus');
  const filterDestination = document.getElementById('filterDestination');
  const filterDate = document.getElementById('filterDate');
  const btnResetFilters = document.getElementById('btnResetFilters');
  const btnEmptyReset = document.getElementById('btnEmptyReset');
  const btnMobileEmptyReset = document.getElementById('btnMobileEmptyReset');
  const toastContainer = document.getElementById('toastContainer');
  const rowContextMenu = document.getElementById('rowContextMenu');

  // Drawers Elements
  const drawerDetails = document.getElementById('drawerDetails');
  const drawerDetailsBackdrop = document.getElementById('drawerDetailsBackdrop');
  const drawerDetailsPanel = document.getElementById('drawerDetailsPanel');
  const btnCloseDetails = document.getElementById('btnCloseDetails');
  const btnCloseDetailsBottom = document.getElementById('btnCloseDetailsBottom');
  const btnDirectSendFromDetails = document.getElementById('btnDirectSendFromDetails');
  const btnEditInDrawer = document.getElementById('btnEditInDrawer');
  const btnPrintPrescription = document.getElementById('btnPrintPrescription');
  const btnOpenLinkedVisit = document.getElementById('btnOpenLinkedVisit');
  const printPreviewModal = document.getElementById('printPreviewModal');
  const btnClosePrintPreview = document.getElementById('btnClosePrintPreview');
  const btnExecutePrint = document.getElementById('btnExecutePrint');

  const drawerNewPrescription = document.getElementById('drawerNewPrescription');
  const drawerNewBackdrop = document.getElementById('drawerNewBackdrop');
  const drawerNewPanel = document.getElementById('drawerNewPanel');
  const btnOpenNewPrescription = document.getElementById('btnOpenNewPrescription');
  const btnCloseNewPrescription = document.getElementById('btnCloseNewPrescription');
  const btnCancelNewPrescription = document.getElementById('btnCancelNewPrescription');
  const btnAddMedicationRow = document.getElementById('btnAddMedicationRow');
  const medicationsContainer = document.getElementById('medicationsContainer');
  const inputDispenseMode = document.getElementById('inputDispenseMode');
  const theraPharmacySection = document.getElementById('theraPharmacySection');
  const externalPharmacySection = document.getElementById('externalPharmacySection');
  const selectTheraPharmacy = document.getElementById('selectTheraPharmacy');
  const inputExternalPharmacyName = document.getElementById('inputExternalPharmacyName');
  const pharmacyValidationMsg = document.getElementById('pharmacyValidationMessage');

  const btnSaveDraft = document.getElementById('btnSaveDraft');
  const btnSaveDocumented = document.getElementById('btnSaveDocumented');
  const btnSendPrescription = document.getElementById('btnSendPrescription');

  // Initialize
  function init() {
    loadState();
    setupEventListeners();
    render();
  }

  function loadState() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        prescriptions = JSON.parse(stored).map((p, idx) => ({...p, visitId: p.visitId ?? (idx === 0 ? 'VST-10482' : ''), version: p.version || 1}));
      } else {
        prescriptions = [...DEFAULT_PRESCRIPTIONS];
        saveState();
      }
    } catch (e) {
      prescriptions = [...DEFAULT_PRESCRIPTIONS];
    }
  }

  function saveState() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(prescriptions));
    } catch (e) {
      // Storage unavailable fallback
    }
  }

  // Safe Toast
  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg text-white font-label-md text-label-md transition-all duration-300 transform translate-y-2 opacity-0';
    
    if (type === 'success') {
      toast.classList.add('bg-tertiary');
    } else if (type === 'error') {
      toast.classList.add('bg-error');
    } else {
      toast.classList.add('bg-primary-container');
    }

    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined text-[20px]';
    icon.textContent = type === 'error' ? 'error' : (type === 'success' ? 'check_circle' : 'info');

    const text = document.createElement('span');
    text.textContent = message;

    toast.appendChild(icon);
    toast.appendChild(text);
    toastContainer.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.remove('translate-y-2', 'opacity-0');
    });

    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-2');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3800);
  }

  // Helpers for Status Badges
  function getWorkflowBadge(state) {
    const badge = document.createElement('span');
    badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold';
    
    switch (state) {
      case 'documented':
        badge.classList.add('bg-tertiary-fixed', 'text-on-tertiary-fixed');
        badge.textContent = 'موثقة';
        break;
      case 'draft':
        badge.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
        badge.textContent = 'مسودة';
        break;
      case 'send_ready':
        badge.classList.add('bg-primary-fixed', 'text-on-primary-fixed');
        badge.textContent = 'جاهزة للإرسال';
        break;
      case 'send_requested':
        badge.classList.add('bg-secondary-fixed', 'text-on-secondary-fixed');
        badge.textContent = 'طلب إرسال';
        break;
      case 'cancelled':
        badge.classList.add('bg-error-container', 'text-on-error-container');
        badge.textContent = 'ملغاة';
        break;
      default:
        badge.classList.add('bg-surface-container', 'text-on-surface');
        badge.textContent = state;
    }
    return badge;
  }

  function getDispenseBadge(status) {
    const badge = document.createElement('span');
    badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-low text-outline';
    badge.textContent = status || 'غير متوفر';
    return badge;
  }

  // Filter & Counts Pipeline
  function computeFilteredList() {
    return prescriptions.filter(item => {
      // Tab Filter
      if (activeTabFilter !== 'all' && item.workflowState !== activeTabFilter) {
        return false;
      }
      // Status Filter Dropdown
      if (activeStatusFilter !== 'all' && item.workflowState !== activeStatusFilter) {
        return false;
      }
      // Destination Filter Dropdown
      if (activeDestinationFilter !== 'all') {
        if (activeDestinationFilter === 'unassigned' && item.destinationMode !== 'unassigned') return false;
        if (activeDestinationFilter === 'thera_network' && item.destinationMode !== 'thera_network') return false;
        if (activeDestinationFilter === 'external' && item.destinationMode !== 'external') return false;
      }
      // Search term
      if (activeSearchTerm.trim() !== '') {
        const query = activeSearchTerm.trim().toLowerCase();
        const matchesName = item.patientName.toLowerCase().includes(query);
        const matchesId = item.patientId.toLowerCase().includes(query);
        const matchesCode = (item.id || '').toLowerCase().includes(query);
        if (!matchesName && !matchesId && !matchesCode) {
          return false;
        }
      }
      return true;
    }).sort((a, b) => {
      if (activeDateFilter === 'oldest') {
        return (a.isoDate || '').localeCompare(b.isoDate || '');
      }
      // Default: newest
      return (b.isoDate || '').localeCompare(a.isoDate || '');
    });
  }

  function updateCounts() {
    const counts = {
      all: prescriptions.length,
      draft: prescriptions.filter(p => p.workflowState === 'draft').length,
      documented: prescriptions.filter(p => p.workflowState === 'documented').length,
      send_ready: prescriptions.filter(p => p.workflowState === 'send_ready').length,
      send_requested: prescriptions.filter(p => p.workflowState === 'send_requested').length
    };

    const tabButtons = statusTabsContainer.querySelectorAll('.status-tab-btn');
    tabButtons.forEach(btn => {
      const filterKey = btn.getAttribute('data-filter');
      const countSpan = btn.querySelector('.tab-count');
      if (countSpan && counts[filterKey] !== undefined) {
        countSpan.textContent = counts[filterKey];
      }
    });
  }

  // Render Table & Cards
  function render() {
    updateCounts();
    const filtered = computeFilteredList();

    // Table render (safe DOM)
    tableBody.replaceChildren();
    if (filtered.length === 0) {
      desktopEmpty.classList.remove('hidden');
      desktopEmpty.classList.add('flex');
    } else {
      desktopEmpty.classList.add('hidden');
      desktopEmpty.classList.remove('flex');

      filtered.forEach(rx => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-container-low/60 transition-colors h-16 group';

        // Patient Cell
        const tdPatient = document.createElement('td');
        tdPatient.className = 'py-3 px-4';
        const pWrapper = document.createElement('div');
        pWrapper.className = 'flex items-center gap-3';
        
        const avatar = document.createElement('div');
        avatar.className = 'w-9 h-9 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-xs shrink-0';
        avatar.textContent = rx.patientName.split(' ').map(n => n[0]).slice(0, 2).join(' ');

        const infoCol = document.createElement('div');
        infoCol.className = 'flex flex-col';

        const nameLine = document.createElement('div');
        nameLine.className = 'flex items-center gap-2';

        const nameSpan = document.createElement('span');
        nameSpan.className = 'font-label-md text-label-md font-semibold text-on-surface';
        nameSpan.textContent = rx.patientName;

        const idBadge = document.createElement('span');
        idBadge.className = 'px-1.5 py-0.5 rounded font-numeric-ui text-[11px] bg-surface-container text-outline';
        idBadge.textContent = rx.patientId;

        nameLine.appendChild(nameSpan);
        nameLine.appendChild(idBadge);

        const subSpan = document.createElement('span');
        subSpan.className = 'font-metadata text-metadata text-outline';
        subSpan.textContent = `${rx.patientAge} سنة • ${rx.patientGender}`;

        infoCol.appendChild(nameLine);
        infoCol.appendChild(subSpan);
        pWrapper.appendChild(avatar);
        pWrapper.appendChild(infoCol);
        tdPatient.appendChild(pWrapper);

        // Prescription Cell
        const tdRx = document.createElement('td');
        tdRx.className = 'py-3 px-4';
        const rxBox = document.createElement('div');
        rxBox.className = 'flex items-center gap-2 text-on-surface font-label-md text-label-md';
        const rxIcon = document.createElement('span');
        rxIcon.className = 'material-symbols-outlined text-primary text-[18px]';
        rxIcon.textContent = 'prescriptions';
        const rxTitle = document.createElement('span');
        rxTitle.textContent = rx.medications && rx.medications.length ? rx.medications[0].name : 'وصفة طبية';
        rxBox.appendChild(rxIcon);
        rxBox.appendChild(rxTitle);
        tdRx.appendChild(rxBox);

        // Date Cell
        const tdDate = document.createElement('td');
        tdDate.className = 'py-3 px-4 font-numeric-ui text-body-md text-on-surface';
        tdDate.textContent = rx.date;

        // Visit Cell
        const tdVisit = document.createElement('td');
        tdVisit.className = 'py-3 px-4';
        const vTitle = document.createElement('span');
        vTitle.className = 'font-label-md text-label-md text-on-surface block';
        vTitle.textContent = rx.visitTitle;
        const vDoctor = document.createElement('span');
        vDoctor.className = 'font-metadata text-metadata text-outline block';
        vDoctor.textContent = `${rx.doctorName}${rx.visitId ? ' • ' + rx.visitId : ''}`;
        tdVisit.appendChild(vTitle);
        tdVisit.appendChild(vDoctor);

        // Prescription Status Cell
        const tdStatus = document.createElement('td');
        tdStatus.className = 'py-3 px-4';
        tdStatus.appendChild(getWorkflowBadge(rx.workflowState));

        // Destination Cell
        const tdDest = document.createElement('td');
        tdDest.className = 'py-3 px-4 font-body-md text-body-md';
        if (rx.destinationMode === 'unassigned') {
          tdDest.className += ' text-outline';
          tdDest.textContent = 'غير محددة';
        } else {
          tdDest.className += ' text-on-surface font-medium';
          tdDest.textContent = rx.pharmacyName;
        }

        // Dispensing Status Cell
        const tdDispense = document.createElement('td');
        tdDispense.className = 'py-3 px-4';
        tdDispense.appendChild(getDispenseBadge(rx.dispenseStatus));

        // Actions Cell
        const tdActions = document.createElement('td');
        tdActions.className = 'py-3 px-4 text-center';
        const actionsWrap = document.createElement('div');
        actionsWrap.className = 'flex items-center justify-center gap-1';

        const btnDetails = document.createElement('button');
        btnDetails.type = 'button';
        btnDetails.className = 'px-2.5 py-1 text-primary hover:bg-surface-container rounded-lg font-label-md text-label-md font-semibold transition-colors';
        btnDetails.textContent = 'عرض التفاصيل';
        btnDetails.addEventListener('click', () => openPrescriptionDetails(rx.id));

        const btnMenu = document.createElement('button');
        btnMenu.type = 'button';
        btnMenu.className = 'p-1 text-outline hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors';
        btnMenu.title = 'خيارات إضافية';
        const menuIcon = document.createElement('span');
        menuIcon.className = 'material-symbols-outlined text-[20px]';
        menuIcon.textContent = 'more_vert';
        btnMenu.appendChild(menuIcon);

        btnMenu.addEventListener('click', (e) => {
          e.stopPropagation();
          openContextMenu(e, rx.id);
        });

        actionsWrap.appendChild(btnDetails);
        actionsWrap.appendChild(btnMenu);
        tdActions.appendChild(actionsWrap);

        tr.appendChild(tdPatient);
        tr.appendChild(tdRx);
        tr.appendChild(tdDate);
        tr.appendChild(tdVisit);
        tr.appendChild(tdStatus);
        tr.appendChild(tdDest);
        tr.appendChild(tdDispense);
        tr.appendChild(tdActions);

        tableBody.appendChild(tr);
      });
    }

    // Mobile render
    mobileContainer.replaceChildren();
    if (filtered.length === 0) {
      mobileEmpty.classList.remove('hidden');
      mobileEmpty.classList.add('flex');
    } else {
      mobileEmpty.classList.add('hidden');
      mobileEmpty.classList.remove('flex');

      filtered.forEach(rx => {
        const card = document.createElement('div');
        card.className = 'bg-surface-container-lowest p-4 rounded-xl shadow-sm flex flex-col gap-3';

        // Top Row: Patient Info & Details Trigger
        const topRow = document.createElement('div');
        topRow.className = 'flex items-center justify-between';

        const pWrap = document.createElement('div');
        pWrap.className = 'flex items-center gap-2.5';
        const av = document.createElement('div');
        av.className = 'w-9 h-9 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-xs';
        av.textContent = rx.patientName.split(' ').map(n => n[0]).slice(0, 2).join(' ');

        const pMeta = document.createElement('div');
        pMeta.className = 'flex flex-col';
        const pName = document.createElement('span');
        pName.className = 'font-label-md text-label-md font-semibold text-on-surface';
        pName.textContent = rx.patientName;
        const pId = document.createElement('span');
        pId.className = 'font-metadata text-metadata text-outline';
        pId.textContent = `${rx.patientId} • ${rx.patientAge} سنة`;
        pMeta.appendChild(pName);
        pMeta.appendChild(pId);
        pWrap.appendChild(av);
        pWrap.appendChild(pMeta);

        const cardMenuBtn = document.createElement('button');
        cardMenuBtn.type = 'button';
        cardMenuBtn.className = 'p-1.5 text-outline hover:text-on-surface';
        cardMenuBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">more_vert</span>';
        cardMenuBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          openContextMenu(e, rx.id);
        });

        topRow.appendChild(pWrap);
        topRow.appendChild(cardMenuBtn);

        // Mid Row: Meds & Visit
        const midRow = document.createElement('div');
        midRow.className = 'bg-surface-container-low p-2.5 rounded-lg flex flex-col gap-1';
        const medLine = document.createElement('div');
        medLine.className = 'flex items-center gap-1.5 text-on-surface font-label-md text-label-md';
        medLine.innerHTML = `<span class="material-symbols-outlined text-primary text-[16px]">prescriptions</span><span>${rx.medications && rx.medications.length ? rx.medications[0].name : 'وصفة طبية'}</span>`;
        
        const visitLine = document.createElement('div');
        visitLine.className = 'font-metadata text-metadata text-outline flex items-center justify-between';
        visitLine.innerHTML = `<span>${rx.visitTitle}</span><span>${rx.date}</span>`;
        midRow.appendChild(medLine);
        midRow.appendChild(visitLine);

        // Status Row
        const statusRow = document.createElement('div');
        statusRow.className = 'flex items-center justify-between pt-1';
        
        const badgesWrap = document.createElement('div');
        badgesWrap.className = 'flex items-center gap-2';
        badgesWrap.appendChild(getWorkflowBadge(rx.workflowState));
        badgesWrap.appendChild(getDispenseBadge(rx.dispenseStatus));

        const actionBtn = document.createElement('button');
        actionBtn.type = 'button';
        actionBtn.className = 'px-3 py-1 bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md rounded-lg';
        actionBtn.textContent = 'عرض التفاصيل';
        actionBtn.addEventListener('click', () => openPrescriptionDetails(rx.id));

        statusRow.appendChild(badgesWrap);
        statusRow.appendChild(actionBtn);

        card.appendChild(topRow);
        card.appendChild(midRow);
        card.appendChild(statusRow);

        mobileContainer.appendChild(card);
      });
    }
  }

  // Open Details Drawer
  function openPrescriptionDetails(prescriptionId) {
    const rx = prescriptions.find(item => item.id === prescriptionId);
    if (!rx) return;
    viewingPrescriptionId = rx.id;

    // Populate drawer elements safely
    document.getElementById('detailsPrescriptionCode').textContent = rx.id;
    document.getElementById('detailPatientName').textContent = rx.patientName;
    document.getElementById('detailPatientId').textContent = rx.patientId;
    document.getElementById('detailPatientDemographics').textContent = `${rx.patientAge} سنة • ${rx.patientGender}`;
    document.getElementById('detailVisitType').textContent = rx.visitTitle;
    document.getElementById('detailPrescriptionDate').textContent = rx.date;
    document.getElementById('detailCanonicalRxId').textContent = rx.id;
    document.getElementById('detailVisitId').textContent = rx.visitId || 'غير مرتبطة بزيارة';
    document.getElementById('detailVersionInfo').textContent = rx.version && rx.version > 1 ? `نفس السجل الأصلي • الإصدار ${rx.version} بعد تعديل موثق` : 'نفس السجل يظهر في الزيارة وملف المريض.';
    btnOpenLinkedVisit.classList.toggle('hidden', !rx.visitId);
    document.getElementById('detailPatientAvatar').textContent = rx.patientName.split(' ').map(n => n[0]).slice(0, 2).join(' ');

    // Status Badges
    const badgeWrap = document.getElementById('detailBadgeStatus');
    badgeWrap.replaceChildren(getWorkflowBadge(rx.workflowState));

    document.getElementById('detailPharmacyDestination').textContent = rx.destinationMode === 'unassigned' ? 'غير محددة' : rx.pharmacyName;
    document.getElementById('detailDispenseStatus').textContent = rx.dispenseStatus || 'غير متوفر';

    // Medications List
    const medsList = document.getElementById('detailMedsList');
    medsList.replaceChildren();

    if (!rx.medications || rx.medications.length === 0) {
      const emptyNotice = document.createElement('div');
      emptyNotice.className = 'p-4 rounded-xl bg-surface-container-low text-on-surface-variant font-body-md text-body-md';
      emptyNotice.textContent = 'تفاصيل الأدوية غير متاحة ضمن بيانات النموذج الحالي للسجل التاريخي';
      medsList.appendChild(emptyNotice);
      document.getElementById('detailMedsCount').textContent = '0 دواء';
    } else {
      document.getElementById('detailMedsCount').textContent = `${rx.medications.length} دواء`;
      rx.medications.forEach((med, idx) => {
        const item = document.createElement('div');
        item.className = 'p-4 rounded-xl bg-surface-container-low flex flex-col gap-2';

        const head = document.createElement('div');
        head.className = 'flex items-center justify-between';
        const nameBox = document.createElement('div');
        nameBox.className = 'flex items-center gap-2';
        const num = document.createElement('span');
        num.className = 'w-6 h-6 rounded-full bg-surface-container flex items-center justify-center font-numeric-ui text-xs font-bold text-on-surface-variant';
        num.textContent = (idx + 1).toString();
        const title = document.createElement('span');
        title.className = 'font-label-md text-label-md font-bold text-on-surface';
        title.textContent = med.name;
        nameBox.appendChild(num);
        nameBox.appendChild(title);

        const formBadge = document.createElement('span');
        formBadge.className = 'font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container text-on-surface-variant';
        formBadge.textContent = med.form || 'قرص';
        head.appendChild(nameBox);
        head.appendChild(formBadge);

        // Dose Details Grid
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 font-label-sm text-label-sm text-on-surface-variant';
        
        grid.innerHTML = `
          <div><span class="text-outline block text-[11px]">التركيز:</span>${med.strength || '--'}</div><div><span class="text-outline block text-[11px]">الجرعة:</span>${med.dose || '--'}</div>
          <div><span class="text-outline block text-[11px]">التكرار:</span>${med.frequency || '--'}</div>
          <div><span class="text-outline block text-[11px]">المدة:</span>${med.duration || '--'}</div>
        `;

        if (med.instructions) {
          const inst = document.createElement('div');
          inst.className = 'mt-1 pt-1 font-metadata text-metadata text-outline border-t border-surface-container-high/40';
          inst.textContent = `التعليمات: ${med.instructions}`;
          item.appendChild(head);
          item.appendChild(grid);
          item.appendChild(inst);
        } else {
          item.appendChild(head);
          item.appendChild(grid);
        }

        medsList.appendChild(item);
      });
    }

    // Notes
    document.getElementById('detailNotes').textContent = rx.notes || 'لا توجد ملاحظات سريرية إضافية مسجلة لهذه الوصفة.';

    // Show Drawer
    drawerDetails.classList.remove('hidden');
    requestAnimationFrame(() => {
      drawerDetailsBackdrop.classList.remove('opacity-0');
      drawerDetailsPanel.classList.remove('-translate-x-full');
    });
  }

  function closePrescriptionDetails() {
    drawerDetailsBackdrop.classList.add('opacity-0');
    drawerDetailsPanel.classList.add('-translate-x-full');
    setTimeout(() => {
      drawerDetails.classList.add('hidden');
      viewingPrescriptionId = null;
    }, 300);
  }

  // Open Context Menu
  function openContextMenu(e, prescriptionId) {
    activeMenuPrescriptionId = prescriptionId;
    const rect = e.currentTarget.getBoundingClientRect();
    
    // Position safely within viewport
    let top = rect.bottom + window.scrollY + 4;
    let right = window.innerWidth - rect.left - window.scrollX;

    rowContextMenu.style.top = `${top}px`;
    rowContextMenu.style.right = `${right}px`;
    rowContextMenu.classList.remove('hidden');
  }

  function closeContextMenu() {
    rowContextMenu.classList.add('hidden');
    activeMenuPrescriptionId = null;
  }

  // New Prescription Drawer Actions
  function openNewPrescriptionDrawer(prefillPatient = null) {
    resetNewPrescriptionForm();
    editingPrescriptionId = null;
    document.getElementById('newPrescriptionTitle').textContent = 'وصفة طبية جديدة';
    const patientSelect = document.getElementById('inputPatientSelect');
    const visitSelect = document.getElementById('inputVisitSelect');
    patientSelect.disabled = false;
    visitSelect.disabled = false;
    if (prefillPatient) patientSelect.value = prefillPatient;

    drawerNewPrescription.classList.remove('hidden');
    requestAnimationFrame(() => {
      drawerNewBackdrop.classList.remove('opacity-0');
      drawerNewPanel.classList.remove('-translate-x-full');
    });
  }

  function openEditPrescriptionDrawer(rx) {
    if (!rx) return;
    resetNewPrescriptionForm();
    editingPrescriptionId = rx.id;
    document.getElementById('newPrescriptionTitle').textContent = `تعديل الوصفة ${rx.id}`;
    const patientSelect = document.getElementById('inputPatientSelect');
    const visitSelect = document.getElementById('inputVisitSelect');
    patientSelect.value = rx.patientId;
    patientSelect.disabled = true;
    const matchingVisit = Array.from(visitSelect.options).find(o => (o.getAttribute('data-visit-id') || '') === (rx.visitId || ''));
    if (matchingVisit) visitSelect.value = matchingVisit.value;
    visitSelect.disabled = !!rx.visitId;
    document.getElementById('inputPrescriptionNotes').value = rx.notes || '';
    medicationsContainer.replaceChildren();
    (rx.medications || []).forEach(m => addMedicationRow(m));
    if (!rx.medications || rx.medications.length === 0) addMedicationRow();
    inputDispenseMode.value = rx.destinationMode || 'none';
    inputDispenseMode.dispatchEvent(new Event('change'));
    if (rx.destinationMode === 'thera_network') selectTheraPharmacy.value = rx.pharmacyName || '';
    if (rx.destinationMode === 'external') inputExternalPharmacyName.value = rx.pharmacyName || '';
    drawerNewPrescription.classList.remove('hidden');
    requestAnimationFrame(() => {
      drawerNewBackdrop.classList.remove('opacity-0');
      drawerNewPanel.classList.remove('-translate-x-full');
    });
  }

  function closeNewPrescriptionDrawer() {
    drawerNewBackdrop.classList.add('opacity-0');
    drawerNewPanel.classList.add('-translate-x-full');
    setTimeout(() => {
      drawerNewPrescription.classList.add('hidden');
      resetNewPrescriptionForm();
      editingPrescriptionId = null;
    }, 300);
  }

  function resetNewPrescriptionForm() {
    document.getElementById('newPrescriptionForm').reset();
    inputDispenseMode.value = 'none';
    theraPharmacySection.classList.add('hidden');
    externalPharmacySection.classList.add('hidden');
    pharmacyValidationMsg.classList.add('hidden');

    medicationsContainer.replaceChildren();
    // Insert initial empty row
    addMedicationRow();
  }

  function addMedicationRow(data = {}) {
    const rowId = 'med_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4);
    const card = document.createElement('div');
    card.id = rowId;
    card.className = 'bg-surface-container-low p-4 rounded-xl flex flex-col gap-3 relative transition-all';

    // Header with remove button
    const head = document.createElement('div');
    head.className = 'flex items-center justify-between';
    const title = document.createElement('span');
    title.className = 'font-label-md text-label-md font-bold text-on-surface flex items-center gap-1.5';
    title.innerHTML = '<span class="material-symbols-outlined text-[18px] text-primary">pill</span><span>بيانات الدواء والجرعة</span>';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'text-outline hover:text-error transition-colors p-1 rounded';
    removeBtn.title = 'حذف الدواء';
    removeBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">delete</span>';
    removeBtn.addEventListener('click', () => {
      if (medicationsContainer.children.length > 1) {
        card.remove();
      } else {
        showToast('يجب أن تحتوي الوصفة على دواء واحد على الأقل', 'error');
      }
    });

    head.appendChild(title);
    head.appendChild(removeBtn);

    // Form inputs grid
    const grid1 = document.createElement('div');
    grid1.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3';
    grid1.innerHTML = `
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">اسم الدواء (العلمي / التجاري) *</label>
        <input type="text" class="med-name w-full h-9 px-3 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="مثال: باراسيتامول / Paracetamol" value="${data.name || ''}" required>
      </div>
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">الشكل الدوائي</label>
        <input type="text" class="med-form w-full h-9 px-3 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="أقراص / شراب / كبسولات / قطرة" value="${data.form || 'أقراص فموية'}">
      </div>
    `;

    const grid2 = document.createElement('div');
    grid2.className = 'grid grid-cols-2 sm:grid-cols-4 gap-2';
    grid2.innerHTML = `
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">التركيز</label>
        <input type="text" class="med-strength w-full h-9 px-2.5 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="500 mg" value="${data.strength || ''}">
      </div>
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">الجرعة</label>
        <input type="text" class="med-dose w-full h-9 px-2.5 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="قرص واحد" value="${data.dose || ''}">
      </div>
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">التكرار</label>
        <input type="text" class="med-frequency w-full h-9 px-2.5 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="3 مرات يومياً" value="${data.frequency || ''}">
      </div>
      <div class="flex flex-col gap-1">
        <label class="font-label-sm text-label-sm text-outline">المدة</label>
        <input type="text" class="med-duration w-full h-9 px-2.5 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="5 أيام" value="${data.duration || ''}">
      </div>
    `;

    const instructionsCol = document.createElement('div');
    instructionsCol.className = 'flex flex-col gap-1';
    instructionsCol.innerHTML = `
      <label class="font-label-sm text-label-sm text-outline">تعليمات الاستخدام للمريض</label>
      <input type="text" class="med-instructions w-full h-9 px-3 font-body-md text-body-md text-on-surface bg-surface-container-lowest rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="مثال: يؤخذ بعد الوجبات مباشرة مع وفرة من السوائل" value="${data.instructions || ''}">
    `;

    card.appendChild(head);
    card.appendChild(grid1);
    card.appendChild(grid2);
    card.appendChild(instructionsCol);
    medicationsContainer.appendChild(card);
  }

  // Builder Validation & Save
  function buildPrescriptionFromForm(targetState) {
    const patientSelect = document.getElementById('inputPatientSelect');
    const selectedOption = patientSelect.options[patientSelect.selectedIndex];
    const visitSelect = document.getElementById('inputVisitSelect');

    const patientId = selectedOption.value;
    const patientName = selectedOption.getAttribute('data-name');
    const patientAge = selectedOption.getAttribute('data-age');
    const patientGender = selectedOption.getAttribute('data-gender');
    const visitTitle = visitSelect.value;
    const visitId = visitSelect.options[visitSelect.selectedIndex]?.getAttribute('data-visit-id') || '';
    const existing = editingPrescriptionId ? prescriptions.find(i => i.id === editingPrescriptionId) : null;
    const notes = document.getElementById('inputPrescriptionNotes').value.trim();

    // Collect medications
    const medCards = medicationsContainer.querySelectorAll('[id^="med_"]');
    const meds = [];
    let hasInvalidMed = false;

    medCards.forEach(card => {
      const nameInput = card.querySelector('.med-name');
      const medName = nameInput.value.trim();
      if (!medName && targetState !== 'draft') {
        hasInvalidMed = true;
        nameInput.focus();
      } else if (medName) {
        meds.push({
          name: medName,
          scientificName: medName,
          form: card.querySelector('.med-form').value.trim() || 'أقراص',
          strength: card.querySelector('.med-strength').value.trim(),
          dose: card.querySelector('.med-dose').value.trim(),
          frequency: card.querySelector('.med-frequency').value.trim(),
          duration: card.querySelector('.med-duration').value.trim(),
          instructions: card.querySelector('.med-instructions').value.trim()
        });
      }
    });

    if (hasInvalidMed && targetState !== 'draft') {
      showToast('يرجى تحديد اسم الدواء المطلوب بالوصفة', 'error');
      return null;
    }

    if (meds.length === 0 && targetState !== 'draft') {
      showToast('يرجى إضافة دواء واحد على الأقل لحفظ الوصفة كموثقة', 'error');
      return null;
    }

    // Pharmacy verification if targetState is 'send_requested'
    const mode = inputDispenseMode.value;
    let pharmacyName = 'غير محددة';

    if (targetState === 'send_requested') {
      if (mode === 'none') {
        pharmacyValidationMsg.classList.remove('hidden');
        showToast('اختر الصيدلية قبل الإرسال', 'error');
        return null;
      }
      if (mode === 'thera_network') {
        const val = selectTheraPharmacy.value;
        if (!val) {
          pharmacyValidationMsg.classList.remove('hidden');
          showToast('اختر الصيدلية قبل الإرسال', 'error');
          return null;
        }
        pharmacyName = val;
      } else if (mode === 'external') {
        const extVal = inputExternalPharmacyName.value.trim();
        if (!extVal) {
          pharmacyValidationMsg.classList.remove('hidden');
          showToast('يرجى إدخال اسم الصيدلية الخارجية المعتمدة', 'error');
          return null;
        }
        pharmacyName = extVal;
      }
      pharmacyValidationMsg.classList.add('hidden');
    } else {
      if (mode === 'thera_network' && selectTheraPharmacy.value) {
        pharmacyName = selectTheraPharmacy.value;
      } else if (mode === 'external' && inputExternalPharmacyName.value.trim()) {
        pharmacyName = inputExternalPharmacyName.value.trim();
      }
    }

    const today = new Date();
    const arabicMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    const formattedDate = `${today.getDate()} ${arabicMonths[today.getMonth()]} ${today.getFullYear()}`;
    const isoDate = today.toISOString().split('T')[0];

    const randomSerial = Math.floor(1000 + Math.random() * 9000);

    return {
      id: existing ? existing.id : `RX-${patientId.replace('P-', '')}-${randomSerial}`,
      patientId: patientId,
      patientName: patientName,
      patientAge: patientAge,
      patientGender: patientGender,
      visitTitle: existing?.visitTitle || visitTitle,
      visitId: existing?.visitId || visitId,
      encounterId: existing?.encounterId || (visitId ? `ENC-${visitId.replace('VST-', '')}` : ''),
      version: existing ? (existing.version || 1) + 1 : 1,
      doctorName: 'د. محمد أحمد',
      date: existing ? existing.date : formattedDate,
      isoDate: existing ? existing.isoDate : isoDate,
      workflowState: targetState,
      destinationMode: mode === 'none' ? 'unassigned' : mode,
      pharmacyName: pharmacyName,
      dispenseStatus: 'غير متوفر', // Immutable domain by physician
      notes: notes || 'تم إنشاء الوصفة الطبية وتوثيقها في الملف السريري.',
      medications: meds
    };
  }

  function commitPrescription(rx, successMessage) {
    if (!rx) return;
    const idx = prescriptions.findIndex(i => i.id === rx.id);
    if (idx >= 0) prescriptions[idx] = rx;
    else prescriptions.unshift(rx);
    saveState();
    render();
    closeNewPrescriptionDrawer();
    showToast(successMessage, 'success');
  }

  function openPrintPreview(prescriptionId) {
    const rx = prescriptions.find(i => i.id === prescriptionId);
    if (!rx) return;
    document.getElementById('printRxId').textContent = rx.id;
    document.getElementById('printRxDate').textContent = rx.date;
    document.getElementById('printVisitId').textContent = rx.visitId || 'غير مرتبطة';
    document.getElementById('printPatientName').textContent = rx.patientName;
    document.getElementById('printPatientMeta').textContent = `${rx.patientId} • ${rx.patientAge} سنة • ${rx.patientGender}`;
    document.getElementById('printNotes').textContent = rx.notes || 'لا توجد ملاحظات إضافية.';
    const list = document.getElementById('printMedsList');
    list.replaceChildren();
    (rx.medications || []).forEach((med, index) => {
      const item = document.createElement('div');
      item.className = 'rounded-xl border border-slate-200 p-4';
      item.innerHTML = `<div class="flex items-center justify-between gap-4"><div class="font-bold text-slate-900">${index + 1}. ${med.name || '--'}</div><div class="text-[11px] text-slate-500">${med.form || ''} ${med.strength || ''}</div></div><div class="grid grid-cols-3 gap-3 mt-3 text-[12px] text-slate-700"><div><span class="text-slate-500 block text-[10px]">الجرعة</span>${med.dose || '--'}</div><div><span class="text-slate-500 block text-[10px]">التكرار</span>${med.frequency || '--'}</div><div><span class="text-slate-500 block text-[10px]">المدة</span>${med.duration || '--'}</div></div>${med.instructions ? `<div class="mt-3 pt-3 border-t border-slate-100 text-[12px] text-slate-600"><span class="font-semibold">التعليمات:</span> ${med.instructions}</div>` : ''}`;
      list.appendChild(item);
    });
    const wm = document.getElementById('printDraftWatermark');
    wm.classList.toggle('hidden', rx.workflowState !== 'draft');
    wm.classList.toggle('flex', rx.workflowState === 'draft');
    printPreviewModal.classList.remove('hidden');
  }

  function closePrintPreview() { printPreviewModal.classList.add('hidden'); }

  // Setup Event Handlers
  function setupEventListeners() {
    // Status Tabs
    statusTabsContainer.addEventListener('click', (e) => {
      const btn = e.target.closest('.status-tab-btn');
      if (!btn) return;
      
      const filter = btn.getAttribute('data-filter');
      activeTabFilter = filter;

      statusTabsContainer.querySelectorAll('.status-tab-btn').forEach(b => {
        b.classList.remove('bg-primary-container', 'text-white', 'shadow-sm');
        b.classList.add('bg-surface-container-low', 'text-on-surface-variant');
      });

      btn.classList.add('bg-primary-container', 'text-white', 'shadow-sm');
      btn.classList.remove('bg-surface-container-low', 'text-on-surface-variant');

      render();
    });

    // Search input
    searchInput.addEventListener('input', (e) => {
      activeSearchTerm = e.target.value;
      render();
    });

    // Dropdown filters
    filterStatus.addEventListener('change', (e) => {
      activeStatusFilter = e.target.value;
      render();
    });

    filterDestination.addEventListener('change', (e) => {
      activeDestinationFilter = e.target.value;
      render();
    });

    filterDate.addEventListener('change', (e) => {
      activeDateFilter = e.target.value;
      render();
    });

    // Reset buttons
    function resetAllFilters() {
      activeSearchTerm = '';
      activeStatusFilter = 'all';
      activeDestinationFilter = 'all';
      activeDateFilter = 'newest';
      activeTabFilter = 'all';

      searchInput.value = '';
      filterStatus.value = 'all';
      filterDestination.value = 'all';
      filterDate.value = 'newest';

      statusTabsContainer.querySelectorAll('.status-tab-btn').forEach(b => {
        if (b.getAttribute('data-filter') === 'all') {
          b.classList.add('bg-primary-container', 'text-white', 'shadow-sm');
          b.classList.remove('bg-surface-container-low', 'text-on-surface-variant');
        } else {
          b.classList.remove('bg-primary-container', 'text-white', 'shadow-sm');
          b.classList.add('bg-surface-container-low', 'text-on-surface-variant');
        }
      });

      render();
      showToast('تمت إعادة ضبط جميع الفلاتر');
    }

    btnResetFilters.addEventListener('click', resetAllFilters);
    btnEmptyReset.addEventListener('click', resetAllFilters);
    btnMobileEmptyReset.addEventListener('click', resetAllFilters);

    // Context Menu Document Click dismiss
    document.addEventListener('click', (e) => {
      if (!rowContextMenu.classList.contains('hidden') && !rowContextMenu.contains(e.target)) {
        closeContextMenu();
      }
    });

    // Context Menu Actions
    rowContextMenu.addEventListener('click', (e) => {
      const actionBtn = e.target.closest('[data-action]');
      if (!actionBtn) return;
      const action = actionBtn.getAttribute('data-action');
      const targetId = activeMenuPrescriptionId;
      closeContextMenu();

      if (action === 'view-details') {
        openPrescriptionDetails(targetId);
      } else if (action === 'open-visit') {
        const rx = prescriptions.find(i => i.id === targetId);
        showToast(rx?.visitId ? `فتح الزيارة المرتبطة ${rx.visitId} (محاكاة)` : 'هذه الوصفة غير مرتبطة بزيارة محددة', rx?.visitId ? 'info' : 'error');
      } else if (action === 'print') {
        openPrintPreview(targetId);
      } else if (action === 'assign-pharmacy') {
        openPrescriptionDetails(targetId);
        showToast('يرجى تحديد الصيدلية من شريط التوجيه بالأسفل', 'info');
      }
    });

    // Details Drawer listeners
    btnCloseDetails.addEventListener('click', closePrescriptionDetails);
    btnCloseDetailsBottom.addEventListener('click', closePrescriptionDetails);
    drawerDetailsBackdrop.addEventListener('click', closePrescriptionDetails);

    btnDirectSendFromDetails.addEventListener('click', () => {
      if (!viewingPrescriptionId) return;
      const rx = prescriptions.find(i => i.id === viewingPrescriptionId);
      if (!rx) return;

      if (rx.destinationMode === 'unassigned') {
        closePrescriptionDetails();
        openNewPrescriptionDrawer(rx.patientId);
        showToast('يرجى تحديد الصيدلية لإتمام عملية الإرسال', 'info');
      } else {
        rx.workflowState = 'send_requested';
        saveState();
        render();
        closePrescriptionDetails();
        showToast('تم تجهيز الوصفة للإرسال إلى الصيدلية المحددة', 'success');
      }
    });

    btnEditInDrawer.addEventListener('click', () => {
      if (!viewingPrescriptionId) return;
      const rx = prescriptions.find(i => i.id === viewingPrescriptionId);
      closePrescriptionDetails();
      if (rx) openEditPrescriptionDrawer(rx);
    });

    btnPrintPrescription.addEventListener('click', () => { if (viewingPrescriptionId) openPrintPreview(viewingPrescriptionId); });
    btnOpenLinkedVisit.addEventListener('click', () => {
      if (!viewingPrescriptionId) return;
      const rx = prescriptions.find(i => i.id === viewingPrescriptionId);
      if (rx?.visitId) showToast(`فتح الزيارة المرتبطة ${rx.visitId} (محاكاة)`, 'info');
    });
    btnClosePrintPreview.addEventListener('click', closePrintPreview);
    btnExecutePrint.addEventListener('click', () => window.print());
    printPreviewModal.addEventListener('click', (e) => { if (e.target === printPreviewModal) closePrintPreview(); });

    // New Prescription Builder Listeners
    btnOpenNewPrescription.addEventListener('click', () => openNewPrescriptionDrawer());
    btnCloseNewPrescription.addEventListener('click', closeNewPrescriptionDrawer);
    btnCancelNewPrescription.addEventListener('click', closeNewPrescriptionDrawer);
    drawerNewBackdrop.addEventListener('click', closeNewPrescriptionDrawer);

    btnAddMedicationRow.addEventListener('click', () => addMedicationRow());

    inputDispenseMode.addEventListener('change', (e) => {
      pharmacyValidationMsg.classList.add('hidden');
      const val = e.target.value;
      if (val === 'thera_network') {
        theraPharmacySection.classList.remove('hidden');
        theraPharmacySection.classList.add('flex');
        externalPharmacySection.classList.add('hidden');
        externalPharmacySection.classList.remove('flex');
      } else if (val === 'external') {
        externalPharmacySection.classList.remove('hidden');
        externalPharmacySection.classList.add('flex');
        theraPharmacySection.classList.add('hidden');
        theraPharmacySection.classList.remove('flex');
      } else {
        theraPharmacySection.classList.add('hidden');
        theraPharmacySection.classList.remove('flex');
        externalPharmacySection.classList.add('hidden');
        externalPharmacySection.classList.remove('flex');
      }
    });

    // Save as Draft
    btnSaveDraft.addEventListener('click', () => {
      const rx = buildPrescriptionFromForm('draft');
      if (rx) {
        commitPrescription(rx, editingPrescriptionId ? 'تم تحديث نفس الوصفة وحفظ التعديل كمسودة' : 'تم حفظ الوصفة كمسودة بنجاح');
      }
    });

    // Save as Documented
    btnSaveDocumented.addEventListener('click', () => {
      const rx = buildPrescriptionFromForm('documented');
      if (rx) {
        commitPrescription(rx, editingPrescriptionId ? 'تم تحديث نفس السجل الطبي للوصفة وتوثيق التعديل' : 'تم توثيق الوصفة الطبية بنجاح');
      }
    });

    // Send to Pharmacy
    btnSendPrescription.addEventListener('click', () => {
      const rx = buildPrescriptionFromForm('send_requested');
      if (rx) {
        commitPrescription(rx, editingPrescriptionId ? 'تم تحديث نفس الوصفة وتجهيزها للإرسال' : 'تم تجهيز الوصفة للإرسال إلى الصيدلية المحددة');
      }
    });
  }

  // Self execute
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
</script></main><script>
(function(){
  const $=id=>document.getElementById(id);
  const sidebar=$('app-sidebar'), topHeader=$('top-header'), main=$('main-content-layout'), pin=$('sidebar-pin-toggle'), pinIcon=$('sidebar-pin-icon');
  const KEY='thera_doctor_sidebar_pinned';
  function applyLayout(collapsed){
    if(!sidebar||!topHeader||!main)return;
    sidebar.classList.toggle('sidebar-collapsed',collapsed);
    topHeader.style.right=collapsed?'72px':'268px';
    main.style.marginRight=collapsed?'72px':'268px';
    if(pinIcon)pinIcon.textContent=collapsed?'push_pin':'keep';
  }
  const pinned=localStorage.getItem(KEY)!=='false'; applyLayout(!pinned);
  if(pin) pin.addEventListener('click',()=>{const collapsed=!sidebar.classList.contains('sidebar-collapsed');localStorage.setItem(KEY,collapsed?'false':'true');applyLayout(collapsed)});
  if(sidebar){
    sidebar.addEventListener('mouseenter',()=>{if(sidebar.classList.contains('sidebar-collapsed'))sidebar.classList.add('sidebar-hovered')});
    sidebar.addEventListener('mouseleave',()=>sidebar.classList.remove('sidebar-hovered'));
  }
  const qBtn=$('quick-add-btn'),qMenu=$('quick-add-dropdown'),qArrow=$('quick-add-arrow');
  if(qBtn&&qMenu){
    qBtn.addEventListener('click',e=>{e.stopPropagation();const hidden=qMenu.classList.toggle('hidden');if(qArrow)qArrow.textContent=hidden?'expand_more':'expand_less'});
    qMenu.addEventListener('click',e=>e.stopPropagation());
    document.addEventListener('click',()=>{qMenu.classList.add('hidden');if(qArrow)qArrow.textContent='expand_more'});
    const rxQuick=[...qMenu.querySelectorAll('button')].find(b=>b.textContent.includes('وصفة طبية'));
    if(rxQuick) rxQuick.addEventListener('click',()=>{const b=document.getElementById('btnOpenNewPrescription');if(b)b.click();qMenu.classList.add('hidden')});
  }
  const doctorTrigger=$('doctor-card-trigger'),doctorMenu=$('doctor-account-menu');
  if(doctorTrigger&&doctorMenu){
    doctorTrigger.addEventListener('click',e=>{e.stopPropagation();doctorMenu.classList.toggle('hidden')});
    doctorMenu.addEventListener('click',e=>e.stopPropagation());
    document.addEventListener('click',()=>doctorMenu.classList.add('hidden'));
  }
})();
</script>
<script>
(function(){
  function closeAllCustomFilterDropdowns(exceptId){
    document.querySelectorAll('[data-dropdown-menu]').forEach(menu=>{
      const id=menu.getAttribute('data-dropdown-menu');
      if(id===exceptId) return;
      menu.classList.add('hidden');
      const btn=document.querySelector('[data-dropdown-select="'+id+'"]');
      if(btn) btn.setAttribute('aria-expanded','false');
    });
  }

  function syncCustomFilter(id){
    const select=document.getElementById(id);
    const btn=document.querySelector('[data-dropdown-select="'+id+'"]');
    const menu=document.querySelector('[data-dropdown-menu="'+id+'"]');
    if(!select||!btn||!menu) return;
    const selected=select.options[select.selectedIndex];
    const label=btn.querySelector('.thera-dropdown-label');
    if(label&&selected) label.textContent=selected.textContent;
    menu.querySelectorAll('.thera-dropdown-option').forEach(opt=>{
      opt.classList.toggle('is-selected',opt.dataset.value===select.value);
    });
  }

  function initCustomFilterDropdowns(){
    ['filterStatus','filterDestination','filterDate'].forEach(id=>{
      const select=document.getElementById(id);
      const btn=document.querySelector('[data-dropdown-select="'+id+'"]');
      const menu=document.querySelector('[data-dropdown-menu="'+id+'"]');
      if(!select||!btn||!menu) return;

      syncCustomFilter(id);
      btn.addEventListener('click',function(e){
        e.stopPropagation();
        const willOpen=menu.classList.contains('hidden');
        closeAllCustomFilterDropdowns(willOpen?id:null);
        menu.classList.toggle('hidden',!willOpen);
        btn.setAttribute('aria-expanded',willOpen?'true':'false');
      });

      menu.addEventListener('click',function(e){
        const option=e.target.closest('.thera-dropdown-option');
        if(!option) return;
        e.stopPropagation();
        select.value=option.dataset.value;
        select.dispatchEvent(new Event('change',{bubbles:true}));
        syncCustomFilter(id);
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded','false');
      });

      select.addEventListener('change',()=>syncCustomFilter(id));
    });

    document.addEventListener('click',()=>closeAllCustomFilterDropdowns(null));
    ['btnResetFilters','btnEmptyReset','btnMobileEmptyReset'].forEach(btnId=>{
      const b=document.getElementById(btnId);
      if(b) b.addEventListener('click',()=>setTimeout(()=>{
        ['filterStatus','filterDestination','filterDate'].forEach(syncCustomFilter);
      },0));
    });
  }

  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',initCustomFilterDropdowns);
  else initCustomFilterDropdowns();
})();
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>
