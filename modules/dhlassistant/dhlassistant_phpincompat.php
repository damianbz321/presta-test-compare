<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class DhlAssistant extends CarrierModule
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'dhlassistant';
        $this->tab = 'shipping_logistics';
        $this->version = '1.7';
        $this->author = 'DHL Parcel Polska';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->bootstrap = false;

        parent::__construct();

        $this->displayName = 'Asystent DHL dla Presta Shop';
        $this->description = 'Moduł wymaga PHP w wersji 5.4 lub wyższej!';
        $this->confirmUninstall = 'Czy na pewno chcesz odinstalować moduł?';
        $this->limited_country = ['pl'];
    }

    /**
     * @return false
     */
    public function install()
    {
        $this->_errors[] = 'Moduł wymaga PHP w wersji 5.4 lub wyższej!';

        return false;
    }

    /**
     * @return mixed
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * @param $oCart
     * @param $mShippingCost
     * @return false
     */
    public function getOrderShippingCost($oCart, $mShippingCost)
    {
        return false;
    }

    /**
     * @param $mParams
     * @return false
     */
    public function getOrderShippingCostExternal($mParams)
    {
        return false;
    }
}
