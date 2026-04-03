<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = ['nombre', 'cod_dane'];
    
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
}