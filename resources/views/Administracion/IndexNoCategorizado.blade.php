@extends("Templates/template")

@section("titulo")
	No categorizados
@stop

@section("miga")
	<li><a href="#">Administración</a></li>
	<li><a href="#">No categorizados</a></li>
@stop

@section("script")

<script>
	var modal_id = "";

	function modalRiesgoDependencia (id_evolucion){
		modal_id = id_evolucion;
		
		$("#modalFormularioRiesgo").modal();
	}

	function btnRiesgoDependencia (){

		var valorDependencia = 0;

		

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
		$("#d1_"+modal_id).val($('#dependencia1').val());
		$("#d2_"+modal_id).val($('#dependencia2').val());
		$("#d3_"+modal_id).val($('#dependencia3').val());
		$("#d4_"+modal_id).val($('#dependencia4').val());
		$("#d5_"+modal_id).val($('#dependencia5').val());
		$("#d6_"+modal_id).val($('#dependencia6').val());

		var valorRiesgo = 0;

		valorRiesgo = parseInt($('#riesgo1').val()) + parseInt($('#riesgo2').val()) +parseInt($('#riesgo3').val());
		$("#r1_"+modal_id).val($('#riesgo1').val());
		$("#r2_"+modal_id).val($('#riesgo2').val());
		$("#r3_"+modal_id).val($('#riesgo3').val());
		
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
		$("#r4_"+modal_id).val($('#riesgo4').val());
		$("#r5_"+modal_id).val($('#riesgo5').val());
		$("#r6_"+modal_id).val($('#riesgo6').val());
		$("#r7_"+modal_id).val($('#riesgo7').val());
		$("#r8_"+modal_id).val($('#riesgo8').val());

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

		$("#c_"+modal_id).val(riesgoDependencia).trigger("change");
		//$("#div-comentario-riesgo").show();

		$('#modalFormularioRiesgo').modal('hide');

	}

    $(function() {
		
		$("#administracionMenu").collapse();

        $("#formNoCategoirzado").bootstrapValidator({
			fields: {
 			 	rut: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El run es obligatorio'
 			 			},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $.trim($("#rut").val());
								var dv = $.trim($("#dv").val());
								if (!esRutValido(field_rut, dv)){
									$("#dv").val('');
								}
								return true;
							}
						}
						
 			 		}
 			 	},
 			 	dv: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El dígito verificador es obligatorio'
 			 			},
 			 			callback: {
 			 				callback: function(value, validator, $field){
 			 					if (value === '') {
 			 						return true;
 			 					}
 			 					var rut=$.trim($("#rut").val());
 			 					var esValido=esRutValido(rut, value);
 			 					if(!esValido){
 			 						return {valid: false, message: "Dígito verificador no coincide con el run"};
 			 					}
								//getUsuario(rut)
 			 					return true;
 			 				}
 			 			}
 			 		}
 			 	}
 			 }
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){

			//limpiar
			$("#excelNoCategorizado").addClass('hidden');
			$("#pdfNoCategorizado").addClass('hidden');
			$("#diasNoCategorizados").empty();

            //prueba 21311067-5
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
            var $button = $form.data('bootstrapValidator').getSubmitButton();
            $("#BuscarPaciente").attr('disabled',true);
 			$.ajax({
 				url: "{{URL::to('infoPacienteNoCategorizado')}}",
 				type: "GET",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(data){

					//limpiar diasNoCategorizados
					$("#diasNoCategorizados").empty();

					html = "";
					key=0;
					
					data.forEach(function(element, indice) {
						if(element){
							if(key==0){
								cabecera="<legend>Días sin categorizar</legend> <div class='col-md-4'>Fecha</div><div class='col-md-6 '>Categorizacion</div>";
								key++;
							}else{
								cabecera ="";
							}
							//primera columna
							date_format= new Date(element.date_trunc);
							fecha_ord =  date_format.getDate()+'/'+(date_format.getMonth()+1)+'/'+date_format.getFullYear();

							html += "<div class='col-md-12'>"+cabecera+"<div class='col-md-4'> <div class='form-group' style='display: inline-block !important;'> <input class='form-control hide' maxlength='2' name='id_evolucion[]' style='padding:10%' type='text' readonly='readonly' value='"+element.id+"'> <input class='form-control' name='fecha[]' style='padding:10%' type='text' readonly='readonly' value='"+fecha_ord+"'> </div> </div>";
							
							html += "<div class='col-md-6'> <div class='form-group' style='width:45%;display: inline-block !important;margin-right:15px;'> <input class='form-control' maxlength='2' name='categorizacion[]' style='padding:10%' type='text' id='c_"+element.id+"'> </div> <div class='btn btn-primary' style='display: inline-block !important;' onclick='modalRiesgoDependencia("+element.id+")'>Calcular </div> </div>";
							
							html += "<div class='col-md-2 hide' > <input class='form-control' name='r1[]' style='padding:10%' type='text' readonly='readonly' id='r1_"+element.id+"' value=''> <input class='form-control' name='r2[]' style='padding:10%' type='text' readonly='readonly' id='r2_"+element.id+"' value=''> <input class='form-control' name='r3[]' style='padding:10%' type='text' readonly='readonly' id='r3_"+element.id+"' value=''> <input class='form-control' name='r4[]' style='padding:10%' type='text' readonly='readonly' id='r4_"+element.id+"' value=''> <input class='form-control' name='r5[]' style='padding:10%' type='text' readonly='readonly' id='r5_"+element.id+"' value=''> <input class='form-control' name='r6[]' style='padding:10%' type='text' readonly='readonly' id='r6_"+element.id+"' value=''> <input class='form-control' name='r7[]' style='padding:10%' type='text' readonly='readonly' id='r7_"+element.id+"' value=''> <input class='form-control' name='r8[]' style='padding:10%' type='text' readonly='readonly' id='r8_"+element.id+"' value=''>  <input class='form-control' name='d1[]' style='padding:10%' type='text' readonly='readonly' id='d1_"+element.id+"' value=''> <input class='form-control' name='d2[]' style='padding:10%' type='text' readonly='readonly' id='d2_"+element.id+"' value=''> <input class='form-control' name='d3[]' style='padding:10%' type='text' readonly='readonly' id='d3_"+element.id+"' value=''> <input class='form-control' name='d4[]' style='padding:10%' type='text' readonly='readonly' id='d4_"+element.id+"' value=''> <input class='form-control' name='d5[]' style='padding:10%' type='text' readonly='readonly' id='d5_"+element.id+"' value=''> <input class='form-control' name='d6[]' style='padding:10%' type='text' readonly='readonly' id='d6_"+element.id+"' value=''> </div></div><br>";	
						}
                    
					});
					if(html == ""){
						$("#diasNoCategorizados").append("<div class='col-md-12'>No se encontraron fechas sin categorizar</div>");
					}else{
						$("#diasNoCategorizados").append(html);
						$("#excelNoCategorizado").removeClass('hidden');
						$("#pdfNoCategorizado").removeClass('hidden');
					}

					$('#formCategorizar').data('bootstrapValidator').addField('categorizacion[]');
					$("#formCategorizar").data('bootstrapValidator').resetForm();
					
 				},
 				error: function(error){
 					console.log(error);
 				}
            });
            $("#BuscarPaciente").attr('disabled',false);
             
		});
		 
		$("#formCategorizar").bootstrapValidator({
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
			}
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            //prueba 21311067-5
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
            var $button = $form.data('bootstrapValidator').getSubmitButton();
            $("#CategorizarPacientes").attr('disabled',true);
 			$.ajax({
 				url: "{{URL::to('categorizarNoCategorizados')}}",
 				type: "GET",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(data){
					 swalExito.fire({
					title: 'Exito!',
					text: data['exito'],
					didOpen: function() {
						setTimeout(function() {
							$("#diasNoCategorizados").empty();
						}, 2000)
					},
					});
 				},
 				error: function(error){
 					console.log(error);
 				}
            });
            $("#CategorizarPacientes").attr('disabled',false);
		 });
		 
		 $("#pdfNoCategorizado").click(function(){
			rut = $("#rut").val();
			reporte = 'pdf';
			window.location.href = "{{URL('/reporteNoCategorizados')}}"+"/"+rut+"/"+reporte;
		}); 

		$("#excelNoCategorizado").click(function(){
			rut = $("#rut").val();
			reporte = 'excel';
			window.location.href = "{{URL('/reporteNoCategorizados')}}"+"/"+rut+"/"+reporte;
		}); 
	});
        
