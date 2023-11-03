<!DOCTYPE HTML>
<html>
<head>
  <title>Pdf Egresos</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


  <style>

      .centrar{
          text-align:center;
      }

      .izquierda {
        text-align:left;
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

        body{
            font-size: 10px;
        }

        .letra{
            font-size: 12px;
        }
        .letra2{
            font-size: 13px;
        }

        .servicios{
            font-size: 15px;
        }



  </style>
  
</head>
<body>
    <div class="centrar">
        <label style="font-size:20px;">INFORME DE EGRESOS</label>
    </div>
    <br>

    <div class="row">
        <div style="width:33%" class="box3"></div>
        <div style="width:25%" class="box3 letra">Inicio: <b>{{$datos[0]["fecha_inicio"]}}</b></div>
        
        <div style="width:33%" class="box3 letra">Fin: <b>{{$datos[0]["fecha"]}}</b></div>
        <div style="width:33%" class="box3"></div>
    </div>

    <div class="row">
            @foreach($datos as $area)
                    <h5 class="servicios" style="margin-bottom: 0px">Servicio: {{$area["area"]}}</h5>
                    <table class="table">
                        <tr>
                            <th style="width:5%" class="letra2">Indice</th>
                            <th style="width:5%" class="letra2">Cta Cte</th>
                            <th style="width:10%" class="letra2">Ficha</th>
                            <th style="width:20%" class="letra2">Nombre</th>
                            <th style="width:5%" class="letra2">Rut</th>
                            <th style="width:10%" class="letra2">Previsión</th>
                            <th style="width:20%" class="letra2">Diagnostico</th>
                            <th style="width:5%" class="letra2">F. Ingreso</th>
                            <th style="width:5%" class="letra2">F. Egreso</th>
                            <th style="width:5%" class="letra2">N° Días</th>
                            <th style="width:5%" class="letra2">Destino Egreso</th>
                            <th style="width:5%" class="letra2">Condición Alta</th>
                        </tr>
                        

                        @foreach($area["casos"] as $key=>$casos)
                            <tr style="margin-bottom: 2px;">
                                <td style="width:5%" class="centrar letra2">{{$key+1}}</td>
                                <td style="width:5%" class="centrar letra2">{{$casos["dau"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$casos["ficha_clinica"]}}</td>
                                <td style="width:20%" class="izquierda letra2">{{$casos["nombre"]}} {{$casos["apellido_paterno"]}} {{$casos["apellido_materno"]}}</td>
                                <td style="width:5%" class="centrar letra2">{{$casos["rut"]}}-{{ ($casos["dv"] == 10) ? 'K' : $casos["dv"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$casos["prevision"]}}</td>
                                <td style="width:20%" class="izquierda letra2">{!!"<strong>(".$casos["id_cie_10"].")</strong>"!!} {{$casos["diagnostico"]}}</td>
                                <td style="width:5%" class="centrar letra2">{{Carbon\Carbon::parse($casos["fecha_ingreso_real"])->format('d/m/Y')}}</td>
                                <td style="width:5%" class="centrar letra2">{{Carbon\Carbon::parse($casos["fecha_termino"])->format('d/m/Y')}}</td>
                                <td style="width:5%" class="centrar letra2">{{$area["nDias"][$key]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$casos["motivo_termino"]}}</td>
                                <td style="width:5%" class="centrar letra2">{{$area["estadoAlta"][$key]}}</td>
                            </tr>
                        @endforeach
                    </table>
            @endforeach      
    </div>





</body>
</html>