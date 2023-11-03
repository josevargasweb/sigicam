select count(*) as aggregate
from (
    SELECT * FROM camas c
    LEFT JOIN (
        select cama, fecha
        FROM historial_eliminacion_camas
        WHERE fecha <= '2014-10-21 23:59:59') AS el
    ON el.cama = c.id
    WHERE el.fecha IS NULL ) AS cm
left join (
    select * from (
        SELECT *, row_number() OVER (PARTITION BY cama ORDER BY fecha DESC) as rk
        FROM t_historial_bloqueo_camas
        WHERE fecha <= '2014-10-21 23:59:59'
        AND (fecha_habilitacion < '2014-10-21 23:59:59' OR fecha_habilitacion IS NULL )) AS h 
    where "rk" = 1) as h on "h"."cama" = "cm"."id"
left join "salas" as "s" on "s"."id" = "cm"."sala"
left join "unidades_en_establecimientos" as "ue" on "ue"."id" = "s"."establecimiento"
left join (
    select * from (
        SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
        FROM t_historial_ocupaciones
        WHERE fecha <= '2014-10-21 23:59:59'
        AND (fecha_liberacion < '2014-10-21 23:59:59' OR fecha_liberacion IS NULL)) AS h) as uo on "uo"."cama" = "cm"."id"
left join (
    select * from (
        SELECT *,
        date_trunc('seconds'::text, fecha + tiempo) AS queda, row_number() OVER (partition by cama order by fecha DESC) as rk
        from t_reservas
        WHERE fecha + tiempo >= '2014-10-21 00:00:00') AS h) as ur on "ur"."cama" = "cm"."id"
where "h"."id" is null
and "uo"."cama" is null
and "ur"."cama" is null
and "ue"."establecimiento" = 8
and "ue"."id" = 4;





select cm.* from (
    SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
    FROM t_historial_ocupaciones
    WHERE fecha <= '2014-11-25 23:59:59'
    AND (fecha_liberacion < '2014-11-25 23:59:59' OR fecha_liberacion IS NULL)
) AS h
inner join "camas" as "cm" on "cm"."id" = "h"."cama"
left join "salas" as "s" on "s"."id" = "cm"."sala"
left join "unidades_en_establecimientos" as "ue" on "ue"."id" = "s"."establecimiento"
where "rk" = 1
and "fecha_liberacion" < '2014-11-25 00:00:00'
and "ue"."establecimiento" = 8;




select count(*) as aggregate
from (
    SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
    FROM t_historial_ocupaciones
    WHERE fecha <= '2014-11-25 23:59:59'
    AND (fecha_liberacion < '2014-11-25 23:59:59' OR fecha_liberacion IS NULL)
) AS h
inner join "camas" as "cm" on "cm"."id" = "h"."cama"
left join "salas" as "s" on "s"."id" = "cm"."sala"
left join "unidades_en_establecimientos" as "ue" on "ue"."id" = "s"."establecimiento"
left join (
    select * from (
        SELECT *, row_number() OVER (PARTITION BY cama ORDER BY fecha DESC) as rk
        FROM t_historial_bloqueo_camas
        WHERE fecha <= 1
        AND (fecha_habilitacion < '2014-11-25 23:59:59' OR fecha_habilitacion IS NULL )
    ) AS h
    where "h"."rk" = '2014-11-25 23:59:59'
) AS ub on "ub"."cama" = "cm"."id"
where "h"."rk" = 1
and ("fecha_liberacion" < '2014-11-25 00:00:00' or "fecha_liberacion" is null)
and "ue"."establecimiento" = 8;




/*
select * from crosstab($$ select "ue"."alias" as "nombre_unidad", "meses"."categoria", extract( epoch FROM (date_trunc('second', CASE
WHEN avg( ho.fecha_liberacion - ho.fecha ) IS NULL
THEN avg( date_trunc('second', now()) - ho.fecha)
ELSE avg( ho.fecha_liberacion - ho.fecha )
END) ) ) /3600/24 AS valor from (select * from 
(SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
FROM t_historial_ocupaciones
WHERE fecha <=  '$$ || '2014-11-25 23:59:59' || $$'  AND (fecha_liberacion <  '$$ || '2014-11-25 23:59:59' || $$'  OR fecha_liberacion IS NULL)) AS h) as ho right join "camas" as "cm" on "cm"."id" = "ho"."cama" right join "historial_camas_en_unidades" as "uc" on "uc"."cama" = "cm"."id" right join "unidades_en_establecimientos" as "ue" on "ue"."id" = "uc"."unidad" left join (select 
(extract(month FROM b.val)::text
||'-'||
extract(year FROM b.val)::text)
as categoria from 
(select generate_series(
    now() - '12 month'::interval,
    now(),
    '1 month'::interval
)::date val) as b 
order by "b"."val" asc) AS meses on "meses"."categoria" = (
(extract(month FROM ho.fecha)::text
||'-'||
extract(year FROM ho.fecha)::text)
) where ue.establecimiento = 8 group by "nombre_unidad", "categoria" order by 1 $$, $$select 
(extract(month FROM b.val)::text
||'-'||
extract(year FROM b.val)::text)
as categoria from 
(select generate_series(
    now() - '12 month'::interval,
    now(),
    '1 month'::interval
)::date val) as b 
order by "b"."val" asc, "categoria" asc$$) as (serie varchar,  "0" float , "1" float , "2" float , "3" float , "4" float , "5" float , "6" float , "7" float , "8" float , "9" float , "10" float , "11" float , "12" float  );*/