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
            <h4 class="box3 titulo" style="width:60%;text-align:center"><b>REPORTE PACIENTES LISTA ESPERA</b></h4>
            <div class="box3 letra" style="width:19%;">
                <div>
                    Fecha: {{$fecha}}
                </div>
            </div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Pacientes Lista Espera</b>
            </div>
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Procedencia: {{$procedencia}}</b>
            </div>
        </div>
	
        

		<div class="row" style="margin-top:10px;">

			<table id="as" style="margin-left:20px;">
				<thead class="letra">
                    <tr>
                        <th>Run</th>
                        <th>Nombre Completo</th>
                        <th>Fecha Nacimiento</th>
                        <th>Diagnóstico</th>
                        <th>Fecha Solicitud</th>
                        <th>Procedencia</th>
                        <th>Área Funcional y Servicio a Cargo</th>
                        <th>Área Funcional y Servicio Destino</th>
                        <th>Comentario</th>
                        <th>N° Dau</th>
                        <th>Riesgo-Dependencia</th>
					</tr>
				</thead>
				<tbody class="letra">
                    @foreach($response as $resp)
                    <tr>
                        <td>{{$resp["rut"]}}</td>
                        <td>{{$resp["nombre_completo"]}}</td>
                        <td>{{$resp["fecha_nacimiento"]}} ({{$resp["edad"]}})</td>
                        <td>{!!$resp["diagnostico"]!!}</td>
                        <td>{{$resp["fecha_solicitud"]}}</td>
                        <td>{!!$resp["procedencia"]!!}</td>
                        <td>{{$resp["area_funcional_cargo"]}}-{{$resp["servicio_cargo"]}}</td>
                        <td>{{$resp["nombre_area_funcional"]}}-{{$resp["nombre_unidad"]}}</td>
                        <td>{{$resp["comentario_lista"]}}</td>
                        <td>{{$resp["dau"]}}</td>
                        <td>{{$resp["categorizacion"]}}</td>
                    </tr>
                @endforeach
				</tbody>
			</table>
			
		</div>

		
	
</body>
</html>