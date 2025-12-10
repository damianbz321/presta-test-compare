<?php
class OrderController extends OrderControllerCore
{
    /*
    * module: eicaptcha
    * date: 2021-10-13 21:35:23
    * version: 2.3.1
    */
    public function postProcess()
    {
        if (
            Tools::isSubmit('submitCreate')
            && Module::isInstalled('eicaptcha')
            && Module::isEnabled('eicaptcha')
            && false === Module::getInstanceByName('eicaptcha')->hookActionContactFormSubmitCaptcha([])
            && !empty($this->errors)
        ) {
            unset($_POST['submitCreate']);
        }
        parent::postProcess();
    }
}
