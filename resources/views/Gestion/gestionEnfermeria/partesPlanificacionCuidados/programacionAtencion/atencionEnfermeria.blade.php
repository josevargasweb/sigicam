<script>

    function cerrarAtencion(t,idAtencion) {
        var tipo_planificacion = $(t).attr('data_tipo_planificacion');
        var signos_hora_arr = ($(t).attr('data_signos_hora') !== "false") ? $(t).attr('data_signos_hora').split(",") : [];
        var is_not_check = ($(t).attr('data_is_not_check') === "true") ? true : false;
        var encabezado = "<h4>¿Está seguro de eliminar este horario de atención de enfermeria?</h4><h5>Al eliminar este horario, no se vera reflejado en RESUMEN DE PLANIFICACION DE CUIDADOS</h5>";
        var check_selector = (tipo_planificacion === "32" && signos_hora_arr.length > 0 && !is_not_check) ? "<div class='row'><div class='col-md-6'><div class='form-group'><label for='mantener_check_tras_eliminar_planificacion'>¿Desea mantener el check para el control de signos vitales tomados a las "+signos_hora_arr[0]+"?:</label><select class='form-control' id='mantener_check_tras_eliminar_planificacion'><option value='true'>Mantener el check en la hora</option><option value='false'>Descheckear la hora</option></select></div></div></div>" : "";
        bootbox.confirm({
            message: encabezado, //+check_selector,
            buttons: {
            confirm: {
                label: 'Si',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result){
                $.ajax({
                    url: "{{URL::to('/gestionEnfermeria')}}/eliminarAtencionEnfermeria",
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:  {
                        "id": idAtencion,
                        "tipo": tipo_planificacion,
                        "mantener_check_tras_eliminar_planificacion": (tipo_planificacion === "32" && signos_hora_arr.length > 0 && !is_not_check) ? $("#mantener_check_tras_eliminar_planificacion").val() : "false",
                    },
                    dataType: "json",
                    type: "post",
                    beforeSend: function () {
                    //   $('#eliminarHoraAtencion').modal('hide');
                    },
                    success: function(data){
                      var grupoId = data.tipo;
                      var opcion = data.opcion;
                        $("#btnAtencionEnf").prop("disabled", false);
                        if (data.exito) {
                            swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        $("dato").empty();
                                        $("dato2").empty();
                                        eliminarH(grupoId,opcion);
                                    }, 2700)
                                },
                            });
                            /* aactualizar tabla con pendientes */
                            
                            tableAtencionEnfermeria.api().ajax.reload();

                        }

                        if (data.error) {

                            swalError.fire({
							title: 'Error',
							text:data.error
							}).then(function(result) {
							if (result.isDenied) {
								  location . reload();
							}
							})
                        }
                    },
                    error: function(error){
                        $("#btnAtencionEnf").prop("disabled", false);
                        console.log(error);
                        tableAtencionEnfermeria.api().ajax.reload();
                    }
                });
                }
            }
        }); 
    }
    function eliminarOTerminarAtencion(estado,tipo) {

        if(estado == 1){
          titulo = 'eliminar'
          color = 'red';
        }
        
        if(estado == 2){
            titulo = 'terminar'
            color = 'green';
        }      
        swalPregunta.fire({
        title: '¿Esta seguro de <span style="color:'+color+';">'+titulo+'</span> todos los horarios de atención de enfermeria?'
         }).then(function(result){
        if (result.isConfirmed) {
            $.ajax({
                    url: "{{URL::to('/gestionEnfermeria')}}/eliminarOTerminarAtencion",
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:  {
                        "estado":estado,
                        "caso":{{$caso}},
                        "tipo": tipo,
                    },
                    dataType: "json",
                    type: "post",
                    success: function(data){
                      
                        if (data.exito) {
                            swalExito.fire({
                            title: 'Exito!',
                            text: data.exito,
                            });
                            /* aactualizar tabla con pendientes */
                            $('#eliminarHoraAtencion').modal('hide');
                            tableAtencionEnfermeria.api().ajax.reload();
                        }

                        if (data.error) {

                            swalError.fire({
							title: 'Error',
							text:data.error
							});
                        }
                    },
                    error: function(error){
                        console.log(error);
                        tableAtencionEnfermeria.api().ajax.reload();
                    }
                });
        }
        });
             
    }

    function terminarAtencion(t,idAtencion){
        var encabezado = "<h4>¿Está seguro de Terminar este horario de atención de enfermeria?</h4><h5>Al Terminar este horario quedara disponible para visualizar en RESUMEN DE PLANIFICACION DE CUIDADOS</h5>";

        bootbox.confirm({
            message: encabezado, //+check_selector,
            buttons: {
            confirm: {
                label: 'Si',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result){
                $.ajax({
                    url: "{{URL::to('/gestionEnfermeria')}}/terminarAtencionEnfermeria",
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:  {
                        "id": idAtencion,
                    },
                    dataType: "json",
                    type: "post",
                    success: function(data){
                      var grupoId = data.tipo;
                      var opcion = data.opcion;
                      
                        $("#btnAtencionEnf").prop("disabled", false);
                        if (data.exito) {
                            swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                            });
                            eliminarH(grupoId,opcion);
                            /* aactualizar tabla con pendientes */
                            tableAtencionEnfermeria.api().ajax.reload();

                        }

                        if (data.error) {

                            swalError.fire({
							title: 'Error',
							text:data.error
							}).then(function(result) {
							if (result.isDenied) {
								  location . reload();
							}
							})
                        }
                    },
                    error: function(error){
                        $("#btnAtencionEnf").prop("disabled", false);
                        console.log(error);
                        tableAtencionEnfermeria.api().ajax.reload();
                    }
                });
                }
            }
        }); 

    }

    function eliminarH(grupoId,opcion){
        $("dato").empty();
        $("dato2").empty();
        $(".botones-eliminar-editar").html('');
        $('.horario_eliminacion_tens').hide();
      var caso = {{$caso}};
      $.ajax({
          url: "{{ URL::to('/gestionEnfermeria')}}/eliminarHora",
          data: {
              caso : caso,
              grupoId : grupoId
          },
          headers: {					         
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
          },
          type: "post",
          dataType: "json",
          success: function (data) {
              //se ejecuta el modal en el caso de no existir datos para mostrar
              if(data.exito){
                $('#eliminarHoraAtencion').modal('hide');
                swalInfo2.fire({
                    title: 'Información',
                    text:data.exito,
                    showConfirmButton: false,
                    timer:3000
                });
              }else{
                $('#eliminarHoraAtencion').modal('show');
              var tipoCuidado = data.tipoCuidado;
              document.getElementById("myModalAtencion").innerHTML = tipoCuidado;
              var datoHoraE = '';
              var datoHoraT = '';

              if(opcion == 1){
                  data.horarios.forEach(myFunction);
                  function myFunction(item, index) {
                      if(data.color[index] == 'colorEnfermera'){

                        datoHoraE += '<div class="'+data.color[index]+'"><div class=""><button class="btn btn-danger botonCerrar" type="button" data_tipo_planificacion = "'+grupoId+'" data_signos_hora = "false" data_is_not_check = "false" onclick="cerrarAtencion(this,'+data.id[index]+')">X</button><div class="valorInterno">'+item+'</div></div></div>';
                      }
                      if(data.color[index] == 'colorTens'){
                        datoHoraT += '<div class="'+data.color[index]+'"><div class=""><button class="btn btn-danger botonCerrar" type="button" data_tipo_planificacion = "'+grupoId+'" data_signos_hora = "false" data_is_not_check = "false" onclick="cerrarAtencion(this,'+data.id[index]+')">X</button><div class="valorInterno">'+item+'</div></div></div>';
                      }
                  }



                  if(datoHoraE != ''){
                    $('.horario_eliminacion_enfermera').show();
                  }
                  if(datoHoraT != ''){
                    $('.horario_eliminacion_tens').show();
                  }
                  document.getElementById("dato").innerHTML = datoHoraE;
                  document.getElementById("dato2").innerHTML = datoHoraT;

                  $(".botones-eliminar-editar").html('<a type="button" class="btn btn-danger" id="btn-eliminar-todo" onclick="eliminarOTerminarAtencion('+opcion+','+grupoId+')" style="margin-top:15%;">Eliminar Todo</a>');
               
              }

              if(opcion == 2){
                data.horarios.forEach(myFunction);
                function myFunction(item, index) {
                    if(data.color[index] == 'colorEnfermera'){
                      datoHoraE += '<div class="'+data.color[index]+'"><div class=""><button class="btn btn-success botonCerrar" type="button" data_tipo_planificacion = "'+grupoId+'" data_signos_hora = "false" data_is_not_check = "false" onclick="terminarAtencion(this,'+data.id[index]+')">FIN</button><div class="valorInterno">'+item+'</div></div></div>';
                    }
                    if(data.color[index] == 'colorTens'){
                      datoHoraT += '<div class="'+data.color[index]+'"><div class=""><button class="btn btn-success botonCerrar" type="button" data_tipo_planificacion = "'+grupoId+'" data_signos_hora = "false" data_is_not_check = "false" onclick="terminarAtencion(this,'+data.id[index]+')">FIN</button><div class="valorInterno">'+item+'</div></div></div>';
                    }
                }
                if(datoHoraE != ''){
                    $('.horario_eliminacion_enfermera').show();
                  }
                  if(datoHoraT != ''){
                    $('.horario_eliminacion_tens').show();
                  }
                document.getElementById("dato").innerHTML = datoHoraE;
                document.getElementById("dato2").innerHTML = datoHoraT;
                $(".botones-eliminar-editar").html('<a type="button" class="btn btn-success" id="btn-eliminar-todo" onclick="eliminarOTerminarAtencion('+opcion+','+grupoId+')" style="margin-top:15%;">Terminar Todo</a>');
            }
              }
            
            
                
          },
          error: function (error) {
            console.log(error);
          }
        });
    }

    function modificarAtencionHoras(atencion){

      var caso = {{$caso}};
      $.ajax({
          url: "{{ URL::to('/gestionEnfermeria')}}/modificarAtencionHoras",
          data: {
              caso : caso,
              id : atencion
          },
          headers: {					         
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
          },
          type: "post",
          dataType: "json",
          success: function (data) {
            
                var array_enfermera = new Array();
                var array_tens = new Array();
                data.contenido.forEach(function(item, index) {
                   if(item.resp_atencion == 1){
                      hora = trasformar(item.horario);
                      array_enfermera.push(hora);
                    }else if(item.resp_atencion == 2){
                        hora = trasformar(item.horario);
                        array_tens.push(hora);
                   }
                });


                function trasformar(numero){
                  switch (numero) {
                      case 0:
                        return numero = '00';
                        break;
                      case 1:
                        return numero = '01';
                        break;
                      case 2:
                        return numero = '02';
                        break;
                      case 3:
                        return numero = '03';
                        break;
                      case 4:
                        return numero = '04';
                        break;
                      case 5:
                        return numero = '05';
                        break;
                      case 6:
                        return numero = '06';
                        break;
                      case 7:
                        return numero = '07';
                        break;
                      case 8:
                        return numero = '08';
                        break;
                      case 9:
                        return numero = '09';
                        break;
                      default:
                        return numero;
                        break;

                    }

                }

                if(array_enfermera.length > 0){
                    $('.horario_atencion_enferma').show();
                }
                $("#modificacion_hora_atencion_enfermera").selectpicker("val",array_enfermera);
                
                if(array_tens.length > 0){
                    $('.horario_atencion_tens').show();
                }
                $("#modificacion_hora_atencion_tens").selectpicker('val',array_tens);

                $("#tipoAtencion").val(atencion);

                $("#tipoHoraAtencion").html(data.tipoCuidado.tipo);
                $("#modificarHoraAtencion").modal('show');

          },
          error: function (error) {
            console.log(error);
          }
        });
    }

  

    function generarTablaAtencion() {
        tableAtencionEnfermeria = $("#tablePCAtencionEnfermeria").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerAtencionEnfermeria/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            /* "initComplete":  */
        });
    }

    function impirimirErroresAtencionEnfermeria (msg) {
        $(".imprimir-mensajes-atencion-enfermeria").find("ul").html('');
        $(".imprimir-mensajes-atencion-enfermeria").css('display','block');
        $.each( msg, function( key, value ) {
            $(".imprimir-mensajes-atencion-enfermeria").find("ul").append("<div style='display: flex'><i class='glyphicon glyphicon-remove' style='color: #a94442;'></i><div style='margin-left: 10px'><h4>"+value+"</h4></div></div>");
            // ('<label><i class="glyphicon glyphicon-remove"><h4>'+value+'</h4></i></label><br>');
        });
    }

    function alertaCuracionesSimples(){
      var caso = {{$caso}};

      $.ajax({
          url: "{{ URL::to('/gestionEnfermeria')}}/alertaCuracionesSimples",
          data: {
              caso : caso
          },
          headers: {					         
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
          },
          type: "post",
          dataType: "json",
          success: function (data) {
            if(data.simples > 0 && data.avanzadas > 0 || data.simples == 0 && data.avanzadas > 0 || data.simples > 0 && data.avanzadas == 0){
            $("#curacionesSimples").html('<li class="list-group-item list-group-item-danger">El paciente registra '+data.simples+' curacion/es simples y '+data.avanzadas+' curacion/es avanzadas</li>').show();
            }else{
            $("#curacionesSimples").html('<li class="list-group-item list-group-item-info">El paciente no registra curaciones</li>').show();
            }
          },
          error: function (error) {
            console.log(error);
          }
      });
    }

    function limpiarIngresarAtencion(){
        $('#tipo_c').val('');
        $('#AETipo').val('');
        $('#seleccionado_AETipo').val('');
        $("#horario1").selectpicker('refresh').change();
        $("[name='horario2[]']").selectpicker('deselectAll').change();
        $("#resp_atencion").val('').change();
        $('.tipos .typeahead').typeahead('val', '');
        $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', 'tipo_c');
    }

    function cargarVistaAtencionEnfermeria(){
        if (typeof tableAtencionEnfermeria !== 'undefined') {
            tableAtencionEnfermeria.api().ajax.reload();
            alertaCuracionesSimples();
        }else{
            generarTablaAtencion();
            alertaCuracionesSimples();
        }
    }

    $(document).ready(function() {
        $('#modificarHoraAtencion').on('hidden.bs.modal', function () {
        $('.horario_atencion_enferma').hide();
        $('.horario_atencion_tens').hide();
    });
        $('#eliminarHoraAtencion').on('hidden.bs.modal', function () {
        $('.horario_eliminacion_enfermera').hide();
        $('.horario_eliminacion_tens').hide();
    });


        $( "#planificacion" ).click(function() {
            var tabsPlanificacionCuidados = $("#tabsPlanificacionCuidados").tabs().find(".active");
            tabPC = tabsPlanificacionCuidados[0].id;

            if(tabPC == "1p"){
                console.log("tabPC atencion enfermeria: ", tabPC);
                cargarVistaAtencionEnfermeria();
            }
        });

        $( "#pAt" ).click(function() {
            cargarVistaAtencionEnfermeria();
        });

        /* $('.fechaPCAE1').datetimepicker({
            format: 'dddd',
            locale: 'es'
        }); */

        $('.fechaPCAE2').datetimepicker({
            format: 'HH'
        }).on('dp.change', function (e) {
            $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', $(this));
        });

        /* Detectar cambios en el select */
        $(document).on('change','#AESolicitado',function(){
            /* Cuando es Baño en cama y lubricación de piel habilitar select de dias de la semana */

            /* resto anotar hora */
            /* if ($("#AESolicitado").val() != 22 ) {
                $("#AEHora").removeClass("hidden disabled");
                $("#AEFecha").addClass("hidden disabled");
                $('#AEFecha').val('');
            }else{
                $("#AEFecha").removeClass("hidden disabled");
                $("#AEHora").addClass("hidden disabled");
                $('#AEHora').val('');
            } */

            $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', 'horario2[]');
        });

  
        function guardarAtencion(evt){
            evt.preventDefault(evt);
            var $form = $(evt.target);

            bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar este horario de atención de enfermeria?</h4>",
                    buttons: {
                    confirm: {
                        label: 'Si',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    
                    if(result){
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/addAtencionEnfermeria",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                
                                $("#btnAtencionEnf").prop("disabled", false);
                                tableAtencionEnfermeria.api().ajax.reload();
                                
                                if (data.exito) {
                                    limpiarIngresarAtencion();

                                    $('#PCAtencionEnfermeria').trigger("reset");
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                   
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tableAtencionEnfermeria.api().ajax.reload();
                                }

                                if(data.errores){
                                    $errors = data.errores;
                                    impirimirErroresAtencionEnfermeria(data.errores);
                                    $("#erroresModalAtencionEnfermeria").modal("show");
                                }
                            },
                            error: function(error){
                                $("#btnAtencionEnf").prop("disabled", false);
                                console.log(error);
                                tableAtencionEnfermeria.api().ajax.reload();
                            }
                        });
                    }else{
                        $("#btnAtencionEnf").prop("disabled", false);
                    }
                }
            }); 
        }

        function agregarTipoAtencion(evt){
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/addaetipo",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    
                if (data.exito) {
                    swalExito.fire({
                    title: 'Exito!',
                    text: data.exito,
                    });
                    /* actualizar tabla con atenciones */
                    limpiarIngresarAtencion();
                    tableAtencionEnfermeria.api().ajax.reload();
                }

                if (data.error) {
                    swalError.fire({
                    title: 'Error',
                    text:data.error
                    });
                    tableAtencionEnfermeria.api().ajax.reload();
                }

                },
                error: function(error){
                    // $("#btnSolicitarImagen").prop("disabled", false);
                    console.log(error);
                }
            });	
        }

        //aqui modificar//
        var datos_cuidado = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				@if($sub_categoria == 4)
				url: '{{URL::to("gestionEnfermeria/")}}/'+'%QUERY/consulta_aetipo_pediatria',	
				@else
				url: '{{URL::to("gestionEnfermeria/")}}/'+'%QUERY/consulta_aetipo',
				@endif
                wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_cuidado.initialize();

        var seleccion_tipo = '';

		$('.tipos .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_cuidado.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#AETipo").val(-1);
                $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', 'tipo_c');
                $(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
				return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Tipo Cuidado</span></div><br>"
		}
		}).on('typeahead:selected', function(event, selection){
            seleccion_tipo = selection.tipo;
			$("[name='AETipo']").val(selection.id);
			$("#seleccionado_AETipo").val(selection.id);
            $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', 'tipo_c');
		}).on('typeahead:change', function(event, selection){
            if($("#tipo_c").val() == '' &&  $("[name='AETipo']").val() != ''){
                $("[name='AETipo']").val('');
            }else if($("#tipo_c").val() != seleccion_tipo){
                $("#AETipo").val(-1);
            }else{
                $('#PCAtencionEnfermeria').bootstrapValidator('revalidateField', 'tipo_c');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos").find("input[name='AETipo']");
		if(!$med.val()&& !$(this).val()){
			$(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $med.val(-1);
        }
		});//aqui modificar//

        $("#PCAtencionEnfermeria").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'tipo_c': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#tipo_c").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar un tipo de atención"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },
                'horario2[]': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){

                                /* if ($("#AESolicitado").val() == 22) {
                                    return true;
                                }else  */
                                if($("#AEHora").val() == null){
                                    return {valid: false, message: "Debe ingresar una hora a la atención"};
                                }else{
                                    return true;
                                }
                            }
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    horario2: validator.getFieldElements('horario2[]').val()
                                };
                            },
                            url: "{{URL::to("/validar_horario2")}}"
                        }
                    }
                },
                'resp_atencion': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    resp_atencion: validator.getFieldElements('resp_atencion').val()
                                };
                            },
                            url: "{{URL::to("/validar_resp_atencion")}}"
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnAtencionEnf").prop("disabled", true);
            
            //codigo de pregunta
            evt.preventDefault(evt);
            var $form = $(evt.target);
            // guardarAtencion(evt);

            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/validar_aetipo2",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    
                    if(data.tipo == -1 && data[0] == true && typeof data.tipo_parecido !== 'undefined'){
                        swalPregunta2.fire({
			               html: "Se ha encontrado un tipo de cuidado similar o relacionado. ¿Desea agregar el nuevo cuidado <span style='color:red;'>"+ $('#tipo_c').val() +"</span> o usar el relacionado <span style='color:red;'>"+data.tipo_parecido.tipo+"</span>?"
                        }).then(function (result) {
                            if (result.isDenied) {
                                agregarTipoAtencion(evt);
                            }else if (result.isConfirmed) {
                                $('#AETipo').val(data.tipo_parecido.id);
                                $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/addAtencionEnfermeria",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnAtencionEnf").prop("disabled", false);

                                if (data.exito) {
                                    $('#PCAtencionEnfermeria').trigger("reset");
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tableAtencionEnfermeria.api().ajax.reload();
                                    limpiarIngresarAtencion();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tableAtencionEnfermeria.api().ajax.reload();
                                }

                                if(data.errores){
                                    $errors = data.errores;
                                    impirimirErroresAtencionEnfermeria(data.errores);
                                    $("#erroresModalAtencionEnfermeria").modal("show");
                                }
                            },
                            error: function(error){
                                $("#btnAtencionEnf").prop("disabled", false);
                                console.log(error);
                                tableAtencionEnfermeria.api().ajax.reload();
                            }
                        });
                            }else{
                                $("#btnAtencionEnf").prop("disabled", false);
                            }

                        });
                    }else if(data.tipo == -1 && data[0] == true && typeof data.tipo_parecido === 'undefined'){
                        swalPregunta.fire({
			               title: "¿Desea agregar <span style='color:red;'>"+ $('#tipo_c').val() +"</span> como nuevo tipo cuidado?"
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                agregarTipoAtencion(evt);
                            }
                            if(result.isDenied){
                                $("#btnAtencionEnf").prop("disabled", false);
                            }

                        });

                    }else if(data.tipo > 0){
                        $('#AETipo').val(data.tipo);
                        guardarAtencion(evt);
                    }
                },
                error: function(error){
                    // $("#btnSolicitarImagen").prop("disabled", false);
                    console.log(error);
                }
            });		
                   
               

        });

        $( "#btn-modificar-horas" ).click(function() {
               var nuevas_horas_enfermera = '';
            if($('#modificacion_hora_atencion_enfermera').val() != null){
                nuevas_horas_enfermera = $('#modificacion_hora_atencion_enfermera').val();
            }

            var nuevas_horas_tens = '';
            if($('#modificacion_hora_atencion_tens').val() != null){
                nuevas_horas_tens = $('#modificacion_hora_atencion_tens').val();
            }

            bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar este horario de atención de enfermeria?</h4>",
                    buttons: {
                    confirm: {
                        label: 'Si',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {

                    if(result){
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/modificacionHorasAtencion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                nuevas_horas_enfermera: nuevas_horas_enfermera,
                                nuevas_horas_tens: nuevas_horas_tens,
                                caso: {{$caso}},
                                tipo: $("#tipoAtencion").val()
                            },
                            dataType: "json",
                            type: "post",
                            success: function(data){
                               if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    // aactualizar tabla con pendientes
                                    $('#modificarHoraAtencion').modal('hide');
                                    tableAtencionEnfermeria.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tableAtencionEnfermeria.api().ajax.reload();
                                }
                            },
                            error: function(error){
                                $("#btnAtencionEnf").prop("disabled", false);
                                console.log(error);
                                tableAtencionEnfermeria.api().ajax.reload();
                            }
                        });
                      }else{
                          $("#btnAtencionEnf").prop("disabled", false);
                      }
                  }
              }); 
        });


    });

