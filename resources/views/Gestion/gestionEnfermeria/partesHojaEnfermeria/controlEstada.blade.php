<script>

    function generarTablaAntibioticos(){
        tablaAntibioticosActual = $("#tablaAntibioticos").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerControlEstadaAntibioticos/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function generarTablaOperaciones(){
        tablaOperacionesActual = $("#tablaOperaciones").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerControlEstadaOperaciones/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },

        });
    }

    function generarTablaProcedimientos(){
        tablaProcedimientosActual = $("#tablaProcedimientos").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerControlEstadaProcedimientos/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function generarTablaOtrosProcedimientos(){
        tablaOtrosProcedimientosActual = $("#tablaOtrosProcedimientos").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerControlEstadaOtros/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function eliminarAntibiotico(idSolicitud) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de eliminar este antibiótico?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarAntibiotico",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnAgregarAntibiotico").prop("disabled", false);

                            if (data.exito) {
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                tablaAntibioticosActual.api().ajax.reload();
                                tablaOperacionesActual.api().ajax.reload();
                                tablaProcedimientosActual.api().ajax.reload();
                                tablaOtrosProcedimientosActual.api().ajax.reload();
                                // $("#btnAgregarAntibiotico").prop("disabled", true);
                            }

                            if (data.error) {
                              swalError.fire({
                            title: 'Error',
                            text:data.error
                            }).then(function(result) {
                            if (result.isDenied) {
                            location . reload();

                            }
                            });
                            }
                        },
                        error: function(error){
                            $("#btnAgregarAntibiotico").prop("disabled", false);
                            console.log(error);
                        }
                    });
                }else{
                    tablaAntibioticosActual.api().ajax.reload();
                    tablaOperacionesActual.api().ajax.reload();
                    tablaProcedimientosActual.api().ajax.reload();
                    tablaOtrosProcedimientosActual.api().ajax.reload();
                }
            }
        }); 
    }

    function finalizarAntibiotico(idSolicitud) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de finalizar este antibiótico?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/finalizarAntibiotico",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnAgregarAntibiotico").prop("disabled", false);

                            if (data.exito) {
                               swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tablaAntibioticosActual.api().ajax.reload();
                                tablaOperacionesActual.api().ajax.reload();
                                tablaProcedimientosActual.api().ajax.reload();
                                tablaOtrosProcedimientosActual.api().ajax.reload();
                            }

                            if (data.error) {
                              swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    location . reload();

                                }
                                });
                            }
                        },
                        error: function(error){
                            $("#btnAgregarAntibiotico").prop("disabled", false);
                            console.log(error);
                        }
                    });
                }else{
                    tablaAntibioticosActual.api().ajax.reload();
                    tablaOperacionesActual.api().ajax.reload();
                    tablaProcedimientosActual.api().ajax.reload();
                }
            }
        }); 
    }

    function imprimirErroresControEstada (msg) {
        $(".imprimir-mensajes-control-estada").find("ul").html('');
        $(".imprimir-mensajes-control-estada").css('display','block');
        $.each( msg, function( key, value ) {
            $(".imprimir-mensajes-control-estada").find("ul").append("<div style='display: flex'><i class='glyphicon glyphicon-remove' style='color: #a94442;'></i><div style='margin-left: 10px'><h4>"+value+"</h4></div></div>");
            // ('<label><i class="glyphicon glyphicon-remove"><h4>'+value+'</h4></i></label><br>');
        });
    }

    function activarValidacionesObligatorias(position){
        //permitir validaciones obligatorias
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'tipo[]', true);
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'tipo[]');
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'fechaColocacion[]', true);
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'fechaColocacion[]');
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'comentario[]');
    }

    function activarValidacionesTipo1(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'antibiotico[]', true); //1
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'antibiotico[]'); //1
    }

    function desactivarValidacionesTipo1(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'antibiotico[]', false); //1
    }

    function activarValidacionesTipo2(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'operacion[]', true); //2
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'operacion[]'); //2
    }

    function desactivarValidacionesTipo2(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'operacion[]', false); //2
    }

    function activarValidacionesTipo3(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'tipoProcedimiento[]', true); //3
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'tipoProcedimiento[]'); //3
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'numero[]', true); //3
        $('#HEControlEstada').bootstrapValidator('revalidateField', 'numero[]'); //3
    }

    function desactivarValidacionesTipo3(){
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'tipoProcedimiento[]', false); //3
        $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'numero[]', false); //3
    }
    
    function cargarVistaControlEstada(){
        obtenerDiasEstada();
        if (typeof tablaAntibioticosActual !== 'undefined') {
            tablaAntibioticosActual.api().ajax.reload();
        }else{
            generarTablaAntibioticos();
        }

        if (typeof tablaOperacionesActual !== 'undefined') {
            tablaOperacionesActual.api().ajax.reload();
        }else{
            generarTablaOperaciones();
        }

        if (typeof tablaProcedimientosActual !== 'undefined') {
            tablaProcedimientosActual.api().ajax.reload();
        }else{
            generarTablaProcedimientos();
        }
        if (typeof tablaOtrosProcedimientosActual !== 'undefined') {
            tablaOtrosProcedimientosActual.api().ajax.reload();
        }else{
            generarTablaOtrosProcedimientos();
        }
    }

    function obtenerDiasEstada(){
        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/obtenerDiasEstada",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (data) {

                var estada = $("#diasEstada");
                estada.html("");
                var buscandoEstada = $("#spanBuscandoEstada");
                if(data){
                    buscandoEstada.html("");
                    estada.append("<div class='col-md-2'>N° días Estada: </div><div class='col-md-2'><div class='col-md-6'> <div class='form-group'><input class='form-control col-md-2' name='estada' type='text' value='"+data+"' disabled></div></div></div>");
                }else{
                    buscandoEstada.html("");
                    // estada.append('<li class="list-group-item list-group-item-danger">El paciente no registra indicaciones médicas</li>');
                }
            },
            error: function (error) {
            }
        });
    }

    function eliminarFilaAntibiotico(position) {
            var myobj = document.getElementById("moduloControlEstada"+position);
            myobj.remove();

            activarValidacionesTipo1();
            activarValidacionesTipo2();
            activarValidacionesTipo3();

            activarValidacionesObligatorias();
        }

        function tipoProcedimientoOnChange(position){
            //obtiene el atributo  de la columna que se quiere modificar
            var posicion = position.getAttribute('data-id');
            var valor = position.value;

            if(valor == '1'){
                $(".mostrarOperacion"+posicion).hide();
                $(".mostrarTipoProcedimiento"+posicion).hide();
                $(".mostrarNumero"+posicion).hide();
                $(".mostrarAntibiotico"+posicion).show();
                //omitir validaciones tipo 2 y 3
                activarValidacionesTipo1();
                // desactivarValidacionesTipo2();
                // desactivarValidacionesTipo3();
            }else if(valor == '2'){
                $(".mostrarAntibiotico"+posicion).hide();
                $(".mostrarTipoProcedimiento"+posicion).hide();
                $(".mostrarNumero"+posicion).hide();
                $(".mostrarOperacion"+posicion).show();
                //omitir validaciones tipo 1 y 3
                activarValidacionesTipo2();
                // desactivarValidacionesTipo1();
                // desactivarValidacionesTipo3();
            }else if(valor == '3'){
                $(".mostrarOperacion"+posicion).hide();
                $(".mostrarAntibiotico"+posicion).hide();
                $(".mostrarTipoProcedimiento"+posicion).show();
                // $(".mostrarNumero"+posicion).show();
                $(".tipoP"+posicion).change();
                //omitir validaciones tipo 1 y 2
                activarValidacionesTipo3();
                // desactivarValidacionesTipo1();
                // desactivarValidacionesTipo2();
            }else if(valor == '4'){
                $(".mostrarOperacion"+posicion).hide();
                $(".mostrarTipoProcedimiento"+posicion).hide();
                $(".mostrarNumero"+posicion).hide();
                $(".mostrarAntibiotico"+posicion).hide();
                //omitir validaciones tipo 2 y 3
                activarValidacionesTipo1();
            }
            activarValidacionesObligatorias();
        }

        function mostrarNumeroOnChange(position){
            var posicion = position.getAttribute('data-id');
       
            var valor = position.value;

            if(valor == 1 || valor == 2 || valor == 3 || valor == 4){
                $(".mostrarNumero"+posicion).show();
                $.ajax({
                url: "{{ URL::to('/gestionEnfermeria')}}/llenarSelectTipoProcedecimiento",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  {
                    tipoP: valor
                },
                dataType: "json",
                type: "post",
                success: function(data){
                    $("#valorNumero"+posicion+" option").remove();
                    $.each( data, function(k, v) {
                        $("#valorNumero"+posicion).append($("<option>", {value:v, text:v}));
                    });
                    $("#numero_hidden"+posicion).val(data[0]);
                    $('#HEControlEstada').bootstrapValidator('revalidateField', 'numero[]'); //
                },
                error: function(error){
                    console.log(error.responseText);
                }
            });
            }else{
                $('#HEControlEstada').bootstrapValidator('enableFieldValidators', 'numero[]', false); //3
               
                
                $("#valorNumero"+posicion+" option").remove();
                $("#numero_hidden"+posicion).val('');
                $(".mostrarNumero"+posicion).hide();
            }
        }

        function numeroOnChange(position){
            var posicion = position.getAttribute('data-id');
            var valor = position.value;
            $("#numero_hidden"+posicion).val(valor);
        }
     
        function limpiarArrays(cantidad){
            for(var i = 0; i == cantidad; i){
                console.log(i);
            }
        }


    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "6b"){
                console.log("tabRdc control estada: ", tabRdc);
                cargarVistaControlEstada();
            }

        });

        $( "#6ab" ).click(function() {
            cargarVistaControlEstada();
        });

        // $("#btnAgregarAntibiotico").prop("disabled", true);
        $("#btnAgregarAntibiotico").prop("disabled", false);

        $("#HEControlEstada").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'tipo[]': {
                    validators:{
                        remote:{
                            data: function(validator){
                                return {
                                    tipo: validator.getFieldElements('tipo[]').val()
                                };
                            },
                            url: "{{ URL::to("/validarTipoControlEstada") }}"
                        }
                    }
                },  
                'fechaColocacion[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar la fecha en que fue colocado el antibiótico'
                        }
                    }
                },
                'antibiotico[]': {
                    validators:{
                        remote:{
							data: function(validator){
								return {
									antibiotico: validator.getFieldElements('antibiotico[]').val()
								};
							},
							url: "{{ URL::to("/validarSelectAntibioticos") }}"
						}
                    }
                },
                'operacion[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el nombre de la operación'
                        }
                    }
                },
                'tipoProcedimiento[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el tipo de procedimiento'
                        },
                        remote:{
							data: function(validator){
								return {
                                    tipoProcedimiento: validator.getFieldElements('tipoProcedimiento[]').val()
								};
							},
							url: "{{ URL::to("/validarSelectTipoProcedimiento") }}"
						}
                    }
                },
                'numero[]': {
                    validators:{
                        callback: {
                            message: 'Debe seleccionar un número correcto',
                            callback: function (value, validator, $field) {
                                // obtiene la posicion de la cual se esta consultando
                                position = $field.attr('id').replace(/[^0-9]/gi, '');
                                $.ajax({
                                    url:"{{ URL::to("/validarSelectNumeroProcedimiento") }}",
                                    data:{ tipoProcedimiento: $("#tipoProcedimiento"+position).val(), numero: value },
                                    dataType: "json",
                                    type: "get",
                                success: function(data){
                                       if(data.valid == false){
                                        return {
                                            valid: false,
                                            message: data.message
                                        };
                                       }
                                    },
                                error: function(error){
                                    console.log(error);
                                }
                                });

                                return true;
                            }
                        }
                    }
                },
                'comentario[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar un comentario'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            // data.bv.disableSubmitButtons(true);
            $("#btnAgregarAntibiotico").prop("disabled", false);
        }).on("success.form.bv", function(evt, data){
            // $("#btnAgregarAntibiotico").prop("disabled", true);
            $("#btnAgregarAntibiotico").prop("disabled", false);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarAntibiotico",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnAgregarAntibiotico").prop("disabled", false);

                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    $("#HEControlEstada").trigger("reset");
                                    $(".selectTipo").val(1);
                                    $(".selectTipo").change();
                                    $('.dPControl').val('');
                                    $(".dPControl").data("DateTimePicker").date(null);
                                    activarValidacionesObligatorias();
                                    tablaAntibioticosActual.api().ajax.reload();
                                    tablaOperacionesActual.api().ajax.reload();
                                    tablaProcedimientosActual.api().ajax.reload();
                                    tablaOtrosProcedimientosActual.api().ajax.reload();
                                    $("#btnAgregarAntibiotico").prop("disabled", true);

                                    $( "#moduloControlEstadacopia" ).empty();
                                    counter = 1;                                
                                }

                                if (data.error) {
                                   swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                }

                                //se dividieron los errores en 3 tipos en base a las opciones del select, asi saber que validaciones pedir u omitir
                                if(data.errorTipo1) {
                                    // //permitir validaciones obligatorias
                                    // activarValidacionesObligatorias();
                                    // //permitir validaciones tipo 1
                                    // activarValidacionesTipo1();
                                    // //omitir tipo 2 y 3
                                    // desactivarValidacionesTipo2();
                                    // desactivarValidacionesTipo3();
                                    
                                    imprimirErroresControEstada(data.errorTipo1);
                                    $("#erroresModalControlEstada").modal("show");
                                }

                                if(data.errorTipo2) {
                                    //permitir validaciones obligatorias
                                    // activarValidacionesObligatorias();
                                    // //permitir validaciones tipo 2
                                    // activarValidacionesTipo2();
                                    // //omitir tipo 1 y 3
                                    // desactivarValidacionesTipo1();
                                    // desactivarValidacionesTipo3();
                                    imprimirErroresControEstada(data.errorTipo2);
                                    $("#erroresModalControlEstada").modal("show");
                                }

                                if(data.errorTipo3) {
                                    // //permitir validaciones obligatorias
                                    // activarValidacionesObligatorias();
                                    // //permitir validaciones tipo 3
                                    // activarValidacionesTipo3();
                                    // //omitir tipo 1 y 2
                                    // desactivarValidacionesTipo1();
                                    // desactivarValidacionesTipo2();
                                    
                                    imprimirErroresControEstada(data.errorTipo3);
                                    $("#erroresModalControlEstada").modal("show");
                                }

                                if(data.errores) {
                                    // activarValidacionesObligatorias();
                                    imprimirErroresControEstada(data.errores);
                                    $("#erroresModalControlEstada").modal("show");
                                }

                            },
                            error: function(error){
                                $("#btnAgregarAntibiotico").prop("disabled", false);
                                console.log(error);
                            }
                        });
                    }
                }            
            });
            $("#btnAgregarAntibiotico").prop("disabled", false);  
        });

        // desactivarValidacionesTipo2();
        // desactivarValidacionesTipo3();


        $('#fechaColocacion0').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#HEControlEstada').bootstrapValidator('revalidateField', $(this));
        });
      
     
    var counter = 1;
    
    $(".agregarAntibiotico").click(function(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloControlEstada");
        var cloneDiv = originalDiv.clone();    
        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloControlEstada'+counter);
        cloneDiv.find(".mostrarAntibiotico0").removeClass("mostrarAntibiotico0").addClass("mostrarAntibiotico"+counter);
        cloneDiv.find(".mostrarOperacion0").removeClass("mostrarOperacion0").addClass("mostrarOperacion"+counter);
        cloneDiv.find(".mostrarNumero0").removeClass("mostrarNumero0").addClass("mostrarNumero"+counter);
        cloneDiv.find(".mostrarTipoProcedimiento0").removeClass("mostrarTipoProcedimiento0").addClass("mostrarTipoProcedimiento"+counter);

        cloneDiv.find(".mostrarOperacion"+counter).css('display','none');
        cloneDiv.find(".mostrarTipoProcedimiento"+counter).css('display','none');
        cloneDiv.find(".mostrarNumero"+counter).css('display','none');
        cloneDiv.find(".mostrarAntibiotico"+counter).css('display','block');


        $("[name='tipo[]']",cloneDiv).attr({'data-id':counter,'id':'tipo'+counter});
        $("[name='antibiotico[]']",cloneDiv).attr({'data-id':counter,'id':'antibiotico'+counter});
        $("[name='operacion[]']",cloneDiv).attr({'data-id':counter,'id':'operacion'+counter});          
        $("[name='operacion[]']",cloneDiv).val('');          
        $("[name='tipoProcedimiento[]']",cloneDiv).attr({'data-id':counter,'id':'tipoProcedimiento'+counter});          
        $("[name='numero[]']",cloneDiv).attr({'data-id':counter,'id':'valorNumero'+counter});          
        $("[name='numero_hidden[]']",cloneDiv).attr({'data-id':counter,'id':'numero_hidden'+counter});          
        $("[name='numero_hidden[]']",cloneDiv).val('');          
        $("[name='fechaColocacion[]']",cloneDiv).attr({'data-id':counter,'id':'fechaColocacion'+counter});          
        $("[name='fechaColocacion[]']",cloneDiv).val('');          
        $("[name='comentario[]']",cloneDiv).attr({'data-id':counter,'id':'comentario'+counter}).val(counter);
        $("[name='comentario[]']",cloneDiv).val('');
        html ='<div class="col-md-1"><button class="btn btn-danger" onclick="eliminarFilaAntibiotico('+counter+')">-</button></div>';          
       
        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloControlEstadacopia").append(cloneDiv);
        cloneDiv.append(html);
      
        $('#fechaColocacion'+counter).datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#HEControlEstada').bootstrapValidator('revalidateField', $(this));
        });
        
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='tipo[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='antibiotico[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='operacion[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='tipoProcedimiento[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='numero[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='fechaColocacion[]']"));
        $('#HEControlEstada').bootstrapValidator('addField', cloneDiv.find("[name='comentario[]']"));


        
        
        //incrementa el contador
        counter++;      
	});
    
 

    });


