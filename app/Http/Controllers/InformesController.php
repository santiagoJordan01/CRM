<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Support\ClienteModuloContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InformesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:supervisor,admin');
    }

    public function exportarExcel(Request $request): StreamedResponse
    {
        $registros = $this->construirQueryInforme($request)
            ->with(['user'])
            ->latest()
            ->get();

        $filename = 'informe_filtros_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($registros): void {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility on Windows
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'ID',
                'Fecha',
                'Banco',
                'Producto',
                'Canal',
                'Cliente',
                'Cedula',
                'Email',
                'Estado',
                'Sub estado',
                'Monto filtrado',
                'Asesor',
            ], ';');

            foreach ($registros as $cliente) {
                fputcsv($output, [
                    (string) $cliente->id,
                    $cliente->created_at?->format('d/m/Y H:i') ?? '',
                    (string) $cliente->campania,
                    (string) $cliente->producto,
                    (string) $cliente->canal,
                    (string) $cliente->nombre_cliente,
                    (string) $cliente->cedula,
                    (string) $cliente->email,
                    (string) $cliente->status,
                    (string) $cliente->sub_status,
                    (string) $cliente->monto_filtrado,
                    (string) ($cliente->user?->name ?? 'ASESOR FREELANCE'),
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function vistaPdf(Request $request)
    {
        $registros = $this->construirQueryInforme($request)
            ->with(['user'])
            ->latest()
            ->get();

        $filtros = [
            'campania' => trim((string) $request->input('campania', '')),
            'producto' => trim((string) $request->input('producto', '')),
            'canal' => trim((string) $request->input('canal', '')),
            'fecha' => trim((string) $request->input('fecha', '')),
            'fecha_desde' => trim((string) $request->input('fecha_desde', '')),
            'fecha_hasta' => trim((string) $request->input('fecha_hasta', '')),
        ];

        return view('informes.filtros_pdf', [
            'registros' => $registros,
            'filtros' => $filtros,
            'generadoEn' => now(),
            'usuario' => $request->user(),
        ]);
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
                // Ignore invalid date value
            }
        } else {
            if ($request->filled('fecha_desde')) {
                try {
                    $fechaDesde = Carbon::parse($request->string('fecha_desde')->toString())->toDateString();
                    $query->whereDate('created_at', '>=', $fechaDesde);
                } catch (\Throwable $e) {
                    // Ignore invalid date value
                }
            }

            if ($request->filled('fecha_hasta')) {
                try {
                    $fechaHasta = Carbon::parse($request->string('fecha_hasta')->toString())->toDateString();
                    $query->whereDate('created_at', '<=', $fechaHasta);
                } catch (\Throwable $e) {
                    // Ignore invalid date value
                }
            }
        }

        ClienteModuloContext::applyFilter($query, 'filtros');

        return $query;
    }
}
