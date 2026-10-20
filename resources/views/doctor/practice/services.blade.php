<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>الخدمات والأسعار - THERA</title>
    @include('doctor.partials.workspace-assets')
</head>
<body>
@include('doctor.partials.workspace-sidebar')
@include('doctor.partials.workspace-topbar')
<main id="main-content-layout">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div><div class="small text-secondary mb-1">الإدارة <span class="material-symbols-outlined fs-6">chevron_left</span> <span class="text-primary fw-bold">الخدمات والأسعار</span></div><h1 class="h3 fw-bold mb-1">الخدمات والأسعار</h1><p class="text-secondary small mb-0">إدارة خدماتك وأسعارها ومدتها في المنشآت المرتبطة بك.</p></div>
        <button class="btn btn-primary px-4 py-2 d-inline-flex align-items-center justify-content-center gap-2" id="add-service-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#service-drawer" @disabled($memberships->isEmpty())><span class="material-symbols-outlined">add</span>إضافة خدمة</button>
    </div>
    @include('doctor.practice.partials.services-guide')
    @if($memberships->isEmpty())<div class="alert alert-info rounded-4">لا توجد عضوية نشطة في منشأة معتمدة. <a class="fw-bold" href="{{ route('doctor.account.professional-profile',['tab'=>'facilities']) }}">أضف منشأة أو أرسل طلب انضمام</a> قبل إضافة الخدمات.</div>@endif
    @if($doctor->verification_status !== 'verified')<div class="alert alert-warning rounded-4">يمكنك تجهيز الخدمات، لكن تفعيلها لقبول الحجز يتطلب توثيق حساب الطبيب. <a href="{{ route('doctor.account.verification') }}" class="fw-bold">إدارة التحقق</a></div>@endif
    <div class="row g-3 mb-4">
        @foreach([['total','إجمالي الخدمات','receipt_long'],['active','مفعّلة لقبول الحجز','event_available'],['paused','متوقفة أو غير مؤهلة','event_busy'],['duration','متوسط المدة','schedule']] as [$key,$label,$icon])
        <div class="col-6 col-xl-3"><div class="service-card p-3 p-lg-4 d-flex align-items-center justify-content-between gap-2 h-100"><div><p class="text-secondary small mb-1">{{ $label }}</p><p class="fw-bold h4 mb-0"><span class="font-num">{{ $summary[$key] }}</span>@if($key==='duration') <span class="small fs-6 text-secondary">دقيقة</span>@endif</p></div><div class="metric-icon"><span class="material-symbols-outlined">{{ $icon }}</span></div></div></div>
        @endforeach
    </div>
    <section class="service-card p-3 mb-4">
        <form data-thera-filter method="GET" action="{{ route('doctor.practice.services') }}" class="row g-2 align-items-end">
            <div class="col-12 col-lg-4"><label for="service-search" class="form-label">البحث</label><input id="service-search" class="form-control" name="search" type="search" value="{{ $search }}" placeholder="اسم الخدمة أو الوصف أو المنشأة..."></div>
            <div class="col-12 col-sm-6 col-lg-2"><label for="service-facility-filter" class="form-label">المنشأة</label><select id="service-facility-filter" class="form-select" name="membership" data-service-select><option value="">كل المنشآت</option>@foreach($memberships as $membership)<option value="{{ $membership->id }}" @selected($membershipId===$membership->id)>{{ $membership->facility->name }}</option>@endforeach</select></div>
            <div class="col-12 col-sm-6 col-lg-2"><label for="service-status-filter" class="form-label">حالة الخدمة</label><select id="service-status-filter" class="form-select" name="status" data-service-select><option value="">كل الحالات</option><option value="active" @selected($status==='active')>مفعلة</option><option value="paused" @selected($status==='paused')>متوقفة</option></select></div>
            <div class="col-12 col-sm-6 col-lg-2"><label for="service-sort" class="form-label">الترتيب</label><select id="service-sort" class="form-select" name="sort" data-service-select><option value="">الأحدث تعديلًا</option><option value="name" @selected($sort==='name')>اسم الخدمة</option><option value="duration" @selected($sort==='duration')>المدة الأقصر</option><option value="price" @selected($sort==='price')>السعر داخل كل عملة</option></select></div>
            <div class="col-auto"><a class="thera-filter-reset" data-filter-reset href="{{ route('doctor.practice.services') }}" aria-label="إعادة ضبط الفلاتر" title="إعادة ضبط الفلاتر"><span class="material-symbols-outlined" aria-hidden="true">refresh</span></a></div>
        </form>
    </section>
    <section class="service-card overflow-hidden">
        @if($services->isEmpty())
            <div class="text-center px-3 py-5"><span class="material-symbols-outlined text-primary fs-1">receipt_long</span><h2 class="h6 fw-bold mt-3">لا توجد خدمات لعرضها</h2><p class="text-secondary small mb-0">أضف خدمتك الأولى، أو غيّر الفلاتر لرؤية خدماتك المحفوظة.</p></div>
        @else
            <div class="table-responsive d-none d-lg-block"><table class="table mb-0"><thead><tr><th>الخدمة</th><th>المنشأة</th><th>السعر</th><th>المدة</th><th>قبول الحجز</th><th>آخر تعديل</th><th>الإجراءات</th></tr></thead><tbody>
                @foreach($services as $service)<tr><td style="max-width:240px"><div class="fw-bold">{{ $service->name }}</div><div class="text-secondary small text-break mt-1">{{ Str::limit($service->description,80) }}</div></td><td>{{ $service->doctorFacility->facility->name }}</td><td class="text-nowrap"><span class="font-num">{{ $service->priceForInput() }}</span> {{ $service->currency }}</td><td class="text-nowrap">{{ $service->duration_minutes }} دقيقة</td><td>@include('doctor.practice.partials.service-status')</td><td class="small text-secondary text-nowrap">{{ $service->updated_at->format('Y/m/d') }}</td><td>@include('doctor.practice.partials.service-actions')</td></tr>@endforeach
            </tbody></table></div>
            <div class="d-lg-none p-3 d-grid gap-3">@foreach($services as $service)<article class="border rounded-4 p-3"><div class="d-flex justify-content-between align-items-start gap-2"><div><h2 class="h6 fw-bold mb-1">{{ $service->name }}</h2><p class="text-secondary small mb-0">{{ $service->doctorFacility->facility->name }}</p></div>@include('doctor.practice.partials.service-status')</div><p class="small text-secondary my-3 text-break">{{ $service->description }}</p><div class="d-flex justify-content-between gap-3 small border-top pt-3 mb-3"><span><strong class="font-num">{{ $service->priceForInput() }}</strong> {{ $service->currency }}</span><span>{{ $service->duration_minutes }} دقيقة</span></div>@include('doctor.practice.partials.service-actions')</article>@endforeach</div>
            @if($services->hasPages())<div class="p-3 border-top">{{ $services->links('pagination::bootstrap-5') }}</div>@endif
        @endif
    </section>
    <p class="small text-secondary mt-3 mb-0">تفعيل الخدمة يحدد قبول الحجز؛ المواعيد المتاحة تُحسب من الدوام والاستثناءات والسعة والحجوزات المسجلة.</p>
