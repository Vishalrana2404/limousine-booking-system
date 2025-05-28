<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkPeakPeriodStatusUpdateRequest extends FormRequest
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
            'peak_period_ids' => 'required|array',
            'peak_period_ids.*' => 'required|integer|exists:peak_periods,id',
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
            'peak_period_ids.required' => __("validation.required"),
            'peak_period_ids.array' => __("validation.array"),
            'peak_period_ids.*.required' => __("validation.required"),
            'peak_period_ids.*.integer' => __("validation.integer"),
            'peak_period_ids.*.exists' => __("validation.exists"),
            'status.required' => __("validation.required"),
            'status.string' => __("validation.string"),
        ];
    }
}
