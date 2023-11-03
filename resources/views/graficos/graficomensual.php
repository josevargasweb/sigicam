<?php

abstract class GraficoConDatos {

	public function __construct(Estadistica $est){
		$estadistica = $est;
		$this->tipo = strtoupper($estadistica->getTipo());
		$categorias = $estadistica->getCategorias();
		$this->categorias = array();
		$this->series = array();

		$this->grafico = $this->setGrafico();
		foreach($categorias as $categoria){
			$this->categorias[] = $categoria->categoria;
		}
		$this->grafico->setCategorias($this->categorias);

		foreach($estadistica->get() as $this->unidad){
			$nombre_serie = $this->unidad->serie;
			$arr = array();
			foreach(range(0,count($categorias) - 1) as $this->val){
				$arr[] = $this->setVal();				
			}
			$this->grafico->agregarSerie($arr, $nombre_serie);
		}
		$this->setDetalles();
	}

	public function get(){
		return $this->grafico->renderJson();
	}

	public function setTitulo($s){
		$this->grafico->setTitulo($s);
		return $this;
	}

	public function setTituloY($s){
		$this->grafico->setTituloY($s);
		return $this;
	}

	public function setYMin($d){
		$this->grafico->setYMin($d);
		return $this;
	}

	public function setVal(){
		$v = $this->unidad->{"$this->val"};
		if ($v === null) return 0;
		else{
			if ($this->tipo === 'INTEGER' || $this->tipo === 'INT'){
				return intval($v);
			}
			elseif ($this->tipo === 'FLOAT' || $this->tipo === 'REAL'){
				return round(floatval($v), 2);
			}
			else{
				return "{$v}";
			}
		}
	}
	public abstract function setGrafico();
	public abstract function setDetalles();

}

class GraficoMensual extends GraficoConDatos{

	/*public function setVal(){
		return $this->unidad->{"$this->val"} === null? 0 : floatval($this->unidad->{"$this->val"});
	}*/

	public function setGrafico(){
		return new GraficoColumnas();
	}

	public function setDetalles(){
		$this->grafico->setYMin(0)->setTituloY("Días");
		$this->grafico->setTitulo("Estadia promedio mensual");
	}
}

class GraficoPastel extends GraficoConDatos{
	public function setGrafico(){
		return new GraficoCircular();
	}

	public function setDetalles(){}
}

class GraficoPorUnidad extends GraficoConDatos{
/*	public function setVal(){
		return $this->unidad->{"$this->val"} === null? 0 : intval($this->unidad->{"$this->val"});
	}*/

	public function setGrafico(){
		return new GraficoColumnas();
	}

	public function setDetalles(){
		$this->grafico->setYMin(0)->setTituloY("Cantidad");
		$this->grafico->setTitulo("Derivaciones del último mes (".date("M, Y").")");
	}
}

class GraficoRanking extends GraficoConDatos{
	/*public function setVal(){
		return $this->unidad->{"$this->val"} === null ? 0 : intval($this->unidad->{"$this->val"});
	}*/

	public function setGrafico(){
		return new GraficoApilado();
	}

	public function setDetalles(){
		$this->grafico->setYMin(0)->setTituloY("Cantidad");
		$this->grafico->setTitulo("Ranking de categorías por servicio");
	}
}
