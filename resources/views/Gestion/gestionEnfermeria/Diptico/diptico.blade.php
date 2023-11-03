<script>
    var documentosDiptico = function(caso){
        $.ajax({
            url: "{{ URL::to('/documentosDiptico') }}",
            headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
            data: {caso: caso},
            dateType: "json",
            type: "post",
            success: function(response){
                $("#contenido").html(response.contenido);
            },
            error: function(error){
                console.log(error);
            }
        });
    }

     $(function() {
        var idcaso = "{{$caso}}";
        $("#caso").val(idcaso);

        $("#diptico").click(function(){
            documentosDiptico(idcaso);
        });
     });
</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

<br>
<div class="formulario">
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Dipticos y documentos</h4>
        </div>
        <div class="panel-body">
            <div id="contenido"></div>
        </div>
    </div>
</div>

