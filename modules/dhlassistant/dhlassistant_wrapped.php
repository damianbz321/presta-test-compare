<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'Core.php';

use DhlAssistant\Core;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Controllers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Wrappers\DbWrapper;

/**
 *
 */
class DhlAssistant extends CarrierModule
{
    /**
     * @var string|int
     */
    public $id_carrier;

    /**
     * @throws Core\Exceptions\LoggedException
     */
    public function __construct()
    {
        $this->name = Wrappers\ConfigWrapper::Get('ModuleName');
        $this->tab = 'shipping_logistics';
        $this->version = Wrappers\ConfigWrapper::Get('Version');
        $this->author = Wrappers\ConfigWrapper::Get('Author');
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->bootstrap = false;

        parent::__construct();

        $this->displayName = Wrappers\ConfigWrapper::Get('FullName');
        $this->description = Wrappers\ConfigWrapper::Get('Description');

        $this->confirmUninstall = Wrappers\ConfigWrapper::Get('UninstallQuestion');
        $this->limited_country = ['pl'];
    }

    /**
     * @return bool
     */
    public function install()
    {
        $SourceWrapper = new Wrappers\SourceWrapper();

        if (!extension_loaded('soap')) {
            $this->_errors[] = 'Rozszerzenie SoapClient dla PHP jest wymagane!';

            return false;
        }

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (parent::install() == false
            || Wrappers\SourceWrapper::SavePsStaticConfig() == false
            || Wrappers\SourceWrapper::InstallSql() == false
            || Wrappers\SourceWrapper::InstallTabs() == false
            || Wrappers\SourceWrapper::InstallOrderState() == false
            || $SourceWrapper->createCarriers() == false
            || $this->registerHook('header') == false
            || $this->registerHook('displayBackOfficeHeader') == false
            || $this->registerHook('displayAdminOrder') == false
            || $this->registerHook('displayPaymentTop') == false
            || $this->registerHook('updateCarrier') == false
            || $this->registerHook('displayCarrierExtraContent') == false
            || $this->registerHook('displayOrderPreview') == false
        ) {
            return false;
        }

        $this->warning = 'Należy skonfigurować moduł!';

        return true;
    }

    /**
     * @return false
     */
    public function uninstall()
    {
        if (Wrappers\SourceWrapper::DeletePsStaticConfig() == false
            || Wrappers\SourceWrapper::UninstallOrderState() == false
            || Wrappers\SourceWrapper::UninstallTabs() == false
            || Wrappers\SourceWrapper::UninstallCarrier() == false
            || Wrappers\SourceWrapper::UninstallSql() == false
        ) {
            return false;
        }

        return parent::uninstall();
    }

    /**
     * @return mixed
     * @throws Core\Exceptions\LoggedException
     */
    public function getContent()
    {
        $iframe_link = '';
        $page = Tools::getValue('page');

        if (!Wrappers\ConfigWrapper::Get('IsModuleConfigured')) {
            $iframe_link = (new Controllers\SettingsEdit())->GetLink();
        } else {
            if ($page == 'Edit' && ($id = Tools::getValue('id'))) {
                $iframe_link = (new Controllers\ShipmentEdit())->GetLink(['source_id' => $id]);
            } else {
                if (class_exists(($class_name = '\DhlAssistant\Classes\Controllers\\' . $page))) {
                    $iframe_link = (new $class_name())->GetLink();
                } else {
                    $iframe_link = (new Controllers\Index())->GetLink();
                }
            }
        }

        $this->context->controller->addJS($this->_path . 'Media/Js/iframeResizer.min.js');
        $this->context->smarty->assign(['dhlassistant_iframe_link' => $iframe_link]);

        return $this->context->smarty->fetch(
            \DhlAssistant\Core::$BASEDIR . 'LocalTemplates/ModuleConfiguration.tpl'
        );
    }

    /**
     * @param $params
     */
    public function hookHeader($params)
    {
        $context = \Context::getContext();

        if (!$context || !isset($context->controller) || !isset($context->controller->php_self)) {
            return;
        }

        if (in_array($this->context->controller->php_self, ['order', 'order-opc'])) {
            $this->context->controller->addCSS(
                $this->_path . 'Media/Css/DhlAssistantFrontOffice.css'
            );
        }
    }

    /**
     * @param $param
     */
    public function hookDisplayBackOfficeHeader($param)
    {
        $this->context->controller->addCSS($this->_path . 'Media/Css/tabs.css');
    }

