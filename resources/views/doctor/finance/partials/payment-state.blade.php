@if(in_array($item->status,['cancelled','no_show']))
<span class="appointment-status status-{{ $item->status }}">{{ App\Models\DoctorAppointment::STATUSES[$item->status] }}</span>@if($item->paidMinor()>0)<small class="d-block text-warning-emphasis mt-1">له رصيد محفوظ — يلزم مراجعة مالية</small>@endif
@else
<span class="appointment-status payment-{{ $item->paymentStatus() }}">{{ ['paid'=>'مسدد بالكامل','partial'=>'مدفوع جزئيًا','unpaid'=>'غير مدفوع'][$item->paymentStatus()] }}</span>
@endif
