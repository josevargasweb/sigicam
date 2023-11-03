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
use App\Models\ExamenLaboratorio;
use App\Models\ExamenLaboratorioOtros;

use App\Models\Establecimiento;
use App\Models\Paciente;

class ExamenLaboratorioController extends Controller
{
    public function agregarExamenLaboratorio(Request $request){
        try {
            DB::beginTransaction();

			Log::info($request);

			if($request->idCaso == ''){
				return response()->json(["error" => "Error al ingresar"]);
			}

			$idCaso = base64_decode($request->idCaso);
			$idAnterior = '';

			$nuevousoExamenLaboratorio = new ExamenLaboratorio;

			//verifica si el formulario viene con id no vacia sino la modifica
			if(isset($request->idformExamenLab) &&  $request->idformExamenLab != ''){

				
                $examenLaboratorio = ExamenLaboratorio::where('id',$request->idformExamenLab)
                        ->where('visible', true)
                        ->first();
                if (empty($examenLaboratorio)) {
                    //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }

                $examenLaboratorio->usuario_modifica = Auth::user()->id;
                $examenLaboratorio->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $examenLaboratorio->visible = false;
                $examenLaboratorio->save();
				
				$idAnterior = $examenLaboratorio->id;
                $nuevousoExamenLaboratorio->id_anterior = $idAnterior;


            }

			$nuevousoExamenLaboratorio->caso = $idCaso;
			$nuevousoExamenLaboratorio->usuario = Auth::user()->id;
			$nuevousoExamenLaboratorio->visible = true;
			$nuevousoExamenLaboratorio->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");

			if(is_array($request->examenes_laboratorio)){
				$examenesOpcionesArray = ["1" =>''];
                $existeVacio = in_array($request->examenes_laboratorio, $examenesOpcionesArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }

				//verifica que el examen de laboratorio anterior contenga la opcion de otros examenes
				if(isset($examenLaboratorio) && $examenLaboratorio->examenes_opciones != ""){
					//convierte el string en array
					$opciones_anteriores = explode( ',', $examenLaboratorio->examenes_opciones );
					//verifica que la opcion 7 ( otros examenes) este dentro del array // y que en el nuevo no lo contenga y asi modificar la tabla formulario_examenes_laboratorio_otros
					if (in_array("7", $opciones_anteriores) && !in_array("7", $request->examenes_laboratorio)) {
						$otros = ExamenLaboratorioOtros::where("id_examen_laboratorio",$examenLaboratorio->id)->where('visible', true)->get();
						if($otros){
							ExamenLaboratorioOtros::where("id_examen_laboratorio",$examenLaboratorio->id)->where('visible', true)
							->update([
								'usuario_modifica' => Auth::user()->id,
								'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
								'visible' => false
							]);
						}
					}
				}
                $request->merge([
                    'examenes_laboratorio' => implode(",", $request->examenes_laboratorio),
                ]);
            }

			if(is_array($request->bioquimicosSangre)){
				$bioquimicosSangreArray = ["1" =>''];
                $existeVacio = in_array($request->bioquimicosSangre, $bioquimicosSangreArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
                $request->merge([
                    'bioquimicosSangre' => implode(",", $request->bioquimicosSangre),
                ]);
            }
			
			if(is_array($request->bioquimicosOrina)){
				$bioquimicosOrinaArray = ["1" =>''];
                $existeVacio = in_array($request->bioquimicosOrina, $bioquimicosOrinaArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
				$request->merge([
					'bioquimicosOrina' => implode(",", $request->bioquimicosOrina),
                ]);
            }
			
			if(is_array($request->gasesElp)){
				$gasesElpArray = ["1" =>''];
                $existeVacio = in_array($request->gasesElp, $gasesElpArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
                $request->merge([
                    'gasesElp' => implode(",", $request->gasesElp),
                ]);
            }
		
			if(is_array($request->perfiles)){
				$perfilesArray = ["1" =>''];
                $existeVacio = in_array($request->perfiles, $perfilesArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
				$request->merge([
					'perfiles' => implode(",", $request->perfiles),
                ]);
            }
		
			if(is_array($request->liquido)){
				$liquidoArray = ["1" =>''];
                $existeVacio = in_array($request->liquido, $liquidoArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
				$request->merge([
					'liquido' => implode(",", $request->liquido),
                ]);
            }
		
			if(is_array($request->hematologicos)){
				$hematologicosArray = ["1" =>''];
                $existeVacio = in_array($request->hematologicos, $hematologicosArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
				$request->merge([
					'hematologicos' => implode(",", $request->hematologicos),
                ]);
            }
		
			if(is_array($request->hormonales)){
				$hormonalesArray = ["1" =>''];
                $existeVacio = in_array($request->hormonales, $hormonalesArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
				$request->merge([
					'hormonales' => implode(",", $request->hormonales),
                ]);
            }

			$nuevousoExamenLaboratorio->examenes_opciones = $request->examenes_laboratorio;
			$nuevousoExamenLaboratorio->bioquimicos_sangre = $request->bioquimicosSangre;
			$nuevousoExamenLaboratorio->bioquimicos_orina = $request->bioquimicosOrina;
			$nuevousoExamenLaboratorio->fiogases_elp = $request->fiogasesElp;
			$nuevousoExamenLaboratorio->temperaturagases_elp = $request->temperaturagasesElp;
			$nuevousoExamenLaboratorio->gases_elp = $request->gasesElp;
			$nuevousoExamenLaboratorio->perfiles = $request->perfiles;
			$nuevousoExamenLaboratorio->liquido = $request->liquido;
			$nuevousoExamenLaboratorio->hematologicos = $request->hematologicos;
			$nuevousoExamenLaboratorio->hormonales = $request->hormonales;
		
			$nuevousoExamenLaboratorio->save();


		
			// //se elimina los datos que tienen id
			if(isset($request->eliminados_otros_examenes) && $request->eliminados_otros_examenes != ''){
				$eliminados_otros_examenes = preg_replace('/,/', '',  $request->eliminados_otros_examenes, 1);
				foreach (explode( ',', $eliminados_otros_examenes ) as $key => $eliminados) {
					$delete = ExamenLaboratorioOtros::where('id',$eliminados)
						->where('visible', true)
						->first();

					$delete->usuario_modifica = Auth::user()->id;
					$delete->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
					$delete->visible = false;
					$delete->save();
				}
			}

			//recorre los datos de otros examanes
			if (isset($request->otros_examenes) && !empty($request->otros_examenes)) {
				foreach ($request->otros_examenes as $key => $otros_examenes) {
					$son_diferentes_otro = true;
					$id_anterior_otro = '';
					
					//busca por la posicion si no viene vacio la id de otros examenes
					if(isset($request->id_otros_examenes[$key])  &&  $request->id_otros_examenes[$key] != ''){
	
						//busca los datos para ver si no fueron modificados con anterioridad
						$otro = ExamenLaboratorioOtros::where('id',$request->id_otros_examenes[$key])
								->where('visible', true)
								->first();
								
						//Si se encuentra un otro antimicrobiano  asociado al id, significa que  si es 
						if (!empty($otro)) {
							//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
							//verifica si 1 de los datos es diferentes y asi poder modificarlo
							if($otro->duracion_otro != $request->duracionAntimicrobiano[$key] 
								|| $otro->id_examen_laboratorio != $nuevousoExamenLaboratorio->id
							){
								//Si alguno de los datos es distinto
								$otro->usuario_modifica = Auth::user()->id;
								$otro->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
								$otro->visible = false;
								$otro->save();
				
								$id_anterior_otro = $otro->id;
							}else{
								$son_diferentes_otro = false;
							}
						}
						//si pasa de esto significa que son disntintos			
					}
					//si 1 de los datos anteriores es diferentes crea una nueva tabla
					if($son_diferentes_otro){
						$nuevootro = new ExamenLaboratorioOtros;
						if($id_anterior_otro != ''){
							$nuevootro->id_anterior = $otro->id;
						}
						$nuevootro->id_examen_laboratorio = $nuevousoExamenLaboratorio->id;
						$nuevootro->examen = strip_tags($otros_examenes);
						$nuevootro->usuario = Auth::user()->id;
						$nuevootro->visible = true;
						$nuevootro->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
						$nuevootro->caso = $idCaso;
						$nuevootro->save();
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

	public function listarExamenLaboratorio($idCaso){
        $response = [];
		$idCaso = base64_decode($idCaso);

        $data = ExamenLaboratorio::dataHistorialExamenLaboratorio($idCaso);
        $response = $data;
        // return response()->json($response);
		return response()->json(["aaData" => $response]);
    }

	public function edit($id){
		Log::info('entra aca');
		/* Validar formulario */
		if(isset($id) &&  $id != ''){
			$examenLaborario = ExamenLaboratorio::where('id',$id)
					->where('visible', true)
					->first();
					
			if (empty($examenLaborario)) {
				//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
				return response()->json(array("info" => "Este formulario ya ha sido modificado"));
			}
		}

		$datos = ExamenLaboratorio::find($id);
		$datosExamenOtros = '';
		if(!empty($datos)){
			$datosExamenOtros = ExamenLaboratorioOtros::where("caso",$datos->caso)->where("id_examen_laboratorio",$datos->id)->where('visible', true)->get();
		
		}
        return response()->json(["datos" => $datos,"datosExamenOtros" => $datosExamenOtros]);
    }

	public function eliminarExamenLaboratorio($id){
        try {
			$eliminar = ExamenLaboratorio::where('id',$id)
                        ->where('visible', true)
                        ->first();
            if($eliminar){
				$otros = ExamenLaboratorioOtros::where("id_examen_laboratorio",$id)->where('visible', true)->get();

				if($otros){
					ExamenLaboratorioOtros::where("id_examen_laboratorio",$id)->where('visible', true)
					->update([
						'usuario_modifica' => Auth::user()->id,
						'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
						'visible' => false
					]);
				}

				$eliminar->usuario_modifica = Auth::user()->id;
				$eliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
				$eliminar->visible = false;
				$eliminar->save();

                return response()->json(array("exito" => "La indicación ha sido eliminada exitosamente.")); 
            }else{
                return response()->json(array("error" => "No existe información sobre la indicación. <br> La información será actualizada."));
            }
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=> "Error al eliminar la indicación."));
        }
    }

	// public function ultimoDiagnostico($idCaso)
    // {
	// 	$idCaso = base64_decode($idCaso);
	// 	$diagnostico = HistorialDiagnostico::where("caso",$idCaso)->orderBy("fecha","desc")->first();
    //     return response()->json(["diagnostico" => $diagnostico]);
    // }

	// public function consulta_antimicrobiano($palabra){
    //     $datos = DB::table("tratamiento_antimicrobiano")
    //         ->select(DB::raw("nombre, id"))
    //         ->where('nombre', 'ilike', '%'.strtoupper($palabra).'%')
    //         ->orderBy('nombre', 'asc')
    //         ->limit(100)
    //         ->get();

    //     return response()->json($datos);
    // }
     
}