<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function index()
    {
        return Usuario::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:6',
            'rol' => 'in:admin,organizador,usuario',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol ?? 'usuario',
        ]);

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        return Usuario::findOrFail($id);
    }

    // Función para actualizar solo la contraseña
    public function updatePassword(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        $usuario->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        // Verificar si el campo 'password' está presente en la solicitud
        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $usuario->update($request->all());
        return response()->json($usuario);
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
