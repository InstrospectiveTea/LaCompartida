<?php

require_once dirname(__FILE__) . '/../conf.php';

class AreaUsuario extends Objeto {

	function AreaUsuario($sesion, $fields = "", $params = "") {
		$this->tabla = 'prm_area_usuario';
		$this->campo_id = 'id';
		$this->sesion = $sesion;
		$this->fields = $fields;
	}

}
