<script>

    function limpiarFormularioBarthel(){
        $('#apertura_ocular').val('').change();
        $('#respuesta_verbal').val('').change();
        $('#respuesta_motora').val('').change();
        $('#totalGlasgow').val('');
        $("#Comer").val('').change();
        $("#Lavarse").val('').change();
        $("#Vestirse").val('').change();
        $("#Arreglarse").val('').change();
        $("#Deposicion").val('').change();
        $("#Miccion").val('').change();
        $("#Retrete").val('').change();
        $("#Trasferencia").val('').change();
        $("#Deambulacion").val('').change();
        $("#Escaleras").val('').change();
        $("#totalBarthel").val('');
        $("#guardarBarthel").prop("disabled", false);
    }

    function validadoresEvolucionEnfermeria(){
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'neurologico');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'cardiovascular');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'respiratorio');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'digestivo');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'metabolico');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'musculoesqueletico');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'tegumentario');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'genitourinario');
        $("#btnGuardarEvolucionEnfermeria").prop("disabled", false);
    }

    function validadoresBarthel(){
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'comida');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'lavado');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'vestido');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'arreglo');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'deposicion');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'miccion');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'retrete');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'trasferencia');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'deambulacion');
        $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'escaleras');
    }

    function toggleValidadoresBarthel(opcion){
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'comida', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'lavado', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'vestido', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'arreglo', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'deposicion', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'miccion', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'retrete', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'trasferencia', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'deambulacion', opcion);
        $('#FormEvolucionEnfermeria').bootstrapValidator('enableFieldValidators', 'escaleras', opcion);
    }

    function pasaValidacionBarthel(){
        var formulario = $("#FormEvolucionEnfermeria").data('bootstrapValidator');
        var iscomida = formulario.isValidField('comida');
        var islavado = formulario.isValidField('lavado');
        var isvestido = formulario.isValidField('vestido');
        var isarreglo = formulario.isValidField('arreglo');
        var isdeposicion = formulario.isValidField('deposicion');
        var ismiccion = formulario.isValidField('miccion');
        var isretrete = formulario.isValidField('retrete');
        var istrasferencia = formulario.isValidField('trasferencia');
        var isdeambulacion = formulario.isValidField('deambulacion');
        var isescaleras = formulario.isValidField('escaleras');
        
        if(iscomida == true && 
            islavado == true && 
            isvestido == true && 
            isarreglo == true && 
            isdeposicion == true && 
            ismiccion == true && 
            isretrete == true && 
            istrasferencia == true && 
            isdeambulacion == true && 
            isescaleras == true){
            return true;
        }else{
            return false;
        }
    }

    function recargarDatosBarthel(){
        var id_barthel = $("#id_formulario_evolucion_enfermeria").val();
        if(id_barthel){
            $.ajax({
                url: "{{ URL::to('/gestionEnfermeria')}}/datosBarthelEvolucionEnfermeria",
                data: {
                    id_barthel : id_barthel
                },
                headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                },
                type: "post",
                dataType: "json",
                success: function (response) {
                    var datosBarthel = response;
                    if(!jQuery.isEmptyObject(datosBarthel)){
                        $("#id_barthel").val(datosBarthel.id_formulario_barthel);
                        $("#Comer").val(datosBarthel.comida);
                        $("#Lavarse").val(datosBarthel.lavado);
                        $("#Vestirse").val(datosBarthel.vestido);
                        $("#Arreglarse").val(datosBarthel.arreglo);
                        $("#Deposicion").val(datosBarthel.deposicion);
                        $("#Miccion").val(datosBarthel.miccion);
                        $("#Retrete").val(datosBarthel.retrete);
                        $("#Trasferencia").val(datosBarthel.trasferencia);
                        $("#Deambulacion").val(datosBarthel.deambulacion);
                        $("#Escaleras").val(datosBarthel.escaleras);
                        suma = Number($("#Comer").val()) + Number($("#Lavarse").val()) + Number($("#Vestirse").val()) + Number($("#Arreglarse").val()) + Number($("#Deposicion").val())+ Number($("#Miccion").val()) + Number($("#Retrete").val()) + Number($("#Trasferencia").val()) + Number($("#Deambulacion").val()) + Number($("#Escaleras").val());
                        $("#totalBarthel").val(suma);
                        $("#indbarthel").val(suma);
                        validadoresBarthel();
                    }
                },
                error: function (error){
                    console.log(error);
                }
            });
        }
    }

    function mostrarDatosEvolucionEnfermeria(){
        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosEvolucionEnfermeria",
            data: {
                caso : caso
            },
            headers: {					         
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
            },
            type: "post",
            dataType: "json",
            success: function (response) {
                var evolucionesEnfermeria = response.evolucionesEnfermeria;
                if(!jQuery.isEmptyObject(evolucionesEnfermeria)){
                    $("#id_formulario_evolucion_enfermeria").val(evolucionesEnfermeria.id);
                    $("#neurologico").val(evolucionesEnfermeria.neurologico);
                    $("#cardiovascular").val(evolucionesEnfermeria.cardiovascular);
                    $("#respiratorio").val(evolucionesEnfermeria.respiratorio);
                    $("#digestivo").val(evolucionesEnfermeria.digestivo);
                    $("#metabolico").val(evolucionesEnfermeria.metabolico);
                    $("#musculoesqueletico").val(evolucionesEnfermeria.musculoesqueletico);
                    $("#tegumentario").val(evolucionesEnfermeria.tegumentario);
                    $("#genitourinario").val(evolucionesEnfermeria.genitourinario);
                }
                var datosBarthel = response.datosBarthel;
                if(!jQuery.isEmptyObject(datosBarthel)){
                    $("#id_barthel").val(datosBarthel.id_formulario_barthel);
                    $("#Comer").val(datosBarthel.comida);
                    $("#Lavarse").val(datosBarthel.lavado);
                    $("#Vestirse").val(datosBarthel.vestido);
                    $("#Arreglarse").val(datosBarthel.arreglo);
                    $("#Deposicion").val(datosBarthel.deposicion);
                    $("#Miccion").val(datosBarthel.miccion);
                    $("#Retrete").val(datosBarthel.retrete);
                    $("#Trasferencia").val(datosBarthel.trasferencia);
                    $("#Deambulacion").val(datosBarthel.deambulacion);
                    $("#Escaleras").val(datosBarthel.escaleras);
                    suma = Number($("#Comer").val()) + Number($("#Lavarse").val()) + Number($("#Vestirse").val()) + Number($("#Arreglarse").val()) + Number($("#Deposicion").val())+ Number($("#Miccion").val()) + Number($("#Retrete").val()) + Number($("#Trasferencia").val()) + Number($("#Deambulacion").val()) + Number($("#Escaleras").val());
                    $("#totalBarthel").val(suma);
                    $("#indbarthel").val(suma);
                }
                validadoresEvolucionEnfermeria();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $(function() {

        var datosBarthel = {};

        $("#epicrisis").click(function(){
            mostrarDatosEvolucionEnfermeria();
        });

        $("#FormEvolucionEnfermeria").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                neurologico: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema neurologico no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                cardiovascular: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema cardiovascular no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                respiratorio: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema respiratorio no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                digestivo: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema digestivo no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                metabolico: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema metabolico no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                musculoesqueletico: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema musculo esqueletico no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                tegumentario: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema tegumentario no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                genitourinario: {
                    validators: {
                        notEmpty: {
                            //message: "el campo sistema genito urinario no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                indbarthel: {
                    validators: {
                        notEmpty: {
                            //message: "el campo indice barthel no debe estar vacío"
                            message: "Este campo no debe estar vacío"
                        }
                    }
                },
                //
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
                }
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);  
            $("#guardarBarthel").prop("disabled",false);
            e.preventDefault(e);
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/guardarEvolucionEnfermeria",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $form.serialize(),
                dataType: "json",
                type: "post",
                success: function(response){

                    if(response.exito){
                        swalExito.fire({
                            title: "Exito!",
                            text: response.exito,
                            didOpen: function() {
                                setTimeout(function(){
                                    $("#btnGuardarEvolucionEnfermeria").prop("disabled", false);
                                        mostrarDatosEvolucionEnfermeria();  
                                        limpiarFormularioBarthel();
                                }, 2000)
                            },
                        });
                    }

                    if(response.error){
                        swalError.fire({
                            title: "Error",
                            text: response.error,
                        }).then(function(result){
                            if(result.isDenied){
                                $("#btnGuardarEvolucionEnfermeria").prop("disabled", false);
                                mostrarDatosEvolucionEnfermeria();
                            }
                        });
                    }
                },
                error: function(error){
                    console.log(error);
                    $("#btnGuardarEvolucionEnfermeria").prop("disabled", false);
                    mostrarDatosEvolucionEnfermeria();
                    limpiarFormularioBarthel();
                }
            })
        });

        $('#barthelmodal').on('hidden.bs.modal', function(){
            $("#guardarBarthel").prop("disabled",false);
            toggleValidadoresBarthel(false);
        });

        $('#barthelmodal').on('shown.bs.modal', function () {
            // recargarDatosBarthel();
            toggleValidadoresBarthel(true);
            validadoresBarthel(); 
            $("#guardarBarthel").prop("disabled",false);
        });

        $("#guardarBarthel").on("click", function(e){
            var respuesta = pasaValidacionBarthel();
            if(respuesta == true){
                var resultBarthel = 0;
                var resultBarthel = $("#totalBarthel").val();
                $("#indbarthel").val(resultBarthel);
                $("#barthelmodal").modal("hide");
                $('#FormEvolucionEnfermeria').bootstrapValidator('revalidateField', 'indbarthel');
                e.preventDefault(e);
            }else{
                e.preventDefault(e);
            }
        });

    });
