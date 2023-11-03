<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

class EvolucionEspecialidad extends Model{

    protected $table = "t_evolucion_especialidades";
    protected $primaryKey = "id";
    public $timestamps = false;


    public static function fechaInicioEspecialidad($caso){

        $inicio = EvolucionEspecialidad::select("fecha")
        ->where("id_caso", $caso)
        ->orderBy("fecha", "asc")
        ->first();

        return ($inicio)?Carbon::parse($inicio->fecha)->startOfDay():"false";

	}

    public static function fechaFinEspecialidad($caso){

        //revisar ultimo dia que se realizo el guardado de la evolucion de especialidad
        $fin = EvolucionEspecialidad::select("fecha_termino","comentario")
		->where("id_caso", $caso)
		->orderBy("id", "desc")
        ->first();

        //si tiene, revisar la fecha de termino, si este no posee fecha de termino se asume que aun esta vigente.
        return ($fin && $fin->fecha_termino != null)?Carbon::parse($fin->fecha_termino)->endOfDay():Carbon::now()->endOfDay();

    }

    public static function cerrarEspecialidad($caso){

        //Esta funcion es para cerrar las especialidades cuando se realice un alta

        //se le debe poner como comentario alta


    }

    public static function especialidadesFecha($caso, $fecha){

        $fecha = Carbon::parse($fecha)->endOfDay();
        //Identificar especialidades del dia
        return EvolucionEspecialidad::select("fecha_termino","comentario","nombre")
        ->join("especialidades as e","e.id","=","t_evolucion_especialidades.id_especialidad")
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

		->orderBy("t_evolucion_especialidades.id", "desc")
        ->get();

    }

    public static function obtenerUltimaEspecialidad($caso){

        //Esta funcion es para cerrar las especialidades cuando se realice un alta
        $caso_completo = Caso::select("casos.fecha_termino as fTerminoCaso, h.fecha_ingreso", "h.fecha_termino fTerminoHospDom")
            ->where("id",$caso)
            ->whereNull("comentario")
            ->leftjoin("hospitalizacion_domiciliaria as h","h.caso","=","casos.id")
            ->first();

        if($caso_completo && ($caso_completo->fTerminoCaso == null || ($caso_completo->fecha_ingreso != null && $caso_completo->fTerminoHospDom == null))){
            //Cuando tiene caso abierto o hosp domiciliaria
            $especialidades = EvolucionEspecialidad::where(function($query) use ($caso) {
                $query->where('id_caso',$caso)
                ->whereNull('fecha_termino');
            })->get();
        }else{
            //Cuando tiene caso cerrado se pondra en el comentario alta, para saber cuales fueron los ultimos en cerrarse
            $especialidades = EvolucionEspecialidad::where("comentario", "alta")->get();
        }

        $espe = [];
        if(count($especialidades) >= 1){
            if($especialidades != "[]"){
                foreach ($especialidades as $value) {
                    $espe [] = $value->id_especialidad;
                }
            }
        }else{
            $espe [] = 7;
        }

        return $espe;
    }

    public static function correccionEspecialidad($caso){
        $fecha_inicio = Carbon::now()->startOfDay();
        //buscar especialidades creadas hoy dia y anularlas
        $especialidads_a = EvolucionEspecialidad::select('id','fecha_termino','fecha','comentario')
                ->where("id_caso", $caso)
                ->whereNull("comentario")
                ->where("fecha",">=",$fecha_inicio)
                ->get();

		foreach($especialidads_a as $esp){
            $correccion_especialidad = EvolucionEspecialidad::where("id",$esp->id)->first();
            $correccion_especialidad->comentario = "correccion";
            $correccion_especialidad->save();
        }

	}

    public static function agregarEspecialidades($caso, $especialidades){

        $especialidads_a = EvolucionEspecialidad::select('id_especialidad')
            ->where("id_caso", $caso)
            ->whereNull("fecha_termino")
            ->whereNull("comentario")->get();

		$especialidades_actuales = array();
		foreach($especialidads_a as $esp){
			if($esp != ''){
				$especialidades_actuales [] = $esp->id_especialidad;
			}

		}

		//valores agregar
        $array_agregar = array_diff($especialidades, $especialidades_actuales);
		//valores diferentes
        $array_sacar = array_diff($especialidades_actuales, $especialidades);
		//agregar
		foreach ($array_agregar as $agregar) {
			//si no se encuentra la espcialidad  y es distinta
			$esp_agregar = new EvolucionEspecialidad();
			$esp_agregar->fecha = Carbon::now();
			$esp_agregar->id_caso = $caso;
			$esp_agregar->id_especialidad = $agregar;
			$esp_agregar->usuario_asigna = Auth::user()->id;
			$esp_agregar->save();

		}

		//quitar
		foreach ($array_sacar as $sacar) {
			//si no se encuentra la espcialidad  y es distinta
            $esp_quitar = EvolucionEspecialidad::where("id_caso", $caso)->where("id_especialidad",$sacar)
                ->whereNull("fecha_termino")
                ->whereNull("comentario")
                ->first();
			$esp_quitar->fecha_termino = Carbon::now();
			$esp_quitar->usuario_quita = Auth::user()->id;
			$esp_quitar->save();

		}

	}

}

?>
