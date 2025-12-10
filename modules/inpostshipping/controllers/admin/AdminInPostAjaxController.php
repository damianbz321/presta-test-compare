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

require_once dirname(__FILE__) . '/InPostShippingAdminController.php';

use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Configuration\SzybkieZwrotyConfiguration;
use InPost\Shipping\Handler\ShippingService\AddServiceHandler;
use InPost\Shipping\Handler\ShippingService\DeleteServiceHandler;
use InPost\Shipping\Handler\ShippingService\UpdateServiceHandler;
use InPost\Shipping\Presenter\Store\Modules\OrganizationModule;
use InPost\Shipping\Validator\ApiConfigurationValidator;
use InPost\Shipping\Validator\SenderValidator;
use InPost\Shipping\Validator\WeekendDeliveryConfigurationValidator;

class AdminInPostAjaxController extends InPostShippingAdminController
{
    public $ajax = true;

    public function ajaxProcessRefreshOrganizationData()
    {
        /** @var OrganizationModule $organizationPresenter */
        $organizationPresenter = $this->module->getService('inpost.shipping.store.module.organization');

        $this->response = $organizationPresenter->present();
    }

    public function ajaxProcessUpdateApiConfiguration()
    {
        $token = Tools::getValue('apiToken');
        $organizationId = Tools::getValue('organizationId');

        /** @var ApiConfigurationValidator $validator */
        $validator = $this->module->getService('inpost.shipping.validator.api_configuration');

        if ($validator->validate([
            'token' => $token,
            'organizationId' => $organizationId,
            'sandbox' => false,
        ])) {
            /** @var ShipXConfiguration $configuration */
            $configuration = $this->module->getService('inpost.shipping.configuration.shipx');
            $configuration->setProductionApiToken($token);
            $configuration->setProductionOrganizationId($organizationId);

            $this->ajaxProcessRefreshOrganizationData();
        } else {
            $this->errors = $validator->getErrors();
        }
    }

    public function ajaxProcessUpdateSandboxApiConfiguration()
    {
        $token = Tools::getValue('sandboxApiToken');
        $organizationId = Tools::getValue('sandboxOrganizationId');
        $enableSandbox = Tools::getValue('enableSandbox');

        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');
        /** @var ApiConfigurationValidator $validator */
        $validator = $this->module->getService('inpost.shipping.validator.api_configuration');

        if ($validator->validate([
            'token' => $token,
            'organizationId' => $organizationId,
            'sandbox' => true,
        ])) {
            $configuration->setSandboxModeEnabled($enableSandbox);
            $configuration->setSandboxApiToken($token);
            $configuration->setSandboxOrganizationId($organizationId);
        } else {
            $this->errors = $validator->getErrors();
            if (!$enableSandbox) {
                $configuration->setSandboxModeEnabled(false);
            }
        }
    }

    public function ajaxProcessDisableSandboxMode()
    {
        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');
        $configuration->setSandboxModeEnabled(false);
    }

    public function ajaxProcessUpdateSenderDetails()
    {
        $sender = json_decode(Tools::getValue('sender'), true);

        /** @var SenderValidator $validator */
        $validator = $this->module->getService('inpost.shipping.validator.sender');

        if ($validator->validate($sender)) {
            /** @var SendingConfiguration $configuration */
            $configuration = $this->module->getService('inpost.shipping.configuration.sending');

            $configuration->setSenderDetails($sender);
        } else {
            $this->errors = $validator->getErrors();
        }
    }

    public function ajaxProcessUpdateSendingOptions()
    {
        /** @var SendingConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.sending');

        $configuration->setDefaultSendingMethod(Tools::getValue('sendingMethod'));
        $configuration->setDefaultLocker(json_decode(Tools::getValue('locker')));
        $configuration->setDefaultPOP(json_decode(Tools::getValue('pop')));
        $configuration->setDefaultDispatchPointId(Tools::getValue('dispatchPoint'));
    }

    public function ajaxProcessAddService()
    {
        /** @var AddServiceHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.add_service');

        if ($carrier = $handler->handle(Tools::getAllValues())) {
            $this->presentCarrier($carrier);
        } else {
            $this->errors = $handler->getErrors();
        }
    }

    public function ajaxProcessUpdateService()
    {
        /** @var UpdateServiceHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.update_service');

        if ($carrier = $handler->handle(Tools::getAllValues())) {
            $this->presentCarrier($carrier);
        } else {
            $this->errors = $handler->getErrors();
        }
    }

    protected function presentCarrier(InPostCarrierModel $carrier)
    {
        $presenter = $this->module->getService('inpost.shipping.presenter.carrier');

        $this->response['carrier'] = $presenter->present($carrier);
    }

    public function ajaxProcessDeleteService()
    {
        /** @var DeleteServiceHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.delete_service');

        if (!$handler->handle(Tools::getAllValues())) {
            $this->errors = $handler->getErrors();
        }
    }

    public function ajaxProcessUpdateWeekendDelivery()
    {
        /** @var WeekendDeliveryConfigurationValidator $validator */
        $validator = $this->module->getService('inpost.shipping.validator.weekend_delivery_configuration');

        if ($validator->validate($request = Tools::getAllValues())) {
            /** @var CarriersConfiguration $configuration */
            $configuration = $this->module->getService('inpost.shipping.configuration.carriers');

            $configuration->setWeekendDeliveryStartDay($request['startDay']);
            $configuration->setWeekendDeliveryStartHour($request['startHour']);
            $configuration->setWeekendDeliveryEndDay($request['endDay']);
            $configuration->setWeekendDeliveryEndHour($request['endHour']);
        } else {
            $this->errors = $validator->getErrors();
        }
    }

    public function ajaxProcessUpdateSzybkieZwroty()
    {
        $storeName = Tools::getValue('storeName');

        /** @var SzybkieZwrotyConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.szybkie_zwroty');
        $configuration->setStoreName($storeName);
    }
}
