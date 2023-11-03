<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Dotacion extends Model
{
  protected $table = "dotacion_cama";
  protected $primaryKey = "id";
  public $timestamps = true;
    //

    public static function dotacion($id){
        $valor = DB::table('dotacion_cama')
                    ->select('dotacion')
                    ->where('id_servicio',$id)
                    ->where('visible', true)
                    ->first();

        return ($valor) ? $valor->dotacion : "Sin Numero";
    }
}
