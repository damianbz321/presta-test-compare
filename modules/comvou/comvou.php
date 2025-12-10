<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once(dirname(__FILE__) . '/models/cunsub.php');


class comvou extends Module
{
    public function __construct()
    {
        $this->name = 'comvou';
        $this->tab = 'advertising_marketing';
        $this->module_key = 'dedd0e7dea4f22afb31800c30aa065d4';
        $this->mypresta_link = 'https://mypresta.eu/modules/advertising-and-marketing/voucher-for-product-comment.html';
        $this->need_instance = 0;
        $this->errors = array();

        $this->bootstrap = true;
        parent::__construct();
        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('Voucher code for product comments + reminders');
        $this->description = $this->l('This module sends voucher codes for product comments and reminders for pending comments');

        $this->version = '1.8.0';
        $this->author = 'MyPresta.eu';
        $this->error = false;
        $this->valid = false;

        $this->checkforupdates();
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
        $this->l('Only applicable if the voucher value is greater than the cart total. If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.');
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
    }

    public static function inconsistency($ret)
    {
        return;
    }

    public function hookactionOrderStatusUpdate($params)
    {
      return;
        $order = new Order($params['id_order']);
        $toRemind = array();
        require_once(dirname(__FILE__) . '/models/cvor.php');
        require_once(dirname(__FILE__) . '/models/cvov.php');
        if (file_exists(_PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php')) {
            require_once _PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php';
        }
        $date_from = '2000';
        $date_verify = (int)strtotime(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - Configuration::get('cvo_r_d'), date('Y'))));

