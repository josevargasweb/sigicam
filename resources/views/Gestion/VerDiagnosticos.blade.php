<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 5/15/15
 * Time: 1:52 PM
 */

?>

<script>

    var _ingresarDiagnostico = function (form) {	
		swalCargando.fire({});
        $.ajax({
            url: "{{ URL::to('/ingresarDiagnostico')}}",
            data: form.serialize(),
            type: "post",
            dataType: "json",
            success: function (data) {
				swalCargando.close();
				Swal.hideLoading();
				if(data.error){
					swalError.fire({
						title: 'Error',
						text:data.error
						});
					$("#div-motivo").show();
				}else{
					swalExito.fire({
						title: 'Exito!',
						text: "se ha ingresado el diagnostico",
					});

					$("#div-motivo").hide();
					ultima_ubicacion = $(".ubicacion").val();
					$("#modalVerDiagnosticos .modal-body").html(data.contenido);
					$(".ubicacion").val(ultima_ubicacion);
					//Recargar la lista de pacientes hospitalizados en domicilio en caso de que ese encuentre en esta lista 
					if(typeof table !== 'undefined') {
						table.api().ajax.reload();
					}
				}
            },
            error: function (error) {
				swalCargando.close();
				Swal.hideLoading();
            }
        });
    }

    var _cambiarCasoSocial = function (form) {
		swalCargando.fire({});
        $.ajax({
            url: "{{ URL::to("cambiarCasoSocial") }}",
            data: form.serialize(),
            type: "post",
            dataType: "json",
            success: function(data) {
				swalCargando.close();
				Swal.hideLoading();
                var st = '';
                if(data.caso_social === true){
                    st = 'El caso se ha marcado como caso social.';
                }
                else{
                    st = 'El caso se ha desmarcado como caso social.';
                }
						swalExito.fire({
					title: 'Modificación de caso',
					text:st
					});
            },
            error: function (error) {
				swalCargando.close();
				Swal.hideLoading();
            }
        });
    }

    $(function () {
        $("#formIngresarDiagnostico").submit(function (e) {
            e.preventDefault();
			var form = $(this);
			console.log(form);
            bootbox.dialog({
                message: "¿Desea ingresar el/los diagnóstico(s)?",
                title: "Confirmación",
                buttons: {
                    main: {
                        label: "Cancelar"
                        //className: "btn-primary"
                    },
                    success: {
                        label: "Ingresar",
                        className: "btn-primary",
                        callback: function () {
							
                            _ingresarDiagnostico(form);
                        }
                    }
                }
            });
            return false;
		});
		
        $("#formCambiarCasoSocial").submit(function(e){
            e.preventDefault();
            var form = $(this);
            _cambiarCasoSocial(form);
            return false;
        });

        $(document).on("input","input[name='diagnosticos[]']",function(){
			var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
			//a.prop("disabled", true);
			var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
			if($cie10.val()){
				$(this).val("");
				$cie10.val("");
				$(this).trigger('input');
			}
		});

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
		limit: 1000
	});
	datos_cie10.initialize();

  	$('.diagnostico_cie101 .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'nombre_cie10',
		source: datos_cie10.ttAdapter(),
		limit: 1000,
		templates: {
			empty: [
			'<div class="empty-message">',
				'No hay resultados',
			'</div>'
			].join('\n'),
			suggestion: function(data){
				if(data.nombre_categoria == null){
					return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				}else{
					return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				}	
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
		}
	}).on('typeahead:selected', function(event, selection){
			console.log("JJJJJJJJJJ");
			$("#texto_cie10").val(selection.nombre_cie10);
			$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
			$("#cie10-principal").prop("disabled", false);
		}).on('typeahead:close', function(ev, suggestion) {//Mauricio
	  console.log('Close typeahead: ' + suggestion);
	  	var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		console.log("padre:",$(this).parents(".diagnostico_cie101"));
		console.log("cie10:",$cie10.val(),!$cie10.val());
		console.log("this:",$(this).val(),!!$(this).val());
		if(!$cie10.val()&&$(this).val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
			console.log("RRRRRRRR");
			//$("#cie10-principal").prop("disabled", false);
		}
	});

	var count=0;

	function invoice_no_setup_typeahead(self, self2) {
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
			limit: 1000
		});
		datos_cie10.initialize();

	    $(self).typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_cie10',
			limit: 1000,
			source: datos_cie10.ttAdapter(),
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					if(data.nombre_categoria == null){
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}else{
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}	
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
			}
		}).on('typeahead:selected', function(event, selection){
			//$("#texto_cie10").val(selection.nombre_cie10);
			$(self2).val(selection.id_cie10);
		}).on('typeahead:close', function(ev, suggestion) {//Mauricio
			var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
			var a = $(this).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
			console.log("botooon: ", a);
			if(!$cie10.val()&&$(this).val())
			{
				$(this).val("");
				$cie10.val("");
				$(this).trigger('input');
			}else{
				console.log("AAAAAAAAA");
				a.prop("disabled", false);
			}
		});

	}

	agregar=function(boton){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		//var $clone =  $template.clone().removeClass('hide').insertBefore($template);

		//console.log($clone.find("input"));
		//var el = $("<input type='text' name='retrun-order-invoice_no' class='return-order-invoice_no'>").insertAfter($('#fileTemplate'));
		$clone.find("input").eq(2).val("");
		invoice_no_setup_typeahead($clone.find("input")[1],$clone.find("input")[2]);
		$(boton).prop("disabled", true);
		/*var $input = $clone.find('input[type="file"]');
		var id="id_"+count;
		$input.prop("id", id);
		$('#'+id).fileinput();
		console.log("#"+id+" .file-input-new .input-group");
		$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});
		count++;
		*/
	}

	borrar=function(boton){
		$(boton).parent().parent().parent().remove();
		var diagnosticos = $("[name='diagnosticos[]']");
		var cantidad = diagnosticos.length -3;
		var anterior = cantidad - 1;
		var a = $(diagnosticos[anterior]).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
		console.log("botooon: ", a);
		if(cantidad - 1 == 0){
			console.log("if");
			$("#cie10-principal").prop("disabled", false);
		}else{
			console.log("else");
			a.prop("disabled", false);
		}
	}
});
</script>

