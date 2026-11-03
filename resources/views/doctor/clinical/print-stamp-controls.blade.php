<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<section class="print-toolbar print-stamp-controls" aria-label="خيارات ختم الطباعة">
    <div><label for="print-stamp-choice" class="form-label fw-bold">الختم في نسخة الطباعة</label>
        <p class="small text-secondary mb-0">اختيارك لهذه النسخة فقط؛ لا يغيّر المستند المعتمد. صورة الختم ليست توقيعًا رقميًا.</p>
        <a class="small" href="{{ route('doctor.account.settings', ['tab' => 'stamps']) }}" target="_blank" rel="noopener">إضافة أختام من الإعدادات</a>
    </div>
    <select id="print-stamp-choice" class="form-select" aria-label="اختيار ختم الطباعة">
        <option value="none" @selected(!($snapshot['stamp_data_uri'] ?? null))>بدون ختم</option>
        @if($snapshot['stamp_data_uri'] ?? null)<option value="saved" selected data-src="{{ $snapshot['stamp_data_uri'] }}">مع ختم الإصدار المحفوظ</option>@endif
        @foreach($stamps as $stamp)<option value="{{ $stamp->id }}" data-src="{{ route('doctor.account.stamp.show', ['stamp' => $stamp->id]) }}">مع ختم — {{ $stamp->name }}</option>@endforeach
    </select>
</section>
