<?php

namespace App\Http\Requests\Doctor\Clinical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorPrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::forUser($this->user('doctor'))->authorize('update', $this->route('doctorPrescription'));

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
        $rules = [
            'revision' => ['required', 'integer', 'min:1'],
            'intent' => ['required', Rule::in(['save', 'approve'])],
            'notes' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*' => ['required', 'array:source,medication_id,name,active_ingredient,strength,dosage_form,dose,frequency,duration,quantity,instructions'],
            'items.*.source' => ['required', Rule::in(['catalog', 'custom'])],
            'items.*.medication_id' => ['exclude_unless:items.*.source,catalog', 'required_if:items.*.source,catalog', 'integer', 'min:1'],
            'review_confirmed' => $approving ? ['required', 'accepted'] : ['nullable', 'boolean'],
        ];
        foreach (['dose', 'frequency', 'duration', 'quantity', 'instructions'] as $field) {
            $rules['items.*.'.$field] = [$approving && $field !== 'instructions' ? 'required' : 'nullable', 'string', 'max:500'];
        }
        foreach (['name' => 150, 'active_ingredient' => 150, 'strength' => 100, 'dosage_form' => 100] as $field => $max) {
            $rules['items.*.'.$field] = ['exclude_unless:items.*.source,custom', $approving || $field === 'name' ? 'required_if:items.*.source,custom' : 'nullable', 'string', 'max:'.$max];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['required' => 'أكمل الحقل المطلوب قبل اعتماد الوصفة.', 'required_if' => 'أكمل بيانات الدواء المطلوبة.', 'items.required' => 'أضف دواءً واحدًا على الأقل.', 'items.min' => 'أضف دواءً واحدًا على الأقل.', 'items.max' => 'الحد الأقصى 30 دواءً في الوصفة.', 'review_confirmed.accepted' => 'أكد مراجعتك للأدوية والجرعات قبل الاعتماد.', 'review_confirmed.required' => 'أكد مراجعتك للأدوية والجرعات قبل الاعتماد.'];
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items');
        if (! is_array($items)) {
            return;
        }
        foreach ($items as &$item) {
            if (is_array($item) && ! array_key_exists('source', $item)) {
                $item['source'] = ! empty($item['medication_id']) ? 'catalog' : 'custom';
            }
        }
        unset($item);
        $this->merge(['items' => $items]);
    }
}
