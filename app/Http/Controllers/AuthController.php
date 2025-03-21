<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\NotificacionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,usuario,organizador'
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        // 📩 Crear notificación de bienvenida
        $mensaje = "¡Bienvenido a EventReservas! Ya puedes gestionar tus eventos y reservas.";
        Notificacion::create([
            'usuario_id' => $usuario->id,
            'mensaje' => $mensaje,
            'leida' => false
        ]);

        // 📧 Enviar notificación por correo
        if (!empty($usuario->email)) {
            try {
                Mail::to($usuario->email)->send(new NotificacionMail($mensaje));
            } catch (\Exception $e) {
                Log::error("Error enviando email: " . $e->getMessage());
                return response()->json([
                    'message' => 'Usuario creado, pero hubo un error enviando el correo.',
                    'error' => $e->getMessage()
                ], 201);
            }
        }

        return response()->json(['success' => true, 'message' => 'Usuario creado con éxito!'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'usuario' => [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol
        ]], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
