<!DOCTYPE html>
<html>
	<head>
	 	<meta charset="UTF-8">
		<style>
			.titulo{
				text-align:center;
				text-transform: uppercase;
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
			label{
				font-weight:bold;
			}
			legend{
				background-color:white;
				text-align:left;
				font-size:14px;
				border:none;
			}
			fieldset{
				border-style:solid;
				border-width:1px;
				border-color:black;
				padding:32px;
			}
			.margen{
				margin-bottom:24px;
			}
			#procedimiento fieldset{
				width:40%;
			}
			.raya_opcion{
				width:20px;
				border-style:solid;
				border-width:1px;
				border-color:black;
				display:inline-block;
				vertical-align:middle;
			}
			.informacion{
				width:60%;
			}
			.nota_informacion{
				width:35%;
				margin-left:5%;
			}
			.caja{
				border-style:solid;
				border-width:1px;
				border-color:black;
				width:100%;
				min-height:40px;
				padding:12px;
			}
			.otra_pagina{
				page-break-after: always;
			}
			table{
				border-style:solid;
				border-width:1px;
				border-color:black;
				width:100%;
				padding:0px;
				border-collapse: collapse;
			}
			td,th{
				border-style:solid;
				border-width:1px;
				border-color:black;
				margin:0px;
				padding:12px;
			}
			th{
				text-align:left;
			}
			.col25{
				width:25%;
			}
			.col33{
				width:33%;
			}
			ul{
				font-weight:normal;
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
	</head>
	<body>
		<div class="logo">
			<img src="{{$formulario->logo_hospital}}">
		</div>
		<div class="hospital">
			<h1>{{$formulario->nombre_hospital}}</h1>
		</div>
		<!-- Título -->
		<div class="titulo margen">
			<h3>Consentimiento informado para interrupción voluntaria del embarazo para mujeres adultas adolescentes entre 14 y 18 años y mujeres con discapacidad no declaradas interdictas </h3>
		</div>
		<!-- Datos personales -->
		<div class="datos_personales margen">
			<div class="izquierda">
				<label>Nombre paciente:</label>
				<span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
			</div>
			<div class="derecha">
				<label>Rut:</label>
				<span>{{$formulario->run}}-{{$formulario->dv}}</span>
			</div>
			<div class="limpiar"></div>
			<div class="izquierda">
				<label>Nombre del establecimiento de salud:</label>
				<span>{{$formulario->nombre_establecimiento}}</span>
			</div>
			<div class="derecha">
				<label>Nº ficha clínica:</label>
				<span>{{$formulario->ficha_clinica}}</span>
			</div>
			<div class="limpiar"></div>
			<div class="izquierda">
				<label>Región:</label>
				<span>{{$formulario->nombre_region}}</span>
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
		<!-- Textos consentimientos -->
		<div class="consentimiento margen">
			<p><b>Estoy en conocimiento que</b> presento un embarazo cuyas características me permiten solicitar la Interrupción Voluntaria de éste, según lo previsto en la Ley Nº 21.030, y por ello manifiesto mi voluntad de acceder a este procedimiento.</p>
			<p><b>Declaro que</b> se me ha entregado y explicado por parte del equipo médico, toda la información sobre el procedimiento que se me realizará y que corresponde al que indico a continuación:</p>
		</div>
		<!-- Procedimiento -->
		<div class="procedimiento margen">
			<fieldset id="procedimiento">
				<legend><b>Procedimiento</b> (Marcar el o los procedimiento(s) que corresponda(n))</legend>
				<fieldset id="medicamentoso" class="izquierda">
					<legend><b>Medicamentoso</b> (incluida vía de administración)</legend>
					<div>
						<input type="checkbox" @if($formulario->mifepristona == true) checked @endif> Mifepristona
					</div>
					<div>
						<input type="checkbox" @if($formulario->misoprostol == true) checked @endif> Misoprostol
					</div>
				</fieldset id="instrumental" class="derecha">
				<fieldset>
					<legend><b>Instrumental</b></legend>
					<div>
						<input type="checkbox" @if($formulario->aspiracion_endouterina == true) checked @endif> Aspiración endouterina (manual o eléctrica)<br>
						<input type="checkbox" @if($formulario->legrado_uterino == true) checked @endif> Legrado uterino
					</div>
					<div>
						<input type="checkbox" @if($formulario->dilatacion_evacuacion_uterina == true) checked @endif> Dilatación y evacuación uterina<br>
						<input type="checkbox" @if($formulario->induccion_parto == true) checked @endif> Inducción de parto prematuro<br>
						<input type="checkbox" @if($formulario->cesarea == true) checked @endif> Cesárea
					</div>
				</fieldset>
				<div class="limpiar"></div>
				<p>NOTA: <b>El profesional</b> que solicita el Consentimiento Informado deberá marcar con una X el o los procedimiento(s) que corresponda(n)</p>
			</fieldset>
		</div>
		<!-- Información -->
		<div class="nota_informacion derecha">
			<p>NOTA: En los temas a continuación, solicitar a <b>la mujer</b> que firma el Consentimiento Informado, marcar con una X la información recibida</p>
		</div>
		<div class="informacion margen">
			<fieldset>
				<legend><b>He recibido información sobre</b></legend>
				<div>
					<hr class="raya_opcion">
					Riesgos más frecuentes del procedimiento
				</div>
				<div>
					<hr class="raya_opcion">
					Efectos secundarios o complicaciones posibles y su manejo
				</div>
			</fieldset>
		</div>
		
		<div class="informacion margen">
			<fieldset>
				<legend><b>También me han informado que este procedimiento se hará de forma</b></legend>
				<div>
					<hr class="raya_opcion">
					Ambulatoria
				</div>
				<div>
					<hr class="raya_opcion">
					Hospitalizada
				</div>
			</fieldset>
		</div>
		<!-- Notas finales -->
		<div class="notas_finales">
			<p><b>Se me ha explicado</b> el tipo de sedación y/o anestesia (local o general), incluidos sus riesgos, y que recibiré medicamentos para tratar el dolor, según lo requiera.</p>
			<p><b>Entiendo que el procedimiento que se me realizará puede ser modificado por decisión médica durante la realización de éste, por razones clínicas que se presenten en el momento, con el fin de resguardar mi salud.</b></p>
		</div>
		
		<div class="otra_pagina"></div>
		<!--  página 2 -->
		<p><b>Me ha explicado también que</b>, una vez al alta:</p>
		<div class="informacion margen izquierda">
			<label>Debo consultar en caso de:</label>
			<div class="caja">
				{{$formulario->consultas}}
			</div>
		</div>
		<div class="nota_informacion derecha">
			<p>NOTA: En los temas a continuación, solicitar a <b>la mujer</b> que firma el Consentimiento Informado, marcar con una X la información recibida</p>
		</div>
		<div class="limpiar"></div>
		<div class="informacion margen">
			<label>Seré controlada en:</label>
			<div class="caja">
				{{$formulario->controles}}
			</div>
		</div>
		<div class="informacion margen">
			<label>En caso de dudas o consultas, debo contactar a (indicar nombre o cargo de la persona, teléfono u otra forma de contacto):</label>
			<div class="caja">
				{{$formulario->dudas}}
			</div>
		</div>
		
		<div class="consentimiento margen">
			<p><b>He comprendido la información</b> que se me ha entregado, teniendo a la vista un documento informativo del procedimiento que se utilizará; he tenido la posibilidad de aclarar las dudas y hacer preguntas las que me han sido respondidas a mi total conformidad.</p>
		</div>
		
		<div class="consentimiento margen">
			<p><b>Entiendo también que puedo cambiar de opinión y anular este Consentimiento en cualquier momento, antes del procedimiento, sin que ello afecte la atención a la que tengo derecho.</p>
		</div>
		
		<table class="margen">
			<tr>
				<th>Nombre de la paciente</th>
				<td colspan="3"></td>
			</tr>
			<tr>
				<th class="col25">Rut</th>
				<td class="col25"></td>
				<th rowspan="2" class="col25">Firma</th>
				<td rowspan="2" class="col25"></td>
			</tr>
			<tr>
				<th>Fecha de nacimiento</th>
				<td ></td>
			</tr>
		</table>
		
		<table class="margen">
			<tr>
				<th>Nombre del médico o profesional que aplica el consentimiento informado</th>
				<td class="col33"></td>
			</tr>
			<tr>
				<th>Rut</th>
				<td></td>
			</tr>
			<tr>
				<th>Firma</th>
				<td></td>
			</tr>
		</table>
		
		<table class="margen">
			<tr>
				<th>Nombre del ministro de fe, asistente para la lectura o facilitador intercultural</th>
				<td class="col33"></td>
			</tr>
			<tr>
				<th>Rut</th>
				<td></td>
			</tr>
			<tr>
				<th>Firma</th>
				<td></td>
			</tr>
		</table>
		
		<ul class="margen">
			<li>Si en el proceso de firma del Consentimiento Informado participa un tercero -ya sea como ministro de Fe, asistente para la lectura del documento o facilitador intercultural- debe quedar individualizado.</li>
			<li>Si la persona no sabe escribir, puede poner su huella digital.</li>
			<li>Firmar el documento en duplicado, dejando una copia en la Ficha Clínica y entregando otra a la paciente.</li>
			<li>Entiendo también que puedo cambiar de opinión y anular este Consentimiento en cualquier momento, antes del procedimiento, sin que ello afecte la atención a la que tengo derecho.</li>
		</ul>
		<div class="izquierda">
			<label>Ciudad:</label> <span>_______________________</span>
		</div>
		<div class="derecha">
			<label>Fecha:</label> <span>_______________________</span>
		</div>
		
	</body>
</html>