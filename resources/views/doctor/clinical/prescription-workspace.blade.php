<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>تفاصيل الوصفة - THERA</title>@include('doctor.partials.workspace-assets')<link rel="stylesheet" href="{{ asset('css/doctor-visits.css') }}"></head><body>
@include('doctor.partials.workspace-sidebar')@include('doctor.partials.workspace-topbar')
@php($readonly=(bool)$prescription->approved_at || ((bool)$visit->closed_at && !$prescription->corrects_prescription_id))
@php($identity=$prescription->issued_snapshot ?? $prescription->correctedFrom?->issued_snapshot ?? [])
@php($superseded=(bool)$prescription->correction?->approved_at)
<main id="main-content-layout">
    <header class="visit-page-heading"><div><nav class="small text-secondary mb-2">العمل الطبي / الوصفات / RX-{{ $prescription->id }}</nav><h1>الوصفة الطبية</h1><p>مرتبطة بالزيارة #{{ $visit->id }} · الموعد #{{ $appointment->id }}</p></div><a class="btn btn-light border" href="{{ route('doctor.clinical.visit',['visit'=>$visit->id]) }}">العودة للزيارة</a></header>
    <section class="service-card visit-patient-banner mb-4"><span class="visit-avatar material-symbols-outlined" aria-hidden="true">person</span><div><h2>{{ $identity['patient_name'] ?? $appointment->patient_name }}</h2><p dir="ltr" class="font-num">{{ $identity['patient_phone'] ?? $appointment->patient_phone }}</p></div><div class="visit-appointment-context"><strong>{{ $identity['facility_name'] ?? $appointment->doctorFacility->facility->name }}</strong><p>هوية المريض مرتبطة بالزيارة ولا تتغير داخل التصحيح.</p></div><span class="visit-status {{ $prescription->approved_at && !$superseded ? 'visit-tone-completed' : '' }}">{{ $superseded ? 'نسخة سابقة — للقراءة فقط' : ($prescription->approved_at ? 'معتمدة — للقراءة فقط' : 'مسودة غير معتمدة') }}</span></section>
    @if($prescription->correctedFrom)
        <section class="alert alert-info rounded-4 small"><strong>إصدار تصحيحي رقم {{ $prescription->document_version }}</strong><p class="mt-2 mb-1">سبب التصحيح: {{ $prescription->correction_reason }}</p><a href="{{ route('doctor.clinical.prescriptions.show', $prescription->correctedFrom) }}">عرض النسخة السابقة RX-{{ $prescription->corrects_prescription_id }}</a>@unless($prescription->approved_at)<p class="mb-0 mt-2">هذه مسودة تصحيح فقط. الوصفة السابقة تبقى المعتمدة حتى اعتماد هذا الإصدار. لا يغير التصحيح حالة الزيارة أو التحصيل أو أي صرف سابق.</p>@endunless</section>
    @endif
    @if($prescription->correction)
        <section class="alert {{ $prescription->correction->approved_at ? 'alert-warning' : 'alert-info' }} rounded-4 small"><strong>{{ $prescription->correction->approved_at ? 'نسخة سابقة — صدر إصدار تصحيحي معتمد بدلًا منها.' : 'توجد مسودة تصحيح غير معتمدة؛ هذه النسخة ما زالت معتمدة.' }}</strong><a class="d-block mt-2" href="{{ route('doctor.clinical.prescriptions.show', $prescription->correction) }}">{{ $prescription->correction->approved_at ? 'عرض الإصدار التصحيحي' : 'متابعة مسودة التصحيح' }}</a></section>
    @endif
    <div class="alert alert-info small rounded-4">الوصفة واعتمادها بيد الطبيب. الدواء غير المدرج خاص بهذه الوصفة ولا يضاف للاقتراحات. يمكن معاينة الوصفة المعتمدة وطباعتها أو حفظها PDF من نافذة الطباعة. الإرسال للمريض أو الصيدلية غير مفعل بعد.</div>
    @if($visit->closed_at && !$prescription->approved_at && !$prescription->corrects_prescription_id)<div class="alert alert-warning small">أُغلقت الزيارة قبل اعتماد الوصفة. بقيت المسودة غير معتمدة وللقراءة فقط، ولا تعتبر وصفة صادرة.</div>@endif
    <div class="row g-4"><div class="col-12 col-xl-8">@include('doctor.clinical.prescription-editor')</div>
    <aside class="col-12 col-xl-4">
        @if($prescription->approved_at && !$prescription->correction)
            <section class="service-card visit-aside mb-4"><h2>تصحيح الوصفة المعتمدة</h2><p>لا تعدّل النسخة الأصلية. وضح السبب لفتح مسودة مستقلة، ثم راجعها واعتمدها. يظل الأصل محفوظًا.</p>
                <form action="{{ route('doctor.clinical.prescriptions.correct', $prescription) }}" method="POST" data-ajax-follow>
                    @csrf<input type="hidden" name="revision" value="{{ $prescription->revision }}">
                    <label for="rx-correction-reason" class="form-label">سبب التصحيح</label><textarea class="form-control mb-3" id="rx-correction-reason" name="reason" rows="3" required minlength="10" maxlength="1000" placeholder="وضح ما يحتاج للتصحيح وسببه…"></textarea>
                    <button type="submit" class="btn btn-outline-primary w-100">إنشاء إصدار تصحيحي</button>
                </form>
            </section>
        @endif
        <section class="service-card visit-aside mb-4">
            <h2>الختم والطباعة</h2>
            @if($prescription->approved_at)
                <p>بيانات الوصفة من الإصدار المحفوظ. اختر بدون ختم أو أحد أختامك في معاينة الطباعة.</p>
                <a class="btn btn-primary w-100 mb-3" target="_blank" rel="noopener" href="{{ route('doctor.clinical.prescriptions.print', $prescription) }}">معاينة وطباعة الوصفة</a>
            @else
                <p>أضف أختامك من الإعدادات. بعد اعتماد الوصفة يمكنك اختيار الختم لنسخة الطباعة؛ الطباعة غير متاحة للمسودة.</p>
            @endif
            <a class="btn btn-outline-primary w-100" href="{{ route('doctor.account.settings', ['tab' => 'stamps']) }}">إدارة الأختام من الإعدادات</a>
        </section>
        <section class="service-card visit-aside mb-4"><h2>دواء غير موجود؟</h2><p>اختر «كتابة دواء خاص بهذه الوصفة» في سطر الدواء، ثم أدخل تفاصيله. لا يرسل للإدارة ولا يظهر لاحقًا في اقتراحاتك أو اقتراحات أطباء آخرين.</p></section>
        <section class="service-card visit-aside"><h2>سجل الحفظ والطباعة</h2>@foreach($prescription->revisions as $entry)<article class="visit-revision"><strong>{{ ['created'=>'إنشاء المسودة','correction_created'=>'إنشاء مسودة تصحيح','saved'=>'حفظ المسودة','approved'=>'اعتماد الوصفة','print_previewed'=>'فتح معاينة الطباعة','print_requested'=>'طلب فتح نافذة الطباعة'][$entry->action] ?? $entry->action }}</strong><small>مراجعة الحفظ {{ $entry->revision }} · {{ $entry->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }}</small></article>@endforeach</section>
    </aside></div>
</main>
<script type="application/json" id="practice-page-state">{}</script>@include('doctor.appointments.partials.scripts')<script src="{{ asset('js/doctor-visits.js') }}" defer></script></body></html>
