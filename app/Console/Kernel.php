<?php

namespace App\Console;

use App\Models\Suspension;
use App\Models\User;
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
        $schedule->call(function() {
            $suspendedUsers = User::where('is_suspended', true)->get();
            $suspendedUsers->filter(function($user) {
                $info = $user->suspensionEndInfo();
                if (!isset($info) || !isset($info['end_date']))
                    return true;

                return strtotime($info['end_date']) <= strtotime(gmdate('d-m-Y'));
            })->each(function($user) {
                $user->is_suspended = false;
                $user->save();
            });
        })->daily();
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
