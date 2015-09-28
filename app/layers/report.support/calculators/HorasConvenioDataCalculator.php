<?php
/**
 * HorasConvenioDataCalculator
 * key: horas_convenio
 * Description: Horas cobrables de profesionales, en formas de cobro FLAT FEE y RETAINER
 *
 * M�s info:
 * https://github.com/LemontechSA/ttb/wiki/Reporte-Calculador:-Horas-Convenio
 *
 */
class HorasConvenioDataCalculator extends AbstractDataCalculator {

	/**
	 * Obtiene la query de trabajos correspondiente a Horas Convenio
	 * Se obtiene desde trabajo.duracion_cobrada filtrando por forma de cobro
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportWorkQuery(Criteria $Criteria) {
		$horas_convenio = "SUM(TIME_TO_SEC(trabajo.duracion_cobrada)) / 3600";

		$Criteria
			->add_select($horas_convenio, 'horas_convenio');

		$or_wheres = array();

		$or_wheres[] = CriteriaRestriction::and_clause(
			CriteriaRestriction::not_in('cobro.estado', array('CREADO', 'EN REVISION')),
			CriteriaRestriction::in('cobro.forma_cobro', array('FLAT FEE', 'RETAINER'))
		);

		$or_wheres[] = CriteriaRestriction::and_clause(
			CriteriaRestriction::or_clause(
				CriteriaRestriction::is_null('cobro.estado'),
				CriteriaRestriction::in('cobro.estado', array('CREADO', 'EN REVISION'))
			),
			CriteriaRestriction::in('contrato.forma_cobro', array('FLAT FEE', 'RETAINER'))
		);

		$Criteria->add_restriction(CriteriaRestriction::or_clause($or_wheres));

		$Criteria
			->add_restriction(CriteriaRestriction::equals('trabajo.cobrable', 1));
	}


	/**
	 * Obtiene la query de tr�tmies correspondiente a Horas Convenio
	 * El valor es Cero para todo tr�mite
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportErrandQuery(&$Criteria) {
		$Criteria = null;
	}

	/**
	 * Obtiene la query de cobros sin trabajos ni tr�mites correspondiente a Horas Convenio
	 *
	 * @param  Criteria $Criteria Query a la que se agregar� el c�lculo
	 * @return void
	 */
	function getReportChargeQuery(&$Criteria) {
		$Criteria = null;
	}

}
