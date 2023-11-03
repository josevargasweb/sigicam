<style>
    .espacio_cal{
        width: 12.333333%;
    }
    .espacio_cal2{
        width: 20.66666667%;
    }
    .espacio_cal3{
        width: 26%;
    }
    .espacio_div{
        margin-left: 15px;
    }
    .modal {
        overflow-y:auto;
    }
</style>
<div class="panel panel-default">
  <div class="panel-heading panel-info">
      <h4>Solicitud De Interconsulta o Derivación:</h4>
  </div>


  <div class="panel-body">
  <legend>Solicitud</legend>
      <div class="row">
          <div class="col-md-12">
              <div class="col-md-6 form-group">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Servicio de Salud</label>
                  {{Form::text('servicio_interconsulta', '', array('id' => 'servicio_interconsulta', 'class' => 'form-control','disabled'))}}
              </div>
              <div class="col-md-6 form-group comuna">
                  <div class="col-md-12 pr-0">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Establecimiento</label>
                      {{Form::text('establecimiento_interconsulta', '', array('id' => 'establecimiento_interconsulta', 'class' => 'form-control','disabled'))}}
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <div class="col-md-6 form-group">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Especialidad</label>
                  {{ Form::select('especialidad_interconsulta', App\Models\EspecialidadMedica::getEspecialidadesMedicas(), null, array('class' => 'form-control selectpicker', 'id' => 'especialidad_interconsulta',"placeholder" => "seleccione")) }}
              </div>
              <div class="col-md-6 form-group comuna">
                  <div class="col-md-12 pr-0">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Unidad</label>
                      {{Form::text('unidad_interconsulta', '', array('id' => 'unidad_interconsulta', 'class' => 'form-control','disabled'))}}
                  </div>
              </div>
          </div>
      </div>
      <legend>Antecedentes del paciente</legend>
      <div class="row">
          <div class=" col-md-12 pl-0">
              <div class="col-md-6">
                    <div class="col-sm-10 pl-0">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Rut</label>
                        <div class="input-group" style="z-index: 0;">
                            {{Form::text('rut', null, array('id' => 'paciente_rut_interconsulta', 'class' => 'form-control','disabled'))}}
                            <span class="input-group-addon"> - </span>
                            {{Form::text('dv', null, array('id' => 'paciente_dv_interconsulta', 'class' => 'form-control', 'style' => 'width: 70px;','disabled'))}}
                        </div>
                    </div>
              </div>
              <div class="col-md-6 pl-0">
                  <div class="col-md-12 pl-0 form-group" >
                      <label for="incontinencia" class="control-label" title="Incontinencia">Nombre (*)</label>
                      {{Form::text('paciente_nombre_interconsulta', '', array('id' => 'paciente_nombre_interconsulta', 'class' => 'form-control','disabled'))}}
                </div>
              </div>
              <div class="col-md-12 pl-0 pr-0">
                  <div class="col-md-6 form-group">
                      <div class="col-md-12 ">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Apellido Paterno</label>
                            {{Form::text('paciente_apellidoPat_interconsulta', '', array('id' => 'paciente_apellidoPat_interconsulta', 'class' => 'form-control','disabled'))}}
                      </div>
                  </div>
                  <div class="col-md-6 pr-0">
                      <div class="col-md-12 pl-0 pr-0 form-group">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Apellido Materno</label>
                            {{Form::text('paciente_apellidoMat_interconsulta', '', array('id' => 'paciente_apellidoMat_interconsulta', 'class' => 'form-control','disabled'))}}
                      </div>
                  </div>
              </div>
              <div class="col-md-12">
                    <div class="col-md-4 pl-0">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Género</label>
                            {{ Form::select('paciente_sexo_interconsulta', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'paciente_sexo_interconsulta', 'class' => 'form-control','disabled')) }}
                    </div>
                    <div class="col-sm-4 pl-0">
                            <div class="col-md-12 pr-0 pl-0">
                                <label for="incontinencia" class="control-label" title="Incontinencia">Fecha de nacimiento</label>
                            </div>
                            {{Form::text('paciente_fecha_interconsulta', '', array('id' => 'paciente_fecha_interconsulta', 'class' => 'form-control','disabled'))}}
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Edad</label>
                        {{Form::text('paciente_edad_interconsulta', '', array('id' => 'paciente_edad_interconsulta', 'class' => 'form-control','disabled'))}}
                    </div>
              </div>
          </div>
      </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-md-12">
                    <table class="table table-bordered" id="tablaTelefonos_interconsulta" class="ignoreTable">
                        <thead>
                            <tr>
                                <th>Indice</th>
                                <th>Tipo</th>
                                <th>Teléfono</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="Telefonos_interconsulta"></tbody>
                    </table>
                    <div class="btn btn-primary agregar_boton_interconsulta" id="addTelefono_interconsulta">+ Teléfono</div> 
                </div>
            </div>
            <br>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-md-12">
                    <legend>Datos de dirección</legend>
                </div>
                <div class="col-sm-4">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Calle</label>
                    {{Form::text('paciente_calle_interconsulta', '', array('id' => 'paciente_calle_interconsulta', 'class' => 'form-control'))}}
                </div>
                <div class="col-sm-2">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Número</label>
                    {{Form::number('paciente_numero_interconsulta', '', array('id' => 'paciente_numero_interconsulta', 'class' => 'form-control'))}}
                </div>
                <div class="col-sm-6">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Observación dirección</label>
                    {{Form::text('paciente_observacion_interconsulta', '', array('id' => 'paciente_observacion_interconsulta', 'class' => 'form-control'))}}
                </div>
            </div>
            <div class="form-group col-md-12">
                <div class="col-sm-6">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Región</label>
                    {{ Form::select('paciente_region_interconsulta', App\Models\Consultas::getRegion(), 3, array('id' => 'paciente_region_interconsulta', 'class' => 'form-control')) }}
                </div>
                <div class="col-sm-6" id="comunas_interconsulta">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Comuna</label>
                    {{Form::select('paciente_comuna_interconsulta',  App\Models\Comuna::where('id_region', '=', 3)->pluck('nombre_comuna','id_comuna'), 4, array('id' => 'paciente_comuna_interconsulta', 'class' => 'form-control'))}}
                </div>
            </div>
        </div>
      <br>
      <div class="row">
            <div class="col-md-12"> 
                <div class="col-md-12 pr-0 pl-0">
                    <legend>Datos Clinicos</legend>
                </div>
                <div class="col-md-12 pl-0">
                    <div class=" col-md-5 pl-0">
                        <div class="col-sm-12 form-group">
                            <label for="calle" class="control-label" title="Calle">Tipo de Centro: </label>
                            {{ Form::select('tipo_centro', array('derivacion'=>'1. Derivación a otro establecimiento de la red pública','traslado extra sistema'=>'2. Derivación a institución privada'), null, ['class' => 'form-control', "id" => "select-tipo-centro", "placeholder" => "seleccione"]) }}
                        </div>
                    </div>
                    <div class="form-group col-md-5" style="margin-left:55px;">
                        <div class="col-sm-12">
                            <div class="col select_red_publica" hidden>
                                <label for="telefono" class="control-label" title="Nombre Social">Centro de Derivación: </label>
                                {{ Form::select('red_publica', App\Models\Establecimiento::getEstablecimientos() + ['0' => 'otros'], null, array('class' => 'form-control selectpicker', "id" => "red_publica", 'data-live-search="true"')) }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col select_red_privada" hidden>
                                <label for="telefono" class="control-label" title="Nombre Social">Centro de Derivación: </label>
                            {{ Form::select('red_privada', App\Models\EstablecimientosExtrasistema::getEstablecimiento(), null, array('class' => 'form-control selectpicker', "id" => "red_privada", 'data-live-search="true"')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                        <div class="col-md-6 pl-0 form-group">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Especialidad derivación</label>
                        {{ Form::select('especialidad_interconsulta_dirigido', App\Models\EspecialidadMedica::getEspecialidadesMedicas(), null, array('class' => 'form-control selectpicker', 'id' => 'especialidad_interconsulta_dirigido',"placeholder" => "seleccione")) }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-md-6 pr-0 pl-0 form-group">
                        <label class="radio-inline">{{Form::radio('datos_clinicos_interconsulta', "confirmacion Diagnóstica", false, array('id'=>'confirmacion_diagnostico_interconsulta__interconsulta'))}}Confirmación Diagnostico</label>
                        <label class="radio-inline">{{Form::radio('datos_clinicos_interconsulta', "paciente en tratamiento", false, array('id'=>'paciente_tratamiento_interconsulta'))}}Paciente en tratamiento</label>
                        <label class="radio-inline" style="margin-left: 0px;">{{Form::radio('datos_clinicos_interconsulta', "realizar Tratamiento", false, array('id'=>'realizar_tratamiento_interconsulta'))}}Realizar Tratamiento</label>
                        <label class="radio-inline">{{Form::radio('datos_clinicos_interconsulta', "otro", false, array('id'=>'paciente_otro_interconsulta'))}}Otro</label>
                    </div>
                    <div class="col-md-6 pr-0 form-group datos_clinicos_interconsulta_otro">
                        <label for="" class="control-label">Especifique</label>
                        {{Form::text('datos_clinicos_interconsulta_otro', '', array('id' => 'datos_clinicos_interconsulta_otro', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-sm-12 pl-0 pr-0" style="margin-bottom:15px;">
                    <div class="col-md-12 pl-0 pr-0">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Hipótesis diagnóstica o diagnóstico</label>
                    </div>
                    <div class="col-md-5 pl-0 select_diagnostico_interconsulta">
                        {{ Form::select('seleccion_diagnostico_interconsulta[]', array(), null, array('class' => 'form-control selectpicker', 'id' => 'seleccion_diagnostico_interconsulta', 'multiple')) }}
                    </div>
                </div>
                <div class="col-md-12 pl-0 pr-0 info_data_diagnostico" hidden>
                    <div class="alert alert-warning" role="alert">No hay diagnosticos para elegir</div>
                </div>
                <div class="col-sm-12 pl-0 pr-0 datos_diagnosticos_interconsulta" hidden>
                    <div class="col-md-6 pl-0 pr-0">
                        <span>Diagnóstico</span>
                    </div>
                    <div class="col-md-5 col-md-offset-1 pl-0 pr-0">
                        <span >Comentario</span>
                    </div>
                    <div id="moduloDiagnosticoInterconsulta">
                        <div class="col-md-6 pl-0 pr-0 form-group">
                            <div class="col-md-12">
                                {{Form::text('diagnostico_interconsulta[]', '', array('id' => 'diagnostico_interconsulta0', 'class' => 'form-control typeahead','disabled'))}}
                                <input type="hidden" value="" name="id_diagnostico_interconsulta[]" id="id_diagnostico_interconsulta0">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="col-md-12 col-md-offset-1">
                                <div class="form-group"> 
                                    {{Form::text('comentario_diagnostico_interconsulta[]', '', array('id' => 'comentario_diagnostico_interconsulta0', 'class' => 'form-control','disabled'))}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="moduloDiagnosticoInterconsultacopia"></div>
                </div>
            </div>
      </div>
      <div class="row">
            <div class="col-md-12">
                <div class="col-sm-12 form-group">
                    <div class="col-md-12 pl-0 pr-0">
                        <label for="">¿Sospecha problema de salud AUGE?</label>
                    </div>
                    <div class="col-md-12 pr-0 pl-0">
                        <label class="radio-inline">{{Form::radio('auge_interconsulta', "si", false, array('id'=>'si_auge_interconsulta'))}}Si</label>
                        <label class="radio-inline">{{Form::radio('auge_interconsulta', "no", false, array('id'=>'no_auge_interconsulta'))}}No</label>
                    </div>
                </div> 
                <div class="col-md-12 pl-0 especificar_problema_interconsulta">
                    <div class="col-sm-12 pr-0 form-group">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Especificar Problema</label>
                        {{Form::textarea('especificar_problema_interconsulta', '', array('id' => 'especificar_problema_interconsulta', 'class' => 'form-control','rows' => '3'))}}
                    </div>
                </div>
            </div>
          <div class="col-md-12">
              <div class="col-sm-12 form-group">
                  <label for="fechaDiagGes" class="control-label" title="fechaDiagGes">Subgrupo o subprograma de salud AUGE (si corresponde)</label>
                  {{Form::textarea('programa_auge_interconsulta', '', array('id' => 'programa_auge_interconsulta', 'class' => 'form-control','rows' => '3'))}}
              </div>
          </div>
          <div class="col-md-12">
              <div class="col-sm-12 form-group">
                  <label for="fechaDiagGes" class="control-label" title="fechaDiagGes">Fundamentos del diagnóstico</label>
                  {{Form::textarea('fund_diagnostico_interconsulta', '', array('id' => 'fund_diagnostico_interconsulta', 'class' => 'form-control','rows' => '3'))}}
              </div>
          </div>
          <div class="col-md-12">
              <div class="col-sm-12 form-group">
                  <label for="fechaDiagGes" class="control-label" title="fechaDiagGes">Examenes realizados</label>
                  {{Form::textarea('examenes_realizados_interconsulta', '', array('id' => 'examenes_realizados_interconsulta', 'class' => 'form-control','rows' => '3'))}}
              </div>
          </div>
      </div>
      <legend>Datos del profesional</legend>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 form-group medicos_interconsulta">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Nombre persona que notifica</label>
                        {{Form::text('medico_interconsulta', null, array('id' => 'medico_interconsulta', 'class' => 'form-control typeahead','disabled'))}}
                        {{Form::hidden('id_medico_interconsulta', null, array('id' => 'id_medico_interconsulta'))}}
                </div>
                <div class="col-md-6 form-group">
                    <div class="col-md-12 ">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Rut</label>
                            <div class="input-group" style="z-index: 0;">
                                {{Form::text('rut_medico_interconsulta', null, array('id' => 'rut_medico_interconsulta', 'class' => 'form-control','disabled'))}}
                                <span class="input-group-addon"> - </span>
                                {{Form::text('dv_medico_interconsulta', null, array('id' => 'dv_medico_interconsulta', 'class' => 'form-control', 'style' => 'width: 70px;','disabled'))}}
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<input id="guardarInterconsulta" type="submit" name="" class="btn btn-primary" value="Guardar">
<br><br>
<script>
    var counterDiagnosticosInterConsulta = 1;
    
    function agregarDiagnosticoInterconsulta(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloDiagnosticoInterconsulta");
        var cloneDiv = originalDiv.clone();    

        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloDiagnosticoInterconsulta'+counterDiagnosticosInterConsulta);
  
        $("[name='diagnostico_interconsulta[]']",cloneDiv).attr({'data-id':counterDiagnosticosInterConsulta,'id':'diagnostico_interconsulta'+counterDiagnosticosInterConsulta});    
        $("[name='diagnostico_interconsulta[]']",cloneDiv).val(''); 
  
        $("[name='comentario_diagnostico_interconsulta[]']",cloneDiv).attr({'data-id':counterDiagnosticosInterConsulta,'id':'comentario_diagnostico_interconsulta'+counterDiagnosticosInterConsulta});    
        $("[name='comentario_diagnostico_interconsulta[]']",cloneDiv).val(''); 

        $("[name='id_diagnostico_interconsulta[]']",cloneDiv).attr({'data-id':counterDiagnosticosInterConsulta,'id':'id_diagnostico_interconsulta'+counterDiagnosticosInterConsulta});    
        $("[name='id_diagnostico_interconsulta[]']",cloneDiv).val(''); 

        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloDiagnosticoInterconsultacopia").append(cloneDiv);

        $('#interconsultaEditForm').bootstrapValidator('addField', cloneDiv.find("[name='diagnostico_interconsulta[]']"));
        // $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='antibioticoCultivo[]']"));
        // $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='locacionCultivo[]']"));

        //incrementa el contador
        counterDiagnosticosInterConsulta++;      
	};  
</script>