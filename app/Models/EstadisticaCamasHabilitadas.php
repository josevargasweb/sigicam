<?php
use Carbon\Carbon;

abstract class EstadisticaDetalleCamas extends EstadisticaCategorizada{
    protected $tipo = 'INTEGER';

    public function setCategorias(){
        return new CategoriaEstablecimientos();
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
}

class EstadisticaCamasHabilitadas extends EstadisticaDetalleCamas {

    public function __construct(\Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        parent::__construct($fecha_inicio, $fecha);
    }

    protected $tipo = 'INTEGER';

//    protected function camasDesocupadas(){
//        return DB::table( DB::raw("(
//            select est.id as id_est,
//            ue.id as id_unidad,
//            h.cama,
//            h.fecha,
//            h.fecha_liberacion,
//            (?)::timestamp without time zone - (?)::timestamp without time zone - (h.fecha_liberacion - h.fecha) as desocupacion
//            from (
//                select h.id,
//                cm.id as cama,
//                cm.sala as sala,
//                case when h.cama is null THEN ? ELSE h.fecha END as fecha,
//                coalesce(fecha_liberacion, ? ) as fecha_liberacion
//                FROM
//                /* Historial de ocupaciones camas en ese tiempo */
//                (
//                select id, cama, caso, fecha, case WHEN fecha_liberacion > ? THEN null ELSE fecha_liberacion END as fecha_liberacion FROM t_historial_ocupaciones
//                 WHERE fecha >= ? AND fecha <= ?
//                ) AS h
//                /* Camas no bloqueadas en ese tiempo */
//                right join (select id, sala from camas where id not in (SELECT id from (
//                    select id, cama, fecha, case WHEN fecha_habilitacion > ? THEN null ELSE fecha_habilitacion END as fecha_habilitacion FROM t_historial_bloqueo_camas
//                    WHERE fecha >= ? AND fecha <= ? ) as sub WHERE fecha_habilitacion is NOT null) AND id NOT IN (SELECT cama from historial_eliminacion_camas WHERE fecha < ? )
//                ) as cm ON cm.id = h.cama
//            ) as h
//            INNER JOIN salas AS s ON h.sala = s.id
//            INNER JOIN unidades_en_establecimientos ue ON s.establecimiento = ue.id
//            INNER JOIN establecimientos est ON ue.establecimiento = est.id
//            LEFT JOIN (
//                select thc.*,
//                row_number() over (partition by thc.unidad order by thc.fecha desc) as rk
//                from t_historial_camas_en_unidades thc
//                WHERE (fecha <= ? AND fecha >= ?)
//            ) AS uc ON (ue.id = uc.unidad AND uc.rk = 1)
//            order by cama
//            )
//        AS deocup"
//        ))
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha_desde)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha_desde)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha_desde)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha_desde)
//        ->addBinding($this->fecha)
//        ->addBinding($this->fecha_desde)
//        ;
//    }
    protected function camasDesocupadas(){
        DB::statement("DROP TABLE IF EXISTS temp_historial_ocupaciones");
        DB::statement("DROP TABLE IF EXISTS temp_historial_bloqueo_camas");
        DB::statement("CREATE TEMP TABLE temp_historial_ocupaciones AS
WITH s AS (
    SELECT
    h.id, cm.id AS cama, caso, motivo,
    CASE WHEN fecha IS NULL THEN  '$this->fecha' ELSE CASE WHEN fecha < '$this->fecha_inicio' THEN '$this->fecha_inicio' ELSE fecha END END AS fecha,
    CASE WHEN fecha_liberacion > '$this->fecha' OR fecha_liberacion IS NULL THEN '$this->fecha' ELSE fecha_liberacion END AS fecha_liberacion
    FROM t_historial_ocupaciones AS h
    RIGHT JOIN camas cm ON cm.id = h.cama
    --
) SELECT id, cama, caso, motivo, fecha, fecha_liberacion, fecha_liberacion - fecha AS tiempo_ocup, '$this->fecha'::timestamp - '$this->fecha_inicio'::timestamp as intervalo, ('$this->fecha'::timestamp - '$this->fecha_inicio'::timestamp) - (fecha_liberacion - fecha) AS diff
FROM s
WHERE fecha_liberacion >= '$this->fecha_inicio' AND fecha <= '$this->fecha'");

        DB::statement("CREATE TEMP TABLE temp_historial_bloqueo_camas AS
WITH s AS (
    SELECT
    h.id, h.cama,
    CASE WHEN fecha < '$this->fecha_inicio' THEN '$this->fecha_inicio' ELSE fecha END AS fecha,
    CASE WHEN fecha_habilitacion > '$this->fecha' OR fecha_habilitacion IS NULL THEN '$this->fecha' ELSE fecha_habilitacion END AS fecha_habilitacion
    FROM t_historial_bloqueo_camas AS h
    --
) SELECT *
FROM s
WHERE fecha_habilitacion >= '$this->fecha_inicio' AND fecha <= '$this->fecha'");

        return DB::table(DB::raw("(SELECT bl.id, ue.establecimiento AS id_est, ue.id AS id_unidad, h.cama, h.fecha, h.fecha_liberacion, h.diff AS desocupacion
FROM temp_historial_ocupaciones AS h
LEFT JOIN temp_historial_bloqueo_camas AS bl ON bl.cama = h.cama
INNER JOIN camas AS cm ON cm.id = h.cama
INNER JOIN salas s ON s.id = cm.sala
INNER JOIN unidades_en_establecimientos AS ue ON ue.id = s.establecimiento
WHERE bl.id IS NULL
ORDER BY ue.establecimiento, ue.alias) AS deocup"));

    }
    protected function set(){
        $this->promedio_desuso = $this->selectDesusoPromedio()->get();
        $this->ultimo_desuso = $this->selectDesusoActual()
        //->where("deocup.rk", "=", "1")
        //->whereRaw("deocup.fecha_liberacion IS NOT NULL")
        ->get();
    }

    public function promedioDesuso(){
        return $this->promedio_desuso;
    }
    public function ultimoDesuso(){
        return $this->ultimo_desuso;
    }

    protected function join(){
        return $this->camasDesocupadas()
        ->join("camas_habilitadas as cm", "cm.id", "=", "deocup.cama")
        //->join("ultimas_camas_unidades as uc", "uc.cama", "=", "h.id")
        ->leftJoin("unidades_en_establecimientos as ue", "ue.id", "=", "deocup.id_unidad")
        ->rightJoin("establecimientos as est", "ue.establecimiento", "=", "est.id")
        ;
    }

    protected function joinDesusoActual(){
        return $this->join()
        ->join("salas as s", "s.id", "=", "cm.sala")
        ;
    }

    protected function selectDesusoPromedio(){
        $q = $this->join()
        ->select(
            "est.nombre AS nombre_unidad",
            DB::raw("to_char(date_trunc('hour', justify_interval(avg(deocup.desocupacion))),  'FMDDD \"días\", FMHH24 \"horas\"' ) AS promedio")
        );
        return $q->groupBy("est.nombre");
    }
    
    protected function selectDesusoActual(){
        return $this->joinDesusoActual()
        ->select(
            "est.nombre",
            "ue.alias as id_sala",
            //"s.id_sala as id_cama",
            DB::raw("to_char( justify_interval(avg(deocup.desocupacion)), 'FMDDD \"días\", FMHH24 \"horas\"' ) as desocupacion")
        )
        ->groupBy('est.nombre')->groupBy("ue.alias");
    }
}

class EstadisticaCamasHabilitadasEstablecimiento extends EstadisticaCamasHabilitadas{

    public function __construct($establecimiento, \Carbon\Carbon $fecha_inicio, \Carbon\Carbon $fecha){
        $this->establecimiento = $establecimiento;
        parent::__construct($fecha_inicio, $fecha);
    }

    protected function selectDesusoPromedio(){
        $q = $this->join()
        ->select(
            "ue.alias AS nombre_unidad",
            DB::raw("to_char(date_trunc('hour', justify_interval(avg(deocup.desocupacion))),  'FMDDD \"días\", FMHH24 \"horas\"' ) AS promedio")
        );
        $q->where("ue.establecimiento", "=", $this->establecimiento);
        return $q->groupBy("ue.alias");
    }

    protected function selectDesusoActual(){
        return $this->joinDesusoActual()
        ->select(
            "ue.alias as nombre",
            DB::raw("CASE WHEN s.nombre IS NOT NULL AND s.nombre NOT LIKE '' THEN s.nombre ELSE 'Sin nombre (' || s.id_sala::varchar ||')' END as id_sala"),
            //"cm.id_cama",
            DB::raw("to_char(justify_interval(avg(deocup.desocupacion)),  'FMDDD \"días\", FMHH24 \"horas\"' ) as desocupacion")
        )
        ->where("ue.establecimiento", "=", $this->establecimiento)
        ->groupBy("ue.alias")->groupBy("s.id_sala")->groupBy("s.nombre");
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





