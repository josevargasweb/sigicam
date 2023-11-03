<script>
    /* function truncate (num, places) {
        return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
    } */

    function limpiarFormularioBarthelSegmentario(){
        $("#ComerSegmentario").val('').change();
        $("#LavarseSegmentario").val('').change();
        $("#VestirseSegmentario").val('').change();
        $("#ArreglarseSegmentario").val('').change();
        $("#DeposicionSegmentario").val('').change();
        $("#MiccionSegmentario").val('').change();
        $("#RetreteSegmentario").val('').change();
        $("#TrasferenciaSegmentario").val('').change();
        $("#DeambulacionSegmentario").val('').change();
        $("#EscalerasSegmentario").val('').change();
        $("#totalBarthelSegmentario").val('');
        $("#detalleBarthelSegmentario").val('');
        $("#guardarBarthelSegmentario").prop("disabled", false);
    }

    function validarBarthelSegmentario(){
             $('#validarBarthel').bootstrapValidator('revalidateField', 'comida');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'lavado');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'vestido');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'arreglo');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'deposicion');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'miccion');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'retrete');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'trasferencia');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'deambulacion');
            $('#validarBarthel').bootstrapValidator('revalidateField', 'escaleras');
    }

    function limpiarFormularioGlasgowSegmentario(){
            $('#apertura_ocular').val('').change();
            $('#respuesta_verbal').val('').change();
            $('#respuesta_motora').val('').change();
            $('#totalGlasgow').val('');
            $('#detalleGlasgow').val('');
    }

    function validarGlasgowSegmentario(){
            $('#validarGlasgow').bootstrapValidator('revalidateField', 'apertura_ocular');
            $('#validarGlasgow').bootstrapValidator('revalidateField', 'respuesta_verbal');
            $('#validarGlasgow').bootstrapValidator('revalidateField', 'respuesta_motora');
    }

    function limpiarFormularioNovaSegmentario(){
        $('#estado_mental').val('').change();
            $('#incontinencia').val('').change();
            $('#movilidad').val('').change();
            $('#nutricion_ingesta').val('').change();
            $('#actividad').val('').change();
            $('#puntos').val('');
            $('#detallex').val('');
    }

    function validarNovaSegmentario(){
             $('#validarNova').bootstrapValidator('revalidateField', 'estado_mental');
            $('#validarNova').bootstrapValidator('revalidateField', 'incontinencia');
            $('#validarNova').bootstrapValidator('revalidateField', 'movilidad');
            $('#validarNova').bootstrapValidator('revalidateField', 'nutricion_ingesta');
            $('#validarNova').bootstrapValidator('revalidateField', 'actividad');
    }

    function limpiarFormularioCaidaSegmentario(){
                $('#caidas_previas').val('').change();
                $('#medicamentos').val('').change();
                $('#deficit').val('').change();
                $('#mental').val('').change();
                $('#deambulacion').val('').change();
                $('#puntosCaida').val('');
                $('#detalleCaida').val('');
    }

    function validarCaidaSegmentario(){
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'caidas_previas');
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'medicamentos');
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'medicamentos[]');
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'deficit');
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'mental');
            $('#validarRiesgoCaida').bootstrapValidator('revalidateField', 'deambulacion');
    }

    function IngresarMostrarGeneral(){
        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosGeneral",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (data) {
                var IEGeneral = data.datos_IEGeneral;
                if(IEGeneral.length > 0){
                    $("#id_formulario_ingreso_enfermeria_general").val(IEGeneral[0].id);

                    if(IEGeneral[0].peso != null && IEGeneral[0].altura != null){
                        var peso = parseFloat(IEGeneral[0].peso);
                        $("#peso").val(peso);

                        var altura = parseFloat(IEGeneral[0].altura).toFixed(2);
                        $("#altura").val(altura);
                        var resultado2 = 0;
                        if(peso > 0 && altura > 0){
                            altura2 = altura * altura;
                            resultado = peso/altura2;
                            resultado2 = parseFloat(resultado).toFixed(1);
                            $("#imc").val(resultado2);
                        }

                        if(edad_global != null && edad_global < 60){
                            if (resultado2 > 0 && resultado2 < 18.5){
                                categoria = 'Insuficiencia ponderal';
                                $("#categoria").val(categoria);
                            }else if(resultado2 >= 18.5 && resultado2 <= 24.9){
                                categoria = 'Intervalo normal';
                                $("#categoria").val(categoria);
                            }else if(resultado2 == 25.0){
                                categoria = 'Sobrepeso';
                                $("#categoria").val(categoria);
                            }else if(resultado2 > 25.0 && resultado2 <= 29.9){
                                categoria = 'Preobesidad';
                                $("#categoria").val(categoria);
                            }else if(resultado2 == 30.0){
                                categoria = 'Obesidad';
                                $("#categoria").val(categoria);
                            }else if(resultado2 > 30.0 && resultado2 <= 34.9){
                                categoria = 'Obesidad de clase I';
                                $("#categoria").val(categoria);
                            }else if(resultado2 >= 35.0 && resultado2 <= 39.9){
                                categoria = 'Obesidad de clase II';
                                $("#categoria").val(categoria);
                            }else if(resultado2 >= 40.0){
                                categoria = 'Obesidad de clase III';
                                $("#categoria").val(categoria);
                            }
                        }else if(edad_global != null && edad_global >= 60){
                            if (resultado2 > 0 && resultado2 <= 23){
                                categoria = 'Bajo peso';
                                $("#categoria").val(categoria);
                            }else if(resultado2 > 23 && resultado2 < 28){
                                categoria = 'Normal';
                                $("#categoria").val(categoria);
                            }else if(resultado2 >= 28 && resultado2 < 32){
                                categoria = 'Sobrepeso';
                                $("#categoria").val(categoria);
                            }else if(resultado2 >= 32){
                                categoria = 'Obesidad';
                                $("#categoria").val(categoria);
                            }
                        }
                    }

                    $("#pas").val(IEGeneral[0].presion_arterial_sistolica);
                    $("#pad").val(IEGeneral[0].presion_arterial_diastolica);
                    $("#pulso").val(IEGeneral[0].pulso);
                    $("#fr").val(IEGeneral[0].frecuencia_cardiaca);
                    $("#temperatura").val(IEGeneral[0].temperatura);
                    $("#saturacion").val(IEGeneral[0].saturacion);
                    var ug = "{{$sub_categoria}}";
                    if(ug == 3){
                        $("#nutricional").val(IEGeneral[0].patron_nutricional).attr('selected','selected');
                    }else{
                        $("#nutricional").val(IEGeneral[0].patron_nutricional);
                    }
                    $("#conciencia").val(IEGeneral[0].estado_conciencia);
                    $("#funcionRespiratoria").val(IEGeneral[0].funcion_respiratoria);
                    $("#higiene").val(IEGeneral[0].higiene);                  

                    //glasgow
                    var datosGlasgow = data.datos_Glasgow;
                    if(!jQuery.isEmptyObject(datosGlasgow)){
                    $("#glasgow").val(datosGlasgow.total);

                    var detalleGlasgow = '';
                    if (datosGlasgow.total >= 3 && datosGlasgow.total <=8 && datosGlasgow.total != null) {
                        detalleGlasgow = 'Grave';
                    }
                    if (datosGlasgow.total >= 9 && datosGlasgow.total <=12 && datosGlasgow.total != null) {
                        detalleGlasgow = 'Moderado';
                    }
                    if (datosGlasgow.total >= 13 && datosGlasgow.total <=15 && datosGlasgow.total != null) {
                        detalleGlasgow = 'Leve';
                    } 

                    $("#glasgow-categoria-segmentario").text(detalleGlasgow);

                    formGlasgow =  $('#validarGlasgow');
                    formGlasgow.find('#apertura_ocular').val(datosGlasgow.apertura_ocular);
                    formGlasgow.find('#respuesta_verbal').val(datosGlasgow.respuesta_verbal);
                    formGlasgow.find('#respuesta_motora').val(datosGlasgow.respuesta_motora);
                    formGlasgow.find('#totalGlasgow').val(datosGlasgow.total);
                    formGlasgow.find('#detalleGlasgow').val(detalleGlasgow);

                    arrayGlasgow = datosGlasgow.apertura_ocular +','+ datosGlasgow.respuesta_verbal +','+ datosGlasgow.respuesta_motora;
                    $('#IEGeneral').find("input[name='arrayGlasgow']").val(arrayGlasgow);

                    validarGlasgowSegmentario();
                    }

                //barthel
                    var datosBarthel = data.datos_Barthel;
                    if(!jQuery.isEmptyObject(datosBarthel)){
                        formBarthel =  $("#validarBarthel");
                        formBarthel.find('#ComerSegmentario').val(datosBarthel.comida);
                        formBarthel.find('#LavarseSegmentario').val(datosBarthel.lavado);
                        formBarthel.find('#VestirseSegmentario').val(datosBarthel.vestido);
                        formBarthel.find('#ArreglarseSegmentario').val(datosBarthel.arreglo);
                        formBarthel.find('#DeposicionSegmentario').val(datosBarthel.deposicion);
                        formBarthel.find('#MiccionSegmentario').val(datosBarthel.miccion);
                        formBarthel.find('#RetreteSegmentario').val(datosBarthel.retrete);
                        formBarthel.find('#TrasferenciaSegmentario').val(datosBarthel.trasferencia);
                        formBarthel.find('#DeambulacionSegmentario').val(datosBarthel.deambulacion);
                        formBarthel.find('#EscalerasSegmentario').val(datosBarthel.escaleras);
                        totalBarthel = datosBarthel.comida + datosBarthel.lavado + datosBarthel.vestido + datosBarthel.arreglo  + datosBarthel.deposicion  + datosBarthel.miccion  + datosBarthel.retrete  + datosBarthel.trasferencia + datosBarthel.deambulacion  + datosBarthel.escaleras;
                        categoriaBarthel = '';
                        if(totalBarthel >= 0 && totalBarthel < 20){
                            categoriaBarthel ="Dependencia total";
                        }else if(totalBarthel >=20 && totalBarthel < 40){
                            categoriaBarthel = "Grave";
                        }else if(totalBarthel >=40 && totalBarthel < 60){
                            categoriaBarthel = "Moderado";
                        }else if(totalBarthel >=60 && totalBarthel < 100){
                            categoriaBarthel = "Leve";
                        }else if(totalBarthel == 100){
                            categoriaBarthel = "Independiente";
                        }

                        $("#totalBarthelSegmentario").val(totalBarthel);
                        $("#detalleBarthelSegmentario").val(categoriaBarthel);
                        
                        $("#barthel-segmentario").val(totalBarthel);
                        $("#barthel-categoria-segmentario").text(categoriaBarthel);

                        arrayBarthel =  datosBarthel.comida +','+ datosBarthel.lavado +','+ datosBarthel.vestido +','+ datosBarthel.arreglo  +','+ datosBarthel.deposicion  +','+ datosBarthel.miccion  +','+ datosBarthel.retrete  +','+ datosBarthel.trasferencia +','+ datosBarthel.deambulacion  +','+ datosBarthel.escaleras;
                        $('#IEGeneral').find("input[name='arrayBarthel']").val(arrayBarthel);
                        validarBarthelSegmentario();
                    }
                //riesgo_caida
                var datosRiesgo = data.datos_Riesgo;
                    if(!jQuery.isEmptyObject(datosRiesgo)){
                        limpiarFormularioCaidaSegmentario();
                        formRiesgoCaida =  $("#validarRiesgoCaida");
                        formRiesgoCaida.find('#caidas_previas').val(datosRiesgo.caidas_previas);
                        formRiesgoCaida.find('#deficit').val(datosRiesgo.deficits_sensoriales);
                        formRiesgoCaida.find('#mental').val(datosRiesgo.estado_mental);
                        formRiesgoCaida.find('#deambulacion').val(datosRiesgo.deambulacion);
                        
                        if (datosRiesgo.medicamentos.indexOf(',') > -1) {
                            formRiesgoCaida.find('#medicamentos').selectpicker('val',datosRiesgo.medicamentos.split(','));
                            formRiesgoCaida.find('#medicamentos').selectpicker('refresh');
                            // formRiesgoCaida.find('#medicamentos').val(datosRiesgo.medicamentos.split(',')).change();
                            arrayRiesgo = datosRiesgo.caidas_previas +','+ datosRiesgo.deficits_sensoriales +','+ datosRiesgo.estado_mental  +','+ datosRiesgo.deambulacion;
                            $('#IEGeneral').find("input[name='arrayRiesgoCaidaMedicamento']").val(datosRiesgo.medicamentos);
                            medicamentos = $('#medicamentos option:selected').length;
                            
                        }else{
                            formRiesgoCaida.find('#medicamentos').val(datosRiesgo.medicamentos).change();
                            medicamentos = (datosRiesgo.medicamentos === "0" || datosRiesgo.medicamentos === 0 || datosRiesgo.medicamentos === "")?0:1;
                            arrayRiesgo = datosRiesgo.caidas_previas +','+ datosRiesgo.medicamentos +','+ datosRiesgo.deficits_sensoriales +','+ datosRiesgo.estado_mental  +','+ datosRiesgo.deambulacion;
                        }
                        deficits_sensoriales = (datosRiesgo.deficits_sensoriales === "0" || datosRiesgo.deficits_sensoriales === 0 || datosRiesgo.deficits_sensoriales === "")?0:1;
                        deambulacion = (datosRiesgo.deambulacion === "0" || datosRiesgo.deambulacion === 0 || datosRiesgo.deambulacion === "")?0:1;
                        
                        totalRiesgo = datosRiesgo.caidas_previas + medicamentos + deficits_sensoriales + datosRiesgo.estado_mental  + deambulacion;
                        
                        categoriaRiesgo = '';
                        if(totalRiesgo <= 1){
                            categoriaRiesgo ="Bajo Riesgo";
                        }else{
                            categoriaRiesgo ="Alto Riesgo";
                        }                        
                        formRiesgoCaida.find('#puntosCaida').val(totalRiesgo);
                        formRiesgoCaida.find('#detalleCaida').val(categoriaRiesgo);
                        
                        $('#caida').val(totalRiesgo);
                        $('#caida-categoria-segmentario').text(categoriaRiesgo);


                        $('#IEGeneral').find("input[name='arrayRiesgoCaida']").val(arrayRiesgo);
                        validarCaidaSegmentario();
                    }

                //nova
                    var datosNova = data.datos_Nova;
                    if(!jQuery.isEmptyObject(datosNova)){
                        formNova = $("#validarNova");
                        formNova.find("#estado_mental").val(datosNova.estado_mental);
                        formNova.find("#incontinencia").val(datosNova.incontinencia);
                        formNova.find("#movilidad").val(datosNova.movilidad);
                        formNova.find("#nutricion_ingesta").val(datosNova.nutricion_ingesta);
                        formNova.find("#actividad").val(datosNova.actividad);

                        totalNova = datosNova.estado_mental + datosNova.incontinencia + datosNova.movilidad + datosNova.nutricion_ingesta  + datosNova.actividad;
                        
                        var detallex = '';
                        if (totalNova == 0) {
                            detallex = "Sin Riesgo";
                        }
                        if (totalNova >= 1 && totalNova <=4) {
                            detallex = "Riesgo Bajo";
                        }
                        if (totalNova >= 5 && totalNova <=8) {
                            detallex = "Riesgo Medio";
                        }
                        if (totalNova >= 9 && totalNova <=15) {
                            detallex = "Riesgo Alto";
                        } 
                        
                        $("#nova").val(totalNova);
                        $("#nova-categoria-segmentario").text(detallex);
                       
                        formNova.find("#puntos").val(totalNova);
                        formNova.find("#detallex").val(detallex);

                        arrayNova = datosNova.estado_mental +','+ datosNova.incontinencia +','+ datosNova.movilidad +','+ datosNova.nutricion_ingesta  +','+ datosNova.actividad;

                        $('#IEGeneral').find("input[name='arrayNova']").val(arrayNova);
                        validarNovaSegmentario();
                    }
                }else{
                    $("#id_formulario_ingreso_enfermeria_general").val("");
                }
            
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $(document).ready(function() {
        
        $( "#iEnfermeria" ).click(function() {
            tabIE = $("#tabsIngresoEnfermeria div.active").attr("id");
                
            if(tabIE == "2h"){
                IngresarMostrarGeneral();
            }
            
        });

        $("#hG").click(function() {
            IngresarMostrarGeneral();
        });

        $("#IEGeneral").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                peso: {
 			 		validators:{
 			 			greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                    callback: {
                        message: "Solo puede contener máximo 2 decimales",
                        callback: function (value, validator) {
                            if (value.substring(value.indexOf('.')).length < 4)   {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
 			 		},
                      
                  },
                  altura: {
 			 		validators:{
 			 			greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			}
 			 		}
 			 	}
            } 
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btGuardarGeneral").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarIEGeneral",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btGuardarGeneral").prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    limpiarFormularioBarthelSegmentario();
                                    IngresarMostrarGeneral();
                                    limpiarFormularioGlasgowSegmentario();
                                    limpiarFormularioNovaSegmentario();
                                    limpiarFormularioCaidaSegmentario();
                                    $('#IEGeneral').find("input[name='arrayRiesgoCaida']").val("");
                                    if($('#IEGeneral').find("input[name='arrayRiesgoCaidaMedicamento']").length ) {
                                        $('#IEGeneral').find("input[name='arrayRiesgoCaidaMedicamento']").val("");
                                    }
                                    $('#IEGeneral').find("input[name='arrayNova']").val("");
                                    $('#IEGeneral').find("input[name='arrayGlasgow']").val("");
                                    $('#IEGeneral').find("input[name='arrayBarthel']").val("");
                                }

                                if (data.error) {
                                    var erroHtml = '<div class="alert alert-danger" style="text-align: left"><ul>';
                                    var errores = data.error.split(',');

                                    errores.forEach(function(valor, id) {
                                        erroHtml += '<li>'+valor+'</li>';
                                    });                    
                                    erroHtml += '</ul></div>';               

                                    swalError.fire({
                                        title: 'Error',
                                        html: erroHtml
                                    });
                                    
                                    IngresarMostrarGeneral();
                                }

                                if(data.info){
                                    swalWarning.fire({
                                        title: "Información",
                                        text: data.info,
                                    }).then(function(result) {
                                        
                                        IngresarMostrarGeneral();
                                    });
                                }
                            },
                            error: function(error){
                                $("#btGuardarGeneral").prop("disabled", false);
                                console.log(error);
                                IngresarMostrarGeneral();
                            }
                        });
                    }else{
                        $("#btGuardarGeneral").prop("disabled", false);
                    }
                }
            }); 
        });

        $("#validarBarthel").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {                
                comida:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                lavado:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                vestido:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                arreglo:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                deposicion:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                miccion:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                retrete:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                trasferencia:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                deambulacion:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                escaleras:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
            }         
                    
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault(evt);

            comida  = $("#ComerSegmentario").val();
            lavado  = $("#LavarseSegmentario").val();
            vestido  = $("#VestirseSegmentario").val();
            arreglo  = $("#ArreglarseSegmentario").val();
            deposicion  = $("#DeposicionSegmentario").val();
            miccion  = $("#MiccionSegmentario").val();
            retrete  = $("#RetreteSegmentario").val();
            trasferencia  = $("#TrasferenciaSegmentario").val();
            deambulacion  = $("#DeambulacionSegmentario").val();
            escaleras  = $("#EscalerasSegmentario").val();

            arrayBarthel = comida +','+ lavado +','+ vestido +','+ arreglo +','+ deposicion +','+ miccion +','+ retrete +','+ trasferencia +','+ deambulacion +','+ escaleras
            $('#IEGeneral').find("input[name='arrayBarthel']").val(arrayBarthel);
            

            var resultBarthelSegmentario = 0;
            var resultBarthelSegmentario = $("#totalBarthelSegmentario").val();
            $("#barthel-segmentario").val(resultBarthelSegmentario);
            
            var categoriaBarthelSegmentario = '';
            var categoriaBarthelSegmentario = $("#detalleBarthelSegmentario").val();
            $("#barthel-categoria-segmentario").text(categoriaBarthelSegmentario);
            $("#barthelmodalSegmentario").modal("hide");

            
        });
		

        $("#validarGlasgow").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {                
                apertura_ocular:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                respuesta_verbal:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                respuesta_motora:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }         
                    
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault(evt);
            apertura_ocular = $('#apertura_ocular').val();
            respuesta_verbal = $('#respuesta_verbal').val();
            respuesta_motora = $('#respuesta_motora').val();

            arrayGlasgow = apertura_ocular +','+ respuesta_verbal +','+ respuesta_motora;
            $('#IEGeneral').find("input[name='arrayGlasgow']").val(arrayGlasgow);

            var resultGlasgow = 0;
            var resultGlasgow = $("#totalGlasgow").val();
            $("#glasgow").val(resultGlasgow);

            var categoriaGlasgow = '';
            var categoriaGlasgow = $("#detalleGlasgow").val();
            $("#glasgow-categoria-segmentario").text(categoriaGlasgow);

            $("#glasgowmodal").modal("hide");
        });

        
       
        $("#validarRiesgoCaida").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
                caidas_previas: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                medicamentos: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'medicamentos[]': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){ 
                                if($("#medicamentos").val() == '' || $("#medicamentos").val() == null){
                                    return {valid: false, message: "Campo obligatorio"};
                                    }
                                return true;
                            }
                        },
                    }
                },
                deficit: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                mental: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                deambulacion: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
            }).on('status.field.bv', function(e, data){
                data.bv.disableSubmitButtons(false);
            }).on("success.form.bv", function(evt){
                evt.preventDefault(evt);

                caidas_previas = $('#caidas_previas').val();
                medicamentos = $('#medicamentos').val();
                deficit = $('#deficit').val();
                mental = $('#mental').val();
                deambulacion = $('#deambulacion').val();
                if(Object.prototype.toString.call(medicamentos) === '[object Array]'){
                    $('#IEGeneral').find("input[name='arrayRiesgoCaidaMedicamento']").val(medicamentos);
                    arrayRiesgoCaida = caidas_previas +','+ deficit +','+ mental +','+ deambulacion;
                }else{
                    arrayRiesgoCaida = caidas_previas +','+ medicamentos +','+ deficit +','+ mental +','+ deambulacion;
                }
                $('#IEGeneral').find("input[name='arrayRiesgoCaida']").val(arrayRiesgoCaida);

                var resultCaida = 0;
                var resultCaida = $("#puntosCaida").val();
                $("#caida").val(resultCaida);

                var categoriaCaida = '';
                var categoriaCaida = $("#detalleCaida").val();
                $("#caida-categoria-segmentario").text(categoriaCaida);

                $("#caidamodal").modal("hide");
        });

       

        $("#validarNova").bootstrapValidator({            
            excluded: ':disabled',            
            fields: { 
                estado_mental:{
                    validators:{
                        notEmpty: {
                            message: 'Campo obilgatorio'
                        }
                    }
                },
                incontinencia:{
                    validators:{
                        notEmpty: {
                            message: 'Campo obilgatorio'
                        }
                    }
                },
                movilidad:{
                    validators:{
                        notEmpty: {
                            message: 'Campo obilgatorio'
                        }
                    }
                },
                nutricion_ingesta:{
                    validators:{
                        notEmpty: {
                            message: 'Campo obilgatorio'
                        }
                    }
                },
                actividad:{
                    validators:{
                        notEmpty: {
                            message: 'Campo obilgatorio'
                        }
                    }
                }
            }        
            }).on('status.field.bv', function(e, data) {            
                 data.bv.disableSubmitButtons(false);      
            }).on("success.form.bv", function(evt){
                evt.preventDefault(evt);

                estado_mental = $('#estado_mental').val();
                incontinencia = $('#incontinencia').val();
                movilidad = $('#movilidad').val();
                nutricion_ingesta = $('#nutricion_ingesta').val();
                actividad = $('#actividad').val();

            arrayNova = estado_mental +','+ incontinencia +','+ movilidad +','+ nutricion_ingesta +','+ actividad;
            $('#IEGeneral').find("input[name='arrayNova']").val(arrayNova);

                var resultNova = 0;
                var resultNova = $("#puntos").val();
                $("#nova").val(resultNova);

                var categoriaNova = '';
                var categoriaNova = $("#detallex").val();
                $("#nova-categoria-segmentario").text(categoriaNova);

                $("#novamodal").modal("hide");
        }); 
        // function truncate (num, places) {
        //     return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
        // }

        $("#peso").on("keyup change", function(){
            $("#imc").val('');
            $("#avisoPeso").html("");
        });

        $("#altura").on("keyup change", function(){
            $("#imc").val('');
            $("#avisoAltura").html("");
        });

        $(".calcularIMC").on("keyup change click", function(){
            var peso = 0;
            var altura = 0;
            var resultado = 0;
            var alturaF = 0;
            var resultado2 = 0;

            peso = $("#peso").val();
            altura = $("#altura").val();

            edad = edad_global;

            if(peso == ''){
                $("#avisoPeso").html("Debe ingresar el peso para calcular el IMC");
                $("#categoria").val("");
            }

            if(altura == ''){
                $("#avisoAltura").html("Debe ingresar la altura para calcular el IMC");
                $("#categoria").val("");
            }

            if(altura == '' && peso == ''){
                $("#avisoPeso").html("");
                $("#avisoAltura").html("");
            }
            
            if( peso != "" && peso > 0 && altura != "" && altura > 0){
                altura2 = altura * altura;
                resultado = peso/altura2;
                resultado2 = parseFloat(resultado).toFixed(1);
                $("#imc").val(resultado2);

                if(edad != null && edad < 60){
                    if (resultado2 > 0 && resultado2 < 18.5){
                        categoria = 'Insuficiencia ponderal';
                        $("#categoria").val(categoria);
                    }else if(resultado2 >= 18.5 && resultado2 <= 24.9){
                        categoria = 'Intervalo normal';
                        $("#categoria").val(categoria);
                    }else if(resultado2 == 25.0){
                        categoria = 'Sobrepeso';
                        $("#categoria").val(categoria);
                    }else if(resultado2 > 25.0 && resultado2 <= 29.9){
                        categoria = 'Preobesidad';
                        $("#categoria").val(categoria);
                    }else if(resultado2 == 30.0){
                        categoria = 'Obesidad';
                        $("#categoria").val(categoria);
                    }else if(resultado2 > 30.0 && resultado2 <= 34.9){
                        categoria = 'Obesidad de clase I';
                        $("#categoria").val(categoria);
                    }else if(resultado2 >= 35.0 && resultado2 <= 39.9){
                        categoria = 'Obesidad de clase II';
                        $("#categoria").val(categoria);
                    }else if(resultado2 >= 40.0){
                        categoria = 'Obesidad de clase III';
                        $("#categoria").val(categoria);
                    }
                }else if(edad != null && edad >= 60){
                    if (resultado2 > 0 && resultado2 <= 23){
                        categoria = 'Bajo peso';
                        $("#categoria").val(categoria);
                    }else if(resultado2 > 23 && resultado2 < 28){
                        categoria = 'Normal';
                        $("#categoria").val(categoria);
                    }else if(resultado2 >= 28 && resultado2 < 32){
                        categoria = 'Sobrepeso';
                        $("#categoria").val(categoria);
                    }else if(resultado2 >= 32){
                        categoria = 'Obesidad';
                        $("#categoria").val(categoria);
                    }
                }
            }
        });

        $("#btnriesgocaida").on("click", function(){
            validarCaidaSegmentario();
        });

        $("#guardarNova").on("click", function(){
            validarNovaSegmentario();
        });


        $("#guardarGlasgow").on("click", function(){
            validarGlasgowSegmentario();
        });


        $("#guardarBarthelSegmentario").on("click", function(){
            validarBarthelSegmentario();
        });

        $(".selectBarthelSegmentario").change(function(){

            comida  = $("#ComerSegmentario").val();
            lavado  = $("#LavarseSegmentario").val();
            vestido  = $("#VestirseSegmentario").val();
            arreglo  = $("#ArreglarseSegmentario").val();
            deposicion  = $("#DeposicionSegmentario").val();
            miccion  = $("#MiccionSegmentario").val();
            retrete  = $("#RetreteSegmentario").val();
            trasferencia  = $("#TrasferenciaSegmentario").val();
            deambulacion  = $("#DeambulacionSegmentario").val();
            escaleras  = $("#EscalerasSegmentario").val();

            suma = Number(comida) + Number(lavado) + Number(vestido) + Number(arreglo) + Number(deposicion)+ Number(miccion) + Number(retrete) + Number(trasferencia) + Number(deambulacion) + Number(escaleras);

            if(suma < 20){
                $("#detalleBarthelSegmentario").val("Dependencia total")
            }else if(suma>=20 && suma < 40){
                $("#detalleBarthelSegmentario").val("Grave")
            }else if(suma>=40 && suma < 60){
                $("#detalleBarthelSegmentario").val("Moderado")
            }else if(suma>=60 && suma < 100){
                $("#detalleBarthelSegmentario").val("Leve")
            }else{
                $("#detalleBarthelSegmentario").val("Independiente")
            }

            $("#totalBarthelSegmentario").val(suma);
        });

        

        $("#caida_delete").on("click", function(event){
            event.preventDefault();
            $('#caida').val('');
            $('#IEGeneral').find("input[name='arrayRiesgoCaida']").val("");
            $('#caida-categoria-segmentario').text('');
            limpiarFormularioCaidaSegmentario();
        });
        $("#nova_delete").on("click", function(event){
            event.preventDefault();
            $('#nova').val('');
            $('#IEGeneral').find("input[name='arrayNova']").val("");
            $('#nova-categoria-segmentario').text('');
            limpiarFormularioNovaSegmentario();
        });
        $("#glasgow_delete").on("click", function(event){
            event.preventDefault();
            $('#glasgow').val('');
            $('#IEGeneral').find("input[name='arrayGlasgow']").val("");
            $('#glasgow-categoria-segmentario').text('');
            limpiarFormularioGlasgowSegmentario();
        });
        $("#barthel_delete").on("click", function(event){
            event.preventDefault();
            $('#barthel-segmentario').val('');
            $('#IEGeneral').find("input[name='arrayBarthel']").val("");
            $('#barthel-categoria-segmentario').text('');
            limpiarFormularioBarthelSegmentario();
        });
   
    });


