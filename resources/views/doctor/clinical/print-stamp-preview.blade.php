<div id="print-stamp-preview">
    <strong>ختم نسخة الطباعة</strong>
    @if($snapshot['stamp_data_uri'] ?? null)
        <img class="document-stamp" src="{{ $snapshot['stamp_data_uri'] }}" alt="ختم الطبيب المحفوظ لهذا الإصدار" id="print-stamp-image">
    @else
        <img alt="ختم الطبيب المختار للطباعة" id="print-stamp-image" hidden>
    @endif
    <p class="missing-stamp" id="print-no-stamp" @if($snapshot['stamp_data_uri'] ?? null) hidden @endif>{{ ($snapshot['stamp_data_uri'] ?? null) ? '' : 'هذه النسخة بدون ختم.' }}</p>
    <small>صورة الختم ليست توقيعًا رقميًا معتمدًا.</small>
</div>
