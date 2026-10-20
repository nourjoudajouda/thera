<section class="service-card p-3 p-lg-4 mb-4" aria-labelledby="schedule-guide-title" id="schedule-guide">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
        <div><h2 class="h6 fw-bold mb-2" id="schedule-guide-title">أول مرة تجهّز الدوام؟ امشِ بهذه الخطوات</h2><p class="small text-secondary mb-0">اختر المنشأة، ثم أضف فترات العمل وخدماتها. أكمل الخطوات أدناه حتى تظهر أوقات المعاينة.</p></div>
        <a class="small fw-bold text-nowrap" href="{{ route('doctor.practice.services', ['membership' => $membership?->id]) }}">إدارة خدمات هذه المنشأة</a>
    </div>
    <ol class="row g-2 list-unstyled mb-3">
        @foreach([['periods','1. أضف فترة وخدماتها','weekly-schedule-section'],['capacity','2. أدخل السعة اليومية','thera-capacity'],['active','3. فعّل الجدول','schedule-active'],['saved','4. احفظ ثم عاين','schedule-save-button']] as [$key,$label,$target])
            <li class="col-6 col-xl-3"><button class="setup-step border rounded-3 p-3 w-100 h-100 d-flex align-items-center gap-2 text-start" type="button" data-guide-target="{{ $target }}"><span class="material-symbols-outlined setup-check-icon" data-setup-icon="{{ $key }}" aria-hidden="true">radio_button_unchecked</span><span><span class="d-block small fw-bold">{{ $label }}</span><span class="d-block small text-secondary mt-1" data-setup-state="{{ $key }}">لم يكتمل بعد</span></span></button></li>
        @endforeach
    </ol>
    <p class="small fw-bold mb-2" id="schedule-next-step" role="status" aria-live="polite">ابدأ بإضافة فترة عمل، ثم أدخل السعة وفعّل الجدول واضغط حفظ الجدول.</p>
    <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center justify-content-between"><p class="small text-secondary mb-0">اعتماد الفترة يضيفها إلى التغييرات فقط؛ زر «حفظ الجدول» هو الذي يحفظها فعليًا.</p><button type="button" class="btn btn-light border btn-sm text-nowrap" id="schedule-guide-action">ابدأ الإعداد</button></div>
    @if($membership && (!$canEdit || !$membership->facility->bookings_enabled || $doctor->verification_status!=='verified' || $services->where('is_active',true)->isEmpty()))
        <div class="border-top pt-3 mt-3 small"><p class="fw-bold mb-2">متطلبات أخرى للظهور في المعاينة:</p><ul class="mb-0 ps-3 text-secondary">
            @if(!$canEdit)<li>يلزم اعتماد عضويتك وتفعيل المنشأة. <a href="{{ route('doctor.account.professional-profile',['tab'=>'facilities']) }}">راجع المنشآت المرتبطة</a>.</li>@endif
            @if($doctor->verification_status!=='verified')<li>حساب الطبيب يحتاج توثيقًا. <a href="{{ route('doctor.account.verification') }}">إدارة التحقق المهني</a>.</li>@endif
            @if(!$membership->facility->bookings_enabled)<li>حجوزات المنشأة غير مفعّلة؛ راجع إدارة المنشأة.</li>@endif
            @if($services->where('is_active',true)->isEmpty())<li>لا توجد خدمة مفعّلة في هذه المنشأة. <a href="{{ route('doctor.practice.services',['membership'=>$membership->id]) }}">أضف خدمة أو فعّل خدمة موجودة</a>.</li>@endif
        </ul></div>
    @endif
</section>
