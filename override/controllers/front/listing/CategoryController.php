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
class CategoryController extends CategoryControllerCore
{
    /*
    * module: purls
    * date: 2022-12-01 11:37:20
    * version: 2.9.2
    */
    protected function setRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI']))
        {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        }
        elseif (isset($_SERVER['HTTP_X_REWRITE_URL']))
        {
            $this->request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        $this->request_uri = rawurldecode($this->request_uri);
        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop))
        {
            $this->request_uri = preg_replace('#^' . preg_quote(Context::getContext()->shop->getBaseURI(), '#') . '#i', '/', $this->request_uri);
        }
        if (Language::isMultiLanguageActivated())
        {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $this->request_uri, $m))
            {
                $_GET['isolang'] = $m[1];
                $this->request_uri = substr($this->request_uri, 3);
            }
        }
        $this->request_uri = strtok($this->request_uri, '?');
    }
    /*
    * module: purls
    * date: 2022-12-01 11:37:20
    * version: 2.9.2
    */
    public function init()
    {
        if (Configuration::get('purls_categories') == 1)
        {
            $link_pattern = Tools::safeOutput(urldecode(Tools::getValue('category_rewrite')));
            $this->setRequestUri();
            if ($link_pattern)
            {
                $categories = explode("/", trim(($this->request_uri ? $this->request_uri : $_SERVER['REQUEST_URI']), "/"));
                $categories_count = count($categories);
                $where_clause = '';
                $shop_association = 'AND cs.id_shop=' . Context::getContext()->shop->id;
                if ($categories_count > 2)
                {
                    $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 '.$shop_association.' AND cll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 2])) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ' AND cc.`id_parent` IN
                            (SELECT clll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` clll
                            INNER JOIN `' . _DB_PREFIX_ . 'category` ccc ON (ccc.id_category = clll.id_category)
                            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` css ON (ccc.id_category = css.id_category)
                            WHERE 1 '.$shop_association.' AND clll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 3])) . '\' AND clll.`id_lang` = ' . Context::getContext()->language->id . ')
                        )';
                }
                else if ($categories_count > 1)
                {
                    $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 '.$shop_association.' AND cll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 2])) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ')';
                }
                $sql = 'SELECT c.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cl
                    INNER JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cl.id_category)
                    INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (c.id_category = cs.id_category)
    				WHERE 1 '.$shop_association.' AND cl.`link_rewrite` = \'' . str_replace('.html', '', psql($link_pattern)) . '\' AND cl.`id_lang` = ' . Context::getContext()->language->id . ' ' . $where_clause;
                $id_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_category != "")
                {
                    $_GET['id_category'] = $id_category;
                    $_GET['category_rewrite'] = '';
                }
                else
                {
                }
            }
            else
            {
            }
            parent::init();
        }
        else
        {
            parent::init();
        }
    }
}