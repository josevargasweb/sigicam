<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caso;
use App\Models\Paciente;
use App\Models\PreAlta;
use App\Models\THistorialOcupaciones;
use Log;
use DB;
use Auth;
use Response;
use Session;
use Carbon\Carbon;
use Consultas;
use App\Models\HospitalizacionDomiciliaria;
use App\Models\ListaTransito;

class PreAltaController extends Controller
{
    public function enviarPreAlta(Request $request){//USADA
        try {
            DB::beginTransaction();

            $idCaso = $request->idCaso;
            $idSala = $request->idSala;
            $idCama = $request->idCama;
            $usuario = Auth::user()->id;

            $historialActual = THistorialOcupaciones::select("t_historial_ocupaciones.id")
            ->where('t_historial_ocupaciones.caso','=',$idCaso)
            ->leftJoin("camas_temporales","camas_temporales.id_historial_ocupaciones","=","t_historial_ocupaciones.id")
			->where(function($q){
				$q->whereNull('t_historial_ocupaciones.fecha_liberacion')
				->orWhere(function($qor){
					$qor->whereNotNull("camas_temporales.id")
					->where("camas_temporales.visible","=",true);
				});
			})
            ->first();

        //Validar que el paciente aun siga en el hospital
        if ($historialActual) {
                $pendiente = PreAlta::select("fecha_solicita", "usuario_solicita","usuario_solicita","fecha_respuesta")
                ->where("idcaso",$idCaso)
                ->first();
				
				$ct = new \App\Models\CamaTemporal();
				$ct->ocultarCaso($idCaso);
				
                if (is_null($pendiente)) {
                    //desbloqueamos la cama
                    $hOcupacionAntiguo = THistorialOcupaciones::find($historialActual->id);
                    $hOcupacionAntiguo->fecha_liberacion = Carbon::now()->format("Y-m-d H:i:s"); 
                    $hOcupacionAntiguo->id_usuario_alta = $usuario; 
                    $hOcupacionAntiguo->save();

					//comprobar que el paciente no se encuentre en lista de espera de hospitalizacion
					if ($hOcupacionAntiguo->fecha_ingreso_real == null) {
						//Si entro es porque el paciente tenia lista de espera de hospitalizacion
						$lista_transito = ListaTransito::where('caso',$idCaso)->first();
						$lista_transito->fecha_termino = Carbon::now()->format("Y-m-d H:i:s");
						$lista_transito->comentario = 'Pre Alta automatico';
						$lista_transito->save();
					}

                    $preAlta = new PreAlta();
                    $preAlta->idcaso = $idCaso;
                    $preAlta->fecha_solicita = Carbon::now()->format("Y-m-d H:i:s"); 
                    $preAlta->usuario_solicita = $usuario; 
                    $preAlta->idhistorialocupacion = $hOcupacionAntiguo->id; 
                    $preAlta->save();

                    DB::commit();
                    return response()->json(array("exito" => 'Se ha enviado a pre alta correctamente'));
                }else{
                    return response()->json(array("warning" => 'El paciente ya se encuentra en pre alta')); 
                }
      
            }else{
                return response()->json(["warning" => "El paciente ya se encuentra egresado"]);            
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al enviar el paciente a pre alta"]);
        }
    
    }



    public function darAltaPrealta(Request $request){
		try{
			$idLista= strip_tags($request->input("idLista"));
			$idCaso= strip_tags($request->input("idCaso"));

			$motivo=strtolower(strip_tags($request->input("motivo")));
			$detalle = "";
			$ficha = strip_tags($request->input("ficha"));
			$medico_alta = strip_tags($request->input("id_medico"));
			$fallec = strip_tags($request->input("fechaFallecimiento"));
			$inputProcedencia = strip_tags($request->input("inputProcedencia"));
			$inputProcedenciaExtra = strip_tags($request->input("inputProcedenciaExtra"));
			$input_alta = strip_tags($request->input("input-alta"));
			$caso=Caso::find($idCaso);

			$caso = Caso::findOrFail($idCaso);

			$paciente = Paciente::where("id",$caso->paciente)->first();

			$fechaEgreso_dato = strip_tags($request->input("fechaEgreso"));
			try{
				$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");
			}catch(Exception $e){
				$fecha_egreso = Carbon::now()->format("Y-m-d H:i:s");
			}

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones


			DB::beginTransaction();

			if($motivo=='hospitalización'){
				$motivoCaso = 'hospitalización domiciliaria';

				$Hdom=new HospitalizacionDomiciliaria;
				$Hdom->caso=$idCaso;
				$Hdom->fecha=$fecha_egreso;
				$Hdom->usuario = Session::get("usuario")->id;
				$Hdom->save();

			}elseif($motivo=='fuga'){
				$motivoCaso = 'Fuga';
			}elseif($motivo=='liberación de responsabilidad'){
				$motivoCaso = 'Liberación de responsabilidad';
			}elseif($motivo == "derivación"){
				// En ambas listas es igual
				$detalle=trim($inputProcedencia);
			}elseif($motivo == "traslado extra sistema"){
				// Ambas listas el mismo motivo
				$detalle=trim($inputProcedenciaExtra);
			}elseif($motivo == "derivacion otra institucion" || $motivo == "otro"){
				// Ambas listas el mismo motivo
				$detalle=trim($input_alta);
			}
			elseif($motivo == "fallecimiento"){
				// Ambas listas el mismo motivo
				$detalle = "Fallecimiento";
				$paciente->fecha_fallecimiento = Carbon::parse($fallec)->format("Y-m-d H:i:s");
				$paciente->save();
			}else{
				if($motivo == "alta"){
					$detalle="Alta a domicilio";
				}else{
					$detalle=ucwords($motivo);
				}
			}

			//Lista Transito
			$lista_transito = ListaTransito::where('caso',$idCaso)
				->whereNotNull('fecha_termino')
				->whereNull('motivo_salida')
				->first();
			if ($lista_transito && $lista_transito->comentario == 'Pre Alta automatico') {
				//Existe lista sin motivo de salida y con el comentario que indica que viene de pre alta
				$lista_transito->fecha_termino = Carbon::now()->format("Y-m-d H:i:s");
				$lista_transito->motivo_salida = $motivo;
				$lista_transito->comentario = $detalle.' - Pre Alta automatico';
				$lista_transito->save();
			}

			//Pre alta
			//cambiar
			$pre_alta=PreAlta::find($idLista);
			$pre_alta->fecha_respuesta=$fecha_egreso;
			$pre_alta->usuario_respuesta = Auth::user()->id;
			$pre_alta->motivo_salida=$motivo;
			$pre_alta->comentario=$detalle;
			$pre_alta->solicitud_aceptada = true;
			$pre_alta->save();

			//Caso
			//motivos_liberacion
			$caso->fecha_termino=$fecha_egreso;
			if($motivo == 'hospitalización' || $motivo == 'fuga' || $motivo == 'liberación de responsabilidad' ){
				$caso->motivo_termino = $motivoCaso;
			}else{
				$caso->motivo_termino = $motivo;
			}
			$caso->detalle_termino = $detalle;
			$caso->ficha_clinica = $ficha;
			$caso->id_medico_alta = $medico_alta;

			if(isset($request->parto)){
				$caso->parto = ($request->parto == 'no') ? false : true;
			}

			$caso->save();

			//Historial Ocupacion
			//motivos_liberacion
			$historialocupaciones = THistorialOcupaciones::where("caso","=",$idCaso)->orderby("fecha","desc")->first();
			$historialocupaciones->fecha_liberacion = $fecha_egreso;
			if($motivo == 'hospitalización' || $motivo == 'fuga' || $motivo == 'liberación de responsabilidad' ){
				$historialocupaciones->motivo = $motivoCaso;
			}else{
				$historialocupaciones->motivo = $motivo;
			}
			$historialocupaciones->id_usuario_alta = Auth::user()->id;
			$historialocupaciones->save();

			// ListaDerivados::cerrarListaDerivado($idCaso);

			DB::commit();
			return response()->json(array("exito" => "El paciente ha egresado"));
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al egresar al paciente"));
		}
	}

    
}