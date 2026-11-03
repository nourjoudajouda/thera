<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DoctorPatientController extends Controller
{
    public const TABS = ['timeline' => 'التسلسل الزمني', 'appointments' => 'المواعيد', 'visits' => 'الزيارات', 'prescriptions' => 'الوصفات', 'orders' => 'طلبات الفحوصات', 'reports' => 'التقارير الخارجية', 'referrals' => 'الإحالات والمتابعات'];

    public const TEMPLATE_TABS = ['summary' => 'الملخص', 'timeline' => 'الخط الزمني', 'visits' => 'الزيارات', 'procedures' => 'الإجراءات الطبية', 'diagnoses' => 'التشخيصات', 'medications' => 'الأدوية الحالية', 'allergies' => 'الحساسية', 'prescriptions' => 'الوصفات', 'lab-results' => 'نتائج المختبر', 'imaging' => 'تقارير الأشعة', 'referrals' => 'الإحالات', 'followups' => 'المتابعات'];

    public function index(Request $request): Response|RedirectResponse
    {
        $doctor = $request->user('doctor');
        Gate::forUser($doctor)->authorize('viewAny', DoctorPatient::class);
        $filters = $request->validate([
            'tab' => ['nullable', Rule::in(['files', 'authorized', 'unlinked'])],
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['upcoming', 'with_visits'])],
            'access' => ['nullable', Rule::in(['authorized', 'limited', 'expired', 'denied'])],
            'upcoming' => ['nullable', 'boolean'],
        ]);
        if (($filters['tab'] ?? '') === 'unlinked') {
            return redirect()->route('doctor.patients.index');
        }
        $searchId = preg_match('/^(?:PT-)?([1-9][0-9]*)$/i', trim($filters['search'] ?? ''), $matches) ? $matches[1] : null;
        $base = DoctorPatient::whereBelongsTo($doctor);
        $counts = ['files' => (clone $base)->count(), 'authorized' => 0];
        $tab = $filters['tab'] ?? 'files';
        $records = $base->withCount('appointments')->withMax('appointments', 'starts_at')
            ->with(['appointments' => fn ($query) => $query->where('doctor_id', $doctor->id)->whereHas('visit')->with('visit')->latest('starts_at')->limit(1)])
            ->withMin(['appointments as next_appointment_at' => fn ($query) => $query->where('doctor_id', $doctor->id)->where('status', 'confirmed')->where('starts_at', '>=', now())], 'starts_at')
            ->when($tab === 'authorized' || ! in_array($filters['access'] ?? '', ['', 'authorized'], true), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$filters['search'].'%')->orWhere('phone', 'like', '%'.$filters['search'].'%')->orWhere('id', $searchId)))
            ->when(($filters['status'] ?? '') === 'upcoming' || $request->boolean('upcoming'), fn ($query) => $query->whereHas('appointments', fn ($query) => $query->where('doctor_id', $doctor->id)->where('status', 'confirmed')->where('starts_at', '>=', now())))
            ->when(($filters['status'] ?? '') === 'with_visits', fn ($query) => $query->whereHas('appointments.visit', fn ($query) => $query->where('doctor_id', $doctor->id)))
            ->latest('id')->paginate(15)->withQueryString();

        return response()->view('doctor.patients.index', compact('doctor', 'records', 'counts', 'tab'))->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request): Response|RedirectResponse
    {
        if (! $request->filled('patient')) {
            return $this->index($request);
        }
        $data = $request->validate(['patient' => ['required', 'integer', 'min:1'], 'tab' => ['nullable', Rule::in(array_keys(self::TABS + self::TEMPLATE_TABS))]]);
        $patient = DoctorPatient::findOrFail($data['patient']);
        $doctor = $request->user('doctor');
        Gate::forUser($doctor)->authorize('view', $patient);
        $tab = $data['tab'] ?? 'summary';
        $appointments = DoctorAppointment::where('doctor_patient_id', $patient->id)->where('doctor_id', $doctor->id);
        $visits = DoctorVisit::where('doctor_id', $doctor->id)->whereIn('doctor_appointment_id', (clone $appointments)->select('id'));
        $documents = [
            'prescriptions' => DoctorPrescription::where('doctor_id', $doctor->id)->whereIn('doctor_visit_id', (clone $visits)->select('id')),
            'orders' => DoctorDiagnosticOrder::where('doctor_id', $doctor->id)->whereIn('doctor_visit_id', (clone $visits)->select('id')),
            'referrals' => DoctorReferral::where('doctor_id', $doctor->id)->whereIn('doctor_visit_id', (clone $visits)->select('id')),
        ];
        $reports = DoctorExternalReport::where('doctor_id', $doctor->id)->whereIn('doctor_diagnostic_order_id', (clone $documents['orders'])->select('id'));
        $counts = ['appointments' => (clone $appointments)->count(), 'visits' => (clone $visits)->count(), 'reports' => (clone $reports)->count()];
        foreach ($documents as $key => $query) {
            $counts[$key] = (clone $query)->count();
        }
        $query = match ($tab) {
            'summary', 'timeline', 'appointments' => (clone $appointments)->with(['doctorFacility.facility', 'visit', 'events' => fn ($query) => $query->select('id', 'doctor_appointment_id', 'action', 'created_at', 'reason')->orderBy('id')])->withSum('payments as paid_minor', 'amount_minor')->orderByDesc('starts_at')->orderByDesc('id'),
            'visits' => (clone $visits)->with('doctorAppointment.doctorFacility.facility')->latest('id'),
            'diagnoses' => (clone $visits)->whereNotNull('content->diagnosis')->where('content->diagnosis', '!=', '')->with('doctorAppointment.doctorFacility.facility')->latest('id'),
            'procedures', 'medications', 'allergies' => (clone $visits)->whereRaw('1 = 0'),
            'reports', 'lab-results', 'imaging' => (clone $reports)->when($tab !== 'reports', fn ($query) => $query->whereHas('order', fn ($query) => $query->where('kind', $tab === 'imaging' ? 'radiology' : 'lab')))->with('order:id,kind,doctor_visit_id,document_version')->latest('id'),
            'followups' => (clone $documents['referrals'])->where('kind', 'followup')->with('correction')->latest('id'),
            default => (clone $documents[$tab])->with('correction')->latest('id'),
        };
        $records = $query->paginate(12)->withQueryString();
        $lastVisit = (clone $visits)->with('doctorAppointment')->latest('started_at')->first();
        $nextAppointment = (clone $appointments)->where('status', 'confirmed')->where('starts_at', '>=', now())->oldest('starts_at')->first();
        $openVisit = (clone $visits)->whereNull('closed_at')->latest('started_at')->first();
        $patient->events()->create(['doctor_id' => $doctor->id, 'action' => 'viewed', 'snapshot' => ['tab' => $tab]]);

        return response()->view('doctor.patients.show', compact('doctor', 'patient', 'tab', 'records', 'counts', 'lastVisit', 'nextAppointment', 'openVisit'))->header('Cache-Control', 'private, no-store');
    }
}
