<form id="createBookingForm" method="post" action="{{ route('save-booking') }}" enctype="multipart/form-data">
    @csrf
    @php
        switch ((int) old('service_type_id')) {
            case 1:
                $pickupLocationDropdown = 'block';
                $pickupLocationTextBox = 'none';
                $departureTimeField = 'none';
                $crossBorderField = 'none';
                $dropoffLocationDropdown = 'none';
                $dropOffLocationTextBox = 'block';
                $noOfHoursTextBox = 'none';
                $dropOffSpan = 'inline';
                $totalLuggageDiv = 'block';
                $totalPaxDiv = 'block';
                $flightDetailDiv = 'block';
                $totalLuggageSpan = 'inline';
                $totalPaxSpan = 'inline';
                $flightDetailSpan = 'inline';
                break;
            case 2:
                $pickupLocationDropdown = 'none';
                $pickupLocationTextBox = 'block';
                $departureTimeField = 'none';
                $crossBorderField = 'none';
                $dropoffLocationDropdown = 'none';
                $dropOffLocationTextBox = 'block';
                $noOfHoursTextBox = 'none';
                $dropOffSpan = 'inline';
                $totalLuggageSpan = 'inline';
                $totalPaxSpan = 'inline';
                $flightDetailSpan = 'none';
                $totalLuggageDiv = 'block';
                $totalPaxDiv = 'block';
                $flightDetailDiv = 'block';
                break;
            case 3:
                $pickupLocationDropdown = 'none';
                $pickupLocationTextBox = 'block';
                $departureTimeField = 'block';
                $crossBorderField = 'none';
                $dropoffLocationDropdown = 'block';
                $dropOffLocationTextBox = 'none';
                $noOfHoursTextBox = 'none';
                $dropOffSpan = 'inline';
                $totalLuggageDiv = 'block';
                $totalPaxDiv = 'block';
                $flightDetailDiv = 'block';
                $totalLuggageSpan = 'inline';
                $totalPaxSpan = 'inline';
                $flightDetailSpan = 'inline';
                break;
            case 4:
                $pickupLocationDropdown = 'none';
                $pickupLocationTextBox = 'block';
                $departureTimeField = 'none';
                $crossBorderField = 'block';
                $dropoffLocationDropdown = 'none';
                $dropOffLocationTextBox = 'block';
                $noOfHoursTextBox = 'block';
                $dropOffSpan = 'none';
                $totalLuggageDiv = 'block';
                $totalPaxDiv = 'block';
                $flightDetailDiv = 'block';
                $totalLuggageSpan = 'inline';
                $totalPaxSpan = 'inline';
                $flightDetailSpan = 'none';
                break;
            case 5:
                $pickupLocationDropdown = 'none';
                $pickupLocationTextBox = 'block';
                $departureTimeField = 'none';
                $crossBorderField = 'none';
                $dropoffLocationDropdown = 'none';
                $dropOffLocationTextBox = 'block';
                $noOfHoursTextBox = 'none';
                $dropOffSpan = 'inline';
                $totalLuggageDiv = 'none';
                $totalPaxDiv = 'none';
                $flightDetailDiv = 'none';
                $totalLuggageSpan = 'none';
                $totalPaxSpan = 'none';
                $flightDetailSpan = 'none';
                break;
            default:
                $pickupLocationDropdown = 'none';
                $pickupLocationTextBox = 'block';
                $departureTimeField = 'none';
                $crossBorderField = 'none';
                $dropoffLocationDropdown = 'none';
                $dropOffLocationTextBox = 'block';
                $noOfHoursTextBox = 'none';
                $dropOffSpan = 'inline';
                $totalLuggageSpan = 'inline';
                $totalPaxSpan = 'inline';
                $flightDetailSpan = 'none';
                $totalLuggageDiv = 'block';
                $totalPaxDiv = 'block';
                $flightDetailDiv = 'block';
        }
        $crossBorder = old('is_cross_border') ? 'checked' : '';
        $pickupLocationId = (int) old('pick_up_location_id');
        if ($pickupLocationId) {
            $pickupLocationTextBox = $pickupLocationId == 12 ? 'block' : 'none';
        }
        $dropoffLocationId = (int) old('drop_off_location_id');
        if ($dropoffLocationId) {
            if ($dropoffLocationId === 12) {
                $pickupLocationTextBox = 'block';
                $dropOffLocationTextBox = 'block';
            } else {
                $dropOffLocationTextBox = 'none';
            }
        }
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
    @endphp
    <div class="row align-items-center">
        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
            <div class="col-md-4">
                <div class="form-group">
                    <label for="clientId">Corporate <span class="text-danger">*</span></label>
                    <select name="client_id" id="clientId"
                        class="form-control form-select custom-select @error('client_id') is-invalid @enderror"
                        autocomplete="off">
                        <option value="">Select One</option>
                        @foreach ($hotelClients as $hotelClient)
                            @php
                                $client = $hotelClient->client ?? null;
                            @endphp
                            @if ($client)
                                @if (old('client_id') == $client->id)
                                    <option value="{{ $client->id }}" selected>{{ $hotelClient->name }}</option>
                                @else
                                    <option value="{{ $client->id }}">{{$hotelClient->name  }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                    @error('client_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        @else
            @if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="clientId">Corporate <span class="text-danger">*</span></label>
                        <select name="client_id" id="clientId"
                            class="form-control form-select custom-select @error('client_id') is-invalid @enderror"
                            autocomplete="off">
                            <option value="">Select One</option>
                            @foreach ($multipleCorporatesHotelData as $hotelClient)
                                @php
                                    $client = $hotelClient->client ?? null;
                                @endphp
                                @if ($client)
                                    @if (old('client_id') == $client->id)
                                        <option value="{{ $client->id }}" selected>{{ $hotelClient->name }}</option>
                                    @else
                                        <option value="{{ $client->id }}">{{$hotelClient->name  }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                        @error('client_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            @endif
        @endif
        <div class="col-md-4" style="min-height: 102px;">
            <div class="form-group">
                <div id="EventCreate" style="display: flex; align-items: center; justify-content: space-between;">
                    <label for="eventId" style="padding-bottom: 0;">Event</label>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <span class="fa fa-plus mt-3"></span>
                    </button>
                </div>
                <select name="event_id" id="eventId"
                    class="form-control form-select custom-select @error('event_id') is-invalid @enderror"
                    autocomplete="off">
                    <option value="">Select An Event</option>
                    @if(!empty($events))
                        @foreach ($events as $event)
                            @if (old('event_id') == $event->id)
                                <option value="{{ $event->id }}" selected>{{ $event->name }}</option>
                            @else
                            <option value="{{ $event->id }}">{{$event->name  }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
                @error('event_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="serviceTypeId">Type of Service <span class="text-danger">*</span></label>
                <select name="service_type_id" id="serviceTypeId"
                    class="form-control form-select custom-select @error('service_type_id') is-invalid @enderror"
                    autocomplete="off">
                    <option value="">Select One</option>
                    @foreach ($serviceTypes as $serviceType)
                        @if (old('service_type_id') == $serviceType->id)
                            <option value="{{ $serviceType->id }}" selected>{{ $serviceType->name }}</option>
                        @else
                            <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
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
    </div>
    <div class="row align-items-center">
        <div class="col-md-4" id="pickupLocationDropdown" style="display:{{ $pickupLocationDropdown }}; min-height: 125px;">
            <div class="form-group">
                <label for="pickupLocationId">Arrival Pick Up Location </label>
                <select name="pick_up_location_id" id="pickupLocationId"
                    class="form-control form-select custom-select @error('pick_up_location_id') is-invalid @enderror"
                    autocomplete="off">
                    <option value="">Select one</option>
                    @foreach ($locations as $location)
                        @if ((int) old('pick_up_location_id') === $location->id)
                            <option value="{{ $location->id }}" selected>{{ $location->name }}</option>
                        @else
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('pick_up_location_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4" id="pickupLocationTextBox" style="display:{{ $pickupLocationTextBox }}; min-height: 125px;">
            <div class="form-group">
                <label for="pickupLocationtext">Pick Up Location <span class="text-danger">*</span></label>
                <input type="text" id="pickupLocationtext" name="pick_up_location"
                    value="{{ old('pick_up_location') }}"
                    class="form-control @error('pick_up_location') is-invalid @enderror" placeholder="Pick Up Location"
                    autocomplete="off">
                @error('pick_up_location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4 additionalContainers" style="display:none; min-height: 120px;">
            @foreach (old('additional_stops', ['']) as $index => $stop)
                <div class="form-group">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label for="additionalStops_{{$index}}">Second Destination</label>
                        @if ($loop->last)
                            <button type="button" id="addStop"><span class="fa fa-plus"></span></button>
                        @else
                            <button type="button" class="remove-stop"><span
                                    class="fas fa-times text-danger"></span></button>
                        @endif
                    </div>                    
                    <input type="text" id="additionalStops_{{$index}}" name="additional_stops[]"
                        value="{{ $stop }}"
                        class="form-control additional-stops @error('additional_stops.' . $index) is-invalid @enderror" placeholder="Second Destination"
                        autocomplete="off">
                    @error('additional_stops.' . $index)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="mt-2 d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input pickup-dropoff-option" type="checkbox" name="pickup_dropoff[0]" value="pickup" id="pickup_{{ $index }}" checked>
                            <label class="form-check-label" for="pickup_{{ $index }}">Pickup</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input pickup-dropoff-option" type="checkbox" name="pickup_dropoff[0]" value="dropoff" id="dropoff_{{ $index }}">
                            <label class="form-check-label" for="dropoff_{{ $index }}">Dropoff</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-4" id="dropoffLocationDropdown" style="display:{{ $dropoffLocationDropdown }}; min-height: 125px;">
            <div class="form-group">
                <label for="dropoffLocationId">Departure Drop Off Location </label>
                <select name="drop_off_location_id" id="dropoffLocationId"
                    class="form-control form-select custom-select @error('drop_off_location_id') is-invalid @enderror"
                    autocomplete="off">
                    <option value="">Select one</option>
                    @foreach ($locations as $location)
                        @if ((int) old('drop_off_location_id') === $location->id)
                            <option value="{{ $location->id }}" selected>{{ $location->name }}</option>
                        @else
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('drop_off_location_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4" id="dropOffLocationTextBox" style="display:{{ $dropOffLocationTextBox }}; min-height: 125px;">
            <div class="form-group">
                <label for="dropOfLocation">Drop Off Location <span class="text-danger" id="dropOffSpan"
                        style="display:{{ $dropOffSpan }};">*</span></label>
                <input type="text" id="dropOfLocation" name="drop_of_location" value="{{ old('drop_of_location') }}"
                    class="form-control @error('drop_of_location') is-invalid @enderror" placeholder="Drop Off Location"
                    autocomplete="off">
                @error('drop_of_location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row align-items-center">
        
        <div class="row" id="additionalStopsContainer">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold" style="padding-bottom:0">Additional Stops? <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input additional-stops-required @error('additional_stops_required') is-invalid @enderror" type="radio" name="additional_stops_required" id="additionalStopsYes" value="yes" autocomplete="off">
                        <label class="form-check-label" for="additionalStopsYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input additional-stops-required @error('additional_stops_required') is-invalid @enderror" type="radio" name="additional_stops_required" id="additionalStopsNo" value="no" autocomplete="off" checked>
                        <label class="form-check-label" for="additionalStopsNo">No</label>
                    </div>
                </div>
                @error('additional_stops_required')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="vehicleType">Type of Vehicle <span class="text-danger">*</span></label>
                <select name="vehicle_type_id" id="vehicleType"
                    class="form-control form-select custom-select @error('vehicle_type_id') is-invalid @enderror"
                    autocomplete="off">
                    <option value="">Select one</option>
                    @foreach ($vehicleTypes as $vehicleType)
                        @if ((int) old('vehicle_type_id') === $vehicleType->id)
                            <option value="{{ $vehicleType->id }}"
                                data-seating-capacity="{{ $vehicleType->seating_capacity }}" selected>
                                {{ $vehicleType->name }} ({{ $vehicleType->seating_capacity }}s)</option>
                        @else
                            <option value="{{ $vehicleType->id }}"
                                data-seating-capacity="{{ $vehicleType->seating_capacity }}">{{ $vehicleType->name }}
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
        <div class="col-md-4">
            <div class="form-group">
                <label for="pickupDate">Date of pick up <span class="text-danger">*</span></label>
                <div class="input-group date" id="pickupDatePicker" data-target-input="nearest">
                    <input type="text" name="pickup_date" value="{{ old('pickup_date') }}" id="pickupDate"
                        class="form-control datetimepicker-input" data-target="#pickupDatePicker"
                        placeholder="dd/mm/yyyy" autocomplete="off" autofocus />
                    <div class="input-group-append" data-target="#pickupDatePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    </div>
                </div>
                @error('pickup_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <!-- Label with inline flex for proper alignment -->
                <label class="d-flex align-items-center" for="pickupTime">
                    Time of pick up <span class="text-danger ms-1">*</span>
                    <div class="form-check d-flex align-items-center ms-2">
                    <input type="checkbox" name="pickup_time_to_be_advised" id="pickup_time_to_be_advised"
                    style="width: 18px; height: 18px; border: 2px solid #a6acaf; border-radius: 4px; 
                    box-shadow: 0px 0px 5px rgba(10,20,30,50%); cursor: pointer; 
                    appearance: none; outline: none; background-color: #fff; display: inline-block;"
                    onclick="this.style.backgroundColor = this.checked ? '#0e161e' : '#fff'; 
                    this.style.borderColor = this.checked ? '#c3c3c3' : '#a6acaf'; 
                    this.style.boxShadow = this.checked ? '0px 0px 8px rgba(15,20,26,80%)' : '0px 0px 5px rgba(10,20,30,50%)';">
                        <label class="form-check-label ms-2" for="pickup_time_to_be_advised">(To Be Advised)</label>
                    </div>
                </label>

                <!-- DateTime Picker -->
                <div class="input-group date" id="pickupTimePicker" data-target-input="nearest">
                    <input type="text" name="pickup_time" value="{{ old('pickup_time') }}" id="pickupTime"
                        class="form-control datetimepicker-input" data-target="#pickupTimePicker" placeholder="HH:MM"
                        autocomplete="off" autofocus />
                    <div class="input-group-append" data-target="#pickupTimePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    </div>
                </div>

                <!-- Validation Error Message -->
                @error('pickup_time')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-md-4" id="departureTimeField" style="display:{{ $departureTimeField }};">
            <div class="form-group">
                <label for="departureTime">Departure Time</label>
                <div class="input-group date" id="departureTimePicker" data-target-input="nearest">
                    <input type="text" name="departure_time" value="{{ old('departure_time') }}"
                        id="departureTime" class="form-control datetimepicker-input"
                        data-target="#departureTimePicker" placeholder="dd/mm/yyyy HH:mm" autocomplete="off"
                        autofocus />
                    <div class="input-group-append" data-target="#departureTimePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    </div>
                </div>
                @error('departure_time')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4" id="flightDetailDiv" style="display:{{ $flightDetailDiv }};">
            <div class="form-group">
                <label for="flightDetail">Flight Details<span class="text-danger" id="flightDetailSpan"
                        style="display:{{ $flightDetailSpan }};">*</span></label>
                <input type="text" id="flightDetail" name="flight_detail" value="{{ old('flight_detail') }}"
                    class="form-control @error('flight_detail') is-invalid @enderror" placeholder="Flight Details"
                    autocomplete="off">
                @error('flight_detail')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4" id="noOfHoursTextBox" style="display:{{ $noOfHoursTextBox }};">
            <div class="form-group">
                <label for="noOfHours">No. of Hours <span class="text-danger">*</span></label>
                <input type="text" id="noOfHours" name="no_of_hours" value="{{ old('no_of_hours') }}"
                    class="form-control @error('no_of_hours') is-invalid @enderror" placeholder="No. of Hours"
                    autocomplete="off">
                @error('no_of_hours')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-2" id="totalPaxDiv" style="display:{{ $totalPaxDiv }};">
            <div class="form-group">
                <label for="totalPax">Total Pax <span class="text-danger" id="totalPaxSpan"
                        style="display:{{ $totalPaxSpan }}">*</span></label>
                <input type="text" id="totalPax" name="total_pax" value="{{ old('total_pax') }}"
                    class="form-control @error('total_pax') is-invalid @enderror" placeholder="Total Pax"
                    autocomplete="off" autofocus>
                @error('total_pax')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-2" id="totalLuggageDiv" style="display:{{ $totalLuggageDiv }};">
            <div class="form-group">
                <label for="totalLuggage">Total Luggage <span class="text-danger" id="totalLuggageSpan"
                        style="display:{{ $totalLuggageSpan }}">*</span></label>
                <input type="text" id="totalLuggage" name="total_luggage" value="{{ old('total_luggage') }}"
                    class="form-control @error('total_luggage') is-invalid @enderror" placeholder="Total Luggage"
                    autocomplete="off" autofocus>
                @error('total_luggage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="clientInstructions">Client Instructions</label>
                <input type="text" id="clientInstructions" name="client_instructions"
                    value="{{ old('client_instructions') }}"
                    class="form-control @error('client_instructions') is-invalid @enderror"
                    placeholder="Client Instructions" autocomplete="off" autofocus>
                @error('client_instructions')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="attachment"><span
                        title="Acceptable Formats: jpg, jpeg, png, gif, doc, docx, txt, pdf, xls, xlsx"
                        class="fa fa-info-circle" aria-hidden="true"></span>
                        Attachment (Please zip multiple files, refer to client user manual)</label>
                <input type="file" id="attachment" name="attachment" value="{{ old('attachment') }}"
                    class="form-control @error('attachment') is-invalid @enderror" placeholder="File">
                @error('attachment')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4" id="crossBorderField" style="display:{{ $crossBorderField }};">
            <div class="form-check mt-3">
                <input class="form-check-input @error('is_cross_border') is-invalid @enderror" type="checkbox"
                    id="crossBorder" name="is_cross_border" {{ $crossBorder }}>
                <label for="crossBorder" class="form-check-label">Cross Border Service</label>
                @error('is_cross_border')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row" id="childContainer">
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label fw-bold" style="padding-bottom:0">Child Seat Required? <span class="text-danger">*</span></label>
                <p class="small mb-3">*Above 1.35m Child seat not required in Singapore</p>
                <div class="form-check">
                    <input class="form-check-input @error('child_seat_required') is-invalid @enderror" type="radio" name="child_seat_required" id="childSeatYes" value="yes" autocomplete="off" autofocus>
                    <label class="form-check-label" for="childSeatYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input @error('child_seat_required') is-invalid @enderror" type="radio" name="child_seat_required" id="childSeatNo" value="no" autocomplete="off" autofocus checked>
                    <label class="form-check-label" for="childSeatNo">No</label>
                </div>
            </div>
            @error('child_seat_required')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div id="childSeatOptions" style="display: none;">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="childSeatCount" class="form-label">No. of Child Seat required: <span class="text-danger">*</span></label>
                    <select class="form-select" id="childSeatCount" name="no_of_seats_required">
                        <option value="1" selected>1</option>
                        <option value="2">2</option>
                    </select>
                </div>
            </div>

            <div id="childAgeInputs">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Child 1:</label>
                        <select class="form-select child-age" name="child_1_age">
                            <option value="<1 yo" selected>&lt;1 yo</option>
                            <option value="1 yo">1 yo</option>
                            <option value="2 yo">2 yo</option>
                            <option value="3 yo">3 yo</option>
                            <option value="4 yo">4 yo</option>
                            <option value="5 yo">5 yo</option>
                            <option value="6 yo">6 yo</option>
                        </select>
                    </div>
                </div>
            </div>

            <p class="text-muted small">1st Child seat is free. Additional child seat is $10.</p>
        </div>
    </div>
    <div class="row" id="meetAndGreetContainer" style="display:none;">
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label fw-bold" style="padding-bottom:0">Meet and Greet? <span class="text-danger">*</span></label>
                <p class="small mb-3">*Seating capacity > 13seater</p>
                <div class="form-check">
                    <input class="form-check-input @error('meet_and_greet') is-invalid @enderror" type="radio" name="meet_and_greet" id="meetAndGreetYes" value="yes" autocomplete="off" autofocus disabled>
                    <label class="form-check-label" for="meetAndGreetYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input @error('meet_and_greet') is-invalid @enderror" type="radio" name="meet_and_greet" id="meetAndGreetNo" value="no" autocomplete="off" autofocus checked>
                    <label class="form-check-label" for="meetAndGreetNo">No</label>
                </div>
            </div>
            @error('meet_and_greet')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row" id="guestContainer">
        <div class="row">
            @foreach (old('guest_name', ['']) as $index => $guest)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="guestName_{{ $index }}">Name of Guest(s) <span
                                class="text-danger">*</span></label>
                        <input type="text" id="guestName_{{ $index }}" name="guest_name[]"
                            value="{{ $guest }}"
                            class="form-control guest-name @error('guest_name.' . $index) is-invalid @enderror"
                            placeholder="Name of Guest(s)" autocomplete="off" autofocus>
                        @error('guest_name.' . $index)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            @endforeach
            @foreach (old('country_code', ['']) as $index => $country_code)
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="country_code_{{ $index }}">
                            <span title="The country code must be an number." class="fa fa-info-circle"
                                aria-hidden="true"></span>
                            Country Code 
                        </label>
                        <input type="text" id="country_code_{{ $index }}" name="country_code[]" value="{{ $country_code }}"
                            class="form-control @error('country_code.' . $index) is-invalid @enderror" placeholder="Code"
                            autocomplete="off" autofocus>
                        @error('country_code.' . $index)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            @endforeach
            @foreach (old('phone', ['']) as $index => $phone)
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="phone_{{ $index }}">Contact <span class="text-danger">*</span></label>
                        <input type="text" id="phone_{{ $index }}" name="phone[]" value="{{ $phone }}"
                            class="form-control @error('phone.' . $index) is-invalid @enderror phone" placeholder="Contact"
                            autocomplete="off">
                        @error('phone.' . $index)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1 mt-4 iconContainer">
                    @if ($loop->last)
                        <button type="button" id="addGuest"><span class="fa fa-plus mt-3"></span></button>
                    @else
                        <button type="button" class="remove-guest"><span
                                class="fas fa-times mt-3 text-danger"></span></button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <div class="text-right mt-5 pt-3 border-top">
        <button type="submit" id="addBookingFormButton" class="btn btn-outline-primary mx-2" title="Save">Save</button>
    </div>
</form>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addEventModalLabel">Add Event</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createEventForm" method="post" action="{{ route('create-event-by-ajax') }}" >
            <div class="row">
                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientIdForEvent">Corporate <span class="text-danger">*</span></label>
                            <select name="client_id_for_event" id="clientIdForEvent"
                                class="form-control form-select custom-select @error('client_id_for_event') is-invalid @enderror"
                                autocomplete="off">
                                <option value="">Select One</option>
                                @foreach ($hotelClients as $hotelClient)
                                    @php
                                        $client = $hotelClient->client ?? null;
                                    @endphp
                                    @if ($client)
                                        @if (old('client_id_for_event') == $client->hotel_id)
                                            <option value="{{ $client->id }}" selected>{{ $hotelClient->name }}</option>
                                        @else
                                            <option value="{{ $client->id }}">{{$hotelClient->name  }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            <span style="display:none" class="invalid-feedback" id="hotel_for_event_error" role="alert"><strong>Please select a hotel</strong>
                            </span>
                        </div>
                    </div>
                @else
                    @if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientIdForEvent">Corporate <span class="text-danger">*</span></label>
                                <select name="client_id_for_event" id="clientIdForEvent"
                                    class="form-control form-select custom-select @error('client_id_for_event') is-invalid @enderror"
                                    autocomplete="off">
                                    <option value="">Select One</option>
                                    @foreach ($multipleCorporatesHotelData as $hotelClient)
                                        @php
                                            $client = $hotelClient->client ?? null;
                                        @endphp
                                        @if ($client)
                                            @if (old('client_id_for_event') == $client->id)
                                                <option value="{{ $client->hotel_id }}" selected>{{ $hotelClient->name }}</option>
                                            @else
                                                <option value="{{ $client->hotel_id }}">{{$hotelClient->name  }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <span style="display:none" class="invalid-feedback" id="hotel_for_event_error" role="alert"><strong>Please select a hotel</strong>
                                </span>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="client_id_for_event" id="clientIdForEvent" value="{{Auth::user()->client->hotel_id}}">
                    @endif
                @endif
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="eventId">Event <span class="text-danger">*</span></label>
                        <input type="text" id="event_name" name="event_name"
                            value="{{ old('event_name') }}"
                            class="form-control @error('event_name') is-invalid @enderror"
                            placeholder="Event Name" autocomplete="off" autofocus>
                        <span style="display:none" class="invalid-feedback" id="event_name_error" role="alert"><strong>Please enter event name</strong>
                    </div>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="addEventFormButton">Create Event</button>
      </div>
    </div>
  </div>
</div>
