<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'locations' => 'sometimes|array',
            'locations.*.address' => 'required_with:locations|string',
            'locations.*.city' => 'required_with:locations|string',
            'locations.*.state' => 'required_with:locations|string',
            'locations.*.zip' => 'required_with:locations|string',
            'contacts' => 'sometimes|array',
            'contacts.*.name' => 'required_with:contacts|string',
            'contacts.*.email' => 'required_with:contacts|email',
            'contacts.*.phone' => 'required_with:contacts|string'
        ];

        if ($this->isMethod('POST')) {
            $rules['email'] .= '|unique:customers';
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['email'] .= '|unique:customers,email,' . $this->route('customer');
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
            'name.required' => 'The customer name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'The phone number is required.',
            'address.required' => 'The address is required.',
        ];
    }
} 