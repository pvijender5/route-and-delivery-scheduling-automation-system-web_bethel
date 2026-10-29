<?php

namespace App\Constants;

class Commonconstant
{
	//Regional zone code for sms
    const REGIONZONE = '+91'; //Please use for India +91, USA +1

    //Covert into miles
    const MILES = '0.00062137';
    
	//Two timezones for Florida
	const TIMEZONE = [
        'America/Chicago' => 'America/Chicago',
        'America/New_York' => 'America/New_York',
        'America/Puerto_Rico'=>'America/Puerto_Rico',
        'America/Boise'=>'America/Boise',
        'America/Phoenix'=>'America/Phoenix',
        'America/Los_Angeles'=>'America/Los_Angeles',
        'America/Juneau'=>'AAmerica/JuneauKDT',
        'Pacific/Honolulu'=>'Pacific/Honolulu',
        'Pacific/Guam'=>'Pacific/Guam',
        'Pacific/Samoa'=>'Pacific/Samoa',
        'Pacific/Wake'=>'Pacific/Wake',
    ];

    //For use mobile SMS
    const SMSMODULENAME = [
        'DRIVER_ASSIGNED' => 'driver assigned',
        'IN_TRANSIT_ON_TIME' => 'in transit on time',
        'DELIVERED' => 'delivered',
        'INVITE_EMAIL' => 'invite email',
        'FORGOT_EMAIL' => 'forgot email',
        // 'POSTED_TO_LOADBOARD' => 'posted to loadboard',
        // 'LOADED' => 'loaded',
        // 'IN_TRANSIT_DELAY' => 'in transit delay',
        // 'IN_TRANSIT_ARRIVED' => 'in transit arrived',
        // 'IN_TRANSIT_CANCELLED' => 'in transit cancelled',
        // 'ASSIGNED_TO_WAREHOUSE' => 'assigned to warehouse',
        // 'IN_PROGRESS' => 'in-progress',
        // 'COMPLETED' => 'completed',
    ]; 

    const STATUSCOLOR = [
        'planned' => '#0f0098',
        'posted-to-loadboard' => '#0f0098',
        'driver-assigned' => '#0f0098',
        'loaded' => '#0f0098',
        'loading' => '#0f0098',
        'in-transit-on-time' => '#0f0098',
        'in-transit-delay' => '#0f0098',
        'in-transit-arrived' => '#0f0098',
        'in-transit-cancelled' => '#0f0098',
        'delivered' => '#0f0098',
        'completed' => '#0f0098',
    ];

}