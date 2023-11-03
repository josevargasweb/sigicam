<style>
    .agregarCirugiasPrevias{
        margin-top: 10%;
    }

    .formulario .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

<br>
<div class="formulario panel panel-default">

    <div class="panel-body">
        <legend>Solicitud Examen Imagenología</legend>
        <button class="btn btn-primary" id="agregarExamen">Generar Solicitud</button>
        <br><br>
        <legend>Listado de solicitudes de examenes de imagenología</legend>
        <table id="tableExamenImageneologia" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 25%">OPCIONES</th>
                    <th style="width: 50%">USUARIO APLICA</th>
                    <th style="width: 25%">FECHA INGRESADA</th>
                </tr>
            </thead>
            <tbody>
    
            </tbody>
        </table>  
    </div>
</div>



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formExamenes')) }}
    {{ Form::hidden('idExamenImagenologia', '', array('class' => 'idExamenImagenologia', 'id' => 'idExamenImagenologia')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoExamen')) }}    
    <div id="formularioAgregarExamen" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"
    style="overflow-y:auto;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Formulario Agregar Examen</h4>
                </div>
                <div class="modal-body">
                    
                    <div class="row" id="div_fecha" style="margin-left: auto">
                        <br>
                        <div class="col-md-1" style="pointer-events: none;">
                            <div class="form-group">
                                {{Form::label('FECHA:', null, ['class' => 'control-label'])}}
                                {{-- {{Form::text('fecha_actual', \Carbon\Carbon::now()->format('d-m-Y'), array('id' => 'fecha_actual', 'class' => 'form-control'))}} --}}
                                <p id="fecha_creacion"></p>
                            </div>
                        </div>
                    </div>

                    <legend>Datos Paciente</legend>
                    <div class="row">
                        <br>
                        <div class="col-md-4" style="pointer-events: none;">
                            <label>NOMBRE:</label>
                            <p id="nombre_paciente"></p><br>
                        </div>
                        <div class="col-md-2" style="pointer-events: none;">
                            <label>RUT</label>
                            <p id="rut_paciente"></p><br>
                        </div>
                        <div class="col-md-3" style="pointer-events: none;">
                            <label>FECHA NACIMIENTO</label>
                            <p id="fecha_paciente"></p><br>
                        </div>
                        <div class="col-md-2" style="pointer-events: none;">
                            <label>Edad</label>
                            <p id="edad_paciente"></p><br>
                        </div>
                    </div>
                    <div class="row" id="div_fecha" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                {{Form::label('PROCEDENCIA:', null, ['class' => 'control-label'])}}<br>
                                {{-- {{Form::text('procedencia_paciente', null, array('id' => 'procedencia_paciente', 'class' => 'form-control'))}} --}}
                                <p id="procedencia_paciente"></p><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                {{Form::label('SERVICIO:', null, ['class' => 'control-label'])}}<br>
                                {{-- {{Form::text('servicio_paciente', null, array('id' => 'servicio_paciente', 'class' => 'form-control'))}}<br> --}}
                                <p id="servicio_paciente"></p><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                {{Form::label('CAMA:', null, ['class' => 'control-label'])}}<br>
                                {{-- {{Form::text('cama_paciente', null, array('id' => 'cama_paciente', 'class' => 'form-control'))}}<br> --}}
                                <p id="cama_paciente"></p><br>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-left: auto;">
                        <div style="pointer-events: none;">
                            <div class="form-group">
                                <div class="col-md-3">
                                    {{Form::label('SOSPECHA DIAGNOSTICA ACTUAL:', null, ['class' => 'control-label'])}}
                                </div>
                                <div class="col-md-7">
                                    <h4 id="diagnostico_actual">
                                        <!-- <li class="list-group-item list-group-item-success" id="diagnostico_actual"></li> -->
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-left: auto;">
                        <div style="pointer-events: none;">
                            <div class="form-group">
                                <div class="col-md-3">
                                    {{Form::label('OTROS DIAGNÓSTICOS/ANTECEDENTES:', null, ['class' => 'control-label'])}}
                                </div>
                                <div class="col-md-7">
                                    <h4 id="otros_diagnosticos_paciente">
                                        <!-- <li class="list-group-item list-group-item-success" id="otros_diagnosticos_paciente"></li> -->
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-left: auto;">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{Form::label('PACIENTE CON MEDIDAS DE AISLAMIENTO:', null, ['class' => 'control-label'])}}<br>
                                <label class="radio-inline">{{Form::radio('aislamiento_paciente', "no", false, array('id' => 'aislamiento_paciente','required' => true))}}No</label>
                                <label class="radio-inline">{{Form::radio('aislamiento_paciente', "si", false, array('id' => 'aislamiento_paciente','required' => true))}}Sí</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-md-offset-2">
                            <div class="form-group">
                                {{Form::label('POSIBILIDAD EMBARAZO:', null, ['class' => 'control-label'])}}<br>
                                <label class="radio-inline">{{Form::radio('posibilidad_embarazo_paciente', "no", false, array('id' => 'posibilidad_embarazo_paciente','required' => true))}}No</label>
                                <label class="radio-inline">{{Form::radio('posibilidad_embarazo_paciente', "si", false, array('id' => 'posibilidad_embarazo_paciente','required' => true))}}Sí</label>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-left: auto;">
                        <div class="col-md-10">
                            <div class="form-group">
                                {{Form::label('Especialidad:', null, ['class' => 'control-label'])}}<br>
                                {{Form::text('especialidad_paciente', null, array('id' => 'especialidad_paciente', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left: auto;">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{Form::label('CIRUGIAS PREVIAS:', null, ['class' => 'control-label'])}}<br>
                                <label class="radio-inline">{{Form::radio('cirugias_previas', "no", false, array('required' => true))}}No</label>
                                <label class="radio-inline">{{Form::radio('cirugias_previas', "si", false, array('required' => true))}}Sí</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="div_cirugias" style="margin-left: auto;" hidden>
                        <div class="cirugiasPrevias" id="cirugiasPrevias">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="control-label">CIRUGIA PREVIA</label>
                                    {{Form::hidden('id_cirugia[]', '', array('id' => 'id_cirugia0'))}}
                                    {{Form::text('cirugia_previa[]', null, array( 'class' => 'cirugia_previa form-control', 'placeholder' => 'Ingrese cirugia previa','id'=>'cirugia_previa0'))}} 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <br>
                            <button type="button" class="btn btn-primary agregarCirugiasPrevias" >+</button>
                        </div>
                        <div class="col-md-12 clonAgregarCirugiasPrevias" id="clonAgregarCirugiasPrevias" style="margin-left: -13px;"></div>
                    </div>

                    <div class="row" style="margin-left: auto;">
                        <legend>Examenes Imagenología</legend>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::select('examenes_imagenologia[]', array('0' => 'Tomografía computada (Scanner)', '1' => 'Ecografía', '2'=>'Radiografía','3' => 'Otro Examen'), null, array('class' => 'form-control selectpicker', 'id' => 'examenes_imagenologia', 'multiple')) }}
                                {{ Form::text('examenes_imagenologia_item', "0", array('class' => 'form-control ', "id" => "examenes_imagenologia_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
                            </div>
                        </div>
                    </div>

                    <div style="margin-left: auto;">
                        <div class="panel panel-default" id="div_tomografia" style="width: 99%" hidden>
                            <div class="panel-heading panel-info">
                                <h4>TOMOGRAFÍA COMPUTADA (SCANNER)</h4>
                            </div>
                            <div class="panel-body">
                                {{-- <div class="row" style="margin-left: auto;"> --}}
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            {{Form::label('EXAMEN SOLICITADO:', null, ['class' => 'control-label'])}}
                                        </div>
                                        <div class="col-md-9">
                                            {{Form::text('examen_solicitado', null, array('id' => 'examen_solicitado', 'class' => 'form-control'))}}
                                        </div>
                                    </div>
                                {{-- </div> --}}
                                <div class="row" style="margin-left: auto;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {{Form::label('CONTRASTE:', null, ['class' => 'control-label'])}}
                                            <br>
                                            <label class="radio-inline">{{Form::radio('contraste', "no", false, array('required' => true))}}No</label>
                                            <label class="radio-inline">{{Form::radio('contraste', "si", false, array('required' => true))}}Sí</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 contraste_si hidden">
                                        <div class="form-group">
                                            {{Form::label('CREATININEMIA', null, ['class' => 'control-label'])}}
                                            {{ Form::number('creatininemia', 0, array('class' => 'form-control ', "id" => "creatininemia", "min" => 0)) }}
                                        </div>
                                    </div>
                                    <div class="col-md-2 contraste_si col-md-offset-1 hidden">
                                        <div class="form-group">
                                            {{Form::label('FECHA', null, ['class' => 'control-label'])}}
                                            {{Form::text('fecha_contraste', null, array('id' => 'fecha_contraste', 'class' => 'form-control dtp_fechas_examen fecha_contraste'))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-left: auto;">
                                    {{-- <div class="col-md-11"> --}}
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                {{Form::label('COMENTARIO', null, ['class' => 'control-label'])}}
                                            </div>
                                            <div class="col-md-9">
                                                <div class="col-md-12">
                                                    {{Form::text('comentario_examen', null, array('class' => 'form-control ', "id" => "comentario_examen"))}}
                                                </div>    
                                            </div>
                                        </div>
                                    {{-- </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" id="div_ecografia" style="width: 99%" hidden>
                            <div class="panel-heading panel-info">
                                <h4>ECOGRAFÍA</h4>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        {{Form::label('ECOGRAFÍA DE:', null, ['class' => 'control-label'])}}
                                    </div>
                                    <div class="col-md-9">
                                        {{Form::text('ecografia', null, array('id' => 'ecografia', 'class' => 'form-control'))}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('ECOGRAFÍA DOPPLER:', null, ['class' => 'control-label'])}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('ecografia_doppler', "no", false, array('required' => true))}}No</label>
                                        <label class="radio-inline">{{Form::radio('ecografia_doppler', "si", false, array('required' => true))}}Sí</label>
                                    </div>
                                </div>
                                <div class="col-md-3 doppler_si hidden">
                                    <div class="form-group">
                                        {{Form::label('EXTREMIDADES:', null, ['class' => 'control-label'])}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('extremidades', "arterial", false, array('required' => true))}}ARTERIAL</label>
                                        <label class="radio-inline">{{Form::radio('extremidades', "venoso", false, array('required' => true))}}VENOSO</label>
                                    </div>
                                </div>
                                <div class="col-md-3 doppler_si hidden">
                                    <div class="form-group">
                                        {{Form::label('LADO:', null, ['class' => 'control-label'])}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('lado_ecografia', "izquierda", false, array('required' => true))}}IZQUIERDA</label>
                                        <label class="radio-inline">{{Form::radio('lado_ecografia', "derecha", false, array('required' => true))}}DERECHA</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" id="div_radiografia" style="width: 99%" hidden>
                            <div class="panel-heading panel-info">
                                <h4>RADIOGRAFÍA</h4>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        {{Form::label('RADIOGRAFÍA DE:', null, ['class' => 'control-label'])}} <br>
                                    </div>
                                    <div class="col-md-9">
                                        {{Form::text('radiografia', null, array('id' => 'radiografia', 'class' => 'form-control'))}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('LADO:', null, ['class' => 'control-label'])}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('lado_radiografia', "izquierda", false, array('required' => true))}}IZQUIERDA</label>
                                        <label class="radio-inline">{{Form::radio('lado_radiografia', "derecha", false, array('required' => true))}}DERECHA</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('PROYECCIONES', null, ['class' => 'control-label'])}}
                                        {{ Form::select('proyecciones[]', array('1' => 'AP','2' => 'Lateral','3' => 'Oblicua','4' => 'Otra'), null, array('id' => 'proyecciones','style' => 'backgroundColor:#000 !important', 'class' => 'selectpicker form-control', 'multiple', 'required', 'data-max-options'=>'8','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 8 especialidades permitidas</label>&quot;]")) }}
                                        {{ Form::text('proyecciones_item', "0", array('class' => 'form-control ', "id" => "proyecciones_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
                                    </div>
                                </div>
                                <div class="col-md-4 col-md-offset-1" id="detalle_otra_proyeccion" hidden>
                                    <div class="form-group">
                                        {{Form::label('COMENTARIO', null, ['class' => 'control-label'])}}
                                        {{ Form::text('comentario_proyeccion', null, array('class' => 'form-control ', "id" => "comentario_proyeccion")) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" id="div_otro_examen" style="width: 99%" hidden>
                            <div class="panel-heading panel-info">
                                <h4>OTRO EXAMEN</h4>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('SELECCIONE:', null, ['class' => 'control-label'])}}
                                        <select class="form-control" name="otro_examen" id="otro_examen">
                                            <option value="">Seleccione</option>
                                            <option value="1">Mamografía</option>
                                            <option value="2">Estudio digestivo</option>
                                            <option value="3">Procedimiento</option>
                                            <option value="4">Resonancia magnética</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8 col-md-offset-1">
                                    <div class="form-group">
                                        {{Form::label('ESPECIFICAR:', null, ['class' => 'control-label'])}}
                                        {{Form::text('especificar_examen', null, array('id' => 'especificar_examen', 'class' => 'form-control'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left: auto;">
                            <br>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('NOMBRE MEDICO SOLICITANTE:', null, ['class' => 'control-label'])}}<br>
                                    <p id="nombre_medico_solicitante" style="pointer-events: none;"></p>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row" style="margin-left: auto;">
                            <br>
                            <legend>RECAUDACIÓN</legend>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('FUNCIONARIO:', null, ['class' => 'control-label'])}}<br>
                                    {{Form::text('funcionario', null, array('id' => 'funcionario', 'class' => 'form-control'))}}<br>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnExamenes', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>
    function validarFormularioSolicitudExamenIamgenologia() {
        $("#formExamenes").bootstrapValidator("revalidateField", "aislamiento_paciente");
        $("#formExamenes").bootstrapValidator("revalidateField", "posibilidad_embarazo_paciente");
        $("#formExamenes").bootstrapValidator("revalidateField", "especialidad_paciente");
        $("#formExamenes").bootstrapValidator("revalidateField", "cirugias_previas");
        // $("[name=cirugias_previas]").change();
        $("#formExamenes").bootstrapValidator("revalidateField", "cirugia_previa[]");
        $("#formExamenes").bootstrapValidator("revalidateField", "examen_solicitado");
        $("#formExamenes").bootstrapValidator("revalidateField", "contraste");
        $("#formExamenes").bootstrapValidator("revalidateField", "creatininemia");
        $("#formExamenes").bootstrapValidator("revalidateField", "fecha_contraste");
        $("#formExamenes").bootstrapValidator("revalidateField", "examenes_imagenologia_item");
        // $("#examenes_imagenologia_item").change();
        $("#formExamenes").bootstrapValidator("revalidateField", "ecografia_doppler");
        $("#formExamenes").bootstrapValidator("revalidateField", "ecografia");
        $("#formExamenes").bootstrapValidator("revalidateField", "lado_radiografia");
        $("#formExamenes").bootstrapValidator("revalidateField", "radiografia");
        $("#formExamenes").bootstrapValidator("revalidateField", "proyecciones_item");
        $("#formExamenes").bootstrapValidator("revalidateField", "comentario_proyeccion");
        $("#formExamenes").bootstrapValidator("revalidateField", "otro_examen");
    }

    function generarTablaExamenImageneologia() {
        var caso = "{{$caso}}";
        tableExamenImagenologia= $("#tableExamenImageneologia").DataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/"+caso+"/listarExamenesMedicos",
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function recargarTablaExamenImagenologia(){
        if(typeof tableExamenImagenologia == 'undefined'){
            generarTablaExamenImageneologia();
        }else{
            tableExamenImagenologia.ajax.reload();
        }
    }
    
    function limpiarProyecciones(){
        $('#proyecciones').selectpicker('val', '');
        $('#proyecciones').selectpicker('refresh');
        detalle = document.getElementById("detalle_otra_proyeccion");
        detalle.style.display='none';
        $("#comentario_proyeccion").val('');
    }

    function limpiarContraste(){
        $("#tipo_via").on("change", function(){
            if($(this).val() == 5){
                $("#opcion_otro_via").show();
            }else{
                $("#opcion_otro_via").hide();
            }
        });
    }

    function eliminarCirugiaPrevia(posicion){
        var fila = document.getElementById("cirugiasPrevias"+posicion);
        fila.remove();
    }

    function datosPacienteExamen(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/infoPacienteExamen",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                var infoPaciente = data.infoPacienteExamen;
                if(infoPaciente){
                    $("#procedencia_paciente").text(infoPaciente.procedencia);
                    if(infoPaciente.requiere_aislamiento){
                        $("input[name=aislamiento_paciente][value='si']").prop("checked",true).change();
                    }else{
                        $("input[name=aislamiento_paciente][value='no']").prop("checked",true).change();
                    }
                    $("#nombre_paciente").text(infoPaciente.nombre);
                    $("#rut_paciente").text(infoPaciente.rut);
                    $("#fecha_paciente").text(infoPaciente.fecha_nacimiento);
                    $("#edad_paciente").text(infoPaciente.edad);
                }

                var ubicacion = data.infoUbicacion;
                $("#servicio_paciente").text(ubicacion.servicio);
                $("#cama_paciente").text(ubicacion.detalleCama);

                diagnosticos = [];
                diagnosticos = data.diagnosticos;
                diagnosticos.forEach(function(element,index) {
                    var diagnostico = element["diagnostico"];
                    if(index == 0){
                        if(diagnostico){
                            $("#diagnostico_actual").append('<li class="list-group-item list-group-item-success" style="font-size: 12px">'+diagnostico+'</li>');
                        }else{
                            $("#diagnostico_actual").append('<li class="list-group-item list-group-item-danger" style="font-size: 12px">Sin información.</li>');
                        }
                    }
                    
                    if(index > 0){
                        if(diagnostico){
                            $("#otros_diagnosticos_paciente").append('<li class="list-group-item list-group-item-success" style="font-size: 12px">'+diagnostico+'</li>');
                        }else{
                            $("#otros_diagnosticos_paciente").append('<li class="list-group-item list-group-item-danger" style="font-size: 12px">Sin información.</li>');
                        }
                    }
                    // else{
                    //     $("#otros_diagnosticos_paciente").append('<li class="list-group-item list-group-item-danger">Sin informaciónNo.</li>');
                    // }
                });

                $("#nombre_medico_solicitante").text(infoPaciente.nombre_medico_solicitante);
            },
            error: function(error){
                console.log("error: ", error);
            }
        });    
    }

    function eliminarExamenImageneologia(id){
        swalPregunta.fire({
            title: "¿Esta seguro de eliminar este examen?"
        }).then(function(result){
            if(result.isConfirmed){
                $.ajax({
                    url: "{{URL::to('/gestionMedica')}}/eliminarExamenImageneologia/"+id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "get",
                    dataType: "json",
                    success: function(data){
                        if(data.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                html: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        recargarTablaExamenImagenologia();
                                    }, 2000)
                                },
                            });
                        }
    
                        if(data.error){
                            swalError.fire({
                                title: 'Error',
                                html:data.error
                            }).then(function(result) {
                                if (result.isDenied) {
                                    recargarTablaExamenImagenologia();
                                }
                            });
                        }
                    },
                    error: function(error){
                        console.log("error: ", error);
                    }
                });
            }   
        });
    }

    function editarExamenImageneologia(id){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('gestionMedica')}}/editarExamenImagenologia/"+id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                var examen = data.examenImagenologia;
                var cirugias = data.cirugias;
                proyecciones = [];
                proyecciones = data.proyecciones;
                if(examen){
                    datosPacienteExamen();
                    
                    $("#idCasoExamen").val(caso);
                    $("#idExamenImagenologia").val(examen.id);
                    $("#fecha_creacion").text(moment(examen.fecha_creacion).format('DD-MM-YYYY'));
                    
                    if(examen.posibilidad_embarazo){
                        $("[name='posibilidad_embarazo_paciente'][value='si']").prop('checked', true).change();
                    }else{
                        $("[name='posibilidad_embarazo_paciente'][value='no']").prop('checked', true).change();
                    }

                    $("#especialidad_paciente").val(examen.especialidad_paciente).change();

                    if(cirugias.length > 0){
                        $("[name='cirugias_previas'][value='si']").prop('checked', true).change();
                        cirugias.forEach(function(element,index){
                            var id_cirugia = element["id"];
                            var cirugia = element["cirugia_previa"];
                            if(index == 0){
                                $("#id_cirugia0").val(id_cirugia);
                                $("#cirugia_previa0").val(cirugia).change();
                            }else if(index > 0){
                                var original = $("#cirugiasPrevias");
                                var clone = original.clone();
                                clone.attr('id','cirugiasPrevias'+index);

                                $("[name='id_cirugia[]']",clone).attr({'data-id': index, 'id': 'id_cirugia'+index});
                                $("[name='id_cirugia[]']",clone).val(id_cirugia).change();

                                $("[name='cirugia_previa[]']",clone).attr({'data-id': index, 'id': 'cirugia_previa'+index});
                                $("[name='cirugia_previa[]']",clone).val(cirugia).change();

                                html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCirugiaPrevia('+index+')">-</button></div>';

                                $("#clonAgregarCirugiasPrevias").append(clone);
                                clone.append(html);

                                $('#formExamenes').bootstrapValidator('addField', clone.find("[name='cirugia_previa[]']"));
                            }
                        });
                    }else{
                        $("[name='cirugias_previas'][value='no']").prop('checked', true).change();
                    }

                   if(examen.opciones_examen_imagenologia != null){
                        var opciones_examen_imagenologia = examen.opciones_examen_imagenologia.split(',');
                        $("#examenes_imagenologia").val(opciones_examen_imagenologia).change();
                        $("#examenes_imagenologia").selectpicker("refresh");                        
                   }

                   if(examen.examen_solicitado){
                        $("#examen_solicitado").val(examen.examen_solicitado).change();

                        if(examen.contraste){
                            $("[name='contraste'][value='si']").prop('checked',true).change();
                            $("#creatininemia").val(examen.creatininemia).change();
                            var fecha_contraste = moment(examen.fecha_contraste).format('DD-MM-YYYY H:mm:ss');
                            $("#fecha_contraste").val(fecha_contraste).change();
                        }else{
                            $("[name='contraste'][value='no']").prop('checked',true).change();
                        }

                        $("#comentario_examen").val(examen.comentario_examen).change();
                   }

                   if(examen.ecografia){
                        $("#ecografia").val(examen.ecografia).change();
                        if(examen.ecografia_doppler){
                            $("[name='ecografia_doppler'][value='si']").prop('checked',true).change();
                            if(examen.extremidades){
                                $("[name='extremidades'][value='venoso']").prop('checked',true).change();
                            }else{
                                $("[name='extremidades'][value='arterial']").prop('checked',true).change();
                            }

                            if(examen.lado_ecografia){
                                $("[name='lado_ecografia'][value='derecha']").prop('checked',true).change();
                            }else{
                                $("[name='lado_ecografia'][value='izquierda']").prop('checked',true).change();
                            }

                        }else{
                            $("[name='ecografia_doppler'][value='no']").prop('checked',true).change();
                        }
                   }

                   if(examen.radiografia){
                        $("#radiografia").val(examen.radiografia).change();
                        if(examen.lado_radiografia){
                            $("[name='lado_radiografia'][value='derecha']").prop('checked',true).change();
                        }else{
                            $("[name='lado_radiografia'][value='izquierda']").prop('checked',true).change();
                        }

                        ids_proyecciones = [];
                        comentario_proyeccion = '';
                        proyecciones.forEach(function(element,index){
                            proyeccion = element.proyeccion;
                            ids_proyecciones.push(proyeccion);

                            if(element.proyeccion == 4){
                                $("#comentario_proyeccion").val(element.comentario_proyeccion).change();
                            }
                        });

                        var result = ids_proyecciones.map(function (x) { 
                            return parseInt(x, 10); 
                        });

                        $("#proyecciones").val(result).change();
                        $("#proyecciones").selectpicker("refresh");
                   }

                   if(examen.otro_examen){
                        $("#otro_examen").val(examen.otro_examen).change();
                        $("#especificar_examen").val(examen.especificar_examen).change()
                   }

                   setTimeout(function() { 
                        if(examen.medidas_aislamiento){
                            $("[name='aislamiento_paciente'][value='si']").prop('checked', true).change();
                        }else{
                            $("[name='aislamiento_paciente'][value='no']").prop('checked', true).change();
                        }
                    }, 1000);
                   
                   
                   $("#formularioAgregarExamen").modal("show");                    
                }else{
                    swalInfo2.fire({
                        title: 'Información',
                        html: data.info
                    }).then(function(result) {
                        recargarTablaExamenImagenologia();
                    })
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    function guardarExamenImagen($form){
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/agregarExamenMedico",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            dataType: "json",
            data: $form .serialize(),
            async: false,
            success: function(data){
                if(data.exito){
                    swalExito.fire({
                        title: 'Exito!',
                        html: data.exito,
                        didOpen: function() {
                            setTimeout(function() {
                                recargarTablaExamenImagenologia();
                                $("#formularioAgregarExamen").modal('hide');
                            }, 2000)
                        },
                    });
                }
                if(data.error){
                    swalInfo2.fire({
                        title: 'Error',
                        html: data.error
                    }).then(function() {
                        recargarTablaExamenImagenologia();
                        $("#formularioAgregarExamen").modal('hide');
                    });
                }

                if(data.errores){
                    let ul = '';
                    
                    ul = "<ul style='text-align:left'>";
                    $.each( data.errores, function( key, value ) {
                        ul +="<li style='list-style:none'>"+value+"</li>";
                    });

                    ul += "</ul>";
                    swalError.fire({
                        title: 'Error',
                        html:ul
                    });
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    function limpiarExamenesImagenologia(){
        $("#div_tomografia").hide();
        $("#div_ecografia").hide();
        $("#div_radiografia").hide();
        $("#div_otro_examen").hide();
        $("#examenes_imagenologia").selectpicker("refresh").val('');
        $("#examenes_imagenologia").selectpicker("refresh");
    }

    $(function() {
        $("#idExamenImagen").click(function(){
            recargarTablaExamenImagenologia();
        });

        $("#gmExamenes").click(function(){
            tabEM = $("#tabsGestionExamenesMedicos div.active").attr("id");
            if(tabEM == "tabExamenImagen"){
                recargarTablaExamenImagenologia();
            }
        });

        $('#formularioAgregarExamen').on('shown.bs.modal', function () {
            validarFormularioSolicitudExamenIamgenologia();
        });

        var contCP = 1;
        $(".agregarCirugiasPrevias").click(function(){
            var original = $("div#cirugiasPrevias");
            var clone = original.clone();
            clone.attr('id', 'cirugiasPrevias'+contCP);
            $("[name='cirugia_previa[]']",clone).attr({'data-id':contCP,'id':'cirugia_previa'+contCP}).val(contCP);
            $("[name='cirugia_previa[]']",clone).val('');

            html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCirugiaPrevia('+contCP+')">-</button></div>'; 

            original.parent().find("#clonAgregarCirugiasPrevias").append(clone);
            clone.append(html);

            $('#formExamenes').bootstrapValidator('addField', clone.find("[name='cirugia_previa[]']"));
            contCP++;
        });

        $('.dtp_fechas_examen').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
            }).on('dp.change', function (e) {
            $(this).change();
        });

        $("#agregarExamen").click(function() {
            var caso = "{{$caso}}";
            datosPacienteExamen();
            $("#idCasoExamen").val(caso);
            $("#fecha_creacion").text(moment().format('DD-MM-YYYY'));
            // cargarUltimaIndicacion();
            $("#formularioAgregarExamen").modal("show");
        });

        $("#formularioAgregarExamen").on("hidden.bs.modal", function(){
            $('#formExamenes').trigger('reset');
            $("#idExamenImagenologia").val("");
            $("#diagnostico_actual").empty();
            $("#otros_diagnosticos_paciente").empty();
            limpiarProyecciones();
            limpiarContraste()
            $(".contraste_si").addClass("hidden");

            $("#fecha_contraste").val("");

            $("#comentario_examen").val("");

            $(".doppler_si").addClass("hidden");
            
            $("#clonAgregarCirugiasPrevias").empty();

            limpiarExamenesImagenologia();
        });

        $("#btnExamenes").on("click", function() {
            var checkeado_aislamiento = $("input[name='aislamiento_paciente']:checked").val();
            if(checkeado_aislamiento === undefined){
                $("input[name=aislamiento_paciente][value='no']").change();
            }

            var checkeado_embarazo = $("input[name='posibilidad_embarazo_paciente']:checked").val();
            if(checkeado_embarazo === undefined){
                $("input[name=posibilidad_embarazo_paciente][value='no']").change();
            }

            $("#especialidad_paciente").change();
            $("input[name='cirugia_previa[]']").change();
            $("#examen_solicitado").change();

            var checkeado_contraste = $("input[name='contraste']:checked").val();
            if(checkeado_contraste === undefined){
                $("input[name=contraste][value='no']").change();
            }

            $("#creatininemia").change();
            $("#fecha_contraste").change();
            $("#comentario_examen").change();
            $("#ecografia").change();

            var checkeado_doppler= $("input[name='ecografia_doppler']:checked").val();
            if(checkeado_doppler === undefined){
                $("input[name=ecografia_doppler][value='no']").change();
            }

            var checkeado_extremidades= $("input[name='extremidades']:checked").val();
            if(checkeado_extremidades === undefined){
                $("input[name=extremidades][value='arterial']").change();
            }

            var checkeado_ecografia = $("input[name='lado_ecografia']:checked").val();
            if(checkeado_ecografia === undefined){
                $("input[name=lado_ecografia][value='derecha']").change();
            }

            $("#radiografia").change();

            var checkeado_radiografia = $("input[name='lado_radiografia']:checked").val();
            if(checkeado_radiografia === undefined){
                $("input[name=lado_radiografia][value='derecha']").change();
            }

            $("#proyecciones").change();
            $("#comentario_proyeccion").change();
            $("#otro_examen").change();
            $("#especificar_examen").change();
        });

        $("#proyecciones").on("change", function(){
            var largo = $("#proyecciones").children(':selected').length;
            $("#proyecciones_item").val(largo).change();

            proyecciones = $(this).val();

            detalle = document.getElementById("detalle_otra_proyeccion");
            if(proyecciones && proyecciones.includes('4')){
                detalle.style.display = 'block';
            }else{
                detalle.style.display = 'none';
            }
        });

        $("input[name=cirugias_previas]").on("change", function(){
            var opcion = $(this).val();
            if(opcion == "si"){
                $("#div_cirugias").show();
            }else{
                $("#div_cirugias").hide();
            }
        });

        $("#examenes_imagenologia").on("change", function(){
            var largo = $("#examenes_imagenologia").children(":selected").length;
            $("#examenes_imagenologia_item").val(largo).change();
            
            var opcion = $(this).val();
            if(opcion == null){
                limpiarExamenesImagenologia();
            }else{
                if(opcion.indexOf("0") != -1){
                    $("#div_tomografia").show();
                }else{
                    $("#div_tomografia").hide();
                }

                if(opcion.indexOf("1") != -1){
                    $("#div_ecografia").show();
                }else{
                    $("#div_ecografia").hide();
                }

                if(opcion.indexOf("2") != -1){
                    $("#div_radiografia").show();
                }else{
                    $("#div_radiografia").hide();
                }

                if(opcion.indexOf("3") != -1){
                    $("#div_otro_examen").show();
                }else{
                    $("#div_otro_examen").hide();
                }
            }  
        })

        $("input[name=contraste]").on("change", function(){
            var opcion = $(this).val();
            if(opcion == "no"){
                $(".contraste_si").addClass("hidden");
                $("#creatininemia").val("");
                $("#fecha_contraste").val("");
            }else{
                $(".contraste_si").removeClass("hidden");
                $("#creatininemia").val("");
                $("#fecha_contraste").val("");
            }
        });

        $("input[name=ecografia_doppler]").on("change", function(){
            var opcion = $(this).val();
            if(opcion == "no"){
                $(".doppler_si").addClass("hidden");
            }else{
                $(".doppler_si").removeClass("hidden");
            }
        })

        $("#formExamenes").bootstrapValidator({
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                aislamiento_paciente: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                posibilidad_embarazo_paciente: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                especialidad_paciente: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'cirugias_previas':{
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'cirugia_previa[]': {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                examen_solicitado: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                creatininemia: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                fecha_contraste: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        },
                        // remote:{
                        // 	data: function(validator){
                        // 		return {
                        // 			caso: validator.getFieldElements('idCaso').val(),
                        // 		};
                        // 	},
                        // 	url: "{{ URL::to("/validarFechaIndicacion") }}"
                        // },
                    }
                },
                'examenes_imagenologia_item': {
                    trigger: 'change keyup',
                    validators: {
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#examenes_imagenologia_item").val();
                                if (value <= 0) {
                                    return {valid: false, message: "Debe seleccionar al menos un tipo" };
                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },
                ecografia: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                radiografia: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                'proyecciones_item': {
                    trigger: 'change keyup',
                    validators: {
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#proyecciones_item").val();
                                if (value <= 0) {
                                    return {valid: false, message: "Debe seleccionar al menos un tipo" };
                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },
                comentario_proyeccion: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                },
                otro_examen: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "Campo obligatorio"
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            evt.preventDefault(evt);
            bootbox.confirm("<h4>¿Está seguro de agregar el examen?</h4>", function(result) {

                if(result){
                    $("#btnIndicaciones").attr('disabled', 'disabled');
                    var $form = $(evt.target);
                    guardarExamenImagen($form);
                }
            });
        });
    });
</script>

