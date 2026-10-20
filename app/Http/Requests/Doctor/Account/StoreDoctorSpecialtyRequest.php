<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Specialty;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDoctorSpecialtyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('doctor') instanceof Doctor;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'specialty_id' => [
                'required',
                'integer',
                Rule::exists('specialties', 'id')->where('is_active', true),
            ],
            'type' => ['required', Rule::in([
                DoctorSpecialty::TYPE_PRIMARY,
                DoctorSpecialty::TYPE_SUBSPECIALTY,
            ])],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $doctor = $this->user('doctor');
                $specialty = Specialty::query()->find($this->integer('specialty_id'));

                if (! $doctor instanceof Doctor || ! $specialty instanceof Specialty) {
                    return;
                }

                $type = $this->string('type')->toString();
                $primaryAssignment = $doctor->primarySpecialtyAssignment()->first();

                if ($doctor->specialtyAssignments()->where('specialty_id', $specialty->id)->exists()) {
                    $validator->errors()->add('specialty_id', 'هذا التخصص مضاف إلى ملفك بالفعل.');

                    return;
                }

                if ($type === DoctorSpecialty::TYPE_PRIMARY) {
                    if ($specialty->parent_id !== null) {
                        $validator->errors()->add('specialty_id', 'اختر تخصصًا رئيسيًا من القائمة.');
                    }

                    if ($primaryAssignment?->verification_status === DoctorSpecialty::STATUS_VERIFIED) {
                        $validator->errors()->add('specialty_id', 'لا يمكن تغيير التخصص الرئيسي بعد توثيقه.');
                    }

                    return;
                }

                if (! $primaryAssignment instanceof DoctorSpecialty) {
                    $validator->errors()->add('specialty_id', 'أضف التخصص الرئيسي أولًا.');

                    return;
                }

                if ($specialty->parent_id !== $primaryAssignment->specialty_id) {
                    $validator->errors()->add('specialty_id', 'اختر تخصصًا فرعيًا تابعًا لتخصصك الرئيسي.');
                }

                if ($doctor->subspecialtyAssignments()->count() >= 5) {
                    $validator->errors()->add('specialty_id', 'يمكنك إضافة خمسة تخصصات فرعية كحد أقصى.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'specialty_id.required' => 'اختر التخصص من القائمة.',
            'specialty_id.exists' => 'التخصص المحدد غير متاح.',
            'type.required' => 'نوع التخصص مطلوب.',
            'type.in' => 'نوع التخصص المحدد غير صحيح.',
        ];
    }
}
