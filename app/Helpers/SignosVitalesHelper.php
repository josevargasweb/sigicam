<?php

namespace App\Helpers;
use App\Models\HojaEnfermeriaControlSignoVital;
use App\Models\HojaEnfermeriaCuidadoEnfermeriaAtencion;
use App\Models\HojaEnfermeriaEnfermeriaIndicacionMedica;
use App\Models\PlanificacionIndicacionMedica;
use App\Models\Glasgow;
use App\Models\InformeEpicrisis;

use Log;
use Exception;
use DB;
use Auth;
use Crypt;
use Session;
use Carbon\Carbon;


class SignosVitalesHelper extends Helper {


    public function getPlanificacionParaLaHora($caso_id, $hora){
        $query = "select * from formulario_planificacion_cuidados_atencion_enfermeria as p 
        where p.visible = true and p.tipo= 32 and p.caso = ? and p.horario = ?";
        return DB::select($query, [$caso_id,$hora]);
    }

    public function getPlanificacionParaLaHoraNula($caso_id){
        $query = "select * from formulario_planificacion_cuidados_atencion_enfermeria as p 
        where p.visible = true and p.tipo= 32 and p.caso = ? and p.horario is null";
        return DB::select($query, [$caso_id]);
    }
   
    public function getIndicacionParaLaHora($caso_id, $hora,$id_indicacion){
        $query = "select * from formulario_planificacion_indicaciones_medicas as p 
        where p.visible = true and p.tipo= 'Control de signos vitales' and p.caso = ? and ?= any(string_to_array(p.horario, ','))   and p.id = $id_indicacion";
        return DB::select($query, [$caso_id,$hora]);
    }
   
    public function getIndicacionParaLaHoraNula($caso_id,$id_indicacion){
        $query = "select * from formulario_planificacion_indicaciones_medicas as p 
        where p.visible = true and p.tipo= 'Control de signos vitales' and p.caso = ?  and p.id = $id_indicacion";
        return DB::select($query, [$caso_id]);
    }

    public function getSignosVitalesById($signos_vitales_id){
        $query = "select * from formulario_hoja_enfermeria_signos_vitales as s 
        where s.id = ? and s.visible = true"; 
        return DB::select($query, [$signos_vitales_id]);
    }

    public function getAtencionCheckInPlanificacion($planificacion_id, $hora, $fecha,$fecha_hasta = null){
        $query = "select * from formulario_hoja_enfermeria_cuidado_enfermeria_atencion as a 
        where a.id_atencion = ? and a.visible = true and a.horario = ? and ";
        if($fecha_hasta != null){
            $query .= " a.fecha_creacion::date BETWEEN ? and ? ";
            return DB::select($query, [$planificacion_id, $hora, $fecha,$fecha_hasta]);
        }else{
            $query .= " a.fecha_creacion::date = ? ";
            return DB::select($query, [$planificacion_id, $hora, $fecha]);
        } 

    }

    //comprobar si existen otros signos en la hora
    public function getOtrosSignosParaHora($caso_id, $signo_vital_id, $hora, $fecha,$fecha_hasta = null){

        $otros_signos_flag = false;
        $otros_signos_data = [];
        $hora = (intval($hora) < 10) ? substr($hora, 1) : $hora;

        $query = "select extract(hour from i.horario1) as hora, extract(minute from i.horario1) as minutos, i.horario1 as horario1 
        from formulario_hoja_enfermeria_signos_vitales as i
        where i.caso = ? and i.visible = true and ";
        if($fecha_hasta != null){
            $query .=" i.horario1::date BETWEEN ? and ? ";
        }else{
            $query .=" i.horario1::date = ? ";
        }

        $query .= " and i.id != ? 
        order by i.fecha_creacion  DESC";
        if($fecha_hasta != null){
            $otros_signos = DB::select($query, [$caso_id, Carbon::parse($fecha)->format('Y-m-d H:i:s'),Carbon::parse($fecha_hasta)->format('Y-m-d H:i:s'),$signo_vital_id]);   
        }else{
            $otros_signos = DB::select($query, [$caso_id, Carbon::parse($fecha)->format('Y-m-d H:i:s'),$signo_vital_id]);   
        }
        
        foreach ($otros_signos as $key => $s) {
            if($s->hora === $hora){
                $otros_signos_flag = true;
                $minutos = (intval($s->minutos) < 10) ? "0".$s->minutos : $s->minutos;
                array_push($otros_signos_data, $s->hora.":".$minutos);
            }
        }

        return (object) array (
            "otros_signos_flag" => $otros_signos_flag,
            "otros_signos_data" => $otros_signos_data
        );
    }

    public function getSignosParaHora($caso_id, $hora){

        $signos_data = [];
        $hora = (string) $hora;

        $query = "select extract(hour from i.horario1) as hora, extract(minute from i.horario1) as minutos, i.horario1 as horario1 
        from formulario_hoja_enfermeria_signos_vitales as i
        where i.caso = ? and i.visible = true and i.horario1::date = ? 
        order by i.fecha_creacion  DESC";
        $signos = DB::select($query, [$caso_id,Carbon::now()]);   
        

        foreach ($signos as $key => $s) {
            if($s->hora === $hora){
                $minutos = (intval($s->minutos) < 10) ? "0".$s->minutos : $s->minutos;
                array_push($signos_data, $s->hora.":".$minutos);
            }
        }

        return (object) array (
            "signos_data" => $signos_data
        );
    }


    public function anadirCheckParaSignosVitales($data){
        $data = (object) $data;
        $atencion_is_not_check = (strip_tags($data->atencion_is_not_check) === "true") ? true : false;
        $planificacion = isset($data->planificacion)  && count($data->planificacion) > 0 ? $data->planificacion : [];
        $signos = isset($data->signos) && count($data->signos) > 0 ? $data->signos : [];

        if(count($signos) > 0){


            //ver si no esta check
            if($atencion_is_not_check){

                //ver si existe planificacion (puede ser planificacion con hora, o planificacion hora nula)
                if
                (count($planificacion) > 0){
                    
                    $id_atencion = $planificacion[0]['id'];
                    $hora = Carbon::parse($signos[0]['horario1'])->format('H');

                    //ver si existe atencion (check)
                    $query = "select * from formulario_hoja_enfermeria_cuidado_enfermeria_atencion as a 
                    where a.id_atencion = ? and a.horario = ?";
                    $db_atencion = DB::select($query, [$id_atencion, $hora]);
                    
                    if
                    (count($db_atencion) > 0){
                        //se actualiza a visible true
                        $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($db_atencion[0]->id);
                        $update->visible = true;
                        $update->save();
                    } 
                    
                    //si no existe, entonces se crea:
                    else {

                        $new =  new HojaEnfermeriaCuidadoEnfermeriaAtencion();
                        $new->id_atencion = $id_atencion;
                        $new->usuario = Auth::user()->id;
                        $new->fecha_creacion = Carbon::now();
                        $new->horario = $hora;
                        $new->realizado = true;
                        $new->visible = true;
                        $new->save();

                    }

                }

            }

        }
        
    }


    public function comprobarIngresoDeSignosSinCheck($caso_id, $signos_vitales_id, $hora, $fecha,$fecha_hasta = null){

        $caso_id = Crypt::decrypt(strip_tags($caso_id));
        $signos_vitales_id = Crypt::decrypt(strip_tags($signos_vitales_id));
        $hora = strip_tags($hora);
        $signos = $this->getSignosVitalesById($signos_vitales_id);
        $fecha = strip_tags($fecha);

        if(count($signos) > 0){ 

            $plan_para_la_hora = $this->getPlanificacionParaLaHora($caso_id, $hora);
            $plan_hora_null = $this->getPlanificacionParaLaHoraNula($caso_id);

            //si tiene planificacion
            if
            (count($plan_para_la_hora) > 0){
        
                $planificacion_id = $plan_para_la_hora[0]->id;
                if($fecha_hasta != null){
                    $atencion = $this->getAtencionCheckInPlanificacion($planificacion_id, $hora, $fecha,$fecha_hasta);
                }else{
                    $atencion = $this->getAtencionCheckInPlanificacion($planificacion_id, $hora, $fecha);
                }
                $atencion_is_not_check = (count($atencion) === 0) ? true : false;

                return (object) array(
                    "atencion_is_not_check" => $atencion_is_not_check,
                    'planificacion' => $plan_para_la_hora,
                    'atencion' => $atencion,
                    'signos' => $signos 
                );
            }

            else if
            (count($plan_hora_null) > 0){

                $planificacion_id = $plan_hora_null[0]->id;
                if($fecha_hasta != null){
                    $atencion = $this->getAtencionCheckInPlanificacion($planificacion_id, $hora, $fecha,$fecha_hasta);
                }else{
                    $atencion = $this->getAtencionCheckInPlanificacion($planificacion_id, $hora, $fecha);
                }
                $atencion_is_not_check = (count($atencion) === 0) ? true : false;

                return (object) array(
                    "atencion_is_not_check" => $atencion_is_not_check,
                    'planificacion' => $plan_hora_null,
                    'atencion' => $atencion,
                    'signos' => $signos 
                );
                
            }

            else {
                return (object) array(
                    "atencion_is_not_check" => true,
                    'planificacion' => [],
                    'atencion' => [],
                    'signos' => $signos 
                );

            }

        }

        return (object) array(
            "atencion_is_not_check" => false,
            'planificacion' => [],
            'atencion' => [],
            'signos' => [] 
        );
    }

