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
class CmsController extends CmsControllerCore
{
    public function init()
    {
        if (Configuration::get('purls_cms') == 1)
        {
            //SET CMS PATTERN
            $link_pattern = Tools::safeOutput(urldecode(Tools::getValue('cms_rewrite')));
            $cms_pattern = '/.*?content\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)/';
            preg_match($cms_pattern, $_SERVER['REQUEST_URI'], $Array);
            if (isset($Array[2]) && $Array[2] != "")
            {
                $link_pattern = $Array[2];
            }
            //SET CMS CATEGORY PATTERN
            $cms_category_link_pattern = Tools::safeOutput(urldecode(Tools::getValue('cms_category_rewrite')));
            $cms_cat_pattern = '/.*?content\/category\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)/';
            preg_match($cms_cat_pattern, $_SERVER['REQUEST_URI'], $urlCatArray);
            if (isset($urlCatArray[2]) && $urlCatArray[2] != "")
            {
                $cms_category_link_pattern = $urlCatArray[2];
            }
            if ($link_pattern)
            {
                $sql = "SELECT tl.id_cms FROM " . _DB_PREFIX_ . "cms_lang tl LEFT OUTER JOIN " . _DB_PREFIX_ . "cms_shop t ON (t.id_cms = tl.id_cms) WHERE tl.link_rewrite='" . psql($link_pattern) . "' AND tl.id_lang=" . (int)$this->context->language->id . " AND t.id_shop=" . (int)$this->context->shop->id;
                $id_cms = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_cms != "")
                {
                    //$_POST['id_cms'] = $id_cms;
                    $_GET['id_cms'] = $id_cms;
                    $_GET['cms_rewrite'] = '';
                }
            }
            elseif ($cms_category_link_pattern)
            {
                $sql = "SELECT id_cms_category FROM " . _DB_PREFIX_ . "cms_category_lang WHERE link_rewrite='" . psql($cms_category_link_pattern) . "' AND id_lang=" . (int)$this->context->language->id;
                $id_cms_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_cms_category != "")
                {
                    //$_POST['id_cms_category'] = $id_cms_category;
                    $_GET['id_cms_category'] = $id_cms_category;
                    $_GET['cms_category_rewrite'] = '';
                }
            }
            parent::init();
        }
        else
        {
            parent::init();
        }
    }
}