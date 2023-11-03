<script>

    function validarFormularioTratamientoAntimicrobiano() {
        $("#usoRestringidoform").bootstrapValidator("revalidateField", "patogeno");
        //$('#usoRestringidoform').bootstrapValidator('revalidateField', 'patogeno');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'sitio_infeccion');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'terapia_empirica_especifica');//validar que este seleccionado
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'fechaCultivo[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'locacionCultivo[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'antimicrobiano_actual[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'dosisAntimicrobiano[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'posologiantimicrobiano[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'duracionAntimicrobiano[]');

        //Cuando se abre un tratamiento actual al hacer cambio de tipo de tratamiento
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'dosisAntimicrobiano_actual[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'posologiantimicrobiano_actual[]');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'duracionAntimicrobiano_actual[]');

        //Validacion de justificacion de solicitud
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'justificacion_temperatura');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'justificacion_parametro');
        $('#usoRestringidoform').bootstrapValidator('revalidateField', 'justificacion_estado');
    }

    function generarTablaUsoRestringido() {
        tabledUsoRestringido = $("#tableUsoRestringido").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/buscarHistorialUsoRestringido/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }


    function editar(idFormulario){
        //$('#usoRestringidoform').resetForm();
        //$("#usoRestringidoform").validate().resetForm();
        //Traer datos del formulario
        //$("#usoRestringidomodal").modal();
        id = idFormulario;
        $.ajax({
            url: "{{URL::to('gestionMedica')}}/editarFormulario/"+id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                if(data.info){
                    swalInfo2.fire({
                        title: 'Información',
                        text: data.info
                    }).then(function(result) {
                        tabledUsoRestringido.api().ajax.reload();
                        $('#usoRestringidomodal').modal('hide');
                    });
                }else{
                    formulario = data.datos;
                    if(formulario.length !== 0){

                        if(formulario.id != null){
                            $("#id_formulario_uso_restringido").val(formulario.id).change();
                        }

                        if(formulario.tipo_tratamiento != null){
                            $("#tipo_tratamiento").val(formulario.tipo_tratamiento).change();
                        }

                        if(data.diagnostico != null || data.diagnostico.diagnostico != ""){
                            $("#diagnosticor").val(data.diagnostico.diagnostico).change();
                            $("#id_diagnostico").val(data.diagnostico.id).change();
                        }

                        //falta terapia empirica
                        if(formulario.terapia_especifica_empirica != null && formulario.terapia_especifica_empirica == "terapia_empirica"){
                            //  $("#checkHabito").prop("checked", true);
                            $("#terapia_empirica").prop("checked", true);
                            $('.mostrarSitioInfeccion').removeAttr( "hidden" );
                            if(formulario.sitio_infeccion != null){
                                $("#sitio_infeccion").val(formulario.sitio_infeccion).change();
                            }
                        }

                        if(formulario.terapia_especifica_empirica != null && formulario.terapia_especifica_empirica == "terapia_especifica"){
                            // $("#checkTerapiaEspecifica").prop("checked", true);
                            $("#terapia_especifica").prop("checked", true);
                            $('.mostrarPatogeno').removeAttr( "hidden" );
                            $('.mostrarCultivos').removeAttr( "hidden" );
                            //$('.mostrarTratamiento').removeAttr( "hidden" );
                            $("#patogeno").val(formulario.patogeno).change();
                            $('#usoRestringidoform').bootstrapValidator('revalidateField', 'patogeno');
                        }
                                                    
                        if(formulario.sospecha_iaas != null && formulario.sospecha_iaas == true){
                            $("#iaasT").prop("checked", true);
                            // $('.mostrarTerapiaEspecifica').removeAttr( "hidden" );  
                        }else if(formulario.sospecha_iaas != null && formulario.sospecha_iaas == false){
                            $("#iaasF").prop("checked", true);
                        }


                        if(formulario.temperatura_justificacion != null){
                            $("#justificacion_temperatura").val(formulario.temperatura_justificacion).change();
                        }
                        
                        if(formulario.parametro_infeccioso_justificacion != null){
                            $("#justificacion_parametro").val(formulario.parametro_infeccioso_justificacion).change();
                        }
                        
                        if(formulario.estado_clinico_justificacion != null){
                            $("#justificacion_estado").val(formulario.estado_clinico_justificacion).change();
                        }
                    
                        if(formulario.comentario_justificacion != null){
                            $("#justificacion_comentario").val(formulario.comentario_justificacion).change();
                        }

                        if(data.datosCultivo != ''){
                            cultivos = data.datosCultivo;
                            if(cultivos.length !== 0){

                            for (i = 0; i < cultivos.length -1; i++) {
                                agregarCultivos();
                            }
                            for (i = 0; i < cultivos.length; i++) {
                                $("#id_cultivo"+i).val( cultivos[i].id).change();
                                $("#fechaCultivo"+i).data("DateTimePicker").date(new Date(cultivos[i].fecha_cultivo));
                                $("#antibioticoCultivo"+i).val( cultivos[i].agente_cultivo).change();
                                $("#locacionCultivo"+i).val( cultivos[i].localizacion_cultivo).change();
                            }
                            }
                        }
                        
                        if(data.tratamientoAnterior != ''){
                            tratamientoAnterior = data.tratamientoAnterior;
        
                            if(tratamientoAnterior.length !== 0){
        
                                for (i = 0; i < tratamientoAnterior.length -1; i++) {
                                        agregarAntimicrobiano();
                                }

                                for (i = 0; i < tratamientoAnterior.length; i++) {
                                    $("#id_antimicrobiano"+i).val( tratamientoAnterior[i].id).change();
                                    $("#antimicrobiano"+i).val( tratamientoAnterior[i].antimicrobiano_tratamiento).change();
                                    $("#dosisAntimicrobiano"+i).val( tratamientoAnterior[i].dosis_tratamiento).change();
                                    $("#posologiantimicrobiano"+i).val( tratamientoAnterior[i].posologia_tratamiento).change();
                                    $("#duracionAntimicrobiano"+i).val( tratamientoAnterior[i].duracion_tratamiento).change();
                                }
                            }
                        }
                        //limpiar zona donde se agregan los antimicrobianos actuales
                        $("#moduloAntimicrobianocopia_actual").empty();

                        if(data.tratamientoActual != ''){
                            tratamientoActual = data.tratamientoActual;
                            if(tratamientoActual.length !== 0){
                                for (i = 0; i < tratamientoActual.length -1; i++) {
                                    agregarAntimicrobianoActual();
                                }
                                for (i = 0; i < tratamientoActual.length; i++) {
                                    $("#id_antimicrobianoActual"+i).val(tratamientoActual[i].id);
                                    $("#antimicrobiano_actual"+i).val( tratamientoActual[i].antimicrobiano_tratamiento);
                                    $("#dosisAntimicrobiano_actual"+i).val( tratamientoActual[i].dosis_tratamiento);
                                    $("#posologiantimicrobiano_actual"+i).val( tratamientoActual[i].posologia_tratamiento);
                                    $("#duracionAntimicrobiano_actual"+i).val( tratamientoActual[i].duracion_tratamiento);
                                }
                            }
                        }else{
                            $( ".mostrarAntimicrobianoAnterior" ).attr('hidden', true);
                            $( "#titulo_tratamiento" ).text('Tratamiento antimicrobiano actual');
                        }
                        $("#editarUsoRestringido").val("editandoUsoRestringido");
                        $("#usoRestringidomodal").modal();
                    }
                }	
            },
            error: function(error){
                console.log(error);
            }
        });
        
    }

    $(document).ready( function() {


        $("#hca").click(function(){
            if (typeof tabledUsoRestringido == 'undefined') {
                generarTablaUsoRestringido();
            }
        });

        $( "#btndUsoRestringidoForm" ).click(function() {
            $.ajax({
                url: "{{URL::to('gestionMedica')}}/ultimoDiagnostico/{{ $caso }}",
                headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                dataType: "json",
                type: "get",
                success: function(data){
                    $("#id_diagnostico").val(data.diagnostico.id);
                    $("#diagnosticor").val(data.diagnostico.diagnostico);
                    $("#titulo_tratamiento").text("Tratamiento antimicrobiano actual");
                    $("#usoRestringidomodal").modal();
                },
                error: function(error){
                    console.log(error);
                }
            });
        });

        $('#usoRestringidomodal').on('shown.bs.modal', function () {
            validarFormularioTratamientoAntimicrobiano();
        });

        $('#usoRestringidomodal').on('hide.bs.modal', function () {
            counterAntimicrobiano = 1;
            counterAntimicrobiano_actual = 1;
            counterCultivos = 1;
            $( "#moduloCultivoscopia" ).empty();
            $( "#moduloAntimicrobianocopia" ).empty();

            $("#id_formulario_uso_restringido").val("");

            $("#tipo_tratamiento").val("1").change();

            $("#id_diagnostico").val("");
            $("#diagnosticor").val("");
            
            $('#terapia_empirica').prop('checked', false);
            $( ".mostrarCultivos" ).attr('hidden', true);
            $( ".mostrarSitioInfeccion" ).attr('hidden', true);
            $("#sitio_infeccion").val("");
            
            $("#iaasT").prop("checked", false);
            $("#iaasF").prop("checked", false);
            // $( ".mostrarTerapiaEspecifica" ).attr('hidden', true);
            $('#terapia_especifica').prop('checked', false);
            //$( ".mostrarTratamiento" ).attr('hidden', true);
            $( ".mostrarPatogeno" ).attr('hidden', true);
            $("#patogeno").val("");

            $("#id_cultivo0").val("");
            $("#fechaCultivo0").val("");
            $("#locacionCultivo0").val("");
            $("#antibioticoCultivo0").val("2").change();

            $("#id_antimicrobiano0").val("");
            $("#antimicrobiano0").val("");
            $("#dosisAntimicrobiano0").val("");
            $("#posologiantimicrobiano0").val("");
            $("#duracionAntimicrobiano0").val("");
         
            $("#id_antimicrobianoActual0").val("");
            $("#antimicrobiano_actual0").val("");
            $("#dosisAntimicrobiano_actual0").val("");
            $("#posologiantimicrobiano_actual0").val("");
            $("#duracionAntimicrobiano_actual0").val("");

            $("#justificacion_temperatura").val("");
            $("#justificacion_parametro").val("");
            $("#justificacion_estado").val("");
            $("#justificacion_comentario").val("");

            $("#eliminados_cultivos").val("");
            $("#eliminados_antimicrobiano").val("");
            $("#eliminados_antimicrobiano_actual").val("");
            
            $("#titulo_tratamiento").val("");

            $("#editarUsoRestringido").val("");
        });

        //validar al guardar formulario
        $("#guardarUsoRestringido").click(function(){
            validarFormularioTratamientoAntimicrobiano();
        });

        $("#usoRestringidoform").bootstrapValidator({
            excluded:[':disabled', ':hidden', ':not(:visible)'],
            /* feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            }, */
            fields: {
                'diagnosticor': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'sitio_infeccion': {
                    trigger: 'change keyup',
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'iaas': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'terapia_empirica_especifica': {
                    trigger: 'change keyup',
                    validators:{
                        /* notEmpty: {
                            message: 'Campo obligatorio'
                        }, */
                        callback: {
                            callback: function(value, validator, $field){
                                if(value == null || value == ''){
                                    return {valid: false, message: "Campo obligatorio"};
                                }                             
                                return true;                                
                            }
                        }
                    }
                },
                'patogeno': {
                    trigger: 'change keyup',
                    validators:{
                        /* notEmpty: {
                            message: 'Campo obligatorio'
                        }, */
                        callback: {
                            callback: function(value, validator, $field){
                                if(value == null || value == ''){
                                    return {valid: false, message: "Campo obligatorio"};
                                }                             
                                return true;
                            }
                        }
                    }
                },
                'nutricion': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'fechaCultivo[]': {
                    trigger: 'change keyup',
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'antibioticoCultivo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'locacionCultivo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'antimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'dosisAntimicrobiano[]': {
                    validators:{
                        greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'posologiantimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'duracionAntimicrobiano[]': {
                    validators:{
                        greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'antimicrobiano_actual[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'dosisAntimicrobiano_actual[]': {
                    validators:{
                        greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'posologiantimicrobiano_actual[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'duracionAntimicrobiano_actual[]': {
                    validators:{
                        greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'justificacion_temperatura': {
                    validators:{
                        greaterThan: {
                            inclusive: true,
                            value: 1,
                            message: 'La cantidad debe ser mayor a 0'
                        },
                        notEmpty: {
                            message: 'Campo obligatorio'
                        },
                        callback: {
                            message: "Solo puede contener máximo 1 decimales",
                            callback: function (value, validator) {
                                if (value.substring(value.indexOf('.')).length < 3)   {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                        }
 			 		}
                },
                'justificacion_parametro': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'justificacion_estado': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
            }
        }).on('status.field.bv', function(e, data){
            $("#usoRestringidoform input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#usoRestringidoform input[type='submit']").prop("disabled", false);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            swalPregunta.fire({
                title: '¿Desea Guardar este formulario?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    guardarFormUsoRestringido($form);
                }
            });
            /* 
            if($('#editarUsoRestringido').val() == 'editandoUsoRestringido'){
                swalPregunta.fire({
                    title: '¿Desea Guardar este formulario?',
                    text:  "Si guarda este formulario el tipo de tratamiento pasara a Cambio Tratamiento"
                }).then(function(result) {
                    if (result.isConfirmed) {
                        guardarFormUsoRestringido($form);
                    }
                });
            }else{
                swalPregunta.fire({
                    title: '¿Desea Guardar este formulario?',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        guardarFormUsoRestringido($form);
                    }
                });
            } */
        });
    });

   function guardarFormUsoRestringido($form){
        $.ajax({
            url: "{{ URL::to('/gestionMedica')}}/agregarUsoRestringido",
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            dataType: "json",
            data: $form .serialize(),
            success: function(data){
                if(data.exito){
                    swalExito.fire({
                        title: 'Exito!',
                        text: data.exito,
                        didOpen: function() {
                            setTimeout(function() {
                                tabledUsoRestringido.api().ajax.reload();
                                $('#usoRestringidomodal').modal('hide');
                            }, 2000)
                        },
                    });
                }
                if(data.error){
                    swalError.fire({
                        title: 'Error',
                        text:data.error
                    });	
                }		
                if(data.info){
                    swalInfo2.fire({
                        title: 'Información',
                        text: data.info
                    }).then(function(result) {
                        tabledUsoRestringido.api().ajax.reload();
                        $('#usoRestringidomodal').modal('hide');
                    });
                }	
            },
            error: function(error){
                console.log(error);
            }
        });
   }


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
{{ Form::hidden('idCaso', $caso, array('id' => 'idCasoUsoRestringido')) }}

    <div class="formulario">
        <input type="hidden" value="" name="id_formulario_diagnostico_medico" id="id_formulario_diagnostico_medico">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                <div class="col-md-12">
                    <legend>Formulario Uso Restringido</legend>
                    <div class="col-md-12 pl-0 pr-0">
                        <div class="col-md-2 pl-0 pr-0">
                        <a href="#" class="btn btn-primary" id="btndUsoRestringidoForm">Generar Formulario</a>
                        </div>
                    </div>
                </div>
                </div>
                <br>
                <br>
                <legend>Listado de Uso Restringido</legend>
                <table id="tableUsoRestringido" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 25%">OPCIONES</th>
                            <th style="width: 50%">USUARIO APLICA</th>
                            <th style="width: 25%">FECHA INGRESADA</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>   
            </div>
        </div>
    </div>
{{ Form::close() }}


{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'usoRestringidoform', 'autocomplete' => 'off')) }}
{{ Form::hidden('idCaso', $caso, array('id' => 'idCasoUsoRestringido')) }}
{{ Form::hidden('id_formulario_uso_restringido', '', array('id' => 'id_formulario_uso_restringido')) }}
{{ Form::hidden('eliminados_antimicrobiano', '', array('id' => 'eliminados_antimicrobiano')) }}
{{ Form::hidden('eliminados_antimicrobiano_actual', '', array('id' => 'eliminados_antimicrobiano')) }}
{{ Form::hidden('eliminados_cultivos', '', array('id' => 'eliminados_cultivos')) }}
{{ Form::hidden('editarUsoRestringido', '', array('id' => 'editarUsoRestringido')) }}
<div class="modal fade" id="usoRestringidomodal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
            @include('Gestion.gestionMedica.partials.FormUsoRestringido')
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
{{Form::close()}}