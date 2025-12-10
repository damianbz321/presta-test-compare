<?php

class Product extends ProductCore
{

    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    )
    {
        $price = parent::priceCalculation(
            $id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
            $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, $specific_price,
            $use_group_reduction, $id_customer, $use_customer_price, $id_cart, $real_quantity, $id_customization
        );
        Hook::exec('actionProductPriceCalculationAfter', [
            'price' => &$price,
            'args' => [
                'id_shop' => $id_shop,
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_country' => $id_country,
                'id_state' => $id_state,
                'zipcode' => $zipcode,
                'id_currency' => $id_currency,
                'id_group' => $id_group,
                'quantity' => $quantity,
                'use_tax' => $use_tax,
                'decimals' => $decimals,
                'only_reduc' => $only_reduc,
                'use_reduc' => $use_reduc,
                'with_ecotax' => $with_ecotax,
                'specific_price' => $specific_price,
                'use_group_reduction' => $use_group_reduction,
                'id_customer' => $id_customer,
                'use_customer_price' => $use_customer_price,
                'id_cart' => $id_cart,
                'real_quantity' => $real_quantity,
                'id_customization' => $id_customization,
            ],
        ]);
        return $price;
    }
}
