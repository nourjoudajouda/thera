<!DOCTYPE html>

<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الملف المهني - THERA</title>
<!-- Google Fonts: Tajawal & Inter -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Tajawal:wght@400;500;700;800&amp;family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&amp;display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<!-- Tailwind CSS v3 with forms and container-queries -->

<!-- Tailwind Configuration -->

<!-- Typography and Material Symbols Styles -->
@include('doctor.partials.assets')
<style data-purpose="typography">
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #F6F9FC;
      color: #1E293B;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
    }
    .font-num {
      font-family: 'Inter', sans-serif;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
      user-select: none;
    }
    .material-symbols-outlined.fill-1 {
      font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
<!-- Layout & Sidebar Transitions -->
<style data-purpose="layout-sidebar">
    .sidebar-scroll {
      overflow-y: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    .sidebar-scroll::-webkit-scrollbar {
      width: 0;
      height: 0;
      display: none;
    }
    
    #app-sidebar {
      transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #top-header {
      transition: right 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #main-content-layout {
      transition: margin-right 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Canonical pinned / collapsed Sidebar states */
    #app-sidebar {
      width: 268px;
      transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s ease;
    }

    #app-sidebar.sidebar-collapsed {
      width: 72px;
    }

    #app-sidebar.sidebar-collapsed.sidebar-hovered {
      width: 268px;
      box-shadow: -8px 0 25px rgba(0, 0, 0, 0.25);
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-label,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .group-heading,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .sidebar-brand-text,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-meta,
    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) #sidebar-pin-toggle {
      display: none !important;
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .nav-item {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    #app-sidebar.sidebar-collapsed:not(.sidebar-hovered) .doctor-card-body {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    /* Custom select arrow styling for RTL */
    .rtl-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
      background-position: left 0.75rem center;
      background-repeat: no-repeat;
      background-size: 1.25em 1.25em;
      padding-left: 2.5rem;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }
  </style>
