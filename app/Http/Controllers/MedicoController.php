<?php
namespace App\Http\Controllers;

use App\Models\Medico;
use View;
use App\Models\Establecimiento;
use App\Models\TituloProfesional;
use App\Models\EspecialidadMedica;
use App\Models\EspecialidadesMedico;
use Illuminate\Http\Request;
use DB;
use Log;
use Auth;

class MedicoController extends Controller {

	public function medico(){

		//$idEstablecimiento=Session::get("idEstablecimiento");
		//$idUsuario = Session::get("usuario");
		//
		$medicos = Medico::join("establecimientos","medico.establecimiento_medico", "=","establecimientos.id")
		->where("visible_medico", true)
		->where('establecimiento_medico',  Auth::user()->establecimiento)
		->get();
		return View::make("HoraTraumatologia/medico",["medicos"=>$medicos]);

	}

	public function crearMedicoPost(Request $request){
		DB::beginTransaction();
		try {
			$medico          = new Medico;
			$rut             = $request->input("rut");
			$dv              = (strtolower($request->input("dv")) == "k") ? 10 : $request->input("dv");
			$establecimiento = $request->input("estab");
			$nombre          = $request->input("nombre");
			$apellido        = $request->input("apellido");

			$establecimiento = 1;
			//return Input::all();
			
			$medico->rut_medico = $rut;
			$medico->dv_medico = $dv;
			$medico->establecimiento_medico = $establecimiento;
			$medico->nombre_medico = $nombre;
			$medico->apellido_medico = $apellido;
			$medico->visible_medico = true;

			$medico->save();
			DB::commit();
			return response()->json([
					"exito" => "Médico creado",
					"msg" => "Médico creado"
					]);
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al modificar el Médico", "msg" => $ex->getMessage()]);
		}
		
	}


	public function crearMedico(){

		$medico = new Medico;
		$estab = Establecimiento::getEstablecimientosSinTodos();
		return View::make("HoraTraumatologia/crearMedico", ["estab"=>$estab, "medico"=>$medico]);
		
	}
		
	public function actualizarMedico($id){

		$medico = Medico::where("id_medico","=",$id)->first();
		$estab = Establecimiento::getEstablecimientosSinTodos();
		return View::make("HoraTraumatologia/crearMedico", ["estab"=>$estab, "medico"=>$medico]);
		
	}


	public function actualizarMedicoPost($id, Request $request){
		try {
			DB::beginTransaction();
			$medico = Medico::where("id_medico","=",$id)->first();

			$rut             = $request->input("rut");
			$dv              = (strtolower($request->input("dv")) == "k") ? 10 : $request->input("dv");
			$establecimiento = 1;
			$nombre          = $request->input("nombre");
			$apellido        = $request->input("apellido");

			$medico->rut_medico = $rut;
			$medico->dv_medico = $dv;
			$medico->establecimiento_medico = $establecimiento;
			$medico->nombre_medico = $nombre;
			$medico->apellido_medico = $apellido;
			$medico->visible_medico = true;

			$medico->save();
			DB::commit();
			return response()->json([
				"exito" => "Médico modificado",
				"msg" => "Médico modificado"
				]);
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al modificar el Médico", "msg" => $ex->getMessage()]);
		}
		


	}

	public function existeMedico(Request $request){

		try{


			$rut=(int)$request->input("rut");
			$esValidoElRut = $request->input("esValidoElRut");
			$medicoGet = $request->input("medico");
			//return $medicoGet;

			$medico=Medico::where("rut_medico", $rut)
				->where('establecimiento_medico',  Auth::user()->establecimiento)
				->where("visible_medico",true)->first();
			//return $medicoGet;
			//return $medico;
			if($medicoGet == 0) // validacion cuando se va a crear
			{
				if($esValidoElRut =='false'){
				return response()->json(["valid" => false, "message"=>"<a href=''></a>"]);
				}
				elseif($medico==null){
				return response()->json(["valid" => true]);
				}
				else{
				return response()->json(["valid" => false, "message"=>"Médico ya existe"]);
				}
			}
			else{ //si medico se va a editar
				return response()->json(["valid" => true]);
			}

		}
		catch(Exception $e){
			return $e;
		}
			
			


	}


