<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteDriverRequest extends FormRequest
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
            'driver_ids' => 'required|array',
            'driver_ids.*' => 'required|integer|exists:drivers,id',
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
            'driver_ids.required' => __("validation.required"),
            'driver_ids.array' => __("validation.array"),
            'driver_ids.*.required' => __("validation.required"),
            'driver_ids.*.integer' => __("validation.integer"),
            'driver_ids.*.exists' => __("validation.exists"),
        ];
    }
}
