<?php

?>

<style>
    .input-no-style {
        border:none;
        background-image:none;
        background-color:transparent;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    .no-point-event {
        pointer-events: none;
    }

    
</style>

<script>    

var count_clicks_modal_signos_vitales = 0;

function isNumeric(value) {
    return /^-?\d+$/.test(value);
}

function hourFormat(hora){
    var is_valid = isNumeric(hora);
    if
    (is_valid){
        return (parseInt(hora) < 10) ? "0"+hora : hora;
    }
    else {
        return "00";
    }

}

function minuteFormat(minutos){
    var is_valid = isNumeric(minutos);
    if
    (is_valid){
        return (parseInt(minutos) < 10) ? "0"+minutos : minutos;
    }
    else {
        return "00";
    }

}


function anadirSignosVitales(contenedor){
	var bv = $("#HESignosVitalesModal").data("bootstrapValidator");

	bv.validate();

	if(!bv.isValid())
	{
		console.log(bv.getInvalidFields());
	}
	
}


function callAjaxCheckIfPlanificacionAfterSignos(caso_id, signos_vitales_id, hora ,fecha,id_indicacion_medica){
    if(id_indicacion_medica == ""){
        return $.ajax({
                url: "{{route('check-planificacion-despues-signos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'caso_id': caso_id, 'signos_vitales_id': signos_vitales_id, 'hora' : hora, 'fecha' : fecha},
                dataType: "json",
                type: "post",
            }).then(function(resp){
                return resp.data;
            });
    }else{
        return false;
    }

}

function callAjaxAddCuidado(){

    var form = $("#HECuidadoEnfermeria");

    return $.ajax({
        url: "{{URL::to('/gestionEnfermeria')}}/addCuidadoEnfermeria",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:  form.serialize(),
        dataType: "json",
        type: "post",
        }).then(function(data){
            return data;
        });

}

function callAjaxAddCuidadoSignosVitales(data){

    return $.ajax({
        url: "{{route('anadir-check-para-signos-vitales')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {'data': data},
        dataType: "json",
        type: "post",
        }).then(function(data){
            return data;
        });

}




function callAjaxDataSignosVitales(fecha,id_indicacion_medica){
    return $.ajax({
        url: "{{route('get-cuidados-signos-vitales-json')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {"caso_id" : "{{$caso_id}}", "fecha" : fecha,"id_indicacion_medica":id_indicacion_medica},
        dataType: "json",
        type: "post",
    }).then(function(resp){
        return resp.data;
    });
}
//eventos modal control de signos vitales
var funcion_validacion_correcta = function(){};
$(function(){
	$("#form-signos-vitales").on("hide.bs.modal",function(){
        //Al momento de ocultar el modal se limpian todos los campos, pero se debe rescatar el caso
        caso = $('#HESignosVitalesModal').find("input[name='caso']").val();
		count_clicks_modal_signos_vitales = 0;
        $("#glassgowx2-categoria").text("");
        $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
        $("#form-signos-vitales :input").val("");
        $('#HESignosVitalesModal').find("input[name='caso']").val(caso);
        cargarCuidados();
	});
	
	$("#btn_cerrar_signos_vitales").on("click",function(){
        //Al momento de cerrar el modal se limpian todos los campos, pero se debe rescatar el caso
        caso = $('#HESignosVitalesModal').find("input[name='caso']").val();
		count_clicks_modal_signos_vitales = 0;
		$("#form-signos-vitales").modal("hide");
        $("#glassgowx2-categoria").text("");
        $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
        $("#form-signos-vitales :input").val("");
        $('#HESignosVitalesModal').find("input[name='caso']").val(caso);
	});
	$('.cuidado-signo-horario').datetimepicker({
	    format: 'HH:mm'
	});

	$("#HESignosVitalesModal").bootstrapValidator({
	    excluded: [":disabled"],
	    fields: {
	    	'metodo1': {
	            validators:{
	                notEmpty: {
	                    message: 'Debe seleccionar un Metodo O2'
	                },
	                remote: {
	                    data: function(validator){
	                        return {
	                            metodo1: validator.getFieldElements('metodo1').val()
	                        };
	                    },
	                    url: "{{URL::to('/validar_metodo1')}}"
	                }
	            }
	        },
            'fio1': {
	            validators:{
	                notEmpty: {
	                    message: 'Debe seleccionar un FIO2'
	                },
	                remote: {
	                    data: function(validator){
	                        return {
	                            metodo1: validator.getFieldElements('fio1').val()
	                        };
	                    },
	                    url: "{{URL::to('/validar_fio1')}}"
	                }
	            }
	        },
            'peso1': {
                validators:{
                    notEmpty: {
                        message: 'Debe ingresar el peso'
                    }
                }
            },
            estado_conciencia:{
                validators:{
                    notEmpty: {
                        message: 'Debe seleccionar un estado de conciencia'
                    }
                }
            },
            'utero': {
                validators: {
                    notEmpty: {
                        message: 'Debe seleccionar una opción'
                    }
                }
            }  
	    }
	    }).on('status.field.bv', function(e, data) {
	    //data.bv.disableSubmitButtons(true);
	    }).on("success.form.bv", function(evt, data){

	        funcion_validacion_correcta();
	    });

});


function modalSignosVitalesPorHora(contenedor,hora,id_indicacion_medica){
    var fecha = (parseInt(hora) >= 9 && parseInt(hora) <= 23) ? $("#fecha_uno").val() : $("#fecha_dos").val();

    callAjaxDataSignosVitales(fecha,id_indicacion_medica).then(function(res){

        var data_modal = null;

        $.each(JSON.parse(res), function(i, item) {
            if(hora === hourFormat(item.hora)){
                data_modal = item;
                return false;
            }
        }); console.log("data_modal: ",data_modal);

        var btn_modal = [];
        var is_saved = false;

        var btn_guardar = {
            label: "Guardar",
            className: "btn btn-primary pull-left btn-cuidados-signos-guardar",
            callback: function() {
                anadirSignosVitales(contenedor);
                modal.modal("hide");
                count_clicks_modal_signos_vitales = 0;
            }
        };

        var btn_cerrar =  {
            label: "Cerrar",
            className: "btn btn-default pull-left btn-cuidados-signos-cerrar",
            callback: function() {
                modal.modal("hide");
                count_clicks_modal_signos_vitales = 0;
            }
        }

        if(data_modal === null){
            $("#btn_guardar_signos_vitales").show();
            $(".cuidado-signo-input").prop("disabled", false);
            $(".cuidado-signo-input").val("");
            $(".contenedor-btn-calcular-glassglow").show();
        }else{
        	$("#btn_guardar_signos_vitales").hide();
            $(".cuidado-signo-input").prop("disabled", true);
            $(".contenedor-btn-calcular-glassglow").hide();
        }
        
        
    /*         btn_modal.push(btn_cerrar);
    */
        $(function () {
        	var bv = $("#HESignosVitalesModal").data("bootstrapValidator");

        	if(bv){
        		bv.resetForm();
        	}
        	$("#btn_guardar_signos_vitales").off("click").on("click",function(){
        		anadirSignosVitales(contenedor);
        	});

        	funcion_validacion_correcta = function(){
    	    	var form = $("#HESignosVitalesModal");

    	        $.ajax({
    	            url: "{{URL::to('/gestionEnfermeria')}}/agregarSignosVitales",
    	            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	            },
    	            data:  form.serialize(),
    	            dataType: "json",
    	            type: "post",
    	            success: function(data){
    	               // $("#btnSolicitarSignos").prop("disabled", false);
    	                if (data.exito) {

    	                    tipo = "{{Auth::user()->tipo}}";
    	                    contenedor.empty();
    	                    contenedor.removeAttr("style");
    	                    var html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div>';

    	                    if
    	                    (tipo === "gestion_clinica"){
    	                        html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div>';
    	                    }
    	                    else if
    	                    (tipo === "tens"){
    	                        html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div>';
    	                    }

                            else if
    	                    (tipo === "matrona"){
    	                        html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div>';
    	                    }

    	                    contenedor.html(html);

    	                    swalExito.fire({
    	                    title: 'Exito!',
    	                    text: data.exito,
    	                    });
    	                }

    	                if (data.faltanDatos) {
    	                    swalWarning.fire({
    	                    title: 'Información',
    	                    text:data['faltanDatos']
    	                    });
    	                }

    	                if (data.error) {
    	                         swalError.fire({
    	                        title: 'Error',
    	                        text:data.error
    	                        }).then(function(result) {
    	                        if (result.isDenied) {
    	                            // location . reload();
                                    cargarCuidados();
    	                        }
    	                        });
    	                }
    	            },
    	            error: function(error){

    	            }
    	        });
                $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
    	        $("#form-signos-vitales").modal("hide");
    	        count_clicks_modal_signos_vitales = 0;
        	}
        	
            $("#form-signos-vitales").modal("show");
        });

        var p_arterial_sis =
        (data_modal !== null && data_modal.presion_alterial_sis !== null && data_modal.presion_alterial_sis !== undefined) ? data_modal.presion_alterial_sis.trim() : "";

        var p_arterial_dias =
        (data_modal !== null && data_modal.presion_alterial_dia !== null && data_modal.presion_alterial_dia !== undefined) ? data_modal.presion_alterial_dia.trim() : "";

        var frec_cardiaca =
        (data_modal !== null && data_modal.frecuencia_cardiaca !== null && data_modal.frecuencia_cardiaca !== undefined) ? data_modal.frecuencia_cardiaca.trim() : "";

        var frec_respiratoria =
        (data_modal !== null && data_modal.frecuencia_respiratoria !== null && data_modal.frecuencia_respiratoria !== undefined) ? data_modal.frecuencia_respiratoria.trim() : "";

        var temp_axilo_rectal =
        (data_modal !== null && data_modal.temperatura_axilo !== null && data_modal.temperatura_axilo !== undefined) ? data_modal.temperatura_axilo.trim() : "";

        var temp_rectal =
        (data_modal !== null && data_modal.temp_rectal !== null && data_modal.temp_rectal !== undefined) ? data_modal.temp_rectal.trim() : "";

        var data_hora =
        (data_modal !== null && data_modal.hora !== null && data_modal.hora !== undefined) ? hourFormat(data_modal.hora.trim()) : hora;

        var data_minutos =
        (data_modal !== null && data_modal.minutos !== null && data_modal.minutos !== undefined) ? minuteFormat(data_modal.minutos.trim()) : "00";

        var hora_min = data_hora+":"+data_minutos;

        var caso_id =
        (data_modal !== null && data_modal.caso_id !== null && data_modal.caso_id !== undefined) ? data_modal.caso_id.trim() : "";

        var signos_vitales_id =
        (data_modal !== null && data_modal.signos_vitales_id !== null && data_modal.signos_vitales_id !== undefined) ? data_modal.signos_vitales_id.trim() : "";

        var saturacion_oxigeno =
        (data_modal !== null && data_modal.saturacion1 !== null && data_modal.saturacion1 !== undefined) ? data_modal.saturacion1.trim() : "";

        var hemoglucotest =
        (data_modal !== null && data_modal.hemoglucotest1 !== null && data_modal.hemoglucotest1 !== undefined) ? data_modal.hemoglucotest1.trim() : "";

        var glasgow =
        (data_modal !== null && data_modal.glasgow1 !== null && data_modal.glasgow1 !== undefined) ? data_modal.glasgow1 : "";

        var metodo_o2 =
        (data_modal !== null && data_modal.metodo2_1 !== null && data_modal.metodo2_1 !== undefined) ? data_modal.metodo2_1.trim() : "";

        var dolor_eva =
        (data_modal !== null && data_modal.dolor1 !== null && data_modal.dolor1 !== undefined) ? data_modal.dolor1.trim() : "";

        var fio =
        (data_modal !== null && data_modal.fio2_1 !== null && data_modal.fio2_1 !== undefined) ? data_modal.fio2_1.trim() : "";

        var estado_conciencia =
        (data_modal !== null && data_modal.estado_conciencia1 !== null && data_modal.estado_conciencia1 !== undefined) ? data_modal.estado_conciencia1.trim() : "";

        var peso =
        (data_modal !== null && data_modal.peso1 !== null && data_modal.peso1 !== undefined) ? data_modal.peso1 : "";

        var presion_arterial_media =
        (data_modal !== null && data_modal.presion_arterial_media1 !== null && data_modal.presion_arterial_media1 !== undefined) ? data_modal.presion_arterial_media1 : "";
        
        var latidos = 
        (data_modal !== null && data_modal.latidos1 !== null && data_modal.latidos1 !== undefined) ? data_modal.latidos1 : "";

        var movimientos = 
        (data_modal !== null && data_modal.movimientos1 !== null && data_modal.movimientos1 !== undefined) ? data_modal.movimientos1 : "";

        var utero = 
        (data_modal !== null && data_modal.utero1 !== null && data_modal.utero1 !== undefined) ? data_modal.utero1 : "";

        var dinamica = 
        (data_modal !== null && data_modal.dinamica1 !== null && data_modal.dinamica1 !== undefined) ? data_modal.dinamica1 : "";

        var flujos = 
        (data_modal !== null && data_modal.flujos1 !== null && data_modal.flujos1 !== undefined) ? data_modal.flujos1 : "";

        if(data_modal !== null && data_modal.glasgow1 !== null && data_modal.glasgow1 !== undefined){
            dato_glasgow =  parseInt(data_modal.glasgow1);
            if(dato_glasgow >= 3 && dato_glasgow <= 8){
                $("#glassgowx2-categoria").text("Grave");
            }else if(dato_glasgow >= 9 && dato_glasgow <= 12){
                $("#glassgowx2-categoria").text("Moderado");
            }else if(dato_glasgow >= 13 && dato_glasgow <= 15){
                $("#glassgowx2-categoria").text("Leve");
            }
            
        }else{
            $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
            $("#glassgowx2-categoria").text("");
        }

        $("[name='fecha_signo_vital']").val(fecha);
        $(".cuidado-signo-horario").val(hora_min);
        $("[name='arterial1']").val(p_arterial_sis);
        $("[name='arterial1dia']").val(p_arterial_dias);
        $("[name='pulso1']").val(frec_cardiaca);
        $("[name='respiratoria1']").val(frec_respiratoria);
        $("[name='axilo1']").val(temp_axilo_rectal);
        $("[name='rectal']").val(temp_rectal);
        $("[name='saturacion1']").val(saturacion_oxigeno);
        $("[name='hemoglucotest1']").val(hemoglucotest);
        $("[name='glasgow1']").val(glasgow);
        $("[name='metodo1']").val(metodo_o2);
        $("[name='dolor1']").val(dolor_eva);
        $("[name='fio1']").val(fio); //.trigger("change");
        $("[name='estado_conciencia1']").val(estado_conciencia);
        $("[name='peso1']").val(peso);
        $("[name='arterial1media1']").val(presion_arterial_media);
        $("[name='latidos_cardio_fetales']").val(latidos);
        $("[name='movimientos_fetales']").val(movimientos);
        $("[name='utero']").val(utero);
        $("[name='dinamica_uterina']").val(dinamica);
        $("[name='flujo_genital']").val(flujos);

        //si tiene planificacion posterior a la fecha de creacion del signo dar opcion de agregar la atencion como checkeada
        if(data_modal !== null){
            // callAjaxCheckIfPlanificacionAfterSignos(caso_id, signos_vitales_id, hora, fecha,id_indicacion_medica).then(function(data){
            if(id_indicacion_medica == ""){
                $.ajax({
                    url: "{{route('check-planificacion-despues-signos')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {'caso_id': caso_id, 'signos_vitales_id': signos_vitales_id, 'hora' : hora, 'fecha' : fecha},
                    dataType: "json",
                    type: "post",
                }).then(function(resp){
                    data = resp.data;
                    if(data.atencion_is_not_check === true){
                        console.log("falta check 2");
                        //muestra modal
                        //modal.modal("show");
                        //bootbox preguntar si agregar check atencion
                        bootbox.confirm({
                            message: "<h4>Esta hora tiene signos vitales tomados, ¿Desea marcar la hora como 'realizada'?</h4>",
                            buttons: {
                                confirm: {
                                    label: 'Si',
                                    className: 'btn-success'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn-danger'
                                }
                            },
                            callback: function (result) {
                                if(result){
                                    callAjaxAddCuidadoSignosVitales(data).then(function(res){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: res.exito,
                                        });

                                        tipo = "{{Auth::user()->tipo}}";
                                        contenedor.empty();
                                        contenedor.removeAttr("style");
                                        var html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div>';

                                        if
                                        (tipo === "gestion_clinica"){
                                            html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div>';
                                        }
                                        else if
                                        (tipo === "tens"){
                                            html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div>';
                                        }
                                        else if
                                        (tipo === "matrona"){
                                            html = '<div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div>';
                                        }
                                        contenedor.html(html);
                                    }).fail(function (jqXHR, exception) {
                                        var error = JSON.parse(jqXHR.responseText).data;
                                        swalError.fire({
                                            title: 'Error',
                                            text:error
                                        });
                                    });
                                }
                            }
                        });
                    } else {
                        //muestra modal
                        $("#form-signos-vitales").modal("show");
                        //Falta actualizar tabla de fondo en caso de que encuentre datos y no tenga su check
                    }

                });
            }else{
                return false;
            }

               

            // }).fail(function (jqXHR, exception) {
            //     var error = JSON.parse(jqXHR.responseText).data;
            //     swalError.fire({
            //     title: 'Error',
            //     text:error
            //     });
            // });

        } else {

            //muestra modal
            $("#form-signos-vitales").modal("show");
        }

    }).fail(function (jqXHR, exception) {
        var error = JSON.parse(jqXHR.responseText).data;
        swalError.fire({
            title: 'Error',
            text:error
        });
    });

}

