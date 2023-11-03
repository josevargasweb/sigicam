@section("script")
@stop
<div class="panel panel-default">
    {{ Form::open([
        'url' => URL::route("generarExcel"),
        "id" => "form-datos-evolucion",
        "method" => "POST",
    ]) }}
    <fieldset>
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-4">Revise los datos y exporte los datos a XLS.</div>
                <div class="col-md-2">{{ Form::submit('Exportar todos los servicios a XLS', array("class" => "btn btn-default", "style" => "font-size: 14px; color: #399865 !important;")) }}</div>
                <div class="col-md-6"></div>
            </div>
        </div>


        <div role="tabpanel" id="tab-resultados" >
            <ul class="nav nav-tabs" role="tablist">
               @section("tabs")
               @show
            </ul>

            <div class="tab-content">
                @section("contenido")
                @show
            </div>
        </div>

    </fieldset>
    {{ Form::close() }}
</div>