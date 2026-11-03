import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const source=await readFile(new URL('../../public/js/doctor-shell.js',import.meta.url),'utf8');
function setup({doctorId='1',storage=new Map(),failed=false}={}) {
    const events={}, nodes=new Map(), location={href:'http://localhost/doctor/account/settings',origin:'http://localhost',assign(url){this.destination=String(url);}};
    function node(id='') {
        const classes=new Set(id.endsWith('-menu')||id==='quick-add-dropdown'?['hidden']:[]);
        return {id,dataset:{},attributes:{},style:{},children:[],listeners:{},textContent:'',hidden:false,value:'',
            classList:{add(value){classes.add(value);},remove(value){classes.delete(value);},contains(value){return classes.has(value);},toggle(value,enabled){if(enabled??!classes.has(value))classes.add(value);else classes.delete(value);}},
            setAttribute(key,value){this.attributes[key]=value;},addEventListener(key,fn){this.listeners[key]=fn;},
            append(...children){this.children.push(...children);},replaceChildren(...children){this.children=children;},
            querySelector(){return this.icon??(this.icon=node());},focus(){this.focused=true;},closest(){return null;}};
    }
    for(const id of ['app-sidebar','doctor-card-trigger','doctor-account-menu','quick-add-btn','quick-add-dropdown','doctor-notifications-toggle','doctor-notifications-menu','doctor-notifications-list','doctor-notifications-dot','doctor-language-toggle','doctor-language-menu','doctor-language-label','doctor-theme-toggle','setting-theme','setting-language','doctor-global-search','doctor-search-menu']) nodes.set(id,node(id));
    nodes.get('app-sidebar').dataset.doctorId=doctorId;
    nodes.get('doctor-notifications-toggle').dataset.notificationsUrl='http://localhost/doctor/shell/notifications';
    const translated=node(); translated.dataset.i18n='الإعدادات'; translated.textContent='الإعدادات';
    const patient=node();patient.textContent='اسم المريض وملاحظته';
    const languages=['ar','en'].map(value=>{const el=node();el.dataset.doctorLanguage=value;return el;});
    const document={documentElement:node(),getElementById(id){return nodes.get(id);},createElement(){return node();},
        querySelectorAll(selector){return selector==='[data-i18n]'?[translated]:selector==='[data-doctor-language]'?languages:[];},
        addEventListener(key,fn){events[key]=fn;},querySelector(){return null;}};
    const window={doctorRoutes:{profile:'http://localhost/doctor/account/professional-profile',settings:location.href,patients:'http://localhost/doctor/patients',appointments:'http://localhost/doctor/appointments'},doctorTranslations:{'الإعدادات':'Settings'},matchMedia(){return {matches:false,addEventListener(){}};}};
    vm.runInNewContext(source,{window,document,location,URL,queueMicrotask:fn=>fn(),localStorage:{getItem:key=>storage.get(key)??null,setItem:(key,value)=>storage.set(key,value)},fetch:async()=>({ok:!failed,redirected:false,json:async()=>({items:[{url:'http://localhost/doctor/appointments',label:'مواعيد اليوم',icon:'calendar_today',count:2}],total:2})})});
    events.DOMContentLoaded();
    return {nodes,events,document,window,storage,languages,translated,patient,location};
}
test('opening a shell menu closes other menus and Escape restores keyboard focus',()=>{
    const s=setup(), account=s.nodes.get('doctor-card-trigger'), language=s.nodes.get('doctor-language-toggle');
    account.listeners.click();
    assert.equal(account.attributes['aria-expanded'],'true');
    language.listeners.click();
    assert.equal(account.attributes['aria-expanded'],'false');
    assert.equal(s.nodes.get('doctor-account-menu').classList.contains('hidden'),true);
    s.events.keydown({key:'Escape'});
    assert.equal(language.attributes['aria-expanded'],'false');assert.equal(language.focused,true);
});
test('theme preferences persist per doctor and stay synchronized with settings without modifying medical text',()=>{
    const storage=new Map(), s=setup({storage});
    s.nodes.get('doctor-theme-toggle').listeners.click();
    assert.equal(s.document.documentElement.dataset.doctorTheme,'dark');
    assert.equal(s.nodes.get('setting-theme').value,'dark');
    assert.equal(setup({storage}).document.documentElement.dataset.doctorTheme,'dark');
    assert.equal(setup({storage,doctorId:'2'}).document.documentElement.dataset.doctorTheme,'light');
    s.languages[1].listeners.click();
    assert.equal(s.translated.textContent,'Settings');assert.equal(s.patient.textContent,'اسم المريض وملاحظته');
    s.languages[0].listeners.click();assert.equal(s.translated.textContent,'الإعدادات');
});
test('notification failures show retry instead of false empty state and success uses safe text',async()=>{
    const failed=setup({failed:true});
    await new Promise(resolve=>setImmediate(resolve));
    assert.equal(failed.nodes.get('doctor-notifications-list').children[1].textContent,'إعادة المحاولة');
    const s=setup();await new Promise(resolve=>setImmediate(resolve));
    assert.equal(s.nodes.get('doctor-notifications-dot').hidden,false);
    assert.equal(s.nodes.get('doctor-notifications-list').children[0].children[2].textContent,'2');
});
test('global search builds encoded doctor scoped destinations rather than rendering the query as markup',()=>{
    const s=setup(), search=s.nodes.get('doctor-global-search');search.value='<img src=x onerror=alert(1)>';
    search.listeners.input();
    const link=s.nodes.get('doctor-search-menu').children[0];
    assert.equal(new URL(link.href).pathname,'/doctor/patients');
    assert.equal(new URL(link.href).searchParams.get('search'),search.value);
    assert.equal(link.textContent,'البحث في المرضى');
});
