<section aria-labelledby="profile-tab-credentials" class="profile-panel" data-panel="credentials" id="profile-panel-credentials" role="tabpanel">
    <div class="profile-card p-5 md:p-6">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-5">
            <div><h3 class="text-lg font-extrabold text-[#0B1020]">المؤهلات والشهادات</h3><p class="text-sm text-slate-500 mt-1">أضف مؤهلاتك مع ملف إثبات خاص للمراجعة.</p></div>
            <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" data-bs-target="#add-credential-modal" data-bs-toggle="modal" type="button"><span class="material-symbols-outlined text-[18px]">add</span>إضافة مؤهل</button>
        </div>

        @if ($credentials->isEmpty())
            <div class="onboarding-empty"><span class="material-symbols-outlined">workspace_premium</span><p class="font-extrabold text-slate-800">لا توجد مؤهلات مضافة</p><p class="text-xs text-slate-500 mt-1.5 leading-6">أضف أول مؤهل أو شهادة مع ملف الإثبات.</p></div>
        @else
            <div class="d-flex flex-column gap-3" id="credential-list">
                @foreach ($credentials as $credential)
                    @php
                        $credentialType = match ($credential->type) { 'qualification' => 'مؤهل', 'certificate' => 'شهادة', 'fellowship' => 'زمالة', 'board' => 'بورد', default => $credential->type };
                        $status = match ($credential->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200'] };
                    @endphp
                    <article class="credential-card p-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                        <div class="min-w-0">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="badge text-bg-light border">{{ $credentialType }}</span><p class="font-extrabold text-slate-800 mb-0">{{ $credential->title }}</p><span class="status-pill {{ $status[1] }} h-6">{{ $status[0] }}</span></div>
                            <p class="text-xs text-slate-500 mt-2 mb-0">{{ $credential->issuer }} • {{ $credential->country }} • {{ $credential->issued_on->year }}</p>
                            @if ($credential->expires_on)<p class="text-xs text-slate-400 mt-1 mb-0">ينتهي في {{ $credential->expires_on->format('Y-m-d') }}</p>@endif
                            @if ($credential->verification_status === 'rejected' && $credential->rejection_reason)<p class="small text-danger mt-2 mb-0">{{ $credential->rejection_reason }}</p>@endif
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('doctor.account.professional-profile.credentials.document', $credential) }}"><span class="material-symbols-outlined fs-6 align-middle">download</span> ملف الإثبات</a>
                            @if ($credential->verification_status !== 'verified')
                                <form action="{{ route('doctor.account.professional-profile.credentials.destroy', $credential) }}" method="POST" data-thera-confirm="هل تريد حذف هذا المؤهل؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">حذف</button></form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <div aria-hidden="true" aria-labelledby="add-credential-title" class="modal fade profile-modal" id="add-credential-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form action="{{ route('doctor.account.professional-profile.credentials.store', ['tab' => 'credentials']) }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-header"><h4 class="modal-title fs-6 fw-bold" id="add-credential-title">إضافة مؤهل أو شهادة</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
                    <div class="modal-body"><div class="row g-4">
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-type">النوع</label><select class="form-select @error('type', 'credential') is-invalid @enderror" id="credential-type" name="type" required><option value="">اختر النوع</option><option value="qualification" @selected(old('type') === 'qualification')>مؤهل</option><option value="certificate" @selected(old('type') === 'certificate')>شهادة</option><option value="fellowship" @selected(old('type') === 'fellowship')>زمالة</option><option value="board" @selected(old('type') === 'board')>بورد</option></select>@error('type', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-title">اسم المؤهل أو الشهادة</label><input class="form-control @error('title', 'credential') is-invalid @enderror" id="credential-title" name="title" type="text" value="{{ old('title') }}" required>@error('title', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-issuer">الجهة المصدرة</label><input class="form-control @error('issuer', 'credential') is-invalid @enderror" id="credential-issuer" name="issuer" type="text" value="{{ old('issuer') }}" required>@error('issuer', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-country">الدولة</label><input class="form-control @error('country', 'credential') is-invalid @enderror" id="credential-country" name="country" type="text" value="{{ old('country') }}" required>@error('country', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-issued-on">تاريخ الإصدار</label><input class="form-control @error('issued_on', 'credential') is-invalid @enderror" id="credential-issued-on" name="issued_on" type="date" value="{{ old('issued_on') }}" required>@error('issued_on', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="field-label form-label" for="credential-expires-on">تاريخ الانتهاء <span class="text-secondary fw-normal">(اختياري)</span></label><input class="form-control @error('expires_on', 'credential') is-invalid @enderror" id="credential-expires-on" name="expires_on" type="date" value="{{ old('expires_on') }}">@error('expires_on', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><label class="field-label form-label" for="credential-document">ملف الإثبات</label><input accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('document', 'credential') is-invalid @enderror" id="credential-document" name="document" type="file" required><div class="form-text">PDF أو JPG أو PNG، بحد أقصى 5 ميجابايت. الملف خاص ولا يظهر للمرضى.</div>@error('document', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div></div>
                    <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">إلغاء</button><button class="btn btn-primary" type="submit">حفظ وإرسال للمراجعة</button></div>
                </form>
            </div>
        </div>
    </div>
</section>