</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

</style>




<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CONTROL DÍAS ESTADA Y OTROS</h4>
            </div>

            <div class="panel-body">
                <legend>Ingresar</legend>
                <div class="col-md-12">
                    <div class="col-md-4"> TIPO</div>
                    {{-- <div class="col-md-2"> ESTADO</div> --}}
                </div>

                <br>

                {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEControlEstada')) }}
                {{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
                {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormControlEstada') )}}
                <div class="controlEstada">
                    <div class="col-md-10 moduloControlEstada" id="moduloControlEstada">
                        <div class="col-md-3"> <div class="form-group"> {{Form::select('tipo[]', array('1' => 'Antibiótico','2' => 'Procedimiento quirúrgico','3' => 'Procedimiento invasivo','4' => 'Otro'), null, array('class' => 'form-control selectTipo', 'id' => 'tipo0','onchange'=>'tipoProcedimientoOnChange(this)','data-id'=>'0'))}} </div> </div>
                        <div class="col-md-3 mostrarAntibiotico0"> <div class="form-group"> {{Form::select('antibiotico[]', App\Models\CaracteristicasAgente::pluck('nombre','id'), null, array( 'class' => 'form-control anti','id' => 'antibiotico0'/*, 'placeholder' => 'Seleccione'*/)) }} </div> </div>
                        <div class="col-md-3 mostrarOperacion0" hidden> <div class="form-group"> {{Form::text('operacion[]', null, array( 'class' => 'form-control oper', 'placeholder' => 'Ingrese descripción','id'=>'operacion0')) }} </div> </div>
                        <div class="col-md-3 mostrarTipoProcedimiento0" hidden> <div class="form-group"> {{Form::select('tipoProcedimiento[]', array('0' => 'Seleccionar','1' => 'SNG', '2' => 'CUP' , '3' => 'SNY' , '4' => 'VVP','5' => 'Catéter Venoso Central', '6' => 'Catéter arterial'), null, array( 'class' => 'form-control tipoP','id'=>'tipoProcedimiento0','onchange'=>'mostrarNumeroOnChange(this)','data-id'=>'0')) }} </div> </div>
                        <div class="col-md-1 mostrarNumero0" hidden> <div class="form-group">
                            {{Form::hidden('numero_hidden[]', null, array('id'=>'numero_hidden0'))}}
                            {{Form::select('numero[]', array(), null, array( 'class' => 'form-control nume','id' => 'valorNumero0','onchange'=>'numeroOnChange(this)','data-id'=>'0')) }} </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{Form::text('fechaColocacion[]', null, array( 'class' => 'dPControl form-control', 'placeholder' => 'Seleccione fecha','id'=>'fechaColocacion0', 'autocomplete' => 'off'))}} </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{Form::text('comentario[]', null, array( 'class' => 'comentario form-control', 'placeholder' => 'Ingrese comentario','id'=>'comentario0', 'maxlength' => 504))}} </div> </div>
                       
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary agregarAntibiotico" >+</button>
                        <button type="submit" class="btn btn-primary" id="btnAgregarAntibiotico">Guardar</button>
                    </div>
                    <div class="col-md-12 moduloControlEstadacopia pl-0 pr-0" id="moduloControlEstadacopia"></div>
                </div>
                {{ Form::close() }} 
                <br>
            </div>
            <div class="panel-body">
                <legend>Control de estada</legend>
                <h4><span id="spanBuscandoEstada">Buscando...</span></h4>
                    <div id="diasEstada"></div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>Antibioticos</h4>
            </div>

            <div class="panel-body">
                <table id="tablaAntibioticos" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 20% !important;">TIPO CONTROL</th>
                            <th style="width: 20% !important;">FECHA ASIGNACIÓN</th>
                            <th style="width: 10% !important;">DÍAS</th>
                            <th style="width: 10% !important;">ESTADO</th>
                            <th style="width: 10% !important;">USUARIO</th>
                            <th style="width: 20% !important;">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>Procedimientos quirúrgicos</h4>
            </div>

            <div class="panel-body">
                <table id="tablaOperaciones" class="table table-striped table-bordered table-hover" width="320px" style="width: 0px;">
                    <thead>
                        <tr>
                            <th style="width: 20% !important;">TIPO CONTROL</th>
                            <th style="width: 20% !important;">FECHA ASIGNACIÓN</th>
                            <th style="width: 10% !important;">DÍAS</th>
                            <th style="width: 10% !important;">ESTADO</th>
                            <th style="width: 10% !important;">USUARIO</th>
                            <th style="width: 20% !important;">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>Procedimientos Invasivos</h4>
            </div>

            <div class="panel-body">
                <table id="tablaProcedimientos" class="table table-striped table-bordered table-hover" width="100%" style="width: 0px;">
                    <thead>
                        <tr>
                            <th style="width: 20% !important;">TIPO CONTROL</th>
                            <th style="width: 20% !important;">FECHA ASIGNACIÓN</th>
                            <th style="width: 10% !important;">DÍAS</th>
                            <th style="width: 10% !important;">ESTADO</th>
                            <th style="width: 10% !important;">USUARIO</th>
                            <th style="width: 20% !important;">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>Otros</h4>
            </div>

            <div class="panel-body">
                <table id="tablaOtrosProcedimientos" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 20% !important;">TIPO CONTROL</th>
                            <th style="width: 20% !important;">FECHA ASIGNACIÓN</th>
                            <th style="width: 10% !important;">DÍAS</th>
                            <th style="width: 10% !important;">ESTADO</th>
                            <th style="width: 10% !important;">USUARIO</th>
                            <th style="width: 20% !important;">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>



<div class="modal fade" id="erroresModalControlEstada" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Falta Información</h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger imprimir-mensajes-control-estada" style="display:none">
                <ul></ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
    </div>
</div>