<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class RouteFilterData
{
    public function __construct(
        public readonly ?string $route_number = null,
        public readonly ?string $status = null,
        public readonly ?int $driver_id = null,
        public readonly ?string $driver_name = null,
        public readonly ?int $vehicle_id = null,
        public readonly ?string $vehicle_number = null,
        public readonly ?int $customer_id = null,
        public readonly ?string $location = null,
        public readonly ?bool $is_completed = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            route_number: $request->get('route_number'),
            status: $request->get('status'),
            driver_id: $request->get('driver_id'),
            driver_name: $request->get('driver_name'),
            vehicle_id: $request->get('vehicle_id'),
            vehicle_number: $request->get('vehicle_number'),
            customer_id: $request->get('customer_id'),
            location: $request->get('location'),
            is_completed: $request->boolean('is_completed'),
            start_date: $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null,
            end_date: $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null
        );
    }
} 