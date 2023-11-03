//ingresarinfeccion
		$("#formInfeccion").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				fechaIngreso2: {
					validators:{
						notEmpty: {
							message: 'Debe ingresar una fecha'
						}
					}
				},
				aislamiento: {
				 	validators:{
				 		notEmpty: {
				 			message: 'seleccione una opcion'
				 		}
				 	}
				}
			}
		}).on('status.field.bv', function(e, data) {
			//data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			console.log(evt);
			evt.preventDefault();
			$.ajax({
				url: "{{ URL::to('/')}}/ingresarinfeccion",
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				//data: $('#derivarForm').serialize(),
				data: $(this).serialize(),
				dataType: "json",
				type: "post",
				success: function(data){
					console.log("exito");
					console.log(data);
					if (data.exito) {
						bootbox.alert("<h4>"+data.exito+"</h4>", function(){
							window.location.href="{{URL::to('/')}}/unidad/<?php echo $Miunidad;?>";
							//location.reload();
						});
					}
					if (data.error) {
						bootbox.alert("<h4>"+data.error+"</h4>", function(){
							$('#solicitar').removeAttr('disabled');
							//location.reload();
						});
					}
				},
				error: function(error){
					$('#solicitar').removeAttr('disabled');
					//$("#divLoadBuscarPaciente").hide();
					bootbox.alert("<h4>Error al ingresar infeccion</h4>");
					console.log(error);
				}
			});
		});
		//ingresarinfeccion

{{-- @include('Gestion.verInfecciones2Iaas'); --}}


