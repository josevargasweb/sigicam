@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

<style>
        .formulario > .panel-default > .panel-heading {
			background-color: #bce8f1 !important;
		}

		.tt-input{
			width:100%;
		}

		.tt-query {
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		}

		.tt-hint {
		color: #999
		}

		.tt-menu {    /* used to be tt-dropdown-menu in older versions */
		/*width: 430px;*/
		margin-top: 4px;
		/* padding: 4px 0;*/
		background-color: #fff;
		border: 1px solid #ccc;
		border: 1px solid rgba(0, 0, 0, 0.2);
		-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
				border-radius: 4px;
		-webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
			-moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
				box-shadow: 0 5px 10px rgba(0,0,0,.2);
			overflow-y: scroll;
			max-height: 350px;
		}

		.tt-suggestion {
		/* padding: 3px 20px;*/
		line-height: 24px;
		}

		.tt-suggestion.tt-cursor,.tt-suggestion:hover {
		color: #fff;
		background-color: #1E9966;
		}

		.tt-suggestion p {
		margin: 0;
		}
		.twitter-typeahead{
			width:100%;
		}

		#dynamicTable tbody{
			counter-reset: Serial;           
		}

		table #dynamicTable{
			border-collapse: separate;
		}

		#dynamicTable tr td:first-child:before{
			counter-increment: Serial;      
			content: counter(Serial); 
		}
        .modal {
        overflow-y:auto;
    }
</style>

