<?php
namespace App\Models\Auth;

use App\Models\Bodega;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $hidden  = ['password', 'remember_token'];
    protected $guarded = ['id'];
    protected $casts   = [
        'active' => 'boolean',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function roleIds()
    {
        return $this->roles->pluck('id')->toArray();
    }

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'user_bodegas');
    }

    public function getAvatarAttribute($value)
    {
        if ($value) {
            return $value;
        } else {
            return base64_encode(file_get_contents(public_path() . '/images/user-generic.jpg'));
        }
    }
}
