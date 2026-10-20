<?php

namespace Tests\Feature\Doctor\Auth;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_doctor_login_page(): void
    {
        $this->get(route('doctor.login'))
            ->assertOk()
            ->assertSee('doctor-login-form', escape: false);
    }

    public function test_guest_is_redirected_from_the_doctor_dashboard_to_login(): void
    {
        $this->get(route('doctor.dashboard'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_login_with_email(): void
    {
        $doctor = Doctor::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $response = $this->post(route('doctor.login.store'), [
            'identity' => 'doctor@example.com',
            'password' => 'SecurePassword123!',
        ]);

        $response->assertRedirect(route('doctor.dashboard', absolute: false));
        $this->assertAuthenticatedAs($doctor, 'doctor');
    }

    public function test_doctor_can_login_with_phone_number(): void
    {
        $doctor = Doctor::factory()->create([
            'phone' => '+962790000001',
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $response = $this->post(route('doctor.login.store'), [
            'identity' => '+962790000001',
            'password' => 'SecurePassword123!',
        ]);

        $response->assertRedirect(route('doctor.dashboard', absolute: false));
        $this->assertAuthenticatedAs($doctor, 'doctor');
    }

    public function test_doctor_cannot_login_with_an_invalid_password(): void
    {
        Doctor::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $this->from(route('doctor.login'))->post(route('doctor.login.store'), [
            'identity' => 'doctor@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect(route('doctor.login'))
            ->assertSessionHasErrors('identity');

        $this->assertGuest('doctor');
    }

    public function test_login_requires_identity_and_password(): void
    {
        $this->from(route('doctor.login'))
            ->post(route('doctor.login.store'))
            ->assertRedirect(route('doctor.login'))
            ->assertSessionHasErrors(['identity', 'password']);

        $this->assertGuest('doctor');
    }

    public function test_authenticated_doctor_is_redirected_away_from_login_page(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.login'))
            ->assertRedirect(route('doctor.dashboard'));
    }

    public function test_doctor_can_logout(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.logout'))
            ->assertRedirect(route('doctor.login'));

        $this->assertGuest('doctor');

        $this->get(route('doctor.dashboard'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_authenticated_dashboard_contains_the_secure_logout_form(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.dashboard'))
            ->assertOk()
            ->assertSee('id="doctor-logout-form"', escape: false)
            ->assertSee('action="'.route('doctor.logout').'"', escape: false);
    }
}
