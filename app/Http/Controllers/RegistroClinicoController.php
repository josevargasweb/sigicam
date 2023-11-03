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
use App\Models\RegistroClinico;

use App\Models\Establecimiento;
use App\Models\Paciente;

class RegistroClinicoController extends Controller
{
    public function agregarRegistroClinico(Request $request){
        try {
            DB::beginTransaction();

			if($request->idCasoRegistroClinico == ''){
				return response()->json(["error" => "Error al ingresar"]);
			}

			$idCaso = base64_decode($request->idCasoRegistroClinico);
			$id_anterior_registro_clinico = '';

			
		//recorre los datos de tratamiento
		foreach ($request->registro_clinico as $key => $registro) {
			$son_diferentes_registro_clinico = true;
			$id_anterior_registro_clinico = '';
			
			//busca por la posicion si no viene vacio la id de cultivo
			if(isset($request->id_formulario_registro_clinico)  &&  $request->id_formulario_registro_clinico != ''){

				//busca los datos para ver si no fueron modificados con anterioridad
				$registro_clinico = RegistroClinico::where('id',$request->id_formulario_registro_clinico)
						->where('visible', true)
						->first();
						
				//Si se encuentra un tratamiento antimicrobiano  asociado al id, significa que  si es 
				if (!empty($registro_clinico)) {
					//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
					//verifica si 1 de los datos es diferentes y asi poder modificarlo
					if($registro_clinico->registro != $registro_clinico){
						//Si alguno de los datos es distinto significa  que son distintos tratamientos
						$registro_clinico->usuario_modifica = Auth::user()->id;
						$registro_clinico->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
						$registro_clinico->visible = false;
						$registro_clinico->save();
		
						$id_anterior_registro_clinico = $registro_clinico->id;
					}else{
						$son_diferentes_registro_clinico = false;
					}
				}
				//si pasa de esto significa que son disntintos tratamiento			
			}
			//si 1 de los datos anteriores es diferentes crea una nueva tabla
			if($son_diferentes_registro_clinico){
				$nuevoRegistro = new RegistroClinico;
				if($id_anterior_registro_clinico != ''){
					$nuevoRegistro->id_anterior = $registro_clinico->id;
				}
				$nuevoRegistro->registro = strip_tags($registro);
				$nuevoRegistro->usuario = Auth::user()->id;
				$nuevoRegistro->visible = true;
				$nuevoRegistro->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
				$nuevoRegistro->caso = $idCaso;
				$nuevoRegistro->save();
			}
		}		

			DB::commit();
			return response()->json(array("exito" => 'Se ha ingresado correctamente'));
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al ingresar"]);
        }
    
    }



	public function buscarHistorialRegistroClinico($idCaso){
        $response = [];
		$idCaso = base64_decode($idCaso);

        $data = RegistroClinico::dataHistorialRegistroClinico($idCaso);
        $response = $data;
        // return response()->json($response);
		return response()->json(["aaData" => $response]);
    }


	public function eliminarRegistroClinico(Request $request){
		try {
            DB::beginTransaction();
			if($request->id_formulario == ''){
				return response()->json(["error" => "Error al eliminar"]);
			}

        	$registro = RegistroClinico::find($request->id_formulario);
			if (!empty($registro)) {
				Log::info('entra aca asdsadsadas');
				$registro->usuario_modifica = Auth::user()->id;
				$registro->visible = false;
				$registro->fecha_modificacion = carbon::now()->format("Y-m-d H:i:s");
				$registro->save();
			}else{
				return response()->json(["error" => "Error al eliminar"]);
			}
			DB::commit();
			return response()->json(array("exito" => 'Se ha eliminado correctamente'));
		} catch (Exception $ex) {
			DB::rollBack();
			return response()->json(["error" => "Error al eliminar"]);
		}
    }

	public function edit($id)
    {
		/* Validar formulario */
		if(isset($id) &&  $id != ''){
			$registro = RegistroClinico::where('id',$id)
					->where('visible', true)
					->first();
					
			if (empty($registro)) {
				//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
				return response()->json(array("info" => "Este formulario ya ha sido modificado"));
			}
		}

        return response()->json(["datos" => $registro]);
    }

	
     
}