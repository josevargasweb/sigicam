<script>

    var bv_options_dt_signos_vitales = {
            excluded: [':disabled', ':hidden', ':not(:visible)', '[readonly=readonly]'],
            fields: {

            }
        };

    function generarGraficoSignosVitales(opt){

        var dt_dia_grafico_signos_vitales = null;

        if(opt == 'carga_defecto'){
            var now = "{{\Carbon\Carbon::now()->format('d-m-Y')}}";
            $('#dt_dia_grafico_signos_vitales').val(now);
            dt_dia_grafico_signos_vitales = now;
        }
        else if (opt == 'generar_grafico') {
            dt_dia_grafico_signos_vitales = $('#dt_dia_grafico_signos_vitales').val();
        }

        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/graficar-signos-vitales/{{ $caso }}",
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:  {
                'fecha': dt_dia_grafico_signos_vitales,
            },
            dataType: "json",
            type: "post",
            success: function(res){
                if(res.error){
                    swalInfo2.fire({
                        title: 'Información',
                        html:res.error
                    });
                }else{
                    var presion_alterial_sis_data_set = res.master_data_set.presion_alterial_sis_data_set;
                    var presion_alterial_dia_data_set = res.master_data_set.presion_alterial_dia_data_set;
                    var frecuencia_cardiaca_data_set = res.master_data_set.frecuencia_cardiaca_data_set;
                    var frecuencia_respiratoria_data_set = res.master_data_set.frecuencia_respiratoria_data_set;
                    var temperatura_axilo_data_set = res.master_data_set.temperatura_axilo_data_set;
                    var temperatura_rectal_data_set = res.master_data_set.temperatura_rectal_data_set;
                    var saturacion_origeno_data_set = res.master_data_set.saturacion_origeno_data_set;
                    var hemoglucotest_data_set = res.master_data_set.hemoglucotest_data_set;
    
                    // call draw
                    draw(presion_alterial_sis_data_set,presion_alterial_dia_data_set,frecuencia_cardiaca_data_set,frecuencia_respiratoria_data_set,temperatura_axilo_data_set,temperatura_rectal_data_set,saturacion_origeno_data_set,hemoglucotest_data_set,hemoglucotest_data_set);
                }

            },
            error: function(xhr, textStatus, error){
                var error_json = JSON.parse(xhr.responseText);
                $("#grafico_signos_vitales").html("");
                $("#grafico_signos_vitales").append(error_json.error);
            }
        });	



        function draw(presion_alterial_sis_data_set,presion_alterial_dia_data_set,frecuencia_cardiaca_data_set,frecuencia_respiratoria_data_set,temperatura_axilo_data_set,temperatura_rectal_data_set,saturacion_origeno_data_set,hemoglucotest_data_set){
            var horas = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];

            if (tieneDatos(presion_alterial_sis_data_set) == false && tieneDatos(presion_alterial_dia_data_set) == false && tieneDatos(frecuencia_cardiaca_data_set) == false && tieneDatos(frecuencia_respiratoria_data_set) == false && tieneDatos(temperatura_axilo_data_set) == false && tieneDatos(temperatura_rectal_data_set) == false && tieneDatos(saturacion_origeno_data_set) == false && tieneDatos(hemoglucotest_data_set) == false) {
                $('#grafico_signos_vitales').html('<span>No hay datos para mostrar</span>')
            }else{


            Highcharts.chart('grafico_signos_vitales', {
                chart: {
                    zoomType: 'xy'
                },
                exporting: {
                    chartOptions: {
                        yAxis: [{
                            title: {
                                text:'',
                            },
                        }]
                    },
                },
                title: {
                    text: '',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                xAxis: [{
                    categories: horas,
                    crosshair: true
                }],
                yAxis: [{
                    title: {
                        text:'',
                    },
                }],
                
                tooltip: {
                    shared: true
                },
                // plotOptions: {
                // series: {
                //     dataLabels: {
                //         enabled: true,
                //         formatter: function() {
                //         var dentroParentesis = /\(([^)]+)\)/;
                //         var sinParentesis = dentroParentesis.exec(this.series.name);
                //         var name = sinParentesis[1];
                //         return   this.y + " " +name ;
                //         },
                //     }
                // }
                // },
                series: [
                    {
                        name: 'Frecuencia cardiaca (Lpm)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(frecuencia_cardiaca_data_set),
                        // yAxis: 3,
                        data: frecuencia_cardiaca_data_set,
                        color: 'blue',
                        tooltip: {
                            valueSuffix: ' Lpm'
                        }
                    },

                    {
                        name: 'Frec. res. (Rpm)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(frecuencia_respiratoria_data_set),
                        // yAxis: 0,
                        data: frecuencia_respiratoria_data_set,
                        color: 'black',
                        tooltip: {
                            valueSuffix: ' Rpm'
                        }

                    },


                    {
                        name: 'P.Arterial Sis. (mmHg)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(presion_alterial_sis_data_set),
                        // yAxis: 1,
                        data: presion_alterial_sis_data_set,
                        color: 'green',
                        tooltip: {
                            valueSuffix: ' mmHg'
                        }

                    },

                    {
                        name: 'Temp. Axilar (°C)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(temperatura_axilo_data_set),
                        // yAxis: 4,
                        data: temperatura_axilo_data_set,
                        color: 'red',
                        tooltip: {
                            valueSuffix: ' °C'
                        }
                    },

                    {
                        name: 'P.Arterial Dias. (mmHg)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(presion_alterial_dia_data_set),
                        // yAxis: 2,
                        data: presion_alterial_dia_data_set,
                        color: 'purple',
                        tooltip: {
                            valueSuffix: ' mmHg'
                        }

                    },
                    
                    {
                        name: 'Temp. Rectal (°C)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(temperatura_rectal_data_set),
                        // yAxis: 5,
                        data: temperatura_rectal_data_set,
                        color: 'orange',
                        tooltip: {
                            valueSuffix: ' °C'
                        }
                    },
                    {
                        name: 'Sat. de oxígeno (%)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(saturacion_origeno_data_set),
                        // yAxis: 5,
                        data: saturacion_origeno_data_set,
                        // color: 'orange',
                        tooltip: {
                            valueSuffix: ' %'
                        }
                    },
                    {
                        name: 'Hemoglucotest (mg/dl)',
                        type: 'spline',
                        connectNulls: true,
                        showInLegend:tieneDatos(hemoglucotest_data_set),
                        // yAxis: 5,
                        data: hemoglucotest_data_set,
                        // color: 'orange',
                        tooltip: {
                            valueSuffix: 'mg/dl'
                        }
                    },


                ],
                credits: {
                    enabled: false
                },
    
                // responsive: {
                //     rules: [{
                //         condition: {
                //             maxWidth: 600
                //         },
                //         chartOptions: {
                //             legend: {
                //                 floating: false,
                //                 layout: 'horizontal',
                //                 align: 'center',
                //                 verticalAlign: 'bottom',
                //                 x: 0,
                //                 y: 0
                //             },
                //             yAxis: [{
                //                 labels: {
                //                     align: 'right',
                //                     x: 0,
                //                     y: -6
                //                 },
                //                 showLastLabel: false
                //             }, {
                //                 labels: {
                //                     align: 'left',
                //                     x: 0,
                //                     y: -6
                //                 },
                //                 showLastLabel: false
                //             }, {
                //                 visible: false
                //             }]
                //         }
                //     }]
                // },
            });
        }

        }

    }


    function funcionesSignosVitales(){
        $('.dPSigno').datetimepicker({
            format: 'HH:mm'
        }).on("dp.change", function () {
            $('#HESignosVitales').bootstrapValidator('revalidateField', 'horario1');
        });

    }

    function generarTablaSignosVitales(opt){

        var dt_dia_info_signos_vitales = null;
        var dt_dia_info_signos_vitales2 = null;

        if(opt == 'carga_defecto'){
            var now = "{{\Carbon\Carbon::now()->format('d-m-Y')}}";
            $("#dt_dia_info_signos_vitales").val(now);
            $("#dt_dia_info_signos_vitales2").val(now);
            dt_dia_info_signos_vitales = now;
            dt_dia_info_signos_vitales2 = now;
        }
        else if (opt == 'generar_grafico') {
            dt_dia_info_signos_vitales = $("#dt_dia_info_signos_vitales").val();
            dt_dia_info_signos_vitales2 = $("#dt_dia_info_signos_vitales2").val();
        }

        $.ajax({
            url: "{{route('validar-obtener-signos-vitales')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:  {"caso_id": "{{ $caso }}" , "fecha_desde": dt_dia_info_signos_vitales,"fecha_hasta":dt_dia_info_signos_vitales2},
            dataType: "json",
            type: "post",
            success: function(data){
                $("#btnSolicitarSignos").prop("disabled", false);
                if (data.exito) {
                    tableSignosVitales = $("#infoSignosVitales").dataTable({
                        "iDisplayLength": 5,
                        "ordering": true,
                        "searching": true,
                        "destroy":true,
                        "ajax": {
                            url: "{{route('obtener-signos-vitales')}}" ,
                            data: {"caso_id": "{{ $caso }}" , "fecha": dt_dia_info_signos_vitales,"fecha_hasta":dt_dia_info_signos_vitales2},
                            type: 'POST'
                        },
                        "oLanguage": {
                            "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                        },
                        "initComplete": funcionesSignosVitales,
                    });
                }

                if (data.error) {
                    swalInfo2.fire({
                        title: 'Información',
                        html:   data.error
                    }).then(function(result) {

                    });
                }
            },
            error: function(error){
                $("#btnSolicitarSignos").prop("disabled", false);
                console.log(error);
            }
        });
        
        
    }

    function editarGlasgow1(idSolicitud,idFila){
        if($('#arrayGlasgowx2-'+idFila).val() != ''){
            glasgowArray = $('#arrayGlasgowx2-'+idFila).val().split(",");
            $('#glasgowXmodal').find('#ocular').val(glasgowArray[0]);
            $('#glasgowXmodal').find('#verbal').val(glasgowArray[1]);
            $('#glasgowXmodal').find('#motora').val(glasgowArray[2]);

            sumag = Number(glasgowArray[0]) + Number(glasgowArray[1]) + Number(glasgowArray[2]);

            if(sumag >= 3 && sumag <= 8){
                $("#detalleGlasgowSignos").val("Grave");
            }else if(sumag >= 9 && sumag <= 12){
                $("#detalleGlasgowSignos").val("Moderado");
            }else if(sumag >= 13 && sumag <= 15){
                $("#detalleGlasgowSignos").val("Leve");
            }

            $("#totalGlasgowX").val(sumag);
        }
        $("#añadirGlasgow1").hide();
        $("#añadirGlasgowx").show();
        $("#editarX").val("editarX");
        $("#idInputX").val(idFila);
        $("#glasgowXmodal").modal("show");
    }

    function eliminarSignoVital(t) {

        var idSolicitud = $(t).attr('data_signo_vital_id');
        var otros_signos_arr = ($(t).attr('data_otros_signos') !== "false") ? $(t).attr('data_otros_signos').split(",") : [];
        var is_not_check = ($(t).attr('data_is_not_check') === "true") ? true: false;
        var encabezado = "<h4>¿Está seguro de eliminar este signo vital?</h4></br>";
        var check_selector = (otros_signos_arr.length > 0 && !is_not_check) ? "<div class='row'><div class='col-md-6'><div class='form-group'><label for='mantener_check_tras_eliminar_signo'>¿Desea mantener el check para el control de signos vital tomado a las "+otros_signos_arr[0]+"?:</label><select class='form-control' id='mantener_check_tras_eliminar_signo'><option value='true'>Mantener el check en la hora</option><option value='false'>Descheckear la hora</option></select></div></div></div>" : "";
        
        var html = encabezado+check_selector;
        bootbox.confirm({
            message: html,
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarSignoVital",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            "id": idSolicitud,
                            "mantener_check_tras_eliminar_signo" : (otros_signos_arr.length > 0 && !is_not_check) ? $("#mantener_check_tras_eliminar_signo").val() : "false",
                            "fecha_signo_vital" : $("[name='fecha_signo_vital']").val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnSolicitarSignos").prop("disabled", false);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tableSignosVitales.api().ajax.reload(funcionesSignosVitales(), false);
                                generarGraficoSignosVitales('carga_defecto');
                            }

                            if (data.error) {
                              swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    location.reload();
                                }
                                });
                            }
                        },
                        error: function(error){
                            $("#btnSolicitarSignos").prop("disabled", false);
                            console.log(error);
                        }
                    });
                }else{
                    tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                }
            }
        }); 
    }

    function modificarSignoVital(t,idFila) {



        let idSolicitud = $(t).attr('data_signo_vital_id');

        bootbox.confirm({
            message: "<h4>¿Está seguro de modificar la información?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarSignoVital",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            horario1: $("#thorario1-"+idFila).val(),
                            diasigno: $("#tdiasigno-"+idFila).val(),
                            arterial1: $("#tarterial1-"+idFila).val(),
                            arterial1dia: $("#tarterial1dia-"+idFila).val(),
                            pulso1: $("#tpulso1-"+idFila).val(),
                            respiratoria1: $("#trespiratoria1-"+idFila).val(),
                            axilo1: $("#taxilar1-"+idFila).val(),
                            rectal: $("#trectal-"+idFila).val(),
                            saturacion1: $("#tsaturacion1-"+idFila).val(),
                            hemoglucotest1: $("#themo1-"+idFila).val(),
                            glasgow1: $("#tglasgow1-"+idFila).val(),
                            arrayGlasgowx2: $("#arrayGlasgowx2-"+idFila).val(),
                            fio1: $("#tfio1-"+idFila).val(),
                            metodo1: $("#tmetodo1-"+idFila).val(),
                            dolor1: $("#tdolor1-"+idFila).val(),
                            fecha: $("[name='horario1']").val(),
                            tipo: $("#arrayGlasgowTipoForm-"+idFila).val(),
                            arterialmedia: $("#tarterialmedia-"+idFila).val(),
                            estado_conciencia: $("#testadoconciencia1-"+idFila).val(),
                            //peso: $("#tpeso1-"+idFila).val(),
                            peso: $("#tpeso-"+idFila).val(),
                            latido: $("#tlatidos1-"+idFila).val(),
                            movimiento: $("#tmovimientos1-"+idFila).val(),
                            utero: $("#tutero1-"+idFila).val(),
                            dinamica: $("#tdinamicas1-"+idFila).val(),
                            flujo: $("#tflujos1-"+idFila).val(),
                            pam: $("#tpam-"+idFila).val(),
                            temp_central: $("#ttemp_central-"+idFila).val(),
                            pvc: $("#tpvc-"+idFila).val(),
                            pcp: $("#tpcp-"+idFila).val(),
                            gc_ic: $("#tgc_ic-"+idFila).val(),
                            rvs_rvp: $("#trvs_rvp-"+idFila).val(),
                            gcs_ramsa_sas: $("#tgcs_ramsa_sas-"+idFila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                                generarGraficoSignosVitales('carga_defecto');
                            }

                            if (data.error) {
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                    if (result.isDenied) {
                                        //location . reload();
                                        tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                                    }
                                });
                            }
                        },
                        error: function(error){
                            //console.log(error);
                        }
                    });
                }else{
                    //tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                }
            }
        });  


    }

    function limpiarGlasgow1(fila) {
        event.preventDefault();
        $('#tglasgow1-'+fila).val('');
        $('#arrayGlasgowx2-'+fila).val('');
    };   

    function cargarVistaSignosVitales(){
        if(typeof tableSignosVitales !== 'undefined') {
            tableSignosVitales.api().ajax.reload();
            //carga por defecto de grafico signos vitales
            generarGraficoSignosVitales('carga_defecto');
        }else{
            generarTablaSignosVitales('carga_defecto');
            //carga por defecto de grafico signos vitales
            generarGraficoSignosVitales('carga_defecto');
        }
    }

    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "1b"){
                console.log("tabRdc signos vitales : ", tabRdc);
                cargarVistaSignosVitales();
            }

        });

        $("#1ab").click(function(){
            cargarVistaSignosVitales();
        });

        $("#validarGlasgow2").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {                
                ocular:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                verbal:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                motora:{
                        validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }         
                    
        }).on('status.field.bv', function(e, data) {            
            data.bv.disableSubmitButtons(false);          
        }).on("success.form.bv", function(evt){
            evt.preventDefault(evt);

            apertura_ocular = $('#ocular').val();
            respuesta_verbal = $('#verbal').val();
            respuesta_motora = $('#motora').val();
            arrayGlasgow = apertura_ocular +','+ respuesta_verbal +','+ respuesta_motora;
            $('#HESignosVitales').find("input[name='arrayGlasgowx2']").val(arrayGlasgow);

            let valor = $('#totalGlasgowX').val();
            if($("#editarX").val() == "editarX"){
                var idFila = $("#idInputX").val();
                $("#tglasgow1-"+idFila).val(valor);
            }else{
                $("#g1").val(valor);
            }
            $("#glasgow-categoria-signos").text($('#detalleGlasgowSignos').val());

            $("#glasgowXmodal").modal("hide");
        });

        $("#añadirGlasgowx").on("click", function(){
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'ocular');
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'verbal');
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'motora');
            var idFila = $("#idInputX").val();
            let valor = $('#totalGlasgowX').val();
            $("#tglasgow1-"+idFila).val(valor);

            ocular = $('#validarGlasgow2').find('#ocular').val();
            verbal = $('#validarGlasgow2').find('#verbal').val();
            motora = $('#validarGlasgow2').find('#motora').val();
            $("#arrayGlasgowx2-"+idFila).val(ocular+","+verbal+","+motora);
            console.log( $("#arrayGlasgowx2-"+idFila).val());
            $("#glasgowXmodal").modal("hide");
        });

        $("#añadirGlasgow1").on("click", function(){
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'ocular');
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'verbal');
            $('#validarGlasgow2').bootstrapValidator('revalidateField', 'motora');
        });

        $('#glasgowXmodal').on('hidden.bs.modal', function(){
            $("#editarX").val('');
            $("#idInputX").val('');
            $('#ocular').val('').change();
            $('#verbal').val('').change();
            $('#motora').val('').change();
            $('#totalGlasgowX').val('');
            $('#detalleGlasgowSignos').val('');
            // $(".esconderBoton").hide();
        });

        $(".selectglasgowX").change(function(){
            r_ocular  = $("#ocular").val();
            r_verbal = $("#verbal").val();
            r_motora = $("#motora").val();

            sumag = Number(r_ocular) + Number(r_verbal) + Number(r_motora);

            if(sumag >= 3 && sumag <= 8){
                $("#detalleGlasgowSignos").val("Grave");
            }else if(sumag >= 9 && sumag <= 12){
                $("#detalleGlasgowSignos").val("Moderado");
            }else if(sumag >= 13 && sumag <= 15){
                $("#detalleGlasgowSignos").val("Leve");
            }

            $("#totalGlasgowX").val(sumag);
        });

        $("#HESignosVitales").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                	horario1: {
						validators:{
							notEmpty: {
								message: 'la hora es obligatoria'
							}
						}
					},
                    'metodo1': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un Metodo'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    metodo1: validator.getFieldElements('metodo1').val()
                                };
                            },
                            url: "{{URL::to("/validar_metodo1")}}"
                        }
                    }
                }, 
                'fio1': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un fio2'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    fio1: validator.getFieldElements('fio1').val()
                                };
                            },
                            url: "{{URL::to("/validar_fio1")}}"
                        }
                    }
                },
                peso: {
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
            // data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarSignos").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                message: "<h4>¿Está seguro de ingresar la información?</h4>",
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarSignosVitales",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarSignos").prop("disabled", false);
                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    $('#HESignosVitales').find("input[name='arrayGlasgowx2']").val("");
                                    $('#glasgow-categoria-signos').text('');
                                    $("#HESignosVitales").trigger("reset");
                                    // $(".mostrarMascarilla").hide();
                                    // $(".texto").hide();
                                    // $(".glas").hide();
                                    // $(".texto").show();
                                    // $(".valor").removeAttr('disabled');
                                    // $(".sele").attr('disabled','disabled');
                                    // $(".valorg").attr('disabled','disabled');
                                    $("[name='horario1']").prop( "disabled", true );
                                    
                                    tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                                    generarGraficoSignosVitales('carga_defecto');
                                    setTimeout(function (){
                                        $("[name='horario1']").prop( "disabled", false );
                                         $("[name='horario1']").on('click', function (){
                                        let horario =  $("[name='horario1']").data('DateTimePicker');
                                        horario.date(moment("{{ \Carbon\Carbon::now()->format('H:i') }}", 'HH:mm'));
                                    });
                                        $('#HESignosVitales').bootstrapValidator('revalidateField', 'horario1');
                                        $('#HESignosVitales').bootstrapValidator('revalidateField', 'metodo1');
                                        $('#HESignosVitales').bootstrapValidator('revalidateField', 'fio1');
                                        var ug = "{{$sub_categoria}}";
                                        if(ug == 3){
                                            $('#HESignosVitales').bootstrapValidator('revalidateField', 'peso');
                                        }
                                    }, 200);

                                }

                                if (data.faltanDatos) {
                                    swalInfo2.fire({
                                    title: 'Información',
                                    text:data['faltanDatos']
                                    }).then(function(result) {
                                        tableSignosVitales.api().ajax.reload(funcionesSignosVitales, false);
                                    });

                                }

                                if (data.error) {
                                        swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                        }).then(function(result) {
                                        if (result.isDenied) {
                                            // location . reload();
                                        }
                                        });

                                }
                            },
                            error: function(error){
                                $("#btnSolicitarSignos").prop("disabled", false);
                                console.log(error);
                                funcionesSignosVitales()
                            }
                        });
                    }
                }            
            });
            $("#btnSolicitarSignos").prop("disabled", false);  
        });

        $('.dPSigno').datetimepicker({
            format: 'HH:mm'
        }).on("dp.change", function () {
            $('#HESignosVitales').bootstrapValidator('revalidateField', 'horario1');
        });
           

        $('#dt_dia_grafico_signos_vitales').datepicker({
            "format": 'dd-mm-yyyy',
            language: "es"
        });
        $('#dt_dia_info_signos_vitales').datepicker({
            "format": 'dd-mm-yyyy',
            language: "es"
        });
        $('#dt_dia_info_signos_vitales2').datepicker({
            "format": 'dd-mm-yyyy',
            language: "es"
        });

        $("#glasgow_control_delete").on("click", function(event){
            event.preventDefault();
            $('#g1').val('');
            $('#HESignosVitales').find("input[name='arrayGlasgowx2']").val("");
        });


        $(document).on('click', '.btn-modificar-signo-vital', function(e) { 

            e.preventDefault();
            
            let idFila = $(this).attr("data_fila_id");

            if(idFila != null && idFila != undefined){
                modificarSignoVital(this,idFila);
            }

        });
   
    });

        
        