</script>
<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

{{Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'FormEvolucionEnfermeria', 'autocomplete' => 'off'))}}
{{Form::hidden('idCaso', $caso, array('class' => 'idCasoEvolucionEnfermeria'))}}
<input type="hidden" value="Epicrisis" name="tipo" id="tipoFormBarthel">
    
<div class="formulario">
    <input type="hidden" value="" name="id_formulario_evolucion_enfermeria" id="id_formulario_evolucion_enfermeria">
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Evolución enfermeria</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"Sistema Neurológico")}}
                        {{Form::text('neurologico', null, ['id' => 'neurologico', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('',"Sistema Cardiovascular")}}
                        {{Form::text('cardiovascular', null, ['id' => 'cardiovascular', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('',"Sistema Respiratorio")}}
                        {{Form::text('respiratorio', null, ['id' => 'respiratorio', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"Sistema Digestivo")}}
                        {{Form::text('digestivo', null, ['id' => 'digestivo', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('',"Sistema Metabólico")}}
                        {{Form::text('metabolico', null, ['id' => 'metabolico', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('',"Sistema Músculo esquelético ")}}
                        {{Form::text('musculoesqueletico', null, ['id' => 'musculoesqueletico', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"Sistema Tegumentario ")}}
                        {{Form::text('tegumentario', null, ['id' => 'tegumentario', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('',"Sistema Genito urinario")}}
                        {{Form::text('genitourinario', null, ['id' => 'genitourinario', 'class' => 'form-control'])}}
                    </div>
                </div>
                @if($infoPaciente->fecha_nacimiento && Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) > 64)
                    <input type="hidden" value="" name="id_barthel" id="id_barthel">
                    <div class="col-md-3 col-md-offset-1" style="">
                        <div class="col-md-8 form-group" style="padding-left: 0;">
                            {{Form::label('',"Indice barthel")}}
                            {{Form::number('indbarthel', null, ['id' => 'indbarthel', 'step' => '0.01', 'class' => 'form-control', 'readonly'])}}
                        </div>
                        <div class="col-md-4" style="margin-top: 20px;padding-left:0;">
                            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#barthelmodal">Realizar</a>
                        </div>
                    </div>    
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" id="btnGuardarEvolucionEnfermeria">Guardar</button>
                </div>
            </div>
            {{-- <br><br>
            <div class="col-md-12">
                <br><br>
                <legend>Lista Evoluciones enfermería</legend>
                <table id="tableEvolucionEnfermeria" class="table table-striped table-bordered table-hover" style="width: fix; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th>FECHA Y USUARIO</th>
                            <th>Sistema neurologico</th>
                            <th>Sistema cardiovascular</th>
                            <th>Sistema respiratorio</th>
                            <th>Sistema digestivo</th>
                            <th>Sistema metabolico</th>
                            <th>Sistema musculo esqueletico</th>
                            <th>Sistema tegumentario</th>
                            <th>Sistema genito urinario</th>
                            <th>Indice Barthel</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div> --}}
        </div>
    </div>
</div>

@if($infoPaciente->fecha_nacimiento && Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) > 15)
    {{Form::hidden('formulariobarthel','si', array('id' => 'formulariobarthel'))}}
    <div class="modal fade" id="barthelmodal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('Gestion.gestionEnfermeria.partials.FormBarthel')
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endif

{{Form::close()}}