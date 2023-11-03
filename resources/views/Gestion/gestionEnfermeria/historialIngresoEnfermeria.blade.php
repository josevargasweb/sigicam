@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("script")

<script>

$(document).ready(function(){
	historial = $("#tablaHistorialIngresoEnfermeria").dataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
        },
	});

	$.ajax({
		url: "{{URL::to('gestionEnfermeria/buscarHistorialIngresoEnfermeria')}}",
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

//$('#bannerformmodal').on('shown.bs.modal', function (e) {
	function editar(id_formulario_hoja_ingreso_enfermeria){

id = id_formulario_hoja_ingreso_enfermeria;
//console.log(id);
$.ajax({                            
			//url: "buscarNombres", 
			url: "{{URL::to('gestionEnfermeria/editarIngresoEnfermeria/')}}"+"/"+id,                           
			headers: {                                 
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
			},                            
			type: "get",                            
			dataType: "json",                            
			//data: {"id":id},                            
			success: function(data){
				console.log(data);
				$("#checkHabito").prop('checked', data.habito_otros);
				if(data.habito_otros == true){
					detalle = document.getElementById("detalleHabito");
					detalle.style.display='block';
				}

				$("#check").prop('checked', data.otro_cateter);
				if(data.otro_cateter == true){
					detalle = document.getElementById("detalleCateter");
					detalle.style.display='block';
				}
				
				if(data.protesis_dental == true){
					 $("input[name=dental][value='si']").prop('checked', true);
					 $("#detalledental").show("slow");
					console.log("activando");
				}
				console.log(data.pertenencias);
				if(data.pertenencias == ""){
					$("input[name=perte][value='no']").prop('checked', true);
					$("#detallePertenencias").hide("slow");
				}else{
					$("input[name=perte][value='si']").prop('checked', true);
					$("#detallePertenencias").show("slow");
				}
				$("#insertar").val('editar');
				$("#antecedentesM").val(data.anamnesis_ant_morbidos);
				$("#antecedentesQ").val(data.anamnesis_ant_quirurgicos);
				$("#antecedentesA").val(data.anamnesis_ant_alergicos); 
				$("#diagnosticoMedico").val(data.diagnosticos_medicos);
				$("#pas").val(data.presion_arterial_sistolica);
				$("#pad").val(data.presion_arterial_diastolica);
				$("#pulso").val(data.pulso);
				$("#fr").val(data.frecuencia_cardiaca);
				$("#temperatura").val(data.temperatura);
				$("#saturacion").val(data.saturacion);
				$("#nutricional").val(data.patron_nutricional);
				$("#conciencia").val(data.estado_conciencia);
				$("#glasgow").val(data.glasgow);
				$("#funcionRespiratoria").val(data.funcion_respiratoria);
				$("#higiene").val(data.higiene);
				$("#cabeza").val(data.cabeza);
				$("#dental").val(data.protesis_dental);
				$("#detalleDental").val(data.detalle_protesis_dental);
				$("#cuello").val(data.cuello);
				$("#torax").val(data.torax);
				$("#abdomen").val(data.abdomen);
				$("#superiores").val(data.extremidades_superiores);
				$("#inferiores").val(data.extremidades_inferiores);
				$("#columnaDorso").val(data.columna_torso);
				$("#genitales").val(data.genitales);
				$("#piel").val(data.piel);
				$("#amnesisActual").val(data.anamnesis_actual);
				$("#pertenencias").val(data.pertenencias);
				$("#detalleOtroCateter").val(data.detalle_otro_cateter);
				$("#diagEnfermeria").val(data.diagnostico_enfermeria);
				$("#examenes").val(data.examenes);
				$("#detalleOtroHabito").val(data.detalle_otro_habito);
				$("#tabaco").prop('checked', data.habito_tabaco);	
				$("#alcohol").prop('checked', data.habito_alcohol);
				$("#drogas").prop('checked', data.habito_drogas);
				$("#branula1").prop('checked', data.branulas1);
				$("#branula2").prop('checked', data.branulas2);
				$("#sng").prop('checked', data.sng);
				$("#cvc").prop('checked', data.cvc);
				$("#foley").prop('checked', data.s_foley);
				$("#nova").val(data.nova);
				$("#caida").val(data.riesgo_caida);
				var peso = parseInt(data.peso);
				$("#peso").val(parseInt(data.peso));
				var altura = parseFloat(data.altura).toFixed(2);
				$("#altura").val(altura);

				if(peso != null && altura != null){
                        altura2 = altura * altura;
                        resultado = peso/altura2;
                        resultado2 = truncate(resultado,1);
                        $("#imc").val(resultado2);

                        if(resultado2 < 16.0){
                            $("#descripcionImc").html("Bajo Peso - Delgadez severa");
                        }

                        if(resultado2 >= 16.0 && resultado2 <= 16.9){
                            $("#descripcionImc").html("Bajo Peso - Delgadez moderada");
                        }

                        if(resultado2 >= 17.0 && resultado2 <= 18.49){
                            $("#descripcionImc").html("Bajo Peso - Delgadez aceptable");
                        }

                        if(resultado2 >= 18.5 && resultado2 <= 24.9){
                            $("#descripcionImc").html("Normal");
                        }

                        if(resultado2 >= 25.0 && resultado2 <= 29.9){
                            $("#descripcionImc").html("Sobrepeso - pre-obeso Riesgo");
                        }

                        if(resultado2 >= 30.0 && resultado2 <= 34.9){
                            $("#descripcionImc").html("Obeso - tipo I Riesgo moderado");
                        }

                        if(resultado2 >= 35.0 && resultado2 <= 39.9){
                            $("#descripcionImc").html("Obeso - tipo II Riesgo severo");
                        }

                        if(resultado2 >= 40){
                            $("#descripcionImc").html("Obeso - tipo III Riesgo muy severo");
                        }
                    }
				
				$("#id_formulario_hoja_ingreso_enfermeria").val(data.id_formulario_hoja_ingreso_enfermeria);
				$("#btnSolicitarIngreso").val("Editar Información"); 
				$("#volver").hide();
				$("#legendIngresoEnfermeria").hide();             
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
		#modalAncho{
            width: 80% !important;
        }

		.formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

</style>
<br>
<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>

<div class="row">
	<div class="col-md-12" style="text-align:center;"><h4>Historial Ingreso Enfermeria</h4></div>
	<div class="col-md-12">
		Nombre Paciente: {{$paciente}}
	</div>

</div>

	


<table id="tablaHistorialIngresoEnfermeria" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
		<thead>
			<tr style="background:#399865;">
				<th>Opciones</th>
				<th>Fecha de creación</th>
				<th>Antecedentes Anamnesis</th>
				<th>Habitos</th>
				<th>Mediciones</th>
				<th>Patrón Nutricional</th>
				<th>Glasgow</th>
				<th><b>Branulas</b></th>
				{{-- <th>Función Respiratoria:</th>
				<th><b>Aplicación</b></th>
				<th><b>Cabeza: </b></th>
				<th><b>Dental:</b></th>
				<th><b>Cuello: </b></th>
				<th><b>Torax: </b></th>
				<th><b>Abdomen:</b></th>
				<th><b>Extremidades: </b></th>
				<th><b>Columna Torso: </b></th>
				<th><b>Genitales: </b></th>
				<th><b>Piel:</b></th>
				<th><b>Anamnesis Actual: </b></th>
				<th><b>Pertenencias: </b></th>
				<th><b>Higiene:</b></th>
				<th><b>Diagnostico Enfermeria: </b></th>
				<th><b>Examenes: </b></th> --}}

				
				
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

	<div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal" style="overflow-y: scroll;">
		<div class="modal-dialog modal-lg">
        	<div class="modal-content">
          		<div class="modal-content">
                	<div class="modal-header">
                		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                		<h4 align="center" class="modal-title" id="myModalLabel">Ingreso Enfermeria</h4>
                	</div>
                	<div class="modal-body">
					@include('Gestion.gestionEnfermeria.ingresoEnfermeria')
                	</div>   
        		</div>
        	</div>
      	</div>
    </div>

@stop
