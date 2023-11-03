<script>
    function generarTablaHistoricaSegmentario(){
        tHistoricoSegmentario = $("#tablaHistoricoSegmentario").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "order": [[0, 'desc']],
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerHistoricoSegmentario/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    $(document).ready(function(){
        $( "#IeHs" ).click(function() {
            if (typeof tHistoricoSegmentario !== 'undefined') {
                tHistoricoSegmentario.api().ajax.reload();
            }else{
                generarTablaHistoricaSegmentario();
            }
        });
    });
</script>

<div class="container">
    <legend>Histórico Examen Físico Segmentario</legend>
    {{-- <br> --}}
    {{ HTML::link("gestionEnfermeria/$caso/obtenerHistorialIngresoEnfermeria", 'Histórico', ['class' => 'btn btn-danger']) }}
    <br><br> 
    <table id="tablaHistoricoSegmentario" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>FECHA CREACIÓN</th>
                <th>FECHA MODIFICACIÓN</th>
                {{-- <th>TIPO MODIFICACIÓN</th> --}}
                <th>CABEZA / PROTESIS DENTAL</th>
                <th>CUELLO / TORAX / ABDOMEN</th>
                <th>EXTREMIDADES</th>
                <th>COLUMNA Y DORSO</th>
                <th>GENITALES / PIEL</th>
                <th>USUARIO</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>