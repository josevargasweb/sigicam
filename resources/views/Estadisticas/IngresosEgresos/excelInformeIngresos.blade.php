<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - INFORME DE INGRESOS</h4>	
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
                        <b>Fecha Inicio: {{ $inicio }} </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha Fin: {{ $fin }} </b>
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
                    {{-- <b>Lista de pacientes Turno de {{$horarioMañana}}</b> --}}
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">
        <table >
                <tr>
                    <th>Indice</th>
                    <th>RUN</th>
                    <th>Nombre y Apellidos</th>
                    <th>Diagnostico</th>
                    <th>Comentario</th>
                    <th>F. Solicitud</th>
                    <th>F. Asignación</th>
                    <th>F. Hospitalización</th>
                </tr>
                @foreach($datos as $key => $caso)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$caso["run"]}}</td>
                    <td>{{$caso["nombre"]}}</td>
                    <td>{{$caso["diagnostico"]}}</td>
                    <td>{{$caso["diagnostico_comentario"]}}</td>
                    <td>{{$caso["fecha_solicitud"]}}</td>
                    <td>{{$caso["fecha_asignacion"]}}</td>
                    <td>{{$caso["fecha_hospitalizacion"]}}</td>
                </tr>
                @endforeach
        </table>
    </div>
</body>
</html>