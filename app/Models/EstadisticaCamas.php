<?php namespace App\Models{

//include(app_path() . '/Models/Estadistica.php');
use DB;
use Log;
class EstadisticaCamasTotal extends Estadistica{

    protected $totalLibres = 0;
    protected $totalHabilitadas = 0;
    protected $totalVigentes = 0;
    protected $totalReservadas = 0;
    protected $totalOcupadas = 0;
    protected $totalBloqueadas = 0;
    protected $totalReconvertidas = 0;



    protected function set(){

        $this->res = $this->resumenCamasTotal();

    }

public function resumenCamasTotal(){
        $establecimientos = Establecimiento::whereHas("unidades", function($q){
            $q->where("visible", true)->whereHas("camas", function($qq){
                $qq->vigentes();
            });
        })
        ->with(["unidades" => function($q){
            $q->where("visible", true)
            ->where('id','<>',21)   // where para que no sume las camas de Emergencia Adultos
            ->where('id','<>',25)   // where para que no sume las camas de Pabellon Quirurjico
            ->with("camasLibres")
            ->with("camasBloqueadas")
            ->with("camasReservadas")
            ->with("camasOcupadas")
            ->with("camasReconvertidas");
        }])
            ->orderBy("nombre", "asc")
            ->get();
        $this->aplanar($establecimientos, "camasLibres");
        $this->aplanar($establecimientos, "camasBloqueadas");
        $this->aplanar($establecimientos, "camasReservadas");
        $this->aplanar($establecimientos, "camasOcupadas");
        $this->aplanar($establecimientos, "camasReconvertidas");
        $this->aplanar($establecimientos, "camasHabilitadas");
        $this->aplanar($establecimientos, "camasVigentes");

        $res = array();
        //var_dump($establecimientos);
        foreach($establecimientos as $obj){
            $res[$obj->nombre] = [];
            $res[$obj->nombre]["id"] = $obj->id;
            $res[$obj->nombre]["nombre"] = $obj->nombre;
            $res[$obj->nombre]["libres"] = $obj->camasLibres->count();
            $this->totalLibres += $res[$obj->nombre]["libres"];
            $res[$obj->nombre]["bloqueadas"] = $obj->camasBloqueadas->count();
            $this->totalBloqueadas += $res[$obj->nombre]["bloqueadas"];
            $res[$obj->nombre]["reservadas"] = $obj->camasReservadas->count();
            $this->totalReservadas += $res[$obj->nombre]["reservadas"];
            $res[$obj->nombre]["ocupadas"] = $obj->camasOcupadas->count();
            $this->totalOcupadas += $res[$obj->nombre]["ocupadas"];
            $res[$obj->nombre]["reconvertidas"] = $obj->camasReconvertidas->count();
            $this->totalReconvertidas += $res[$obj->nombre]["reconvertidas"];
            $res[$obj->nombre]["habilitadas"] = $obj->camasHabilitadas->count();
            $this->totalHabilitadas += $res[$obj->nombre]["habilitadas"];
            $res[$obj->nombre]["vigentes"] = $obj->camasVigentes->count();
            $this->totalVigentes += $res[$obj->nombre]["vigentes"];
        }
        return $res;

    }

    public function habilitadas()   {return $this->totalHabilitadas;}
    public function vigentes()      {return $this->totalVigentes;}
    public function reservadas()    {return $this->totalReservadas;}
    public function ocupadas()      {return $this->totalOcupadas;}
    public function disponibles()   {return $this->totalLibres;}
    public function deshabilitadas(){return $this->totalBloqueadas;}

    public function joinHistorialEstablecimiento($historial){
        return $this->joinCamaEstablecimiento( $historial->join("camas AS cm", "cm.id", "=", "h.cama") );
    }

    public function joinCamaEstablecimiento($camas){
        return $camas
        ->leftJoin("salas as s", "s.id", "=", "cm.sala")
        ->leftJoin("unidades_en_establecimientos as ue", "ue.id", "=", "s.establecimiento");
    }
}

class EstadisticaCamas extends EstadisticaCamasTotal{
    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        $this->unidad_obj = null;
        parent::__construct($fecha_inicio, $fecha);
    }

    protected function set(){
        $this->unidad_obj = UnidadEnEstablecimiento::getModel()->setFecha($this->fecha)
            ->where("establecimiento", $this->establecimiento)
            ->where('id','<>',21)   // where para que no sume las camas de Emergencia Adultos
            ->where('id','<>',25)   // where para que no sume las camas de Pabellon Quirurjico
            ->where("visible",true)
            ->with("camasLibres")
            ->with("camasBloqueadas")
            ->with("camasReservadas")
            ->with("camasOcupadas")
            ->with("camasReconvertidas")
            ->with("camasVigentes")
            ->with("camasHabilitadas")
            ->whereHas("camasEnFecha", function($q){
                $q->vigentes($this->fecha);
            })->get();
        ;
        $this->res = $this->resumenCamasTotal();
    }

    protected function resumenCamas(){
        $res = array();

        foreach($this->unidad_obj as $obj){
            $res[$obj->url] = [];
            $res[$obj->url]["nombre"] = $obj->alias;
            $res[$obj->url]["libres"] = $obj->camasLibres->count();
            $res[$obj->url]["bloqueadas"] = $obj->camasBloqueadas->count();
            $res[$obj->url]["reservadas"] = $obj->camasReservadas->count();
            $res[$obj->url]["ocupadas"] = $obj->camasOcupadas->count();
            $res[$obj->url]["reconvertidas"] = $obj->camasReconvertidas->count();
            $res[$obj->url]["habilitadas"] = $obj->camasHabilitadas->count();
            $res[$obj->url]["vigentes"] = $obj->camasVigentes->count();

            $this->totalLibres += $res[$obj->url]["libres"];
            $this->totalBloqueadas += $res[$obj->url]["bloqueadas"];
            $this->totalReservadas += $res[$obj->url]["reservadas"];
            $this->totalOcupadas += $res[$obj->url]["ocupadas"];
            $this->totalReconvertidas += $res[$obj->url]["reconvertidas"];
            $this->totalHabilitadas += $res[$obj->url]["habilitadas"];
            $this->totalVigentes += $res[$obj->url]["vigentes"];
        }

        

        return $res;

    }

    /*public function _habilitadas(){
        return parent::_habilitadas()->where("ue.establecimiento", "=", $this->establecimiento );
    }
    public function _vigentes(){
        return parent::_vigentes()->where("ue.establecimiento", "=", $this->establecimiento );
    }
    public function _reservadas(){
        return parent::_reservadas()->where("ue.establecimiento", "=", $this->establecimiento );
    }
    public function _ocupadas(){
        return parent::_ocupadas()->where("ue.establecimiento", "=", $this->establecimiento );
    }
    public function _disponibles(){
        return parent::_disponibles()->where("ue.establecimiento", "=", $this->establecimiento );
    }
    public function _deshabilitadas(){
        return parent::_deshabilitadas()->where("ue.establecimiento", "=", $this->establecimiento );
    }*/
}

