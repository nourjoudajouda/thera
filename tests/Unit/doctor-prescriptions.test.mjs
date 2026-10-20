import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const sourceCode = await readFile(new URL('../../public/js/doctor-prescriptions.js', import.meta.url), 'utf8');

test('switching between catalog and private medication submits only fields of the selected source', () => {
    const listeners = new Map();
    const source = {value:'custom',addEventListener(name, handler) {listeners.set(name,handler);}};
    const privateInputs = [{disabled:false}], catalogInputs = [{disabled:true}];
    const custom = {hidden:false,querySelectorAll() {return privateInputs;}};
    const catalog = {hidden:true,querySelectorAll() {return catalogInputs;}};
    const row = {querySelector(selector) {return selector === '[data-rx-source]' ? source : (selector === '[data-rx-custom]' ? custom : catalog);}};
    const container = {querySelectorAll() {return [row];},addEventListener() {}};
    let changes = 0;
    const form = {dataset:{},querySelectorAll() {return [];},dispatchEvent() {changes++;}};
    const document = {getElementById(id) {return id === 'prescription-form' ? form : (id === 'rx-items' ? container : null);}};
    vm.runInNewContext(sourceCode, {window:{},document,Event:class {}});
    assert.equal(catalogInputs[0].disabled, true);
    assert.equal(privateInputs[0].disabled, false);

    source.value = 'catalog';
    listeners.get('change')();

    assert.equal(custom.hidden, true);
    assert.equal(privateInputs[0].disabled, true);
    assert.equal(catalog.hidden, false);
    assert.equal(catalogInputs[0].disabled, false);
    assert.equal(changes, 1);
});
