<script>
    function mostrarDetalleHabito(){
        detalle = document.getElementById("detalleHabito");
        checkHabito = document.getElementById("checkHabito");
        if(checkHabito.checked){
            detalle.style.display='block';
        }
        else{
            detalle.style.display='none';
            $("#detalleOtroHabito").val('');
            $("#btGuardarnAnamnesis").prop('disabled',false);
        }
    }

    // function mostrarPuebloOrigen(){

    //   var value=$("input[name='puebloind']:checked").val();

    //   if(value == "si")$("#pueblo").show("slow");
    //   else $("#pueblo").hide("slow");$(".cla_ind").attr("hidden",true);
    // }

    // $(document).on('change', '#pueblo_ind', function () {
    //   if ($("#pueblo_ind").val() == 'Otro') {
    //     $(".cla_ind").removeAttr("hidden");
    //   }else{
    //     $(".cla_ind").attr("hidden",true);
    //   }

    // });

    // function verdatoPueblo(){
    //     var caso = {{$caso}};
    //     $.ajax({
    //         url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosPueblo",
    //         data: {
    //             caso : caso
    //         },
    //         headers: {					         
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
    //         },
    //         type: "post",
    //         dataType: "json",
    //         success: function (data) {

    //             var pueblo = data['pueblo_indigena'];
    //             var detalle_pueblo = data['detalle_pueblo_indigena'];
                    
    //             if( pueblo === null || pueblo == 'Ninguno'){
    //               $("#pueblono").prop('checked', true);
    //             }else{
    //               $("#pueblosi").prop('checked', true);
    //               $("#pueblo_ind").val(pueblo);
    //               $("#pueblo").css('display','block');
    //               $("#pueblo").show("slow");
    //             }
    //             if(pueblo == 'Otro'){
    //                 $(".cla_ind").removeAttr("hidden");
    //                 $("#esp_pueblo").val(detalle_pueblo);
    //             }
    //         },
    //         error: function (error) {
    //             console.log(error);
    //         }
    //     });
    // }

    function verdatoMedicamento(){
            var caso = {{$caso}};
            $.ajax({
                url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosMedicamentos",
                data: {
                    caso : caso
                },
                headers: {					         
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                },
                type: "post",
                dataType: "json",
                success: function (data) {

                  data.forEach(function(val, idx) {
                    var nombreMedicamento = val['nombre'];
                    var id = val['id']

                    html = '<tr> <td></td> <td><input type="text" name="nombreMedicamento[]" value="'+nombreMedicamento+'" class="form-control"> <input type="hidden" name="ids[]" value="'+id+'"/> </td> <td> <button class="btn btn-danger eliminar_rn" type="button" data-id="'+id+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';

                    $( "#Medicamentos" ).append(html);
                  });
                },
                error: function (error) {
                    console.log(error);
                }
            });

        }

    function IngresarMostrarAnamnesis(){

        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosAnamnesis",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (data) {
                fecha_nacimiento = data.fecha_nacimiento;
                edad_global = (fecha_nacimiento != null) ? moment().diff(fecha_nacimiento, 'years',false) : null;
                console.log(data);
                if(data.datos_gicologico !== null || data.datos_gicologico !== ''){
                    ginecolologico = data.datos_gicologico;
                  
                    var gesta = ginecolologico.gesta;
                    if(gesta == true){
                        $("#gestaT").prop('checked', true);
                        $(".gesta").show();
                        $("#observacionGesta").val(ginecolologico.gesta_observacion);
                    }else if(gesta == false){
                        $("#gestaF").prop('checked', true);
                        $(".gesta").hide();
                        $("#observacionGesta").val("");
                    }else{
                        $("#gestaT").prop('checked', false);
                        $("#gestaF").prop('checked', false);
                        $(".gesta").hide();
                        $("#observacionGesta").val("");
                    }
                    
                    var parto = ginecolologico.parto;
                    if(parto){
                        $("#partoT").prop('checked', true);
                        $(".parto").show();
                        $("#observacionParto").val(ginecolologico.parto_observacion);
                    }else if(parto == false){
                        $("#partoF").prop('checked', true);
                        $(".parto").hide();
                        $("#observacionParto").val("");
                    }else{
                        $("#partoT").prop('checked', false);
                        $("#partoF").prop('checked', false);
                        $(".parto").hide();
                        $("#observacionParto").val("");
                    }
                    
                    var aborto = ginecolologico.aborto;
                    if(aborto){
                        $("#abortoT").prop('checked', true);
                        $(".aborto").show();
                        $("#observacionAborto").val(ginecolologico.aborto_observacion);
                    }else if(aborto == false){
                        $("#abortoF").prop('checked', true);
                        $(".aborto").hide();
                        $("#observacionAborto").val("");
                    }else{
                        $("#abortoT").prop('checked', false);
                        $("#abortoF").prop('checked', false);
                        $(".aborto").hide();
                        $("#observacionAborto").val("");
                    }
                    
                    var vaginal = ginecolologico.parto_vaginal;
                    if(vaginal){
                        $("#vaginalT").prop('checked', true);
                        $(".vaginal").show();
                        $("#observacionVaginal").val(ginecolologico.parto_vaginal_observacion);
                    }else if(vaginal == false){
                        $("#vaginalF").prop('checked', true);
                        $(".vaginal").hide();
                        $("#observacionVaginal").val("");
                    }else{
                        $("#vaginalT").prop('checked', false);
                        $("#vaginalF").prop('checked', false);
                        $(".vaginal").hide();
                        $("#observacionVaginal").val("");
                    }
                    
                    var forceps = ginecolologico.forceps;
                    if(forceps){
                        $("#forcepsT").prop('checked', true);
                    }else if(forceps == false){
                        $("#forcepsF").prop('checked', true);
                    }else{
                        $("#forcepsT").prop('checked', false);
                        $("#forcepsF").prop('checked', false);
                    }
                                        
                    var cesarias = ginecolologico.cesarias;
                    if(cesarias){
                        $("#cesariaT").prop('checked', true);
                        $(".cesaria").show();
                        $("#observacionCesaria").val(ginecolologico.cesarias_observacion);
                    }else if(cesarias == false){
                        $("#cesariaF").prop('checked', true);
                        $(".cesaria").hide();
                        $("#observacionCesaria").val("");
                    }else{
                        $("#cesariaT").prop('checked', false);
                        $("#cesariaF").prop('checked', false);
                        $(".cesaria").hide();
                        $("#observacionCesaria").val("");
                    }
                  
                    var vivos_muertos = ginecolologico.vivos_muertos;
                    $("#vivosMuertos").val(vivos_muertos);
                    
                    var fecha_ultimo_parto = ginecolologico.fecha_ultimo_parto;
                    if(fecha_ultimo_parto != '' && fecha_ultimo_parto != null && fecha_ultimo_parto != undefined){
                        $('#fechaUltimoParto').data("DateTimePicker").date(new Date(fecha_ultimo_parto));
                    }

                    var metodo_anticonceptivo = ginecolologico.metodo_anticonceptivo;
                    if(metodo_anticonceptivo){
                        $("#anticonceptivoT").prop('checked', true);
                        $(".anticonceptivo").show();
                        $("#fechaUltimoAnticonceptivo").val(ginecolologico.metodo_anticonceptivo_observacion);
                    }else if(metodo_anticonceptivo == false){
                        $("#anticonceptivoF").prop('checked', true);
                        $(".anticonceptivo").hide();
                        $("#fechaUltimoAnticonceptivo").val("");
                    }else{
                        $("#anticonceptivoT").prop('checked', false);
                        $("#anticonceptivoF").prop('checked', false);
                        $(".anticonceptivo").hide();
                        $("#fechaUltimoAnticonceptivo").val("");
                    }

                    var menarquia = ginecolologico.menarquia;
                    if(menarquia){
                        $("#menarquiaT").prop('checked', true);
                        $(".menarquia").show();
                        $("#observacionMenarquia").val(ginecolologico.menarquia_observacion);
                    }else if(menarquia == false){
                        $("#menarquiaF").prop('checked', true);
                        $(".menarquia").hide();
                        $("#observacionMenarquia").val("");
                    }else{
                        $("#menarquiaT").prop('checked', false);
                        $("#menarquiaF").prop('checked', false);
                        $(".menarquia").hide();
                        $("#observacionMenarquia").val("");
                    }

                    var ciclo_menstrual = ginecolologico.ciclo_menstrual;
                    $("#cicloMenstrual").val(ciclo_menstrual);

                    var menopausia = ginecolologico.menopausia;
                    if(menopausia){
                        $("#menopausiaT").prop('checked', true);
                    }else if(menopausia == false){
                        $("#menopausiaF").prop('checked', true);
                    }else{
                        $("#menopausiaT").prop('checked', false);
                        $("#menopausiaF").prop('checked', false);
                    }

                    var pap = ginecolologico.pap;
                    $("#pap").val(pap);

                    var fur = ginecolologico.fur;
                    $("#fur").val(fur);
                   
                }

                if(data.datos_anamnesis.length > 0){
                    data = data.datos_anamnesis;
                    $("#id_formulario_ingreso_enfermeria_anamnesis").val(data[0].id);
                    $("#idGineCologica").val(data[0].idginecologica);
                    $("#antecedentesM").val(data[0].anamnesis_ant_morbidos);
                    $("#antecedentesQ").val(data[0].anamnesis_ant_quirurgicos);
                    var ant_alergicos = data[0].anamnesis_ant_alergicos;
                    if(ant_alergicos){
                        $("#ant_alergicoT").prop('checked', true);
                        $("#detalle_ant_alergicos").removeAttr("hidden");
                    }else{
                        $("#ant_alergicoF").prop('checked', true);
                        $("#detalle_ant_alergicos").attr("hidden",true);
                    }
                    $("#antecedentesA").val(data[0].anamnesis_ant_alergicos);

                    $("#checkHabito").prop('checked', data[0].habito_otros);

                    if(data[0].habito_otros == true){
                    detalle = document.getElementById("detalleHabito");
                    detalle.style.display='block';
                    }

                    $("#detalleOtroHabito").val(data[0].detalle_otro_habito);
                    $("#tabaco").prop('checked', data[0].habito_tabaco);
                    $("#alcohol").prop('checked', data[0].habito_alcohol);
                    $("#drogas").prop('checked', data[0].habito_drogas);
                    $("#diagnosticoMedico").val(data[0].diagnosticos_medicos);
                    $("#amnesisActual").val(data[0].anamnesis_actual);
					
					if(data[0].precaucion_estandar === true){
						$("input[name=precaucion_estandar][value=si]").prop("checked",true);
					}
					else if(data[0].precaucion_estandar === false){
						$("input[name=precaucion_estandar][value=no]").prop("checked",true);
					}
					else{
						$("input[name=precaucion_estandar]").prop("checked",false);
					}
					$("input[name=precaucion_estandar]:checked").trigger("change");
					
					$("#precaucion_respiratorio").prop("checked",data[0].precaucion_respiratorio);
					$("#precaucion_contacto").prop("checked",data[0].precaucion_contacto);
					$("#precaucion_gotitas").prop("checked",data[0].precaucion_gotitas);

                    if (data[0].deis==true){
                        $("#deisT").prop('checked', data[0].deis);
                    }else{
                        $("#deisF").prop('checked', true);
                    }

                    if (data[0].acom==true){
                        $("#oacomT").prop('checked', data[0].acom);
                        $("#acompañamiento").show("slow");
                        $("#acompanante").val(data[0].acompanante);
                        $("#vinculo_acompanante").val(data[0].vinculo_acompanante);
                        $("#telefono_acompanante").val(data[0].telefono_acompanante);
                    }else{
                        $("#oacomF").prop('checked', true);
                        ocultarLimpiarAcompañamiento();
                    }
                }

            
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    /* function dtpEditar () {
        $('.dPpertenenciaE').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        });
    } */

    /* function cargarPertenencias(){

        var caso = {{$caso}};
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerPertenencias/{{ $caso }}",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "get",
            dataType: "json",
            success: function (data) {
              console.log(data);
              data.forEach(function(val, idx) {
                var pertenencia = val['pertenencia'];
                var responsable = val['responsable'];
                var id = val['id']

                html = '<tr> <td></td> <td><input type="text" name="objetoPersonal[]" value="'+pertenencia+'" class="form-control"> <input type="hidden" name="idsobj[]" value="'+id+'"/> </td><td><input type="text" name="responsable[]" value="'+responsable+'" class="form-control"> <input type="hidden" name="idsobjres[]" value="'+id+'"/> </td> <td> <button class="btn btn-danger eliminar_rn" type="button" data-idobj="'+id+'"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';

                $( "#Objpersonal" ).append(html);
              });
            },
            error: function (error) {
                console.log(error);
            }
        });

    } */

    /* function imprimirErroresEditar (msg) {
            $(".imprimir-mensajes-pertenencias").find("ul").html('');
            $(".imprimir-mensajes-pertenencias").css('display','block');
            $.each( msg, function( key, value ) {
                $(".imprimir-mensajes-pertenencias").find("ul").append("<div style='display: flex'><i class='glyphicon glyphicon-remove' style='color: #a94442;'></i><div style='margin-left: 10px'><h4>"+value+"</h4></div></div>");
            });
        } */

    function ocultarLimpiarAcompañamiento(){
        $("#acompanante").val("");
        $("#vinculo_acompanante").val("");
        $("#telefono_acompanante").val("");
        $("#acompañamiento").hide();
        $("#btGuardarnAnamnesis").prop('disabled',false);
    }

    function cargarVistaAnamnesis(){
        IngresarMostrarAnamnesis();
        $("#Medicamentos").empty();
        verdatoMedicamento();
        $("#Objpersonal").empty();
        //cargarPertenencias();
    }

    $(function() {
        edad_global = null;

        //cargarPertenencias();

        $(document).on("click", ".eliminar_rn", function(){
            $(this).closest('tr').empty();
        });

        IngresarMostrarAnamnesis();
        verdatoMedicamento();
        // verdatoPueblo();

        $("#addMedicamento").click(function(){
            html = '<tr> <td></td> <td> <input type="text" name="nombreMedicamento[]" class="form-control"> <input type="hidden" name="ids[]" value=""/> </td> <td> <button class="btn btn-danger eliminar_rn" type="button"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';

            $("#Medicamentos").append(html);
        });

        $("#addObjpersonal").click(function(){
            html = '<tr> <td></td> <td> <input type="text" name="objetoPersonal[]" class="form-control"> <input type="hidden" name="idsobj[]" value=""/> </td><td> <input type="text" name="responsable[]" class="form-control"> <input type="hidden" name="idsobjres[]" value=""/> </td> <td> <button class="btn btn-danger eliminar_rn" type="button"><span class="glyphicon glyphicon-trash"></span> Eliminar</button> </td> </tr>';

            $("#Objpersonal").append(html);
        });

        $( "#iEnfermeria" ).click(function() {
            tabIE = $("#tabsIngresoEnfermeria div.active").attr("id");

            if(tabIE == "1h"){
                console.log("tabIE anamnesis: ", tabIE);
                cargarVistaAnamnesis();
            }
            
        });

        $("#hA").click(function() {
            cargarVistaAnamnesis();
        });

        $("#IEAnamnesis").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                ant_alergicos: {
                    validators:{
                        callback: {
							callback: function(value, validator, $field){
								if(value == 'pendiente'){
									$("#t_fecha_ida").addClass("hidden");
									$("#t_fecha_rescate").addClass("hidden");
								}
								else if(value == 'ida'){
									$("#t_fecha_ida").removeClass("hidden");
									$("#t_fecha_rescate").addClass("hidden");
								}
								else if(value == "ida-rescate"){
									$("#t_fecha_ida").removeClass("hidden");
									$("#t_fecha_rescate").removeClass("hidden");
								}
								return true;
							}
						}
                    }
                },
                antecedentesA:{
                    validators:{
                        trigger: 'change',
                        notEmpty:{
                            message: 'Debe ingresar los antecedentes alergicos'
                        }
                    }
                },
                'oacom':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        },
                    }
                },
                'deis':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        },
                    }
                },
                'detalleOtroHabito':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar otro habito'
                        } 
                    }
                },
                'acompanante':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar el nombre del acompañante'
                        } 
                    }
                },
                'vinculo_acompanante':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar el vinculo del acompañante con el paciente'
                        } 
                    }
                },
                'telefono_acompanante':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar el telefono del acompañante'
                        } 
                    }
                },
                'ant_alergico':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'gesta':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'observacionGesta':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'parto':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'observacionParto':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'aborto':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'observacionAborto':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'vaginal':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'observacionVaginal':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'forceps':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'cesaria':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'observacionCesaria':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'vivosMuertos':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una cantidad'
                        } 
                    }
                },
                'fechaUltimoParto':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una fecha'
                        } 
                    }
                },
                'anticonceptivo':{
                    validators:{
                        notEmpty:{
                            message: 'Debe elegir una opción'
                        } 
                    }
                },
                'fechaUltimoAnticonceptivo':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'menarquia':{
                    validators:{
                        notEmpty:{
                            message: 'Debe elegir una opción'
                        } 
                    }
                },
                'observacionMenarquia':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'cicloMenstrual':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar un ciclo'
                        } 
                    }
                },
                'menopausia':{
                    validators:{
                        notEmpty:{
                            message: 'Debe elegir una opción'
                        } 
                    }
                },
                'pap':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
                'fur':{
                    validators:{
                        notEmpty:{
                            message: 'Debe ingresar una observación'
                        } 
                    }
                },
            }
        }).on('status.field.bv', function(e, data) {
        }).on("success.form.bv", function(evt, data){
            $("#btGuardarnAnamnesis").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar esta información?</h4>",
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarIEAnamnesis",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btGuardarnAnamnesis").prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    // location.reload();
                                    IngresarMostrarAnamnesis();
                                    $("#Medicamentos").empty();
                                    verdatoMedicamento();
                                    $("#Objpersonal").empty();
                                   // cargarPertenencias();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    }); 
                                    IngresarMostrarAnamnesis();
                                    $("#Medicamentos").empty();
                                    verdatoMedicamento();
                                    $("#Objpersonal").empty();
                                    //cargarPertenencias();
                                }
                                
                                if(data.aviso){
                                    swalAviso.fire({
                                        title: 'Aviso',
                                        text:data.aviso
                                    });
                                }
                            },
                            error: function(error){
                                $("#btGuardarnAnamnesis").prop("disabled", false);
                                console.log(error);
                                IngresarMostrarAnamnesis();
                                $("#Medicamentos").empty();
                                verdatoMedicamento();
                                $("#Objpersonal").empty();
                                //cargarPertenencias();
                            }
                        });
                    }else{
                        $("#btGuardarnAnamnesis").prop("disabled", false);
                    }
                }
            }); 
        });

        $("[name='ant_alergico']").on("change", function () {
            $("#btGuardarnAnamnesis").prop('disabled',false); //para que en cada cambio active el boton independiente de si falta ingresar info, luego volvera a validar
            if ($(this).val() == 'si') {
                $("#detalle_ant_alergicos").removeAttr("hidden");
                $('#IEAnamnesis').bootstrapValidator('enableFieldValidators', 'antecedentesA', true);
                $("IEAnamnesis").bootstrapValidator('revalidateField','antecedentesA');
            }else{
                $("#detalle_ant_alergicos").attr("hidden",true);
                $("#antecedentesA").val("");
                $('#IEAnamnesis').bootstrapValidator('enableFieldValidators', 'antecedentesA', false);

            }
        });

        $("input[name='oacom']").change(function(){
            let valor = $(this).val();
            if(valor == "si"){
                $("#acompañamiento").show();
            }else{
                ocultarLimpiarAcompañamiento();
                // $("#acompañamiento").hide();
            }
        });
		
		$("input[name=precaucion_estandar]").on("change",function(){
			if($("input[name=precaucion_estandar]:checked").val() === "si"){
				mostrar_precauciones(true);
			}
			else{
				mostrar_precauciones(false);
				limpiar_precauciones();
			}
		});

        var ocultarTodoGineco=function(){
        var gesta=$("input[name='gesta']:checked").val();
        var parto =$("input[name='parto']:checked").val();
        var aborto =$("input[name='aborto']:checked").val();
        var vaginal =$("input[name='vaginal']:checked").val();
        var forceps =$("input[name='forceps']:checked").val();
        var cesaria =$("input[name='cesaria']:checked").val();
        
        var anticonceptivo =$("input[name='anticonceptivo']:checked").val();
        var menarquia =$("input[name='menarquia']:checked").val();
        var menopausia =$( "select[name='menopausia'] option:selected" ).val();

        if(gesta == "si")$(".gesta").show();
        else $(".gesta").hide("");
        if(parto == "si")$(".parto").show();
        else $(".parto").hide("");
      
        if(aborto == "si")$(".aborto").show();
        else $(".aborto").hide("");
        if(vaginal == "si")$(".vaginal").show();
        else $(".vaginal").hide("");
        if(cesaria == "si")$(".cesaria").show();
        else $(".cesaria").hide("");
        
        if(anticonceptivo == "si")$(".anticonceptivo").show();
        else $(".anticonceptivo").hide("");
        if(menarquia == "si")$(".menarquia").show();
        else $(".menarquia").hide("");
    
    }

    $("input[name='gesta']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".gesta").show();
                $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionGesta');
            }else{ 
                $(".gesta").hide("");
                $("#observacionGesta").val('');
            }
        });
    
    $("input[name='parto']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".parto").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionParto');
        }else{ 
            $(".parto").hide("");
            $("#observacionParto").val('');
        }
        
    });
    
    $("input[name='aborto']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".aborto").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionAborto');
        }else{ 
            $(".aborto").hide("");
            $("#observacionAborto").val('');
        }
        
    });
    
    $("input[name='vaginal']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".vaginal").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionVaginal');
        }else{ 
            $(".vaginal").hide("");
            $("#observacionVaginal").val('');
        }
        
    });
    
    $("input[name='cesaria']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".cesaria").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionCesaria');
        }else{ 
            $(".cesaria").hide("");
            $("#observacionCesaria").val('');
        }
        
    });
    
    $("input[name='anticonceptivo']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".anticonceptivo").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','fechaUltimoAnticonceptivo');
        }else{ 
            $(".anticonceptivo").hide("");
            $("#fechaUltimoAnticonceptivo").val('');
        }
        
    });
    
    $("input[name='menarquia']").on("change", function(){
        var value=$(this).val();
        if(value == "si"){
            $(".menarquia").show();
            $("#IEAnamnesis").bootstrapValidator('revalidateField','observacionMenarquia');
        }else{ 
            $(".menarquia").hide("");
            $("#observacionMenarquia").val('');
        }
        
    });
    $('#fechaUltimoParto').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#IEAnamnesis').bootstrapValidator('revalidateField', 'fechaUltimoParto');
        });
    ocultarTodoGineco();

    });
	function mostrar_precauciones(mostrar){
		$("#div_precaucion").prop("hidden",!mostrar);
	}
	function limpiar_precauciones(){
		$("#precaucion_respiratorio").prop("checked",false);
		$("#precaucion_contacto").prop("checked",false);
		$("#precaucion_gotitas").prop("checked",false);
	}


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

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'IEAnamnesis')) }}
{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}

    <div class="formulario">
        <input type="hidden" value="" name="id_formulario_ingreso_enfermeria" id="id_formulario_ingreso_enfermeria_anamnesis">
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>I. Anamnesis</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                            <label for="antecedentesM" class="control-label" title="Nombre">Antecedentes Morbidos y tratamiento: </label>
                                {{Form::textArea('antecedentesM', null, array('id' => 'antecedentesM', 'class' => 'form-control', 'rows' => 4))}}
                    </div>
                </div>
                <br>
                <div class="row">

                  <div class="form-group col-md-3">
                    <div class="col-sm-12">
                      {{Form::label('', "Ofrecimiento de acompañamiento:", array( ))}}
                        <div class="input-group">
                          <label class="radio-inline">{{Form::radio('oacom', "no", false, array('id'=>'oacomF', 'required' => true))}}No</label>
                          <label class="radio-inline">{{Form::radio('oacom', "si", false, array('id'=>'oacomT', 'required' => true))}}Sí</label>
                        </div>
                    </div>
                  </div>

                    <div class="form-group col-md-3">
                      <div class="col-sm-12">
                          {{Form::label('', "DEIS:", array( ))}}
                          <div class="input-group">
                            <label class="radio-inline">{{Form::radio('deis', "no", false, array('id'=>'deisF', 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('deis', "si", false, array('id'=>'deisT', 'required' => true))}}Sí</label>
                          </div>
                      </div>
                    </div>

                    {{-- <div class="form-group col-md-6">
                      <div class="col-sm-6">
                      {{Form::label('', "Pertenencia a algún pueblo originario:", array( ))}} <br>
                        <div class="input-group">
                          <label class="radio-inline">{{Form::radio('puebloind', "no", false, array('id'=> 'pueblono', 'required' => true, 'onclick'=> 'mostrarPuebloOrigen()'))}}No</label>
                          <label class="radio-inline">{{Form::radio('puebloind', "si", false, array('id'=> 'pueblosi', 'required' => true, 'onclick'=> 'mostrarPuebloOrigen()'))}}Sí</label>
                        </div>
                      </div>
                      <div class="col-sm-6" id="pueblo" style="display:none">
                        {{Form::select('pueblo_ind',["Mapuche" =>"1. Mapuche", "Aymara"=> "2. Aymara", "Rapa nui" => "3. Rapa Nui (Pascuense)", "Lican Antai" => "4. Lican Antai (Atacameño)", "Quechua" => "5. Quechua", "Colla" => "6. Colla","Diaguita" => "7. Diaguita" , "Kawéscar" => "8. Kawésqar", "Yagán" => "9. Yagán (Yámana)", "Ninguno" => "96. Ninguno", "Otro" => "99. Otro"], null, array('id' => 'pueblo_ind', 'class' => 'form-control', 'autofocus' => 'true'))}}
                      </div>
                      <div class="col cla_ind" style="margin-top: 60px;" hidden>
              					{{Form::text('esp_pueblo', null, array('id' => 'esp_pueblo', 'class' => 'form-control'))}}
              					<p class="subtitulos" align="center"> ESPECIFICAR</p>
              				</div>
                    </div> --}}

                </div>
                <br>
                <div class="row" id="acompañamiento" hidden>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('', "Nombre de cuidador o familiar", array()) }}
                                {{ Form::text('acompanante', null, array('id' => 'acompanante', 'class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-3 col-md-offset-1">
                            <div class="form-group">
                                {{ Form::label('', "Vinculo familiar", array()) }}
                                {{ Form::text('vinculo_acompanante', null, array('id' => 'vinculo_acompanante', 'class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-3 col-md-offset-1">
                            <div class="form-group">
                                {{ Form::label('', "Número telefonico de cuidador o familiar", array()) }}
                                {{ Form::text('telefono_acompanante', null, array('id' => 'telefono_acompanante', 'class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <p class="subtitulos" align="left" ><b>Lista Medicamentos</b></p>
                            <table id="rn_table" class="table table-striped table-bordered" style="width:100%;">
                                <thead style="background-color:#1E9966; color:#FFF">
                                    <tr>
                                        <th><p class="subtitulos" align="left">N°</p></th>
                                        <th><p class="subtitulos" align="left">Nombre</p></th>
                                        <th><p class="subtitulos" align="left"><b>Opciones</b></p></th>
                                    </tr>
                                </thead>
                                <tbody id="Medicamentos">
    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
    
                    <div class="col-md-12" align="left" style="margin-bottom:10px;">
                        <div class="btn btn-primary" id="addMedicamento" >+ Medicamento</div>
                    </div>
                </div>
                <legend></legend>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                            <label for="antecedentesQ" class="control-label" title="Nombre">Antecedentes Quirurgicos: </label>
                                {{Form::textArea('antecedentesQ', null, array('id' => 'antecedentesQ', 'class' => 'form-control', 'rows' => 3))}}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-3">
                        <div class="col-sm-12">
                            {{Form::label('', "Antecedentes Alergicos:", array( ))}}
                            <div class="input-group">
                              <label class="radio-inline">{{Form::radio('ant_alergico', "no", false, array('id'=>'ant_alergicoF'))}}No</label>
                              <label class="radio-inline">{{Form::radio('ant_alergico', "si", false, array('id'=>'ant_alergicoT'))}}Sí</label>
                            </div>
                        </div>
                    </div>
                    <div id="detalle_ant_alergicos" hidden>
                        <div class="col-md-12">
                            <div class="col-sm-12 form-group">
                                <label for="antecedentesA" class="control-label" title="Nombre">Detalle Antecedentes Alergicos: </label>
                                {{Form::textArea('antecedentesA', null, array('id' => 'antecedentesA', 'class' => 'form-control', 'rows' => 2))}}
                            </div>
                        </div>
                    </div>
                </div>
                
                <br>
                <div class="row">
                    <div class="col-sm-12">
                            <label for="habitos" class="control-label" title="Habitos">Habitos: </label>
                            <br>
                                <label> <input id="tabaco" name="habitos[]" type="checkbox" value="tabaco" title="tabaco" /> <span title="tabaco">Tabaco</span></label>
                                <label> <input id="alcohol" name="habitos[]" type="checkbox" value="alcohol" title="alcohol"/> <span title="alcohol">Alcohol</span></label>
                                <label> <input id="drogas" name="habitos[]" type="checkbox" value="drogas"/> Drogas</label>
                                <label> <input name="habitos[]" id="checkHabito" onchange="mostrarDetalleHabito()" type="checkbox" value="otras" /> Otras</label>
                    </div>

                    <div id="detalleHabito" style="display: none; margin-left:15px;" class="col-sm-12">
                        <div class="form-group">
                            <label for="detalleOtroHabito" class="control-label" title="pertenencias">Detalle otro habito: </label>
                                {{Form::textArea('detalleOtroHabito', null, array('id' => 'detalleOtroHabito', 'class' => 'form-control', 'rows' => 2))}}
                        </div>
                    </div>
                    <br>
                </div>
                <br>
				@if($sub_categoria == 4)
                    <div class="col-sm-12 form-group">
                        {{Form::label('', "Precaución estándar:", array( ))}}
                        <div class="input-group">
                          <label class="radio-inline">{{Form::radio('precaucion_estandar', "no", false, array('id'=>'precaucion_estandarF', 'required' => true))}}No</label>
                          <label class="radio-inline">{{Form::radio('precaucion_estandar', "si", false, array('id'=>'precaucion_estandarT', 'required' => true))}}Sí</label>
                        </div>
                    </div>
				<br>
				<div class="row" id="div_precaucion" hidden>
					<div class="col-md-12">
						<label>
							<input type="checkbox" name="precaucion_respiratorio" id="precaucion_respiratorio"> 
							Respiratorio
						</label>
						<label>
							<input type="checkbox" name="precaucion_contacto" id="precaucion_contacto"> 
							Contacto
						</label>
						<label>
							<input type="checkbox" name="precaucion_gotitas" id="precaucion_gotitas"> 
							Gotitas
						</label>
					</div>
				</div>
				<br>
				@endif
                <div class="row">
                    <div class="col-sm-12">
                            <label for="diagnosticosM" class="control-label" title="Nombre">Diagnosticos Medicos: </label>
                                {{Form::textArea('diagnosticoMedico', null, array('id' => 'diagnosticoMedico', 'class' => 'form-control', 'rows' => 3))}}
                    </div>
                    <div class="col-sm-12">
                        <label for="anamnesisA" class="control-label" title="Nombre">Anamnesis Actual: </label>
                        {{Form::textArea('amnesisActual', null, array('id' => 'amnesisActual', 'class' => 'form-control', 'rows' => 4))}}
                    </div>
                </div>
                <br>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <p class="subtitulos" align="left" ><b>Listado de Objetos Personales</b></p>
                            <table id="rn_table" class="table table-striped table-bordered" style="width:100%;">
                                <thead style="background-color:#1E9966; color:#FFF">
                                    <tr>
                                        <th><p class="subtitulos" align="left">N°</p></th>
                                        <th><p class="subtitulos" align="left">Objeto personal</p></th>
                                        <th><p class="subtitulos" align="left"><b>Responsable</b></p></th>
                                        <th><p class="subtitulos" align="left"><b>Opciones</b></p></th>
                                    </tr>
                                </thead>
                                <tbody id="Objpersonal">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>

                    <div class="col-md-12" align="left" style="margin-bottom:10px;">
                        <div class="btn btn-primary" id="addObjpersonal" >+ Objeto Personal</div>
                    </div>

                </div> --}}
                <br>
                @if($sub_categoria == 2)
                {{Form::hidden('idGineCologica', null, array('id' => 'idGineCologica'))}}
                {{Form::hidden('sub_categoria', $sub_categoria, array('id' => 'sub_categoria'))}}
                <legend>Antecedentes obstétricos</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 pl-0 pr-0">
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {{Form::label('', "Gesta", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('gesta', "no", false, array('id'=>'gestaF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('gesta', "si", false, array('id'=>'gestaT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 gesta">
                                        <div class="form-group">
                                            {{Form::label('', "Observación", array( ))}}
                                            {{Form::text('observacionGesta', null, array('id' => 'observacionGesta', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-3 col-md-offset-1">
                                        <div class="form-group">
                                            {{Form::label('', "Parto", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('parto', "no", false, array('id'=>'partoF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('parto', "si", false, array('id'=>'partoT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 parto">
                                        <div class="form-group">
                                            {{Form::label('', "Observación", array( ))}}
                                            {{Form::text('observacionParto', null, array('id' => 'observacionParto', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-0 pr-0">
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {{Form::label('', "Aborto", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('aborto', "no", false, array('id'=>'abortoF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('aborto', "si", false, array('id'=>'abortoT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 aborto">
                                        <div class="form-group">
                                            {{Form::label('', "Observación", array( ))}}
                                            {{Form::text('observacionAborto', null, array('id' => 'observacionAborto', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-3 col-md-offset-1">
                                        <div class="form-group">
                                            {{Form::label('', "Parto vaginal", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('vaginal', "no", false, array('id'=>'vaginalF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('vaginal', "si", false, array('id'=>'vaginalT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 vaginal">
                                        <div class="form-group">
                                            {{Form::label('', "Observación", array( ))}}
                                            {{Form::text('observacionVaginal', null, array('id' => 'observacionVaginal', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-0 pr-0">
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {{Form::label('', "Forceps", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('forceps', "no", false, array('id'=>'forcepsF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('forceps', "si", false, array('id'=>'forcepsT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-3 col-md-offset-1">
                                        <div class="form-group">
                                            {{Form::label('', "Cesarias", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('cesaria', "no", false, array('id'=>'cesariaF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('cesaria', "si", false, array('id'=>'cesariaT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 cesaria">
                                        <div class="form-group">
                                            {{Form::label('', "Observación", array( ))}}
                                            {{Form::text('observacionCesaria', null, array('id' => 'observacionCesaria', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-0 pr-0">
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('', "Vivos/muertos", array( ))}}
                                            {{Form::number('vivosMuertos', null, array('id' => 'vivosMuertos', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-md-offset-1">
                                        <div class="form-group">
                                            {{Form::label('', "Fecha de ultimo parto", array( ))}}
                                            {{Form::text('fechaUltimoParto', null, array('id' => 'fechaUltimoParto', 'class' => 'form-control' , 'autocomplete'=>'off'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-0 pr-0">
                                <div class="col-md-6 pl-0 pr-0">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{Form::label('', "Método anticonceptivo", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('anticonceptivo', "no", false, array('id'=>'anticonceptivoF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('anticonceptivo', "si", false, array('id'=>'anticonceptivoT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 anticonceptivo">
                                        <div class="form-group">
                                            {{Form::label('', "Observacion", array( ))}}
                                            {{Form::text('fechaUltimoAnticonceptivo', null, array('id' => 'fechaUltimoAnticonceptivo', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <legend>Antecedentes ginecológicos</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 pl-0 pr-0">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('', "Menarquia", array( ))}}
                                        <div class="input-group">
                                            <label class="radio-inline">{{Form::radio('menarquia', "no", false, array('id'=>'menarquiaF'))}}No</label>
                                            <label class="radio-inline">{{Form::radio('menarquia', "si", false, array('id'=>'menarquiaT'))}}Sí</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 menarquia">
                                    <div class="form-group">
                                        {{Form::label('', "Observación", array( ))}}
                                        {{Form::text('observacionMenarquia', null, array('id' => 'observacionMenarquia', 'class' => 'form-control'))}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pl-0 pr-0">
                                <div class="col-md-6 col-md-offset-1">
                                    <div class="form-group">
                                        {{Form::label('', "Ciclo menstrual", array( ))}}
                                        {{Form::text('cicloMenstrual', null, array('id' => 'cicloMenstrual', 'class' => 'form-control'))}}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 pl-0 pr-0">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {{Form::label('', "Menopausia", array( ))}}
                                            <div class="input-group">
                                                <label class="radio-inline">{{Form::radio('menopausia', "no", false, array('id'=>'menopausiaF'))}}No</label>
                                                <label class="radio-inline">{{Form::radio('menopausia', "si", false, array('id'=>'menopausiaT'))}}Sí</label>
                                            </div>
                                        </div>
                                    </div>
                                <div class="col-md-3">
                                        <div class="form-group">
                                            {{Form::label('', "PAP", array( ))}}
                                            {{Form::text('pap', null, array('id' => 'pap', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-md-offset-1">
                                        <div class="form-group">
                                            {{Form::label('', "FUR", array( ))}}
                                            {{Form::text('fur', null, array('id' => 'fur', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                @endif
                <br><br>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" id="btGuardarnAnamnesis">Guardar</button>
                    </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
