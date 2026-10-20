<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_admin_login_page(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('admin-login-form', escape: false);
    }

    public function test_guest_is_redirected_from_admin_dashboard_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_active_admin_can_login(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@thera.test',
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'ADMIN@THERA.TEST',
            'password' => 'SecurePassword123!',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotNull($admin->fresh()->last_login_at);
    }

    public function test_inactive_admin_cannot_login(): void
    {
        Admin::factory()->inactive()->create([
            'email' => 'admin@thera.test',
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => 'admin@thera.test',
            'password' => 'SecurePassword123!',
        ])->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_doctor_session_cannot_access_admin_pages(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_session_cannot_access_doctor_pages(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('doctor.dashboard'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_admin_can_logout_without_logging_out_the_doctor_guard(): void
    {
        $admin = Admin::factory()->create();
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')->actingAs($admin, 'admin')
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest('admin');
        $this->assertAuthenticatedAs($doctor, 'doctor');
    }
}
