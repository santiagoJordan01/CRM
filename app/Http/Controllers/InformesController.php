<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Support\ClienteModuloContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        $filename = 'informe_filtros_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($registros): void {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Informe Filtros');

            $headers = [
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
            ];

            $lastCol = Coordinate::stringFromColumnIndex(count($headers));

            foreach ($headers as $index => $header) {
                $cell = Coordinate::stringFromColumnIndex($index + 1) . '1';
                $sheet->setCellValue($cell, $header);
            }

            $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F67B7'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row = 2;
            foreach ($registros as $cliente) {
                $sheet->setCellValue("A{$row}", (int) $cliente->id);
                $sheet->setCellValue("B{$row}", $cliente->created_at?->format('d/m/Y H:i') ?? '');
                $sheet->setCellValue("C{$row}", (string) $cliente->campania);
                $sheet->setCellValue("D{$row}", (string) $cliente->producto);
                $sheet->setCellValue("E{$row}", (string) $cliente->canal);
                $sheet->setCellValue("F{$row}", (string) $cliente->nombre_cliente);
                $sheet->setCellValueExplicit("G{$row}", (string) $cliente->cedula, DataType::TYPE_STRING);
                $sheet->setCellValue("H{$row}", (string) $cliente->email);
                $sheet->setCellValue("I{$row}", (string) $cliente->status);
                $sheet->setCellValue("J{$row}", (string) $cliente->sub_status);

                $monto = (float) preg_replace('/[^0-9.-]/', '', (string) $cliente->monto_filtrado);
                $sheet->setCellValue("K{$row}", $monto);
                $sheet->setCellValue("L{$row}", (string) ($cliente->user?->name ?? 'ASESOR FREELANCE'));

                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                }

                $row++;
            }

            $lastRow = max(2, $row - 1);

            $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDE3EA'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle("K2:K{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");
            $sheet->freezePane('A2');

            for ($i = 1; $i <= count($headers); $i++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