</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'IEGeneral')) }}
{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}
<!-- formulario barthel -->
{{ Form::hidden('arrayBarthel','', array()) }}
<!-- formulario glasgow -->
{{ Form::hidden('arrayGlasgow','', array()) }}
<!-- formulario nova -->
{{ Form::hidden('arrayNova','', array()) }}
<!-- formulario riesgo caida -->
{{ Form::hidden('arrayRiesgoCaida','', array()) }}
<!-- formulario riesgo caida medicamento -->
{{ Form::hidden('arrayRiesgoCaidaMedicamento','', array()) }}
<div class="formulario">
    <input type="hidden" value="" name="id_formulario_ingreso_enfermeria" id="id_formulario_ingreso_enfermeria_general">
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>II. Examen Físico General</h4>
        </div>
        <div class="panel-body">
            @if($sub_categoria == 3)
            <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "Peso (Kg)", array( ))}}
                            {{Form::number('peso', null, array('id' => 'peso', 'step' => '0.1' ,'class' => 'form-control'))}}
                            <span id="avisoPeso"></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                            {{Form::label('', "Talla (cm)", array( ))}}
                            {{Form::number('altura', null, array('id' => 'altura', 'step' => '0.01', 'class' => 'form-control'))}}
                            <span id="avisoAltura"></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                        {{Form::label('', "Estado Nutricional", array( ))}}
                            {{Form::select('nutricional', array(''=>'Seleccione', 'RN'=>'RN', 'AEG'=>'AEG', 'GEG' =>'GEG', 'PEG' => 'PEG'), null,array('class' => 'form-control', 'id'=>'nutricional'))}}
                        </div>
                    </div>
                </div>
            @else
                <legend>Calcular IMC</legend>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "Peso (Kg)", array( ))}}
                            {{Form::number('peso', null, array('id' => 'peso', 'step' => '0.1' ,'class' => 'form-control calcularIMC'))}}
                            <span id="avisoPeso"></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                            {{Form::label('', "Altura (Mt)", array( ))}}
                            {{Form::number('altura', null, array('id' => 'altura', 'step' => '0.01', 'class' => 'form-control calcularIMC'))}}
                            <span id="avisoAltura"></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "IMC", array( ))}}
                            {{Form::number('imc', null, array('id' => 'imc', 'step' => '0.01', 'class' => 'form-control', 'readonly'))}}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "Categoria", array( ))}}
                            {{Form::text('categoria', null, array('id' => 'categoria', 'class' => 'form-control', 'readonly'))}}
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "Estado Nutricional", array( ))}}
                            {{Form::text('nutricional', null, array('id' => 'nutricional', 'class' => 'form-control'))}}
                        </div>
                    </div>
                </div>
            @endif
            <br><br>
            <legend>Signos vitales</legend>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Presión Arterial Sistolica (mmHg)", array( ))}}
                        {{Form::number('pas', null, array('id' => 'pas', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Presión Arterial Diastolica (mmHg)", array( ))}}
                        {{Form::number('pad', null, array('id' => 'pad', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Frecuencia Respiratoria (Rpm)", array( ))}}
                        {{Form::number('pulso', null, array('id' => 'pulso', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Frecuencia cardiaca (Lpm)", array( ))}}
                        {{Form::number('fr', null, array('id' => 'fr', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Temperatura (Cº)", array( ))}}
                        {{Form::number('temperatura', null, array('id' => 'temperatura', 'step' => '0.1', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Saturación de oxígeno (%)", array( ))}}
                        {{Form::number('saturacion', null, array('id' => 'saturacion', 'class' => 'form-control', 'min' => '0'))}}
                    </div>
                </div>
            </div>
            <br><br>
            <legend>Valoración de necesidades</legend>
            <div class="col-md-12">
                <div class="col-md-5">
                    <div class="form-group">
                        {{Form::label('', "Estado conciencia", array( ))}}
                        {{Form::text('conciencia', null, array('id' => 'conciencia', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-5 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Funcion respiratoria", array( ))}}
                        {{Form::text('funcionRespiratoria', null, array('id' => 'funcionRespiratoria', 'class' => 'form-control'))}}
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-5">
                    <div class="form-group">
                        {{Form::label('', "Higiene (Aseo y Confort)", array( ))}}
                        {{Form::text('higiene', null, array('id' => 'higiene', 'class' => 'form-control'))}}
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3" style="padding-left: 0;">
                    <div class="col-md-5" style="padding-left: 0;">
                        {{Form::label('',"Nova")}}
                        {{Form::number('nova', null, array('id' => 'nova', 'step' => '0.01', 'class' => 'form-control', 'style' => 'z-index:1', 'readonly'))}}
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;;padding-left:0;">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#novamodal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;;padding-left:0;">
                        <a href="#" class="btn btn-success" id="nova_delete">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </div>
                    <div class="col-md-12" style="padding-left: 0">
                        <div class="col-md-7" style="padding-left: 0;">
                            <p id="nova-categoria-segmentario" class=""></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="padding-left: 0;">
                    <div class="col-md-5" style="padding-left: 0;">
                        {{Form::label('',"Riesgo Caída")}}
                        {{Form::number('caida', null, array('id' => 'caida', 'step' => '0.01', 'class' => 'form-control', 'style' => 'z-index:1', 'readonly'))}}
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;padding-left:0;">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#caidamodal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;;padding-left:0;">
                        <a href="#" class="btn btn-success" id="caida_delete">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </div>
                    <div class="col-md-12" style="padding-left: 0">
                        <div class="col-md-7" style="padding-left: 0;">
                            <span id="caida-categoria-segmentario"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="padding-left: 0;">
                    <div class="col-md-5" style="padding-left: 0;">
                        {{Form::label('',"Glasgow")}}
                        {{Form::number('glasgow', null, array('id' => 'glasgow', 'step' => '0.01', 'class' => 'form-control', 'style' => 'z-index:1', 'readonly'))}}
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;padding-left:0;">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#glasgowmodal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;;padding-left:0;">
                        <a href="#" class="btn btn-success" id="glasgow_delete">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </div>
                    <div class="col-md-12" style="padding-left: 0">
                        <div class="col-md-7" style="padding-left: 0;">
                            <p id="glasgow-categoria-segmentario"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="padding-left: 0;">
                    <div class="col-md-5" style="padding-left: 0;">
                        {{Form::label('',"Barthel")}}
                        {{Form::number('barthel', null, array('id' => 'barthel-segmentario', 'step' => '0.01', 'class' => 'form-control', 'style' => 'z-index:1', 'readonly'))}}
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;padding-left:0;">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#barthelmodalSegmentario">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;;padding-left:0;">
                        <a href="#" class="btn btn-success" id="barthel_delete">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </div>
                    <div class="col-md-12" style="padding-left: 0">
                        <div class="col-md-7" style="padding-left: 0;">
                            <p id="barthel-categoria-segmentario"></p>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="col-md-12">
                <div class="col-md-2" style="padding-left: 0">
                    <button type="submit" class="btn btn-primary" id="btGuardarGeneral">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarBarthel', 'autocomplete' => 'off')) }}
<div class="modal fade" id="barthelmodalSegmentario" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="panel panel-default">
	<div class="panel-heading panel-info">
		<h4>Índice de Barthel Inicial</h4>
    </div>
	<div class="panel-body">
		<div style="text-align: left;">			
			<div>
				<input name="inicio" value="true" hidden="">
				<input name="tipo-encuesta" value="indiceBarthel" hidden="">
			</div>     
            <table class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr style="background:#399865; color: cornsilk;">
                        <th>Parámetro</th>
                        <th>Situación del paciente</th>
                    </tr>
                </thead>
                <tbody class="agrupar-trs">

                    <tr>
                        <td >
                        <label for="" class="control-label" title="Comer">Comer</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('comida', array(''=>'Seleccione', '10'=>'(10 pts.) Totalmente independiente', '5'=>'(5 pts.) Necesita ayuda para cortar carne, el pan, etc.', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'ComerSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Lavarse">Lavarse</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('lavado', array(''=>'Seleccione', '5'=>'(5 pts.) Independiente: entra y sale solo del baño', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'LavarseSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Comer">Vestirse</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('vestido', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente: capaz de ponerse y de quitarse la ropa, abotonarse, atarse los zapatos', '5'=>'(5 pts.) Necesita ayuda', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'VestirseSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Arreglarse">Arreglarse</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('arreglo', array(''=>'Seleccione', '5'=>'(5 pts.) Independiente para lavarse la cara, las manos, peinarse, afeitarse, maquillarse, etc.', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'ArreglarseSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Deposicion">Deposiciones (valórese la semana previa)</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('deposicion', array(''=>'Seleccione', '10'=>'(10 pts.) Continencia normal', '5'=>'(5 pts.) Ocasionalmente algún episodio de incontinencia, o necesita ayuda para administrarse supositorios o lavativas', '0' =>'(0 pts.) Incontinencia'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'DeposicionSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Miccion">Micción (valórese la semana previa)</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('miccion', array(''=>'Seleccione', '10'=>'(10 pts.) Continencia normal, o es capaz de cuidarse de la sonda si tiene una puesta', '5'=>'(5 pts.) Un episodio diario como máximo de incontinencia, o necesita ayuda para cuidar de la sonda', '0' =>'(0 pts.) Incontinencia'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'MiccionSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Retrete">Usar el retrete</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('retrete', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente para ir al cuarto de aseo, quitarse y ponerse la ropa', '5'=>'(5 pts.) Necesita ayuda para ir al retrete, pero se limpia solo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'RetreteSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Trasferencia">Trasladarse</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('trasferencia', array(''=>'Seleccione', '15'=>'(15 pts.) Independiente para ir del sillón a la cama','10'=>'(10 pts.) Mínima ayuda física o supervisión para hacerlo', '5'=>'(5 pts.) Necesita gran ayuda, pero es capaz de mantenerse sentado solo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'TrasferenciaSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Deambulacion">Deambular</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('deambulacion', array(''=>'Seleccione', '15'=>'(15 pts.) Independiente, camina solo 50 metros','10'=>'(10 pts.) Necesita ayuda física o supervisión para caminar 50 metros', '5'=>'(5 pts.) Independiente en silla de ruedas sin ayuda', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'DeambulacionSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                        <label for="" class="control-label" title="Escaleras">Escalones</label>
                        </td>
                        <td>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="col-sm-10">
                                    
                                    {{Form::select('escaleras', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente para bajar y subir escaleras', '5'=>'(5 pts.) Necesita ayuda física o supervisión para hacerlo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthelSegmentario', 'id'=>'EscalerasSegmentario'))}}
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>

                    <tr>
                        <td >
                            Total:
                        </td>
                        <td>
                            <div class="col-md-6">
                                <input type="number" min="0" id="totalBarthelSegmentario" name="indiceBarthel-total" class="form-control indiceBarthelInicial-total" readonly=""  data-fv-field="indiceBarthel-total" value="0">
                            </div>
                            <div class="col-md-6">
                                {{Form::text('detalleBarthel', "Independiente", array('readonly','id' => 'detalleBarthelSegmentario', 'class' => 'form-control'))}}
                            </div>
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">

                            <input id="guardarBarthelSegmentario" type="submit" name="" class="btn btn-primary" value="Guardar">
                        </td>
                    </tr>
                </tbody>

            </table>
		</div>
	</div>
</div>

    <p>Máxima puntuación: 100 puntos (90 si va en silla de ruedas)</p>

    <table class="table table-bordered">
        <thead style="background:#399865; color: cornsilk;">
                <tr>
                <th>Resultado</th>
                    <th>Grado de dependencia</th>
                </tr>
        </thead>
        <tbody>
              <tr>
                  <td>&lt;20</td>
                  <td>Dependencia Total</td>
              </tr>

              <tr>
                  <td>20-39</td>
                  <td>Grave</td>
              </tr>

              <tr>
                  <td>40-59</td>
                  <td>Moderado</td>
              </tr>

              <tr>
                  <td>60-99</td>
                  <td>Leve</td>
              </tr>

              <tr>
                  <td>100</td>
                  <td>Independiente</td>
              </tr>
        </tbody>
        </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarGlasgow', 'autocomplete' => 'off')) }}
<div class="modal fade" id="glasgowmodal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                @include('Gestion.gestionEnfermeria.partials.Formglasgow')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}

{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarNova', 'autocomplete' => 'off')) }}
<div class="modal fade" id="novamodal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                @include('Gestion.gestionEnfermeria.partials.FormNova')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}

{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarRiesgoCaida', 'autocomplete' => 'off')) }}
<div class="modal fade" id="caidamodal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                @include('Gestion.gestionEnfermeria.partials.FormRiesgoCaida')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
