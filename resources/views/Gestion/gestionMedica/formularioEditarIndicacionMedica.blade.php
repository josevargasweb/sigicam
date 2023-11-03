<div class="row" id="div_fecha" style="margin-left: auto;">
    <br>
    {{-- <legend>Fecha</legend> --}}
    <div class="col-md-2" style="pointer-events: none;">
        <div class="form-group">
            {{Form::label('Fecha', null, ['class' => 'control-label'])}}
            {{Form::text('fecha_indicacion_medica_', \Carbon\Carbon::now()->format('d-m-Y'), array( 'class' => 'form-control', 'id' => 'fecha_indicacion_medica_', 'autocomplete' => 'off'))}}
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
            {{Form::text('fecha_emision_', null, array('id' => 'fecha_emision_', 'class' => 'form-control dtp_fechas_'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1">
        <div class="form-group">
            {{Form::label('Fecha vigencia', null, ['class' => 'control-label'])}}
            {{Form::text('fecha_vigencia_', null, array('id' => 'fecha_vigencia_', 'class' => 'form-control dtp_fechas_'))}}
        </div>
    </div>
</div>

<div class="row" id="div_reposo" style="margin-left: auto;">
    <br>
    <legend>Reposo</legend>
    <div class="col-md-2"> 
        <div class="form-group">
            {{Form::label('Tipo Reposo', null, ['class' => 'control-label'])}}
            {{Form::select('tipo_reposo_', array('1' => 'Absoluto','2' => 'Semisentado','3' => 'Relativo', '4' => 'Relativo asistido', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_reposo_', 'placeholder' => 'Seleccione'))}}
        </div>
    </div>

    <div class="col-md-2 col-md-offset-1" id="opcion_grados_semisentado_" hidden>
        <div class="form-group">
            {{Form::label('Grados Semisentado', null, ['class' => 'control-label'])}}
            {{Form::number('grados_semisentado_', null, array('id' => 'grados_semisentado_', 'class' => 'form-control', 'min' => 1, 'step' => 0.1))}}
        </div>
    </div>

    <div class="col-md-8 col-md-offset-1" id="opcion_otro_reposo_" hidden>
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('otro_reposo_', null, array('id' => 'otro_reposo_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_via" style="margin-left: auto;">
    <br>
    <legend>Régimen</legend>
    <div class="col-md-2" id="opcion_via_">
        <div class="form-group">
            {{Form::label('Tipo Vía', null, ['class' => 'control-label'])}}
            {{Form::select('tipo_via_', array('1' => 'Oral','2' => 'SNY','3' => 'SNG', '4' => 'Parental', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_via_', 'placeholder' => 'Seleccione'))}}
        </div>
    </div>
    <div class="col-md-8 col-md-offset-1" id="opcion_otro_via_" hidden>
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('detalle_via_', null, array('id' => 'detalle_via_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_consistencia" style="margin-left: auto;">
    {{-- REGIMEN -> CONSISTENCIA --}}
    <div class="col-md-2" id="opcion_consistencia_">
        <div class="form-group">
            {{Form::label('Tipo Consistencia', null, ['class' => 'control-label'])}}
            {{Form::select('tipo_consistencia_', array('1' => 'Líquido','2' => 'Blando','3' => 'Papillas', '4' => 'Común', '5' => 'Otro'), null, array('class' => 'form-control', 'id' => 'tipo_consistencia_', 'placeholder' => 'Seleccione'))}}
        </div>
    </div>
    {{-- REGIMEN -> CONSISTENCIA -> OTRO --}}
    <div class="col-md-8 col-md-offset-1" id="opcion_otro_consistencia_" hidden>
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('detalle_consistencia_', null, array('id' => 'detalle_consistencia_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_tipo" style="margin-left: auto;">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label">Tipo:</label>
            {{ Form::select('tipos_[]', array('1' => 'Hiposódico','2' => 'Hipocalórico','3' => 'Hipograso','4' => 'Hipoglúcido','5' => 'Liviano','6' => 'Sin residuos','7' => 'Rico en fibra','8' => 'Común', '9' => 'Otro'), null, array('id' => 'tipos_','style' => 'backgroundColor:#000 !important', 'class' => 'selectpicker form-control', 'multiple', 'required', 'data-max-options'=>'8','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 8 especialidades permitidas</label>&quot;]")) }}
            {{ Form::text('tipos_item_', "0", array('class' => 'form-control ', "id" => "tipos_item_", "style" => "height:0px !important; padding:0; border:0px;")) }}
        </div>
    </div>
    <div class="col-md-8 col-md-offset-1" id="opcion_otro_tipo_tipo_" hidden>
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('detalle_tipo_otro_', null, array('id' => 'detalle_tipo_otro_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_volumen" style="margin-left: auto;">
    <div class="col-md-11">
        <div class="form-group">
            {{Form::label('Volumen', null, ['class' => 'control-label'])}}
            {{Form::text('volumen_', null, array('id' => 'volumen_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_signos_vitales" style="margin-left: auto;">
    <br>
    <legend>Control de signos vitales</legend>
    <div class="col-md-2"> 
        <div class="form-group">
            {{Form::label('Cada cuantas horas', null, ['class' => 'control-label'])}}
            {{Form::number('horas_signos_vitales_', null, array('id' => 'horas_signos_vitales_', 'class' => 'form-control', 'min' => 0, 'placeholder' => 'cada x horas'))}}
        </div>        
    </div>
    <div class="col-md-8 col-md-offset-1"> 
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('detalle_signos_vitales_', null, array('id' => 'detalle_signos_vitales_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_hemoglucotest" style="margin-left: auto;">
    <br>
    <legend>Control de hemoglucotest</legend>
    <div class="col-md-2">
        <div class="form-group">
            {{Form::label('Cada cuantas horas', null, ['class' => 'control-label'])}}
            {{Form::number('horas_hemoglucotest_', null, array('id' => 'horas_hemoglucotest_', 'class' => 'form-control', 'min' => 0, 'placeholder' => 'cada x horas'))}}
        </div>
    </div>
    <div class="col-md-8 col-md-offset-1"> 
        <div class="form-group">
            {{Form::label('Comentario', null, ['class' => 'control-label'])}}
            {{Form::text('detalle_hemoglucotest_', null, array('id' => 'detalle_hemoglucotest_', 'class' => 'form-control'))}}
        </div>
    </div>
</div>

<div class="row" id="div_oxigeno" style="margin-left: auto;">
    <br>
    <legend>Oxigeno para saturar</legend>
    <div class="col-md-2">
        <div class="form-group">
            {{Form::label('Especifique', null, ['class' => 'control-label'])}}
            {{Form::number('oxigeno_', null, array('id' => 'oxigeno_', 'class' => 'form-control', 'min' => 0))}}
        </div>
    </div>
    <div class="col-md-1"><br><br>%</div>    
</div>

<div class="row" id="div_sueros" style="margin-left: auto;">
    <br>
    <legend>Suero</legend>
    <div>
        <div class="col-md-2"> 
            <div class="form-group radioButtonSuero" >
                {{Form::label('', "Especifique", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
                <br>
                <label class="radio-inline">{{Form::radio('sueros_', "no", false, array('required' => true))}}No</label>
                <label class="radio-inline">{{Form::radio('sueros_', "si", false, array('required' => true))}}Sí</label>
            </div>
        </div>
        <div class="col-md-4 col-md-offset-1 listado_suero_ hidden">
            <div class="form-group">
                {{Form::label('Sueros', null, ['class' => 'control-label'])}}
                {{Form::select('suero_', $sueros, null, array('id' => 'suero_','style' => 'backgroundColor:#000 !important','class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}}
                {{ Form::text('suero_item_', "0", array('class' => 'form-control ', "id" => "suero_item_", "style" => "height:0px !important; padding:0; border:0px;")) }}
            </div>
        </div>
        <div class="col-md-2 col-md-offset-1 listado_suero_ hidden">
            <div class="form-group">
                {{Form::label('Mililitro (ml) total', null, ['class' => 'control-label'])}}
                {{Form::number('mililitro_', null, array('id' => 'mililitro_', 'class' => 'form-control', 'min' => 0))}}
            </div>
        </div>
    </div>
</div>

<div class="row" id="div_farmacos" style="margin-left: auto;">
    <br>
    <legend>Farmacos</legend>
    <div class="farmacosExtras_" id="farmacosExtras_">
        <div class="col-md-4">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('Nombre', null, ['class' => 'control-label'])}}
                    {{ Form::hidden('id_farmaco_[]', '', array('id' => 'id_farmaco_0')) }}
                    {{Form::select('nombre_farmaco_[]', $farmacos, null, array('id' => 'nombre_farmaco_0', 'class' => 'form-control'/* , 'required' */,'placeholder' => 'seleccione'))}}
                    {{-- {{Form::select('nombre_farmaco_[]', $farmacos, null, array('id' => 'nombre_farmaco0','style' => 'backgroundColor:#000 !important', 'class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}} --}}
                    {{-- {{Form::select('suero_', $sueros_, null, array('id' => 'suero_','style' => 'backgroundColor:#000 !important','class' => 'form-control selectpicker', 'required', 'data-live-search' => 'true', 'placeholder' => 'seleccione'))}} --}}
                </div>
            </div> 
            
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('Vía de administración', null, ['class' => 'control-label'])}}
                    {{ Form::select('via_administracion_[]', array('Oral' => 'Oral', 'Sublingual' => 'Sublingual', 'Tópica' => 'Tópica', 'Transdérmica' => 'Transdérmica', 'Oftalmológica' => 'Oftalmológica', 'Inhalatoria' => 'Inhalatoria', 'Rectal' => 'Rectal', 'Vaginal' => 'Vaginal', 'Intravenosa' => 'Intravenosa', 'Intramuscular' => 'Intramuscular', 'Subcutánea' => 'Subcutánea', 'Intradérmica' => 'Intradérmica', 'Ótica' => 'Ótica', 'Nasal' => 'Nasal'), null, array( 'class' => 'form-control', 'id' => 'via_administracion_0')) }}
                </div>
            </div> 
        </div>
        <div class="col-md-1">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('Intervalo', null, ['class' => 'control-label'])}}
                    {{Form::number('intervalo_farmaco_[]', null, array('id' => 'intervalo_farmaco_0', 'class' => 'form-control', 'min' => 0))}}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('Detalle', null, ['class' => 'control-label'])}}
                    {{Form::text('detalle_farmaco_[]', null, array('id' => 'detalle_farmaco_0', 'class' => 'form-control'))}}
                </div>
            </div> 
        </div>
    </div>
    <div class="col-md-1">
        <br>
        <button type="button" class="btn btn-primary agregarFarmacosExtras_" >+</button>
    </div>
    <div class="col-md-12 FarmacosCopia_ pl-0 pr-0" id="FarmacosCopia_"></div>
</div>

<div class="row" id="div_atencion_terapeutica" style="margin-left: auto;">
    <br>
    <legend>Atención terapéutica</legend>
    <div class="col-md-4">
        <div class="form-group">
            <input type="checkbox" name="atencion_terapeutica_[]" value="1">Kinesiterapia motora</br>
            <input type="checkbox" name="atencion_terapeutica_[]" value="2">Kinesiterapia respiratoria</br>
            <input type="checkbox" name="atencion_terapeutica_[]" value="3">Atención Fonoaudiológica</br>
            <input type="checkbox" name="atencion_terapeutica_[]" value="4">Atención por Terapeuta ocupacional</br>
            <input type="checkbox" name="atencion_terapeutica_[]" value="5">Evaluación nutricional</br>
        </div>
    </div>
</div>

<div class="row primera_indicacion_ hidden" style="margin-left: auto;">
    <br>
    <legend>Escala de prevención de trombosis venosa</legend>
    <div>
        <div class="col-md-4"> 
            <div class="form-group">
                {{Form::label('', "Padua", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
                <br>
                <label class="radio-inline">{{Form::radio('padua_', "no", false, array('required' => true))}}No</label>
                <label class="radio-inline">{{Form::radio('padua_', "si", false, array('required' => true))}}Sí</label>
            </div>
        </div>
        <div class="col-md-4 col-md-offset-1">
            <div class="form-group">
                {{Form::label('Caprini', null, ['class' => 'control-label'])}}<br>
                <label class="radio-inline">{{Form::radio('caprini_', "no", false, array('required' => true))}}No</label>
                <label class="radio-inline">{{Form::radio('caprini_', "si", false, array('required' => true))}}Sí</label>
            </div>
        </div>
    </div>
</div>

<div class="row" id="div_campos_extras" style="margin-left: auto">
    <br>
    <div class="camposExtras_" id="camposExtras_">
        <div class="col-md-10"> 
            <div class="form-group">
                <label class="control-label">Otro</label>
                {{ Form::hidden('id_comentario_[]', '', array('id' => 'id_comentario_0')) }} 
                {{Form::text('campoExtra_[]', null, array( 'class' => 'campoExtra_ form-control', 'placeholder' => 'Ingrese comentario','id'=>'campoExtra_0'))}} 
            </div>
        </div>
    </div>
    <div class="col-md-1">
        <br>
        <button type="button" class="btn btn-primary agregarCamposExtras_" >+</button>
    </div>
    <div class="col-md-12 clonAgregarCamposExtras_" id="clonAgregarCamposExtras_" style="margin-left: -13px;"></div>
</div>