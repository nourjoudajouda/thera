import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/doctor-visit-template.js', import.meta.url), 'utf8');
function setup({readOnly = false, diagnosisValue = ''} = {}) {
    const element = () => ({children:[], listeners:new Map(), value:'', textContent:'', attributes:{}, dataset:{},
        get childNodes() { return this.children; },
        append(...children) { this.children.push(...children); }, replaceChildren() { this.children=[]; this.textContent=''; },
        setAttribute(key,value) { this.attributes[key]=value; },
        addEventListener(name,handler) { this.listeners.set(name,handler); },
        dispatchEvent(event) { this.listeners.get(event.type)?.(event); },
    });
    const form = element(); form.querySelector = selector => selector === 'fieldset' ? {disabled:readOnly} : {textContent:'النبض'}; form.querySelectorAll = () => [];
    const diagnosis = element(); diagnosis.value=diagnosisValue;
    const input = element(), list = element(), modal = element(); modal.contains=() => false;
    const add = element(), review = element();
    const nodes = {'visit-documentation-form':form,'visit-diagnosis':diagnosis,'template-diagnosis-input':input,'template-diagnosis-list':list,'template-diagnosis-add':add,'visit-review-open':review,'visit-vitals-modal':modal,'template-vitals-values':element()};
    let changes=0; diagnosis.addEventListener('input',()=>changes++);
    const window={TheraAlerts:{info() {}}};
    const document={getElementById:id=>nodes[id]??null,createElement:element};
    vm.runInNewContext(source,{window,document,Event:class {constructor(type) {this.type=type;}}});
    return {form,diagnosis,input,list,add,review,window,get changes() {return changes;}};
}

test('template diagnosis editor renders safe text and preserves the same newline text field on add and remove', () => {
    const page=setup({diagnosisValue:'تشخيص أول'});
    page.input.value='<img src=x onerror=alert(1)>';
    page.add.listeners.get('click')();
    assert.equal(page.diagnosis.value,'تشخيص أول\n<img src=x onerror=alert(1)>');
    assert.equal(page.list.children[1].children[0].textContent,'<img src=x onerror=alert(1)>');
    page.list.children[0].children[1].listeners.get('click')();
    assert.equal(page.diagnosis.value,'<img src=x onerror=alert(1)>');
    assert.equal(page.input.value,'');
});

test('pending diagnosis is included before draft submission and before the existing close review', () => {
    const page=setup();
    page.input.value='تشخيص محفوظ مع المسودة';
    page.form.listeners.get('submit')({preventDefault() {throw new Error('should submit');}});
    assert.equal(page.diagnosis.value,'تشخيص محفوظ مع المسودة');
    page.input.value='تشخيص ثان';
    page.review.listeners.get('click')({stopImmediatePropagation() {throw new Error('should review');}});
    assert.equal(page.diagnosis.value,'تشخيص محفوظ مع المسودة\nتشخيص ثان');
});

test('read-only diagnoses have no remove actions and repeat initialization does not replace handlers', () => {
    const page=setup({readOnly:true,diagnosisValue:'توثيق معتمد'});
    assert.equal(page.list.children[0].children.length,1);
    const listener=page.form.listeners.get('submit');
    page.window.initDoctorVisitTemplate();
    assert.equal(page.form.listeners.get('submit'),listener);
});

test('diagnosis length limit prevents submitting silently truncated clinical text', () => {
    const page=setup({diagnosisValue:'x'.repeat(10000)});
    page.input.value='إضافة';
    let prevented=false;
    page.form.listeners.get('submit')({preventDefault() {prevented=true;}});
    assert.equal(prevented,true);
    assert.equal(page.diagnosis.value.length,10000);
    assert.equal(page.input.value,'إضافة');
});
