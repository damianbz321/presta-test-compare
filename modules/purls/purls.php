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
class purls extends Module
{
    public function __construct()
    {
        @ini_set("display_errors", 0);
        @error_reporting(0); //E_ALL
        $this->bootstrap = true;
        $this->name = 'purls';
        $this->version = '2.9.2';
        $this->author = 'MyPresta.eu';
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = 1;
        $this->mypresta_link = 'https://mypresta.eu/modules/seo/pretty-clean-urls-pro.html';
        $this->tab = 'seo';
        $this->displayName = $this->l('Pretty Clean URLs');
        $this->description = $this->l('This module generates clean and pretty looking urls in your online store. It increases SEO value of the store.');
        parent::__construct();
        $this->checkforupdates();
        $this->checkIfOverrideDirExists();
    }

    private function checkIfOverrideDirExists()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            if (file_exists('../override/controllers/front/')) {
                if (!file_exists('../override/controllers/front/listing')) {
                    mkdir('../override/controllers/front/listing', 0777, true);
                }
            }
        }
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        //for update feature purposes
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = purlsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (purlsUpdate::version($this->version) < purlsUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = purlsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (purlsUpdate::version($this->version) < purlsUpdate::version(purlsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function makeUnique($type, $id_lang, $id_product = false)
    {
        $all_langauges = Language::getLanguages(true);
        if ($type == 'false' or $id_lang == 'false') {
            return;
        }

        if ($type == 'categories') {
            $collisions = $this->getAllCategoryCollisions($id_lang);
            foreach ($collisions as $collision) {
                $categories = $this->getAllCategoryCollisionsRewrite($collision['link_rewrite'], $id_lang);
                $i = 0;
                foreach ($categories as $cat_collision) {
                    $category = new Category($cat_collision['id_category'], null, $this->context->shop->id);
                    if ($i != 0) {
                        foreach ($all_langauges as $language) {
                            if ($language['id_lang'] == $id_lang) {
                                $category->link_rewrite[$id_lang] = $collision['link_rewrite'] . '-' . $i;
                            }
                        }
                    }
                    $i++;
                    $category->save();
                }
            }
        }
        if ($type == 'products') {
            $collisions = $this->getAllProductCollisions($id_lang, $id_product);
            foreach ($collisions as $collision) {
                $products = $this->getAllProductCollisionsRewrite($collision['link_rewrite'], $id_lang);
                $i = 0;
                foreach ($products as $prod_collision) {
                    $product = new Product($prod_collision['id_product'], false, null, $this->context->shop->id);
                    if ($i != 0) {
                        foreach ($all_langauges as $language) {
                            if ($language['id_lang'] == $id_lang) {
                                $product->link_rewrite[$id_lang] = $collision['link_rewrite'] . '-' . $i;
                            }
                        }
                    }
                    $i++;
                    $product->save();
                }
            }
        }
    }

    public function getContent()
    {
        if (Configuration::get('PURLS_ROUTE_cms_rule', $this->context->language->id) == false OR Configuration::get('PURLS_ROUTE_cms_rule', $this->context->language->id) == '') {
            $array = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $array[$lang['id_lang']] = 'info/{rewrite}.html';
                Configuration::updateValue('PURLS_ROUTE_cms_rule', $array);
            }
        }
        if (Configuration::get('PURLS_ROUTE_cms_category_rule', $this->context->language->id) == false OR Configuration::get('PURLS_ROUTE_cms_category_rule', $this->context->language->id) == '') {
            $array = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $array[$lang['id_lang']] = 'info/{rewrite}';
                Configuration::updateValue('PURLS_ROUTE_cms_category_rule', $array);
            }
        }
        if (Configuration::get('PURLS_ROUTE_supplier_rule', $this->context->language->id) == false OR Configuration::get('PURLS_ROUTE_supplier_rule', $this->context->language->id) == '') {
            $array = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $array[$lang['id_lang']] = 'supplier/{rewrite}/';
                Configuration::updateValue('PURLS_ROUTE_supplier_rule', $array);
            }
        }
        if (Configuration::get('PURLS_ROUTE_manufacturer_rule', $this->context->language->id) == false OR Configuration::get('PURLS_ROUTE_manufacturer_rule', $this->context->language->id) == '') {
            $array = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $array[$lang['id_lang']] = 'manufacturer/{rewrite}/';
                Configuration::updateValue('PURLS_ROUTE_manufacturer_rule', $array);
            }
        }

        if (Configuration::get('PS_ROUTE_category_rule') == false OR Configuration::get('PS_ROUTE_category_rule') == '') {
            $array = '{parent_categories:/}{rewrite}/';
            Configuration::updateValue('PS_ROUTE_category_rule', $array);
        }
        if (Configuration::get('PS_ROUTE_product_rule') == false OR Configuration::get('PS_ROUTE_product_rule') == '') {
            $array = '{category:/}{rewrite}.html';
            Configuration::updateValue('PS_ROUTE_product_rule', $array);
        }

        if (Tools::getValue('AjaxCollisions', 'false') != 'false' && Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('id_lang', 'false') != 'false') {
            $this->makeUnique('products', Tools::getValue('id_lang'), Tools::getValue('id_product'));
        }

        if (Tools::getValue('makeUnique', 'false') != 'false' && Tools::getValue('makeUniqueLanguage', 'false') != 'false') {
            $this->makeUnique(Tools::getValue('makeUnique', 'false'), Tools::getValue('makeUniqueLanguage', 'false'));
        }
        $langs = Language::getLanguages();
        $langs = count($langs);
        //echo '<pre>'; print_r($compare_urls); exit;
        if (Tools::getValue('product_rewrite', 'false') != 'false') {
            $this->context->smarty->assign('exact_coll', purls::getAllProductCollisionsRewrite(Tools::getValue('product_rewrite'), Tools::getValue('id_lang', null)));
        }

        if (Tools::getValue('category_rewrite', 'false') != 'false') {
            $this->context->smarty->assign('exact_coll_cat', purls::getAllCategoryCollisionsRewrite(Tools::getValue('category_rewrite'), Tools::getValue('id_lang', null)));
        }

        $this->context->smarty->assign(array(
            'langs_active' => (int)$langs,
            'purls' => $this,
            'purls_url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . Tools::getValue('token')
        ));
        $cronurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri() . 'cronjob.php?key=' . $this->secure_key;
        $this->context->smarty->assign('cron_purls', $cronurl);

        $code_collisions = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'purls/views/purls.tpl');


        $output = '';
        $errors = array();
        if (Tools::isSubmit('submitCategoryFeatured')) {
            $array = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $array['PURLS_ROUTE_cms_category_rule'][$lang['id_lang']] = Tools::getValue('PURLS_ROUTE_cms_category_rule_' . $lang['id_lang']);
                $array['PURLS_ROUTE_cms_rule'][$lang['id_lang']] = Tools::getValue('PURLS_ROUTE_cms_rule_' . $lang['id_lang']);
                $array['PURLS_ROUTE_supplier_rule'][$lang['id_lang']] = Tools::getValue('PURLS_ROUTE_supplier_rule_' . $lang['id_lang']);
                $array['PURLS_ROUTE_manufacturer_rule'][$lang['id_lang']] = Tools::getValue('PURLS_ROUTE_manufacturer_rule_' . $lang['id_lang']);
            }
            Configuration::updateValue('purls_product_hash', Tools::getValue('purls_product_hash'));
            Configuration::updateValue('PURLS_ROUTE_cms_category_rule', $array['PURLS_ROUTE_cms_category_rule']);
            Configuration::updateValue('PURLS_ROUTE_cms_rule', $array['PURLS_ROUTE_cms_rule']);
            Configuration::updateValue('PURLS_ROUTE_supplier_rule', $array['PURLS_ROUTE_supplier_rule']);
            Configuration::updateValue('PURLS_ROUTE_manufacturer_rule', $array['PURLS_ROUTE_manufacturer_rule']);
            Configuration::updateValue('PS_ROUTE_category_rule', Tools::getValue('PS_ROUTE_category_rule'));
            Configuration::updateValue('PS_ROUTE_product_rule', Tools::getValue('PS_ROUTE_product_rule'));


            Configuration::updateValue('purls_products', Tools::getValue('purls_products'));
            Configuration::updateValue('purls_categories', Tools::getValue('purls_categories'));
            Configuration::updateValue('purls_manufacturers', Tools::getValue('purls_manufacturers'));
            Configuration::updateValue('purls_suppliers', Tools::getValue('purls_suppliers'));
            Configuration::updateValue('purls_cms', Tools::getValue('purls_cms'));
            Configuration::updateValue('purls_301', Tools::getValue('purls_301'));
            if (isset($errors) && count($errors)) {
                $output .= $this->displayError(implode('<br />', $errors));
            } else {
                $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
            }
        }

        return "<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement(\"iframe\");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src=\"javascript:false\",r.title=\"\",r.role=\"presentation\",(r.frameElement||r).style.cssText=\"display: none\",d=document.getElementsByTagName(\"script\"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain=\"'+n+'\";void(0);',o=s}o.open()._l=function(){var o=this.createElement(\"script\");n&&(this.domain=n),o.id=\"js-iframe-async\",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload=\"document._l();\">'),o.close()}(\"//assets.zendesk.com/embeddable_framework/main.js\",\"prestasupport.zendesk.com\");/*]]>*/</script>" . $output . $this->renderForm() . $code_collisions;
    }

    public function getConfigFieldsValues()
    {
        $array = array();
        foreach (Language::getLanguages(false) AS $lang) {
            $array['PURLS_ROUTE_cms_category_rule'][$lang['id_lang']] = Configuration::get('PURLS_ROUTE_cms_category_rule', $lang['id_lang']);
            $array['PURLS_ROUTE_cms_rule'][$lang['id_lang']] = Configuration::get('PURLS_ROUTE_cms_rule', $lang['id_lang']);
            $array['PURLS_ROUTE_supplier_rule'][$lang['id_lang']] = Configuration::get('PURLS_ROUTE_supplier_rule', $lang['id_lang']);
            $array['PURLS_ROUTE_manufacturer_rule'][$lang['id_lang']] = Configuration::get('PURLS_ROUTE_manufacturer_rule', $lang['id_lang']);
        }

        return array_merge($array, array(
            'purls_product_hash' => Tools::getValue('purls_product_hash', Configuration::get('purls_product_hash')),
            'PS_ROUTE_category_rule' => Tools::getValue('PS_ROUTE_category_rule', Configuration::get('PS_ROUTE_category_rule')),
            'PS_ROUTE_product_rule' => Tools::getValue('PS_ROUTE_product_rule', Configuration::get('PS_ROUTE_product_rule')),
            'purls_products' => Tools::getValue('purls_products', Configuration::get('purls_products')),
            'purls_categories' => Tools::getValue('purls_categories', Configuration::get('purls_categories')),
            'purls_manufacturers' => Tools::getValue('purls_manufacturers', Configuration::get('purls_manufacturers')),
            'purls_suppliers' => Tools::getValue('purls_suppliers', Configuration::get('purls_suppliers')),
            'purls_cms' => Tools::getValue('purls_cms', Configuration::get('purls_cms')),
            'purls_301' => Tools::getValue('purls_301', Configuration::get('purls_301')),
        ));
    }

    public function renderForm()
    {
        if (Configuration::get('purls_products') == 0) {
            if (strpos(Configuration::get('PS_ROUTE_product_rule'), "{id}") == false) {
                $this->context->controller->errors[] = $this->l('Your product rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of product pages');
            }
            if (strpos(Configuration::get('PS_ROUTE_product_rule'), '{-:id_product_attribute}') == false) {
                $this->context->controller->errors[] = $this->l('Your product rewrite url does not contain {-:id_product_attribute} variable. It is required when you do not want to remove ID of product attribute from URL of product pages');
            }
        }
        if (Configuration::get('purls_categories') == 0) {
            if (strpos(Configuration::get('PS_ROUTE_category_rule'), '{id}') == false) {
                $this->context->controller->errors[] = $this->l('Your category rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of category pages');
            }
        }
        if (Configuration::get('purls_manufacturers') == 0) {
            if (strpos(Configuration::get('PS_ROUTE_manufacturer_rule'), '{id}') == false) {
                $this->context->controller->errors[] = $this->l('Your manufacturer rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of manufacturer pages');
            }
        }
        if (Configuration::get('purls_suppliers') == 0) {
            if (strpos(Configuration::get('PS_ROUTE_supplier_rule'), '{id}') == false) {
                $this->context->controller->errors[] = $this->l('Your supplier rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of supplier pages');
            }
        }

        if (Configuration::get('purls_cms') == 0) {
            if (strpos(Configuration::get('PS_ROUTE_cms_category_rule'), '{id}') == false) {
                $this->context->controller->errors[] = $this->l('Your cms category rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of supplier pages');
            }
            if (strpos(Configuration::get('PS_ROUTE_cms_rule'), '{id}') == false) {
                $this->context->controller->errors[] = $this->l('Your cms rewrite url does not contain {id} variable. It is required when you do not want to remove ID from URL of supplier pages');
            }
        }


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Select from what kind of URLs you want to remove ID'),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Product ID'),
                        'name' => 'purls_products',
                        'desc' => '<div class="alert alert-info">' . $this->l('Decide what ID of product you want to remove') . '</div><div class="alert alert-warning">' . $this->l('If you want to remove:') . '<br/>' .
                            '<strong>' . $this->l('ID of product + id of attribute') . '</strong> ' . $this->l('remember to configure schema of urls and remove {id} and {id_product_attribute} from "Route to products"') . ' <br/>' .
                            '<strong>' . $this->l('ID of attribute only') . '</strong> ' . $this->l('remember to configure schema of urls and remove {id_product_attribute} from "Route to products"') . ' <br/>' .
                            '<strong>' . $this->l('ID of product only') . '</strong> ' . $this->l('remember to configure schema of urls and remove {id} from "Route to products". As an Id of attribute use') . ' <span class="label label-danger">{id_product_attribute:-}</span><br/>' .
                            $this->l('Details about configuration of "schema of urls" you can find ') . ' <a target="_blank" href="https://mypresta.eu/documentation/pretty-clean-urls/configuration-and-module-usage/schema-of-urls-settings.html">' . $this->l("on documentation page of this module") . '</a>' .
                            '</div>',
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'label' => $this->l('Do not remove ID numbers'),
                                ),
                                array(
                                    'value' => '1',
                                    'label' => $this->l('ID of product + ID of attribute'),
                                ),
                                array(
                                    'value' => '2',
                                    'label' => $this->l('ID of attribute only'),
                                ),
                                array(
                                    'value' => '3',
                                    'label' => $this->l('ID of product only'),
                                ),
                            ),
                            'id' => 'value',
                            'name' => 'label',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hashcode with attributes'),
                        'name' => 'purls_product_hash',
                        'is_bool' => true,
                        'desc' => $this->l('If you want to remove hashcode with attributes from url to product page - activate this option. Option, when disabled will include hashcode with selected attributes to url like: #/size-s/color-white - thanks to this you can point an exact combination to select - without id combination in the url. wow!') . ' ' . $this->l('This option works when id product attribute is removed from url.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Categories'),
                        'name' => 'purls_categories',
                        'is_bool' => true,
                        'desc' => $this->l('Remove ID from category links'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Manufacturers'),
                        'name' => 'purls_manufacturers',
                        'is_bool' => true,
                        'desc' => $this->l('Remove ID from manufacturer links'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Suppliers'),
                        'name' => 'purls_suppliers',
                        'is_bool' => true,
                        'desc' => $this->l('Remove ID from supplier links'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('CMS'),
                        'name' => 'purls_cms',
                        'is_bool' => true,
                        'desc' => $this->l('Remove ID from CMS links'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('301 redirection from old urls'),
                        'name' => 'purls_301',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Turn on auto-redirection from old urls (with ID) to new urls (without ID)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product rule'),
                        'name' => 'PS_ROUTE_product_rule',
                        'lang' => false,
                        'desc' => $this->l('Route to product page.') . ' ' . $this->l('Available keywords: ') . '{rewrite}* , {category} , {categories}, {id}, {-:id_product_attribute}' . ' <span class="label label-info">' . $this->l('This field is also configurable under "schema of urls" section in: Shop parameters > Traffic & SEO') . '</span>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category rule'),
                        'name' => 'PS_ROUTE_category_rule',
                        'lang' => false,
                        'desc' => $this->l('Route to category page.') . ' ' . $this->l('Available keywords: ') . '{id}, {rewrite}* , {parent_categories}' . ' <span class="label label-info">' . $this->l('This field is also configurable under "schema of urls" section in: Shop parameters > Traffic & SEO') . '</span>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Supplier rule'),
                        'name' => 'PURLS_ROUTE_supplier_rule',
                        'lang' => true,
                        'desc' => $this->l('Route to supplier page.') . ' ' . $this->l('Available keywords: ') . '{id}, {rewrite}* <span class="label label-danger">' . (Configuration::get('purls_suppliers') == 0 ? $this->l('To edit this field you need to activate option to remove id from this type of url') : '') . '</span>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Brand rule'),
                        'name' => 'PURLS_ROUTE_manufacturer_rule',
                        'lang' => true,
                        'desc' => $this->l('Route to manufacturer page.') . ' ' . $this->l('Available keywords: ') . '{id}, {rewrite}* <span class="label label-danger">' . (Configuration::get('purls_manufacturers') == 0 ? $this->l('To edit this field you need to activate option to remove id from this type of url') : '') . '</span>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cms page rule'),
                        'name' => 'PURLS_ROUTE_cms_rule',
                        'lang' => true,
                        'desc' => $this->l('Route to cms page.') . ' ' . $this->l('Available keywords: ') . '{categories:/}, {id}, {rewrite}* <span class="label label-danger">' . (Configuration::get('purls_cms') == 0 ? $this->l('To edit this field you need to activate option to remove id from this type of url') : '') . '</span>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cms category page rule'),
                        'name' => 'PURLS_ROUTE_cms_category_rule',
                        'lang' => true,
                        'desc' => $this->l('Route to cms category page.') . ' ' . $this->l('Available keywords: ') . '{id}, {rewrite}* <span class="label label-danger">' . (Configuration::get('purls_cms') == 0 ? $this->l('To edit this field you need to activate option to remove id from this type of url') : '') . '</span>',
                    ),
                ),
                'submit' => array('title' => $this->l('Save'),),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCategoryFeatured';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($fields_form)) . $this->checkforupdates(0, true);
    }

    public function inconsistency($var)
    {
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function install()
    {
        if (!parent:: install() ||
            !$this->registerHook('Header') ||
            !$this->registerHook('ModuleRoutes') ||
            !$this->registerHook('actionAdminControllerSetMedia')) {
            return false;
        }
        return true;
    }

    public function hookModuleRoutes()
    {
        $category_rule = rtrim(Configuration::get('PS_ROUTE_category_rule'), '/');
        if ($category_rule == 'NULL' || $category_rule == false) {
            $category_rule = '{rewrite}';
        };
        $supplier_rule = rtrim(Configuration::get('PURLS_ROUTE_supplier_rule'), '/');
        if ($supplier_rule == 'NULL' || $supplier_rule == false) {
            $supplier_rule = 'supplier/{rewrite}';
        };
        $manufacturer_rule = rtrim(Configuration::get('PURLS_ROUTE_manufacturer_rule'), '/');
        if ($manufacturer_rule == 'NULL' || $manufacturer_rule == false) {
            $manufacturer_rule = 'manufacturer/{rewrite}';
        };
        $as4SqRegeXp = '[_a-zA-Z0-9\pL\pS-/:+]*';
        if (Module::isInstalled('pm_advancedsearch4')) {
            return array(
                'module-pm_advancedsearch4-searchresults-categories' => array(
                    'controller' => 'searchresults',
                    'rule' => $category_rule . '/filter-{id}/s-{id_search}/{as4_sq}',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id_category_search'),
                        'id_search' => array('regexp' => '[0-9]+', 'param' => 'id_search'),
                        'as4_sq' => array('regexp' => $as4SqRegeXp, 'param' => 'as4_sq'),
                        'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                        'parent_categories' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'pm_advancedsearch4',
                        'as4_from' => 'category',
                    )
                ),
                'module-pm_advancedsearch4-searchresults-suppliers' => array(
                    'controller' => 'searchresults',
                    'rule' => $supplier_rule . '/filter-{id}/s-{id_search}/{as4_sq}',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id_supplier_search'),
                        'id_search' => array('regexp' => '[0-9]+', 'param' => 'id_search'),
                        'as4_sq' => array('regexp' => $as4SqRegeXp, 'param' => 'as4_sq'),
                        'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'pm_advancedsearch4',
                        'as4_from' => 'supplier',
                    )
                ),
                'module-pm_advancedsearch4-searchresults-manufacturers' => array(
                    'controller' => 'searchresults',
                    'rule' => $manufacturer_rule . '/filter-{id}/s-{id_search}/{as4_sq}',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id_manufacturer_search'),
                        'id_search' => array('regexp' => '[0-9]+', 'param' => 'id_search'),
                        'as4_sq' => array('regexp' => $as4SqRegeXp, 'param' => 'as4_sq'),
                        'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'pm_advancedsearch4',
                        'as4_from' => 'manufacturer',
                    )
                ),
            );
        }
    }

    public static function getAnchor($id, $id_product_attribute, $with_id = false)
    {
        $attributes = Product::getAttributesParams($id, $id_product_attribute);
        $anchor = '#';
        $sep = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        foreach ($attributes as &$a) {
            foreach ($a as &$b) {
                $b = str_replace($sep, '_', Tools::link_rewrite($b));
            }
            $anchor .= '/' . ($with_id && isset($a['id_attribute']) && $a['id_attribute'] ? $a['id_attribute_group'] . '|' . (int)$a['id_attribute'] . $sep : '') . $a['group'] . $sep . $a['name'];
        }

        return $anchor;
    }

    public function hookHeader($params)
    {
        if (Tools::getValue('controller') == 'product' && Tools::getValue('ajax', 'false') == 'false' && Tools::getValue('id_product', 'false') != 'false') {
            if (Configuration::get('purls_product_hash') == 0 && (Configuration::get('purls_products') == 1 || Configuration::get('purls_products') == 2)) {
                $attributes = Product::getProductAttributesIds(Tools::getValue('id_product'));
                if (is_array($attributes)) {
                    if (count($attributes) > 0) {
                        $this->context->controller->addJS(($this->_path) . 'views/js/purls.js', 'all');
                        $json = array();
                        foreach ($attributes as $combination) {
                            $json[str_replace("#/", "", self::getAnchor(Tools::getValue('id_product'), (int)$combination['id_product_attribute'], false))] = str_replace("#/", "", self::getAnchor(Tools::getValue('id_product'), (int)$combination['id_product_attribute'], true));
                        }
                        Media::addJsDef(array('purls_attributes' => $json));
                    }
                }
            }
        }
    }

    public function getAllProductCollisions($id_lang = null, $id_product = false)
    {
        $where_id_product = '';
        if ($id_lang == null) {
            $id_lang = $this->context->language->id;
        }
        if ($id_product != false) {
            $product = new Product($id_product, false, $id_lang);
            $where_id_product = 'AND pl.`link_rewrite` = "' . (string)$product->link_rewrite . '"';
        }


        return Db::getInstance()->executeS('
		SELECT  pl.`link_rewrite`, pl.`id_product`, pl.`name`, count(pl.`link_rewrite`) as times
		FROM `' . _DB_PREFIX_ . 'product_lang` pl 
        WHERE pl.id_shop = ' . $this->context->shop->id . ' AND pl.id_lang = ' . $id_lang . ' ' . $where_id_product . ' 
		GROUP BY pl.`link_rewrite`
		HAVING COUNT(pl.`link_rewrite`) >= 2');
    }

    public static function getAllProductCollisionsRewrite($rewrite, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT pl.`link_rewrite`, pl.`id_product`, pl.`name`
		FROM `' . _DB_PREFIX_ . 'product_lang` pl 
        WHERE pl.`link_rewrite` ="' . $rewrite . '" AND pl.`id_shop` = ' . Context::getContext()->shop->id . ' AND pl.`id_lang` = ' . $id_lang . '');
    }

    public function getAllCategoryCollisions($id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = $this->context->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT `link_rewrite`, `id_category`, `name`, count(`link_rewrite`) as times
		FROM `' . _DB_PREFIX_ . 'category_lang`  WHERE id_shop = ' . $this->context->shop->id . ' AND id_lang = ' . $id_lang . '
		GROUP BY `link_rewrite`
		HAVING COUNT(`link_rewrite`) >= 2');
    }

    public function getAllCategoryCollisionsRewrite($rewrite, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT `link_rewrite`, `id_category`, `name`
		FROM `' . _DB_PREFIX_ . 'category_lang`  
        WHERE `id_shop` = ' . Context::getContext()->shop->id . ' AND 
        `id_lang` = ' . Context::getContext()->language->id . ' AND 
        `link_rewrite` = "' . $rewrite . '"');
    }

    public function cronJob()
    {
        foreach (Language::getLanguages(true) AS $language => $value_language) {
            $this->makeUnique('products', $value_language['id_lang']);
            $this->makeUnique('categories', $value_language['id_lang']);
        }
    }

}

class purlsUpdate extends purls
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}