<?php 
namespace App\Models{

    use DB;
    use TipoUsuario;
    use Session;
    use Log;
    use Auth;

    use Carbon\Carbon;


    class ApiConsultas{

        public static function validar($validar){
            $validacion_interna = $validar["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["replyTO"];
            if($validacion_interna){
                if ($validacion_interna["estado"] == -23) {
                    return "false";
                }
                
            }
            Log::info($validacion_interna);
            return "true";
        }
    }
}