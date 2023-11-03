<?php

class EstadisticaInfecciones extends Estadistica{

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
    		$sql=DB::select(DB::raw("select COUNT(*)as cantidad from infecciones as i left join casos as c  on c.id=i.caso where i.fecha >= '".$categorias[$i]."-01' AND i.fecha <'$fecha_siguiente' $sqlest"));

    		$resultado=empty($sql)?0:$sql[0]->cantidad;
    		$valores[]=$resultado;
    	}
    	
    	return $valores;
    }

public static function obtenerListaIAAS($sqlest){
        $response=[];

        $iaas=DB::table( DB::raw("(
            select e.nombre as establecimiento,p.rut,p.dv,p.nombre,p.apellido_materno,p.apellido_paterno,pi.aislamiento,pi.fallecimiento,pi.motivo_fallecimiento,i.fecha_termino,i.motivo_termino,ia.servicioiaas,
            ia.fecha_inicio,ia.fecha_iaas,ia.localizacion,ia.procedimiento_invasivo,ia.agente1,ia.sensibilidad1,ia.intermedia1,
            ia.resistencia1,ia.sensibilidad2,ia.intermedia2,ia.resistencia2,ia.sensibilidad3,ia.intermedia3,ia.resistencia3,ia.sensibilidad4,ia.intermedia4,ia.resistencia4,
            ia.sensibilidad5,ia.intermedia5,ia.resistencia5,ia.sensibilidad6,ia.intermedia6,ia.resistencia6
            ,ia.agente2,ia.sensibilidad7,ia.intermedia7,ia.resistencia7,ia.sensibilidad8,ia.intermedia8,ia.resistencia8,
            ia.sensibilidad9,ia.intermedia9,ia.resistencia9,ia.sensibilidad10,ia.intermedia10,ia.resistencia10,
            ia.sensibilidad11,ia.intermedia11,ia.resistencia11,ia.sensibilidad12,ia.intermedia12,ia.resistencia12
            from iaas as ia,infecciones as i,pacientes_infeccion as pi, casos as c, pacientes as p,establecimientos as e
            where i.id=ia.id_infeccion and i.id=pi.id_infeccion and i.caso=c.id and p.id=c.paciente and c.establecimiento=e.id 
            $sqlest order by e.nombre
            ) as re"
                    ))->get();

        foreach($iaas as $datoiaas){

            $apellido=$datoiaas->apellido_paterno." ".$datoiaas->apellido_materno;
            $dv=($datoiaas->dv == 10) ? "K" : $datoiaas->dv;
            $rut=(empty($datoiaas->rut)) ? "" : $datoiaas->rut."-".$dv;
            $response[]=[$datoiaas->establecimiento,$datoiaas->nombre, $apellido, $rut, $datoiaas->aislamiento, $datoiaas->fallecimiento, $datoiaas->motivo_fallecimiento,
            $datoiaas->fecha_termino,$datoiaas->motivo_termino,$datoiaas->servicioiaas,$datoiaas->fecha_inicio,$datoiaas->fecha_iaas,$datoiaas->localizacion,
            $datoiaas->procedimiento_invasivo,$datoiaas->agente1,$datoiaas->sensibilidad1,$datoiaas->intermedia1,$datoiaas->resistencia1,
            $datoiaas->sensibilidad2,$datoiaas->intermedia2,$datoiaas->resistencia2,$datoiaas->sensibilidad3,$datoiaas->intermedia3,$datoiaas->resistencia3,
            $datoiaas->sensibilidad4,$datoiaas->intermedia4,$datoiaas->resistencia4,$datoiaas->sensibilidad5,$datoiaas->intermedia5,$datoiaas->resistencia5,
            $datoiaas->sensibilidad6,$datoiaas->intermedia6,$datoiaas->resistencia6,$datoiaas->agente2,
            $datoiaas->sensibilidad7,$datoiaas->intermedia7,$datoiaas->resistencia7,$datoiaas->sensibilidad8,$datoiaas->intermedia8,$datoiaas->resistencia8,
            $datoiaas->sensibilidad9,$datoiaas->intermedia9,$datoiaas->resistencia9,$datoiaas->sensibilidad10,$datoiaas->intermedia10,$datoiaas->resistencia10,
            $datoiaas->sensibilidad11,$datoiaas->intermedia11,$datoiaas->resistencia11,$datoiaas->sensibilidad12,$datoiaas->intermedia12,$datoiaas->resistencia12
            ];
        }
        return $response;
    }

