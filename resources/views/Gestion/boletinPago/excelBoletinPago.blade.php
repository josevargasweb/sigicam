<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - NOTA DE CARGO DE PACIENTE HOSPITALIZADO</h4>	
	</div>

	<table>
		<thead>
			<tr>
				<td>
					<b>{{$info["infoGeneral"]["nombreEstablecimiento"]}}</b>
				</td>
			</tr>
			
		</thead>
	</table>
	{{-- Informacion de sala y mes consulta --}}
	<table>
		<thead>
			<tr>
				<td>UNIDAD:</td>
				<td colspan="2"><b>{{$info["infoGeneral"]["nombreUnidad"]}}</b></td>
				<td>SALA:</td>
				<td colspan="2"><b>{{$info["infoGeneral"]["nombreSala"]}}</b></td>
				<td>MES:</td>
				<td colspan="2"><b>{{$info["fecha"][0]}}</b></td>
				<td>AÑO:</td>
				<td colspan="2"><b>{{$info["fecha"][1]}}</b></td>
			</tr>
		</thead>
	</table>
	{{-- Informacion paciente --}}
	<table>
		<thead>
			<tr>
				<td>NOMBRE PACIENTE:</td>
				<td colspan="5"><b>{{$info["paciente"]["nombre"]}}</b></td>
				<td>R.U.T.:</td>
				<td colspan="2"><b>{{$info["paciente"]["rut"]}}</b></td>
				<td>PREVISIÓN:</td>
				<td colspan="2"><b>{{$info["paciente"]["prevision"]}}</b></td>
			</tr>
		</thead>
	</table>

	{{-- Infomracion hospitalizacion y consulta --}}
	<table>
		<thead>
			<tr>
				<td>HOSPITALIZACIÓN:</td>
				<td colspan="5"><b>{{$info["infoGeneral"]["fechaIngreso"]}}</b></td>
				<td>CONSULTA:</td>
				<td colspan="2"><b>{{$info["fechaActual"]}}</b></td>
			</tr>
		</thead>
	</table>


	<div hidden>
		{{ $key_s = 3 }}
		{{ $key_m = 3 }}
		{{ $key_i = 3 }}
	</div>

	<table  style="border: red 5px solid;">
		<thead>
			<tr style="background-color: #1E9966">
				<td rowspan="2" style="color: #FFFFFF" align="center"><b>INSUMOS</b></td>					
				<td rowspan="2" style="color: #FFFFFF"><b>CÓDIGO</b></td>
				@if(count($info["fechas"]) > 0)
					<td colspan="{{ count($info['fechas']) }}" align="center" style="color: #FFFFFF"><b>DÍAS</b></td>
				@endif
				<td rowspan="2" style="color: #FFFFFF"><b>CANT.</b></td>
				<td rowspan="2" style="color: #FFFFFF"><b>VALOR</b></td>
			</tr> 
			
			<tr style="background-color: #1E9966">
				<td></td>
				<td></td>
				@for( $key = 0; $key < count($info['fechas']);$key++)
					<td style="color: #FFFFFF" align="center" >{{ $info['fechas'][$key] }}</td>
				@endfor  
				
			</tr>   
			             
		</thead>
		
		<tbody>

			@foreach ($info["datos"] as $dato)
				<tr>
					@foreach($dato as $key_i => $d)
						 @if ($key_i > 1)
							<td align="center">{{ $d }}</td>
						@else
							<td align="left">{{ $d }}</td>
						@endif
					@endforeach
				</tr>
				
			@endforeach                    
		</tbody>
		
		<tfoot>
			<tr style="background-color: #1E9966">
				<td style="color: #FFFFFF" colspan="{{ $key_i }}"><b>TOTAL</b></td>
				<td style="color: #FFFFFF" >${{ $info["total"] }}</td>
			</tr>
		</tfoot>
	</table>
	
		
</body>
</html>