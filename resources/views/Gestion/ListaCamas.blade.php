<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - INFORME DE TODAS LAS UNIDADES</h4>	
	</div>

	<table>
		<thead>
			<tr>
				<td>
					<b>Fecha</b>
				</td>
				<td>
					<b>{{ \Carbon\Carbon::now()->format("d-m-Y H:i") }}</b>
				</td>
			</tr>
		</thead>
	</table>

	@if(strtoupper($area) != "TODAS")
		<table>
			<thead>
				<tr>
					<td>
						<b>Servicio</b>
					</td>
					<td>
						<b>{{strtoupper($area)}}</b>
					</td>
				</tr>
				<tr>
					<td>
						<b>Unidad</b>
					</td>
					<td>
						<b>{{strtoupper($unidad)}}</b>
					</td>
				</tr>
			</thead>
		</table>
	@endif
	
	@if(!empty($camas_resumen) )

		<div class="row">

			<table id="as">
				<thead><tr>
						<th style="background-color:#F6F6F6;">Pieza</th>
						<th>Cama</th>
						<th>Ficha</th>
						<th>RUN</th>
						<th>Nombre Paciente</th>
						<th>Estado Médico</th>
						<th>Médico Tratante</th>
						<th>Diagnóstico</th>
						<th>Última Categorización</th>
						<th>Estado paciente</th>
						<th>Acompañamiento</th>
					</tr>
				</thead>
				<tbody>
					@foreach($camas_censo as $cama_censo)
						<tr>
							<td>{{$cama_censo[0]}}</td>
							<td>{{$cama_censo[1]}}</td>
							<td>{{$cama_censo[2]}}</td>
							<td>{{$cama_censo[3]}}</td>
							<td>{{$cama_censo[4]}}</td>
							<td>{{$cama_censo[5]}}</td>
							<td>{{$cama_censo[6]}}</td>
							<td>{{$cama_censo[7]}}</td>
							<td>{{$cama_censo[8]}}</td>
							<td>{{$cama_censo[9]}}</td>
							<td>{{$cama_censo[10]}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			
		</div>

		<div class="row">
			<table id="tablaVistaLista">
				<thead>

					<tr>
						<th>Estadística</th>
						<th></th>
						<th colspan="1">Disponibles</th>
						<th colspan="1">No disponibles</th>
					</tr>
					
					<tr>
						<th></th>
						<th>Total camas</th>
						<th>Camas</th>						
						{{-- <th>Camas Extras</th>	 --}}					
						<th>Uso Paciente</th>						
						{{-- <th>Otras Causas</th>						
						<th>Camas Extras</th>	 --}}					
					</tr>
				</thead>
				<tbody>
					
					<tr>
						<td>Servicio</td>
						<td>{{$camas_resumen[0]}}</td>
						<td>{{$camas_resumen[1]}}</td>
						<td>{{$camas_resumen[2]}}</td>
					</tr>

					<tr>
						<td>Hospital</td>
						<td>{{$camas_resumen[3]}}</td>
						<td>{{$camas_resumen[4]}}</td>
						<td>{{$camas_resumen[5]}}</td>
					</tr>
				</tbody>
			</table>
		</div>

	@else 
		<div class="row">
			<table id="tablaVistaLista">
				<thead>
					<tr>
						<th>Servicio</th>
						<th>Sala</th>
						<th>Cama</th>
						<th>Tipo</th>
						<th>Tipo Etario</th>
						<th>Diagnóstico CIE-10</th>
						<th>Código CIE-10</th>
						<th>Diagnóstico Clínico</th>
						<th>Paciente</th>
						<th>Edad</th>
						<th>Run</th>
						<th>Primera categorización</th>
						<th>Última categorización</th>
						<th>Fecha de solicitud</th>
						<th>Fecha de asignación</th>
						<th>Fecha de hospitalización</th>
						<th>Estado</th>
					</tr>
				</thead>
				<tbody>
					@foreach($camas as $cama)
					<tr>
						<td>{{$cama[0]}}</td>
						<td>{{$cama[1]}}</td>
						<td>{{$cama[2]}}</td>
						<td>{{$cama[3]}}</td>
						<td>{{$cama[22]}}</td>
						<td>{{$cama[4]}}</td>
						<td>{{$cama[21]}}</td>
						<td>{{$cama[20]}}</td>
						<td>{{$cama[5]}}</td>
						<td>{{$cama[13]}}</td>
						<td>{{$cama[6]}}</td>
						<td>{{$cama[7]}}</td>
						<td>{{$cama[15]}}</td>
						<td>{{$cama[8]}}</td>
						<td>{{$cama[16]}}</td>
						<td>{{$cama[17]}}</td>
						<td>{{$cama[9]}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	@endif
	
</body>
</html>