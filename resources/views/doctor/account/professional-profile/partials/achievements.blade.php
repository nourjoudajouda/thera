<section aria-labelledby="profile-tab-achievements" class="profile-panel" data-panel="achievements" id="profile-panel-achievements" role="tabpanel">
    <div class="profile-card p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
            <div><h3 class="text-lg font-extrabold text-[#0B1020]">الإنجازات المهنية</h3><p class="text-sm text-slate-500 mt-1">الجوائز والاعتمادات والإنجازات المهنية القابلة للتحقق.</p></div>
            <button class="btn btn-primary inline-flex items-center justify-center gap-2" data-bs-target="#add-achievement-modal" data-bs-toggle="modal" type="button"><span class="material-symbols-outlined text-[18px]">add</span>إضافة إنجاز</button>
        </div>

        @if ($achievements->isEmpty())
            <div class="onboarding-empty"><span class="material-symbols-outlined">military_tech</span><p class="font-extrabold text-slate-800">لا توجد إنجازات مضافة</p><p class="text-xs text-slate-500 mt-1.5 leading-6">أضف إنجازًا مهنيًا وأرفق إثباته لإرساله للمراجعة.</p></div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="achievement-list">
                @foreach ($achievements as $achievement)
                    @php
                        $achievementType = match ($achievement->type) { 'award' => 'جائزة', 'accreditation' => 'اعتماد', 'honor' => 'تكريم', 'professional' => 'إنجاز مهني', default => $achievement->type };
                        $status = match ($achievement->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد التحقق', 'bg-amber-50 text-amber-700 border border-amber-200'] };
                    @endphp
                    <div class="min-w-0">
                        <article class="achievement-card p-4 h-full flex flex-col" data-profile-achievement="{{ $achievement->id }}">
                            <div class="flex items-start justify-between gap-3"><div><span class="text-[11px] font-bold {{ $achievement->type === 'award' ? 'text-amber-700 bg-amber-50' : 'text-purple-600 bg-purple-50' }} px-2 py-1 rounded-lg">{{ $achievementType }}</span><p class="font-extrabold text-slate-800 mt-3 mb-1">{{ $achievement->title }}</p><p class="text-xs text-slate-500 mt-1.5">{{ $achievement->organization }} • {{ $achievement->year }}</p></div><span class="status-pill {{ $status[1] }} h-7 shrink-0">{{ $status[0] }}</span></div>
                            <p class="text-sm text-slate-600 leading-7 mt-3 mb-0">{{ $achievement->description }}</p>
                            @if ($achievement->verification_status === 'rejected' && $achievement->rejection_reason)<p class="small text-danger mt-2 mb-0">{{ $achievement->rejection_reason }}</p>@endif
                            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end"><button class="text-xs font-bold text-[#0495F6] hover:text-[#0466F1]" data-bs-toggle="modal" data-bs-target="#achievement-details-{{ $achievement->id }}" type="button">عرض التفاصيل</button></div>
                        </article>
                        <div aria-hidden="true" aria-labelledby="achievement-details-title-{{ $achievement->id }}" class="modal fade profile-modal" id="achievement-details-{{ $achievement->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content">
                                <div class="modal-header"><h4 class="modal-title" id="achievement-details-title-{{ $achievement->id }}">تفاصيل الإنجاز المهني</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
                                <div class="modal-body">
                                    <div class="rounded-2xl border border-slate-200 p-5">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold">{{ $achievementType }}</span>
                                            <span class="status-pill {{ $status[1] }} h-6">{{ $status[0] }}</span>
                                        </div>
                                        <h4 class="text-lg font-extrabold text-slate-900 mt-4">{{ $achievement->title }}</h4>
                                        <p class="text-sm text-slate-600 leading-7 mt-3">{{ $achievement->description }}</p>
                                    </div>
                                    <div class="mt-4 rounded-2xl border border-slate-200 px-4">
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">الجهة</span><strong class="text-sm text-slate-800">{{ $achievement->organization }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">السنة</span><strong class="text-sm text-slate-800">{{ $achievement->year }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">حالة الظهور العام</span><strong class="text-sm text-slate-800">{{ $achievement->verification_status === 'verified' && $doctor->verification_status === 'verified' ? 'ظاهر في الملف العام' : 'غير ظاهر حتى اكتمال التحقق' }}</strong></div>
                                        <div class="summary-row py-3 flex items-start justify-between gap-4"><span class="text-sm text-slate-500">الإثبات</span><strong class="text-sm text-slate-800">مرفق في ملف خاص</strong></div>
                                    </div>
                                    @if ($achievement->verification_status === 'rejected' && $achievement->rejection_reason)<p class="text-sm text-red-600 mt-3">{{ $achievement->rejection_reason }}</p>@endif
                                </div>
                                <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">إغلاق</button><div class="flex flex-wrap items-center gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('doctor.account.professional-profile.achievements.evidence', $achievement) }}"><span class="material-symbols-outlined fs-6 align-middle">download</span> ملف الإثبات</a>
                                @if ($achievement->verification_status !== 'verified')<form action="{{ route('doctor.account.professional-profile.achievements.destroy', $achievement) }}" method="POST" data-thera-confirm="هل تريد حذف هذا الإنجاز؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-i18n="حذف">حذف</button></form>@endif
                            </div></div>
                            </div></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div aria-hidden="true" aria-labelledby="add-achievement-title" class="modal fade profile-modal" id="add-achievement-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content"><form action="{{ route('doctor.account.professional-profile.achievements.store', ['tab' => 'achievements']) }}" enctype="multipart/form-data" method="POST">@csrf
            <div class="modal-header"><h4 class="modal-title fs-6 fw-bold" id="add-achievement-title">إضافة إنجاز مهني</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
            <div class="modal-body"><div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="min-w-0"><label class="field-label form-label" for="achievement-type" data-i18n="نوع الإنجاز">نوع الإنجاز</label><select class="form-select @error('type', 'achievement') is-invalid @enderror" id="achievement-type" name="type" required><option value="">اختر النوع</option><option value="award" @selected(old('type') === 'award')>جائزة</option><option value="accreditation" @selected(old('type') === 'accreditation')>اعتماد</option><option value="honor" @selected(old('type') === 'honor')>تكريم</option><option value="professional" @selected(old('type') === 'professional')>إنجاز مهني</option></select>@error('type', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="min-w-0"><label class="field-label form-label" for="achievement-title" data-i18n="عنوان الإنجاز">عنوان الإنجاز</label><input class="form-control @error('title', 'achievement') is-invalid @enderror" id="achievement-title" name="title" type="text" value="{{ old('title') }}" required>@error('title', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="min-w-0"><label class="field-label form-label" for="achievement-organization" data-i18n="الجهة">الجهة</label><input class="form-control @error('organization', 'achievement') is-invalid @enderror" id="achievement-organization" name="organization" type="text" value="{{ old('organization') }}" required>@error('organization', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="min-w-0"><label class="field-label form-label" for="achievement-year" data-i18n="السنة">السنة</label><input class="form-control font-num @error('year', 'achievement') is-invalid @enderror" id="achievement-year" max="{{ now()->year }}" min="1950" name="year" type="number" value="{{ old('year') }}" required>@error('year', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="md:col-span-2 min-w-0"><label class="field-label form-label" for="achievement-description" data-i18n="وصف مختصر">وصف مختصر</label><textarea class="form-control @error('description', 'achievement') is-invalid @enderror" id="achievement-description" maxlength="2000" name="description" required>{{ old('description') }}</textarea>@error('description', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="md:col-span-2 min-w-0"><label class="field-label form-label" for="achievement-evidence" data-i18n="ملف الإثبات">ملف الإثبات</label><label class="profile-upload relative h-24 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center gap-2 text-sm text-slate-500 cursor-pointer hover:border-[#0495F6] hover:bg-blue-50/30"><span class="material-symbols-outlined">upload_file</span><span class="profile-upload-name">اختيار PDF / JPG / PNG</span><input accept=".pdf,.jpg,.jpeg,.png" class="profile-upload-input @error('evidence', 'achievement') is-invalid @enderror" data-profile-upload id="achievement-evidence" name="evidence" type="file" required></label><div class="form-text">PDF أو JPG أو PNG، بحد أقصى 5 ميجابايت. الملف خاص ولا يظهر للمرضى.</div>@error('evidence', 'achievement')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div></div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button" data-i18n="إلغاء">إلغاء</button><button class="btn btn-primary" type="submit" data-i18n="حفظ وإرسال للمراجعة">حفظ وإرسال للمراجعة</button></div>
        </form></div></div>
    </div>
</section>