    public function obtenerSignosVitales($request){

        $resultado =  [];
        $data = [];
        $fecha = strip_tags($request->fecha);
        if(isset($request->fecha_hasta)){
            $fecha_hasta = strip_tags($request->fecha_hasta);
        }
        $caso_id_des = strip_tags($request->caso_id);
        $caso_id_enc = Crypt::encrypt($caso_id_des);

        $sub_categoria = $this->getSubCategoria($caso_id_des);

        $query = "
        select i.id, i.fecha_creacion, i.horario1, i.presion_arterial1, i.presion_arterial1dia, i.presion_arterial_media, i.pulso1, i.frec_respiratoria1, i.temp_axilo1,i.temp_rectal, i.saturacion1, i.hemoglucotest1, i.glasgow1,glasgow.apertura_ocular,glasgow.respuesta_verbal,glasgow.respuesta_motora,glasgow.total, i.fio2_1, i.metodo2_1, i.dolor1, u.nombres, u.apellido_paterno, u.apellido_materno, i.estado_conciencia, i.pam, i.temp_central, i.pvc, i.pcp, i.peso, i.latidos_cardio_fetales, i.movimientos_fetales, i.utero, i.dinamica_uterina, i.flujo_genital, i.gc_ic, i.rvs_rvp, i.gcs_ramsa_sas   
        from formulario_hoja_enfermeria_signos_vitales as i
        inner join usuarios as u on u.id = i.usuario_ingresa
        left join formulario_escala_glasgow as glasgow on glasgow.id_formulario_escala_glasgow = i.indglasgow1
        where i.caso = ? and i.visible = true and i.id_indicacion is null and ";

        if(isset($request->fecha_hasta)){
            $query .="i.horario1::date BETWEEN  ? AND ? ORDER BY i.horario1";

            $signosVitales = DB::select($query, [$caso_id_des, Carbon::parse($fecha)->format('Y-m-d H:i:s'), Carbon::parse($fecha_hasta)->format('Y-m-d H:i:s')]);
        }else{

            $query .="i.horario1::date = ? ORDER BY i.horario1";
    
            $signosVitales = DB::select($query, [$caso_id_des, Carbon::parse($fecha)->format('Y-m-d H:i:s')]);
        }

        $epicrisis = InformeEpicrisis::datosEpicrisis($caso_id_des);
        $sub_categoria = $epicrisis["sub_categoria"];

        foreach ($signosVitales as $key => $vital) {
            $signo_vital_id = Crypt::encrypt($vital->id);
            $horario1 = (isset($vital->horario1)) ? Carbon::parse($vital->horario1)->format("H:i") : "";
            $hora = (isset($vital->horario1)) ? Carbon::parse($vital->horario1)->format("H") : "";

            if(isset($request->fecha_hasta)){
                $otros_signos = $this->getOtrosSignosParaHora($caso_id_des, $vital->id, $hora, $fecha,$fecha_hasta)->otros_signos_data;
            }else{
    
                $otros_signos = $this->getOtrosSignosParaHora($caso_id_des, $vital->id, $hora, $fecha)->otros_signos_data;
            }
            $otros_signos_data = (count($otros_signos) > 0) ? implode(",", $otros_signos) : "false";
            
            
            if(isset($request->fecha_hasta)){
                $is_not_check = ($this->comprobarIngresoDeSignosSinCheck($caso_id_enc, $signo_vital_id, $hora, $fecha,$fecha_hasta)->atencion_is_not_check) ? "true" : "false";
            }else{
    
                $is_not_check = ($this->comprobarIngresoDeSignosSinCheck($caso_id_enc, $signo_vital_id, $hora, $fecha)->atencion_is_not_check) ? "true" : "false";
            }
            
            $diaSigno = Carbon::parse($vital->fecha_creacion)->format("d-m-Y");
            $htmlhora1 = "<div class='form-group'>
            <div class='col-md-11'>
            <input class='dPSigno form-control' name='thorario1[".$key."]' id='thorario1-".$key."' type='text' value='".$horario1."'>
            <input id='tdiasigno-".$key."' type='hidden' value='".$diaSigno."'>
            <script>$('#thorario1-".$key."').datetimepicker({format: 'HH:mm'});</script>
            </div>";


            $arterial1 = "";
            if(isset($vital->presion_arterial1)){
                $arterial1 = $vital->presion_arterial1;
            }

            $htmlarterial1 = "<input class='form-control' name='tarterial1[".$key."]' id='tarterial1-".$key."' min='0' max='500' type='number' value='".$arterial1."'>";

            $arterial1dia = "";
            if(isset($vital->presion_arterial1dia)){
                $arterial1dia = $vital->presion_arterial1dia;
            }
            $htmlarterial1dia = "<input class='form-control' name='tarterial1dia[".$key."]' id='tarterial1dia-".$key."' min='0' max='500' type='number' value='".$arterial1dia."'>";

            $arterial1media = "";
            if($vital->presion_arterial_media){
                $arterial1media = $vital->presion_arterial_media;
            }
            $htmlarterial1media = "<input class='form-control' id='tarterialmedia-".$key."' min='0' type='number' value='".$arterial1media."'>";

            $pulso1 = "";
            if(isset($vital->pulso1)){
                $pulso1 = $vital->pulso1;
            }
            $htmlpulso1 = "<input class='form-control' name='tpulso1[".$key."]' id='tpulso1-".$key."' min='0' max='500' type='number' value='".$pulso1."'>";

            $respiratoria1 = "";
            if(isset($vital->frec_respiratoria1)){
                $respiratoria1 = $vital->frec_respiratoria1;
            }
            $htmlrespiratoria1 = "<input class='form-control' name='trespiratoria1[".$key."]' id='trespiratoria1-".$key."' min='0' max='100' type='number' value='".$respiratoria1."'>";

            $axilar1 = "";
            if(isset($vital->temp_axilo1)){
                $axilar1 = $vital->temp_axilo1;
            }
            $htmlaxilar1 = "<input class='form-control' step='0.1' name='taxilar1[".$key."]' id='taxilar1-".$key."' min='0' max='50' type='number' value='".$axilar1."'>";
            
            $rectal = "";
            if(isset($vital->temp_rectal)){
                $rectal = $vital->temp_rectal;
            }
            $htmlrectal = "<input class='form-control' step='0.1' name='trectal[".$key."]' id='trectal-".$key."' min='0' max='50' type='number' value='".$rectal."'>";

            $saturacion1 = "";
            if(isset($vital->saturacion1)){
                $saturacion1 = $vital->saturacion1;
            }
            $htmlsaturacion1 = "<input class='form-control' step='0.1' name='tsaturacion1[".$key."]' id='tsaturacion1-".$key."' min='0' max='100' type='number' value='".$saturacion1."'>";

            $hemoglu1 = "";
            if(isset($vital->hemoglucotest1)){
                $hemoglu1 = $vital->hemoglucotest1;
            }
            $htmlhemo1 = "<input class='form-control' step='0.1' name='themo1[".$key."]' id='themo1-".$key."' min='0' max='2000' type='number' value='".$hemoglu1."'>";

            $arrayGlasgowx2 = '';
            $glasgow1 = "";
            if($vital->apertura_ocular != null && $vital->apertura_ocular != '' && $vital->respuesta_verbal != null && $vital->respuesta_verbal != '' && $vital->respuesta_motora != null && $vital->respuesta_motora != ''){
                $arrayGlasgowx2 = $vital->apertura_ocular.','.$vital->respuesta_verbal.','.$vital->respuesta_motora;
                $glasgow1 = $vital->total;
            }
            $htmlglasgow1 = "<input class='form-control' min='0' id='tglasgow1-".$key."' type='number' value='".$glasgow1."' readonly>";

            $htmlArrayGlasgowx2 = " <input class='form-control' id='arrayGlasgowx2-".$key."' type='hidden' value='".$arrayGlasgowx2."'>
                                    <input type='hidden' value='En Curso' name='tipoFormGlasgow' id='arrayGlasgowTipoForm-".$key."'>";

            $htmlfio1 = "<select name='fioseleccionado' class='form-control col-md-2' id='tfio1-".$key."'>";

           
            // '21' => '21', 
            // '24' => '24', 
            // '26' => '26', 
            // '28' => '28', 
            // '31' => '31', NO VA CONCE
            // '32' => '32', 
            // '35' => '35', 
            // '36' => '36', 
            // '40' => '40', 
            // '45' => '45', 
            // '50' => '50', 
            // '60' => '60', 
            // '70' => '70-80'
            // '90' => '90-100'

            $numeros = [
            '21','24','26','28','32','35','36','40','45','50','60','70','90'
               ];

            

            for( $i=0; $i<count($numeros); $i++ ) {
                if($numeros[$i] == '70'){
                    $num_fio2 = '70-80';
                }elseif ($numeros[$i] == '90') {
                    $num_fio2 = '90-100';
                }else{
                    $num_fio2 = $numeros[$i];
                }
                
                $htmlfio1 .= '<option ' 
                            . ( $vital->fio2_1 == $numeros[$i] ? 'selected="selected"' : '' ) . ' value="'.$numeros[$i].'">' 
                            .( $num_fio2 )
                            . '</option>';
            }

            $htmlfio1.="</select>";



            $htmlmetodo1 = "<select name='tmeseleccionado' class='form-control col-md-2' id='tmetodo1-".$key."'>";
            if($vital->metodo2_1 == "1"){
                $htmlmetodo1.="<option value='1' selected>Naricera</option>
                <option value='2'>Simple</option>
                <option value='3'>Venturi</option>
                <option value='4'>Reservorio</option>
                <option value='5'>Ambiental</option>";
            }elseif($vital->metodo2_1 == "2"){
                $htmlmetodo1.="<option value='1'>Naricera</option>
                <option value='2' selected>Simple</option>
                <option value='3'>Venturi</option>
                <option value='4'>Reservorio</option>
                <option value='5'>Ambiental</option>";
            }elseif($vital->metodo2_1 == "3"){
                $htmlmetodo1.="<option value='1'>Naricera</option>
                <option value='2'>Simple</option>
                <option value='3' selected>Venturi</option>
                <option value='4'>Reservorio</option>
                <option value='5'>Ambiental</option>";
            }elseif($vital->metodo2_1 == "4"){
                $htmlmetodo1.="<option value='1'>Naricera</option>
                <option value='2'>Simple</option>
                <option value='3'>Venturi</option>
                <option value='4' selected>Reservorio</option>
                <option value='5'>Ambiental</option>";
            }else{
                $htmlmetodo1.="<option value='1'>Naricera</option>
                <option value='2'>Simple</option>
                <option value='3'>Venturi</option>
                <option value='4'>Reservorio</option>
                <option value='5' selected>Ambiental</option>";
            }
            $htmlmetodo1.="</select>";

           
            $dolor1 = "";
            if(isset($vital->dolor1)){
                $dolor1 = $vital->dolor1;
            }
            $htmldolor1 = "<input class='form-control col-md-1' name='tdolor1[".$key."]' id='tdolor1-".$key."' min='1' max='10' type='number' value='".$dolor1."'>";

            if($sub_categoria == 2){
                $htmllatidos = "<input class='form-control col-md-1' id='tlatidos1-".$key."' type='text' value='".$vital->latidos_cardio_fetales."'>";
                $htmlmovimientos = "<input class='form-control col-md-1' id='tmovimientos1-".$key."' type='text' value='".$vital->movimientos_fetales."'>";
                $htmlutero = "<select name='tuteseleccionado' class='form-control col-md-2' id='tutero1-".$key."'>";
                if($vital->utero == "1"){
                    $htmlutero.="<option value='1' selected>Reposo</option>
                    <option value='2'>Irritable</option>";
                }elseif($vital->utero == "2"){
                    $htmlutero.="<option value='1'>Reposo</option>
                    <option value='2' selected>Irritable</option>";
                }
                $htmlutero.="</select>";
                $htmldinamica = "<input class='form-control col-md-1' id='tdinamicas1-".$key."' type='text' value='".$vital->dinamica_uterina."'>";
                $htmlflujo = "<input class='form-control col-md-1' id='tflujos1-".$key."' type='text' value='".$vital->flujo_genital."'>";
                $resultado [] = [
                    $htmlhora1."<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($vital->fecha_creacion)->format("d-m-Y H:i")." <br> por: {$vital->nombres} {$vital->apellido_paterno} {$vital->apellido_materno}</b> </p> </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-7'><label>P.Arterial Sis. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1."</div>
                        <br>
                        <div class='col-md-7'><label>P.Arterial Dias. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1dia."</div>
                        <br>
                        <div class='col-md-7'><label>Frecuencia cardiaca (Lpm)</label> </div>
                        <div class='col-md-5'>".$htmlpulso1." </div>
                        <br>
                        <div class='col-md-7'><label>Frec. res. (Rpm)</label> </div>
                        <div class='col-md-5'> ".$htmlrespiratoria1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Axilar (°C)</label> </div>
                        <div class='col-md-5'>".$htmlaxilar1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Rectal (°C)</label> </div>
                        <div class='col-md-5'>".$htmlrectal."</div>
                        <br>
                        <div class='col-md-7'><label>Saturación de oxígeno (%)</label> </div>
                        <div class='col-md-5'>".$htmlsaturacion1."</div>
                    </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-6'><label>Hemoglucotest (mg/dl)</label> </div>
                        <div class='col-md-6'>".$htmlhemo1." </div>
                        <br><br><br>
                        <div class='col-md-6'><label>Glasgow</label> </div>
                        <div class='col-md-6'>".$htmlglasgow1."</div>
                        <div class='col-md-12' style='margin-left: 172px;'>
                                <a class='btn btn-success' onclick='editarGlasgow1(".$vital->id.",".$key.")'>
                                    <i class='glyphicon glyphicon-edit'></i>
                                </a>    
                                <a class='btn btn-danger' onclick='limpiarGlasgow1(".$key.")'>
                                    <i class='glyphicon glyphicon-trash'></i>
                                </a>
                        </div>
                        <br><br><br><br><br>
                        <div class='col-md-6'><label>FIO2</label> </div>
                        <div class='col-md-6'>".$htmlfio1."</div>
                        <br><br><br>
                        <div class='col-md-6'><label>Metodo O2</label> </div>
                        <div class='col-md-6'>".$htmlmetodo1."</div>
                        <br><br><br>
                        <div class='col-md-6'><label>Dolor</label> </div>
                        <div class='col-md-6'>".$htmldolor1." </div>
                        <div class='col-md-12'>".$htmlArrayGlasgowx2." </div>
                    </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-12'><label>Latidos cardio fetales</label> </div>
                        <div class='col-md-12'>".$htmllatidos."</div>
                        <br>
                        <div class='col-md-12'><label>Movimientos fetales</label> </div>
                        <div class='col-md-12'>".$htmlmovimientos."</div>
                        <br>
                        <div class='col-md-12'><label>Útero</label> </div>
                        <div class='col-md-12'>".$htmlutero." </div>
                        <br>
                        <div class='col-md-12'><label>Dinamíca uterina</label> </div>
                        <div class='col-md-12'> ".$htmldinamica."</div>
                        <br>
                        <div class='col-md-12'><label>Flujo genital</label> </div>
                        <div class='col-md-12'>".$htmlflujo."</div>
                    </div>",
                    "<div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-success' data_signo_vital_id = ".$signo_vital_id." onclick='modificarSignoVital(this,".$key.")'><i class='glyphicon glyphicon-edit'></i></button>
                    </div>
                    <br><br>
                    <div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-danger' data_signo_vital_id = ".$signo_vital_id." data_otros_signos = ".$otros_signos_data." data_is_not_check = ".$is_not_check." 
                    onclick='eliminarSignoVital(this)'><i class='glyphicon glyphicon-trash'></i></button>
                    </div>"
                ];
		    }else if($sub_categoria == 3){
                $htmlestadoconciencia = "<select name='tecseleccionado' class='form-control col-md-2' id='testadoconciencia1-".$key."'>";
                if($vital->estado_conciencia == "1"){
                    $htmlestadoconciencia.="<option value='1' selected>Activo</option>
                    <option value='2'>Hipotónico</option>
                    <option value='3'>Llora</option>
                    <option value='4'>Apnea</option>
                    <option value='5'>Rosado</option>
                    <option value='6'>Pálido</option>";
                }elseif($vital->estado_conciencia == "2"){
                    $htmlestadoconciencia.="<option value='1'>Activo</option>
                    <option value='2' selected>Hipotónico</option>
                    <option value='3'>Llora</option>
                    <option value='4'>Apnea</option>
                    <option value='5'>Rosado</option>
                    <option value='6'>Pálido</option>";
                }elseif($vital->estado_conciencia == "3"){
                    $htmlestadoconciencia.="<option value='1'>Activo</option>
                    <option value='2'>Hipotónico</option>
                    <option value='3' selected>Llora</option>
                    <option value='4'>Apnea</option>
                    <option value='5'>Rosado</option>
                    <option value='6'>Pálido</option>";
                }elseif($vital->estado_conciencia == "4"){
                    $htmlestadoconciencia.="<option value='1'>Activo</option>
                    <option value='2'>Hipotónico</option>
                    <option value='3'>Llora</option>
                    <option value='4' selected>Apnea</option>
                    <option value='5'>Rosado</option>
                    <option value='6'>Pálido</option>";
                }elseif($vital->estado_conciencia == "5"){
                    $htmlestadoconciencia.="<option value='1'>Activo</option>
                    <option value='2'>Hipotónico</option>
                    <option value='3'>Llora</option>
                    <option value='4'>Apnea</option>
                    <option value='5' selected>Rosado</option>
                    <option value='6'>Pálido</option>";
                }else{
                    $htmlestadoconciencia.="<option value='1'>Activo</option>
                    <option value='2'>Hipotónico</option>
                    <option value='3'>Llora</option>
                    <option value='4'>Apnea</option>
                    <option value='5'>Rosado</option>
                    <option value='6' selected>Pálido</option>";
                }
                $htmlestadoconciencia.="</select>";

                $htmlmetodo1 = "<select name='tmeseleccionado' class='form-control col-md-2' id='tmetodo1-".$key."'>";
                if($vital->metodo2_1 == "1"){
                    $htmlmetodo1.="<option value='1' selected>Ventilación mecánica</option>
                    <option value='2'>Cánula vestibular</option>
                    <option value='3'>CPAP</option>
                    <option value='4'>NIPPV</option>";
                }elseif($vital->metodo2_1 == "2"){
                    $htmlmetodo1.="<option value='1'>Ventilación mecánica</option>
                    <option value='2' selected>Cánula vestibular</option>
                    <option value='3'>CPAP</option>
                    <option value='4'>NIPPV</option>";
                }elseif($vital->metodo2_1 == "3"){
                    $htmlmetodo1.="<option value='1'>Ventilación mecánica</option>
                    <option value='2'>Cánula vestibular</option>
                    <option value='3' selected>CPAP</option>
                    <option value='4'>NIPPV</option>";
                }else{
                    $htmlmetodo1.="<option value='1'>Ventilación mecánica</option>
                    <option value='2'>Cánula vestibular</option>
                    <option value='3'>CPAP</option>
                    <option value='4' selected>NIPPV</option>";
                }
                $htmlmetodo1.="</select>";

                $peso = "";
                if($vital->peso){
                    $peso = $vital->peso;
                }
                $htmlpeso = "<input class='form-control col-md-1' id='tpeso-".$key."' min='0' type='number' value='".$peso."'>";

                $resultado [] = [
                    $htmlhora1."<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($vital->fecha_creacion)->format("d-m-Y H:i")." <br> por: {$vital->nombres} {$vital->apellido_paterno} {$vital->apellido_materno}</b> </p> </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-7'><label>P.Arterial Sis. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1."</div>
                        <br>
                        <div class='col-md-7'><label>P.Arterial Dias. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1dia."</div>
                        <br>
                        <div class='col-md-7'><label>P.Arterial Media. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1media."</div>
                        <br>
                        <div class='col-md-7'><label>Frecuencia cardiaca (Lpm)</label> </div>
                        <div class='col-md-5'>".$htmlpulso1." </div>
                        <br>
                        <div class='col-md-7'><label>Frec. res. (Rpm)</label> </div>
                        <div class='col-md-5'> ".$htmlrespiratoria1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Axilar (°C)</label> </div>
                        <div class='col-md-5'>".$htmlaxilar1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Rectal (°C)</label> </div>
                        <div class='col-md-5'>".$htmlrectal."</div>
                        <br>
                        <div class='col-md-7'><label>Saturación de oxígeno (%)</label> </div>
                        <div class='col-md-5'>".$htmlsaturacion1."</div>
                    </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-6'><label>Hemoglucotest (mg/dl)</label> </div>
                        <div class='col-md-6'>".$htmlhemo1." </div>
                        <br>
                        <div class='col-md-6'><label>Estado conciencia</label> </div>
                        <div class='col-md-6'>".$htmlestadoconciencia."</div>
                        
                        <br>
                        <div class='col-md-6'><label>FIO2</label> </div>
                        <div class='col-md-6'>".$htmlfio1."</div>
                        <br>
                        <div class='col-md-6'><label>Metodo O2</label> </div>
                        <div class='col-md-6'>".$htmlmetodo1."</div>
                        <br>
                        <div class='col-md-6'><label>Dolor</label> </div>
                        <div class='col-md-6'>".$htmldolor1." </div>
                        <div class='col-md-6'><label>Peso</label> </div>
                        <div class='col-md-6'>".$htmlpeso." </div>
                        <div class='col-md-12'>".$htmlArrayGlasgowx2." </div>
                    </div>",
                    "<div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-success' data_signo_vital_id = ".$signo_vital_id." onclick='modificarSignoVital(this,".$key.")'><i class='glyphicon glyphicon-edit'></i></button>
                    </div>
                    <br><br>
                    <div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-danger' data_signo_vital_id = ".$signo_vital_id." data_otros_signos = ".$otros_signos_data." data_is_not_check = ".$is_not_check." 
                    onclick='eliminarSignoVital(this)'><i class='glyphicon glyphicon-trash'></i></button>
                    </div>"
                ];
            }else if($sub_categoria == 4){
                $pam = "";
                if(isset($vital->pam)){
                    $pam = $vital->pam;
                }
                $htmlpam= "<input class='form-control col-md-1' name='tpam[".$key."]' id='tpam-".$key."' type='number' value='".$pam."' step='0.1'>";

                $temp_central = "";
                if(isset($vital->temp_central)){
                    $temp_central = $vital->temp_central;
                }
                $htmltemp_central= "<input class='form-control col-md-1' name='ttemp_central[".$key."]' id='ttemp_central-".$key."' type='number' value='".$temp_central."' min='0' max='50' step='0.1'>";

                $pvc = "";
                if(isset($vital->pvc)){
                    $pvc = $vital->pvc;
                }
                $htmlpvc= "<input class='form-control col-md-1' name='tpvc[".$key."]' id='tpvc-".$key."' type='number' value='".$pvc."' min='0' max='30' step='0.1'>";

                $pcp = "";
                if(isset($vital->pcp)){
                    $pcp = $vital->pcp;
                }
                $htmlpcp= "<input class='form-control col-md-1' name='tpcp[".$key."]' id='tpcp-".$key."' type='number' value='".$pcp."' min='0' max='50' step='0.1'>";

                $peso = "";
                if(isset($vital->peso)){
                    $peso = $vital->peso;
                }
                $htmlpeso= "<input class='form-control col-md-1' name='tpeso[".$key."]' id='tpeso-".$key."' type='number' value='".$peso."' min='0' max='700' step='0.1'>";

                $gc_ic = "";
                if(isset($vital->gc_ic)){
                    $gc_ic = $vital->gc_ic;
                }
                $htmlgc_ic= "<input class='form-control col-md-1' name='tgc_ic[".$key."]' id='tgc_ic-".$key."' type='number' value='".$gc_ic."' min='0' max='30' step='0.1'>";


                $rvs_rvp = "";
                if(isset($vital->rvs_rvp)){
                    $rvs_rvp = $vital->rvs_rvp;
                }
                $htmlrvs_rvp= "<input class='form-control col-md-1' name='trvs_rvp[".$key."]' id='trvs_rvp-".$key."' type='number' value='".$rvs_rvp."' min='0' max='10000' step='0.1'>";

                $gcs_ramsa_sas = "";
                if(isset($vital->gcs_ramsa_sas)){
                    $gcs_ramsa_sas = $vital->gcs_ramsa_sas;
                }
                $htmlgcs_ramsa_sas= "<input class='form-control col-md-1' name='tgcs_ramsa_sas[".$key."]' id='tgcs_ramsa_sas-".$key."' type='text' value='".$gcs_ramsa_sas."'>";
                
                $htmlInputsForUnidadPediatrica = 
                "<div class='row'>
                    <div class='col-md-6'><br><label>Presión arterial media PAM (mmHg)</label> </div>
                        <div class='col-md-6'>".$htmlpam."</div>
                    </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>T° central</label> </div>
                    <div class='col-md-6'>".$htmltemp_central."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>PVC</label> </div>
                    <div class='col-md-6'>".$htmlpvc."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>PCP</label> </div>
                    <div class='col-md-6'>".$htmlpcp."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>Peso</label> </div>
                    <div class='col-md-6'>".$htmlpeso."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>GC/IC</label> </div>
                    <div class='col-md-6'>".$htmlgc_ic."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>RVS/RVP</label> </div>
                    <div class='col-md-6'>".$htmlrvs_rvp."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'><br><label>GCS/RAMSA/SAS</label> </div>
                    <div class='col-md-6'>".$htmlgcs_ramsa_sas."</div>
                </div>";

                $resultado [] = [
                    $htmlhora1."<br><div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($vital->fecha_creacion)->format("d-m-Y H:i")." <br> por " .$vital->nombres. " " .$vital->apellido_paterno. " " .$vital->apellido_materno."</b> </p> </div>",
                    "<div class='col-md-12'>
                        <div class='row'>
                            <div class='col-md-7'><br><label>P.Arterial Sis. (mmHg)</label> </div>
                            <div class='col-md-5'>".$htmlarterial1."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>P.Arterial Dias. (mmHg)</label> </div>
                            <div class='col-md-5'>".$htmlarterial1dia."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>Frecuencia cardiaca (Lpm)</label> </div>
                            <div class='col-md-5'>".$htmlpulso1." </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>Frec. res. (Rpm)</label> </div>
                            <div class='col-md-5'> ".$htmlrespiratoria1."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>Temp. Axilar (°C)</label> </div>
                            <div class='col-md-5'>".$htmlaxilar1."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>Temp. Rectal (°C)</label> </div>
                            <div class='col-md-5'>".$htmlrectal."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-7'><br><label>Saturación de oxígeno (%)</label> </div>
                            <div class='col-md-5'>".$htmlsaturacion1."</div>
                        </div>
                    </div>",
                    "<div class='col-md-12'>
                        <div class='row'>
                            <div class='col-md-6'><br><label>Hemoglucotest (mg/dl)</label> </div>
                            <div class='col-md-6'>".$htmlhemo1." </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-6'><br><label>Glasgow</label> </div>
                            <div class='col-md-3'>".$htmlglasgow1."</div>
                            <div class='col-md-3'>
                                <a class='btn btn-primary' onclick='editarGlasgow1(".$vital->id.",".$key.")' style='margin-top: 1px;margin-left: -28px;'>
                                    <i class='glyphicon glyphicon-edit'></i>
                                </a>
                                <a class='btn btn-success' onclick='limpiarGlasgow1(".$key.")' style='margin-top: 1px;margin-left: -3px;'>
                                    <i class='glyphicon glyphicon-trash'></i>
                                </a>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-6'><br><label>FIO2</label> </div>
                            <div class='col-md-6'>".$htmlfio1."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-6'><br><label>Metodo O2</label> </div>
                            <div class='col-md-6'>".$htmlmetodo1."</div>
                        </div>
                        <div class='row'>
                            <div class='col-md-6'><br><label>Dolor</label> </div>
                            <div class='col-md-6'>".$htmldolor1." </div>
                            <div class='col-md-12'>".$htmlArrayGlasgowx2." </div>
                        </div>".$htmlInputsForUnidadPediatrica."

                    </div>",
                   /*  $vital->nombres. " " .$vital->apellido_paterno. " " .$vital->apellido_materno, */
                    "<div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn-xs btn-warning btn-modificar-signo-vital' data_signo_vital_id = ".$signo_vital_id." data_fila_id = ".$key.">Modificar</button>
                    </div>
                    <br><br>
                    <div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn-xs btn-danger' data_signo_vital_id = ".$signo_vital_id." data_otros_signos = ".$otros_signos_data." data_is_not_check = ".$is_not_check." 
                    onclick='eliminarSignoVital(this)'>Eliminar</button>
                    </div>"
                ];
            }else{
                $resultado [] = [
                    $htmlhora1."<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($vital->fecha_creacion)->format("d-m-Y H:i")." <br> por: {$vital->nombres} {$vital->apellido_paterno} {$vital->apellido_materno}</b> </p> </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-7'><label>P.Arterial Sis. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1."</div>
                        <br>
                        <div class='col-md-7'><label>P.Arterial Dias. (mmHg)</label> </div>
                        <div class='col-md-5'>".$htmlarterial1dia."</div>
                        <br>
                        <div class='col-md-7'><label>Frecuencia cardiaca (Lpm)</label> </div>
                        <div class='col-md-5'>".$htmlpulso1." </div>
                        <br>
                        <div class='col-md-7'><label>Frec. res. (Rpm)</label> </div>
                        <div class='col-md-5'> ".$htmlrespiratoria1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Axilar (°C)</label> </div>
                        <div class='col-md-5'>".$htmlaxilar1."</div>
                        <br>
                        <div class='col-md-7'><label>Temp. Rectal (°C)</label> </div>
                        <div class='col-md-5'>".$htmlrectal."</div>
                        <br>
                        <div class='col-md-7'><label>Saturación de oxígeno (%)</label> </div>
                        <div class='col-md-5'>".$htmlsaturacion1."</div>
                    </div>",
                    "<div class='col-md-12'>
                        <div class='col-md-6'><label>Hemoglucotest (mg/dl)</label> </div>
                        <div class='col-md-6'>".$htmlhemo1." </div>
                        <br>
                        <div class='col-md-6'><label>Glasgow</label> </div>
                        <div class='col-md-3'>".$htmlglasgow1."</div>
                        <div class='col-md-3'>
                            <a class='btn btn-primary' onclick='editarGlasgow1(".$vital->id.",".$key.")' style='margin-top: 1px;margin-left: -28px;'>
                                <i class='glyphicon glyphicon-edit'></i>
                            </a>
                            <a class='btn btn-success' onclick='limpiarGlasgow1(".$key.")' style='margin-top: 1px;margin-left: -3px;'>
                                <i class='glyphicon glyphicon-trash'></i>
                            </a>
                        </div>
                        <br>
                        <div class='col-md-6'><label>FIO2</label> </div>
                        <div class='col-md-6'>".$htmlfio1."</div>
                        <br>
                        <div class='col-md-6'><label>Metodo O2</label> </div>
                        <div class='col-md-6'>".$htmlmetodo1."</div>
                        <br>
                        <div class='col-md-6'><label>Dolor</label> </div>
                        <div class='col-md-6'>".$htmldolor1." </div>
                        <div class='col-md-12'>".$htmlArrayGlasgowx2." </div>
                    </div>",
                    "<div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-success' data_signo_vital_id = ".$signo_vital_id." onclick='modificarSignoVital(this,".$key.")'><i class='glyphicon glyphicon-edit'></i></button>
                    </div>
                    <br><br>
                    <div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn btn-danger' data_signo_vital_id = ".$signo_vital_id." data_otros_signos = ".$otros_signos_data." data_is_not_check = ".$is_not_check." 
                    onclick='eliminarSignoVital(this)'><i class='glyphicon glyphicon-trash'></i></button>
                    </div>"
                ];
            }
        }
        return $resultado;

    }


