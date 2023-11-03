<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomiciliariaComentario extends Model
{
    protected $table = "lista_comentarios_domiciliaria";
    protected $primaryKey = "id";
    public $timestamps = false;
}
