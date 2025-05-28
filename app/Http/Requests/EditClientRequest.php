<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditClientRequest extends FormRequest
{
    /**
     * Determine if the Client is authorized to make this request.
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
    public function rules(Request $request): array
    {
        $userId = $request->input('user_id');
        return [
            'first_name' => 'required|string|max:50|min:2',
            // 'last_name' => 'required|string|max:50|min:2',
            'client_type' => 'required|integer',
            'hotel_id' => 'required',
            'status' => 'required|string|in:ACTIVE,INACTIVE',
            'country_code' => 'nullable|digits_between:1,3',
            'phone' => 'nullable|regex:/^[0-9]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($userId) {
                    $query->whereNull('deleted_at')->where('id', '!=', $userId);
                }),
            ],
            'invoice' => 'required|string|max:50|min:2',
            'event' => 'nullable|string|max:50|min:3',
           
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
            'first_name.required' => __("validation.required"),
            'first_name.string' => __("validation.string"),
            'first_name.max' => __("validation.max.string"),
            'first_name.min' => __("validation.min.string"),

            // 'last_name.required' => __("validation.required"),
            // 'last_name.string' => __("validation.string"),
            // 'last_name.max' => __("validation.max.string"),
            // 'last_name.min' => __("validation.min.string"),

            'client_type.required' => __('validation.required'),
            'client_type.integer' => __("validation.integer"),
            'hotel_id.required' => __("validation.custom.client.required"),
            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
            'status.in' =>   __("validation.custom.status.custum_status"),
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

            'invoice.required' => __("validation.required"),
            'invoice.string' => __("validation.string"),
            'invoice.max' => __("validation.max.string"),
            'invoice.min' => __("validation.min.string"),

            'event.required' => __("validation.required"),
            'event.string' => __("validation.string"),
            'event.max' => __("validation.max.string"),
            'event.min' => __("validation.min.string"),
        ];
    }
}
