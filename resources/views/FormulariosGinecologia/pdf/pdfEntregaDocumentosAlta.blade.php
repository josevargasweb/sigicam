<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
	legend{
		background-color:white;
		text-align:center;
		font-size:14px;
		border:none;
	}
	fieldset{
		border-style:solid;
		border-width:1px;
		border-color:black;
		padding:32px;
	}
	#documentacion_entregada{
		float:left;
		width:40%;
	}
	#acompañante_egreso{
		float:right;
		width:40%;
	}
	#observaciones{}
	.panel-heading{
		text-align:center;
	}
	
	.izquierda{
		float:left;
		width:50%;
	}
	.derecha{
		float:right;
		width:50%;
	}
	.limpiar{
		clear:both;
	}
	.margen{
		height:24px;
		margin:0px;
		padding:0px;
	}
	.margen2{
		height:48px;
		margin:0px;
		padding:0px;
	}
	.margen3{
		height:72px;
		margin:0px;
		padding:0px;
	}
	.firma{
		width:300px;
		margin:auto;
	}
	.raya_firma{
		border-style:solid;
		border-width:1px;
		border-color:black;
	}
	.text-center{
		text-align:center;
	}
	label{
		font-weight:bold;
	}
	.logo{
		display: inline-block;
		width:10%;
	}
	.logo img{
		width:100%;
	}
	.hospital{
		text-align:center;
		display:inline-block;
		float:right;
	}
	</style>
    <title>Entrega de documentos al alta 
