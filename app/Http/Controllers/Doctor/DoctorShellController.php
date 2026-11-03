<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorAppointment;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPayment;
use App\Models\DoctorReferral;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorShellController extends Controller
{
    public function notifications(Request $request): JsonResponse
    {
        $doctor = $request->user('doctor');
        $preferences = $doctor->accountPreferences()['notifications'];
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $appointments = DoctorAppointment::query()->where('doctor_id', $doctor->id)->where('status', 'confirmed')
            ->whereBetween('starts_at', [$today->utc(), $today->addDay()->utc()->subSecond()]);
        $items = [];
        if ($preferences['appointments'] && ($count = (clone $appointments)->count())) {
            $items[] = ['key' => 'appointments', 'count' => $count, 'label' => 'مواعيد اليوم', 'icon' => 'calendar_today', 'url' => route('doctor.appointments.index', ['view' => 'list', 'date' => $today->toDateString()])];
        }
        if ($preferences['arrival'] && ($count = (clone $appointments)->whereNotNull('arrived_at')->whereDoesntHave('visit', fn ($query) => $query->whereNotNull('closed_at'))->count())) {
            $items[] = ['key' => 'arrival', 'count' => $count, 'label' => 'مرضى سجّلوا الوصول', 'icon' => 'how_to_reg', 'url' => route('doctor.appointments.index', ['view' => 'list', 'date' => $today->toDateString(), 'arrival' => 'arrived'])];
        }
        if ($preferences['results'] && ($count = DoctorExternalReport::query()->where('doctor_id', $doctor->id)->whereNull('reviewed_at')->count())) {
            $items[] = ['key' => 'results', 'count' => $count, 'label' => 'تقارير خارجية بانتظار المراجعة', 'icon' => 'assignment_turned_in', 'url' => route('doctor.orders.results')];
        }
        if ($preferences['referrals'] && ($count = DoctorReferral::query()->where('doctor_id', $doctor->id)->where('status', 'draft')->count())) {
            $items[] = ['key' => 'referrals', 'count' => $count, 'label' => 'إحالات ومتابعات مسودة', 'icon' => 'sync_alt', 'url' => route('doctor.clinical.referrals')];
        }
        if ($preferences['payments']) {
            $net = DoctorPayment::query()->selectRaw('COALESCE(SUM(amount_minor), 0)')->whereColumn('doctor_appointment_id', 'doctor_appointments.id');
            $count = (clone $appointments)->whereNotNull('arrived_at')->where('price_minor', '>', $net)->count();
            if ($count) {
                $items[] = ['key' => 'payments', 'count' => $count, 'label' => 'مواعيد وصلت وبقي عليها تحصيل', 'icon' => 'payments', 'url' => route('doctor.finance.collections', ['date' => $today->toDateString()])];
            }
        }

        return response()->json(['items' => $items, 'total' => array_sum(array_column($items, 'count'))])->header('Cache-Control', 'private, no-store');
    }
}
