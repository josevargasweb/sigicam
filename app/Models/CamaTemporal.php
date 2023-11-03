<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class CamaTemporal extends Model
{
    protected $table = 'camas_temporales';

    protected $primaryKey = 'id';
	
	public $timestamps = false;

	
	public function traerCantidadCamas($unidad){
		$cantidad = DB::select("SELECT
		COUNT(*)AS total
		FROM camas_temporales
		WHERE
		unidad = ?
		AND visible IS TRUE",[$unidad]);
		return $cantidad[0]->total;
	}
	public function traerCamasDeUnidad($unidad){
		return $this->detalleCamas($unidad);
	}
	public function moverACamaTemporal($caso){
		
		$ids = $this->traerUnidadHistorial($caso);
		
		if($ids == null){
			throw new \Exception("No se han encontrado los datos del caso");
		}
		
		if($this->traerCantidadCamas($ids->id_unidad) >= 4){
			throw new \Exception("Ya posee el límite de 4 camas temporales. Se recomienda sacar algún paciente de la lista o esperar a que se desocupe un espacio");
		}
		
		$this->unidad = $ids->id_unidad;
		$this->caso = $caso;
		$this->id_historial_ocupaciones = $ids->id_historial_ocupaciones;
		$this->usuario = Auth::user()->id;
		
		$this->save();
	}
	public function cerrarHistorial($caso){
		$tho = $this->traerUnidadHistorial($caso);
		if($tho == null){
			throw new \Exception("No se han encontrado los datos del caso");
		}
		DB::update("UPDATE t_historial_ocupaciones SET motivo = 'traslado interno', fecha_liberacion = '" . date("Y-m-d H:i:s") . "' WHERE id = ?",[$tho->id_historial_ocupaciones]);
	}
	public function ocultarCaso($caso){
		DB::update("UPDATE camas_temporales SET visible = false WHERE caso = ?",[$caso]);
	}
	private function traerUnidadHistorial($caso) {
		$ids = DB::select("SELECT 
		uee.id AS id_unidad,
		t.id AS id_historial_ocupaciones
		FROM t_historial_ocupaciones t
		INNER JOIN camas c on c.id = t.cama
		INNER JOIN salas s ON c.sala = s.id
		INNER JOIN unidades_en_establecimientos uee ON s.establecimiento = uee.id
		WHERE caso = ?
		AND t.motivo IS NULL
		",[$caso]);
		if($ids){
			return $ids[0];
		}
		return null;
		
	}
	private function detalleCamas($unidad){
		return DB::select("SELECT
			s.id AS id_sala,
			s.nombre AS nombre_sala,
			s.descripcion AS descripcion_sala,
			cm.id AS id_cama_unq,
			cm.id_cama,
			cm.descripcion AS cama_descripcion,
			p.id AS id_paciente,
			p.nombre AS \"nombrePaciente\",
			p.apellido_paterno AS \"apellidoPaterno\",
			p.nombre || ' ' || p.apellido_paterno AS nombre_completo,
			p.sexo,
			c.id AS id_caso,
			uep.riesgo,
			uep.id_usuario,
			uev.cama as ocupado,
			th.fecha_alta, 
			th.fecha_ingreso_real, 
			th.fecha_liberacion,
			ur.cama as reservado,
			ur.renovada as renovada,
			ub.cama as bloqueado,
			ub.fecha as fecha_bloqueo,
			(
				SELECT EXISTS(
					SELECT * FROM lista_derivados
					WHERE caso = c.id
					AND fecha_egreso_lista IS NULL
				)
			)AS derivado,
			(
				SELECT EXISTS(
					SELECT * FROM lista_pabellon
					WHERE id_caso = c.id
					AND fecha_salida IS NULL
				)
			)AS en_pabellon,
			(
				SELECT EXISTS(
					SELECT * FROM infecciones
					WHERE caso = c.id
					AND fecha_termino IS NULL
				)
			)AS tiene_infeccion,
			(
				SELECT estadia_promedio
				FROM diagnosticos
				INNER JOIN cie_10 ON cie_10.id_cie_10 = diagnosticos.id_cie_10
				WHERE diagnosticos.caso = c.id 
				AND diagnosticos.id_cie_10 IS NOT NULL 
				ORDER BY fecha ASC 
				LIMIT 1

			)AS estadia_promedio

			FROM camas_temporales ctemp
			INNER JOIN casos c ON c.id = ctemp.caso
			INNER JOIN t_historial_ocupaciones th ON th.id = ctemp.id_historial_ocupaciones
			INNER JOIN camas cm ON cm.id = th.cama
			INNER JOIN salas s ON s.id = cm.sala
			INNER JOIN pacientes p ON p.id = c.paciente

			LEFT JOIN ultimos_estados_pacientes uep ON uep.caso = c.id
			LEFT JOIN ultimas_ocupaciones uev ON uev.cama = cm.id
			LEFT JOIN ultimas_reservas ur ON ur.cama = cm.id 
			LEFT JOIN ultimos_bloqueos_camas ub ON ub.cama = cm.id


			WHERE 
			ctemp.unidad = ?
			AND ctemp.visible IS TRUE",[$unidad]);
	}
	public function infoPaciente($caso){
		$info = DB::select("SELECT
			c.ficha_clinica,
			c.dau,
			p.rut || '-' || p.dv AS rut_completo,
			p.rn,
			CASE WHEN p.rut_madre IS NOT NULL THEN
				p.rut_madre || '-' || p.dv_madre
			ELSE
				'No especificado'
			END AS rut_madre,
			CASE WHEN p.extranjero THEN
				'Sí'
			ELSE
				'No'
			END AS extranjero,
			p.nombre,
			p.apellido_paterno,
			p.apellido_materno,
			p.sexo,
			CASE WHEN p.telefono IS NOT NULL AND p.telefono != '' THEN
				p.telefono
			ELSE
				'No posee'
			END AS telefono,
			p.nombre_social,
			TO_CHAR(p.fecha_nacimiento,'DD-MM-YYYY') AS fecha_nacimiento,
			CASE WHEN EXTRACT(YEAR FROM AGE(p.fecha_nacimiento)) = 0 THEN
					EXTRACT(MONTH FROM AGE(p.fecha_nacimiento)) || ' meses ' || EXTRACT(DAYS FROM AGE(p.fecha_nacimiento)) || ' días'
				ELSE
					EXTRACT(YEAR FROM AGE(p.fecha_nacimiento)) || ' años ' || EXTRACT(MONTH FROM AGE(p.fecha_nacimiento)) || ' meses'
			END AS edad,
			c.prevision,
			TO_CHAR(c.fecha_ingreso2,'DD-MM-YYYY HH24:MI') AS fecha_solicitud,
			TO_CHAR(uo.fecha_ingreso_real,'DD-MM-YYYY HH24:MI') AS fecha_hospitalizacion,
			CASE WHEN c.caso_social THEN
				'Sí'
			ELSE
				'No'
			END AS caso_social,
			(
				SELECT
				id_cie_10 || ' ' || diagnostico AS diagnostico
				FROM diagnosticos
				WHERE caso = c.id
				ORDER BY fecha DESC
				LIMIT 1
			)AS ultimo_diagnostico,
			(
				SELECT
				comentario
				FROM diagnosticos
				WHERE caso = c.id
				ORDER BY fecha DESC
				LIMIT 1
			)AS comentario_diagnostico,
			COALESCE(
				(
					SELECT
					STRING_AGG(area_funcional.nombre, ', ')AS nombre
					FROM casos
					LEFT JOIN complejidad_area_funcional ON casos.id_complejidad_area_funcional = complejidad_area_funcional.id_complejidad_area_funcional
					LEFT JOIN area_funcional ON complejidad_area_funcional.id_area_funcional = area_funcional.id_area_funcional
					WHERE casos.paciente = p.id
				),
				'No hay sugerencias'
			)AS sugerencia_area_funcional,
			COALESCE(
				(
					SELECT
					complejidad_servicio.nombre_servicio
					FROM t_evolucion_casos
					INNER JOIN complejidad_area_funcional ON complejidad_area_funcional.id_complejidad_area_funcional = t_evolucion_casos.id_complejidad_area_funcional
					INNER JOIN complejidad_servicio ON complejidad_servicio.id_complejidad = complejidad_area_funcional.id_complejidad
					WHERE 
					t_evolucion_casos.caso = c.id
					LIMIT 1
				),
				'Sin servicio'
			)AS servicio_a_cargo,
			COALESCE(
				(
					SELECT
					area_funcional.nombre
					FROM t_evolucion_casos
					INNER JOIN complejidad_area_funcional ON complejidad_area_funcional.id_complejidad_area_funcional = t_evolucion_casos.id_complejidad_area_funcional
					INNER JOIN area_funcional ON area_funcional.id_area_funcional = complejidad_area_funcional.id_area_funcional
					WHERE 
					t_evolucion_casos.caso = c.id
					LIMIT 1
				),
				'Sin servicio'
			)AS area_a_cargo,
			CASE WHEN c.requiere_aislamiento THEN
				'Sí requiere aislamiento'
			ELSE
				'No requiere aislamiento'
			END AS requiere_aislamiento,
			(
				SELECT
				riesgo
				FROM t_evolucion_casos
				WHERE 
				caso = c.id
				ORDER BY fecha DESC
				LIMIT 1
			)AS riesgo,
			(
				SELECT
				INITCAP(usuarios.nombres || ' ' || usuarios.apellido_paterno || ' ' || usuarios.apellido_materno)
				FROM usuarios
				WHERE usuarios.id = c.id_usuario
			)AS usuario_ingreso,
			(
				SELECT
				INITCAP(usuarios.nombres || ' ' || usuarios.apellido_paterno || ' ' || usuarios.apellido_materno)
				FROM usuarios
				WHERE usuarios.id = tho.id_usuario_ingresa
			)AS usuario_hospitaliza,
			(
				SELECT
				INITCAP(usuarios.nombres || ' ' || usuarios.apellido_paterno || ' ' || usuarios.apellido_materno)
				FROM usuarios
				INNER JOIN lista_transito ON lista_transito.id_usuario_ingresa = usuarios.id
				WHERE 
				lista_transito.caso = c.id
			)AS usuario_asigna,
			(
				SELECT 
				iaas.agente1
				FROM infecciones 
				INNER JOIN iaas ON iaas.id_infeccion = infecciones.id
				WHERE 
				caso = c.id 
				AND fecha_termino IS NULL
			)AS patogeno,
			(
				SELECT 
				pacientes_infeccion.aislamiento
				FROM infecciones 
				INNER JOIN iaas ON iaas.id_infeccion = infecciones.id
				INNER JOIN pacientes_infeccion ON pacientes_infeccion.id_infeccion = infecciones.id
				WHERE 
				caso = c.id 
				AND fecha_termino IS NULL
			)AS aislamiento,
			(
				SELECT 
				date_part('day', duracion_iaas)||' días'||' '|| date_part('hour', duracion_iaas)||' horas' as duracion_iaas 
				FROM (
					SELECT
					CASE WHEN fecha_termino IS NULL THEN 
						(now() - fecha) 
					ELSE 
						(fecha_termino - fecha) 
					END AS duracion_iaas
					FROM infecciones
					WHERE caso = c.id
				) tab
			)AS dias_aislamiento,
			tho.cama AS id_cama,
			cm.sala AS id_sala,
			c.id AS id_caso,
			cm.id_cama AS nombre_cama,
			s.nombre AS nombre_sala
			FROM casos c
			INNER JOIN pacientes p ON p.id = c.paciente
			INNER JOIN camas_temporales ctemp ON ctemp.caso = c.id
			INNER JOIN t_historial_ocupaciones tho ON tho.id = ctemp.id_historial_ocupaciones
			INNER JOIN camas cm ON cm.id = tho.cama
			INNER JOIN salas s ON s.id = cm.sala
			LEFT JOIN ultimas_ocupaciones uo ON uo.cama = cm.id
			WHERE
			c.id = ?
			AND ctemp.visible IS TRUE
		",[$caso]);
		if($info){
			return $info[0];
		}
		return null;
	}
	
}
