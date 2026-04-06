<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteNotificacion extends Model
{
    protected $table = 'cliente_notificaciones';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'actor_user_id',
        'cliente_nombre',
        'old_status',
        'old_sub_status',
        'new_status',
        'new_sub_status',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
