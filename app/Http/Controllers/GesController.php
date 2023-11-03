<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caso;
use App\Models\GesNotificacion;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Comuna;
use App\Models\RepresentanteGes;
use App\Models\Telefono;
use App\Models\HistorialDiagnostico;

use Log;
use DB;
use Auth;
use Response;
use Session;
use Carbon\Carbon;
use Consultas;
use App\Models\HospitalizacionDomiciliaria;

class GesController extends Controller
{

	public function agregarGes(Request $request){
		try {
			log::info($request);
			DB::beginTransaction();
			$caso = base64_decode($request->idCasoGes);
			$idanterior = '';
			$idanteriorRepresentante = '';

			//modifica al medico 
			$medico = Medico::where('id_medico',$request->id_medico_ges)
			->first();
			if(!$medico){
				return response()->json(array("info" => "Error al buscar al medico"));
			}
			
			//modifica al paciente
			$Pacientecaso = Caso::where('id',$caso)->first();
			$paciente = Paciente::where("id",$Pacientecaso->paciente)->first();

			if(!$paciente){
				return response()->json(array("info" => "Error al buscar al paciente"));
			}


			

			if($request->id_notificacion_ges != ''){

				$gesNotificacion = GesNotificacion::where('id',$request->id_notificacion_ges)
                    ->where('visible', true)
                    ->first();
				
				if (!$gesNotificacion) {
					//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
					return response()->json(array("info" => "Este formulario ya ha sido modificado"));
				}

				// $gesNotificacionDiagnostico = GesNotificacion::where('id',"<>",$request->id_notificacion_ges)
				// ->where('caso',$caso)
				// ->where('id_diagnostico_ges',$request->id_diagnostico_ges)
				// ->where('visible', true)
				// ->first();

				// if ($gesNotificacionDiagnostico) {
				// 	//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
				// 	return response()->json(array("info" => "ya existe un formulario con el diagnostico elegido"));
				// }

				$gesNotificacionDiagnostico = GesNotificacion::where('id',$request->id_notificacion_ges)
                    ->where('visible', true)
                    ->first();

				if($gesNotificacion->id_representante != null || $gesNotificacion->id_representante != ''){
					$representante = RepresentanteGes::where('id',$gesNotificacion->id_representante)
                    ->where('visible', true)
                    ->first();

					if (!$representante) {
						//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
						return response()->json(array("info" => "Este formulario ya ha sido modificado"));
					}

					$representante->usuario_modifica = Auth::user()->id;
					$representante->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
					$representante->visible = false;
					$representante->save();

					$idanteriorRepresentante = $representante->id;
				}

				$gesNotificacion->usuario_modifica = Auth::user()->id;
                $gesNotificacion->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $gesNotificacion->visible = false;
                $gesNotificacion->save();

				$idanterior = $gesNotificacion->id;
			}else{
				$gesNotificacionDiagnostico = GesNotificacion::where('caso',$caso)
				->where('id_diagnostico_ges',$request->id_diagnostico_ges)
				->where('visible', true)
				->first();

				if ($gesNotificacionDiagnostico) {
					//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
					return response()->json(array("info" => "ya existe un formulario con el diagnostico elegido"));
				}
			}

			//verifica que los datos eliminados existan y que no vengan vacios
			if(isset($request->telefono_eliminado_ges) && $request->telefono_eliminado_ges != ''){
				$telefono_eliminado_ges = preg_replace('/,/', '',  $request->telefono_eliminado_ges, 1);
				foreach (explode( ',', $telefono_eliminado_ges ) as $key => $eliminados) {
					$delete = Telefono::where('id',$eliminados)
					->first();
					$delete->delete();
				}
			}

			if(isset($request->tipo_telefono) && !empty($request->tipo_telefono) && isset($request->telefono) && !empty($request->telefono) ){
				//buscar si existen el telefono y lo modifica
				foreach ($request->tipo_telefono  as $key => $telfono) {
					Log::info('entra aca telefono');
					$telefono = "";
					if(!empty($request->telefono_id[$key])){
						$telefono = Telefono::where("id_paciente",$Pacientecaso->paciente)->where("tipo",$telfono)->where("id",$request->telefono_id[$key])->first();
					}
	
					if($telefono != "" && $telefono){
						$telefono->tipo = $telfono;
						$telefono->telefono = $request->telefono[$key];
						$telefono->save();
					}else{
						$nuevoTelefono = new Telefono();
						$nuevoTelefono->id_paciente = $paciente->id;
						$nuevoTelefono->tipo = $telfono;
						$nuevoTelefono->telefono = $request->telefono[$key];
						$nuevoTelefono->save();
					}
				}
			}

			$paciente->nombre = $request->paciente_nombre_ges;
			$paciente->apellido_paterno = $request->paciente_apellidoPat_ges;
			$paciente->apellido_materno = $request->paciente_apellidoMat_ges;
			$paciente->calle = $request->paciente_calle_ges;
			$paciente->numero = $request->paciente_numero_ges;
			$paciente->observacion = $request->paciente_observacion_ges;
			$paciente->id_comuna = $request->comuna;
			$paciente->correo = $request->paciente_correo_ges;
			$paciente->save();
	

			$nuevoRepresentanteId = "";
			if($request->nombre_representante_ges != "" && $request->rut_representante_ges != "" && $request->dv_representante_ges != ""){
				$nuevoRepresentante = new RepresentanteGes();
				$nuevoRepresentante->caso = $caso;
				$nuevoRepresentante->nombre_completo = $request->nombre_representante_ges;
				$nuevoRepresentante->rut = $request->rut_representante_ges;
				$nuevoRepresentante->dv = $request->dv_representante_ges;
				$nuevoRepresentante->telefono = $request->telefono_representante_ges;
				$nuevoRepresentante->correo = $request->correo_representante_ges;
				$nuevoRepresentante->usuario = Auth::user()->id;
				$nuevoRepresentante->visible = true;
				$nuevoRepresentante->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
				if($idanteriorRepresentante != ''){
					$nuevoRepresentante->id_anterior = $idanteriorRepresentante;
				}
				$nuevoRepresentante->save();
				$nuevoRepresentanteId = $nuevoRepresentante->id;
			}
			
			
			
			$nuevoGesNotificacion = new GesNotificacion();
			$nuevoGesNotificacion->caso = $caso;
			$nuevoGesNotificacion->id_medico = $request->id_medico_ges;
			$nuevoGesNotificacion->id_representante = ($nuevoRepresentanteId != "") ? $nuevoRepresentanteId:null;
			$nuevoGesNotificacion->confirmacion_diagnostico_ges = $request->paciente_antecedentes_ges;
			$nuevoGesNotificacion->confirmacion_tratamiento = $request->ant_conf;
			$nuevoGesNotificacion->fecha = Carbon::parse($request->fechaDiagGes)->format("Y-m-d H:i:s");
			// $nuevoGesNotificacion->hora = Carbon::parse($request->horaDiagGes)->format("H:i:s");
			$nuevoGesNotificacion->usuario = Auth::user()->id;
			$nuevoGesNotificacion->visible = true;
			$nuevoGesNotificacion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
			$nuevoGesNotificacion->id_diagnostico_ges = $request->id_diagnostico_ges;
			if($idanterior != ''){
				$nuevoGesNotificacion->id_anterior = $idanterior;
			}
			$nuevoGesNotificacion->save();



			
			DB::commit();
		 return response()->json(array("exito" => 'Se ha guardado correctamente'));
		}  catch (Exception $ex) {
			DB::rollBack();
			Log::info($ex);
			return response()->json(["error" => "Error al guardar los antecedentes del paciente"]);
		}
	}

