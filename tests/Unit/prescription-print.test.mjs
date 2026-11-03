import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const code = await readFile(new URL('../../public/js/prescription-print.js', import.meta.url), 'utf8');

function harness(response, images = []) {
    let handler;
    const calls = [], errors = [];
    let prints = 0;
    const button = {disabled:false,dataset:{url:'/prescriptions/7/print'},addEventListener(event, callback) {handler = callback;},setAttribute() {},removeAttribute() {}};
    const document = {getElementById() {return button;},querySelector() {return {content:'csrf-token'};},fonts:{ready:Promise.resolve()},images};
    vm.runInNewContext(code, {document,window:{print() {prints++;},TheraAlerts:{error(message) {errors.push(message);}}},fetch:async (url, options) => {calls.push({url,options}); return response;}});
    return {click:() => handler(),button,calls,errors,prints:() => prints};
}

test('printing logs an authenticated CSRF-protected request before opening the print dialog without reloading', async () => {
    const page = harness({ok:true,headers:{get() {return 'application/json';}}});
    await page.click();
    assert.equal(page.prints(), 1);
    assert.equal(page.calls.length, 1);
    assert.equal(page.calls[0].options.method, 'POST');
    assert.equal(page.calls[0].options.credentials, 'same-origin');
    assert.equal(page.calls[0].options.headers['X-CSRF-TOKEN'], 'csrf-token');
    assert.equal(page.button.disabled, false);
    assert.deepEqual(page.errors, []);
});

test('expired sessions and failed audit requests do not open the print dialog or claim success', async () => {
    const page = harness({ok:false,headers:{get() {return 'application/json';}}});
    await page.click();
    assert.equal(page.prints(), 0);
    assert.equal(page.errors.length, 1);
    assert.equal(page.button.disabled, false);
});

test('redirected login HTML is not mistaken for a successful print request', async () => {
    const page = harness({ok:true,headers:{get() {return 'text/html';}}});
    await page.click();
    assert.equal(page.prints(), 0);
    assert.equal(page.errors.length, 1);
});

test('a broken stamp or logo prevents silently printing a document missing its image', async () => {
    const page = harness({ok:true,headers:{get() {return 'application/json';}}}, [{complete:true,naturalWidth:0}]);
    await page.click();
    assert.equal(page.prints(), 0);
    assert.equal(page.errors.length, 1);
    assert.equal(page.button.disabled, false);
});

function stampHarness({ok = true, uri = null} = {}) {
    const listeners = {}, calls = [], errors = [];
    let prints = 0;
    const button = {disabled:false,dataset:{url:'/prescriptions/7/print'},addEventListener(event, callback) {listeners.print = callback;},setAttribute() {},removeAttribute() {}};
    const choice = {value:'12',disabled:false,selectedOptions:[{dataset:{src:'/doctor/account/stamp?stamp=12'}}],addEventListener(event, callback) {listeners.change = callback;}};
    const image = {hidden:true,complete:true,naturalWidth:100,src:'',classList:{add() {}},removeAttribute(name) {if (name === 'src') this.src = '';},async decode() {}};
    const noStamp = {hidden:false,textContent:'هذه النسخة بدون ختم.'};
    const document = {getElementById() {return button;},querySelector(selector) {
        return {'#print-stamp-choice':choice,'#print-stamp-image':image,'#print-no-stamp':noStamp,'meta[name="csrf-token"]':{content:'token'}}[selector];
    },images:[image],fonts:{ready:Promise.resolve()}};
    vm.runInNewContext(code, {document,window:{print() {prints++;},TheraAlerts:{error(message) {errors.push(message);}}},fetch:async (url, options) => {
        calls.push(options); return {ok,headers:{get() {return 'application/json';}},async json() {return {stamp_data_uri:uri};}};
    }});
    return {listeners,choice,image,noStamp,calls,errors,prints:() => prints};
}

test('selecting a stamp previews it without navigating and printing uses the server-validated stamp bytes', async () => {
    const page = stampHarness({uri:'data:image/png;base64,c2VydmVy'});
    page.listeners.change();
    assert.equal(page.image.src, '/doctor/account/stamp?stamp=12');
    assert.equal(page.image.hidden, false);
    assert.equal(page.noStamp.hidden, true);
    assert.equal(page.calls.length, 0);
    await page.listeners.print();
    assert.deepEqual(JSON.parse(page.calls[0].body), {stamp_choice:'12'});
    assert.equal(page.image.src, 'data:image/png;base64,c2VydmVy');
    assert.equal(page.prints(), 1);
    assert.equal(page.choice.disabled, false);
});

test('without-stamp removes the image and does not block printing because the hidden image has no source', async () => {
    const page = stampHarness();
    page.choice.value = 'none';
    page.choice.selectedOptions = [{dataset:{}}];
    page.image.naturalWidth = 0;
    page.listeners.change();
    assert.equal(page.image.hidden, true);
    assert.equal(page.image.src, '');
    assert.equal(page.noStamp.hidden, false);
    await page.listeners.print();
    assert.deepEqual(JSON.parse(page.calls[0].body), {stamp_choice:'none'});
    assert.equal(page.prints(), 1);
    assert.deepEqual(page.errors, []);
});

test('a rejected stamp choice does not open printing and unlocks the selection', async () => {
    const page = stampHarness({ok:false});
    await page.listeners.print();
    assert.equal(page.prints(), 0);
    assert.equal(page.errors.length, 1);
    assert.equal(page.choice.disabled, false);
});
