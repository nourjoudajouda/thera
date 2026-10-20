<?php

namespace App\Http\Requests\Doctor\Practice;

use App\Models\Doctor;
use App\Models\DoctorScheduleException;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorScheduleExceptionRequest extends FormRequest
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
        $exception = $this->route('doctorScheduleException');
        if ($exception instanceof DoctorScheduleException) {
            Gate::forUser($doctor)->authorize('update', $exception);
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
        $periodRules = $this->input('type') === DoctorScheduleException::TYPE_SPECIAL
            ? array_replace(SaveDoctorScheduleRequest::periodRules(), ['periods' => ['required', 'array', 'min:1', 'max:4']])
            : ['periods' => ['exclude']];

        return [
            'doctor_facility_id' => ['required', 'integer'],
            'revision' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addYear()->toDateString()],
            'type' => ['required', Rule::in(array_keys(DoctorScheduleException::TYPES))],
            'note' => ['nullable', 'string', 'max:500'],
            ...$periodRules,
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('periods_payload')) {
            $raw = $this->input('periods_payload');
            $this->merge(['periods' => is_string($raw) ? json_decode($raw, true) : null]);
        }
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_replace((new SaveDoctorScheduleRequest)->messages(), [
            'type.in' => 'نوع الاستثناء غير صالح.',
            'date.date_format' => 'أدخل تاريخًا صحيحًا.',
            'date.after_or_equal' => 'لا يمكن تعديل استثناء بتاريخ سابق لليوم.',
            'date.before_or_equal' => 'اختر تاريخًا خلال سنة من اليوم.',
            'periods.min' => 'أضف ساعات العمل الخاصة لهذا التاريخ.',
            'periods.max' => 'الحد الأقصى أربع فترات خاصة في اليوم.',
            'note.max' => 'الملاحظة لا تزيد عن 500 حرف.',
        ]);
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return array_replace((new SaveDoctorScheduleRequest)->attributes(), ['date' => 'تاريخ الاستثناء', 'type' => 'نوع الاستثناء', 'note' => 'الملاحظة']);
    }
}
