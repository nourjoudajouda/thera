import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/doctor-practice-ajax.js', import.meta.url), 'utf8');

test('explicit native settings shortcut links are not intercepted as partial page updates', async () => {
    const listeners = new Map();
    let requests = 0, prevented = false;
    const link = {target:'_self',href:'http://localhost:8000/doctor/account/professional-profile?tab=achievements',closest() {return {};},hasAttribute() {return false;}};
    vm.runInNewContext(source, {window:{},document:{addEventListener(name, listener) {listeners.set(name, listener);}},fetch:async () => {requests++;}});

    await listeners.get('click')({defaultPrevented:false,button:0,target:{closest() {return link;}},preventDefault() {prevented = true;}});

    assert.equal(requests, 0);
    assert.equal(prevented, false);
});

for (const field of ['action', 'method', 'intent']) {
    test(field === 'action' ? 'an action-named select does not shadow the form submission URL' : (field === 'method' ? 'a method-named select does not shadow the form method' : 'the clicked clinical submit intent is included in the request'), async () => {
        const listeners = new Map();
        const requests = [];
        const main = { setAttribute() {}, removeAttribute() {} };
        class Form {
            method = field === 'method' ? { value: 'cash' } : 'post';
            action = { value: 'cancelled', toString: () => '[object HTMLSelectElement]' };
            closest() { return main; }
            querySelectorAll() { return []; }
            getAttribute(name) { return name === 'action' ? '/doctor/appointments/1/status' : (name === 'method' ? 'post' : null); }
            hasAttribute() { return false; }
        }
        const document = {
            addEventListener(name, listener) { listeners.set(name, listener); },
            querySelectorAll() { return []; },
            getElementById() { return main; },
        };
        const context = vm.createContext({
            document, HTMLFormElement: Form, FormData: class { values = {}; append(name, value) { this.values[name] = value; } }, URL,
            location: { href: 'http://localhost:8000/doctor/appointments/details?appointment=1' },
            window: { TheraAlerts: { error() {}, success() {} } },
            fetch: async (url, options) => {
                requests.push({ url: String(url), options });
                return { ok: true, headers: { get: () => 'application/json' }, json: async () => ({ message: 'saved' }), text: async () => '' };
            },
            DOMParser: class { parseFromString() { return { getElementById: () => null }; } },
        });
        vm.runInContext(source, context);

        await listeners.get('submit')({ target: new Form(), defaultPrevented: false, preventDefault() {}, submitter: field === 'intent' ? { name: 'intent', value: 'close', innerHTML: 'close', setAttribute() {}, removeAttribute() {} } : undefined });

        assert.equal(requests[0].url, '/doctor/appointments/1/status');
        assert.equal(requests[0].options.method, 'POST');
        assert.equal(requests[0].options.headers.Accept, 'application/json');
        if (field === 'intent') assert.equal(requests[0].options.body.values.intent, 'close');
    });
}

test('cancelling unsaved clinical navigation prevents fetching replacement content', async () => {
    let requests = 0;
    const window = {confirmDoctorVisitLeave:async () => false};
    vm.runInNewContext(source, {
        window, document:{addEventListener() {}},
        fetch:async () => { requests++; },
    });

    await window.TheraPractice.navigate('/doctor/clinical/visit?visit=2');

    assert.equal(requests, 0);
});

test('embedded collection saves refresh appointments instead of following the finance response URL', async () => {
    const listeners = new Map(), requests = [];
    const main = { setAttribute() {}, removeAttribute() {} };
    const href = 'http://localhost:8000/doctor/appointments?view=list&date=2026-11-01&collection=18&search=Reem';
    class Form {
        id = 'finance-payment-form';
        closest() { return main; }
        querySelectorAll() { return []; }
        getAttribute(name) { return name === 'action' ? '/doctor/finance/collections/18' : (name === 'method' ? 'post' : null); }
        hasAttribute() { return false; }
    }
    vm.runInNewContext(source, {
        document: { addEventListener(name, listener) { listeners.set(name, listener); }, querySelectorAll() { return []; }, getElementById() { return main; } },
        HTMLFormElement: Form, FormData: class {}, URL, location: { href },
        window: { TheraAlerts: { error() {}, success() {} } },
        fetch: async (url, options) => {
            requests.push({ url: String(url), options });
            return { ok: true, headers: { get: () => 'application/json' }, json: async () => ({ message: 'saved', url: '/doctor/finance/collections?appointment=18' }), text: async () => '' };
        },
        DOMParser: class { parseFromString() { return { getElementById: () => null }; } },
    });
    await listeners.get('submit')({ target: new Form(), defaultPrevented: false, preventDefault() {} });
    assert.equal(requests[0].options.method, 'POST');
    assert.equal(requests[1].url, href);
    assert.equal(requests[1].options.cache, 'no-store');
});

