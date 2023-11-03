@extends("Templates/template")

@section("titulo")
Inicio
@stop

@section("script")
<script>

	$(function(){
		var mensaje="{{ $mensajes }}";
		if(mensaje != "") $().notifyMe('top', 'info', '', mensaje, 200);

		$('#resumen').dataTable({	
			"iDisplayLength": -1,
			"bJQueryUI": true,
			"searching": false,
			"ordering": false,
			"info": false,
			"bAutoWidth" : true,
			"oLanguage": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
			"fnInitComplete": function(oSettings, json) {
				$("#resumen_length").remove();
			}
		});
	});
</script>
@stop

@section("css")
<style type="text/css">
	.notify{
		z-index: 9999;
	}
	.notify-content{
		font-size: 28px;
	}
</style>
@stop

@section("section")

<fieldset>
	<legend>Resumen camas</legend>
	<div class="table-responsive">
		<table id="resumen" class="table table-bordered table-striped" style="width: 100% !important" width="100%">
			<thead style="text-align: center;">
				<tr>
					<th></th>
					<th><figure>{{ HTML::image('img/camaVerde.png', null, $attributes = array("class" => "cama")) }} <figcaption><label>Cama libre</label></figcaption> </figure></th>
					
					<th><figure>{{ HTML::image('img/camaRoja.png', null, $attributes = array("class" => "cama")) }} <figcaption><label>Cama ocupada</label></figcaption> </figure></th>
					<th><figure>{{ HTML::image('img/camaAzul.png', null, $attributes = array("class" => "cama")) }} <figcaption><label>Cama reconvertida</label></figcaption> </figure></th>
					<th><figure>{{ HTML::image('img/camaNegra.png', null, $attributes = array("class" => "cama")) }} <figcaption><label>Cama bloqueada</label></figcaption> </figure></th>
				</tr>
			</thead>
			<tbody>
				@foreach($resumen as $r)
				<tr>
					@if(Session::get('usuario')->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
					<th>{{HTML::link("index/camas/".$r['id'], $r["nombre"], ["class" => "cursor negro"])}}</th>
					@else
					<th>{{$r["nombre"]}}</th>
					@endif
					<td>{{$r["libres"]}}</td>
					<td>{{$r["ocupadas"]}}</td>
					<td>{{$r["reconvertidas"]}}</td>
					<td>{{$r["bloqueadas"]}}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td>Total</td>
					<td>{{$total["totalLibres"]}}</td>
					<td>{{$total["totalOcupadas"]}}</td>
					<td>{{$total["totalReconvertidas"]}}</td>
					<td>{{$total["totalBloqueadas"]}}</td>
				</tr>
			</tfoot>
		</table>
	</div>
</fieldset>


@stop
