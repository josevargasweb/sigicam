<script>
    $(document).ready( function() {
        $("#usoRestringidoform").bootstrapValidator({
            excluded:[':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'diagnosticor': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'sitio_infeccion': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'iaas': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'patogeno': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'nutricion': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'fechaCultivo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'antibioticoCultivo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'locacionCultivo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'antimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'dosisAntimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'posologiantimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'duracionAntimicrobiano[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'justificacion_temperatura': {
                    validators:{
 			 			greaterThan: {
 			 				inclusive: true,
 			 				value: 1,
 			 				message: 'La cantidad debe ser mayor a 0'
 			 			},
                    callback: {
                        message: "Solo puede contener máximo 1 decimales",
                        callback: function (value, validator) {
                            if (value.substring(value.indexOf('.')).length < 3)   {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
 			 		}
                },
                'justificacion_parametro': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'justificacion_estado': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
            }
        }).on('status.field.bv', function(e, data){
            $("#usoRestringidoform input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#usoRestringidoform input[type='submit']").prop("disabled", false);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                message: "<h4>¿Está seguro de ingresar la información?</h4>",
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
                            url: "{{ URL::to('/gestionEnfermeria')}}/agregarUsoRestringido",
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
                                                // $form[0] . reset();
                                                // location . reload();
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
                                        // location . reload();
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