    /**
     * @param $params
     * @return string
     * @throws Core\Exceptions\LoggedException
     * @throws Core\Exceptions\SourceLoggedException
     */
    public function hookDisplayOrderPreview($params)
    {
        if (!Wrappers\SourceWrapper::HasUserUsingRight() || !isset($params['order_id'])) {
            return '';
        }

        $smarty_params = [
            'dhlassistant_configuration_link' => false,
            'dhlassistant_shipment' => null,
            'dhlassistant_sended' => false,
            'dhlassistant_tracking_link' => false,
            'dhlassistant_label_links' => false,
            'dhlassistant_edit_link' => false,
            'dhlassistant_country_not_supported' => false,
        ];

        if (!Wrappers\ConfigWrapper::Get('IsModuleConfigured')) {
            $smarty_params['dhlassistant_configuration_link'] = $this->context->link->getAdminLink('AdminModules')
                . '&configure='
                . $this->name
                . '&page=SettingsEdit';
        } else {
            $source_id = (int)$params['order_id'];
            $smarty_params['dhlassistant_edit_link'] = $this->context->link->getAdminLink('AdminModules')
                . '&configure='
                . $this->name
                . '&page=Edit&id='
                . $source_id;

            if (Wrappers\SourceWrapper::HasSourceShipment($source_id)) {
                /* @var $shipment DataModels\Shipment */
                $shipment = Wrappers\SourceWrapper::GetShipmentForSource($source_id);
                $smarty_params['dhlassistant_sended'] = $shipment->IsSended();

                if ($smarty_params['dhlassistant_sended']) {
                    $smarty_params['dhlassistant_shipment'] = $shipment;
                    $smarty_params['dhlassistant_tracking_link'] = Wrappers\DhlWrapper::GetTrackLink($shipment);
                    $get_label_controller = new Controllers\GetLabel();

                    foreach ($shipment->GetAvailableLabelTypes() as $label_code) {
                        $smarty_params['dhlassistant_label_links'][$label_code] = $get_label_controller->GetLink(
                            [
                                'id' => $shipment->Id,
                                'type' => $label_code
                            ]
                        );
                    }
                }
            } else {
                $order = new \Order((int)$source_id);
                $address = new \Address((int)$order->id_address_delivery);
                $country = new \Country($address->id_country);

                if (!in_array($country->iso_code, Wrappers\ConfigWrapper::Get('AvailableCountryCodes'))) {
                    $smarty_params['dhlassistant_country_not_supported'] = true;
                }
            }
        }

        $this->context->smarty->assign($smarty_params);

        return $this->context->smarty->fetch(
            \DhlAssistant\Core::$BASEDIR . 'LocalTemplates/HookAdminOrder.tpl'
        );
    }

