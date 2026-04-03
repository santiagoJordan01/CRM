<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class clienteController extends Controller
{
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
        // Validar campos básicos (ajusta según necesidades reales)
        $data = $request->validate([
            'campania' => 'nullable|string|max:255',
            'producto' => 'nullable|string|max:255',
            'fecha_vinculacion' => 'nullable|date',
            'departamento_id' => 'nullable|integer',
            'ciudad' => 'nullable|string|max:255',
        ]);

        // Por ahora solo redirigimos de vuelta con mensaje de éxito simulado.
        // Aquí puedes crear el modelo o guardar en DB según la lógica del proyecto.
        return redirect()->route('registros')->with('success', 'Registro guardado correctamente (simulado).');
    }
}
