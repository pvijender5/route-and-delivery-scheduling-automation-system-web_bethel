<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'delivery_date' => 'required|date',
            'total_stops' => 'required|integer|min:1',
            'origin_points' => 'required|string',
            'last_dropoff_location' => 'required|string',
            'total_quantity' => 'required|numeric|min:0',
            'planned_departure_time' => 'required|date_format:Y-m-d H:i:s',
            'fork_lift' => 'required|boolean',
            'assigned_driver' => 'required|exists:users,id',
            'status' => 'required|string',
            'shipments' => 'required|array|min:1',
            'shipments.*.shipment_id' => 'required|exists:shipments,id',
            'shipments.*.delivery_order' => 'required|integer|min:1',
            'shipments.*.estimated_delivery_time' => 'required|date_format:Y-m-d H:i:s',
            'shipments.*.delivery_status' => 'required|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'delivery_date.required' => 'The delivery date is required.',
            'delivery_date.date' => 'The delivery date must be a valid date.',
            'total_stops.required' => 'The total number of stops is required.',
            'total_stops.integer' => 'The total stops must be an integer.',
            'total_stops.min' => 'The total stops must be at least 1.',
            'origin_points.required' => 'The origin points are required.',
            'last_dropoff_location.required' => 'The last dropoff location is required.',
            'total_quantity.required' => 'The total quantity is required.',
            'total_quantity.numeric' => 'The total quantity must be a number.',
            'total_quantity.min' => 'The total quantity must be at least 0.',
            'planned_departure_time.required' => 'The planned departure time is required.',
            'planned_departure_time.date_format' => 'The planned departure time must be in the format Y-m-d H:i:s.',
            'fork_lift.required' => 'The fork lift field is required.',
            'fork_lift.boolean' => 'The fork lift field must be true or false.',
            'assigned_driver.required' => 'The assigned driver is required.',
            'assigned_driver.exists' => 'The selected driver is invalid.',
            'status.required' => 'The status is required.',
            'shipments.required' => 'At least one shipment is required.',
            'shipments.array' => 'The shipments must be an array.',
            'shipments.min' => 'At least one shipment is required.',
            'shipments.*.shipment_id.required' => 'The shipment ID is required.',
            'shipments.*.shipment_id.exists' => 'The selected shipment is invalid.',
            'shipments.*.delivery_order.required' => 'The delivery order is required.',
            'shipments.*.delivery_order.integer' => 'The delivery order must be an integer.',
            'shipments.*.delivery_order.min' => 'The delivery order must be at least 1.',
            'shipments.*.estimated_delivery_time.required' => 'The estimated delivery time is required.',
            'shipments.*.estimated_delivery_time.date_format' => 'The estimated delivery time must be in the format Y-m-d H:i:s.',
            'shipments.*.delivery_status.required' => 'The delivery status is required.'
        ];
    }
} 