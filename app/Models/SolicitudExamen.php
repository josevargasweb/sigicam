<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class SolicitudExamen extends Model{
	
	protected $table = "solicitud_examen";
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
			(
				SELECT peso FROM formulario_ie_fisico_general WHERE caso = c.id
			)AS peso,
			(
				SELECT altura FROM formulario_ie_fisico_general WHERE caso = c.id
			)AS talla
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
		$this->caso = $request->caso;
		$this->edad = (isset($request->edad_paciente_solicitud_examen)  && $request->edad_paciente_solicitud_examen != '')?$request->edad_paciente_solicitud_examen:null;
		$this->diagnostico = strip_tags($request->id_diagnostico_paciente_solicitud_examen);
		$this->peso = strip_tags($request->peso_paciente_solicitud_examen);
		$this->talla = strip_tags($request->talla_paciente_solicitud_examen);
		$this->{$request->categoria_prioridad_solicitud_examen} = true;
		
		foreach($request->examen_solicitado_solicitud_examen as $examenes_solicitados){
			$this->{$examenes_solicitados} = true;
		}
		$this->creado_por = Auth::user()->id;
		$this->save();
	}
	public function historial($caso){
		return DB::select(
			"SELECT
			en.id,
			en.urgente,
			en.medio_urgente,
			en.puede_esperar,
			en.ecocardiograma,
			en.test_esfuerzo,
			en.holter_presion,
			en.holter_arritmia,
			u.nombres || ' ' || u.apellido_paterno AS nombre_usuario,
			TO_CHAR(en.fecha,'DD-MM-YYYY HH24:MI')AS fecha
			FROM solicitud_examen en
			INNER JOIN diagnosticos d ON d.id = en.diagnostico
			INNER JOIN usuarios u ON u.id = en.creado_por
			WHERE en.visible IS TRUE
			AND en.caso = ?"
			,[$caso]);
	}
	public function eliminar($id){
		DB::update("UPDATE solicitud_examen SET visible = FALSE WHERE id = ?",[$id]);
	}
	public function cargar($id){
		$datos_personales = DB::select("SELECT 
			p.nombre || ' ' || p.apellido_paterno || ' ' || p.apellido_materno AS nombre,
			p.rut || '-' || p.dv AS rut,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
			en.edad,
			d.diagnostico,
			en.peso,
			en.talla,
			en.urgente,
			en.medio_urgente,
			en.puede_esperar,
			en.ecocardiograma,
			en.test_esfuerzo,
			en.holter_presion,
			en.holter_arritmia,
			TO_CHAR(en.fecha,'DD-MM-YYYY')AS fecha,
			en.id
			FROM solicitud_examen en
			INNER JOIN casos c ON c.id = en.caso
			INNER JOIN pacientes p ON c.paciente = p.id
			INNER JOIN diagnosticos d ON d.id = en.diagnostico
			WHERE en.id = ?
			",[$id]);
		
		if($datos_personales)
		{
			return $datos_personales[0];
		}
		throw new \Exception("No existe la solicitud");
	}
	public function pdf($id){
		$datos_personales = DB::select("SELECT 
			p.nombre || ' ' || p.apellido_paterno || ' ' || p.apellido_materno AS nombre,
			p.rut || '-' || p.dv AS rut,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY')AS fecha_nacimiento,
			en.edad,
			d.diagnostico,
			en.peso,
			en.talla,
			en.urgente,
			en.medio_urgente,
			en.puede_esperar,
			en.ecocardiograma,
			en.test_esfuerzo,
			en.holter_presion,
			en.holter_arritmia,
			TO_CHAR(en.fecha,'DD-MM-YYYY')AS fecha
			FROM solicitud_examen en
			INNER JOIN casos c ON c.id = en.caso
			INNER JOIN pacientes p ON c.paciente = p.id
			INNER JOIN diagnosticos d ON d.id = en.diagnostico
			WHERE en.id = ?
			",[$id]);
		
		if($datos_personales)
		{
			return $datos_personales[0];
		}
	}
}

?>