<!DOCTYPE HTML>
<html>
<head>
  <title>Título de la página</title>
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
    
    <div style="margin-top:10px;">
        <div class="box3 letra" style="width:20%;">
            <div>
                {{$establecimiento->nombre}}
            </div> 
        </div>
        <h4 class="box3 titulo" style="width:60%;text-align:center"><b>DÍAS SIN CATEGORIZAR</b></h4>
        <div class="box3 letra" style="width:19%;">
            <div>
                Fecha: {{Carbon\Carbon::now()->format('d-m-Y H:i:s')}}
            </div>
        </div>
        
    </div>

    <div class="row">
        <table style="margin-left: 20px;">
            <thead class="letra">
                <h4>NOMBRE: {{$paciente->nombre}} {{$paciente->apellido_paterno}} {{$paciente->apellido_materno}}</h4>
                <h4>RUT: {{$paciente->rut}}-{{$paciente->dv}}</h4>
                <tr>
                    <th style="width:30%" class="letra2">Indice</th>
                    <th style="width:80%" class="letra2">Fecha sin Categorizar</th>
                </tr>
            </thead>
            <tbody class="letra">
                @foreach($datos as $key => $nocat)
                    <tr style="margin-bottom: 2px;">
                        <td style="width:30%" class="centrar letra2">{{$key+1}}</td>
                        <td style="width:80%" class="centrar letra2">{{Carbon\Carbon::parse($nocat->date_trunc)->format('d/m/Y')}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table> 
    </div>





</body>
</html>