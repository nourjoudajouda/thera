@php($patientName=$order->issued_snapshot['patient_name'] ?? $order->doctorVisit->doctorAppointment->patient_name)
@php($tests=collect($order->content['items'] ?? []))
@php($state=$order->correction?->approved_at?'نسخة سابقة':($order->approved_at?'موثق':'مسودة'))
@php($reportState=$order->external_reports_count?'تقرير خارجي مرفوع':'لا يوجد تقرير')
@php($reviewState=$order->reviewed_reports_count ? ($order->reviewed_reports_count===$order->external_reports_count?'تمت المراجعة':'مراجعة جزئية') : ($order->external_reports_count?'بانتظار المراجعة':'—'))
@if($mobile ?? false)
<article class="document-mobile-record"><header><h2>{{ $patientName }}</h2><span class="document-status {{ $order->approved_at?'approved':'' }}">{{ $state }}</span></header><p>{{ $order->reference() }} · زيارة #{{ $order->doctor_visit_id }} · {{ $order->created_at->format('Y/m/d') }}</p><p>{{ $tests->take(2)->pluck('name')->join('، ') ?: 'المسودة لم تُستكمل بعد' }}</p><div><span class="document-status {{ $order->priority==='urgent'?'urgent':'' }}">{{ App\Models\DoctorDiagnosticOrder::PRIORITIES[$order->priority] }}</span> <span class="document-status">{{ $reportState }}</span></div><footer><small>{{ $reviewState }}</small><a href="{{ route('doctor.orders.show',$order) }}">عرض التفاصيل</a></footer></article>
@else
<tr><td><div class="document-patient"><span class="document-avatar" aria-hidden="true">{{ mb_substr($patientName,0,1) }}</span><div><strong>{{ $patientName }}</strong><small dir="ltr">{{ $order->issued_snapshot['patient_phone'] ?? $order->doctorVisit->doctorAppointment->patient_phone }}</small></div></div></td>
<td><strong>{{ $tests->take(2)->pluck('name')->join('، ') ?: 'مسودة طلب' }}</strong><small>{{ $order->reference() }} · إصدار {{ $order->document_version }} · {{ $tests->count() }} فحص</small></td>
@if($kind==='lab')<td class="font-num">{{ $order->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d') }}</td>@else<td>{{ $tests->pluck('body_area')->filter()->unique()->join('، ') ?: '—' }}</td>@endif
<td><a href="{{ route('doctor.clinical.visit',['visit'=>$order->doctor_visit_id]) }}">زيارة #{{ $order->doctor_visit_id }}</a><small>{{ $order->issued_snapshot['facility_name'] ?? $order->doctorVisit->doctorAppointment->doctorFacility->facility->name }}</small>@if($kind==='radiology')<small>{{ $order->created_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d') }}</small>@endif</td>
<td><span class="document-status {{ $order->priority==='urgent'?'urgent':'' }}">{{ App\Models\DoctorDiagnosticOrder::PRIORITIES[$order->priority] }}</span></td>
<td><span class="document-status {{ $order->approved_at?'approved':'' }}">{{ $state }}</span></td><td class="text-secondary">غير محددة</td>
@if($kind==='lab')<td class="text-secondary">لا توجد نتيجة THERA</td>@endif
<td><span class="document-status">{{ $reportState }}</span></td><td>{{ $reviewState }}</td>
<td><a href="{{ route('doctor.orders.show',$order) }}">{{ $order->approved_at?'عرض التفاصيل':'متابعة المسودة' }}</a></td></tr>
@endif
