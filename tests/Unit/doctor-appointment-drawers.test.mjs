import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/doctor-appointments.js', import.meta.url), 'utf8');

function initializeDrawer(href, queryKey = 'appointment') {
    const listeners = new Map();
    const location = { href };
    const drawer = { dataset: { financeQueryKey: queryKey }, addEventListener(name, listener) { listeners.set(name, listener); } };
    const window = { bootstrap: { Offcanvas: { getOrCreateInstance() { return { show() {} }; } } } };
    const document = {
        getElementById(id) { return id === 'practice-page-state' ? { textContent: '{}' } : null; },
        querySelector(selector) { return selector === '[data-finance-drawer]' ? drawer : null; },
    };
    vm.runInNewContext(source, {
        window, document, location, URL,
        history: { replaceState(state, title, url) { location.href = String(url); } },
    });
    return { listeners, location, window };
}

test('closing collections removes only the selected appointment and preserves date and filters', () => {
    const { listeners, location } = initializeDrawer('http://localhost:8000/doctor/finance/collections?appointment=18&date=2026-10-16&search=Reem&payment_status=open');

    listeners.get('hidden.bs.offcanvas')();

    const url = new URL(location.href);
    assert.equal(url.searchParams.has('appointment'), false);
    assert.equal(url.searchParams.get('date'), '2026-10-16');
    assert.equal(url.searchParams.get('search'), 'Reem');
    assert.equal(url.searchParams.get('payment_status'), 'open');
});

test('a late close event cannot clear a different appointment opened afterwards', () => {
    const { listeners, location } = initializeDrawer('http://localhost:8000/doctor/finance/collections?appointment=18&date=2026-10-16');
    location.href = 'http://localhost:8000/doctor/finance/collections?appointment=19&date=2026-10-16';

    listeners.get('hidden.bs.offcanvas')();

    assert.equal(new URL(location.href).searchParams.get('appointment'), '19');
});

test('closing an embedded collection preserves the appointment detail and list filters', () => {
    const { listeners, location } = initializeDrawer('http://localhost:8000/doctor/appointments/details?appointment=18&collection=18&date=2026-11-01&view=list&search=Reem&page=2', 'collection');
    listeners.get('hidden.bs.offcanvas')();
    const url = new URL(location.href);
    assert.equal(url.searchParams.has('collection'), false);
    assert.equal(url.searchParams.get('appointment'), '18');
    assert.equal(url.searchParams.get('view'), 'list');
    assert.equal(url.searchParams.get('date'), '2026-11-01');
    assert.equal(url.searchParams.get('search'), 'Reem');
    assert.equal(url.searchParams.get('page'), '2');
});

test('a stale embedded drawer close does not clear the next patient collection', () => {
    const { listeners, location } = initializeDrawer('http://localhost:8000/doctor/appointments?collection=18&view=list', 'collection');
    location.href = 'http://localhost:8000/doctor/appointments?collection=19&view=list';
    listeners.get('hidden.bs.offcanvas')();
    assert.equal(new URL(location.href).searchParams.get('collection'), '19');
});
