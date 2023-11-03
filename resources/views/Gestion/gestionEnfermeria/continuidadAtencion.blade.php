<script>
        function guardarFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario){
            console.log(formulario,tabla_actualizar,limpiarDatos,botonFormulario);
            formulario.preventDefault(formulario);
            var $form = $(formulario.target);
	                       $.ajax({
                            url: "{{URL::to('/')}}/addcuidadoAlta",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                
                                botonFormulario.prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    limpiarDatos();
                                    
                                    tabla_actualizar.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tabla_actualizar.api().ajax.reload();
                                }
                            },
                            error: function(error){
                                botonFormulario.prop("disabled", false);
                                console.log(error);
                                tabla_actualizar.api().ajax.reload();
                            }
                        });
	                
        }

        function guargarNuevoTipoFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario){
            formulario.preventDefault(formulario);
            var $form = $(formulario.target);
            $.ajax({
                url: "{{URL::to('/')}}/addaepicrisistipo",
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
                    /* actualizar tabla con atenciones */
                    limpiarDatos();
                    tabla_actualizar.api().ajax.reload();
                }

                if (data.error) {
                    swalError.fire({
                    title: 'Error',
                    text:data.error
                    });
                    tabla_actualizar.api().ajax.reload();
                }

                },
                error: function(error){
                    botonFormulario.prop("disabled", false);
                    console.log(error);
                }
            });	
        }

        function elimintarFormulario(idcuidado,nombreForm,tabla_actualizar){
            $.ajax({
                        url: "{{URL::to('/')}}/eliminarCuidado",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idcuidado,
                            nombreForm:nombreForm
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tabla_actualizar.api().ajax.reload();
                            }

                            if (data.error) {
                                swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    tabla_actualizar.api().ajax.reload();
                                }
                                })
                              
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });	
        }

        function mostrarModal(idCuidado,nombre_formulario,nombre_modal){
            $.ajax({
            url: "{{URL::to('/')}}/obtenerCuidadoAlta/"+idCuidado+"/"+nombre_formulario,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                $("#"+nombre_modal+" .modal-body").html(data.contenido);
			    $("#"+nombre_modal).modal();

            },
            error: function(error){
                console.log(error);
            }
        });	
        }

        function guargarNuevoTipoModal(formulario,tabla_actualizar,boton_formulario,nombre_modal){
            formulario.preventDefault(formulario);
            var $form = $(formulario.target);
                $.ajax({
                url: "{{URL::to('/')}}/addaepicrisistipo",
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
                    didOpen: function() {
                        nombre_modal.modal('hide');
                    },
                    });
                    /* actualizar tabla con atenciones */
                    tabla_actualizar.api().ajax.reload();
                }

                if (data.error) {
                    swalError.fire({
                    title: 'Error',
                    text:data.error
                    });
                    tabla_actualizar.api().ajax.reload();
                }

                },
                error: function(error){
                    boton_formulario.prop("disabled", false);
                    console.log(error);
                }
            });	
        }

        function actualizarFormulario(formulario,tabla_actualizar,nombre_modal,boton_formulario,nuevo_item){
                        formulario.preventDefault();
                         var $form = $(formulario.target);
                         boton_formulario.prop("disabled", true);			
                        $.ajax({
                            url: "{{URL::to('/')}}/modificarPCCuidado",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(res){
                                nombre_modal.modal('hide');

                                swalExito.fire({
                                title: 'Exito!',
                                text: res.exito,
                                });
                                    
                                //actualizar tabla
                                tabla_actualizar.api().ajax.reload();

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                });
                            },
                            complete: function (){
                                boton_formulario.prop("disabled", false);
                            }
                        });	
        }

        function todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto){
            if(data.tipo == -1 && data[0] == true && typeof data.referencia !== 'undefined' && data.referencia != ''){
                    swalPregunta2.fire({
			               html: "Se ha encontrado un tipo de cuidado similar o relacionado. ¿Desea reemplazarlo por el nuevo <span style='color:red;'>"+ item_nombre.val() +"</span> o usar el relacionado <span style='color:red;'>"+data.referencia.tipo+"</span>?"
                        }).then( function(result) {
                            if (result.isDenied) {
                                guargarNuevoTipoModal(formulario,tabla_actualizar,boton_formulario,nombre_modal)
                            }else if (result.isConfirmed) {
                                item_id.val(data.referencia.id);
                                actualizarFormulario(formulario,tabla_actualizar,nombre_modal,boton_formulario);
                            }else{
                               botonFormulario.prop("disabled", false);
                            }

                        });
                 }else if(data.tipo == -1 && data[0] == true && typeof data.referencia === 'undefined' || data.tipo == -1 && data[0] == true && data.referencia == ''){
                        swalPregunta.fire({
			               title: "¿Desea agregar <span style='color:red;'>"+ item_nombre.val() +"</span> como nuevo tipo "+texto+"?"
		            }).then(function(result){
                        if (result.isConfirmed) {
                            guargarNuevoTipoModal(formulario,tabla_actualizar,boton_formulario,nombre_modal)
                           }else if(result.isDenied){
                            boton_formulario.prop("disabled", false);
                           }
                       });
                    }else if(data.tipo > 0){
                        swalPregunta.fire({
                        title: "¿Está seguro de actualizar la información?"
                        }).then(function(result){
                        if (result.isConfirmed) {
                            actualizarFormulario(formulario,tabla_actualizar,nombre_modal,boton_formulario);					
                        }else if (result.isDenied) {
                            boton_formulario.prop("disabled", false);
                        }
                    });
                    }
        }

        function todasLasValidaciones(data,formulario,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto){
                 if(data.tipo == -1 && data[0] == true && typeof data.referencia !== 'undefined' && data.referencia != ''){
                    swalPregunta2.fire({
			               html: "Se ha encontrado un tipo de cuidado similar o relacionado. ¿Desea agregar el nuevo cuidado <span style='color:red;'>"+ item_nombre.val() +"</span> o usar el relacionado <span style='color:red;'>"+data.referencia.tipo+"</span>?"
                        }).then(function(result) {
                            if (result.isDenied) {
                                guargarNuevoTipoFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario);
                            }else if (result.isConfirmed) {
                                item_id.val(data.referencia.id);
                                guardarFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario);
                            }else{
                               botonFormulario.prop("disabled", false);
                            }

                        });
                 }
                 else if(data.tipo == -1 && data[0] == true && typeof data.referencia === 'undefined' || data.tipo == -1 && data[0] == true && data.referencia == ''){
                        swalPregunta.fire({
			               title: "¿Desea agregar <span style='color:red;'>"+ item_nombre.val() +"</span> como nuevo tipo de "+texto+"?"
		            }).then(function(result){
                        if (result.isConfirmed) {
                            guargarNuevoTipoFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario);
                        //si confirma
                        }else if(result.isDenied){
                           botonFormulario.prop("disabled", false);
                        }
                    });
                    }else if(data.tipo > 0){
                    swalPregunta.fire({
			               title: "¿Está seguro de agregar este "+texto+"?"
		            }).then(function(result){
                        if (result.isConfirmed) {
                        guardarFormulario(formulario,tabla_actualizar,limpiarDatos,botonFormulario);
                        }else if(result.isDenied){
                            botonFormulario.prop("disabled", false);
                        }
                         }); 
                    }else if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                   botonFormulario.prop("disabled", false);
                    }
        }

</script>
<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    p {
        font-size: 13px;
    }
    .p-0{
        padding:0;
    }
    .mt-1{
        margin-top:20px;
    }
</style>

<div class="formulario">
  
 
    <div class="panel panel-default">
        <div class="panel-heading panel-info" style="background-color: #bce8f1 !important;">
            <h4>Continuidad de la atención</h4>
        </div>
            <div class="panel-body">
            @include('Gestion.gestionEnfermeria.epicrisis.controlMedico')
            @include('Gestion.gestionEnfermeria.epicrisis.interConsulta')
            @include('Gestion.gestionEnfermeria.epicrisis.examenesPendientes')
            @include('Gestion.gestionEnfermeria.epicrisis.medicamentoAlAlta')
            @include('Gestion.gestionEnfermeria.epicrisis.educacionesRealizadas')
            @include('Gestion.gestionEnfermeria.epicrisis.otros')
  
        </div>
    </div>
    
 

</div>  

