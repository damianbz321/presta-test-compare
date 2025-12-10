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
class AdminProductsController extends AdminProductsControllerCore
{
    /*
    * module: purls
    * date: 2022-12-01 11:37:18
    * version: 2.9.2
    */
    public function getPreviewUrl(Product $product)
    {
        $id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);
        if (!ShopUrl::getMainShopDomain())
        {
            return false;
        }
        $is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        $preview_url = $this->context->link->getProductLink($product, $this->getFieldValue($product, 'link_rewrite', $id_lang), Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $id_lang), null, $id_lang, (int)Context::getContext()->shop->id, 0, $is_rewrite_active);
        if (!$product->active)
        {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $preview_url .= ((strpos($preview_url, '?') === false) ? '?' : '&') . 'adtoken=' . $this->token . '&ad=' . $admin_dir . '&id_employee=' . (int)$this->context->employee->id;
        }
        return $preview_url;
    }
}
?>