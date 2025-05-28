<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteEventRequest extends FormRequest
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
            'event_ids' => 'required|array',
            'event_ids.*' => 'required|integer|exists:events,id',
        ];
    }
    public function messages(): array
    {
        return [
            'event_ids.required' => __("validation.required"),
            'event_ids.array' => __("validation.array"),
            'event_ids.*.required' => __("validation.required"),
            'event_ids.*.integer' => __("validation.integer"),
            'event_ids.*.exists' => __("validation.exists"),
        ];
    }
}
