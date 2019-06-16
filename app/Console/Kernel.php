<?php

namespace App\Console;

use App\Console\Commands\CleanDemoSite;
use App\Console\Commands\CreateWatchlists;
use App\Console\Commands\UpdateListsFromRemote;
use App\Console\Commands\UpdateNewsFromRemote;
use Common\Settings\Settings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var array
     */
    protected $commands = [
        UpdateNewsFromRemote::class,
        UpdateListsFromRemote::class,
        CreateWatchlists::class,
        CleanDemoSite::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $settings = app(Settings::class);

        if ($settings->get('news.auto_update')) {
            $schedule->command('news:update')->daily();
        }

        if (config('common.site.demo')) {
            $schedule->command('demo:clean')->daily();
        }

        $schedule->command('lists:update')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
