<section aria-labelledby="profile-tab-facilities" class="profile-panel" data-panel="facilities" id="profile-panel-facilities" role="tabpanel">
    <div class="profile-card p-5 md:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="text-lg font-extrabold text-[#0B1020]" data-i18n="المنشآت">المنشآت</h3>
                <p class="text-sm text-slate-500 mt-1 mb-0">اطلب الانضمام إلى منشأة معتمدة أو أرسل طلب إضافة منشأة غير موجودة.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button class="btn btn-outline-primary inline-flex items-center justify-center gap-2" data-bs-target="#propose-facility-modal" data-bs-toggle="modal" type="button">
                    <span class="material-symbols-outlined text-[18px]">add_business</span>طلب إضافة منشأة
                </button>
                <button class="btn btn-primary inline-flex items-center justify-center gap-2" data-bs-target="#join-facility-modal" data-bs-toggle="modal" type="button" @disabled($availableFacilities->isEmpty())>
                    <span class="material-symbols-outlined text-[18px]">domain_add</span>طلب الانضمام
                </button>
            </div>
        </div>

        @if ($facilityMemberships->isEmpty())
            <div class="onboarding-empty">
                <span class="material-symbols-outlined">apartment</span>
                <p class="font-extrabold text-slate-800">لا توجد منشآت مرتبطة بحسابك</p>
                <p class="text-xs text-slate-500 mt-1.5 leading-6">ابحث عن منشأة معتمدة وأرسل طلب انضمام؛ ستصبح العضوية نشطة بعد اعتمادها.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($facilityMemberships as $membership)
                    @php
                        $facilityType = match ($membership->facility->type) {
                            'hospital' => 'مستشفى',
                            'medical_center' => 'مركز طبي',
                            'clinic' => 'عيادة',
                            default => $membership->facility->type,
                        };
                        $membershipStatus = match ($membership->membership_status) {
                            'active' => ['نشطة', 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'check_circle'],
                            'pending' => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200', 'pending'],
                            'rejected' => ['مرفوضة', 'bg-red-50 text-red-700 border border-red-200', 'cancel'],
                            default => ['موقوفة', 'bg-red-50 text-red-700 border border-red-200', 'block'],
                        };
                    @endphp
                    <div class="min-w-0">
                        <article class="facility-card p-4 h-full">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-[#EFF6FF] text-[#0466F1] flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined">{{ $membership->facility->type === 'hospital' ? 'local_hospital' : 'apartment' }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-800 mb-1">{{ $membership->facility->name }}</p>
                                        <p class="text-xs text-slate-500 mb-0">{{ $facilityType }} • {{ $membership->facility->city }}</p>
                                    </div>
                                </div>
                                <span class="status-pill {{ $membershipStatus[1] }} h-7 shrink-0"><span class="material-symbols-outlined text-[15px]">{{ $membershipStatus[2] }}</span>{{ $membershipStatus[0] }}</span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                <div class="min-w-0"><div class="rounded-xl bg-slate-50 p-3 h-full"><span class="text-slate-400">الصفة</span><p class="font-bold text-slate-700 mt-1 mb-0">{{ $membership->role }}</p></div></div>
                                <div class="min-w-0"><div class="rounded-xl bg-slate-50 p-3 h-full"><span class="text-slate-400">حجوزات THERA</span><p class="font-bold {{ $membership->facility->bookings_enabled ? 'text-emerald-700' : 'text-slate-500' }} mt-1 mb-0">{{ $membership->facility->bookings_enabled ? 'مفعّلة' : 'غير مفعّلة' }}</p></div></div>
                            </div>

                            @if ($membership->membership_status === 'rejected' && $membership->rejection_reason)
                                <p class="small text-danger mt-3 mb-0">{{ $membership->rejection_reason }}</p>
                            @endif

                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                @if ($membership->membership_status === 'active')
                                    <form action="{{ route('doctor.account.professional-profile.facilities.update', $membership) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input name="is_public" type="hidden" value="0">
                                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600">
                                            <span>إظهار في الملف العام</span>
                                            <span class="toggle">
                                                <input name="is_public" onchange="this.form.submit()" type="checkbox" value="1" @checked($membership->is_public)>
                                                <span class="toggle-track"></span>
                                            </span>
                                        </label>
                                    </form>
                                @else
                                    <p class="text-xs text-slate-500 mb-0">بانتظار اعتماد إدارة المنشأة أو THERA.</p>
                                @endif

                                <div class="flex items-center gap-3">
                                    <button class="text-xs font-bold text-[#0495F6] hover:text-[#0466F1]" data-bs-target="#facility-details-{{ $membership->id }}" data-bs-toggle="modal" type="button">عرض التفاصيل</button>
                                    @if ($membership->membership_status === 'pending')
                                        <form action="{{ route('doctor.account.professional-profile.facilities.destroy', $membership) }}" method="POST" data-thera-confirm="هل تريد إلغاء طلب الانضمام؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" type="submit">إلغاء الطلب</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div aria-hidden="true" aria-labelledby="facility-details-title-{{ $membership->id }}" class="modal fade profile-modal" id="facility-details-{{ $membership->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content">
                                <div class="modal-header"><h4 class="modal-title" id="facility-details-title-{{ $membership->id }}">{{ $membership->facility->name }}</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
                                <div class="modal-body">
                                    <p class="text-sm text-slate-500 mb-3">{{ $facilityType }} • {{ $membership->facility->city }}</p>
                                    <div class="flex justify-between gap-3 py-2"><span class="text-secondary">حالة العضوية</span><strong>{{ $membershipStatus[0] }}</strong></div>
                                    <div class="flex justify-between gap-3 py-2 border-t border-slate-100"><span class="text-secondary">الصفة داخل المنشأة</span><strong>{{ $membership->role }}</strong></div>
                                    <div class="flex justify-between gap-3 py-2 border-t border-slate-100"><span class="text-secondary">الظهور العام</span><strong>{{ $membership->membership_status === 'active' && $membership->is_public ? 'ظاهر' : 'غير ظاهر' }}</strong></div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">إغلاق</button></div>
                                </div></div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($facilityProposals->isNotEmpty())
            <div class="mt-5 pt-4 border-t border-slate-100">
                <div class="mb-3">
                    <h4 class="font-extrabold text-slate-800 mb-1">طلبات إضافة منشآت جديدة</h4>
                    <p class="text-xs text-slate-500 mb-0">هذه المنشآت غير معتمدة بعد ولن تظهر في ملفك العام قبل مراجعتها.</p>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach ($facilityProposals as $proposal)
                        @php
                            $proposalType = match ($proposal->type) { 'hospital' => 'مستشفى', 'medical_center' => 'مركز طبي', 'clinic' => 'عيادة', default => $proposal->type };
                            $proposalStatus = match ($proposal->status) { 'approved' => ['تم الاعتماد', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200'] };
                        @endphp
                        <article class="facility-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-extrabold text-slate-800 mb-0">{{ $proposal->name }}</p>
                                    <span class="status-pill {{ $proposalStatus[1] }} h-6">{{ $proposalStatus[0] }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-2 mb-0">{{ $proposalType }} • {{ $proposal->city }} • صفتك: {{ $proposal->role }}</p>
                                @if ($proposal->status === 'rejected' && $proposal->rejection_reason)<p class="text-xs text-danger mt-2 mb-0">{{ $proposal->rejection_reason }}</p>@endif
                            </div>
                            @if ($proposal->status === 'pending')
                                <form action="{{ route('doctor.account.professional-profile.facility-proposals.destroy', $proposal) }}" method="POST" data-thera-confirm="هل تريد إلغاء طلب إضافة المنشأة؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">إلغاء الطلب</button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500 flex items-start gap-2">
            <span class="material-symbols-outlined text-[19px] text-slate-400">info</span>
            <span>لا يستطيع الطبيب تفعيل العضوية بنفسه. بعد قبول الطلب ستصبح العضوية نشطة ويمكنك التحكم بظهورها في الملف العام.</span>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="join-facility-title" class="modal fade profile-modal" id="join-facility-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form action="{{ route('doctor.account.professional-profile.facilities.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title fs-6 fw-bold" id="join-facility-title">طلب الانضمام إلى منشأة</h4>
                        <button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="field-label form-label" for="join-facility-id" data-i18n="المنشأة">المنشأة</label>
                                <select class="form-select @error('facility_id', 'facilityMembership') is-invalid @enderror" id="join-facility-id" name="facility_id" required>
                                    <option value="">ابحث عن المنشأة</option>
                                    @foreach ($availableFacilities as $facility)
                                        <option value="{{ $facility->id }}" @selected((int) old('facility_id') === $facility->id)>{{ $facility->name }} — {{ $facility->city }}</option>
                                    @endforeach
                                </select>
                                @error('facility_id', 'facilityMembership')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="field-label form-label" for="join-facility-role">صفتك داخل المنشأة</label>
                                <input class="form-control @error('role', 'facilityMembership') is-invalid @enderror" id="join-facility-role" maxlength="100" name="role" placeholder="مثال: طبيب، استشاري، أخصائي" type="text" value="{{ old('role') }}" required>
                                @error('role', 'facilityMembership')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs text-blue-700 leading-6">سيصل الطلب إلى إدارة المنشأة أو THERA للمراجعة، ولن تظهر المنشأة في ملفك العام قبل الاعتماد.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" type="button" data-i18n="إلغاء">إلغاء</button>
                        <button class="btn btn-primary" type="submit">إرسال طلب الانضمام</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="propose-facility-title" class="modal fade profile-modal" id="propose-facility-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form action="{{ route('doctor.account.professional-profile.facility-proposals.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title fs-6 fw-bold" id="propose-facility-title">طلب إضافة منشأة جديدة</h4>
                        <button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="min-w-0">
                                <label class="field-label form-label" for="proposal-facility-name">اسم المنشأة</label>
                                <input class="form-control @error('name', 'facilityProposal') is-invalid @enderror" id="proposal-facility-name" maxlength="255" name="name" type="text" value="{{ old('name') }}" required>
                                @error('name', 'facilityProposal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="min-w-0">
                                <label class="field-label form-label" for="proposal-facility-type">نوع المنشأة</label>
                                <select class="form-select @error('type', 'facilityProposal') is-invalid @enderror" id="proposal-facility-type" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="clinic" @selected(old('type') === 'clinic')>عيادة</option>
                                    <option value="medical_center" @selected(old('type') === 'medical_center')>مركز طبي</option>
                                    <option value="hospital" @selected(old('type') === 'hospital')>مستشفى</option>
                                </select>
                                @error('type', 'facilityProposal')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="min-w-0">
                                <label class="field-label form-label" for="proposal-facility-city">المدينة</label>
                                <input class="form-control @error('city', 'facilityProposal') is-invalid @enderror" id="proposal-facility-city" maxlength="100" name="city" type="text" value="{{ old('city') }}" required>
                                @error('city', 'facilityProposal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="min-w-0">
                                <label class="field-label form-label" for="proposal-facility-role">صفتك داخل المنشأة</label>
                                <input class="form-control @error('role', 'facilityProposal') is-invalid @enderror" id="proposal-facility-role" maxlength="100" name="role" placeholder="مثال: طبيب، استشاري، أخصائي" type="text" value="{{ old('role') }}" required>
                                @error('role', 'facilityProposal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="md:col-span-2 min-w-0"><div class="rounded-xl border border-amber-100 bg-amber-50 p-3 text-xs text-amber-800 leading-6">إرسال الطلب لا ينشئ منشأة نشطة مباشرة. ستتحقق الإدارة من بيانات المنشأة أولًا ثم تربط عضويتك بها بعد الاعتماد.</div></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" type="button" data-i18n="إلغاء">إلغاء</button>
                        <button class="btn btn-primary" type="submit">إرسال طلب الإضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
