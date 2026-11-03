@if($appointment->source === App\Models\DoctorAppointment::SOURCE_PATIENT_APP)
    <span class="appointment-status appointment-source-app" title="حجزه المريض من تطبيق THERA"><span class="material-symbols-outlined" aria-hidden="true">smartphone</span>من تطبيق المريض</span>
@endif
@if($appointment->is_test)
    <span class="appointment-status" title="موعد للتجربة ولا يمثل تحصيلًا حقيقيًا">تجريبي</span>
@endif
@if($appointment->doctor_facility_id)
    <a href="{{ route('doctor.facilities.check-in', $appointment->doctorFacility->facility_id) }}" target="_blank" rel="noopener">QR المنشأة</a>
@endif
