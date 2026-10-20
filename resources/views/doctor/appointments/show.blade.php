<!DOCTYPE html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>تفاصيل الموعد - THERA</title>
@include('doctor.partials.workspace-assets')
<link rel="stylesheet" href="{{ asset('css/doctor-appointments.css') }}"><link rel="stylesheet" href="{{ asset('css/doctor-appointment-details.css') }}">
</head><body>
@include('doctor.partials.workspace-sidebar')
@include('doctor.partials.workspace-topbar')
<main id="main-content-layout" class="appointment-details-page">
    <header class="details-page-heading"><div><nav class="appointments-breadcrumb" aria-label="مسار الصفحة">المواعيد <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span><span class="current">تفاصيل الموعد</span></nav><h1>تفاصيل الموعد</h1><p>بيانات الحجز والحضور والتحصيل، وجميع التغييرات في مكان واحد.</p></div><a class="btn btn-light border" href="{{ route('doctor.appointments.index',['view'=>'list','date'=>$appointment?->starts_at->setTimezone(config('thera.schedule_timezone'))->toDateString()]) }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>العودة للمواعيد</a></header>
    @if(!$appointment)
        <div class="service-card text-center p-5"><span class="material-symbols-outlined text-primary fs-1">event</span><p class="text-secondary mt-3 mb-0">اختر موعدًا من قائمة المواعيد لعرض تفاصيله.</p></div>
    @else
        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8">
                <section class="service-card details-card mb-4" aria-labelledby="details-patient-name">
                    <header class="details-patient-header"><div class="d-flex align-items-center gap-3"><span class="details-avatar material-symbols-outlined" aria-hidden="true">person</span><div><p class="details-eyebrow">موعد رقم <span class="font-num">#{{ $appointment->id }}</span></p><h2 id="details-patient-name">{{ $appointment->patient_name }}</h2><p class="details-phone font-num" dir="ltr">{{ $appointment->patient_phone }}</p></div></div><span class="appointment-status status-{{ $appointment->status }}">{{ App\Models\DoctorAppointment::STATUSES[$appointment->status] }}</span></header>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6"><div class="details-booking-item"><span class="material-symbols-outlined" aria-hidden="true">domain</span><div><p>المنشأة</p><strong>{{ $appointment->doctorFacility->facility->name }}</strong></div></div></div>
                        <div class="col-12 col-sm-6"><div class="details-booking-item"><span class="material-symbols-outlined" aria-hidden="true">medical_services</span><div><p>الخدمة</p><strong>{{ $appointment->service_name }}</strong></div></div></div>
                        <div class="col-12 col-sm-6"><div class="details-booking-item"><span class="material-symbols-outlined" aria-hidden="true">calendar_today</span><div><p>التاريخ</p><strong class="font-num">{{ $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d') }}</strong></div></div></div>
                        <div class="col-12 col-sm-6"><div class="details-booking-item"><span class="material-symbols-outlined" aria-hidden="true">schedule</span><div><p>الوقت — {{ config('thera.schedule_timezone') }}</p><strong class="font-num" dir="ltr">{{ $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->format('H:i') }} – {{ $appointment->ends_at->setTimezone(config('thera.schedule_timezone'))->format('H:i') }}</strong></div></div></div>
                    </div>
                    @if($appointment->notes)<div class="details-notes"><span class="material-symbols-outlined" aria-hidden="true">sticky_note_2</span><div><h3>ملاحظة تنظيمية</h3><p>{{ $appointment->notes }}</p></div></div>@endif
                </section>
                <div class="d-flex gap-2 mb-4">@include('doctor.clinical.visit-actions')</div>
                @if($appointment->status==='confirmed' && !$appointment->visit)
                    <section class="service-card details-card mb-4" aria-labelledby="details-management-title">
                        <header class="details-section-heading"><span class="details-icon material-symbols-outlined" aria-hidden="true">tune</span><div><h2 id="details-management-title">إدارة الموعد</h2><p>سجّل حضور المريض أو أدر التغييرات على الحجز.</p></div></header>
                        <div class="details-primary-actions">
                            @if(!$appointment->arrived_at && $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->isToday())
                                <form method="POST" action="{{ route('doctor.appointments.status',$appointment) }}" data-ajax-follow data-thera-confirm="تسجيل حضور المريض يدويًا؟" data-thera-confirm-text="لن يغيّر هذا الإجراء حالة التحصيل أو يضيف المريض لقائمة انتظار جاهزة.">@csrf @method('PATCH')<input type="hidden" name="revision" value="{{ $appointment->revision }}"><input type="hidden" name="action" value="arrived"><button class="btn btn-success" type="submit"><span class="material-symbols-outlined" aria-hidden="true">how_to_reg</span>تسجيل الحضور</button></form>
                            @endif
                            @if(!$appointment->arrived_at && $appointment->starts_at->isFuture())<a class="btn btn-light border" href="{{ route('doctor.appointments.edit',$appointment) }}"><span class="material-symbols-outlined" aria-hidden="true">edit_calendar</span>إعادة الجدولة</a>@endif
                        </div>
                        <form class="details-status-form" method="POST" action="{{ route('doctor.appointments.status',$appointment) }}" data-ajax-follow data-thera-confirm="تحديث حالة الموعد؟" data-thera-confirm-text="سيُحفظ السبب في السجل. لا ينفذ هذا الإجراء أي استرداد أو حركة مالية.">
                            @csrf @method('PATCH')<input type="hidden" name="revision" value="{{ $appointment->revision }}">
                            <div class="row g-3 align-items-end"><div class="col-12 col-sm-5 col-lg-4"><label for="appointment-action" class="form-label">الإجراء</label><select class="form-select" id="appointment-action" name="action" data-appointment-select><option value="cancelled">إلغاء الموعد</option>@if(!$appointment->arrived_at && $appointment->ends_at->isPast())<option value="no_show">لم يحضر</option>@endif</select></div><div class="col-12 col-sm-7 col-lg-8"><label for="appointment-change-reason" class="form-label">السبب</label><input class="form-control" name="reason" id="appointment-change-reason" maxlength="500" required placeholder="اكتب سبب الإجراء"></div><div class="col-12 details-status-footer"><small>يُحفظ السبب ضمن سجل تغييرات الموعد.</small><button class="btn btn-outline-danger" type="submit">تطبيق الإجراء</button></div></div>
                        </form>
                    </section>
                @endif
                <section class="service-card details-card" aria-labelledby="details-history-title">
                    <header class="details-section-heading"><span class="details-icon material-symbols-outlined" aria-hidden="true">history</span><div><h2 id="details-history-title">سجل تغييرات الموعد</h2><p>تسلسل الإجراءات المحفوظة على هذا الموعد.</p></div><span class="details-event-count font-num">{{ $appointment->events->count() }}</span></header>
                    <div class="details-history-list">
                        @forelse($appointment->events as $event)
                            <article class="details-history-item"><span class="details-history-dot" aria-hidden="true"></span><div class="details-history-content"><div class="details-history-heading"><h3>{{ App\Models\DoctorAppointmentEvent::ACTIONS[$event->action] ?? $event->action }}</h3><time class="font-num" datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }}</time></div>@if($event->reason)<p class="details-history-reason">{{ $event->reason }}</p>@endif @if($event->action==='rescheduled')<div class="details-history-reschedule font-num" dir="ltr">{{ Carbon\CarbonImmutable::parse($event->before['starts_at'])->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }} → {{ Carbon\CarbonImmutable::parse($event->after['starts_at'])->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }}</div>@endif<small>بواسطة الطبيب صاحب الموعد</small></div></article>
                        @empty<p class="text-secondary small mb-0">لا توجد تغييرات مسجلة لهذا الموعد.</p>@endforelse
                    </div>
                </section>
            </div>
            <div class="col-12 col-xl-4 details-finance-column">@include('doctor.finance.partials.appointment-finance-summary')</div>
        </div>
    @endif
</main><script type="application/json" id="practice-page-state">{}</script>@include('doctor.appointments.partials.scripts')
</body></html>
