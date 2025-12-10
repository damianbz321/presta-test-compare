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

namespace InPost\Shipping\Hook;

use InPost\Shipping\ShipX\Resource\Service;
use Media;
use Tools;

class Assets extends AbstractHook
{
    const HOOK_LIST = [
        'actionAdminControllerSetMedia',
        'actionFrontControllerSetMedia',
        'displayAdminAfterHeader',
    ];

    const GEO_WIDGET_JS_URL = 'https://geowidget.easypack24.net/js/sdk-for-javascript.js';
    const GEO_WIDGET_CSS_URL = 'https://geowidget.easypack24.net/css/easypack.css';

    protected $ordersDisplay;

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('configure') === $this->module->name) {
            $this->module->getAssetsManager()
                ->registerJavaScripts([
                    'app.js',
                    self::GEO_WIDGET_JS_URL,
                ])
                ->registerStyleSheets([self::GEO_WIDGET_CSS_URL]);
        } elseif (Tools::getValue('controller') === 'AdminOrders') {
            $display = $this->getOrdersDisplay();

            Media::addJsDef([
                'shopIs177' => $this->shopContext->is177(),
                'inpostLockerServices' => Service::LOCKER_SERVICES,
                'inpostLockerStandard' => Service::INPOST_LOCKER_STANDARD,
            ]);

            if ($display === 'view') {
                $this->module->getAssetsManager()
                    ->registerJavaScripts([
                        self::GEO_WIDGET_JS_URL,
                        'admin/tools.js',
                        'admin/common.js',
                        'admin/order-details.js',
                    ])
                    ->registerStyleSheets([
                        self::GEO_WIDGET_CSS_URL,
                        'admin/orders.css',
                    ]);
            } elseif ($display === 'index') {
                $this->module->getAssetsManager()
                    ->registerJavaScripts([
                        'admin/tools.js',
                        'admin/order-list.js',
                    ]);
            }
        }

        if ($this->shouldDisplayLoader()) {
            $this->module->getAssetsManager()
                ->registerStyleSheets(['admin/loader.css']);
        }
    }

    public function hookDisplayAdminAfterHeader()
    {
        return $this->shouldDisplayLoader()
            ? $this->module->display($this->module->name, 'views/templates/hook/loader.tpl')
            : '';
    }

    protected function shouldDisplayLoader()
    {
        return Tools::getValue('controller') === 'AdminOrders'
            && in_array($this->getOrdersDisplay(), ['index', 'view'])
            || isset($this->context->controller->module)
            && $this->context->controller->module === $this->module;
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (in_array(Tools::getValue('controller'), ['order', 'orderopc'])) {
            $this->module->getAssetsManager()
                ->registerJavaScripts([self::GEO_WIDGET_JS_URL], [
                    'position' => 'head',
                    'attributes' => ['async'],
                ])
                ->registerJavaScripts([
                    $this->shopContext->is17() ? 'checkout17.js' : 'checkout16.js',
                ])
                ->registerStyleSheets([
                    self::GEO_WIDGET_CSS_URL,
                    'front.css',
                ]);

            Media::addJsDef([
                'inPostAjaxController' => $this->context->link->getModuleLink($this->module->name, 'ajax'),
                'inPostLocale' => Tools::strtolower($this->context->language->iso_code) === 'pl' ? 'pl' : 'uk',
            ]);
        }
    }

    protected function getOrdersDisplay()
    {
        if (!isset($this->ordersDisplay)) {
            $this->ordersDisplay = $this->initOrdersDisplay();
        }

        return $this->ordersDisplay;
    }

    protected function initOrdersDisplay()
    {
        if ($this->shopContext->is177()) {
            switch (Tools::getValue('action')) {
                case 'vieworder':
                    return 'view';
                case 'addorder':
                    return 'create';
                default:
                    return 'index';
            }
        }

        if (Tools::isSubmit('vieworder')) {
            return 'view';
        } elseif (Tools::isSubmit('addorder')) {
            return 'create';
        }

        return 'index';
    }
}
