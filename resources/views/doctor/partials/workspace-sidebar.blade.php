<style>
    #app-sidebar{display:flex!important;flex-direction:column;height:100dvh}
    #app-sidebar>header,#app-sidebar>footer{flex-shrink:0}
    #app-sidebar #sidebar-nav-container{flex:1 1 0%;min-height:0;overflow-y:auto;overflow-x:hidden;overscroll-behavior-y:contain;scrollbar-width:none;-ms-overflow-style:none}
    #app-sidebar #sidebar-nav-container::-webkit-scrollbar{width:0;height:0;display:none}
</style>
<aside aria-labelledby="doctor-sidebar-title" class="offcanvas-lg offcanvas-end fixed top-0 right-0 bottom-0 z-50 bg-[#16243E] border-l border-[rgba(255,255,255,0.08)] flex flex-col select-none" data-purpose="application-sidebar" id="app-sidebar" tabindex="-1" data-doctor-id="{{ $doctor->id }}">
<!-- Sidebar Header & Official Logo -->
<header class="h-16 flex items-center justify-between px-4 bg-[#132038] border-b border-[rgba(255,255,255,0.08)] flex-shrink-0">
@include('doctor.partials.brand')
<button aria-label="إغلاق القائمة" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#app-sidebar" type="button"></button>
<button class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors" id="sidebar-pin-toggle" title="تثبيت / طي القائمة" type="button">
<span class="material-symbols-outlined text-xl fill-1" id="sidebar-pin-icon">push_pin</span>
</button>
</header>
<!-- Scrollable Navigation Items -->
<div class="sidebar-scroll flex-1 min-h-0 py-4 px-3 space-y-6 overflow-y-auto overflow-x-hidden" id="sidebar-nav-container" role="navigation" aria-label="قائمة الطبيب" tabindex="0">
<!-- Group 1: العمل اليومي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="العمل اليومي">العمل اليومي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.dashboard') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">grid_view</span>
<span class="nav-label" data-i18n="الرئيسية">الرئيسية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.appointments.index') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">calendar_today</span>
<span class="nav-label" data-i18n="المواعيد">المواعيد</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.patients.index') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">person_search</span>
<span class="nav-label" data-i18n="المرضى">المرضى</span>
</a>
</div>
<!-- Group 2: العمل الطبي -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="العمل الطبي">العمل الطبي</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.clinical.visits') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">stethoscope</span>
<span class="nav-label" data-i18n="الزيارات">الزيارات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.clinical.prescriptions') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">prescriptions</span>
<span class="nav-label" data-i18n="الوصفات الطبية">الوصفات الطبية</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.orders.lab') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">science</span>
<span class="nav-label" data-i18n="طلبات المختبر">طلبات المختبر</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.orders.radiology') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">radiology</span>
<span class="nav-label" data-i18n="طلبات الأشعة">طلبات الأشعة</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.orders.results') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">assignment_turned_in</span>
<span class="nav-label" data-i18n="النتائج">النتائج</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.clinical.referrals') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">sync_alt</span>
<span class="nav-label" data-i18n="الإحالات والمتابعات">الإحالات والمتابعات</span>
</a>
</div>
<!-- Group 3: الإدارة -->
<div class="space-y-1">
<p class="group-heading px-3 text-[13px] font-bold text-[#8EA3BA] uppercase tracking-wider mb-2" data-i18n="الإدارة">الإدارة</p>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.practice.services') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">receipt_long</span>
<span class="nav-label" data-i18n="الخدمات والأسعار">الخدمات والأسعار</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.practice.schedule') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">schedule</span>
<span class="nav-label" data-i18n="الدوام والجدول">الدوام والجدول</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.finance.collections') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">account_balance_wallet</span>
<span class="nav-label" data-i18n="التحصيل والمستحقات">التحصيل والمستحقات</span>
</a>
<a class="nav-item flex items-center gap-3 px-4 h-[46px] rounded-xl transition-all text-base text-slate-300 hover:text-white hover:bg-white/5 font-semibold" href="{{ route('doctor.account.settings') }}">
<span class="material-symbols-outlined text-[20px] flex-shrink-0">settings</span>
<span class="nav-label" data-i18n="الإعدادات">الإعدادات</span>
</a>
</div>
</div>
<!-- Fixed Doctor Footer Profile -->
<footer class="relative bg-[#132038] border-t border-[rgba(255,255,255,0.08)] p-3 flex-shrink-0" id="sidebar-doctor-card">
<button type="button" aria-label="قائمة حساب الطبيب" aria-expanded="false" aria-controls="doctor-account-menu" class="doctor-card-body flex items-center justify-between cursor-pointer rounded-xl p-1.5 hover:bg-white/5 transition-all w-full text-right" id="doctor-card-trigger">
<div class="flex items-center gap-2.5 overflow-hidden">
<div class="relative flex-shrink-0">
<div class="w-10 h-10 rounded-full bg-[#203254] text-white flex items-center justify-center font-bold text-xs border border-white/20">{{ mb_substr(preg_replace('/^د\.\s*/u', '', $doctor->name), 0, 1) }}</div>
<span data-doctor-availability-dot class="absolute bottom-0 left-0 w-2.5 h-2.5 border-2 border-[#132038] rounded-full" style="background-color:{{ ['available'=>'#10b981','busy'=>'#f59e0b'][$doctor->accountPreferences()['availability']] ?? '#94a3b8' }}"></span>
</div>
<div class="doctor-meta flex flex-col text-right overflow-hidden">
<span class="text-white text-sm font-bold truncate">{{ $doctor->name }}</span>
<span class="text-xs text-slate-400 truncate">{{ $doctor->professional_title ?: 'طبيب' }}</span>
<span data-doctor-availability-label data-i18n="{{ App\Models\Doctor::AVAILABILITY[$doctor->accountPreferences()['availability']] }}" class="text-[11px] text-slate-300 font-medium">{{ App\Models\Doctor::AVAILABILITY[$doctor->accountPreferences()['availability']] }}</span>
</div>
</div>
<span class="doctor-meta text-slate-400 p-1" aria-hidden="true">
<span class="material-symbols-outlined text-lg">more_vert</span>
</span>
</button>
<!-- Doctor Menu Dropdown Popover -->
<div class="hidden absolute bottom-full mb-2 right-3 w-56 bg-[#132038] rounded-xl shadow-dropdown border border-[rgba(255,255,255,0.08)] py-1.5 z-50 text-slate-200 text-sm" id="doctor-account-menu">
<button data-doctor-destination="profile" class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-slate-400">person</span>
<span class="" data-i18n="الملف الشخصي">الملف الشخصي</span>
</button>
<button data-doctor-destination="settings" class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
<span class="material-symbols-outlined text-lg text-slate-400">manage_accounts</span>
<span class="" data-i18n="إعدادات الحساب">إعدادات الحساب</span>
</button>
<button data-doctor-destination="availability" class="w-full text-right px-4 py-2 hover:bg-white/5 flex items-center gap-2 text-slate-200" type="button">
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
</aside>
