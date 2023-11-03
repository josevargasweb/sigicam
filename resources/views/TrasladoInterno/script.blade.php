<script>

   

    $(function(){
        $("#solicitudTrasladoInterno").collapse();
        generarTablaEnCurso();
        //  
        $( "#enCurso" ).click(function() {
            if (typeof tableEnCurso !== 'undefined') {
                tableEnCurso.api().ajax.reload();
            }else{
                generarTablaEnCurso();
            }
        });

        $( "#aceptada" ).click(function() {
            if (typeof tableAceptado !== 'undefined') {
                tableAceptado.api().ajax.reload();
            }else{
                generarTablaAceptado();
            }
        });

        $( "#rechazado" ).click(function() {
            if (typeof tableRechazado !== 'undefined') {
                tableRechazado.api().ajax.reload();
            }else{
                generarTablaRechazado();
            }
        });
    });
</script>