<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>استرجاع حساب الطبيب - THERA</title>

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet"/>
@include('doctor.partials.assets', ['useFormPlugin' => false])
<style>
  *{box-sizing:border-box}
  html,body{margin:0;width:100%;height:100%;overflow:hidden;background:#F5F8FC;font-family:'Tajawal',sans-serif;color:#0B1020}
  button,input{font-family:'Tajawal',sans-serif}

  .page{
    width:100vw;height:100vh;display:grid;
    grid-template-columns:minmax(0,1.06fr) minmax(450px,.94fr);
    overflow:hidden;background:#F5F8FC;
  }

  .brand-pane{
    position:relative;overflow:hidden;color:#fff;
    background:
      radial-gradient(circle at 84% 12%, rgba(87,187,255,.12), transparent 24%),
      radial-gradient(circle at 7% 92%, rgba(141,92,255,.16), transparent 30%),
      linear-gradient(135deg,#07172C 0%,#082548 58%,#083B60 100%);
  }
  .brand-pane::before{
    content:"";position:absolute;inset:0;opacity:.09;
    background-image:
      linear-gradient(rgba(255,255,255,.11) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,255,255,.11) 1px,transparent 1px);
    background-size:48px 48px;
    mask-image:linear-gradient(to bottom,rgba(0,0,0,.85),transparent 100%);
  }
  .orb{position:absolute;border-radius:999px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)}
  .brand-inner{position:relative;z-index:2;height:100%;padding:26px 32px 28px;display:flex;flex-direction:column}
  .brand-top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px}
  .brand-logo{height:100px;max-width:180px;object-fit:contain}
  .brand-copy{max-width:620px;margin:auto 0}
  .eyebrow{display:inline-flex;align-items:center;gap:8px;color:#7DD3FC;font-size:12px;font-weight:800;letter-spacing:.07em;margin-bottom:10px}
  .hero-title{font-size:38px;line-height:1.2;font-weight:800;letter-spacing:-.015em;margin:0}
  .hero-copy{font-size:14px;line-height:1.9;color:#D7E0EA;margin-top:12px;max-width:560px}
  .cards-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:26px;max-width:560px}
  .feature-card{
    min-height:140px;padding:16px 18px;border-radius:18px;
    background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.14);
    backdrop-filter:blur(8px);box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
  }
  .feature-icon{width:38px;height:38px;border-radius:12px;background:rgba(125,211,252,.10);display:flex;align-items:center;justify-content:center;margin-bottom:14px}
  .feature-card h3{margin:0;font-size:15px;font-weight:800;line-height:1.4}
  .feature-card p{margin:7px 0 0;color:#CFD9E6;font-size:11.5px;line-height:1.75}
  .brand-foot{display:flex;align-items:center;justify-content:space-between;gap:18px;color:#A6B3C4;font-size:10.5px;margin-top:auto;padding-top:18px}
  .secure-note{display:flex;align-items:center;gap:7px}
  .secure-dot{width:8px;height:8px;border-radius:50%;background:#10B981;box-shadow:0 0 0 4px rgba(16,185,129,.12)}

  .login-pane{display:flex;align-items:center;justify-content:center;padding:28px 36px;background:#F5F8FC}
  .login-wrap{width:100%;max-width:440px}
  .portal-tag{display:inline-flex;align-items:center;gap:7px;padding:6px 10px;border-radius:999px;background:#ECF8FF;color:#0369A1;font-size:11px;font-weight:800;margin-bottom:12px}
  .title{font-size:29px;line-height:1.25;font-weight:800;margin:0}
  .subtitle{font-size:13px;line-height:1.8;color:#718096;margin-top:8px;max-width:390px}
  .form{margin-top:24px;display:flex;flex-direction:column;gap:15px}
  .label{display:block;font-size:11.5px;font-weight:800;color:#334155;margin-bottom:7px}
  .password-line{display:flex;align-items:center;justify-content:space-between;gap:12px}
  .link-btn{border:0;background:transparent;color:#0495F6;font-size:11px;font-weight:800;cursor:pointer;padding:0}
  .link-btn:hover{color:#0466F1}
  .input-wrap{position:relative}
  .input-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#94A3B8;font-size:18px!important;pointer-events:none}
  .field{
    width:100%;height:48px;padding:0 43px 0 44px;border:1px solid #DCE4EC;border-radius:12px;background:#fff;
    color:#0B1020;font-size:13.5px;outline:none;transition:border-color .17s ease, box-shadow .17s ease, background .17s ease;
    box-shadow:0 1px 2px rgba(15,23,42,.02);
  }
  .field:hover{border-color:#CBD5E1}
  .field:focus{border-color:#0495F6;box-shadow:0 0 0 3px rgba(4,149,246,.09)}
  .field.error{border-color:#EF4444;box-shadow:0 0 0 3px rgba(239,68,68,.07)}
  .field-error{display:none;color:#DC2626;font-size:10.5px;margin-top:6px}
  .field-error.show{display:block}
  .eye-btn{position:absolute;left:7px;top:50%;transform:translateY(-50%);width:36px;height:36px;border:0;border-radius:9px;background:transparent;color:#94A3B8;display:flex;align-items:center;justify-content:center;cursor:pointer}
  .eye-btn:hover{background:#F1F5F9;color:#64748B}
  .remember{display:flex;align-items:center;gap:9px;color:#64748B;font-size:11.5px;cursor:pointer;width:max-content;user-select:none}
  .check{appearance:none;width:17px;height:17px;border:1.4px solid #CBD5E1;border-radius:5px;background:#fff;display:grid;place-content:center;cursor:pointer}
  .check:checked{background:#0495F6;border-color:#0495F6}
  .check:checked:after{content:"✓";font-size:11px;color:#fff;font-weight:900;line-height:1}
  .primary{height:48px;border:0;border-radius:12px;background:#0495F6;color:#fff;display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px;font-weight:800;cursor:pointer;box-shadow:0 8px 20px rgba(4,149,246,.16);transition:.17s ease}
  .primary:hover{background:#0466F1;box-shadow:0 9px 24px rgba(4,102,241,.18)}
  .primary:disabled{opacity:.62;cursor:not-allowed}
  .divider{display:flex;align-items:center;gap:12px;margin:21px 0 16px}
  .divider:before,.divider:after{content:"";height:1px;background:#E2E8F0;flex:1}
  .divider span{font-size:10.5px;color:#94A3B8;font-weight:600;white-space:nowrap}
  .register{height:46px;width:100%;border:1px solid #DCE4EC;border-radius:12px;background:#fff;color:#334155;font-size:13px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.17s ease}
  .register:hover{background:#F8FAFC;border-color:#C8D3DF}
  .register .material-symbols-outlined{color:#0495F6;font-size:18px}
  .verification-line{margin-top:15px;display:flex;align-items:flex-start;gap:8px;color:#718096;font-size:10.5px;line-height:1.7}
  .verification-line .material-symbols-outlined{font-size:16px;color:#10B981;margin-top:1px;flex:none}

  .toast{animation:toast-in .2s ease-out}
  @keyframes toast-in{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:translateY(0)}}

  @media(max-height:760px){
    .brand-inner{padding:22px 28px 22px}
    .brand-logo{height:100px}
    .hero-title{font-size:34px}
    .hero-copy{font-size:12.5px;margin-top:10px}
    .cards-grid{gap:10px;margin-top:18px}
    .feature-card{min-height:124px;padding:14px 15px}
    .feature-icon{width:34px;height:34px;margin-bottom:11px}
    .feature-card h3{font-size:14px}
    .feature-card p{font-size:10.5px;line-height:1.65}
    .brand-foot{padding-top:14px}
    .login-pane{padding:18px 30px}
    .title{font-size:26px}
    .form{margin-top:19px;gap:12px}
    .field{height:45px}
    .primary{height:45px}
    .register{height:43px}
  }

  @media(max-width:980px){
    html,body{overflow:auto}
    .page{height:auto;min-height:100vh;grid-template-columns:1fr}
    .brand-pane{display:none}
    .login-pane{min-height:100vh;padding:28px 20px}
  }
</style>
<style id="thera-recovery-v1">
  .recovery-wrap{max-width:440px}
  .recovery-form{margin-top:27px}
  .recovery-help{
    margin-top:17px;
    padding:12px 13px;
    border:1px solid #DDEAF6;
    border-radius:12px;
    background:#F5FAFF;
    color:#66788F;
    font-size:10.5px;
    line-height:1.75;
    display:flex;
    align-items:flex-start;
    gap:8px;
  }
  .recovery-help .material-symbols-outlined{
    font-size:17px;
    color:#0495F6;
    flex:none;
    margin-top:1px;
  }
  .recovery-success{text-align:center}
  .success-icon{
    width:58px;height:58px;border-radius:18px;
    background:#ECFDF5;color:#10B981;
    display:flex;align-items:center;justify-content:center;
    margin:0 auto;
  }
  .success-icon .material-symbols-outlined{font-size:29px}
  .success-card{
    margin-top:22px;
    border:1px solid #DDE7F0;
    background:#fff;
    border-radius:14px;
    padding:13px 14px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    text-align:right;
  }
  .success-card .material-symbols-outlined{color:#10B981;font-size:22px}
  .success-label{
    display:block;
    color:#94A3B8;
    font-size:10.5px;
    margin-bottom:3px;
  }
  .success-card strong{
    display:block;
    color:#0B1020;
    font-size:12px;
    direction:ltr;
    text-align:right;
  }
  .resend-link{
    margin:13px auto 0;
    border:0;background:transparent;color:#0495F6;
    font-size:11px;font-weight:800;cursor:pointer;
  }
  .resend-link:hover{color:#0466F1}
</style>
<style id="thera-auth-final-logo-size">
  .brand-logo{
    height:100px !important;
    width:auto !important;
    max-width:220px !important;
    object-fit:contain !important;
  }
</style>


</head>
<body>
<div class="page">
<section class="login-pane">
<div class="login-wrap recovery-wrap">
<div class="portal-tag">
<span class="material-symbols-outlined text-[16px]">key</span>
<span>استرجاع الحساب</span>
</div>
<div id="recovery-request-state">
<h1 class="title">نسيت كلمة المرور؟</h1>
<p class="subtitle">أدخل البريد الإلكتروني أو رقم الهاتف المرتبط بحساب الطبيب، وسنبدأ مسار الاسترجاع المعتمد للحساب.</p>
<form class="form recovery-form" id="doctor-recovery-form" method="POST" action="{{ route('doctor.password.email') }}" data-server-auth novalidate="">
@csrf
@if (session('status'))<p class="mb-4 rounded-xl bg-emerald-50 p-3 text-xs font-bold text-emerald-700">{{ session('status') }}</p>@endif
<div>
<label class="label" for="recovery-identity">البريد الإلكتروني أو رقم الهاتف</label>
<div class="input-wrap">
<span class="material-symbols-outlined input-icon">person_search</span>
<input autocomplete="username" class="field @error('identity') error @enderror" id="recovery-identity" name="identity" value="{{ old('identity') }}" placeholder="doctor@example.com أو +962..." type="text"/>
</div>
<p class="field-error" id="recovery-error">أدخل البريد الإلكتروني أو رقم الهاتف.</p>
</div>
<button class="primary" id="recovery-submit" type="submit">
<span class="material-symbols-outlined text-[18px]">arrow_back</span>
<span>متابعة الاسترجاع</span>
</button>
</form>
<div class="recovery-help">
<span class="material-symbols-outlined">info</span>
<span>طريقة الاسترجاع النهائية تعتمد على آلية المصادقة المرتبطة بالحساب؛ قد تكون عبر وسيلة اتصال موثّقة أو آلية أخرى يعتمدها النظام.</span>
</div>
</div>
<div class="hidden recovery-success" id="recovery-sent-state">
<div class="success-icon">
<span class="material-symbols-outlined">mark_email_read</span>
</div>
<h1 class="title !mt-5">تم بدء استرجاع الحساب</h1>
<p class="subtitle !mt-2" id="recovery-sent-text">تحقق من وسيلة الاسترجاع المرتبطة بحسابك واتبع التعليمات لإكمال العملية.</p>
<div class="success-card">
<div>
<span class="success-label">الحساب</span>
<strong id="recovery-account-preview">—</strong>
</div>
<span class="material-symbols-outlined">verified_user</span>
</div>
<button class="primary mt-5" id="back-login-from-success" type="button">
<span class="material-symbols-outlined text-[18px]">login</span>
<span>العودة إلى تسجيل الدخول</span>
</button>
<button class="resend-link" id="retry-recovery" type="button">استخدام بريد أو رقم هاتف آخر</button>
</div>
<div class="divider"><span>تذكرت كلمة المرور؟</span></div>
<button class="register" id="back-to-login" type="button">
<span class="material-symbols-outlined">login</span>
<span>العودة إلى تسجيل الدخول</span>
</button>
</div>
</section>
<aside class="brand-pane">
<div class="orb w-[330px] h-[330px] -top-24 -right-20"></div>
<div class="orb w-[420px] h-[420px] -bottom-36 -left-28"></div>
<div class="brand-inner">
<div class="brand-top">
<img alt="THERA Logo" class="brand-logo" src="{{ asset('images/doctor/thera-logo.png') }}"/>
</div>
<div class="brand-copy">
<h2 class="hero-title">مساحة واحدة للعمل الطبي<br/>من الموعد حتى المتابعة.</h2>
<p class="hero-copy">إدارة المواعيد والمرضى والزيارات والوصفات والطلبات والنتائج ضمن سياق طبي موحّد ومحكوم بالصلاحيات.</p>
<div class="cards-grid">
<article class="feature-card">
<div class="feature-icon"><span class="material-symbols-outlined text-[#A7F3D0] text-[21px]">work_history</span></div>
<h3>سجل سريري مترابط</h3>
<p>زيارة، ووصفة، ومختبر، وأشعة، ونتيجة، وإحالة في مسار واحد.</p>
</article>
<article class="feature-card">
<div class="feature-icon"><span class="material-symbols-outlined text-[#7DD3FC] text-[21px]">calendar_month</span></div>
<h3>مواعيد وجدولة</h3>
<p>عرض يومي، وأسبوعي، وشهري، وسعة حجوزات واضحة.</p>
</article>
<article class="feature-card">
<div class="feature-icon"><span class="material-symbols-outlined text-[#FDE68A] text-[21px]">verified</span></div>
<h3>هوية مهنية موثوقة</h3>
<p>ملف مهني منفصل ومسار تحقق قبل التوثيق.</p>
</article>
<article class="feature-card">
<div class="feature-icon"><span class="material-symbols-outlined text-[#C4B5FD] text-[21px]">shield_lock</span></div>
<h3>صلاحيات وتدقيق</h3>
<p>وصول سياقي مع حماية البيانات الحساسة وسجل تدقيق.</p>
</article>
</div>
</div>
<div class="brand-foot">
<span>THERA Connected Healthcare</span>
<div class="secure-note">
<span class="secure-dot"></span>
<span>وصول محكوم بالصلاحيات</span>
</div>
</div>
</div>
</aside>
</div>
<div class="fixed bottom-6 left-6 z-[100] flex flex-col gap-2 pointer-events-none" id="toast-host"></div>


<script id="thera-doctor-recovery-runtime">
(() => {
  'use strict';
  const $ = id => document.getElementById(id);

  function toast(message,type='info'){
    const host=$('toast-host');
    if(!host)return;
    const tone=type==='success'?'bg-emerald-600':
               type==='warning'?'bg-amber-500':
               type==='error'?'bg-red-600':'bg-slate-800';

    const el=document.createElement('div');
    el.className=`toast ${tone} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-bold max-w-sm pointer-events-auto flex items-center gap-2`;

    const icon=document.createElement('span');
    icon.className='material-symbols-outlined text-[18px]';
    icon.textContent=type==='success'?'check_circle':
                     type==='warning'?'warning':
                     type==='error'?'error':'info';

    const text=document.createElement('span');
    text.textContent=message;
    el.append(icon,text);
    host.appendChild(el);
    setTimeout(()=>el.remove(),2800);
  }

  function setError(active){
    $('recovery-identity')?.classList.toggle('error',active);
    $('recovery-error')?.classList.toggle('show',active);
  }

  function maskIdentity(value){
    const v=value.trim();
    if(v.includes('@')){
      const [name,domain]=v.split('@');
      const visible=name.slice(0,2);
      return `${visible}${'*'.repeat(Math.max(3,name.length-2))}@${domain}`;
    }
    const digits=v.replace(/\s+/g,'');
    if(digits.length<=4)return digits;
    return `${digits.slice(0,3)}••••${digits.slice(-3)}`;
  }

  function backToLogin(){
    window.location.href=@js(route('doctor.login'));
  }

  $('back-to-login')?.addEventListener('click',backToLogin);
  $('back-login-from-success')?.addEventListener('click',backToLogin);

  $('retry-recovery')?.addEventListener('click',()=>{
    $('recovery-sent-state').classList.add('hidden');
    $('recovery-request-state').classList.remove('hidden');
    $('recovery-identity').value='';
    setError(false);
    setTimeout(()=>$('recovery-identity')?.focus(),80);
  });
})();
</script>@include('doctor.partials.route-map')
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>
