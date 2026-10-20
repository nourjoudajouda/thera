<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use App\Models\Facility;
use App\Models\FacilityProposal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDoctorFacilityProposalRequest extends FormRequest
{
    protected $errorBag = 'facilityProposal';

    public function authorize(): bool
    {
        return $this->user('doctor') instanceof Doctor;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'type' => ['required', Rule::in(['clinic', 'medical_center', 'hospital'])],
            'city' => ['required', 'string', 'min:2', 'max:100'],
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
                if (! $doctor instanceof Doctor) {
                    return;
                }

                $name = $this->string('name')->toString();
                $city = $this->string('city')->toString();

                if (Facility::query()->where('name', $name)->where('city', $city)->exists()) {
                    $validator->errors()->add('name', 'هذه المنشأة موجودة بالفعل؛ استخدم طلب الانضمام إليها.');

                    return;
                }

                if ($doctor->facilityProposals()
                    ->where('name', $name)
                    ->where('city', $city)
                    ->where('status', FacilityProposal::STATUS_PENDING)
                    ->exists()) {
                    $validator->errors()->add('name', 'سبق أن أرسلت طلب إضافة لهذه المنشأة وهو قيد المراجعة.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->string('name')->trim()->toString(),
            'city' => $this->string('city')->trim()->toString(),
            'role' => $this->string('role')->trim()->toString(),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'أدخل اسم المنشأة.',
            'name.min' => 'يجب أن يتكون اسم المنشأة من 3 أحرف على الأقل.',
            'type.required' => 'اختر نوع المنشأة.',
            'type.in' => 'نوع المنشأة المحدد غير صحيح.',
            'city.required' => 'أدخل المدينة.',
            'city.min' => 'أدخل اسم مدينة صحيحًا.',
            'role.required' => 'أدخل صفتك المهنية داخل المنشأة.',
            'role.max' => 'يجب ألا تتجاوز الصفة المهنية 100 حرف.',
        ];
    }
}
