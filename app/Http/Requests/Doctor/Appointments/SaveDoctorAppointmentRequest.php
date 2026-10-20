<?php

namespace App\Http\Requests\Doctor\Appointments;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SaveDoctorAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $doctor = $this->user('doctor');
        if (! $doctor instanceof Doctor) {
            return false;
        }
        if ($appointment = $this->route('doctorAppointment')) {
            Gate::forUser($doctor)->authorize('update', $appointment);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $editing = $this->route('doctorAppointment') instanceof DoctorAppointment;

        return [
            'doctor_service_id' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addYear()->toDateString()],
            'time' => ['required', 'date_format:H:i'],
            'patient_name' => [$editing ? 'exclude' : 'required', 'string', 'max:150'],
            'patient_phone' => [$editing ? 'exclude' : 'required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'notes' => [$editing ? 'exclude' : 'nullable', 'string', 'max:1000'],
            'request_key' => [$editing ? 'exclude' : 'required', 'uuid'],
            'revision' => [$editing ? 'required' : 'exclude', 'integer', 'min:1'],
            'reason' => [$editing ? 'required' : 'exclude', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['required' => 'هذا الحقل مطلوب.', 'date.*' => 'اختر تاريخًا صحيحًا من اليوم وحتى سنة.', 'time.*' => 'اختر وقتًا صحيحًا من الأوقات المتاحة.', 'patient_phone.regex' => 'أدخل رقم هاتف من 8 إلى 15 رقمًا مع رمز الدولة عند الحاجة.', 'request_key.*' => 'مفتاح الحجز غير صالح. افتح نموذج حجز جديد.', 'revision.*' => 'نسخة الموعد غير صالحة. أعد فتح تفاصيله.', 'max' => 'القيمة أطول من الحد المسموح.'];
    }
}
