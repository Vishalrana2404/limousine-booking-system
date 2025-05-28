@extends('components.layout')
@section('content')
<form id="editClientForm" method="post" action="{{ route('client-update',$clientData) }}">
    @csrf
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <input type="hidden" id="userId" name="user_id" value="{{ $clientData->user->id}}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('clients') }}" title="Clients">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Clients
                        </a>
                        <h6 class="head-sm medium">Edit Clients</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="editClientFormButton" class="btn btn-outline-primary float-right mx-2" title="Save">Save</button>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card no-box-shadow">
                <div class="card-header px-0">
                    <h3 class="head-sm medium">User Information</h3>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName">First Name <span class="text-danger">*</span></label>
                                <input type="text" id="firstName" name="first_name" value="{{ $clientData->user->first_name }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="First name" autocomplete="off" autofocus>
                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" id="lastname" name="last_name" value="{{  $clientData->user->last_name }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last name" autocomplete="off">
                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clientType">Client Type <span class="text-danger">*</span></label>
                                <select name="client_type" id="clientType" class="form-control form-select custom-select @error('client_type') is-invalid @enderror" autocomplete="off">
                                    <option value="">Select one</option>
                                    @foreach($userTypeData as $userType)
                                    @if ( $clientData->user->user_type_id === $userType->id)
                                    <option value="{{  $userType->id }}" selected>{{ $userType->name }}</option>
                                    @else
                                    <option value="{{ $userType->id }}">{{ $userType->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('client_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hotelId">Corporate <span class="text-danger">*</span></label>
                                <select name="hotel_id" id="hotelId" class="form-control form-select custom-select @error('hotel_id') is-invalid @enderror" autocomplete="off">
                                    <option value="">Select one</option>
                                    @foreach($hotels as $hotel)
                                    @if ($clientData->hotel_id === $hotel->id)
                                    <option value="{{  $hotel->id }}" selected>{{ $hotel->name }}</option>
                                    @else
                                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('hotel_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" id="email" name="email" value="{{  $clientData->user->email }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autocomplete="off">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <a href="javascript:void(0)" id="resetPassword">Reset Password</a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="country_code">
                                    <span title="The country code must be an number." class="fa fa-info-circle" aria-hidden="true"></span>
                                    Country Code
                                </label>
                                <input type="text" id="country_code" name="country_code" value="{{ $clientData->user->country_code }}" class="form-control @error('country_code') is-invalid @enderror" placeholder="Code" autocomplete="off" autofocus>
                                @error('country_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="phone">Contact</label>
                                <input type="text" id="phone" name="phone" value="{{ $clientData->user->phone }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact" autocomplete="off">
                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card no-box-shadow">
                <div class="card-header px-0">
                    <h3 class="head-sm medium">Link Multi Corporates</h3>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        @if(!empty($clientData->multiCorporates) && count($clientData->multiCorporates) > 0)
                            @foreach($clientData->multiCorporates as $corporateKey => $corporate)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="multiHotelId_{{$corporateKey}}" class="form-label">Corporate</label>
                                        <div class="d-flex">
                                            <select name="multi_hotel_id[]" id="multiHotelId_{{$corporateKey}}" class="form-control form-select custom-select @error('multi_hotel_id') is-invalid @enderror multiple-hotels">
                                                <option value="">Select one</option>
                                                @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->id }}" {{ $corporate->hotel_id == $hotel->id ? 'selected' : '' }}>
                                                    {{ $hotel->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @if($corporateKey == 0)
                                                <button type="button" id="addHotel" class="btn ms-2">
                                                    <span class="fa fa-plus"></span>
                                                </button>
                                            @else
                                                <button type="button" class="remove-hotel btn ms-2">
                                                    <span class="fas fa-times text-danger"></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="multiHotelId_0" class="form-label">Corporate</label>
                                    <div class="d-flex">
                                        <select name="multi_hotel_id[]" id="multiHotelId_0" class="form-control form-select custom-select @error('multi_hotel_id') is-invalid @enderror multiple-hotels">
                                            <option value="">Select one</option>
                                            @foreach($hotels as $hotel)
                                            <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <button type="button" id="addHotel" class="btn ms-2">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </div>
                                    @error('multi_hotel_id')
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card no-box-shadow">
                <div class="card-header px-0">
                    <h3 class="head-sm medium">Admin Information</h3>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice">Invoice <span class="text-danger">*</span></label>
                                <input type="text" id="invoice" name="invoice" value="{{ $clientData->invoice}}" class="form-control @error('invoice') is-invalid @enderror" placeholder="Invoice" autocomplete="off">
                                @error('invoice')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control form-select custom-select @error('status') is-invalid @enderror" autocomplete="off">
                                    <option value="ACTIVE" {{ $clientData->status === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                    <option value="INACTIVE" {{ $clientData->status === "INACTIVE" ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entity">Entity</label>
                                <select name="entity" id="entity" class="form-control form-select custom-select @error('entity') is-invalid @enderror">
                                    <option value="">Select one</option>
                                    @foreach($entities as $entity)
                                    @if ($clientData->entity === $entity)
                                    <option value="{{  $entity }}" selected>{{ $entity }}</option>
                                    @else
                                    <option value="{{ $entity }}">{{ $entity }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('entity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
@vite(['resources/js/Clients.js'])
<script>
    const props = {
        routes: {
            checkUniqueEmail: "{{route('check-unique-email')}}",
            resetPassword: "{{route('password-change')}}",
        },
        hotels: @json($hotels)
    };
</script>
@endsection