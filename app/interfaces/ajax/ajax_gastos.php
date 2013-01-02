<?php
require_once dirname(__FILE__) . '/../../conf.php';

$sesion = new Sesion(array('ADM'));

$limitdesde = isset($_REQUEST['iDisplayStart']) ? $_REQUEST['iDisplayStart'] : '0';
$limitcantidad = isset($_REQUEST['iDisplayLength']) ? $_REQUEST['iDisplayLength'] : '25';
$arrayorden = array(0 => 'fecha', 1 => 'glosa_cliente', 5 => 'egreso', 6 => 'ingreso', 7 => 'con_impuesto', 8 => 'estado', 10 => 'cobrable');
$orden = $arrayorden[intval($_REQUEST['iSortCol_0'])] . " " . $_REQUEST['sSortDir_0'];

if (!isset($where) || (isset($where) && $where == '')) {
	$where = 1;
}
if ($_REQUEST['opc'] == 'contratoasunto') {
	$codigo_asunto = $_REQUEST['codigo_asunto'];
	$data = array('id_contrato' => '');
	if ($codigo_asunto) {
		$contrato = new Contrato($sesion);
		$contrato->LoadByCodigoAsunto($codigo_asunto);
		$data['id_contrato'] = $contrato->fields['id_contrato'];
		if(isset($contrato->fields['codigo_contrato'])){
			$data['codigo_contrato'] = $contrato->fields['codigo_contrato'];
		}
	}
	echo json_encode($data);
	exit;
}
if ($_REQUEST['opc'] == 'actualizagastos') {

	$whereclause = base64_decode($_POST['whereclause']);
	$querypreparar = "UPDATE cta_corriente
						JOIN asunto using (codigo_asunto)
						JOIN contrato on contrato.id_contrato=asunto.id_contrato
						JOIN cliente on contrato.codigo_cliente=asunto.codigo_cliente
						LEFT JOIN cobro on cta_corriente.id_cobro=cobro.id_cobro ";

	$setclause = ' set cta_corriente.fecha_touch=now() ';
	if (isset($_POST['montocastigar'])) {
		$setclause.=', cta_corriente.monto_cobrable=0';
	}
	if (isset($_POST['id_proveedor']) && intval($_POST['id_proveedor'] > 0)) {
		$setclause.=', cta_corriente.id_proveedor=' . intval($_POST['id_proveedor']);
	}
	if (isset($_POST['codigo_asunto']) && $_POST['codigo_asunto'] != '') {
		$setclause.=", cta_corriente.codigo_asunto='{$_POST['codigo_asunto']}'";
	} else if (!empty($_POST['codigo_asunto_secundario'])) {
		$setclause.=", cta_corriente.codigo_asunto='{$_POST['codigo_asunto_secundario']}'";
	}


	$querypreparar.=$setclause . ' WHERE ' . $whereclause;

	debug($querypreparar);
	$query = $sesion->pdodbh->prepare($querypreparar);

	$query->execute();
	echo "jQuery('#boton_buscar').click();";
	die();
} else if ($_REQUEST['opc'] == 'buscar' || ($_GET['opclistado'] == 'listado' && $_GET['selectodos'] == 1) || $_GET['totalctacorriente'] == 1) {
	if ($where != '') {
		$where = 1;

		if (UtilesApp::GetConf($sesion, 'CodigoSecundario')) {
			if ($codigo_cliente_secundario) {
				$where .= " AND cliente.codigo_cliente_secundario = '$codigo_cliente_secundario'";

				if ($codigo_asunto_secundario) {
					$asunto = new Asunto($sesion);
					$asunto->LoadByCodigoSecundario($codigo_asunto_secundario);
					$query_asuntos = "SELECT codigo_asunto_secundario FROM asunto WHERE id_contrato = '" . $asunto->fields['id_contrato'] . "' ";
					$resp = mysql_query($query_asuntos, $sesion->dbh) or Utiles::errorSQL($query_asuntos, __FILE__, __LINE__, $sesion->dbh);
					$asuntos_list_secundario = array();
					while (list($codigo) = mysql_fetch_array($resp)) {
						array_push($asuntos_list_secundario, $codigo);
					}
					$lista_asuntos_secundario = implode("','", $asuntos_list_secundario);
				}
			}
		} else {
			if ($codigo_cliente) {
				$where .= " AND cta_corriente.codigo_cliente = '$codigo_cliente'";

				if ($codigo_asunto) {
					$asunto = new Asunto($sesion);
					$asunto->LoadByCodigo($codigo_asunto);
					$query_asuntos = "SELECT codigo_asunto FROM asunto WHERE id_contrato = '" . $asunto->fields['id_contrato'] . "' ";
					$resp = mysql_query($query_asuntos, $sesion->dbh) or Utiles::errorSQL($query_asuntos, __FILE__, __LINE__, $sesion->dbh);
					$asuntos_list = array();
					while (list($codigo) = mysql_fetch_array($resp)) {
						array_push($asuntos_list, $codigo);
					}
					$lista_asuntos = implode("','", $asuntos_list);
				}
			}
		}
		if ($fecha1 != '') {
			$fecha_ini = Utiles::fecha2sql($fecha1);
		} else {
			$fecha_ini = '';
		}
		if ($fecha2 != '') {
			$fecha_fin = Utiles::fecha2sql($fecha2);
		} else {
			$fecha_fin = '';
		}

		if ($_GET['egresooingreso'] == 'soloingreso') {
			$where .= " AND cta_corriente.ingreso>0";
		} else if ($_GET['egresooingreso'] == 'sologastos') {
			$where .= " AND cta_corriente.egreso>0";
		}
		if ($cobrado == 'NO') {
			$where .= " AND (cta_corriente.id_cobro is null OR  cobro.estado  in ('SIN COBRO','CREADO','EN REVISION')   ) ";
		}
		if ($cobrado == 'SI') {
			$where .= " AND cta_corriente.id_cobro is not null AND cobro.estado in ('EMITIDO', 'FACTURADO', 'PAGO PARCIAL','PAGADO', 'ENVIADO AL CLIENTE' ,'INCOBRABLE') ";
		}
		if ($codigo_asunto && $lista_asuntos) {
			$where .= " AND cta_corriente.codigo_asunto = '$codigo_asunto'";
		}
		if ($codigo_asunto_secundario && $lista_asuntos_secundario) {
			$where .= " AND asunto.codigo_asunto_secundario IN ('$lista_asuntos_secundario')";
		}
		if ($id_usuario_orden) {
			$where .= " AND cta_corriente.id_usuario_orden = '$id_usuario_orden'";
		}
		if (isset($cobrable) && $cobrable != '') {
			$where .= " AND cta_corriente.cobrable = $cobrable";
		}
		if ($id_usuario_responsable) {
			$where .= " AND contrato.id_usuario_responsable = '$id_usuario_responsable' ";
		}
		if (isset($id_tipo) and $id_tipo != '') {
			$where .= " AND cta_corriente.id_cta_corriente_tipo = '$id_tipo'";
		}
		if ($clientes_activos == 'activos') {
			$where .= " AND ( ( cliente.activo = 1 AND asunto.activo = 1 ) OR ( cliente.activo AND asunto.activo IS NULL ) ) ";
		}
		if ($clientes_activos == 'inactivos') {
			$where .= " AND ( cliente.activo != 1 OR asunto.activo != 1 ) ";
		}
		if ($fecha1 && $fecha2) {
			$where .= " AND cta_corriente.fecha BETWEEN '" . Utiles::fecha2sql($fecha1) . "' AND '" . Utiles::fecha2sql($fecha2) . ' 23:59:59' . "' ";
		} else if ($fecha1) {
			$where .= " AND cta_corriente.fecha >= '" . Utiles::fecha2sql($fecha1) . "' ";
		} else if ($fecha2) {
			$where .= " AND cta_corriente.fecha <= '" . Utiles::fecha2sql($fecha2) . "' ";
		} else if (!empty($id_cobro)) {
			$where .= " AND cobro.id_cobro='$id_cobro' ";
		}

		// Filtrar por moneda del gasto
		if ($moneda_gasto != '') {
			$where .= " AND cta_corriente.id_moneda=$moneda_gasto ";
		}
	} else {
		$where = base64_decode($where);
	}

	$idioma_default = new Objeto($sesion, '', '', 'prm_idioma', 'codigo_idioma');
	$idioma_default->Load(strtolower(UtilesApp::GetConf($sesion, 'Idioma')));

	$col_select = " ,if(cta_corriente.cobrable = 1,'Si','No') as esCobrable ";
}


