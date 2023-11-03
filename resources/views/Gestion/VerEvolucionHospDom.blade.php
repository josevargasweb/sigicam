
{{-- Script Nolazko--}}
<script>
$("#complejidad_servicio").on("change", function(){
        var value = $(this).val();
        console.log('value: '+value)
        $.ajax({
            url: '{{URL::to("getAreaFuncionalPorServicio")}}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { "complejidad_servicio": value },
            dataType: "json",
            type: "post",
            success: function(data){
                console.log(data);

    
                $("#servicios2").empty();
                data.forEach(function(element){
                    $("#servicios2").append('<option value="'+element.id_complejidad_area_funcional+'" selected="selected">'+element.nombre+'</option>');
                });
            },
            error: function(error){
                console.log(error);
            }
        });

        console.log(value);
    });
</script>
{{-- Fin Script Nolazko--}}

    
<script>

    $( document ).ready(function() {

        console.log({{$caso}});
        caso = {{$caso}};
        $(".detalles-caso").val(caso);
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            }
        });

        $.ajax({
            url: "{{ URL::to('/nuevoRiesgoActual')}}",
            data: {caso : {{$caso}}},
            type: "post",
            dataType: "json",
            success: function(data){
                console.log("cual riesgo: ",data);
                if (data != "") {
                    $("#riesgohd").val(data);
                }

            },
            error: function(error){

            }
        });

        var _token = $('input[name="_token"]').val();

        


        
    });

    function modalRiesgoDependencia(){
    
		$("#modalFormularioRiesgo").modal();
	
        $.ajax({
            url: "{{ URL::to('/riesgoActual')}}",
            data: {caso : {{$caso}}},
            type: "post",
            dataType: "json",
            success: function(data){
                nombre = data.nombre+" "+data.apellido_paterno+" "+data.apellido_materno;
                $("#nombre-paciente").html(nombre);
                dependencia1 = parseInt(data.dependencia1,0);
                dependencia2 = parseInt(data.dependencia2,0);
                dependencia3 = parseInt(data.dependencia3,0);
                dependencia4 = parseInt(data.dependencia4,0);
                dependencia5 = parseInt(data.dependencia5,0);
                dependencia6 = parseInt(data.dependencia6,0);

                riesgo1 = parseInt(data.riesgo1, 0);
                riesgo2 = parseInt(data.riesgo2, 0);
                riesgo3 = parseInt(data.riesgo3, 0);
                riesgo4 = parseInt(data.riesgo4, 0);
                riesgo5 = parseInt(data.riesgo5, 0);
                riesgo6 = parseInt(data.riesgo6, 0);
                riesgo7 = parseInt(data.riesgo7, 0);
                riesgo8 = parseInt(data.riesgo8, 0);
                riesgo9 = parseInt(data.riesgo9, 0);
                
                $("#dependencia1").val(dependencia1);
                $("#dependencia2").val(dependencia2);
                $("#dependencia3").val(dependencia3);
                $("#dependencia4").val(dependencia4);
                $("#dependencia5").val(dependencia5);
                $("#dependencia6").val(dependencia6);

                $("#riesgo1").val(riesgo1);
                $("#riesgo2").val(riesgo2);
                $("#riesgo3").val(riesgo3);
                $("#riesgo4").val(riesgo4);
                $("#riesgo5").val(riesgo5);
                $("#riesgo6").val(riesgo6);
                $("#riesgo7").val(riesgo7);
                $("#riesgo8").val(riesgo8);
                $("#riesgo9").val(riesgo9);


                $("#modalFormularioRiesgo2 #dependencia1").val(dependencia1);
                $("#modalFormularioRiesgo2 #dependencia2").val(dependencia2);
                $("#modalFormularioRiesgo2 #dependencia3").val(dependencia3);
                $("#modalFormularioRiesgo2 #dependencia4").val(dependencia4);
                $("#modalFormularioRiesgo2 #dependencia5").val(dependencia5);
                $("#modalFormularioRiesgo2 #dependencia6").val(dependencia6);

                $("#modalFormularioRiesgo2 #riesgo1").val(riesgo1);
                $("#modalFormularioRiesgo2 #riesgo2").val(riesgo2);
                $("#modalFormularioRiesgo2 #riesgo3").val(riesgo3);
                $("#modalFormularioRiesgo2 #riesgo4").val(riesgo4);
                $("#modalFormularioRiesgo2 #riesgo5").val(riesgo5);
                $("#modalFormularioRiesgo2 #riesgo6").val(riesgo6);
                $("#modalFormularioRiesgo2 #riesgo7").val(riesgo7);
                $("#modalFormularioRiesgo2 #riesgo8").val(riesgo8);
                $("#modalFormularioRiesgo2 #riesgo9").val(riesgo9);
                
            
                $('.selectpicker').selectpicker('refresh');
            },
            error: function(error){

            }
        });
        

    }

    function btnRiesgoDependencia (){
        console.log("???");
        var valorDependencia = 0;

        valorDependencia = parseInt($('#dependencia1').val()) +parseInt($('#dependencia2').val()) +parseInt($('#dependencia4').val()) +parseInt($('#dependencia5').val());

        if (parseInt($('#dependencia3').val()) > 10) {
            valorDependencia += parseInt($('#dependencia3').val().substr(0,1));
        }else{
            valorDependencia += parseInt($('#dependencia3').val());
        }

        if (parseInt($('#dependencia6').val()) > 10) {
            valorDependencia += parseInt($('#dependencia6').val().substr(0,1));
        }else{
            valorDependencia += parseInt($('#dependencia6').val());
        }
        var valorRiesgo = 0;

        valorRiesgo = parseInt($('#riesgo1').val()) + parseInt($('#riesgo2').val()) +parseInt($('#riesgo3').val());

        if (parseInt($('#riesgo4').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo4').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo4').val());
        }
        if (parseInt($('#riesgo5').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo5').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo5').val());
        }
        if (parseInt($('#riesgo6').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo6').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo6').val());
        }
        if (parseInt($('#riesgo7').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo7').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo7').val());
        }
        if (parseInt($('#riesgo8').val()) > 10) {
            valorRiesgo += parseInt($('#riesgo8').val().substr(0,1));
        }else{
            valorRiesgo += parseInt($('#riesgo8').val());
        }

        var riesgoDependencia = "";
        if (valorRiesgo >=19) {
            riesgoDependencia = "A";
        }else if(valorRiesgo >= 12 && valorRiesgo <= 18){
            riesgoDependencia = "B";
        }else if (valorRiesgo >= 6 && valorRiesgo <= 11) {
            riesgoDependencia = "C";
        }else{
            riesgoDependencia = "D";
        }


        if (valorDependencia >=13) {
            riesgoDependencia += "1";
        }else if(valorDependencia >= 7 && valorDependencia <= 12){
            riesgoDependencia += "2";
        }else{
            riesgoDependencia += "3";
        }

        $("#riesgohd").val(riesgoDependencia);
        $("#div-comentario-riesgo").show();
        $("#div-servicio").show();
        $("#div-area-funcional").show();
        $('#modalFormularioRiesgo').modal('hide');

        /* Inicio Nolazko*/
            /******************* Select Servicio *********************/
                var riesgo=$('#riesgohd').val();
                var riesgo=document.getElementById('riesgo').value;   
                console.log(riesgo);
                if(riesgo == 'A1' || riesgo == "A2" || riesgo == "A3" || riesgo == "B1" || riesgo == "B2"){
                    console.log("critico");
                    complejidad = 'crítico';
                }
                else if (riesgo == 'B3' || riesgo == "C1" || riesgo == "C2"){
                    console.log("medio");
                    complejidad = 'medio';
                }
                else if(riesgo == 'C3' || riesgo == "D1" || riesgo == "D2" || riesgo == "D3"){
                    console.log("básico");
                    complejidad = "básico";
                }

                $.ajax({
                    url: '{{URL::to("getComplejidadPorRiesgo")}}',
                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { "complejidad": complejidad },
                    dataType: "json",
                    type: "post",
                    success: function(data){
                        console.log(data);
                        $("#complejidad_servicio").empty();
                        $("#complejidad_servicio").append('<option value="" selected="selected">Seleccione servicio</option>');
                        
                        $.each(data, function(kery, value){

                            $("#complejidad_servicio").append('<option value='+value.id_complejidad+' >'+value.nombre_servicio+'</option>');
                        });
                        
                        
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
             /******************* FIN Select Servicio *********************/
        /*Fin NOlazko*/

    }

    function generarTablaVerRiesgos(){
       tablaVerRiesgos = $("#tabla-evoluciones-paciente").dataTable({
            "iDisplayLength": 8,
            "ordering": false,
            "searching": false,
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    $(function(){

        if(typeof tablaVerRiesgos != 'undefined'){
            $("#tabla-evoluciones-paciente").dataTable({
                "iDisplayLength": 8,
                "ordering": false,
                "searching": false,
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
            });
        }else{
            generarTablaVerRiesgos();
        }

        $("#formIngresarEvolucion").submit(function(e){
            
            e.preventDefault();
            var dependencias = [$("#dependencia1").val(), $("#dependencia2").val(), $("#dependencia3").val(), $("#dependencia4").val(), $("#dependencia5").val(), $("#dependencia6").val()];
            var riesgos = [$("#riesgo1").val(), $("#riesgo2").val(), $("#riesgo3").val(), $("#riesgo4").val(), $("#riesgo5").val(), $("#riesgo6").val(), $("#riesgo7").val(), $("#riesgo8").val()];

            selects = $('#modalFormularioRiesgo2').find("select");
            console.log(selects);
            dependencias2 = [];
            riesgos2 = [];
            $.each(selects, function(i, val){
                
                idInput = val.id;
                //saco el primer caracter para saber si es riesgo o dependencia
                primerCaracter = idInput.substr(0,1);

                if(primerCaracter == "d"){
                    dependencias2.push($(this).val());
                    //console.log(parseInt($("#"+idInput).val()));
                }

                if(primerCaracter == "r"){
                    riesgos2.push($(this).val());
                    //console.log(parseInt($("#"+idInput).val()));
                }
            });

            $.ajax({
                url: "{{ URL::to('/cambiarRiesgo')}}",
                data: {
                    dependencias2: dependencias2, 
                    riesgos2: riesgos2,
                    dependencias : dependencias, 
                    riesgos : riesgos, 
                    categoria : $("#riesgohd").val(), 
                    idCaso : $(".detalles-caso").val(), 
                    motivo:$("#motivo_riesgo_nuevo").val(), 
                    comentario_riesgo: $("#comentario-riesgo").val(),
                    
                },
                type: "post",
                dataType: "json",
                success: function(data){
                    if(data.error){
                        swalError.fire({
						title: 'Error',
						text:data.error
						});
                        if (data.tipo == "101") {
                            $("#div-motivo").show();
                        }
                        if (data.tipo == "100") {
                            $("#div-comentario-riesgo").show();
                        }
                        
                    }else{
                         swalExito.fire({
						title: 'Exito!',
						text:"Se ha guardado la categoria",
						didOpen: function() {
							setTimeout(function() {
						location . reload();
							}, 2000)
						},
						});
                        $("#modalVerDetalles .modal-body").html(data.contenido);
                        // location.reload();
                    }
                    //location.reload();
                },
                error: function(error){

                }
            });
            return false;
        });
    });
</script>


<fieldset>
    {{ csrf_field() }}
    <legend>Categorización</legend>
    <div class="form-group col-md-12">
    	<div class="table-responsive">
        <table id="tabla-evoluciones-paciente" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Categoría</th>
            </tr>
            </thead>
            <tbody>
            @foreach($evoluciones as $evolucion)
                <tr>
                    <td>{{ $evolucion['fecha'] }}</td>
                    <td>{{ $evolucion['riesgo'] }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
        </div>
    </div>
    {{ Form::open( array('url' => 'cambiarRiesgo', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formIngresarEvolucion') ) }}
    {{ Form::hidden('caso', '', array('class' => 'detalles-caso')) }}
    <div class="form-group col-md-12">
        <!-- {{ Form::select('nuevo-riesgo', array_slice($riesgos,1), null, array('id' => 'nuevo-riesgo', 'class' => 'form-control', 'style' => 'width: 70%;')) }} -->

    </div>
    @if(Session::get('usuario')->tipo != 'director' && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS && Session::get("usuario")->tipo !== TipoUsuario::CENSO)
        

        
        <div class="col-sm-6" id="div-comentario-riesgo" hidden style="margin-bottom: 10px;">
            <label for="riesgo" class="control-label" title="Riesgo">Comentario riesgo: </label>
            {{ Form::textarea('comentario-riesgo', null, array('id' => 'comentario-riesgo','class' => 'form-control', 'rows'=>'3')) }}
        </div>

        <br>
        
        {{-- Nolazko --}}
        <div class="row">
            <div id="div-motivo" class="col-sm-8" style="margin-bottom: 10px; margin-left: 15px;" hidden>
                <label>Motivo</label>
                {{ Form::textarea('motivo', null, array('id' => 'motivo_riesgo_nuevo','class' => 'form-control', 'rows'=>'3')) }}
            </div>
            <div class="riesgo form-group col-md-12 ">
                <div class="col-sm-2" style="width: 50px;">
                    {{ Form::text('riesgo', null, array('id' => 'riesgohd','class' => 'form-control', 'readonly', 'style' => 'text-align:center; width: 50px;')) }}

                </div>
                <div class="col-sm-6" style="padding-left: 45px;">                    
                    <a id="riesgo" type="" class="btn btn-primary" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
                </div>
                <div class="col-sm-4">
                    {{ Form::submit('Guardar categoria', array('id' => 'btnCambiarCategoria', 'class' => 'btn btn-success')) }}
                </div>
            </div>
        </div>
    @endif
</fieldset>
{{ Form::close() }}
