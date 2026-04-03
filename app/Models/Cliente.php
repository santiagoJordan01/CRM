<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'user_id',
        'campania',
        'producto',
        'cedula',
        'genero',
        'email',
        'departamento_id',
        'municipio_id',
        'perfil',
        'empresa',
        'fecha_vinculacion',
        'canal',
        'plazo',
        'ingreso_principal',
        'tipo_cliente',
        'destino',
        'nombre_cliente',
        'fecha_nacimiento',
        'sector',
        'nit_empresa',
        'tipo_contrato',
        'monto_filtrado',
        'celular_cliente',
        'otros_ingresos',
        'observaciones',
        'status',
        'sub_status',
        'recordatorio',
        'soporte_1',
        'soporte_2',
        'soporte_3',
    ];

    protected $casts = [
        'fecha_vinculacion' => 'date',
        'fecha_nacimiento' => 'date',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
