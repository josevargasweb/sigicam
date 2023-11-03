
{{-- Script Nolazko--}}
<script>

$(".slctpikr").selectpicker();
$("#complejidad_servicio").on("change", function(){
        var value = $(this).val();
        console.log('value: '+value)
        $.ajax({
            url: '{{URL::to("getAreaFuncionalPorServicio")}}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { "complejidad_servicio": value },
            dataType: "json",
            type: "post",
            success: function(data){
                console.log(data);

    
                $("#servicios2").empty();
                data.forEach(function(element){
                    $("#servicios2").append('<option value="'+element.id_complejidad_area_funcional+'" selected="selected">'+element.nombre+'</option>');
                });
            },
            error: function(error){
                console.log(error);
            }
        });

        console.log(value);
    });
</script>
{{-- Fin Script Nolazko--}}

    
<script>

    $( document ).ready(function() {
        console.log("realizando");
        var hora = new Date();
        console.log("date "+hora);

        $.ajax({
            url: "{{ URL::to('/consultarHora')}}",
            type: "post",
            dataType: "json",
            success: function(data){
                console.log("data:", data);

                @if(Session::get("usuario")->tipo != "master" && Session::get("usuario")->tipo != "enfermeraP")
                    if (data.exito) {
                        $(".riesgo").removeClass("hidden");
                    }
                    if (data.error) {
                        $(".riesgo_info").removeClass("hidden");
                    }
                @else
                    $(".riesgo").removeClass("hidden");
                @endif

            },
            error: function(error){

            }
        });


        console.log({{$caso}});
        caso = {{$caso}};
        $(".detalles-caso").val(caso);
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            }
        });

        $.ajax({
            url: "{{ URL::to('/riesgoActual')}}",
            data: {caso : {{$caso}}},
            type: "post",
            dataType: "json",
            success: function(data){
                if (data.riesgo != null) {
                    $("#riesgo").val(data.riesgo);
                    if(data.riesgo == "D2" || data.riesgo == "D3"){
                        $("#div-comentario-riesgo").removeClass( "hidden");
                    }else{
                        if($("#div-motivo").hasClass("hidden") == false){    
                            $("#div-comentario-riesgo").addClass( "comentario-riesgo");       
                        }
                        
                    }
                }

            },
            error: function(error){

            }
        });

        var _token = $('input[name="_token"]').val();

        


        
    });

    function modalRiesgoDependencia(){
        if('{{$unidad->url}}'=='saludmentalapace' || '{{$unidad->url}}'=='saludmentalapiace'){
		$("#modalFormularioRiesgo2").modal();
	}
	else{
		$("#modalFormularioRiesgo").modal();
	}
       // $("#modalFormularioRiesgo").modal();

        $.ajax({
            url: "{{ URL::to('/riesgoActual')}}",
            data: {caso : {{$caso}}},
            type: "post",
            dataType: "json",
            success: function(data){
                console.log(data);
                nombre = data.nombre+" "+data.apellido_paterno+" "+data.apellido_materno;
                $("#nombre-paciente").html(nombre);
                dependencia1 = parseInt(data.dependencia1,0);
                dependencia2 = parseInt(data.dependencia2,0);
                dependencia3 = parseInt(data.dependencia3,0);
                dependencia4 = parseInt(data.dependencia4,0);
                dependencia5 = parseInt(data.dependencia5,0);
                dependencia6 = parseInt(data.dependencia6,0);

                riesgo1 = parseInt(data.riesgo1, 0);
                riesgo2 = parseInt(data.riesgo2, 0);
                riesgo3 = parseInt(data.riesgo3, 0);
                riesgo4 = parseInt(data.riesgo4, 0);
                riesgo5 = parseInt(data.riesgo5, 0);
                riesgo6 = parseInt(data.riesgo6, 0);
                riesgo7 = parseInt(data.riesgo7, 0);
                riesgo8 = parseInt(data.riesgo8, 0);
                riesgo9 = parseInt(data.riesgo9, 0);
                
                $("#dependencia1").val(dependencia1);
                $("#dependencia2").val(dependencia2);
                $("#dependencia3").val(dependencia3);
                $("#dependencia4").val(dependencia4);
                $("#dependencia5").val(dependencia5);
                $("#dependencia6").val(dependencia6);

                $("#riesgo1").val(riesgo1);
                $("#riesgo2").val(riesgo2);
                $("#riesgo3").val(riesgo3);
                $("#riesgo4").val(riesgo4);
                $("#riesgo5").val(riesgo5);
                $("#riesgo6").val(riesgo6);
                $("#riesgo7").val(riesgo7);
                $("#riesgo8").val(riesgo8);
                $("#riesgo9").val(riesgo9);


                $("#modalFormularioRiesgo2 #dependencia1").val(dependencia1);
                $("#modalFormularioRiesgo2 #dependencia2").val(dependencia2);
                $("#modalFormularioRiesgo2 #dependencia3").val(dependencia3);
                $("#modalFormularioRiesgo2 #dependencia4").val(dependencia4);
                $("#modalFormularioRiesgo2 #dependencia5").val(dependencia5);
                $("#modalFormularioRiesgo2 #dependencia6").val(dependencia6);

                $("#modalFormularioRiesgo2 #riesgo1").val(riesgo1);
                $("#modalFormularioRiesgo2 #riesgo2").val(riesgo2);
                $("#modalFormularioRiesgo2 #riesgo3").val(riesgo3);
                $("#modalFormularioRiesgo2 #riesgo4").val(riesgo4);
                $("#modalFormularioRiesgo2 #riesgo5").val(riesgo5);
                $("#modalFormularioRiesgo2 #riesgo6").val(riesgo6);
                $("#modalFormularioRiesgo2 #riesgo7").val(riesgo7);
                $("#modalFormularioRiesgo2 #riesgo8").val(riesgo8);
                $("#modalFormularioRiesgo2 #riesgo9").val(riesgo9);
                
            
                $('.selectpicker').selectpicker('refresh');
            },
            error: function(error){

            }
        });
        

    }

    function btnRiesgoDependencia (){
        console.log("???");
        var valorDependencia = 0;

        valorDependencia = parseInt($('#dependencia1').val()) +parseInt($('#dependencia2').val()) +parseInt($('#dependencia4').val()) +parseInt($('#dependencia5').val());

        if (parseInt($('#dependencia3').val()) > 10) {
            valorDependencia += parseInt($('#dependencia3').val().substr(0,1));
        }else{
            valorDependencia += parseInt($('#dependencia3').val());
        }

        if (parseInt($('#dependencia6').val()) > 10) {
            valorDependencia += parseInt($('#dependencia6').val().substr(0,1));
        }else{
            valorDependencia += parseInt($('#dependencia6').val());
        }
        var valorRiesgo = 0;

        valorRiesgo = parseInt($('#riesgo1').val()) + parseInt($('#riesgo2').val()) +parseInt($('#riesgo3').val());

        if (parseInt($('#riesgo4').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo4').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo4').val());
        }
        if (parseInt($('#riesgo5').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo5').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo5').val());
        }
        if (parseInt($('#riesgo6').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo6').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo6').val());
        }
        if (parseInt($('#riesgo7').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo7').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo7').val());
        }
        if (parseInt($('#riesgo8').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo8').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo8').val());
        }

        var riesgoDependencia = "";
        if (valorRiesgo >=19) {
            riesgoDependencia = "A";
        }else if(valorRiesgo >= 12 && valorRiesgo <= 18){
            riesgoDependencia = "B";
        }else if (valorRiesgo >= 6 && valorRiesgo <= 11) {
            riesgoDependencia = "C";
        }else{
            riesgoDependencia = "D";
        }


        if (valorDependencia >=13) {
            riesgoDependencia += "1";
        }else if(valorDependencia >= 7 && valorDependencia <= 12){
            riesgoDependencia += "2";
        }else{
            riesgoDependencia += "3";
        }

        $("#riesgo").val(riesgoDependencia);
        $("#div-comentario-riesgo").show();
        $("#div-servicio").show();
        $("#div-area-funcional").show();
        $('#modalFormularioRiesgo').modal('hide');

        /* Inicio Nolazko*/
            /******************* Select Servicio *********************/
                var riesgo=$('#riesgo').val();
                var riesgo=document.getElementById('riesgo').value;   
                console.log(riesgo);
                if(riesgo == 'A1' || riesgo == "A2" || riesgo == "A3" || riesgo == "B1" || riesgo == "B2"){
                    console.log("critico");
                    complejidad = 'crítico';
                }
                else if (riesgo == 'B3' || riesgo == "C1" || riesgo == "C2"){
                    console.log("medio");
                    complejidad = 'medio';
                }
                else if(riesgo == 'C3' || riesgo == "D1" || riesgo == "D2" || riesgo == "D3"){
                    console.log("básico");
                    complejidad = "básico";
                }

                /* $.ajax({
                    url: '{{URL::to("getComplejidadPorRiesgo")}}',
                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { "complejidad": complejidad },
                    dataType: "json",
                    type: "post",
                    success: function(data){
                        console.log(data);
                        $("#complejidad_servicio").empty();
                        $("#complejidad_servicio").append('<option value="" selected="selected">Seleccione servicio</option>');
                        
                        $.each(data, function(kery, value){

                            $("#complejidad_servicio").append('<option value='+value.id_complejidad+' >'+value.nombre_servicio+'</option>');
                        });
                        
                        
                    },
                    error: function(error){
                        console.log(error);
                    }
                }); */
                
             /******************* FIN Select Servicio *********************/
        /*Fin NOlazko*/
        //$("#formIngresarEvolucion").bootstrapValidator("revalidateField", "riesgo");
        if(riesgo == "D2" || riesgo == "D3"){
            $("#div-comentario-riesgo").removeClass( "hidden");
        }
        

    }


    $(function(){
        var veces = 0;

        $("#tabla-evoluciones-paciente").dataTable({
            "iDisplayLength": 8,
            "ordering": false,
            "searching": false,
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });

        $("#tabla-especialidades-paciente").dataTable({
            "iDisplayLength": 8,
            "ordering": false,
            "searching": false,
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });


        $("#btnCambiarCategoria").on("click", function(){
            console.log("clcik boton");
            $("#btnAceptarAsingar").attr('disabled', 'disabled');
            $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "riesgo");
            $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "especialidad[]");
            if($("#div-motivo").hasClass("hidden") == false){    
                $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "motivo");
            }   
            if($("#div-comentario-riesgo  ").hasClass("hidden") == false){    
                $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "comentario-riesgo");
            }                 
            
        });
        
        


        $("#formIngresarEvolucion").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                riesgo: {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                console.log("validacion riesgo");
                                if(value == null || value == ''){
                                    return {valid: false, message: "El riesgo debe ser ingresado"};
                                }                             
                                
                                if(value == 'D3' || value == 'D2'){
                                    $("#div-comentario-riesgo").removeClass( "hidden");     
                                    //$("#formIngresarEvolucion").bootstrapValidator("revalidateField", "comentario-riesgo");                        
                                }
                                return true;
                                
                            }
                        }
                    }                
                },
                'especialidad[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar al menos una especialidad'
                        }
                    }
                },
                'categoria_atencion': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un tipo de atención'
                        }
                    }
                },
                'categoria_acompañamiento': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un acompañamiento'
                        }
                    }
                },
                motivo: {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                console.log("validacion motivo");
                                if($("#div-motivo").hasClass("hidden") ){   
                                    return true;                      
                                }
                                if(value == null || value == ''){
                                    return {valid: false, message: "Falta añadir este comentario"};
                                }
                                return true;
                                
                            }
                        }
                    }   
                },
                'comentario-riesgo': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                console.log("validacion comentario riesgo");
                                if($("#div-comentario-riesgo").hasClass("hidden")){    
                                    return true;
                                }

                                if ( value = null || value == ''){
                                    return {valid: false, message: "El comentario es obligatorio"};    
                                }
                                return true;
                                
                            }
                        }
                    }   
                }
            }
            
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            swalCargando.fire({});
            console.log("entro form");            
            if(veces >= 1){
                return false;
            }
            veces++;
            console.log(veces);
            $("#btnAceptarAsingar").attr('disabled', 'disabled');
            evt.preventDefault(evt);
            console.log(evt);
            var $form = $(evt.target);

            var dependencias = [$("#dependencia1").val(), $("#dependencia2").val(), $("#dependencia3").val(), $("#dependencia4").val(), $("#dependencia5").val(), $("#dependencia6").val()];
            var riesgos = [$("#riesgo1").val(), $("#riesgo2").val(), $("#riesgo3").val(), $("#riesgo4").val(), $("#riesgo5").val(), $("#riesgo6").val(), $("#riesgo7").val(), $("#riesgo8").val()];

            selects = $('#modalFormularioRiesgo2').find("select");
            dependencias2 = [];
            riesgos2 = [];
            $.each(selects, function(i, val){
                
                idInput = val.id;
                //saco el primer caracter para saber si es riesgo o dependencia
                primerCaracter = idInput.substr(0,1);

                if(primerCaracter == "d"){
                    dependencias2.push($(this).val());
                    //console.log(parseInt($("#"+idInput).val()));
                }

                if(primerCaracter == "r"){
                    riesgos2.push($(this).val());
                    //console.log(parseInt($("#"+idInput).val()));
                }
            });

            categoria_atencion = '';
            if ($('#categoria_atencion').length){
                categoria_atencion = $('#categoria_atencion').val();
            }
            
            categoria_acompañamiento = '';
            if ($('#categoria_acompañamiento').length){
                categoria_acompañamiento = $('#categoria_acompañamiento').val();

            }
            console.log(categoria_atencion,categoria_acompañamiento);
            $.ajax({
                url: "{{ URL::to('/cambiarRiesgo')}}",
                data: {
                    servicios:"{{$unidad->url}}", 
                    dependencias2: dependencias2, 
                    riesgos2: riesgos2,
                    dependencias : dependencias, 
                    riesgos : riesgos, 
                    categoria : $("#riesgo").val(), 
                    idCaso : $(".detalles-caso").val(), 
                    motivo:$("#motivo_riesgo_nuevo").val(), 
                    comentario_riesgo: $("#comentario-riesgo").val(),
                    servicios2: $("#servicios2").val(), //Nolazko
                    especialidad: $("#especialidad_array").val(),
                    categoria_atencion:categoria_atencion,
                    categoria_acompañamiento:categoria_acompañamiento
                },
                type: "post",
                dataType: "json",
                success: function(data){
                    swalCargando.close();
                    Swal.hideLoading();
                    if(data.error){
                        swalError.fire({
						title: 'Error',
						text:data.error
						});
                        if (data.tipo == "101") {
                            $("#div-motivo").removeClass( "hidden");  
                            $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "motivo");
                        }
                        if (data.tipo == "100") {
                            $("#div-comentario-riesgo").removeClass( "hidden");
                            $("#formIngresarEvolucion").bootstrapValidator("revalidateField", "comentario-riesgo");
                        }
                        veces--;
                    }else{
                        swalExito.fire({
						title: 'Exito!',
						text: "Datos ingresados correctamente",
						didOpen: function() {
							setTimeout(function() {
						location . reload();
							}, 2000)
						},
						});
                    
                        $("#modalVerDetalles .modal-body").html(data.contenido);
                    }
                    //location.reload();
                },
                error: function(error){

                }
            });
            swalCargando.close();
            Swal.hideLoading();
            $('#btnAceptarAsingar').removeAttr("disabled");

            return false;
            
        });

    });
