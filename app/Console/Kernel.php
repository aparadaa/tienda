<?php

namespace App\Console;

use App\Jobs\ArticulosJob;
use App\Jobs\CuentasContablesJob;
use App\Jobs\EntradasOCJob;
use App\Jobs\EntradasOCPendientesJob;
use App\Jobs\ExistenciasArticulosJob;
use App\Jobs\GetOCompraJob;
use App\Jobs\PreciosArticulosJob;
use App\Jobs\SalidasInvPendientesJob;
use App\Jobs\SolicitudesCompradorPendientesJob;
use App\Jobs\TrasladosPendientesJob;
use App\Jobs\TruncateUserSessionJob;
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
       
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
