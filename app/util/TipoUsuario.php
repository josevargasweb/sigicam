<?php namespace App\util{
//este es el que vale
	class TipoUsuario{
		public static $ADMINSS="admin_ss";
		public static $ADMIN="admin";
		public static $USUARIO="usuario";

		const ADMIN = 'admin';
		const ADMINSS = 'admin_ss';
		const ADMINIAAS = 'admin_iaas';
		const USUARIO = 'usuario';
		const GESTION_CLINICA = 'gestion_clinica';
		const MONITOREO_SSVQ = 'monitoreo_ssvq';
		const DIRECTOR = 'director';
		const IAAS = 'iaas';//infeccion Intrahospitalaria
		const VISUALIZADOR = 'visualizador';
		const ESTADISTICAS = 'estadisticas';
		const CENSO = 'censo';
		const MASTER = 'master';
		const MASTERSS = 'master_ss';
		const PABELLON = 'pabellon';
		const ENFERMERA_P = 'enfermeraP';
		const ADMINCOMERCIAL = 'admin_comercial';
		const MEDICO = 'medico';
		const TENS = 'tens';
		const MATRONA_NEONATOLOGIA = 'matrona_neonatologia';
		const GRD = 'grd';
		const SUPERVISORA_DE_SERVICIO = 'supervisora_de_servicio';
		const KINESIOLOGO = 'kinesiologo';
		const AUXILIAR_DE_ENFEMERIA = 'auxiliar_de_enfermeria';
		const CDT = "cdt"; 
		const SECRETARIA = "secretaria"; 
		const OIRS = "oirs";
		const MEDICO_JEFE_DE_SERVICIO = "medico_jefe_servicio";
		const ENCARGADO_HOSP_DOM = "encargado_hosp_domiciliaria";

		public static function getNombre($tipo){
		if($tipo == self::ADMINSS) return "Administrador servicio de salud";//ve mapa de camas
		if($tipo == self::ADMINIAAS) return "Administrador de IAAS";//produce error al inicio // ve infor de usuarios por buscador 
		if($tipo == self::ADMIN) return "Gestor de camas";//ve mapa de camas
		if($tipo == self::USUARIO) return "Urgencia";//era Usuario //Ve mapa de camas
		if($tipo == self::MONITOREO_SSVQ) return "Monitor servicio de salud";//produce error al inicio // ve infor de usuarios por buscador 
		if($tipo == self::GESTION_CLINICA) return "Enfermera";// ve mapa de camas
		if($tipo == self::DIRECTOR) return "Director del establecimiento";//ve mapa de camas
		if($tipo == self::IAAS) return "Infeccion Intrahospitalaria";//ve mapa de camas
		if($tipo == self::VISUALIZADOR) return "Visualizador";// solo ve datos de usuarios en buscador, pero sin mayor detalle
		if($tipo == self::ESTADISTICAS) return "Estadisticas";// ve mapa de camas
		if($tipo == self::CENSO) return "Censo";// ve mapa de camas
		if($tipo == self::MASTER) return "master"; // ve todo
		if($tipo == self::MASTERSS) return "master ss"; // ve todo
		if($tipo == self::PABELLON) return "Pabellon"; // no hay usuarios
		if($tipo == self::ENFERMERA_P) return "Enfermera(pensionado-matrona)";//ve mapa de camas
		if($tipo == self::ADMINCOMERCIAL) return "Administrador de comercialización";//Se encarga de revisar los productos del hospital. 
		if($tipo == self::MEDICO) return "Médico";
		if($tipo == self::TENS) return "Tens";
		if($tipo == self::MATRONA_NEONATOLOGIA) return "Matrona Neonatología";
		if($tipo == self::GRD) return "GRD"; //gestion de egresos (Grupos Relacionados por el Diagnóstico)
		if($tipo == self::SUPERVISORA_DE_SERVICIO) return "Supervisora de servicio"; //Las características de este son las  mismas que para una enfermera normal , pero que esta puedan ver los reportes
		if($tipo == self::KINESIOLOGO) return "Kinesiólogo"; //aun no tienen restricciones de vista, solo que no puedan ver la parte de administración y que solo puedan cambiar la contraseña
		if($tipo == self::AUXILIAR_DE_ENFEMERIA) return "Auxiliar de enfermería"; //aun no tienen restricciones de vista, solo que no puedan ver la parte de administración y que solo puedan cambiar la contraseña
		if($tipo == self::CDT) return "CDT"; //no puede entrar a los servicios, ve todo lo referente al modulo de hosp dom, puede buscar pacientes pero no puede editar ni ir a la unidad, tampoco agregar indicaciones
		if($tipo == self::SECRETARIA) return "Secretaria"; //no puede entrar a los servicios, ve todo lo referente al modulo de hosp dom, puede buscar pacientes pero no puede editar ni ir a la unidad, tampoco agregar indicaciones
		if($tipo == self::OIRS) return "OIRS";
		if($tipo == self::MEDICO_JEFE_DE_SERVICIO) return "Medico jefe de serivicio"; //puede ver lo mismo que el director de establecimiento y recursos de enfermería
		if($tipo == self::ENCARGADO_HOSP_DOM) return "Encargado hospitalización domiciliaria";
		}
	}
}
?>