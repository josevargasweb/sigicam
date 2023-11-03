@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("script")

<script>
$(function() {
    $("#estadistica").collapse();

    $(".fecha-grafico").datepicker({
        startView: 'months',
        minViewMode: "months",
        autoclose: true,
        language: "es",
        format: "mm-yyyy",
        //todayHighlight: true,
        endDate: "+0d"
    });

    $('#tablaVistaLista').dataTable({
		//"aaSorting": [[8, "desc"],[9, "desc"]],
		"iDisplayLength": 25,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{ URL::to('/') }}/js/spanish.txt"
		},
		aoColumns: [
			{mData: 0},
			{mData: 1},
			{mData: function(source, type, val) {return display_num_cama(source, type);}},
			{mData: 3},
			{mData: 4},
			{mData: 5},
			{mData: 6},
			{mData: function(source, type, val) {return display_tiempo_estada(source, type, 7);}},
			//{mData: function(source, type, val) {return display_estado(source, type, val);}},
			{mData: 8},
			{mData: function(source, type, val) {return display_tiempo_estada(source, type, 9);}},
			{mData: 12},
			{mData: 13},
			{mData: 14},
		]
	});

    var display_tiempo_estada = function(source, type, val){
		if (type === 'set'){
			return;
		}
		if (type === 'display'){
			return source[val];
		}
		if (type === 'filter'){
			return source[val];
		}
		return source[10];
	}

	var display_num_cama = function(source, type){
		if (type === 'set'){
			return;
		}
		if (type === 'display'){
			return source[2];
		}
		if (type === 'filter'){
			return source[2];
		}
		return source[11];
	}

    var display_estado = function(source, type, val){
        if (type === 'set'){
            return "";
        }
        if (type === 'display'){
            return source[8];
        }
        if (type === 'filter'){
            return source[8];
        }
        switch (source[8]){
            case 'Libre': return 0;
            case 'Bloqueada': return 1;
            case 'Reservada': return 2;
            case 'Ocupada': return 3;
            case 'Reconvertida': return 4;
        }
    }

    var obtenerCamasLista=function(){
        $.ajax({
            url: "{{asset('estadisticas/IntervaloRotSus/tablaIntervalos')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {unidad: "urologia"},
            dataType: "json",
            type: "get",
            success: function(data){
                console.log(data);
                var tabla=$("#tablaVistaLista").dataTable();
                tabla.fnClearTable();
                if(data.length != 0) tabla.fnAddData(data);
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    obtenerCamasLista();  

});
</script>

@stop

@section("section")
	<fieldset>
		<div class="col-sm-12">
			<div class="col-sm-12">
				<label>Seleccione fecha</label>
			</div>
			<div class="col-sm-2 form-group">
				<input type="text" id="fecha-grafico" class="form-control fecha-grafico">
			</div>
			<div class="col-sm-2 form-group">
				<button id="btn-grafico" class="btn btn-primary">Generar gráfico</button>
			</div>
		</div>
		<div class="col-md-12">
            <div id="tablaCamas" class="table-responsive">
                <table id="tablaVistaLista" class="table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Sala</th>
                            <th>Cama</th>
                            <th>Tipo</th>
                            <th>Diagnóstico</th>
                            <th>Paciente</th>
                            <th>Run</th>
                            <th>Categorización</th>
                            <th>Ingreso</th>
                            <th>Estado</th>
                            <th>Tiempo</th>
                            <th>Edad</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
		</div>
	</fieldset>
@stop
