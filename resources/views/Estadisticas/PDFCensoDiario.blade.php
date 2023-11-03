<!DOCTYPE HTML>
<html>
<head>
  <title>Título de la página</title>
  <meta charset="UTF-8">
  {{ HTML::style('css/bootstrap.css') }}

  {{ HTML::script('js/jquery-1.9.1.min.js') }}
  {{ HTML::script('js/bootstrap.js') }}
  <style>

      .centrar{
          text-align:center;
      }

      .mover{
          margin-left:100px !important;
      }

      .box2 {
        display: inline-block;
        width: 170px;
        height: 10px;
        border-top: 1px solid;
        margin-left:60px;
        }

        .box3{
            display: inline-block;
        }
  </style>
  
</head>
<body>
    <div class="centrar">
        <label style="font-size:20px;">CENSO DIARIO</label>
    </div>

    <div style="margin-top:10px;">
        <div class="box3" style="width:47em; margin-left:30px;">Nombre Establecimiento: {{$nombreEstablecimiento}}</div>
        <div class="box3" style="width:80px;">Día: {{$dia}}</div>
        <div class="box3" style="width:80px;">Mes: {{$mes}}</div>
        <div class="box3" style="width:100px;">Año: {{$ano}}</div>
    </div>
    <br><br><br>

    @foreach($detalle_estab as $detalle)
        <div>
            <div class="box3" style="width:26%; margin-left:80px; font-size:11px;">Servicio: {{$detalle["nombre_servicio"]}}</div>
            <div class="box3" style="width:35%; font-size:11px;">Existencia de pacientes hospitalizados a las 0 horas: {{$detalle["numero_pacientes_hospitalizados"]}}</div>
            <div class="box3" style="width:25%; font-size:11px;">N° de camas en trabajos: {{$detalle["numero_camas_en_trabajo"]}}</div>
        </div>
    @endforeach

    <!-- Salto de página -->
    <div style="page-break-after:always;"></div>
    <!-- Fin salto página -->

        <!-- <table border="1" width="680" style="margin-left:45px; margin-top:10px;">  
            <tr>
                <th colspan="8" style="border:0;"></th>
            </tr>
            <tr>
                <th class="centrar" rowspan="2" style="width: 30%; font_size:13px;">Nombre y Apellidos</th>
                <th class="centrar" rowspan="2" style="width: 12%; font_size:13px;">Servicio</th>
                <th class="centrar" colspan="2" style="width: 24%; font_size:13px;">Ingresos</th>
                <th class="centrar" colspan="3" style="width: 36%; font_size:13px;">Egresos</th>
                <th class="centrar" rowspan="2" style="width: 12%; font_size:13px;">Ingresos y egresos en el mismo día</th>
            </tr>
            <tr>
                <td class="centrar" style="font_size:10px;">Desde fuera o de otro hospital</td>
                <td class="centrar" style="font_size:10px;">De servicios de este mismo hospital</td>
                <td class="centrar" style="font_size:10px;">Alta al hogar u otro hospital</td>
                <td class="centrar" style="font_size:10px;">Traslado a otro servicio de este hospital</td>
                <td class="centrar" style="font_size:10px;">Fallecido</td>
            </tr>
        <table> -->
            @if(count($datos) == 0)
                <table border="1" width="680" style="margin-left:45px; margin-top:50px;">
                <tr>
                    <th colspan="8" style="border:0;"></th>
                </tr>
                <tr>
                    <th class="centrar" rowspan="2" style="width: 30%; font_size:13px;">Nombre y Apellidos</th>
                    <th class="centrar" rowspan="2" style="width: 17%; font_size:13px;">Servicio</th>
                    <th class="centrar" colspan="2" style="width: 22%; font_size:13px;">Ingresos</th>
                    <th class="centrar" colspan="3" style="width: 34%; font_size:13px;">Egresos</th>
                    <th class="centrar" rowspan="2" style="width: 11%; font_size:13px;">Ingresos y egresos en el mismo día</th>
                </tr>
                <tr>
                    <td class="centrar" style="font_size:10px;">Desde fuera o de otro hospital</td>
                    <td class="centrar" style="font_size:10px;">De servicios de este mismo hospital</td>
                    <td class="centrar" style="font_size:10px;">Alta al hogar u otro hospital</td>
                    <td class="centrar" style="font_size:10px;">Traslado a otro servicio de este hospital</td>
                    <td class="centrar" style="font_size:10px;">Fallecido</td>
                </tr>
                
                <tr>
                    <td>TOTAL</td>
                    <td class="centrar"></td>
                    <td class="centrar" style="font_size:11px;">{{$total_ingreso}}</td>
                    <td class="centrar" style="font_size:11px;">{{$total_ingreso_mismo}}</td>
                    <td class="centrar" style="font_size:11px;">{{$total_egreso}}</td>
                    <td class="centrar" style="font_size:11px;">{{$total_egreso_mismo}}</td>
                    <td class="centrar" style="font_size:11px;">{{$total_fallecido}}</td>
                    <td class="centrar"></td>
                </tr>
                </table>  
            @endif

            @foreach($datos as $key=>$dato)
                <table border="1" width="680" style="margin-left:45px; margin-top:50px;">
                <tr>
                    <th colspan="8" style="border:0;"></th>
                </tr>
                <tr>
                    <th class="centrar" rowspan="2" style="width: 30%; font_size:13px;">Nombre y Apellidos</th>
                    <th class="centrar" rowspan="2" style="width: 17%; font_size:13px;">Servicio</th>
                    <th class="centrar" colspan="2" style="width: 22%; font_size:13px;">Ingresos</th>
                    <th class="centrar" colspan="3" style="width: 34%; font_size:13px;">Egresos</th>
                    <th class="centrar" rowspan="2" style="width: 11%; font_size:13px;">Ingresos y egresos en el mismo día</th>
                </tr>
                <tr>
                    <td class="centrar" style="font_size:10px;">Desde fuera o de otro hospital</td>
                    <td class="centrar" style="font_size:10px;">De servicios de este mismo hospital</td>
                    <td class="centrar" style="font_size:10px;">Alta al hogar u otro hospital</td>
                    <td class="centrar" style="font_size:10px;">Traslado a otro servicio de este hospital</td>
                    <td class="centrar" style="font_size:10px;">Fallecido</td>
                </tr>
                @foreach($dato as $dat)
                    <tr>
                        <td class="centrar" style="font_size:10px;">{{$dat["nombre"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["servicio"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["desde_fuera"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["este_mismo"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["alta_otro"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["traslado"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["fallecido"]}}</td>
                        <td class="centrar" style="font_size:10px;">{{$dat["ingreso_egreso"]}}</td>
                    </tr>
                @endforeach
                @if($key +1 < count($datos))
                    </table>
                    <div style="page-break-after:always;"></div>
                @endif
                @if($key +1 == count($datos))
                    <tr>
                        <td>TOTAL</td>
                        <td class="centrar"></td>
                        <td class="centrar" style="font_size:11px;">{{$total_ingreso}}</td>
                        <td class="centrar" style="font_size:11px;">{{$total_ingreso_mismo}}</td>
                        <td class="centrar" style="font_size:11px;">{{$total_egreso}}</td>
                        <td class="centrar" style="font_size:11px;">{{$total_egreso_mismo}}</td>
                        <td class="centrar" style="font_size:11px;">{{$total_fallecido}}</td>
                        <td class="centrar"></td>
                    </tr>
                    </table>
                @endif
            @endforeach

            <!-- <tr>
                <td>TOTAL</td>
                <td class="centrar"></td>
                <td class="centrar" style="font_size:11px;">{{$total_ingreso}}</td>
                <td class="centrar" style="font_size:11px;">{{$total_ingreso_mismo}}</td>
                <td class="centrar" style="font_size:11px;">{{$total_egreso}}</td>
                <td class="centrar" style="font_size:11px;">{{$total_egreso_mismo}}</td>
                <td class="centrar" style="font_size:11px;">{{$total_fallecido}}</td>
                <td class="centrar"></td>
            </tr> -->

        <br>
        <div style="margin-left:80px; margin-right:60px;">
            <span style="font-size:11.6px;">Sólo deben registrarse los hospitalizados que ingresan y/o egresan en el día, anotando las horas y minutos en los casilleros correspondientes. El registro de la información es de la total responsabilidad de quien designe el Jefe de Servicio y debe hacerse en el mismo momento en que se produce el ingreso o egreso.</span>
        </div>

        <div style="margin-left:80px; margin-top:60px;">
            <!-- <span>Firma del encargado</span> -->
            <div style="display: inline-block; width:140px; margin-top:-16px;">
                Firma del encargado
            </div>
            <div class="box2 centrar" style="font-size:11.6px;">
                Primer Turno
            </div>
            <div class="box2 centrar" style="font-size:11.6px;">
                Segundo Turno
            </div>
            <div class="box2 centrar" style="font-size:11.6px;">
                Tercer Turno
            </div>
        </div>
</body>
</html>