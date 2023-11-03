<?php namespace App\Models{

use DB;
use Log;
use Auth;
use Carbon\carbon;

use App\Models\Paciente;


    class GestionEgresos
    {
        public static function compararPacientesEgresados(){
            //probando cruce de datos
            $fecha_null = "1800-10-10 12:00:00";
            $pacientes = DB::table('pacientes as p')
                ->select('p.id')
                ->join('hospitalizados_recibidos as hr', 'hr.rut','=',DB::raw("p.rut::text"))
                // ->where('hr.rut',"<>","No")
                ->where('hr.fecha_ingreso','<>',$fecha_null)
                ->where('hr.fecha_egreso','<>',$fecha_null)
                // ->where('hr.gdr','=','no')
                // ->where('hr.epi','=','No')
                ->get();

                foreach ($pacientes as $key => $pac) {
                    $caso = Caso::where('paciente',$pac->id)->whereNotNull('fecha_termino')->orderBy('fecha_termino','desc')->first();
                    log::info("caso: ".$pac->id);
                    log::info($caso->toArray());
                }

            return $pacientes->toArray();
        }
    }
}
