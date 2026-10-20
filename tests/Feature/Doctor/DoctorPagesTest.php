<?php

namespace Tests\Feature\Doctor;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DoctorPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function doctorPageRoutes(): array
    {
        return [
            'login' => ['doctor.login'],
            'register' => ['doctor.register'],
            'forgot password' => ['doctor.password.request'],
            'reset password' => ['doctor.password.reset'],
            'dashboard' => ['doctor.dashboard'],
            'verification' => ['doctor.account.verification'],
            'professional profile' => ['doctor.account.professional-profile'],
            'settings' => ['doctor.account.settings'],
            'appointments' => ['doctor.appointments.index'],
            'new appointment' => ['doctor.appointments.create'],
            'appointment details' => ['doctor.appointments.show'],
            'patients' => ['doctor.patients.index'],
            'patient details' => ['doctor.patients.show'],
            'visits' => ['doctor.clinical.visits'],
            'clinical visit' => ['doctor.clinical.visit'],
            'prescriptions' => ['doctor.clinical.prescriptions'],
            'referrals' => ['doctor.clinical.referrals'],
            'lab orders' => ['doctor.orders.lab'],
            'radiology orders' => ['doctor.orders.radiology'],
            'results' => ['doctor.orders.results'],
            'services' => ['doctor.practice.services'],
            'schedule' => ['doctor.practice.schedule'],
            'collections' => ['doctor.finance.collections'],
        ];
    }

    #[DataProvider('doctorPageRoutes')]
    public function test_doctor_page_renders_successfully(string $routeName): void
    {
        if ($routeName === 'doctor.password.request') {
            $this->get(route($routeName))
                ->assertRedirect(route('doctor.login', ['recovery' => 1]));

            return;
        }

        if (! in_array($routeName, [
            'doctor.login',
            'doctor.register',
            'doctor.password.request',
            'doctor.password.reset',
        ], true)) {
            $this->actingAs(Doctor::factory()->create(), 'doctor');
        }

        $parameters = $routeName === 'doctor.password.reset' ? ['token' => 'test-token'] : [];
        $response = $this->get(route($routeName, $parameters));

        if ($routeName === 'doctor.clinical.visit') {
            $response->assertRedirect(route('doctor.clinical.visits'));

            return;
        }

        $response->assertOk();
        $response->assertSee('THERA', escape: false);
        $response->assertSee('window.doctorRoutes', escape: false);
    }
}
