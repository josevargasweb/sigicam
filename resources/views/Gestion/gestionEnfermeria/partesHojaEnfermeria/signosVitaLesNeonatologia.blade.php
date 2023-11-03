<input type="hidden" id="sub_categoria" name="sub_categoria" value="{{$sub_categoria}}">
<div class="col-md-12">

    <div class="col-md-2 texto" style="pointer-events: none;">
        {{Form::label('lbl_fecha_signo_vital', "FECHA", array( ))}}
        <div class="form-group"> {{Form::text('fecha_signo_vital', \Carbon\Carbon::now()->format('d-m-Y'), array( 'class' => 'form-control', 'id' => 'fecha_signo_vital', 'autocomplete' => 'off'))}} </div>
    </div>

    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_horario_signo_vital', "HORARIO", array( ))}}
        <div class="form-group"> {{Form::text('horario1', null, array( 'class' => 'dPSigno form-control', 'placeholder' => 'HH:mm', 'autocomplete' => 'off' , 'autofocus' => 'false','required'))}} </div>
    </div>

</div>

<div class="col-md-12">
    <div class="col-md-2 texto">
        {{Form::label('lbl_presion_arterial_sistolica', "P. Arterial Sis. (mmHg)", array( ))}}
        <div class="form-group">
            {{Form::number('arterial1', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_presion_arterial_diastolica', "P. Arterial Dias. (mmHg)", array( ))}}
        <div class="form-group">
            {{Form::number('arterial1dia', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_presion_arterial_media', "P. Arterial Media. (mmHg)", array( ))}}
        <div class="form-group">
            {{Form::number('arterial1media', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_pulso', "Frec. cardiaca (Lpm)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('pulso1', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-2 texto">
        {{Form::label('lbl_frec_respiratoria', "Frec. Respiratoria (Rpm)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('respiratoria1', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 texto col-md-offset-1 texto">
        {{Form::label('lbl_temp_axilo', "Temp. Axílar (°C)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('axilo1', null, array('step' => '0.1','class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_temp_rectal', "Temp. Rectal (°C)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('rectal', null, array( 'step' => '0.1','class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_saturacion', "Sat. de oxígeno (%)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('saturacion1', null, array( 'step' => '0.1','class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-2 texto">
        {{Form::label('lbl_hemoglucotest', "Hemoglucotest (mg/dl)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('hemoglucotest1', null, array('step' => '0.1', 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_estado_conciencia', "Estado de conciencia", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::select('estado_conciencia', array('1' => 'Activo', '2' => 'Hipotónico', '3' => 'Llora', '4' => 'Apnea', '5' => 'Rosado', '6' => 'Pálido'), null, array( 'id' => 'estado_conciencia', 'class' => 'form-control sele','placeholder' => 'Seleccione')) }}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_metodo_o2', "Metodo O2", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::select('metodo1', array('1' => 'Ventilación mecánica', '2' => 'Cánula vestibular', '3' => 'CPAP', '4' => 'NIPPV'), null, array( 'id' => 'metodo1', 'class' => 'form-control sele','placeholder' => 'Seleccione')) }}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_dolor', "Dolor (EVA)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('dolor1', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-2 texto">
        {{Form::label('lbl_fio2', "FIO2", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::select('fio1', array('21' => '21','24' => '24', '26' => '26', '28' => '28', '32' => '32', '35' => '35', '36' => '36', '40' => '40', '45' => '45', '50' => '50', '60' => '60', '70' => '70-80', '90' => '90-100'), null, array( 'id'=> 'fio1','class' => 'form-control sele','placeholder' => 'Seleccione')) }}
        </div>
    </div>
    <div class="col-md-2 col-md-offset-1 texto">
        {{Form::label('lbl_peso', "Peso (gr)", array( 'class' => ''))}}
        <div class="form-group">
            {{Form::number('peso', null, array( 'class' => 'form-control valor', 'min' => '0'))}}
        </div>
    </div>
</div>