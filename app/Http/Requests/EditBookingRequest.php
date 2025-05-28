<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EditBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules(Request $request)
    {
        return [
            'service_type_id' => 'required|numeric|integer',
            'pick_up_location_id' => 'nullable',
            'drop_off_location_id' => 'nullable',
            'pick_up_location' => 'nullable|min:3',
            'drop_of_location' => 'nullable|min:3',
            'vehicle_type_id' => 'required',
            'pickup_date' => 'nullable|date_format:d/m/Y',
            'pickup_time' => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:d/m/Y H:i',
            'no_of_hours' => 'nullable|numeric|min:3|max:24',
            'phone.*' => 'required',
            'total_pax' => 'nullable|numeric|digits_between:1,23',
            'total_luggage' => 'nullable|numeric|max:100',
            'guest_name.*' => 'required|min:3|max:50',
            // "arrival_charge" => 'nullable|required_if:service_type_id,1|regex:/^\d{1,8}(\.\d{1,2})?$/',
            // "transfer_charge" => 'nullable|required_if:service_type_id,2|regex:/^\d{1,8}(\.\d{1,2})?$/',
            // "departure_charge" => 'nullable|required_if:service_type_id,3|regex:/^\d{1,8}(\.\d{1,2})?$/',
            // "disposal_charge" => 'nullable|required_if:service_type_id,4|regex:/^\d{1,8}(\.\d{1,2})?$/',
            // "delivery_charge" => 'nullable|required_if:service_type_id,5|regex:/^\d{1,8}(\.\d{1,2})?$/',
        ];
    }

    // Optionally, you can define custom error messages here
    public function messages()
    {
        return [
            // "arrival_charge.required_if" => __("validation.custom.arrival_charge.required"),
            // "arrival_charge.regex" => __("validation.custom.decimal.regex"),
            // "transfer_charge.required_if" =>__("validation.custom.transfer_charge.required"),
            // "transfer_charge.regex" => __("validation.custom.decimal.regex"),
            // "departure_charge.required_if" => __("validation.custom.departure_charge.required"),
            // "departure_charge.regex" => __("validation.custom.decimal.regex"),
            // "disposal_charge.required_if" =>__("validation.custom.disposal_charge.required"),
            // "disposal_charge.regex" => __("validation.custom.decimal.regex"),
            // "delivery_charge.required_if" =>__("validation.custom.delivery_charge.required"),
            // "delivery_charge.regex" => __("validation.custom.decimal.regex"),

            'service_type_id.required' => __("validation.custom.service_type_id.required"),
            'service_type_id.integer' => __("validation.custom.integer"),
            'pick_up_location_id.required' => __("validation.custom.pick_up_location_id.required"),
            'drop_off_location_id.required' => __("validation.custom.drop_off_location_id.required"),
            'pick_up_location.required' => __("validation.custom.pick_up_location.required"),
            'pick_up_location.required' => __("validation.custom.pick_up_location.min"),

            'drop_of_location.required' => __("validation.custom.drop_of_location.required"),
            'drop_of_location.required' => __("validation.custom.drop_of_location.min"),
            'vehicle_type_id.required' => __("validation.custom.vehicle_type_id.required"),

            'pickup_date.required' => __("validation.custom.pickup_date.required"),
            'pickup_date.date_format' => __("validation.custom.pickup_date.regex"),

            'pickup_time.required' => __("validation.custom.pickup_time.required"),
            'pickup_time.date_format' => __("validation.custom.time_format"),

            'departure_time.required' => __("validation.custom.departure_time.required"),
            'departure_time.date_format' => __("validation.custom.time_format"),

            'no_of_hours.required' => __("validation.custom.no_of_hours.required"),
            'no_of_hours.numeric' => __("validation.custom.no_of_hours.digits"),
            'no_of_hours.min' => __("validation.custom.no_of_hours.min"),
            'no_of_hours.max' => __("validation.custom.no_of_hours.max"),

            'phone.*.required' => __("validation.custom.phone.required"),
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
