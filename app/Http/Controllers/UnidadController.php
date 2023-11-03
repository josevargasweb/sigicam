<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class UnidadController extends Controller{


	public static function consulta(){
        
                $establecimiento = Auth::user()->establecimiento;    
                
                $datos = DB::table('unidades as u')
                        ->select('u.nombre as unidad','u.id as id_unidad','a.nombre as area','a.id_area_funcional as id_area','ue.establecimiento as establecimiento')
                        ->join('servicios_ofrecidos as so', 'so.unidad', '=', 'u.id')
                        ->join('unidades_en_establecimientos as ue', 'ue.id','=','so.unidad_en_establecimiento')
                        ->join('area_funcional as a', 'a.id_area_funcional','=','ue.id_area_funcional')
                        ->where('ue.establecimiento',$establecimiento)
                        ->orderBy('area')
                        ->get();

                return response()->json($datos);
        }
}

?>
