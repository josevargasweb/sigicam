<!DOCTYPE HTML>
<html>
<head>
  <title>Título de la página</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


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
    <div style="margin-top:10px;">
            <div class="box3 letra" style="width:20%;">
                <div>
                    {{$datos[0]["establecimiento"]}}
                </div> 
            </div>
            <div class="box3 titulo" style="width:60%;text-align:center"><b>REPORTE PACIENTES EN ESPERA DE PABELLÓN</b></div>
            <div class="box3 letra" style="width:19%;">
                <div>
                Fecha: {{$datos[0]["fecha"]}}
                </div>
            </div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Lista Pabellón por servicio</b>
            </div>
        </div>

    <div class="row">
            @foreach($datos as $area)
                    <h5 class="servicios" style="margin-bottom: 0px">Servicio: {{$area["area"]}} ({{$area["nombre_unidad"]}})</h5>
                    <table class="table">
                        <tr>
                            <th style="width:5%" class="letra2">Indice</th>
                            <th style="width:5%" class="letra2">Run</th>
                            <th style="width:10%" class="letra2">Nombre Completo</th>
                            <th style="width:30%" class="letra2">Diagnóstico</th>
                            <th style="width:10%" class="letra2">Fecha de Ingreso</th>
                            <th style="width:10%" class="letra2">Comentario</th>
                        </tr>
                        

                        @foreach($area["pacientes"] as $key=>$p)
                            <tr>
                                <td style="width:5%" class="centrar letra2">{{$key+1}}</td>
                                <td style="width:5%" class="centrar letra2">{{$p["rut"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$p["nombre_completo"]}}</td>
                                <td style="width:30%" class="letra2">{!!$p["diagnostico"]!!}</td>
                                <td style="width:10%" class="centrar letra2">{{$p["fecha_ingreso"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$p["comentario"]}}</td>
                            </tr>
                        @endforeach
                    </table>
            @endforeach      
    </div>





</body>
</html>