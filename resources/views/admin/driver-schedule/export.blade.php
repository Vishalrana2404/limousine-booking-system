@php
    use App\CustomHelper;
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <style>
        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            font-size: 12px;
            word-wrap: break-word;
            /* added to prevent content overflow */
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <table id="exportTable">
        <thead>
            <tr>
                <th>Booking </th>
                <th>Time</th>
                <th>Type</th>
                <th>Pick-up</th>
                <th>Drop-off</th>
                <th>Guests</th>
                <th>Client</th>
                @if ($isDisplayContact === 'true')
                    <th>Contact</th>
                @endif
                <th>Driver</th>
                <th>Instructions</th>
                <th>Vehicle</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through data rows -->
            @foreach ($driversBooking as $key => $drivers)
                @php
                    $pickUpLocation = null;
                    $pickupLocationId = $drivers->pick_up_location_id ?? null;
                    if ($pickupLocationId && $pickupLocationId !== 8) {
                        $pickUpLocation = $drivers->pickUpLocation->name ?? null;
                    } else {
                        $pickUpLocation = $drivers->pick_up_location;
                    }

                    $dropOffLocation = null;
                    $dropOffLocationId = $drivers->drop_off_location_id ?? null;
                    if ($dropOffLocationId && $dropOffLocationId !== 8) {
                        $dropOffLocation = $drivers->dropOffLocation->name ?? null;
                    } else {
                        $dropOffLocation = $drivers->drop_of_location;
                    }
                    $hotel = $drivers->client->hotel->name ?? null;
                    $event = $drivers->client->event ?? null;
                    if ($hotel) {
                        $hotelValue = $hotel;
                    } else {
                        $hotelValue = 'N/A';
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
                    $guestNames = $drivers->guest_name ?? null;
                    $resultGuestName = null;
                    if ($guestNames) {
                        $guestNameArray = explode(',', $guestNames);
                        foreach ($guestNameArray as $key => $name) {
                            $resultGuestName .= $key + 1 . '. ' . ucfirst(trim($name)) . '<br>';
                        }
                    }
                    $vehicleClassName = $drivers->vehicle->vehicleClass->name ?? null;
                    $vehicleNumber = $drivers->vehicle->vehicle_number ?? null;
                    $Vehicle = null;
                    if ($vehicleClassName && $vehicleNumber) {
                        $Vehicle = $vehicleClassName . '<br>' . $vehicleNumber;
                    }
                @endphp
                <tr>
                    <td style="width:10%;">
                        #{{ $drivers->id }}
                    </td>
                    <td style="width:10%;">{!! $pickUpTime ?? 'N/A' !!}</td>
                    <td style="width:10%;">{{ $drivers->serviceType->name ?? 'N/A' }}</td>
                    <td style="width:10%;">{{ $pickUpLocation ?? 'N/A' }}</td>
                    <td style="width:10%;">{{ $dropOffLocation ?? 'N/A' }}</td>
                    <td style="width:10%;">{!! $resultGuestName ?? 'N/A' !!}</td>
                    <td style="width:10%;">{!! $hotelValue !!}</td>
                    @if ($isDisplayContact === 'true')
                        <td style="width:10%;">
                            {{ $drivers->country_code ? '+(' . $drivers->country_code . ')' : '' }}{{ $drivers->phone ?? 'N/A' }}
                        </td>
                    @endif
                    <td style="width:10%;">{{ $drivers->driver->name ?? 'N/A' }}</td>
                    <td style="width:10%;"> {{ $drivers->client_instructions ?? 'N/A' }}</td>
                    <td style="width:10%;">{!! $Vehicle ?? 'N/A' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
