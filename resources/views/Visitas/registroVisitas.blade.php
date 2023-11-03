@extends("Templates/template")

@section("titulo")
	Búsqueda 800 371 900
@stop

@section("miga")
	<li><a href="#">Búsqueda</a></li>
	<li><a href="#" onclick='location.reload()'>Búsqueda de pacientes</a></li>
@stop

@section("script")
<script>
$(function(){
	$("#visitasMenu").collapse();
	//Modificar tamaño tabla
	$("#main_").removeClass("col-md-7");
	$("#main_").addClass("col-md-9");

	$("#menuIzquierdo").remove();
	
	$("#tablaPacientesPorNombre").DataTable({
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});
	
	var pacientesPorNombre = [];
	function deshabilitarBuscarPaciente()
	{
		$("#buscar_paciente").prop("disabled",true);
	}
	function seleccionarPaciente(indice){
		cargarDatosPaciente(pacientesPorNombre[indice]);
		deshabilitarBuscarPaciente();
		revalidar();
	}
	function cargarTabla(datosPacientes)
	{
		pacientesPorNombre = datosPacientes;
		var tabla = $("#tablaPacientesPorNombre").DataTable();
		tabla.clear();
		for(var i = 0; i < datosPacientes.length; i++)
		{
			var accion = "<button type='button' class='btn btn-primary btn-seleccion' data-indice='" + i + "'>Seleccionar</button>";
			
			if(datosPacientes[i].recibe_visitas === false)
			{
				accion = "El paciente no puede recibir visitas";
			}
			else if(datosPacientes[i].permite_visitas === false){
				accion = "El paciente ha alcanzado el máximo de visitas permitidas";
			}
			
			tabla.row.add([
				datosPacientes[i].nombre,
				datosPacientes[i].apellido_paterno,
				datosPacientes[i].apellido_materno,
				datosPacientes[i].nombre_social,
				datosPacientes[i].fecha_nacimiento,
				accion
			]);
			tabla.draw();
		}
	}
	$("#tablaPacientesPorNombre").on("click","button.btn-seleccion",function(){
		seleccionarPaciente($(this).data("indice"));
		$("#modalBuscarPacientePorNombre").modal("hide");
	});
	function rut(identificacion)
	{
		var id = identificacion.replace(/[.-]/g,"");
		return id.substring(0,id.length - 1);
	}
	function obtener_identificacion(acompanante,finalizar){
		var tipo = $(acompanante ? (finalizar ? "#tipo_identificacion_acompanante_finalizar" : "#tipo_identificacion_acompanante") : "#tipo_identificacion").val();
		if(tipo == "rut" || tipo == "rut_madre")
		{
			return {
				tipo: tipo,
				n_identificacion: rut($(acompanante ? (finalizar ? "#n_identificacion_acompanante_finalizar" : "#n_identificacion_acompanante") : "#n_identificacion").val()),
				dv: $(acompanante ? (finalizar ? "#n_identificacion_acompanante_finalizar" : "#n_identificacion_acompanante") : "#n_identificacion").val().slice(-1),
				identificacion_completa: $(acompanante ? (finalizar ? "#n_identificacion_acompanante_finalizar" : "#n_identificacion_acompanante") : "#n_identificacion").val()
			};
		}
		return {
			tipo: tipo,
			n_identificacion: $(acompanante ? (finalizar ? "#n_identificacion_acompanante_finalizar" : "#n_identificacion_acompanante") : "#n_identificacion").val(),
			dv: null,
			identificacion_completa:$(acompanante ? (finalizar ? "#n_identificacion_acompanante_finalizar" : "#n_identificacion_acompanante") : "#n_identificacion").val()
		};
	}
	$("#btn_cancelar").on("click",function(){
		window.location.reload(true);
	});
	function revalidar()
	{
		var campos = [
			"n_identificacion_acompanante",
			"n_identificacion",
			"sala",
			"cama",
			"nombre_acompanante",
			"primer_apellido_acompanante",
			"nombre",
			"primer_apellido",
			"telefono",
			"servicio",
			"area"
		];
		for(var i = 0; i < campos.length; i++)
		{
			$("#form_registro_visitas").bootstrapValidator("revalidateField",campos[i]);
		}
	}
	$("#buscar_datos").on("click",function(){
		
		var identificacion = obtener_identificacion(true);
		if(identificacion.tipo != "rut")
		{
			return;
		}

		console.log(identificacion.n_identificacion);
		swalCargando.fire({title:'Cargando datos de la visita'});
			$.ajax({
			url: "{{URL::to('/validarRutAcompanante')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {'n_identificacion_acompanante': $("#n_identificacion_acompanante").val()},
			dataType: "json",
			type: "get",
			success: function(data){

				swalCargando.close();
				Swal.hideLoading();

				if(data.informacion){
					swalInfo2.fire({
					title: 'Información',
					text:data.informacion
					}).then(function(result) {
						location.reload();
						$("form").trigger("reset");
					});
				}

				if(data.error){
					swalError.fire({
						title: 'Error',
						text:data.error
					}).then(function(result) {
						if (result.isDenied) {
							location.reload();
							$("form").trigger("reset");
						}
					});
				}

				if(data.exito){
					validarRutAcompanante(identificacion.n_identificacion)
				}
				
			},
			error: function(error){
				swalCargando.close();
				Swal.hideLoading();
				console.log(error);
			}
			});
	});
	
	$("#buscar_datos_finalizar").on("click",function(){
		
		var identificacion = obtener_identificacion(true,true);

		swalCargando.fire({title:'Cargando datos de la visita'});
		$.ajax({
			url: "{{URL::to('/buscarVisita')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				n_identificacion: identificacion.identificacion_completa,
				tipo_identificacion: identificacion.tipo
			},
			dataType: "json",
			type: "post",
			success: function(data){
				Swal.close();

				if(!data.id_registro_visitas){
					swalInfo2.fire({
						title: 'Información',
						text: "Esta persona no registra visitas."
					}).then((result) => {
						location.reload(true);
					});
					return;
				}
				$("#id_registro").val(data.id_registro_visitas);
				$("#nombre_acompanante_finalizar").val(data.nombre);
				$("#primer_apellido_acompanante_finalizar").val(data.apellido);
				$("#nombre_finalizar").val(data.nombre_paciente);
				$("#primer_apellido_finalizar").val(data.apellido_paciente);
				if(data.nombre != ""){
					$("#nombre_acompanante_finalizar").prop('readonly', true);
				}
				if($("#nombre_acompanante_finalizar").val() == ""){
					$("#nombre_acompanante_finalizar").prop('readonly', false);
				}

				if(data.apellido != ""){
					$("#primer_apellido_acompanante_finalizar").prop('readonly', true);
				}
				if($("#primer_apellido_acompanante_finalizar").val() == ""){
					$("#primer_apellido_acompanante_finalizar").prop('readonly', false);
				}

				$("#n_identificacion_acompanante_finalizar").prop('readonly', true);
				$("#tipo_identificacion_acompanante_finalizar").css('pointer-events','none');
				
			},
			error: function(error){
				Swal.close();
				console.log(error);
			}
		});
	});
	function buscarPaciente(){
		var identificacion = obtener_identificacion(false);
		
		swalCargando.fire({title:'Cargando datos del paciente'});
		$.ajax({
			url: "{{URL::to('/buscarCaso')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				tipo_identificacion: identificacion.tipo,
				n_identificacion: identificacion.n_identificacion
			},
			dataType: "json",
			type: "post",
			success: function(data){
				
				Swal.close();

				if(!data.id_caso)
				{
					if(data.msg)
					{
						swalInfo2.fire({
							title: 'Información',
							text: data.msg
						}).then(function(){
							location.reload(true);
						});
					}
					else{
						swalInfo2.fire({
							title: 'Información',
							text: "No se ha encontrado el paciente"
						});
					}
					return;
				}
				if(data.recibe_visitas === false)
				{
					swalInfo2.fire({
						title: 'Información',
						text: "El paciente no puede recibir visitas"
					});
					return;
				}
				cargarDatosPaciente(data);
				deshabilitarBuscarPaciente();
				
				revalidar();
				
			},
			error: function(error){
				Swal.close();
				console.log(error);
			}
		});
	}
	function buscarPacientePorNombre(){
		swalCargando.fire({title:'Cargando datos del paciente'});
		$.ajax({
			url: "{{URL::to('/buscarCasoPorNombre')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				tipo_identificacion: "nombre",
				nombre: $("#nombre").val(),
				apellido: $("#primer_apellido").val()
			},
			dataType: "json",
			type: "post",
			success: function(data){
				
				Swal.close();
				if(data.msg)
				{
					swalInfo2.fire({
						title: 'Información',
						text: data.msg
					}).then(function(){
						location.reload(true);
					});
				}
				else if(data.length === 0){
					swalInfo2.fire({
						title: 'Información',
						text: "No se ha encontrado el paciente"
					});
				}
				else if(data.length === 1){
					if(data[0].permite_visitas === false){
						swalInfo2.fire({
							title: 'Información',
							text: "El paciente ha alcanzado el máximo de visitas permitidas."
						}).then(function(){
							location.reload(true);
						});
					}
					else if(data[0].recibe_visitas === false){
						swalInfo2.fire({
							title: 'Información',
							text: "El paciente no puede recibir visitas"
						});
					}
					else{
						cargarDatosPaciente(data[0]);
						deshabilitarBuscarPaciente();
					}
				}
				else{
					cargarTabla(data);
					$("#modalBuscarPacientePorNombre").modal("show");
				}
				revalidar();
				
			},
			error: function(error){
				Swal.close();
				console.log(error);
			}
		});
	}
	function cargarDatosPaciente(data){
		if(data.rn === "si")
		{
			$("#n_identificacion").val(data.rut_madre + calcular_dv(data.rut_madre));
		}
		else if(data.identificacion === "run")
		{
			$("#n_identificacion").val(data.rut + calcular_dv(data.rut));
		}
		else if(data.identificacion === "pasaporte"){
			$("#n_identificacion").val(data.n_identificacion);
		}
		
		$("#nombre").val(data.nombre);
		$("#primer_apellido").val(data.apellido_paterno);
		$("#sala").val(data.nombre_sala);
		$("#id_sala").val(data.id_sala);
		$("#cama").val(data.nombre_cama);
		$("#id_cama").val(data.id_cama);

		$("#servicio").val(data.nombre_servicio);
		$("#id_servicio").val(data.id_servicio);
		$("#id_caso").val(data.id_caso);
		$("#id_paciente").val(data.id_paciente);

		$("#id_area").val(data.id_area_funcional);
		$("#area").val(data.nombre_area);

		if(data.nombre_sala)
		{
			$("#sala").prop("readonly",true);
		}
		if(data.nombre_cama)
		{
			$("#cama").prop("readonly",true);
		}
		if(data.nombre_servicio)
		{
			$("#servicio").prop("readonly",true);
		}
		if(data.nombre_area)
		{
			$("#area").prop("readonly",true);
		}

		$("#n_identificacion").prop('readonly', true);
		$("#nombre").prop('readonly', true);
		$("#primer_apellido").prop('readonly', true);
		$("#tipo_identificacion").css('pointer-events','none');
	}
	$("#buscar_paciente").on("click",function(){
		
		if($("#tipo_identificacion").val() == "nombre"){
			buscarPacientePorNombre();
		}
		else{
			buscarPaciente();
		}
	});
	function texto_a_rut(texto_rut)
	{
		var rut_completo=texto_rut;
		var rut_sin_formato=rut_completo.replace(/[.-]/g,"");
			
		var rut=rut_sin_formato.substring(0,rut_sin_formato.length-1);
			
		var dv=rut_sin_formato.substring(rut_sin_formato.length-1,rut_sin_formato.length);
		
		return {rut:rut,dv:dv};
	}
	function calcular_dv(valor)
	{
		var val = new String(valor);
		var s=2;
		var num=0;
		for(var i=val.length-1;i>=0;i--)
		{
			num+=parseInt(val.charAt(i))*s;
			s++;
			if(s>7)
			{
				s=2;
			}
		}
		var modulo=num%11;
		
		var dv=11-modulo;
		if(dv==10)
		{
			return 'K';
		}
		else if(dv==11)
		{
			return '0';
		}
		return new String(dv);
		
	}
	function validar_rut(rut)
	{
		var r = texto_a_rut(rut);
		
		return r.dv.toString().toLowerCase() == calcular_dv(r.rut).toString().toLowerCase();

		
	}

	function validarRutAcompanante(rut){
		$.ajax({
			url: "{{URL::to('/getPaciente')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				rut: rut
			},
			dataType: "json",
			type: "post",
			success: function(data){
				Swal.close();

				if(!data.nombre){
					swalInfo2.fire({
						title: 'Información',
						text: "No se han encontrado los datos de la visita, escríbalos manualmente"
					});
					return;
				}
				
				$("#nombre_acompanante").val(data.nombre);
				$("#primer_apellido_acompanante").val(data.apellidoP);
				if(data.nombre != ""){
					$("#nombre_acompanante").prop('readonly', true);
				}
				if($("#nombre_acompanante").val() == ""){
					$("#nombre_acompanante").prop('readonly', false);
				}

				if(data.apellidoP != ""){
					$("#primer_apellido_acompanante").prop('readonly', true);
				}
				if($("#primer_apellido_acompanante").val() == ""){
					$("#primer_apellido_acompanante").prop('readonly', false);
				}

				$("#n_identificacion_acompanante").prop('readonly', true);
				$("#tipo_identificacion_acompanante").css('pointer-events','none');
				revalidar();
				
			},
			error: function(error){
				Swal.close();
				console.log(error);
			}
		});
	}
	///validador
	$.fn.bootstrapValidator.validators.validarRut = {
        validate: function(validator, $field, options) {
        	var $padre = $($field).parent().parent();
        	var tipo_id = $padre.find("[id^=tipo_identificacion]").val();
        	if(tipo_id == "pasaporte" || tipo_id == "nombre"){
            	return true;
        	}
        	else{
            	return validar_rut($field.val());
        	}
        }
    };
    function validador(){
    	$("#form_registro_visitas").bootstrapValidator({
            fields:{
				n_identificacion_acompanante:{
					validators:{
						notEmpty: {
							message: 'Debe especificar la identificación'
						},
						validarRut:{
						message: 'Rut no válido'
    					},
					}
				}, 
    			n_identificacion:{
    				validators:{
    					notEmpty: {
    						message: 'Debe especificar la identificación'
    					},
    					validarRut:{
    						message: 'Rut no válido'
    					}
    				}
    			},
    			area:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			sala:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			cama:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			nombre_acompanante:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			primer_apellido_acompanante:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			nombre:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			primer_apellido:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			telefono:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			servicio:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			}
            }
        }).on('success.form.bv', function(e){
            e.preventDefault();
            $.ajax({
    			url: "{{URL::to('/guardarRegistroVisita')}}",
    			headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    			},
    			data: $(this).serialize(),
    			dataType: "json",
    			type: "post",
    			success: function(data){
    				if(data.exito){
    					swalExito.fire({
							title: "Exito!",
							text: "Se ha guardado correctamente",
							didOpen: function()  {
							setTimeout(function() {
								location.reload(true);
							}, 2000);
							},
						});
    				}
    				else if(data.error){
    					swalError.fire({
							title: "Error!",
							text: "Ha ocurrido un error",
							didOpen: function()  {
								setTimeout(function() {
								location.reload(true);
								}, 2000);
							},
  				        });
    				}else if(data.info){
						swalInfo.fire({
						title: 'Información',
						text:data.info
						});
					}
    				
    			},
    			error: function(error){
    				Swal.close();
    				console.log(error);
    			}
    		});
        });
    }
	
	function validador_finalizar(){
    	$("#form_salida").bootstrapValidator({
            fields:{
  				n_identificacion_acompanante_finalizar:{
    				validators:{
    					notEmpty: {
    						message: 'Debe especificar la identificación'
    					},
    					validarRut:{
    						message: 'Rut no válido'
    					}
    				}
    			},
      			nombre_acompanante_finalizar:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			primer_apellido_acompanante_finalizar:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			nombre_finalizar:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
    			primer_apellido_finalizar:{
    				validators:{
    					notEmpty: {
    						message: 'Este campo es obligatorio'
    					},
    				}
    			},
            }
        }).on('success.form.bv', function(e){
            e.preventDefault();
            $.ajax({
    			url: "{{URL::to('/salidaVisita')}}",
    			headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    			},
    			data: $(this).serialize(),
    			dataType: "json",
    			type: "post",
    			success: function(data){
    				if(data.exito){
    					swalExito.fire({
							title: "Exito!",
							text: "El visitante ha sido egresado",
							didOpen: function()  {
							setTimeout(function() {
								location.reload(true);
							}, 2000);
							},
						});
    				}
    				else{
    					swalError.fire({
							title: "Error!",
							text: "Ha ocurrido un error",
							didOpen: function()  {
								setTimeout(function() {
								location.reload(true);
								}, 2000);
							},
  				        });
    				}
    				
    			},
    			error: function(error){
    				Swal.close();
    				console.log(error);
    			}
    		});
        });
    }
	
	$("#tipo_identificacion").on("change",function(){
		//switch para placeholder
		switch($(this).val())
		{
			case "rut":
			case "rut_madre":
				$("#n_identificacion").attr("placeholder","Debe incluir el dígito verificador");
				break;
			case "pasaporte":
				$("#n_identificacion").attr("placeholder","");
				break;
			case "nombre":
				$("#n_identificacion").attr("placeholder","");
				break;
			default:
				$("#n_identificacion").attr("placeholder","");
		}
		//switch para habilitar/deshabilitar campos
		switch($(this).val())
		{
			case "rut":
			case "rut_madre":
			case "pasaporte":
				$("#n_identificacion").prop("readonly",false);
				$("#nombre").prop("readonly",true);
				$("#primer_apellido").prop("readonly",true);
				$("#buscar_paciente").prop("disabled",false);
				break;
			case "nombre":
				$("#n_identificacion").prop("readonly",true);
				$("#nombre").prop("readonly",false);
				$("#primer_apellido").prop("readonly",false);
				$("#buscar_paciente").prop("disabled",true);
				break;
			default:
				$("#n_identificacion").prop("readonly",false);
				$("#nombre").prop("readonly",true);
				$("#primer_apellido").prop("readonly",true);
				$("#buscar_paciente").prop("disabled",false);
		}
		$("#n_identificacion").val("");
		$("#nombre").val("");
		$("#primer_apellido").val("");
	});
	$("#nombre,#primer_apellido").on("input",function(){
		if($("#nombre").val() !== "" && $("#primer_apellido").val() !== "")
		{
			$("#buscar_paciente").prop("disabled",false);
		}
		else{
			$("#buscar_paciente").prop("disabled",true);
		}
	});
	$("#tipo_identificacion_acompanante").on("change",function(){
            if($(this).val() == "rut")
            {
                $("#n_identificacion_acompanante").attr("placeholder","Debe incluir el dígito verificador");
            }
            else{
                $("#n_identificacion_acompanante").attr("placeholder","");
            }
            $("#n_identificacion_acompanante").val("");
	});
	$("#tipo_identificacion_acompanante_finalizar").on("change",function(){
            if($(this).val() == "rut")
            {
                $("#n_identificacion_acompanante_finalizar").attr("placeholder","Debe incluir el dígito verificador");
            }
            else{
                $("#n_identificacion_acompanante_finalizar").attr("placeholder","");
            }
            $("#n_identificacion_acompanante_finalizar").val("");
	});
	validador();
	validador_finalizar();
	
});
</script>
@stop

