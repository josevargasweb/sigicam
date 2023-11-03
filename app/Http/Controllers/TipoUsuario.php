<?php namespace App\util{

	class TipoUsuario{
		public static $ADMINSS="admin_ss";
		public static $ADMIN="admin";
		public static $USUARIO="usuario";

		const ADMIN = 'admin';//gestor
		const ADMINSS = 'admin_ss';
		const ADMINIAAS = 'admin_iaas';
		const USUARIO = 'usuario';
		const GESTION_CLINICA = 'gestion_clinica';//enfermera
		const MONITOREO_SSVQ = 'monitoreo_ssvq';
		const DIRECTOR = 'director';//director
		const IAAS = 'iaas';
		const VISUALIZADOR = 'visualizador';
		const ESTADISTICAS = 'estadisticas';
		const CENSO = 'censo';
		const MASTER = 'master';
		const PABELLON = 'pabellon';
		const ENFERMERA_P = 'enfermeraP';
		const ADMINCOMERCIAL = 'admin_comercial';
		const MEDICO = 'medico';
		const OIRS = "oirs";
		const MEDICO_JEFE_DE_SERVICIO = "medico_jefe_servicio";
		const MASTERSS = "master_ss";

		public static function getNombre($tipo){
			if($tipo == self::ADMINSS) return "Administrador servicio de salud";
			if($tipo == self::ADMINIAAS) return "Administrador de IAAS";
			if($tipo == self::ADMIN) return "Gestor de camas";
			if($tipo == self::USUARIO) return "Urgencia";//era Usuario
			if($tipo == self::MONITOREO_SSVQ) return "Monitor servicio de salud";
			if($tipo == self::GESTION_CLINICA) return "Enfermera";
			if($tipo == self::DIRECTOR) return "Director del establecimiento";
			if($tipo == self::IAAS) return "Infeccion Intrahospitalaria";
			if($tipo == self::VISUALIZADOR) return "Visualizador";
			if($tipo == self::ESTADISTICAS) return "Estadisticas";
			if($tipo == self::CENSO) return "Censo";
			if($tipo == self::MASTER) return "master";
			if($tipo == self::PABELLON) return "Pabellon";
			if($tipo == self::ENFERMERA_P) return "Enfermera(pensionado-matrona)";
			if($tipo == self::ADMINCOMERCIAL) return "Administrador de comercialización";
			if($tipo == self::MEDICO) return "Médico";
			if($tipo == self::MEDICO_JEFE_DE_SERVICIO) return "Medico jefe de serivicio";
			if($tipo == self::MASTERSS) return "master ss";
		}

	}
}
?>
