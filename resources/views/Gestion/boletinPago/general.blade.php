<style>
    .subtitulos {
        font-size: 10px;
    }

    .box3{
        display: inline-block;
    }
</style>

<div id="idCasoBoletin">

</div>

<div class="form-group col-md-12">
    <ul class="nav nav-tabs">
        <li class="nav active"><a href="#ResumenPago" data-toggle="tab">Resumen de Pago</a></li>
        <li class="nav "><a href="#ProductoModificado" data-toggle="tab">Productos modificados</a></li>                
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade in active" style="padding-top:10px;" id="ResumenPago">

            <div class="form-group col-md-12">
                <p class="subtitulos" align="left" ><b>NOTAS DE CARGO DE PACIENTE HOSPITALIZADO</b></p>
                <div class="table-responsive">
                <table id="resumen_productos" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Valor Total</th>
                        <th>Usuario Asigna</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>
                </div>
            </div>

        </div>

        <div class="tab-pane fade" style="padding-top:10px;" id="ProductoModificado">

            <div class="form-group col-md-12">
                <p class="subtitulos" align="left" ><b>PRODUCTOS MODIFICADOS</b></p>
                <div class="table-responsive">
                <table id="resumen_productos_modificados" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Valor Total</th>
                        <th>Usuario Modifica</th>
                        <th>Tipo y Fecha Modificación</th>
                    </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>

</div>

<div id="boletinDiarioPdf" class="col-md-12">
    <div class="col-md-6">
	
        <div class="col-md-3" style="text-align:center;">
            <label for="">Ingrese fecha</label>
        </div>
        <div class="col-md-3" style="text-align:center;">
            {{Form::text('fechaBoletin', null, array('id' =>'fechaBoletin' , 'class' => 'form-control fechaBoletinTabla'))}}
        </div>
        <button class="btn btn-danger" type="button" id="btnGenerarBoletin">Generar Pdf</button>
        <button class="btn btn-success" type="button" id="btnGenerarBoletinExcel">Generar Excel</button>
    </div>

    <div class="col-md-6">
        <button class="btn btn-danger" type="button" id="btnGenerarBoletinHistorico">Generar Pdf Historico</button>
        <button class="btn btn-success" type="button" id="btnGenerarBoletinHistoricoExcel">Generar Excel Historico</button>
    </div>
</div>
{{ Form::open(array('url' => 'añadirProducto', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'añadirProductoForm', 'autocomplete' => 'off')) }}

<input type="text" name='idCaso' id="idCasoProducto" value = '0' hidden>
<div class="col-md-12">
    <br>
    <p class="subtitulos" align="left" ><b>INGRESO DE INSUMO, SUERO O MEDICAMENTO.</b></p>
    <table id="tablaProductos" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><p class="subtitulos" align="left"> Nombre:</p></th>
                <th><p class="subtitulos" align="left"> Código</p></th>
                <th><p class="subtitulos" align="left"> Cantidad</p></th>
                <th><p class="subtitulos" align="left"> Valor Total</p></th>
                <th><p class="subtitulos" align="left"> Fecha</p></th>
            </tr>
        </thead>
        <tbody id="productos_new">
            <tr id="fileTemplate"> 
                <td style='width: 30%;' class="productos"> 
                    <div class="form-group col-md-12">
                        <input type='text' name='nombre' class='form-control typeahead infoProductos' id="nombreProducto">  
                        <input type='text' name='id_producto' class='id_producto infoProductos' hidden id="idProducto"> 
                        <input type='text' name='id_boletin_producto' class='idBoletinProducto infoProductos' hidden>  
                        <input type='text' class='infoProductos' id="valor_unitario_producto" hidden>  
                    </div>  
                </td> 
                <td style='width: 15%;'> 
                    <div class="form-group col-md-12">
                        <input type='text' name='codigo' class='form-control infoProductos codigo_producto' disabled> 
                    </div>
                </td> 
                <td style='width: 15%;'> 
                    <div class="form-group col-md-12">
                        <input type='text' name='cantidad' class='form-control infoProductos' id="cantidad">  
                    </div>
                </td> 
                <td style='width: 15%;'> 
                    <div class="form-group col-md-12">
                        <input type='text' name='valor' class='form-control infoProductos' id="valor" disabled> 
                    </div>
                </td> 
                <td style='width: 25%;'> 
                    <div class="form-group col-md-12">
                        <input type='text' name='fecha' class='form-control infoProductos fecha-sel-Producto' id="fecha_sel_Producto"> 
                    </div>
                </td> 
            </tr>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-3" style="text-align: left;">
            {{-- <div class="btn btn-success" type="submit" id="guardar" ></div> --}}
            <button type="submit" class="btn btn-primary">Añadir</button>
        </div>    
        {{-- <div class="col-md-offset-6 col-md-3" style="text-align: right;">
            <div class="btn btn-primary" id="addProducto" >+ Productos</div>
        </div>    --}} 
    </div>
    
</div>
{{ Form::close() }}

