<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - REPORTE DE CAMAS BLOQUEADAS/h4>	
    </div>
    
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Lista de Camas Bloqueadas</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha : {{\Carbon\Carbon::parse($hoy)->format('d/m/Y H:i')}}</b>
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
            <tbody>
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