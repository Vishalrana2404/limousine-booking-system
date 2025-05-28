@extends('auth.layout')
@section('content')
<div class="card-body login-card-body">
    <p class="login-box-msg dark-color">Sign in to start your session</p>

    <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="input-group mb-3 d-none" >
            <input type="email" class="form-control  @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" name="email" placeholder="Email" autofocus>
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
        <div class="input-group mb-3">
            <input type="password" id="password" class="form-control  @error('password') is-invalid @enderror" name="password" placeholder="Password" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa fa-eye-slash passwordIcon"></span>
                </div>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" id="password-confirm" class="form-control  @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="Confirm Password" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa fa-eye-slash passwordIcon"></span>
                </div>
            </div>
            @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <button type="submit" id="resetPasswordFormButton" class="theme-btn btn-block" title="Reset Password">Reset Password</button>
    </form>
</div>

@endsection