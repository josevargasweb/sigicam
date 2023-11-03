<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroVisitas;
use Log;
use DB;

class RegistroVisitasController extends Controller{
	
	public function guardar(Request $request){
		try{
			$n_id = $request->n_identificacion_acompanante;
			$request->n_identificacion_acompanante = preg_replace("/[.-]/","",$n_id);
			Log::info($request);
			$existeRegistro = RegistroVisitas::where("caso","=",$request->id_caso)
			->where("id_paciente","=",$request->id_paciente)
			->where("id_unidad","=",$request->id_servicio)
			->where("id_sala","=",$request->id_sala)
			->where("id_cama","=",$request->id_cama)
			->whereNull('fecha_salida_visita')
			->first();
			Log::info($existeRegistro);
			Log::info(empty($existeRegistro));
			if(empty($existeRegistro)){
				$rv = new RegistroVisitas();
				$rv->guardar($request);
				return response()->json(["exito" => true]);
			}else{
				return response()->json(["info" => "Tiene una visita sin finalizar"]);
			}
		}catch(\Exception $e){
			return response()->json(["exito" => false]);
			dd($e->getMessage());
		}
	}
	public function buscarCaso(Request $request)
	{ \Log::info($request);
		try{
			$rv = new RegistroVisitas();			
			$datos = $rv->buscarCaso($request);
			if(!$datos){
				return response()->json(["exito" => false,"msg" => "El paciente no se encuentra hospitalizado."]);
			}
			if(!$rv->visitasPermitidas($datos->id_caso)){
				return response()->json(["exito" => false,"msg" => "El paciente ha alcanzado el máximo de visitas permitidas."]);
			}
			
			return response()->json($datos);
		}catch(\Exception $e){
			dd([$e->getMessage()]);
		}
		
	}
	public function buscarCasoPorNombre(Request $request)
	{
		try{
			$rv = new RegistroVisitas();			
			$datos = $rv->buscarCasoPorNombre($request);
			if(!$datos){
				return response()->json(["exito" => false,"msg" => "El paciente no se encuentra hospitalizado."]);
			}
			return response()->json($datos);
		}catch(\Exception $e){
			dd([$e->getMessage()]);
		}
		
	}
	public function buscarVisita(Request $request){
		try{
			$n_id = $request->n_identificacion;
			$request->n_identificacion = preg_replace("/[.-]/","",$n_id);
			$rv = new RegistroVisitas();
			$datos = $rv->buscarVisita($request);
			return response()->json($datos);
		}catch(\Exception $e){
			return response()->json(["exito" => false]);
		}
	}
	public function salidaVisita(Request $request){
		try{
			if(!$request->id_registro){
				return response()->json(["exito" => false]);
			}
			$rv = new RegistroVisitas();
			$rv->guardarSalida($request);
			return response()->json(["exito" => true]);
		} catch (\Exception $ex) {
			return response()->json(["exito" => false]);
		}
	}
	public function vista(){
		return view("Visitas.registroVisitas");
	}
	public function validarRutAcompanante(Request $request)
	{
		Log::info($request);
		try{
			$n_id = $request->n_identificacion_acompanante;
			$n_identificacion_acompanante = preg_replace("/[.-]/","",$n_id);
			$existeVisita = RegistroVisitas::select(DB::raw("CONCAT(pacientes.nombre,' ', pacientes.apellido_paterno) as paciente"),"registro_visitas.nombre as visitante")
			->leftJoin('pacientes', 'pacientes.id', '=', 'registro_visitas.id_paciente')
			->where('registro_visitas.n_identificacion',$n_identificacion_acompanante)
			->whereNull('registro_visitas.fecha_salida_visita')
			->first();
			if($existeVisita && $existeVisita != ''){
				return response()->json(["informacion" => "El/La visitante $existeVisita->visitante se encuentra acompañando al paciente $existeVisita->paciente"]);
			}
			return response()->json(["exito" => true]);
		}catch(\Exception $e){
			return response()->json(["error" => "Existe un error al comprobar la identificacion del visitante"]);

		}
		
	}
}
