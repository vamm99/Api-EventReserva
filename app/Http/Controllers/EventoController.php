<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionMail;

class EventoController extends Controller
{
    public function index()
    {
        return Evento::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'capacidad' => 'required|integer|min:1',
            'organizador_id' => 'required|exists:usuarios,id'
        ]);


        $evento = Evento::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'capacidad' => $request->capacidad,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'organizador_id' => $request->organizador_id
        ]);

        return response()->json([
            'eventos' => $evento,
            'message' => 'Evento creado con éxito!'
        ], 201);
    }

    public function show($id)
    {
        return Evento::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $evento->update($request->all());

        // Notificar a todos los usuarios con reserva en este evento
        $usuarios = Usuario::whereHas('reservas', function ($query) use ($id) {
            $query->where('evento_id', $id);
        })->get();

        if ($usuarios->count() > 0) {
            $mensaje = "El evento '{$evento->titulo}' ha sido actualizado. Revisa los nuevos detalles.";

            foreach ($usuarios as $usuario) {
                Notificacion::create([
                    'usuario_id' => $usuario->id,
                    'mensaje' => $mensaje,
                    'leida' => false
                ]);

                try {
                    Mail::to($usuario->email)->send(new NotificacionMail($mensaje));
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Evento actualizado, pero hubo un error enviando los correos.',
                        'error' => $e->getMessage()
                    ], 201);
                }
            }
        }

        return response()->json($evento);
    }

    // Funcion para obtner la capacidad de cada evento
    public function getCapacidad($id)
    {
        $evento = Evento::findOrFail($id);
        return response()->json($evento->capacidad);
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $usuarios = Usuario::whereHas('reservas', function ($query) use ($id) {
            $query->where('evento_id', $id);
        })->get();

        $evento->delete();

        if ($usuarios->count() > 0) {
            $mensaje = "El evento '{$evento->titulo}' ha sido cancelado.";

            foreach ($usuarios as $usuario) {
                Notificacion::create([
                    'usuario_id' => $usuario->id,
                    'mensaje' => $mensaje,
                    'leida' => false
                ]);

                try {
                    Mail::to($usuario->email)->send(new NotificacionMail($mensaje));
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Evento eliminado, pero hubo un error enviando los correos.',
                        'error' => $e->getMessage()
                    ], 201);
                }
            }
        }

        return response()->json(['message' => 'Evento eliminado']);
    }
}