class EstadisticaCamasUnidad extends EstadisticaCamas{

    public function __construct($establecimiento, $unidad, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->unidad = $unidad;
        parent::__construct($establecimiento, $fecha_inicio, $fecha);
    }

    public function set(){
        $this->unidad_obj = UnidadEnEstablecimiento::whereHas("camas", function($q){
            $q->vigentes();
        })
            ->with("camasLibres")
            ->with("camasBloqueadas")
            ->with("camasReservadas")
            ->with("camasOcupadas")
            ->with("camasReconvertidas")
            ->with("camasVigentes")
            ->with("camasHabilitadas")
            ->where("id", $this->unidad)
            ->get();
        $this->res = $this->resumenCamas();
    }

    /*public function _habilitadas(){
        return parent::_habilitadas()->where("ue.id", "=", $this->unidad );
    }
    public function _vigentes(){
        return parent::_vigentes()->where("ue.id", "=", $this->unidad );
    }
    public function _reservadas(){
        return parent::_reservadas()->where("ue.id", "=", $this->unidad );
    }
    public function _ocupadas(){
        return parent::_ocupadas()->where("ue.id", "=", $this->unidad );
    }
    public function _disponibles(){
        return parent::_disponibles()->where("ue.id", "=", $this->unidad );
    }
    public function _deshabilitadas(){
        return parent::_deshabilitadas()->where("ue.id", "=", $this->unidad );
    }*/

}

