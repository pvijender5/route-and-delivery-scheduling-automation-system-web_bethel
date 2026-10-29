<?php

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Shipment $shipment;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Shipment $shipment, string $oldStatus = '')
    {
        $this->shipment = $shipment;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $shipment->status;
    }
} 