<?php

namespace App\Http\Controllers\Doctor\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Practice\SaveDoctorServiceRequest;
use App\Http\Requests\Doctor\Practice\UpdateDoctorServiceStatusRequest;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorServiceController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user('doctor');
        abort_unless($doctor instanceof Doctor, 403);
        $query = DoctorService::query()->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor));
        $total = (clone $query)->count();
        $active = (clone $query)->bookingEnabled()->count();
        $averageDuration = (clone $query)->avg('duration_minutes');
        $search = $request->string('search')->trim()->limit(100)->toString();
        $status = $request->string('status')->toString();
        $membershipId = $request->integer('membership');
        $sort = $request->string('sort')->toString();
        $currencies = config('thera.currencies');
        $services = $query->with(['doctorFacility.facility', 'doctorFacility.doctor'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('doctorFacility.facility', fn ($query) => $query->where('name', 'like', "%{$search}%"))))
            ->when($status === 'active', fn ($query) => $query->bookingEnabled())
            ->when($status === 'paused', fn ($query) => $query->whereNot(fn ($query) => $query->bookingEnabled()))
            ->when($membershipId > 0, fn ($query) => $query->where('doctor_facility_id', $membershipId))
            ->when($sort === 'name', fn ($query) => $query->orderBy('name'))
            ->when($sort === 'duration', fn ($query) => $query->orderBy('duration_minutes'))
            ->when($sort === 'price', fn ($query) => $query->orderBy('currency')->orderByDesc('price_minor'))
            ->orderByDesc('updated_at')->orderByDesc('id')->paginate(15)->withQueryString();

        $memberships = $doctor->facilityMemberships()->with('facility')
            ->where('membership_status', DoctorFacility::STATUS_ACTIVE)
            ->whereHas('facility', fn ($query) => $query->where('is_active', true))->get();
        $editingService = null;
        if ($request->integer('edit') > 0) {
            $editingService = DoctorService::query()->findOrFail($request->integer('edit'));
            Gate::forUser($doctor)->authorize('update', $editingService);
        }

        return view('doctor.practice.services', [
            'doctor' => $doctor, 'services' => $services, 'memberships' => $memberships,
            'currencies' => $currencies, 'editingService' => $editingService,
            'search' => $search, 'status' => $status, 'membershipId' => $membershipId, 'sort' => $sort,
            'summary' => [
                'total' => $total,
                'active' => $active,
                'paused' => $total - $active,
                'duration' => (int) round($averageDuration ?? 0),
            ],
        ]);
    }

    public function store(SaveDoctorServiceRequest $request): RedirectResponse|JsonResponse
    {
        $this->save($request);

        return $this->saved($request, 'تمت إضافة الخدمة بنجاح.');
    }

    public function update(SaveDoctorServiceRequest $request, DoctorService $doctorService): RedirectResponse|JsonResponse
    {
        $this->save($request, $doctorService);

        return $this->saved($request, 'تم تحديث الخدمة بنجاح.');
    }

    public function updateStatus(UpdateDoctorServiceStatusRequest $request, DoctorService $doctorService): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($request, $doctorService): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorService->refresh();
            $membership = DoctorFacility::query()->lockForUpdate()->findOrFail($doctorService->doctor_facility_id);
            if ($request->boolean('is_active')) {
                $this->ensureMembership($doctor, $membership, true);
            }
            $doctorService->update(['is_active' => $request->boolean('is_active')]);
        });

        return $this->saved($request, $request->boolean('is_active') ? 'تم تفعيل الخدمة لقبول الحجوزات.' : 'تم إيقاف الحجز للخدمة مع الاحتفاظ ببياناتها.');
    }

    private function saved(Request $request, string $message): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route('doctor.practice.services')->with('status', $message);
    }

    private function save(SaveDoctorServiceRequest $request, ?DoctorService $service = null): void
    {
        DB::transaction(function () use ($request, $service): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $membership = DoctorFacility::query()->lockForUpdate()->find($request->integer('doctor_facility_id'));
            $this->ensureMembership($doctor, $membership, $request->boolean('is_active'));
            if ($service) {
                $service->refresh();
                if ($service->doctor_facility_id !== $membership->id) {
                    if (DoctorAppointment::query()->where('doctor_service_id', $service->id)->where('status', 'confirmed')->where('ends_at', '>', now())->exists()) {
                        throw ValidationException::withMessages(['doctor_facility_id' => 'لا يمكن نقل خدمة لها مواعيد قادمة إلى منشأة أخرى. أعد جدولة المواعيد أو ألغها أولًا.']);
                    }
                    $schedules = DoctorSchedule::query()->where('doctor_facility_id', $service->doctor_facility_id)->with('exceptions')->get();
                    foreach ($schedules as $schedule) {
                        $periods = [...$schedule->periods, ...$schedule->exceptions->flatMap(fn ($exception) => $exception->periods ?? [])->all()];
                        foreach ($periods as $period) {
                            if (in_array($service->id, $period['service_ids'], true)) {
                                throw ValidationException::withMessages(['doctor_facility_id' => 'الخدمة مرتبطة بفترات دوام أو ساعات خاصة. أزل ارتباطها من الجدول والاستثناءات قبل نقلها إلى منشأة أخرى.']);
                            }
                        }
                    }
                }
            }
            if (DoctorService::query()->where('doctor_facility_id', $membership->id)
                ->where('name', $request->validated('name'))
                ->when($service, fn ($query) => $query->whereKeyNot($service->id))->exists()) {
                throw ValidationException::withMessages(['name' => 'توجد خدمة بهذا الاسم في المنشأة نفسها.']);
            }
            $data = [
                ...$request->safe()->only(['doctor_facility_id', 'name', 'description', 'currency', 'duration_minutes', 'instructions', 'is_active']),
                'price_minor' => DoctorService::priceToMinor($request->validated('price'), $request->validated('currency')),
            ];
            if ($service) {
                $service->update($data);
            } else {
                DoctorService::query()->create($data);
            }
        });
    }

    private function ensureMembership(Doctor $doctor, ?DoctorFacility $membership, bool $activate): void
    {
        if (! $membership || $membership->doctor_id !== $doctor->id
            || $membership->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $membership->facility->is_active) {
            throw ValidationException::withMessages(['doctor_facility_id' => 'اختر منشأة نشطة لديك عضوية معتمدة فيها.']);
        }
        if ($activate && ($doctor->verification_status !== 'verified' || ! $membership->facility->bookings_enabled)) {
            throw ValidationException::withMessages(['is_active' => 'تفعيل الخدمة يتطلب توثيق الطبيب وتفعيل حجوزات المنشأة. يمكنك حفظها متوقفة مؤقتًا.']);
        }
    }
}
