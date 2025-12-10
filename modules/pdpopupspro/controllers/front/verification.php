<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*/

class PdPopupsProVerificationModuleFrontController extends ModuleFrontController
{
    private $message = '';
    public $ssl = true;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->ps_version_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_version_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        parent::__construct();
    }

    public function postProcess()
    {
        $this->message = $this->module->confirmEmail(Tools::getValue('token'), (int)Tools::getValue('id'));
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign('message', $this->message);
        if ($this->ps_version_16) {
            $this->setTemplate('verification_execution.tpl');
        } else {
            $this->setTemplate('module:pdpopupspro/views/templates/front/verification_execution_ps17.tpl');
        }
    }
}
