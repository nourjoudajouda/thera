@extends('admin.layouts.app')
@section('title','تفويضات الأسرة')
@section('content')
<h1>تفويضات الأسرة وإثبات الولاية</h1><p>تحقق من هوية المستفيد وإثبات الولاية ونطاقها قبل الاعتماد. اعتماد العلاقة يتيح إدارة الملف والحجز وقراءة السجل ومشاركته ضمن التفويض.</p>
@foreach($records as $record)
<section class="page-card p-4 mb-3"><h2>{{ $record->patient->name }} — {{ $record->patient->reference() }}</h2><p>تاريخ الميلاد: {{ $record->patient->birth_date?->format('Y-m-d') }} · مقدم الطلب: {{ $record->user->name }} · العلاقة: {{ $record->relationship }} · الحالة: {{ $record->status }}</p>
@if($record->proof_path)<a href="{{ route('admin.patient-delegations.proof',$record) }}" target="_blank" rel="noopener">عرض إثبات الولاية</a>@endif
<form method="post" action="{{ route('admin.patient-delegations.update',$record) }}">@csrf @method('PATCH')<label>سبب القرار<input class="form-control" name="reason" required maxlength="500"></label><label>انتهاء التفويض<input class="form-control" type="date" name="expires_at" value="{{ now()->addDays(30)->format('Y-m-d') }}"></label><div class="mt-2">@if($record->status==='pending')<button class="btn btn-primary" name="action" value="approve">اعتماد الإثبات والتفويض</button><button class="btn btn-outline-danger" name="action" value="reject">رفض</button>@endif<button class="btn btn-outline-secondary" name="action" value="revoke">سحب التفويض</button></div></form></section>
@endforeach
{{ $records->links() }}
@endsection
