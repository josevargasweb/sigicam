<?php
use Carbon\Carbon;

class EstadisticaCamasDeshabilitadas extends EstadisticaDetalleCamas{
    protected $fecha_inicio;
    public function __construct(\Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        //$this->fecha_desde = Carbon::createFromFormat('d-m-Y', $fecha_desde)->toDateString();
        parent::__construct($fecha_inicio, $fecha);
    }
    
    protected function queryMain(){
        return $this->join()
        ->select(
            DB::raw("'serie' as serie"),
            "est.nombre as categoria",
            DB::raw("count(cm.id) as val")
        )
        ->groupBy("est.nombre")
        ->groupBy("serie");
    }

    protected function join(){
        return DB::table('historial_bloqueo_camas as h')
        ->join('camas as cm', "h.cama", "=", "cm.id")
        ->leftJoin('historial_eliminacion_camas as hec', "cm.id", "=", "hec.cama")
        ->join("ultimas_camas_unidades as uc", "uc.cama", "=", "cm.id")
        /*->join("salas as s", "s.id", "=", "cm.sala")*/
        ->rightJoin("unidades_en_establecimientos as ue", "ue.id", "=", "uc.unidad")
        ->rightJoin("establecimientos as est", "est.id", "=", "ue.establecimiento")
        ->where(function($q){
            $q->where("hec.fecha", "=", null)->orWhere(function($q){
                $q->where("hec.fecha", "<=", $this->fecha)
                ->where("hec.fecha", ">=", $this->fecha_inicio);
            });

        })
        ->where(function($q){
            $q->where("h.fecha_habilitacion", "=", null)->orWhere(function($q){
                $q->where("h.fecha_habilitacion", "<=", $this->fecha)
                ->where("h.fecha_habilitacion", ">=", $this->fecha_inicio);
            });
        })
        /*->whereNotNull("s.id")*/
        ;
    }

    protected function selectActual(){
        return $this->joinActual()
        ->select(
            "est.nombre as nombre_unidad",
            "ue.alias as id_sala",
            "s.id_sala as id_cama",
            DB::raw("count(h.fecha) as tiempo")
        )
        ->groupBy("est.nombre")->groupBy("ue.alias")->groupBy("s.id_sala");
    }

    protected function joinActual(){
        return $this->join()
        ->join("salas as s", "s.id", "=", "cm.sala");
    }

    protected function selectPromedio(){
        return $this->join()->select(
            "est.nombre as nombre_unidad",
            DB::raw("to_char( justify_interval(max('{$this->fecha}'::timestamp without time zone - h.fecha)), 'FMdd \"días\", FMHH24 \"horas\"' ) as promedio")
        )
        ->groupBy("est.nombre");
    }

    protected function set(){
        $this->promedios = $this->selectPromedio()
                                ->get();
        $this->actual = $this->selectActual()
                                ->get();
    }

    public function camasDeshabilitadas(){
        return $this->actual;
    }

    public function promediosDeshabilitadas(){
        return $this->promedios;
    }
}

class EstadisticaCamasDeshabilitadasEstablecimiento extends EstadisticaCamasDeshabilitadas{

    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        parent::__construct($fecha_inicio, $fecha);
    }

    protected $tipo = 'INTEGER';

    protected function set(){
        $this->promedios = $this->selectPromedio()
                                ->where("ue.establecimiento", "=", $this->establecimiento)
                                ->get();
        $this->actual = $this->selectActual()
                                ->where("ue.establecimiento", "=", $this->establecimiento)
                                ->get();
    }
    
    protected function selectPromedio(){
        return $this->join()
        ->select(
            "ue.alias as nombre_unidad",
            DB::raw("to_char( justify_interval(max('{$this->fecha}'::timestamp without time zone - h.fecha)), 'FMdd \"días\", FMHH24 \"horas\"' ) as promedio")
        )
        ->groupBy('ue.alias');
    }

    protected function selectActual(){
        return $this->joinActual()
        ->select(
            "ue.alias as nombre_unidad",
            DB::raw("CASE WHEN s.nombre IS NOT NULL AND s.nombre NOT LIKE '' THEN s.nombre ELSE 'Sin nombre (' || s.id_sala::varchar ||')' END as id_sala"),
            "cm.id_cama",
            DB::raw("to_char(justify_interval('{$this->fecha}'::timestamp without time zone - h.fecha),  'FMdd \"días\", FMHH24 \"horas\"' ) as tiempo")
        )
        ;
    }

    public function setCategorias(){
        return new CategoriaUnidades($this->establecimiento);
    }

    protected function queryMain(){
        return $this->join()
        ->select(
            DB::raw("'serie' as serie"),
            "ue.alias as categoria",
            DB::raw("count(cm.id) as val")
        )
        ->where("ue.establecimiento", "=", $this->establecimiento)
        ->groupBy("alias")
        ->groupBy("serie");
    }

}

