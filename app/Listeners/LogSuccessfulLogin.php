<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use App\Models\UserLog;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ipAddress = Request::ip();
        UserLog::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'login_time' => now(),
            'platform' => isset($user->device_token) ? 'mobile' : 'web',
            'is_logged_in' => true,
            'device_token' => $user->device_token,
            'auth_token' => $event->remember,
        ]);
    }
}
