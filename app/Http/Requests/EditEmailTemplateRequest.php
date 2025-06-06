<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $templateId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('email_templates')->ignore($templateId)->whereNull('deleted_at')],
            'subject' => 'required|string|max:255',
            'header' => 'required|string',
            'footer' => 'required|string',
            'message' => 'required|string',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.unique' => __('validation.unique'),
            'subject.required' => __('validation.required'),
            'header.required' => __('validation.required'),
            'footer.required' => __('validation.required'),
            'message.required' => __('validation.required'),
            'qr_code_image.image' => __('validation.image'),
            'qr_code_image.mimes' => __('validation.mimes'),
            'qr_code_image.max' => __('validation.max.file'),
        ];
    }
}
