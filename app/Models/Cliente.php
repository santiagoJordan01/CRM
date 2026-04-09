<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'user_id',
        'mesa_control_user_id',
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
        'observacion_mesa_control',
        'tasa_ea',
        'numero_credito',
        'oficina_radicacion',
        'financiera_1',
        'financiera_2',
        'financiera_3',
        'status',
        'sub_status',
        'recordatorio',
        'mesa_control_respondido_at',
        'soporte_1',
        'soporte_2',
        'soporte_3',
        'soporte_4',
        'soporte_5',
        'soporte_6',
        'mesa_soporte_1',
        'mesa_soporte_2',
        'mesa_soporte_3',
    ];

    protected $casts = [
        'fecha_vinculacion' => 'date',
        'fecha_nacimiento' => 'date',
        'mesa_control_respondido_at' => 'datetime',
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

    public function mesaControlUser()
    {
        return $this->belongsTo(User::class, 'mesa_control_user_id');
    }
}