    public function agregarSignosVitales($request){
        if($request->arrayGlasgowx2 != '' && count(explode(",", $request->arrayGlasgowx2)) == 3){
            $arrayGlasgowx2 = explode(",", $request->arrayGlasgowx2);

            //valida si se envian datos que no corresponden
            $validarGlasgowx2 = ["1" =>'1','2','3','4','5','6'];
            $existe = array_diff($arrayGlasgowx2, $validarGlasgowx2);
            if(count($existe) > 0){
                return response()->json(["error" => "Error al ingresar los signos vitales"]);   
            }
            $glasgow = new Glasgow;
            $glasgow->usuario_responsable = Auth::user()->id;
            $glasgow->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $glasgow->caso = Crypt::decrypt(strip_tags($request->caso));
            $glasgow->apertura_ocular = $arrayGlasgowx2[0];
            $glasgow->respuesta_verbal = $arrayGlasgowx2[1];
            $glasgow->respuesta_motora = $arrayGlasgowx2[2];
            $glasgow->total = $arrayGlasgowx2[0] + $arrayGlasgowx2[1] + $arrayGlasgowx2[2];
            $glasgow->visible = true;
            $glasgow->tipo = 'En Curso';
            $glasgow->save();
        }

        /* CAPTURA DATOS */

        // input fecha en vista control signos vitales
        $fecha = strip_tags(trim($request->fecha_signo_vital));
        $hora = Carbon::parse(strip_tags($request->horario1))->format('H');
        $hora_min = strip_tags($request->horario1);
        $horario1 = Carbon::createFromFormat('d-m-Y H:i', $fecha." ".$hora_min)->format('Y-m-d H:i:s');
        $caso_id = Crypt::decrypt(strip_tags($request->caso));
        $user_id = Auth::user()->id;
        $presion_arterial1 = strip_tags($request->arterial1);
        $presion_arterial1dia = strip_tags($request->arterial1dia);
        $pulso1 = strip_tags($request->pulso1);
        $frec_respiratoria1 = strip_tags($request->respiratoria1);
        $temp_axilo1 = strip_tags($request->axilo1);
        $temp_rectal = strip_tags($request->rectal);
        $saturacion1 = strip_tags($request->saturacion1);
        $hemoglucotest1 = strip_tags($request->hemoglucotest1);
        $indglasgow1 = (isset($glasgow) && $glasgow->id_formulario_escala_glasgow) ? $glasgow->id_formulario_escala_glasgow : null;  
        $fio2_1 = strip_tags($request->fio1);
        $metodo2_1 = strip_tags($request->metodo1);
        $dolor1 = strip_tags($request->dolor1);
        $presion_arterial_media = (isset($request->arterial1media) && $request->arterial1media) ? strip_tags($request->arterial1media) : '';
        $estado_conciencia1 = (isset($request->estado_conciencia) && $request->estado_conciencia) ? strip_tags($request->estado_conciencia) : '';
        $peso = (isset($request->peso) && $request->peso) ? $request->peso : 0;

        //control obstétrico => sub_unidad => 2 => gineco-obstetrico
        $latidos = (isset($request->latidos_cardio_fetales) && $request->latidos_cardio_fetales) ? $request->latidos_cardio_fetales : '';
        $movimientos = (isset($request->movimientos_fetales) && $request->movimientos_fetales) ? $request->movimientos_fetales : '';
        $utero = (isset($request->utero) && $request->utero) ? $request->utero : '';
        $dinamica = (isset($request->dinamica_uterina) && $request->dinamica_uterina) ? $request->dinamica_uterina : '';
        $flujo = (isset($request->flujo_genital) && $request->flujo_genital) ? $request->flujo_genital : '';

        //nuevos
        $sub_categoria = $this->getSubCategoria($caso_id);
        $pam  = (isset($request->pam) && $sub_categoria == 4) ? trim(strip_tags($request->pam)) : "";
        $temp_central = (isset($request->temp_central) && $sub_categoria == 4) ? trim(strip_tags($request->temp_central)) : "";
        $pvc = (isset($request->pvc) && $sub_categoria == 4) ? trim(strip_tags($request->pvc)) : "";
        $pcp = (isset($request->pcp) && $sub_categoria == 4) ? trim(strip_tags($request->pcp)) : "";
        $peso = (isset($request->peso) && $sub_categoria == 4) ? trim(strip_tags($request->peso)) : null;
        $gc_ic = (isset($request->gc_ic) && $sub_categoria == 4) ? trim(strip_tags($request->gc_ic)) : "";
        $rvs_rvp = (isset($request->rvs_rvp) && $sub_categoria == 4) ? trim(strip_tags($request->rvs_rvp)) : "";
        $gcs_ramsa_sas = (isset($request->gcs_ramsa_sas) && $sub_categoria == 4) ? trim(strip_tags($request->gcs_ramsa_sas)) : "";

        $id_indicacion_medica = ($request->idIndicacionMedica != "") ? $request->idIndicacionMedica : "";  

        /* VALIDA DOMINIO DE LO QUE LLEGA */

        //presion_arterial1
        $presion_arterial1_is_valid = (mb_strlen($presion_arterial1) === 0) || 
        ( 
            $this->isInteger($presion_arterial1) == true &&
            (int) $presion_arterial1 >= 0 && (int) $presion_arterial1 <= 500
        ) ? true : false;

        if(!$presion_arterial1_is_valid){
            throw new Exception('Campo P.Arterial Sis. (mmHg) no valido. Valor:'.$presion_arterial1);
        }

        //presion_arterial1dia
        $presion_arterial1dia_is_valid = (mb_strlen($presion_arterial1dia) === 0) ||
        ( 
            $this->isInteger($presion_arterial1dia) == true &&
            (int) $presion_arterial1dia >= 0 && (int) $presion_arterial1dia <= 500
        ) ? true : false;

        if(!$presion_arterial1dia_is_valid){
            throw new Exception('Campo P.Arterial Dias. (mmHg) no valido. Valor:'.$presion_arterial1dia);
        }

        //pulso1
        $pulso1_is_valid = (mb_strlen($pulso1) === 0) ||
        (
            $this->isInteger($pulso1) == true &&
            (int) $pulso1 >= 0 && (int) $pulso1 <= 500
        ) ? true : false;

        if(!$pulso1_is_valid){
            throw new Exception('Campo Frecuencia cardiaca (Lpm) no valido. Valor:'.$pulso1);
        }

        //frec_respiratoria1
        $frec_respiratoria1_is_valid = (mb_strlen($frec_respiratoria1) === 0) ||
        (
            $this->isInteger($frec_respiratoria1) == true &&
            (int) $frec_respiratoria1 >= 0 && (int) $frec_respiratoria1 <= 100
        ) ? true : false;

        if(!$frec_respiratoria1_is_valid){
            throw new Exception('Campo Frec. res. (Rpm) no valido. Valor:'.$frec_respiratoria1);
        }

        //temp_axilo1
        $temp_axilo1_is_valid = (mb_strlen($temp_axilo1) === 0) ||
        (
            $this->isNumeric($temp_axilo1) == true &&
            (float) $temp_axilo1 >= 0 && (float) $temp_axilo1 <= 50
        ) ? true : false;

        if(!$temp_axilo1_is_valid){
            throw new Exception('Campo Temp. Axilar (°C) no valido. Valor:'.$temp_axilo1);
        }

        //temp_rectal
        $temp_rectal_is_valid = (mb_strlen($temp_rectal) === 0) ||
        (
            $this->isNumeric($temp_rectal) == true &&
            (float) $temp_rectal >= 0 && (float) $temp_rectal <= 50
        ) ? true : false;

        if(!$temp_rectal_is_valid){
            throw new Exception('Campo Temp. Rectal (°C) no valido. Valor:'.$temp_rectal);
        }

        //saturacion1
        $saturacion1_is_valid = (mb_strlen($saturacion1) === 0) ||
        (
            $this->isNumeric($saturacion1) == true &&
            (float) $saturacion1 >= 0 && (float) $saturacion1 <= 100
        ) ? true : false;

        if(!$saturacion1_is_valid){
            throw new Exception('Campo Saturación de oxígeno (%) no valido. Valor:'.$saturacion1);
        }

        //hemoglucotest1
        $hemoglucotest1_is_valid = (mb_strlen($hemoglucotest1) === 0) ||
        (
            $this->isNumeric($hemoglucotest1) == true &&
            (float) $hemoglucotest1 >= 0 && (float) $hemoglucotest1 <= 2000
        ) ? true : false;

        if(!$hemoglucotest1_is_valid){
            throw new Exception('Campo Hemoglucotest (mg/dl) no valido. Valor:'.$saturacion1);
        }

        //metodo2_1
        $metodo2_1_is_valid = (mb_strlen($metodo2_1) === 0) ||
        (
            in_array($metodo2_1, array('1','2','3','4','5'))
        ) ? true : false;

        if(!$metodo2_1_is_valid){
            throw new Exception('Campo Metodo O2 no valido. Valor:'.$metodo2_1);
        }

        //dolor1
        $dolor1_is_valid = (mb_strlen($dolor1) === 0) ||
        (
            $this->isInteger($dolor1) == true &&
            (int) $dolor1 >= 1 && (int) $dolor1 <= 10
        ) ? true : false;

        if(!$dolor1_is_valid){
            throw new Exception('Campo Dolor no valido. Valor:'.$dolor1);
        }

        //fio2_1
        $fio2_1_is_valid = (mb_strlen($fio2_1) === 0) ||
        (
            in_array($fio2_1, array('21','24','26','28','32','35','36','40','45','50','60','70','90'))
        ) ? true : false;

        if(!$fio2_1_is_valid){
            throw new Exception('Campo FIO2 no valido. Valor:'.$fio2_1);
        }

        if($sub_categoria == 4){

            //pam
            $pam_is_valid = (mb_strlen($pam) === 0) ? true : $this->isNumeric($pam) == true;
            if(!$pam_is_valid){
                throw new Exception('Campo pam no valido. '.$pam);
            }

            //temp_central 
            $temp_central_is_valid = (mb_strlen($temp_central) === 0) ? true : $this->isNumeric($temp_central) == true && (float) $temp_central >=0 && (float) $temp_central <=50;

            if(!$temp_central_is_valid){
                throw new Exception('Campo temp_central no valido. '.$temp_central);
            }

            //pvc
            $pvc_is_valid = (mb_strlen($pvc) === 0) ? true : $this->isNumeric($pvc) == true && (float) $pvc >=0 && (float) $pvc <=30;

            if(!$pvc_is_valid){
                throw new Exception('Campo pvc no valido. '.$pvc);
            }

            //pcp  
            $pcp_is_valid = (mb_strlen($pcp) === 0) ? true : $this->isNumeric($pcp) == true && (float) $pcp >=0 && (float) $pcp <=50;
            if(!$pcp_is_valid){
                throw new Exception('Campo pcp no valido. '.$pcp);
            }

            //peso  
            $peso_is_valid = (mb_strlen($peso) === 0) ? true : $this->isNumeric($peso) == true && (float) $peso >=0 && (float) $peso <=700;
            if(!$peso_is_valid){
                throw new Exception('Campo peso no valido. '.$peso);
            }

            //gc_ic
            $gc_ic_is_valid = (mb_strlen($gc_ic) === 0) ? true : $this->isNumeric($gc_ic) == true && (float) $gc_ic >=0 && (float) $gc_ic <=30;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo gc_ic no valido. '.$gc_ic);
            }

            //rvs_rvp
            $rvs_rvp_is_valid = (mb_strlen($rvs_rvp) === 0) ? true : $this->isNumeric($rvs_rvp) == true && (float) $rvs_rvp >=0 && (float) $rvs_rvp <=10000;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo rvs_rvp no valido. '.$rvs_rvp);
            }

