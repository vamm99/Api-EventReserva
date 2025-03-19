<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Reserva;
use App\Models\Usuario;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // Reporte de reservas por evento
    public function reservasPorEvento($evento_id)
    {
        $evento = Evento::findOrFail($evento_id);
        $reservas = Reserva::where('evento_id', $evento_id)->get();

        return response()->json([
            'evento' => $evento->titulo,
            'total_reservas' => $reservas->count(),
            'reservas' => $reservas
        ]);
    }

    // Reporte de asistencia por evento
    public function asistenciaPorEvento($evento_id)
    {
        $evento = Evento::findOrFail($evento_id);
        $asistencias = Reserva::where('evento_id', $evento_id)
            ->where('estado', 'confirmada')
            ->count();

        return response()->json([
            'evento' => $evento->titulo,
            'asistencias' => $asistencias,
            'capacidad' => $evento->capacidad
        ]);
    }

    // Reporte de actividad de usuarios
    public function actividadUsuarios()
    {
        $usuarios = Usuario::withCount('reservas')->get();
        return response()->json($usuarios);
    }

    // Exportar reporte a CSV
    public function exportarCSV($tipo, $id = null)
    {
        $response = new StreamedResponse(function () use ($tipo, $id) {
            $handle = fopen('php://output', 'w');

            if ($tipo === 'reservas') {
                fputcsv($handle, ['Evento ID', 'Total Reservas']);
                fputcsv($handle, [$id, Reserva::where('evento_id', $id)->count()]);
            } elseif ($tipo === 'asistencia') {
                fputcsv($handle, ['Evento ID', 'Asistencias Confirmadas']);
                fputcsv($handle, [$id, Reserva::where('evento_id', $id)->where('estado', 'confirmada')->count()]);
            } elseif ($tipo === 'actividad') {
                fputcsv($handle, ['Usuario ID', 'Nombre', 'Email', 'Reservas Hechas']);
                $usuarios = Usuario::withCount('reservas')->get(['id', 'nombre', 'email', 'reservas_count']);
                foreach ($usuarios as $usuario) {
                    fputcsv($handle, [$usuario->id, $usuario->nombre, $usuario->email, $usuario->reservas_count]);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="reporte_' . $tipo . '.csv"');
        return $response;
    }
}