<div class="container" width='50%'>
	<ul class="nav nav-tabs">
		<li class="nav in active"><a href="#datosPaciente" data-toggle="tab">DatosPaciente</a></li>
		<li class="nav"><a href="#iaas" data-toggle="tab">IAAS</a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane fade in active" style="padding-top:10px;" id="datosPaciente">
			<fieldset>
				<legend>Informacion del paciente</legend>
				<div class="row">
					<div class="form-group col-md-6">
						<label for="rut" class="col-sm-2 control-label">Rut: </label>
						<div class="col-sm-10">
							<div class="input-group">
								@if(!$paciente)
								{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
								<span class="input-group-addon"> - </span>
								{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
								@else
								{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true', 'readonly'))}}
								<span class="input-group-addon"> - </span>
								{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;', 'readonly'))}}
								@endif
							</div>
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="fecha" class="col-sm-2 control-label">Fecha de nacimiento: </label>
						<div class="col-sm-10">
							{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control', 'required' => "true"))}}
						</div>
					</div>
				</div>
	
				<div class="row">
					<div class="form-group col-md-6">
						<label for="rut" class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="rut" class="col-sm-2 control-label">Edad: </label>
						<div class="col-sm-10">
							{{Form::text('edad', null, array('id' => 'edad', 'class' => 'form-control'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6">
						<label for="rut" class="col-sm-2 control-label">Apellido paterno: </label>
						<div class="col-sm-10">
							{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="fecha" class="col-sm-2 control-label">Apellido materno: </label>
						<div class="col-sm-10">
							{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
						</div>
					</div>
	
					<div class="form-group col-md-6">
						<label for="fecha" class="col-sm-2 control-label">Género: </label>
						<div class="col-sm-10">
							{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
						</div>
					</div>		
					@foreach ($paciente_infeccion as $paciente_infec)
					<div class="form-group col-md-6">
						<label for="prevision" class="col-sm-2 control-label">Servicio de Ingreso: </label>
						<div class="col-sm-10">
							{{ Form::text('servicio', $paciente_infec->servicio_ingreso, array('class' => 'form-control','disabled')) }}
						</div>
					</div>		
				</div>
				<div class="row">
					<div class="form-group col-md-6">
						<label for="prevision" class="col-sm-2 control-label">Previsión: </label>
						<div class="col-sm-10">
							{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('class' => 'form-control')) }}
						</div>
					</div>
					{{--<div class="form-group col-md-6">
						<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
						<div class="col-sm-10">
							{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
						</div>--}}
					</div>
				</div>
				@if($esIaas || Session::get("usuario")->tipo === TipoUsuario::IAAS)
				<div class="row">
					<div class="form-group col-md-6">
							<label  class="col-sm-2 control-label">Numero de Ficha: </label>
							<div class="col-sm-10">
								{{Form::text('numero_ficha', $paciente_infec->numero_ficha, array('id' => 'numero_ficha', 'class' => 'form-control'))}}
							</div>
					</div>
					<div class="form-group col-md-6">
							<label  class="col-sm-2 control-label">Peso de nacimiento: </label>
							<div class="col-sm-6">
							<input disabled type="text" class="form-control" value = "{{$paciente_infec->peso_nacimiento}}"/>
							</div>
					</div>
				</div>
				@endif
				<div class="row">
					<div class="form-group col-sm-6">
							<label  class="col-sm-2 control-label">Categoria: </label>
							<div class="col-sm-8">
								<input disabled type="text" class="form-control" value = "{{$paciente_infec->categoria}}"/>
							</div>
					</div>
					<div class="form-group col-md-6">
						<label for="fecha" class="col-sm-2 control-label">Fecha de Ingreso: </label>
						<div class="col-sm-10">
							{{Form::text('fechaIngreso2', null, array('id' => 'fechaIngreso2', 'class' => 'form-control', 'required' => "true"))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6">
							<label  class="col-sm-2 control-label">Tipo de aislamiento Ingresado: </label>
							<div class="col-sm-8">
							<input disabled type="text" class="form-control" value = "{{trim($paciente_infec->aislamiento,"{}")}}"/>
							</div>
					</div>
	
					<div class="form-group col-md-6">
							<label  class="col-sm-2 control-label">Actualizar aislamiento: </label>
							<div class="col-sm-6">
								<select  name="aislamiento[]" class="selectpicker" multiple> 
								 <option>Sin aislamiento</option>
								 <option>Contacto</option>
								 <option>Aereo</option> 
								 <option>Por gotitas</option>  
								</select>
							</div>
					</div>
				</div>
	
				<div class="row">
					<div class="form-group col-md-6">
							<label  class="col-sm-2 control-label">Reingreso: </label>
							<div class="col-sm-3">
						@if(trim($paciente_infec->reingreso)=="Si"){{ Form::radio('reingreso','Si',true, array('disabled')) }} SI
						@else {{ Form::radio('reingreso','Si',false, array('disabled')) }} SI
						@endif
						@if(trim($paciente_infec->reingreso)=="No"){{ Form::radio('reingreso','No',true, array('disabled')) }} NO
						@else {{ Form::radio('reingreso','No',false, array('disabled')) }} NO
						@endif
							</div>
					</div>
					<div id="divIaas" class="form-group col-md-6">
							<label  class="col-sm-3 control-label">Numero de días de reingreso: </label>
							<div class="col-sm-9">
							<input disabled type="text" class="form-control" value = "{{$paciente_infec->dias_reingreso}}"/>
							</div>
					</div>
				</div>
			</fieldset>
	
			<fieldset>
			<legend>ANTECEDENTES MORBIDOS</legend><br>
						<div class="form-group col-xs-6 col-sm-6 col-md-9">
							<label class="col-sm-3 control-label">ANTECEDENTES</label>
							<div class="col-sm-9">
								@if($paciente_infec->diabetes)<label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" checked disabled="true" /> <span title="diabetes">Diabetes</span></label>
								@else <label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" disabled="true"/> <span title="diabetes" >Diabetes</span></label>
								@endif
								@if($paciente_infec->hipertension)<label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension" checked disabled="true"/> <span title="hipertension">Hipertensión</span></label>
								@else <label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension"disabled="true"/> <span title="hipertension" >Hipertensión</span></label>
								@endif
								@if($paciente_infec->enfermedad_autoinmune)<label> <input name="morbidos[]" type="checkbox" value="enfermedad" checked disabled="true"/> Enfermedad autoinmune</label>
								@else <label> <input name="morbidos[]" type="checkbox" value="enfermedad" disabled="true"/> Enfermedad autoinmune</label>
								@endif
								@if($paciente_infec->otro!='0')
									<label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" checked disabled="true"/> Otras</label>
									{{Form::text('Otro',$paciente_infec->otro, array('id' => 'Otro','placeholder'=>'Ingrese Otro','disabled'=>'true', 'class' => 'form-control'))}}
								@else <label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" disabled="true"/> Otras</label>
								@endif
								
							</div>
						</div>
			</fieldset>
	
			<fieldset>
				<br>
				<legend>Auditoria de muerte</legend>
				<div class="row">
						<div class="form-group col-md-12">
							<label class="col-sm-2 control-label">Auditoria de muerte: </label>
							<div class="col-sm-10">
								@if(trim($paciente_infec->fallecimiento)=="Si"){{ Form::radio('fallecimiento','Si',true) }} SI
								@else {{ Form::radio('fallecimiento','Si') }} SI
								@endif
			
								@if(trim($paciente_infec->fallecimiento)=="No"){{ Form::radio('fallecimiento','No',true) }} NO
								@else {{ Form::radio('fallecimiento','No') }} No
								@endif
							</div>
						</div>
				</div>
				<div id="muerte" class="row">
							<div class="form-group col-md-6">
								<label  class="col-sm-1 control-label" style="width:170px">Fecha de fallecimiento: </label>
								<div class="col-sm-9" style="width:235px">
									{{Form::text('fechaMuerte',$paciente_infec->fecha_fallecimiento, array('id' => 'fechaMuerte', 'class' => 'form-control'))}}
								</div>
							</div>
							<div class="form-group col-md-6">
								<label class="col-sm-2 control-label"></label>
								<div class="col-sm-10">
									@if(trim($paciente_infec->motivo_fallecimiento)=="La muerte fue causada por la IAAS"){{ Form::radio('muerte','La muerte fue causada por la IAAS',true) }} La muerte fue causada por la IAAS<br>
									@else {{ Form::radio('muerte','La muerte fue causada por la IAAS') }} La muerte fue causada por la IAAS<br>
									@endif
									@if(trim($paciente_infec->motivo_fallecimiento)=="La IAAS contribuyo a la muerte sin ser la causa de ella"){{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella',true) }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
									@else {{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella') }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
									@endif
									@if(trim($paciente_infec->motivo_fallecimiento)=="No hubo relacion entra la IAAS y la muerte"){{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte',true) }} No hubo relación entra la IAAS y la muerte<br>
									@else {{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte') }} No hubo relación entra la IAAS y la muerte<br>
									@endif
									@if(trim($paciente_infec->motivo_fallecimiento)=="Se desconoce la asociación entre la IAAS y la muerte"){{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte',true) }} Se desconoce la asociación entre la IAAS y la muerte
									@else {{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte') }} Se desconoce la asociación entre la IAAS y la muerte
									@endif
								</div>
							</div>
							<br>
				@endforeach<!-- Fin datos pacientes -->			
				</div>
			</fieldset>
			
			</fieldset>
		</div>
		
		<div class="tab-pane fade" style="padding-top:10px;" id="iaas">
			<fieldset>
				<legend>informacion iaas</legend>
				<fieldset>
					<legend>IAAS</legend>
					<?php $contador2=1;?>
			@foreach ($iaas2 as $iaas)
			<br><br>
			<div class="row">
					<legend>Notificación de Infección <?php echo $contador2;?></legend>
					</div>
					<br>
					<div class="row">
						<div class="form-group col-md-6">
							<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
							<div class="col-sm-9">
								<input disabled type="text" class="form-control" value = "{{$iaas->fecha_inicio}}"/>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label  class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
							<div class="col-sm-9">
								<input disabled type="text" class="form-control" value = "{{$iaas->fecha_iaas}}"/>
							</div>
						</div>
						<div class="form-group">
						</div>
						<div class="form-group col-md-6">
							<label  class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
							<div class="col-sm-9">
								<input disabled type="text" class="form-control" value = "{{$iaas->servicioiaas}}"/>
							</div>
						</div>
						<div class="form-group">
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
							<div class="col-sm-10" style="width:720px" >
								<input disabled type="text" class="form-control" value = "{{$iaas->localizacion}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
							<div class="col-sm-10" style="width:720px" >
							<input disabled type="text" class="form-control" value = "{{$iaas->procedimiento_invasivo}}"/>
							</div>
						</div>
			
						<div class="row">
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
							<div class="col-sm-10" style="width:720px" >
							<input disabled type="text" class="form-control" value = "{{$iaas->agente1}}"/>
							</div>
						</div>
					</div>
			<div class="row">
					<tfoot>
					@if(trim($iaas->sensibilidad1)!="NINGUNA")
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad1}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia1: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia1}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia1: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia1}}"/>
								</div>
							</td>
						</tr>
					@endif
						@if(trim($iaas->sensibilidad2)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad2}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia2: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia2}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia2: </label>
									<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia2}}"/>
								</div>
							</td>
						</tr>
						@endif
						@if(trim($iaas->sensibilidad3)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad3}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia3}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia3}}"/>
								</div>
							</td>
						</tr>
						@endif
						@if(trim($iaas->sensibilidad4)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad4}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia4}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia4}}"/>
								</div>
							</td>
						</tr>
						@endif
						@if(trim($iaas->sensibilidad5)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad5}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia5}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia5}}"/>
								</div>
							</td>
						</tr>
						@endif
						@if(trim($iaas->sensibilidad6)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad6}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia6}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia6}}"/>
								</div>
							</td>
						</tr>	
						@endif
					</tfoot>
			</div>
			@if(trim($iaas->agente2)!="Sin información")
					<!-- Fin agente 1-->
					<br><br><br>
			<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
							<div class="col-sm-10" style="width:720px" >
			<input disabled type="text" class="form-control" value = "{{$iaas->agente2}}"/>
							</div>
						</div>
			<div class="row">
					<tfoot>
					@if(trim($iaas->sensibilidad7)!="NINGUNA")
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad7}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia1: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia7}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia1: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia7}}"/>
								</div>
							</td>
						</tr>
					@endif
					@if(trim($iaas->sensibilidad8)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad8}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia2: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia8}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia2: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia8}}"/>
								</div>
							</td>
						</tr>
					@endif
					@if(trim($iaas->sensibilidad9)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad9}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia9}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia3: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia9}}"/>
								</div>
							</td>
						</tr>
					@endif
					@if(trim($iaas->sensibilidad10)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad10}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia10}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia4: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia10}}"/>
								</div>
							</td>
						</tr>
					@endif
					@if(trim($iaas->sensibilidad11)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad11}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia11}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia5: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia11}}"/>
								</div>
							</td>
						</tr>
					@endif
					@if(trim($iaas->sensibilidad12)!="NINGUNA")
						<br><br><br>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad12}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->intermedia12}}"/>
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia6: </label>
								<div class="col-sm-3" style="width:220px">
							<input disabled type="text" class="form-control" value = "{{$iaas->resistencia12}}"/>
								</div>
							</td>
						</tr>	
					@endif
					</tfoot>
			</div>
					<!-- Fin agente 2-->
			</div> 
			@endif	
					<br>
						@if(trim($iaas->cierre)=="no")
						<label class="col-sm-9 control-label"> Cerrar la Notificación de Infección <?php echo $contador2;?>
						<select  name="cerrar[]" class="horario"> 
						 <option value="si">Si</option>
						 <option value="no"selected>No</option>     
						</select>
						</label>
						 @endif	
						 @if(trim($iaas->cierre)=="si")
						 <label class="col-sm-9 control-label"> IAAS FINALIZADA <?php echo $contador2;?>
						<select  name="cerrar[]" class="horario"> 
						 <option value="si" selected>Si</option>
						 <option value="no">No</option>     
						</select>
						</label>
						 @endif
					<br>
				<?php $contador2=$contador2+1;?>
				@endforeach<!-- Fin Localizacion-->	
			</fieldset>
					<br><br>
						<tfoot>
									<tr>
										<td colspan="4" class="text-left">
											<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus"></span> Agregar Localización</a>
										</td>
									</tr>
						</tfoot>
					<br><br>
			
					<!-- Copia localizacion--> 
					<div id="templateRow2" class="row hide"><br>
						<div class="form-group col-md-6">
							<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
							<div class="col-sm-9">
								{{Form::text('fechaIngreso[]', null, array('id' => 'fechaIngreso', 'class' => 'form-control'))}}
							</div>
						</div>
						<div class="form-group col-md-6">
							<label class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
							<div class="col-sm-9">
								{{Form::text('fechaInicio[]', null, array('id' => 'fechaInicio', 'class' => 'form-control'))}}
							</div>
						</div>
						<div class="form-group">
						</div>
						<div class="form-group col-md-6">
							<label class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
							<div class="col-sm-9">
								{{ Form::select('servicioIAAS[]', $UnidadesIAAS, 'SIN INFORMACION', array('class' => 'form-control')) }}
							</div>
						</div>
						<div class="form-group">
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
							<div class="col-sm-10" style="width:720px" >
								{{ Form::select('localizacion[]', $localizacion, 'SIN INFORMACION', array('class' => 'form-control')) }}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
							<div class="col-sm-10" style="width:720px" >
								{{ Form::select('procedimiento[]', $procedimiento, 'SIN INFORMACION', array('class' => 'form-control')) }}
							</div>
						</div>
						<div class="row">
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
							<div class="col-sm-10" style="width:720px" >
								{{ Form::select('agente1[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente1', 'class' => 'form-control')) }}
							</div>
						</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('sensibilidad1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia1: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia1: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('sensibilidad2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia2: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('intermedia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia2: </label>
									<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('sensibilidad3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia3: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia3: </label>
								<div class="col-sm-3" style="width:235px">
								 {{ Form::select('resistencia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
								<div class="col-sm-3" style="width:220px">
							 {{ Form::select('sensibilidad4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia4: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia4: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
								<div class="col-sm-3" style="width:220px">
							 {{ Form::select('sensibilidad5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia5: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia5: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia6: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia6: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>	
					</tfoot>
			</div>
					<!-- Fin agente 1-->
					<br><br><br>
			<div class="form-group">
							<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
							<div class="col-sm-10" style="width:720px" >
									{{ Form::select('agente2[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente2', 'class' => 'form-control')) }}
							</div>
						</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia1: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia1: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia2: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia2: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia3: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia3: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia4: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia4: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia5: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia5: </label>
								<div class="col-sm-3" style="width:235px">
								{{ Form::select('resistencia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
						</tfoot>
			</div>
			<div class="row">
					<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
								<div class="col-sm-3" style="width:220px">
								 {{ Form::select('sensibilidad12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Intermedia6: </label>
								<div class="col-sm-3" style="width:220px">
								{{ Form::select('intermedia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
							<td colspan="4" class="text-left">
								<label class="col-sm-1 control-label">Resistencia6: </label>
								<div class="col-sm-3" style="width:235px">
							   {{ Form::select('resistencia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>	
					</tfoot>
			</div>
					<!-- Fin agente 2-->
						
					<br><br><br>
						<div class="input-group col-md-10">
							<div class="input-group-btn">
								<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Agregar Localización</a>
								<a class="btn btn-default" onclick="deleteOtro(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Quitar Localización</a>
							</div>
						</div>
					</div> <!-- Fin Localizacion copia-->	
					<br><br>
					</fieldset>
			
			
			<div class="modal-footer">
				<div class="form-group col-md-6">
					<div class="col-sm-12">
						<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
						<button id="solicitar" type="submit" class="btn btn-primary">Actualizar</button>
					</div>
				</div>
				<div class="form-group col-md-6">
					<div class="col-sm-3">
						<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
						<button id="cerrar" type="button" onclick="liberar();" class="btn btn-primary">Cerrar Todas las Infecciones Notificadas</button>
					</div>
				</div>
			</div>
			{{ Form::close() }}
			</div> <!-- Fin pestaña localizacion -->
			
			
			</div>
			</div> <!-- Fin panel-->
			</fieldset>
		</div>
		
	</div>
</div>

---------------------------------------------------
<div class="tab-pane fade active in" id="tab-datosPaciente" >
	<fieldset>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="rut" class="col-sm-2 control-label">Rut: </label>
				<div class="col-sm-10">
					<div class="input-group">
						@if(!$paciente)
						{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
						<span class="input-group-addon"> - </span>
						{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
						@else
						{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true', 'readonly'))}}
						<span class="input-group-addon"> - </span>
						{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;', 'readonly'))}}
						@endif
					</div>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="fecha" class="col-sm-2 control-label">Fecha de nacimiento: </label>
				<div class="col-sm-10">
					{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control', 'required' => "true"))}}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<label for="rut" class="col-sm-2 control-label">Nombre: </label>
				<div class="col-sm-10">
					{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="rut" class="col-sm-2 control-label">Edad: </label>
				<div class="col-sm-10">
					{{Form::text('edad', null, array('id' => 'edad', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="rut" class="col-sm-2 control-label">Apellido paterno: </label>
				<div class="col-sm-10">
					{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="fecha" class="col-sm-2 control-label">Apellido materno: </label>
				<div class="col-sm-10">
					{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-6">
				<label for="fecha" class="col-sm-2 control-label">Género: </label>
				<div class="col-sm-10">
					{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
				</div>
			</div>		
	@foreach ($paciente_infeccion as $paciente_infec)
			<div class="form-group col-md-6">
				<label for="prevision" class="col-sm-2 control-label">Servicio de Ingreso: </label>
				<div class="col-sm-10">
					{{ Form::text('servicio', $paciente_infec->servicio_ingreso, array('class' => 'form-control','disabled')) }}
				</div>
			</div>		
		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="prevision" class="col-sm-2 control-label">Previsión: </label>
				<div class="col-sm-10">
					{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			{{--<div class="form-group col-md-6">
				<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
				<div class="col-sm-10">
					{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
				</div>
			</div>--}}
		</div>
		@if($esIaas || Session::get("usuario")->tipo === TipoUsuario::IAAS)
		<div class="row">
			<div class="form-group col-md-6">
					<label  class="col-sm-2 control-label">Numero de Ficha: </label>
					<div class="col-sm-10">
						{{Form::text('numero_ficha', $paciente_infec->numero_ficha, array('id' => 'numero_ficha', 'class' => 'form-control'))}}
					</div>
			</div>
			<div class="form-group col-md-6">
					<label  class="col-sm-2 control-label">Peso de nacimiento: </label>
					<div class="col-sm-6">
					<input disabled type="text" class="form-control" value = "{{$paciente_infec->peso_nacimiento}}"/>
					</div>
			</div>
		</div>
		@endif
		<div class="row">
			<div class="form-group col-sm-6">
					<label  class="col-sm-2 control-label">Categoria: </label>
					<div class="col-sm-8">
						<input disabled type="text" class="form-control" value = "{{$paciente_infec->categoria}}"/>
					</div>
			</div>
			{{-- <div class="form-group col-md-6">
				<label for="fecha" class="col-sm-2 control-label">Fecha de Ingreso: </label>
				<div class="col-sm-10">
					{{Form::text('fechaIngreso2', null, array('id' => 'fechaIngreso2', 'class' => 'form-control', 'required' => "true"))}} 
				</div>
			</div> --}}
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
					<label  class="col-sm-2 control-label">Tipo de aislamiento Ingresado: </label>
					<div class="col-sm-8">
					<input disabled type="text" class="form-control" value = "{{trim($paciente_infec->aislamiento,"{}")}}"/>
					</div>
			</div>

			<div class="form-group col-md-6">
					<label  class="col-sm-2 control-label">Actualizar aislamiento: </label>
					<div class="col-sm-6">
						<select  name="aislamiento[]" class="selectpicker" multiple> 
						<option>Sin aislamiento</option>
						<option>Contacto</option>
						<option>Aereo</option> 
						<option>Por gotitas</option>  
						</select>
					</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
					<label  class="col-sm-2 control-label">Reingreso: </label>
					<div class="col-sm-3">
				@if(trim($paciente_infec->reingreso)=="Si"){{ Form::radio('reingreso','Si',true, array('disabled')) }} SI
				@else {{ Form::radio('reingreso','Si',false, array('disabled')) }} SI
				@endif
				@if(trim($paciente_infec->reingreso)=="No"){{ Form::radio('reingreso','No',true, array('disabled')) }} NO
				@else {{ Form::radio('reingreso','No',false, array('disabled')) }} NO
				@endif
					</div>
			</div>
			<div id="divIaas" class="form-group col-md-6">
					<label  class="col-sm-3 control-label">Numero de días de reingreso: </label>
					<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$paciente_infec->dias_reingreso}}"/>
					</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
	<legend>ANTECEDENTES MORBIDOS</legend><br>
				<div class="form-group col-xs-6 col-sm-6 col-md-9">
					<label class="col-sm-3 control-label">ANTECEDENTES</label>
					<div class="col-sm-9">
						@if($paciente_infec->diabetes)<label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" checked disabled="true" /> <span title="diabetes">Diabetes</span></label>
						@else <label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" disabled="true"/> <span title="diabetes" >Diabetes</span></label>
						@endif
						@if($paciente_infec->hipertension)<label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension" checked disabled="true"/> <span title="hipertension">Hipertensión</span></label>
						@else <label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension"disabled="true"/> <span title="hipertension" >Hipertensión</span></label>
						@endif
						@if($paciente_infec->enfermedad_autoinmune)<label> <input name="morbidos[]" type="checkbox" value="enfermedad" checked disabled="true"/> Enfermedad autoinmune</label>
						@else <label> <input name="morbidos[]" type="checkbox" value="enfermedad" disabled="true"/> Enfermedad autoinmune</label>
						@endif
						@if($paciente_infec->otro!='0')
							<label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" checked disabled="true"/> Otras</label>
							{{Form::text('Otro',$paciente_infec->otro, array('id' => 'Otro','placeholder'=>'Ingrese Otro','disabled'=>'true', 'class' => 'form-control'))}}
						@else <label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" disabled="true"/> Otras</label>
						@endif
						
					</div>
				</div>
	</fieldset>

	<fieldset>
				<br>
				<legend>Auditoria de muerte</legend>
				<div class="row">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Auditoria de muerte: </label>
						<div class="col-sm-10">
							@if(trim($paciente_infec->fallecimiento)=="Si"){{ Form::radio('fallecimiento','Si',true) }} SI
							@else {{ Form::radio('fallecimiento','Si') }} SI
							@endif

							@if(trim($paciente_infec->fallecimiento)=="No"){{ Form::radio('fallecimiento','No',true) }} NO
							@else {{ Form::radio('fallecimiento','No') }} No
							@endif
						</div>
					</div>
				</div>
				<div id="muerte" class="row">
						<div class="form-group col-md-6">
							<label  class="col-sm-1 control-label" style="width:170px">Fecha de fallecimiento: </label>
							<div class="col-sm-9" style="width:235px">
								{{Form::text('fechaMuerte',$paciente_infec->fecha_fallecimiento, array('id' => 'fechaMuerte', 'class' => 'form-control'))}}
							</div>
						</div>
						<div class="form-group col-md-6">
						<label class="col-sm-2 control-label"></label>
							<div class="col-sm-10">
								@if(trim($paciente_infec->motivo_fallecimiento)=="La muerte fue causada por la IAAS"){{ Form::radio('muerte','La muerte fue causada por la IAAS',true) }} La muerte fue causada por la IAAS<br>
								@else {{ Form::radio('muerte','La muerte fue causada por la IAAS') }} La muerte fue causada por la IAAS<br>
								@endif
								@if(trim($paciente_infec->motivo_fallecimiento)=="La IAAS contribuyo a la muerte sin ser la causa de ella"){{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella',true) }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
								@else {{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella') }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
								@endif
								@if(trim($paciente_infec->motivo_fallecimiento)=="No hubo relacion entra la IAAS y la muerte"){{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte',true) }} No hubo relación entra la IAAS y la muerte<br>
								@else {{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte') }} No hubo relación entra la IAAS y la muerte<br>
								@endif
								@if(trim($paciente_infec->motivo_fallecimiento)=="Se desconoce la asociación entre la IAAS y la muerte"){{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte',true) }} Se desconoce la asociación entre la IAAS y la muerte
								@else {{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte') }} Se desconoce la asociación entre la IAAS y la muerte
								@endif
							</div>
						</div>
						<br>
				</div>
	@endforeach
	</fieldset>
</div> 
<!-- Fin datos pacientes -->



---------------------------------------------------
<div class="tab-pane fade " id="tab-localizacion" >
		<fieldset>
		<legend>IAAS</legend>
		<?php $contador2=1;?>
	@foreach ($iaas2 as $iaas)
	<br><br>
	<div class="row">
		<legend>Notificación de Infección <?php echo $contador2;?></legend>
		</div>
		<br>
		<div class="row">
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->fecha_inicio}}"/>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->fecha_iaas}}"/>
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->servicioiaas}}"/>
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
				<div class="col-sm-10" style="width:720px" >
					<input disabled type="text" class="form-control" value = "{{$iaas->localizacion}}"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
				<div class="col-sm-10" style="width:720px" >
				<input disabled type="text" class="form-control" value = "{{$iaas->procedimiento_invasivo}}"/>
				</div>
			</div>

			<div class="row">
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
				<div class="col-sm-10" style="width:720px" >
				<input disabled type="text" class="form-control" value = "{{$iaas->agente1}}"/>
				</div>
			</div>
		</div>
	<div class="row">
		<tfoot>
		@if(trim($iaas->sensibilidad1)!="NINGUNA")
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad1}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia1}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia1}}"/>
					</div>
				</td>
			</tr>
		@endif
			@if(trim($iaas->sensibilidad2)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad2}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia2}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
						<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia2}}"/>
					</div>
				</td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad3)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad3}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia3}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia3}}"/>
					</div>
				</td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad4)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad4}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia4}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia4}}"/>
					</div>
				</td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad5)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad5}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia5}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia5}}"/>
					</div>
				</td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad6)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad6}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia6}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia6}}"/>
					</div>
				</td>
			</tr>	
			@endif
		</tfoot>
	</div>
	@if(trim($iaas->agente2)!="Sin información")
		<!-- Fin agente 1-->
		<br><br><br>
	<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
				<div class="col-sm-10" style="width:720px" >
	<input disabled type="text" class="form-control" value = "{{$iaas->agente2}}"/>
				</div>
			</div>
	<div class="row">
		<tfoot>
		@if(trim($iaas->sensibilidad7)!="NINGUNA")
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad7}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia7}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia7}}"/>
					</div>
				</td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad8)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad8}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia8}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia8}}"/>
					</div>
				</td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad9)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad9}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia9}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia9}}"/>
					</div>
				</td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad10)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad10}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia10}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia10}}"/>
					</div>
				</td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad11)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad11}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia11}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia11}}"/>
					</div>
				</td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad12)!="NINGUNA")
			<br><br><br>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad12}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia12}}"/>
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia12}}"/>
					</div>
				</td>
			</tr>	
		@endif
		</tfoot>
	</div>
		<!-- Fin agente 2-->
	@endif	
		<br>
			@if(trim($iaas->cierre)=="no")
			<label class="col-sm-9 control-label"> Cerrar la Notificación de Infección <?php echo $contador2;?>
			<select  name="cerrar[]" class="horario"> 
			<option value="si">Si</option>
			<option value="no"selected>No</option>     
			</select>
			</label>
			@endif	
			@if(trim($iaas->cierre)=="si")
			<label class="col-sm-9 control-label"> IAAS FINALIZADA <?php echo $contador2;?>
			<select  name="cerrar[]" class="horario"> 
			<option value="si" selected>Si</option>
			<option value="no">No</option>     
			</select>
			</label>
			@endif
		<br>
	<?php $contador2=$contador2+1;?>
	@endforeach<!-- Fin Localizacion-->	
	</fieldset>
		<br><br>
			<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus"></span> Agregar Localización</a>
							</td>
						</tr>
			</tfoot>
		<br><br>

		<!-- Copia localizacion--> 
		<div id="templateRow2" class="row hide"><br>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaIngreso[]', null, array('id' => 'fechaIngreso', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaInicio[]', null, array('id' => 'fechaInicio', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
				<div class="col-sm-9">
					{{ Form::select('servicioIAAS[]', $UnidadesIAAS, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('localizacion[]', $localizacion, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('procedimiento[]', $procedimiento, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="row">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('agente1[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente1', 'class' => 'form-control')) }}
				</div>
			</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
						<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
				{{ Form::select('sensibilidad4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				{{ Form::select('sensibilidad5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>	
		</tfoot>
	</div>
		<!-- Fin agente 1-->
		<br><br><br>
	<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
				<div class="col-sm-10" style="width:720px" >
						{{ Form::select('agente2[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente2', 'class' => 'form-control')) }}
				</div>
			</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:235px">
					{{ Form::select('resistencia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>
			</tfoot>
	</div>
	<div class="row">
		<tfoot>
			<tr>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('sensibilidad12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
					{{ Form::select('intermedia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
				<td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:235px">
				{{ Form::select('resistencia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</td>
			</tr>	
		</tfoot>
	</div>
		<!-- Fin agente 2-->
			
		<br><br><br>
			<div class="input-group col-md-10">
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Agregar Localización</a>
					<a class="btn btn-default" onclick="deleteOtro(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Quitar Localización</a>
				</div>
			</div>
		</div> <!-- Fin Localizacion copia-->	
		<br><br>
		</fieldset>


	<div class="modal-footer">
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
				<button id="solicitar" type="submit" class="btn btn-primary">Actualizar</button>
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="col-sm-3">
				<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
				<button id="cerrar" type="button" onclick="liberar();" class="btn btn-primary">Cerrar Todas las Infecciones Notificadas</button>
			</div>
		</div>
	</div>

</div> <!-- Fin pestaña localizacion -->