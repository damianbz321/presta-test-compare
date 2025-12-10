<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 5.5
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class ordercoupons extends Module
{
    function __construct()
    {
        ini_set("display_errors", 0);
        error_reporting(0);
        $this->name = 'ordercoupons';
        $this->tab = 'advertising_marketing';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/advertising-and-marketing/rewards-voucher-codes-after-orders.html';
        $this->version = '3.1.8';
        $this->bootstrap = true;
        $this->module_key = 'a691882fcd74706f0917882e2f4d12c9';
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l('Voucher after order - rewards module');
        $this->description = $this->l('Order rewards program for your prestashop, customer loyalty module - grant vouchers for your loyal customers');
        $this->availableTemplateVars['{order_id}'] = '';
        $this->availableTemplateVars['{order_reference}'] = '';
        $this->availableTemplateVars['{voucher}'] = '';
        $this->availableTemplateVars['{voucher_name}'] = '';
        $this->availableTemplateVars['{voucher_date_from}'] = '';
        $this->availableTemplateVars['{voucher_date_to}'] = '';
        $this->availableTemplateVars['{voucher_dateonly_from}'] = '';
        $this->availableTemplateVars['{voucher_dateonly_to}'] = '';
        $this->availableTemplateVars['{voucher_value}'] = '';
        $this->availableTemplateVars['{voucher_description}'] = '';
        $this->availableTemplateVars['{customer_firstname}'] = '';
        $this->availableTemplateVars['{customer_lastname}'] = '';
        $this->emailTemplatesManager = new ordercouponsEmailTemplatesManager($this->name, $this->availableTemplateVars);
        $this->searchTool = new searchToolordercoupons($this->name, '');

        //voucher engine fields to translate
        $this->l('Tax excluded');
        $this->l('Tax included');
        $this->l('Shipping excluded');
        $this->l('Shipping included');
        $this->l('Enabled');
        $this->l('Disabled');
        $this->l('Percent(%)');
        $this->l('Amount');
        $this->l('None');
        $this->l('Value');
        $this->l('Amount');
        $this->l('Order (without shipping)');
        $this->l('Specific product');
        $this->l('Product ID');
        $this->l('enter product ID number');
        $this->l('how to get product id?');
        $this->l('Select categories from list above, use CTRL+click to select multiple categories, CTRL+A to select all of them');
        $this->l('Select products from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('General settings');
        $this->l('Name');
        $this->l('This will be displayed in the cart summary, as well as on the invoice');
        $this->l('Description');
        $this->l('For your eyes only. This will never be displayed to the customer');
        $this->l('Voucher length');
        $this->l('How many characters will be used to generate voucher code');
        $this->l('Enable sufix');
        $this->l('Turn this option on if you want to enable sufix for your voucher code. It will be added AFTER generated code like CODE_sufix.');
        $this->l('Sufix');
        $this->l('Define sufix for your voucher code');
        $this->l('Enable prefix');
        $this->l('Turn this option on if you want to enable prefix for your voucher code. It will be added BEFORE generated code like prefix_CODE.');
        $this->l('Prefix');
        $this->l('Define prefix for your voucher code');
        $this->l('Highlight');
        $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.');
        $this->l('Partial use');
        $this->l('Only applicable if the voucher value is greater than the cart total.
If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.');
        $this->l('Priority');
        $this->l('Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".');
        $this->l('Active');
        $this->l('Conditions');
        $this->l('Expiration time');
        $this->l('Define how long (in days) voucher code will be active');
        $this->l('Minimum amount');
        $this->l('You can choose a minimum amount for the cart either with or without the taxes and shipping.');
        $this->l('Total available');
        $this->l('The cart rule will be applied to the first "X" customers only.');
        $this->l('Total available for each user');
        $this->l('A customer will only be able to use the cart rule "X" time(s).');
        $this->l('Add rule concerning categories');
        $this->l('Add rule concerning products');
        $this->l('Actions');
        $this->l('Free shipping');
        $this->l('Apply a discount');
        $this->l('Apply discount to');
        $this->l('Turn this option on if you want dont want to allow to use this code with other voucher codes');
        $this->l('Uncombinable with other codes');
        $this->l('Select manufacturers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('Add rule concerning manufacturers');
        $this->l('Add rule concerning attributes');
        $this->l('Select Attributes from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('Cheapest product');
        $this->l('Selected products');
        $this->l('Cumulative with price reductions');
        $this->l('Turn this option on if you want to allow to use this code with price reductions');
        $this->l('Date from');
        $this->l('Date to');
        $this->l('Expiry date, format: YYYY-MM-DD HH:MM:SS');
        $this->l('Start date, format: YYYY-MM-DD HH:MM:SS');
        $this->l('Conditions');
        //2.1
        $this->l('Search for product');
        $this->l('or enter product ID');
        $this->l('product combination ID');
        //2.5
        $this->l('Send free gift');
        //2.7
        $this->l('Add rule concerning carriers');
        $this->l('Select carriers from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        //3.2
        $this->l('Please fill out each available field - do not leave fields empty. Otherwise module will not generate coupon codes or these codes will not work properly.');
        $this->l('Below you can find links to YouTube videos where you can find more informations about this voucher code configuration tool.');
        $this->l('Please note that settings here are related to one unique voucher code that module will generate.');
        $this->l('Suggested values for fields below: Total available: 1, Total available for each user: 1');
        $this->l('This means that customer that will receive one unique voucher will have possibility to use it during checkout only one time (as long as you will use suggested values)');
        $this->l('Video description of advanced voucher configuration tool');
        $this->l('General settings');
        $this->l('Conditions settings');
        $this->l('Actions settings');
        //3.4
        $this->l('Add rule concerning suppliers');
        $this->l('Select suppliers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        //3.6
        $this->l('Share voucher between shops');
        $this->l('If enabled - voucher will be shared between shops (multistore), if disabled - voucher will be available only in shop where it was generated');
        //4.0
        $this->l('Select groups from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        $this->l('Add rule concerning groups of customers');
        //4.4
        $this->l('Exclude discounted products');
        $this->l('If enabled, the voucher will not apply to products already on sale.');
        //5.2
        $this->l('Select countries from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        $this->l('Add rule concerning countries');

        $this->checkforupdates();
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
            <div class="panel" id="fieldset_myprestaupdates">
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
                        $actual_version = ordercouponsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (ordercouponsUpdate::version($this->version) < ordercouponsUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = ordercouponsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (ordercouponsUpdate::version($this->version) < ordercouponsUpdate::version(ordercouponsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
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
        $exp = $explode = explode(".", $version);
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

    function install()
    {
        if (!parent::install() or !Configuration::updateValue('update_' . $this->name, '0') or !$this->registerHook('actionOrderStatusUpdate') or @$this->installdb() or !$this->createMenu()) {
            return false;
        }
        return true;
    }

    public function runStatement($statement)
    {
        if (@!Db::getInstance()->Execute($statement)) {
            return false;
        }
        return true;
    }

    public function inconsistency($return_report = 1)
    {
        $form = '';
        $return = array();
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $table['orewards']['value2']['type'] = "VARCHAR";
        $table['orewards']['value2']['length'] = "5";
        $table['orewards']['value2']['default'] = "1";
        $table['orewards']['min']['type'] = "VARCHAR";
        $table['orewards']['min']['length'] = "5";
        $table['orewards']['min']['default'] = "0";
        $table['orewards']['min_order']['type'] = "DECIMAL";
        $table['orewards']['min_order']['length'] = "17,2";
        $table['orewards']['min_order']['default'] = "X";
        $table['orewards']['min_currency']['type'] = "VARCHAR";
        $table['orewards']['min_currency']['length'] = "4";
        $table['orewards']['min_currency']['default'] = "0";
        $table['orewards']['max']['type'] = "VARCHAR";
        $table['orewards']['max']['length'] = "5";
        $table['orewards']['max']['default'] = "0";
        $table['orewards']['max_order']['type'] = "DECIMAL";
        $table['orewards']['max_order']['length'] = "17,2";
        $table['orewards']['max_order']['default'] = "X";
        $table['orewards']['max_currency']['type'] = "VARCHAR";
        $table['orewards']['max_currency']['length'] = "5";
        $table['orewards']['max_currency']['default'] = "0";
        $table['orewards']['manufacturers']['type'] = "VARCHAR";
        $table['orewards']['manufacturers']['length'] = "2";
        $table['orewards']['manufacturers']['default'] = "0";
        $table['orewards']['manufacturers_list']['type'] = "TEXT";
        $table['orewards']['manufacturers_list']['length'] = "2";
        $table['orewards']['manufacturers_list']['default'] = "X";
        $table['orewards']['nocoupon']['type'] = "VARCHAR";
        $table['orewards']['nocoupon']['length'] = "2";
        $table['orewards']['nocoupon']['default'] = "0";
        $table['orewards']['percentage']['type'] = "VARCHAR";
        $table['orewards']['percentage']['length'] = "2";
        $table['orewards']['percentage']['default'] = "0";
        $table['orewards']['percentage_value']['type'] = "VARCHAR";
        $table['orewards']['percentage_value']['length'] = "10";
        $table['orewards']['percentage_value']['default'] = "0";
        $table['orewards']['associate_customer']['type'] = "VARCHAR";
        $table['orewards']['associate_customer']['length'] = "2";
        $table['orewards']['associate_customer']['default'] = "0";
        $table['orewards']['no_guests']['type'] = "VARCHAR";
        $table['orewards']['no_guests']['length'] = "2";
        $table['orewards']['no_guests']['default'] = "0";
        $table['orewards']['prod']['type'] = "VARCHAR";
        $table['orewards']['prod']['length'] = "2";
        $table['orewards']['prod']['default'] = "0";
        $table['orewards']['prod_list']['type'] = "TEXT";
        $table['orewards']['prod_list']['length'] = "2";
        $table['orewards']['prod_list']['default'] = "X";

        $table['orewards']['prod_eo']['type'] = "VARCHAR";
        $table['orewards']['prod_eo']['length'] = "2";
        $table['orewards']['prod_eo']['default'] = "0";
        $table['orewards']['prod_eo_list']['type'] = "TEXT";
        $table['orewards']['prod_eo_list']['length'] = "2";
        $table['orewards']['prod_eo_list']['default'] = "X";

        $table['orewards']['prod_diff_than']['type'] = "VARCHAR";
        $table['orewards']['prod_diff_than']['length'] = "2";
        $table['orewards']['prod_diff_than']['default'] = "0";
        $table['orewards']['prod_diff_than_list']['type'] = "TEXT";
        $table['orewards']['prod_diff_than_list']['length'] = "2";
        $table['orewards']['prod_diff_than_list']['default'] = "X";


        $table['orewards']['id_shop']['type'] = "VARCHAR";
        $table['orewards']['id_shop']['length'] = "5";
        $table['orewards']['id_shop']['default'] = "1";
        $table['orewards']['exvalcat']['type'] = "VARCHAR";
        $table['orewards']['exvalcat']['length'] = "1";
        $table['orewards']['exvalcat']['default'] = "0";
        $table['orewards']['excat']['type'] = "VARCHAR";
        $table['orewards']['excat']['length'] = "255";
        $table['orewards']['excat']['default'] = "";
        $table['orewards']['groups']['type'] = "VARCHAR";
        $table['orewards']['groups']['length'] = "255";
        $table['orewards']['groups']['default'] = "";
        //2.2.1
        $table['orewards']['mail_template']['type'] = "VARCHAR";
        $table['orewards']['mail_template']['length'] = "255";
        $table['orewards']['mail_template']['default'] = "oc_voucher";
        //2.3.1
        $table['orewards']['minbasket']['type'] = "VARCHAR";
        $table['orewards']['minbasket']['length'] = "1";
        $table['orewards']['minbasket']['default'] = "1";
        //2.3.7
        $table['orewards']['percentage_type']['type'] = "VARCHAR";
        $table['orewards']['percentage_type']['length'] = "1";
        $table['orewards']['percentage_type']['default'] = "1";
        $table['orewards']['specific_product']['type'] = "VARCHAR";
        $table['orewards']['specific_product']['length'] = "20";
        $table['orewards']['specific_product']['default'] = "0";
        //3.0.6
        $table['orewards']['active']['type'] = "VARCHAR";
        $table['orewards']['active']['length'] = "1";
        $table['orewards']['active']['default'] = "1";
        //3.0.7
        $table['orewards']['categories']['type'] = "VARCHAR";
        $table['orewards']['categories']['length'] = "2";
        $table['orewards']['categories']['default'] = "0";
        $table['orewards']['categories_list']['type'] = "TEXT";
        $table['orewards']['categories_list']['length'] = "2";
        $table['orewards']['categories_list']['default'] = "X";

        foreach (Db::getInstance()->executeS("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA ='" . _DB_NAME_ . "' AND TABLE_NAME='" . _DB_PREFIX_ . "orewards'") AS $key => $column) {
            $return[$column['COLUMN_NAME']] = "1";
        }

        foreach ($table['orewards'] as $key => $field) {
            if (!isset($return[$key])) {
                $error[$key]['type'] = "0";
                $error[$key]['message'] = $this->l('Database inconsistency, column does not exist');
                if ($field['default'] != "X") {
                    if ($this->runStatement("ALTER TABLE `${prefix}orewards` ADD COLUMN `" . $key . "` " . $field['type'] . "(" . $field['length'] . ") NULL DEFAULT '" . $field['default'] . "'")) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                } else {
                    if ($this->runStatement("ALTER TABLE `${prefix}orewards` ADD COLUMN `" . $key . "` " . $field['type'])) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                }
                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], '1');
                }
            } else {
                $error[$key]['type'] = "1";
                $error[$key]['message'] = $this->l('OK!');
                $error[$key]['fixed'] = $this->l('');
                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], '1');
                }
            }
        }

        $form .= '<table class="inconsistency"><tr><td colspan="4" style="text-align:center">' . $this->l('DB TABLE') . '</td></tr>';
        foreach ($error as $column => $info) {
            $form .= "<tr><td class='inconsistency" . $info['type'] . "'></td><td>" . $column . "</td><td>" . $info['message'] . "</td><td>" . $info['fixed'] . "</td></tr>";
        }
        $form .= "</table>";

        if ($return_report == 1) {
            return $form;
        } else {
            return true;
        }
    }

    public function createMenu()
    {
        $tab = new Tab();
        $tab->id_parent = Tab::getIdFromClassName('AdminParentOrders');
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Customers rewards');
        }
        $tab->class_name = 'AdminOrderCouponsRewards';
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function uninstall()
    {
        if (parent::uninstall() == false) {
            return false;
        }
        $idTabs = array();
        if (Tab::getIdFromClassName('AdminOrderCouponsRewards')) {
            $idTabs[] = Tab::getIdFromClassName('AdminOrderCouponsRewards');
            foreach ($idTabs as $idTab) {
                if ($idTab) {
                    $tab = new Tab($idTab);
                    $tab->delete();
                }
            }
        }
        return true;
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $statements = array();
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}orewards_list` (" . '`id_reward` int(10) NOT NULL AUTO_INCREMENT,' . '`id_cart_rule` int(10) ,' . '`id_customer` int(10) ,' . '`id_order` int(10) ,' . '`reward` VARCHAR(50) ,' . '`date_add` VARCHAR(50) ,' . '`date_upd` VARCHAR(50) ,' . '`date_del` VARCHAR(50) ,' . 'PRIMARY KEY (`id_reward`)' . ")";
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}orewards` (" . '`id_reward` int(10) NOT NULL AUTO_INCREMENT,' . '`value` int(10) ,' . '`internal_name` VARCHAR(50),' . 'PRIMARY KEY (`id_reward`)' . ")";
        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        $this->inconsistency(0);
        //return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit_newVoucher')) {
            if (Tools::getValue('id_reward_edit')) {
                $ocoupon = new oreward(Tools::getValue('id_reward_edit'));
            } else {
                $ocoupon = new oreward();
            }


            $ocoupon->internal_name = Tools::getValue('internal_name');
            $ocoupon->value = Tools::getValue('value');
            $ocoupon->value2 = Tools::getValue('value2');
            $ocoupon->min = Tools::getValue('min');
            $ocoupon->min_order = Tools::getValue('min_order');
            $ocoupon->min_currency = Tools::getValue('min_currency');
            $ocoupon->max = Tools::getValue('max');
            $ocoupon->max_order = Tools::getValue('max_order');
            $ocoupon->max_currency = Tools::getValue('max_currency');
            $ocoupon->nocoupon = Tools::getValue('nocoupon', 0);
            $ocoupon->associate_customer = Tools::getValue('associate_customer', 0);
            $ocoupon->no_guests = Tools::getValue('no_guests', 0);
            $ocoupon->exvalcat = Tools::getValue('exvalcat', 0);
            $ocoupon->excat = Tools::getValue('excat', '');
            $ocoupon->groups = implode(",", Tools::getValue('oc_cgroup'));
            $ocoupon->id_shop = $this->context->shop->id;
            $ocoupon->mail_template = Tools::getValue('mail_template', 'oc_voucher');

            // MANUFACTURERS
            $ocoupon->manufacturers = Tools::getValue('manufacturers');
            $ocoupon->manufacturers_list = trim(Tools::getValue('manufacturers_list'));

            // CATEGORIES
            $ocoupon->categories = Tools::getValue('categories');
            $ocoupon->categories_list = trim(Tools::getValue('categories_list'));

            $ocoupon->prod = Tools::getValue('prod');
            $ocoupon->prod_list = trim(Tools::getValue('prod_list'));
            $ocoupon->prod_eo = Tools::getValue('prod_eo');
            $ocoupon->prod_eo_list = trim(Tools::getValue('prod_eo_list'));
            $ocoupon->prod_diff_than = Tools::getValue('prod_diff_than');
            $ocoupon->prod_diff_than_list = trim(Tools::getValue('prod_diff_than_list'));

            $ocoupon->percentage = Tools::getValue('percentage');
            $ocoupon->minbasket = Tools::getValue('minbasket');
            $ocoupon->percentage_value = Tools::getValue('percentage_value');
            $ocoupon->percentage_type = Tools::getValue('percentage_type');
            $ocoupon->specific_product = Tools::getValue('order_value_specific_product');
            if ($ocoupon->save()) {
                $this->context->controller->confirmations[] = $this->l('Reward saved');
            }

            $orewardtitle = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $orewardtitle[$lang['id_lang']] = Tools::getValue('orewardtitle_' . $lang['id_lang'], $this->l('Email title'));

                if ($orewardtitle[$lang['id_lang']] == "") {
                    $orewardtitle[$lang['id_lang']] = $this->l('Email title');
                }
            }
            Configuration::updateValue('orewardtitle' . $ocoupon->id, $orewardtitle);
        }

        if (Tools::isSubmit('id_reward_test')) {
            $oc = new ordercouponsVoucherEngine("oc" . Tools::getValue('id_reward_test'));
            $oc_reward = new oreward(Tools::getValue('id_reward_test'));

            if ($oc_reward->percentage == 1) {
                $voucher = $oc->AddVoucherCode("oc" . Tools::getValue('id_reward_test'), null, null, null, null, rand(1, 20));
            } else {
                $voucher = $oc->AddVoucherCode("oc" . Tools::getValue('id_reward_test'));
            }

            $cartRule = new CartRule(CartRule::getIdByCode($voucher->code));
            $voucher_value = null;
            if ($cartRule->reduction_amount > 0) {
                $voucher_currency = new Currency($cartRule->reduction_currency);
                $voucher_currency_sign = $voucher_currency->sign;
                $voucher_value = Tools::displayPrice($cartRule->reduction_amount,  $voucher_currency);
                if ($cartRule->free_shipping == 1) {
                    if ($voucher_value == null) {
                        $voucher_value = $this->l('Free shipping');
                    } else {
                        $voucher_value .= " + " . $this->l('Free shipping');
                    }
                }
            } elseif ($cartRule->reduction_percent > 0) {
                $voucher_value = $cartRule->reduction_percent . "%";
                if ($cartRule->free_shipping == 1) {
                    if ($voucher_value == null) {
                        $voucher_value = $this->l('Free shipping');
                    } else {
                        $voucher_value .= " + " . $this->l('Free shipping');
                    }
                }
            } elseif ($cartRule->free_shipping == 1) {
                if ($voucher_value == null) {
                    $voucher_value = $this->l('Free shipping');
                } else {
                    $voucher_value .= " + " . $this->l('Free shipping');
                }
            }


            $templateVars['{order_id}'] = '{order_id}';
            $templateVars['{order_reference}'] = '{order_reference}';
            $templateVars['{voucher}'] = $voucher->code;
            $templateVars['{voucher_date_from}'] = $cartRule->date_from;
            $templateVars['{voucher_date_to}'] = $cartRule->date_to;
            $templateVars['{voucher_dateonly_from}'] = date("Y-m-d", strtotime($cartRule->date_from));
            $templateVars['{voucher_dateonly_to}'] = date("Y-m-d", strtotime($cartRule->date_to));
            $templateVars['{voucher_value}'] = $voucher_value;
            $templateVars['{voucher_description}'] = $cartRule->description;

            if (!isset($id_lang)) {
                $id_lang = Context::getContext()->language->id;
            }
            if (!isset($id_shop)) {
                $id_shop = Context::getContext()->shop->id;
            }
            if (Mail::Send($id_lang, $oc_reward->mail_template, Configuration::get('orewardtitle' . Tools::getValue('id_reward_test'), $id_lang), $templateVars, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)), null, null, dirname(__file__) . '/mails/', false, $id_shop, (Configuration::get('oc_copy') == 1 ? strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)) : NULL))) {
                $this->context->controller->confirmations[] = $this->l('Test email delivered to: ') . strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop));
            }

        }

        if (Tools::isSubmit('ocoupon_remove')) {
            $ocoupon = new oreward(Tools::getValue('id_reward'));
            if ($ocoupon->delete()) {
                Configuration::deleteByName('orewardtitle' . Tools::getValue('id_reward'));
                $this->context->controller->confirmations[] = $this->l('Reward deleted');
            }
        }

        if (Tools::isSubmit('ocoupon_activate')) {
            $ocoupon = new oreward(Tools::getValue('id_reward'));
            $ocoupon->active = !$ocoupon->active;
            $ocoupon->save();
            if ($ocoupon->active == 1) {
                $this->context->controller->confirmations[] = $this->l('Reward activated');
            } else {
                $this->context->controller->confirmations[] = $this->l('Reward deactivated');
            }
        }

        if (Tools::isSubmit('save_voucher_settings')) {
            ordercouponsVoucherEngine::updateVoucher(Tools::getValue('voucherPrefix'), $_POST);
            $this->context->controller->confirmations[] = $this->l('Reward voucher pattern saved');
        }
        return $this->renderBackOfficeMenu() . $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ordercoupons/views/templates/admin/scripts.tpl') . $this->renderStyles() . $this->searchTool->initTool();
    }

    public function getOrdersNbByIdCustomer($id, $idorder = false)
    {
        $sql_time_frame = '';
        $time_frame = Configuration::get('oc_timeframe');
        if ($time_frame == 0) {
            $sql_time_frame = '';
        } elseif ($time_frame == 99) {
            $sql_time_frame = "o.date_add >= '" . Configuration::get('oc_date_from') . "' AND " . "o.date_add <= '" . Configuration::get('oc_date_to') . "' AND ";
        }

        if ($idorder != false) {
            $idorder = 'OR o.id_order=' . $idorder;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT count(*) as count FROM `' . _DB_PREFIX_ . 'orders` o WHERE ' . $sql_time_frame . ' o.id_customer="' . $id . '" AND (o.current_state IN (' . Configuration::get('oc_ostates') . ') ' . $idorder . ')');
    }

    public function getOrdersNbByIdCustomerAndIdOrder($id, $idorder = false)
    {
        $sql_time_frame = '';
        $time_frame = Configuration::get('oc_timeframe');
        if ($time_frame == 0) {
            $sql_time_frame = '';
        } elseif ($time_frame == 99) {
            $sql_time_frame = "o.date_add >= '" . Configuration::get('oc_date_from') . "' AND " . "o.date_add <= '" . Configuration::get('oc_date_to') . "' AND ";
        }

        if ($idorder != false) {
            $idorder = 'AND o.id_order=' . $idorder;
        }

        $idorder_state = '';
        if (Tools::getValue('id_order','false') != 'false') {
            if (Tools::getValue('id_order_state','false') != 'false') {
                $states = explode(',', Configuration::get('oc_ostates'));
                if (in_array(Tools::getValue('id_order_state','false'), $states)){
                    $idorder_state = 'OR o.id_order='.Tools::getValue('id_order','false');
                }
            }
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT count(*) as count FROM `' . _DB_PREFIX_ . 'orders` o WHERE ' . $sql_time_frame . ' o.id_customer="' . $id . '" AND ((o.current_state IN (' . Configuration::get('oc_ostates') . ') '.$idorder_state.') ' . $idorder . ')');
    }

    public function hookupdateOrderStatus($params)
    {
        return $this->hookactionOrderStatusUpdate($params);
    }

    public function hookactionOrderStatusUpdate($params)
    {
        if (Configuration::get('oc_cron') != 1) {
            $jest = 0;
            foreach (explode(",", Configuration::get('oc_ostates')) as $k => $v) {
                if ($v == $params['newOrderStatus']->id) {
                    $jest = 1;
                }
            }

            $die = 0;
            if ($jest == 1) {
                $order = new Order($params['id_order']);
                $customer = new Customer($order->id_customer);

                if (Configuration::get('oc_timeframe') == 99) {
                    $nbofvoucehrsverify = $this->getOrdersNbByIdCustomerAndIdOrder($order->id_customer, $params['id_order']);
                    if ($nbofvoucehrsverify[0]['count'] == 0) {
                        $die = 1;
                    }
                }

                if (Configuration::get('oc_counter') == 2) {
                    $nbofvouchers = orewardslist::getByIdCustomer($customer->id);
                    $nbofvouchers = $nbofvouchers['count'];
                    $nbofvouchers++;
                } else {
                    $nbofvouchers = $this->getOrdersNbByIdCustomer($order->id_customer, $params['id_order']);
                    $nbofvouchers = $nbofvouchers[0]['count'];
                    $nbofvouchersindb = orewardslist::getByIdCustomer($order->id_customer);
                    $voucherExists = oreward::getByValue($nbofvouchers);
                    $voucherExists = $voucherExists['count'];
                    $voucherExistsFull = oreward::getByValueFull($nbofvouchers);

                    if ($nbofvouchersindb['count'] == $nbofvouchers) {
                        $die = 1;
                    }
                    $alreadyAddedType = orewardslist::getByIdCustomerAndIdReward($order->id_customer, $voucherExistsFull['id_reward']);

                    if ($alreadyAddedType['count'] >= 1) {
                        $die = 1;
                    }
                }



                $voucherExists = oreward::getByValue($nbofvouchers);
                $voucherExists = $voucherExists['count'];
                $voucherExistsFull = oreward::getByValueFullArray($nbofvouchers);
                $result = orewardslist::getByIdCustomerAndIdOrder($customer->id, $order->id);
                $alreadyAdded = $result['count'];

                foreach ($voucherExistsFull as $voucherExistsFull) {
                    $order = new Order($params['id_order']);
                    $excluded = array();
                    if ($voucherExistsFull['exvalcat'] == 1) {
                        foreach ($order->getProductsDetail() as $k => $v) {
                            foreach (Product::getProductCategories($v['id_product']) AS $kk => $vv) {
                                if (in_array($vv, explode(",", $voucherExistsFull['excat'])) == true && !isset($excluded[$v['id_order_detail']])) {
                                    $excluded[$v['id_order_detail']]=true;
                                    if ($voucherExistsFull['percentage_type'] == 1) {
                                        $order->total_paid = $order->total_paid - $v['total_price_tax_incl'];
                                    } elseif ($voucherExistsFull['percentage_type'] == 2) {
                                        $order->total_paid_tax_excl = $order->total_paid_tax_excl - $v['total_price_tax_excl'];
                                    } elseif ($voucherExistsFull['percentage_type'] == 3) {
                                        $order->total_products_wt = $order->total_products_wt - $v['total_price_tax_incl'];
                                    } elseif ($voucherExistsFull['percentage_type'] == 4) {
                                        $order->total_products = $order->total_products - $v['total_price_tax_excl'];
                                    }
                                }
                            }
                        }
                    }


                    $diee = 0;
                    if ($voucherExistsFull['min'] == 1) {
                        $currency_order = new Currency($order->id_currency);
                        $currency_to = new Currency($voucherExistsFull['min_currency']);
                        if ($order->total_paid <= Tools::convertPriceFull($voucherExistsFull['min_order'], $currency_order, $currency_to)) {
                            $diee = 1;
                        }
                    }

                    if ($voucherExistsFull['max'] == 1) {
                        $currency_order = new Currency($order->id_currency);
                        $currency_to = new Currency($voucherExistsFull['max_currency']);
                        if ($order->total_paid >= Tools::convertPrice($voucherExistsFull['max_order'], $currency_order, $currency_to)) {
                            $diee = 1;
                        }
                    }

                    // MANUFACTURER CHECK
                    if ($voucherExistsFull['manufacturers'] == 1) {
                        $jest_manufacturer = 0;
                        foreach ($order->getProductsDetail() as $k => $v) {
                            if (in_array($v['id_manufacturer'], explode(",", $voucherExistsFull['manufacturers_list'])) == true) {
                                $jest_manufacturer = 1;
                            }
                        }

                        if ($jest_manufacturer == 0) {
                            $diee = 1;
                        }
                    }


                    // CATEGORY CHECK
                    if ($voucherExistsFull['categories'] == 1) {
                        $jest_categories = 0;
                        foreach ($order->getProductsDetail() as $k => $v) {
                            foreach (Product::getProductCategories($v['product_id']) AS $kk => $vv) {
                                if (in_array($vv, explode(",", $voucherExistsFull['categories_list'])) == true) {
                                    $jest_categories = 1;
                                }
                            }
                        }

                        if ($jest_categories == 0) {
                            $diee = 1;
                        }
                    }


                    // PRODUCT CHECK
                    if ($voucherExistsFull['prod'] == 1) {
                        $has_product = 0;
                        foreach ($order->getProductsDetail() as $k => $v) {
                            if (in_array($v['id_product'], explode(",", $voucherExistsFull['prod_list'])) == true) {
                                $has_product = 1;
                            }
                        }

                        if ($has_product == 0) {
                            $diee = 1;
                        }
                    }

                    if ($voucherExistsFull['prod_eo'] == 1) {
                        $has_product = 0;
                        foreach ($order->getProductsDetail() as $k => $v) {
                            if (in_array($v['id_product'], explode(",", $voucherExistsFull['prod_eo_list'])) == true) {
                                $has_product = 1;
                            }
                        }

                        if ($has_product == 1) {
                            $diee = 1;
                        }
                    }


                    if ($voucherExistsFull['prod_diff_than'] == 1) {
                        $has_product = 0;
                        foreach ($order->getProductsDetail() as $k => $v) {
                            if (!in_array($v['id_product'], explode(",", $voucherExistsFull['prod_diff_than'])) == true) {
                                $has_product = 1;
                            }
                        }

                        if ($has_product == 1) {
                            $diee = 1;
                        }
                    }


                    if ($voucherExistsFull['nocoupon'] == 1) {
                        if (count($order->getCartRules()) > 0) {
                            $diee = 1;
                        }
                    }


                    if ($voucherExistsFull['no_guests'] == 1) {
                        if ($customer->is_guest == 1) {
                            $diee = 1;
                        }
                    }



                    if (isset($customer->id)) {
                        $die_group = 0;
                        $permitted = 0;
                        foreach (Customer::getGroupsStatic($customer->id) AS $ck => $cv) {
                            $permitted_groups = explode(",", $voucherExistsFull['groups']);

                            if (in_array($cv, $permitted_groups)) {
                                $permitted = 1;
                            }
                        }
                        if ($permitted != 1) {
                            $die_group = 1;
                        }
                    }




                    if ($alreadyAdded == 0 and $voucherExists >= 1 and $die == 0 and $diee != 1 and $die_group != 1) {
                        $oc = new ordercouponsVoucherEngine("oc" . $voucherExistsFull['id_reward']);
                        $oc_reward = new oreward($voucherExistsFull['id_reward']);

                        if ($oc_reward->minbasket == 1) {
                            $minbasket = null;
                        } elseif ($oc_reward->minbasket == 2) {
                            $minbasket = $order->total_paid;
                        } elseif ($oc_reward->minbasket == 3) {
                            $minbasket = $order->total_paid * 2;
                        }

                        if ($oc_reward->percentage == 1) {
                            if ($oc_reward->percentage_type == 3) {
                                $order_value = $order->total_products_wt;
                            } elseif ($oc_reward->percentage_type == 4) {
                                $order_value = $order->total_products;
                            } elseif ($oc_reward->percentage_type == 1) {
                                $order_value = $order->total_paid_tax_incl;
                            } elseif ($oc_reward->percentage_type == 2) {
                                $order_value = $order->total_paid_tax_excl;
                            } elseif ($oc_reward->percentage_type == 5) {
                                foreach ($order->getProductsDetail() as $k => $v) {
                                    if ($oc_reward->specific_product == $v['product_id']) {
                                        $order_value = $v['total_price_tax_incl'];
                                    }
                                }
                            } elseif ($oc_reward->percentage_type == 6) {
                                if ($oc_reward->specific_product == $v['product_id']) {
                                    $order_value = $v['total_price_tax_excl'];
                                }
                            } else {
                                $order_value = $order->total_paid;
                            }

                            $voucher = $oc->AddVoucherCode("oc" . $voucherExistsFull['id_reward'], ($oc_reward->associate_customer == 1 ? $customer->id : null), null, null, null, ($order_value * $oc_reward->percentage_value / 100), null, null, null, null, null, $minbasket, $order->id_currency, $order->id_currency);
                        } else {
                            $voucher = $oc->AddVoucherCode("oc" . $voucherExistsFull['id_reward'], ($oc_reward->associate_customer == 1 ? $customer->id : null), null, null, null, null, null, null, null, null, null, $minbasket, $order->id_currency);
                        }


                        $orewardslist = new orewardslist();
                        $orewardslist->date_add = date("Y-m-d H:i:s");
                        $orewardslist->date_upd = date("Y-m-d H:i:s");
                        $orewardslist->id_customer = $customer->id;
                        $orewardslist->id_order = $order->id;
                        $orewardslist->reward = $voucher->code;
                        $orewardslist->id_cart_rule = $voucherExistsFull['id_reward'];
                        $orewardslist->add();

                        $cartRule = new CartRule(CartRule::getIdByCode($voucher->code));
                        $voucher_value = null;
                        if ($cartRule->reduction_amount > 0) {
                            $voucher_currency = new Currency($cartRule->reduction_currency);
                            $voucher_currency_sign = $voucher_currency->sign;
                            $voucher_value = Tools::displayPrice($cartRule->reduction_amount,  $voucher_currency);
                            //$voucher_value = $cartRule->reduction_amount . " " . $voucher_currency_sign;
                            if ($cartRule->free_shipping == 1) {
                                if ($voucher_value == null) {
                                    $voucher_value = $this->l('Free shipping');
                                } else {
                                    $voucher_value .= " + " . $this->l('Free shipping');
                                }
                            }
                        } elseif ($cartRule->reduction_percent > 0) {
                            $voucher_value = $cartRule->reduction_percent . "%";
                            if ($cartRule->free_shipping == 1) {
                                if ($voucher_value == null) {
                                    $voucher_value = $this->l('Free shipping');
                                } else {
                                    $voucher_value .= " + " . $this->l('Free shipping');
                                }
                            }
                        } elseif ($cartRule->free_shipping == 1) {
                            if ($voucher_value == null) {
                                $voucher_value = $this->l('Free shipping');
                            } else {
                                $voucher_value .= " + " . $this->l('Free shipping');
                            }
                        }

                        $templateVars['{order_id}'] = $order->id;
                        $templateVars['{order_reference}'] = $order->reference;
                        $templateVars['{voucher}'] = $voucher->code;
                        $templateVars['{voucher_name}'] = $voucher->name;
                        $templateVars['{voucher_date_from}'] = $cartRule->date_from;
                        $templateVars['{voucher_date_to}'] = $cartRule->date_to;
                        $templateVars['{voucher_dateonly_from}'] = date("Y-m-d", strtotime($cartRule->date_from));
                        $templateVars['{voucher_dateonly_to}'] = date("Y-m-d", strtotime($cartRule->date_to));
                        $templateVars['{voucher_value}'] = $voucher_value;
                        $templateVars['{voucher_description}'] = $cartRule->description;
                        $templateVars['{customer_firstname}'] = $customer->firstname;
                        $templateVars['{customer_lastname}'] = $customer->lastname;

                        if (!isset($id_lang)) {
                            $id_lang = $order->id_lang;
                        }
                        if (!isset($id_shop)) {
                            $id_shop = Context::getContext()->shop->id;
                        }

                        Mail::Send($id_lang, $oc_reward->mail_template, Configuration::get('orewardtitle' . $oc_reward->id_reward, $id_lang), $templateVars, strval($customer->email), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)), null, null, dirname(__file__) . '/mails/', false, $id_shop, (Configuration::get('oc_copy') == 1 ? strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)) : null));
                    }
                }
            }
        }
    }

    public function croonJob()
    {
        if (Configuration::get('oc_cron') == 1) {
            if (Configuration::get('oc_timeframe') == 99) {
                $date_from = Configuration::get('oc_date_from');
            } else {
                $date_from = date("Y-m-d h:i:s", strtotime("-25 days"));
            }
            $date_to = date("Y-m-d h:i:s", strtotime("-" . (Configuration::get('oc_xdays') ? Configuration::get('oc_xdays') : 0) . " days"));

            foreach (Order::getOrdersIdByDate($date_from, $date_to) AS $key => $id_order) {

                $order = new Order($id_order);
                $params['newOrderStatus'] = new StdClass();
                $params['newOrderStatus']->id = $order->current_state;
                $params['id_order'] = $id_order;
                $jest = 0;
                foreach (explode(",", Configuration::get('oc_ostates')) as $k => $v) {
                    if ($v == $params['newOrderStatus']->id) {
                        $jest = 1;
                    }
                }

                $die = 0;
                if ($jest == 1) {
                    //echo "jest!";
                    $order = new Order($params['id_order']);
                    $customer = new Customer($order->id_customer);

                    if (Configuration::get('oc_timeframe') == 99) {
                        $nbofvoucehrsverify = $this->getOrdersNbByIdCustomerAndIdOrder($order->id_customer, $params['id_order']);
                        if ($nbofvoucehrsverify[0]['count'] == 0) {
                            $die = 1;
                        }
                    }

                    if (Configuration::get('oc_counter') == 2) {
                        $nbofvouchers = orewardslist::getByIdCustomer($customer->id);
                        $nbofvouchers = $nbofvouchers['count'];
                        $nbofvouchers++;
                    } else {
                        $nbofvouchers = $this->getOrdersNbByIdCustomer($order->id_customer);
                        $nbofvouchers = $nbofvouchers[0]['count'];
                        $nbofvouchersindb = orewardslist::getByIdCustomer($order->id_customer);
                        $voucherExists = oreward::getByValue($nbofvouchers);
                        $voucherExists = $voucherExists['count'];
                        $voucherExistsFull = oreward::getByValueFull($nbofvouchers);

                        if ($nbofvouchersindb['count'] == $nbofvouchers) {
                            $die = 1;
                        }
                        $alreadyAddedType = orewardslist::getByIdCustomerAndIdReward($order->id_customer, $voucherExistsFull['id_reward']);
                        if ($alreadyAddedType['count'] >= 1) {
                            $die = 1;
                        }
                    }

                    $voucherExists = oreward::getByValue($nbofvouchers);
                    $voucherExists = $voucherExists['count'];
                    $voucherExistsFull = oreward::getByValueFullArray($nbofvouchers);
                    $result = orewardslist::getByIdCustomerAndIdOrder($customer->id, $order->id);
                    $alreadyAdded = $result['count'];

                    foreach ($voucherExistsFull as $voucherExistsFull) {
                        $excluded = array();
                        if ($voucherExistsFull['exvalcat'] == 1) {
                            foreach ($order->getProductsDetail() as $k => $v) {
                                foreach (Product::getProductCategories($v['id_product']) AS $kk => $vv) {
                                    if (in_array($vv, explode(",", $voucherExistsFull['excat'])) == true && !isset($excluded[$v['id_order_detail']])) {
                                        $excluded[$v['id_order_detail']]=true;
                                        if ($voucherExistsFull['percentage_type'] == 1) {
                                            $order->total_paid = $order->total_paid - $v['total_price_tax_incl'];
                                        } elseif ($voucherExistsFull['percentage_type'] == 2) {
                                            $order->total_paid_tax_excl = $order->total_paid_tax_excl - $v['total_price_tax_excl'];
                                        } elseif ($voucherExistsFull['percentage_type'] == 3) {
                                            $order->total_products_wt = $order->total_products_wt - $v['total_price_tax_incl'];
                                        } elseif ($voucherExistsFull['percentage_type'] == 4) {
                                            $order->total_products = $order->total_products - $v['total_price_tax_excl'];
                                        }
                                    }
                                }
                            }
                        }

                        $diee = 0;
                        if ($voucherExistsFull['min'] == 1) {
                            $currency_order = new Currency($order->id_currency);
                            $currency_to = new Currency($voucherExistsFull['min_currency']);
                            if ($order->total_paid <= Tools::convertPriceFull($voucherExistsFull['min_order'], $currency_order, $currency_to)) {
                                $diee = 1;
                            }
                        }

                        if ($voucherExistsFull['max'] == 1) {
                            $currency_order = new Currency($order->id_currency);
                            $currency_to = new Currency($voucherExistsFull['max_currency']);
                            if ($order->total_paid >= Tools::convertPrice($voucherExistsFull['max_order'], $currency_order, $currency_to)) {
                                $diee = 1;
                            }
                        }

                        if ($voucherExistsFull['manufacturers'] == 1) {
                            $jest_manufacturer = 0;
                            foreach ($order->getProductsDetail() as $k => $v) {
                                if (in_array($v['id_manufacturer'], explode(",", $voucherExistsFull['manufacturers_list'])) == true) {
                                    $jest_manufacturer = 1;
                                }
                            }

                            if ($jest_manufacturer == 0) {
                                $diee = 1;
                            }
                        }

                        // CATEGORY CHECK
                        if ($voucherExistsFull['categories'] == 1) {
                            $jest_categories = 0;
                            foreach ($order->getProductsDetail() as $k => $v) {
                                foreach (Product::getProductCategories($v['product_id']) AS $kk => $vv) {
                                    if (in_array($vv, explode(",", $voucherExistsFull['categories_list'])) == true) {
                                        $jest_categories = 1;
                                    }
                                }
                            }

                            if ($jest_categories == 0) {
                                $diee = 1;
                            }
                        }

                        if ($voucherExistsFull['prod'] == 1) {
                            $has_product = 0;
                            foreach ($order->getProductsDetail() as $k => $v) {
                                if (in_array($v['id_product'], explode(",", $voucherExistsFull['prod_list'])) == true) {
                                    $has_product = 1;
                                }
                            }

                            if ($has_product == 0) {
                                $diee = 1;
                            }
                        }

                        if ($voucherExistsFull['prod_eo'] == 1) {
                            $has_product = 0;
                            foreach ($order->getProductsDetail() as $k => $v) {
                                if (in_array($v['id_product'], explode(",", $voucherExistsFull['prod_eo_list'])) == true) {
                                    $has_product = 1;
                                }
                            }

                            if ($has_product == 1) {
                                $diee = 1;
                            }
                        }


                        if ($voucherExistsFull['prod_diff_than'] == 1) {
                            $has_product = 0;
                            foreach ($order->getProductsDetail() as $k => $v) {
                                if (!in_array($v['id_product'], explode(",", $voucherExistsFull['prod_diff_than'])) == true) {
                                    $has_product = 1;
                                }
                            }

                            if ($has_product == 1) {
                                $diee = 1;
                            }
                        }


                        if ($voucherExistsFull['nocoupon'] == 1) {
                            if (count($order->getCartRules()) > 0) {
                                $diee = 1;
                            }
                        }

                        if ($voucherExistsFull['no_guests'] == 1) {
                            if ($customer->is_guest == 1) {
                                $diee = 1;
                            }
                        }

                        if (isset($customer->id)) {
                            $die_group = 0;
                            $permitted = 0;
                            foreach (Customer::getGroupsStatic($customer->id) AS $ck => $cv) {
                                $permitted_groups = explode(",", $voucherExistsFull['groups']);
                                if (in_array($cv, $permitted_groups)) {
                                    $permitted = 1;
                                }
                            }

                            if ($permitted != 1) {
                                $die_group = 1;
                            }
                        }

                        if ($alreadyAdded == 0 and $voucherExists >= 1 and $die == 0 and $diee != 1 and $die_group != 1) {
                            $oc = new ordercouponsVoucherEngine("oc" . $voucherExistsFull['id_reward']);
                            $oc_reward = new oreward($voucherExistsFull['id_reward']);

                            if ($oc_reward->minbasket == 1) {
                                $minbasket = null;
                            } elseif ($oc_reward->minbasket == 2) {
                                $minbasket = $order->total_paid;
                            } elseif ($oc_reward->minbasket == 3) {
                                $minbasket = $order->total_paid * 2;
                            }

                            if ($oc_reward->percentage == 1) {
                                if ($oc_reward->percentage_type == 3) {
                                    $order_value = $order->total_products_wt;
                                } elseif ($oc_reward->percentage_type == 4) {
                                    $order_value = $order->total_products;
                                } elseif ($oc_reward->percentage_type == 1) {
                                    $order_value = $order->total_paid_tax_incl;
                                } elseif ($oc_reward->percentage_type == 2) {
                                    $order_value = $order->total_paid_tax_excl;
                                } else {
                                    $order_value = $order->total_paid;
                                }
                                $voucher = $oc->AddVoucherCode("oc" . $voucherExistsFull['id_reward'], ($oc_reward->associate_customer == 1 ? $customer->id : null), null, null, null, ($order_value * $order->total_paid / 100), null, null, null, null, null, $minbasket, $order->id_currency, $order->id_currency);
                            } else {
                                $voucher = $oc->AddVoucherCode("oc" . $voucherExistsFull['id_reward'], ($oc_reward->associate_customer == 1 ? $customer->id : null), null, null, null, null, null, null, null, null, null, $minbasket, $order->id_currency);
                            }
                            $orewardslist = new orewardslist();
                            $orewardslist->date_add = date("Y-m-d H:i:s");
                            $orewardslist->date_upd = date("Y-m-d H:i:s");
                            $orewardslist->id_customer = $customer->id;
                            $orewardslist->id_order = $order->id;
                            $orewardslist->reward = $voucher->code;
                            $orewardslist->id_cart_rule = $voucherExistsFull['id_reward'];
                            $orewardslist->add();

                            $cartRule = new CartRule(CartRule::getIdByCode($voucher->code));
                            $voucher_value = null;
                            if ($cartRule->reduction_amount > 0) {
                                $voucher_currency = new Currency($cartRule->reduction_currency);
                                $voucher_currency_sign = $voucher_currency->sign;
                                $voucher_value = Tools::displayPrice($cartRule->reduction_amount,  $voucher_currency);
                                //$voucher_value = $cartRule->reduction_amount . " " . $voucher_currency_sign;
                                if ($cartRule->free_shipping == 1) {
                                    if ($voucher_value == null) {
                                        $voucher_value = $this->l('Free shipping');
                                    } else {
                                        $voucher_value .= " + " . $this->l('Free shipping');
                                    }
                                }
                            } elseif ($cartRule->reduction_percent > 0) {
                                $voucher_value = $cartRule->reduction_percent . "%";
                                if ($cartRule->free_shipping == 1) {
                                    if ($voucher_value == null) {
                                        $voucher_value = $this->l('Free shipping');
                                    } else {
                                        $voucher_value .= " + " . $this->l('Free shipping');
                                    }
                                }
                            } elseif ($cartRule->free_shipping == 1) {
                                if ($voucher_value == null) {
                                    $voucher_value = $this->l('Free shipping');
                                } else {
                                    $voucher_value .= " + " . $this->l('Free shipping');
                                }
                            }

                            $templateVars['{order_id}'] = $order->id;
                            $templateVars['{order_reference}'] = $order->reference;
                            $templateVars['{voucher}'] = $voucher->code;
                            $templateVars['{voucher_name}'] = $voucher->name;
                            $templateVars['{voucher_date_from}'] = $cartRule->date_from;
                            $templateVars['{voucher_date_to}'] = $cartRule->date_to;
                            $templateVars['{voucher_dateonly_from}'] = date("Y-m-d", strtotime($cartRule->date_from));
                            $templateVars['{voucher_dateonly_to}'] = date("Y-m-d", strtotime($cartRule->date_to));
                            $templateVars['{voucher_value}'] = $voucher_value;
                            $templateVars['{voucher_description}'] = $cartRule->description;
                            $templateVars['{customer_firstname}'] = $customer->firstname;
                            $templateVars['{customer_lastname}'] = $customer->lastname;

                            if (!isset($id_lang)) {
                                $id_lang = $order->id_lang;
                            }
                            if (!isset($id_shop)) {
                                $id_shop = Context::getContext()->shop->id;
                            }
                            if (Mail::Send($id_lang, $oc_reward->mail_template, Configuration::get('orewardtitle' . $oc_reward->id_reward, $id_lang), $templateVars, strval($customer->email), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)), null, null, dirname(__file__) . '/mails/', false, $id_shop, (Configuration::get('oc_copy') == 1 ? strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)) : ''))) {
                                echo $this->l('customer: ') . $customer->email . ' ' . $this->l('received code: ') . $voucher->code . ", ";
                            }
                        }
                    }
                } else {
                    //echo "niema!";
                }
            }
        }
    }


    public function renderBackOfficeMenu()
    {
        $this->context->smarty->assign('ocMain', $this->renderFormStates());
        $this->context->smarty->assign('ocListVouchers', $this->renderFormListVouchers());
        $this->context->smarty->assign('ocNewReward', $this->renderFormVoucherNew());
        $this->context->smarty->assign('ocUpdates', $this->checkforupdates(0, 1));
        if (Tools::isSubmit('id_reward_pattern')) {
            $this->context->smarty->assign('ocPattern', $this->renderVoucherPatternSettings());
        } else {
            $this->context->smarty->assign('ocPattern', '');
        }
        return $this->display(__file__, 'views/templates/admin/admin.tpl');
    }

    public function renderFormOrderStates()
    {
        $explode_states = explode(',', Configuration::get('oc_ostates'));
        $this->context->smarty->assign('oc_ostates', $explode_states);
        $this->context->smarty->assign('orderStates', OrderState::getOrderStates($this->context->language->id));
        return $this->display(__file__, 'views/templates/admin/orderstates.tpl');
    }

    public function renderFormVoucherNew($getFields = false)
    {
        if ((int)Tools::getValue('id_reward', 0) != 0 && Tools::getValue('ocoupon_edit', 'false') != 'false') {
            $oreward = new oreward(Tools::getValue('id_reward', 0));
        } else {
            $oreward = new oreward();
        }

        $edit = '';
        if ((int)Tools::getValue('id_reward', 0) != 0 && Tools::getValue('ocoupon_edit', 'false') != 'false') {
            $edit = "<input type='hidden' name='ocoupon_edit'/><input type='hidden' value='" . Tools::getValue('id_reward', 0) . "' name='id_reward_edit'/><input type='hidden' value='" . Tools::getValue('id_reward', 0) . "' name='id_reward'/>";
        }


        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'internal_name',
                        'desc' => $this->l('Internal name of action, it will be visible only for you'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mail title'),
                        'name' => 'orewardtitle',
                        'required' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Email template file'),
                        'name' => 'mail_template',
                        'required' => true,
                        'lang' => false,
                        'class' => 'emailTemplateManager_selectBox',
                        'desc' => $this->l('Select email template file (located in module mails/en directory)') . '<br/><br/>' . $this->emailTemplatesManager->generateEmailTemplatesManagerButton(),
                        'options' => array(
                            'query' => $this->getMailFiles(),
                            'id' => 'name',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Orders from'),
                        'name' => 'value',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Orders to'),
                        'name' => 'value2',
                        'desc' =>
                            "<div class='alert alert-info'>" .
                            $this->l('You can specify range, for exmaple, customer will receive coupon if his/her number of orders is between values you specify in this field. Exmaple "FROM 1 TO 3", "FROM 4 TO 20"') .
                            '<br/>' .
                            $this->l('If you want to grant coupon only for first, second, third order etc. use ranges like "FROM 1 TO 1", "FROM 2 TO 2", FROM "3 TO 3"') .
                            '<br/><br/>' .
                            $this->l('Reward counter option defines how module will check permission to receive reward (if customer after order meet requiremets to get reward)') .
                            '<br/>' . '<br/>' .
                            "<strong>" . $this->l('Reward counter based on VALID ORDERS') . "</strong>" .
                            '<br/>' .
                            $this->l('This option will count all orders of user, even old ones. If you will define range: (for example) to generate reward for 0-5 orders - module will generate it only one time if number of placed orders is between 0 and 5') .
                            "</div>",
                    ),
                    array(
                        'type' => 'html',
                        'label' => "",
                        'name' => 'html1',
                        'html_content' => $edit . "<hr/><h1>" . $this->l('Order value conditions') . "</h1><div class='alert alert-info'>" . $this->l('You can enable condition that will check value of order. Module will generate coupon code only if "order total value" will meet defined maximum / minimum conditions.') . "</div>",
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Minimum order value'),
                        'name' => 'min',
                        'desc' => $this->l('Check this option if you want to send voucher code only if min order value is equal (or more) to amount:'),
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Value'),
                        'name' => 'min_order',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Currency'),
                        'name' => 'min_currency',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => Currency::getCurrencies(true, false, true),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Maximum order value'),
                        'name' => 'max',
                        'desc' => $this->l('Check this option if you want to send voucher code only if max order value is equal (or less) to amount:'),
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Value'),
                        'name' => 'max_order',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Currency'),
                        'name' => 'max_currency',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => Currency::getCurrencies(true, false, true),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'label' => "",
                        'name' => 'html2',
                        'html_content' => "<h4>" . $this->l('Exclusions') . "</h4>" .
                            "<div class='alert alert-info'>" . $this->l('If you will enable option to exclude products associated with selected categories, module will remove these products from calculation related to maximum and minimum order value conditons. Subject of the conditions will be: [new order total value] = [order total value] - [value of products associated with selected categories]') . "</div>" .
                            "<div class='alert alert-warning'>" . $this->l('The modified "Order total" value will be also a subject of generation process of voucher code value (if option to generate "Voucher value based on order value" will be enabled)') . "</div>"
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude products from selected categories'),
                        'name' => 'exvalcat',
                        'desc' => $this->l('Check this option if you want to exclude from "order total" products from defined categories. If you will enable option module will remove these products from calculation related to maximum and minimum order value conditons.'),
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Categories'),
                        'name' => 'excat',
                        'readonly' => true,
                        'desc' => $this->l('Search for category') . $this->searchTool->searchTool('category', 'excat', 'replace', true, $oreward->excat),
                        'prefix' => $this->searchTool->searchTool('category', 'excat', ''),
                    ),
                    array(
                        'type' => 'html',
                        'label' => "",
                        'name' => 'html3',
                        'html_content' => "<hr/><h1>" . $this->l('Products conditions') . "</h1><div class='alert alert-info'>" . $this->l('You can enable conditions that willl generate voucher code only if order will contain selected products') . "</div>",
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Categories'),
                        'name' => 'categories',
                        'required' => true,
                        'desc' => $this->l('Check this option if you want to send voucher code only if customer bought product associated with selected categories'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Select categories'),
                        'name' => 'categories_list',
                        'readonly' => true,
                        'desc' => $this->l('Search for category') . $this->searchTool->searchTool('category', 'categories_list', 'replace', true, $oreward->categories_list),
                        'prefix' => $this->searchTool->searchTool('category', 'categories_list', ''),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturers'),
                        'name' => 'manufacturers',
                        'required' => true,
                        'desc' => $this->l('Check this option if you want to send voucher code only if customer bought product associated with selected manufacturers'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Select manufacturers'),
                        'name' => 'manufacturers_list',
                        'readonly' => true,
                        'desc' => $this->l('Search for manufacturer') . $this->searchTool->searchTool('manufacturer', 'manufacturers_list', 'replace', true, $oreward->manufacturers_list),
                        'prefix' => $this->searchTool->searchTool('manufacturer', 'manufacturers_list', ''),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products'),
                        'name' => 'prod',
                        'required' => true,
                        'desc' => $this->l('Check this option if you want to send voucher code only if customer bought product from defined list of products. From list below select products (search for them before)'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Select products'),
                        'name' => 'prod_list',
                        'readonly' => true,
                        'desc' => $this->l('Search for product') . $this->searchTool->searchTool('product', 'prod_list', 'replace', true, $oreward->prod_list),
                        'prefix' => $this->searchTool->searchTool('product', 'prod_list', ''),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude orders with products different than'),
                        'name' => 'prod_diff_than',
                        'required' => true,
                        'desc' => $this->l('Option when active will check if order has products other than defined below. And if so:') . ' <span class="label label-danger">' . $this->l('Reward will not be generated') . '</span>',
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Select products'),
                        'name' => 'prod_diff_than_list',
                        'readonly' => true,
                        'desc' => $this->l('Search for product') . $this->searchTool->searchTool('product', 'prod_diff_than_list', 'replace', true, $oreward->prod_diff_than_list),
                        'prefix' => $this->searchTool->searchTool('product', 'prod_diff_than_list', ''),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude Products'),
                        'name' => 'prod_eo',
                        'required' => true,
                        'desc' => $this->l('This option when active will check if order has some specific product(s). And if so: ') . '<span class="label label-danger">' . $this->l('Reward will not be generated') . '</span>',
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Select products'),
                        'name' => 'prod_eo_list',
                        'readonly' => true,
                        'desc' => $this->l('Search for product') . $this->searchTool->searchTool('product', 'prod_eo_list', 'replace', true, $oreward->prod_eo_list),
                        'prefix' => $this->searchTool->searchTool('product', 'prod_eo_list', ''),
                    ),
                    array(
                        'type' => 'html',
                        'label' => $this->l('Groups of customers permitted to receive reward'),
                        'name' => 'oc_cgroup',
                        'html_content' => $this->renderFormGroups($oreward),
                    ),
                    array(
                        'type' => 'html',
                        'label' => "",
                        'name' => 'html4',
                        'html_content' => "<hr/><h1>" . $this->l('Other conditions') . "</h1>",
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Only if customer not used voucher'),
                        'name' => 'nocoupon',
                        'required' => true,
                        'desc' => $this->l('Activate this option if you want to generate reward only when order was placed without voucher code'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Generate only for customers'),
                        'name' => 'no_guests',
                        'required' => true,
                        'desc' => $this->l('Activate this option only if you want to generate reward for customers (not for guest checkout)'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'label' => $this->l(''),
                        'name' => 'html5',
                        'html_content' => "<hr/><h1>" . $this->l('Voucher settings') . "</h1>" .
                            "<div class='alert alert-warning'>" . $this->l('Please note that this is only a small part of voucher settings that you can configure. Once you will create this reward you will be able to configure settings of voucher pattern - do it, because it is required') . "</div>",
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Dynamic "Minimal basket" value'),
                        'name' => 'minbasket',
                        'required' => true,
                        'desc' => $this->l('Define what will be the minimal basket value option for generated voucher code. If you will select option to not define it automatically - module will use settings from voucher pattern code instead'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Do not define it automatically')
                                ),
                                array(
                                    'value' => '2',
                                    'name' => $this->l('Equal to order value')
                                ),
                                array(
                                    'value' => '3',
                                    'name' => $this->l('Doubled order value')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Dynamic voucher code value'),
                        'name' => 'percentage',
                        'required' => true,
                        'desc' => $this->l('This option will generate voucher code with value that will be equal to percentage value of placed order. To use this option, while you will define voucher pattern settings, please use option with action "amount value".'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('What value you want to use to calculate the voucher value?'),
                        'name' => 'percentage_type',
                        'required' => true,
                        'desc' => $this->l('Define what will be the minimal basket value option for generated voucher code. If you will select option to not define it automatically - module will use settings from voucher pattern code instead'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Order total paid (products + shipping) - tax inc.')
                                ),
                                array(
                                    'value' => '2',
                                    'name' => $this->l('Order total paid (products + shipping) - tax exc.')
                                ),
                                array(
                                    'value' => '3',
                                    'name' => $this->l('Products value - tax inc.')
                                ),
                                array(
                                    'value' => '4',
                                    'name' => $this->l('Products value - tax exc.')
                                ),
                                array(
                                    'value' => '5',
                                    'name' => $this->l('Specific product value (if it was purchased) - tax inc.')
                                ),
                                array(
                                    'value' => '6',
                                    'name' => $this->l('Specific product value (if it was purchased) - tax exc.')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Percentage value'),
                        'name' => 'percentage_value',
                        'prefix' => '%',
                        'desc' => $this->l('Define percentage value for option: "Voucher value based on order value"'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Specific product ID'),
                        'name' => 'order_value_specific_product',
                        'readonly' => true,
                        'desc' => "<div class='alert alert-info'>" . $this->l("Module will check cart if it contains this product and if so - voucher value will be equal to value of this product.\"Specific product ID\"") . "</div>" . $this->searchTool->searchTool('product', 'order_value_specific_product', 'replace', true, $oreward->specific_product),
                        'prefix' => $this->searchTool->searchTool('product', 'order_value_specific_product', 'replace'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Associate with customer account'),
                        'name' => 'associate_customer',
                        'required' => true,
                        'desc' => $this->l('Activate this option if you want to associate voucher with customer account. Only this customer will have possibility to use this reward.'),
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            )
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = 'id_ordercoupons_newVoucher';
        $helper->identifier = 'ordercoupons_newVoucher';
        $helper->submit_action = 'btnSubmit_newVoucher';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValuesVoucher(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function renderFormListVouchers()
    {
        $form = '
            <div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('Defined rewards') . '</div>' . "
            <ul class=\"oc_slides\">";
        foreach (oreward::getThemAll() as $k => $v) {
            $form .= "
               <li>
                   <div class=\"slideBody\"> 
                   <span class=\"nb\">" . $v['value'] . "</span>" . ($v['value'] != $v['value2'] ? '<span class="nb" style="background:none; color:black; padding-left:0px; padding-right:0px;">-</span><span class="nb">' . $v['value2'] . '</span>' : '') . "
                   <span class='name'>" . $v['internal_name'] . "&nbsp;</span>
                   <span class=\"coupontest\" onclick=\"oc_coupontest" . $v['id_reward'] . ".submit();\">" . $this->l('Send test email') . "</span>
                   <span class=\"label-tooltip coupon\" onclick=\"oc_coupon" . $v['id_reward'] . ".submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Set voucher code value and usage conditions') . "\"></span>
                   <span class=\"label-tooltip edit\" onclick=\"oc_couponedit" . $v['id_reward'] . ".submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Edit this reward') . "\"></span>
                   <span class=\"label-tooltip remove\" onclick=\"oc_couponremove" . $v['id_reward'] . ".submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Remove this reward') . "\"></span>" . "
                   <span class=\"label-tooltip onoff activate" . $v['active'] . "\" onclick=\"oc_couponactivate" . $v['id_reward'] . ".submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Activate / deactivate the reward') . "\"></span>" . '
                   <form name="ocouponactivate" id="oc_couponactivate' . $v['id_reward'] . '" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="ocoupon_activate" value="1"/><input type="hidden" name="id_reward" value="' . $v['id_reward'] . '"></form>

                   <form name="ocoupon" id="oc_coupon' . $v['id_reward'] . '" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="id_reward" value="' . $v['id_reward'] . '"><input type="hidden" name="id_reward_pattern" value="' . $v['id_reward'] . '"></form>
                   <form name="ocouponedit" id="oc_couponedit' . $v['id_reward'] . '" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="ocoupon_edit" value="1"/><input type="hidden" name="id_reward" value="' . $v['id_reward'] . '"></form>
                   <form name="ocouponremove" id="oc_couponremove' . $v['id_reward'] . '" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="ocoupon_remove" value="1"/><input type="hidden" name="id_reward" value="' . $v['id_reward'] . '"></form>
                   <form name="ocoupontest" id="oc_coupontest' . $v['id_reward'] . '" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="id_reward_test" value="' . $v['id_reward'] . '"></form>
                   </div>
                   ' . "
               </li>";
        }
        $form .= "</ul>
            ";
        return $form;
    }

    public function renderFormStates()
    {
        if (Tools::isSubmit('btnSubmit_ostates')) {
            $ostates = '';
            foreach (Tools::getValue('oc_ostates') as $k => $v) {
                $ostates .= $v . ",";
            }
            $ostates = substr($ostates, 0, -1);
            Configuration::updateValue("oc_ostates", $ostates);
            Configuration::updateValue("oc_counter", Tools::getValue('oc_counter'));
            Configuration::updateValue('oc_copy', Tools::getValue('oc_copy'));
            Configuration::updateValue('oc_cron', Tools::getValue('oc_cron'));
            Configuration::updateValue('oc_xdays', Tools::getValue('oc_xdays'));
            Configuration::updateValue('oc_timeframe', Tools::getValue('oc_timeframe'));
            Configuration::updateValue('oc_date_from', Tools::getValue('oc_date_from'));
            Configuration::updateValue('oc_date_to', Tools::getValue('oc_date_to'));
        }

        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'html',
                        'label' => $this->l('Select order states'),
                        'name' => 'oc_ostates',
                        'html_content' => $this->renderFormOrderStates(),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Reward counter based on'),
                        'name' => 'oc_counter',
                        'desc' => '<div class="">
                            <div class="alert alert-info">
                            ' . $this->l('Reward counter option defines how module will check permission to receive reward (if customer after order meet requiremets to get reward)') . '<br/><br/>
                            <u>' . $this->l('Reward counter based on VALID ORDERS') . '</u></br/>
                            ' . $this->l('This option will count all orders of user, even old ones.') . ' ' . $this->l('If you will define range: (for example) to generate reward for 0-5 orders - module will generate it only one time if number of placed orders is between 0 and 5') . '<br/><br/>
                            <u>' . $this->l('Reward counter based on GENERATED COUPONS') . '</u></br/>
                            ' . $this->l('This option will count rewards that customer received, it will not count old orders.') . ' ' . $this->l('If you will define range: (for example) to generate new reward for 0-5 orders - module will generate it each time the counter will fit to defined range ') . '<br/><br/>
                            </div>
                        </div>',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Valid orders')
                                ),
                                array(
                                    'value' => '2',
                                    'name' => $this->l('Generated coupons for orders')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'class' => 't',
                        'label' => $this->l('Time frame'),
                        'name' => 'oc_timeframe',
                        'desc' => $this->l('To count number of placed orders (to check privileges to receive voucher code) module will get purchases from defined time period'),
                        'values' => array(
                            array(
                                'id' => 'ordercoupons_ordersdate_0',
                                'value' => 0,
                                'label' => $this->l('All orders')
                            ),
                            array(
                                'id' => 'ordercoupons_ordersdate_99',
                                'value' => 99,
                                'label' => $this->l('Specific time frame')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Time frame') . ' ' . $this->l('[date from]'),
                        'name' => 'oc_date_from',
                        'desc' => $this->l('Set date from'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Time frame') . ' ' . $this->l('[date to]'),
                        'name' => 'oc_date_to',
                        'desc' => $this->l('Set date to'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Cron Job'),
                        'name' => 'oc_cron',
                        'desc' => $this->showCron(),
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Send reward after X days'),
                        'name' => 'oc_xdays',
                        'desc' => $this->l('Module will send reward after X days. Set value of this option to 0 to send voucher immediately'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Send copy of email with code to shop owner'),
                        'name' => 'oc_copy',
                        'desc' => $this->l('Option when active will send to shop admin a copy of each email that module sends'),
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = 'id_ordercoupons_ostates';
        $helper->identifier = 'ordercoupons_ostates';
        $helper->submit_action = 'btnSubmit_ostates';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function renderFormGroups($reward = false)
    {
        $explode_states = explode(',', ($reward == false ? array() : $reward->groups));
        $this->context->smarty->assign('oc_cgroup', $explode_states);
        $this->context->smarty->assign('oc_customerGroups', Group::getGroups($this->context->language->id, $this->context->shop->id));
        return $this->display(__file__, 'views/templates/admin/customerGroups.tpl');
    }

    public function showCron()
    {
        $croonurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri() . 'cronjob.php?key=' . $this->secure_key;
        return '<div class="alert alert-info">
                ' . $this->l('If you will turn option below on - coupons will not be send after you will change the order status on order detail page. Use this option only if you want to generate and send coupons with croon job. Add link attached below to your croon job table.') . '<br /><br />
                ' . $croonurl . '
                </div>';
    }

    public function getConfigFieldsValues()
    {
        return array(
            'oc_timeframe' => Configuration::get('oc_timeframe'),
            'oc_ostates' => Configuration::get('oc_ostates'),
            'oc_counter' => Configuration::get('oc_counter'),
            'oc_cron' => Configuration::get('oc_cron'),
            'oc_copy' => Configuration::get('oc_copy'),
            'oc_xdays' => Configuration::get('oc_xdays'),
            'oc_date_from' => Configuration::get('oc_date_from'),
            'oc_date_to' => Configuration::get('oc_date_to'),
        );
    }

    public function getConfigFieldsValuesVoucher()
    {
        if (Tools::getValue('ocoupon_edit', 'false') != 'false' && (int)Tools::getValue('id_reward', 0) != 0) {
            $oreward = new oreward(Tools::getValue('id_reward', 0));

            $orewardtitle = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $orewardtitle[$lang['id_lang']] = Configuration::get('orewardtitle' . Tools::getValue('id_reward', 0), $lang['id_lang']);
                if ($orewardtitle[$lang['id_lang']] == "") {
                    $orewardtitle[$lang['id_lang']] = $this->l('Email title');
                }
            }

            return array(
                'orewardtitle' => $orewardtitle,
                'internal_name' => $oreward->internal_name,
                'mail_template' => $oreward->mail_template,
                'value' => $oreward->value,
                'value2' => $oreward->value2,
                'min' => $oreward->min,
                'min_order' => $oreward->min_order,
                'min_currency' => $oreward->min_currency,
                'max' => $oreward->max,
                'max_order' => $oreward->max_order,
                'max_currency' => $oreward->max_currency,
                'exvalcat' => $oreward->exvalcat,
                'excat' => $oreward->excat,
                'manufacturers' => $oreward->manufacturers,
                'manufacturers_list' => $oreward->manufacturers_list,
                'prod' => $oreward->prod,
                'prod_list' => $oreward->prod_list,
                'prod_eo' => $oreward->prod_eo,
                'prod_eo_list' => $oreward->prod_eo_list,
                'prod_diff_than_list' => $oreward->prod_diff_than_list,
                'prod_diff_than' => $oreward->prod_diff_than,
                'oc_cgroup' => explode(",", $oreward->groups),
                'nocoupon' => $oreward->nocoupon,
                'no_guests' => $oreward->no_guests,
                'minbasket' => $oreward->minbasket,
                'percentage' => $oreward->percentage,
                'percentage_type' => $oreward->percentage_type,
                'percentage_value' => $oreward->percentage_value,
                'order_value_specific_product' => $oreward->specific_product,
                'associate_customer' => $oreward->associate_customer,
                'categories' => $oreward->categories,
                'categories_list' => $oreward->categories_list,
            );
        } else {
            $orewardtitle = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $orewardtitle[$lang['id_lang']] = $this->l('Email title');
            }

            return array(
                'orewardtitle' => $orewardtitle,
                'internal_name' => '',
                'mail_template' => 'oc_voucher',
                'value' => '1',
                'value2' => '5',
                'min' => '0',
                'min_order' => '',
                'min_currency' => '0',
                'max' => '0',
                'max_order' => '',
                'max_currency' => '0',
                'exvalcat' => '0',
                'excat' => '',
                'manufacturers' => 0,
                'manufacturers_list' => '',
                'prod' => 0,
                'prod_list' => '',
                'oc_cgroup' => '',
                'nocoupon' => 0,
                'no_guests' => 0,
                'minbasket' => 1,
                'percentage' => 0,
                'percentage_type' => 1,
                'percentage_value' => 0,
                'order_value_specific_product' => '',
                'associate_customer' => 1,
                'categories' => 0,
                'categories_list' => '',
            );
        }
    }

    public function renderVoucherPatternSettings()
    {
        $vn = new ordercouponsVoucherEngine("oc" . (int)Tools::getValue('id_reward'));
        $form = '
		    <form class="panel" id="oc' . (int)Tools::getValue('id_reward') . 'form" action="' . $_SERVER['REQUEST_URI'] . '" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="id_reward" value="' . Tools::getValue('id_reward') . '"><input type="hidden" name="id_reward_pattern" value="' . Tools::getValue('id_reward') . '">
                    ' . $vn->generateForm() . '
                    <div class="panel-footer">
						<button type="submit" name="save_voucher_settings" value="1" class="btn btn-default pull-right">
							<i class="process-icon-save"></i> ' . $this->l('Save') . '
						</button>
					</div>
            </form>';
        return $form;
    }

    public function renderStyles()
    {
        return '<style>
 
                .oc_slides .slideBody { overflow: hidden; }
                .oc_slides {padding-left:0px;}
                .oc_slides li {font-size:10px!important; list-style: none; margin: 0 0 4px 0; padding: 15px 10px; background-color: #F4E6C9; border: #CCCCCC solid 1px; color:#000;}
                .oc_slides li:hover {border:1px #000 dashed;}
                .oc_slides li .name {font-size:18px!important;}
                .oc_slides li .nb {color:#FFF; background:#000; padding:5px 10px; font-size:18px; font-weight:bold; margin-right:10px; }
                .oc_slides .activate0 {background:url("../modules/ordercoupons/img/pro_off.png") bottom right no-repeat; cursor:pointer;}
                .oc_slides .activate1 {background:url("../modules/ordercoupons/img/pro_on.png") bottom right no-repeat; cursor:pointer;}
                .oc_slides .onoff {margin-right:6px; margin-top:5px; opacity:0.3; position:relative;  width:52px; height:24px; display:inline-block; float:right;  cursor:pointer;}
                .oc_slides .remove {opacity:0.3; position:relative; width:32px; height:32px; display:inline-block; float:right; background:url("../modules/ordercoupons/img/trash.png") top no-repeat; cursor:pointer;}
                .oc_slides .edit {margin-right:6px; opacity:0.3; position:relative;  width:32px; height:32px; display:inline-block; float:right; background:url("../modules/ordercoupons/img/edit.png") top no-repeat; cursor:pointer;}
                .oc_slides .coupon {opacity:0.3; position:relative; width:32px; height:32px; display:inline-block; float:right; background:url("../modules/ordercoupons/img/coupon.png") top no-repeat; cursor:pointer;}
                .oc_slides .coupontest {opacity:0.6; cursor:pointer; margin-left:100px; font-size:14px;}
                .oc_slides .onoff:hover, .oc_slides .coupon:hover, .oc_slides .remove:hover, .oc_slides .edit:hover, .oc_slides .activate:hover { opacity:1.0; }
                .oc_slides .edit, .oc_slides .remove {margin-right:5px;}
                
        </style>';
    }

    public function getMailFiles()
    {
        $dir = "../modules/ordercoupons/mails/en/";
        $dh = opendir($dir);
        $files = array();
        $exists = array();
        while (false !== ($filename = readdir($dh))) {
            if ($filename != ".." && $filename != "." && $filename != "" && $filename != "index.php") {
                $explode = explode(".", $filename);
                if (!isset($exists[$explode[0]])) {
                    $exists[$explode[0]] = true;
                    $files[]['name'] = $explode[0];
                }
            }
        }
        return $files;
    }

}

if (file_exists(_PS_MODULE_DIR_ . 'ordercoupons/lib/voucherengine/engine.php')) {
    require_once _PS_MODULE_DIR_ . 'ordercoupons/lib/voucherengine/engine.php';
}

class ordercouponsUpdate extends ordercoupons
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

require_once _PS_MODULE_DIR_ . 'ordercoupons/models/orewardslist.php';
require_once _PS_MODULE_DIR_ . 'ordercoupons/models/oreward.php';

if (file_exists(_PS_MODULE_DIR_ . 'ordercoupons/lib/emailTemplatesManager/emailTemplatesManager.php')) {
    require_once _PS_MODULE_DIR_ . 'ordercoupons/lib/emailTemplatesManager/emailTemplatesManager.php';
}

if (file_exists(_PS_MODULE_DIR_ . 'ordercoupons/lib/searchTool/searchTool.php')) {
    require_once _PS_MODULE_DIR_ . 'ordercoupons/lib/searchTool/searchTool.php';
}

?>