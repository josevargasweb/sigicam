<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\Complejidad_servicio;
use App\Models\AreaFuncional;
class AreaFuncionalController extends Controller {
    /**
     * Recibe un archivo y los guarda en public/files
     */
    public static function consulta_areasFuncionales($palabra) {
        //return $palabra;

        $datos=DB::select(DB::raw(
            "
            SELECT
            CONCAT(a.nombre,' ',a.codigo) AS area, a.nombre , a.id_area_funcional, a.codigo, u.codigo as u
            FROM unidades_en_establecimientos as u
            INNER JOIN area_funcional as a ON a.id_area_funcional=u.id_area_funcional
            WHERE  
            a.nombre ILIKE '%".$palabra."%'
            OR a.codigo ILIKE '%".$palabra."%'
            OR u.codigo ILIKE '%".$palabra."%'
            ORDER BY a.nombre ASC
            LIMIT 50
            "
        ));
        

                
		return response()->json($datos);
    }
    
    
    public function getAreaFuncionalPorServicio(Request $request){

        if($request->complejidad_servicio == null){
            return response()->json("");
        }
        else{
            $getAreaFuncional = DB::table('complejidad_area_funcional')
            ->join("area_funcional","area_funcional.id_area_funcional","=","complejidad_area_funcional.id_area_funcional")
    					->where('id_complejidad','=',$request->complejidad_servicio)
                        ->get();            
    		return response()->json($getAreaFuncional);
        }
    }

}