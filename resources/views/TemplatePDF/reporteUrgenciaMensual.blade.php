<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>

    <style>

        .centrar{
            text-align:center;
        }
    
        .mover{
            margin-left:100px !important;
        }
    
        .letra {
            font-size: x-small;
        }

        .titulo{
            font-size: large;
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


        <div style="margin-top:10px;">
            <div class="box3 letra" style="width:20%;">
                <div>
                    {{$hospital}}
                </div> 
                <div>
                    Atención Cerrada
                </div>
            </div>
            <div class="box3 titulo" style="width:60%;text-align:center"><b>REPORTE DE URGENCIA</b></div>
            <div class="box3 letra" style="width:19%;">
                <div>
                    {{$fecha}}
                </div>
            </div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Servicio de Urgencia</b>
            </div>
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Pacientes sobre 12 horas en Urgencia:</b>  {{ $porcentaje }}%
            </div>
            <div class="letra">
                <b>Pacientes bajo 12 horas en Urgencia:</b>  {{ $porcentaje_resto }}%
            </div>
        </div>
	
        

		<div class="row" style="margin-top:10px;">

			<table id="as" style="margin-left:20px;">
				<thead class="letra">
                    <tr>
                        <th>Nombre paciente</th>
						<th>RUN</th>						
                        <th>Diagnostico</th>
                        <th>Fecha de ingreso</th>
                        <th>Fecha de salida</th>
                        <th>Estadia</th>
                        <th>Destino</th>
					</tr>
				</thead>
				<tbody class="letra">

                    @foreach( $informacion as $info)
                        @if($info["estadia"] >= 12)
                            <tr style="color: red; font-weight: bold;">
                        @else 
                            <tr>
                        @endif
                            <td>{{strtoupper($info["nombrePaciente"])}}</td>
                            <td>{{$info["rut"]}}</td>
                            <td>{{$info["diagnostico"]}}</td>
                            <td>{{$info["fechaIngreso"]}}</td>
                            <td>{{$info["fechaSalida"]}}</td>
                            <td>{{$info["estadia"]}} hrs</td>
                            <td>{{$info["destino"]}}</td>
                        </tr>
                    @endforeach
                    
				</tbody>
			</table>
			
		</div>

		
	
</body>
</html>