</main>

<aside class="offcanvas offcanvas-end" id="service-drawer" tabindex="-1" aria-labelledby="service-drawer-title">
    <div class="offcanvas-header"><div><h2 class="offcanvas-title h5 fw-bold" id="service-drawer-title">{{ $editingService ? 'تعديل الخدمة' : 'إضافة خدمة' }}</h2><p class="small text-secondary mb-0 mt-1">حدّد المنشأة والسعر والمدة وتعليمات المريض.</p></div><button class="btn-close m-0" type="button" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button></div>
    <div class="offcanvas-body">
        <form id="service-form" method="POST" action="{{ $editingService ? route('doctor.practice.services.update',$editingService) : route('doctor.practice.services.store') }}">@csrf @if($editingService) @method('PUT') @endif
            <div class="mb-4"><label class="form-label" for="service-name">اسم الخدمة <span class="text-danger">*</span></label><input class="form-control @error('name') is-invalid @enderror" id="service-name" name="name" maxlength="150" value="{{ old('name',$editingService?->name) }}" required placeholder="مثل: استشارة أو متابعة">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-4"><label class="form-label" for="service-description">وصف الخدمة</label><textarea class="form-control" id="service-description" name="description" rows="3" maxlength="2000" placeholder="وصف مختصر لما تتضمنه الخدمة">{{ old('description',$editingService?->description) }}</textarea>@error('description')<p class="small text-danger">{{ $message }}</p>@enderror</div>
            <div class="mb-4"><label class="form-label" for="service-membership">مكان تقديم الخدمة <span class="text-danger">*</span></label><select class="form-select" id="service-membership" name="doctor_facility_id" required data-service-select><option value="">اختر المنشأة</option>@foreach($memberships as $membership)<option value="{{ $membership->id }}" data-bookings="{{ $membership->facility->bookings_enabled ? '1':'0' }}" @selected((string)old('doctor_facility_id',$editingService?->doctor_facility_id)===(string)$membership->id)>{{ $membership->facility->name }} — {{ $membership->facility->city }}</option>@endforeach</select><div class="form-text">تظهر المنشآت النشطة التي لديك عضوية معتمدة فيها.</div>@error('doctor_facility_id')<p class="small text-danger mt-1">{{ $message }}</p>@enderror</div>
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-4"><label class="form-label" for="service-price">السعر <span class="text-danger">*</span></label><input class="form-control font-num @error('price') is-invalid @enderror" id="service-price" name="price" type="number" min="0" max="999999.999" step="0.001" inputmode="decimal" value="{{ old('price',$editingService?->priceForInput()) }}" required placeholder="0.00">@error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-sm-4"><label class="form-label" for="service-currency">العملة <span class="text-danger">*</span></label><select class="form-select" id="service-currency" name="currency" required data-service-select><option value="">اختر العملة</option>@foreach($currencies as $code=>$currency)<option value="{{ $code }}" data-decimals="{{ $currency['decimals'] }}" @selected(old('currency',$editingService?->currency)===$code)>{{ $code }} — {{ $currency['label'] }}</option>@endforeach</select>@error('currency')<p class="small text-danger">{{ $message }}</p>@enderror</div>
                <div class="col-12 col-sm-4"><label class="form-label" for="service-duration">المدة بالدقائق <span class="text-danger">*</span></label><input class="form-control font-num @error('duration_minutes') is-invalid @enderror" id="service-duration" name="duration_minutes" type="number" min="5" max="480" step="1" value="{{ old('duration_minutes',$editingService?->duration_minutes ?? 30) }}" required>@error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <div class="alert alert-primary rounded-4 small">عربون حجز الطبيب حسب التحليل: {{ config('thera.doctor_deposit_percentage') }}% من سعر الخدمة. التحصيل الفعلي سيُربط عند بناء الحجز والدفع.</div>
            <div class="mb-4"><label class="form-label" for="service-instructions">تعليمات للمريض</label><textarea class="form-control" id="service-instructions" name="instructions" rows="3" maxlength="2000" placeholder="مثل: إحضار التقارير السابقة">{{ old('instructions',$editingService?->instructions) }}</textarea>@error('instructions')<p class="small text-danger">{{ $message }}</p>@enderror</div>
            <div class="border rounded-4 p-3 d-flex align-items-center justify-content-between gap-3"><div><label for="service-active" class="fw-bold small mb-1">متاحة لقبول الحجز عبر THERA</label><p class="form-text mb-0" id="service-activation-note">عند إيقافها تبقى بيانات الخدمة محفوظة.</p></div><div class="form-check form-switch m-0"><input name="is_active" type="hidden" value="0"><input class="form-check-input" style="width:40px;height:22px" id="service-active" name="is_active" value="1" type="checkbox" @checked(old('is_active',$editingService?->is_active ?? false))></div></div>@error('is_active')<p class="small text-danger mt-2">{{ $message }}</p>@enderror
            <p class="small text-secondary mt-3 mb-0">تغيير السعر والمدة يخص الحجوزات الجديدة. عند تنفيذ الحجز تُحفظ نسخة من بيانات الخدمة وقت الحجز.</p>
        </form>
    </div>
    <div class="service-drawer-footer d-flex justify-content-between gap-3"><button class="btn btn-light border px-4" data-bs-dismiss="offcanvas" type="button">إلغاء</button><button class="btn btn-primary px-4 d-inline-flex align-items-center justify-content-center gap-2" type="submit" form="service-form"><span class="material-symbols-outlined fs-5">save</span>حفظ الخدمة</button></div>
</aside>

@include('doctor.partials.route-map')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/doctor.js') }}" defer></script>
<script src="{{ asset('js/doctor-workspace.js') }}" defer></script>
<script type="application/json" id="practice-page-state">{!! json_encode(['verified'=>$doctor->verification_status==='verified','openDrawer'=>(bool)$editingService || session()->hasOldInput('name'),'hasEditingService'=>(bool)$editingService], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/doctor-practice-ajax.js') }}" defer></script>
<script src="{{ asset('js/doctor-services.js') }}" defer></script>
</body>
</html>
