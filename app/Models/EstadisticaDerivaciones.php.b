<?php


class EstadisticaDerivacionesTotal extends Estadistica{

	protected function set(){
	}
	/*
	 * ESTAS SON LAS ACEPTADAS*/
	public function _completadas(){
		return $this->joinCerradasUsuario()
					->where("udc.motivo_cierre", "=", "aceptado");
	}

	public function _canceladas(){
		return $this->joinCerradasUsuario()
					->where("udc.motivo_cierre", "=", "cancelado");
	}
	public function _rechazadas(){
		return $this->joinCerradasUsuario()
					->where("udc.motivo_cierre", "=", "rechazado");
	}
	public function _espera(){
		return $this->joinAbiertasUsuario();
	}
	public function _recibidos(){
		return $this->joinCerradasDestino()
					->where("udc.motivo_cierre", "=", "aceptado");
	}

	public function _enviadas_aceptadas(){
		return $this->joinCerradasUsuario()
			->where("udc.motivo_cierre", "aceptado");
	}
	public function _enviadas_aceptadas_pendiente(){
		return $this->joinCerradasUsuario()
			->where("udc.motivo_cierre", "aceptado, pendiente de cama");
	}
	public function _enviadas_rechazadas(){
		return $this->joinCerradasUsuario()
			->where("udc.motivo_cierre", "rechazado");
	}
	public function _enviadas_canceladas(){
		return $this->joinCerradasUsuario()
			->where("udc.motivo_cierre", "cancelado");
	}
	public function _enviadas_en_espera(){
		return $this->joinAbiertasUsuario();
	}

	public function _recibidas_aceptadas(){
		return $this->joinCerradasDestino()
			->where("udc.motivo_cierre", "aceptado");
	}
	public function _recibidas_aceptadas_pendiente(){
		return $this->joinCerradasDestino()
			->where("udc.motivo_cierre", "aceptado, pendiente de cama");
	}
	public function _recibidas_rechazadas(){
		return $this->joinCerradasDestino()
			->where("udc.motivo_cierre", "rechazado");
	}
	public function _recibidas_canceladas(){
		return $this->joinCerradasDestino()
			->where("udc.motivo_cierre", "cancelado");
	}
	public function _recibidas_en_espera(){
		return $this->joinAbiertasDestino();
	}


	public function enviadas_aceptadas(){
		return (int) $this->_enviadas_aceptadas()->count();
	}

	public function enviadas_aceptadas_pendiente(){
		return (int) $this->_enviadas_aceptadas_pendiente()->count();
	}

	public function enviadas_rechazadas(){
		return (int) $this->_enviadas_rechazadas()->count();
	}

	public function enviadas_canceladas(){
		return (int) $this->_enviadas_canceladas()->count();
	}

	public function enviadas_en_espera(){
		return (int) $this->_enviadas_en_espera()->count();
	}

	public function recibidas_aceptadas(){
		return (int) $this->_recibidas_aceptadas()->count();
	}

	public function recibidas_aceptadas_pendiente(){
		return (int) $this->_recibidas_aceptadas_pendiente()->count();
	}

	public function recibidas_rechazadas(){
		return (int) $this->_recibidas_rechazadas()->count();
	}

	public function recibidas_canceladas(){
		return (int) $this->_recibidas_canceladas()->count();
	}

	public function recibidas_en_espera(){
		return (int) $this->_recibidas_en_espera()->count();
	}



	public function completadas(){
		return $this->_completadas()->count() * 1;
	}

	public function canceladas(){
		return $this->_canceladas()->count() * 1;
	}

	public function rechazadas(){
		return $this->_rechazadas()->count() * 1;
	}

	public function espera(){
		return $this->_espera()->count() * 1;
	}

	public function recibidos(){
		return $this->_recibidos()->count() * 1;
	}

	public function pacientesRecibidos(){
		return $this->recibidos();
	}

