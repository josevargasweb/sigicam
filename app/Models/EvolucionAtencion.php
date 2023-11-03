<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

class EvolucionAtencion extends Model{

    protected $table = "t_evolucion_atencion";
    protected $primaryKey = "id";
    public $timestamps = false;


    public static function fechaInicioAtencion($caso){

        $inicio = EvolucionAtencion::select("fecha")
        ->where("id_caso", $caso)
        ->orderBy("fecha", "asc")
        ->first();

        return ($inicio)?Carbon::parse($inicio->fecha)->startOfDay():"false";

	}

    public static function fechaFinAtencion($caso){

        //revisar ultimo dia que se realizo el guardado de la evolucion de atencion
        $fin = EvolucionAtencion::select("fecha_termino","comentario")
		->where("id_caso", $caso)
		->orderBy("id", "desc")
        ->first();

        //si tiene, revisar la fecha de termino, si este no posee fecha de termino se asume que aun esta vigente.
        return ($fin && $fin->fecha_termino != null)?Carbon::parse($fin->fecha_termino)->endOfDay():Carbon::now()->endOfDay();

    }

    public static function cerrarAtencion($caso){

        //Esta funcion es para cerrar las atenciones cuando se realice un alta

        //se le debe poner como comentario alta


    }

    public static function atencionesFecha($caso, $fecha){

        $fecha = Carbon::parse($fecha)->endOfDay();
        //Identificar atenciones del dia
        return EvolucionAtencion::select("fecha_termino","comentario","tipo_atencion")
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

    // public static function obtenerUltimaAtencion($caso){

    //     //Esta funcion es para cerrar las atenciones cuando se realice un alta
    //     $caso_completo = Caso::select("casos.fecha_termino as fTerminoCaso, h.fecha_ingreso", "h.fecha_termino fTerminoHospDom")
    //         ->where("id",$caso)
    //         ->whereNull("comentario")
    //         ->leftjoin("hospitalizacion_domiciliaria as h","h.caso","=","casos.id")
    //         ->first();

    //     if($caso_completo && ($caso_completo->fTerminoCaso == null || ($caso_completo->fecha_ingreso != null && $caso_completo->fTerminoHospDom == null))){
    //         //Cuando tiene caso abierto o hosp domiciliaria
    //         $atenciones = EvolucionAtencion::where(function($query) use ($caso) {
    //             $query->where('id_caso',$caso)
    //             ->whereNull('fecha_termino');
    //         })->get();
    //     }else{
    //         //Cuando tiene caso cerrado se pondra en el comentario alta, para saber cuales fueron los ultimos en cerrarse
    //         $atenciones = EvolucionAtencion::where("comentario", "alta")->get();
    //     }

    //     $espe = [];
    //     if(count($atenciones) >= 1){
    //         if($atenciones != "[]"){
    //             foreach ($atenciones as $value) {
    //                 $espe [] = $value->id_especialidad;
    //             }
    //         }
    //     }else{
    //         $espe [] = 7;
    //     }

    //     return $espe;
    // }

    public static function correccionAtencion($caso){
        $fecha_inicio = Carbon::now()->startOfDay();
        //buscar atenciones creadas hoy dia y anularlas
        $atencions_a = EvolucionAtencion::select('id','fecha_termino','fecha','comentario')
                ->where("id_caso", $caso)
                ->whereNull("comentario")
                ->where("fecha",">=",$fecha_inicio)
                ->get();

		foreach($atencions_a as $ate){
            $atcorreccion_atencion = EvolucionAtencion::where("id",$ate->id)->first();
            $atcorreccion_atencion->comentario = "correccion";
            $atcorreccion_atencion->save();
        }

	}

    public static function agregarAtenciones($caso, $atencion){

        $atencions_a = EvolucionAtencion::select('tipo_atencion')
            ->where("id_caso", $caso)
            ->whereNull("fecha_termino")
            ->whereNull("comentario")->first();

            if (isset($atencions_a->tipo_atencion) && strcmp($atencion, $atencions_a->tipo_atencion) !== 0) {
                $atencion_quitar = EvolucionAtencion::where("id_caso", $caso)->where("tipo_atencion",$atencions_a->tipo_atencion)
                ->whereNull("fecha_termino")
                ->whereNull("comentario")
                ->first();
                $atencion_quitar->fecha_termino = Carbon::now();
                $atencion_quitar->usuario_quita = Auth::user()->id;
                $atencion_quitar->save();

            }

            $atencion_agregar = new EvolucionAtencion();
            $atencion_agregar->fecha = Carbon::now();
            $atencion_agregar->id_caso = $caso;
            $atencion_agregar->tipo_atencion = $atencion;
            $atencion_agregar->usuario_asigna = Auth::user()->id;
            $atencion_agregar->save();
	}

}

?>
