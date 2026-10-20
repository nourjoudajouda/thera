<div class="row g-3 appointments-daily-metrics weekly-metrics">
    @foreach([['مواعيد الأسبوع',$summary['total'],'موعدًا مسجلًا','sky','calendar_month'],['جاهزون وينتظرون',$summary['ready'],'حضور مسجل والمبلغ مسدد','green','how_to_reg'],['خانات متاحة','—','اختر الخدمة والتاريخ لمعاينة التوفر','amber','event_available']] as [$label,$number,$hint,$tone,$icon])
        <div class="col-12 col-md-4"><div class="appointments-month-metric metric-{{ $tone }}"><div><p>{{ $label }}</p><strong class="font-num">{{ $number }}</strong> <small>{{ $hint }}</small></div><span class="metric-symbol"><span class="material-symbols-outlined">{{ $icon }}</span></span></div></div>
    @endforeach
</div>
<section class="appointments-week-grid" aria-label="مواعيد الأسبوع">
    @foreach($calendarDays as $calendarDay)
        @php($isToday=$calendarDay['date']->isToday())
        <article @class(['weekly-day-card','today'=>$isToday])>
            <header class="weekly-day-head"><div><h2>{{ App\Models\DoctorSchedule::DAYS[$calendarDay['date']->dayOfWeek] }} @if($isToday)<span class="weekly-today">اليوم</span>@endif</h2><p>{{ $calendarDay['date']->day }} {{ $calendarDay['date']->locale('ar')->translatedFormat('F') }}</p></div><span class="weekly-count">{{ $calendarDay['appointments']->count() }} مواعيد</span></header>
            <div class="weekly-day-body">
                @forelse($calendarDay['appointments'] as $item)
                    <a class="weekly-appt-item" href="{{ route('doctor.appointments.show',['appointment'=>$item->id]) }}">
                        <div class="weekly-appointment-heading"><div><strong>{{ $item->patient_name }}</strong><small><span class="font-num">#{{ $item->id }}</span> · {{ $item->service_name }}</small></div><time class="font-num">{{ $item->starts_at->setTimezone(config('thera.schedule_timezone'))->format('h:i') }} {{ $item->starts_at->setTimezone(config('thera.schedule_timezone'))->hour<12?'ص':'م' }}</time></div>
                        <div class="timeline-badges">@include('doctor.appointments.partials.appointment-badges')</div>
                    </a>
                @empty
                    <div class="weekly-empty">لا توجد مواعيد مسجلة لهذا اليوم ضمن الفلاتر المختارة.</div>
                @endforelse
                <a class="weekly-day-link" href="{{ route('doctor.appointments.index',['view'=>'list','date'=>$calendarDay['date']->toDateString()]) }}">عرض اليوم<span class="material-symbols-outlined">arrow_back</span></a>
            </div>
        </article>
    @endforeach
</section>
