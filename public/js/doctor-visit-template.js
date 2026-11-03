(() => {
    'use strict';
    window.initDoctorVisitTemplate = () => {
        const form = document.getElementById('visit-documentation-form');
        if (!form || form.dataset.templateBound) return;
        form.dataset.templateBound = '1';
        const diagnosis = document.getElementById('visit-diagnosis');
        const input = document.getElementById('template-diagnosis-input');
        const list = document.getElementById('template-diagnosis-list');
        const readOnly = !!form.querySelector('fieldset')?.disabled;
        const changed = () => diagnosis.dispatchEvent(new Event('input', {bubbles:true}));
        const renderDiagnosis = () => {
            list.replaceChildren();
            const lines = diagnosis.value.split('\n').map(line => line.trim()).filter(Boolean);
            if (!lines.length) { list.textContent = 'لم تتم إضافة تشخيص لهذه الزيارة بعد'; return; }
            lines.forEach((line, index) => {
                const row = document.createElement('div');
                const text = document.createElement('span'); text.textContent = line; row.append(text);
                if (!readOnly) {
                    const remove = document.createElement('button'); remove.type = 'button'; remove.setAttribute('aria-label', 'حذف التشخيص: ' + line);
                    const icon = document.createElement('span'); icon.className = 'material-symbols-outlined'; icon.setAttribute('aria-hidden','true'); icon.textContent = 'close'; remove.append(icon);
                    remove.addEventListener('click', () => { lines.splice(index,1); diagnosis.value = lines.join('\n'); changed(); renderDiagnosis(); }); row.append(remove);
                }
                list.append(row);
            });
        };
        const addDiagnosis = () => {
            const value = input?.value.trim();
            if (!value) return true;
            const combined = [diagnosis.value.trim(), value].filter(Boolean).join('\n');
            if (combined.length > 10000) { window.TheraAlerts.info('نص التشخيص أطول من الحد المسموح.'); return false; }
            diagnosis.value = combined; input.value = ''; changed(); renderDiagnosis(); return true;
        };
        document.getElementById('template-diagnosis-add')?.addEventListener('click', addDiagnosis);
        input?.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); addDiagnosis(); } });
        form.addEventListener('submit', event => { if (!readOnly && !addDiagnosis()) event.preventDefault(); });
        document.getElementById('visit-review-open')?.addEventListener('click', event => { if (!addDiagnosis()) event.stopImmediatePropagation(); }, {capture:true});
        diagnosis.addEventListener('input', renderDiagnosis);
        renderDiagnosis();
        const modal = document.getElementById('visit-vitals-modal');
        const vitals = Array.from(form.querySelectorAll('input[name^="vitals["]'));
        const renderVitals = () => {
            const values = document.getElementById('template-vitals-values'); values.replaceChildren();
            vitals.filter(field => field.value !== '').forEach(field => {
                const item = document.createElement('span'); const label = document.createElement('small');
                label.textContent = form.querySelector(`label[for="${field.id}"]`).textContent;
                const number = document.createElement('strong'); number.className = 'font-num'; number.textContent = field.value;
                item.append(label,number); values.append(item);
            });
            if (!values.childNodes.length) { const empty = document.createElement('p'); empty.textContent = 'لم يتم تسجيل علامات حيوية لهذه الزيارة'; values.append(empty); }
        };
        modal.addEventListener('hidden.bs.modal', renderVitals);
        form.addEventListener('invalid', event => {
            if (modal.contains(event.target)) {
                event.preventDefault();
                modal.addEventListener('shown.bs.modal', () => event.target.focus(), {once:true});
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        }, true);
    };
    window.initDoctorVisitTemplate();
})();
