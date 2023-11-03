<script>
    $(function() {
        $("#pdfResumenCuidados").click(function(){
            fecha_pdf = $("#fecha_pdf").val();
            caso = "{{$caso}}";
            if(fecha_pdf){
                window.location.href = "{{url('gestionEnfermeria/pdfResumenCuidados')}}"+"/"+caso+"/"+fecha_pdf;
            }else{
                swalError.fire({
                title: 'Error',
                text:"Debe ingresar una fecha"
                });
            }
        });

        $("#fecha_pdf").on("keyup", function(){
            value = $(this).val();
            if(!$(this).val()){
                  swalError.fire({
                title: 'Error',
                text:"Debe ingresar una fecha"
                });
            }
        });
    });
</script>

<br>
<fieldset>
    <legend>Seleccionar día </legend>
    <div>
        <label for="inicio">Inicio</label>
    </div>
    <div class="form-group col-md-2">
        <input id="fecha_pdf" class="form-control fecha-sel" style="margin-left: -17px;" type="text" value='{{\Carbon\Carbon::now()->format("d-m-Y")}}'>
    </div>
                    
    <div class="form-group">
        <button id="pdfResumenCuidados" class="btn btn-danger">PDF Planificación</button>
    </div>
</fieldset>