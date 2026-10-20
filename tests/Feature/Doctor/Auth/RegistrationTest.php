<?php

namespace Tests\Feature\Doctor\Auth;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_doctor_registration_page(): void
    {
        $this->get(route('doctor.register'))
            ->assertOk()
            ->assertSee('doctor-register-form', escape: false);
    }

    public function test_guest_can_create_a_doctor_account(): void
    {
        $response = $this->post(route('doctor.register.store'), [
            'name' => 'Dr. Ahmad Ali',
            'email' => 'AHMAD@EXAMPLE.COM',
            'phone' => '+962 79 000 0002',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'terms' => '1',
        ]);

        $doctor = Doctor::query()->where('email', 'ahmad@example.com')->firstOrFail();

        $response->assertRedirect(route('doctor.account.professional-profile', ['onboarding' => 1]));
        $this->assertAuthenticatedAs($doctor, 'doctor');
        $this->assertSame('+962790000002', $doctor->phone);
        $this->assertSame('profile_incomplete', $doctor->verification_status);
        $this->assertNotNull($doctor->terms_accepted_at);
        $this->assertTrue(Hash::check('SecurePassword123!', $doctor->password));
    }

    public function test_registration_requires_all_required_fields(): void
    {
        $this->from(route('doctor.register'))
            ->post(route('doctor.register.store'))
            ->assertRedirect(route('doctor.register'))
            ->assertSessionHasErrors(['name', 'email', 'phone', 'password', 'terms']);

        $this->assertDatabaseCount('doctors', 0);
        $this->assertGuest('doctor');
    }

    public function test_registration_rejects_duplicate_email_and_phone(): void
    {
        Doctor::factory()->create([
            'email' => 'doctor@example.com',
            'phone' => '+962790000003',
        ]);

        $this->from(route('doctor.register'))->post(route('doctor.register.store'), [
            'name' => 'Another Doctor',
            'email' => 'doctor@example.com',
            'phone' => '+962790000003',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'terms' => '1',
        ])->assertRedirect(route('doctor.register'))
            ->assertSessionHasErrors(['email', 'phone']);

        $this->assertDatabaseCount('doctors', 1);
    }

    public function test_registration_rejects_unconfirmed_password_and_unaccepted_terms(): void
    {
        $this->from(route('doctor.register'))->post(route('doctor.register.store'), [
            'name' => 'Dr. Ahmad Ali',
            'email' => 'ahmad@example.com',
            'phone' => '+962790000004',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ])->assertRedirect(route('doctor.register'))
            ->assertSessionHasErrors(['password', 'terms']);

        $this->assertDatabaseCount('doctors', 0);
    }

    public function test_authenticated_doctor_cannot_open_registration_page(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.register'))
            ->assertRedirect(route('doctor.dashboard'));
    }
}
