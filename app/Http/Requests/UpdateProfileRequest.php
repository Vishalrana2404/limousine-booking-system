<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'first_name' => 'string|max:50|min:3',
            'last_name' => 'string|max:50|min:3',
            // 'email' => 'string|email|max:255|unique:users,email,' . $userId . ',id',
        ];
    }
}