	public function eliminarMedico(Request $request){
		DB::beginTransaction();
		try {
			$idMedico = $request->input("idMedico");
			$medico=Medico::where("id_medico", $idMedico)->first();

			$medico->visible_medico = false;
			$medico->save();
			DB::commit();
			return response()->json(["exito" => "Médico eliminado","msg" => "Médico eliminado"]);
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al eliminar médico","msg" => "Error al eliminar médico"]);
		}


	}

	public function consulta_medicos($palabra)
		{
			/* $datos=DB::select(DB::raw(
			"
			SELECT
			cc10.nombre AS nombre_categoria,
			cc10.id_categoria_cie_10 AS id_categoria,
			FROM categoria_cie_10 AS cc10
			INNER JOIN cie_10 AS c10 ON cc10.id_categoria_cie_10=c10.id_categoria_cie_10
			WHERE  c10.visible=1
			AND c10.nombre ILIKE '%".$palabra."%'
			ORDER BY cc10.id_categoria_cie_10 ASC
			LIMIT 10
			"

			
		)); */
			$datos = DB::table("medico")
			->select(DB::raw("CONCAT(nombre_medico,' ',apellido_medico) AS nombre_apellido, rut_medico, id_medico, nombre_medico,apellido_medico, dv_medico"))
			->where('establecimiento_medico',  Auth::user()->establecimiento)
			->where(function($q) use ($palabra) {
				$q->where('nombre_medico', 'like', '%'.strtoupper($palabra).'%')
					->orWhere('apellido_medico', 'like', '%'.strtoupper($palabra).'%');
			})			
			//->where('visible_medico', '=', true)
			->orderBy('nombre_medico', 'asc')
			->limit(50)
			->get();
			return response()->json($datos);
	}

	public function consulta_medicos_completo($palabra)
	{
		Log::info($palabra);
		$datos = DB::table("medico")
		->leftJoin('establecimientos', 'medico.establecimiento_medico', '=', 'establecimientos.id')
		->select(DB::raw("CONCAT(medico.nombre_medico,' ',medico.apellido_medico) AS nombre_apellido, medico.rut_medico, medico.id_medico, medico.nombre_medico,medico.apellido_medico, medico.dv_medico , establecimientos.nombre AS nombre_establecimiento"))
		->where('establecimiento_medico',  Auth::user()->establecimiento)
		->where(function($q) use ($palabra) {
			$q->where('nombre_medico', 'like', '%'.strtoupper($palabra).'%')
				->orWhere('apellido_medico', 'like', '%'.strtoupper($palabra).'%');
		})			
		->orderBy('nombre_medico', 'asc')
		->limit(50)
		->get();
		return response()->json($datos);
}


	public function consulta_medicos_nombre($palabra){
			$datos = DB::table("medico")
			->select(DB::raw(" rut_medico, id_medico, nombre_medico,apellido_medico, dv_medico,especialidad "))
			->where('establecimiento_medico',  Auth::user()->establecimiento)
			->where(function($q) use ($palabra) {
				$q->where('nombre_medico', 'like', '%'.strtoupper($palabra).'%')
					->orWhere('apellido_medico', 'like', '%'.strtoupper($palabra).'%');
			})						
			//->where('visible_medico', '=', true)
			->orderBy('nombre_medico', 'asc')
			->limit(50)
			->get();
			foreach($datos as $key => $dato){
				$apellido_medico = null;
				if($dato){
					if($dato->apellido_medico){
						$apellido_medico = explode(" ", $dato->apellido_medico);
					}
				}

				if($apellido_medico){
					if(count($apellido_medico) > 1){
						$datos[$key]->primer_apellido = $apellido_medico[0];
						$datos[$key]->segundo_apellido = $apellido_medico[1];
					}else{
						$datos[$key]->primer_apellido = $datos->apellido_medico;
					}
				}
			}
			
			return response()->json($datos);
	}

