@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("script")
	@include('Gestion.gestionEnfermeria.partials.scriptBarthel')  {{-- Incluye script para guardar formulario barthel --}}
	<script>
	$(document).ready(function(){
		
		historial = $("#tablaHistorialBarthel").dataTable({
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
			},
		});

		$.ajax({
			url: "{{URL::to('gestionEnfermeria/buscarHistorialBarthel')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {"idCaso": {{$caso}}},
				dataType: "json",
				type: "post",
				success: function(data){
					if(data.error){
						console.log("error: no encuentra datos");
					}
					historial.fnClearTable();
					if(data.length !=0) historial.fnAddData(data);
				},
				error: function(error){
					console.log(error);
				}
		});
	});


	function editar(id_formulario_barthel){
		id = id_formulario_barthel;
		$.ajax({                            
			url: "{{URL::to('gestionEnfermeria/editarBarthel/')}}"+"/"+id,                           
			headers: {                                 
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
			},                            
			type: "get",                            
			dataType: "json",                             
			success: function(data){                                
				console.log(data); 

				comida  = $("#Comer").val(data.comida).change();
				lavado  = $("#Lavarse").val(data.lavado).change();
				vestido  = $("#Vestirse").val(data.vestido).change();
				arreglo  = $("#Arreglarse").val(data.arreglo).change();
				deposicion  = $("#Deposicion").val(data.deposicion).change();
				miccion  = $("#Miccion").val(data.miccion).change();
				retrete  = $("#Retrete").val(data.retrete).change();
				trasferencia  = $("#Trasferencia").val(data.trasferencia).change();
				deambulacion  = $("#Deambulacion").val(data.deambulacion).change();
				escaleras  = $("#Escaleras").val(data.escaleras).change();
			
				$("#id_formulario_barthel").val(data.id_formulario_barthel);

				//Estas cosas no se si funcionan. Hay que probarlas
				$("[name=fecha_creacion]").val([data.fecha_creacion]);
				$("[name=usuario_responsable]").val([data.usuario_responsable]);
				$("#guardarBarthel").val("Editar Información");
				$("#legendBarthel").hide();
				$("#volver").hide();

				total = data.arreglo + data.comida + data.deambulacion + data.deposicion + data.escaleras + data.lavado + data.miccion + data.retrete + data.trasferencia + data.vestido;

				if(total < 20){
                    $("#detalleBarthel").val("Dependencia total")
                }else if(total>=20 && total < 40){
                    $("#detalleBarthel").val("Dependencia grave")
                }else if(total>=40 && total < 60){
                    $("#detalleBarthel").val("Dependencia moderado")
                }else if(total>=60 && total < 100){
                    $("#detalleBarthel").val("Dependencia leve")
                }else{
                    $("#detalleBarthel").val("Independiente")
                }
				
				$("#totalBarthel").val(total);
				                   
			},                            
			error: function(error){                                
				//console.log(error);                            
			}                        
		});  
		$('#bannerformmodal').modal('show');
	}

	</script>
  	<meta name="csrf-token" content="{{{ Session::token() }}}">


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
<div class="container">
	<a href="javascript:history.back()" class="btn btn-primary">Volver</a>

	<div class="row">
		<div class="col-md-12" style="text-align:center;"><h4>Historial Indice Barthel</h4></div>
		<div class="col-md-12">
			<div class="col-md-2">
				{{ HTML::link(URL::route('pdfHistorialBarthel', [$caso]), 'Historial PDF', ['class' => 'btn btn-danger']) }}
			</div>
		</div>
		<div class="col-md-12">
			<br>
			Nombre Paciente: {{$nombreCompleto}}
		</div>
	</div>
	<br>
	<table id="tablaHistorialBarthel" class="table  table-condensed table-hover">
		<thead>
			<tr style="background:#399865;">
				<th>Opciones</th>
				<th>Usuario aplica</th>
				<th>Fecha aplicación</th>
				<th>Total</th>
				<th>Tipo</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

</div>

<div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 align="center" class="modal-title" id="myModalLabel">Indice de Barthel</h4>
				</div>
				<div class="modal-body">
					{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'formBarthel', 'autocomplete' => 'off')) }}

					<input type="hidden" value="{{$caso}}" name="caso">
					<input type="hidden" value="" name="id_formulario_barthel" id="id_formulario_barthel">
					<input type="hidden" value="Editar" name="tipoFormBarthel" id="tipoFormBarthel">
					<div>
						<input name="inicio" value="true" hidden="">
						<input name="tipo-encuesta" value="indiceBarthel" hidden="">
					</div>

					@include('Gestion.gestionEnfermeria.partials.FormBarthel')

					{{Form::close()}}
				</div>

			</div>
		</div>
	</div>
</div>

@stop