</script>

<style>

/*
    .help-block{
        color: #a94442;
    }
*/
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    .esteModal {
        padding-right: 0;
        padding-left: 0;
    }


    #container {
    height: 400px;
    }

    /* GRAFICO SIGNOS VITALES STYLES */
    .highcharts-figure, .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }
    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }
    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
        padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    .infoth1{
     width: 10%;  
    }
    .infoth2{
     width: 30%;  
    }
    .infoth3{
     width: 35%;  
    }
    .infoth4{
     width: 10%;  
    }
    .infoth5{
     width: 10%;  
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HESignosVitales')) }}
<!-- formulario glasgow -->
{{ Form::hidden ('caso', $caso_id_encrypted, array('class' => 'idCasoEnfermeria') )}}
{{ Form::hidden('arrayGlasgowx2','', array()) }}
<input type="hidden" value="En Curso" name="tipoFormGlasgowSV">

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default" style="width: auto">
            <div class="panel-heading panel-info">
                <h4>CONTROL SIGNOS VITALES</h4>
            </div>

            <div class="panel-body">
                <legend>Ingresar nuevo control de signos vitales</legend>

                @if($sub_categoria == 3)
                    @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.signosVitalesNeonatologia')
                @else
                    <div class="col-md-12">

                        <div class="col-md-2 texto" style="pointer-events: none;">
                            {{Form::label('lbl_fecha_signo_vital', "FECHA", array( ))}}
                            <div class="form-group"> {{Form::text('fecha_signo_vital', \Carbon\Carbon::now()->format('d-m-Y'), array( 'class' => 'form-control', 'id' => 'fecha_signo_vital', 'autocomplete' => 'off'))}} </div>
                        </div>

                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_horario_signo_vital', "HORARIO", array( ))}}
                            <div class="form-group"> {{Form::text('horario1', null, array( 'class' => 'dPSigno form-control', 'placeholder' => 'HH:mm', 'autocomplete' => 'off' , 'autofocus' => 'false','required'))}} </div>
                        </div>

                    </div>


                    <div class="col-md-12">
                        <div class="col-md-2 texto">
                            {{Form::label('lbl_presion_arterial_sistolica', "P. Arterial Sis. (mmHg)", array( ))}}
                            <div class="form-group">
                                {{Form::number('arterial1', null, array( 'class' => 'form-control valor', 'min' => '0' , 'max' => '500', 'step' => '1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_presion_arterial_diastolica', "P. Arterial Dias. (mmHg)", array( ))}}
                            <div class="form-group">
                                {{Form::number('arterial1dia', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '500', 'step' => '1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_pulso', "Frec. cardiaca (Lpm)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('pulso1', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '500', 'step' => '1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_frec_respiratoria', "Frec. Respiratoria (Rpm)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('respiratoria1', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '100', 'step' => '1'))}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-2 texto">
                            {{Form::label('lbl_temp_axilo', "Temp. Axílar (°C)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('axilo1', null, array('step' => '0.1','class' => 'form-control valor', 'min' => '0', 'max' => '50'))}}
                            </div>
                        </div>
                        <div class="col-md-2 texto col-md-offset-1 texto">
                            {{Form::label('lbl_temp_rectal', "Temp. Rectal (°C)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('rectal', null, array( 'step' => '0.1','class' => 'form-control valor', 'min' => '0', 'max' => '50'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_saturacion', "Sat. de oxígeno (%)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('saturacion1', null, array( 'step' => '0.1','class' => 'form-control valor', 'min' => '0', 'max' => '100'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_hemoglucotest', "Hemoglucotest (mg/dl)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('hemoglucotest1', null, array('step' => '0.1', 'class' => 'form-control valor', 'min' => '0', 'max' => '2000'))}}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-md-3 texto">
                                {{Form::label('lbl_glasgow', "Glasgow", array( 'class' => ''))}}
                                <div class="form-group col-md-12">
                                    <div class="col-md-4 esteModal">
                                        {{Form::number('glasgow1', null, array( 'class' => 'form-control valorg ', 'readonly', /*'data-toggle="modal"', 'data-target="#glasgowXmodal"',*/ 'id' => 'g1'))}}
                                    </div>
                                    <div class="col-md-4" style="margin-right:30px;">
                                        {{-- <input id="btnCalcularGlassglow" type="button" name="" class="btn btn-primary" value="Calcular"> --}}
                                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#glasgowXmodal">Calcular</a>
                                    </div>
                                    <div class="col-md-1" style="padding-left:0;">
                                        <a href="#" class="btn btn-success" id="glasgow_control_delete">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-12" style="padding-left: 0">
                                        <div class="col-md-7" style="padding-left: 0;">
                                            <p id="glasgow-categoria-signos"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 texto">
                                {{Form::label('lbl_metodo_o2', "Metodo O2", array( 'class' => ''))}}
                                <div class="form-group">
                                    {{Form::select('metodo1', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio', '5' => 'Ambiental'), null, array( 'id' => 'metodo1', 'class' => 'form-control sele','placeholder' => 'Seleccione')) }}
                                </div>
                            </div>
                            <div class="col-md-2 col-md-offset-1 texto">
                                {{Form::label('lbl_dolor', "Dolor (EVA)", array( 'class' => ''))}}
                                <div class="form-group">
                                    {{Form::number('dolor1', null, array( 'step' => '1', 'class' => 'form-control valor', 'min' => '1', 'max' => '10'))}}
                                </div>
                            </div>
                            <div class="col-md-2 col-md-offset-1 texto">
                                {{Form::label('lbl_fio2', "FIO2", array( 'class' => ''))}}
                                <div class="form-group">
                                    {{Form::select('fio1', array('21' => '21','24' => '24', '26' => '26', '28' => '28', '32' => '32', '35' => '35', '36' => '36', '40' => '40', '45' => '45', '50' => '50', '60' => '60', '70' => '70-80', '90' => '90-100'), null, array( 'id'=> 'fio1','class' => 'form-control sele','placeholder' => 'Seleccione')) }}
                                </div>
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
                                {{Form::text('latidos_cardio_fetales', null, array('class' => 'form-control valor'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_movimientos_fetales', "Movimientos fetales", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::text('movimientos_fetales', null, array('class' => 'form-control valor'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_utero', "Útero", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::select('utero', array('1' => 'Reposo', '2' => 'Irritable'), null, array( 'id' => 'utero', 'class' => 'form-control sele','placeholder' => 'Seleccione')) }}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_dinamica_uterina', "Dinamíca uterina", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::text('dinamica_uterina', null, array('class' => 'form-control valor'))}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2 texto">
                            {{Form::label('lbl_flujo_genital', "Flujo genital", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::text('flujo_genital', null, array('class' => 'form-control valor'))}}
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($sub_categoria == 4)
                    <div class="col-md-12">
                        <div class="col-md-2 texto">
                            {{Form::label('lbl_pam', "Presión arterial media PAM (mmHg)", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('pam', null, array( 'class' => 'form-control valor','step' => '0.1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            <br>
                            {{Form::label('lbl_temp_central', "T° central", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('temp_central', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '50', 'step' => '0.1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            <br>
                            {{Form::label('lbl_pvc', "PVC", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('pvc', null, array( 'class' => 'form-control valor' , 'min' => '0', 'max' => '30','step' => '0.1'))}}
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1 texto">
                            <br>
                            {{Form::label('lbl_pcp', "PCP", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('pcp', null, array( 'class' => 'form-control valor' , 'min' => '0', 'max' => '50','step' => '0.1'))}}
                            </div>
                        </div>

                    </div>

                
                    <div class="col-md-12">

                        <div class="col-md-2 texto">
                            {{Form::label('lbl_peso', "Peso", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('peso', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '700','step' => '0.1'))}}
                            </div>
                        </div>

                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_gc_ic', "GC/IC", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('gc_ic', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '30', 'step' => '0.1'))}}
                            </div>
                        </div>

                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_rvs_rvp', "RVS/RVP", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::number('rvs_rvp', null, array( 'class' => 'form-control valor', 'min' => '0', 'max' => '10000', 'step' => '0.1'))}}
                            </div>
                        </div>

                        <div class="col-md-2 col-md-offset-1 texto">
                            {{Form::label('lbl_gcs_ramsa_sas', "GCS/RAMSA/SAS", array( 'class' => ''))}}
                            <div class="form-group">
                                {{Form::text('gcs_ramsa_sas', null, array( 'class' => 'form-control valor'))}}
                            </div>
                        </div>

                    </div>
                @endif

                <div class="col-md-12 nopadding signoVital">
                    <div class="col-md-2">
                        <input id="btnSolicitarSignos" type="submit" name="" class="btn btn-primary" value="Guardar">
                    </div>
                </div>

                <div class="col-md-12 nopadding">
                    <br><br>
                    <legend>Grafica de signos vitales</legend>
                    <div class="col-md-12 nopadding">
                        <div class="form-group">
                            <label id="lbl_dia_grafico_signos_vitales" for = "dt_dia_grafico_signos_vitales" class="col-md-1 control-label"> Fecha </label>
                            <div class="col-md-3">
                                <input id="dt_dia_grafico_signos_vitales" type="text" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <button id="btn_generar_grafico_signos_vitales" class="btn btn-default" type="button" style="padding-top:6px !important;" onclick="generarGraficoSignosVitales('generar_grafico');">Generar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 nopadding">
                        <div id="grafico_signos_vitales"></div>
                    </div>
                </div>

                <br><br><br>

                <div class="col-md-12 nopadding">
                    <br><br>
                    <legend>Listado de Signos vitales</legend>
                    <div class="col-md-12 nopadding">
                        <div class="form-group">
                            <label id="lbl_dia_info_signos_vitales" for = "dt_dia_grafico_signos_vitales" class="col-md-1 control-label"> Desde </label>
                            <div class="col-md-3">
                                <input id="dt_dia_info_signos_vitales" type="text" class="form-control" autocomplete="off">
                            </div>
                            <label id="lbl_dia_info_signos_vitales2" for = "dt_dia_grafico_signos_vitales" class="col-md-1 control-label"> Hasta </label>
                            <div class="col-md-3">
                                <input id="dt_dia_info_signos_vitales2" type="text" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <button id="btn_generar_grafico_signos_vitales" class="btn btn-default" type="button" style="padding-top:6px !important;" onclick="generarTablaSignosVitales('generar_grafico');">Generar</button>
                            </div>
                        </div>
                    </div>
                    <table id="infoSignosVitales" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th {{-- style="width: 10%" --}} class="infoth1">FECHA DE TOMA</th>
                                @if($sub_categoria == 3)
                                    <th {{-- style="width: 30%" --}} class="infoth2">P. ARTERIAL SIS. / P. ARTERIAL DIAS. / P. ARTERIAL MEDIA / FRECUENCIA CARDIACA / FREC. RESPIRATORIA / TEMP. AXILO/RECTAL / SATURACIÓN </th>
                                    <th {{-- style="width: 35%" --}} class="infoth3">HEMOGLUCO TEST / ESTADO CONCIENCIA / FIO2 / METODO O2 / DOLOR / PESO</th>    
                                @else
                                    <th {{-- style="width: 30%" --}} class="infoth2">P. ARTERIAL SIS. / P. ARTERIAL DIAS. / FRECUENCIA CARDIACA / FREC. RESPIRATORIA / TEMP. AXILO/RECTAL / SATURACIÓN </th>
                                    <th {{-- style="width: 35%" --}} class="infoth3">HEMOGLUCO TEST / GLASGOW / FIO2 / METODO O2 / DOLOR</th>
                                @endif
                                @if($sub_categoria == 2)
                                    <th {{-- style="width: 30%" --}} class="infoth4">LCF / MOVIMIENTOS FETALES / ÚTERO / DINÁMICA UTERINA / FLUJO GENITAL</th>
                                @endif
                                <th {{-- style="width: 1%" --}} class="infoth5">OPCIONES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>

{{ Form::close() }}

{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'validarGlasgow2', 'autocomplete' => 'off')) }}
<div class="modal fade glasX" id="glasgowXmodal" data-backdrop="static">
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
                            {{Form::select('ocular', array(''=>'Seleccione', '1' => '(1 pts.) No abre','2' => '(2 pts.) Al dolor','3' => '(3 pts.) A la voz','4' => '(4 pts.) Espontaneo'), null,array('class' => 'form-control selectglasgowX', 'id'=>'ocular'))}}

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-sm-10">
                            <label for="via-ingreso" class="control-label" title="Via ingreso">Respuesta verbal: </label>
                            {{Form::select('verbal', array(''=>'Seleccione', '1' => '(1 pts.) No hay','2' => '(2 pts.) Sonidos incomprensibles','3' => '(3 pts.) Palabras sueltas','4' => '(4 pts.) Desorientado', '5'=>'(5 pts.) Orientado'), null,array('class' => 'form-control selectglasgowX', 'id'=>'verbal'))}}

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-sm-10">
                            <label for="rut" class="control-label" title="Rut">Respuesta motora: </label>
                            {{Form::select('motora', array(''=>'Seleccione', '1' => '(1 pts.) No','2' => '(2 pts.) Descerebracioón','3' => '(3 pts.) Decorticación','4' => '(4 pts.) Movimientos sin proposito', '5'=>'(5 pts.) Localiza estiumlo doloroso', '6'=>'(6 pts.) Obedece ordenes'), null,array('class' => 'form-control selectglasgowX', 'id'=>'motora'))}}

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-md-12">
                            <label for="total" class="col-form-label" style="margin-bottom: 0px;">Total:</label>
                        </div>
                        <div class="col-sm-5">
                            <input type="number" name="total" id="totalGlasgowX" class="form-control" readonly>
                        </div>
                        <div class="col-sm-5">
                            {{Form::text('detalleGlasgowSignos', "", array('readonly','id' => 'detalleGlasgowSignos', 'class' => 'form-control'))}}
                        </div>
                    </div>
                </div>
                <input id="añadirGlasgow1" type="submit" name="" class="btn btn-primary" value="Añadir">
                <input id="añadirGlasgowx" style="display:none" type="submit" name="" class="btn btn-primary" value="Añadir">
                <br><br>
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
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