</script>

<style>
    #turnos thead  {
        color: #032c11 !important;
        background-color: #1E9966;
        text-align: center;
        border: 3px !important;
    }

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    .colorDefault {
        width: 70px;
        height: 60px;
        background-color: #14774e;
        font-weight: bold !important;
        color: white !important;
        border-radius: 6px;
        display: inline-block;
        margin-right:5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .colorTens {
        width: 70px;
        height: 60px;
        background-color: #A3B5FD;
        color: black !important;
        font-weight: bold !important;
        border-radius: 6px;
        display: inline-block;
        margin-right:5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .colorEnfermera {
        width: 70px;
        height: 60px;
        background-color: #0062cc;
        color: white !important;
        font-weight: bold !important;
        border-radius: 6px;
        display: inline-block;
        margin-right:5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }
	.colorMatrona {
        width: 70px;
        height: 60px;
        background-color: #8D121D;
        color: white !important;
        font-weight: bold !important;
        border-radius: 6px;
        display: inline-block;
        margin-right:5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .botonCerrar {
        width: 100%;
        height: 25px;
    }

    .valorInterno{
        padding-top: 5px;
        text-align: center;
        font-size:15px;
        color: white;
    }
    .horario_atencion_enferma,
    .horario_atencion_tens,
    .horario_eliminacion_enfermera,
    .horario_eliminacion_tens,    
    .hidden
    {
        display:none;
    }

    .mb-0{
        margin-bottom:0px;
    }


    @media (min-width: 992px){
        .tipo_cuidado_div div{
            padding-left:0;
        } 
        .offset-1 {
            margin-left: 2.333333%;
        }
    }

</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'PCAtencionEnfermeria')) }}

