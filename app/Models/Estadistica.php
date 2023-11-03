<?php namespace App\Models{

//include(app_path() . '/Models/EstadisticaDerivaciones.php');

use DB;
use stdClass;

abstract class Categorizacion{
	abstract public function categorias();
	abstract public function subquery();
	abstract public function col();
	abstract public function numeroCategorias();
}

class CategoriaRiesgos extends Categorizacion{
	
	public function __construct(){
		$this->sql = "UNNEST('{No categorizados, A1, A2, A3, B1, B2, B3, C1, C2, C3, D1, D2, D3}'::varchar[]) AS categoria";
	}
	public function riesgos(){
		return DB::table(DB::raw($this->sql));
	}
	public function categorias(){
		return $this->riesgos();
	}
	public function subquery(){
		return DB::raw("(SELECT * FROM {$this->sql}) AS riesgos");
	}
	public function col(){
		return "riesgos.categoria";
	}
	public function numeroCategorias(){
		return 14;
	}

}

class CategoriaEstablecimientos extends Categorizacion{
	public function subquery(){
		return DB::raw("(SELECT * FROM establecimientos order by id) AS establecimientos");
	}
	public function categorias(){
		return DB::table("establecimientos")
		->select("nombre as categoria")
		->orderBy("categoria", "asc");
	}
	public function col(){
		return "establecimientos.categoria";
	}
	public function numeroCategorias(){
		return DB::table("establecimientos")
		->count() - 1;
	}

}

class CategoriaUnidades extends Categorizacion{
	public function __construct($idEstablecimiento){
		$this->establecimiento = $idEstablecimiento;
	}
	public function subquery(){
		return DB::raw("(SELECT distinct alias AS categoria from unidades_en_establecimientos ue
		inner join salas s on s.establecimiento = ue.id
		inner join camas cm on cm.sala = s.id
			WHERE ue.establecimiento = {$this->establecimiento}
			AND s.id IS NOT NULL
			AND cm.id IS NOT NULL
			order by categoria asc) AS unidades"
		);
	}
	public function categorias(){
		return DB::table("unidades_en_establecimientos AS unidades")
		->join("salas AS s", "s.establecimiento", "=", "unidades.id")
		->join("camas AS cm", "cm.sala", "=", "s.id")
		->select("alias as categoria")->distinct()
		->whereRaw("unidades.establecimiento = {$this->establecimiento}")
		->whereRaw("s.id IS NOT NULL")
		->whereRaw("cm.id IS NOT NULL")
		->orderBy("categoria", "asc");
	}

	public function col(){
		return "unidades.categoria";
	}

	public function numeroCategorias(){
		/*return DB::table("unidades_en_establecimientos")
		->whereRaw("establecimiento = {$this->establecimiento}")*/
		$this->categorias()
		->count() - 1;
	}
}

class CategoriaUnidadesAcotadas extends Categorizacion{
	public function __construct(array $ids){
		$this->ids = $ids;
	}

	public function categorias(){
		$q = DB::table("unidades as u")
		->join("unidades_en_establecimientos as ue", "ue.unidad", "=", "u.id")
		//->join("derivaciones as d", "ue.id", "=", "d.destino")
		->select("u.nombre as categoria");

		foreach($this->ids as $id){
			$q->orWhereRaw("ue.establecimiento = {$id}");
		}
		return $q;
	}

	public function subquery(){
		return DB::raw("({$this->categorias()->toSql()}) AS unidades");
	}

	public function col(){
		return "unidades.categoria";
	}

	public function numeroCategorias(){
		return $this->categorias()->count() - 1;
	}
}

class CategoriaMeses extends Categorizacion{
    private $mes = null;

	public function __construct($cuantos = 12, \Carbon\Carbon $mes = null){
		$this->cuantos = $cuantos;
        if($mes === null){
            $this->mes = \Carbon\Carbon::now();
        }
        else{
            $this->mes = $mes;
        }
	}

