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

class OrderOpcController extends OrderOpcControllerCore
{
    protected function _getPaymentMethods()
    {
        $cart = Context::getContext()->cart;
        $module = Module::getInstanceByName('pminpostpaczkomaty');
        if ($module->isSelectedCarrierBw($cart->id_carrier) || $module->isSelectedCarrierCod($cart->id_carrier)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'pminpostpaczkomatylist` WHERE id_cart = '.(int)$cart->id;
            $row = Db::getInstance()->getRow($sql);
            if ($row === false) {
                return '<p class="warning">'.Tools::displayError('Proszę wybrać paczkomat. ').'</p>';
            }
        }
        
        return parent::_getPaymentMethods();
    }
}
