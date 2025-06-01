<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddMultipleBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules(Request $request)
    {
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
        return [
            'multiple_client_id.*' => function ($attribute, $value, $fail) use ($userTypeSlug) {
                if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) {
                    if (empty($value)) {
                        $fail(__("validation.custom.client_id.required"));
                    }
                }
            },
            'multiple_service_type_id.*' => 'required',
            'multiple_drop_off_location_id.*' => 'nullable',
            'multiple_pick_up_location_id.*' => 'nullable',
            'multiple_pick_up_location.*' => function ($attribute, $value, $fail) use ($request) {
                $index = substr($attribute, strrpos($attribute, '.') + 1);
                $serviceTypeId = $request->input('multiple_service_type_id.' . $index);
                $pickupLocationId = $request->input('multiple_pick_up_location_id.' . $index);
            
                if (in_array($serviceTypeId, ["2", "3", "4", "5"]) || $pickupLocationId == 12) {
                    if ($value === null || strlen($value) < 3) {
                        $fail(__('validation.custom.pick_up_location.required'));
                    }
                }
            },
            'multiple_drop_of_location.*' => function ($attribute, $value, $fail) use ($request) {
                $index = substr($attribute, strrpos($attribute, '.') + 1);
                $serviceTypeId = $request->input('multiple_service_type_id.' . $index);
                $dropLocationId = $request->input('multiple_drop_off_location_id.' . $index);
            
                if (in_array($serviceTypeId, ["1", "2", "5"]) || $dropLocationId == 12) {
                    if ($value === null || strlen($value) < 3) {
                        $fail(__('validation.custom.drop_of_location.required'));
                    }
                }
            },
            'multiple_vehicle_type_id.*' => 'required',
            'multiple_pickup_date.*' => function ($attribute, $value, $fail) use ($request) {
               
                $index = substr($attribute, strrpos($attribute, '.') + 1); // Extract index from attribute name
                $pickupDate = $request->input('multiple_pickup_date.' . $index);
                if (empty($pickupDate) || empty($value)) {
                    $fail(__('validation.custom.pickup_date.required'));
                }
            },
            'multiple_pickup_time.*' => function ($attribute, $value, $fail) use ($request) {
                $index = substr($attribute, strrpos($attribute, '.') + 1); // Extract index from attribute name
                $pickupTime = $request->input('multiple_pickup_time.' . $index);

                if (empty($pickupTime) || empty($value)) {
                    $fail(__('validation.custom.pickup_time.required'));
                }
            },
            'multiple_departure_time.*' => 'nullable|date_format:d/m/Y H:i',
            'multiple_no_of_hours.*' => 'nullable|numeric|min:3|max:24',
            'multiple_phone.*' => 'required',
            'multiple_total_pax.*' => 'nullable|numeric|digits_between:1,23',
            'multiple_total_luggage.*' => 'nullable|numeric|max:100',
        ];
    }


    // Optionally, you can define custom error messages here
    public function messages()
    {
        return [
            'multiple_service_type_id.*.required' => __("validation.custom.service_type_id.required"),
            'multiple_pick_up_location_id.*.required' => __("validation.custom.pick_up_location_id.required"),
            'multiple_pick_up_location.*.required' => __("validation.custom.pick_up_location.required"),
            'multiple_pick_up_location.min' => __("validation.custom.pick_up_location.min"),

            'multiple_drop_of_location.*.required' => __("validation.custom.drop_of_location.required"),
            'multiple_drop_of_location.*.min' => __("validation.custom.drop_of_location.min"),
            'multiple_vehicle_type_id.*.required' => __("validation.custom.vehicle_type_id.required"),

            'multiple_pickup_date.*.required' => __("validation.custom.pickup_date.required"),
            'multiple_pickup_date.*.date_format' => __("validation.custom.pickup_date.regex"),

            'multiple_pickup_time.*.required' => __("validation.custom.pickup_time.required"),
            'multiple_pickup_time.*.date_format' => __("validation.custom.time_format"),

            'multiple_departure_time.*.required' => __("validation.custom.departure_time.required"),
            'multiple_departure_time.*.date_format' => __("validation.custom.date_time_format"),

            'multiple_no_of_hours.*.required' => __("validation.custom.no_of_hours.required"),
            'multiple_no_of_hours.*.numeric' => __("validation.custom.no_of_hours.digits"),
            'multiple_no_of_hours.*.min' => __("validation.custom.no_of_hours.min"),
            'multiple_no_of_hours.*.max' => __("validation.custom.no_of_hours.max"),

            'multiple_phone.*.required' => __("validation.custom.phone.required"),
            // 'multiple_phone.*.digits_between' => __("validation.custom.phone.digits_between"),

            'multiple_total_pax.*.required' => __("validation.custom.total_pax_booking.required"),
            'multiple_total_pax.*.numeric' => __("validation.custom.total_pax_booking.digits"),
            'multiple_total_pax.*.digits_between' => __("validation.digits_between"),


            'multiple_total_luggage.*.required' => __("validation.custom.total_luggage_booking.required"),
            'multiple_total_luggage.*.numeric' => __("validation.custom.total_luggage_booking.digits"),
            'multiple_total_luggage.*.max' => __("validation.custom.total_luggage_booking.max"),
        ];
    }
}
