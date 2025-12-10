<?php
/**
 * Class przelewy24validateOrderRequestModuleFrontController
 *
 * @author    Przelewy24
 * @copyright Przelewy24
 * @license   https://www.gnu.org/licenses/lgpl-3.0.en.html
 *
 */

/**
 * Class Przelewy24validateOrderRequestModuleFrontController
 */
class Przelewy24validateOrderRequestModuleFrontController extends ModuleFrontController
{
    /**
     * Init content.
     */
    public function initContent()
    {
        parent::initContent();

        /** @var CartCore $cart */
        $cart = Context::getContext()->cart;
        /** @var CustomerCore $customer */
        $customer = Context::getContext()->customer;

        $requestCartId = (int)Tools::getValue('cartId');

        $currency = new Currency($cart->id_currency);

        $suffix = Przelewy24Helper::getSuffix($currency->iso_code);

        $lang = (new Przelewy24())->getLangArray();
        $description = Przelewy24OrderDescriptionHelper::buildDescription(
            $this->module->l('Order'),
            $suffix,
            new Przelewy24PaymentData($cart),
            $lang['Cart']." {$cart->id}"
        );

        if ($requestCartId === (int)$cart->id) {
            if ($cart && (2 === (int)Configuration::get('P24_VERIFYORDER' . $suffix))) {
                if (!$cart->OrderExists()) {
                    $amount = $cart->getOrderTotal(true, Cart::BOTH);

                    $orderBeginingState = Configuration::get('P24_ORDER_STATE_1');

                    $cartId = (int)$cart->id;

                    $this->module->validateOrder(
                        $cartId,
                        (int)$orderBeginingState,
                        (float)$amount,
                        $this->module->displayName,
                        null,
                        array(),
                        (int)$cart->id_currency,
                        false,
                        $customer->secure_key
                    );

                    /* PrestaShop require us to clear the cart after action above. */
                    unset($this->context->cookie->id_cart);

                    $servicePaymentOptions = new Przelewy24ServicePaymentOptions(new Przelewy24());
                    $servicePaymentOptions->setExtrachargeByOrderId((int)Order::getIdByCartId($cartId));
                }

                $orderId = (int)Order::getIdByCartId($cartId);


                Przelewy24Helper::renderJson(
                    array(
                        'orderId' => $orderId,
                        'description' => $description,
                    )
                );
            }
        }
        Przelewy24Helper::renderJson(array());
    }
}
