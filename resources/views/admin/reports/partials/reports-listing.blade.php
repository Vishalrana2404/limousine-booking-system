@php
    use App\CustomHelper;
    $user = Auth::user();
    $userTypeSlug = $user->userType->slug ?? null;
@endphp
@forelse($driversBooking as $key => $drivers)
    @php
        $eventName = !empty($drivers->event) && !empty($drivers->event->name) ? $drivers->event->name : NULL;
        $pickUpLocation = null;
        $pickupLocationId = $drivers->pick_up_location_id ?? null;
        if ($pickupLocationId && $pickupLocationId !== 8) {
            $pickUpLocation = $drivers->pickUpLocation->name ?? null;
        } else {
            $pickUpLocation = $drivers->pick_up_location;
        }

        $dropOffLocation = null;
        if ($drivers->service_type_id === 3) {
            if (!empty($drivers->flight_detail)) {
                $dropOffLocation = $drivers->departure_time
                    ? $drivers->flight_detail .
                        ' / ' .
                        CustomHelper::parseDateTime($drivers->departure_time, 'd M, Y H:i')
                    : $drivers->flight_detail;
                $dropOffLocationEditVal = $drivers->flight_detail ?? null;
            }
        } else {
            $dropOffLocationId = $drivers->drop_off_location_id ?? null;
            if ($dropOffLocationId && $dropOffLocationId !== 8) {
                $dropOffLocation = $drivers->dropOffLocation->name ?? null;
            } else {
                $dropOffLocation = $drivers->drop_of_location;
            }
        }
        $additionalStops = $drivers->additional_stops;
        $additionalStopsVal = '';
        if(!empty($additionalStops))
        {
            $additionalStopsVal = explode('||', $drivers->additional_stops);
        }
        $pickUpTime = 'N/A';
        $pickup = $drivers->pickup_time ? CustomHelper::formatTime($drivers->pickup_time) : null;
        if ($drivers->service_type_id === 4) {
            if ($pickup && $drivers->no_of_hours) {
                $pickupDateTime = new DateTime($pickup);
                $pickupDateTime->modify('+' . $drivers->no_of_hours . ' hours');
                $endTime = $pickupDateTime->format('H:i');
                $pickUpTime = $pickup . '</br>(' . $endTime . ')';
            } else {
                $pickUpTime = 'N/A';
            }
        } else {
            $pickUpTime = $pickup ? $pickup : 'N/A';
        }
        $firstName = $drivers->updatedBy->first_name ?? null;
        $lastName = $drivers->updatedBy->last_name ?? null;
        $fullName = CustomHelper::getFullName($firstName, $lastName);
        $hotel = $drivers->client->hotel->name ?? null;
        if ($hotel) {
            $hotelValue = $hotel;
        } else {
            $hotelValue = 'N/A';
        }

        $attachment = $drivers->attachment ?? null;
        $guestNames = $drivers->guest_name ?? null;
        $resultGuestName = null;
        if ($guestNames) {
            $guestNameArray = explode(',', $guestNames);
            foreach ($guestNameArray as $key => $name) {
                $resultGuestName .= $key + 1 . '. ' . ucfirst(trim($name)) . '<br>';
            }
        }
        $VehicleClassName = $drivers->vehicle->vehicleClass->name ?? null;
        $VehicleNumber = $drivers->vehicle->vehicle_number ?? null;
        $Vehicle = null;
        if ($VehicleClassName && $VehicleNumber) {
            $Vehicle = $VehicleClassName . '<br>' . $VehicleNumber;
        }
        $rowClass = '';
        if ($drivers->status === 'PENDING') {
            $rowClass = 'pending-status';
        } elseif ($booking->status === 'CANCELLED WITH CHARGES') {
            $rowClass = 'cancelled-with-charges';
        } elseif ($drivers->status === 'COMPLETED') {
            $rowClass = 'completed-status';
        } elseif ($drivers->status === 'CANCELLED') {
            $rowClass = 'cancelled-status';
        }

    @endphp
    <tr class="{{ $rowClass }}" data-id="{{ $drivers->id }}">
        <td>
            @if ($attachment)
                <a target="blank" href="{{ Storage::url($attachment) }}" class="attachment-link">
                    <i class="fa fa-paperclip text-dark"></i>
                </a>
            @endif
            #{{ $drivers->id }}
        </td>
        <td>{{ date('d-m-Y', strtotime($drivers->pickup_date)) ?? 'N/A'}}</td>
        <td>{!! $pickUpTime ?? 'N/A' !!}</td>
        <td class="text-truncate">{{ $drivers->serviceType->name ?? 'N/A' }}</td>
        <td class="text-truncate toggalPickup">{{ $pickUpLocation ?? 'N/A' }}</td>
        <td class="text-truncate toggalDropOff">{{ $dropOffLocation ?? 'N/A' }}</td>
        <td class="text-truncate toggalAdditionalStops">
            @if(!empty($additionalStopsVal))
                @foreach($additionalStopsVal as $stopKey => $additionalStop)
                    {{ $stopKey+1 . '. ' . $additionalStop }}
                    @if($stopKey != count($additionalStopsVal))<br>@endif
                @endforeach
            @endif
        </td>
        <td class="text-truncate toggalGuest">{!! $resultGuestName ?? 'N/A' !!}</td>
        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
            <td class="text-truncate">{!! $hotelValue !!}</td>
        @endif
        <td class="text-truncate toggalEvent">{!! $eventName !!}</td>
        <td class="text-truncate toggalContact">
            {{ $drivers->country_code ? '+(' . $drivers->country_code . ')' : '' }}{{ $drivers->phone ?? 'N/A' }}</td>
        <td class="text-truncate">{{ $drivers->driver->name ?? 'N/A' }}</td>
        <td class="text-truncate">{!! $Vehicle ?? 'N/A' !!}</td>
        <td class="text-truncate">{!! $drivers->status ?? 'N/A' !!}</td>
        <td class="text-truncate">
            @if(!empty($drivers->createdBy->first_name) && !empty($drivers->createdBy->last_name))
                @if(!empty($drivers->createdBy->first_name))
                    {{ $drivers->createdBy->first_name }}
                @endif
                @if(!empty($drivers->createdBy->last_name))
                    {{ ' ' . $drivers->createdBy->last_name }}
                @endif
            @else
                N/A
            @endif
        </td>
        <td class="text-truncate">
            @if(!empty($drivers->linkedClients))
                @foreach($drivers->linkedClients as $clientKey => $client)
                    {{ $clientKey+1 . '. ' . $client->first_name . ' ' . $client->last_name }}
                    @if($clientKey != count($drivers->linkedClients))<br>@endif
                @endforeach
            @endif
        </td>
        <td class="text-truncate">{!! date('d-m-Y H:i', strtotime($drivers->created_at)) ?? 'N/A' !!}</td>
    </tr>
@empty
    <tr>
        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
            <td colspan="18" class="text-center">No Record Found.</td>
        @else
            <td colspan="11" class="text-center">No Record Found.</td>
        @endif
    </tr>
@endforelse
