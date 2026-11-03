<div class="row g-3 appointments-daily-metrics">
    @foreach([['مواعيد اليوم',$summary['total'],'موعدًا مسجلًا','sky','calendar_today'],['بانتظار التحصيل',$summary['waiting'],'وصلوا وبقي عليهم مبلغ','amber','hourglass_top'],['جاهزون وينتظرون',$summary['ready'],($doctor->requiresCollectionBeforeVisit()?'حضور مسجل والمبلغ مسدد':'حضور مسجل؛ التحصيل غير مشروط'),'green','how_to_reg']] as [$label,$number,$hint,$tone,$icon])
        <div class="col-12 col-md-4"><div class="appointments-month-metric metric-{{ $tone }}"><div><p>{{ $label }}</p><strong class="font-num">{{ $number }}</strong> <small>{{ $hint }}</small></div><span class="metric-symbol"><span class="material-symbols-outlined">{{ $icon }}</span></span></div></div>
    @endforeach
</div>
<section class="appointments-timeline" aria-label="قائمة مواعيد اليوم الزمنية">
    <header class="timeline-header"><div><strong>قائمة مواعيد اليوم الزمنية</strong> <small>( مرتبة حسب الوقت )</small></div><div class="timeline-legend"><span class="text-success">حضور مسجل</span><span class="text-secondary">لم يسجل حضور</span></div></header>
    @forelse($appointments as $item)
        @php($localStart=$item->starts_at->setTimezone(config('thera.schedule_timezone')))
        <article @class(['timeline-row','has-arrived'=>$item->arrived_at && $item->status==='confirmed'])>
            <div class="timeline-patient-block">
                <div class="timeline-time font-num"><strong>{{ $localStart->format('H:i') }}</strong><small>{{ $localStart->hour<12?'صباحًا':'مساءً' }}</small></div>
                <div class="timeline-information">
                    <div class="timeline-patient-heading"><strong>{{ $item->patient_name }}</strong><span class="timeline-service" title="{{ $item->doctorFacility->facility->name }}">{{ $item->service_name }}</span></div>
                    <div class="timeline-badges">@include('doctor.appointments.partials.appointment-badges')<span class="timeline-reference font-num">#{{ $item->id }}</span></div>
                </div>
            </div>
            <div class="timeline-actions">
                @include('doctor.clinical.visit-actions',['appointment'=>$item])
                @if($item->queueState()==='ready')<form method="POST" action="{{ route('doctor.appointments.queue.store',$item) }}" data-thera-confirm="استدعاء المريض؟" data-thera-confirm-text="سيُسجل الاستدعاء فقط، ولن تُفتح زيارة طبية تلقائيًا.">@csrf<input type="hidden" name="revision" value="{{ $item->revision }}"><button class="timeline-action action-primary" type="submit"><span class="material-symbols-outlined">record_voice_over</span>استدعاء المريض</button></form>@endif
                @if($item->remainingMinor()>0 && in_array($item->status,['confirmed','completed']))<a class="timeline-action" href="{{ route('doctor.appointments.index',array_merge(request()->except('collection'),['collection'=>$item->id])) }}"><span class="material-symbols-outlined">payments</span>تسجيل تحصيل</a>@endif
                @if($item->status==='confirmed' && !$item->arrived_at && $localStart->isToday())
                    <form method="POST" action="{{ route('doctor.appointments.status',$item) }}" data-thera-confirm="تسجيل حضور المريض يدويًا؟" data-thera-confirm-text="الحضور لا يسجل تحصيلًا أو يبدأ زيارة تلقائيًا.">@csrf @method('PATCH')<input type="hidden" name="revision" value="{{ $item->revision }}"><input type="hidden" name="action" value="arrived"><button class="timeline-action" type="submit"><span class="material-symbols-outlined">how_to_reg</span>تسجيل الوصول</button></form>
                @endif
                <a href="{{ route('doctor.appointments.show',['appointment'=>$item->id]) }}" @class(['timeline-action','action-primary'=>$item->arrived_at && $item->status==='confirmed'])><span class="material-symbols-outlined">event_note</span>تفاصيل الموعد</a>
            </div>
        </article>
    @empty
        <div class="appointments-empty"><span class="material-symbols-outlined">event_busy</span><strong>لا توجد مواعيد ضمن التاريخ والفلاتر المختارة.</strong><a class="timeline-action" href="{{ route('doctor.appointments.create') }}">إضافة موعد</a></div>
    @endforelse
</section>
