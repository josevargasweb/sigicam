<script>
    function generarNovedades() {
        tableNovedades = $("#tablePCNovedades").dataTable({
            "iDisplayLength": 5,
            "ordering": false,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerNovedades/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": function () {
            }
        }); 
    }

    function validarNovedad(idfila){
        if($("#novedad"+idfila).val() == ""){
            $('#novedad'+idfila).focus($("#novedad"+idfila).css({
                'border': '1px solid #a94442'
            }));
            $("#errorNovedad"+idfila).html('Debe Ingresar la novedad');
        }else{
            $("#errorNovedad"+idfila).html('');
            $('#novedad'+idfila).focus($("#novedad"+idfila).css({
                'border': '1px solid #ccc'
            }));
        }   
    }

    function eliminarNovedad(idnovedad) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar esta novedad?</h4>",				
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarNovedad",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idnovedad
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            console.log(data);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tableNovedades.api().ajax.reload();
                            }

                            if (data.error) {
                                swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    tableNovedades.api().ajax.reload();
                                }
                                })
                              
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });				
                }else{
                    tableNovedades.api().ajax.reload();
                }				
            }
        }); 
    }

    function modificarNovedad(idnovedad, idfila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarNovedad",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idnovedad,
                            novedad: $("#novedad"+idfila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tableNovedades.api().ajax.reload();
                            }

                            if (data.error) {
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    }).then(function(result) {
                                    if (result.isDenied) {
                                        tableNovedades.api().ajax.reload();
                                    }
                                    })
                        
                            }

                            if(data.errores){
                                imprimirErroresEditarNovedad(data.errores);
                                $("#erroresNovedadModal").modal("show");
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });				
                }else{
                    tableNovedades.api().ajax.reload();
                }				
            }
        });  
    }

    function imprimirErroresEditarNovedad (msg) {
        $(".imprimir-mensajes-novedad").find("ul").html('');
        $(".imprimir-mensajes-novedad").css('display','block');
        $.each( msg, function( key, value ) {
            $(".imprimir-mensajes-novedad").find("ul").append("<div style='display: flex'><i class='glyphicon glyphicon-remove' style='color: #a94442;'></i><div style='margin-left: 10px'><h4>"+value+"</h4></div></div>");
        });
    }

    function cargarVistaNovedades(){
        if (typeof tableNovedades !== 'undefined') {
            tableNovedades.api().ajax.reload();
        }else{
            generarNovedades();
        }
    }

    $(document).ready(function() {

        $( "#planificacion" ).click(function() {
            var tabsPlanificacionCuidados = $("#tabsPlanificacionCuidados").tabs().find(".active");
            tabPC = tabsPlanificacionCuidados[0].id;

            if(tabPC == "2p"){
                console.log("tabPC atencion enfermeria: ", tabPC);
                cargarVistaIndicacionMedica();
            }
            
        });

        $( "#pN" ).click(function() {
            cargarVistaNovedades();
        });

        $("#PCNovedades").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                novedad: {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una novedad'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({				
                message: "<h4>¿Está seguro de agregar esta novedad?</h4>",				
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
                            url: "{{URL::to('/gestionEnfermeria')}}/addNovedades",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    tableNovedades.api().ajax.reload();
                                    $("#novedad").val('');
                                    $('#PCNovedades').bootstrapValidator('revalidateField', 'novedad');
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
                    }			
                }
            });
            $("#btnNovedad").attr("disabled", false);
        });

        
    }); 

</script>

<style>
    #turnos thead  {
        color: #032c11 !important;
        background-color: #1E9966;
        text-align: center;
        border: 3px !important;
    }

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'PCNovedades')) }}

{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}

    <div class="formulario" style="">
        <br>
        
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>Novedades:</h4>
            </div>
            <div class="panel-body">
                <legend>Ingresar nueva novedad del paciente</legend>
                <div class="col-md-12">
                    <div class="col-md-4"> NOVEDAD</div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-4"> 
                        <div class="form-group"> {{Form::text('novedad', null, array('id' => 'novedad','class' => 'form-control', 'rows' => '3','style' => 'resize:none'))}} </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnNovedad">Guardar</button>
                    </div>
                </div>

                <br><br>

                <legend>Listado de novedades</legend>
                <table id="tablePCNovedades" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 25%">USUARIO</th>
                            <th style="width: 50%">NOVEDAD</th>
                            <th style="width: 25%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>                          
            </div>
        </div>

    
    </div>
 
{{ Form::close() }}

<div class="modal fade" id="erroresNovedadModal" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Falta Información</h4>
        </div>
        <div class="modal-body">
         <div class="alert alert-danger imprimir-mensajes-novedad" style="display:none">
            <ul></ul>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>