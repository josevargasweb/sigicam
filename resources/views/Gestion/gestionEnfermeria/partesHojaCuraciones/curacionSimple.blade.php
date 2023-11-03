<script>
    function validarDateTabla () {
        $('.curacionSimple').datepicker({
        autoclose: true,
        language: "es",
        format: "dd-mm-yyyy"
        });
    }

    function generarTablaCuracionSimple(){
        tablacuracionsimples = $("#tablacuracionsimple").dataTable({
            "iDisplayLength": 10,
            "ordering": true,
            "searching": true,
            //"bJQueryUI": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerCuracionSimple/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": validarDateTabla
        });
    }

    function eliminarCuracionSimple(idSolicitud) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de eliminar este registro?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarCuracionSimple",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tablacuracionsimples.api().ajax.reload(validarDateTabla, false);
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
                            console.log(error);
                        }
                    });
                }else{
                    tablacuracionsimples.api().ajax.reload(validarDateTabla, false);
                }
            }
        });
    }

    function modificarCuracionSimple(idSolicitud,idFila) {

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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarCuracionSimple",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            tobservaciones: $("#observaciones-"+idFila).val(),
                            tproximacuracion: $("#proximaCuracion-"+idFila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                tablacuracionsimples.api().ajax.reload(validarDateTabla, false);
                            }

                            if (data.error) {
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    tablacuracionsimples.api().ajax.reload(validarDateTabla, false);
                }
            }
        });
    }

function cargarVistaCuracionSimple(){
    if(typeof tablacuracionsimples !== 'undefined') {
        tablacuracionsimples.api().ajax.reload(validarDateTabla, false);
        }else{
            generarTablaCuracionSimple();
        }
}

$(document).ready(function() {
    //limpiarDatos();
    $("#hojaDeCuracion").click(function(){
        var tabsHojaCuraciones = $("#tabsHojaCuraciones").tabs().find(".active");
        tabHC = tabsHojaCuraciones[0].id;

        if(tabHC == "3ch"){
            console.log("tabHC curacion simple: ", tabHC);
            cargarVistaCuracionSimple();
        }
        
    });
    
    $(".dPPcuracion").datepicker({
        autoclose: true,
        language: "es",
        format: "dd-mm-yyyy"
    }).on("changeDate", function(){
        $('#ingresoCuracionSimpleform').bootstrapValidator('revalidateField', 'proximaCuracion');
    });

    $("#3hc").click(function(){
        cargarVistaCuracionSimple();
    });
        $("#ingresoCuracionSimpleform").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                proximaCuracion: {
                    validators:{
                        notEmpty: {
                            message: 'Ingrese proxima fecha de curación'
                        }
                    }
            },
            'observaciones':{
                    validators:{
                        notEmpty: {
                            message: 'Ingrese la observación'
                        }
                    }
            },
            'tipo_curacion': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un tipo de curación'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    tipo_curacion: validator.getFieldElements('tipo_curacion').val()
                                };
                            },
                            url: "{{URL::to("/validar_tipo_curacion")}}"
                        }
                    }
                }
            }
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarHCuracionSimple").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/ingresoHojaCuracionSimple",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                //$("#ingresoCuracionSimpleform").trigger("reset");
                                $("#btnGuardarHCuracionSimple").prop("disabled", false);
                                $("#proximaCuracion").val("").trigger("change");
                                $("#observaciones").val("").trigger("change");
                                $('#ingresoCuracionSimpleform').bootstrapValidator('resetForm', true);

                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    //generarTablaCuracionSimple();
                                    tablacuracionsimples.api().ajax.reload(validarDateTabla, false);

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
                                $("#btnGuardarHCuracionSimple").prop("disabled", false);
                                console.log(error);
                            }
                        });
                    }
                }
            });  
            $("#btnGuardarHCuracionSimple").prop("disabled", false);
        });

    })

</script>

<div class="formulario">
<div class="panel panel-default">
  <div class="panel-heading panel-info">
      <h4>REGISTRO DE CURACIÓN</h4>
  </div>
  <div class="panel-body">
      <div class="col-md-12">
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal bb',  'id' => 'ingresoCuracionSimpleform')) }}
        {{ Form::hidden('idCaso', $caso, array('class' => 'idCaso')) }}
          <legend>Ingresar nueva curación</legend>
          <div class="col-md-12">
              <div class="col-md-10">
                  <div class="form-group">
                    {{Form::label('Tipo curación')}}
                    {{Form::select('tipo_curacion', array('1' => 'Simple', '2' => 'Avanzada'), null, array('id' => 'tipo_curacion', 'class' => 'form-control', 'placeholder' => 'seleccionar'))}}
                  </div>
              </div>
            <div class="col-md-10">
                <div class="form-group">
                    {{Form::label('lbl_observaciones', "Observaciones", array('class' => ''))}}
                    {{Form::textArea('observaciones', null, array('id' => 'observaciones', 'class' => 'form-control', 'rows' => 4))}}
                </div>
            </div>
          </div>
          <div class="col-md-12">
              <div class="col-md-2">
                  {{Form::label('lbl_fecha', "Fecha", array('class' => ''))}}
                  <div class="form-group">
                  {{Form::text('proximaCuracion', null, array('id' => 'proximaCuracion', 'class' => 'form-control dPPcuracion', 'autocomplete'=>'off'))}}
                  </div>
              </div>
          </div>
          <div class="col-md-12">
              <div class="col-md-2">
                  <input  type="submit" name="" id="btnGuardarHCuracionSimple" class="btn btn-primary" value="Guardar">
                  <br>
              </div>
          </div>
      {{ Form::close() }}
      <br><br>
      </div>

  <div class="col-md-12">
    <br><br>
    <legend>Registro de curaciones</legend>
      <table id="tablacuracionsimple" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style="width: 20%">CURACIONES</th>
            <th style="width: 15%">OBSERVACIONES</th>
            <th style="width: 20%">FECHA CURACIÓN</th>
            <th style="width: 20%">FUNCIONARIO RESPONSABLE</th>
            <th style="width: 25%">OPCIONES</th>
          </tr>
        </thead>
      <tbody></tbody>
    </table>
  </div>
  </div>
</div>
</div>
