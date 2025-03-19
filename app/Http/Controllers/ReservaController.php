<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

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
        return response()->json($reserva, 201);
    }

    public function show($id)
    {
        return Reserva::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update($request->all());
        return response()->json($reserva);
    }

    public function destroy($id)
    {
        Reserva::destroy($id);
        return response()->json(['message' => 'Reserva eliminada']);
    }
}
