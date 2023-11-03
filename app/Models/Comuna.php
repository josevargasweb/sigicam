<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Comuna extends Model
{
    protected $table = 'comuna';
	public $timestamps = false;
    protected $primaryKey = 'id_comuna';

    public static function getComunas(){
        return self::all()->pluck('nombre_comuna','id_comuna');
    }    

    public static function getRegion($id_comuna){
        $respuesta = Comuna::select("id_region")->where("id_comuna", $id_comuna)->first();
        return $respuesta;
    }    

}