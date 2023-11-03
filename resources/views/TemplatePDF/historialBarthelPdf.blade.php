<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>

    <style>
        .letra {
            font-size: 12px;
        }

        .titulo{
            font-size: large;
        }
    
        .box3{
            display: inline-block;
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
            <h4 class="box3 titulo" style="width:60%;text-align:center"><b>HISTORIAL BARTHEL</b></h4>
            <div class="box3 letra" style="width:19%;">
                <div>
                    Fecha: {{$fecha}}
                </div>
            </div>
            
        </div>

        <div class="row letra">
            <div class="col-md-6">
              <b>Nombre Paciente:</b>  {{ $infoPaciente->nombre }} {{ $infoPaciente->apellido_paterno }} {{ $infoPaciente->apellido_materno }}
            </div>
            <div class="col-md-2">
              <b>Rut:</b>  {{ $infoPaciente->rut }}-{{ ($infoPaciente->dv == 10)?'K':$infoPaciente->dv }}
            </div>
            <div class="col-md-4">
              <b>Fecha de nacimiento:</b>  {{ $infoPaciente->fecha_nacimiento }} ({{ Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) }} AÑOS)
            </div>
          </div>
{{-- 
        <div style="margin-top:10px;">
            <div class="letra">
                <b>Formulario Macdems</b>
            </div>
        </div> --}}
	
        

		<div class="row" style="margin-top:10px;">

			<table id="as" style="margin-left:20px;">
				<thead class="letra">
                    <tr>
                        <th style="width:20%">Usuario aplica</th>
                        <th style="width:20%">Fecha de aplicación</th>
                        <th style="width:20%">Total</th>
                        <th style="width:20%">Tipo</th>
					</tr>
				</thead>
				<tbody class="letra">
                    @foreach($response as $resp)
                    <tr>
                        <td>{{$resp[1]}}</td>
                        <td style="text-align: center">{{$resp[2]}}</td>
                        <td style="text-align: center">{{$resp[3]}}</td>
                        <td style="text-align: center">{{$resp[4]}}</td>
                    </tr>
                @endforeach
				</tbody>
			</table>
			
		</div>

		
	
</body>
</html>