abstract class EstadisticaCategorizadaCamas extends EstadisticaCategorizada{

    public function joinOcupacionesEstablecimiento($historial){
        return DB::table(DB::raw("({$historial->toSql()}) as ho"))
        ->rightJoin("camas as cm", "cm.id", "=", "ho.cama")
        ->rightJoin("historial_camas_en_unidades as uc", "uc.cama", "=", "cm.id")
        ->rightJoin("unidades_en_establecimientos as ue", "ue.id", "=", "uc.unidad")
        ->rightJoin("establecimientos AS est", "est.id", "=", "ue.establecimiento")
        ->mergeBindings($historial)
        ;
    }
}

class RankingServiciosTotal extends EstadisticaCategorizadaCamas{

    public function riesgosXunidades(){
        return DB::table( DB::raw(
            "(SELECT riesgo.riesgo as categoria, riesgo.riesgo as id_categoria, est.id as id_serie, est.nombre as serie
            FROM UNNEST('{No categorizados, A1, A2, A3, B1, B2, B3, C1, C2, C3, D1, D2, D3}'::varchar[])
            AS riesgo
            FULL OUTER JOIN establecimientos AS est ON TRUE) AS rxu"
        ));
    }

    protected $tipo = 'integer';
    protected function setCategorias(){
        return new CategoriaRiesgos();
    }
    public function join(){
        return $this->joinOcupacionesEstablecimiento( $this->ultimasOcupaciones() )
        ->leftjoin("casos as cs", "cs.id", "=", "ho.caso")
        ->leftJoin("ultimas_evoluciones_pacientes as uev", "cs.id", "=", "uev.caso")
        ;
    }
    public function select(){
        return $this->join()
        ->select (DB::raw("CASE WHEN uev.riesgo::varchar is NULL THEN 'No categorizados' ELSE uev.riesgo::varchar END as categoria"), "{$this->serstr} AS serie",
            DB::raw("COUNT(ho.id) AS val")
        );
    }

    public function prepararMain(){
        $q = $this->select()
        ->groupBy($this->catstr)
        ->groupBy($this->serstr);
        $q2 = $this->riesgosXunidades();

        return DB::table( DB::raw("({$q->toSql()}) AS query"))
        ->rightJoin( DB::raw("({$q2->toSql()}) AS rxu"), function($j){
            $j->on(DB::raw("rxu.id_categoria::varchar"), "=", DB::raw("query.categoria::varchar"))->on(DB::raw("rxu.id_serie::varchar"), "=", DB::raw("query.serie::varchar"));
        })
        ->mergeBindings($q)->mergeBindings($q2);
    }

    public function queryMain(){
        return $this->prepararMain()
        ->select("rxu.serie", DB::raw("rxu.categoria as categoria"), DB::raw("max(query.val) as val"))
        ->groupBy("rxu.categoria")
        ->groupBy("rxu.serie");
    }

    protected function set(){
        $this->catstr = "uev.riesgo";
        $this->serstr = "est.id";
    }
}

class RankingServicios extends RankingServiciosTotal{
    public function select(){
        return parent::select()
        ->where("est.id", "=", $this->establecimiento);
    }

