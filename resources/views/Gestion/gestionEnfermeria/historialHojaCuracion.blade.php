@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("miga")
<li><a href="#">Gestión de Enfermeria</a></li>
<li><a href="#" onclick='location.reload()'>Historial Paciente</a></li>
@stop

@section("script")

<script>

	$(document).ready(function(){
		historial = $("#tablaHistorialCuracion").dataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
        },
	});

		$.ajax({
			url: "{{URL::to('gestionEnfermeria/buscarHistorialCuracion')}}",
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
		function editar(id_formulario_hoja_curaciones){

id = id_formulario_hoja_curaciones;
//console.log(id);
$.ajax({                            
			//url: "buscarNombres", 
			url: "{{URL::to('gestionEnfermeria/editarHojaCuracion/')}}"+"/"+id,                           
			headers: {                                 
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
			},                            
			type: "get",                            
			dataType: "json",                            
			//data: {"id":id},                            
			success: function(data){
				console.log(data);
				if(data.aspecto == "Eritematoso"){
					$("#aspecto").val(1);
				}
				if(data.aspecto == "Enrojecido"){
					$("#aspecto").val(2);
				}
				if(data.aspecto == "Amarillo pálido"){
					$("#aspecto").val(3);
				}
				if(data.aspecto == "Necrótico"){
					$("#aspecto").val(4);
				}

				if(data.mayor_extension == "0 - 1 cm"){
					$("#mayorExtension").val(1);
				}
				if(data.mayor_extension == "> 1 - 3 cm"){
					$("#mayorExtension").val(2);
				}
				if(data.mayor_extension == "> 3 - 6 cm"){
					$("#mayorExtension").val(3);
				}
				if(data.mayor_extension == "> 6 cm"){
					$("#mayorExtension").val(4);
				}

				if(data.profundidad == "0"){
					$("#profundidad").val(1);
				}
				if(data.profundidad == "< 1 cm"){
					$("#profundidad").val(2);
				}
				if(data.profundidad == "1 - 3 cm"){
					$("#profundidad").val(3);
				}
				if(data.profundidad == "> 3 cm"){
					$("#profundidad").val(4);
				}
				
				if(data.exudado_cantidad == "Ausente"){
					$("#cantidad").val(1);
				}
				if(data.exudado_cantidad == "Escaso"){
					$("#cantidad").val(2);
				}
				if(data.exudado_cantidad == "Moderado"){
					$("#cantidad").val(3);
				}
				if(data.exudado_cantidad == "Abundante"){
					$("#cantidad").val(4);
				}

				if(data.exudado_calidad == "Sin exudado"){
					$("#calidad").val(1);
				}
				if(data.exudado_calidad == "Seroso"){
					$("#calidad").val(2);
				}
				if(data.exudado_calidad == "Turbio"){
					$("#calidad").val(3);
				}
				if(data.exudado_calidad == "Purulento"){
					$("#calidad").val(4);
				}

				if(data.tejido_granulatorio == "100 - 75%"){
					$("#granulatorio").val(1);
				}
				if(data.tejido_granulatorio == "< 75 - 50%"){
					$("#granulatorio").val(2);
				}
				if(data.tejido_granulatorio == "< 50 - 25%"){
					$("#granulatorio").val(3);
				}
				if(data.tejido_granulatorio == "< 25%"){
					$("#granulatorio").val(4);
				}

				if(data.tejido_esfacelado == "Ausente"){
					$("#esfacelado").val(1);
				}
				if(data.tejido_esfacelado == "< 25%"){
					$("#esfacelado").val(2);
				}
				if(data.tejido_esfacelado == "25 - 50%"){
					$("#esfacelado").val(3);
				}
				if(data.tejido_esfacelado == "> 50%"){
					$("#esfacelado").val(4);
				}

				if(data.tejido_necrotico == "Ausente"){
					$("#necrotico").val(1);
				}
				if(data.tejido_necrotico == "< 25%"){
					$("#necrotico").val(2);
				}
				if(data.tejido_necrotico == "25 - 50%"){
					$("#necrotico").val(3);
				}
				if(data.tejido_necrotico == "> 50%"){
					$("#necrotico").val(4);
				}

				if(data.edema == "Ausente"){
					$("#edema").val(1);
				}
				if(data.edema == "+"){
					$("#edema").val(2);
				}
				if(data.edema == "++"){
					$("#edema").val(3);
				}
				if(data.edema == "+++"){
					$("#edema").val(4);
				}

				if(data.dolor == "0 - 1"){
					$("#dolor").val(1);
				}
				if(data.dolor == "2 - 3"){
					$("#dolor").val(2);
				}
				if(data.dolor == "4 - 6"){
					$("#dolor").val(3);
				}
				if(data.dolor == "7 - 10"){
					$("#dolor").val(4);
				}

				if(data.piel_circundante == "Sana"){
					$("#pielC").val(1);
				}
				if(data.piel_circundante == "Descamada"){
					$("#pielC").val(2);
				}
				if(data.piel_circundante == "Eritematosa"){
					$("#pielC").val(3);
				}
				if(data.piel_circundante == "Macerada"){
					$("#pielC").val(4);
				}
				var fecha = moment(data.proxima_curacion).format('DD-MM-YYYY');
				$("#totalCuracion").val(data.total);
				$("#observaciones").val(data.observaciones);
				$("#proximaCuracion").val(fecha);
				$("#id_formulario_hoja_curaciones").val(data.id_formulario_hoja_curaciones);
				$("#btnSolicitar").val("Editar Información");
				$("#volver").hide();
				$("#legendCuracion").hide();
				                   
			},                            
			error: function(error){                                
				//console.log(error);                            
			}                        
			}); 
			$('#bannerformmodal').modal('show'); 

}


</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
    
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>
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
		.formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>


<div class="row">
	<div class="col-md-12" style="text-align:center;"><h4>Historial Hoja curación</h4></div>
	<div class="col-md-12">
		Nombre Paciente: {{$nombreCompleto}}
	</div>

</div>



<table id="tablaHistorialCuracion" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
		<thead>
			<tr style="background:#399865;">
				<th>Opciones</th>
				<th>Fecha de creación</th>
				<th>Total</th>
				<th>Observaciones:</th>
				<th>Proxima Curación</th>		
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
                <h4 align="center" class="modal-title" id="myModalLabel">Hoja Curación</h4>
                </div>
                <div class="modal-body">
					@include('Gestion.gestionEnfermeria.hojaCuraciones')
                </div>
                       
        </div>
        </div>
      </div>
    </div>

@stop
