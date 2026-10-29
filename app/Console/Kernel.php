<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\SendScheduledSMS::class,
    ];

    
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('sms:send-scheduled')->dailyAt('09:00');
        $browserTimeZone = config('app.env') == 'production' ? 'America/New_York' : 'Asia/Calcutta';
        $schedule->command('sms:send-scheduled')
        //->everyMinute()
        ->dailyAt('09:00')
        ->timezone($browserTimeZone);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
