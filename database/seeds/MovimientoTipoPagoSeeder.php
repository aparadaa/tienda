<?php

use App\Models\TipoPago;
use Illuminate\Database\Seeder;

class MovimientoTipoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPago::truncate();

        TipoPago::create([
            'id'          => 1,
            'descripcion' => 'Efectivo',
        ]);

        TipoPago::create([
            'id'          => 2,
            'descripcion' => 'Electrónico',
        ]);

        TipoPago::create([
            'id'          => 3,
            'descripcion' => 'Traslado',
        ]);
    }
}
