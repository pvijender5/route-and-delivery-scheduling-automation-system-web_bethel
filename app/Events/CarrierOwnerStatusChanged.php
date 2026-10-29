<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarrierOwnerStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $carrierOwner;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(User $carrierOwner, string $oldStatus = '')
    {
        $this->carrierOwner = $carrierOwner;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $carrierOwner->status;
    }
} 