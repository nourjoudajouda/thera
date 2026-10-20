@once
<style>
    .swal2-container.thera-alert-container { z-index: 20000; }
    .thera-alert-popup { direction: rtl; font-family: 'Tajawal', sans-serif; border-radius: 22px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 24px 70px rgba(19,32,56,.18); }
    .thera-alert-title { font-size: 22px; font-weight: 800; color: #132038; padding: 8px 12px 0; line-height: 1.5; }
    .thera-alert-text { font-size: 14px; color: #64748b; line-height: 1.9; margin: 12px 8px 0; }
    .thera-alert-actions { gap: 10px; margin-top: 24px; width: 100%; }
    .thera-alert-button { font-family: inherit; min-height: 44px; padding: 10px 24px; border-radius: 12px; font-size: 14px; font-weight: 800; cursor: pointer; border: 1px solid transparent; transition: background .15s; }
    .thera-alert-confirm { background: #0495f6; color: white; }
    .thera-alert-confirm:hover { background: #0466f1; }
    .thera-alert-cancel { background: #f8fafc; color: #475569; border-color: #dce4ec; }
    .thera-alert-cancel:hover { background: #edf2f7; }
    .thera-alert-button:focus-visible { outline: 3px solid #7dd3fc; outline-offset: 3px; }
    .thera-alert-popup .swal2-icon { margin-top: 8px; }
    @media (max-width: 575px) { .thera-alert-popup { padding: 20px 16px; } .thera-alert-title { font-size: 20px; } }
</style>
<script>
    window.theraAlertMessages = {{ Illuminate\Support\Js::from([
        'status' => session('status'),
        'errors' => collect($errors->getBags())->flatMap(fn ($bag) => $bag->all())->unique()->values()->all(),
    ]) }};
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<script src="{{ asset('js/thera-alerts.js') }}" defer></script>
@endonce
