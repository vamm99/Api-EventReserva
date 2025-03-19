<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ReporteController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Rutas solo para administradores
    Route::middleware('rol:admin')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
    });

    // Rutas para organizadores y administradores
    Route::middleware('rol:admin,organizador')->group(function () {
        Route::apiResource('eventos', EventoController::class);
        Route::apiResource('notificaciones', NotificacionController::class);
    });

    // Rutas accesibles para todos los usuarios autenticados
    Route::apiResource('reservas', ReservaController::class);

    // Rutas de reportes
    Route::middleware('rol:admin,organizador')->group(function () {
        Route::get('/reportes/reservas/{evento_id}', [ReporteController::class, 'reservasPorEvento']);
        Route::get('/reportes/asistencia/{evento_id}', [ReporteController::class, 'asistenciaPorEvento']);
        Route::get('/reportes/actividad-usuarios', [ReporteController::class, 'actividadUsuarios']);
        Route::get('/reportes/exportar/{tipo}/{id?}', [ReporteController::class, 'exportarCSV']);
    });
});
