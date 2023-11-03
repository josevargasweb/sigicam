<?php namespace App\Models{

use DB;
use stdClass;

class Crosstab {

	protected $categorias = null;
	protected $set = null;
	protected $series = null;

	public static function crearCross(
		\Illuminate\Database\Query\Builder $categorias,
		\Illuminate\Database\Query\Builder $series
	){
		$r = new Crosstab();
		$r->setCategorias($categorias);
		$r->setSeries($series);
		/*$r->categoriasXseries();*/
		return $r;
	}

	public function setCategorias(\Illuminate\Database\Query\Builder $categorias){
		/* Categorias debe tener las columnas id_categoria, categoria */
		$this->categorias_sq = DB::raw("({$categorias->toSql()}) as categorias");
		$this->categorias = $categorias;
	}

	public function setSeries(\Illuminate\Database\Query\Builder $series){
		/*  Series debe tener las columnas id_serie, serie */
		$this->series_sq = DB::raw("({$series->toSql()}) as series");
		$this->series = $series;
	}

	public function setSet(\Illuminate\Database\Query\Builder $set){
		/* Set debe tener las columnas categoria, serie, y val.
		categoria se relaciona con id_categoria y serie se relaciona con id_serie. */
		$this->set_sq = DB::raw("({$set->toSql()}) as \"set\"");
		$this->set = $set;
	}

	public function categoriasXseries(){
	}

	protected function queryMain(){

		return DB::table( $this->set_sq )
		->rightJoin( DB::raw("
			(select * FROM ({$this->categorias->toSql()}) as categorias
			FULL OUTER JOIN ({$this->series->toSql()}) as series
			ON TRUE) AS cxs"), function($j){
				$j->on(DB::raw("cast(cxs.id_categoria AS varchar)"), "=", DB::raw("cast(set.categoria AS varchar)"))
				->on("cxs.id_serie", "=", "set.serie");
		} )
		->mergeBindings($this->set)
		->mergeBindings($this->categorias)
		->mergeBindings($this->series);
	}

	public function cross(\Illuminate\Database\Query\Builder $set){
		
		$this->setSet($set);
		$q1 = $this->queryMain()->orderByRaw("1")->get();
		$q2 = $this->categorias->orderBy("categoria", "asc")->get();

		$arr = array();
		$cats = array();
		$proto = new stdClass();
		foreach($q2 as $k => $categoria){
			$proto->{"$k"} = null;
			$cat = $categoria->categoria;
			$cats[$cat] = $k;
		}
		foreach($q1 as $k => $fila){
			if(!isset($arr["{$fila->serie}"])) $copy = clone $proto;
			else $copy = $arr["{$fila->serie}"];

			$copy->serie = $fila->serie;

			if (isset($fila->categoria)) {
				$key = $cats[$fila->categoria];
				$copy->{"{$key}"} = $fila->val;
			}
			$arr["{$fila->serie}"] = $copy;
		}
		$this->datos = new \Illuminate\Database\Eloquent\Collection;
		foreach($arr as $val){
			$this->datos[] = $val;
		}
		return $this;
	}
}
}
