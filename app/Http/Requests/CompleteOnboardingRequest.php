<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            // Company Details
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'fiscal_year_start' => 'nullable|string|max:5',
            'currency' => 'nullable|string|max:3',

            // Admin Details
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'personal_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($user) {
                    return $query->where('tenant_id', $user->tenant_id);
                })->ignore($user->id)
            ],
            'work_email' => 'nullable|email|max:255',
            'language' => 'nullable|string|max:10',
            'password' => 'required|string|min:8|confirmed',

            // Working Hours
            'working_hours' => 'nullable|array',
            'working_hours.monday' => 'nullable|array',
            'working_hours.tuesday' => 'nullable|array',
            'working_hours.wednesday' => 'nullable|array',
            'working_hours.thursday' => 'nullable|array',
            'working_hours.friday' => 'nullable|array',
            'working_hours.saturday' => 'nullable|array',
            'working_hours.sunday' => 'nullable|array',

            // Department
            'branch_name' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',

            // API Settings (only for Pro/Enterprise)
            'ai_provider' => 'nullable|string|max:50',
            'ai_model' => 'nullable|string|max:100',
            'ai_api_key' => 'nullable|string|max:1000',
            'google_calendar_api_key' => 'nullable|string|max:1000',
            'google_meet_api_key' => 'nullable|string|max:1000',

            // SMTP Settings (only for Pro/Enterprise)
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:1000',
            'smtp_encryption' => 'nullable|string|in:tls,ssl',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'personal_email.unique' => 'This email address is already in use.',
            'company_logo.max' => 'The company logo must not be larger than 2MB.',
            'company_logo.mimes' => 'The company logo must be a valid image file.',
        ];
    }
}
