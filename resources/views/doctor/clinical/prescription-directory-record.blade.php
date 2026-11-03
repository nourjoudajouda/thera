@php($identity=$prescription->issued_snapshot ?? $prescription->correctedFrom?->issued_snapshot ?? [])
@php($patientName=$identity['patient_name'] ?? $prescription->doctorVisit->doctorAppointment->patient_name)
@php($state=$prescription->correction?->approved_at?'نسخة سابقة':($prescription->approved_at?'موثقة':'مسودة'))
@if($mobile ?? false)
<article class="document-mobile-record"><header><h2>{{ $patientName }}</h2><span class="document-status {{ $prescription->approved_at?'approved':'' }}">{{ $state }}</span></header><p>RX-{{ $prescription->id }} · زيارة #{{ $prescription->doctor_visit_id }} · إصدار {{ $prescription->document_version }}</p><p>{{ $identity['facility_name'] ?? $prescription->doctorVisit->doctorAppointment->doctorFacility->facility->name }} · {{ $prescription->created_at->format('Y/m/d') }}</p><footer><small>جهة الصرف غير محددة · لم تُرسل للصرف</small><a href="{{ route('doctor.clinical.prescriptions.show',$prescription) }}">عرض التفاصيل</a></footer></article>
@else
<tr><td><div class="document-patient"><span class="document-avatar" aria-hidden="true">{{ mb_substr($patientName,0,1) }}</span><div><strong>{{ $patientName }}</strong><small dir="ltr">{{ $identity['patient_phone'] ?? $prescription->doctorVisit->doctorAppointment->patient_phone }}</small></div></div></td>
<td><strong>{{ collect($prescription->content['items'] ?? [])->take(2)->pluck('name')->filter()->join('، ') ?: 'وصفة طبية' }}</strong><small class="font-num">RX-{{ $prescription->id }} · إصدار {{ $prescription->document_version }}</small></td>
<td class="font-num">{{ $prescription->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d') }}</td>
<td><a href="{{ route('doctor.clinical.visit',['visit'=>$prescription->doctor_visit_id]) }}">زيارة #{{ $prescription->doctor_visit_id }}</a><small>{{ $identity['facility_name'] ?? $prescription->doctorVisit->doctorAppointment->doctorFacility->facility->name }}</small></td>
<td><span class="document-status {{ $prescription->approved_at?'approved':'' }}">{{ $state }}</span></td><td class="text-secondary">غير محددة</td><td><span class="document-status">غير متوفر</span></td>
<td><a href="{{ route('doctor.clinical.prescriptions.show',$prescription) }}">{{ $prescription->approved_at?'عرض التفاصيل':'متابعة المسودة' }}</a></td></tr>
@endif