	public function consulta_medicos_rut_completo($palabra)
		{
			$datos = DB::table("medico")
		->leftJoin('establecimientos', 'medico.establecimiento_medico', '=', 'establecimientos.id')
		->select(DB::raw("CONCAT(medico.nombre_medico,' ',medico.apellido_medico) AS nombre_apellido, medico.rut_medico, medico.id_medico, medico.nombre_medico,medico.apellido_medico, medico.dv_medico , establecimientos.nombre AS nombre_establecimiento,medico.direccion,medico.ciudad"))
		->where('establecimiento_medico',  Auth::user()->establecimiento)
		->where(function($q) use ($palabra) {
			$q->where('rut_medico', '=', $palabra);
		})			
		->first();
		return response()->json($datos);

	}
	
	public function consulta_medicos_rut($palabra)
		{
			$datos = DB::table("medico")
			->select(DB::raw("nombre_medico, id_medico, apellido_medico, especialidad"))
			->where('rut_medico', $palabra)
			->where('establecimiento_medico',  Auth::user()->establecimiento)
			->first();
			$apellido_medico = null;
			if($datos){
				if($datos->apellido_medico){
					$apellido_medico = explode(" ", $datos->apellido_medico);
				}
			}

			if($apellido_medico){
				if(count($apellido_medico) > 1){
					$datos->primer_apellido = $apellido_medico[0];
					$datos->segundo_apellido = $apellido_medico[1];
				}else{
					$datos->primer_apellido = $datos->apellido_medico;
				}
			}
			
			
			return response()->json($datos);
	}

	public function indexMedicos(){
		//$medicos = Medico::all();
		$medicosHabilitados = Medico::infoMedicoHabilitados();
		$medicosDeshabilitados = Medico::infoMedicoDeshabilitados();
		$establecimientos = Establecimiento::getEstablecimientosSinTodos();
		$tituloProfesionales = TituloProfesional::getTituloProfesionales();
		$especialidadMedica = EspecialidadMedica::getEspecialidadesMedicas();
		
			return view::make("Medicos/IndexMedicos", [
				"medicos" => $medicosHabilitados,
				"establecimientos" => $establecimientos,
				"deshabilitados" => $medicosDeshabilitados,
				"tituloProfesionales" => $tituloProfesionales,
				"especialidadMedica" => $especialidadMedica
			]);
	}

	public function deshabilitarMedico(Request $request){
			try {
				DB::beginTransaction();
				$id_medico = $request->input("id_medico");
				$medico = Medico::find($id_medico);
				$medico->visible_medico = false;
				$medico->save();
				DB::commit();
				return response()->json(["exito" => "El médico ha sido deshabilitado"]);
			} catch (Exception $ex) {
				DB::rollBack();
				return response()->json(["error" => "Error al deshabilitar al médico", "msg" => $ex->getMessage()]);
			}
	}

	public function habilitarMedico(Request $request){
			try {
				DB::beginTransaction();
				$id_medico = $request->input("id_medico");
				$medico = Medico::find($id_medico);
				$medico->visible_medico = true;
				$medico->save();
				DB::commit();
				return response()->json(["exito" => "El médico ha sido habilitado"]);
			} catch (Exception $ex) {
				DB::rollBack();
				return response()->json(["error" => "Error al habilitar al médico", "msg" => $ex->getMessage()]);
			}
	}

	public function cargarEspecialidades($id_medico){
		$especialidades = EspecialidadesMedico::where("id_medico", $id_medico)
												->where("visible", true)
												->get();
		$especialidadesArray = [];

		foreach($especialidades as $especialidad){
			$especialidadesArray []= $especialidad["cod_especialidad"];
		}

		return $especialidadesArray;
	}

