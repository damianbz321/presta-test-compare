<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

class PushOnCartAddToCartModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $token = pSQL(Configuration::get('PUSHONCART_TOKEN'));
        $token_post = pSQL(Tools::getValue('token'));

        if ($token_post != $token) {
            exit('fail');
        }

        $pushoncart = $this->module;
        $product = new Product((int)Tools::getValue('product'));
        $attributes = Tools::getValue('pushoncart_product_attribute');

        if ((int)!empty($attributes)) {
            $count_comparer = 0;

            /* Getting attribute combination */
            $query_attribute = 'SELECT distinct(pac'.$count_comparer.'.id_product_attribute) FROM '._DB_PREFIX_.'product_attribute_combination as pac'.$count_comparer.' JOIN ';

            foreach ($attributes as $key => $attribute) {
                $count_comparer++;
                $query_attribute .= _DB_PREFIX_.'product_attribute_combination as pac'.$count_comparer;
                $query_attribute .= ' ON pac'.$count_comparer.'.id_product_attribute = pac'.($count_comparer - 1).'.id_product_attribute JOIN ';
            }
            $count_comparer = 0;
            $query_attribute = rtrim($query_attribute, ' JOIN');

            $query_attribute .= ' JOIN '._DB_PREFIX_.'product_attribute AS pa ON pac0.id_product_attribute = pa.id_product_attribute ';
            $query_attribute .= ' WHERE';

            foreach ($attributes as $attribute) {
                $query_attribute .= ' pac'.$count_comparer.'.id_attribute = '.$attribute.' AND';
                $count_comparer++;
            }

            $query_attribute .= ' pa.id_product = '.(int)Tools::getValue('product');
            $chosen_attribute = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_attribute);
        } else {
            $chosen_attribute = $product->getDefaultAttribute($product->id);
        }
        /* End of getting attribute combination */

        $context = Context::getContext();
        $cart = new Cart((int)$context->cart->id);

        if (Validate::isLoadedObject($cart)) {
            $id_cart_rule = (int)Tools::getValue('id_cart_rule');
            $cart_rule = new CartRule($id_cart_rule);

            if (Validate::isLoadedObject($cart_rule)) {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
                $update = $cart_rule->update();

                $attributes = Tools::getValue('pushoncart_product_attributes');
                $result_update_cart = $cart->updateQty(1, $product->id, $chosen_attribute, false, 'up', 0, null, false);
                if ($result_update_cart == 1) {
                    $result_add_cart_rule_to_cart = $cart->addCartRule($id_cart_rule);
                } else {
                    $cart->removeCartRule($id_cart_rule);
                }
            } else {
                Tools::displayError('Error loading cart_rule in AddToCart.php');
            }
        } else {
            Tools::displayError('Error loading cart in AddToCart.php');
        }

        if ($pushoncart->ps17) {
            $link = $context->link->getPageLink(
                'cart',
                null,
                $context->language->id,
                ['action' => 'show']
            );
            Tools::redirect($link);
        } else {
            Tools::redirect('order');
        }
    }
}
