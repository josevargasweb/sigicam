@extends("Templates/template")

@section("titulo")
Gestión de Productos
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión de Productos</a></li>
@stop

@section("script")
<script>
    $('#tablaProductos').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": 10,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 	});

    $('#tablaProductosDeshabilitados').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": 10,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 	});

    var deshabilitar = function(id_producto){
        bootbox.dialog({
            message: "<h4>¿ Desea eliminar este producto ?</h4>",
            title: "Confirmación",
            buttons: {
                main: {
                    label: "Aceptar",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            url: "deshabilitarProducto",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id_producto: id_producto},
                            type: "post",
                            dataType: "json",
                            success: function(response){
                                mensaje(response);
                                console.log("respuesta: ", response);
                            },
                            error: function(error){
                                console.log("error: ", error);
                            }
                        });
                    }
                },
                danger: {
                    label: "Cancelar",
                    className: "btn-danger",
                    callback: function(){

                    }
                }
            }
        });
    }

    var habilitar = function(id_producto){
        bootbox.dialog({
            message: "<h4>¿ Desea reingresar el producto ?</h4>",
            title: "Confirmación",
            buttons: {
                main: {
                    label: "Aceptar",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            url: "habilitarProducto",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id_producto: id_producto},
                            type: "post",
                            dataType: "json",
                            success: function(response){
                                mensaje(response);
                                console.log("respuesta: ", response);
                            },
                            error: function(error){
                                console.log("error: ", error);
                            }
                        });
                    }
                },
                danger: {
                    label: "Cancelar",
                    className: "btn-danger",
                    callback: function(){
                    }
                }
            }
        });
    }

    $(function() {
        $("#formCrearProducto").bootstrapValidator({
			fields: {
                nombre_producto:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
                valor_producto:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			},integer: {
                            message: 'Debe ingresar solo números'
                        }
 			 		}
 			 	},
 			 }
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
            var $button = $form.data('bootstrapValidator').getSubmitButton();
 			$.ajax({
 				url: "{{URL::to('administracion/registrarProducto')}}",
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(response){
 					if(response.exito){
                         swalExito.fire({
						title: 'Exito!',
						text: response.exito,
						didOpen: function() {
							setTimeout(function() {
                        window.location.href = "{{URL::to('/administracion/gestionProductos')}}";
							}, 2000)
						},
						});

 					}
 					if(response.error){
 						console.log(response.error);

                    swalError.fire({
						title: 'Error',
						text:response.error
						});
 					}
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
 		});
	});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#crearProducto" aria-controls="crearProducto" role="tab" data-toggle="tab">Crear Producto</a></li>
        <li role="presentation"><a href="#gestionProducto" aria-controls="gestionProducto" role="tab" data-toggle="tab">Gestionar Productos</a></li>
        <li role="presentation"><a href="#HabilitarProducto" aria-controls="HabilitarProducto" role="tab" data-toggle="tab">Productos Eliminados</a></li>
        
    </ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="crearProducto" style="margin-top:20px;">
            {{ Form::open(array('url' => 'administracion/registrarMedico', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCrearProducto')) }}
            <div class="form-group">
                <label class="col-sm-2 control-label" title="visible">Visible: </label>
                <div class="col-sm-4">
                    {{ Form::select('visible_producto', array( true => 'Si', false => 'No'), 
                        null, array('id' => 'visible_producto', 'class' => 'form-control')) }}
                </div>
            </div>
			<div class="form-group">
				<label for="rut" class="col-sm-2 control-label">Código: </label>
				<div class="col-sm-4" style="width: 490px;">
					{{Form::text('codigo_producto', null, array('id' => 'codigo_producto', 'class' => 'form-control'))}}
				</div>
            </div>
            <div class="form-group">
				<label for="nombres" class="col-sm-2 control-label">Nombre: </label>
				<div class="col-sm-4">
					{{Form::text('nombre_producto', null, array('id' => 'nombre_producto', 'class' => 'form-control'))}}
				</div>
            </div>

            <div class="form-group">
				<label for="nombres" class="col-sm-2 control-label">Valor: </label>
				<div class="col-sm-4">
					{{Form::text('valor_producto', null, array('id' => 'valor_producto', 'class' => 'form-control'))}}
				</div>
            </div>

            {{-- <div id="divEstab" class="form-group">
				<label for="establecimiento" class="col-sm-2 control-label">Tipo: </label>
				<div class="col-sm-4">
					{{ Form::select('tipo_producto', array( 'medicamento' => 'Medicamento' ,'insumo' => 'Insumo', 'suero' => 'Suero'), null, array('class' => 'form-control', 'id' => 'tipo_proucto')) }}
				</div>
            </div> --}}

			<div class="form-group">
				<div class="col-sm-4">
					{{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
				</div>
			</div>
			{{ Form::close() }}
		</div>

		<div role="tabpanel" class="tab-pane" id="HabilitarProducto" style="margin-top:20px;">
			<div class="table-responsive">
                <table id="tablaProductosDeshabilitados" class="table table-striped table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            {{-- <th>Tipo</th> --}}
                            <th>Valor</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deshabilitados as $deshabilitado)
                        <tr>
                            <td>{{$deshabilitado["nombre_establecimiento"]}}</td> 
                            <td>{{$deshabilitado["codigo"]}}</td>
                            <td>{{$deshabilitado["nombre"]}}</td>
                            {{-- <td>{{$deshabilitado["tipo"]}}</td> --}}
                            <td>{{$deshabilitado["valor"]}}</td>
                            <td> <a class="btn btn-sm btn-primary" onclick="habilitar({{$deshabilitado['id']}});" class="cursor">Habilitar</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
        </div>
        
        <div role="tabpanel" class="tab-pane" id="gestionProducto" style="margin-top:20px;">
			<div class="table-responsive">
                <table id="tablaProductos" class="table table-striped table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                           {{--  <th>Tipo</th> --}}
                            <th>Valor</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>{{$producto["nombre_establecimiento"]}}</td> 
                            <td>{{$producto["codigo"]}}</td>
                            <td>{{$producto["nombre"]}}</td>
                            {{-- <td>{{$producto["tipo"]}}</td> --}}
                            <td>{{$producto["valor"]}}</td>
                            <td> 
                                <a class="btn btn-sm btn-danger" onclick="deshabilitar({{$producto['id']}});" class="cursor">Borrar</a>    
                                <a class="btn btn-sm btn-primary" href="{{asset('administracion/editarProducto/'.$producto['id'])}}" class="cursor">Editar</a>
                                <a class="btn btn-sm btn-success" href="{{asset('administracion/actualizarProducto/'.$producto['id'])}}" class="cursor">Actualizar Valor</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
		</div>
	</div>

</div>
@stop

