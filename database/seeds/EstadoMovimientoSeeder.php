<?php

use App\Models\EstadoMovimiento;
use Illuminate\Database\Seeder;

class EstadoMovimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadoMovimiento::truncate();

        EstadoMovimiento::create([
            'id'          => 1,
            'descripcion' => 'FINALIZADO',
        ]);

        EstadoMovimiento::create([
            'id'          => 2,
            'descripcion' => 'ENVIADO',
        ]);

        EstadoMovimiento::create([
            'id'          => 3,
            'descripcion' => 'RECIBIDO',
        ]);

    }
}
