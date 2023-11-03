@extends("Templates/template")

@section("titulo")
Actualizar Producto
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#">Gestión de Productos</a></li>
<li><a href="#" onclick='location.reload()'>Actualizar Producto</a></li>
@stop

@section("script")

<script>
    $(function() {

        $("#formActualizarProducto").bootstrapValidator({
			fields: {
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
 				url: "{{URL::to('administracion/actualizarValorProducto')}}",
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(response){
                     //mensaje(response);
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

@stop

@section("section")
    <style>
        .formulario > .panel-default > .panel-heading {
            background-color: #bce8f1 !important;
        }
    </style>

    <br>
    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formActualizarProducto')) }}

    {{Form::hidden('id_producto', $producto->id)}}
    <div class="formulario" style="height: 550px;">
		<div class="panel panel-default" >
			<div class="panel-heading panel-info">
                <h4>Datos de Producto</h4>
            </div>
            <div class="panel-body">


                <div class="form-group">
                    <label class="col-sm-2 control-label" title="visible">Visible: </label>
                    <div class="col-sm-4">
                        {{ Form::select('visible_producto', array( true => 'Si', false => 'No'), 
                            $producto->visible, array('id' => 'visible_producto', 'class' => 'form-control', 'disabled' )) }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="rut" class="col-sm-2 control-label">Código: </label>
                    <div class="col-sm-4" style="width: 490px;">
                        {{Form::text('codigo_producto', $producto->codigo, array('id' => 'codigo_producto', 'class' => 'form-control', 'readonly' => 'true'))}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="nombres" class="col-sm-2 control-label">Nombre: </label>
                    <div class="col-sm-4">
                        {{Form::text('nombre_producto', $producto->nombre, array('id' => 'nombre_producto', 'class' => 'form-control', 'readonly' => 'true'))}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="nombres" class="col-sm-2 control-label">Valor: </label>
                    <div class="col-sm-4">
                        {{Form::text('valor_producto', $producto->valor, array('id' => 'valor_producto', 'class' => 'form-control'))}}
                    </div>
                </div>
                {{-- <div id="divEstab" class="form-group">
                    <label for="establecimiento" class="col-sm-2 control-label">Tipo: </label>
                    <div class="col-sm-4">
                        {{ Form::select('tipo_producto', array( 'medicamento' => 'Medicamento' ,'insumo' => 'Insumo', 'suero' => 'Suero'), $producto->tipo, array('class' => 'form-control', 'id' => 'tipo_proucto', 'disabled')) }}
                    </div>
                </div> --}}

                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="col-sm-10">
                            {{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
                        </div>       
                    </div>    
                </div>
            </div>
        </div>
        {{ Form::close() }}  
    </div>
	   
        
        
        
@stop
