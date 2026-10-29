<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
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
            'route_number' => 'required|string|max:50|unique:routes',
            'delivery_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'status' => 'required|in:pending,active,completed,cancelled',
            'total_distance' => 'nullable|numeric|min:0',
            'total_stops' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'shipment_ids' => 'sometimes|array',
            'shipment_ids.*' => 'exists:shipments,id'
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['route_number'] = 'sometimes|required|string|max:50|unique:routes,route_number,' . $this->route;
            $rules['delivery_date'] = 'sometimes|required|date';
            $rules['start_time'] = 'sometimes|required|date_format:H:i';
            $rules['end_time'] = 'sometimes|required|date_format:H:i|after:start_time';
            $rules['driver_id'] = 'sometimes|required|exists:users,id';
            $rules['vehicle_id'] = 'sometimes|required|exists:vehicles,id';
            $rules['status'] = 'sometimes|required|in:pending,active,completed,cancelled';
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
            'route_number.required' => 'A route number is required.',
            'route_number.unique' => 'This route number is already in use.',
            'delivery_date.required' => 'A delivery date is required.',
            'delivery_date.date' => 'Please provide a valid delivery date.',
            'start_time.required' => 'A start time is required.',
            'start_time.date_format' => 'Please provide a valid start time in HH:mm format.',
            'end_time.required' => 'An end time is required.',
            'end_time.date_format' => 'Please provide a valid end time in HH:mm format.',
            'end_time.after' => 'The end time must be after the start time.',
            'driver_id.required' => 'A driver must be assigned to the route.',
            'driver_id.exists' => 'The selected driver is invalid.',
            'vehicle_id.required' => 'A vehicle must be assigned to the route.',
            'vehicle_id.exists' => 'The selected vehicle is invalid.',
            'status.required' => 'A status must be specified.',
            'status.in' => 'The selected status is invalid.',
            'shipment_ids.*.exists' => 'One or more selected shipments are invalid.'
        ];
    }
} 