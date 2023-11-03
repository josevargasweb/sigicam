<br>
<fieldset>
    <legend>RESUMEN PLANIFICACIÓN DE INDICACIONES</legend>
    <div class="col-md-8">
        <div class="form-group">
            <div class="col-md-2">
                <label class="control-label">Mes indicación</label>
            </div>                
            <div class="col-md-3">
                <input class="form-control dpfechaind" type="text" name="fecha_mes_indicaciones" id="fecha_mes_indicaciones">
            </div>
            <div class="col-md-3">
                <button id="generarPdfIndicaciones" class="btn btn-danger">PDF Indicaciones</button>
            </div>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</fieldset>

<script>

    function avisoFecha(){
        swalError.fire({
            title: 'Error',
            text:"Debe ingresar una fecha"
        });
    }

    $("#generarPdfIndicaciones").click(function(){
        var caso = "{{$caso}}";
        var fecha = $("#fecha_mes_indicaciones").val();
        console.log("fecha: ",fecha);
        if(fecha){
            var fecha2 = "01-" + fecha;
            console.log("lleno: ",fecha);
            window.location.href = "{{url('gestionMedica')}}"+"/"+caso+"/pdfResumenIndicaciones/"+fecha2;
        }else{
            console.log("vacio: ",fecha);
            avisoFecha();
        }
        // var ruta = window.location.href;
        // var ruta = ruta + "/pdfResumenIndicaciones";
        // window.open(ruta, "_blank");
    });

    $("#fecha_mes_indicaciones").on("keyup", function(){
        value = $(this).val();
        if(!$(this).val()){
            avisoFecha();
        }
    });

    $(".dpfechaind").datetimepicker({
        format: 'MM-YYYY',
        locale: 'es'
    }).on('change', function (e){
        $(this).change();
    });

    $('.dtp_fechas').datetimepicker({
        format: 'DD-MM-YYYY HH:mm',
        locale: 'es'
        }).on('dp.change', function (e) {
        $(this).change();
    });
</script>