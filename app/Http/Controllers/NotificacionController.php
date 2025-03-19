<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Mail\NotificacionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    public function index()
    {
        return Notificacion::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'mensaje' => 'required|string',
        ]);

        $notificacion = Notificacion::create([
            'usuario_id' => $request->usuario_id,
            'mensaje' => $request->mensaje,
            'leida' => false
        ]);

        // Enviar correo electrónico
        $usuario = Usuario::find($request->usuario_id);
        if ($usuario && !empty($usuario->email)) {
            try {
                Mail::to($usuario->email)->send(new NotificacionMail($request->mensaje));
            } catch (\Exception $e) {
                Log::error("Error enviando email: " . $e->getMessage());
                return response()->json([
                    'message' => 'Notificación creada, pero hubo un error enviando el correo.'
                ], 201);
            }
        }

        return response()->json($notificacion, 201);
    }

    public function show($id)
    {
        return Notificacion::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update($request->all());
        return response()->json($notificacion);
    }

    public function destroy($id)
    {
        Notificacion::destroy($id);
        return response()->json(['message' => 'Notificación eliminada']);
    }
}
