$("#select-motivo-liberacion").on("change", function(){
    var value=$(this).val();
    $.ajax({
        url: '{{URL::to("getEspecificarAlta")}}',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { "tipo-alta": value },
        dataType: "json",
        type: "get",
        success: function(data){
            $("#motivo-liberacion").empty();
            $("#motivo-liberacion").html(data.data);
        },
        error: function(error){
            console.log(error);
        }
    });
});


$("#modalAllta").on('hidden.bs.modal', function () {
    $('#formDarAlta').bootstrapValidator('resetForm', true);   
});