public static function obtenerListaFechaIAAS($fecha2){

        $establecimiento=Session::get('idEstablecimiento');
        $sqlest="";
        if(!empty($establecimiento))$sqlest="AND c.establecimiento=$establecimiento";

        $fecha = Carbon\Carbon::createFromFormat("m-Y",$fecha2);
        $anno = $fecha->year;
        $mes = $fecha->month;

        $fecha_desde = \Carbon\Carbon::createFromDate($anno, $mes, 1)->firstOfMonth();
        $fecha_hasta = \Carbon\Carbon::createFromDate($anno, $mes, 1)->lastOfMonth();

        $response=[];

        $iaas=DB::table( DB::raw("(
            select e.nombre as establecimiento,p.rut,p.dv,p.nombre,p.apellido_materno,p.apellido_paterno,pi.aislamiento,pi.fallecimiento,pi.motivo_fallecimiento,i.fecha_termino,i.motivo_termino,ia.servicioiaas,
            ia.fecha_inicio,ia.fecha_iaas,ia.localizacion,ia.procedimiento_invasivo,ia.agente1,ia.sensibilidad1,ia.intermedia1,
            ia.resistencia1,ia.sensibilidad2,ia.intermedia2,ia.resistencia2,ia.sensibilidad3,ia.intermedia3,ia.resistencia3,ia.sensibilidad4,ia.intermedia4,ia.resistencia4,
            ia.sensibilidad5,ia.intermedia5,ia.resistencia5,ia.sensibilidad6,ia.intermedia6,ia.resistencia6
            ,ia.agente2,ia.sensibilidad7,ia.intermedia7,ia.resistencia7,ia.sensibilidad8,ia.intermedia8,ia.resistencia8,
            ia.sensibilidad9,ia.intermedia9,ia.resistencia9,ia.sensibilidad10,ia.intermedia10,ia.resistencia10,
            ia.sensibilidad11,ia.intermedia11,ia.resistencia11,ia.sensibilidad12,ia.intermedia12,ia.resistencia12
            from iaas as ia,infecciones as i,pacientes_infeccion as pi, casos as c, pacientes as p,establecimientos as e
            where i.id=ia.id_infeccion and i.id=pi.id_infeccion and i.caso=c.id and p.id=c.paciente
            and i.fecha >='$fecha_desde' and i.fecha<='$fecha_hasta' and c.establecimiento=e.id 
            $sqlest order by e.nombre
            ) as re"
                    ))->get();

        foreach($iaas as $datoiaas){

            $apellido=$datoiaas->apellido_paterno." ".$datoiaas->apellido_materno;
            $dv=($datoiaas->dv == 10) ? "K" : $datoiaas->dv;
            $rut=(empty($datoiaas->rut)) ? "" : $datoiaas->rut."-".$dv;
            $response[]=[$datoiaas->establecimiento,$datoiaas->nombre, $apellido, $rut, $datoiaas->aislamiento, $datoiaas->fallecimiento, $datoiaas->motivo_fallecimiento,
            $datoiaas->fecha_termino,$datoiaas->motivo_termino,$datoiaas->servicioiaas,$datoiaas->fecha_inicio,$datoiaas->fecha_iaas,$datoiaas->localizacion,
            $datoiaas->procedimiento_invasivo,$datoiaas->agente1,$datoiaas->sensibilidad1,$datoiaas->intermedia1,$datoiaas->resistencia1,
            $datoiaas->sensibilidad2,$datoiaas->intermedia2,$datoiaas->resistencia2,$datoiaas->sensibilidad3,$datoiaas->intermedia3,$datoiaas->resistencia3,
            $datoiaas->sensibilidad4,$datoiaas->intermedia4,$datoiaas->resistencia4,$datoiaas->sensibilidad5,$datoiaas->intermedia5,$datoiaas->resistencia5,
            $datoiaas->sensibilidad6,$datoiaas->intermedia6,$datoiaas->resistencia6,$datoiaas->agente2,
            $datoiaas->sensibilidad7,$datoiaas->intermedia7,$datoiaas->resistencia7,$datoiaas->sensibilidad8,$datoiaas->intermedia8,$datoiaas->resistencia8,
            $datoiaas->sensibilidad9,$datoiaas->intermedia9,$datoiaas->resistencia9,$datoiaas->sensibilidad10,$datoiaas->intermedia10,$datoiaas->resistencia10,
            $datoiaas->sensibilidad11,$datoiaas->intermedia11,$datoiaas->resistencia11,$datoiaas->sensibilidad12,$datoiaas->intermedia12,$datoiaas->resistencia12
            ];
        }
         return $response;
    }


}



