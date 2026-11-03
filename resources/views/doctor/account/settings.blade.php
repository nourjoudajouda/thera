<!DOCTYPE html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الإعدادات - THERA</title>
@include('doctor.partials.workspace-assets')
<link rel="stylesheet" href="{{ asset('css/doctor-settings.css') }}">
<link rel="stylesheet" href="{{ asset('css/doctor-payment-policy.css') }}"></head><body>
@include('doctor.partials.workspace-sidebar')
@include('doctor.partials.workspace-topbar')
<main id="main-content-layout" data-settings-availability="{{ $preferences['availability'] }}">
<header class="settings-heading mb-4"><div><nav class="small text-secondary mb-2"><span data-i18n="الإدارة">الإدارة</span> / <span data-i18n="الإعدادات">الإعدادات</span></nav><h1 data-i18n="الإعدادات">الإعدادات</h1><p class="text-secondary mb-0" data-i18n="إدارة تفضيلات حساب الطبيب، الملف المهني، حالة التوفر، الإشعارات، وسياسة الحجز والدفع.">إدارة تفضيلات حساب الطبيب، الملف المهني، حالة التوفر، الإشعارات، وسياسة الحجز والدفع.</p></div><button class="btn btn-primary" type="submit" form="doctor-settings-form"><span class="material-symbols-outlined" aria-hidden="true">save</span><span data-i18n="حفظ الإعدادات">حفظ الإعدادات</span></button></header>
<section class="service-card settings-shell">
<nav class="settings-local-nav" aria-label="أقسام الإعدادات">
@foreach(['general'=>['tune','الإعدادات العامة'],'profile'=>['badge','الملف المهني والتحقق'],'availability'=>['radio_button_checked','حالة التوفر'],'notifications'=>['notifications','الإشعارات'],'payment'=>['payments','سياسة الحجز والدفع'],'stamps'=>['approval','الأختام والطباعة']] as $key=>[$icon,$label])
<button class="settings-nav-btn {{ $tab===$key?'active':'' }}" type="button" data-settings-tab="{{ $key }}" aria-controls="section-{{ $key }}" @if($tab===$key) aria-current="page" @endif><span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span><span data-i18n="{{ $label }}">{{ $label }}</span></button>
@endforeach
</nav>
<form id="doctor-settings-form" method="POST" action="{{ route('doctor.account.settings.update') }}" data-ajax-follow>
@csrf @method('PUT')<input type="hidden" name="revision" value="{{ $doctor->preferences_revision ?? 0 }}"><input type="hidden" name="tab" value="{{ $tab }}">
@foreach($errors->all() as $error)<div class="alert alert-danger" role="alert">{{ $error }}</div>@endforeach
<section id="section-general" class="settings-section" @if($tab!=='general') hidden @endif><h2 data-i18n="الإعدادات العامة">الإعدادات العامة</h2><p class="text-secondary small" data-i18n="تفضيلات واجهة الطبيب وسياق المنشأة المستخدم في المساحة الحالية.">تفضيلات واجهة الطبيب وسياق المنشأة المستخدم في المساحة الحالية.</p><div class="settings-row-group">
<div class="settings-row"><div><label class="form-label" for="setting-facility" data-i18n="المنشأة المفضلة">المنشأة المفضلة</label><p data-i18n="اختصار للوصول إلى خدماتها وجدولها؛ لا تغيّر صلاحياتك أو مواعيدك السابقة.">اختصار للوصول إلى خدماتها وجدولها؛ لا تغيّر صلاحياتك أو مواعيدك السابقة.</p></div><div class="settings-field"><select class="form-select" name="preferred_membership_id" id="setting-facility" data-appointment-select><option value="" data-i18n="بدون منشأة مفضلة">بدون منشأة مفضلة</option>@foreach($memberships as $membership)<option value="{{ $membership->id }}" @selected((string)old('preferred_membership_id',$preferences['preferred_membership_id'])===(string)$membership->id)>{{ $membership->facility->name }}</option>@endforeach</select>
@if($preferences['preferred_membership_id'] && !$memberships->contains('id',$preferences['preferred_membership_id']))<p class="small text-warning mt-2">المنشأة المحفوظة لم تعد متاحة؛ اختر عضوية نشطة قبل الحفظ.</p>@endif
</div></div>
<div class="settings-row"><div><label class="form-label" for="setting-language" data-i18n="لغة الواجهة">لغة الواجهة</label><p data-i18n="يمكن التبديل أيضًا من الشريط العلوي. تُحفظ لغة القوائم على هذا الجهاز لهذا الحساب.">يمكن التبديل أيضًا من الشريط العلوي. تُحفظ لغة القوائم على هذا الجهاز لهذا الحساب.</p></div><select id="setting-language" class="form-select settings-field"><option value="ar" data-i18n="العربية">العربية</option><option value="en">English</option></select></div>
<div class="settings-row"><div><label class="form-label" for="setting-theme" data-i18n="مظهر الواجهة">مظهر الواجهة</label><p data-i18n="يؤثر على واجهة الحساب فقط، ويُحفظ على هذا الجهاز.">يؤثر على واجهة الحساب فقط، ويُحفظ على هذا الجهاز.</p></div><select id="setting-theme" class="form-select settings-field"><option value="light" data-i18n="فاتح">فاتح</option><option value="dark" data-i18n="داكن">داكن</option><option value="system" data-i18n="حسب الجهاز">حسب الجهاز</option></select></div></div>
</section>
<section id="section-profile" class="settings-section settings-profile-template" @if($tab!=='profile') hidden @endif>
    @php
        $isVerified = $doctor->verification_status === 'verified';
        $serviceNames = $memberships->flatMap(fn ($membership) => $membership->services)->pluck('name')->unique()->values();
    @endphp
    <header class="settings-profile-heading"><h2 data-i18n="الملف المهني والتحقق">الملف المهني والتحقق</h2><p>ملخص حالة ملف الطبيب والبيانات المهنية المرتبطة به.</p></header>
    <div class="settings-verification-summary {{ $isVerified ? 'is-verified' : 'is-pending' }}" role="status">
        <span class="material-symbols-outlined" aria-hidden="true">{{ $isVerified ? 'verified' : 'pending' }}</span>
        <div><strong>{{ $isVerified ? 'تم التحقق من الطبيب' : 'لم يُعتمد التحقق المهني بعد' }}</strong><p>{{ $isVerified ? 'حالة الملف المهني: موثّق.' : 'تابع حالة الطلب والوثائق المطلوبة من صفحة التحقق المهني.' }}</p></div>
    </div>
    <dl class="settings-profile-summary">
        <div title="{{ $doctor->email }} · {{ $doctor->phone }}"><dt>الاسم</dt><dd>{{ $doctor->name }}</dd></div>
        <div><dt>التخصص</dt><dd>{{ $doctor->primarySpecialtyAssignment?->specialty?->name_ar ?? 'لم يُضف تخصص رئيسي' }}</dd></div>
        <div><dt>المنشآت المرتبطة</dt><dd>{{ $memberships->count() }} منشأة</dd></div>
        <div><dt>الخدمات</dt><dd>{{ $serviceNames->isNotEmpty() ? $serviceNames->implode('، ') : 'لم تُضف خدمات مفعّلة بعد' }}</dd></div>
    </dl>
    <div class="settings-profile-actions">
        @foreach([
            ['الملف الشخصي والمعلومات المهنية', 'البيانات الشخصية والمهنية والتخصصات.', 'إدارة الملف المهني', route('doctor.account.professional-profile')],
            ['التحقق المهني', 'حالة التحقق والوثائق المهنية المطلوبة.', 'إدارة التحقق', route('doctor.account.verification')],
            ['الإنجازات المهنية', 'الإنجازات والمعلومات المهنية المنشورة في الملف.', 'إدارة الإنجازات', route('doctor.account.professional-profile', ['tab' => 'achievements'])],
        ] as [$title, $description, $label, $url])
            <div class="settings-profile-action"><div><h3>{{ $title }}</h3><p>{{ $description }}</p></div><a class="settings-open-page" aria-label="{{ $label }}" title="{{ $label }}" href="{{ $url }}" target="_self"><span class="material-symbols-outlined" aria-hidden="true">open_in_new</span></a></div>
        @endforeach
    </div>
