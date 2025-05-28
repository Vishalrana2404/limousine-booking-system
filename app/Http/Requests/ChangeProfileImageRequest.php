<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangeProfileImageRequest extends FormRequest
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
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

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
            'profile_image.required' => __("validation.required"),
            'profile_image.image' => __("validation.image"),
            'profile_image.max' => __("validation.custom.invalid_image_size"),
        ];
    }
}
