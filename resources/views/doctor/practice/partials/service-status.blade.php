@if($service->isBookingEnabled())
    <span class="service-status bg-success-subtle text-success"><span class="material-symbols-outlined fs-6">check_circle</span>مفعلة</span>
@elseif($service->is_active)
    <span class="service-status bg-warning-subtle text-warning-emphasis">غير مؤهلة للحجز</span>
@else
    <span class="service-status bg-secondary-subtle text-secondary">متوقفة</span>
@endif
