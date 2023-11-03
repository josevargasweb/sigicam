<?php

namespace App\Http\Controllers;

use App\Models\InformeProcesoDiagnostico;
use Illuminate\Http\Request;

use App\Models\Usuario;
use App\Models\Caso;
use App\Models\Paciente;
use App\Models\Procedencia;
use App\Models\HistorialDiagnostico;

use Carbon\Carbon;
use Auth;
use DB;
use Log;
use Exception;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDF;


class InformeProcesoDiagnosticoController extends Controller
{
    public function infoPacienteInforme(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $infoInforme = [];
            
            $infoInforme = InformeProcesoDiagnostico::dataInformeProcesoDiagnostico($caso);

            return response()->json(array("infoInforme" => $infoInforme)); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>"Error..."));
        }
    }

    public function agregarInformeProcesoDiagnostico (Request $request){
        $idCaso = base64_decode($request->idCaso);
        $idInformeProceso = $request->idInforme;
        $fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
        $usuario_logeado = Auth::user()->id;
        try {
            //validaciones.
            $validador = Validator::make($request->all(), [
                'num_folio' => 'required',
                'fecha_informe' => 'required',
                'especialidad' => 'required',//Rule::requiredIf($request->contraste == 'si'),
                'historia_clinica' => 'required',//Rule::requiredIf($request->contraste == 'si'),
                'problema_saluda_auge' => 'required',
                'subgrupo_salud_auge' => 'required',
                'fundamentos_diagnostico' => 'required',
                'tratamiento_indicaciones' => 'required',
                'fecha_inicio_tratamiento' => 'required',
            ],[
                'num_folio.required' => 'Debe ingresar un numumero de folio',
                'fecha_informe.required' => 'Debe ingresar la fecha del informe',
                'especialidad.required' => 'Debe ingresar la especialidad',
                'historia_clinica.required' => 'Debe ingresar la historia clinica',
                'problema_saluda_auge.required' => 'Debe ingresar problema de salud auge',
                'subgrupo_salud_auge.required' => 'Debe ingresar subgrupo de salud auge',
                'fundamentos_diagnostico.required' => 'Debe ingresar fundamenos del diagnóstico',
                'tratamiento_indicaciones.required' => 'Debe ingresar un tratamiento o indicaciones',
                'fecha_inicio_tratamiento.required' => 'Debe seleccionar una fecha de inicio de tratamiento',
                
            ]);

            if($validador->fails()){
                return response()->json(['errores' => $validador->errors()->all()]);
            }

            DB::beginTransaction();
            $nuevoInforme = new InformeProcesoDiagnostico;

            if($idInformeProceso){
                $falsear = InformeProcesoDiagnostico::find($idInformeProceso);
                $falsear->visible = false;
                $falsear->usuario_modifica = Auth::user()->id;
                $falsear->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $falsear->estado = "Editado";
                $falsear->save();
                $nuevoInforme->id_anterior = $falsear->id; 
            }

            $nuevoInforme->caso = $idCaso;
            $nuevoInforme->usuario_ingresa = Auth::user()->id;
            $nuevoInforme->fecha_creacion = $fecha_creacion;
            $nuevoInforme->visible = true;
            $nuevoInforme->num_folio = strip_tags($request->num_folio);
            $nuevoInforme->fecha_informe = ($request->fecha_informe) ? Carbon::parse($request->fecha_informe)->format('Y-m-d H:i:s') : null;
            $nuevoInforme->especialidad = strip_tags($request->especialidad);
            $nuevoInforme->historia_clinica = strip_tags($request->historia_clinica);
            $nuevoInforme->rut_beneficiario = ($request->rut_beneficiario) ? $request->rut_beneficiario : null;
            $nuevoInforme->dv_beneficiario = ($request->dv_beneficiario == "K" || $request->dv_beneficiario == "k") ? 10 : ($request->dv_beneficiario) ? $request->dv_beneficiario : null;
            $nuevoInforme->problema_saluda_auge = strip_tags($request->problema_saluda_auge);
            if($request->confirmacion_auge == "si"){
                $nuevoInforme->confirmacion_auge = true;
            }else if($request->confirmacion_auge == "no"){
                $nuevoInforme->confirmacion_auge = false;
            }else{
                $nuevoInforme->confirmacion_auge = null;
            }
            $nuevoInforme->subgrupo_salud_auge = strip_tags($request->subgrupo_salud_auge);
            $nuevoInforme->fundamentos_diagnostico = strip_tags($request->fundamentos_diagnostico);
            $nuevoInforme->tratamiento_indicaciones = strip_tags($request->tratamiento_indicaciones);
            $nuevoInforme->fecha_inicio_tratamiento = ($request->fecha_inicio_tratamiento) ? Carbon::parse($request->fecha_inicio_tratamiento)->format('Y-m-d H:i:s') : null;
            $nuevoInforme->save();
            DB::commit();
            return response()->json(array("exito"=>"Informe proceso diagnóstico ingresado correctamente."));
        } catch (Exception $ex) {
            DB::rollback();   
            Log::info($ex);
            return response()->json(array("error"=>"Error al ingresar el Informe proceso de diagnóstico."));
        }
    }

    public function listarInformesProcesoDiagnostico ($idCaso){
        $response = [];
        $caso = base64_decode($idCaso);
        $data = InformeProcesoDiagnostico::dataHistorialInformeProcesoDiagnostico($caso);
        $response = $data;
        return response()->json(["aaData" => $response]);
    }

    public function editarInformeProcesoDiagnostico($id){
        if($id != ''){
            $informe = InformeProcesoDiagnostico::where('id',$id)
            ->where('visible', true)
            ->first();

            if(empty($informe)){
                return response()->json(["info" => "Este informe ya ha sido modificado."]);
            }
            return response()->json(["informe" => $informe]);
        }else{
            return response()->json(["error" => "Error al modificar el informe."]);
        }
        
        $datos = InformeProcesoDiagnostico::find($id);
        if(!empty($datos)){

        }
    }

    public function eliminarInformeProcesoDiagnostico($id){
        try {
            DB::beginTransaction();
            $eliminar = InformeProcesoDiagnostico::where('id',$id)
            ->where('visible', true)
            ->first();

            if(empty($eliminar)){
                return response()->json(["info" => "Este informe no se encuentra disponible."]);
            }

            $eliminar->visible = false;
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $eliminar->estado = "Eliminado";
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "El informe ha sido eliminado correctamente."]);
        } catch (Exception $ex) {
            DB::rollback();
            Log::info($ex);  
            return response()->json(["error" => "Error al eliminar el informe."]);
        }

    }

    public function pdfInformeProcesoDiagnostico($id){
        try {
            $informe = InformeProcesoDiagnostico::where('id',$id)
            ->where('visible', true)
            ->first();

            $infoExtraInforme = InformeProcesoDiagnostico::dataInformeProcesoDiagnostico($informe->caso);
    
            $html = PDF::loadView("Gestion.gestionMedica/Pdf/pdfInformeProcesoDiagnostico",[
                "infoExtraInforme" => $infoExtraInforme,
                "informe" => $informe
            ]);
    
            return $html->setPaper('legal','portrait')->download('InformeProcesoDiagnostico.pdf');
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
