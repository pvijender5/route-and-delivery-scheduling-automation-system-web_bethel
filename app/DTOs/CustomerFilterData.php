<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerFilterData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $location = null,
        public readonly ?bool $is_active = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->get('name'),
            email: $request->get('email'),
            phone: $request->get('phone'),
            location: $request->get('location'),
            is_active: $request->boolean('is_active'),
            start_date: $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null,
            end_date: $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null
        );
    }
} 