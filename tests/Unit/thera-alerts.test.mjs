import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../../public/js/thera-alerts.js', import.meta.url), 'utf8');

for (const intent of ['save', 'close']) {
    test(`clinical ${intent} ${intent === 'close' ? 'requires final confirmation' : 'does not require a confirmation dialog'}`, async () => {
        const listeners = new Map(), dialogs = [];
        let prevented = false;
        class Form {
            dataset = {theraConfirmIntent:'close'};
            matches() { return true; }
        }
        const window = {Swal:{async fire(options) { dialogs.push(options); return {isConfirmed:false}; }}};
        const document = {readyState:'complete', addEventListener(name, handler) { listeners.set(name, handler); }};
        vm.runInNewContext(source, {window, document, HTMLFormElement:Form});

        await listeners.get('submit')({target:new Form(), submitter:{value:intent}, preventDefault() { prevented = true; }});

        assert.equal(dialogs.length, intent === 'close' ? 1 : 0);
        assert.equal(prevented, intent === 'close');
    });
}
