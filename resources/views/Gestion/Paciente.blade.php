@extends("Templates/template")

@section("titulo")
Ingresar Paciente
@stop

@section("script")
<script>

	var marcarCama=function(idSala, idCama){
		$("#salaReserva").val(idSala);
		$("#camaReserva").val(idCama);
		if(idSala != -1 && idCama != -1){
			$("#msgCamaSelected strong").text("Ha seleccionado la cama "+idCama+" de la sala "+idSala);
			$("#msgCamaSelected").show();
		}
		else $("#msgCamaSelected").hide();
	}

	
	var camaIsSelected=function(){
		var idSala=parseInt($("#salaReserva").val());
		var idCama=parseInt($("#camaReserva").val());
		console.log( idSala!=-1);
		console.log(idCama!=-1);
		return (idSala != -1 && idCama != -1);
	}

	var cambiarDeshabilitacionAsginacion=function(disabled){
		$("#tipo").prop("disabled", disabled);
		$("#motivo").prop("disabled", disabled);
		$("#horas").prop("disabled", disabled);
		$("input[name='rdCamas']").prop("disabled", disabled);
	}

	var buscarCama=function(){
		$.ajax({
			url: "{{URL::to('/')}}/{{$unidad}}/buscarCama",
			type: "post",
			data: {sexo: $("#sexo").val(), unidad: "{{$unidad}}"},
			dataType: "json",
			success: function(data){
				if(!data.tiene) $("#derivar").show();
				$("#mapaSalas").empty();
				$("#msgCamaSelected").hide();
				$("#cama").val("");
				$("#sala").val("");
				var salas = $.map(data.salas, function(value, index) {
					return [value];
				});
				var totalDivs=(Math.round(salas.length/3) == 0) ? 1 : Math.round(salas.length/3);
				for(var i=1; i<=totalDivs; i++){
					var id="div-sala-"+i;
					var div="<div id='"+id+"' class='row'></div><br>";
					$("#mapaSalas").append(div);
				}
				var range=0;
				var divIt=1;
				var sala=1;
				while(range < salas.length){
					var salasSelect=salas.slice(range, range+3);
					for(var i=0; i<salasSelect.length; i++){
						var divSala="<div id='sala-"+sala+"' class='col-md-4 well well-sm'><fieldset><legend>Sala "+sala+"</legend><div class='row'>";

						var salasSelected = $.map(salasSelect[i], function(value, index) {
							return [value];
						});
						for(var k=1; k<=salasSelected.length; k++){
							var id="div-cama-"+sala+"-"+k;
							var div="<div id='"+id+"' class='col-md-3' style='margin-top: 8px'></div>";
							divSala+=div;
						}
						divSala+="</div></fieldset>";
						$("#div-sala-"+divIt).append(divSala);
						var rangeCama=0;
						for(var j=0; j<salasSelected.length; j++){
							var div=salasSelect[i][j].img;
							$("#div-cama-"+sala+"-"+(j+1)).append(div);
						}
						sala+=1;
					}
					range+=3;
					divIt+=1;
				}
				$("#asignacion").show();
			},
			error: function(error){
				console.log(errror);
			}
		});
	}

	$(function(){

		$("#salaReserva").val("-1");
		$("#camaReserva").val("-1");

		//tieneCamas();

		$("#rut").on("blur", function(){
			validarRutAlias();
			var rut=$(this).val();
			if(rut != ""){
				validaRut(this, document.getElementById("dv"));
			}
		});


		$("#dv").on("blur", function(){
			var rut=$("#rut").val();
			if(rut != ""){
				validaRut(document.getElementById("rut"), this);
			}
		});

		$("#fechaNac").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true
		}).on("changeDate", function(){
			validarFechaNac(document.getElementById("fechaNac"));
		});

		$("#fechaNac").on("blur", function(){
			validarFechaNac(this);
		});

		$("#buscarCamasForm").submit(function(evt){
			evt.preventDefault();
			if(camaIsSelected()){
				$.ajax({
					url: $(this).prop("action"),
					type: "post",
					dataType: "json",
					data: $(this).serialize(),
					success: function(data){
						if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								location . reload();
							}, 2000)
						},
						});
						}
						if(data.error){
						swalError.fire({
						title: 'Error',
						text:data.error
						});
							console.log(data.error);
						}
						console.log(data);
					},
					error: function(error){
						console.log(error);
					}
				});
			}
			else{
				swalWarning.fire({
				title: 'Información',
				text:"Debe seleccionar una cama"
				})
			} 
		});

		buscarCama();

		$("#tipo").on("change", function(){			
			if($(this).val() == "ingresar"){
				$('#horas').prop( "disabled", true );
				$('#motivo').prop( "disabled", true );		
				$('#horas').removeAttr('required');
				$('#motivo').removeAttr('required');

			}else if($(this).val() == "reservar"){
				$('#horas').prop( "disabled", false );
				$('#motivo').prop( "disabled", false );
				$('#horas').prop('required',true);
				$('#motivo').prop( "required", true );			
			}
		});

	});
