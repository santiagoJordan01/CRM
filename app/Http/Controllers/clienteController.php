<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class clienteController extends Controller
{
    private function mapRegistro(Cliente $cliente): array
    {
        $departamentoNombre = $cliente->departamento?->nombre
            ?? $cliente->municipio?->departamento?->nombre
            ?? '';

        return [
            'id' => (string) $cliente->id,
            'fecha' => $cliente->created_at?->format('d/M Y g:i a') ?? '',
            'modificacion' => $cliente->updated_at?->format('d/M Y g:i a') ?? '',
            'gestion' => '+' . ($cliente->created_at?->diffInDays(now()) ?? 0) . ' d',
            'campania' => $cliente->campania,
            'producto' => $cliente->producto,
            'canal' => $cliente->canal,
            'cedula' => $cliente->cedula,
            'nombre' => $cliente->nombre_cliente,
            'perfil' => $cliente->perfil,
            'empresa' => $cliente->empresa,
            'monto' => '$ ' . $cliente->monto_filtrado,
            'plazo' => (string) $cliente->plazo,
            'status' => $cliente->status,
            'sub_status' => $cliente->sub_status,
            'asesor' => strtoupper($cliente->user?->name ?? 'ASESOR FREELANCE'),
            'supervisor' => 'ANDREA GONZALEZ MONJE',
            'ciudad' => $cliente->municipio?->nombre ?? '',
            'departamento' => $departamentoNombre,
            'observaciones' => $cliente->observaciones,
            'resultado_fecha' => $cliente->updated_at?->format('d/M Y | g:i a') ?? '',
            'recordatorio' => $cliente->recordatorio,
        ];
    }

    public function filtrosIndex()
    {
        $registros = Cliente::with(['user', 'municipio.departamento', 'departamento'])
            ->latest()
            ->get()
            ->map(function (Cliente $cliente) {
                return $this->mapRegistro($cliente);
            });

        return view('gestion_filtros', compact('registros'));
    }

    public function filtrosShow(string $id)
    {
        $cliente = Cliente::with(['user', 'municipio.departamento', 'departamento'])->findOrFail($id);
        $registro = $this->mapRegistro($cliente);

        return view('gestion_filtros_detalle', compact('registro'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('registros', compact('departamentos'));
    }

    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($municipios);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'campania' => 'required|string|max:255',
            'producto' => 'required|string|max:255',
            'cedula' => 'required|string|max:30',
            'genero' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'departamento_id' => 'required|integer|exists:departamentos,id',
            'perfil' => 'required|string|max:255',
            'empresa' => 'required|string|max:255',
            'fecha_vinculacion' => 'required|date',
            'canal' => 'required|string|max:100',
            'plazo' => 'required|integer|min:0',
            'ingreso_principal' => 'required|string|max:30',
            'tipo_cliente' => 'required|string|max:100',
            'destino' => 'required|string|max:255',
            'nombre_cliente' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'ciudad' => 'required|integer|exists:municipios,id',
            'sector' => 'required|string|max:100',
            'nit_empresa' => 'required|string|max:30',
            'tipo_contrato' => 'required|string|max:255',
            'monto_filtrado' => 'required|string|max:30',
            'celular_cliente' => 'required|string|max:20',
            'otros_ingresos' => 'required|string|max:30',
            'observaciones' => 'required|string|max:2000',
            'soporte_1' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_2' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_3' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
        ]);

        $soporte1 = $request->hasFile('soporte_1') ? $request->file('soporte_1')->store('soportes', 'public') : '';
        $soporte2 = $request->hasFile('soporte_2') ? $request->file('soporte_2')->store('soportes', 'public') : '';
        $soporte3 = $request->hasFile('soporte_3') ? $request->file('soporte_3')->store('soportes', 'public') : '';

        Cliente::create([
            'user_id' => $request->user()?->id,
            'campania' => $data['campania'],
            'producto' => $data['producto'],
            'cedula' => $data['cedula'],
            'genero' => $data['genero'],
            'email' => $data['email'],
            'departamento_id' => $data['departamento_id'],
            'municipio_id' => $data['ciudad'],
            'perfil' => $data['perfil'],
            'empresa' => $data['empresa'],
            'fecha_vinculacion' => $data['fecha_vinculacion'],
            'canal' => $data['canal'],
            'plazo' => $data['plazo'],
            'ingreso_principal' => $data['ingreso_principal'],
            'tipo_cliente' => $data['tipo_cliente'],
            'destino' => $data['destino'],
            'nombre_cliente' => $data['nombre_cliente'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'sector' => $data['sector'],
            'nit_empresa' => $data['nit_empresa'],
            'tipo_contrato' => $data['tipo_contrato'],
            'monto_filtrado' => $data['monto_filtrado'],
            'celular_cliente' => $data['celular_cliente'],
            'otros_ingresos' => $data['otros_ingresos'],
            'observaciones' => $data['observaciones'],
            'status' => 'Viable',
            'sub_status' => 'Pendiente Radicar',
            'recordatorio' => 'Recordatorio / Tarea',
            'soporte_1' => $soporte1,
            'soporte_2' => $soporte2,
            'soporte_3' => $soporte3,
        ]);

        return redirect()->route('filtros.index')->with('success', 'Nuevo filtro creado correctamente.');
    }
}
