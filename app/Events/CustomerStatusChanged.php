<?php

namespace App\Events;

use App\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Customer $customer;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer, string $oldStatus = '')
    {
        $this->customer = $customer;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $customer->status;
    }
} 