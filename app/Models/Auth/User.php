<?php
namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use App\Models\CollectForm\Formulario;

class User extends Authenticatable
{
	use Notifiable;
	protected $connection = 'core';
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

	public function formularios() {
		return $this->belongsToMany(Formulario::class, 'collectform.usuarios_formularios', 'user_id', 'formulario_id');
	}

	public function getAvatarAttribute($value)
    {
		if ($value)
        {
			return $value;
		}
		else
        {
			return base64_encode(file_get_contents(public_path() . '/images/user-generic.jpg'));
		}
	}
}
