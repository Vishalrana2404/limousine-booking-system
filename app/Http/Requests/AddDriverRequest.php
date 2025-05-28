<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddDriverRequest extends FormRequest
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
            'name' => 'required|string|max:500|min:2',
            'country_code' => 'required|digits_between:1,3',
            'phone' => 'required|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255|unique:drivers,email',
            'type' => 'required',
            'gender' => 'required',
            'chat_id' => "required|min:11|max:11|regex:/^-?\d+$/"
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
            'name.required' => __("validation.custom.name.required"),
            'name.string' => __("validation.string"),
            'name.max' => __("validation.custom.driver_name.max"),
            'name.min' => __("validation.custom.driver_name.min"),

            'country_code.required' => __("validation.required"),
            'country_code.integer' => __("validation.integer"),
            'country_code.digits_between' => __("validation.custom.country_code.digits_between"),

            'phone.required' => __("validation.required"),
            'phone.regex' => __("validation.custom.phone.regex"),

            'email.max' =>  __("validation.max.string"),
            'email.unique' => __("validation.unique"),
            'type.required' => __("validation.custom.type"),
            'gender.required' => __("validation.custom.gender"),
        ];
    }
}
