@extends("Templates/template")

@section("titulo")
Gestión de usuario
@stop


@section("script")
<script>
"use strict";
$(document).ready(function() {
    $("#documentoMenu").collapse();
    let tbArchivosSettings = {
        "bJQueryUI": true,
        "language": {
            "sSearch":         "Buscar",
            "oPaginate":{
                "sFirst":      "Primero",
                "sLast":       "Último",
                "sNext":       "Siguiente",
                "sPrevious":   "Anterior"
            },
            "sZeroRecords":    "No se encontraron resultados",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sLoadingRecords": "Cargando...",
                "sEmptyTable":     "No hay datos",
                "sLengthMenu":     "Mostrar _MENU_ registros",
        }
    };

    let tbArchivosApi = $("#tb-archivos").DataTable(tbArchivosSettings);
    
    function setearEventoClickBotones() {
        $(".remove-btn button").unbind('click').click(function() {
            let btnDelete = $(this);
            let contenedor = btnDelete.parent().parent();

            $("#modalConfirmar").modal('toggle');

            $("#btnSi").unbind('click').click(function() {

                $.ajax({
                    url: "eliminar",
                    type: "GET",
                    data: {nroDocumento: contenedor.attr("id-file")},
                    success: function(data) {
                        if(data) {
                            $("#alert-archivo-subido").show("slow");
                            setTimeout(function() { $("#alert-archivo-subido").hide("slow"); }, 5000);
                            $("#tb-archivos").dataTable().api().row(btnDelete.parent().parent()).remove().draw();
                        }
                    }
                });

                btnDelete.parent().parent().remove();
            });
        });

        $(".view-btn button").click(function() {
            let contenedor = $(this).parent().parent();
            let modalArchivo = $("#modalVerArchivo");
            let idArchivo = contenedor.attr("id-file");

            // obteniendo info del archivo
            $.get(idArchivo+"/link", function(file) {
                let url = window.location.origin+file.link;
                // google viewer
                let pattern = /\.(doc|docx|xls|xlsx|pdf|ppt|pptx)/;
                if(pattern.test(file.ext)) { url = "http://docs.google.com/gview?url="+encodeURIComponent(url)+"&embedded=true"; }

                // configurando modal
                $("#iframeArchivo").attr("src", url);
                $("#modalArchivoTitle").text( file.name+file.ext );
            });

            modalArchivo.modal("toggle");
        });
    }

    // traer archivos para tabla
    function poblarTabla() {
        tbArchivosApi.clear();

        $.get("listar", function(data) {
            console.log(data);
            for(let file of data) {
                // piezas
                let i = $("<i />");
                let a = $("<a />");
                let button = $("<button />", {class: "btn btn-default"});
                let td = $("<td />");
                let tr = $("<tr />");

                // armado
                let fila = tr.clone().attr('id-file', file.id);
                let nombre = td.clone().text(file.name+file.extension);
                let peso = td.clone().text(file.size);
                let fecha = td.clone().text(file.uploadDate);
                let removeBtn = td.clone().addClass('remove-btn').append(button.clone().attr('title', 'Eliminar Archivo').append(i.clone().addClass('glyphicon glyphicon-trash')));
                let downloadBtn = td.clone().addClass('download-btn').append(button.clone().attr('title', 'Descargar Archivo').append(a.clone().addClass('glyphicon glyphicon-download-alt').attr('target', '_blank').attr('href',file.id+'/descargar')));
                let viewBtn = td.clone().addClass('view-btn').append(button.clone().attr('title', 'Ver Archivo').append(i.clone().addClass('glyphicon glyphicon-eye-open')));
 
                fila.append( nombre );
                fila.append( peso );
                fila.append( fecha );
                //fila.append( viewBtn );
                fila.append( downloadBtn );
@if (Session::get("usuario")->tipo == TipoUsuario::ADMINSS )
                fila.append( removeBtn );
@endif
                tbArchivosApi.row.add( fila );
            }
            //file = null;
            tbArchivosApi.draw();
            setearEventoClickBotones();
        });
    }

    // subir archivos
    $("input[type=file]").fileinput();

    $("#upload").bootstrapValidator({
        excluded: ':disabled',
        fields: {
            file: {
                validators:{
                    notEmpty: {
                        message: 'El archivo es obligatorio'
                    }
                }
            }
        }
    }).on('status.field.bv', function(e, data) {
        $("#upload input[type='submit']").prop("disabled", false);
    }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        var $form = $(evt.target)[0];
        var data=new FormData($form);
        $("#loading").show();
        $.ajax({
            url: "subir",
            type: "post",
            dataType: "json",
            data: new FormData($form),
            cache: false,
            contentType: false,
            processData: false,
            success: function(data){
                if(data){
                    swalExito.fire({
						title: 'Exito!',
						text: "Archivo Subido correctamente",
						didOpen: function() {
							setTimeout(function() {
                                $("#resultado").html(data.contenido);
                                poblarTabla();
							}, 2000)
						},
						});
                } 
                if(data.error) {
                    swalError.fire({
                    title: 'Error',
                    text:data.error
                    });
                    console.log(data.msg);
                }
                $("#upload input[type='submit']").prop("disabled", false);
                $("#loading").hide();
            },
            error: function(error){
                console.log(error);
            }
        });
        return false;
    });
    //poblar tabla
    poblarTabla();

    


});
</script>
@stop