    /**
     * @param $params
     * @return string|void
     */
    public function hookDisplayCarrierExtraContent($params)
    {
        try {
            $carrierCode = DbWrapper::GetCodeCarrier($params['carrier']['id']);

            $params['address'] = current($this->context->cart->getAddressCollection());
            $params['cart'] = $this->context->cart;

            $country = new \Country((int)$params['address']->id_country);

            if ($country->iso_code == 'DE') {
                $map_parcelshop = 'https://parcelshop.dhl.pl/mapa?country=DE&ptype=parcelShop';
                $map_parcelstation = 'https://parcelshop.dhl.pl/mapa?country=DE&ptype=packStation';
            } elseif (in_array($country->iso_code, ['DK', 'FR', 'SK'])) {
                $map_parcelshop = 'https://parcelshop.dhl.pl/mapa?country=' . $country->iso_code;
            }

            $templates = [
                Enums\Carrier::PL_CARRIER_POP => [
                    'tpl' => 'LocalTemplates/PrestaOrderFormCarrierHandling.tpl',
                    'js' => 'Media/Js/PlCarrierPop.js',
                    'js_checkout_validation' => 'Media/Js/CheckoutValidation.js',
                    'map_link' => 'https://parcelshop.dhl.pl/mapa?country=PL&amp;ptype=parcelShop',
                ],
                Enums\Carrier::PL_CARRIER_POP_COD => [
                    'tpl' => 'LocalTemplates/PrestaOrderFormCarrierHandling.tpl',
                    'js' => 'Media/Js/PlCarrierPopCod.js',
                    'js_checkout_validation' => 'Media/Js/CheckoutValidation.js',
                    'map_link_cod' => 'https://parcelshop.dhl.pl/mapa?type=lmcod'
                ],
                Enums\Carrier::FOREIGN_CARRIER_PARCELSHOP => [
                    'tpl' => 'LocalTemplates/PrestaOrderFormCarrierHandling.tpl',
                    'js' => 'Media/Js/ForeignCarrierParcelshop.js',
                    'js_checkout_validation' => 'Media/Js/CheckoutValidation.js',
                    'map_link_station' => $map_parcelshop,
                    'map_link' => $map_parcelstation,
                ],
            ];

            $tpl = 'LocalTemplates/Default.tpl';

            if (isset($templates[$carrierCode])) {
                $tpl = $templates[$carrierCode]['tpl'];
                $js = $templates[$carrierCode]['js'];
                $jsCheckoutValidation = $templates[$carrierCode]['js_checkout_validation'];
            }

            if (!\Validate::isLoadedObject($country)
                || !in_array($country->iso_code, Wrappers\ConfigWrapper::Get('AvailableCountryCodes'))
            ) {
                return '';
            }

            $params_obj = Wrappers\SourceWrapper::GetSourceOrderAdditionalParams(
                (int)$params['cart']->id,
                $country->iso_code
            );

            /* @var $dhluser DataModels\DhlUser */
            $country_services = Wrappers\ConfigWrapper::Get('DefaultDhlUser')
                ->GetAvailableCountries()[$country->iso_code]
                ->AvailableServices;

            $ps_service = null;
            $pl_service = null;
            $ps_only_services = true;

            /* @var $service DataModels\DhlCountryService */
            while (true) {
                if ($country_services) {
                    foreach ($country_services as $service) {
                        if (!$ps_service && $service->AllowParcelShop) {
                            $ps_service = $service;
                        }

                        if (!$pl_service && $service->AllowParcelLocker) {
                            $pl_service = $service;
                        }

                        if (!$service->ParcelShopOnlyService) {
                            $ps_only_services = false;
                        }
                    }

                    if ($ps_service || $pl_service) {
                        break;
                    }
                }

                return '';
            }

            $smarty_params = [
                'dhlassistant_default_parcel_shop' => (int)(
                    Wrappers\ConfigWrapper::Get('DefaultParcelShopInPL')
                    && ($country->iso_code == Enums\Country::PL)
                    && (!$params_obj->IsSaved() || $params_obj->SendToParcelShop)
                ),
                'dhlassistant_carrier_handling_js_url' => Wrappers\ConfigWrapper::Get('BaseUrl') . $js,
                'dhlassistant_checkout_validation_js_url' => Wrappers\ConfigWrapper::Get('BaseUrl') . $jsCheckoutValidation,
                'dhlassistant_carrier_id' => $params['carrier']['id'],
                'dhlassistant_iso_code' => $country->iso_code,
                'dhlassistant_ajax_catcher_url' => $this->context->link->getModuleLink(
                    Wrappers\ConfigWrapper::Get('ModuleName'),
                    'AjaxCatcher',
                    ['controller' => 'AjaxCatcher']
                ),
                'dhlassistant_is_ps_available' => (int)(bool)$ps_service,
                'dhlassistant_is_ps_only_service' => (int)$ps_only_services,
                'dhlassistant_is_pl_available' => (int)(bool)$pl_service,
                'dhlassistant_is_map_for_parcel_available' => (int)($ps_service && $ps_service->AllowSearchParcelByMap),
                'dhlassistant_map_for_ps_url' => $templates[$carrierCode]['map_link_station'],
                'dhlassistant_map_for_pl_url' => $templates[$carrierCode]['map_link'],
                'dhlassistant_map_for_pl_url_cod' => $templates[$carrierCode]['map_link_cod'],

                'dhlassistant_require_postnummer_for_pl' => (int)($pl_service && $pl_service->RequirePostnummerForParcelLocker),
                'dhlassistant_require_postalcode_for_ps' => (int)($ps_service && $ps_service->RequirePostalCodeForParcel),
                'dhlassistant_require_postalcode_for_pl' => (int)($pl_service && $pl_service->RequirePostalCodeForParcel),
                'dhlassistant_send_ps' => (int)($ps_service && $params_obj->SendToParcelShop),
                'dhlassistant_send_pl' => (int)($pl_service && $params_obj->SendToParcelLocker),
                'dhlassistant_parcel_ident' => $params_obj->ParcelIdent,
                'dhlassistant_postnummer' => $params_obj->Postnummer,
                'dhlassistant_parcel_postal_code' => $params_obj->ParcelPostalCode,
                'dhlassistant_carrier_code' => $carrierCode,
            ];

            $this->context->smarty->assign($smarty_params);

            return $this->context->smarty->fetch(
                \DhlAssistant\Core::$BASEDIR . $tpl);


        } catch (\Exception $Ex) {
        }
    }

