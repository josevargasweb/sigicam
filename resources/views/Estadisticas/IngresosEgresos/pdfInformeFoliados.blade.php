<!DOCTYPE HTML>
<html>
<head>
  <title>Informe Foliados</title>
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
        <label style="font-size:20px;">INFORME DE EGRESOS FOLIADOS</label>
    </div>
    <br><br>

    <div class="row">
        <div style="width:33%" class="box3"></div>
        <div style="width:25%" class="box3 letra">Inicio: <b>{{$fecha_inicio}}</b></div>
        
        <div style="width:33%" class="box3 letra">Fin: <b>{{$fecha}}</b></div>
        <div style="width:33%" class="box3"></div>
    </div>

    <div class="row">
        <div class="col-xs-12">

        <table class="table">
            <tr>
                <th style="width:5%" class="letra2">Cta Cte</th>
                <th style="width:5%" class="letra2">Folio</th>
                <th style="width:25%" class="letra2">Nombre</th>
                <th style="width:10%" class="letra2">F. Ingreso</th>
                <th style="width:10%" class="letra2">F. Egreso</th>
                <th style="width:20%" class="letra2">Servicio</th>
                <th style="width:5%" class="letra2">N° Días</th>
                <th style="width:5%" class="letra2">Condición Alta</th>
            </tr>
            

            @foreach($datos as $key=>$casos)
                <tr>
                    <td style="width:5%" class="letra2 centrar"></td>
                    <td style="width:5%" class="letra2 centrar">{{$casos["folio"]}}</td>
                    <td style="width:25%" class="letra2">{{$casos["casos"]["nombre"]}} {{$casos["casos"]["apellido_paterno"]}} {{$casos["casos"]["apellido_materno"]}}</td>
                    <td style="width:10%" class="letra2 centrar">{{Carbon\Carbon::parse($casos["casos"]["fecha_ingreso_real"])->format('d/m/Y')}}</td>
                    <td style="width:10%" class="letra2 centrar">{{Carbon\Carbon::parse($casos["casos"]["fecha_termino"])->format('d/m/Y')}}</td>
                    <td style="width:20%" class="letra2 centrar">{{$casos["casos"]["alias"]}}</td>
                    <td style="width:5%" class="letra2 centrar">{{$casos["diff"]}}</td>
                    <td style="width:5%" class="letra2 centrar">{{$casos["estadoAlta"]}}</td>
                    {{-- <td>{{$casos->dau}}</td>
                    <td>{{$casos->ficha_clinica}}</td>
                    <td>{{$casos->nombre}} {{$casos->apellido_paterno}} {{$casos->apellido_materno}}</td>
                    <td>{{Carbon\Carbon::parse($casos->fecha_ingreso_real)->format('d/m/Y')}}</td>
                    <td>{{Carbon\Carbon::parse($casos->fecha_termino)->format('d/m/Y')}}</td>
                    <td>{{$area["nDias"][$key]}}</td>
                    <td>{{$area["estadoAlta"][$key]}}</td> --}}
                </tr>
            @endforeach
        </table>

        <br><br><br>
        </div>
    </div>

    <div class="row">
    <div class="col-xs-6">Total Pacientes: {{$totalPacientes}}</div>
    <div class="col-xs-6">Total días: {{$sumaDias}} </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12" > <b>Nota:</b> Para la suma de "Total días" no se ha tomado en consideración los pacientes que se egresaron es espera de cama, ni tampoco en espera de hospitalización</div>
    </div>





</body>
</html>