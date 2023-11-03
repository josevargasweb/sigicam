@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("script")

<script>

    $(document).ready(function(){

        $("#ingresoPacientePostradoform").bootstrapValidator({            
            excluded: ':disabled',            
            fields: {				
                fecha: {					
                    validators:{					
                        notEmpty: {					   
                             message: 'La fecha es obligatoria'					
                            },
                            date: {
                                format: 'DD-MM-YYYY h:m',
                                message: 'Ingrese la fecha en el formato correcto'
                            }				
                        }				
                    },
                sitio: {					
                    validators:{					
                        notEmpty: {					   
                             message: 'El sitio es obligatorio'					
                        }					
                    }				
                },
            }        
        }).on('status.field.bv', function(e, data) {            
            $("#ingresoPacientePostradoform input[type='submit']").prop("disabled", false);        
        }).on("success.form.bv", function(evt){            
            $("#ingresoPacientePostradoform input[type='submit']").prop("disabled", false);            
            evt.preventDefault(evt);            
            var $form = $(evt.target);            
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
                            url: "{{URL::to('/gestionEnfermeria')}}/ingresoPacientePostrado",			    
                            headers: {					         
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                            },					    
                            type: "post",					    
                            dataType: "json",					    
                            data: $form .serialize(),					    
                            success: function(data){					        					        
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
                                        text: data.error
                                    });
                                }

                                if(data.info) {
                                    swalInfo2.fire({
                                        title: 'Información',
                                        text: data.info
                                    }).then(function(result) {
                                        location.reload();
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

        historial = $("#tablaHistorialPacientePostrado").dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
            },
        });

        $.ajax({
            url: "{{URL::to('gestionEnfermeria/buscarHistorialPacientePostrado')}}",
            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {"idCaso": {{$caso}}},
                    dataType: "json",
                    type: "post",
                    success: function(data){
                        if(data.error){
                            console.log("error: no se encuentran datos");
                        }
                        console.log(data);
                        historial.fnClearTable();
                        if(data.length !=0) historial.fnAddData(data);
                    },
                    error: function(error){
                        console.log(error);
                    }
        });
    });

	function editar(id_formulario_paciente_postrado){

		id = id_formulario_paciente_postrado;
		//console.log(id);
		$.ajax({                            
				//url: "buscarNombres", 
				url: "{{URL::to('gestionEnfermeria/editarPacientePostrado/')}}"+"/"+id,                           
				headers: {                                 
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
				},                            
				type: "get",                            
				dataType: "json",                            
				//data: {"id":id},                            
				success: function(data){
					var fecha = moment(data.fecha).format('DD-MM-YYYY HH:mm');
					console.log(fecha);
					
					//var fecha = 
					$("#sitio").val(data.sitio);
					$("#fecha").val(fecha);
					
					
					$("#id_formulario_paciente_postrado").val(data.id_formulario_paciente_postrado);
					$("#btnpostrados").val("Editar Información");
					$("#volver").hide();
					$("#legendPostrado").hide();
					                   
				},                            
				error: function(error){                                
					//console.log(error);                            
				}                        
		});  
		$('#bannerformmodal').modal('show');

	}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>
@stop

@section("miga")
<li><a href="#">Gestión de Enfermeria</a></li>
<li><a href="#" onclick='location.reload()'>Historial Paciente</a></li>
@stop

@section("section")
<style>
    .table > thead:first-child > tr:first-child > th {
        color: cornsilk;
    }


    table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc {
        color: #032c11 !important;
    }

    table.dataTable thead .sorting, 
    table.dataTable thead .sorting_asc, 
    table.dataTable thead .sorting_desc {
        background : none;
    }

    table > thead:first-child > tr:first-child > th{
        vertical-align: middle;
    }

</style>
<br>
<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>


<div class="row">
	<div class="col-md-12" style="text-align:center;"><h4>Historial Paciente Dismovilizado</h4></div>
	<div class="col-md-12">
		Nombre Paciente: {{$nombreCompleto}}
	</div>

</div>


    <table id="tablaHistorialPacientePostrado" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
		<thead>
			<tr style="background:#399865;">
				<th>Opciones</th>
                <th>Fecha de creación</th>
                <th>Sitio</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

	<div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 align="center" class="modal-title" id="myModalLabel">Paciente Dismovilizado</h4>
                    </div>
                    <div class="modal-body">
                        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'ingresoPacientePostradoform', 'autocomplete' => 'on')) }}
                        {{ Form::hidden('idCaso', $caso, array('id' => 'idCaso')) }}
                        <input type="hidden" value="En Curso" name="tipoFormPacientePostrado" id="tipoFormPacientePostrado">
                        <input type="hidden" value="" name="id_formulario_paciente_postrado" id="id_formulario_paciente_postrado">
                    
                        @include('Gestion.gestionEnfermeria.partials.FormPacientePostrado')
                        {{ Form::close() }}
                    </div>
                        
                </div>
            </div>
        </div>
    </div>

@stop
