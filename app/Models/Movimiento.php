<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $guarded = ['id'];

    public function estado()
    {
        return $this->belongsTo(EstadoMovimiento::class, 'id', 'estado_movimiento_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id', 'tipo_movimiento_id');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class);
    }

    public function detalles()
    {
        return $this->hasMany(MovimientoDetalle::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function bodegaRecibe()
    {
        return $this->belongsTo(Bodega::class, 'recibe_bodega_id');
    }
}
