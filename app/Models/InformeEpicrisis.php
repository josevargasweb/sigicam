<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class InformeEpicrisis extends Model
{
    protected $table = "informe_epicrisis";
	public $timestamps = false;
    protected $primaryKey = "id";
    
    public static function datosEpicrisis($caso){
        $dau = Caso::find($caso, 'dau');
        $dau->dau = ($dau->dau) ? $dau->dau : '--';
    
        $ubicacion = DB::table('t_historial_ocupaciones as t')
                    ->join("camas as c", "c.id", "=", "t.cama")
                    ->join("salas as s", "c.sala", "=", "s.id")
                    ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
                    ->join("area_funcional AS af", "uee.id_area_funcional", "=", "af.id_area_funcional")
                    ->where("t.caso", $caso)
                    ->whereNull("t.motivo")
                    ->select("uee.alias as nombre_unidad",  "af.nombre as nombre_area_funcional","uee.id")
                    ->first();
    
        $formatoFecha = "d-m-Y H:i:s";
        $fechaSolicitud = ListaEspera::select('fecha')->where('caso',$caso)->first();
        $fecha_solicitud = ($fechaSolicitud['fecha']) ? Carbon::parse($fechaSolicitud['fecha'])->format($formatoFecha) : '--';
    
        if($fecha_solicitud == '--'){
            $fechaSolicitud = Caso::select('fecha_ingreso2')->find($caso);
            $fecha_solicitud = ($fechaSolicitud['fecha_ingreso2']) ? Carbon::parse($fechaSolicitud['fecha_ingreso2'])->format($formatoFecha) : '--';
        }
    
        $hospitalizacion = THistorialOcupaciones::select("fecha_ingreso_real","fecha_alta")->where("caso",$caso)->whereNotNull("fecha_ingreso_real")->first();
    
        $fecha_hospitalizacion = ($hospitalizacion['fecha_ingreso_real']) ? Carbon::parse($hospitalizacion['fecha_ingreso_real'])->format($formatoFecha) : '--';
    
        $fecha_egreso = ($hospitalizacion["fecha_alta"] != "") ? Carbon::parse($hospitalizacion['fecha_alta'])->format($formatoFecha) : Carbon::now()->format($formatoFecha);
    
        if($fecha_hospitalizacion != '--'){
            $solicitud = Carbon::parse($fecha_hospitalizacion);
                    $estadia2 = $solicitud->diffInDays($fecha_egreso);
                    $estadia2 .= " días";
        }else{
            $estadia2 = "0 días";
        }
    
        $motivos = Consultas::getMotivosLiberacion();
        unset($motivos['traslado interno']);

        $nombre_unidad = $ubicacion->nombre_unidad;
        $nombre_area_funcional = $ubicacion->nombre_area_funcional;
    
        $diagnosticos = DB::table("diagnosticos")->where('caso', $caso)->get(['id','diagnostico']);

        $subcategoria = HistorialSubcategoriaUnidad::select("id_subcategoria")->where('id_unidad',$ubicacion->id)->where('visible',true)->first();
        
        return  [
            "dau" => $dau,
            "nombre_unidad" => $nombre_unidad,
            "nombre_area_funcional" => $nombre_area_funcional,
            "fecha_solicitud" => $fecha_solicitud,
            "fecha_hospitalizacion" => $fecha_hospitalizacion,
            "fecha_egreso" => $fecha_egreso,
            
            "estadia2" => $estadia2,
            "motivos" => $motivos,
            "diagnosticos" => $diagnosticos,
        	"sub_categoria" => ($subcategoria) ? $subcategoria->id_subcategoria : null 
        ];
        
    }
}