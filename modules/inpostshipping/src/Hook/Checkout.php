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

use Address;
use Carrier;
use Cart;
use InPost\Shipping\CartChoiceUpdater;
use InPost\Shipping\Presenter\CheckoutDataPresenter;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostCarrierModel;
use InPostCartChoiceModel;
use Order;
use OrderController;
use Tools;

class Checkout extends AbstractHook
{
    use ErrorsTrait;

    const HOOK_LIST = [
        'actionCarrierProcess',
        'actionValidateOrder',
    ];

    const HOOK_LIST_16 = [
        'displayCarrierList',
    ];

    const HOOK_LIST_17 = [
        'displayCarrierExtraContent',
        'actionValidateStepComplete',
    ];

    protected $deliveryChoiceProcessed = false;

    public function hookActionCarrierProcess($params)
    {
        if ($this->shopContext->is17()) {
            if (!$this->deliveryChoiceProcessed && Tools::isSubmit('delivery_option')) {
                $this->processDeliveryChoice($params['cart'], Tools::getAllValues());
            }
        } elseif (Tools::getValue('step') == OrderController::STEP_PAYMENT) {
            $this->processDeliveryChoice($params['cart'], Tools::getAllValues());

            if ($this->hasErrors()) {
                $this->context->controller->errors = array_merge(
                    $this->context->controller->errors,
                    $this->getErrors()
                );

                $this->module->getAssetsManager()
                    ->registerJavaScripts([
                        _THEME_JS_DIR_ . 'order-carrier.js',
                    ]);
            }
        }
    }

    public function hookActionValidateStepComplete($params)
    {
        if ($params['step_name'] === 'delivery' &&
            $this->processDeliveryChoice($this->context->cart, $params['request_params'])
        ) {
            $this->deliveryChoiceProcessed = true;

            if ($this->hasErrors()) {
                $params['completed'] = false;
                $this->storeSessionData($params['request_params']);
            }
        }
    }

    protected function processDeliveryChoice(Cart $cart, array $requestParams)
    {
        $deliveryOption = $requestParams['delivery_option'];

        $carrierIds = explode(',', trim($deliveryOption[$cart->id_address_delivery], ','));
        foreach ($carrierIds as $carrierId) {
            if ($carrierData = InPostCarrierModel::getDataByCarrierId($carrierId)) {
                $updater = $this->getCartChoiceUpdater($cart->id, $carrierData)
                    ->setEmail(isset($requestParams['inpost_email']) ? $requestParams['inpost_email'] : null)
                    ->setPhone(isset($requestParams['inpost_phone']) ? $requestParams['inpost_phone'] : null);

                if ($carrierData['lockerService']) {
                    $locker = isset($requestParams['inpost_locker'][$carrierId])
                        ? $requestParams['inpost_locker'][$carrierId]
                        : null;

                    $updater->setTargetPoint($locker);
                }

                $updater->saveChoice($cart->id);

                if ($updater->hasErrors()) {
                    $this->setErrors($updater->getErrors());
                }

                return true;
            }
        }

        return false;
    }

    protected function getCartChoiceUpdater($id_cart, array $carrierData)
    {
        /** @var CartChoiceUpdater $updater */
        $updater = $this->module->getService('inpost.shipping.updater.cart_choice');

        return $updater
            ->setCartChoice(new InPostCartChoiceModel($id_cart))
            ->setCarrierData($carrierData);
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        if ((!Tools::getValue('confirmDeliveryOption') || $this->deliveryChoiceProcessed) &&
            $carrierData = InPostCarrierModel::getDataByCarrierId($params['carrier']['id'])
        ) {
            $this->assignTemplateVariables($carrierData, $this->retrieveSessionData());

            return $this->module->display(
                $this->module->name,
                'views/templates/hook/carrier-extra-content.tpl'
            );
        }

        return '';
    }

    public function hookDisplayCarrierList($params)
    {
        $content = '';

        /** @var Address $address */
        $address = $params['address'];

        $deliveryOption = $this->context->cart->getDeliveryOption(null, true);

        $carrierIds = explode(',', trim($deliveryOption[$address->id], ','));
        foreach ($carrierIds as $carrierId) {
            if ($carrierData = InPostCarrierModel::getDataByCarrierId($carrierId)) {
                $this->assignTemplateVariables($carrierData);

                $content .= $this->module->display(
                    $this->module->name,
                    'views/templates/hook/16/carrier-extra-content.tpl'
                );
            }
        }

        return $content;
    }

    protected function assignTemplateVariables(array $carrierData, array $sessionData = [])
    {
        /** @var CheckoutDataPresenter $presenter */
        $presenter = $this->module->getService('inpost.shipping.presenter.checkout_data');

        $this->context->smarty->assign($presenter->present($carrierData, $sessionData));
    }

    public function hookActionValidateOrder($params)
    {
        /** @var Order $order */
        $order = $params['order'];
        $carrier = new Carrier($order->id_carrier);
        if ($carrier->external_module_name !== $this->module->name) {
            $cartChoice = new InPostCartChoiceModel();
            $cartChoice->id = $order->id_cart;
            $cartChoice->delete();
        }
    }

    // preserve errors and submitted values to retrieve after redirect
    protected function storeSessionData(array $requestParams)
    {
        $data = json_encode([
            'email' => isset($requestParams['inpost_email']) ? $requestParams['inpost_email'] : null,
            'phone' => isset($requestParams['inpost_phone']) ? $requestParams['inpost_phone'] : null,
            'errors' => $this->getErrors(),
        ]);

        switch (session_status()) {
//            case PHP_SESSION_NONE:
//                session_start();
//                // no break
//            case PHP_SESSION_ACTIVE:
//                $_SESSION['inpost_data'] = $data;
//                break;
            default:
                $this->context->cookie->inpost_data = $data;
                break;
        }
    }

    protected function retrieveSessionData()
    {
        static $data;

        if (!isset($data)) {
            $data = [];

            switch (session_status()) {
//                case PHP_SESSION_NONE:
//                    session_start();
//                    // no break
//                case PHP_SESSION_ACTIVE:
//                    if (isset($_SESSION['inpost_data'])) {
//                        $data = json_decode($_SESSION['inpost_data'], true);
//                        unset($_SESSION['inpost_data']);
//                    }
//                    break;
                default:
                    if (isset($this->context->cookie->inpost_data)) {
                        $data = json_decode($this->context->cookie->inpost_data, true);
                        unset($this->context->cookie->inpost_data);
                    }
                    break;
            }
        }

        return $data;
    }
}
