<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'company_id' => 'required|exists:companies,id',
            'email_subject' => 'required|string|max:255',
            'email_content' => 'required|string',
            'target_emails' => 'required|array|min:1',
            'target_emails.*' => 'required|email',
            'launch_date' => 'nullable|date|after:now',
            'end_date' => 'nullable|date|after:launch_date',
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
            'name.required' => 'Campaign name is required.',
            'company_id.required' => 'Company selection is required.',
            'email_subject.required' => 'Email subject is required.',
            'email_content.required' => 'Email content is required.',
            'target_emails.required' => 'At least one target email is required.',
            'target_emails.*.email' => 'Each target email must be a valid email address.',
        ];
    }
}
