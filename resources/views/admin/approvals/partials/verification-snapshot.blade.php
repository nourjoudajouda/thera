@if ($item->profile_snapshot)
    <div class="border rounded-4 p-3 mt-3 bg-light">
        <p class="fw-bold small mb-2">البيانات المسجلة وقت إرسال الطلب</p>
        <div class="row g-2 small">
            <div class="col-12 col-md-6">الاسم: {{ $item->profile_snapshot['name'] }}</div>
            <div class="col-12 col-md-6">المسمى: {{ $item->profile_snapshot['professional_title'] }}</div>
            <div class="col-12 col-md-6">البريد: {{ $item->profile_snapshot['email'] }}</div>
            <div class="col-12 col-md-6">الهاتف: {{ $item->profile_snapshot['phone'] }}</div>
            <div class="col-12 col-md-6">التخصص الرئيسي: {{ $item->profile_snapshot['primary_specialty_name'] }}</div>
            <div class="col-12 col-md-6">سنوات الخبرة: {{ $item->profile_snapshot['years_experience'] }}</div>
            <div class="col-12 text-secondary">{{ $item->profile_snapshot['professional_bio'] }}</div>
        </div>
    </div>
@else
    <p class="small text-warning-emphasis mt-3 mb-0">هذا الطلب أُرسل قبل إضافة حفظ نسخة البيانات. راجع ملف الطبيب والمستندات قبل الاعتماد.</p>
@endif
