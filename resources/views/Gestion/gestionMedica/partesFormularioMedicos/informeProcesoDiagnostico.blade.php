<style>
    #hr_menos_separacion{
        margin-top: 0px;
        margin-bottom: 10px;
    }
</style>

<br>
<div class="panel panel-default">
      <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <legend>Informe proceso diagnóstico</legend>
                    <div class="col-md-12 pl-0 pr-0">
                        <div class="col-md-2 pl-0 pr-0">
                            <a href="#" class="btn btn-primary" id="btnInformeProceso">Generar informe</a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <legend>Listado de informes proceso diagnóstico</legend>
            <table id="tablaInformeProceso" class="table table-striped table-bordered table-hover" width="100%">
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




{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'informesProcesoForm', 'autocomplete' => 'off')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoInformeProceso')) }}
    {{ Form::hidden('idInforme', '', array('class' => 'idInforme', 'id' => 'idInformeProceso')) }}
    <div id="informeProcesoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
                            <h4 class="modal-title" style="display: inline-block;">Informe Proceso Diagnóstico</h4>
                            <button id="btn_pdf_informe" class='btn btn-danger pull-right hidden' style="margin-top: -4px;" type='button'>PDF</button>
                        </div>
                        <div class="panel-body">
                            @include('Gestion.gestionMedica.partials.formInformeProcesoDiagnostico')
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{Form::submit('Guardar', array('id' => 'btnEnviarInformeProceso', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>
    function validar(){
        $("#num_folio").change();
        $("#fecha_informe").change();
        $("#historia_clinica").change();
        $("#rut_beneficiario").change();
        $("#dv_beneficiario").change();
        $("#sexo").change();
        $("#subgrupo_salud_auge").change();
        // $("#diagnostico_informe").change();
        $("#tratamiento_indicaciones").change();
        $("#fundamentos_diagnostico").change();
        $("#fecha_inicio_tratamiento").change();
        $("#problema_saluda_auge").change();
        $("#confirmacion_auge").change();
        $("#especialidad").change();
    }

    function generarTablaInformeProcesoDiagnostico() {
        let caso = "{{$caso}}";
        tableInformeProceso = $("#tablaInformeProceso").DataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/listarInformesProcesoDiagnostico/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            // destroy: true
        });
    }

    function datosPacienteInforme(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/infoPacienteInforme",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                var info = data.infoInforme;
                if(info){ 
                    $("#establecimiento").text(info.nombre_establecimiento);
                    $("#nombre_servicio_salud").text(info.servicio_salud);
                    $("#unidad").text(info.nombre_unidad);
                    $("#nombre_paciente").text(info.nombre_paciente);
                    $("#rut_paciente").text(info.rut_paciente);
                    $("#sexo").val(info.sexo_paciente).change();
                    $("#fecha_nacimiento").text(info.fecha_nacimiento);
                    $("#edad").text(info.edad + ' año(s)');
                    var diagnosticos = info.diagnosticos;
                    diagnosticos.forEach(function(element,index) {
                        var diagnostico = element["diagnostico"];
                        $("#diagnostico_informe").append(diagnostico + '<br>');
                    });
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });    
    }

    function guardarInforme($form){
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/agregarInformeProcesoDiagnostico",
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
                                recargarTabla();
                                $("#informeProcesoModal").modal('hide');
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

                if(data.errores){
                    let ul = "";
                    ul = "<ul style='text-align:left'>";
                    $.each(data.errores, function(key, value) {
                        ul += "<li style='list-style: none;'>"+value+"</li>";
                    });
                    ul += "</ul>";
                    swalError.fire({
                        title: 'Error',
                        html: ul
                    });
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    function editarInforme(id){
        let caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/editarInformeProcesoDiagnostico/"+id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            success: function(data){
                var datos_informe = data.informe;
                if(datos_informe){
                    datosPacienteInforme();
                    $("#idInformeProceso").val(datos_informe.id);
                    $("#btn_pdf_informe").removeClass("hidden");
                    $("#idCasoInformeProceso").val(caso);
                    $("#num_folio").val(datos_informe.num_folio);
                    $("#fecha_informe").val(moment(datos_informe.fecha_informe).format('DD-MM-YYYY HH:mm'));
                    $("#especialidad").val(datos_informe.especialidad);
                    $("#historia_clinica").val(datos_informe.historia_clinica);
                    $("#rut_beneficiario").val(datos_informe.rut_beneficiario);
                    $("#dv_beneficiario").val(datos_informe.dv_beneficiario);
                    $("#problema_saluda_auge").val(datos_informe.problema_saluda_auge);
                    
                    if(datos_informe.confirmacion_auge){
                        $("input[name=confirmacion_auge][value='si']").prop("checked",true);
                    }else if(datos_informe.confirmacion_auge == false){
                        $("input[name=confirmacion_auge][value='no']").prop("checked",true);
                    }else{  
                        $("input[name=confirmacion_auge][value='no']").prop("checked",true);
                    }

                    $("#subgrupo_salud_auge").val(datos_informe.subgrupo_salud_auge);
                    $("#fundamentos_diagnostico").val(datos_informe.fundamentos_diagnostico);
                    $("#tratamiento_indicaciones").val(datos_informe.tratamiento_indicaciones);
                    $("#fecha_inicio_tratamiento").val(moment(datos_informe.fecha_inicio_tratamiento).format('DD-MM-YYYY HH:mm'));
                    $("#informeProcesoModal").modal();
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }
    
    function eliminarInforme(id){
        swalPregunta.fire({
            title: '¿Desea eliminar el informe?',
            showDenyButton: true,
            confirmButtonText: 'Si',
            denyButtonText: 'No',
        }).then(function(result){
            if(result.isConfirmed){
                $.ajax({
                    url: "{{URL::to('/gestionMedica')}}/eliminarInformeProcesoDiagnostico/"+id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'get',
                    success: function(data){
                        if(data.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        recargarTabla();
                                    }, 2000)
                                },
                            });
                        }
                        if(data.error){
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            }).then(function(result){
                                recargarTabla();
                            });	
                        }

                        if(data.info){
                            swalInfo2.fire({
                                title: 'Información',
                                text: data.info
                            }).then(function(result) {
                                recargarTabla();
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

    function recargarTabla(){
        if(typeof tableInformeProceso == 'undefined'){
            generarTablaInformeProcesoDiagnostico();
        }else{
            tableInformeProceso.ajax.reload();
        }
    }

    function pdfInforme(id){
        window.location.href = "{{url('gestionMedica/pdfInformeProcesoDiagnostico')}}"+"/"+id;
    }

    $("#hipd").click(function(){
        recargarTabla();
    });

    $("#btnInformeProceso").click(function(){
        let caso = "{{$caso}}";
        $("#idCasoInformeProceso").val(caso);
        datosPacienteInforme();
        $("#informeProcesoModal").modal();
    });

    $("#btnEnviarInformeProceso").click(function(){
        validar();
    });

    $("#btn_pdf_informe").click(function(){
        var id = $("#idInformeProceso").val();
        pdfInforme(id);
    });

    $("#informeProcesoModal").on('shown.bs.modal', function(){
        validar();
    });

    $("#informeProcesoModal").on("hidden.bs.modal", function(){
        $('#informesProcesoForm').trigger('reset');
        $("#idInformeProceso").val("");
        $("#diagnostico_informe").empty();
        $("#btn_pdf_informe").addClass("hidden");
    });

    $("#rut_beneficiario").on("keyup", function() {
        if($("#rut_beneficiario").val() != ''){
            $("#dv_beneficiario").change();
        }
        if($("#rut_beneficiario").val() != ''  && $("#dv").val() != ''){
            $("#dv_beneficiario").change();
        }
        if($("#rut_beneficiario").val() == ''  && $("#dv").val() != ''){
            $("#dv_beneficiario").change();
        }
    });

    $("#informesProcesoForm").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {
            num_folio: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            fecha_informe: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Fecha obligatoria"
                    }
                }
            },
            especialidad: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            historia_clinica: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            rut_beneficiario: {
                trigger: 'change keyup',
                validators: {
                    integer: {
                        message: 'Debe ingresar solo números'
                    }
                }
            },
            dv_beneficiario: {
                trigger: 'change keyup',
                validators: {
                    regexp: {
                        regexp: /([0-9]|k)/i,
                        message: 'Dígito verificador no valido'
                    },
                    callback: {
                        callback: function(value){
                            var field_rut = $("#rut_beneficiario");
                            var dv = $("#dv_beneficiario");
                            if(field_rut.val() == '' && dv.val() == ''){
                                return true;
                            }
                            if(field_rut.val() != '' && dv.val() == ''){
                                return {valid: false, message:"Debe ingresar el dígito verificador"};
                            }
                            if(field_rut.val() == '' && dv.val() != ''){
                                return {valid: false, message:"Debe ingresar el run"};
                            }
                            var esValido=esRutValido(field_rut.val(), dv.val());
                            if(!esValido){
                                return {valid: false, message: "Dígito verificador no coincide con el run"};
                            }
                            return true;
                        }
                    }
                }
            },
            sexo: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            subgrupo_salud_auge: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            problema_saluda_auge: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            confirmacion_auge: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            // diagnostico_informe: {
            //     trigger: 'change keyup',
            //     validators: {
            //         notEmpty: {
            //             message: "Campo obligatorio"
            //         }
            //     }
            // },
            tratamiento_indicaciones: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            fundamentos_diagnostico: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            fecha_inicio_tratamiento: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Fecha obligatoria"
                    }
                }
            },
        }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        swalPregunta.fire({
            title: '¿Desea Guardar este formulario?',
            showDenyButton: true,
            confirmButtonText: 'Si',
            denyButtonText: 'No',
        }).then(function(result){
            if(result.isConfirmed){
                $("#btnIndicaciones").attr('disabled', false);
                var $form = $(evt.target);
                guardarInforme($form);
            }
        });
    });

    $('.dtp_fechasInforme').datetimepicker({
        format: 'DD-MM-YYYY HH:mm',
        locale: 'es'
    }).on('dp.change', function (e) {
        $(this).change();
    });
</script>