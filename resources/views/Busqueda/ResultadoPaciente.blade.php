
@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop
<script>

	var idCaso = null;

	function reingresar(paciente) {
		swalCargando.fire({});
		/* ../reingresar/123456 */
		$.ajax({
			url: "reingresar/"+paciente,
			dataType: "json",
			/* data: {
				"paciente" : paciente,
			}, */
			type: "GET",
			success: function(data){
				setTimeout(function() {
				swalCargando.close();
				Swal.hideLoading();
				},2500);
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
				if(data.error){
					swalError.fire({
					title: 'Error',
					text:data.error
					});
					//console.log(data.error);
				}

				if(data.cambio){
					$(".nombreModal").html(data.nombrePaciente+": "+data.cambio);
					//bootbox.alert("<h4>"+data.cambio+"</h4>");
					reasignar(data.caso,data.idSala, data.idCama,data.idPaciente);
				}

				
			},
			error: function(error){
				console.log(error);
				swalError.fire({
				title: 'Error',
				text:"No se encontraron resultados"
				});
			}
		});

	}

	//usado para traslado directo
	//idPaciente solo se usa para el reingreso
	var reasignar=function( caso, idSala, idCama,idPaciente){
		 //alert();
 		$("#salaReasignar").val(idSala);
 		$("#camaReasignar").val(idCama);
 		$("#casoReasignar").val(caso);
 		$("#pacienteReingresar").val(idPaciente);
		idCaso = caso;
		$("#unidadReasignar").val($('#tabUnidad').find('.active').children().data("id")); 
 		//dialog.modal("hide");

 		getUnidades();

 		//var unidad = "#{$unidad}";
 		//console.log(unidad);
		setTimeout(function(){
			$("#modalReasignar").modal();
		},2000);
	 	/* $('.nav-tabs a[href="#id-{$unidad}"]').tab('show'); */

 	}

	var getUnidades=function(){
		var unidades=[];
		$.ajax({
			url: "{{URL::to('/')}}/getUnidades",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			async: false,
			success: function(data){
				unidades=data;
				$("#tabUnidad").empty();
				$("#contentUnidad").empty();
				for(var i=0; i<data.length; i++){
					var nombre=data[i].url;
					var id="id-"+nombre;
					var active = (i == 0) ? "active" : "";
					$("#tabUnidad").append("<li id="+nombre+" class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+"</a></li>");
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
					generarMapaCamasDisponibles(id, data[i].url, true);
				}
				for(var i=0; i<data.length; i++){
					$("#id-"+data[i].url).removeClass("active");
				}
				if(data.length > 0) {
					//$("#id-"+data[0].url).tab("hide");
					$("#id-"+data[0].url).addClass("active");
					$("#id-"+data[0].url).tab("show");
				}
				
				//console.log($('#tabUnidad').find('.active').children().data("id"));
			},
			error: function(error){
				console.log(error);
			}
		});
		return unidades;
	}


	var generarMapaCamasDisponibles=function(mapaDiv, unidad){
		//alert("map");
		$.ajax({
			url: "{{URL::to('/')}}/unidad/"+unidad+"/getCamasDisponiblesVerdes",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {unidad: unidad, idCaso: $("#casoReasignar").val()},
			dataType: "json",
			type: "post",
			success: function(data){
				crearMapaCamas(mapaDiv, data);
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var marcarCamaDisponible=function(event, cama){
		
        event.preventDefault();
		
		var servicio_original = $("#unidad_original").val();

		var dialog = bootbox.dialog({
			//title: 'Se ha realizado el traslado interno',
			message: "¿Desea trasladar al paciente?",
			buttons: {
				cancel: {
					label: "No",
					className: 'btn-danger',
					callback: function(){
						//location.reload();
					}
				},
				ok: {
					label: "Si",
					className: 'btn-primary',
					callback: function(){
						$.ajax({
							url: "{{URL::to('/')}}/reasignar",
							headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
							data: {camaOld: $("#camaReasignar").val(), casoOld: $("#casoReasignar").val(), camaNew: cama, error: true,id_paciente:$("#pacienteReingresar").val()},
							type: "post",
							dataType: "json",
							success: function(data){
								//console.log(data);
								if(data.error){
									swalError.fire({
									title: 'Error',
									text:data.error
									});
									$("#modalReasignar").modal('toggle');
									//$("#modalIngresar").data('bs.modal', null);
								}else{
 
									swalExito.fire({
									title: 'Exito!',
									text:data.msg,
									didOpen: function() {
										setTimeout(function() {
									 $('#modalReasignar').modal('hide');
										}, 2000)
									},
									});
								}
							},
							error: function(error){
								console.log(error);
							}	 
						});
						
					}
				}
			}
		});	
 	}


	 function generarTablaHistorialVisitas(visitas) {
        tableHistorialVisitas = $("#tableHistorialVisitas").dataTable({
			"destroy": true,
            "ordering": false,
            "searching": false,
            "data":visitas,
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }
	 	
	var verHistorialVisitas= function(idCaso){
	$.ajax({
		url: "{{ URL::to('/')}}/verHistorialVisitas/"+idCaso,
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		type: "get",
		success: function(data){
			$("#id_caso_historial_visitas").val(idCaso);
			if (typeof data.paciente[0] !== 'undefined') {
				rut = "RUN no disponible";
				if(data.paciente[0].rut != null && data.paciente[0].rut != ""){
					rut = data.paciente[0].rut +"-"+ data.paciente[0].dv; 
				}
				$("#modalHistorialVisitas").find('.visitas-datosPacientes').text(data.paciente[0].nombre_completo+" ("+rut+")");
				if(data.paciente[0].recibe_visitas == true){
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:Si');
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas:'+data.paciente[0].num_personas_visitas);
					$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas:'+data.paciente[0].cant_horas_visitas);
					
					cargarDatosEdicionVisitas(true,data.paciente[0].num_personas_visitas,data.paciente[0].cant_horas_visitas);
				}else if(data.paciente[0].recibe_visitas == false){
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:No');
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas: 0');
					$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas: 0');
					
					cargarDatosEdicionVisitas(false,0,0);
				}else{
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?: Sí');
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas: 1');
					$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas: No configurado');
					
					cargarDatosEdicionVisitas(null,"","");
				}
			}
			if (typeof tableHistorialVisitas != 'undefined') {
				generarTablaHistorialVisitas(data.visitas);
			}
			
			$("#modalHistorialVisitas").modal();
		},
		error: function(error){
			console.log("error: ", error);
		}
	});
}

function cargarDatosEdicionVisitas(recibe_visitas,cant_personas,cant_horas)
{
	$("#modalHistorialVisitas").find('input[name=recibe_visitas_]').prop("checked",false);
	if(recibe_visitas !== null)
	{
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_][value=' + (recibe_visitas ? "true" : "false") + ']').prop("checked",true);
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_]:checked').trigger("change");
		$("#modalHistorialVisitas").find('#cantidad_personas_').val(cant_personas);
		$("#modalHistorialVisitas").find('#cantidad_horas_').val(cant_horas);
	}
	else{
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_][value=true]').prop("checked",true);
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_]:checked').trigger("change");
		$("#modalHistorialVisitas").find('#cantidad_personas_').val(1);
	}
}
function cargarDatosEdicionVisitasSoloLectura(recibe_visitas,cant_personas,cant_horas)
{
	var t_recibe_visitas = recibe_visitas === "true" ? "Sí" : (recibe_visitas === "false" ? "No" : "No configurado");
	var t_cant_personas = /\d/.test(cant_personas) ? cant_personas : "No configurado";
	var t_cant_horas = /\d/.test(cant_horas) ? cant_horas : "No configurado";
	
	$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:' + t_recibe_visitas);
	$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas:' + (t_recibe_visitas == "No" ? 0 : t_cant_personas));
	$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas:'+(t_recibe_visitas == "No" ? 0 : t_cant_horas));
}

$(function(){

	$("#btn_descargar_historial_visita").on("click", function(){
		var caso = $("#id_caso_historial_visitas").val();
		window.location.href = "{{URL::to('/')}}/pdfHistorialRegistroVisitas/"+caso;
	});

	$('#tablaResultadoBusqueda').dataTable({	
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 10,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		},
		aoColumnDefs : [
			{mRender : function(data, type, row) {
					//console.log(data);
					//console.log(type);
					//console.log(row);
					var temp = $("<a></a>");
					@if(Session::get('usuario')->tipo == "visualizador")
						temp = $("<span></span>");
					@endif
					temp.prop("class", "info-paciente")
					.prop("href", "{{ URL::route('mostrarInfo') }}/" + row.idpaciente)
					.html(data);
					return $("<div/>").append(temp.clone()).html();
				}, aTargets:[1,2,3]
			},
			{mData : "estab", aTargets:[0]},
			{mData : "rut", aTargets: [1]},
			{mData : "nombre", aTargets : [2]},
			{mData : "apellidos", aTargets : [3]},
			{mData : "solicitud_cama", aTargets :[4]},
			{mData : "fecha_hosp", aTargets :[5]},
			{mData : "procedencia", aTargets : [6]},	
			{mData : "diagnostico", aTargets :[7]},
			{mData : "servicio", aTargets :[8]},
			{mData : "sala", aTargets :[9]},
			{mData : "cama", aTargets :[10]},
			{mData : "opcion", aTargets :[11]},
		]
	});

	$('#tablaResultadoEgresados').dataTable({	
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 10,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
	},
	aoColumnDefs : [
		{
			mRender : function(data, type, row) {
				var temp = $("<a></a>");
				@if(Session::get('usuario')->tipo == "visualizador")
					temp = $("<span></span>");
				@endif
				temp.prop("class", "info-paciente")
				.prop("href", "{{ URL::route('mostrarInfo') }}/" + row.idpaciente)
				.html(data);
				return $("<div/>").append(temp.clone()).html();
			}, aTargets:[1,2,3]
		},
		{mData : "estab", aTargets:[0]},
		{mData : "rut", aTargets: [1]},
		{mData : "nombre", aTargets : [2]},
		{mData : "apellidos", aTargets : [3]},
		{mData : "solicitud_cama", aTargets :[4]},
		{mData : "procedencia", aTargets : [5]},
		{mData : "diagnostico", aTargets :[6]},
		{mData : "fecha_egreso", aTargets : [7]},
		{mData : "fecha_hosp", aTargets : [8]},
		{mData : "opcion", aTargets :[9]},
  	]
	});
});
</script>

<style>
	#tablaResultadoEgresados.table > thead:first-child > tr:first-child > th {
		color: cornsilk ;
	}

	#tablaResultadoBusqueda.table > thead:first-child > tr:first-child > th {
		color: cornsilk ;
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

