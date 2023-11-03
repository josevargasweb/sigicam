<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use View;
use Auth;
use TipoUsuario;
use Log;
use Carbon\Carbon;
use Excel;

class HospitalizacionDomiciliaria extends Model{

	protected $table = "hospitalizacion_domiciliaria";
	public $timestamps = false;
	protected $primaryKey = 'id';


	public function casos(){
		return $this->belongsTo("App\Models\Caso", "caso", "id");
	}

	public static function existeEnlistaPorCaso($idCaso){
		$lista=self::where("caso", "=", $idCaso)->whereNull("fecha_termino")->first();
		return (is_null($lista))?false:true;
	}

	public static function pacienteEnListaEspera($idPaciente){
		$lista=DB::table("hospitalizacion_domiciliaria as l")->join("casos as c", "c.id", "=", "l.caso")->where("c.paciente", "=", $idPaciente)
		->whereNull("l.fecha_termino")->first();
		return (is_null($lista))?false:true;
	}

	public static function promedioPacientesHospDom($inicio, $fin){

		$establecimiento = "";
		if(Auth::user()->establecimiento != null){
			$establecimiento = "c.establecimiento = ".Auth::user()->establecimiento." and ";
		}

		$promedio = DB::select(DB::Raw("select AVG(
			CASE
				WHEN h.fecha_termino is null THEN
					DATE_PART('day',CURRENT_DATE - h.fecha)
				ELSE
					CASE
						WHEN h.fecha_termino >= '".$inicio."' and h.fecha_termino <= '".$fin."' THEN
							DATE_PART('day',h.fecha_termino - h.fecha)
						ELSE
							DATE_PART('day', '".$fin."' - h.fecha)
					END

			END
			) as prom
		from hospitalizacion_domiciliaria as h
		join casos as c on c.id = h.caso
		where ".$establecimiento." h.fecha >= '".$inicio."' and h.fecha <= '".$fin."'"));

		return round($promedio[0]->prom,0);

	}

	public static function promedioMesPacientesHospDom($inicio, $fin, $mes){

		$promedio = DB::select(DB::Raw("select  AVG(
			CASE WHEN h.fecha_termino is null THEN
				DATE_PART('day',CURRENT_DATE - h.fecha)
			ELSE
				CASE WHEN h.fecha_termino >= '".$inicio."' and h.fecha_termino <= '".$fin."' THEN
					DATE_PART('day',h.fecha_termino - h.fecha)
				ELSE
					DATE_PART('day', '".$fin."' - h.fecha)
				END
			END
		) as prom, MIN(
			CASE WHEN h.fecha_termino is null THEN
				DATE_PART('day',CURRENT_DATE - h.fecha)
			ELSE
				CASE WHEN h.fecha_termino >= '".$inicio."' and h.fecha_termino <= '".$fin."' THEN
					DATE_PART('day',h.fecha_termino - h.fecha)
				ELSE
					DATE_PART('day', '".$fin."' - h.fecha)
				END
			END
		) as min, MAX(
			CASE WHEN h.fecha_termino is null THEN
				DATE_PART('day',CURRENT_DATE - h.fecha)
			ELSE
				CASE WHEN h.fecha_termino >= '".$inicio."' and h.fecha_termino <= '".$fin."' THEN
					DATE_PART('day',h.fecha_termino - h.fecha)
				ELSE
					DATE_PART('day', '".$fin."' - h.fecha)
				END
			END
		)
		from hospitalizacion_domiciliaria as h
		where EXTRACT(MONTH FROM h.fecha::TIMESTAMP) = ".$mes."
		and h.fecha >= '".$inicio."' and h.fecha <= '".$fin."'"));

		return response()->json(["prom" => round($promedio[0]->prom,0), "min" => round($promedio[0]->min,0), "max" => round($promedio[0]->max,0)]);

	}

	public static function promedioHospitalizacionCompleta($inicio, $fin){

		//seleccionar los casos que no ingresen por hospitalizacion domiciliaria
		$casos = DB::select(DB::Raw("select
		c.id as idCaso,
		CASE WHEN t.fecha_liberacion is null THEN
			DATE_PART('day',CURRENT_DATE - t.fecha_ingreso_real)
		ELSE
			DATE_PART('day',t.fecha_liberacion - t.fecha_ingreso_real)
		END as dias
		from casos as c
		left join t_historial_ocupaciones as t on t.caso = c.id
		where c.detalle_procedencia <> 'Hospitalizacion Domiciliaria'
		and t.fecha_ingreso_real >= '".$inicio."' and t.fecha_ingreso_real <= '".$fin."' "));


		return response()->json($casos);

	}

	public static function cie10MasComunHospDomiciliaria($inicio, $fin){

		//seleccionar los casos que no ingresen por hospitalizacion domiciliaria
		$diagnosticos = DB::select(DB::Raw("select d.diagnostico , count(d.diagnostico) as total
			from hospitalizacion_domiciliaria as h
			join diagnosticos as d on d.caso = h.caso
			and  h.fecha >= '".$inicio."' and h.fecha <= '".$fin."'
			group by d.diagnostico
			order by total desc
		"));

		$ordenado_diagn = [];
		$ordenado_cant = [];
		$otros = 0; 
		foreach($diagnosticos as $key => $diagnostico){

			if($key < 10){
				array_push($ordenado_diagn, $diagnostico->diagnostico);
				array_push($ordenado_cant, $diagnostico->total);
			}else{
				$otros += $diagnostico->total;
			}

		}

		if(isset($key) && $key >= 9){
			array_push($ordenado_diagn, "Otros");
			array_push($ordenado_cant, $otros);
		}

		if ($diagnosticos) {
			return response()->json(["diagnostico0" =>$diagnosticos[0]->diagnostico, "diagnosticos" => $ordenado_diagn, "cant_diagnostico" => $ordenado_cant]);
		}
		return response()->json(["diagnostico0" => "Sin información", "diagnosticos" => $ordenado_diagn, "cant_diagnostico" => $ordenado_cant]);
		

	}

	public static function obtenerListaPacientes($idEst){
		$response=[];
		$datos = DB::select(DB::Raw("
			select
			e.idEvolucion,
			p.id as idPaciente,
			p.nombre, 
			p.apellido_paterno as apellidoP,
			p.apellido_materno as apellidoM,
			p.rut,
			p.dv,
			p.sexo,
			c.id as idCaso,
			p.telefono,
			p.calle,
			p.numero,
			co.nombre_comuna as comuna,
			p.observacion,
			l.id as idLista,
			l.fecha
			from hospitalizacion_domiciliaria as l  
			inner join casos as c on c.id = l.caso
			inner join pacientes as p on p.id = c.paciente
			left join 
			(select 
				max(e.id) as idEvolucion,
				c.id idCaso
				from hospitalizacion_domiciliaria as l  
				left join casos as c on l.caso = c.id
				left join t_evolucion_casos as e on e.caso = c.id
				left join riesgos as r on e.riesgo_id = r.id
				where c.establecimiento = $idEst
				and l.fecha_termino is null
				and e.riesgo_id is not null
				group by (c.id)
			) as e on e.idCaso= c.id 
			left join comuna as co on co.id_comuna = p.id_comuna
			where l.fecha_termino is null
		"));

		foreach($datos as $dato){
			$idPaciente=$dato->idpaciente;
			$apellido=$dato->apellidop." ".$dato->apellidom;
			$nombreCompleto = $dato->nombre. " ".$apellido;
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "-" : $dato->rut."-".$dv;
			$direccion = ($dato->calle && $dato->numero)? $dato->calle.", ".$dato->numero.", ".$dato->comuna: $dato->comuna;
			$telefono = 'No posee';
			$copy_t_telefonos = '';
			$t_telefono = Telefono::where('id_paciente',$idPaciente)->select('tipo','telefono')->get();
			if(count($t_telefono) > 0){				
				$copy_t_telefonos = $t_telefono->toJson();
				foreach ($t_telefono as $key => $tele) {
					if($key == 0){
						$telefono = "<br>(".$tele->tipo.") ".$tele->telefono ."<br>";
					}else{
						$telefono .= "(".$tele->tipo.") ".$tele->telefono ."<br>";
					}
				}
			}else{
				if( !is_null($dato->telefono) && $dato->telefono != '' && $dato->telefono != '-'){
					$telefono = "<br>(Casa) ".$dato->telefono;
				}
			}
			$fecha=date("d-m-Y H:i:s", strtotime($dato->fecha));
			$dato->diagnostico = HistorialDiagnostico::where("caso","=",$dato->idcaso)->select("diagnostico","id_cie_10","comentario")->orderBy('fecha','desc')->first();
			$fecha_comp = Carbon::parse($dato->fecha);
			$diff_dias =  $fecha_comp->diffInDays(Carbon::now());
			
			$categoria = "";
			if($dato->idevolucion != NULL){
				$categoria = EvolucionCaso::select("categoria", "fecha")
				->join("riesgos as r", "r.id", "=", "t_evolucion_casos.riesgo_id")
				->where("t_evolucion_casos.id", "=", $dato->idevolucion)->first();				
			}

			if($categoria == ""){
				$categoria_nombre = "No posee";
			}else{
				$categoria_nombre = $categoria->categoria;
			}

			$sexo = $dato->sexo;
			
			$opciones=View::make("HospitalizacionDom/OpcionesLista", [
				"idCaso" => $dato->idcaso,
				"idLista" => $dato->idlista,
				"nombreCompleto" => $nombreCompleto,
				"idPaciente" => $idPaciente,
				"nombrePaciente" => $nombreCompleto,
				"rutPaciente" => $rut,
				"sexo" => $sexo]
			)->render();

			$info_comentario = DomiciliariaComentario::where("id_hosp_dom", $dato->idlista)->orderBy("fecha", "desc")->first();

			$comentario = ($info_comentario)?$info_comentario->comentario."<br> (Fecha: ".$info_comentario->fecha.")":"";

			$datos_domicilio = [
				$dato->nombre." ".$apellido,
				"<strong style='color:#000000'>(".$dato->diagnostico->id_cie_10.")</strong> ".$dato->diagnostico->diagnostico ."<br> <strong style='color:#000000'>Comentario:</strong>".$dato->diagnostico->comentario,
				$fecha." <b>(".$diff_dias." días)</b>",
				"<div style='text-align:center'>".$categoria_nombre."</div>",
				$comentario,
				 " <strong style='color:#000000'>Dirección: </strong>".$direccion."<br><br> <strong style='color:#000000'>Observación:</strong> ".$dato->observacion, $telefono
			];

			if(Auth::user()->tipo == TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER   || Auth::user()->tipo == TipoUsuario::MASTERSS || Auth::user()->tipo == TipoUsuario::ENCARGADO_HOSP_DOM){
				$datos_domicilio = [
					$opciones,
					$dato->nombre." ".$apellido, 
					$rut,"<strong style='color:#000000'>(".$dato->diagnostico->id_cie_10.")</strong> ".$dato->diagnostico->diagnostico ."<br> <strong style='color:#000000'>Comentario:</strong>".$dato->diagnostico->comentario, $fecha." <b>(".$diff_dias." días)</b>",
					"<div style='text-align:center'>".$categoria_nombre."</div>",
					$comentario, 
					" <strong style='color:#000000'>Dirección:</strong> ".$direccion."<br> <strong style='color:#000000'>Observación:</strong> ".$dato->observacion,
					$telefono
				];
			}		
			$response[] = $datos_domicilio;
			

		}
		return $response;
	}



	public static function infoHospitalizacionDomiciliaria(){

		$idEst = Auth::user()->establecimiento;
		$response=[];
		$datos=DB::table("hospitalizacion_domiciliaria as l")->join("casos as c", "c.id", "=", "l.caso")->join("pacientes as p", "p.id", "=", "c.paciente")->join("usuarios as u", "l.usuario", "=", "u.id")
		->whereNull("l.fecha_termino")->where("u.establecimiento", $idEst)->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.dv as dv",
			"l.fecha as fecha",
			"c.id as idCaso",
			"l.id as idLista")->get();

		foreach($datos as $dato){
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "" : $dato->rut."-".$dv;
			$fecha=date("d-m-Y H:i:s", strtotime($dato->fecha));
			$dato->diagnostico = HistorialDiagnostico::where("caso","=",$dato->idCaso)->select("diagnostico")->first();
			$fecha_comp = Carbon::parse($dato->fecha);
			$diff_dias =  $fecha_comp->diffInDays(Carbon::now());

			$response [] = [
				$dato->nombre." ".$dato->apellidoP." ".$dato->apellidoM,
				$rut,
				($dato->diagnostico)?$dato->diagnostico->diagnostico:"",
				$fecha,
				$diff_dias
			];
		}
		return $response;
	}

	public static function excelHospitalizacionDomiciliaria(){
		Excel::create('HospitalizacionDomiciliaria', function($excel) {
			$excel->sheet('HospitalizacionDomiciliaria', function($sheet) {

				$sheet->mergeCells('A1:P1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});
				#
				$idEst = Auth::user()->establecimiento;
				$establecimiento = Establecimiento::where("id",$idEst)->first();
				$response = HospitalizacionDomiciliaria::infoHospitalizacionDomiciliaria();
                $hoy = Carbon::now();

                $sheet->loadView('HospitalizacionDom.ReporteListaEsperaExcel', [
					"datos" => $response,
					"hospital" => $establecimiento->nombre,
					"fecha" => $hoy->format("d/m/Y")
					]
				);
			});
		})->download('xls');
	}


}

?>
