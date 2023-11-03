<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Auth;
use View;
use Log;
use Excel;
use Carbon\Carbon;

class ListaDerivados extends Model implements Auditable{
	
	use \OwenIt\Auditing\Auditable;
    
    protected $table = "lista_derivados";
    //public $incrementing =  true;
    public $timestamps = true;
    //protected $primaryKey = 'caso'; // se cambio caso como primary key para poder hacer el find en quitarRecuperacion
    protected $primaryKey = 'id_lista_derivados';

	//guardando informacion en dudits
	protected $auditInclude = [];
	protected $auditTimestamps = true;
    protected $auditThreshold = 10;


    // public static function cerrarListaDerivado($idCaso){
    //     try{
    //         DB::beginTransaction();
    //         $listaDerivacion = ListaDerivados::where('caso', '=', $idCaso)->whereNull('fecha_egreso_lista')->first();
    //         if($listaDerivacion){
    //             $listaDerivacion->fecha_egreso_lista = date('Y-m-d H:i');
    //             $listaDerivacion->id_usuario_egresa = Auth::user()->id;
    //             $listaDerivacion->save();
    //         }
    //         DB::commit();
	// 	}catch(\Exception $e){
	// 		DB::rollBack();
	// 		return response()->json(["error" => "Error al sacar de lista derivacion. ".$e]);
	// 	}
	// }

    public static function casoDerivado($idCaso){
        $derivado = ListaDerivados::where("caso",$idCaso)->whereNull('fecha_egreso_lista')->first();
        if($derivado){
            return true;
        }
		return false;
        		
    }
    