<div class="panel panel-default">
  <div class="panel-heading panel-info">
      <h4>Formulario de constancia información al pacientes GES:</h4>
  </div>


  <div class="panel-body">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6 form-group">
                <label for="incontinencia" class="control-label" title="Incontinencia">Diagnóstico CIE-10</label>
                {{Form::text('diagnostico_ges', '', array('id' => 'diagnostico_ges', 'class' => 'form-control','disabled'))}}
            </div>
            <div class="col-md-6 form-group comuna">
                <div class="col-md-12 pr-0">
                    <label for="incontinencia" class="control-label" title="Incontinencia">Comentario</label>
                    {{Form::text('diagnostico_comentario_ges', '', array('id' => 'diagnostico_comentario_ges', 'class' => 'form-control','disabled'))}}
                </div>
            </div>
        </div>
    </div>
    <legend>Datos prestador</legend>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-md-12">
                  <label for="estado_mental" class="control-label" title="Estado mental">Nombre de la institución</label>
                  {{Form::text('establecimiento_medico', '', array('id' => 'establecimiento_medico', 'class' => 'form-control','disabled'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <div class="col-md-6 form-group">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Dirección</label>
                  {{Form::text('medico_direccion_ges', '', array('id' => 'medico_direccion_ges', 'class' => 'form-control','disabled'))}}
              </div>
              <div class="col-md-6 form-group comuna">
                  <div class="col-md-12 pr-0">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Ciudad</label>
                      {{Form::text('medico_ciudad_ges', '', array('id' => 'medico_ciudad_ges', 'class' => 'form-control','disabled'))}}
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <div class="col-md-6 form-group medicos">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Nombre persona que notifica</label>
                    {{Form::text('medicoAltaGes', null, array('id' => 'medicoAltaGes', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('id_medico_ges', null, array('id' => 'id_medico_ges'))}}
              </div>
              <div class="col-md-6 form-group">
                  <div class="col-md-12 ">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Rut</label>
                        <div class="input-group" style="z-index: 0;">
                            {{Form::text('rut_medico_ges', null, array('id' => 'rut_medico_ges', 'class' => 'form-control'))}}
                            <span class="input-group-addon"> - </span>
                            {{Form::text('dv_medico_ges', null, array('id' => 'dv_medico_ges', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                        </div>
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
                            {{Form::text('rut', null, array('id' => 'paciente_rut_ges', 'class' => 'form-control','disabled'))}}
                            <span class="input-group-addon"> - </span>
                            {{Form::text('dv', null, array('id' => 'paciente_dv_ges', 'class' => 'form-control', 'style' => 'width: 70px;','disabled'))}}
                        </div>
                    </div>
              </div>
              <div class="col-md-6 pl-0">
                  <div class="col-md-12 pl-0 form-group" >
                      <label for="incontinencia" class="control-label" title="Incontinencia">Nombre (*)</label>
                      {{Form::text('paciente_nombre_ges', '', array('id' => 'paciente_nombre_ges', 'class' => 'form-control','disabled'))}}
                </div>
              </div>
              <div class="col-md-12 pl-0 pr-0">
                  <div class="col-md-6 form-group">
                      <div class="col-md-12 ">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Apellido Paterno</label>
                            {{Form::text('paciente_apellidoPat_ges', '', array('id' => 'paciente_apellidoPat_ges', 'class' => 'form-control','disabled'))}}
                      </div>
                  </div>
                  <div class="col-md-6 pr-0">
                      <div class="col-md-12 pl-0 pr-0 form-group">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Apellido Materno</label>
                            {{Form::text('paciente_apellidoMat_ges', '', array('id' => 'paciente_apellidoMat_ges', 'class' => 'form-control','disabled'))}}
                      </div>
                  </div>
              </div>
              <div class="col-md-12">
                 
                  <div class="col-md-4 pl-0">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Género</label>
                        {{ Form::select('paciente_sexo_ges', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'paciente_sexo_ges', 'class' => 'form-control','disabled')) }}
                  </div>
                  <div class="col-sm-4 pl-0">
                        <div class="col-md-12 pr-0 pl-0">
                            <label for="incontinencia" class="control-label" title="Incontinencia">Aseguradora</label>
                        </div>
                        {{ Form::select('paciente_prevision_ges', App\Models\Prevision::getPrevisiones(), 'SIN INFORMACION', array('id' => 'paciente_prevision_ges', 'class' => 'form-control','disabled')) }}
                  </div>
                    <div class="col-md-4 form-group">
                        <label for="incontinencia" class="control-label" title="Incontinencia">Correo electronico (E-mail)</label>
                        {{Form::email('paciente_correo_ges', '', array('id' => 'paciente_correo_ges', 'class' => 'form-control'))}}
                    </div>
              </div>
          </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
            <div class="col-md-12">
                <table class="table table-bordered" id="tablaTelefonos_ges" class="ignoreTable">
                    <thead>
                        <tr>
                            <th>Indice</th>
                            <th>Tipo</th>
                            <th>Teléfono</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="Telefonos_ges"></tbody>
                </table>
                <div class="btn btn-primary agregar_boton" id="addTelefono_ges">+ Teléfono</div> 
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
                  {{Form::text('paciente_calle_ges', '', array('id' => 'paciente_calle_ges', 'class' => 'form-control'))}}
              </div>
              <div class="col-sm-2">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Número</label>
                  {{Form::number('paciente_numero_ges', '', array('id' => 'paciente_numero_ges', 'class' => 'form-control'))}}
              </div>
              <div class="col-sm-6">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Observación dirección</label>
                  {{Form::text('paciente_observacion_ges', '', array('id' => 'paciente_observacion_ges', 'class' => 'form-control'))}}
              </div>
            
          </div>
          <div class="form-group col-md-12">
          <div class="col-sm-6">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Región</label>
                  {{ Form::select('paciente_region_ges', App\Models\Consultas::getRegion(), 3, array('id' => 'paciente_region_ges', 'class' => 'form-control')) }}
              </div>
          <div class="col-sm-6" id="comunas_ges">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Comuna</label>
                  {{Form::select('paciente_comuna_ges',  App\Models\Comuna::where('id_region', '=', 3)->pluck('nombre_comuna','id_comuna'), 4, array('id' => 'paciente_comuna_ges', 'class' => 'form-control'))}}
              </div>
          </div>
      </div>
      <br>
      <div class="row">
          <div class="col-md-6"> 
              <div class="col-md-12 pl-0 pr-0 form-group">
                  <div class="col-md-12 pr-0">
                      <legend>Antecedentes del paciente</legend>
                  </div>
                  <div class="col-sm-12 pr-0">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Confirmación diagnostico GES</label>
                      {{Form::textarea('paciente_antecedentes_ges', '', array('id' => 'paciente_antecedentes_ges', 'class' => 'form-control'))}}
                  </div>
              </div>
              <div class="col-sm-12 form-group">
                  <div class="col-md-12 pr-0 pl-0">
                  <label class="radio-inline">{{Form::radio('ant_conf', "confirmacion diagnostico", false, array('id'=>'confirmacion_tratamiento'))}}Confirmación Diagnostico</label>
                  <label class="radio-inline">{{Form::radio('ant_conf', "paciente tratamiento", false, array('id'=>'paciente_tratamiento'))}}Paciente en tratamiento</label>
                </div>
              </div>
          </div>
          <div class="col-md-6 pl-0">
              <div class="col-md-12">
                  <legend>Notificación</legend>
              </div>
              <div class="col-sm-12 form-group">
                  <label for="fechaDiagGes" class="control-label" title="fechaDiagGes">Fecha</label>
                  {{Form::text('fechaDiagGes', '', array('id' => 'fechaDiagGes', 'class' => 'form-control', 'placeholder' => ''))}}
              </div>
              <!-- <div class="col-sm-12 form-group">
                  <label for="horaDiagGes" class="control-label" title="horaDiagGes">Hora</label>
                  {{Form::text('horaDiagGes', '', array('id' => 'horaDiagGes', 'class' => 'form-control', 'placeholder' => 'HH:mm'))}}
              </div> -->
          </div>
      </div>
      <legend>Antecedentes del representante</legend>
      <p>* En caso que la persona que "tomó conocimiento" no sea el paciente. Identificar los siguientes datos:</p>
      <div class="row">
          <div class="col-md-12">
              <div class="col-sm-6 form-group">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Nombre completo</label>
                  {{Form::text('nombre_representante_ges', '', array('id' => 'nombre_representante_ges', 'class' => 'form-control'))}}
              </div>
              <div class="col-md-6 form-group">
                    <div class="col-sm-10">
                      <label for="rut_representante_ges" class="control-label" title="rut_representante_ges">Rut</label>
                        <div class="input-group" style="z-index: 0;">
                            {{Form::text('rut_representante_ges', null, array('id' => 'rut_representante_ges', 'class' => 'form-control'))}}
                            <span class="input-group-addon"> - </span>
                            {{Form::text('dv_representante_ges', null, array('id' => 'dv_representante_ges', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                        </div>
                    </div>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <div class="col-sm-6 form-group">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Teléfono contacto</label>
                  {{Form::text('telefono_representante_ges', '', array('id' => 'telefono_representante_ges', 'class' => 'form-control'))}}
              </div>
              <div class="col-md-6">
                  <div class="col-sm-12 form-group">
                      <label for="incontinencia" class="control-label" title="Incontinencia">Correo electrónico</label>
                      {{Form::email('correo_representante_ges', '', array('id' => 'correo_representante_ges', 'class' => 'form-control'))}}
                  </div>
              </div>
          </div>
      </div>
      
  </div>
</div>


<input id="guardarNova" type="submit" name="" class="btn btn-primary" value="Ingresar Información">

<script>
    $(document).ready( function() {
     
    });
</script>