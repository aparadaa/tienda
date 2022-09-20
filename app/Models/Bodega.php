<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $guarded = ['id'];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_bodegas');
    }
}