	public function mostrarDiagnosticos($caso){
		try {
			$caso = base64_decode($caso);
			$historialDiagnostico = HistorialDiagnostico::select('diagnosticos.diagnostico','diagnosticos.id')
			->where('diagnosticos.caso',$caso)
			->whereNotExists(function($query)
                {
                    $query->select(DB::raw(1))
                          ->from('formulario_ges_notificacion')
                          ->whereRaw('diagnosticos.id = formulario_ges_notificacion.id_diagnostico_ges')
                          ->whereRaw('formulario_ges_notificacion.visible = true');
                })
			->get();
			
			return response()->json(array(
				"historialDiagnostico"=>$historialDiagnostico
			)); 
		}  catch (Exception $ex) {
			Log::info($ex);
			return response()->json(["error" => "Error al mostrar los diagnosticos"]);
		}
	}

	public function mostrarDiagnosticosGes($caso){

		$caso = base64_decode($caso);
		$notificaciones = GesNotificacion::select('formulario_ges_notificacion.id','formulario_ges_notificacion.fecha_creacion','formulario_ges_notificacion.fecha_modificacion','medico.nombre_medico','medico.apellido_medico','formulario_ges_notificacion.fecha','formulario_ges_notificacion.confirmacion_tratamiento','formulario_ges_notificacion.confirmacion_diagnostico_ges')
		->leftJoin('medico', 'medico.id_medico', '=', 'formulario_ges_notificacion.id_medico')
		->where('caso',$caso)->where('visible',true)
		->get();


		$resultado = [];

        foreach ($notificaciones as $key => $notificacion) {
			$fecha_modificacion = "No posee modificación";
			if($notificacion->fecha_modificacion != null){
				$fecha_modificacion = Carbon::parse($notificacion->fecha_modificacion)->format("d-m-Y H:i");
			}
			$usuario = "Usuario responsable:<b>". $notificacion->nombre_medico." ".$notificacion->apellido_medico."</b><br> Fecha creación: ".Carbon::parse($notificacion->fecha_creacion)->format("d-m-Y H:i")."</b><br> Fecha modificación: ".$fecha_modificacion;
			$confirmacion_tratamiento = "Fecha notificación: ".$notificacion->fecha."<br>"."<b>". $notificacion->confirmacion_tratamiento."</b>"."<br>"."Información medica: ".substr($notificacion->confirmacion_diagnostico_ges,0,30);

            $opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='modificar_notificacion(".$notificacion->id.")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='eliminar_notificacion(".$notificacion->id.")'>Eliminar</button>
            </div>";		
    
			$resultado [] = [
				$usuario,
				$confirmacion_tratamiento,
				$opciones
			];
			
		}

		return response()->json(["aaData" => $resultado]);
    }

