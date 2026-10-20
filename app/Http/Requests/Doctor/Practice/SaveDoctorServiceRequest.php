<?php

namespace App\Http\Requests\Doctor\Practice;

use App\Models\Doctor;
use App\Models\DoctorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $doctor = $this->user('doctor');
        if (! $doctor instanceof Doctor) {
            return false;
        }
        $service = $this->route('doctorService');
        if ($service instanceof DoctorService) {
            Gate::forUser($doctor)->authorize('update', $service);
        }

        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $currency = $this->string('currency')->toString();
        $decimals = (int) config("thera.currencies.{$currency}.decimals", 2);
        $unique = Rule::unique('doctor_services', 'name')->where('doctor_facility_id', $this->integer('doctor_facility_id'));
        if ($this->route('doctorService') instanceof DoctorService) {
            $unique->ignore($this->route('doctorService'));
        }

        return [
            'doctor_facility_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:150', $unique],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'currency' => ['required', Rule::in(array_keys(config('thera.currencies')))],
            'price' => ['required', 'regex:/\A[0-9]{1,6}(?:\.[0-9]{1,'.$decimals.'})?\z/'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->string('name')->trim()->toString(),
            'description' => $this->string('description')->trim()->toString() ?: null,
            'instructions' => $this->string('instructions')->trim()->toString() ?: null,
            'currency' => $this->string('currency')->trim()->upper()->toString(),
            'price' => $this->string('price')->trim()->toString(),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'doctor_facility_id.required' => 'اختر منشأة تقديم الخدمة.',
            'doctor_facility_id.integer' => 'المنشأة المحددة غير صالحة.',
            'name.required' => 'أدخل اسم الخدمة.',
            'name.max' => 'اسم الخدمة يجب ألا يتجاوز 150 حرفًا.',
            'name.unique' => 'توجد خدمة بهذا الاسم في المنشأة نفسها.',
            'currency.required' => 'اختر عملة السعر.',
            'currency.in' => 'العملة المحددة غير مدعومة.',
            'price.required' => 'أدخل سعر الخدمة.',
            'price.regex' => 'أدخل سعرًا موجبًا أو صفرًا بدقة العملة المحددة، وبحد أقصى ست خانات صحيحة.',
            'duration_minutes.required' => 'أدخل مدة الخدمة بالدقائق.',
            'duration_minutes.integer' => 'مدة الخدمة يجب أن تكون عددًا صحيحًا.',
            'duration_minutes.min' => 'مدة الخدمة لا تقل عن 5 دقائق.',
            'duration_minutes.max' => 'مدة الخدمة لا تزيد عن 480 دقيقة.',
            'is_active.required' => 'حدد حالة إتاحة الخدمة.',
            'is_active.boolean' => 'حالة إتاحة الخدمة غير صالحة.',
            'description.max' => 'الوصف يجب ألا يتجاوز 2000 حرف.',
            'instructions.max' => 'التعليمات يجب ألا تتجاوز 2000 حرف.',
        ];
    }
}
