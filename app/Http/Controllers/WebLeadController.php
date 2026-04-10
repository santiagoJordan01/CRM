<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\WebLead;
use Illuminate\Http\Request;

class WebLeadController extends Controller
{
    public function landing()
    {
        $departamentos = Departamento::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $bancosDb = Cliente::query()
            ->whereNotNull('campania')
            ->select('campania')
            ->distinct()
            ->orderBy('campania')
            ->pluck('campania');

        $productosDb = Cliente::query()
            ->whereNotNull('producto')
            ->select('producto')
            ->distinct()
            ->orderBy('producto')
            ->pluck('producto');

        $canalesDb = Cliente::query()
            ->whereNotNull('canal')
            ->select('canal')
            ->distinct()
            ->orderBy('canal')
            ->pluck('canal');

        $catalogoBancos = collect([
            'Banco AV Villas',
            'Banco BBVA',
            'Banco Caja social',
            'Banco de Occidente',
        ]);

        $catalogoProductos = collect([
            'Libranza',
            'Consumo',
            'Hipotecario',
            'Tarjeta de credito',
            'Vehiculos',
        ]);

        $catalogoCanales = collect([
            'Digital',
            'Tradicional',
            'Llamada',
            'WhatsApp',
            'Correo',
        ]);

        $bancos = $catalogoBancos->merge($bancosDb)->filter()->unique()->values();
        $productos = $catalogoProductos->merge($productosDb)->filter()->unique()->values();
        $canales = $catalogoCanales->merge($canalesDb)->filter()->unique()->values();

        return view('landing', compact('departamentos', 'bancos', 'productos', 'canales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:30',
            'email' => 'required|email|max:255',
            'celular_cliente' => 'required|string|max:20',
            'campania' => 'required|string|max:255',
            'producto' => 'required|string|max:255',
            'canal' => 'nullable|string|max:100',
            'genero' => 'nullable|string|max:50',
            'departamento_id' => 'nullable|integer|exists:departamentos,id',
            'municipio_id' => 'nullable|integer|exists:municipios,id',
            'fecha_nacimiento' => 'nullable|date',
            'monto_referido' => 'nullable|string|max:30',
            'ingreso_referido' => 'nullable|string|max:30',
            'observaciones' => 'nullable|string|max:2000',
        ]);

        WebLead::create($data + [
            'status' => 'pendiente',
        ]);

        return redirect()->route('landing')->with('success', 'Gracias. Tus datos fueron enviados y pronto un asesor de mesa de control te contactara.');
    }

    public function index()
    {
        $leadsPendientes = WebLead::query()
            ->with(['departamento', 'municipio'])
            ->where('status', 'pendiente')
            ->latest()
            ->paginate(20, ['*'], 'pendientes_page');

        $leadsConvertidos = WebLead::query()
            ->with(['convertedCliente', 'convertedBy'])
            ->where('status', 'convertido')
            ->latest('converted_at')
            ->limit(20)
            ->get();

        return view('mesa_control_leads', compact('leadsPendientes', 'leadsConvertidos'));
    }

    public function convertir(Request $request, WebLead $lead)
    {
        if ($lead->status === 'convertido' && $lead->converted_cliente_id) {
            return redirect()
                ->route('filtros.show', $lead->converted_cliente_id)
                ->with('success', 'Este lead ya fue convertido anteriormente.');
        }

        $municipio = null;
        if ($lead->municipio_id) {
            $municipio = Municipio::query()->find($lead->municipio_id);
        }

        if (! $municipio) {
            $municipio = Municipio::query()->orderBy('id')->first();
        }

        if (! $municipio) {
            return back()->withErrors(['lead' => 'No hay municipios configurados para convertir este lead.']);
        }

        $departamentoId = $lead->departamento_id ?: $municipio->departamento_id;

        $observaciones = trim("Lead captado desde pagina web.\n" . ($lead->observaciones ?: 'Sin observaciones del cliente.'));

        $cliente = Cliente::create([
            'user_id' => null,
            'mesa_control_user_id' => $request->user()?->id,
            'campania' => $lead->campania,
            'producto' => $lead->producto,
            'cedula' => $lead->cedula ?: 'PENDIENTE',
            'genero' => $lead->genero ?: 'No definido',
            'email' => $lead->email,
            'departamento_id' => $departamentoId,
            'municipio_id' => $municipio->id,
            'perfil' => 'Lead Web',
            'empresa' => 'Por definir',
            'fecha_vinculacion' => now()->toDateString(),
            'canal' => $lead->canal ?: 'Digital',
            'plazo' => 0,
            'ingreso_principal' => $lead->ingreso_referido ?: '0',
            'tipo_cliente' => 'Persona Natural',
            'destino' => 'Por definir',
            'nombre_cliente' => $lead->nombre_cliente,
            'fecha_nacimiento' => $lead->fecha_nacimiento ? $lead->fecha_nacimiento->toDateString() : now()->subYears(30)->toDateString(),
            'sector' => 'Por definir',
            'nit_empresa' => 'N/A',
            'tipo_contrato' => 'Por definir',
            'monto_filtrado' => $lead->monto_referido ?: '0',
            'celular_cliente' => $lead->celular_cliente,
            'otros_ingresos' => '0',
            'observaciones' => $observaciones,
            'status' => 'Inicia Filtro',
            'sub_status' => 'Inicia Filtro',
            'recordatorio' => 'Lead web pendiente de gestion',
        ]);

        $lead->update([
            'status' => 'convertido',
            'converted_cliente_id' => $cliente->id,
            'converted_by_user_id' => $request->user()?->id,
            'converted_at' => now(),
        ]);

        return redirect()
            ->route('filtros.show', $cliente->id)
            ->with('success', 'Lead convertido correctamente y enviado a Gestion Filtros con estado inicial.');
    }
}