</script>

<fieldset>
    {{ csrf_field() }}

    {{-- <div class="" width='50%'>
        <ul class="nav nav-pills primerNav">
            <li class="nav active"><a href="#categorizacion" data-toggle="tab">Categorizacion</a></li>
            <li class="nav"><a href="#especialidad" data-toggle="tab">Especialidad</a></li>
        </ul>

        <div class="tab-content"> --}}
            <div class="tab-pane fade in active" style="padding-top:10px;" id="categorizacion">
                <div class="col-sm-12">
                    <legend>Categorización</legend>

                    <div class="col-md-12">
                        <div class="table-responsive">
                        <table id="tabla-evoluciones-paciente" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Categoría</th>
                                <th>Servicio</th>
                                <th>Área Funcional</th>
                                <th>Estado paciente</th>
                                @if($edad <= 15 || $edad == '')
                                 <th>Acompañamiento</th>
                                @endif
                                <th>Especialidades</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($evoluciones as $evolucion)
                                <tr>
                                    <td>{{ $evolucion['fecha'] }}</td>
                                    <td>{{ $evolucion['riesgo'] }}</td>
                                    <td>{{ $evolucion['servicios'] }}</td>
                                    <td>{{ $evolucion['area'] }}</td>
                                    <td>{!! $evolucion['atencion'] !!}</td>
                                    @if($edad <= 15 || $edad == '')
                                        <td>{!! $evolucion['acompañamiento'] !!}</td>
                                    @endif
                                    <td>{!! $evolucion['especialidades'] !!}</td>
                                </tr>
                            @endforeach
                
                            </tbody>
                        </table>
                        </div>
                    </div>

                    @if(Session::get("usuario")->tipo !== TipoUsuario::MEDICO)
                        <div class="riesgo_info hidden form-group col-md-12">
                            <p>El horario habilitado para realizar la categorización del paciente es :
                                <b>00:00 AM - 05:59 PM</b>
                            </p>
                        </div>
                        {{ Form::open( array('method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formIngresarEvolucion') ) }}
                        {{ Form::hidden('caso', '', array('class' => 'detalles-caso')) }}
                        <div class="form-group col-md-12">
                            <!-- {{ Form::select('nuevo-riesgo', array_slice($riesgos,1), null, array('id' => 'nuevo-riesgo', 'class' => 'form-control', 'style' => 'width: 70%;')) }} -->
                    
                        </div>
                        @if(Session::get('usuario')->tipo != 'director' && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS && Session::get("usuario")->tipo !== TipoUsuario::CENSO)
                            <div class="col-sm-6 form-group hidden" id="div-comentario-riesgo"  style="margin-bottom: 10px;">
                                <label for="riesgo" class="control-label" title="Riesgo">Comentario riesgo: </label> <br>
                                {{ Form::textarea('comentario-riesgo', null, array('id' => 'comentario-riesgo','class' => 'form-control', 'rows'=>'3')) }}
                            </div>

                            
                            <br>
                            {{-- Nolazko --}}
                            {{-- <div class="col-sm-12" id="div-servicio" hidden style="margin-bottom: 10px;">
                                <label for="complejidad" class="control-label" title="Unidad">Servicio: </label>
                                {{ Form::select('complejidad_servicio', array(""=>""), 0 , array('class' => 'form-control', 'id' => 'complejidad_servicio'), $atributos) }}
                            </div>
                    
                            <div class="col-sm-12" id="div-area-funcional" hidden style="margin-bottom: 10px;">
                                <label for="servicios2" class="control-label" title="Unidad">Área funcional: </label>
                                {{ Form::select('servicios2', array(""=>""), 0 , array('class' => 'form-control', 'id' => 'servicios2')) }}
                            </div> --}}
                            {{-- Nolazko --}}
                            <div class="row">
                                <div id="div-motivo" class="col-sm-8 form-group hidden" style="margin-bottom: 10px; margin-left: 15px;">
                                    <label>Motivo:</label><br>
                                    {{ Form::textarea('motivo', null, array('id' => 'motivo_riesgo_nuevo','class' => 'form-control', 'rows'=>'3')) }}
                                </div>
                                <div class="riesgo col-md-12 hidden" style="">
                                    <div class="col-md-5">
                                        Categoria
                                    </div>
                                    <div class="col-md-6">
                                        @if( Session::get("usuario")->establecimiento== 27  || Session::get("usuario")->tipo === TipoUsuario::MASTER  || Session::get("usuario")->tipo === TipoUsuario::MASTERSS)
                                            <div class="col-md-3">
                                                Estado paciente
                                            </div>
                                            @if($edad <= 15 || $edad == '')
                                                <div class="col-md-3">
                                                    Acompañamiento
                                                </div>
                                            @endif
                                        @endif
                                        <div class="col-md-3">
                                            Especialidad(es)
                                        </div>
                                    </div>
                                </div>
                                <div class="riesgo col-md-12 hidden" style="">
                                    <div class="col-md-1 form-group" style="">
                                        {{ Form::text('riesgo', null, array('id' => 'riesgo','class' => 'form-control', 'readonly', 'style' => 'text-align:center; width: 50px;')) }}
                    
                                    </div>
                                    <div class="col-md-4 form-group" style="">                    
                                        <a id="riesgo" type="" class="btn btn-primary" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
                                    </div>
                                    <div class="col-md-6">
                                        @if( Session::get("usuario")->establecimiento== 27  || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS)
                                            <div class="col-md-3 form-group" style="">
                                                {{Form::select('categoria_atencion', array('Estable' => 'Estable', 'Regular' => 'Regular', 'Grave' =>'Grave', 'Muy grave' =>'Muy grave'), null, array('id' => 'categoria_atencion', 'class' => 'form-control', 'placeholder' => 'seleccione'))}}           
                                            </div>
                                            @if($edad <= 15 || $edad == '')
                                                <div class="col-md-3 form-group" style="">
                                                    {{Form::select('categoria_acompañamiento', array('Diurno' => 'Diurno', 'Nocturno' => 'Nocturno', 'Ambos' =>'Ambos'), null, array('id' => 'categoria_acompañamiento', 'class' => 'form-control', 'placeholder' => 'seleccione'))}}           
                                                </div>
                                            @endif
                                        @endif
                                        <div class="col-md-4 form-group" style="">
                                            {{ Form::select('especialidad[]', $lista_especialidades, null, array('id' => 'especialidad_array','class' => 'slctpikr form-control', 'multiple','data-max-options'=>'3','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;, &quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;]"))}}            
                                        </div>
                                        <div class="col-md-1 form-group" style="">
                                            {{ Form::submit('Guardar categoria', array('id' => 'btnCambiarCategoria', 'class' => 'btn btn-success')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            {{-- <div class="tab-pane fade" style="padding-top:10px;" id="especialidad">
                <div class="col-sm-12">
                    <legend>Especialidad</legend>
                    
                    <div class="col-md-12">
                        <div class="table-responsive">
                        <table id="tabla-especialidades-paciente" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Especialidades</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($especialidades as $especialidad)
                                <tr>
                                    <td>{{ $especialidad['fecha'] }}</td>
                                    <td>{!! $especialidad['especialidades'] !!}</td>
                                </tr>
                            @endforeach
                
                            </tbody>
                        </table>
                        </div>
                    </div>

                </div>
            </div>
            
        </div> --}}
    </div>

    
    
    
</fieldset>
{{ Form::close() }}
