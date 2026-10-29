<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_id' => ['required', 'string', 'max:255'],
            'route_date' => ['required', 'date'],
            'status' => ['sometimes', 'string', 'in:pending,in_transit,delivered,cancelled'],
            'fork_lift' => ['sometimes', 'boolean'],
            'delivery_window' => ['required', 'array'],
            'delivery_window.*.start' => ['required', 'date_format:H:i'],
            'delivery_window.*.end' => ['required', 'date_format:H:i', 'after:delivery_window.*.start'],
            'notes' => ['sometimes', 'array'],
            'notes.*.text' => ['required_with:notes', 'string'],
            'delivery_location' => ['required', 'array'],
            'delivery_location.name' => ['required', 'string', 'max:255'],
            'delivery_location.address_line_1' => ['required', 'string', 'max:255'],
            'delivery_location.city' => ['required', 'string', 'max:255'],
            'delivery_location.state' => ['required', 'string', 'max:255'],
            'delivery_location.zipcode' => ['required', 'string', 'max:20'],
            'delivery_location.contact_phone' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'The selected customer is invalid.',
            'warehouse_id.required' => 'Please select a warehouse.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',
            'order_id.required' => 'The order ID is required.',
            'route_date.required' => 'The route date is required.',
            'route_date.date' => 'Please enter a valid date.',
            'status.in' => 'The status must be one of: pending, in transit, delivered, cancelled.',
            'delivery_window.required' => 'The delivery window is required.',
            'delivery_window.*.start.required' => 'The delivery window start time is required.',
            'delivery_window.*.start.date_format' => 'The delivery window start time must be in HH:MM format.',
            'delivery_window.*.end.required' => 'The delivery window end time is required.',
            'delivery_window.*.end.date_format' => 'The delivery window end time must be in HH:MM format.',
            'delivery_window.*.end.after' => 'The delivery window end time must be after the start time.',
            'delivery_location.required' => 'The delivery location details are required.',
            'delivery_location.name.required' => 'The delivery location name is required.',
            'delivery_location.address_line_1.required' => 'The delivery address is required.',
            'delivery_location.city.required' => 'The delivery city is required.',
            'delivery_location.state.required' => 'The delivery state is required.',
            'delivery_location.zipcode.required' => 'The delivery zipcode is required.',
            'delivery_location.contact_phone.required' => 'The delivery contact phone is required.',
        ];
    }
} 