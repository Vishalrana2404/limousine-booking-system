<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules(Request $request)
    {
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
        $serviceTypeId = $request->input('service_type_id') ?? null;
        $pickupLocationId = $request->input('pick_up_location_id') ?? null;
        $dropLocationId = $request->input('drop_off_location_id') ?? null;
        return [
            'client_id' => ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) ? 'required' : 'nullable',
            // 'event_id' => 'required',
            'service_type_id' => 'required|integer',
            'pick_up_location_id' => 'nullable',
            'drop_off_location_id' => 'nullable',
            'pick_up_location' => (in_array($serviceTypeId, ["2", "3", "4", "5"]) || $pickupLocationId === "12") ? 'required|min:3' : 'nullable|min:3',
            'drop_of_location' => (in_array($serviceTypeId, ["1", "2", "5"]) || $dropLocationId === "12") ? 'required|min:3' : 'nullable|min:3',
            'vehicle_type_id' => 'required',
            'pickup_date' => 'nullable|date_format:d/m/Y',
            'pickup_time' => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:d/m/Y H:i',
            'no_of_hours' => 'nullable|numeric|min:3|max:24',
            'phone' => 'required',
            'total_pax' => 'nullable|numeric|digits_between:1,23',
            'total_luggage' => 'nullable|numeric|max:100',
            'guest_name.*' => 'required|min:3|max:50',
        ];
    }

    // Optionally, you can define custom error messages here
    public function messages()
    {
        return [
            'client_id.required' => __("validation.custom.client_id.required"),
            // 'event_id.required' => __("validation.custom.event_id.required"),
            'service_type_id.required' => __("validation.custom.service_type_id.required"),
            'service_type_id.integer' => __("validation.custom.integer"),

            'pick_up_location_id.required' => __("validation.custom.pick_up_location_id.required"),

            'drop_off_location_id.required' => __("validation.custom.drop_off_location_id.required"),

            'pick_up_location.required' => __("validation.custom.pick_up_location.required"),
            'pick_up_location.min' => __("validation.custom.pick_up_location.min"),

            'drop_of_location.required' => __("validation.custom.drop_of_location.required"),
            'drop_of_location.min' => __("validation.custom.drop_of_location.min"),

            'vehicle_type_id.required' => __("validation.custom.vehicle_type_id.required"),

            'pickup_date.required' => __("validation.custom.pickup_date.required"),
            'pickup_date.date_format' => __("validation.custom.pickup_date.regex"),

            'pickup_time.required' => __("validation.custom.pickup_time.required"),
            'pickup_time.date_format' => __("validation.custom.time_format"),

            'departure_time.required' => __("validation.custom.departure_time.required"),
            'departure_time.date_format' => __("validation.custom.date_time_format"),

            'no_of_hours.required' => __("validation.custom.no_of_hours.required"),
            'no_of_hours.numeric' => __("validation.custom.no_of_hours.digits"),
            'no_of_hours.min' => __("validation.custom.no_of_hours.min"),
            'no_of_hours.max' => __("validation.custom.no_of_hours.max"),


            'phone.required' => __("validation.custom.phone.required"),
            // 'phone.digits_between' => __("validation.custom.phone.digits_between"),

            'total_pax.required' => __("validation.custom.total_pax_booking.required"),
            'total_pax.numeric' => __("validation.custom.total_pax_booking.digits"),
            'total_pax.digits_between' => __("validation.digits_between"),


            'total_luggage.required' => __("validation.custom.total_luggage_booking.required"),
            'total_luggage.numeric' => __("validation.custom.total_luggage_booking.digits"),
            'total_luggage.max' => __("validation.custom.total_luggage_booking.max"),


            'guest_name.*.required' => __("validation.custom.guest_name.required"),
            'guest_name.*.min' => __("validation.custom.guest_name.min"),
            'guest_name.*.max' => __("validation.custom.guest_name.max"),

        ];
    }
}
