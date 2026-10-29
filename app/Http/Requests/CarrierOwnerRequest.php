<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarrierOwnerRequest extends FormRequest
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
            'carrier_company_name' => ['required', 'string', 'max:255'],
            'carrier_owner_name' => ['required', 'string', 'max:255'],
            'carrier_owner_id' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'primary_contact_number' => ['required', 'string', 'max:20'],
            'truck_id' => ['required', 'exists:trucks,id'],
            'status' => ['required', 'in:active,inactive'],
            'make_default' => ['boolean'],
            'resequencing_shipments_allowed' => ['boolean'],
        ];

        // Add unique validation for create
        if ($this->isMethod('post')) {
            $rules['carrier_company_name'][] = 'unique:driver_details,carrier_company_name';
            $rules['email'][] = 'unique:users,email';
            $rules['primary_contact_number'][] = 'unique:users,primary_contact_number';
        }

        // Modify unique validation for update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $carrierId = $this->route('id');
            $rules['carrier_company_name'][] = Rule::unique('driver_details', 'carrier_company_name')->ignore($carrierId, 'user_id');
            $rules['email'][] = Rule::unique('users', 'email')->ignore($carrierId);
            $rules['primary_contact_number'][] = Rule::unique('users', 'primary_contact_number')->ignore($carrierId);
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
            'carrier_company_name.required' => 'The carrier company name is required.',
            'carrier_company_name.unique' => 'This carrier company name is already in use.',
            'carrier_owner_name.required' => 'The carrier owner name is required.',
            'carrier_owner_id.required' => 'The carrier owner ID is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'primary_contact_number.required' => 'The contact number is required.',
            'primary_contact_number.unique' => 'This contact number is already in use.',
            'truck_id.required' => 'Please select a truck.',
            'truck_id.exists' => 'The selected truck is invalid.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
} 