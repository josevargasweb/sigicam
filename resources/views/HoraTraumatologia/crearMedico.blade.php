@extends("Templates/template")

@section("titulo")
Agregar Médico
@stop

@section("script")

<script>
var enviado=false;
var enviado2 = false;
	$(function(){

		$("#traumatologiaHoras").collapse();

		if($("#rut").val() != "") $("#rut").prop("readonly", true);
		if($("#dv").val() != "") $("#dv").prop("readonly", true);

		if($("#rut").val() == "") $("#sinRut").val(1);

		$("#fecha").datetimepicker({
			//autoclose: true,
			locale: "es",
			format: "DD-MM-YYYY HH:mm:ss",
			//todayHighlight: true,
			minDate: new Date()
		}).on("changeDate", function(){
			$('#formHora').bootstrapValidator('revalidateField', 'fecha');
		});


		$("#fechaNac").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true,
			endDate: "+0d"
		}).on("changeDate", function(){
			$('#asignarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
		});

		var esValidoElRut  = false;
		console.log(esValidoElRut);
		$("#crearMedico").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				rut: {
					validators: {
						notEmpty: {
							message: 'El run es obligatorio'
						},
						callback: {
							callback: function(value, validator, $field){
								return true;
							}
						}
					}
				},
				dv: {
					validators:{
						notEmpty: {
							message: 'El dígito es obligatorio'
						},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut");

								var dv = $("#dv");

								$("#rut2").val(field_rut.val());
								$("#dv2").val(dv.val());
								
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
									esValidoElRut = false;
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}
								esValidoElRut = true;
								return true;
							}
						},
						remote:{
							url: "{{URL::to('/')}}/existeMedico",
							headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
							data: function(validator) {
							
								return {

									rut: validator.getFieldElements('rut').val(),
									esValidoElRut: esValidoElRut,
									
									medico: {{count($medico->id_medico)}}


								};
							},
							type: "post"
						}
					}
				},
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
			console.log(evt.currentTarget.action);
			evt.preventDefault(evt);
 			var $form = $(evt.target);

 			$("#btnEnviar").prop("disabled", false);
 			var fv = $form.data('bootstrapValidator');
 			if(!enviado2){
				enviado2 = true;
			}
			else{
				return false;
			}

			enviado2 = true;
 			console.log("ok");
 				$.ajax({
 				url: evt.currentTarget.action,
 				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 				type: "post",
 				dataType: "json",
 				data: new FormData($form[0]),
 				cache: false,
 				contentType: false,
 				processData: false,
 				success: function(data){
 					console.log(data);
 					if(data.exito){
						 swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								location.href="{{URL::to('medicos')}}"; 
							}, 2000)
						},
						});

 					}
 					if(data.error){
 						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						console.log(data.error);
 					}
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
 				/*$.ajax({
 					url: $form .prop("action"),
 					type: "post",
 					dataType: "json",

 					data: new FormData($form[0]),
 					async: false,
 					//contentType: false,
 					//processData: false,
 					success: function(data){
 						enviado=true;
 						if(data.exito)
 							bootbox.alert("<h4>"+data.exito+"</h4>", function(){ location.href="{{URL::to('pedirHora')}}"; });
 						if(data.error)
 							swalError.fire({
						title: 'Error',
						text:data.error
						});
 					},
 					error: function(error){
 						console.log(error);
 					}
 				});*/
 			
 		});

	});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

 @section("miga")
 <li><a href="#">Agregar Medico</a></li>
 @stop


@section("section")

<fieldset>
	
	<!--{{ Form::open(array('method'=> 'post', 'url' => 'pedirHora', 'id'=>'formEditarPaciente','files'=> true)) }}-->

	       <?php

			    if (count($medico->id_medico)): //si existe el medico 
			    //echo "SI";
			        $form_data = array('url' => 'actualizarMedico/'.$medico->id_medico,"id"=>'crearMedico');
			        $action    = 'Editar';
			    else:
			        $form_data = array('url' => 'crearMedico',"id"=>'crearMedico');
			        $action    = 'Agregar';        
			    endif;
			?>

<legend>{{$action}} Médico</legend>

{{ Form::open($form_data) }}
<!--<form action="crerMedico" id='crearMedico' method="post" enctype="multipart/form-data">-->

	@if($medico->dv_medico == '10')
		<?php $medico->dv_medico = "K"; ?>
	@endif
 
	  <div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label"> Run Médico:* </label>
			<div class="col-sm-10">
				<div class="input-group">
					{{Form::text('rut', $medico->rut_medico, array('id' => 'rut', 'class' => 'form-control'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', $medico->dv_medico, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 50px;'))}}
				</div>
			</div>
		</div>
	</div>

	
  	 <!--<div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label">Establecimiento: </label>
			<div class="col-sm-10">
					{{Form::select('estab',$estab,$medico->establecimiento_medico,array('id'=>"estab", 'class'=>'form-control'))}}

			</div>
		</div>
	</div>-->




	<div class="row">
		<div class="form-group col-md-10">
			<label for="nombre" class="col-sm-2 control-label">Nombre:* </label>
			<div class="col-md-10">
			{{Form::text('nombre', $medico->nombre_medico, array('id' => 'nombre', 'class' => 'form-control'))}}
					
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-10">
			<label for="apellido" class="col-sm-2 control-label">Apellido: </label>
			<div class="col-md-10">
				{{Form::text('apellido', $medico->apellido_medico, array('id' => 'apellido', 'class' => 'form-control'))}}			
			</div>
		</div>
	</div>
        

  <div class="form-group">

  </div>

	<div class="row">
		<div class="form-group col-md-10">
			{{Form::submit('Aceptar', array('class' => 'btn btn-primary', 'id'=>'btnEnviar')) }}
			<a href="{{URL::previous()}}" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
		</div>
		
	</div>
	{{ Form::close() }}
</fieldset>













@stop