	public function generarMeses(){
		return DB::table(DB::raw("
                (select generate_series(
                    TIMESTAMP '{$this->mes}' - '{$this->cuantos} month'::interval,
                    TIMESTAMP '{$this->mes}',
                    '1 month'::interval
                )::date val) as b 
		"))
		->select(DB::raw("{$this->selectMeses('b.val')} as categoria"))
		->orderBy("b.val", "asc");
	}

	public function numeroCategorias(){
		return $this->cuantos;
	}

	public function selectMeses($col){
		return "
			(extract(month FROM {$col})::text
			||'-'||
			extract(year FROM {$col})::text)
		";
	}
	public function categorias(){
		return $this->generarMeses();
	}

	public function subquery(){
		$meses = $this->generarMeses();
		return DB::raw("({$meses->toSql()}) AS meses");
	}

	public function col(){
		return "meses.categoria";
	}
}

 abstract class Estadistica{
     protected $fecha = null;
     protected $fecha_inicio = null;
	 protected $tabla_base = null;
	 protected $tipo = 'integer';

	public function __construct(\Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->fecha_inicio = $fecha_inicio;
		$this->fecha = $fecha;
		$this->set();
	}

	 protected function aplanar(&$ests, $campo){
		 foreach($ests as &$est){
			 $est->{$campo} = new \Illuminate\Database\Eloquent\Collection();
			 foreach($est->unidades as $unidad){
				 foreach($unidad->{$campo} as $cama){
					 $est->{$campo}[] = $cama;
				 }
			 }
		 }
	 }

     public function derivaciones(){
         /* Hay que seleccionar las que !(esten cerradas antes de la fecha inicio y las que estén abiertas
         después de la fecha termino )*/
         return DB::table( DB::raw(
             "(
            SELECT
            id,
            caso,
            usuario,
            fecha,
            (CASE WHEN fecha_cierre < ? THEN fecha_cierre ELSE null END) as fecha_cierre,
            motivo_cierre,
            comentario,
            destino,
            row_number() OVER (PARTITION BY caso ORDER BY fecha DESC) as rk
    		FROM derivaciones WHERE fecha <= ? ) AS d"
         ))
             ->addBinding($this->fecha)
             ->addBinding($this->fecha);
     }

     public function derivaciones_cerradas(){
         return $this->derivaciones()
             ->whereNotNull("fecha_cierre")
             ->where("fecha", "<=", $this->fecha)
             ->where("fecha_cierre", ">=", $this->fecha_inicio);
     }

     public function derivaciones_abiertas(){
         return $this->derivaciones()
             ->where("fecha_cierre", null)
             ->where("fecha", "<=", $this->fecha);
     }


     public function selectRiesgo(){
		return DB::raw("(CASE WHEN uev.riesgo IS NULL THEN cs.riesgo ELSE uev.riesgo END) AS cat_riesgo");
	}
	public function selectUnidad(){
		return DB::raw("(CASE WHEN u.nombre IS NULL THEN uu.nombre ELSE u.nombre END) AS nombre_unidad");
	}

	public function ultimasReservas(){
        return DB::table(DB::raw("
                (SELECT *, date_trunc('seconds'::text, 
                fecha + tiempo) AS queda, row_number() OVER (partition by cama order by fecha DESC) as rk
                from t_reservas WHERE fecha + tiempo >= ?) AS h"))
        ->addBinding($this->fecha_inicio);
    }

    public function ultimasOcupaciones(){
        return DB::table(DB::raw("
            (SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
                FROM t_historial_ocupaciones
                WHERE fecha <= ? AND (fecha_liberacion < ? OR fecha_liberacion IS NULL)) AS h"))
        ->addBinding($this->fecha)
        ->addBinding($this->fecha);
    }

    public function camasVigentes(){
        return DB::table( DB::raw(
            "(SELECT * FROM camas c
                LEFT JOIN (select cama,fecha FROM historial_eliminacion_camas WHERE fecha <= ?) AS el
                ON el.cama = c.id
                WHERE el.fecha IS NULL ) AS cm"
        ))->addBinding($this->fecha);
    }

    public function historialBloqueoCamas(){
        return DB::table( DB::raw(
            "(SELECT *, row_number() OVER (PARTITION BY cama ORDER BY fecha DESC) as rk
                FROM t_historial_bloqueo_camas WHERE fecha <= ? AND (fecha_habilitacion < ? OR fecha_habilitacion IS NULL )) AS h"))
        ->addBinding($this->fecha)
        ->addBinding($this->fecha);
    }

    public function camasHabilitadas(){
        $hb = $this->historialBloqueoCamas()->where('rk', '=', 1)->where("fecha_habilitacion", "=", null);
        return $this->camasVigentes()
        ->leftJoin( DB::raw("({$hb->toSql()}) as h"), "h.cama", "=", "cm.id" )
        ->mergeBindings($hb)
        ->where("h.id", "=", null);
    }

	 /* TODAS LAS DERIVACIONES */


    public function riesgosXunidades(){
    	return DB::table( DB::raw(
    		"(SELECT riesgo.riesgo, ue.id as unidad, ue.alias, ue.establecimiento, est.nombre
    		FROM UNNEST('{A1, A2, A3, B1, B2, B3, C1, C2, C3, D1, D2, D3}'::riesgo[])
    		AS riesgo
    		FULL OUTER JOIN unidades_en_establecimientos ue ON TRUE
    		LEFT JOIN establecimientos est ON est.id = ue.establecimiento) AS rxu"
    	));
    }

    public function getTipo(){
    	return $this->tipo;
    }

	protected abstract function set();
}

abstract class EstadisticaCategorizada extends Estadistica{

	public function __construct(\Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
		parent::__construct($fecha_inicio, $fecha);
		$this->checkCategoria( $this->setCategorias() );
		$this->cross();
	}
	public function getCategorias(){
		return $this->categorias->categorias()->orderBy("categoria", "asc")->get();
	}

	protected function generarColumnas(){
		if(!isset($this->tipo)){
			throw new Exception("Se debe definir el atributo \$tipo con un string como 'INT', 'INTERVAL', 'FLOAT', 'VARCHAR', etc.");
		}
		$cols = "";
		$cuantos = $this->categorias->numeroCategorias();
		foreach(range( 0, $cuantos ) as $val){
			$cols.= " \"{$val}\" {$this->tipo} ";
			if($val != $cuantos) $cols.= ",";
		}
		return $cols;
	}

	protected function cross(){
		$q1 = $this->queryMain()->orderBy("categoria", "asc")->get();//->orderByRaw("1");
		$q2 = $this->categorias->categorias()->orderBy("categoria", "asc")->get();

		$arr = array();
		$cats = array();
		$proto = new stdClass();
		foreach($q2 as $k => $categoria){
			$proto->{"$k"} = null;
			$cat = $categoria->categoria;
			$cats[$cat] = $k;
		}
		//var_dump($cats);
		foreach($q1 as $k => $fila){
			//var_dump($fila);
			if(!isset($arr["{$fila->serie}"])) $copy = clone $proto;
			else $copy = $arr["{$fila->serie}"];

			$copy->serie = $fila->serie;

			if (isset($fila->categoria) && isset($cats[$fila->categoria])) {
				$key = $cats[$fila->categoria];
				$copy->{"{$key}"} = $fila->val;
			} 
			$arr["{$fila->serie}"] = $copy;
		}
		$this->datos = new \Illuminate\Database\Eloquent\Collection;
		foreach($arr as $val){
			$this->datos[] = $val;
		}
	}
	protected abstract function queryMain();

    protected function checkCategoria(Categorizacion $cat){
    	$this->categorias = $cat;
    }

    protected abstract function setCategorias();

    public function get(){
    	return $this->datos;
    }
}


}
