(() => {
    'use strict';
    const button = document.getElementById('prescription-print-button');
    const choice = document.querySelector('#print-stamp-choice');
    const image = document.querySelector('#print-stamp-image');
    const noStamp = document.querySelector('#print-no-stamp');
    const renderStamp = source => {
        if (!image || !noStamp) return;
        image.hidden = !source;
        noStamp.hidden = !!source;
        noStamp.textContent = source ? '' : 'هذه النسخة بدون ختم.';
        if (source) { image.classList.add('document-stamp'); image.alt = 'ختم الطبيب المختار لنسخة الطباعة'; image.src = source; }
        else image.removeAttribute('src');
    };
    const updatePreview = () => renderStamp(choice.selectedOptions[0]?.dataset.src || null);
    if (choice && window.jQuery?.fn?.select2) {
        window.jQuery(choice).select2({dir:'rtl',width:'100%',minimumResultsForSearch:0}).on('change', updatePreview);
    } else {
        choice?.addEventListener?.('change', updatePreview);
    }
    button?.addEventListener('click', async () => {
        if (button.disabled) return;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        const hasChoice = !!choice?.selectedOptions;
        if (hasChoice) choice.disabled = true;
        try {
            const response = await fetch(button.dataset.url, {
                method: 'POST', credentials: 'same-origin', cache: 'no-store',
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, ...(hasChoice ? {'Content-Type':'application/json'} : {})},
                ...(hasChoice ? {body:JSON.stringify({stamp_choice:choice.value})} : {}),
            });
            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) throw new Error('تعذر تسجيل طلب الطباعة. تحقق من الجلسة والاتصال وحاول مجددًا.');
            if (hasChoice) {
                const result = await response.json();
                if (!Object.prototype.hasOwnProperty.call(result, 'stamp_data_uri')) throw new Error('تعذر التحقق من الختم المختار.');
                renderStamp(result.stamp_data_uri);
            }
            await document.fonts?.ready;
            await Promise.all(Array.from(document.images).filter(image => !image.hidden).map(async image => {
                if (image.decode) await image.decode();
                if (!image.complete || image.naturalWidth === 0) throw new Error('لم يكتمل تحميل صورة الختم أو الشعار. أعد فتح المعاينة قبل الطباعة.');
            }));
            window.print();
        } catch (error) {
            window.TheraAlerts.error(error.message || 'تعذر فتح الطباعة.');
        } finally {
            button.disabled = false;
            if (hasChoice) choice.disabled = false;
            button.removeAttribute('aria-busy');
        }
    });
})();
