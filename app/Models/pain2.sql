select *
from crosstab($$
    select "ue"."alias" as "nombre_unidad",
    "riesgos"."categoria",
    COUNT(ho.id) AS val
    from "ultimas_ocupaciones" as "ho"
    right join "camas" as "cm" on "cm"."id" = "ho"."cama"
    right join "historial_camas_en_unidades" as "uc" on "uc"."cama" = "cm"."id"
    right join "unidades_en_establecimientos" as "ue" on "ue"."id" = "uc"."unidad"
    left join "casos" as "cs" on "cs"."id" = "ho"."caso"
    left join "ultimas_evoluciones_pacientes" as "uev" on "cs"."id" = "uev"."caso"
    full outer join (
        SELECT * FROM UNNEST(''{A1, A2, A3, A4, A5, B1, B2, B3, B4, B5, C1, C2, C3, C4, C5}''::varchar[]) AS categoria
    ) AS riesgos on "riesgos"."categoria" = uev.riesgo::varchar
    where ue.establecimiento = 8
    and ue.i = 4
    group by "riesgos"."categoria", "nombre_unidad"
    order by 1
$$, $$
    select *
    from UNNEST(''{A1, A2, A3, A4, A5, B1, B2, B3, B4, B5, C1, C2, C3, C4, C5}''::varchar[]) AS categoria
    order by "categoria" asc
$$)
as (
    serie varchar,
    "0" integer ,
    "1" integer ,
    "2" integer ,
    "3" integer ,
    "4" integer ,
    "5" integer ,
    "6" integer ,
    "7" integer ,
    "8" integer ,
    "9" integer ,
    "10" integer ,
    "11" integer ,
    "12" integer ,
    "13" integer ,
    "14" integer
)
;



prepare plan30 as
select * from crosstab($$
    select "ue"."alias"::varchar as "nombre_unidad"::varchar, "meses"."categoria"::integer,
    extract( epoch FROM (date_trunc(''second'', CASE 
        WHEN avg( ho.fecha_liberacion - ho.fecha ) IS NULL
        THEN avg( date_trunc(''second'', now()) - ho.fecha)
        ELSE avg( ho.fecha_liberacion - ho.fecha )
        END) ) ) /3600/24 AS valor
    from (
        select *
        from (
            SELECT *, row_number() OVER (partition by cama order by fecha DESC) as rk
            FROM t_historial_ocupaciones
            WHERE fecha <= (''' $1 ''')::timestamp
            AND (fecha_liberacion < (''' $2 ''')::timestamp OR fecha_liberacion IS NULL)
        ) AS h) as ho
    right join "camas" as "cm" on "cm"."id" = "ho"."cama"
    right join "historial_camas_en_unidades" as "uc" on "uc"."cama" = "cm"."id"
    right join "unidades_en_establecimientos" as "ue" on "ue"."id" = "uc"."unidad"
    left join (
        select (
            extract(month FROM b.val)::text ||''-''|| extract(year FROM b.val)::text
        ) as categoria
        from (
            select generate_series(
                now() - ''12 month''::interval,
                now(),
                ''1 month''::interval
            )::date val
        ) as b 
        order by "b"."val" asc
    ) AS meses on "meses"."categoria" = ((extract(month FROM ho.fecha)::text ||''-''|| extract(year FROM ho.fecha)::text))
    where ue.establecimiento = '''$3'''
    group by "nombre_unidad", "categoria"
    order by 1
$$, $$select 
(extract(month FROM b.val)::text
||''-''||
extract(year FROM b.val)::text)
as categoria from 
(select generate_series(
now() - ''12 month''::interval,
now(),
''1 month''::interval
)::date val) as b 
order by "b"."val" asc, "categoria" asc$$) as (serie varchar,  "0" float , "1" float , "2" float , "3" float , "4" float , "5" float , "6" float , "7" float , "8" float , "9" float , "10" float , "11" float , "12" float  )