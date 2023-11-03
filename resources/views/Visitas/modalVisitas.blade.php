<div class="row">
    <div class="col-md-4" style="">
        <div class="form-group col-md-12">
            {{Form::label('', "¿Puede recibir visitas?", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
            <br>
            <label class="radio-inline">{{Form::radio('recibe_visitas', "no", false, array('required' => true))}}No</label>
            <label class="radio-inline">{{Form::radio('recibe_visitas', "si", false, array('required' => true))}}Sí</label>
        </div>
    </div>

    <div class="form-group col-md-4 div-recibe-visitas" hidden>
        <div class="col-md-12">
            <label for="recibe_visitas" class="control-label" title="Motivo">Cantidad de personas: </label>
            {{Form::number('cantidad_personas', null, array('id' => 'cantidad_personas', 'class' => 'form-control', 'min' => '1', 'enabled', "style" => "width:100%"))}}
        </div>
    </div>
    <div class="form-group col-md-4 div-recibe-visitas" hidden>
        <div class="col-md-12">
            <label for="recibe_visitas" class="control-label" title="Motivo">Cantidad de horas: </label>
            {{Form::number('cantidad_horas', null, array('id' => 'cantidad_horas', 'class' => 'form-control', 'min' => '1', 'enabled', "style" => "width:100%"))}}
        </div>
    </div>
    <div id="input_comentario_visitas" hidden>
        <div class="col-md-6 form-group">
            <label class="control-label">Comentario</label><br>
            <input type="text" name="comentario_visitas" id="comentario_visitas" class="form-control">
        </div>
    </div>
</div>