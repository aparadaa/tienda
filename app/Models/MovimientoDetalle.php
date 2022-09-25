<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoDetalle extends Model
{
    protected $guarded = ['id'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