</title>
<body>
	<div class="">
		<div class="logo">
			<img src="{{$formulario->logo_hospital}}">
		</div>
		<div class="hospital">
			
            <h1>{{$formulario->nombre_hospital}}</h1>
        </div>
        <div class="text-center">
            <h2>Entrega de documentos al alta</h2>
            <h4>Servicio de Obtetricia y Ginecología</h4>
        </div>

        <div class="panel-body">

			<div class="margen"></div>
            <div class="row">
                    <div class="izquierda">
                        <label for="entrega_documentos_alta_run">RUN:</label>
                        <span>{{$formulario->run}}-{{$formulario->dv}}</span>
                    </div>                
                	<div class="derecha">
                        <label for="entrega_documentos_alta_run">Nombre:</label>
                        <span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
                    </div>
                    <div class="limpiar"></div>


                    <div class="izquierda">
                        <label for="entrega_documentos_alta_ficha_clinica">Ficha clinica:</label>
                        <span>{{$formulario->ficha_clinica}}</span>
                    </div>                

                    <div class="derecha">
                        <label for="entrega_documentos_alta_fecha">Fecha:</label>
                        <span>{{$formulario->fecha}}</span>
                    </div>
					
					<div class="limpiar"></div>
					<div class="izquierda">
						<label>Servicio:</label>
						<span>{{$formulario->servicio}}</span>
					</div>
					<div class="limpiar"></div>
					<div class="izquierda">
						<label>Sala:</label>
						<span>{{$formulario->sala}}</span>
					</div>
					<div class="derecha">
						<label>Cama:</label>
						<span>{{$formulario->cama}}</span>
					</div>

            </div>
            <div class="limpiar margen"></div>
            
			<!-- DOCUMENTACIÓN -->
            <fieldset id="documentacion_entregada" class="row">
            	<legend><b>Documentaci&oacute;n entregada</b></legend>
            	<div class="row">

	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_epicrisis_medica">Epicrisis m&eacute;dica</label>
	                </div>
					@php
					$epicrisis_si = "";
					$epicrisis_no = "";
					$epicrisis_nc = "";
					if($formulario->epicrisis_medica == "si")
					{
						$epicrisis_si = "checked";
					}
					else if($formulario->epicrisis_medica == "no")
					{
						$epicrisis_no = "checked";
					}
					else if($formulario->epicrisis_medica == "n/c")
					{
						$epicrisis_nc = "checked";
					}
					@endphp
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="si" {{$epicrisis_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="no" {{$epicrisis_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="n/c" {{$epicrisis_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$carnet_si = "";
					$carnet_no = "";
					$carnet_nc = "";
					if($formulario->carnet_alta == "si")
					{
						$carnet_si = "checked";
					}
					else if($formulario->carnet_alta == "no")
					{
						$carnet_no = "checked";
					}
					else if($formulario->carnet_alta == "n/c")
					{
						$carnet_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_carnet_alta">Carnet de alta</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carnet_alta" value="si" {{$carnet_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carnet_alta" value="no" {{$carnet_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carnet_alta" value="n/c" {{$carnet_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$recetas_farmacos_si = "";
					$recetas_farmacos_no = "";
					$recetas_farmacos_nc = "";
					if($formulario->recetas_farmacos == "si")
					{
						$recetas_farmacos_si = "checked";
					}
					else if($formulario->recetas_farmacos == "no")
					{
						$recetas_farmacos_no = "checked";
					}
					else if($formulario->recetas_farmacos == "n/c")
					{
						$recetas_farmacos_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_recetas_farmacos">Recetas de f&aacute;rmacos</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="si" {{$recetas_farmacos_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="no" {{$recetas_farmacos_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="n/c" {{$recetas_farmacos_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$citaciones_control_si = "";
					$citaciones_control_no = "";
					$citaciones_control_nc = "";
					if($formulario->citaciones_control == "si")
					{
						$citaciones_control_si = "checked";
					}
					else if($formulario->citaciones_control == "no")
					{
						$citaciones_control_no = "checked";
					}
					else if($formulario->citaciones_control == "n/c")
					{
						$citaciones_control_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_citaciones_control">Citaciones a control</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="si" {{$citaciones_control_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="no" {{$citaciones_control_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="n/c" {{$citaciones_control_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$carne_identidad_si = "";
					$carne_identidad_no = "";
					$carne_identidad_nc = "";
					if($formulario->carne_identidad == "si")
					{
						$carne_identidad_si = "checked";
					}
					else if($formulario->carne_identidad == "no")
					{
						$carne_identidad_no = "checked";
					}
					else if($formulario->carne_identidad == "n/c")
					{
						$carne_identidad_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_carne_identidad">Carn&eacute; de identidad</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="si" {{$carne_identidad_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="no" {{$carne_identidad_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="n/c" {{$carne_identidad_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$comprobante_parto_si = "";
					$comprobante_parto_no = "";
					$comprobante_parto_nc = "";
					if($formulario->comprobante_parto == "si")
					{
						$comprobante_parto_si = "checked";
					}
					else if($formulario->comprobante_parto == "no")
					{
						$comprobante_parto_no = "checked";
					}
					else if($formulario->comprobante_parto == "n/c")
					{
						$comprobante_parto_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_comprobante_parto">Comprobante de parto</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="si" {{$comprobante_parto_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="no" {{$comprobante_parto_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="n/c" {{$comprobante_parto_nc}}>
	                        N/C
	                    </label>               
	                
	                </div>
	                
	            </div>
	
	            <div class="row">
					@php
					$carne_control_parental_si = "";
					$carne_control_parental_no = "";
					$carne_control_parental_nc = "";
					if($formulario->carne_control_parental == "si")
					{
						$carne_control_parental_si = "checked";
					}
					else if($formulario->carne_control_parental == "no")
					{
						$carne_control_parental_no = "checked";
					}
					else if($formulario->carne_control_parental == "n/c")
					{
						$carne_control_parental_nc = "checked";
					}
					@endphp
	                <div class="col-sm-3">
	                    <label class="radio-inline" for="entrega_documentos_alta_carne_control_parental">Carn&eacute; de control prenatal</label>
	                </div>
	
	                <div class="col-sm-9">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="si" {{$carne_control_parental_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="no" {{$carne_control_parental_no}}>
	                        No
	                    </label>                                        
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="n/c" {{$carne_control_parental_nc}}>
	                        N/C
	                    </label>               
	                </div>
	                
	            </div>
            </fieldset>

            <!-- ACOMPAÑANTE -->
			<fieldset id="acompañante_egreso" class="row">
				<legend><b>Egreso hospitalario acompa&ntilde;ado</b></legend>
				<div class="row">
				@php
				$egreso_hospitalario_si = "";
				$egreso_hospitalario_no = "";
				
				if($formulario->egreso_hospitalario_acompanado === true)
				{
					$egreso_hospitalario_si = "checked";
				}
				else if($formulario->egreso_hospitalario_acompanado === false)
				{
					$egreso_hospitalario_no = "checked";
				}
				@endphp
	                <div class="col-sm-12">
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_egreso_hospitalario_acompanado" value="si" {{$egreso_hospitalario_si}}>
	                        S&iacute;
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="entrega_documentos_alta_egreso_hospitalario_acompanado" value="no" {{$egreso_hospitalario_no}}>
	                        No
	                    </label>                                        
	              
	                </div>
	                
	            </div>
				@if($formulario->egreso_hospitalario_acompanado == "true")
	            <div class="row" id="entrega_documentos_alta_acompanante_container" >
	
	                <div class="col-md-12">
	                <br>
	                    <div class="col-md-6 form-group">
	                        <label for="entrega_documentos_alta_acompanante">¿Qui&eacute;n acompa&ntilde;a al paciente?</label>
	                        <br>
	                        <span>{{$formulario->quien_acompana_paciente}}</span>
	                    </div>                
	                </div>
	                
	            </div>
				@endif
			</fieldset>
			<div class="limpiar"></div>
            <!-- OBSERVACIONES -->
            <fieldset class="row" id="observaciones">
            	<legend><b>Observaciones</b></legend>
            
	            <div class="row">
	
	                <div class="col-md-12">
	                    <div class="col-md-6 form-group">
	                        <p >{{$formulario->observaciones}}</p>
	                    </div>                
	                </div>
	                
	            </div>
            </fieldset>
            
            <div class="margen3"></div>
            
            <div class="text-center">
            	<div class="firma">
	            	<hr class="raya_firma">
	            	<p>Firma conforme del paciente o su tutor legal responsable</p>
            	</div>
            </div>


        </div>
    </div>
</body>
</html>