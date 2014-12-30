<?php

class ProcessLockController extends AbstractController {

	public function __construct() {
		parent::__construct();
		$this->LoadBusiness('ProcessLocking');
	}

	public function is_locked($process) {
		$this->renderJSON(array('locked' => $this->ProcessLockingBusiness->isLocked($process)));
	}

	public function was_notified($process) {
		$this->renderJSON($this->ProcessLockingBusiness->wasNotified($process));
	}

	public function get_locker($process) {
		$this->renderJSON($this->ProcessLockingBusiness->getLocker($process)->fields);
	}

	public function set_notified($id) {
		$this->renderJSON(array('notified' => $this->ProcessLockingBusiness->setNotified($id)));
	}

	/**
	 * Ejecuta un shell con el mismo nombre del proceso
	 * @param type $process es el nombre de clase de la Shell que ser� ejecutada.
	 */
	public function exec($process) {
		if ($this->request['method'] != 'post') {
			$this->renderJSON(array('error' => 'No se permite este metodo de ejecuci�n.'));
		}
		if ($this->ProcessLockingBusiness->isLocked($process)) {
			$this->renderJSON(array('error' => 'El proceso indicado se encuentra bloqueado.', 'locker' => $this->ProcessLockingBusiness->getLocker($process)));
		}
		$shell = \TTB\Utiles::underscoreize(Cobro::PROCESS_NAME);
		$data = $this->data;
		$data['user_id'] = $this->Session->usuario->fields['id_usuario'];
		$data['form']['cobrosencero'] = (empty($this->data['cobrosencero'])? 0 : 1);
		$shell_cmd = sprintf("%s/console/console %s --domain=%s --subdir=%s --data='%s' > /dev/null &", ROOT_PATH, $shell, SUBDOMAIN, ROOTDIR, json_encode($data));
		exec($shell_cmd);
		$this->autoRender = false;
		$this->renderJSON(array('executing' => true));
	}

}