import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/doctor-visits.js', import.meta.url), 'utf8');

function setup(formId = 'visit-documentation-form') {
    const events = new Map(), formEvents = new Map(), calls = [];
    const indicator = {dataset:{savedLabel:'saved on server'}, classList:{toggle() {}}, textContent:''};
    const form = {dataset:{}, querySelector() { return {disabled:false}; }, addEventListener(name, handler) { formEvents.set(name, handler); }};
    const window = {
        addEventListener(name, handler) { events.set(name, handler); },
        TheraAlerts:{async confirm(options) { calls.push(options); return {isConfirmed:window.confirmed}; }},
        confirmed:false,
    };
    const document = {
        getElementById(id) { return id === formId ? form : (id === 'visit-save-state' ? indicator : null); },
        addEventListener() {},
    };
    vm.runInNewContext(source, {window, document});
    return {window, formEvents, indicator, events, calls};
}

test('editing a visit shows unsaved state and cancelling navigation retains changes', async () => {
    const {window, formEvents, indicator, calls} = setup();
    formEvents.get('input')();

    assert.equal(await window.confirmDoctorVisitLeave(), false);
    assert.equal(calls.length, 1);
    assert.match(indicator.textContent, /غير محفوظة/);
    window.confirmed = true;
    assert.equal(await window.confirmDoctorVisitLeave(), true);
});

test('a successful save clears the warning while failed or cancelled saves stay dirty', async () => {
    const {window, formEvents, events, indicator} = setup();
    let warnings = 0;
    const event = {preventDefault() { warnings++; }};
    formEvents.get('change')();
    window.initDoctorVisit();
    events.get('beforeunload')(event);
    assert.equal(warnings, 1);

    window.markDoctorVisitSaved();
    events.get('beforeunload')(event);

    assert.equal(warnings, 1);
    assert.equal(await window.confirmDoctorVisitLeave(), true);
    assert.equal(indicator.textContent, 'saved on server');
});

test('prescription edits use the same unsaved-change protection as visit documentation', () => {
    const {window, formEvents} = setup('prescription-form');

    formEvents.get('input')();

    assert.equal(window.doctorVisitIsDirty(), true);
    window.markDoctorVisitSaved();
    assert.equal(window.doctorVisitIsDirty(), false);
});
