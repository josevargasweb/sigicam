<script>

    function validarFormularioExamanesLaboratorio() {
        $("#formExamenLab").bootstrapValidator("revalidateField", "examenes_laboratorio_values");
        $("#formExamenLab").bootstrapValidator("revalidateField", "bioquimicosSangre[]");
        $('#formExamenLab').bootstrapValidator('revalidateField', 'bioquimicosOrina[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'temperaturagasesElp');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'fiogasesElp');//validar que este seleccionado
        $('#formExamenLab').bootstrapValidator('revalidateField', 'gasesElp[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'perfiles[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'liquido[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'hematologicos[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'hormonales[]');
        $('#formExamenLab').bootstrapValidator('revalidateField', 'otros_examenes[]');
    }   

    function eliminarFilaOtrosExamenes(position) {
        var myobj = document.getElementById("moduloOtrosExamanes"+position);
        if($("#id_otros_examenes"+position).val() != '' && typeof $("#eliminados_otros_examenes") !== "undefined"){
                $("#eliminados_otros_examenes").val($("#eliminados_otros_examenes").val()+','+$("#id_otros_examenes"+position).val())
        }
        myobj.remove();               
    }

    var counterOtrosExamenes  = 1;

    function agregarOtrosExamanes(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloOtrosExamanes");
        var cloneDiv = originalDiv.clone();    
        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloOtrosExamanes'+counterOtrosExamenes);

        cloneDiv.find('.btnAgregarOtrosExamanes').remove();

        $("[name='otros_examenes[]']",cloneDiv).attr({'data-id':counterOtrosExamenes,'id':'otros_examenes'+counterOtrosExamenes});          
        $("[name='otros_examenes[]']",cloneDiv).val('');          

        $("[name='id_otros_examenes[]']",cloneDiv).attr({'data-id':counterOtrosExamenes,'id':'id_otros_examenes'+counterOtrosExamenes});    
        $("[name='id_otros_examenes[]']",cloneDiv).val(''); 
    
        html ='<div class="col-md-2 text-center"><button class="btn btn-danger" onclick="eliminarFilaOtrosExamenes('+counterOtrosExamenes+')">-</button></div>';       
        
        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloOtrosExamanescopia").append(cloneDiv);
        cloneDiv.append(html);
        
        $('#formExamenLab').bootstrapValidator('addField', cloneDiv.find("[name='otros_examenes[]']"));

        //incrementa el contador
        counterOtrosExamenes++;      
    };


    function generarTablaExamenLaboratorio() {
        tabledExamenLaboratorio = $("#tableExamenLaboratorio").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/listarExamenLaboratorio/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function guardarFormExamenLaboratorio($form){
        $.ajax({
            url: "{{ URL::to('/gestionMedica')}}/agregarExamenLaboratorio",
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
                                tabledExamenLaboratorio.api().ajax.reload();
                                $('#formularioAgregarExamenLab').modal('hide');
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
                        tabledExamenLaboratorio.api().ajax.reload();
                        $('#formularioAgregarExamenLab').modal('hide');
                    });
                }	
            },
            error: function(error){
                console.log(error);
            }
        });
   }

   function eliminarExamenLaboratorio(idFormulario){
    swalPregunta.fire({
			title: "¿Esta seguro de eliminar este examen?"
		}).then(function(result){
            if (result.isConfirmed) {
                id = idFormulario;
                $.ajax({
                    url: "{{URL::to('gestionMedica')}}/eliminarExamenLaboratorio/"+id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    type: "get",
                    success: function(data){
                        if(data.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        tabledExamenLaboratorio.api().ajax.reload();
                                        $('#formularioAgregarExamenLab').modal('hide');
                                    }, 2000)
                                },
                            });
                        }
                        if(data.error){
                            swalError.fire({
                                title: 'Error',
                                text: data.error
                            }).then(function(result) {
                                tabledExamenLaboratorio.api().ajax.reload();
                                $('#formularioAgregarExamenLab').modal('hide');
                            });
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            }
        });
        
    }

   function editarExamenLaboratorio(idFormulario){
        id = idFormulario;
        $.ajax({
            url: "{{URL::to('gestionMedica')}}/editarExamenLaboratorio/"+id,
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
                        tabledExamenLaboratorio.api().ajax.reload();
                        $('#formularioAgregarExamenLab').modal('hide');
                    });
                }else{

                    formulario = data.datos;
                    console.log(data);
                    if(formulario.length !== 0){

                        if(formulario.id != null){
                            $("#idformExamenLab").val(formulario.id).change();
                        }

                        if(formulario.examenes_opciones != null){
                            examenes_opciones = formulario.examenes_opciones.split(','); 
                            $('#examenes_laboratorio').val(examenes_opciones);
                             $('#examenes_laboratorio').selectpicker('refresh');
                        }

                        if(formulario.bioquimicos_sangre != null){
                            enablebioquimicosSangre();
                            bioquimicos_sangre = formulario.bioquimicos_sangre.split(','); 
                            bioquimicos_sangre.forEach(function(valor,indice) {
                                $('input:checkbox[name="bioquimicosSangre[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }

                        if(formulario.bioquimicos_orina != null){
                            enablebioquimicosOrina();
                            bioquimicos_orina = formulario.bioquimicos_orina.split(','); 
                            bioquimicos_orina.forEach(function(valor,indice) {
                                $('input:checkbox[name="bioquimicosOrina[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }
                      
                        if(formulario.gases_elp != null){
                            enablegasesElp();
                            gases_elp = formulario.gases_elp.split(','); 
                            gases_elp.forEach(function(valor,indice) {
                                $('input:checkbox[name="gasesElp[]"][value="' + valor + '"]').prop('checked', true);
                            });

                            if(formulario.fiogases_elp != null){
                                $("#fiogasesElp").val(formulario.fiogases_elp).change();
                            }

                            if(formulario.temperaturagases_elp != null){
                                $("#temperaturagasesElp").val(formulario.temperaturagases_elp);
                            }
                        }
                        
                        if(formulario.perfiles != null){
                            enableperfiles();
                            perfiles = formulario.perfiles.split(','); 
                            perfiles.forEach(function(valor,indice) {
                                $('input:checkbox[name="perfiles[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }
                      
                        if(formulario.liquido != null){
                            enableliquido();
                            liquido = formulario.liquido.split(','); 
                            liquido.forEach(function(valor,indice) {
                                $('input:checkbox[name="liquido[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }

                        if(formulario.hematologicos != null){
                            enablehematologicos();
                            hematologicos = formulario.hematologicos.split(','); 
                            hematologicos.forEach(function(valor,indice) {
                                $('input:checkbox[name="hematologicos[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }

                        if(formulario.hormonales != null){
                            enablehormonales();
                            hormonales = formulario.hormonales.split(','); 
                            hormonales.forEach(function(valor,indice) {
                                $('input:checkbox[name="hormonales[]"][value="' + valor + '"]').prop('checked', true);
                            });
                        }

                         //limpiar zona donde se agregan los antimicrobianos actuales
                         $("#moduloOtrosExamanescopia").empty();

                        if(data.datosExamenOtros != ''){
                            enableotros();
                            datosExamenOtros = data.datosExamenOtros;
                            if(datosExamenOtros.length !== 0){
                                for (i = 0; i < datosExamenOtros.length -1; i++) {
                                    agregarOtrosExamanes();
                                }
                                for (i = 0; i < datosExamenOtros.length; i++) {
                                    $("#id_otros_examenes"+i).val(datosExamenOtros[i].id);
                                    $("#otros_examenes"+i).val( datosExamenOtros[i].examen);
                                }
                            }
                        }else{
                            
                            disableotros();
                        }
                        var largo = $("#examenes_laboratorio").children(':selected').length;
                        $("#examenes_laboratorio_values").val(largo).change();

                        $("#formularioAgregarExamenLab").modal();
                    }
                }	
            },
            error: function(error){
                console.log(error);
            }
        });
        
    }

    function ocultarTodosExamenesLab(){
        disablebioquimicosSangre();
        disablebioquimicosOrina();
        disablegasesElp();
        disableperfiles();
        disableliquido();
        disablehematologicos();
        disablehormonales();
        disableotros();
        $("#btGuardarOtro").prop("disabled", true);
    }

    //Bioquímicos sangre
    function disablebioquimicosSangre(){
        //guardar id_aterior en caso de tenerlo
        $("#detallebioquimicosSangre").hide();
        $("#detallebioquimicosSangre :input").prop("disabled", true);
    }

    function enablebioquimicosSangre(){
        $("#detallebioquimicosSangre").show();
        $("#detallebioquimicosSangre :input").prop("disabled", false);
    }

    
    //Bioquímicos Orina
    function disablebioquimicosOrina(){
        //guardar id_aterior en caso de tenerlo
        $("#detallebioquimicosOrina").hide();
        $("#detallebioquimicosOrina :input").prop("disabled", true);
    }

    function enablebioquimicosOrina(){
        $("#detallebioquimicosOrina").show();
        $("#detallebioquimicosOrina :input").prop("disabled", false);
    }

    
    //Gases y ELP
    function disablegasesElp(){
        //guardar id_aterior en caso de tenerlo
        $("#detallegasesElp").hide();
        $("#detallegasesElp :input").prop("disabled", true);
    }

    function enablegasesElp(){
        $("#detallegasesElp").show();
        $("#detallegasesElp :input").prop("disabled", false);
    }

    //Perfiles
    function disableperfiles(){
        //guardar id_aterior en caso de tenerlo
        $("#detalleperfiles").hide();
        $("#detalleperfiles :input").prop("disabled", true);
    }

    function enableperfiles(){
        $("#detalleperfiles").show();
        $("#detalleperfiles :input").prop("disabled", false);
    }

    //Liquidos
    function disableliquido(){
        //guardar id_aterior en caso de tenerlo
        $("#detalleliquido").hide();
        $("#detalleliquido :input").prop("disabled", true);
    }

    function enableliquido(){
        $("#detalleliquido").show();
        $("#detalleliquido :input").prop("disabled", false);
    }

    //Hematologicos
    function disablehematologicos(){
        //guardar id_aterior en caso de tenerlo
        $("#detallehematologicos").hide();
        $("#detallehematologicos :input").prop("disabled", true);
    }

    function enablehematologicos(){
        $("#detallehematologicos").show();
        $("#detallehematologicos  :input").prop("disabled", false);
    }

    //Hormonales
    function disablehormonales(){
        //guardar id_aterior en caso de tenerlo
        $("#detallehormonales").hide();
        $("#detallehormonales :input").prop("disabled", true);
    }

    function enablehormonales(){
        $("#detallehormonales").show();
        $("#detallehormonales  :input").prop("disabled", false);
    }
    
    //Otros
    function disableotros(){
        //guardar id_aterior en caso de tenerlo
        $("#detalleotros_examenes").hide();
        $("#detalleotros_examenes :input").prop("disabled", true);
    }

    function enableotros(){
        $("#detalleotros_examenes").show();
        $("#detalleotros_examenes  :input").prop("disabled", false);
    }
    


    function detallesExamenesLab(){
        examenes_lab = [];
        examenes_lab = $("#examenes_laboratorio").val(); 
        if(examenes_lab == null){
            ocultarTodosExamenesLab();
            examenes_lab = '';
        }         
       if(examenes_lab.indexOf("0") != -1){
            enablebioquimicosSangre();
       }else{
            disablebioquimicosSangre();
            $('input[name="bioquimicosSangre[]"]').each(function() { 
			this.checked = false; 
		    });
       }
       if(examenes_lab.indexOf("1") != -1){
            enablebioquimicosOrina();
       }else{
            disablebioquimicosOrina();
            $('input[name="bioquimicosOrina[]"]').each(function() { 
                this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("2") != -1){
            enablegasesElp();
       }else{
            disablegasesElp();
            $("#temperaturagasesElp").val("");
            $("#fiogasesElp").val(0).change();
            $('input[name="gasesElp[]"]').each(function() { 
                this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("3") != -1){
            enableperfiles();
       }else{
            disableperfiles();
            $('input[name="perfiles[]"]').each(function() { 
                this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("4") != -1){
            enableliquido();
       }else{
            disableliquido();
            $('input[name="liquido[]"]').each(function() { 
                this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("5") != -1){
            enablehematologicos();
       }else{
            disablehematologicos();
            $('input[name="hematologicos[]"]').each(function() { 
                this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("6") != -1){
            enablehormonales();
       }else{
            disablehormonales();
            $('input[name="hormonales[]"]').each(function() { 
                    this.checked = false; 
            });
       }
       if(examenes_lab.indexOf("7") != -1){
            enableotros();
       }else{
            disableotros();
            counterOtrosExamenes  = 1;
            $("#moduloOtrosExamanescopia").empty();
            $("#id_otros_examenes0").val("");
            $("#otros_examenes0").val("");
            $("#eliminados_otros_examenes").val("");
       }   

    }

    $(function() {

        $("#idExamenLaborario").click(function(){
            if (typeof tabledExamenLaboratorio == 'undefined') {
                generarTablaExamenLaboratorio();
            }
        });

        $( "#examenes_laboratorio" ).change(function() {
            examenes_lab = [];
            examenes_lab = $("#examenes_laboratorio").val(); 

            if(examenes_lab == null){
                examenes_lab = '';

            }else{
                $("#btnExamenesLab").prop("disabled", false);
            }

            detallesExamenesLab();
        });
 
        $("#agregarExamenLaboratorio").click(function() {
            var caso = "{{$caso}}";
            // datosPacienteExamen();
            $("#idCasoExamenLab").val(caso);
            $("#formularioAgregarExamenLab").modal("show");
        });

        $('#formularioAgregarExamenLab').on('shown.bs.modal', function () {
            validarFormularioExamanesLaboratorio();
        });

        $("#formularioAgregarExamenLab").on("hidden.bs.modal", function(){
            // $('#formExamenLab').data('bootstrapValidator').resetForm(true);
            
            $("#idformExamenLab").val("");
            $("#eliminados_otros_examenes").val("");
            ocultarTodosExamenesLab();
            $('input[name="bioquimicosSangre[]"]').each(function() { 
			this.checked = false; 
		    });
            $('input[name="bioquimicosOrina[]"]').each(function() { 
                this.checked = false; 
            });
            $("#temperaturagasesElp").val("");
            $("#fiogasesElp").val(0).change();
            $('input[name="gasesElp[]"]').each(function() { 
                this.checked = false; 
            });
            $('input[name="perfiles[]"]').each(function() { 
                this.checked = false; 
            });
            $('input[name="liquido[]"]').each(function() { 
                this.checked = false; 
            });
            $('input[name="hematologicos[]"]').each(function() { 
                this.checked = false; 
            });
            $('input[name="hormonales[]"]').each(function() { 
                    this.checked = false; 
            });
            counterOtrosExamenes  = 1;
            $("#moduloOtrosExamanescopia").empty();
            $("#id_otros_examenes0").val("");
            $("#otros_examenes0").val("");
            
            $("#examenes_laboratorio").selectpicker("refresh").val('');
		    $("#examenes_laboratorio").selectpicker("refresh"); 
            
            $("#examenes_laboratorio_values").val('');
        });

        $("#examenes_laboratorio").on("change", function(){
            console.log("jaja");
            var largo = $("#examenes_laboratorio").children(':selected').length;
            console.log(largo);
            $("#examenes_laboratorio_values").val(largo).change();
        });

        $("#formExamenLab").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'examenes_laboratorio_values': {
                    trigger: 'change keyup',
                    validators: {
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#examenes_laboratorio_values").val();
                                if (value <= 0) {
                                    return {valid: false, message: "Debe seleccionar al menos un tipo" };
                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },
                'bioquimicosSangre[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'bioquimicosOrina[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'temperaturagasesElp': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'fiogasesElp': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'gasesElp[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'perfiles[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'liquido[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'hematologicos[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'hormonales[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'otros_examenes[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
            }
        }).on('status.field.bv', function(e, data) {
            $("#formExamenLab input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#formExamenLab input[type='submit']").prop("disabled", false);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            swalPregunta.fire({
                title: '¿Desea Guardar este formulario?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    guardarFormExamenLaboratorio($form);
                }
            });
        });
    });


</script>


<style>
    .formulario .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    /* #detallehormonales{
        max-height:168.844px;
    } */
    @media (min-width: 992px){
        #detallehematologicos .help-block{
            width: 100%;
        }
        
        #detallehematologicos .checkbox, 
        #detallebioquimicosSangre .checkbox, 
        #detallehormonales .checkbox, 
        #detallebioquimicosOrina .checkbox,
        #detallegasesElp .checkbox,
        #detalleperfiles .checkbox,
        #detalleliquido .checkbox{            
            width: 33.33333333%;
            float: left;
        }
        #checkboxacidoLactico, #checkboxsangria{
            width: 100% !important;
        }

        #checkboxestrandiol, #checkboxElectroBioquimico, #checkboxLiquido{
            width: 66.66666667% !important;
        }
       
    }
</style>

<div class="formulario panel panel-default">

    <div class="panel-body">
        <legend>Examen Laboratorio</legend>
        <button class="btn btn-primary" id="agregarExamenLaboratorio">Generar Examen</button>
        <br><br>
        <legend>Listado de examenes de laborario</legend>
        <table id="tableExamenLaboratorio" class="table table-striped table-bordered table-hover">
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
{{ Form::close() }}


{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formExamenLab')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCaso', 'id' => 'idCasoExamenLab')) }}
    {{ Form::hidden('idformExamenLab', '', array('id' => 'idformExamenLab')) }}
    {{ Form::hidden('eliminados_otros_examenes', '', array('id' => 'eliminados_otros_examenes')) }}
    <div id="formularioAgregarExamenLab" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"
    style="overflow-y:auto;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>
                <div class="modal-body">
                    <div style="margin-left: auto;">
                        <div class="row">
                            <div class="col-md-12">
                                <legend>Examenes Laboratorio</legend>
                                <div class="col-md-3">
                                    <div class="form-group">
                                    {{ Form::select('examenes_laboratorio[]', array('0' => 'Bioquímicos sangre', '1' => 'Bioquímica Orina', '2'=>'Gases y ELP','3' => 'Perfiles','4' => 'Liquido','5' => 'Hematológicos','6' => 'Hormonales','7' => 'Otros examenes'), null, array('class' => 'form-control selectpicker', 'id' => 'examenes_laboratorio', 'multiple')) }}

                                    {{ Form::text('examenes_laboratorio_values', "0", array('class' => 'form-control ', "id" => "examenes_laboratorio_values", "style" => "height:0px !important; padding:0; border:0px;")) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detallebioquimicosSangre" hidden>
                            <div class="col-md-12">
                                <legend>Bioquímicos sangre</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                          <div class="checkbox">
                                            <label> <input id="glicemia" name="bioquimicosSangre[]" type="checkbox" value="1" title="glicemia" /> <span title="glicemia">Glicemia</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="glucosa" name="bioquimicosSangre[]" type="checkbox" value="2" title="glucosa" /> <span title="glucosa">Glucosa, Curva tolerancia</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="cetonicos" name="bioquimicosSangre[]" type="checkbox" value="3" title="cetonicos" /> <span title="cuerpos cetonicos">Cuerpos cetónicos</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="nitrogeno" name="bioquimicosSangre[]" type="checkbox" value="4" title="nitrogeno" /> <span title="nitrogeno">Nitrógeno Ureico</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="albumina" name="bioquimicosSangre[]" type="checkbox" value="5" title="albumina" /> <span title="albumina">Albumina / Proteínas totales</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="creatinemia" name="bioquimicosSangre[]" type="checkbox" value="6" title="creatinemia" /> <span title="creatinemia">Creatinemia</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="clearence" name="bioquimicosSangre[]" type="checkbox" value="7" title="clearence" /> <span title="clearence">Clearence creatinina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="bilirrubinaTotal" name="bioquimicosSangre[]" type="checkbox" value="8" title="bilirrubinaTotal" /> <span title="bilirrubinaTotal">Bilirrubina Total</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="bilirrubinaDirecta" name="bioquimicosSangre[]" type="checkbox" value="9" title="bilirrubinaDirecta" /> <span title="bilirrubinaDirecta">Bilirrubina Total y directa</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="fosfatasas" name="bioquimicosSangre[]" type="checkbox" value="10" title="fosfatasas" /> <span title="fosfatasas">Fosfatasas alcalinas</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="calcemia" name="bioquimicosSangre[]" type="checkbox" value="11" title="calcemia" /> <span title="calcemia">Calcemia</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="fosfemia" name="bioquimicosSangre[]" type="checkbox" value="12" title="fosfemia" /> <span title="fosfemia">Fosfemia</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="colesterolTotal" name="bioquimicosSangre[]" type="checkbox" value="13" title="colesterolTotal" /> <span title="colesterolTotal">Colesterol Total</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="colesterolHDL" name="bioquimicosSangre[]" type="checkbox" value="14" title="colesterolHDL" /> <span title="colesterolHDL">Colesterol HDL</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="trigliceridos" name="bioquimicosSangre[]" type="checkbox" value="15" title="trigliceridos" /> <span title="trigliceridos">Trigliceridos</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="magnesio" name="bioquimicosSangre[]" type="checkbox" value="16" title="magnesio" /> <span title="magnesio">Magnesio</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="got" name="bioquimicosSangre[]" type="checkbox" value="17" title="got" /> <span title="got">GOT / GP 2X</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ggt" name="bioquimicosSangre[]" type="checkbox" value="18" title="ggt" /> <span title="ggt">GGT</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ck" name="bioquimicosSangre[]" type="checkbox" value="19" title="ck" /> <span title="ck">CK</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ckmb" name="bioquimicosSangre[]" type="checkbox" value="20" title="ckmb" /> <span title="ckmb">CK - MB</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ldii" name="bioquimicosSangre[]" type="checkbox" value="21" title="ldii" /> <span title="ldii">LDII</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="amilasa" name="bioquimicosSangre[]" type="checkbox" value="22" title="amilasa" /> <span title="amilasa">Amilasa</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="acidoUnico" name="bioquimicosSangre[]" type="checkbox" value="23" title="acidoUnico" /> <span title="acidoUnico">Ácido Único</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="proteinaCReactiva" name="bioquimicosSangre[]" type="checkbox" value="24" title="proteinaCReactiva" /> <span title="proteinaCReactiva">Proteina C reactiva</span></label>
                                        </div>
                                        <div class="checkbox" id="checkboxacidoLactico">
                                            <label> <input id="acidoLactico" name="bioquimicosSangre[]" type="checkbox" value="25" title="acidoLactico" /> <span title="acidoLactico">Ácido Láctico</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detallebioquimicosOrina" hidden>
                            <div class="col-md-12">
                                <legend>Bioquímicos Orina</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label> <input id="orinaCompleta" name="bioquimicosOrina[]" type="checkbox" value="1" title="orinaCompleta" /> <span title="orinaCompleta">Orina completa</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="sedimentoOrina" name="bioquimicosOrina[]" type="checkbox" value="2" title="sedimentoOrina" /> <span title="sedimentoOrina">Sedimento de Orina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="nitrogenoUreico" name="bioquimicosOrina[]" type="checkbox" value="3" title="nitrogenoUreico" /> <span title="nitrogenoUreico">Nitrógeno Ureico / 24 hrs</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="acidoUico" name="bioquimicosOrina[]" type="checkbox" value="4" title="acidoUico" /> <span title="acidoUico">Ácido Úrico</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="amilasuria" name="bioquimicosOrina[]" type="checkbox" value="5" title="amilasuria" /> <span title="amilasuria">Amilasuria / 24 hrs</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="glucosuria" name="bioquimicosOrina[]" type="checkbox" value="6" title="glucosuria" /> <span title="glucosuria">Glucosuria</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="proteinuria" name="bioquimicosOrina[]" type="checkbox" value="7" title="proteinuria" /> <span title="proteinuria">Proteinuria</span></label>
                                        </div>
                                        <div class="checkbox" id="checkboxElectroBioquimico">
                                            <label> <input id="electrolitos" name="bioquimicosOrina[]" type="checkbox" value="8" title="electrolitos" /> <span title="electrolitos">Electrolitos (Na, K, Cl)</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row"  id="detallegasesElp" hidden>
                            <div class="col-md-12">
                                <legend>Gases y ELP</legend>
                                <div class="col-md-10">
                                    <div class="col-md-12 pl-0 pr-0">
                                        <div class="col-md-3 pl-0">
                                            {{Form::label('fiogasesElp', "FIO2", array( 'class' => ''))}}
                                            <div class="form-group">
                                                {{Form::select('fiogasesElp', array('21' => '21','24' => '24', '26' => '26', '28' => '28', '32' => '32', '35' => '35', '36' => '36', '40' => '40', '45' => '45', '50' => '50', '60' => '60', '70' => '70-80', '90' => '90-100'), null, array( 'id'=> 'fio1','class' => 'form-control sele','id' => 'fiogasesElp','placeholder' => 'Seleccione')) }}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="col-md-12">
                                                {{Form::label('temperaturagasesElp', "Temp. (°C)", array( 'class' => ''))}}
                                               <div class="form-group">
                                                   {{Form::number('temperaturagasesElp', null, array('step' => '0.1','class' => 'form-control valor', 'min' => '0', 'max' => '50','id' => 'temperaturagasesElp'))}}
                                               </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="checkbox">
                                                <label> <input id="gasesSanguineos" name="gasesElp[]" type="checkbox" value="1" title="gasesSanguineos" /> <span title="gasesSanguineos">Gases Sanguineos</span></label>
                                            </div>
                                            <div class="checkbox">
                                                <label> <input id="elp" name="gasesElp[]" type="checkbox" value="2" title="elp" /> <span title="elp">ELP (Na, K, Cl)</span></label>
                                            </div>                                     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row"  id="detalleperfiles" hidden>
                            <div class="col-md-12">
                                <legend>Perfiles</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label> <input id="lipidico" name="perfiles[]" type="checkbox" value="1" title="lipidico" /> <span title="lipidico">Lipidico</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="hepatico" name="perfiles[]" type="checkbox" value="2" title="hepatico" /> <span title="hepatico">Hepático</span></label>
                                        </div>                                     
                                        <div class="checkbox">
                                            <label> <input id="cardiaco" name="perfiles[]" type="checkbox" value="3" title="cardiaco" /> <span title="cardiaco">Cardiaco</span></label>
                                        </div>                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detalleliquido" hidden>
                            <div class="col-md-12">
                                <legend>Líquido</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label> <input id="fisico" name="liquido[]" type="checkbox" value="1" title="fisico" /> <span title="fisico">Físico - Quimico</span></label>
                                        </div>
                                        <div class="checkbox" id="checkboxLiquido">
                                            <label> <input id="citologico" name="liquido[]" type="checkbox" value="2" title="citologico" /> <span title="citologico">Citológico</span></label>
                                        </div>                                                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detallehematologicos" hidden>
                            <div class="col-md-12">
                                <legend>Hematológicos</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label> <input id="hemograma" name="hematologicos[]" type="checkbox" value="1" title="hemograma" /> <span title="hemograma">Hemograma VHS</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="plaquetas" name="hematologicos[]" type="checkbox" value="2" title="plaquetas" /> <span title="plaquetas">Rto. Plaquetas</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="protrombina" name="hematologicos[]" type="checkbox" value="3" title="protrombina" /> <span title="protrombina">T. Protrombina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="hematocrito" name="hematologicos[]" type="checkbox" value="4" title="hematocrito" /> <span title="hematocrito">Hematocrito/Hemoglobina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="linfocitos" name="hematologicos[]" type="checkbox" value="5" title="linfocitos" /> <span title="linfocitos">Rto. Linfocitos</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ttpa" name="hematologicos[]" type="checkbox" value="6" title="ttpa" /> <span title="ttpa">TTPA</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="hematologico" name="hematologicos[]" type="checkbox" value="7" title="hematologico" /> <span title="hematologico">Perfil Hematológico (histograma)</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="eosinof" name="hematologicos[]" type="checkbox" value="8" title="eosinof" /> <span title="eosinof">Eosinof. Nasal</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="dimero" name="hematologicos[]" type="checkbox" value="9" title="dimero" /> <span title="dimero">Dimero D</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="reticulosis" name="hematologicos[]" type="checkbox" value="10" title="reticulosis" /> <span title="reticulosis">Rto. Reticulosis</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="vhs" name="hematologicos[]" type="checkbox" value="11" title="vhs" /> <span title="vhs">VHS</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="leucocitos" name="hematologicos[]" type="checkbox" value="12" title="leucocitos" /> <span title="leucocitos">Rto. Leucocitos</span></label>
                                        </div>
                                        <div class="checkbox" id="checkboxsangria">
                                            <label> <input id="sangria" name="hematologicos[]" type="checkbox" value="13" title="sangria" /> <span title="sangria">Tpo. de Sangría (lvy)</span></label>
                                        </div>
                                 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detallehormonales" hidden>
                            <div class="col-md-12">
                                <legend>Hormonales</legend>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label> <input id="t3" name="hormonales[]" type="checkbox" value="1" title="t3" /> <span title="t3">T3</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="insulina" name="hormonales[]" type="checkbox" value="2" title="insulina" /> <span title="insulina">Insulina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="foliculo" name="hormonales[]" type="checkbox" value="3" title="foliculo" /> <span title="foliculo">H. Foliculo Estimulante</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="t4" name="hormonales[]" type="checkbox" value="4" title="t4" /> <span title="t4">T4</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="insulina" name="hormonales[]" type="checkbox" value="5" title="insulina" /> <span title="insulina">Curva Insulina</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="luteinizante" name="hormonales[]" type="checkbox" value="6" title="luteinizante" /> <span title="luteinizante">ll. Luteinizante</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="ts11" name="hormonales[]" type="checkbox" value="7" title="ts11" /> <span title="ts11">TS11</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="progesterona" name="hormonales[]" type="checkbox" value="8" title="progesterona" /> <span title="progesterona">Progesterona</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="beta" name="hormonales[]" type="checkbox" value="9" title="beta" /> <span title="beta">Beta - HCG</span></label>
                                        </div>
                                        <div class="checkbox">
                                            <label> <input id="prolactina" name="hormonales[]" type="checkbox" value="10" title="prolactina" /> <span title="prolactina">Prolactina</span></label>
                                        </div>
                                        <div class="checkbox" id="checkboxestrandiol">
                                            <label> <input id="estrandiol" name="hormonales[]" type="checkbox" value="11" title="estrandiol" /> <span title="estrandiol">Estrandiol</span></label>
                                        </div>
                                                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="detalleotros_examenes" hidden>
                            <div class="col-md-12">
                                <legend>Otros exámenes</legend>
                                <div class="col-md-10">
                                    <div class="col-md-7 pl-0 pr-0" id="moduloOtrosExamanes">
                                        <div class="form-group col-md-9 pl-0"> 
                                            <div class="examenesImagenes"> 
                                                {{Form::hidden('id_otros_examenes[]', null, array('id' => 'id_otros_examenes0', 'class' => 'form-control'))}}
                                                {{Form::text('otros_examenes[]', null, array('id' => 'otros_examenes0', 'class' => 'form-control'))}}
                                            </div> 
                                        </div> 
                                        <div class="col-md-3 text-right btnAgregarOtrosExamanes">
                                            <button type="button" class="btn btn-primary agregarOtrosExamanes" onclick="agregarOtrosExamanes()">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12 moduloOtrosExamanescopia pl-0 pr-0" id="moduloOtrosExamanescopia"></div>   
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnExamenesLab', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}


