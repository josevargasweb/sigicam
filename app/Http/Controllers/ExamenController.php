<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\ArchivoFormulario;
use App\Models\Examen;
use App\Models\Caso;
use App\Models\THistorialOcupaciones;
use App\Models\HistorialDiagnostico;


use Session;
use DB;
use Funciones;
use Log; 
use Auth;
use Carbon\Carbon;

class ExamenController extends Controller
{


    public function cantidadExamenPendiente(Request $request){

        $idCaso = $request->idCaso;
        $cantExamen = Examen::where("caso","=",$idCaso)->where("pendiente","=","TRUE")->where("visible",true)->count();

        $historial_caso = THistorialOcupaciones::where('caso', $idCaso)->orderBy("fecha", "desc")->first();
        $altaSinLiberarCama = ($historial_caso->fecha_alta != null || $historial_caso->fecha_alta != "") ? true : false;
        
        return response()->json(array("cantidad"=>$cantExamen, "altaSinLiberar" => $altaSinLiberarCama));
    }

    public function obtenerListaEstudios(Request $request){
		$casos = Caso::select('casos.id as id_caso', 'pacientes.rut', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno', 'pacientes.dv', 'pacientes.fecha_nacimiento', 'pacientes.id as paciente_id')
			->distinct("casos.id")
			->join("examenes", "examenes.caso", "=", "casos.id")
			->join("pacientes", "pacientes.id", "=", "casos.paciente")
			->where("examenes.pendiente", true)
			->where("examenes.visible", true)
			->whereNull("casos.fecha_termino")
			->get();

		$resultado = [];
		foreach ($casos as $caso) {
			$diagnostico = HistorialDiagnostico::select("diagnostico")
				->where("caso", $caso->id_caso)
				->orderby("fecha", "desc")
				->first();
			$ultimo_examen = Examen::select("tipo", "fecha", "pendiente","visible")
                ->where("caso", $caso->id_caso)
                ->where("visible",true)
                ->where("pendiente",true)
				->orderby("fecha", "desc")
                ->first();

            $mayor_tiempo = Examen::select("tipo", "fecha", "pendiente","visible")
                ->where("caso", $caso->id_caso)
                ->where("visible",true)
                ->where("pendiente",true)
				->orderby("fecha", "asc")
                ->first();
                
			$fecha_caso = Caso::select("fecha_ingreso")
				->where("id",$caso->id_caso)
				->orderby("fecha_ingreso", "desc")
				->first()
                ->fecha_ingreso; 
                
			/* $dias_espera = Carbon::now()->diffInDays($fecha_caso); */

			$cama = DB::table('t_historial_ocupaciones as t')
				->join("camas as c", "c.id", "=", "t.cama")
				->join("salas as s", "c.sala", "=", "s.id")
				->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
				->join("area_funcional AS af", "uee.id_area_funcional", "=", "af.id_area_funcional")
				->where("caso", $caso->id_caso)
				->select("uee.alias as nombre_unidad",  "af.nombre as nombre_area_funcional")
				->first();
			
			$examenes_pendientes = Examen::where('pendiente', '=', true)
                ->where('caso', '=', $caso->id_caso)
                ->where('pendiente',true)
                ->where('visible',true)
                ->count();

            $dia = (Carbon::now()->diffInDays($ultimo_examen->fecha) == 1) ?"día":"días";
			$resultado[] = [
				"rut" => $caso->rut,
				"nombreCompleto" => $caso->nombre. " " .$caso->apellido_paterno. " " .$caso->apellido_materno,
				"dv" => ($caso->dv == 10) ? "K" : $caso->dv,
				"diagnostico" => $diagnostico,
				"fecha_caso" => $fecha_caso,
				/* "servicio" => $cama->nombre_unidad, */
				"areaYservicio" => $cama->nombre_area_funcional." - <b>".$cama->nombre_unidad."</b>",
				"paciente_id" => $caso->paciente_id,
				"id_caso" => $caso->id_caso,
				"ultimo" => $ultimo_examen->tipo." (<b>".$ultimo_examen->fecha."</b>)",
				"mayor_tiempo" => $mayor_tiempo->tipo." (<b>".$mayor_tiempo->fecha."</b>)"."<br><h5><span class='label label-danger'>".Carbon::now()->diffInDays($ultimo_examen->fecha)." $dia</span></h5>",
				/* "dias_espera" => $dias_espera, */
				"cant_pendiente" => "<div class='text-center'>".$examenes_pendientes."</div>"
			];
		}

		return ["data" => $resultado];

	}

    public function sacarListaEEP(Request $request){

        try{
            DB::beginTransaction();

            $examenes = Examen::where("caso","=",$request->id)->where("pendiente","=","TRUE")->where("visible",true)->update([
                'visible' => false,
                'usuario_modifica' => Auth::user()->id,
                'tipo_modificacion' => 'Sacado',
                'updated_at' => Carbon::now()
            ]);
            
            DB::commit();
            return response()->json(["exito" => "Examenes sacados de la lista de Exámenes / Estudios / Procedimientos"]);
            
        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al sacar de la lista de  Exámenes / Estudios / Procedimientos"]);
        }
    }
}