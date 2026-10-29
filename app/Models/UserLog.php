<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    
    protected $table = 'user_logs';

    protected $fillable = [
        'user_id',
        'platform',
        'auth_token',
        'device_token',
        'ip_address',
        'is_logged_in',
        'is_logged_in',
        'login_time',
        'logout_time',        
    ];

}
