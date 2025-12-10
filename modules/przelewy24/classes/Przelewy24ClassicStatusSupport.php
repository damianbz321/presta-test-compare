<?php
/**
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24RestStatusSupport
 */
class Przelewy24ClassicStatusSupport implements Przelewy24StatusSupportInterface
{
    /**
     * Get payload for log.
     *
     * @return string
     */
    public function getPayloadForLog()
    {
        return var_export($_POST, true);
    }

    /**
     * Get session id.
     *
     * @return string
     */
    public function getSessionId()
    {
        return Tools::getValue('p24_session_id');
    }

    /**
     * Get P24 order id.
     *
     * @return string
     */
    public function getP24OrderId()
    {
        return Tools::getValue('p24_order_id');
    }

    /**
     * Get P24 order id.
     *
     * @return string
     */
    public function getP24Number()
    {
        return Tools::getValue('p24_order_id');
    }

    /**
     * Possible card to save.
     *
     * @return bool
     */
    public function possibleCardToSave()
    {
        return in_array((int)Tools::getValue('p24_method'), Przelewy24OneClickHelper::getCardPaymentIds());
    }

    /**
     * Verify payload.
     *
     * @param string $totalAmount;
     * @param Currency $currency
     * @param string $suffix
     * @return bool
     */
    public function verify($totalAmount, $currency, $suffix)
    {
        if ((int)Tools::getValue('p24_merchant_id') !== (int)Configuration::get('P24_MERCHANT_ID' . $suffix)) {
            return false;
        } elseif ((int)Tools::getValue('p24_pos_id') !== (int)Configuration::get('P24_SHOP_ID' . $suffix)) {
            return false;
        } elseif ((string)Tools::getValue('p24_amount') !== (string)$totalAmount) {
            return false;
        } elseif (Tools::getValue('p24_currency') !== $currency->iso_code) {
            return false;
        } elseif (!$this->checkSign($totalAmount, $currency, $suffix)) {
            return false;
        }

        $transactionService = Przelewy24RestTransactionInterfaceFactory::buildForSuffix($suffix);
        $payload = new Przelewy24PayloadForRestTransactionVerify();
        $payload->merchantId = (int)Configuration::get('P24_MERCHANT_ID' . $suffix);
        $payload->posId = (int)Configuration::get('P24_SHOP_ID' . $suffix);
        $payload->sessionId = (string)Tools::getValue('p24_session_id');
        $payload->amount = (int)$totalAmount;
        $payload->currency = (string)$currency->iso_code;
        $payload->orderId = (int)Tools::getValue('p24_order_id');

        $verified = $transactionService->verify($payload);
        PrestaShopLogger::addLog('postProcess trnVerify' . var_export($verified, true), 1);

        return $verified;
    }

    /**
     * Check sign.
     *
     * @param $totalAmount
     * @param Currency $currency
     * @param $suffix
     * @return array
     */
    private function checkSign($totalAmount, Currency $currency, $suffix)
    {
        $expectedSign = md5(
            Tools::getValue('p24_session_id') .
            '|' .
            Tools::getValue('p24_order_id') .
            '|' .
            $totalAmount .
            '|' .
            $currency->iso_code .
            '|' .
            Configuration::get('P24_SALT' . $suffix)
        );
        $sign = Tools::getValue('p24_sign');

        return $expectedSign == $sign;
    }
}
