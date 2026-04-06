<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithClienteSupports;
use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class RegistroClienteController extends Controller
{
    use InteractsWithClienteSupports;

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('registros', compact('departamentos'));
    }

    public function getMunicipios(string $departamentoId)
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

        $soporte1 = $this->storeSupportFile($request, 'soporte_1', 'soportes');
        $soporte2 = $this->storeSupportFile($request, 'soporte_2', 'soportes');
        $soporte3 = $this->storeSupportFile($request, 'soporte_3', 'soportes');

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
            'status' => 'Inicia Filtro',
            'sub_status' => 'Inicia Filtro',
            'recordatorio' => 'Recordatorio / Tarea',
            'soporte_1' => $soporte1,
            'soporte_2' => $soporte2,
            'soporte_3' => $soporte3,
        ]);

        return redirect()->route('filtros.index')->with('success', 'Nuevo filtro creado correctamente.');
    }
}
