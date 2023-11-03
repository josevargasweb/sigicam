@extends("Templates/template")

@section("titulo")
Gestión Buscador
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión Establecimientos</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>
	$(document).ready(function() {
    
	// Setup - add a text input to each footer cell
    $('#admCamas thead tr').clone(true).appendTo( '#admCamas thead' );
    $('#admCamas thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
 
    var table = $('#admCamas').DataTable( {
        orderCellsTop: true,
		fixedHeader: true,
		"oLanguage": {
              "sUrl": "{{URL::to('/')}}/js/spanish.txt"
          }
    } );
} );
	

</script>

  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")
<style>
	#admCamas_filter {
		display: none;
	}
</style>
<div class="table-responsive">
<table id="admCamas" class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>Establecimiento</th>
			<th>Area</th>
			<th>Servicio</th>
            <th>Sala</th>
			<th>Cama</th>
			{{-- <th>ID</th> --}}
            <th>Ir</th>
		</tr>
	</thead>
	<tbody>
		@foreach($salas as $sala)
		<tr>
			<td>{{$sala->nombre_estab}}</td>
			<td>{{$sala->area}}</td>
			<td>{{$sala->alias}}</td>
			<td>{{$sala->nombre_sala}}</td>
			<td>{{$sala->cama}}</td>
			{{-- <td>{{$sala->url}}</td> --}}
			<td>{{ HTML::linkRoute('unidad', 'IR', [$sala->url], ["style" => "color:#04B404"] ) }}</td>
			</tr>
        @endforeach
	</tbody>
</table>
</div>
<br>




@stop