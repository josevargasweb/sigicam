@extends("Templates/template")

@section("titulo")
	Pacientes en espera de categorización
@stop

@section("miga")
	<li><a href="#">Estadisticas</a></li>
	<li><a href="#" onclick='location.reload()'>Lista de pacientes no categorizados</a></li>
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>
	$("#urgenciaMenu").collapse();
	$( "#ingreso0" ).change(function() {
		$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'fechaTraslado');
	});

	var categorizar = function(idCaso, idUnidad){
		$(".id_caso").val(idCaso);
		if(idUnidad == 195 || idUnidad == 196){
			$("#modalFormularioRiesgo2").modal();
		}
		else{
			$("#modalFormularioRiesgo").modal();
		}
	}


	function btnRiesgoDependencia (){
		var valorDependencia = 0;

        caso = $(".id_caso").val();
        dependencia1 = $('#dependencia1').val();
        dependencia2 = $('#dependencia2').val();
        dependencia3 = $('#dependencia3').val();
        dependencia4 = $('#dependencia4').val();
        dependencia5 = $('#dependencia5').val();
        dependencia6 = $('#dependencia6').val();

        riesgo1 = $('#riesgo1').val();
        riesgo2 = $('#riesgo2').val();
        riesgo3 = $('#riesgo3').val();
        riesgo4 = $('#riesgo4').val();
        riesgo5 = $('#riesgo5').val();
        riesgo6 = $('#riesgo6').val();
        riesgo7 = $('#riesgo7').val();
        riesgo8 = $('#riesgo8').val();


		valorDependencia = parseInt($('#dependencia1').val()) +parseInt($('#dependencia2').val()) +parseInt($('#dependencia4').val()) +parseInt($('#dependencia5').val());

		if (parseInt($('#dependencia3').val()) > 10) {
			valorDependencia += parseInt($('#dependencia3').val().substr(0,1));
		}else{
			valorDependencia += parseInt($('#dependencia3').val());
		}

		if (parseInt($('#dependencia6').val()) > 10) {
			valorDependencia += parseInt($('#dependencia6').val().substr(0,1));
		}else{
			valorDependencia += parseInt($('#dependencia6').val());
		}

		var valorRiesgo = 0;

		valorRiesgo = parseInt($('#riesgo1').val()) + parseInt($('#riesgo2').val()) +parseInt($('#riesgo3').val());

		if (parseInt($('#riesgo4').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo4').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo4').val());
		}
		if (parseInt($('#riesgo5').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo5').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo5').val());
		}
		if (parseInt($('#riesgo6').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo6').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo6').val());
		}
		if (parseInt($('#riesgo7').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo7').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo7').val());
		}
		if (parseInt($('#riesgo8').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo8').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo8').val());
		}

		var riesgoDependencia = "";
		if (valorRiesgo >=19) {
			riesgoDependencia = "A";
		}else if(valorRiesgo >= 12 && valorRiesgo <= 18){
			riesgoDependencia = "B";
		}else if (valorRiesgo >= 6 && valorRiesgo <= 11) {
			riesgoDependencia = "C";
		}else{
			riesgoDependencia = "D";
		}


		if (valorDependencia >=13) {
			riesgoDependencia += "1";
		}else if(valorDependencia >= 7 && valorDependencia <= 12){
			riesgoDependencia += "2";
		}else{
			riesgoDependencia += "3";
		}

		$("#riesgo").val(riesgoDependencia);
		$("#div-comentario-riesgo").show();


        $.ajax({
			url: '{{URL::to("ingresarCategorizacion")}}',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {"caso":caso,"riesgoDependencia":riesgoDependencia,"dependencia1":dependencia1,"dependencia2":dependencia2,"dependencia3":dependencia3,"dependencia4":dependencia4,"dependencia5":dependencia5,"dependencia6":dependencia6,"riesgo1":riesgo1,"riesgo2":riesgo2,"riesgo3":riesgo3,"riesgo4":riesgo4,"riesgo5":riesgo5,"riesgo6":riesgo6,"riesgo7":riesgo7,"riesgo8":riesgo8},
			dataType: "json",
			type: "post",
			success: function(data){
				swalExito.fire({
				title: 'Exito!',
				text: data.exito,
				didOpen: function() {
					setTimeout(function() {
						location . reload();
					}, 2000)
				},
				});
			},
			error: function(error){
				console.log(error);
			}
		});


		$('#modalFormularioRiesgo').modal('hide');

	}


	function btnRiesgoDependencia2() {

        caso = $(".id_caso").val();
        dependencia1 = $('#dependencia1').val();
        dependencia2 = $('#dependencia2').val();
        dependencia3 = $('#dependencia3').val();
        dependencia4 = $('#dependencia4').val();
        dependencia5 = $('#dependencia5').val();

        riesgo1 = $('#riesgo1').val();
        riesgo2 = $('#riesgo2').val();
        riesgo3 = $('#riesgo3').val();
        riesgo4 = $('#riesgo4').val();
        riesgo5 = $('#riesgo5').val();
        riesgo6 = $('#riesgo6').val();
        riesgo7 = $('#riesgo7').val();
        riesgo8 = $('#riesgo8').val();
        riesgo9 = $('#riesgo9').val();

		//busco los select dentro del modal
		selects = $('#modalFormularioRiesgo2').find("select");

		sumaRiesgo =0;
		sumaDependencia =0;
		//recorro los select
		$.each(selects, function(i, val){
			//el id del input
			idInput = val.id;
			//saco el primer caracter para saber si es riesgo o dependencia
			primerCaracter = idInput.substr(0,1);

			if(primerCaracter == "d"){
				sumaDependencia += parseInt($(this).val().substr(0,1));
			}

			if(primerCaracter == "r"){
				sumaRiesgo += parseInt($(this).val().substr(0,1));
			}



		});


		if (sumaRiesgo >=20) {
			riesgoDependencia = "A";
		}else if(sumaRiesgo >= 13 && sumaRiesgo <= 19){
			riesgoDependencia = "B";
		}else if (sumaRiesgo >= 6 && sumaRiesgo <= 12) {
			riesgoDependencia = "C";
		}else{
			riesgoDependencia = "D";
		}


		if (sumaDependencia >=12) {
			riesgoDependencia += "1";
		}else if(sumaDependencia >= 6 && sumaDependencia <= 11){
			riesgoDependencia += "2";
		}else{
			riesgoDependencia += "3";
		}



		//$('#modalFormularioRiesgo2').modal('hide');



        $.ajax({
			url: '{{URL::to("ingresarCategorizacion")}}',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {"caso":caso,"riesgoDependencia":riesgoDependencia,"dependencia1":dependencia1,"dependencia2":dependencia2,"dependencia3":dependencia3,"dependencia4":dependencia4,"dependencia5":dependencia5,"riesgo1":riesgo1,"riesgo2":riesgo2,"riesgo3":riesgo3,"riesgo4":riesgo4,"riesgo5":riesgo5,"riesgo6":riesgo6,"riesgo7":riesgo7,"riesgo8":riesgo8,"riesgo9":riesgo9},
			dataType: "json",
			type: "post",
			success: function(data){
				swalExito.fire({
				title: 'Exito!',
				text: data.exito,
				didOpen: function() {
					setTimeout(function() {
						location . reload();
					}, 2000)
				},
				});
			},
			error: function(error){
				console.log(error);
			}
		});
	}




