<script>
    $(document).ready( function() {
        $("#escalaMacdemsform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
                edad: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                caidas_previas: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                antecedentes: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                compr_conciencia: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data){
            $("#escalaMacdemsform input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#escalaMacdemsform input[type='submit']").prop("disabled", false); 
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                message: "<h4>¿Está seguro de ingresar la información?</h4>",
                buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result){
                if(result){
                    $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/ingresoEscalaMacdems",
                        headers: {					         
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        success: function(data){
                            if(data.exito) {
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            $form[0].reset();
                                            location.reload();
                                        }, 2000)
                                    },
                                });      			        
                            }

                            if(data.error) {
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });
                            }

                            if(data.info) {
                                swalInfo2.fire({
                                    title: 'Información',
                                    text: data.info
                                }).then(function(result) {
                                    location . reload();
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
        });
    });
</script>