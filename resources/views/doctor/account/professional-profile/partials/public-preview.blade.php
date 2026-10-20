@php
    $doctorNameParts = collect(preg_split('/\s+/u', trim($doctor->name)) ?: [])->filter()->values();
    $doctorInitials = $doctorNameParts
        ->take(2)
        ->map(fn (string $part): string => mb_substr($part, 0, 1))
        ->implode(' ');
    $publicBio = $doctor->bio ?: $doctor->professional_bio;
@endphp

<section aria-labelledby="profile-tab-preview" class="profile-panel" data-panel="preview" id="profile-panel-preview" role="tabpanel">
    @if ($doctor->verification_status !== 'verified')
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4" role="alert">
            <span class="material-symbols-outlined mt-1">visibility</span>
            <div>
                <strong>هذه معاينة لملفك كما سيظهر للمرضى.</strong>
                <div class="small mt-1">لن يُنشر الملف للعامة قبل اكتمال التحقق المهني، ولن تظهر العناصر غير الموثقة في هذه المعاينة.</div>
            </div>
        </div>
    @endif

    <div class="profile-card overflow-hidden public-preview-shell soft-grid">
        <div class="p-5 p-md-4 p-lg-5 border-bottom bg-white bg-opacity-75">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-4">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-[#E6F4FE] to-[#EEF2FF] border border-[#D8E8F6] flex items-center justify-center text-[#0466F1] text-3xl font-extrabold shrink-0">
                    {{ $doctorInitials ?: 'ط' }}
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h3 class="text-2xl font-extrabold text-[#0B1020] mb-0">{{ $doctor->name }}</h3>
                        @if ($doctor->verification_status === 'verified')
                            <span class="status-pill bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="material-symbols-outlined text-[16px]">verified</span>موثّق</span>
                        @else
                            <span class="status-pill bg-amber-50 text-amber-700 border border-amber-200"><span class="material-symbols-outlined text-[16px]">pending</span>بانتظار التحقق</span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-600 mt-2 mb-0">{{ $doctor->professional_title ?: 'لم يُحدد المسمى المهني بعد' }}</p>
                    <div class="d-flex flex-wrap gap-3 gap-md-4 mt-3 text-sm text-slate-500">
                        @if (! is_null($doctor->years_experience))
                            <span class="d-inline-flex align-items-center gap-1"><span class="material-symbols-outlined text-[17px]">history</span>{{ $doctor->years_experience }} {{ $doctor->years_experience === 1 ? 'سنة خبرة' : 'سنوات خبرة' }}</span>
                        @endif
                        @if (filled($doctor->languages))
                            <span class="d-inline-flex align-items-center gap-1"><span class="material-symbols-outlined text-[17px]">language</span>{{ implode('، ', $doctor->languages) }}</span>
                        @endif
                        @if ($primarySpecialtyAssignment)
                            <span class="d-inline-flex align-items-center gap-1"><span class="material-symbols-outlined text-[17px]">medical_information</span>{{ $primarySpecialtyAssignment->specialty->name_ar }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 p-md-5 bg-white bg-opacity-75">
            <div class="row g-4">
                <div class="col-12 col-xl-8">
                <div class="d-flex flex-column gap-4">
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 p-md-5">
                        <h4 class="font-extrabold text-slate-800 mb-2">نبذة عن الطبيب</h4>
                        @if ($publicBio)
                            <p class="text-sm leading-7 text-slate-600 mb-0">{{ $publicBio }}</p>
                        @else
                            <p class="text-sm text-slate-400 mb-0">لم تُضف نبذة عامة بعد.</p>
                        @endif
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-4 p-md-5">
                        <h4 class="font-extrabold text-slate-800 mb-3">التخصصات</h4>
                        @if ($primarySpecialtyAssignment)
                            <div class="d-flex flex-wrap gap-2">
                                <span class="status-pill bg-blue-50 text-blue-700 border border-blue-200">{{ $primarySpecialtyAssignment->specialty->name_ar }}</span>
                                @foreach ($verifiedSubspecialtyAssignments as $assignment)
                                    <span class="status-pill bg-slate-50 text-slate-700 border border-slate-200">{{ $assignment->specialty->name_ar }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 mb-0">لم يُحدد التخصص الرئيسي بعد.</p>
                        @endif
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-4 p-md-5">
                        <h4 class="font-extrabold text-slate-800 mb-3">المؤهلات المهنية</h4>
                        @forelse ($verifiedCredentials as $credential)
                            <div class="d-flex align-items-start gap-3 {{ ! $loop->first ? 'pt-3 mt-3 border-top' : '' }}">
                                <span class="material-symbols-outlined text-emerald-500">verified</span>
                                <div>
                                    <p class="font-bold text-sm text-slate-800 mb-0">{{ $credential->title }}</p>
                                    <p class="text-xs text-slate-500 mt-1 mb-0">{{ $credential->issuer }}@if ($credential->issued_on) • {{ $credential->issued_on->format('Y') }}@endif</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 mb-0">لا توجد مؤهلات موثقة ظاهرة للعامة بعد.</p>
                        @endforelse
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-4 p-md-5">
                        <h4 class="font-extrabold text-slate-800 mb-3">الإنجازات</h4>
                        @forelse ($verifiedAchievements as $achievement)
                            <div class="d-flex align-items-start gap-3 {{ ! $loop->first ? 'pt-3 mt-3 border-top' : '' }}">
                                <span class="material-symbols-outlined text-purple-500">workspace_premium</span>
                                <div>
                                    <p class="font-bold text-sm text-slate-800 mb-0">{{ $achievement->title }}</p>
                                    <p class="text-xs text-slate-500 mt-1 mb-0">{{ $achievement->organization }}@if ($achievement->year) • {{ $achievement->year }}@endif</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 mb-0">لا توجد إنجازات موثقة ظاهرة للعامة بعد.</p>
                        @endforelse
                    </article>
                </div>
                </div>

                <aside class="col-12 col-xl-4">
                <div class="d-flex flex-column gap-4">
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 p-md-5">
                        <h4 class="font-extrabold text-slate-800 mb-3">المنشآت</h4>
                        @forelse ($publicFacilityMemberships as $membership)
                            @php
                                $facilityType = match ($membership->facility->type) {
                                    'hospital' => 'مستشفى',
                                    'medical_center' => 'مركز طبي',
                                    'clinic' => 'عيادة',
                                    default => $membership->facility->type,
                                };
                            @endphp
                            <div class="{{ ! $loop->first ? 'pt-3 mt-3 border-top' : '' }}">
                                <p class="font-bold text-sm text-slate-800 mb-0">{{ $membership->facility->name }}</p>
                                <p class="text-xs text-slate-500 mt-1 mb-0">{{ $facilityType }} • {{ $membership->facility->city }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 mb-0">لا توجد منشآت مفعّلة للظهور العام.</p>
                        @endforelse
                    </article>

                    <article class="rounded-2xl border border-blue-100 bg-blue-50 p-4 p-md-5">
                        <div class="d-flex align-items-start gap-3">
                            <span class="material-symbols-outlined text-[#0495F6]">medical_services</span>
                            <div>
                                <p class="font-extrabold text-slate-800 mb-1">الخدمات والمواعيد</p>
                                <p class="text-xs text-slate-600 leading-6 mb-3">تُدار الخدمات والأسعار ومواعيد الحجز من القسم المخصص لها.</p>
                                <a class="btn btn-outline-primary btn-sm fw-bold" href="{{ route('doctor.practice.services') }}">إدارة الخدمات</a>
                            </div>
                        </div>
                    </article>
                </div>
                </aside>
            </div>
        </div>
    </div>
</section>
