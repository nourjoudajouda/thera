<section aria-labelledby="profile-tab-credentials" class="profile-panel" data-panel="credentials" id="profile-panel-credentials" role="tabpanel">
    <div class="profile-card p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
            <div><h3 class="text-lg font-extrabold text-[#0B1020]" data-i18n="المؤهلات والشهادات">المؤهلات والشهادات</h3><p class="text-sm text-slate-500 mt-1">المؤهلات المهنية مع حالة التحقق لكل عنصر.</p></div>
            <button class="btn btn-primary inline-flex items-center justify-center gap-2" data-bs-target="#add-credential-modal" data-bs-toggle="modal" type="button"><span class="material-symbols-outlined text-[18px]">add</span>إضافة مؤهل</button>
        </div>

        @if ($credentials->isEmpty())
            <div class="onboarding-empty"><span class="material-symbols-outlined">workspace_premium</span><p class="font-extrabold text-slate-800">لا توجد مؤهلات مضافة</p><p class="text-xs text-slate-500 mt-1.5 leading-6">أضف أول مؤهل أو شهادة مع ملف الإثبات.</p></div>
        @else
            <div class="flex flex-col gap-3" id="credential-list">
                @foreach ($credentials as $credential)
                    @php
                        $credentialType = match ($credential->type) { 'qualification' => 'مؤهل', 'certificate' => 'شهادة', 'fellowship' => 'زمالة', 'board' => 'بورد', default => $credential->type };
                        $status = match ($credential->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200'] };
                    @endphp
                    <article class="credential-card p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4" data-profile-credential="{{ $credential->id }}">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-extrabold text-slate-800">{{ $credential->title }}</p>
                                <span class="status-pill {{ $status[1] }} h-6">{{ $status[0] }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">{{ $credential->issuer }} • {{ $credential->country }} • {{ $credential->issued_on->year }}</p>
                        </div>
                        <button class="text-xs font-bold text-[#0495F6] hover:text-[#0466F1] shrink-0" data-bs-toggle="modal" data-bs-target="#credential-details-{{ $credential->id }}" type="button">{{ $credential->verification_status === 'verified' ? 'عرض التفاصيل' : 'عرض المستند' }}</button>
                    </article>
                    <div aria-hidden="true" aria-labelledby="credential-details-title-{{ $credential->id }}" class="modal fade profile-modal" id="credential-details-{{ $credential->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content">
                            <div class="modal-header"><h4 class="modal-title" id="credential-details-title-{{ $credential->id }}">{{ $credential->verification_status === 'verified' ? 'تفاصيل المؤهل' : 'عرض المستند' }}</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
                            <div class="modal-body">
                                @if ($credential->verification_status === 'verified')
                                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-start gap-3">
                                        <span class="material-symbols-outlined text-emerald-600">verified</span>
                                        <div><p class="font-extrabold text-emerald-800">{{ $credential->title }}</p><p class="text-xs text-emerald-700 mt-1">مؤهل موثّق في THERA</p></div>
                                    </div>
                                @endif
                                <div class="{{ $credential->verification_status === 'verified' ? 'mt-4' : 'grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_260px] gap-4' }}">
                                    @if ($credential->verification_status !== 'verified')
                                        <div class="profile-document-preview rounded-2xl bg-slate-100 border border-slate-200 p-5">
                                            @if (in_array(strtolower(pathinfo($credential->document_original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true))
                                                <img src="{{ route('doctor.account.professional-profile.credentials.document', ['doctorCredential' => $credential, 'preview' => 1]) }}" alt="ملف إثبات {{ $credential->title }}">
                                            @else
                                                <iframe loading="lazy" sandbox title="ملف إثبات {{ $credential->title }}" src="{{ route('doctor.account.professional-profile.credentials.document', ['doctorCredential' => $credential, 'preview' => 1]) }}"></iframe>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="rounded-2xl border border-slate-200 px-4 h-fit">
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">النوع</span><strong class="text-sm text-slate-800">{{ $credentialType }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">الجهة المصدرة</span><strong class="text-sm text-slate-800">{{ $credential->issuer }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">الدولة</span><strong class="text-sm text-slate-800">{{ $credential->country }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">تاريخ الإصدار</span><strong class="text-sm text-slate-800">{{ $credential->issued_on->format('Y-m-d') }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">تاريخ الانتهاء</span><strong class="text-sm text-slate-800">{{ $credential->expires_on?->format('Y-m-d') ?? 'لا ينطبق' }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">حالة التحقق</span><strong class="text-sm text-slate-800">{{ $status[0] }}</strong></div>
                                    </div>
                                </div>
                                @if ($credential->verification_status === 'rejected' && $credential->rejection_reason)<p class="text-sm text-red-600 mt-3">{{ $credential->rejection_reason }}</p>@endif
                                <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-500">ملف الإثبات خاص ولا يظهر للمريض. الملف العام يعرض اسم المؤهل والجهة والسنة وحالة التحقق فقط.</div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-light" data-bs-dismiss="modal" type="button">إغلاق</button>
                                <a class="btn btn-outline-primary" href="{{ route('doctor.account.professional-profile.credentials.document', $credential) }}">تحميل ملف الإثبات</a>
                                @if ($credential->verification_status !== 'verified')
                                    <form action="{{ route('doctor.account.professional-profile.credentials.destroy', $credential) }}" method="POST" data-thera-confirm="هل تريد حذف هذا المؤهل؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit">حذف</button></form>
                                @endif
                            </div>
                        </div></div>
                    </div>
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
                    <div class="modal-body"><div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="min-w-0"><label class="field-label form-label" for="credential-type">النوع</label><select class="form-select @error('type', 'credential') is-invalid @enderror" id="credential-type" name="type" required><option value="">اختر النوع</option><option value="qualification" @selected(old('type') === 'qualification')>مؤهل</option><option value="certificate" @selected(old('type') === 'certificate')>شهادة</option><option value="fellowship" @selected(old('type') === 'fellowship')>زمالة</option><option value="board" @selected(old('type') === 'board')>بورد</option></select>@error('type', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="min-w-0"><label class="field-label form-label" for="credential-title">اسم المؤهل أو الشهادة</label><input class="form-control @error('title', 'credential') is-invalid @enderror" id="credential-title" name="title" type="text" value="{{ old('title') }}" required>@error('title', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="min-w-0"><label class="field-label form-label" for="credential-issuer">الجهة المصدرة</label><input class="form-control @error('issuer', 'credential') is-invalid @enderror" id="credential-issuer" name="issuer" type="text" value="{{ old('issuer') }}" required>@error('issuer', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="min-w-0"><label class="field-label form-label" for="credential-country">الدولة</label><input class="form-control @error('country', 'credential') is-invalid @enderror" id="credential-country" name="country" type="text" value="{{ old('country') }}" required>@error('country', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="min-w-0"><label class="field-label form-label" for="credential-issued-on">تاريخ الإصدار</label><input class="form-control @error('issued_on', 'credential') is-invalid @enderror" id="credential-issued-on" name="issued_on" type="date" value="{{ old('issued_on') }}" required>@error('issued_on', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="min-w-0"><label class="field-label form-label" for="credential-expires-on">تاريخ الانتهاء <span class="text-secondary fw-normal">(اختياري)</span></label><input class="form-control @error('expires_on', 'credential') is-invalid @enderror" id="credential-expires-on" name="expires_on" type="date" value="{{ old('expires_on') }}">@error('expires_on', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="md:col-span-2 min-w-0"><label class="field-label form-label" for="credential-document" data-i18n="ملف الإثبات">ملف الإثبات</label><label class="profile-upload relative h-24 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center gap-2 text-sm text-slate-500 cursor-pointer hover:border-[#0495F6] hover:bg-blue-50/30"><span class="material-symbols-outlined">upload_file</span><span class="profile-upload-name">اختيار PDF / JPG / PNG</span><input accept=".pdf,.jpg,.jpeg,.png" class="profile-upload-input @error('document', 'credential') is-invalid @enderror" data-profile-upload id="credential-document" name="document" type="file" required></label><div class="form-text">PDF أو JPG أو PNG، بحد أقصى 5 ميجابايت. الملف خاص ولا يظهر للمرضى.</div>@error('document', 'credential')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div></div>
                    <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button" data-i18n="إلغاء">إلغاء</button><button class="btn btn-primary" type="submit" data-i18n="حفظ وإرسال للمراجعة">حفظ وإرسال للمراجعة</button></div>
                </form>
            </div>
        </div>
    </div>
</section>
