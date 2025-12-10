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

class PdPopUpsProAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        $this->runAjax();
    }

    public function runAjax()
    {
        $module = new PdPopUpsPro();
        if (Tools::getValue('secure_key') == $module->secure_key) {
            $action = Tools::getValue('action');
            if ($action == 'subscribeEmail') {
                $context = Context::getContext();
                $customer_email = Tools::getValue('email');
                $id_pdpopupspro = Tools::getValue('id_pdpopupspro');
                $register_status = $module->isNewsletterRegistered($customer_email);
                if ($register_status == $module::GUEST_REGISTERED) {
                    die('1');
                }

                if ($register_status == $module::CUSTOMER_REGISTERED) {
                    die('2');
                }

                if ($register_status == $module::GUEST_NOT_REGISTERED) {
                    $module->registerGuest($customer_email);
                    $token = $module->getToken($customer_email, $register_status);
                    if ($module->skip_validation == false) {
                        if ($module->sendVerificationEmail($customer_email, $token, $id_pdpopupspro)) {
                            die('3');
                        }
                    } else {
                        $module->confirmEmail($token, $id_pdpopupspro);
                        die('4');
                    }
                }

                if ($register_status == $module::CUSTOMER_NOT_REGISTERED) {
                    $module->registerUser($customer_email);
                    $popup = new PopUpModel($id_pdpopupspro);
                    if ($popup->voucher_enabled) {
                        $module->sendVoucher($customer_email, $id_pdpopupspro);
                    }
                    die('5');
                }
                die('0');
            }
        }
    }
}
