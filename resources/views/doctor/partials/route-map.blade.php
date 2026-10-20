<script>
    window.doctorPage = @js(Route::currentRouteName());
    window.doctorRoutes = @js([
        'dashboard' => route('doctor.dashboard'),
        'appointments' => route('doctor.appointments.index'),
        'appointmentCreate' => route('doctor.appointments.create'),
        'appointmentShow' => route('doctor.appointments.show'),
        'patients' => route('doctor.patients.index'),
        'patientShow' => route('doctor.patients.show'),
        'visits' => route('doctor.clinical.visits'),
        'visit' => route('doctor.clinical.visit'),
        'prescriptions' => route('doctor.clinical.prescriptions'),
        'labOrders' => route('doctor.orders.lab'),
        'radiologyOrders' => route('doctor.orders.radiology'),
        'results' => route('doctor.orders.results'),
        'referrals' => route('doctor.clinical.referrals'),
        'services' => route('doctor.practice.services'),
        'schedule' => route('doctor.practice.schedule'),
        'collections' => route('doctor.finance.collections'),
        'verification' => route('doctor.account.verification'),
        'profile' => route('doctor.account.professional-profile'),
        'settings' => route('doctor.account.settings'),
        'login' => route('doctor.login'),
        'register' => route('doctor.register'),
        'forgotPassword' => route('doctor.password.request'),
        'resetPassword' => route('doctor.password.request'),
        'logout' => route('doctor.logout'),
    ]);
    window.csrfToken = @js(csrf_token());
</script>
@auth('doctor')
    <form id="doctor-logout-form" method="POST" action="{{ route('doctor.logout') }}" hidden>
        @csrf
    </form>
@endauth
