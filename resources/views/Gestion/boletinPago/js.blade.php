    
    var tableProducto = $("#resumen_productos").DataTable();
    var tableProductoModificados = $("#resumen_productos_modificados").DataTable();

    //funcion eliminar productos
    function eliminarProducto(esto,idProducto) {

        bootbox.confirm({
            message: "<h4>¿Desea eliminar este producto de la lista?</h4>",
            buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                
                if(result){
                    //Si la respuesta fue positiva, se procedera a editar los campos del producto y recargar la tabla
                    $.ajax({
                        url: "{{ URL::to('/')}}/eliminarProducto",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            producto: idProducto
                        },
                        type: "post",
                        dataType: "json",
                        success: function(data){
                            var nFilas = $("#resumen_productos >tbody >tr").length;
                            var id_fila = $(esto).parent().parent().index();

                            var eliminar = id_fila+1;
                            /* console.log("id_fila:",id_fila);
                            console.log("eliminar",eliminar);
                            console.log("n_filas ",nFilas); */
                            
                            if(nFilas != 1){
                                document.getElementById("resumen_productos").deleteRow(eliminar);
                            }
                        },
                        error: function(error){
                            swalError.fire({
                                title: 'Error',
                                text: 'Error al eliminar producto'
                            });
                            console.log(error);
                        }
                    });
                }
            }
        });
        
    }

    //funcion cargar producto para editar
    function editarProducto(esto,idProducto) {
        
        $.ajax({
            url: "{{ URL::to('/')}}/cargarProducto/"+idProducto,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                console.log(data);
                $("#nombreProducto").val(data.nombre);
                $("#idProducto").val(data.idProducto);
                $("#valor_unitario_producto").val(data.valorUnitario);
                $(".codigo_producto").val(data.codigo);
                $("#cantidad").val(data.cantidad);
                $("#valor").val(data.valor);
                $(".fecha-sel-Producto").val(data.fecha);
                $(".idBoletinProducto").val(data.idBP);
            },
            error: function(error){
                swalError.fire({
                    title: 'Error',
                    text: 'Error al editar producto'
                });
                console.log(error);
            }
        });

    }

    //clic al hacer en boletin de pago
    var boletinPago=function(idCaso){
        $("#modalBoletinPago").modal();
        $("#idCasoProducto").val(idCaso);
        
        /* linkBoletin = "<a href='../exportrBoletinPDF/"+idCaso+"' class='btn btn-danger'>Generar Pdf</a> <br>"; */

        tableProducto.destroy();
        //tabla deproductos en el paciente
        tableProducto = $("#resumen_productos").DataTable({
            //"iDisplayLength": 4,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: '../obtenerListaProductos' ,
                type: 'GET',
                data:{
                    id : idCaso
                }
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });

        tableProductoModificados.destroy();
        //tabla deproductos en el paciente
        tableProductoModificados = $("#resumen_productos_modificados").DataTable({
            //"iDisplayLength": 4,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: '../obtenerListaProductosModificados' ,
                type: 'GET',
                data:{
                    id : idCaso
                }
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });

        /* $("#boletinDiarioPdf").html(linkBoletin); */

        
    } 

    //funcion refrescar productos
    var refrescarTabla=function(){
        //recargar tabla
        tableProducto.ajax.reload();
        //limpiar campos
        $(".infoProductos").val('');
    }

    $( document ).ready(function() {

        var fecha = $(".fecha-sel-Producto").datetimepicker({
            locale: "es",
            format: "DD-MM-YYYY",
            maxDate: moment().format("YYYY-MM-DD")
        });
        
        
        $(".fechaBoletinTabla").datetimepicker({
            locale: "es",
            format: "DD-MM-YYYY",
            maxDate: moment().format("YYYY-MM-DD")
        });

        

        $("#btnGenerarBoletinHistorico").click(function(){
            var caso = $("#idCasoProducto").val();
            window.location.href = "{{url('exportarBoletinHistoricoPDF')}}"+"/Historico/"+caso;
        });

        $("#btnGenerarBoletinHistoricoExcel").click(function(){
            var caso = $("#idCasoProducto").val();
            window.location.href = "{{url('exportarBoletinHistoricoExcel')}}"+"/Historico/"+caso;
        });

        //Generar boletin
        $("#btnGenerarBoletin").click(function(){
			fechaComprobar = $("#fechaBoletin").val();
			if(fechaComprobar == ""){
                swalError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una fecha'
                });
			}else{
                var caso = $("#idCasoProducto").val();
                $.ajax({
                    url: "{{ URL::to('/')}}/validarFechaBoletin",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "get",
                    dataType: "json",
                    data:{
                        fecha : fechaComprobar,
                        caso: caso
                    },
                    success: function(data){
                        console.log(data);
                        mensaje = data.message;
                        if(data.valid == true){
                            window.location.href = "{{url('exportarBoletinPDF')}}"+"/"+caso+"/"+fechaComprobar;
                        }else{
                            swalError.fire({
                                title: 'Error',
                                text: data.message
                            });
                        }
                        
                    },
                    error: function(error){
                        console.log(error);
                        return false;
                    }
                });
				
			}
        });
        
        $("#btnGenerarBoletinExcel").click(function(){
			fechaComprobar = $("#fechaBoletin").val();
			if(fechaComprobar == ""){
				swalError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una fecha'
                });
			}else{
                var caso = $("#idCasoProducto").val();
                $.ajax({
                    url: "{{ URL::to('/')}}/validarFechaBoletin",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "get",
                    dataType: "json",
                    data:{
                        fecha : fechaComprobar,
                        caso: caso
                    },
                    success: function(data){
                        console.log(data);
                        mensaje = data.message;
                        if(data.valid == true){
                            window.location.href = "{{url('exportarBoletinExcel')}}"+"/"+caso+"/"+fechaComprobar;
                        }else{
                            swalError.fire({
                                title: 'Error',
                                text: data.message
                            });
                        }
                        
                    },
                    error: function(error){
                        swalError.fire({
                            title: 'Error',
                            text: 'Error al generar Excel'
                        });
                        console.log(error);
                        return false;
                    }
                });
				
			}
		});
        

        $(".fecha-sel-Producto").on("dp.change", function(){
            $("#añadirProductoForm").bootstrapValidator("revalidateField", "fecha");
        });
        
        
        //detectar cierre de modal 
        $("#modalBoletinPago").on('hidden.bs.modal', function () {
            //limpiar modal
            $(".infoProductos").val('');
        });

        //detectar cambios de cntidad y clcular nuevo valor totl

        $( "#cantidad" ).keyup(function() {
            console.log($("#valor_unitario_producto").val());
            console.log($("#cantidad").val());
            valorTmp= $("#valor_unitario_producto").val()*$("#cantidad").val();
            $("#valor").val(valorTmp); 
        });

        //typeahead de productos
        var datos_productos = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '{{URL::to('/')}}/'+'%QUERY/consulta_productos',
                wildcard: '%QUERY',
                filter: function(response) {
                    return response;
                }
            },
            limit: 50
        });
    
        datos_productos.initialize();
    
        $('.productos .typeahead').typeahead(null, {
          name: 'best-pictures',
          display: 'nombre',
          source: datos_productos.ttAdapter(),
          limit: 50,
          templates: {
            empty: [
              '<div class="empty-message">',
                'No hay resultados',
              '</div>'
            ].join('\n'),
            suggestion: function(data){
                //console.log(data.nombre_apellido);
                var nombres = data;
                //console.log(data);
                codigo = (data.codigo != null)?data.codigo:"Sin código";
                return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre+"</b></span><span class='col-sm-4'><b>"+codigo+"</b></span></div>"
            },
            header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre producto</span><span class='col-sm-4' style='color:#1E9966;'>Código</span></div><br>"
          }
        }).on('typeahead:selected', function(event, selection){
            if(selection.id != null){
                //$(this).parent().parent().find(".id_producto").val(selection.id);
                $(".id_producto").val(selection.id);
                $(".codigo_producto").val(selection.codigo);
                $("#valor_unitario_producto").val(selection.valor);
            }else{
                $(".infoProductos").val('');
            }
        }).on('typeahead:close', function(ev, suggestion) {
            //var $pro=$(this).parents(".productos").find("input[name='id']");
            var $pro = $(this).parent().parent().find(".id_producto");
            console.log(suggestion);
            console.log(ev);
            if(!$pro.val()&&$(this).val()){
                $(".infoProductos").val('');
                console.log("limpiado?");
            } 
        });

        //Guardar productos
        $("#añadirProductoForm").bootstrapValidator({
            excluded: [],
            fields: {
                nombre: {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar nombre del producto'
                        }
                    }
                },
                cantidad: {
                    validators:{
                        integer: {
                            message: 'Debe ingresar solo números'
                        },
                        notEmpty: {
                            message: 'Debe ingresar cantidad del producto'
                        }
                    }
                },
                valor: {
                    validators: {
                        integer: {
                            message: 'Debe ingresar solo números'
                        },
                        notEmpty: {
                            message: 'Debe ingresar valor al producto'
                        }
                    }
                },
                fecha:{
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una fecha'
                        },
                        remote:{
                            data: function(validator){
                                return {
                                    fecha: $("#fecha_sel_Producto").val(),
                                    caso: $('#idCasoProducto').val()
                                };
                            },
                            url: "{{ URL::to('/validarFechaBoletin') }}"
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            evt.preventDefault(evt);
            bootbox.confirm({
				message: "<h4>¿Está seguro de querer ingresar este producto al paciente?</h4>",
				buttons: {
					confirm: {
						label: 'Si',
						className: 'btn-success'
					},
					cancel: {
						label: 'No',
						className: 'btn-danger'
					}
				},
				callback: function (result) {
                    
					if(result){
                        var $form = $(evt.target);

						$.ajax({
							url: "{{ URL::to('/')}}/ingresarProducto",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data: $form.serialize(),
							type: "post",
							dataType: "json",
							success: function(data){
                                console.log(data.editable);
                                if(data.editable){
                                    console.log(data);
                                    bootbox.confirm({
                                        message: "<h4>"+data.editable+"</h4>",
                                        buttons: {
                                            confirm: {
                                                label: 'Si',
                                                className: 'btn-success'
                                            },
                                            cancel: {
                                                label: 'No',
                                                className: 'btn-danger'
                                            }
                                        },
                                        callback: function (result) {
                                            
                                            if(result){
                                                //Si la respuesta fue positiva, se procedera a editar los campos del producto y recargar la tabla
                                                $.ajax({
                                                    url: "{{ URL::to('/')}}/editarProducto",
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    data: $form.serialize(),
                                                    type: "post",
                                                    dataType: "json",
                                                    success: function(data){
                                                        swalExito.fire({
                                                            title: 'Exito!',
                                                            text:'Producto editado correctamente',
                                                            didOpen: function() {
                                                                setTimeout(function() {
                                                                    refrescarTabla();
                                                                }, 2000)
                                                            },
                                                        });
                                                        
                                                    },
                                                    error: function(error){
                                                        swalError.fire({
                                                            title: 'Error',
                                                            text: 'Error al editar producto'
                                                        });
                                                        console.log(error);
                                                    }
                                                });
                                            }
                                        }
                                    });

                                }else{
                                    swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                                refrescarTabla();
                                            }, 2000)
                                        },
                                    });
                                }
							},
							error: function(error){
                                swalError.fire({
                                    title: 'Error',
                                    text: 'Error al ingresar producto'
                                });
								console.log(error);
							}
						});
					}
				}
			});
        });

        //añadir productos
        /* $("#addProducto").click(function(){
            //primera forma de a;adir
            //html = "<tr> <td style='width: 13%;'> <input type='text' name='nombre[]' class='form-control'>  <input type='text' name='id[]' class='form-control' >  </td> <td style='width: 13%;'> <input type='text' name='cantidad[]' class='form-control'>  </td> <td> <input type='text' name='fecha[]' class='form-control'> </td> <td> <input type='text' name='codigo[]' class='form-control'> </td> <td> <input type='text' name='valor[]' class='form-control'> </td> <td> <button class='btn btn-danger' type='button' onclick='eliminarProducto(this)'> <span class='glyphicon glyphicon-trash'></span> Eliminar </button> </td> </tr>";

            //$( "#productos_new" ).append(html);

            //añadir segunda forma por clone
            var $template = $('#fileTemplate');
            var $clone =  $template.clone().removeClass('hide').insertBefore($template);
            //var $clone =  $template.clone().removeClass('hide').insertBefore($template);

            //console.log($clone.find("input"));
            //var el = $("<input type='text' name='retrun-order-invoice_no' class='return-order-invoice_no'>").insertAfter($('#fileTemplate'));

            $clone.find("input").eq(2).val("");
           
            invoice_no_setup_typeahead($clone.find("input")[1],$clone.find("input")[2]);
            //$(this).prop("disabled", true);

        }); */


        //funcion agregar productos extras quedara deshabilitada mientras no consiga encontrar el error
        /* function invoice_no_setup_typeahead(self, self2) {
            var datos_productos = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '{{URL::to('/')}}/'+'%QUERY/consulta_productos',
                    wildcard: '%QUERY',
                    filter: function(response) {
                        return response;
                    }
                },
                limit: 50
            });
        
            datos_productos.initialize();
        
            $(self).typeahead(null, {
              name: 'best-pictures',
              display: 'nombre',
              source: datos_productos.ttAdapter(),
              limit: 50,
              templates: {
                empty: [
                  '<div class="empty-message">',
                    'No hay resultados',
                  '</div>'
                ].join('\n'),
                suggestion: function(data){
                    //console.log(data.nombre_apellido);
                    var nombres = data;
                    //console.log(data);
                    return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre + " - " + data.codigo +"</b></span><span class='col-sm-4'><b>"+data.tipo+"</b></span></div>"
                },
                header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre producto - código</span><span class='col-sm-4' style='color:#1E9966;'>Tipo</span></div><br>"
              }
            }).on('typeahead:selected', function(event, selection){
                console.log(event);
                //$("#medico").val('asdas');
                console.log(selection.id);
                console.log($(this).parent().parent().find(".id_producto"));
                //console.log(event.parent());
                // console.log($("[name='id_producto']"));
                //$(self2).val(selection.id)
                $(this).parent().parent().find(".id_producto").val(selection.id);
                //$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
            }).on('typeahead:close', function(ev, suggestion) {
              console.log('Close typeahead: ' + suggestion);
              //var $pro=$(this).parents(".productos").find("input[name='id']");
              var $pro = $(this).parent().parent().find(".id_producto");
    
              if(!$pro.val()&&$(this).val()){
                  $(this).val("");
                  $pro.val("");
                  $(this).trigger('input');
                  console.log("limpiado?");
              } 
            });
        } */
    

    });



