@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("script")

<script>

	var inicializarTabla=function(id){
		$("#"+id).dataTable({	
			"aaSorting": [[0, "desc"]],
			"iDisplayLength": 10,
			"bJQueryUI": true,
			"bAutoWidth" : false,
			"oLanguage": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
		});
	}

	var getUnidades=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getUnidadesIndex",
			data: {id: "{{$id}}"},
			type: "post",
			dataType: "json",
			success: function(data){
				$("#tabUnidad").empty();
				$("#contentUnidad").empty();
				for(var i=0; i<data.length; i++){
					var nombre=data[i].alias;
					var id="id-"+nombre;
					var active = (i == 0) ? "active" : "";
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+"</a></li>");
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane mapa-camas-class' id='"+data[i].alias+"' style='margin-top: 20px;'></div>");
					if($("#vista").val() == 1) generarMapaCamas(id, data[i].alias);
					else generarListaCama(id, data[i].alias);

				}
				if(data.length > 0) {
					$("#id-"+data[0].alias).addClass("active");
					$("#id-"+data[0].alias).tab("show");
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var generarListaCama=function(id, unidad){
		$.ajax({
			url: "getListacCamaUnidad",
			data: {id: "{{$id}}", unidad: unidad},
			type: "post",
			dataType: "json",
			success: function(data){
				var idTabla=id+"-tabla";
				var table="<div class='table-responsive'>";
				table+="<table id='"+idTabla+"' class='table table-striped table-bordered tableLista' width='100%'>";
				table+="<thead>";
				table+="<th>Servicio</th> <th>Sala</th> <th>Cama</th> <th>Diagnóstico</th> <th>Paciente</th> <th>Run</th> <th>Estado</th> <th>Tiempo</th>";
				table+="</thead>";
				table+="<tbody>";
				for(var i=0; i<data.length; i++){
					table+="<tr> <td>"+data[i][0]+"</td> <td>"+data[i][1]+"</td> <td>"+data[i][2]+"</td> <td>"+data[i][3]+"</td> <td>"+data[i][4]+"</td> <td>"+data[i][5]+"</td> <td>"+data[i][6]+"</td> <td>"+data[i][7]+"</td> </tr>";
				}
				table+="</tbody>";
				table+="</table>";
				table+="</div>";
				$("#"+id).html(table);
				inicializarTabla(idTabla);
			},
			error: function(error){
				console.log(error);
			}
		})
	}

 	var generarMapaCamas=function(mapaDiv, unidad){
 		$.ajax({
 			url: "getCamas",
 			data: {unidad: unidad, id: "{{$id}}"},
 			dataType: "json",
 			type: "post",
 			success: function(data){
 				crearMapaCamas(mapaDiv, data);
 				resizeMapaCamas(mapaDiv);
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

	$(function(){

		getUnidades();

		$("#vista").on("change", function(){
			getUnidades();
		});

	});
</script>

@stop

 @section("miga")
 <li><a href="#">Gestión de Camas</a></li>
 <li><a href="#">{{$nombre}}</a></li>
 @stop


@section("section")
<h3>Camas de {{$nombre}}</h3>
<fieldset>
	<div class="form-inline">
		<label>Seleccionar vista: </label>
		<select id="vista" class="form-control">
			<option value="1">Vista iconos</option>
			<option value="2">Vista lista</option>
		</select>
	</div>
	<br><br>
	<div id="divMapa" class="row">
		<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
		</ul>
		<div id="contentUnidad" class="tab-content">
		</div>
	</div>

</fieldset>

@stop