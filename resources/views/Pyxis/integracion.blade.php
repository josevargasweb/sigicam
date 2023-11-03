@extends("Sesion/templateIndex")

@section("titulo")
Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público
@stop

@section("contenido")
<div class="col-sm-12 sin-pad-lateral">

    <div style="margin:0 100px;">
    	<legend>Tabla de integracion Pyxis &nbsp;  
    		 <button type="button" class="btn btn-default btn-sm" onclick="location.reload();">
	          	<span class="glyphicon glyphicon-refresh"></span> Recargar
	        </button>
    	</legend>
			<div>
				<table class="table table-bordered categorizacion">
					<thead>
						<tr>
							<th>Correlativo</th>
							<th>id_paciente</th>
							<th>id_alt_paciente_run</th>
							<th>id_alt_paciente_hc</th>
							<th>nombre_paciente</th>
							<th>unidad_enfermeria</th>
							<th>sala</th>
							<th>cama</th>
							<th>fecha_admision</th>
							<th>mensaje</th>
							<th>estado</th>
							<th>fecha_insercion</th>
							<th>fecha_lectura</th>
							<th>maquina</th>
							<th>dato_enviado</th>
							
						</tr>
					</thead>
					<tbody>
						@foreach($tabla as $pyxis)
							<tr>
								<td>{{$pyxis->correlativo}}</td>
								<td>{{$pyxis->id_paciente}}</td>
								<td>{{$pyxis->id_alt_paciente_run}}</td>
								<td>{{$pyxis->id_alt_paciente_hc}}</td>
								<td>{{$pyxis->nombre_paciente}}</td>
								<td>{{
								($pyxis->unidad_enfermeria == null) ? 'NULL' 
									: ( ($pyxis->unidad_enfermeria == "PIVOTE" && ($pyxis->sala == "UTI" || $pyxis->sala == "UCI") ) ? 'UPC'
										: $pyxis->unidad_enfermeria)}}</td>
								<td>{{$pyxis->sala}}</td>
								<td>{{$pyxis->cama}}</td>
								<td>{{$pyxis->fecha_admision}}</td>
								<td>{{$pyxis->mensaje}}</td>
								<td>{{$pyxis->estado}}</td>
								<td>{{$pyxis->fecha_insercion}}</td>
								<td>{{$pyxis->fecha_lectura == null ? 'NULL' : $pyxis->fecha_lectura}}</td>
								<td>{{$pyxis->maquina == null ? 'NULL' : $pyxis->maquina}}</td>
								<td>{{$pyxis->dato_enviado == null ? 'NULL' : $pyxis->dato_enviado}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

			</div>
	</div>
 </div>

@stop