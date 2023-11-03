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
            <h4 class="box3 titulo" style="width:60%;text-align:center"><b>REPORTE CAMAS BLOQUEADAS</b></h4>
            <div class="box3 letra" style="width:19%;">
                <div>
                    Fecha: {{$fecha}}
                </div>
            </div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra">
                <b>Lista Camas Bloqueadas</b>
            </div>
        </div>
	
        

		<div class="row" style="margin-top:10px;">

			<table id="as" style="margin-left:20px;">
				<thead class="letra">
                    <tr>
                        <th>Estado</th>
                        <th>Nombre cama</th>
                        <th>Nombre sala</th>
                        <th>Nombre unidad</th>
                        <th>Nombre area funcional</th>
                        <th>Fecha de bloqueo</th>
                        <th>Días de bloqueo</th>
                        <th>Comentario bloqueo</th>
                        <th>Fecha de habilitacion</th>
                        <th>Comentario de habilitacion</th>
					</tr>
				</thead>
				<tbody class="letra">
                    @foreach($response as $resp)
                    <tr>
                        <td>{!!$resp[0]!!}</td>
                        <td>{{$resp[1]}}</td>
                        <td>{{$resp[2]}}</td>
                        <td>{{$resp[3]}}</td>
                        <td>{{$resp[4]}}</td>
                        <td>{!!$resp[5]!!}</td>
                        <td>{{$resp[6]}}</td>
                        <td>{{$resp[7]}}</td>
                        <td>{{$resp[8]}}</td>
                        <td>{{$resp[9]}}</td>
                    </tr>
                @endforeach
				</tbody>
			</table>
			
		</div>

		
	
</body>
</html>