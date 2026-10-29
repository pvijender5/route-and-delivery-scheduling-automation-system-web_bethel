<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', Password::defaults()],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'boolean',
            'profile' => 'sometimes|array',
            'profile.address' => 'required_with:profile|string',
            'profile.city' => 'required_with:profile|string',
            'profile.state' => 'required_with:profile|string',
            'profile.zip' => 'required_with:profile|string',
            'settings' => 'sometimes|array',
            'settings.notification_preferences' => 'sometimes|array',
            'settings.theme' => 'sometimes|string|in:light,dark,system'
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['email'] = 'sometimes|required|email|unique:users,email,' . $this->user;
            $rules['password'] = ['sometimes', 'required', Password::defaults()];
            $rules['roles'] = 'sometimes|required|array';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'roles.required' => 'At least one role must be assigned.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
            'profile.address.required_with' => 'Address is required when profile is present.',
            'profile.city.required_with' => 'City is required when profile is present.',
            'profile.state.required_with' => 'State is required when profile is present.',
            'profile.zip.required_with' => 'ZIP code is required when profile is present.',
            'settings.theme.in' => 'Invalid theme selection.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone)
            ]);
        }
    }
} 