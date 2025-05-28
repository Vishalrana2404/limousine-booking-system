@extends('components.layout')
@section('content')
<form id="createUserForm" method="post" action="{{ route('save-user') }}">
    @csrf
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('users') }}" title="Users">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Users
                        </a>
                        <h6 class="head-sm medium">Add User</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="addUserFormButton" class="float-right btn btn-outline-primary mx-2" title="Save">Save</button>
                        {{-- <button type="button" class="float-right btn btn-outline-danger mx-2"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button> --}}
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-box-shadow">
                            <div class="card-header px-0">
                                <div class="row">
                                    <h3 class="head-sm semibold">User Information</h3>
                                </div>
                            </div>
                            <div class="card-body px-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName">First Name <span class="text-danger">*</span></label>
                                            <input type="text" id="firstName" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" " placeholder=" First name" autofocus>
                                            @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lastname">Last name <span class="text-danger">*</span></label>
                                            <input type="text" id="lastname" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last name">
                                            @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="userType">User Type <span class="text-danger">*</span></label>
                                            <select name="user_type" id="userType" class="form-control form-select custom-select @error('user_type') is-invalid @enderror">
                                                <option value="">Select one</option>
                                                @foreach($userTypeData as $userType)
                                                @if (old('user_type') === $userType->id)
                                                <option value="{{  $userType->id }}" selected>{{ $userType->name }}</option>
                                                @else
                                                <option value="{{ $userType->id }}">{{ $userType->name }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            @error('user_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="department">Department <span class="text-danger">*</span></label>
                                            <select name="department" id="department" class="form-control form-select custom-select @error('department') is-invalid @enderror">
                                                <option value="">Select one</option>
                                                @foreach($departments as $department)
                                                @if (old('department') === $department)
                                                <option value="{{  $department }}" selected>{{ $department }}</option>
                                                @else
                                                <option value="{{ $department }}">{{ $department }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            @error('department')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fa fa-eye-slash passwordIcon"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="country_code">
                                                <span title="The country code must be an number." class="fa fa-info-circle" aria-hidden="true"></span>
                                                Country Code
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="country_code" name="country_code" value="{{ old('country_code') }}" class="form-control @error('country_code') is-invalid @enderror" placeholder="Code" autocomplete="off" autofocus>
                                            @error('country_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="phone">Contact <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact">
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control form-select custom-select @error('status') is-invalid @enderror">
                                                <option value="ACTIVE" {{ old('status') === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                                <option value="INACTIVE" {{ old('status') === "INACTIVE" ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</form>
@vite(['resources/js/Users.js'])
<script>
    const props = {
        routes: {
            checkUniqueEmail: "{{route('check-unique-email')}}",
        },
    };
</script>
@endsection