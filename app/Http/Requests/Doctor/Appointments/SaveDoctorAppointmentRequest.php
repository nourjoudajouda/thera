<?php

namespace App\Http\Requests\Doctor\Appointments;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorAppointmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('patient_mode')) {
            $this->merge(['patient_mode' => 'new']);
        }
        if ($this->input('patient_mode') === 'new_thera' && is_string($this->input('patient_email'))) {
            $this->merge(['patient_email' => mb_strtolower(trim($this->input('patient_email')))]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $doctor = $this->user('doctor');
        if (! $doctor instanceof Doctor) {
            return false;
        }
        if (! $this->route('doctorAppointment') && in_array($this->input('patient_mode'), ['thera', 'new_thera'], true) && $doctor->verification_status !== 'verified') {
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
        $existingPatient = $this->input('patient_mode') === 'existing';
        $theraPatient = $this->input('patient_mode') === 'thera';

        return [
            'doctor_service_id' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addYear()->toDateString()],
            'time' => ['required', 'date_format:H:i'],
            'patient_mode' => [$editing ? 'exclude' : 'required', Rule::in(['new', 'existing', 'thera', 'new_thera'])],
            'patient_email' => [$editing || $this->input('patient_mode') !== 'new_thera' ? 'exclude' : 'required', 'string', 'email', 'max:255'],
            'thera_patient_id' => [$editing || ! $theraPatient ? 'exclude' : 'required', 'ulid', Rule::exists('patients', 'public_id')->where('is_active', true)->where('booking_enabled', true)->whereNotNull('phone_verified_at')],
            'doctor_patient_id' => [$editing || ! $existingPatient ? 'exclude' : 'required', 'integer', Rule::exists('doctor_patients', 'id')->where('doctor_id', $this->user('doctor')->id)],
            'patient_name' => [$editing || $existingPatient || $theraPatient ? 'exclude' : 'required', 'string', 'max:150'],
            'patient_phone' => [$editing || $existingPatient || $theraPatient ? 'exclude' : 'required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'notes' => [$editing ? 'exclude' : 'nullable', 'string', 'max:1000'],
            'request_key' => [$editing ? 'exclude' : 'required', 'uuid'],
            'revision' => [$editing ? 'required' : 'exclude', 'integer', 'min:1'],
            'reason' => [$editing ? 'required' : 'exclude', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['thera_patient_id.required' => 'اختر مريضًا من مرضى ثيرا.', 'thera_patient_id.ulid' => 'رقم ملف ثيرا غير صالح. ابحث عن المريض مجددًا.', 'thera_patient_id.exists' => 'المريض غير متاح للحجز من دليل ثيرا. ابحث عنه مجددًا.', 'required' => 'هذا الحقل مطلوب.', 'date.*' => 'اختر تاريخًا صحيحًا من اليوم وحتى سنة.', 'time.*' => 'اختر وقتًا صحيحًا من الأوقات المتاحة.', 'patient_phone.regex' => 'أدخل رقم هاتف من 8 إلى 15 رقمًا مع رمز الدولة عند الحاجة.', 'request_key.*' => 'مفتاح الحجز غير صالح. افتح نموذج حجز جديد.', 'revision.*' => 'نسخة الموعد غير صالحة. أعد فتح تفاصيله.', 'max' => 'القيمة أطول من الحد المسموح.'];
    }
}
