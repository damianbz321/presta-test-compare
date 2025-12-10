<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

require_once(_PS_MODULE_DIR_ . 'baselinker/classes/BaseLinkerOrder.php');

class Baselinker extends Module {
	public function __construct() {
		$this->name = 'baselinker';
		$this->tab = 'other';
		$this->version = '0.0.31';
		$this->author = 'BaseLinker';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Baselinker', 'baselinker');
		$this->description = $this->l('Rozszerzenia API na potrzeby integracji z BaseLinkerem', 'baselinker');
		$this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować', 'baselinker');
	}

	public function install() {
		parent::install();
		$this->registerHook('addWebserviceResources');
		return true;
	}

	public function uninstall() {
		return parent::uninstall();
	}

	public function hookAddWebserviceResources() {
		return array(
			'bl_order' => array('description' => 'Extended order data for use by BaseLinker', 'class' => 'BaseLinkerOrder'),
		);
	}
}

