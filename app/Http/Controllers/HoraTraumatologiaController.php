<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Medico;
use View;
use App\Models\SolicitudHoraTraumatologia;
use App\Models\Paciente;
use File;
use App\Models\SolicitudHoraTraumatologiaArchivo;

class HoraTraumatologiaController extends Controller {

public function pedirHora(){

$idEstablecimiento=Session::get("idEstablecimiento");
$idUsuario = Session::get("usuario");
$medicos = Medico::where("visible_medico", true)->get();
$medicoArray = [];
foreach($medicos as $medico){
    $medicoArray[$medico->id_medico] =$medico->nombre_medico." ".$medico->apellido_medico;
}

return View::make("HoraTraumatologia/pedirHora", [
    "establecimiento_origen"=>$idEstablecimiento,
    "usuario" => $idUsuario,
    "medicos" => $medicoArray
    ]);
}

public function pedirHoraPost(Request $request){

    

    $solicitud = new SolicitudHoraTraumatologia;


    $destino = storage_path().'/data/HorasTraumatologia/';

    $fecha        = $request->input("fecha");
    $rut          = (int)$request->input("rut");
    $estabOrigen  = $request->input("establecimiento_origen");
    $estabDestino = $request->input("establecimiento_destino");
    $rutUsuario   = $request->input("rutUsuario");
    $comentario   = $request->input("comentario");
    $archivos      = $request->file('file');
    $medico       = $request->input("medico");



    $paciente = Paciente::where("rut", $rut)->first();

    $solicitud->fecha_solicitud         = $fecha;
    $solicitud->usuario_encargado       = $rutUsuario;
    $solicitud->paciente                = $paciente->id;
    $solicitud->establecimiento_origen  = $estabOrigen;
    $solicitud->establecimiento_destino = $estabDestino;
    $solicitud->estado_solicitud        = "encurso";
    $solicitud->texto_solicitud         = $comentario;
    $solicitud->medico                  = $medico;
    $solicitud->save();

  

        $destino = storage_path().'/data/HorasTraumatologia/';
        $destino = $destino.$solicitud->id_solicitud_hora_traumatologia;
        File::makeDirectory($destino, 0775, true, true);

        if($request->hasFile('files'))
        {


            foreach($archivos as $archivo){
                if(empty($archivo)) continue;

                $filename = $archivo->getClientOriginalName();
                File::makeDirectory($destino, 0775, true, true);
                $archivo->move($destino, $filename);
                

                $solicitudArchivos = new SolicitudHoraTraumatologiaArchivo;
                $solicitudArchivos->solicitud = $solicitud->id_solicitud_hora_traumatologia;
                $solicitudArchivos->recurso = $destino."/".$filename;
                $solicitudArchivos->save();


            }
        }


    


    
    
    

    return response()->json([
            "exito" => "Solicitud enviada",
            "msg" => "Paciente creado"
            ]);
}


public function revisarHora(){



$idEstablecimiento=Session::get("idEstablecimiento");



//si no es quillota muestro las solicitudes del establecimiento del usuario
// si es quillota muestro todas
// 

$motivos = array("encurso","aceptada","rechazada");

foreach ($motivos as $motivo) {
    # code...



if($idEstablecimiento != 1){
    

    $solicitudes = SolicitudHoraTraumatologia::
    select("id_solicitud_hora_traumatologia","fecha_solicitud","pacientes.nombre AS paciente_nombre", "establecimientos.nombre AS est_nombre", "estado_solicitud AS estado","pacientes.rut","pacientes.dv", "texto_solicitud","texto_respuesta","archivo","medico.nombre_medico AS nombre_medico","medico.apellido_medico AS apellido_medico")
    ->join("pacientes","pacientes.id","=","solicitud_hora_traumatologia.paciente")
    ->join("establecimientos", "establecimientos.id", "=", "solicitud_hora_traumatologia.establecimiento_origen")
    ->join('medico',"medico.id_medico","=","solicitud_hora_traumatologia.medico")
    ->where("establecimiento_origen","=",$idEstablecimiento)
    ->where("estado_solicitud","=",$motivo)
    ->get();

    $response[$motivo][] = $solicitudes;
}
else{
    

    $solicitudes = SolicitudHoraTraumatologia::
    select("id_solicitud_hora_traumatologia","fecha_solicitud","pacientes.nombre AS paciente_nombre", "establecimientos.nombre AS est_nombre", "estado_solicitud AS estado","pacientes.rut","pacientes.dv", "texto_solicitud","texto_respuesta","archivo","medico.nombre_medico AS nombre_medico","medico.apellido_medico AS apellido_medico")
    ->join("pacientes","pacientes.id","=","solicitud_hora_traumatologia.paciente")
    ->join("establecimientos", "establecimientos.id", "=", "solicitud_hora_traumatologia.establecimiento_origen")
    ->join('medico',"medico.id_medico","=","solicitud_hora_traumatologia.medico")
    ->where("estado_solicitud","=",$motivo)
    ->get();

    $response[$motivo][] = $solicitudes;
}

}

    //return $response;
    

return View::make("HoraTraumatologia/revisarHora", [
    "solicitudes"=>$solicitudes,
    "establecimiento"=>$idEstablecimiento,
    "motivos"=>$motivos,
    "response" =>$response
    ]);
}
//promedio por solicitud

public function responderHora(Request $request){

    //return Input::all();
    //
    try
    {

    
    $idSolicitud = $request->input("idSolicitud");
    $estadoSolicitud = $request->input("estado_solicitud");
    $comentario = $request->input("comentario");

    $solicitud = SolicitudHoraTraumatologia::where("id_solicitud_hora_traumatologia","=",$idSolicitud)->first();

    $solicitud->estado_solicitud = $estadoSolicitud;
    $solicitud->texto_respuesta = $comentario;
    $solicitud->save();

    return response()->json([
            "exito" => "Solicitud modificada",
            "msg" => "Solicitud modificada"
            ]);
    }
    catch(Exception $ex){
        return $ex;
    }
    
    //return "res";
    //
}

public function cancelarHora(Request $request){

    try
    {


    $idSolicitud = $request->input("idSolicitud");


    $archivos = SolicitudHoraTraumatologiaArchivo::where("solicitud","=",$idSolicitud)->delete();

    $solicitud = SolicitudHoraTraumatologia::where("id_solicitud_hora_traumatologia","=",$idSolicitud)->first();
    $solicitud->delete();
    return response()->json([
            "exito" => "Solicitud cancelada",
            "msg" => "Solicitud cancelada"
            ]);
    }
    catch(Exception $ex){
        return $ex;
    }

}


public function descargarAdjuntoHora($idArchivo){
    try
    {


    $solicitud = SolicitudHoraTraumatologiaArchivo::where("id_archivo","=",$idArchivo)->first();
    
    //return $solicitud;
    return response()->download($solicitud->recurso);

    }
    catch(Exception $ex){
        return $ex;
    }

}

public function getArchivosHora(Request $request){
    try{
        $response = array();
        $id = $request->input("idSolicitud");
        $archivos = SolicitudHoraTraumatologiaArchivo::where("solicitud","=",$id)->get();

        foreach($archivos as $archivo){
            $archivo=HTML::link("descargarAdjuntoHora/$archivo->id_archivo", basename($archivo->recurso));
          $response[]=array(array($archivo));
        }



        
        return response()->json($response);
    }
    catch(Exception $ex){
        return $ex;
    }
}


}