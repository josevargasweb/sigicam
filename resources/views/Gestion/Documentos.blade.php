@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>
	$(function(){		
		
		$('#listaEspera').dataTable({	
			/* dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'Pacientes en espera',
					text: 'Exportar',
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7,8]
					} ,
					className: 'btn btn-default',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						var clRow = $('row', sheet);
						//$('row c', sheet).attr( 's', '25' );  //bordes
						$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
						$('row', sheet).attr('ht',15);
						$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
						$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
					}
				}
    		], */
			"bJQueryUI": true,
			"iDisplayLength": 10,
			/* "order": [[ 6, "asc" ]], */
			"columnDefs": [
       			{ type: 'date-euro', targets: 2 }
     		],
			"ajax": "listaDocumentos",
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
			/* "sPaginationType": "full_numbers", */
		});

	});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<!-- <li><a href="#">Urgencia</a></li>
<li><a href="#">Documentos</a></li> -->
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")
<fieldset>
	<legend>Descarga de documentos</legend>
	<div class="table-responsive col-sm-8" style="padding-left:0px;">
	<table id="listaEspera" class="table  table-condensed table-hover">
		<thead>
			<tr>
				<th>Nombre archivo</th>
				<th>Fecha subida</th>
				<th>Descargar</th>
			</tr>
		</thead>
		<tbody>
        </tbody>
	</table>
	</div>
</fieldset>

@stop