<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $fillable = ['nombre', 'cod_dane', 'departamento_id'];
    
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}