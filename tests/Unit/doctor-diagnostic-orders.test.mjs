import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const code = await readFile(new URL('../../public/js/doctor-diagnostic-orders.js', import.meta.url), 'utf8');

function setup(count = 1) {
    const listeners = new Map(), messages = [], inserted = [];
    let changes = 0;
    const rows = Array.from({length:count}, (_,index) => ({index,remove() {rows.splice(rows.indexOf(this),1);}}));
    const container = {querySelectorAll() {return rows;},insertAdjacentHTML(position,html) {inserted.push(html);rows.push({remove() {rows.splice(rows.indexOf(this),1);}});},addEventListener(name,handler) {listeners.set(name,handler);}};
    const add = {addEventListener(name,handler) {listeners.set('add',handler);}};
    const form = {dataset:{},querySelectorAll() {return rows.map((row,index)=>({name:`items[${index}][name]`}));},dispatchEvent() {changes++;}};
    const elements = new Map([['diagnostic-order-form',form],['diagnostic-items',container],['diagnostic-item-template',{innerHTML:'<input name="items[__INDEX__][name]" id="test-__INDEX__">'}],['diagnostic-add-item',add]]);
    const document = {getElementById(id) {return elements.get(id);}};
    const window = {TheraAlerts:{info(message) {messages.push(message);}}};
    vm.runInNewContext(code,{document,window,Event:class {}});
    const remove = row => listeners.get('click')({target:{closest() {return {closest() {return row;}};}}});
    return {rows,inserted,messages,add:()=>listeners.get('add')(),remove,changes:()=>changes,window};
}

test('added private test rows use fresh names after removal and mark the document unsaved', () => {
    const page = setup();
    page.add();
    page.remove(page.rows[1]);
    page.add();
    assert.match(page.inserted[0], /items\[1\]/);
    assert.match(page.inserted[1], /items\[2\]/);
    assert.equal(page.rows.length, 2);
    assert.equal(page.changes(), 3);
});

test('the final item cannot be removed and the server limit of twenty is reflected in the editor', () => {
    const single = setup();
    single.remove(single.rows[0]);
    assert.equal(single.rows.length, 1);
    assert.equal(single.messages.length, 1);
    const full = setup(20);
    full.add();
    assert.equal(full.rows.length, 20);
    assert.equal(full.messages.length, 1);
    assert.equal(full.changes(), 0);
});
