<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
</head>
<body>
    
	<div class="row">
        <h4>SIGICAM</h4>
        
    </div>
    
    <br>
	<div class="row">
		<table id="tablaVistaLista" class="table  table-condensed table-collapse">
			<thead>
                <tr style="background:#399865;">
                    <th rowspan="2" style="text-align:center; border: 1px solid black;">Servicios Clinicos</th>
                    <th rowspan="2" style="text-align:center; border: 1px solid black;">Existencias</th>
                    <th colspan="2" style="text-align:center; border: 1px solid black;">CAMAS</th>
                    <th colspan="3" style="text-align:center; border: 1px solid black;">DÍAS CAMAS</th>
                    <th colspan="2" style="text-align:center; border: 1px solid black;">DÍAS ESTADA</th>
                    <th colspan="5" style="text-align:center; border: 1px solid black;">INDICADORES</th>
                </tr>
                <tr style="background:#399865; color: cornsilk;">
                    <th>Servicios Clinicos</th>
                    <th>Existencias</th>
                    <th>Dotación</th>
                    <th>Promedio Disponible</th>
                    <th>Disponible</th>
                    <th>Ocupados</th>
                    <th>Ocupados Beneficiarios</th>
                    <th>Total</th>
                    <th>Beneficiarios</th>
                    <th>Indice de sustitución</th>
                    <th>Porcentaje Ocupacional</th>
                    <th>Promedio Días Estada</th>
                    <th>Indice de Rotación</th>
                    <th>% Letalidad</th> 
                </tr>
			</thead>
			<tbody>
                @foreach($html["informacion"] as $info)
                    <tr>
                        <td>{{$info[0]}}</td>
                        <td>{{$info[1]}}</td>
                        <td>{{$info[2]}}</td>
                        <td>{{$info[3]}}</td>
                        <td>{{$info[4]}}</td>
                        <td>{{$info[5]}}</td>
                        <td>{{$info[6]}}</td>
                        <td>{{$info[7]}}</td>
                        <td>{{$info[8]}}</td>
                        <td>{{$info[9]}}</td>
                        <td>{{$info[10]}}</td>
                        <td>{{$info[11]}}</td>
                        <td>{{$info[12]}}</td>
                        <td>{{$info[13]}}</td>
                    </tr>
                    

                @endforeach
                
			</tbody>
		</table>
	</div>
	
</body>
</html>