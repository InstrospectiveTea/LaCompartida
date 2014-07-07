<?php

class FacturacionElectronicaCl extends FacturacionElectronica {

	public static function ValidarFactura() {
		global $pagina, $RUT_cliente, $direccion_cliente, $ciudad_cliente, $comuna_cliente, $giro_cliente;
		if (empty($RUT_cliente)) {
			$pagina->AddError(__('Debe ingresar RUT del cliente.'));
		}
		if (empty($direccion_cliente)) {
			$pagina->AddError(__('Debe ingresar Direcci�n del cliente.'));
		}
		if (empty($comuna_cliente)) {
			$pagina->AddError(__('Debe ingresar Comuna del cliente.'));
		}
		if (empty($ciudad_cliente)) {
			$pagina->AddError(__('Debe ingresar Ciudad del cliente.'));
		}
		if (empty($giro_cliente)) {
			$pagina->AddError(__('Debe ingresar Giro del cliente.'));
		}
	}

	public static function GeneraFacturaElectronica($hookArg) {
		$Sesion = new Sesion();
		$Factura = $hookArg['Factura'];
		if (!empty($Factura->fields['dte_url_pdf'])) {
			$hookArg['InvoiceURL'] = $Factura->fields['dte_url_pdf'];
		} else {
			$Estudio = new PrmEstudio($Sesion);
			$Estudio->Load($Factura->fields['id_estudio']);
			$rut = $Estudio->GetMetaData('rut');
			$usuario = $Estudio->GetMetadata('facturacion_electronica_cl.usuario');
			$password = $Estudio->GetMetadata('facturacion_electronica_cl.password');
			$WsFacturacionCl = new WsFacturacionCl($rut, $usuario, $password);
			if ($WsFacturacionCl->hasError()) {
				$hookArg['Error'] = array(
					'Code' => $WsFacturacionCl->getErrorCode(),
					'Message' => $WsFacturacionCl->getErrorMessage()
				);
			} else {
				$arrayDocumento = self::FacturaToArray($Sesion, $Factura ,$Estudio);
				$hookArg['ExtraData'] = $arrayDocumento;
				$result = $WsFacturacionCl->emitirFactura($arrayDocumento);
				if (!$WsFacturacionCl->hasError()) {
					try {
						$Factura->Edit('dte_xml', $result['Detalle']['Documento']['xmlDTE']);
						$Factura->Edit('dte_fecha_creacion', date('Y-m-d H:i:s'));
						$file_url = $result['Detalle']['Documento']['urlPDF'];
						$Factura->Edit('dte_url_pdf', $file_url);
						if ($Factura->Write()) {
							$hookArg['InvoiceURL'] = $file_url;
						}
					} catch (Exception $ex) {
						$hookArg['Error'] = array(
							'Code' => 'SaveGeneratedInvoiceError',
							'Message' => print_r($ex, true)
						);
					}
				} else {
					$hookArg['Error'] = array(
						'Code' => 'BuildingInvoiceError',
						'Message' => utf8_decode($WsFacturacionCl->getErrorMessage())
					);
				}
			}
		}
		return $hookArg;
	}

