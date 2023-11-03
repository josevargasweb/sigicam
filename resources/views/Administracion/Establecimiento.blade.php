@extends("Templates/template")

@section("titulo")
Gestión Establemcimientos
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión Establecimientos</a></li>
@stop


@section("script")
<script>

	var editar=function(nombre, complejidad, id){
		$("#nombre").val(nombre);
		$("#complejidad").val(complejidad.toLowerCase());
		$("#idEstab").val(id);
		$("#modalEditarEstab").modal("show");
	}

	var obtenerEstablecimientos=function(){
		$.ajax({
			url: "obtenerEstablecimientos",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			type: "get",
			dataType: "json",
			success: function(data){
				table.api().ajax.reload();
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	$(function(){
		$("#administracionMenu").collapse();

		 table=$('#admCamas').dataTable({
			"bJQueryUI": true,
			"iDisplayLength": 10,
			"ajax": "obtenerEstablecimientos",
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
		});

 		$("#formEditarEstab").bootstrapValidator({
 			 excluded: ':disabled',
 			 fields: {
 			 	nombre: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El nombre es obligatorio'
 			 			}
 			 		}
 			 	}
 			 }
 		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			$.ajax({
 				url: "editarEstablecimiento",
 				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(data){
 					$("#modalEditarEstab").modal("hide");
 					$("#formEditarEstab input[type='submit']").prop("disabled", false);
  					if(data.error){
 						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						console.log(data.error);
 					}
 					else obtenerEstablecimientos();
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
 		});


	});

</script>

  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop


@section("section")
<div class="row">
	<div class="col-md-12"> 
		<div class="table-responsive">
		<table id="admCamas" class="table table-condensed table-hover">
			<thead>
				<tr>
					<th>Establecimiento</th>
					<th>Complejidad</th>
					<th>Editar</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</div>
	</div>
</div>
<br>

<div id="modalEditarEstab" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar Establecimiento</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarEstab')) }}
			{{ Form::hidden('idEstab', '', array('id' => 'idEstab')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control', 'autofocus' => 'true'))}}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Complejidad: </label>
						<div class="col-sm-10">
							{{ Form::select('complejidad', $complejidad, '', array('class' => 'form-control', 'id' => 'complejidad')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>


@stop