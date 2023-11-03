<style>
     {
        margin-left: -40px !important;
    }
</style>

<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Indicaciones</h4>
    </div>
    <div class="panel-body">
        <button class="btn btn-success" id="agregarIndicacion">Agregar Indicación</button>
        <div>
            <br><br>
            <legend>Indicacion Actual {{\Carbon\Carbon::now()->format('d-m-Y')}}</legend>
            <div id="existeIndicacion">
                <button id="btn_editar" class="btn btn-success" hidden>Ver / Editar</button>
                <input type="text" hidden id="idIndicacionVisualizar">
                <!-- <button id="btn_agregar_comentarios_indicacion" class="btn btn-primery" hidden>Agregar comentarios</button> -->
                
                <div id="div_no_existe" class="alert alert-danger" role="alert" hidden>
                    <p style="text-align: center">
                        NO EXISTE INDICACIÓN PARA LA FECHA ACTUAL.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</div>



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formIndicaciones', 'autocomplete' => 'off')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoFormIndicacion')) }}
    <div id="formularioAgregarIndicacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Formulario Agregar Indicación</h4>
                </div>
                <div class="modal-body">
                    {{-- <div class="formulario" style="overflow-y: scroll;     height: 550px;"> --}}
                        {{-- @include('Gestion.gestionMedica.formularioIndicacionMedica') --}}

                        <div class="row" id="div_fecha" style="margin-left: auto;">
                            <br>
                            {{-- <legend>Fecha</legend> --}}
                            <div class="col-md-2" style="pointer-events: none;">
                                <div class="form-group">
                                    {{Form::label('Fecha Actual', null, ['class' => 'control-label'])}}
                                    {{Form::text('fecha_indicacion_medica', \Carbon\Carbon::now()->format('d-m-Y'), array( 'class' => 'form-control', 'id' => 'fecha_indicacion_medica', 'autocomplete' => 'off'))}}
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="alert alert-info" role="alert">
                            <p style="text-align: center">
                                AVISO: COMPROBAR QUE NO EXISTA UNA INDICACIÓN CON LA MISMA FECHA DE EMISIÓN.
                            </p>
                        </div>

                        <div class="row" id="div_fechas" style="margin-left: auto;">
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{Form::label('Fecha emisión', null, ['class' => 'control-label'])}}
                                    {{Form::text('fecha_emision', null, array('id' => 'fecha_emision', 'class' => 'form-control dtp_fechas fecha_emision'))}}
                                </div>
                            </div>
                            <div class="col-md-2 col-md-offset-1">
                                <div class="form-group">
                                    {{Form::label('Fecha vigencia', null, ['class' => 'control-label'])}}
                                    {{Form::text('fecha_vigencia', null, array('id' => 'fecha_vigencia', 'class' => 'form-control dtp_fechas'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_reposo" style="margin-left: auto;">
                            <br>
                            <legend>Reposo</legend>
                            <div class="col-md-2"> 
                                <div class="form-group">
                                    {{Form::label('Tipo Reposo', null, ['class' => 'control-label'])}}
                                    {{Form::select('tipo_reposo', array('1' => 'Absoluto','2' => 'Semisentado','3' => 'Relativo', '4' => 'Relativo asistido', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_reposo', 'placeholder' => 'Seleccione'))}}
                                </div>
                            </div>
        
                            <div class="col-md-2 col-md-offset-1" id="opcion_grados_semisentado" hidden>
                                <div class="form-group">
                                    {{Form::label('Grados Semisentado', null, ['class' => 'control-label'])}}
                                    {{Form::number('grados_semisentado', null, array('id' => 'grados_semisentado', 'class' => 'form-control', 'min' => 1, 'step' => 0.1))}}
                                </div>
                            </div>
        
                            <div class="col-md-8 col-md-offset-1" id="opcion_otro_reposo" hidden>
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('otro_reposo', null, array('id' => 'otro_reposo', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_via" style="margin-left: auto;">
                            <br>
                            <legend>Régimen</legend>
                            <div class="col-md-2" id="opcion_via">
                                <div class="form-group">
                                    {{Form::label('Tipo Vía', null, ['class' => 'control-label'])}}
                                    {{Form::select('tipo_via', array('1' => 'Oral','2' => 'SNY','3' => 'SNG', '4' => 'Parental', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_via', 'placeholder' => 'Seleccione'))}}
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-1" id="opcion_otro_via" hidden>
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('detalle_via', null, array('id' => 'detalle_via', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>
        
                        <div class="row" id="div_consistencia" style="margin-left: auto;">
                            {{-- REGIMEN -> CONSISTENCIA --}}
                            <div class="col-md-2" id="opcion_consistencia">
                                <div class="form-group">
                                    {{Form::label('Tipo Consistencia', null, ['class' => 'control-label'])}}
                                    {{Form::select('tipo_consistencia', array('1' => 'Líquido','2' => 'Blando','3' => 'Papillas', '4' => 'Común', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_consistencia', 'placeholder' => 'Seleccione'))}}
                                </div>
                            </div>
                            {{-- REGIMEN -> CONSISTENCIA -> OTRO --}}
                            <div class="col-md-8 col-md-offset-1" id="opcion_otro_consistencia" hidden>
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('detalle_consistencia', null, array('id' => 'detalle_consistencia', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_tipo" style="margin-left: auto;">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Tipo:</label>
                                    {{ Form::select('tipos[]', array('1' => 'Hiposódico','2' => 'Hipocalórico','3' => 'Hipograso','4' => 'Hipoglúcido','5' => 'Liviano','6' => 'Sin residuos','7' => 'Rico en fibra','8' => 'Común', '9' => 'Otro'), null, array('id' => 'tipos','style' => 'backgroundColor:#000 !important', 'class' => 'selectpicker form-control', 'multiple', 'required', 'data-max-options'=>'8','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 8 especialidades permitidas</label>&quot;]")) }}
                                    {{ Form::text('tipos_item', "0", array('class' => 'form-control ', "id" => "tipos_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-1" id="opcion_otro_tipo_tipo" hidden>
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('detalle_tipo_otro', null, array('id' => 'detalle_tipo_otro', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_volumen" style="margin-left: auto;">
                            <div class="col-md-11">
                                <div class="form-group">
                                    {{Form::label('Volumen', null, ['class' => 'control-label'])}}
                                    {{Form::text('volumen', null, array('id' => 'volumen', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_signos_vitales" style="margin-left: auto;">
                            <br>
                            <legend>Control de signos vitales</legend>
                            <div class="col-md-2"> 
                                <div class="form-group">
                                    {{Form::label('Cada cuantas horas', null, ['class' => 'control-label'])}}
                                    {{Form::number('horas_signos_vitales', null, array('id' => 'horas_signos_vitales', 'class' => 'form-control', 'min' => 0, 'placeholder' => 'cada x horas'))}}
                                </div>        
                            </div>
                            <div class="col-md-8 col-md-offset-1"> 
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('detalle_signos_vitales', null, array('id' => 'detalle_signos_vitales', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_hemoglucotest" style="margin-left: auto;">
                            <br>
                            <legend>Control de hemoglucotest</legend>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{Form::label('Cada cuantas horas', null, ['class' => 'control-label'])}}
                                    {{Form::number('horas_hemoglucotest', null, array('id' => 'horas_hemoglucotest', 'class' => 'form-control', 'min' => 0, 'placeholder' => 'cada x horas'))}}
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-1"> 
                                <div class="form-group">
                                    {{Form::label('Comentario', null, ['class' => 'control-label'])}}
                                    {{Form::text('detalle_hemoglucotest', null, array('id' => 'detalle_hemoglucotest', 'class' => 'form-control'))}}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_oxigeno" style="margin-left: auto;">
                            <br>
                            <legend>Oxigeno para saturar</legend>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{Form::label('Especifique', null, ['class' => 'control-label'])}}
                                    {{Form::number('oxigeno', null, array('id' => 'oxigeno', 'class' => 'form-control', 'min' => 0))}}
                                </div>
                            </div>
                            <div class="col-md-1"><br><br>%</div>    
                        </div>

                        <div class="row" id="div_sueros" style="margin-left: auto;">
                            <br>
                            <legend>Suero</legend>
                            <div>
                                <div class="col-md-2"> 
                                    <div class="form-group">
                                        {{Form::label('', "Especifique", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('sueros', "no", false, array('required' => true))}}No</label>
                                        <label class="radio-inline">{{Form::radio('sueros', "si", false, array('required' => true))}}Sí</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-md-offset-1 listado_suero hidden">
                                    <div class="form-group">
                                        {{Form::label('Sueros', null, ['class' => 'control-label'])}}
                                        {{Form::select('suero', $sueros, null, array('id' => 'suero','style' => 'backgroundColor:#000 !important','class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}}
                                        {{ Form::text('suero_item', "0", array('class' => 'form-control ', "id" => "suero_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
                                    </div>
                                </div>
                                <div class="col-md-2 col-md-offset-1 listado_suero hidden">
                                    <div class="form-group">
                                        {{Form::label('Mililitro (ml) total', null, ['class' => 'control-label'])}}
                                        {{Form::number('mililitro', null, array('id' => 'mililitro', 'class' => 'form-control', 'min' => 0))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="div_farmacos" style="margin-left: auto;">
                            <br>
                            <legend>Farmacos</legend>
                            <div class="farmacosExtras col-md-11 pl-0 pr-0" id="farmacosExtras">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('Nombre', null, ['class' => 'control-label'])}}
                                            {{Form::select('nombre_farmaco[]', $farmacos, null, array('id' => 'nombre_farmaco0', 'class' => 'form-control', /* 'required', */'placeholder' => 'seleccione'))}}
                                            {{-- {{Form::select('nombre_farmaco[]', $farmacos, null, array('id' => 'nombre_farmaco0','style' => 'backgroundColor:#000 !important', 'class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}} --}}
                                            {{-- {{Form::select('suero', $sueros, null, array('id' => 'suero','style' => 'backgroundColor:#000 !important','class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}} --}}
                                        </div>
                                    </div> 
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('Vía de administración', null, ['class' => 'control-label'])}}
                                            {{ Form::select('via_administracion[]', array('Oral' => 'Oral', 'Sublingual' => 'Sublingual', 'Tópica' => 'Tópica', 'Transdérmica' => 'Transdérmica', 'Oftalmológica' => 'Oftalmológica', 'Inhalatoria' => 'Inhalatoria', 'Rectal' => 'Rectal', 'Vaginal' => 'Vaginal', 'Intravenosa' => 'Intravenosa', 'Intramuscular' => 'Intramuscular', 'Subcutánea' => 'Subcutánea', 'Intradérmica' => 'Intradérmica', 'Ótica' => 'Ótica', 'Nasal' => 'Nasal'), null, array( 'class' => 'form-control', 'id' => 'via_administracion0')) }}
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('Intervalo', null, ['class' => 'control-label'])}}
                                            {{Form::number('intervalo_farmaco[]', null, array('id' => 'intervalo_farmaco0', 'class' => 'form-control', 'min' => 0))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('Detalle', null, ['class' => 'control-label'])}}
                                            {{Form::text('detalle_farmaco[]', null, array('id' => 'detalle_farmaco0', 'class' => 'form-control'))}}
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-1">
                                <br>
                                <button type="button" class="btn btn-primary agregarFarmacosExtras" >+</button>
                            </div>
                            <div class="col-md-12 FarmacosCopia pl-0 pr-0" id="FarmacosCopia"></div>
                        </div>
                        
                        <div class="row" id="div_atencion_terapeutica" style="margin-left: auto;">
                            <br>
                            <legend>Atención terapéutica</legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="checkbox" name="atencion_terapeutica[]" value="1">Kinesiterapia motora</br>
                                    <input type="checkbox" name="atencion_terapeutica[]" value="2">Kinesiterapia respiratoria</br>
                                    <input type="checkbox" name="atencion_terapeutica[]" value="3">Atención Fonoaudiológica</br>
                                    <input type="checkbox" name="atencion_terapeutica[]" value="4">Atención por Terapeuta ocupacional</br>
                                    <input type="checkbox" name="atencion_terapeutica[]" value="5">Evaluación nutricional</br>
                                </div>
                            </div>
                        </div>

                        <div class="row primera_indicacion hidden" style="margin-left: auto;">
                            <br>
                            <legend>Escala de prevención de trombosis venosa</legend>
                            <div>
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        {{Form::label('', "Padua", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
                                        <br>
                                        <label class="radio-inline">{{Form::radio('padua', "no", false, array('required' => true))}}No</label>
                                        <label class="radio-inline">{{Form::radio('padua', "si", false, array('required' => true))}}Sí</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-md-offset-1">
                                    <div class="form-group">
                                        {{Form::label('Caprini', null, ['class' => 'control-label'])}}<br>
                                        <label class="radio-inline">{{Form::radio('caprini', "no", false, array('required' => true))}}No</label>
                                        <label class="radio-inline">{{Form::radio('caprini', "si", false, array('required' => true))}}Sí</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_campos_extras" style="margin-left: auto">
                            <br>
                            <div class="camposExtras" id="camposExtras">
                                <div class="col-md-10"> 
                                    <div class="form-group">
                                        <label class="control-label">Otro</label> 
                                        {{Form::text('campoExtra[]', null, array( 'class' => 'campoExtra form-control', 'placeholder' => 'Ingrese comentario','id'=>'campoExtra0'))}} 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <br>
                                <button type="button" class="btn btn-primary agregarCamposExtras" >+</button>
                            </div>
                            <div class="col-md-12 clonAgregarCamposExtras" id="clonAgregarCamposExtras" style="margin-left: -13px;"></div>
                        </div>
                    {{-- </div> --}}
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnIndicaciones', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>


    function validarFormularioAgregarIndicacion(){
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'fecha_emision');
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'fecha_vigencia');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'tipo_reposo');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'grados_semisentado');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'tipo_via');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_via');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'tipo_consistencia');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_consistencia');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'tipos_item');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_tipo_otro');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'volumen');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'horas_signos_vitales');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_signos_vitales');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'horas_hemoglucotest');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_hemoglucotest');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'oxigeno');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'sueros');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'suero');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'mililitro');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'nombre_farmaco[]');   
        nombre_farmaco = document.getElementsByName('nombre_farmaco[]');
        if(nombre_farmaco.length > 1){
            $('#formIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco[]', true);  
            $('#formIndicaciones').bootstrapValidator('revalidateField', 'nombre_farmaco[]');   
        }else{
            $('#formIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco[]', false);  
        }
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'via_administracion[]');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'intervalo_farmaco[]');     
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'detalle_farmaco[]');     
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'padua');       
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'caprini');       
        //$('#formIndicaciones').bootstrapValidator('revalidateField', 'campoExtra[]');       
    }

    function ocultarSemisentado(){
        $("#opcion_grados_semisentado").hide();
        $("#grados_semisentado").val("");
    }

    function ocultarOtroReposo(){
        $("#opcion_otro_reposo").hide();
        $("#otro_reposo").val("");
    }

    function ocultarVia(){
        $("#detalle_via").val('');
        $("#tipo_via").val('');
        $("#opcion_otro_via").hide();
    }

    function ocultarConsistencia(){
        $("#tipo_consistencia").val('');
        $("#detalle_consistencia").val('');
        $("#opcion_otro_consistencia").hide();
    }

    function ocultarTipo(){
        $('#tipos').selectpicker('val', '');
        $('#tipos').selectpicker('refresh');
        detalle = document.getElementById("opcion_otro_tipo_tipo");
        detalle.style.display='none';
        $("#detalle_tipo_otro").val('');
    }

    function ocultarVolumen(){
        $("#volumen").val('');
    }

    function cargarUltimaIndicacion(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/ultimaIndicacionMedica",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.ultimaIndicacion){
                    var tipo_reposo = (data.ultimaIndicacion.tipo_reposo) ? data.ultimaIndicacion.tipo_reposo : '';
                    var otro_reposo = (data.ultimaIndicacion.otro_reposo) ? data.ultimaIndicacion.otro_reposo : '';
                    var volumen = (data.ultimaIndicacion.volumen) ? data.ultimaIndicacion.volumen : '';
                    var grados_semisentado = (data.ultimaIndicacion.grados_semisentado) ? data.ultimaIndicacion.grados_semisentado : '';
                    var tipo_via = (data.ultimaIndicacion.tipo_via) ? data.ultimaIndicacion.tipo_via : '';
                    var detalle_via = (data.ultimaIndicacion.detalle_via) ? data.ultimaIndicacion.detalle_via : '';
                    var tipo_consistencia = (data.ultimaIndicacion.tipo_consistencia) ? data.ultimaIndicacion.tipo_consistencia : '';
                    var detalle_consistencia = (data.ultimaIndicacion.detalle_consistencia) ? data.ultimaIndicacion.detalle_consistencia : '';
                    var tipos_reposo = (data.tipos_reposo) ? data.tipos_reposo : '';
                    var array_reposos = [];
                    var detalle_tipo_otro = '';
                    tipos_reposo.forEach(function(element,index,tipos_reposo) {
                        var cant = tipos_reposo.length - 1;
                        if(element){
                            if(index == cant){
                                detalle_tipo_otro = element.detalle_tipo;
                            }
                            array_reposos.push(element.tipo);
                        }
                    });
                    var tipo_via = (data.ultimaIndicacion.tipo_via) ? data.ultimaIndicacion.tipo_via : '';
                    var horas_signos_vitales = (data.ultimaIndicacion.horas_signos_vitales) ? data.ultimaIndicacion.horas_signos_vitales : '';
                    var detalle_signos_vitales = (data.ultimaIndicacion.detalle_signos_vitales) ? data.ultimaIndicacion.detalle_signos_vitales : '';
                    var horas_hemoglucotest = (data.ultimaIndicacion.horas_hemoglucotest) ? data.ultimaIndicacion.horas_hemoglucotest : '';
                    var detalle_hemoglucotest = (data.ultimaIndicacion.detalle_hemoglucotest) ? data.ultimaIndicacion.detalle_hemoglucotest : '';
                    var oxigeno = (data.ultimaIndicacion.oxigeno) ? data.ultimaIndicacion.oxigeno : '';
                    var sueros = (data.ultimaIndicacion.sueros) ? data.ultimaIndicacion.sueros : false;
                    var suero = (data.ultimaIndicacion.suero) ? data.ultimaIndicacion.suero : '';
                    var mililitro = (data.ultimaIndicacion.mililitro) ? data.ultimaIndicacion.mililitro : '';
                    var str_atencion = data.ultimaIndicacion.atencion_terapeutica;
                    var atencion_terapeutica = (str_atencion) ? str_atencion.split(",") : '';
                    var cant_atenciones = (atencion_terapeutica) ? atencion_terapeutica.length : 0;
                    $("[name='atencion_terapeuticax[]']").each(function(){
                        var valor = $(this).val();
                        var atencion_ = atencion_terapeutica.indexOf(valor);
                        if(atencion_ != -1 ){
                            var atencion = atencion_ + 1;
                            if ($(this).is(':not(:checked)')){
                                $(this).prop('checked', true).change();
                            }
                        }
                    });
                    var fecha_emision = (data.ultimaIndicacion.fecha_emision) ? moment(data.ultimaIndicacion.fecha_emision).format('DD-MM-YYYY HH:mm') : '';
                    var fecha_vigencia = (data.ultimaIndicacion.fecha_vigencia) ? moment(data.ultimaIndicacion.fecha_vigencia).format('DD-MM-YYYY HH:mm') : '';
                    
                    var farmacos = (data.farmacos) ? data.farmacos : '';
                    var comentarios = (data.comentarios) ? data.comentarios : '';

                    $("#tipo_reposox").val(tipo_reposo).change();
                    $("#otro_reposox").val(otro_reposo).change();
                    $("#grados_semisentadox").val(grados_semisentado).change();
                    $("#tipo_viax").val(tipo_via).change();
                    $("#detalle_viax").val(detalle_via).change();
                    $("#tipo_consistenciax").val(tipo_consistencia).change();
                    $("#detalle_consistenciax").val(detalle_consistencia).change();
                    $("#tiposx").selectpicker('val',array_reposos);                
                    $('#tiposx').selectpicker('refresh');
                    $('#tiposx').change();
                    
                    var tipos = $("#tiposx").selectpicker('val');
                    detalle = document.getElementById("opcion_otro_tipo_tipox");
                    if(tipos.indexOf('9') != -1){
                        detalle.style.display='block';
                        $("#detalle_tipo_otrox").val(detalle_tipo_otro).change();
                    }else{
                        detalle.style.display='none';
                        $("#detalle_tipo_otrox").val('');
                    }
                    $("#volumenx").val(volumen).change();
                    $("#horas_signos_vitalesx").val(horas_signos_vitales).change();
                    $("#detalle_signos_vitalesx").val(detalle_signos_vitales).change();
                    $("#horas_hemoglucotestx").val(horas_hemoglucotest).change();
                    $("#detalle_hemoglucotestx").val(detalle_hemoglucotest).change();
                    $("#oxigenox").val(oxigeno).change();
                    if(sueros == true){
                        $("[name='suerosx'][value='si']").prop('checked', true).change();
                        $("#suerox").val(suero).change();
                        $("#mililitrox").val(mililitro).change();
                    }else{
                        $("[name='suerosx'][value='no']").prop('checked', true).change();
                    }
                    $("#fecha_emisionx").val(fecha_emision);
                    //.change();
                    $("#fecha_vigenciax").val(fecha_vigencia);
                    //.change();

                    farmacos.forEach(function(element,index) {
                        var id_farmaco = element["id"];
                        var nombre_farmaco = element["id_farmaco"];
                        var via_administracion = element["via_administracion"];
                        var intervalo_farmaco = element["intervalo_farmaco"];
                        var detalle_farmaco = element["detalle_farmaco"];
                        if(index == 0){
                            $("#id_farmacox0").val(id_farmaco);
                            $("#nombre_farmacox0").val(nombre_farmaco).change();
                            $("#via_administracionx0").val(via_administracion).change();
                            $("#intervalo_farmacox0").val(intervalo_farmaco).change();
                            $("#detalle_farmacox0").val(detalle_farmaco).change();
                        }else if(index > 0){
                            var original = $("#farmacosExtrasx");
                            var clone = original.clone();
                            clone.attr('id', 'farmacosExtrasx'+index);
                            $("[name='id_farmacox[]']",clone).attr({'data-id': index, 'id': 'id_farmacox'+index});
                            $("[name='id_farmacox[]']",clone).val(id_farmaco);
                            $("[name='nombre_farmacox[]']",clone).attr({'data-id': index, 'id': 'nombre_farmacox'+index});
                            $("[name='nombre_farmacox[]']",clone).val(nombre_farmaco);
                            $("[name='via_administracionx[]']",clone).attr({'data-id': index, 'id': 'via_administracionx'+index});
                            $("[name='via_administracionx[]']",clone).val(via_administracion);
                            $("[name='intervalo_farmacox[]']",clone).attr({'data-id': index, 'id': 'intervalo_farmacox'+index});
                            $("[name='intervalo_farmacox[]']",clone).val(intervalo_farmaco);
                            $("[name='detalle_farmacox[]']",clone).attr({'data-id': index, 'id': 'detalle_farmacox'+index});
                            $("[name='detalle_farmacox[]']",clone).val(detalle_farmaco);
                            
                            html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarFilaFarmacox('+index+')">-</button></div>';      
                            
                            $("#FarmacosCopiax").append(clone);
                            clone.append(html);

                            $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='nombre_farmacox[]']"));
                            $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='via_administracionx[]']"));
                            $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='intervalo_farmacox[]']"));
                        }
                    });

                    comentarios.forEach(function(element,index,comentarios) {
                        var comentario = element["comentario"];
                        if(index == 0){
                            $("#campoExtrax0").val(comentario).change();
                        }else if(index > 0){
                            var original = $("#camposExtrasx");
                            var clone = original.clone();
                            clone.attr('id','camposExtrasx'+index);
                            
                            $("[name='campoExtrax[]']",clone).attr({'data-id':index,'id':'campoExtrax'+index}).val(index);

                            $("[name='campoExtrax[]']",clone).val(comentario);

                            html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCampoExtrax('+index+')">-</button></div>';

                            $("#clonAgregarCamposExtrasx").append(clone);
                            clone.append(html);

                            $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='campoExtrax[]']"));
                        }
                    });

                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    function indicacionDiaActual(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/indicacionDiaActual",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.existe == true){
                    $("#btn_editar").show();
                    $("#idIndicacionVisualizar").val(data.id_indicacion);
                    // $("#btn_agregar_comentarios_indicacion").show();
                    $("#div_no_existe").hide();
                }else{
                    $("#btn_editar").hide();
                    $("#idIndicacionVisualizar").val("");
                    // $("#btn_agregar_comentarios_indicacion").hide();
                    $("#div_no_existe").show();
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    function primeraIndicacion(caso){
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/consultaPrimeraIndicacion",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                console.log(data);
                if(data.primera){
                    // mostrar campos
                    $(".primera_indicacion").removeClass("hidden");
                }else{
                    // ocultar campos
                    $(".primera_indicacion").addClass("hidden");
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    $("#agregarIndicacion").click(function() {
        var caso = "{{$caso}}";
        //consultar si ya existe un registro.
        primeraIndicacion(caso);
        $("#idCasoFormIndicacion").val(caso);
        $("#formularioAgregarIndicacion").modal("show");
    });

    $("#tipo_reposo").on("change", function(){
        if($(this).val() == 2){
            $("#otro_reposo").val('');
            $("#opcion_otro_reposo").hide();
            $("#opcion_grados_semisentado").show();
        }else if($(this).val() == 5){
            $("#grados_semisentado").val('');
            $("#opcion_grados_semisentado").hide();
            $("#opcion_otro_reposo").show();
        }else{
            $("#grados_semisentado").val('');
            $("#opcion_grados_semisentado").hide();
            $("#otro_reposo").val('');            
            $("#opcion_otro_reposo").hide();
        }
    });

    $("#tipo_via").on("change", function(){
        if($(this).val() == 5){
            $("#opcion_otro_via").show();
        }else{
            $("#opcion_otro_via").hide();
        }
    });

    $("#tipo_consistencia").on("change", function(){
        if($(this).val() == 5){
            $("#opcion_otro_consistencia").show();
        }else{
            $("#detalle_consistencia").val('');
            $("#opcion_otro_consistencia").hide();
        }
    });

    $("#formularioAgregarIndicacion").on("hidden.bs.modal", function(){
        $('#formIndicaciones').trigger('reset');
        ocultarSemisentado();
        ocultarOtroReposo();
        ocultarVia();
        ocultarConsistencia();
        ocultarTipo();
        ocultarVolumen();

        $(".listado_suero").addClass("hidden");

        $('#suero').selectpicker('val', '');
        $('#suero').selectpicker('refresh');
        
        $("#fecha_emision").val("");
        $("#fecha_vigencia").val("");
        
        $(".primera_indicacion").addClass("hidden");

        $("#clonAgregarCamposExtras").empty();
        $("#FarmacosCopia").empty();
    });

    $("#btnIndicaciones").on("click", function() {
        $("#tipo_reposo").change();
        $("#grados_semisentado").change();
        $("#otro_reposo").change();
        $("#tipo_via").change();
        $("#detalle_via").change();
        $("#tipo_consistencia").change();
        $("#detalle_consistencia").change();
        $("#tipos").change();
        $("#detalle_tipo_otro").change();
        $("#volumen").change();
        $("#detalle_signos_vitales").change();

        $("#tipo_regimen").change();
		var checkeado = $("input[name='sueros']:checked").val();
		if(checkeado === undefined){
			$("input[name=sueros][value='no']").change();
		}
        $('#suero').change();
        /* $("#fecha_emision").change();
        $("#fecha_vigencia").change(); */

        var checkeado_padua = $("input[name='padua']:checked").val();
		if(checkeado_padua === undefined){
			$("input[name=padua][value='no']").change();
		}

        var checkeado_caprini = $("input[name='caprini']:checked").val();
		if(checkeado_caprini === undefined){
			$("input[name=caprini][value='no']").change();
		}

        $("input[name='campoExtra[]']").change();
        $("input[name='nombre_farmaco[]']").change();
        $("input[name='via_administracion[]']").change();
        $("input[name='intervalo_farmaco[]']").change();
	});

    $("#btnEditarIndicaciones").on("click", function() {
        $("#tipo_reposox").change();
        $("#grados_semisentadox").change();
        $("#otro_reposox").change();
        $("#tipo_viax").change();
        $("#detalle_viax").change();
        $("#tipo_consistenciax").change();
        $("#detalle_consistenciax").change();
        $("#tiposx").change();
        $("#detalle_tipo_otrox").change();
        $("#volumenx").change();
        $("#detalle_signos_vitalesx").change();

        $("#tipo_regimenx").change();
		var checkeado = $("input[name='suerosx']:checked").val();
		if(checkeado === undefined){
			$("input[name=suerosx][value='no']").change();
		}
        $('#suerox').change();
        /* $("#fecha_emisionx").change();
        $("#fecha_vigenciax").change(); */
        $("input[name='campoExtrax[]']").change();
        $("input[name='nombre_farmacox[]']").change();
        $("input[name='via_administracionx[]']").change();
        $("input[name='intervalo_farmacox[]']").change();
	});

    $("#tipos").on("change", function(){
		var largo= $("#tipos").children(':selected').length;
		$("#tipos_item").val(largo).change();
		$('#formIndicaciones').bootstrapValidator('revalidateField', 'tipos_item');
	});

    $("#tipos").on("changed.bs.select", function(e, clickedIndex, isSelected, oldValue){
        if(clickedIndex == 8){
            detalle = document.getElementById("opcion_otro_tipo_tipo");
            if(isSelected == true){
                detalle.style.display='block';
            }else{
                detalle.style.display='none';
                $("#detalle_tipo_otro").val('');
            }
        }
    });

    $("#suero").on("change", function(){
        var valor = $(this).val();
		$("#suero_item").val(valor).change();
		$('#formIndicaciones').bootstrapValidator('revalidateField', 'suero_item');
	});

    $('#formularioAgregarIndicacion').on('shown.bs.modal', function () {
        console.log("abrio modal");
        validarFormularioAgregarIndicacion();
    });

    $("#formIndicaciones").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {
            tipo_reposo: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            grados_semisentado: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            otro_reposo: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_regimen: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_via: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            detalle_via: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            tipo_consistencia: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            detalle_consistencia: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            'tipos_item': {
                trigger: 'change keyup',
                validators: {
                    callback: {
                        callback: function(value, validator, $field){
                            var cantidad = $("#tipos_item").val();
                            if (value <= 0) {
                                return {valid: false, message: "Debe seleccionar al menos un tipo" };
                            }else{
                                return true;
                            }
                        }
					}
                }
            },
            detalle_tipo_otro: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            },
            /* volumen: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: "Campo obligatorio"
                    }
                }
            }, */
            sueros: {
                trigger: 'change keyup',
                validators: {
                    callback: {
                        callback: function(value, validator, $field){
                            if(value == "no"){
                                $(".listado_suero").addClass("hidden");
                                $("#suero").val("");
                            }else{
                                $(".listado_suero").removeClass("hidden");
                                $("#suero").val("");
                            }
                            return true;
                        }
                    }
                }
            },
            'suero_item': {
                trigger: 'change keyup',
                validators: {
                    callback: {
                        callback: function(value, validator, $field){
                            var valor = $("#suero_item").val();
                            if (valor <= 0 || valor == "") {
                                return {valid: false, message: "Debe seleccionar al menos un suero" };
                            }else{
                                return true;
                            }
                        }
					}
                }
            },
            'nombre_farmaco[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            },
            /* 'via_administracion[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            },*/ 
            /*
            'intervalo_farmaco[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            }, */
            fecha_emision: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    },
                    remote:{
						data: function(validator){
                            console.log("validator");
                            console.log(validator);
							return {
								caso: validator.getFieldElements('idCaso').val(),
								fecha_vigencia: validator.getFieldElements('fecha_vigencia').val(),
							};
						},
						url: "{{ URL::to("/validarFechaIndicacion") }}"
					},
                    callback: {
                        callback: function(value, validator, $field) {
                            var esValidao=validarFormatoFechaHora(value);
                            if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};

                            if( $("#fecha_vigencia").val() == "") return true;
								
                            var esMenor=compararFechaIndicacion(value,$("#fecha_vigencia").val())
                            if(!esMenor) return {valid: false, message: "Fecha debe ser menor a fecha de vigencia"};

                            return true;
                        }
                    }
                }
            },
            fecha_vigencia: {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    },
                    callback: {
                        callback: function(value, validator, $field) {
                            var esValidao=validarFormatoFechaHora(value);
                            if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};

                            if( $("#fecha_emision").val() == "") return true;

                            var esMenor=compararFechaIndicacion($("#fecha_emision").val(),value)
                            if(!esMenor) return {valid: false, message: "Fecha debe ser mayor a fecha de emisión"};
								
                            return true;
                        }
                    }
                }
            }/* ,
            'campoExtra[]': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Campo obligatorio'
                    }
                }
            } */
        }
    }).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		bootbox.confirm("<h4>¿Está seguro de agregar la indicación?</h4>", function(result) {

            if(result){
                $("#btnIndicaciones").attr('disabled', false);
                var $form = $(evt.target);
                // swalCargando.fire({});
                $.ajax({
                    url: "{{URL::to('/gestionMedica')}}/agregarIndicacionMedica",
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
                                text: data.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                        indicacionDiaActual();
                                        $("#formularioAgregarIndicacion").modal('hide');
                                    }, 2000)
                                },
                            });
                        }
                        if(data.error){
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            }).then(function(result) {
                                if (result.isDenied) {
                                }
                            });
                            console.log(data.error);
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            }
		});
	});

    $('.dtp_fechas').datetimepicker({
        format: 'DD-MM-YYYY HH:mm',
        locale: 'es'
        }).on('dp.change', function (e) {
        $(this).change();
    });

    $('.dtp_fechasx').datetimepicker({
        format: 'DD-MM-YYYY HH:mm',
        locale: 'es'
        }).on('dp.change', function (e) {
        $(this).change();
    });

    var contCE = 1;
    $(".agregarCamposExtras").click(function(){
        var original = $("div#camposExtras");
        var clone = original.clone();
        clone.attr('id', 'camposExtras'+contCE);
        $("[name='campoExtra[]']",clone).attr({'data-id':contCE,'id':'campoExtra'+contCE}).val(contCE);
        $("[name='campoExtra[]']",clone).val('');

        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCampoExtra('+contCE+')">-</button></div>'; 

        original.parent().find("#clonAgregarCamposExtras").append(clone);
        clone.append(html);

        $('#formIndicaciones').bootstrapValidator('addField', clone.find("[name='campoExtra[]']"));
        contCE++;
    });

    var contCEx = 1;
    $(".agregarCamposExtrasx").click(function(){
        var original = $("div#camposExtrasx");
        var clone = original.clone();
        clone.attr('id', 'camposExtrasx'+contCEx);
        $("[name='campoExtrax[]']",clone).attr({'data-id':contCEx,'id':'campoExtrax'+contCEx}).val(contCEx);
        $("[name='campoExtrax[]']",clone).val('');

        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarCampoExtrax('+contCEx+')">-</button></div>'; 

        original.parent().find("#clonAgregarCamposExtrasx").append(clone);
        clone.append(html);

        $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='campoExtrax[]']"));
        contCE++;
    });

    function eliminarCampoExtra(posicion){
        var fila = document.getElementById("camposExtras"+posicion);
        fila.remove();
    }

    function eliminarCampoExtrax(posicion){
        var fila = document.getElementById("camposExtrasx"+posicion);
        fila.remove();
    }

    var contS = 1;
    $(".agregarFarmacosExtras").click(function(){
        var original = $("div#farmacosExtras");
        var clone = original.clone();
        clone.attr('id', 'farmacosExtras'+contS);
        $("[name='nombre_farmaco[]']",clone).attr({'data-id': contS, 'id': 'nombre_farmaco'+contS});
        $("[name='nombre_farmaco[]']",clone).val('');
        $("[name='via_administracion[]']",clone).attr({'data-id': contS, 'id': 'via_administracion'+contS});
        $("[name='via_administracion[]']",clone).val('');
        $("[name='intervalo_farmaco[]']",clone).attr({'data-id': contS, 'id': 'intervalo_farmaco'+contS});
        $("[name='intervalo_farmaco[]']",clone).val('');
        $("[name='detalle_farmaco[]']",clone).attr({'data-id': contS, 'id': 'detalle_farmaco'+contS});
        $("[name='detalle_farmaco[]']",clone).val('');
        
        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarFilaFarmaco('+contS+')">-</button></div>';      
        
        original.parent().find("#FarmacosCopia").append(clone);
        clone.append(html);
        $('#formIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco[]', true); 
        $('#formIndicaciones').bootstrapValidator('addField', clone.find("[name='nombre_farmaco[]']"));
        $('#formIndicaciones').bootstrapValidator('addField', clone.find("[name='via_administracion[]']"));
        $('#formIndicaciones').bootstrapValidator('addField', clone.find("[name='intervalo_farmaco[]']"));
        contS++;
    });

    var contSx = 1;
    $(".agregarFarmacosExtras").click(function(){
        var original = $("div#farmacosExtrasx");
        var clone = original.clone();
        clone.attr('id', 'farmacosExtrasx'+contS);
        $("[name='nombre_farmacox[]']",clone).attr({'data-id': contSx, 'id': 'nombre_farmacox'+contSx});
        $("[name='nombre_farmacox[]']",clone).val('');
        $("[name='via_administracionx[]']",clone).attr({'data-id': contSx, 'id': 'via_administracionx'+contSx});
        $("[name='via_administracionx[]']",clone).val('');
        $("[name='intervalo_farmacox[]']",clone).attr({'data-id': contSx, 'id': 'intervalo_farmacox'+contSx});
        $("[name='intervalo_farmacox[]']",clone).val('');
        $("[name='detalle_farmacox[]']",clone).attr({'data-id': contSx, 'id': 'detalle_farmacox'+contS});
        $("[name='detalle_farmacox[]']",clone).val('');
        
        html ='<div class="col-md-1"><br><button class="btn btn-danger" onclick="eliminarFilaFarmacox('+contSx+')">-</button></div>';      
        
        original.parent().find("#FarmacosCopiax").append(clone);
        clone.append(html);

        $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='nombre_farmacox[]']"));
        $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='via_administracionx[]']"));
        $('#formActualizarIndicacionesx').bootstrapValidator('addField', clone.find("[name='intervalo_farmacox[]']"));
        contS++;
    });

    function eliminarFilaFarmaco(position){
        var fila = document.getElementById("farmacosExtras"+position);
        fila.remove();

        nombre_farmaco = document.getElementsByName('nombre_farmaco[]');
        if(nombre_farmaco.length > 1){
            $('#formIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco[]', true);  
            $('#formIndicaciones').bootstrapValidator('revalidateField', 'nombre_farmaco[]');   
        }else{
            $('#formIndicaciones').bootstrapValidator('enableFieldValidators', 'nombre_farmaco[]', false);  
            // $("#nombre_farmaco0").val('').change();
        }
    }

    function eliminarFilaFarmacox(position){
        var fila = document.getElementById("farmacosExtrasx"+position);
        fila.remove();
    }

    function validarFechaIndicacion(caso,fecha){
        console.log("fecha validacion dijiste?");
        $.ajax({
            url: "{{URL::to('/')}}/validarFechaIndicacion",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            data: {
                caso: caso, 
                fecha_emision: fecha,
                fecha_vigencia: $("#fecha_vigencia").val(),
            },
            dataType: "json",
            success: function(data){
                console.log(data);
                if(data.valid == false){
                    console.log("sweetalerts");
                    swalInfo.fire({
                        title: 'Información',
                        text: data.message,
                        allowOutsideClick: false
                    });
                }
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    }

    $(".fecha_emision").on("dp.change", function(e){
        var caso = $("#idCasoFormIndicacion").val();
        var fecha = $(this).val();
        if(fecha && e.oldDate != null){
            var existeRegistro=validarFechaIndicacion(caso,fecha);
        }
        $("#fecha_vigencia").change();
    });

    $("#fecha_vigencia").on("dp.change", function(){
        console.log("revalidar fecha emision");
        $("#fecha_emision").change();
        $('#formIndicaciones').bootstrapValidator('revalidateField', 'fecha_emision');
    });

    $("#gestionIndicaciones").click(function(){
        var tabsIndicacionesMedicas = $("#tabsIndicacionesMedicas").tabs().find(".active");
        tabIM = tabsIndicacionesMedicas[0].id;

        if(tabIM == "tabIndicacion"){
            indicacionDiaActual();
        }
    });

    $("#idIndicacion").click(function(){
        indicacionDiaActual();
    });

    $("#btn_editar").click(function(){
        var caso = "{{$caso}}";
        // $("#idCasoFormIndicacionx").val(caso);
        // cargarUltimaIndicacion();
        // $("#formularioActualizarIndicacionx").modal("show");
        $("#idIndicacion").removeClass("active");
        $("#idHistoralIndicacion").addClass("active");
        $("#idHistoralIndicacion").trigger('click');
        $("#tabIndicacion").removeClass("active");
        $("#tabHistorialIndicacion").addClass("active");
        //verEditarIndicacion($("#idCaso").val(),$("#idIndicacionVisualizar").val());
        verEditarIndicacion(caso,$("#idIndicacionVisualizar").val());

    });

    $(function() {
        indicacionDiaActual();
    });

</script>

