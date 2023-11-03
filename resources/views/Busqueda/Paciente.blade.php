@extends("Templates/template")

@section("titulo")
	Búsqueda
@stop

@section("miga")
	<li><a href="#">Búsqueda</a></li>
	<li><a href="#" onclick='location.reload()'>Búsqueda de pacientes</a></li>
@stop

@section("script")

<script>

	var urlTexto = "";

	$('#modalAñadirIndicaciones').on('hidden.bs.modal', function (e) {
		/* limpiar campos */
	});


	function verificarDiagn() {
		if($("#comDiagModal").val().length <= 0){
			$("#smallTextDiagn").show();
			$("#comDiagModal").css({"border-color":"red"});
			$("#submitDiagn").prop("disabled",true);
		}else{
			$("#smallTextDiagn").hide();
			$("#comDiagModal").css({"border-color":"none"});
			$("#submitDiagn").prop("disabled",false);
		}
	}

	function modificarDiagnosticos(idDiagnostico) {
		$("#modalEditarComentario").modal();
		/* 6633855 K */
		var valor=$("#"+idDiagnostico).html();
		$("#comDiagModal").val(valor);
		$("#idDiagn").val(idDiagnostico);
		verificarDiagn();
	}


	$('.fecha_solicitud').datetimepicker({
            locale: "es",
            format: 'DD-MM-YYYY HH:mm'
        }).on('dp.change', function (e) { 
            var dom = $(".fecha_solicitud");
            // $('#form_agregar_indicacion').bootstrapValidator('revalidateField', dom);
        });

	function verificarAIndicacion() {
		if($("#fechaIndicacion").val().length <= 0){
			$("#smallTextAFecha").show();
			$("#fechaIndicacion").css({"border-color":"red"});
		}else{
			$("#smallTextAFecha").hide();
			$("#fechaIndicacion").css("border-color","#000");
		}

		if($("#comIndicacion").val().length <= 0){
			$("#smallTextAComentario").show();
			$("#comIndicacion").css({"border-color":"red"});
		}else{
			$("#smallTextAComentario").hide();
			$("#comIndicacion").css("border-color","#000");
		}

		if($("#fechaIndicacion").val().length <= 0 || $("#comIndicacion").val().length <= 0){
			$("#submitIndicacionA").prop("disabled",true);
		}else{
			$("#submitIndicacionA").prop("disabled",false);
		}
	}

	/* Abrir modal añadir indicaciones */
	function modalAIndicaciones(idCaso) {
		$("#modalAñadirIndicaciones").modal();
		/* añadir idCaso y fecha actual */
		$(".idCasoIndicacion").val(idCaso);
		//fechaActual = moment(window._gc_now).format("MM-DD-YYYY HH:mm:ss");
		var dNow = new Date(Date.now());
		mes = ((dNow.getMonth()+1) < 10)?'0'+(dNow.getMonth()+1): (dNow.getMonth()+1);
		dia = (dNow.getDate() < 10)?'0'+dNow.getDate():dNow.getDate();
		hora =  (dNow.getHours()<10)?'0'+dNow.getHours(): dNow.getHours();
		minutos =  (dNow.getMinutes()<10)?'0'+dNow.getMinutes():dNow.getMinutes();
		segundos = (dNow.getSeconds()<10)?'0'+dNow.getSeconds():dNow.getSeconds();
		fechaActual =  dia+ '-' +mes+ '-' + dNow.getFullYear() + ' ' +hora+ ':' +minutos+":" + segundos;
		//var fechaActual = "{{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}"
		$("#fechaIndicacion").val(fechaActual);
		$("#comIndicacion").val("");
		/* mostrar boton Añadir */
		$("#submitIndicacionA").show();
		$("#submitIndicacionE").hide();
		/* titulo del modal */
		$("#tipoI").html("Añadir indicaciones medicas");
		/* url a la que debe dirigirse */
		urlTexto = "paciente/addIndicacion";
		/* verificar campos */
		verificarAIndicacion();
	}
	/* Abir un modal de edicion */
	function modalEIndicaciones(idIndicacion) {
		$("#modalAñadirIndicaciones").modal();
		/* tipo de modal */
		$("#tipoI").html("Editar indicaciones medicas");
		/* mostrar boton de editar  aunque se puede mehorar y solo cambiarle el texto y su clase*/
		$("#submitIndicacionE").show();
		$("#submitIndicacionA").hide();
		/* Cargar Valores  idIndicacion, fecha y comentario*/
		$(".idIndicacion").val(idIndicacion);
		var fechaIndica = $('#'+idIndicacion+'EIF').html();
		var comenIndica = $('#'+idIndicacion+'EIC').html();
		$("#fechaIndicacion").val(fechaIndica);
		$("#comIndicacion").val(comenIndica);
		/* url a la que debe dirigirse */
		urlTexto = "paciente/editIndicacion";
		/* verificar campos */
		verificarAIndicacion();

	}


	function infoPaciente(idPaciente){
		$.ajax({
			url: "{{ URL::route('mostrarInfo') }}/" +idPaciente,
			type: "GET",
			success: function(data){
				//console.log(data);
				$("#informacion").css({display:'initial'}).html(data);
			},
			error: function(error){
				console.log(error);
			}

		});
		return false;
	}

	function eliminarIndicaciones(idIndicacion) {
		var dialog = bootbox.dialog({
			message: "<h4>¿Desea eliminar esta indicación?</h4>",
			buttons: {
				cancel: {
					label: "No",
					className: 'btn-danger',
					callback: function(){
						//location.reload();
					}
				},
				ok: {
					label: "Si",
					className: 'btn-primary',
					callback: function(){
						$.ajax({
							url: "paciente/deleteIndicacion",
							headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
							data: {idIndicacion: idIndicacion},
							type: "post",
							success: function(data){
								//console.log("buenaaaaa: ",data)
								infoPaciente($("#IdPacienteActual").val());
								swalExito.fire({
								title: 'Exito!',
								text: data.exito,
								});
							},
							error: function(error){
								console.log("hjhj: ", error);
							}
						});
					}
				}
			}
		});
	}

	$(function(){

		//Cambiar diseño de busqueda
		$(".main").removeClass("col-md-7");
		$(".main").addClass("col-md-9");

		$("#smallTextDiagn").hide();
		$("#smallTextAComentario").hide();
		$("#smallTextAFecha").hide();

		$("#fechaIndicacion").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
    	});



		$(".submitIndicacion").on("submit", function(ev) {
			ev.preventDefault();
			$.ajax({
				url: urlTexto,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					idCaso: $(".idCasoIndicacion").val(),
					fecha: $("#fechaIndicacion").val(),
					comentario: $("#comIndicacion").val(),
					idIndicacion: $(".idIndicacion").val()
				},
				type: "post",
				dataType: "json",
				success: function(data){
					if(data.exito){
						$("#modalAñadirIndicaciones").modal('hide');
						$("#comIndicacion").val('');
						/* recarga los caso del paciente de nuevo */
						infoPaciente($("#IdPacienteActual").val());
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						});
					}
					else{
						swalError.fire({
						title: 'Error',
						text:data.error
						});
					}
				},
				error: function(error){
					swalError.fire({
						title: 'Error',
						text:"Error al ingresar indicación"
						});
					console.log(error);
				}
			});
		});
		/* verificaicon de inputs indicaciones */
		$("#fechaIndicacion").on("input",function(){
			verificarAIndicacion();
		});

		$("#comIndicacion").on("input",function(){
			verificarAIndicacion();
		});

		$(".submitDiagnModf").on("submit", function(ev) {
			ev.preventDefault();
			$.ajax({
				url: "paciente/modificarDiagnostico",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					idDiagn: $("#idDiagn").val(),
					comentario: $("#comDiagModal").val()
				},
				type: "post",
				dataType: "json",
				success: function(data){
					if(data.exito){
						$("#modalEditarComentario").modal('hide');
						/* recarga los caso del paciente de nuevo */
						infoPaciente($("#IdPacienteActual").val());
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						});
					}
					else{
						swalError.fire({
						title: 'Error',
						text:data.error
						});
					}
				},
				error: function(error){
					swalError.fire({
						title: 'Error',
						text:"Error al ingresar diagnóstico"
						});
					console.log(error);
				}
			});
		});

		$("#comDiagModal").on("input",function(){
			verificarDiagn();
		});


		$("#tipo").change(function(){
			//console.log($(this).val());
			if($(this).val() == "rut"){
				$("#smallText").show();
				$("#busquedaRut").show();
				$("#busquedaNombre").hide();
				$("#nombre_apellidos").hide();
			}else if($(this).val() == "nombre_apellido"){
				$("#smallText").hide();
				$("#busquedaRut").hide();
				$("#busquedaNombre").hide();
				$("#nombre_apellidos").show();
			}
			else{
				$("#smallText").hide();
				$("#busquedaRut").hide();
				$("#nombre_apellidos").hide();
				$("#busquedaNombre").show();
			}

		});



		$(".submitBusqueda").on("submit", function(ev) {
			$("#formBusquedaGeneral input[type='submit']").prop("disabled", true);
			$("#resultados").css({display:'none'});
			swalCargando.fire({title:'Buscando datos históricos del paciente. Espere por favor'});
			// $(".loader").show();
	    	ev.preventDefault();
	    	ev.stopPropagation();
			$("#informacion").html('');
			var tabla=$("#tablaResultadoBusqueda").dataTable();
	        tabla.fnClearTable();
			var tabla2=$("#tablaResultadoEgresados").dataTable();
	        tabla2.fnClearTable();
	        action = $(this).prop("action");
			
	        $.ajax({
	            url: action,
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
					// $(".loader").hide();
				swalCargando.close();
				Swal.hideLoading();
					$("#formBusquedaGeneral input[type='submit']").prop("disabled", false);
	            	//$("#resultados").css({display:'initial'});
					$('#resultados').show();
					if(data.pacientes){
						tabla.fnAddData(data.pacientes);
					}
					if(data.egresados){
						tabla2.fnAddData(data.egresados);
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
	                tabla.fnClearTable();
					tabla2.fnClearTable();
					$("#formBusquedaGeneral input[type='submit']").prop("disabled", false);
					swalCargando.close();
				Swal.hideLoading();
	            }
	        });
			return false;
	    });




	    $("#resultados").on("click", "a.info-paciente", function(){
			cadena = $(this).attr("href");
			cadenaArray = cadena.split('/');
    		idPaciente  = cadenaArray[cadenaArray.length - 1];
			$("#IdPacienteActual").val(idPaciente);
			infoPaciente(idPaciente);
	    	/*
				ANTES SE HACIA ASI
			 $.ajax({
	    		url: $(this).prop("href"),
	    		type: "GET",
	    		success: function(data){
	    			//console.log(data);
	    			$("#informacion").css({display:'initial'}).html(data);
	    		},
	    		error: function(error){
	    			console.log(error);
	    		}

	    	});

			*/
	    	return false;
	    });

	    function formatearRut(rut){
			if(!rut){
				return;
			}
			rut=rut.replace(/[^0-9Kk]/g,"");
			if(rut.length==1){
				return rut;
			}
				
			var dv=rut.slice(-1);
			var num=soloNumeroRut(rut);
			//var num_f=num.replace(/./g, function(c, i, a) {
			//	return i && c !== "." && ((a.length - i) % 3 === 0) ? '.' + c : c;
			//});
			return num+"-"+dv;
		}
		function soloNumeroRut(rut){
			var r=rut.slice(0,-1);
			return r.replace(/[.-]/g,"");
		}

		$("#inputRut").on("input",function(){
			$(this).val(formatearRut($(this).val()));
			if(!verificarRut($(this).val())){
				$("#submit_rut").prop("disabled",true);
				$("#inputRut").css({"border-color":"red"});
			}else{
				$("#submit_rut").prop("disabled",false);
				$("#inputRut").css({"border-color":"green"});

			}
		});

		$("#submit_nombre").prop("disabled",true);

		$("#nombre").on("input",function(){
			if($(this).val().length >= 3){
				$("#submit_nombre").prop("disabled",false);
			}else{
				$("#submit_nombre").prop("disabled",true);
			}
		});

		$("#submit_apellido").prop("disabled",true);

		$("#apellido").on("input",function(){
			if($(this).val().length >= 3){
				$("#submit_apellido").prop("disabled",false);
			}else{
				$("#submit_apellido").prop("disabled",true);
			}
		});

		function verificarRut(rut){

			var dvr=rut.slice(-1).toUpperCase();
			var num=soloNumeroRut(rut);

			var arut = new Array(8);
			var i, j, dv;
			if ((num.length) = 0 ) {
				return false;
			}else {
				for (i=1; i<9;i++) {
					arut[i]=0;
				}
				i=0
				for (j = (9-(num.length)); j<9;j++) {
					if (( num.substr(i,1) >= 0) & ( num.substr(i,1) <= 9)){
						arut[j] = num.substr(i,1); i++;
					}
				}
				if (i>0) {
					dv = 11 - (( (arut[1]*3) + (arut[2]*2) + (arut[3]*7) + (arut[4]*6) + (arut[5]*5) + (arut[6]*4) + (arut[7]*3) + (arut[8]*2) )%11)
					if (dv === 10) {
						dv = "K";
					}
					else if (dv === 11) {
						dv = "0";
					}
					 return dv==dvr;
				}
			}
			return false;
		}

	
	});

	function limpiarfecha_ingreso2(){
		$('.fecha_solicitud_cama').hide();
		$('#fecha_solicitud_cama ').val('');
		$("#fecha_solicitud_cama").data("DateTimePicker").date(null);
		}
	function fecha_ingreso_real(){
		$('.fecha_hospitalizacion').hide();
		$('#fecha_hospitalizacion ').val('');
		$("#fecha_hospitalizacion").data("DateTimePicker").date(null);
		}
	function fecha(){
		$('.fecha_asignacion').hide();
		$('#fecha_asignacion ').val('');
		$("#fecha_asignacion").data("DateTimePicker").date(null);
		}
	function indicacion_hospitalizacion(){
		$('.fecha_asignacion_medica').hide();
		$('#fecha_asignacion_medica ').val('');
		$("#fecha_asignacion_medica").data("DateTimePicker").date(null);
		}
	function fecha_termino(){
		$('.fecha_egreso').hide();
		$('#fecha_egreso ').val('');
		$("#fecha_egreso").data("DateTimePicker").date(null);
		}




		$('#fecha_solicitud_cama').datetimepicker({
				format: "DD-MM-YYYY HH:mm",
				locale: 'es'
			}).on("dp.change", function(){ 
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_solicitud_cama');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_hospitalizacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion_medica');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_egreso');
			});
		$('#fecha_hospitalizacion').datetimepicker({
				format: "DD-MM-YYYY HH:mm",
				locale: 'es'
			}).on("dp.change", function(){ 
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_solicitud_cama');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_hospitalizacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion_medica');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_egreso');
			});
		$('#fecha_asignacion').datetimepicker({
				format: "DD-MM-YYYY HH:mm",
				locale: 'es'
			}).on("dp.change", function(){ 
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_solicitud_cama');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_hospitalizacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion_medica');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_egreso');
			});
		$('#fecha_asignacion_medica').datetimepicker({
				format: "DD-MM-YYYY HH:mm",
				locale: 'es'
			}).on("dp.change", function(){ 
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_solicitud_cama');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_hospitalizacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion_medica');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_egreso');
			});
		$('#fecha_egreso').datetimepicker({
				format: "DD-MM-YYYY HH:mm",
				locale: 'es'
			}).on("dp.change", function(){ 
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_solicitud_cama');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_hospitalizacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_asignacion_medica');
				$('#formModFechas').bootstrapValidator('revalidateField', 'fecha_egreso');
			});







		function modalModificarFechas(idCaso) {
	    $.ajax({
                url: "paciente/mostrarFechas/"+idCaso,
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "get",
                success: function(data){
					if(data.error){
						swalError.fire({
							title: "Error",
							text: data.error,
						});
					}else{
						if(data[0].idCaso != null && data[0].idCaso != ''){
							$('#idCasoModFechas').val(data[0].idCaso);

						if(data[0].historialId != null && data[0].historialId != ''){
							$('#idHistorialModFechas').val(data[0].historialId);
						}else{
							$('#idHistorialModFechas').val('');
						}
						if(data[0].listaTransitoId != null && data[0].listaTransitoId != ''){
							$('#idListaTransitoModFechas').val(data[0].listaTransitoId);
						}else{
							$('#idListaTransitoModFechas').val('');
						}
						if(data[0].paciente != null && data[0].paciente != ''){
							$('#pacienteModFechas').val(data[0].paciente);
						}else{
							$('#pacienteModFechas').val('');
						}

						if(data[0].fecha_ingreso2 != null && data[0].fecha_ingreso2 != ''){
							$('#fecha_solicitud_cama').data("DateTimePicker").date(new Date(data[0].fecha_ingreso2));
							$('.fecha_solicitud_cama').show();
							$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_solicitud_cama', true);
						}else{
							limpiarfecha_ingreso2();
						}
				
			
			

						if(data[0].fecha_ingreso_real != null && data[0].fecha_ingreso_real != ''){
							$('#fecha_hospitalizacion').data("DateTimePicker").date(new Date(data[0].fecha_ingreso_real));
							$('.fecha_hospitalizacion').show();
							$('#fecha_hospitalizacion_anterior').val(data[0].fecha_ingreso_real);
							$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_hospitalizacion', true);
							$('.dato_hospitalizacion').text('('+data[0].servicio+'/'+data[0].sala+'/'+data[0].cama+')');
						}else{
							$('#fecha_hospitalizacion_anterior').val('');
							fecha_ingreso_real();
							
						}
						if(data[0].fecha != null && data[0].fecha != ''){
							$('#fecha_asignacion').data("DateTimePicker").date(new Date(data[0].fecha));
							$('.fecha_asignacion').show();
							$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_asignacion', true);

						}else{
							fecha();
						}
						if(data[0].indicacion_hospitalizacion != null && data[0].indicacion_hospitalizacion != ''){
							$('#fecha_asignacion_medica').data("DateTimePicker").date(new Date(data[0].indicacion_hospitalizacion));
							$('.fecha_asignacion_medica').show();
							$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_asignacion_medica', true);
						}else{
							indicacion_hospitalizacion();
						}
						if(data[0].fecha_termino != null && data[0].fecha_termino != ''){
							$('#fecha_egreso').data("DateTimePicker").date(new Date(data[0].fecha_termino));
							$('.fecha_egreso').show();
							$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_egreso', true);
						}else{
							fecha_termino();
						}

			

						$("#modalModificarFechas").modal();
						$("#idFechaCaso").val(idCaso);
						}else{
							$('#idCasoModFechas').val('');
							$('#idHistorialModFechas').val('');
							$('#idListaTransitoModFechas').val('');
							$('#pacienteModFechas').val('');
							limpiarfecha_ingreso2();
							fecha_ingreso_real();
							fecha();
							indicacion_hospitalizacion();
							fecha_termino();
							$('.dato_hospitalizacion').text('');
							$('#fecha_hospitalizacion_anterior').val('');
							swalInfo.fire({
							title: "Información",
							text: 'No hay datos para mostrar',
						});
						}
					
					}
                },
                error: function(error){
                    console.log(error);
                }
            });
		}



		
		$('#formModFechas').on('hide.bs.modal', function (e) {
			$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_solicitud_cama', false);
			$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_hospitalizacion', false);
			$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_asignacion', false);
			$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_asignacion_medica', false);
			$('#formModFechas').bootstrapValidator('enableFieldValidators', 'fecha_egreso', false);
			$('#idCasoModFechas').val('');
			$('#idHistorialModFechas').val('');
			$('#idListaTransitoModFechas').val('');
			$('#pacienteModFechas').val('');
			limpiarfecha_ingreso2();
			fecha_ingreso_real();
			fecha();
			indicacion_hospitalizacion();
			fecha_termino();
			$('.dato_hospitalizacion').text('');
			$('#fecha_hospitalizacion_anterior').val('');
			
		});


		$("#formModFechas").bootstrapValidator({
			excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
				fecha_solicitud_cama:{
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						remote:{
                            data: function(validator){
                                return {
                                    idCaso: validator.getFieldElements('idCaso').val(),
                                    paciente: validator.getFieldElements('paciente').val(),
                                    fecha_asignacion_medica: validator.getFieldElements('fecha_asignacion_medica').val(),
                                    fecha_asignacion: validator.getFieldElements('fecha_asignacion').val(),
                                    fecha_hospitalizacion: validator.getFieldElements('fecha_hospitalizacion').val(),
                                    fecha_egreso: validator.getFieldElements('fecha_egreso').val(),
                                };
                            },
                            url: "paciente/validarFechaSolicitud"
                        }
					}
				}, 
				fecha_hospitalizacion:{
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						remote:{
                            data: function(validator){
                                return {
                                    idCaso: validator.getFieldElements('idCaso').val(),
                                    paciente: validator.getFieldElements('paciente').val(),
									fecha_solicitud_cama: validator.getFieldElements('fecha_solicitud_cama').val(),
									fecha_asignacion: validator.getFieldElements('fecha_asignacion').val(),
									fecha_egreso: validator.getFieldElements('fecha_egreso').val(),
									fecha_asignacion_medica: validator.getFieldElements('fecha_asignacion_medica').val(),
									fecha_hospitalizacion_anterior: validator.getFieldElements('fecha_hospitalizacion_anterior').val(),
                                };
                            },
                            url: "paciente/validarModFechas"
                        }
					}
				}, 
				fecha_asignacion:{
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						remote:{
                            data: function(validator){
                                return {
                                    idCaso: validator.getFieldElements('idCaso').val(),
                                    paciente: validator.getFieldElements('paciente').val(),
									fecha_solicitud_cama: validator.getFieldElements('fecha_solicitud_cama').val(),
									fecha_hospitalizacion: validator.getFieldElements('fecha_hospitalizacion').val(),
									fecha_asignacion_medica: validator.getFieldElements('fecha_asignacion_medica').val(),
                                    fecha_egreso: validator.getFieldElements('fecha_egreso').val(),
                                };
                            },
                            url: "paciente/validarAsignacion"
                        }
					}
				}, 
				fecha_asignacion_medica:{
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						remote:{
                            data: function(validator){
                                return {
                                    idCaso: validator.getFieldElements('idCaso').val(),
                                    paciente: validator.getFieldElements('paciente').val(),
                                    fecha_solicitud_cama: validator.getFieldElements('fecha_solicitud_cama').val(),
                                    fecha_asignacion: validator.getFieldElements('fecha_asignacion').val(),
                                    fecha_hospitalizacion: validator.getFieldElements('fecha_hospitalizacion').val(),
                                    fecha_egreso: validator.getFieldElements('fecha_egreso').val(),
                                };
                            },
                            url: "paciente/validarFechaIndicacionMedica"
                        }
					}
				}, 
				fecha_egreso:{
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						remote:{
                            data: function(validator){
                                return {
                                    idCaso: validator.getFieldElements('idCaso').val(),
                                    paciente: validator.getFieldElements('paciente').val(),
									fecha_asignacion_medica: validator.getFieldElements('fecha_asignacion_medica').val(),
									fecha_solicitud_cama: validator.getFieldElements('fecha_solicitud_cama').val(),
                                    fecha_asignacion: validator.getFieldElements('fecha_asignacion').val(),
                                    fecha_hospitalizacion: validator.getFieldElements('fecha_hospitalizacion').val(),
                                };
                            },
                            url: "paciente/validarFechaEgresoBPaciente"
                        }
					}
				}, 
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            $("#submitFecha").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $.ajax({
                url: "paciente/addFechas",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
					$("#submitFecha").prop("disabled", false);
					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								$('#modalModificarFechas').modal('hide');
								$("#submitFecha").prop("disabled", false);
							}, 2000)
						},
						});
					}
					if(data.error){
						swalError.fire({
                            title: 'Error',
                            text:data.error
						}).then(function(result) {
                            if (result.isDenied) {
								$('#modalModificarFechas').modal('hide');
                            }
						});
					}
                },
                error: function(error){
                    $("#submitFecha").prop("disabled", false);
                    console.log(error);
                }
            });
        });
	
