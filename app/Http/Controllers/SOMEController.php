<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 1/21/15
 * Time: 3:12 PM
 */
namespace App\Http\Controllers;
class SOMEController extends Controller {
    private $csv = array();
    private $lista_bd = array();
    private $lista_csv = array();
    private $para_agregar = array();
    private $para_quitar = array();
    private $lista_negra = [
      /*  'Quimioterapia  (Ambulatorio ...)',
        'UMA',
        'Hemodinamia',
        'SALA INGRESO EGRESO 4TO PISO',
        'UCAM  (Ambulatorio ...)',
        'SQP Ambulatorio  (Ambulatorio ...)',
        'Diálisis  (Ambulatorio ...)',
        'Hemodinamia',
        'UCAM  (Ambulatorio ...)',
        'Endoscopia y Varios (Ambulatorio ...)',*/
    ];

    private $caso_n;
    private $msg;
    private $establecimiento_obj;
    private $cama_libre;
    private $c_unidad;
    private $fechaCambio;
    private $caso_nuevo;
    private $fechaIng;
    private $traslado_externo;
    private $fechaCierre;

    private $n_unidad;
    private $cama;
    private $sala;
    private $id_sala_some;

    private $lista_permitidos_quilpue = [
        2,10,3,4,6,11,18,5,1,19,20,
        '2','10','3','4','6','11','18','5','1','19','20',
        'HQPE-AC-086',
        'HQPE-AC-095',
		'HQPE-AD-01',
		'HQPE-AC-084',
		'HQPE-AC-089',
		'HQPE-HD-01'

    ];

    public static $previsiones = [
        'A' => 'FONASA A',
        'B' => 'FONASA B',
        'BANCO DEL ESTADO' => 'OTROS',
        'BANMEDICA' => 'ISAPRE',
        'C' => 'FONASA C',
        'CAPREDENA' => 'CAPREDENA',
        'CHUQUICAMATA' => 'ISAPRE',
        'CIGNA SALUD' => 'OTROS',
        'COLMENA' => 'ISAPRE',
        'CONSALUD' => 'ISAPRE',
        'Convenio' => 'CONVENIO',
        'Cruz Blanca' => 'ISAPRE',
        'D' => 'FONASA D',
        'DIPRECA' => 'DIPRECA',
        'EL TENIENTE' => 'OTROS',
        'FERROSALUD' => 'ISAPRE',
        'FUNDACION' => 'ISAPRE',
        'GALENICA' => 'OTROS',
        'INSTSALUD' => 'OTROS',
        'ISAMEDICA' => 'OTROS',
        'ISAPRE' => 'ISAPRE',
        'ISAPRES' => 'ISAPRE',
        'LA CUMBRE' => 'OTROS',
        'MAS VIDA' => 'ISAPRE',
        'NATURMED' => 'OTROS',
        'NO ASIGNADA' => 'PARTICULAR',
        'NORMEDICA' => 'ISAPRE',
        'OPTIMA SALUD' => 'ISAPRE',
        'PARTICULAR' => 'PARTICULAR',
        'PRAIS' => 'PRAIS',
        'PROMEPART' => 'OTROS',
        'RIO BLANCO' => 'ISAPRE',
        'SAN LORENZO' => 'ISAPRE',
        'SELECCIONE' => 'SIN INFORMACION',
        'SELECCIONE ISAPRE' => 'ISAPRE',
        'SFERA' => 'OTROS',
        'SHELL CHILE' => 'OTROS',
        'SIN CLASIFICACION' => 'INDETERMINADO',
        'SIN PREVISION' => 'PARTICULAR',
        'UMBRAL' => 'OTROS',
        'VIDA PLENA' => 'OTROS',
        'VIDA TRES' => 'ISAPRE',
        // A continuación, códigos de INTERSYSTEMS
        'C004' => 'CAPREDENA', //CAPREDENA (CAPREDENA)
        'C005' => 'DIPRECA', //DIPRECA (DIPRECA)
        '12' => 'OTROS', //MLE (FONASA)
        'AT01' => 'PARTICULAR', //ABN Amro (INACTIVO)
        'AT02' => 'PARTICULAR', //Aseguradora Magallanes (INACTIVO)
        'AT03' => 'PARTICULAR', //Bci (INACTIVO)
        'AT04' => 'PARTICULAR', //Chilena Consolidada (INACTIVO)
        'AT05' => 'PARTICULAR', //Consorcio Nacional (INACTIVO)
        'C006' => 'DIPRECA', //Dipreca - Carabineros (INACTIVO)
        'C007' => 'DIPRECA', //Dipreca - PDI (INACTIVO)
        'AT06' => 'PARTICULAR', //ING Vida (INACTIVO)
        'INP01' => 'PARTICULAR', //INP Escolar (INACTIVO)
        'INP02' => 'PARTICULAR', //INP Trabajo (INACTIVO)
        'AT07' => 'PARTICULAR', //Interamericana Vida (INACTIVO)
        'AT08' => 'PARTICULAR', //Ise Chile (INACTIVO)
        'AT09' => 'PARTICULAR', //Liberty (INACTIVO)
        '11' => 'PARTICULAR', //MAI (INACTIVO)
        'AT10' => 'PARTICULAR', //Mapfre (INACTIVO)
        'AT11' => 'PARTICULAR', //Penta Security (INACTIVO)
        'AT12' => 'PARTICULAR', //Renta Nacional (INACTIVO)
        'AT13' => 'PARTICULAR', //Royal (INACTIVO)
        'AT14' => 'PARTICULAR', //Sin Información (INACTIVO)
        '108' => 'ISAPRE', //Alemana Salud S.A (ISAPRE)
        '65' => 'ISAPRE', //Chuquicamata Ltda. (ISAPRE)
        '67' => 'ISAPRE', //Colmena Golden Cross S.A (ISAPRE)
        '82' => 'ISAPRE', //Compensación S.A (ISAPRE)
        '107' => 'ISAPRE', //Consalud S.A (ISAPRE)
        '78' => 'ISAPRE', //Cruz Blanca S.A (ISAPRE)
        '94' => 'ISAPRE', //Cruz del Norte Ltda. (ISAPRE)
        '81' => 'ISAPRE', //Ferrosalud S.A (ISAPRE)
        '75' => 'OTROS', //Fundacion de Salud Shell Chile (ISAPRE)
        '63' => 'ISAPRE', //Fusat Ltda. (ISAPRE)
        '84' => 'ISAPRE', //Galenica S.A (ISAPRE)
        '54' => 'ISAPRE', //ING Salud Isapre S.A (ISAPRE)
        '77' => 'ISAPRE', //Isagas S.A (ISAPRE)
        '86' => 'OTROS', //Isamedica S.A (ISAPRE)
        '99' => 'ISAPRE', //Isapre Banmedica S.A (ISAPRE)
        '76' => 'ISAPRE', //Isapre Fundación (ISAPRE)
        '83' => 'ISAPRE', //Ismed S.A (ISAPRE)
        '69' => 'ISAPRE', //Ispen Ltda. (ISAPRE)
        '106' => 'ISAPRE', //La Araucana S.A (ISAPRE)
        '89' => 'ISAPRE', //Linksalud S.A (ISAPRE)
        '96' => 'ISAPRE', //LinkSalud-Vida S.A (ISAPRE)
        '88' => 'ISAPRE', //Masvida S.A (ISAPRE)
        '70' => 'ISAPRE', //Normédica (ISAPRE)
        '57' => 'OTROS', //Promepart Isapre (ISAPRE)
        '68' => 'ISAPRE', //Rio Blanco Ltda. (ISAPRE)
        '62' => 'ISAPRE', //San Lorenzo Ltda. (ISAPRE)
        '104' => 'OTROS', //Sfera (ISAPRE)
        '64' => 'ISAPRE', //Sudamerica S.A (ISAPRE)
        '87' => 'ISAPRE', //Unimed S.A (ISAPRE)
        '66' => 'OTROS', //Vida Plena (ISAPRE)
        '80' => 'ISAPRE', //Vida Tres (ISAPRE)
        '2' => 'OTROS', //ACC. DE TRABAJO Y ENF. PROFESIONALES (Ley 16744) (LEYES/PROGRAMAS SOCIALES)
        '1' => 'OTROS', //ACC. DE TRANSPORTE (Ley 18490) (LEYES/PROGRAMAS SOCIALES)
        '3' => 'OTROS', //ACC. ESCOLAR (Ley 16744) (LEYES/PROGRAMAS SOCIALES)
        '7' => 'OTROS', //CHILE CRECE CONTIGO (LEYES/PROGRAMAS SOCIALES)
        '6' => 'OTROS', //CHILE SOLIDARIO (LEYES/PROGRAMAS SOCIALES)
        '9' => 'OTROS', //GES (LEYES/PROGRAMAS SOCIALES)
        '4' => 'OTROS', //LEY DE URGENCIA (19650/99) (LEYES/PROGRAMAS SOCIALES)
        '8' => 'OTROS', //OTRO PROGRAMA SOCIAL (LEYES/PROGRAMAS SOCIALES)
        '5' =>	'PRAIS', //PRAIS (LEYES/PROGRAMAS SOCIALES)
        'C003' => 'OTROS', //ARMADA (OTRA)
        'C001' => 'OTROS', //EJERCITO DE CHILE (OTRA)
        'C002' => 'OTROS', //FACH (OTRA)
        'PT' => 'PARTICULAR', //Prevision Provisioria (PREVISION PROVISORIA)
        'P' => 'PARTICULAR', //PARTICULAR (PARTICULAR)

    ];

