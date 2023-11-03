<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caso;
use Log;
use DB;
use Auth;
use Response;
use Session;
use Carbon\Carbon;
use Consultas;
use View;
use PDF;
use App\Models\UsoRestringido;
use App\Models\UsoRestringidocultivo;
use App\Models\UsoRestringidotratamiento;
use App\Models\HistorialDiagnostico;
use App\Models\Establecimiento;
use App\Models\Paciente;

class UsoRestringidoController extends Controller
{
    public function agregarUsoRestringido(Request $request){
        try {
            DB::beginTransaction();

			if($request->idCaso == ''){
				return response()->json(["error" => "Error al ingresar"]);
			}

			$idCaso = base64_decode($request->idCaso);
			$idAnterior = '';

			$nuevousoRestringido = new UsoRestringido;

			//verifica si el formulario viene con id no vacia sino la modifica
			if(isset($request->id_formulario_uso_restringido) &&  $request->id_formulario_uso_restringido != ''){

				
                $usoRestringido = UsoRestringido::where('id',$request->id_formulario_uso_restringido)
                        ->where('visible', true)
                        ->first();
						
                if (empty($usoRestringido)) {
                    //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }

                $usoRestringido->usuario_modifica = Auth::user()->id;
                $usoRestringido->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $usoRestringido->visible = false;
                $usoRestringido->save();
				
				$idAnterior = $usoRestringido->id;
                $nuevousoRestringido->id_anterior = $idAnterior;


            }

			$nuevousoRestringido->tipo_tratamiento = $request->tipo_tratamiento;
			//Mientras tanto se quedara como comentada esta seccion debido a que es una funcion mas complicada de lo que parece
			/* if($request->editarUsoRestringido != ''){
				$nuevousoRestringido->tipo_tratamiento = 3;
			}else{
				$nuevousoRestringido->tipo_tratamiento = $request->tipo_tratamiento;
			} */

			$nuevousoRestringido->id_diagnostico = $request->id_diagnostico;
			

			if($request->terapia_empirica_especifica == 'terapia_empirica'){
				$nuevousoRestringido->terapia_especifica_empirica = 'terapia_empirica';
				$nuevousoRestringido->sitio_infeccion = $request->sitio_infeccion;

			}elseif($request->terapia_empirica_especifica == 'terapia_especifica'){
				$nuevousoRestringido->terapia_especifica_empirica = 'terapia_especifica';
				$nuevousoRestringido->patogeno = $request->patogeno;
			}
			
			if($request->iaas == 'si'){
				$nuevousoRestringido->sospecha_iaas = true;
			}else{
				$nuevousoRestringido->sospecha_iaas = false;
			}
			
			
			$nuevousoRestringido->temperatura_justificacion = $request->justificacion_temperatura;
			$nuevousoRestringido->parametro_infeccioso_justificacion = $request->justificacion_parametro;
			$nuevousoRestringido->estado_clinico_justificacion = $request->justificacion_estado;
			$nuevousoRestringido->comentario_justificacion = $request->justificacion_comentario;
			$nuevousoRestringido->caso = $idCaso;
			$nuevousoRestringido->usuario = Auth::user()->id;
			$nuevousoRestringido->visible = true;
			$nuevousoRestringido->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");

			$nuevousoRestringido->save();


			//verifica que los datos eliminados existan y que no vengan vacios
			if(isset($request->eliminados_cultivos) && $request->eliminados_cultivos != ''){
				$eliminados_cultivos = preg_replace('/,/', '',  $request->eliminados_cultivos, 1);
				foreach (explode( ',', $eliminados_cultivos ) as $key => $eliminados) {
					$delete = UsoRestringidocultivo::where('id',$eliminados)
						->where('visible', true)
						->first();

					$delete->usuario_modifica = Auth::user()->id;
					$delete->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
					$delete->visible = false;
					$delete->save();
				}
			}
			if($request->terapia_empirica_especifica == 'terapia_especifica'){	

				if(isset($request->id_formulario_uso_restringido) &&  $request->id_formulario_uso_restringido != ''){
					UsoRestringidotratamiento::where('id_uso_restringido',$request->id_formulario_uso_restringido)
					->update(['usuario_modifica'=> Auth::user()->id,
					'fecha_modificacion'=>Carbon::now()->format("Y-m-d H:i:s"),
					'visible'=>false
					]);
				}

			//recorre los datos de cultivo
			foreach ($request->fechaCultivo as $key => $fechaCultivo) {
				$son_diferentes_cultivo = true;
				$id_anterior_cultivo = '';

				//busca por la posicion si no viene vacio la id de cultivo
				if(isset($request->id_cultivo[$key])  &&  $request->id_cultivo[$key] != ''){

					//busca los datos para ver si no fueron modificados con anterioridad
					$cultivo = UsoRestringidocultivo::where('id',$request->id_cultivo[$key])
							->where('visible', true)
							->first();
	
					if (empty($cultivo)) {
						//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
						return response()->json(array("info" => "Este formulario ya ha sido modificado"));
					}
					
					//verifica si 1 de los datos es diferentes y asi poder modificarlo
					if($cultivo->fecha_cultivo != Carbon::parse($request->fechaCultivo[$key])->format('Y-m-d H:i:s')  || $cultivo->agente_cultivo != $request->antibioticoCultivo[$key] || $cultivo->localizacion_cultivo != $request->locacionCultivo[$key] || $cultivo->id_uso_restringido != $request->idAnterior ){
						$cultivo->usuario_modifica = Auth::user()->id;
						$cultivo->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
						$cultivo->visible = false;
						$cultivo->save();
		
						$id_anterior_cultivo = $cultivo->id;
					}else{
						$son_diferentes_cultivo = false;
					}
	
				
				}
				//si 1 de los datos anteriores es diferentes crea una nueva tabla
				if($son_diferentes_cultivo){
					$nuevoCultivo = new UsoRestringidocultivo;
					if($id_anterior_cultivo != ''){
						$nuevoCultivo->id_anterior = $cultivo->id;
					}
					
					$nuevoCultivo->id_uso_restringido = $nuevousoRestringido->id;
					$nuevoCultivo->fecha_cultivo = strip_tags($request->fechaCultivo[$key]);
					$nuevoCultivo->agente_cultivo = strip_tags($request->antibioticoCultivo[$key]);
					$nuevoCultivo->localizacion_cultivo = strip_tags($request->locacionCultivo[$key]);
					$nuevoCultivo->usuario = Auth::user()->id;
					$nuevoCultivo->visible = true;
					$nuevoCultivo->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
					$nuevoCultivo->caso = $idCaso;
					$nuevoCultivo->save();
					
				}
				
			}

		}else{
				if(isset($request->id_formulario_uso_restringido) &&  $request->id_formulario_uso_restringido != ''){
				UsoRestringidocultivo::where('id_uso_restringido',$request->id_formulario_uso_restringido)
				->where('visible', true)
				->update(['usuario_modifica'=> Auth::user()->id,
				'fecha_modificacion'=>Carbon::now()->format("Y-m-d H:i:s"),
				'visible'=>false
				]);
			}
		}


		// //se elimina los datos que tienen id
		if(isset($request->eliminados_antimicrobiano) && $request->eliminados_antimicrobiano != ''){
			$eliminados_antimicrobiano = preg_replace('/,/', '',  $request->eliminados_antimicrobiano, 1);
			foreach (explode( ',', $eliminados_antimicrobiano ) as $key => $eliminados) {
				$delete = UsoRestringidotratamiento::where('id',$eliminados)
					->where('visible', true)
					->first();

				$delete->usuario_modifica = Auth::user()->id;
				$delete->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
				$delete->visible = false;
				$delete->save();
			}
		}

			
		//recorre los datos de tratamiento
		foreach ($request->antimicrobiano as $key => $antimicrobiano) {
			$son_diferentes_tratamiento = true;
			$id_anterior_tratamiento = '';
			
			//busca por la posicion si no viene vacio la id de cultivo
			if(isset($request->id_antimicrobiano[$key])  &&  $request->id_antimicrobiano[$key] != ''){

				//busca los datos para ver si no fueron modificados con anterioridad
				$tratamiento = UsoRestringidotratamiento::where('id',$request->id_antimicrobiano[$key])
						->where('visible', true)
						->first();
						
				//Si se encuentra un tratamiento antimicrobiano  asociado al id, significa que  si es 
				if (!empty($tratamiento)) {
					//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
					//verifica si 1 de los datos es diferentes y asi poder modificarlo
					if($tratamiento->antimicrobiano_tratamiento != $request->antimicrobiano[$key]  
						|| $tratamiento->dosis_tratamiento != $request->dosisAntimicrobiano[$key] 
						|| $tratamiento->posologia_tratamiento != $request->posologiantimicrobiano[$key] 
						|| $tratamiento->duracion_tratamiento != $request->duracionAntimicrobiano[$key] 
						|| $tratamiento->id_uso_restringido != $nuevousoRestringido->id
					){
						//Si alguno de los datos es distinto significa  que son distintos tratamientos
						$tratamiento->usuario_modifica = Auth::user()->id;
						$tratamiento->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
						$tratamiento->visible = false;
						$tratamiento->save();
		
						$id_anterior_tratamiento = $tratamiento->id;
					}else{
						$son_diferentes_tratamiento = false;
					}
				}
				//si pasa de esto significa que son disntintos tratamiento			
			}
			//si 1 de los datos anteriores es diferentes crea una nueva tabla
			if($son_diferentes_tratamiento){
				$nuevotratamiento = new UsoRestringidotratamiento;
				if($id_anterior_tratamiento != ''){
					$nuevotratamiento->id_anterior = $tratamiento->id;
				}
				$nuevotratamiento->id_uso_restringido = $nuevousoRestringido->id;
				$nuevotratamiento->antimicrobiano_tratamiento = strip_tags($request->antimicrobiano[$key]);
				$nuevotratamiento->dosis_tratamiento = strip_tags($request->dosisAntimicrobiano[$key]);
				$nuevotratamiento->posologia_tratamiento = strip_tags($request->posologiantimicrobiano[$key]);
				$nuevotratamiento->duracion_tratamiento = strip_tags($request->duracionAntimicrobiano[$key]);
				$nuevotratamiento->usuario = Auth::user()->id;
				$nuevotratamiento->visible = true;
				$nuevotratamiento->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
				$nuevotratamiento->tipo_formulario = "tratamiento_anterior";
				$nuevotratamiento->caso = $idCaso;
				$nuevotratamiento->save();
			}
		}

		// //se elimina los datos que tienen id
		if(isset($request->eliminados_antimicrobiano_actual) && $request->eliminados_antimicrobiano_actual != ''){
			$eliminados_antimicrobiano_actual = preg_replace('/,/', '',  $request->eliminados_antimicrobiano_actual, 1);
			foreach (explode( ',', $eliminados_antimicrobiano_actual ) as $key => $eliminados_actual) {
				$delete_actual = UsoRestringidotratamiento::where('id',$eliminados_actual)
					->where('visible', true)
					->first();

				$delete_actual->usuario_modifica = Auth::user()->id;
				$delete_actual->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
				$delete_actual->visible = false;
				$delete_actual->save();
			}
		}


		if($request->tipo_tratamiento == '2' || $request->tipo_tratamiento == '3'){
			//recorre los datos de tratamiento actual
			foreach ($request->antimicrobiano_actual as $key => $antimicrobiano_actual) {
				$son_diferentes_tratamiento_actual = true;
				$id_anterior_tratamiento_actual = '';
				
				//busca por la posicion si no viene vacio la id de cultivo
				if(isset($request->id_antimicrobianoActual[$key])  &&  $request->id_antimicrobianoActual[$key] != ''){

					//busca los datos para ver si no fueron modificados con anterioridad
					$tratamiento_actual = UsoRestringidotratamiento::where('id',$request->id_antimicrobianoActual[$key])
							->where('visible', true)
							->first();
	
					if (empty($tratamiento_actual)) {
						//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
						return response()->json(array("info" => "Este formulario ya ha sido modificado"));
					}
					
					//verifica si 1 de los datos es diferentes y asi poder modificarlo
					if($tratamiento_actual->antimicrobiano_tratamiento != $request->antimicrobiano_actual[$key]  || $tratamiento_actual->dosis_tratamiento != $request->dosisAntimicrobiano_actual[$key] || $tratamiento_actual->posologia_tratamiento != $request->posologiantimicrobiano_actual[$key] || $tratamiento_actual->duracion_tratamiento != $request->duracionAntimicrobiano_actual[$key] ||  $tratamiento_actual->id_uso_restringido[$key] != $nuevousoRestringido->id){
						$tratamiento_actual->usuario_modifica = Auth::user()->id;
						$tratamiento_actual->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
						$tratamiento_actual->visible = false;
						$tratamiento_actual->save();
		
						$id_anterior_tratamiento_actual = $tratamiento->id;
					}else{
						$son_diferentes_tratamiento_actual = false;
					}

				
				}
				//si 1 de los datos anteriores es diferentes crea una nueva tabla
				if($son_diferentes_tratamiento_actual){
					if($request->dosisAntimicrobiano_actual[$key] != '' && $request->posologiantimicrobiano_actual[$key] != '' &&  $request->duracionAntimicrobiano_actual[$key] != ''){
						$nuevotratamiento_actual = new UsoRestringidotratamiento;
						if($id_anterior_tratamiento_actual != ''){
							$nuevotratamiento_actual->id_anterior = $tratamiento->id;
						}
						$nuevotratamiento_actual->id_uso_restringido = $nuevousoRestringido->id;
						$nuevotratamiento_actual->antimicrobiano_tratamiento = strip_tags($request->antimicrobiano_actual[$key]);
						$nuevotratamiento_actual->dosis_tratamiento = strip_tags($request->dosisAntimicrobiano_actual[$key]);
						$nuevotratamiento_actual->posologia_tratamiento = strip_tags($request->posologiantimicrobiano_actual[$key]);
						$nuevotratamiento_actual->duracion_tratamiento = strip_tags($request->duracionAntimicrobiano_actual[$key]);
						$nuevotratamiento_actual->usuario = Auth::user()->id;
						$nuevotratamiento_actual->visible = true;
						$nuevotratamiento_actual->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
						$nuevotratamiento_actual->tipo_formulario = "tratamiento_actual";
						$nuevotratamiento_actual->caso = $idCaso;
						$nuevotratamiento_actual->save();
					}
				}
			}
		}

			


			DB::commit();
			return response()->json(array("exito" => 'Se ha ingresado correctamente'));
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al ingresar"]);
        }
    
    }


	public function historialUsoRestringido($caso){
        $historial = DB::table("formulario_uso_restringido")
        ->where("caso",$caso)
        ->orderBy("fecha_creacion", "asc")
        ->where("visible",true)
        ->get();

        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return view::make("Gestion/gestionEnfermeria/historialusoRestringido")
        ->with(array(
            "caso" => $caso,
            "hist" => $historial,
            "paciente" => $nombreCompleto));
    }


	public function buscarHistorialUsoRestringido($idCaso){
        $response = [];
		$idCaso = base64_decode($idCaso);

        $data = UsoRestringido::dataHistorialUsoRestringido($idCaso);
        $response = $data;
        // return response()->json($response);
		return response()->json(["aaData" => $response]);
    }

	public function edit($id)
    {
		/* Validar formulario */
		if(isset($id) &&  $id != ''){
			$usoRestringido = UsoRestringido::where('id',$id)
					->where('visible', true)
					->first();
					
			if (empty($usoRestringido)) {
				//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
				return response()->json(array("info" => "Este formulario ya ha sido modificado"));
			}
		}

		$datos = UsoRestringido::find($id);
		$datosCultivo = '';
		$tratamientoAnterior = '';
		$diagnostico = '';
		if(!empty($datos)){
			$diagnostico = HistorialDiagnostico::where("caso",$datos->caso)->where("id",$datos->id_diagnostico)->orderBy("fecha","desc")->first();
			if($datos->terapia_especifica_empirica == "terapia_especifica"){
				$datosCultivo =  UsoRestringidocultivo::where("id_uso_restringido",$datos->id)->where('visible',true)->get();
			}
			$tratamientoAnterior =  UsoRestringidotratamiento::where("id_uso_restringido",$datos->id)->where('tipo_formulario','tratamiento_anterior')->where('visible',true)->orderBy('fecha_creacion', 'asc')->get();
			
			$tratamientoActual =  UsoRestringidotratamiento::where("id_uso_restringido",$datos->id)->where('tipo_formulario','tratamiento_actual')->where('visible',true)->orderBy('fecha_creacion', 'asc')->get();
		}
        return response()->json(["datos" => $datos,"diagnostico" => $diagnostico,"datosCultivo" => $datosCultivo,"tratamientoAnterior" => $tratamientoAnterior,"tratamientoActual" => $tratamientoActual]);
    }

	public function ultimoDiagnostico($idCaso)
    {
		$idCaso = base64_decode($idCaso);
		$diagnostico = HistorialDiagnostico::where("caso",$idCaso)->orderBy("fecha","desc")->first();
        return response()->json(["diagnostico" => $diagnostico]);
    }

	public function consulta_antimicrobiano($palabra){
        $datos = DB::table("tratamiento_antimicrobiano")
            ->select(DB::raw("nombre, id"))
            ->where('nombre', 'ilike', '%'.strtoupper($palabra).'%')
            ->orderBy('nombre', 'asc')
            ->limit(100)
            ->get();

        return response()->json($datos);
    }
     
}