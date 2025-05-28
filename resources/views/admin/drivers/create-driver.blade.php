@extends('components.layout')
@section('content')
<form id="createDriverForm" method="post" action="{{ route('save-driver') }}">
    @csrf
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('drivers') }}" title="Drivers">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Drivers
                        </a>
                        <h6 class="head-sm medium">Add Driver</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="addDriverFormButton" class="float-right btn btn-outline-primary mx-2" title="Save">Save</button>
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
                                    <h3 class="head-sm medium">Driver Information</h3>
                                </div>
                            </div>
                            <div class="card-body px-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autocomplete="off" autofocus>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vehicle">Default Vehicles</label>
                                            <select name="vehicle" id="vehicle" class="form-control form-select custom-select @error('vehicle') is-invalid @enderror" autofocus>
                                                <option value="">Select one</option>
                                                @foreach($vehiclesData as $vehicle)
                                                @if (old('vehicle') === $vehicle->id)
                                                <option value="{{  $vehicle->id }}" selected>{{ $vehicle->vehicleClass->name }} ({{$vehicle->vehicle_number}})</option>
                                                @else
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicleClass->name }} ({{$vehicle->vehicle_number}})</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            @error('vehicle')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autocomplete="off" autofocus>
                                            @error('email')
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
                                            <label for="phone">Phone <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone" autocomplete="off" autofocus>
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="chat_id"><span id="telegramInfo" title="Telegram info" class="fa fa-info-circle" aria-hidden="true"></span> Telegram Chat ID <span class="text-danger">*</span></label>
                                            <input type="text" id="chat_id" name="chat_id" value="{{ old('chat_id') }}" class="form-control @error('chat_id') is-invalid @enderror" placeholder="Telegram Chat ID" autocomplete="off" autofocus>
                                            @error('chat_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Type <span class="text-danger">*</span></label>
                                            <select name="type" id="type" class="form-control form-select custom-select @error('type') is-invalid @enderror">
                                                <option value="">Select one</option>
                                                <option value="INHOUSE" {{ old('type') === "INHOUSE" ? 'selected' : '' }}>In-House</option>
                                                <option value="OUTSOURCE" {{ old('type') === "OUTSOURCE" ? 'selected' : '' }}>Out-Source</option>
                                            </select>
                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">Gender <span class="text-danger">*</span></label>
                                            <select name="gender" id="gender" class="form-control form-select custom-select @error('gender') is-invalid @enderror">
                                                <option value="">Select one</option>
                                                <option value="MALE" {{ old('gender') === "MALE" ? 'selected' : '' }}>Male</option>
                                                <option value="FEMALE" {{ old('gender') === "FEMALE" ? 'selected' : '' }}>Female</option>
                                                <option value="OTHER" {{ old('gender') === "OTHER" ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('gender')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Race</label>
                                            <select name="race" id="race" class="form-control form-select custom-select @error('race') is-invalid @enderror">
                                                <option value="">Select one</option>
                                                @foreach ($raceArr as $key => $race)
                                                <option value="{{$race}}" {{ old('race') === $race ? 'selected' : '' }}>{{$race}}</option>
                                                @endforeach
                                            </select>
                                            @error('race')
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
@include("modals.teligram-info-modal")
@vite(['resources/js/Drivers.js'])
<script>
    const props = {
        routes: {},
    }
</script>
@endsection