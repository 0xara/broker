<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('exchange:watch-binance-price')->everyMinute();

        $schedule->command('exchange:watch-tehran-stock-exchange-price')->everyTwoMinutes();

        $schedule->command('exchange:broadcast-tehran-stock-exchange-price')->everyMinute();

        $schedule->command('exchange:watch-tehran-stock-exchange-datalist')->daily();

        $schedule->command('exchange:watch-currency-exchange-price')->everyTenMinutes();

        $schedule->command('exchange:broadcast-currency-exchange-price')->everyMinute();
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
