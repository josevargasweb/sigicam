@extends("Templates/template")

@section("titulo")
Historico
@stop

@section("miga")
<li><a href="#">Historico</a></li>
<li><a href="#" onclick='location.reload()'>Histórico</a></li>
@stop

@section("script")
<script>
$(function(){

    table=$('#tablaResultado').dataTable({
        "bJQueryUI": true,
        "iDisplayLength": 15,
        "language": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        }

    });

    $("#tablaResultado").on("click", "a.info-paciente", function(){
        $.ajax({
            url: $(this).prop("href"),
            type: "GET",
            success: function(data){
                console.log(data);
                $("#informacion").css({display:'initial'}).html(data);
                /* $("#modalDocumentosDerivacion2 .modal-body").html(data);
			    $("#modalDocumentosDerivacion2").modal(); */
            },
            error: function(error){
                console.log(error);
            }

        });
        return false;
    });

});
</script>
@stop
 
@section("section")
<fieldset>
	<legend>Datos históricos del paciente</legend>


    @if($Miunidad != "error")
	    <form style='display: hidden' action='../../unidad/{{$Miunidad}}' method='GET' id='form'>
	        <input hidden type='paciente' name='paciente' value='{{$paciente}}'>
		    <input hidden type='text' name='id_sala' value='{{$sala}}'>
	    	<input hidden type='text' name='id_cama' value='{{$cama}}'>
	    	<input hidden type='text' name='caso' value='{{$idCaso}}'>
		    <button class='btn btn-primary' type='submit' style="text-align : right">Ir a unidad</button>
	    </form>
    @endif
    <br>
 

 
    <div class="table-responsive">
    <table id="tablaResultado" class="table table-striped table-condensed table-bordered">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Run</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Alta</th>
                <th>Diagnóstico</th>
                <th>Establecimiento</th>
                <!--<th>Tuvo IAAS</th>-->
            </tr>
            </thead>
            <tbody>

            @foreach ($DatosHistoricos as $Datos)
            <tr>
                <td>{{$Datos->nombrep}}</td>
                <td>{{$Datos->apellido_paterno,"&nbsp",$Datos->apellido_materno}}</td>
                <td>
                
                    @if($Datos->dv==10){{$Datos->rut."-"."K"}}
                    @else <a class='info-paciente' href="{{asset('busquedaIAAS/paciente/info/'.$Datos->id_paciente)}}">{{$Datos->rut."-".$Datos->dv}}</a>
                    @endif
                </td>
                <td>{{$Datos->fecha_ingreso}}</td>
                <td>{{$Datos->fecha_termino}}</td>
                <td>{{$Datos->id_cie_10}} {{$Datos->diagnostico}}</td>
                <td>
                  {{$Datos->nombre}}

                </td>
                <!--<td>
                    @if($Datos->iaas)Sí
                    @else No
                    @endif
                </td>-->
            </tr>
            @endforeach
            </tbody>
    </table>
    </div>

</fieldset>

<div class="row" style="margin-top: 20px;">
    <div id="informacion" class="col-md-12">
    </div>
</div>

@stop
