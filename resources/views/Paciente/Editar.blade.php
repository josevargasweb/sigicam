@extends("Templates/template")

@section("titulo")
Editar paciente
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>
	var enviado=false;

	var obtenerPaciente=function(rut, id){
		$.ajax({
			url: "/paciente/obtenerPaciente",
			data:{rut: rut, id: id},
			type: "post",
			dataType: "json",
			success: function(data){
				if(data != ""){
					$("#rut").val(data.rut);
					$("#dv").val(data.dv);
					$("#fecha_nacimiento").val(data.fecha_nacimiento);
					$("#nombre").val(data.nombre);
					$("#apellido_paterno").val(data.apellido_paterno);
					$("#apellido_materno").val(data.apellido_materno);
					$("#sexo").val(data.sexo);
                    if(data.extranjero === true){
                        $("#extranjero-si").prop("checked", true);
                    }
                    else if(data.extranjero === false){
                        $("#extranjero-no").prop("checked", true);
                    }

				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	$(function(){
		var telefonete = '{{$paciente->telefono}}'; 
	@if($paciente->telefono != '-' || $paciente->telefono == '')
		$("#telefono_antiguo").val('{{$paciente->telefono}}');
	@else
		$(".old_fone").hide();
	@endif
	
	var t = 1;	
	var limite = 4; //para limitar a 3
	
	$(document).on("click", ".eliminar_telefono", function(e){
		e.preventDefault();
		var child = $(this).closest('tr').nextAll();
		$(this).parents('tr').remove();
		t--;
	});

	@foreach($telefonos as $key => $t)
		html = '<tr> <td class="row-index"></td> <td><select name="tipo_telefono[]" class="form-control" id="tipo_telefono_'+t+'"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td> <input id="telefono_'+t+'" type="number" name="telefono[]" class="form-control"> </td><td> <button class="btn btn-danger eliminar_telefono" type="button" id="rn'+t+'" data-id="'+t+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
		$("#Telefonos").append(html);
		$("#tipo_telefono_"+t).val('{{$t->tipo}}');
		$("#telefono_"+t).val('{{$t->telefono}}');
		t++;
	@endforeach	

	$("#addTelefono").click(function(){
		if(t < limite){
			html = '<tr> <td class="row-index"></td> <td><select name="tipo_telefono[]" class="form-control"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td> <input type="number" name="telefono[]"" class="form-control" placeholder="Ingrese número de teléfono"> </td><td> <button class="btn btn-danger eliminar_telefono" type="button" id="rn'+t+'" data-id="'+t+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
			$("#tablaTelefonos").append(html);
			t++;
		}
	});

		if($("#rut").val() != ''){
			if($("#nombre").val() != ''){
				$("#nombre").attr('readonly', true);
			}else{
				$("#nombre").attr('readonly', false);
			}

			if($("#apellido_paterno").val() != ''){
				$("#apellido_paterno").attr('readonly', true);
			}else{
				$("#apellido_paterno").attr('readonly', false);
			}

			if($("#apellido_materno").val() != ''){
				$("#apellido_materno").attr('readonly', true);
			}else{
				$("#apellido_materno").attr('readonly', false);
			}
		}

		$("input[name='extranjero']").on("change", function(){
			var value=$(this).val();
			if(value == "si")$("#NumPasaporte").show("slow");
			else $("#NumPasaporte").hide("slow");
		});

		$("#region").on("change", function(){
			//buscarGeo();

			$.ajax({
				url: "{{URL::to('/comunas')}}",
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: { "region": $("#region").val() },
				dataType: "json",
				type: "post",
				success: function(data){
					//la variable que llevaralas opciones y estara en html
					var html = "<select name='comuna' id='comuna' class='form-control'>";

					data.forEach(function(element){
						html +=  "<option value="+element.id_comuna+">"+element.nombre_comuna+"</option>";
					});

					html += "</select>";
					//se anade al select
					$("#comunas").find('#comuna').remove().end().append(html);
					//$("#comunas");
					//$("#comunas").find('option').remove().end().append('<option value="whatever">text</option>');

				},
				error: function(error){
					//$("#divLoadBuscarPaciente").hide();
					console.log(error);
				}
			});

		});

		if($("#rut").val() != "") $("#rut").prop("readonly", true);
		if($("#dv").val() != "") $("#dv").prop("readonly", true);
		$("#rut").on("change", function(){
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'rut');
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'dv');
		});
		$("#dv").on("change", function(){
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'rut');
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'dv');
		});
		$("#rut_madre").on("change", function(){
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'rut_madre');
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'dv_madre');
		});
		$("#dv_madre").on("change", function(){
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'rut_madre');
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'dv_madre');
		});

		if($("#rut").val() == "") $("#sinRut").val(1);

		$("#fecha_nacimiento").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true,
			endDate: "+0d"
		}).on("changeDate", function(){
			$('#formEditarPaciente').bootstrapValidator('revalidateField', 'fecha_nacimiento');
		});

		$("#formEditarPaciente").bootstrapValidator({
			excluded: [':disabled',':hidden'],
			fields: {
				rut: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								return true;
							}
						}
					}
				},
				dv: {
					validators:{
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut");
								var dv = $("#dv");
								if(field_rut.val() == '' && dv.val() == '') {
									return true;
								}
								if(field_rut.val() != '' && dv.val() == ''){
									return {valid: false, message: "Debe ingresar el dígito verificador"};
								}
								if(field_rut.val() == '' && dv.val() != ''){
									return {valid: false, message: "Debe ingresar el run"};
								}
								var rut = $.trim(field_rut.val());
								var esValido=esRutValido(field_rut.val(), dv.val());
								if(!esValido){
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}
								return true;
							}
						},
						remote:{
							url: "{{URL::to('/')}}/paciente/tieneCaso",
							headers: {
 							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data: function(validator) {
								return {
									rut: validator.getFieldElements('rut').val(),
                                    id: $("#id").val()
								};
							},
							type: "post"
						}
					}
				},
				rut_madre: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								return true;
							}
						}
					}
				},
				dv_madre: {
					validators:{
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut_madre");
								var dv = $("#dv_madre");
								if(field_rut.val() == '' && dv.val() == '') {
									return true;
								}
								if(field_rut.val() != '' && dv.val() == ''){
									return {valid: false, message: "Debe ingresar el dígito verificador"};
								}
								if(field_rut.val() == '' && dv.val() != ''){
									return {valid: false, message: "Debe ingresar el run"};
								}
								var rut = $.trim(field_rut.val());
								var esValido=esRutValido(field_rut.val(), dv.val());
								if(!esValido){
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}
								return true;
							}
						}
					}
				},
				nombre: {
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				fecha_nacimiento: {
					validators:{
						notEmpty: {
							message: 'El fecha de nacimiento es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFecha(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				},
				numeroCalle: {
					validators:{
						integer: {
							message: "Debe ingresar solo números"
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			var fv = $form.data('bootstrapValidator');
 			if(!enviado){
 				$.ajax({
 					url: $form .prop("action"),
 					type: "post",
 					dataType: "json",
 					data: $form .serialize(),
 					async: false,
 					success: function(data){
 						enviado=true;
 						if(data.exito){
							swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									location.href="{{URL::previous()}}";
								}, 2000)
							},
							});
						 }
 						if(data.error)
 							swalError.fire({
							title: 'Error',
							text:data.error
							});
 					},
 					error: function(error){
 						console.log(error);
 					}
 				});
 			}
		 });

		$("#comuna").on("change", function(){
			buscarGeo();
		});

		$("#calle, #numeroCalle").on('keyup', function(){
			buscarGeo();
		});

		function buscarGeo(){
			var geocoder = new google.maps.Geocoder();
			var address =  $("#calle").val()+" "+$("#numeroCalle").val()+" ,"+$("#comuna option:selected").text()+", Chile";

			geocoder.geocode({
							'address': address
			}, function (results, status) {

					var latitud=null;
					var longitud=null;



					if (status == google.maps.GeocoderStatus.OK) {
							latitud = results[0].geometry.location.lat();
							longitud = results[0].geometry.location.lng();
					} else {
							latitud= null;
							longitud=null;
					}

					// Completar Los Campos de Latitud y Longitud
					// Completar Los Campos de Latitud y Longitud
					$("#latitud").val(latitud);
					$("#longitud").val(longitud);
					//document.getElementById('longitud').value = longitud;
					//google.maps.event.trigger(map, 'resize');
					//map.setCenter(new google.maps.LatLng(latitud,longitud));
					//marker.setPosition(new google.maps.LatLng(latitud,longitud));



			});
		}

	});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
  {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAKUCTycz-4g3GNG0uRjY6rXGui2PurUAM"></script> --}}

@stop

 @section("miga")
 <li><a href="#">Editar paciente</a></li>
 @stop


@section("section")
<style>
	.ajustar {
		z-index: 1 !important;
	}

	#tablaTelefonos tbody{
		counter-reset: Serial;           
	}

	table #tablaTelefonos{
		border-collapse: separate;
	}

	#tablaTelefonos tr td:first-child:before{
		counter-increment: Serial;      
		content: counter(Serial); 
	}
