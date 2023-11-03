<script>
    $("#formBarthel").bootstrapValidator({
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {                
			comida:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			lavado:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			vestido:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			arreglo:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			deposicion:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			miccion:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			retrete:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			trasferencia:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			deambulacion:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			escaleras:{
					validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			}
		}
	}).on("success.form.bv", function(evt){

		evt.preventDefault(evt);
		var $form = $(evt.target);

		var form = $(this).serialize();

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
						url: '{{URL::to("gestionEnfermeria/guardarBarthel")}}', 
						data: form,
						type: "post",
						dataType: "json",
						success: function(data){
							if(data.exito){
								swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
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
		return false;
	});
</script>