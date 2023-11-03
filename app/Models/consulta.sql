SELECT
--ev.id as evid, hcs.id as hcsid, ho.id as hoid, cm.id as cmid, sl.id as cmid, p.id as pid, cs.id as csid,
cs.id, cs.fecha_ingreso, cs.fecha_termino, cm.id_cama, sl.id_sala, p.rut, p.dv, p.sexo, p.fecha_nacimiento, ev.riesgo,
ho.evento
FROM pacientes p
INNER JOIN casos cs ON cs.paciente = p.id
INNER JOIN ultimos_eventos_camas ho ON ho.caso = cs.id
INNER JOIN camas cm ON ho.cama = cm.id
INNER JOIN ultimas_salas_camas hcs ON hcs.cama = cm.id
INNER JOIN salas sl ON sl.id = hcs.sala
INNER JOIN ultimo_estado_paciente ev ON ev.caso = cs.id
WHERE ho.evento <> 'libre'
;

SELECT p.rut, p.dv, oc.evento, us.cama, us.sala
