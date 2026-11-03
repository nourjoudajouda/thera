<?php

namespace Database\Factories;

use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorExternalReport>
 */
class DoctorExternalReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_diagnostic_order_id' => DoctorDiagnosticOrder::factory()->state(['status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['patient_name' => 'سليم خليل']]),
            'doctor_id' => fn (array $attributes) => DoctorDiagnosticOrder::findOrFail($attributes['doctor_diagnostic_order_id'])->doctor_id,
            'title' => 'تقرير فحص', 'source_name' => 'مختبر الرواد', 'source_reference' => null,
            'report_date' => now()->toDateString(), 'file_path' => 'external-reports/'.fake()->uuid().'.pdf',
            'file_mime' => 'application/pdf', 'file_size' => 100, 'file_sha256' => hash('sha256', 'report'), 'request_key' => fake()->uuid(),
        ];
    }
}
