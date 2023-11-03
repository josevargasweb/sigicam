@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>

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
        <legend>Registro medico del paciente</legend>
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
          <li id="gestionDiagnosticos" class="active">
              <a  href="#1a" data-toggle="tab">Diagnósticos Médicos</a>
          </li>
          <li id="gestionIndicaciones">
              <a href="#3a" data-toggle="tab">Indicaciones Medicas</a>
          </li>
          <li id="registroMedico">
              <a href="#5a" data-toggle="tab">Evolución Medica</a>
          </li>
          <li id="gmExamenes">
              <a  href="#2a" data-toggle="tab">Solicitud de exámenes</a>
          </li>
          <li id="gestionInterconsulta">
              <a href="#6a" data-toggle="tab">Interconsultas</a>
          </li>
          <li id="fomularios">
              <a href="#4a" data-toggle="tab">Formularios</a>
          </li>
      </ul>
      <div class="tab-content clearfix">
        <div class="tab-pane active" id="1a">
            @include('Gestion.gestionMedica.gestionDiagnostico')
        </div>
        <div class="tab-pane" id="2a">
            @include('Gestion.gestionMedica.gestionExamenes')
        </div>
        <div class="tab-pane" id="3a">
            @include('Gestion.gestionMedica.gestionIndicaciones')
        </div>
        <div class="tab-pane" id="4a">
            @include('Gestion.gestionMedica.gestionFormularios')
        </div>
        <div class="tab-pane" id="5a">
            @include('Gestion.gestionMedica.registroClinico')
        </div>
        <div class="tab-pane" id="6a">
            @include('Gestion.gestionMedica.gestioninterconsulta')
        </div>
      </div>
    </div>
  </div>




@stop
