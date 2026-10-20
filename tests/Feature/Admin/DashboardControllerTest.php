<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorCredential;
use App\Models\FacilityProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_real_doctor_and_pending_request_counts(): void
    {
        $admin = Admin::factory()->create();
        Doctor::factory()->count(2)->create();
        DoctorCredential::factory()->count(2)->create();
        DoctorCredential::factory()->verified()->create();
        FacilityProposal::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('doctorCount', 6)
            ->assertViewHas('pendingTotal', 3)
            ->assertSee('طلبات الاعتماد')
            ->assertSee('data-admin-pending-count="3"', escape: false)
            ->assertSee('عربي (AR)')
            ->assertSee('id="admin-sidebar-pin"', escape: false)
            ->assertSee('class="quick-add-button"', escape: false)
            ->assertSee('class="dropdown-menu quick-add-menu"', escape: false)
            ->assertSee('sweetalert2@11')
            ->assertSee('ابحث عن طبيب بالاسم أو البريد...')
            ->assertDontSee('>الطلبات</span>', escape: false);
    }
}
