<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'template_ids' => 'required|array',
            'template_ids.*' => 'required|integer|exists:email_templates,id',
        ];
    }

    public function messages(): array
    {
        return [
            'template_ids.required' => __('validation.required'),
            'template_ids.array' => __('validation.array'),
            'template_ids.*.required' => __('validation.required'),
            'template_ids.*.integer' => __('validation.integer'),
            'template_ids.*.exists' => __('validation.exists'),
        ];
    }
}
