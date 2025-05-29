<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddEditHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|min:3',
            // 'term_conditions' => 'required|min:3',
            'status' => 'required|string',
            //rule for billing Agreement
            'per_trip_arr' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'per_trip_arr' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'per_trip_dep' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'per_trip_transfer' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'per_trip_delivery' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'per_hour_rate' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'peak_period_surcharge' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'mid_night_surcharge_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'midnight_surcharge_greater_then_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'arrivel_waiting_time' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'departure_and_transfer_waiting' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'last_min_request_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'last_min_request_greater_then_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'outside_city_surcharge_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'outside_city_surcharge_greater_then_23_seats' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'additional_stop' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
            'misc_charges' => 'nullable|regex:/^\d{1,8}(\.\d{1,2})?$/',
        ];
    }
    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __("validation.required"),
            'name.string' => __("validation.string"),
            // 'term_conditions.required' => __("validation.custom.term_conditions.required"),
            // 'term_conditions.min' => __("validation.custom.term_conditions.min"),

            'name.max' => __("validation.max.string"),
            'name.min' => __("validation.min.string"),

            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
            'per_trip_arr.regex' => __("validation.custom.decimal.regex"),


            'per_trip_dep.regex' => __("validation.custom.decimal.regex"),

            'per_trip_transfer.regex' => __("validation.custom.decimal.regex"),

            'per_trip_delivery.regex' =>  __("validation.custom.decimal.regex"),

            'per_hour_rate.regex' => __("validation.custom.decimal.regex"),

            'peak_period_surcharge.regex' => __("validation.custom.decimal.regex"),


            'mid_night_surcharge_23_seats.regex' => __("validation.custom.decimal.regex"),

            'midnight_surcharge_greater_then_23_seats.regex' =>  __("validation.custom.decimal.regex"),

            'arrivel_waiting_time.regex' => __("validation.custom.decimal.regex"),

            'departure_and_transfer_waiting.regex' =>  __("validation.custom.decimal.regex"),

            'last_min_request_23_seats.regex' =>  __("validation.custom.decimal.regex"),

            'last_min_request_greater_then_23_seats.regex' =>  __("validation.custom.decimal.regex"),

            'outside_city_surcharge_23_seats.regex' =>  __("validation.custom.decimal.regex"),

            'outside_city_surcharge_greater_then_23_seats.regex' =>  __("validation.custom.decimal.regex"),

            'additional_stop.regex' =>  __("validation.custom.decimal.regex"),
            'misc_charges.regex' =>  __("validation.custom.decimal.regex"),
        ];
    }
}
