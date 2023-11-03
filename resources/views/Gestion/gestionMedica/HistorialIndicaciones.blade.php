<br>
<div>
    <legend>Historial indicaciones</legend>
    <table id="tablaHistoralIndicaciones" class="table table.condensed table-hover">
        <thead>
            <tr style="background:#399865">
                <th style="width: 20% !important">DATOS INDICACIÓN</th>
                <th style="width: 60% !important">RESUMEN</th>
                <th style="width: 20% !important">OPCIONES</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarIndicaciones')) }}
    {{ Form::hidden('idIndicacion', '', array('class' => 'idIndicacion', 'id' => 'idIndicacion_')) }}
    {{ Form::hidden('idCaso_', '', array('class' => 'idCaso_', 'id' => 'idCasoFormIndicacion_')) }}
    <div id="formularioEditarIndicacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Formulario Agregar Indicación</h4>
                </div>
                <div class="modal-body">
                    {{-- <div class="formulario" style="overflow-y: scroll;     height: 550px;"> --}}
                        @include('Gestion.gestionMedica.formularioEditarIndicacionMedica')
                    {{-- </div> --}}
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Actualizar', array('id' => 'btnActualizarIndicaciones', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}


@include('Gestion.gestionMedica.ComentariosIndicacionMedica.modalGestionarComentario')
<br>
<script>
    function validarFormularioEditarIndicacion(){
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'fecha_emision_');
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'fecha_vigencia_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'tipo_reposo_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'grados_semisentado_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'tipo_via_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'detalle_via_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'tipo_consistencia_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'detalle_consistencia_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'tipos_item_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'detalle_tipo_otro_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'horas_signos_vitales_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'horas_hemoglucotest_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'oxigeno_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'sueros_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'suero_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'mililitro_');     
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'padua_');       
        $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'caprini_');    

        nombre_farmaco_ = document.getElementsByName('nombre_farmaco_[]');
        if(nombre_farmaco_.length > 1){
            $('#formEditarIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco_[]', true);  
            $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'nombre_farmaco_[]');   
        }else{
            $('#formEditarIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco_[]', false);  
        }

    }
    
    function cargarIndicaciones(){
        var caso = "{{$caso}}";
        tableIndicacionesMedicas = $("#tablaHistoralIndicaciones").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "destroy":true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/"+caso+"/cargarIndicaciones",
                // data: {"caso": "{{ $caso }}"},
                type: 'GET'
            },
            "autoWidth": false,
            "columns": [
                { "width": 20, "targets": 0 },
                { "width": 60, "targets": 1 },
                { "width": 20, "targets": 2 }
            ],
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            }
        });
    }

    function cargarIndicacion(caso,id){
        var idcaso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+id+"/cargarIndicacionMedica",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.indicacion){
                    var tipo_reposo = (data.indicacion.tipo_reposo) ? data.indicacion.tipo_reposo : '';
                    var otro_reposo = (data.indicacion.otro_reposo) ? data.indicacion.otro_reposo : '';
                    var volumen = (data.indicacion.volumen) ? data.indicacion.volumen : '';
                    var grados_semisentado = (data.indicacion.grados_semisentado) ? data.indicacion.grados_semisentado : '';
                    var tipo_via = (data.indicacion.tipo_via) ? data.indicacion.tipo_via : '';
                    var detalle_via = (data.indicacion.detalle_via) ? data.indicacion.detalle_via : '';
                    var tipo_consistencia = (data.indicacion.tipo_consistencia) ? data.indicacion.tipo_consistencia : '';
                    var detalle_consistencia = (data.indicacion.detalle_consistencia) ? data.indicacion.detalle_consistencia : '';
                    var tipos_reposo = (data.tipos_reposo) ? data.tipos_reposo : '';
                    var array_reposos = [];
                    var detalle_tipo_otro = '';
                    tipos_reposo.forEach(function(element,index,tipos_reposo) {
                        if(element){
                            if(element.tipo == "9"){  
                                //Si el elemento es de tipo 9 (OTRO), se añade el comentario de este                               
                                detalle_tipo_otro = element.detalle_tipo;
                            }
                            array_reposos.push(element.tipo);
                        }
                    });
                    var tipo_via = (data.indicacion.tipo_via) ? data.indicacion.tipo_via : '';
                    var horas_signos_vitales = (data.indicacion.horas_signos_vitales) ? data.indicacion.horas_signos_vitales : '';
                    var detalle_signos_vitales = (data.indicacion.detalle_signos_vitales) ? data.indicacion.detalle_signos_vitales : '';
                    var horas_hemoglucotest = (data.indicacion.horas_hemoglucotest) ? data.indicacion.horas_hemoglucotest : '';
                    var detalle_hemoglucotest = (data.indicacion.detalle_hemoglucotest) ? data.indicacion.detalle_hemoglucotest : '';
                    var oxigeno = (data.indicacion.oxigeno) ? data.indicacion.oxigeno : '';
                    var sueros = (data.indicacion.sueros) ? data.indicacion.sueros : false;
                    var suero = (data.indicacion.suero) ? data.indicacion.suero : '';
                    var mililitro = (data.indicacion.mililitro) ? data.indicacion.mililitro : '';
                    var str_atencion = data.indicacion.atencion_terapeutica;
                    var atencion_terapeutica = (str_atencion) ? str_atencion.split(",") : '';
                    var cant_atenciones = (atencion_terapeutica) ? atencion_terapeutica.length : 0;
                    $("[name='atencion_terapeutica_[]']").each(function(){
                        var valor = $(this).val();
                        var atencion_ = atencion_terapeutica.indexOf(valor);
                        if(atencion_ != -1 ){
                            var atencion = atencion_ + 1;
                            if ($(this).is(':not(:checked)')){
                                $(this).prop('checked', true).change();
                            }
                        }
                    });
                    var fecha_emision = (data.indicacion.fecha_emision) ? moment(data.indicacion.fecha_emision).format('DD-MM-YYYY HH:mm') : '';
                    var fecha_vigencia = (data.indicacion.fecha_vigencia) ? moment(data.indicacion.fecha_vigencia).format('DD-MM-YYYY HH:mm') : '';
                    
                    var rellenar_padua_caprini = (data.pauda_caprini > 0) ? false : true;
                    var padua = (data.indicacion.padua) ? data.indicacion.padua : false;
                    var caprini = (data.indicacion.caprini) ? data.indicacion.caprini : false;
                    
                    var farmacos = (data.farmacos) ? data.farmacos : '';
                    var comentarios = (data.comentarios) ? data.comentarios : '';

                    $("#idIndicacion_").val(id);
                    $("#idCasoFormIndicacion_").val(idcaso);
                    $("#tipo_reposo_").val(tipo_reposo).change();
                    $("#otro_reposo_").val(otro_reposo).change();
                    $("#grados_semisentado_").val(grados_semisentado).change();
                    $("#tipo_via_").val(tipo_via).change();
                    $("#detalle_via_").val(detalle_via).change();
                    $("#tipo_consistencia_").val(tipo_consistencia).change();
                    $("#detalle_consistencia_").val(detalle_consistencia).change();
                    $("#tipos_").selectpicker('val',array_reposos);                
                    $('#tipos_').selectpicker('refresh');
                    $('#tipos_').change();
                    var tipos = $("#tipos_").selectpicker('val');
                    detalle = document.getElementById("opcion_otro_tipo_tipo_");

                    if(tipos == null || tipos.indexOf('9') != -1){
                        detalle.style.display='block';
                        $("#detalle_tipo_otro_").val(detalle_tipo_otro).change();
                    }else{
                        detalle.style.display='none';
                        $("#detalle_tipo_otro_").val('');
                    }
                    $("#volumen_").val(volumen).change();
                    $("#horas_signos_vitales_").val(horas_signos_vitales).change();
                    $("#detalle_signos_vitales_").val(detalle_signos_vitales).change();
                    $("#horas_hemoglucotest_").val(horas_hemoglucotest).change();
                    $("#detalle_hemoglucotest_").val(detalle_hemoglucotest).change();
                    $("#oxigeno_").val(oxigeno).change();
                    if(sueros == true){
                        $("[name='sueros_'][value='si']").prop('checked', true).change();
                        $("#suero_").val(suero).change();
                        $("#mililitro_").val(mililitro).change();
                        $(".listado_suero_").removeClass("hidden");
                    }else{
                        $("[name='sueros_'][value='no']").prop('checked', true).change();
                        $(".listado_suero_").addClass("hidden");
                    }
                    $("#fecha_emision_").val(fecha_emision).change();
                    //.change();
                    $("#fecha_vigencia_").val(fecha_vigencia).change();
                    //.change();

                    farmacos.forEach(function(element,index) {
                        var id_farmaco = (element["id"]) ? element["id"] : '';
                        var nombre_farmaco = element["id_farmaco"];
                        var via_administracion = element["via_administracion"];
                        var intervalo_farmaco = element["intervalo_farmaco"];
                        var detalle_farmaco = element["detalle_farmaco"];
                        if(index == 0){
                            $("#id_farmaco_0").val(id_farmaco);
                            $("#nombre_farmaco_0").val(nombre_farmaco).change();
                            $("#via_administracion_0").val(via_administracion).change();
                            $("#intervalo_farmaco_0").val(intervalo_farmaco).change();
                            $("#detalle_farmaco_0").val(detalle_farmaco).change();
                        }else if(index > 0){
                            var original = $("#farmacosExtras_");
                            var clone = original.clone();
                            clone.attr('id', 'farmacosExtras_'+index);
                            $("[name='id_farmaco_[]']",clone).attr({'data-id': index, 'id': 'id_farmaco_'+index});
                            $("[name='id_farmaco_[]']",clone).val(id_farmaco);
                            $("[name='nombre_farmaco_[]']",clone).attr({'data-id': index, 'id': 'nombre_farmaco_'+index});
                            $("[name='nombre_farmaco_[]']",clone).val(nombre_farmaco);
                            $("[name='via_administracion_[]']",clone).attr({'data-id': index, 'id': 'via_administracion_'+index});
                            $("[name='via_administracion_[]']",clone).val(via_administracion);
                            $("[name='intervalo_farmaco_[]']",clone).attr({'data-id': index, 'id': 'intervalo_farmaco_'+index});
                            $("[name='intervalo_farmaco_[]']",clone).val(intervalo_farmaco);
                            $("[name='detalle_farmaco_[]']",clone).attr({'data-id': index, 'id': 'detalle_farmaco_'+index});
                            $("[name='detalle_farmaco_[]']",clone).val(detalle_farmaco);
                            
                            html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarFilaFarmaco_('+index+')">-</button></div>';      
                            
                            $("#FarmacosCopia_").append(clone);
                            clone.append(html);

                            //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='nombre_farmaco_[]']"));
                            //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='via_administracion_[]']"));
                            //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='intervalo_farmaco_[]']"));
                        }
                    });

                    if(padua != null || caprini !=null){
                        $(".primera_indicacion_").removeClass("hidden");
                        if(padua == true){
                            $("[name='padua_'][value='si']").prop('checked', true).change();
                        }else{
                            $("[name='padua_'][value='no']").prop('checked', true).change();
                        }

                        if(caprini == true){
                            $("[name='caprini_'][value='si']").prop('checked', true).change();
                        }else{
                            $("[name='caprini_'][value='no']").prop('checked', true).change();
                        }
                    }else{
                        $(".primera_indicacion_").addClass("hidden");
                        // $("[name='padua_'][value='no']").prop('checked', true).change();
                        // $("[name='caprini_'][value='no']").prop('checked', true).change();
                    }

                    if(rellenar_padua_caprini){
                        $(".primera_indicacion_").removeClass("hidden");
                    }

                    comentarios.forEach(function(element,index) {
                        var id_comentario = element["id"];
                        var comentario = element["comentario"];
                        if(index == 0){
                            $("#id_comentario_0").val(id_comentario);
                            $("#campoExtra_0").val(comentario).change();
                        }else if(index > 0){
                            var original = $("#camposExtras_");
                            var clone = original.clone();
                            clone.attr('id','camposExtras_'+index);

                            $("[name='id_comentario_[]']",clone).attr({'data-id': index, 'id': 'id_comentario_'+index});
                            $("[name='id_comentario_[]']",clone).val(id_comentario);
                            
                            $("[name='campoExtra_[]']",clone).attr({'data-id':index,'id':'campoExtra_'+index}).val(index);
                            $("[name='campoExtra_[]']",clone).val(comentario);

                            html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCampoExtra_('+index+')">-</button></div>';

                            $("#clonAgregarCamposExtras_").append(clone);
                            clone.append(html);

                            $('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='campoExtra_[]']"));
                        }
                    });

                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    function ocultarSemisentado_(){
        $("#opcion_grados_semisentado_").hide();
        $("#grados_semisentado_").val("");
    }

    function ocultarOtroReposo_(){
        $("#opcion_otro_reposo_").hide();
        $("#otro_reposo_").val("");
    }

    function ocultarVia_(){
        $("#detalle_via_").val('');
        $("#tipo_via_").val('');
        $("#opcion_otro_via_").hide();
    }

    function ocultarConsistencia_(){
        $("#tipo_consistencia_").val('');
        $("#detalle_consistencia_").val('');
        $("#opcion_otro_consistencia_").hide();
    }

    function ocultarTipo_(){
        $('#tipos_').selectpicker('val', '');
        $('#tipos_').selectpicker('refresh');
        detalle = document.getElementById("opcion_otro_tipo_tipo_");
        detalle.style.display='none';
        $("#detalle_tipo_otro_").val('');
    }

    function ocultarVolumen_(){
        $("#volumen_").val('');
    }

    function verEditarIndicacion(caso,id){
        var caso = "{{$caso}}";

        cargarIndicacion(caso,id);
        // $("#idCasoFormIndicacion_").val(caso);
        $("#formularioEditarIndicacion").modal("show");
    }

    function eliminarIndicacion(id){
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+id+"/eliminarIndicacion",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.exito){
                    swalExito.fire({
                        title: 'Exito!',
                        text: data.exito,
                        didOpen: function() {
                            setTimeout(function() {
                                cargarIndicaciones();
                            }, 2000)
                        },
                    });
                }

                if(data.error){
                    swalError.fire({
                        title: 'Error',
                        text:data.error
                    }).then(function(result) {
                        if (result.isDenied) {
                            cargarIndicaciones();
                        }
                    });
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    $("#tipo_reposo_").on("change", function(){
        if($(this).val() == 2){
            $("#otro_reposo_").val('');
            $("#opcion_otro_reposo_").hide();
            $("#opcion_grados_semisentado_").show();
        }else if($(this).val() == 5){
            $("#grados_semisentado_").val('');
            $("#opcion_grados_semisentado_").hide();
            $("#opcion_otro_reposo_").show();
        }else{
            $("#grados_semisentado_").val('');
            $("#opcion_grados_semisentado_").hide();
            $("#otro_reposo_").val('');            
            $("#opcion_otro_reposo_").hide();
        }
    });

    $("#tipo_via_").on("change", function(){
        if($(this).val() == 5){
            $("#opcion_otro_via_").show();
        }else{
            $("#opcion_otro_via_").hide();
        }
    });

    $("#tipo_consistencia_").on("change", function(){
        if($(this).val() == 5){
            $("#opcion_otro_consistencia_").show();
        }else{
            $("#detalle_consistencia_").val('');
            $("#opcion_otro_consistencia_").hide();
        }
    });

    $('#formularioEditarIndicacion').on('shown.bs.modal', function () {
        validarFormularioEditarIndicacion();
    });

    $("#formularioEditarIndicacion").on("hidden.bs.modal", function(){
        $("#idIndicacion_").val('');
        $('#formEditarIndicaciones').trigger('reset');
        ocultarSemisentado_();
        ocultarOtroReposo_();
        ocultarVia_();
        ocultarConsistencia_();
        ocultarTipo_();
        ocultarVolumen_();

        $(".listado_suero_").addClass("hidden");

        $('#suero_').selectpicker('val', '');
        $('#suero_').selectpicker('refresh');
        
        $("#fecha_emision_").val("");
        $("#fecha_vigencia_").val("");

        $(".primera_indicacion_").addClass("hidden");
        
        $("#clonAgregarCamposExtras_").empty();
        $("#FarmacosCopia_").empty();
    });

    $("input[name='sueros_']").on("change", function() {
		var checkeado = $("input[name='sueros_']:checked").val();
		if(checkeado == 'si'){
            $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'suero_item_');
            $(".listado_suero_").removeClass("hidden");
        }else{
            $(".listado_suero_").addClass("hidden");
        }
	});

    $("#btnActualizarIndicaciones").on("click", function() {
        $("#tipo_reposo_").change();
        $("#grados_semisentado_").change();
        $("#otro_reposo_").change();
        $("#tipo_via_").change();
        $("#detalle_via_").change();
        $("#tipo_consistencia_").change();
        $("#detalle_consistencia_").change();
        $("#tipos_").change();
        $("#detalle_tipo_otro_").change();
        $("#volumen_").change();
        $("#detalle_signos_vitales_").change();

        $("#tipo_regimen_").change();
		var checkeado = $("input[name='sueros_']:checked").val();
		if(checkeado === undefined){
			$("input[name=sueros_][value='no']").change();
		}
        $('#suero_').change();
        //$("#fecha_emision_").change();
        //$("#fecha_vigencia_").change();

        var checkeado_padua = $("input[name='padua_']:checked").val();
		if(checkeado_padua === undefined){
			$("input[name=padua_][value='no']").change();
		}

        var checkeado_caprini = $("input[name='caprini_']:checked").val();
		if(checkeado_caprini === undefined){
			$("input[name=caprini_][value='no']").change();
		}

        $("input[name='campoExtra_[]']").change();
        $("input[name='nombre_farmaco_[]']").change();
        $("input[name='via_administracion_[]']").change();
        $("input[name='intervalo_farmaco_[]']").change();
	});

    $("#tipos_").on("change", function(){
		var largo= $("#tipos_").children(':selected').length;
		$("#tipos_item_").val(largo).change();
		$('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'tipos_item_');
	});

    $("#tipos_").on("changed.bs.select", function(e, clickedIndex, isSelected, oldValue){
        if(clickedIndex == 8){
            detalle = document.getElementById("opcion_otro_tipo_tipo_");
            if(isSelected == true){
                detalle.style.display='block';
            }else{
                detalle.style.display='none';
                $("#detalle_tipo_otro_").val('');
            }
        }
    });

    $("#suero_").on("change", function(){
        var valor = $(this).val();
		$("#suero_item_").val(valor).change();
		$('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'suero_item_');
	});

    $("input[type=radio][name=sueros_").on("change", function(){
        var opcion = $(this).val();
        if(opcion == "si"){
            $(".listado_suero_").removeClass("hidden");
        }else{
            $(".listado_suero_").addClass("hidden");
        }
    });

    $("#gestionIndicaciones").click(function(){
        var tabsIndicacionesMedicas = $("#tabsIndicacionesMedicas").tabs().find(".active");
        tabIM = tabsIndicacionesMedicas[0].id;

        if(tabIM == "tabHistorialIndicacion"){
            cargarIndicaciones();
        }
    });

    $("#idHistoralIndicacion").click(function(){
        cargarIndicaciones();
    });

    $("#formEditarIndicaciones").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {
            tipo_reposo_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            grados_semisentado_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            otro_reposo_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_regimen_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_via_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            detalle_via_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_consistencia_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            detalle_consistencia_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            'tipos_item_': {
                trigger: 'change keyup',
                validators: {
                    callback: {
                        callback: function(value, validator, $field){
                            var cantidad = $("#tipos_item_").val();
                            if (value <= 0) {
                                return {valid: false, message: "Debe seleccionar al menos un tipo" };
                            }else{
                                return true;
                            }
                        }
                    }
                }
            },
            detalle_tipo_otro_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            /* volumen_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            }, */
            sueros_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    },
                    /* callback: {
                        callback: function(value, validator, $field){

                            //Revalidar si es si la parte de seleccioanr suero
                            if(value == "no"){
                                //$(".listado_suero_").addClass("hidden");
                                //$("#suero_").val("");
                            }else{
                                //$(".listado_suero_").removeClass("hidden");
                                //$("#suero_").val("");
                            }
                            
                            return true;
                        }
                    } */
                }
            },
            'suero_item_': {
                trigger: 'change keyup',
                validators: {
                    callback: {
                        callback: function(value, validator, $field){
                            var valor = $("#suero_item_").val();
                            if (valor <= 0 || valor == "") {
                                return {valid: false, message: "Debe seleccionar al menos un suero" };
                            }else{
                                return true;
                            }
                        }
                    }
                }
            },
            'nombre_farmaco_[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            }, /*
            'via_administracion_[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            },
            'intervalo_farmaco_[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            }, */
            fecha_emision_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    },
                    remote:{
						data: function(validator){
							return {
								caso: validator.getFieldElements('idCaso_').val(),
                                indicacion: $('#idIndicacion_').val(),
                                fecha_vigencia: $('#fecha_vigencia_').val()
							};
						},
						url: "{{ URL::to("/validarFechaIndicacionActualizar") }}"
					},
                    callback: {
                        callback: function(value, validator, $field) {
                            var esValidao=validarFormatoFechaHora(value);
                            if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};

                            if( $("#fecha_vigencia_").val() == "") return true;
                                
                            var esMenor=compararFechaIndicacion(value,$("#fecha_vigencia_").val())
                            if(!esMenor) return {valid: false, message: "Fecha debe ser menor a fecha de vigencia"};

                            return true;
                        }
                    }
                }
            },
            fecha_vigencia_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    },
                    callback: {
                        callback: function(value, validator, $field) {
                            var esValidao=validarFormatoFechaHora(value);
                            if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};

                            if( $("#fecha_emision_").val() == "") return true;

                            var esMenor=compararFechaIndicacion($("#fecha_emision_").val(),value)
                            if(!esMenor) return {valid: false, message: "Fecha debe ser mayor a fecha de emisión"};
                                
                            return true;
                        }
                    }
                }
            }/* ,
            'campoExtra_[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            } */
        }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        bootbox.confirm("<h4>¿Está seguro de actualizar esta indicación?</h4>", function(result) {

            if(result){
                $("#btnActualizarIndicaciones").attr('disabled', 'disabled');
                var $form = $(evt.target);
                // swalCargando.fire({});
                $.ajax({
                    url: "{{URL::to('/gestionMedica')}}/editarIndicacionMedica",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    dataType: "json",
                    data: $form .serialize(),
                    async: false,
                    success: function(data){
                        if(data.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        $("#formularioEditarIndicacion").modal('hide');
                                        cargarIndicaciones();
                                    }, 2000)
                                },
                            });
                        }
                        if(data.error){
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            }).then(function(result) {
                                if (result.isDenied) {
                                }
                            });
                        }

                        if(data.info){
                            swalInfo.fire({
                                title: 'Información',
                                text:data.info
                            }).then(function(result) {
                                if (result.isDenied) {
                                }
                            });
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            }
        });
    });

    $('.dtp_fechas_').datetimepicker({
        format: 'DD-MM-YYYY HH:mm',
        locale: 'es'
    }).on('dp.change', function (e) {
        $(this).change();
    });

    var contCE_ = 1;
    $(".agregarCamposExtras_").click(function(){
        var original = $("div#camposExtras_");
        var clone = original.clone();
        clone.attr('id', 'camposExtras_'+contCE_);
        $("[name='campoExtra_[]']",clone).attr({'data-id':contCE_,'id':'campoExtra_'+contCE_}).val(contCE_);
        $("[name='campoExtra_[]']",clone).val('');

        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCampoExtra_('+contCE_+')">-</button></div>'; 

        original.parent().find("#clonAgregarCamposExtras_").append(clone);
        clone.append(html);

        $('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='campoExtra_[]']"));
        contCE_++;
    });

    function eliminarCampoExtra_(posicion){
        var fila = document.getElementById("camposExtras_"+posicion);
        fila.remove();
    }

    var contS_ = 1;
    $(".agregarFarmacosExtras_").click(function(){
        var original = $("div#farmacosExtras_");
        var clone = original.clone();
        clone.attr('id', 'farmacosExtras_'+contS_);
        $("[name='id_farmaco_[]']",clone).attr({'data-id': contS_, 'id': 'id_farmaco_'+contS_});
        $("[name='id_farmaco_[]']",clone).val('');
        $("[name='nombre_farmaco_[]']",clone).attr({'data-id': contS_, 'id': 'nombre_farmaco_'+contS_});
        $("[name='nombre_farmaco_[]']",clone).val('');
        $("[name='via_administracion_[]']",clone).attr({'data-id': contS_, 'id': 'via_administracion_'+contS_});
        $("[name='via_administracion_[]']",clone).val('');
        $("[name='intervalo_farmaco_[]']",clone).attr({'data-id': contS_, 'id': 'intervalo_farmaco_'+contS_});
        $("[name='intervalo_farmaco_[]']",clone).val('');
        $("[name='detalle_farmaco_[]']",clone).attr({'data-id': contS_, 'id': 'detalle_farmaco_'+contS_});
        $("[name='detalle_farmaco_[]']",clone).val('');
        
        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarFilaFarmaco_('+contS_+')">-</button></div>';      
        
        original.parent().find("#FarmacosCopia_").append(clone);
        clone.append(html);

        //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='nombre_farmaco_[]']"));
        //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='via_administracion_[]']"));
        //$('#formEditarIndicaciones').bootstrapValidator('addField', clone.find("[name='intervalo_farmaco_[]']"));
        contS_++;
    });

    function eliminarFilaFarmaco_(position){
        var fila = document.getElementById("farmacosExtras_"+position);
        fila.remove();
       
     nombre_farmaco_ = document.getElementsByName('nombre_farmaco_[]');
        if(nombre_farmaco_.length > 1){
            $('#formEditarIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco_[]', true);  
            $('#formEditarIndicaciones').bootstrapValidator('revalidateField', 'nombre_farmaco_[]');   
        }else{
            $('#formEditarIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco_[]', false);  
        }
    }

    function validarFechaIndicacionX(caso,fecha){
        $.ajax({
            url: "{{URL::to('/')}}/validarFechaIndicacionActualizar",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            data: {
                caso: caso, 
                fecha_emision_: fecha, 
                fecha_vigencia: $("#fecha_vigencia_").val(), 
                indicacion: $("#idIndicacion_").val()
            },
            dataType: "json",
            success: function(data){
                if(data.valid == false){
                    swalInfo.fire({
                        title: 'Información',
                        text: data.message,
                        allowOutsideClick: false
                    });
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    $("#fecha_emision_").on("dp.change", function(e){
        var caso = $("#idCasoFormIndicacion_").val();
        var fecha = $(this).val();
        var indicacion = $("#idIndicacion_").val();
        if(fecha && e.oldDate != null){
            var existeRegistro=validarFechaIndicacionX(caso,fecha,indicacion);
        }
        $("#fecha_vigencia_").change();
    });

    $("#fecha_vigencia_").on("dp.change", function(){
        $("#fecha_emision_").change();
    });


    //Comentarios 
    function cargarComentarios(id){
        $("#id_indicacion_").val(id);
        
        tableComentariosIndicacion = $("#tablaComentariosIndicacion").dataTable({
            "iDisplayLength": 5,
            "order": [[ 0, "desc" ]],
            "ordering": true,
            "searching": true,
            "destroy":true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/"+id+"/cargarComentariosIndicacion",
                type: 'GET'
            },
            "autoWidth": false,
            "columns": [
                { "width": 60, "targets": 0 },
                { "width": 20, "targets": 1 }
            ],
            "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': [ 0 ] }
            ],
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            }
        });
    }

    function gestionarComentariosIndicacion(id){
        $("#modalGestionarComentarios").modal("show");
        cargarComentarios(id);
    }

</script>