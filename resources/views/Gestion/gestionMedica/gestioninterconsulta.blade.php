<script>
    var t_interconsulta = 1;

    function validarFormularioGestionInterconsulta() {
        $("#interconsultaEditForm").bootstrapValidator("revalidateField", "especialidad_interconsulta");
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'datos_clinicos_interconsulta');
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'datos_clinicos_interconsulta_otro');
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'auge_interconsulta');
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'especificar_problema_interconsulta');
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'programa_auge_interconsulta');
        $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'fund_diagnostico_interconsulta');
    }

    function generarTablaInterconsultaMedica() {
        tabledInterconsulta = $("#tableInterconsulta").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/historialDiagnosticosInterconsulta/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function generarInterconsultaPDF(id_formulario,caso){
        id = id_formulario;
        window.location.href = "{{URL::to('gestionMedica')}}/pdfInterconsulta/"+id+"/"+caso;
    }

    function buscarComunaInterconsulta(comuna_interconsulta = null){
		$.ajax({
			url: "{{URL::to('/comunas')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "region": $("#paciente_region_interconsulta").val() },
			dataType: "json",
			type: "post",
			success: function(data){		
				var html = '';
				//la variable que llevaralas opciones y estara en html
				var html = "<select name='comuna' id='paciente_comuna_interconsulta' class='form-control'>";
				data.forEach(function(element){
					html +=  "<option value="+element.id_comuna+">"+element.nombre_comuna+"</option>";

				});

				html += "</select>";
				//se anade al select
				$("#comunas_interconsulta").find('#paciente_comuna_interconsulta').remove().end().append(html);

				if(comuna_interconsulta != null){
					$("#paciente_comuna_interconsulta").val(comuna_interconsulta);
				}

			},
			error: function(error){
				console.log(error);
			}
		});
	}

    function agregandoDiagnosticos(idInterconsulta,position){
        $.ajax({
            url: "{{URL::to('gestionMedica')}}/obtenerDiagnosticosPorId/{{ $caso }}/"+idInterconsulta,
            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
            dataType: "json",
            type: "get",
            success: function(data){
                if(data.historialdiagnostico){
                    $('#diagnostico_interconsulta'+position).val(data.historialdiagnostico.diagnostico);
                    $('#comentario_diagnostico_interconsulta'+position).val(data.historialdiagnostico.comentario);
                    $('#id_diagnostico_interconsulta'+position).val(data.historialdiagnostico.id)
                }
            },error: function(error){
                    console.log(error);
            }
        });
    }

    function eliminar_interconsulta_medica(idFormulario){
		swalPregunta.fire({
			title: "¿Esta Seguro de eliminar este formulario?"
		}).then(function(result){
			if (result.isConfirmed) {
				$.ajax({
					url: "{{URL::to('/gestionMedica')}}/eliminar_interconsulta_medica/"+idFormulario,
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
								tabledInterconsulta.api().ajax.reload();
								$('#interconsultamodal').modal('hide');
							});
							
						}
						if(data.exito){
							swalExito.fire({
								title: 'Exito!',
								text: data.exito,
								didOpen: function() {
									setTimeout(function() {
								// location . reload();
                                tabledInterconsulta.api().ajax.reload();
									$('#interconsultamodal').modal('hide');
									}, 2000)
								},
							});
						}
						if(data.error){
							swalError.fire({
								title: 'Error',
								text: data.error
							}).then(function(result) {
								tabledInterconsulta.api().ajax.reload();
								$('#interconsultamodal').modal('hide');
							});
						}
				
		
					},
					error: function(error){
						console.log(error);
					}
				});
			}else{
				tabledInterconsulta.api().ajax.reload();
				$('#interconsultamodal').modal('hide');
			}
		});

	}

    function modificar_interconsulta_medica(idFormulario){
        $.ajax({
            url: "{{ URL::to('/gestionMedica')}}/modificar_interconsulta_medica/"+idFormulario,
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                
                if(data.info){
                    swalInfo2.fire({
                        title: 'Información',
                        text: data.info
                    }).then(function(result) {
                        // tabledUsoRestringido.api().ajax.reload();
                        $("#interconsultamodal").modal();
                    });
                }else if(data.error){
                    swalError.fire({
                    title: 'Error',
                    text: data.error
                    }).then(function(result) {
                        tabledInterconsulta.api().ajax.reload();
                        $('#interconsultamodal').modal('hide');
                    });
                }else{  
                    if(typeof data.infoPaciente !== 'undefined' && Object.keys(data.infoPaciente).length !== 0){
                        infoPaciente = data.infoPaciente;
                        if(data.establecimiento != ""){
                            $("#establecimiento_interconsulta").val(data.establecimiento.nombre_establecimiento);
                            $("#servicio_interconsulta").val(data.establecimiento.servicio_salud);
                            $("#unidad_interconsulta").val(data.establecimiento.unidad);

                        }
                        t_interconsulta = 1;
                        $("#Telefonos_interconsulta").empty();

                            
                        telefonos = data.telefonos;
                        if(telefonos.length !== 0){
                            telefonos.forEach(function(telefono){
                                html = '<tr> <td class="row-index">'+t_interconsulta+'</td> <td><select name="tipo_telefono_interconsulta[]" class="form-control" id="tipo_telefono_interconsulta_'+t_interconsulta+'"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td><input id="telefono_id_interconsulta_'+t_interconsulta+'" type="number" name="telefono_id_interconsulta[]" hidden> <input id="telefono_'+t_interconsulta+'" type="number" name="telefono[]" class="form-control"> </td><td> <button class="btn btn-danger eliminar_telefono_interconsulta" type="button" id="rn_interconsulta'+t_interconsulta+'" data-id="'+t_interconsulta+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
                                $("#Telefonos_interconsulta").append(html);
                                $("#tipo_telefono_interconsulta_"+t).val(telefono.tipo);
                                $("#telefono_"+t_interconsulta).val(telefono.telefono);
                                $("#telefono_id_interconsulta_"+t_interconsulta).val(telefono.id);
                                t_interconsulta++;
                            });
                        }           

                        paciente_rut_interconsulta = "";
                        if(infoPaciente.rut != null){
                            paciente_rut_interconsulta = infoPaciente.rut;
                        }
                        $("#paciente_rut_interconsulta").val(paciente_rut_interconsulta).change();

                        paciente_dv_interconsulta = "";
                        if(infoPaciente.dv != null){
                            paciente_dv_interconsulta = infoPaciente.dv;
                        }
                        $("#paciente_dv_interconsulta").val(paciente_dv_interconsulta).change();
                        
                        paciente_nombre_interconsulta = "";
                        if(infoPaciente.nombre != null){
                            paciente_nombre_interconsulta = infoPaciente.nombre;
                        }
                        $("#paciente_nombre_interconsulta").val(paciente_nombre_interconsulta).change();
                        
                        paciente_pat_interconsulta = "";
                        if(infoPaciente.apellido_paterno != null){
                            paciente_pat_interconsulta = infoPaciente.apellido_paterno;
                        }
                        $("#paciente_apellidoPat_interconsulta").val(paciente_pat_interconsulta).change();
                        
                        paciente_mat_interconsulta = "";
                        if(infoPaciente.apellido_materno != null){
                            paciente_mat_interconsulta = infoPaciente.apellido_materno;
                        }
                        $("#paciente_apellidoMat_interconsulta").val(paciente_mat_interconsulta).change();
                        

                        paciente_mat_interconsulta = "masculino";
                        if(infoPaciente.sexo != null){
                            paciente_mat_interconsulta = infoPaciente.sexo;
                        }
                        $("#paciente_sexo_interconsulta").val(paciente_mat_interconsulta).change();

                        paciente_naci_interconsulta = "";
                        if(infoPaciente.fecha_nacimiento != null){
                            paciente_naci_interconsulta = infoPaciente.fecha_nacimiento;
                        }
                        $("#paciente_fecha_interconsulta").val(paciente_naci_interconsulta).change();
                        
                        paciente_edad_interconsulta = "";
                        if(data.edad != null){
                            paciente_edad_interconsulta = data.edad;
                        }
                        $("#paciente_edad_interconsulta").val(paciente_edad_interconsulta).change();

                        //nombre de calle
                        
                        if(  infoPaciente.calle != null){
                            $("#paciente_calle_interconsulta").val(infoPaciente.calle);
                        }

                        // //numero de calle
                        if( infoPaciente.numero != null){
                            $("#paciente_numero_interconsulta").val(infoPaciente.numero);
                        }

                        // //observacion
                        if( infoPaciente.observacion != null){
                            $("#paciente_observacion_interconsulta").val(infoPaciente.observacion);
                        }

                        if(data.region){
                            $("#paciente_region_interconsulta").val(data.region);
                            buscarComunaInterconsulta(data.comuna);						
                        }

                        $("#select-tipo-centro").val(data.interconsulta.tipo_centro);

                        if(data.interconsulta.tipo_centro == 'derivacion'){
                            $(".select_red_publica").removeAttr("hidden");
                            $('select[name=red_publica]').val(data.interconsulta.red_publica);
                            $('.selectpicker').selectpicker('refresh');
                            $(".select_red_privada").attr("hidden",true);
                        }else if(data.interconsulta.tipo_centro == 'traslado extra sistema'){
                            $(".select_red_privada").removeAttr("hidden");
                            $('select[name=red_privada]').val(data.interconsulta.red_privada);
                            $('.selectpicker').selectpicker('refresh');
                            $(".select_red_publica").attr("hidden",true);
                        }else{
                            $(".select_red_privada").attr("hidden",true);
                            $(".select_red_publica").attr("hidden",true);
                        }

                        $("#moduloAntimicrobianocopia_actual").empty();

                        historialdiagnostico = data.historialdiagnostico;
                        //finaliza
                        var html_diagnostico = "";
                        if(historialdiagnostico.length !== 0){
                            // html_diagnostico ="<select name='seleccion_diagnostico_interconsulta[]' id='seleccion_diagnostico_interconsulta' class='form-control selectpicker' multiple >"
                            historialdiagnostico.forEach(function(element){
                                //si cambia de grupo se debe crear una nueva cabecerA
                                html_diagnostico +=  "<option value="+element.id+">"+element.diagnostico+"</option>";
                            }); 
                            // html_diagnostico +="</select>";
                            // $('.select_diagnostico_interconsulta').html(html_diagnostico);
                            $("#seleccion_diagnostico_interconsulta").html(html_diagnostico);
                            $("#seleccion_diagnostico_interconsulta").selectpicker('refresh');
                        }


                        if(data.usuario != null){
                            $("#id_medico_interconsulta").val(data.usuario.id_usuario);
                            $("#medico_interconsulta").val(data.usuario.nombre_usuario);
                            $("#rut_medico_interconsulta").val(data.usuario.rut_usuario);
                            $("#dv_medico_interconsulta").val(data.usuario.dv_usuario);
                        }

                        if(typeof data.interconsulta !== 'undefined' && Object.keys(data.interconsulta).length !== 0){
                            data_interconsulta = data.interconsulta;

                            if( data_interconsulta.id != null){
                                $('#id_formulario_interconsulta').val(data_interconsulta.id).change();
                            }

                            if( data_interconsulta.especialidad_interconsulta != null){
                                $("#especialidad_interconsulta").val(data_interconsulta.especialidad_interconsulta).change();
                                $('#especialidad_interconsulta').selectpicker('refresh');
                            }
                           
                            if( data_interconsulta.especialidad_interconsulta_dirigido != null){
                                $("#especialidad_interconsulta_dirigido").val(data_interconsulta.especialidad_interconsulta_dirigido).change();
                                $('#especialidad_interconsulta_dirigido').selectpicker('refresh');
                            }
                            
                            if(data_interconsulta.tipo_diagnostico == 'otro'){
                                $("#paciente_otro_interconsulta").prop("checked", true);
                                $("#realizar_tratamiento_interconsulta").prop("checked", false);
                                $("#paciente_tratamiento_interconsulta").prop("checked", false);
                                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", false);
                                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', false);
                                $("#datos_clinicos_interconsulta_otro").val("");
                                if( data_interconsulta.tipo_diagnostico_otro != null){
                                    $("#datos_clinicos_interconsulta_otro").val(data_interconsulta.tipo_diagnostico_otro).change();
                                }
                            }else if(data_interconsulta.tipo_diagnostico == 'realizar Tratamiento'){
                                $("#realizar_tratamiento_interconsulta").prop("checked", true);
                                $("#paciente_otro_interconsulta").prop("checked", false);
                                $("#paciente_tratamiento_interconsulta").prop("checked", false);
                                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", false);
                                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', true);
                                $("#datos_clinicos_interconsulta_otro").val("");
                            }else if(data_interconsulta.tipo_diagnostico == 'paciente en tratamiento'){
                                $("#paciente_tratamiento_interconsulta").prop("checked", true);
                                $("#realizar_tratamiento_interconsulta").prop("checked", false);
                                $("#paciente_otro_interconsulta").prop("checked", false);
                                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", false);
                                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', true);
                                $("#datos_clinicos_interconsulta_otro").val("");
                            }else if(data_interconsulta.tipo_diagnostico == 'confirmacion Diagnóstica'){
                                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", true);
                                $("#paciente_tratamiento_interconsulta").prop("checked", false);
                                $("#realizar_tratamiento_interconsulta").prop("checked", false);
                                $("#paciente_otro_interconsulta").prop("checked", false);
                                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', true);
                                $("#datos_clinicos_interconsulta_otro").val("");
                            }else{
                                $("#paciente_tratamiento_interconsulta").prop("checked", false);
                                $("#realizar_tratamiento_interconsulta").prop("checked", false);
                                $("#paciente_otro_interconsulta").prop("checked", false);
                                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", false);
                                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', true);
                                $("#datos_clinicos_interconsulta_otro").val("");
                            }

                            if(data_interconsulta.id_diagnostico_interconsulta != null){
                                id_diagnostico_interconsulta = data_interconsulta.id_diagnostico_interconsulta.split(','); 
                                $('#seleccion_diagnostico_interconsulta').val(id_diagnostico_interconsulta);
                                $('#seleccion_diagnostico_interconsulta').selectpicker('refresh');

                                var selectedItemInterconsulta = $('#seleccion_diagnostico_interconsulta').val();
                                if(selectedItemInterconsulta != null && selectedItemInterconsulta.length == 1){
                                    $('.datos_diagnosticos_interconsulta').attr('hidden', false);
                                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();
                                    agregandoDiagnosticos(selectedItemInterconsulta,0);
                                    counterDiagnosticosInterConsulta = 1;
                                }else if(selectedItemInterconsulta != null && selectedItemInterconsulta.length > 1){
                                    counterDiagnosticosInterConsulta = 1;
                                    $('#diagnostico_interconsulta0').val("");
                                    $('#comentario_diagnostico_interconsulta0').val("");
                                    $('#id_diagnostico_interconsulta0').val("")
                                    $('.datos_diagnosticos_interconsulta').attr('hidden', false);
                                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();

                                    for (let index_interconsulta = 0; index_interconsulta < selectedItemInterconsulta.length -1; index_interconsulta++) {
                                        agregarDiagnosticoInterconsulta();                       
                                    }

                                    for (let index_interconsulta = 0; index_interconsulta < selectedItemInterconsulta.length; index_interconsulta++) {
                                        agregandoDiagnosticos(selectedItemInterconsulta[index_interconsulta],index_interconsulta);
                                    }
                                    
                                }else{
                                    $('#diagnostico_interconsulta0').val("");
                                    $('#comentario_diagnostico_interconsulta0').val("");
                                    $('#id_diagnostico_interconsulta0').val("")
                                    $('.datos_diagnosticos_interconsulta').attr('hidden', true);
                                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();
                                    counterDiagnosticosInterConsulta = 1;
                                }
                            }else{
                                $('#diagnostico_interconsulta0').val("");
                                $('#comentario_diagnostico_interconsulta0').val("");
                                $('#id_diagnostico_interconsulta0').val("")
                                $('.datos_diagnosticos_interconsulta').attr('hidden', true);
                                $( "#moduloDiagnosticoInterconsultacopia" ).empty();
                                counterDiagnosticosInterConsulta = 1;
                            }

                            if(data_interconsulta.problema_salud_auge == true){
                                $("#si_auge_interconsulta").prop("checked", true);
                                $("#no_auge_interconsulta").prop("checked", false);
                                $( ".especificar_problema_interconsulta" ).attr('hidden', false);
                                $("#especificar_problema_interconsulta").val("");
                                if( data_interconsulta.especificar_problema_salud_auge != null){
                                    $("#especificar_problema_interconsulta").val(data_interconsulta.especificar_problema_salud_auge).change();
                                }
                            }else if(data_interconsulta.problema_salud_auge == false){
                                $("#si_auge_interconsulta").prop("checked", false);
                                $("#no_auge_interconsulta").prop("checked", true);
                                $( ".especificar_problema_interconsulta" ).attr('hidden', true);
                                $("#especificar_problema_interconsulta").val("");
                            }else{
                                $("#si_auge_interconsulta").prop("checked", false);
                                $("#no_auge_interconsulta").prop("checked", false);
                                $( ".especificar_problema_interconsulta" ).attr('hidden', true);
                                $("#especificar_problema_interconsulta").val("");
                            }
                            
                            if( data_interconsulta.sub_programa_salud_auge != null){
                                $("#programa_auge_interconsulta").val(data_interconsulta.sub_programa_salud_auge).change();
                            }
                         
                            if( data_interconsulta.fundamentos_diagnostico != null){
                                $("#fund_diagnostico_interconsulta").val(data_interconsulta.fundamentos_diagnostico).change();
                            }
                           
                            if( data_interconsulta.examenes_realizados != null){
                                $("#examenes_realizados_interconsulta").val(data_interconsulta.examenes_realizados).change();
                            }
                            
                        }

                        $("#interconsultamodal").modal();
                    }else{
                        swalError.fire({
                        title: 'Error',
                        text: "Se produjo un problema"
                        }).then(function(result) {
                            tabledInterconsulta.api().ajax.reload();
                            $('#interconsultamodal').modal('hide');
                        });
                    }

                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }


    $(document).on('change', '#select-tipo-centro', function () {
      if ($("#select-tipo-centro").val() == "derivacion") {
		$(".select_red_publica").removeAttr("hidden");
		$(".select_red_privada").attr("hidden",true);
      }else if ($("#select-tipo-centro").val() == "traslado extra sistema"){
		  $(".select_red_privada").removeAttr("hidden");
		  $(".select_red_publica").attr("hidden",true);
      }else{
		$(".select_red_privada").attr("hidden",true);
		$(".select_red_publica").attr("hidden",true);
	  }
    });

    
    $(document).ready( function() {

        $("#gestionInterconsulta").click(function(){
			if (typeof tabledInterconsulta == 'undefined') {
				generarTablaInterconsultaMedica();
			}
		});
      
                    
            $("#addTelefono_interconsulta").click(function(){
                if(t_interconsulta < limite){
                    html = '<tr> <td class="row-index">'+t_interconsulta+'</td> <td><select name="tipo_telefono_interconsulta[]" class="form-control"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td><input id="telefono_id_interconsulta_'+t_interconsulta+'" type="number" name="telefono_id_interconsulta[]" hidden> <input type="number" name="telefono[]"" class="form-control" placeholder="Ingrese número de teléfono"> </td><td> <button class="btn btn-danger eliminar_telefono_interconsulta" type="button" id="rn_interconsulta'+t_interconsulta+'" data-id="'+t_interconsulta+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
                    $("#tablaTelefonos_interconsulta").append(html);
                    t_interconsulta++;
                }
            });


            $(document).on("click", ".eliminar_telefono_interconsulta", function(e){
                // telefono_eliminado_interconsulta
                position_telefono = $(this).attr("data-id");
                if($("#telefono_id_interconsulta_"+position_telefono).val() != ''){
                    $("#telefono_eliminado_interconsulta").val($("#telefono_eliminado_interconsulta").val()+','+$("#telefono_id_interconsulta_"+position_telefono).val());
                }
                e.preventDefault();
                var child = $(this).closest('tr').nextAll();
                $(this).parents('tr').remove();
                t_interconsulta--;
            });

            $( "#btnInterconsultaForm" ).click(function() {
                $('.datos_clinicos_interconsulta_otro').attr('hidden', true);
                $('.especificar_problema_interconsulta').attr('hidden', true);
                $.ajax({
                    url: "{{URL::to('gestionMedica')}}/obtenerDiagnosticosDatosPaciente/{{ $caso }}",
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                    dataType: "json",
                    type: "get",
                    success: function(data){
                        // datos_clinicos_interconsulta_otro
                        if(data.info){
                            swalInfo2.fire({
                                title: 'Información',
                                text: data.info
                            }).then(function(result) {
                                // tabledUsoRestringido.api().ajax.reload();
                                $("#interconsultamodal").modal();
                            });
                        }else{  
                            
                            if(typeof data.infoPaciente !== 'undefined' && Object.keys(data.infoPaciente).length !== 0){
                                infoPaciente = data.infoPaciente;

                                if(data.establecimiento != ""){
                                $("#establecimiento_interconsulta").val(data.establecimiento.nombre_establecimiento);
                                $("#servicio_interconsulta").val(data.establecimiento.servicio_salud);
                                $("#unidad_interconsulta").val(data.establecimiento.unidad);

                            }
                                t_interconsulta = 1;
                                $("#Telefonos_interconsulta").empty();

                                telefonos = data.telefonos;
                                if(telefonos.length !== 0){
                                    telefonos.forEach(function(telefono){
                                        html = '<tr> <td class="row-index">'+t_interconsulta+'</td> <td><select name="tipo_telefono_interconsulta[]" class="form-control" id="tipo_telefono_interconsulta_'+t_interconsulta+'"> <option value="Movil">Movil</option> <option value="Casa">Casa</option><option value="Trabajo">Trabajo</option> </select></td> <td><input id="telefono_id_interconsulta_'+t_interconsulta+'" type="number" name="telefono_id_interconsulta[]" hidden> <input id="telefono_'+t_interconsulta+'" type="number" name="telefono[]" class="form-control"> </td><td> <button class="btn btn-danger eliminar_telefono_interconsulta" type="button" id="rn_interconsulta'+t_interconsulta+'" data-id="'+t_interconsulta+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';
                                        $("#Telefonos_interconsulta").append(html);
                                        $("#tipo_telefono_interconsulta_"+t).val(telefono.tipo);
                                        $("#telefono_"+t_interconsulta).val(telefono.telefono);
                                        $("#telefono_id_interconsulta_"+t_interconsulta).val(telefono.id);
                                        t_interconsulta++;
                                    });
                                }           

                                paciente_rut_interconsulta = "";
                                if(infoPaciente.rut != null){
                                    paciente_rut_interconsulta = infoPaciente.rut;
                                }
                                $("#paciente_rut_interconsulta").val(paciente_rut_interconsulta).change();

                                paciente_dv_interconsulta = "";
                                if(infoPaciente.dv != null){
                                    paciente_dv_interconsulta = infoPaciente.dv;
                                }
                                $("#paciente_dv_interconsulta").val(paciente_dv_interconsulta).change();
                                
                                paciente_nombre_interconsulta = "";
                                if(infoPaciente.nombre != null){
                                    paciente_nombre_interconsulta = infoPaciente.nombre;
                                }
                                $("#paciente_nombre_interconsulta").val(paciente_nombre_interconsulta).change();
                              
                                paciente_pat_interconsulta = "";
                                if(infoPaciente.apellido_paterno != null){
                                    paciente_pat_interconsulta = infoPaciente.apellido_paterno;
                                }
                                $("#paciente_apellidoPat_interconsulta").val(paciente_pat_interconsulta).change();
                              
                                paciente_mat_interconsulta = "";
                                if(infoPaciente.apellido_materno != null){
                                    paciente_mat_interconsulta = infoPaciente.apellido_materno;
                                }
                                $("#paciente_apellidoMat_interconsulta").val(paciente_mat_interconsulta).change();
                              

                                paciente_mat_interconsulta = "masculino";
                                if(infoPaciente.sexo != null){
                                    paciente_mat_interconsulta = infoPaciente.sexo;
                                }
                                $("#paciente_sexo_interconsulta").val(paciente_mat_interconsulta).change();


                                paciente_naci_interconsulta = "";
                                if(infoPaciente.fecha_nacimiento != null){
                                    paciente_naci_interconsulta = infoPaciente.fecha_nacimiento;
                                }
                                $("#paciente_fecha_interconsulta").val(paciente_naci_interconsulta).change();
                              
                                paciente_edad_interconsulta = "";
                                if(data.edad != null){
                                    paciente_edad_interconsulta = data.edad;
                                }
                                $("#paciente_edad_interconsulta").val(paciente_edad_interconsulta).change();

                                //nombre de calle
                                
                                if(  infoPaciente.calle != null){
                                    $("#paciente_calle_interconsulta").val(infoPaciente.calle);
                                }

                                // //numero de calle
                                if( infoPaciente.numero != null){
                                    $("#paciente_numero_interconsulta").val(infoPaciente.numero);
                                }

                                // //observacion
                                if( infoPaciente.observacion != null){
                                    $("#paciente_observacion_interconsulta").val(infoPaciente.observacion);
                                }

                                if(data.region){
                                    $("#paciente_region_interconsulta").val(data.region);
                                    buscarComunaInterconsulta(data.comuna);						
                                }

                                $("#moduloAntimicrobianocopia_actual").empty();

                                historialdiagnostico = data.historialdiagnostico;
                                //finaliza
                                var html_diagnostico = "";
                                if(historialdiagnostico.length !== 0){
                                    // html_diagnostico ="<select name='seleccion_diagnostico_interconsulta[]' id='seleccion_diagnostico_interconsulta' class='form-control selectpicker' multiple >"
                                    historialdiagnostico.forEach(function(element){
                                        //si cambia de grupo se debe crear una nueva cabecerA
                                        html_diagnostico +=  "<option value="+element.id+">"+element.diagnostico+"</option>";
                                    }); 
                                    // html_diagnostico +="</select>";
                                    // $('.select_diagnostico_interconsulta').html(html_diagnostico);
                                    $("#seleccion_diagnostico_interconsulta").html(html_diagnostico);
                                    $("#seleccion_diagnostico_interconsulta").selectpicker('refresh');
                                }


                                if(data.usuario != null){
                                    $("#id_medico_interconsulta").val(data.usuario.id_usuario);
                                    $("#medico_interconsulta").val(data.usuario.nombre_usuario);
                                    $("#rut_medico_interconsulta").val(data.usuario.rut_usuario);
                                    $("#dv_medico_interconsulta").val(data.usuario.dv_usuario);
                                }


                                $("#interconsultamodal").modal();
                            }else{
                                swalError.fire({
                                title: 'Error',
                                text: "No existen datos para mostrar"
                                }).then(function(result) {
                                    tabledInterconsulta.api().ajax.reload();
                                    $('#interconsultamodal').modal('hide');
                                });
                            }
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            });

            $("#paciente_region_interconsulta").on("change", function(){
                buscarComunaInterconsulta();
            });


            $("#interconsultaEditForm").bootstrapValidator({
                excluded: [':disabled', 'hidden',':not(:visible)'],
                fields: {
                    datos_clinicos_interconsulta: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'El tipo de diagnostico es obligatorio'
                            }
                        }
                    },
                    datos_clinicos_interconsulta_otro: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'Debe especificar'
                            }
                        }
                    },
                    especialidad_interconsulta: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'La especialidad es obligatoria'
                            }
                        }
                    },
                    auge_interconsulta: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'Sospecha problema de salud AUGE es obligatorio'
                            }
                        }
                    },
                    especificar_problema_interconsulta: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'Especifique el problema'
                            }
                        }
                    },
                    programa_auge_interconsulta: {
                        trigger: 'change keyup',
                        validators:{
                            notEmpty: {
                                message: 'Este campo es obligatorio'
                            }
                        }
                    },
                    fund_diagnostico_interconsulta: {
                        trigger: 'change keyup',
                        validators: {
                            notEmpty: {
                                message: 'Los fundamentos de diagnostico es obligatorio'
                            },
                        }
                    },
                    tipo_centro: {
                        trigger: 'change keyup',
                        validators: {
                            notEmpty: {
                                message: 'El tipo de centro es obligatorio'
                            },
                        }
                    },
                }
            }).on('status.field.bv', function(e, data) {
                data.bv.disableSubmitButtons(false);
            }).on('error.form.bv', function(e) {
            }).on("success.form.bv", function(evt){
                evt.preventDefault(evt);
                    var $form = $(evt.target);
                var $button      = $form.data('bootstrapValidator').getSubmitButton();

                $("#guardarInterconsulta").attr('disabled', 'disabled');
                // swalCargando.fire({});
             
                swalPregunta.fire({
                    title: "¿Esta Seguro de guardar este formulario?"
                }).then(function(result){
                    // swalCargando.close();
                    // Swal.hideLoading();
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{URL::to('gestionMedica')}}/agregarinterconsulta",
                            type: 'post',
                            dataType: 'json',
                            data: $form .serialize(),
                        })
                        .done(function(data) {
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                        tabledInterconsulta.api().ajax.reload();
                                        $('#interconsultamodal').modal('hide');
                                        }, 2000)
                                    },
                                });
                            }
                            if(data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text: data.error
                                }).then(function(result) {
                                    tabledInterconsulta.api().ajax.reload();
                                    $('#interconsultamodal').modal('hide');
                                });
                            }
                        });
                    }else{
                            tabledInterconsulta.api().ajax.reload();
                            $('#interconsultamodal').modal('hide');
                    }
                });
            });

            $('#interconsultamodal').on('shown.bs.modal', function () {
                validarFormularioGestionInterconsulta();
            });

            $('#interconsultamodal').on('hide.bs.modal', function () {
                t_interconsulta = 1;
                counterDiagnosticosInterConsulta = 1;
                $("#id_formulario_interconsulta").val("");
                $("#telefono_eliminado_interconsulta").val("");

                $("#servicio_interconsulta").val("");
                $("#establecimiento_interconsulta").val("");
                $("#especialidad_interconsulta").val("");
                $("#unidad_interconsulta").val("");

                $("#paciente_rut_interconsulta").val("");
                $("#paciente_dv_interconsulta").val("");
                $("#paciente_nombre_interconsulta").val("");
                $("#paciente_apellidoPat_interconsulta").val("");
                $("#paciente_sexo_interconsulta").val("masculino").change();
                $("#paciente_fecha_interconsulta").val("");
                $("#paciente_edad_interconsulta").val("");

                $( "#Telefonos_interconsulta" ).empty();

                $("#paciente_calle_interconsulta").val("");
                $("#paciente_numero_interconsulta").val("");
                $("#paciente_observacion_interconsulta").val("");
                $("#paciente_region_interconsulta").val(3).change();
                buscarComunaInterconsulta();
                $("#paciente_comuna_interconsulta").val(3102).change();

                $("#confirmacion_diagnostico_interconsulta__interconsulta").prop("checked", false);
                $("#paciente_tratamiento_interconsulta").prop("checked", false);
                $("#realizar_tratamiento_interconsulta").prop("checked", false);
                $("#paciente_otro_interconsulta").prop("checked", false);
                $( ".datos_clinicos_interconsulta_otro" ).attr('hidden', true);
                $("#datos_clinicos_interconsulta_otro").val("");
                
                $('#diagnostico_interconsulta0').val("");
                $('#comentario_diagnostico_interconsulta0').val("");
                $('#id_diagnostico_interconsulta0').val("")
                $('.datos_diagnosticos_interconsulta').attr('hidden', true);
                $( "#moduloDiagnosticoInterconsultacopia" ).empty();

                $("#si_auge_interconsulta").prop("checked", false);
                $("#no_auge_interconsulta").prop("checked", false);
                $( ".especificar_problema_interconsulta" ).attr('hidden', true);
                $("#especificar_problema_interconsulta").val("");

                $("#programa_auge_interconsulta").val("");
                $("#fund_diagnostico_interconsulta").val("");
                $("#examenes_realizados_interconsulta").val("");

                $("#id_medico_interconsulta").val("");
                $("#medico_interconsulta").val("");
                $("#rut_medico_interconsulta").val("");
                $("#dv_medico_interconsulta").val("");

                if (typeof tabledInterconsulta == 'undefined') {
				    generarTablaInterconsultaMedica();
			    }else{
                    tabledInterconsulta.api().ajax.reload();
                }
            });

            //validar al guardar formulario
            $("#guardarInterconsulta").click(function(){
                validarFormularioGestionInterconsulta();
            });

            $("#paciente_region_interconsulta").on("change", function(){
			    buscarComunaInterconsulta();
		    });

            $("input[name=datos_clinicos_interconsulta]").on("change", function(){
                if($(this).val() == 'otro'){
                    $('.datos_clinicos_interconsulta_otro').attr('hidden', false);
                    $('#datos_clinicos_interconsulta_otro').val("");
                    $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'datos_clinicos_interconsulta_otro');
                }else{ 
                    $('.datos_clinicos_interconsulta_otro').attr('hidden', true);
                    $('#datos_clinicos_interconsulta_otro').val("");
                }
		    }); 
           
            $("input[name=auge_interconsulta]").on("change", function(){
                if($(this).val() == 'si'){
                    $('.especificar_problema_interconsulta').attr('hidden', false);
                    $('#especificar_problema_interconsulta').val("");
                    $('#interconsultaEditForm').bootstrapValidator('revalidateField', 'especificar_problema_interconsulta');
                }else{ 
                    $('.especificar_problema_interconsulta').attr('hidden', true);
                    $('#especificar_problema_interconsulta').val("");
                }
		    }); 
            $('#seleccion_diagnostico_interconsulta').change(function () {
                var selectedItemInterconsulta = $('#seleccion_diagnostico_interconsulta').val();
                if(selectedItemInterconsulta != null && selectedItemInterconsulta.length == 1){
                    $('.datos_diagnosticos_interconsulta').attr('hidden', false);
                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();
                    agregandoDiagnosticos(selectedItemInterconsulta,0);
                    counterDiagnosticosInterConsulta = 1;
                }else if(selectedItemInterconsulta != null && selectedItemInterconsulta.length > 1){
                    counterDiagnosticosInterConsulta = 1;
                    $('#diagnostico_interconsulta0').val("");
                    $('#comentario_diagnostico_interconsulta0').val("");
                    $('#id_diagnostico_interconsulta0').val("")
                    $('.datos_diagnosticos_interconsulta').attr('hidden', false);
                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();

                    for (let index_interconsulta = 0; index_interconsulta < selectedItemInterconsulta.length -1; index_interconsulta++) {
                        agregarDiagnosticoInterconsulta();                       
                    }

                    for (let index_interconsulta = 0; index_interconsulta < selectedItemInterconsulta.length; index_interconsulta++) {
                        agregandoDiagnosticos(selectedItemInterconsulta[index_interconsulta],index_interconsulta);
                    }
                    
                }else{
                    $('#diagnostico_interconsulta0').val("");
                    $('#comentario_diagnostico_interconsulta0').val("");
                    $('#id_diagnostico_interconsulta0').val("")
                    $('.datos_diagnosticos_interconsulta').attr('hidden', true);
                    $( "#moduloDiagnosticoInterconsultacopia" ).empty();
                    counterDiagnosticosInterConsulta = 1;
                }
            });

        });

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

    .diagnostico_cie101 .twitter-typeahead .twitter-typeahead:first-child{
        display:none;
    }
