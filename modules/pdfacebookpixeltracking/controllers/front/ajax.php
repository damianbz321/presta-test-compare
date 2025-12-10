<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Pixel Tracking © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Pixel Tracking PrestaShop 1.5.x and 1.6.x Module
* @version   1.1.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      24-05-2016
*/

class PdFacebookPixelTrackingAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    
    public function displayAjax()
    {
        $module = new PdFacebookPixelTracking();

        if (Tools::getValue('secure_key') == $module->secure_key) {
            $id_product = Tools::getValue('product_id');
            $groups = Tools::getValue('attributes_groups');
            $action = Tools::getValue('action');

            if ($action == 'updateCart') {
                $id_product_attribute = Tools::getValue('product_id_product_attribute');
                $id_lang = Context::getContext()->language->id;
                $product = new Product((int)$id_product, false, $id_lang);
                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 2);
                $content_ctegory = $module->getCategoryPath($product->id_category_default);
                $content_ids = $module->getProductIdStringByType($product, $id_product_attribute);
                $data = array(
                    'content_ids' => $content_ids,
                    'content_category' => $content_ctegory,
                    'content_name' => $product->name,
                    'content_value' => $price,
                );
                die(Tools::jsonEncode($data));

            } elseif ($action == 'updateProduct') {

                $id_product_attribute = (int)Product::getIdProductAttributeByIdAttributes((int)$id_product, $groups);
                $id_lang = Context::getContext()->language->id;
                $product = new Product((int)$id_product, false, $id_lang);
                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 2);
                $content_ctegory = $module->getCategoryPath($product->id_category_default);
                $content_ids = $module->getProductIdStringByType($product, $id_product_attribute);
                $data = array(
                    'content_ids' => $content_ids,
                    'content_category' => $content_ctegory,
                    'content_name' => $product->name,
                    'content_value' => $price,
                );
                die(Tools::jsonEncode($data));
            }
        }
    }
}