test('opening a new workspace inserts its drawer even when the previous page had no drawer', async () => {
    const appended = [], nodes = new Map();
    const main = {setAttribute() {}, removeAttribute() {}, replaceWith(next) { nodes.set('main-content-layout', next); }};
    nodes.set('main-content-layout', main);
    const drawer = {id:'service-drawer'};
    const state = {id:'practice-page-state'};
    const next = new Map([['main-content-layout',main], ['service-drawer',drawer], ['practice-page-state',state]]);
    const document = {
        addEventListener() {}, querySelectorAll() { return []; }, getElementById(id) { return nodes.get(id); },
        body:{append(node) { appended.push(node); nodes.set(node.id,node); }},
    };
    const window = {initDoctorAppointments() {}, TheraAlerts:{error(message) { throw new Error(message); }}};
    vm.runInNewContext(source, {
        window, document, URL, location:{href:'http://localhost:8000/doctor/clinical/visit?visit=1'}, history:{replaceState() {}},
        fetch:async () => ({ok:true, async text() { return ''; }}),
        DOMParser:class {parseFromString() {return {title:'Prescription',getElementById(id) {return next.get(id); }}; }},
    });

    await window.TheraPractice.navigate('/doctor/clinical/prescriptions/1');

    assert.ok(appended.includes(drawer));
});
for (const attribute of ['data-inline-create', 'data-inline-editor']) {
    test(`generic AJAX leaves ${attribute} forms to the visit popup without replacing the parent draft`, async () => {
        const listeners = new Map();
        let requests = 0, prevented = false;
        class Form { closest() { return {}; } hasAttribute(name) { return name === attribute; } }
        vm.runInNewContext(source, {
            document:{addEventListener(name, listener) { listeners.set(name, listener); }},
            HTMLFormElement:Form, window:{}, fetch:async () => { requests++; },
        });
        await listeners.get('submit')({target:new Form(), defaultPrevented:false, preventDefault() { prevented=true; }});
        assert.equal(requests, 0);
        assert.equal(prevented, false);
    });
}
test('successful visit closure navigates to appointments instead of re-rendering the visit', async () => {
    const listeners = new Map(), requests = [], destinations = [];
    let markedSaved = false;
    class Form {
        id='visit-documentation-form';
        closest() { return {}; }
        querySelectorAll() { return []; }
        hasAttribute() { return false; }
        getAttribute(name) { return name==='method'?'post':'/doctor/clinical/visits/4'; }
    }
    vm.runInNewContext(source, {
        document:{addEventListener(name, listener) { listeners.set(name, listener); },querySelectorAll() {return [];},getElementById() {return {setAttribute() {},removeAttribute() {}};}}, HTMLFormElement:Form,
        FormData:class {append() {}},
        window:{markDoctorVisitSaved() {markedSaved=true;},location:{assign(url) {destinations.push(url);}}},
        fetch:async (url) => {requests.push(url);return {ok:true,headers:{get() {return 'application/json';}},json:async () => ({redirect:true,url:'/doctor/appointments?view=list'})};},
    });
    await listeners.get('submit')({target:new Form(),defaultPrevented:false,preventDefault() {},submitter:{name:'intent',value:'close',innerHTML:'close',setAttribute() {},removeAttribute() {}}});
    assert.equal(markedSaved,true);
    assert.deepEqual(destinations,['/doctor/appointments?view=list']);
    assert.equal(requests.length,1);
});
