<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait InteractsWithClienteSupports
{
    protected function soporteMeta(?string $path): ?array
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $nombreArchivo = basename($path);

        if (strpos($nombreArchivo, '___') !== false) {
            $parts = explode('___', $nombreArchivo, 2);
            $nombreArchivo = $parts[1] ?? $nombreArchivo;
        } else {
            // Compatibilidad con formatos anteriores.
            $nombreArchivo = preg_replace('/^\d+_/', '', $nombreArchivo);
        }

        return [
            'nombre' => $nombreArchivo,
            'url' => asset('storage/' . $path),
        ];
    }

    protected function storeSupportFile(Request $request, string $field, string $directory): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $originalName = basename((string) $file->getClientOriginalName());
        $originalName = str_replace(["\\", '/'], '_', trim($originalName));

        if ($originalName === '') {
            $originalName = $file->hashName();
        }

        $storedName = time() . '_' . uniqid() . '___' . $originalName;

        return $file->storeAs($directory, $storedName, 'public');
    }

    protected function soporteSlots(?string $soporte1, ?string $soporte2, ?string $soporte3): array
    {
        return [
            [
                'titulo' => 'Soporte 1',
                'archivo' => $this->soporteMeta($soporte1),
            ],
            [
                'titulo' => 'Soporte 2',
                'archivo' => $this->soporteMeta($soporte2),
            ],
            [
                'titulo' => 'Soporte 3',
                'archivo' => $this->soporteMeta($soporte3),
            ],
        ];
    }
}
