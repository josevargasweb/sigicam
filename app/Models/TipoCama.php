<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 1/16/15
 * Time: 4:10 PM
 */
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use Log;
class TipoCama extends Model {
    protected $table = "tipos_cama";

    public function camas(){
        return $this->hasMany("App\Models\Cama", "tipo", "id");
    }

    public static function seleccion(){
        $tipos = self::orderBy('nombre', 'asc')
        ->whereIn("id",[11,12,13])
        ->get(["id", "nombre"]);
        $r = array();
        foreach($tipos as $tipo){
            $r[$tipo->id] = $tipo->nombre;
        }
        return $r;
    }

    public static function traduccionTipo($id){

        if($id == 11){
            $tipo = "Básica";
        } else if($id == 12){
            $tipo = "Media";
        }else{
            $tipo = "Crítica";
        }
        
        return $tipo;
    }
}