</section>
<section id="section-availability" class="settings-section" @if($tab!=='availability') hidden @endif><h2 data-i18n="حالة التوفر">حالة التوفر</h2><p class="text-secondary small">حالة يحددها الطبيب وتظهر في حسابه؛ ليست إثباتًا لتوفر موعد أو حضور الطبيب إلكترونيًا.</p>
<div class="settings-row"><div><label class="form-label" for="setting-availability" data-i18n="حالة التوفر الحالية">حالة التوفر الحالية</label><p>الحالة المحفوظة: {{ App\Models\Doctor::AVAILABILITY[$preferences['availability']] }}</p></div><select class="form-select settings-field" name="availability" id="setting-availability" data-appointment-select>@foreach(App\Models\Doctor::AVAILABILITY as $value=>$label)<option value="{{ $value }}" @selected(old('availability',$preferences['availability'])===$value)>{{ $label }}</option>@endforeach</select></div>
<div class="alert alert-info rounded-4 small mt-3">تغيير الحالة لا يوقف الحجوزات ولا يغير الدوام أو السعة أو الاستثناءات. استخدم صفحة الدوام لإدارة الفترات المتاحة.</div>
<div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-primary" href="{{ route('doctor.practice.schedule',array_filter(['membership'=>$preferences['preferred_membership_id']],fn($id)=>$memberships->contains('id',$id))) }}">الدوام والجدول</a><a class="btn btn-light border" href="{{ route('doctor.practice.services',array_filter(['membership'=>$preferences['preferred_membership_id']],fn($id)=>$memberships->contains('id',$id))) }}">الخدمات والأسعار</a></div>
</section>
<section id="section-notifications" class="settings-section" @if($tab!=='notifications') hidden @endif><h2 data-i18n="الإشعارات">الإشعارات</h2><p class="text-secondary small" data-i18n="تفضيلات التنبيهات المحفوظة لحسابك.">تفضيلات التنبيهات المحفوظة لحسابك.</p><div class="alert alert-info rounded-4 small">تُعرض التنبيهات التشغيلية داخل قائمة الجرس وفق هذه الخيارات. الإرسال الخارجي مستقل عنها.</div>
@foreach(App\Models\Doctor::NOTIFICATION_PREFERENCES as $key=>$label)<div class="settings-row"><label for="notify-{{ $key }}" class="form-label mb-0">{{ $label }}</label><div class="form-check form-switch mb-0"><input type="hidden" name="notifications[{{ $key }}]" value="0"><input class="form-check-input" type="checkbox" role="switch" name="notifications[{{ $key }}]" id="notify-{{ $key }}" value="1" @checked(old('notifications.'.$key,$preferences['notifications'][$key]))></div></div>@endforeach
</section>
        <section class="settings-section payment-policy" id="section-payment" data-payment-policy-editor @if($tab!=='payment') hidden @endif>
          @php
              $paymentPolicy = $preferences['payment_policy'];
              $policyType = old('payment_policy.type', $paymentPolicy['type']);
              $depositPercent = old('payment_policy.deposit_percent', $paymentPolicy['deposit_percent']);
              $remainingTiming = old('payment_policy.remaining_timing', $paymentPolicy['remaining_timing']);
              $policyType = is_string($policyType) && array_key_exists($policyType, App\Models\Doctor::PAYMENT_POLICY_TYPES) ? $policyType : $paymentPolicy['type'];
              $depositPercent = is_scalar($depositPercent) ? $depositPercent : $paymentPolicy['deposit_percent'];
              $remainingTiming = is_string($remainingTiming) && array_key_exists($remainingTiming, App\Models\Doctor::PAYMENT_REMAINING_TIMINGS) ? $remainingTiming : $paymentPolicy['remaining_timing'];
              $exampleDeposit = $policyType === 'none' ? 0 : ($policyType === 'full' ? 100 : (int) $depositPercent);
          @endphp
          <div class="mb-5">
            <h2 class="text-lg font-extrabold text-slate-900">سياسة الحجز والدفع</h2>
            <p class="text-xs text-slate-500 mt-1">السياسة الافتراضية للطبيب للحجوزات الجديدة.</p>
          </div>

          <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 mb-4 max-w-3xl">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#0495F6]">info</span>
              <div>
                <p class="text-sm font-extrabold text-blue-950">سياسة الطبيب هي الـ Default</p>
                <p class="text-xs text-blue-800 leading-6 mt-1">تُحفظ هذه السياسة لحساب الطبيب. تعديلها لا يسجّل دفعات ولا يغيّر أسعار الحجوزات السابقة. التحصيل الحالي يدوي؛ الربط الإلكتروني والسياسات الخاصة بكل خدمة لم تُفعّل بعد.</p>
              </div>
            </div>
          </div>

          <div class="border border-slate-200 rounded-xl overflow-hidden max-w-3xl">
            <div class="setting-row p-4">
              <p class="text-sm font-bold text-slate-900">المبلغ المطلوب عند الحجز</p>
              <p class="text-xs text-slate-500 mt-1 mb-3">حدد السياسة الافتراضية للحجوزات الجديدة.</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment_policy[type]" value="none" type="radio" id="payment-type-none" @checked($policyType === 'none')>
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">بدون دفعة مقدمة</p>
                    <p class="text-[11px] text-slate-500 mt-1">لا يطلب دفع عند إنشاء الحجز.</p>
                  </div>
                </label>
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment_policy[type]" value="percentage" type="radio" id="payment-type-percentage" @checked($policyType === 'percentage')>
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">دفعة مقدمة بنسبة</p>
                    <p class="text-[11px] text-slate-500 mt-1">يحدد الطبيب النسبة المطلوبة.</p>
                  </div>
                </label>
                <label class="payment-choice cursor-pointer">
                  <input class="sr-only" name="payment_policy[type]" value="full" type="radio" id="payment-type-full" @checked($policyType === 'full')>
                  <div class="border border-slate-200 rounded-xl p-3 h-full">
                    <p class="text-sm font-bold text-slate-900">دفع كامل 100%</p>
                    <p class="text-[11px] text-slate-500 mt-1">يطلب سعر الخدمة كاملًا عند الحجز.</p>
                  </div>
                </label>
              </div>
            </div>

            <div class="setting-row p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3" id="deposit-percent-row">
              <div>
                <p class="text-sm font-bold text-slate-900">نسبة الدفعة المقدمة</p>
                <p class="text-xs text-slate-500 mt-1">تستخدم فقط عند اختيار «دفعة مقدمة بنسبة».</p>
              </div>
              <div class="relative w-full md:w-[180px]">
                <input class="compact-field w-full pl-10 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900" id="deposit-percent" aria-label="نسبة الدفعة المقدمة" name="payment_policy[deposit_percent]" value="{{ $depositPercent }}" @disabled($policyType !== 'percentage') min="1" max="99" step="1" type="number">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-bold">%</span>
              </div>
            </div>

            <div class="setting-row p-4">
              <p class="text-sm font-bold text-slate-900">موعد دفع الرصيد المتبقي</p>
              <p class="text-xs text-slate-500 mt-1 mb-3">ينطبق عندما يبقى جزء غير مدفوع من قيمة الخدمة.</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="payment_policy[remaining_timing]" value="before_visit" type="radio" id="payment-remaining-before_visit" @checked($remainingTiming === 'before_visit')><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">قبل الزيارة</p><p class="text-[11px] text-slate-500 mt-1">يجب إكمال الرصيد قبل بدء الزيارة.</p></div></label>
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="payment_policy[remaining_timing]" value="at_arrival" type="radio" id="payment-remaining-at_arrival" @checked($remainingTiming === 'at_arrival')><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">عند الوصول</p><p class="text-[11px] text-slate-500 mt-1">يتم تحصيله عند الاستقبال.</p></div></label>
                <label class="payment-choice cursor-pointer"><input class="sr-only" name="payment_policy[remaining_timing]" value="after_visit" type="radio" id="payment-remaining-after_visit" @checked($remainingTiming === 'after_visit')><div class="border border-slate-200 rounded-xl p-3 h-full"><p class="text-sm font-bold text-slate-900">بعد الزيارة</p><p class="text-[11px] text-slate-500 mt-1">يسمح بإكمال الرصيد بعد انتهاء الزيارة.</p></div></label>
              </div>
            </div>

            <div class="setting-row p-4 flex items-center justify-between gap-4" id="early-payment-row">
              <div>
                <p class="text-sm font-bold text-slate-900">السماح بالدفع المبكر للرصيد</p>
                <p class="text-xs text-slate-500 mt-1">يسمح للمريض بإكمال المبلغ المتبقي قبل الموعد المحدد للدفع.</p>
              </div>
              <label class="cursor-pointer">
                <input name="payment_policy[allow_early_payment]" type="hidden" value="0"><input class="peer sr-only" id="allow-early-payment" name="payment_policy[allow_early_payment]" value="1" type="checkbox" @checked(old('payment_policy.allow_early_payment', $paymentPolicy['allow_early_payment'])) aria-label="السماح بالدفع المبكر للرصيد">
                <span class="toggle-track block"></span>
              </label>
            </div>
          </div>
          <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 max-w-3xl">
            <p class="text-sm font-extrabold text-blue-900">مثال معاينة</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-xs">
              <div><span class="text-blue-700">سعر الخدمة</span><p class="font-extrabold text-blue-950 mt-1">100.00</p></div>
              <div><span class="text-blue-700">المطلوب عند الحجز</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-deposit">{{ number_format($exampleDeposit, 2, '.', '') }}</p></div>
              <div><span class="text-blue-700">المتبقي</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-remaining">{{ number_format(100 - $exampleDeposit, 2, '.', '') }}</p></div>
              <div><span class="text-blue-700">موعد المتبقي</span><p class="font-extrabold text-blue-950 mt-1" id="payment-example-timing">{{ $policyType === 'full' ? 'مدفوع بالكامل' : (App\Models\Doctor::PAYMENT_REMAINING_TIMINGS[$remainingTiming] ?? '') }}</p></div>
            </div>
          </div>

          <details class="payment-current-info">
              <summary>التحصيل المتاح حاليًا</summary>
              <p>ليست بوابة دفع مفعّلة ولا عمولة للمنصة.</p>
              <p>تسجيل تحصيل يدوي مع سجل دفعات وإيصال.</p>
              <p>بدء الزيارة يتطلب الحضور والاستدعاء، والسداد الكامل عند تفعيل اشتراط التحصيل.</p>
              <p id="require-collection-help">اشتراط التحصيل قبل بدء الزيارة مرتبط باختيار «قبل الزيارة» أو «عند الوصول». تسجيل الحضور والاستدعاء مطلوبان في الحالتين، ولا تتأثر الزيارات التي بدأت مسبقًا.</p>
              <p>العربون الإلكتروني والاستردادات والتسويات لم تُربط بعد؛ قواعدها واتفاقات الطبيب والمنشأة تحتاج اعتمادًا مستقلًا.</p>
              <a href="{{ route('doctor.finance.collections') }}">التحصيل وسجل الدفعات</a>
          </details>
        </section>
@include('doctor.account.stamps')
<footer class="settings-footer"><span class="small text-secondary" role="status" id="settings-save-state" data-i18n="الإعدادات المعروضة من الحساب على الخادم.">الإعدادات المعروضة من الحساب على الخادم.</span><button class="btn btn-primary" type="submit" data-i18n="حفظ الإعدادات">حفظ الإعدادات</button></footer>
</form></section>
<form id="doctor-stamp-upload" action="{{ route('doctor.account.stamp.store') }}" method="POST" enctype="multipart/form-data" data-ajax-follow>@csrf</form>
</main><script type="application/json" id="practice-page-state">{}</script>
@include('doctor.appointments.partials.scripts')
</body></html>
