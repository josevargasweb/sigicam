<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <head>
      <title>SIGICAM</title>
      
      <style>
        div {
          font: 13px;
        }

        .box3{
          display: inline-block !important;
        }

        table {
          border-collapse: collapse;
        }
        .table > thead:first-child > tr:first-child > th{
            color: cornsilk;
        }

        table, th, td {
          border: 1px solid black;
        }
      </style>

    </head>

    <body>
        <div class="row"  style="margin-left: 10px">
            <div class="box3" style="width:20%">
                Unidad de Estadística
                <br>
                {{$establecimiento}}
            </div>

            <div class="box3" style="width:60%; text-align:center;">
                OCUPACIONES DE CAMAS - DIAS DE ESTADA E INDICADORES
                <br>
                MES DE {{strtoupper($mes)}} - {{$anno}}
            </div>

            <div class="box3" style="width:19%">
                Fecha Actual: {{$hoy}}
                <br>
                Fecha Calculo: {{$fecha}}
            </div>
        </div>
        
        
        <table id="resumenMensual" class="table  table-condensed table-collapse">
            <thead>
                <tr style="background:#399865;">
                    <th rowspan="2" style="text-align:center;">Servicios Clinicos</th>
                    <th rowspan="2" style="text-align:center;">Existencias</th>
                    <th colspan="2" style="text-align:center;">CAMAS</th>
                    <th colspan="3" style="text-align:center;">DÍAS CAMAS</th>
                    <th colspan="2" style="text-align:center;">DÍAS ESTADA</th>
                    <th colspan="5" style="text-align:center;">INDICADORES</th>
                </tr>
                <tr style="background:#399865; color: cornsilk;">
                    <th>Dotación</th>
                    <th>Promedio Disponible</th>
                    <th>Disponible</th>
                    <th>Ocupados</th>
                    <th>Ocupados Beneficiarios</th>
                    <th>Total</th>
                    <th>Beneficiarios</th>
                    <th>Indice de sustitución</th>
                    <th>Porcentaje Ocupacional</th>
                    <th>Promedio Días Estada</th>
                    <th>Indice de Rotación</th>
                    <th>% Letalidad</th>
                </tr>                
            </thead>
            <tbody>
                @foreach($informacion as $info)
                    <tr>
                        <td>{{$info[0]}}</td>
                        <td>{{$info[1]}}</td>
                        <td>{{$info[2]}}</td>
                        <td>{{$info[3]}}</td>
                        <td>{{$info[4]}}</td>
                        <td>{{$info[5]}}</td>
                        <td>{{$info[6]}}</td>
                        <td>{{$info[7]}}</td>
                        <td>{{$info[8]}}</td>
                        <td>{{$info[9]}}</td>
                        <td>{{$info[10]}}</td>
                        <td>{{$info[11]}}</td>
                        <td>{{$info[12]}}</td>
                        <td>{{$info[13]}}</td>
                    </tr>
                    

                @endforeach

            </tbody>
        </table>
      
        
    </body>
</html>