</style>
{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'interconsultaForm')) }}
{{ Form::hidden('idCasoInterconsulta', $caso, array('id' => 'idCasoInterconsulta')) }}
    <div class="formulario">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <legend>Ingresar interconsulta</legend>
                        <div class="col-md-12 pl-0 pr-0">
                            <div class="col-md-2 pl-0 pr-0">
                            <a href="#" class="btn btn-primary" id="btnInterconsultaForm">Generar Formulario</a>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <legend>Listado interconsulta</legend>
                <table id="tableInterconsulta" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 25%">USUARIO</th>
                            <th style="width: 50%">TIPO DIAGNOSTICO</th>
                            <th></th>
                            <th style="width: 25%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>   
            </div>
        </div>
    </div>
{{ Form::close() }}


{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'interconsultaEditForm', 'autocomplete' => 'off')) }}
{{ Form::hidden('idCasoInterconsulta', $caso, array('id' => 'idCasoInterconsulta')) }}
{{ Form::hidden('id_formulario_interconsulta', '', array('id' => 'id_formulario_interconsulta')) }}
{{ Form::hidden('telefono_eliminado_interconsulta','', array('id' => 'telefono_eliminado_interconsulta')) }}
<div class="modal fade" id="interconsultamodal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
            @include('Gestion.gestionMedica.partials.FormInterconsulta')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