    public function actualizarMapa($csv){
        $rows = str_getcsv($csv, "\n");
        foreach($rows as $row){
            $this->csv[] = str_getcsv($row, "|");
        }
        $this->generar_lista_bd();
        $this->generar_lista_csv();

        $para_agregar = array_values(array_diff($this->lista_csv, $this->lista_bd));
        $para_quitar = array_values(array_diff($this->lista_bd, $this->lista_csv));

        foreach($para_agregar as $fila){
            $a = explode("|", $fila);
            $this->para_agregar[$a[0]][] = $a[1];
        }

        foreach($para_quitar as $fila){
            $a = explode("|", $fila);
            $this->para_quitar[$a[0]][] = $a[1];
        }

        return Response::json(["quitar" => $this->para_quitar, "agregar" => $this->para_agregar]);
    }

    private function generar_lista_csv(){
        foreach($this->csv as $fila){
            if(!in_array($fila[1], $this->lista_negra))
                $this->lista_csv[] = "{$fila[1]}|{$fila[5]}";
        }
    }

    private function generar_lista_bd(){
        $out = UnidadEnEstablecimiento::with("salas.camasHabilitadas" )->where("establecimiento", 8)->get();
        foreach($out as $fila){
            foreach($fila->salas as $sala){
                if($sala->camas->count() > 0)
                    $this->lista_bd[] = "{$fila->alias}|{$sala->nombre}";
            }
        }
    }

    private function quitar(){
        foreach($this->para_quitar as $n_unidad => $n_salas){
            $salas = Sala::with(["unidadEnEstablecimiento" => function($q) use ($n_unidad){
                $q->where("alias", $n_unidad)->where("establecimiento", 8);
            }])->whereIn("nombre", $n_salas)->with("camas")->get();
            foreach($salas as $sala){
                foreach($sala->camasHabilitadas() as $cama) {
                    $cama->bloqueoAutomatico();
                }
            }
        }
    }