$(document).ready(function() {

    function limpiarGlasgowx2(){
        $('#ocular2').val('').change();
        $('#verbal2').val('').change();
        $('#motora2').val('').change();
        $("#detalleGlasgowx2").val("");
        $('#totalGlasgowX2').val('');
    }

    $("#HECuidadoEnfermeria").bootstrapValidator({
        excluded: ':disabled',
        fields: {

        }
        }).on('status.field.bv', function(e, data) {
        //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#brnActualizarCuidados").prop("disabled", true);
            evt.preventDefault(evt);

            bootbox.confirm({
                message: "<h4>¿Está seguro de actualizar la información?</h4>",
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
                    callAjaxAddCuidado().then(function(data){
                        if (data.exito) {
                            swalExito.fire({
                            title: 'Exito!',
                            text: data.exito,
                            didOpen: function() {
                                setTimeout(function() {
                                    cargarCuidados();
                                }, 2000)
                            },
                            });
                        }

                        if (data.error) {
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            }).then(function(result) {
                                cargarCuidados();
                            });
                        }
                    }).fail(function (error) {
                        $("#brnActualizarCuidados").prop("disabled", false);
                    });
                }
            }
        });
        $("#brnActualizarCuidados").prop("disabled", false);
    });

    $( ".contenedor-atenciones" ).on( "click", ".tipo-atencion-realizada", function(e) {

        var tipo_atencion = $( this ).attr("data_tipo_atencion");
        if(tipo_atencion === "Control de signos vitales"){
            if(typeof $( this ).attr("data_id_atencion") !== 'undefined'){
                array_id_atencion = $( this ).attr("data_id_atencion").split("_");
                $("#idIndicacionMedica").val(array_id_atencion[0]);
            }
            e.preventDefault();
            var contenedor = $(this);
            var hora = $( this ).attr("data_hora_atencion");
           var id_indicacion_medica = $("#idIndicacionMedica").val();
            count_clicks_modal_signos_vitales++;
            if(count_clicks_modal_signos_vitales === 1){
                modalSignosVitalesPorHora(contenedor,hora,id_indicacion_medica);
            }
        }


    });

    $("#validarGlasgow3").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {                
            ocular2:{
                    validators:{
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            },
            verbal2:{
                    validators:{
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            },
            motora2:{
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
        let valor = $('#totalGlasgowX2').val();
        $(".g1:not(:hidden)").val(valor);
        $("#glasgow-modal").modal("hide");
    });

    $(".selectglasgowX2").change(function(){
        r_ocular  = $("#ocular2").val();
        r_verbal = $("#verbal2").val();
        r_motora = $("#motora2").val();

        sumag = Number(r_ocular) + Number(r_verbal) + Number(r_motora);

        $("#totalGlasgowX2").val(sumag);

        if(sumag >= 3 && sumag <= 8){
            $("#detalleGlasgowx2").val("Grave");
        }else if(sumag >= 9 && sumag <= 12){
            $("#detalleGlasgowx2").val("Moderado");
        }else if(sumag >= 13 && sumag <= 15){
            $("#detalleGlasgowx2").val("Leve");
        }
			
    });

    $('#glasgow-modal').on('show.bs.modal', function () {
        if($('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val() != ''){
            arrayGlasgow2 = $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val().split(",");
            $('#ocular2').val(arrayGlasgow2[0]);
            $('#verbal2').val(arrayGlasgow2[1]);
            $('#motora2').val(arrayGlasgow2[2]);

            sumag2 = Number(arrayGlasgow2[0]) + Number(arrayGlasgow2[1]) + Number(arrayGlasgow2[2]);
            if(sumag2 >= 3 && sumag2 <= 8){
                $("#detalleGlasgowx2").val("Grave");
            }else if(sumag2 >= 9 && sumag2 <= 12){
                $("#detalleGlasgowx2").val("Moderado");
            }else if(sumag2 >= 13 && sumag2 <= 15){
                $("#detalleGlasgowx2").val("Leve");
            }
            $("#totalGlasgowX2").val(sumag2);

            $('#validarGlasgow3').bootstrapValidator('revalidateField', 'ocular2');
            $('#validarGlasgow3').bootstrapValidator('revalidateField', 'verbal2');
            $('#validarGlasgow3').bootstrapValidator('revalidateField', 'motora2');
        }else{
            $('#ocular2').val('').change();
            $('#verbal2').val('').change();
            $('#motora2').val('').change();
            $("#detalleGlasgowx2").val("");
            $('#totalGlasgowX2').val('');
        }
    });

    $('#HESignosVitalesModal').on('hidden.bs.modal', function(){
        //Al momento de ocultar el modal se limpian todos los campos, pero se debe rescatar el caso
        $('#ocular2').val('').change();
        $('#verbal2').val('').change();
        $('#motora2').val('').change();
        $("#detalleGlasgowx2").val("");
        $('#totalGlasgowX2').val('');
        $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
        caso = $('#HESignosVitalesModal').find("input[name='caso']").val();
        $("#HESignosVitalesModal").data('bootstrapValidator').resetForm();
        $('#HESignosVitalesModal').find("input[name='caso']").val(caso)
    });

    $("#añadirGlasgow1x").on("click", function(){
        $('#validarGlasgow3').bootstrapValidator('revalidateField', 'ocular2');
        $('#validarGlasgow3').bootstrapValidator('revalidateField', 'verbal2');
        $('#validarGlasgow3').bootstrapValidator('revalidateField', 'motora2');

        $('#glassgowx2-categoria').text($("#detalleGlasgowx2").val());
        apertura_ocularx2 = $('#ocular2').val();
        respuesta_verbalx2 = $('#verbal2').val();
        respuesta_motorax2 = $('#motora2').val();

        arrayGlasgowx2 = apertura_ocularx2 +','+ respuesta_verbalx2 +','+ respuesta_motorax2;
        $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val(arrayGlasgowx2);
    });

    $("#glasgow_control_signos_delete").on("click", function(event){
        event.preventDefault();
        $('#g1_modal').val('');
        $('#glassgowx2-categoria').text("");
        $('#HESignosVitalesModal').find("input[name='arrayGlasgowx2']").val("");
    });    

});

</script>

<fieldset>

    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HECuidadoEnfermeria')) }}

    {{ Form::hidden ('turno', $tipo_turno , array('class' => '') )}}


    <div class="col-md-12">
        {{ Form::hidden ('json_signos_vitales', null , array('class' => '') )}}
        <div class="cuidadosPlanificados cuidadosEnfermeria cargarInputs">
            @if(isset($datos_indicaciones_medicas) && !empty($datos_indicaciones_medicas))
                <div class="col-md-12 pl-0 pr-0">
                    @foreach ($datos_indicaciones_medicas as $key => $indicaciones)
                        @if(count($datos_indicaciones_medicas) > 1)
                            <div class="col-md-6 pl-0 pr-0">    
                        @else
                            <div class="">
                        @endif
                                <div class="panel panel-default">
                                    <div class="panel-body"> 
                                        <div class="col-md-12">
                                            @if($key == 0)
                                                @if($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 21 &&  \Carbon\Carbon::now()->format('H') <= 23)
                                                    <span class="input-no-style">{{\Carbon\Carbon::now()->format('d-m-Y')}}</span>
                                                @elseif ($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 0 &&  \Carbon\Carbon::now()->format('H') <= 8)
                                                    <span class="input-no-style">{{\Carbon\Carbon::now()->subDay(1)->format('d-m-Y')}}</span>
                                                @else 
                                                    <span class="input-no-style">{{\Carbon\Carbon::now()->format('d-m-Y')}}</span>
                                                @endif
                                            @else
                                                @if($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 21 &&  \Carbon\Carbon::now()->format('H') <= 23)
                                                    <span class="input-no-style">{{\Carbon\Carbon::now()->addDays(1)->format('d-m-Y')}}</span>  
                                                @elseif ($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 0 &&  \Carbon\Carbon::now()->format('H') <= 8)
                                                    <span> class="input-no-style"{{\Carbon\Carbon::now()->format('d-m-Y')}}</span>  
                                                @endif
                                            @endif
                                        </div> 
                                        @if($indicaciones->tipo_reposo == 5)
                                            <div class="col-md-9" style="margin-bottom: 15px;pointer-events: none;">
                                                <div class="col-md-3 pl-0 pr-0">
                                                    <b>REPOSO:</b> Otro
                                                </div>
                                                <div class="col-md-9 pl-0 pr-0">
                                                    Comentario:{{$indicaciones->otro_reposo}}
                                                </div>
                                            </div> 
                                        @else
                                            <div class="col-md-3" style="pointer-events: none;">
                                                <label for="nombre">
                                                    REPOSO:
                                                </label>
                                                @if($indicaciones->tipo_reposo == 1)
                                                    Absoluto
                                                @elseif($indicaciones->tipo_reposo == 2)
                                                    Semisentado 
                                                    @if($indicaciones->grados_semisentado != null) 
                                                        (°{{$indicaciones->grados_semisentado}})
                                                    @endif
                                                @elseif($indicaciones->tipo_reposo == 3)
                                                    Relativo
                                                @elseif($indicaciones->tipo_reposo == 4)
                                                    Relativo asistido
                                                @endif
                                            </div> 
                                        @endif
                                        <div class="col-md-9" style="pointer-events: none;">
                                            <label for="nombre">
                                                REGIMEN:
                                            </label>
                                            <div class="col-md-12 pl-0 pr-0">
                                                @if($indicaciones->tipo_via == 5)
                                                    <div class="col-md-12 pl-0 pr-0" style="margin-bottom: 15px">
                                                        <div class="col-md-3 pl-0 pr-0">
                                                            Via:Otro
                                                        </div>
                                                        <div class="col-md-9 pl-0 pr-0">
                                                            @if($indicaciones->detalle_via != null) 
                                                            Comentario:{{$indicaciones->detalle_via}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                <div class="col-md-2 pl-0 pr-0">
                                                    Via:
                                                    @if($indicaciones->tipo_via == 1)
                                                        Oral
                                                    @elseif($indicaciones->tipo_via == 2)
                                                        SNY
                                                    @elseif($indicaciones->tipo_via == 3)
                                                        SNG
                                                    @elseif($indicaciones->tipo_via == 4)
                                                        Parental
                                                    @endif
                                                </div>
                                                @endif
                                                @if($indicaciones->tipo_via == 5)
                                                    <div class="col-md-12 pl-0 pr-0" style="margin-bottom: 15px">
                                                        <div class="col-md-3 pl-0 pr-0">
                                                            Consistencia:Otro
                                                        </div>
                                                        <div class="col-md-9 pl-0 pr-0">
                                                            @if($indicaciones->detalle_consistencia != null) 
                                                                Comentario:{{$indicaciones->detalle_consistencia}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-md-3 pl-0 pr-0">
                                                        Consistencia:
                                                        @if($indicaciones->tipo_consistencia == 1)
                                                            Líquido
                                                        @elseif($indicaciones->tipo_consistencia == 2)
                                                            Blando
                                                        @elseif($indicaciones->tipo_consistencia == 3)
                                                            Papillas
                                                        @elseif($indicaciones->tipo_consistencia == 4)
                                                            Común
                                                        @endif
                                                    </div>
                                                @endif
                                                @if(str_contains($tipos_indicaciones_medicas, 'Otro'))
                                                    <div class="col-md-12 pl-0 pr-0">
                                                        <div class="col-md-3 pl-0 pr-0">
                                                            Regimen: 
                                                            @if(isset($tipos_indicaciones_medicas) && !empty($tipos_indicaciones_medicas))
                                                                {{$tipos_indicaciones_medicas}}
                                                            @endif
                                                        </div>
                                                        <div class="col-md-9 pl-0 pr-0">
                                                            @if($indicaciones->detalle_tipo != null) 
                                                                Comentario:{{$indicaciones->detalle_tipo}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-md-5 pl-0 pr-0">
                                                        Regimen: 
                                                        @if(isset($tipos_indicaciones_medicas) && !empty($tipos_indicaciones_medicas))
                                                            {{$tipos_indicaciones_medicas}}
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="col-md-2 pl-0 pr-0">
                                                    @if($indicaciones->volumen != null)
                                                        Volumen: {{$indicaciones->volumen}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>  <!--  div pertenece a la linea 793 -->
                    @endforeach
                </div>
            @endif

            <div class="col-md-12" style="pointer-events: none;">
                <div class="col-md-offset-2 col-md-3">
                    @if($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 21 &&  \Carbon\Carbon::now()->format('H') <= 23)
                        <input type="text" id="fecha_uno" name="fecha_uno" class="form-control input-no-style" value="{{\Carbon\Carbon::now()->format('d-m-Y')}}">
                    @elseif ($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 0 &&  \Carbon\Carbon::now()->format('H') <= 8)
                        <input type="text" id="fecha_uno" name="fecha_uno" class="form-control input-no-style" value="{{\Carbon\Carbon::now()->subDay(1)->format('d-m-Y')}}">
                    @else 
                        <input type="text" id="fecha_uno" name="fecha_uno" class="form-control input-no-style" value="{{\Carbon\Carbon::now()->format('d-m-Y')}}">
                    @endif
                </div> 
    
                @if($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 21 &&  \Carbon\Carbon::now()->format('H') <= 23)
                    <div class="col-md-7">
                        <input type="text" id="fecha_dos" name="fecha_dos" class="form-control input-no-style" value="{{\Carbon\Carbon::now()->addDays(1)->format('d-m-Y')}}">
                    </div>
                    @elseif ($tipo_turno === "noche" && \Carbon\Carbon::now()->format('H') >= 0 &&  \Carbon\Carbon::now()->format('H') <= 8)
                    <div class="col-md-7">
                        <input type="text" id="fecha_dos" name="fecha_dos" class="form-control input-no-style" value="{{\Carbon\Carbon::now()->format('d-m-Y')}}">
                    </div>
                @endif

            </div>
            <div class="col-md-12" style="border-bottom-style: solid; border-bottom-width: 4px;">
                <div class="col-md-2">CUIDADO</div>
                <div class="col-md-10">
                    @foreach($turno as $t)
                        <div class="col-md-1" style="text-align:center">{{ $t }}</div>
                    @endforeach
                </div>
            </div>

            @foreach($atencion_realizar as $key => $atencion)
            <div class="col-md-12 contenedor-atenciones" style="border-bottom-style: solid; border-bottom-width: 4px;">
                <div class="col-md-2">{{ $atencion }}</div>
                <div class="col-md-10">
                @for($i = 0; $i<12 ;$i++)

                    @if(isset($atencion_realizada[$key])  && in_array((int) $turno[$i], $atencion_realizada[$key]))
                    {{-- {{ $tipo_profecional_realizada[$key][(int) $turno[$i]]["tipoU"] }} {{ $i }} {{ $turno[$i] }} --}}

                        @if($tipo_profecional_atencion[$key][(int) $turno[$i]]["tipoU"] == 'gestion_clinica')
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" ><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @elseif($tipo_profecional_atencion[$key][(int) $turno[$i]]["tipoU"] == 'tens')
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" ><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @elseif($tipo_profecional_atencion[$key][(int) $turno[$i]]["tipoU"] == 'matrona')
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" ><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @else
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" ><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div></div>
                        @endif
                    @elseif(in_array((int) $turno[$i], $horario_realizar[$key]))
                        {{-- Son horarios en que deben realizarce atenciones --}}
                        @if( $indice_turno > $i   )
                            {{-- Si se paso en la hora actual --}}
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" style="background-color:rgb(248, 2, 2);">{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$cuidados[$key][0] , false, ['class' => 'form-control']) }}</div>
                        @else
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" style="background-color:rgb(15, 99, 7);">{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$cuidados[$key][0] , false, ['class' => 'form-control']) }}</div>
                        @endif

                    @else
                        <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$atencion}}" >{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$cuidados[$key][0] , false, ['class' => 'form-control']) }}</div>
                    @endif


                @endfor
                </div>
            </div>
            @endforeach
            @foreach($indicacion_realizar as $key2 => $indicacion)
            <div class="col-md-12" style="border-bottom-style: solid; border-bottom-width: 4px;">
                <div class="col-md-2">
                    @if($indicacion[1] != null)
                        <b>({{ $indicacion[4] }})</b><br>
                        {{ $indicacion[1] }}
                    @elseif($indicacion[2] != null)
                        <b>({{ $indicacion[4] }})</b><br>
                        {{ $indicacion[2] }} <br>
                        {{ $indicacion[3] }}
                    @endif
                 </div>
                <div class="col-md-10">
                @for($i = 0; $i<12 ;$i++)

                    @if(isset($indicacion_realizada[$key2])  && in_array((int) $turno[$i], $indicacion_realizada[$key2]))

                        @if($tipo_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'gestion_clinica')
                            <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @elseif($tipo_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'tens')
                            <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @elseif($tipo_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'matrona')
                            <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                        @else
                            <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div></div>
                        @endif

                    @elseif(in_array((int) $turno[$i], $horario_realizar_indicacion[$key2]))
                        {{-- Son horarios en que deben realizarce indicaciones --}}
                        @if( $indice_turno > $i   )
                            {{-- Si se paso en la hora actual --}}
                            <div class="col-md-1" style="background-color:rgb(248, 2, 2);">{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$indicacion[0] , false, ['class' => 'form-control']) }}</div>
                        @else
                            <div class="col-md-1" style="background-color:rgb(15, 99, 7);">{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$indicacion[0] , false, ['class' => 'form-control']) }}</div>
                        @endif

                    @else
                        <div class="col-md-1">{{ Form::checkbox('cuidadoRealizado[]', $i.'_'.$indicacion[0] , false, ['class' => 'form-control']) }}</div>
                    @endif

                @endfor
                </div>
            </div>
            @endforeach


            @foreach($indicacion_medica_realizar as $key2 => $indicacion_medica)
            <div class="col-md-12 contenedor-atenciones" style="border-bottom-style: solid; border-bottom-width: 4px;">
                <div class="col-md-2">
                @if($indicacion_medica[1] != null && $indicacion_medica[1] == 'Farmacos' || $indicacion_medica[1] != null && $indicacion_medica[1] == 'Suero')
                    {{ $indicacion_medica[1] }}<br>
                @if($indicacion_medica[3] != null && $indicacion_medica[3] == 1)
                    (Enfermera)<br>
                @elseif($indicacion_medica[3] != null && $indicacion_medica[3] == 2)
                    (Tens)<br>
                @endif
                    {{ $indicacion_medica[2] }}
                    @else
                        INDICACIÓN MEDICA<br>
                        @if($indicacion_medica[3] != null && $indicacion_medica[3] == 1)
                            (Enfermera)<br>
                        @elseif($indicacion_medica[3] != null && $indicacion_medica[3] == 2)
                            (Tens)<br>
                        @endif
                        {{ $indicacion_medica[1] }}
                    @endif
                 </div>
                <div class="col-md-10">
                @for($i = 0; $i<12 ;$i++)

                    @if(isset($indicacion_medica_realizada[$key2])  && in_array((int) $turno[$i], $indicacion_medica_realizada[$key2]))
                        @if($indicacion_medica[1] == 'Control de signos vitales')
                            @if($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'gestion_clinica')
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @elseif($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'tens')
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @elseif($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'matrona')
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @else
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div></div>
                            @endif
                        @else
                            @if($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'gestion_clinica')
                                <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: blue; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @elseif($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'tens')
                                <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #A3B5FD; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @elseif($tipo_medica_profecional_indicacion[$key2][(int) $turno[$i]]["tipoU"] == 'matrona')
                                <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="color: #8D121D; font-size: 30px; line-height: 1.4 !important;"></label></div></div>
                            @else
                                <div class="col-md-1"><div style="text-align:center;"><label class="glyphicon glyphicon-ok-sign" style="font-size: 30px; line-height: 1.4 !important; "></label></div></div>
                            @endif
                            
                        @endif
                    @elseif(in_array((int) $turno[$i], $horario_realizar_indicacion_medica[$key2]))
                        @if($indicacion_medica[1] == 'Control de signos vitales')
                            {{-- Son horarios en que deben realizarce indicaciones --}}
                            @if( $indice_turno > $i   )
                                {{-- Si se paso en la hora actual --}}
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}" style="background-color:rgb(248, 2, 2);">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                            @else
                                <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}" style="background-color:rgb(15, 99, 7);">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                            @endif
                        @else
                            {{-- Son horarios en que deben realizarce indicaciones --}}
                            @if( $indice_turno > $i   )
                                {{-- Si se paso en la hora actual --}}
                                <div class="col-md-1" style="background-color:rgb(248, 2, 2);">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                            @else
                                <div class="col-md-1" style="background-color:rgb(15, 99, 7);">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                            @endif
                        @endif
                    @else
                        @if($indicacion_medica[1] == 'Control de signos vitales')
                            <div class="col-md-1 tipo-atencion-realizada" data_hora_atencion="{{$turno[$i]}}" data_tipo_atencion="{{$indicacion_medica[1]}}" data_id_atencion="{{$indicacion_medica[0]}}">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                        @else
                            <div class="col-md-1">{{ Form::checkbox('cuidadoMedicoRealizado[]', $i.'_'.$indicacion_medica[0] , false, ['class' => 'form-control']) }}</div>
                        @endif
                    @endif

                @endfor
                </div>
            </div>
            @endforeach

        </div>

        @if(empty($atencion_realizar) && empty($indicacion_realizar) && empty($indicacion_medica_realizar))
            <div class="col-md-12 sinCuidadosEnfermeria" style="text-align: center">
                <h4>No se encontraron cuidados de enfermería planificados</h4>
            </div>
        @else
            <div class="col-md-12">
                <button type="submit" class="btn btn-success" id="brnActualizarCuidados" style="margin-top:15px;">Guardar</button>
            </div>
        @endif

    </div>

    {{ Form::close() }}

