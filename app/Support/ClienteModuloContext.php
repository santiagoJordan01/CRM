<?php

namespace App\Support;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;

class ClienteModuloContext
{
    public static function all(): array
    {
        return [
            'filtros' => [
                'clave' => 'filtros',
                'titulo' => 'Filtro',
                'titulo_gestion' => 'Gestion filtros',
                'indexRoute' => 'filtros.index',
                'showRoute' => 'filtros.show',
                'procesoRoute' => 'filtros.proceso',
                'responderRoute' => 'filtros.responder',
                'subStatusResponder' => ['Inicia Filtro', 'Pendiente Mesa de Control'],
                'opcionesEstado' => [
                    ['status' => 'Viable', 'sub_status' => 'Pendiente Radicar'],
                    ['status' => 'No Viable', 'sub_status' => 'Expo Titular Color Semaforo'],
                ],
                'opcionesEstadoAsesor' => [
                    ['status' => 'Preradicacion Comercial', 'sub_status' => 'Envio Digital Docs'],
                ],
            ],
            'radicados' => [
                'clave' => 'radicados',
                'titulo' => 'Radicado',
                'titulo_gestion' => 'Gestion radicados',
                'indexRoute' => 'radicados.index',
                'showRoute' => 'radicados.show',
                'procesoRoute' => 'radicados.proceso',
                'responderRoute' => 'radicados.responder',
                'subStatusResponder' => ['Envio Digital Docs', 'En Analisis', 'En Comite'],
                'opcionesEstado' => [
                    ['status' => 'Aprobado', 'sub_status' => 'Aprobado'],
                    ['status' => 'Negado', 'sub_status' => 'Cap Dcto'],
                    ['status' => 'Negado', 'sub_status' => 'Sujeto a Reconsideracion'],
                    ['status' => 'Negado', 'sub_status' => 'No Sujeto a Reconsideracion'],
                    ['status' => 'En Estudio', 'sub_status' => 'En Analisis'],
                    ['status' => 'En Estudio', 'sub_status' => 'En Comite'],
                ],
            ],
            'aprobados' => [
                'clave' => 'aprobados',
                'titulo' => 'Aprobado',
                'titulo_gestion' => 'Gestion aprobados',
                'indexRoute' => 'aprobados.index',
                'showRoute' => 'aprobados.show',
                'procesoRoute' => 'aprobados.proceso',
                'responderRoute' => 'aprobados.responder',
                'subStatusResponder' => ['Pendiente Aprobacion', 'Aprobado'],
                'opcionesEstado' => [
                    ['status' => 'Aprobado', 'sub_status' => 'Pte desembolso'],
                    ['status' => 'Cliente Desiste', 'sub_status' => 'No acepta condiciones'],
                ],
            ],
            'desembolso' => [
                'clave' => 'desembolso',
                'titulo' => 'Desembolso',
                'titulo_gestion' => 'Gestion desembolso',
                'indexRoute' => 'desembolso.index',
                'showRoute' => 'desembolso.show',
                'procesoRoute' => 'desembolso.proceso',
                'responderRoute' => 'desembolso.responder',
                'subStatusResponder' => ['Pendiente Desembolso', 'Pte desembolso', 'fallido'],
                'opcionesEstado' => [
                    ['status' => 'Contabilizacion Pendiente', 'sub_status' => 'exitoso'],
                    ['status' => 'Contabilizacion Pendiente', 'sub_status' => 'fallido'],
                    ['status' => 'Contabilizacion aceptada', 'sub_status' => 'exitoso'],
                    ['status' => 'Contabilizacion aceptada', 'sub_status' => 'fallido'],
                ],
            ],
        ];
    }

    public static function get(string $modulo): array
    {
        $contextos = self::all();

        if (! isset($contextos[$modulo])) {
            abort(404, 'Modulo no encontrado.');
        }

        return $contextos[$modulo];
    }

    public static function applyFilter(Builder $query, string $modulo): void
    {
        if ($modulo === 'filtros') {
            $query->where(function (Builder $q) {
                $q->whereIn('status', ['Inicia Filtro', 'Viable', 'No Viable'])
                    ->orWhereIn('sub_status', ['Inicia Filtro', 'Pendiente Mesa de Control']);
            });

            return;
        }

        if ($modulo === 'radicados') {
            $query->where(function (Builder $q) {
                $q->whereIn('status', ['Preradicacion Comercial', 'En Estudio', 'Negado', 'Radicado', 'No Radicado'])
                    ->orWhereIn('sub_status', ['Envio Digital Docs', 'En Analisis', 'En Comite', 'Cap Dcto', 'Sujeto a Reconsideracion', 'No Sujeto a Reconsideracion']);
            });

            return;
        }

        if ($modulo === 'aprobados') {
            $query->where(function (Builder $q) {
                $q->whereIn('sub_status', ['Pendiente Aprobacion', 'Aprobado'])
                    ->orWhereIn('status', ['No Aprobado', 'Cliente Desiste']);
            });

            return;
        }

        if ($modulo === 'desembolso') {
            $query->where(function (Builder $q) {
                $q->whereIn('sub_status', ['Pendiente Desembolso', 'Pte desembolso', 'exitoso', 'fallido'])
                    ->orWhereIn('status', ['Contabilizacion Pendiente', 'Contabilizacion aceptada', 'Desembolsado', 'No Desembolsado']);
            });
        }
    }

    public static function inferFromCliente(Cliente $cliente): string
    {
        if (
            in_array((string) $cliente->sub_status, ['Pendiente Desembolso', 'Pte desembolso', 'exitoso', 'fallido'], true)
            || in_array((string) $cliente->status, ['Contabilizacion Pendiente', 'Contabilizacion aceptada', 'Desembolsado', 'No Desembolsado'], true)
        ) {
            return 'desembolso';
        }

        if (
            in_array((string) $cliente->sub_status, ['Pendiente Aprobacion', 'Aprobado', 'No acepta condiciones'], true)
            || in_array((string) $cliente->status, ['Aprobado', 'No Aprobado', 'Cliente Desiste'], true)
            || (string) $cliente->sub_status === 'Aprobado'
        ) {
            return 'aprobados';
        }

        if (
            in_array((string) $cliente->status, ['Preradicacion Comercial', 'En Estudio', 'Negado', 'Radicado', 'No Radicado', 'Envio Digital Docs'], true)
            || in_array((string) $cliente->sub_status, ['Envio Digital Docs', 'En Analisis', 'En Comite', 'Cap Dcto', 'Sujeto a Reconsideracion', 'No Sujeto a Reconsideracion'], true)
        ) {
            return 'radicados';
        }

        return 'filtros';
    }
}
