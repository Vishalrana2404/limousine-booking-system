<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteClientRequest extends FormRequest
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
            'client_ids' => 'required|array',
            'client_ids.*' => 'required|integer|exists:clients,id',
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
            'client_ids.required' => __("validation.required"),
            'client_ids.array' => __("validation.array"),
            'client_ids.*.required' => __("validation.required"),
            'client_ids.*.integer' => __("validation.integer"),
            'client_ids.*.exists' => __("validation.exists"),
        ];
    }
}
