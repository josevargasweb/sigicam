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
        function editar(idHojaEnfermeria) {

            $.ajax({
                url: "{{URL::to('gestionEnfermeria/datosHoja')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"idHojaEnfermeria": idHojaEnfermeria},
                dataType: "json",
                type: "post",
                success: function(data){
                    if (data.error) {
                        console.log("no encontre datos");
                    }

                    $("#estada").val(data.hojaEnfermeria.controlestada);
                    $("#operado").val(data.hojaEnfermeria.controloperado);
                    
                    //separar atb
                    cadena1 = data.hojaEnfermeria.controlatb;
                    tmp = cadena1.split(",");
                    $(".atb").each(function(index, value){
                        $(this).val(tmp[index]);
                    });

                    $("#folio").val(data.hojaEnfermeria.folio);
                    $("#indicacionesF").val(data.hojaEnfermeria.indicacion);
                    $("#horarioF").val(data.hojaEnfermeria.horario);
                    $("turnoL1").val(data.hojaEnfermeria.cambioposicion10i);
                    $("turnoL2").val(data.hojaEnfermeria.cambioposicion10d);
                    $("turnoL31").val(data.hojaEnfermeria.cambioposicion14i);
                    $("turnoL32").val(data.hojaEnfermeria.cambioposicion14d);
                    $("turnoL41").val(data.hojaEnfermeria.cambioposicion18i);
                    $("turnoL42").val(data.hojaEnfermeria.cambioposicion18d);
                    $("turnoN1").val(data.hojaEnfermeria.cambioposicion22i);
                    $("turnoN2").val(data.hojaEnfermeria.cambioposicion22d);
                    $("turnoN31").val(data.hojaEnfermeria.cambioposicion2i);
                    $("turnoN32").val(data.hojaEnfermeria.cambioposicion2d);
                    $("turnoN41").val(data.hojaEnfermeria.cambioposicion5i);
                    $("turnoN42").val(data.hojaEnfermeria.cambioposicion5d);
                    $("#valoracionTurnoLargo").val(data.hojaEnfermeria.valoracionturnol);
                    $("#nombreEnfermeroTurnoLargo").val(data.hojaEnfermeria.enfermeraturnol);
                    $("#valoracionTurnoNoche").val(data.hojaEnfermeria.valoracionturnon);
                    $("#nombreEnfermeroTurnoNoche").val(data.hojaEnfermeria.enfermeraturnon);

                    var total = 0;
                    $('#criterioEdad').val(data.hojaEnfermeria.valoracionedad.toString());
                    total += (data.hojaEnfermeria.valoracionedad.toString() == "true")?1:0;
                    $('#criterioComprConciencia').val(data.hojaEnfermeria.valoracionconciencia.toString());
                    total += (data.hojaEnfermeria.valoracionconciencia.toString() == "true")?2:0;
                    $('#criterioAgiPsicomotora').val(data.hojaEnfermeria.valoracionpsicomotora.toString());
                    total += (data.hojaEnfermeria.valoracionpsicomotora.toString() == "true")?2:0;
                    $('#criterioLimSensorial').val(data.hojaEnfermeria.valoracionsensorial.toString());
                    total += (data.hojaEnfermeria.valoracionsensorial.toString() == "true")?1:0;
                    $('#criterioLimMotora').val(data.hojaEnfermeria.valoracionmotora.toString());
                    total += (data.hojaEnfermeria.valoracionmotora.toString() == "true")?1:0;
                    $("#totalEnfermeria").val(total);

                    $("#sngEnfermeria").val(data.hojaEnfermeria.procedimientosng);
                    $("#sny").val(data.hojaEnfermeria.procedimientosny);
                                        
                    //separar atb
                    cadena2 = data.hojaEnfermeria.procedimientovvp;
                    tmp2 = cadena2.split(",");
                    $(".vpp").each(function(index, value){
                        $(this).val(tmp2[index]);
                    });

                    $("#cup").val(data.hojaEnfermeria.procedimientocup);
                    
                    $(".signoVital").each(function(index, value){      
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if (index == 0) {
                                if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario1);                             if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario2);
                                if(index2 == 2) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario3);
                                if(index2 == 3)$(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario4);
                            }else{
                                if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].descripcion);
                                if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario1);
                                if(index2 == 2) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario2);
                                if(index2 == 3) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario3);
                                if(index2 == 4) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlSignoVital[index].horario4);
                            }
                        }
                    });

                    $(".volumenesSoluciones").each(function(index, value){       
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].solucion);
                            if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].volumen);
                            if(index2 == 2) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].inicio);
                            if(index2 == 3) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].fin);
                        }
                    });

                    $(".volumenesSoluciones").each(function(index, value){       
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].solucion);
                            if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].volumen);
                            if(index2 == 2) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].inicio);
                            if(index2 == 3) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaVolumenSolucion[index].fin);
                        }
                    });

                    $(".controlEgreso").each(function(index, value){       
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlEgreso[index].descripcion);
                            if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlEgreso[index].largo);
                            if(index2 == 2) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlEgreso[index].noche);
                            if(index2 == 3) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlEgreso[index].total);
                            if(index2 == 4) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaControlEgreso[index].observacion);
                        }
                    });

                    $(".examenLaboratorio").each(function(index, value){       
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaExamenLaboratorio[index].solicitado);
                            if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaExamenLaboratorio[index].tomado);
                        }
                    });

                    $(".examenImagen").each(function(index, value){       
                        for (let index2 = 0; index2 < $(this).children().length; index2++) {
                            if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaExamenImagen[index].solicitado);
                            if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaExamenImagen[index].tomado);
                        }
                    });

                    if (data.HojaEnfermeriaInterconsulta.length != 0 ) {
                        $(".interconsulta").each(function(index, value){
                            for (let index2 = 0; index2 < $(this).children().length; index2++) {
                                if(index2 == 0) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaInterconsulta[index].valor1);
                                if(index2 == 1) $(this).children().children().children().eq(index2).val(data.HojaEnfermeriaInterconsulta[index].valor2);
                            }
                        });
                    }                  

                    $(".descripcionCuidado").each(function(index, value){   
                        if (data.HojaEnfermeriaCuidadoEnfermeria.length > index) {
                            $(this).children().val(data.HojaEnfermeriaCuidadoEnfermeria[index].cuidado);
                        }                        
                    });

                    $(".turno1").each(function(index, value){  
                        if (data.HojaEnfermeriaCuidadoEnfermeria.length > index) {     
                            $(this).children().children().val(data.HojaEnfermeriaCuidadoEnfermeria[index].turnolargoizquierdo);
                        }
                    });

                    $(".turno2").each(function(index, value){      
                        if (data.HojaEnfermeriaCuidadoEnfermeria.length > index) { 
                            $(this).children().children().val(data.HojaEnfermeriaCuidadoEnfermeria[index].turnolargoderecho);
                        }                         
                    });

                    $(".turno3").each(function(index, value){   
                        if (data.HojaEnfermeriaCuidadoEnfermeria.length > index) { 
                            $(this).children().children().val(data.HojaEnfermeriaCuidadoEnfermeria[index].turnonocheizquierdo);
                        }
                    });

                    $(".turno4").each(function(index, value){       
                        if (data.HojaEnfermeriaCuidadoEnfermeria.length > index) { 
                            $(this).children().children().val(data.HojaEnfermeriaCuidadoEnfermeria[index].turnonochederecho);
                        }
                    });
                    
                    $("#idFormEnfermeria").val(data.hojaEnfermeria.id_formulario_hoja_enfermeria);  
                    $("#btnSolicitar").val("Editar Información");  
                    
                },
                error: function(error){
                    console.log(error);
                }
            });
            $('#modalHojaEnfermeria').modal('show')
        }

        $(document).ready(function(){
            historial = $("#tablaHistorialHojaEnfermeria").dataTable();

            $.ajax({
                url: "{{URL::to('gestionEnfermeria/buscarHistorialHojaEnfermeria')}}",
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
        <div class="col-md-12" style="text-align:center;"><h4>Historial Hoja de Enfermeria</h4></div>
        <div class="col-md-12">
            Nombre Paciente: {{$paciente}}
        </div>

    </div>

    <table id="tablaHistorialHojaEnfermeria" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">

        <thead>

            <tr style="background:#399865;">
                <th>Información de creación</th>
                <th>Opciones</th>
                <th>Indicaciones</th>
                <th>Valoración en enfermeria</th>
                
            </tr>
            
        </thead>
        <tbody>
            
        </tbody>
    </table>

    <div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="modalHojaEnfermeria">
		<div class="modal-dialog modal-lg" id="modalAncho">
        	<div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Hoja Enfermeria</h4>
                </div>
                <div class="modal-body">
                    @include('Gestion.gestionEnfermeria.hojaEnfermeriaForm')
                </div>
        	</div>
      	</div>
    </div>
    
@stop