<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PrintStampSelection
{
    /** @param array<string, mixed> $snapshot
     * @return array{choice: string, stamp_id: int|null, name: string|null, stamp_data_uri: string|null, sha256: string|null}
     */
    public function resolve(Request $request, Doctor $doctor, array $snapshot): array
    {
        $data = $request->validate(['stamp_choice' => ['sometimes', 'required', 'string', 'max:30', 'regex:/^(none|saved|[1-9][0-9]*)$/']]);
        $choice = $data['stamp_choice'] ?? 'saved';
        $uri = $choice === 'saved' ? ($snapshot['stamp_data_uri'] ?? null) : null;
        $stamp = null;
        if (! in_array($choice, ['none', 'saved'], true)) {
            $stamp = $doctor->stamps()->findOrFail($choice);
            if (! Storage::disk('local')->exists($stamp->path)) {
                throw ValidationException::withMessages(['stamp_choice' => 'ملف الختم غير متاح؛ اختر ختمًا آخر أو بدون ختم.']);
            }
            $bytes = Storage::disk('local')->get($stamp->path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
            if (! in_array($mime, ['image/png', 'image/jpeg'], true)) {
                throw ValidationException::withMessages(['stamp_choice' => 'صورة الختم غير صالحة؛ اختر ختمًا آخر.']);
            }
            $uri = 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        return ['choice' => $choice, 'stamp_id' => $stamp?->id, 'name' => $stamp?->name,
            'stamp_data_uri' => $uri, 'sha256' => $uri ? hash('sha256', $uri) : null];
    }
}