    public function riesgosXunidades(){
        return parent::riesgosXunidades()->where("id_serie", "=", $this->establecimiento);
    }

    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        parent::__construct($fecha_inicio, $fecha);
    }

    protected function setCategorias(){
        return new CategoriaRiesgos();
    }
}

class RankingCategoriasUnidad extends RankingServicios{
    public function __construct($establecimiento, $unidad, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->unidad = $unidad;
        parent::__construct($establecimiento, $fecha_inicio, $fecha);
    }
    public function riesgosXunidades(){
        return DB::table( DB::raw(
            "(SELECT riesgo.riesgo as categoria, riesgo.riesgo as id_categoria, ue.alias as serie, ue.id as id_serie
            FROM UNNEST('{A1, A2, A3, B1, B2, B3, C1, C2, C3, D1, D2, D3}'::riesgo[])
            AS riesgo
            FULL OUTER JOIN unidades_en_establecimientos ue ON TRUE
            WHERE ue.establecimiento = ?
            AND ue.id = ? ) AS rxu"
        ))
        ->addBinding($this->establecimiento)
        ->addBinding($this->unidad);
    }

    protected function set(){
        $this->catstr = "uev.riesgo";
        $this->serstr = "ue.id";
    }

    protected function setCategorias(){
        return new CategoriaRiesgos();
    }

    public function select(){
        return $this->join()
        ->select ("uev.riesgo as categoria", "{$this->serstr} AS serie",
            DB::raw("COUNT(ho.id) AS val")
        );
    }
}

class EstadiaPromedioTotal extends EstadisticaCategorizadaCamas{

    public function selectEstadia(){
        return $this->joinOcupacionesEstablecimiento($this->ultimasOcupaciones())
        ->leftJoin( $this->categorias->subquery(), $this->categorias->col(), "=", DB::raw("({$this->categorias->selectMeses('ho.fecha')})"))
        ->select(
            //$this->selectUnidad(),
            $this->categorias->col()
        );
    }

    public function select(){
        return $this->selectEstadia()
        ->addSelect("est.nombre as serie");
    }

    public function promedioEstadia(){
        return $this->select()
        ->addSelect(DB::raw(
            "extract( epoch FROM (date_trunc('second', CASE
                WHEN avg( ho.fecha_liberacion - ho.fecha ) IS NULL
                THEN avg( date_trunc('second', now()) - ho.fecha)
                ELSE avg( ho.fecha_liberacion - ho.fecha )
            END) ) ) /3600/24 AS val"
        ));
    }

    public function estadiaMensualUnidades(){
        return $this->promedioEstadia()
        ->groupBy("serie")
        ->groupBy("categoria");
    }
    public function getUnidad($idUnidad){
        $nombreUnidad = Unidad::find($idUnidad)->nombre;
        return $this->estadiaMensualUnidades()
        ->having(DB::raw("(CASE WHEN u.nombre IS NULL THEN uu.nombre ELSE u.nombre END)"), "=", $nombreUnidad)
        ->get();
    }

    protected function set(){
    	$this->tipo = 'float';
    }

    protected function setCategorias(){
    	return new CategoriaMeses(12);
    }

    protected function queryMain(){
    	return $this->estadiaMensualUnidades();
    }
}

class EstadiaPromedio extends EstadiaPromedioTotal{

    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        parent::__construct($fecha_inicio, $fecha);
    }

    public function queryMain(){
        return parent::queryMain()
        ->where("ue.establecimiento", "=", $this->establecimiento)
        ;
    }

   /* */
}

class EstadiaPromedioUnidad extends EstadiaPromedio{
    public function __construct($establecimiento, $unidad, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->unidad = $unidad;
        parent::__construct($establecimiento, $fecha_inicio, $fecha);
    }

    public function queryMain(){
        return parent::queryMain()
        ->where("ue.id", "=", $this->unidad)
        ;
    }

