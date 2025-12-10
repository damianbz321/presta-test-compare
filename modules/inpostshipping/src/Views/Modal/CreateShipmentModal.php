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

namespace InPost\Shipping\Views\Modal;

use Carrier;
use Currency;
use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\ChoiceProvider\DimensionTemplateChoiceProvider;
use InPost\Shipping\ChoiceProvider\SendingMethodChoiceProvider;
use InPost\Shipping\ChoiceProvider\ShippingServiceChoiceProvider;
use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\PrestaShopContext;
use InPost\Shipping\ShipX\Resource\Service;
use InPostCarrierModel;
use InPostShipping;
use Order;
use Tools;

class CreateShipmentModal extends AbstractModal
{
    const TRANSLATION_SOURCE = 'CreateShipmentModal';

    const MODAL_ID = 'inpost-create-shipment-modal';

    protected $customerChoiceDataProvider;
    protected $shippingServiceChoiceProvider;
    protected $sendingMethodChoiceProvider;
    protected $dimensionTemplateChoiceProvider;
    protected $sendingConfiguration;
    protected $carriersConfiguration;

    protected $order;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        PrestaShopContext $shopContext,
        CustomerChoiceDataProvider $customerChoiceDataProvider,
        ShippingServiceChoiceProvider $shippingServiceChoiceProvider,
        SendingMethodChoiceProvider $sendingMethodChoiceProvider,
        DimensionTemplateChoiceProvider $dimensionTemplateChoiceProvider,
        SendingConfiguration $sendingConfiguration,
        CarriersConfiguration $carriersConfiguration
    ) {
        parent::__construct($module, $link, $shopContext);

        $this->customerChoiceDataProvider = $customerChoiceDataProvider;
        $this->shippingServiceChoiceProvider = $shippingServiceChoiceProvider;
        $this->sendingMethodChoiceProvider = $sendingMethodChoiceProvider;
        $this->dimensionTemplateChoiceProvider = $dimensionTemplateChoiceProvider;
        $this->sendingConfiguration = $sendingConfiguration;
        $this->carriersConfiguration = $carriersConfiguration;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    protected function assignContentTemplateVariables()
    {
        if (isset($this->order)) {
            $customerChoice = $this->customerChoiceDataProvider->getDataByCartId($this->order->id_cart);
            $carrier = new Carrier($this->order->id_carrier);
            $inPostCarrier = new InPostCarrierModel($carrier->id_reference);
            $defaultDimensions = $this->carriersConfiguration->getDefaultShipmentDimensions($customerChoice->service);
            $defaultTemplates = $this->carriersConfiguration->getDefaultDimensionTemplates();
            $defaultSendingMethods = $this->carriersConfiguration->getDefaultSendingMethods();

            $this->context->smarty->assign([
                'shipmentAction' => $this->link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'createShipment',
                ]),
                'id_order' => $this->order->id,
                'customerEmail' => $customerChoice->email,
                'customerPhone' => $customerChoice->phone,
                'serviceChoices' => $this->shippingServiceChoiceProvider->getChoices(),
                'selectedService' => $customerChoice->service,
                'sendingMethodChoices' => $this->sendingMethodChoiceProvider->getChoices(),
                'defaultSendingMethods' => $defaultSendingMethods,
                'defaultSendingMethod' => isset($defaultSendingMethods[$customerChoice->service])
                    ? $defaultSendingMethods[$customerChoice->service]
                    : $this->sendingConfiguration->getDefaultSendingMethod(),
                'defaultPop' => $this->sendingConfiguration->getDefaultPOP(),
                'defaultLocker' => $this->sendingConfiguration->getDefaultLocker(),
                'selectedPoint' => $customerChoice->point,
                'dimensionTemplateChoices' => $this->dimensionTemplateChoiceProvider->getChoices(),
                'orderReference' => $this->order->reference,
                'length' => $defaultDimensions ? $defaultDimensions['length'] : 0,
                'width' => $defaultDimensions ? $defaultDimensions['width'] : 0,
                'height' => $defaultDimensions ? $defaultDimensions['height'] : 0,
                'weight' => $this->order->getTotalWeight() ?: ($defaultDimensions ? $defaultDimensions['weight'] : 0),
                'cashOnDelivery' => $inPostCarrier->cod,
                'weekendDelivery' => $inPostCarrier->weekend_delivery,
                'useTemplate' => in_array($inPostCarrier->service, Service::LOCKER_SERVICES) && !$defaultDimensions,
                'defaultTemplates' => $defaultTemplates,
                'template' => isset($defaultTemplates[$customerChoice->service])
                    ? $defaultTemplates[$customerChoice->service]
                    : null,
                'orderTotal' => Tools::math_round($this->order->total_paid, 2),
                'currencySign' => Currency::getCurrencyInstance($this->order->id_currency)->sign,
            ]);
        }
    }

    public function renderContent()
    {
        return isset($this->order) ? parent::renderContent() : '';
    }

    protected function getTitle()
    {
        return $this->module->l('Create shipment', self::TRANSLATION_SOURCE);
    }

    protected function getClasses()
    {
        return $this->shopContext->is177() ? '' : 'modal-lg';
    }

    protected function getActions()
    {
        return [
            [
                'type' => 'button',
                'value' => 'submitShipment',
                'class' => 'js-submit-shipment-form btn-primary',
                'label' => $this->module->l('Submit', self::TRANSLATION_SOURCE),
            ],
        ];
    }
}
