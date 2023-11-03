@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>

    var caso_id = '{{$caso}}';

    $(".fechaPC").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});
    // RECARGAR
      var time = new Date().getTime();

      $(document.body).bind("mousemove keypress", function(e) {
          time = new Date().getTime();
      });

      function refresh() {
          if(new Date().getTime() - time >= 900000){//900000 => 15 minutos
              bootbox.confirm({				
                  message: "<h4>Han pasado 15 minutos de inactividad. <br>¿Desea recargar la pantalla?</h4>",				
                  buttons: {					
                      confirm: {					
                          label: 'Si',					
                          className: 'btn-success'					
                      },					
                      cancel: {					
                          label: 'No',					
                          className: 'btn-danger'					
                      }				
                  },				
                  callback: function (result) {								
                      
                      if(result){					
                        location.reload();
                      }else{
                          setTimeout(refresh, 1000);
                      }				
                  }
              });  
          }else{
              setTimeout(refresh, 1000);
          }
      }

      setTimeout(refresh, 1000);
      //RECARGAR

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">


@stop

@section("miga")
<!-- <li><a href="#">Urgencia</a></li>
<li><a href="#">Documentos</a></li> -->
@stop

@section("section")


@if($sub_categoria == 1)
  <!-- alertas de alergias logica-->
  {{ HTML::script('js/formularios_ginecologia/partograma-alergias-notificator.js') }}

@endif

<style>

	.formulario > .panel-default > .panel-heading {
		background-color: #bce8f1 !important;
	}

	.tt-input{
		width:100%;
	}
	.tt-query {
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	}

	.tt-hint {
		color: #999
	}

	.tt-menu {    /* used to be tt-dropdown-menu in older versions */
		/*width: 430px;*/
		margin-top: 4px;
		/* padding: 4px 0;*/
		background-color: #fff;
		border: 1px solid #ccc;
		border: 1px solid rgba(0, 0, 0, 0.2);
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
			border-radius: 4px;
		-webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
		-moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
			box-shadow: 0 5px 10px rgba(0,0,0,.2);
		overflow-y: scroll;
		max-height: 350px;
	}

	.tt-suggestion {
		/* padding: 3px 20px;*/
		line-height: 24px;
	}

	.tt-suggestion.tt-cursor,.tt-suggestion:hover {
		color: #fff;
		background-color: #1E9966;

	}

	.tt-suggestion p {
		margin: 0;
	}
	.twitter-typeahead{
		width:100%;
	}

	#modalCamas {
		overflow-y:scroll
	}

	.primerNav{
		background-color:#1E9966;
	}
	.primerNav > li > a{
		color: #fff !important;
	}

		.primerNav > li.active > a, .primerNav > li.active > a:hover,.primerNav > li.active > a:focus,.primerNav > li > a:hover, .primerNav > li > a:focus{
			background-color:#c35c6b;
		}

	.segundoNav{
		background-color:#c35c6b
	}

	.segundoNav > li > a{
		color: #fff !important;
	}

		.segundoNav > li.active > a, .segundoNav > li.active > a:hover,.segundoNav > li.active > a:focus, .segundoNav > li > a:hover, .segundoNav > li > a:focus{
			background-color:#bce8f1;
			color: #1e9966 !important;
		}

	.tercerNav{
		background-color:#c35c6b;
		margin-top: -16px;
		/* margin-left: -15px; */
	}
	.tercerNav > li > a{
		color: #fff !important;
	}
		.tercerNav > li.active > a, .tercerNav > li.active > a:hover,.tercerNav > li.active > a:focus,.tercerNav > li > a:hover, .tercerNav > li > a:focus{
			background-color: #bce8f1; 
			color: #1e9966 !important;
		}    
	/*
	.dropdown-menu > li > a {
	color:#399865 !important;
	}

	.btn-default {
	color:#399865 !important;
	}
	*/

