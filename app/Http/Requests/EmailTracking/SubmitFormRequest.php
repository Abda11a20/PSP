<?php

namespace App\Http\Requests\EmailTracking;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint for phishing simulation
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|max:255', // We don't store this anyway
            'department' => 'nullable|string|max:255',
            'timestamp' => 'required|date',
            'campaign_type' => 'nullable|string|max:50',
            'template_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'password.max' => 'Password cannot exceed 255 characters.',
            'department.max' => 'Department name cannot exceed 255 characters.',
            'timestamp.required' => 'Timestamp is required.',
            'timestamp.date' => 'Please provide a valid timestamp.',
            'campaign_type.max' => 'Campaign type cannot exceed 50 characters.',
            'template_name.max' => 'Template name cannot exceed 255 characters.',
        ];
    }
}