<fieldset>
    <legend>Diagnósticos</legend>
    <div class="form-group col-md-12">
        <div class="table-responsive">
            <table id="tabla-diagnostico-paciente" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th style="color: black;">Fecha</th>
                    <th style="color: black;">Diagnóstico</th>
					<th style="color: black;">Comentario</th>
                </tr>
                </thead>
                <tbody>

                @foreach($diagnosticos as $diagnostico)
                    <tr>
                        <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $diagnostico->fecha)->format("d-m-Y H:i:s")}}</td>
                        <td>{{$diagnostico->id_cie_10}} {{$diagnostico->diagnostico}}</td>
						<td>{{$diagnostico->comentario}} </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
	</div>
	
    @if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS
		&& Session::get("usuario")->tipo !== TipoUsuario::CENSO
	)
		<legend>Agregar nuevo</legend>
        {{ Form::open( array('url' => 'ingresarDiagnostico', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formIngresarDiagnostico') ) }}
		{{ Form::hidden('caso', "$caso", array('class' => 'detalle-diagnostico')) }}
		{{ Form::hidden('ubicacion', null, array('class' => 'ubicacion')) }}
		
		<div class="row" style="padding-bottom:25px">
			
				<div class="form-group col-md-12">
					<div class="col-sm-2">
						<label for="files[]">Diagnóstico CIE10</label>
					</div>
					<div class="col-sm-9 diagnostico_cie101">
						<input type="text" name="diagnosticos[]" class='form-control typeahead' style='width:350px'/>
						<input type="hidden" name="hidden_diagnosticos[]">
					</div>
					<div class="col-sm-1" style="right: 30px;">
						<button disabled id="cie10-principal" class="btn btn-default" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
					</div>
				</div>
		</div>
				
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-sm-2">
					<label for="">Comentario diagnóstico</label>
				</div>
				<div class="col-sm-9 ">
					<input type="text" name="nuevo-diagnostico[]" class='form-control' style='width:350px' />
				</div>

			</div>
		</div>
					

    <div id="fileTemplate" class="row hide" style="padding-bottom:25px">
		<div class="form-group col-md-12">
			<div class="col-md-2">
				<label for="files[]">Diagnóstico CIE10</label>
			</div>
			<div class="col-md-7 diagnostico_cie101">
				<input type="text" name="diagnosticos[]" class='form-control typeahead' style='width:350px'/>

				<input type="hidden" name="hidden_diagnosticos[]">
			</div>
			<div class="col-md-3" style="right: -21px;">
				<button disabled class="btn btn-default" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
				<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
			</div>
		</div>
						
		<div class="form-group col-md-12">
			<div class="col-sm-2">
				<label for="">Comentario diagnóstico</label>
			</div>
			<div class="col-sm-9 ">
				<input type="text" name="nuevo-diagnostico[]" class='form-control' style='width:350px' />
			</div>

		</div>
	</div>

	<div id="div-motivo" class="col-sm-8" style="margin-top: 10px; margin-bottom: 10px;" hidden>
		<label>Motivo</label>
		{{ Form::textarea('motivo', null, array('id' => 'motivo','class' => 'form-control', 'style'=>'height:100px;')) }}
	</div>

	<div class="row" style="padding-top:25px">
		<div class="form-group col-md-12">
			{{ Form::submit('Guardar diagnóstico', array('id' => 'btnCambiarCategoria', 'class' => 'btn btn-primary', 'style'=>'margin-left: 12px;')) }}
		</div>
	</div>
	
	{{ Form::close() }}

</fieldset>

<br><br>

<fieldset>
    
	@if(Session::get("usuario")->tipo != TipoUsuario::MEDICO)
		<legend>Caso social</legend>
		{{ Form::open([ 'url' => 'cambiarCasoSocial', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formCambiarCasoSocial']) }}
			<div class="form-group col-md-12">
				<!-- <label for="caso_social" class="col-sm-2 control-label">Caso social: </label> -->
				<div class="col-sm-3">Es caso social:</div>
				<div class="col-sm-2">
					<label for="caso_social_no" class="col-sm-2 control-label" style="margin:0;">No</label>
					{{Form::radio('caso_social', "no", $caso_obj->caso_social === false, ['required' => true, "style" => "vertical-align: middle; margin:0", "id" => "caso_social_no"])}}
				</div>
				<div class="col-sm-2">
					<label for="caso_social_si" class="col-sm-2 control-label">Sí</label>
					{{Form::radio('caso_social', "si", $caso_obj->caso_social === true, ['required' => true, "style" => "vertical-align: middle; margin:0", "id" => "caso_social_si"] )}}
				</div>
				<div class="col-sm-5">
					{{ Form::submit('Actualizar', array('id' => 'btnCambiarCaso', 'class' => 'btn btn-primary',)) }}
				</div>
			</div>
		{{ Form::close() }}
	@endif



    @endif
</fieldset>
