<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\DoctorPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<DoctorPayment> */
class DoctorPaymentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'doctor_appointment_id' => DoctorAppointment::factory(),
            'doctor_id' => fn (array $attributes) => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->doctor_id,
            'currency' => fn (array $attributes) => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->currency,
            'kind' => 'payment', 'amount_minor' => 1000, 'method' => 'cash',
            'request_key' => (string) Str::uuid(), 'request_fingerprint' => hash('sha256', (string) Str::uuid()),
            'receipt_snapshot' => fn (array $attributes) => ['patient_name' => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->patient_name, 'service_name' => 'استشارة طبية', 'facility_name' => 'عيادة الشفاء', 'price_minor' => 20000, 'currency' => $attributes['currency']],
        ];
    }
}
