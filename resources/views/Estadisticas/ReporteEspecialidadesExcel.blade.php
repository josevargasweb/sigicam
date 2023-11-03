<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE ESPECIALIDADES</h4>	
	</div>

	
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Establecimiento: {{ $establecimiento->nombre }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha Actual : {{\Carbon\Carbon::parse($hoy)->format('d/m/Y H:i')}}</b>
                    </td>
                </tr>
                @if($nombreMes != null && $anno != null)
                <tr>
                    <td>
                        <b>Datos de : {{$nombreMes ." del ". $anno}}</b>
                    </td>
                </tr>
                @endif
            </thead>
        </table>
    </div>
    
    <br>
    <div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                    <b>Información de Especialidades</b>
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">

        <table id="as">
            <thead>
                <tr>
                    <th>Indicación</th>
                    <th>Restricción</th>
                    <th>Medicina</th>
                    <th>Cirugia</th>
                    <th>Traumatologia</th>
                    <th>Neurologia</th>
                    <th>Urologia</th>
                    <th>Neurocirugia</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3" style="vertical-align: middle">Días de Hospitalizados</td>
                    <td>1 a 3 días</td>
                    <td>{{$datos['info_categorias'][1][1]}}</td>
                    <td>{{$datos['info_categorias'][1][2]}}</td>
                    <td>{{$datos['info_categorias'][1][3]}}</td>
                    <td>{{$datos['info_categorias'][1][4]}}</td>                    
                    <td>{{$datos['info_categorias'][1][5]}}</td>
                    <td>{{$datos['info_categorias'][1][6]}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>4 a 12 días</td>
                    <td>{{$datos["info_categorias"][2][1]}}</td>
                    <td>{{$datos["info_categorias"][2][2]}}</td>
                    <td>{{$datos["info_categorias"][2][3]}}</td>
                    <td>{{$datos["info_categorias"][2][4]}}</td>                    
                    <td>{{$datos["info_categorias"][2][5]}}</td>
                    <td>{{$datos["info_categorias"][2][6]}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>más 12 días</td>
                    <td>{{$datos['info_categorias'][3][1]}}</td>
                    <td>{{$datos['info_categorias'][3][2]}}</td>
                    <td>{{$datos['info_categorias'][3][3]}}</td>
                    <td>{{$datos['info_categorias'][3][4]}}</td>                    
                    <td>{{$datos['info_categorias'][3][5]}}</td>
                    <td>{{$datos['info_categorias'][3][6]}}</td>
                </tr>
                <tr>
                    <td colspan=2>Total Hospitalizados</td>
                    <td>{{$datos['total_especialidades'][1]}}</td>
                    <td>{{$datos['total_especialidades'][2]}}</td>
                    <td>{{$datos['total_especialidades'][3]}}</td>
                    <td>{{$datos['total_especialidades'][4]}}</td>
                    <td>{{$datos['total_especialidades'][5]}}</td>
                    <td>{{$datos['total_especialidades'][6]}}</td>
                </tr>
                <tr>
                    <td colspan=2>Promedio de estancia hospitalaria</td>
                    <td>{{ ($datos['total_dias'][1] != 0) ? round($datos['total_dias'][1] / $datos['total_especialidades'][1],0) : '0' }}</td>
                    <td>{{ ($datos['total_dias'][2] != 0) ? round($datos['total_dias'][2] / $datos['total_especialidades'][2],0) : '0' }}</td>
                    <td>{{ ($datos['total_dias'][3] != 0) ? round($datos['total_dias'][3] / $datos['total_especialidades'][3],0) : '0' }}</td>
                    <td>{{ ($datos['total_dias'][4] != 0) ? round($datos['total_dias'][4] / $datos['total_especialidades'][4],0) : '0' }}</td>
                    <td>{{ ($datos['total_dias'][5] != 0) ? round($datos['total_dias'][5] / $datos['total_especialidades'][5],0) : '0' }}</td>
                    <td>{{ ($datos['total_dias'][6] != 0) ? round($datos['total_dias'][6] / $datos['total_especialidades'][6],0) : '0' }}</td>
                </tr>
            </tbody>
        </table>
        
    </div>
</body>
</html>