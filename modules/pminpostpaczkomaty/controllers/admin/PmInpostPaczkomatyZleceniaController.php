<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/
require_once(dirname(__FILE__).'/../../lib/api.php');
require_once(dirname(__FILE__).'/../../lib/shipxapi.php');

class PmInpostPaczkomatyZleceniaController extends ModuleAdminController
{
    public function __construct()
    {
        $this->cat_cache = array();
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;
        $this->bootstrap = true;
        $this->api = InpostApiShipx::getInstance();
        parent::__construct();
    }

    public function initContent()
    {
        $this->assignVariables();
        if ($this->errors != '') {
        }
        $this->content = $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/zlecenia.tpl');
        parent::initContent();
    }

    private function assignVariables()
    {
        $this->context->smarty->assign(
            array(
                'zlecenia' => $this->api->getZlecenia()
            )
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('print_zlecenie')) {
            $this->errors = $this->api->printZlecenie(Tools::getValue('print_zlecenie'));
        }
    }

    public function viewAccess($disable = false)
    {
        return true;
    }
}
