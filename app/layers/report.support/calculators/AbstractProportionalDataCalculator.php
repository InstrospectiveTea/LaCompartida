<?php

/**
 * Corresponde a la clase base para los calculadores que
 * requieren ser devueltos de acuerdo a una proporcionalidad
 */
abstract class AbstractProportionalDataCalculator extends AbstractCurrencyDataCalculator {

	const PROPORTIONALITY_CLIENT = 'cliente';
	const PROPORTIONALITY_STANDARD = 'estandar';

	private $proportionality;

	/**
	 * Constructor
	 * @param Sesion $Session         La sesión para el acceso a datos
	 * @param [type] $filtersFields   Los campos/keys por los que se debe filtrar y sus valores
	 * @param [type] $grouperFields   Los campos/keys por los que se debe agrupar
	 * @param [type] $currencyId      La moneda en la que se devolverán los valores
	 * @param [type] $proportionality La proporcionalidad en la que se deben distribuir los valores
	 */
	public function __construct(Sesion $Session, $filtersFields, $grouperFields, $currencyId, $proportionality) {
		parent::__construct($Session, $filtersFields, $grouperFields, $currencyId);
		$this->proportionality = $proportionality;
	}

	/**
	 * Obtiene la proporcionalidad elegida
	 * @return [type] [description]
	 */
	public function getProportionality() {
		return $this->proportionality;
	}

	/**
	 * Devuelve el par tabla.campo de la tarifa del trabajo
	 * en base a la proporcionalidad elegida
	 * @return string campo de donde se obtendrá la tarifa
	 */
	function getWorksFeeField() {
		$proportionality = $this->getProportionality();
		if ($proportionality == PROPORTIONALITY_STANDARD)  {
			return 'trabajo.tarifa_hh_estandar';
		} else {
			return 'trabajo.tarifa_hh';
		}
	}

	/**
	 * Devuelve el campo del cobro desde donde se obtiene el monto total
	 * producido de trabajos en base a la proporcionalidad elegida
	 * @return string campo de donde se obtendrá el monto
	 */
	function getWorksProportionalityAmountField() {
		$proportionality = $this->getProportionality();
		if ($proportionality == PROPORTIONALITY_STANDARD)  {
			return 'monto_thh_estandar';
		} else {
			return 'monto_thh';
		}
	}

	/**
	 * Devuelve el par tabla.campo de la tarifa del trámite
	 * en base a la proporcionalidad elegida
	 * @return string campo de donde se obtendrá la tarifa
	 */
	function getErrandsFeeField() {
		$proportionality = $this->getProportionality();
		if ($proportionality == PROPORTIONALITY_STANDARD)  {
			return 'tramite.tarifa_tramite_estandar';
		} else {
			return 'tramite.tarifa_tramite';
		}
	}

	/**
	 * Devuelve el campo del cobro desde donde se obtiene el monto total
	 * producido de trámites en base a la proporcionalidad elegida.
	 * Actualmente Trámites no posee un campo total para tarifa estándar
	 * @return string campo de donde se obtendrá el monto
	 */
	function getErrandsProportionalityAmountField() {
		$proportionality = $this->getProportionality();
		if ($proportionality == PROPORTIONALITY_STANDARD)  {
			return 'monto_tramites';
		} else {
			return 'monto_tramites';
		}
	}
}
