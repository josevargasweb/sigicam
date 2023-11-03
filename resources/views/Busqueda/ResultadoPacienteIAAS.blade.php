<script>
$(function(){
	$('#tablaResultadoBusqueda').dataTable({	
	"aaSorting": [[0, "desc"]],
	"iDisplayLength": 25,
	"bJQueryUI": true,
	"oLanguage": {
		"sUrl": "{{URL::to('/')}}/js/spanish.txt"
	},
	aoColumnDefs : [
				{
				mRender : function(data, type, row) {
		      			//console.log(data);
		      			//console.log(type);
		      			//console.log(row);
		      			var temp = $("<a></a>")
		      			.prop("href", "{{ URL::route('mostrarInfo') }}/" + row.idpaciente)
		      			.prop("class", "info-paciente")
		      			.html(data);
		      			return $("<div/>").append(temp.clone()).html();
		      		}, aTargets:[1]
		      	},
        		{mData : "estab", aTargets:[0]},
        		{mData : "rut", aTargets: [1]},
        		{mData : "nombre", aTargets : [2]},
        		{mData : "apellidos", aTargets : [3]},
        		{mData : "fecha_ingreso", aTargets :[4]},
        		{mData : "diagnostico", aTargets :[5]},
        		{mData : "servicio", aTargets :[6]},
        		{mData : "sala", aTargets :[7]},
            	{mData : "cama", aTargets :[8]},   	


  	]
  	
	});
});
</script>
<div class="table-responsive">
<table id="tablaResultadoBusqueda" class="table table-striped table-bordered table-hover">
	<tfoot>
		<tr>
			<th>Establecimiento</th>
			<th>Run</th>
			<th>Nombre</th>
			<th>Apellidos</th>
			<th>Fecha ingreso</th>
			<th>Diagnóstico</th>
			<th>Servicio</th>
			<th>Sala</th>
			<th>Cama</th>
		</tr>
	</tfoot>
	<thead>
		<tr>
			<th>Establecimiento</th>
			<th>Run</th>
			<th>Nombre</th>
			<th>Apellidos</th>
			<th>Fecha ingreso</th>
			<th>Diagnóstico</th>
			<th>Servicio</th>
			<th>Sala</th>
			<th>Cama</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
</div>