<!-- modals -->
<div class="modal fade" id="form-signos-vitales">
    <div class="modal-dialog" style="min-width:960px;">
	    <div class="modal-content">
	      <div class="modal-header">
	      		<button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title" style="text-align: center;">Control de signos vitales</h4>
	      </div>
	      {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HESignosVitalesModal')) }}
	      <div class="modal-body">
	      	<div class="row">
	            {{ Form::hidden ('caso', $caso_id_encrypted, array('class' => 'idCasoEnfermeria') )}}
	            {{ Form::hidden ('idIndicacionMedica', null, array('id' => 'idIndicacionMedica') )}}
                <!-- formulario glasgow -->
                {{ Form::hidden('arrayGlasgowx2','', array()) }}
	            <div class="col-md-12 formulario">
	                <div class="panel panel-default" style="width: auto">
	
	                    <div class="panel-body">
	
	                        @if($sub_categoria == 3)
                                @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.signosVitalesNeoRegistroEnfermeria')
                            @else
                                <div class="col-md-12" style="pointer-events: none;">
	
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_fecha_signo_vital', "FECHA", array( ))}}
                                        <div class="form-group"> {{Form::text('fecha_signo_vital', null, array( 'class' => 'cuidado-signo-fecha form-control cuidado-signo-input', 'id' => 'fecha_signo_vital_modal', 'autocomplete' => 'off'))}} </div>
                                    </div>

                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_horario_signo_vital', "HORARIO", array( ))}}
                                        <div class="form-group"> {{Form::text('horario1', null, array( 'class' => 'cuidado-signo-horario form-control cuidado-signo-input', 'placeholder' => 'HH:mm', 'autocomplete' => 'off'))}} </div>
                                    </div>

                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_presion_arterial_sistolica', "P. Arterial Sis. (mmHg)", array( ))}}
                                        <div class="form-group">
                                            {{Form::number('arterial1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_presion_arterial_diastolica', "P. Arterial Dias. (mmHg)", array( ))}}
                                        <div class="form-group">
                                            {{Form::number('arterial1dia', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_pulso', "Frec. cardiaca (Lpm)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('pulso1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_frec_respiratoria', "Frec. Respiratoria (Rpm)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('respiratoria1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_temp_axilo', "Temp. Axilar (°C)&nbsp;&nbsp;&nbsp;", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('axilo1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 texto col-md-offset-1 texto">
                                        {{Form::label('lbl_temp_rectal', "Temp. Rectal (°C)&nbsp;&nbsp;&nbsp;", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('rectal', null, array( 'step' => '0.1','class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_saturacion', "Sat. de oxígeno (%)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('saturacion1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_hemoglucotest', "Hemoglucotest (mg/dl)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('hemoglucotest1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-3 texto " style="padding-left:5px;padding-rigth:5px;">
                                        {{Form::label('lbl_glasgow', "Glasgow", array( 'class' => ''))}}
                                        </br>
                                        <div class="form-group col-md-12">
                                            <div class="col-md-4 esteModal">
                                                {{Form::text('glasgow1', null, array( 'class' => 'form-control valorg cuidado-signo-input g1', 'readonly', /*'data-toggle="modal"', 'data-target="#glasgowXmodal"',*/ 'id' => 'g1_modal'))}}
                                            </div>
                                            <div class="col-md-5 contenedor-btn-calcular-glassglow">
                                            {{-- <input id="btnCalcularGlassglow" class="btn btn-primary " type="button" value="Calcular" onclick="calcGlassglow();"> --}}
                                            <a href="#" class="btn btn-primary"  data-toggle="modal" data-target="#glasgow-modal" style="margin-left:-9px;">Calcular</a>
                                            </div>
                                            <div class="col-md-1 contenedor-btn-calcular-glassglow" style="padding-left:0;margin-left:17px;">
                                                <a href="#" class="btn btn-success" id="glasgow_control_signos_delete">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </div>
                                            <div class="col-md-12" style="padding-left: 0">
                                                <div class="col-md-7" style="padding-left: 0;">
                                                    <p id="glassgowx2-categoria"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_metodo_o2', "Metodo O2", array( 'class' => ''))}}
                                        <div class="form-group">
                                        {{Form::select('metodo1', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio', '5' => 'Ambiental'), null, array( 'id' => 'metodo1_modal', 'class' => 'form-control valor cuidado-signo-input','placeholder' => 'Seleccione',"required" => "required")) }}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_dolor', "Dolor (EVA)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::number('dolor1', null, array( 'class' => 'form-control valor cuidado-signo-input', 'min' => '0'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_fio2', "FIO2", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::select('fio1', array('21' => '21','24' => '24', '26' => '26', '28' => '28', '32' => '32', '35' => '35', '36' => '36', '40' => '40', '45' => '45', '50' => '50', '60' => '60', '70' => '70-80', '90' => '90-100'), null, array( 'class' => 'form-control sele cuidado-signo-input','placeholder' => 'Seleccione',"required" => "required")) }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($sub_categoria == 2)
                                <legend>Control obstétrico</legend>
                                <div class="col-md-12">
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_latidos_cardio_fetales', "Latidos Cardio Fetales (LCF)", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::text('latidos_cardio_fetales', null, array('class' => 'form-control cuidado-signo-input valor'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_movimientos_fetales', "Movimientos fetales", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::text('movimientos_fetales', null, array('class' => 'form-control cuidado-signo-input valor'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_utero', "Útero", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::select('utero', array('1' => 'Reposo', '2' => 'Irritable'), null, array( 'id' => 'utero', 'class' => 'form-control sele cuidado-signo-input','placeholder' => 'Seleccione')) }}
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-md-offset-1 texto">
                                        {{Form::label('lbl_dinamica_uterina', "Dinamíca uterina", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::text('dinamica_uterina', null, array('class' => 'form-control cuidado-signo-input valor'))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-2 texto">
                                        {{Form::label('lbl_flujo_genital', "Flujo genital", array( 'class' => ''))}}
                                        <div class="form-group">
                                            {{Form::text('flujo_genital', null, array('class' => 'form-control cuidado-signo-input valor'))}}
                                        </div>
                                    </div>
                                </div>
                            @endif
	
	                    </div>
	                </div>
	            </div>
	        </div>
	      </div>
	       <div class="modal-footer">
				<button type="button" id="btn_guardar_signos_vitales" class="btn btn-primary pull-right" style="display:none;">Guardar</button>
				<button type="button" id="btn_cerrar_signos_vitales" class="btn btn-default pull-right" style="margin-right:20px;">Cerrar</button>
		  </div>
		  {{ Form::close() }}
	    </div>
	</div>
</div>

</fieldset>

{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarGlasgow3', 'autocomplete' => 'off')) }}
<div class="modal fade glasX" id="glasgow-modal" data-backdrop="static">
    {{Form::hidden('editarX', null, array('id' => 'editarX'))}}
    {{Form::hidden('idInputX', null,  array('id' => 'idInputX'))}}
    <div class="modal-dialog" id="dialogprueba">
        <div id="contentprueba" class="modal-content">
            <div class="modal-header">
                <h4 style="text-align: left;display: inline-block;" >Escala de glasgow:</h4>
                <button type="button" class="close" data-dismiss="modal" style="text-align: right;display: inline-block;    padding-top: 10px;">
                    <span>X</span>
                </button>
            </div>
            <div id="bodyprueba" class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-sm-10">
                            <label for="fecha-ingreso" class="control-label" title="Fecha de ingreso">Apertura ocular </label>
                            {{Form::select('ocular2', array(''=>'Seleccione', '1' => '(1 pts.) No abre','2' => '(2 pts.) Al dolor','3' => '(3 pts.) A la voz','4' => '(4 pts.) Espontaneo'), null,array('class' => 'form-control selectglasgowX2', 'id'=>'ocular2'))}}

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-sm-10">
                            <label for="via-ingreso" class="control-label" title="Via ingreso">Respuesta verbal: </label>
                            {{Form::select('verbal2', array(''=>'Seleccione', '1' => '(1 pts.) No hay','2' => '(2 pts.) Sonidos incomprensibles','3' => '(3 pts.) Palabras sueltas','4' => '(4 pts.) Desorientado', '5'=>'(5 pts.) Orientado'), null,array('class' => 'form-control selectglasgowX2', 'id'=>'verbal2'))}}

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-sm-10">
                            <label for="rut" class="control-label" title="Rut">Respuesta motora: </label>
                            {{Form::select('motora2', array(''=>'Seleccione', '1' => '(1 pts.) No','2' => '(2 pts.) Descerebracioón','3' => '(3 pts.) Decorticación','4' => '(4 pts.) Movimientos sin proposito', '5'=>'(5 pts.) Localiza estiumlo doloroso', '6'=>'(6 pts.) Obedece ordenes'), null,array('class' => 'form-control selectglasgowX2', 'id'=>'motora2'))}}

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-md-12">
                        <label for="rut" class="control-label" title="Rut">Total:</label>
                        </div>
                            <div class="col-sm-5">
                            <input type="number" name="total" id="totalGlasgowX2" class="form-control" readonly>
                            </div>
                            <div class="col-sm-5">
                                {{Form::text('detalleGlasgowx2', '', array('readonly','id' => 'detalleGlasgowx2', 'class' => 'form-control'))}}
                            </div>
                        </div>
          
                </div>

                <table class="table table-bordered">
                <thead style="background:#399865; color: cornsilk;">
                    <tr>
                        <th>Resultado</th>
                        <th>Gravedad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>3-8</td>
                        <td>Grave</td>
                    </tr>

                    <tr>
                        <td>9-12</td>
                        <td>Moderado</td>
                    </tr>

                    <tr>
                        <td>13-15</td>
                        <td>Leve</td>
                    </tr>
                </tbody>
            </table>
                <input id="añadirGlasgow1x" type="submit" name="" class="btn btn-primary" value="Añadir">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
