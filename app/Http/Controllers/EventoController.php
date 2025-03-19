<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        return Evento::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'capacidad' => 'required|integer|min:1',
            'organizador_id' => 'required|exists:usuarios,id',
        ]);

        $evento = Evento::create($request->all());
        return response()->json($evento, 201);
    }

    public function show($id)
    {
        return Evento::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $evento->update($request->all());
        return response()->json($evento);
    }

    public function destroy($id)
    {
        Evento::destroy($id);
        return response()->json(['message' => 'Evento eliminado']);
    }
}
