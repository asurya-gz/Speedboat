<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sync from WooCommerce (Online → Offline) every 1 minute for near real-time
        $schedule->command('woocommerce:sync-from --limit=50')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Sync to WooCommerce (Offline → Online) every 1 minute as backup
        // (Real-time sync happens via Event Listener, this is for retry failed syncs)
        $schedule->command('woocommerce:sync-to --retry')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Sync master data every 5 minutes (tidak perlu sering)
        $schedule->command('woocommerce:sync-master-data')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
