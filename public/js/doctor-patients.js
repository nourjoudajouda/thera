(() => {
    'use strict';
    window.initDoctorPatients = () => {
        document.querySelectorAll('[data-patient-picker-scope]').forEach(scope => {
            if (scope.dataset.patientBound) return;
            scope.dataset.patientBound = 'true';
            const mode = scope.querySelector('[data-patient-mode]');
            const picker = scope.querySelector('[data-patient-picker-url]');
            const update = () => scope.querySelectorAll('[data-patient-fields]').forEach(fields => {
                const inactive = fields.dataset.patientFields !== mode.value;
                fields.hidden = inactive;
                fields.disabled = inactive;
            });
            mode.addEventListener('change', update);
            if (window.jQuery?.fn.select2) {
                window.jQuery(mode).on('change.doctorPatients', update);
                window.jQuery(picker).select2({dir:'rtl',width:'100%',minimumInputLength:1,placeholder:'ابحث بالاسم أو الهاتف أو رقم الملف',language:{inputTooShort:()=>'اكتب اسمًا أو رقمًا للبحث',noResults:()=>'لا يوجد ملف مطابق. تأكد من البيانات أو اختر إنشاء ملف جديد.',searching:()=>'جارٍ البحث…'},ajax:{url:picker.dataset.patientPickerUrl,dataType:'json',delay:300,cache:false,data:params=>({q:params.term})}});
                const theraPicker = scope.querySelector('[data-thera-patient-picker-url]');
                if (theraPicker) window.jQuery(theraPicker).select2({dir:'rtl',width:'100%',minimumInputLength:8,placeholder:'رقم الهاتف كاملًا أو رقم ملف ثيرا',language:{inputTooShort:()=>'اكتب رقم الهاتف كاملًا مع رمز الدولة أو رقم ملف ثيرا',noResults:()=>'لا يوجد مريض مطابق متاح للحجز. تحقق من الرقم.',searching:()=>'جارٍ البحث في مرضى ثيرا…',errorLoading:()=>'تعذر البحث. حاول مجددًا بعد قليل.'},ajax:{url:theraPicker.dataset.theraPatientPickerUrl,dataType:'json',delay:400,cache:false,data:params=>({q:params.term})}});
            }
            update();
        });
    };
    window.initDoctorPatients();
})();
