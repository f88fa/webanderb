@extends('beneficiary-portal.layout')

@section('title', 'تم رفض الطلب')

@section('content')
<div class="bp-header">
    <h1><i class="fas fa-times-circle" style="color: #dc3545;"></i> تم رفض طلب التسجيل</h1>
    <p>نأسف، لم يتم الموافقة على طلب التسجيل في الوقت الحالي.</p>
</div>
@if($rejectedRequest && $rejectedRequest->rejection_reason)
<div class="bp-alert bp-alert-error" style="margin-bottom: 1.5rem;">
    <strong>سبب الرفض:</strong><br>{{ $rejectedRequest->rejection_reason }}
</div>
@endif
<p>يمكنك التواصل مع الإدارة للاستفسار، أو تقديم طلب جديد لاحقاً.</p>
<form method="POST" action="{{ route('logout') }}" style="margin-top: 1.5rem;">
    @csrf
    <button type="submit" class="bp-btn bp-btn-secondary"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
</form>
@endsection
