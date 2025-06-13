<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

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
                fputcsv($handle, ['Evento ID', 'Nombre Evento', 'Total Reservas', 'Reservas Confirmadas', 'Reservas Pendientes']);
                $evento = Evento::withCount([
                    'reservas',
                    'reservas as reservas_confirmadas_count' => function ($query) {
                        $query->where('estado', 'confirmada');
                    },
                    'reservas as reservas_pendientes_count' => function ($query) {
                        $query->where('estado', 'pendiente');
                    }
                ])->find($id);
                
                fputcsv($handle, [
                    $evento->id,
                    $evento->titulo,
                    $evento->reservas_count,
                    $evento->reservas_confirmadas_count,
                    $evento->reservas_pendientes_count
                ]);
            } elseif ($tipo === 'asistencia') {
                fputcsv($handle, ['Evento ID', 'Nombre Evento', 'Total Asistentes', 'Asistencia por Estado']);
                
                // Primero obtenemos las estadísticas de asistencia
                $asistencia = Reserva::select('estado', DB::raw('COUNT(*) as count'))
                    ->where('evento_id', $id)
                    ->groupBy('estado')
                    ->get()
                    ->pluck('count', 'estado')
                    ->toArray();
                
                // Luego obtenemos la información del evento
                $evento = Evento::find($id);
                
                fputcsv($handle, [
                    $evento->id,
                    $evento->titulo,
                    array_sum($asistencia),
                    json_encode($asistencia)
                ]);
            } elseif ($tipo === 'actividad') {
                fputcsv($handle, ['Usuario ID', 'Nombre', 'Email', 'Total Reservas', 'Reservas Confirmadas', 'Reservas Pendientes', 'Última Reserva']);
                $usuarios = Usuario::with([
                    'reservas' => function ($query) {
                        $query->orderBy('created_at', 'desc')->limit(1);
                    },
                    'reservas.evento' => function ($query) {
                        $query->select('id', 'titulo');
                    }
                ])->withCount([
                    'reservas',
                    'reservas as reservas_confirmadas_count' => function ($query) {
                        $query->where('estado', 'confirmada');
                    },
                    'reservas as reservas_pendientes_count' => function ($query) {
                        $query->where('estado', 'pendiente');
                    }
                ])->get(['id', 'nombre', 'email']);
                
                foreach ($usuarios as $usuario) {
                    fputcsv($handle, [
                        $usuario->id,
                        $usuario->nombre,
                        $usuario->email,
                        $usuario->reservas_count,
                        $usuario->reservas_confirmadas_count,
                        $usuario->reservas_pendientes_count,
                        $usuario->reservas->first() ? $usuario->reservas->first()->created_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="reporte_' . $tipo . '.csv"');
        return $response;
    }

    public function reservasPorFechaUnix($fecha_unix)
    {
        $fecha = Carbon::createFromTimestamp($fecha_unix)->toDateString();

        // Obtener reservas agrupadas por evento en esa fecha
        $reservas = Reserva::select('evento_id', DB::raw('count(*) as total'))
            ->whereDate('created_at', $fecha)
            ->groupBy('evento_id')
            ->with('evento:id,titulo')
            ->get()
            ->map(function ($reserva) {
                return [
                    'evento_id' => $reserva->evento_id,
                    'evento' => $reserva->evento ? $reserva->evento->titulo : 'Sin evento',
                    'total' => $reserva->total
                ];
            });

        return response()->json($reservas);
    }
}
