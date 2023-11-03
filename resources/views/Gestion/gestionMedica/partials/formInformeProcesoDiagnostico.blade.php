<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                <label for="num_folio" class="control-label">FOLIO N°</label>
                {{Form::text('num_folio', null, array('id' => 'num_folio', 'class' => 'form-control'))}}
            </div>
        </div>
        <div class="col-md-3 col-md-offset-4">
            <div class="form-group">
                <label for="nombre">FECHA INFORME</label>
                {{Form::text('fecha_informe', null, array('id' => 'fecha_informe', 'class' => 'form-control dtp_fechasInforme'))}}
            </div>
        </div>
    </div>
</div>
<br>
<div class="panel panel-default">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <label for="">1. Servicio de Salud </label>
                <p id="nombre_servicio_salud"></p>
            </div>
            <div class="col-md-4 col-md-offset-3">
                <div class="form-group">
                    <label for="">2. Establecimiento</label>
                    <p id="establecimiento"></p>
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-md-4">
                <label for="">3. Especialidad</label>
                {{Form::text('especialidad', null, array('id' => 'especialidad', 'class' => 'form-control'))}}
            </div>
            <div class="col-md-4 col-md-offset-3">
                <div class="form-group">
                    <label for="">4. Unidad</label>
                    <p id="unidad"></p>
                </div>
            </div>
        </div>
    </div>
</div>                        
<br>
<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4 class="modal-title">DATOS DEL (DE LA) PACIENTE</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <label for="">5. Nombre</label>
                <p id="nombre_paciente"></p>
            </div>
            <div class="col-md-4 col-md-offset-3">
                <div class="form-group">
                    <label for="">6. Historia clinica</label>
                    {{Form::text('historia_clinica', null, array('id' => 'historia_clinica', 'class' => 'form-control'))}}
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3">
                <label for="">7. RUT.</label>
                <p id="rut_paciente"></p>
            </div>
            <div class="col-md-3 col-md-offset-2">
                <br>
                <p>8. Si es recien nacido, RUT de padre o madre beneficiario</p>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="rut_beneficiario" class="control-label" title="Rut">Run: </label>
                    <div class="input-group" style="z-index: 0;">
                        {{Form::text('rut_beneficiario', null, array('id' => 'rut_beneficiario', 'class' => 'form-control'))}}
                        <span class="input-group-addon"> - </span>
                        {{Form::text('dv_beneficiario', null, array('id' => 'dv_beneficiario', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-3">
                <div class="form-group">
                    {{Form::label("9. Sexo", null, array( 'class' => 'control-label'))}}
                    {{Form::select('sexo', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'sexo', 'class' => 'form-control', 'placeholder' => 'Seleccione')) }}
                </div>
            </div>
            <div class="col-md-3 col-md-offset-3">
                <label for="fecha_nacimiento">10. Fecha de Nacimiento</label>
                <p id="fecha_nacimiento"></p>
            </div>
            <div class="col-md-3">
                <label for="edad">11. Edad</label>
                <p id="edad"></p>
            </div>
        </div>
    </div>
</div>
<br>
<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4 class="modal-title">Datos Clinicos</h4>
    </div>
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">12. Problema de salud AUGE</label>
                    <textarea class="form-control" name="problema_saluda_auge" id="problema_saluda_auge" cols="5" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <div class="form-group">
                    <label for="">13. ¿Confirma que el diagnostico pertenece al sistema AUGE?</label><br>
                    <label class="radio-inline">{{Form::radio('confirmacion_auge', "no", false, array('id' => 'confirmacion_auge'))}}No</label>
                    <label class="radio-inline">{{Form::radio('confirmacion_auge', "si", false, array('id' => 'confirmacion_auge'))}}Sí</label>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-11">
                <div class="form-group">
                    <label for="">14. Subgrupo o subcategoria de salud AUGE</label>
                    <textarea class="form-control" name="subgrupo_salud_auge" id="subgrupo_salud_auge" cols="5" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-11">
                <div class="form-group">
                    <label>15. Diagnósticos</label>
                    <li class="list-group-item list-group-item-success" id="diagnostico_informe"></li>
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-11">
                <div class="form-group">
                    <label for="">16. Fundamentos del diagnóstico</label>
                    <textarea class="form-control" name="fundamentos_diagnostico" id="fundamentos_diagnostico" cols="5" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <hr id="hr_menos_separacion">
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-11">
                <div class="form-group">
                    <label for="">17. Tratamiento e indicaciones</label>
                    <textarea class="form-control" name="tratamiento_indicaciones" id="tratamiento_indicaciones" cols="5" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-left: auto;">
        <div class="col-md-12">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">18. El tratamiento deberá iniciarse a más tardar el</label>
                    {{Form::text('fecha_inicio_tratamiento', null, array('id' => 'fecha_inicio_tratamiento', 'class' => 'form-control dtp_fechasInforme'))}}
                </div>
            </div>
        </div>
    </div>
</div>