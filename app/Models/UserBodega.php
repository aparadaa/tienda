<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class UserBodega extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public function bodegas()
    {
        return $this->hasMany(Bodega::class, 'id', 'bodega_id');
    }
}
