<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol'
    ];

    protected $hidden = [
        'password',
    ];

    public function esAdmin()
    {
        return $this->rol === 'admin';
    }

    public function esUsuario()
    {
        return $this->rol === 'usuario';
    }
}
