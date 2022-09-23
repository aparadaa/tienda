<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMovmientoDetallesAddSubTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimiento_detalles', function (Blueprint $table) {
            $table->decimal('sub_total', 10, 2)->after('precio_venta_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimiento_detalles', function (Blueprint $table) {
            $table->dropColumn('sub_total');
        });
    }
}
