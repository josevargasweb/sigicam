@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("miga")
<li><a href="#">Gestión de Enfermeria</a></li>
<li><a href="#" onclick='location.reload()'>Historial Planificación de los cuidados</a></li>
@stop

@section("script")
    

    <script>

        function editar(idPlanificacionCuidados) {

            $.ajax({
                url: "{{URL::to('gestionEnfermeria/datosPlanificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"idPlanificacionCuidados": idPlanificacionCuidados},
                dataType: "json",
                type: "post",
                success: function(data){
                    if (data.error) {
                        console.log("no encontre datos");
                    }

                    cadena = data.PlanificacionCuidados.turnobanol;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at1_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnobanon;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at1_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnoaseoparciall;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at2_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnoaseoparcialn;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at2_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnocambiobajol;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at3_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnocambiobajon;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at3_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnocambioaltomediol;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at4_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnocambioaltomedion;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at4_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnoaseoocularl;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at5_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnoaseoocularn;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at5_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnoaseobucall;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at6_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnoaseobucaln;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at6_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnoaseogenitall;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at7_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnoaseogenitaln;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at7_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnorevisionbrazaletel;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at8_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnorevisionbrazaleten;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at8_tn").val(tmp);
                    }

                    cadena = data.PlanificacionCuidados.turnorevisionbajadasgoteosl;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at9_tl").val(tmp);
                    }
                    cadena = data.PlanificacionCuidados.turnorevisionbajadasgoteosn;
                    if (cadena != null) {
                        tmp = cadena.split(",");
                        $("#at9_tn").val(tmp);
                    }

                    $(".selectpicker").selectpicker("refresh");
                    
                    $("#novedades").val(data.PlanificacionCuidados.novedades);

                    $("#responsableProteccion").val(data.PlanificacionCuidados.proteccion_nobre);
                    $("#proteccionCambio").val(data.PlanificacionCuidados.proteccion_fecha);

                    $("#responsableCuracion").val(data.PlanificacionCuidados.curacion_nobre);
                    $("#curacionCambio").val(data.PlanificacionCuidados.curacion_fecha);
                    
                    $("#btnVolverCuidado").hide();
                    $("#legendCuidado").hide();
                    
                    $("#idFormPlanificacion").val(data.PlanificacionCuidados.id_formulario_planificacion_cuidados);  
                    $("#btnplancuidados").val("Editar Información");  
                    
                },
                error: function(error){
                    console.log(error);
                }
            });
            $('#modalPlanificacionCuidado').modal('show')
        }

        $(document).ready(function(){
            historial = $("#tablaHistorialPlanificacion").dataTable(
                {	
			"bJQueryUI": true,
			"iDisplayLength": 10,
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}}
            );

            $.ajax({
                url: "{{URL::to('gestionEnfermeria/buscarHistorialPlanificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"idCaso": {{$caso}}},
                dataType: "json",
                type: "post",
                success: function(data){
                    if (data.error) {
                        console.log("no encontre datos");
                    }
                    console.log(data);


                    /* var tabla=$("#optimizacion-table").dataTable().columnFilter({
                        aoColumns: [
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "textarea"},
                            null
                        ]
                    }); */
                    historial.fnClearTable();
                    if(data.length != 0) historial.fnAddData(data);
                
                },
                error: function(error){
                    console.log(error);
                }
            });

        });
    </script>
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
    </style>

    <a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>


    <div class="row">
        <div class="col-md-12" style="text-align:center;"><h4>Historial Planificación de los cuidados</h4></div>
        <div class="col-md-12">
            Nombre Paciente: {{$paciente}}
        </div>

    </div>

    <table id="tablaHistorialPlanificacion" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">

        <thead>

            <tr style="background:#399865;">
                <th>Información de creación</th>
                <th>Opciones</th>
                <th>Novedades</th>
                <th>Cambio de protecciones</th>
                <th>Programación de curaciones</th>
            </tr>
            
        </thead>
        <tbody>
            
        </tbody>
    </table>

    <div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="modalPlanificacionCuidado">
		<div class="modal-dialog modal-lg" id="modalAncho">
        	<div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Planificación de Cuidados</h4>
                </div>
                <div class="modal-body">
                    @include('Gestion.gestionEnfermeria.planificacionCuidados')
                </div>
        	</div>
      	</div>
    </div>
    
@stop