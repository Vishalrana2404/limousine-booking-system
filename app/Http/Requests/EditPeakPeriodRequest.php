<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditPeakPeriodRequest extends FormRequest
{
    /**
     * Determine if the vehicle class is authorized to make this request.
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
        $rules = [
            'event' => 'required|string|max:100|min:3',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
            'status' => 'required|string',
        ];
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
            'event.required' => __("validation.custom.event_name.required"),
            'event.string' => __("validation.string"),
            'event.max' => __("validation.custom.event_name.max"),
            'event.min' => __("validation.custom.event_name.min"),

            'start_date.required' => __("validation.custom.event_start_date.required"),
            'start_date.date_format' => __("validation.custom.event_start_date.regex"),

            'end_date.required' => __('validation.custom.event_end_date.required'),
            'end_date.date_format' => __("validation.custom.event_end_date.regex"),
            'end_date.after_or_equal' => __('validation.custom.event_end_date.after_or_equal'),

            'status.required' => __("validation.custom.status.required"),
            'status.string' => __("validation.string"),
        ];
    }
}

