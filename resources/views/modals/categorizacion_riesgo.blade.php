<div id="modalFormularioRiesgo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<input type="hidden" name="id_caso" class="id_caso">
		<div class="modal-dialog" style="width: 80%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="modal-title" style="font-size:20px;">Formulario de Riesgo - Dependencia</div>
				</div>

				<div class="row" style="margin: 0;">
					<div class="col-sm-12 control-label" style="font-size:15px;">CUIDADOS QUE IDENTIFICAN DEPENDENCIA <div>
				</div>

				<div class="modal-body">

					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Cambio de Ropa de cama y/o personal, o Cambio de Pañales, o toallas o apositos higenicos</label>
							<div class="col-sm-6">
								<select name="dependencia1" id="dependencia1" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario receptor de estos cuidados básicos, requeridos 3 veces o más(con/sin participación de la familia)">3 pts. </option>
									<option value="2" data-subtext="Usuario receptor de estos cuidados básicos 2 veces al día (con/sin participación de la familia)">2 pts.</option>
									<option value="1" data-subtext="Usuario y familia realizan estos cuidados con ayuda y supervisión, cualquiera sea la frecuencia">1 pts.</option>
									<option value="0" data-subtext="Usuario realiza solo el auto cuidado de cambio de ropa o cambio de pañal, toallas o apósitos higienicos">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Movilización y Transporte(levantada, deambulación y cambio de posición) </label>
							<div class="col-sm-6">

								<select name="dependencia2" id="dependencia2" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario no se levanta y requiere cambio de posición en cama, 10 o más veces al día con/sin participación de familia">3 pts. </option>
									<option value="2" data-subtext="Usuario es levantado a silla y requiere de cambio de posición, entre 4 a 9 veces al día sin/con participación de familia">2 pts.</option>
									<option value="1" data-subtext="Usuario se levanta y deambula con ayuda y se cambia de posición en cama, solo o con ayuda de familia">1 pts.</option>
									<option value="0" data-subtext="Usuario deambula sin ayuda y se moviliza solo en cama">0 pts.</option>

								</select>

							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados de Alimentación: </label>
							<label for="horas" class="col-sm-4 control-label"> Oral, Enteral o Parenteral  </label>
							<div class="col-sm-6">

								<select name="dependencia3" id="dependencia3" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario recibe alimentación y/o hidratación por vía parenteral total/parcial y requiere control de ingesta oral">3 pts. </option>
									<option value="32" data-subtext="Usuario recibe alimentación por vía enteral permanente o discontinua">3 pts.</option>
									<option value="2" data-subtext="Usuario recibe alimentación por vía oral, con asistencia del personal de enfermería">2 pts.</option>
									<option value="1" data-subtext="Usuario se alimenta por vía oral, con ayuda y supervisión'">1 pts.</option>
									<option value="0" data-subtext="Usuario se alimenta sin ayuda">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados de Eliminación: </label>
							<label for="horas" class="col-sm-4 control-label">  Orina, Deposiciones </label>
							<div class="col-sm-6">

								<select name="dependencia4" id="dependencia4" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario elimina a través de sonda, prótesis, procedim, dialiticos, colectires adhesivos o pañales">3 pts. </option>
									<option value="2" data-subtext="Usuario elimina por vía natural y se le entregan o colocan al usuario los colectores(chata o pato)">2 pts.</option>
									<option value="1" data-subtext="Usuario y familia realizan recolección de egresos con ayuda o supervisión">1 pts.</option>
									<option value="0" data-subtext="Usuario usa colectores(chata o pato) sin ayuda y/o usa WC">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Apoyo Psicosocial y Emocional: </label>
							<label for="horas" class="col-sm-4 control-label"> a usuario receptivo, angustiado, triste, agresivo, evasivo </label>
							<div class="col-sm-6">
								<select name="dependencia5" id="dependencia5" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario recibe más de 30 minutos de apoyo durante turno">3 pts. </option>
									<option value="2" data-subtext="Usuario recibe entre 15 y 30 min. de apoyo durante turno">2 pts.</option>
									<option value="1" data-subtext="Usuario recibe entre 5 y 14 min. de apoyo durante el turno">1 pts.</option>
									<option value="0" data-subtext="Usuario recibe menos de 5 min. de apoyo durante el turno">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Vigilancia: </label>
							<label for="horas" class="col-sm-4 control-label">  por alteración conciencia, riesgo caída o riesgo incidente (desplazamiento, retiro de vías, sondas, tubos), limitación física o por edad o de los sentidos </label>
							<div class="col-sm-6">
								<select name="dependencia6" id="dependencia6" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario con alteración de conciencia">3 pts. </option>
									<option value="32" data-subtext="Usuario con riesgo de caída o incidentes">3 pts.</option>
									<option value="2" data-subtext="Usuario conciente pero intranquilo y c/riesgo caída o incidente">2 pts.</option>
									<option value="1" data-subtext="Usuario conciente pero con inestabilidad de la marcha o no camina por alteración física">1 pts.</option>
									<option value="0" data-subtext="Usuario conciente, orientado, autónomo">0 pts.</option>

								</select>

							</div>
						</div>



					</div>

					<div class="row" style="margin: 0;">
						<div for="horas" class="col-sm-12 control-label" style="font-size: 15px;">CUIDADOS ESPECIFICOS DE ENFERMERIA QUE IDENTIFICAN RIESGO <div>
					</div>

					<div class="modal-body">
					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Medición diaria de Signos Vitales (2 o mas parámetros simultáneos): </label>
							<label for="horas" class="col-sm-4 control-label">  Presión arterial, temperatura corporal, frecuencia cardiaca, frecuencia respiratoria, nivel de dolor y otros  </label>
							<div class="col-sm-6">
								<select name="riesgo1" id="riesgo1" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Control por 8 veces y más (cada 3 horas o más frecuente)">3 pts. </option>
									<option value="2" data-subtext="Control por 4 a 7 veces (cada 4, 5, 6 o 7 horas)">2 pts.</option>
									<option value="1" data-subtext="Control por 2 a 3 veces (cada 8, 9, 10, 11 o 12 horas">1 pts.</option>
									<option value="0" data-subtext="Control por 1 vez (cada 13 a cada 24 horas">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Balance hidrico: </label>
							<label for="horas" class="col-sm-4 control-label">Medición de Ingreso y Egreso realizado por profesionales en las ultimas 24 hrs.</label>
							<div class="col-sm-6">
								<select name="riesgo2" id="riesgo2" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Balance hidrico por 6 veces o más (cada 4 horas o más frecuente)">3 pts. </option>
									<option value="2" data-subtext="Balance hidrico por 2 a 5 veces (cada 12, 8 ,6 o 5 horas)">2 pts.</option>
									<option value="1" data-subtext="Balance hidrico por 1 vez (cada 24 horas o menor de cada 12 horas)">1 pts.</option>
									<option value="0" data-subtext="No requiere">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados en Oxigenoterapia: </label>
							<label for="horas" class="col-sm-4 control-label">Por cánula de traqueostomía, tubo endotraqueal, cámara, halo, máscara,
							sonda o bigotera.</label>
							<div class="col-sm-6">
								<select name="riesgo3" id="riesgo3" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Administración de oxígeno por tubo o cánula endotraqueal">3 pts. </option>
									<option value="2" data-subtext="Administración de oxígeno por máscara">2 pts.</option>
									<option value="1" data-subtext="Administración de oxígeno por canula nasal">1 pts.</option>
									<option value="0" data-subtext="Sin oxigenoterapia">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados diarios de la Vía Aérea: </label>
							<label for="horas" class="col-sm-4 control-label">Aspiración de secreciones y Apoyo kinesico requerido</label>
							<div class="col-sm-6">
								<select name="riesgo4" id="riesgo4" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario con vía aérea artificial (tubo o cánula endotraqueal)">3 pts. </option>
									<option value="32" data-subtext="Usuario con vía aérea artif. o natural con 4 o + aspiraciones secreciones fraqueales y/o kinésico + de 4 veces">3 pts. </option>
									<option value="2" data-subtext="Usuario respira por vía natural y requiere de 1 a 3 aspiraciones de secreciones y/o apoyo kinésico 2 a 3 veces al día">2 pts.</option>
									<option value="1" data-subtext="Usuario respira por vía natural, sin aspiración de secreciones y/o apoyo kinésico 1 vez al día">1 pts.</option>
									<option value="0" data-subtext="Usuario no requiere de apoyo ventilatorio adicional">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Intervenciones profesionales: </label>
							<label for="horas" class="col-sm-4 control-label">Intervenciones quirurgicas y procedimientos invasivos, tales como punciones, toma de muestras, instalaciones de las vías, sondas y tubos .</label>
							<div class="col-sm-6">
								<select name="riesgo5" id="riesgo5" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="1 o más procedimientos invasivos realizadosmédicos en ultimas 24 horas">3 pts. </option>
									<option value="32" data-subtext="3 o más procedimientos invasivos realizados por enfermeras en últimas 24 horas">3 pts. </option>
									<option value="21" data-subtext="1 o 2 procedimientos invasivos realizados por enfermeras en últimas 24 horas">2 pts.</option>
									<option value="22" data-subtext="1 o más procedimientos invasivos realizados por otros profesionales  en últimas 24 horas">2 pts.</option>
									<option value="0" data-subtext="No se realizan procedimientos invasivos en 24 horas">0 pts.</option>

								</select>

							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Cuidados de Piel y Curaciones: </label>
							<label for="horas" class="col-sm-4 control-label">Prevención de lesiones de la piel y curaciones o refuerzo de apósitos</label>
							<div class="col-sm-6">
								<select name="riesgo6" id="riesgo6" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Curación o refuerzo de apósitos 3 o más veces en el día, independiente de la complejidad de la técnica empleada">3 pts. </option>
									<option value="21" data-subtext="Curación o refuerzo de apósitos 1 a 2 veces en el día, independiente de la complejidad de la técnica empleada">2 pts.</option>
									<option value="22" data-subtext="Prevención compleja de lesiones de la piel: uso de colchón antiescaras, piel de cordero u otro">2 pts.</option>
									<option value="1" data-subtext="Prevención corriente de lesiones: aseo, lubricación y protección de zonas propensas">1 pts.</option>
									<option value="0" data-subtext="No requiere">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">7.- Administración de Tratamiento Farmacologico: </label>
							<label for="horas" class="col-sm-4 control-label">Por vía inyectable EV, inyectable no EV, y por otras vías tales como oral, ocular, aérea, etc </label>
							<div class="col-sm-6">
								<select name="riesgo7" id="riesgo7" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Tratamiento intratecal e inyectable endovenoso, directo o por fleboclisis">3 pts. </option>
									<option value="32" data-subtext="Tratamiento dirario con 5 o más fármacos distintos, administrados por diferentes vías no inyectable">3 pts. </option>
									<option value="21" data-subtext="Tratamiento inyectable no endovenoso (IM, SC, ID)">2 pts.</option>
									<option value="22" data-subtext="Tratamiento diario con 2 a 3 fármacos, administrados por diferentes vías no inyectable">2 pts.</option>
									<option value="1" data-subtext="Tratamiento con 1 fármaco, administrado por diferentes vías no inyectable">1 pts.</option>
									<option value="0" data-subtext="Sin tratamiento farmacológico">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">8.- Presencia de Elementos Invasivos: </label>
							<label for="horas" class="col-sm-4 control-label">Catéteres y vías vasculares centrales y/o periféricas. Manejo de sondas urinarias y digestivas a permanencia. Manejo de drenajes intracavitareos o percutáneos</label>
							<div class="col-sm-6">
								<select name="riesgo8" id="riesgo8" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Con 3 o más elementos invasivos (sondas, drenajes, cateteres o vías vasculares)">3 pts. </option>
									<option value="21" data-subtext="Con 1 o 2 elementos invasivos (sonda, drenaje, vía arterial, cateter o vía venosa central)">2 pts.</option>
									<option value="22" data-subtext="Con 2 o más vías venosas perféricas (mariposas, teflones, agujas)">2 pts.</option>
									<option value="1" data-subtext="Con 1 vías venosa periférica (mariposas, teflones, agujas)">1 pts.</option>
									<option value="0" data-subtext="Sin elementos invasivos">0 pts.</option>

								</select>
							</div>
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<a id="" type="" class="btn btn-primary"  onclick="btnRiesgoDependencia()">Aceptar</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
</div>



