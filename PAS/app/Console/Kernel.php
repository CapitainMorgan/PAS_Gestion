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
        // $schedule->command('inspire')->hourly();
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

    protected function scheduleTimezone()
    {
        return 'UTC';
    }

    /**
     * Define middleware for the application.
     * 
     * @return void
     */
    protected $routeMiddleware = [
        // Autres middlewares ici...
    
        'checkArticlePermissions' => \App\Http\Middleware\CheckArticlePermissions::class,
        'checkFournisseurPermissions' => \App\Http\Middleware\CheckFournisseurPermissions::class,
        'checkVentePermissions' => \App\Http\Middleware\CheckVentePermissions::class,
        'checkUserPermissions' => \App\Http\Middleware\CheckUserPermissions::class,
        'checkDepotPermissions' => \App\Http\Middleware\CheckDepotPermissions::class,
        'checkFraisSocietePermissions' => \App\Http\Middleware\CheckFraisSocietePermissions::class,
        // Ajouter d'autres middlewares pour les autres tables
    ];
}
