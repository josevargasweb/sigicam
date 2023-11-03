<!DOCTYPE HTML>
<html>
<head>
  <title>Título de la página</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


  {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> --}}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


  <style>

    .ley{
		visibility: hidden;
	}

	.campos {
		font-size: 9px;
		padding-bottom:1px;
	}

    .subtitulos {
        font-size: 8px;
    }

    .letra_chica {
        font-size: 8px !important;
    }

	.letra_diminuta {
        font-size: 5px !important;
    }

	.cuadrado {
		width:2.5vh;
		max-width:100px;
		height:2.5vh;
		max-height:100px;
		position:relative;
		background:green;
	}

	.cuadrado2 {
		width:3vh;
		max-width:100px;
		height:2.5vh;
		max-height:100px;
		position:relative;
		background:green;
	}

	.box3{
		display: inline-block !important;
	}

	.box4{
		display: inline-block !important;
		font-size: 9px;
		padding-bottom:1px;
	}

	.box5{
		font-size: 9px;
	}

	#num_egreso{
		width: 130px;
	}

	.formulario{
		height:20px;
		width: 150px !important;
	}

	.red{
		background: red;
	}
	.blue{
		background: blue;
	}

	.green{
		background: green;
	}

	.traslados{
		margin-top:7px;
	}

	.traslados2{
		margin-top:10px;
	}


	/* desde aqui tabla */

	table.blueTable {
	border: 1px solid #1C6EA4;
	background-color: #EEEEEE;
	width: 100%;
	text-align: left;
	border-collapse: collapse;
	}
	table.blueTable td, table.blueTable th {
	border: 1px solid #AAAAAA;
	padding: 3px 2px;
	}
	table.blueTable tbody td {
	font-size: 13px;
	}
	table.blueTable tr:nth-child(even) {
	background: #D0E4F5;
	}
	table.blueTable thead {
	background: #1C6EA4;
	background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
	background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
	background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
	border-bottom: 2px solid #444444;
	}
	table.blueTable thead th {
	font-size: 15px;
	font-weight: bold;
	color: #FFFFFF;
	border-left: 2px solid #D0E4F5;
	}
	table.blueTable thead th:first-child {
	border-left: none;
	}

	table.blueTable tfoot {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
	background: #D0E4F5;
	background: -moz-linear-gradient(top, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
	background: -webkit-linear-gradient(top, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
	background: linear-gradient(to bottom, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
	border-top: 2px solid #444444;
	}
	table.blueTable tfoot td {
	font-size: 14px;
	}
	table.blueTable tfoot .links {
	text-align: right;
	}
	table.blueTable tfoot .links a{
	display: inline-block;
	background: #1C6EA4;
	color: #FFFFFF;
	padding: 2px 8px;
	border-radius: 5px;
	}

  </style>

</head>
<body>


    <div class="row">
        <div class="box3" style="width: 25%">
            <p class="subtitulos">
                <b>
                    MINISTERIO DE SALUD <br>
                    DEPARTAMENTO DE ESTADÍSTICAS E <br>
                    INFORMACIÓN DE SALUD
                </b>
            </p>
        </div>

        <div class="box3" style="width: 45%">

            <h4 align="center"><u>Informe Estadístico de Egreso Hospitalario</u></h4>

			{{-- <h4 align="center">C.CORRIENTE: <b>{{$caso->cuenta_corriente}}</b></h4> --}}
		</div>

        <div class="box3 " style="width: 20%; margin-bottom:-40px;">
                <div class="box3" style="width:75px;padding-left 10px;">
                    <h5>N° EGRESO</h5>
                </div>
                <div class="box3" style="margin-top:35px;">

					<div style="border-style: solid; height:30px !important;">
					<p id="num_egreso" align="center">
						@if($informe_egreso)

							@if($informe_egreso->n_egreso)
								{{$informe_egreso->n_egreso}}
							@endif
						@endif
					</p>
					</div>


					<p class="letra_chica" align="center">
                        <b>
                            USO EXCLUSIVO UNIDAD DE ESTADÍSTICA
                        </b>
                    </p>
                </div>
        </div>
	</div>
	{{-- <br> --}}
	<div class="" style="height: 20px; !important">
		<div class="box3" style="width: 40%;">

			<div class="campos" style="border-style: solid; height: 11px !important; width:100% !important; " >
			<label id="" style="padding-left: 2px; padding-right: 2px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">{{$establecimiento->nombre}}</label>
			</div>
			<div align="left" class="subtitulos">
				<b>NOMBRE ESTABLECIMIENTO</b>
			</div>

		</div>


		<div class="box3" style="width: 16%;">

			<div class="campos" style="border-style: solid; height: 11px !important; text-align: center; " id="cod_establecimiento">
				{{$establecimiento->codigo}}
			</div>

			<div align="center" class="subtitulos" >
				CODIGO ESTABLECIMIENTO
			</div>

		</div>

		<div class="box3" style="width: 16%;">

			<div class="campos" style="border-style: solid; height:11px !important; text-align: center; " id="n_admision">
				@if($caso)
					@if(isset($caso->cuenta_corriente))
						{{strtoupper($caso->cuenta_corriente)}}
					@endif
				@endif
			</div>

			<div align="center" class="subtitulos" >
				N° ADMISIÓN
			</div>

		</div>

		<div class="box3" style="width: 26.5%;">

			<div class="campos" style="border-style: solid; height:11px !important; text-align: center; " id="n_historia_clinica">
				@if($caso)
					@if($caso->ficha_clinica)
						{{strtoupper($caso->ficha_clinica)}}
					@endif
				@endif
			</div>

			<div align="center" class="subtitulos" >
				N° HISTORIA CLINICA
			</div>

		</div>
	</div>

	<div style="border-style: dotted none none none; margin-top:4; margin-bottom:-10px;" class="subtitulos">
		<p align= "center">
			<b>DATOS DE IDENTIFICACIÓN DEL (DE LA) PACIENTE:</b>
		</p>
	</div>

	<div style="border-style: dotted none none none; !important; margin-top:2px;">
		<div align="left" class="subtitulos" style="margin-top:2px;">
			<b>NOMBRE PACIENTE</b>
		</div>
    <div class="box3" style="margin-top:4; width: 28%;">

			<div class="campos" style="border-style: solid; height:11px !important; text-align: center; " id="primer_apellido">
				{{strtoupper ($paciente->apellido_paterno)}}
			</div>

			<div align="center" class="subtitulos" >
				PRIMER APELLIDO
			</div>

		</div>

		<div class="box3" style="width: 28%;">

      <div class="campos" style="border-style: solid; height:11px !important; text-align: center; " id="segundo_apellido">
        {{strtoupper ($paciente->apellido_materno)}}
      </div>

      <div align="center" class="subtitulos" >
        SEGUNDO APELLIDO
      </div>

		</div>

		<div class="box3" style="width: 43%;">

      <div class="campos" style="border-style: solid; height:11px !important; text-align: center; " id="nombres">
        {{strtoupper ($paciente->nombre)}}
      </div>

      <div align="center" class="subtitulos" >
        NOMBRES
      </div>

		</div>
	</div>

	<div style="margin-bottom: 18px; margin-top: -40px;">
		<div class="box3" style="padding-bottom:10px;">
			<div align="left" class="subtitulos box3 " >
				<b>TIPO DE IDENTIFICACIÓN</b>
			</div>
      <div class="box4" style="border-style: solid; width: 10px; height:11px !important; text-align: center;" id="nombres">
				@if($paciente)
						@if($paciente->rut != null)
							1
						@elseif($paciente->rut == null)
							2
						@endif
				@endif
			</div>
      <div class="box4" style="height: 5px; width: 250px; margin-top:10px; margin-left:-120px">
  			<p class="letra_diminuta" style="">1. RUN / 2. Pasaporte</p>
        <p class="letra_diminuta" style="margin-top:-10px;">3. Indocumentado / 4. Otro</p>
  		</div>
		</div>

		<div class="box3" style="padding-bottom:10px; padding-left:60px;">
			<div align="right" class="subtitulos box3">
				<b>SEXO</b>
			</div>
      <div class="box4" style="border-style: solid; height:11px !important; text-align: center;" id="nombres">
				@if($paciente)
					@if($paciente->sexo)
						@if($paciente->sexo == 'masculino')
							01
						@elseif($paciente->sexo == 'femenino')
							02
						@elseif($paciente->sexo == 'indefinido')
							03
						@else
							99
						@endif
					@else
						99
					@endif
				@endif
			</div>
      <div class="box4" style="height: 5px; width: 250px; margin-top:10px; margin-left: -50px">
  			<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">01. Hombre / 02. Mujer</p>
        <p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">03. INTERSEX (INDETERMINADO) / 99. DESCONOCIDO</p>
  		</div>
		</div>

    <div class="box3" style="padding-bottom:10px; margin-left:-80px; width:180px;">
			<div align="left" class="subtitulos box3" style="" >
				<b>FECHA DE NACIMIENTO</b>
			</div>
      <div class="box4" style="border-style: solid; height:11px !important;width:17px !important; text-align: center;" id="nombres">
				@if($paciente)
					@if($paciente->fecha_nacimiento)
						{{$fn_dia}}
					@endif
				@endif
			</div>
      <div class="box3" style="width: 1%;">
  			-
  		</div>
      <div class="box4" style="border-style: solid; height:11px !important;width:17px !important; text-align: center;" id="nombres">
        @if($paciente)
          @if($paciente->fecha_nacimiento)
            {{$fn_mes}}
          @endif
        @endif
      </div>
      <div class="box3" style="width: 1%;">
  			-
  		</div>
      <div class="box4" style="border-style: solid; height:11px !important;width:27px !important; text-align: center;" id="nombres">
				@if($paciente)
					@if($paciente->fecha_nacimiento)
						{{$fn_year}}
					@endif
				@endif
			</div>
		</div>

    <div class="box3" style="padding-bottom:10px; width: 8%; padding-left:20px;">
			<div class="subtitulos box3" style="width: 35px;">
				<b>EDAD</b>
			</div>

			<div class="box4 " style="width: 30px; border-style: solid; height:11px !important; text-align: center; padding-left:-5px; margin-left:-7px; " id="primer_apellido">
				@if($paciente)
					@if($paciente->fecha_nacimiento)
						{{$edad}}
					@endif
				@endif
			</div>
		</div>

    <div class="box3" style="padding-bottom:10px; padding-left:20px; margin-top: 5px;">
			<div class="subtitulos box3" style="">
				<b>UNIDAD MEDIDA DE LA EDAD</b>
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center;" id="primer_apellido">
				@if($paciente)
					@if($paciente->fecha_nacimiento)
						{{$unidad_medida}}
					@endif
				@endif
			</div>

			<div class="box4" style="width: 50%; margin-top:17px; margin-left: -135px;" >
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">1. Años / 2. Meses</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">3. Días / 4. Horas</p>
			</div>

		</div>
	</div>

	<div class="" style="height:65px;">
		<div class="box3" style="width: 37%; margin-top: -18px">
      @if($paciente->rut != null)
			<div align="left" class="subtitulos" style="">
				1.RUN
			</div>

			<div class="box4" style="width: 41%; border-style: solid; height:11px !important; text-align: center; " id="rut">
				@if($paciente)
					@if($paciente->rut)
						{{$paciente->rut}}
					@endif
				@endif

			</div>

			<div class="box3" style="width: 3%;">
				-
			</div>

			<div class="box4" style="width: 6%; border-style: solid; height:11px !important; text-align: center; " id="dv">
				@if($paciente)
					@if($paciente->dv)
						@if($paciente->dv == 10)
							K
						@else
							{{$paciente->dv}}
						@endif
					@endif
				@endif
			</div>
    @endif
      @if($paciente->rut == null)
			<div align="left" class="subtitulos" style="margin-top: 0px;">
				2. N° de Pasaporte u otro documento
			</div>

			<div class="box4" style="width: 55%; border-style: solid; height:11px !important; text-align: center; " id="primer_apellido">
				{{$num_identificacion}}
			  @if($paciente)
					@if($paciente->identificacion == "pasaporte" || $paciente->identificacion == "otro documento")
						@if($paciente->n_identificacion)
							{{$paciente->n_identificacion}}
						@endif
					@endif
				@endif
		</div>
  @endif
	</div>

  <div class="box3" style="width: 30%; margin-left: -60px; margin-top: -5px;">
    <div style="">
      <div class="box3" style="width: 50%; ">
        <div align="left" class="subtitulos box3" >
          <b>PUEBLOS INDIGENAS</b>
        </div>
      </div>
      <div class="box3" style="width: 50%;">
        <div class="box4" style="width: 20px; margin-left:-20px; border-style: solid; height:11px !important; text-align: center; " id="">
          @if($paciente)
            @if($paciente->pueblo_indigena)
              {{$paciente->pueblo_indigena}}
            @else
              11
            @endif
          @endif
        </div>
        <div style="margin-left: 10;margin-top: -15;  font-size: 10px;">
          <b>
            <u style="width: 100%;">
              @if($paciente)
                @if($paciente->pueblo_indigena && $paciente->pueblo_indigena == 99)
                  {{$paciente->detalle_pueblo_indigena}}
                @endif
              @endif
            </u>
          </b>
        </div>
      </div>
    </div>

    <div style="">
      <div class="box3" style="width: 350px; margin-left: -18px; margin-top: -26px;" class="">
        <p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">1. Mapuche / 2. Aymara / 3. Rapa Nui (Pascuense) / 4. Lican Antai (Atacameño) / 5. Quechua</p>
        <p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">6. Colla / 7. Diaguita 8. Kawésqar / 9. Yagán (Yámana) / 96. Ninguno / 99. Otro (Especificar)</p>
      </div>
    </div>
  </div>

  <div class="box3" style="width: 22%; margin-top:-20px;">
    <div style="width:100%;">
      <div class="box3" style="width:100%;">
        <div align="left" class="subtitulos box3" >
          <b>PAÍS DE ORIGEN DE EL(LA) PACIENTE</b>
        </div>
      </div>
      <br>
      <div class="box4" style="width: 50%; margin-left: 160px; margin-top: -23px; border-style: solid; height:11px !important; text-align: center; " id="primer_apellido">
        @if($pais)
          {{strtoupper($pais->nombre_pais)}}
        @endif
      </div>

      <div align="center" class="subtitulos" style="width: 100px; margin-left: 150px; margin-top: -5px;">
        Nombre País
      </div>
    </div>
  </div>
  <div style="border-style:dotted none none none;margin-top:-20px; height: 5px;" class="subtitulos"></div>
</div>

	<div class="" style="width: 50%;border-style:none solid none none ; height:71px; margin-top:-40px;">

		<div class=" box3 " style="margin-top:-98px;">
		<div class=" box3 " style="width: 135px;">
			<div class="subtitulos " style="margin-top:0px;">
				<b>CATEGORIA OCUPACIONAL</b>
			</div>
			<div class="box4" style="margin-top:-8px; width: 20px; border-style: solid; height:11px" >
				@if($paciente->categoria_ocupacional == 0)
					00
				@elseif($paciente->categoria_ocupacional < 4 && $paciente->categoria_ocupacional > 0)
					0{{$paciente->categoria_ocupacional-1}}
				@else
					99
				@endif
			</div>
			<div class="box3" style="width: 80px;  margin-top:0px; padding-top:0px;margin-left:0px;">
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">00. INACTIVOS</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">01. ACTIVOS</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">02. CESANTE O DESOCUPADOS</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">99. DESCONOCIDO</p>
			</div>
			<div class="box4" style="margin-top:28px; margin-left:-112px; width: 20px; border-style: solid; height:11px" >
				@if($paciente)
					@if($paciente->categoria_activo && $paciente->categoria_activo != "sin información")
						{{$paciente->categoria_activo}}
					@else

					@endif
				@endif
			</div>
			<div class="box3  letra_diminuta" style="margin-top:28px; padding-top:-20px; margin-left:0px; width: 85px;">
				En caso de marcar la alternativa "Activos" identifique la opción declarada
			</div>
		</div>
		<div class=" box3" style="width: 170px;  margin-top:21px; padding-top:0px;margin-left:-25px;">
			<div style="margin-top: 5px;">
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">01. Miembro del poder ejecutivo de los cuerpos legislativos, personal directivo de la administración pública y de empresa</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">02. Profesionales científicos o intelectuales</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">03. Tecnicos y profesionales de nivel medio</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">04. Empleados de oficina</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">05. Trabajadores de los servicios y vendedores de comercio y mercado</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">06. Agricultores y trabajadores calificados agropecuarios y pesqueros</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">07. Oficiales, operarios y artesanos de artes mecánicas de otros oficios</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">08. Operadores de instalaciones y máquinas y montadoras</p>
			</div>
		</div>
		<div class=" box3" style="width: 50px;  margin-top:px; padding-top:0px;margin-left:0px;">
			<div style="margin-top: 5px;">
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">09. Trabajadores no calificados</p>
				<p class="letra_diminuta" style="margin-bottom: 0px;margin-top: 0px;">10. Fuerzas armadas</p>
				<p class="letra_diminuta" style="margin-bottom: 0;margin-top: 0;">99. Desconocido</p>
			</div>
		</div>
		</div>

		<div class="box3" style="width: 65%; height:75px; border-style:none solid none none ;margin-top:-95px; margin-left:50px;padding-top:-7px;">
			<div class="subtitulos" style="padding-top: 5px;">
				<b>NIVEL DE INSTRUCCIÓN</b>
			</div>
			<div style="">
				<div class="box4" style="width: 20px; height:11px; border-style: solid; margin-top:-35px; ">
					@if($paciente)
						@if($paciente->nivel_instruccion)
							@if($paciente->nivel_instruccion < 7)
								0{{$paciente->nivel_instruccion}}
							@else
								9{{$paciente->nivel_instruccion}}
							@endif
						@endif
					@endif
				</div>
				<div class="box3" style="width: 110px; padding-top:10px;">
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">01. Prebásica</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">02. Básica</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">03. Media</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">04. Técnico de nivel Superior</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">05. Profesional universitario</p>
				</div>
				<div class="box3" style="width: 100px; padding-top:10px;">
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">06. Sin instrucción</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">97. No recuerda</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">98. No responde</p>
				</div>
			</div>
		</div>

		<div class="box3" style="width: 26%; height: 180px; margin-left:11px; margin-top:23px;">
			<div class="subtitulos" style="margin-bottom: 10px; margin-top:-8px;">
				<b>TELÉFONO FIJO</b>
			</div>
			<div class="box4" style="width: 150PX; height:11px !important; text-align: center; margin-left: -20px; margin-top:-10px;">
				@if($telefonocasa)
					{{$telefonocasa->telefono}}
				@endif
			</div>

			<div class="subtitulos" style="margin-bottom: 10px; margin-top:0px;">
				<b>TELÉFONO MÓVIL</b>
			</div>
			<div class="box4" style="width: 150PX; height:11px !important; text-align: center; margin-left: -20px; margin-top:-10px;">
				@if($telefonomovil)
					{{$telefonomovil->telefono}} 
				@endif
			</div>
		</div>

	</div>

	<div class="" style="border-style:none none dotted none;"></div>

	<div class="" style="margin-top: 5px;padding-top:-3px;">
		<div class="subtitulos" style="">
			<b>DOMICILIO</b>
		</div>

	</div>

	<div class="box3" style="width: 10%; margin-top: 10px;">
		<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">01. Calle</p>
		<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">02. Avenida</p>
		<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">03. Pasaje</p>
	</div>
	<div class="box3" style="width: 10%; margin-left: -20px; margin-top: -10px;">
		<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">04. Camino</p>
		<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">09. Otro</p>
	</div>

	<div class="box4" style="width: 20px; height:11px; border-style: solid;margin-left:-15px; margin-top:-22px;">
		@if($paciente)
			@if($paciente->tipo_direccion)
				@if($paciente->tipo_direccion < 5)
					0{{$paciente->tipo_direccion}}
				@else
					09
				@endif
			@endif
		@endif
	</div>


	<div class=" box3 " style="width: 60%;margin-left:5px; margin-top:-10px;">
		<div class="box4" style="border-style: solid;width: 400px; height:11px !important; text-align: center; margin-top:3; " id="primer_apellido">
			@if($paciente)
				@if($paciente->calle)
					{{strtoupper($paciente->calle)}}
				@endif
			@endif
		</div>

		<div class=" subtitulos" style="text-align: center;">
			Nombre
		</div>
	</div>

	<div class=" box3 " style="width: 20%;margin-left:-15px;margin-top:-10px;">
		<div class="box4" style="border-style: solid;width: 142px;  height:11px !important; text-align: center;  " id="primer_apellido">
			@if($paciente)
				@if($paciente->numero)
					{{$paciente->numero}}
				@endif
			@endif
		</div>

		<div class=" subtitulos" style="text-align: center;">
			Número
		</div>
	</div>

	<div class="" style="margin-bottom:5px;margin-top:-15px;padding-bottom:-5px;">
		<div class="subtitulos box3 " style="margin-left:200px;width:20%;">
			<b>Comuna Residencia</b>
		</div>
		<div class="box4" style="border-style: solid; height:11px !important; text-align: center; width:354px; margin-left 50px;" id="primer_apellido">
			@if($comuna_paciente)
				{{strtoupper($comuna_paciente->nombre_comuna)}}
			@endif
		</div>
	</div>

	<div class="" style="border-style:none none dotted none;"></div>

	<div class=" box3  " style="width:25%; height:60px;">

		<div class="subtitulos " style="margin-top:-1px !important;">
			<b>PREVISION</b>
		</div>

		<div class="box4 " style="width: 20px; height:11px; border-style: solid; margin-top:-40px;text-align:center;">
			@if($prevision)
				@if($prevision < 6)
					0{{$prevision}}
				@else
					{{$prevision}}
				@endif
			@endif
		</div>

		<div class="box3 " style="width:38%;margin-top:14px;">
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">01. FONASA</p>
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">02. ISAPRE</p>
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">03. CAPREDENA</p>
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">04. DIPRECA</p>
		</div>

		<div class="box3 " style="width:45%;margin-top: 8px;">
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">05. SISA</p>
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">96. NINGUNA</p>
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">99. DESCONOCIDO</p>
		</div>
	</div>

	<div class=" box3 " style="width:15%;padding-left: 10px; border-style:  none solid none solid ;height: 60px; margin-top: 10px !important;">
		<div class="subtitulos " style="margin-top: -12px !important;">
			<b>Clasificación Beneficiario FONASA</b>
		</div>

		<div class="box3 " style="width:25%;margin-top:5px;">
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">A) A</p>
      <p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">B) B</p>
      <p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">C) C</p>
      <p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">D) D</p>
		</div>

		<div class="box4" style="width: 20px; height: 11px; border-style: solid; margin-top:-20px;text-align:center;">
			@if($beneficio)
				{{$beneficio}}
			@endif
		</div>

	</div>

	<div class=" box3 " style="width:11%;padding-left: 0px; border-style:none solid none none; height: 60px; margin-top: 10px !important;">
		<div class="subtitulos " style="margin-top: -12px !important;">
			<b>Modalidad de atención FONASA</b>
		</div>

		<div class="box3 " style="width:15%;margin-top:4px;">
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">01) MAI</p>
		</div>
		<div class="box3 " style="width:15%;margin-top:0px;">
			<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;margin-left:5px">02) MLE</p>
		</div>

		<div class="box4" style="width: 20px; border-style: solid; margin-top:0px; margin-left:13px; height:11px; text-align:center;">
			@if($caso)
				@if($caso->modalidad_fonasa < 3 && $caso->modalidad_fonasa > 0)
					0{{$caso->modalidad_fonasa}}
				@endif
			@endif
		</div>

	</div>

	<div class=" box3 " style="width:38%; margin-top:15px; height:60px;">
		<div class="subtitulos " style="margin-top: -17px !important;">
			<b>LEYES PREVISIONALES</b>
		</div>

		<div class="box3 " style="width: 49%; margin-top:0px;">

			<div class="box3 " style="width:55%; margin-left:0px;">
				<p class="subtitulos" style="margin-bottom: 0;">1. SI</p>
			</div>
			<div class="box3 " style="width:55%; margin-left:-55px;">
				<p class="subtitulos" style="margin-bottom: 0;">2. NO</p>
			</div>

			<div class="box4" style="width: 20px; border-style: solid; margin-top:-21px; margin-left:-93px;height:11px;text-align:center;">
				@if($caso)
					@if($caso->leyes_previsionales == 'true')
						1
					@else
						2
					@endif
				@endif
			</div>

		</div>

		<div class="box3" style="width: 60%; margin-left:-10px;">
			<div class="box4" style="width: 20px; border-style: solid; margin-top:-12px;height:11px;margin-left:-75px;">
				@if($caso)
					@if($caso->leyes_previsionales == 'true' && $caso->ley)
						0{{$caso->ley}}
					@endif
				@endif
			</div>

			<div class="box3" style="margin-top:10px !important; width:300px;">
				<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">01. Ley 18.490: accidente de transporte</p>
				<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">02. Ley 18.744: accidentes del trabajo y enfermedades profesionales</p>
				<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">03. Ley 16.744: accidente escolar</p>
				<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">04. Ley 19.650/99 de urgencia</p>
				<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">05. Ley 19.992 PRAIS</p>
			</div>
		</div>

	</div>


	<div class="" style="border-style:none none dotted none ; margin-top: -6px;"></div>

	<div class="" style="margin-bottom: -17px; margin-top: 20px; height:80px;">
		<div class=" box3 " style="width: 60%; margin-top:8px;">
			<div class="subtitulos" style="margin-bottom: 5px;">
				<b>PROCEDENCIA DEL (DE LA) PACIENTE</b>
			</div>

			<div class="">
				<div class=" box3" style="width: 45%; margin-top:0px;">
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">1. Unidad Emergencia (mismo establecimiento)</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">3. Atención especialidades (mismo establecimiento)</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">4. Otro establecimiento</p>
				</div>
				<div class=" box3" style="width: 55%; margin-top:0px;">
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">5. Otra procedencia</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">6. Área de Cirugía Mayor Ambulatoria (mismo establecimiento)</p>
					<p class="subtitulos" style="margin-bottom: 0;margin-top: 0;">7. Hospital comunitario o de baja complejidad</p>

				</div>
				<div class=" box3" style="width: 45%; margin-top:0px;">
					<div class="box4" style="width: 20px; border-style: solid; margin-left:130px; margin-top:-15px;height:11px;text-align:center;">
						@if($informe_egreso)
							@if($informe_egreso->procedencia_paciente)
								{{$informe_egreso->procedencia_paciente}}
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>

		<div class="box3" style="width: 59%; margin-top:8px;">
			<div class="subtitulos" style="margin-left:10px;">
				<b>ESTABLECIMIENTO DE PROCEDENCIA</b>
			</div>
			<div class="box3" style="margin-top:25px;">
			<div class="box3" style="width: 70%;">
				<div class="box4" style="margin-top:-45px; margin-left:10px; border-style: solid; width:264px;height:11px !important; text-align: center; " id="">
					@if($informe_egreso)
						@if($informe_egreso->procedencia_paciente)
							@if($informe_egreso->procedencia_paciente == 4 || $informe_egreso->procedencia_paciente == 7)
								{{strtoupper($informe_egreso->establecimiento_procedencia)}}
							@endif
						@endif
					@endif
				</div>
				<div class="subtitulos" style="text-align: center; margin-top:-52px;">
					(Solo llenar si se registró opción 4 o 7)
				</div>
			</div>

			<div class="box3" style="width: 29%; margin-top:15px;margin-left:-190px;">
				<div class="box4" style="border-style: solid; width:120px;height:11px !important; text-align: center;" id="">
					@if($informe_egreso)
						@if($informe_egreso->cod_establecimiento_procedencia)
							@if($informe_egreso->procedencia_paciente == 4 || $informe_egreso->procedencia_paciente == 7)
								{{$informe_egreso->cod_establecimiento_procedencia}}
							@endif
						@endif
					@endif
				</div>
				<div class="subtitulos" style="text-align: center; margin-bottom: 25px;">
					Código Establecimiento
				</div>
			</div>
			</div>
		</div>
	</div>

	<div style="border-style: dotted none dotted none; height:15px;padding-bottom:-3px;" class="subtitulos">
		<p align= "center">
			<b>DATOS DE LA HOSPITALIZACIÓN:</b>
		</p>
	</div>

	<div class=" box3" style="width: 40%;height:110px;border-style: none none none none;margin-top:25px;">
		<div style="margin-top:-10px;">
			<div class=" box3" style="width: 30%; height: 15px;">
				<div class="subtitulos" style=""></div>
			</div>

			<div class=" box3" style="width: 20%;height: 15px;padding-left: 2px;">
				<div class="subtitulos box3" style="width:25px;">
					Hora
				</div>
				<div class="subtitulos box3" style="width:20px;">
					Minutos
				</div>
			</div>

			<div class=" box3" style="width: 45%;height: 15px;margin-top:5px;">
				<div class="subtitulos" style="text-align: center;">
						FECHA (dd-mm-aaaa)
				</div>
			</div>
		</div>

		<div style="margin-top:-10px;">
			<div class=" subtitulos box3" style="width: 30%;">
				INGRESO
			</div>

			<div class="  box3" style="width: 21%;margin-top:10px;">
				<div class="box4" style="width: 20px; border-style: solid; height:11px;">
					@if($fecha_hosp_hr)
						{{$fecha_hosp_hr}}
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_min)
						{{$fecha_hosp_min}}
					@endif
				</div>
			</div>

			<div class="box3 " style="width: 43%; padding-left: 8px;margin-top:10px;">
				<div class="box4" style="width: 20px; border-style: solid; height:11px;">
					@if($fecha_hosp_dia)
						@if($fecha_hosp_cantidad >= 1)
							{{$fecha_hosp_dia[0]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid; height:11px;">
					@if($fecha_hosp_mes)
						@if($fecha_hosp_cantidad >= 1)
							{{$fecha_hosp_mes[0]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px; border-style: solid; padding-left:5px; height:11px;">
					@if($fecha_hosp_year)
						@if($fecha_hosp_cantidad >= 1)
							{{$fecha_hosp_year[0]}}
						@endif
					@endif
				</div>
			</div>
		</div>

		<div id="1" class="traslados" style="margin-top:-2px;">
			<div class=" subtitulos box3" style="width: 30%;margin-top:-5px;">
				1er TRASLADO
			</div>

			<div class="  box3" style="width: 21%;"></div>

			<div class="box3 " style="width: 43%; padding-left: 8px;margin-top:5px;">
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_dia)
						@if($fecha_hosp_cantidad >= 2)
							{{$fecha_hosp_dia[1]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_mes)
						@if($fecha_hosp_cantidad >= 2)
							{{$fecha_hosp_mes[1]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px; border-style: solid;height:11px; padding-left:5px;">
					@if($fecha_hosp_year)
						@if($fecha_hosp_cantidad >= 2)
							{{$fecha_hosp_year[1]}}
						@endif
					@endif
				</div>
			</div>
		</div>
		<div class="traslados" id="2" style="margin-top:3px;">
			<div class=" subtitulos box3" style="width: 30%;margin-top:-5px;">
				2° TRASLADO
			</div>

			<div class="  box3" style="width: 21%;"></div>

			<div class="box3 " style="width: 43%; padding-left: 8px;">
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_dia)
						@if($fecha_hosp_cantidad >= 3)
							{{$fecha_hosp_dia[2]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_mes)
						@if($fecha_hosp_cantidad >= 3)
							{{$fecha_hosp_mes[2]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px; border-style: solid;height:11px; padding-left:5px;">
					@if($fecha_hosp_year)
						@if($fecha_hosp_cantidad >= 3)
							{{$fecha_hosp_year[2]}}
						@endif
					@endif
				</div>
			</div>
		</div>
		<div class="traslados" id="3" style="margin-top:-1px;">
			<div class=" subtitulos box3" style="width: 30%; padding-top:0px;margin-top:-5px;">
				3er TRASLADO
			</div>

			<div class="  box3" style="width: 21%;"></div>

			<div class="box3 " style="width: 43%; padding-left: 8px;">
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_dia)
						@if($fecha_hosp_cantidad >= 4)
							{{$fecha_hosp_dia[3]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_mes)
						@if($fecha_hosp_cantidad >= 4)
							{{$fecha_hosp_mes[3]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px; border-style: solid;height:11px; padding-left:5px;">
					@if($fecha_hosp_year)
						@if($fecha_hosp_cantidad >= 4)
							{{$fecha_hosp_year[3]}}
						@endif
					@endif
				</div>
			</div>
		</div>
		<div class="traslados" id="4" style="margin-top:-1px;">
			<div class=" subtitulos box3" style="width: 30%;margin-top:-5px;">
				4° TRASLADO
			</div>

			<div class="  box3" style="width: 21%;"></div>

			<div class="box3 " style="width: 43%; padding-left: 8px;">
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_dia)
						@if($fecha_hosp_cantidad >= 5)
							{{$fecha_hosp_dia[4]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px; border-style: solid;height:11px;">
					@if($fecha_hosp_mes)
						@if($fecha_hosp_cantidad >= 5)
							{{$fecha_hosp_mes[4]}}
						@endif
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px; border-style: solid;height:11px; padding-left:5px;">
					@if($fecha_hosp_year)
						@if($fecha_hosp_cantidad >= 5)
							{{$fecha_hosp_year[4]}}
						@endif
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class=" box3 " style="width:35%;height:110px;border-style: none solid none dotted;margin-top:0px;">
		<div style="text-decoration:underline; height: 20px;text-align: center;margin-bottom:1px;" class="subtitulos">
			UNIDAD FUNCIONAL
		</div>

		<div class="box5" style=" border-style: none none solid none;height:11px;margin-bottom:0px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
			@if($unidad_funcional)
				@if($fecha_hosp_cantidad >= 1)
					{{$unidad_funcional[0]}}
				@endif
			@endif
		</div>

		<div class="box5" style=" border-style: none none solid none;height:11px;margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
			@if($unidad_funcional)
				@if($fecha_hosp_cantidad >= 2)
					{{$unidad_funcional[1]}}
				@endif
			@endif
		</div>

		<div class="box5" style=" border-style: none none solid none;height:11px;margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
			@if($unidad_funcional)
				@if($fecha_hosp_cantidad >= 3)
					{{$unidad_funcional[2]}}
				@endif
			@endif
		</div>

		<div class="box5" style=" border-style: none none solid none;height:11px;margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
			@if($unidad_funcional)
				@if($fecha_hosp_cantidad >= 4)
					{{$unidad_funcional[3]}}
				@endif
			@endif
		</div>

		<div class="box5" style=" border-style: none none solid none;height:11px;margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
			@if($unidad_funcional)
				@if($fecha_hosp_cantidad >= 5)
					{{$unidad_funcional[4]}}
				@endif
			@endif
		</div>

	</div>
	<div class=" box3 " style="width: 23%;height:110px; margin-top:-10px;">
		<div class="" style="height: 20px; margin-bottom:0px;">
			<div class=" letra_diminuta box3" style="width: 50%;">
				CÓDIGO UNIDAD FUNCIONAL
			</div>

			<div class=" letra_diminuta box3" style="width: 50%;margin-left:-10px;">
				CÓDIGO SERVICIO CLÍNICO
			</div>
		</div>

		<div class="" style="margin-top:0px;">
			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%;" id="">
				@if($codigo_area_funcional)
					@if($fecha_hosp_cantidad >= 1)
						{{$codigo_area_funcional[0]}}
					@endif
				@endif
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%; margin-left 20px;" id="">
				@if($codigo_servicio_clinico)
					@if($fecha_hosp_cantidad >= 1)
						{{$codigo_servicio_clinico[0]}}
					@endif
				@endif
			</div>
		</div>

		<div class="" style="margin-bottom:0px;">
			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%;" id="">
				@if($codigo_area_funcional)
					@if($fecha_hosp_cantidad >= 2)
						{{$codigo_area_funcional[1]}}
					@endif
				@endif
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%; margin-left 20px;" id="">
				@if($codigo_servicio_clinico)
					@if($fecha_hosp_cantidad >= 2)
						{{$codigo_servicio_clinico[1]}}
					@endif
				@endif
			</div>
		</div>

		<div class="" style="margin-bottom:0px;">
			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%;" id="">
				@if($codigo_area_funcional)
					@if($fecha_hosp_cantidad >= 3)
						{{$codigo_area_funcional[2]}}
					@endif
				@endif
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%; margin-left 20px;" id="">
				@if($codigo_servicio_clinico)
					@if($fecha_hosp_cantidad >= 3)
						{{$codigo_servicio_clinico[2]}}
					@endif
				@endif
			</div>
		</div>

		<div class="" style="margin-bottom:0px;">
			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%;" id="">
				@if($codigo_area_funcional)
					@if($fecha_hosp_cantidad >= 4)
						{{$codigo_area_funcional[3]}}
					@endif
				@endif
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%; margin-left 20px;" id="">
				@if($codigo_servicio_clinico)
					@if($fecha_hosp_cantidad >= 4)
						{{$codigo_servicio_clinico[3]}}
					@endif
				@endif
			</div>
		</div>

		<div class="" style="">
			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%;" id="">
				@if($codigo_area_funcional)
					@if($fecha_hosp_cantidad >= 5)
						{{$codigo_area_funcional[4]}}
					@endif
				@endif
			</div>

			<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 46.3%; margin-left 20px;" id="">
				@if($codigo_servicio_clinico)
					@if($fecha_hosp_cantidad >= 5)
						{{$codigo_servicio_clinico[4]}}
					@endif
				@endif
			</div>
		</div>

	</div>

	<div class="" style="border-style: dotted none dotted none;margin-top:-20px;padding-bottom:-15px; height:55px;">
		<div style="width: 80%; margin-top:-15px;" class="box3 ">
			<div style="margin-bottom:5px;">
				<div class=" box3" style="width: 30%; height: 15px;">
					<div class="subtitulos" style=""></div>
				</div>

				<div class=" box3" style="width: 20%;height: 15px;margin-top:-5px; margin-left: -128px;">
					<div class="subtitulos box3" style="width:25px;">
						Hora
					</div>
					<div class="subtitulos box3" style="width:20px;">
						Minutos
					</div>
				</div>

				<div class=" box3" style="width: 45%;height: 15px; margin-left: -120px;">
					<div class="subtitulos" style="text-align: center;">
							FECHA (dd-mm-aaaa)
					</div>
				</div>
			</div>

			<div class=" subtitulos box3" style="width: 30%;margin-top:-5px;padding-bottom:8px;">
				<b>EGRESO</b>
			</div>

			<div class="  box3" style="width: 21%;padding-bottom:2px; margin-left: -130px;">
				<div class="box4" style="width: 20px;height: 11px;border-style: solid;">
					@if($hr_egreso)
						{{$hr_egreso}}
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px;height: 11px;border-style: solid;">
					@if($min_egreso)
						{{$min_egreso}}
					@endif
				</div>
			</div>

			<div class="box3 " style="width: 43%; padding-bottom:2px; margin-left: -50px;">
				<div class="box4" style="width: 20px;height: 11px;border-style: solid;">
					@if($fecha_egreso_dia)
						{{$fecha_egreso_dia}}
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 20px;height: 11px;border-style: solid;">
					@if($fecha_egreso_mes)
						{{$fecha_egreso_mes}}
					@endif
				</div>
				<div class="box3" style="width:5px;">
					-
				</div>
				<div class="box4" style="width: 35px;height: 11px;border-style: solid;">
					@if($fecha_egreso_year)
						{{$fecha_egreso_year}}
					@endif
				</div>
			</div>

			<div style="margin-top:-25px;padding-bottom:3px;" class="">
				<div class=" box3" style="width: 50%;">
					<div class=" subtitulos box3 " style="width: 55%;margin-left:240px;">
						<b>DÍAS DE ESTADA</b>
					</div>
					<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 10%; margin-left:30px;" id="primer_apellido">
						@if($estada)
							{{$estada}}
						@endif
					</div>
				</div>
				<div class=" box3" style="width: 55%; margin-left:80px;">
					<div class=" subtitulos box3 " style="width: 55%;">
						<div class="subtitulos">CONDICIÓN AL EGRESO:</div>
						<div class="subtitulos">1) Vivo  2)Fallecido</div>
					</div>

					<div class="box3" style="width:20px !important; margin-left:-75px;">
						<div class="box4" style="width:20px !important;height: 11px;border-style: solid; text-align:center;">
							@if($caso)
								@if($caso->condicion_egreso)
									@if($caso->condicion_egreso == 'vivo')
										1
									@else
										2
									@endif
								@elseif($caso->motivo_termino != "fallecimiento")
									1
								@else
									2
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<div style="width:27%; margin-top:-8px; margin-left:-55px;padding-bottom:5px;" class=" box3">
			<div class="" style="height:20px; margin-top: 30px; margin-left:5px;">
				<div class=" letra_diminuta box3 " style="width:51%;padding-bottom:-5px;">
					CÓDIGO UNIDAD FUNCIONAL
				</div>

				<div class=" letra_diminuta box3 " style="width: 48%;padding-bottom:-5px;">
					CÓDIGO SERVICIO CLÍNICO
				</div>
			</div>

			<div class="" style="padding-bottom:-10px;">
				<div class="box4 " style=";border-style: solid; height:11px !important; text-align: center; width:88.5px;" id="">
					@if(isset($informe_egreso->cod_fun_egreso))
						{{$informe_egreso->cod_fun_egreso}}
					@endif
				</div>

				<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width:88.5px;" id="">
					@if(isset($informe_egreso->cod_ser_egreso))
						{{$informe_egreso->cod_ser_egreso}}
					@endif
				</div>
			</div>

			<div class="subtitulos" style="margin-bottom:10px;">
				<p class="" align="center"><b>RESPONSABLE ESTADÍSTICA</b></p>
			</div>
		</div>

	</div>

	<div>
		<div class=" box3" style="width:580px; border-style: none solid none none;margin-top:11px;">
			<div class="subtitulos">
				<b> RESPONSABLE MÉDICO O PROFESIONAL TRATANTE</b>
			</div>

			<div style="margin-bottom: 0px;">
				<div class="box3  subtitulos" style="width: 20%;padding-top:5px;">
					<b>DIAGNÓSTICO PRINCIPAL</b>
				</div>
				<div class="box4" style="width: 75%; border-style: solid; height:11px !important; text-align: left; margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;padding-left:4px;" id="primer_apellido">
					@if($diagnostico_principal)
						{{strtoupper($diagnostico_principal->diagnostico)}}
					@endif
				</div>
			</div>

			<div style="margin-bottom: 0px;">
				<div class="box3  subtitulos" style="width: 20%;">
					<b>CAUSA EXTERNA (si corresponde)</b>
				</div>
				<div class="box4 " style="width: 75%; border-style: solid; height:11px !important; text-align: left; margin-top:5px;  overflow: hidden; white-space: nowrap; text-overflow: ellipsis;padding-left:4px;" id="primer_apellido">
					@if($causa_externa)
						{{strtoupper($causa_externa)}}
					@endif
				</div>
			</div>

			<div style="margin-bottom: 0px;">
				<div class="box3  subtitulos" style="width: 20%;padding-top:5px;">
					<b>OTRO DIAGNÓSTICO</b>
				</div>
				<div class="box4 " style="width: 75%; border-style: solid; height:11px !important; text-align: left; margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;padding-left:4px;" id="primer_apellido">
					@if($otros_diagnosticos)
						@if($otros_diagnosticos[0])
							{{strtoupper($otros_diagnosticos[0]->diagnostico)}}
						@endif
					@endif
				</div>
			</div>

			<div style="margin-bottom: 0px;">
				<div class="box3  subtitulos" style="width: 20%;padding-top:5px;">
					<b>OTRO DIAGNÓSTICO *</b>
				</div>
				<div class="box4 " style="width: 75%; border-style: solid; height:11px !important; text-align: left; margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;padding-left:4px;" id="primer_apellido">
					@if($otros_diagnosticos)
						@if(isset($otros_diagnosticos[1]))
							{{strtoupper($otros_diagnosticos[1]->diagnostico)}}
						@endif
					@endif
				</div>
			</div>

		</div>

		<div class="box3" style="width:19%; margin-top:-20px;" >
			<div class="subtitulos" align="center" style="margin-top:55px; margin-bottom:-10px;">
				<b> CÓDIGO CIE-10</b>
			</div>

			<div class="box4" style="border-style: solid;margin-left:8px; height:11px !important; text-align: center; width:105px; margin-top:-45px; margin-bottom:0px;" id="">
				@if($diagnostico_principal)
					{{strtoupper($diagnostico_principal->id_cie_10)}}
				@endif
			</div>

			<div class="box4" style="border-style: solid;margin-left:-113px; height:11px !important; text-align: center; width:105px; margin-top:23px; margin-bottom:0px;" id="">
				@if(isset($cod_causa_externa))
					{{strtoupper($cod_causa_externa)}}
				@endif
			</div>

			<div class="box4" style="border-style: solid;margin-left:-113px; height:11px !important; text-align: center; width:105px; margin-top:46px; margin-bottom:0px;" id="">
				@if($otros_diagnosticos)
					@if(isset($otros_diagnosticos[0]))
						{{strtoupper($otros_diagnosticos[0]->id_cie_10)}}
					@endif
				@endif
			</div>

			<div class="box4" style="border-style: solid;margin-left:-113px; height:11px !important; text-align: center; width:105px; margin-top:68px; margin-bottom:0px;" id="">
				@if($otros_diagnosticos)
					@if(isset($otros_diagnosticos[1]))
						{{strtoupper($otros_diagnosticos[1]->id_cie_10)}}
					@endif
				@endif
			</div>
		</div>

		<div style="border-style: none none dotted none;margin-top:-90px;height:0px;padding-bottom:13px;" class="subtitulos">
		<p align= "left">
			<b>(*) : Ver Instructivo</b>
		</p>
		</div>

	</div>

	@if($recien_nacido_tabla_extras)

		<div class="subtitulos" style="width:100%;text-align:center;">
			RECIEN NACIDOS EXTRAS
		</div>
		<div style="border-style: none none dotted none; padding-bottom: 5px; margin-top:30px;">

			<div class="" style="width:100%;">
				<div class="box3" style="width:10%; ">
					<div class="subtitulos" style="border-style: solid none none solid; height:30px !important; text-align: center; " id="">
						<b>Orden en el nacimiento (*)</b>
					</div>
					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid none none solid; height:20px !important; text-align: center;" id="">
								@if($rn->orden_nacimiento)
									{{$rn->orden_nacimiento +1}}
								@endif
							</div>
						@else
							<div class="" style="border-style: solid none solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->orden_nacimiento)
									{{$rn->orden_nacimiento +1}}
								@endif
							</div>
						@endif
					@endforeach
				</div>

				<div class="box3" style="width:15%;margin-left:-5px">
					<div class="subtitulos" style="border-style: solid none none solid; height:30px !important; text-align: center; " id="n_admision">
						<b>Condición al Nacer:</b>
						<br>
						<b>1)Vivo</b>   <b>2)Fallecido</b>
					</div>
					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid none none solid; height:20px !important; text-align: center;" id="">
								@if($rn->condicion)
									@if($rn->condicion == 'vivo')
										1
									@else
										2
									@endif
								@endif
							</div>
						@else
							<div class="" style="border-style: solid none solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->condicion)
									@if($rn->condicion == 'vivo')
										1
									@else
										2
									@endif
								@endif
							</div>
						@endif
					@endforeach
				</div>

				<div class="box3" style="width:30%;margin-left:-5px">
					<div class="subtitulos" style="border-style: solid none none solid; height:30px !important; text-align: center; " id="n_admision">
						<div class="subtitulos box3" style="width:20%;" >
							<b>SEXO</b>
						</div>
						<div class="subtitulos box3" style="width:25%;margin-top:5px;">
							<div align="left">
								<b>01. Hombre</b>
							</div>
							<div align="left">
								<b>02. Mujer</b>
							</div>
						</div>
						<div class="subtitulos box3" style="width:55%;margin-top:5px;">
							<div align="left">
								<b>03. Intersex (Indeterminado)</b>
							</div>
							<div align="left">
								<b>99. Desconocido</b>
							</div>
						</div>
					</div>

					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid none none solid; height:20px !important; text-align: center;" id="">
								@if($rn->condicion)
									@if($rn->sexo == 'masculino')
										01
									@elseif($rn->sexo == 'femenino')
										02
									@elseif($rn->sexo == 'indefinido')
										03
									@else
										99
									@endif
								@endif
							</div>
						@else
							<div class="" style="border-style: solid none solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->condicion)
									@if($rn->sexo == 'masculino')
										01
									@elseif($rn->sexo == 'femenino')
										02
									@elseif($rn->sexo == 'indefinido')
										03
									@else
										99
									@endif
								@endif
							</div>
						@endif
					@endforeach
				</div>

				<div class="box3" style="width:15%;margin-left:-5px">
					<div class="subtitulos" style="border-style: solid none none solid; height:30px !important; text-align: center; " id="n_admision">
						<b>Peso en gramos:</b>
					</div>

					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid none none solid; height:20px !important; text-align: center;" id="">
								@if($rn->peso_gramos)
									{{$rn->peso_gramos}}
								@endif
							</div>
						@else
							<div class="" style="border-style: solid none solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->peso_gramos)
									{{$rn->peso_gramos}}
								@endif
							</div>
						@endif
					@endforeach
				</div>

				<div class="box3" style="width:15%;margin-left:-5px">
					<div class="subtitulos" style="border-style: solid none none solid; height:30px !important; text-align: center; " id="n_admision">
						<b>apgar 5 Minutos:</b>
					</div>
					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid none none solid; height:20px !important; text-align: center;" id="">
								@if($rn->apgar)
									{{$rn->apgar}}
								@endif
							</div>
						@else
							<div class="" style="border-style: solid none solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->apgar)
									{{$rn->apgar}}
								@endif
							</div>
						@endif
					@endforeach
				</div>

				<div class="box3" style="width:15%;margin-left:-5px">
					<div class="subtitulos" style="border-style: solid solid none solid; height:30px !important; text-align: center; " id="n_admision">
						<b>Anomalia congénita:</b>
						<br>
						<b>SI o</b>   <b>NO</b>
					</div>

					@foreach($recien_nacido_tabla_extras as $key => $rn)
						@if($key < count($recien_nacido_tabla_extras)-1)
							<div class="" style="border-style: solid solid none solid; height:20px !important; text-align: center;" id="">
								@if($rn->anomalia_congenita)
									{{$rn->anomalia_congenita}}
								@endif
							</div>
						@else
							<div class="" style="border-style: solid solid solid solid; height:20px !important; text-align: center;" id="">
								@if($rn->anomalia_congenita)
									{{$rn->anomalia_congenita}}
								@endif
							</div>
						@endif
					@endforeach
				</div>

			</div>

		</div>
	@endif

	<div class="" style="border-style: none none dotted none; margin-top:-30px; ">
		<div style="margin-top:-40px;">
			<div style="width: 25%; margin-top:-5px;" class="subtitulos box3">
				<b>INTERVENCIÓN QUIRÚRGICA</b>
			</div>

			<div style="width: 10%;margin-top:5px;" class="box3">
				<div class="box3">
					<div class="subtitulos">
						1. Si
					</div>
				</div>
				<div class="box3">
					<div class="subtitulos">
						2. No
					</div>
				</div>
				<div class="box4 " style="width: 20px; border-style: solid;height:11px; text-align:center;">
					@if($intervencion_quirurgica)
						@if($intervencion_quirurgica->intervencion_quirurgica)
							{{$intervencion_quirurgica->intervencion_quirurgica}}
						@endif
					@endif
				</div>
			</div>

			<div class="box3 subtitulos " style="margin-left: 52%; width: 10%; ">
				<b>CÓDIGO FONASA</b>
			</div>
		</div>

		<div style="margin-top: 0px;">
			<div style="width: 25%; margin-top: -18px;" class="subtitulos box3">
				<b>INTERVENCIÓN QUIRÚRGICA PRINCIPAL</b>
			</div>

			<div style="width: 60%; margin-top: -9px;" class="box3">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width: 100%; margin-bottom: 5px;" id="">
					@if($intervencion_quirurgica)
						@if($intervencion_quirurgica->intervencion_quirurgica_principal)
							{{strtoupper($intervencion_quirurgica->intervencion_quirurgica_principal)}}
						@endif
					@endif
				</div>
			</div>

			<div style="width: 13%;margin-top: -9px;" class="box3">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width:83px; margin-bottom: 5px;" id="">
					@if($intervencion_quirurgica)
						@if($intervencion_quirurgica->codigo_intervencion_quirurgica_principal)
							{{strtoupper($intervencion_quirurgica->codigo_intervencion_quirurgica_principal)}}
						@endif
					@endif
				</div>
			</div>
		</div>

		<div style="margin-bottom: 10px;">
			<div style="width: 25%; margin-top: -19px;" class="subtitulos box3">
				<b>OTRA INTERVENCIÓN QUIRÚRGICA (*)</b>
			</div>

			<div style="width: 60%;margin-top: -9px;" class="box3">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width:100%; margin-bottom: 5px;" id="">
					@if($intervencion_quirurgica)
						@if($intervencion_quirurgica->otra_intervencion_quirurgica)
							{{strtoupper($intervencion_quirurgica->otra_intervencion_quirurgica)}}
						@endif
					@endif
				</div>
			</div>

			<div style="width: 13%;margin-top: -4px;" class="box3">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width:83px; margin-bottom: 10px;margin-top:-20px;" id="">
					@if($intervencion_quirurgica)
						@if($intervencion_quirurgica->codigo_otra_intervencion_quirurgica)
							{{strtoupper($intervencion_quirurgica->codigo_otra_intervencion_quirurgica)}}
						@endif
					@endif
				</div>
			</div>
		</div>

		<p class="subtitulos" align= "left" style="margin-top:-50px;">
			<b>(*) : Ver Instructivo</b>
		</p>
<div class=""  style="border-style: none none none dotted; margin-bottom: 0; "></div>
	</div>

	<div class="">

		<div style="margin-top:-5px;" class="subtitulos box3">
			<b>DATOS DEL MÉDICO O PROFESIONAL TRATANTE Y/O QUE FIRMA EL ALTA</b>
		</div>
		<div class="box3" style="margin-top:5px;">
			<div style="width:50%;" class="box3" align="left">
				<div class="box3 subtitulos" style="width:55px;margin-top:-5px;margin-left:20px;">
					<b>Especialidad:</b>
				</div>

				<div class="box4" style="border-style: solid; height:11px !important; text-align: center; width:319px;" id="primer_apellido">
					@if($medico)
						@if($medico->especialidad)
							{{strtoupper($medico->especialidad)}}
						@endif
					@endif
				</div>
			</div>
		</div>
		{{-- <div style="width:40%; margin-top:12px;margin-left:-650px;margin-bottom:10px;" class="box3 subtitulos" align="left"><b>Nombre:</b> </div> --}}

		<div class="" style="margin-top:-20px;">
			<div class="box3 " style="width:20%;">
				<div class="box4" style="border-style: solid;margin-left:0px; height:11px !important; text-align: center; width: 90%; margin-bottom: 5px;" id="primer_apellido">
					@if($medico)
						@if($medico->apellido_p)
							{{$medico->apellido_p}}
						@endif
					@endif
				</div>
				<div align="center" class="subtitulos" style="margin-top:-7px;">
					PRIMER APELLIDO
				</div>
			</div>

			<div class="box3 " style="width:20%;">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width: 90%; margin-bottom: 5px;" id="primer_apellido">
					@if($medico)
						@if(isset($medico->apellido_m))
							{{$medico->apellido_m}}
						@endif
					@endif
				</div>
				<div align="center" class="subtitulos" style="margin-top:-7px;">
					SEGUNDO APELLIDO
				</div>
			</div>

			<div class="box3 " style="width:58%;">
				<div class="box4" style="border-style: solid;margin-left:10px; height:11px !important; text-align: center; width:398px; margin-bottom: 5px;" id="primer_apellido">
					@if($medico)
						@if($medico->nombre_medico)
							{{$medico->nombre_medico}}
						@endif
					@endif
				</div>
				<div align="center" class="subtitulos" style="margin-top:-7px;">
					NOMBRES
				</div>
			</div>

		</div>

		<div style="margin-top:0px;">

			<div class="box4" style="width: 20%; border-style: solid; height:11px !important; text-align: center; " id="primer_apellido">
				@if($medico)
					@if($medico->rut_medico)
						{{$medico->rut_medico}}
					@endif
				@endif
			</div>
			<div class="box3" style="width: 3%; text-align:center;">
				-
			</div>
			<div class="box4 " style="width: 20px; border-style: solid; height:11px !important; text-align: center;">
				@if($medico)
					@if($medico->rut_medico)
						@if($medico->dv_medico < 10)
							{{$medico->dv_medico}}
						@else
							K
						@endif
					@endif
				@endif
			</div>
			<div class="subtitulos" style="margin-top:-7px;margin-left:65px;">
				RUN
			</div>
      		<div style="border-style: none none dotted none; margin-top:2px;"></div>
		</div>
		<div>
			<p class="subtitulos" align= "left" style="margin-top:2px;">
				<b>Información protegida por Ley N° 19.628, sobre protección de la vida privada y con garantía del secreto estadístico, establecido en la Ley N° 117.374.</b>
			</p>
		</div>

	</div>


  	@if($fecha_hosp_dia_extras)

  		<div class="subtitulos " style="height:10px;width:100%;text-align:center;border-style:  none none dotted none;">
  			TRASLADOS EXTRAS
  		</div>

  		<div class="" style="margin-top:15px;">
  			@if($fecha_hosp_dia_extras)
          <div class="box6" style="height:20px;">
    				<div class="subtitulos box3" style="text-align: center; margin-left: 180px; margin-top:-15px;">
    						FECHA (dd-mm-aaaa)
    				</div>

            <div class="subtitulos box3" style="text-decoration:underline; height: 20px;text-align: center; margin-left: 100px; margin-top:-5px;" >
        			UNIDAD FUNCIONAL
        		</div>

            <div class=" letra_diminuta box3" style="width: 50%; margin-top:-15px; margin-left:95px;">
      				CÓDIGO UNIDAD FUNCIONAL
      			</div>

      			<div class=" letra_diminuta box3" style="width: 50%;margin-left:175px; margin-top: -35px; ">
      				CÓDIGO SERVICIO CLÍNICO
      			</div>
          </div>
  				@foreach($fecha_hosp_dia_extras as $key => $fecha_dia_extra)
  					<div class="row" style="height:20px;">
  						<div class="subtitulos box3" style="height:30px; width:20%;padding-left:15px;">
  							{{$key+1}} TRASLADO EXTRA

  						</div>
  						<div class="box3" style="width: 21%; margin-top:-10px;">
  							<div class="box3 " style="width: 43%; padding-left: 8px;">
  								<div class="box4" style="width: 20px; border-style: solid;height:11px;">
  									@if($fecha_dia_extra)
  										{{$fecha_dia_extra}}
  									@endif
  								</div>
  								<div class="box3" style="width:5px;">
  									-
  								</div>
  								<div class="box4" style="width: 20px; border-style: solid;height:11px;">
  									@if($fecha_dia_extra)
  										{{$fecha_hosp_mes_extras[$key]}}
  									@endif
  								</div>
  								<div class="box3" style="width:5px;">
  									-
  								</div>
  								<div class="box4" style="width: 35px; border-style: solid;height:11px; padding-left:5px;">
  									@if($fecha_dia_extra)
  										{{$fecha_hosp_year_extras[$key]}}
  									@endif
  								</div>
  							</div>
  						</div>

  						<div class="box3" style="height:30px; width:34%; margin-top:-7px; margin-left: -22px; border-style:none solid none dotted;">
  							<div class="box5" style="border-style: none none solid none;height:11px;margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
  								@if($unidad_f_extras[$key])
  									{{$unidad_f_extras[$key]}}
  								@endif
  							</div>
  						</div>

  						<div class="box3" style="height:30px; width:20%; margin-top:0px;">
  							<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 50%;" id="">
  								@if($cod_area_extras[$key])
  									{{$cod_area_extras[$key]}}
  								@endif
  							</div>

  							<div class="box4 " style="border-style: solid; height:11px !important; text-align: center; width: 50%; margin-left 20px;" id="">
  								@if($servicio_c_extras[$key])
  									{{$servicio_c_extras[$key]}}
  								@endif
  							</div>
  						</div>
  					</div>

  				@endforeach
  			@endif
  		</div>

  	@endif

    @if($otro_diagnostico_extra)
      <div class="subtitulos " style="height:10px;width:100%;text-align:center;border-style:  none none dotted none;">
        OTROS DIAGNÓSTICOS
      </div>
      <div class="subtitulos" align="center" style="margin-top:5px; margin-left: 250px;">
				<b> CÓDIGO CIE-10</b>
			</div>
        @foreach($otro_diagnostico_extra as $key => $diagnostico_extra)
          <div style="width: 480px;margin-bottom: 0px;">
            <div style="width: 430px;border-style:none solid none none;">
              <div class="box3  subtitulos" style="width: 100px;padding-top:5px;">
                <b>OTRO DIAGNÓSTICO</b>
              </div>
              <div class="box4" style="width: 300px; border-style: solid; height:11px !important; text-align: left; margin-top:5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;padding-left:4px;">
                  {{strtoupper($diagnostico_extra->diagnostico)}}
              </div>
            </div>
            <div class="box4" style="width: 50px; margin-left:450px; margin-top: -16px; margin-bottom:0px; border-style: solid; height:11px; text-align: center; ">
      					{{strtoupper($diagnostico_extra->id_cie_10)}}
      			</div>
          </div>

    		@endforeach
        <div class="subtitulos " style="height:10px;width:100%; margin-bottom:5px;">
        </div>
    @endif

	@if($otro_diagnostico_extra || $fecha_hosp_dia_extras)
		<div>
			<p class="subtitulos" align= "left" style="margin-top:2px;">
				<b>Información protegida por Ley N° 19.628, sobre protección de la vida privada y con garantía del secreto estadístico, establecido en la Ley N° 117.374.</b>
			</p>
		</div>
	@endif

	{{ HTML::script('js/bootstrap.js') }}
</body>
</html>
