<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $guarded = ['id'];
    
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
