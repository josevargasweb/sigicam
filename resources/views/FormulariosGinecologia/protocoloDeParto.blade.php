@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("section")

    <script>
        var caso_id = '{{$formulario_data->caso_id}}';
    </script>
    <!-- alertas de alergias logica-->
    {{ HTML::script('js/formularios_ginecologia/partograma-alergias-notificator.js') }}


    <style>
        .help-block {
            color: #a94442;
        }
		/*fieldset{
			border-style:solid;
			border-width:1px;
			border-color: black;
			min-height:100px;
			padding: 15px;
			margin-bottom:10px;
		}*/
		legend{
			font-size: 14px;
			display:inline-block;
			border:none;
			width:auto;
			text-transform: uppercase;
		}
		.recien_nacido .row{
			margin-bottom: 20px;
		}
		.nivel_atencion{
			min-height:138px;
		}
		.atendido .row div{
			min-height: 40px;
			line-height: 30px;
		}
		.invisible{
			width: 0px;
			visibility: hidden;
		}
		.descripcion{
			height:232px;
		}
		.panel-body .row{
			margin-bottom: 15px;
		}
    </style>

    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
    <br><br>

    {{ 
        Form::open(
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'protocolo_de_parto_form')
        ) 
    }}

    <br>
	<h2>Historia Clínica Perinatal</h2>
    <!-- -->
	<div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Tipo de parto</h4>
        </div>

        <div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Tipo de parto</label>
					</div>
					<div class="col-md-6">
						<select class="form-control" name="tipo_parto" id="tipo_parto">
							<option value="">Seleccione</option>
							<option value="Espont.">Espontáneo</option>
							<option value="Cesárea">Cesárea</option>
							<option value="Otros">Otros</option>
							<option value="Forceps">Forceps</option>
							<option value="Podálica">Podálica</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Término de embarazo</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="termino_embarazo" id="termino_embarazo" class="form-control">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Indicaciones principales parto operatorio inducción</label>
					</div>
					<div class="col-md-6">
						<textarea name="indicaciones_principales" class="form-control" id="indicaciones_principales">{{$formulario_data->indicaciones_principales}}</textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Nº semanas de gestación</label>
					</div>
					<div class="col-md-6">
						<input type="number" name="mini_indicaciones_principales" class="form-control" id="mini_indicaciones_principales">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Nivel de atención</label>
					</div>
					<div class="col-md-6">
						<select name="nivel_atencion" id="nivel_atencion" class="form-control">
							<option value="">Seleccione</option>
							<option value="nivel_atencion_1">1</option>
							<option value="nivel_atencion_2">2</option>
							<option value="nivel_atencion_3">3</option>
							<option value="nivel_atencion_domic">Domicilio</option>
							<option value="nivel_atencion_otro">Otro</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-1">

							</div>
							<div class="col-md-1">
								Médico
							</div>
							<div class="col-md-1">
								Matrona
							</div>
							<div class="col-md-1">
								Aux.
							</div>
							<div class="col-md-1">
								Alumno
							</div>
							<div class="col-md-1">
								Otro
							</div>
						</div>
						<div class="row">
							<div class="col-md-1">
								Atención Parto
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_parto[]" value="atendido_parto_medico">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_parto[]" value="atendido_parto_matrona">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_parto[]" value="atendido_parto_aux">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_parto[]" value="atendido_parto_alumno">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_parto[]" value="atendido_parto_otro">
							</div>
							<div class="col-md-3">
								<label>Responsable At. Maternal</label>
							</div>
							<div class="col-md-3">
								<input type="text" name="responsable_at_maternal" class="form-control" id="responsable_at_maternal">
							</div>
						</div>
						<div class="row">
							<div class="col-md-1">
								Atención Neonato
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_neonato[]" value="atendido_neonato_medico">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_neonato[]" value="atendido_neonato_matrona">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_neonato[]" value="atendido_neonato_aux">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_neonato[]" value="atendido_neonato_alumno">
							</div>
							<div class="col-md-1">
								<input type="checkbox" name="atendido_neonato[]" value="atendido_neonato_otro">
							</div>
							<div class="col-md-3">
								<label>Responsable At. Neonatal</label>
							</div>
							<div class="col-md-3">
								<input type="text" name="responsable_at_neonatal" class="form-control" id="responsable_at_neonatal">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Muerte intrauterina</label>
					</div>
					<div class="col-md-6">
						<select name="muerte_intrauterina" id="muerte_intrauterina" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
				<div class="col-md-6" id="div_muerte_intrauterina_detalle" hidden>
					<div class="col-md-6">
						<label>Especifique</label>
					</div>
					<div class="col-md-6">
						<select name="muerte_intrauterina_detalle" id="muerte_intrauterina_detalle" class="form-control">
							<option value="">Seleccione</option>
							<option value="Emb.">Emb.</option>
							<option value="Parto">Parto</option>
							<option value="Otro">?</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Episiotomia</label>
					</div>
					<div class="col-md-6">
						<select name="episiotomia" id="episiotomia" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Desgarros</label>
					</div>
					<div class="col-md-6">
						<select name="desgarros" id="desgarros" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Alumbramiento natural</label>
					</div>
					<div class="col-md-6">
						<select name="alumbramiento_natural" id="alumbramiento_natural" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Alumbramiento completo</label>
					</div>
					<div class="col-md-6">
						<select name="alumbramiento_completo" id="alumbramiento_completo" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Revisión instrumental</label>
					</div>
					<div class="col-md-6">
						<select name="revision_instrumental" id="revision_instrumental" class="form-control">
							<option value="">Seleccione</option>
							<option value="si">Sí</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-3">
						<label>Peso placenta (gr)</label>
					</div>
					<div class="col-md-3">
						<input type="number" class="form-control" name="peso_placenta" id="peso_placenta">
					</div>
					<div class="col-md-3">
						<label>Longitud cordón (cm)</label>
					</div>
					<div class="col-md-3">
						<input type="number" class="form-control" name="long_cordon" id="long_cordon">
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-6">
						<label>Observaciones</label>
					</div>
					<div class="col-md-6">
						<textarea class="form-control" name="observaciones_placenta_cordon" id="observaciones_placenta_cordon">{{$formulario_data->observaciones_placenta_cordon}}</textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
        <div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<fieldset>
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-2">
									<div class="col-md-2">
										Anestesia
									</div>
								</div>
								<div class="col-md-10">
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_peridural" value="Peridural">
											Peridural
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_raquidea" value="Raquídea">
											Raquídea
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_general" value="General">
											General
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_local" value="Local">
											Local
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_analgesia_tranquilizante" value="Analgesia/Tranquilizante">
											Analgesia/<br>Tranquilizante
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="anestesia_ninguna" value="Ninguna">
											Ninguna
										</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									<div class="col-md-2">
										Medicamentos
									</div>
								</div>
								<div class="col-md-10">
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="medicamento[]" value="medicamento_ocitocina" id="medicamento_ocitocina">
											Ocitocina
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="medicamento[]" value="medicamento_antibioticos" id="medicamento_antibioticos">
											Antibióticos
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="medicamento[]" value="medicamento_otro" id="medicamento_otro">
											Otro
										</label>
									</div>
									<div class="col-md-4" id="div_medicamento_detalle_otro" hidden>
										<label>
											Cuáles
											<input type="text" class="form-control" name="medicamento_detalle_otro" id="medicamento_detalle_otro">
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="checkbox" name="medicamento[]" value="medicamento_ninguno" id="medicamento_ninguno">
											Ninguna
										</label>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<fieldset class="recien_nacido">
						<legend>Recién nacido</legend>
						<div class="row">
							<div class="col-md-3">
								<div class="col-md-4">
									<label>Sexo</label>
								</div>
								<div class="col-md-8">
									<select name="sexo_recien_nacido" id="sexo_recien_nacido" class="form-control">
										<option value="">Seleccione</option>
										<option value="femenino">F</option>
										<option value="masculino">M</option>
									</select>
								</div>
							</div>
							<div class="col-md-5">
								<div class="col-md-4">
									<label>Peso al nacer</label>
								</div>
								<div class="col-md-4">
									<input type="text" class="form-control" name="peso_al_nacer" id="peso_al_nacer">
								</div>
								<div class="col-md-4">
									<label>
										<input type="checkbox" name="peso_menor_2500" id="peso_menor_2500">
										Menor 2500 g.
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="col-md-6">
									<label>Talla (cm)</label>
								</div>
								<div class="col-md-6">
									<input type="number" class="form-control" name="talla" id="talla" step="any">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="col-md-6">
									<label>Perímetro cefálico (cm)</label>
								</div>
								<div class="col-md-6">
									<input type="number" class="form-control" name="per_cef" id="per_cef" step="any">
								</div>
							</div>
							<div class="col-md-4">
								<div class="col-md-4">
									<label>Edad por examen físico (sem)</label>
								</div>
								<div class="col-md-4">
									<input type="number" class="form-control" name="edad_ex_fisico" id="edad_ex_fisico">
								</div>
								<div class="col-md-4">
									<label>
										<input type="checkbox" name="edad_menor_37" id="edad_menor_37">
										Menor 37
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="col-md-6">
									<label>Peso E.G.</label>
								</div>
								<div class="col-md-6">
									<select name="peso_eg" id="peso_eg" class="form-control">
										<option value="">Seleccione</option>
										<option value="Adec.">Adecuado</option>
										<option value="Peq.">Pequeño</option>
										<option value="Gde.">Grande</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-2">
									Apgar
								</div>
								<div class="col-md-2">
									<label>1 min</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" name="apgar_1_min" id="apgar_1_min" min="0" max="10">
								</div>
								<div class="col-md-2">
									<label>3 min</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" name="apgar_3_min" id="apgar_3_min" min="0" max="10">
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-md-2">
									<label>5 min</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" name="apgar_5_min" id="apgar_5_min" min="0" max="10">
								</div>
								<div class="col-md-2">
									<label>10 min</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" name="apgar_10_min" id="apgar_10_min" min="0" max="10">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-6">
									Reanim. resp.
								</div>
								<div class="col-md-6">
									<select name="reanim_resp" id="reanim_resp" class="form-control">
										<option value="">Seleccione</option>
										<option value="No">No</option>
										<option value="Máscara">Máscara</option>
										<option value="Tubo">Tubo</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-md-6">
									VDRL
								</div>
								<div class="col-md-6">
									<select name="vdrl" id="vdrl" class="form-control">
										<option value="">Seleccione</option>
										<option value="-">-</option>
										<option value="+">+</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-6">
									VIH
								</div>
								<div class="col-md-6">
									<select name="vih" id="vih" class="form-control">
										<option value="">Seleccione</option>
										<option value="-">-</option>
										<option value="+">+</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-md-6">
									Examen físico
								</div>
								<div class="col-md-6">
									<select name="examen_fisico" id="examen_fisico" class="form-control">
										<option value="">Seleccione</option>
										<option value="Normal">Normal</option>
										<option value="Anormal">Anormal</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-6">
									Alojamiento conjunto
								</div>
								<div class="col-md-6">
									<select name="alojamiento_conjunto" id="alojamiento_conjunto" class="form-control">
										<option value="">Seleccione</option>
										<option value="si">Sí</option>
										<option value="no">No</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-md-6">
									Hospitalizado
								</div>
								<div class="col-md-6">
									<select name="hospitalizado" id="hospitalizado" class="form-control">
										<option value="">Seleccione</option>
										<option value="si">Sí</option>
										<option value="no">No</option>
									</select>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Protocolo de parto</h4>
        </div>

        <div class="panel-body">

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-12 form-group">
                        <label for="diagnostico_preoperatorio">Diagnóstico preoperatorio</label>
                        <textarea type="text" class="form-control" id="diagnostico_preoperatorio" name="diagnostico_preoperatorio">{{$formulario_data->diagnostico_preoperatorio}}</textarea>
                    </div>                
                
                </div>
                
            </div>


            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="cirujano">Cirujano</label>
                        <input type="text" class="form-control" id="cirujano" name="cirujano">                                           
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="anestesista">Anestesista</label>
                        <input type="text" class="form-control" id="anestesista" name="anestesista">                                           
                    </div>                
                
                </div>
				
				<div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="ayudante">Ayudante</label>
                        <input type="text" class="form-control" id="ayudante" name="ayudante">                                           
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-4">
                
                    <div class="col-md-12 form-group">
                        <label for="matrona">Matrona</label>
                        <input type="text" class="form-control" id="matrona" name="matrona" >
                    </div>
                
                </div>
                <div class="col-md-4">
                
                    <div class="col-md-12 form-group">
                        <label for="arsenalera">Arsenalera</label>
                        <input type="text" class="form-control" id="arsenalera" name="arsenalera">
                    </div>
                
                </div>
            </div>
            <div class="row">

                <div class="col-md-8">
                    <div class="col-md-12 form-group">
                        <label for="operacion">Operación</label>
                        <input type="text" class="form-control" id="operacion" name="operacion">                                                      
                    </div>  
                </div>


            </div>           

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-12 form-group">
                        <label for="diagnostico_postoperatorio">Diagnóstico postoperatorio</label>
                        <textarea type="text" class="form-control" id="diagnostico_postoperatorio" name="diagnostico_postoperatorio">{{$formulario_data->diagnostico_postoperatorio}}</textarea>
                    </div>                
                </div>
                <div class="col-md-12">
                    <div class="col-md-12 form-group">
                        <label for="descripcion_operatoria">Descripción operatoria</label>
                        <textarea type="text" class="form-control" id="descripcion_operatoria" name="descripcion_operatoria">{{$formulario_data->descripcion_operatoria}}</textarea>
                    </div>                
                </div>
            </div>
		</div>
	</div>

    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="protocolo_de_parto_save" type="button" name="" class="btn btn-primary" value="Guardar">
  {{--  <input id="protocolo_de_parto_save_pdf" type="button" class="btn btn-primary" value="Guardar e imprimir">--}}
    @endif

    @if (isset($formulario_data->form_id))
    <input id="protocolo_de_parto_save" type="button" name="" class="btn btn-primary" value="Modificar">
 {{--   <input id="protocolo_de_parto_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">
    <a id="protocolo_de_parto_pdf" href="{{url('formularios-ginecologia/protocolo-de-parto/pdf/'.$formulario_data->caso_id)}}" target="_blank" class="btn btn-primary">Imprimir</a> --}}
    @endif

    {!! HTML::script('js/formularios_ginecologia/helper.js') !!}
    <script>

        const bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
               

            }
        };

        function load() {
            @if (isset($formulario_data->form_id))
            $("#cirujano").val("{{$formulario_data->cirujano}}");
            $("#anestesista").val("{{$formulario_data->anestesista}}");
			$("#ayudante").val("{{$formulario_data->ayudante}}");
			$("#matrona").val("{{$formulario_data->matrona}}");
            $("#arsenalera").val("{{$formulario_data->arsenalera}}");


            $("#operacion").val("{{$formulario_data->operacion}}");
			
			$("#tipo_parto").val("{{$formulario_data->tipo_parto}}");
            $("#termino_embarazo").val("{{$formulario_data->termino_embarazo}}");
			$("#mini_indicaciones_principales").val("{{$formulario_data->indicaciones_principales_mini}}");
			
			$("#nivel_atencion").val("{{$formulario_data->nivel_atencion}}");
			
			
			@if($formulario_data->atendido_parto_medico)
				$("input[name='atendido_parto[]'][value='atendido_parto_medico']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_parto_matrona)
				$("input[name='atendido_parto[]'][value='atendido_parto_matrona']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_parto_aux)
				$("input[name='atendido_parto[]'][value='atendido_parto_aux']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_parto_alumno)
				$("input[name='atendido_parto[]'][value='atendido_parto_alumno']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_parto_otro)
				$("input[name='atendido_parto[]'][value='atendido_parto_otro']").prop("checked",true);
			@endif

			$("#responsable_at_maternal").val("{{$formulario_data->responsable_at_maternal}}");
			
            @if($formulario_data->atendido_neonato_medico)
				$("input[name='atendido_neonato[]'][value='atendido_neonato_medico']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_neonato_matrona)
				$("input[name='atendido_neonato[]'][value='atendido_neonato_matrona']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_neonato_aux)
				$("input[name='atendido_neonato[]'][value='atendido_neonato_aux']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_neonato_alumno)
				$("input[name='atendido_neonato[]'][value='atendido_neonato_alumno']").prop("checked",true);
			@endif
			
			@if($formulario_data->atendido_neonato_otro)
				$("input[name='atendido_neonato[]'][value='atendido_neonato_otro']").prop("checked",true);
			@endif
			
            $("#responsable_at_neonatal").val("{{$formulario_data->responsable_at_neonatal}}");
			
			$("#episiotomia").val("{{$formulario_data->episiotomia === true ? 'si' : ($formulario_data->episiotomia === false ? 'no' : '') }}");
			
			$("#muerte_intrauterina_detalle").val("{{$formulario_data->muerte_intrauterina_detalle}}");
			
			$("#muerte_intrauterina").val("{{$formulario_data->muerte_intrauterina === true ? 'si' : ($formulario_data->muerte_intrauterina === false ? 'no' : '') }}");
			$("#muerte_intrauterina").trigger("change");
			
			$("#desgarros").val("{{$formulario_data->desgarros === true ? 'si' : ($formulario_data->desgarros === false ? 'no' : '') }}");
			
			$("#alumbramiento_natural").val("{{$formulario_data->alumbramiento_natural === true ? 'si' : ($formulario_data->alumbramiento_natural === false ? 'no' : '') }}");
			$("#alumbramiento_completo").val("{{$formulario_data->alumbramiento_completo === true ? 'si' : ($formulario_data->alumbramiento_completo === false ? 'no' : '') }}");
			
			$("#revision_instrumental").val("{{$formulario_data->revision_instrumental === true ? 'si' : ($formulario_data->revision_instrumental === false ? 'no' : '') }}");
			
			
			$("input[name=anestesia_peridural]").prop("checked","{{$formulario_data->anestesia_peridural}}" ? true : false);
			$("input[name=anestesia_raquidea]").prop("checked","{{$formulario_data->anestesia_raquidea}}" ? true : false);
			$("input[name=anestesia_general]").prop("checked","{{$formulario_data->anestesia_general}}" ? true : false);
			$("input[name=anestesia_local]").prop("checked","{{$formulario_data->anestesia_local}}" ? true : false);
			$("input[name=anestesia_analgesia_tranquilizante]").prop("checked","{{$formulario_data->anestesia_analgesia_tranquilizante}}" ? true : false);
			$("input[name=anestesia_ninguna]").prop("checked","{{$formulario_data->anestesia_ninguna}}" ? true : false);
            
			@if($formulario_data->medicamento_ocitocina)
				$("input[name='medicamento[]'][value='medicamento_ocitocina']").prop("checked",true);
			@endif
			
			@if($formulario_data->medicamento_antibioticos)
				$("input[name='medicamento[]'][value='medicamento_antibioticos']").prop("checked",true);
			@endif
			
			@if($formulario_data->medicamento_otro)
				$("input[name='medicamento[]'][value='medicamento_otro']").prop("checked",true);
				$("input[name='medicamento[]'][value='medicamento_otro']").trigger("change");
			@endif
			
			@if($formulario_data->medicamento_ninguno)
				$("input[name='medicamento[]'][value='medicamento_ninguno']").prop("checked",true);
			@endif
			
            $("#medicamento_detalle_otro").val("{{$formulario_data->medicamento_detalle_otro}}");
			
			$("#sexo_recien_nacido").val("{{$formulario_data->sexo_recien_nacido}}");
			$("#peso_al_nacer").val("{{$formulario_data->peso_al_nacer}}");
			@if($formulario_data->peso_menor_2500)
				$("#peso_menor_2500").prop("checked",true);
			@endif
			
			$("#talla").val("{{$formulario_data->talla}}");
			$("#per_cef").val("{{$formulario_data->per_cef}}");
			$("#edad_ex_fisico").val("{{$formulario_data->edad_ex_fisico}}");
			@if($formulario_data->edad_menor_37)
				$("#edad_menor_37").prop("checked",true);
			@endif
			
			$("#peso_eg").val("{{$formulario_data->peso_eg}}");
			$("#apgar_1_min").val("{{$formulario_data->apgar_1_min}}");
			$("#apgar_3_min").val("{{$formulario_data->apgar_3_min}}");
			$("#apgar_5_min").val("{{$formulario_data->apgar_5_min}}");
			$("#apgar_10_min").val("{{$formulario_data->apgar_10_min}}");
			
			$("#reanim_resp").val("{{$formulario_data->reanim_resp}}");
			$("#vdrl").val("{{$formulario_data->vdrl}}");
			$("#vih").val("{{$formulario_data->vih}}");
			$("#examen_fisico").val("{{$formulario_data->examen_fisico}}");
			$("#alojamiento_conjunto").val("{{$formulario_data->alojamiento_conjunto === true ? 'si' : ($formulario_data->alojamiento_conjunto === false ? 'no' : '') }}");
			$("#hospitalizado").val("{{$formulario_data->hospitalizado === true ? 'si' : ($formulario_data->hospitalizado === false ? 'no' : '') }}");
			
			$("#peso_placenta").val("{{$formulario_data->peso_placenta}}");
			$("#long_cordon").val("{{$formulario_data->long_cordon}}");

			
            initFormValidation ($("#protocolo_de_parto_form"));
            @endif

        }

        $(document).ready(function() {
			$('#termino_embarazo').datetimepicker({
				format: 'DD-MM-YYYY HH:mm'
			});

            $(document).on('click', '#protocolo_de_parto_save,#protocolo_de_parto_save_pdf', function() {

                var con_pdf = $(this).attr("id") == "protocolo_de_parto_save_pdf";
                var bv = initFormValidation ($("#protocolo_de_parto_form"));

                if(bv.isValid()){
                    bootbox.confirm({
                        message: "<h4>¿Está seguro de ingresar la información?</h4>",
                        buttons: {
                            confirm: {
                                label: 'Si',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'No',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if(result){
                                $("#protocolo_de_parto_save,#protocolo_de_parto_save_pdf").attr("disabled", true);

                                var form_data = $("#protocolo_de_parto_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                $.ajax({
                                    url: "{{URL::route('protocolo-de-parto-save')}}",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:  form_data,
                                    dataType: "json",
                                    type: "post",
                                    success: function(data){
                                    	swalExito.fire({
                							title: 'Exito!',
                							text: data.status,
                							didOpen: function() {
                								setTimeout(function() {
                									if(con_pdf)
                                                    {                                               
                                                    	window.open("{{url('formularios-ginecologia/protocolo-de-parto/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");   
                                                    } 
                                                    @if (!isset($formulario_data->form_id))
                                                    location.reload();
                                                    @else
                                                    $("#protocolo_de_parto_save,#protocolo_de_parto_save_pdf").attr("disabled", false);
                                                    @endif
                								}, 2000);
                							},
										});
                                    },
                                    error: function(request, status, error){
                                        

                                    	try {
                                            var json_res = JSON.parse(request.responseText);
                                            swalError.fire({
                        						title: 'Error',
                        						text:json_res.status
                        					});
                                        } 
                                        
                                        catch (error) {
                                        	swalError.fire({
                        						title: 'Error',
                        						text:"Ha ocurrido un error"
                        					});
                                        }
                                        $("#protocolo_de_parto_save,#protocolo_de_parto_save_pdf").attr("disabled", false);

                                    }
                                });
                            }
                        }            
                    });               

                }                

            });
			
			$("#muerte_intrauterina").on("change",function(){
				if($(this).val() === "si"){
					deshabilitarmuerteIntrauterinaDetalle(false);
				}
				else{
					deshabilitarmuerteIntrauterinaDetalle(true);
				}
			});
			$("input[name=anestesia_ninguna]").on("change",function(){
				if($(this).is(":checked")){
					$("input[name^=anestesia_]:not(input[name=anestesia_ninguna])").prop("checked",false);
				}
			});
			$("input[name^=anestesia_]:not(input[name=anestesia_ninguna])").on("change",function(){
				$("input[name=anestesia_ninguna]").prop("checked",false);
			});
			
			$("#medicamento_ninguno").on("change",function(){
				if($(this).is(":checked")){
					$("input[name='medicamento[]']:not(#medicamento_ninguno)").prop("checked",false);
					mostrarDetalleOtroMedicamento(false);
				}
			});
			$("input[name='medicamento[]']:not(#medicamento_ninguno)").on("change",function(){
				$("#medicamento_ninguno").prop("checked",false);
			});
			$("#medicamento_otro").on("change",function(){
				if($(this).is(":checked")){
					mostrarDetalleOtroMedicamento(true);
				}
				else{
					mostrarDetalleOtroMedicamento(false);
				}
			});
			
			function deshabilitarmuerteIntrauterinaDetalle(deshabilitar){
				$("#div_muerte_intrauterina_detalle").prop("hidden",deshabilitar);
				if(deshabilitar){
					$("#muerte_intrauterina_detalle").val("");
				}
			}
			function mostrarDetalleOtroMedicamento(mostrar){
				$("#div_medicamento_detalle_otro").prop("hidden",!mostrar);
				if(!mostrar){
					$("#medicamento_detalle_otro").val("");
				}
			}

            //al final siempre
            load();
        });

    </script>

@stop