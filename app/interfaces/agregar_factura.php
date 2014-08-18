<?php
require_once dirname(__FILE__) . '/../conf.php';
//La funcionalidad contenida en esta pagina puede invocarse desde integracion_contabilidad3.php (SOLO GUARDAR).
//(desde_webservice ser� true). Esa pagina emula el POST, es importante revisar que los cambios realizados en la FORM
//se repliquen en el ingreso de datos via webservice.

if ($desde_webservice && UtilesApp::VerificarPasswordWebServices($usuario, $password)) {
	$sesion = new Sesion();
	$factura = new Factura($sesion);
} else { //ELSE (no es WEBSERVICE)
	$sesion = new Sesion(array('COB'));
	$pagina = new Pagina($sesion);
	$DocumentoLegalNumero = new DocumentoLegalNumero($sesion);
	$factura = new Factura($sesion);

	if ($id_cobro > 0) {
		$cobro = new Cobro($sesion);
		$cobro->load($id_cobro);
		$contrato = new Contrato($sesion);
		if (empty($id_contrato)) {
			$id_contrato = $cobro->fields['id_contrato'];
		}
		$contrato->Load($id_contrato);
	}

	if (!empty($id_factura)) {
		$factura->Load($id_factura);
		if (empty($codigo_cliente)) {
			$codigo_cliente = $factura->fields['codigo_cliente'];
		}
	} else {
		if (empty($codigo_cliente)) {
			$codigo_cliente = $cobro->fields['codigo_cliente'];
		}
	}

	if (!empty($codigo_cliente) && empty($codigo_cliente_secundario)) {
		$Cliente = new Cliente($sesion);
		$codigo_cliente_secundario = $Cliente->CodigoACodigoSecundario($codigo_cliente);
	}

	if ($factura->loaded() && !$id_cobro) {
		$id_cobro = $factura->fields['id_cobro'];
	}

	if ($factura->loaded()) {
		$id_documento_legal = $factura->fields['id_documento_legal'];
	}

	$query = "SELECT id_documento_legal, glosa, codigo FROM prm_documento_legal WHERE id_documento_legal = '$id_documento_legal'";
	$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $sesion->dbh);
	list($id_documento_legal, $tipo_documento_legal, $codigo_tipo_doc) = mysql_fetch_array($resp);

	if (!$tipo_documento_legal) {
		$pagina->FatalError('Error al cargar el tipo de Documento Legal');
	}

	if ($opc == 'generar_factura') {
		// POR HACER
		// mejorar
		if ($id_factura) {
			UtilesApp::generarFacturaPDF($id_factura, $sesion);
		} else {
			echo "Error";
		}
		exit;
	}

	$opc_inicial = $opcion;

	if ($opcion == "restaurar") {
		$opc_inicial = $opcion;
		$opcion = "guardar";
	}

	if ($opcion == "anular") {
		$data = array('Factura' => $factura);
		$Slim->applyHook('hook_anula_factura_electronica', &$data);
		$error = $data['Error'];
		if (!is_null($error)) {
			$pagina->AddError($error['Message'] ? $error['Message'] : __($error['Code']));
			$requiere_refrescar = "window.opener.Refrescar();";
		} else {
			$factura->Edit('estado', 'ANULADA');
			$factura->Edit("id_estado", $id_estado ? $id_estado : "1");
			$factura->Edit('anulado', 1);
			if ($factura->Escribir()) {
				$pagina->AddInfo(__('Documento Tributario') . ' ' . __('anulado con �xito'));
				$requiere_refrescar = "window.opener.Refrescar();";
			}
		}
	}
}

//FIN DE ELSE (No es WEBSERVICE)

