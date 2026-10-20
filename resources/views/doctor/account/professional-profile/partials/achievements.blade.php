<section aria-labelledby="profile-tab-achievements" class="profile-panel" data-panel="achievements" id="profile-panel-achievements" role="tabpanel">
    <div class="profile-card p-5 md:p-6">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-5">
            <div><h3 class="text-lg font-extrabold text-[#0B1020]">الإنجازات المهنية</h3><p class="text-sm text-slate-500 mt-1">الجوائز والاعتمادات والإنجازات المهنية القابلة للتحقق.</p></div>
            <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" data-bs-target="#add-achievement-modal" data-bs-toggle="modal" type="button"><span class="material-symbols-outlined text-[18px]">add</span>إضافة إنجاز</button>
        </div>

        @if ($achievements->isEmpty())
            <div class="onboarding-empty"><span class="material-symbols-outlined">military_tech</span><p class="font-extrabold text-slate-800">لا توجد إنجازات مضافة</p><p class="text-xs text-slate-500 mt-1.5 leading-6">أضف إنجازًا مهنيًا وأرفق إثباته لإرساله للمراجعة.</p></div>
        @else
            <div class="row g-4" id="achievement-list">
                @foreach ($achievements as $achievement)
                    @php
                        $achievementType = match ($achievement->type) { 'award' => 'جائزة', 'accreditation' => 'اعتماد', 'honor' => 'تكريم', 'professional' => 'إنجاز مهني', default => $achievement->type };
                        $status = match ($achievement->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200'] };
                    @endphp
                    <div class="col-12 col-xl-6">
                        <article class="achievement-card p-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-3"><div><span class="badge text-bg-light border">{{ $achievementType }}</span><p class="font-extrabold text-slate-800 mt-3 mb-1">{{ $achievement->title }}</p><p class="text-xs text-slate-500 mb-0">{{ $achievement->organization }} • {{ $achievement->year }}</p></div><span class="status-pill {{ $status[1] }} h-7 shrink-0">{{ $status[0] }}</span></div>
                            <p class="text-sm text-slate-600 leading-7 mt-3 mb-0">{{ $achievement->description }}</p>
                            @if ($achievement->verification_status === 'rejected' && $achievement->rejection_reason)<p class="small text-danger mt-2 mb-0">{{ $achievement->rejection_reason }}</p>@endif
                            <div class="mt-auto pt-3 border-top d-flex flex-wrap justify-content-end gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('doctor.account.professional-profile.achievements.evidence', $achievement) }}"><span class="material-symbols-outlined fs-6 align-middle">download</span> ملف الإثبات</a>
                                @if ($achievement->verification_status !== 'verified')<form action="{{ route('doctor.account.professional-profile.achievements.destroy', $achievement) }}" method="POST" data-thera-confirm="هل تريد حذف هذا الإنجاز؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">حذف</button></form>@endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div aria-hidden="true" aria-labelledby="add-achievement-title" class="modal fade profile-modal" id="add-achievement-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content"><form action="{{ route('doctor.account.professional-profile.achievements.store', ['tab' => 'achievements']) }}" enctype="multipart/form-data" method="POST">@csrf
            <div class="modal-header"><h4 class="modal-title fs-6 fw-bold" id="add-achievement-title">إضافة إنجاز مهني</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
            <div class="modal-body"><div class="row g-4">
                <div class="col-12 col-md-6"><label class="field-label form-label" for="achievement-type">نوع الإنجاز</label><select class="form-select @error('type', 'achievement') is-invalid @enderror" id="achievement-type" name="type" required><option value="">اختر النوع</option><option value="award" @selected(old('type') === 'award')>جائزة</option><option value="accreditation" @selected(old('type') === 'accreditation')>اعتماد</option><option value="honor" @selected(old('type') === 'honor')>تكريم</option><option value="professional" @selected(old('type') === 'professional')>إنجاز مهني</option></select>@error('type', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="field-label form-label" for="achievement-title">عنوان الإنجاز</label><input class="form-control @error('title', 'achievement') is-invalid @enderror" id="achievement-title" name="title" type="text" value="{{ old('title') }}" required>@error('title', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-8"><label class="field-label form-label" for="achievement-organization">الجهة</label><input class="form-control @error('organization', 'achievement') is-invalid @enderror" id="achievement-organization" name="organization" type="text" value="{{ old('organization') }}" required>@error('organization', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-4"><label class="field-label form-label" for="achievement-year">السنة</label><input class="form-control font-num @error('year', 'achievement') is-invalid @enderror" id="achievement-year" max="{{ now()->year }}" min="1950" name="year" type="number" value="{{ old('year') }}" required>@error('year', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="field-label form-label" for="achievement-description">وصف مختصر</label><textarea class="form-control @error('description', 'achievement') is-invalid @enderror" id="achievement-description" maxlength="2000" name="description" required>{{ old('description') }}</textarea>@error('description', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="field-label form-label" for="achievement-evidence">ملف الإثبات</label><input accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('evidence', 'achievement') is-invalid @enderror" id="achievement-evidence" name="evidence" type="file" required><div class="form-text">PDF أو JPG أو PNG، بحد أقصى 5 ميجابايت. الملف خاص ولا يظهر للمرضى.</div>@error('evidence', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div></div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">إلغاء</button><button class="btn btn-primary" type="submit">حفظ وإرسال للمراجعة</button></div>
        </form></div></div>
    </div>
</section>
