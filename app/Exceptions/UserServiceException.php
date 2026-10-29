<?php

namespace App\Exceptions;

use Exception;

class UserServiceException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error'
        ], 500);
    }
} 