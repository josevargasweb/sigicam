<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>
	<div class="row">
		<h4>SIGICAM</h4>	
	</div>

	<div class="row">
		<table id="tablaVistaLista">
			<thead>

				<tr>
					<th>Estadística</th>
					<th></th>
				</tr>

			</thead>
			<tbody>
				
				<tr>
					<td>Porcentaje Categorizacion</td>
					<td>{{ $formula1 }}%</td>
				</tr>
				<tr>
					<td>Porcentaje Sin Categorizacion</td>
					<td>{{ $formula1_resto }}%</td>
				</tr>
				
			</tbody>
		</table>
	</div>

	<div class="row">
		<table id="tablaVistaLista">
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Mes</th>
					<th>Area</th>
					<th>Servicio</th>
					<th>Sala</th>
					<th>Run</th>
					<th>DV</th>
					<th>Nombres</th>
					<th>Paterno</th>
					<th>Materno</th>
					<th>Fecha hospitalización</th>
					<th>Fecha de categorización</th>
					<th>Categorización</th>
                    <th>Egreso</th>
				</tr>
			</thead>
			<tbody>
            @foreach($resultado as $res)
				<tr>
					<td>{{$res->fecha}}</td>
					<td>{{$mes}}</td>
					<td>{{$res->area}}</td>
					<td>{{$res->servicio}}</td>
					<td>{{$res->sala}}</td>
					<td>{{$res->run}}</td>
					<td>{{$res->dv}}</td>
					<td>{{$res->nombres}}</td>
					<td>{{$res->paterno}}</td>
					<td>{{$res->materno}}</td>
					<td>{{$res->fecha_hospitalizacion}}</td>
					<td>{{$res->fecha_categorizacion}}</td>
					<td>{{$res->categorizacion}}</td>
                    <td>{{$res->egreso}}</td>
				</tr>
            @endforeach
			</tbody>
		</table>
	</div>
	
</body>
</html>