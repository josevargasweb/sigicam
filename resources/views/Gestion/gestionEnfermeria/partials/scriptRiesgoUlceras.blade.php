<script>
    $(document).ready( function() {
        $("#riesgoUlceraform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
                percepcion_sensorial: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                exposicion_humedad: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                actividad: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                movilidad: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                nutricion: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                peligro_lesiones: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data){
            $("#riesgoUlceraform input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#riesgoUlceraform input[type='submit']").prop("disabled", false); 
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
                            url: "{{URL::to('')}}/ingresoRiesgoUlcera",
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
                                if(data.error){
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