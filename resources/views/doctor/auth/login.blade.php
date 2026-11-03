@php($isAdminPortal = ($authPortal ?? 'doctor') === 'admin')
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $isAdminPortal ? 'دخول الإدارة' : 'تسجيل دخول الطبيب' }} - THERA</title>

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
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
  .brand-top{display:flex;align-items:flex-start;justify-content:flex-end;gap:18px}
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

  .login-pane{display:flex;align-items:center;justify-content:center;min-width:0;padding:28px 36px;background:#F5F8FC}
  .login-wrap{width:100%;max-width:440px;min-width:0}
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
  a.register{text-decoration:none}
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
    .page{width:100%;height:auto;min-height:100vh;grid-template-columns:minmax(0,1fr)}
    .brand-pane{display:none}
    .login-pane{min-height:100vh;padding:28px 20px}
  }
</style>

<style id="thera-auth-final-logo-size">
  .brand-top img.brand-logo{
    height:94px !important;
    width:136px !important;
    max-width:136px !important;
    object-fit:contain !important;
  }
</style>


</head>
<body>
<div class="page">
  <section class="login-pane">
    <div class="login-wrap">
      <div class="portal-tag">
        <span class="material-symbols-outlined text-[16px]">{{ $isAdminPortal ? 'admin_panel_settings' : 'stethoscope' }}</span>
        <span>{{ $isAdminPortal ? 'بوابة الإدارة' : 'بوابة الطبيب' }}</span>
      </div>

      <h1 class="title">تسجيل الدخول إلى THERA</h1>
      <p class="subtitle">{{ $isAdminPortal ? 'ادخل إلى لوحة الإدارة لمراجعة الأطباء وطلبات الاعتماد.' : 'ادخل إلى مساحة عملك لإدارة المواعيد والمرضى والزيارات.' }}</p>

      <form class="form" id="{{ $isAdminPortal ? 'admin-login-form' : 'doctor-login-form' }}" method="POST" action="{{ $isAdminPortal ? route('admin.login.store') : route('doctor.login.store') }}" data-server-auth novalidate>
        @csrf
        <div>
          <label class="label" for="login-identity">{{ $isAdminPortal ? 'البريد الإلكتروني' : 'البريد الإلكتروني أو رقم الهاتف' }}</label>
          <div class="input-wrap">
            <span class="material-symbols-outlined input-icon">person</span>
            <input class="field {{ $errors->has($isAdminPortal ? 'email' : 'identity') ? 'error' : '' }}" id="login-identity" name="{{ $isAdminPortal ? 'email' : 'identity' }}" type="{{ $isAdminPortal ? 'email' : 'text' }}" value="{{ old($isAdminPortal ? 'email' : 'identity') }}" autocomplete="username" placeholder="{{ $isAdminPortal ? 'admin@thera.test' : 'doctor@example.com أو +962...' }}" />
          </div>
          <p class="field-error {{ $errors->has($isAdminPortal ? 'email' : 'identity') ? 'show' : '' }}" id="identity-error">{{ $errors->first($isAdminPortal ? 'email' : 'identity', $isAdminPortal ? 'أدخل البريد الإلكتروني.' : 'أدخل البريد الإلكتروني أو رقم الهاتف.') }}</p>
        </div>

        <div>
          <div class="password-line">
            <label class="label !mb-0" for="login-password">كلمة المرور</label>
            @unless($isAdminPortal)<button class="link-btn" id="forgot-password-btn" type="button">نسيت كلمة المرور؟</button>@endunless
          </div>
          <div class="input-wrap mt-[7px]">
            <span class="material-symbols-outlined input-icon">lock</span>
            <input class="field @error('password') error @enderror" id="login-password" name="password" type="password" autocomplete="current-password" placeholder="أدخل كلمة المرور" />
            <button class="eye-btn" id="toggle-password" type="button" aria-label="إظهار كلمة المرور">
              <span class="material-symbols-outlined text-[18px]">visibility</span>
            </button>
          </div>
          <p class="field-error @error('password') show @enderror" id="password-error">{{ $errors->first('password', 'أدخل كلمة المرور.') }}</p>
        </div>

        <label class="remember">
          <input class="check" id="remember-me" name="remember" value="1" type="checkbox" @checked(old('remember'))/>
          <span>تذكرني على هذا الجهاز</span>
        </label>

        <button class="primary" id="login-submit" type="submit">
          <span class="material-symbols-outlined text-[18px]">login</span>
          <span>تسجيل الدخول</span>
        </button>
      </form>

      <div class="divider"><span>{{ $isAdminPortal ? 'وصول مخصص لإدارة THERA' : 'طبيب جديد على THERA؟' }}</span></div>

      @unless($isAdminPortal)
        <a class="register" id="create-doctor-account" href="{{ route('doctor.register') }}">
          <span class="material-symbols-outlined">person_add</span>
          <span>إنشاء حساب طبيب جديد</span>
        </a>
      @else
        <div class="register" aria-label="وصول إداري محمي">
          <span class="material-symbols-outlined">admin_panel_settings</span>
          <span>وصول إداري محمي</span>
        </div>
      @endunless

      <div class="verification-line">
        <span class="material-symbols-outlined">{{ $isAdminPortal ? 'shield_lock' : 'verified_user' }}</span>
        <span>{{ $isAdminPortal ? 'هذه الصفحة مخصصة لمديري النظام، وجميع روابطها محمية بصلاحيات مستقلة.' : 'بعد إنشاء الحساب، يكمل الطبيب ملفه المهني ويرسل طلب التحقق قبل ظهوره كطبيب موثّق.' }}</span>
      </div>
    </div>
  </section>

  <aside class="brand-pane">
    <div class="orb w-[330px] h-[330px] -top-24 -right-20"></div>
    <div class="orb w-[420px] h-[420px] -bottom-36 -left-28"></div>

    <div class="brand-inner">
      <div class="brand-top">
        <img src="{{ asset('images/doctor/thera-logo.png') }}" alt="THERA Logo" class="brand-logo"/>
      </div>

      <div class="brand-copy">
