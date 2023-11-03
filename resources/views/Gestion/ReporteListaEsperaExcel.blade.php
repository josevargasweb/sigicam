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
                        <b>Servicio de Urgencia</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha : {{$fecha}}</b>
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">

        <table id="as">
            <thead><tr>
                    <th>Nombre Paciente</th>
                    <th>RUN</th>
                    <th>Tiempo de Espera</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $dato)
                    <tr>
                        <td>{{$dato[0]}}</td>
                        <td>{{$dato[1]}}</td>
                        <td>{{$dato[2]}} hrs.</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>

	
</body>
</html>