@extends("Templates/template")

@section("titulo")
Contingecias
@stop


@section("miga")
<li><a href="#">Contingencia</a></li>
<li><a href="#" onclick='location.reload()'>Ver Contingecias</a></li>
@stop

@section("script")

<script>

var getContingencias=function(){
	$.ajax({
		url: "getContingencias",
		headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
		type: "post",
		dataType: "json",
		success: function(data){
			console.log(data);
			var tabla=$("#contingencias").dataTable();
			tabla.fnClearTable();
			if(data.length > 0) tabla.fnAddData(data);   
		},
		error: function(error){
			console.log(error);
		}
	});
}

var anularContingencia=function(id){
	bootbox.dialog({
		message: "<h4>¿ Desea anula la contingencia ?</h4>",
		title: "Confirmación",
		buttons: {
			main: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "anularContingencia",
						headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
						data: {id: id},
						dataType: "json",
						type: "post",
						success: function(data){
							mensaje(data);
						},
						error: function(error){
							console.log(error);
						}
					});
				}
			},
			danger: {
				label: "Cancelar",
				className: "btn-danger",
				callback: function() {
				}
			}
		}
	});
}

$(function(){

	$("#contingenciaMenu").collapse();

	$("#contingencias").dataTable({	
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 10,
		"bJQueryUI": true,
		"bAutoWidth" : false,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	getContingencias();

});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("css")

@stop

@section("section")

<fieldset>
	<legend>Ver contingencias</legend>
	<div class="table-responsive">
	<table id="contingencias" class="table table-striped table-bordered table-hover" style="width: 100%;">
		<thead>
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
				<th>Hospitales de origen</th>
			@endif
			<th>Hospitales de destino</th>
			<th>Nombre médico de turno</th>
			<th>Pacientes en espera</th>
			<th>Pacientes derivacion cama básica</th>
			<th>Pacientes derivacion cama mayor</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</thead>
	</table>
	</div>
</fieldset>

@stop