<h2 class="hero-title">{!! $isAdminPortal ? 'إدارة الاعتمادات<br/>بوضوح وأمان.' : 'مساحة واحدة للعمل الطبي<br/>من الموعد حتى المتابعة.' !!}</h2>
        <p class="hero-copy">{{ $isAdminPortal ? 'مراجعة حسابات الأطباء وبياناتهم المهنية وطلبات المنشآت من مساحة إدارية موحّدة ومحكومة بالصلاحيات.' : 'إدارة المواعيد والمرضى والزيارات والوصفات والطلبات والنتائج ضمن سياق طبي موحّد ومحكوم بالصلاحيات.' }}</p>

        <div class="cards-grid">
          <article class="feature-card">
            <div class="feature-icon"><span class="material-symbols-outlined text-[#A7F3D0] text-[21px]">{{ $isAdminPortal ? 'manage_accounts' : 'work_history' }}</span></div>
            <h3>{{ $isAdminPortal ? 'إدارة حسابات الأطباء' : 'سجل سريري مترابط' }}</h3>
            <p>{{ $isAdminPortal ? 'متابعة الأطباء المسجلين وحالة اكتمال ملفاتهم المهنية.' : 'زيارة، ووصفة، ومختبر، وأشعة، ونتيجة، وإحالة في مسار واحد.' }}</p>
          </article>

          <article class="feature-card">
            <div class="feature-icon"><span class="material-symbols-outlined text-[#7DD3FC] text-[21px]">{{ $isAdminPortal ? 'approval' : 'calendar_month' }}</span></div>
            <h3>{{ $isAdminPortal ? 'طلبات اعتماد مركزية' : 'مواعيد وجدولة' }}</h3>
            <p>{{ $isAdminPortal ? 'التخصصات والمؤهلات والإنجازات والمنشآت في مكان واحد.' : 'عرض يومي، وأسبوعي، وشهري، وسعة حجوزات واضحة.' }}</p>
          </article>

          <article class="feature-card">
            <div class="feature-icon"><span class="material-symbols-outlined text-[#FDE68A] text-[21px]">verified</span></div>
            <h3>{{ $isAdminPortal ? 'قرارات موثقة' : 'هوية مهنية موثوقة' }}</h3>
            <p>{{ $isAdminPortal ? 'قبول أو رفض واضح مع حفظ السبب وإظهاره للطبيب.' : 'ملف مهني منفصل ومسار تحقق قبل التوثيق.' }}</p>
          </article>

          <article class="feature-card">
            <div class="feature-icon"><span class="material-symbols-outlined text-[#C4B5FD] text-[21px]">shield_lock</span></div>
            <h3>صلاحيات وتدقيق</h3>
            <p>{{ $isAdminPortal ? 'جلسة إدارية منفصلة لحماية البيانات والقرارات الحساسة.' : 'وصول سياقي مع حماية البيانات الحساسة وسجل تدقيق.' }}</p>
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

