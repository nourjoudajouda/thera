<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DoctorStampController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:80'],
            'stamp' => ['required', 'file', 'image', 'mimes:png,jpg,jpeg', 'extensions:png,jpg,jpeg', 'max:1024', 'dimensions:min_width=40,min_height=40,max_width=2000,max_height=2000'],
        ], [
            'stamp.required' => 'اختر صورة ختمك.',
            'stamp.image' => 'الختم يجب أن يكون صورة PNG أو JPG صحيحة.',
            'stamp.mimes' => 'يسمح بصور PNG وJPG فقط.',
            'stamp.extensions' => 'يسمح بامتداد PNG أو JPG فقط.',
            'stamp.max' => 'حجم صورة الختم لا يتجاوز 1 ميجابايت.',
            'stamp.dimensions' => 'أبعاد الختم من 40 إلى 2000 بكسل لكل اتجاه.',
        ]);
        $doctor = $request->user('doctor');
        $path = $data['stamp']->store('doctors/'.$doctor->id.'/stamps', 'local');
        abort_unless(is_string($path), 503, 'تعذر حفظ صورة الختم.');
        DB::transaction(function () use ($doctor, $path, $data): void {
            $locked = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            $locked->stamps()->create(['name' => $data['name'] ?? 'ختم الطبيب', 'path' => $path]);
            $locked->forceFill(['stamp_path' => $path])->save();
            Log::info('doctor_stamp_updated', ['doctor_id' => $locked->id, 'stamp_sha256' => hash('sha256', Storage::disk('local')->get($path))]);
        });
        $message = 'أُضيف الختم إلى أختامك المحفوظة. يمكنك اختياره عند الطباعة، ولم تتغير المستندات المعتمدة.';
        $url = route('doctor.account.settings', ['tab' => 'stamps']);

        return $request->expectsJson() ? response()->json(compact('message', 'url')) : redirect($url)->with('status', $message);
    }

    public function show(Request $request): Response
    {
        $request->validate(['stamp' => ['sometimes', 'required', 'integer', 'min:1']]);
        $doctor = $request->user('doctor');
        $path = $request->has('stamp')
            ? $doctor->stamps()->findOrFail($request->integer('stamp'))->path
            : $doctor->fresh()->stamp_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);
        $bytes = Storage::disk('local')->get($path);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        abort_unless(in_array($mime, ['image/png', 'image/jpeg'], true), 404);

        return response($bytes)->header('Content-Type', $mime)->header('X-Content-Type-Options', 'nosniff')->header('Cache-Control', 'private, no-store');
    }
}
