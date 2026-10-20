<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorService;
use App\Models\Facility;
use Illuminate\Database\Seeder;

class DoctorServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $doctor = Doctor::query()->firstOrCreate(['email' => 'services-demo@thera.test'], [
            'name' => 'طبيب تجربة الخدمات', 'phone' => '+962790099991',
            'password' => 'ServiceDemo123!', 'verification_status' => 'verified',
            'professional_title' => 'طبيب تجريبي',
        ]);
        $facility = Facility::query()->firstOrCreate(['name' => 'منشأة تجريبية للخدمات', 'city' => 'بيئة الاختبار'], [
            'type' => 'clinic', 'is_active' => true, 'bookings_enabled' => true,
        ]);
        $membership = DoctorFacility::query()->firstOrCreate(['doctor_id' => $doctor->id, 'facility_id' => $facility->id], [
            'role' => 'طبيب', 'membership_status' => DoctorFacility::STATUS_ACTIVE, 'is_public' => false,
        ]);
        DoctorService::query()->firstOrCreate(['doctor_facility_id' => $membership->id, 'name' => 'استشارة تجريبية'], [
            'description' => 'خدمة لاختبار العرض والحفظ فقط.', 'price_minor' => 30125,
            'currency' => 'JOD', 'duration_minutes' => 30, 'is_active' => true,
        ]);
    }
}
