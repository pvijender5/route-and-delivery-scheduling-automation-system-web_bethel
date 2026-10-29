<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'location' => ['sometimes', 'array'],
            'location.name' => ['required_with:location', 'string', 'max:255'],
            'location.address_line_1' => ['required_with:location', 'string', 'max:255'],
            'location.city' => ['required_with:location', 'string', 'max:255'],
            'location.state' => ['required_with:location', 'string', 'max:255'],
            'location.zipcode' => ['required_with:location', 'string', 'max:20'],
            'location.contact_phone' => ['required_with:location', 'string', 'max:20'],
            'location.is_default' => ['sometimes', 'boolean']
        ];

        // Add unique validation for create
        if ($this->isMethod('post')) {
            $rules['customer_id'][] = 'unique:customers,customer_id';
            $rules['email'][] = 'unique:customers,email';
            $rules['phone'][] = 'unique:customers,phone';
        }

        // Modify unique validation for update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $customerId = $this->route('id');
            $rules['customer_id'][] = Rule::unique('customers', 'customer_id')->ignore($customerId);
            $rules['email'][] = Rule::unique('customers', 'email')->ignore($customerId);
            $rules['phone'][] = Rule::unique('customers', 'phone')->ignore($customerId);
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
            'customer_id.required' => 'The customer ID is required.',
            'customer_id.unique' => 'This customer ID is already in use.',
            'name.required' => 'The customer name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'This phone number is already in use.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either active or inactive.',
            'location.name.required_with' => 'The location name is required when adding a location.',
            'location.address_line_1.required_with' => 'The address is required when adding a location.',
            'location.city.required_with' => 'The city is required when adding a location.',
            'location.state.required_with' => 'The state is required when adding a location.',
            'location.zipcode.required_with' => 'The zipcode is required when adding a location.',
            'location.contact_phone.required_with' => 'The contact phone is required when adding a location.',
        ];
    }
} 