        if (Configuration::get('cvo_r') == 2) {
            $orders[] = $params['id_order'];
            foreach ($orders AS $order => $id) {
                $reminder = cvor::getLastReminder($id);
                if ($reminder == false) {
                    $toRemind[$id] = new Order($id);
                } else {
                    if (Configuration::get('cvo_repeat') == 1) {
                        $date_reminder = (int)strtotime(date('Y-m-d', strtotime($reminder['deliverydate'])));
                        if ((int)$date_reminder < (int)$date_verify) {
                            $toRemind[$id] = new Order($id);
                        }
                    }
                }
            }

            foreach ($toRemind AS $id => $order) {
                if (!in_array($params['newOrderStatus']->id, explode(",", Configuration::get('cvo_orderstates')))) {
                    unset($toRemind[$id]);
                }
            }

            if (count($toRemind) > 0) {
                foreach ($toRemind AS $thisOrder) {
                    if ($this->checkPrivilegesReminder($thisOrder)) {
                        $this->sendReminder($thisOrder);
                    }
                }
            }
        }
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        //for update feature purposes
    }

    public function hookvalidateCustomerFormFields($params)
    {
        if (Configuration::get('CR_ALLOWED') != 1) {
            return;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        if (Tools::isSubmit('submitCreate') && Tools::getValue('controller') == 'identity') {
            if (Tools::getValue('cunsub', 'false') != 'false') {
                if (Tools::getValue('cunsub') == 1) {
                    $object = cunsub::returnObjectByMail(Context::getContext()->customer->email);
                    if ($object != NULL && $object != false) {
                        $object->delete();
                    }
                }
            } else {
                $object = cunsub::returnObjectByMail(Context::getContext()->customer->email);
                if ($object == NULL || $object == false) {
                    $cunsub = new cunsub();
                    $cunsub->email = Context::getContext()->customer->email;
                    $cunsub->ip = $ip_address;
                    $cunsub->unsub_date = date("Y-m-d h:i:s");
                    $cunsub->add();
                }
            }
        }
    }

    public function hookdisplayCustomerIdentityForm($params)
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        if (Tools::isSubmit('submitIdentity')) {
            if (Tools::getValue('cunsub', 'false') != 'false') {
                if (Tools::getValue('cunsub') == 1) {
                    $object = cunsub::returnObjectByMail(Context::getContext()->customer->email);
                    if ($object != NULL && $object != false) {
                        $object->delete();
                    }
                }
            } else {
                $object = cunsub::returnObjectByMail(Context::getContext()->customer->email);
                if ($object == NULL || $object == false) {
                    $cunsub = new cunsub();
                    $cunsub->email = Context::getContext()->customer->email;
                    $cunsub->ip = $ip_address;
                    $cunsub->unsub_date = date("Y-m-d h:i:s");
                    $cunsub->add();
                }
            }
        }

        return $this->display(__file__, 'views/templates/hook/identity.tpl');
    }

    public function hookdisplayCustomerAccountForm($params)
    {
        return $this->display(__file__, 'views/templates/hook/identity.tpl');
    }

    public function hookactionCustomerAccountAdd($params)
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        if (isset($params['newCustomer'])) {
            if (Tools::getValue('cunsub', 'false') != 'false') {
                if (Tools::getValue('cunsub') == 1) {
                    $object = cunsub::returnObjectByMail($params['newCustomer']->email);
                    if ($object != NULL && $object != false) {
                        $object->delete();
                    }
                }
            } else {
                $object = cunsub::returnObjectByMail($params['newCustomer']->email);
                if ($object == NULL || $object == false) {
                    $cunsub = new cunsub();
                    $cunsub->email = $params['newCustomer']->email;
                    $cunsub->ip = $ip_address;
                    $cunsub->unsub_date = date("Y-m-d h:i:s");
                    $cunsub->add();
                }
            }
        }
    }

    public function hookHeader($params)
    {
        if (Configuration::get('CR_ALLOWED') != 1) {
            return;
        }
        if (Tools::getValue('mail', 'false') != 'false' && Tools::getValue('comhash', 'false') != 'false') {
            if (Validate::isEmail(Tools::getValue('mail'))) {
                $customer = Customer::getCustomersByEmail(Tools::getValue('mail'));
                if (md5($customer[0]['email'] . $customer[0]['passwd']) == Tools::getValue('comhash')) {
                    $unsubscribed = cunsub::checkUnsubs(Tools::getValue('mail'));
                    if ($unsubscribed == false) {
                        $cunsub = new cunsub();
                        $cunsub->email = $customer[0]['email'];
                        $cunsub->unsub_date = date("Y-m-d h:i:s");
                        $cunsub->ip = $_SERVER['REMOTE_ADDR'];
                        if ($cunsub->add()) {
                            $this->context->smarty->assign('cunsub', $this->l('We registered your unsubscription from comments reminders'));
                        } else {
                            $this->context->smarty->assign('cunsub', $this->l('There is something wrong with usubscription from comments reminders.'));
                        }
                    } else {
                        $this->context->smarty->assign('cunsub', $this->l('You already unsubscribed from comments reminders or customer account does not exists'));
                    }
                    return $this->display(__FILE__, 'views/templates/hook/header.tpl');
                }
            }
        }
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
                        $actual_version = comvouUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (comvouUpdate::version($this->version) < comvouUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = comvouUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (comvouUpdate::version($this->version) < comvouUpdate::version(comvouUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function createMenu()
    {
        $tab = new Tab();
        $tab->id_parent = Tab::getIdFromClassName('AdminPriceRule');
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Vouchers for Comments');
        }
        $tab->class_name = 'AdminComVouComments';
        $tab->module = $this->name;
        $tab->add();

        $tab = new Tab();
        $tab->id_parent = Tab::getIdFromClassName('AdminOrders');
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Reminders (comments)');
        }
        $tab->class_name = 'AdminComRemComments';
        $tab->module = $this->name;
        $tab->add();


        $tab = new Tab();
        $tab->id_parent = Tab::getIdFromClassName('AdminParentCustomer');
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Comments / vouchers unsubscriptions');
        }
        $tab->class_name = 'AdminComUnsub';
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
        if (Tab::getIdFromClassName('AdminComVouComments')) {
            $idTabs[] = Tab::getIdFromClassName('AdminComVouComments');
            $idTabs[] = Tab::getIdFromClassName('AdminComRemComments');
            $idTabs[] = Tab::getIdFromClassName('AdminComUnsub');

            foreach ($idTabs as $idTab) {
                if ($idTab) {
                    $tab = new Tab($idTab);
                    $tab->delete();
                }
            }
        }

        return true;
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->createMenu() ||
            !$this->registerHook('displayCustomerIdentityForm') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('header') ||
            !$this->registerHook('actionOrderStatusUpdate') ||
            !$this->registerHook('displayCustomerAccountForm') ||
            !$this->registerHook('actionCustomerAccountAdd') ||
            !$this->registerHook('validateCustomerFormFields')

        ) {
            return false;
        }

        Configuration::updateValue('cvo_r', 0);
        Configuration::updateValue('cvo_r_d', 7);
        Configuration::updateValue('cvo_repeat', 0);
        Configuration::updateValue('cvo_v', 0);
        Configuration::updateValue('cvo_v_fn', 1);
        Configuration::updateValue('cvo_v_t', 'order');

        return $this->installdb();
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $statements = array();
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}cvor` (" . '`id_cvor` int(10) NOT NULL AUTO_INCREMENT,' . '`id_order` int(10) NOT NULL,' . '`deliverydate` DATETIME,' . '`email` VARCHAR(100),' . 'PRIMARY KEY (`id_cvor`)' . ")";
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}cvov` (" . '`id_cvov` int(10) NOT NULL AUTO_INCREMENT,' . '`id_product_comment` int(10) NOT NULL,' . '`id_product` int(10) NOT NULL,' . '`deliverydate` DATETIME,' . '`email` VARCHAR(100),' . '`voucher` VARCHAR(30),' . '`id_voucher` int(10),' . 'PRIMARY KEY (`id_cvov`)' . ")";
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}cunsub` (`id_cunsub` int(10) NOT NULL AUTO_INCREMENT, `email` VARCHAR(200), `ip` VARCHAR(200), `unsub_date` VARCHAR(200), PRIMARY KEY (`id_cunsub`))";


        foreach ($statements as $statement) {
            if (!Db:: getInstance()->Execute($statement)) {
                return false;
            }
        }
        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitComVouFilters')) {
            Configuration::updateValue('cvo_filters', Tools::getValue('cvo_filters', false));
            Configuration::updateValue('cvo_fil_manuf', implode(",", Tools::getValue('cvo_fil_manuf', array())));
        }

        if (Tools::isSubmit('submitBlockRss')) {
            $errors = array();
            $cvo_r_d = Tools::getValue('cvo_r_d');
            if ($cvo_r_d AND !Validate::isInt($cvo_r_d)) {
                $errors[] = $this->l('Days field accepts only numbers');
            }

            $array_groups_reminder = array();
            $array_groups_voucher = array();
            foreach (Group::getGroups($this->context->language->id) AS $group) {
                if (Tools::getValue('cvo_customergroups_reminder_' . $group['id_group'], 'false') != 'false') {
                    $array_groups_reminder[] = $group['id_group'];
                }
                if (Tools::getValue('cvo_customergroups_voucher_' . $group['id_group'], 'false') != 'false') {
                    $array_groups_voucher[] = $group['id_group'];
                }
            }

            $title = array();
            $titlev = array();
            foreach (Language::getLanguages(true) AS $value) {
                $title[$value['id_lang']] = Tools::getValue('CVOR_TITLE_' . $value['id_lang']);
                $titlev[$value['id_lang']] = Tools::getValue('CVOV_TITLE_' . $value['id_lang']);
            }
            Configuration::updateValue('CVOR_TITLE', $title);
            Configuration::updateValue('CVOV_TITLE', $titlev);

            if (!sizeof($errors)) {
                Configuration::updateValue('cvo_customergroups_reminder', implode(',', $array_groups_reminder));
                Configuration::updateValue('cvo_customergroups_voucher', implode(',', $array_groups_voucher));
                Configuration::updateValue('cvo_r_d', $cvo_r_d);
                Configuration::updateValue('CR_ALLOWED', Tools::getValue('CR_ALLOWED'));
                Configuration::updateValue('cvo_r', Tools::getValue('cvo_r'));
                Configuration::updateValue('cvo_r_fn', Tools::getValue('cvo_r_fn'));
                Configuration::updateValue('cvo_r_date', Tools::getValue('cvo_r_date'));
                Configuration::updateValue('cvo_repeat', Tools::getValue('cvo_repeat'));
                Configuration::updateValue('cvo_v', Tools::getValue('cvo_v'));
                Configuration::updateValue('cvo_vonlycust', Tools::getValue('cvo_vonlycust'));
                Configuration::updateValue('cvo_v_t', Tools::getValue('cvo_v_t'));
                Configuration::updateValue('cvo_v_fn', Tools::getValue('cvo_v_fn'));
                Configuration::updateValue('cvo_v_date', Tools::getValue('cvo_v_date'));
                Configuration::updateValue('cvo_img_type', Tools::getValue('cvo_img_type'));
                Configuration::updateValue('cvo_orderstates', implode(',', Tools::getValue('cvo_orderstates', array())));
                Configuration::updateValue('CVO_LIM_M_V', Tools::getValue('CVO_LIM_M_V', 0));
                Configuration::updateValue('CVO_LIM_M', Tools::getValue('CVO_LIM_M', 0));

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError(implode('<br />', $errors));
            }
        }
        return $output . $this->cronJobUrl() . $this->form() . $this->checkforupdates(0, true);
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

    public function form()
    {
        if (file_exists(_PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php')) {
            require_once _PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php';
        }

        if (Tools::isSubmit('save_voucher_settings')) {
            comvouVoucherEngine::updateVoucher(Tools::getValue('voucherPrefix'), $_POST);
        }

        $token = Tools::getAdminTokenLite('AdminModules');
        $module_link = $this->context->link->getAdminLink('AdminModules', false);
        $url = $module_link . '&configure=comvou&token=' . $token . '&tab_module=administration&module_name=comvou';
        $category_tree = '';
        $root = Category::getRootCategory();
        $cvv = new comvouVoucherEngine("cvv");

        $this->context->smarty->assign(array(
            'comvou' => $this,
            'cvv' => $cvv,
            'version' => _PS_VERSION_,
            'URL' => $url,
            'errors' => $this->errors,
            'link' => $this->context->link
        ));
        return $this->display(__FILE__, 'form.tpl');
    }

    public function cronJobUrl()
    {
        if (Module::isInstalled('revws') && Module::isEnabled('revws')) {
            $this->context->controller->informations[] = $this->l('Module will use comments database from "Revws - Product Reviews" addon by DataKick ');
        }
        if (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) {
            $this->context->controller->informations[] = $this->l('Module will use comments database from "product comments" addon');
        }
        if (Module::isInstalled('iqitreviews') && Module::isEnabled('iqitreviews')) {
            $this->context->controller->informations[] = $this->l('Module will use comments database from "IQIT REVIEWS" addon by IQIT ');
        }

        $croonurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri() . 'cronjob.php?key=' . $this->secure_key;
        return '<div class="bootstrap">
            		<div class="alert alert-info">
            			' . $this->l('Add this url to your cron job table to send reminders and vouchers automatically') . '<br />
                        ' . $croonurl . '
            		</div>
            	</div>';
    }

    public function renderForm()
    {
        $options_images = ImageType::getImagesTypes('products');


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Send comment reminder'),
                        'name' => 'cvo_r',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes, send with cron job')
                                ),
                                array(
                                    'value' => '2',
                                    'name' => $this->l('Send immediately after order status change')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Ask for permissions to send reminders'),
                        'desc' => $this->l('Option when active will ask customer about permission to send reminders + voucher codes during customer register process and in customer details settings'),
                        'name' => 'CR_ALLOWED',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Remind about comments after:'),
                        'name' => 'cvo_r_d',
                        'required' => true,
                        'class' => 'col-sm-2',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('Module will send reminder about possibility to comment after X days from purchase'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Repeat remind'),
                        'desc' => $this->l('Option - when enabled will remind customer regularly.') . '<br/>' . $this->l('Module will send reminder every') . ' <span id="reminder_days_repeat"></span> ' . $this->l('days'),
                        'name' => 'cvo_repeat',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email title'),
                        'name' => 'CVOR_TITLE',
                        'lang' => true,
                        'desc' => $this->l('Module will use this as a title of reminder email that is sent to customer'),
                    ),
                    array(
                        'type' => 'html',
                        'label' => $this->l('Select order states'),
                        'name' => 'cvo_orderstates',
                        'html_content' => $this->renderFormOrderStates(),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Group of customers'),
                        'desc' => '<div class="alert alert-warning">' . $this->l('Module will not send reminders to customers associated with at least one group selected above. If you want to deliver reminders for everyone - just do not select any group of customers') . '</div>',
                        'name' => 'cvo_customergroups_reminder',
                        'values' => array(
                            'query' => Group::getGroups($this->context->language->id),
                            'id' => 'id_group',
                            'name' => 'name',
                            'value' => 'id_group',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Send reminders about comments for products from orders:'),
                        'name' => 'cvo_r_fn',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('All previous orders')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Only for products bought after (select date)')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Date: '),
                        'name' => 'cvo_r_date',
                        'required' => true,
                        'lang' => false,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image size for product pictures'),
                        'name' => 'cvo_img_type',
                        'options' => array(
                            'query' => $options_images,
                            'id' => 'name',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Module will include this image size to reminder to list of products') . '<hr>',
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Send voucher code for comment'),
                        'name' => 'cvo_v',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Send voucher for customers that purchased product only'),
                        'name' => 'cvo_vonlycust',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Option when it will be enabled will send a voucher code for comments that were added by customers that purchased product'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Group of customers'),
                        'desc' => '<div class="alert alert-warning">' . $this->l('Module will not send vouchers to customers associated with at least one group selected above. If you want to deliver vouchers for everyone - just do not select any group of customers') . '</div>',
                        'name' => 'cvo_customergroups_voucher',
                        'values' => array(
                            'query' => Group::getGroups($this->context->language->id),
                            'id' => 'id_group',
                            'name' => 'name',
                            'value' => 'id_group',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('When generate voucher?'),
                        'name' => 'cvo_v_t',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => 'product',
                                    'name' => $this->l('For each product comment')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Generate vouchers for comments: '),
                        'name' => 'cvo_v_fn',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('All previously added comments')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('Only for comments added after (select date)')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Date: '),
                        'name' => 'cvo_v_date',
                        'required' => true,
                        'lang' => false
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email title'),
                        'name' => 'CVOV_TITLE',
                        'lang' => true,
                        'desc' => $this->l('Module will use this as a title of email with voucher code that is sent to customer'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Limit the number of emails'),
                        'name' => 'CVO_LIM_M',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('You can limit number of emails that module will send during one execution of cron task'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'desc' => $this->l('If you will activate option to limit number of emails delivered to customers - type here numeric value of limit'),
                        'label' => $this->l('Emails limit: '),
                        'name' => 'CVO_LIM_M_V',
                        'required' => true,
                        'lang' => false
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBlockRss';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderFormFilters()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Filters'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Enable filters'),
                        'name' => 'cvo_filters',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '0',
                                    'name' => $this->l('no')
                                ),
                                array(
                                    'value' => '1',
                                    'name' => $this->l('yes')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturers'),
                        'name' => 'cvo_fil_manuf',
                        'required' => true,
                        'size' => 10,
                        'multiple' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => Manufacturer::getManufacturers($this->context->language->id),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = 'ComVouFilters';
        $helper->submit_action = 'submitComVouFilters';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderFormOrderStates()
    {
        $explode_states = explode(',', Configuration::get('cvo_orderstates'));
        $this->context->smarty->assign('cvo_orderstates', $explode_states);
        $this->context->smarty->assign('orderStates', OrderState::getOrderStates($this->context->language->id));
        return $this->display(__file__, 'views/templates/admin/orderstates.tpl');
    }

    public function cronJob()
    {
        $products = array();
        $toRemind = array();
        require_once(dirname(__FILE__) . '/models/cvor.php');
        require_once(dirname(__FILE__) . '/models/cvov.php');
        if (file_exists(_PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php')) {
            require_once _PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php';
        }
        $date_from = '2000';
        $date_verify = (int)strtotime(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - Configuration::get('cvo_r_d'), date('Y'))));

        if (Configuration::get('cvo_r') == 1) {
            if (Configuration::get('cvo_r_fn') == 1) {
                $date_from = Configuration::get('cvo_r_date');
            }
            $orders = Order::getOrdersIdByDate($date_from, date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - Configuration::get('cvo_r_d'), date('Y'))));

            foreach ($orders AS $order => $id) {
                $reminder = cvor::getLastReminder($id);
                if ($reminder == false) {
                    $toRemind[$id] = new Order($id);
                } else {
                    if (Configuration::get('cvo_repeat') == 1) {
                        $date_reminder = (int)strtotime(date('Y-m-d', strtotime($reminder['deliverydate'])));
                        if ((int)$date_reminder < (int)$date_verify) {
                            $toRemind[$id] = new Order($id);
                        }
                    }
                }
            }


            foreach ($toRemind AS $id => $order) {
                if (!in_array($order->current_state, explode(",", Configuration::get('cvo_orderstates')))) {
                    unset($toRemind[$id]);
                }
            }


            if (count($toRemind) > 0) {
                foreach ($toRemind AS $thisOrder) {
                    if ($this->checkPrivilegesReminder($thisOrder)) {
                        $this->sendReminder($thisOrder);
                    }
                }
            }
        }

        $date_from = '2000';
        if (Configuration::get('cvo_v') == 1) {
            if (Configuration::get('cvo_v_fn') == 1) {
                $date_from = Configuration::get('cvo_v_date');
            }
            $comments = $this->getAllCommentsWithoutVoucher($date_from);


            foreach ($comments as $key => $comment) {

                if (Configuration::get('CVO_LIM_M') == 1) {
                    if (isset($lim) && is_numeric($lim)) {
                        $lim++;
                    } else {
                        $lim = 1;
                    }

                    if (Configuration::get('CVO_LIM_M_V') < $lim) {
                        return;
                    }
                }

                if (isset($comment['comment_email'])) {
                    if ($comment['comment_email'] != null && $comment['comment_email'] != NULL){
                        $comment['email'] = $comment['comment_email'];
                    }
                }

                if ($comment['email'] == NULL) {
                    if (isset($comment['customer_email'])){
                        if ($comment['customer_email'] != null) {
                            $comment['email'] = $comment['customer_email'];
                        }
                    }
                }

                if ($comment['email'] != null && $this->checkPrivilegesVoucher($comment)) {
                    $cvv = new comvouVoucherEngine("cvv");
                    $code = $cvv->addVoucherCode('cvv', $comment['id_customer']);
                    $cvov = new cvov();
                    $cvov->email = $comment['email'];
                    $cvov->id_product = $comment['id_product'];
                    $cvov->id_product_comment = $comment['id_product_comment'];
                    $cvov->deliverydate = date("Y-m-d h:i:s");
                    $cvov->voucher = $code->code;
                    $cvov->id_voucher = $code->id;
                    if ($cvov->save()) {
                        $this->sendVoucher($comment, $code);
                    }
                }
            }
        }
    }

    public function getAllCommentsWithoutVoucher($date_from)
    {
        $customers = Configuration::get('cvo_vonlycust');

        if (Module::isInstalled('revws') && Module::isEnabled('revws')) {
            // DATA KICK MODULE SUPPORT
            return (Db::getInstance()->executeS('
            SELECT  pc.`id_review` as `id_product_comment`, pc.`id_product`, c.`id_customer`, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.`display_name`) as `customer_name`, c.`email`, pc.`content`, (SELECT AVG(rr.`grade`) FROM `' . _DB_PREFIX_ . 'revws_review_grade` rr WHERE rr.`id_review` = pc.`id_review`) as `grade`, pc.`date_add`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'revws_review` pc
            ' . ($customers ? '
            INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
            ' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            WHERE pc.`date_add` > "' . $date_from . '" AND
            pc.`id_review` NOT IN (SELECT `id_product_comment` AS `id_review` FROM `' . _DB_PREFIX_ . 'cvov`)
            ORDER BY pc.`date_add` DESC'));
        }

        if (Module::isInstalled('iqitreviews') && Module::isEnabled('iqitreviews')) {
            // IQIT MODULE SUPPORT
            return (Db::getInstance()->executeS('
            SELECT pc.`id_customer`, pc.`id_review` as `id_product_comment`, pc.`id_product`, c.`id_customer`,  c.`email`, pc.`comment` as `content`, pc.`rating` as `grade`, pc.`date_add`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'iqitreviews_products` pc
            ' . ($customers ? '
            INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
            ' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            WHERE pc.`date_add` > "' . $date_from . '" AND
            pc.`id_review` NOT IN (SELECT `id_product_comment` AS `id_review` FROM `' . _DB_PREFIX_ . 'cvov`)
            ORDER BY pc.`date_add` DESC'));
        }

        $inner_join = '';
        $where_join = '';
        if (Configuration::get('cvo_filters')) {
            $inner_join = "INNER JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = pc.`id_product`) ";
            $where_join = "AND p.`id_manufacturer` IN (" . (Configuration::get('cvo_fil_manuf') != false && trim(Configuration::get('cvo_fil_manuf')) != '' ? Configuration::get('cvo_fil_manuf') : 0) . ") ";
        }


        return (Db::getInstance()->executeS('
		SELECT pc.`id_product_comment`, pc.`id_product`, c.id_customer, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, c.email, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`
		FROM `' . _DB_PREFIX_ . 'product_comment` pc
		' . ($customers ? '
		INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
        INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
		' : '') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		' . $inner_join . '
		WHERE pc.`date_add` > "' . $date_from . '"
		' . $where_join . ' AND
	    pc.`id_product_comment` NOT IN (SELECT id_product_comment FROM `' . _DB_PREFIX_ . 'cvov`)
		GROUP by pc.id_product_comment ORDER BY pc.`date_add` DESC
		ORDER BY pc.`date_add` DESC'));
    }

    public static function getAllCommentsWithoutVoucherById($id, $lang)
    {
        $customers = Configuration::get('cvo_vonlycust');

        if (Module::isInstalled('revws') && Module::isEnabled('revws')) {
            //DATAKICK MODULE SUPPORT
            return (Db::getInstance()->executeS('
            SELECT  pc.`id_review` as `id_product_comment`, pc.`id_product`, c.id_customer, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.`display_name`) as `customer_name`, c.`email`, pc.`content`, (SELECT AVG(rr.`grade`) FROM `' . _DB_PREFIX_ . 'revws_review_grade` rr WHERE rr.`id_review` = pc.`id_review`) as `grade`, pc.`date_add`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'revws_review` pc
            ' . ($customers ? '
            INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
            ' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)$lang . Shop::addSqlRestrictionOnLang('pl') . ')
            WHERE pc.`id_review` = "' . $id . '"
            ORDER BY pc.`date_add` DESC'));
        }

        if (Module::isInstalled('iqitreviews') && Module::isEnabled('iqitreviews')) {
            //IQIT MODULE SUPPORT
            return (Db::getInstance()->executeS('
            SELECT  pc.`id_review` as `id_product_comment`, pc.`id_product`, c.id_customer, c.`email`, pc.`comment` as `content`,  pc.`rating` as `grade`, pc.`date_add`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'iqitreviews_products` pc
            ' . ($customers ? '
            INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
            ' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)$lang . Shop::addSqlRestrictionOnLang('pl') . ')

            WHERE pc.`id_review` = "' . $id . '"
            ORDER BY pc.`date_add` DESC'));
        }

        $inner_join = '';
        $where_join = '';
        if (Configuration::get('cvo_filters')) {
            $inner_join = "INNER JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = pc.`id_product`) ";
            $where_join = "AND p.`id_manufacturer` IN (" . (Configuration::get('cvo_fil_manuf') != false && trim(Configuration::get('cvo_fil_manuf')) != '' ? Configuration::get('cvo_fil_manuf') : 0) . ") ";
        }

        return (Db::getInstance()->executeS('
		SELECT pc.`id_product_comment`, pc.`id_product`, c.id_customer, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, c.email, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`
		FROM `' . _DB_PREFIX_ . 'product_comment` pc
		' . ($customers ? '
		INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON pc.`id_product` = od.`product_id`
        INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON od.`id_order` = o.`id_order` AND o.`id_customer` = pc.`id_customer`
		' : '') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)$lang . Shop::addSqlRestrictionOnLang('pl') . ')
		' . $inner_join . '
		WHERE pc.`id_product_comment` = "' . $id . '"
		' . $where_join . '
		ORDER BY pc.`date_add` DESC'));
    }

    public static function getImagesByID($id_product, $limit = 1)
    {
        $id_image = Db::getInstance()->ExecuteS('SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE cover=1 AND `id_product` = ' . (int)$id_product . ' ORDER BY position ASC LIMIT 0, ' . (int)$limit);
        $toReturn = array();
        if (!$id_image) {
            return;
        } else {
            foreach ($id_image as $image) {
                $toReturn[] = $id_product . '-' . $image['id_image'];
            }
        }
        return $toReturn;
    }

    public function sendReminder($order, $addObject = true)
    {
        $verify = 0;
        $products = '<table style="text-align:left; width:100%;"><tbody><tr><th>'.$this->l('Product name').'</th><th>'.$this->l('quantity').'</th></tr>';
        foreach ($order->getProductsDetail() AS $product) {
            $images = $this->getImagesByID($product['product_id'], 1);
            $image = explode("-", $images[0]);

            $products .= "
            <tr>
                <td>
                    <table>
                    <td>
                    <img src='" . $this->context->link->getImageLink('product', $image[1], ((Configuration::get('cvo_img_type') != false && Configuration::get('cvo_img_type') != "") ? Configuration::get('cvo_img_type') : 'home_default')) . "' style='max-width:60px; max-height:60px;'/></a>
                    </td>
                    <td>
                    <a href=\"" . $this->context->link->getProductLink($product['product_id'], null, null, null, $order->id_lang) . "\">" . $product['product_name'] . "
                    </td>
                    </table>
                </td>
                <td>
                    " . $product['product_quantity'] . "
                </td>
            </tr>
            ";
            if (Configuration::get('cvo_filters')) {
                $product = new Product ($product['product_id'], false, $this->context->language->id);
                if (in_array($product->id_manufacturer, explode(",", Configuration::get('cvo_fil_manuf')))) {
                    $verify = 1;
                }
            }
        }

        $products.='</tbody></table>';

        if (Configuration::get('cvo_filters') && $verify == 0) {
            $this->context->controller->errors[] = $this->l('Reminder not delivered to customer, filters on module configuration page are active and products in this order do not meet defined filter conditions');
            return;
        }

        $customer = new Customer($order->id_customer);
        if (!$this->checkPrivilegesReminder($order)) {
            return;
        }
        if (!filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
            echo $customer->email . ' ' . $this->l('is not valid email address') . ' ' . $this->l('PrestaShop can\'t deliver email there') . '<br/>';
            return;
        }


        if (cunsub::checkUnsubs($customer->email) != false && Configuration::get('CR_ALLOWED') == 1) {
            return;
        }

        $templateVars = array();
        $templateVars['{unsubscription}'] = $this->context->link->getPageLink('index', true, $this->context->language->id, array('mail' => $customer->email, 'comhash' => md5($customer->email . $customer->passwd)));
        $templateVars['{order_reference}'] = $order->reference;
        $templateVars['{first_name}'] = $customer->firstname;
        $templateVars['{last_name}'] = $customer->lastname;
        $templateVars['{products}'] = $products;

        if (Mail::Send($order->id_lang, 'comvou_reminder', Configuration::get('CVOR_TITLE', (int)$order->id_lang), $templateVars, strval($customer->email), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $order->id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $order->id_shop)), null, null, dirname(__file__) . '/mails/', false, $order->id_shop)) {
            $cvor = new cvor;
            $cvor->id_order = $order->id;
            $cvor->email = $customer->email;
            $cvor->deliverydate = date('Y-m-d H:i:s');
            if ($addObject == true) {
                $cvor->save();
            }
        }

    }

    public function checkPrivilegesReminder($order)
    {
        $customer = new Customer($order->id_customer);
        $explode_reminder_groups = explode(',', Configuration::get('cvo_customergroups_reminder'));
        $do_not_deliver = 0;
        if (count($explode_reminder_groups) > 0) {
            foreach (Customer::getGroupsStatic($order->id_customer) AS $group) {
                if (in_array($group, $explode_reminder_groups)) {
                    $do_not_deliver = 1;
                }
            }
        }

        if ($do_not_deliver == 1) {
            $this->context->controller->errors[] = $customer->email . ' ' . $this->l('will not receive reminder, customer group excluded from reminders delivery');
            return false;
        }
        return true;
    }

    public function sendVoucher($comment, $voucher)
    {
        if (isset($comment['id_product_comment'])) {
            $comment = $comment;
        } elseif (isset($comment[0]['id_product_comment'])) {
            $comment = $comment[0];
        } else {
            return false;
        }

        if (!$this->checkPrivilegesVoucher($comment)) {
            return;
        }

        $customer = new Customer($comment['id_customer']);
        if (!filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
            echo $customer->email . ' ' . $this->l('is not valid email address') . ' ' . $this->l('PrestaShop can\'t deliver email there') . '<br/>';
            return;
        }

        if (cunsub::checkUnsubs($customer->email) != false && Configuration::get('CR_ALLOWED') == 1) {
            return;
        }

        if ($this->psversion() == 5 || $this->psversion() == 6) {
            $cartRule = new CartRule(CartRule::getIdByCode($voucher->code));
            $voucher_value = null;
            if ($cartRule->reduction_amount > 0) {
                $voucher_currency = new Currency($cartRule->reduction_currency);
                $voucher_currency_sign = $voucher_currency->sign;
                $voucher_value = $cartRule->reduction_amount . " " . $voucher_currency_sign;
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
        }

        $templateVars = array();
        $templateVars['{unsubscription}'] = $this->context->link->getPageLink('index', true, $this->context->language->id, array('mail' => $customer->email, 'comhash' => md5($customer->email . $customer->passwd)));
        $templateVars['{first_name}'] = $customer->firstname;
        $templateVars['{last_name}'] = $customer->lastname;
        $templateVars['{voucher}'] = $voucher->code;
        $templateVars['{voucher_date_from}'] = $cartRule->date_from;
        $templateVars['{voucher_date_to}'] = $cartRule->date_to;
        $templateVars['{voucher_value}'] = $voucher_value;
        $templateVars['{voucher_description}'] = $cartRule->description;

        if (Mail::Send((int)(isset($customer->id_lang) ? $customer->id_lang:Context::getContext()->language->id), 'comvoucher', Configuration::get('CVOV_TITLE', (int)(isset($customer->id_lang) ? $customer->id_lang:Context::getContext()->language->id)), $templateVars, strval($customer->email), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, (isset($customer->id_shop) ? $customer->id_shop:Context::getContext()->shop->id))), strval(Configuration::get('PS_SHOP_NAME', null, null, (isset($customer->id_shop) ? $customer->id_shop:Context::getContext()->shop->id))), null, null, dirname(__file__) . '/mails/', false, (isset($customer->id_shop) ? $customer->id_shop:Context::getContext()->shop->id)))
        {

        }
    }

    public function checkPrivilegesVoucher($comment)
    {
        $customer = new Customer($comment['id_customer']);
        $explode_voucher_groups = explode(',', Configuration::get('cvo_customergroups_voucher'));
        $do_not_deliver = 0;
        if (count($explode_voucher_groups) > 0) {
            foreach (Customer::getGroupsStatic($comment['id_customer']) AS $group) {
                if (in_array($group, $explode_voucher_groups)) {
                    $do_not_deliver = 1;
                }
            }
        }

        if ($do_not_deliver == 1) {
            $this->context->controller->errors[] = $customer->email . ' ' . $this->l('will not receive voucher, customer group excluded from vouchers delivery') . '<br/>';
            return false;
        }
        return true;
    }

    public function getConfigFieldsValues()
    {
        $array_groups_reminder = explode(",", Configuration::get('cvo_customergroups_reminder'));
        $array_groups_voucher = explode(",", Configuration::get('cvo_customergroups_voucher'));
        $array_new_groups = array();

        $title = array();
        $titlev = array();
        foreach (Language::getLanguages(true) AS $value) {
            $title[$value['id_lang']] = Configuration::get('CVOR_TITLE', $value['id_lang']);
            $titlev[$value['id_lang']] = Configuration::get('CVOV_TITLE', $value['id_lang']);
        }

        $array_return = array(
            'CR_ALLOWED' => Tools::getValue('CR_ALLOWED', Configuration::get('CR_ALLOWED')),
            'cvo_r_d' => Tools::getValue('cvo_r_d', Configuration::get('cvo_r_d')),
            'cvo_r' => Tools::getValue('cvo_r', Configuration::get('cvo_r')),
            'cvo_r_fn' => Tools::getValue('cvo_r_fn', Configuration::get('cvo_r_fn')),
            'cvo_r_date' => Tools::getValue('cvo_r_date', Configuration::get('cvo_r_date')),
            'cvo_v' => Tools::getValue('cvo_v', Configuration::get('cvo_v')),
            'cvo_vonlycust' => Tools::getValue('cvo_vonlycust', Configuration::get('cvo_vonlycust')),
            'cvo_v_t' => Tools::getValue('cvo_v_t', Configuration::get('cvo_v_t')),
            'cvo_v_fn' => Tools::getValue('cvo_v_fn', Configuration::get('cvo_v_fn')),
            'cvo_v_date' => Tools::getValue('cvo_v_date', Configuration::get('cvo_v_date')),
            'cvo_repeat' => Tools::getValue('cvo_repeat', Configuration::get('cvo_repeat')),
            'cvo_img_type' => Tools::getValue('cvo_img_type', Configuration::get('cvo_img_type')),
            'CVOR_TITLE' => $title,
            'CVOV_TITLE' => $titlev,
            'cvo_filters' => Tools::getValue('cvo_filters', Configuration::get('cvo_filters')),
            'cvo_fil_manuf[]' => explode(',', Configuration::get('cvo_fil_manuf')),
            'CVO_LIM_M' => Tools::getValue('CVO_LIM_M', Configuration::get('CVO_LIM_M')),
            'CVO_LIM_M_V' => Tools::getValue('CVO_LIM_M_V', Configuration::get('CVO_LIM_M_V')),
        );

        foreach (Group::getGroups($this->context->language->id) AS $group) {
            foreach ($array_groups_reminder AS $k => $g) {
                if ($group['id_group'] == $g) {
                    if (!isset($array_new_groups['cvo_customergroups_reminder_' . $group['id_group']])) {
                        $array_new_groups['cvo_customergroups_reminder_' . $group['id_group']] = $group['id_group'];
                    }
                }
            }
            if (!isset($array_new_groups['cvo_customergroups_reminder_' . $group['id_group']])) {
                $array_new_groups['cvo_customergroups_reminder_' . $group['id_group']] = false;
            }
            foreach ($array_groups_voucher AS $k => $g) {
                if ($group['id_group'] == $g) {
                    if (!isset($array_new_groups['cvo_customergroups_voucher_' . $group['id_group']])) {
                        $array_new_groups['cvo_customergroups_voucher_' . $group['id_group']] = $group['id_group'];
                    }
                }
            }
            if (!isset($array_new_groups['cvo_customergroups_voucher_' . $group['id_group']])) {
                $array_new_groups['cvo_customergroups_voucher_' . $group['id_group']] = false;
            }

        }
        return (array_merge($array_return, $array_new_groups));
    }
}

class comvouUpdate extends comvou
{
    public static function inconsistency($return)
    {
        return true;
    }

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