<style data-purpose="professional-profile-page">
  .profile-tabs-wrap { width:100%; max-width:100%; overflow:hidden; }
  .profile-tabs-list { width:100%; min-width:0; display:grid; grid-template-columns:minmax(0,1.2fr) minmax(0,1.25fr) minmax(0,.95fr) minmax(0,.85fr) minmax(0,1.55fr) minmax(0,.9fr) minmax(0,1.15fr); align-items:center; gap:8px; }
  .profile-tab { width:100%; min-width:0; height:40px; padding-inline:8px; white-space:nowrap; border:1px solid transparent; border-radius:999px; background:#EEF4FF; color:#3F4752; font-size:13px; font-weight:700; transition:.18s ease; }
  .profile-tab:hover { background:#E5EEFF; color:#0F172A; }
  .profile-tab[aria-selected="true"] { background:#0495F6; border-color:#0495F6; color:#fff; box-shadow:0 1px 3px rgba(4,149,246,.16); }
  .profile-panel { display:none; }
  .profile-panel.active { display:block; }
  .profile-card { background:#fff; border:1px solid #E2E8F0; border-radius:18px; box-shadow:0 2px 10px rgba(15,23,42,.035); }
  .field-label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:7px; }
  .field-help { font-size:12px; color:#94A3B8; margin-top:6px; }
  .input-base { width:100%; }
  .textarea-base { width:100%; min-height:118px; resize:vertical; }
  .status-pill { display:inline-flex; align-items:center; gap:5px; height:28px; padding:0 10px; border-radius:999px; font-size:12px; font-weight:700; }
  .summary-row + .summary-row { border-top:1px solid #F1F5F9; }
  .progress-track { height:7px; border-radius:999px; background:#E2E8F0; overflow:hidden; }
  .progress-fill { height:100%; border-radius:999px; background:#0495F6; }
  .avatar-drop { border:1px dashed #CBD5E1; transition:.18s ease; }
  .avatar-drop:hover { border-color:#0495F6; background:#F8FCFF; }
  .credential-card, .facility-card, .achievement-card, .specialty-card { border:1px solid #E2E8F0; border-radius:14px; background:#fff; }
  .public-preview-shell { background:linear-gradient(180deg,#F7FBFF 0,#FFFFFF 180px); }
  .soft-grid { background-image:radial-gradient(circle at 1px 1px, rgba(148,163,184,.16) 1px, transparent 0); background-size:18px 18px; }
  .toggle { position:relative; width:44px; height:24px; flex:0 0 auto; }
  .toggle input { opacity:0; width:0; height:0; }
  .toggle-track { position:absolute; inset:0; border-radius:999px; background:#CBD5E1; transition:.18s ease; cursor:pointer; }
  .toggle-track::after { content:""; position:absolute; top:3px; right:3px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.22); transition:.18s ease; }
  .toggle input:checked + .toggle-track { background:#0495F6; }
  .toggle input:checked + .toggle-track::after { transform:translateX(-20px); }
  @media (max-width: 1199.98px) {
    .profile-tabs-wrap { overflow-x:auto; overflow-y:hidden; scrollbar-width:none; overscroll-behavior-inline:contain; }
    .profile-tabs-wrap::-webkit-scrollbar { display:none; }
    .profile-tabs-list { display:flex; width:max-content; min-width:max-content; justify-content:flex-start; }
    .profile-tab { width:auto; min-width:max-content; padding-inline:14px; font-size:14px; }
  }
</style>
<style id="thera-profile-unified-v3">
  html,body{overflow-x:hidden}
  .save-section,#add-credential-btn{border-radius:8px!important}
</style>
<style id="thera-profile-onboarding-v4">
.onboarding-banner{
  border:1px solid #CFE8FB;
  background:linear-gradient(90deg,#F5FBFF 0%,#F8FAFF 100%);
  border-radius:16px;
  padding:16px 18px;
}
.onboarding-step-pill{
  height:26px;padding:0 9px;border-radius:999px;
  display:inline-flex;align-items:center;
  background:#EEF2F7;color:#64748B;font-size:11px;font-weight:800;
}
.onboarding-step-pill.active{background:#E0F2FE;color:#0369A1}
body.onboarding-profile-mode [data-onboarding-required="1"]::after{
  content:" *";color:#EF4444;font-weight:900;
}
.onboarding-empty{
  border:1px dashed #CBD5E1;
  border-radius:16px;
  background:#F8FAFC;
  padding:24px;
  text-align:center;
  color:#64748B;
}
.onboarding-empty .material-symbols-outlined{
  width:44px;height:44px;border-radius:14px;background:#EEF4FF;color:#0495F6;
  display:flex;align-items:center;justify-content:center;margin:0 auto 10px;
}
.professional-profile-page .thera-control,
.professional-profile-page .form-control,
.professional-profile-page .form-select,
.professional-profile-page .input-base,
.professional-profile-page .textarea-base {
  width:100%;
  min-height:44px!important;
  border:1px solid #CBD5E1!important;
  border-radius:12px!important;
  background-color:#FFFFFF!important;
  color:#334155;
  padding:10px 13px!important;
  font-family:'Tajawal',sans-serif!important;
  font-size:14px!important;
  font-weight:500;
  line-height:1.5;
  box-shadow:none!important;
  box-sizing:border-box;
  transition:border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}
.professional-profile-page .form-select,
.professional-profile-page select.input-base,
.professional-profile-page .thera-select {
  padding-right:13px!important;
  padding-left:44px!important;
  background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.7' d='M6 8l4 4 4-4'/%3e%3c/svg%3e")!important;
  background-position:left 13px center!important;
  background-repeat:no-repeat!important;
  background-size:18px 18px!important;
  appearance:none!important;
  cursor:pointer;
}
.professional-profile-page textarea.form-control,
.professional-profile-page .textarea-base,
.professional-profile-page .thera-textarea {
  min-height:118px!important;
  height:auto;
  padding:12px 13px!important;
  resize:vertical;
}
.professional-profile-page :is(.thera-control, .form-control, .form-select, .input-base, .textarea-base):hover:not(:disabled):not(:focus) {
  border-color:#94A3B8!important;
}
.professional-profile-page :is(.thera-control, .form-control, .form-select, .input-base, .textarea-base):focus {
  outline:0!important;
  border-color:#0495F6!important;
  background-color:#FFFFFF!important;
  box-shadow:0 0 0 3px rgba(4,149,246,.10)!important;
}
.professional-profile-page :is(.thera-control, .form-control, .form-select, .input-base, .textarea-base)::placeholder {
  color:#94A3B8;
  opacity:1;
}
.professional-profile-page :is(.thera-control, .form-control, .form-select, .input-base, .textarea-base):disabled {
  border-color:#E2E8F0!important;
  background-color:#F1F5F9!important;
  color:#94A3B8!important;
  cursor:not-allowed;
  opacity:1!important;
}
.professional-profile-page :is(.thera-control, .form-control, .form-select).is-invalid {
  border-color:#DC3545!important;
  background-position:left 13px center!important;
}
.professional-profile-page input.form-control[type="file"] {
  padding:0!important;
  overflow:hidden;
  line-height:42px;
}
.professional-profile-page input.form-control[type="file"]::file-selector-button {
  height:42px;
  margin:0 0 0 12px;
  padding:0 16px;
  border:0;
  border-left:1px solid #E2E8F0;
  background:#F8FAFC;
  color:#334155;
  font-family:'Tajawal',sans-serif;
  font-weight:700;
}
.professional-profile-page .input-group > .form-control {
  width:1%!important;
  min-width:0;
  flex:1 1 auto;
  border-radius:0 12px 12px 0!important;
}
.professional-profile-page .input-group > .input-group-text {
  height:44px;
  min-height:44px;
  flex:0 0 auto;
  display:flex;
  align-items:center;
  white-space:nowrap;
  border:1px solid #CBD5E1;
  border-right:0;
  border-radius:12px 0 0 12px!important;
  background:#F8FAFC;
  color:#64748B;
  font-size:13px;
  font-weight:700;
}
.professional-profile-page input[type="number"] {
  font-family:'Inter','Tajawal',sans-serif;
}
.professional-profile-page .invalid-feedback { font-size:12px; font-weight:700; }
.professional-profile-page .select2-container {
  width:100%!important;
  max-width:100%;
  font-family:'Tajawal',sans-serif;
  text-align:right;
}
.professional-profile-page .select2-container--default .select2-selection--single {
  height:44px;
  border:1px solid #CBD5E1;
  border-radius:12px;
  background:#FFFFFF;
  transition:border-color .16s ease, box-shadow .16s ease;
}
.professional-profile-page .select2-container--default .select2-selection--single .select2-selection__rendered {
  height:42px;
  padding-right:13px;
  padding-left:44px;
  color:#334155;
  font-size:14px;
  font-weight:500;
  line-height:42px;
}
.professional-profile-page .select2-container--default .select2-selection--single .select2-selection__placeholder {
  color:#94A3B8;
}
.professional-profile-page .select2-container--default .select2-selection--single .select2-selection__arrow {
  right:auto;
  left:12px;
  width:22px;
  height:42px;
}
.professional-profile-page .select2-container--default.select2-container--focus .select2-selection--single,
.professional-profile-page .select2-container--default.select2-container--open .select2-selection--single {
  border-color:#0495F6;
  box-shadow:0 0 0 3px rgba(4,149,246,.10);
}
.professional-profile-page .select2-container--default.select2-container--disabled .select2-selection--single {
  border-color:#E2E8F0;
  background:#F1F5F9;
  cursor:not-allowed;
}
.professional-profile-page .form-select.is-invalid + .select2-container .select2-selection--single {
  border-color:#DC3545;
}
.select2-container--open { z-index:2060; }
.select2-dropdown {
  overflow:hidden;
  border:1px solid #CBD5E1;
  border-radius:12px;
  background:#FFFFFF;
  box-shadow:0 14px 30px rgba(15,23,42,.14);
  font-family:'Tajawal',sans-serif;
  direction:rtl;
}
.select2-search--dropdown { padding:10px; }
.select2-container--default .select2-search--dropdown .select2-search__field {
  height:40px;
  padding:8px 12px;
  border:1px solid #CBD5E1;
  border-radius:10px;
  outline:0;
  text-align:right;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
  border-color:#0495F6;
  box-shadow:0 0 0 3px rgba(4,149,246,.10);
}
.select2-results__options { padding:6px; }
.select2-results__option { padding:9px 11px; border-radius:8px; font-size:14px; }
.select2-container--default .select2-results__option--selected { background:#EFF6FF; color:#0466F1; }
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background:#0495F6; color:#FFFFFF; }
.specialty-primary-editor {
  border-color:#D8E8F6!important;
  background:#F8FBFF;
}
.specialty-primary-editor .btn,
.professional-profile-page .modal-footer .btn {
  min-height:44px;
  border-radius:12px;
  font-weight:700;
}
.professional-profile-page .profile-modal .modal-dialog {
  padding-inline:12px;
}
.professional-profile-page .profile-modal .modal-content {
  overflow:hidden;
  border:1px solid #E2E8F0!important;
  border-radius:18px!important;
  box-shadow:0 22px 60px rgba(15,23,42,.22)!important;
}
.professional-profile-page .profile-modal .modal-content > form {
  display:flex;
  flex-direction:column;
  min-height:0;
  max-height:calc(100vh - 48px);
}
.professional-profile-page .profile-modal .modal-header {
  position:relative;
  min-height:64px;
  padding:16px 20px!important;
  padding-inline-end:70px!important;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  border-bottom:1px solid #E2E8F0;
  background:#FFFFFF;
}
.professional-profile-page .profile-modal .modal-title {
  margin:0;
  color:#1E293B;
  line-height:1.5;
}
.professional-profile-page .profile-modal .modal-header .btn-close {
  position:absolute;
  inset-inline-end:20px;
  top:50%;
  transform:translateY(-50%);
  width:34px;
  height:34px;
  flex:0 0 34px;
  box-sizing:border-box;
  margin:0!important;
  padding:9px!important;
  background-size:12px;
  border-radius:10px;
  opacity:.55;
}
.professional-profile-page .profile-modal .modal-header .btn-close:hover,
.professional-profile-page .profile-modal .modal-header .btn-close:focus {
  background-color:#F1F5F9;
  opacity:.8;
  box-shadow:none;
}
.professional-profile-page .profile-modal .modal-body {
  padding:22px 20px 24px!important;
  overflow-y:auto;
}
.professional-profile-page .profile-modal .modal-footer {
  min-height:76px;
  padding:14px 20px 16px!important;
  display:flex;
  justify-content:flex-end;
  gap:10px;
  border-top:1px solid #E2E8F0;
  background:#F8FAFC;
}
.professional-profile-page .profile-modal .modal-footer > * {
  margin:0!important;
}
.professional-profile-page .profile-modal .modal-footer .btn {
  padding-inline:18px;
}
@media (max-width: 767.98px) {
  #main-content-layout { padding:64px 12px 20px!important; }
  .professional-profile-form .profile-card { border-radius:14px; }
  .professional-profile-form .save-section { width:100%; justify-content:center; }
  .professional-profile-page .profile-modal .modal-dialog { padding-inline:0; }
  .professional-profile-page .profile-modal .modal-content { border-radius:0!important; }
  .professional-profile-page .profile-modal .modal-body { padding:20px 16px!important; }
  .professional-profile-page .profile-modal .modal-header,
  .professional-profile-page .profile-modal .modal-footer { padding-inline:16px!important; }
  .professional-profile-page .profile-modal .modal-header { padding-inline-end:62px!important; }
  .professional-profile-page .profile-modal .modal-header .btn-close { inset-inline-end:16px; }
  .professional-profile-page .profile-modal .modal-footer .btn-primary { flex:1; }
}
@media (max-width: 991.98px) {
  #app-sidebar.offcanvas-lg {
    width:min(86vw,320px)!important;
    max-width:320px;
    z-index:1055;
  }
  #app-sidebar.sidebar-collapsed,
  #app-sidebar.sidebar-collapsed.sidebar-hovered { width:min(86vw,320px)!important; }
  #app-sidebar .sidebar-brand-text,
  #app-sidebar .nav-label,
  #app-sidebar .group-heading,
  #app-sidebar .doctor-meta { display:initial!important; }
  #app-sidebar .nav-item { justify-content:flex-start!important; padding-left:1rem!important; padding-right:1rem!important; }
  #app-sidebar .doctor-card-body { justify-content:space-between!important; padding:.375rem!important; }
  #sidebar-pin-toggle { display:none!important; }
  #top-header {
    right:0!important;
    width:100%;
    padding-inline:12px!important;
    gap:10px;
  }
  #main-content-layout { margin-right:0!important; }
  #top-header .header-search { width:min(36vw,260px); }
  #top-header .header-actions { gap:8px; }
  .profile-tabs-wrap { margin-inline:-12px; padding-inline:12px; }
}
@media (min-width: 992px) {
  #app-sidebar.offcanvas-lg {
    position:fixed!important;
    inset:0 0 0 auto!important;
    width:268px;
    height:100vh!important;
    flex-grow:0!important;
    visibility:visible!important;
    transform:none!important;
    background:#16243E!important;
    border-left:1px solid rgba(255,255,255,.08)!important;
  }
}
@media (max-width: 575.98px) {
  #top-header .header-search { display:none; }
  #top-header .header-secondary-action { display:none!important; }
  #quick-add-btn { width:40px; padding:0!important; justify-content:center; }
  #quick-add-btn .quick-add-label,
  #quick-add-arrow { display:none; }
  #quick-add-dropdown { position:fixed; top:58px; right:12px; left:12px; width:auto; }
  #main-content-layout h1 { font-size:24px; }
  #main-content-layout .profile-card { padding:16px!important; }
  #profile-onboarding-banner .onboarding-actions { width:100%; justify-content:space-between; }
  #continue-to-verification { flex:1; justify-content:center; }
}
</style>
<link rel="stylesheet" href="{{ asset('css/doctor-professional-profile.css') }}">
</head>
<body class="professional-profile-page select-none bg-[#F6F9FC] text-slate-800 antialiased min-h-screen">
@include('doctor.partials.workspace-sidebar')
@include('doctor.partials.workspace-topbar')
<main class="pt-16 p-6 min-h-screen" data-purpose="workspace-content" id="main-content-layout" style="margin-right:268px;">
<div class="max-w-[1500px] mx-auto pt-3">
<div class="mt-4 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-5">
<div>
<div class="flex items-center gap-2 text-sm text-slate-400 mb-1.5">
<span data-i18n="حساب الطبيب">حساب الطبيب</span><span class="material-symbols-outlined text-[16px]">chevron_left</span><span class="text-[#0495F6] font-bold" data-i18n="الملف المهني">الملف المهني</span>
</div>
<h1 class="text-[28px] leading-tight font-extrabold text-[#0B1020]" data-i18n="الملف المهني">الملف المهني</h1>
<p class="mt-1 text-sm text-slate-500" data-i18n="إدارة المعلومات المهنية التي تظهر في ملفك العام على THERA.">إدارة المعلومات المهنية التي تظهر في ملفك العام على THERA.</p>
</div>
</div><section class="hidden onboarding-banner mb-4" id="profile-onboarding-banner">
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
<div class="flex items-start gap-3 min-w-0">
<div class="w-11 h-11 rounded-xl bg-white border border-blue-100 text-[#0495F6] flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">person_edit</span>
</div>
<div class="min-w-0">
<div class="flex flex-wrap items-center gap-2">
<p class="font-extrabold text-[#0B1020]">إعداد حساب الطبيب</p>
<span class="onboarding-step-pill active">1 الملف المهني</span>
<span class="material-symbols-outlined text-[15px] text-slate-300">chevron_left</span>
<span class="onboarding-step-pill">2 التحقق المهني</span>
</div>
<p class="text-xs text-slate-500 mt-1.5 leading-6">أكمل البيانات المهنية الأساسية، ثم انتقل إلى التحقق لرفع المستندات وإرسال الطلب.</p>
</div>
</div>
<div class="onboarding-actions flex items-center gap-3 shrink-0">
<div class="text-left">
<p class="text-[11px] text-slate-400">اكتمال الخطوة</p>
<p class="font-num font-extrabold text-[#0495F6]" id="onboarding-profile-progress">0%</p>
</div>
<button class="h-10 px-4 rounded-lg bg-[#0495F6] hover:bg-[#0466F1] text-white text-sm font-bold flex items-center gap-2 disabled:opacity-45 disabled:cursor-not-allowed" {{ $profileCompletion < 100 ? 'disabled' : '' }} form="professional-profile-form" id="continue-to-verification" name="next" type="submit" value="1">
<span>متابعة إلى التحقق المهني</span>
<span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
</button>
</div>
</div>
</section>
<!-- Doctor profile summary -->
<section class="profile-card p-5 mb-4">
<div class="flex flex-col xl:flex-row xl:items-center gap-5">
<div class="flex items-center gap-4 min-w-0 flex-1">
<div class="relative shrink-0">
<div class="w-[78px] h-[78px] rounded-2xl bg-gradient-to-br from-[#E6F4FE] to-[#EEF2FF] border border-[#D8E8F6] flex items-center justify-center text-[#0466F1] text-2xl font-extrabold overflow-hidden" id="avatar-preview">{{ mb_substr($doctor->name, 0, 2) }}</div>
<button class="absolute -bottom-2 -left-2 w-8 h-8 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 hover:text-[#0495F6]" id="avatar-button" title="تغيير الصورة" type="button">
<span class="material-symbols-outlined text-[18px]">photo_camera</span>
</button>
<input accept="image/*" class="hidden" id="avatar-input" type="file"/>
</div>
<div class="min-w-0">
<div class="flex flex-wrap items-center gap-2 mb-1">
<h2 class="text-xl font-extrabold text-[#0B1020]" id="doctor-profile-name">{{ $doctor->name }}</h2>
<span class="status-pill {{ $doctor->verification_status === 'verified' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}" id="doctor-profile-verification-badge"><span class="material-symbols-outlined text-[16px]">{{ $doctor->verification_status === 'verified' ? 'verified' : 'pending' }}</span>{{ $doctor->verification_status === 'verified' ? 'تم التحقق' : 'لم يُرسل طلب التحقق' }}</span>
</div>
<p class="text-sm font-semibold text-slate-600">{{ $doctor->professional_title ?: 'أكمل المسمى المهني' }}</p>
<div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
<span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">medical_information</span>{{ $primarySpecialtyAssignment?->specialty?->name_ar ?? 'التخصص الرئيسي غير مضاف' }}</span>
<span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">work_history</span>{{ $doctor->years_experience !== null ? $doctor->years_experience.' سنة خبرة' : 'سنوات الخبرة غير مضافة' }}</span>
<span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">language</span>{{ filled($doctor->languages) ? implode('، ', $doctor->languages) : 'اللغات غير مضافة' }}</span>
</div>
</div>
</div>
<div class="w-full xl:w-[330px] shrink-0">
<div class="flex items-center justify-between text-sm mb-2">
<span class="font-bold text-slate-700" data-i18n="اكتمال الملف">اكتمال الملف</span><span class="font-num font-bold text-[#0495F6]" id="profile-completion-value">{{ $profileCompletion }}%</span>
</div>
<div class="progress-track"><div class="progress-fill" id="profile-completion-fill" style="width:{{ $profileCompletion }}%"></div></div>
<p class="text-xs text-slate-400 mt-2" data-i18n="أكمل المعلومات المهنية والمؤهلات لتحسين ظهور ملفك للمرضى.">أكمل المعلومات المهنية والمؤهلات لتحسين ظهور ملفك للمرضى.</p>
</div>
</div>
</section>
<!-- cross-links -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
<div class="profile-card p-4 flex items-center justify-between gap-4">
<div class="flex items-center gap-3 min-w-0">
<div class="w-10 h-10 rounded-xl bg-[#EFF6FF] text-[#0466F1] flex items-center justify-center shrink-0"><span class="material-symbols-outlined">payments</span></div>
<div class="min-w-0"><p class="font-bold text-sm text-slate-800" data-i18n="الخدمات والأسعار">الخدمات والأسعار</p><p class="text-xs text-slate-500 mt-0.5">{{ $activeServiceCount }} خدمات مفعلة • تُدار من شاشة الخدمات والأسعار</p></div>
</div>
<a class="text-xs font-bold text-[#0495F6] hover:text-[#0466F1] whitespace-nowrap" href="{{ route('doctor.practice.services') }}">إدارة الخدمات</a>
</div>
<div class="profile-card p-4 flex items-center justify-between gap-4">
<div class="flex items-center gap-3 min-w-0">
<div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0" id="verification-crosslink-icon"><span class="material-symbols-outlined">verified_user</span></div>
<div class="min-w-0"><p class="font-bold text-sm text-slate-800" data-i18n="التحقق المهني">التحقق المهني</p><p class="text-xs text-slate-500 mt-0.5" id="verification-crosslink-status">{{ $doctor->verification_status === 'verified' ? 'تم التحقق من الملف المهني • الحساب نشط' : 'لم يتم إرسال طلب التحقق بعد • أكمل الملف أولًا' }}</p></div>
</div>
<a class="text-xs font-bold text-[#0495F6] hover:text-[#0466F1] whitespace-nowrap" href="{{ route('doctor.account.verification', ['onboarding' => 1]) }}" id="verification-crosslink-button">إدارة التحقق</a>
</div>
</div>
<!-- Tabs -->
@if (session('status'))
<div class="alert alert-success flex items-center gap-2 mb-4" role="alert">
<span class="material-symbols-outlined">check_circle</span>
<span>{{ session('status') }}</span>
</div>
@endif
@if ($errors->any() || $errors->facilityMembership->any() || $errors->facilityProposal->any() || $errors->credential->any() || $errors->achievement->any())
<div class="alert alert-danger flex items-start gap-2 mb-4" role="alert">
<span class="material-symbols-outlined mt-1">error</span>
<div><strong>تعذر حفظ الملف.</strong><div class="small mt-1">راجع الحقول المطلوبة ثم حاول مجددًا.</div></div>
</div>
@endif
<div class="profile-tabs-wrap mb-4">
<div aria-label="أقسام الملف المهني" class="profile-tabs-list" role="tablist">
<button aria-controls="profile-panel-basic" aria-selected="true" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="basic" id="profile-tab-basic" role="tab" tabindex="0" type="button" data-i18n="المعلومات الأساسية">المعلومات الأساسية</button>
<button aria-controls="profile-panel-professional" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="professional" id="profile-tab-professional" role="tab" tabindex="-1" type="button" data-i18n="المعلومات المهنية">المعلومات المهنية</button>
<button aria-controls="profile-panel-specialties" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="specialties" id="profile-tab-specialties" role="tab" tabindex="-1" type="button" data-i18n="التخصصات">التخصصات</button>
<button aria-controls="profile-panel-facilities" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="facilities" id="profile-tab-facilities" role="tab" tabindex="-1" type="button" data-i18n="المنشآت">المنشآت</button>
<button aria-controls="profile-panel-credentials" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="credentials" id="profile-tab-credentials" role="tab" tabindex="-1" type="button" data-i18n="المؤهلات والشهادات">المؤهلات والشهادات</button>
<button aria-controls="profile-panel-achievements" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="achievements" id="profile-tab-achievements" role="tab" tabindex="-1" type="button" data-i18n="الإنجازات">الإنجازات</button>
<button aria-controls="profile-panel-preview" aria-selected="false" class="profile-tab h-10 px-4 rounded-full text-sm font-semibold" data-tab="preview" id="profile-tab-preview" role="tab" tabindex="-1" type="button" data-i18n="معاينة الملف العام">معاينة الملف العام</button>
</div>
</div>
<!-- BASIC -->
<form action="{{ route('doctor.account.professional-profile.update') }}" class="professional-profile-form" id="professional-profile-form" method="POST">
@csrf
@method('PUT')
<input name="onboarding" type="hidden" value="{{ request()->boolean('onboarding') ? 1 : 0 }}"/>
<section aria-labelledby="profile-tab-basic" class="profile-panel active" data-panel="basic" id="profile-panel-basic" role="tabpanel">
<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_330px] gap-4">
<div class="profile-card p-5 md:p-6">
<div class="flex items-start justify-between gap-4 mb-5">
<div><h3 class="text-lg font-extrabold text-[#0B1020]" data-i18n="المعلومات الأساسية">المعلومات الأساسية</h3><p class="text-sm text-slate-500 mt-1" data-i18n="المعلومات العامة التي تساعد المرضى على التعرف عليك.">المعلومات العامة التي تساعد المرضى على التعرف عليك.</p></div>
<span class="status-pill bg-blue-50 text-blue-700 border border-blue-200"><span class="material-symbols-outlined text-[15px]">public</span>تظهر للعامة</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="min-w-0"><label class="field-label form-label" data-onboarding-required="1" for="basic-full-name" data-i18n="الاسم الكامل">الاسم الكامل</label><input class="form-control @error('name') is-invalid @enderror" id="basic-full-name" name="name" type="text" value="{{ old('name', $doctor->name) }}"/>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="min-w-0"><label class="field-label form-label" for="basic-languages" data-i18n="اللغات">اللغات</label><input class="form-control @error('languages_text') is-invalid @enderror" id="basic-languages" name="languages_text" type="text" value="{{ old('languages_text', implode('، ', $doctor->languages ?? [])) }}"/><p class="field-help" data-i18n="افصل بين اللغات بفاصلة.">افصل بين اللغات بفاصلة.</p>@error('languages_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
<div class="md:col-span-2 min-w-0"><label class="field-label form-label" for="basic-bio" data-i18n="نبذة مختصرة">نبذة مختصرة</label><textarea class="form-control @error('bio') is-invalid @enderror" id="basic-bio" maxlength="280" name="bio">{{ old('bio', $doctor->bio) }}</textarea><div class="flex justify-between mt-2"><p class="field-help m-0" data-i18n="ستظهر هذه النبذة في بداية ملفك العام.">ستظهر هذه النبذة في بداية ملفك العام.</p><span class="text-[11px] text-slate-400 font-num" id="basic-bio-count">{{ mb_strlen(old('bio', $doctor->bio ?? '')) }} / 280</span></div>@error('bio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
</div>
<div class="mt-6 pt-4 border-t border-slate-100 flex justify-end"><button class="save-section btn btn-primary inline-flex items-center gap-2 px-4" type="submit"><span class="material-symbols-outlined text-[18px]">save</span>حفظ التغييرات</button></div>
</div>
<aside class="profile-card p-5 h-fit">
<div class="flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-[#0495F6]">info</span><h4 class="font-extrabold text-sm text-slate-800" data-i18n="ما الذي يظهر للمرضى؟">ما الذي يظهر للمرضى؟</h4></div>
<div class="space-y-0 text-sm">
<div class="summary-row py-3 flex items-center justify-between gap-3"><span class="text-slate-600" data-i18n="الصورة والاسم">الصورة والاسم</span><span class="text-emerald-600 font-bold text-xs" data-i18n="عام">عام</span></div>
<div class="summary-row py-3 flex items-center justify-between gap-3"><span class="text-slate-600" data-i18n="اللغات">اللغات</span><span class="text-emerald-600 font-bold text-xs" data-i18n="عام">عام</span></div>
<div class="summary-row py-3 flex items-center justify-between gap-3"><span class="text-slate-600">النبذة المختصرة</span><span class="text-emerald-600 font-bold text-xs" data-i18n="عام">عام</span></div>
<div class="summary-row py-3 flex items-center justify-between gap-3"><span class="text-slate-600" data-i18n="الهاتف والبريد">الهاتف والبريد</span><span class="text-slate-400 font-bold text-xs" data-i18n="إعدادات الحساب">إعدادات الحساب</span></div>
</div>
<div class="mt-4 rounded-xl bg-[#F8FAFC] border border-slate-100 p-3 text-xs leading-6 text-slate-500">المعلومات المهنية الموثقة تُدار من الأقسام المخصصة لها ولا يتم تعديلها من هنا.</div>
</aside>
</div>
</section>
<!-- PROFESSIONAL -->
<section aria-labelledby="profile-tab-professional" class="profile-panel" data-panel="professional" id="profile-panel-professional" role="tabpanel">
<div class="profile-card p-5 md:p-6">
<div class="mb-5"><h3 class="text-lg font-extrabold text-[#0B1020]" data-i18n="المعلومات المهنية">المعلومات المهنية</h3><p class="text-sm text-slate-500 mt-1">البيانات المهنية الأساسية المعروضة ضمن ملفك العام.</p></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="min-w-0"><label class="field-label form-label" data-onboarding-required="1" for="professional-title" data-i18n="المسمى المهني">المسمى المهني</label><input class="form-control @error('professional_title') is-invalid @enderror" id="professional-title" name="professional_title" type="text" value="{{ old('professional_title', $doctor->professional_title) }}"/>@error('professional_title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="min-w-0"><label class="field-label form-label" data-onboarding-required="1" for="professional-experience" data-i18n="سنوات الخبرة">سنوات الخبرة</label><div class="relative profile-experience"><input class="form-control font-num @error('years_experience') is-invalid @enderror" id="professional-experience" max="80" min="0" name="years_experience" type="number" value="{{ old('years_experience', $doctor->years_experience) }}"/><span class="profile-experience-unit">سنة</span>@error('years_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
<div class="md:col-span-2 min-w-0"><label class="field-label form-label" data-onboarding-required="1" for="professional-bio" data-i18n="النبذة المهنية">النبذة المهنية</label><textarea class="form-control @error('professional_bio') is-invalid @enderror" id="professional-bio" maxlength="2000" name="professional_bio">{{ old('professional_bio', $doctor->professional_bio) }}</textarea>@error('professional_bio')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-5">
<div class="rounded-2xl border border-slate-200 p-4 flex items-center justify-between"><div><p class="text-xs text-slate-400" data-i18n="التخصص الرئيسي">التخصص الرئيسي</p><p class="font-bold text-slate-800 mt-1">{{ $primarySpecialtyAssignment?->specialty?->name_ar ?? 'لم يُحدد بعد' }}</p><p class="text-xs text-slate-500 mt-1">{{ $subspecialtyAssignments->count() }} تخصصات فرعية</p></div><button class="text-sm font-bold text-[#0495F6]" data-jump="specialties" type="button">إدارة التخصصات</button></div>
<div class="rounded-2xl border border-slate-200 p-4 flex items-center justify-between"><div><p class="text-xs text-slate-400" data-i18n="التحقق المهني">التحقق المهني</p><p class="font-bold {{ $doctor->verification_status === 'verified' ? 'text-emerald-700' : 'text-amber-700' }} mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[17px]">{{ $doctor->verification_status === 'verified' ? 'verified' : 'pending' }}</span>{{ $doctor->verification_status === 'verified' ? 'تم التحقق' : 'لم يُرسل الطلب' }}</p><p class="text-xs text-slate-500 mt-1">{{ $doctor->profile_completed_at ? 'تم إكمال الملف الأساسي' : 'الملف الأساسي غير مكتمل' }}</p></div><a class="text-sm font-bold text-[#0495F6]" href="{{ route('doctor.account.verification', ['onboarding' => 1]) }}">إدارة التحقق</a></div>
</div>
<div class="mt-6 pt-4 border-t border-slate-100 flex justify-end"><button class="save-section btn btn-primary inline-flex items-center gap-2 px-4" type="submit"><span class="material-symbols-outlined text-[18px]">save</span>حفظ التغييرات</button></div>
</div>
</section>
</form>
<!-- SPECIALTIES -->
<section aria-labelledby="profile-tab-specialties" class="profile-panel" data-panel="specialties" id="profile-panel-specialties" role="tabpanel">
<div class="profile-card p-5 md:p-6">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5"><div><h3 class="text-lg font-extrabold text-[#0B1020]" data-i18n="التخصصات">التخصصات</h3><p class="text-sm text-slate-500 mt-1">التخصص الرئيسي والتخصصات الفرعية المسجلة في THERA.</p></div>@if ($primarySpecialtyAssignment)<button class="btn btn-primary inline-flex items-center justify-center gap-2" data-bs-target="#add-subspecialty-modal" data-bs-toggle="modal" type="button" @disabled($availableSubspecialties->isEmpty() || $subspecialtyAssignments->count() >= 5)><span class="material-symbols-outlined text-[18px]">add</span>إضافة تخصص فرعي</button>@endif</div>


@if (! $primarySpecialtyAssignment)
<div class="mb-5"><button class="btn btn-primary" data-bs-target="#primary-specialty-modal" data-bs-toggle="modal" type="button">اختيار التخصص الرئيسي</button></div>
@endif
<div class="space-y-3" id="specialty-list">
@if ($primarySpecialtyAssignment)
@php($primaryStatus = match ($primarySpecialtyAssignment->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'verified'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200', 'cancel'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200', 'pending'] })
<div class="specialty-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-3"><div class="flex items-center gap-3"><div class="w-11 h-11 rounded-xl bg-blue-50 text-[#0495F6] flex items-center justify-center shrink-0"><span class="material-symbols-outlined">medical_information</span></div><div><div class="flex flex-wrap items-center gap-2"><p class="font-extrabold text-slate-800 mb-0">{{ $primarySpecialtyAssignment->specialty->name_ar }}</p><span class="status-pill bg-slate-100 text-slate-600 h-6 text-[11px]">رئيسي</span></div><p class="text-xs text-slate-500 mt-1 mb-0">التخصص الأساسي الظاهر في نتائج البحث والملف العام.</p>@if ($primarySpecialtyAssignment->verification_status !== 'verified')<button class="text-xs font-bold text-[#0495F6] mt-2" data-bs-target="#primary-specialty-modal" data-bs-toggle="modal" type="button">تغيير التخصص الرئيسي</button>@endif</div></div><span class="status-pill {{ $primaryStatus[1] }}"><span class="material-symbols-outlined text-[15px]">{{ $primaryStatus[2] }}</span>{{ $primaryStatus[0] }}</span></div>
@else
<div class="text-center border border-dashed rounded-3 p-4 p-md-5"><span class="material-symbols-outlined text-primary fs-1">medical_information</span><p class="fw-bold text-dark mt-2 mb-1">لم تضف تخصصك الرئيسي بعد</p><p class="small text-secondary mb-0">اختيار التخصص الرئيسي مطلوب لإكمال الملف وإظهار التخصصات الفرعية المناسبة.</p></div>
@endif

@foreach ($subspecialtyAssignments as $assignment)
@php($status = match ($assignment->verification_status) { 'verified' => ['موثّق', 'bg-emerald-50 text-emerald-700 border border-emerald-200'], 'rejected' => ['مرفوض', 'bg-red-50 text-red-700 border border-red-200'], default => ['قيد المراجعة', 'bg-amber-50 text-amber-700 border border-amber-200'] })
<div class="specialty-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-3"><div><p class="font-bold text-slate-800 mb-1">{{ $assignment->specialty->name_ar }}</p><p class="text-xs text-slate-500 mb-0">تخصص فرعي</p></div><div class="flex flex-wrap items-center gap-2"><span class="status-pill {{ $status[1] }}">{{ $status[0] }}</span>@if ($assignment->verification_status !== 'verified')<form action="{{ route('doctor.account.professional-profile.specialties.destroy', $assignment) }}" method="POST" data-thera-confirm="هل تريد حذف هذا التخصص الفرعي؟" data-thera-confirm-button="نعم، متابعة" data-thera-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-i18n="حذف">حذف</button></form>@endif</div></div>
@endforeach
</div>
</div>

<div aria-hidden="true" aria-labelledby="primary-specialty-title" class="modal fade profile-modal" id="primary-specialty-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content"><form action="{{ route('doctor.account.professional-profile.specialties.store', ['tab' => 'specialties']) }}" class="specialty-primary-editor" method="POST">
@csrf
<input name="type" type="hidden" value="primary"/>
<div class="modal-header"><h4 class="modal-title fs-6 fw-bold" id="primary-specialty-title">التخصص الرئيسي</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body">
<div class="min-w-0">
<label class="form-label fw-bold small" for="primary-specialty">{{ $primarySpecialtyAssignment ? 'تغيير التخصص الرئيسي' : 'التخصص الرئيسي' }}</label>
<select class="form-select @error('specialty_id') is-invalid @enderror" data-current-value="{{ $primarySpecialtyAssignment?->specialty_id }}" id="primary-specialty" name="specialty_id" required @disabled($primarySpecialtyAssignment?->verification_status === 'verified')>
<option value="">اختر التخصص الرئيسي</option>
@foreach ($primarySpecialties as $specialty)
<option value="{{ $specialty->id }}" @selected((int) old('specialty_id', $primarySpecialtyAssignment?->specialty_id) === $specialty->id)>{{ $specialty->name_ar }}</option>
@endforeach
</select>
@error('specialty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if ($primarySpecialtyAssignment?->verification_status === 'verified')<p class="small text-success fw-semibold mt-3 mb-0"><span class="material-symbols-outlined align-middle fs-6">verified</span> التخصص الرئيسي موثّق ولا يمكن تغييره من هذه الصفحة.</p>@elseif ($primarySpecialtyAssignment)<p class="small text-secondary mt-3 mb-0">تغيير التخصص الرئيسي سيزيل التخصصات الفرعية الحالية لأنها مرتبطة به.</p>@endif
</div><div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">إلغاء</button><button class="btn btn-primary" id="save-primary-specialty" type="submit" @disabled($primarySpecialtyAssignment?->verification_status === 'verified')>{{ $primarySpecialtyAssignment ? 'حفظ التخصص الجديد' : 'حفظ التخصص الرئيسي' }}</button></div></form></div></div></div>
<div aria-hidden="true" aria-labelledby="add-subspecialty-title" class="modal fade profile-modal" id="add-subspecialty-modal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content"><form action="{{ route('doctor.account.professional-profile.specialties.store', ['tab' => 'specialties']) }}" method="POST">@csrf<input name="type" type="hidden" value="subspecialty"/><div class="modal-header"><h4 class="modal-title fs-6 fw-bold" id="add-subspecialty-title" data-i18n="إضافة تخصص فرعي">إضافة تخصص فرعي</h4><button aria-label="إغلاق" class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body"><label class="form-label fw-bold small" for="subspecialty">التخصص الفرعي</label><select class="form-select" id="subspecialty" name="specialty_id" required><option value="">اختر التخصص الفرعي</option>@foreach ($availableSubspecialties as $specialty)<option value="{{ $specialty->id }}">{{ $specialty->name_ar }}</option>@endforeach</select><p class="form-text mb-0">تظهر هنا فقط التخصصات التابعة لتخصصك الرئيسي.</p></div><div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button" data-i18n="إلغاء">إلغاء</button><button class="btn btn-primary" type="submit" data-i18n="حفظ وإرسال للمراجعة">حفظ وإرسال للمراجعة</button></div></form></div></div>
</div>
</section>
<!-- FACILITIES -->
@include('doctor.account.professional-profile.partials.facilities')
<!-- CREDENTIALS -->
@include('doctor.account.professional-profile.partials.credentials')
<!-- ACHIEVEMENTS -->
@include('doctor.account.professional-profile.partials.achievements')
<!-- PUBLIC PREVIEW -->
@include('doctor.account.professional-profile.partials.public-preview')
</div>
</main>
<div aria-hidden="true" class="fixed inset-0 z-[200] hidden" id="demo-modal">
<div class="absolute inset-0 bg-slate-950/45 backdrop-blur-[1px]" data-modal-close=""></div>
<div class="absolute inset-0 flex items-center justify-center p-3 md:p-6 pointer-events-none">
<section aria-labelledby="demo-modal-title" aria-modal="true" class="pointer-events-auto w-full max-w-3xl max-h-[90vh] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" role="dialog">
<header class="h-16 px-5 md:px-6 flex items-center justify-between border-b border-slate-200 bg-white shrink-0">
<div class="min-w-0">
<h3 class="font-extrabold text-lg text-[#0B1020] truncate" id="demo-modal-title">التفاصيل</h3>
<p class="text-xs text-slate-400 mt-0.5" id="demo-modal-subtitle">معاينة تفاعلية داخل النموذج</p>
</div>
<button class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-500 flex items-center justify-center" data-modal-close="" title="إغلاق" type="button">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</header>
<div class="p-5 md:p-6 overflow-y-auto" id="demo-modal-body"></div>
<footer class="px-5 md:px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-end gap-2 shrink-0" id="demo-modal-footer">
<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-bold" data-modal-close="" type="button" data-i18n="إغلاق">إغلاق</button>
</footer>
</section>
</div>
</div>
<div class="fixed bottom-6 left-6 z-[220] flex flex-col gap-2 pointer-events-none" id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<script>
(() => {
  'use strict';
  const $ = (id) => document.getElementById(id);

  document.querySelectorAll('[data-profile-upload]').forEach(input => {
    input.addEventListener('change', () => {
      input.closest('.profile-upload').querySelector('.profile-upload-name').textContent = input.files?.[0]?.name || 'اختيار PDF / JPG / PNG';
    });
  });

  function initializeSelect2(select) {
    if (!window.jQuery?.fn?.select2 || select.dataset.select2Initialized === 'true') return;
    const modal = select.closest('.modal');
    const emptyOption = [...select.options].find(option => option.value === '');
    const configuration = {
      width: '100%',
      dir: 'rtl',
      language: 'ar',
      placeholder: emptyOption?.textContent?.trim() || 'اختر من القائمة',
      dropdownParent: modal ? window.jQuery(modal) : window.jQuery(document.body),
    };

    window.jQuery(select).select2(configuration);
    select.dataset.select2Initialized = 'true';
  }

  document.querySelectorAll('.professional-profile-page select.form-select:not(.modal select)').forEach(initializeSelect2);
  document.querySelectorAll('.profile-modal').forEach(modal => {
    modal.addEventListener('shown.bs.modal', () => modal.querySelectorAll('select.form-select').forEach(initializeSelect2));
  });

  const primarySpecialtySelect = $('primary-specialty');
  const savePrimarySpecialtyButton = $('save-primary-specialty');
  function syncPrimarySpecialtyButton() {
    if (!primarySpecialtySelect || !savePrimarySpecialtyButton) return;
    const currentValue = primarySpecialtySelect.dataset.currentValue || '';
    savePrimarySpecialtyButton.disabled = primarySpecialtySelect.disabled
      || primarySpecialtySelect.value === ''
      || primarySpecialtySelect.value === currentValue;
  }
  if (primarySpecialtySelect) {
    primarySpecialtySelect.addEventListener('change', syncPrimarySpecialtyButton);
    window.jQuery?.(primarySpecialtySelect).on('select2:select select2:clear', syncPrimarySpecialtyButton);
    syncPrimarySpecialtyButton();
  }

  const profileTabButtons = [...document.querySelectorAll('.profile-tab')];
  const profileTabPanels = [...document.querySelectorAll('.profile-panel')];
  const profileTabsWrap = document.querySelector('.profile-tabs-wrap');

  function activateTab(name, options = {}) {
    const activeButton = profileTabButtons.find(btn => btn.dataset.tab === name);
    const activePanel = profileTabPanels.find(panel => panel.dataset.panel === name);
    if (!activeButton || !activePanel) return;

    profileTabButtons.forEach(btn => {
      const isActive = btn === activeButton;
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      btn.tabIndex = isActive ? 0 : -1;
    });
    profileTabPanels.forEach(panel => {
      const isActive = panel === activePanel;
      panel.classList.toggle('active', isActive);
      panel.hidden = !isActive;
    });

    if (options.historyMode) {
      const url = new URL(window.location.href);
      url.searchParams.set('tab', name);
      window.history[`${options.historyMode}State`]({ profileTab: name }, '', url);
    }

    if (options.focus) activeButton.focus({ preventScroll: true });
    if (options.scroll && profileTabsWrap) {
      window.scrollTo({ top: Math.max(0, profileTabsWrap.offsetTop - 76), behavior: 'smooth' });
    }
  }

  profileTabButtons.forEach((btn, index) => {
    btn.addEventListener('click', () => activateTab(btn.dataset.tab, { historyMode: 'push', scroll: true }));
    btn.addEventListener('keydown', event => {
      let nextIndex = null;
      if (event.key === 'Home') nextIndex = 0;
      if (event.key === 'End') nextIndex = profileTabButtons.length - 1;
      if (event.key === 'ArrowLeft') nextIndex = (index + 1) % profileTabButtons.length;
      if (event.key === 'ArrowRight') nextIndex = (index - 1 + profileTabButtons.length) % profileTabButtons.length;
      if (nextIndex === null) return;
      event.preventDefault();
      const nextButton = profileTabButtons[nextIndex];
      activateTab(nextButton.dataset.tab, { historyMode: 'push', focus: true, scroll: true });
    });
  });
  document.querySelectorAll('[data-jump]').forEach(btn => btn.addEventListener('click', () => activateTab(btn.dataset.jump, { historyMode: 'push', scroll: true })));
  const requestedTab = @json(request('tab'));
  if (['basic', 'professional', 'specialties', 'facilities', 'credentials', 'achievements', 'preview'].includes(requestedTab)) {
    activateTab(requestedTab);
  }
  window.addEventListener('popstate', () => {
    const tabFromUrl = new URL(window.location.href).searchParams.get('tab') || 'basic';
    activateTab(tabFromUrl);
  });
  @if ($errors->credential->any())
  activateTab('credentials', { historyMode: 'replace' });
  window.addEventListener('DOMContentLoaded', () => window.bootstrap?.Modal.getOrCreateInstance($('add-credential-modal')).show());
  @elseif ($errors->achievement->any())
  activateTab('achievements', { historyMode: 'replace' });
  window.addEventListener('DOMContentLoaded', () => window.bootstrap?.Modal.getOrCreateInstance($('add-achievement-modal')).show());
  @elseif ($errors->facilityMembership->any())
  activateTab('facilities', { historyMode: 'replace' });
  window.addEventListener('DOMContentLoaded', () => window.bootstrap?.Modal.getOrCreateInstance($('join-facility-modal')).show());
  @elseif ($errors->facilityProposal->any())
  activateTab('facilities', { historyMode: 'replace' });
  window.addEventListener('DOMContentLoaded', () => window.bootstrap?.Modal.getOrCreateInstance($('propose-facility-modal')).show());
  @elseif ($errors->hasAny(['specialty_id', 'type']))
  activateTab('specialties', { historyMode: 'replace' });
  window.addEventListener('DOMContentLoaded', () => window.bootstrap?.Modal.getOrCreateInstance($(@json(old('type') === 'subspecialty' ? 'add-subspecialty-modal' : 'primary-specialty-modal'))).show());
  @elseif ($errors->hasAny(['professional_title', 'years_experience', 'professional_bio']))
  activateTab('professional', { historyMode: 'replace' });
  @endif

  function toast(msg) {
    const container = $('toast-container');
    if (!container) return;
    const el = document.createElement('div');
    el.className = 'pointer-events-auto min-w-[260px] max-w-[360px] bg-[#0B1020] text-white rounded-xl shadow-lg px-4 py-3 flex items-center gap-2 text-sm font-bold translate-y-2 opacity-0 transition-all';
    el.innerHTML = '<span class="material-symbols-outlined text-emerald-400 text-[19px]">check_circle</span><span>'+msg+'</span>';
    container.appendChild(el);
    requestAnimationFrame(() => { el.classList.remove('translate-y-2','opacity-0'); });
    setTimeout(() => { el.classList.add('translate-y-2','opacity-0'); setTimeout(() => el.remove(), 220); }, 2200);
  }
  const avatarButton = $('avatar-button');
  const avatarInput = $('avatar-input');
  const avatarPreview = $('avatar-preview');
  if (avatarButton && avatarInput) avatarButton.addEventListener('click', () => avatarInput.click());
  if (avatarInput && avatarPreview) avatarInput.addEventListener('change', () => {
    const f = avatarInput.files && avatarInput.files[0]; if (!f) return;
    const reader = new FileReader();
    reader.onload = e => { avatarPreview.innerHTML = '<img src="'+e.target.result+'" class="w-full h-full object-cover" alt="صورة الطبيب">'; toast('تم تحديث معاينة الصورة'); };
    reader.readAsDataURL(f);
  });


  // Interactive prototype modals and demo actions
  const modal = $('demo-modal');
  const modalTitle = $('demo-modal-title');
  const modalSubtitle = $('demo-modal-subtitle');
  const modalBody = $('demo-modal-body');
  const modalFooter = $('demo-modal-footer');

  function openModal(title, body, options = {}) {
    if (!modal || !modalBody || !modalTitle || !modalFooter) return;
    modalTitle.textContent = title;
    modalSubtitle.textContent = options.subtitle || 'معاينة تفاعلية داخل النموذج';
    modalBody.innerHTML = body;
    modalFooter.innerHTML = options.footer || '<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-bold" type="button" data-modal-close>إغلاق</button>';
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    bindModalClose();
    if (typeof options.onOpen === 'function') options.onOpen();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  function bindModalClose() {
    if (!modal) return;
    modal.querySelectorAll('[data-modal-close]').forEach(el => el.onclick = closeModal);
  }
  if (modal) modal.querySelectorAll('[data-modal-close]').forEach(el => el.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal(); });

  const infoRow = (label, value) => '<div class="py-3 flex items-start justify-between gap-4 border-b border-slate-100 last:border-0"><span class="text-sm text-slate-500">'+label+'</span><span class="text-sm font-bold text-slate-800 text-left">'+value+'</span></div>';
  const demoNote = '<div class="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-xs leading-6 text-blue-700 flex items-start gap-2"><span class="material-symbols-outlined text-[18px] mt-0.5">info</span><span>هذه بيانات تجريبية داخل نموذج التصميم حتى تتمكن من مراجعة التفاصيل والتدفق قبل ربط الـBackend.</span></div>';

  document.querySelectorAll('[data-demo-action="manage-services"]').forEach(btn => btn.addEventListener('click', () => {
    openModal('الخدمات والأسعار', `
      ${demoNote}
      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        ${[
          ['استشارة طب باطني','30 دقيقة','30 د.أ','متاحة في مركز الشفاء'],
          ['متابعة طبية','20 دقيقة','20 د.أ','متاحة في مركز الشفاء'],
          ['استشارة أمراض مزمنة','40 دقيقة','40 د.أ','متاحة في مركز الشفاء'],
          ['متابعة نتائج وفحوصات','20 دقيقة','25 د.أ','متاحة في مستشفى المدينة']
        ].map(s => `<div class="rounded-2xl border border-slate-200 p-4"><div class="flex items-center justify-between gap-3"><p class="font-extrabold text-slate-800">${s[0]}</p><span class="status-pill bg-emerald-50 text-emerald-700 h-6">نشطة</span></div><div class="mt-3 text-xs text-slate-500 space-y-1"><p>${s[1]} • ${s[2]}</p><p>${s[3]}</p></div></div>`).join('')}
      </div>
      <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-600">إدارة السعر، مدة الخدمة وسياسة الدفع تتم من شاشة <b>الخدمات والأسعار</b> وليس من الملف المهني. الملف المهني يعرض فقط المعلومات المناسبة للظهور العام.</div>
    `, {footer:'<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-bold" type="button" data-modal-close>إغلاق</button><button class="h-10 px-4 rounded-xl bg-[#0495F6] text-white text-sm font-bold" type="button" id="demo-go-services">فتح شاشة الخدمات والأسعار</button>', onOpen:()=>{ const b=$('demo-go-services'); if(b) b.onclick=()=>toast('في النسخة الكاملة سيتم الانتقال إلى شاشة الخدمات والأسعار'); }});
  }));

  document.querySelectorAll('[data-demo-action="manage-verification"]').forEach(btn => btn.addEventListener('click', () => {
    openModal('ملخص التحقق المهني', `
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-start gap-3"><span class="material-symbols-outlined text-emerald-600">verified_user</span><div><p class="font-extrabold text-emerald-800">تم التحقق من الطبيب</p><p class="text-xs text-emerald-700 mt-1">آخر قرار تحقق: 14 سبتمبر 2026</p></div></div>
      <div class="mt-4 rounded-2xl border border-slate-200 px-4">
        ${infoRow('حالة التحقق','تم التحقق')}
        ${infoRow('حالة الحساب','نشط')}
        ${infoRow('المستندات المطلوبة','5 / 5 مكتملة')}
        ${infoRow('المؤهلات الموثقة','1')}
        ${infoRow('عناصر قيد المراجعة','2')}
      </div>
      <div class="mt-4">${demoNote}</div>
    `, {footer:'<button class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-bold" type="button" data-modal-close>إغلاق</button><button class="h-10 px-4 rounded-xl bg-[#0495F6] text-white text-sm font-bold" type="button" id="demo-open-verification">إدارة التحقق</button>', onOpen:()=>{ const b=$('demo-open-verification'); if(b) b.onclick=()=>toast('سيتم ربطها بصفحة Doctor Verification'); }});
  }));

})();
</script><script id="thera-profile-onboarding-runtime">
(() => {
  'use strict';
  const params = new URLSearchParams(window.location.search);
  if (params.get('onboarding') !== '1') return;

  document.body.classList.add('onboarding-profile-mode');
  const $ = id => document.getElementById(id);
  $('profile-onboarding-banner')?.classList.remove('hidden');

  const badge = $('doctor-profile-verification-badge');
  if (badge) {
    badge.className = 'status-pill bg-amber-50 text-amber-700 border border-amber-200';
    badge.innerHTML = '<span class="material-symbols-outlined text-[16px]">pending</span>لم يُرسل طلب التحقق';
  }

  const crossStatus = $('verification-crosslink-status');
  if (crossStatus) crossStatus.textContent = 'لم يتم إرسال طلب التحقق بعد • الحالة: مسجل';

  const crossIcon = $('verification-crosslink-icon');
  if (crossIcon) {
    crossIcon.className = 'w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0';
  }

  const emptyState = (icon, title, text) => {
    const div = document.createElement('div');
    div.className = 'onboarding-empty';
    div.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-extrabold text-slate-800">${title}</p><p class="text-xs text-slate-500 mt-1.5 leading-6">${text}</p>`;
    return div;
  };

  const requiredIds = ['basic-full-name','professional-title','professional-experience','professional-bio'];
  const hasPrimarySpecialty = @json((bool) $primarySpecialtyAssignment);

  function profileCompletion() {
    let completed = 0;
    requiredIds.forEach(id => {
      const el = $(id);
      const value = (el?.value || '').trim();
      if (id === 'professional-experience') {
        if (value !== '' && Number(value) >= 0) completed++;
      } else if (value) {
        completed++;
      }
    });

    if (hasPrimarySpecialty) completed++;

    const percent = Math.round((completed / (requiredIds.length + 1)) * 100);

    if ($('onboarding-profile-progress')) $('onboarding-profile-progress').textContent = `${percent}%`;
    if ($('profile-completion-value')) $('profile-completion-value').textContent = `${percent}%`;
    if ($('profile-completion-fill')) $('profile-completion-fill').style.width = `${percent}%`;

    const cta = $('continue-to-verification');
    if (cta) cta.disabled = percent < 100;

    return percent;
  }

  requiredIds.forEach(id => $(id)?.addEventListener('input', profileCompletion));

  profileCompletion();
})();
</script>@include('doctor.partials.route-map')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/doctor.js') }}" defer></script>
</body>
</html>
