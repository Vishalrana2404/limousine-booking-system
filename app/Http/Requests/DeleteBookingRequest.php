<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteBookingRequest extends FormRequest
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
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'required|integer|exists:bookings,id',
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
            'booking_ids.required' => __("validation.required"),
            'booking_ids.array' => __("validation.array"),
            'booking_ids.*.required' => __("validation.required"),
            'booking_ids.*.integer' => __("validation.integer"),
            'booking_ids.*.exists' => __("validation.exists"),
        ];
    }
}
