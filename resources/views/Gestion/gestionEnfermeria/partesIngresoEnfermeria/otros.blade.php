<script>

    function ocultarTodos(){
        disableBranula1();
        disableBranula2();
        disableFoley();
        disableSNG();
        disableCVC();
        disableNASO();
        disableTraq();
        disableOsto();
        disableOtro();  
        $("#btGuardarOtro").prop("disabled", true);
    }

    //BRANULA 1
    function disableBranula1(){
        //guardar id_aterior en caso de tenerlo
        var idcateter = $("#numeroIdB1").val();

        $("#detalleBranula1").hide();
        $("#detalleBranula1 :input").prop("disabled", true);
        $("#detalleBranula1 :input").val('');
        $("#numeroIdB1").val(idcateter);
    }

    function enableBranula1(){
        $("#detalleBranula1").show();
        $("#detalleBranula1 :input").prop("disabled", false);
        $("#calculoB1").prop("disabled", true);
    }

    //BRANULA2
    function disableBranula2(){
        var idcateter = $("#numeroIdB2").val();
        $("#detalleBranula2").hide();
        $("#detalleBranula2 :input").prop("disabled", true);
        $("#detalleBranula2 :input").val('');
        $("#numeroIdB2").val(idcateter);
    }

    function enableBranula2(){
        $("#detalleBranula2").show();
        $("#detalleBranula2 :input").prop("disabled", false);
        $("#calculoB2").prop("disabled", true);
    }

    //FOLEY
    function disableFoley(){
        var idcateter = $("#numeroIdF").val();
        $("#detalleFoley").hide();
        $("#detalleFoley :input").prop("disabled", true);
        $("#detalleFoley :input").val('');
        $("#numeroIdF").val(idcateter);
    }

    function enableFoley(){
        $("#detalleFoley").show();
        $("#detalleFoley :input").prop("disabled", false);
        $("#calculoF").prop("disabled", true);
    }

    //SNG
    function disableSNG(){
        var idcateter = $("#numeroIdSng").val();
        $("#detalleSNG").hide();
        $("#detalleSNG :input").prop("disabled", true);
        $("#detalleSNG :input").val('');
        $("#numeroIdSng").val(idcateter);
    }

    function enableSNG(){
        $("#detalleSNG").show();
        $("#detalleSNG :input").prop("disabled", false);
        $("#calculoSng").prop("disabled", true);
    }

    //SNG
    function disableCVC(){
        var idcateter = $("#numeroIdCvc").val();
        $("#detalleCVC").hide();
        $("#detalleCVC :input").prop("disabled", true);
        $("#detalleCVC :input").val('');      
        $("#numeroIdCvc").val(idcateter);  
    }

    function enableCVC(){
        $("#detalleCVC").show();
        $("#detalleCVC :input").prop("disabled", false);
        $("#calculoCvc").prop("disabled", true);
    }

    //Nasoyeyunales
    function disableNASO(){
        var idcateter = $("#numeroIdNasoye").val();
        $("#nasoyeyunales").hide();
        $("#nasoyeyunales :input").prop("disabled", true);
        $("#nasoyeyunales :input").val('');
        $("#numeroIdNasoye").val(idcateter);  
    }

    function enableNASO(){
        $("#nasoyeyunales").show();
        $("#nasoyeyunales :input").prop("disabled", false);
        $("#calculoNasoye").prop("disabled", true);
    }

    //Traqueotomia
    function disableTraq(){
        var idcateter = $("#numeroIdTraqueo").val();
        $("#traqueotomia").hide();
        $("#traqueotomia :input").prop("disabled", true);
        $("#traqueotomia :input").val('');
        $("#numeroIdTraqueo").val(idcateter);  
    }

    function enableTraq(){
        $("#traqueotomia").show();
        $("#traqueotomia :input").prop("disabled", false);
        $("#calculoTraqueo").prop("disabled", true);
    }

    //Traqueotomia
    function disableOsto(){
        var idcateter = $("#numeroIdOsto").val();
        $("#detalleOstomia").hide();
        $("#detalleOstomia :input").prop("disabled", true);        
        $("#detalleOstomia :input").val('');
        $("#numeroIdOsto").val(idcateter); 
        $("input[type='radio'][name='baguetaOsto']").prop("checked", false);
    }

    function enableOsto(){
        $("#detalleOstomia").show();
        $("#detalleOstomia :input").prop("disabled", false);
        $("#baguetaOstoNo").val("no");
        $("#baguetaOstoSi").val("si");
        $("#calculoBolsaOsto").prop("disabled", true);
    }

    //Otros
    function disableOtro(){
        var idcateter = $("#numeroIdOtro").val();
        $("#otro").hide();
        $("#otro :input").prop("disabled", true);
        $("#otro :input").val('');
        $("#numeroIdOtro").val(idcateter);
    }

    function enableOtro(){
        $("#otro").show();
        $("#otro :input").prop("disabled", false);
        $("#calculoOtro").prop("disabled", true);
    }

    function IngresarMostrarOtros(){
        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosCateteres",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (data) {
                if(data.length > 0){
                    var n_cateter = [];
                    disableBranula1();
                    disableBranula2();
                    disableFoley(); 
                    disableSNG(); 
                    disableCVC(); 
                    disableNASO(); 
                    disableTraq(); 
                    disableOsto();
                    disableOtro(); 

                    data.forEach(function(datos) {
                        if(datos.tipo_cateter == 0){
                            //mostrar branula 1 y habilitar campos
                            $("#detalleBranula1").show();
                            enableBranula1();
                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdB1').val(datos.id);
                            $('#numeroB1').val(datos.numero);
                            $("#lugarB1").val(datos.lugar_instalacion);
                            var fechaB1 = datos.fecha_instalacion;
                            if(fechaB1 != null){
                                $('#fechaB1').data("DateTimePicker").date(new Date(fechaB1));
                                var fechaB1sinFormato = datos.fecha_instalacion;
                                $("#calculoB1").val(calculoDias(fechaB1sinFormato));
                            }else{
                                $('#fechaB1').val('');
                                $("#fechaB1").data("DateTimePicker").date(null);
                                $("#calculoB1").val('');
                            }
                            $("#responsableB1").val(datos.responsable_instalcion);
                        }

                        if(datos.tipo_cateter == 1){
                            //mostrar branula 2 y habilitar campos
                            $("#detalleBranula2").show();
                            enableBranula2();
                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdB2').val(datos.id);
                            $('#numeroB2').val(datos.numero);
                            $("#lugarB2").val(datos.lugar_instalacion);
                            var fechaB2 = datos.fecha_instalacion;
                            if(fechaB2 != null){
                                $('#fechaB2').data("DateTimePicker").date(new Date(fechaB2));
                                var fechaB2sinFormato = datos.fecha_instalacion;
                                $("#calculoB2").val(calculoDias(fechaB2sinFormato));
                            }else{
                                $('#fechaB2').val('');
                                $("#fechaB2").data("DateTimePicker").date(null);
                                $("#calculoB2").val('');
                            }
                            $("#responsableB2").val(datos.responsable_instalcion);
                        }

                        if(datos.tipo_cateter == 2){
                            $("#detalleFoley").show();
                            enableFoley();

                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdF').val(datos.id);
                            $('#numeroF').val(datos.numero);
                            $("#lugarF").val(datos.lugar_instalacion);
                            var fechaF = datos.fecha_instalacion;
                            if(fechaF != null){
                                $('#fechaF').data("DateTimePicker").date(new Date(fechaF));
                                var fechaFsinFormato = datos.fecha_instalacion;
                                $("#calculoF").val(calculoDias(fechaFsinFormato));
                            }else{
                                $('#fechaF').val('');
                                $("#fechaF").data("DateTimePicker").date(null);
                                $("#calculoF").val('');
                            }
                            $('#materialF').val(datos.material_fabricacion);
                            if(datos.fecha_curacion != null)
                            {
                                $('#fechaCuracionF').data("DateTimePicker").date(new Date(datos.fecha_curacion));
                            	// $('#fechaCuracionF').val(moment(datos.fecha_curacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY'));
                            }
                            $('#responsableF').val(datos.responsable_curacioin);
                            $('#observacionF').val(datos.observacion);
                        }

                        if(datos.tipo_cateter == 3){
                            $("#detalleSNG").show();
                            enableSNG();

                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdSng').val(datos.id);
                            $('#numeroSng').val(datos.numero);
                            $("#lugarSng").val(datos.lugar_instalacion);
                            var fechaSng = datos.fecha_instalacion;
                            if(fechaSng != null){
                                $('#fechaSng').data("DateTimePicker").date(new Date(fechaSng));
                                var fechaSngsinFormato = datos.fecha_instalacion;
                                 $("#calculoSng").val(calculoDias(fechaSngsinFormato));
                            }else{
                                $('#fechaSng').val('');
                                $("#fechaSng").data("DateTimePicker").date(null);
                                $("#calculoSng").val('');
                            }
                            $('#materialSng').val(datos.material_fabricacion);
                            $('#responsableSng').val(datos.responsable_curacioin);
                        }

                        if(datos.tipo_cateter == 4){
                            $("#detalleCVC").show();
                            enableCVC();

                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdCvc').val(datos.id);
                            $('#numeroCvc').val(datos.numero);
                            $('#tipoCvc').val(datos.tipo);
                            $("#lugarCvc").val(datos.lugar_instalacion);
                            var fechaCvc = datos.fecha_instalacion;
                            if(fechaCvc != null){
                                $('#fechaCvc').data("DateTimePicker").date(new Date(fechaCvc));
                                var fechaCvcsinFormato = datos.fecha_instalacion;
                                $("#calculoCvc").val(calculoDias(fechaCvcsinFormato));
                            }else{
                                $('#fechaCvc').val('');
                                $("#fechaCvc").data("DateTimePicker").date(null);
                                $("#calculoCvc").val('');
                            }
                            $('#materialCvc').val(datos.material_fabricacion);
                            $('#responsableCvc').val(datos.responsable_curacioin);
                            $('#viaCvc').val(datos.via_instalacion);
                        }

                        if(datos.tipo_cateter == 5){
                            $("#nasoyeyunales").show();
                            enableNASO();

                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdNasoye').val(datos.id);
                            $('#numeroNasoye').val(datos.numero);
                            var fechaNasoye = datos.fecha_instalacion;
                            if(fechaNasoye != null){
                                $('#fechaNasoye').data("DateTimePicker").date(new Date(fechaNasoye));
                                var fechaNasoyesinFormato = datos.fecha_instalacion;
                                $("#calculoNasoye").val(calculoDias(fechaB2sinFormato));
                            }else{
                                $('#fechaNasoye').val('');
                                $("#fechaNasoye").data("DateTimePicker").date(null);
                                $("#calculoNasoye").val('');
                            }
                            $("#responsableNasoye").val(datos.responsable_instalcion);
                        }

                        if(datos.tipo_cateter == 6){
                            $("#traqueotomia").show();
                            enableTraq();

                            n_cateter.push(datos.tipo_cateter);
                            $('#numeroIdTraqueo').val(datos.id);
                            $('#cuffTraqueo').val(datos.medicion_cuff);
                            var fechaTraqueo = datos.fecha_instalacion;
                            if(fechaTraqueo != null){
                                $('#fechaTraqueo').data("DateTimePicker").date(new Date(fechaTraqueo));
                                var fechaTraqueosinFormato = datos.fecha_instalacion;
                                $("#calculoTraqueo").val(calculoDias(fechaTraqueosinFormato));
                            }else{
                                $('#fechaTraqueo').val('');
                                $("#fechaTraqueo").data("DateTimePicker").date(null);
                                $("#calculoTraqueo").val('');
                            }
                            $('#responsableTraqueo').val(datos.responsable_instalcion);
                            if(datos.fecha_curacion != null)
                            {
                                $('#fechaCambioTraqueo').data("DateTimePicker").date(new Date(datos.fecha_curacion));
                            }
                            $('#veppTraqueo').val(datos.observacion);
                        }

                        if(datos.tipo_cateter == 7){
                            $("#detalleOstomia").show();
                            enableOsto();
                            n_cateter.push(datos.tipo_cateter);
                            //cargar datos
                            $('#numeroIdOsto').val(datos.id);
                            $('#tipoOsto').val(datos.tipo);
                            var fechaOsto = datos.fecha_instalacion;
                            if(fechaOsto != null){
                                $('#fechaOsto').data("DateTimePicker").date(new Date(fechaOsto));
                            }else{
                                $('#fechaOsto').val('');
                                $("#fechaOsto").data("DateTimePicker").date(null);
                            }
                            $('#cuidadoOsto').val(datos.cuidado_enfermeria);
                            var fechaOstosinFormato = datos.fecha_curacion;
                            if(fechaOstosinFormato != null){
                                $('#fechaCambioOsto').data("DateTimePicker").date(new Date(fechaOstosinFormato));
                                var fechaTraqueosinFormato = datos.fecha_instalacion;
                                $("#calculoBolsaOsto").val(calculoDias(fechaOstosinFormato));
                            }else{
                                $('#fechaCambioOsto').val('');
                                $("#fechaCambioOsto").data("DateTimePicker").date(null);
                                $("#calculoBolsaOsto").val('');
                            }
                            $('#valoracionEstomaOsto').val(datos.valoracion_estomaypiel);
                            $('#cuidadoEstomaOsto').val(datos.responsable_curacion_ostomias);
                            $('#medicionEfluenteOsto').val(datos.medicion_efluente);
                            $('#caracteristicaOsto').val(datos.observacion);
                            $('#detalleEducacionOsto').val(datos.detalle_educacion);
                            $("input[type='radio'][name='baguetaOsto']").prop("checked", false);
                            if(datos.bagueta == false){
                                $('#baguetaOstoNo').prop("checked", true);
                            }else if(datos.bagueta == true){
                                $('#baguetaOstoSi').prop("checked", true);
                            }
                            $("#responsableOsto").val(datos.responsable_curacioin);
                        }

                        if(datos.tipo_cateter == 8){ console.log(datos);
                            $("#otro").show();
                            enableOtro();
                            n_cateter.push(datos.tipo_cateter);
                            $("#detalleOtro").val(datos.detalle);
                            $("#numeroOtro").val(datos.numero);
                            $("#numeroIdOtro").val(datos.id);
                            $("#tipoOtro").val(datos.tipo);
                            var fechaOtro = datos.fecha_instalacion;
                            if(fechaOtro != null){
                                $("#fechaOtro").data("DateTimePicker").date(new Date(fechaOtro));
                                $("#calculoOtro").val(calculoDias(fechaOtro));
                            }else{
                                $("#fechaOtro").val('');
                                $("#fechaOtro").data("DateTimePicker").date(null);
                            }
                            $("#lugarOtro").val(datos.lugar_instalacion);
                            $("#materialOtro").val(datos.material_fabricacion);
                            $("#viaOtro").val(datos.via_instalacion_otro);
                            $("#responsableOtro").val(datos.responsable_curacioin);
                        }

                    });

                    $('#cateteres').val(n_cateter);
                    $('#cateteres').selectpicker('refresh');

                    if(n_cateter >= 0){
                        $("#btGuardarOtro").prop("disabled", false);
                    }
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    IngresarMostrarOtros();


    function calculoDias(fecha){
      var fechaActual = Date.now();
      var fechaInicio = moment(fecha);
      var fechaFin    = fechaActual;
      var diff = fechaFin - fechaInicio;
      var resultado = Math.floor(diff/(1000*60*60*24));
      if(resultado < 0){
        resultado = 0;
      }else{
        resultado = resultado;
      }

      return resultado;
    }

    function detallesCateter(){
        var cateter = [];
        cateter = $("#cateteres").val(); console.log("desde detallesCateter: ", cateter);
        if(cateter == null){
            ocultarTodos();
            cateter = '';
        }         
       if(cateter.indexOf("0") != -1){
        enableBranula1();
       }else{
        disableBranula1();
        $('#fechaB1').val('');
        $("#fechaB1").data("DateTimePicker").date(null);
        $('#calculoB1').val('');
       }
       if(cateter.indexOf("1") != -1){
        enableBranula2();
       }else{
        disableBranula2();
        $('#fechaB2').val('');
        $("#fechaB2").data("DateTimePicker").date(null);
        $('#calculoB2').val('');
       }
       if(cateter.indexOf("2") != -1){
        enableFoley();
       }else{
        disableFoley();
        $('#fechaF').val('');
        $("#fechaF").data("DateTimePicker").date(null);
        $('#fechaCuracionF').val('');
        $("#fechaCuracionF").data("DateTimePicker").date(null);
        $('#calculoF').val('');
       }
       if(cateter.indexOf("3") != -1){
        enableSNG();
       }else{
        disableSNG();
        $('#fechaSng ').val('');
        $("#fechaSng").data("DateTimePicker").date(null);
        $('#calculoSng').val('');
       }
       if(cateter.indexOf("4") != -1){
        enableCVC();
       }else{
        disableCVC();
        $('#fechaCvc ').val('');
        $("#fechaCvc").data("DateTimePicker").date(null);
        $('#calculoCvc').val('');
       }
       if(cateter.indexOf("5") != -1){
        enableNASO();
       }else{
        disableNASO();
        $('#fechaNasoye ').val('');
        $("#fechaNasoye").data("DateTimePicker").date(null);
        $('#calculoNasoye').val('');
       }
       if(cateter.indexOf("6") != -1){
        enableTraq();
       }else{
        disableTraq();
        $('#fechaTraqueo ').val('');
        $("#fechaTraqueo").data("DateTimePicker").date(null);
        $('#fechaCambioTraqueo').val('');
        $("#fechaCambioTraqueo").data("DateTimePicker").date(null);
        $('#calculoTraqueo').val('');
       }
       if(cateter.indexOf("7") != -1){
        enableOsto();
       }else{
        disableOsto();
        $('#fechaCambioOsto ').val('');
        $("#fechaCambioOsto").data("DateTimePicker").date(null);
        $('#fechaOsto').val('');
        $("#fechaOsto").data("DateTimePicker").date(null);
        $('#calculoBolsaOsto').val('');
       }
       if(cateter.indexOf("8") != -1){  
        enableOtro();
       }else{
        disableOtro();
       }
        // (cateter.indexOf("0") !=-1)?enableBranula1():disableBranula1();
        // (cateter.indexOf("1") !=-1)?enableBranula2():disableBranula2();
        // (cateter.indexOf("2") !=-1)?enableFoley():disableFoley(); 
        // (cateter.indexOf("3") !=-1)?enableSNG():disableSNG(); 
        // (cateter.indexOf("4") !=-1)?enableCVC():disableCVC(); 
        // (cateter.indexOf("5") !=-1)?enableNASO():disableNASO(); 
        // (cateter.indexOf("6") !=-1)?enableTraq():disableTraq(); 
        // (cateter.indexOf("7") !=-1)?enableOsto():disableOsto();         

    }

    $(document).ready(function() {
        
        $("#btGuardarOtro").prop("disabled", true);
        $('.fecha-instalacion').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#IEOtros').bootstrapValidator('revalidateField', 'fecha[]');
        });
    
        $('.fecha-curacion').datetimepicker({
            format: "DD-MM-YYYY",
            locale: 'es'
        });
        $(".fecha-curacion").on("dp.change",function(){
        	$('#IEOtros').bootstrapValidator('revalidateField', $(this));
        });
        $('#fechaB1').on("dp.change",function(){
        	var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoB1").val(cantidad);
        });

        $('#fechaB2').on("dp.change",function(){
        	var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoB2").val(cantidad);
        });

        $('#fechaF').on("dp.change",function(){
        	var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoF").val(cantidad);
        });

        $('#fechaCuracionF').on("dp.change",function(){
            $('#IEOtros').bootstrapValidator('revalidateField', 'fecha[]');
        });
        
        $("#fechaSng").on("dp.change",function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoSng").val(cantidad);
        });
        $("#fechaCvc").on("dp.change",function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoCvc").val(cantidad);
        });

        $("#fechaNasoye").on("dp.change",function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoNasoye").val(cantidad);
        });
        $("#fechaTraqueo").on("dp.change",function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoTraqueo").val(cantidad);
        });

        $("#fechaCambioOsto").on("dp.change",function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
        	var cantidad = calculoDias(fecha);
        	$("#calculoBolsaOsto").val(cantidad);
        });

        $("#fechaOtro").on("dp.change", function(){
            var fecha = moment($(this).val(), "DD-MM-YYYY HH:mm").format('YYYY-MM-DD HH:mm:ss');
            var cantidad = calculoDias(fecha);
            $("#calculoOtro").val(cantidad);
        });

        $( "#iEnfermeria" ).click(function() {
            tabIE = $("#tabsIngresoEnfermeria div.active").attr("id");
        
            if(tabIE == "4h"){
                console.log("tabIE otros: ", tabIE);
                IngresarMostrarOtros();
            }
            
        });

        $("#hO").click(function() {
            IngresarMostrarOtros();
        });

        var caso = {{$caso}};
      /*  $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/obtenerIndicacionesMedicas",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (data) {
                var indicaciones = $("#indicaciones");
                var buscando = $("#spanBuscando");
                if(data.length > 0){
                    buscando.html("");
                    $(data).each(function(i, v){
                    indicaciones.append('<ul class="list-group"><li class="list-group-item list-group-item-info" style="list-style:none;">'+ v.indicacion +'</li></ul>');
                })
                }else{
                    buscando.html("");
                    indicaciones.append('<li class="list-group-item list-group-item-danger">El paciente no registra indicaciones médicas</li>');
                }
            },
            error: function (error) {
            }
        }); */

        $("#btGuardarOtro").click(function() {
            //validar campos al apretar el boton
            $('#IEOtros').bootstrapValidator('revalidateField', 'fecha[]');
        });

        $("#IEOtros").bootstrapValidator({
            excluded: [':disabled', ':hidden'],
            fields: {
                'fecha[]':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una fecha'
                        },
                    }
                },
                myClass: {
                    selector: '.fecha-curacion',
                    validators: {
                        notEmpty: {
                            message: 'Debe elegir una fecha'
                        }
                    }
                },
                'baguetaOsto':{
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        },
                    }
                },
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btGuardarOtro").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarIEOtros",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form.serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btGuardarOtro").prop("disabled", false);

                                if (data.exito) {
                                    swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                    });
                                    // location.reload();
                                    IngresarMostrarOtros();
                                    calculoDias();
                                }

                                if (data.error) {
                                    swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                    });
                                    // location.reload();
                                    IngresarMostrarOtros();
                                    calculoDias();
                                }
                            },
                            error: function(error){
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });
                                $("#btGuardarOtro").prop("disabled", false);
                                console.log(error);
                                // location.reload();
                                IngresarMostrarOtros();
                                calculoDias();
                            }
                        });
                    }else{
                        $("#btGuardarOtro").prop("disabled", false);
                    }
                }
            }); 
        });


        $( "#cateteres" ).change(function() {
            var cateter = [];
            cateter = $("#cateteres").val(); console.log("cateter: ", cateter);

            if(cateter == null){
                cateter = '';

            }else{
                $("#btGuardarOtro").prop("disabled", false);
            }

            detallesCateter();
        });

    });
