<?php

namespace App\Http\Requests\Doctor\Practice;

use App\Models\Doctor;
use App\Models\DoctorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateDoctorServiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $doctor = $this->user('doctor');
        $service = $this->route('doctorService');
        if (! $doctor instanceof Doctor || ! $service instanceof DoctorService) {
            return false;
        }
        Gate::forUser($doctor)->authorize('update', $service);

        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['is_active' => ['required', 'boolean']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'is_active.required' => 'حدد حالة إتاحة الخدمة.',
            'is_active.boolean' => 'حالة إتاحة الخدمة غير صالحة.',
        ];
    }
}
