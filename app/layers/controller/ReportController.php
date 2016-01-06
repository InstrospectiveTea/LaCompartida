<?php

class ReportController extends AbstractController {

	function agrupatedWork() {
		$this->loadBusiness('Working');
		$report = $this->WorkingBusiness->agrupatedWorkReport($this->data);
		$report->render();
		exit;
	}

	public function agrupatedWorkFilters() {
		$this->loadBusiness('Coining');

		$this->set('gropued_by_default', 'lawyer');
		$this->set('gropued_by', array('lawyer' => 'Abogado', 'client' => 'Cliente'));
		$this->set('mostrar_valores', array(__('Horas Trabajadas'), __('Horas Facturables Corregidas')));
		$this->set('monedas', $this->CoiningBusiness->currenciesToArray($this->CoiningBusiness->getCurrencies()));

		$moneda_base = $this->CoiningBusiness->getBaseCurrency();
		$this->set('moneda_base', $moneda_base->get($moneda_base->getIdentity()));

		$response['detail'] = $this->renderTemplate('Reports/agrupated_work_filters');

		$this->renderJSON($response);
	}

	public function productionByPeriod() {
		$this->loadBusiness('Working');
		$this->loadBusiness('Coining');
		$this->loadBusiness('Searching');

		$projectArea = $this->SearchingBusiness->getAssociativeArray('ProjectArea', 'id_area_proyecto', 'glosa');
		$projectType = $this->SearchingBusiness->getAssociativeArray('ProjectType', 'id_tipo_proyecto', 'glosa_tipo_proyecto');

		$this->layoutTitle = 'Reporte de Producción por Periodo';
		$this->set('cobrable_estados', array('No', 'Si'));
		$this->set('mostrar_estados', array(__('Horas Trabajadas'), __('Horas Cobradas'), __('Valor Cobrado')));
		$this->set('monedas', $this->CoiningBusiness->currenciesToArray($this->CoiningBusiness->getCurrencies()));
		$this->set('areas', $projectArea);
		$this->set('tipo_asunto', $projectType);
		$moneda_base = $this->CoiningBusiness->getBaseCurrency();
		$this->set('moneda_base', $moneda_base->get($moneda_base->getIdentity()));
		if (!empty($this->data)) {
			$report = $this->WorkingBusiness->productionByPeriodReport($this->data);
			$this->set('report', $report);
			if ($this->data['opc'] == 'Spreadsheet') {
				$report->render();
			}
		}
	}
}
