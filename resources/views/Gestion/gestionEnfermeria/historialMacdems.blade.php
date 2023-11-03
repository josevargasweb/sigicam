@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("script")

    @include('Gestion.gestionEnfermeria.partials.scriptMacdems')
    <script>
        $(document).ready(function(){
            
            historial = $("#escalaMacdems").dataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
                },
            });

            $.ajax({
                url: "{{URL::to('gestionEnfermeria/buscarHistorialEscalaMacdems')}}",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"idCaso": {{$caso}}},
                dataType: "json",
                type: "post",
                success: function(data){
                    if(data.error){
                        console.log("error: no se encuentran datos");
                    }
                    console.log(data);
                    historial.fnClearTable();
                    if(data.length !=0) historial.fnAddData(data);
                },
                error: function(error){
                    console.log(error);
                }
            });
        });

        function editar(id_formulario_escala_macdems){
            id = id_formulario_escala_macdems;
            $.ajax({
                url: "{{URL::to('gestionEnfermeria/editarEscalaMacdems/')}}"+"/"+id,                           
                headers: {                                 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
                },                            
                type: "get",                            
                dataType: "json",                            
                success: function(data){
                    console.log(data);
                    $("#caidas_previas_macdems").val(data.datos.caidas_previas).change();
                    $("#edad").val(data.datos.edad).change();
                    $("#antecedentes").val(data.datos.antecedentes).change();
                    criterio_conciencia = (data.datos.criterio_compr_conciencia == true) ? 1 : 0;
                    $("#compr_conciencia").val(criterio_conciencia).change();
                    $("#id_formulario_escala_macdems").val(data.datos.id).change();
                    $("#legendMacdems").hide();
                    $("#btnVolverMacdems").hide();
                    $("#btnescalamacdems").val("Editar Información");          
                },                            
                error: function(error){                                
                    console.log(error);                            
                }                        
            });  
            $('#modalFormMacdems').modal('show');
        }
    </script>
@stop

@section('section')

<div class="container">
    <fieldset>
        <a href="javascript:history.back()" class="btn btn-primary">Volver</a>

        <div class="row">
            <div class="col-md-12" style="text-align:center;"><h4>Historial Escala Macdems</h4>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{ HTML::link(URL::route('pdfHistorialMacdems', [$caso]), 'Historial PDF', ['class' => 'btn btn-danger']) }}
                </div>
            </div>
            <div class="col-md-12">
                <br>
                Nombre Paciente: {{$paciente}}
            </div>
        </div>

        <table id="escalaMacdems" class="table  table-condensed table-hover">
            <thead>
                <tr style="background:#399865;">
                    <th>Opciones</th>
                    <th>Usuario aplica</th>
                    <th>Fecha aplicación</th>
                    <th>Total</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </fieldset>
</div>

<div class="modal fade modalFormMacdems" tabindex="-1" role="dialog" aria-labelledby="modalFormMacdems" aria-hidden="true" id="modalFormMacdems">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 align="center" class="modal-title" id="myModalLabel">Escala Macdems</h4>
                    </div>
                    <div class="modal-body">
                        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaMacdemsform')) }}
                        <input type="hidden" value="En Curso" name="tipoFormMacdems" id="tipoFormMacdems">
                        {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoMacdems')) }}
                        <input type="hidden" value="" name="id_formulario_escala_macdems" id="id_formulario_escala_macdems">
                            <br>
                            @include('Gestion.gestionEnfermeria.partials.FormMacdems')
                        {{ Form::close()}}
                    </div>

            </div>
            </div>
          </div>
        </div>
@stop
