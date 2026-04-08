@extends('beneficiary-portal.layout')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="bp-header">
    <h1><i class="fas fa-sign-in-alt" style="color: var(--bp-primary);"></i> تسجيل الدخول - بوابة المستفيدين</h1>
    <p>أدخل بريدك الإلكتروني وكلمة المرور للوصول لسجلك</p>
</div>
<div class="bp-nav">
    <a href="{{ route('beneficiary-portal.index') }}"><i class="fas fa-home"></i> الرئيسية</a>
    <a href="{{ route('beneficiary-portal.register') }}"><i class="fas fa-user-plus"></i> تسجيل جديد</a>
</div>
<form method="POST" action="{{ route('login.post') }}" class="bp-form">
    @csrf
    <input type="hidden" name="redirect_beneficiary" value="1">
    <div class="form-group">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="form-group">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
        <button type="submit" class="bp-btn bp-btn-primary"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</button>
    </div>
</form>
@endsection
