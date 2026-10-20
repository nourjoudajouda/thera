<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>المواعيد والجدول السريري - THERA</title>@include('doctor.partials.workspace-assets')<link rel="stylesheet" href="{{ asset('css/doctor-appointments.css') }}"></head><body>
@include('doctor.partials.workspace-sidebar')@include('doctor.partials.workspace-topbar')
<main id="main-content-layout" class="appointments-{{ $mode }}-view appointments-designed-view">
    @include('doctor.appointments.partials.toolbar')
    @if($mode==='month')
        @include('doctor.appointments.partials.month-calendar')
    @elseif($mode==='week')
        @include('doctor.appointments.partials.week-board')
    @else
        @include('doctor.appointments.partials.day-timeline')
    @endif
    @if($mode!=='month' && $appointments->hasPages())<div class="mt-3">{{ $appointments->links('pagination::bootstrap-5') }}</div>@endif
    <p class="appointments-integration-note">التوقيت: {{ config('thera.schedule_timezone') }}. الحضور وحده لا يعني الجاهزية: يلزم تسديد المتبقي للانتظار والاستدعاء. الاستدعاء لا يبدأ زيارة طبية تلقائيًا.</p>
</main>
<script type="application/json" id="practice-page-state">{}</script>@include('doctor.appointments.partials.scripts')
</body></html>
