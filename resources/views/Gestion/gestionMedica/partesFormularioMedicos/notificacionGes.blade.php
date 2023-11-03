<script>

	function validarFormularioGes() {
		//Validaciones formualrio GES
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "medico_direccion_ges");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "medico_ciudad_ges");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "medicoAltaGes");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "rut_medico_ges");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "dv_medico_ges");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "paciente_antecedentes_ges");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "fechaDiagGes");
		$("#validardiagnostico").bootstrapValidator("revalidateField", "ant_conf");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "nombre_representante_ges");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "rut_representante_ges");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "dv_representante_ges");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "telefono_representante_ges");
		// $("#validardiagnostico").bootstrapValidator("revalidateField", "correo_representante_ges");
	}

	function mostrarListadoDiagnosticos() {
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/mostrarDiagnosticos/{{ $caso }}",
			headers: {        
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "get",
			//dataType: "json",
			success: function(data){
				if(data.historialDiagnostico){
					console.log(data.historialDiagnostico);
					historialDiagnostico = data.historialDiagnostico;
					console.log(historialDiagnostico.length);	
					if(historialDiagnostico.length > 0){
						$('.no_hay_datos_diagnosticos_ges').prop("hidden", true);
						$('.notificacion_diagnostico_ges').prop("hidden", false);
						var htmlDiagnostico = '';
						//la variable que llevaralas opciones y estara en html
						historialDiagnostico.forEach(function(element){
							htmlDiagnostico +=  "<option value="+element.id+">"+element.diagnostico+"</option>";
						});
						$('#diagnosticoMedico').html(htmlDiagnostico);
					}else{
						$('.notificacion_diagnostico_ges').prop("hidden", true);
						$('.no_hay_datos_diagnosticos_ges').prop("hidden", false);
					}
				}
			},
			error: function(error){
				console.log(error);
				swalCargando.close();
				Swal.hideLoading();
			}
		});
	}
	function generarTablaNotificacionGes() {
		tableNotificacionGes = $("#tabledNotificacionGes").dataTable({
			"iDisplayLength": 5,
			"ordering": true,
			"searching": true,
			"ajax": {
				url: "{{URL::to('/gestionMedica')}}/mostrarDiagnosticosGes/{{ $caso }}" ,
				type: 'GET'
			},
			"oLanguage": {
				"sUrl": "{{ URL::to('/') }}/js/spanish.txt"
			},
		});
	}
	var getMedicoRut=function(rut){
		swalCargando.fire({title:'Cargando datos del paciente'});
		$.ajax({
			url: "{{URL::to('/')}}/"+rut+"/consulta_medicos_rut_completo",
			headers: {        
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "get",
			//dataType: "json",
			success: function(data){
				swalCargando.close();
				Swal.hideLoading();
				if(!jQuery.isEmptyObject(data)){
					$("[name='medicoAltaGes']").val(data.nombre_apellido);
					$("[name='id_medico_ges']").val(data.id_medico);
					$("[name='establecimiento_medico']").val(data.nombre_establecimiento);
					// $('#medico_direccion_ges').prop("disabled", false);
					// $('#medico_ciudad_ges').prop("disabled", false);
					$('#medico_direccion_ges').val("Los Carrera 1320, Copiapó, Atacama");
					$('#medico_ciudad_ges').val("Copiapó");
				}else{
					swalInfo.fire({
						title: "Información",
						text: 'El rut ingresado no esta registrado',
					});
					$("[name='medicoAltaGes']").val("");
					$("[name='id_medico_ges']").val("");
					$("[name='establecimiento_medico']").val("");
					$('#medico_direccion_ges').val("");
					$('#medico_ciudad_ges').val("");
					$('#medico_direccion_ges').prop("disabled", true);
					$('#medico_ciudad_ges').prop("disabled", true);
					$("#rut_medico_ges").val("");
					$("#dv_medico_ges").val("");
				}

				$("#validardiagnostico").bootstrapValidator("revalidateField", "medicoAltaGes");

			},
			error: function(error){
				console.log(error);
				swalCargando.close();
				Swal.hideLoading();
			}
		});
	}

	function buscarComunaGes(comuna = null){
		$.ajax({
			url: "{{URL::to('/comunas')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "region": $("#paciente_region_ges").val() },
			dataType: "json",
			type: "post",
			success: function(data){		
				var html = '';
				//la variable que llevaralas opciones y estara en html
				var html = "<select name='comuna' id='paciente_comuna_ges' class='form-control'>";
				data.forEach(function(element){
					html +=  "<option value="+element.id_comuna+">"+element.nombre_comuna+"</option>";

				});

				html += "</select>";
				//se anade al select
				$("#comunas_ges").find('#paciente_comuna_ges').remove().end().append(html);

				if(comuna != null){
					$("#paciente_comuna_ges").val(comuna);
				}

			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var t = 1;	
	var limite = 4; //para limitar a 3


	function modificar_notificacion(idFormulario){
		swalCargando.fire({title:'Cargando datos del paciente'});
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/modificar_notificacion/"+idFormulario,
			headers: {        
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "get",
			//dataType: "json",
			success: function(data){
				swalCargando.close();
				Swal.hideLoading();
				console.log(data);

				if(data.informacion){
					swalInfo2.fire({
						title: 'Error',
						text: data.informacion
					}).then(function(result) {
						tableNotificacionGes.api().ajax.reload();
						$('#diagnosticoGes').modal('hide');
						mostrarListadoDiagnosticos();
					});

				}else if(data.error){
					swalError.fire({
						title: 'Error',
						text: data.error
					}).then(function(result) {
						tableNotificacionGes.api().ajax.reload();
						$('#diagnosticoGes').modal('hide');
						mostrarListadoDiagnosticos();
					});

				}else if(data.gesNotificacion){
					
					$("#diagnosticoGes").modal();
					if(data.historialdiagnostico){
							if(data.historialdiagnostico.diagnostico != null){
								$("#diagnostico_ges").val(data.historialdiagnostico.diagnostico+" "+data.historialdiagnostico.id_cie_10);
								$("#id_diagnostico_ges").val(data.historialdiagnostico.id);
							}
							if(data.historialdiagnostico.comentario != null){
								$("#diagnostico_comentario_ges").val(data.historialdiagnostico.comentario);
							}
					}
					
					$("#id_notificacion_ges").val(data.gesNotificacion.id);

					$("#paciente_antecedentes_ges").val(data.gesNotificacion.confirmacion_diagnostico_ges);
					$('#fechaDiagGes').data("DateTimePicker").date(new Date(data.gesNotificacion.fecha));
					// $('#horaDiagGes').data("DateTimePicker").date(new Date(data.gesNotificacion.hora));
					// $('#horaDiagGes').val(data.gesNotificacion.hora);
					// console.log(horaGes);
					// $("#horaDiagGes").val(horaGes)
					
					if(data.gesNotificacion.confirmacion_tratamiento == 'confirmacion diagnostico'){
						$("#confirmacion_tratamiento").prop("checked", true);
						$("#paciente_tratamiento").prop("checked", false);
						
					}else if(data.gesNotificacion.confirmacion_tratamiento == 'paciente tratamiento'){
						$("#paciente_tratamiento").prop("checked", true);
						$("#confirmacion_tratamiento").prop("checked", false);
					}

					telefonos = data.telefonos;
					if(telefonos.length !== 0){
						telefonos.forEach(function(telefono){
							html = '<tr> <td class="row-index">'+t+'</td> <td><select name="tipo_telefono[]" class="form-control" id="tipo_telefono_'+t+'"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td> <input id="telefono_'+t+'" type="number" name="telefono[]" class="form-control"> <input id="telefono_id_'+t+'" type="number" name="telefono_id[]" hidden>  </td><td> <button class="btn btn-danger eliminar_telefono_ges" type="button" id="rn'+t+'" data-id="'+t+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
							$("#Telefonos_ges").append(html);
							$("#tipo_telefono_"+t).val(telefono.tipo);
							$("#telefono_"+t).val(telefono.telefono);
							$("#telefono_id_"+t).val(telefono.id);
							t++;
						});
					}

					if(data.infoPaciente){
						$("#paciente_nombre_ges").val(data.infoPaciente.nombre);
						$("#paciente_apellidoPat_ges").val(data.infoPaciente.apellido_paterno);
						$("#paciente_apellidoMat_ges").val(data.infoPaciente.apellido_materno);
						$("#paciente_rut_ges").val(data.infoPaciente.rut);
						$("#paciente_dv_ges").val(data.infoPaciente.dv);
						$("#paciente_sexo_ges").val(data.infoPaciente.sexo).change();
						$("#paciente_prevision_ges").val(data.prevision).change();
						// $("#paciente_telefono_ges").val(data.infoPaciente.telefono);
						//correo
						if(  data.infoPaciente.correo != null){
							$("#paciente_correo_ges").val(data.infoPaciente.correo);
						}
						//nombre de calle
						if(  data.infoPaciente.calle != null){
							$("#paciente_calle_ges").val(data.infoPaciente.calle);
						}
						// //numero de calle
						if( data.infoPaciente.numero != null){
							$("#paciente_numero_ges").val(data.infoPaciente.numero);
						}
						// //observacion
						if( data.infoPaciente.observacion != null){
							$("#paciente_observacion_ges").val(data.infoPaciente.observacion);
						}
					
					}

					if(data.region){
						$("#paciente_region_ges").val(data.region);
						buscarComunaGes(data.comuna);						
					}

					if(data.medico){
						$("[name='medicoAltaGes']").val(data.medico.nombre_apellido);
						$("[name='id_medico_ges']").val(data.medico.id_medico);
						$("[name='establecimiento_medico']").val(data.medico.nombre_establecimiento);
						// $('#medico_direccion_ges').prop("disabled", false);
						// $('#medico_ciudad_ges').prop("disabled", false);
						$('#medico_direccion_ges').val("Los Carrera 1320, Copiapó, Atacama");
					$('#medico_ciudad_ges').val("Copiapó");
						$('#rut_medico_ges').val(data.medico.rut_medico);
						$('#dv_medico_ges').val(data.medico.dv_medico);
					}

					if(data.representante){
						$("#nombre_representante_ges").val(data.representante.nombre_completo);
						$("#rut_representante_ges").val(data.representante.rut);
						$("#dv_representante_ges").val(data.representante.dv);
						$("#telefono_representante_ges").val(data.representante.telefono);
						$("#correo_representante_ges").val(data.representante.correo);
					}
				}
				// $("#validardiagnostico").bootstrapValidator("revalidateField", "medicoAltaGes");
			},
			error: function(error){
				console.log(error);
				swalCargando.close();
				Swal.hideLoading();
			}
		});
	}

	function eliminar_notificacion(idFormulario){
		swalPregunta.fire({
			title: "¿Esta Seguro de eliminar este formulario?"
		}).then(function(result){
			if (result.isConfirmed) {
				$.ajax({
					url: "{{URL::to('/gestionMedica')}}/eliminar_notificacion/"+idFormulario,
					headers: {        
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "get",
					//dataType: "json",
					success: function(data){	
						if(data.informacion){
							swalInfo2.fire({
								title: 'Error',
								text: data.informacion
							}).then(function(result) {
								tableNotificacionGes.api().ajax.reload();
								$('#diagnosticoGes').modal('hide');
								mostrarListadoDiagnosticos();
							});
							
						}
						if(data.exito){
							swalExito.fire({
								title: 'Exito!',
								text: data.exito,
								didOpen: function() {
									setTimeout(function() {
								// location . reload();
									tableNotificacionGes.api().ajax.reload();
									$('#diagnosticoGes').modal('hide');
									mostrarListadoDiagnosticos();
									}, 2000)
								},
							});
						}
						if(data.error){
							swalError.fire({
								title: 'Error',
								text: data.error
							}).then(function(result) {
								tableNotificacionGes.api().ajax.reload();
								$('#diagnosticoGes').modal('hide');
								mostrarListadoDiagnosticos();
							});
						}
				
		
					},
					error: function(error){
						console.log(error);
					}
				});
			}else{
				tableNotificacionGes.api().ajax.reload();
				$('#diagnosticoGes').modal('hide');
			}
		});

	}

	$(document).ready( function() {
		$('#diagnosticoGes').on('hide.bs.modal', function () {
			t = 1;
			$("#id_diagnostico_ges").val("");
			$("#diagnostico_ges").val("");
			$("#diagnostico_comentario_ges").val("");

			$("#establecimiento_medico").val("");
			$('#medico_direccion_ges').prop("disabled", true);
			$("#medico_direccion_ges").val("");
            $('#medico_ciudad_ges').prop("disabled", true);
			$("#medico_ciudad_ges").val("");
			$("#medicoAltaGes").val("");
			$("#id_medico_ges").val("");
			$("#rut_medico_ges").val("");
			$("#dv_medico_ges").val("");

			$("#paciente_rut_ges").val("");
			$("#paciente_dv_ges").val("");
			$("#paciente_nombre_ges").val("");
			$("#paciente_apellidoPat_ges").val("");
			$("#paciente_apellidoMat_ges").val("");
			$("#paciente_sexo_ges").val("");

			$("#paciente_sexo_ges").val("masculino").change();
			$("#paciente_prevision_ges").val("FONASA A").change();
			$("#paciente_correo_ges").val("");

			$("#Telefonos_ges").html("");
			$("#telefono_eliminado_ges").val("");
			$("#id_notificacion_ges").val("");
			
			$("#paciente_calle_ges").val("");
			$("#paciente_numero_ges").val("");
			$("#paciente_observacion_ges").val("");
			$("#paciente_region_ges").val(3).change();
			buscarComunaGes();
			$("#paciente_comuna_ges").val(3102).change();
			
			$("#paciente_antecedentes_ges").val("");
			$("#confirmacion_tratamiento").prop("checked", false);
			$("#paciente_tratamiento").prop("checked", false);
			$("#fechaDiagGes").val("");
			$("#fechaDiagGes").data("DateTimePicker").date(null);

			$("#nombre_representante_ges").val("");
			$("#rut_representante_ges").val("");
			$("#dv_representante_ges").val("");
			$("#telefono_representante_ges").val("");
			$("#correo_representante_ges").val("");
			
			// $('#validardiagnostico').bootstrapValidator('resetForm', true);
			
            // if( $('#medico_direccion_ges').val() == '' &&  $('#medico_ciudad_ges').val() == '' && $('#medicoAltaGes').val() == ''){
            //     $('#medico_direccion_ges').prop("disabled", true);
            //     $('#medico_ciudad_ges').prop("disabled", true);
            // }
        })


		$("#fomularios, #hA").click(function(){
			if (typeof tableNotificacionGes == 'undefined') {
				generarTablaNotificacionGes();
			}else{
				tableNotificacionGes.api().ajax.reload();
			}

			mostrarListadoDiagnosticos();
		});

		$('#diagnosticoGes').on('shown.bs.modal', function () {
            validarFormularioGes();
        });
      
        $("#addTelefono_ges").click(function(){
            if(t < limite){
                html = '<tr> <td class="row-index">'+t+'</td> <td><select name="tipo_telefono[]" class="form-control"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td><input id="telefono_id_'+t+'" type="number" name="telefono_id[]" hidden> <input type="number" name="telefono[]"" class="form-control" placeholder="Ingrese número de teléfono"> </td><td> <button class="btn btn-danger eliminar_telefono_ges" type="button" id="rn'+t+'" data-id="'+t+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
                $("#tablaTelefonos_ges").append(html);
                t++;
            }
        });

        $(document).on("click", ".eliminar_telefono_ges", function(e){
			// telefono_eliminado_ges
			position_telefono = $(this).attr("data-id");
			if($("#telefono_id_"+position_telefono).val() != ''){
				$("#telefono_eliminado_ges").val($("#telefono_eliminado_ges").val()+','+$("#telefono_id_"+position_telefono).val());
			console.log($("#telefono_id_"+position_telefono).val());

			}
            e.preventDefault();
            var child = $(this).closest('tr').nextAll();
            $(this).parents('tr').remove();
            t--;
        });

        $('#btndiagnosticoGes').click(function(){
			
            if( $('#diagnosticoMedico').has('option').length > 0 ) {
                $.ajax({
					url: "{{URL::to('/')}}/getDatosDiagnoticosMedico",
					type: "get",
					data: {diagnostico: $("#diagnosticoMedico").val(),caso:'{{base64_decode($caso)}}'},
					dataType: "json",
					success: function(data){
						console.log(data);
						if(data.informacion){
							swalInfo2.fire({
								title: 'Error',
								text: data.informacion
							}).then(function(result) {
								tableNotificacionGes.api().ajax.reload();
								$('#diagnosticoGes').modal('hide');
								mostrarListadoDiagnosticos();
							});
							
						}
						if(data.error){
							swalError.fire({
								title: 'Error',
								text: data.error
							}).then(function(result) {
								tableNotificacionGes.api().ajax.reload();
								$('#diagnosticoGes').modal('hide');
								mostrarListadoDiagnosticos();
							});
						}
						if(data.historialdiagnostico){
							if(data.historialdiagnostico.diagnostico != null){
								$("#diagnostico_ges").val(data.historialdiagnostico.diagnostico+" "+data.historialdiagnostico.id_cie_10);
								$("#id_diagnostico_ges").val(data.historialdiagnostico.id);
							}
							if(data.historialdiagnostico.comentario != null){
								$("#diagnostico_comentario_ges").val(data.historialdiagnostico.comentario);
							}
							telefonos = data.telefonos;
							if(telefonos.length !== 0){
								telefonos.forEach(function(telefono){
									html = '<tr> <td class="row-index">'+t+'</td> <td><select name="tipo_telefono[]" class="form-control" id="tipo_telefono_'+t+'"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td><input id="telefono_id_'+t+'" type="number" name="telefono_id[]" hidden> <input id="telefono_'+t+'" type="number" name="telefono[]" class="form-control"> </td><td> <button class="btn btn-danger eliminar_telefono_ges" type="button" id="rn'+t+'" data-id="'+t+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
									$("#Telefonos_ges").append(html);
									$("#tipo_telefono_"+t).val(telefono.tipo);
									$("#telefono_"+t).val(telefono.telefono);
									$("#telefono_id_"+t).val(telefono.id);
									t++;
								});
							}
							if(data.infoPaciente){
								$("#paciente_nombre_ges").val(data.infoPaciente.nombre);
								$("#paciente_apellidoPat_ges").val(data.infoPaciente.apellido_paterno);
								$("#paciente_apellidoMat_ges").val(data.infoPaciente.apellido_materno);

								$("#paciente_rut_ges").val(data.infoPaciente.rut);
								$("#paciente_dv_ges").val(data.infoPaciente.dv);


								$("#paciente_sexo_ges").val(data.infoPaciente.sexo).change();

								$("#paciente_prevision_ges").val(data.prevision).change();
								// $("#paciente_telefono_ges").val(data.infoPaciente.telefono);

								
								//correo
								if(  data.infoPaciente.correo != null){
									$("#paciente_correo_ges").val(data.infoPaciente.correo);
								}

								//nombre de calle
								if(  data.infoPaciente.calle != null){
									$("#paciente_calle_ges").val(data.infoPaciente.calle);
								}

								// //numero de calle
								if( data.infoPaciente.numero != null){
									$("#paciente_numero_ges").val(data.infoPaciente.numero);
								}

								// //observacion
								if( data.infoPaciente.observacion != null){
									$("#paciente_observacion_ges").val(data.infoPaciente.observacion);
								}

								if(data.region){
									$("#paciente_region_ges").val(data.region);
									buscarComunaGes(data.comuna);						
								}
							
							}
							$("#diagnosticoGes").modal();
					}
					
					},
					error: function(error){
						console.log(error);
					}
				});
              
            }else{
                swalInfo2.fire({
                    title: 'Información',
                    text: "No se puede asignar ges ya que no tiene diagnosticos registrados"
                })
            }

		}); 


		var datos_medicos = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicos'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/consulta_medicos_completo',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 50
		});

		datos_medicos.initialize();

		$('.medicos .typeahead').typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_apellido',
			source: datos_medicos.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
					'<div class="empty-message">',
						'No hay resultados',
					'</div>'
					].join('\n'),
					suggestion: function(data){
						var nombres = data;
						return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
					},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
			}
		}).on('typeahead:selected', function(event, selection){
			$("#medico").val('asdas');
			$("[name='id_medico_ges']").val(selection.id_medico);
			$("[name='rut_medico_ges']").val(selection.rut_medico);
			$("[name='dv_medico_ges']").val(selection.dv_medico);
			$("[name='establecimiento_medico']").val(selection.nombre_establecimiento);
			// $('#medico_direccion_ges').prop("disabled", false);
			// $('#medico_ciudad_ges').prop("disabled", false);

			$("#validardiagnostico").bootstrapValidator("revalidateField", "medicoAltaGes");
			$("#validardiagnostico").bootstrapValidator("revalidateField", "rut_medico_ges");
			$("#validardiagnostico").bootstrapValidator("revalidateField", "dv_medico_ges");
			// $("[name='establecimiento_medico']").val(selection.nombre_establecimiento);
			// $("[name='establecimiento_medico']").val(selection.nombre_establecimiento);
		
			//$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
		}).on('typeahead:close', function(ev, suggestion) {
			var $med=$(this).parents(".medicos").find("input[name='id_medico_ges']");
			if(!$med.val()&&$(this).val()){
				$(this).val("");
				$med.val("");
				$(this).trigger('input');
			}
		});

		$("#validardiagnostico").bootstrapValidator({
			excluded: [':disabled', 'hidden',':not(:visible)'],
			fields: {
				// medico_direccion_ges: {
				// 	trigger: 'change keyup',
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'La direccion es obligatoria'
				// 		}
				// 	}
				// },
				// medico_ciudad_ges: {
				// 	trigger: 'change keyup',
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'La ciudad es obligatoria'
				// 		}
				// 	}
				// },
				medicoAltaGes: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				rut_medico_ges: {
					trigger: 'change keyup',
					validators: {
						notEmpty: {
							message: 'El rut es obligatorio'
						},
						integer: {
							message: 'Debe ingresar solo números'
						}
					}
				},
				dv_medico_ges: {
					trigger: 'change keyup',
					validators:{
						regexp: {
							regexp: /([0-9]|k)/i,
							message: 'Dígito verificador no valido'
						},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut_medico_ges");
								var dv = $("#dv_medico_ges");
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
								else{
									getMedicoRut(rut);
								}
								return true;
							}
						}
					}
				},
				paciente_nombre_ges: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				paciente_apellidoPat_ges: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				paciente_apellidoMat_ges: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				paciente_region_ges: {
					trigger: 'change keyup',
				},
				paciente_comuna_ges: {
					trigger: 'change keyup',
				},
				paciente_antecedentes_ges: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Los antecedentes son obligatorio'
						}
					}
				},
				ant_conf: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Los antecedentes son obligatorios'
						}
					}
				},
				fechaDiagGes:{
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Debe ingresar una fecha'
						},
						callback: {
							callback: function(value, validator, $field){
								var esValidao=validarFormatoFechaHora(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				},
				// horaDiagGes: {
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'la hora es obligatoria'
				// 		}
				// 	}
				// },
				// nombre_representante_ges: {
				// 	trigger: 'change keyup',
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'el nombre es obligatorio'
				// 		}
				// 	}
				// },
				// rut_representante_ges: {
				// 	trigger: 'change keyup',
				// 	validators: {
				// 		notEmpty: {
				// 			message: 'el rut es obligatorio'
				// 		},
				// 		integer: {
				// 			message: 'el rut es obligatorio'
				// 		}
				// 	}
				// },
				// dv_representante_ges: {
				// 	trigger: 'change keyup',
				// 	validators:{
				// 		regexp: {
				// 			regexp: /([0-9]|k)/i,
				// 			message: 'dígito verificador no valido'
				// 		},
				// 		callback: {
				// 			callback: function(value, validator, $field){
				// 				var field_rut = $("#rut_representante_ges");
				// 				var dv = $("#dv_representante_ges");
				// 				if(field_rut.val() == '' && dv.val() == '') {
				// 					return true;
				// 				}
				// 				if(field_rut.val() != '' && dv.val() == ''){
				// 					return {valid: false, message: "debe ingresar el dígito verificador"};
				// 				}
				// 				if(field_rut.val() == '' && dv.val() != ''){
				// 					return {valid: false, message: "debe ingresar el run"};
				// 				}
				// 				var rut = $.trim(field_rut.val());
				// 				var esValido=esRutValido(field_rut.val(), dv.val());
				// 				if(!esValido){
				// 					return {valid: false, message: "dígito verificador no coincide con el run"};
				// 				}
				// 				return true;
				// 			}
				// 		}
				// 	}
				// },
				// telefono_representante_ges: {
				// 	trigger: 'change keyup',
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'el telefono es obligatorio'
				// 		}
				// 	}
				// }
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on('error.form.bv', function(e) {
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
				var $form = $(evt.target);
			var $button      = $form.data('bootstrapValidator').getSubmitButton();

			$("#guardarNova").attr('disabled', 'disabled');
			swalCargando.fire({});
			$.ajax({
				url: "{{URL::to('gestionMedica')}}/agregarGes",
				type: 'post',
				dataType: 'json',
				data: $form .serialize(),
			})
			.done(function(data) {
				console.log(data);
				swalCargando.close();
				Swal.hideLoading();

				if(data.exito){
					swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						// location . reload();
							tableNotificacionGes.api().ajax.reload();
							$('#diagnosticoGes').modal('hide');
							mostrarListadoDiagnosticos();
							}, 2000)
						},
					});
				}
				if(data.info){
					swalInfo2.fire({
						title: 'Error',
						text: data.info
					}).then(function(result) {
						tableNotificacionGes.api().ajax.reload();
						$('#diagnosticoGes').modal('hide');
						mostrarListadoDiagnosticos();
					});
					
				}
				if(data.error){
					swalError.fire({
						title: 'Error',
						text: data.error
					}).then(function(result) {
						tableNotificacionGes.api().ajax.reload();
						$('#diagnosticoGes').modal('hide');
						mostrarListadoDiagnosticos();
					});
				}	
			});

		});

		$('#fechaDiagGes').datetimepicker({
			format: "DD-MM-YYYY HH:mm",
			locale: 'es'
		}).on('dp.change', function (e) {
			$('#validardiagnostico').bootstrapValidator('revalidateField', 'fechaDiagGes');
		});
		
		// $('#horaDiagGes').datetimepicker({
		// 	format: 'HH:mm'
		// }).on("dp.change", function () {
		// 	$('#validardiagnostico').bootstrapValidator('revalidateField', 'horaDiagGes');
		// });

		$("#paciente_region_ges").on("change", function(){
			buscarComunaGes();
		});
	});

    // if( $('#fruit_name').has('option').length > 0 ) {}
