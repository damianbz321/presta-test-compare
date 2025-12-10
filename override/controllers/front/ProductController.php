<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
class ProductController extends ProductControllerCore
{
    /*
    * module: purls
    * date: 2022-12-01 11:37:19
    * version: 2.9.2
    */
    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1)
        {
            return $exp[1];
        }
        if ($part == 2)
        {
            return $exp[2];
        }
        if ($part == 3)
        {
            return $exp[3];
        }
    }
    /*
    * module: purls
    * date: 2022-12-01 11:37:19
    * version: 2.9.2
    */
    public function getTemplateVarProduct()
    {
        if ($this->psversion(1) == 7 && $this->psversion(2) >= 4 && $this->psversion(3) >= 0)
        {
            return parent::getTemplateVarProduct();
        }
        $productSettings = $this->getProductPresentationSettings();
        $extraContentFinder = new ProductExtraContentFinder();
        $product = $this->objectPresenter->present($this->product);
        $product['id_product'] = (int)$this->product->id;
        $product['out_of_stock'] = (int)$this->product->out_of_stock;
        $product['new'] = (int)$this->product->new;
        if (Tools::getValue('idpa', 'false') != 'false')
        {
            $product['id_product_attribute'] = (int)Tools::getValue('idpa');
        }
        else
        {
            $product['id_product_attribute'] = $this->getIdProductAttribute();
        }
        $product['minimal_quantity'] = $this->getProductMinimalQuantity($product);
        $product['quantity_wanted'] = $this->getRequiredQuantity($product);
        $product['extraContent'] = $extraContentFinder->addParams(array('product' => $this->product))->present();
        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);
        $product_full = $this->addProductCustomizationData($product_full);
        $product_full['show_quantities'] = (bool)(Configuration::get('PS_DISPLAY_QTIES') && Configuration::get('PS_STOCK_MANAGEMENT') && $this->product->quantity > 0 && $this->product->available_for_order && !Configuration::isCatalogMode());
        $product_full['quantity_label'] = ($this->product->quantity > 1) ? $this->trans('Items', array(), 'Shop.Theme.Catalog') : $this->trans('Item', array(), 'Shop.Theme.Catalog');
        $product_full['quantity_discounts'] = $this->quantity_discounts;
        if ($product_full['unit_price_ratio'] > 0)
        {
            $unitPrice = ($productSettings->include_taxes) ? $product_full['price'] : $product_full['price_tax_exc'];
            $product_full['unit_price'] = $unitPrice / $product_full['unit_price_ratio'];
        }
        $group_reduction = GroupReduction::getValueForProduct($this->product->id, (int)Group::getCurrent()->id);
        if ($group_reduction === false)
        {
            $group_reduction = Group::getReduction((int)$this->context->cookie->id_customer) / 100;
        }
        $product_full['customer_group_discount'] = $group_reduction;
        $presenter = $this->getProductPresenter();
        return $presenter->present($productSettings, $product_full, $this->context->language);
    }
    /*
    * module: purls
    * date: 2022-12-01 11:37:19
    * version: 2.9.2
    */
    private function getIdProductAttribute()
    {
        if ($this->psversion(1) == 7 && $this->psversion(2) >= 4 && $this->psversion(3) >= 0)
        {
            return parent::getIdProductAttribute();
        }
        if (Tools::getValue('idpa', 'false') != 'false')
        {
            $requestedIdProductAttribute = (int)Tools::getValue('idpa');
        }
        else
        {
            $requestedIdProductAttribute = (int)Product::getDefaultAttribute($this->product->id);
        }
        if (!Configuration::get('PS_DISP_UNAVAILABLE_ATTR'))
        {
            $productAttributes = array_filter($this->product->getAttributeCombinations(), function ($elem)
            {
                return $elem['quantity'] > 0;
            });
            $productAttribute = array_filter($productAttributes, function ($elem) use ($requestedIdProductAttribute)
            {
                return $elem['id_product_attribute'] == $requestedIdProductAttribute;
            });
            if (empty($productAttribute) && !empty($productAttributes))
            {
                return (int)array_shift($productAttributes)['id_product_attribute'];
            }
        }
        return $requestedIdProductAttribute;
    }
    /*
    * module: purls
    * date: 2022-12-01 11:37:19
    * version: 2.9.2
    */
    protected function assignPriceAndTax()
    {
        if ($this->psversion(1) == 7 && $this->psversion(2) >= 4 && $this->psversion(3) >= 0)
        {
            return parent::assignPriceAndTax();
        }
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
        $id_group = (int)Group::getCurrent()->id;
        $id_country = $id_customer ? (int)Customer::getCurrentCountry($id_customer) : (int)Tools::getCountry();
        $tax = (float)$this->product->getTaxesRate(new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->context->smarty->assign('tax_rate', $tax);
        $product_price_with_tax = Product::getPriceStatic($this->product->id, true, null, 6);
        if (Product::$_taxCalculationMethod == PS_TAX_INC)
        {
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        }
        $id_currency = (int)$this->context->cookie->id_currency;
        $id_product = (int)$this->product->id;
        $id_product_attribute = Tools::getValue('id_product_attribute', null);
        $id_shop = $this->context->shop->id;
        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, false, (int)$this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount)
        {
            if ($quantity_discount['id_product_attribute'])
            {
                $combination = new Combination((int)$quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int)$this->context->language->id);
                foreach ($attributes as $attribute)
                {
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int)$quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount')
            {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }
        $product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
        $this->quantity_discounts = $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float)$tax, $this->product->ecotax);
        $this->context->smarty->assign(array(
            'no_tax' => Tax::excludeTaxeOption() || !$tax,
            'tax_enabled' => Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ));
    }
}