<div id="modalFormularioRiesgo2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<input type="hidden" name="id_caso" class="id_caso">
	<div class="modal-dialog" style="width: 80%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="modal-title" style="font-size:20px;">Formulario de Riesgo - Dependencia</div>
				</div>

				<div class="row" style="margin: 0;">
					<div class="col-sm-12 control-label" style="font-size:15px;">CUIDADOS QUE IDENTIFICAN DEPENDENCIA <div>
				</div>

				<div class="modal-body">

<div class="row" style="margin: 0;">
	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">1.- Cuidados en Confort y Bienestar: </label>
		<label for="horas" class="col-sm-4 control-label">Cambio de Ropa de cama y/o personal, o Cambio de Pañales, o toallas o apositos higenicos</label>
		<div class="col-sm-6">
			<select name="dependencia1_2" id="dependencia1" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Usuario receptor de estos cuidados básicos de confort por incapacidad física o mental para realizarlos, con o sin participación de familia ">3 pts. </option>
				<option value="2" data-subtext="Usuario requiere de ayuda y supervisión para realizar estos cuidados (uso de objetos peligrosos o restringidos)">2 pts.</option>
				<option value="1" data-subtext="Usuario desmotivado requiere de impulso y ayuda para realizar estos cuidados">1 pts.</option>
				<option value="0" data-subtext="Usuario realiza solo el auto cuidado de bañarse, lavarse cara/boca/manos, ponerse y quitarse ropa ">0 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">2.- Cuidados en deambulación: </label>
		<label for="horas" class="col-sm-4 control-label">Movilización y transporte (levantada, deambulación, cambio posición en la cama) </label>
		<div class="col-sm-6">

			<select name="dependencia2_2" id="dependencia2" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Usuario no se levanta y es receptor de cambio de posición en cama, 10 o más veces al día, con o sin participación de familia