</style>
<div class="container">
    <fieldset>
        <legend>Registro clínico de enfermería</legend>
        <div class="row">
            <div class="col-md-6">
                <b>NOMBRE PACIENTE:</b>  {{ $infoPaciente->nombre }} {{ $infoPaciente->apellido_paterno }} {{ $infoPaciente->apellido_materno }}
            </div>
            <div class="col-md-2">
                <b>RUT:</b>  {{ $infoPaciente->rut }}-{{ ($infoPaciente->dv == 10)?'K':$infoPaciente->dv }}
            </div>
            <div class="col-md-4">
                <b>FECHA DE NACIMIENTO:</b>  
                @if($infoPaciente->fecha_nacimiento) {{ Carbon\Carbon::parse($infoPaciente->fecha_nacimiento)->format('d-m-Y') }} ({{ Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) }} AÑOS) @else SIN INFORMACION @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <b>NOMBRE SOCIAL: </b> @if($infoPaciente->nombre_social){{ $infoPaciente->nombre_social }} @else SIN INFORMACION @endif
            </div>
            <div class="col-md-2">
                <b>SEXO: </b> {{ strtoupper($infoPaciente->sexo) }}
            </div>
            <div class="col-md-4">
                <b>PREVISION: </b> {{ $prevision }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <b>TELEFONOS: </b>
                @forelse ($telefonos as $item)
                  ({{ strtoupper($item->tipo) }}) {{$item->telefono}}.
                @empty
                  SIN INFORMACION
                @endforelse
            </div>
        </div>
    </fieldset>
    <br>
    <div id="exTab1" class="col-md-12">
      <ul  class="nav nav-pills primerNav">
		  @if($sub_categoria == 5)
          <li id="iEnfermeria">
			@else
			<li id="iEnfermeria" class="active">
			@endif
			 
              <a  href="#1a" data-toggle="tab">Ingreso de enfermería</a>
          </li>
          <li id="planificacion">
              <a href="#3a" data-toggle="tab">Planificación de los cuidados</a>
          </li>
          <li id="hojaDeEnfermeria">
              <a href="#2a" data-toggle="tab">Registro diario de cuidados</a>
          </li>
          <li id="hojaDeCuracion">
            <a href="#8a" data-toggle="tab">Hoja de curaciones</a>
        </li>
        
        <li id="pertenencias">
          <a href="#10a" data-toggle="tab">Pertenencias</a>
        </li>
       
        <li>
          <div class="dropdown">
            <button style="background-color: #1e9966; color: white;" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
              Formularios
              <span class="caret"></span>
            </button>
            <ul style="background-color:#c35c6b;" class="dropdown-menu" aria-labelledby="dropdownMenu1">
              <li>{{ HTML::link("gestionEnfermeria/$caso/formBarthel", 'Indice Barthel')}}</li>
              <li>{{ HTML::link("gestionEnfermeria/$caso/formGlasgow", 'Escala de Glasgow')}}</li>
              <li>{{ HTML::link("gestionEnfermeria/$caso/formNova", 'Escala Nova')}}</li>
              <li>{{ HTML::link("gestionEnfermeria/$caso/formRiesgoCaida", 'Riesgo Caída')}}</li>
              <li>{{ HTML::link("gestionEnfermeria/$caso/formPacientePostrado", 'Paciente Dismovilizado')}}</li>
               {{-- //formulario nuevo macdems --}}
               <li>{{ HTML::link("gestionEnfermeria/$caso/formMacdems", 'Escala Macdems')}}</li>
               <li>{{ HTML::link("gestionEnfermeria/$caso/formRiesgoUlcera", 'Riesgo de Lesiones')}}</li>

            </ul>
          </div>
        </li>
		@if($sub_categoria == 1)
               {{-- 1 => GINECOLÓGICA --}}
		<li>
          <div class="dropdown">
            <button style="background-color: #1e9966; color: white;" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
              Matronería
              <span class="caret"></span>
            </button>
            <ul style="background-color:#c35c6b;" class="dropdown-menu" aria-labelledby="dropdownMenu2">
               <li>{{ HTML::link("formularios-ginecologia/$caso/entrega-documentos-alta", 'Entrega Documentos Alta')}}</li>
               <li>{{ HTML::link("formularios-ginecologia/$caso/consentimiento-informado-interrupcion-embarazo", 'Consentimiento informado de interrupción del embarazo')}}</li>
               <li>{{ HTML::link("formularios-ginecologia/$caso/epicrisis-interrupcion-gestacion-iii-trimestre", 'Epicrisis interrupción de gestación III trimestre')}}</li>
			@if($tiene_partograma)
               <li>{{ HTML::link("formularios-ginecologia/$caso/partograma", 'Partograma')}}</li>
			  @endif
               <li>{{ HTML::link("formularios-ginecologia/$caso/solicitud-transfusion-productos-sanguineos", 'Solicitud de transfusión de productos sanguíneos')}}</li>
			   <li>{{ HTML::link("formularios-ginecologia/$caso/protocolo-de-parto", 'Historia clínica perinatal')}}</li>
            </ul>
          </div>
        </li>
		@endif
		@if($sub_categoria == 5)
		<li id="partograma5" class="active">
			<a href="#tab_partograma" data-toggle="tab">Partograma</a>
		</li>
		@endif
        <li id="epicrisis">
          <a href="#9a" data-toggle="tab">Epicrisis</a>
        </li>
        {{-- 2 => GINECO - OBSTETRICO --}}
        @if($sub_categoria == 2)
          <li id="EgresoRn">
            <a href="#12a" data-toggle="tab">Egreso recién nacido</a>
          </li>
        @endif
      <li id="diptico">
        <a href="#11a" data-toggle="tab">Dipticos y documentos</a>
      </li>
      </ul>

      <div class="tab-content clearfix">
		@if($sub_categoria == 5)
		<div class="tab-pane" id="1a">
		@else
        <div class="tab-pane active" id="1a">
		@endif
            @include('Gestion.gestionEnfermeria.ingresoEnfermeria')
        </div>
        <div class="tab-pane" id="2a">
            @include('Gestion.gestionEnfermeria.hojaEnfermeria')
        </div>
        <div class="tab-pane" id="3a">
            @include('Gestion.gestionEnfermeria.planificacionCuidados')
        </div>
        <div class="tab-pane" id="8a">
            <!-- <h3>Uso diario.</h3> -->
            @include('Gestion.gestionEnfermeria.hojaCuraciones')
        </div>
        <div class="tab-pane" id="10a">
          @include('Gestion.gestionEnfermeria.pertenencias')
        </div>
        <div class="tab-pane" id="9a">
          @include('Gestion.gestionEnfermeria.epicrisis')
        </div>
        <div class="tab-pane" id="12a">
          <div id="app">
            <egresoreciennacidogineco :id-caso="{{ $caso }}"></egresoreciennacidogineco>
          </div>
        </div>
        <div class="tab-pane" id="11a">
          @include('Gestion.gestionEnfermeria.Diptico.diptico')
        </div>
		@if($sub_categoria == 5)
		<div class="tab-pane active" id="tab_partograma">
          @include('FormulariosGinecologia.partograma',["tab" => true])
        </div>
		@endif
      </div>
    </div>
  </div>



  <script src="{{URL::to('/')}}/js/app.js"></script>
@stop
