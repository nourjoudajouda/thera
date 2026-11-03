<!DOCTYPE html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الرئيسية - THERA</title>
@include('doctor.partials.workspace-assets')
<link rel="stylesheet" href="{{ asset('css/doctor-dashboard.css') }}"></head><body>
@include('doctor.partials.workspace-sidebar')
@include('doctor.partials.workspace-topbar')
<main id="main-content-layout" class="doctor-template-surface doctor-home-template"><div class="flex flex-col w-full gap-6">
<!-- HEALTHCARE HERO BANNER (Executive clean medical styling with soft blue-slate tint #EEF4FA and border #CBD5E1) -->
<div class="relative overflow-hidden rounded-2xl bg-[#EEF4FA] border border-[#CBD5E1] px-6 py-5 shadow-sm flex items-center justify-between flex-wrap gap-4">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-3">
<h1 class="text-2xl font-bold text-[#0B1020]">أهلًا، {{ $doctor->name }}</h1>
</div>
<p class="text-sm font-medium text-[#475569]">{{ $counts['appointments'] }} موعد اليوم • {{ $counts['waiting'] }} في الانتظار</p>
</div>
<div class="text-xs text-[#334155] flex items-center gap-1.5 font-semibold bg-white px-3 py-1.5 rounded-lg border border-[#CBD5E1] shadow-xs">
<span class="material-symbols-outlined text-[16px] text-[#0495F6]">calendar_today</span>
<span class="">{{ $today->locale('ar')->translatedFormat('l، j F Y') }}</span>
</div>
</div>
<!-- 5 OPERATIONAL KPI CARDS WITH RICHER VARIANT B TINTS -->
<section aria-label="مؤشرات الأداء التشغيلية" class="dashboard-original-metrics">
<!-- مواعيد اليوم -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#E0F2FE] border border-[#7DD3FC] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#0369A1] font-sans">{{ $counts['appointments'] }}</span>
<span class="text-xs font-bold text-[#0369A1] mt-0.5" data-i18n="مواعيد اليوم">مواعيد اليوم</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#7DD3FC] flex items-center justify-center text-[#0369A1] shadow-xs">
<span class="material-symbols-outlined text-[20px]">calendar_today</span>
</div>
</div>
<!-- في الانتظار -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#FEF3C7] border border-[#FCD34D] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#B45309] font-sans">{{ $counts['waiting'] }}</span>
<span class="text-xs font-bold text-[#B45309] mt-0.5">في الانتظار</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#FCD34D] flex items-center justify-center text-[#B45309] shadow-xs">
<span class="material-symbols-outlined text-[20px]">hourglass_top</span>
</div>
</div>
<!-- جاهزون للدخول -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#D1FAE5] border border-[#6EE7B7] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#047857] font-sans">{{ $counts['ready'] }}</span>
<span class="text-xs font-bold text-[#047857] mt-0.5">جاهزون للدخول</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#6EE7B7] flex items-center justify-center text-[#047857] shadow-xs">
<span class="material-symbols-outlined text-[20px]">check_circle</span>
</div>
</div>
<!-- نتائج للمراجعة -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#EDE9FE] border border-[#C4B5FD] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#6D28D9] font-sans">{{ $counts['reports'] }}</span>
<span class="text-xs font-bold text-[#6D28D9] mt-0.5">نتائج للمراجعة</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#C4B5FD] flex items-center justify-center text-[#6D28D9] shadow-xs">
<span class="material-symbols-outlined text-[20px]">assignment</span>
</div>
</div>
<!-- متابعات قادمة -->
<div class="flex items-center justify-between p-4 rounded-xl bg-[#CFFAFE] border border-[#67E8F9] shadow-sm">
<div class="flex flex-col">
<span class="text-2xl font-bold text-[#0E7490] font-sans">{{ $counts['followups'] }}</span>
<span class="text-xs font-bold text-[#0E7490] mt-0.5">متابعات قادمة</span>
</div>
<div class="w-9 h-9 rounded-lg bg-white/90 border border-[#67E8F9] flex items-center justify-center text-[#0E7490] shadow-xs">
<span class="material-symbols-outlined text-[20px]">swap_horiz</span>
</div>
</div>
</section>
<!-- MAIN OPERATIONAL AREA: 2-COLUMN RATIO (~2/3 and 1/3) -->
<div class="dashboard-original-panels">
<!-- جاهزون الآن -->
<div class="flex flex-col">
<div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-4 h-full flex flex-col justify-between">
<div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F1F5F9]">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-[#047857]">how_to_reg</span>
<h2 class="text-sm font-bold text-[#0B1020]">جاهزون الآن</h2>
</div>
<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#D1FAE5] text-[#047857] border border-[#6EE7B7]/60">{{ $counts['ready'] }} مرضى</span>
</div>
<div class="space-y-2.5 flex-1 flex flex-col justify-center">
@forelse($ready as $appointment)
<div class="dashboard-ready-row p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between gap-3">
<div class="flex items-center gap-3 min-w-0">
<div class="w-8 h-8 rounded-full bg-[#0495F6]/10 text-[#0495F6] font-bold flex items-center justify-center text-xs shrink-0">{{ mb_substr($appointment->patient_name,0,1) }}</div>
<div class="flex items-center gap-2 flex-wrap"><span class="text-sm font-bold text-[#0B1020]">{{ $appointment->patient_name }}</span><span class="text-xs text-[#64748B] font-num">{{ $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->format('H:i') }}</span><span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#D1FAE5] text-[#047857]">{{ $appointment->queueState()==='called'?'تم الاستدعاء':'جاهز للطبيب' }}</span></div></div>
<a class="btn btn-sm {{ $appointment->queueState()==='called'?'btn-primary':'btn-light border' }}" href="{{ route('doctor.appointments.show',['appointment'=>$appointment->id]) }}">{{ $appointment->queueState()==='called'?'فتح الزيارة':'استدعاء' }}</a>
</div>
@empty<p class="text-sm text-[#64748B] text-center py-4 mb-0">لا يوجد مرضى جاهزون الآن.</p>@endforelse
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
<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#E0F2FE] text-[#0369A1] border border-[#7DD3FC]/60">{{ $alertCount }}</span>
</div>
<div class="space-y-2.5 flex-1 flex flex-col justify-center">
<div class="p-3 rounded-xl bg-[#EDE9FE] border border-[#C4B5FD] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#6D28D9] shrink-0">assignment</span>
<span class="text-xs font-bold text-[#4C1D95]">{{ $counts['reports'] }} تقارير خارجية تحتاج مراجعة</span>
</div>
<div class="p-3 rounded-xl bg-[#FEF3C7] border border-[#FCD34D] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#B45309] shrink-0">hourglass_top</span>
<span class="text-xs font-bold text-[#78350F]">{{ $counts['waiting'] }} مرضى في الانتظار</span>
</div>
<div class="p-3 rounded-xl bg-[#CFFAFE] border border-[#67E8F9] flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#0E7490] shrink-0">swap_horiz</span>
<span class="text-xs font-bold text-[#164E63]">{{ $counts['followups'] }} متابعات قادمة خلال 7 أيام</span>
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
<div class="dashboard-original-shortcuts">
<!-- موعد لمريض جديد -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0495F6] hover:bg-[#F0F7FF] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.appointments.create') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0495F6] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">person_add</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0495F6] transition-colors truncate w-full" data-i18n="إضافة مريض">إضافة مريض</span>
</a>
<!-- حجز موعد -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0284C7] hover:bg-[#F0FDFA] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.appointments.create') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0284C7] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">calendar_today</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0284C7] transition-colors truncate w-full">حجز موعد</span>
</a>
<!-- فتح زيارة -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0495F6] hover:bg-[#F0F7FF] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.clinical.visits') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0495F6] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">stethoscope</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0495F6] transition-colors truncate w-full">فتح زيارة</span>
</a>
<!-- وصفة طبية -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#0466F1] hover:bg-[#EEF2FF] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.clinical.prescriptions') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#0466F1] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">description</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#0466F1] transition-colors truncate w-full" data-i18n="وصفة طبية">وصفة طبية</span>
</a>
<!-- طلب مختبر -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#047857] hover:bg-[#ECFDF5] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.orders.lab') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#047857] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">science</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#047857] transition-colors truncate w-full" data-i18n="طلب مختبر">طلب مختبر</span>
</a>
<!-- طلب أشعة -->
<a class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#6D28D9] hover:bg-[#FAF5FF] hover:shadow-xs transition-all group text-center" href="{{ route('doctor.orders.radiology') }}">
<div class="w-9 h-9 rounded-lg bg-white border border-[#E2E8F0] text-[#6D28D9] flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-[20px]">radiology</span>
</div>
<span class="text-xs font-bold text-[#0B1020] group-hover:text-[#6D28D9] transition-colors truncate w-full" data-i18n="طلب أشعة">طلب أشعة</span>
</a>
</div>
</section>
</div></main>
<script type="application/json" id="practice-page-state">{}</script>@include('doctor.appointments.partials.scripts')
</body></html>