@section("section")
<style>
.row{
	margin-bottom:12px;
}
input[type="number"]
{
    -webkit-appearance: textfield !important;
    margin: 0;
    -moz-appearance:textfield !important;
}
.separador_formulario{
	border-left-style: solid;
	border-left-width: 1px;
}
#main_{
	margin-left: 15% !important;
}
</style>

<div class="">
	<div class="col-md-12">
		<div class="col-sm-8">
			<form id="form_registro_visitas">
				<input type="hidden" id="id_caso" name="id_caso">
				<input type="hidden" id="id_paciente" name="id_paciente">
				<input type="hidden" id="id_servicio" name="id_servicio">
				<input type="hidden" id="id_area" name="id_area">
				<input type="hidden" id="id_sala" name="id_sala">
				<input type="hidden" id="id_cama" name="id_cama">
				<div class="col-md-12">
					<h4>Registrar visita</h4>
					<fieldset>
						<legend>Datos hospitalizado</legend>
						<div class="row">
							<div class="col-sm-4 form-group">
								<label>Buscar por</label>
								<select class="form-control" id="tipo_identificacion">
									<option value="rut">RUT</option>
									<option value="pasaporte">Pasaporte</option>
									<option value="rut_madre">Rut de la madre</option>
									<option value="nombre">Nombre</option>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label>Nº identificación</label>
								<input id="n_identificacion" class="form-control" name="n_identificacion" placeholder="Debe incluir el dígito verificador">
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 form-group">
								<label>Nombre</label>
								<input id="nombre" class="form-control" name="nombre" readonly>
							</div>
							<div class="col-sm-4 form-group">
								<label>Primer apellido</label>
								<input id="primer_apellido" class="form-control" name="primer_apellido" readonly>
							</div>
							<div class="col-sm-4 form-group">
								<label>&nbsp;</label><br>
								<button class="btn btn-primary" type="button" id="buscar_paciente">Buscar paciente</button>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 form-group">
								<label>Área</label>
								<input id="area" class="form-control" name="area" readonly>
							</div>
							<div class="col-sm-3 form-group">
								<label>Servicio</label>
								<input id="servicio" class="form-control" name="servicio" readonly>
							</div>
							<div class="col-sm-3 form-group">
								<label>Sala</label>
								<input id="sala" class="form-control" name="sala" readonly>
							</div>
							<div class="col-sm-3 form-group">
								<label>Cama</label>
								<input id="cama" class="form-control" name="cama" readonly>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 form-group">
								<label>Relación con el paciente</label>
								<textarea id="observaciones" class="form-control" rows="4" name="observaciones"></textarea>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>Datos acompañante</legend>
						<div class="row">
							<div class="col-sm-4 form-group">
								<label>Tipo identificación</label>
								<select id="tipo_identificacion_acompanante" class="form-control" name="tipo_identificacion_acompanante">
									<option value="rut">RUT</option>
									<option value="pasaporte">Pasaporte</option>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label>Nº identificación</label>
								<input id="n_identificacion_acompanante" class="form-control" name="n_identificacion_acompanante" placeholder="Debe incluir el dígito verificador">
							</div>
							<div class="col-sm-4 form-group">
								<label>&nbsp;</label><br>
								<button class="btn btn-primary" type="button" id="buscar_datos">Buscar datos</button>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 form-group">
								<label>Nombre</label>
								<input id="nombre_acompanante" class="form-control" name="nombre_acompanante">
							</div>
							<div class="col-sm-4 form-group">
								<label>Primer apellido</label>
								<input id="primer_apellido_acompanante" class="form-control" name="primer_apellido_acompanante">
							</div>
							<div class="col-sm-4 form-group">
								<label>Teléfono</label>
								<input id="telefono" class="form-control" name="telefono" type="number" max="999999999" min="0">
							</div>
						</div>
					</fieldset>
					<div class="row">
						<div class="col-sm-12">
							<button type="button" class="btn btn-danger pull-left" id="btn_cancelar">Limpiar formulario</button>
							<button type="submit" class="btn btn-primary pull-right" id="btn_guardar">Guardar</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-sm-4 separador_formulario">
			<form id="form_salida">
				<input type="hidden" id="id_registro" name="id_registro">
				<h4>Finalizar visita</h4>
				<fieldset>
					<legend>Datos del acompañante</legend>
					<div class="row">
						<div class="col-sm-12 form-group">
							<label>Tipo identificación</label>
							<select id="tipo_identificacion_acompanante_finalizar" class="form-control" name="tipo_identificacion_acompanante_finalizar">
								<option value="rut">RUT</option>
								<option value="pasaporte">Pasaporte</option>
							</select>
						</div>
						<div class="col-sm-12 form-group">
							<label>Nº de identificación</label>
							<input type="text" class="form-control" id="n_identificacion_acompanante_finalizar" name="n_identificacion_acompanante_finalizar">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 form-group">
							<button class="btn btn-primary" type="button" id="buscar_datos_finalizar">Buscar</button>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 form-group">
							<label>Nombre</label>
							<input id="nombre_acompanante_finalizar" class="form-control" name="nombre_acompanante_finalizar" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 form-group">
							<label>Primer apellido</label>
							<input id="primer_apellido_acompanante_finalizar" class="form-control" name="primer_apellido_acompanante_finalizar" readonly>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Datos del hospitalizado</legend>
					<div class="row">
						<div class="col-sm-12 form-group">
							<label>Nombre</label>
							<input id="nombre_finalizar" class="form-control" name="nombre_finalizar" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 form-group">
							<label>Primer apellido</label>
							<input id="primer_apellido_finalizar" class="form-control" name="primer_apellido_finalizar" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 form-group">
							<button type="submit" class="btn btn-primary" id="btn_finalizar">Finalizar visita</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<div id="modalBuscarPacientePorNombre" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Pacientes encontrados</h4>
	  </div>
	  <div class="modal-body">
		  <table id="tablaPacientesPorNombre" class="table">
			  <thead>
				  <tr>
					  <th>Nombre</th>
					  <th>Apellido paterno</th>
					  <th>Apellido materno</th>
					  <th>Nombre social</th>
					  <th>Fecha nacimiento</th>
					  <th>Acciones</th>
				  </tr>
			  </thead>
			  <tbody>
			  </tbody>
		  </table>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	  </div>
	</div>
  </div>
</div>

@stop