	public function _promedioAceptacionEnviados(){
		return $this->_completadas()
		->select(
			DB::raw("date_trunc('second', udc.fecha_cierre) as fecha_cierre"),
			DB::raw("date_trunc('second', udc.fecha) as fecha")
		);
	}

	public function _promedioAceptacionRecibidos(){
		return $this->_recibidos()
		->select(
			DB::raw("date_trunc('second', udc.fecha_cierre) as fecha_cierre"),
			DB::raw("date_trunc('second', udc.fecha) as fecha")
		);
	}

	public function promedioAceptacionRecibidos(){
		return  $this->formatearFechas($this->_promedioAceptacionRecibidos()->first());
		
	}

	public function promedioAceptacionEnviados(){
		return $this->formatearFechas($this->_promedioAceptacionEnviados()->first());
		
	}

	protected function formatearFechas($fechas){
		if(is_null($fechas)) {
			return '0 minutos';
		}
		return Funciones::intervalo($fechas->fecha_cierre, $fechas->fecha);
	}

	protected function joinCerradasUsuario(){
		$q = $this->derivaciones_cerradas();
		return DB::table(DB::raw("({$q->toSql()}) AS udc"))
		->join("usuarios as us", "us.id", "=", "udc.usuario")
		->mergeBindings($q)
		;
	}

	protected function joinAbiertasUsuario(){
		$q = $this->derivaciones_abiertas();
		return DB::table(DB::raw("({$q->toSql()}) AS udc"))
		->join("usuarios as us", "us.id", "=", "udc.usuario")
		->mergeBindings($q)
		;
	}

	protected function joinCerradasDestino(){
		$q = $this->derivaciones_cerradas();
		return DB::table(DB::raw("({$q->toSql()}) AS udc"))
		->join("unidades_en_establecimientos AS uee", "uee.id", "=", "udc.destino")
		->mergeBindings($q)
		;
	}

	protected function joinAbiertasDestino(){
		$q = $this->derivaciones_abiertas();
		return DB::table(DB::raw("({$q->toSql()}) AS udc"))
			->join("unidades_en_establecimientos AS uee", "uee.id", "=", "udc.destino")
			->mergeBindings($q)
			;
	}

	public function joinCasos($q){
		return $q->join("casos as cs", "cs.id", "=", "udc.caso")
		->leftjoin("ultimas_evoluciones_pacientes as uev", "cs.id", "=", "uev.caso")
		;
	}
}

class EstadisticaDerivaciones extends EstadisticaDerivacionesTotal{
	public function __construct($establecimiento, $fecha = null){
		$this->establecimiento = $establecimiento;
		parent::__construct($fecha);
	}

	public function _completadas(){
		return parent::_completadas()
		->where("us.establecimiento", "=", $this->establecimiento);
	}

	public function _canceladas(){
		return parent::_canceladas()
		->where("us.establecimiento", "=", $this->establecimiento);
	}

	public function _rechazadas(){
		return parent::_rechazadas()
		->where("us.establecimiento", "=", $this->establecimiento);
	}

	public function _espera(){
		return parent::_espera()
		->where("us.establecimiento", "=", $this->establecimiento);
	}

	public function _recibidos(){
		return parent::_recibidos()
		->where("uee.establecimiento", "=", $this->establecimiento);
	}

	public function promedioAceptacionRecibidos(){
		return $this->formatearFechas(parent::_promedioAceptacionRecibidos()
		->where("uee.establecimiento", "=", $this->establecimiento)
		->first());
	}

	public function promedioAceptacionEnviados(){
		return $this->formatearFechas(parent::_promedioAceptacionEnviados()
		->where("us.establecimiento", "=", $this->establecimiento)
		->first());
	}

