@extends("Templates/template")

@section("titulo")
Historial Rutas
@stop

@section("script")

@stop

@section("miga")
	<li><a href="#">Gestión de Ambulancias</a></li>
	<li><a href="#">Incorporar Rutas</a></li>
 	<li><a href="../indexRutas">Historial</a></li>
@stop


@section("section")
	<legend>Historial de Ruta de Ambulancia con patente: <b>{{$ambulancia->patente}}</b></legend>
	<table class = "table bg-info table table-striped table-bordered table-condensed no-footer" id = "gridHistorialRuta" >
	    <thead>
	        <tr>
	            <th WIDTH="200">Hospital Origen</th>
	            <th>Hora de salida solicitada:</th>
	            <th>Hora app. de llegada:</th>
	            <th WIDTH="200">Hospital destino:</th>
	            <th WIDTH="125">Paciente:</th>
	        </tr>
	        
	    </thead>
	    
	    <tbody>
	    	@foreach($rutas as $ruta)
		        <tr>
		            <td>{!! $ruta->origen !!}</td>
		            <td>{!! $ruta->hora_salida !!}</td>
		            <td>{!! $ruta->hora_llegada_API !!}</td>
		            <td>{!! $ruta->destino !!}</td>
		            @if($ruta->nombre != "" )
		            	@if($ruta->rut != "" || $ruta->rut != null)
		            		<td><h4 style="text-align: center;"><span class="label label-info" >{!! $ruta->nombre !!} ({!! $ruta->rut !!}-{!! $ruta->dv !!})</span></h4></td>
		            	@else
		            		<td><h4 style="text-align: center;"><span class="label label-warning" >{!! $ruta->nombre !!} (Sin Rut)</span></h4></td>
		            	@endif
		           	@else
		           		<td><h4 style="text-align: center;"><span class="label label-default">sin paciente</span></h4></td>
		           	@endif
		        </tr> 
	        @endforeach
	    </tbody>
	</table>	

	<script>
		$("#gridHistorialRuta").dataTable({
			"order": [ 1, 'desc' ],
	        "language": {
	            "lengthMenu":     "Mostrar _MENU_ por página",
	            "zeroRecords":    "No se ha encontrado registros",
	            "info":           "Mostrando pagina _PAGE_ de _PAGES_",
	            "infoEmpty":      "No se ha encontrado información",
	            "infoFiltered":   "(filtered from _MAX_ total records)",
	            "search":         "Buscar:",
	            "paginate": {
	                "first":      "Primero",
	                "last":       "Ultimo",
	                "next":       "Siguiente",
	                "previous":   "Anterior"
	            },
	        }
	    });
	</script>
@stop