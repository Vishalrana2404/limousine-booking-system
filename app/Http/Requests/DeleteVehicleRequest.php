<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteVehicleRequest extends FormRequest
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
        return [
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'required|integer|exists:vehicles,id',
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
            'vehicle_ids.required' => __("validation.required"),
            'vehicle_ids.array' => __("validation.array"),
            'vehicle_ids.*.required' => __("validation.required"),
            'vehicle_ids.*.integer' => __("validation.integer"),
            'vehicle_ids.*.exists' => __("validation.exists"),
        ];
    }
}
