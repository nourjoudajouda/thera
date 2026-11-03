import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/doctor-patients.js', import.meta.url), 'utf8');
const scope = modeValue => {
    const listeners = [];
    const mode = {value:modeValue, addEventListener:(event,handler)=>listeners.push(handler)};
    const fields = ['new','existing','thera','new_thera'].map(value=>({dataset:{patientFields:value},hidden:false,disabled:false}));
    return {dataset:{},mode,fields,listeners,querySelector:selector=>selector==='[data-patient-mode]'?mode:{dataset:{patientPickerUrl:'/doctor/appointments/contacts'}},querySelectorAll:()=>fields};
};
test('patient mode enables only the selected identity inputs without copying or merging contact data',()=>{
    const current=scope('new');
    const context=vm.createContext({document:{querySelectorAll:()=>[current]},window:{}});
    vm.runInContext(source,context);
    assert.equal(current.fields[0].disabled,false);
    assert.equal(current.fields[1].disabled,true);
    current.mode.value='existing';current.listeners[0]();
    assert.equal(current.fields[0].disabled,true);
    assert.equal(current.fields[0].hidden,true);
    assert.equal(current.fields[1].disabled,false);
    assert.equal(current.fields[1].hidden,false);
});
test('Thera selection submits only its picker and switching back disables stale Thera fields',()=>{
    const current=scope('thera');
    const context=vm.createContext({document:{querySelectorAll:()=>[current]},window:{}});
    vm.runInContext(source,context);
    assert.equal(current.fields[0].disabled,true);
    assert.equal(current.fields[1].disabled,true);
    assert.equal(current.fields[2].disabled,false);
    assert.equal(current.fields[2].hidden,false);
    current.mode.value='existing';current.listeners[0]();
    assert.equal(current.fields[1].disabled,false);
    assert.equal(current.fields[2].disabled,true);
    assert.equal(current.fields[2].hidden,true);
});
test('AJAX replacement binds a fresh patient picker once and preserves preselected existing mode',()=>{
    let current=scope('new');
    const context=vm.createContext({document:{querySelectorAll:()=>[current]},window:{}});
    vm.runInContext(source,context);
    context.window.initDoctorPatients();
    assert.equal(current.listeners.length,1);
    current=scope('existing');context.window.initDoctorPatients();context.window.initDoctorPatients();
    assert.equal(current.listeners.length,1);
    assert.equal(current.fields[0].disabled,true);
    assert.equal(current.fields[1].disabled,false);
});
test('network registration enables only its fields and never submits its stale email in local mode',()=>{
    const current=scope('new_thera');
    const context=vm.createContext({document:{querySelectorAll:()=>[current]},window:{}});
    vm.runInContext(source,context);
    assert.deepEqual(current.fields.map(field=>field.disabled),[true,true,true,false]);
    current.mode.value='new';current.listeners[0]();
    assert.deepEqual(current.fields.map(field=>field.disabled),[false,true,true,true]);
    assert.equal(current.fields[3].hidden,true);
});
