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
            <div class="box3 titulo" style="width:60%;text-align:center"><b>INFORME DE CENSO DE CAMAS</b></div>
            <div class="box3 letra" style="width:19%;">
                <div>
                    {{$fecha}}
                </div>
            </div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Servicio</b> <b>{{$unidad}}</b>
            </div>
            <div class="letra">
                <b>Unidad</b> <b>{{$area}}</b>
            </div>
        </div>
	
        

		<div class="row" style="margin-top:10px;">

			<table id="as" style="margin-left:20px;">
				<thead class="letra">
                    <tr>
						<th style="background-color:#F6F6F6;">Pieza</th>
						<th>Cama</th>
						<th>Ficha</th>
						<th>RUN</th>
						<th>Nombre Paciente</th>
						<th>Estado Médico</th>
                        <th>Médico Tratante</th>
                        <th>Diagnóstico</th>
                        <th>Última Categorización</th>
                        <th>Estado paciente</th>
                        <th>Acompañamiento</th>
					</tr>
				</thead>
				<tbody class="letra">

                    @foreach( $camas_censo as $paciente)
                        <tr>
                            <td>{{$paciente[0]}}</td>
                            <td>{{$paciente[1]}}</td>
                            <td>{{$paciente[2]}}</td>
                            <td>{{$paciente[3]}}</td>
                            <td>{{$paciente[4]}}</td>
                            <td>{{$paciente[5]}}</td>
                            <td>{{$paciente[6]}}</td>
                            <td>{{$paciente[7]}}</td>
                            <td style="text-align: center">{{$paciente[8]}}</td>
                            <td>{{$paciente[9]}}</td>
                            <td>{{$paciente[10]}}</td>
                        </tr>
                    @endforeach
                    
				</tbody>
			</table>
			
		</div>

		<div class="row" style="margin-top:20px;">
			<table id="tablaVistaLista" class="letra" >
				<thead>

					<tr>
						<th>Estadística</th>
						<th></th>
						<th colspan="1">Disponibles</th>
						<th colspan="1">No disponibles</th>
					</tr>
					
					<tr>
						<th></th>
						<th>Total camas</th>
						<th>Camas</th>						
						{{-- <th>Camas Extras</th>	 --}}					
						<th>Uso Paciente</th>						
						{{-- <th>Otras Causas</th>						
						<th>Camas Extras</th>	 --}}					
					</tr>
				</thead>
				<tbody>
					
					<tr>
						<td>Servicio</td>
						<td>{{$camas_resumen[0]}}</td>
						<td>{{$camas_resumen[1]}}</td>
						<td>{{$camas_resumen[2]}}</td>
					</tr>

					<tr>
						<td>Hospital</td>
						<td>{{$camas_resumen[3]}}</td>
						<td>{{$camas_resumen[4]}}</td>
						<td>{{$camas_resumen[5]}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	
</body>
</html>