$cobrosnoeditables = array();
if ($_GET['totalctacorriente']) { ?>
	<form id="buscacliente" method="POST" action="seguimiento_cobro.php" target="_blank">
		<b><?php
			echo __('Balance cuenta gastos'); ?>: <?php echo UtilesApp::GetSimboloMonedaBase($sesion);
			$balance = UtilesApp::TotalCuentaCorriente($sesion, $where, $cobrable, true);
			if ($codigo_cliente_secundario || $codigo_cliente) { ?>
				<input type="hidden" id="codcliente" name="codcliente" value="1"/>
			<?php } else { ?>
				<input type="hidden" id="codcliente" name="codcliente" value="0"/>
			<?php }
			if (is_array($balance)) {
				echo number_format($balance[0], 0, $idioma_default->fields['separador_decimales'], $idioma_default->fields['separador_miles']); ?>
				<input type="hidden" id="codigo_cliente" name="codigo_cliente" value="<?php echo $codigo_cliente; ?>"/>
				<input type="hidden" id="codigo_cliente_secundario" name="codigo_cliente_secundario" value="<?php echo $codigo_cliente_secundario; ?>"/>
				<input type="hidden" id="festado" name="estado[]" value="CREADO"/>
				<input type="hidden" id="festado" name="opc" value="buscar"/>
				<input type="hidden" id="festado" name="fecha_fin" value="<?php echo date('d-m-Y'); ?>"/>
				<input type="hidden"  name="ingreso" value="<?php echo $balance[1]; ?>"/>
				<input type="hidden"  name="egreso" value="<?php echo $balance[2]; ?>"/>
				<input type="hidden" id="balance" name="balance" value="<?php echo $balance[3]; ?>"/>
			<?php } else {
				echo number_format($balance, 0, $idioma_default->fields['separador_decimales'], $idioma_default->fields['separador_miles']);
			}?>
		</b>
	</form>
	<?php
	exit;
} else if ($_GET['opclistado'] == 'listado') { ?>
	<form id="form_edita_gastos_masivos">
		<table id="overlayeditargastos">
			<?php
			if ($_GET['selectodos'] == 1) {
				$where.="  AND (cobro.estado is null or cobro.estado in ('SIN COBRO','CREADO','EN REVISION'))";
			} else {
				$arraygasto = explode(';', ($_GET['movimientos']));
				if (sizeof($arraygasto) > 0) {
					$where = " ( cobro.estado is null or cobro.estado in ('SIN COBRO','CREADO','EN REVISION') ) and id_movimiento in (" . implode(',', $arraygasto) . ")  ";
				}
			}

			$querypreparar = "update cta_corriente
								JOIN asunto using(codigo_asunto)
								JOIN contrato on contrato.id_contrato=asunto.id_contrato
								JOIN cliente on contrato.codigo_cliente=asunto.codigo_cliente
								LEFT JOIN cobro on cta_corriente.id_cobro=cobro.id_cobro
											set fecha_touch=now()
									WHERE $where";


			if (UtilesApp::GetConf($sesion, 'TipoGasto') && $prov == 'false') {
				?>
				<tr>
					<td align=right>
						<?php echo __('Tipo de Gasto') ?>
					</td>
					<td align=left>
						<?php echo Html::SelectQuery($sesion, "SELECT id_cta_corriente_tipo, glosa FROM prm_cta_corriente_tipo", "id_cta_corriente_tipo", '1', '', '', "160"); ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td align="right">
					<?php echo __('Proveedor') ?>
				</td>
				<td align="left">
					<?php echo Html::SelectQuery($sesion, "SELECT id_proveedor, glosa FROM prm_proveedor ORDER BY glosa", "id_proveedor", '0', '', 'Cualquiera', "160"); ?>
				</td>
			</tr>
			<tr>
				<td align="right">
					<?php echo 'Castigar ' . __('Monto') ?>
				</td>
				<td align="left">
					<input name="montocastigar" id="montocastigar" type="checkbox"   value="0" />
					<span style="color:#777; font-size:10px"> (Al activar se baja el monto <?php echo __('cobrable'); ?> a cero para todos los gastos seleccionados)</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" size="100" id="whereclause" name="whereclause" value="<?php echo base64_encode($where); ?>"/>
					<p>S&oacute;lo se modificar&aacute; los gastos que no pertenezcan a <?php echo __('Cobros emitidos'); ?></p>
				</td>
			</tr>
		</table>
	</form>
<?php
} else if ($_REQUEST['opc'] == 'buscar') {

	$selectfrom = "FROM cta_corriente
						LEFT JOIN asunto USING(codigo_asunto)
						LEFT JOIN contrato ON asunto.id_contrato = contrato.id_contrato
						LEFT JOIN cliente ON asunto.codigo_cliente = cliente.codigo_cliente
						LEFT JOIN prm_idioma ON asunto.id_idioma = prm_idioma.id_idioma
						LEFT JOIN prm_moneda ON cta_corriente.id_moneda=prm_moneda.id_moneda
						LEFT JOIN prm_cta_corriente_tipo ON cta_corriente.id_cta_corriente_tipo=prm_cta_corriente_tipo.id_cta_corriente_tipo
						LEFT JOIN usuario ON usuario.id_usuario=cta_corriente.id_usuario
						LEFT JOIN cobro on cta_corriente.id_cobro=cobro.id_cobro
					WHERE
						$where
						 ";
	$query = "SELECT
				cta_corriente.id_movimiento,
				cta_corriente.fecha,
				cta_corriente.egreso,
				cta_corriente.ingreso,
				cta_corriente.monto_cobrable,
				ifnull(cta_corriente.codigo_cliente,'-') codigo_cliente,
				ifnull(cta_corriente.numero_documento,'-') numero_documento,
				cta_corriente.numero_ot,
				ifnull(cta_corriente.descripcion,'-') descripcion,
				cobro.id_cobro,
				ifnull(concat_ws(' - ',asunto.codigo_asunto,asunto.glosa_asunto),'-') glosa_asunto,
				ifnull(concat_ws(' - ',cliente.codigo_cliente, cliente.glosa_cliente),'-') glosa_cliente,
				prm_moneda.simbolo,
				prm_moneda.cifras_decimales,
				prm_cta_corriente_tipo.glosa as tipo,
				ifnull(cobro.estado,'SIN COBRO') as estado,
				cta_corriente.con_impuesto,
				prm_idioma.codigo_idioma,
				contrato.activo AS contrato_activo,
				1 as opcion, contrato.id_contrato
				$col_select
			$selectfrom
			ORDER BY $orden
			LIMIT $limitdesde,$limitcantidad";


	$selectcount = "SELECT COUNT(*) $selectfrom ";

	$sesion->debug($query);




	try {
		$rows = $sesion->pdodbh->query($selectcount)->fetch();
		$resp = $sesion->pdodbh->query($query);
	} catch (PDOException $e) {
		if ($sesion->usuario->fields['rut'] == '99511620') {
			$Slim = Slim::getInstance('default', true);
			$arrayPDOException = array('File' => $e->getFile(), 'Line' => $e->getLine(), 'Mensaje' => $e->getMessage(), 'Query' => $query, 'Trace' => json_encode($e->getTrace()), 'Parametros' => json_encode($resp));
			$Slim->view()->setData($arrayPDOException);
			$Slim->applyHook('hook_error_sql');
		}

		$resultado = array(
			'iTotalRecords' => $rows[0],
			'iTotalDisplayRecords' => $rows[0],
			'aaData' => array()
		);
		echo json_encode($resultado);
		exit;
	}


	$i = 0;
	/*  $resp = mysql_query($query, $sesion->dbh);
	  $rows=mysql_fetch_row(mysql_query('SELECT FOUND_ROWS()', $sesion->dbh)); */


	$resultado = array(
		'iTotalRecords' => $rows[0],
		'iTotalDisplayRecords' => $rows[0],
		'aaData' => array()
	);
	$mas = 0;
	foreach ($resp as $fila) {
		$stringarray = array(
			date('d-m-Y', strtotime($fila['fecha'])),
			$fila['glosa_cliente'] ? utf8_encode($fila['glosa_cliente']) : ' - ',
			$fila['glosa_asunto'] ? utf8_encode($fila['glosa_asunto']) : ' - ',
			$fila['tipo'] ? $fila['tipo'] : ' - ',
			$fila['descripcion'] ? utf8_encode($fila['descripcion']) : ' ',
			$fila['egreso'] ? $fila['simbolo'] . ' ' . $fila['egreso'] : ' ',
			$fila['ingreso'] ? $fila['simbolo'] . ' ' . $fila['ingreso'] : ' ',
			$fila['con_impuesto'] ? $fila['con_impuesto'] : ' ',
			$fila['id_cobro'] ? $fila['id_cobro'] : ' ',
			$fila['estado'] ? $fila['estado'] : ' ',
			$fila['esCobrable'] ? $fila['esCobrable'] : 'No',
			$fila['contrato_activo'] ? $fila['contrato_activo'] : ' ',
			$fila['id_movimiento'],
			$fila['egreso'] > 0 ? $fila['simbolo'] . ' ' . $fila['monto_cobrable'] : ' ',
			$fila['id_contrato']
		);
		$resultado['aaData'][] = $stringarray;
		$mas += $fila['egreso'];
		$mas -= $fila['ingreso'];
	}
	echo json_encode($resultado);
}

function Monto(& $fila) {
	global $sesion;
	$idioma = new Objeto($sesion, '', '', 'prm_idioma', 'codigo_idioma');
	if ($fila->fields['codigo_idioma'] != '') {
		$idioma->Load($fila->fields['codigo_idioma']);
	} else {
		$idioma->Load(strtolower(UtilesApp::GetConf($sesion, 'Idioma')));
	}
	return $fila->fields['egreso'] > 0 ? $fila->fields[simbolo] . " " . number_format($fila->fields['monto_cobrable'], $fila->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) : '';
}

function removeBOM($string) {
	return str_replace(array('\n', "\n"), '', $string);
}

function Ingreso(& $fila) {
	global $sesion;
	$idioma = new Objeto($sesion, '', '', 'prm_idioma', 'codigo_idioma');
	if ($fila->fields['codigo_idioma'] != '') {
		$idioma->Load($fila->fields['codigo_idioma']);
	} else {
		$idioma->Load(strtolower(UtilesApp::GetConf($sesion, 'Idioma')));
	}
	return $fila->fields['ingreso'] > 0 ? $fila->fields['simbolo'] . " " . number_format($fila->fields['monto_cobrable'], $fila->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) : '';
}
