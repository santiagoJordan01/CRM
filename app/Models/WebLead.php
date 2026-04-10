<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebLead extends Model
{
    protected $fillable = [
        'nombre_cliente',
        'cedula',
        'email',
        'celular_cliente',
        'campania',
        'producto',
        'canal',
        'genero',
        'departamento_id',
        'municipio_id',
        'fecha_nacimiento',
        'monto_referido',
        'ingreso_referido',
        'observaciones',
        'status',
        'converted_cliente_id',
        'converted_by_user_id',
        'converted_at',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'converted_at' => 'datetime',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function convertedCliente()
    {
        return $this->belongsTo(Cliente::class, 'converted_cliente_id');
    }

    public function convertedBy()
    {
        return $this->belongsTo(User::class, 'converted_by_user_id');
    }
}