	public function editarMedico($id_medico){

			$establecimientos = Establecimiento::getEstablecimientosSinTodos();
			$medico = Medico::find($id_medico);
			$especialidadMedico = $this->cargarEspecialidades($id_medico);

			$tituloProfesionales = TituloProfesional::getTituloProfesionales();
			$especialidadMedica = EspecialidadMedica::getEspecialidadesMedicas();
			return view::make("Medicos/EditarMedico", [
				"medico" => $medico,
				"especialidadMedico" => $especialidadMedico,
				"tituloProfesionales" => $tituloProfesionales,
				"especialidadMedica" => $especialidadMedica,
				"establecimientos" => $establecimientos
			]);

	}

	public function actualizarDatosMedico(Request $request){
		try {
			log::info($request);
			DB::beginTransaction();
			//verifica que el titulo no venga vacio
			if($request->titulo == '' && $request->especialidad == ''){
				return response()->json(["error" => "Debe ingresar titulo profesional y especialidad"]);
			}else{
				if($request->titulo == ''){
					return response()->json(["error" => "Debe ingresar titulo profesional"]);
				}
				
				if($request->especialidad == ''){
					return response()->json(["error" => "Debe ingresar especialidad"]);
				}
			}
		

			$tituloProfesional = TituloProfesional::getTituloProfesional($request->titulo);
			if(empty($tituloProfesional)){
				return response()->json(["error" => "Error, al ingresar titulo profesional"]);
			}
			
			//verifica que la especialidad no venga vacio
			$especialidadMedica = EspecialidadMedica::getEspecialidadesMedicasArray($request->especialidad);
			if(empty($especialidadMedica)){
				return response()->json(["error" => "Error, al ingresar titulo profesional"]);
			}
			
			//convertir especialidad a string
			$especialidadMedicaIndice = []; 
			$especialidadArray = [];
			foreach($especialidadMedica as $especialidad){
				$especialidadArray[]= $especialidad->nombre;
				$especialidadMedicaIndice[]= $especialidad->id;
			}
			$especialidadString = implode(",", $especialidadArray);

			$visible = NULL;
			log::info($request->visible_medico);
			if($request->visible_medico == 1){
				$visible = true;	
			}elseif($request->visible_medico == 0){
				$visible = false;	
			}else{
				return response()->json(["error" => "Error, al ingresar el tipo de visibilidad"]);
			}
			
			$medico = Medico::find($request->id_medico);
			$medico->rut_medico = $request->rut_medico;
			$medico->dv_medico = ($request->dv_medico == "k" || $request->dv_medico == "K") ? 10 : $request->dv_medico;
			$medico->visible_medico = ($request->visible_medico == 1) ? 1 : 0;
			$medico->establecimiento_medico = (int)$request->establecimiento_medico;
			$medico->nombre_medico = strtoupper($request->nombre_medico);
			$medico->apellido_medico = strtoupper($request->apellido_medico);
			$medico->visible_medico = $visible;
			$medico->especialidad = ($especialidadString == "") ? null : strtoupper($especialidadString);
			$medico->celular = ($request->celular == "") ? null : $request->celular;
			$medico->correo = ($request->email == "") ? null : $request->email;
			$medico->cod_titulo = ($request->titulo == "") ? null : strtoupper($request->titulo);
			$medico->titulo = ($tituloProfesional == "") ? null : strtoupper($tituloProfesional->nombre);
			$medico->save();
			
			
			//especialidades
			$especialidadMedicoCargada = $this->cargarEspecialidades($request->id_medico);			
			$especialidadesBorradas = array_diff($especialidadMedicoCargada, $especialidadMedicaIndice); //especialidades borradas
			foreach($especialidadesBorradas as $borradas){
				$especialidadesMedico = EspecialidadesMedico::where('id_medico',$request->id_medico)
				->where('cod_especialidad',$borradas)
				->update(['visible'=>false]);
			}

			$especialidadesAgregadas = array_diff($especialidadMedicaIndice,$especialidadMedicoCargada); //especialidades agregadas
			$especialidadAgregar = EspecialidadMedica::getEspecialidadesMedicasArray($especialidadesAgregadas);


			foreach($especialidadAgregar as $agregada){
				$especialidadesMedico = new EspecialidadesMedico();
					$especialidadesMedico->id_usuario = Auth::user()->id;
					$especialidadesMedico->id_medico = $medico->id_medico;
					$especialidadesMedico->especialidad = $agregada->nombre;
					$especialidadesMedico->visible = true;
					$especialidadesMedico->cod_especialidad = $agregada->codigo;
					$especialidadesMedico->save();
			}

			DB::commit();
			return response()->json(["exito" => "El Médico ha sido modificado"]);
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al modificar el Médico", "msg" => $ex->getMessage()]);
		}
	}

