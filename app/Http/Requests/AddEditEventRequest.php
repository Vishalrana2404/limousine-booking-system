<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddEditEventRequest extends FormRequest
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
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;

        return [
            'hotel_id' => ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff'])) ? 'required' : 'nullable',
            'name' => 'required|string',
            'status' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => __("validation.required"),
            'name.required' => __("validation.required"),
            'name.string' => __("validation.string"),

            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
        ];
    }
}
