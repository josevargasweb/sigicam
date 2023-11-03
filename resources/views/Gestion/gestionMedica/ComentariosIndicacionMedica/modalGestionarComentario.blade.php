<div id="modalGestionarComentarios" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Gestionar Comentarios de la indicación</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="width: 99%;margin-left: 4px;">
                    {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formularioAgregarEditar', 'autocomplete' => 'off')) }}
                        @include('Gestion.gestionMedica.ComentariosIndicacionMedica.formComentarioIndicacionMedica')
                    {{ Form::close()}}
                </div>
                
                <br><br>
                <legend>Comentarios de la indicación</legend>
                <table id="tablaComentariosIndicacion" class="table table.condensed table-hover">
                    <thead>
                        <tr style="background:#399865; color:white;">
                            <th>COMENTARIO</th>
                            <th style="width: 10%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $("#nuevoComentario").click(function(){
        $("#id_comentario_").val("");
        $("#comentario_indicacion_").val("").change();
        $("#enviarComentario").removeClass('hidden');
        $("#editarComentario").addClass('hidden');
        $("#nuevoComentario").addClass('hidden');
    });

    function editarComentario(id,comentario){
        $("#enviarComentario").addClass('hidden');
        $("#editarComentario").removeClass('hidden');
        $("#nuevoComentario").removeClass('hidden');
        var caso = "{{$caso}}";
        $("#caso_").val(caso);
        // $("#id_indicacion_").val(id);
        $("#id_comentario_").val(id);
        $("#comentario_indicacion_").val(comentario).change();
    }

    function eliminarComentario(id){
        bootbox.confirm("<h4>¿Está seguro de eliminar esta comentario?</h4>", function(result) {
            if(result){
                $.ajax({
                    url: "{{URL::to('/gestionMedica')}}/"+id+"/eliminarComentarioAgregado",
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
                                        cargarComentarios(data.id);
                                        $("#comentario_indicacion_").val("");
                                        $("#enviarComentario").removeClass('hidden');
                                        $("#editarComentario").addClass('hidden');
                                        $("#nuevoComentario").addClass('hidden');
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
                                    // cargarIndicaciones();
                                }
                            });
                        }
                    },
                    error: function(error){
                        console.log("error: ", error);
                    }
                });
            }
        });
    }

    $("#modalGestionarComentarios").on("hidden.bs.modal", function(){
        $('#formularioAgregarEditar').trigger('reset');
        $("#enviarComentario").removeClass('hidden');
        $("#editarComentario").addClass('hidden');
        $("#nuevoComentario").addClass('hidden');
    });

    $("#enviarComentario").click(function(){
        var caso = "{{$caso}}";
        $("#caso_").val(caso);
        $("#id_comentario_").val("");
        $("#comentario_indicacion_").change();
    });

    $("#formularioAgregarEditar").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {
            comentario_indicacion_: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            }
        }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        var $form = $(evt.target);
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/agregarComentarioIndicacionMedica",
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
                                cargarComentarios(data.id);
                                $("#comentario_indicacion_").val("");
                                $("#enviarComentario").removeClass('hidden');
                                $("#editarComentario").addClass('hidden');
                                $("#nuevoComentario").addClass('hidden');
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
                    console.log(data.error);
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });
</script>