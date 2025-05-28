<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditVehicleRequest extends FormRequest
{
    /**
     * Determine if the vehicle is authorized to make this request.
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
        $vehicleId = $this->route('vehicle')->id;
        $rules = [
            'vehicle_class' => 'required|string',
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,'. $vehicleId .',id',
            'image' => 'image|mimes:jpeg,png,jpg',
            'status' => 'required|string',
        ];
        return $rules;
    }
    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_class.required' => __("validation.custom.vehicle_class.required"),
            'vehicle_class.string' => __("validation.string"),

            'vehicle_number.required' => __("validation.custom.vehicle_number.required"),
            'vehicle_number.string' => __("validation.string"),
            'vehicle_number.unique' => __("validation.unique"),

            'image.image' => __("validation.image"),
            'image.mimes' => __('validation.mimes'),


            'status.required' => __("validation.custom.status.required"),
            'status.string' => __("validation.string"),
        ];
    }
}

