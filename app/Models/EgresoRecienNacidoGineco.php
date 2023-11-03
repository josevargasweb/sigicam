<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgresoRecienNacidoGineco extends Model
{
    public $table = "egreso_recien_nacido_gineco";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}