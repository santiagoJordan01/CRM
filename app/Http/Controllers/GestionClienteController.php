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

    private function mapRegistro(Cliente $cliente): array
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

        ClienteModuloContext::applyFilter($query, $modulo);

        return $query;
    }

    private function mostrarModuloIndex(Request $request, string $modulo)
    {
        $moduloContext = ClienteModuloContext::get($modulo);

        $registros = $this->construirQueryModulo($request, $modulo)
            ->latest()
            ->get()
            ->map(function (Cliente $cliente) {
                return $this->mapRegistro($cliente);
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

        $registro = $this->mapRegistro($cliente);
        $soportesMesaControlSlots = $this->soporteSlots(
            $cliente->mesa_soporte_1,
            $cliente->mesa_soporte_2,
            $cliente->mesa_soporte_3
        );

        $puedeResponderMesaControl = $request->user()?->isSupervisor()
            && in_array((string) $cliente->sub_status, $moduloContext['subStatusResponder'], true);

        $opcionesEstado = $moduloContext['opcionesEstado'];

        return view('gestion_filtros_detalle', compact(
            'registro',
            'puedeResponderMesaControl',
            'soportesMesaControlSlots',
            'moduloContext',
            'opcionesEstado'
        ));
    }

   private function mostrarModuloProceso(Request $request, string $modulo, string $id)
{
    $moduloContext = ClienteModuloContext::get($modulo);

    $cliente = Cliente::with(['user', 'mesaControlUser', 'municipio.departamento', 'departamento'])->findOrFail($id);
    $this->validarAccesoFiltro($cliente, $request);

    $registro = $this->mapRegistro($cliente);

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

        $historial[] = [
            'fecha' => $fechaTransicion?->format('d/M Y | g:i a') ?? '',
            'status' => $statusTransicion !== '' ? $statusTransicion : '-',
            'sub_status' => $subStatusTransicion !== '' ? $subStatusTransicion : '-',
            'comentario' => $esMesaControlActual ? ($cliente->observacion_mesa_control ?: '-') : '-',
            'respuesta_archivos' => $esMesaControlActual ? $soportesMesaHistorial : [],
            'soporte_archivos' => [],
            'autor' => strtoupper($transicion->actor?->name ?? 'USUARIO CRM'),
            'orden' => $fechaTransicion?->timestamp ?? 0,
        ];
    }

    $ultimaEntrada = collect($historial)->sortBy('orden')->last();
    $statusActualCliente = (string) ($cliente->status ?? '');
    $subStatusActualCliente = (string) ($cliente->sub_status ?? '');

    if (
        $statusActualCliente !== ''
        && (
            ! $ultimaEntrada
            || (string) ($ultimaEntrada['status'] ?? '') !== $statusActualCliente
            || (string) ($ultimaEntrada['sub_status'] ?? '') !== $subStatusActualCliente
        )
    ) {
        $autorActual = strtoupper($cliente->mesaControlUser?->name ?? $cliente->user?->name ?? 'USUARIO CRM');

        $historial[] = [
            'fecha' => $cliente->updated_at?->format('d/M Y | g:i a') ?? '',
            'status' => $statusActualCliente,
            'sub_status' => $subStatusActualCliente !== '' ? $subStatusActualCliente : '-',
            'comentario' => $cliente->observacion_mesa_control ?: '-',
            'respuesta_archivos' => [],
            'soporte_archivos' => $soportesAsesorHistorial,
            'autor' => $autorActual,
            'orden' => $cliente->updated_at?->timestamp ?? 0,
        ];
    }

    if (
        $transicionesEstado->isEmpty()
        && ((string) $cliente->status !== 'Inicia Filtro' || (string) $cliente->sub_status !== 'Inicia Filtro')
    ) {
        $historial[] = [
            'fecha' => $cliente->updated_at?->format('d/M Y | g:i a') ?? '',
            'status' => (string) $cliente->status,
            'sub_status' => (string) $cliente->sub_status,
            'comentario' => $cliente->observacion_mesa_control ?: '-',
            'respuesta_archivos' => $soportesMesaHistorial,
            'soporte_archivos' => [],
            'autor' => strtoupper($cliente->mesaControlUser?->name ?? 'USUARIO CRM'),
            'orden' => $cliente->updated_at?->timestamp ?? 0,
        ];
    }

    usort($historial, function (array $a, array $b): int {
        return ($b['orden'] ?? 0) <=> ($a['orden'] ?? 0);
    });

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

    $puedeActualizarSupervisor = $user?->isSupervisor() === true;
    $esAsesor = $user?->isAsesor() === true;

    $puedeActualizarAsesor = $esAsesor
        && $modulo === 'filtros'
        && (string) $cliente->status === 'Viable'
        && (string) $cliente->sub_status === 'Pendiente Radicar';

    $opcionesEstado = $moduloContext['opcionesEstado'] ?? [];

    $filtrosContext = ClienteModuloContext::get('filtros');
    $opcionesEstadoAsesor = $filtrosContext['opcionesEstadoAsesor'] ?? [];

    $contextosModulos = ClienteModuloContext::all();

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
        'soportesCreacionSlots',
        'moduloContext',
        'opcionesEstado',
        'opcionesEstadoAsesor',
        'catalogoStatusGeneral',
        'catalogoSubStatusGeneral',
        'mostrarPanelAsesor',
        'esAsesor',
        'puedeActualizarAsesor',
        'puedeActualizarSupervisor'
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

        $data = $request->validate([
            'status' => ['required', Rule::in($estadosPermitidos)],
            'sub_status' => ['required', Rule::in($subEstadosPermitidos)],
            'observacion_mesa_control' => 'required|string|max:2000',
            'mesa_soporte_1' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'mesa_soporte_2' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
            'mesa_soporte_3' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp3,wav,ogg,m4a,mp4,mov,avi,mkv,webm|max:20480',
        ]);

        $combinacionValida = $opcionesEstado->contains(function (array $opcion) use ($data): bool {
            return (string) $opcion['status'] === (string) $data['status']
                && (string) $opcion['sub_status'] === (string) $data['sub_status'];
        });

        if (! $combinacionValida) {
            return back()->withErrors([
                'sub_status' => 'La combinacion de status y sub status no es valida para este modulo.',
            ])->withInput();
        }

        $mesaSoporte1 = $this->storeSupportFile($request, 'mesa_soporte_1', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_1;

        $mesaSoporte2 = $this->storeSupportFile($request, 'mesa_soporte_2', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_2;

        $mesaSoporte3 = $this->storeSupportFile($request, 'mesa_soporte_3', 'soportes_mesa_control')
            ?? $cliente->mesa_soporte_3;

        $cliente->update([
            'status' => $data['status'],
            'sub_status' => $data['sub_status'],
            'observacion_mesa_control' => $data['observacion_mesa_control'],
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
}