<br><br>
<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item active">
		<a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">En hospital</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Egresados</a>
	</li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
		<div class="table-responsive">
			<br>
			<h5>Pacientes en Hospital</h5>
			<br>
			<table id="tablaResultadoBusqueda" class="table table-condensed table-hover">
				<thead>
					<tr style="background:#399865;">
						<th>Establecimiento</th>
						<th>Run</th>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Solicitud de cama</th>
						<th>Fecha hospitalización</th>
						<th>Procedencia</th>
						<th>Diagnóstico</th>
						<th>Servicio</th>
						<th>Sala</th>
						<th>Cama</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
  </div>

<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
  	<div class="table-responsive">
	  	<br>
		<h5>Pacientes egresados</h5>
		<br>
		<table id="tablaResultadoEgresados" class="table table-condensed table-hover">
			<thead>
				<tr style="background:#399865;"> 
					<th>Establecimiento</th>
					<th>Run</th>
					<th>Nombre</th>
					<th>Apellidos</th>
					<th>Solicitud de cama</th>
					<th>Procedencia</th>
					<th>Diagnóstico</th>
					<th>Fecha egreso</th>
					<th>Fecha hospitalización</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
  </div>
</div>

<input type="hidden" id="salaReasignar"/>
<input type="hidden" id="camaReasignar"/>
<input type="hidden" id="casoReasignar"/>
<input type="hidden" id="unidadReasignar"/>
<input type="hidden" id="pacienteReingresar"/>