</style>
<fieldset>
	<legend>Editar paciente</legend>
	{{ Form::model($paciente, ["url" => "paciente/editarPaciente", "id" => "formEditarPaciente", "class" => "form-horizontal", "role" => "form", 'autocomplete' => 'off']) }}
	{{ Form::hidden('id', "$paciente->id", array('id' => 'id')) }}
	{{ Form::hidden('sinRut', "0", array('id' => 'sinRut')) }}
	{{ Form::hidden('latitud', null, array('id' => 'latitud')) }}
	{{ Form::hidden('longitud', null, array('id' => 'longitud')) }}
	{{ Form::hidden('caso', $caso["datos"]->id, array('id' => 'caso')) }}

	@if($paciente->rut_madre)
		<div class="row">
			<div class="form-group col-md-8">
				<div class="col-sm-12">
					<label for="rut_madre" class="control-label">Run Madre: </label>

					<div class="input-group">
						{{Form::text('rut_madre', $paciente->rut_madre, array('id' => 'rut_madre', 'class' => 'form-control ajustar'))}}
						<span class="input-group-addon"> - </span>
						{{Form::text('dv_madre', $paciente->dv_madre, array('id' => 'dv_madre', 'class' => 'form-control ajustar', 'style' => 'width: 50px;'))}}
					</div>
				</div>
			</div>
		</div>
	@endif

	<div class="row">
		<div class="form-group col-md-8">
			<div class="col-sm-12">
				<label for="rut" class="control-label">Run: </label>

				<div class="input-group">
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control ajustar'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control ajustar', 'style' => 'width: 50px;'))}}
				</div>
			</div>
		</div>
		<div class="form-group col-md-4">
			<div class="col-sm-11">
				<label for="rut" class="control-label">Ficha clínica: </label>
				<div class="input-group">
					{{Form::text('ficha', $caso["datos"]->ficha_clinica, array('id' => 'ficha', 'class' => 'form-control ajustar'))}}
				</div>
			</div>
		</div>
	</div>


    <div class="row">
        <div class="form-group col-md-6">
            <label for="extranjero" class="col-sm-12 control-label">Extranjero: </label>
            @if($paciente->extranjero == 'false')
                <div class="col-sm-2">
                    <label for="extranjero" class="col-sm-2 control-label">
						{{Form::radio('extranjero', "no", true, array('required' => true, "style" => "vertical-align: baseline", "id" => "extranjero-no",
						"checked" => ""))}}No</label>
                </div>
                <div class="col-sm-2">
                    <label for="extranjero" class="col-sm-2 control-label">
                        {{Form::radio('extranjero', "si", false, array('required' => true, "style" => "vertical-align: baseline", "id" => "extranjero-si"))}}Sí</label>
               </div>
            @else
				<div class="col-sm-2">
					<label for="extranjero" class="col-sm-2 control-label">
						{{Form::radio('extranjero', "no", false, array('required' => true, "style" => "vertical-align: baseline", "id" => "extranjero-no"))}}No</label>
				</div>
				<div class="col-sm-2">
					<label for="extranjero" class="col-sm-2 control-label">
						{{Form::radio('extranjero', "si", true, array('required' => true, "style" => "vertical-align: baseline", "id" => "extranjero-si", "checked" => ""))}}Sí</label>
				</div>	
            @endif
		</div>
		<div class="form-group col-md-6">
			<div class="col-sm-11">
				<label for="fecha_nacimiento" class="control-label">Fecha de nacimiento: </label>

				{{Form::text('fecha_nacimiento', null, array('id' => 'fecha_nacimiento', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="col-md-5">
			@if($paciente->extranjero == 'false')
				<div id="NumPasaporte" style="margin-left: -27px;" hidden>
			@else
				<div id="NumPasaporte" style="margin-left: -27px;">
			@endif
				<label for="npasaporte" class="control-label">Número Pasaporte:</label>
					{{Form::text('n_pasaporte', $paciente->n_identificacion , array('id' => 'n_pasaporte', 'class' => 'form-control', 'autofocus' => 'true'))}}
					<font color="black">Ingresar el número de pasaporte en caso de tener.</font>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group col-md-4">
			<div class="col-sm-12">
				<label for="nombre" class="control-label">Nombre: </label>

				{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-4">
			<div class="col-sm-10">
				<label for="apellido_paterno" class="control-label">Apellido Paterno: </label>

				{{Form::text('apellido_paterno', null, array('id' => 'apellido_paterno', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-4">
			<div class="col-sm-12">
				<label for="apellido_materno" class=" control-label">Apellido Materno: </label>

				{{Form::text('apellido_materno', null, array('id' => 'apellido_materno', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" >
			<div class="col-md-3" style="margin-left: -17px;"> 
				<label for="sexo" class="control-label">Género: </label>
				{{ Form::select('sexo', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
			</div>
			{{-- <div class="col-md-3 offset-md-2 old_fone">
				<label for="telefono" class="control-label" title="Nombre Social">Teléfono: </label>
				{{Form::number('telefono_antiguo', null, array('id' => 'telefono_antiguo', 'class' => 'form-control'))}}
			</div> --}}
			{{-- <div class="col-md-2 offset-md-1 old_fone" style="margin-top: 25px;">
				<a class="btn btn-success" id="conservar">✔ Conservar</a>
			</div>
			<div class="col-md-2 old_fone" style="margin-top: 25px;">
				<a class="btn btn-danger" id="eliminarNum">X Eliminar</a>
			</div> --}}
		</div>
	</div>
	
	<div>
		<br>
		<label for="">Telefonos:</label>
		<table class="table table-bordered" id="tablaTelefonos" class="ignoreTable">
			<thead>
				<tr>
					<th>Indice</th>
					<th>Tipo</th>
					<th>Teléfono</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody id="Telefonos"></tbody>
		</table>
		<div class="btn btn-primary" id="addTelefono" >+ Teléfono</div>
	</div>
	<div class="row">
		<div class="row" align="right" style="margin-right: 15px; margin-bottom:10px;">
			{{-- <div class="btn btn-primary" id="addTelefono" >+ Teléfono</div> --}}
		</div>
	</div>

	<legend> Datos de dirección</legend>
	<div class="row">
		<div class="form-group col-md-4">
			<div class="col-sm-12">
				<label for="nombre" class="control-label">Calle: </label>

				{{Form::text('calle', $paciente->calle, array('id' => 'calle', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-2">
			<div class="col-sm-12">
				<label for="nombre" class="control-label">Número: </label>

				{{Form::text('numeroCalle', $paciente->numero, array('id' => 'numeroCalle', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="nombre" class="control-label">Observación dirección:</label>
				{{Form::text('observacionCalle', $paciente->observacion, array('id' => 'observacionCalle', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>

	<div class="row">

	</div>

	<div class="row">

	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="region" class="control-label" title="Region">Region: </label>
				{{ Form::select('region', $regiones, $region->id_region, array('id' => 'region', 'class' => 'form-control')) }}
			</div>
		</div>

		<div class="form-group col-md-6">
			<div class="col-sm-11" id="comunas">
				<label for="comuna" class="control-label" title="Comuna">Comuna: </label>
				{{Form::select('comuna', $comunas, $paciente->id_comuna, array('id' => 'comuna', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="form-group col-md-10">
			{{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
			<a href="{{URL::previous()}}" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
		</div>

	</div>
	{{ Form::close() }}
</fieldset>

@stop
