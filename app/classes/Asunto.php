<?php

require_once dirname(__FILE__) . '/../conf.php';

class Asunto extends AsuntoCargaMasiva {

	//Etapa actual del proyecto
	public $etapa = null;
	//Primera etapa del proyecto
	public $primera_etapa = null;
	public $monto = null;
	//TODO: usar llave unica multiple para resaltar asuntos existentes, siempre y cuando:
	//TODO: aplicar logica de campo unique, con soporte para unique de 2 campos (codigo_cliente/glosa_asunto)

	public function Asunto($sesion, $fields = "", $params = "") {
		$this->tabla = "asunto";
		$this->campo_id = "id_asunto";
		$this->sesion = $sesion;
		$this->fields = $fields;
		$this->log_update = true;
	}

	public function LoadByCodigo($codigo_asunto) {
		$query = "SELECT id_asunto FROM asunto WHERE codigo_asunto='$codigo_asunto'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($id) = mysql_fetch_array($resp);
		return $this->Load($id);
	}

	public function LoadByCodigoSecundario($codigo_asunto_secundario) {
		$query = "SELECT id_asunto FROM asunto WHERE codigo_asunto_secundario='$codigo_asunto_secundario'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($id) = mysql_fetch_array($resp);
		return $this->Load($id);
	}

	public function LoadByContrato($id_contrato) {
		$query = "SELECT id_asunto FROM asunto WHERE id_contrato = '$id_contrato' LIMIT 1";
		$resp = $this->sesion->pdodbh->query($query)->fetch(PDO::FETCH_ASSOC);
		return $this->Load($resp['id_asunto']);
	}

	public function CodigoACodigoSecundario($codigo_asunto) {
		if ($codigo_asunto != '') {
			$query = "SELECT codigo_asunto_secundario FROM asunto WHERE codigo_asunto = '$codigo_asunto'";
			$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
			list($codigo_asunto_secundario) = mysql_fetch_array($resp);
			return $codigo_asunto_secundario;
		} else {
			return false;
		}
	}

	public function CodigoSecundarioACodigo($codigo_asunto_secundario) {
		if ($codigo_asunto_secundario != '') {
			$query = "SELECT codigo_asunto FROM asunto WHERE codigo_asunto_secundario = '$codigo_asunto_secundario'";
			$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
			list($codigo_asunto) = mysql_fetch_array($resp);
			return $codigo_asunto;
		} else {
			return false;
		}
	}

	/**
	 * Funci�n que crea los c�digos de asunto
	 * @param string $codigo_cliente
	 * @param string $glosa_asunto
	 * @param boolean $secundario
	 * @return string
	 * @deprecated use MatterManager::makeMatterCode()
	 */
	public function AsignarCodigoAsunto($codigo_cliente, $glosa_asunto = "", $secundario = false) {
		$MatterManager = new MatterManager($this->sesion);
		return $MatterManager->makeMatterCode($codigo_cliente, $glosa_asunto, $secundario);
	}

	public function AsignarCodigoAsuntoSecundario($codigo_cliente_secundario, $glosa_asunto = "") {
		return $this->AsignarCodigoAsunto($codigo_cliente_secundario, $glosa_asunto, true);
	}

