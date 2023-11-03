<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

class EvolucionAcompanamiento extends Model{

    protected $table = "t_evolucion_acompanamiento";
    protected $primaryKey = "id";
    public $timestamps = false;


    public static function fechaInicioAcompanamiento($caso){

        $inicio = EvolucionAcompanamiento::select("fecha")
        ->where("id_caso", $caso)
        ->orderBy("fecha", "asc")
        ->first();

        return ($inicio)?Carbon::parse($inicio->fecha)->startOfDay():"false";

	}

    public static function fechaFinAcompanamiento($caso){

        //revisar ultimo dia que se realizo el guardado de la evolucion de acom
        $fin = EvolucionAcompanamiento::select("fecha_termino","comentario")
		->where("id_caso", $caso)
		->orderBy("id", "desc")
        ->first();

        //si tiene, revisar la fecha de termino, si este no posee fecha de termino se asume que aun esta vigente.
        return ($fin && $fin->fecha_termino != null)?Carbon::parse($fin->fecha_termino)->endOfDay():Carbon::now()->endOfDay();

    }

    public static function cerrarAcompanamiento($caso){

        //Esta funcion es para cerrar las acompanamientos cuando se realice un alta

        //se le debe poner como comentario alta


    }

    public static function acompanamientosFecha($caso, $fecha){

        $fecha = Carbon::parse($fecha)->endOfDay();
        //Identificar acompanamientos del dia
        return EvolucionAcompanamiento::select("fecha_termino","comentario","tipo_acompanamiento")
        ->where(function($query) use ($fecha,$caso) {
            $query->where("id_caso", $caso)
            ->where("fecha","<=",$fecha)
            ->where(function($query) use ($fecha) {
                $query->Where("fecha_termino",">",$fecha)
                ->orWhereNull("fecha_termino");
            })
            ->where(function($query) {
                $query->where("comentario","<>","correccion")
                ->orWhereNull("comentario")   ;
            });
        })

		->orderBy("id", "desc")
        ->first();

    }

    // public static function obtenerUltimaAcompanamiento($caso){

    //     //Esta funcion es para cerrar las acompanamientos cuando se realice un alta
    //     $caso_completo = Caso::select("casos.fecha_termino as fTerminoCaso, h.fecha_ingreso", "h.fecha_termino fTerminoHospDom")
    //         ->where("id",$caso)
    //         ->whereNull("comentario")
    //         ->leftjoin("hospitalizacion_domiciliaria as h","h.caso","=","casos.id")
    //         ->first();

    //     if($caso_completo && ($caso_completo->fTerminoCaso == null || ($caso_completo->fecha_ingreso != null && $caso_completo->fTerminoHospDom == null))){
    //         //Cuando tiene caso abierto o hosp domiciliaria
    //         $acompanamientos = EvolucionAcompanamiento::where(function($query) use ($caso) {
    //             $query->where('id_caso',$caso)
    //             ->whereNull('fecha_termino');
    //         })->get();
    //     }else{
    //         //Cuando tiene caso cerrado se pondra en el comentario alta, para saber cuales fueron los ultimos en cerrarse
    //         $acompanamientos = EvolucionAcompanamiento::where("comentario", "alta")->get();
    //     }

    //     $espe = [];
    //     if(count($acompanamientos) >= 1){
    //         if($acompanamientos != "[]"){
    //             foreach ($acompanamientos as $value) {
    //                 $espe [] = $value->id_especialidad;
    //             }
    //         }
    //     }else{
    //         $espe [] = 7;
    //     }

    //     return $espe;
    // }

    public static function correccionAcompanamiento($caso){
        $fecha_inicio = Carbon::now()->startOfDay();
        //buscar acompanamientos creadas hoy dia y anularlas
        $acompanas_a = EvolucionAcompanamiento::select('id','fecha_termino','fecha','comentario')
                ->where("id_caso", $caso)
                ->whereNull("comentario")
                ->where("fecha",">=",$fecha_inicio)
                ->get();

		foreach($acompanas_a as $acom){
            $atcorreccion_acompanamiento = EvolucionAcompanamiento::where("id",$acom->id)->first();
            $atcorreccion_acompanamiento->comentario = "correccion";
            $atcorreccion_acompanamiento->save();
        }

	}

    public static function agregarAcompanamientoes($caso, $acom){

        $acompanas_a = EvolucionAcompanamiento::select('tipo_acompanamiento')
            ->where("id_caso", $caso)
            ->whereNull("fecha_termino")
            ->whereNull("comentario")->first();

            if (isset($acompanas_a->tipo_acompanamiento) && strcmp($acom, $acompanas_a->tipo_acompanamiento) == 0) {
                $acom_quitar = EvolucionAcompanamiento::where("id_caso", $caso)->where("tipo_acompanamiento",$acompanas_a->tipo_acompanamiento)
                ->whereNull("fecha_termino")
                ->whereNull("comentario")
                ->first();
                $acom_quitar->fecha_termino = Carbon::now();
                $acom_quitar->usuario_quita = Auth::user()->id;
                $acom_quitar->save();

            }

            $acom_agregar = new EvolucionAcompanamiento();
            $acom_agregar->fecha = Carbon::now();
            $acom_agregar->id_caso = $caso;
            $acom_agregar->tipo_acompanamiento = $acom;
            $acom_agregar->usuario_asigna = Auth::user()->id;
            $acom_agregar->save();
	}

}

?>
