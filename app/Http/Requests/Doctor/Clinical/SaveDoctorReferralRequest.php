<?php

namespace App\Http\Requests\Doctor\Clinical;

use App\Models\DoctorReferral;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveDoctorReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::forUser($this->user('doctor'))->authorize('update', $this->route('doctorReferral'));

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
        $followup = $this->route('doctorReferral')->kind === 'followup';
        $document = $this->route('doctorReferral');
        $retainedDate = $document->corrects_document_id && $this->input('due_date') === ($document->correctedFrom->content['due_date'] ?? null);

        return [
            'revision' => ['required', 'integer', 'min:1'],
            'intent' => ['required', Rule::in(['save', 'approve'])],
            'title' => [$approving ? 'required' : 'nullable', 'string', 'max:200'],
            'priority' => ['required', Rule::in(array_keys(DoctorReferral::PRIORITIES))],
            'reason' => [$approving ? 'required' : 'nullable', 'string', 'max:5000'],
            'clinical_summary' => ['nullable', 'string', 'max:5000'],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'target' => [$followup ? 'exclude' : ($approving ? 'required' : 'nullable'), 'string', 'max:80', 'regex:/^(doctor|facility):[1-9][0-9]*$/'],
            'due_date' => [$followup ? ($approving ? 'required' : 'nullable') : 'exclude', 'date_format:Y-m-d', ...($approving && ! $retainedDate ? ['after_or_equal:'.now(config('thera.schedule_timezone'))->toDateString()] : [])],
            'followup_kind' => [$followup ? 'required' : 'exclude', Rule::in(array_keys(DoctorReferral::FOLLOWUP_KINDS))],
            'time_window' => [$followup ? 'required' : 'exclude', Rule::in(array_keys(DoctorReferral::WINDOWS))],
            'review_confirmed' => $approving ? ['required', 'accepted'] : ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ['required' => 'أكمل الحقل المطلوب قبل الاعتماد.', 'target.regex' => 'اختر طبيبًا أو منشأة مسجلة من القائمة.', 'due_date.after_or_equal' => 'تاريخ المتابعة يجب أن يكون اليوم أو بعده.', 'review_confirmed.accepted' => 'أكد مراجعتك للمستند قبل الاعتماد.', 'max' => 'النص أطول من الحد المسموح.'];
    }
}