if ($opcion == "guardar") {

	$guardar_datos = true;

	//El webservice ignora todo llamado a $pagina
	if ($desde_webservice) {
		$errores = array();
		if (!is_numeric($monto_honorarios_legales) || !is_numeric($monto_gastos_con_iva) || !is_numeric($monto_gastos_sin_iva)) {
			$errores[] = 'error';
		}
	} else {
		if (empty($cliente)) {
			$pagina->AddError(__('Debe ingresar la razon social del cliente.'));
		}
		if (Conf::GetConf($sesion, 'NuevoModuloFactura')) {
			if (!is_numeric($monto_honorarios_legales)) {
				$pagina->AddError(__('Debe ingresar un monto v�lido para los honorarios. (' . $monto_honorarios_legales . ')'));
			}
			if (!is_numeric($monto_gastos_con_iva)) {
				$pagina->AddError(__('Debe ingresar un monto v�lido para los gastos c/ IVA. (' . $monto_gastos_con_iva . ')'));
			}
			if (Conf::GetConf($sesion, 'UsarGastosConSinImpuesto') && !is_numeric($monto_gastos_sin_iva)) {
				$pagina->AddError(__('Debe ingresar un monto v�lido para los gastos s/ IVA. (' . $monto_gastos_sin_iva . ')'));
			}
		}
		($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_validar_factura') : false;
		$errores = $pagina->GetErrors();
	}

	if (!empty($errores)) {
		$guardar_datos = false;
	}

	if ($guardar_datos) {

		//chequear
		$mensaje_accion = 'guardar';
		$factura->Edit('subtotal', $monto_neto);
		$factura->Edit('porcentaje_impuesto', $porcentaje_impuesto);

		if ($comprobante_erp) {
			$factura->Edit('comprobante_erp', $comprobante_erp);
		}

		$factura->Edit('condicion_pago', '' . $condicion_pago);
		$factura->Edit('fecha_vencimiento', $fecha_vencimiento_pago_input ? Utiles::fecha2sql($fecha_vencimiento_pago_input) : "");
		$factura->Edit('iva', $iva);
		$factura->Edit('id_estudio', $id_estudio);
		$factura->Edit('total', '' . ($monto_neto + $iva));
		$factura->Edit("id_factura_padre", $id_factura_padre ? $id_factura_padre : NULL);
		$factura->Edit("fecha", Utiles::fecha2sql($fecha));
		$factura->Edit("cliente", $cliente ? addslashes($cliente) : "");
		$factura->Edit("RUT_cliente", $RUT_cliente ? $RUT_cliente : "");
		$factura->Edit("direccion_cliente", $direccion_cliente ? addslashes($direccion_cliente) : "");

		$factura->Edit("comuna_cliente", $comuna_cliente ? addslashes($comuna_cliente) : "");
		$factura->Edit("factura_codigopostal", $factura_codigopostal ? $factura_codigopostal : "");
		$factura->Edit("dte_metodo_pago", $dte_metodo_pago ? $dte_metodo_pago : "");
		$factura->Edit("dte_metodo_pago_cta", $dte_metodo_pago_cta ? $dte_metodo_pago_cta : "");

		if (!is_null($dte_id_pais) && !empty($dte_id_pais)) {
			$factura->Edit("dte_id_pais", $dte_id_pais ? $dte_id_pais : "");
		}

		$factura->Edit("ciudad_cliente", $ciudad_cliente ? addslashes($ciudad_cliente) : "");
		if (Conf::GetConf($sesion, 'RegionCliente')) {
			$factura->Edit("factura_region", $factura_region ? addslashes($factura_region) : "");
		}
		$factura->Edit("giro_cliente", $giro_cliente ? addslashes($giro_cliente) : "");
		$factura->Edit("codigo_cliente", $codigo_cliente ? $codigo_cliente : "");
		$factura->Edit("id_cobro", $id_cobro ? $id_cobro : NULL);
		$factura->Edit("id_documento_legal", $id_documento_legal ? $id_documento_legal : 1);
		$factura->Edit('serie_documento_legal', $serie);
		$factura->Edit("numero", $numero ? $numero : "1");
		$factura->Edit("id_estado", $id_estado ? $id_estado : "1");
		$factura->Edit("id_moneda", $id_moneda_factura ? $id_moneda_factura : "1");

		if ($id_estado == '5') {
			$factura->Edit('estado', 'ANULADA');
			$factura->Edit('anulado', 1);
			$mensaje_accion = 'anulado';
		} else if (!empty($factura->fields['anulado'])) {
			$factura->Edit('estado', 'ABIERTA');
			$factura->Edit('anulado', '0');
		}

		($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_agregar_factura') : false;

		if (Conf::GetConf($sesion, 'NuevoModuloFactura')) {
			$factura->Edit("descripcion", $descripcion_honorarios_legales);
			$factura->Edit("honorarios", $monto_honorarios_legales ? $monto_honorarios_legales : NULL);
			$factura->Edit("subtotal", $monto_honorarios_legales ? $monto_honorarios_legales : NULL);
			$factura->Edit("subtotal_sin_descuento", $monto_honorarios_legales ? $monto_honorarios_legales : NULL);
			$factura->Edit("descripcion_subtotal_gastos", $descripcion_gastos_con_iva ? $descripcion_gastos_con_iva : NULL);
			$factura->Edit("subtotal_gastos", $monto_gastos_con_iva ? $monto_gastos_con_iva : NULL);
			$factura->Edit("descripcion_subtotal_gastos_sin_impuesto", $descripcion_gastos_sin_iva ? $descripcion_gastos_sin_iva : NULL);
			$factura->Edit("subtotal_gastos_sin_impuesto", $monto_gastos_sin_iva ? $monto_gastos_sin_iva : NULL);
			$factura->Edit("total", $total ? $total : NULL);
			$factura->Edit("iva", $iva_hidden ? $iva_hidden : NULL);
		} else {
			$factura->Edit("descripcion", $descripcion);
		}

		if (Conf::GetConf($sesion, 'TipoDocumentoIdentidadFacturacion')) {
			$factura->Edit('id_tipo_documento_identidad', $tipo_documento_identidad);
		}

		$factura->Edit('letra', $letra);
		if ($letra_inicial) {
			$factura->Edit('letra', $letra_inicial);
		}

		if (empty($factura->fields['id_factura'])) {
			$generar_nuevo_numero = true;
		}

		if ($id_cobro && empty($factura->fields['id_factura'])) {
			if (!$cobro->Load($id_cobro)) {
				$cobro = null;
			}
			if ($cobro) {
				$factura->Edit('id_moneda', $cobro->fields['opc_moneda_total']);
			}
		}

		if (!$factura->ValidarDocLegal()) {
			if (empty($id_estudio)) {
				$estudios = PrmEstudio::GetEstudios($sesion);
				$id_estudio = $estudios[0]['id_estudio'];
			}

			$numero_documento_legal = $factura->ObtenerNumeroDocLegal($id_documento_legal, $serie, $id_estudio);

			if (!$desde_webservice) {
				$pagina->AddInfo('El numero ' . $numero . ' del ' . __('documento tributario') . ' ya fue usado, pero se ha asignado uno nuevo, por favor verifique los datos y vuelva a guardar');
				$factura->Edit('numero', $numero_documento_legal);
			} else {
				$resultado = array('error' => 'El n�mero ' . $numero . ' del ' . __('documento tributario') . ' ya fue usado, vuelva a intentar con n�mero: ' . $numero_documento_legal);
			}
		} else {
			if ($mensaje_accion == 'anulado') {
				$data_anular = array('Factura' => $factura);
				($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_anula_factura_electronica', &$data_anular) : false;
				$error_message = $data_anular['Error'];
				echo "<!-- {$error_message} -->";
				if (!is_null($error_message)) {
					$pagina->AddInfo($factura->fields["dte_estado_descripcion"] . " <br/>Para consultar el estado de su factura, puede dar clic en el �cono i (m�s informaci�n)");
					$factura->Load($id_factura);
				}
			}

			if (Conf::GetConf($sesion, 'UsarModuloProduccion')) {
				$factura->ActualizaGeneradores();
			}

			if ($factura->Escribir()) {
				if ($generar_nuevo_numero) {
					$factura->GuardarNumeroDocLegal($id_documento_legal, $numero, $serie, $id_estudio);
				}

				$signo = $codigo_tipo_doc == 'NC' ? 1 : -1; //es 1 o -1 si el tipo de doc suma o resta su monto a la liq
				$neteos = empty($id_factura_padre) ? null : array(array($id_factura_padre, $signo * $factura->fields['total']));

				$cta_cte_fact = new CtaCteFact($sesion);
				$mvto_guardado = $cta_cte_fact->RegistrarMvto($factura->fields['id_moneda'], $signo * ($factura->fields['total'] - $factura->fields['iva']), $signo * $factura->fields['iva'], $signo * $factura->fields['total'], $factura->fields['fecha'], $neteos, $factura->fields['id_factura'], null, $codigo_tipo_doc, $ids_monedas_documento, $tipo_cambios_documento, !empty($factura->fields['anulado']));


				if ($mvto_guardado->fields['tipo_mvto'] != 'NC' && $mvto_guardado->fields['saldo'] == 0 && $mvto_guardado->fields['anulado'] != 1) {
					$query = "SELECT id_estado FROM prm_estado_factura WHERE codigo = 'C'";
					$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $sesion->dbh);
					list($id_estado_cobrado) = mysql_fetch_array($resp);

					$factura->Edit('id_estado', $id_estado_cobrado);
				}

				//El webservice ignora todo llamado a $pagina
				if (!$desde_webservice) {
					if ($opc_inicial != 'restaurar') {
						$pagina->AddInfo(__('Documento Tributario') . ' ' . $mensaje_accion . ' ' . __(' con �xito'));
					}
				}
				$requiere_refrescar = "window.opener.Refrescar();";



				# Esto se puede descomentar para imprimir facturas desde la edici�n

				if ($id_cobro) {

					if ($cobro->Load($id_cobro)) {
						$cobro->CambiarEstadoSegunFacturas();
					}

					$cobro->AgregarFactura($factura);

					if ($usar_adelantos && empty($factura->fields['anulado']) && $codigo_tipo_doc != 'NC') {
						$documento = $cobro->DocumentoCobro();
						$documento->GenerarPagosDesdeAdelantos($documento->fields['id_documento'], array($factura->fields['id_factura'] => $factura->fields['total']));
					}
				}
			}
		}

		$observacion = new Observacion($sesion);
		$observacion->Edit('fecha', date('Y-m-d H:i:s'));
		$observacion->Edit('comentario', "MODIFICACI�N FACTURA");
		$observacion->Edit('id_usuario', $sesion->usuario->fields['id_usuario']);
		$observacion->Edit('id_factura', $factura->fields['id_factura']);
		$observacion->Write();
	}
}

//Fin opcion guardar

if ($desde_webservice) {
	if ($factura->fields['id_factura']) {
		$resultado = array(
			'id_factura' => $factura->fields['id_factura'],
			'descripci�n' => 'El ' . __('documento tributario') . ' se ha guardado exitosamente.'
		);
	}
	return 'EXITO';
	//Si vengo del webservice, no continua.
}

// Se ingresa la anotaci�n de modificaci�n de factura en el historial
if (!$id_factura && $factura->loaded()) {
	$id_factura = $factura->fields['id_factura'];
}

$titulo_pagina = $txt_pagina = $id_factura ? __('Edici�n de ') . $tipo_documento_legal . ' #' . $factura->fields['numero'] : __('Ingreso de ') . $tipo_documento_legal;

if ($id_cobro) {
	$titulo_pagina .= ' ' . __('para Cobro') . ' #' . $id_cobro;
	$txt_pagina .= ' ' . __('para Cobro') . '&nbsp; <a href="cobros6.php?id_cobro=' . $id_cobro . '&popup=1">#' . $id_cobro . '</a>';
}

$pagina->titulo = $titulo_pagina;
$pagina->PrintTop($popup);


/* Mostrar valores por defecto */

//SIN DESGLOSE
$suma_monto = 0;
$suma_iva = 0;
$suma_total = 0;

//CON DESGLOSE
$descripcion_honorario = __(Conf::GetConf($sesion, 'FacturaDescripcionHonorarios'));

$monto_honorario = 0;
$descripcion_subtotal_gastos = __(Conf::GetConf($sesion, 'FacturaDescripcionGastosConIva'));
$monto_subtotal_gastos = 0;
$descripcion_subtotal_gastos_sin_impuesto = __(Conf::GetConf($sesion, 'FacturaDescripcionGastosSinIva'));
$monto_subtotal_gastos_sin_impuesto = 0;


//ASIGNO LOS MONTOS POR DEFECTO DE LOS DOCUMENTOS
$x_resultados = UtilesApp::ProcesaCobroIdMoneda($sesion, $id_cobro, array(), $cobro->fields['opc_moneda_total'], true);

$opc_moneda_total = $x_resultados['opc_moneda_total'];
$id_moneda_factura = $opc_moneda_total;

if (!empty($factura->fields['id_factura'])) {
	$id_moneda_factura = $factura->fields['id_moneda'];
}

$cifras_decimales_opc_moneda_total = $x_resultados['cifras_decimales_opc_moneda_total'];
$subtotal_honorarios = $x_resultados['monto_honorarios'][$opc_moneda_total];
$subtotal_gastos_sin_impuestos = $x_resultados['subtotal_gastos_sin_impuesto'][$opc_moneda_total];
$subtotal_gastos = $x_resultados['subtotal_gastos'][$opc_moneda_total] - $subtotal_gastos_sin_impuestos;
$impuesto_gastos = $x_resultados['impuesto_gastos'][$opc_moneda_total];
$impuesto = $x_resultados['impuesto'][$opc_moneda_total];

//SIN DESGLOSE
$suma_monto = $subtotal_honorarios + $subtotal_gastos;
$suma_iva = $impuesto_gastos + $impuesto;
$suma_total = $subtotal_honorarios + $subtotal_gastos + $impuesto_gastos + $impuesto;

//CON DESGLOSE
$cobro_ = new Cobro($sesion);
$descripcion_honorario = __(Conf::GetConf($sesion, 'FacturaDescripcionHonorarios'));

if ($descripcion_honorario == '') {
	$descripcion_honorario = $contrato->fields['glosa_contrato'];
}

if (Conf::GetConf($sesion, 'DescripcionFacturaConAsuntos')) {
	$descripcion_honorario .= "\n" . implode(', ', $cobro_->AsuntosNombreCodigo($id_cobro));
}

$monto_honorario = $subtotal_honorarios;
$descripcion_subtotal_gastos = __(Conf::GetConf($sesion, 'FacturaDescripcionGastosConIva'));
$monto_subtotal_gastos = $subtotal_gastos;
$descripcion_subtotal_gastos_sin_impuesto = __(Conf::GetConf($sesion, 'FacturaDescripcionGastosSinIva'));
$monto_subtotal_gastos_sin_impuesto = $subtotal_gastos_sin_impuestos;

if ($factura->loaded()) {
	$porcentaje_impuesto = $factura->fields['porcentaje_impuesto'];
} else if ($id_cobro > 0) {
	$porcentaje_impuesto = $cobro->fields['porcentaje_impuesto'];
} else {
	$porcentaje_impuesto = 0;
}

$query_moneda = "SELECT m.simbolo , m.glosa_moneda, m.cifras_decimales FROM prm_moneda m WHERE m.id_moneda = " . $id_moneda_factura;
$resp_moneda = mysql_query($query_moneda, $sesion->dbh) or Utiles::errorSQL($resp_moneda, __FILE__, __LINE__, $sesion->dbh);
list($simbolo, $glosa_moneda, $cifras_decimales) = mysql_fetch_array($resp_moneda);
$simbolosinadorno = $simbolo;

if ($factura->fields['total'] > 0) {
	$simbolo = "<span style='padding-left:5px'>" . $simbolo . "</span>";

	//SIN DESGLOSE
	if ($factura->fields['subtotal']) {
		$suma_monto = $factura->fields['subtotal'];
	}

	if ($factura->fields['iva']) {
		$suma_iva = $factura->fields['iva'];
	}

	if ($factura->fields['total']) {
		$suma_total = $factura->fields['total'];
	}

	//CON DESGLOSE
	$descripcion_honorario = $factura->fields['descripcion'];
	$monto_honorario = $factura->fields['subtotal'];
	$honorario = $factura->fields['subtotal'];
	$descripcion_subtotal_gastos = $factura->fields['descripcion_subtotal_gastos'];
	$monto_subtotal_gastos = $factura->fields['subtotal_gastos'];
	$descripcion_subtotal_gastos_sin_impuesto = $factura->fields['descripcion_subtotal_gastos_sin_impuesto'];
	$monto_subtotal_gastos_sin_impuesto = $factura->fields['subtotal_gastos_sin_impuesto'];

	if ($descripcion_honorario == '' && $monto_honorario > 0) {
		$descripcion_honorario = __('Honorarios Legales');
		if (Conf::GetConf($sesion, 'DescripcionFacturaConAsuntos')) {
			$descripcion_honorario .= "\n" . implode(', ', $cobro_->AsuntosNombreCodigo($id_cobro));
		}
	}

	if ($descripcion_subtotal_gastos == '') {
		$descripcion_subtotal_gastos = __('Gastos c/ IVA');
	}

	if ($descripcion_subtotal_gastos_sin_impuesto == '') {
		$descripcion_subtotal_gastos_sin_impuesto = __('Gastos s/ IVA');
	}
}

if ($monto_honorario == '') {
	$monto_honorario = 0;
}

if ($monto_subtotal_gastos == '') {
	$monto_subtotal_gastos = 0;
}

if ($monto_subtotal_gastos_sin_impuesto == '') {
	$monto_subtotal_gastos_sin_impuesto = 0;
}

/*
 * FIN - Mostrar valores por defecto
 */
//echo Autocompletador::CSS();
?>

<form method=post id="form_facturas" name="form_facturas">
	<input type="hidden" name="opcion" value="" />
	<input type='hidden' name="id_factura" id="id_factura" value="<?php echo $factura->fields['id_factura']; ?>" />
	<input type="hidden" name="id_documento_legal" value="<?php echo $id_documento_legal; ?>" />
	<input type="hidden" name="elimina_ingreso" id="elimina_ingreso" value="" />
	<input type="hidden" name="id_cobro" id="id_cobro" value="<?php echo $id_cobro; ?>" />
	<input type="hidden" name="subTotal" id="subTotal" value="<?php echo $suma_monto; ?>" />
	<input type="hidden" name="id_contrato" id="id_contrato" value='<?php echo $id_contrato ?>'/>
	<input type="hidden" name="id_moneda_factura" id="id_moneda_factura" value='<?php echo $id_moneda_factura ?>'/>
	<input type="hidden" class="aproximable" name="honorario_disp" id="honorario_disp" value='<?php echo $honorario_disp ?>'/>
	<input type="hidden" class="aproximable" name="gastos_con_impuestos_disp" id="gastos_con_impuestos_disp" value='<?php echo $gastos_con_impuestos_disp ?>'/>
	<input type="hidden" class="aproximable" name="gastos_sin_impuestos_disp" id="gastos_sin_impuestos_disp" value='<?php echo $gastos_sin_impuestos_disp ?>'/>
	<input type="hidden" class="aproximable" name="honorario_total" id="honorario_total" value='<?php echo $honorario_total ?>'/>
	<input type="hidden" class="aproximable" name="gastos_con_impuestos_total" id="gastos_con_impuestos_total" value='<?php echo $gastos_con_impuestos_total ?>'/>
	<input type="hidden" class="aproximable" name="gastos_sin_impuestos_total" id="gastos_sin_impuestos_total" value='<?php echo $gastos_sin_impuestos_total ?>'/>
	<input type='hidden' name='opc' id='opc' value='buscar'>
	<input type="hidden" name="porcentaje_impuesto" id="porcentaje_impuesto" value="<?php echo $porcentaje_impuesto; ?>">
	<input type="hidden" name="usar_adelantos" id="usar_adelantos" value="0"/>

	<!-- Calendario DIV -->
	<div id="calendar-container" style="width:221px; position:absolute; display:none;">
		<div class="floating" id="calendar"></div>
	</div>
	<!-- Fin calendario DIV -->
	<br>

	<table width='90%'>
		<tr>
			<td align="left">
				<b><?php echo $txt_pagina; ?></b>
			</td>
		</tr>
	</table>

	<br>

	<table style="border: 0px solid black;" width='90%'>
		<tr>
			<td align="left">
				<b><?php echo __('Informaci�n de') . ' ' . $tipo_documento_legal; ?></b>
			</td>
		</tr>
	</table>

	<table class="border_plomo" style="background-color:#FFFFFF;" width='95%'>
		<tbody>
			<tr>
				<td id="controles_factura" colspan="4" align="center"></td>
			</tr>
			<?php
			// Si no viene de un POST puede ser nuevo o existente, si es nuevo ocupo el del $contrato
			if (empty($id_estudio)) {
				$id_estudio = !empty($factura->fields['id_estudio']) ? $factura->fields['id_estudio'] : $contrato->fields['id_estudio'];
			}

			$estudios_array = PrmEstudio::GetEstudios($sesion);
			if (count($estudios_array) > 1) {
			?>
				<tr>
					<td align="right"><?php echo __('Compan�a'); ?></td>
					<td align="left" colspan="3">
						<?php echo Html::SelectArray($estudios_array, 'id_estudio', $id_estudio, 'id="id_estudio" onchange="cambiarEstudio(this.value)"', '', '300px'); ?>
					</td>
				</tr>
			<?php } else { ?>
				<input type="hidden" name="id_estudio" id="id_estudio" value="<?php echo $estudios_array[0]['id_estudio']; ?>" />
			<?php } ?>

			<?php
			$numero_documento = '';

			if (Conf::GetConf($sesion, 'NuevoModuloFactura')) {
				$serie = $DocumentoLegalNumero->SeriesPorTipoDocumento($id_documento_legal, true);
				$numero_documento = $factura->ObtenerNumeroDocLegal($id_documento_legal, $serie, $id_estudio);
			} else if (Conf::GetConf($sesion, 'UsaNumeracionAutomatica')) {
				$numero_documento = $factura->ObtieneNumeroFactura();
			}
			?>
			<tr>
				<td width="140" align="right"><?php echo __('N�mero'); ?></td>
				<td align="left">
					<?php
					if (Conf::GetConf($sesion, 'NumeroFacturaConSerie')) {
						$serie_documento_legal = $factura->fields['serie_documento_legal'];
						echo Html::SelectQuery($sesion, $DocumentoLegalNumero->SeriesQuery($id_estudio), 'serie', $serie_documento_legal, 'onchange="NumeroDocumentoLegal()"', null, 60);
					} else {
						$serie_documento_legal = $DocumentoLegalNumero->SeriesPorTipoDocumento(1, true);
						?>
						<input type="hidden" name="serie" id="serie" value="<?php echo $serie_documento_legal; ?>">
					<?php } ?>
					<input type="text" name="numero" value="<?php echo $factura->fields['numero'] ? $factura->fields['numero'] : $numero_documento; ?>" id="numero" size="11" maxlength="10" />
				</td>
				<td align="right"><?php echo __('Estado'); ?></td>
				<?php
					$deshabilita_estado = ($factura->fields['anulado'] == 1 && ($factura->DTEAnulado() || $factura->DTEProcesandoAnular())) ? 'disabled' : '';
				?>
				<td align="left" nowrap>
					<?php echo Html::SelectQuery($sesion, "SELECT id_estado, glosa FROM prm_estado_factura ORDER BY id_estado ASC", "id_estado", $factura->fields['id_estado'] ? $factura->fields['id_estado'] : $id_estado, 'onchange="mostrarAccionesEstado(this.form)" ' . $deshabilita_estado, '', "160"); ?>
					<?php ($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_factura_dte_estado') : false; ?>
				</td>
			</tr>

			<?php
			//Se debe elegir un documento legal padre si:
			$buscar_padre = false;

			$query_doc = "SELECT codigo FROM prm_documento_legal WHERE id_documento_legal = '$id_documento_legal'";
			$resp_doc = mysql_query($query_doc, $sesion->dbh) or Utiles::errorSQL($query_doc, __FILE__, __LINE__, $sesion->dbh);
			list($codigo_documento_legal) = mysql_fetch_array($resp_doc);

			if (($codigo_documento_legal == 'NC') && ($id_cobro || $codigo_cliente)) {
				$glosa_numero_serie = Conf::GetConf($sesion, 'NumeroFacturaConSerie') ? "prm_documento_legal.glosa,' #', factura.serie_documento_legal, '-', numero" : "prm_documento_legal.glosa, ' #', numero";
				if ($id_cobro) {
					$query_padre = "SELECT id_factura, CONCAT({$glosa_numero_serie}) FROM factura JOIN prm_documento_legal USING (id_documento_legal) WHERE id_cobro = '{$id_cobro}'";
				} else if ($codigo_cliente) {
					$query_padre = "SELECT id_factura, CONCAT({$glosa_numero_serie}) FROM factura JOIN prm_documento_legal USING (id_documento_legal) WHERE codigo_cliente = '{$codigo_cliente}'";
				}
				$resp_padre = mysql_query($query_padre, $sesion->dbh) or Utiles::errorSQL($query_padre, __FILE__, __LINE__, $sesion->dbh);
				if (list($a, $b) = mysql_fetch_array($resp_padre)) {
					$buscar_padre = true;
				}
			}

			if ($buscar_padre) {
			?>
			<tr>
				<td align="right"><?php echo __('Para Documento Tributario:') ?></td>
				<td align="left" colspan="3"><?php echo Html::SelectQuery($sesion, $query_padre, 'id_factura_padre', $factura->fields['id_factura_padre'], '', '--', '160') ?></td>
			</tr>
			<?php } ?>

		<?php
		$zona_horaria = Conf::GetConf($sesion, 'ZonaHoraria');

		if ($zona_horaria) {
			date_default_timezone_set($zona_horaria);
		}
		?>
		<tr>
			<td align="right"><?php echo __('Fecha') ?></td>
			<td align="left" colspan=2><input type="text" name="fecha" clase="fechadiff" value="<?php echo $factura->fields['fecha'] ? Utiles::sql2date($factura->fields['fecha']) : date('d-m-Y') ?>" id="fecha" size="11" maxlength="10" /></td>

			<td><span style='display:none' id=letra_inicial>&nbsp;&nbsp;
		<?php echo __('Letra') ?>
					:&nbsp;
					<input name='letra_inicial' value='<?php echo $factura->fields['letra'] ? $factura->fields['letra'] : '' ?>' size=10/>
				</span></td>
		</tr>
		<tr>
			<td align="right"><?php echo __('Cliente') ?></td>
			<td align="left" colspan="3">
				<?php UtilesApp::CampoCliente($sesion, $codigo_cliente, $codigo_cliente_secundario, $codigo_asunto, $codigo_asunto_secundario);?>
			</td>
		</tr>
		<tr style="display:none;">
			<td><?php UtilesApp::CampoAsunto($sesion, $codigo_cliente, $codigo_cliente_secundario, $codigo_asunto, $codigo_asunto_secundario); ?></td>
		</tr>

		<tr>
			<?php if (Conf::GetConf($sesion, 'TipoDocumentoIdentidadFacturacion')) { ?>
				<td align="right"><?php echo __('Doc. Identidad'); ?></td>
				<td align="left" colspan="3">
					<?php echo Html::SelectQuery($sesion, "SELECT id_tipo_documento_identidad, glosa FROM prm_tipo_documento_identidad", "tipo_documento_identidad", $factura->fields['id_tipo_documento_identidad'], "", " ", 150); ?>
					<input type="text" name="RUT_cliente" value="<?php echo $factura->loaded() ? $factura->fields['RUT_cliente'] : $contrato->fields['rut']; ?>" id="RUT_cliente" size="40" maxlength="20" />
				</td>
			<?php } else { ?>
				<td align="right"><?php echo __('ROL/RUT'); ?></td>
				<td align="left" colspan="3">
					<input type="text" name="RUT_cliente" value="<?php echo $factura->loaded() ? $factura->fields['RUT_cliente'] : $contrato->fields['rut']; ?>" id="RUT_cliente" size="70" maxlength="20" />
				</td>
			<?php } ?>
		</tr>
		<tr>
			<td align="right"><?php echo __('Raz&oacute;n Social Cliente'); ?></td>
			<td align="left" colspan="3">
				<input type="text" name="cliente" value="<?php echo $factura->loaded() ? $factura->fields['cliente'] : $contrato->fields['factura_razon_social']; ?>" id="cliente" size="70"/>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo __('Direcci&oacute;n Cliente'); ?></td>
			<td align="left" colspan="3">
				<input type="text" name="direccion_cliente" value="<?php echo $factura->loaded() ? $factura->fields['direccion_cliente'] : $contrato->fields['factura_direccion']; ?>" id="direccion_cliente" size="70" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo __('Comuna'); ?></td>
			<td align="left" colspan="3">
				<input type="text" name="comuna_cliente" value="<?php echo $factura->loaded() ? $factura->fields['comuna_cliente'] : $contrato->fields['factura_comuna']; ?>" id="comuna_cliente" size="70" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo __('C�digo Postal'); ?></td>
			<td align="left" colspan="3"><input type="text" name="factura_codigopostal" value="<?php echo $factura->loaded() ? $factura->fields['factura_codigopostal'] : $contrato->fields['factura_codigopostal']; ?>" id="factura_codigopostal" size="30" maxlength="20" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo __('Ciudad'); ?></td>
			<td align="left" colspan="3"><input type="text" name="ciudad_cliente" value="<?php echo $factura->loaded() ? $factura->fields['ciudad_cliente'] : $contrato->fields['factura_ciudad']; ?>" id="ciudad_cliente" size="70" maxlength="255" />
			</td>
		</tr>
		<?php if (Conf::GetConf($sesion, 'RegionCliente')) { ?>
		<tr>
			<td align="right"><?php echo __('Regi�n'); ?></td>
			<td align="left" colspan="3"><input type="text" name="factura_region" value="<?php echo $factura->loaded() ? $factura->fields['factura_region'] : $contrato->fields['region_cliente']; ?>" id="factura_region" size="70" maxlength="255" />
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td align="right"><?php echo __('Giro'); ?></td>
			<td align="left" colspan="3">
				<input type="text" name="giro_cliente" value="<?php echo $factura->loaded() ? $factura->fields['giro_cliente'] : $contrato->fields['factura_giro']; ?>" id="giro_cliente" size="70" maxlength="255" />
			</td>
		</tr>
		<?php ($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_factura_metodo_pago') : false; ?>
		<tr>
			<td align="right"><?php echo __('Condici�n de Pago') ?></td>
			<td align="left" colspan="3">
				<select type="text" name="condicion_pago" value="<?php echo $factura->fields['condicion_pago'] ?>" id="condicion_pago" >
					<?php
					$condiciones_pago = array(
						1 => 'CONTADO',
						3 => 'CC 15 d�as',
						4 => 'CC 30 d�as',
						5 => 'CC 45 d�as',
						6 => 'CC 60 d�as',
						7 => 'CC 75 d�as',
						8 => 'CC 90 d�as',
						9 => 'CC 120 d�as',
						12 => 'LETRA 30 d�as',
						13 => 'LETRA 45 d�as',
						14 => 'LETRA 60 d�as',
						15 => 'LETRA 90 d�as',
						18 => 'CHEQUE 30 d�as',
						19 => 'CHEQUE 45 d�as',
						20 => 'CHEQUE 60 d�as',
						21 => 'CHEQUE A FECHA'
					);
					foreach ($condiciones_pago as $vc => $cond) {
						echo "<option ";
						if ($factura->fields['condicion_pago'] == $vc) {
							echo "selected";
						}
						echo " value=" . $vc . ">" . str_pad($vc, 2, '0', STR_PAD_LEFT) . ': ' . $cond . "</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="fecha_vencimiento_pago" style="visibility: visible;">
			<td align="right" ><?php echo __('Fecha Vencimiento')?></td>
			<td align="left" colspan="3" ><input type="text" name="fecha_vencimiento_pago_input" id="fecha_vencimiento_pago_input" value="<?php echo $factura->fields['fecha_vencimiento'] ? Utiles::sql2date($factura->fields['fecha_vencimiento']) : date('d-m-Y') ?>" size="11" maxlength="10" /></td>
		</tr>

		<?php
		$cantidad_lineas_descripcion = Conf::GetConf($sesion, 'CantidadLineasDescripcionFacturas');
		if (Conf::GetConf($sesion, 'NuevoModuloFactura')) {
			?>
			<tr id='descripcion_factura'>
				<td align="right" width="100">&nbsp;</td>
				<td align="left" style="vertical-align:bottom" width="250"><?php echo __('Descripci�n'); ?></td>
				<td align="left" width="100"><?php echo __('Monto'); ?></td>
				<td align="left"><?php echo __('Monto Impuesto'); ?></td>
			</tr>

			<tr id="fila_descripcion_honorarios_legales">
				<td id="glosa_honorarios_legales" align="right"><?php echo __('Honorarios legales'); ?></td>
				<td align="left">
					<?php
					if (Conf::GetConf($sesion, 'DescripcionFacturaConAsuntos')) {
						?>
						<textarea id="descripcion_honorarios_legales" name="descripcion_honorarios_legales"  id="descripcion_honorarios_legales" cols="50" rows="5" style="font-family: Arial; font-size: 11px"><?php echo trim($descripcion_honorario); ?></textarea>
						<?php
					} else if ($cantidad_lineas_descripcion > 1) {
						?>
						<textarea  id="descripcion_honorarios_legales"  name="descripcion_honorarios_legales"  id="descripcion_honorarios_legales" cols="50" rows="<?php echo $cantidad_lineas_descripcion ?>" style="font-family: Arial; font-size: 11px; text-align: left;"><?php echo trim($descripcion_honorario); ?></textarea>
						<?php
					} else {
						?>
						<input type="text" name="descripcion_honorarios_legales" id="descripcion_honorarios_legales" value="<?php echo trim($descripcion_honorario); ?>" maxlength="250" size="40" />
						<?php
					}
					?>
				</td>

				<td id="td_honorarios_legales"  align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" name="monto_honorarios_legales" class="aproximable"  id="monto_honorarios_legales" value="<?php echo isset($honorario) ? $honorario : $monto_honorario; ?>" size="10" maxlength="30" onblur="desgloseMontosFactura(this.form)"; onkeydown="MontoValido(this.id);"></td>
				<td id="td_impto_honorarios_legales" align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" name="monto_iva_honorarios_legales" class="aproximable"   id="monto_iva_honorarios_legales" value="<?php echo $impuesto; ?>" disabled="true" value="0" size="10" maxlength="30" onkeydown="MontoValido(this.id);"></td>
			</tr>

			<tr id="fila_descripcion_gastos_con_iva">
				<td align="right"><?php echo __('Gastos c/ IVA'); ?></td>
				<td align="left">
					<?php if ($cantidad_lineas_descripcion > 1) { ?>
						<textarea id="descripcion_gastos_con_iva" name="descripcion_gastos_con_iva" cols="50" rows="<?php echo $cantidad_lineas_descripcion ?>" style="font-family: Arial; font-size: 11px; text-align: left;"><?php echo trim($descripcion_subtotal_gastos); ?></textarea>
					<?php } else { ?>
						<input type="text" id="descripcion_gastos_con_iva" name="descripcion_gastos_con_iva" value="<?php echo trim($descripcion_subtotal_gastos); ?>" size="40" maxlength="250">
					<?php } ?>
				</td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" name="monto_gastos_con_iva"  class="aproximable"  id="monto_gastos_con_iva" value="<?php echo isset($gastos_con_iva) ? $gastos_con_iva : $monto_subtotal_gastos; ?>" size="10" maxlength="30" onblur="desgloseMontosFactura(this.form)"  >
				</td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" name="monto_iva_gastos_con_iva" class="aproximable"   id="monto_iva_gastos_con_iva" value="<?php echo $impuesto_gastos; ?>" disabled="true" value="0" size="10" maxlength="30" >
				</td>
			</tr>

			<tr id="fila_monto_gastos_sin_iva"  <?php echo (!Conf::GetConf($sesion, 'UsarGastosConSinImpuesto')) ? "style='display:none;'" : ""; ?> >
				<td align="right"><?php echo __('Gastos s/ IVA'); ?></td>
				<td align="left">
					<?php if ($cantidad_lineas_descripcion > 1) { ?>
						<textarea id="descripcion_gastos_sin_iva" name="descripcion_gastos_sin_iva" cols="50" rows="<?php echo $cantidad_lineas_descripcion ?>" style="font-family: Arial; font-size: 11px; text-align: left;"><?php echo trim($descripcion_subtotal_gastos_sin_impuesto); ?></textarea>
					<?php } else { ?>
						<input type="text" id="descripcion_gastos_sin_iva" name="descripcion_gastos_sin_iva"     id="descripcion_gastos_sin_iva" value="<?php echo trim($descripcion_subtotal_gastos_sin_impuesto); ?>" size="40" maxlength="250" >
					<?php } ?>
				</td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" name="monto_gastos_sin_iva"  class="aproximable"  id="monto_gastos_sin_iva" value="<?php echo isset($gastos_sin_iva) ? $gastos_sin_iva : $monto_subtotal_gastos_sin_impuesto; ?>" size="10" maxlength="30"   ></td>
				<td align="left">&nbsp;</td>
			</tr>

			<tr>
				<td align="right" colspan=2 ><?php echo __('Monto') ?></td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text"  class="aproximable"  name="monto_neto" id='monto_neto' value="<?php echo $suma_monto; ?>" size="10" maxlength="30" disabled="true"  /></td>
				<td align="left">&nbsp;</td>
			</tr>

			<tr id='descripcion_factura'>
				<td align="right" colspan=2><?php echo __('Impuesto') ?></td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" id='iva'  class="aproximable"  name="iva" value="<?php echo $suma_iva; ?>" size="10" maxlength="30" disabled="true"  />
					<input type="hidden" id='iva_hidden'   class="aproximable" name="iva_hidden"></td>
			</tr>

			<tr id='descripcion_factura'>
				<td align="right" colspan=2><?php echo __('Monto Total') ?></td>
				<td align="left" nowrap><?php echo $simbolo; ?>
					<input type="text" id='total' name="total"  class="aproximable"  value="<?php echo $suma_total; ?>" size="10" maxlength="30"  readonly="readonly"></td>
				<td>&nbsp;</td>
			</tr>

		<?php } else { ?>

			<tr id='descripcion_factura'>
				<td align="right"><?php echo __('Descripci�n') ?></td>
				<td align="left"><textarea id="descripcion" name="descripcion" cols="45" rows="3"><?php echo $factura->loaded() ? $factura->fields['giro_cliente'] : $contrato->fields['glosa_contrato']; ?></textarea></td>
			</tr>
			<tr id='descripcion_factura'>
				<td align="right"><?php echo __('Monto') ?></td>
				<td align="left"><input type="text" name="monto_neto" class="aproximable"  id='monto_neto' value="<?php echo $suma_monto; ?>" onchange="var total = Number($('monto_neto').value.replace(',', '.')) + Number($('iva').value.replace(',', '.'));
							$('total').value = total.toFixed(2);" /></td>
			</tr>
			<tr id='descripcion_factura'>
				<td align="right"><?php echo __('Impuesto') ?></td>
				<td align="left"><input type="text" id='iva' name="iva" class="aproximable"  value="<?php echo $suma_iva; ?>" size="10" maxlength="30"   onchange="var total = Number($('monto_neto').value.replace(',', '.')) + Number($('iva').value.replace(',', '.'));
							$('total').value = total.toFixed(2);" /></td>
			</tr>
			<tr id='descripcion_factura'>
				<td align="right"><?php echo __('Monto Total') ?></td>
				<td align="left"><input type="text" id='total' name="total"  class="aproximable"  value="<?php echo $suma_total; ?>" size="10" maxlength="30"  readonly="readonly"></td>
			</tr>
			<?php
		}
		?>


		<tr>
			<td align="right" colspan="4">
				<div id="TipoCambioFactura" style="display:none; left: 100px; top: 300px; background-color: white; position:absolute; z-index: 4;">
					<fieldset style="background-color:white;">
						<legend>
							<?php echo __('Tipo de Cambio Documento de Pago') ?>
						</legend>
						<div id="contenedor_tipo_load">&nbsp;</div>
						<div id="contenedor_tipo_cambio">
							<table style='border-collapse:collapse;' cellpadding='3'>
								<tr>
									<?php
									if ($factura->fields['id_factura']) {
										$query = "SELECT count(*)
									FROM cta_cte_fact_mvto_moneda
									LEFT JOIN cta_cte_fact_mvto AS ccfm ON ccfm.id_cta_cte_mvto=cta_cte_fact_mvto_moneda.id_cta_cte_fact_mvto
									WHERE ccfm.id_factura = '" . $factura->fields['id_factura'] . "'";
										$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $sesion->dbh);
										list($cont) = mysql_fetch_array($resp);
									} else {
										$cont = 0;
									}
									if ($cont > 0) {
										$query = "SELECT prm_moneda.id_moneda, glosa_moneda, cta_cte_fact_mvto_moneda.tipo_cambio
									FROM cta_cte_fact_mvto_moneda
									JOIN prm_moneda ON cta_cte_fact_mvto_moneda.id_moneda = prm_moneda.id_moneda
									LEFT JOIN cta_cte_fact_mvto ON cta_cte_fact_mvto.id_cta_cte_mvto = cta_cte_fact_mvto_moneda.id_cta_cte_fact_mvto
									WHERE cta_cte_fact_mvto.id_factura = '" . $factura->fields['id_factura'] . "'";
										$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $sesion->dbh);
									} else {
										$query = "SELECT prm_moneda.id_moneda, glosa_moneda, cobro_moneda.tipo_cambio
									FROM cobro_moneda
									JOIN prm_moneda ON cobro_moneda.id_moneda = prm_moneda.id_moneda
									WHERE id_cobro = '" . $id_cobro . "'";
										$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $sesion->dbh);
									}
									$num_monedas = 0;
									$ids_monedas = array();
									$tipo_cambios = array();
									while (list($id_moneda, $glosa_moneda, $tipo_cambio) = mysql_fetch_array($resp)) {
										?>
										<td><span><b>
													<?php echo $glosa_moneda ?>
												</b></span><br>
											<input type='text' size=9 id='factura_moneda_<?php echo $id_moneda ?>' name='factura_moneda_<?php echo $id_moneda ?>' value='<?php echo $tipo_cambio ?>' /></td>
										<?php
										$num_monedas++;
										$ids_monedas[] = $id_moneda;
										$tipo_cambios[] = $tipo_cambio;
									}
									?>
								</tr>
								<tr>
									<td colspan=<?php echo $num_monedas ?> align=center>
										<a href="javascript:void(0);" icon="ui-icon-save" onclick="ActualizarDocumentoMonedaPago($('todo_cobro'))"><?php echo __('Guardar') ?></a>
										<a href="javascript:void(0);" icon="ui-icon-exitl" onclick="CancelarDocumentoMonedaPago()"><?php echo __('Cancelar') ?></a>
										<input type="hidden" id="tipo_cambios_factura" name="tipo_cambios_factura" value="<?php echo implode(',', $tipo_cambios) ?>" />
										<input type="hidden" id="ids_monedas_factura" name="ids_monedas_factura" value="<?php echo implode(',', $ids_monedas) ?>" /></td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<br>

	<table style="border: 0px solid #666;" width='95%'>
		<tbody>
		<tr>
			<td align="left">
				<a class="btn botonizame" href="javascript:void(0);" icon="ui-icon-save" onclick="return Validar(jQuery('#form_facturas').get(0));"><?php echo __('Guardar') ?></a>
				<a class="btn botonizame"  href="javascript:void(0);" icon="ui-icon-exit" onclick="Cerrar();" ><?php echo __('Cancelar') ?></a>
				<?php if ($factura->loaded() && $factura->fields['anulado'] == 1 && !$factura->DTEAnulado() && !$factura->DTEAnulado() && !$factura->DTEProcesandoAnular()) { ?>
					<a class="btn botonizame" href="javascript:void(0);" icon="ui-icon-restore" onclick="return Cambiar(jQuery('#form_facturas').get(0), 'restaurar');"><?php echo __('Restaurar') ?></a>
				<?php } ?>
				<a class="btn botonizame" icon="ui-icon-money" href='javascript:void(0)' onclick="MostrarTipoCambioPago()" title="<?php echo __('Tipo de Cambio del Documento de Pago al ser pagado.') ?>"><?php echo __('Actualizar Tipo de Cambio') ?></a>
			</td>
		</tr>
		</tbody>
	</table>
</form>
<script  type="text/javascript" src="https://static.thetimebilling.com/js/typewatch.js"></script>

<script type="text/javascript">
	var cantidad_decimales = <?php echo intval($cifras_decimales_opc_moneda_total); ?>;
	var string_decimales = "<?php echo str_pad('', $cifras_decimales_opc_moneda_total, '0'); ?>";
	var porcentaje_impuesto = "<?php echo $porcentaje_impuesto; ?>";
	var saldo_trabajos = "<?php echo $x_resultados['monto_trabajos'][$opc_moneda_total]; ?>";
	var saldo_tramites = "<?php echo $x_resultados['monto_trabajos'][$opc_moneda_total]; ?>";

	<?php
	if ($id_cobro > 0) {
		echo "var porcentaje_impuesto_gastos = '{$cobro->fields['porcentaje_impuesto_gastos']}';";
	} else {
		if ($cobro->fields['porcentaje_impuesto_gastos'] == 0 && (Conf::GetConf($sesion, 'ValorImpuestoGastos'))) {
			echo "var porcentaje_impuesto_gastos = '" . Conf::GetConf($sesion, 'ValorImpuestoGastos') . "';";
		}
	}

	$numeros_serie = $DocumentoLegalNumero->UltimosNumerosSerie($id_documento_legal);
	$series = array();
	foreach ($numeros_serie as $numero_serie) {
		$series[$numero_serie['estudio']][$numero_serie['serie']] = $numero_serie['numero'];
	}
	echo 'var estudio_series = ' . json_encode($series) . ';';
	?>

// funcion ajax para asignar valores a los campos del cliente en agregar factura
	function CargarDatosCliente(sin_contrato) {
		<?php if (Conf::GetConf($sesion, 'CodigoSecundario')) { ?>
			var id_origen = 'codigo_cliente_secundario';
		<?php } else { ?>
			var id_origen = 'codigo_cliente';
		<?php } ?>
		var accion = 'cargar_datos_contrato';
		var id_contrato = "<?php echo $id_contrato; ?>";
		var select_origen = document.getElementById(id_origen);
		var rut = document.getElementById('RUT_cliente');
		var cliente = document.getElementById('cliente');
		var direccion_cliente = document.getElementById('direccion_cliente');
		var comuna_cliente = document.getElementById('comuna_cliente');
		var ciudad_cliente = document.getElementById('ciudad_cliente');

		<?php if (Conf::GetConf($sesion, 'RegionCliente')) { ?>
			var factura_region = document.getElementById('factura_region');
		<?php } ?>

		var giro_cliente = document.getElementById('giro_cliente');
		var factura_codigopostal = document.getElementById('factura_codigopostal');
		var dte_id_pais = document.getElementById('dte_id_pais');

		<?php if (Conf::GetConf($sesion, 'NuevoModuloFactura')) { ?>
			var descripcion_honorarios_legales = document.getElementById('descripcion_honorarios_legales');
			var monto_honorarios_legales = document.getElementById('monto_honorarios_legales');
			var monto_iva_honorarios_legales = document.getElementById('monto_iva_honorarios_legales');
			var descripcion_gastos_con_iva = document.getElementById('descripcion_gastos_con_iva');
			var monto_gastos_con_iva = document.getElementById('monto_gastos_con_iva');
			var monto_iva_gastos_con_iva = document.getElementById('monto_iva_gastos_con_iva');
			<?php	if (Conf::GetConf($sesion, 'UsarGastosConSinImpuesto') == '1') { ?>
			var descripcion_gastos_sin_iva = document.getElementById('descripcion_gastos_sin_iva');
			var monto_gastos_sin_iva = document.getElementById('monto_gastos_sin_iva');
			<?php
			}
		} else { ?>
			var descripcion = document.getElementById('descripcion');
		<?php } ?>
		var http = getXMLHTTP();

		var url = root_dir + '/app/interfaces/ajax.php?accion=' + accion + '&codigo_cliente=' + select_origen.value;
		if (!sin_contrato) {
			url += '&id_contrato=' + id_contrato;
		}

		http.open('get', url, true);
		http.onreadystatechange = function()
		{
			if (http.readyState == 4)
			{
				var response = http.responseText;

				if (response.indexOf('|') != -1)
				{
					response = response.split('\\n');
					response = response[0];
					var campos = response.split('~');
					if (response.indexOf('VACIO') != -1)
					{
						//dejamos los campos en blanco.
						rut.value = '';
						direccion_cliente.value = '';
						cliente.value = '';
						alert('No existen <?php echo __('cobros'); ?> para este cliente.');
					}
					else
					{
						//select_destino.length = 1;
						for (i = 0; i < campos.length; i++)
						{
							valores = campos[i].split('|');
							var option = new Option();
							option.value = valores[0];
							option.text = valores[1];

							// Cliente
							if (valores[0] != '') {
								cliente.value = valores[0];
							} else {
								cliente.value = '';
							}
							// Direcci�n
							if (valores[1] != '') {
								direccion_cliente.value = valores[1];
							} else {
								direccion_cliente.value = '';
							}
							// Rut
							if (valores[2] != '') {
								rut.value = valores[2];
							} else {
								rut.value = '';
							}
							// Comuna
							if (valores[3] != '') {
								comuna_cliente.value = valores[3];
							} else {
								comuna_cliente.value = '';
							}
							// Ciudad
							if (valores[4] != '') {
								ciudad_cliente.value = valores[4];
							} else {
								ciudad_cliente.value = '';
							}

							//Estado
							<?php if (Conf::GetConf($sesion, 'RegionCliente')) { ?>
								if(valores[5] != ''){
									factura_region.value = valores[5]
								} else{
									factura_region.value = '';
								}
							<?php } ?>

							// Giro
							if (valores[6] != '') {
								giro_cliente.value = valores[6];
							} else {
								giro_cliente.value = '';
							}

							// Codigo Postal
							if (valores[7] != '') {
								factura_codigopostal.value = valores[7];
							} else {
								factura_codigopostal.value = '';
							}

							// Pa�s
							if (valores[8] != '') {
								if (dte_id_pais) {
									dte_id_pais.value = valores[8];
								}
							} else {
								if (dte_id_pais) {
									dte_id_pais.value = '';
								}
							}
						}
					}
				}
				else
				{
					if (response.indexOf('head') != -1)
					{
						alert('Sesi�n Caducada');
						top.location.href = '".Conf::Host()."';
					}
					else
						alert(response);
				}
			}
			cargando = false;
		};
		http.send(null);
	}

	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	function MontoValido(id_campo)
	{
		var monto = document.getElementById(id_campo).value.replace('\,', '.');
		var arr_monto = monto.split('\.');
		var monto = arr_monto[0];
		for ($i = 1; $i < arr_monto.length - 1; $i++)
			monto += arr_monto[$i];
		if (arr_monto.length > 1)
			monto += '.' + arr_monto[arr_monto.length - 1];

		document.getElementById(id_campo).value = monto;
	}

	function MostrarTipoCambioPago()
	{
		$('TipoCambioFactura').show();
	}
	function CancelarDocumentoMonedaPago()
	{
		$('TipoCambioFactura').hide();
	}

	function BuscarFacturas()
	{
		document.forms.item(submit);
	}

	function Letra()
	{
		$('letra_inicial').show();
	}

	function mostrarAccionesEstado(form)
	{
		var id_estado = form.id_estado.value;
		$('letra_inicial').hide();
		if (id_estado == '4')
		{
			$('letra_inicial').show();
		}
		else if (id_estado == '5')
		{
			//Cambiar(form,'anular');
		}
	}

	function CambioCliente()
	{
		//$('id_cobro').value = 'nulo';
		CargarDatosCliente();
	}

	function Cambiar(form, opc)
	{
		form.opcion.value = 'guardar';
		document.getElementById('id_estado').value = 1;
		//form.submit();
		Validar(form);
	}
	var saltar_validacion_saldo = 0;
	var mostrar_alert_saldo = 0;
	function ValidaSaldoPendienteCobro(form)
	{
		var http = getXMLHTTP();
		var url = 'ajax.php?accion=saldo_cobro_factura&id=' + $('id_cobro').value;
		var honorarios = form.monto_neto.value;
		var gastos_con_impuestos = form.monto_gastos_con_iva.value;
		var gastos_sin_impuestos = 0;
		var tipo_doc_legal = form.id_documento_legal.value;
		loading("Actualizando campo");
		http.open('get', url, false);
		http.onreadystatechange = function()
		{
			if (http.readyState == 4)
			{
				var response = http.responseText;
				if (response == 'primera_factura')
				{
					saltar_validacion_saldo = 1;
				}
				saldos = response.split('//');
				jQuery('#honorario_disp').val(jQuery('#honorario_total').parseNumber({format: "###.000", locale: "us"}) + jQuery.parseNumber(saldos[0], {format: "###.000", locale: "us"}));
				jQuery('#gastos_con_impuestos_disp').val(jQuery('#gastos_con_impuestos_total').parseNumber({format: "###.000", locale: "us"}) + jQuery.parseNumber(saldos[1], {format: "###.000", locale: "us"}));
				jQuery('#gastos_sin_impuestos_disp').val(jQuery('#gastos_sin_impuestos_total').parseNumber({format: "###.000", locale: "us"}) + jQuery.parseNumber(saldos[1], {format: "###.000", locale: "us"}));
			}
		};
		http.send(null);
	}

	function Validar(form)
	{

		<?php
		UtilesApp::GetConfJS($sesion, 'UsarGastosConSinImpuesto');
		UtilesApp::GetConfJS($sesion, 'TipoSelectCliente');
		UtilesApp::GetConfJS($sesion, 'TipoDocumentoIdentidadFacturacion');
		UtilesApp::GetConfJS($sesion, 'TipoSelectCliente');
		UtilesApp::GetConfJS($sesion, 'CodigoSecundario');
		UtilesApp::GetConfJS($sesion, 'NuevoModuloFactura');
		?>
		var msgerror = '';
		if (TipoDocumentoIdentidadFacturacion != 0) {
			if (!Validar_Rut())
				return false;
		}
		if (TipoSelectCliente == 'autocompletador') {
			if (form.glosa_cliente.value == "")
			{
				alert('<?php echo __('Debe ingresar un cliente') ?>');
				form.glosa_cliente.focus();
				return false;
			}
		} else if (CodigoSecundario != 0) {
			if (form.codigo_cliente_secundario.value == "")
			{
				alert('<?php echo __('Debe ingresar un cliente') ?>');
				form.codigo_cliente_secundario.focus();
				return false;
			}
		} else {
			if (form.codigo_cliente.value == "")
			{
				alert('<?php echo __('Debe ingresar un cliente') ?>');
				form.codigo_cliente.focus();
				return false;
			}
		}

		if (form.cliente.value == "")
		{
			alert("<?php echo __('Debe ingresar la razon social del cliente.') ?>");
			form.cliente.focus();
			return false;
		}

		if (NuevoModuloFactura == 1) {

			if (form.monto_honorarios_legales.value == "")
			{
				alert('<?php echo __('Debe ingresar un monto para los honorarios') ?>');
				form.monto_honorarios_legales.focus();
				return false;
			}
			if (!isNumber(form.monto_honorarios_legales.value))
			{
				alert('<?php echo __('Debe ingresar un monto v�lido para los honorarios') ?>');
				form.monto_honorarios_legales.focus();
				return false;
			}
			if (form.monto_iva_honorarios_legales.value == "")
			{
				alert('<?php echo __('Debe ingresar un monto IVA para los honorarios') ?>');
				form.monto_iva_honorarios_legales.focus();
				return false;
			}
			if (!isNumber(form.monto_iva_honorarios_legales.value))
			{
				alert('<?php echo __('Debe ingresar un monto IVA v�lido para los honorarios.') ?>');
				form.monto_iva_honorarios_legales.focus();
				return false;
			}

			if (form.monto_gastos_con_iva.value == "")
			{
				alert('<?php echo __('Debe ingresar un monto para los gastos c/ IVA') ?>');
				form.monto_gastos_con_iva.focus();
				return false;
			}
			if (!isNumber(form.monto_gastos_con_iva.value))
			{
				alert('<?php echo __('Debe ingresar un monto v�lido para los gastos c/ IVA') ?>');
				form.monto_gastos_con_iva.focus();
				return false;
			}
			if (form.monto_iva_gastos_con_iva.value == "")
			{
				alert('<?php echo __('Debe ingresar un monto iva para los gastos c/ IVA') ?>');
				form.monto_iva_gastos_con_iva.focus();
				return false;
			}
			if (!isNumber(form.monto_iva_gastos_con_iva.value))
			{
				alert('<?php echo __('Debe ingresar un monto iva v�lido para los gastos c/ IVA') ?>');
				form.monto_iva_gastos_con_iva.focus();
				return false;
			}


			var http = getXMLHTTP();
			http.open('get', 'ajax.php?accion=obtener_num_pagos&id_factura=' + form.id_factura.value, false);  //debe ser syncrono para que devuelva el valor antes de continuar
			http.send(null);
			num_pagos = http.responseText;
			opcion_seleccionada = form.id_estado.options[form.id_estado.selectedIndex].text;
			id_opcion_seleccionada = form.id_estado.options[form.id_estado.selectedIndex].value;
			id_opcion_original = <?php echo $factura->fields['id_estado'] ? $factura->fields['id_estado'] : '1' ?>;

			if (num_pagos > 0 && (opcion_seleccionada.toLowerCase() == "anulado" || opcion_seleccionada.toLowerCase() == "anulada") && id_opcion_seleccionada != id_opcion_original) {
				alert('<?php echo __('La factura no puede anularse ya que posee pagos asociados.'); ?>');
				form.id_estado.value = id_opcion_original;
				return false;
			}

			<?php if (!$factura->loaded() && ($id_documento_legal != 2)) { ?>
			ValidaSaldoPendienteCobro(form);

			jQuery('#monto_gastos_con_iva, #gastos_con_impuestos_disp, #monto_honorarios_legales, #honorario_disp,#monto_gastos_sin_iva,#gastos_sin_impuestos_disp').formatNumber({format: "0.000", locale: "us"});
			var format_number = {format: "0.000", locale: "us"};
			var monto_gastos_sin_iva_validacion = jQuery('#monto_gastos_sin_iva').parseNumber(format_number);
			var gastos_sin_impuestos_disp_validacion = jQuery('#gastos_sin_impuestos_disp').parseNumber(format_number);

			var monto_honorarios_legales_value = jQuery.parseNumber(form.monto_honorarios_legales.value, format_number);
			var monto_gastos_con_iva_value = jQuery.parseNumber(form.monto_gastos_con_iva.value, format_number);
			var honorario_disp_value = jQuery.parseNumber(form.honorario_disp.value, format_number);
			var gastos_con_impuestos_disp_value = jQuery.parseNumber(form.gastos_con_impuestos_disp.value, format_number);

			if ((form.id_documento_legal.value != 2) && (saltar_validacion_saldo == 0) && ( (monto_honorarios_legales_value + monto_gastos_con_iva_value + monto_gastos_sin_iva_validacion) > (honorario_disp_value + gastos_con_impuestos_disp_value + gastos_sin_impuestos_disp_validacion))) {

				if (!confirm('<?php echo __("Los montos ingresados superan el saldo a facturar") ?>')) {
					if (UsarGastosConSinImpuesto == '1') {
						if (form.monto_honorarios_legales.value > form.honorario_disp.value) {
							form.monto_honorarios_legales.focus();
						}
						else if (form.monto_gastos_con_iva.value > form.gastos_con_impuestos_disp.value) {
							form.monto_gastos_con_iva.focus();
						}
						else if (form.monto_gastos_sin_iva.value > form.gastos_sin_impuestos_disp.value) {
							form.monto_gastos_sin_iva.focus();
						}

					} else {

						if (form.monto_honorarios_legales.value > form.honorario_disp.value) {
							form.monto_honorarios_legales.focus();
						}
						else if (form.monto_gastos_con_iva.value > form.gastos_con_impuestos_disp.value) {
							form.monto_gastos_con_iva.focus();
						}

					}

					return false;
				}
			}

		<?php } ?>

			if (UsarGastosConSinImpuesto == '1') {

				if (form.monto_gastos_sin_iva.value == "")
				{
					alert('<?php echo __('Debe ingresar un monto para los gastos s/ IVA') ?>');
					form.monto_gastos_sin_iva.focus();
					return false;
				}
				if (!isNumber(form.monto_gastos_sin_iva.value))
				{
					alert('<?php echo __('Debe ingresar un monto v�lido para los gastos s/ IVA') ?>');
					form.monto_gastos_sin_iva.focus();
					return false;
				}
				if (form.descripcion_gastos_sin_iva.value == "" && form.descripcion_honorarios_legales.value == "" && form.descripcion_gastos_con_iva.value == "")
				{
					alert('<?php echo __('Debe ingresar una descripci�n para los honorarios y/o  gastos') ?>');
					form.descripcion_gastos_con_iva.focus();
					return false;
				}
			}

		} else {

			if (form.descripcion.value == "") {
				alert('<?php echo __('Debe ingresar una descripci�n') ?>');
				form.descripcion.focus();
				return false;
			}
		}


		if (form.id_factura_padre && form.id_factura_padre.value == "") {
			alert('<?php echo __('Este documento debe estar asociado a un documento tributario') ?>');
			form.id_factura_padre.focus();
			return false;
		}

		<?php
		if (!$factura->loaded() && $id_cobro && $id_documento_legal != 2) {
			$saldo = $factura->SaldoAdelantosDisponibles($codigo_cliente, $id_contrato, $subtotal_honorarios, $subtotal_gastos, $cobro->fields['opc_moneda_total']);
			if ($saldo) {
				?>
				if (confirm("<?php echo __('Existen adelantos por ') . $saldo . __(' asociados a esta liquidaci�n. �Desea utilizarlos para saldar esta ') . $tipo_documento_legal . '?' ?>")) {

					$('usar_adelantos').value = '1';
				}
			<?php }
		}
		?>

		form.opcion.value = 'guardar';

		if (NuevoModuloFactura == 1) {
			form.iva_hidden.value = form.iva.value;
		}

		// Debe ser syncrono para que devuelva el valor antes de continuar
		http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=obtener_num_pagos&id_factura=' + jQuery('#id_factura_padre').attr('value'), false);
		http.send(null);
		num_pagos = http.responseText;

		if (num_pagos > 0) {
			var mensaje = 'Estimado usuario, est� tratando de asociar una nota de cr�dito a una factura que contiene pagos.\n\n�Desea continuar?';
			if (!confirm(mensaje)) {
				return false;
			}
		}

		form.submit();
		return true;
	}

	function Cerrar()
	{
		window.close();
	}

	function desgloseMontosFactura(form) {


		var monto_impuesto = 0;
		var monto_impuesto_gasto = 0;
		var monto_honorario = 0;
		var monto_gasto_con_impuesto = 0;
		var monto_gasto_sin_impuesto = 0;
		var monto_neto_suma = 0;
		var decimales = <?php echo intval($cifras_decimales_opc_moneda_total); ?>;

		monto_impuesto = form.monto_honorarios_legales.value * (porcentaje_impuesto / 100);
		monto_impuesto_gasto = form.monto_gastos_con_iva.value * (porcentaje_impuesto_gastos / 100);
		monto_impuesto_suma = parseFloat(monto_impuesto) + parseFloat(monto_impuesto_gasto);
		<?php
		if (Conf::GetConf($sesion, 'UsarGastosConSinImpuesto') == '1') {
		?>
			monto_gasto_sin_impuesto = form.monto_gastos_sin_iva.value;
		<?php } ?>

		monto_neto_suma = parseFloat(form.monto_honorarios_legales.value) +
						parseFloat(form.monto_gastos_con_iva.value)
						+ parseFloat(monto_gasto_sin_impuesto);

		form.monto_neto.value = monto_neto_suma;
		jQuery('#monto_iva_honorarios_legales').val(jQuery.formatNumber(monto_impuesto + 0.000001, {format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"}));
		jQuery('#monto_iva_gastos_con_iva').val(jQuery.formatNumber(monto_impuesto_gasto + 0.000001, {format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"}));
		jQuery('#iva').val(jQuery('#monto_iva_honorarios_legales').parseNumber() + jQuery('#monto_iva_gastos_con_iva').parseNumber());
		var total = Number($('monto_neto').value.replace(',', '.')) + Number($('iva').value.replace(',', '.'));
		$('total').value = total.toFixed(decimales);


		if (cantidad_decimales != -1) {

			jQuery('.aproximable').each(function() {
				jQuery(this).parseNumber({format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"});
				jQuery(this).formatNumber({format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"});
			});

		}

	}

	function ActualizarDocumentoMonedaPago()
	{
		ids_monedas = $('ids_monedas_factura').value;
		arreglo_ids = ids_monedas.split(',');
		$('tipo_cambios_factura').value = "";
		for (var i = 0; i < arreglo_ids.length - 1; i++)
			$('tipo_cambios_factura').value += $('factura_moneda_' + arreglo_ids[i]).value + ",";
		i = arreglo_ids.length - 1;
		$('tipo_cambios_factura').value += $('factura_moneda_' + arreglo_ids[i]).value;
		//alert( $('id_factura').value );
		if ($('id_factura').value != '')
		{
			var tc = new Array();
			for (var i = 0; i < arreglo_ids.length; i++)
				tc[i] = $('factura_moneda_' + arreglo_ids[i]).value;
			$('contenedor_tipo_load').innerHTML =
							"<table width=510px><tr><td align=center><br><br><img src='<?php echo Conf::ImgDir() ?>/ajax_loader.gif'/><br><br></td></tr></table>";
			var http = getXMLHTTP();
			var url = root_dir + '/app/interfaces/ajax.php?accion=actualizar_factura_moneda&id_factura=<?php echo $factura->fields['id_factura'] ?>&ids_monedas=' + ids_monedas + '&tcs=' + tc.join(',');
			http.open('get', url);
			http.onreadystatechange = function()
			{
				if (http.readyState == 4)
				{
					var response = http.responseText;
					alert(response);
					if (response == 'EXITO')
					{
						$('contenedor_tipo_load').innerHTML = '';
					}
				}
			}
			http.send(null);
		}
		CancelarDocumentoMonedaPago();
	}

	/*Validador de Rut*/
	function Validar_Rut()
	{
		<?php if (!Conf::GetConf($sesion, 'TipoDocumentoIdentidadFacturacion')) : ?>
				return true;
		<?php else: ?>
				var tipo = $('tipo_documento_identidad');
				if (tipo.value != 5) {
					return true;
				}
		<?php endif; ?>

		var o = $('RUT_cliente');
		var tmpstr = "";
		var intlargo = o.value

		if (intlargo.length > 0) {
			crut = o.value
			largo = crut.length;

			if (largo < 2) {
				alert('<?php echo __("Rut inv�lido") ?>');
				o.focus();
				return false;
			}

			for (i = 0; i < crut.length; i++)
				if (crut.charAt(i) != ' ' && crut.charAt(i) != '.' && crut.charAt(i) != '-') {
					tmpstr = tmpstr + crut.charAt(i);
				}

			rut = tmpstr;
			crut = tmpstr;
			largo = crut.length;

			if (largo > 2) {
				rut = crut.substring(0, largo - 1);
			} else {
				rut = crut.charAt(0);
			}

			dv = crut.charAt(largo - 1);

			if (rut == null || dv == null) {
				alert('<?php echo __("Rut inv�lido") ?>');
				o.focus();
				return false;
			}

			var dvr = '0';
			suma = 0;
			mul = 2;

			for (i = rut.length - 1; i >= 0; i--)
			{
				suma = suma + rut.charAt(i) * mul;
				if (mul == 7)
					mul = 2;
				else
					mul++;
			}

			res = suma % 11;

			if (res == 1) {
				dvr = 'k';
			} else if (res == 0) {
				dvr = '0';
			} else {
				dvi = 11 - res;
				dvr = dvi + "";
			}

			if (dvr != dv.toLowerCase()) {
				alert('<?php echo __("El Rut Ingresado es Invalido") ?>');
				o.focus();
				return false;
			}
			return true;
		}

		alert('<?php echo __("Rut inv�lido") ?>');
		o.focus();
		return false;

	}

	function ObtenerPagos(id_factura)
	{
		/* por algun motivo no me lo toma, aunque sea sincrono */
		var http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=obtener_num_pagos&id_factura=' + id_factura, false);
		http.onreadystatechange = revisaEstado;
		http.send(null);

		function revisaEstado()
		{
			if (http.readyState == 4)
			{
				response = http.responseText;
				return response;
			}
		}

		return http.responseText;
	}

	function NumeroDocumentoLegal() {
		var estudio_serie_numero = jQuery(document).data('estudio_serie_numero');

		jQuery.each(estudio_series, function(estudio, series) {
			if (jQuery('#id_estudio').val() == estudio) {
				jQuery.each(series, function(serie, numero) {
					if (jQuery('#serie').val() == serie) {
						if (estudio_serie_numero.estudio != estudio || estudio_serie_numero.serie != serie) {
							jQuery('#numero').val(numero);
						} else {
							jQuery('#numero').val(estudio_serie_numero.numero);
						}
						return false;
					}
				});
				return false;
			}
		});

		return true;
	}

	function cambiarEstudio(id_estudio) {
		if (jQuery('#serie').attr('type') == 'hidden') {
			var estudio_serie_numero = jQuery(document).data('estudio_serie_numero');

			jQuery.each(estudio_series, function(estudio, series) {
				if (jQuery('#id_estudio').attr('value') == estudio) {
					jQuery.each(series, function(serie, numero) {
						if (estudio_serie_numero.estudio != estudio || estudio_serie_numero.serie != serie) {
							jQuery('#numero').attr('value', numero);
						} else {
							jQuery('#numero').attr('value', estudio_serie_numero.numero);
						}
						return false;
					});
				}
			});
		} else {
			var select = jQuery('#serie');
			var options = (select.prop) ? select.prop('options') : select.attr('options');

			jQuery('option', select).remove();

			jQuery.each(estudio_series, function(estudio, series) {
				if (jQuery('#id_estudio').attr('value') == estudio) {
					jQuery.each(series, function(serie, numero) {
						options[options.length] = new Option(serie, serie);
					});
				}
			});

			NumeroDocumentoLegal();
		}

		return true;
	}

	function obtiene_fecha_vencimiento(dias, myDate){
		var offset = (dias * 24 * 60 * 60 * 1000);

		myDate.setTime(myDate.getTime() + offset);

		//Transformar objeto date a fecha
		var dia = myDate.getDate();
		var mes = myDate.getMonth() + 1;
		if(mes < 10){
			mes = '0' + mes;
		}
		if(dia < 10){
			dia = '0' + dia;
		}
		var anio = myDate.getFullYear();

		var fecha_vencimiento_pago = dia + "-" + mes + "-" + anio;

		return fecha_vencimiento_pago;
	}

	<?php
	if (Conf::GetConf($sesion, 'NuevoModuloFactura')) {
		echo "desgloseMontosFactura(document.form_facturas);\n";
		if ($factura->loaded() && $factura->fields['id_estado'] == '4' && $factura->fields['letra'] != '') {
			echo "Letra();\n";
		}
	}
	?>

	jQuery(document).ready(function() {
		jQuery(document).data('estudio_serie_numero', {
			'estudio': jQuery('#id_estudio').attr('value'),
			'serie': jQuery('#serie').attr('value'),
			'numero': jQuery('#numero').attr('value')
		});

		jQuery('#codigo_cliente,#campo_codigo_cliente').change(function() {
			CargarDatosCliente(1);
		});

		jQuery('#fecha').change(function(){
			jQuery('#condicion_pago').trigger('change');
		});

		//Manejo de select de condicion de pago.
		jQuery('#condicion_pago').change(function(){
			var codigo = jQuery(this).val();
			if(codigo == 1 || codigo == 21){

				//jQuery('.fecha_vencimiento_pago').css('visibility', 'visible');
				jQuery('#fecha_vencimiento_pago_input').attr('readonly',false);
				var dias = 1;
				var myDate = new Date();
				var fecha_vencimiento_pago = obtiene_fecha_vencimiento(dias, myDate);

				jQuery('#fecha_vencimiento_pago_input').val(fecha_vencimiento_pago);
			}
			else{

				//jQuery('.fecha_vencimiento_pago').css('visibility', 'hidden');
				jQuery('#fecha_vencimiento_pago_input').attr('readonly',true);
				var texto = jQuery(this).find(":selected").text();
				var splitted_text = texto.split(' ');
				var dias = splitted_text[2];
				dias++;
				var fecha_definida = jQuery('#fecha').val();
				var fecha_definida_split = fecha_definida.split('-');
				var myDate = new Date(fecha_definida_split[2], fecha_definida_split[1] - 1, fecha_definida_split[0]);

				var fecha_vencimiento_pago = obtiene_fecha_vencimiento(dias, myDate);

				jQuery('#fecha_vencimiento_pago_input').val(fecha_vencimiento_pago);
			}
		});

		jQuery('#condicion_pago').trigger('change');

		if (cantidad_decimales != -1) {
			jQuery('.aproximable').each(function() {
				jQuery(this).val = jQuery(this).parseNumber({format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"}) + 0.0000001;
				jQuery(this).formatNumber({format: "0.<?php echo str_pad('', $cifras_decimales_opc_moneda_total, "0"); ?>", locale: "us"});
			});

			jQuery('.aproximable').typeWatch({
				callback: function() {
					desgloseMontosFactura(jQuery('#form_facturas').get(0));
				},
				wait: 700,
				highlight: false,
				captureLength: 1
			});
		}

		jQuery('#RUT_cliente').blur(function() {
			<?php if (Conf::GetConf($sesion, 'TipoDocumentoIdentidadFacturacion')) { ?>
				Validar_Rut();
			<?php } ?>
		});

		<?php if (($codigo_cliente || $codigo_cliente_secundario) && empty($id_factura)) { ?>
			CargarDatosCliente();
		<?php } ?>

		<?php echo ($requiere_refrescar) ? $requiere_refrescar : ''; ?>

	});

	<?php ($Slim = Slim::getInstance('default', true)) ? $Slim->applyHook('hook_factura_javascript_after') : false; ?>
</script>
<?php $pagina->PrintBottom($popup);
