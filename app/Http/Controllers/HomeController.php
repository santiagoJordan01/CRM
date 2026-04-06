<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteNotificacion;
use Illuminate\Http\Request;

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

        return view('home', compact(
            'bancos',
            'productos',
            'canales',
            'recordatorios',
            'notificaciones',
            'notificacionesNoLeidas',
            'calendarEvents'
        ));
    }
}
