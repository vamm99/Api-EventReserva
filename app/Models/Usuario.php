<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['nombre', 'email', 'password', 'rol'];

    protected $hidden = ['password'];

    protected $casts = [
        'rol' => 'string',
    ];
}
