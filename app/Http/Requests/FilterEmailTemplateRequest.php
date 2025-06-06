<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max.string'),
            'subject.string' => __('validation.string'),
            'subject.max' => __('validation.max.string'),
        ];
    }
}
