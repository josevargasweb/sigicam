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
class TipoUnidad extends Model {
    protected $table = "tipos_unidad";

    public function camas(){
        return $this->hasMany("App\Models\Cama", "tipo_unidad", "id");
    }

    public static function seleccion(){
        $tipos = self::orderBy('nombre', 'asc')
        ->get(["id", "nombre"]);
        $r = array();
        foreach($tipos as $tipo){
            if($tipo->id == 1){
                $r[$tipo->id] = "ADULTO";
            }elseif($tipo->id == 2){
                $r[$tipo->id] = "ADULTO (UCI)";
            }elseif($tipo->id == 3){
                $r[$tipo->id] = "PEDIATRÍA";
            }elseif($tipo->id == 4){
                $r[$tipo->id] = "PEDIATRÍA (UCI)";
            }elseif($tipo->id == 5){
                $r[$tipo->id] = "NEONATOLOGÍA";
            }else{
                $r[$tipo->id] = "NEONATOLOGÍA (UCI)";
            }
        }
        return $r;
    }

    public static function descripcion($id){
        return TipoUnidad::find($id,'descripcion');
    }
}