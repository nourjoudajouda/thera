@if($appointment->visit)
    <a class="btn btn-primary btn-sm" href="{{ route('doctor.clinical.visit',['visit'=>$appointment->visit->id]) }}">{{ $appointment->visit->closed_at?'عرض الزيارة':'متابعة الزيارة' }}</a>
@elseif($appointment->queueState()==='called')
    <form method="POST" action="{{ route('doctor.clinical.visits.start',$appointment) }}" data-ajax-redirect data-thera-confirm="بدء الزيارة الطبية؟" data-thera-confirm-text="تأكد من هوية المريض قبل البدء. ستفتح زيارة مرتبطة بهذا الموعد فقط.">@csrf<input type="hidden" name="revision" value="{{ $appointment->revision }}"><button type="submit" class="btn btn-primary btn-sm">بدء الزيارة الطبية</button></form>
@endif
