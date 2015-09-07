<?php
/**
 * La rentabilidad base es Valor Cobrado / Valor Trabajado Est�ndar
 *
 * Esta informaci�n se obtiene de: Trabajos y Tr�mites
 *
 * Condiciones para obtener un valor cobrado:
 *  * Que exista un cobro en estado: EMITIDO, FACTURADO, ENVIADO AL CLIENTE,
 *    PAGO PARCIAL o PAGADO
 *  * Que lo que se est� cobrando sea Cobrable
 *
 * M�s info: https://github.com/LemontechSA/ttb/wiki/Reporte-Calculador:-Rentabilidad-Base
 */
class RentabilidadBaseDataCalculator extends AbstractProportionalDataCalculator {

	/**
	 * Obtiene la query de trabajos correspondiente a la rentabilidad base
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportWorkQuery(Criteria $Criteria) {
		$factor = $this->getWorksProportionalFactor();
		$billed_amount = "SUM(
			{$factor}
			*
			(
				(documento.monto_trabajos / (documento.monto_trabajos + documento.monto_tramites))
				*
				documento.subtotal_sin_descuento * cobro_moneda_documento.tipo_cambio
			)
		)
		*
		(1 / cobro_moneda.tipo_cambio)";

		$standard_amount = "
			SUM((TIME_TO_SEC(duracion) / 3600) *
			IF(
				cobro.id_cobro IS NULL OR cobro_moneda_cobro.tipo_cambio IS NULL OR cobro_moneda.tipo_cambio IS NULL,
				trabajo.tarifa_hh_estandar * (moneda_por_cobrar.tipo_cambio / moneda_display.tipo_cambio),
				trabajo.tarifa_hh_estandar * (cobro_moneda_cobro.tipo_cambio / cobro_moneda.tipo_cambio)
			))";

		$Criteria->add_left_join_with(
			array('prm_moneda', 'moneda_por_cobrar'),
			CriteriaRestriction::equals(
				'moneda_por_cobrar.id_moneda',
				'contrato.id_moneda'
			)
		)->add_left_join_with(
			array('prm_moneda', 'moneda_display'),
			CriteriaRestriction::equals(
				'moneda_display.id_moneda',
				$this->currencyId
			)
		);

		$on_usuario_tarifa = CriteriaRestriction::and_clause(
			CriteriaRestriction::equals(
				'usuario_tarifa.id_usuario',
				'trabajo.id_usuario'
			),
			CriteriaRestriction::equals(
				'usuario_tarifa.id_moneda',
				'contrato.id_moneda'
			)
		);

		$Criteria->add_left_join_with('usuario_tarifa', $on_usuario_tarifa);

		$Criteria->add_inner_join_with('tarifa', CriteriaRestriction::and_clause(
			CriteriaRestriction::equals('tarifa.id_tarifa', 'usuario_tarifa.id_tarifa'),
			CriteriaRestriction::equals('tarifa.tarifa_defecto', 1)
		));

		$billed_amount = "IF(
			cobro.estado IN ('EMITIDO','FACTURADO','ENVIADO AL CLIENTE','PAGO PARCIAL','PAGADO'),
			$billed_amount, 0)";

		$Criteria
			->add_select($standard_amount, 'valor_divisor')
			->add_select($billed_amount, 'rentabilidad_base');

		$Criteria
			->add_restriction(CriteriaRestriction::equals('trabajo.cobrable', 1))
			->add_restriction(CriteriaRestriction::in('cobro.estado', array('EMITIDO', 'FACTURADO', 'ENVIADO AL CLIENTE', 'PAGO PARCIAL', 'PAGADO')));
	}

	/**
	 * Obtiene la query de tr�mites correspondiente a la rentabilidad base
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportErrandQuery($Criteria) {
		$factor = $this->getErrandsProportionalFactor();

		$billed_amount =  "SUM(
			{$factor}
			*
			(
				(documento.monto_tramites / (documento.monto_trabajos + documento.monto_tramites))
				*
				documento.subtotal_sin_descuento * cobro_moneda_documento.tipo_cambio
			)
		)
		*
		(1 / cobro_moneda.tipo_cambio)";

		$standard_amount = "
			SUM(
			IF(
				cobro.id_cobro IS NULL OR cobro_moneda_cobro.tipo_cambio IS NULL OR cobro_moneda.tipo_cambio IS NULL,
				tramite.tarifa_tramite_estandar * (moneda_por_cobrar.tipo_cambio / moneda_display.tipo_cambio),
				tramite.tarifa_tramite_estandar * (cobro_moneda_cobro.tipo_cambio / cobro_moneda.tipo_cambio)
			))";

		$Criteria->add_left_join_with(
			array('prm_moneda', 'moneda_por_cobrar'),
			CriteriaRestriction::equals(
				'moneda_por_cobrar.id_moneda',
				'contrato.id_moneda'
			)
		)->add_left_join_with(
			array('prm_moneda', 'moneda_display'),
			CriteriaRestriction::equals(
				'moneda_display.id_moneda',
				$this->currencyId
			)
		);

		$billed_amount = "IF(
			cobro.estado IN ('EMITIDO','FACTURADO','ENVIADO AL CLIENTE','PAGO PARCIAL','PAGADO'),
			$billed_amount, 0)";

		$Criteria
			->add_select($standard_amount, 'valor_divisor')
			->add_select($billed_amount, 'rentabilidad_base');

		$Criteria
			->add_restriction(CriteriaRestriction::equals('tramite.cobrable', 1))
			->add_restriction(CriteriaRestriction::in('cobro.estado', array('EMITIDO', 'FACTURADO', 'ENVIADO AL CLIENTE', 'PAGO PARCIAL', 'PAGADO')));
	}

	/**
	 * Obtiene la query de cobros sin trabajos ni tr�mites
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportChargeQuery($Criteria) {
				$billed_amount = '
			(1 / IFNULL(asuntos_cobro.total_asuntos, 1)) *
			SUM((cobro.monto_subtotal - cobro.descuento)
				* (cobro_moneda_cobro.tipo_cambio / cobro_moneda.tipo_cambio)
			)
		';

		$billed_amount = "IF(
			cobro.estado IN ('EMITIDO','FACTURADO','ENVIADO AL CLIENTE','PAGO PARCIAL','PAGADO'),
			$billed_amount, 0)";

		$Criteria
			->add_select('0', 'valor_divisor')
			->add_select($billed_amount, 'rentabilidad_base');

		$Criteria
			->add_restriction(CriteriaRestriction::in('cobro.estado', array('EMITIDO', 'FACTURADO', 'ENVIADO AL CLIENTE', 'PAGO PARCIAL', 'PAGADO')));

	}

}
