<?php

class Product extends ProductCore {
	/*
    * module: amproductfields
    * date: 2021-12-22 23:07:55
    * version: 1.0
    */
    public $custom_field;
	/*
    * module: amproductfields
    * date: 2021-12-22 23:07:55
    * version: 1.0
    */
    public $custom_field_lang;
	/*
    * module: amproductfields
    * date: 2021-12-22 23:07:55
    * version: 1.0
    */
    public $custom_field_lang_wysiwyg;

    public $test = 'ok';

	/*
    * module: amproductfields
    * date: 2021-12-22 23:07:55
    * version: 1.0
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null){


        if(isset($_POST['custom_field_lang_wysiwyg_']))
        {
            $update = "UPDATE " . _DB_PREFIX_ . "product_lang set custom_field_lang_wysiwyg='{$_POST['custom_field_lang_wysiwyg_']}' where id_product='{$_POST['form']['id_product']}'";
            Db::getInstance()->execute($update);
        }

			self::$definition['fields']['custom_field'] = [
	            'type' => self::TYPE_STRING,
	            'required' => false, 'size' => 255
	        ];
	        self::$definition['fields']['custom_field_lang'] = [
	            'type' => self::TYPE_STRING,
	            'lang' => true,
	            'required' => false, 'size' => 255
	        ];
	        self::$definition['fields']['custom_field_lang_wysiwyg'] = [
	            'type' => self::TYPE_HTML,
	            'lang' => true,
	            'required' => false,
	            'validate' => 'isCleanHtml'
	        ];
	        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
	}
    /*
    * module: pshowupsell
    * date: 2022-02-03 08:01:12
    * version: 1.1.0
    */
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
