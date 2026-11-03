import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const source=await readFile(new URL('../../public/js/doctor-settings.js',import.meta.url),'utf8');
function setup() {
    const events={}, warnings={}, urls=[];
    const tabField={value:'general'}, status={textContent:''}, label={textContent:'',dataset:{}}, dot={style:{}};
    const form={dataset:{},addEventListener(name,handler){events[name]=handler;},elements:{namedItem(){return tabField;}}};
    const tabs=['general','availability','notifications'].map(tab=>({dataset:{settingsTab:tab},classList:{toggle(){}},setAttribute(){},removeAttribute(){},addEventListener(name,handler){this.click=handler;}}));
    const sections=tabs.map(tab=>({id:'section-'+tab.dataset.settingsTab,hidden:false}));
    let current=form;
    const window={addEventListener(name,handler){warnings[name]=handler;}};
    const document={
        getElementById(id){return id==='doctor-settings-form'?current:id==='settings-save-state'?status:{dataset:{settingsAvailability:'busy'}};},
        querySelectorAll(selector){return selector==='[data-settings-tab]'?tabs:selector==='.settings-section'?sections:selector==='[data-doctor-availability-label]'?[label]:[dot];}
    };
    const context=vm.createContext({window,document,URL,location:{href:'http://localhost/doctor/account/settings'},history:{replaceState(a,b,url){urls.push(String(url));}}});
    vm.runInContext(source,context);
    return {window,form,events,warnings,tabs,sections,tabField,status,label,dot,urls,replace(value){current=value;}};
}
test('switching settings sections preserves unsaved inputs and updates the deep link without fetching',()=>{
    const s=setup();
    s.events.change();
    s.tabs[2].click();
    assert.equal(s.form.dataset.settingsDirty,'1');
    assert.equal(s.tabField.value,'notifications');
    assert.equal(s.sections[2].hidden,false);
    assert.equal(s.sections[0].hidden,true);
    assert.match(s.urls[0],/tab=notifications/);
    let prevented=false;
    s.warnings.beforeunload({preventDefault(){prevented=true;}});
    assert.equal(prevented,true);
});
test('saved availability updates the shared label and a replaced clean form has no stale leave warning',()=>{
    const s=setup();
    assert.equal(s.label.textContent,'مشغول مؤقتًا');
    assert.equal(s.dot.style.backgroundColor,'#f59e0b');
    s.events.input();
    s.replace({dataset:{}});
    let prevented=false;
    s.warnings.beforeunload({preventDefault(){prevented=true;}});
    assert.equal(prevented,false);
});

test('device appearance and language changes do not falsely mark server preferences unsaved',()=>{
    const s=setup();
    s.events.change({target:{id:'setting-theme'}});
    s.events.change({target:{id:'setting-language'}});
    assert.equal(s.form.dataset.settingsDirty,undefined);
    s.events.change({target:{id:'setting-availability'}});
    assert.equal(s.form.dataset.settingsDirty,'1');
});

function paymentEditor() {
    const s=setup(), handlers={};
    let type='percentage', timing='after_visit';
    const percent={value:'35',disabled:false}, deposit={}, remaining={}, timingPreview={};
    const classes=()=>({values:new Map(),toggle(key,value){this.values.set(key,value);}});
    const row={classList:classes()}, timingRow={classList:classes()}, earlyRow={classList:classes()};
    const timingInputs=['before_visit','at_arrival','after_visit'].map(value=>({value,disabled:false,closest(){return timingRow;}}));
    const earlyInputs=[{disabled:false},{disabled:false}];
    const nodes={'#deposit-percent':percent,'#deposit-percent-row':row,'#payment-example-deposit':deposit,'#payment-example-remaining':remaining,'#payment-example-timing':timingPreview,'#payment-remaining-before_visit':timingInputs[0],'#early-payment-row':earlyRow};
    const editor={dataset:{},querySelector(selector){
        if(selector==='input[name="payment_policy[type]"]:checked')return {value:type};
        if(selector==='input[name="payment_policy[remaining_timing]"]:checked')return {value:timing};
        return nodes[selector];
    },querySelectorAll(selector){return selector==='input[name="payment_policy[remaining_timing]"]'?timingInputs:earlyInputs;},addEventListener(event,handler){handlers[event]=handler;}};
    const form={querySelector(){return editor;}};
    s.window.initDoctorPaymentPolicy(form);
    return {percent,deposit,remaining,timingPreview,timingInputs,earlyInputs,handlers,setType(value){type=value;handlers.change();},setTiming(value){timing=value;handlers.change();}};
}

test('template payment choices immediately preview percentage, full and no deposit without resetting the entered percentage',()=>{
    const s=paymentEditor();
    assert.equal(s.deposit.textContent,'35.00');
    assert.equal(s.remaining.textContent,'65.00');
    assert.equal(s.timingPreview.textContent,'بعد الزيارة');
    s.setType('full');
    assert.equal(s.deposit.textContent,'100.00');
    assert.equal(s.remaining.textContent,'0.00');
    assert.equal(s.timingPreview.textContent,'مدفوع بالكامل');
    assert.equal(s.percent.disabled,true);
    assert.equal(s.timingInputs.every(input=>input.disabled),true);
    assert.equal(s.earlyInputs.every(input=>input.disabled),true);
    s.setType('none');
    assert.equal(s.deposit.textContent,'0.00');
    assert.equal(s.remaining.textContent,'100.00');
    assert.equal(s.timingInputs.some(input=>input.disabled),false);
    s.setType('percentage');
    assert.equal(s.percent.disabled,false);
    assert.equal(s.percent.value,'35');
    assert.equal(s.deposit.textContent,'35.00');
});

test('percentage and remaining timing edits update the example without changing the actual entered input',()=>{
    const s=paymentEditor();
    s.percent.value='45';
    s.handlers.input();
    assert.equal(s.deposit.textContent,'45.00');
    assert.equal(s.remaining.textContent,'55.00');
    s.setTiming('at_arrival');
    assert.equal(s.timingPreview.textContent,'عند الوصول');
    s.percent.value='125';
    s.handlers.input();
    assert.equal(s.percent.value,'125');
});