</script>

<style>
	.loader {
		position: fixed;
		left: 270px;
		top: 280px;
		width: 70%;
		height: 50%;
		z-index: 9999;
		background: url("{{URL::to('/')}}/images/default.gif") 50% 50% no-repeat #f5f5f5;
		opacity: .8;
	}

	.xs-modal{
		width: 400px !important;
	}

	.dato_hospitalizacion{
		display:block;
	}
</style>
@stop

@section("section")
<div class="row">
	<legend>Búsqueda de pacientes</legend>

	<input type="hidden" id="IdPacienteActual">

	{{ Form::open(array('route' => 'busquedaGeneral', "class" => "submitBusqueda", 'method' => 'get', 'role' => 'form', 'id' => 'formBusquedaGeneral')) }}

	{{-- <div class="col-md-12" style="margin-top: 20px;"> --}}
		<div class="col-md-3">
			{{ Form::select('tipo', array('rut'=>'Rut', /* 'nombre'=>'Nombre', */ 'nombre_apellido' => 'Nombre Completo', 'ficha'=>'Ficha'), '', array('class'=>'form-control', "id"=>"tipo") ) }}
		</div>
		<div class="col-md-3" id="busquedaNombre" hidden>
			{{ Form::text('busqueda', '', array("style" => "width:100%;", "class" => "form-control", "placeholder" => "ingrese N° de Ficha")) }}
		</div>
		<div class="col-md-3" id="busquedaRut">
			{{ Form::text('busqueda2', '', array("style" => "width:100%;", "class" => "form-control", "id"=>"inputRut", "placeholder" => "ingrese rut")) }}
			<small id="smallText" class="text">ej. 18456932-0</small>
		</div> 
		<div class="col-md-8" id="nombre_apellidos" hidden>
			<div class="col-md-4">
				{{ Form::text('nombre', '', array("style" => "width:100%;", "class" => "form-control", "placeholder" => "Nombre")) }}
			</div>
			<div class="col-md-4">
				{{ Form::text('paterno', '', array("style" => "width:100%;", "class" => "form-control", "placeholder" => "Apellido paterno")) }}
			</div>
			<div class="col-md-4">
				{{ Form::text('materno', '', array("style" => "width:100%;", "class" => "form-control", "placeholder" => "Apellido materno")) }}
			</div>
		</div>
		<div class="col-md-1">
			{{ Form::submit("Buscar", array("id" => "submit_ficha", "class" => "btn btn-primary")) }}
		</div>
	{{-- </div> --}}
	{{ Form::close() }}

	<div class="row" style="margin-top: 20px;">
		<div class="loader" hidden></div>
		<div id="resultados" class="col-md-12" style="display: none;">
			@include("Busqueda/ResultadoPaciente")
		</div>
	</div>
	<div class="row" style="margin-top: 20px;">
		<div id="informacion" class="col-md-12">
		</div>
	</div>
