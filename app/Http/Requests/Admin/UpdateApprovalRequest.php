<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('admin') instanceof Admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'rejection_reason' => ['nullable', 'required_if:decision,reject', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'rejection_reason' => $this->filled('rejection_reason')
                ? $this->string('rejection_reason')->trim()->toString()
                : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'حدد قرار الاعتماد.',
            'decision.in' => 'قرار الاعتماد غير صالح.',
            'rejection_reason.required_if' => 'اكتب سبب الرفض ليظهر للطبيب.',
            'rejection_reason.max' => 'سبب الرفض يجب ألا يتجاوز 1000 حرف.',
        ];
    }
}