	public static function AnulaFacturaElectronica($hookArg) {
		$Sesion = new Sesion();
		$Factura = $hookArg['Factura'];

		if (!$Factura->FacturaElectronicaCreada()) {
			return $hookArg;
		}

		$Estudio = new PrmEstudio($Sesion);
		$Estudio->Load($Factura->fields['id_estudio']);
		$rut = $Estudio->GetMetaData('rut');
		$usuario = $Estudio->getMetadata('facturacion_electronica_cl.usuario');
		$password = $Estudio->getMetadata('facturacion_electronica_cl.password');
		$WsFacturacionCl = new WsFacturacionCl($rut, $usuario, $password);
		if ($WsFacturacionCl->hasError()) {
			$hookArg['Error'] = array(
				'Code' => $WsFacturacionCl->getErrorCode(),
				'Message' => $WsFacturacionCl->getErrorMessage()
			);
		} else {
			$WsFacturacionCl->anularFactura($Factura->fields['numero']);
			if (!$WsFacturacionCl->hasError()) {
				try {
					$Factura->Edit('dte_fecha_anulacion', date('Y-m-d H:i:s'));
					$Factura->Write();
				} catch (Exception $ex) {
					$hookArg['Error'] = array(
						'Code' => 'SaveCanceledInvoiceError',
						'Message' => print_r($ex, true)
					);
				}
			} else {
				$hookArg['Error'] = array(
					'Code' => 'CancelGeneratedInvoiceError',
					'Message' => $WsFacturacionCl->getErrorMessage()
				);
			}
		}
		return $hookArg;
	}

	/**
	 * Genera array de datos de la factura para enviar a Facturacion.cl
	 * @param Sesion $Sesion
	 * @param Factura $Factura
	 * @return array
	 */
	public static function FacturaToArray(Sesion $Sesion, Factura $Factura, PrmEstudio $Estudio) {
		$subtotal_factura = $Factura->fields['subtotal'] + $Factura->fields['subtotal_gastos'] + $Factura->fields['subtotal_gastos_sin_impuesto'];
		$arrayFactura = array(
			'fecha_emision' => Utiles::sql2date($Factura->fields['fecha'], '%Y-%m-%d'),
			'folio' => $Factura->fields['numero'],
			'monto_neto' => intval($subtotal_factura),
			'tasa_iva' => intval($Factura->fields['porcentaje_impuesto']),
			'monto_iva' => intval($Factura->fields['iva']),
			'monto_total' => intval($Factura->fields['total']),
			'emisor' => array(
				'rut' => $Estudio->GetMetaData('rut'),
				'razon_social' => $Estudio->GetMetaData('razon_social'),
				'giro' => $Estudio->GetMetaData('giro'),
				'codigo_actividad' => $Estudio->GetMetaData('codigo_actividad'),
				'direccion' => $Estudio->GetMetaData('direccion'),
				'comuna' => $Estudio->GetMetaData('comuna'),
				'cuidad' => $Estudio->GetMetaData('cuidad')
			),
			'receptor' => array(
				'rut' => $Factura->fields['RUT_cliente'],
				'razon_social' => UtilesApp::transliteration($Factura->fields['cliente']),
				'giro' => UtilesApp::transliteration($Factura->fields['giro_cliente']),
				'direccion' => UtilesApp::transliteration($Factura->fields['direccion_cliente']),
				'comuna' => UtilesApp::transliteration($Factura->fields['comuna_cliente']),
				'cuidad' => UtilesApp::transliteration($Factura->fields['ciudad_cliente'])
			),
			'detalle' => array()
		);

		if ($Factura->fields['subtotal'] > 0) {
			$arrayFactura['detalle'][] = array(
				'descripcion' => $Factura->fields['descripcion'],
				'cantidad' => 1,
				'precio_unitario' => (int) number_format($Factura->fields['subtotal'], 2, '.', '')
			);
		}

		if ($Factura->fields['subtotal_gastos'] > 0) {
			$arrayFactura['detalle'][] = array(
				'descripcion' => $Factura->fields['descripcion_subtotal_gastos'],
				'cantidad' => 1,
				'precio_unitario' => (int) number_format($Factura->fields['subtotal_gastos'], 2, '.', '')
			);
		}

		if ($Factura->fields['subtotal_gastos_sin_impuesto'] > 0) {
			$arrayFactura['detalle'][] = array(
				'descripcion' => $Factura->fields['descripcion_subtotal_gastos_sin_impuesto'],
				'cantidad' => 1,
				'precio_unitario' => (int) number_format($Factura->fields['subtotal_gastos_sin_impuesto'], 2, '.', '')
			);
		}

		return $arrayFactura;
	}

}