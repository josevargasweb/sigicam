<script>


     $(function() {
        //////////////////////////////////
        //REPORTE MENSUAL CATEGORIZACION//
        //////////////////////////////////        

        $("#formMensual").on("submit", function(){
            var valor = $("#fecha-grafico-informe-mensual").val();
            if(valor == ""){
               swalWarning.fire({
                title: 'Información',
                text:"Debe seleccionar una fecha"
                });
            }else{
                var mes = $("#fecha-grafico-informe-mensual").datepicker('getDate').getMonth()+1;
                var anno = $("#fecha-grafico-informe-mensual").datepicker('getDate').getFullYear();

                $("#anno_informe_mensual").val(anno);
                $("#mes_informe_mensual").val(mes);
            }
        });
     });
    
</script>

<fieldset>
    <legend>Informe mensual de categorización</legend>
    <div class="col-sm-12">
        <div class="col-sm-12">
            <label>Seleccione fecha</label>
        </div>
        
        {{Form::open(["url"=>asset('estadisticas/informeMensualCategDatos') , "method"=>"GET", "id"=>"formMensual"])}}
            <div class="col-sm-2 form-group">
                <input type="text" id="fecha-grafico-informe-mensual" class="form-control fecha-grafico" required value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
            </div>

            <input type="hidden" name="anno" id="anno_informe_mensual" >
            <input type="hidden" name="mes" id="mes_informe_mensual" >
            
            @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
            <div class="col-sm-6 form-group">
                {{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento_informe_mensual')) }}
            </div>
            @else
                {{ Form::hidden('establecimiento', Session::get("usuario")->establecimiento, array('id' => 'establecimiento_informe_mensual')) }}
            @endif
            <input type="submit" value="Exportar" class="btn btn-primary" style="font-size: 14px; color: white !important;">
        {{Form::close()}}
    </div>
</fieldset>