<section id="section-stamps" class="settings-section" @if($tab !== 'stamps') hidden @endif>
    <h2>الأختام والطباعة</h2>
    <p class="text-secondary small">احفظ أختامك هنا، ثم اختر بدون ختم أو أحد الأختام المحفوظة عند معاينة أي مستند للطباعة.</p>
    <div class="settings-row-group p-3 mb-4">
        <h3 class="h6 fw-bold">إضافة ختم</h3>
        <div class="row g-3">
            <div class="col-12 col-md-6"><label class="form-label" for="stamp-name">اسم الختم</label><input class="form-control" form="doctor-stamp-upload" id="stamp-name" name="name" maxlength="80" required placeholder="مثل: ختم العيادة"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="stamp-file">صورة الختم</label><input class="form-control" form="doctor-stamp-upload" id="stamp-file" name="stamp" type="file" accept="image/png,image/jpeg" required aria-describedby="stamp-upload-help"></div>
        </div>
        <p id="stamp-upload-help" class="small text-secondary mt-3">PNG أو JPG، حتى 1 ميجابايت، بأبعاد 40–2000 بكسل. يفضّل PNG بخلفية شفافة. الأختام خاصة بحسابك وليست توقيعًا رقميًا معتمدًا.</p>
        <button class="btn btn-primary" form="doctor-stamp-upload" type="submit"><span class="material-symbols-outlined" aria-hidden="true">add</span>إضافة الختم</button>
    </div>
    <h3 class="h6 fw-bold">الأختام المحفوظة</h3>
    <div class="row g-3">
        @forelse($stamps as $stamp)
            <div class="col-12 col-sm-6"><article class="settings-row-group p-3 h-100"><img class="d-block w-100 rounded-3 bg-white mb-3" src="{{ route('doctor.account.stamp.show', ['stamp' => $stamp->id]) }}" alt="{{ $stamp->name }}" style="height:120px;object-fit:contain"><strong class="text-break">{{ $stamp->name }}</strong></article></div>
        @empty
            <div class="col-12"><p class="small text-secondary">لا توجد أختام مضافة بعد. يمكنك الطباعة بدون ختم.</p></div>
        @endforelse
    </div>
    @if($doctor->stamp_path && !$stamps->contains('path', $doctor->stamp_path))
        <div class="settings-row-group p-3 mt-3"><strong>الختم السابق</strong><img class="d-block bg-white rounded-3 mt-2" src="{{ route('doctor.account.stamp.show') }}" alt="الختم السابق للطبيب" style="max-width:100%;width:180px;height:110px;object-fit:contain"><p class="small text-secondary mb-0">يبقى محفوظًا في الإصدارات السابقة. أعد إضافته باسم ليظهر في قائمة أختام الطباعة.</p></div>
    @endif
</section>
