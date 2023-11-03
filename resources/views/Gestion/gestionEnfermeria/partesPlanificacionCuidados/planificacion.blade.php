<script>
</script>

<div  class="container" style="padding-left: 0px;">	

    <br>
    @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.programacionAtencion.atencionEnfermeria')
    @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.programacionAtencion.gestionIndicacionMedica')
    <!-- @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.programacionAtencion.indicacionMedica') -->

    

    {{-- <div class="tab-content clearfix">
        <br>
        <div class="col">
            @forelse ($curacionesHoy as $c)
                <div class="alert alert-info alert-dismissible fade in" role="alert"> 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span></button> <strong>Hoy corresponde una curación {{\Carbon\Carbon::parse($c->proxima_curacion)->format('d/m/Y')}}</strong>  
                </div>
            @empty
                <div class="alert alert-info alert-dismissible fade in" role="alert"> 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span></button> <strong>No hay curaciones para hoy</strong>  
                </div>
            @endforelse
        </div>
        
        <div class="col">
            <div class="alert alert-info alert-dismissible fade in" role="alert"> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> <strong>
                @forelse ($indicaciones as $i)
                     {{\Carbon\Carbon::parse($i->fecha_creacion)->format('d/m/Y H:i')}} - {{ $i->indicacion }}<br>
                @empty
                    <p>No hay indicaciones para hoy</p>
                @endforelse
                </strong>  
            </div>
        </div>
        
    </div> --}}
		
</div>


          
