<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkUserStatusUpdateRequest extends FormRequest
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
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|integer|exists:users,id',
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
            'user_ids.required' => __("validation.required"),
            'user_ids.array' => __("validation.array"),
            'user_ids.*.required' => __("validation.required"),
            'user_ids.*.integer' => __("validation.integer"),
            'user_ids.*.exists' => __("validation.exists"),
            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
        ];
    }
}
