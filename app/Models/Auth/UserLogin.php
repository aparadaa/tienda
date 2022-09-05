<?php
namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $connection = 'core';
    protected $guarded = ['id'];
}
