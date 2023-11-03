<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patologias extends Model
{
    protected $table = "patologias";
	public $timestamps = false;
    protected $primaryKey = "id";
    
    public static function seleccion(){
        $tipos = self::orderBy('id', 'asc')
        ->get(["id", "nombre","abreviacion"]);
        $r = array();
        foreach($tipos as $tipo){
            $r[$tipo->id] = "(".$tipo->abreviacion.") ".$tipo->nombre;
        }
        $r["select"] = "Seleccione";
        return $r;
    }
}