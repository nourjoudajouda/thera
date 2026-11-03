<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorFacility;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use App\Models\Patient;
use App\Models\PatientDoctorConsent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorVisitPatientHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_shows_prior_patient_history_with_dates_sources_and_read_only_links_without_copying_content(): void
    {
        $patient = $this->patient();
        $prior = $this->visit($patient, 4, true, ['medical_history' => 'تاريخ مرضي سابق محفوظ', 'diagnosis' => 'تشخيص موثق سابق', 'diagnosis_type' => 'initial']);
        $rx = DoctorPrescription::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(4), 'content' => ['items' => [['name' => 'دواء سابق موثق']]]]);
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(4)]);
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id, 'title' => 'تقرير سابق محفوظ', 'source_name' => 'مختبر المصدر', 'report_date' => now()->subDays(3), 'reviewed_at' => now()->subDays(2)]);
        $current = $this->visit($patient);
        $before = $current->fresh()->getAttributes();

        $response = $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertSee('تاريخ مرضي سابق محفوظ')->assertSee('تشخيص موثق سابق')->assertSee('صفة التشخيص المسجلة: مبدئي')
            ->assertSee('دواء سابق موثق')->assertSee('تقرير سابق محفوظ')->assertSee('مختبر المصدر')
            ->assertSee('2026/10/29')->assertSee('تقرير خارجي — غير معتمد من THERA')
            ->assertSee('الوصفات السابقة لا تثبت الاستخدام الحالي')->assertSee('غياب المعلومات لا يعني عدم وجود حساسية')
            ->assertViewHas('patientHistory', fn ($history) => $history['histories']->modelKeys() === [$prior->id] && $history['prescriptions']->modelKeys() === [$rx->id] && $history['reports']->modelKeys() === [$report->id]);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertSee('target="_blank" rel="noopener"', false);
        $this->assertSame($before, $current->fresh()->getAttributes());
        $this->assertSame([], $current->content);
        $this->assertDatabaseCount('doctor_visit_revisions', 1);
        $this->assertNull($rx->fresh()->correction);
    }

    public function test_history_is_limited_to_the_linked_patient_and_current_doctor_not_identical_contacts_or_requested_ids(): void
    {
        $patient = $this->patient();
        $prior = $this->visit($patient, 3, true, ['medical_history' => 'تاريخ الملف الصحيح']);
        $sameContact = DoctorPatient::factory()->create(['doctor_id' => $patient->doctor_id, 'name' => $patient->name, 'phone' => $patient->phone]);
        $this->visit($sameContact, 2, true, ['medical_history' => 'بيانات ملف آخر مخفية']);
        $foreign = DoctorPatient::factory()->create(['name' => $patient->name, 'phone' => $patient->phone]);
        $this->visit($foreign, 2, true, ['medical_history' => 'بيانات طبيب آخر مخفية']);
        $unlinkedAppointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'arrived_at' => now()]);
        DoctorVisit::factory()->create(['doctor_appointment_id' => $unlinkedAppointment->id, 'started_at' => now()->subDay(), 'status' => 'completed', 'closed_at' => now()->subDay(), 'content' => ['assessment' => 'تقييم محفوظ', 'medical_history' => 'بيانات موعد غير مربوط مخفية']]);
        $current = $this->visit($patient);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id, 'patient' => $foreign->id]))->assertOk()
            ->assertSee('تاريخ الملف الصحيح')->assertDontSee('بيانات ملف آخر مخفية')->assertDontSee('بيانات طبيب آخر مخفية')->assertDontSee('بيانات موعد غير مربوط مخفية')
            ->assertViewHas('patientHistory', fn ($history) => $history['visits']->modelKeys() === [$prior->id] && $history['patient_id'] === $patient->id);
        $this->actingAs($foreign->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertNotFound();
    }

    public function test_unlinked_appointment_does_not_guess_a_patient_file_or_history_from_contacts(): void
    {
        $patient = $this->patient();
        $this->visit($patient, 2, true, ['medical_history' => 'معلومة لا يجوز تخمين صاحبها']);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone]);
        $current = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id]);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertSee('الموعد غير مربوط بملف مريض')->assertDontSee('معلومة لا يجوز تخمين صاحبها')
            ->assertViewHas('patientHistory', fn ($history) => ! $history['linked'] && $history['histories']->isEmpty());
    }

    public function test_context_excludes_future_and_current_visits_and_draft_medical_claims_but_labels_open_prior_visits(): void
    {
        $patient = $this->patient();
        $this->visit($patient, 2, false, ['medical_history' => 'تاريخ غير نهائي لا يعرض', 'diagnosis' => 'تشخيص مسودة لا يعرض']);
        $current = $this->visit($patient, 0, false, ['medical_history' => 'توثيق خاص بالزيارة الحالية']);
        $future = $this->visit($patient, -1, true, ['medical_history' => 'معلومة لاحقة مخفية', 'diagnosis' => 'تشخيص لاحق مخفي']);
        DoctorPrescription::factory()->create(['doctor_visit_id' => $current->id, 'status' => 'approved', 'approved_at' => now(), 'content' => ['items' => [['name' => 'وصفة الزيارة الحالية مخفية من التاريخ']]]]);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertSee('توثيق خاص بالزيارة الحالية')->assertSee('التاريخ المرضي في هذه الزيارة')->assertSee('التوثيق غير نهائي')
            ->assertDontSee('تاريخ غير نهائي لا يعرض')->assertDontSee('تشخيص مسودة لا يعرض')->assertDontSee('معلومة لاحقة مخفية')->assertDontSee('تشخيص لاحق مخفي')->assertDontSee('وصفة الزيارة الحالية مخفية من التاريخ')
            ->assertViewHas('patientHistory', fn ($history) => $history['histories']->isEmpty() && $history['diagnoses']->isEmpty() && ! $history['visits']->contains('id', $future->id));
    }

    public function test_history_preview_is_bounded_and_ordered_by_real_dates_not_insertion_order(): void
    {
        $patient = $this->patient();
        $recent = $this->visit($patient, 1, true, ['medical_history' => 'الأحدث', 'diagnosis' => 'تشخيص أحدث']);
        $oldest = $this->visit($patient, 10, true, ['medical_history' => 'الأقدم المخفي من الملخص', 'diagnosis' => 'تشخيص أقدم']);
        $middle = $this->visit($patient, 2, true, ['medical_history' => 'الثاني', 'diagnosis' => 'تشخيص ثان']);
        $third = $this->visit($patient, 3, true, ['medical_history' => 'الثالث', 'diagnosis' => 'تشخيص ثالث']);
        $current = $this->visit($patient);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertDontSee('الأقدم المخفي من الملخص')
            ->assertViewHas('patientHistory', fn ($history) => $history['histories']->modelKeys() === [$recent->id, $middle->id, $third->id] && ! $history['visits']->contains('id', $oldest->id));
    }

    public function test_unapproved_and_superseded_documents_are_not_shown_as_current_historical_prescriptions_or_reports(): void
    {
        $patient = $this->patient();
        $prior = $this->visit($patient, 2, true);
        $oldRx = DoctorPrescription::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(2), 'content' => ['items' => [['name' => 'وصفة سابقة مستبدلة مخفية']]]]);
        $newRx = DoctorPrescription::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDay(), 'corrects_prescription_id' => $oldRx->id, 'document_version' => 2, 'correction_reason' => 'تصحيح موثق', 'content' => ['items' => [['name' => 'الوصفة التصحيحية الظاهرة']]]]);
        $draftVisit = $this->visit($patient, 1, false);
        DoctorPrescription::factory()->create(['doctor_visit_id' => $draftVisit->id, 'content' => ['items' => [['name' => 'وصفة مسودة مخفية']]]]);
        $oldOrder = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(2)]);
        $newOrder = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDay(), 'corrects_document_id' => $oldOrder->id, 'document_version' => 2, 'correction_reason' => 'تصحيح موثق']);
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $oldOrder->id, 'title' => 'تقرير طلب مستبدل مخفي']);
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $newOrder->id, 'title' => 'تقرير الإصدار الحالي']);
        $current = $this->visit($patient);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertSee('الوصفة التصحيحية الظاهرة')->assertSee('تقرير الإصدار الحالي')
            ->assertDontSee('وصفة سابقة مستبدلة مخفية')->assertDontSee('وصفة مسودة مخفية')->assertDontSee('تقرير طلب مستبدل مخفي')
            ->assertViewHas('patientHistory', fn ($history) => $history['prescriptions']->modelKeys() === [$newRx->id]);
    }

    public function test_history_text_is_escaped_and_cannot_inject_controls_into_clinical_form(): void
    {
        $patient = $this->patient();
        $script = '<script>alert("history")</script>';
        $this->visit($patient, 1, true, ['medical_history' => $script, 'diagnosis' => $script]);
        $current = $this->visit($patient);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()->assertSee($script)->assertDontSee($script, false);
        $this->assertSame([], $current->fresh()->content);
    }

    public function test_open_visit_shows_final_history_for_the_same_thera_patient_across_doctors_without_opening_their_records(): void
    {
        $network = Patient::factory()->create();
        $patient = $this->patient($network);
        $other = $this->patient($network);
        $other->doctor->update(['name' => 'د. ليان الخطيب']);
        $prior = $this->visit($other, 4, true, ['medical_history' => 'تاريخ مشترك موثق', 'diagnosis' => 'تشخيص سابق مشترك', 'clinical_notes' => 'ملاحظات خاصة لا يشملها الملخص']);
        $rx = DoctorPrescription::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(4), 'content' => ['items' => [['name' => 'دواء موثق سابقًا']]]]);
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $prior->id, 'status' => 'approved', 'approved_at' => now()->subDays(4)]);
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id, 'title' => 'تقرير سابق من طبيب آخر']);
        $current = $this->visit($patient);

        $response = $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()
            ->assertSee('تاريخ مشترك موثق')->assertSee('تشخيص سابق مشترك')->assertSee('دواء موثق سابقًا')->assertSee('تقرير سابق من طبيب آخر')->assertSee('د. ليان الخطيب')
            ->assertDontSee('ملاحظات خاصة لا يشملها الملخص')
            ->assertDontSee('href="'.route('doctor.clinical.visit', ['visit' => $prior->id]).'"', false)
            ->assertDontSee('href="'.route('doctor.clinical.prescriptions.show', $rx).'"', false)
            ->assertDontSee('href="'.route('doctor.orders.reports.show', $report).'"', false)
            ->assertViewHas('patientHistory', fn ($history) => $history['shared'] && $history['histories']->modelKeys() === [$prior->id]);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame([], $current->fresh()->content);
        $this->assertSame(['shared_history_visit_ids' => [$prior->id]], $current->revisions()->where('action', 'viewed')->sole()->snapshot);
        $this->assertSame(0, $prior->revisions()->count());
        $this->get(route('doctor.clinical.visit', ['visit' => $prior->id]))->assertNotFound();
        $this->get(route('doctor.clinical.prescriptions.show', $rx))->assertNotFound();
        $this->get(route('doctor.orders.reports.show', $report))->assertNotFound();
    }

    public function test_closing_current_visit_ends_shared_history_access_and_only_its_doctor_can_open_it(): void
    {
        $network = Patient::factory()->create();
        $patient = $this->patient($network);
        $other = $this->patient($network);
        $this->visit($other, 3, true, ['medical_history' => 'المعلومة المشتركة أثناء العلاج']);
        $this->visit($patient, 2, true, ['medical_history' => 'تاريخ الطبيب المسؤول']);
        $current = $this->visit($patient);
        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()->assertSee('المعلومة المشتركة أثناء العلاج');

        $this->putJson(route('doctor.clinical.visits.update', $current), ['revision' => 1, 'intent' => 'close', 'assessment' => 'التقييم النهائي للزيارة'])->assertOk()->assertJsonPath('redirect', true);

        $this->assertNotNull($current->fresh()->closed_at);
        $this->assertSame('completed', $current->doctorAppointment->fresh()->status);
        $this->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()->assertSee('التقييم النهائي للزيارة')->assertSee('تاريخ الطبيب المسؤول')->assertDontSee('المعلومة المشتركة أثناء العلاج')
            ->assertViewHas('patientHistory', fn ($history) => ! $history['shared']);
        $this->actingAs($other->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertNotFound();
        $this->putJson(route('doctor.clinical.visits.update', $current), ['revision' => 2, 'intent' => 'save', 'assessment' => 'تعديل غير مسموح'])->assertNotFound();
        $this->assertSame('التقييم النهائي للزيارة', $current->fresh()->content['assessment']);
    }

    public function test_network_history_never_uses_identical_contacts_or_unfinished_and_future_visits(): void
    {
        $network = Patient::factory()->create();
        $patient = $this->patient($network);
        $other = $this->patient($network);
        $draft = $this->visit($other, 2, false, ['medical_history' => 'مسودة الطبيب الآخر مخفية', 'diagnosis' => 'تشخيص غير نهائي مخفي']);
        DoctorPrescription::factory()->create(['doctor_visit_id' => $draft->id, 'status' => 'approved', 'approved_at' => now()->subDay(), 'content' => ['items' => [['name' => 'دواء زيارة غير منتهية مخفي']]]]);
        $this->visit($other, -1, true, ['medical_history' => 'تاريخ مستقبلي مخفي']);
        $unrelated = DoctorPatient::factory()->create(['patient_id' => Patient::factory(), 'name' => $patient->name, 'phone' => $patient->phone]);
        $this->visit($unrelated, 2, true, ['medical_history' => 'تاريخ ملف ثيرا مختلف مخفي']);
        $current = $this->visit($patient);

        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id, 'patient' => $unrelated->id]))->assertOk()
            ->assertDontSee('مسودة الطبيب الآخر مخفية')->assertDontSee('تشخيص غير نهائي مخفي')->assertDontSee('دواء زيارة غير منتهية مخفي')->assertDontSee('تاريخ مستقبلي مخفي')->assertDontSee('تاريخ ملف ثيرا مختلف مخفي')
            ->assertViewHas('patientHistory', fn ($history) => $history['shared'] && $history['histories']->isEmpty() && $history['visits']->isEmpty() && $history['prescriptions']->isEmpty());
    }

    #[TestWith(['unverified'])]
    #[TestWith(['inactive_membership'])]
    #[TestWith(['inactive_facility'])]
    #[TestWith(['not_arrived'])]
    #[TestWith(['cancelled'])]
    public function test_shared_history_requires_an_active_treatment_context(string $restriction): void
    {
        $network = Patient::factory()->create();
        $patient = $this->patient($network);
        $other = $this->patient($network);
        $this->visit($other, 2, true, ['medical_history' => 'تاريخ شبكة خارج السياق مخفي']);
        $current = $this->visit($patient);
        match ($restriction) {
            'unverified' => $patient->doctor->update(['verification_status' => 'profile_incomplete']),
            'inactive_membership' => $current->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'inactive_facility' => $current->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
            'not_arrived' => $current->doctorAppointment->update(['arrived_at' => null]),
            'cancelled' => $current->doctorAppointment->update(['status' => 'cancelled']),
        };

        $this->actingAs($patient->doctor->fresh(), 'doctor')->get(route('doctor.clinical.visit', ['visit' => $current->id]))->assertOk()->assertDontSee('تاريخ شبكة خارج السياق مخفي')
            ->assertViewHas('patientHistory', fn ($history) => ! $history['shared'] && $history['histories']->isEmpty());
    }

    public function test_the_existing_visit_workspace_stops_network_history_after_consent_revocation_or_expiry(): void
    {
        $network = Patient::factory()->create();
        $patient = $this->patient($network);
        $foreign = $this->patient($network);
        $this->visit($foreign, 2, true, ['diagnosis' => 'تشخيص مشترك يحتاج موافقة']);
        $current = $this->visit($patient);
        $url = route('doctor.clinical.visit', ['visit' => $current->id]);
        $this->actingAs($patient->doctor, 'doctor')->get($url)->assertOk()->assertSee('تشخيص مشترك يحتاج موافقة');
        $consent = PatientDoctorConsent::where('patient_id', $network->id)->where('doctor_id', $patient->doctor_id)->firstOrFail();
        $consent->update(['revoked_at' => now()]);
        $this->get($url)->assertOk()->assertDontSee('تشخيص مشترك يحتاج موافقة');
        $consent->update(['revoked_at' => null, 'expires_at' => now()->subMinute()]);
        $this->get($url)->assertOk()->assertDontSee('تشخيص مشترك يحتاج موافقة');
    }

    private function patient(?Patient $network = null): DoctorPatient
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-02 10:00', config('thera.schedule_timezone')));

        $file = DoctorPatient::factory()->for(Doctor::factory()->state(['verification_status' => 'verified']))->create(['name' => 'سارة خالد', 'patient_id' => $network?->id]);
        if ($network) {
            PatientDoctorConsent::factory()->create(['patient_id' => $network->id, 'doctor_id' => $file->doctor_id, 'granted_by' => $network->user_id]);
        }

        return $file;
    }

    /** @param array<string, mixed> $content */
    private function visit(DoctorPatient $patient, int $daysAgo = 0, bool $closed = false, array $content = []): DoctorVisit
    {
        $started = now()->subDays($daysAgo);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_facility_id' => DoctorFacility::factory()->for($patient->doctor), 'doctor_patient_id' => $patient->id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'arrived_at' => $started, 'starts_at' => $started, 'ends_at' => $started->copy()->addMinutes(30)]);

        return DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'started_at' => $started, 'closed_at' => $closed ? $started->copy()->addMinutes(30) : null, 'status' => $closed ? 'completed' : 'draft', 'content' => array_replace($closed ? ['assessment' => 'تقييم محفوظ'] : [], $content)]);
    }
}
