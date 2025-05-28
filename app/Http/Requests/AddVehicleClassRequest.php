<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddVehicleClassRequest extends FormRequest
{
    /**
     * Determine if the vehicle class is authorized to make this request.
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
            'seating_capacity' => 'required|integer',
            'total_luggage' => 'required|integer',
            'total_pax' => 'required|integer|lte:seating_capacity',
            'status' => 'required|string',
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
            'name.required' => __("validation.custom.class_name.required"),
            'name.string' => __("validation.string"),
            'name.max' => __("validation.custom.class_name.max"),
            'name.min' => __("validation.custom.class_name.min"),

            'seating_capacity.required' => __("validation.custom.seating_capacity.required"),
            'seating_capacity.integer' => __("validation.integer"),

            'total_luggage.required' => __('validation.custom.total_luggage.required'),
            'total_luggage.integer' => __("validation.integer"),

            'total_pax.required' => __('validation.custom.total_pax.required'),
            'total_pax.integer' => __("validation.integer"),
            'total_pax.lte' => __("validation.custom.total_pax.lte"),

            'status.required' => __("validation.custom.status.required"),
            'status.string' => __("validation.string"),
        ];
    }
}
