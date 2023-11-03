<script>
    $(document).ready( function() {
        $("#riesgoCaidaform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
                caidas_previas: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                medicamentos: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'medicamentos[]': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){                                  
                                if($("#medicamentos").val() == '' || $("#medicamentos").val() == null){
                                    return {valid: false, message: "Campo obligatorio"};
                                    }
                                return true;
                            }
                        },
                    }
                },
                deficit: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                mental: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                deambulacion: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
            }).on('status.field.bv', function(e, data){
                $("#riesgoCaidaform input[type='submit']").prop("disabled", false);
            }).on("success.form.bv", function(evt){
                $("#riesgoCaidaform input[type='submit']").prop("disabled", false); 
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
                            url: "{{URL::to('/gestionEnfermeria')}}/ingresoRiesgoCaida",
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
                                        $form[0] . reset();
                                        location . reload();
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