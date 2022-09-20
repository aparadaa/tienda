<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->call('ModulesSeeder');
        $this->call('PermissionsSeeder');
        $this->call('ModulePermissionsSeeder');
        $this->call('MenuSeeder');
        $this->call('MovimientoTipoSeeder');
        $this->call('MovimientoTipoPagoSeeder');
        $this->call('EstadoMovimientoSeeder');
        Schema::enableForeignKeyConstraints();
    }
}
