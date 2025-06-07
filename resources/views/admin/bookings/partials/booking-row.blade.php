@php
        use App\CustomHelper;
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
        $driverName = $booking->driver->name ?? null;
        $driverPhone = $booking->driver->phone ?? null;
        $driverCountryCode = $booking->driver->country_code ?? null;
        $driverType = $booking->driver->driver_type ?? null;
        $driverValue = null;
        $pickUpTime = 'N/A';
        $pickup = $booking->pickup_time ? CustomHelper::formatTime($booking->pickup_time) : null;
        if ($booking->service_type_id === 4) {
            if ($pickup && $booking->no_of_hours) {
                $pickupDateTime = new DateTime($pickup);
                $pickupDateTime->modify('+' . $booking->no_of_hours . ' hours');
                $endTime = $pickupDateTime->format('H:i');
                $pickUpTime = $pickup . '</br>(' . $endTime . ')';
            } else {
                $pickUpTime = 'N/A';
            }
        } else {
            $pickUpTime = $pickup ? $pickup : 'N/A';
        }
        $pickupDate = $booking->pickup_date;
        $pickupDateTime = $pickupDate . ' ' . $pickup;
        $pickupDateTimeStamp = strtotime($pickupDateTime);
        $currentTimeStamp = strtotime(date('Y-m-d H:i'));
        $hoursDifference = ($pickupDateTimeStamp - $currentTimeStamp) / 3600;
        
        if ($driverName) {
            if ($driverType === 'OUTSOURCE') {
                $driverValue = '(' . $driverName . ') </br> (+' . $driverCountryCode . ')' . $driverPhone;
            } else {
                $driverValue = $driverName . '</br> (+' . $driverCountryCode . ')' . $driverPhone;
            }
        }
        $vehicleNumber = $booking->vehicle->vehicle_number ?? null;
        $vehicleClassName = $booking->vehicleType->name ?? null;
        $vehicleValue = 'N/A';
        $VehicleClassName = $booking->vehicle->vehicleClass->name ?? null;
        if ($VehicleClassName && $vehicleNumber) {
            $vehicleValue = $VehicleClassName . '</br>' . $vehicleNumber;
        }
        $pickUpLocation = null;

        if ($booking->service_type_id === 1) {
            if($booking->pick_up_location_id == 8 || $booking->pick_up_location_id == 9 || $booking->pick_up_location_id == 10 || $booking->pick_up_location_id == 11 || $booking->pick_up_location_id == 12)
            {
                if($booking->pick_up_location_id == 12)
                {
                    $pickUpLocation = $booking->pick_up_location;
                }else{
                    $pickUpLocation = $booking->pickUpLocation->name;
                }
            }else{
                $pickUpLocation = $booking->flight_detail ?? null;
            }
        } else {
            $pickupLocationId = $booking->pick_up_location_id ?? null;
            if ($pickupLocationId && $pickupLocationId !== 12) {
                $pickUpLocation = $booking->pickUpLocation->name ?? null;
            } else {
                $pickUpLocation = $booking->pick_up_location;
            }
        }

        $dropOffLocation = null;
        $dropOffLocationEditVal = null;
        if ($booking->service_type_id === 3) {
             if($booking->drop_off_location_id == 8 || $booking->drop_off_location_id == 9 || $booking->drop_off_location_id == 10 || $booking->drop_off_location_id == 11 || $booking->drop_off_location_id == 12)
            {
                if($booking->drop_off_location_id == 12)
                {
                    $dropOffLocation = $booking->drop_of_location;
                }else{
                    $dropOffLocation = $booking->dropOffLocation->name;
                }
            }else{
                $dropOffLocation = $booking->departure_time
                    ? $booking->flight_detail .
                        ' / ' .
                        CustomHelper::parseDateTime($booking->departure_time, 'd M, Y H:i')
                    : $booking->flight_detail;
                $dropOffLocationEditVal = $booking->flight_detail ?? null;
            }
        } else {
            $dropOffLocationId = $booking->drop_off_location_id ?? null;
            if ($dropOffLocationId && $dropOffLocationId !== 12) {
                $dropOffLocation = $booking->dropOffLocation->name ?? null;
                $dropOffLocationEditVal = $booking->dropOffLocation->name ?? null;
            } else {
                $dropOffLocation = $booking->drop_of_location ?? null;
                $dropOffLocationEditVal = $booking->drop_of_location ?? null;
            }
        }

        $pickupAdditionalStops = '';
        $dropoffAdditionalStops = '';

        if ($booking->additional_stops_required === 'yes') {
            $destinationLabels = config('constants.destination_labels');
            $destinationNumbers = config('constants.destination_numbers');

            foreach ($booking->additionalStops as $stop) {
                $index = array_search($stop->destination_sequence, $destinationLabels);

                // Skip if sequence not found
                if ($index === false) continue;

                $line = $destinationNumbers[$index] . '. ' . $stop->additional_stop_address;

                if ($stop->destination_type === 'pickup') {
                    $pickupAdditionalStops .= ($pickupAdditionalStops ? '<br>' : '') . $line;
                } elseif ($stop->destination_type === 'dropoff') {
                    $dropoffAdditionalStops .= ($dropoffAdditionalStops ? '<br>' : '') . $line;
                }
            }
        }

        $countryCodes = explode(',', $booking->country_code ?? '');
        $phones = explode(',', $booking->phone ?? '');

        $contacts = [];
        foreach ($countryCodes as $index => $code) {
            $phone = $phones[$index] ?? null;

            $contacts[] = $index+1 . '. ' . ($code ? '+(' . $code . ') ' : '') . ($phone);
        }
        $firstName = $booking->updatedBy->first_name ?? null;
        $lastName = $booking->updatedBy->last_name ?? null;
        $fullName = CustomHelper::getFullName($firstName, $lastName);
        $hotel = $booking->client->hotel->name ?? null;
        $event = $booking->event->name ?? null;
        if ($hotel) {
            $hotelValue = $hotel . '<br> (' . $event . ')';
        } else {
            $hotelValue = 'N/A';
        }

        $attachment = $booking->attachment ?? null;
        $guestNames = $booking->guest_name ?? null;
        $resultGuestName = null;
        if ($guestNames) {
            $guestNameArray = explode(',', $guestNames);
            foreach ($guestNameArray as $key => $name) {
                $resultGuestName .= $key + 1 . '. ' . ucfirst(trim($name)) . '<br>';
            }
        }
        $dispatch = '<i class="fas fa-question-circle dispatch"></i>';
        if ($booking->status === 'CANCELLED') {
            $dispatch = '<i class="fas fa-times"></i>';
        } else {
            if ($booking->is_driver_notified && $booking->is_driver_acknowledge) {
                $dispatch = '<i class="fa fa-check"></i>';
            } elseif ($booking->is_driver_notified) {
                $dispatch = '<i class="fa fa-mobile dispatch"></i>';
            } else {
                $dispatch = '<i class="fas fa-question-circle dispatch"></i>';
            }
        }
        $rowClass = '';
        if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) {
            if ($booking->status === 'CANCELLED') {
                $rowClass = 'cancelled-status';
            } elseif ($booking->status === 'COMPLETED') {
                $rowClass = 'completed-status';
            }elseif($booking->status !== 'CANCELLED' && $booking->client_asked_to_cancel === 'yes'){
                $rowClass = 'requested-to-cancel';
            } elseif (($booking->status === 'PENDING' || is_null($booking->driver_id) || is_null($booking->vehicle_id)) && $booking->status !== 'ACCEPTED') {
                $rowClass = 'pending-status';
            }
        }

        $isEditable =
            $userTypeSlug === null ||
            (in_array($userTypeSlug, ['client-admin', 'client-staff']) && $booking->status === 'PENDING') ||
            in_array($userTypeSlug, ['admin', 'admin-staff'])
                ? true
                : false;
    @endphp
  <tr id="{{ $booking->id }}" class="{{ $rowClass }}" data-id="{{ $booking->id }}"
      data-is-notified="{{ $booking->is_driver_notified }}" data-is-acknowledge="{{ $booking->is_driver_acknowledge }}"
      data-driver-id="{{ $booking->driver_id }}" data-pickup-date="{{ $booking->pickup_date }}">
      @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
          <td class="sticky-column">
              <div class="custom-control custom-checkbox">
                  <input class="custom-control-input bookingTableCheckbox cellCheckbox" type="checkbox"
                      id="bulkBookingAction_{{ $booking->id }}">
                  <label for="bulkBookingAction_{{ $booking->id }}" class="custom-control-label"></label>
              </div>
          </td>
      @endif
      <td class="sticky-column">
        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))

            <a class="text-dark mx-1 {{$userTypeSlug}}" href="{{ route('edit-booking', ['booking' => $booking->id]) }}" title="Edit">
                <i class="fas fa-pencil-alt mr-1"></i>
            </a>
            
        @else
            @if($booking->status === 'PENDING' && $booking->client_asked_to_cancel != 'yes')
            @if($hoursDifference > 24)
            <a class="text-dark mx-1" href="{{ route('edit-booking', ['booking' => $booking->id]) }}" title="Edit">
                <i class="fas fa-pencil-alt mr-1"></i></a>
                @endif
            @endif
            @if($booking->status === 'ACCEPTED' && $booking->client_asked_to_cancel != 'yes')
                @if($hoursDifference > 24)
                    <a class="text-dark mx-1" href="{{ route('edit-booking', ['booking' => $booking->id]) }}" title="Edit">
                        <i class="fas fa-pencil-alt mr-1"></i>
                    </a>
                @endif
            @endif
        @endif
          {{-- @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
              <button title="Delete"><i data-id="{{ $booking->id }}"
                      class="fas fa-solid fa-trash text-danger mr-2 mx-1"></i></button>
          @endif --}}
      </td>
      <td class="sticky-column {{$hoursDifference}}">
          @if ($attachment && Storage::disk('public')->exists($attachment))
              <a target="blank" href="{{ Storage::url($attachment) }}" class="attachment-link">
                  <i class="fa fa-paperclip text-dark"></i>
              </a>
          @endif
          #{{ $booking->id }}
      </td>
      <td
          @if ($isEditable) data-name="pickup_date" data-old="{{ CustomHelper::parseDateTime($booking->pickup_date, 'd/m/Y') }}" @endif>
          {{ CustomHelper::parseDateTime($booking->pickup_date, 'd M, Y') }}</td>
      <td
          @if ($isEditable) data-name="pickup_time" data-old="{{ CustomHelper::formatTime($booking->pickup_time) }}" @endif>
          {!! $booking->to_be_advised_status === 'yes' && $pickUpTime === '00:00' ? 'To Be Advised' : $pickUpTime ?? 'N/A' !!}</td>
      <td class="text-truncate">{{ $booking->serviceType->name ?? 'N/A' }}</td>
      <td @if ($isEditable) data-name="pickup_location" data-old="{{ $pickUpLocation }}"
          data-service-id="{{ $booking->service_type_id }}" data-old-id="{{ $booking->pick_up_location_id }}" @endif
          class="text-truncate" style="max-width: 200px" title="{{ $pickUpLocation ?? 'N/A' }}">
          {{ $pickupAdditionalStops !== '' ? '1. ' : ''; }} {{ $pickUpLocation ?? 'N/A' }}
          <br>
          {!! $pickupAdditionalStops !!}
        </td>
      <td @if ($isEditable) data-name="drop_of_location" data-old="{{ $dropOffLocationEditVal }}"
          data-service-id="{{ $booking->service_type_id }}" data-old-id="{{ $booking->drop_off_location_id }}" @endif
          class="text-truncate" style="max-width: 200px" title="{{ $dropOffLocation ?? 'N/A' }}">
          {!! $dropoffAdditionalStops !!}
          <br>
          {{ $dropoffAdditionalStops !== '' || $pickupAdditionalStops !== '' ? count($booking->additionalStops) + 2 . '. ' : ''; }}{{ $dropOffLocation ?? 'N/A' }}
      </td>
      <td @if ($isEditable) data-name="guest_name" data-old="{{ $guestNames }}" @endif
          class="text-truncate" style="max-width: 200px" title="{{ $resultGuestName ?? 'N/A' }}">{!! $resultGuestName ?? 'N/A' !!}</td>
      @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
          <td class="text-truncate" title="{{ strip_tags($hotelValue) }}">{{ \Illuminate\Support\Str::limit(strip_tags($hotelValue), 22) }}</td>
      @endif
        <td @if ($isEditable) data-name="phone" data-old="{{ $booking->phone }}" data-country-code="{{ $booking->country_code }}" @endif
          class="text-truncate" style="max-width: 200px" title="{{ $booking->country_code ? '+(' . $booking->country_code . ')' : '' }}{{ $booking->phone ?? 'N/A' }}">            
            @foreach($contacts as $index => $contact)
                {{ $contact }}
                @if($index+1 != count($contacts))
                    <br>
                @endif
            @endforeach
        </td>
      <td @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) data-name="driver_id" data-old="{{ $driverValue }}" data-old-id="{{ $booking->driver_id }}" @endif
          class="text-truncate">{!! $driverValue ?? 'N/A' !!}</td>
      <td @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) data-name="vehicle_id" data-old="{{ $vehicleValue }}" data-old-id="{{ $booking->vehicle_id }}" @endif
          class="text-truncate">{!! $vehicleValue ?? 'N/A' !!}</td>
      <td @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) data-name="vehicle_type_id" data-old="{{ $vehicleClassName }}" data-old-id="{{ $booking->vehicle_type_id }}" @endif
          class="text-truncate">{!! $vehicleClassName ?? 'N/A' !!}</td>
      <td @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) data-name="status" data-old="{{ ucfirst(strtolower($booking->status)) }}"
          data-old-id="{{ $booking->status }}" @endif
          class="text-truncate">
          @if($booking->status == 'CANCELLED')
            {{ ucfirst(strtolower($booking->status)) }}
          @else
            @if($booking->client_asked_to_cancel == 'yes')
                Requested Cancel
            @else
                {{ ucfirst(strtolower($booking->status)) }}
            @endif
          @endif
        </td>
      <td @if ($isEditable) data-name="client_instructions" data-old="{{ $booking->client_instructions }}" @endif
          class="text-truncate" style="max-width: 200px" title="{{ $booking->client_instructions ?? 'N/A' }}">
          {{ $booking->client_instructions ?? 'N/A' }}</td>
        
        @if(Auth::user()->userType == null || (Auth::user()->userType->slug === null || in_array(Auth::user()->userType->slug, ['admin', 'admin-staff'])))
        <td @if ($isEditable) data-name="latest_admin_comment" data-old="{{ $booking->latest_admin_comment }}" @endif
            class="text-truncate" style="max-width: 200px" title="{{ $booking->latest_admin_comment ?? 'N/A' }}">
            {{ $booking->latest_admin_comment ?? 'N/A' }}</td>
        @endif

      <!-- <td class="text-truncate">{{ CustomHelper::formatSingaporeDate($booking->created_at) ?? 'N/A' }}</td> -->
      <!-- @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
          <td data-name="driver_remark" data-old="{{ $booking->driver_remark }}" class="text-truncate cell-width-300"
              title="{{ $booking->driver_remark }}">
              {{ $booking->driver_remark ?? 'N/A' }}</td>
          <td class="text-truncate">{!! $dispatch !!}</td>
          <td data-name="internal_remark" data-old="{{ $booking->internal_remark }}"
              class="text-truncate cell-width-300" title="{{ $booking->internal_remark }}">
              {{ $booking->internal_remark ?? 'N/A' }}</td> -->
          <!-- <td class="text-truncate">{{ $fullName ?? 'N/A' }}</td> -->
      <!-- @endif -->
  </tr>
