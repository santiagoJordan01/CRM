<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteNotificacion;
use App\Support\ClienteModuloContext;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function abrir(Request $request, string $id)
    {
        $notificacion = ClienteNotificacion::query()
            ->where('id', $id)
            ->where('user_id', $request->user()?->id)
            ->firstOrFail();

        if (! $notificacion->read_at) {
            $notificacion->forceFill(['read_at' => now()])->save();
        }

        $cliente = Cliente::find($notificacion->cliente_id);
        $moduloDestino = $cliente ? ClienteModuloContext::inferFromCliente($cliente) : 'filtros';
        $moduloContext = ClienteModuloContext::get($moduloDestino);

        return redirect()->route($moduloContext['showRoute'], $notificacion->cliente_id);
    }
}
