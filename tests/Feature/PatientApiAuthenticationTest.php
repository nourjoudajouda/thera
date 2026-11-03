<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientApiAuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private function registerPatient(): string
    {
        return $this->postJson('/api/v1/auth/register', ['name' => 'مريض تجريبي', 'email' => 'patient@example.test', 'phone' => '+970599123456', 'password' => 'TheraTest1234', 'password_confirmation' => 'TheraTest1234', 'terms_accepted' => true, 'device_name' => 'test-device'])->assertOk()->json('data.token');
    }

    public function test_invalid_email_types_return_validation_errors(): void
    {
        $this->postJson('/api/v1/auth/register', ['email' => ['invalid']])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/api/v1/auth/login', ['email' => ['invalid']])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_registration_verification_and_login_reuse_one_patient(): void
    {
        config(['patient.development_verification' => true]);
        $token = $this->registerPatient();
        $this->withToken($token)->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.patient.booking_enabled', false);
        $code = $this->withToken($token)->postJson('/api/v1/auth/phone/code')->assertOk()->json('data.development_code');
        $this->withToken($token)->postJson('/api/v1/auth/phone/verify', ['code' => $code])->assertOk()->assertJsonPath('data.patient.booking_enabled', true);
        $this->withToken($token)->postJson('/api/v1/auth/phone/verify', ['code' => $code])->assertUnprocessable();
        $this->postJson('/api/v1/auth/login', ['email' => 'PATIENT@example.test', 'password' => 'TheraTest1234', 'device_name' => 'another-device'])->assertOk();
        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_invalid_expired_code_and_production_never_reveal_a_code(): void
    {
        config(['patient.development_verification' => true]);
        $token = $this->registerPatient();
        $code = $this->withToken($token)->postJson('/api/v1/auth/phone/code')->json('data.development_code');
        $this->withToken($token)->postJson('/api/v1/auth/phone/verify', ['code' => '000000'])->assertUnprocessable();
        $this->travel(6)->minutes();
        $this->withToken($token)->postJson('/api/v1/auth/phone/verify', ['code' => $code])->assertUnprocessable();
        $this->app->instance('env', 'production');
        $this->withToken($token)->postJson('/api/v1/auth/phone/code')->assertStatus(503)->assertJsonMissingPath('data.development_code');
        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'patient@example.test'])->assertStatus(503);
    }

    public function test_profile_is_private_and_existing_doctor_created_account_can_login(): void
    {
        $user = User::factory()->create(['email' => 'created@example.test', 'password' => 'TheraTest1234']);
        $patient = Patient::factory()->create(['user_id' => $user->id]);
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
        $token = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'TheraTest1234', 'device_name' => 'device'])->assertOk()->json('data.token');
        $this->withToken($token)->putJson('/api/v1/patient/profile', ['name' => 'اسم محدث', 'health_information' => ['allergies' => 'حساسية شخصية']])->assertOk()->assertJsonPath('data.patient.name', 'اسم محدث');
        $this->assertSame($patient->public_id, $patient->fresh()->public_id);
        $this->assertDatabaseCount('patients', 1);
    }

    public function test_password_reset_revokes_tokens_and_code_is_single_use(): void
    {
        config(['patient.development_verification' => true]);
        $this->registerPatient();
        $code = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'patient@example.test'])->json('data.development_code');
        $data = ['email' => 'patient@example.test', 'code' => $code, 'password' => 'NewTheraPass123', 'password_confirmation' => 'NewTheraPass123'];
        $this->postJson('/api/v1/auth/reset-password', $data)->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->postJson('/api/v1/auth/reset-password', $data)->assertUnprocessable();
    }
}
