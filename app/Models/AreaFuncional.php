<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ComplejidadAreaFuncional;
use DB;
use Auth;

class AreaFuncional extends Model
{
    protected $table = "area_funcional";
    protected $primaryKey = "id_area_funcional";

    public $timestamps = false;


    public function complejidad_area_funcional(){
    	return $this->hasOne(ComplejidadAreaFuncional::class, 'id_area_funcional');
    }

    public static function areasEnEstablecimiento($idEstablecimiento){
 
            return DB::table('area_funcional as a')
                    ->select('a.id_area_funcional as id', 'a.nombre')
                    ->join('unidades_en_establecimientos as u', 'u.id_area_funcional', '=', 'a.id_area_funcional')
                    ->where('u.establecimiento', $idEstablecimiento)
                    ->pluck('a.nombre','a.id_area_funcional as id');
    }

    public static function nombreTodasAreasFuncionales(){
        return DB::table('area_funcional as a')
                    ->select('a.id_area_funcional as id', 'a.nombre')
                    ->pluck('a.nombre','a.id_area_funcional as id');
    }

    public static function areaUnidad($unidad){
        return DB::table('area_funcional as a')
                ->select('a.nombre')
                ->join('unidades_en_establecimientos as u', 'u.id_area_funcional', '=', 'a.id_area_funcional')
                ->where('u.url', $unidad)
                ->where('u.establecimiento', Auth::user()->establecimiento)
                ->first()->nombre;
    }

    public static function nombreAreaFuncional($id){
        $valor = DB::table('area_funcional as a')
                    ->select('a.nombre')
                    ->join('unidades_en_establecimientos as u', 'u.id_area_funcional', '=', 'a.id_area_funcional')
                    ->where('a.id_area_funcional',$id)
                    // ->where('u.establecimiento', Auth::user()->establecimiento)
                    ->first();

        return ($valor) ? $valor->nombre : "Sin especificar";
    }

    public static function todasAreasFuncionales(){
        $areas = DB::table('area_funcional')
        ->select('id_area_funcional as id','nombre')
        ->get();

        return response()->json($areas);
    }
	public static function areasFuncionalesEstablecimientoOrdenadas($establecimiento){
		
		return DB::select("SELECT 
		a.id_area_funcional AS id,
		a.nombre,
		a.orden 
		FROM unidades_en_establecimientos u 
		INNER JOIN area_funcional a ON a.id_area_funcional = u.id_area_funcional
		INNER JOIN salas_con_camas s ON s.establecimiento = u.id
		WHERE u.establecimiento = ? 
		AND u.visible IS TRUE
		GROUP BY a.id_area_funcional
		ORDER BY a.orden ASC",[$establecimiento]);
	}
	public function guardarOrden($dato){
		DB::update("UPDATE area_funcional SET orden = ? WHERE id_area_funcional = ?",[$dato["orden"],$dato["id"]]);
	}
}