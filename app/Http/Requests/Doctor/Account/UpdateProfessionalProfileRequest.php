<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProfessionalProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'languages_text' => ['nullable', 'string', 'max:500'],
            'languages' => ['nullable', 'array', 'max:10'],
            'languages.*' => ['string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:280'],
            'professional_title' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:80'],
            'professional_bio' => ['required', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $languages = Str::of($this->string('languages_text')->toString())
            ->replace(',', '،')
            ->explode('،')
            ->map(fn (string $language): string => trim($language))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->merge([
            'name' => $this->string('name')->trim()->toString(),
            'languages' => $languages === [] ? null : $languages,
            'bio' => $this->string('bio')->trim()->toString() ?: null,
            'professional_title' => $this->string('professional_title')->trim()->toString(),
            'professional_bio' => $this->string('professional_bio')->trim()->toString(),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'أدخل الاسم الكامل.',
            'languages.max' => 'يمكنك إضافة 10 لغات كحد أقصى.',
            'languages.*.max' => 'يجب ألا يزيد اسم اللغة عن 50 حرفًا.',
            'bio.max' => 'يجب ألا تزيد النبذة المختصرة عن 280 حرفًا.',
            'professional_title.required' => 'أدخل المسمى المهني.',
            'years_experience.required' => 'أدخل عدد سنوات الخبرة.',
            'years_experience.integer' => 'سنوات الخبرة يجب أن تكون رقمًا صحيحًا.',
            'years_experience.min' => 'سنوات الخبرة لا يمكن أن تكون قيمة سالبة.',
            'years_experience.max' => 'سنوات الخبرة المدخلة غير صحيحة.',
            'professional_bio.required' => 'أدخل النبذة المهنية.',
            'professional_bio.max' => 'يجب ألا تزيد النبذة المهنية عن 2000 حرف.',
        ];
    }
}
