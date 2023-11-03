@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")
<script>
    $(function(){
        table = $('#historialDerivados').dataTable({
            "bJQueryUI": true,
            "iDisplayLength": 10,
            // "order": [[5,"desc"]],
            "language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
        });
    });
    var obtenerListaComentariosDerivado = function(idCaso, idLista){
        $.ajax({
            url: "obtenerComentariosListaDerivado",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {idCaso:idCaso, idLista: idLista},
            dataType: "json",
            type: "post",
            success: function(data){
                console.log("comentarios: ",data);
                $('#listaComentariosDerivado').dataTable( {
                "aaData": data,
                "bDestroy": true,
                "columnDefs": [
                { type: 'date-euro', targets: 0 }
            ],
                "columns": [
                    { "data": "comentario" },
                    { "data": "fecha" }
                ],
                "language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
            })
            },
            error: function(error){
                console.log(error);
            }
        });
    }
</script>
@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Historial Derivados</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")
<fieldset>
    <a href="javascript:history.back()" class="btn btn-default" style="color: #399865">Volver</a>
    <br><br>
    <legend>Historial Derivados</legend>
    
	<div class="table-responsive">
	<table id="historialDerivados" class="table  table-condensed table-hover">
		<thead>
                <tr>
				<th style="width:100px">Run</th>
				<th>Nombre Completo</th>
				{{-- <th>Fecha Hospitalización</th> --}}
                <th>Fecha de Ingreso Derivación</th>
                <th>Fecha de Egreso Derivación</th>
                <th>Historial Comentarios</th>
            </tr>
		</thead>
		<tbody>
            @foreach ($response as $resp)
                <tr>
                    <td>{{$resp->rut . "-" .$resp->dv}}</td>
                    <td>{{$resp->nombre . " " .$resp->apellidoP . " " .$resp->apellidoM}}</td>
                    {{-- <td><label hidden>{{$resp->fechaHospitalizacion}}</label>{{\Carbon\Carbon::parse($resp->fechaHospitalizacion)->format('d/m/Y H:i')}}</td> --}}
                    <td><label hidden>{{$resp->fechaIngresoDerivacion}}</label>{{\Carbon\Carbon::parse($resp->fechaIngresoDerivacion)->format('d/m/Y H:i')}}</td>
                    @if($resp->fechaEgresoDerivacion)
                    <td><label hidden>{{$resp->fechaEgresoDerivacion}}</label>{{\Carbon\Carbon::parse($resp->fechaEgresoDerivacion)->format('d/m/Y H:i')}}</td>
                    @else
                    <td>---</td>
                    @endif
                    <td><a href="#" class="btn btn-primary" data-toggle="modal" onclick="obtenerListaComentariosDerivado({{$resp->idCaso}},{{$resp->idLista}})" data-target="#modalListaComentariosDerivado">Ver</a></td>
                </tr>
            @endforeach
        </tbody>
	</table>
	</div>
</fieldset>

<div id="modalListaComentariosDerivado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title">Historial De Comentarios</h4>
            </div>
          <div class="modal-body">			
                 <fieldset>
                  <div class="table-responsive">
                  <table id="listaComentariosDerivado" class="table  table-condensed table-hover">
                      <thead>
                          <tr style="background:#399865;">
                              <th style="width:100px">Comentario</th>
                              <th>Fecha</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                  </table>
                  </div>
              </fieldset>
            </div> 
          <div class="modal-footer">
          </div>
      </div>
    </div>
</div>

@stop