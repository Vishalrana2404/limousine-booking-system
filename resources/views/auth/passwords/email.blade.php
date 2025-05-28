@extends('auth.layout')
@section('content')
<div class="card-body login-card-body">
    <p class="login-box-msg dark-color">Enter your email address to reset your password</p>
    <form id="forgotPasswordForm" method="POST" action="{{ route('forget_password.email') }}">
        @csrf
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif
        <div class="input-group mb-3">
            <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <button type="submit" id="forgotPasswordFormButton" class="theme-btn btn-block" title="Send Password Reset Link">Send Password Reset Link</button>
    </form>
</div>
@endsection