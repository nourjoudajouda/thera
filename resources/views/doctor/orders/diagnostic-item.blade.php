<article class="diagnostic-item mb-3" data-diagnostic-item>
    <header class="d-flex justify-content-between align-items-center gap-3 mb-3"><h3 class="h6 fw-bold mb-0">{{ $order->kind === 'lab' ? 'الفحص المطلوب' : 'التصوير المطلوب' }}</h3>@unless($readonly)<button type="button" class="btn btn-sm btn-outline-danger" data-diagnostic-remove aria-label="حذف الفحص من المسودة" data-i18n="حذف">حذف</button>@endunless</header>
    <div class="row g-3">
        <div class="col-12 {{ $order->kind === 'radiology' ? 'col-md-6' : '' }}"><label class="form-label" for="diagnostic-{{ $index }}-name">{{ $order->kind === 'lab' ? 'اسم الفحص' : 'نوع التصوير / اسم الفحص' }}</label><input class="form-control" type="text" id="diagnostic-{{ $index }}-name" name="items[{{ $index }}][name]" maxlength="200" required autocomplete="off" value="{{ $item['name'] ?? '' }}"></div>
        @if($order->kind === 'radiology')<div class="col-12 col-md-6"><label class="form-label" for="diagnostic-{{ $index }}-area">المنطقة / العضو</label><input class="form-control" type="text" id="diagnostic-{{ $index }}-area" name="items[{{ $index }}][body_area]" maxlength="200" autocomplete="off" value="{{ $item['body_area'] ?? '' }}"></div>@endif
        <div class="col-12"><label class="form-label" for="diagnostic-{{ $index }}-instructions">تعليمات خاصة بهذا الفحص</label><textarea class="form-control" rows="2" id="diagnostic-{{ $index }}-instructions" name="items[{{ $index }}][instructions]" maxlength="1000">{{ $item['instructions'] ?? '' }}</textarea></div>
    </div>
</article>
