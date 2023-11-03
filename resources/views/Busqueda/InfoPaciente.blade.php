<div>
    <fieldset>
        {{--@if ( Session::get('idEstablecimiento') == $info['general']['id_estab'] )
            <a href="#">Editar datos paciente</a>
        @endif --}}
        <legend>Datos paciente</legend>
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>Run</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Fecha de nacimiento (edad)</th>
            </tr>
            </thead>
            <tbody>
                <td>{{{ $info['general']['rut'] }}}</td>
                <td>{{{ $info['general']['nombre'] }}}</td>
                <td>{{{ $info['general']['apellidos'] }}}</td>
                <td>{{ $info['general']['fecha_nacimiento'] }}</td>
            </tbody>
        </table>
        </div>

    </fieldset>
</div>

<div>
    <fieldset>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>Establecimiento</th>
                <th>Servicio</th>
                <th>Fecha de ingreso</th>
                <th>Fecha de hospitalización</th>
                @if($info['general']['salida'] == '')
                    
                    @else
                        <th>Fecha de salida urgencias</th>
                    @endif
                    <th>Diagnóstico</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{{ $info['general']['estab'] }}}</td>
                    <td>{{{ $info['general']['servicio'] }}}</td>
                    <td>{{{ date("d-m-Y H:i", strtotime($info['general']['fecha_ingreso'])) }}}</td>
                    @if($info['general']['fecha_hosp'] == '-')
                        <td>-</td>
                    @else
                        <td>{{{ date("d-m-Y H:i", strtotime($info['general']['fecha_hosp'])) }}}</td>
                    @endif
    
                    @if($info['general']['salida'] == '')
                    
                    @else
                        <td>{{{ date("d-m-Y H:i", strtotime($info['general']['salida'])) }}}</td>
                    @endif
                <td>{{{ $info['general']['diagnostico'] }}}</td>
            </tr>
            </tbody>
        </table>
        </div>

        {{-- @if ( Session::get('idEstablecimiento') == $info['general']['estab'] )
            <a href="#">Editar datos paciente</a>
        @endif --}}

        <div> 
            {!! $info['gestora'] !!} 
        </div> 
        <br> 
    </fieldset>
</div>
<div>
    <fieldset>
        
        @if(count($info['detalles']) == 0)
            <h4 style="text-align: center">Este paciente no posee casos de hospitalización</h4>
        @else 
            <legend>Casos</legend>
            <div class="panel-group" id="accordion" style="width:100%;">
                @foreach($info['detalles'] as $caso => $detalle)
                    @include("Busqueda/Casos", array(
                        "caso" => $detalle['caso'], 
                        "evoluciones" => $detalle['evoluciones'], 
                        "traslados" => $detalle['traslados'], 
                        "diagnosticos" => $detalle['diagnosticos'], 
                        /*"alta" =>$info['alta'],*/
                        "indicaciones" => $detalle['indicaciones'], 
                        "derivaciones" => $detalle['derivaciones'], 
                        "examenes" => $detalle['examenes'], 
                        "altas" => $detalle['altas']
                        ))
                @endforeach
            </div>
        @endif
        
    </fieldset>
</div>
<script>
    $(function(){
        $("html, body").animate({ scrollTop: $('#informacion').offset().top }, 1000);

              	//Mauricio//
        var datos_medicos = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicos'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '{{URL::to('/')}}/'+'%QUERY/consulta_medicos',
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
          display: 'nombre_apellido',
          source: datos_medicos.ttAdapter(),
          limit: 50,
          templates: {
          empty: [
            '<div class="empty-message">',
            'No hay resultados',
            '</div>'
          ].join('\n'),
          suggestion: function(data){
            var nombres = data;
            return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
          },
          header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
          }
        }).on('typeahead:selected', function(event, selection){
          $("#medico").val('asdas');
          $("[name='id_medico']").val(selection.id_medico);
          //$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
        }).on('typeahead:close', function(ev, suggestion) {
          var $med=$(this).parents(".medicos").find("input[name='id_medico']");
          if(!$med.val()&&$(this).val()){
            $(this).val("");
            $med.val("");
            $(this).trigger('input');
          }
        });

        $("#medicoDerivador").on('change keyup',function(){
            var me = $(this);
            if(!me.val()){
                me.val('');
                $("[name='id_medico']").val('');
            }
        });
    });
</script>
<!-- tabla categorización -->
<script>
$(function(){
	$(".tabla_categorizacion").DataTable({
		language: {
	        url: "{{asset('/js/spanish.txt')}}"
	    },
	    "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ]
	});
});
</script>
