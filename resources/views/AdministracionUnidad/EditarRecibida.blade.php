@extends("Templates/template")

@section("titulo")
Editar servicio
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li>{{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión de establecimiento')}}</li>
<li>{{ HTML::link("/administracionUnidad/unidad/$idEstab", "$nombre") }}</li>
<li>{{ HTML::link("/administracionUnidad/editarUnidad/$idEstab/$idUnidad", "$alias") }}</li>
<li><a href="#" onclick='location.reload()'>{{$servicio}}</a></li>
@stop

@section("script")
<script>
	$(function(){

		$(".servicios").bootstrapDualListbox({
			filterPlaceHolder: "Buscar",
			filterTextClear: "Quitar todo",
			infoText: "Mostrando {0}",
			moveAllLabel: "Mover todo",
			selectedListLabel: "Servicios seleccionadas",
			nonSelectedListLabel: "Servicios no seleccionados",
			infoTextEmpty: "Lista vacía",
			infoTextFiltered: "<span class='label label-warning'>Filtrados</span> {0} de {1}"
		});

		$("#formUpdateServiciosRecibidos").submit(function(evt){
			evt.preventDefault(evt);
			var servicios=[];
			$("#bootstrap-duallistbox-selected-list_servicios option").each(function(){
				servicios.push($(this).val());
			});
			var establecimiento=$("#estabHidden").val();
			var unidadEn=$("#unidadEn").val();
			$.ajax({
				url: $(this).prop("action"),
				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				data:{ servicios: servicios, establecimiento: establecimiento, unidad: unidadEn },
				dataType: "json",
				type: "post",
				success: function(data){
					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								location.href="{{ URL::previous() }}";
							}, 2000)
						},
						});
					} 
					if(data.error){
						swalError.fire({
						title: 'Error',
						text:data.error
						});
					} 
				},
				error: function(error){
					console.log(error);
				}
			});
			return false;
		});

		$("select[multiple='multiple']").css("width", "100%");
	});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")
	@if($estab->some)
		@include("AdministracionUnidad/Alerta")
	@endif
	<fieldset>
	<legend style="margin: 0;">Servicios recibidos</legend>
	<div class="row">
		{{ Form::open(array('url' => 'administracion/updateServiciosRecibidos', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formUpdateServiciosRecibidos', 'style' => 'padding-left: 15px; padding-top: 30px;')) }}
		{{ Form::hidden('establecimiento', $idEstab, array('id' => 'estabHidden')) }}
		{{ Form::hidden('unidad', $idUnidad, array('id' => 'unidadEn')) }}
		<select class="servicios" name="servicios"  multiple="multiple" size="10" style="width: 100%;">
			@foreach($noTieneServiciosRecibidos as $key => $value)
			<option value="{{$key}}">{{$value}}</option>
			@endforeach
			@foreach($tieneServiciosRecibidos as $key => $value)
			<option value="{{$key}}" selected>{{$value}}</option>
			@endforeach
		</select> 
		<br>
		{{ Form::submit('Editar', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</fieldset>
@stop