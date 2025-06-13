<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Evento;
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

        $evento = Evento::find($request->evento_id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        if ($evento->capacidad <= 0) {
            return response()->json(['message' => 'El evento ha alcanzado su capacidad máxima'], 400);
        }

        // Decrementar la capacidad del evento
        $evento->capacidad -= 1;
        $evento->save();

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

    public function getReservasByUsuario($usuario_id)
    {
        $reservas = Reserva::where('usuario_id', $usuario_id)
            ->with('evento')
            ->get();

        return response()->json($reservas);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        // Validate estado value
        $allowedEstados = ['pendiente', 'confirmada', 'cancelada'];
        $estado = $request->estado;

        if (!in_array($estado, $allowedEstados)) {
            return response()->json([
                'message' => 'Estado inválido. Los valores permitidos son: pendiente, confirmada, cancelada'
            ], 400);
        }

        // Update only the estado field
        $reserva->estado = $estado;
        $reserva->save();

        $usuario = Usuario::find($reserva->usuario_id);

        if ($usuario) {
            $mensaje = "";

            if ($estado === 'confirmada') {
                $mensaje = "Tu reserva ha sido confirmada. ¡Nos vemos en el evento!";
            } elseif ($estado === 'cancelada') {
                // Incrementar la capacidad del evento al cancelar la reserva
                $evento = Evento::find($reserva->evento_id);
                if ($evento) {
                    $evento->capacidad += 1;
                    $evento->save();
                }
                // $reserva->delete(); // Eliminar la reserva al cancelarla
                // $reserva = null; // Para evitar enviar datos de reserva cancelada
                // Mensaje de cancelación
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
