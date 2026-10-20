import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/thera-filters.js', import.meta.url), 'utf8');
function setup() {
    const calls = [], timers = new Map(), events = new Map(), selectEvents = new Map(), inputEvents = new Map();
    let sequence = 0;
    const select = { addEventListener(name, callback) { selectEvents.set(name, callback); } };
    const input = { type: 'search', addEventListener(name, callback) { inputEvents.set(name, callback); } };
    const form = {
        dataset: {}, isConnected: true, valid: true,
        fields: [['status', 'completed'], ['search', ''], ['page', '3']],
        checkValidity() { return this.valid; },
        getAttribute() { return '/doctor/clinical/visits'; },
        closest() { return null; },
        addEventListener() {},
        querySelectorAll(query) { return query === 'select' ? [select] : [input]; },
    };
    const window = { TheraPractice: { async navigate(url, options) { calls.push({url:String(url), options}); } } };
    const document = {
        readyState: 'complete', activeElement: null,
        querySelectorAll() { return [form]; },
        addEventListener(name, callback) { events.set(name, callback); },
    };
    vm.runInNewContext(source, {
        window, document, URL, URLSearchParams,
        location: {href:'http://localhost:8000/doctor/clinical/visits?page=3', origin:'http://localhost:8000'},
        FormData: class { constructor(item) { this.fields = item.fields; } [Symbol.iterator]() { return this.fields[Symbol.iterator](); } },
        setTimeout(callback) { const id = ++sequence; timers.set(id, callback); return id; },
        clearTimeout(id) { timers.delete(id); },
    });
    return {window, form, calls, timers, events, selectEvents, inputEvents};
}

test('select filters immediately update results and drop obsolete pagination and empty values', async () => {
    const {selectEvents, calls} = setup();

    await selectEvents.get('change')();

    assert.equal(calls[0].url, 'http://localhost:8000/doctor/clinical/visits?status=completed');
});

test('typing waits until the latest search value instead of requiring a search button', async () => {
    const {inputEvents, form, timers, calls} = setup();
    inputEvents.get('input')();
    form.fields = [['search', 'ريم']];
    inputEvents.get('input')();

    assert.equal(calls.length, 0);
    assert.equal(timers.size, 1);
    await [...timers.values()][0]();

    assert.equal(new URL(calls[0].url).searchParams.get('search'), 'ريم');
});

test('incomplete or detached filter forms do not send a request', async () => {
    const {window, form, calls} = setup();
    form.valid = false;

    await window.TheraFilters.submit(form);
    form.valid = true; form.isConnected = false;
    await window.TheraFilters.submit(form);

    assert.equal(calls.length, 0);
});

test('a filter drawer is retained when its criteria update automatically', async () => {
    const {window, form, calls} = setup();
    const drawer = {};
    form.closest = selector => selector === '.offcanvas.show' ? drawer : null;

    await window.TheraFilters.submit(form);

    assert.equal(calls[0].options.filterDrawer, drawer);
});

test('admin filters replace results and URL without a full page navigation', async () => {
    const {window, form} = setup();
    delete window.TheraPractice;
    const requests = [], replacements = [], historyUpdates = [];
    const region = {dataset:{}};
    const main = {
        setAttribute() {}, removeAttribute() {},
        replaceWith(next) { replacements.push(next); },
    };
    const document = {
        readyState:'complete', activeElement:null,
        getElementById() { return main; },
        querySelectorAll(query) { return query.startsWith('form[') ? [form] : []; },
        addEventListener() {},
    };
    form.dataset = {};
    form.getAttribute = () => '/admin/doctors';
    vm.runInNewContext(source, {
        window, document, URL, URLSearchParams, AbortController,
        location:{href:'http://localhost:8000/admin/doctors',origin:'http://localhost:8000'},
        FormData:class { [Symbol.iterator]() { return [['search','ريم']][Symbol.iterator](); } },
        fetch:async (url, options) => {
            requests.push({url:String(url), options});
            return {ok:true, async text() { return 'results'; }};
        },
        DOMParser:class { parseFromString() { return {title:'الأطباء', getElementById() { return region; }}; } },
        history:{replaceState(state, title, url) { historyUpdates.push(String(url)); }},
    });

    await window.TheraFilters.submit(form);

    assert.equal(requests[0].options.headers['X-Requested-With'], 'XMLHttpRequest');
    assert.equal(new URL(requests[0].url).searchParams.get('search'), 'ريم');
    assert.equal(replacements[0], region);
    assert.equal(historyUpdates[0], requests[0].url);
    assert.equal(document.title, 'الأطباء');
});
