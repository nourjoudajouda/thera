<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>وصفة RX-{{ $prescription->id }} — THERA</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/prescription-print.css') }}">
    @include('alerts')
    <script src="{{ asset('js/prescription-print.js') }}" defer></script>
</head>
<body>
    <nav class="print-toolbar" aria-label="إجراءات الوصفة">
        <div><strong>{{ $prescription->correction?->approved_at ? 'معاينة نسخة سابقة' : 'معاينة الإصدار المعتمد' }}</strong><small>اختر «حفظ كملف PDF» داخل نافذة الطباعة لتنزيل نسخة.</small></div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ route('doctor.clinical.prescriptions.show', $prescription) }}">العودة للوصفة</a>
            <button type="button" class="btn btn-primary" id="prescription-print-button" data-url="{{ route('doctor.clinical.prescriptions.print.store', $prescription) }}">طباعة / حفظ PDF</button>
        </div>
    </nav>
    @include('doctor.clinical.print-stamp-controls')
    <main class="prescription-document">
        @if($prescription->correction?->approved_at)<div class="alert alert-warning mb-4"><strong>نسخة سابقة — صدر إصدار تصحيحي معتمد RX-{{ $prescription->correction->id }} بدلًا منها.</strong><p class="mb-0">هذه النسخة محفوظة للتتبع. راجع أحدث إصدار قبل الاستخدام.</p></div>@endif
        @if($prescription->corrects_prescription_id)<div class="alert alert-info mb-4"><strong>إصدار تصحيحي رقم {{ $prescription->document_version }} للوصفة RX-{{ $prescription->corrects_prescription_id }}</strong><p class="mb-0">سبب التصحيح: {{ $prescription->correction_reason }}</p></div>@endif
        <header class="document-heading">
            <div><span class="document-eyebrow">وصفة طبية</span><h1>{{ $snapshot['doctor_name'] ?? 'اسم الطبيب غير محفوظ' }}</h1><p>{{ $snapshot['professional_title'] ?? '' }}</p><p>{{ $snapshot['specialty'] ?? 'التخصص غير محفوظ في هذا الإصدار' }}</p></div>
            <div class="document-brand">@if($snapshot['logo_data_uri'] ?? null)<img src="{{ $snapshot['logo_data_uri'] }}" alt="THERA">@else<strong>THERA</strong>@endif</div>
        </header>
        <section class="document-context" aria-label="بيانات الوصفة">
            <div><span>اسم المريض</span><strong>{{ $snapshot['patient_name'] ?? 'غير محفوظ' }}</strong></div>
            <div><span>رقم التواصل</span><strong dir="ltr">{{ $snapshot['patient_phone'] ?? '—' }}</strong></div>
            <div><span>الجهة المرتبطة بالزيارة</span><strong>{{ $snapshot['facility_name'] ?? 'غير محفوظة' }}</strong></div>
            <div><span>تاريخ الزيارة</span><strong dir="ltr">{{ $snapshot['visit_date'] ?? 'غير محفوظ' }}</strong></div>
            <div><span>رقم المستند / إصدار الوصفة</span><strong>RX-{{ $prescription->id }} / {{ $prescription->document_version }}</strong></div>
            <div><span>تاريخ الاعتماد — توقيت فلسطين</span><strong dir="ltr">{{ $prescription->approved_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }}</strong></div>
        </section>
        <section aria-labelledby="medications-heading">
            <h2 id="medications-heading">الأدوية والتعليمات</h2>
            @foreach($prescription->content['items'] ?? [] as $item)
                <article class="document-medication">
                    <header><span class="medication-number">{{ $loop->iteration }}</span><div><h3>{{ $item['name'] ?? '' }}</h3><p>{{ $item['active_ingredient'] ?? '' }} · {{ $item['strength'] ?? '' }} · {{ $item['dosage_form'] ?? '' }}</p></div></header>
                    <dl class="medication-details">
                        <div><dt>الجرعة</dt><dd>{{ $item['dose'] ?? '—' }}</dd></div>
                        <div><dt>التكرار</dt><dd>{{ $item['frequency'] ?? '—' }}</dd></div>
                        <div><dt>المدة</dt><dd>{{ $item['duration'] ?? '—' }}</dd></div>
                        <div><dt>الكمية</dt><dd>{{ $item['quantity'] ?? '—' }}</dd></div>
                    </dl>
                    @if($item['instructions'] ?? null)<div class="document-instructions"><strong>تعليمات الاستخدام</strong><p>{{ $item['instructions'] }}</p></div>@endif
                </article>
            @endforeach
        </section>
        @if($prescription->content['notes'] ?? null)<section class="document-notes"><h2>ملاحظات الطبيب</h2><p>{{ $prescription->content['notes'] }}</p></section>@endif
        <footer class="document-footer">
            @include('doctor.clinical.print-stamp-preview')
            <div><strong>RX-{{ $prescription->id }} · الإصدار {{ $prescription->document_version }} · حفظ {{ $prescription->revision }}</strong><p>مرجع الزيارة #{{ $snapshot['visit_id'] ?? '—' }}</p><small>إصدار الوصفة أو طباعتها لا يعني صرف الدواء أو إرساله للمريض.</small></div>
        </footer>
    </main>
</body>
</html>
