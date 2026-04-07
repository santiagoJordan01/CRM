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
                    ['status' => 'Preradicacion Comercial', 'sub_status' => 'Pendiente Radicar'],
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
                'subStatusResponder' => ['Pendiente Radicar', 'Envio Digital Docs'],
                'opcionesEstado' => [
                    ['status' => 'Radicado', 'sub_status' => 'Pendiente Aprobacion'],
                    ['status' => 'No Radicado', 'sub_status' => 'No Gestionable'],
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
                'subStatusResponder' => ['Pendiente Aprobacion'],
                'opcionesEstado' => [
                    ['status' => 'Aprobado', 'sub_status' => 'Pendiente Desembolso'],
                    ['status' => 'No Aprobado', 'sub_status' => 'No Gestionable'],
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
                'subStatusResponder' => ['Pendiente Desembolso'],
                'opcionesEstado' => [
                    ['status' => 'Desembolsado', 'sub_status' => 'Desembolsado'],
                    ['status' => 'No Desembolsado', 'sub_status' => 'No Gestionable'],
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
                $q->whereIn('status', ['Preradicacion Comercial', 'Radicado', 'No Radicado'])
                    ->orWhere('sub_status', 'Envio Digital Docs')
                    ->orWhere('status', 'Envio Digital Docs');
            });

            return;
        }

        if ($modulo === 'aprobados') {
            $query->where(function (Builder $q) {
                $q->where('sub_status', 'Pendiente Aprobacion')
                    ->orWhereIn('status', ['Aprobado', 'No Aprobado']);
            });

            return;
        }

        if ($modulo === 'desembolso') {
            $query->where(function (Builder $q) {
                $q->where('sub_status', 'Pendiente Desembolso')
                    ->orWhereIn('status', ['Desembolsado', 'No Desembolsado']);
            });
        }
    }

    public static function inferFromCliente(Cliente $cliente): string
    {
        if (
            $cliente->sub_status === 'Pendiente Desembolso'
            || in_array((string) $cliente->status, ['Desembolsado', 'No Desembolsado'], true)
        ) {
            return 'desembolso';
        }

        if (
            $cliente->sub_status === 'Pendiente Aprobacion'
            || in_array((string) $cliente->status, ['Aprobado', 'No Aprobado'], true)
        ) {
            return 'aprobados';
        }

        if (
            in_array((string) $cliente->status, ['Preradicacion Comercial', 'Radicado', 'No Radicado', 'Envio Digital Docs'], true)
            || (string) $cliente->sub_status === 'Envio Digital Docs'
        ) {
            return 'radicados';
        }

        return 'filtros';
    }
}
