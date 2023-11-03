<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE PACIENTES EN LISTA DE ESPERA</h4>	
    </div>
    
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Lista de Espera</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Procedencia: {{$procedencia}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha: {{\Carbon\Carbon::parse($hoy)->format('d/m/Y H:i')}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Establecimiento: {{ $establecimiento->nombre }}</b>
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">
        <table id="as">
            <thead>
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
            <tbody>
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