<?php

namespace App\DTOs\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;

class UserFilterData
{
    public function __construct(
        public readonly ?int $role_id = null,
        public readonly ?string $status = null,
        public readonly ?string $search = null,
        public readonly ?int $carrier_owner_id = null,
        public readonly ?int $company_id = null,
        public readonly ?string $permission = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null,
        public readonly ?string $sort_by = 'created_at',
        public readonly ?string $sort_direction = 'desc',
        public readonly ?int $current_user_id = null,
        public readonly ?int $per_page = 15
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            role_id: $request->integer('role_id'),
            status: $request->get('status'),
            search: $request->get('search'),
            carrier_owner_id: $request->integer('carrier_owner_id'),
            company_id: $request->integer('company_id'),
            permission: $request->get('permission'),
            start_date: $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null,
            end_date: $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null,
            sort_by: $request->get('sort_by', 'created_at'),
            sort_direction: $request->get('sort_direction', 'desc'),
            current_user_id: $request->integer('current_user_id'),
            per_page: $request->integer('per_page', 15)
        );
    }
} 