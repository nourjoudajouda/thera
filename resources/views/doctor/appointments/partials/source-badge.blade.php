@if($appointment->source === App\Models\DoctorAppointment::SOURCE_PATIENT_APP)
    <span class="appointment-status appointment-source-app" title="حجزه المريض من تطبيق THERA"><span class="material-symbols-outlined" aria-hidden="true">smartphone</span>من تطبيق المريض</span>
@endif
