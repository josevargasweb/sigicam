@extends("Templates/template")

@section("titulo")
Búsqueda
@stop

@section("miga")
<li><a href="#">Búsqueda</a></li>
<li><a href="#" onclick='location.reload()'>Busqueda de servicios</a></li>
@stop

@section("script")

<script>

var getCuposEstablecimiento=function(servicio){
	$.ajax({
		url: "getCuposEstablecimiento",
		headers: {        
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
		data: {servicio: servicio},
		type: "post",
		dataType: "json",
		success: function(data){
			var tabla=$("#tablaEstab").dataTable();
			tabla.fnClearTable();
			if(data.length > 0) tabla.fnAddData(data);  
		},
		error: function(error){
			console.log(error);
		}
	});
}

$(function(){
	$("#buscarMenu").collapse();

	$("#tablaEstab").dataTable({	
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 25,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	$("#servicios").on("change", function(){
		var value=$(this).val();
		getCuposEstablecimiento(value);
	});

	getCuposEstablecimiento($("#servicios").val());


});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")
<fieldset>
	<legend>Búsqueda de servicios</legend>
	<div class="form-horizontal"> 
		<div class="form-group error">
			<label class="col-sm-1 control-label">Servicios: </label>
			<div class="col-sm-3">
				{{ Form::select('servicios', $servicios, null, array('class' => "form-control", 'id' => 'servicios')) }}
			</div>
		</div>
	</div>
	<br>
	<div class="table-responsive">
	<table id="tablaEstab" class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th>Establecimiento</th>
				<th>Servicio</th>
				<th>Cupos</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
	
</fieldset>
<br><br>
@stop
