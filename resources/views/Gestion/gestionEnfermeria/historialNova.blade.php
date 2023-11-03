@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("miga")
<!-- <li><a href="#">Urgencia</a></li>
<li><a href="#">Documentos</a></li> -->
@stop

@section("script")
<script>
	$( document ).ready(function() {

		$("#escalaNovaform").bootstrapValidator({            
        excluded: ':disabled',            
        fields: { 
            estado_mental:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            incontinencia:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            movilidad:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            nutricion_ingesta:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            actividad:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            }
        }        
    }).on('status.field.bv', function(e, data) {            
        $("#escalaNovaform input[type='submit']").prop("disabled", false);        
    }).on("success.form.bv", function(evt){            
        $("#escalaNovaform input[type='submit']").prop("disabled", false);            
        evt.preventDefault(evt);            
        var $form = $(evt.target);
        datos = $("#escalaNovaform").serialize();
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
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);
                if(result){					
                            console.log("entra alajax?");					
                            $.ajax({					    
                            //url: "escalaNovaform",	
                            url: "{{URL::to('/gestionEnfermeria')}}/store",
                            //url: '{{URL::to("escalaNovaform")}}', 				    
                            headers: {					         
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                            },					    
                            type: "post",					    
                            dataType: "json",					    
                            data: $("#escalaNovaform").serialize(), //$form .serialize(),					    
                            success: function(data){					        
                                //$("#formEnviarDerivado").modal("hide");					        
                                //if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });	
                                console.log("historial");				        
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
                            error: function(error){					        
                                console.log(error);					    
                            }					
                            });					
                        }
            }
        });        
    });

  	table=$('#tabla').dataTable({	
			//responsive: true,
			dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'Pacientes en espera',
					text: 'Exportar',
					exportOptions: {
						columns: [1,2,3,4,5,6,7]
					} ,
					className: 'btn btn-default',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						var clRow = $('row', sheet);
						//$('row c', sheet).attr( 's', '25' );  //bordes
						$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
						$('row', sheet).attr('ht',15);
						$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
						$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
					}
				}
    		],
			"bJQueryUI": true,
			"iDisplayLength": 10,
			"order": [[ 1, "asc" ]],
			"columnDefs": [
       			{ type: 'date-euro', targets: 1 }
     		],
			"ajax": "datosTablaNova",
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
			"sPaginationType": "full_numbers",
			
		});
	});

		function editar(id_formulario_escala_nova){
			id = id_formulario_escala_nova;
			console.log(id);
			$.ajax({                            
						//url: "buscarNombres", 
                        url: "{{URL::to('gestionEnfermeria/editarNova/')}}"+"/"+id,                           
                        headers: {                                 
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
                        },                            
                        type: "get",                            
                        dataType: "json",                            
                        //data: {"id":id},                            
                        success: function(data){                                
                            console.log(data); 
							$("#estado_mental").val([data.estado_mental]).change();
							$("#incontinencia").val([data.incontinencia]).change();
							$("#movilidad").val([data.movilidad]).change();
							$("#nutricion_ingesta").val([data.nutricion_ingesta]).change();
							$("#actividad").val([data.actividad]).change();
							//$("#total").val(data.total);
							$("#id_formulario_escala_nova").val([data.id_formulario_escala_nova]);
							$("#legendNova").hide();
							$("#volver").hide();
							$("#guardarNova").val("Editar Información");
							
							total = data.estado_mental + data.incontinencia + data.movilidad + data.nutricion_ingesta + data.actividad;
							console.log(total);
							if (total == 0) {
								//$("#spanDetalleTotal").text("Sin Riesgo");
								$("#detallex").val("Sin Riesgo");
                            }
                            if (total >= 1 && total <=4) {
								//$("#spanDetalleTotal").text("Riesgo Bajo");
								$("#detallex").val("Riesgo Bajo");
                            }
                            if (total >= 5 && total <=8) {
								//$("#spanDetalleTotal").text("Riesgo Medio");
								$("#detallex").val("Riesgo Medio");
                            }
                            if (total >= 9 && total <=15) {
								//$("#spanDetalleTotal").text("Riesgo Alto");
								$("#detallex").val("Riesgo Alto");
							} 
							$("#puntos").val(total);                
                        },                            
                        error: function(error){                              
                        }                        
                        });  
						$('#bannerformmodal').modal('show');
		}


</script>


@stop

@section('section')
		<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>

        <div class="col-md-12">
            <div class="col-md-12" style="text-align:center;"><h4>Historial Escala Nova</h4></div>
            <div class="col-md-12">
                Nombre Paciente: {{$nombreCompleto}}
            </div>

        </div>


        <table id="tabla" class="table  table-condensed table-hover">
            <thead>
                <tr style="background:#399865;">
                    <th>Opciones</th>
                    <th>Fecha Creación</th>
                    <th>Estado mental</th>
                    <th>Incontinencia</th>
                    <th>Movilidad</th>
                    <th>Nutrición ingesta</th>
                    <th>Actividad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>







    <div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 align="center" class="modal-title" id="myModalLabel">Escala Nova</h4>
                    </div>
                    <div class="modal-body">
                        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaNovaform')) }}
                        {{ Form::hidden ('caso', $caso, array('id' => 'caso') )}}
                        <input type="hidden" value="Editar" name="tipoFormNova" id="tipoFormNova">
                        <br>   
                        <input type="hidden" value="" name="id_formulario_escala_nova" id="id_formulario_escala_nova">
                        <br>
                    
                        @include('Gestion.gestionEnfermeria.partials.FormNova')
                        {{ Form::close() }}
                    </div>
                        
            </div>
            </div>
        </div>
    </div>
@stop
