<?php
/**
 * Copyright 2021-2021 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2021 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

class InPostShipping extends CarrierModule
{
    public $confirmUninstall;

    public $id_carrier;

    /** @var \InPost\Shipping\HookDispatcher */
    protected $hookDispatcher;

    /** @var \InPost\Shipping\Install\Installer */
    protected $installer;

    /** @var \InPost\Shipping\Adapter\TranslateAdapter */
    protected $translate;

    /** @var \InPost\Shipping\Adapter\AssetsManager */
    protected $assetsManager;

    protected $serviceContainer;

    public function __construct()
    {
        $this->name = 'inpostshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.1.0';
        $this->author = 'InPost S.A.';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('InPost Shipping');
        $this->description = $this->l('Official InPost integration module for PrestaShop');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->limited_countries = ['pl'];

        $this->ps_versions_compliancy = ['min' => '1.6.1', 'max' => _PS_VERSION_];

        if ($this->shouldUseLiveApi()) {
            $this->useLiveApi();
        }
    }

    public function install()
    {
        return parent::install()
            && $this->getInstaller()->install();
    }

    public function uninstall()
    {
        return $this->getInstaller()->uninstall()
            && parent::uninstall();
    }

    protected function getInstaller()
    {
        if (!isset($this->installer)) {
            $this->installer = $this->getService('inpost.shipping.install.installer');
        }

        return $this->installer;
    }

    public function getContent()
    {
        $this->useLiveApi();

        /** @var \InPost\Shipping\Presenter\Store\StorePresenter $storePresenter */
        $storePresenter = $this->getService('inpost.shipping.store.presenter');
        Media::addJsDef([
            'store' => $storePresenter->present(),
        ]);

        return $this->display(__FILE__, '/views/templates/admin/configuration.tpl');
    }

    public function getService($serviceName)
    {
        if (!isset($this->serviceContainer)) {
            $this->serviceContainer = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }

        return $this->serviceContainer->getService($serviceName);
    }

    protected function getHookDispatcher()
    {
        if (!isset($this->hookDispatcher)) {
            $this->hookDispatcher = $this->getService('inpost.shipping.hook_dispatcher');
        }

        return $this->hookDispatcher;
    }

    public function __call($methodName, array $arguments)
    {
        return $this->getHookDispatcher()->dispatch($methodName, isset($arguments[0]) ? $arguments[0] : []);
    }

    protected function useLiveApi()
    {
        /** @var \InPost\Shipping\Configuration\ShipXConfiguration $configuration */
        $configuration = $this->getService('inpost.shipping.configuration.shipx');
        $configuration->setSandboxMode(false);
    }

    protected function getTranslate()
    {
        if (!isset($this->translate)) {
            $this->translate = $this->getService('inpost.shipping.adapter.translate');
        }

        return $this->translate;
    }

    public function getAssetsManager()
    {
        if (!isset($this->assetsManager)) {
            $this->assetsManager = $this->getService('inpost.shipping.adapter.assets_manager');
        }

        return $this->assetsManager;
    }

    public function l($string, $specific = false, $locale = null)
    {
        if (self::$_generate_config_xml_mode) {
            return $string;
        }

        return $this->getTranslate()->getModuleTranslation(
            $this,
            $string,
            $specific ? Tools::strtolower($specific) : $this->name,
            $locale
        );
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    /** @param Cart $params */
    public function getOrderShippingCost($params, $shipping_cost)
    {
        if (!$this->context->controller instanceof AdminController) {
            /** @var \InPost\Shipping\TimeChecker $timeChecker */
            $timeChecker = $this->getService('inpost.shipping.time_checker');

            if (!$timeChecker->shouldEnableWeekendDelivery()) {
                return false;
            }
        }

        return $shipping_cost;
    }

    /** @param Cart $params */
    public function getOrderShippingCostExternal($params)
    {
        return false;
    }

    protected function shouldUseLiveApi()
    {
        return isset($this->context->controller)
            ? $this->context->controller instanceof FrontController
            : 'module' === Tools::getValue('fc') && $this->name === Tools::getValue('module');
    }
}
