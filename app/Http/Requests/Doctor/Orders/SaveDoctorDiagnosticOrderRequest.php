<?php

namespace App\Http\Requests\Doctor\Orders;

use App\Models\DoctorDiagnosticOrder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorDiagnosticOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::forUser($this->user('doctor'))->authorize('update', $this->route('doctorDiagnosticOrder'));

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $approving = $this->input('intent') === 'approve';
        $radiology = $this->route('doctorDiagnosticOrder')->kind === 'radiology';

        return [
            'revision' => ['required', 'integer', 'min:1'],
            'intent' => ['required', Rule::in(['save', 'approve'])],
            'priority' => ['required', Rule::in(array_keys(DoctorDiagnosticOrder::PRIORITIES))],
            'clinical_context' => [$approving ? 'required' : 'nullable', 'string', 'max:5000'],
            'clinical_question' => $radiology ? [$approving ? 'required' : 'nullable', 'string', 'max:2000'] : ['exclude'],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*' => ['required', 'array:name,body_area,instructions'],
            'items.*.name' => ['required', 'string', 'max:200'],
            'items.*.body_area' => [$radiology ? ($approving ? 'required' : 'nullable') : 'exclude', 'string', 'max:200'],
            'items.*.instructions' => ['nullable', 'string', 'max:1000'],
            'review_confirmed' => $approving ? ['required', 'accepted'] : ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['required' => 'أكمل الحقل المطلوب قبل المتابعة.', 'items.required' => 'أضف فحصًا واحدًا على الأقل.', 'items.max' => 'الحد الأقصى 20 فحصًا في الطلب.', 'items.*.name.required' => 'اكتب اسم الفحص المطلوب.', 'items.*.body_area.required' => 'حدد المنطقة أو العضو لكل فحص أشعة.', 'clinical_context.required' => 'اكتب السياق السريري قبل اعتماد الطلب.', 'clinical_question.required' => 'اكتب السؤال السريري لطلب الأشعة.', 'review_confirmed.required' => 'أكد مراجعة الطلب قبل الاعتماد.', 'review_confirmed.accepted' => 'أكد مراجعة الطلب قبل الاعتماد.', 'priority.in' => 'الأولوية المحددة غير صحيحة.', 'max' => 'القيمة أطول من الحد المسموح.'];
    }
}
