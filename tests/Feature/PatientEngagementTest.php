<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use App\Models\Patient;
use App\Models\PatientDelegation;
use App\Models\PatientDevice;
use App\Models\PatientNotification;
use App\Models\PatientReview;
use App\Services\PatientNotificationPublisher;
use App\Services\PatientPushSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PatientEngagementTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_notifications_are_deduplicated_and_hidden_after_access_is_revoked(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $guardian = Patient::factory()->bookable()->create();
        $grant = PatientDelegation::factory()->create(['patient_id' => $patient->id, 'user_id' => $guardian->user_id, 'status' => 'approved', 'expires_at' => now()->addDays(30)]);
        $publisher = app(PatientNotificationPublisher::class);
        $publisher->publish($patient, 'appointment_updated', 'appointment', 99, 'event-99');
        $publisher->publish($patient, 'appointment_updated', 'appointment', 99, 'event-99');
        $this->assertDatabaseCount('patient_notifications', 2);
        $this->actingAs($guardian->user, 'sanctum')->getJson('/api/v1/notifications')->assertOk()->assertJsonCount(1, 'data');
        $id = PatientNotification::where('user_id', $guardian->user_id)->sole()->id;
        $grant->update(['status' => 'revoked']);
        $this->getJson('/api/v1/notifications')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/notifications/'.$id.'/read')->assertNotFound();
        $this->actingAs($patient->user, 'sanctum')->getJson('/api/v1/notifications')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_reminders_do_not_modify_prescriptions_and_cannot_cross_patient_files(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $other = Patient::factory()->bookable()->create();
        $url = '/api/v1/patients/'.$patient->public_id.'/reminders';
        $this->actingAs($patient->user, 'sanctum');
        $this->postJson($url, ['title' => 'تذكير شخصي', 'time' => '09:00', 'timezone' => 'Asia/Hebron', 'enabled' => true])->assertOk()->assertJsonPath('data.0.source', 'patient_entry');
        $foreign = DoctorPrescription::factory()->create(['status' => 'approved', 'approved_at' => now()]);
        $this->postJson($url, ['title' => 'دواء', 'time' => '10:00', 'timezone' => 'Asia/Hebron', 'enabled' => true, 'prescription_id' => $foreign->id])->assertNotFound();
        $this->actingAs($other->user, 'sanctum')->getJson($url)->assertNotFound();
        $this->assertDatabaseCount('patient_reminders', 1);
    }

    public function test_favorites_and_review_moderation_require_completed_visit(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $service = DoctorService::factory()->create(['is_active' => true]);
        $doctor = $service->doctorFacility->doctor;
        $doctor->update(['verification_status' => 'verified']);
        $service->doctorFacility->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        $file = DoctorPatient::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'doctor_patient_id' => $file->id, 'doctor_facility_id' => $service->doctor_facility_id, 'arrived_at' => now()]);
        $this->actingAs($patient->user, 'sanctum');
        $this->putJson('/api/v1/favorites/'.$doctor->id)->assertOk()->assertJsonCount(1, 'data');
        $url = '/api/v1/patients/'.$patient->public_id.'/appointments/'.$appointment->id.'/review';
        $this->putJson($url, ['rating' => 5, 'comment' => 'خدمة جيدة'])->assertNotFound();
        DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم موثق']]);
        $appointment->update(['status' => 'completed']);
        $this->putJson($url, ['rating' => 5, 'comment' => 'خدمة جيدة'])->assertOk()->assertJsonPath('data.status', 'pending');
        $this->getJson('/api/v1/doctors')->assertJsonPath('data.0.doctor.published_reviews_count', 0);
        $review = PatientReview::sole();
        $this->actingAs(Admin::factory()->create(), 'admin')->patch(route('admin.patient-reviews.update', $review), ['status' => 'approved', 'reason' => 'محتوى مناسب للنشر'])->assertRedirect();
        $this->actingAs($patient->user, 'sanctum')->getJson('/api/v1/doctors')->assertJsonPath('data.0.doctor.published_reviews_count', 1);
    }

    public function test_push_sender_uses_generic_preview_and_does_not_send_after_revocation(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $token = $patient->user->createToken('device');
        PatientDevice::create(['user_id' => $patient->user_id, 'personal_access_token_id' => $token->accessToken->id, 'token' => 'device-test-token', 'token_hash' => hash('sha256', 'device-test-token'), 'platform' => 'android', 'locale' => 'ar', 'enabled' => true]);
        $file = tempnam(sys_get_temp_dir(), 'thera-fcm-');
        try {
            config(['patient.fcm_project' => 'test-project', 'patient.fcm_service_account' => $file]);
            Cache::put('thera-fcm-token:'.hash('sha256', $file), 'mock-access-token', 3000);
            Http::preventStrayRequests();
            Http::fake(['https://fcm.googleapis.com/v1/projects/test-project/messages:send' => Http::response(['name' => 'sent'])]);
            $n = PatientNotification::create(['patient_id' => $patient->id, 'user_id' => $patient->user_id, 'kind' => 'medical_document', 'event_key' => 'test-message', 'target_type' => 'prescription', 'target_id' => 99]);
            app(PatientPushSender::class)->send($n);
            $this->assertSame('sent', $n->fresh()->push_state);
            Http::assertSent(fn ($request) => $request['message']['data'] === ['notification_id' => (string) $n->id] && ! str_contains(json_encode($request->data()), $patient->name));
            $patient->update(['is_active' => false]);
            app(PatientPushSender::class)->send($n);
            $this->assertSame('skipped', $n->fresh()->push_state);
            Http::assertSentCount(1);
        } finally {
            unlink($file);
        }
    }
}
