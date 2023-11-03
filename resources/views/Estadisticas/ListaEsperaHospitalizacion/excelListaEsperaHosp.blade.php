<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE PACIENTES EN LISTA DE ESPERA DE HOSPITALIZCIÓN</h4>	
    </div>
    
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Lista de Espera Hospitalización</b>
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
                    <th>Diagnóstico</th>
                    <th>Fecha de Asignación</th>
                    <th>Fecha de indicación médica</th>
                    <th>Servicio</th>
                    <th>Cama</th>
                </tr>   
            </thead>
            <tbody>
                @foreach($response as $resp)
                    <tr>
                        <td>{{$resp[1]}}</td>
                        <td>{{$resp[2]}}</td>
                        <td>{{$resp[3]}}</td>
                        <td>{{$resp[8]}}</td>
                        <td>{{$resp[5]}}</td>
                        <td>{{$resp[6]}}</td>
                        <td>{{$resp[7]}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>