</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    #rn_table tbody{
        counter-reset: Serial;           
    }

    table #rn_table{
        border-collapse: separate;
    }

    #rn_table tr td:first-child:before{
    counter-increment: Serial;      
    content: counter(Serial); 
    }
</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'diagnosticoForm')) }}
{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}

    <div class="formulario">
        <input type="hidden" value="" name="id_formulario_diagnostico_medico" id="id_formulario_diagnostico_medico">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row notificacion_diagnostico_ges">
					<div class="col-md-12">
						<legend>Notificación GES</legend>
						<div class="col-md-12">
							<div class="col-md-4 pl-0 pr-0"> Diagnósticos</div>
						</div>
						<div class="col-md-12">
						<div class="form-group tipo_cuidado_div col-md-4">
							<div class="tipos">
							{{ Form::select('diagnosticoMedico', array(), null, array('id' => 'diagnosticoMedico', 'class' => 'form-control')) }}
							</div>
						</div>
							<div class="col-md-2">
							<a href="#" class="btn btn-primary" id="btndiagnosticoGes">Asignar GES</a>
							</div>
						</div>
					</div>
                </div>
				<div class="row no_hay_datos_diagnosticos_ges" hidden>
					<div class="col-md-12">
						<div class="alert alert-warning text-center" role="alert">NO HAY DIAGNÓSTICOS PARA GENERAR UNA NOTIFICACIÓN GES</div>
					</div>
				</div>
                <br>
                <br>
                <legend>Listado de diagnósticos GES</legend>
                <table id="tabledNotificacionGes" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 30%">INFORMACIÓN</th>
                            <th style="width: 50%">RESUMEN GES</th>
                            <th style="width: 20%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>   
            </div>
        </div>
    </div>
{{ Form::close() }}


{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validardiagnostico', 'autocomplete' => 'off')) }}
{{ Form::hidden('idCasoGes', $caso, array('class' => 'idCasoGes')) }}
{{ Form::hidden('id_notificacion_ges',null, array('id' => 'id_notificacion_ges')) }}
{{ Form::hidden('id_diagnostico_ges',null, array('id' => 'id_diagnostico_ges')) }}
{{ Form::hidden('telefono_eliminado_ges','', array('id' => 'telefono_eliminado_ges')) }}
<div class="modal fade" id="diagnosticoGes" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
            @include('Gestion.gestionMedica.partials.FormGES')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}