    public function movCamaSOME(){

        /* A ver si corresponden el establecimiento y la unidad! */
        $n_unidad = Input::get("npu_n_unidad");
        $c_unidad = Input::get("npu_c_unidad");

        /*if(!in_array($c_unidad, $this->lista_permitidos_quilpue, true)){
            exit();
        }*/

        if ($n_unidad === null || $c_unidad === null || $n_unidad === '' || $c_unidad == ''){
            return Response::json([
                "error" => "Faltan datos en mensaje",
                "info" => ""
            ]);
        }

        try {
            $establecimiento = Establecimiento::where("some", Input::get("establecimiento"))
                ->with(["unidades" => function($q) use ($c_unidad){
                    $q->where("some", $c_unidad);
                }])->first();
        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return Response::json([
                "error" => "Establecimiento o unidad no soportados",
                "info" => ""
            ]);
        }

        if($establecimiento->unidades === null || $establecimiento->unidades->isEmpty()){
            $u = new UnidadEnEstablecimiento();
            $u->establecimiento = $establecimiento->id;
            $u->alias = $n_unidad;
            $u->url = Funciones::sanearString($n_unidad);
            $u->some = $c_unidad;
            $u->save();
            $establecimiento->unidades = new \Illuminate\Database\Eloquent\Collection([$u]);
        }

        try{
            $sala = $establecimiento->unidades->first()
                ->salas()
                ->where("nombre", Input::get("npu_n_sala"))
                ->firstOrFail();
        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $sala = new Sala();
            $sala->nombre = Input::get("npu_n_sala");
            $sala->establecimiento = $establecimiento->unidades->first()->id;
            $sala->save();
        }
        if(Input::get("npu_n_cama") * 1){
            $n_cama = "CAMA ".Input::get("npu_n_cama");
        }
        else{
            $n_cama = Input::get("npu_n_cama");
        }
        try{
            $cama = $sala->camas()->where("id_cama", $n_cama)->firstOrFail();
        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $cama = new Cama();
            $cama->id_cama = $n_cama;
            $cama->sala = $sala->id;
            //$cama->descripcion = "Cama ingresada automáticamente por módulo SOME";
            $cama->save();
        }

        $fecha_inicio = Input::get("npu_fecha_inicio");
        $fecha_termino = Input::get("npu_fecha_termino");

        $movimiento = Input::get("npu_codigo_mov");
        $motivo = Input::get("nte_motivo");
        $motivo = $motivo ? $motivo:"Motivo no disponible";
        if($movimiento == -1){
            try{
                HistorialEliminacion::where("cama", $cama->id)->firstOrFail();
                return Response::json([
                    "exito" => "La cama ya esta eliminada",
                    "msg" => "Cama ya eliminada",
                    "info" => ""
                ]);
            }
            catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){}
            try {
                $eliminacion = new HistorialEliminacion();
                $eliminacion->cama = $cama->id;
                $eliminacion->fecha = $fecha_inicio;
                $eliminacion->motivo = "eliminacion";
                $eliminacion->save();
            }
            catch(Exception $e){
                return Response::json(["error" => "No se pudo eliminar",
                    "info" => ""
                ]);
            }
            return Response::json([
                "exito" => "Eliminada {$n_cama}",
                "msg" => "Cama eliminada"
            ]);

        }
        if    ($movimiento == 1){//reservada
            return Response::json([
                "error" => "Reservas desactivada",
                "info" => ""
            ]);
        }
        elseif($movimiento == 2){//en reparacion
            try {
                if($fecha_inicio) {
                    $bloqueo = $cama->bloqueos()
                        ->where("fecha", $fecha_inicio)
                        ->where("motivo", "problemas estructurales")
                        ->firstOrFail();
                }
                else{
                    /* Si la fecha de inicio está vacía puede que sea un mensaje de Quilpué. Se busca "cerrar" un
                    evento, entonces se tiene que buscar uno que tenga "fecha_habilitacion" como NULL */
                    $bloqueo = $cama->bloqueos()
                        ->where("motivo", "problemas estructurales")
                        ->whereNull("fecha_habilitacion")
                        ->firstOrFail();
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                if($fecha_inicio) {
                    $bloqueo = new HistorialBloqueo();
                    $bloqueo->cama = $cama->id;
                    $bloqueo->fecha = $fecha_inicio;
                    $bloqueo->motivo = "problemas estructurales";
                    $bloqueo->save();
                }
                else{
                    /* En este caso se intenta cerrar un estado que no existe. Pero tampoco tengo fecha de inicio para
                    ingresar uno. Lo más probable es q nunca se llegue a este estado. */
                    return Response::json([
                        "error" => "Se intenta dar fin a un evento que nunca inicio."
                    ]);
                }
            }

            if($fecha_termino){
                $bloqueo->fecha_habilitacion = $fecha_termino;
                $bloqueo->motivo_habilitacion = "Habilitación por módulo SOME: {$motivo}";
                $bloqueo->save();
            }
            return Response::json([
                "exito" => "{$bloqueo->id} {$n_cama}",
                "msg" => "Estado aceptado"
            ]);
        }
        elseif($movimiento == 3){//bloqueada
            try {
                if ($fecha_inicio) {
                    $bloqueo = $cama->bloqueos()
                        ->where("fecha", $fecha_inicio)
                        ->where("motivo", "<>", "problemas estructurales")
                        ->firstOrFail();
                } else {
                    /* Si la fecha de inicio está vacía puede que sea un mensaje de Quilpué. Se busca "cerrar" un
                    evento, entonces se tiene que buscar uno que tenga "fecha_habilitacion" como NULL */
                    $bloqueo = $cama->bloqueos()
                        ->where("motivo", "<>", "problemas estructurales")
                        ->whereNull("fecha_habilitacion")
                        ->firstOrFail();
                }
            }
            catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                if ($fecha_inicio) {
                    $bloqueo = new HistorialBloqueo();
                    $bloqueo->cama = $cama->id;
                    $bloqueo->fecha = $fecha_inicio;
                    $bloqueo->motivo = "SOME: {$motivo}";
                    $bloqueo->save();
                }
                else{
                    return Response::json([
                        "error" => "Se intenta dar fin a un evento que nunca inicio."
                    ]);
                }
            }
            if($fecha_termino){
                $bloqueo->fecha_habilitacion = $fecha_termino;
                $bloqueo->motivo_habilitacion = "Habilitación por módulo SOME: {$motivo}";
                $bloqueo->save();
            }
            return Response::json([
                "exito" => "{$bloqueo->id} {$n_cama}",
                "msg" => "Bloqueo aceptado"
            ]);
        }
        else{
            return Response::json([
                "error" => "Codigo de movimiento de cama no manejado",
                "info" => ""
            ]);
        }
    }

    public function altaSOME(){

            return DB::transaction(function() {

                $recibido = Input::get("motivo");
                $fecha = Input::get("fechaCambio"); //esta fecha cierra el caso
                $fechaMov = Input::get("fechaMov"); // esta fecha cierra la cama.

                $caso = $this->caso_n;

                if($fecha === '' || $fecha === null ){
                    $fecha = \Carbon\Carbon::now();
                }

                if($fechaMov === '' || $fechaMov === null ){
                    $fechaMov = $fecha;
                }

                $fecha = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fecha);
                try{
                    $fechaMov = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fechaMov);
                }
                catch(Exception $e){
                    $fechaMov = $fecha;
                }

                switch($recibido){
                    case "Alta a Domicilio":
                        $motivo = "alta";
                        break;
                    case "Derivación":
                    case "Derivacion":
                    case "Derivaci&oacute;n":
                    case "Derivaci":
                        $motivo = "traslado externo";
                        break;
                    case "Fallecido":
                        $motivo = "fallecimiento";
                        break;
                    default:
                        $motivo = "otro";
                        break;
                }
                /* Buscar si existe el rut*/
                /* @var $paciente Paciente */

                /* Actualizar igualmente el riesgo si está disponible.*/

                /* Buscar si el caso efectivamente está ocupando una cama */
                /*$h = $caso->historialOcupacion()->first();*/
                $est = $caso->establecimiento($fechaMov);
                if(!$est->isEmpty()) {
                    try {
                        /* @var $cama Cama */
                        if ($est->first()->some === 'QUILPUE') {
                            /* hack horrendo pero buee... */
                            $cama = $caso->camas()
                                ->orderBy("fecha", "desc")
                                ->first();
                            $unidad = $cama->salaDeCama()->first()->unidadEnEstablecimiento()->first();
                            /*if(!in_array($unidad->some, $this->lista_permitidos_quilpue, true)){
                                return;
                            }*/
                            $cama->reconvertirOriginal($fechaMov);
                        }

                        $caso->fecha_termino = $fecha;
                        $caso->motivo_termino = $motivo;
                        $caso->detalle_termino = "Actualizado por SOME";
                        $caso->save();
                        $caso->liberar($motivo, $fechaMov);

                    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                        //En este punto el paciente no fue liberado anteriormente!

                    }
                }
            });

    }

    public function ingresarPacienteSOME(){
        $rut = Input::get("rut") === '' || Input::get("rut") === '0' ? null: Input::get("rut");
        $id_interno = Input::get("id_interno") === '' || Input::get("id_interno") === '0' || Input::get("id_interno") === 0 ? null: Input::get("id_interno");
        $ficha = Input::get("ficha") === ''|| Input::get("ficha") === '0' || Input::get("ficha") === 0 ? null:Input::get("ficha");
        $dv = Input::get("dv") == 'K' ? 10:Input::get("dv");
        $dv = $rut === null? null:$dv;
        $apellidoP = strtoupper(Input::get("apellidoP"));
        $apellidoM = strtoupper(Input::get("apellidoM"));
        $nombre = strtoupper(Input::get("nombre"));
        $fechaNac = trim(Input::get("fechaNac"));
        $sexo = strtolower(Input::get("sexo"));

        /*
        $paciente2=null;
        if($rut == null){$paciente2=null;}else{
        $paciente2 = Paciente::where("rut", "=", $rut)->first();}

        if($paciente2 != null){
                $idPaciente = $paciente2->id;
                $caso4 = Caso::where("paciente", "=", $idPaciente)->whereNull("fecha_termino")->first();
                    if($caso4 != null)
                        {  
                            $case = $caso4->id;
                            $casa = Caso::find($case);
                            $casa->fecha_termino = date("Y-m-d H:i:s");
                            $casa->motivo_termino = "alta";
                            $casa->save();
                        }
        }
        */
        try{
            $fechaIng = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", Input::get("fechaIng"));
        }
        catch(Exception $e){
            $fechaIng = null;
        }
        try{
            $fechaCierre = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", Input::get("fechaCambio"));
        }catch(Exception $e){
            $fechaCierre = null;
        }
        try{
            $fechaCambio = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", Input::get("fechaMov"));
        }catch(Exception $e){
            $fechaCambio = $fechaCierre;
        }
        try{
            $fechaNac = \Carbon\Carbon::createFromFormat("Y-m-d", $fechaNac);
            if($fechaNac->year <= 0){
                $fechaNac = null;
            }
        }catch(Exception $e){
            $fechaNac = null;
        }
        $codigo = trim(Input::get("codigo"));
        $diagnostico = trim(Input::get("diagnostico"));
        $riesgo = trim(Input::get("riesgo"));
        $medico = trim(Input::get("medico"));
        $cama = trim(Input::get("cama"));
        $n_unidad = trim(Input::get("n_unidad") );
        $c_unidad = trim(Input::get("c_unidad") );
        $establecimiento = trim(Input::get("establecimiento") );
        $sala = trim(Input::get("sala") );
        $id_sala_some = trim(Input::get("id_sala"));
        $prevision = trim(Input::get("prevision"));

        if(empty($prevision)) $prevision = null;
        if(empty($id_sala_some)) $id_sala_some = null;
        if(empty($cama)) $cama = null;
        if(empty($n_unidad)) $n_unidad = null;
        if(empty($c_unidad)) $c_unidad = null;
        if(empty($establecimiento)) $establecimiento = null;
        if(empty($sala)) $sala = null;

        if($sala=="SALA DE CIERRE ATENCION PEDIATRIA" || $sala=="SALA DE CIERRE ATENCION")
        {
            $paciente3 = Paciente::where("rut", "=", $rut)->first();
            if($paciente3 != null){
                    $idPaciente = $paciente3->id;
                    $caso4 = Caso::where("paciente", "=", $idPaciente)->whereNull("fecha_termino")->first();
                        if($caso4 != null)
                            {  
                                $case = $caso4->id;
                                $casa = Caso::find($case);
                                $casa->fecha_termino = date("Y-m-d H:i:s");
                                $casa->motivo_termino = "alta";
                                $casa->save();
                            }
            }
        }

        /* @var $establecimiento_obj Establecimiento */
		if($establecimiento=="QUILLOTA"){
			$establecimiento_obj = Establecimiento::where("id", 1)->first();
		}else{
			$establecimiento_obj = Establecimiento::where("some", $establecimiento)->first();
		}
        $this->establecimiento_obj = $establecimiento_obj;
        $msg = "";

        if($cama * 1){
            $cama = "CAMA {$cama}";
        }

        if($sexo !== 'masculino' && $sexo !== 'femenino'){
            $sexo = null;
        }

        $previsiones = array_keys(Prevision::getPrevisiones());

        if(!in_array($prevision, $previsiones)){
            /* TODO: manejador de previsiones que no se encuentren en la tabla (asociación).*/
            if(isset(self::$previsiones[mb_strtoupper($prevision)])) {
                $prevision = self::$previsiones[mb_strtoupper($prevision)];
            }
            else{
                $prevision = "SIN INFORMACION";
            }
        }

        /* Hay que obtener una cama libre para ingresar al paciente
            El esquema de camas del SOME no es compatible con el del
            gestor de camas, porque en el gestor se tiene un id propio
            por sala, pero en el some se tiene un id por unidad.
            El gestor simplemente ingresará al paciente a una cama que esté
            disponible.
        */

        /* Crear el objeto paciente y actualizarlo */

        /*
         * el paciente puede venir con Rut seteado y/o id_sec seteado (id interno)
         * Los NN vienen sin rut en un principio, pero debieran venir con el id_sec
         * luego, cuando se quiere actualizar el paciente, vendrá con el rut seteado
         * y con el id_sec seteado.
         * No todos los hospitales con ws tienen el id_sec.
         */
        $paciente = null;
        DB::beginTransaction();
        try{
            if ($id_interno !== null){
                /*$paciente = Paciente::where("id_sec", $id_interno)->whereNotNull("id_sec")->firstOrFail();*/
                $paciente = Paciente::whereHas("idsEnEstablecimientos", function($q) use ($establecimiento_obj, $id_interno){
                    $q->where("establecimiento", $establecimiento_obj->id)
                    ->where("id_paciente", $id_interno);
                })->first();
            }
            if($paciente === null){
                if($rut == null)
                {
                    $paciente = Paciente::whereNombre($nombre)->whereApellido_paterno($apellidoP)->whereApellido_materno($apellidoM)->firstOrFail();
                    if($nombre=="RN")
                    {
                        $RnEsta="Rn/".$establecimiento;
                        $paciente->rn=$RnEsta;
                        $paciente->save();
                    }
                }
                else $paciente = Paciente::whereRut($rut)->whereNotNull("rut")->firstOrFail();
            }
            $paciente_nuevo = false;
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $paciente = new Paciente();
            $paciente_nuevo = true;
        }
        try {
            $paciente->id_sec = $id_interno;
            $paciente->rut = $rut;
            $paciente->dv = $dv;
            $paciente->save();
        }
        catch(PDOException $e){
            /*
             *  Si se actualiza el paciente, pero el rut ya existe, entonces encontrar a ese paciente
             * por el rut y actualizar el caso para que corresponda al nuevo paciente. Lerolero.
             */
            DB::rollBack();
            DB::beginTransaction();
            $paciente = Paciente::where("rut", $rut)->first();

        }

        try{
            if ($fechaIng !== null) {
                $caso_n = $paciente->casos()->where("fecha_ingreso", "{$fechaIng}")->firstOrFail();
            }
            else{
                $caso_n = $paciente->casos()->firstOrFail();
                $fechaIng = $caso_n->fecha_ingreso;
            }
            $caso_nuevo = false;
        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $caso_n = new Caso();
            $caso_n->paciente = $paciente->id;
            $caso_n->fecha_ingreso = $fechaIng;
            $caso_nuevo = true;
        }
        /* aprovechar de actualizar el establecimiento del caso */
        $caso_n->establecimiento = $establecimiento_obj->id;
        $traslado_externo = false;

        if($caso_nuevo){
            /* @var $caso_anterior Caso */
            $caso_anterior = $paciente->casos()->where("fecha_ingreso", "<", "{$fechaIng}")->first();
            if($caso_anterior !== null){
                $est = $caso_anterior->establecimientoCaso()->first();
                if($est === null){
                    $est = $caso_anterior->establecimiento();
                    if(!$est->isEmpty()) $est = $est->first();
                    else{
                        $est = null;
                    }
                }
                if($est !== null){
                    $fecha_lib = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $fechaCambio);
                    if($est->some !== $establecimiento){
                        $motivo_cierre = 'traslado externo';
                        $detalle_cierre = $establecimiento_obj->nombre;
                        $traslado_externo = true;
                    }
                    else{
                        $motivo_cierre = 'otro';
                        $detalle_cierre = "SOME: renovación diagnóstico";
                    }
                    try{
                        $caso_anterior->liberar($motivo_cierre, "{$fecha_lib}");
                    }
                    catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                        /* EN este punto, liberar falló porque no encontró el último historial
                    que estuviera "libre". */
                    }
                    if($caso_anterior->fecha_termino === null) {
                        $caso_anterior->fecha_termino = "{$fecha_lib}";
                        if($caso_anterior->motivo_termino === null)
                            $caso_anterior->motivo_termino = $motivo_cierre;
                        if($caso_anterior->detalle_termino === null)
                            $caso_anterior->detalle_termino = $detalle_cierre;
                    }
                    $caso_anterior->save();
                    /* TRATAR DE ACEPTAR CUALQUIER DERIVACION QUE SE ENCUENTRE DEL CASO ANTERIORS */
                    try {
                        /* @var $solicitud_traslado Derivacion */
                        $solicitud_traslado = $caso_anterior->derivacionesActivas()
                            ->whereHas("unidadDestino", function ($q) use ($establecimiento) {
                                $q->whereHas("establecimientos", function ($q) use ($establecimiento) {
                                    $q->where("some", $establecimiento);
                                });
                            })
                            ->firstOrFail();
                        if($solicitud_traslado->fecha_cierre === null)
                            $solicitud_traslado->cerrar("aceptado", "Ingresado por SOME");
                        else {
                            $solicitud_traslado->motivo_cierre = "aceptado";
                            $solicitud_traslado->save();
                        }


                    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {}
                }
            }
            /* Si el caso es nuevo hay que escribir los datos que vienen en el mensaje */
            if($medico !== null && $medico !== ''){
                $caso_n->medico = $medico;
            }
            $msg.= "@nuevo caso: {$caso_n->id}";

            /* Aquí existe el caso de que el paciente ya esté ocupando la cama, y eso significa de que
            debe ser trasladado! Pero si el paciente ocupa cama, debería aparecer con un caso actual*/
        }
        /*SI el caso no es nuevo entonces no se sobreescriben esos datos, excepto la previsión. */
        if ($diagnostico !== null && $diagnostico !== '' && $caso_n->diagnostico === null){
            $caso_n->diagnostico = $diagnostico;
        }

        if($ficha !== null){
            $caso_n->id_sec = $ficha;
        }

        if($paciente->rut === null && $rut !== null){
            /*
             * PUEDE que el rut del paciente ya existan entre los pacientes de la BD,
             * pero que no se haya sabido al momento de ingresar al paciente. Cuando eso
             * pasa, el paciente está "dos veces". Se debe encontrar primero si existe un
             * paciente con el rut, y de ser así convertir el caso al nuevo paciente.
             * Esta conversión se hace en este punto del código porque tenemos el caso per-
             * teneciente a este paciente.
             */

            $pac_temp = Paciente::where("rut", $rut)->first();
            if($pac_temp !== null){
                /* Habrá que encontrar los casos del paciente antiguo (el sin rut)*/
                $casos_paciente = $paciente->casos()->get();
                foreach($casos_paciente as $caso_paciente){
                    $caso_paciente->paciente = $pac_temp->id;
                    $caso_paciente->save();
                }
                $ids_paciente = $paciente->idsEnEstablecimientos()->get();
                foreach($ids_paciente as $id_paciente){
                    $id_paciente->paciente = $pac_temp->id;
                    $id_paciente->save();
                }
                $paciente->delete();
                unset($paciente);
                $paciente = $pac_temp;
            }
            unset($pac_temp);
        }

        $paciente->apellido_paterno = $apellidoP;
        $paciente->apellido_materno = $apellidoM;
        $paciente->nombre = $nombre;
        $paciente->fecha_nacimiento = $fechaNac;
        $paciente->sexo = $sexo;
        $paciente->save();

        if ($id_interno !== null) {
            try {
                $ids = IDPaciente::where("paciente", $paciente->id)->firstOrFail();
            }
            catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                $ids = new IDPaciente();
                $ids->paciente = $paciente->id;
                $ids->establecimiento = $establecimiento_obj->id;
                $ids->id_paciente = $id_interno;
                $ids->save();
            }
        }

        $caso_n->prevision = $prevision;
        $caso_n->paciente = $paciente->id;
        $caso_n->save();

        /* Diagnosticos  */

        $diag = $caso_n->diagnosticos()
            ->where("fecha", "<=", $fechaCambio)
            ->orderBy("fecha", "desc")
            ->first();

        if($diag === null || $diag->diagnostico !== $diagnostico){
            $diag_n = new HistorialDiagnostico();
            $diag_n->fecha = $fechaCambio;
            $diag_n->diagnostico = $diagnostico;
            $diag_n->caso = $caso_n->id;
            $diag_n->save();
        }

        if (!empty($riesgo)){
            try{
                $evolucion = EvolucionCaso::where("caso", "=", $caso_n->id)
                    ->where("fecha", "<", $fechaCambio)
                    ->orderBy("fecha", "desc")
                    ->firstOrFail();
                if ($evolucion->riesgo != $riesgo){
                    $msg.= "@Antiguo riesgo: {$evolucion->riesgo}";
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
                }
                else{
                    $msg.= "@No se actualiza el riesgo";
                }
            }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e){
                $evolucion = new EvolucionCaso();
                $evolucion->caso = $caso_n->id;
                $evolucion->riesgo = $riesgo;
                $evolucion->fecha = $fechaCambio;
                $evolucion->save();
                $msg.= "@Nuevo riesgo: {$riesgo}";
            }
        }
        else{
            $msg.= "@No se actualiza el riesgo";
        }

        DB::commit();

        /* Los siguientes deberían ejecutarse si hay disponible info de unidad/sala/cama */

        $this->n_unidad = $n_unidad;
        $this->caso_n = $caso_n;
        $this->msg = $msg;
        $this->c_unidad = $c_unidad;
        $this->fechaCambio = $fechaCambio;
        $this->caso_nuevo = $caso_nuevo;
        $this->fechaIng = $fechaIng;
        $this->traslado_externo = $traslado_externo;
        $this->fechaCierre = $fechaCierre;
        $this->establecimiento_obj = $establecimiento_obj;
        $this->sala = $sala;
        $this->cama = $cama;
        $this->id_sala_some = $id_sala_some;

    }

    private function asignar(){
        /* @var $caso_n Caso */
        $cama_libre = &$this->cama_libre;
        $caso_n = &$this->caso_n;
        $msg = &$this->msg;
        $c_unidad = &$this->c_unidad;
        $fechaCambio = &$this->fechaCambio;
        $caso_nuevo = &$this->caso_nuevo;
        $fechaIng = &$this->fechaIng;
        $traslado_externo = &$this->traslado_externo;

        $n_unidad = &$this->n_unidad;
        $cama = &$this->cama;
        $sala = &$this->sala;
        $establecimiento_obj = &$this->establecimiento_obj;
        $id_sala_some = &$this->id_sala_some;

        /*  Si no existe la Unidad, se retira con error! */
        try {
            $unidad = UnidadEnEstablecimiento::whereHas('establecimientos', function ($q) use ($establecimiento_obj) {
                $q->whereSome($establecimiento_obj->some);
            })
                ->whereSome($c_unidad)->firstOrFail();
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $unidad = new UnidadEnEstablecimiento();
            $unidad->establecimiento = $establecimiento_obj->id;
            $unidad->alias = $n_unidad;
            $unidad->url = Funciones::sanearString($n_unidad);
            $unidad->some = $c_unidad;
            /*return Response::json([
                "error" => "No existe unidad {$n_unidad} en {$establecimiento}",
                "info" => ""
            ]);*/
            $unidad->save();
        }
        /* Si no existe la sala, debería crear una */
        try {
            $sala_obj = Sala::where("establecimiento", $unidad->id)
                ->where("nombre", $sala)->where("codigo_some", $id_sala_some)->firstOrFail();
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $sala_obj = new Sala;
            $sala_obj->nombre = $sala;
            $sala_obj->id_sala = DB::raw("(SELECT coalesce(max(id_sala) + 1, 1) FROM salas WHERE establecimiento = {$unidad->id})");
            $sala_obj->establecimiento = $unidad->id;
            $sala_obj->codigo_some = $id_sala_some;
            $sala_obj->save();
            $msg .= "@Nueva sala:{$sala_obj->nombre}";
        }

        try {
            $cama_libre = Cama::where("sala", "=", $sala_obj->id)
                ->where("id_cama", "=", $cama)
                ->firstOrFail();
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $cama_libre = new Cama;
            $cama_libre->id_cama = "{$cama}";
            $cama_libre->descripcion = "Agregado automáticamente por SOME";
            $cama_libre->sala = $sala_obj->id;
            $cama_libre->save();
            $histCama = HistorialCamasUnidades::where("cama", $cama_libre->id)->first();
            $histCama->fecha = $fechaIng;
            $histCama->save();
            $msg .= "@Nueva cama:{$cama_libre->id_cama}";
        }

        try{
            $h = $cama_libre->ocupaciones()
                ->where("fecha_liberacion", "=", null)
                ->orderBy("fecha", "desc")
                ->firstOrFail();
            if($h->caso != $caso_n->id){
                $no_asignar = false;
            }
            else{
                /*  Si el caso es el mismo significa que el mensaje es repetido y no se debe
                    asignar cama al paciente. */
                $no_asignar = true;
                $msg.= "@No se traslada";
            }
        }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e){

            $no_asignar = false;
        }

        if(!$no_asignar){
            /*al paciente se le asigna una cama */
            /*if(isset($c_unidad_origen)){
                if($c_unidad_origen*1 !== $c_unidad*1){
                    // reconvertir
                    try {
                        $unidad_origen = $establecimiento_obj->unidades()->where("some", $c_unidad_origen)->firstOrFail();
                        $hist = new HistorialCamasUnidades;
                        $hist->cama = $cama_libre->id;
                        $hist->fecha = $fechaCambio;
                        $hist->unidad = $unidad_origen->id;
                        $hist->save();
                    }catch(Exception $e){

                    }
                }
            }*/
            $fechaReconv = $fechaCambio;
            if ($caso_nuevo){
                $h = $caso_n->asignarCama($cama_libre->id, $fechaCambio);
                $h->save();
                $msg.= "@Se ha ingresado al paciente: {$h->id}";
                $fechaReconv = $fechaIng;
                /* Reconvertir cama */
            }
            else {
                try {
                    $h = $caso_n->reasignarCama($cama_libre->id, $fechaCambio);
                    if($h->fecha < $fechaCambio ) {
                        $msg .= "@No se traslada: {$h->id}";
                    }
                    else{
                        $msg .= "@Se ha trasladado al paciente: {$h->id}";
                        $fechaReconv = $fechaCambio;
                        /* quitar la reconversión de camas si se trata de quilpue */
                        try{
                            $hist = HistorialOcupacion::where("fecha", "<", $fechaCambio)
                                ->where("caso", $caso_n->id)
                                ->orderBy("fecha", "desc")
                                ->firstOrFail();
                            $histcama = new HistorialCamasUnidades();
                            $histcama->cama  = $hist->cama;
                            $histcama->fecha = $fechaReconv;
                            $histcama->save();
                        }
                        catch(Exception $e){

                        }
                    }
                    /* HACK */
                    $camb = Input::get("fechaCambio");
                    if( $camb !== null && $camb !== ''){

                    }

                }
                catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                    if(!$traslado_externo) {
                        $h = $caso_n->asignarCama($cama_libre->id, $fechaCambio);
                        $h->save();
                        $msg .= "@Se ha ingresado al paciente: {$h->id}";
                        $fechaReconv = $fechaIng;
                    }
                    else{
                        $h = $caso_n->asignarCama($cama_libre->id, $fechaCambio);
                        $h->save();
                        $msg .= "@Se ha ingresado al paciente: {$h->id}";
                        $fechaReconv = $fechaCambio;
                    }
                }
            }
            if($this->fechaCierre !== null){
                if($h->fecha_liberacion === null) {
                    $h->fecha_liberacion = $this->fechaCierre;
                    $h->save();
                }
                $caso_n->fecha_termino = $this->fechaCierre;
                $caso_n->save();
            }
            $c_unidad_origen = Input::get("c_unidad_origen");
            if(isset($c_unidad_origen)){
                if($c_unidad_origen*1 !== $c_unidad*1){
                    /* reconvertir */
                    try {
                        $unidad_origen = $establecimiento_obj->unidades()->where("some", $c_unidad_origen)->firstOrFail();
                        $hist = new HistorialCamasUnidades;
                        $hist->cama = $cama_libre->id;
                        $hist->fecha = $fechaReconv;
                        $hist->unidad = $unidad_origen->id;
                        $hist->save();
                    }catch(Exception $e){

                    }
                }
            }

        }
    }

    public function movimientoSOME(){

        $movimiento = Input::get("movimiento");
        foreach(Input::all() as $k => $v){
            
        }
        if(is_null($movimiento) || $movimiento === ''){
            $movimiento = Input::get("codigo");
        }
        if ($movimiento === 'A04' || $movimiento === 'A02' || $movimiento === 'A01' || $movimiento === 'A08'){
            /*if(Input::get("establecimiento") === 'QUILPUE' && !in_array($this->c_unidad, $this->lista_permitidos_quilpue)){
                exit();
            }*/
            try{

                

                

                $this->ingresarPacienteSOME();


                if($this->c_unidad == "HQPE-AC-095"){
                        if($this->id_sala_some == "HQ-35"){
                            $this->sala = "SALA 1 NEO";
                        }
                        if($this->id_sala_some == "HQ-36"){
                            $this->sala = "SALA 2 NEO";
                        }
                        if($this->id_sala_some == "HQ-79"){
                            $this->sala = "SALA 3 NEO";
                        }


                }


                if($this->c_unidad == "HQTA-HOSP PED"){
                        if($this->id_sala_some == "HQTA-HOSP PED 01"){
                            $this->sala = "Sala Médico Quirúrgico";
                        }
                        if($this->id_sala_some == "HQTA-HOSP PED 02"){
                            $this->sala = "Sala Lactante";
                        }
                        if($this->id_sala_some == "HQTA-HOSP PED 03"){
                            $this->sala = "Sala UCE";
                        }
                        if($this->id_sala_some == "HQTA-HOSP PED 04"){
                            $this->sala = "Sala Pre Escolares 1";
                        }
                        if($this->id_sala_some == "HQTA-HOSP PED 05"){
                            $this->sala = "Sala Pre Escolares 2";
                        }
                        if($this->id_sala_some == "HQTA-HOSP PED 07"){
                            $this->sala = "Sala Pre Escolares 3";
                        }

                }


                if($this->c_unidad == "HQPE-AC-086"){
                        if($this->id_sala_some == "HQ-73"){
                            $this->sala = "SALA 1 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-74"){
                            $this->sala = "SALA 2 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-75"){
                            $this->sala = "SALA 3 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-76"){
                            $this->sala = "SALA 4 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-77"){
                            $this->sala = "SALA 5 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-78"){
                            $this->sala = "SALA 6 PEDIATRIA";
                        }
                        if($this->id_sala_some == "HQ-79"){
                            $this->sala = "SALA 3 NEO";
                        }

                }

                if($movimiento === 'A08'){
                    $ocupacionCaso = DB::table("t_historial_ocupaciones")
                    ->where("caso","=",$this->caso_n->id)
                    ->first();

                    // Si el paciente no esta ingresado
                    if(!$ocupacionCaso){
                        //se crea el paciente y se ingresa a la cama enviada.
                        if($this->c_unidad != null && $this->sala != null && $this->cama != null){
                           $this->asignar(); 
                        }
                        
                    }


                    if($this->id_sala_some == "HQ-104" || $this->id_sala_some == 'HQ-81'){
                        $recibido = Input::get("motivo");
                            try{
                                $this->ingresarPacienteSOME();
                                if($this->c_unidad !== null && $this->sala !== null && $this->cama !== null) {
                                    $this->asignar();
                                }
                                $this->altaSOME();

                                //(new NotificadorController())->notificar();
                                return Response::json(["exito" => $this->caso_n->paciente, "msg" => "{$this->msg}@Motivo:{$this->caso_n->motivo}({$recibido})"]);

                            }
                            catch(PDOException $e){
                                DB::rollBack();
                                return Response::json(["error" => "El paciente no está registrado", "info" => ""]);
                            }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e){
                                DB::rollBack();
                                return Response::json(["error" => "El paciente no está registrado", "info" => ""]);
                            }catch(Exception $e){
                                DB::rollBack();
                                Log::warning("{$e->getMessage()}\n{$e->getFile()}({$e->getLine()})\n{$e->getTraceAsString()}");
                                return Response::json(["error" => "Error inesperado", "info" => $e->getMessage()]);
                            }


                            
                        }
                }

                if($movimiento === 'A04' || $movimiento === 'A02' || $movimiento === 'A01'){
                    if($this->c_unidad !== null && $this->sala !== null && $this->cama !== null) {
                        $this->asignar();
                    }

                    
                }
                return Response::json(["exito" => $this->caso_n->paciente, "msg" => $this->msg]);

            }catch(PDOException $e){
                DB::rollBack();
                Log::warning("{$e->getCode()}{$e->getMessage()}\n{$e->getFile()}({$e->getLine()})\n{$e->getTraceAsString()}");
                return Response::json([
                    "error" => "Error en BD: {$e->getCode()}",
                    "info" => $e->getMessage()
                ]);
            }catch(Exception $e){
                DB::rollBack();
                Log::warning("{$e->getMessage()}\n{$e->getFile()}({$e->getLine()})\n{$e->getTraceAsString()}");
                return Response::json([
                    "error" => "Error inesperado",
                    "info" => $e->getMessage()
                ]);
            }

        }
        elseif($movimiento == 'A03'){
            $recibido = Input::get("motivo");
            try{
                $this->ingresarPacienteSOME();
                if($this->c_unidad !== null && $this->sala !== null && $this->cama !== null) {
                    $this->asignar();
                }
                $this->altaSOME();

                //(new NotificadorController())->notificar();
                return Response::json(["exito" => $this->caso_n->paciente, "msg" => "{$this->msg}@Motivo:{$this->caso_n->motivo}({$recibido})"]);

            }
            catch(PDOException $e){
                DB::rollBack();
                return Response::json(["error" => "El paciente no está registrado", "info" => ""]);
            }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e){
                DB::rollBack();
                return Response::json(["error" => "El paciente no está registrado", "info" => ""]);
            }catch(Exception $e){
                DB::rollBack();
                Log::warning("{$e->getMessage()}\n{$e->getFile()}({$e->getLine()})\n{$e->getTraceAsString()}");
                return Response::json(["error" => "Error inesperado", "info" => $e->getMessage()]);
            }
        }
        elseif($movimiento == 'N01'){
            try {
                return $this->movCamaSOME();
            }
            catch(Exception $e){
                return Response::json(["error" => "Error interno con N01", "info" => ""]);
            }
        }
        else return Response::json(["error" => "Código de movimiento inválido.", "info" => '']);
    }


}
