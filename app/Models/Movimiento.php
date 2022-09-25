<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $guarded = ['id'];

    public function estado()
    {
        return $this->belongsTo(EstadoMovimiento::class, 'estado_movimiento_id', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoMovimiento::class, 'tipo_movimiento_id', 'id');
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

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
