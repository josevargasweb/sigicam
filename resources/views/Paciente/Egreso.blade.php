@extends("Templates/template")

@section("titulo")
Editar paciente
@stop

@section("script")
<script>

	var pdf = false;

	$( "#fichaEgreso" ).click(function() {
		pdf = true;
	});

	$( "#guardarDatos" ).click(function() {
		pdf = false;
	});


	//funcion eliminar recien nacidos
	function eliminar(esto) {

		var nFilas = $("#rn_table >tbody >tr").length;
		var id_fila = $(esto).parent().parent().find('td').eq(0).html();
		var eliminar = id_fila;
		var siguiente =$(esto).parent().parent().next();


		while(id_fila <= nFilas){

			var nuevo_id_fila = id_fila;
			console.log("siguiente while", siguiente.find('td').eq(0).html(nuevo_id_fila));
			siguiente = siguiente.next();

			id_fila++;
		}
		document.getElementById("rn_table").deleteRow(eliminar);


	}

	function eliminarDiagn(esto) {

		var id_fila = $(esto).parent().parent().find('td').eq(0).html();
		var eliminar = id_fila;
		var siguiente =$(esto).parent().parent().next();


		while(id_fila <= nFilas){

			var nuevo_id_fila = id_fila;

			siguiente = siguiente.next();

			id_fila++;
		}
		document.getElementById("rn_table").deleteRow(eliminar);


	}

	$(function(){

		//Cambiar diseño de busqueda
		$(".main").removeClass("col-md-7");
		$(".main").addClass("col-md-9");


		//subir en el orden de nacimiento
		$(document).on("click", "#subir", function(){
			var id_fila_subir = $(this).parent().parent().find('button').eq(2).data('id');
			var fila_completa = $(this).parent().parent();
			var indice_fila = fila_completa.index();
			console.log("fila click subir: ", id_fila_subir);

			if(indice_fila > 0){
				var prioridad_fila_subir = fila_completa.find('td').eq(0).text();
				var prioridad_fila_bajar = fila_completa.prev().find('td').eq(0).text();
				var fila_cambio = fila_completa.prev();
				var id_fila_bajar = fila_cambio.find('button').eq(2).data('id');
				fila_completa.find('td').eq(0).text(prioridad_fila_bajar);
				fila_completa.prev().find('td').eq(0).text(prioridad_fila_subir);
				fila_completa.insertBefore(fila_completa.prev());
				console.log("fila bajar: ", id_fila_bajar);

			}
		});
		//bajar en el orden de nacimiento
		$(document).on("click", "#bajar", function(){
			var id_fila_bajar = $(this).parent().parent().find('button').eq(2).data('id');
			var fila_completa = $(this).parent().parent();
			var prioridad_fila_bajar = fila_completa.find('td').eq(0).text();
			var prioridad_fila_subir = fila_completa.next().find('td').eq(0).text();
			var indice_fila = fila_completa.index();

			var total_filas = $("#rn_table >tbody >tr").length;
			if(indice_fila +1 < total_filas){
				var fila_cambio = fila_completa.next();
				var id_fila_subir = fila_cambio.find('button').eq(2).data('id');
				fila_completa.find('td').eq(0).text(prioridad_fila_subir);
				fila_completa.next().find('td').eq(0).text(prioridad_fila_bajar);
				fila_completa.insertAfter(fila_completa.next());
			}
		});

		//SECCION DE BLOQUEOS DE INFORMACION EN CASO DE TENER DATOS EN BD

		//datos principales del boletin de egreso
		@if($informe_egreso)
			$("#num_egreso").val('{{$informe_egreso->n_egreso}}');
			$("#n_admision").val('{{$informe_egreso->n_admision}}');
			$("#cod_fun_egreso").val('{{$informe_egreso->cod_fun_egreso}}');
			$("#cod_ser_egreso").val('{{$informe_egreso->cod_ser_egreso}}');
		@endif

		@if($caso)
			@if($caso->ficha_clinica)
				$("#n_clinico").val('{{$caso->ficha_clinica}}');
			@endif
		@endif



		//el apellido paterno del paciente
		@if ($paciente->apellido_paterno != null)
			$("#apellido_p").val('{{$paciente->apellido_paterno}}');
			/* $("#apellido_p").attr("disabled",true); */
		@endif

		//el apellido materno del paciente
		@if ($paciente->apellido_materno != null)
			$("#apellid_m").val('{{$paciente->apellido_materno}}');
			/* $("#apellid_m").attr("disabled",true); */
		@endif

		//el nombre del paciente
		@if ($paciente->nombre != null)
			$("#nombres").val('{{$paciente->nombre}}');
			$("#nombres").attr("disabled",true);
		@endif

		//comprobando datos del pasaporte
		// console.log("extranjero: ", {{$extranjero}});
		// console.log("tipo_identificacion: ", {{$tipo_identificacion}});
		// console.log("num_identificacion: ", {{$num_identificacion}});
		//fin

		//oculta el numero de pasaporte o run del paciente en caso de tenerlo
		 //@if($tipo_identificacion == 1)
		//	$("#campo_run").removeAttr('hidden');
		// @elseif($tipo_identificacion == 2 || $tipo_identificacion == 4)
		// 	$("#campo_pasaporte").removeAttr('hidden');
		// @endif
		// @if($tipo_identificacion != null)
		// 	@if($tipo_identificacion == 1)
		// 	$("#campo_run").removeAttr('hidden');
		// 	@endif

		// 	@if($tipo_identificacion == 2 || $tipo_identificacion == 4)
		// 		@if($num_identificacion != null)
		// 		$("#campo_pasaporte").removeAttr('hidden');
		// 		$("#pasaporte").val('{{$num_identificacion}}');
		// 		@endif
		// 	@endif
		//@endif

		//si tiene rut, se llenan los  datos del rut, dv, tipo de identificacion y se bloquean
		@if ($paciente->rut != null)
			$("#campo_run").removeAttr('hidden');
			$("#rut").val('{{$paciente->rut}}');
			$("#rut").attr("disabled",true);

			$("#dv").val('{{$paciente->dv}}');
			$("#dv").attr("disabled",true);

			$("#tipo_documento").val('{{$tipo_identificacion}}');
			$("#tipo_documento").attr("disabled",true);
		@else
		$("#tipo_documento").val('{{$tipo_identificacion}}');
			@if($tipo_identificacion != null)
				@if($tipo_identificacion == 1)
				$("#campo_run").removeAttr('hidden');
				@endif

				@if($tipo_identificacion == 2 || $tipo_identificacion == 4)
					@if($num_identificacion != null)
					$("#campo_pasaporte").removeAttr('hidden');
					$("#pasaporte").val('{{$num_identificacion}}');
					@endif
				@endif
			@endif
		// console.log("sin ru");
		@endif

		//genero de la persona
		@if ($sexo != null)
			$("#genero").val('{{$sexo}}');
			$("#genero").attr("disabled",true);
		@endif

		//fecha de nacimiento, edad y unidad de medida
		@if( $paciente->fecha_nacimiento != false)
			$("#fecha_nac_u").val( "{{$paciente->fecha_nacimiento}}" );
			$("#fecha_nac_u").attr("disabled",true);

			$("#edad_paciente").val( "{{$edad}}" );
			$("#edad_paciente").attr("disabled",true);

			$("#u_medida_edad").val( "{{$unidad_medida}}" );
			$("#u_medida_edad").attr("disabled",true);
		@endif

		//pueblo indigena
		@if( $paciente->pueblo_indigena != null)
			$("#pueblo_ind").val( "{{$paciente->pueblo_indigena}}" );

			@if($paciente->pueblo_indigena == 'Otro' )
				$("#esp_pueblo").val( "{{$paciente->detalle_pueblo_indigena}}" );
				$(".cla_ind").removeAttr("hidden");
			@endif
		@endif

		//pais origen
		@if( $paciente->id_pais != null)
			$("#nombre_pais").val( "{{$paciente->id_pais}}" );
			//$("#nombre_pais").attr("disabled",true);
		@endif

		//categoria ocupacional
		@if($paciente->categoria_ocupacional)
			$("#cat_ocup").val('{{$paciente->categoria_ocupacional}}');
			$("#cat_activo").val("99");
			@if($paciente->categoria_ocupacional == 'activos')
				$(".cat_ocup_activo").removeAttr("hidden");
				$("#cat_activo").val('{{$paciente->categoria_activo}}');
			@endif
		@endif

		//nivel de instruccion
		@if( $paciente->nivel_instruccion != null)
			$("#educacion").val( "{{$paciente->nivel_instruccion}}" );
			//$("#educacion").attr("disabled",true);
		@endif

		//telefono fijo es el que se ingresa en TELEFONO
		@if(!empty($telefonocasa->tipo))
			@if( $telefonocasa->tipo == "Casa")
				$("#tel_fijo").val( "{{$telefonocasa->telefono}}" );
				$("#tel_fijo").attr("disabled",true);
			@endif
		@endif

		//telefono movil

		@if(!empty($telefonomovil->tipo))
			@if( $telefonomovil->tipo == "Movil")
				$("#tel_movil").val( "{{$telefonomovil->telefono}}" );
				$("#id_movil").val( "{{$telefonomovil->id}}" );
				$("#tel_movil").attr("disabled",true);
			@endif
		@endif
		
		/* if(!empty($telefonomovil->tipo))
			if( $telefonomovil->tipo == "Movil")
				if($key == 0)
					$("#tel_movil").val( "$telefono->telefono" );
					$("#id_movil").val( "$telefono->id" );
				console.log(' $telefono->telefono  ');
				endif
				if($key > 0)
					html = "<input type='text' name='tmovil[]' style='width: 200px; margin-top:-35px; margin-left: 220px;' class='form-control' id='tel_movil"+$key+"'>";
					$( "#tmovil" ).append(html);
					$("#tel_movil"+$key).val("$telefono->telefono");
					$("#tel_movil"+$key).attr("disabled",true);
				endif
			endif
		endif */


		//tipo de calle
		@if( $paciente->tipo_direccion != null)
			$("#tipo_calle").val( "{{$paciente->tipo_direccion}}" );
			$("#tipo_calle").attr("disabled",true);
		@endif

		//nombre de calle
		@if( $paciente->calle != null)
			$("#calle").val( "{{$paciente->calle}}" );
			$("#calle").attr("disabled",true);
		@endif

		//numero de calle
		@if( $paciente->numero != null)
			$("#calle_num").val( "{{$paciente->numero}}" );
			$("#calle_num").attr("disabled",true);
		@endif

		//comuna a la que pertenece
		@if( $paciente->id_comuna != null)
			$("#comuna").val( "{{$paciente->id_comuna}}" );
			$("#comuna").attr("disabled",true);
		@endif

		//tipo de prevision
		@if( $prevision_18 != null)
			$("#prevision").val( "{{$prevision_18}}" );
			$("#prevision").attr("disabled",true);

			@if ( $prevision_18 == 1 )
				$("#tramo_fonasa").val( "{{$beneficiario_19}}" );
				$("#tramo_fonasa").attr("disabled",true);
			@endif
		@endif

		//leyes previsionales
		$("#ley_previsional_opc").val("false");

		@if( $caso->leyes_previsionales != null)
			$("#ley_previsional_opc").val( '{{$caso->leyes_previsionales}}' );
			@if($caso->leyes_previsionales == 'true')

				/* $("#ley_previsional_opc").attr("disabled",true);	 */
				$("#ley_previsional_parrafo").removeClass("ley");
				$("#ley_previsional").removeClass("ley");
				//ley que se aplica
				@if( $caso->ley != null)
					$("#ley_previsional").val( "{{$caso->ley}}" );
					/* $("#ley_previsional").attr("disabled",true);	 */
				@endif
			@endif
		@endif

		//modalidad fonasa
		@if( $caso->modalidad_fonasa != null)
			$("#mod_aten").val( '{{$caso->modalidad_fonasa}}' );
		@endif


		//INGRESO PACIENTE
		$("#hr").val("{{$hr}}");
		$("#hr").attr("disabled",true);
		$("#min").val("{{$min}}");
		$("#min").attr("disabled",true);

		//fechas de traslados y ingreso
		cod_traslados = 0;
		@foreach($fecha_hosp as $key => $fecha)
			/* console.log("algo", "{{$fecha}}"); */
			@if($key == 0)
				$("#fecha_ingreso").val("{{$fecha}}");
				$("#fecha_ingreso").attr("disabled",true);
			@elseif($key <= 4)
				$("#fecha_ingreso"+{{$key}}).val("{{$fecha}}");
				$("#fecha_ingreso"+{{$key}}).attr("disabled",true);
			@else
				html = "<div class='row' style='margin-bottom:10px; margin-top:0;'> <div class='col-md-6'> 	<div class='col-md-offset-6 col-md-6' style='padding-top: 25px;'> <input type='text' name='fechaTraslado[]' style='text-align: right;' class='form-control fechaIngreso ' id='fecha_ingreso"+{{$key}}+"'> </div> </div> <div class='col-md-3 areaFuncional' style='padding-top: 25px;'>	<input type='text' name='areaFuncional[]' class='form-control typeahead' id='areaFuncional"+{{$key}}+"' data-id ='"+{{$key}}+"'> </div> <div class='col-md-3' style='padding-top: 25px;'> <div class='col-md-6'> <input type='text' name='codigoUnidad[]' class='form-control' id='codUnidad"+{{$key}}+"'> </div> <div class='col-md-6'> <input type='text' name='servicioClinico[]' class='form-control' id='codServicio"+{{$key}}+"'> </div> </div> </div>";


				$( "#trasladosExtras" ).append(html);

				$("#fecha_ingreso"+{{$key}}).val("{{$fecha}}");
				$("#fecha_ingreso"+{{$key}}).attr("disabled",true);


			@endif
			cod_traslados = {{$key}};
			/* console.log("traslado", cod_traslados); */
			//fecha_ingreso area_funcional cod_funcional cod_servicio
			//console.log("key:", {{$key}});
		@endforeach

		//area funcional del ingreso y tralados
		@foreach($unidad_f as $key => $unidad)
			//console.log("algo", "{{$unidad}}");
			//console.log("key", "{{$key}}");

			@if($key == 0)
				$("#area_funcional").val("{{$unidad}}")
				$("#area_funcional").attr("disabled",true);
			@elseif($key >= 1)
				$("#areaFuncional"+{{$key}}).val("{{$unidad}}");
				$("#areaFuncional"+{{$key}}).attr("disabled",true);
			@endif

		@endforeach

		@foreach($cod_area as $key => $cod_a)
			/* console.log("algo", "{{$cod_a}}"); */

			@if($key == 0)
				$("#codUnidad0").val("{{$cod_a}}")
				//$("#codUnidad0").attr("disabled",true);
			@elseif($key >= 1)
				$("#codUnidad"+{{$key}}).val("{{$cod_a}}");
				//$("#codUnidad"+{{$key}}).attr("disabled",true);
			@endif

		@endforeach

		@foreach($servicio_c as $key => $cod_s)
			/* console.log("algo", "{{$cod_s}}"); */

			@if($key == 0)
				$("#codServicio0").val("{{$cod_s}}")
				//$("#codServicio0").attr("disabled",true);
			@elseif($key >= 1)
				$("#codServicio"+{{$key}}).val("{{$cod_s}}");
				//$("#codServicio"+{{$key}}).attr("disabled",true);
			@endif

		@endforeach


		//egreso
		@if( $p_egresado == 1 )
			$("#hr_egreso").attr("disabled",true);
			$("#min_egreso").attr("disabled",true);
			$("#fecha_egreso").attr("disabled",true);
		@endif
		$("#hr_egreso").val("{{$hr_egreso}}");

		$("#min_egreso").val("{{$min_egreso}}");

		$("#fecha_egreso").val("{{$fecha_egreso}}");

		$("#estada").val("{{$estada}}");
		$("#estada").attr("disabled",true);

		//fallecimiento del pacietne
		@if($caso->condicion_egreso != null)
			//si tiene condicion de egreso
			$("#cond_egreso").val('{{$caso->condicion_egreso}}');
		@else
		//sino saca algo referente al motivo de termino que tuvo el paciente
			@if( $caso->motivo_termino == 'fallecimiento')
				$("#cond_egreso").val('fallecido');
			@else
				$("#cond_egreso").val('vivo');
			@endif
		@endif

		//diagnostico
		@if( $diagnostico_principal->diagnostico != null)
			$("#diagn_principal").val("{{$diagnostico_principal->diagnostico}}");
			$("#diagn_principal").attr("disabled",true);
			$("#cie_10").val("{{$diagnostico_principal->id_cie_10}}");
			$("#cie_10").attr("disabled",true);
		@endif

		@foreach($otro_diagnostico as $key => $diagnostico)

			@if($key > 1)

				html = "<div class='row'> <div class='col-md-offset-3 col-md-6 diagnostico'> <input type='text' name='diagnostico[]' class='form-control typeahead' id='otro_diagnostico"+{{$key}}+"' data-id ='"+{{$key}}+"' disabled> </div> <div class='col-md-3'> <input type='text' name='cod_diagn[]' id='cie_10_otro_diagn"+{{$key}}+"' class='form-control' disabled> </div> </div> <br>";

				$( "#diagnsExtras" ).append(html);

			@endif
			$("#otro_diagnostico"+{{$key}}).val('{{$diagnostico->diagnostico}}');
			$("#cie_10_otro_diagn"+{{$key}}).val('{{$diagnostico->id_cie_10}}');

		@endforeach

		//causa externa
		@if( $causa_externa != null)
			$("#diagn_causa_externa").val("{{$causa_externa}}");
			$("#diagn_causa_externa").attr("disabled",true);
		@endif

		@if ($procedencia_22 == 4 || $procedencia_22 == 6)
			$("#hosp_procedencia").removeAttr('disabled');
		@endif

		@if($medico != false)

			$("#especialidad").val("{{$medico->especialidad}}");
			$("#apellidoP_medico").val("{{$medico->apellido_p}}");
			$("#apellidoM_medico").val("{{$medico->apellido_m}}");
			$("#nombres_medico").val("{{$medico->nombre_medico}}");
			$("#rut_medico").val("{{$medico->rut_medico}}");
			$("#dv_medico").val("{{$medico->dv_medico}}");
			$("#id_medico").val("{{$medico->id_medico}}");

		@endif

		//intervencion quirurgica

		@if($intervencion_quirurgica)
			$("#int_qu").val('{{$intervencion_quirurgica->intervencion_quirurgica}}');
			console.log("im", '{{$intervencion_quirurgica->intervencion_quirurgica}}');
			$("#int_qu_pr").val('{{$intervencion_quirurgica->intervencion_quirurgica_principal}}');
			$("#codigo_fo1").val('{{$intervencion_quirurgica->codigo_intervencion_quirurgica_principal}}');
			$("#ot_int_qu").val('{{$intervencion_quirurgica->otra_intervencion_quirurgica}}');
			$("#codigo_fo2").val('{{$intervencion_quirurgica->codigo_otra_intervencion_quirurgica}}');
			$("#proc").val('{{$intervencion_quirurgica->procedimiento}}');
			console.log("im2", '{{$intervencion_quirurgica->procedimiento}}');
			$("#proc_principal").val('{{$intervencion_quirurgica->procedimiento_principal}}');
			$("#cod_fonasa_p_1").val('{{$intervencion_quirurgica->codigo_procedimiento_principal}}');
			$("#proc_principal2").val('{{$intervencion_quirurgica->otro_procedimiento}}');
			$("#cod_fonasa_p_2").val('{{$intervencion_quirurgica->codigo_otro_procedimiento}}');
		@endif

		//fallecimiento

		@if($caso->motivo_termino == 'fallecimiento')
			$("#cond_egreso").val();
		@endif



		//funcionalidades
		$(".fechaIngreso").inputmask({
			alias: "datetime",
			inputFormat:"dd-mm-yyyy",
		});

		$("#fecha_egreso").inputmask({
			alias: "datetime",
			inputFormat:"dd-mm-yyyy",
		});

		var rn = 1;

		//en caso de elimianr, se debe quitar un valor a la variable rn y se pueda seguir coontando normalmente
		$(document).on("click", ".eliminar_rn", function(){
			rn--;
		});
		//cargar Recien nacidos
		@foreach($recien_nacidos as $key => $rn)
			html = "<tr id='"+rn+"'> <td>"+rn+"</td> <td><button type='button' id='subir'> <span class='glyphicon glyphicon-arrow-up'></span> </button> <button type='button' id='bajar'> <span  class='glyphicon glyphicon-arrow-down'></span> </button> </td>  <td> <select name='cond_nacer[]' class='form-control' id='cond"+rn+"'> <option value='vivo'>Vivo</option> <option value='fallecido'>Fallecido</option> </select> </td> <td style='width: 13%;'> <select name='sexo[]' class='form-control' id='sex"+rn+"'> <option value='masculino'>01. Hombre</option> <option value='femenino'>02. Mujer</option> <option value='indefinido'>03. Intersex (Indeterminado)</option> <option value='desconocido'>04. Desconocido</option> </select> </td> <td> <input id='peso"+rn+"' type='text' name='peso[]' class='form-control'> </td> <td> <input id='apgar"+rn+"' type='text' name='apgar[]' class='form-control'> </td> <td> <select name='anomalia[]' class='form-control' id='anomalia"+rn+"'> <option value='true'>SI</option> <option value='false'>NO</option> </select> </td> <td> <button class='btn btn-danger eliminar_rn' type='button' id='rn"+rn+"' data-id='"+rn+"'  onclick='eliminar(this)'><span class='glyphicon glyphicon-trash'></span> Eliminar</button> </td> </tr>";

			$( "#RecienNacidos" ).append(html);

			$("#cond"+rn).val('{{$rn->condicion}}');
			$("#sex"+rn).val('{{$rn->sexo}}');
			$("#peso"+rn).val('{{$rn->peso_gramos}}');
			$("#apgar"+rn).val('{{$rn->apgar}}');
			$("#anomalia"+rn).val('{{$rn->anomalia_congenita}}');

			rn++;

		@endforeach

		//añadir nuevos recien nacidos
		$("#addRN").click(function(){
			html = "<tr id='"+rn+"'> <td>"+rn+"</td> <td>	<button type='button' id='subir'> <span class='glyphicon glyphicon-arrow-up'></span> </button> <button type='button' id='bajar'> <span  class='glyphicon glyphicon-arrow-down'></span> </button> </td>  <td> <select name='cond_nacer[]' class='form-control'> <option value='vivo'>Vivo</option> <option value='fallecido'>Fallecido</option> </select> </td> <td style='width: 13%;'> <select name='sexo[]' class='form-control'> <option value='masculino'>01. Hombre</option> <option value='femenino'>02. Mujer</option> <option value='indefinido'>03. Intersex (Indeterminado)</option> <option value='desconocido'>04. Desconocido</option> </select> </td> <td> <input type='text' name='peso[]' class='form-control'> </td> <td> <input type='text' name='apgar[]' class='form-control'> </td> <td> <select name='anomalia[]' class='form-control'> <option value='true'>SI</option> <option value='false'>NO</option> </select> </td> <td> <button class='btn btn-danger eliminar_rn' type='button' id='rn"+rn+"' data-id='"+rn+"' onclick='eliminar(this)'><span class='glyphicon glyphicon-trash'></span> Eliminar</button> </td> </tr>";

			$( "#RecienNacidos" ).append(html);

			rn++;
		});




		//boton agregar diagnosticos
		var diagnostico = 0;
		var diagn = 5;
		$("#addDiagn").click(function(){
			html = "<div class='row'> <div class='col-md-3'> <button class='btn btn-danger eliminar_diagn' type='button' onclick='eliminarDiagn(this)'> <span class='glyphicon glyphicon-trash'></span> Eliminar </div> <div class='col-md-6 diagnostico'> <input type='text' name='diagnostico[]' class='form-control typeahead' id='diagn"+diagn+"' data-id ='"+diagn+"'> </div> <div class='col-md-3'> <input type='text' name='cod_diagn[]' id='cod_diagn"+diagn+"' class='form-control'> </div> </div> <br>";

			$( "#diagnsExtras" ).append(html);
			$('#diagnsExtras .typeahead').typeahead('destroy');

			var datos_cie10 = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				remote: {
					url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
					wildcard: '%QUERY',
					filter: function(response) {
						return response;
					}
				},
				limit: 50
			});

			datos_cie10.initialize();

			$('#diagnsExtras .typeahead').typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_cie10',
			source: datos_cie10.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					console.log(data);
					return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
			}
			}).on('typeahead:selected', function(event, selection){
				console.log("seection", selection);
				/* var id = $(this).attr("id");
				var num = "#codUnidad"+ $(this).data("id"); */

				//asigna el codigo unidad funcional
				/* $("#diagn"+$(this).data("id")).val("asd"); */
				$("#diagn"+$(this).data("id")).attr("disabled",true);
				$("#cod_diagn"+$(this).data("id")).attr("disabled",true);
				$("#cod_diagn"+$(this).data("id")).val(selection.id_cie10);
				/* $("#cod_diagn5").val("12312");  */
				/* console.log("hola", $(this)); */


			}).on('typeahead:close', function(ev, suggestion) {//Mauricio
				console.log('Close typeahead: ' + suggestion);
			});

			diagnostico += 1;
			diagn += 1;

		});



		///boton agregar traslados
		var traslado = 0;
		cod_traslados += 1;
		$( "#addTraslado" ).click(function() {
			//html = "<div class='row' style='margin-bottom:10px; margin-top:0;'> <div class='col-md-6'> <div class='col-md-offset-3 col-md-3' style='padding-top: 25px;'> <input type='text' class='form-control horaingreso' name='hora_min[]'> </div> 	<div class=' col-md-6' style='padding-top: 25px;'> <input type='text' name='fechaTraslado[]' class='form-control fechaIngreso ' id='ingreso"+traslado+"'> </div> </div> <div class='col-md-3 areaFuncional' style='padding-top: 25px;'>	<input type='text' name='areaFuncional[]' class='form-control typeahead' id='areaFuncional"+cod_traslados+"' data-id ='"+cod_traslados+"'> </div> <div class='col-md-3' style='padding-top: 25px;'> <div class='col-md-6'> <input type='text' name='codigoUnidad[]' class='form-control' id='codUnidad"+cod_traslados+"'> </div> <div class='col-md-6'> <input type='text' name='servicioClinico[]' class='form-control' id='codServicio"+cod_traslados+"'> </div> </div> </div>";

			html = "<div class='row' style='margin-bottom:10px; margin-top:0;'> <div class='col-md-6'> <div class='col-md-offset-3 col-md-3' style='padding-top: 25px;'> <input type='hidden' class='form-control horaingreso'  name='hora_min[]'> </div> 	<div class=' col-md-6' style='padding-top: 25px;'> <input type='text' name='fechaTraslado[]' class='form-control fechaIngreso ' id='ingreso"+traslado+"'> </div> </div> <div class='col-md-3 areaFuncional' style='padding-top: 25px;'>	<input type='text' name='areaFuncional[]' class='form-control typeahead' id='areaFuncional"+cod_traslados+"' data-id ='"+cod_traslados+"'> </div> <div class='col-md-3' style='padding-top: 25px;'> <div class='col-md-6'> <input type='text' name='codigoUnidad[]' class='form-control' id='codUnidad"+cod_traslados+"'> </div> <div class='col-md-6'> <input type='text' name='servicioClinico[]' class='form-control' id='codServicio"+cod_traslados+"'> </div> </div> </div>";


			$( "#trasladosExtras" ).append(html);
			$(".fechaIngreso").inputmask({
				alias: "datetime",
				//inputFormat:"dd-mm-yyyy",
				inputFormat:"dd-mm-yyyy",
			});


			$('.areaFuncional .typeahead').typeahead('destroy')

			//lista de areas
			var area = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('areaFuncional'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				remote: {
					url: '{{URL::to('/')}}/'+'%QUERY/consulta_areasFuncionales',
					wildcard: '%QUERY',
					filter: function(response) {
						return response;
					}
				},
				limit: 50
			});

			area.initialize();

			$('.areaFuncional .typeahead').typeahead(null, {
			name: 'best-pictures',
			display: 'nombre',
			source: area.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					console.log(data);
					return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre +"</b></span><span class='col-sm-4'><b>" + data.codigo +"</b></span></span><span class='col-sm-4'><b>" + data.u +"</b></span> <legend></legend> </div>"
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>UNIDAD FUNCIONAL</span><span class='col-sm-4' style='color:#1E9966;'>Código Unidad</span></div><br> <span class='col-md-offset-8 col-sm-4' style='color:#1E9966;'>Código Servicio</span>"
			}
			}).on('typeahead:selected', function(event, selection){
				console.log("seection", selection);
				/* var id = $(this).attr("id");
				var num = "#codUnidad"+ $(this).data("id"); */

				//asigna el codigo unidad funcional
				$("#codUnidad"+$(this).data("id")).val(selection.codigo);
				$("#codServicio"+$(this).data("id")).val(selection.u);


			}).on('typeahead:close', function(ev, suggestion) {//Mauricio
				console.log('Close typeahead: ' + suggestion);
			});

			traslado += 1;
			cod_traslados += 1;

		});

		//lista de areas
		var area = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('areaFuncional'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/consulta_areasFuncionales',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 50
		});

		area.initialize();

		$('.areaFuncional .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'nombre',
		source: area.ttAdapter(),
		limit: 50,
		templates: {
			empty: [
			'<div class="empty-message">',
				'No hay resultados',
			'</div>'
			].join('\n'),
			suggestion: function(data){
				//console.log(data.nombre_apellido);
				var nombres = data;
				console.log(data);
				return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre +"</b></span><span class='col-sm-4'><b>" + data.codigo +"</b></span></span><span class='col-sm-4'><b>" + data.u +"</b></span> <legend></legend> </div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>UNIDAD FUNCIONAL</span><span class='col-sm-4' style='color:#1E9966;'>Código Unidad</span></div><br> <span class='col-md-offset-8 col-sm-4' style='color:#1E9966;'>Código Servicio</span>"
		}
		}).on('typeahead:selected', function(event, selection){
			console.log("seection", selection);
			/* var id = $(this).attr("id");
			var num = "#codUnidad"+ $(this).data("id"); */

			//asigna el codigo unidad funcional
			$("#codUnidad"+$(this).data("id")).val(selection.codigo);
			$("#codServicio"+$(this).data("id")).val(selection.u);


		}).on('typeahead:close', function(ev, suggestion) {//Mauricio
			console.log('Close typeahead: ' + suggestion);
		});


		//MEDICOS
		var datos_medicos = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicos'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/consulta_medicos_nombre',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 50
		});

		datos_medicos.initialize();

		$('.medicos .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'nombre_medico',
		source: datos_medicos.ttAdapter(),
		limit: 50,
		templates: {
			empty: [
			'<div class="empty-message">',
				'No hay resultados',
			'</div>'
			].join('\n'),
			suggestion: function(data){
				//console.log(data.nombre_apellido);
				var nombres = data;
				//console.log(data);
				return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
		}
		}).on('typeahead:selected', function(event, selection){
			$("#especialidad").val(selection.especialidad);
			$("#apellidoP_medico").val(selection.primer_apellido);
			$("#apellidoM_medico").val(selection.segundo_apellido);
			$("#id_medico").val(selection.id_medico);
			$("#rut_medico").val(selection.rut_medico);
			$("#dv_medico").val(selection.dv_medico);

			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'dv_medico');
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'rut_medico');
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'apellidoP_medico');

		}).on('typeahead:close', function(ev, suggestion) {//Mauricio
			console.log('Close typeahead: ' + suggestion);
			/* $("#especialidad").val("");
			$("#apellidoP_medico").val("");
			$("#apellidoM_medico").val("");
			$("#id_medico").val("");
			$("#rut_medico").val("");
			$("#dv_medico").val(""); */
		});


		$( "#pueblo_ind" ).change(function() {
			if ($( "#pueblo_ind" ).val() == 'Otro') {
				$(".cla_ind").removeAttr("hidden");
			}else{
				$(".cla_ind").attr("hidden",true);
			}
			console.log("pueblo;: ", $( "#pueblo_ind" ).val());
		});

		$( "#tipo_documento" ).change(function() {
			console.log("algoooooss");
			console.log($( "#tipo_documento" ).val());
			if ($( "#tipo_documento" ).val() == 1) {
				console.log("algoooooss");
				console.log($( "#tipo_documento" ).val());
				$("#campo_run").removeAttr("hidden");
				$("#campo_pasaporte").attr("hidden",true);
			}else if($( "#tipo_documento" ).val() == 2 || $( "#tipo_documento" ).val() == 4){
				console.log($( "#tipo_documento" ).val());
				$("#campo_pasaporte").removeAttr("hidden");
				$("#campo_run").attr("hidden",true);
			}else{
				console.log($( "#tipo_documento" ).val());
				console.log('cuando no hay tipo de documento');
				$("#campo_run").attr("hidden",true);
				$("#campo_pasaporte").attr("hidden",true);
			}
		});



		$( "#cat_ocup" ).change(function() {
			if ($("#cat_ocup" ).val() == "activos") {
				$(".cat_ocup_activo").removeAttr("hidden");
			}else{
				$(".cat_ocup_activo").attr("hidden",true);
				$("#cat_activo").val("");
			}
		});

		$( "#ley_previsional_opc" ).change(function() {
			if ($("#ley_previsional_opc" ).val() == "true") {
				$("#ley_previsional_parrafo").removeClass("ley");
				$("#ley_previsional").removeClass("ley");
			}else{
				$("#ley_previsional_parrafo").addClass("ley");
				$("#ley_previsional").addClass("ley");
			}
		});

		$( "#procedencia" ).change(function() {
			if ($("#procedencia" ).val() == 4 || $("#procedencia" ).val() == 7) {
				$("#hosp_procedencia").removeAttr("disabled");
			}else{
				$("#hosp_procedencia").attr("disabled",true);
			}
		});

		//validacion rut medico
		$( "#rut_medico" ).change(function() {
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'dv_medico');
		});
		$( "#dv_medico" ).change(function() {
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'dv_medico');
		});


		//datos de hospitalizacion (INGRESO)
		$( "#hr" ).change(function() {
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'hr');
		});

		//rebalidar campo especificar de indigena
		$( "#pueblo_ind" ).change(function() {
			$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'esp_pueblo');
		});


		$("#fecha_nac_u").inputmask({
			alias: "datetime",
			inputFormat:"dd-mm-yyyy",
			//"onincomplete": function(){ alert('inputmask incomplete')}
			"oncomplete": function(){
				console.log("calcular edad",$("#fecha_nac_u").val());
				$.ajax({
					url: "../edad",
					type: "get",
					dataType: "json",
					data: {edad : $("#fecha_nac_u").val()},
					success: function(data){
						console.log("algo", data);
						if (data.exito == true) {
							$("#edad_paciente").val(data.edad);
							$("#u_medida_edad").val(data.unidad_medida);
						}else{
							$("#edad_paciente").val("");
							$("#u_medida_edad").val("");
							$("#fecha_nac_u").val("");
						}
					},
					error: function(error){
						console.log(error);
					}
				});
			}
		});


		if($("#rut").val() != "") $("#rut").prop("readonly", true);
		if($("#dv").val() != "") $("#dv").prop("readonly", true);

		if($("#rut").val() == "") $("#sinRut").val(1);



		$("#formFichaEgresoPaciente").bootstrapValidator({
			excluded: ':disabled',
			fields: {

				rut_medico:{
					validators: {
						/* notEmpty: {
							message: 'El rut del medico no debe estar vacio'
						}, */
						callback: {
							callback: function(value, validator, $field){
								console.log("val:", value);
								return true;
							}
						}
					}
				},
				dv_medico: {
					validators:{
						/* notEmpty: {
							message: 'El digito verificador del medico no debe estar vacio'
						}, */
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut_medico");
								var dv = $("#dv_medico");
								if(field_rut.val() == '' && dv.val() == '') {
									return true;
								}
								if(field_rut.val() != '' && dv.val() == ''){
									$("#especialidad").val("");
									$("#apellidoP_medico").val("");
									$("#apellidoM_medico").val("");
									$("#nombres_medico").val("");
									$("#id_medico").val("");
									return {valid: false, message: "Debe ingresar el dígito verificador"};
								}
								if(field_rut.val() == '' && dv.val() != ''){
									$("#especialidad").val("");
									$("#apellidoP_medico").val("");
									$("#apellidoM_medico").val("");
									$("#nombres_medico").val("");
									$("#id_medico").val("");
									return {valid: false, message: "Debe ingresar el run"};
								}
								var rut = $.trim(field_rut.val());
								var esValido=esRutValido(field_rut.val(), dv.val());

								if(!esValido){
									$("#especialidad").val("");
									$("#apellidoP_medico").val("");
									$("#apellidoM_medico").val("");
									$("#nombres_medico").val("");
									$("#id_medico").val("");
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}

								$.ajax({
									url: "../../"+$("#rut_medico").val()+"/consulta_medicos_rut",
									type: "get",
									async: false,
									success: function(data){
										console.log("data",data);
										$("#especialidad").val(data.especialidad);
										$("#apellidoP_medico").val(data.primer_apellido);
										$("#apellidoM_medico").val(data.segundo_apellido);
										$("#nombres_medico").val(data.nombre_medico);
										$("#id_medico").val(data.id_medico);
										$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'apellidoP_medico');
										$('#formFichaEgresoPaciente').bootstrapValidator('revalidateField', 'nombres_medico');
									},
									error: function(error){
										console.log(error);
										$("#especialidad").val("");
										$("#apellidoP_medico").val("");
										$("#apellidoM_medico").val("");
										$("#nombres_medico").val("");
										$("#id_medico").val("");
										return {valid: false, message: "Medico no coincide con base de datos"};
									}
								});
								return true;
							}
						},

					}
				},
				rut: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								return true;
							}
						}
					}
				},
				dv: {
					validators:{
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut");
								var dv = $("#dv");
								if(field_rut.val() == '' && dv.val() == '') {
									return true;
								}
								if(field_rut.val() != '' && dv.val() == ''){
									return {valid: false, message: "Debe ingresar el dígito verificador"};
								}
								if(field_rut.val() == '' && dv.val() != ''){
									return {valid: false, message: "Debe ingresar el run"};
								}
								var rut = $.trim(field_rut.val());
								var esValido=esRutValido(field_rut.val(), dv.val());
								if(!esValido){
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}
								return true;
							}
						},

					}
				},
				tel_movil: {
					validators:{
						integer: {
							message: "Debe ingresar solo números"
						}
					},
				},
				cod_establecimiento: {
					validators:{
						notEmpty: {
							message: 'El código es obligatorio'
						}
					}
				},
				n_admision: {
					validators:{
						notEmpty: {
							message: 'El número de admisión es obligatorio'
						}
					}
				},
				n_clinico: {
					validators:{
						notEmpty: {
							message: 'El número de historia clínica es obligatorio'
						}
					}
				},
				num_egreso: {
					validators:{
						notEmpty: {
							message: 'El número de egreso es obligatorio'
						}
					}
				},
				nombres: {
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				fecha_nac_u: {
					validators:{
						notEmpty: {
							message: 'El fecha de nacimiento es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFecha(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				},
				edad_paciente: {
					validators:{
						notEmpty: {
							message: 'La edad del paciente es obligatoria'
						}
					}
				},
				esp_pueblo: {
					validators:{
						callback: {
							callback: function(value, validator, $field){
								if ($("#pueblo_ind").val() != 'Otro') {
									return true;
								}else if($("#pueblo_ind").val() == 'Otro'){
									if ($("#esp_pueblo").val() == "") {
										return {valid: false, message: "Ingrese el pueblo origen"};
									}else{
										return true;
									}
								}
							}
						}
					}
				},
				nombre_pais: {
					validators:{
						notEmpty: {
							message: 'El nombre del país es obligatorio'
						}
					}
				},
				hosp_procedencia: {
					validators:{
						notEmpty: {
							message: "Debe indicar el hospital procedencia"
						}
					}
				},
				hr: {
					validators:{
						integer: {
							message: "Debe ingresar solo números"
						},
						callback: {
							callback: function(value, validator, $field){

								if (value >= 0 && value < 24) {
									return true;
								}else{
									return {valid: false, message: "Debe ingresar valores entre 0 y 23"};
								}
							}
						}
					},
				},
				min: {
					validators:{
						integer: {
							message: "Debe ingresar solo números"
						},
						callback: {
							callback: function(value, validator, $field){

								if (value >= 0 && value < 60) {
									return true;
								}else{
									return {valid: false, message: "Debe ingresar valores entre 0 y 23"};
								}
							}
						}
					},
				},
				fecha_ingreso: {
					validators:{
						notEmpty: {
							message: 'La fecha de ingreso es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFecha(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				},
				area_funcional: {
					validators:{
						notEmpty: {
							message: 'Debe ingresar área funcional'
						}
					}
				},
				// cod_funcional: {
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'Debe ingresar código del área funcional'
				// 		}
				// 	}
				// },
				// cod_servicio: {
				// 	validators:{
				// 		notEmpty: {
				// 			message: 'Debe ingresar código del servicio'
				// 		}
				// 	}
				// },
				fecha_egreso: {
					validators:{
						notEmpty: {
							message: 'La fecha de egreso es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFecha(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				},
				estada: {
					validators:{
						notEmpty: {
							message: "Debe ingresar días de estada del paciente"
						}
					}
				},
				diagn_principal: {
					validators:{
						notEmpty: {
							message: "El diagnóstico principal es obligatorio"
						}
					}
				},
				cie_10: {
					validators:{
						notEmpty: {
							message: "Ingrese el CIE-10 de la enfermedad"
						}
					}
				},
				apellidoP_medico: {
					/* validators:{
						notEmpty: {
							message: "Ingrese el apellido del médico"
						}
					} */
				},
				nombres_medico: {
					/* validators:{
						notEmpty: {
							message: "Ingrese el nombre del médico"
						}
					} */
				},
				'peso[]': {
					validators:{
						/* notEmpty: {
							message: "Ingrese el nombre del médico"
						}, */
						integer: {
							message: "Debe ingresar solo números"
						},
					},
				}
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			var fv = $form.data('bootstrapValidator');
			console.log("pdf", pdf);

			if (pdf == true) {
				//generar pdf
				$.ajax({
					url: "../fichaEgresoPDF?id_caso="+$("#id_caso").val(),
					type: "get",
					//dataType: "json",
					data: $form .serialize(),
					async: false,
					success: function(data){
						console.log("data",data);
					},
					error: function(error){
						console.log(error);
					}
				});
			}else{
				//guardar
				$.ajax({
					url: $form .prop("action"),
					type: "post",
					dataType: "json",
					data: $form .serialize(),
					async: false,
					success: function(data){
						enviado=true;
						if(data.exito)
							swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									location . reload();
								}, 2000)
							},
							});
						if(data.error)
							swalError.fire({
							title: 'Error',
							text:data.error
							});
					},
					error: function(error){
						console.log(error);
					}
				});
			}




		 });


	});

</script>


@stop

 @section("miga")
 <li><a href="#">Informe Estadístico de Egreso Hospitalario</a></li>
 @stop


@section("section")

<style>
	.ley{
		visibility: hidden;
	}

    .subtitulos {
        font-size: 10px;
    }

    .letra_chica {
        font-size: 8px;
    }

	.cuadrado {
		width:2.5vh;
		max-width:100px;
		height:2.5vh;
		max-height:100px;
		position:relative;
		background:green;
	}

	.cuadrado2 {
		width:3vh;
		max-width:100px;
		height:2.5vh;
		max-height:100px;
		position:relative;
		background:green;
	}

	.numero {
		color: white;
		font-size: 12px;
		text-align: center;
		margin: 5px;
	}

	/* typeahead */
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
</style>


@if($url != "error")
	<form style='display: hidden' action='../../unidad/{{$url}}' method='GET' id='form'>
		<input hidden type='text' name='paciente' value='{{$paciente->id}}'>
		<input hidden type='text' name='id_sala' value='{{$sala}}'>
		<input hidden type='text' name='id_cama' value='{{$cama}}'>
		<input hidden type='text' name='caso' value='{{$id_caso}}'>
		<button class='btn btn-primary' type='submit' style="text-align : right">Ir a unidad</button>
	</form>
@endif

<br>
<fieldset>
	{{ Form::model($paciente,  ["url" => "paciente/fichaEgresoPaciente", "id" => "formFichaEgresoPaciente", "class" => "form-horizontal", "role" => "form", 'autocomplete' => 'off']) }}

    <div class="row">
		{{ Form::hidden('id_caso', "$id_caso", array('id' => 'id_caso')) }}
		{{ Form::hidden('id_paciente', "$paciente->id", array('id' => 'id_paciente')) }}
		{{ Form::hidden('id_medico', null, array('id' => 'id_medico')) }}
        <div class="col-md-3">
            <p class="subtitulos">
                <b>
                    MINISTERIO DE SALUD <br>
                    DEPARTAMENTO DE ESTADÍSTICAS E <br>
                    INFORMACIÓN DE SALUD
                </b>
            </p>
        </div>

        <div class="col-md-5">

            <h4 align="center"><u>Informe Estadístico de Egreso Hospitalario</u></h4>
		</div>

        <div class="col-md-4">
                <div class="col-md-6" align="right">
                    <label class="subtitulos">N° EGRESO</label>
                </div>
                <div class="col-md-6">
                    {{Form::text('num_egreso', null, array('id' => 'num_egreso', 'class' => 'form-control '))}}
                </div>
                <div class="col" align="right">
                    <p class="letra_chica">
                        <b>
                            USO EXCLUSIVO UNIDAD DE ESTADÍSTICA
                        </b>
                    </p>
                </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-4">
			{{Form::text('nom_establecimiento', $establecimiento->nombre, array('id' => 'nom_establecimiento', 'class' => 'form-control ', 'disabled'))}}
            <p class="subtitulos" align="left"> <label class="cuadrado"><b class="numero">1</b> </label> <b>NOMBRE ESTABLECIMIENTO</b></p>

        </div>

        <div class="col-md-8">

            <div class="col-md-4">
                {{Form::text('cod_establecimiento', $establecimiento->codigo, array('id' => 'cod_establecimiento', 'class' => 'form-control ', 'disabled'))}}
                <p class="subtitulos" align="center">CÓDIGO ESTABLECIMIENTO</p>
            </div>

            <div class="col-md-4">
                {{Form::text('n_admision', null, array('id' => 'n_admision', 'class' => 'form-control '))}}
                <p class="subtitulos" align="center"><label class="cuadrado"><b class="numero">2</b> </label> N° ADMISIÓN</p>
			</div>

			<div class="col-md-4">
                {{Form::text('n_clinico', null, array('id' => 'n_clinico', 'class' => 'form-control ', 'disabled'))}}
                <p class="subtitulos" align="center"><label class="cuadrado"><b class="numero">3</b> </label> N° HISTORIA CLINICA</p>
			</div>


		</div>

    </div>

	<br>

	<div class="panel panel-default" >
		<div class="panel-heading panel-info">
				<h5>DATOS DE IDENTIFICACIÓN DEL (DE LA) PACIENTE:</h5>
		</div>

		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<p class="subtitulos">
						<label class="cuadrado"><b class="numero">4</b> </label> NOMBRE PACIENTE
					</p>
				</div>

			</div>

			<div class="row">
				<div class="col-md-3">
					{{Form::text('apellido_p', null, array('id' => 'apellido_p', 'class' => 'form-control ' , 'disabled'))}}
					<p class="subtitulos" align="center">PRIMER APELLIDO</p>
				</div>

				<div class="col-md-3">
					{{Form::text('apellid_m', null, array('id' => 'apellid_m', 'class' => 'form-control ', 'disabled' , 'disabled'))}}
					<p class="subtitulos" align="center">SEGUNDO APELLIDO</p>
				</div>

				<div class="col-md-4" align="right">
					{{Form::text('nombres', null, array('id' => 'nombres', 'class' => 'form-control ', 'disabled' , 'disabled'))}}
					<p class="subtitulos" align="center">NOMBRES</p>
				</div>
			</div>

		</div>

		<br>
		<div class="row">
			<div class="col-md-5">
				<div class="col-md-4">
					<p class="subtitulos" align="left"><label class="cuadrado"><b class="numero">5</b> </label> TIPO DE IDENTIFICACIÓN</p>
				</div>

				<div class="col-md-8" style="padding-right: 8px; padding-left: 8px;">
					{{Form::select('tipo_documento', array('1' => '1. RUN', '2' => '2. Pasaporte', '3' => '3. Indocumentado', '4' => '4. Otro documento de identificación'), null, array('id' => 'tipo_documento', 'class' => 'form-control ', 'disabled'))}}
				</div>

			</div>

			<div class="col-md-3">
				<div class="col-md-4" style="padding-left: 0px;">
					<p class="subtitulos" align="left"><label class="cuadrado"><b class="numero">6</b> </label> SEXO</p>
				</div>

				<div class="col-md-8" style="padding-right: 5px; padding-left: 5px;">
					{{Form::select('genero', ["1" => "1. Hombre", "2" => "2. Mujer", "3" => "3. INTERSEX (INDETERMINADO)", "4" => "4. DESCONOCIDO"],null , array('id' => 'genero', 'class' => 'form-control ', 'disabled'))}}
				</div>
			</div>

			<div class="col-md-4" style="padding-right: 0px;">

				<div class=" col-md-4 offset-md-2">
					<p class="subtitulos" align="left"><label class="cuadrado"><b class="numero">7</b> </label> FECHA DE NACIMIENTO</p>
				</div>

				<div class="col-md-8">
					<div class="col-md-12">
						{{Form::text('fecha_nac_u', null, array('id' => 'fecha_nac_u', 'class' => 'form-control', 'disabled'))}}
					</div>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="col-md-12" id="campo_run" hidden>
					<p class="subtitulos" align="left">1.RUN:</p>
					<div class="input-group">

						{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'style' => 'z-index: 1;', 'disabled'))}}
						<span class="input-group-addon"> - </span>

						{{Form::text('dv',null , array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;', 'disabled'))}}
					</div>

					<br>


				</div>
				<br>
				<div class="col-md-12" id="campo_pasaporte" hidden>
					<p class="subtitulos" align="left">2.N° de Pasaporte u otro documento</p>
					{{Form::text('pasaporte', null, array('id' => 'pasaporte', 'class' => 'form-control', 'disabled'))}}
					<br>
				</div>



			</div>

		</div>
<br>
		<div class="row">
			<div class="col-md-3">
				<div class="col-md-6">
					<p class="subtitulos" align="left"><label class="cuadrado"><b class="numero">8</b> </label> EDAD</p>
				</div>

				<div class="col-md-6">
					{{Form::text('edad_paciente',null , array('id' => 'edad_paciente', 'class' => 'form-control', 'disabled'))}}
				</div>
			</div>

			<div class="col-md-3">
				<div class="col-md-6">
					<p class="subtitulos" align="left"><label class="cuadrado"><b class="numero">9</b> </label> UNIDAD MEDIDA DE LA EDAD</p>
				</div>

				<div class="col-md-6">
					{{Form::select('u_medida_edad',["0" => "", "1" =>"1. Años", "2"=> "2. Meses", "3" => "3. Días", "4" => "4. Horas"],null , array('id' => 'u_medida_edad', 'class' => 'form-control' , 'disabled'))}}
				</div>
			</div>

			<div class="col-md-3 green">
				<div class="col-md-6 red">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">10</b> </label> PUEBLO INDIGENAS</p>
				</div>

				<div class="col-md-6">
					{{Form::select('pueblo_ind',["Mapuche" =>"1. Mapuche", "Aymara"=> "2. Aymara", "Rapa nui" => "3. Rapa Nui (Pascuense)", "Lican Antai" => "4. Lican Antai (Atacameño)", "Quechua" => "5. Quechua", "Colla" => "6. Colla","Diaguita" => "7. Diaguita" , "Kawéscar" => "8. Kawésqar", "Yagán" => "9. Yagán (Yámana)", "Ninguno" => "96. Ninguno", "Otro" => "99. Otro"], null, array('id' => 'pueblo_ind', 'class' => 'form-control'))}}
				</div>

				<div class="col cla_ind" hidden>
					{{Form::text('esp_pueblo', null, array('id' => 'esp_pueblo', 'class' => 'form-control'))}}
					<p class="subtitulos" align="center"> ESPECIFICAR</p>
				</div>
			</div>

			<div class="col-md-3">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">11</b> </label> PAÍS DE ORIGEN DEL (DE LA ) PACIENTE</p>
					{{Form::select('nombre_pais',$paises, 'CL', array('id' => 'nombre_pais', 'class' => 'form-control'))}}
					<p class="subtitulos" align="center">Nombre País</p>
				</div>

			</div>
		</div>

		<legend></legend>

		<div class="row">
			<div class="col-md-5">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">12</b> </label> CATEGORIA OCUPACIONAL </p>
					{{Form::select('cat_ocup',["inactivos" =>"00. INACTIVOS", "activos"=> "01. ACTIVOS", "cesante o desocupados" => "02. CESANTE O DESOCUPADOS", "desconocido" => "99. DESCONOCIDO"], null, array('id' => 'cat_ocup', 'class' => 'form-control'))}}
					<br>
					<p class="subtitulos cat_ocup_activo" align="left" hidden> En caso de marcar la alternativa "Activos" en el casillero correspondiente identifique la opción declarada </p>
					<div class="cat_ocup_activo" hidden>
						{{Form::select('cat_ocup_activo',["01" =>"01. Miembro del poder ejecutivo de los cuerpos legislativos, personal directivo de la administración pública y de empresa", "02"=> "02. Profesionales científicos o intelectuales", "03" => "03. Tecnicos y profesionales de nivel medio", "04" => "04. Empleados de oficina", "05" => "05. Trabajadores de los servicios y vendedores de comercio y mercado", "06" => "06. Agricultores y trabajadores calificados agropecuarios y pesqueros", "07" => "07. Oficiales, operarios y artesanos de artes mecánicas de otros oficios", "08" => "08. Operadores de instalaciones y máquinas y montadoras", "09" => "09. Trabajadores no calificados", "10" => "10. Fuerzas armadas", "99" => "99. Desconocido"], null, array('id' => 'cat_activo', 'class' => 'form-control'))}}
					</div>

				</div>


			</div>

			<div class="col-md-3">
				<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">13</b> </label> NIVEL DE INSTRUCCIÓN </p>

				{{Form::select('educacion',["prebasica" =>"01. Prebásicaa", "basica"=> "02. Básica", "media" => "03. Media", "tecnico nivel superior" => "04. Técnico de nivel Superior", "universitario" => "05. Profesional universitario", "sin instrucción" => "06. Sin instrucción", "no recuerda" => "97. No recuerda", "no responde" => "98. No responde"], null, array('id' => 'educacion', 'class' => 'form-control'))}}
			</div>

			<div class="col-md-4">
				<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">14</b> </label> TELÉFONO FIJO </p>
				{{Form::text('tel_fijo', null, array('id' => 'tel_fijo', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-5">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">15</b> </label> TELÉFONO MÓVIL </p>
					{{Form::text('tel_movil', null, array('id' => 'tel_movil', 'class' => 'form-control', 'style'=>'width: 200px;'))}}
					{{ Form::hidden('id_movil', null, array('id' => 'id_movil')) }}
					<p id='tmovil'></p>
				</div>
			</div>
		</div>
		<br>

		<legend></legend>

		<div class="row">
			<div class=" col-md-12">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">16</b> </label> DOMICILIO</p>
				</div>

			</div>

			<div class=" col-md-12">
				<div class="col-md-12">
					<div class="col-md-2" style="padding-left: 0;">
						{{Form::select('tipo_calle',["1" =>"01. Calle", "2"=> "02. Avenida", "3" => "03. Pasaje", "4" => "04. Camino", "9" => "09. Otro"], null, array('id' => 'tipo_calle', 'class' => 'form-control', 'disabled'))}}
					</div>
					<div class="col-md-8">
						{{Form::text('calle', null, array('id' => 'calle', 'class' => 'form-control', 'disabled'))}}
						<p class="subtitulos" align="center">Nombre</p>
					</div>
					<div class="col-md-2">
						{{Form::text('calle_num', null, array('id' => 'calle_num', 'class' => 'form-control', 'disabled'))}}
						<p class="subtitulos" align="center">Número</p>
					</div>
				</div>

			</div>
		</div>
		<br>


		<div class="row">
			<div class=" col-md-12">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">17</b> </label> COMUNA RESIDENCIA</p>
				</div>

			</div>

			<div class="col-md-4">
				<div class="col-md-12">
					{{Form::select('comuna',$comunas, null, array('id' => 'comuna', 'class' => 'form-control' , 'disabled'))}}
				</div>

			</div>
		</div>
		<br>
		<legend></legend>

		<div class="row">
			<div class="col-md-3">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">18</b> </label> PREVISIÓN</p>

					{{ Form::select('prevision', ["0" => "", "1" => "01. FONASA", "2" => "02. ISAPRE", "3" => "03. CAPREDENA", "4" => "04. DIPRECA", "5" => "05. SISA", "96" => "96. NINGUNA", "99" => "99. DESCONOCIDO"],null , array('id' => 'prevision', 'class' => 'form-control', 'disabled')) }}
				</div>



			</div>

			<div class="col-md-3">
				<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">19</b> </label> Clasificación Beneficiario FONASA</p>
				{{ Form::select('tramo_fonasa', ["0" => "", "1" => "Tramo A", "2" => "Tramo B", "3" => "Tramo C", "4" => "Tramo D"], null, array('id' => 'tramo_fonasa', 'class' => 'form-control', 'disabled')) }}

			</div>
			<div class="col-md-3">
				<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">20</b> </label> Modalidad de atención FONASA</p>
				{{ Form::select('mod_aten', ["sin información" => "", "mai" => "01. Modalidad de atención institucional (MAI)", "mle" => "02. Modalidad de atención libre elección (MLE)"], null, array('id' => 'mod_aten', 'class' => 'form-control')) }}
			</div>
			<div class="col-md-3">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">21</b> </label> LEYES PREVISIONALES</p>
					{{ Form::select('ley_previsional_opc', ["true" => "01.SI", "false" => "02. NO"], 0, array('id' => 'ley_previsional_opc', 'class' => 'form-control')) }}

					<br>
					<p id="ley_previsional_parrafo" class="subtitulos ley" align="left"> En caso que la variable leyes provisionales sea "Si", seleccione la alternativa correspondiente. </p>
					{{ Form::select('ley_previsional', ["accidente transporte" => "01. Ley 18.490: accidente de transporte", "accidente trabajo" => "02. Ley 18.744: accidentes del trabajo y enfermedades profesionales", "accidente escolar" => "03. Ley 16.744: accidente escolar", "urgencias" => "04. Ley 19.650/99 de urgencia", "prais" => "05. Ley 19.992 PRAIS"], NULL, array('id' => 'ley_previsional', 'class' => 'form-control ley')) }}
				</div>

			</div>
		</div>
		<br>

		<legend></legend>
		<div class="row">
			<div class="col-md-6">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">22</b> </label> PROCEDENCIA DEL (DE LA) PACIENTE</p>

					{{ Form::select('procedencia', ["0" => "","1" => "1. Unidad Emergencia (mismo establecimiento)", "3" => "3. Atención especialidades (mismo establecimiento)", "4" => "4. Otro establecimiento", "5" => "5. Otra procedencia", "6" => "6. Área de Cirugía Mayor Ambulatoria (mismo establecimiento)", "7" => "7. Hospital comunitario o de baja complejidad"], $procedencia_22, array('id' => 'procedencia', 'class' => 'form-control', 'disabled')) }}
				</div>

			</div>

			<div class="col-md-6">
				<div class="col-md-12">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">23</b> </label> ESTABLECIMIENTO DE PROCEDENCIA</p>

					{{Form::text('hosp_procedencia', null, array('id' => 'hosp_procedencia', 'class' => 'form-control', 'disabled'))}}
					<p class="subtitulos" align="center"> (Solo llenar si se registró opción 4 o 7) </p>
				</div>


			</div>
		</div>

	</div>




	<br>

	<div class="panel panel-default" >
		<div class="panel-heading panel-info">
			<h5>DATOS DE LA HOSPITALIZACIÓN:</h5>
		</div>
		<br>
		<div class="panel-body" id="sectorTraslados">
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-2" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">24</b> </label> INGRESO</p>
					</div>
					<div class="col-md-3">
						<p class="subtitulos" align="center">Hora / Minutos</p>
						<div class="input-group">
							{{Form::text('hr', null, array('id' => 'hr', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;'))}}
							<span class="input-group-addon"> : </span>
							{{Form::text('min',null , array('id' => 'min', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;'))}}
						</div>
					</div>
					<div class="col-md-offset-1 col-md-6">
						<p class="subtitulos" align="center">FECHA (dd-mm-aaaa)</p>
						{{Form::text('fecha_ingreso', null, array('id' => 'fecha_ingreso', 'class' => 'form-control fechaIngreso', 'style'=>'text-align: right;'))}}
					</div>
				</div>

				<div class="col-md-3 areaFuncional">
					<p class="subtitulos" align="center">UNIDAD FUNCIONAL</p>
					{{Form::text('area_funcional', null, array('id' => 'area_funcional', 'class' => 'form-control typeahead', 'data-id' => "0"))}}
				</div>

				<div class="col-md-3">
					<div class="col-md-6">
						<p class="letra_chica" align="center" style="margin-bottom: 2px;">CÓDIGO UNIDAD FUNCIONAL</p>
						{{Form::text('cod_funcional', null, array('id' => 'codUnidad0', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						<p class="letra_chica" align="center" style="margin-bottom: 1px;"> CÓDIGO SERVICIO CLÍNICO </p>
						{{Form::text('cod_servicio', null, array('id' => 'codServicio0', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class=" col-md-2" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">25</b> </label> 1er TRASLADO</p>
					</div>
					<div class="col-md-offset-4 col-md-6" style="padding-top: 25px;">
						{{Form::text('fecha_ingreso1', null, array('id' => 'fecha_ingreso1', 'class' => 'form-control fechaIngreso', 'style'=>'text-align: right;', 'disabled'))}}
					</div>
				</div>

				<div class="col-md-3 areaFuncional" style="padding-top: 25px;">
					{{Form::text('area_funcional1', null, array('id' => 'areaFuncional1', 'class' => 'form-control typeahead' , 'data-id' => "1" , 'disabled'))}}
				</div>

				<div class="col-md-3" style="padding-top: 25px;">
					<div class="col-md-6">
						{{-- {{Form::text('cod_funcional1', null, array('id' => 'codUnidad1', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_funcional1', null, array('id' => 'codUnidad1', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						{{-- {{Form::text('cod_servicio1', null, array('id' => 'codServicio1', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_servicio1', null, array('id' => 'codServicio1', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class=" col-md-2" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">26</b> </label> 2do TRASLADO</p>
					</div>
					<div class="col-md-offset-4 col-md-6" style="padding-top: 25px;">
						{{Form::text('fecha_ingreso2', null, array('id' => 'fecha_ingreso2', 'class' => 'form-control fechaIngreso ', 'style'=>'text-align: right;', 'disabled'))}}
					</div>
				</div>

				<div class="col-md-3 areaFuncional" style="padding-top: 25px;">
					{{Form::text('area_funcional2', null, array('id' => 'areaFuncional2', 'class' => 'form-control typeahead' , 'data-id' => "2", 'disabled'))}}
				</div>

				<div class="col-md-3" style="padding-top: 25px;">
					<div class="col-md-6">
						{{-- {{Form::text('cod_funcional2', null, array('id' => 'codUnidad2', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_funcional2', null, array('id' => 'codUnidad2', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						{{-- {{Form::text('cod_servicio2', null, array('id' => 'codServicio2', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_servicio2', null, array('id' => 'codServicio2', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class=" col-md-2" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">27</b> </label> 3er TRASLADO</p>
					</div>
					<div class="col-md-offset-4 col-md-6" style="padding-top: 25px;">
						{{Form::text('fecha_ingreso3', null, array('id' => 'fecha_ingreso3', 'class' => 'form-control fechaIngreso', 'style'=>'text-align: right;', 'disabled'))}}
					</div>
				</div>

				<div class="col-md-3 areaFuncional" style="padding-top: 25px;">
					{{Form::text('area_funcional3', null, array('id' => 'areaFuncional3', 'class' => 'form-control typeahead', 'data-id' => "3", 'disabled'))}}
				</div>

				<div class="col-md-3" style="padding-top: 25px;">
					<div class="col-md-6">
						{{-- {{Form::text('cod_funcional3', null, array('id' => 'codUnidad3', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_funcional3', null, array('id' => 'codUnidad3', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						{{-- {{Form::text('cod_servicio3', null, array('id' => 'codServicio3', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_servicio3', null, array('id' => 'codServicio3', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class=" col-md-2" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">28</b> </label> 4to TRASLADO <b>(*)</b></p>
					</div>
					<div class="col-md-offset-4 col-md-6" style="padding-top: 25px;">
						{{Form::text('fecha_ingreso4', null, array('id' => 'fecha_ingreso4', 'class' => 'form-control fechaIngreso', 'style'=>'text-align: right;', 'disabled'))}}
					</div>
				</div>

				<div class="col-md-3 areaFuncional" style="padding-top: 25px;">
					{{Form::text('area_funcional4', null, array('id' => 'areaFuncional4', 'class' => 'form-control typeahead', 'data-id' => "4", 'disabled'))}}
				</div>

				<div class="col-md-3" style="padding-top: 25px;">
					<div class="col-md-6">
						{{-- {{Form::text('cod_funcional4', null, array('id' => 'codUnidad4', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_funcional4', null, array('id' => 'codUnidad4', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						{{-- {{Form::text('cod_servicio4', null, array('id' => 'codServicio4', 'class' => 'form-control', 'disabled'))}} --}}
						{{Form::text('cod_servicio4', null, array('id' => 'codServicio4', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>

		 	<div id="trasladosExtras">
				<div class="subtitulos" align="center"><b>Traslados extras</b> </div>

			</div>
{{--
			<div class="row" align="right" style="margin-right: 15px; margin-bottom:10px;">
				<div class="btn btn-primary" id="addTraslado" >+ Traslados</div>
			</div>
--}}
			<div class="row">
				<div class="col-md-9">
					<p class="subtitulos" align="left" ><b>(*) : Ver instructivo</b></p>
				</div>

				<div class="col-md-3">
					<p class="subtitulos" align="center" ><b>RESPONSABLE: ESTADÍSTICA</b></p>
				</div>

			</div>



			<legend></legend>
			<br>

			<div class="row">

					<div class="col-md-2" style="padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">29</b> </label> EGRESO</p>
					</div>
					<div class="col-md-2" style="margin-left: -65px;">
						<p class="subtitulos" align="center" style="margin-left: -20px;">Hora / Minutos</p>
						<div class="input-group col-md-2">
							{{Form::text('hr_egreso', null, array('id' => 'hr_egreso', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;', 'disabled'))}}
							<span class="input-group-addon"> : </span>
							{{Form::text('min_egreso',null , array('id' => 'min_egreso', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;', 'disabled'))}}
						</div>
					</div>
					<div class="col-md-2" style="margin-left: -10px;">
						<p class="subtitulos" align="left">FECHA (dd-mm-aaaa)</p>
						{{Form::text('fecha_egreso', null, array('id' => 'fecha_egreso', 'class' => 'form-control', 'style' => 'width: 100px; z-index: 1;', 'disabled'))}}
					</div>
					<div class="col-md-3" style="margin-left: -40px;">
						<div class="col-md-2" style="width: 75px; margin-left: 0px; padding-top: 25px;">
							<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">30</b></label> DÍAS ESTADA</p>
						</div>
						<div class="input-group col-md-2">
							{{Form::text('estada', null, array('id' => 'estada', 'class' => 'form-control', 'style' => 'margin-top: 22px; margin-left: 0px; width: 50px; z-index: 1;'))}}
						</div>
					</div>
					<div class="col-md-2" style="padding-top: 25px; margin-left: -80px;">
						<p class="subtitulos" align="left" style="width: 145px;"><label class="cuadrado2"><b class="numero">31</b></label> CONDICIÓN DE EGRESO</p>
					</div>
					<div class="col-md-2">
						{{Form::select('cond_egreso',["vivo" => "1. VIVO", "fallecido" => "2. FALLECIDO"], null, array('id' => 'cond_egreso', 'class' => 'form-control', 'style' => 'margin-top: 24px; margin-bottom: 40px;'))}}
					</div>

			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="col-md-12" style="padding-left: 0; padding-top: 25px;">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">32</b> </label> DESTINO AL ALTA</p>
					</div>

				</div>

				<div class="col-md-4">
					{{Form::select('destino_alta',["alta" => "1. Domicilio", "derivacion" => "2. Derivación a otro establecimiento de la red pública", "traslado extra sistema" => "3. Derivación a institución privada", "derivacion otra institucion" => "4. Derivación a otros centros u otra institución", "liberacion de responsabilidad" => "5. Alta voluntaria", "fuga" => "6. Fuga del paciente", "hospitalizacion domiciliaria" => "7. Hospitalización domiciliaria", "null" => ""], "null", array('id' => 'destino_alta', 'class' => 'form-control', 'style' => 'margin-top: 22px;', 'disabled'))}}
				</div>

				<div class="col-md-4">

					<div class="col-md-6" style="margin-top:-10px;">
						<p class="letra_chica" align="center">CÓDIGO UNIDAD FUNCIONAL</p>
						{{Form::text('cod_fun_egreso', null, array('id' => 'cod_fun_egreso', 'class' => 'form-control'))}}
					</div>

					<div class="col-md-6">
						<p class="letra_chica" align="center">CÓDIGO SERVICIO CLÍNICO</p>
						{{Form::text('cod_ser_egreso', null, array('id' => 'cod_ser_egreso', 'class' => 'form-control'))}}
					</div>
				</div>
				<br>
			</div>

			<br>
			<div class="row">

				<div class="col-md-offset-9 col-md-3">
					<p class="subtitulos" align="center" ><b>RESPONSABLE: ESTADÍSTICA</b></p>
				</div>

			</div>

			<br>
			<legend></legend>
			<div class="row">

				<div class="col-md-9">
					<p class="subtitulos" align="left"><b>RESPONSABLE MÉDICO O PROFESIONAL TRATANTE</b> </p>
				</div>

				<div class="col-md-3">
					<p class="subtitulos" align="center">CÓDIGO CIE-10 </p>
				</div>


				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">33</b> </label> DIAGNÓSTICO PRINCIPAL</p>
				</div>

				<div class="col-md-6">
					{{Form::text('diagn_principal', null, array('id' => 'diagn_principal', 'class' => 'form-control', 'disabled'))}}
				</div>

				<div class="col-md-3">
					{{Form::text('cie_10', null, array('id' => 'cie_10', 'class' => 'form-control', 'disabled'))}}
				</div>
			</div>

			<br>

			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">34</b> </label>CAUSA EXTERNA (si corresponde) </p>
				</div>

				<div class="col-md-6">
					{{Form::text('diagn_causa_externa', null, array('id' => 'diagn_causa_externa', 'class' => 'form-control', 'disabled'))}}
				</div>

				<div class="col-md-3">
					{{Form::text('cie_10_causa_externa', null, array('id' => 'cie_10_causa_externa', 'class' => 'form-control', 'disabled'))}}
				</div>
			</div>

			<br>

			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">35</b> </label> OTRO DIAGNÓSTICO</p>
				</div>

				<div class="col-md-6">
					{{Form::text('otro_diagnostico0', null, array('id' => 'otro_diagnostico0', 'class' => 'form-control', 'disabled'))}}
				</div>

				<div class="col-md-3">
					{{Form::text('cie_10_otro_diagn0', null, array('id' => 'cie_10_otro_diagn0', 'class' => 'form-control', 'disabled'))}}
				</div>
			</div>

			<br>

			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">36</b> </label> OTRO DIAGNÓSTICO *</p>
				</div>

				<div class="col-md-6">
					{{Form::text('otro_diagnostico1', null, array('id' => 'otro_diagnostico1', 'class' => 'form-control', 'disabled'))}}
				</div>

				<div class="col-md-3">
					{{Form::text('cie_10_otro_diagn1', null, array('id' => 'cie_10_otro_diagn1', 'class' => 'form-control', 'disabled'))}}
				</div>
			</div>
			<br>

			<div id="diagnsExtras">
				<div class="subtitulos" align="center"><b>Otros diagnósticos</b> </div>
			</div>

			<br>
			<div class="row" align="right" style="margin-right: 15px; margin-bottom:10px;">
				<div class="btn btn-primary" id="addDiagn" >+ Diagnósticos</div>
			</div>

			<legend></legend>

			<div class="row">
				<div class="col-md-12">
					<p class="subtitulos" align="left" ><b>DATOS DEL RECIÉN NACIDO: (Sólo completar para egreso obstétrico que termina en Parto)</b></p>
					<table id="rn_table" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th colspan="2"><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">37</b> </label> Orden en el nacimiento (*)</p></th>
								<th><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">38</b> </label> Condición al nacer:</p></th>
								<th><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">39</b> </label> SEXO</p></th>
								<th><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">40</b> </label> Peso en gramos</p></th>
								<th><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">41</b> </label> Apgar 5 minutos</p></th>
								<th><p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">42</b> </label> Anomalia congénita</p></th>
								<th><p class="subtitulos" align="left"><b>Opciones</b></p></th>
							</tr>
						</thead>
						<tbody id="RecienNacidos">
							{{-- <tr>
								<td>1</td>
								<td>
									<select name='cond_nacer[]' class='form-control'>
										<option value='vivo'>Vivo</option>
										<option value='fallecido'>Fallecido</option>
									</select>
								</td>
								<td style="width: 13%;">{{Form::select('sexo',["1" => "01. Hombre", "2" => "02. Mujer", "3" => "03. Intersex (Indeterminado)", "4" => "04. Desconocido"], null, array('id' => 'sexo', 'class' => 'form-control'))}}</td>
								<td>
									{{Form::text('peso1', null, array('id' => 'peso1', 'class' => 'form-control'))}}
								</td>

								<td>{{Form::text('apgar', null, array('id' => 'apgar', 'class' => 'form-control'))}}</td>
								<td>{{Form::select('cond_nacer',["1" => "SI", "2" => "NO"], null, array('id' => 'cond_nacer', 'class' => 'form-control'))}}</td>
							</tr> --}}
						</tbody>
					</table>
				</div>
			</div>
			<br>

			<div class="row">
				<div class="row" align="right" style="margin-right: 15px; margin-bottom:10px;">
					<div class="btn btn-primary" id="addRN" >+ Récien Nacidos</div>
				</div>
				<div class="col-md-9">
					<p class="subtitulos" align="left" ><b>(*) : Ver instructivo</b></p>
				</div>


			</div>

			<legend></legend>
			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">43</b> </label> INTERVENCIÓN QUIRÚRGICA</p>
				</div>
				<div class="col-md-2">
					{{Form::select('int_qu',["true" => "SI", "false" => "NO"], null, array('id' => 'int_qu', 'class' => 'form-control'))}}
				</div>

			</div>
			<br>

			<div class="row">
				<div class="col-md-offset-10 col-md-2">
					<p class="subtitulos" align="center" ><b> CÓDIGO FONASA</b></p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">44</b> </label> INTERVENCIÓN QUIRÚRGICA PRINCIPAL</p>
				</div>
				<div class="col-md-7">
					{{Form::text('int_qu_pr', null, array('id' => 'int_qu_pr', 'class' => 'form-control'))}}
				</div>
				<div class="col-md-2">
					{{Form::text('codigo_fo1', null, array('id' => 'codigo_fo1', 'class' => 'form-control'))}}
				</div>
			</div>
			<br>

			<div class="row">
				<div class="col-md-3">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">45</b> </label> OTRA INTERVENCIÓN QUIRÚRGICA (*)</p>
				</div>
				<div class="col-md-7">
					{{Form::text('ot_int_qu', null, array('id' => 'ot_int_qu', 'class' => 'form-control'))}}
				</div>
				<div class="col-md-2">
					{{Form::text('codigo_fo2', null, array('id' => 'codigo_fo2', 'class' => 'form-control'))}}
				</div>
			</div>
			<br>

			<div class="row">
				<div class="col-md-9">
					<p class="subtitulos" align="left" ><b>(*) : Ver instructivo</b></p>
				</div>
			</div>




		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-2">
					<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">46</b> </label> 	PROCEDIMIENTO</p>
					{{Form::select('proc',["true" => "SI", "false" => "NO"], null, array('id' => 'proc', 'class' => 'form-control'))}}
				</div>

				<div class="col-md-10">
					<div class="col-md-12" style="margin-bottom: 10px;">
						<div class="col-md-3">
							<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">47</b> </label> PROCEDIMIENTO PRINCIPAL</p>
						</div>

						<div class="col-md-7">
							{{Form::text('proc_principal', null, array('id' => 'proc_principal', 'class' => 'form-control'))}}
						</div>

						<div class="col-md-2">
							{{Form::text('cod_fonasa_p_1', null, array('id' => 'cod_fonasa_p_1', 'class' => 'form-control'))}}
						</div>
					</div>
					<br>
					<div class="col-md-12">
						<div class="col-md-3">
							<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">48</b> </label> OTRO PROCEDIMIENTO (*)</p>
						</div>

						<div class="col-md-7">
							{{Form::text('proc_principal2', null, array('id' => 'proc_principal2', 'class' => 'form-control'))}}
						</div>

						<div class="col-md-2">
							{{Form::text('cod_fonasa_p_2', null, array('id' => 'cod_fonasa_p_2', 'class' => 'form-control'))}}
						</div>
					</div>
					<br>
					<div class="col-md-12">
						<div class="col-md-12">
							<p class="subtitulos" align="left" ><b>(*) : Ver instructivo</b></p>
						</div>

					</div>



				</div>
			</div>

		</div>
		<br>
	</div>

	<br>

	<div class="panel panel-default" >
		<div class="panel-heading panel-info">
			<h5>DATOS DEL MÉDICO O PROFESIONAL TRATANTE Y/O QUE FIRMA EL ALTA</h5>
		</div>
		<br>
		<div class="panel-body">
			<div class="row">
				<div class="col">
					<div class="col-md-6">
						<p class="subtitulos" align="left">RUN:</p>
						<div class="input-group">
							{{Form::text('rut_medico', null, array('id' => 'rut_medico', 'class' => 'form-control', 'style' => 'z-index: 1;'))}}
							<span class="input-group-addon"> - </span>
							{{Form::text('dv_medico', null, array('id' => 'dv_medico', 'class' => 'form-control', 'style' => 'width: 50px; z-index: 1;'))}}
						</div>

						<br>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6" style="padding-top: 10px;">
					<p class="subtitulos">
						<label class="cuadrado"><b class="numero">49</b> </label> Nombre:
					</p>
				</div>

				<div class="col-md-6">
					<div class="col-md-4" style="padding-left: 0; ">
						<p class="subtitulos" align="left"><label class="cuadrado2"><b class="numero">50</b> </label> Especialidad:</p>

					</div>

					<div class="col-md-8">
						{{Form::text('especialidad', null, array('id' => 'especialidad', 'class' => 'form-control','readonly'))}}
					</div>
				</div>

			</div>

			<div class="row">
				<div class="col-md-3">
					{{Form::text('apellidoP_medico', null, array('id' => 'apellidoP_medico', 'class' => 'form-control ','readonly'))}}
					<p class="subtitulos" align="center">PRIMER APELLIDO</p>
				</div>

				<div class="col-md-3">
					{{Form::text('apellidoM_medico', null, array('id' => 'apellidoM_medico', 'class' => 'form-control ','readonly'))}}
					<p class="subtitulos" align="center">SEGUNDO APELLIDO</p>
				</div>

				<div class="col-md-6 medicos" align="right">
					{{Form::text('nombres_medico', null, array('id' => 'nombres_medico', 'class' => 'form-control typeahead'))}}
					<p class="subtitulos" align="center">NOMBRES</p>
				</div>
			</div>



			<legend></legend>
			<p class="subtitulos" align="left">Información protegida por Ley N° 19.628, sobre protección de la vida privada y con garantía del secreto estadístico, establecido en la Ley N° 117.374.</p>
		</div>

	</div>

	<br>

	<div class="row">
		<div class="form-group col-md-10">
			{{Form::submit('Actualizar', array('class' => 'btn btn-primary', 'id' => 'guardarDatos')) }}

			{{-- <a href="{{URL::previous()}}" class="btn btn-danger" data-dismiss="modal">Cancelar</a> --}}
			<a href="{{URL::to("paciente/fichaEgresoPDF?id_caso=".$id_caso."")}}" class="btn btn-danger">Generar PDF</a>
			{{-- <button id="fichaEgreso" class="btn ">Generar PDF</button> --}}
		</div>

	</div>

	{{ Form::close() }}

</fieldset>

@stop
