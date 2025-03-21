<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Obtener todas las notificaciones de un usuario autenticado.
     */
    public function index()
    {
        $usuarioId = Auth::id(); // Obtener el usuario autenticado
        return response()->json(Notificacion::where('usuario_id', $usuarioId)->orderBy('created_at', 'desc')->get());
    }

    /**
     * Obtener una notificación específica.
     */
    public function show($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('usuario_id', Auth::id()) // Solo permitir ver sus propias notificaciones
            ->firstOrFail();

        return response()->json($notificacion);
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarComoLeida($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('usuario_id', Auth::id()) // Solo permitir marcar sus propias notificaciones
            ->firstOrFail();

        $notificacion->update(['leida' => true]);

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    /**
     * Eliminar una notificación.
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('usuario_id', Auth::id()) // Solo permitir eliminar sus propias notificaciones
            ->firstOrFail();

        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada']);
    }
}
