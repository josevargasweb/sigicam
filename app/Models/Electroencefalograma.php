<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Electroencefalograma extends Model{
	
	protected $table = "formulario_solicitud_electroencefalograma";
	protected $primaryKey = "id_formulario_solicitud_electroencefalograma";
	public $timestamps = false;
	
	public function datosPaciente($caso){
		$datos_personales = DB::select("SELECT 
			p.nombre || ' ' || p.apellido_paterno || ' ' || p.apellido_materno AS nombre,
			p.rut || '-' || p.dv AS rut,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
			EXTRACT(YEAR FROM AGE(p.fecha_nacimiento))AS edad,
			(
				SELECT
				id
				FROM diagnosticos
				WHERE caso = c.id
				ORDER BY fecha DESC
				LIMIT 1
			)AS id_diagnostico,
			(
				SELECT
				diagnostico
				FROM diagnosticos
				WHERE caso = c.id
				ORDER BY fecha DESC
				LIMIT 1
			)AS diagnostico,
			c.prevision
			FROM pacientes p
			INNER JOIN casos c ON c.paciente = p.id
			WHERE c.id = ?
			",[$caso]);
		
		$dato_personal = null;
		if($datos_personales){
			$dato_personal = $datos_personales[0];
		}
		else{
			throw new \Exception("No existe el paciente");
		}
		
		return $dato_personal;
	}
	public function guardar($request){
		$this->id_caso = $request->caso;
		$this->edad = (isset($request->edad_paciente_electroencefalograma)  && $request->edad_paciente_electroencefalograma != '')?$request->edad_paciente_electroencefalograma:null; 
		$this->id_diagnostico = $request->id_diagnostico_paciente_electroencefalograma;
		$this->comentario_diagnostico = strip_tags($request->comentario_diagnostico_paciente_electroencefalograma);
		$this->procedencia = strip_tags($request->procedencia_paciente_electroencefalograma);
		$this->lesion_localizacion = $request->lesion_localizacion_paciente_electroencefalograma;
		$this->intervencion_area = $request->intervencion_area_paciente_electroencefalograma;
		$this->medicamento = $request->medicamento_paciente_electroencefalograma;
		$this->fecha_ultima_crisis = $request->fecha_ultima_crisis_paciente_electroencefalograma ? $request->fecha_ultima_crisis_paciente_electroencefalograma : null;
		$this->lateralidad = $request->categoria_lateralidad_electroencefalograma;
		
		foreach($request->examen_solicitado_electroencefalograma as $examenes_solicitados){
			$this->{$examenes_solicitados} = true;
		}
		
		$this->medicamentos = $request->medicamentos_paciente_electroencefalograma;
		$this->dosis = $request->dosis_paciente_electroencefalograma;
		$this->via_administracion = $request->via_administracion_paciente_electroencefalograma;
		$this->horario_previo_examen = $request->horario_previo_examen_paciente_electroencefalograma ? $request->horario_previo_examen_paciente_electroencefalograma : null;
		
		$this->creado_por = Auth::user()->id;
		$this->save();
	}
	public function historial($caso){
		return DB::select(
			"SELECT
			en.*,
			en.id_formulario_solicitud_electroencefalograma AS id,
			TO_CHAR(en.fecha_ultima_crisis,'DD-MM-YYYY')AS fecha_ultima_crisis,
			TO_CHAR(en.fecha,'DD-MM-YYYY')AS fecha,
			u.nombres || ' ' || u.apellido_paterno AS nombre_usuario
			FROM formulario_solicitud_electroencefalograma en
			INNER JOIN usuarios u ON u.id = en.creado_por
			INNER JOIN diagnosticos d ON d.id = en.id_diagnostico
			WHERE en.visible IS TRUE
			AND en.id_caso = ?"
			,[$caso]);
	}
	public function eliminar($id){
		DB::update("UPDATE formulario_solicitud_electroencefalograma SET visible = FALSE WHERE id_formulario_solicitud_electroencefalograma = ?",[$id]);
	}
	public function cargar($id){
		$datos_personales = DB::select("SELECT 
			p.nombre || ' ' || p.apellido_paterno || ' ' || p.apellido_materno AS nombre,
			p.rut || '-' || p.dv AS rut,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
			en.*,
			en.id_formulario_solicitud_electroencefalograma AS id,
			TO_CHAR(en.fecha_ultima_crisis,'DD-MM-YYYY')AS fecha_ultima_crisis,
			TO_CHAR(en.horario_previo_examen,'HH24:MI')AS horario_previo_examen,
			c.prevision,
			d.diagnostico,
			TO_CHAR(en.fecha,'DD-MM-YYYY')AS fecha
			FROM formulario_solicitud_electroencefalograma en
			INNER JOIN casos c ON c.id = en.id_caso
			INNER JOIN pacientes p ON c.paciente = p.id
			INNER JOIN diagnosticos d ON d.id = en.id_diagnostico
			WHERE en.id_formulario_solicitud_electroencefalograma = ?
			",[$id]);
		
		if($datos_personales)
		{
			return $datos_personales[0];
		}
		throw new \Exception("No existe la solicitud");
	}
	public function pdf($id){
		$datos_personales = DB::select("SELECT 
			en.*,
			p.nombre || ' ' || p.apellido_paterno || ' ' || p.apellido_materno AS nombre,
			p.rut || '-' || p.dv AS rut,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
			TO_CHAR(en.fecha_ultima_crisis,'DD-MM-YYYY')AS fecha_ultima_crisis,
			TO_CHAR(en.horario_previo_examen,'HH24:MI')AS horario_previo_examen,
			d.diagnostico,
			c.prevision,
			TO_CHAR(en.fecha,'DD-MM-YYYY')AS fecha
			FROM formulario_solicitud_electroencefalograma en
			INNER JOIN casos c ON c.id = en.id_caso
			INNER JOIN pacientes p ON c.paciente = p.id
			INNER JOIN diagnosticos d ON d.id = en.id_diagnostico
			WHERE en.id_formulario_solicitud_electroencefalograma = ?
			",[$id]);
		
		if($datos_personales)
		{
			return $datos_personales[0];
		}
	}
}

?>