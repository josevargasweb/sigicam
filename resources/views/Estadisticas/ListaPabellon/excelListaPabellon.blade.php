<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE PACIENTES</h4>	
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
            </thead>
        </table>
    </div>
    
    <br>
    <div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                    <b>Pacientes En lista de Pabellón</b>
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">
        <table class="table">
            <thead>
                <tr>
                    <th>Run</th>
                    <th>Nombre Completo</th>
                    <th>Diagnóstico</th>
                    <th>Fecha de Ingreso</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($response as $key => $p)
                    <tr>
                        <td>{{$p["rut"]}}</td>
                        <td>{{strtoupper($p["nombre_completo"])}}</td>
                        <td>{!!$p["diagnostico"]!!}</td>
                        <td>{{$p["fecha_ingreso"]}}</td>
                        <td>{{$p["comentario"]}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table> 
    </div>
</body>
</html>