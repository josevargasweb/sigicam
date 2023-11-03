<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class Producto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "productos";
    protected $primaryKey = "id";

    protected $auditInclude = [
       
    ];
    protected $auditTimestamps = true;
    protected $auditThreshold = 10;


    protected $fillable = [
        'visible', 'nombre', 'codigo'
    ];

    public function boletinesProductos(){
        return $this->belongsToMany(Boletin::class, "boletin_producto", "id_producto", "id_boletin");
    }


    public static function infoProductoHabilitados(){
		$habilitados = Producto::join('establecimientos as e','productos.id_establecimiento','=','e.id')
		->select("productos.id","productos.codigo","productos.nombre","e.nombre as nombre_establecimiento","productos.tipo","productos.valor")
        ->where("productos.visible", "=",  true)
		->get();
		
		return $habilitados;
	}

	public static function infoProductoDeshabilitados(){
		$deshabilitados = Producto::join('establecimientos as e','productos.id_establecimiento','=','e.id')
		->select("productos.id","productos.codigo","productos.nombre","e.nombre as nombre_establecimiento","productos.tipo","productos.valor")
        ->where("productos.visible", "=",  false)
        ->whereNull("productos.tipo_modificacion")
		->get();
		
		return $deshabilitados;
    }

    
}
