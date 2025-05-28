<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        $rules = [
            'first_name' => 'required|string|max:50|min:2',
            'last_name' => 'required|string|max:50|min:2',
            'user_type' => 'required|integer',
            'department' => 'required',
            'status' => 'required|string',
            'country_code' => 'required|digits_between:1,3',
            'phone' => 'required|regex:/^[0-9]+$/',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($userId) {
                    $query->whereNull('deleted_at')->where('id', '!=', $userId);
                }),
            ],

        ];

        // If password is provided, add validation rules for it
        if ($this->filled('password')) {
            $rules['password'] = 'required|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s])(?!.*\s).{8,}$/';
        }

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
            'first_name.required' => __("validation.required"),
            'first_name.string' => __("validation.string"),
            'first_name.max' => __("validation.max.string"),
            'first_name.min' => __("validation.min.string"),

            'last_name.required' => __("validation.required"),
            'last_name.string' => __("validation.string"),
            'last_name.max' => __("validation.max.string"),
            'last_name.min' => __("validation.min.string"),

            'user_type.required' => __('validation.required'),
            'user_type.integer' => __("validation.integer"),

            'department.required' => __('validation.custom.department.required'),

            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
            'country_code.required' => __("validation.required"),
            'country_code.integer' => __("validation.integer"),
            'country_code.digits_between' => __("validation.custom.country_code.digits_between"),
            'phone.required' => __("validation.required"),
            'phone.regex' => __("validation.custom.phone.regex"),

            'email.required' => __("validation.required"),
            'email.string' => __("validation.string"),
            'email.email' => __("validation.email"),
            'email.max' =>  __("validation.max.string"),
            'email.unique' => __("validation.unique"),
            // 'password.required' => __("validation.custom.password_required"),
            // 'password.min' => __("validation.custom.password_min_length"),
            // 'password.regex' => __("validation.custom.password_regex"),

        ];
    }
}
