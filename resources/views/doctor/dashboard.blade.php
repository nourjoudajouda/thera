<!DOCTYPE html>

<html dir="rtl" lang="ar" style=""><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>@include('doctor.partials.assets')
<style>@layer base{html,body{margin:0;padding:0;font-family:'Tajawal',sans-serif;}button,input,select,textarea{font-family:'Tajawal',sans-serif;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><style>
  /* Sidebar transition classes */
  #app-sidebar {
    width: 268px;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
  }
  #app-sidebar.sidebar-collapsed {
    width: 72px;
  }
  #app-sidebar.sidebar-collapsed.sidebar-hovered {
    width: 268px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
  }
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-text,
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-group-title,
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-meta-text,
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-doctor-more {
    display: none !important;
  }
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-nav-item {
    justify-content: center;
    padding-left: 0;
    padding-right: 0;
  }
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-wrapper {
    justify-content: center;
    padding-left: 0;
    padding-right: 0;
    width: 100%;
  }
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-pin-btn {
    display: none;
  }
  #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-doctor-card {
    justify-content: center;
    padding: 0.5rem 0;
  }
  #main-content-layout {
    transition: margin-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  #top-header {
    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
</style>
</head><body class="antialiased" style="background-color: #F6F9FC; color: #0B1020;">
<!-- SIDEBAR (Variant B - Soft Navy Professional) -->
<aside class="fixed right-0 top-0 h-full z-50 flex flex-col shadow-lg overflow-visible w-[268px]" id="app-sidebar" style="background-color: #16243E; border-left: 1px solid rgba(255, 255, 255, 0.08);"><!-- SIDEBAR HEADER / BRAND -->
<div class="h-16 px-4 flex items-center justify-between shrink-0" style="background-color: #132038; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
<a class="sidebar-brand-wrapper flex items-center justify-center min-w-0 transition-all" data-path="dashboard" href="#" title="THERA">
<img alt="THERA" class="h-9 w-auto max-w-[145px] object-contain shrink-0 filter brightness-110" src="{{ asset('images/doctor/thera-logo.png') }}"/>
</a>
<button class="sidebar-pin-btn w-8 h-8 rounded-lg flex items-center justify-center text-[#AFC3D9] hover:text-[#0495F6] hover:bg-white/5 transition-colors shrink-0" id="sidebar-pin-toggle" onclick="toggleSidebarPin()" title="تثبيت / طي القائمة" type="button">
<span class="material-symbols-outlined text-[19px]" id="sidebar-pin-icon">push_pin</span>
</button>
</div>
<!-- NAVIGATION LINKS -->
<div class="flex-1 overflow-y-auto px-3 py-3 space-y-4">
<!-- GROUP: العمل اليومي -->
<div class="space-y-1">
<div class="sidebar-group-title px-3 py-1.5 text-[13px] font-semibold text-[#8EA3BA] tracking-wider">العمل اليومي</div>
<nav class="space-y-1">
<a aria-current="page" class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all bg-[#0495F6] hover:bg-[#0466F1] text-white font-bold shadow-md shadow-[#0495F6]/20" data-path="dashboard" href="#" title="الرئيسية">
<span class="material-symbols-outlined text-[22px] shrink-0 text-white">grid_view</span>
<span class="sidebar-text truncate text-[16px] font-bold">الرئيسية</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="appointments" href="#" title="المواعيد">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">calendar_today</span>
<span class="sidebar-text truncate text-[16px]">المواعيد</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="patients" href="#" title="المرضى">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">person_search</span>
<span class="sidebar-text truncate text-[16px]">المرضى</span>
</a>
</nav>
</div>
<!-- GROUP: العمل الطبي -->
<div class="space-y-1">
<div class="sidebar-group-title px-3 py-1.5 text-[13px] font-semibold text-[#8EA3BA] tracking-wider">العمل الطبي</div>
<nav class="space-y-1">
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="clinical-visits" href="#" title="الزيارات">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">stethoscope</span>
<span class="sidebar-text truncate text-[16px]">الزيارات</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="prescriptions" href="#" title="الوصفات الطبية">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">prescriptions</span>
<span class="sidebar-text truncate text-[16px]">الوصفات الطبية</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="lab-orders" href="#" title="طلبات المختبر">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">science</span>
<span class="sidebar-text truncate text-[16px]">طلبات المختبر</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="radiology-orders" href="#" title="طلبات الأشعة">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">radiology</span>
<span class="sidebar-text truncate text-[16px]">طلبات الأشعة</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="diagnostic-results" href="#" title="النتائج">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">assignment_turned_in</span>
<span class="sidebar-text truncate text-[16px]">النتائج</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="referrals-and-followups" href="#" title="الإحالات والمتابعات">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">sync_alt</span>
<span class="sidebar-text truncate text-[16px]">الإحالات والمتابعات</span>
</a>
</nav>
</div>
<!-- GROUP: الإدارة -->
<div class="space-y-1">
<div class="sidebar-group-title px-3 py-1.5 text-[13px] font-semibold text-[#8EA3BA] tracking-wider">الإدارة</div>
<nav class="space-y-1">
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="services-pricing" href="#" title="الخدمات والأسعار">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">receipt_long</span>
<span class="sidebar-text truncate text-[16px]">الخدمات والأسعار</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="doctor-schedule" href="#" title="الدوام والجدول">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">schedule</span>
<span class="sidebar-text truncate text-[16px]">الدوام والجدول</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="billing-receivables" href="#" title="التحصيل والمستحقات">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">account_balance_wallet</span>
<span class="sidebar-text truncate text-[16px]">التحصيل والمستحقات</span>
</a>
<a class="sidebar-nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl text-[#D7E3F1] hover:text-[#FFFFFF] hover:bg-white/[0.06] transition-all font-semibold" data-path="clinic-settings" href="#" title="الإعدادات">
<span class="material-symbols-outlined text-[22px] shrink-0 text-[#AFC3D9]">settings</span>
<span class="sidebar-text truncate text-[16px]">الإعدادات</span>
</a>
</nav>
</div>
</div>
<!-- SIDEBAR FOOTER / DOCTOR CARD -->
<div class="p-3 relative shrink-0" style="background-color: #132038; border-top: 1px solid rgba(255, 255, 255, 0.08);">
<div class="group relative">
<div class="sidebar-doctor-card p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between hover:border-white/20" onclick="toggleDoctorMenu()" style="background-color: #132038; border-color: rgba(255, 255, 255, 0.1);">
<div class="flex items-center gap-2.5 min-w-0">
<div class="relative shrink-0">
<img alt="د. محمد أحمد" class="w-9 h-9 rounded-full object-cover ring-2 ring-[#0495F6]/40" src="https://lh3.googleusercontent.com/aida/AEtjO1XCFCGKWzRSiU4yKnzR5DJEAeK0t-y1xe9SvA8gkWjHHrq8OVS4xmm1Lqm-B1gG1OEdAN8hUZOMuwPa94xN6ZK2pJgpbnYNCxF4SxIn6I9zSjMQcO3_v0673cadfnpMqWLa8KqnDCDzrAWkFoNpw4pS7puhRajPOlSiA_SqHiBc-zdlYQAvQkDC2QnMJu3-gxj2WvgtqSw9lZ1WNffg2HrosU3wcw27r9VMt_hdTvqzJL52I0jEzD32bAU"/>
<span class="absolute -bottom-0.5 -right-0.5 flex h-3 w-3">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#10B981] opacity-75"></span>
<span class="relative inline-flex rounded-full h-3 w-3 bg-[#10B981] border border-[#132038]"></span>
</span>
</div>
<div class="doctor-meta-text flex-1 min-w-0 text-right">
<div class="text-[14px] text-[#FFFFFF] truncate font-bold">د. محمد أحمد</div>
<div class="text-[12px] text-[#AFC3D9] truncate">استشاري طب باطني</div>
<div class="flex items-center gap-1 mt-0.5 text-[11px] font-semibold text-[#10B981]">
<span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
<span class="">متاح الآن</span>
</div>
</div>
</div>
<span class="sidebar-doctor-more material-symbols-outlined text-[#AFC3D9] text-[20px] shrink-0">more_vert</span>
</div>
<!-- Doctor Account Menu Dropdown (Dark navy styled) -->
<div class="absolute bottom-full right-0 mb-2 rounded-xl shadow-2xl py-1.5 z-50 border min-w-[210px] hidden text-right" id="doctor-account-menu" style="background-color: #132038; border-color: rgba(255, 255, 255, 0.12);">
<a class="flex items-center gap-2.5 px-3 py-2 text-sm text-[#D7E3F1] hover:bg-white/[0.08] hover:text-white transition-colors font-medium" data-path="profile-doctor" href="#">
<span class="material-symbols-outlined text-[18px] text-[#0495F6]">account_circle</span>
<span class="">الملف الشخصي</span>
</a>
<a class="flex items-center gap-2.5 px-3 py-2 text-sm text-[#D7E3F1] hover:bg-white/[0.08] hover:text-white transition-colors font-medium" data-path="account-settings" href="#">
<span class="material-symbols-outlined text-[18px] text-[#AFC3D9]">settings</span>
<span class="">إعدادات الحساب</span>
</a>
<a class="flex items-center gap-2.5 px-3 py-2 text-sm text-[#D7E3F1] hover:bg-white/[0.08] hover:text-white transition-colors font-medium" data-path="availability-status" href="#">
<span class="material-symbols-outlined text-[18px] text-[#10B981]">schedule</span>
<span class="">تغيير حالة التوفر</span>
</a>
<div class="my-1 h-px" style="background-color: rgba(255, 255, 255, 0.08);"></div>
<a class="flex items-center gap-2.5 px-3 py-2 text-sm text-[#EF4444] hover:bg-[#EF4444]/15 transition-colors font-semibold" data-path="logout" href="#">
<span class="material-symbols-outlined text-[18px] text-[#EF4444]">logout</span>
<span class="">تسجيل الخروج</span>
</a>
</div>
</div>
</div></aside>
<!-- MAIN CONTENT CONTAINER -->
<div class="mr-[268px] min-h-screen transition-all duration-300" id="main-content-layout" style="margin-right: 268px;">
<!-- TOPBAR -->
<header class="fixed top-0 right-[268px] left-0 h-16 px-6 flex items-center z-40 bg-white transition-all duration-300" id="top-header" style="background-color: #FFFFFF; border-bottom: 1px solid #E2E8F0; right: 268px; box-shadow: 0 1px 2px rgba(16, 27, 53, 0.03);"><div class="w-full flex items-center justify-between gap-4"><div class="flex items-center gap-4">
<!-- Quick Add Button & Dropdown -->
<div class="relative inline-block" id="quick-add-container" onmouseenter="if (window.quickAddLeaveTimer) { clearTimeout(window.quickAddLeaveTimer); window.quickAddLeaveTimer = null; } const menu = document.getElementById('quick-add-dropdown'); const arrow = document.getElementById('quick-add-arrow'); if (menu) { menu.classList.remove('hidden'); if (arrow) arrow.textContent = 'expand_less'; }" onmouseleave="window.quickAddLeaveTimer = setTimeout(() =&gt; { closeQuickAddMenu(); }, 200);">
<button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] active:bg-[#0353c7] text-white font-semibold text-sm transition-all shadow-sm cursor-pointer" id="quick-add-btn" onclick="toggleQuickAddMenu()" type="button">
<span class="material-symbols-outlined text-[20px]">add</span>
<span class="">إضافة</span>
<span class="material-symbols-outlined text-[18px] transition-transform" id="quick-add-arrow">expand_more</span>
</button>
<div class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-xl py-2 z-50 border text-right hidden" id="quick-add-dropdown" style="border-color: #E2E8F0;">
<div class="px-4 py-1.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">تشغيلي</div>
<div class="space-y-0.5 px-2">
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="new-patient" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">person_add</span>
<span class="">إضافة مريض</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="new-appointment" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">calendar_today</span>
<span class="">موعد جديد</span>
</a>
</div>
<div class="my-2 bg-[#E2E8F0] h-px"></div>
<div class="px-4 py-1.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">سريري</div>
<div class="space-y-0.5 px-2">
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="clinical-visits" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">stethoscope</span>
<span class="">زيارة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="prescriptions" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">prescriptions</span>
<span class="">وصفة طبية</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="lab-orders" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">science</span>
<span class="">طلب مختبر</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="radiology-orders" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">radiology</span>
<span class="">طلب أشعة</span>
</a>
<a class="flex items-center gap-2 px-3 py-2 rounded-lg text-[#0B1020] hover:bg-[#EFF6FF] hover:text-[#0495F6] transition-colors text-sm font-medium" data-path="referrals-and-followups" href="#" onclick="closeQuickAddMenu()">
<span class="material-symbols-outlined text-[20px] text-[#0495F6] shrink-0">sync_alt</span>
<span class="">إحالة / متابعة</span>
</a>
</div>
</div>
</div>
<!-- Search Bar -->
<div class="relative min-w-[280px] lg:min-w-[340px]">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#64748B] text-[20px]">search</span>
<input class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#0B1020] placeholder:text-[#64748B] text-sm rounded-lg pr-10 pl-4 py-2 transition-all focus:outline-none focus:bg-white focus:border-[#0495F6] focus:ring-2 focus:ring-[#0495F6]/20 font-sans" placeholder="ابحث عن مريض أو موعد... (⌘K)" type="text"/>
</div>
</div>
<!-- Topbar Utilities (Notifications, Language, Theme) -->
<div class="flex items-center gap-2">
<button class="relative w-9 h-9 rounded-lg flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0495F6] transition-colors" title="الإشعارات" type="button">
<span class="material-symbols-outlined text-[22px]">notifications</span>
<span class="absolute top-1.5 left-1.5 flex h-2 w-2">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EF4444] opacity-75"></span>
<span class="relative inline-flex rounded-full h-2 w-2 bg-[#EF4444]"></span>
</span>
</button>
<button class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0495F6] transition-colors" title="اللغة" type="button">
<span class="material-symbols-outlined text-[18px]">translate</span>
<span class="">عربي (AR)</span>
</button>
<button class="w-9 h-9 rounded-lg flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0495F6] transition-colors" title="تبديل المظهر" type="button">
<span class="material-symbols-outlined text-[22px]">light_mode</span>
</button>
</div></div></header>
<!-- MAIN WORKSPACE -->
<main class="relative pt-20 w-full px-6 pb-8" style="background-color: #F6F9FC;"><div class="flex flex-col w-full gap-6">
<!-- HEALTHCARE HERO BANNER (Executive clean medical styling with soft blue-slate tint #EEF4FA and border #CBD5E1) -->
<div class="relative overflow-hidden rounded-2xl bg-[#EEF4FA] border border-[#CBD5E1] px-6 py-5 shadow-sm flex items-center justify-between flex-wrap gap-4">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-3">
<h1 class="text-2xl font-bold text-[#0B1020]">صباح الخير، د. محمد أحمد</h1>
</div>
<p class="text-sm font-medium text-[#475569]">14 موعد اليوم • 3 في الانتظار</p>
</div>
<div class="text-xs text-[#334155] flex items-center gap-1.5 font-semibold bg-white px-3 py-1.5 rounded-lg border border-[#CBD5E1] shadow-xs">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">calendar_today</span>
<span class="">الأربعاء، 23 أكتوبر 2024</span>
</div>
</div>
<!-- 5 OPERATIONAL KPI CARDS WITH RICHER VARIANT B TINTS -->
<section aria-label="مؤشرات الأداء التشغيلية" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
<!-- مواعيد اليوم -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#E0F2FE] border border-[#7DD3FC] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#0369A1] font-sans">14</span>
<span class="text-xs font-bold text-[#0369A1] mt-0.5">مواعيد اليوم</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#7DD3FC] flex items-center justify-center text-[#0369A1] shadow-xs">
<span class="material-symbols-outlined text-[20px]">calendar_today</span>
</div>
</div>
<!-- في الانتظار -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#FEF3C7] border border-[#FCD34D] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#B45309] font-sans">3</span>
<span class="text-xs font-bold text-[#B45309] mt-0.5">في الانتظار</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#FCD34D] flex items-center justify-center text-[#B45309] shadow-xs">
<span class="material-symbols-outlined text-[20px]">hourglass_top</span>
</div>
</div>
<!-- جاهزون للدخول -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#D1FAE5] border border-[#6EE7B7] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#047857] font-sans">2</span>
<span class="text-xs font-bold text-[#047857] mt-0.5">جاهزون للدخول</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#6EE7B7] flex items-center justify-center text-[#047857] shadow-xs">
<span class="material-symbols-outlined text-[20px]">check_circle</span>
</div>
</div>
<!-- نتائج للمراجعة -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#EDE9FE] border border-[#C4B5FD] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#6D28D9] font-sans">4</span>
<span class="text-xs font-bold text-[#6D28D9] mt-0.5">نتائج للمراجعة</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#C4B5FD] flex items-center justify-center text-[#6D28D9] shadow-xs">
<span class="material-symbols-outlined text-[20px]">assignment</span>
</div>
</div>
<!-- متابعات قادمة -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#CFFAFE] border border-[#67E8F9] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#0E7490] font-sans">5</span>
<span class="text-xs font-bold text-[#0E7490] mt-0.5">متابعات قادمة</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#67E8F9] flex items-center justify-center text-[#0E7490] shadow-xs">
<span class="material-symbols-outlined text-[20px]">swap_horiz</span>
</div>
</div>
</section>
<!-- MAIN OPERATIONAL AREA: 2-COLUMN RATIO (~2/3 and 1/3) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- جاهزون الآن -->
<div class="lg:col-span-2 flex flex-col">
<div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-4 h-full flex flex-col justify-between">
<div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F1F5F9]">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-[#047857]">how_to_reg</span>
<h2 class="text-sm font-bold text-[#0B1020]">جاهزون الآن</h2>
</div>
<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#D1FAE5] text-[#047857] border border-[#6EE7B7]/60">2 مرضى</span>
</div>
<div class="space-y-2.5 flex-1 flex flex-col justify-center">
<div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-[#0495F6]/10 text-[#0495F6] font-bold flex items-center justify-center text-xs shrink-0">س‌ق</div>
<div class="flex items-center gap-2 flex-wrap">
<span class="text-sm font-bold text-[#0B1020]">سالم القحطاني</span>
<span class="text-[#CBD5E1]">•</span>
<span class="text-xs text-[#64748B] font-latin">10:30 ص</span>
<span class="text-[#CBD5E1]">•</span>
<span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#D1FAE5] text-[#047857] border border-[#A7F3D0]">جاهز للطبيب</span>
</div>
</div>
<a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-xs font-bold transition-all shadow-sm shrink-0" data-path="clinical-visits" href="#">
<span class="material-symbols-outlined text-[15px]">stethoscope</span>
<span class="">فتح الزيارة</span>
</a>
</div>
<div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-[#6D28D9]/10 text-[#6D28D9] font-bold flex items-center justify-center text-xs shrink-0">ف‌ش</div>
<div class="flex items-center gap-2 flex-wrap">
<span class="text-sm font-bold text-[#0B1020]">فاطمة الشريف</span>
<span class="text-[#CBD5E1]">•</span>
<span class="text-xs text-[#64748B] font-latin">11:00 ص</span>
<span class="text-[#CBD5E1]">•</span>
<span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#FEF3C7] text-[#B45309] border border-[#FDE68A]">تنتظر منذ 14 دقيقة</span>
</div>
</div>
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#CBD5E1] hover:border-[#0495F6] text-[#0B1020] text-xs font-semibold transition-all shrink-0 shadow-xs" type="button">
<span class="material-symbols-outlined text-[15px] text-[#64748B]">campaign</span>
<span class="">استدعاء</span>
</button>
</div>
</div>
</div>
</div>
<!-- تنبيهات مهمة -->
<div class="flex flex-col">
<div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-4 h-full flex flex-col justify-between">
<div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F1F5F9]">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-[#B45309]">notification_important</span>
<h2 class="text-sm font-bold text-[#0B1020]">تنبيهات مهمة</h2>
</div>
<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#E0F2FE] text-[#0369A1] border border-[#7DD3FC]/60">3</span>
</div>
<div class="space-y-2.5 flex-1 flex flex-col justify-center">
<div class="p-3 rounded-xl bg-[#EDE9FE] border border-[#C4B5FD] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#6D28D9] shrink-0">assignment</span>
<span class="text-xs font-bold text-[#4C1D95]">4 نتائج تحتاج مراجعة</span>
</div>
<div class="p-3 rounded-xl bg-[#FEF3C7] border border-[#FCD34D] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#B45309] shrink-0">hourglass_top</span>
<span class="text-xs font-bold text-[#78350F]">3 مرضى في الانتظار</span>
</div>
<div class="p-3 rounded-xl bg-[#CFFAFE] border border-[#67E8F9] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#0E7490] shrink-0">swap_horiz</span>
<span class="text-xs font-bold text-[#164E63]">5 متابعات هذا الأسبوع</span>
</div>
</div>
</div>
</div>
</div>
<!-- QUICK ACTIONS: COMPACT LIGHTWEIGHT GRID -->
<section aria-label="إجراءات سريعة" class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-4">
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-[#0495F6]"></span>
<h2 class="text-sm font-bold text-[#0B1020]">إجراءات سريعة</h2>
</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
<!-- إضافة مريض -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0495F6] hover:bg-[#F0F7FF] hover:shadow-xs transition-all group text-center" data-path="new-patient" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0495F6] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">person_add</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0495F6] transition-colors truncate w-full">إضافة مريض</span>
</a>
<!-- حجز موعد -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0284C7] hover:bg-[#F0FDFA] hover:shadow-xs transition-all group text-center" data-path="appointments" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0284C7] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">calendar_today</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0284C7] transition-colors truncate w-full">حجز موعد</span>
</a>
<!-- فتح زيارة -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0495F6] hover:bg-[#F0F7FF] hover:shadow-xs transition-all group text-center" data-path="clinical-visits" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0495F6] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">stethoscope</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0495F6] transition-colors truncate w-full">فتح زيارة</span>
</a>
<!-- وصفة طبية -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0466F1] hover:bg-[#EEF2FF] hover:shadow-xs transition-all group text-center" data-path="prescriptions" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0466F1] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">description</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0466F1] transition-colors truncate w-full">وصفة طبية</span>
</a>
<!-- طلب مختبر -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#047857] hover:bg-[#ECFDF5] hover:shadow-xs transition-all group text-center" data-path="lab-orders" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#047857] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">science</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#047857] transition-colors truncate w-full">طلب مختبر</span>
</a>
<!-- طلب أشعة -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#6D28D9] hover:bg-[#FAF5FF] hover:shadow-xs transition-all group text-center" data-path="radiology-orders" href="#">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#6D28D9] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">radiology</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#6D28D9] transition-colors truncate w-full">طلب أشعة</span>
</a>
</div>
</section>
</div></main>
</div>
<script>
  // App Shell Interactive Collapse & Pin State
  let isPinned = true;
  const sidebar = document.getElementById('app-sidebar');
  const mainContent = document.getElementById('main-content-layout');
  const topHeader = document.getElementById('top-header');
  const pinBtn = document.getElementById('sidebar-pin-toggle');
  const pinIcon = document.getElementById('sidebar-pin-icon');

  function updateSidebarState() {
    if (isPinned) {
      sidebar.classList.remove('sidebar-collapsed', 'sidebar-hovered');
      mainContent.style.marginRight = '268px';
      topHeader.style.right = '268px';
      pinBtn.classList.remove('text-[#AFC3D9]');
      pinBtn.classList.add('text-[#0495F6]', 'bg-white/10');
      pinIcon.textContent = 'push_pin';
      pinBtn.title = 'طي القائمة الجانبية';
    } else {
      sidebar.classList.add('sidebar-collapsed');
      sidebar.classList.remove('sidebar-hovered');
      mainContent.style.marginRight = '72px';
      topHeader.style.right = '72px';
      pinBtn.classList.add('text-[#AFC3D9]');
      pinBtn.classList.remove('text-[#0495F6]', 'bg-white/10');
      pinIcon.textContent = 'push_pin';
      pinBtn.title = 'تثبيت القائمة الجانبية';
    }
  }

  function toggleSidebarPin() {
    isPinned = !isPinned;
    localStorage.setItem('thera_sidebar_pinned', isPinned ? 'true' : 'false');
    updateSidebarState();
  }

  function toggleDoctorMenu() {
    const menu = document.getElementById('doctor-account-menu');
    if (menu) {
      menu.classList.toggle('hidden');
    }
  }

  // Close doctor menu when clicked outside
  document.addEventListener('click', (e) => {
    const menu = document.getElementById('doctor-account-menu');
    const doctorCard = document.querySelector('.sidebar-doctor-card');
    if (menu && !menu.classList.contains('hidden')) {
      if (!menu.contains(e.target) && !doctorCard.contains(e.target)) {
        menu.classList.add('hidden');
      }
    }
  });

  // Hover expand behavior when unpinned
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

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    const savedPin = localStorage.getItem('thera_sidebar_pinned');
    isPinned = savedPin !== null ? savedPin === 'true' : true;
    updateSidebarState();
  });

  function toggleQuickAddMenu(){
    const menu = document.getElementById('quick-add-dropdown');
    const arrow = document.getElementById('quick-add-arrow');
    if(menu){
      const isHidden = menu.classList.toggle('hidden');
      if(arrow){ arrow.textContent = isHidden ? 'expand_more' : 'expand_less'; }
    }
  }

  function closeQuickAddMenu(){
    const menu = document.getElementById('quick-add-dropdown');
    const arrow = document.getElementById('quick-add-arrow');
    if(menu){ menu.classList.add('hidden'); }
    if(arrow){ arrow.textContent = 'expand_more'; }
  }

  document.addEventListener('click', function(e){
    const container = document.getElementById('quick-add-container');
    if(container && !container.contains(e.target)){
      closeQuickAddMenu();
    }
  });
</script>
@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body></html>