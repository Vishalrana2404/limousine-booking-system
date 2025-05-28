<?php

namespace App\Http\Requests;

use App\Rules\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'current_password' => ['required', new CurrentPassword()],
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ];
    }
    
    public function messages()
    {
        return [
            'current_password.required' => __('validation.required_current_password'),
            'new_password.required' => __('validation.required_new_password'),
            'new_password.min' => __('validation.password_min_length'),
            'new_password.different' => __('validation.password_diffrent_from_current_password'),
            'confirm_password.required' => __('validation.confirm_password'),
            'confirm_password.same' => __('validation.password_not_match'),
        ];
    }
}
