<?php

class ChargeController extends AbstractController {
	public $helpers = array('EntitiesListator', array('\TTB\Html', 'Html'), 'Form', 'Paginator');

	public function slidingScaleDetail() {
		$chargeId = $this->data['charge'];
		$languageCode = $this->data['language'];
		$this->loadBusiness('Charging');
		$this->loadBusiness('Coining');
		$this->loadBusiness('Translating');
		$charge = $this->ChargingBusiness->getCharge($chargeId);
		$currency = $this->CoiningBusiness->getCurrency($charge->get('id_moneda'));
		$language = $this->TranslatingBusiness->getLanguageByCode($languageCode);
		$slidingScales = $this->ChargingBusiness->getSlidingScales($chargeId, $languageCode);
		$response['detail'] = $this->ChargingBusiness->getSlidingScalesDetailTable($slidingScales, $currency, $language);
		$this->renderJSON($response);
	}

	public function feeAmountDetailTable() {
		$chargeId = $this->data['charge'] ? $this->data['charge'] : $this->params['charge'];
		
		$this->loadBusiness('Charging');
		$this->loadBusiness('Coining');
		$this->loadBusiness('Translating');

		$charge = $this->ChargingBusiness->getCharge($chargeId);
		$currency = $this->CoiningBusiness->getCurrency($charge->get('opc_moneda_total'));
		$language = $this->TranslatingBusiness->getLanguageByCode('es');
		
		$detail  = $this->ChargingBusiness->getAmountDetailOfFees($charge, $currency);
		$slidingScales = $this->ChargingBusiness->getSlidingScales($chargeId, 'es');

		$this->set('slidingScales', $slidingScales);
		$this->set('feeDetiail', $detail);
		$this->set('currency', $currency);
		$this->set('language', $language);

		$response['detail'] = $this->renderTemplate('Charge/detail_fees');
		$this->renderJSON($response);
		
	}	
}