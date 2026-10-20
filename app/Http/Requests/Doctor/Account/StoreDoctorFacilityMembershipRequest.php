<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use App\Models\Facility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDoctorFacilityMembershipRequest extends FormRequest
{
    protected $errorBag = 'facilityMembership';

    public function authorize(): bool
    {
        return $this->user('doctor') instanceof Doctor;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'facility_id' => [
                'required',
                'integer',
                Rule::exists('facilities', 'id')->where('is_active', true),
            ],
            'role' => ['required', 'string', 'max:100'],
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
                $facility = Facility::query()->find($this->integer('facility_id'));

                if (! $doctor instanceof Doctor || ! $facility instanceof Facility) {
                    return;
                }

                if ($doctor->facilityMemberships()->whereBelongsTo($facility)->exists()) {
                    $validator->errors()->add('facility_id', 'لديك عضوية أو طلب سابق مرتبط بهذه المنشأة.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['role' => $this->string('role')->trim()->toString()]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'facility_id.required' => 'اختر المنشأة من القائمة.',
            'facility_id.exists' => 'المنشأة المحددة غير متاحة.',
            'role.required' => 'أدخل صفتك المهنية داخل المنشأة.',
            'role.max' => 'يجب ألا تتجاوز الصفة المهنية 100 حرف.',
        ];
    }
}