	public function registrarMedico(Request $request){
		try {

			if($request->titulo == '' && $request->especialidad == ''){
				return response()->json(["error" => "Debe ingresar titulo profesional y especialidad"]);
			}else{
				if($request->titulo == ''){
					return response()->json(["error" => "Debe ingresar titulo profesional"]);
				}
				
				if($request->especialidad == ''){
					return response()->json(["error" => "Debe ingresar especialidad"]);
				}
			}
		
			$repetido = Medico::select('rut_medico')->where("rut_medico","=",$request->rut_medico)->first();
			if($repetido){
				return response()->json(["error" => "Error, el médico ya esta registrado"]);
			}

			//verifica que el titulo no venga vacio
			$tituloProfesional = TituloProfesional::getTituloProfesional($request->titulo);
			if(empty($tituloProfesional)){
				return response()->json(["error" => "Error, al ingresar titulo profesional"]);
			}
			
			//verifica que la especialidad no venga vacio
			$especialidadMedica = EspecialidadMedica::getEspecialidadesMedicasArray($request->especialidad);
			if(empty($especialidadMedica)){
				return response()->json(["error" => "Error, al ingresar titulo profesional"]);
			}
			
			//convertir especialidad a string
			$especialidadArray = [];
			foreach($especialidadMedica as $especialidad){
				$especialidadArray[]= $especialidad->nombre;
			}
			$especialidadString = implode(",", $especialidadArray);

			$visible = NULL;
			if($request->visible == 1){
				$visible = true;	
			}elseif($request->visible == 0){
				$visible = false;	
			}else{
				return response()->json(["error" => "Error, al ingresar el tipo de visibilidad"]);
			}
			
				DB::beginTransaction();
				$medico = new Medico;
				$medico->rut_medico = $request->rut_medico;
				$medico->dv_medico = ($request->dv_medico == "k" || $request->dv_medico == "K") ? 10 : $request->dv_medico;
				$medico->visible_medico = ($request->visible_medico == 1) ? 1 : 0;
				$medico->establecimiento_medico = (int)$request->establecimiento_medico;
				$medico->nombre_medico = strtoupper($request->nombre_medico);
				$medico->apellido_medico = strtoupper($request->apellido_medico);
				$medico->visible_medico = $visible;
				$medico->especialidad = ($especialidadString == "") ? null : strtoupper($especialidadString);
				$medico->celular = ($request->celular == "") ? null : $request->celular;
				$medico->correo = ($request->email == "") ? null : $request->email;
				$medico->cod_titulo = ($request->titulo == "") ? null : strtoupper($request->titulo);
				$medico->titulo = ($tituloProfesional == "") ? null : strtoupper($tituloProfesional->nombre);
				$medico->save();

				foreach($especialidadMedica as $especialidad){
					$especialidadesMedico = new EspecialidadesMedico();
					$especialidadesMedico->id_usuario = Auth::user()->id;
					$especialidadesMedico->id_medico = $medico->id_medico;
					$especialidadesMedico->especialidad = $especialidad->nombre;
					$especialidadesMedico->visible = true;
					$especialidadesMedico->cod_especialidad = $especialidad->codigo;
					$especialidadesMedico->save();
				}



				DB::commit();
			    return response()->json(["exito" => "El Médico ha sido creado exitosamente"]);
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al modificar el Médico", "msg" => $ex->getMessage()]);
		}
	}
}
