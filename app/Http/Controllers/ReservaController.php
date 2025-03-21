<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionMail;

class ReservaController extends Controller
{
    public function index()
    {
        return Reserva::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'evento_id' => 'required|exists:eventos,id',
            'estado' => 'in:pendiente,confirmada,cancelada',
        ]);

        $reserva = Reserva::create($request->all());
        $usuario = Usuario::find($request->usuario_id);

        if ($usuario) {
            $mensaje = "Tu reserva para el evento ha sido registrada. Por favor, confirma tu asistencia.";

            Notificacion::create([
                'usuario_id' => $usuario->id,
                'mensaje' => $mensaje,
                'leida' => false
            ]);

            try {
                Mail::to($usuario->email)->send(new NotificacionMail($mensaje));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Reserva creada, pero hubo un error enviando el correo.',
                    'error' => $e->getMessage()
                ], 201);
            }
        }

        return response()->json($reserva, 201);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update($request->all());
        $usuario = Usuario::find($reserva->usuario_id);

        if ($usuario) {
            $mensaje = "";

            if ($request->estado === 'confirmada') {
                $mensaje = "Tu reserva ha sido confirmada. ¡Nos vemos en el evento!";
            } elseif ($request->estado === 'cancelada') {
                $mensaje = "Tu reserva ha sido cancelada. Si fue un error, por favor contáctanos.";
            }

            if ($mensaje) {
                Notificacion::create([
                    'usuario_id' => $usuario->id,
                    'mensaje' => $mensaje,
                    'leida' => false
                ]);

                try {
                    Mail::to($usuario->email)->send(new NotificacionMail($mensaje));
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Reserva actualizada, pero hubo un error enviando el correo.',
                        'error' => $e->getMessage()
                    ], 201);
                }
            }
        }

        return response()->json($reserva);
    }

    public function destroy($id)
    {
        Reserva::destroy($id);
        return response()->json(['message' => 'Reserva eliminada']);
    }
}