$(function() {
	$("#btnGuardarRiesgo").hide();

	/* table = $('#tablaCategorizar').dataTable({
            "bJQueryUI": true,
            "iDisplayLength": 2,
            "order": [[5,"desc"]],
            "language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
    }); */

	

	$(document).on("click", "#btnBuscar", function(){
		$("#dvLoading").show();
		$("#btnBuscar").attr('disabled',true);
		$("#btnGuardarRiesgo").attr('disabled',true);
		
		$.ajax({
			url: 'buscarPacientesSinCategorizar',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {unidad: $("#unidad").val()},
			dataType: "json",
			type: "post",
			success: function(data){
				$("#casos").empty();

				if (data[1] == "correcta") {
					if($( "#unidad option:selected" ).text() != "Hospitalización Domiciliaria" && data[0].original.cantidad_pediatricos != '' && data[0].original.cantidad_pediatricos > 0){
					$("#categorizacion_acompanamiento").removeClass("hidden");
				}else{
					$("#categorizacion_acompanamiento").addClass("hidden");
				}
				if($( "#unidad option:selected" ).text() == "Hospitalización Domiciliaria"){
					$("#categorizacion_acompanamiento").addClass("hidden");
					$("#categorizacion_atencion").addClass("hidden");
				}else{
					$("#categorizacion_atencion").removeClass("hidden");
				}

					if(typeof data[0].original.datos !== 'undefined' && data[0].original.datos.length > 0){
						$("#btnGuardarRiesgo").show();
					}else{
						$("#btnGuardarRiesgo").hide();
					}
					edad = '';
					atencion = '';
					if(typeof data[0].original.datos !== 'undefined'){
						data[0].original.datos.forEach(function(element){
							var html = "";
							var rut = "";
							if(element.rut){
								rut += element.rut;
								if(element.dv == 10){
									rut += "-K";
								}else{
									rut += "-"+element.dv;
								}
							}
							html += "<tr><td>"+rut+"</td>";
							html += "<td>"+element.nombre+"</td>";
							html += "<td>"+element.fecha_nacimiento+"</td>";
							html += "<td>"+element.area_funcional+"</td>";
							// html += "<td>"+element.sala+"</td>";
							// html += "<td>"+element.cama+"</td>";
							html += "<td>"+element.horas+"</td>";
							html += "<td>"+element.categorizacion_anterior+"</td>";
							
							html += "<td><div class='form-group' style='width: 100%;'><input class='form-control newCat' maxlength='2' name='categorizacion[]' type='text'></div>	<input class='form-control hidden' maxlength='2' name='categorizacion_id[]' value='"+element.id_caso+"' type='text'> </td>";
							if($( "#unidad option:selected" ).text() != "Hospitalización Domiciliaria"){
								if (element.select_atenciones !== undefined) {
								html += "<td>"+element.select_atenciones+"</td>";
								}else if(element.select_atenciones === undefined){
									html += "<td class='text-center'>--- <input name='categoria_atencion[]' hidden></td>";
								}
								if(data[0].original.cantidad_pediatricos > 0){
									if(element.select_acomapamientos !== undefined && element.edad <= 15 || element.select_acomapamientos !== undefined && element.edad == '' ){
										html += "<td>"+element.select_acomapamientos+"</td>";
									}else{
										html += "<td class='text-center'>--- <input name='categoria_acompanamiento[]' hidden></td>";
									}
								}
	
							}
						
	
							html += "<td>"+element.select_especialidades+"</td></tr>";
							
	
							$("#casos").append(html);
							$(".slctpikr").selectpicker();
	
							//viendo si puedo validar desde aca :c
							var caso = String(element.id_caso);
							var txt_espe = "especialidad-";
							var espe_caso = txt_espe.concat(caso);
							var desdeback = $("#"+espe_caso).val();
	
						});
					}
				
					
					$('#guardarRiesgo').data('bootstrapValidator').addField('categorizacion[]');
					$('#guardarRiesgo').data('bootstrapValidator').addField('especialidad[]');
					$("#guardarRiesgo").data('bootstrapValidator').resetForm();

				}else{
					$("#btnGuardarRiesgo").hide();
					swalInfo2.fire({
					title: 'Información',
					text:"El horario para categorizar pacientes es de <b>00:00</b> - <b>10:00</b>"
					});
				}

				$("#btnBuscar").attr('disabled',false);
				$("#btnGuardarRiesgo").attr('disabled',false);
				$("#dvLoading").hide();
			},
			error: function(error){
				console.log(error);
			}
		});
	});


	$( ".newCat" ).on("change", function() {
		$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', false);

	});

	$("#guardarRiesgo").bootstrapValidator({
		excluded: ':disabled',
		feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
		fields: {
			"categorizacion[]": {
				validators:{
					/* notEmpty: {
							message: 'El riesgo es obligatorio'
					},*/
					callback: {
						callback: function(value, validator, $field){
							var array = ['a1','a2','a3','A1','A2','A3','b1','b2','b3','B1','B2','B3','c1','c2','c3','C1','C2','C3','d1','d2','d3','D1','D2','D3'];

							ubicacion = jQuery.inArray( value , array );

							if(ubicacion >= 0 || value == ''){
								return true;
							}else{
								return {valid: false, message: "Este valor es incorrecto"};
							}
						}
					}
				}
			},
			"categoria_acompanamiento[]": {
				validators:{
					notEmpty: {
							message: 'Seleccione al menos una opción'
					}
				}
			},
			"categoria_atencion[]": {
				validators:{
					notEmpty: {
							message: 'Seleccione al menos una opción'
					}
				}
			},
			"especialidad[]": {
				validators:{
					notEmpty: {
							message: 'Seleccione al menos una opción'
					}
				}
			},

		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt, data){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#btnGuardarRiesgo").attr('disabled',true);

		// aqui ocurrira la magia
		var cant_pacientes = $("[name='especialidad[]']").length;
		for (let index = 0; index < cant_pacientes; index++) {
			var valor = $("[name='especialidad[]']").eq(index).val();
			var id = $("[name='especialidad[]']").eq(index).attr("id");
			var text = id.split('-');
			var result = text[1];
			
			$("#espeid-"+result).val(valor);
		}
		// aqui ocurrira la magia		
		$.ajax({
			url: "{{URL::to('/urgencia')}}/registrarRiesgos",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:  $form .serialize(),
			dataType: "json",
			type: "post",
			success: function(data){
				if (data.exito) {
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

				if (data.error) {
					swalError.fire({
					title: 'Error',
					text:data.error
					}).then(function(result) {
					if (result.isDenied) {
							location . reload();
					}
					});
				}
			},
			error: function(error){
				console.log(error);
				$("#btnGuardarRiesgo").attr('disabled',false);
			}
		});

	});

    $('#tablaDocDer').dataTable({
		//responsive: true,
		"aaSorting": [[0, "desc"]],
		dom: 'Bfrtip',
		buttons: [
			{
				extend: 'excelHtml5',
				messageTop: 'Pacientes en espera de categorización',
				exportOptions: {
					columns: [0,1,2,3,4]
				} ,
				customize: function (xlsx) {
					var sheet = xlsx.xl.worksheets['sheet1.xml'];
					var clRow = $('row', sheet);
					//$('row c', sheet).attr( 's', '25' );  //bordes
					$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
					$('row', sheet).attr('ht',15);
					$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
					$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
				}
			}
		],
		"iDisplayLength": 15,
		"order": [[ 7, "desc" ]],
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		},
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			var texto = $('td', nRow)[7].outerText;
			var categorizacion = $('td', nRow)[8].outerText;
			if (texto >= 8 && categorizacion == ""){
				$('td', nRow).css('color', '#d14d33');
				$('td', nRow).css('font-weight', 'bold');
			}
		}
	});
});

