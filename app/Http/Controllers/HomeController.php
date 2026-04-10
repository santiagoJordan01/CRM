<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteNotificacion;
use App\Support\ClienteModuloContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Cliente::query();
        $user = $request->user();

        if ($user?->isAsesor()) {
            $baseQuery->where('user_id', $user->id);
        }

        $bancos = (clone $baseQuery)
            ->whereNotNull('campania')
            ->select('campania')
            ->distinct()
            ->orderBy('campania')
            ->pluck('campania');

        $productos = (clone $baseQuery)
            ->whereNotNull('producto')
            ->select('producto')
            ->distinct()
            ->orderBy('producto')
            ->pluck('producto');

        $canales = (clone $baseQuery)
            ->whereNotNull('canal')
            ->select('canal')
            ->distinct()
            ->orderBy('canal')
            ->pluck('canal');

        $recordatorios = (clone $baseQuery)
            ->latest()
            ->limit(5)
            ->get(['id', 'nombre_cliente', 'recordatorio', 'updated_at']);

        $notificacionesNoLeidas = ClienteNotificacion::query()
            ->where('user_id', $user?->id)
            ->whereNull('read_at')
            ->count();

        $notificaciones = ClienteNotificacion::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->limit(20)
            ->get();

        $calendarEvents = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        $informeFiltros = [
            'campania' => trim((string) $request->input('campania', '')),
            'producto' => trim((string) $request->input('producto', '')),
            'canal' => trim((string) $request->input('canal', '')),
            'fecha' => trim((string) $request->input('fecha', '')),
            'fecha_desde' => trim((string) $request->input('fecha_desde', '')),
            'fecha_hasta' => trim((string) $request->input('fecha_hasta', '')),
        ];

        // Solo permitir mostrar el informe si el usuario es supervisor o admin
        $mostrarInforme = $request->boolean('buscar_informe') && $request->user()?->isSupervisorOrAdmin();
        $informeRegistros = collect();

        if ($mostrarInforme) {
            $informeRegistros = $this->construirQueryInforme($request)
                ->with(['user'])
                ->latest()
                ->limit(30)
                ->get();
        }

        return view('home', compact(
            'bancos',
            'productos',
            'canales',
            'recordatorios',
            'notificaciones',
            'notificacionesNoLeidas',
            'calendarEvents',
            'informeFiltros',
            'informeRegistros',
            'mostrarInforme'
        ));
    }

    private function construirQueryInforme(Request $request): Builder
    {
        $query = Cliente::query();

        if ($request->user()?->isAsesor()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('campania')) {
            $query->where('campania', $request->string('campania')->toString());
        }

        if ($request->filled('producto')) {
            $query->where('producto', $request->string('producto')->toString());
        }

        if ($request->filled('canal')) {
            $query->where('canal', $request->string('canal')->toString());
        }

        if ($request->filled('fecha')) {
            try {
                $fecha = Carbon::parse($request->string('fecha')->toString())->toDateString();
                $query->whereDate('created_at', $fecha);
            } catch (\Throwable $e) {
                // Ignore invalid date value.
            }
        } else {
            if ($request->filled('fecha_desde')) {
                try {
                    $fechaDesde = Carbon::parse($request->string('fecha_desde')->toString())->toDateString();
                    $query->whereDate('created_at', '>=', $fechaDesde);
                } catch (\Throwable $e) {
                    // Ignore invalid date value.
                }
            }

            if ($request->filled('fecha_hasta')) {
                try {
                    $fechaHasta = Carbon::parse($request->string('fecha_hasta')->toString())->toDateString();
                    $query->whereDate('created_at', '<=', $fechaHasta);
                } catch (\Throwable $e) {
                    // Ignore invalid date value.
                }
            }
        }

        ClienteModuloContext::applyFilter($query, 'filtros');

        return $query;
    }
}
