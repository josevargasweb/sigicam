<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class BoletinProducto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "boletin_producto";
    protected $primaryKey = "id";

    protected $auditInclude = [
       
    ];
    protected $auditTimestamps = true;
    protected $auditThreshold = 10;

    public static function comprobarEdicion($caso, $producto, $fecha){

        $boletin = Boletin::where("caso",$caso)->orderBy("id", "asc")->first();
        $format_fecha = Carbon::parse($fecha)->format("Y-m-d H:i:s");

        //si el boletin no existe significa que no tendra edicion
        if(!isset($boletin) ){
            return "no hay datos";
        }

        //revisar si tiene un producto ese dia
        $producto = BoletinProducto::where("id_boletin", $boletin->id)
                        ->join("productos", "productos.id","=","boletin_producto.id_producto")
                        ->where("id_producto", $producto)
                        ->where("fecha", $format_fecha)
                        ->first();

        if(isset($producto)){
            return "Este $producto->tipo ya fue cargado ¿Desea cambiar estos datos?";
            //return "El <b>$producto->tipo -".$producto->nombre ."</b> (<b>Código: ".$producto->codigo."</b>) ya se encuentra asignado al día <b>".$fecha." </b>, con  una cantidad de <b>".$producto->cantidad."</b> y un valor de <b>".$producto->valor."</b>¿Desea cambiar estos datos?" ;
        }else{
            return "no hay datos";
        }
    }    
}
