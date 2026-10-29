<?php

namespace App\Constants;

class ShipmentStatus
{
    public const PENDING = 'pending';
    public const IN_TRANSIT = 'in_transit';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    public const VALID_STATUSES = [
        self::PENDING,
        self::IN_TRANSIT,
        self::DELIVERED,
        self::CANCELLED
    ];
} 