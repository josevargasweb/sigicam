<?php

namespace App\Models;

use DB;

class EstadisticaCVC extends Estadistica{

	private $establecimiento;
	
    protected function set(){

        $this->res = $this->total();

    }
    public function setEstablecimiento($es)
    {
    	$this->establecimiento=$es;
    }
    public function categorias()
    {
    	$r=DB::select(DB::raw("(SELECT
                (extract(year FROM m)::text ||'-'||extract(month from m)::text) as mes
                FROM
                (select generate_series(
                    now() - '12 month'::interval,
                    now(),
                    '1 month'::interval
                )::date as m) as b ORDER BY m ASC)"));
        $arr=array();
        for($i=0;$i<count($r);$i++)
        {
        	$arr[]=$r[$i]->mes;
        }
        return $arr;
    }
    public function total()
    {
    	$sqlest="";
    	if($this->establecimiento)
    		$sqlest=" AND c.establecimiento=".$this->establecimiento;
    	$categorias=$this->categorias();
    	$valores=array();
    	$primero=date("Y-m-01");
    	for($i=0;$i<count($categorias);$i++)
    	{
    		$fecha_siguiente=date("Y-m-01",strtotime("$primero +1 month"));
    		if($i<count($categorias)-1)
    			$fecha_siguiente=$categorias[$i+1]."-01";
    		$sql=DB::select(DB::raw("select cv.cvc_dias,i.fecha from casos as c,infecciones as i,cvc as cv where cv.id_infeccion=i.id and i.caso=c.id and i.fecha >= '".$categorias[$i]."-01' AND i.fecha <'$fecha_siguiente' $sqlest"));
    		
    		$arr=array();
    		$arr[]=count($sql);
    	/*	for($j=0;$j<count($sql);$j++)
    		{
    			$arr[]=$sql[$j]->cvc_dias;
    		}*/
    		$resultado=empty($sql)?array(0):array_sum($arr);
    		$valores[]=$resultado;
    	}
    	
    	return $valores;
    }
}



