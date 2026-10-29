<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipmentFilterData
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int $customer_id = null,
        public readonly ?string $customer_name = null,
        public readonly ?int $route_id = null,
        public readonly ?string $route_number = null,
        public readonly ?string $tracking_number = null,
        public readonly ?string $delivery_status = null,
        public readonly ?bool $is_priority = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->get('status'),
            customer_id: $request->get('customer_id'),
            customer_name: $request->get('customer_name'),
            route_id: $request->get('route_id'),
            route_number: $request->get('route_number'),
            tracking_number: $request->get('tracking_number'),
            delivery_status: $request->get('delivery_status'),
            is_priority: $request->boolean('is_priority'),
            start_date: $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null,
            end_date: $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null
        );
    }
} 