<div id="toast-host" class="fixed bottom-6 left-6 z-[100] flex flex-col gap-2 pointer-events-none"></div>

@unless($isAdminPortal)
<div id="forgot-modal" class="fixed inset-0 z-[90] {{ request()->boolean('recovery') || session('recovery_status') || $errors->recovery->isNotEmpty() ? '' : 'hidden' }}">
  <div class="absolute inset-0 bg-slate-950/35 backdrop-blur-[1px]" data-close-forgot></div>
  <section class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[430px] max-w-[92vw] rounded-2xl bg-white border border-slate-200 shadow-2xl overflow-hidden">
    <header class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">استرجاع الحساب</h2>
        <p class="text-xs text-slate-500 mt-1">أدخل البريد الإلكتروني أو رقم الهاتف المرتبط بحسابك.</p>
      </div>
      <button type="button" data-close-forgot class="w-9 h-9 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center">
        <span class="material-symbols-outlined">close</span>
      </button>
    </header>
    <form method="POST" action="{{ route('doctor.password.email') }}" data-server-auth>
      @csrf
    <div class="p-5">
      @if (session('recovery_status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold leading-6 text-emerald-700">{{ session('recovery_status') }}</div>
      @endif
      <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني أو رقم الهاتف</label>
      <input id="recovery-identity" name="identity" value="{{ old('identity') }}" class="field !pr-3 !pl-3 @error('identity', 'recovery') error @enderror" type="text" placeholder="doctor@example.com أو +962..."/>
      @error('identity', 'recovery')<p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
      <p class="text-[11px] text-slate-500 mt-2 leading-5">سيتم ربط طريقة الاسترجاع بآلية المصادقة المعتمدة للنظام.</p>
    </div>
    <footer class="px-5 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-2">
      <button type="button" data-close-forgot class="h-10 px-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold">إلغاء</button>
      <button id="send-recovery-btn" type="submit" class="h-10 px-4 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-sm font-bold">متابعة</button>
    </footer>
    </form>
  </section>
</div>
@endunless

<script>
(() => {
  'use strict';
  const $ = id => document.getElementById(id);

  function toast(message, type='info') {
    const host = $('toast-host');
    if(!host) return;
    const tone = type==='success' ? 'bg-emerald-600' :
                 type==='warning' ? 'bg-amber-500' :
                 type==='error' ? 'bg-red-600' : 'bg-slate-800';
    const el = document.createElement('div');
    el.className = `toast ${tone} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-bold max-w-sm pointer-events-auto flex items-center gap-2`;
    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined text-[18px]';
    icon.textContent = type==='success' ? 'check_circle' :
                       type==='warning' ? 'warning' :
                       type==='error' ? 'error' : 'info';
    const text = document.createElement('span');
    text.textContent = message;
    el.append(icon,text);
    host.appendChild(el);
    setTimeout(()=>el.remove(),2700);
  }

  function errorState(inputId, errorId, active) {
    $(inputId)?.classList.toggle('error',active);
    $(errorId)?.classList.toggle('show',active);
  }

  $('toggle-password')?.addEventListener('click',() => {
    const input = $('login-password');
    const icon = $('toggle-password').querySelector('.material-symbols-outlined');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.textContent = show ? 'visibility_off' : 'visibility';
    $('toggle-password').setAttribute('aria-label',show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
  });

  $('forgot-password-btn')?.addEventListener('click',() => {
    $('forgot-modal').classList.remove('hidden');
    setTimeout(()=>$('recovery-identity')?.focus(),80);
  });

  document.querySelectorAll('[data-close-forgot]').forEach(el => {
    el.addEventListener('click',()=>$('forgot-modal').classList.add('hidden'));
  });

  document.addEventListener('keydown',e => {
    if(e.key==='Escape') $('forgot-modal')?.classList.add('hidden');
  });
})();
</script>
@unless($isAdminPortal)
  @include('doctor.partials.route-map')
  <script src="{{ asset('js/doctor.js') }}" defer></script>
@endunless
</body>
</html>