">3 pts. </option>
				<option value="2" data-subtext="Usuario es levantado a silla y/o acompañado en deambulación y requiere de ayuda y supervisión">2 pts.</option>
				<option value="1" data-subtext="Usuario requiere de impulso y motivación para levantada y/o en deambulación y en cambio de posición">1 pts.</option>
				<option value="0" data-subtext="Usuario se levanta y deambula sin ayuda y se moviliza solo">0 pts.</option>

			</select>

		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">3.- Cuidados en Alimentación e Hidratación: </label>
		<label for="horas" class="col-sm-4 control-label"> Oral, Enteral o Parenteral  </label>
		<div class="col-sm-6">

			<select name="dependencia3_2" id="dependencia3" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Usuario recibe alimentación y/o hidratación x vía parenteral total/parcial o vía enteral permanente o discontínua">3 pts. </option>
				<option value="32" data-subtext="Usuario recibe alimentación por vía oral la que es administrada con o sin participación de familia">3 pts.</option>
				<option value="2" data-subtext="Usuario se alimenta por vía oral, con supervisión estrecha de ingesta y post ingesta">2 pts.</option>
				<option value="1" data-subtext="Usuario se alimenta por vía oral o enteral, con ayuda y supervisión moderada">1 pts.</option>
				<option value="0" data-subtext="Usuario se alimenta sin ayuda, pero con supervisión indirecta">0 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">4.- Cuidados de Eliminación: </label>
		<label for="horas" class="col-sm-4 control-label">  Orina, Vómitos, Deposiciones	 </label>
		<div class="col-sm-6">

			<select name="dependencia4_2" id="dependencia4" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Usuario elimina egresos por sonda, colectores adhesivos o pañales y/o vómitos">3 pts. </option>
				<option value="2" data-subtext="Usuario elimina egresos por vía natural y se le entregan o colocan colectores (chata, pato, otro)">2 pts.</option>
				<option value="1" data-subtext="Usuario elimina egresos en el baño con ayuda y supervisión">1 pts.</option>
				<option value="0" data-subtext="Usuario elimina egresos en el baño o usa colectores (chata, pato, otro) sin ayuda">0 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">5.- Vigilancia en Riesgo caída o incidentes: </label>
		<label for="horas" class="col-sm-4 control-label"> por Alteración de conciencia, Limitación física o por edad o de los sentidos, Riesgo de desplazamiento o retiro vías, sondas, tubos, Presencia de Riesgo psicoterapéutico (Automedicación, Consumo drogas, Alcohol)		 </label>
		<div class="col-sm-6">
			<select name="dependencia5_2" id="dependencia5" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Usuario c/alteración de conciencia (desorientado, confuso, excitado, agresivo) y/o bajo efecto fármacos y/o con 2 o más elementos invasivos">3 pts. </option>
				<option value="32" data-subtext="Usuario consciente con inquietud psicomotora (con discapacidad física/mental y/o efectos secundarios a fármacos)">3 pts.</option>
				<option value="2" data-subtext="Usuario consciente pero con riesgo de caídas o retiro de elementos invasivos (incidentes) por edad >70 años o alteración física-mental o fármacos con 1 o más elementos invasivos">2 pts.</option>
				<option value="1" data-subtext="Usuario consciente, orientado, autovalente, eventual riesgo de accidente ">1 pts.</option>

			</select>
		</div>
	</div>





