<?php

use App\Models\TipoMovimiento;
use Illuminate\Database\Seeder;

class MovimientoTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoMovimiento::truncate();

        TipoMovimiento::create([
            'id'     => 1,
            'nombre' => 'Entrada',
            'signo'  => '+',
        ]);

        TipoMovimiento::create([
            'id'     => 2,
            'nombre' => 'Venta',
            'signo'  => '-',
        ]);

        TipoMovimiento::create([
            'id'     => 3,
            'nombre' => 'Traslado',
            'signo'  => '-',
        ]);
    }
}
