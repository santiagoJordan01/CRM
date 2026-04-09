<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithClienteSupports;
use App\Models\Cliente;
use App\Models\ClienteNotificacion;
use App\Support\ClienteModuloContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class GestionClienteController extends Controller
{
    use InteractsWithClienteSupports;

    private function esEstadoRadicacionIniciada(?string $status, ?string $subStatus): bool
    {
        return (string) $status === 'Preradicacion Comercial'
            && (string) $subStatus === 'Envio Digital Docs';
    }

    private function resolverEstadoVisual(string $modulo, ?string $status, ?string $subStatus): array
    {
        if ($modulo === 'radicados' && $this->esEstadoRadicacionIniciada($status, $subStatus)) {
            return [
                'status' => 'Radicacion Iniciada',
                'sub_status' => 'Radicacion Iniciada',
            ];
        }

        return [
            'status' => (string) ($status ?? ''),
            'sub_status' => (string) ($subStatus ?? ''),
        ];
    }

    private function inferirModuloHistorial(?string $status, ?string $subStatus): string
    {
        $status = (string) ($status ?? '');
        $subStatus = (string) ($subStatus ?? '');

        if (
            in_array($subStatus, ['Pendiente Desembolso', 'Pte desembolso', 'exitoso', 'fallido'], true)
            || in_array($status, ['Contabilizacion Pendiente', 'Contabilizacion aceptada', 'Desembolsado', 'No Desembolsado'], true)
        ) {
            return 'desembolso';
        }

        if (
            in_array($subStatus, ['Pendiente Aprobacion', 'Aprobado', 'No acepta condiciones'], true)
            || in_array($status, ['Aprobado', 'No Aprobado', 'Cliente Desiste'], true)
            || $subStatus === 'Aprobado'
        ) {
            return 'aprobados';
        }

        if (
            in_array($status, ['Preradicacion Comercial', 'En Estudio', 'Negado', 'Radicado', 'No Radicado', 'Envio Digital Docs', 'Radicacion Iniciada'], true)
            || in_array($subStatus, ['Envio Digital Docs', 'En Analisis', 'En Comite', 'Cap Dcto', 'Sujeto a Reconsideracion', 'No Sujeto a Reconsideracion', 'Radicacion Iniciada'], true)
            || str_contains(strtolower($status), 'radicacion')
            || str_contains(strtolower($subStatus), 'radicacion')
        ) {
            return 'radicados';
        }

        return 'filtros';
    }

    private function mapRegistro(Cliente $cliente, string $modulo): array
    {
        $departamentoNombre = $cliente->departamento?->nombre
            ?? $cliente->municipio?->departamento?->nombre
            ?? '';

        $soportesAsesor = array_values(array_filter([
            $this->soporteMeta($cliente->soporte_1),
            $this->soporteMeta($cliente->soporte_2),
            $this->soporteMeta($cliente->soporte_3),
            $this->soporteMeta($cliente->soporte_4),
            $this->soporteMeta($cliente->soporte_5),
            $this->soporteMeta($cliente->soporte_6),
        ]));

        $soportesMesaControl = array_values(array_filter([
            $this->soporteMeta($cliente->mesa_soporte_1),
            $this->soporteMeta($cliente->mesa_soporte_2),
            $this->soporteMeta($cliente->mesa_soporte_3),
        ]));

        $resultadoFecha = $cliente->mesa_control_respondido_at ?? $cliente->updated_at;
        $estadoVisual = $this->resolverEstadoVisual($modulo, $cliente->status, $cliente->sub_status);

        return [
            'id' => (string) $cliente->id,
            'fecha' => $cliente->created_at?->format('d/M Y g:i a') ?? '',
            'modificacion' => $cliente->updated_at?->format('d/M Y g:i a') ?? '',
            'gestion' => '+' . ($cliente->created_at?->diffInDays(now()) ?? 0) . ' d',
            'banco' => $cliente->campania,
            'campania' => $cliente->campania,
            'producto' => $cliente->producto,
            'canal' => $cliente->canal,
            'cedula' => $cliente->cedula,
            'nombre' => $cliente->nombre_cliente,
            'perfil' => $cliente->perfil,
            'empresa' => $cliente->empresa,
            'genero' => $cliente->genero,
            'email' => $cliente->email,
            'fecha_nacimiento' => $cliente->fecha_nacimiento?->format('Y-m-d') ?? '',
            'fecha_vinculacion' => $cliente->fecha_vinculacion?->format('Y-m-d') ?? '',
            'destino' => $cliente->destino,
            'tipo_cliente' => $cliente->tipo_cliente,
            'sector' => $cliente->sector,
            'nit_empresa' => $cliente->nit_empresa,
            'tipo_contrato' => $cliente->tipo_contrato,
            'monto' => '$ ' . $cliente->monto_filtrado,
            'plazo' => (string) $cliente->plazo,
            'monto_filtrado' => '$ ' . $cliente->monto_filtrado,
            'celular' => $cliente->celular_cliente,
            'ingreso_principal' => '$ ' . $cliente->ingreso_principal,
            'otros_ingresos' => '$ ' . $cliente->otros_ingresos,
            'tasa_ea' => (string) ($cliente->tasa_ea ?? ''),
            'numero_credito' => (string) ($cliente->numero_credito ?? ''),
            'oficina_radicacion' => (string) ($cliente->oficina_radicacion ?? ''),
            'financiera_1' => (string) ($cliente->financiera_1 ?? ''),
            'financiera_2' => (string) ($cliente->financiera_2 ?? ''),
            'financiera_3' => (string) ($cliente->financiera_3 ?? ''),
            'status' => $estadoVisual['status'],
            'sub_status' => $estadoVisual['sub_status'],
             'user_id' => $cliente->user_id,
            'asesor' => strtoupper($cliente->user?->name ?? 'ASESOR FREELANCE'),
            'mesa_control' => strtoupper($cliente->mesaControlUser?->name ?? 'MESA DE CONTROL'),
            'ciudad' => $cliente->municipio?->nombre ?? '',
            'departamento' => $departamentoNombre,
            'observaciones' => $cliente->observaciones,
            'observacion_mesa_control' => $cliente->observacion_mesa_control,
            'resultado_fecha' => $resultadoFecha?->format('d/M Y | g:i a') ?? '',
            'recordatorio' => $cliente->recordatorio,
            'soportes_asesor' => $soportesAsesor,
            'soportes_mesa_control' => $soportesMesaControl,
        ];
    }

    private function validarAccesoFiltro(Cliente $cliente, Request $request): void
    {
        $user = $request->user();

        if ($user?->isAsesor() && (int) $cliente->user_id !== (int) $user->id) {
            abort(403, 'No tienes permisos para ver este filtro.');
        }
    }

    private function crearNotificacionCambioEstado(
        Cliente $cliente,
        ?string $statusAnterior,
        ?string $subStatusAnterior,
        ?int $actorUserId
    ): void {
        $statusNuevo = $cliente->status;
        $subStatusNuevo = $cliente->sub_status;

        $cambioStatus = (string) $statusAnterior !== (string) $statusNuevo;
        $cambioSubStatus = (string) $subStatusAnterior !== (string) $subStatusNuevo;

        if (! $cliente->user_id || (! $cambioStatus && ! $cambioSubStatus)) {
            return;
        }

        ClienteNotificacion::create([
            'user_id' => $cliente->user_id,
            'cliente_id' => $cliente->id,
            'actor_user_id' => $actorUserId,
            'cliente_nombre' => $cliente->nombre_cliente,
            'old_status' => $statusAnterior,
            'old_sub_status' => $subStatusAnterior,
            'new_status' => $statusNuevo,
            'new_sub_status' => $subStatusNuevo,
        ]);
    }

    private function construirQueryModulo(Request $request, string $modulo)
    {
        $query = Cliente::with(['user', 'mesaControlUser', 'municipio.departamento', 'departamento']);

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
                // Si la fecha no tiene formato valido, se ignora el filtro.
            }
        }

        if ($request->filled('fecha_desde')) {
            try {
                $fechaDesde = Carbon::parse($request->string('fecha_desde')->toString())->toDateString();
                $query->whereDate('created_at', '>=', $fechaDesde);
            } catch (\Throwable $e) {
                // Si la fecha no tiene formato valido, se ignora el filtro.
            }
        }

        if ($request->filled('fecha_hasta')) {
            try {
                $fechaHasta = Carbon::parse($request->string('fecha_hasta')->toString())->toDateString();
                $query->whereDate('created_at', '<=', $fechaHasta);
            } catch (\Throwable $e) {
                // Si la fecha no tiene formato valido, se ignora el filtro.
            }
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();

            if ($status === 'Radicacion Iniciada') {
                $query->where('status', 'Preradicacion Comercial')
                    ->where('sub_status', 'Envio Digital Docs');
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('sub_status')) {
            $subStatus = $request->string('sub_status')->toString();

            if ($subStatus === 'Radicacion Iniciada') {
                $query->where('status', 'Preradicacion Comercial')
                    ->where('sub_status', 'Envio Digital Docs');
            } else {
                $query->where('sub_status', $subStatus);
            }
        }

        if ($request->filled('q')) {
            $termino = trim($request->string('q')->toString());

            $query->where(function ($q) use ($termino) {
                $q->where('nombre_cliente', 'like', "%{$termino}%")
                    ->orWhere('cedula', 'like', "%{$termino}%")
                    ->orWhere('empresa', 'like', "%{$termino}%")
                    ->orWhere('campania', 'like', "%{$termino}%")
                    ->orWhere('producto', 'like', "%{$termino}%")
                    ->orWhere('canal', 'like', "%{$termino}%");

                if (is_numeric($termino)) {
                    $q->orWhere('id', (int) $termino);
                }
            });
        }

        ClienteModuloContext::applyFilter($query, $modulo);

        return $query;
    }

    private function mostrarModuloIndex(Request $request, string $modulo)
    {
        $moduloContext = ClienteModuloContext::get($modulo);

        $registros = $this->construirQueryModulo($request, $modulo)
            ->latest()
            ->get()
            ->map(function (Cliente $cliente) use ($modulo) {
                return $this->mapRegistro($cliente, $modulo);
            });

        $vistaPorModulo = [
            'filtros' => 'gestion_filtros',
            'radicados' => 'gestion_radicados',
            'aprobados' => 'gestion_aprobados',
            'desembolso' => 'gestion_desembolso',
        ];

        return view($vistaPorModulo[$modulo] ?? 'gestion_filtros', compact('registros', 'moduloContext'));
    }

    private function mostrarModuloDetalle(Request $request, string $modulo, string $id)
    {
        $moduloContext = ClienteModuloContext::get($modulo);

        $cliente = Cliente::with(['user', 'mesaControlUser', 'municipio.departamento', 'departamento'])->findOrFail($id);
        $this->validarAccesoFiltro($cliente, $request);

        $registro = $this->mapRegistro($cliente, $modulo);
        $soportesMesaControlSlots = $this->soporteSlots(
            $cliente->mesa_soporte_1,
            $cliente->mesa_soporte_2,
            $cliente->mesa_soporte_3
        );

        $puedeResponderMesaControl = $request->user()?->isSupervisorOrAdmin()
            && in_array((string) $cliente->sub_status, $moduloContext['subStatusResponder'], true);

        $opcionesEstado = $moduloContext['opcionesEstado'];

            $asesores = \App\Models\User::where('role', 'asesor')->get(['id', 'name']);


        return view('gestion_filtros_detalle', compact(
            'registro',
            'puedeResponderMesaControl',
            'soportesMesaControlSlots',
            'moduloContext',
            'opcionesEstado',
             'asesores' 
        ));
    }

   private function mostrarModuloProceso(Request $request, string $modulo, string $id)
{
    $moduloContext = ClienteModuloContext::get($modulo);

    $cliente = Cliente::with(['user', 'mesaControlUser', 'municipio.departamento', 'departamento'])->findOrFail($id);
    $this->validarAccesoFiltro($cliente, $request);

    $registro = $this->mapRegistro($cliente, $modulo);

    $soportesAsesorHistorial = array_values(array_filter([
        $this->soporteMeta($cliente->soporte_1),
        $this->soporteMeta($cliente->soporte_2),
        $this->soporteMeta($cliente->soporte_3),
        $this->soporteMeta($cliente->soporte_4),
        $this->soporteMeta($cliente->soporte_5),
        $this->soporteMeta($cliente->soporte_6),
    ]));

    $soportesMesaHistorial = array_values(array_filter([
        $this->soporteMeta($cliente->mesa_soporte_1),
        $this->soporteMeta($cliente->mesa_soporte_2),
        $this->soporteMeta($cliente->mesa_soporte_3),
    ]));

    $transicionesEstado = ClienteNotificacion::with('actor')
        ->where('cliente_id', $cliente->id)
        ->orderBy('created_at')
        ->get();

    $historial = [
        [
            'fecha' => $cliente->created_at?->format('d/M Y | g:i a') ?? '',
            'status' => 'Inicia Filtro',
            'sub_status' => 'Inicia Filtro',
            'comentario' => $cliente->observaciones ?: '-',
            'respuesta_archivos' => [],
            'soporte_archivos' => $soportesAsesorHistorial,
            'autor' => strtoupper($cliente->user?->name ?? 'ASESOR FREELANCE'),
            'modulo_historial' => 'filtros',
            'notificacion_id' => null,
            'editable' => false,
            'orden' => $cliente->created_at?->timestamp ?? 0,
        ],
    ];

    foreach ($transicionesEstado as $transicion) {
        $statusTransicion = (string) ($transicion->new_status ?? '');
        $subStatusTransicion = (string) ($transicion->new_sub_status ?? '');

        if ($statusTransicion === '' && $subStatusTransicion === '') {
            continue;
        }

        $fechaTransicion = $transicion->created_at;
        $esMesaControlActual = $cliente->mesa_control_respondido_at
            && $fechaTransicion
            && abs($fechaTransicion->diffInSeconds($cliente->mesa_control_respondido_at, false)) <= 1;

        $estadoVisualTransicion = $this->resolverEstadoVisual($modulo, $statusTransicion, $subStatusTransicion);
        $esRadicacionIniciada = $modulo === 'radicados'
            && $this->esEstadoRadicacionIniciada($statusTransicion, $subStatusTransicion);

        $comentarioTransicion = $esMesaControlActual
            ? ($cliente->observacion_mesa_control ?: '-')
            : ($esRadicacionIniciada ? ($cliente->observaciones ?: '-') : '-');

        $respuestaArchivosTransicion = $esMesaControlActual ? $soportesMesaHistorial : [];
        $soporteArchivosTransicion = $esRadicacionIniciada ? $soportesAsesorHistorial : [];
        $moduloHistorial = $this->inferirModuloHistorial($statusTransicion, $subStatusTransicion);

        $historial[] = [
            'fecha' => $fechaTransicion?->format('d/M Y | g:i a') ?? '',
            'status' => $estadoVisualTransicion['status'] !== '' ? $estadoVisualTransicion['status'] : '-',
            'sub_status' => $estadoVisualTransicion['sub_status'] !== '' ? $estadoVisualTransicion['sub_status'] : '-',
            'comentario' => $comentarioTransicion,
            'respuesta_archivos' => $respuestaArchivosTransicion,
            'soporte_archivos' => $soporteArchivosTransicion,
            'autor' => strtoupper($transicion->actor?->name ?? 'USUARIO CRM'),
            'modulo_historial' => $moduloHistorial,
            'notificacion_id' => (int) $transicion->id,
            'editable' => true,
            'orden' => $fechaTransicion?->timestamp ?? 0,
        ];
    }

    $ultimaEntrada = collect($historial)->sortBy('orden')->last();
    $estadoVisualActual = $this->resolverEstadoVisual($modulo, $cliente->status, $cliente->sub_status);
    $statusActualCliente = $estadoVisualActual['status'];
    $subStatusActualCliente = $estadoVisualActual['sub_status'];

    if (
        $statusActualCliente !== ''
        && (
            ! $ultimaEntrada
            || (string) ($ultimaEntrada['status'] ?? '') !== $statusActualCliente
            || (string) ($ultimaEntrada['sub_status'] ?? '') !== $subStatusActualCliente
        )
    ) {
        $autorActual = strtoupper($cliente->mesaControlUser?->name ?? $cliente->user?->name ?? 'USUARIO CRM');
        $moduloHistorialActual = $this->inferirModuloHistorial($cliente->status, $cliente->sub_status);

        $historial[] = [
            'fecha' => $cliente->updated_at?->format('d/M Y | g:i a') ?? '',
            'status' => $statusActualCliente,
            'sub_status' => $subStatusActualCliente !== '' ? $subStatusActualCliente : '-',
            'comentario' => $cliente->observacion_mesa_control ?: '-',
            'respuesta_archivos' => [],
            'soporte_archivos' => $soportesAsesorHistorial,
            'autor' => $autorActual,
            'modulo_historial' => $moduloHistorialActual,
            'notificacion_id' => null,
            'editable' => false,
            'orden' => $cliente->updated_at?->timestamp ?? 0,
        ];
    }

    if (
        $transicionesEstado->isEmpty()
        && ((string) $cliente->status !== 'Inicia Filtro' || (string) $cliente->sub_status !== 'Inicia Filtro')
    ) {
        $esRadicacionIniciadaActual = $modulo === 'radicados'
            && $this->esEstadoRadicacionIniciada($cliente->status, $cliente->sub_status);
        $moduloHistorialActual = $this->inferirModuloHistorial($cliente->status, $cliente->sub_status);

        $historial[] = [
            'fecha' => $cliente->updated_at?->format('d/M Y | g:i a') ?? '',
            'status' => $statusActualCliente,
            'sub_status' => $subStatusActualCliente,
            'comentario' => $esRadicacionIniciadaActual
                ? ($cliente->observaciones ?: '-')
                : ($cliente->observacion_mesa_control ?: '-'),
            'respuesta_archivos' => $esRadicacionIniciadaActual ? [] : $soportesMesaHistorial,
            'soporte_archivos' => $esRadicacionIniciadaActual ? $soportesAsesorHistorial : [],
            'autor' => strtoupper($esRadicacionIniciadaActual
                ? ($cliente->user?->name ?? 'ASESOR FREELANCE')
                : ($cliente->mesaControlUser?->name ?? 'USUARIO CRM')),
            'modulo_historial' => $moduloHistorialActual,
            'notificacion_id' => null,
            'editable' => false,
            'orden' => $cliente->updated_at?->timestamp ?? 0,
        ];
    }

    usort($historial, function (array $a, array $b): int {
        return ($b['orden'] ?? 0) <=> ($a['orden'] ?? 0);
    });

    $contextosModulos = ClienteModuloContext::all();
    $ordenModulos = ['desembolso', 'aprobados', 'radicados', 'filtros'];
    $historialAgrupado = collect($historial)->groupBy('modulo_historial');
    $historialSeccionado = [];

    foreach ($ordenModulos as $claveModulo) {
        $itemsModulo = $historialAgrupado->get($claveModulo, collect());

        if ($itemsModulo->isEmpty()) {
            continue;
        }

        $historialSeccionado[] = [
            'clave' => $claveModulo,
            'titulo' => $contextosModulos[$claveModulo]['titulo_gestion'] ?? ucfirst($claveModulo),
            'items' => $itemsModulo->values()->all(),
        ];
    }

    $modulosExtras = $historialAgrupado->keys()->reject(function ($claveModulo) use ($ordenModulos) {
        return in_array((string) $claveModulo, $ordenModulos, true);
    });

    foreach ($modulosExtras as $claveModulo) {
        $itemsModulo = $historialAgrupado->get($claveModulo, collect());

        if ($itemsModulo->isEmpty()) {
            continue;
        }

        $historialSeccionado[] = [
            'clave' => (string) $claveModulo,
            'titulo' => ucfirst((string) $claveModulo),
            'items' => $itemsModulo->values()->all(),
        ];
    }

    $soportesCreacionSlots = [
        [
            'titulo' => 'Soporte 1',
            'archivo' => $this->soporteMeta($cliente->soporte_1),
        ],
        [
            'titulo' => 'Soporte 2',
            'archivo' => $this->soporteMeta($cliente->soporte_2),
        ],
        [
            'titulo' => 'Soporte 3',
            'archivo' => $this->soporteMeta($cliente->soporte_3),
        ],
        [
            'titulo' => 'Soporte 4',
            'archivo' => $this->soporteMeta($cliente->soporte_4),
        ],
        [
            'titulo' => 'Soporte 5',
            'archivo' => $this->soporteMeta($cliente->soporte_5),
        ],
        [
            'titulo' => 'Soporte 6',
            'archivo' => $this->soporteMeta($cliente->soporte_6),
        ],
    ];

    $user = $request->user();

    $puedeActualizarSupervisor = $user?->isSupervisorOrAdmin() === true;
    $esAsesor = $user?->isAsesor() === true;

    $puedeActualizarAsesor = $esAsesor
        && $modulo === 'filtros'
        && (string) $cliente->status === 'Viable'
        && (string) $cliente->sub_status === 'Pendiente Radicar';

    $puedeCrearNuevoFiltroAsesor = $esAsesor
        && $modulo === 'radicados'
        && (string) $cliente->status === 'Negado'
        && in_array((string) $cliente->sub_status, ['Cap Dcto', 'Sujeto a Reconsideracion'], true);

    $opcionesEstado = $moduloContext['opcionesEstado'] ?? [];

    $filtrosContext = ClienteModuloContext::get('filtros');
    $opcionesEstadoAsesor = $filtrosContext['opcionesEstadoAsesor'] ?? [];

    $opcionesGlobales = collect($contextosModulos)->flatMap(function (array $contexto) {
        return array_merge(
            $contexto['opcionesEstado'] ?? [],
            $contexto['opcionesEstadoAsesor'] ?? []
        );
    });

    $catalogoStatusGeneral = collect(['Inicia Filtro'])
        ->merge($opcionesGlobales->pluck('status'))
        ->unique()
        ->values()
        ->all();

    $catalogoSubStatusGeneral = collect(['Inicia Filtro'])
        ->merge(collect($contextosModulos)->flatMap(function (array $contexto) {
            return $contexto['subStatusResponder'] ?? [];
        }))
        ->merge($opcionesGlobales->pluck('sub_status'))
        ->unique()
        ->values()
        ->all();

    $mostrarPanelAsesor = $esAsesor;

    return view('proceso', compact(
        'registro',
        'historial',
        'historialSeccionado',
        'soportesCreacionSlots',
        'moduloContext',
        'opcionesEstado',
        'opcionesEstadoAsesor',
        'catalogoStatusGeneral',
        'catalogoSubStatusGeneral',
        'mostrarPanelAsesor',
        'esAsesor',
        'puedeActualizarAsesor',
        'puedeActualizarSupervisor',
        'puedeCrearNuevoFiltroAsesor'
    ));
}

    public function filtrosActualizarAsesor(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $this->validarAccesoFiltro($cliente, $request);
        $statusAnterior = $cliente->status;
        $subStatusAnterior = $cliente->sub_status;

        if (! $request->user()?->isAsesor()) {
            abort(403, 'Solo un asesor puede actualizar este estado.');
        }

        if ((string) $cliente->status !== 'Viable' || (string) $cliente->sub_status !== 'Pendiente Radicar') {
            return back()->withErrors([
                'status' => 'El cliente debe estar en estado Viable / Pendiente Radicar para actualizar este paso.',
            ]);
        }

        $moduloContext = ClienteModuloContext::get('filtros');
        $opcionesEstadoAsesor = collect($moduloContext['opcionesEstadoAsesor'] ?? []);

        $estadosPermitidos = $opcionesEstadoAsesor
            ->pluck('status')
            ->unique()
            ->values()
            ->all();

        $subEstadosPermitidos = $opcionesEstadoAsesor
            ->pluck('sub_status')
            ->unique()
            ->values()
            ->all();

        $data = $request->validate([
            'status' => ['required', Rule::in($estadosPermitidos)],
            'sub_status' => ['required', Rule::in($subEstadosPermitidos)],
            'observaciones' => 'nullable|string|max:2000',
            'soporte_1' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_2' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_3' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_4' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_5' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'soporte_6' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
        ]);

        $combinacionValida = $opcionesEstadoAsesor->contains(function (array $opcion) use ($data): bool {
            return (string) $opcion['status'] === (string) $data['status']
                && (string) $opcion['sub_status'] === (string) $data['sub_status'];
        });

        if (! $combinacionValida) {
            return back()->withErrors([
                'sub_status' => 'La combinacion de status y sub status no es valida para el asesor.',
            ])->withInput();
        }

        $soporte1 = $this->storeSupportFile($request, 'soporte_1', 'soportes')
            ?? $cliente->soporte_1;
        $soporte2 = $this->storeSupportFile($request, 'soporte_2', 'soportes')
            ?? $cliente->soporte_2;
        $soporte3 = $this->storeSupportFile($request, 'soporte_3', 'soportes')
            ?? $cliente->soporte_3;
        $soporte4 = $this->storeSupportFile($request, 'soporte_4', 'soportes')
            ?? $cliente->soporte_4;
        $soporte5 = $this->storeSupportFile($request, 'soporte_5', 'soportes')
            ?? $cliente->soporte_5;
        $soporte6 = $this->storeSupportFile($request, 'soporte_6', 'soportes')
            ?? $cliente->soporte_6;

        if (! $soporte1 && ! $soporte2 && ! $soporte3 && ! $soporte4 && ! $soporte5 && ! $soporte6) {
            return back()->withErrors([
                'soporte_1' => 'Debes adjuntar al menos un soporte para enviar este cliente a Radicados.',
            ])->withInput();
        }

        $cliente->update([
            'status' => $data['status'],
            'sub_status' => $data['sub_status'],
            'observaciones' => $data['observaciones'] ?: $cliente->observaciones,
            'soporte_1' => $soporte1,
            'soporte_2' => $soporte2,
            'soporte_3' => $soporte3,
            'soporte_4' => $soporte4,
            'soporte_5' => $soporte5,
            'soporte_6' => $soporte6,
        ]);

        $this->crearNotificacionCambioEstado(
            $cliente->fresh(),
            $statusAnterior,
            $subStatusAnterior,
            $request->user()?->id
        );

        return redirect()
            ->route('radicados.show', $cliente->id)
            ->with('success', 'Estado actualizado por asesor. El cliente ahora esta en Gestion de Radicados.');
    }

    private function responderModulo(Request $request, string $modulo, string $id)
    {
        $moduloContext = ClienteModuloContext::get($modulo);

        $cliente = Cliente::findOrFail($id);
        $statusAnterior = $cliente->status;
        $subStatusAnterior = $cliente->sub_status;

        $opcionesEstado = collect($moduloContext['opcionesEstado']);
        $editandoHistorial = $request->filled('edit_notificacion_id');

        if ($editandoHistorial) {
            $contextos = ClienteModuloContext::all();

            $opcionesGlobales = collect($contextos)->flatMap(function (array $contexto) {
                return array_merge(
                    $contexto['opcionesEstado'] ?? [],
                    $contexto['opcionesEstadoAsesor'] ?? []
                );
            });

            $estadosPermitidos = collect(['Inicia Filtro'])
                ->merge($opcionesGlobales->pluck('status'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $subEstadosPermitidos = collect(['Inicia Filtro'])
                ->merge($opcionesGlobales->pluck('sub_status'))
                ->filter()
                ->unique()
                ->values()
                ->all();
        } else {
            $estadosPermitidos = $opcionesEstado
                ->pluck('status')
                ->unique()
                ->values()
                ->all();

            $subEstadosPermitidos = $opcionesEstado
                ->pluck('sub_status')
                ->unique()
                ->values()
                ->all();
        }

        $data = $request->validate([
            'status' => ['required', Rule::in($estadosPermitidos)],
            'sub_status' => ['required', Rule::in($subEstadosPermitidos)],
            'edit_notificacion_id' => 'nullable|integer|exists:cliente_notificaciones,id',
            'observacion_mesa_control' => 'required|string|max:2000',
            'tasa_ea' => 'nullable|string|max:60',
            'numero_credito' => 'nullable|string|max:120',
            'oficina_radicacion' => 'nullable|string|max:255',
            'financiera_1' => 'nullable|string|max:255',
            'financiera_2' => 'nullable|string|max:255',
            'financiera_3' => 'nullable|string|max:255',
            'mesa_soporte_1' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'mesa_soporte_2' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'mesa_soporte_3' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
        ]);

        if (! $editandoHistorial) {
            $combinacionValida = $opcionesEstado->contains(function (array $opcion) use ($data): bool {
                return (string) $opcion['status'] === (string) $data['status']
                    && (string) $opcion['sub_status'] === (string) $data['sub_status'];
            });

            if (! $combinacionValida) {
                return back()->withErrors([
                    'sub_status' => 'La combinacion de status y sub status no es valida para este modulo.',
                ])->withInput();
            }
        }

        if (
            ! $editandoHistorial
            &&
            $modulo === 'radicados'
            && (string) $data['status'] === 'En Estudio'
            && (string) $data['sub_status'] === 'En Comite'
            && ! ((string) $cliente->status === 'En Estudio' && (string) $cliente->sub_status === 'En Analisis')
        ) {
            return back()->withErrors([
                'sub_status' => 'Para pasar a En Comite, primero debes registrar En Estudio / En Analisis.',
            ])->withInput();
        }

        $mesaSoporte1 = $this->storeSupportFile($request, 'mesa_soporte_1', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_1;

        $mesaSoporte2 = $this->storeSupportFile($request, 'mesa_soporte_2', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_2;

        $mesaSoporte3 = $this->storeSupportFile($request, 'mesa_soporte_3', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_3;

        $resolverTextoOpcional = static function (?string $nuevoValor, ?string $valorActual): ?string {
            $nuevoValor = trim((string) $nuevoValor);

            return $nuevoValor !== '' ? $nuevoValor : $valorActual;
        };

        if ($editandoHistorial) {
            $notificacion = ClienteNotificacion::where('id', $data['edit_notificacion_id'])
                ->where('cliente_id', $cliente->id)
                ->first();

            if (! $notificacion) {
                return back()->withErrors([
                    'edit_notificacion_id' => 'No se encontro la fila de historial seleccionada.',
                ])->withInput();
            }

            $notificacion->update([
                'new_status' => $data['status'],
                'new_sub_status' => $data['sub_status'],
                'actor_user_id' => $request->user()?->id,
            ]);

            $ultimaNotificacion = ClienteNotificacion::where('cliente_id', $cliente->id)
                ->latest('created_at')
                ->first();

            if ($ultimaNotificacion && (int) $ultimaNotificacion->id === (int) $notificacion->id) {
                $cliente->update([
                    'status' => $data['status'],
                    'sub_status' => $data['sub_status'],
                    'observacion_mesa_control' => $data['observacion_mesa_control'],
                    'tasa_ea' => $resolverTextoOpcional($data['tasa_ea'] ?? null, $cliente->tasa_ea),
                    'numero_credito' => $resolverTextoOpcional($data['numero_credito'] ?? null, $cliente->numero_credito),
                    'oficina_radicacion' => $resolverTextoOpcional($data['oficina_radicacion'] ?? null, $cliente->oficina_radicacion),
                    'financiera_1' => $resolverTextoOpcional($data['financiera_1'] ?? null, $cliente->financiera_1),
                    'financiera_2' => $resolverTextoOpcional($data['financiera_2'] ?? null, $cliente->financiera_2),
                    'financiera_3' => $resolverTextoOpcional($data['financiera_3'] ?? null, $cliente->financiera_3),
                    'mesa_control_user_id' => $request->user()?->id,
                    'mesa_control_respondido_at' => now(),
                    'mesa_soporte_1' => $mesaSoporte1,
                    'mesa_soporte_2' => $mesaSoporte2,
                    'mesa_soporte_3' => $mesaSoporte3,
                ]);
            }

            return redirect()
                ->route($moduloContext['procesoRoute'] ?? 'filtros.proceso', $cliente->id)
                ->with('success', 'Fila del historial actualizada correctamente.');
        }

        $cliente->update([
            'status' => $data['status'],
            'sub_status' => $data['sub_status'],
            'observacion_mesa_control' => $data['observacion_mesa_control'],
            'tasa_ea' => $resolverTextoOpcional($data['tasa_ea'] ?? null, $cliente->tasa_ea),
            'numero_credito' => $resolverTextoOpcional($data['numero_credito'] ?? null, $cliente->numero_credito),
            'oficina_radicacion' => $resolverTextoOpcional($data['oficina_radicacion'] ?? null, $cliente->oficina_radicacion),
            'financiera_1' => $resolverTextoOpcional($data['financiera_1'] ?? null, $cliente->financiera_1),
            'financiera_2' => $resolverTextoOpcional($data['financiera_2'] ?? null, $cliente->financiera_2),
            'financiera_3' => $resolverTextoOpcional($data['financiera_3'] ?? null, $cliente->financiera_3),
            'mesa_control_user_id' => $request->user()?->id,
            'mesa_control_respondido_at' => now(),
            'mesa_soporte_1' => $mesaSoporte1,
            'mesa_soporte_2' => $mesaSoporte2,
            'mesa_soporte_3' => $mesaSoporte3,
        ]);

        $this->crearNotificacionCambioEstado(
            $cliente->fresh(),
            $statusAnterior,
            $subStatusAnterior,
            $request->user()?->id
        );

        return redirect()
            ->route($moduloContext['showRoute'], $cliente->id)
            ->with('success', 'Respuesta guardada correctamente.');
    }

    public function filtrosIndex(Request $request)
    {
        return $this->mostrarModuloIndex($request, 'filtros');
    }

    public function radicadosIndex(Request $request)
    {
        return $this->mostrarModuloIndex($request, 'radicados');
    }

    public function aprobadosIndex(Request $request)
    {
        return $this->mostrarModuloIndex($request, 'aprobados');
    }

    public function desembolsoIndex(Request $request)
    {
        return $this->mostrarModuloIndex($request, 'desembolso');
    }

    public function filtrosShow(Request $request, string $id)
    {
        return $this->mostrarModuloDetalle($request, 'filtros', $id);
    }

    public function radicadosShow(Request $request, string $id)
    {
        return $this->mostrarModuloDetalle($request, 'radicados', $id);
    }

    public function aprobadosShow(Request $request, string $id)
    {
        return $this->mostrarModuloDetalle($request, 'aprobados', $id);
    }

    public function desembolsoShow(Request $request, string $id)
    {
        return $this->mostrarModuloDetalle($request, 'desembolso', $id);
    }

    public function procesoIndex(Request $request, string $id)
    {
        return $this->mostrarModuloProceso($request, 'filtros', $id);
    }

    public function filtrosProceso(Request $request, string $id)
    {
        return $this->mostrarModuloProceso($request, 'filtros', $id);
    }

    public function radicadosProceso(Request $request, string $id)
    {
        return $this->mostrarModuloProceso($request, 'radicados', $id);
    }

    public function aprobadosProceso(Request $request, string $id)
    {
        return $this->mostrarModuloProceso($request, 'aprobados', $id);
    }

    public function desembolsoProceso(Request $request, string $id)
    {
        return $this->mostrarModuloProceso($request, 'desembolso', $id);
    }

    public function filtrosResponderMesaControl(Request $request, string $id)
    {
        return $this->responderModulo($request, 'filtros', $id);
    }

    public function radicadosResponderMesaControl(Request $request, string $id)
    {
        return $this->responderModulo($request, 'radicados', $id);
    }

    public function aprobadosResponderMesaControl(Request $request, string $id)
    {
        return $this->responderModulo($request, 'aprobados', $id);
    }

    public function desembolsoResponderMesaControl(Request $request, string $id)
    {
        return $this->responderModulo($request, 'desembolso', $id);
    }


    public function asignarAsesor(Request $request, string $id)
{
    // Solo supervisores pueden asignar
    if (! $request->user()?->isSupervisorOrAdmin()) {
        abort(403, 'Solo supervisores pueden reasignar filtros.');
    }

    $request->validate([
        'asesor_id' => 'required|exists:users,id',
    ]);

    $cliente = Cliente::findOrFail($id);
    $cliente->user_id = $request->asesor_id;
    $cliente->save();

    // Opcional: registrar notificación o log
    return back()->with('success', 'Filtro asignado exitosamente al asesor.');
}

public function desasignarAsesor(Request $request, string $id)
{
    // Solo supervisores pueden desasignar
    if (! $request->user()?->isSupervisorOrAdmin()) {
        abort(403, 'Solo supervisores pueden quitar la asignación de un filtro.');
    }

    $cliente = Cliente::findOrFail($id);
    $cliente->user_id = null;
    $cliente->save();

    return back()->with('success', 'El filtro ya no está asignado a ningún asesor. El asesor ya no lo verá en su listado.');
}
}