    /**
     * @param $params
     * @return string|void
     */
    public function hookDisplayPaymentTop($params)
    {
        try {
            if (!Wrappers\ConfigWrapper::Get('IsModuleConfigured')
                || !\Validate::isLoadedObject($params['cart'])
                || !($params['cart']->id_carrier == Wrappers\ConfigWrapper::Get('CarrierId'))
            ) {
                return '';
            }

            $address = new \Address ((int)$params['cart']->id_address_delivery);

            if (!\Validate::isLoadedObject($address)) {
                return '';
            }

            $country = new \Country($address->id_country);

            if (!\Validate::isLoadedObject($country)
                || !in_array(
                    $country->iso_code,
                    Wrappers\ConfigWrapper::Get('AvailableCountryCodes')
                )
            ) {
                return '';
            }

            if (Wrappers\SourceWrapper::CanCartBeCod(
                Wrappers\ConfigWrapper::Get('DefaultDhlUser'),
                $params['cart']
            )) {
                return '';
            }

            $cache_id = 'exceptionsCache';
            $exceptions_cache = (\Cache::isStored($cache_id)) ? \Cache::retrieve($cache_id) : [];
            $controller = (\Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
            $id_hook = \Hook::getIdByName('displayPayment');
            $shop = new \Shop($params['cart']->id_shop);

            if (!\Validate::isLoadedObject($shop)) {
                return '';
            }

            if ($payment_modules = \Module::getPaymentModules()) {
                foreach ($payment_modules as $module) {
                    if (in_array($module['name'], Wrappers\ConfigWrapper::Get('CodPaymentModules'))) {
                        $module_instance = \Module::getInstanceByName($module['name']);

                        if (\Validate::isLoadedObject($module_instance)) {
                            $key = (int)$id_hook . '-' . (int)$module_instance->id;
                            $exceptions_cache[$key][$shop->id][] = $controller;
                        }
                    }
                }

                \Cache::store($cache_id, $exceptions_cache);
            }
        } catch (\Exception $Ex) {
        }
    }

    /**
     * @param $params
     * @throws Core\Exceptions\LoggedException
     */
    public function hookUpdateCarrier($params)
    {
        if (!isset($params['id_carrier'])
            || !isset($params['carrier'])
            || !\Validate::isLoadedObject($params['carrier'])
        ) {
            return;
        }

        $oldCarrierId = (int)$params['id_carrier'];
        $newCarrierId = (int)$params['carrier']->id;
        DbWrapper::UpdateCarrierId($oldCarrierId, $newCarrierId);
    }

    /**
     * @param $oCart
     * @param $mShippingCost
     * @return false
     * @throws Core\Exceptions\LoggedException
     */
    public function getOrderShippingCost($oCart, $mShippingCost)
    {
        if (!\Validate::isLoadedObject($oCart)) {
            return false;
        }

        $address = new \Address((int)$oCart->id_address_delivery);
        $country = new \Country((int)$address->id_country);

        if (!Wrappers\ConfigWrapper::Get('IsModuleConfigured')
            || !$address
            || !$country
            || !$this->id_carrier
            || !in_array($country->iso_code, Wrappers\ConfigWrapper::Get('AvailableCountryCodes'))
        ) {
            return false;
        }

        if (!$this->validateCountry($country->iso_code, $this->id_carrier)) {
            return false;
        }

        return $mShippingCost;
    }

    /**
     * Validate coutries.
     *
     * @param string $countryIsoCode
     * @param string|int $carrierId
     * @return bool
     */
    public function validateCountry($countryIsoCode, $carrierId)
    {
        $carrierCode = DbWrapper::GetCodeCarrier($carrierId);

        try {
            if (!in_array($carrierCode, ['FOREIGN_CARRIER_PARCELSHOP', 'FOREIGN_CARRIER_STANDARD'])
                && in_array($countryIsoCode, ['DK', 'DE', 'FR', 'SK'])
            ) {
                return false;
            }

            if (!in_array(
                    $carrierCode,
                    ['PL_CARRIER_POP_COD', 'PL_CARRIER_POP', 'PL_CARRIER_STANDARD_COD', 'CARRIER_STANDARD']
                )
                && ($countryIsoCode == 'PL')
            ) {
                return false;
            }

            if (!in_array($countryIsoCode, ['DK', 'DE', 'SK', 'FR', 'PL'])
                && $carrierCode != 'FOREIGN_CARRIER_STANDARD'
            ) {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }

        return true;
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
