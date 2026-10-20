<?php

namespace App\Http\Controllers\Doctor\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Practice\SaveDoctorScheduleExceptionRequest;
use App\Http\Requests\Doctor\Practice\SaveDoctorScheduleRequest;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user('doctor');
        abort_unless($doctor instanceof Doctor, 403);
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $memberships = $doctor->facilityMemberships()->with('facility')->orderBy('id')->get();
        $membershipId = $request->integer('membership') ?: (int) ($memberships->firstWhere('membership_status', DoctorFacility::STATUS_ACTIVE)?->id ?? $memberships->first()?->id);
        $membership = $memberships->firstWhere('id', $membershipId);
        if ($membershipId) {
            abort_unless($membership, 404);
        }
        $schedule = $membership?->schedule()->with(['exceptions' => fn ($query) => $query->orderBy('date')])->first();
        $services = $membership ? $membership->services()->orderBy('name')->get() : collect();
        $preview = null;
        $previewDate = $request->string('date')->toString() ?: $today->addDay()->toDateString();
        $previewServiceId = $request->integer('service');
        if ($request->has('preview')) {
            $request->validate(['date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addYear()->toDateString()], 'service' => ['required', 'integer']], [
                'date.*' => 'اختر تاريخ معاينة صحيحًا بين اليوم وسنة من اليوم.', 'service.*' => 'اختر خدمة لمعاينة التوفر.',
            ]);
            $service = $services->firstWhere('id', $previewServiceId);
            abort_unless($service, 404);
            $preview = $schedule ? $schedule->preview(CarbonImmutable::parse($previewDate, config('thera.schedule_timezone')), $service)
                : ['status' => 'غير متاح', 'slots' => [], 'capacity' => 0, 'note' => 'احفظ الجدول أولًا لمعاينة التوفر.'];
        }
        $editingException = null;
        if ($request->integer('edit_exception')) {
            $editingException = DoctorScheduleException::query()->findOrFail($request->integer('edit_exception'));
            Gate::forUser($doctor)->authorize('update', $editingException);
            abort_unless($schedule && $editingException->doctor_schedule_id === $schedule->id, 404);
        }
        $periods = $schedule?->periods ?? [];
        $minutes = collect($periods)->sum(fn ($period) => collect(DoctorSchedule::windows($period))->sum(fn ($window) => $window[1] - $window[0]));

        return view('doctor.practice.schedule', [
            'doctor' => $doctor, 'memberships' => $memberships, 'membership' => $membership,
            'schedule' => $schedule, 'services' => $services, 'editingException' => $editingException,
            'readinessSpecialPeriods' => $schedule?->exceptions->filter(fn ($exception) => $exception->type === DoctorScheduleException::TYPE_SPECIAL && $exception->date->greaterThanOrEqualTo($today))
                ->flatMap(fn ($exception) => $exception->periods ?? [])->values()->all() ?? [],
            'canEdit' => $membership && $membership->membership_status === DoctorFacility::STATUS_ACTIVE && $membership->facility->is_active,
            'summary' => ['days' => collect($periods)->pluck('weekday')->unique()->count(), 'hours' => round($minutes / 60, 1), 'breaks' => collect($periods)->sum(fn ($period) => count($period['breaks'])), 'exceptions' => $schedule?->exceptions->filter(fn ($exception) => $exception->date->greaterThanOrEqualTo($today))->count() ?? 0],
            'preview' => $preview, 'previewDate' => $previewDate, 'previewServiceId' => $previewServiceId,
            'today' => $today->toDateString(), 'maxDate' => $today->addYear()->toDateString(),
        ]);
    }

    public function store(SaveDoctorScheduleRequest $request): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($request): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $membership = $this->editableMembership($doctor, $request->integer('doctor_facility_id'));
            $schedule = $membership->schedule()->lockForUpdate()->first();
            $this->checkRevision($request->integer('revision'), $schedule);
            $periods = $this->validatePeriods($request->validated('periods'), $membership);
            $data = [...$request->safe()->only(['daily_capacity', 'is_active']), 'periods' => $periods, 'revision' => ($schedule?->revision ?? 0) + 1];
            if ($schedule) {
                $schedule->update($data);
            } else {
                $membership->schedule()->create($data);
            }
            $this->ensureNoConflicts($doctor);
        });

        return $this->redirect($request, $request->integer('doctor_facility_id'), 'تم حفظ الدوام والسعة اليومية والخدمات المرتبطة به.');
    }

    public function storeException(SaveDoctorScheduleExceptionRequest $request): RedirectResponse|JsonResponse
    {
        $this->saveException($request);

        return $this->redirect($request, $request->integer('doctor_facility_id'), 'تمت إضافة استثناء الجدول.');
    }

    public function updateException(SaveDoctorScheduleExceptionRequest $request, DoctorScheduleException $doctorScheduleException): RedirectResponse|JsonResponse
    {
        $this->saveException($request, $doctorScheduleException);

        return $this->redirect($request, $request->integer('doctor_facility_id'), 'تم تحديث استثناء الجدول.');
    }

    public function destroyException(Request $request, DoctorScheduleException $doctorScheduleException): RedirectResponse|JsonResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('delete', $doctorScheduleException);
        $request->validate(['revision' => ['required', 'integer', 'min:1']], ['revision.*' => 'نسخة الجدول غير صالحة. حدّث الصفحة.']);
        $membershipId = $doctorScheduleException->doctorSchedule->doctor_facility_id;
        DB::transaction(function () use ($request, $doctorScheduleException, $membershipId): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $membership = $this->editableMembership($doctor, $membershipId);
            $schedule = $membership->schedule()->lockForUpdate()->firstOrFail();
            $this->checkRevision($request->integer('revision'), $schedule);
            $doctorScheduleException->delete();
            $schedule->increment('revision');
            $this->ensureNoConflicts($doctor);
        });

        return $this->redirect($request, $membershipId, 'تم حذف الاستثناء وإعادة تطبيق الجدول الأسبوعي.');
    }

    private function saveException(SaveDoctorScheduleExceptionRequest $request, ?DoctorScheduleException $exception = null): void
    {
        DB::transaction(function () use ($request, $exception): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $membership = $this->editableMembership($doctor, $request->integer('doctor_facility_id'));
            $schedule = $membership->schedule()->lockForUpdate()->first();
            if (! $schedule) {
                throw ValidationException::withMessages(['revision' => 'احفظ جدول المنشأة قبل إضافة الاستثناءات.']);
            }
            if ($exception) {
                abort_unless($exception->doctor_schedule_id === $schedule->id, 404);
            }
            $this->checkRevision($request->integer('revision'), $schedule);
            $date = CarbonImmutable::parse($request->validated('date'), config('thera.schedule_timezone'));
            $duplicate = $schedule->exceptions()->whereDate('date', $date->toDateString())->when($exception, fn ($query) => $query->whereKeyNot($exception->id))->exists();
            if ($duplicate) {
                throw ValidationException::withMessages(['date' => 'يوجد استثناء لهذا التاريخ. عدّل الاستثناء الموجود بدلًا من إضافته مرتين.']);
            }
            $periods = $request->validated('type') === DoctorScheduleException::TYPE_SPECIAL
                ? $this->validatePeriods(array_map(fn ($period) => array_replace($period, ['weekday' => $date->dayOfWeek]), $request->validated('periods')), $membership) : null;
            $data = [...$request->safe()->only(['date', 'type', 'note']), 'periods' => $periods];
            if ($exception) {
                $exception->update($data);
            } else {
                $schedule->exceptions()->create($data);
            }
            $schedule->increment('revision');
            $this->ensureNoConflicts($doctor);
        });
    }

    private function editableMembership(Doctor $doctor, int $id): DoctorFacility
    {
        $membership = DoctorFacility::query()->with('facility')->lockForUpdate()->find($id);
        if (! $membership || $membership->doctor_id !== $doctor->id || $membership->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $membership->facility->is_active) {
            throw ValidationException::withMessages(['doctor_facility_id' => 'اختر منشأة نشطة لديك عضوية معتمدة فيها.']);
        }

        return $membership;
    }

    private function checkRevision(int $revision, ?DoctorSchedule $schedule): void
    {
        if ($revision !== ($schedule?->revision ?? 0)) {
            throw ValidationException::withMessages(['revision' => 'تغيّر الجدول منذ فتح الصفحة. حدّث الصفحة قبل الحفظ لتجنب الكتابة فوق تغييرات أحدث.']);
        }
    }

    /** @param array<int, array<string, mixed>> $periods
     * @return list<array<string, mixed>>
     */
    private function validatePeriods(array $periods, DoctorFacility $membership): array
    {
        $services = $membership->services()->get()->keyBy('id');
        $normalized = [];
        foreach ($periods as $index => $period) {
            if ($period['end'] <= $period['start']) {
                throw ValidationException::withMessages(["periods.{$index}.end" => 'نهاية الفترة يجب أن تكون بعد بدايتها ضمن اليوم نفسه.']);
            }
            $breaks = array_values($period['breaks']);
            usort($breaks, fn ($a, $b) => strcmp($a['start'], $b['start']));
            $lastEnd = $period['start'];
            foreach ($breaks as $break) {
                if ($break['end'] <= $break['start'] || $break['start'] < $lastEnd || $break['end'] > $period['end']) {
                    throw ValidationException::withMessages(["periods.{$index}.breaks" => 'الاستراحات يجب أن تكون داخل الفترة، بنهاية بعد البداية، وبدون تداخل.']);
                }
                $lastEnd = $break['end'];
            }
            $ids = array_values(array_unique(array_map('intval', $period['service_ids'])));
            $normalizedPeriod = ['weekday' => (int) $period['weekday'], 'start' => $period['start'], 'end' => $period['end'], 'breaks' => $breaks, 'service_ids' => $ids];
            $maxMinutes = collect(DoctorSchedule::windows($normalizedPeriod))->max(fn ($window) => $window[1] - $window[0]) ?? 0;
            foreach ($ids as $id) {
                $service = $services->get($id);
                if (! $service) {
                    throw ValidationException::withMessages(["periods.{$index}.service_ids" => 'اختر خدمات تابعة لهذه المنشأة فقط.']);
                }
                if ($service->duration_minutes > $maxMinutes) {
                    throw ValidationException::withMessages(["periods.{$index}.service_ids" => "الفترة لا تتسع لخدمة {$service->name} بعد خصم الاستراحات."]);
                }
            }
            foreach ($normalized as $other) {
                if ($other['weekday'] === $normalizedPeriod['weekday'] && $this->overlaps($other, $normalizedPeriod)) {
                    throw ValidationException::withMessages(['periods' => 'لا يمكن تداخل فترتين في اليوم نفسه.']);
                }
            }
            $normalized[] = $normalizedPeriod;
        }
        usort($normalized, fn ($a, $b) => [$a['weekday'], $a['start']] <=> [$b['weekday'], $b['start']]);

        return $normalized;
    }

    private function ensureNoConflicts(Doctor $doctor): void
    {
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $schedules = DoctorSchedule::query()->where('is_active', true)
            ->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor)->where('membership_status', DoctorFacility::STATUS_ACTIVE)->whereHas('facility', fn ($query) => $query->where('is_active', true)))
            ->with(['doctorFacility.facility', 'exceptions' => fn ($query) => $query->where('date', '>=', $today->toDateString())])->get();
        $dates = $schedules->flatMap(fn ($schedule) => $schedule->exceptions->pluck('date'))->map(fn ($date) => $date->toDateString())->unique();
        foreach ($schedules as $i => $first) {
            foreach ($schedules->slice($i + 1) as $second) {
                foreach ($first->periods as $a) {
                    foreach ($second->periods as $b) {
                        if ($a['weekday'] === $b['weekday'] && $this->overlaps($a, $b)) {
                            $this->conflict($second->doctorFacility->facility->name);
                        }
                    }
                }
                foreach ($dates as $dateString) {
                    $date = CarbonImmutable::parse($dateString, config('thera.schedule_timezone'));
                    foreach ($first->periodsForDate($date) as $a) {
                        foreach ($second->periodsForDate($date) as $b) {
                            if ($this->overlaps($a, $b)) {
                                $this->conflict($second->doctorFacility->facility->name.' بتاريخ '.$dateString);
                            }
                        }
                    }
                }
            }
        }
    }

    /** @param array<string, mixed> $first
     * @param  array<string, mixed>  $second
     */
    private function overlaps(array $first, array $second): bool
    {
        return $first['start'] < $second['end'] && $first['end'] > $second['start'];
    }

    private function conflict(string $facility): never
    {
        throw ValidationException::withMessages(['periods' => 'يوجد تداخل مع دوام الطبيب في '.$facility.'. عدّل الوقت أو أوقف الجدول الآخر.']);
    }

    private function redirect(Request $request, int $membershipId, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('doctor.practice.schedule', ['membership' => $membershipId])->with('status', $message);
    }
}
