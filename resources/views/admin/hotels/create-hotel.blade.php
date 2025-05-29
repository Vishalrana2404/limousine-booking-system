@extends('components.layout')
@section('content')
    <form id="hotelForm" method="post" action="{{ route('save-hotel') }}">
        @csrf
        <div class="content-wrapper">
            <section class="content-header border-bottom">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <a class="dark-color back-btn" href="{{ route('hotels') }}" title="Corporates">
                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)"
                                        fill="url(#pattern0_31_250)" />
                                    <defs>
                                        <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1"
                                            height="1">
                                            <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                        </pattern>
                                        <image id="image0_31_250" width="128" height="128"
                                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                    </defs>
                                </svg>
                                Corporates
                            </a>
                            <h6 class="head-sm medium">Add Corporate</h6>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" id="addHotelFormButton"
                                class="float-right btn btn-outline-primary mx-2" title="Save">Save</button>
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
                                        <h3 class="head-sm medium">Corporate Information</h3>
                                    </div>
                                </div>
                                <div class="card-body px-0">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" id="name" name="name"
                                                    value="{{ old('name') }}"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Name" autocomplete="off" autofocus>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status"
                                                    class="form-control form-select custom-select @error('status') is-invalid @enderror">
                                                    <option value="ACTIVE"
                                                        {{ old('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                                    <option value="INACTIVE"
                                                        {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive
                                                    </option>
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
                                                <label for="is_head_office">Assign Head Office</label>
                                                <select name="is_head_office" id="is_head_office"
                                                    class="form-control form-select custom-select @error('is_head_office') is-invalid @enderror isHeadOffice">
                                                    <option value="1" >Yes</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                                @error('is_head_office')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="col-md-12">
                                                <label for="linked_head_office" >Link To Head Office</label>
                                                <select name="linked_head_office" id="linked_head_office"
                                                    class="form-control form-select custom-select @error('linked_head_office') is-invalid @enderror linkToHeadOffice">
                                                    <option value="">Select Head Office</option>
                                                    @if(!empty($headOffices))
                                                        @foreach($headOffices as $headOffice)
                                                            <option value="{{ $headOffice->id }}">{{ $headOffice->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            @error('linked_head_office')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="termConditions">Term & Conditions</label>
                                                <textarea id="termConditions" name="term_conditions" rows="5"
                                                    class="form-control @error('term_conditions') is-invalid @enderror" placeholder="Term & Conditions"
                                                    autocomplete="off" autofocus>{{ old('term_conditions') }}</textarea>
                                                @error('term_conditions')
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
                    <div class="card no-box-shadow">
                        <div class="card-header px-0">
                            <h3 class="head-sm medium">Billing Agreement</h3>
                        </div>
                        <div class="card-body px-0">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row g-2">
                                        <label for="perTripArr" class="col-sm-4 col-form-label">Per Trip Arr</label>
                                    </div>
                                    @if(!empty($vehicleClasses))
                                        @foreach($vehicleClasses as $vehicleClass)
                                            <div class="form-group row g-2 mx-4">
                                                <label for="perTripArr_{{ $vehicleClass->id }}" class="col-sm-4 col-form-label">{{ $vehicleClass->name }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="perTripArr_{{ $vehicleClass->id }}" name="per_trip_arr_{{ $vehicleClass->id }}"
                                                        value="{{ old('per_trip_arr_' . $vehicleClass->id) }}"
                                                        class="form-control hotel-vehicle-fair-arrival @error('per_trip_arr_' . $vehicleClass->id) is-invalid @enderror"
                                                        placeholder="{{ $vehicleClass->name }} Charges" autocomplete="off">
                                                    @error('per_trip_arr_' . $vehicleClass->id)
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group row g-2">
                                        <label for="perTripDep" class="col-sm-4 col-form-label">Per Trip Dep</label>
                                    </div>
                                    @if(!empty($vehicleClasses))
                                        @foreach($vehicleClasses as $vehicleClass)
                                            <div class="form-group row g-2 mx-4">
                                                <label for="perTripDep_{{ $vehicleClass->id }}" class="col-sm-4 col-form-label">{{ $vehicleClass->name }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="perTripDep_{{ $vehicleClass->id }}" name="per_trip_dep_{{ $vehicleClass->id }}"
                                                        value="{{ old('per_trip_dep_' . $vehicleClass->id) }}"
                                                        class="form-control hotel-vehicle-fair-departure @error('per_trip_dep_' . $vehicleClass->id) is-invalid @enderror"
                                                        placeholder="{{ $vehicleClass->name }} Charges" autocomplete="off">
                                                    @error('per_trip_dep_' . $vehicleClass->id)
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group row g-2">
                                        <label for="perTripTransfer" class="col-sm-4 col-form-label">Per Trip Transfer</label>
                                    </div>
                                    @if(!empty($vehicleClasses))
                                        @foreach($vehicleClasses as $vehicleClass)
                                            <div class="form-group row g-2 mx-4">
                                                <label for="perTripTransfer_{{ $vehicleClass->id }}" class="col-sm-4 col-form-label">{{ $vehicleClass->name }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="perTripTransfer_{{ $vehicleClass->id }}" name="per_trip_transfer_{{ $vehicleClass->id }}"
                                                        value="{{ old('per_trip_transfer_' . $vehicleClass->id) }}"
                                                        class="form-control hotel-vehicle-fair-transfer @error('per_trip_transfer_' . $vehicleClass->id) is-invalid @enderror"
                                                        placeholder="{{ $vehicleClass->name }} Charges" autocomplete="off">
                                                    @error('per_trip_transfer_' . $vehicleClass->id)
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group row g-2">
                                        <label for="perTripDelivery" class="col-sm-4 col-form-label">Per Trip Delivery</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="perTripDelivery" name="per_trip_delivery"
                                                value="{{ old('per_trip_delivery') }}"
                                                class="form-control @error('per_trip_delivery') is-invalid @enderror"
                                                placeholder="Per trip delivery" autocomplete="off">
                                            @error('per_trip_delivery')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="perHourDelivery" class="col-sm-4 col-form-label">Per Trip Rates</label>
                                    </div>                                
                                    @if(!empty($vehicleClasses))
                                        @foreach($vehicleClasses as $vehicleClass)
                                            <div class="form-group row g-2 mx-4">
                                                <label for="perHourDelivery_{{ $vehicleClass->id }}" class="col-sm-4 col-form-label">{{ $vehicleClass->name }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="perHourDelivery_{{ $vehicleClass->id }}" name="per_hour_rate_{{ $vehicleClass->id }}"
                                                        value="{{ old('per_hour_rate_' . $vehicleClass->id) }}"
                                                        class="form-control hotel-vehicle-fair-per-hour @error('per_hour_rate_' . $vehicleClass->id) is-invalid @enderror"
                                                        placeholder="{{ $vehicleClass->name }} Charges" autocomplete="off">
                                                    @error('per_hour_rate_' . $vehicleClass->id)
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group row g-2">
                                        <label for="peakPeriodSurcharge" class="col-sm-4 col-form-label">Peak Period
                                            Surcharge</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="peakPeriodSurcharge" name="peak_period_surcharge"
                                                value="{{ old('peak_period_surcharge') }}"
                                                class="form-control @error('peak_period_surcharge') is-invalid @enderror"
                                                placeholder="Peak Period Surcharge" autocomplete="off">
                                            @error('peak_period_surcharge')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierMidnightSurcharge23Seats"
                                            class="col-sm-4 col-form-label">Midnight Surcharge (23s)</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_midnight_surcharge_23_seats"
                                                id="fixedMultiplierMidnightSurcharge23Seats"
                                                class="form-control form-select custom-select @error('fixed_multiplier_midnight_surcharge_23_seats') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_midnight_surcharge_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_midnight_surcharge_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_midnight_surcharge_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="midNightSurcharge23Seats"
                                                name="mid_night_surcharge_23_seats"
                                                value="{{ old('mid_night_surcharge_23_seats') }}"
                                                class="form-control @error('mid_night_surcharge_23_seats') is-invalid @enderror"
                                                placeholder="Midnight Surcharge (23s)" autocomplete="off">
                                            @error('mid_night_surcharge_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierMidnightSurchargeLessThen23Seats"
                                            class="col-sm-4 col-form-label">Midnight surcharge (< 23s)</label>
                                                <div class="col-sm-3">
                                                    <select
                                                        name="fixed_multiplier_midnight_surcharge_greater_then_23_seats"
                                                        id="fixedMultiplierMidnightSurchargeLessThen23Seats"
                                                        class="form-control form-select custom-select @error('fixed_multiplier_midnight_surcharge_greater_then_23_seats') is-invalid @enderror"
                                                        autocomplete="off">
                                                        <option value="">Select one</option>
                                                        <option value="FIXED"
                                                            {{ old('fixed_multiplier_midnight_surcharge_greater_then_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                            Fixed</option>
                                                        <option value="MULTIPLIER"
                                                            {{ old('fixed_multiplier_midnight_surcharge_greater_then_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                            Multiplier</option>
                                                    </select>
                                                    @error('fixed_multiplier_midnight_surcharge_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="text" id="midNightSurchargeLessThen23Seats"
                                                        name="midnight_surcharge_greater_then_23_seats"
                                                        value="{{ old('midnight_surcharge_greater_then_23_seats') }}"
                                                        class="form-control @error('midnight_surcharge_greater_then_23_seats') is-invalid @enderror"
                                                        placeholder="Midnight Surcharge(< 23s)" autocomplete="off">
                                                    @error('midnight_surcharge_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierArrivelWaitingTime"
                                            class="col-sm-4 col-form-label">Arrival
                                            waiting time</br>(60 minutes
                                            grace)</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_arrivel_waiting_time"
                                                id="fixedMultiplierArrivelWaitingTime"
                                                class="form-control form-select custom-select @error('fixed_multiplier_arrivel_waiting_time') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_arrivel_waiting_time') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_arrivel_waiting_time') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_arrivel_waiting_time')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="arrivelWaitingTime" name="arrivel_waiting_time"
                                                value="{{ old('arrivel_waiting_time') }}"
                                                class="form-control @error('arrivel_waiting_time') is-invalid @enderror"
                                                placeholder="Arrival waiting Time" autocomplete="off">
                                            @error('arrivel_waiting_time')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierArrivelWaitingTime"
                                            class="col-sm-4 col-form-label">Departure and transfer waiting (30 minutes
                                            grace)</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_departure_and_transfer_waiting"
                                                id="fixedMultiplierDepatureAndTransferWaiting"
                                                class="form-control form-select custom-select @error('fixed_multiplier_departure_and_transfer_waiting') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_departure_and_transfer_waiting') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_departure_and_transfer_waiting') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_departure_and_transfer_waiting')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="depatureAndTransferWaiting"
                                                name="departure_and_transfer_waiting"
                                                value="{{ old('departure_and_transfer_waiting') }}"
                                                class="form-control @error('departure_and_transfer_waiting') is-invalid @enderror"
                                                placeholder="Departure and transfer waiting" autocomplete="off">
                                            @error('departure_and_transfer_waiting')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierLastMinRequest23Seats"
                                            class="col-sm-4 col-form-label">Last
                                            Min Request (23s)</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_last_min_request_23_seats"
                                                id="fixedMultiplierLastMinRequest23Seats"
                                                class="form-control form-select custom-select @error('fixed_multiplier_departure_and_transfer_waiting') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_last_min_request_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_last_min_request_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_last_min_request_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="lastMinRequest23Seats"
                                                name="last_min_request_23_seats"
                                                value="{{ old('last_min_request_23_seats') }}"
                                                class="form-control @error('last_min_request_23_seats') is-invalid @enderror"
                                                placeholder="Last Min Request (23s)" autocomplete="off">
                                            @error('last_min_request_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierLastMinRequestLessThen23Seats"
                                            class="col-sm-4 col-form-label">Last Min Request (< 23s)</label>
                                                <div class="col-sm-3">
                                                    <select name="fixed_multiplier_last_min_request_greater_then_23_seats"
                                                        id="fixedMultiplierLastMinRequestLessThen23Seats"
                                                        class="form-control form-select custom-select @error('fixed_multiplier_last_min_request_greater_then_23_seats') is-invalid @enderror"
                                                        autocomplete="off">
                                                        <option value="">Select one</option>
                                                        <option value="FIXED"
                                                            {{ old('fixed_multiplier_last_min_request_greater_then_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                            Fixed</option>
                                                        <option value="MULTIPLIER"
                                                            {{ old('fixed_multiplier_last_min_request_greater_then_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                            Multiplier</option>
                                                    </select>
                                                    @error('fixed_multiplier_last_min_request_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="text" id="lastMinRequestLessThen23Seats"
                                                        name="last_min_request_greater_then_23_seats"
                                                        value="{{ old('last_min_request_greater_then_23_seats') }}"
                                                        class="form-control @error('last_min_request_greater_then_23_seats') is-invalid @enderror"
                                                        placeholder="Last Min Request (<23s)" autocomplete="off">
                                                    @error('last_min_request_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierOutsideCitySurcharge23Seats"
                                            class="col-sm-4 col-form-label">Outside city surcharge(23s)</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_outside_city_surcharge_23_seats"
                                                id="fixedMultiplierOutsideCitySurcharge23Seats"
                                                class="form-control form-select custom-select @error('fixed_multiplier_outside_city_surcharge_23_seats') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_outside_city_surcharge_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_outside_city_surcharge_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_outside_city_surcharge_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="outsideCitySurcharge23Seats"
                                                name="outside_city_surcharge_23_seats"
                                                value="{{ old('outside_city_surcharge_23_seats') }}"
                                                class="form-control @error('outside_city_surcharge_23_seats') is-invalid @enderror"
                                                placeholder="Outside city surcharge(23s)" autocomplete="off">
                                            @error('outside_city_surcharge_23_seats')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierOutsideCitySurcharge23Seats"
                                            class="col-sm-4 col-form-label">Outside city surcharge(< 23s)</label>
                                                <div class="col-sm-3">
                                                    <select
                                                        name="fixed_multiplier_outside_city_surcharge_greater_then_23_seats"
                                                        id="fixedMultiplierOutsideCitySurchargeLessThen23Seats"
                                                        class="form-control form-select custom-select @error('fixed_multiplier_outside_city_surcharge_greater_then_23_seats') is-invalid @enderror"
                                                        autocomplete="off">
                                                        <option value="">Select one</option>
                                                        <option value="FIXED"
                                                            {{ old('fixed_multiplier_outside_city_surcharge_greater_then_23_seats') === 'FIXED' ? 'selected' : '' }}>
                                                            Fixed</option>
                                                        <option value="MULTIPLIER"
                                                            {{ old('fixed_multiplier_outside_city_surcharge_greater_then_23_seats') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                            Multiplier</option>
                                                    </select>
                                                    @error('fixed_multiplier_outside_city_surcharge_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="text" id="outsideCitySurchargeLessThen23Seats"
                                                        name="outside_city_surcharge_greater_then_23_seats"
                                                        value="{{ old('outside_city_surcharge_greater_then_23_seats') }}"
                                                        class="form-control @error('outside_city_surcharge_greater_then_23_seats') is-invalid @enderror"
                                                        placeholder="Outside city surcharge(<23s)" autocomplete="off">
                                                    @error('outside_city_surcharge_greater_then_23_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierOutsideCitySurcharge23Seats"
                                            class="col-sm-4 col-form-label">Additional Stop</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_additional_stop"
                                                id="fixedMultiplierAdditionalStop"
                                                class="form-control form-select custom-select @error('fixed_multiplier_additional_stop') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_additional_stop') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_additional_stop') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_additional_stop')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="additionalStop" name="additional_stop"
                                                value="{{ old('additional_stop') }}"
                                                class="form-control @error('additional_stop') is-invalid @enderror"
                                                placeholder="Additional Stop" autocomplete="off">
                                            @error('additional_stop')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row g-2">
                                        <label for="fixedMultiplierOutsideCitySurcharge23Seats"
                                            class="col-sm-4 col-form-label">Misc charges</label>
                                        <div class="col-sm-3">
                                            <select name="fixed_multiplier_misc_charges" id="fixedMultiplierMiscCharges"
                                                class="form-control form-select custom-select @error('fixed_multiplier_misc_charges') is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                <option value="FIXED"
                                                    {{ old('fixed_multiplier_misc_charges') === 'FIXED' ? 'selected' : '' }}>
                                                    Fixed</option>
                                                <option value="MULTIPLIER"
                                                    {{ old('fixed_multiplier_misc_charges') === 'MULTIPLIER' ? 'selected' : '' }}>
                                                    Multiplier</option>
                                            </select>
                                            @error('fixed_multiplier_misc_charges')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" id="miscCharges" name="misc_charges"
                                                value="{{ old('misc_charges') }}"
                                                class="form-control @error('misc_charges') is-invalid @enderror"
                                                placeholder="Misc charges" autocomplete="off">
                                            @error('misc_charges')
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
            </section>
        </div>
    </form>
    @vite(['resources/js/Hotels.js'])
    <script>
        const props = {
            routes: {},
            isCreatePage:true,
        }
    </script>
@endsection
