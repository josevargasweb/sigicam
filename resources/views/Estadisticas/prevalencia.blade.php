<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
</head>
<body>
    
	<div class="row">
        <h4>SIGICAM- ESTUDIO DE PREVALENCIA</h4>
        
    </div>
    
    <br>
	<div class="row">
		<table id="tablaVistaLista" class="table  table-condensed table-collapse">
			<thead>
                <tr style="background:#399865; color: cornsilk;">
                    <th style="text-align:center;">Fecha de hospitalización</th>
                    <th style="text-align:center;">Días camas acumulados</th>
                    <th style="text-align:center;">Rut</th>
                    <th style="text-align:center;">Nombre</th>
                    <th style="text-align:center;">Edad</th>
                    <th style="text-align:center;">Género</th>
                    <th style="text-align:center;">N° de historia clinica</th>
                    <th style="text-align:center;">N° de cama</th>
                    <th style="text-align:center;">Sala</th>
                    <th style="text-align:center;">Servicio clínico</th>
                    <th style="text-align:center;">Fecha egreso ultima hospitalización</th>
                </tr>
			</thead>
			<tbody>
                @foreach($datos as $dato)
                    <tr>
                        <td style="text-align: center;">{{ $dato->hospitalizacion }}</td>
                        <td style="text-align: center;">{{ $dato->dias_acumulado }} días</td>
                        @if($dato->rutcompleto == "No posee")
                            <td style="text-align: center;color:red;">{{ $dato->rutcompleto }}</td>
                        @else
                            <td style="text-align: center;">{{ $dato->rutcompleto }}</td>
                        @endif
                        <td style="text-align: left;">{{ $dato->nombre }} {{ $dato->apellido_paterno }} {{ $dato->apellido_materno }}</td>
                        <td style="text-align: center;">{{ $dato->edad }} años</td>
                        <td style="text-align: center;">{{ ucwords($dato->sexo) }}</td>
                        <td style="text-align: center;">{{ $dato->ficha_clinica }}</td>
                        <td style="text-align: center;">{{ $dato->id_cama }}</td>
                        <td style="text-align: center;">{{ $dato->nombre_sala }}</td>
                        <td style="text-align: center;">{{ $dato->nombre_servicio }}</td>
                        <td style="text-align: center;">{{ $dato->ultimpo_egreso }}</td>
                    </tr>
                @endforeach
                
			</tbody>
		</table>
	</div>
	
</body>
</html>