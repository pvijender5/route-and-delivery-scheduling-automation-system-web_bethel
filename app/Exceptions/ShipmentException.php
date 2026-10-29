<?php

namespace App\Exceptions;

use Exception;

class ShipmentException extends Exception
{
    /**
     * Create a new shipment exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 