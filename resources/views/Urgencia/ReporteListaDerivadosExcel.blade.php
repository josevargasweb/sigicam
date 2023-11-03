<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE PACIENTES EN Lista de Derivados</h4>	
	</div>

	
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Lista de Derivación</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha : {{\Carbon\Carbon::parse($hoy)->format('d/m/Y H:i')}}</b>
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
                    <th>Fecha de nacimiento</th>
                    <th>Fecha de Ingreso</th>
                    <th>Procedencia</th>
                    <th>Diagnóstico</th>
                    <th>Unidad</th>
                    <th>Cama</th>
                    <th>Ultimo Comentario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($response as $resp)
                    <tr>
                        <td>{{$resp["nombre_completo"]}}</td>
                        <td>{{$resp["rut"]}}</td>
                        <td>{{$resp["fecha_nacimiento_excel"]}}</td>
                        <td>{{$resp["fecha_ingreso_excel"]}}</td>
                        <td>{{$resp["procedencia"]}}</td>
                        <td>{!!$resp["diagCom"]!!}</td>
                        <td>{{$resp["servicio"]}}</td>
                        <td>{{$resp["cama"]}}</td>
                        <td>{{$resp["ultimo_comentario"]}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>

	
</body>
</html>