</div>

<div class="row" style="margin: 0;">
	<div for="horas" class="col-sm-12 control-label" style="font-size: 15px;">CUIDADOS IDENTIFICAN RIESGO <div>
</div>

<div class="modal-body">
<div class="row" style="margin: 0;">
	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">6.- Medición diaria de riesgos vitales (2 o más simultáneos): </label>
		<label for="horas" class="col-sm-4 control-label">  PA, T°, FC, FR, Oximetría y parámetros invasivos (PAM, PV, PP) y otros  </label>
		<div class="col-sm-6">
			<select name="riesgo1_2" id="riesgo1" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Control por 8 veces y más (cada 3 o más frecuente)">3 pts. </option>
				<option value="2" data-subtext="Control por 4 a 7 veces (cada 4, 5, 6 o 7 horas)">2 pts.</option>
				<option value="1" data-subtext="Control por 2 a 3 veces (cada 8, 9, 10, 11 o 12 horas">1 pts.</option>
				<option value="0" data-subtext="Control por 1 vez (cada 13 a cada 24 horas">0 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">7.- Balance Hídrico y/o control de ingresos y egresos: </label>
		<label for="horas" class="col-sm-4 control-label">Balance de Ingresos y Egresos realizado por Profesionales (med, enf) en últimas 24 horas.</label>
		<div class="col-sm-6">
			<select name="riesgo2_2" id="riesgo2" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Balance Hídrico y/o control de ingresos y egresos por 2 a 3 veces por día">3 pts. </option>
				<option value="2" data-subtext="Balance Hídrico y/o control de ingresos y egresos por 1 vez (cada 24 horas o menos de cada 12 horas)">2 pts.</option>
				<option value="0" data-subtext="No requiere y/o control de ingresos y egresos">0 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">8.- Cuidados integridad de la piel: </label>
		<label for="horas" class="col-sm-4 control-label">Prevención de lesiones de la piel, heridas, rasguños y curaciones simples o avanzadas.</label>
		<div class="col-sm-6">
			<select name="riesgo3_2" id="riesgo3" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Curación 3 o más veces en el día, independiente de la complejidad de técnica empleada">3 pts. </option>
				<option value="21" data-subtext="Curación 1 a 2 veces en el día, independiente de la complejidad de técnica empleada">2 pts.</option>
				<option value="22" data-subtext="Prevención compleja de lesiones de la piel: uso de colchón antiescaras">2 pts.</option>
				<option value="1" data-subtext="Prevención corriente de lesiones: aseo, lubricación y protección de zonas propensas, valoración piel">1 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">9.- Intervención en agitación psicomotora y auto o hetero agresión: </label>
		<label for="horas" class="col-sm-4 control-label"> Procedimiento terapéutico que debe ser realizado por una persona debidamente capacitada, según norma técnica del MINSAL Contención en Psiquiatría</label>
		<div class="col-sm-6">
			<select name="riesgo4_2" id="riesgo4" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Usuario receptor de contención emocional + ambiental + farmacológica +  física 1 o más veces durante el día">3 pts. </option>
				<option value="32" data-subtext="Usuario receptor de contención emocional + ambiental + farmacológica 1 o más veces durante el día">3 pts. </option>
				<option value="21" data-subtext="Usuario receptor de contención emocional y ambiental 1 o más veces durante el día">2 pts.</option>
				<option value="22" data-subtext="Usuario receptor de contención emocional 1 o más veces durante el día">2 pts.</option>
				<option value="1" data-subtext="Usuario recibe apoyo durante el turno (solo conversar, acompañar y escuchar)">1 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">10.- Intervención en riesgo fuga: </label>
		<label for="horas" class="col-sm-4 control-label">Acciones destinadas a la observación y al cuidado del  paciente con ideación y/o gestos de fuga.</label>
		<div class="col-sm-6">
			<select name="riesgo5_2" id="riesgo5" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Usuario requiere observación permanente, con visión directa de equipo de enfermería, con espacio delimitado">3 pts. </option>
				<option value="32" data-subtext="Usuario requiere observación directa de equipo de enfermería y compañía de pares, en participación de actividades grupales">3 pts. </option>
				<option value="2" data-subtext="Usuario requiere observación directa en horarios especiales (visitas, reuniones familiares, interconsultas fuera de la unidad) de 1 o más veces al día">2 pts.</option>
				<option value="1" data-subtext="Usuario requiere observación indirecta del equipo clínico, entendida supervisión habitual en aquellos pacientes que se encuentran sicopatologicamente estables">1 pts.</option>
				<option value="0" data-subtext="No se realizan procedimientos invasivos en 24 horas">0 pts.</option>

			</select>

		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">11.- Intervenciones Profesionales: </label>
		<label for="horas" class="col-sm-4 control-label">Acciones terapéuticas tales cómo Visita enfermería c/cambio plan cuidados, visita médica c/ cambio indicaciones, Psicoeducación y relación ayuda, Procedimientos invasivos y tales como punciones, toma de muestras por vía invasiva, instalaciones de vías, sondas y tubos, etc.</label>
		<div class="col-sm-6">
			<select name="riesgo6_2" id="riesgo6" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="1 o más acciones terapéuticas o procedimientos realizados por médicos en las últimas 24 horas">3 pts. </option>
				<option value="32" data-subtext="2 o más acciones terapéuticas o procedimientos invasivos realizados por enfermeras en las últimas 24 horas">3 pts.</option>
				<option value="21" data-subtext="1 acción terapéutica o procedimiento invasivo realizado por enfermeras en las últimas 24 horas">2 pts.</option>
				<option value="22" data-subtext="1 o más acciones o procedimientos invasivos realizados por otros profesionales en las últimas 24 horas">2 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">12.- Administración de tratamiento farmacológico habitual: </label>
		<label for="horas" class="col-sm-4 control-label">Por vía inyectable EV, injectable no EV, y por otras vías tales como oral, ocular, aérea, rectal, vaginal, etc. </label>
		<div class="col-sm-6">
			<select name="riesgo7_2" id="riesgo7" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Tratamiento inyectable endovenoso, directo o por fleboclisis (con 1 o más fármacos y/o psicofármacos)">3 pts. </option>
				<option value="32" data-subtext="Tratamiento diario con 5 o más fármacos y/o psicofármacos distintos, administrados por diferentes vías no inyectables">3 pts. </option>
				<option value="21" data-subtext="Tratamiento diario con 1 o más fármacos y/o psicofármacos inyectable no endovenoso (IM, SC, ID)">2 pts.</option>
				<option value="22" data-subtext="Tratamiento diario con 2 a 4 fármacos y/o psicofármacos, administrados por diferentes vías no inyectables">2 pts.</option>
				<option value="1" data-subtext="Tratamiento diario 1 fármaco y/o psicofármaco, administrados por diferentes vías no inyectables">1 pts.</option>

			</select>
		</div>
	</div>

	<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">13.- Presencia de elementos no invasivos e invasivos: </label>
		<label for="horas" class="col-sm-4 control-label"> elementos desde punto de vista médico quirúrgico para pacientes hospitalizados en salud mental: cánulas, vías respiratorias, catéteres y vías vasculares centrales, periféricas y arteriales, manejo de drenajes, sondas urinarias y digestivas a permanencia y ostomías</label>
		<div class="col-sm-6">
			<select name="riesgo8_2" id="riesgo8" class="form-control selectpicker" data-show-subtext="true">
				<option value="31" data-subtext="Con 3 o más elementos invasivos (cánulas respiratorias, sondas, drenajes, catéteres o vías vasculares) con o sin oxigenoterapia">3 pts. </option>
				<option value="32" data-subtext="Con 1 o más elementos invasivos (sondas, drenajes, catéteres o vías vasculares) con oxigenoterapia">3 pts.</option>
				<option value="22" data-subtext="Con 1 a 2 elementos invasivos (sonda, drenaje, catéter o vía vascular central o periférica) sin oxigenoterapia">2 pts.</option>
				<option value="1" data-subtext="Con 1 vía venosa periférica (mariposas, teflones, agujas) y sin oxigenoterapia">1 pts.</option>
				<option value="0" data-subtext="Sin elementos invasivos y sin oxigenoterapia">0 pts.</option>

			</select>
		</div>
	</div>

<div class="form-group col-md-12">
		<label for="horas" class="col-sm-2 control-label">14.- Apoyo Emocional y Psicosocial: </label>
		<label for="horas" class="col-sm-4 control-label"> a Usuario Receptivo Angustiado, Triste, Agresivo, Evasivo </label>
		<div class="col-sm-6">
			<select name="riesgo9_2" id="riesgo9" class="form-control selectpicker" data-show-subtext="true">
				<option value="3" data-subtext="Usuario recibe más de 60 minutos de apoyo durante el turno (conversar, acompañar, escuchar) ">3 pts. </option>
				<option value="2" data-subtext="Usuario recibe entre 10 y menos de 60 minutos de apoyo durante el turno (conversar, acompañar, escuchar) ">2 pts.</option>
				<option value="1" data-subtext="Usuario recibe apoyo emocional y psicosocial desde 10 min o menos durante el turno">1 pts.</option>
			

			</select>
		</div>
	</div>

	<div class="modal-footer">
					<a id="" type="" class="btn btn-primary"  onclick="btnRiesgoDependencia2()">Aceptar</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>


</div>

</div>


			</div>
	</div>
</div>
</div>


