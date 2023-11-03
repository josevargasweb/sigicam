<?php

class EstadisticaLocalizacion extends Estadistica{

	private $establecimiento;
	private $localizacion;
	
    protected function set(){

        $this->res = $this->total();

    }
    public function setLocalizacion($loc)
    {
    	$this->localizacion=$loc;
    }
    public function setEstablecimiento($es)
    {
    	$this->establecimiento=$es;
    }
    public function categorias()
    {
    	$cat=array(
    		"Parto vaginal",
    		"Cesarea con trabajo de parto",
    		"Cesarea sin trabajo de parto"
    		);
    	return $cat;
    }
    public function total()
    {
    	if($this->localizacion==null)
    		return -1;
    	$sqlest="";
    	if($this->establecimiento)
    		$sqlest=" AND casos.establecimiento=".$this->establecimiento;
    	$categorias=$this->categorias();
    	$valores=array();
    	$primero=date("Y-m-01");
    	for($i=0;$i<count($categorias);$i++)
    	{
    		$fecha_siguiente=date("Y-m-01",strtotime("$primero +1 month"));
    		if($i<count($categorias)-1)
    			$fecha_siguiente=$categorias[$i+1]."-01";
    		$sql=DB::select(DB::raw("select COUNT(*)as cantidad from iaas as i left join infecciones as c  on c.id=i.id_infeccion left join casos on c.caso=casos.id where i.fecha_inicio >= '".$this->fecha_inicio."-01' AND i.fecha_inicio <'".$this->fecha."' AND i.procedimiento_invasivo='".$categorias[$i]."' AND i.localizacion='".$this->localizacion."' $sqlest"));

    		$resultado=empty($sql)?0:$sql[0]->cantidad;
    		$valores[]=$resultado;
    	}
    	
    	return $valores;
    }
}



