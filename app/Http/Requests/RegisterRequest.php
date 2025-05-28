<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'first_name' => 'required|string',
            'country_code' => 'required|string',
            'phone' => 'required|digits_between:7,15',
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
            'email.required' => __("validation.required"),
            'email.string' => __("validation.string"),
            'email.email' => __("validation.email"),
            'first_name.required' => __("validation.required"),
            'country_code.required' => __("validation.required"),
            'phone.required' => __("validation.required"),
        ];
    }
}