	public function _enviadas_aceptadas(){
		return parent::_enviadas_aceptadas()->where("us.establecimiento", $this->establecimiento);
	}
	public function _enviadas_aceptadas_pendiente(){
		return parent::_enviadas_aceptadas_pendiente()->where("us.establecimiento", $this->establecimiento);
	}
	public function _enviadas_rechazadas(){
		return parent::_enviadas_rechazadas()->where("us.establecimiento", $this->establecimiento);
	}
	public function _enviadas_canceladas(){
		return parent::_enviadas_canceladas()->where("us.establecimiento", $this->establecimiento);
	}
	public function _enviadas_en_espera(){
		return parent::_enviadas_en_espera()->where("us.establecimiento", $this->establecimiento);
	}
	public function _recibidas_aceptadas(){
		return parent::_recibidas_aceptadas()->where("uee.establecimiento", $this->establecimiento);
	}
	public function _recibidas_aceptadas_pendiente(){
		return parent::_recibidas_aceptadas_pendiente()->where("uee.establecimiento", $this->establecimiento);
	}
	public function _recibidas_rechazadas(){
		return parent::_recibidas_rechazadas()->where("uee.establecimiento", $this->establecimiento);
	}
	public function _recibidas_canceladas(){
		return parent::_recibidas_canceladas()->where("uee.establecimiento", $this->establecimiento);
	}
	public function _recibidas_en_espera(){
		return parent::_recibidas_en_espera()->where("uee.establecimiento", $this->establecimiento);
	}
}

abstract class SetEspecial{
	/**
	*	@return \Illuminate\Database\Query\Builder 
	*/
	public abstract function table();
	public function get(){
		return $this->table()
		->select(
			"{$this->col_id} AS {$this->id}",
			"{$this->col_nombre} AS {$this->tipo}"
		);
	}
	public static function categorias(){
		$class = get_called_class();
		$args = func_get_args();
		$i = new ReflectionClass($class);
		$r = $i->newInstanceArgs($args);
		$r->id = "id_categoria";
		$r->tipo = "categoria";
		return $r;
	}
	public static function series(){
		$class = get_called_class();
		$args = func_get_args();
		$i = new ReflectionClass($class);
		$r = $i->newInstanceArgs($args);
		$r->id = "id_serie";
		$r->tipo = "serie";
		return $r;
	}
	public function __construct(){
	
	}
}

class SetRiesgos extends SetEspecial{
	public function table(){
		$this->col_nombre = "categoria";
		$this->col_id = "categoria";
		return DB::table(
    	DB::raw("(
    		SELECT *
    		FROM UNNEST('{A1, A2, A3, B1, B2, B3, C1, C2, C3, D1, D2, D3}'::riesgo[])
    		as categoria
    		)as categorias ")
    	);
	}
}

