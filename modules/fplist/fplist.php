<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author mypresta.eu
 * @copyright VEKIA|PL VATEU 9730945634
 * @license EULA: This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'fplist/model/fpl.php';

class fplist extends Module
{
    function __construct()
    {
        ini_set("display_errors", 0);
        error_reporting(0); //E_ALL
        $this->name = 'fplist';
        $this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/product-features-list.html';
        $this->version = '1.2.1';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Display features on list of products');
        $this->description = $this->l('Module to display features on list of products');
        $this->checkforupdates();
        $this->available_hooks = array(
            'displayProductListReviews',
            'displayProductDeliveryTime'
        );
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
                        $actual_version = fplistUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (fplistUpdate::version($this->version) < fplistUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = fplistUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (fplistUpdate::version($this->version) < fplistUpdate::version(fplistUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        return $exp[1];
    }

    public function inconsistency($ret)
    {
        return;
    }

    public function createMenu()
    {
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Features on lists of products');
        }
        $tab->class_name = 'AdminFplistAssociation';
        $tab->id_parent = 9;
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function uninstall()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminAddTosHistory');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
        return parent::uninstall();
    }

    public function installsql($statements)
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        $this->inconsistency(0);
        return true;
    }

    public function install()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $install_sql = array();
        $install_sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fpl` (
        `id_fpl` int(5) unsigned NOT NULL AUTO_INCREMENT,
        `id_category` int(5) NOT NULL DEFAULT '1',
        `features` VARCHAR(200) NOT NULL DEFAULT '',
        `active` int(5) NOT NULL DEFAULT '0',
        KEY `idfplistkey` (`id_fpl`)) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

        if (!parent::install() ||
            !$this->installsql($install_sql) ||
            !$this->registerHook('displayProductDeliveryTime') ||
            !$this->registerHook('displayProductListReviews') ||
            !$this->registerHook('displayHeader') ||
            !$this->createMenu()) {
            return false;
        }
        return true;
    }

    public function displayModule($params)
    {
        $features = array();
        if (!isset($params['product']['id_product']) && isset($params['product']['id'])) {
            $params['product']['id_product'] = $params['product']['id'];
        }

        if (isset($params['product']['id_product'])) {
            $params['product']['id_product'] = (int)$params['product']['id_product'];
            $product = new Product($params['product']['id_product'], false, Context::getContext()->language->id);
            foreach (fpl::getAll(true) AS $fpl) {
                if ($product->id_category_default == $fpl['id_category']) {
                    $exploded_features = explode(",", $fpl['features']);
                    foreach ($exploded_features AS $ef) {
                        foreach (self::getFeaturesStatic($params['product']['id_product']) AS $fp) {
                            if ($ef == $fp['id_feature']) {
                                $features[$fp['id_feature']]['feature_name'] = $fp['feature_name'];
                                $features[$fp['id_feature']]['feature_value'] = $fp['value'];
                            }
                        }
                    }
                }
            }
        }
        $this->context->smarty->assign('fplist_features', $features);
        return $this->display(__FILE__, 'views/templates/fplist-front.tpl');
    }

    public function hookdisplayProductListReviews($params)
    {
        if (Configuration::get('fplist_position') == 'displayProductListReviews') {
            return $this->displayModule($params);
        }
    }

    public function hookdisplayProductDeliveryTime($params)
    {
        if (Configuration::get('fplist_position') == 'displayProductDeliveryTime') {
            return $this->displayModule($params);
        }
    }

    public static function getFeaturesStatic($id_product)
    {
        if (!Feature::isFeatureActive()) {
            return array();
        }
        $id_lang = Context::getContext()->language->id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT fp.id_feature, fp.id_product, fp.id_feature_value, custom, fl.name AS feature_name, fvl.value
				FROM `' . _DB_PREFIX_ . 'feature_product` fp
				LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (fp.id_feature = fl.id_feature AND fl.id_lang = ' . $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fp.id_feature_value = fv.id_feature_value)
				LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = ' . $id_lang . ')
				WHERE `id_product` = ' . (int)$id_product
        );
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/fplist.css');
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postProcess();
        }
        return $this->renderForm() . $this->checkforupdates(0, 1);
    }

    public function renderForm()
    {
        if (Tools::getValue('submit_fplist')) {
            Configuration::updateValue('fplist_position', Tools::getValue('fplist_position'));
        }

        $options = array();
        foreach ($this->available_hooks AS $hook) {
            $array = array();
            $array['name'] = $hook;
            $array['id'] = $hook;
            $options[] = $array;
        }
        $inputs = array(
            array(
                'type' => 'select',
                'label' => $this->l('Position of tags'),
                'name' => 'fplist_position',
                'desc' => $this->l('Select position (on list of products) where you want to display features associated with product') . '<div class="alert alert-info">
                                    ' . $this->l('displayProductListReviews - this is default position available in PrestaShop 1.7.') . '<br/>
                                    ' . $this->l('displayProductDeliveryTime - it is not default position in PrestaShop 1.7 but it it frequently used by many themes') . '<br/>
                                  </div>',
                'options' => array(
                    'query' => $options,
                    'id' => 'name',
                    'name' => 'name'
                ),
            )
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = 'fplist';
        $helper->submit_action = 'submit_fplist';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getBlockFieldsGlobal(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form)) . $this->checkforupdates(0, 1);
        $this->context->controller->informations[] = $this->l('Category - feature associations manager is available here:') . ' <strong><a href="' . $this->context->link->getAdminLink('AdminFplistAssociation', true) . '">' . $this->l('Catalog > Features on lists of products') . '</a></strong>';
    }

    public function getBlockFieldsGlobal()
    {
        return array(
            'fplist_position' => Configuration::get('fplist_position'),
        );
    }

}

class fplistUpdate extends fplist
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 4) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 3) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "0000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "00000";
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
