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
        querySelector() { return null; },
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

test('review requires an assessment or diagnosis and renders all entered fields as text without saving', () => {
    const handlers = new Map();
    const makeField = (id, value, tagName = 'TEXTAREA') => ({id, value, tagName, attributes:{}, setAttribute(key,value) { this.attributes[key] = value; }, removeAttribute(key) { delete this.attributes[key]; }, focus() { this.focused = true; }});
    const assessment = makeField('visit-assessment', '');
    const diagnosis = makeField('visit-diagnosis', '');
    const history = makeField('visit-medical_history', '<img src=x onerror=alert(1)>');
    const form = {dataset:{}, querySelector(selector) { return selector === 'fieldset' ? {disabled:false} : {textContent:selector}; }, querySelectorAll(selector) { return selector === '[data-visit-section-state]' ? [] : [assessment, diagnosis, history]; }, addEventListener(name, handler) { const previous = handlers.get(name); handlers.set(name,event => { previous?.(event); handler(event); }); }, reportValidity() { return true; }};
    const review = {hidden:true, scrollIntoView() {}};
    const error = {hidden:true};
    const content = {children:[], replaceChildren() { this.children = []; }, append(...items) { this.children.push(...items); }};
    const reviewButton = {addEventListener(name, handler) { handlers.set('review',handler); }};
    const nodes = {'visit-documentation-form':form,'visit-review-open':reviewButton,'visit-review':review,'visit-review-error':error,'visit-assessment':assessment,'visit-diagnosis':diagnosis,'visit-review-content':content,'visit-review-heading':{focus() {}},'visit-review-edit':{addEventListener(name,handler) { handlers.set('edit',handler); }},'visit-complaint':makeField('visit-complaint','')};
    nodes['visit-review-confirm'] = {addEventListener(name,handler) { handlers.set('confirm',handler); }};
    nodes['visit-close-submit'] = {value:'close'};
    let submitted = null;
    form.requestSubmit = button => { submitted = button.value; };
    const document = {getElementById(id) { return nodes[id] || null; }, querySelector() { return null; }, createElement() { return {textContent:''}; }, addEventListener() {}};
    const window = {addEventListener() {}};
    vm.runInNewContext(source,{document,window});

    handlers.get('review')();
    assert.equal(error.hidden,false);
    assert.equal(assessment.focused,true);
    assert.equal(review.hidden,true);

    assessment.value = 'تقييم مدون';
    handlers.get('input')();
    handlers.get('review')();
    assert.equal(review.hidden,false);
    assert.equal(error.hidden,true);
    assert.equal(content.children.at(-1).textContent,'<img src=x onerror=alert(1)>');
    assert.equal(window.doctorVisitIsDirty(),true);
    handlers.get('confirm')();
    assert.equal(submitted,'close');
    submitted = null;
    handlers.get('edit')();
    assert.equal(review.hidden,true);
    assert.equal(history.value,'<img src=x onerror=alert(1)>');
    handlers.get('confirm')();
    assert.equal(submitted,null);
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

test('diagnostic order edits remain dirty until a confirmed server save', async () => {
    const {window, formEvents} = setup('diagnostic-order-form');
    formEvents.get('input')();
    assert.equal(window.doctorVisitIsDirty(), true);
    assert.equal(await window.confirmDoctorVisitLeave(), false);
    window.markDoctorVisitSaved();
    assert.equal(window.doctorVisitIsDirty(), false);
});

test('referral edits use the clinical unsaved-change guard', async () => {
    const {window, formEvents} = setup('referral-form');
    formEvents.get('input')();
    assert.equal(window.doctorVisitIsDirty(), true);
    assert.equal(await window.confirmDoctorVisitLeave(), false);
    window.markDoctorVisitSaved();
    assert.equal(window.doctorVisitIsDirty(), false);
});