	//funcion que cambia todos los asuntos de un cliente
	public function InsertarCodigoAsuntosPorCliente($codigo_cliente) {
		$query = "SELECT id_asunto FROM asunto WHERE codigo_cliente='$codigo_cliente'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		for ($i = 1; list($id) = mysql_fetch_array($resp); $i++) {
			$this->fields[$this->campo_id] = $id;
			$codigo_asunto = $codigo_cliente . '-' . sprintf("%04d", $i);
			$this->Edit("codigo_asunto", $codigo_asunto);
			$this->Write();
		}
		return true;
	}

	//funcion que actualiza todos los codigos de los clientes existentes (usar una vez para actualizar el registro)
	public function ActualizacionCodigosAsuntos() {
		$query = "SELECT codigo_cliente FROM cliente";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		for ($i = 1; list($id) = mysql_fetch_array($resp); $i++) {
			if ($id != 'NULL')
				$this->InsertarCodigoAsuntosPorCliente($id);
		}
		return true;
	}

	public function TotalHoras($emitido = true) {
		$where = '';
		if (!$emitido) {
			$where = "AND (t2.id_cobro IS NULL OR cobro.estado = 'CREADO' OR cobro.estado='EN REVISION')";
		}

		$query = "SELECT SUM(TIME_TO_SEC(duracion_cobrada))/3600 as hrs_no_cobradas
				FROM trabajo AS t2
				LEFT JOIN cobro on t2.id_cobro=cobro.id_cobro
				WHERE 1 $where
				AND t2.cobrable = 1
				AND t2.codigo_asunto='" . $this->fields['codigo_asunto'] . "'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($total_horas_no_cobradas) = mysql_fetch_array($resp);
		return $total_horas_no_cobradas;
	}

	public function TotalMonto($emitido = true) {
		$where = '';
		if (!$emitido) {
			$where = " AND (trabajo.id_cobro IS NULL OR cobro.estado = 'CREADO' OR cobro.estado = 'EN REVISION') ";
		}

		$query = "SELECT SUM((TIME_TO_SEC(duracion_cobrada)/3600)*usuario_tarifa.tarifa), prm_moneda.simbolo
				FROM trabajo
				JOIN asunto ON trabajo.codigo_asunto = asunto.codigo_asunto
				JOIN contrato ON asunto.id_contrato = contrato.id_contrato
				JOIN prm_moneda ON contrato.id_moneda=prm_moneda.id_moneda
				LEFT JOIN usuario_tarifa ON (trabajo.id_usuario=usuario_tarifa.id_usuario AND contrato.id_moneda=usuario_tarifa.id_moneda AND contrato.id_tarifa = usuario_tarifa.id_tarifa)
				LEFT JOIN cobro on trabajo.id_cobro=cobro.id_cobro
				WHERE 1 $where
				AND trabajo.cobrable = 1
				AND trabajo.codigo_asunto='" . $this->fields['codigo_asunto'] . "' GROUP BY trabajo.codigo_asunto";

		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($total_monto_trabajado, $moneda) = mysql_fetch_array($resp);
		return array($total_monto_trabajado, $moneda);
	}

	public function AlertaAdministrador($mensaje, $sesion) {
		$query = "SELECT CONCAT_WS(' ',nombre, apellido1, apellido2) as nombre, email
				FROM usuario
				 WHERE activo=1 AND id_usuario = '" . $this->fields['id_encargado'] . "'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($nombre, $email) = mysql_fetch_array($resp);

		if (method_exists('Conf', 'GetConf')) {
			$MailAdmin = Conf::GetConf($sesion, 'MailAdmin');
		} else if (method_exists('Conf', 'MailAdmin')) {
			$MailAdmin = Conf::MailAdmin();
		}

		Utiles::Insertar($sesion, __("Alerta") . " " . __("ASUNTO") . " - " . $this->fields['glosa_asunto'] . " | " . Conf::AppName(), $mensaje, $email, $nombre);
		Utiles::Insertar($sesion, __("Alerta") . " " . __("ASUNTO") . " - " . $this->fields['glosa_asunto'] . " | " . Conf::AppName(), $mensaje, $MailAdmin, $nombre);
		return true;
	}

	/**
	 * Verifica si el asunto puede ser eliminado
	 * @return boolean
	 */
	public function CheckDelete() {
		#Valida si no tiene alg�n trabajo relacionado
		$query = "SELECT COUNT(*) FROM trabajo WHERE codigo_asunto = '{$this->fields['codigo_asunto']}'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($count) = mysql_fetch_array($resp);
		if ($count > 0) {
			$this->error = __('No se puede eliminar un') . ' ' . __('asunto') . ' ' . __('que tiene trabajos asociados');
			return false;
		}

		$query = "SELECT Count(*) FROM cta_corriente WHERE codigo_asunto = '{$this->fields['codigo_asunto']}'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($count) = mysql_fetch_array($resp);
		if ($count > 0) {
			$this->error = __('No se puede eliminar un') . ' ' . __('asunto') . ' ' . __('que tiene gastos asociados');
			return false;
		}

		#solo se puede eliminar asuntos que no tengan cobros asociados
		$query = "SELECT COUNT(*) FROM cobro_asunto WHERE codigo_asunto = '{$this->fields['codigo_asunto']}'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($count) = mysql_fetch_array($resp);
		if ($count > 0) {
			$query = "SELECT cobro.id_cobro
					FROM cobro_asunto
					JOIN cobro ON cobro.id_cobro = cobro_asunto.id_cobro
					WHERE cobro_asunto.codigo_asunto = '{$this->fields['codigo_asunto']}'";
			$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
			list($cobro) = mysql_fetch_array($resp);
			$this->error = __('No se puede eliminar un') . ' ' . __('asunto') . ' ' . __('que tiene cobros asociados') . ". " .
					__('Cobro asociado') . __(': #' . $cobro);
			return false;
		}

		#solo se pueden eliminar asuntos que no tengan carpetas asociados
		$query = "SELECT COUNT(*) FROM carpeta WHERE codigo_asunto = '{$this->fields['codigo_asunto']}'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		list($count) = mysql_fetch_array($resp);
		if ($count > 0) {
			$query = "SELECT id_carpeta, glosa_carpeta FROM carpeta WHERE codigo_asunto = '{$this->fields['codigo_asunto']}'";
			$resp = mysql_query($query, $this->sesion->dbh) or Utile::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
			list($id_carpeta, $glosa_carpeta) = mysql_fetch_array($resp);
			$this->error = __('No se puede eliminar un') . ' ' . __('asunto') . ' ' . __('que tiene carpetas asociados. Carpeta asociado: #' . $id_carpeta . ' ( ' . $glosa_carpeta . ' )');
			return false;
		}

		return true;
	}

	public function Eliminar() {
		$id_contrato_indep = $this->fields['id_contrato_indep'];

		if (! empty($id_contrato_indep)) {
			$criteria = new Criteria($this->sesion);
			$criteria->add_select('COUNT(*)', 'total')
					->add_from('cobro_pendiente')
					->add_restriction(CriteriaRestriction::not_equal('hito', 1))
					->add_restriction(CriteriaRestriction::equals('id_contrato', $id_contrato_indep));

			$result = $criteria->run();
			$cobro_programado = ($result[0]['total'] > 0) ? true : false;

			if ($cobro_programado) {
				$this->error = __('El') . ' ' . __('contrato') . ' ' . __('del') . ' ' . __('asunto') . ' ' . __('tiene cobros programados configurados, no se puede eliminar el') . ' ' . __('asunto') . '.';
			}

			$criteria = new Criteria($this->sesion);
			$criteria->add_select('COUNT(*)', 'total')
					->add_from('cobro_pendiente')
					->add_restriction(CriteriaRestriction::equals('hito', 1))
					->add_restriction(CriteriaRestriction::is_null('id_cobro'))
					->add_restriction(CriteriaRestriction::equals('id_contrato', $id_contrato_indep));

			$result = $criteria->run();
			$hito = ($result[0]['total'] > 0) ? true : false;

			if ($hito) {
				$this->error = __('El') . ' ' . __('contrato') . ' ' . __('del') . ' ' . __('asunto') . ' ' . __('tiene hitos configurados, no se puede eliminar el') . ' ' . __('asunto') . '.';
			}

			if ($cobro_programado || $hito) {
				return false;
			}
		}

		if ($this->Delete()) {
			if (!empty($id_contrato_indep)) {
				$ContratoIndependiente = new Contrato($this->sesion);
				$ContratoIndependiente->Load($id_contrato_indep);
				if ($ContratoIndependiente->Loaded()) {
					if (!$ContratoIndependiente->Eliminar()) {
						$ContratoIndependiente->Edit('activo', 0);
						$ContratoIndependiente->Write();
					}
				}
			}
			return true;
		} else {
			#Nota:
			#Si la eliminaci�n del asunto sali� mal, es por algo que ocurre en CheckDelete. EL llamado
			#del mencionado m�todo se realiza en Objeto.php, ya que esta clase lo sobrecarga, y efectivamente contiene
			#reglas de negocio respecto a cuando se puede eliminar un asunto.
			return false;
		}
	}

	public function QueryReporte($filtros = array()) {
		$wheres = array();

		if ($filtros['activo']) {
			$wheres[] = "a1.activo = " . ($filtros['activo'] == 'SI' ? 1 : 0);
		}

		if ($filtros['id_grupo_cliente']) {
			$wheres[] = "cliente.id_grupo_cliente = '{$filtros['id_grupo_cliente']}'";
		}

		if ($filtros['codigo_asunto'] != "") {
			$wheres[] = "a1.codigo_asunto LIKE '{$filtros['codigo_asunto']}%'";
		}

		if ($filtros['glosa_asunto'] != "") {
			$nombre = strtr($filtros['glosa_asunto'], ' ', '%');
			$wheres[] = "a1.glosa_asunto LIKE '%$nombre%'";
		}

		if ($filtros['codigo_cliente'] || $filtros['codigo_cliente_secundario']) {
			if (UtilesApp::GetConf($this->sesion, 'CodigoSecundario')) {
				$wheres[] = "cliente.codigo_cliente_secundario = '{$filtros['codigo_cliente_secundario']}'";
				$cliente = new Cliente($this->sesion);
				if ($cliente->LoadByCodigoSecundario($filtros['codigo_cliente_secundario'])) {
					$codigo_cliente = $cliente->fields['codigo_cliente'];
				}
			} else {
				$wheres[] = "cliente.codigo_cliente = '{$filtros['codigo_cliente']}'";
			}
		}

		if ($filtros['opc'] == "entregar_asunto") {
			$wheres[] = "a1.codigo_cliente = '{$filtros['codigo_cliente']}' ";
		}

		if ($filtros['fecha1'] || $filtros['fecha2']) {
			$wheres[] = "a1.fecha_creacion BETWEEN '" . Utiles::fecha2sql($filtros['fecha1']) . "' AND '" . Utiles::fecha2sql($filtros['fecha2']) . " 23:59:59'";
		}

		if ($filtros['motivo'] == "cobros") {
			$wheres[] = "a1.activo='1' AND a1.cobrable = '1'";
		}

		if ($filtros['id_usuario']) {
			$wheres[] = "a1.id_encargado = '{$filtros['id_usuario']}' ";
		}

		if ($filtros['id_area_proyecto']) {
			$wheres[] = "a1.id_area_proyecto = '{$filtros['id_area_proyecto']}' ";
		}

		$on_encargado2 = UtilesApp::GetConf($this->sesion, 'EncargadoSecundario') ? "contrato.id_usuario_secundario" : "a1.id_encargado2";

		$where = empty($wheres) ? '' : (' WHERE ' . implode(' AND ', $wheres));

		//Este query es mejorable, se podr�a sacar horas_no_cobradas y horas_trabajadas, pero ya no se podr�a ordenar por estos campos.
		$query = "SELECT SQL_CALC_FOUND_ROWS
		a1.codigo_asunto,
		a1.codigo_asunto_secundario as codigo_secundario,
		a1.glosa_asunto,
		a1.descripcion_asunto,
		a1.id_moneda,
		IF(a1.activo=1,'SI','NO') as activo,
		a1.fecha_inactivo,
		a1.contacto,
		IF(a1.cobrable=1, 'SI', 'NO') as cobrable,
		a1.fono_contacto,
		a1.email_contacto,
		a1.direccion_contacto,
		a1.fecha_creacion,

		tarifa.glosa_tarifa,
		cliente.glosa_cliente,
		cliente.id_grupo_cliente,
		prm_tipo_proyecto.glosa_tipo_proyecto AS tipo_proyecto,
		prm_area_proyecto.glosa AS area_proyecto,
		prm_idioma.glosa_idioma,
		contrato.monto,
		contrato.forma_cobro,
		prm_moneda.glosa_moneda,
		prm_moneda.simbolo as simbolo_moneda,
		prm_moneda.cifras_decimales as decimales_moneda,

		usuario.username as username,
		CONCAT(usuario.apellido1, ', ', usuario.nombre) as nombre,

		usuario_ec.username as username_ec,
		CONCAT(usuario_ec.apellido1, ', ', usuario_ec.nombre) as nombre_ec,

		usuario_secundario.username as username_secundario,
		IF(usuario_secundario.username IS NULL, '', CONCAT(usuario_secundario.apellido1, ', ', usuario_secundario.nombre)) as nombre_secundario,

		SUM(TIME_TO_SEC(trabajo.duracion))/3600 AS horas_trabajadas,
		SUM(IF(cobro_trabajo.estado IS NULL OR cobro_trabajo.estado = 'CREADO' OR cobro_trabajo.estado = 'EN REVISION',
		TIME_TO_SEC(trabajo.duracion_cobrada), 0))/3600 AS horas_no_cobradas,

		IF( contrato.tipo_descuento = 'VALOR', contrato.descuento, CONCAT(contrato.porcentaje_descuento,'%' ) ) AS descuento,

		IF(a1.id_contrato != cliente.id_contrato, 'SI', 'NO') AS cobro_independiente,
		contraparte,
		cotizado_con";

		if ($filtros['ver_desglose_area']) {
			$query .= "
		, (SELECT GROUP_CONCAT(DISTINCT CASE
				WHEN prm_area_proyecto_desglose.requiere_desglose = 1
				THEN CONCAT(prm_area_proyecto_desglose.glosa, ': ', a1.desglose_area)
				ELSE prm_area_proyecto_desglose.glosa END)
			FROM asunto_area_proyecto_desglose
			INNER JOIN prm_area_proyecto_desglose
			ON asunto_area_proyecto_desglose.id_area_proyecto_desglose = prm_area_proyecto_desglose.id_area_proyecto_desglose
			WHERE prm_area_proyecto_desglose.id_area_proyecto = prm_area_proyecto.id_area_proyecto
			AND asunto_area_proyecto_desglose.id_asunto = a1.id_asunto
			ORDER BY prm_area_proyecto_desglose.glosa ASC) AS desglose_area";
		}

		if ($filtros['ver_sector_economico']) {
			$query .= "
		, (SELECT GROUP_CONCAT(DISTINCT CASE
				WHEN prm_giro.requiere_desglose = 1
				THEN CONCAT(prm_giro.glosa, ': ', a1.giro)
				ELSE prm_giro.glosa END)
			FROM asunto_giro
			INNER JOIN prm_giro
			ON asunto_giro.id_giro = prm_giro.id_giro
			WHERE asunto_giro.id_asunto = a1.id_asunto
			ORDER BY prm_giro.glosa ASC) AS sector_economico";
		}

		if ($filtros['ver_glosa_estudio']) {
			$query .= ", prm_estudio.glosa_estudio";
		}

		$query .= "
		FROM asunto AS a1
		LEFT JOIN cliente ON cliente.codigo_cliente=a1.codigo_cliente
		LEFT JOIN contrato ON contrato.id_contrato = a1.id_contrato
		LEFT JOIN tarifa ON contrato.id_tarifa=tarifa.id_tarifa
		LEFT JOIN prm_idioma ON a1.id_idioma = prm_idioma.id_idioma
		LEFT JOIN prm_tipo_proyecto ON a1.id_tipo_asunto=prm_tipo_proyecto.id_tipo_proyecto
		LEFT JOIN prm_area_proyecto ON a1.id_area_proyecto=prm_area_proyecto.id_area_proyecto
		LEFT JOIN prm_moneda ON contrato.id_moneda=prm_moneda.id_moneda
		LEFT JOIN usuario ON a1.id_encargado = usuario.id_usuario
		LEFT JOIN usuario as usuario_ec ON contrato.id_usuario_responsable = usuario_ec.id_usuario
		LEFT JOIN usuario as usuario_secundario ON usuario_secundario.id_usuario = $on_encargado2";

		if (!$filtros['ocultar_trabajos_cobros']) {
			$query .= "
		LEFT JOIN trabajo ON trabajo.codigo_asunto = a1.codigo_asunto AND trabajo.cobrable = 1
		LEFT JOIN cobro as cobro_trabajo ON trabajo.id_cobro = cobro_trabajo.id_cobro";
		}

		if ($filtros['ver_glosa_estudio']) {
			$query .= " LEFT JOIN prm_estudio ON prm_estudio.id_estudio = contrato.id_estudio";
		}

		$query .= $where . "
		GROUP BY a1.codigo_asunto
		ORDER BY a1.codigo_asunto, a1.codigo_cliente ASC";

		return $query;
	}

	public function DownloadExcel($filtros = array()) {
		require_once Conf::ServerDir() . '/classes/Reportes/SimpleReport.php';

		$SimpleReport = new SimpleReport($this->sesion);
		$SimpleReport->SetRegionalFormat(UtilesApp::ObtenerFormatoIdioma($this->sesion));
		$SimpleReport->LoadConfiguration('ASUNTOS');

		$filtros['ver_glosa_estudio'] = $SimpleReport->Config->columns['glosa_estudio']->Visible();
		$filtros['ver_desglose_area'] = $SimpleReport->Config->columns['desglose_area']->Visible();
		$filtros['ver_sector_economico'] = $SimpleReport->Config->columns['sector_economico']->Visible();

		// Overridear configuraciones del reporte con confs
		$usa_username = UtilesApp::GetConf($this->sesion, 'UsaUsernameEnTodoElSistema');
		$mostrar_encargado_secundario = UtilesApp::GetConf($this->sesion, 'EncargadoSecundario');
		$mostrar_encargado2 = UtilesApp::GetConf($this->sesion, 'AsuntosEncargado2');
		$encargado = $mostrar_encargado_secundario || $mostrar_encargado2;

		$SimpleReport->Config->columns['username']->Visible($usa_username);
		$SimpleReport->Config->columns['nombre']->Visible(!$usa_username);

		$SimpleReport->Config->columns['username_ec']->Visible($usa_username);
		$SimpleReport->Config->columns['nombre_ec']->Visible(!$usa_username);

		$SimpleReport->Config->columns['username_secundario']->Visible($usa_username && $encargado);
		$SimpleReport->Config->columns['nombre_secundario']->Visible(!$usa_username && $encargado);

		$SimpleReport->Config->columns['username_ec']->Title(__('Encargado Comercial'));
		$SimpleReport->Config->columns['nombre_ec']->Title(__('Encargado Comercial'));

		if ($mostrar_encargado_secundario) {
			$SimpleReport->Config->columns['username_secundario']->Title(__('Encargado Secundario'));
			$SimpleReport->Config->columns['nombre_secundario']->Title(__('Encargado Secundario'));
		}

		// Swapear codigo y codigo_secundario
		if (UtilesApp::GetConf($this->sesion, 'CodigoSecundario')) {
			$SimpleReport->Config->columns['codigo_asunto']->Field('codigo_secundario');
			$SimpleReport->Config->columns['codigo_secundario']->Field('codigo_asunto');
		}

		$query = $this->QueryReporte($filtros);

		$filtros['ocultar_trabajos_cobros'] = true;
		$query_count = $this->QueryReporte($filtros);
		$query_count = preg_replace('/(^\s*SELECT\s)[\s\S]+(\sFROM\s)/mi', '$1 COUNT(*) AS n $2', $query_count);
		$query_count = preg_replace('/\sGROUP BY.+|\sORDER BY.+|\sLIMIT.+/mi', '', $query_count);

		try {
			$result = $this->sesion->pdodbh->query($query_count)->fetch();
		} catch (Exception $ex) {
			Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
		}

		if ($result['n'] > 10000) {
			throw new Exception('Est� tratando de descargar mas de 10.000 registros, por favor limite su b�squeda e intente nuevamente.');
		}

		$this->sesion->pdodbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
		$statement = $this->sesion->pdodbh->prepare($query);
		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$SimpleReport->LoadResults($results);

		$writer = SimpleReport_IOFactory::createWriter($SimpleReport, 'Spreadsheet');
		$writer->save(__('Planilla_Asuntos'));
	}

	/**
	 * Find all active matters by client code
	 * Return an array with next elements:
	 * 	code (secondary if used) and name
	 */
	public function findAllByClientCode($code, $include_all = 0) {
		$matters = array();
		$active = 1;
		$sql_select_client_code = '`client`.`codigo_cliente`';
		$sql_select_matter_code = '`matter`.`codigo_asunto`';

		// find if the client used secondary code
		if (UtilesApp::GetConf($this->sesion, 'CodigoSecundario') == '1') {
			$sql_select_client_code = '`client`.`codigo_cliente_secundario`';
			$sql_select_matter_code = '`matter`.`codigo_asunto_secundario`';
		}

		if (!$include_all) {
			$sql_include = "AND `matter`.`activo`=:active";
		} else {
			$sql_include = "";
		}

		$sql = "SELECT $sql_select_client_code AS `client_code`, $sql_select_matter_code AS `code`,
		`matter`.`glosa_asunto` AS `name`,
		`prm_idioma`.`codigo_idioma` AS `language`,
		`prm_idioma`.`glosa_idioma` AS `language_name`,
		`matter`.`activo` AS active
		FROM `cliente` AS `client`
		INNER JOIN `asunto` AS `matter` ON `matter`.`codigo_cliente` = `client`.`codigo_cliente`
		LEFT JOIN `prm_idioma` USING (`id_idioma`)
		WHERE $sql_select_client_code=:code {$sql_include}
		ORDER BY `matter`.`glosa_asunto` ASC";

		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('code', $code);
		if (!$include_all) {
			$Statement->bindParam('active', $active);
		}
		$Statement->execute();

		while ($matter = $Statement->fetch(PDO::FETCH_OBJ)) {
			array_push($matters, array(
				'client_code' => $matter->client_code,
				'code' => $matter->code,
				'name' => !empty($matter->name) ? $matter->name : null,
				'language' => !empty($matter->language) ? $matter->language : null,
				'language_name' => !empty($matter->language_name) ? $matter->language_name : null,
				'active' => (int) $matter->active
					)
			);
		}

		return $matters;
	}

	/**
	 * Find all active matters
	 * Return an array with next elements:
	 * 	code (secondary if used) and name
	 */
	public function findAllActive($timestamp = 0, $include_all = 0) {
		$matters = array();
		$active = 1;
		$sql_select_client_code = '`client`.`codigo_cliente`';
		$sql_select_matter_code = '`matter`.`codigo_asunto`';

		// find if the client used secondary code
		if (UtilesApp::GetConf($this->sesion, 'CodigoSecundario') == '1') {
			$sql_select_client_code = '`client`.`codigo_cliente_secundario`';
			$sql_select_matter_code = '`matter`.`codigo_asunto_secundario`';
		}

		if (!$include_all) {
			$sql_include = "AND `matter`.`activo`=:active";
		} else {
			$sql_include = "";
		}

		$sql = "SELECT
		$sql_select_client_code AS `client_code`,
		$sql_select_matter_code AS `code`,
		`matter`.`glosa_asunto` AS `name`,
		`prm_idioma`.`codigo_idioma` AS `language`,
		`prm_idioma`.`glosa_idioma` AS `language_name`,
		`matter`.`activo` AS active
		FROM `cliente` AS `client`
		INNER JOIN `asunto` AS `matter` ON `matter`.`codigo_cliente` = `client`.`codigo_cliente`
		LEFT JOIN `prm_idioma` USING (`id_idioma`)
		WHERE `matter`.`glosa_asunto`<>'' {$sql_include}
		AND (`matter`.`fecha_touch`>=:timestamp OR `matter`.`fecha_creacion`>=:timestamp)
		ORDER BY `matter`.`glosa_asunto` ASC";

		$Statement = $this->sesion->pdodbh->prepare($sql);
		if (!$include_all) {
			$Statement->bindParam('active', $active);
		}
		$Statement->bindParam('timestamp', date('Y-m-d', $timestamp));
		$Statement->execute();

		while ($matter = $Statement->fetch(PDO::FETCH_OBJ)) {
			array_push($matters, array(
				'client_code' => $matter->client_code,
				'code' => $matter->code,
				'name' => !empty($matter->name) ? $matter->name : null,
				'language' => !empty($matter->language) ? $matter->languag : null,
				'language_name' => !empty($matter->language_name) ? $matter->language_name : null,
				'active' => (int) $matter->active
					)
			);
		}

		return $matters;
	}

	public function CodigoSecundarioSiguienteCorrelativo() {
		$query = "SELECT MAX(SUBSTR(codigo_asunto_secundario, INSTR(codigo_asunto_secundario, '-') + 1, LENGTH(codigo_asunto_secundario)) *1) ultimo
			FROM asunto";
		$qr = $this->sesion->pdodbh->query($query);
		$ultimo = $qr->fetch(PDO::FETCH_ASSOC);
		return $ultimo['ultimo'] + 1;
	}

	public function CodigoSecundarioValidarCorrelativo($codigo) {
		if (!preg_match('/^[0-9]+$/', $codigo)) {
			return __('C�digo secundario') . ' invalido';
		}
		$query = "SELECT codigo_asunto_secundario
			FROM asunto
			HAVING SUBSTR(codigo_asunto_secundario, INSTR(codigo_asunto_secundario, '-') + 1, LENGTH(codigo_asunto_secundario)) = $codigo";
		$qr = $this->sesion->pdodbh->query($query);
		$ultimo = $qr->fetch(PDO::FETCH_ASSOC);
		return empty($ultimo) ? true : __('C�digo secundario') . ' existente';
	}

	/**
	 * M�todo que realiza la escritura del desglose de �reas para el asunto
	 * @param $details Array que contiene los Ids de desgloses de �reas a agregar
	 */
	public function writeAreaDetails($details) {
		$sql = "DELETE FROM `asunto_area_proyecto_desglose` WHERE id_asunto=:id";
		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('id', $this->fields[$this->campo_id]);
		if ($Statement->execute()) {
			if (is_null($details) || empty($details)) {
				return;
			}
			foreach ($details as $id_area_proyecto_desglose) {
				$sql = "INSERT INTO `asunto_area_proyecto_desglose`
		SET id_asunto=:id_asunto, id_area_proyecto_desglose=:id_area_proyecto_desglose";
				$Statement = $this->sesion->pdodbh->prepare($sql);
				$Statement->bindParam('id_asunto', $this->fields[$this->campo_id]);
				$Statement->bindParam('id_area_proyecto_desglose', $id_area_proyecto_desglose);
				$Statement->execute();
			}
		}
	}

	/**
	 * M�todo que realiza la escritura de los giros asociados al asunto
	 * @param $details Array que contiene los Ids de cada giro
	 */
	public function writeEconomicActivities($details) {
		$sql = "DELETE FROM `asunto_giro` WHERE id_asunto=:id";
		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('id', $this->fields[$this->campo_id]);
		if ($Statement->execute()) {
			if (is_null($details) || empty($details)) {
				return;
			}
			foreach ($details as $id_giro) {
				$sql = "INSERT INTO asunto_giro
		SET id_asunto=:id_asunto, id_giro=:id_giro";
				$Statement = $this->sesion->pdodbh->prepare($sql);
				$Statement->bindParam('id_asunto', $this->fields[$this->campo_id]);
				$Statement->bindParam('id_giro', $id_giro);
				$Statement->execute();
			}
		}
	}

	/**
	 * M�todo que obtiene todos los desgloses de �reas
	 */
	public function getAreaDetails() {
		$sql = "SELECT `asunto_area_proyecto_desglose`.`id_area_proyecto_desglose`
		FROM `asunto_area_proyecto_desglose`
		WHERE `asunto_area_proyecto_desglose`.`id_asunto`=:id_asunto";
		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('id_asunto', $this->fields[$this->campo_id]);
		$Statement->execute();
		$details = $Statement->fetchAll(PDO::FETCH_COLUMN, 0);
		return $details;
	}

	/**
	 * M�todo que obtiene los giros del asunto
	 */
	public function getEconomicActivities() {
		$sql = "SELECT id_giro
		FROM asunto_giro
		WHERE id_asunto=:id_asunto";
		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('id_asunto', $this->fields[$this->campo_id]);
		$Statement->execute();
		$details = $Statement->fetchAll(PDO::FETCH_COLUMN, 0);
		return $details;
	}

	/**
	 * Retorna true si es que el codigo de asunto secundario es encontrado
	 * y false si es que no fue encontrado.
	 *
	 * @param mixed $codigo_asunto_secundario
	 * @return boolean
	 */
	public function existeCodigoAsuntoSecundario($codigo_asunto_secundario) {
		$sql = 'SELECT COUNT(*)
						FROM ' . $this->tabla .
				' WHERE codigo_asunto_secundario = :codigo_asunto_secundario';

		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('codigo_asunto_secundario', $codigo_asunto_secundario);
		$Statement->execute();

		$resp = $Statement->fetchAll(PDO::FETCH_COLUMN, 0);

		if (is_array($resp)) {
			return (boolean) $resp[0];
		}

		return false;
	}


	/**
	 * Busca un codigo de asunto secundario para un asunto con id_asunto
	 * distinto del entregado.
	 *
	 * @param mixed $codigo_asunto_secundario
	 * @param int $id_asunto
	 * @return boolean
	 */
	public function existeCodigoAsuntoSecundarioParaOtroIdAsunto($codigo_asunto_secundario, $id_asunto) {
		$sql = 'SELECT COUNT(*)
						FROM ' . $this->tabla .
				' WHERE
						codigo_asunto_secundario = :codigo_asunto_secundario
						AND id_asunto <> :id_asunto';

		$Statement = $this->sesion->pdodbh->prepare($sql);
		$Statement->bindParam('codigo_asunto_secundario', $codigo_asunto_secundario);
		$Statement->bindParam('id_asunto', $id_asunto);
		$Statement->execute();

		$resp = $Statement->fetchAll(PDO::FETCH_COLUMN, 0);

		if (is_array($resp)) {
			return (boolean) $resp[0];
		}

		return false;
	}

	public static function totalMattersOfAccountManager(&$Session, $user_id, $active = true) {
		$Criteria = new Criteria($Session);

		$Criteria->add_select('count(*)', 'total')
				->add_from('asunto')
				->add_inner_join_with('contrato', 'contrato.id_contrato = asunto.id_contrato_indep')
				->add_restriction(CriteriaRestriction::equals('asunto.activo', $active ? 1 : 0))
				->add_restriction(CriteriaRestriction::equals('contrato.id_usuario_responsable', $user_id));

		$result = array_shift($Criteria->run());

		return (int) $result['total'];
	}

	/**
	 * Carga los datos extra del asunto y devielve el Objeto
	 * @return \MatterExtra
	 */
	public function loadExtra($fields = null) {
		if (!$this->Loaded()) {
			return new GenericModel();
		}
		$MatterExtraService = new MatterExtraService($this->sesion);
		$MatterExtra = $MatterExtraService->getByMatterId($this->fields['id_asunto']);
		return $MatterExtra ? $MatterExtra : $MatterExtraService->MatterExtraDAO->newEntity();
	}

	/**
	 * Guarda los datos extra del asunto
	 * @param array $data
	 * @return boolean
	 */
	public function saveExtra(array $data) {
		if (!$this->Loaded()) {
			return false;
		}
		$MatterExtraService = new MatterExtraService($this->sesion);
		$MatterExtra = $MatterExtraService->getByMatterId($this->fields['id_asunto']);
		if (!$MatterExtra) {
			$MatterExtra = $MatterExtraService
				->MatterExtraDAO
				->newEntity(array('id_asunto' => $this->fields['id_asunto']));
		}
		$MatterExtra->fillFromArray($data);
		return $MatterExtraService->saveOrUpdate($MatterExtra);
	}

	/**
	 * Reimplementaci�n de Objeto::Edit
	 * Edita el valor de un campo
	 * @param string  $field campo de la tabla
	 * @param mix  $value valor que se asignar�
	 * @param boolean $log_field si es verdadero entonces se guarda el historial del cambio
	 */
	public function Edit($field, $value, $log_field = false) {
		if ((isset($this->log_update) && $this->log_update == true) || $log_field == true) {
			if ($this->fields[$field] != $value) {
				if (($value != 'NULL' || ($this->fields[$field]) != '')) {
					if ((empty($this->fields[$field])) == false || empty($value) == false) {
						$this->logear[$field] = true;
						$this->valor_antiguo[$field] = $this->fields[$field];
					}
				}
			}
		}

		$this->fields[$field] = $value;
		$this->changes[$field] = true;
	}

}
