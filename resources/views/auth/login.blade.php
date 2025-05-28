@extends('auth.layout')
@section('content')
<div class="card-body login-card-body">
    <p class="login-box-msg dark-color">Sign in to start your session</p>

    <form id="loginForm" method="POST" action="{{ route('submit-login') }}">
        @csrf
        <div class="input-group mb-3">
            <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email" placeholder="Email" autocomplete="off" autofocus>
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
            <input type="password" class="form-control  @error('password') is-invalid @enderror" name="password" placeholder="Password"  autocomplete="off" autofocus>
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
        <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <button type="submit" id="loginButton" class="btn-block theme-btn" title="Sign In">Sign In</button>
            </div>
            <!-- /.col -->
        </div>
    </form>
    <p class="mt-2 mb-1 d-flex align-items-center justify-content-between">
        <button type="button" class="theme-color text-xs fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        <a class="theme-color text-xs" href="{{route('password.request')}}">Forgot Password?</a>
    </p>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="registerModalLabel">Register</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="first_name" class="form-label text-black">First Name <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name">
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label text-black">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>
                        <div class="mb-3">
                            <label for="country_code" class="form-label text-black">Country Code <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="country_code" name="country_code">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label text-black">Phone <span style="color:red">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-black">Email <span style="color:red">*</span></label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <button type="submit" id="registerButton" class="btn-block theme-btn" title="Register">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection