<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class RegistroVisitas extends Model{

	protected $table = "registro_visitas";
	public $timestamps = false;
	protected $primaryKey = "id_registro_visitas";
	
	public function guardar($request){
		$this->caso = $request->id_caso;
		$this->id_paciente = $request->id_paciente;
		$this->id_unidad = $request->id_servicio;
		$this->id_sala = $request->id_sala;
		$this->id_cama = $request->id_cama;
		$this->id_area = $request->id_area;
		$this->observaciones = $request->observaciones;
		$this->tipo_identificacion = $request->tipo_identificacion_acompanante;
		$this->n_identificacion = $request->n_identificacion_acompanante;
		$this->nombre = $request->nombre_acompanante;
		$this->apellido = $request->primer_apellido_acompanante;
		$this->telefono = $request->telefono;
		$this->visible = true;
		$this->usuario_responsable = Auth::user()->id;
		
		$this->save();
	}
	public function buscarCasoPorNombre($request){
		
		$datos = DB::select("SELECT
		h.caso as id_caso,
		c.id as id_cama,
		c.id_cama as nombre_cama,
		s.id as id_sala,
		s.nombre as nombre_sala,
		e.id AS id_establecimiento,
		e.nombre AS nombre_establecimiento,
		u.id AS id_servicio,
		u.alias AS nombre_servicio,
		u.id_area_funcional, 
		af.nombre AS nombre_area, 
		cv.recibe_visitas,    
		cv.num_personas_visitas,
		cv.cant_horas_visitas,
		p.identificacion,
		p.rut,
		p.rut_madre,
		p.n_identificacion,
		p.nombre,
		p.apellido_paterno,
		p.apellido_materno,
		p.nombre_social,
		p.rn,
		p.id AS id_paciente,
		TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
		(SELECT
		(
			SELECT
			COUNT(*)AS cantidad
			FROM registro_visitas
			WHERE
			caso = h.caso
			AND fecha_salida_visita IS NULL
			AND fecha_entrada_visita::DATE = CURRENT_DATE
		)
		<
		COALESCE(
		(
			SELECT 
			num_personas_visitas
			FROM configuracion_visitas 
			WHERE
			id_caso = h.caso
			AND visible IS TRUE
		),
		1
		))AS permite_visitas
		FROM t_historial_ocupaciones h
		JOIN casos ca ON h.caso=ca.id 
		JOIN camas c ON h.cama = c.id 
		JOIN salas s ON c.sala = s.id
		JOIN unidades_en_establecimientos u ON s.establecimiento = u.id
		JOIN establecimientos_antiguos e ON u.establecimiento = e.id
		JOIN lista_transito lt ON lt.caso=h.caso
		JOIN area_funcional af ON af.id_area_funcional=u.id_area_funcional
		LEFT JOIN configuracion_visitas cv ON cv.id_caso=h.caso AND cv.visible IS TRUE
		JOIN pacientes p ON p.id = ca.paciente
		WHERE 
		h.fecha_liberacion IS NULL 
		AND h.motivo IS NULL 
		AND h.fecha_ingreso_real IS NOT NULL
		AND lt.fecha_termino IS NOT NULL
		AND h.caso IN (
			SELECT
			casos.id
			FROM casos
			JOIN pacientes pa on casos.paciente = pa.id
			WHERE
			(
				SOUNDEXESP(SPLIT_PART(pa.nombre, ' ', 1)) = SOUNDEXESP(?)
				AND SOUNDEXESP(pa.apellido_paterno) = SOUNDEXESP(?)
			)
			AND casos.fecha_termino IS NULL
		)
		",[$request->nombre,$request->apellido]);
        return $datos;
	}
	public function buscarCaso($request){
		$sql = "";
		switch($request->tipo_identificacion)
		{
			case "rut":
				$sql = "(identificacion = 'run' AND pa.rut = ?) ";
				break;
			case "rut_madre":
				$sql = "(identificacion = 'run' AND pa.rn = 'si' AND pa.rut_madre = ?)";
				break;
			case "pasaporte":
				$sql = "(identificacion = 'pasaporte' AND pa.n_identificacion = ?)";
				break;
			default:
				return null;
		}
		$id_caso = DB::select("SELECT
		ca.id,
		pa.id AS id_paciente,
		pa.nombre,
		pa.apellido_paterno
		FROM casos ca 
		JOIN pacientes pa on ca.paciente = pa.id
		WHERE
		$sql
		AND ca.fecha_termino IS NULL",[$request->n_identificacion]);
		if($id_caso)
		{
			$id_caso = $id_caso[0];
		}
		else {
			return null;
		}
		
		$datos = DB::select("SELECT
		    h.caso as id_caso,
		    c.id as id_cama,
		    c.id_cama as nombre_cama,
		    s.id as id_sala,
		    s.nombre as nombre_sala,
		    e.id AS id_establecimiento,
		    e.nombre AS nombre_establecimiento,
		    u.id AS id_servicio,
		    u.alias AS nombre_servicio,
		    u.id_area_funcional, 
		    af.nombre AS nombre_area, 
		    cv.recibe_visitas,    
		    cv.num_personas_visitas,
		    cv.cant_horas_visitas
		FROM t_historial_ocupaciones h
		JOIN casos ca ON h.caso=ca.id 
		JOIN camas c ON h.cama = c.id 
		JOIN salas s ON c.sala = s.id
		JOIN unidades_en_establecimientos u ON s.establecimiento = u.id
		JOIN establecimientos_antiguos e ON u.establecimiento = e.id
		JOIN lista_transito lt ON lt.caso=h.caso
		JOIN area_funcional af ON af.id_area_funcional=u.id_area_funcional
		LEFT JOIN configuracion_visitas cv ON cv.id_caso=h.caso AND cv.visible IS TRUE
		WHERE 
		h.fecha_liberacion IS NULL 
		AND h.motivo IS NULL 
		AND h.fecha_ingreso_real IS NOT NULL
		AND lt.fecha_termino IS NOT NULL
		AND h.caso= ?",[$id_caso->id]);
		if($datos){
			$datos[0]->nombre = $id_caso->nombre;
			$datos[0]->apellido_paterno = $id_caso->apellido_paterno;
			$datos[0]->id_caso = $id_caso->id;
			$datos[0]->id_paciente = $id_caso->id_paciente;
                        return (object)$datos[0];
		}
        return new \stdClass();
	}
	public function buscarVisita($request){
		$registro = DB::select(""
			. "SELECT "
			. "rv.id_registro_visitas,"
			. "rv.nombre,"
			. "rv.apellido,"
			. "p.nombre AS nombre_paciente, "
			. "p.apellido_paterno AS apellido_paciente "
			. "FROM registro_visitas rv "
			. "INNER JOIN pacientes p ON p.id = rv.id_paciente "
			. "WHERE "
			. "LOWER(rv.n_identificacion) = LOWER(?) "
			. "AND rv.tipo_identificacion = ? "
			. "AND rv.fecha_salida_visita IS NULL "
			. "AND rv.fecha_entrada_visita::DATE = CURRENT_DATE",
			[
				$request->n_identificacion,
				$request->tipo_identificacion
			]);
		if($registro){
			return (object)$registro[0];
		}
		return new \stdClass();
	}
	public function guardarSalida($request){
		$rv = RegistroVisitas::find($request->id_registro);
		$rv->fecha_salida_visita = date("Y-m-d H:i:s");
		$rv->save();
	}
	public function visitasPermitidas($id_caso){
		$permitir = DB::select("SELECT
		(
			SELECT
			COUNT(*)AS cantidad
			FROM registro_visitas
			WHERE
			caso = ?
			AND fecha_salida_visita IS NULL
			AND fecha_entrada_visita::DATE = CURRENT_DATE
		)
		<
		COALESCE(
		(
			SELECT 
			num_personas_visitas
			FROM configuracion_visitas 
			WHERE
			id_caso = ?
			AND visible IS TRUE
		),
		1
		)AS permitir",[
			$id_caso,
			$id_caso
		]);
		
		return $permitir[0]->permitir;
	}
}