</div>
	
<br><br>



{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'formModFechas', 'autocomplete' => 'off')) }}
{{ Form::hidden('idCaso', '', array('class' => '', 'id'=> 'idCasoModFechas')) }}
{{ Form::hidden('idHistorial', '', array('class' => '', 'id' => 'idHistorialModFechas')) }}
{{ Form::hidden('idListaTransito', '', array('class' => '', 'id' => 'idListaTransitoModFechas')) }}
{{ Form::hidden('paciente', '', array('class' => '', 'id' => 'pacienteModFechas')) }}
<div id="modalModificarFechas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog xs-modal">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificación de Fechas</h4>
			</div>
			<div class="modal-body">
			<div class="row">
				<div class="col-md-12 fecha_asignacion_medica">
					<div class="form-group">
						<div class="col-md-12">
							<label>Fecha indicación médica de hospitalización </label>
						</div> 
						<div class="col-md-9">
							{{Form::text('fecha_asignacion_medica', null, array('id' => 'fecha_asignacion_medica', 'class' => 'form-control fecha_solicitud'))}}
						</div>
					</div>
				</div>
				<div class="col-md-12 fecha_solicitud_cama">
					<div class="form-group"> 
						<div class="col-md-12">
							<label>Fecha solicitud de cama</label>
						</div>
						<div class="col-md-9">
							{{Form::text('fecha_solicitud_cama', null, array('id' => 'fecha_solicitud_cama', 'class' => 'form-control fecha_solicitud'))}}
						</div>
					</div>
			 	 </div>
				  <div class="col-md-12 fecha_asignacion">
					<div class="form-group"> 
						<div class="col-md-12">
							<label>Fecha asignación de cama  </label>
						</div>
						<div class="col-md-9">
							{{Form::text('fecha_asignacion', null, array('id' => 'fecha_asignacion', 'class' => 'form-control fecha_solicitud'))}}
						</div>
					</div>
				</div>
				<div class="col-md-12 fecha_hospitalizacion">
					<div class="form-group"> 
						<div class="col-md-12">
							<label>Fecha hospitalización </label>
							<label class="dato_hospitalizacion"></label>
						</div>
						<div class="col-md-9">
						{{ Form::hidden('fecha_hospitalizacion_anterior', '', array('class' => '', 'id' => 'fecha_hospitalizacion_anterior')) }}
							{{Form::text('fecha_hospitalizacion', null, array('id' => 'fecha_hospitalizacion', 'class' => 'form-control fecha_solicitud'))}}
						</div>
					</div>
				</div>
				<div class="col-md-12 fecha_egreso">
					<div class="form-group"> 
						<div class="col-md-12">
							<label>Fecha egreso</label>
						</div>
						<div class="col-md-9">
							{{Form::text('fecha_egreso', null, array('id' => 'fecha_egreso', 'class' => 'form-control fecha_solicitud'))}}
						</div>
					</div>
			  	</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-1">
						{{ Form::submit("Modificar", array("class" => "btn btn-success", 'id' => 'submitFecha')) }}
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
<div id="modalEditarComentario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificación de Diagnostico</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array("url" => "modificarDiagnostico ","class" => "submitDiagnModf", 'method' => 'post', 'role' => 'form', 'id' => 'formDiagnComentario')) }}
				<div class="row">
					<div class="col-md-12">
						<input type="hidden" id="idDiagn" name="idDiagn">
						{{ Form::textarea('comDiagModal', '', array("style" => "width:100%;", "class" => "form-control", 'id' => 'comDiagModal','required' => 'required', 'rows' => '3')) }}
						<small id="smallTextDiagn" class="text" style="color:red">Debe ingresar un valor</small>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-1">
						{{ Form::submit("Modificar", array("class" => "btn btn-success", 'id' => 'submitDiagn')) }}
					</div>
				</div>
				{{ Form::close() }}

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" class="idCasoIndicacion" name="idCasoIndicacion">
<input type="hidden" class="idIndicacion" name="idIndicacion">
<div id="modalAñadirIndicaciones" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="tipoI"></h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array("url" => "addIndicacion","class" => "submitIndicacion", 'method' => 'post', 'role' => 'form', 'id' => 'formAñadirIndicacion')) }}
				<div class="row">
					<div class="col-md-12">
						<label for="fechaIndicacion" class="control-label" title="Ficha clinica">Fecha indicación : </label>
						{{ Form::text('fechaIndicacion', '', array("style" => "width:100%;", "class" => "form-control", 'id' => 'fechaIndicacion','required' => 'required', 'rows' => '3', 'disabled' => 'disabled')) }}
						<small id="smallTextAFecha" class="text" style="color:red">Debe ingresar una fecha</small>
					</div>
					<div class="col-md-12">
						<label for="comentarioIndicacion" class="control-label" title="Ficha clinica">Comentario indicación : </label>
						{{ Form::textarea('comIndicacion', '', array("style" => "width:100%;", "class" => "form-control", 'id' => 'comIndicacion','required' => 'required', 'rows' => '3')) }}
						<small id="smallTextAComentario" class="text" style="color:red">Debe ingresar un valor</small>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-1">
						{{ Form::submit("Añadir", array("class" => "btn btn-success", 'id' => 'submitIndicacionA')) }}
						{{ Form::submit("Editar", array("class" => "btn btn-warning", 'id' => 'submitIndicacionE')) }}
					</div>
				</div>
				{{ Form::close() }}

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

@stop
