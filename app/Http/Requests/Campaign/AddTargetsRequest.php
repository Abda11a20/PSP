<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class AddTargetsRequest extends FormRequest
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
            'targets' => 'required|array|min:1',
            'targets.*.name' => 'required|string|max:255',
            'targets.*.email' => 'required|email|max:255',
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
            'targets.required' => 'At least one target is required.',
            'targets.array' => 'Targets must be provided as an array.',
            'targets.min' => 'At least one target is required.',
            'targets.*.name.required' => 'Target name is required.',
            'targets.*.name.string' => 'Target name must be a string.',
            'targets.*.name.max' => 'Target name must not exceed 255 characters.',
            'targets.*.email.required' => 'Target email is required.',
            'targets.*.email.email' => 'Target email must be a valid email address.',
            'targets.*.email.max' => 'Target email must not exceed 255 characters.',
        ];
    }
}