	public function modificar_notificacion($idFormulario){
		try {
			$gesNotificacion = GesNotificacion::select('id','caso','id_medico','id_representante','confirmacion_diagnostico_ges','fecha','confirmacion_tratamiento','id_diagnostico_ges')
			->where('id',$idFormulario)
			->where('visible', true)
			->first();

			if(!$gesNotificacion){
				return response()->json(["informacion" => "Este diagnostico ya fue modificado"]);
			}
		
			$representante = RepresentanteGes::select('nombre_completo','rut','dv','telefono','correo')
			->where('id',$gesNotificacion->id_representante)
			->where('visible', true)
			->first();
	
			$medico = Medico::leftJoin('establecimientos', 'medico.establecimiento_medico', '=', 'establecimientos.id')
			->select(DB::raw("CONCAT(medico.nombre_medico,' ',medico.apellido_medico) AS nombre_apellido, medico.rut_medico, medico.id_medico, medico.nombre_medico,medico.apellido_medico, medico.dv_medico , establecimientos.nombre AS nombre_establecimiento,medico.direccion,medico.ciudad"))
			->where('id_medico',$gesNotificacion->id_medico)
			->first();
			
			$caso = Caso::find($gesNotificacion->caso);
			$paciente = Paciente::where("id",$caso->paciente)->first();
			$region = "";
			$comuna = "";
			if($paciente->id_comuna != null){
				$region = Comuna::getRegion($paciente->id_comuna)->id_region;
				$comuna = $paciente->id_comuna;
			}
			$telefonos = Telefono::where('id_paciente',$paciente->id)->get();

			$historialdiagnostico = HistorialDiagnostico::where("caso",$gesNotificacion->caso)->where("id",$gesNotificacion->id_diagnostico_ges)->first();
			return response()->json(["gesNotificacion"=>$gesNotificacion,"historialdiagnostico"=>$historialdiagnostico,"representante"=>$representante,"medico"=>$medico,"infoPaciente"=>$paciente,"region"=>$region,"comuna"=>$comuna,"telefonos"=>$telefonos]);

		} catch (Exception $ex) {
			return response()->json(["error" => "Error al recibir los datos"]);
		}

    }

	public function eliminar_notificacion($idFormulario){
		try {
			DB::beginTransaction();
			if($idFormulario != "" && $idFormulario){

				$gesNotificacion = GesNotificacion::where('id',$idFormulario)
				->where('visible', true)
				->first();

				if(!$gesNotificacion){
					return response()->json(["informacion" => "Este fomulario ya fue modificado"]);
				}
				
				$gesNotificacion->usuario_modifica = Auth::user()->id;
				$gesNotificacion->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
				$gesNotificacion->visible = false;
				$gesNotificacion->save();

				if($gesNotificacion->id_representante != null){
					$representante = RepresentanteGes::where('id',$gesNotificacion->id_representante)
					->where('visible', true)
					->first();

					$representante->usuario_modifica = Auth::user()->id;
					$representante->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
					$representante->visible = false;
					$representante->save();
				}
			}
		
			DB::commit();
			return response()->json(["exito" => "El formulario ha sido eliminado con exito"]);

		} catch (Exception $ex) {
			DB::rollBack();
			Log::info($ex);
			return response()->json(["error" => "Error al recibir los datos"]);
		}

		

    }
    
}