</script>
{{--
<script type="text/javascript">
    function mostrarDetalleCateteres(){
        detalle = document.getElementById("detalleCateter");
        check = document.getElementById("check");
        if(check.checked){
            detalle.style.display='block';
        }
        else{
            detalle.style.display='none';
            $("#detalleOtroCateter").val('');
        }
    }
</script>
--}}
<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    .col-cateter{
        margin-left: 2%;
    }
    .col-cateter-1{
        margin-left: 1%;
    }
    .letraInvasivos{
      font-size: 78.8%;
    }

</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'IEOtros')) }}
{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}
<div class="formulario">
  {{--<input type="hidden" value="" name="id_formulario_ingreso_enfermeria" id="id_formulario_ingreso_enfermeria_otros">--}}
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
			@if($sub_categoria == 1)
			<h4>V. Otros</h4>
			@else
            <h4>IV. Otros</h4>
			@endif
        </div>
        <div class="panel-body" style="min-height: 300px;">

            <br>
            <div class="col-md-12">
              <div class="col-md-2">
                  {{Form::label('', "Cateteres:", array( ))}}
                  <div class="form-group">
                      {{ Form::select('cateteres[]', array('0' => 'Branula 1', '1' => 'Branula 2', '2'=>'S. Foley','3' => 'SNG','4' => 'CVC','5' => 'Sondas Nasoyeyunales','6' => 'Traqueotomía','7' => 'Ostomías','8' => 'Otro'), null, array('class' => 'form-control selectpicker', 'id' => 'cateteres', 'multiple')) }}
                  </div>
              </div>
            </div>
            <br>
            <div class="col-md-12"  style="display: none;" id="detalleBranula1">
              <legend>Branula 1</legend>
              {{Form::text('idcateter[]', null, array('id' => 'numeroIdB1', 'class' => 'form-control hidden'))}}
              {{Form::text('material[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('responsableCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::select('tipo[]', array('0'=>''), null, array('id' => 'tipoB1', 'class' => 'form-control hidden'))}}
              <div class="col-md-10">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('numero[]', null, array('id' => 'numeroB1', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fecha[]', null, array('id' => 'fechaB1', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('lugar[]', null, array('id' => 'lugarB1', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('responsableInst[]', null, array('id' => 'responsableB1', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('calculoB1', null, array('id' => 'calculoB1', 'class' => 'form-control', 'disabled'))}}
                    </div>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="display: none;" id="detalleBranula2">
              <legend>Branula 2</legend>
              {{Form::text('idcateter[]', null, array('id' => 'numeroIdB2', 'class' => 'form-control hidden'))}}
              {{Form::text('material[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('responsableCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::select('tipo[]', array('0'=>''), null, array('id' => 'tipoB2', 'class' => 'form-control hidden'))}}
              <div class="col-md-10">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('numero[]', null, array('id' => 'numeroB2', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fecha[]', null, array('id' => 'fechaB2', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('lugar[]', null, array('id' => 'lugarB2', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('responsableInst[]', null, array('id' => 'responsableB2', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('calculoB2', null, array('id' => 'calculoB2', 'class' => 'form-control', 'disabled'))}}
                    </div>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="display: none;" id="detalleFoley">
              <legend>S. Foley</legend>
              {{Form::text('idcateter[]', null, array('id' => 'numeroIdF', 'class' => 'form-control hidden'))}}
              {{Form::text('responsableInst[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::select('tipo[]', array('0'=>''), null, array('id' => 'tipoF', 'class' => 'form-control hidden'))}}
              <div class="col-md-11">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('numero[]', null, array('id' => 'numeroF', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fecha[]', null, array('id' => 'fechaF', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('lugar[]', null, array('id' => 'lugarF', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Material de fabricación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('material[]', null, array('id' => 'materialF', 'class' => 'form-control'))}}
                    </div>
                </div>

                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Mantención", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fechaCura[]', null, array('id' => 'fechaCuracionF', 'class' => 'form-control fecha-curacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>

              </div>
              <div class="col-md-10">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Curación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('responsableCura[]', null, array('id' => 'responsableF', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-5 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Observación", array('class' => 'letraInvasivos' ))}}
                        {{Form::textarea('observacion[]', null, array('id' => 'observacionF', 'class' => 'form-control', 'rows'=>'3'))}}
                    </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('calculoF', null, array('id' => 'calculoF', 'class' => 'form-control', 'disabled'))}}
                    </div>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="display: none;" id="detalleSNG">
                <legend>SNG</legend>
                {{Form::text('idcateter[]', null, array('id' => 'numeroIdSng', 'class' => 'form-control hidden'))}}
                {{Form::text('responsableInst[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::select('tipo[]', array('0'=>''), null, array('id' => 'tipoSng', 'class' => 'form-control hidden'))}}
                <div class="col-md-11">
                  <div class="col-md-1">
                      <div class="form-group">
                          {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('numero[]', null, array('id' => 'numeroSng', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('fecha[]', null, array('id' => 'fechaSng', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('lugar[]', null, array('id' => 'lugarSng', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('material[]', null, array('id' => 'materialSng', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('responsableCura[]', null, array('id' => 'responsableSng', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('calculoSng', null, array('id' => 'calculoSng', 'class' => 'form-control', 'disabled'))}}
                      </div>
                  </div>
                </div>
            </div>
            <div class="col-md-12" style="display: none;" id="detalleCVC">
                <legend>CVC</legend>
                {{Form::text('idcateter[]', null, array('id' => 'numeroIdCvc', 'class' => 'form-control hidden'))}}
                {{Form::text('responsableInst[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
                <div class="col-md-10">
                  <div class="col-md-1">
                      <div class="form-group">
                          {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('numero[]', null, array('id' => 'numeroCvc', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Tipo", array('class' => 'letraInvasivos' ))}}
                          {{Form::select('tipo[]', array('yugular' => 'Yugular', 'subclavio' => 'Subclavio', 'femoral' => 'Femoral' ), null, array('id' => 'tipoCvc', 'class' => 'form-control','placeholder' => 'Seleccione','required' => 'required'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('fecha[]', null, array('id' => 'fechaCvc', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('lugar[]', null, array('id' => 'lugarCvc', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('material[]', null, array('id' => 'materialCvc', 'class' => 'form-control'))}}
                      </div>
                  </div>
                </div>
                <div class="col-md-10">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Vía de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('viaCvc', null, array('id' => 'viaCvc', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('responsableCura[]', null, array('id' => 'responsableCvc', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('calculoCvc', null, array('id' => 'calculoCvc', 'class' => 'form-control', 'disabled'))}}
                      </div>
                  </div>
                </div>
            </div>
            <div class="col-md-12" style="display: none;" id="nasoyeyunales">
              <legend>Sondas Nasoyeyunales</legend>
              {{Form::text('idcateter[]', null, array('id' => 'numeroIdNasoye', 'class' => 'form-control hidden'))}}
              {{Form::text('material[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('lugar[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('responsableCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::select('tipo[]', array('0'=>''),null, array('id' => 'tipoNasoye', 'class' => 'form-control hidden'))}}
              <div class="col-md-10">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('numero[]', null, array('id' => 'numeroNasoye', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fecha[]', null, array('id' => 'fechaNasoye', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>
                <div class="col-md-3 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('responsableInst[]', null, array('id' => 'responsableNasoye', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-3 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('calculoNasoye', null, array('id' => 'calculoNasoye', 'class' => 'form-control', 'disabled'))}}
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" style="display: none;" id="traqueotomia">
              <legend>Traqueotomía</legend>
              {{Form::text('idcateter[]', null, array('id' => 'numeroIdTraqueo', 'class' => 'form-control hidden'))}}
              {{Form::text('material[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('numero[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('lugar[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::text('responsableCura[]', null, array( 'class' => 'form-control hidden'))}}
              {{Form::select('tipo[]', array('0'=>''), null, array('id' => 'tipoTraqueo', 'class' => 'form-control hidden'))}}
              <div class="col-md-11">
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('fecha[]', null, array('id' => 'fechaTraqueo', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                    </div>
                </div>
                <div class="col-cateter-1 col-md-2">
                  <div class="form-group">
                      {{Form::label('', "Responsable de Instalación", array('class' => 'letraInvasivos' ))}}
                      {{Form::text('responsableInst[]', null, array('id' => 'responsableTraqueo', 'class' => 'form-control'))}}
                  </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                  <div class="form-group">
                      {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                      {{Form::text('calculoTraqueo', null, array('id' => 'calculoTraqueo', 'class' => 'form-control','disabled'))}}
                  </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                  <div class="form-group">
                      {{Form::label('', "Fecha de cambio de filtro", array('class' => 'letraInvasivos' ))}}
                      {{Form::text('fechaCura[]', null, array('id' => 'fechaCambioTraqueo', 'class' => 'form-control fecha-curacion', 'autocomplete'=>'off'))}}
                  </div>
                </div>
                <div class="col-md-2 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Medición CUFF", array('class' => 'letraInvasivos' ))}}
                        {{Form::text('cuffTraqueo', null, array('id' => 'cuffTraqueo', 'class' => 'form-control'))}}
                    </div>
                </div>
              </div>
              <div class="col-md-10">
                <div class="col-md-5">
                    <div class="form-group">
                        {{Form::label('', "Valoración de estoma y piel de periostomal ", array('class' => 'letraInvasivos' ))}}
                        {{Form::textarea('observacion[]', null, array('id' => 'veppTraqueo', 'class' => 'form-control', 'rows'=>'3'))}}
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" style="display: none;"  id="detalleOstomia" hidden>
                <legend>Ostomías</legend>
                {{Form::text('idcateter[]', null, array('id' => 'numeroIdOsto', 'class' => 'form-control hidden'))}}
                {{Form::text('material[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('numero[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('lugar[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('responsableInst[]', null, array( 'class' => 'form-control hidden'))}}
                <div class="col-md-11">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Tipo", array('class' => 'letraInvasivos' ))}}
                          {{ Form::select('tipo[]', array('Colonoscopia' => 'Colonoscopia', 'Ileostomia' => 'Ileostomía', 'Yeyuno'=>'Yeyuno'), null, array('class' => 'form-control', 'id' => 'tipoOsto','placeholder' => 'Seleccione','required' => 'required')) }}
                          {{--Form::text('tipoOsto', null, array('id' => 'tipoOsto', 'class' => 'form-control'))--}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('fecha[]', null, array('id' => 'fechaOsto', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Cuidados de Enfermería", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('cuidadoOsto', null, array('id' => 'cuidadoOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha cambio de bolsa", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('fechaCura[]', null, array('id' => 'fechaCambioOsto', 'class' => 'form-control fecha-curacion'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable (cuidados Enfermeria y cambio bolsa)", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('responsableCura[]', null, array('id' => 'responsableOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>

                </div>
                <div class="col-md-11">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Cálculo días cambio de bolsa", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('calculoBolsaOsto', null, array('id' => 'calculoBolsaOsto', 'class' => 'form-control', 'disabled'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Valoración de estoma y piel periostomal", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('valoracionEstomaOsto', null, array('id' => 'valoracionEstomaOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-4 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable (Valoración de estoma y piel periostomal)", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('cuidadoEstomaOsto', null, array('id' => 'cuidadoEstomaOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Medición efluente en cc", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('medicionEfluenteOsto', null, array('id' => 'medicionEfluenteOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>
                </div>
                <div class="col-md-11">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Características", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('observacion[]', null, array('id' => 'caracteristicaOsto', 'class' => 'form-control'))}}
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                        <div class="form-group">
                            {{Form::label('', "Detalle de la educación al paciente", array('class' => 'letraInvasivos' ))}}
                            {{Form::text('detalleEducacionOsto', null, array('id' => 'detalleEducacionOsto', 'class' => 'form-control'))}}
                        </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                        <div class="form-group">
                            {{Form::label('', "Bagueta", array('class' => 'letraInvasivos' ))}}
                            <div class="input-group">
                            <label class="radio-inline">{{Form::radio('baguetaOsto', "no", false, array('id'=>'baguetaOstoNo', 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('baguetaOsto', "si", false, array('id'=>'baguetaOstoSi', 'required' => true))}}Sí</label>
                            </div>
                        </div>
                  </div>
                </div>
            </div>
            <br>
            <div class="col-md-12" style="display: none;" id="otro">
                <legend>Otro</legend>
                {{Form::text('idcateter[]', null, array('id' => 'numeroIdOtro', 'class' => 'form-control hidden'))}}
                {{Form::text('responsableInst[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('fechaCura[]', null, array( 'class' => 'form-control hidden'))}}
                {{Form::text('observacion[]', null, array( 'class' => 'form-control hidden'))}}
                <div class="col-md-11">
                    <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', 'Detalle', ['class' => 'letraInvasivos'])}}
                            {{Form::text('detalle', null, ['id' => 'detalleOtro', 'class' => 'form-control'])}}
                        </div>
                    </div>
                    <div class="col-md-1 col-cateter-1">
                        <div class="form-group">
                            {{Form::label('', "Número", array('class' => 'letraInvasivos' ))}}
                            {{Form::text('numero[]', null, array('id' => 'numeroOtro', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                        {{Form::label('', "Tipo", array('class' => 'letraInvasivos' ))}}
                        {{Form::select('tipo[]', array('yugular' => 'Yugular', 'subclavio' => 'Subclavio', 'femoral' => 'Femoral' ), null, array('id' => 'tipoOtro', 'class' => 'form-control','placeholder' => 'Seleccione','required' => 'required'))}}
                      </div>
                    </div>
                    <div class="col-cateter col-md-2">
                        <div class="form-group">
                            {{Form::label('', "Fecha de Instalación", array('class' => 'letraInvasivos' ))}}
                            {{Form::text('fecha[]', null, array('id' => 'fechaOtro', 'class' => 'form-control fecha-instalacion', 'autocomplete'=>'off'))}}
                        </div>
                    </div>
                    <div class="col-cateter col-md-2">
                        <div class="form-group">
                            {{Form::label('', "Lugar de Instalación", array('class' => 'letraInvasivos' ))}}
                            {{Form::text('lugar[]', null, array('id' => 'lugarOtro', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('material[]', null, array('id' => 'materialOtro', 'class' => 'form-control'))}}
                      </div>
                    </div>
                </div>
                <div class="col-md-11">
                    <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Vía de Instalación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('viaOtro', null, array('id' => 'viaOtro', 'class' => 'form-control'))}}
                      </div>
                    </div>
                    <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('responsableCura[]', null, array('id' => 'responsableOtro', 'class' => 'form-control'))}}
                      </div>
                    </div>
                    <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array('class' => 'letraInvasivos' ))}}
                          {{Form::text('calculoOtro', null, array('id' => 'calculoOtro', 'class' => 'form-control', 'disabled'))}}
                      </div>
                    </div>
                </div>
            </div>
            {{--
            <div class="row">
                <div id="detalleCateter" style="display: none;" class="col-sm-12">
                    <label for="detalleOtroCateter" class="control-label" title="pertenencias">Detalle otro cateter: </label>
                    {{Form::textArea('detalleOtroCateter', null, array('id' => 'detalleOtroCateter', 'class' => 'form-control', 'rows' => 2))}}
                </div>

                <br>
                <div class="col-sm-12">
                    <legend>Indicaciones del médico</legend>--}}
                    {{--<h4><span id="spanBuscando">Buscando...</span></h4>
                    <div id="indicaciones"></div>
                    <label for="Pertenencias" class="control-label" title="pertenencias">Diagnostico de enfermeria: </label>
                      --}}
                  {{--  {{Form::textArea('indicacionMedico', null, array('id' => 'indicacionMedico', 'class' => 'form-control', 'rows' => 2))}}
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-sm-12">
                    <label for="Examenes" class="control-label" title="pertenencias">Examenes: </label>
                    {{Form::textArea('examenes', null, array('id' => 'examenes', 'class' => 'form-control', 'rows' => 5))}}
                </div>
            </div>--}}
            <br><br>
            <div class="col-md-12">
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary" id="btGuardarOtro">Guardar</button>
              </div>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
