<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteHotelRequest extends FormRequest
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
            'hotel_ids' => 'required|array',
            'hotel_ids.*' => 'required|integer|exists:hotels,id',
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
            'hotel_ids.required' => __("validation.required"),
            'hotel_ids.array' => __("validation.array"),
            'hotel_ids.*.required' => __("validation.required"),
            'hotel_ids.*.integer' => __("validation.integer"),
            'hotel_ids.*.exists' => __("validation.exists"),
        ];
    }
}
