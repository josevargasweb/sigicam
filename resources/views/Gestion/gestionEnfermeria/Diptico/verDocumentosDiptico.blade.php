<script>

    var eliminarDocumentoDiptico = function($id){
        $.ajax({
            url: "{{ URL::to('/quitarDocumentoDiptico')}}/"+$id,
            type: "post",
            dataType: "json",
            success: function (response) {
                if(response.exito){
                    swalExito.fire({
                        title: 'Exito!',
                        text: response.exito,
                        didOpen: function() {
                            setTimeout(function() {
                                $("#contenido").html(response.contenido.original.contenido);
                            }, 2000)
                        },
                    });
                }

                if(response.error){
                    swalError.fire({
                        title: 'Error',
                        text: response.error
                    });
                }
            },
            error: function (error){
                console.log(error);
            }
        });
    }

    $('#fileupload').fileupload({
        url: "{{ URL::to('/fileuploadDiptico') }}",
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<div/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));

            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
        /* ajax inser bd */
        $.ajax({
            url: "{{ URL::to('/ingresarDocumentoDiptico') }}",
            data: {"files":data.result.files,"caso":$("#caso").val()},
            type: "post",
            dataType: "json",
            success: function (response) {
                $("#contenido").html(response.contenido);
            },
            error: function (error) {

            }
        });

        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>').attr('target', '_blank').prop('href', file.url);
                $(data.context.children()[index]).wrap(link);
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index]).append('<br>').append(error);
            }
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index]).append('<br>').append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
        
</script>

<fieldset>
    <legend>Lista de dipticos y documentos</legend>
    <table id="tabla-documentos-paciente" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Dipticos y documentos</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($documentos as $documento)
            <tr>
                <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $documento->fecha_creacion)->format("d-m-Y H:i")}}</td>
                <td><a  href="{{URL::to('descargarDocumentoDiptico/'.$documento->id)}}">{{$documento->documento}}</a></td>
                <td><button class="btn btn-danger" onclick=eliminarDocumentoDiptico({{$documento->id}})>Eliminar</button></td>
            </tr>
            @empty
                <tr><td colspan="3" style="text-align: center;">Sin documentos</td></tr>
            @endforelse
        </tbody>
    </table>
    <br>
    <div class="form-group col-md-12" style="padding-top:25px">        
        {{ Form::hidden('caso', '', array('id' => 'caso')) }}
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Agregar archivos...</span>
            <input id="fileupload" type="file" name="files[]" multiple="">
        </span>
        <br>
        <div id="progress" class="progress">
            <div class="progress-bar progress-bar-success"></div>
        </div>
        <div id="files" class="files"></div>
        <br>
    </div>
</fieldset>