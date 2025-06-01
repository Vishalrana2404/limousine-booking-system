@php
    $user = Auth::user();
    $userTypeSlug = $user->userType->slug ?? null;
@endphp
<form id="createMultipleBookingForm" method="post" action="{{ route('save-multiple-booking') }}"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="multiple_booking" value="1">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive custom-table">
                <table id="createMultipleBokkingTable"
                    class="table table-head-fixed text-nowrap table-hover m-0 xl-table" style="width:4000px !important">
                    <thead>
                        <tr>
                            <th>Type Of Service</th>
                            @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                <th class="cell-min-width-200">Corporate </th>
                            @else
                                @if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                                    <th class="cell-min-width-200">Corporate </th>
                                @endif
                            @endif
                            <th class="cell-min-width-200">Event </th>
                            <th>Date </th>
                            <th>Time</th>
                            <th>To Be Advised</th>
                            <th>Flight</th>
                            <th>Guest Name</th>
                            <th class="cell-min-width-400"><span title="Code without + sign" class="fa fa-info-circle"
                                    aria-hidden="true"></span>Guest Contact</th>
                            <th>No. Of Pax</th>
                            <th>No. Of Bags</th>
                            <th>Hrs</th>
                            <th>Departure Time</th>
                            <th>Pick Up</th>
                            <th>Drop Off</th>
                            <th>Additional Stop</th>
                            <th>Vehicle</th>
                            <!-- <th>Class</th> -->
                            <th>Instructions</th>
                            <th>Child Seat Required</th>
                            <th>No Of Child Seat Required</th>
                            <th>Child 1 Age</th>
                            <th>Child 2 Age</th>
                            <th>Meet And Greet</th>
                            <th><span title="Acceptable Formats: jpg, jpeg, png, gif, doc, docx, txt, pdf, xls, xlsx"
                                    class="fa fa-info-circle" aria-hidden="true"></span> Attachment</th>
                            <th>Cross Border Service</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (old('multiple_pickup_date', ['']) as $index => $pickup)
                            @php

                                switch ((int) old('multiple_service_type_id.' . $index)) {
                                    case 1:
                                        $pickUpLocationDropdown = 'block';
                                        $pickupLocationTextBox = 'none';
                                        $departureTimeField = 'none';
                                        $crossBorderField = 'none';
                                        $dropoffLocationDropdown = 'none';
                                        $dropOffLocationTextBox = 'block';
                                        $noOfHoursTextBox = 'none';
                                        $flightDetailDiv = 'block';
                                        $totalPaxDiv = 'block';
                                        $totalLuggageDiv = 'block';
                                        break;
                                    case 2:
                                        $pickUpLocationDropdown = 'none';
                                        $pickupLocationTextBox = 'block';
                                        $departureTimeField = 'none';
                                        $crossBorderField = 'none';
                                        $dropoffLocationDropdown = 'none';
                                        $dropOffLocationTextBox = 'block';
                                        $noOfHoursTextBox = 'none';
                                        $flightDetailDiv = 'block';
                                        $totalPaxDiv = 'block';
                                        $totalLuggageDiv = 'block';
                                        break;
                                    case 3:
                                        $pickUpLocationDropdown = 'none';
                                        $pickupLocationTextBox = 'block';
                                        $departureTimeField = 'block';
                                        $crossBorderField = 'none';
                                        $dropoffLocationDropdown = 'block';
                                        $dropOffLocationTextBox = 'none';
                                        $noOfHoursTextBox = 'none';
                                        $flightDetailDiv = 'block';
                                        $totalPaxDiv = 'block';
                                        $totalLuggageDiv = 'block';
                                        break;
                                    case 4:
                                        $pickUpLocationDropdown = 'none';
                                        $pickupLocationTextBox = 'block';
                                        $departureTimeField = 'none';
                                        $crossBorderField = 'block';
                                        $dropoffLocationDropdown = 'none';
                                        $dropOffLocationTextBox = 'block';
                                        $noOfHoursTextBox = 'block';
                                        $flightDetailDiv = 'block';
                                        $totalPaxDiv = 'block';
                                        $totalLuggageDiv = 'block';
                                        break;
                                    case 5:
                                        $pickUpLocationDropdown = 'none';
                                        $pickupLocationTextBox = 'block';
                                        $departureTimeField = 'none';
                                        $crossBorderField = 'none';
                                        $dropoffLocationDropdown = 'none';
                                        $dropOffLocationTextBox = 'block';
                                        $noOfHoursTextBox = 'none';
                                        $flightDetailDiv = 'none';
                                        $totalPaxDiv = 'none';
                                        $totalLuggageDiv = 'none';
                                        break;
                                    default:
                                        $pickUpLocationDropdown = 'none';
                                        $pickupLocationTextBox = 'block';
                                        $departureTimeField = 'none';
                                        $crossBorderField = 'none';
                                        $dropoffLocationDropdown = 'none';
                                        $dropOffLocationTextBox = 'block';
                                        $noOfHoursTextBox = 'none';
                                        $flightDetailDiv = 'block';
                                        $totalPaxDiv = 'block';
                                        $totalLuggageDiv = 'block';
                                }
                                $crossBorder = old('multiple_cross_border.' . $index) ? 'checked' : '';
                                $pickupLocationId = (int) old('multiple_pick_up_location_id.' . $index);
                                if ($pickupLocationId) {
                                    $pickupLocationTextBox = $pickupLocationId === 8 ? 'block' : 'none';
                                }
                                $dropoffLocationId = (int) old('multiple_drop_off_location_id.' . $index);
                                if ($dropoffLocationId) {
                                    if ($dropoffLocationId === 8) {
                                        $pickupLocationTextBox = 'block';
                                        $dropOffLocationTextBox = 'block';
                                    } else {
                                        $dropOffLocationTextBox = 'none';
                                    }
                                }
                                $child_seat_required = old('child_seat_required_' . $index);
                                $noOfSeatsRequired = old('no_of_seats_required_' . $index);
                            @endphp
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_service_type_id[{{ $index }}]"
                                            id="serviceTypeId_{{ $index }}"
                                            class="form-control form-select custom-select multiple_service_type_id serviceTypeId @error('multiple_service_type_id.' . $index) is-invalid @enderror"
                                            autocomplete="off">
                                            <option value="">Select one</option>
                                            @foreach ($serviceTypes as $serviceType)
                                                @if (old('multiple_service_type_id.' . $index) == $serviceType->id)
                                                    <option value="{{ $serviceType->id }}" selected>
                                                        {{ $serviceType->name }}</option>
                                                @else
                                                    <option value="{{ $serviceType->id }}">{{ $serviceType->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('multiple_service_type_id.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <td>
                                        <div class="form-group">
                                            <select name="multiple_client_id[{{ $index }}]"
                                                id="clientId_{{ $index }}"
                                                class="form-control form-select multiple_client_id custom-select @error('multiple_client_id.' . $index) is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select One</option>
                                                @foreach ($hotelClients as $hotelClient)
                                                    @php
                                                        $client = $hotelClient->client ?? null;
                                                    @endphp
                                                    @if ($client)
                                                        @if (old('multiple_client_id.' . $index) == $client->id)
                                                            <option value="{{ $client->id }}" selected>
                                                                {{ !empty($hotelClient->name) ? $hotelClient->name : '' }}</option>
                                                        @else
                                                            <option value="{{ $client->id }}">
                                                                {{ !empty($hotelClient->name) ? $hotelClient->name : '' }}
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('multiple_client_id.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </td>
                                @else                                    
                                    @if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                                        <td>
                                            <div class="form-group">
                                                <select name="multiple_client_id[{{ $index }}]"
                                                    id="clientId_{{ $index }}"
                                                    class="form-control form-select multiple_client_id custom-select @error('multiple_client_id.' . $index) is-invalid @enderror"
                                                    autocomplete="off">
                                                    <option value="">Select One</option>
                                                    @foreach ($multipleCorporatesHotelData as $hotelClient)
                                                        @php
                                                            $client = $hotelClient->client ?? null;
                                                        @endphp
                                                        @if ($client)
                                                            @if (old('multiple_client_id.' . $index) == $client->id)
                                                                <option value="{{ $client->id }}" selected>
                                                                    {{ !empty($hotelClient->name) ? $hotelClient->name : '' }}</option>
                                                            @else
                                                                <option value="{{ $client->id }}">
                                                                    {{ !empty($hotelClient->name) ? $hotelClient->name : '' }}
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('multiple_client_id.' . $index)
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </td>
                                    @endif
                                @endif
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_event_id[{{ $index }}]"
                                            id="eventId_{{ $index }}"
                                            class="form-control form-select multiple_event_id custom-select @error('multiple_event_id.' . $index) is-invalid @enderror"
                                            autocomplete="off">
                                            <option value="">Select An Event</option>
                                            @if(in_array($userTypeSlug, ['client-staff', 'client-admin']))
                                                @if(!empty($events))
                                                    @foreach ($events as $event)
                                                        @if (old('event_id') == $event->id)
                                                            <option value="{{ $event->id }}" selected>{{ $event->name }}</option>
                                                        @else
                                                        <option value="{{ $event->id }}">{{$event->name  }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>
                                        @error('multiple_event_id.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group date" id="pickupDatePicker_{{ $index }}"
                                            data-target-input="nearest">
                                            <input type="text" name="multiple_pickup_date[{{ $index }}]"
                                                value="{{ old('multiple_pickup_date.' . $index) }}"
                                                id="pickupDate_{{ $index }}"
                                                class="form-control multiple_pickup_date datetimepicker-input @error('multiple_pickup_date.' . $index) is-invalid @enderror"
                                                data-target="#pickupDatePicker_{{ $index }}"
                                                placeholder="dd/mm/yyyy" autocomplete="off" autofocus />
                                            <div class="input-group-append"
                                                data-target="#pickupDatePicker_{{ $index }}"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"
                                                        aria-hidden="true"></i></div>
                                            </div>
                                        </div>
                                        @error('multiple_pickup_date.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group date" id="pickupTimePicker_{{ $index }}"
                                            data-target-input="nearest">
                                            <input type="text" name="multiple_pickup_time[{{ $index }}]"
                                                value="{{ old('multiple_pickup_time.' . $index) }}"
                                                id="pickupTime_{{ $index }}"
                                                class="form-control multiple_pickup_time datetimepicker-input @error('multiple_pickup_time.' . $index) is-invalid @enderror"
                                                data-target="#pickupTimePicker_{{ $index }}" placeholder="HH:MM"
                                                autocomplete="off" autofocus />
                                            <div class="input-group-append"
                                                data-target="#pickupTimePicker_{{ $index }}"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"
                                                        aria-hidden="true"></i></div>
                                            </div>
                                        </div>
                                        @error('multiple_pickup_time.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group" style="margin-bottom : 0 !important; text-align:center;">
                                        <input type="checkbox" class="multiple-pickup-time-to-be-advised" name="multiple_pickup_time_to_be_advised[{{$index}}]" id="multiple_pickup_time_to_be_advised_{{$index}}"
                                        style="width: 18px; height: 18px; border: 2px solid #a6acaf; border-radius: 4px; 
                                        box-shadow: 0px 0px 5px rgba(10,20,30,50%); cursor: pointer; 
                                        appearance: none; outline: none; background-color: #fff; display: inline-block;"
                                        onclick="this.style.backgroundColor = this.checked ? '#0e161e' : '#fff'; 
                                        this.style.borderColor = this.checked ? '#c3c3c3' : '#a6acaf'; 
                                        this.style.boxShadow = this.checked ? '0px 0px 8px rgba(15,20,26,80%)' : '0px 0px 5px rgba(10,20,30,50%)';">
                                        @error('multiple_pickup_time.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group flightDetailDiv" style="display:{{ $flightDetailDiv }};">
                                        <input type="text" id="flightDetail_{{ $index }}"
                                            name="multiple_flight_detail[{{ $index }}]"
                                            value="{{ old('multiple_flight_detail.' . $index) }}"
                                            class="form-control multiple_flight_detail @error('multiple_flight_detail.' . $index) is-invalid @enderror"
                                            placeholder="Flight Details" autocomplete="off">
                                        @error('multiple_flight_detail.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group guestNameContainer" style="display:flex; align-items: center; justify-content: center">
                                        <input type="text" id="guestName_{{ $index }}_0"
                                            name="multiple_guest_name[{{ $index }}][0]"
                                            value="{{ old('multiple_guest_name[.' . $index.'][0]') }}"
                                            class="form-control multiple_guest_name @error('multiple_guest_name[.' . $index.'][0]') is-invalid @enderror"
                                            placeholder="Name of Guest(s)" autocomplete="off" autofocus>
                                            <button type="button" class="col-sm-3 multiple-add-guest" style="bottom: 8px;"><span class="fa fa-plus mt-3"></span></button>
                                    </div>
                                    @error('multiple_guest_name.' . $index)
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </td>
                                <td>
                                    <div class="d-flex gap-2 guestContactContainer">
                                        <div class="form-group w-50">
                                            <input type="text" id="country_code_{{ $index }}_0"
                                                name="multiple_country_code[{{ $index }}][0]"
                                                value="{{ old('multiple_country_code[.' . $index.'][0]') }}"
                                                class="form-control multiple_country_code @error('multiple_country_code[.' . $index.'][0]') is-invalid @enderror"
                                                placeholder="code" autocomplete="off" autofocus>
                                            @error('multiple_country_code.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="phone_{{ $index }}_0"
                                                name="multiple_phone[{{ $index }}][0]"
                                                value="{{ old('multiple_phone[.' . $index.'][0]') }}"
                                                class="form-control multiple_phone @error('multiple_phone[.' . $index.'][0]') is-invalid @enderror"
                                                placeholder="Contact" autocomplete="off">
                                            @error('multiple_phone.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group totalPaxDiv" style="display:{{ $totalPaxDiv }};">
                                        <input type="text" id="totalPax_{{ $index }}"
                                            name="multiple_total_pax[{{ $index }}]"
                                            value="{{ old('multiple_total_pax.' . $index) }}"
                                            class="form-control multiple_total_pax @error('multiple_total_pax.' . $index) is-invalid @enderror"
                                            placeholder="Total Pax" autocomplete="off" autofocus>
                                        @error('multiple_total_pax.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group totalLuggageDiv" style="display:{{ $totalLuggageDiv }};">
                                        <input type="text" id="totalLuggage_{{ $index }}"
                                            name="multiple_total_luggage[{{ $index }}]"
                                            value="{{ old('multiple_total_luggage.' . $index) }}"
                                            class="form-control multiple_total_luggage @error('multiple_total_luggage.' . $index) is-invalid @enderror"
                                            placeholder="Total Luggage" autocomplete="off" autofocus>
                                        @error('multiple_total_luggage.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group noOfHoursTextBox"
                                        style="display:{{ $noOfHoursTextBox }};">
                                        <input type="text" id="noOfHours_{{ $index }}"
                                            name="multiple_no_of_hours[{{ $index }}]"
                                            value="{{ old('multiple_no_of_hours.' . $index) }}"
                                            class="form-control multiple_no_of_hours @error('multiple_no_of_hours.' . $index) is-invalid @enderror"
                                            placeholder="No. of Hours" autocomplete="off">
                                        @error('multiple_no_of_hours.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group departureTimeField"
                                        style="display:{{ $departureTimeField }};">
                                        <div class="input-group date" id="departureTimePicker_{{ $index }}"
                                            data-target-input="nearest">
                                            <input type="text" name="multiple_departure_time[{{ $index }}]"
                                                id="departureTime_{{ $index }}"
                                                value="{{ old('multiple_departure_time.' . $index) }}"
                                                class="form-control datetimepicker-input multiple_departure_time @error('multiple_departure_time.' . $index) is-invalid @enderror"
                                                data-target="#departureTimePicker_{{ $index }}"
                                                placeholder="dd/mm/yyyy HH:MM" autocomplete="off" autofocus />
                                            <div class="input-group-append"
                                                data-target="#departureTimePicker_{{ $index }}"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"
                                                        aria-hidden="true"></i></div>
                                            </div>
                                        </div>
                                        @error('multiple_departure_time.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>

                                    <div class="d-flex gap-2">
                                        <div class="form-group pickUpLocationDropdown"
                                            style="display:{{ $pickUpLocationDropdown }};">
                                            <select name="multiple_pick_up_location_id[{{ $index }}]"
                                                id="pickupLocationId_{{ $index }}"
                                                class="form-control form-select custom-select multiple_pick_up_location_id pickupLocationId @error('multiple_pick_up_location_id.' . $index) is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                @foreach ($locations as $location)
                                                    @if (old('multiple_pick_up_location_id.' . $index) == $location->id)
                                                        <option value="{{ $location->id }}" selected>
                                                            {{ $location->name }}</option>
                                                    @else
                                                        <option value="{{ $location->id }}">{{ $location->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('multiple_pick_up_location_id.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group pickUpLocationTextbox"
                                            style="display:{{ $pickupLocationTextBox }};">
                                            <input type="text" id="pickupLocationtext_{{ $index }}"
                                                name="multiple_pick_up_location[{{ $index }}]"
                                                value="{{ old('multiple_pick_up_location.' . $index) }}"
                                                class="form-control multiple_pick_up_location @error('multiple_pick_up_location.' . $index) is-invalid @enderror"
                                                placeholder="Pick Up Location" autocomplete="off">
                                            @error('multiple_pick_up_location.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <div class="form-group dropOffLocationDropdown"
                                            style="display:{{ $dropoffLocationDropdown }};">
                                            <select name="multiple_drop_off_location_id[{{ $index }}]"
                                                id="dropOffLocationId_{{ $index }}"
                                                class="form-control form-select custom-select multiple_drop_off_location_id dropOffLocationId @error('multiple_drop_off_location_id.' . $index) is-invalid @enderror"
                                                autocomplete="off">
                                                <option value="">Select one</option>
                                                @foreach ($locations as $location)
                                                    @if (old('multiple_drop_off_location_id') === $location->id)
                                                        <option value="{{ $location->id }}" selected>
                                                            {{ $location->name }}</option>
                                                    @else
                                                        <option value="{{ $location->id }}">{{ $location->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('multiple_drop_off_location_id.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group dropOffLocationTextBox"
                                            style="display:{{ $dropOffLocationTextBox }};">
                                            <input type="text" id="dropOfLocation_{{ $index }}"
                                                name="multiple_drop_of_location[{{ $index }}]"
                                                value="{{ old('multiple_drop_of_location.' . $index) }}"
                                                class="form-control multiple_drop_of_location @error('multiple_drop_of_location.' . $index) is-invalid @enderror"
                                                placeholder="Drop Off Location" autocomplete="off">
                                            @error('multiple_drop_of_location.' . $index)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 additionalContainer" style="flex-direction: column;">
                                        <div class="additionalStopInput col-sm-12" style="display:flex; align-items: flex-start; justify-content: center;">
                                            <input type="text" id="multipleAdditionalStops_0_0"
                                                name="multiple_additional_stops[0][0]"
                                                class="form-control col-sm-9 multiple_additional_stops"
                                                placeholder="Additional Stop(s)" autocomplete="off" autofocus>
                                            <button type="button" class="col-sm-3 multiple-add-stop"><span class="fa fa-plus mt-3"></span></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_vehicle_type_id[{{ $index }}]"
                                            id="vehicleType_{{ $index }}"
                                            class="form-control form-select custom-select multiple_vehicle_type_id @error('multiple_vehicle_type_id.' . $index) is-invalid @enderror"
                                            autocomplete="off">
                                            <option value="">Select one</option>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                @if (old('multiple_vehicle_type_id.' . $index) == $vehicleType->id)
                                                    <option value="{{ $vehicleType->id }}"
                                                        data-seating-capacity="{{ $vehicleType->seating_capacity }}"
                                                        selected>{{ $vehicleType->name }}
                                                        ({{ $vehicleType->seating_capacity }}s)
                                                    </option>
                                                @else
                                                    <option value="{{ $vehicleType->id }}"
                                                        data-seating-capacity="{{ $vehicleType->seating_capacity }}">
                                                        {{ $vehicleType->name }}
                                                        ({{ $vehicleType->seating_capacity }}s)</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('multiple_vehicle_type_id.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <!-- <td>
                                    <div class="form-group">
                                        <select name="multiple_vehicle_type_id[{{ $index }}]"
                                            id="vehicleType_{{ $index }}"
                                            class="form-control form-select custom-select multiple_vehicle_type_id @error('multiple_vehicle_type_id.' . $index) is-invalid @enderror"
                                            autocomplete="off">
                                            <option value="">Select one</option>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                @if (old('multiple_vehicle_type_id.' . $index) == $vehicleType->id)
                                                    <option value="{{ $vehicleType->id }}"
                                                        data-seating-capacity="{{ $vehicleType->seating_capacity }}"
                                                        selected>{{ $vehicleType->name }}
                                                        ({{ $vehicleType->seating_capacity }}s)
                                                    </option>
                                                @else
                                                    <option value="{{ $vehicleType->id }}"
                                                        data-seating-capacity="{{ $vehicleType->seating_capacity }}">
                                                        {{ $vehicleType->name }}
                                                        ({{ $vehicleType->seating_capacity }}s)</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('multiple_vehicle_type_id.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td> -->
                                <td>
                                    <div class="form-group">
                                        <input type="text" id="clientInstructions_{{ $index }}"
                                            name="multiple_client_instructions[{{ $index }}]"
                                            value="{{ old('multiple_client_instructions.' . $index) }}"
                                            class="form-control multiple_client_instructions @error('multiple_client_instructions.' . $index) is-invalid @enderror"
                                            placeholder="Client Instructions" autocomplete="off" autofocus>
                                        @error('multiple_client_instructions.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_child_seat_required[]"
                                            id="child_seat_required_{{ $index }}"
                                            class="form-control form-select custom-select multiple_child_seat_required @error('multiple_child_seat_required.' . $index) is-invalid @enderror"
                                            autocomplete="off">
                                            <option value="yes">Yes</option>
                                            <option value="no" selected>No</option>
                                        </select>
                                        @error('multiple_child_seat_required.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_no_of_seats_required[]"
                                            id="no_of_seats_required_{{ $index }}"
                                            class="form-control form-select custom-select multiple_no_of_seats_required @error('multiple_no_of_seats_required.' . $index) is-invalid @enderror"
                                            autocomplete="off" style="display:none;">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                        @error('multiple_no_of_seats_required.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_child_1_age[]"
                                            id="child_1_age_{{ $index }}"
                                            class="form-control form-select custom-select multiple_child_1_age @error('multiple_child_1_age.' . $index) is-invalid @enderror"
                                            autocomplete="off" style="display:none;">
                                            <option value="<1 yo" selected>&lt;1 yo</option>
                                            <option value="1 yo">1 yo</option>
                                            <option value="2 yo">2 yo</option>
                                            <option value="3 yo">3 yo</option>
                                            <option value="4 yo">4 yo</option>
                                            <option value="5 yo">5 yo</option>
                                            <option value="6 yo">6 yo</option>
                                        </select>
                                        @error('multiple_child_1_age.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_child_2_age[]"
                                            id="child_2_age_{{ $index }}"
                                            class="form-control form-select custom-select multiple_child_2_age @error('multiple_child_2_age.' . $index) is-invalid @enderror"
                                            autocomplete="off" style="display:none;">
                                            <option value="<1 yo" selected>&lt;1 yo</option>
                                            <option value="1 yo">1 yo</option>
                                            <option value="2 yo">2 yo</option>
                                            <option value="3 yo">3 yo</option>
                                            <option value="4 yo">4 yo</option>
                                            <option value="5 yo">5 yo</option>
                                            <option value="6 yo">6 yo</option>
                                        </select>
                                        @error('multiple_child_2_age.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select name="multiple_meet_and_greet[]"
                                            id="meet_and_greet_{{ $index }}"
                                            class="form-control form-select custom-select multiple_meet_and_greet @error('multiple_meet_and_greet.' . $index) is-invalid @enderror"
                                            autocomplete="off" style="display:none;">
                                            <option value="YES" disabled>Yes</option>
                                            <option value="NO" selected>No</option>
                                        </select>
                                        @error('multiple_meet_and_greet.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="file" id="attachment_{{ $index }}"
                                            name="multiple_attachment[{{ $index }}]"
                                            value="{{ old('multiple_attachment.' . $index) }}"
                                            class="form-control multiple_attachment @error('multiple_attachment.' . $index) is-invalid @enderror"
                                            placeholder="File">
                                        @error('multiple_attachment.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check mt-2 crossBorderField"
                                        style="display:{{ $crossBorderField }};">
                                        <input
                                            class="form-check-input multiple_is_cross_border @error('multiple_is_cross_border.' . $index) is-invalid @enderror"
                                            type="checkbox" id="crossBorder_{{ $index }}"
                                            name="multiple_is_cross_border[{{ $index }}]">
                                        <label for="crossBorder_{{ $index }}"
                                            class="form-check-label"></label>
                                        @error('multiple_is_cross_border.' . $index)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($index > 0)
                                        <button type="button" class="remove-booking-row"><span
                                                class="fas fa-times mt-3 text-danger"></span></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="text-right mt-5 pt-3 border-top">
        <button type="submit" id="addMultipleBookingFormButton" class="btn btn-outline-primary mx-2"
            title="Save">Save</button>
    </div>
</form>
