<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class UserFilterData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $role = null,
        public readonly ?bool $is_active = null,
        public readonly ?bool $is_driver = null,
        public readonly ?bool $is_merchandiser = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null,
        public readonly ?string $sort_by = 'created_at',
        public readonly ?string $sort_direction = 'desc'
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->get('name'),
            email: $request->get('email'),
            role: $request->get('role'),
            is_active: $request->boolean('is_active'),
            is_driver: $request->boolean('is_driver'),
            is_merchandiser: $request->boolean('is_merchandiser'),
            start_date: $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null,
            end_date: $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null,
            sort_by: $request->get('sort_by', 'created_at'),
            sort_direction: $request->get('sort_direction', 'desc')
        );
    }
} 