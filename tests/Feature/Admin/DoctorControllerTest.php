<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_registered_doctors(): void
    {
        $admin = Admin::factory()->create();
        Doctor::factory()->create(['name' => 'الطبيب المطلوب', 'email' => 'wanted@example.com']);
        Doctor::factory()->create(['name' => 'طبيب آخر', 'email' => 'other@example.com']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.doctors.index', ['search' => 'wanted@example.com']))
            ->assertOk()
            ->assertSee('class="admin-search-field"', escape: false)
            ->assertSee('الطبيب المطلوب')
            ->assertDontSee('طبيب آخر');
    }

    public function test_admin_can_view_a_doctors_professional_details(): void
    {
        $admin = Admin::factory()->create();
        $doctor = Doctor::factory()->create([
            'name' => 'د. أحمد خالد',
            'professional_bio' => 'نبذة مهنية خاصة بالطبيب.',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.doctors.show', $doctor))
            ->assertOk()
            ->assertSee('د. أحمد خالد')
            ->assertSee('نبذة مهنية خاصة بالطبيب.');
    }
}
