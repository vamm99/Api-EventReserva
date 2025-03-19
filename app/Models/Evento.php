<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'capacidad',
        'organizador_id'
    ];

    public function organizador()
    {
        return $this->belongsTo(Usuario::class, 'organizador_id');
    }
}
