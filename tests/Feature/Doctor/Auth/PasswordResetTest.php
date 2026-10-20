<?php

namespace Tests\Feature\Doctor\Auth;

use App\Models\Doctor;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_uses_the_login_modal_instead_of_a_separate_page(): void
    {
        $this->get(route('doctor.password.request'))
            ->assertRedirect(route('doctor.login', ['recovery' => 1]));

        $this->get(route('doctor.login', ['recovery' => 1]))
            ->assertOk()
            ->assertSee('id="forgot-modal"', escape: false)
            ->assertSee('action="'.route('doctor.password.email').'"', escape: false);
    }

    public function test_doctor_can_request_a_password_reset_link_by_email(): void
    {
        Notification::fake();
        $doctor = Doctor::factory()->create(['email' => 'doctor@example.com']);

        $this->from(route('doctor.login'))
            ->post(route('doctor.password.email'), ['identity' => 'doctor@example.com'])
            ->assertRedirect(route('doctor.login'))
            ->assertSessionHas('recovery_status');

        Notification::assertSentTo($doctor, ResetPassword::class);
    }

    public function test_unknown_identity_receives_the_same_generic_response(): void
    {
        Notification::fake();

        $this->from(route('doctor.login'))
            ->post(route('doctor.password.email'), ['identity' => 'unknown@example.com'])
            ->assertRedirect(route('doctor.login'))
            ->assertSessionHas('recovery_status');

        Notification::assertNothingSent();
    }

    public function test_doctor_can_reset_the_password_with_a_valid_token(): void
    {
        $doctor = Doctor::factory()->create(['email' => 'doctor@example.com']);
        $token = Password::broker('doctors')->createToken($doctor);

        $this->post(route('doctor.password.update'), [
            'token' => $token,
            'email' => $doctor->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ])->assertRedirect(route('doctor.login'));

        $this->assertTrue(Hash::check('NewSecurePassword123!', $doctor->fresh()->password));
    }

    public function test_invalid_token_does_not_change_the_password(): void
    {
        $doctor = Doctor::factory()->create(['email' => 'doctor@example.com']);
        $oldPassword = $doctor->password;

        $this->post(route('doctor.password.update'), [
            'token' => 'invalid-token',
            'email' => $doctor->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ])->assertSessionHasErrors('email');

        $this->assertSame($oldPassword, $doctor->fresh()->password);
    }
}
