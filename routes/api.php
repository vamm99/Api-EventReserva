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
    Route::get('/me', [AuthController::class, 'me']);

    // Rutas solo para administradores
    Route::middleware('rol:admin')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
    });

    // Rutas para organizadores y administradores
    Route::middleware('rol:admin,organizador,usuario')->group(function () {
        Route::apiResource('eventos', EventoController::class);
    });

    Route::get('eventos/{id}/capacidad', [EventoController::class, 'getCapacidad']);

    // Rutas accesibles para todos los usuarios autenticados
    Route::apiResource('reservas', ReservaController::class);
    Route::get('reservas/usuario/{usuario_id}', [ReservaController::class, 'getReservasByUsuario']);

    // Rutas de notificaciones (para cualquier usuario autenticado)
    Route::get('notificaciones', [NotificacionController::class, 'index']); // Listar notificaciones del usuario autenticado
    Route::get('notificaciones/{id}', [NotificacionController::class, 'show']); // Ver una notificación específica
    Route::put('notificaciones/{id}/leida', [NotificacionController::class, 'marcarComoLeida']); // Marcar como leída
    Route::delete('notificaciones/{id}', [NotificacionController::class, 'destroy']); // Eliminar notificación
    // Ruta para actualizar la contraseña
    Route::put('password/{id}', [UsuarioController::class, 'updatePassword']);


    // Rutas de reportes
    Route::middleware('rol:admin,organizador')->group(function () {
        Route::get('/reportes/reservas/{evento_id}', [ReporteController::class, 'reservasPorEvento']);
        Route::get('/reportes/asistencia/{evento_id}', [ReporteController::class, 'asistenciaPorEvento']);
        Route::get('/reportes/actividad-usuarios', [ReporteController::class, 'actividadUsuarios']);
        Route::get('/reportes/exportar/{tipo}/{id?}', [ReporteController::class, 'exportarCSV']);
        Route::get('reporte/reservas-por-fecha/{fecha_unix}', [ReporteController::class, 'reservasPorFechaUnix']);
    });
});