@section("miga")
<li><a class="cursor">Documentos</a></li>
<li><a href="{{Request::url()}}" class="cursor"><!--ACA IBA ALGO--></a></li>
@stop

@section("section")
<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado Archivos</a></li>
@if (Session::get("usuario")->tipo == TipoUsuario::ADMINSS )
        <li role="presentation"><a href="#subir" aria-controls="subir" role="tab" data-toggle="tab">Subir Archivos</a></li>
@endif
    </ul>

    <!-- Tab panes -->
    <div class="tab-content panel-body">
        <!-- listado de archivos -->
        <div role="tabpanel" class="tab-pane active" id="listado">
            <div class="row">
            <div id="alert-archivo-subido" class="alert alert-success alert-dismissible col-md-12 center-block" role="alert" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>¡Archivo borrado!</h4>
            </div>
            <div class="col-md-12">
                <table id="tb-archivos" class="table table-bordered tabla-consultas">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Tamaño</th>
                            <th class="fecha-hora">Fecha</th>
                            <!-- <th class="btn-container"></th> -->
                            <th class="btn-container"></th>
@if (Session::get("usuario")->tipo == TipoUsuario::ADMINSS )
                            <th class="btn-container"></th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- subir archivos -->

        <div role="tabpanel" class="tab-pane" id="subir">
            <fieldset>
                <legend>Subir archivo</legend>
                <div class="panel panel-default">
                    <div class="panel-body">
                    {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'upload', 'onsubmit' => 'return false', 'files'=> true)) }}
                    <div class="form-group col-md-12">
                        <label for="subir" class="col-sm-2 control-label">Subir archivo: </label>
                        <div class="col-sm-10">
                            <input type="file" name="file" class="file" title="Elegir archivo" data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-sm-2">
                            <input type="submit" value="Subir" class="btn btn-primary" />
                        </div>
                        <div id="loading" class="col-sm-10" style="display: none;">
                            <span>Subiendo archivo, por favor espere {{ HTML::image('img/loading.gif') }}</span>
                        </div>
                    </div>
                    {{ Form::close() }}
                    </div>
                </div>
            </fieldset>
            <div class="panel panel-default" id="resultado">
            </div>
        </div>

    </div>
</div>




@include("Documentos/ModalConfirmar", ['texto' => '¿Esta seguro de eliminar el archivo?'])
@include("Documentos/ModalArchivo")

@stop
