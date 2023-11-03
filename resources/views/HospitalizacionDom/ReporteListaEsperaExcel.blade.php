<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE PACIENTES EN HOSPITALIZACIÓN DOMICILIARIA</h4>	
	</div>

	
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Hospitalización domiciliaria</b>
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
                    <th>Diagnostico</th>
                    <th>Fecha de Ingreso</th>
                    <th>Días de Hospitalización</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $dato)
                    <tr>
                        <td>{{$dato[0]}}</td>
                        <td>{{$dato[1]}}</td>
                        <td>{{$dato[2]}}</td>
                        <td>{{$dato[3]}}</td>
                        <td>{{$dato[4]}} Días</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>

	
</body>
</html>