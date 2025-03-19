<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\NotificacionController;

Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('eventos', EventoController::class);
Route::apiResource('reservas', ReservaController::class);
Route::apiResource('notificaciones', NotificacionController::class);
