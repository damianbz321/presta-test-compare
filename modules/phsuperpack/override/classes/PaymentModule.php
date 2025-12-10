<?php
use PrestaShop\PrestaShop\Adapter\StockManager;

abstract class PaymentModule extends PaymentModuleCore
{
    public function validateOrder(
        $id_cart,
        $id_order_state,
        $amount_paid,
        $payment_method = 'Unknown',
        $message = null,
        $extra_vars = [],
        $currency_special = null,
        $dont_touch_amount = false,
        $secure_key = false,
        ?Shop $shop = null,
        ?string $order_reference = null
    ) {
        $unzip = (int)Configuration::get('PH_PACK_UNZIP');
        if ($unzip == 1)
        {
            if (!isset($this->context)) {
                $this->context = Context::getContext();
            }
            $this->context->cart = new Cart((int)$id_cart);

            $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
            $success = true;
            $products = $this->context->cart->getProducts();
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
            $modify = false;
            $price_total_pack = 0;
            $price_total = 0;

            $item_id = 0;
            $item_id_attribute = 0;

            foreach ($products as $product) {

                if (!Pack::isPack($product['id_product']))
                {
                    continue;
                }

                $v = $this->context->cart->deleteProduct((int) $product['id_product'], (int) $product['id_product_attribute']);
                if ($v)
                {

                    $price_total_pack = $product['total'] / $product["quantity"]; // single pack

                    $items = Pack::getItemTable((int)$product['id_product'], $lang_id, true);
                    foreach ($items as $item)
                    {
                        $item_id = (int)$item['id_product'];
                        $item_id_attribute = (int)$item['id_product_attribute'];
                        if ($item_id > 0) {



                            $pr = new Product($item_id);
                            if ($item_id_attribute > 0)
                                $price = $pr->getPrice(false, $item_id_attribute, 6);
                            else
                                $price = $pr->getPrice(false, null, 6);

                            $price_total += $price * $item['pack_quantity'];
                            $price_total_without_qty += $price;

                            $success &= $this->context->cart->updateQty(
                                (int) $item['pack_quantity'] * $product["quantity"],
                                (int) $item_id,
                                (int) $item_id_attribute,
                                false,
                                'up',
                                0,
                                null,
                                false,
                                false
                            );

                            $modify = true;
                        }
                    }

                    if ($price_total > $price_total_pack)
                    {
                        $new_price_without_tax = $price_total - $price_total_pack;

                        foreach ($items as $item)
                        {
                            $item_id = (int)$item['id_product'];
                            $item_id_attribute = (int)$item['id_product_attribute'];
                            if ($item_id > 0) {

                                $pr = new Product($item_id);
                                if ($item_id_attribute > 0)
                                    $price = $pr->getPrice(false, $item_id_attribute, 6);
                                else
                                    $price = $pr->getPrice(false, null, 6);

                                $break_now = false;
                                $new_price = 0;
                                if ($new_price_without_tax < $price)
                                {
                                    $new_price = $price - $new_price_without_tax;
                                    $break_now = true;
                                }
                                else if ($new_price_without_tax > $price)
                                {
                                    $new_price_without_tax = $new_price_without_tax - $price;
                                    $new_price = 0;
                                }

                                $specific_price = new SpecificPrice();
                                $specific_price->id_product = $item_id;
                                $specific_price->id_product_attribute = $item_id_attribute;
                                $specific_price->id_cart = (int)$id_cart;
                                $specific_price->from_quantity = 1;
                                // $specific_price->from_quantity = $item['pack_quantity'] * $product["quantity"];
                                $specific_price->price = $new_price;
                                $specific_price->reduction_type = 'amount';
                                $specific_price->reduction_tax = 1;
                                $specific_price->reduction = 0;
                                $specific_price->id_shop = $this->context->cart->id_shop;
                                $specific_price->id_shop_group = 0;
                                $specific_price->id_currency = 0;
                                $specific_price->id_country = 0;
                                $specific_price->id_group = 0;
                                $specific_price->id_customer = (int)$this->context->customer->id;
                                $specific_price->from = "0000-00-00 00:00:00";
                                $specific_price->to = "0000-00-00 00:00:00";
                                $specific_price->add();

                                if ($break_now || $new_price == 0)
                                {
                                    break;
                                }
                            }
                        }
                    }

                    $price_total_pack = 0;
                    $price_total = 0;
                    $price_total_without_qty = 0;
                }
            }

            if ($modify)
            {
                $this->context->cart->update();
            }
            $this->context->cart = new Cart((int)$id_cart);
            $package_list = $this->context->cart->getPackageList(true);
        }

        $result = parent::validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, $currency_special, $dont_touch_amount,$secure_key, $shop);
        return $result;
    }
}