<div id="modalReasignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Traslado interno</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
				<input type="hidden" id="salaReasignar"/>
				<input type="hidden" id="camaReasignar"/>
				<input type="hidden" id="casoReasignar"/>
				<input type="hidden" id="unidadReasignar"/>
				<input type="hidden" id="pacienteReingresar"/>
				<div class="row">
					<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
					</ul>
					<div id="contentUnidad" class="tab-content">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>


<div id="modalHistorialVisitas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Historial de visitas Paciente <button type="button" class="btn btn-danger" id="btn_descargar_historial_visita">PDF</button></h4>
				<p class="visitas-datosPacientes" style="margin-bottom: 0px;"></p>
            </div>
            <div class="modal-body">
				<form id="historial_edicion_registro_visitas">
					<input type="hidden" name="id_caso_" id="id_caso_historial_visitas">
					<div id="configuracion_visitas_solo_lectura">
						<div class="col-md-3 h5 visitas-bool" style="padding-left: 0;">
						</div>
						<div class="col-md-3 h5 visitas-cantPersonas text-center">
						</div>
						<div class="col-md-3 h5 visitas-cantHoras text-right">
						</div>
					</div>
					<div id="configuracion_visitas_edicion" hidden>
						<div class="col-sm-3">
							<label>¿Puede recibir visitas?</label><br>
							<input type="radio" id="recibe_visitas_si" name="recibe_visitas_" value="true">Sí&nbsp;
							<input type="radio" id="recibe_visitas_no" name="recibe_visitas_" value="false">No
						</div>
						<div id="inputs_config_historial">
							<div class="col-sm-3">
								<label>Cantidad de personas</label>
								<input type="number" min="1" max="6" id="cantidad_personas_" name="cantidad_personas_" class="form-control">
							</div>
							<div class="col-sm-3">
								<label>Cantidad de horas</label>
								<input type="number" min="1" max="6" id="cantidad_horas_" name="cantidad_horas_" class="form-control">
							</div>
						</div>
					</div>
				
				</form>
				<br>
				<br>

				<table id="tableHistorialVisitas" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>N°</th>
							<th>Fecha</th>
							<th>Hora ingreso</th>
							<th>Nombre completo</th>
							<th>N° identificación</th>
							<th>Telefono</th>
							<th>Relación con el paciente</th>
							<th>Ususario responsable</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
