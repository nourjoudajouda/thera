@php($patientMode=old('patient_mode',isset($selectedPatient)&&$selectedPatient ? 'existing' : 'new'))
<div data-patient-picker-scope>
    <label class="form-label" for="patient-mode">ملف المريض</label><select class="form-select mb-3" name="patient_mode" id="patient-mode" data-patient-mode data-appointment-select><option value="new" @selected($patientMode==='new')>إنشاء ملف محلي جديد</option><option value="existing" @selected($patientMode==='existing')>اختيار مريض من ملفاتي</option><option value="thera" @selected($patientMode==='thera')>مرضى ثيرا</option><option value="new_thera" @selected($patientMode==='new_thera')>إنشاء مريض على شبكة ثيرا</option></select>
    <fieldset data-patient-fields="existing" @disabled($patientMode!=='existing') @if($patientMode!=='existing') hidden @endif>
        <label class="form-label" for="patient-file-picker">المريض ورقم ملفه</label><select class="form-select" name="doctor_patient_id" id="patient-file-picker" data-patient-picker-url="{{ route('doctor.appointments.contacts') }}" required><option value="">ابحث بالاسم أو الهاتف أو رقم الملف</option>@if(isset($selectedPatient)&&$selectedPatient)<option selected value="{{ $selectedPatient->id }}">{{ $selectedPatient->reference() }} · {{ $selectedPatient->name }} — {{ $selectedPatient->phone }}</option>@endif</select><p class="form-text">ابحث عن المريض بالاسم أو الهاتف أو رقم الملف.</p>
    </fieldset>
    <fieldset data-patient-fields="thera" @disabled($patientMode!=='thera') @if($patientMode!=='thera') hidden @endif>
        <label class="form-label" for="thera-patient-picker">المريض المسجّل في ثيرا</label>
        <select class="form-select" name="thera_patient_id" id="thera-patient-picker" data-thera-patient-picker-url="{{ route('doctor.appointments.thera-patients') }}" required><option value="">اكتب رقم الهاتف كاملًا أو رقم ملف ثيرا</option></select>
        <p class="form-text">ابحث برقم الهاتف كاملًا مع رمز الدولة، أو رقم الملف الذي يبدأ بـ TH-. الاختيار للحجز فقط، ولا يمنح وصولًا إلى السجل الطبي لدى أطباء آخرين.</p>
    </fieldset>
    <fieldset data-patient-fields="new" @disabled($patientMode!=='new') @if($patientMode!=='new') hidden @endif>
        <div class="row g-3 mt-1"><div class="col-12 col-sm-6"><label class="form-label" for="new-patient-name">اسم المريض</label><input class="form-control" name="patient_name" id="new-patient-name" value="{{ old('patient_name') }}" maxlength="150" required autocomplete="name"></div><div class="col-12 col-sm-6"><label class="form-label" for="new-patient-phone" data-i18n="رقم التواصل">رقم التواصل</label><input class="form-control font-num" type="tel" dir="ltr" name="patient_phone" id="new-patient-phone" value="{{ old('patient_phone') }}" pattern="\+?[0-9]{8,15}" maxlength="16" required autocomplete="tel" placeholder="+962…"></div></div><p class="form-text mt-3 mb-0">ينشأ ملف مستقل داخل حساب هذا الطبيب. لا يُنشأ حساب للمريض ولا يُدمج تلقائيًا مع اسم أو هاتف مشابه.</p>
    </fieldset>
    <fieldset data-patient-fields="new_thera" @disabled($patientMode!=='new_thera') @if($patientMode!=='new_thera') hidden @endif>
        <div class="row g-3 mt-1">
            <div class="col-12 col-sm-6"><label class="form-label" for="network-patient-name">اسم المريض</label><input class="form-control" name="patient_name" id="network-patient-name" value="{{ old('patient_name') }}" maxlength="150" required autocomplete="name"></div>
            <div class="col-12 col-sm-6"><label class="form-label" for="network-patient-phone" data-i18n="رقم التواصل">رقم التواصل</label><input class="form-control font-num" name="patient_phone" id="network-patient-phone" type="tel" dir="ltr" value="{{ old('patient_phone') }}" pattern="\+?[0-9]{8,15}" maxlength="16" required autocomplete="tel" placeholder="+962…"></div>
            <div class="col-12"><label class="form-label" for="network-patient-email">البريد الإلكتروني للمريض</label><input class="form-control" name="patient_email" id="network-patient-email" type="email" dir="ltr" value="{{ old('patient_email') }}" maxlength="255" required autocomplete="email"><p class="form-text">سيكون البريد اسم المستخدم، وتُرسل إليه بيانات الدخول بعد حفظ الموعد بنجاح. تأكد من أنه يخص المريض.</p></div>
        </div>
        <p class="form-text mt-3 mb-0">ينشأ حساب مستقل على شبكة ثيرا، مع ملف خاص داخل حساب هذا الطبيب. لا يظهر للطبيب باسورد المريض، ولا يتم توثيق الهاتف أو مشاركة السجل الطبي تلقائيًا.</p>
    </fieldset>
</div>
