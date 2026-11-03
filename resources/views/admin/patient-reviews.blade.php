@extends('admin.layouts.app')
@section('title','مراجعة تقييمات المرضى')
@section('content')
<h1>مراجعة تقييمات المرضى</h1><p>راجع المحتوى قبل نشره، بما يشمل المعلومات الشخصية أو السريرية التي قد يذكرها المريض.</p>
@foreach($records as $record)<section class="page-card p-4 mb-3"><h2>{{ $record->doctor->name }}</h2><p>{{ $record->rating }} / 5 · {{ $record->status }}</p><p>{{ $record->comment }}</p><form method="post" action="{{ route('admin.patient-reviews.update',$record) }}">@csrf @method('PATCH')<input class="form-control mb-2" name="reason" placeholder="سبب القرار" required maxlength="500"><button class="btn btn-primary" name="status" value="approved">اعتماد للنشر</button><button class="btn btn-outline-danger" name="status" value="rejected">رفض</button></form></section>@endforeach{{ $records->links() }}
@endsection
