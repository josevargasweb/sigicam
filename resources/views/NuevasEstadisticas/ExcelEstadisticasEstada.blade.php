<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>INFORME PACIENTES LARGA ESTADIA</h4>	
	</div>

	
	<div class="row">
        <table>
            <thead>
                <tr>
                    <td>
                        <b>Establecimiento: {{ $hospital }}</b>
                    </td>
                    <tr>
                        <td>
                            <b>Fecha: {{ $fecha }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Estadía superior a los {{$dias}} días hospitalizados.</b>
                        </td>
                    </tr>
                    
                </tr>
            </thead>
        </table>
    </div>
    <br>

    <div class="row">
        @foreach($datos as $area)
        <h5 class="servicios" style="margin-bottom: 0px">Servicio: {{$area["area"]}}</h5>
        <table id="as">
                <tr>
                    <th>Indice</th>
                    <th>Sala</th>
                    <th>Cama</th>
                    <th>Run</th>
                    <th>Nombre</th>
                    <th>Diagnóstico</th>
                    <th>F. Ingreso</th>
                    <th>N° Días</th>
                    <th>Observaciones</th>
                </tr>
                @foreach($area["casos"] as $key=>$paciente)
                <tr>
                    <td style="text-align: left">{{$key+1}}</td>
                    <td style="text-align: left">{{$paciente["sala"]}}</td>
                    <td>{{$paciente["id_cama"]}}</td>
                    <td>{{$paciente["rut"]}}</td>
                    <td>{{$paciente["nombre"]}} {{$paciente["apellido"]}}</td>
                    <td>{{$paciente["diagnostico"]}}</td>
                    <td>{{$paciente["fecha"]}}</td>
                    <td>{{$paciente["dias"]}}</td>
                    <td></td>
                </tr>
                @endforeach
        </table>
        @endforeach 
    </div>
</body>
</html>