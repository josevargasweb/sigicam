<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - INFORME DE EGRESOS</h4>	
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
                        <b>Fecha Inicio: {{$datos[0]["fecha_inicio"]}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Fecha Fin: {{$datos[0]["fecha"]}}</b>
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
        @foreach($datos as $area)
        <h5 class="servicios" style="margin-bottom: 0px">Servicio: {{$area["area"]}}</h5>
        <table id="as">
                <tr>
                    {{-- <th>Indice</th> --}}
                    {{-- <th>Cta Cte</th> --}}
                    {{-- <th>Ficha</th> --}}
                    <th>Nombre</th>
                    <th>Rut</th>
                    <th>Previsión</th>
                    <th>Diagnostico</th>
                    <th>Comentario</th>
                    <th>F. Ingreso</th>
                    <th>F. Egreso</th>
                    <th>N° Días</th>
                    <th>Destino Egreso</th>
                    <th>Condición Alta</th>
                </tr>
                @foreach($area["casos"] as $key=>$casos)
                <tr>
                    {{-- <td>{{$key+1}}</td> --}}
                    {{-- <td>{{$casos["dau"]}}</td> --}}
                    {{-- <td>{{$casos["ficha_clinica"]}}</td> --}}
                    <td>{{$casos["nombre"]}} {{$casos["apellido_paterno"]}} {{$casos["apellido_materno"]}}</td>
                    <td>{{$casos["rut"]}}-{{ ($casos["dv"] == 10) ? 'K' : $casos["dv"]}}</td>
                    <td>{{$casos["prevision"]}}</td>
                    <td>{{"(".$casos["id_cie_10"].")"}} {{$casos["diagnostico"]}}</td>
                    <td>{{$casos["comentario"]}}</td>
                    <td>{{Carbon\Carbon::parse($casos["fecha_ingreso_real"])->format('d/m/Y')}}</td>
                    <td>{{Carbon\Carbon::parse($casos["fecha_termino"])->format('d/m/Y')}}</td>
                    <td>{{$area["nDias"][$key]}}</td>
                    <td>{{$casos["motivo_termino"]}}</td>
                    <td>{{$area["estadoAlta"][$key]}}</td>
                </tr>
                @endforeach
        </table>
        @endforeach 
    </div>
</body>
</html>