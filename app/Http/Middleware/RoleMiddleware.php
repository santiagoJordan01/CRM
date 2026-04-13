<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Jerarquia de roles: asesor < supervisor < admin.
     * Un rol superior hereda acceso de roles inferiores.
     *
     * @var array<string, int>
     */
    private const ROLE_LEVELS = [
        'asesor' => 1,
        'supervisor' => 2,
        'admin' => 3,
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'No tienes permisos para realizar esta accion.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userLevel = self::ROLE_LEVELS[$user->role] ?? null;
        if ($userLevel === null) {
            abort(403, 'No tienes permisos para realizar esta accion.');
        }

        $requiredLevels = array_values(array_filter(
            array_map(static fn (string $role): ?int => self::ROLE_LEVELS[$role] ?? null, $roles),
            static fn (?int $level): bool => $level !== null
        ));

        if (empty($requiredLevels)) {
            abort(403, 'No tienes permisos para realizar esta accion.');
        }

        $minimumRequiredLevel = min($requiredLevels);
        if ($userLevel < $minimumRequiredLevel) {
            abort(403, 'No tienes permisos para realizar esta accion.');
        }

        return $next($request);
    }
}