{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}
{{ Form::hidden('sub_categoria', $sub_categoria, ['class' => 'sub_categoria']) }}
    <div class="formulario" style="">
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>PLANIFICACIÓN DE LOS CUIDADOS DE ENFERMERÍA</h4>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <legend>Ingresar nueva atención de enfermeria</legend>
                    <div class="col-md-12">
                        <div class="col-md-4"> TIPO</div>
                        <div class="col-md-2 offset-1"> HORARIO</div>
                        <div class="col-md-3 offset-1">RESPONSABLE</div>
                    </div>

                    <div class="col-md-12">
                    <div class="form-group tipo_cuidado_div col-md-4">
						<div class="tipos">
							{{Form::text('tipo_c', null, array('id' => 'tipo_c', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('AETipo', null, array('id' => 'AETipo'))}}
                            {{Form::hidden('seleccionado_AETipo', null, array('id' => 'seleccionado_AETipo'))}}
						</div>
					</div>
                        <div class="col-md-2 offset-1">
                            <div class="form-group">
                                {{Form::select('horario1', array('Lunes' => 'Lunes', 'Martes' => 'Martes','Miércoles' => 'Miércoles','Jueves' => 'Jueves', 'Viernes' => 'Viernes', 'Sabado' => 'Sabado', 'Domingo' => 'Domingo'),null, array('class' => 'form-control hidden', 'id' => 'AEFecha'))}}
                                {{Form::select('horario2[]', [ '0'=> '00', '1' => '01', '2' => '02', '3' => '03','4'=> '04', '5' => '05', '6' => '06', '7' => '07', '8'=> '08', '9' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker', 'id' => 'AEHora', 'multiple'))}} {{-- fechaPCAE2 --}}
                            </div>
                        </div>
                        <div class="col-md-3 offset-1">
                            <div class="form-group">
								@php
								$valores_resp_atencion = array('1' => 'Enfermera', '2' => 'Tens');
								if($sub_categoria == 1){
                                    $valores_resp_atencion = array('2' => 'Tens');
									$valores_resp_atencion['3'] = "Matrona/ón";
								}
								
								@endphp
                                {{Form::select('resp_atencion', $valores_resp_atencion, null, array('id' => 'resp_atencion', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" id="btnAtencionEnf">Guardar</button>
                        </div>
                    </div>
                </div>

                <br><br>

                <div class="col-md-12">
                    <legend>Listado de atenciones de enfermeria</legend>

                    <p>Simbologia de responsables en colores</p>

                    <div class="row">
                        @if($sub_categoria != 1)
                            <div class="col-md-3">
                                <div class="form-control" style="background-color: #0062cc; color: white; font-weight: bold;">Enfermera</div>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <div class="form-control" style="background-color: #A3B5FD; color: black; font-weight: bold;">Tens</div>
                        </div>
						
                        @if($sub_categoria == 1)
                            <div class="col-md-3">
                                <div class="form-control" style="background-color: #8D121D; color: white; font-weight: bold;">Matrona/ón</div>
                            </div>
                        @endif
                    </div>


                    <table id="tablePCAtencionEnfermeria" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 20%">ATENCIÓN DE ENFERMERÍA</th>
                                <th>TURNO DÍA</th>
                                <th>TURNO NOCHE</th>
                                <th>OPCIONES</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>


            </div>
        </div>


    </div>

{{ Form::close() }}


<div class="modal fade" id="erroresModalAtencionEnfermeria" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Falta Información</h4>
        </div>
        <div class="modal-body">
         <div class="alert alert-danger imprimir-mensajes-atencion-enfermeria" style="display:none">
            <ul></ul>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="eliminarHoraAtencion" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalAtencion">Falta Información</h4>

          </div>
          <div class="modal-body">
            <div class="horario_eliminacion_enfermera">
                <div class="form-group mb-0">
                    {{Form::label('', "HORAS ASIGNADAS A ENFERMERA", array( ))}}
                </div>  
                <div class="" id="dato">

                </div>
                <br>
            </div>
            <div class="horario_eliminacion_tens">
                <div class="form-group mb-0">
                    {{Form::label('', "HORAS ASIGNADAS A TENS", array( ))}}
                </div>  
                <div class="" id="dato2">

                </div>
                <br>
            </div>
           <div class="alert alert-danger imprimir-mensajes-atencion-enfermeria" style="display:none">
              <ul></ul>
          </div>
          <div class="form-group col-md-offset-9 botones-eliminar-editar">
        </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>




<div class="modal fade" id="modificarHoraAtencion" tabindex="-1" role="dialog" aria-labelledby="modificarHoraAtencion">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 id="tipoHoraAtencion" class="modal-title">Falta Información</h4>
      </div>
      <div class="modal-body" style="padding-bottom: 0px;">
        {{ Form::hidden ('tipoAtencion', null, array('class' => '', 'id'=>'tipoAtencion') )}}   
       
        <div class="horario_atencion_enferma">
        <div class="form-group mb-0">
            {{Form::label('', "HORAS ASIGNADAS A ENFERMERA", array( ))}}
        </div>  
        <div class="form-group col-md-offset-1">
            <p>HORARIO</p>
            {{Form::select('modificacion_hora_atencion_enfermera[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker','id' => 'modificacion_hora_atencion_enfermera' ,'multiple'))}}
        </div>
        <br>
    </div>
    
    <div class="horario_atencion_tens">
        <div class="form-group mb-0">
            {{Form::label('', "HORAS ASIGNADAS A TENS", array( ))}}
        </div>
        <div class="form-group col-md-offset-1">
            <p>HORARIO</p>
            {{Form::select('modificacion_hora_atencion_tens[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker','id' => 'modificacion_hora_atencion_tens' ,'multiple'))}}
            </div>
        </div>
        
        <div class="form-group col-md-offset-10">
            <button type="button" class="btn btn-success" id="btn-modificar-horas" style="margin-top:15%;">Actualizar</button>
        </div>
        {{ Form::close() }}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
    </div>
</div>
</div>
</div>

