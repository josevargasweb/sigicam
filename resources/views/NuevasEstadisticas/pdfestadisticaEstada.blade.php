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
    <div class="centrar">
        <label style="font-size:20px;">INFORME PACIENTES LARGA ESTADIA</label>
    </div>
    <br>

    <div class="row">
            @foreach($datos as $area)
                    <h5 class="servicios" style="margin-bottom: 0px">Servicio: {{ $area["area"] }}</h5>
                    <br>
                    <table class="table">
                        <tr>
                            <th style="width:5%" class="letra2">Indice</th>
                            <th style="width:10%" class="letra2">Sala</th>
                            <th style="width:10%" class="letra2">Cama</th>
                            <th style="width:10%" class="letra2">Run</th>
                            <th style="width:30%" class="letra2">Nombre</th>
                            <th style="width:15%" class="letra2">Diagnóstico</th>
                            <th style="width:10%" class="letra2">F. Ingreso</th>
                            <th style="width:10%" class="letra2">N° Días</th>
                            <th style="width:10%" class="letra2">Observaciones</th>
                        </tr>

                        @foreach($area["casos"] as $key=>$paciente)
                            <tr>
                                <td style="width:5%" class="centrar letra2">{{$key+1}}</td>
                                <td style="width:10%" class="centrar letra2">{{$paciente["sala"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$paciente["id_cama"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$paciente["rut"]}}</td>
                                <td style="width:30%" class="letra2">{{$paciente["nombre"]}} {{$paciente["apellido"]}}</td>
                                <td style="width:15%; text-align:left;" class="letra2">{{$paciente["diagnostico"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$paciente["fecha"]}}</td>
                                <td style="width:10%" class="centrar letra2">{{$paciente["dias"]}}</td>
                                <td style="width:10%" class="centrar letra2"></td>
                            </tr>
                          @endforeach
                    </table>
            @endforeach


    </div>





</body>
</html>