    public function select(){
        return $this->selectEstadia()
        ->addSelect("ue.alias as serie");
    }
}

abstract class DotacionTipoCama extends EstadisticaCat{
    protected function join(){
        $uc = "select * from ultimas_camas_unidades WHERE fecha <= ? ";
        return DB::table("camas_vigentes AS cm")
        ->join("salas as s", "s.id", "=", "cm.sala")
        ->join("unidades_en_establecimientos as ue", "s.establecimiento", "=", "ue.id")
        ->join("establecimientos as est", "est.id", "=", "ue.establecimiento")
        ->join(DB::raw("({$uc}) as uc"), "uc.cama", "=", "cm.id")
        ->leftJoin("tipos_cama as tcuc", "tcuc.id", "=", "uc.tipo")
        ->leftJoin("tipos_cama AS tccama", "tccama.id", "=", "cm.tipo")
        ->leftJoin("tipos_cama AS tcsala", "tcsala.id", "=", "s.tipo_cama")
        ->addBinding($this->fecha);
    }
}

class DotacionTipoCamaTotal extends DotacionTipoCama{

    protected function sub(){
        $sq = $this->join()
        ->select(DB::raw("
            CASE WHEN tcuc.id IS NULL THEN (
                CASE WHEN tccama.id IS NULL THEN
                tcsala.id
                ELSE tccama.id
                END
            ) ELSE
            tcuc.id
            END as tipo"
        ), "ue.establecimiento", "est.nombre as nombre", "ue.id as unidad");

        return DB::table(DB::raw("({$sq->toSql()}) AS sub"))
        ->rightJoin("tipos_cama AS tc", "tc.id", "=", "sub.tipo")
        ->mergeBindings($sq);
    }
    protected function select(){
        return $this->sub()
        ->select(DB::raw("count(sub.*) as val"), "tc.id as categoria");
    }

    protected function query(){
        return $this->select()
        ->addSelect(DB::raw("sub.establecimiento AS serie"));
    }

    protected function queryMain(){
        return $this->query()
        ->groupBy("tc.id")
        ->groupBy("serie");
    }

    protected function set(){
        $this->setCategorias(SetTiposCama::categorias());
        $this->setSeries(SetEstablecimientos::series());
    }
}

class DotacionTipoCamaEstablecimiento extends DotacionTipoCamaTotal{

    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        /* return $establecimiento; */
        $this->nombre = Establecimiento::findOrFail($this->establecimiento)->nombre;
        parent::__construct($fecha_inicio, $fecha);
    }

    protected function query(){
        //$bindings = $this->select()->getBindings();
        //array_unshift( $bindings, $this->nombre);
        return $this->select()
        ->addSelect(DB::raw("'{$this->nombre}'::varchar(100) as serie"))
        //->setBindings($bindings)
        ->where("sub.establecimiento", "=", $this->establecimiento);
    }

    protected function set(){
        /* return "set"; */
        $this->setCategorias(SetTiposCama::categorias());
        $this->setSeries(SetPersonalizado::series([$this->nombre]));
    }

}

class DotacionTipoCamaUnidad extends DotacionTipoCamaEstablecimiento{
    public function __construct($establecimiento, $unidad, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->unidad = $unidad;
        $this->alias = UnidadEnEstablecimiento::findOrFail($this->unidad)->alias;
        parent::__construct($establecimiento, $fecha_inicio, $fecha);

    }
    protected function query(){
        //$bindings = $this->select()->getBindings();
        //array_unshift( $bindings, $this->alias);
        return $this->select()
        ->addSelect(DB::raw("'{$this->alias}'::varchar(100) as serie"))
       // ->setBindings($bindings)
        ->where("sub.unidad", "=", $this->unidad);
    }

    protected function set(){
        $this->setCategorias(SetTiposCama::categorias());
        $this->setSeries(SetPersonalizado::series([$this->alias]));
    }
}

}
