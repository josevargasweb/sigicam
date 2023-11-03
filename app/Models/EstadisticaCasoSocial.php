<?php namespace App\Models{

use DB;

class EstadisticaCasoSocial extends Estadistica{

	private $establecimiento;
	
    protected function set(){

        $this->res = $this->totalCasos();

    }
    public function setEstablecimiento($es)
    {
    	$this->establecimiento=$es;
    }
    public function totalCasos()
    {
    	$sqlest="";
    	if($this->establecimiento)
    		$sqlest=" AND establecimiento=".$this->establecimiento;

        $casos_true=DB::select(DB::raw("SELECT COUNT(caso_social) AS casos_true FROM casos WHERE caso_social=true AND fecha_termino is not null AND updated_at>='".$this->fecha_inicio."' AND updated_at<='".$this->fecha."' $sqlest"));
        $casos_pasados_true=DB::select(DB::raw("SELECT COUNT(caso_social) AS casos_pasados_true FROM casos WHERE caso_social=true AND fecha_termino is null AND updated_at>='".$this->fecha_inicio."' AND updated_at<='".$this->fecha."' $sqlest"));
        $casos_false=DB::select(DB::raw("SELECT COUNT(caso_social) AS casos_false FROM casos WHERE caso_social=false AND updated_at>='".$this->fecha_inicio."' AND updated_at<='".$this->fecha."' $sqlest"));
        $a=(int)$casos_true[0]->casos_true;//2
        $b=(int)$casos_false[0]->casos_false; //1
        $c=(int)$casos_pasados_true[0]->casos_pasados_true;//3

        $casos=array($a,$b,$c);
    	return $casos;
    }
}

}


