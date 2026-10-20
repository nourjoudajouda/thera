<?php

namespace App\Http\Controllers\Doctor\Finance;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorPayment;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorCollectionController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user('doctor');
        $data = $request->validate(['date' => ['nullable', 'date_format:Y-m-d'], 'membership' => ['nullable', 'integer'], 'search' => ['nullable', 'string', 'max:100'], 'payment_status' => ['nullable', Rule::in(['open', 'settled'])], 'appointment' => ['nullable', 'integer']]);
        $day = CarbonImmutable::parse($data['date'] ?? CarbonImmutable::today(config('thera.schedule_timezone'))->toDateString(), config('thera.schedule_timezone'));
        $memberships = $doctor->facilityMemberships()->with('facility')->get();
        if ($request->filled('membership')) {
            abort_unless($memberships->contains('id', $request->integer('membership')), 404);
        }
        $query = DoctorAppointment::query()->whereBelongsTo($doctor)->where('starts_at', '>=', $day->utc())->where('starts_at', '<', $day->addDay()->utc())
            ->when($request->filled('membership'), fn ($query) => $query->where('doctor_facility_id', $request->integer('membership')))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('patient_name', 'like', '%'.$data['search'].'%')->orWhere('patient_phone', 'like', '%'.$data['search'].'%')->orWhere('id', $data['search'])));
        $net = DoctorPayment::query()->selectRaw('COALESCE(SUM(amount_minor), 0)')->whereColumn('doctor_appointment_id', 'doctor_appointments.id');
        if ($request->filled('payment_status')) {
            $query->where('price_minor', $data['payment_status'] === 'open' ? '>' : '<=', $net);
            if ($data['payment_status'] === 'open') {
                $query->whereIn('status', ['confirmed', 'completed']);
            }
        }
        $summaryRows = (clone $query)->withSum('payments as paid_minor', 'amount_minor')->get();
        $totals = $summaryRows->groupBy('currency')->map(fn ($rows) => ['paid' => $rows->sum(fn ($item) => $item->paidMinor()), 'remaining' => $rows->whereIn('status', ['confirmed', 'completed'])->sum(fn ($item) => $item->remainingMinor())]);
        $appointments = $query->with(['doctorFacility.facility', 'queueEntry'])->withSum('payments as paid_minor', 'amount_minor')->orderBy('starts_at')->orderBy('id')->paginate(20)->withQueryString();
        $selected = $request->filled('appointment') ? DoctorAppointment::query()->whereBelongsTo($doctor)->with(['doctorFacility.facility', 'queueEntry', 'payments.reversal'])->withSum('payments as paid_minor', 'amount_minor')->findOrFail($request->integer('appointment')) : null;

        return view('doctor.finance.collections', compact('doctor', 'day', 'memberships', 'appointments', 'selected', 'totals'));
    }

    public function store(Request $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);
        $decimals = (int) config('thera.currencies.'.$doctorAppointment->currency.'.decimals', 2);
        $request->validate(['amount' => ['required', 'string', 'regex:/\A[0-9]{1,9}(?:\.[0-9]{1,'.$decimals.'})?\z/'], 'method' => ['required', Rule::in(array_keys(DoctorPayment::METHODS))], 'reference' => ['required_if:method,bank_transfer', 'nullable', 'string', 'max:100'], 'reason' => ['nullable', 'string', 'max:500'], 'revision' => ['required', 'integer', 'min:1'], 'request_key' => ['required', 'uuid']], ['amount.regex' => 'أدخل مبلغًا صحيحًا وفق دقة عملة الموعد، بدون فواصل آلاف أو صيغة أسية.', 'reference.required_if' => 'أدخل مرجع التحويل المسجل للتحقق منه.', 'request_key.required' => 'أعد فتح نموذج التحصيل ثم حاول مجددًا.']);
        [$whole, $fraction] = array_pad(explode('.', $request->input('amount'), 2), 2, '');
        $amount = (int) $whole * (10 ** $decimals) + (int) str_pad($fraction, $decimals, '0');
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'مبلغ التحصيل يجب أن يكون أكبر من صفر.']);
        }

        return $this->save($request, $doctorAppointment, $amount);
    }

    public function reverse(Request $request, DoctorPayment $doctorPayment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorPayment->doctorAppointment);
        $request->validate(['reason' => ['required', 'string', 'min:3', 'max:500'], 'revision' => ['required', 'integer', 'min:1'], 'request_key' => ['required', 'uuid']], ['reason.required' => 'اكتب سبب عكس القيد.']);

        return $this->save($request, $doctorPayment->doctorAppointment, -$doctorPayment->amount_minor, $doctorPayment);
    }

    private function save(Request $request, DoctorAppointment $appointment, int $amount, ?DoctorPayment $original = null): JsonResponse|RedirectResponse
    {
        $payment = DB::transaction(function () use ($request, $appointment, $amount, $original): DoctorPayment {
            Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $appointment->refresh()->load(['doctorFacility.facility', 'queueEntry'])->loadSum('payments as paid_minor', 'amount_minor');
            $kind = $original ? 'reversal' : 'payment';
            $payload = ['appointment' => $appointment->id, 'kind' => $kind, 'amount' => $amount, 'method' => $original?->method ?? $request->input('method'), 'reference' => $original ? $original->reference : $request->input('reference'), 'reason' => $request->input('reason'), 'original' => $original?->id];
            $fingerprint = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
            $existing = DoctorPayment::query()->where('doctor_id', $request->user('doctor')->id)->where('request_key', $request->input('request_key'))->first();
            if ($existing) {
                if (! hash_equals($existing->request_fingerprint, $fingerprint)) {
                    throw ValidationException::withMessages(['request_key' => 'استُخدم مفتاح العملية لبيانات مختلفة؛ أعد فتح النموذج.']);
                }

                return $existing;
            }
            if ($appointment->revision !== $request->integer('revision')) {
                throw ValidationException::withMessages(['revision' => 'تغيّر الموعد أو التحصيل؛ حدّث البيانات قبل المتابعة.']);
            }
            if ($original) {
                if ($original->kind !== 'payment' || $original->reversal()->exists()) {
                    throw ValidationException::withMessages(['reason' => 'لا يمكن عكس هذا القيد أو عكسه أكثر من مرة.']);
                }
                if ($appointment->queueEntry) {
                    throw ValidationException::withMessages(['reason' => 'لا يُعكس القيد بعد استدعاء المريض من هذه الشاشة؛ يلزم مسار مراجعة مالية منفصل.']);
                }
            } else {
                if (! in_array($appointment->status, ['confirmed', 'completed'], true) || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
                    throw ValidationException::withMessages(['amount' => 'لا يُسجل تحصيل لموعد ملغى أو عدم حضور، أو خارج عضوية ومنشأة نشطتين.']);
                }
                if ($amount > $appointment->remainingMinor()) {
                    throw ValidationException::withMessages(['amount' => 'المبلغ أكبر من المتبقي على هذا الموعد؛ حدّث البيانات قبل المتابعة.']);
                }
            }
            $payment = $appointment->payments()->create([
                'doctor_id' => $request->user('doctor')->id, 'kind' => $kind, 'amount_minor' => $amount, 'currency' => $appointment->currency,
                'method' => $original?->method ?? $request->input('method'), 'reference' => $original ? $original->reference : $request->input('reference'), 'reason' => $request->input('reason'),
                'reversal_of_id' => $original?->id, 'request_key' => $request->input('request_key'), 'request_fingerprint' => $fingerprint,
                'receipt_snapshot' => $original?->receipt_snapshot ?? ['patient_name' => $appointment->patient_name, 'service_name' => $appointment->service_name, 'facility_name' => $appointment->doctorFacility->facility->name, 'starts_at' => $appointment->starts_at->toIso8601String(), 'price_minor' => $appointment->price_minor, 'currency' => $appointment->currency],
            ]);
            $before = ['paid_minor' => $appointment->paidMinor(), 'revision' => $appointment->revision];
            $appointment->update(['revision' => $appointment->revision + 1]);
            $appointment->events()->create(['doctor_id' => $request->user('doctor')->id, 'action' => $original ? 'payment_reversed' : 'payment_recorded', 'reason' => $request->input('reason'), 'before' => $before, 'after' => ['payment_id' => $payment->id, 'amount_minor' => $amount, 'currency' => $payment->currency, 'paid_minor' => $before['paid_minor'] + $amount, 'revision' => $appointment->revision]]);

            return $payment;
        }, 3);
        $message = $original ? 'حُفظ قيد عكسي مستقل مع السبب. لا ينفذ هذا الإجراء استردادًا ماليًا فعليًا.' : 'تم تسجيل التحصيل وحفظ الإيصال وتحديث المتبقي.';
        $url = route('doctor.finance.collections', ['appointment' => $appointment->id, 'date' => $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->toDateString()]);

        return $request->expectsJson() ? response()->json(['message' => $message, 'url' => $url, 'receipt_url' => route('doctor.finance.receipt', $payment)]) : redirect($url)->with('status', $message);
    }

    public function receipt(Request $request, DoctorPayment $doctorPayment): View
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $doctorPayment->doctorAppointment);
        $doctorPayment->load(['doctor', 'original', 'reversal']);

        return view('doctor.finance.receipt', ['payment' => $doctorPayment]);
    }
}
