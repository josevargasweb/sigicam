@extends("Templates/template")

@section("titulo")
Gestión Camas
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Editar Unidades</a></li>
@stop

@section("script")
<script>

$(function(){

	$(".servicios, .serviciosRecibidos").bootstrapDualListbox({
		filterPlaceHolder: "Buscar",
		filterTextClear: "Quitar todo",
		infoText: "Mostrando {0}",
		moveAllLabel: "Mover todo",
		selectedListLabel: "Servicios seleccionadas",
		nonSelectedListLabel: "Servicios no seleccionados",
		infoTextEmpty: "Lista vacía",
		infoTextFiltered: "<span class='label label-warning'>Filtrados</span> {0} de {1}"
	});

	$("select[multiple='multiple']").css("width", "100%");

	$("#formUpdateServicios").submit(function(){
		var servicios=[];
		$(".servicios").each(function(){
			servicios.push($(this).val());
		});
		var establecimiento=$("#estabHidden").val();
		var unidadEn=$("#unidadEn").val();
		$.ajax({
			url: $(this).prop("action"),
			data:{ servicios: servicios, establecimiento: establecimiento, unidadEn: unidadEn },
			dataType: "json",
			type: "post",
			success: function(data){
				if(data.exito){
					swalExito.fire({
					title: 'Exito!',
					text: data['exito'],
					didOpen: function() {
						setTimeout(function() {
						location.href="{{ URL::previous() }}";
						}, 2000)
					},
					});
				} 
				if(data.error){
					swalError.fire({	
					title: 'Error',
					text:data.error
					});
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
		return false;
	});

	$("#formUpdateServiciosRecibidos").submit(function(){
		var servicios=[];
		$(".serviciosRecibidos").each(function(){
			servicios.push($(this).val());
		});
		var establecimiento=$("#estabHiddenRecibido").val();
		var unidadEn=$("#unidadEnRecibido").val();
		$.ajax({
			url: $(this).prop("action"),
			data:{ servicios: servicios, establecimiento: establecimiento, unidadEn: unidadEn },
			dataType: "json",
			type: "post",
			success: function(data){
				if(data.exito){
					swalExito.fire({
					title: 'Exito!',
					text: data['exito'],
					didOpen: function() {
						setTimeout(function() {
							location.href="{{ URL::previous() }}";
						}, 2000)
					},
					});
				} 
					if(data.error){
					swalError.fire({
					title: 'Error',
					text:data.error
					});
					} 
				},
				error: function(error){
					console.log(error);
				}
			});
		return false;
	});

	$("#formEditarNombre").bootstrapValidator({
		excluded: ':disabled',
		group: '.error',
		fields: {
			nombreUnidad: {
				validators:{
					notEmpty: {
						message: 'El nombre de la unidad es obligatorio'
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
			url: $form .prop("action"),
			type: "post",
			dataType: "json",
			data: $form .serialize(),
			success: function(data){

				if(data.exito){
					swalExito.fire({
						title: 'Exito!',
						text: data['exito'],
						didOpen: function() {
							setTimeout(function() {
								location.href="{{ URL::previous() }}";
							}, 2000)
						},
						});
				} 
				if(data.error){
					swalError.fire({
					title: 'Error',
					text:data.error
					});
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
	});

});

</script>
@stop

@section("section")
<fieldset>
	<legend>Editar unidad {{$nombreUnidad}}</legend>
	<div class="row error">
		{{ Form::open(array('url' => 'administracion/editarNombre', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formEditarNombre', 'style' => 'padding-left: 15px;')) }}
		{{ Form::hidden('establecimiento', $establecimiento, array('id' => 'estabHidden')) }}
		{{ Form::hidden('unidad', $unidad, array('id' => 'unidad')) }}
		<label>Nombre unidad: </label>
		{{Form::text('nombreUnidad', "$nombreUnidad", array('id' => 'nombreUnidad', 'class' => 'form-control'))}}
		{{Form::submit('Editar nombre', array('class' => 'btn btn-primary')) }}
		<a href="{{ URL::previous() }}" class="btn btn-danger">Volver</a>
		{{ Form::close() }}	
	</div>
</fieldset>
<br><br>

<fieldset>
	<legend style="margin: 0;">Servicios ofrecidos</legend>
	<div class="row">
		{{ Form::open(array('url' => 'administracion/updateServicios', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formUpdateServicios', 'style' => 'padding-left: 15px; padding-top: 30px;')) }}
		{{ Form::hidden('establecimiento', $establecimiento, array('id' => 'estabHidden')) }}
		{{ Form::hidden('unidad', $unidad, array('id' => 'unidadEn')) }}
		<select class="servicios" name="servicios"  multiple="multiple" size="10" style="width: 100%;">
			@foreach($noTieneServicios as $key => $value)
			<option value="{{$key}}">{{$value}}</option>
			@endforeach
			@foreach($tieneServicios as $key => $value)
			<option value="{{$key}}" selected>{{$value}}</option>
			@endforeach
		</select> 
		<br>
		{{ Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</fieldset>

<br><br>

<fieldset>
	<legend style="margin: 0;">Servicios recibidos</legend>
	<div class="row">
		{{ Form::open(array('url' => 'administracion/updateServiciosRecibidos', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formUpdateServiciosRecibidos', 'style' => 'padding-left: 15px; padding-top: 30px;')) }}
		{{ Form::hidden('establecimiento', $establecimiento, array('id' => 'estabHiddenRecibido')) }}
		{{ Form::hidden('unidad', $unidad, array('id' => 'unidadEnRecibido')) }}
		<select class="serviciosRecibidos" name="servicios"  multiple="multiple" size="10" style="width: 100%;">
			@foreach($noTieneServiciosRecibidos as $key => $value)
			<option value="{{$key}}">{{$value}}</option>
			@endforeach
			@foreach($tieneServiciosRecibidos as $key => $value)
			<option value="{{$key}}" selected>{{$value}}</option>
			@endforeach
		</select> 
		<br>
		{{ Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</fieldset>
<br><br>
@stop