            //gcs_ramsa_sas
            $gcs_ramsa_sas_is_valid = true;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo gcs_ramsa_sas no valido. '.$gcs_ramsa_sas);
            }

        }



        //* PERSISTE DATOS */
        $signosVitales = new HojaEnfermeriaControlSignoVital;
        $signosVitales->caso = $caso_id;
        $signosVitales->usuario_ingresa = $user_id;
        $signosVitales->fecha_creacion = Carbon::now();
        $signosVitales->visible = true;
        $signosVitales->horario1 = $horario1;
        $signosVitales->presion_arterial1 = $presion_arterial1;
        $signosVitales->presion_arterial1dia = $presion_arterial1dia;
        $signosVitales->pulso1 = $pulso1;
        $signosVitales->frec_respiratoria1 = $frec_respiratoria1;
        $signosVitales->temp_axilo1 = $temp_axilo1;
        $signosVitales->temp_rectal = $temp_rectal;
        $signosVitales->saturacion1 = $saturacion1;
        $signosVitales->hemoglucotest1 = $hemoglucotest1;
        $signosVitales->presion_arterial_media = $presion_arterial_media;
        $signosVitales->estado_conciencia = $estado_conciencia1;
        //$signosVitales->peso = $peso;
        $signosVitales->indglasgow1 = $indglasgow1;
        $signosVitales->fio2_1 = $fio2_1;
        $signosVitales->metodo2_1 = $metodo2_1;
        $signosVitales->dolor1 = $dolor1;

        //nuevos
        $signosVitales->pam = $pam;
        $signosVitales->temp_central = $temp_central; 
        $signosVitales->pvc = $pvc;
        $signosVitales->pcp = $pcp; 
        if ($peso != null) {
            $signosVitales->peso = $peso; 
        }       
        $signosVitales->gc_ic = $gc_ic; 
        $signosVitales->rvs_rvp = $rvs_rvp; 
        $signosVitales->gcs_ramsa_sas = $gcs_ramsa_sas;


        $signosVitales->latidos_cardio_fetales = $latidos;
        $signosVitales->movimientos_fetales = $movimientos;
        $signosVitales->utero = $utero;
        $signosVitales->dinamica_uterina = $dinamica;
        $signosVitales->flujo_genital = $flujo;
        if($id_indicacion_medica != ""){
            $indicacionExiste = PlanificacionIndicacionMedica::where('caso',$caso_id)->where('id',$id_indicacion_medica)->where('visible',true)->first();
            if(empty($indicacionExiste)){
                throw new Exception('Error al ingresar los signos vitales');
            }
            $signosVitales->id_indicacion = $id_indicacion_medica;
        }
        $signosVitales->save();

        
        if($id_indicacion_medica == ""){
            //comprobar si tiene planificacion para la hora
            $planificacion = $this->getPlanificacionParaLaHora($caso_id, $hora);

            if(count($planificacion) > 0){
                $id_atencion = $planificacion[0]->id;
                // Ver si la hora ya esta checkeada 
                $atencion_old = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora)
                ->where("id_atencion", $id_atencion)
                ->whereDate('fecha_creacion','=',$horario1)
                ->first();
    
                if($atencion_old === null){
                    $atencion_new =  new HojaEnfermeriaCuidadoEnfermeriaAtencion();
                    $atencion_new->id_atencion = $id_atencion;
                    $atencion_new->usuario = $user_id;
                    $atencion_new->fecha_creacion = $horario1;
                    $atencion_new->horario = $hora;
                    $atencion_new->realizado = true;
                    $atencion_new->visible = true;
                    $atencion_new->save();
                } 
                else {
                    $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_old->id);
                    $update->visible = true;
                    $update->save();
                }
    
            }else {
                //ver si existe planificacion hora nula
                $plan_hora_nula = $this->getPlanificacionParaLaHoraNula($caso_id);
    
                if(count($plan_hora_nula) > 0){
                    $id_atencion = $plan_hora_nula[0]->id;
    
                    //Ver si la hora ya esta checkeada 
                    $atencion_old = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                    where("horario", $hora)
                    ->where("id_atencion", $id_atencion)
                    ->whereDate('fecha_creacion','=',$horario1)
                    ->first();
    
    
                    if($atencion_old === null){
                        $atencion_new =  new HojaEnfermeriaCuidadoEnfermeriaAtencion();
                        $atencion_new->id_atencion = $id_atencion;
                        $atencion_new->usuario = $user_id;
                        $atencion_new->fecha_creacion = $horario1;
                        $atencion_new->horario = $hora;
                        $atencion_new->realizado = true;
                        $atencion_new->visible = true;
                        $atencion_new->save();
                    } 
                    else {
                        $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_old->id);
                        $update->visible = true;
                        $update->save();
                    }
    
                }
    
            }
        }else{
             //comprobar si tiene planificacion para la hora
             $planificacion = $this->getIndicacionParaLaHora($caso_id, $hora, $id_indicacion_medica);

            if(count($planificacion) > 0){
                $id_indicacion = $planificacion[0]->id;
                // Ver si la hora ya esta checkeada 
                $atencion_old = HojaEnfermeriaEnfermeriaIndicacionMedica::where("horario", $hora)
                    ->where("id_indicacion", $id_indicacion_medica)
                    ->whereDate('fecha_creacion','=',$horario1)
                    ->first();
    
                if(empty($atencion_old)){
                    $atencion_new =  new HojaEnfermeriaEnfermeriaIndicacionMedica();
                    $atencion_new->id_indicacion = $id_indicacion_medica;
                    $atencion_new->usuario = $user_id;
                    $atencion_new->visible = true;
                    $atencion_new->fecha_creacion = $horario1;
                    $atencion_new->horario = $hora;
                    $atencion_new->realizado = true;
                    $atencion_new->save();
                } 
                else {
                    $update = HojaEnfermeriaEnfermeriaIndicacionMedica::find($atencion_old->id);
                    $update->visible = true;
                    $update->save();
                }
    
            }else {

                $plan_hora_nula_indicacion = $this->getIndicacionParaLaHoraNula($caso_id,$id_indicacion_medica);
                //ver si existe planificacion hora nula
                    //Ver si la hora ya esta checkeada 
                    if(count($plan_hora_nula_indicacion) > 0){
                        $atencion_old = HojaEnfermeriaEnfermeriaIndicacionMedica::
                        where("horario", $hora)
                        ->where("id_indicacion", $id_indicacion_medica)
                        ->whereDate('fecha_creacion','=',$horario1)
                        ->first();
        
                        if(empty($atencion_old)){
                            
                            $atencion_new =  new HojaEnfermeriaEnfermeriaIndicacionMedica();
                            $atencion_new->id_indicacion = $id_indicacion_medica;
                            $atencion_new->usuario = $user_id;
                            $atencion_new->visible = true;
                            $atencion_new->fecha_creacion = $horario1;
                            $atencion_new->horario = $hora;
                            $atencion_new->realizado = true;
                            $atencion_new->save();
                        } 
                        else {
                            $update = HojaEnfermeriaEnfermeriaIndicacionMedica::find($atencion_old->id);
                            $update->visible = true;
                            $update->save();
                        }
                    }else{
                        throw new Exception('Error al ingresar los signos vitales');
                    }
            }

        }

        return $signosVitales;
    }

    public function guardarGlasgow($request, $formulario_modificar,$tipo){
		if($request->arrayGlasgowx2 != '' && count(explode(",", $request->arrayGlasgowx2)) == 3){
			$glasgowForm = explode(",", $request->arrayGlasgowx2);
			//valida si se envian datos que no corresponden
			$validarGlasgow = ["1" =>'1','2','3','4','5','6'];
			$existe = array_diff($glasgowForm, $validarGlasgow);           

            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis','Editar'];
            $existe = in_array($request->tipo, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

			/* if(count($existe) > 0){
				return response()->json(["error" => "Error al actualizar la informacion del signo vital glasgow"]);   
			} */
            if (isset($request->id)) {
                //Esto tiene un formulario de signos vitales asociados y denro de este viene el id del formulario glasgow
                $id_formulario_sighnos_vitales = Crypt::decrypt(strip_tags($request->id));
                $formulario_signos_vitales = HojaEnfermeriaControlSignoVital::find($id_formulario_sighnos_vitales);
                $request->id_glasgow = "";
                if ($formulario_signos_vitales ) {
                    $request->id_glasgow = $formulario_signos_vitales->indglasgow1;
                }
                
            }
            
            $glasgow = new Glasgow;
            
            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->id_glasgow && ($request->tipo == 'En Curso' || $request->tipo == 'Editar') ){
                //si es En curso, se debe consultar por el id 
                if ($request->id_glasgow) {                    
                    //buscar el id del formulario y comprobar si esta visible
                    $glasgowAnterior = Glasgow::where('id_formulario_escala_glasgow',$request->id_glasgow)
                        ->where(function($q){
                            $q->where('visible', true)
                            ->orWhereNull('visible');
                        })->first();

                    if ($glasgowAnterior) {
                        //Al final, si se trae algun tipo de valor de glasgow, este se debe modificar, de lo contrario se omite y se asume que es nuevo
                        $glasgowAnterior->usuario_modifica = Auth::user()->id;
                        $glasgowAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                        $glasgowAnterior->visible = false;
                        $glasgowAnterior->save();
                        
                    }else{
                        //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                        return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                    }
                }
            }


			$glasgow->usuario_responsable = Auth::user()->id;
			$glasgow->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
			$glasgow->caso = $formulario_modificar->caso;
			$glasgow->apertura_ocular = $glasgowForm[0];
			$glasgow->respuesta_verbal = $glasgowForm[1];
			$glasgow->respuesta_motora = $glasgowForm[2];
			$glasgow->total = $glasgowForm[0] + $glasgowForm[1] + $glasgowForm[2];
			$glasgow->tipo = $tipo;
            if(isset($glasgowAnterior)){
                //si existe algun tipo de dato en glasgow, este debe significar que tenia un anterior y debe ser cambiado
                $glasgow->id_anterior = $glasgowAnterior->id_formulario_escala_glasgow;
                $glasgow->tipo = $glasgowAnterior->tipo;
            }else{
                $glasgow->tipo = $tipo;
            }

			return $glasgow;
		}
	}

    public function modificarSignoVital($request){
        $fecha_signo_vital = Carbon::parse(strip_tags($request->fecha))->format('Y-m-d');
        $dia_signo_vital = (isset($request->diasigno)) ? strip_tags($request->diasigno) : null;
        $signo_vital_id = (isset($request->id)) ? Crypt::decrypt(strip_tags(trim(strip_tags($request->id)))) : null;
        $presion_arterial1 = (isset($request->arterial1)) ? strip_tags(trim($request->arterial1)) : "";
        $presion_arterial1dia = (isset($request->arterial1dia)) ? strip_tags(trim($request->arterial1dia)) : "";
        $pulso1 = (isset($request->pulso1)) ? strip_tags(trim($request->pulso1)) : "";
        $frec_respiratoria1 = (isset($request->respiratoria1)) ? strip_tags(trim($request->respiratoria1)) : "";
        $temp_axilo1 = (isset($request->axilo1)) ? strip_tags(trim($request->axilo1)) : "";
        $temp_rectal= (isset($request->rectal)) ? strip_tags(trim($request->rectal)) : "";
        $saturacion1 = (isset($request->saturacion1)) ? strip_tags(trim($request->saturacion1)) : "";
        $hemoglucotest1 = (isset($request->hemoglucotest1)) ? strip_tags(trim($request->hemoglucotest1)) : "";
        $glasgow1 = (isset($request->glasgow1) && trim($request->glasgow1) != '') ? strip_tags(trim($request->glasgow1)) : null;
        $fio2_1 = (isset($request->fio1)) ? strip_tags(trim($request->fio1)) : "";
        $metodo2_1 = (isset($request->metodo1)) ? strip_tags(trim($request->metodo1)) : "";
        $dolor1 = (isset($request->dolor1)) ? strip_tags(trim($request->dolor1)) : "";
        $user_id = Auth::user()->id;
        $horario1 = null;  
        $hora_new = Carbon::parse($horario1)->format('H');

        $editado = HojaEnfermeriaControlSignoVital::find($signo_vital_id);
        $hora_old = Carbon::parse($editado->horario1)->format('H');
        $caso_id = $editado->caso;

        $presion_arterial_media = (isset($request->arterialmedia) && $request->arterialmedia) ? strip_tags($request->arterialmedia) : '';
        $estado_conciencia1 = (isset($request->estado_conciencia) && $request->estado_conciencia) ? strip_tags($request->estado_conciencia) : '';
        $peso = (isset($request->peso) && $request->peso) ? $request->peso : 0;

        $latido = (isset($request->latido) && $request->latido) ? $request->latido : '';
        $movimiento = (isset($request->movimiento) && $request->movimiento) ? $request->movimiento : '';
        $utero = (isset($request->utero) && $request->utero) ? $request->utero : '';
        $dinamica = (isset($request->dinamica) && $request->dinamica) ? $request->dinamica : '';
        $flujo = (isset($request->flujo) && $request->flujo) ? $request->flujo : '';

        //nuevos
        $sub_categoria = $this->getSubCategoria($caso_id);
        $pam  = (isset($request->pam) && $sub_categoria == 4) ? trim(strip_tags($request->pam)) : "";
        $temp_central = (isset($request->temp_central) && $sub_categoria == 4) ? trim(strip_tags($request->temp_central)) : "";
        $pvc = (isset($request->pvc) && $sub_categoria == 4) ? trim(strip_tags($request->pvc)) : "";
        $pcp = (isset($request->pcp) && $sub_categoria == 4) ? trim(strip_tags($request->pcp)) : "";
        $peso = (isset($request->peso) && $sub_categoria == 4) ? trim(strip_tags($request->peso)) : null;
        $gc_ic = (isset($request->gc_ic) && $sub_categoria == 4) ? trim(strip_tags($request->gc_ic)) : "";
        $rvs_rvp = (isset($request->rvs_rvp) && $sub_categoria == 4) ? trim(strip_tags($request->rvs_rvp)) : "";
        $gcs_ramsa_sas = (isset($request->gcs_ramsa_sas) && $sub_categoria == 4) ? trim(strip_tags($request->gcs_ramsa_sas)) : "";

        /* VALIDA DOMINIO DE LO QUE LLEGA */

        //horario1
        $horario1_is_valid = true;
        try {
            $horario1 = (isset($dia_signo_vital) && isset($request->horario1)) ? Carbon::parse($dia_signo_vital." ".$request->horario1)->format('Y-m-d H:i:s') : null ;  
        }catch (Exception $e){
            $horario1_is_valid = false;
        }

        if(!$horario1_is_valid){
            throw new Exception('Campo Horario no valido. Valor:'.$horario1);
        }


        //signo_vital_id
        $signo_vital_id_is_valid = (
            isset($signo_vital_id) && 
            $this->isInteger($signo_vital_id) == true
        ) ? true : false;

        if(!$signo_vital_id_is_valid){
            throw new Exception('Campo signo_vital_id no valido. Valor:'.$signo_vital_id);
        }

        //presion_arterial1
        $presion_arterial1_is_valid = (mb_strlen($presion_arterial1) === 0) || 
        ( 
            $this->isInteger($presion_arterial1) == true &&
            (int) $presion_arterial1 >= 0 && (int) $presion_arterial1 <= 500
        ) ? true : false;

        if(!$presion_arterial1_is_valid){
            throw new Exception('Campo P.Arterial Sis. (mmHg) no valido. Valor:'.$presion_arterial1);
        }

        //presion_arterial1dia
        $presion_arterial1dia_is_valid = (mb_strlen($presion_arterial1dia) === 0) ||
        ( 
            $this->isInteger($presion_arterial1dia) == true &&
            (int) $presion_arterial1dia >= 0 && (int) $presion_arterial1dia <= 500
        ) ? true : false;

        if(!$presion_arterial1dia_is_valid){
            throw new Exception('Campo P.Arterial Dias. (mmHg) no valido. Valor:'.$presion_arterial1dia);
        }

        //pulso1
        $pulso1_is_valid = (mb_strlen($pulso1) === 0) ||
        (
            $this->isInteger($pulso1) == true &&
            (int) $pulso1 >= 0 && (int) $pulso1 <= 500
        ) ? true : false;

        if(!$pulso1_is_valid){
            throw new Exception('Campo Frecuencia cardiaca (Lpm) no valido. Valor:'.$pulso1);
        }

        //frec_respiratoria1
        $frec_respiratoria1_is_valid = (mb_strlen($frec_respiratoria1) === 0) ||
        (
            $this->isInteger($frec_respiratoria1) == true &&
            (int) $frec_respiratoria1 >= 0 && (int) $frec_respiratoria1 <= 100
        ) ? true : false;

        if(!$frec_respiratoria1_is_valid){
            throw new Exception('Campo Frec. res. (Rpm) no valido. Valor:'.$frec_respiratoria1);
        }

        //temp_axilo1
        $temp_axilo1_is_valid = (mb_strlen($temp_axilo1) === 0) ||
        (
            $this->isNumeric($temp_axilo1) == true &&
            (float) $temp_axilo1 >= 0 && (float) $temp_axilo1 <= 50
        ) ? true : false;

        if(!$temp_axilo1_is_valid){
            throw new Exception('Campo Temp. Axilar (°C) no valido. Valor:'.$temp_axilo1);
        }

        //temp_rectal
        $temp_rectal_is_valid = (mb_strlen($temp_rectal) === 0) ||
        (
            $this->isNumeric($temp_rectal) == true &&
            (float) $temp_rectal >= 0 && (float) $temp_rectal <= 50
        ) ? true : false;

        if(!$temp_rectal_is_valid){
            throw new Exception('Campo Temp. Rectal (°C) no valido. Valor:'.$temp_rectal);
        }

        //saturacion1
        $saturacion1_is_valid = (mb_strlen($saturacion1) === 0) ||
        (
            $this->isNumeric($saturacion1) == true &&
            (float) $saturacion1 >= 0 && (float) $saturacion1 <= 100
        ) ? true : false;

        if(!$saturacion1_is_valid){
            throw new Exception('Campo Saturación de oxígeno (%) no valido. Valor:'.$saturacion1);
        }

        //hemoglucotest1
        $hemoglucotest1_is_valid = (mb_strlen($hemoglucotest1) === 0) ||
        (
            $this->isNumeric($hemoglucotest1) == true &&
            (float) $hemoglucotest1 >= 0 && (float) $hemoglucotest1 <= 2000
        ) ? true : false;

        if(!$hemoglucotest1_is_valid){
            throw new Exception('Campo Hemoglucotest (mg/dl) no valido. Valor:'.$saturacion1);
        }

        //metodo2_1
        $metodo2_1_is_valid = (mb_strlen($metodo2_1) === 0) ||
        (
            in_array($metodo2_1, array('1','2','3','4','5'))
        ) ? true : false;

        if(!$metodo2_1_is_valid){
            throw new Exception('Campo Metodo O2 no valido. Valor:'.$metodo2_1);
        }

        //dolor1
        $dolor1_is_valid = (mb_strlen($dolor1) === 0) ||
        (
            $this->isInteger($dolor1) == true &&
            (int) $dolor1 >= 1 && (int) $dolor1 <= 10
        ) ? true : false;

        if(!$dolor1_is_valid){
            throw new Exception('Campo Dolor no valido. Valor:'.$dolor1);
        }

        //fio2_1
        $fio2_1_is_valid = (mb_strlen($fio2_1) === 0) ||
        (
            in_array($fio2_1, array('21','24','26','28','32','35','36','40','45','50','60','70','90'))
        ) ? true : false;

        if(!$fio2_1_is_valid){
            throw new Exception('Campo FIO2 no valido. Valor:'.$fio2_1);
        }

        if($sub_categoria == 4){

            //pam
            $pam_is_valid = (mb_strlen($pam) === 0) ? true : $this->isNumeric($pam) == true;
            if(!$pam_is_valid){
                throw new Exception('Campo pam no valido. '.$pam);
            }

            //temp_central 
            $temp_central_is_valid = (mb_strlen($temp_central) === 0) ? true : $this->isNumeric($temp_central) == true && (float) $temp_central >=0 && (float) $temp_central <=50;

            if(!$temp_central_is_valid){
                throw new Exception('Campo temp_central no valido. '.$temp_central);
            }

            //pvc
            $pvc_is_valid = (mb_strlen($pvc) === 0) ? true : $this->isNumeric($pvc) == true && (float) $pvc >=0 && (float) $pvc <=30;

            if(!$pvc_is_valid){
                throw new Exception('Campo pvc no valido. '.$pvc);
            }

            //pcp  
            $pcp_is_valid = (mb_strlen($pcp) === 0) ? true : $this->isNumeric($pcp) == true && (float) $pcp >=0 && (float) $pcp <=50;
            if(!$pcp_is_valid){
                throw new Exception('Campo pcp no valido. '.$pcp);
            }

            //peso  
            $peso_is_valid = (mb_strlen($peso) === 0) ? true : $this->isNumeric($peso) == true && (float) $peso >=0 && (float) $peso <=700;
            if(!$peso_is_valid){
                throw new Exception('Campo peso no valido. '.$peso);
            }

            //gc_ic
            $gc_ic_is_valid = (mb_strlen($gc_ic) === 0) ? true : $this->isNumeric($gc_ic) == true && (float) $gc_ic >=0 && (float) $gc_ic <=30;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo gc_ic no valido. '.$gc_ic);
            }

            //rvs_rvp
            $rvs_rvp_is_valid = (mb_strlen($rvs_rvp) === 0) ? true : $this->isNumeric($rvs_rvp) == true && (float) $rvs_rvp >=0 && (float) $rvs_rvp <=10000;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo rvs_rvp no valido. '.$rvs_rvp);
            }

            //gcs_ramsa_sas
            $gcs_ramsa_sas_is_valid = true;
            if(!$gc_ic_is_valid){
                throw new Exception('Campo gcs_ramsa_sas no valido. '.$gcs_ramsa_sas);
            }

        }


        $editado->usuario_modifica = $user_id;
        $editado->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
        $editado->horario1 = $horario1;
        $editado->presion_arterial1 = $presion_arterial1;
        $editado->presion_arterial1dia = $presion_arterial1dia;
        $editado->pulso1 = $pulso1;
        $editado->frec_respiratoria1 = $frec_respiratoria1;
        $editado->temp_axilo1 = $temp_axilo1;
        $editado->temp_rectal = $temp_rectal;
        $editado->saturacion1 = $saturacion1;
        $editado->hemoglucotest1 = $hemoglucotest1;
        $editado->glasgow1 = $glasgow1;
        $editado->fio2_1 = $fio2_1;
        $editado->metodo2_1 = $metodo2_1;
        $editado->dolor1 = $dolor1;
        $editado->presion_arterial_media = $presion_arterial_media;
        $editado->estado_conciencia = $estado_conciencia1;
        $editado->peso = $peso;
        $editado->latidos_cardio_fetales = $latido;
        $editado->movimientos_fetales = $movimiento;
        $editado->utero = $utero;
        $editado->dinamica_uterina = $dinamica;
        $editado->flujo_genital = $flujo;

        //nuevos
        if($sub_categoria == 4){
            $editado->pam = $pam;
            $editado->temp_central = $temp_central; 
            $editado->pvc = $pvc;
            $editado->pcp = $pcp; 
            $editado->gc_ic = $gc_ic; 
            $editado->rvs_rvp = $rvs_rvp; 
            $editado->gcs_ramsa_sas = $gcs_ramsa_sas;
        }


        $editado->save();
     
        if($request->arrayGlasgowx2 != ''){
            //Aqui esta guardando los datos.
            $glasgow = $this->guardarGlasgow($request, $editado,'En Curso');
            $glasgow->visible = true;
            $glasgow->save();
        }else{
            //Aqui solo van cuando no tiene informacion para actualizar los signos vitales y se falsea 
            $glasgowEliminar = Glasgow::find($editado->indglasgow1);
            if($glasgowEliminar != ''){
				$glasgowEliminar->update([
					'usuario_modifica' => Auth::user()->id,
					'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
					'visible' => false
				]);
            }
        }
            $indglasgow1 = (isset($glasgow) && $glasgow->id_formulario_escala_glasgow) ? $glasgow->id_formulario_escala_glasgow : null; 
            $signoVitalYaEditado = HojaEnfermeriaControlSignoVital::find($editado->id);
            $signoVitalYaEditado->indglasgow1 = $indglasgow1;
            $signoVitalYaEditado->save();

        if($hora_old !== $hora_new){

            //obtener planificaciones
            $planificacion_hora_old = $this->getPlanificacionParaLaHora($caso_id, $hora_old);
            $planificacion_hora_new = $this->getPlanificacionParaLaHora($caso_id, $hora_new);
            $planificacion_hora_nula = $this->getPlanificacionParaLaHoraNula($caso_id);


            //obtener id_atencion (id de planificaciones)
            $id_atencion_old =  (count($planificacion_hora_old) > 0) ? $planificacion_hora_old[0]->id  : null ;
            $id_atencion_new =  (count($planificacion_hora_new) > 0) ? $planificacion_hora_new[0]->id : null;
            $id_atencion_null = (count($planificacion_hora_nula) > 0) ? $planificacion_hora_nula[0]->id : null;


            //inicialización variables resultset de atenciones (checks)
            $atencion_plan_hora_old = null;
            $atencion_plan_hora_nula_old = null;
            $atencion_plan_hora_new = null;
            $atencion_plan_hora_nula_new = null;

            //obtener atenciones (checks)
            if(isset($id_atencion_old)){
                $atencion_plan_hora_old = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora_old)
                ->where("id_atencion", $id_atencion_old)
                ->whereDate('fecha_creacion','=', $horario1)
                ->first();

            }

            if(isset($id_atencion_new)){
                $atencion_plan_hora_new = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora_new)
                ->where("id_atencion", $id_atencion_new)
                ->whereDate('fecha_creacion','=', $horario1)
                ->first();
                
            }

            if(isset($id_atencion_null)){

                $atencion_plan_hora_nula_old = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora_old)
                ->where("id_atencion", $id_atencion_null)
                ->whereDate('fecha_creacion','=', $horario1)
                ->first();

                $atencion_plan_hora_nula_new = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora_new)
                ->where("id_atencion", $id_atencion_null)
                ->whereDate('fecha_creacion','=', $horario1)
                ->first();

            }

            //ACTUALIZAR CHECKS ATENCIÓN

            /* HORA OLD */

            //ver si existe un check plan hora_old. Si existe entonces:
            if(isset($atencion_plan_hora_old)){

                //dejar check hora_old como visible false
                $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_old->id);
                $update->visible = false;
                $update->save();

            }

            //ver si existe un check plan hora_old nulo. Si existe entonces:
            else if
            (isset($atencion_plan_hora_nula_old)){

                //dejar check hora_old como visible false
                $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_nula_old->id);
                $update->visible = false;
                $update->save();                

            }

            /* HORA NEW */

            //verificar si existe planificacion hora_new
            if(isset($id_atencion_new)){

                //ver si existe un check plan hora_new. Si existe entonces:
                if(isset($atencion_plan_hora_new)){
                    $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_new->id);
                    $update->visible = true;
                    $update->save();
                }

                else {

                    //se crea una atencion plan hora_new nula
                    $new = new HojaEnfermeriaCuidadoEnfermeriaAtencion();
                    $new->id_atencion = $id_atencion_new;
                    $new->usuario = $user_id;
                    $new->fecha_creacion = $horario1;
                    $new->horario = $hora_new;
                    $new->realizado = true;
                    $new->visible = true;
                    $new->save();

                }


            }

            //verificar si existe planificacion hora nula
            else if(isset($id_atencion_null)) {

                //ver si existe un check plan hora nula hora new . Si existe entonces:
                if(isset($atencion_plan_hora_nula_new)){
                    $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_nula_new->id);
                    $update->visible = true;
                    $update->save();
                }
                else {

                    //se crea una atencion plan hora_new nula
                    $new = new HojaEnfermeriaCuidadoEnfermeriaAtencion();
                    $new->id_atencion = $id_atencion_null;
                    $new->usuario = $user_id;
                    $new->fecha_creacion = $horario1;
                    $new->horario = $hora_new;
                    $new->realizado = true;
                    $new->visible = true;
                    $new->save();

                }
                
            }

        }

    }

    public function eliminarSignoVital($request){

        $fecha_signo_vital = Carbon::parse(strip_tags($request->fecha_signo_vital))->format('Y-m-d H:i:s');
        $signo_vital_id = Crypt::decrypt(strip_tags($request->id));
        $signo_vital = HojaEnfermeriaControlSignoVital::find($signo_vital_id);

        if($signo_vital){

            //inicialización variables
            $mantener_check_tras_eliminar_signo = (strip_tags($request->mantener_check_tras_eliminar_signo)=== "true") ? true : false;
            
            $atencion = null;
            $atencion_plan = null;
            $planificacion = null;
            $otros_signos_flag = false;

            //borra control de signo vital
            $signo_vital->usuario_modifica = Auth::user()->id;
            $signo_vital->fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");
            $signo_vital->visible = false;
            $signo_vital->save();
    
            $caso_id = $signo_vital->caso;
            $hora = Carbon::parse($signo_vital->horario1)->format('H');

            //obtener planificaciones
            $planificacion_hora = $this->getPlanificacionParaLaHora($caso_id, $hora);
            $planificacion_hora_nula = $this->getPlanificacionParaLaHoraNula($caso_id);

            //obtener atencion (check)
            if
            (count($planificacion_hora) > 0){
                $planificacion = $planificacion_hora[0];
                $atencion_plan = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora)
                ->where("id_atencion", $planificacion->id)
                ->whereDate('fecha_creacion','=', $fecha_signo_vital)
                ->first();

            }

            else if
            (count($planificacion_hora_nula) > 0){

                $planificacion = $planificacion_hora_nula[0];
                $atencion_plan = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora)
                ->where("id_atencion", $planificacion->id)
                ->whereDate('fecha_creacion','=', $fecha_signo_vital)
                ->first();

            }

            //ver si existe un check plan hora. Si existe entonces:
            if(isset($atencion_plan)){

                //comprobar si realmente existen otros signos en la hora
                $otros_signos_flag = $this->getOtrosSignosParaHora($caso_id, $signo_vital_id, $hora, strip_tags($request->fecha_signo_vital))->otros_signos_flag;

                //dejar check hora segun respuesta
                $atencion = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan->id);
                $check = ($mantener_check_tras_eliminar_signo === true && $otros_signos_flag === true);
                $atencion->visible = $check;
                $atencion->save();

            }    

            //sanitizacion objetos (solo mostrar atributos)
            $atencion =  ($atencion) ? (object) $atencion->first()->toArray() : null;
            $signo_vital =  ($signo_vital) ? (object) $signo_vital->first()->toArray() : null;
    
            return (object) array(
                'signo_vital' => $signo_vital,
                'planificacion' => $planificacion,
                'atencion' => $atencion,
                'otros_signos' => $otros_signos_flag
            );

        }
        else {
            throw new Exception('Ha ocurrido un error');
        }

    }


    public function getAllSignosVitalesCuidadosDelDia($request){

        Log::info($request);

        $caso_id = trim(strip_tags($request->caso_id));
        $fecha = Carbon::parse(trim(strip_tags($request->fecha)))->format('Y-m-d H:i:s');

        /* CAPTURAR LO QUE LLEGA */
        $caso_id = (isset($caso_id) && trim($caso_id) !== "") ?
        trim(strip_tags($caso_id)) : null;

        $horario = (isset($horario) && trim($horario) !== "") ?
        trim(strip_tags($horario)) : null;

        /* VALIDACIONES */
        if(!isset($caso_id)){ throw new Exception('Error con caso_id.'); }
        $indicacion =" ";
        if($request->id_indicacion_medica != ""){
            $indicacion = " and i.id_indicacion = ".$request->id_indicacion_medica." ";
        }else{
            $indicacion =" and i.id_indicacion is null ";
        }

        $query = "select distinct on (date_trunc('hour', i.horario1)) extract(hour from i.horario1) as hora, extract(MINUTE from i.horario1) as minutos, i.horario1 as horario1, i.presion_arterial1 as presion_alterial_sis, i.presion_arterial1dia as presion_alterial_dia, i.pulso1 as frecuencia_cardiaca, i.frec_respiratoria1 as frecuencia_respiratoria, i.temp_axilo1 as temperatura_axilo, i.id as signos_vitales_id, 
        i.saturacion1 as saturacion1, i.hemoglucotest1 as hemoglucotest1, glasgow.total as glasgow1, i.metodo2_1 as metodo2_1, i.dolor1 as dolor1,
        i.fio2_1 as fio2_1,
		i.temp_rectal, i.estado_conciencia as estado_conciencia1, i.peso as peso1, i.presion_arterial_media as presion_arterial_media1,
        i.latidos_cardio_fetales as latidos1, i.movimientos_fetales as movimientos1, i.utero as utero1,
        i.dinamica_uterina as dinamica1, i.flujo_genital as flujos1
        from formulario_hoja_enfermeria_signos_vitales as i
        inner join usuarios as u on u.id = i.usuario_ingresa
        left join formulario_escala_glasgow as glasgow on glasgow.id_formulario_escala_glasgow = i.indglasgow1
        where i.caso = ? and i.visible = true and i.horario1::date = ? $indicacion
        order by date_trunc('hour', i.horario1), i.fecha_creacion  DESC";

        $signos = DB::select($query, [$caso_id,$fecha]);        

        foreach ($signos as $key => $s) {
            //encripta el id del caso para temas de seguridad.
            $s->caso_id = Crypt::encrypt($caso_id);
            $s->signos_vitales_id = Crypt::encrypt($s->signos_vitales_id);

        }
        return $signos;

    }


}