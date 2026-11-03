<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorAppointment;
use App\Models\DoctorExternalReport;
use App\Models\DoctorReferral;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $doctor = $request->user('doctor');
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $base = DoctorAppointment::where('doctor_id', $doctor->id)
            ->where('starts_at', '>=', $today->utc())
            ->where('starts_at', '<', $today->addDay()->utc());
        $present = (clone $base)->where('status', 'confirmed')->whereNotNull('arrived_at')
            ->with(['doctor', 'queueEntry', 'visit'])
            ->withSum('payments as paid_minor', 'amount_minor')->orderBy('starts_at')->get();
        $ready = $present->filter(fn (DoctorAppointment $appointment) => in_array($appointment->queueState(), ['ready', 'called'], true));
        $counts = [
            'appointments' => (clone $base)->count(),
            'waiting' => $present->filter(fn (DoctorAppointment $appointment) => $appointment->queueState() === 'waiting_payment')->count(),
            'ready' => $ready->count(),
            'reports' => DoctorExternalReport::where('doctor_id', $doctor->id)->whereNull('reviewed_at')->count(),
            'followups' => DoctorReferral::where('doctor_id', $doctor->id)->where('kind', 'followup')
                ->where('status', 'approved')->whereNotNull('approved_at')
                ->where('content->due_date', '>=', $today->toDateString())
                ->where('content->due_date', '<', $today->addDays(7)->toDateString())
                ->whereDoesntHave('correction', fn ($query) => $query->where('status', 'approved')->whereNotNull('approved_at'))->count(),
        ];
        $ready = $ready->take(6);
        $alertCount = collect(['reports', 'waiting', 'followups'])->filter(fn (string $key) => $counts[$key] > 0)->count();

        return response()->view('doctor.dashboard', compact('doctor', 'today', 'counts', 'ready', 'alertCount'))
            ->header('Cache-Control', 'private, no-store');
    }
}