class SetPersonalizado extends SetEspecial{
	public function __construct(array $items){
		$this->items = implode(',', $items);
	}
	public function table(){
		$this->col_nombre = "serie";
		$this->col_id = "serie";
		$bind = '{'.$this->items.'}';
		return DB::table(DB::raw("(
			SELECT * FROM UNNEST('{$bind}'::varchar[]) as serie
		) as series"))
		//->addBinding('{'.$this->items.'}')
		;
	}
}

class SetMeses extends SetEspecial{
	public function __construct($cuantos){
		$this->cuantos = $cuantos;
	}
	public function table(){
		$this->col_nombre = "mes";
		$this->col_id = "mes";
		return DB::table(DB::raw("
                (SELECT
                (extract(month from m)::text ||'-'||extract(year FROM m)::text) as mes
                FROM
                (select generate_series(
                    now() - '{$this->cuantos} month'::interval,
                    now(),
                    '1 month'::interval
                )::date as m) as b ORDER BY m DESC) as meses
		"))
		//->addBinding("{$this->cuantos} month")
		;
	}
}

class SetTiposCama extends SetEspecial{
	public function table(){
		$this->col_nombre = "nombre";
		$this->col_id = "id";
		return DB::table("tipos_cama")->orderBy("nombre");
	}
}

class SetEstablecimientos extends SetEspecial{
	public function table(){
		$this->col_nombre = "nombre";
		$this->col_id = "id";
		return DB::table("establecimientos")->orderBy("nombre");
	}
}

class SetUnidades extends SetEspecial{
	public function __construct($estab){
		$this->establecimiento = $estab;
	}
	public function table(){
		$this->col_nombre = "alias";
		$this->col_id = "id";
		return DB::table("unidades_en_establecimientos")
		->where("establecimiento", "=", $this->establecimiento);
	}
}

abstract class EstadisticaCat extends Estadistica{
	protected abstract function queryMain();
	public function get(){
    	$q1 = $this->queryMain();
    	return Crosstab::crearCross($this->categorias, $this->series)->cross($q1)->datos;
    }
    public function setSeries(SetEspecial $series){
    	$this->series = $series->get();
    	return $this;
    }

    public function setCategorias(SetEspecial $categorias){
    	$this->categorias = $categorias->get();
    	return $this;
    }

    public function getCategorias(){
    	return $this->categorias->get();
    }
}

abstract class EstadisticaCategorizadaDerivaciones extends EstadisticaCat{ //extends EstadisticaCategorizada{

	public function _join($q){
		//$q = $this->derivaciones();
		return DB::table(DB::raw("({$q->toSql()}) as d"))
		->join("usuarios as us", "us.id", "=", "d.usuario")
		->join("establecimientos as est_solicitante", "est_solicitante.id", "=", "us.establecimiento")
		->rightJoin("unidades_en_establecimientos as ue", "ue.id", "=", "d.destino")
		->join("establecimientos as est_destino", "est_destino.id", "=", "ue.establecimiento")
		->mergeBindings($q)
		;
	}

	public function join_abiertas(){
		return $this->_join($this->derivaciones_abiertas());
	}

	public function join_cerradas(){
		return $this->_join($this->derivaciones_cerradas());
	}
}

abstract class DerivacionesPorEstablecimiento extends EstadisticaCategorizadaDerivaciones{
	/* @var $estadisticas EstadisticaDerivacionesTotal*/
	protected $estadisticas;
	protected function set()
	{
		$this->tipo = 'integer';
		$this->setCategorias(SetEstablecimientos::categorias());
		$this->estadisticas = new EstadisticaDerivacionesTotal(
			DateTime::createFromFormat('Y-m-d H:i:s', $this->fecha)->format('d-m-Y')
		);
	}

	public function _queryMain($q){
		$this->setSeries(SetPersonalizado::series([$this->serie]));
		return $this->_join($q)
			->select(
				DB::raw("'{$this->serie}' AS serie"),
				DB::raw("COUNT(d.*) as val"),
				"est_destino.id AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}
}

class DerivacionesAceptadasEstablecimiento extends DerivacionesPorEstablecimiento{
	protected $serie = "aceptadas";
	protected function queryMain(){
		return $this->_queryMain($this->estadisticas->_recibidas_aceptadas());
	}

}

class DerivacionesRechazadasEstablecimiento extends DerivacionesPorEstablecimiento{
	protected $serie = "rechazadas";
	public function queryMain(){
		return $this->_queryMain($this->estadisticas->_recibidas_rechazadas());
	}
}

class DerivacionesDemoraEstablecimiento extends DerivacionesPorEstablecimiento{
	protected $serie = "promedio de espera para aceptación";
	public function queryMain(){
		$this->setSeries(SetPersonalizado::series([$this->serie]));
		return $this->_join($this->estadisticas->_recibidas_aceptadas()->select(
			DB::raw("*, (extract(epoch FROM (fecha_cierre - fecha)/60)) as ep")
		))
			->select(
				DB::raw("'{$this->serie}' AS serie"),
				DB::raw("AVG(d.ep) as val"),
				"est_destino.id AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}
}

class RankingDerivacionesCategoriasTotal extends EstadisticaCategorizadaDerivaciones{
	/* @var $estadisticas EstadisticaDerivaciones */
	protected $estadisticas;
	protected function set(){

    	$this->tipo = 'integer';

    	$this
    	->setSeries(SetPersonalizado::series([
    		'aceptadas', 'aceptadas con cama pendiente', 'canceladas', 'rechazadas', 'en espera'
    	]))
    	->setCategorias(SetRiesgos::categorias());
    	$this->estadisticas = $this->setEstadisticas(
    		DateTime::createFromFormat('Y-m-d H:i:s',$this->fecha)->format('d-m-Y')
		);
    }

    public function setEstadisticas($date){
    	return new EstadisticaDerivacionesTotal($date);
    }

	public function joinRiesgos(){		
		return $this->join()
		->leftJoin("casos as cs", "cs.id", "=", "d.caso")
		->leftjoin("ultimas_evoluciones_pacientes as uev", "cs.id", "=", "uev.caso");
	}


	public function query1(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_enviadas_aceptadas_pendiente());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'aceptadas con cama pendiente' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	public function query3(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_enviadas_aceptadas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
		->select(
			DB::raw("'aceptadas' AS serie"),
			DB::raw("COUNT(sq.*) as val"),
			"sq.riesgo AS categoria"
		)
		->groupBy("categoria")
		->groupBy("serie");
	}

	public function query4(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_enviadas_canceladas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
		->select(
			DB::raw("'canceladas' AS serie"),
			DB::raw("COUNT(sq.*) as val"),
			"sq.riesgo AS categoria"
		)
		->groupBy("categoria")
		->groupBy("serie");
	}

	public function query5(){
		$q = $this->estadisticas->joinCasos($this->estadisticas->_enviadas_rechazadas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
		->select(
			DB::raw("'rechazadas' AS serie"),
			DB::raw("COUNT(sq.*) as val"),
			"sq.riesgo AS categoria"
		)
		->groupBy("categoria")
		->groupBy("serie");
	}

	public function query6(){
		$q = $this->estadisticas->joinCasos($this->estadisticas->_enviadas_en_espera());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
		->select(
			DB::raw("'en espera' AS serie"),
			DB::raw("COUNT(sq.*) as val"),
			"sq.riesgo AS categoria"
		)
		->groupBy("categoria")
		->groupBy("serie");
	}

	protected function queryMain(){
		return $this->query1()
		//->union($this->query2())
		->union($this->query3())
		->union($this->query4())
		->union($this->query5())
		->union($this->query6());
	}
}

class RankingDerivacionesCategorias extends RankingDerivacionesCategoriasTotal{

	public function __construct($establecimiento, $fecha = null){
		$this->establecimiento = $establecimiento;
		parent::__construct($fecha);
	}

	public function setEstadisticas($date){
    	return new EstadisticaDerivaciones($this->establecimiento, $date);
	}

}

class RankingDerivacionesCategoriasUnidad extends RankingDerivacionesCategorias{
	
	public function __construct($establecimiento, $unidad, $fecha = null){
		$this->unidad = $unidad;
		parent::__construct($establecimiento, $fecha);
	}
}

class DerivacionesMensualesTotal extends EstadisticaCategorizadaDerivaciones{

	protected $tipo = 'float';

	public function joinUnidades(){
		return $this->_join($this->derivaciones())
		->whereNotNull('fecha_cierre')
		->where('motivo_cierre', "=", "aceptado");
	}
	public function select1(){
		return $this->joinUnidades()
		->select(
			DB::raw("'enviadas' AS serie"),
			$this->extraer_mes,
			DB::raw("count(d.id) as val")
		);
	}
	public function select2(){
		return $this->joinUnidades()
		->select(
			DB::raw("'recibidas' AS serie"),
			$this->extraer_mes,
			DB::raw("count(d.id) as val")
		);
	}
	
	protected function queryMain(){
		return $this->query1();
	}
	public function query1(){
		return $this->select1()
		->groupBy("categoria")
		->groupBy("serie");
	}
	public function query2(){
		return $this->select2()
		->groupBy("categoria")
		->groupBy("serie");
	}
	
	protected function set(){
		$this->extraer_mes = DB::raw("(extract(month from fecha)::text ||'-'||extract(year FROM fecha)::text) AS categoria");
		$this->setSeries(SetPersonalizado::series(['enviadas',]));
		$this->setCategorias(SetMeses::categorias(12));
	}

}


class DerivacionesMensuales extends DerivacionesMensualesTotal{

	public function __construct($establecimiento, $fecha = null){
		$this->establecimiento = $establecimiento;
		parent::__construct($fecha);
	}

	public function select1(){
		return parent::select1()
		->where("est_solicitante.id", "=", $this->establecimiento);
	}
	public function select2(){
		return parent::select2()
		->where("est_destino.id", "=", $this->establecimiento);
	}
	protected function queryMain(){
		return $this->query1()->union($this->query2());
	}
	protected function set(){
		parent::set();
		$this->setSeries(SetPersonalizado::series(['enviadas', 'recibidas']));
	}
}

/* HACK HORRIBLE que debe ser arreglado */

class RankingDerivacionesRecibidasCategoriasTotal extends EstadisticaCategorizadaDerivaciones{
	/* @var $estadisticas EstadisticaDerivaciones */
	protected $estadisticas;
	protected function set(){

		$this->tipo = 'integer';

		$this
			->setSeries(SetPersonalizado::series([
				'aceptadas', 'aceptadas con cama pendiente', 'canceladas', 'rechazadas', 'en espera'
			]))
			->setCategorias(SetRiesgos::categorias());
		$this->estadisticas = $this->setEstadisticas(
			DateTime::createFromFormat('Y-m-d H:i:s',$this->fecha)->format('d-m-Y')
		);
	}

	public function setEstadisticas($date){
		return new EstadisticaDerivacionesTotal($date);
	}

	public function joinRiesgos(){
		return $this->join()
			->leftJoin("casos as cs", "cs.id", "=", "d.caso")
			->leftjoin("ultimas_evoluciones_pacientes as uev", "cs.id", "=", "uev.caso");
	}


	public function query1(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_recibidas_aceptadas_pendiente());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'aceptadas con cama pendiente' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	public function query3(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_recibidas_aceptadas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'aceptadas' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	public function query4(){
		$q = $this->estadisticas->joinCasos(
			$this->estadisticas->_recibidas_canceladas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'canceladas' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	public function query5(){
		$q = $this->estadisticas->joinCasos($this->estadisticas->_recibidas_rechazadas());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'rechazadas' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	public function query6(){
		$q = $this->estadisticas->joinCasos($this->estadisticas->_recibidas_en_espera());
		return DB::table( DB::raw("({$q->toSql()}) as sq") )->mergeBindings($q)
			->select(
				DB::raw("'en espera' AS serie"),
				DB::raw("COUNT(sq.*) as val"),
				"sq.riesgo AS categoria"
			)
			->groupBy("categoria")
			->groupBy("serie");
	}

	protected function queryMain(){
		return $this->query1()
			//->union($this->query2())
			->union($this->query3())
			->union($this->query4())
			->union($this->query5())
			->union($this->query6());
	}
}

class RankingDerivacionesRecibidasCategorias extends RankingDerivacionesRecibidasCategoriasTotal{

	public function __construct($establecimiento, $fecha = null){
		$this->establecimiento = $establecimiento;
		parent::__construct($fecha);
	}

	public function setEstadisticas($date){
		return new EstadisticaDerivaciones($this->establecimiento, $date);
	}

}

class RankingDerivacionesRecibidasCategoriasUnidad extends RankingDerivacionesRecibidasCategorias{

	public function __construct($establecimiento, $unidad, $fecha = null){
		$this->unidad = $unidad;
		parent::__construct($establecimiento, $fecha);
	}
}