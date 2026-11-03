@php($inlineRecord = $prescription ?? $order ?? $document)
@php($inlineTitle = ['prescription'=>'الوصفة الطبية','lab'=>'طلب مختبر','radiology'=>'طلب أشعة','referral'=>'إحالة','followup'=>'خطة متابعة'][$inlineKind])
@php($readonly = (bool)$inlineRecord->approved_at || ((bool)$visit->closed_at && !($inlineRecord->corrects_prescription_id ?? $inlineRecord->corrects_document_id)))
<section data-inline-document data-inline-visit="{{ $visit->id }}" data-inline-kind="{{ $inlineKind }}" data-inline-title="{{ $inlineTitle }}" data-inline-readonly="{{ $readonly?'1':'0' }}">
<div class="inline-document-patient"><span class="material-symbols-outlined" aria-hidden="true">person</span><div><strong>{{ $inlineRecord->issued_snapshot['patient_name'] ?? $appointment->patient_name }}</strong><small>زيارة #{{ $visit->id }} · موعد #{{ $appointment->id }} · {{ $inlineRecord->issued_snapshot['facility_name'] ?? $appointment->doctorFacility->facility->name }}</small></div><span class="visit-status {{ $inlineRecord->approved_at?'visit-tone-completed':'' }}">{{ $inlineRecord->approved_at?'معتمد — للقراءة فقط':'مسودة غير معتمدة' }}</span></div>
@if($inlineRecord->correction?->approved_at)<div class="alert alert-warning small">نسخة سابقة — صدر إصدار تصحيحي معتمد بدلًا منها.</div>@endif
@if($visit->closed_at && !$inlineRecord->approved_at && $readonly)<div class="alert alert-warning small">أُغلقت الزيارة؛ المسودة للقراءة فقط ولا تعتبر مستندًا صادرًا.</div>@endif
@if($inlineKind==='prescription')
<p class="inline-document-notice">الدواء غير المدرج خاص بهذه الوصفة ولا يُضاف للاقتراحات. الاعتماد لا يرسل الوصفة للمريض أو الصيدلية ولا يسجل صرفًا.</p>
@include('doctor.clinical.prescription-editor')
@elseif(in_array($inlineKind,['lab','radiology']))
<p class="inline-document-notice">الفحوصات المكتوبة خاصة بهذا الطلب. الاعتماد يحفظ المستند دون إرسال أو حجز أو إنشاء نتيجة.</p>
@include('doctor.orders.diagnostic-editor')
@else
<p class="inline-document-notice">المستند مرتبط بالزيارة الحالية. الاعتماد لا يرسل إحالة ولا يحجز موعدًا ولا يمنح جهة أخرى صلاحية لملف المريض.</p>
@include('doctor.clinical.referral-editor')
@endif
@if($inlineRecord->approved_at)<div class="inline-document-print"><a class="btn btn-outline-primary" target="_blank" rel="noopener" href="{{ $inlineKind==='prescription' ? route('doctor.clinical.prescriptions.print',$inlineRecord) : (in_array($inlineKind,['lab','radiology']) ? route('doctor.orders.print',$inlineRecord) : route('doctor.clinical.referrals.print',$inlineRecord)) }}">معاينة وطباعة / PDF</a></div>@endif
<details class="inline-document-history"><summary>سجل الحفظ والإصدارات</summary>@foreach($inlineRecord->revisions as $entry)<p class="small text-secondary mb-2">{{ ['created'=>'إنشاء المسودة','saved'=>'حفظ المسودة','approved'=>'اعتماد المستند','correction_created'=>'إنشاء تصحيح','print_previewed'=>'معاينة الطباعة','print_requested'=>'طلب الطباعة'][$entry->action] ?? $entry->action }} · {{ $entry->revision }} · {{ $entry->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i') }}</p>@endforeach</details>
@if(in_array($inlineKind,['prescription','lab','radiology']))<section class="inline-document-destination"><h3>{{ $inlineKind==='prescription'?'جهة الصرف والإرسال':'جهة التنفيذ والإرسال' }}</h3><p>بدون تحديد جهة الآن</p><small>ربط {{ $inlineKind==='prescription'?'الصيدليات':'جهات التنفيذ' }} غير مفعّل حاليًا. الحفظ والاعتماد لا يرسلان المستند تلقائيًا.</small></section>@endif
</section>
