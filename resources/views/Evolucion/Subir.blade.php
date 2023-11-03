@extends("Templates/template")

@section("titulo")
Subir archivo
@stop

@section("miga")
<nav class="navbar navbar-default navbar-static subir-nav-header miga">
	@include("Templates/migaCollapse")
	<div class="collapse navbar-collapse bs-js-navbar-collapse">
		<div class="navbar-header">
			<ol class="breadcrumb listaMiga">
				<li><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
				<li><a href="#">Evolución</a></li>
				<li><a href="#" onclick='location.reload()'>Subir archivo</a></li>
			</ol>
		</div>
		@include("Templates/migaAcciones")
	</div>
</nav>
@stop

@section("script")

<script>
	
	$(function(){
		$("#evolucionMenu").collapse();
		$("input[type=file]").fileinput();

		$("#upload").bootstrapValidator({
 			 excluded: ':disabled',
 			 fields: {
 			 	file: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El archivo es obligatorio'
 			 			}
 			 		}
 			 	}
 			 }
 		}).on('status.field.bv', function(e, data) {
 			$("#upload input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
        	evt.preventDefault(evt);
			console.log("SE APRETO!");
 			var $form = $(evt.target)[0];
 			var data=new FormData($form);
 			$("#loading").show();
 			$.ajax({
 				url: "subirExcel",
 				type: "post",
 				dataType: "json",
 				data: new FormData($form),
 				cache: false,
 				contentType: false,
 				processData: false,
 				success: function(data){
					 
 					if(data.exito){
						 swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								$("#resultado").html(data.contenido);
							}, 2000)
						},
						});
					 }
 					if(data.error) {
 						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						console.log(data.msg);
 					}
 					$("#upload input[type='submit']").prop("disabled", false);
 					$("#loading").hide();
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
			return false;
 		});

	});

</script>

@stop

@section("section")
<fieldset>
	<legend>Subir archivo</legend>
	<div class="panel panel-default">
		<div class="panel-body">
		{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'upload', 'onsubmit' => 'return false', 'files'=> true)) }}
		<div class="form-group col-md-12">
			<label for="subr" class="col-sm-2 control-label">Subir archivo: </label>
			<div class="col-sm-10">
				<input type="file" name="file" class="file" title="Elegir archivo" data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
			</div>
		</div>
		<div class="form-group col-md-12">
			<div class="col-sm-2">
				<input type="submit" value="Subir" class="btn btn-primary" />
			</div>
			<div id="loading" class="col-sm-10" style="display: none;">
				<span>Subiendo archivo, por favor espere {{ HTML::image('img/loading.gif') }}</span>
			</div>
		</div>
		{{ Form::close() }}
		</div>
	</div>
</fieldset>
<div class="panel panel-default" id="resultado">
</div>



@stop