    public static function obtenerListaDerivados(){
        $idEsta = Auth::user()->establecimiento;
		
		$response=[];
		$idLista = 0;
		$data = DB::table("lista_derivados as l")
			->join("casos as c","c.id","=","l.caso")
			->join("pacientes as p","p.id","=","c.paciente")
			->join("usuarios as u","l.id_usuario_ingresa","=","u.id")
			->join("procedencias as pro","pro.id","=","c.procedencia")
			->where("c.establecimiento",$idEsta)
			->whereNull("l.fecha_egreso_lista")
			->whereNull("l.id_usuario_egresa")
			->select(
				"l.id_lista_derivados as idLista",
				"c.id as idCaso",
				"p.id as idPaciente",
				"p.nombre as nombre",
				"p.apellido_paterno as apellidoP",
				"p.apellido_materno as apellidoM",
				"p.rut as rut",
				"p.dv as dv",
				"p.fecha_nacimiento",
				"pro.nombre as procedencia",
				"l.fecha_ingreso_lista as fechaIngreso")
			->orderBy("l.fecha_ingreso_lista", "desc")
			->get();
			
		$inicioLabel = "<label hidden>";
		$finLabel = "</label>";

		foreach($data as $d){
			//Declaracion de variables 
			$detalleCama = "";
			$servicio = "";
			$testUrl = "";
			$idCama = "";
			$idSala = "";

			$idCaso = $d->idCaso;
			$idLista = $d->idLista;
			$apellido=$d->apellidoP." ".$d->apellidoM;
			$nombre_completo = $d->nombre." ".$apellido;
			$dv=($d->dv == 10) ? 'K' : $d->dv;
			$rut = (empty($d->rut)) ? "" : $d->rut."-".$dv;
			$fecha_nacimiento = date("d-m-Y", strtotime($d->fecha_nacimiento));
			$fecha_ingreso = date("d-m-Y", strtotime($d->fechaIngreso));
			$idPaciente = $d->idPaciente;

			$procedencia = $d->procedencia;

			$fecha_liberacion = DB::table('t_historial_ocupaciones as t')
				->join('lista_derivados as l', 'l.caso', '=', 't.caso')
				->select('t.fecha_liberacion as fecha')
				->where('l.id_lista_derivados', $idLista)
				->first();

			if($fecha_liberacion == '[]'){
				$fecha_liberacion = null;
			}

			//Informacion de la cama
			$cama = DB::table('t_historial_ocupaciones as t')
				->join("camas as c", "c.id", "=", "t.cama")
				->join("salas as s", "c.sala", "=", "s.id")
				->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
				->where("caso", "=", $idCaso)
				->orderBy("t.created_at", "desc")
				->select("cama", "c.id_cama as id", "s.id as idSala", "s.nombre as sala_nombre", "uee.alias as nombre_unidad", "uee.url","t.fecha_liberacion")
				->first();

			//indica que aun no ha sido asignado a la cama y se encuentra en espera de hospitalizacion
			$lista_transito = DB::table('lista_transito as lt')
				->select('lt.fecha_termino')
				->where('lt.caso', '=', $idCaso)
				->whereNull('lt.fecha_termino')
				->first();

			//informacion cama	
			if($cama){
				if($lista_transito){
					//si el paciente se encuentra en lisat de transito
					$servicio = "Pre-Asignado a " .$cama->nombre_unidad;
					$detalleCama = "Pre-Asignado a " .$cama->sala_nombre." (".$cama->id.")";
				}else{
					//sino indicar la cama en la que se encuentra
					$servicio = $cama->nombre_unidad;				
					$detalleCama = $cama->sala_nombre." (".$cama->id.")";
				}			

				//test url cama
				$testUrl = $cama->url;
				$idCama = $cama->cama;
				$idSala = $cama->idSala;	
			}			
			
			//ultimo diagnostico paciente
			$diag = HistorialDiagnostico::where("caso","=",$idCaso)->orderby("fecha","desc")->select("diagnostico", "comentario as co")->first();
			$d->diagnostico = $diag->diagnostico;
			$d->co = $diag->co;

			if($d->co == ""){
				$d->co = "&nbsp";
			}

			//ultimo comentario en la bitacora
			$ultimoComentario = ListaDerivadosComentarios::select('comentario')
				->where('caso',$idCaso)
				->orderBy('fecha', 'desc')
				->first();
			
			//Si tiene ubisacion dar posibilidad de ir a la Unidad
			if($testUrl != "" && $cama->fecha_liberacion == null){
				$volver_unidad = "  <form style='display: hidden' action='../unidad/".$testUrl."' method='GET'>
					<input hidden type='text' name='paciente' value='".$idPaciente."'>
					<input hidden type='text' name='id_sala' value='".$idSala."'>
					<input hidden type='text' name='id_cama' value='".$idCama."'>
					<input hidden type='text' name='caso' value='".$idCaso."'>
					<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
				</form>";
			}else{
				$volver_unidad = "<h4><span class='label label-danger'>No disponible</span></h4>";
			}
			
								
			$opciones = View::make("Urgencia/OpcionesListaDerivados", ["idCaso" => $idCaso, "idLista" => $idLista])->render();

			$response[] = [
				"id" => $idLista,
				"rut" => $rut, 
				"nombre_completo" => $nombre_completo, 
				"fecha_nacimiento" => $inicioLabel.$d->fecha_nacimiento.$finLabel.$fecha_nacimiento,
				"fecha_nacimiento_excel" => $fecha_nacimiento,
				"fecha_ingreso" => $inicioLabel.$d->fechaIngreso.$finLabel.$fecha_ingreso,
				"fecha_ingreso_excel" => $fecha_ingreso,
				"opciones" => $opciones,
				"cama" => $detalleCama,
				"ultimo_comentario" => $ultimoComentario->comentario,
				"volver_unidad" => $volver_unidad,
				"servicio" => $servicio,
				"procedencia" => $procedencia,
				"diagnostico" => $d->diagnostico."  <label> <strong style='color:black;'>Comentario: ".$d->co."</strong></label>",
				"diagCom" => $d->diagnostico. ". Comentario: " .$d->co
			];

		}
		return $response;
	}

	public static function excelListaDerivados(){
		Excel::create('ListaDerivados', function($excel) {
			$excel->sheet('ListaDerivados', function($sheet){

				$sheet->mergeCells('A1:J1');
				$sheet->setAutosize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row){

					$row->setBackground('#1B9966');
					$row->setFontColor('#FFFFFF');
					$row->setAlignment('center');
				});

				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$data = ListaDerivados::obtenerListaDerivados();

				$sheet->loadview('Urgencia.ReporteListaDerivadosExcel', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data
				]);
			});
		})->download('xls');
	}
	
	public static function historialDerivados(){
		
		return DB::table("lista_derivados as l")
		->join("casos as c","c.id","=","l.caso")
		->join("pacientes as p","p.id","=","c.paciente")
		// ->join("ultimas_ocupaciones as uo","uo.paciente","=","p.id")
		->where("c.establecimiento", Auth::user()->establecimiento)
		->whereNotNull("fecha_egreso_lista")
		// ->where("estado","=","Realizada")
		->select(
			"l.id_lista_derivados as idLista",
			"l.caso as idCaso",
			"p.id as idPaciente",
			"p.rut as rut",
			"p.dv as dv",
			"p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			// "uo.fecha_ingreso_real as fechaHospitalizacion",
			"l.fecha_ingreso_lista as fechaIngresoDerivacion",
			"l.fecha_egreso_lista as fechaEgresoDerivacion")
			->orderBy("l.fecha_ingreso_lista", "desc")
			->get();
	}

}

?>