</script>

@stop

@section("section")
    <style>
        .formulario > .panel-default > .panel-heading {
            background-color: #bce8f1 !important;
        }
    </style>

    <br>
    

    <div class="formulario" style="    height: 550px;">
		<div class="panel panel-default" >
			<div class="panel-heading panel-info">
                <h4>Datos de paciente</h4>
            </div>
            <div class="panel-body">
				<p>En esta sección pueden buscar los días que no se categorizo el paciente. De momento solo se encuentra disponible la version para pacientes con RUN</p>
                {{ Form::open(array( 'method' => 'get', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formNoCategoirzado')) }}
                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="col-sm-12">
                            <label class="control-label" title="Extranjero">Run: </label>
                            <div class="input-group">
                                {{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
                                <span class="input-group-addon"> - </span>
                                {{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4" style="margin-top: 23px;">
                        {{Form::submit('Buscar', array('class' => 'btn btn-primary', 'id' => 'BuscarPaciente', 'type' => 'button')) }}
					</div>

					<div class="col-sm-12">
						<button id="pdfNoCategorizado" class="btn btn-danger hidden">PDF dias no categorizado</button>
						<button id="excelNoCategorizado" class="btn btn-success hidden">Excel dias no categorizado</button>
					</div>
                </div>
                {{ Form::close() }}	  

				<br>
                {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCategorizar')) }}
                <div class="row" >
                    <div class="form-group col-md-12" id="diasNoCategorizados">
                        
                    </div>

                    
				</div>
				<div class="col-md-12" style="margin-top: 23px;">
					{{Form::submit('Categorizar', array('class' => 'btn btn-primary', 'id' => 'CategorizarPacientes')) }}
				</div>
                {{ Form::close() }}	  
            </div>
        </div>
    </div>

    
    @include('modals.categorizacion_riesgo')
        
        
        
@stop
