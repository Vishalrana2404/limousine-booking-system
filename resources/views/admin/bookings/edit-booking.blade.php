@extends('components.layout')
@section('content')
    @php
        use Carbon\Carbon;
        
        $loggedUserType = Auth::user()->userType->type ?? null;
    @endphp
    <form id="updateBookingForm" method="post" action="{{ route('update-booking', $booking) }}" enctype="multipart/form-data">
        @csrf
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header border-bottom">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <a class="dark-color back-btn" href="{{ route('bookings') }}" title="Bookings">
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
                                Bookings
                            </a>
                            <h6 class="head-sm medium">Edit Booking #{{ $booking->id }}</h6>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" id="" class="btn btn-outline-primary float-right mx-2"
                            title="Save">Save</button>
                            @if($loggedUserType !== 'client-admin' && $loggedUserType !== 'client-staff' && $loggedUserType !== 'client')
                                <button type="button" data-id="{{ $booking->id }}" class="btn btn-outline-danger float-right mx-2 delete-booking-btn" title="Delete">Delete</button>
                            @else
                                <button type="button" data-id="{{ $booking->id }}" class="btn btn-outline-danger float-right mx-2 cancel-booking-btn" title="Cancel Booking">Cancel Booking</button>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">

                @php
                    $billingAgreement = $booking->client->hotel->billingAgreement ?? null;
                    $serviceTypeId = $booking->service_type_id ?? null;
                    $statusValue = $booking->status ?? null;
                    $additionalStops = explode('||', $booking->additional_stops);
                    switch ($serviceTypeId) {
                        case 1:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'block';
                            $pickUpLocationTextBox = 'none';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'block';
                            $departureDateTime = 'none';
                            $perTripArrival = 'block';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'none';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        case 2:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'none';
                            $departureDateTime = 'none';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'block';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'none';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        case 3:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'block';
                            $dropOffLocationtextBox = 'none';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'block';
                            $departureDateTime = 'block';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'block';
                            $perTripDesposal = 'none';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        case 4:
                            $tripEnded = 'block';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'block';
                            $crossBorderDropDown = 'block';
                            $flightDetailRows = 'none';
                            $departureDateTime = 'none';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'block';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        case 5:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'none';
                            $departureDateTime = 'none';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'none';
                            $perTripDelivery = 'block';
                            $noOfLuggage = 'none';
                            $noOfPax = 'none';
                            break;
                        case 6:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'block';
                            $pickUpLocationTextBox = 'none';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'block';
                            $departureDateTime = 'none';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'block';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        case 7:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'block';
                            $dropOffLocationtextBox = 'none';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'block';
                            $departureDateTime = 'block';
                            $perTripArrival = 'none';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'block';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                        default:
                            $tripEnded = 'none';
                            $pickUpLocationDropdown = 'none';
                            $pickUpLocationTextBox = 'block';
                            $dropOffLocationDropdown = 'none';
                            $dropOffLocationtextBox = 'block';
                            $noOfHours = 'none';
                            $crossBorderDropDown = 'none';
                            $flightDetailRows = 'none';
                            $departureDateTime = 'none';
                            $perTripArrival = 'block';
                            $perTripTransfer = 'none';
                            $perTripDeparture = 'none';
                            $perTripDesposal = 'none';
                            $perTripDelivery = 'none';
                            $noOfLuggage = 'block';
                            $noOfPax = 'block';
                            break;
                    }
                    $isReadOnly = null;
                    $isDisabled = null;
                    if ($loggedUserType === 'client') {
                        $isReadOnly = 'readonly';
                        $isDisabled = 'disabled';
                    }

                    $child_seat_required = $booking->child_seat_required;

                    switch ($child_seat_required) {
                        case 'yes':
                            $childSeatOptions = 'flex';
                            break;
                        default : 
                            $childSeatOptions = 'none';
                    }

                    $additionalStopAddBtn = (($booking->service_type_id == 1 || $booking->service_type_id == 2 || $booking->service_type_id == 3) && count($additionalStops) > 2) ? 'none' : 'block';
                @endphp
                <div class="row row row-gap-3">
                    <div class="col-md-6">
                        <div class="card no-box-shadow border-1 p-3 h-100">
                            <div class="card-header px-0">
                                <h3 class="head-sm medium">Trip Summary</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item border-top-0">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="status" class="col-sm-6 col-form-label">Reservation Status</label>
                                            <div class="col-sm-6">
                                                @php
                                                    $status = $booking->status ?? null;
                                                    switch ($status) {
                                                        case 'ACCEPTED':
                                                            $badge = 'bg-accepted-badge';
                                                            break;
                                                        case 'PENDING':
                                                            $badge = 'bg-pending-badge';
                                                            break;
                                                        case 'COMPLETED':
                                                            $badge = 'bg-completed-badge';
                                                            break;
                                                        case 'CANCELLED':
                                                            $badge = 'bg-canceled-badge';
                                                            break;
                                                        case 'SCHEDULED':
                                                            $badge = 'bg-completed-badge';
                                                            break;
                                                        default:
                                                            $badge = 'bg-pending-badge';
                                                            break;
                                                    }
                                                @endphp
                                                <span
                                                    class="badge {{ $badge }}">{{ ucfirst(strtolower($status)) }}</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="pick-up-date" class="col-sm-6 col-form-label">Date of Pick
                                                Up</label>
                                            <div class="col-sm-6">
                                                <div class="input-group date" id="pickup-date-picker"
                                                    data-target-input="nearest">
                                                    <input type="text" name="pickup_date"
                                                        value="{{ App\CustomHelper::formatDate($booking->pickup_date) ?? null }}"
                                                        id="pick-up-date" class="form-control datetimepicker-input"
                                                        data-target="#pickup-date-picker" placeholder="dd/mm/yyyy"
                                                        autocomplete="off" autofocus />
                                                    <div class="input-group-append" data-target="#pickup-date-picker"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"
                                                                aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('pickup_date')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="pick-up-time" class="col-sm-6 col-form-label">Pick Up Time To Be Advised</label>
                                            <input type="checkbox" name="pickup_time_to_be_advised" id="pickup_time_to_be_advised"
                                                style="width: 18px; height: 18px; border: 2px solid {{ $booking->to_be_advised_status === 'yes' ? '#c3c3c3' : '#a6acaf' }}; border-radius: 4px; 
                                                box-shadow: {{ $booking->to_be_advised_status === 'yes' ? '0px 0px 8px rgba(15,20,26,80%)' : '0px 0px 5px rgba(10,20,30,50%)' }}; cursor: pointer; 
                                                appearance: none; outline: none; background-color: {{ $booking->to_be_advised_status === 'yes' ? '#0e161e' : '#fff' }}; display: inline-block;"
                                                onclick="this.style.backgroundColor = this.checked ? '#0e161e' : '#fff'; 
                                                this.style.borderColor = this.checked ? '#c3c3c3' : '#a6acaf'; 
                                                this.style.boxShadow = this.checked ? '0px 0px 8px rgba(15,20,26,80%)' : '0px 0px 5px rgba(10,20,30,50%)';"
                                                {{ $booking->to_be_advised_status === 'yes' ? 'checked=checked' : '' }}>

                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="pick-up-time" class="col-sm-6 col-form-label">Time of Pick
                                                Up</label>
                                            <div class="col-sm-6">
                                                <div class="input-group date" id="pick-up-time-picker"
                                                    data-target-input="nearest">
                                                    <input type="text" name="pickup_time"
                                                        value="{{ App\CustomHelper::formatTime($booking->pickup_time) ?? null }}"
                                                        id="pick-up-time" class="form-control datetimepicker-input"
                                                        data-target="#pick-up-time-picker" placeholder="HH:MM"
                                                        autocomplete="off" autofocus {{$booking->to_be_advised_status == 'yes' ? 'readonly' : 'no'}}/>
                                                    <div class="input-group-append" data-target="#pick-up-time-picker"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"
                                                                aria-hidden="true"></i></div>
                                                    </div>
                                                </div>
                                                @error('pickup_time')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="service-types" class="col-sm-6 col-form-label">Type of
                                                Service</label>
                                            <div class="col-sm-6">
                                                <select name="service_type_id" id="service-types"
                                                    class="form-control form-select custom-select @error('service_type_id') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select One</option>
                                                    @foreach ($serviceTypes as $serviceType)
                                                        @if ($booking->service_type_id === $serviceType->id)
                                                            <option value="{{ $serviceType->id }}" selected>
                                                                {{ $serviceType->name }}</option>
                                                        @else
                                                            <option value="{{ $serviceType->id }}">
                                                                {{ $serviceType->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('service_type_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="event-id" class="col-sm-6 col-form-label">Event</label>
                                            <div class="col-sm-6">
                                                <select name="event_id" id="sevent-id"
                                                    class="form-control form-select custom-select @error('event_id') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select One</option>
                                                    @foreach ($events as $event)
                                                        @if ($booking->event_id === $event->id)
                                                            <option value="{{ $event->id }}" selected>
                                                                {{ $event->name }}</option>
                                                        @else
                                                            <option value="{{ $event->id }}">
                                                                {{ $event->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('service_type_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item flightDetailRows" style="display:{{ $flightDetailRows }}">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="flight-details" class="col-sm-6 col-form-label">Flight
                                                Details</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="flight-details"
                                                    value="{{ $booking->flight_detail }}" name="flight_detail"
                                                    class="form-control @error('flight_detail') is-invalid @enderror"
                                                    placeholder="Flight Details">
                                                @error('flight_detail')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item departureDateTime"
                                        style="display:{{ $departureDateTime }}">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="flight-departure-time" class="col-sm-6 col-form-label">Flight
                                                Departure Time</label>
                                            <div class="col-sm-6">
                                                <div class="input-group date" id="flight-departure-time-picker"
                                                    data-target-input="nearest">
                                                    <input type="text" name="departure_time"
                                                        value="{{ App\CustomHelper::formatDateTime($booking->departure_time) ?? null }}"
                                                        id="flight-departure-time"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#flight-departure-time-picker"
                                                        placeholder="dd/mm/yyyy HH:mm" autocomplete="off" autofocus />
                                                    <div class="input-group-append"
                                                        data-target="#flight-departure-time-picker"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"
                                                                aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('departure_time')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="noOfHours" style="display:{{ $noOfHours }};">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="no-of-hours" class="col-sm-6 col-form-label">No. of hours</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="no-of-hours"
                                                    value="{{ $booking->no_of_hours ?? null }}" name="no_of_hours"
                                                    class="form-control @error('no_of_hours') is-invalid @enderror"
                                                    placeholder="No. of hours">
                                                @error('no_of_hours')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    @if ($loggedUserType === null || $loggedUserType === 'admin')
                                        <li class="list-group-item" id="tripEnded" style="display:{{ $tripEnded }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label for="trip-ended" class="col-sm-6 col-form-label">Trip Ended</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group date" id="trip-ended-picker"
                                                        data-target-input="nearest">
                                                        <input type="text" name="trip_ended"
                                                            value="{{ App\CustomHelper::formatDateTime($booking->trip_ended) ?? null }}"
                                                            id="trip-ended"
                                                            class="form-control datetimepicker-input @error('trip_ended') is-invalid @enderror"
                                                            data-target="#trip-ended-picker"
                                                            placeholder="dd/mm/yyyy HH:mm" autocomplete="off" autofocus />
                                                        <div class="input-group-append" data-target="#trip-ended-picker"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"
                                                                    aria-hidden="true"></i></div>
                                                        </div>
                                                    </div>
                                                    @error('trip_ended')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    <li class="list-group-item" id="crossBorderDropDown"
                                        style="display:{{ $crossBorderDropDown }};">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="cross-border-service" class="col-sm-6 col-form-label">Cross Border
                                                Service</label>
                                            <div class="col-sm-6">
                                                <select name="is_cross_border" id="cross-border-service"
                                                    class="form-control form-select custom-select @error('is_cross_border') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select one</option>
                                                    <option value="1"
                                                        {{ $booking->is_cross_border === 1 ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0"
                                                        {{ $booking->is_cross_border == 0 ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                                @error('is_cross_border')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card no-box-shadow border-1 p-3 h-100">
                            <div class="card-header px-0">
                                <h3 class="head-sm medium">Passenger Details</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-unbordered mb-3">
                                    @php
                                        $allGuests = !empty($booking->guest_name) ? explode(',', $booking->guest_name) : NULL;
                                        $allCountryCodes = !empty($booking->country_code) ? explode(',', $booking->country_code) : NULL;
                                        $allPhones = !empty($booking->phone) ? explode(',', $booking->phone) : NULL;

                                    @endphp
                                    @if(!empty($allGuests))
                                        @foreach($allGuests as $guest_key => $guest)
                                            <li class="list-group-item border-top-0">
                                                <div class="form-group row row-gap-2 mb-0">
                                                    <label for="guest-name-{{$guest_key}}" class="col-sm-6 col-form-label">Guest Name</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" id="guest-name-{{$guest_key}}" name="guest_name[]"
                                                            value="{{ $guest }}"
                                                            class="form-control @error('is_cross_border.' . $guest_key) is-invalid @enderror guest-name"
                                                            placeholder="Guest Name">
                                                        @error('guest.' . $guest_key)
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="form-group row row-gap-2 mb-0">
                                                    <label for="contact-number-{{$guest_key}}" class="col-sm-6 col-form-label">Contact
                                                        Number</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" value="{{ !empty($allCountryCodes[$guest_key]) ? $allCountryCodes[$guest_key] : '' }}"
                                                            id="country-code-{{$guest_key}}" name="country_code[]"
                                                            class="form-control @error('country_code.' . $guest_key) is-invalid @enderror country-code"
                                                            placeholder="Country Code" autocomplete="off">
                                                        @error('country_code.' . $guest_key)
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="phone[]" value="{{ !empty($allPhones[$guest_key]) ? $allPhones[$guest_key] : '' }}"
                                                            id="contact-number-{{$guest_key}}"
                                                            class="form-control @error('phone.' . $guest_key) is-invalid @enderror phone"
                                                            placeholder="Contact Number" autocomplete="off">
                                                        @error('phone.' . $guest_key)
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </li>
                                            @if($guest_key + 1 !== count($allGuests))
                                                <li class="list-group-item border-top-0">
                                                    <div class="form-group row row-gap-2 mb-0">
                                                        <button type="button" class="remove-guest" style="color:white; background-color:red; padding: 10px;">Remove Guest</button>
                                                    </div>
                                                </li> 
                                            @endif
                                        @endforeach
                                    @else
                                        <li class="list-group-item border-top-0">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label for="guest-name-0" class="col-sm-6 col-form-label">Guest Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="guest-name-0" name="guest_name[]"
                                                        value=""
                                                        class="form-control @error('is_cross_border') is-invalid @enderror guest-name"
                                                        placeholder="Guest Name">
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label for="contact-number-0" class="col-sm-6 col-form-label">Contact
                                                    Number</label>
                                                <div class="col-sm-2">
                                                    <input type="text" value=""
                                                        id="country-code-0" name="country_code[]"
                                                        class="form-control @error('country_code') is-invalid @enderror country-code"
                                                        placeholder="Country Code" autocomplete="off">
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" name="phone[]" value=""
                                                        id="contact-number-0"
                                                        class="form-control @error('phone') is-invalid @enderror phone"
                                                        placeholder="Contact Number" autocomplete="off">
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    <li class="list-group-item border-top-0">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <button type="button" id="addGuest" style="color:white; background-color:black; padding: 10px;">Add Guest</button>
                                        </div>
                                    </li>                                    
                                    
                                    <li class="list-group-item" id="noOfPax" style="display:{{ $noOfPax }};">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="no-of-pax" class="col-sm-6 col-form-label">Number of Pax</label>
                                            <div class="col-sm-6">
                                                <input type="text" name="total_pax" value="{{ $booking->total_pax }}"
                                                    id="no-of-pax"
                                                    class="form-control @error('total_pax') is-invalid @enderror"
                                                    placeholder="Number of Pax">
                                                @error('total_pax')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="noOfLuggage" style="display:{{ $noOfLuggage }};">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="no-of-luggage" class="col-sm-6 col-form-label">Number of
                                                Luggage</label>
                                            <div class="col-sm-6">
                                                <input type="text" name="total_luggage"
                                                    value="{{ $booking->total_luggage ?? '0' }}" id="no-of-luggage"
                                                    class="form-control @error('total_luggage') is-invalid @enderror"
                                                    placeholder="Number of Luggage">
                                                @error('total_luggage')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="childContainer">
                                        <p class="small mb-3">*Above 1.35m Child seat not required in Singapore</p>
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label" for="child_seat_required">Child Seat Required? <span class="text-danger">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('child_seat_required') is-invalid @enderror" type="radio" name="child_seat_required" id="childSeatYes" value="yes" autocomplete="off" autofocus {{ $booking->child_seat_required == 'yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="childSeatYes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input @error('child_seat_required') is-invalid @enderror" type="radio" name="child_seat_required" id="childSeatNo" value="no" autocomplete="off" autofocus {{ $booking->child_seat_required == 'no' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="childSeatNo">No</label>
                                                </div>
                                                @error('child_seat_required')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="childSeatContainer" style="display: {{$booking->child_seat_required == 'yes' ? 'block' : 'none';}}">
                                        <p class="text-muted small">1st Child seat is free. Additional child seat is $10.</p>
                                        <div class="form-group row row-gap-2 mb-0" id="childSeatOptions" style="display:{{ $childSeatOptions }};">
                                            <label for="childSeatCount" class="col-sm-6 col-form-label">No. of Child Seat required:</label>
                                            <div class="col-sm-6">
                                                <select class="form-select" id="childSeatCount" name="no_of_seats_required">
                                                    <option value="1" {{$booking->no_of_seats_required == 1 ? 'selected' : ''}}>1</option>
                                                    <option value="2" {{$booking->no_of_seats_required == 2 ? 'selected' : ''}}>2</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="child1Age" style="display : {{$booking->child_seat_required == 'yes' && ($booking->no_of_seats_required == 1 || $booking->no_of_seats_required == 2) ? 'block' : 'none';}}">
                                        <div class="form-group row row-gap-2 mb-0" id="childAgeInputs">
                                            <label class="col-sm-6 col-form-label">Child 1:</label>
                                            <div class="col-sm-6">
                                                <select class="form-select child-age" name="child_1_age" id="child_1_age">
                                                    <option value="<1 yo" {{$booking->child_1_age == "<1 yo" ? 'selected' : ''}}>&lt;1 yo</option>
                                                    <option value="1 yo" {{$booking->child_1_age == "1 yo" ? 'selected' : ''}}>1 yo</option>
                                                    <option value="2 yo" {{$booking->child_1_age == "2 yo" ? 'selected' : ''}}>2 yo</option>
                                                    <option value="3 yo" {{$booking->child_1_age == "3 yo" ? 'selected' : ''}}>3 yo</option>
                                                    <option value="4 yo" {{$booking->child_1_age == "4 yo" ? 'selected' : ''}}>4 yo</option>
                                                    <option value="5 yo" {{$booking->child_1_age == "5 yo" ? 'selected' : ''}}>5 yo</option>
                                                    <option value="6 yo" {{$booking->child_1_age == "6 yo" ? 'selected' : ''}}>6 yo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="child2Age" style="display : {{$booking->child_seat_required == 'yes' && $booking->no_of_seats_required == 2 ? 'block' : 'none';}}">
                                        <div class="form-group row row-gap-2 mb-0" id="childAgeInputs">
                                            <label class="col-sm-6 col-form-label">Child 2:</label>
                                            <div class="col-sm-6">
                                                <select class="form-select child-age" name="child_2_age" id="child_2_age">
                                                    <option value="<1 yo" {{$booking->child_2_age == "<1 yo" ? 'selected' : ''}}>&lt;1 yo</option>
                                                    <option value="1 yo" {{$booking->child_2_age == "1 yo" ? 'selected' : ''}}>1 yo</option>
                                                    <option value="2 yo" {{$booking->child_2_age == "2 yo" ? 'selected' : ''}}>2 yo</option>
                                                    <option value="3 yo" {{$booking->child_2_age == "3 yo" ? 'selected' : ''}}>3 yo</option>
                                                    <option value="4 yo" {{$booking->child_2_age == "4 yo" ? 'selected' : ''}}>4 yo</option>
                                                    <option value="5 yo" {{$booking->child_2_age == "5 yo" ? 'selected' : ''}}>5 yo</option>
                                                    <option value="6 yo" {{$booking->child_2_age == "6 yo" ? 'selected' : ''}}>6 yo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item" id="meetAndGreetContainer">
                                        <p class="small mb-3">*Seating capacity > 13seater</p>
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label" for="meet_and_greet">Meet And Greet? <span class="text-danger">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('meet_and_greet') is-invalid @enderror" type="radio" name="meet_and_greet" id="meetAndGreetYes" value="yes" autocomplete="off" autofocus {{ $booking->meet_and_greet == 'YES' ? 'checked' : '' }} {{ !empty($booking->vehicle_type_id) && $vehicleTypes->where('id', $booking->vehicle_type_id)->first()->seating_capacity > 13 ? '' : 'disabled' }}>
                                                    <label class="form-check-label" for="meetAndGreetYes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input @error('meet_and_greet') is-invalid @enderror" type="radio" name="meet_and_greet" id="meetAndGreetNo" value="no" autocomplete="off" autofocus {{ $booking->meet_and_greet == 'NO' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="meetAndGreetNo">No</label>
                                                </div>
                                                @error('meet_and_greet')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-outline no-box-shadow border-1 p-3 h-100">
                            <div class="card-header px-0">
                                <h3 class="head-sm medium">Route and Driver Information</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item border-top-0">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="pick-up-location-id"
                                                class="col-sm-6 col-form-label">Pick-up</label>
                                            <div class="col-sm-6" id="pickUpLocationDropdown"
                                                style="display:{{ $pickUpLocationDropdown }};">
                                                <select name="pick_up_location_id" id="pick-up-location-id"
                                                    class="form-control form-select custom-select @error('pick_up_location_id') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select one</option>
                                                    @foreach ($locations as $location)
                                                        @if ($booking->pick_up_location_id === $location->id)
                                                            <option value="{{ $location->id }}" selected>
                                                                {{ $location->name }}</option>
                                                        @else
                                                            <option value="{{ $location->id }}">{{ $location->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('pick_up_location_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6" id="pickUpLocationTextBox"
                                                style="display:{{ $pickUpLocationTextBox }};">
                                                <input type="text" id="pick-up-location" name="pick_up_location"
                                                    value="{{ $booking->pick_up_location }}"
                                                    class="form-control @error('pick_up_location') is-invalid @enderror"
                                                    placeholder="Pick Up Location" autocomplete="off">
                                                @error('pick_up_location')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="drop-of-location-id"
                                                class="col-sm-6 col-form-label">Drop-off</label>
                                            <div class="col-sm-6" id="dropOffLocationDropdown"
                                                style="display:{{ $dropOffLocationDropdown }};">
                                                <select name="drop_off_location_id" id="drop-off-location-id"
                                                    class="form-control form-select custom-select @error('drop_off_location_id') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select one</option>
                                                    @foreach ($locations as $location)
                                                        @if ($booking->drop_off_location_id === $location->id)
                                                            <option value="{{ $location->id }}" selected>
                                                                {{ $location->name }}</option>
                                                        @else
                                                            <option value="{{ $location->id }}">{{ $location->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('drop_off_location_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6" id="dropOffLocationtextBox"
                                                style="display:{{ $dropOffLocationtextBox }};">
                                                <input type="text" id="drop-off-location" name="drop_of_location"
                                                    value="{{ $booking->drop_of_location }}"
                                                    class="form-control @error('drop_of_location') is-invalid @enderror"
                                                    placeholder="Drop Off Location" autocomplete="off">
                                                @error('drop_of_location')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    @if(!empty($additionalStops))
                                        @foreach($additionalStops as $stopKey => $stop)
                                            <li class="list-group-item border-top-0 all-additional-stops">
                                                <div class="form-group row row-gap-2 mb-0">
                                                    <label for="additionalStops_{{$stopKey}}" class="col-sm-6 col-form-label" id="additional_stop_label_{{$stopKey}}">Additional Stop {{$stopKey+1}}</label>
                                                    <div class="col-sm-6" style="display:flex; align-items:center; justify-content: space-between;">
                                                        <input type="text" id="additionalStops_{{$stopKey}}" name="additional_stops[]"
                                                        value="{{ $stop }}"
                                                        class="form-control col-sm-9 additional-stops"
                                                        placeholder="Additional Stop">
                                                        @if ($stopKey == 0)
                                                            <button type="button" id="addStop" class="col-sm-3" style="display: {{$additionalStopAddBtn}}"><span class="fa fa-plus mt-3"></span></button>
                                                        @else
                                                            <button type="button" class="remove-stop col-sm-3"><span
                                                                    class="fas fa-times mt-3 text-danger"></span></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="vehicle-type" class="col-sm-6 col-form-label">Type of
                                                Vehicle</label>
                                            <div class="col-sm-6">
                                                <select name="vehicle_type_id" id="vehicle-type"
                                                    class="form-control form-select custom-select @error('vehicle_type_id') is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select one</option>
                                                    @foreach ($vehicleTypes as $vehicleType)
                                                        @if ($booking->vehicle_type_id === $vehicleType->id)
                                                            <option value="{{ $vehicleType->id }}"
                                                                data-seating-capacity="{{ $vehicleType->seating_capacity }}"
                                                                selected>
                                                                {{ $vehicleType->name }}
                                                                ({{ $vehicleType->seating_capacity }}s)
                                                            </option>
                                                        @else
                                                            <option value="{{ $vehicleType->id }}"
                                                                data-seating-capacity="{{ $vehicleType->seating_capacity }}">
                                                                {{ $vehicleType->name }}
                                                                ({{ $vehicleType->seating_capacity }}s)
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('vehicle_type_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="driver-id" class="col-sm-6 col-form-label">Driver</label>
                                            <div class="col-sm-6">
                                                <select name="driver_id" id="driver-id"
                                                    class="form-control form-select custom-select @error('driver_id') is-invalid @enderror"
                                                    autocomplete="off" {{ $isDisabled }}>
                                                    <option value="">Select one</option>
                                                    @foreach ($drivers as $driver)
                                                        @php
                                                            $isOffDay = App\CustomHelper::checkDriverOffDay(
                                                                $booking->pickup_date,
                                                                $driver->id,
                                                                $driverOffDays,
                                                            );
                                                        @endphp
                                                        @if (!$isOffDay)
                                                            @if ($booking->driver_id === $driver->id)
                                                                <option value="{{ $driver->id }}"
                                                                    data-driver-contact="{{ $driver->phone }}"
                                                                    data-vehicle-id="{{ $driver->vehicle_id }}" selected>
                                                                    {{ $driver->name }}
                                                                </option>
                                                            @else
                                                                <option value="{{ $driver->id }}"
                                                                    data-driver-contact="{{ $driver->phone }}"
                                                                    data-vehicle-id="{{ $driver->vehicle_id }}">
                                                                    {{ $driver->name }}
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('driver_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="driver-contact" class="col-sm-6 col-form-label">Driver
                                                Contact</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="driver-contact"
                                                    value="{{ $booking->driver->phone ?? null }}" name="driver_contact"
                                                    class="form-control" placeholder="Driver Contact"
                                                    {{ $isReadOnly }}>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label for="vehicle-id" class="col-sm-6 col-form-label">Vehicle number</label>
                                            <div class="col-sm-6">
                                                <select name="vehicle_id" id="vehicle-id"
                                                    class="form-control form-select custom-select @error('vehicle_id') is-invalid @enderror"
                                                    autocomplete="off" {{ $isDisabled }}>
                                                    <option value="">Select one</option>
                                                    @foreach ($vehicles as $vehicle)
                                                        @if ($booking->vehicle_id === $vehicle->id)
                                                            <option value="{{ $vehicle->id }}" selected>
                                                                {{ $vehicle->vehicleClass->name }}
                                                                ({{ $vehicle->vehicle_number }})
                                                            </option>
                                                        @else
                                                            <option value="{{ $vehicle->id }}">
                                                                {{ $vehicle->vehicleClass->name }}
                                                                ({{ $vehicle->vehicle_number }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('vehicle_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-outline no-box-shadow border-1 p-3 h-100">
                            <div class="card-header px-0">
                                <h3 class="head-sm medium">Client Information</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item border-top-0">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label">Booked By</label>
                                            <div class="col-sm-6">
                                                @php
                                                    $firstName = $booking->createdBy->first_name ?? null;
                                                    $lastName = $booking->createdBy->last_name ?? null;
                                                    $fullName = App\CustomHelper::getFullName($firstName, $lastName);
                                                @endphp
                                                <span>{{ $fullName ?? null }}</span>
                                            </div>
                                        </div>
                                    </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label class="col-sm-6 col-form-label">Corporate</label>
                                                <div class="col-sm-6">
                                                    <!-- @php
                                                        $hotelName = $booking->client->hotel->name ?? null;
                                                    @endphp -->
                                                    <!-- <span>{{ $hotelName ?? null }}</span> -->

                                                    <select name="client_id" id="clientId"
                                                        class="form-control form-select custom-select @error('client_id') is-invalid @enderror"
                                                        autocomplete="off">
                                                        <option value="">Select One</option>
                                                        @foreach ($hotelClients as $hotelClient)
                                                            @php
                                                                $client = $hotelClient->client ?? null;
                                                            @endphp
                                                            @if ($client)
                                                                <option value="{{ $client->id }}" {{ $booking->client->id == $client->id ? 'selected' : ''}}>{{$hotelClient->name  }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="client_hotel_id" id="client_hotel_id" value="{{!empty($booking->client->hotel_id) ? $booking->client->hotel_id : '';}}">
                                                </div>
                                            </div>
                                        </li>
                                    @if ($loggedUserType === null || $loggedUserType === 'admin')
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label class="col-sm-6 col-form-label">Invoice</label>
                                                <div class="col-sm-6">
                                                    @php
                                                        $invoice = $booking->client->invoice ?? null;
                                                    @endphp
                                                    <span>{{ $invoice ?? null }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label class="col-sm-6 col-form-label">Entity</label>
                                                <div class="col-sm-6">
                                                    @php
                                                        $entity = $booking->client->entity ?? null;
                                                    @endphp
                                                    <span>{{ $entity ?? null }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        @if(!empty($hotelIdsFromLinkedCorporates) && count($hotelIdsFromLinkedCorporates) > 0)
                                            <!-- <li class="list-group-item">
                                                <div class="form-group row row-gap-2 mb-0">
                                                    <label class="col-sm-6 col-form-label">Corporate</label>
                                                    <div class="col-sm-6">
                                                        @php
                                                            $hotelName = $booking->client->hotel->name ?? null;
                                                        @endphp
                                                        <span>{{ $hotelName ?? null }}</span>
                                                        <input type="hidden" name="client_hotel_id" id="client_hotel_id" value="{{!empty($booking->client->hotel_id) ? $booking->client->hotel_id : '';}}">
                                                    </div>
                                                </div>
                                            </li> -->
                                        @endif
                                    @endif

                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label">Access Given Clients</label>
                                            <div class="col-sm-6 access_given_clients_div">
                                                @if(!empty($booking->linked_clients))
                                                
                                                    @php
                                                            $linkedClients = explode(',', $booking->linked_clients);
                                                    @endphp
                                                    @foreach($linkedClients as $bookingClientKey => $bookingClient)
                                                        @php
                                                            $clientId = str_replace('"', '', $bookingClient);

                                                            $clientId = str_replace('[', '', $clientId);

                                                            $clientId = str_replace(']', '', $clientId);

                                                        @endphp
                                                        <select name="access_given_clients[]" id="access_given_clients_{{$bookingClientKey}}"
                                                            class="form-control form-select custom-select col-sm-9 access_given_clients"
                                                            autocomplete="off">
                                                            <option value="">Select Client</option>
                                                            @foreach ($clients as $client)
                                                                @php
                                                                    if(gettype($client) !== 'array')
                                                                    {
                                                                        $client->toArray();
                                                                        $client['user']->toArray();
                                                                    }
                                                                @endphp
                                                                @if($client['user_id'] !== $booking->created_by_id)
                                                                    <option value="{{ $client['user_id'] }}" {{ $clientId == $client['user_id'] ? 'selected' : '';}}>
                                                                        <?= (!empty($client['user']['first_name']) ? ucwords($client['user']['first_name']) : '') . (!empty($client['user']['last_name']) ? ' ' . ucwords($client['user']['last_name']) : ''); ?>
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @if($bookingClientKey == 0)
                                                            <button type="button" id="addClient" class="col-sm-2"><span class="fa fa-plus mt-3"></span></button>
                                                        @else
                                                            <button type="button" class="remove-client col-sm-2" id="remove_client_{{$bookingClientKey}}"><span class="fas fa-times mt-3 text-danger" id="client_span_{{$bookingClientKey}}"></span></button>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <select name="access_given_clients[]" id="access_given_clients_0"
                                                        class="form-control form-select custom-select @error('access_given_clients') is-invalid @enderror col-sm-9 access_given_clients"
                                                        autocomplete="off">
                                                        <option value="">Select Client</option>
                                                        @foreach ($clients as $client)
                                                            @php
                                                                if(gettype($client) !== 'array')
                                                                {
                                                                    $client->toArray();
                                                                    $client['user']->toArray();
                                                                }
                                                            @endphp
                                                            @if($client['user_id'] !== $booking->created_by_id)
                                                            <option value="{{ $client['user_id'] }}">
                                                                <?= (!empty($client['user']['first_name']) ? ucwords($client['user']['first_name']) : '') . (!empty($client['user']['last_name']) ? ' ' . ucwords($client['user']['last_name']) : ''); ?>
                                                            </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <button type="button" id="addClient" class="col-sm-2"><span class="fa fa-plus mt-3"></span></button>
                                                @endif
                                                @error('access_given_clients')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label">Booking Date</label>
                                            <div class="col-sm-6">
                                                <span>{{ App\CustomHelper::formatSingaporeDate($booking->created_at) ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label">Client
                                                Instructions</label>
                                            <div class="col-sm-6">

                                                @php
                                                    $clientInstructions = $booking->client_instructions ?? null;
                                                @endphp
                                                @if ($loggedUserType === null || $loggedUserType === 'admin')
                                                    <span>{{ $clientInstructions ?? '-' }}</span>
                                                    @error('client_instructions')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @else
                                                    <textarea id="client-instructions" rows="4" name="client_instructions"
                                                        class="form-control @error('client_instructions') is-invalid @enderror" placeholder="Client Instructions">{{ $clientInstructions }}</textarea>
                                                    @error('client_instructions')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-group row row-gap-2 mb-0">
                                            <label class="col-sm-6 col-form-label">Attachments</label>
                                            <div class="col-sm-6">
                                                @php
                                                    $attachment = $booking->attachment ?? null;
                                                @endphp

                                                @if ($attachment && Storage::disk('public')->exists($attachment))
                                                    <div class="col-sm-2">
                                                        <a target="blank" href="{{ Storage::url($attachment) }}"
                                                            class="attachment-link">
                                                            <i class="fa fa-file text-dark"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    @if ($loggedUserType === null || $loggedUserType === 'admin')
                                                        <div class="col-sm-2">
                                                            <span>-</span>
                                                        </div>
                                                    @endif
                                                @endif
                                                @if ($loggedUserType === 'client')
                                                    <div class="col-sm-10">
                                                        <input type="file" id="attachment" name="attachment"
                                                            value="{{ old('attachment') }}"
                                                            class="form-control @error('attachment') is-invalid @enderror"
                                                            placeholder="File">
                                                        @error('attachment')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @if ($loggedUserType === null || $loggedUserType === 'admin')
                        <div class="col-md-6">
                            <div class="card card-outline no-box-shadow border-1 p-3 h-100">
                                <div class="card-header px-0">
                                    <h3 class="head-sm medium">Billing Information</h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item border-top-0">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <label class="col-sm-4 col-form-label"></label>
                                                <label class="col-sm-4 col-form-label text-right">Description</label>
                                                <label class="col-sm-4 col-form-label text-right">Cost</label>

                                            </div>
                                        </li>
                                        <li class="list-group-item" id="perTripArrival"
                                            style="display:{{ $perTripArrival }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <label for="arrival-charge" class="col-sm-4 col-form-label py-0">Per Trip
                                                    Arrival</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="arrival-charge-description"
                                                        name="arrival_charge_description"
                                                        value="{{ $booking->bookingBilling->arrival_charge_description ?? null }}"
                                                        class="form-control @error('arrival_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('arrival_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $amount = 0.00;
                                                        if(!empty($corporateFairBillingDetailsService))
                                                        {
                                                            if($corporateFairBillingDetailsService->billing_type == 'Arrival')
                                                            {
                                                                $amount = $corporateFairBillingDetailsService->amount;
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="arrival-charge" name="arrival_charge"
                                                            value="{{$amount}}"
                                                            class="form-control @error('arrival_charge') is-invalid @enderror"
                                                            placeholder="Per Trip Arrival" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                    </div>
                                                    @error('arrival_charge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item" id="perTripTransfer"
                                            style="display:{{ $perTripTransfer }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <label for="transfer-charge" class="col-sm-4 col-form-label py-0">Per Trip
                                                    Transfer</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="transfer-charge-description"
                                                        name="transfer_charge_description"
                                                        value="{{ $booking->bookingBilling->transfer_charge_description ?? null }}"
                                                        class="form-control @error('transfer_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('transfer_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $amount = 0.00;
                                                        if(!empty($corporateFairBillingDetailsService))
                                                        {
                                                            if($corporateFairBillingDetailsService->billing_type == 'Transfer')
                                                            {
                                                                $amount = $corporateFairBillingDetailsService->amount;
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="transfer-charge" name="transfer_charge"
                                                            value="{{ $amount }}"
                                                            class="form-control @error('transfer_charge') is-invalid @enderror"
                                                            placeholder="Per Trip Transfer" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                    </div>
                                                    @error('transfer_charge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item" id="perTripDeparture"
                                            style="display:{{ $perTripDeparture }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <label for="departure-charge" class="col-sm-4 col-form-label py-0">Per
                                                    Trip
                                                    Departure</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="departure-charge-description"
                                                        name="departure_charge_description"
                                                        value="{{ $booking->bookingBilling->departure_charge_description ?? null }}"
                                                        class="form-control @error('departure_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('departure_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $amount = 0.00;
                                                        if(!empty($corporateFairBillingDetailsService))
                                                        {
                                                            if($corporateFairBillingDetailsService->billing_type == 'Departure')
                                                            {
                                                                $amount = $corporateFairBillingDetailsService->amount;
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="departure-charge"
                                                            name="departure_charge"
                                                            value="{{ $amount }}"
                                                            class="form-control @error('departure_charge') is-invalid @enderror"
                                                            placeholder="Per Trip Departure" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                    </div>
                                                    @error('departure_charge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item" id="perTripDesposal"
                                            style="display:{{ $perTripDesposal }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <label for="disposal-charge" class="col-sm-4 col-form-label py-0">Per Hour
                                                    Rate</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="disposal-charge-description"
                                                        name="disposal_charge_description"
                                                        value="{{ $booking->bookingBilling->disposal_charge_description ?? null }}"
                                                        class="form-control @error('disposal_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('disposal_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $amount = 0.00;
                                                        $original_amount = 0.00;
                                                        if(!empty($corporateFairBillingDetailsPerHour))
                                                        {
                                                            if($corporateFairBillingDetailsPerHour->billing_type == 'Hour')
                                                            {
                                                                $original_amount = $corporateFairBillingDetailsPerHour->amount;

                                                                if(!empty($booking->no_of_hours))
                                                                {
                                                                    $amount = $original_amount * $booking->no_of_hours;
                                                                }else{
                                                                    $amount = $original_amount;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="disposal-charge" name="disposal_charge"
                                                            value="{{ $amount }}"
                                                            class="form-control @error('disposal_charge') is-invalid @enderror"
                                                            placeholder="Per Hour Rate" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                    <input type="hidden" id="original-disposal-charge" name="original_disposal_charge"
                                                        value="{{ $original_amount }}"
                                                        class="form-control">
                                                    </div>
                                                    @error('disposal_charge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item" id="perTripDelivery"
                                            style="display:{{ $perTripDelivery }};">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <label for="delivery-charge" class="col-sm-4 col-form-label py-0">Per Trip
                                                    Delivery</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="delivery-charge-description"
                                                        name="delivery_charge_description"
                                                        value="{{ $booking->bookingBilling->delivery_charge_description ?? null }}"
                                                        class="form-control @error('delivery_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('delivery_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $deliveryChargeValue =
                                                            $booking->bookingBilling->delivery_charge ?? null;
                                                        $perTripDeliveryValue =
                                                            $deliveryChargeValue !== null
                                                                ? $deliveryChargeValue
                                                                : (@$billingAgreement->per_trip_delivery !== null
                                                                    ? $billingAgreement->per_trip_delivery
                                                                    : null);
                                                    @endphp
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="delivery-charge" name="delivery_charge"
                                                            value="{{ $perTripDeliveryValue ?? null }}"
                                                            class="form-control @error('delivery_charge') is-invalid @enderror"
                                                            placeholder="Per Trip Delivery">
                                                    </div>
                                                    @error('delivery_charge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        @php
                                            $checkDate = Carbon::parse($booking->pickup_date);
                                            $isPeakPeriod = false;

                                            // Iterate over each event in the collection
                                            foreach ($peakPeriods as $peakPeriod) {
                                                // Parse start_date and end_date using Carbon
                                                $startDate = Carbon::parse($peakPeriod->start_date);
                                                $endDate = Carbon::parse($peakPeriod->end_date);
                                                if ($checkDate->between($startDate, $endDate)) {
                                                    $isPeakPeriod = true;
                                                    break; // Return true if a matching event is found
                                                }
                                            }
                                            $isPeakPeriodSurcharge =
                                                $booking->bookingBilling->is_peak_period_surcharge ?? null;
                                            $isPeakPeriodSurchargeValue = ($isPeakPeriod
                                                    ? 'checked'
                                                    : $isPeakPeriodSurcharge)
                                                ? 'checked'
                                                : '';
                                            $peakPeriodSurcharge =
                                                $booking->bookingBilling->peak_period_surcharge ?? null;
                                            $peakPeriodSurchargeValue = ($peakPeriodSurcharge
                                                    ? $peakPeriodSurcharge
                                                    : @$billingAgreement->peak_period_surcharge)
                                                ? @$billingAgreement->peak_period_surcharge
                                                : null;
                                        @endphp
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_peak_period_surcharge" id="is-peak-period-surcharge"
                                                            {{ $isPeakPeriodSurchargeValue }}>
                                                        <label for="is-peak-period-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="peak-period-surcharge"
                                                    class="col-sm-4 col-form-label py-0">Peak
                                                    Period
                                                    Surcharge</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="peak-period-charge-description"
                                                        name="peak_period_charge_description"
                                                        value="{{ $booking->bookingBilling->peak_period_charge_description ?? null }}"
                                                        class="form-control @error('peak_period_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('peak_period_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="peak-period-surcharge"
                                                            name="peak_period_surcharge"
                                                            value="{{ $peakPeriodSurchargeValue ?? null }}"
                                                            class="form-control @error('peak_period_surcharge') is-invalid @enderror"
                                                            placeholder="Peak Period Surcharge">
                                                    </div>
                                                    @error('peak_period_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    @php
                                                        $isMidNightSurcharge =
                                                            $booking->bookingBilling->is_mid_night_surcharge ?? null;
                                                        $isMidNightSurchargeValue = $isMidNightSurcharge
                                                            ? 'checked'
                                                            : '';
                                                    @endphp
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_mid_night_surcharge" id="is-mid-night-surcharge"
                                                            {{ $isMidNightSurchargeValue }}>
                                                        <label for="is-mid-night-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="mid-night-surcharge"
                                                    class="col-sm-4 col-form-label py-0">Midnight
                                                    Surcharge</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="mid-night-charge-description"
                                                        name="mid_night_charge_description"
                                                        value="{{ $booking->bookingBilling->mid_night_charge_description ?? null }}"
                                                        class="form-control @error('mid_night_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('mid_night_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $seatingCapacity =
                                                            $booking->vehicleType->seating_capacity ?? null;
                                                        $midNightSurchargeValue = null;
                                                        $isMultiplierMidnight = null;
                                                        $midNightSurcharge =
                                                            $booking->bookingBilling->mid_night_surcharge ?? null;
                                                        if ($midNightSurcharge) {
                                                            $midNightSurchargeValue = $midNightSurcharge;
                                                            $isFixedMidnightSurcharge =
                                                                $booking->bookingBilling->is_fixed_midnight_surcharge ??
                                                                null;
                                                            $isMultiplierMidnight = $isFixedMidnightSurcharge
                                                                ? ''
                                                                : 'x';
                                                        } elseif ($seatingCapacity) {
                                                            if ($seatingCapacity <= 23) {
                                                                $midNightSurchargeValue =
                                                                    @$billingAgreement->mid_night_surcharge_23_seats ??
                                                                    null;
                                                                $isMultiplierMidnight =
                                                                    @$billingAgreement->fixed_multiplier_midnight_surcharge_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            } else {
                                                                $midNightSurchargeValue =
                                                                    @$billingAgreement->midnight_surcharge_greater_then_23_seats ??
                                                                    null;
                                                                $isMultiplierMidnight =
                                                                    @$billingAgreement->fixed_multiplier_midnight_surcharge_greater_then_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            }
                                                        }
                                                    @endphp
                                                    <input type="hidden" name="is_fixed_midnight_surcharge"
                                                        id="is-fixed-midnight-surcharge"
                                                        value="{{ $isMultiplierMidnight }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierMidnight ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="mid-night-surcharge"
                                                                name="mid_night_surcharge"
                                                                value="{{ $midNightSurchargeValue ?? null }}"
                                                                class="form-control @error('mid_night_surcharge') is-invalid @enderror"
                                                                placeholder="Midnight Surcharge">
                                                        </div>
                                                    </div>
                                                    @error('mid_night_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isArrWaitingTimeSurcharge =
                                                                $booking->bookingBilling
                                                                    ->is_arr_waiting_time_surcharge ?? null;
                                                            $isArrWaitingTimeSurchargeValue = $isArrWaitingTimeSurcharge
                                                                ? 'checked'
                                                                : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_arr_waiting_time_surcharge"
                                                            id="is-arr-waiting-time-surcharge"
                                                            {{ $isArrWaitingTimeSurchargeValue }}>
                                                        <label for="is-arr-waiting-time-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="arrivel-waiting-time"
                                                    class="col-sm-4 col-form-label py-0">Arrival
                                                    Waiting
                                                    Time Surcharge</label>
                                                <div class="col-sm-4 text-sm">
                                                    <input type="text" id="'arrivel-waiting-charge-description"
                                                        name="arrivel_waiting_charge_description"
                                                        value="{{ $booking->bookingBilling->arrivel_waiting_charge_description ?? null }}"
                                                        class="form-control @error('arrivel_waiting_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('arrivel_waiting_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $isMultiplierArrivalWating = null;
                                                        $arrivalWatingValue = null;
                                                        $isFixedArrivalWaitingSurcharge =
                                                            $booking->bookingBilling
                                                                ->is_fixed_arrival_waiting_surcharge ?? null;
                                                        $arrivalWaitingSurcharge =
                                                            $booking->bookingBilling->arrivel_waiting_time_surcharge ??
                                                            null;
                                                        if ($arrivalWaitingSurcharge) {
                                                            $isMultiplierArrivalWating = $isFixedArrivalWaitingSurcharge
                                                                ? ''
                                                                : 'x';
                                                            $arrivalWatingValue = $arrivalWaitingSurcharge;
                                                        } else {
                                                            $arrivalWaiting =
                                                                @$billingAgreement->fixed_multiplier_arrivel_waiting_time ??
                                                                null;
                                                            $isMultiplierArrivalWating =
                                                                $arrivalWaiting === 'MULTIPLIER' ? 'x' : '';
                                                            $arrivalWatingValue =
                                                                @$billingAgreement->arrivel_waiting_time ?? null;
                                                        }

                                                    @endphp
                                                    <input type="hidden" id="is-fixed-arrival-waiting-surcharge"
                                                        name="is_fixed_arrival_waiting_surcharge"
                                                        value="{{ $isMultiplierArrivalWating }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierArrivalWating ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="arrivel-waiting-time"
                                                                name="arrivel_waiting_time_surcharge"
                                                                value="{{ $arrivalWatingValue ?? null }}"
                                                                class="form-control @error('arrivel_waiting_time_surcharge') is-invalid @enderror"
                                                                placeholder="Arrival Waiting Time Surcharge">
                                                        </div>
                                                    </div>
                                                    @error('arrivel_waiting_time_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isOutsideCitySurcharge =
                                                                $booking->bookingBilling->is_outside_city_surcharge ??
                                                                null;
                                                            $isWithinRecords = false;
                                                            if ($booking->pick_up_location) {
                                                                $isWithinRecords = App\CustomHelper::isAddressWithinRecords(
                                                                    $booking->pick_up_location,
                                                                );
                                                                if (!$isWithinRecords) {
                                                                    if ($booking->drop_of_location) {
                                                                        $isWithinRecords = App\CustomHelper::isAddressWithinRecords(
                                                                            $booking->drop_of_location,
                                                                        );
                                                                    }
                                                                }
                                                            }
                                                            $isOutsideCitySurchargeValue = ($isWithinRecords
                                                                    ? 'checked'
                                                                    : $isOutsideCitySurcharge)
                                                                ? 'checked'
                                                                : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_outside_city_surcharge" id="is-out-of-city-surcharge"
                                                            {{ $isOutsideCitySurchargeValue }}>
                                                        <label for="is-out-of-city-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="out-of-city-surcharge"
                                                    class="col-sm-4 col-form-label py-0">Out of
                                                    City
                                                    Surcharge</label>
                                                <div class="col-sm-4 text-sm">
                                                    <input type="text" id="'outside-city-charge-description"
                                                        name="outside_city_charge_description"
                                                        value="{{ $booking->bookingBilling->outside_city_charge_description ?? null }}"
                                                        class="form-control @error('outside_city_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('outside_city_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $outCitySurchargeValue = null;
                                                        $isMultiplierOutCity = null;
                                                        $outsideCitySurcharge =
                                                            $booking->bookingBilling->outside_city_surcharge ?? null;
                                                        if ($outsideCitySurcharge) {
                                                            $outCitySurchargeValue = $outsideCitySurcharge;
                                                            $isFixedOutsideCitySurcharge =
                                                                $booking->bookingBilling
                                                                    ->is_fixed_outside_city_surcharge ?? null;
                                                            $isMultiplierOutCity = $isFixedOutsideCitySurcharge
                                                                ? ''
                                                                : 'x';
                                                        } elseif ($seatingCapacity) {
                                                            if ($seatingCapacity <= 23) {
                                                                $outCitySurchargeValue =
                                                                    @$billingAgreement->outside_city_surcharge_23_seats ??
                                                                    null;
                                                                $isMultiplierOutCity =
                                                                    @$billingAgreement->fixed_multiplier_outside_city_surcharge_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            } else {
                                                                $outCitySurchargeValue =
                                                                    @$billingAgreement->outside_city_surcharge_greater_then_23_seats ??
                                                                    null;
                                                                $isMultiplierOutCity =
                                                                    @$billingAgreement->fixed_multiplier_outside_city_surcharge_greater_then_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            }
                                                        }
                                                    @endphp
                                                    <input type="hidden" id="is-fixed-out-of-city-surcharge"
                                                        name="is_fixed_outside_city_surcharge"
                                                        value="{{ $isMultiplierOutCity }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierOutCity ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="out-of-city-surcharge"
                                                                name="outside_city_surcharge"
                                                                value="{{ $outCitySurchargeValue ?? null }}"
                                                                class="form-control @error('outside_city_surcharge') is-invalid @enderror"
                                                                placeholder="Out of City Surcharge">
                                                        </div>
                                                    </div>
                                                    @error('outside_city_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isLastMinuteSurcharge =
                                                                $booking->bookingBilling->is_last_minute_surcharge ??
                                                                null;
                                                            $isLastMinuteSurchargeValue = $isLastMinuteSurcharge
                                                                ? 'checked'
                                                                : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_last_minute_surcharge"
                                                            id="is-last-minute-booking-surcharge"
                                                            {{ $isLastMinuteSurchargeValue }}>
                                                        <label for="is-last-minute-booking-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="last-minute-surcharge"
                                                    class="col-sm-4 col-form-label py-0">Additional Charges</label>
                                                <div class="col-sm-4 text-sm">
                                                    <input type="text" id="'last-minute-charge-description"
                                                        name="last_minute_charge_description"
                                                        value="{{ $booking->bookingBilling->last_minute_charge_description ?? null }}"
                                                        class="form-control @error('last_minute_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('last_minute_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $lastMinuteSurchargeValue = null;
                                                        $isMultiplierLastMinute = null;
                                                        $lastMinuteSurcharge =
                                                            $booking->bookingBilling->last_minute_surcharge ?? null;
                                                        if ($lastMinuteSurcharge) {
                                                            $lastMinuteSurchargeValue = $lastMinuteSurcharge;
                                                            $isFixedLastMinuteSurcharge =
                                                                $booking->bookingBilling
                                                                    ->is_fixed_last_minute_surcharge ?? null;
                                                            $isMultiplierLastMinute = $isFixedLastMinuteSurcharge
                                                                ? ''
                                                                : 'x';
                                                        } elseif ($seatingCapacity) {
                                                            if ($seatingCapacity <= 23) {
                                                                $lastMinuteSurchargeValue =
                                                                    @$billingAgreement->last_min_request_23_seats ??
                                                                    null;
                                                                $isMultiplierLastMinute =
                                                                    @$billingAgreement->fixed_multiplier_last_min_request_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            } else {
                                                                $lastMinuteSurchargeValue =
                                                                    @$billingAgreement->last_min_request_greater_then_23_seats ??
                                                                    null;
                                                                $isMultiplierLastMinute =
                                                                    @$billingAgreement->fixed_multiplier_last_min_request_greater_then_23_seats ===
                                                                    'MULTIPLIER'
                                                                        ? 'x'
                                                                        : '';
                                                            }
                                                        }
                                                    @endphp
                                                    <input type="hidden" id="is-fixed-last-minute-surcharge"
                                                        name="is_fixed_last_minute_surcharge"
                                                        value="{{ $isMultiplierLastMinute }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierLastMinute ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="last-minute-surcharge"
                                                                name="last_minute_surcharge"
                                                                value="{{ $lastMinuteSurchargeValue ?? null }}"
                                                                class="form-control @error('last_minute_surcharge') is-invalid @enderror"
                                                                placeholder="Last Minute Booking Surcharge">
                                                        </div>
                                                    </div>
                                                    @error('last_minute_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isAdditionalStopSurcharge = !empty($booking->bookingBilling->is_additional_stop_surcharge) || !empty($booking->additional_stops) ? true : false;
                                                            $isAdditionalStopSurchargeValue = $isAdditionalStopSurcharge
                                                                ? 'checked'
                                                                : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_additional_stop_surcharge"
                                                            id="is-additional-stop-charge"
                                                            {{ $isAdditionalStopSurchargeValue }}>
                                                        <label for="is-additional-stop-charge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="additional-stop-surcharge"
                                                    class="col-sm-4 col-form-label py-0">Additional
                                                    Stop Charges</label>
                                                <div class="col-sm-4 text-sm">
                                                    <input type="text" id="'additional-charge-description"
                                                        name="additional_charge_description"
                                                        value="{{ $booking->bookingBilling->additional_charge_description ?? null }}"
                                                        class="form-control @error('additional_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('additional_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                    $isMultiplierAdditionalStopCharge = null;
                                                        $additionalStopChargeValue = null;
                                                        $additionalStopSurcharge = $booking->bookingBilling->additional_stop_surcharge ?? null;
                                                        $additional_stop_charges_from_stops = !empty($booking->additional_stops) && ($booking->service_type_id === 1 || $booking->service_type_id === 2 || $booking->service_type_id === 3) ? number_format(count(explode("||", $booking->additional_stops)) * 5, 2, '.', '') : number_format(0, 2, '.', '');
                                                        if ($additionalStopSurcharge) {
                                                            $additionalStopChargeValue = $additionalStopSurcharge + $additional_stop_charges_from_stops;
                                                            $isFixedAdditionalStopSurcharge = $booking->bookingBilling->is_fixed_additional_stop_surcharge ?? null;
                                                            $isMultiplierAdditionalStopCharge = $isFixedAdditionalStopSurcharge
                                                                ? ''
                                                                : 'x';
                                                        } else {
                                                            $additionalStopChargeValue =
                                                                $billingAgreement->additional_stop ?? 0;
                                                            $additionalStopChargeValue = $additionalStopChargeValue + $additional_stop_charges_from_stops;
                                                            $AdditionalStopCharge =
                                                                $billingAgreement->fixed_multiplier_additional_stop ??
                                                                null;
                                                            $isMultiplierAdditionalStopCharge =
                                                                $AdditionalStopCharge === 'MULTIPLIER' ? 'x' : '';
                                                        }
                                                    @endphp
                                                    <input type="hidden" id="is-fixed-additional-stop-surcharge"
                                                        name="is_fixed_additional_stop_surcharge"
                                                        value="{{ $isMultiplierAdditionalStopCharge }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierAdditionalStopCharge ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="additional-stop-surcharge"
                                                                name="additional_stop_surcharge"
                                                                value="{{ $additional_stop_charges_from_stops ?? null }}"
                                                                class="form-control @error('additional_stop_surcharge') is-invalid @enderror"
                                                                placeholder="Additional Stop Charges" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                        </div>
                                                    </div>
                                                    @error('additional_stop_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isMiscSurcharge =
                                                                $booking->bookingBilling->is_misc_surcharge ?? null;
                                                            $isMiscSurchargeValue = $isMiscSurcharge ? 'checked' : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="is_misc_surcharge" id="is-misc-surcharge"
                                                            {{ $isMiscSurchargeValue }}>
                                                        <label for="is-misc-surcharge"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="misc-surcharge" class="col-sm-4 col-form-label py-0">Misc
                                                    Charges</label>
                                                <div class="col-sm-4 text-sm">
                                                    <input type="text" id="'misc-charge-description"
                                                        name="misc_charge_description"
                                                        value="{{ $booking->bookingBilling->misc_charge_description ?? null }}"
                                                        class="form-control @error('misc_charge_description') is-invalid @enderror"
                                                        placeholder="Description" autocomplete="off">
                                                    @error('misc_charge_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-3">
                                                    @php
                                                        $isMultiplierMiscCharge = null;
                                                        $miscChargesValue = null;
                                                        $miscSurcharge =
                                                            $booking->bookingBilling->misc_surcharge ?? null;
                                                        if ($miscSurcharge) {
                                                            $miscChargesValue = $miscSurcharge;
                                                            $isFixedMiscSurcharge =
                                                                $booking->bookingBilling->is_fixed_misc_surcharge ??
                                                                null;
                                                            $isMultiplierMiscCharge = $isFixedMiscSurcharge ? '' : 'x';
                                                        } else {
                                                            $miscChargesValue = $billingAgreement->misc_charges ?? null;
                                                            $miscCharge =
                                                                $billingAgreement->fixed_multiplier_misc_charges ??
                                                                null;
                                                            $isMultiplierMiscCharge =
                                                                $miscCharge === 'MULTIPLIER' ? 'x' : '';
                                                        }
                                                    @endphp
                                                    <input type="hidden" id="is-fixed-misc-surcharge"
                                                        name="is_fixed_misc_surcharge"
                                                        value="{{ $isMultiplierMiscCharge }}" />
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isMultiplierAdditionalStopCharge ? '<i class="fas fa-times"></i>' : '<i class="fas fa-dollar-sign"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="misc-surcharge"
                                                                name="misc_surcharge"
                                                                value="{{ $miscChargesValue ?? null }}"
                                                                class="form-control @error('misc_surcharge') is-invalid @enderror"
                                                                placeholder="Misc Charges">
                                                        </div>
                                                    </div>
                                                    @error('misc_surcharge')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </li>
                                        @php
                                            $extraChildSeatChargesDiv = ($booking->child_seat_required == 'yes' && $booking->no_of_seats_required == 2) ? 'block' : 'none';
                                            $totalBillingCharges = $booking->bookingBilling->total_charge ?? 0;
                                            if($booking->child_seat_required == 'yes' && $booking->no_of_seats_required == 2)
                                            {
                                                $totalBillingCharges =  (int)($totalBillingCharges) + 10;
                                            }
                                        @endphp
                                        <li class="list-group-item" style="display: {{$extraChildSeatChargesDiv}}" id="extra-child-seat-charges-div">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1">
                                                    <div class="custom-control custom-checkbox">
                                                        @php
                                                            $isExtraChildSeatMoreThanOne = $booking->child_seat_required == 'yes' && $booking->no_of_seats_required == 2 ?? null;
                                                            $isExtraChildSeatMoreThanOneValue = $isExtraChildSeatMoreThanOne
                                                                ? 'checked'
                                                                : '';
                                                        @endphp
                                                        <input class="custom-control-input" type="checkbox"
                                                            name="extra_child_seat_charges"
                                                            id="extra_child_seat_charges"
                                                            {{ $isExtraChildSeatMoreThanOneValue }}>
                                                        <label for="extra_child_seat_charges"
                                                            class="custom-control-label"></label>
                                                    </div>
                                                </div>
                                                <label for="extra-child-seat-charges"
                                                    class="col-sm-4 col-form-label py-0">Extra Child Seat</label>
                                                <div class="col-sm-4 text-sm">
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    {!! $isExtraChildSeatMoreThanOne ? '<i class="fas fa-dollar-sign"></i>' : '<i class="fas fa-times"></i>' !!}
                                                                </span>
                                                            </div>
                                                            <input type="text" id="extra-child-seat-charges"
                                                                name="extra_seat_child_charge"
                                                                value="10.00"
                                                                class="form-control"
                                                                placeholder="Extra Child Seat Charges" {{$loggedUserType === 'client-staff' || $loggedUserType === 'client-admin' ? 'readonly' : ''}}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group row row-gap-2 mb-0">
                                                <div class="col-sm-1"></div>
                                                <div class="col-sm-5 text-left"><span class="bold">Total</span></div>
                                                <div class="col-sm-6 text-right"><span
                                                        id="total-charges">{{ $booking->status == 'CANCELLED' ? 0 : $totalBillingCharges; }}</span>
                                                </div>
                                                <input type="hidden" name="booking_status_for_total_charges" id="booking-status-for-total-charges"
                                                    value="{{ $booking->status }}" />
                                                <input type="hidden" name="total_charge" id="total-charge"
                                                    value="{{ $booking->status == 'CANCELLED' ? 0 : $totalBillingCharges; }}" />
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="head-sm medium">Booking Logs</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @include('admin.logs.partials.logs')
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </form>
    @include('modals.blackout-period-modal')
    <style>
        .pac-container {
            margin-top: -55px;
        }
    </style>
    @vite(['resources/js/EditBookings.js'])
    <script>
        const props = {
            routes: {
                deleteBookings: "{{ route('delete-bookings') }}",
                cancelBooking: "{{ route('cancel-booking') }}",
                corporateFareCharges: "{{ route('get-corporate-fare-charges') }}",
                baseUrl: "{{ url('/') }}"
            },
            peakPeriods: @json($peakPeriods),
            driverOffDays: @json($driverOffDays),
            drivers: @json($drivers),
            clients: @json($clients),
            bookingCreatedBy: @json($booking->created_by_id),
            loggedUser:@json(Auth::user()),
        }
    </script>
@endsection