// $("#especialidad").on("change", function(){
// 	var largo= $("#especialidad").children(':selected').length;
// 	$("#especialidad_item").val(largo);
// 	$('#buscarCamasForm').bootstrapValidator('revalidateField', 'especialidad_item');
// });

</script>

@stop

@section("section")

    <style>
		.loader {
			position: fixed;
			left: 270px;
			top: 280px;
			width: 70%;
			height: 50%;
			z-index: 9999;
			background: url("{{URL::to('/')}}/images/default.gif") 50% 50% no-repeat #fff;
			background-color: transparent !important;
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

        #consultar{
            margin-top: 20px;
        }

		@media (min-width: 1200px)
		{
			.lg-table{
				width: 1300px;
			}
		}
		
    </style>



        {{-- <div class="row">
				<div class="col-sm-12">
					<legend>Espera de categorización</legend>
					<p style="color:#d14d33; font-size: 14px;">*Pacientes en rojo llevan más de 8 horas sin ser categorizados desde su hospitalización</p>
				</div>
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table id="tablaDocDer" class="table table-striped table-bordered table-hover">
                        <tfoot>
                            <tr>
							<th>Opciones</th>
                                <th>Rut</th>
                               <th>Nombre</th>
                               <th>Fecha nacimiento</th>
                               <th>Area funcional</th>
							   <th>Unidad funcional</th>
							   <th>sala</th>
								<th>cama</th>
								<th>Tiempo sin categorizar</th>
                                <th>Categorización anterior</th>

                            </tr>
                        </tfoot>
                        <thead>
                            <tr>
							<th>Opciones</th>
								<th>Rut</th>
								<th>Nombre</th>
								<th>Fecha nacimiento</th>
								<th>Area funcional</th>
								<th>Unidad funcional</th>
								<th>sala</th>
								<th>cama</th>
								<th>Tiempo sin categorizar</th>
								<th>Categorización anterior</th>

                            </tr>
                        </thead>
                        <tbody>
                        @foreach($casos as $caso)
                            <tr>
							<td>
                                    <div class="dropdown">
                                         <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                            Opciones
                                             <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
                                        <li role="presentation"><a class='cursor' onclick='categorizar({{$caso['id_caso']}},{{$caso['id_unidad_en_establecimiento']}})'>Categorizar</a></li>
                                        <li role="presentation"><a href="{{URL::to('/')}}/paciente/editar/{{$caso['paciente_id']}}">Editar paciente</a></li>

                                        </ul>
                                    </div>
                                </td>
                                <td>{{$caso['rut']}}@if($caso['rut'])-@endif{{$caso['dv']}}</td>
                                <td>{{$caso['nombre']}}</td>
                                <td>{{$caso['fecha_nacimiento']}}</td>
                                <td>{{$caso["area_funcional"]}}</td>
								<td>{{$caso["unidad_funcional"]}}</td>
								<td>{{$caso["sala"]}}</td>
								<td>{{$caso["cama"]}}</td>
								<td style="text-align: center;">{{$caso["horas"]}}</td>
                                <td>{{$caso["categorizacion_anterior"]}}</td>

                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                    </div>
                </div>
			</div> --}}

			<div class="row container">
				<legend>Espera de categorización</legend>
			</div>
			<br>

			{{ Form::label('unidad', 'Seleccione el área que desea categorizar')}}
			{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'guardarRiesgo')) }}
			<div class="row container">
				<div class="col-md-3">
					{{ Form::select('unidad', $servicios, null, array('id' => 'unidad', 'class' => 'form-control')) }}
				</div>

				<div class="col-md-2">
					<input id="btnBuscar" type="button" name="" class="btn btn-primary" value="Buscar Pacientes">
				</div>
			</div>

			<br>

			<div class="container lg-table">
				<table id="tablaCategorizar" class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>Rut</th>
							<th>Nombre</th>
							<th>Fecha de Nacimiento</th>
							<th>Área / Servicio / Sala / Cama</th>
							<th>Tiempo sin categorizar</th>
							<th>Categorización anterior</th>
							<th  style="width: 10% !important;">Categorización actual</th>
							<th id="categorizacion_atencion" class="hidden text-center" style="width: 15% !important;">Estado paciente</th>
							<th id="categorizacion_acompanamiento" class="hidden" style="width: 15% !important;">Acompañamiento</th>
							<th style="width: 20% !important;">Especialidades</th>
						</tr>
					</thead>
	
					<tbody id="casos">
	
					</tbody>
	
				</table>
				<div class="row">
					<input id="btnGuardarRiesgo" type="submit" name="" class="btn btn-primary" value="Guardar Categorizaciones">
				</div>
			</div>
			

			<div id="dvLoading" class="loader" hidden></div>
			<br>
			

			{{ Form::close() }}
@stop
