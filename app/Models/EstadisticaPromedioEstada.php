<?php

/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 21/07/15
 * Time: 17:05
 */
class EstadisticaPromedioEstada extends EstadisticaCategorizada
{
    protected $bindings;
    private $query = "
with sub AS (
	select p.id as id_paciente,
	p.rut,
	p.dv,
	c.id as id_cama,
	c.id_cama as nombre_cama,
	s.id as id_sala,
	s.nombre as nombre_sala,
	ue.alias,
	ue.id as id_servicio,
	e.nombre as nombre_est,
	e.id as id_est,
	cs.id as id_caso,
	cs.fecha_ingreso,
	cs.fecha_termino,
	cs.diagnostico,
	h.fecha,
	extract(day from h.fecha) as dia_ingreso,
	extract(month from h.fecha) as mes_ingreso,
	extract(year from h.fecha) as anno_ingreso,
	coalesce(h.fecha_liberacion,  ? ) as fecha_liberacion, --fin;
	coalesce(h.fecha_liberacion,  ? ) - h.fecha as estadia --fin;
	FROM t_historial_ocupaciones h
	inner join camas c on c.id = h.cama
	inner join salas s on s.id = c.sala
	inner join unidades_en_establecimientos ue on ue.id = s.establecimiento
	inner join establecimientos e on e.id = ue.establecimiento
	inner join casos cs on cs.id = h.caso
	inner join pacientes p on p.id = cs.paciente
)
select anno_ingreso || '-' || mes_ingreso as categoria,
extract(days from extract(epoch from avg(estadia))/3600/24 * interval '1 days') as valor
from sub
WHERE(fecha >= ? AND fecha_liberacion < ?) -- inicio; fin;
";
    private $fin_query = "
GROUP BY anno_ingreso, mes_ingreso
-- ORDER BY avg(estadia)
";
    protected $mitad_query = "";

    public function setCategorias(){
        return new CategoriaMeses(12, $this->fecha);
    }

    public function set(){}

    public function queryMain(){
        return DB::table( DB::raw("({$this->query} {$this->mitad_query} {$this->fin_query}) as tabla") )
            ->setBindings($this->bindings);
    }

    public function __construct(\Carbon\Carbon $f1, \Carbon\Carbon $f2){
        $this->bindings = [$f2, $f2, $f1, $f2];
        parent::__construct($f1, $f2);
    }
}

class EstadisticaPromedioEstadaEstablecimiento extends EstadisticaPromedioEstada{
    public function __construct($establecimiento, \Carbon\Carbon $f1, \Carbon\Carbon $f2){
        $this->establecimiento = $establecimiento;
        parent::__construct($f1, $f2);
    }
    public function set(){
        $this->mitad_query = "AND e.id = ?";
        $this->bindings[] = $this->establecimiento;
    }
}

class EstadisticaPromedioEstadaServicio extends EstadisticaPromedioEstadaEstablecimiento{
    public function __construct($establecimiento, $unidad, $f1, $f2){
        $this->unidad = $unidad;
        parent::__construct($establecimiento, $f1, $f2);
    }

    public function set(){
        $this->mitad_query = "AND ue.id = ?";
        $this->bindings[] = $this->unidad;
    }
}