</script>
@stop

@section("miga")
<nav class="navbar navbar-default navbar-static subir-nav-header miga">
	@include("Templates/migaCollapse")
	<div class="collapse navbar-collapse bs-js-navbar-collapse">
		<div class="navbar-header">
			<ol class="breadcrumb listaMiga">
				<li><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
				<li><a href="#">Gestión de Camas</a></li>
				<li><a href="#">Ingresar Paciente</a></li>
			</ol>
		</div>
		@include("Templates/migaAcciones")
	</div>
</nav>
@stop

@section("section")
{{ Form::open(array('url' => 'asignarCama', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'buscarCamasForm')) }}
{{ Form::hidden('sala', '', array('id' => 'salaReserva')) }}
{{ Form::hidden('cama', '', array('id' => 'camaReserva')) }}
<fieldset>
	<legend>Gestión Camas {{$unidad}}</legend>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="run" class="col-sm-2 control-label">Run: </label>
			<div class="col-sm-10">
				<div class="input-group">
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
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
			<label for="alias" class="col-sm-2 control-label">Alias: </label>
			<div class="col-sm-10">
				{{Form::text('alias', null, array('id' => 'alias', 'class' => 'form-control', 'onblur' => 'validarRutAlias();'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Género: </label>
			<div class="col-sm-10">
				{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="riesgo" class="col-sm-2 control-label">Previsión: </label>
			<div class="col-sm-10">
				{{ Form::select('prevision', $prevision, null, array('class' => 'form-control')) }}
			</div>
		</div>
		<div class="form-group col-md-6">
			{{HTML::link('derivar', 'Derivar', array('class' => 'btn btn-danger', 'style' => 'display: none;', 'id' => 'derivar'))}}
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
			<div class="col-sm-10">
				{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control', 'required' => true))}}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>
			<div class="col-sm-10">
				{{ Form::select('riesgo', $riesgo, null, array('class' => 'form-control')) }}
			</div>
		</div>
		<div class="form-group col-md-6">
			{{HTML::link('derivar', 'Derivar', array('class' => 'btn btn-danger', 'style' => 'display: none;', 'id' => 'derivar'))}}
		</div>
	</div>
	<div id="asignacion">
		<div id="mapaSalas" style="margin-top: 40px;">
		</div>
		<div class="row">
			<div id="msgCamaSelected" style="display: none; font-size: 14px;" class="alert alert-info" role="alert">
				<span class="glyphicon glyphicon-info-sign"></span>
				<strong></strong>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<div class="row">
					<div class="form-group col-md-12">
						{{ Form::select('tipo', array('ingresar' => 'Ingresar', 'reservar' => 'Reservar'), null, array('id' => 'tipo', 'class' => 'form-control')) }}
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Horas de reserva: </label>
						<div class="col-sm-10">
							{{ Form::input('number', 'horas', null, ['id' => 'horas', 'class' => 'form-control', 'min' => '1', 'disabled']) }}
						</div>
					</div>
					{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
					{{ HTML::link(URL::previous(), 'Cancelar', array('class' => 'btn btn-danger'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="motivo" class="col-sm-2 control-label">Motivo: </label>
				<div class="col-sm-10">
					{{Form::textarea('motivo', null, array('id' => 'motivo', 'class' => 'form-control', 'rows' => '5',  'disabled'))}}
				</div>
			</div>
		</div>
	</div>
</fieldset>

{{ Form::close() }}

@stop