<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

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
