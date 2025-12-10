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

if (!defined('_PS_VERSION_'))
    exit;


class lpreminder extends Module
{

    public function __construct()
    {
        $this->name = 'lpreminder';
        $this->tab = 'advertising_marketing';
        $this->module_key = 'dedd0e7dea4f22afb31800c30aa065d4';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();
        $this->mypresta_link = 'https://mypresta.eu/modules/advertising-and-marketing/loyalty-points-reminder.html';
        $this->checkforupdates(0, 0);
        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('Loyalty points reminder');
        $this->description = $this->l('This module sends the reminder to customers about loylaty points that are near to expire');

        $this->version = '1.4.2';
        $this->author = 'MyPresta.eu';
        $this->error = false;
        $this->valid = false;
        //$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
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
                        $actual_version = lpreminderUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (lpreminderUpdate::version($this->version) < lpreminderUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = lpreminderUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (lpreminderUpdate::version($this->version) < lpreminderUpdate::version(lpreminderUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function inconsistency($ret)
    {
        return true;
    }

    public function install()
    {
        if (!parent::install())
            return false;

        Configuration::updateValue('lpr_days', 7);


        return $this->installdb();
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $statements = array();
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}lpr` ("
            . '`id_reminder` int(10) NOT NULL AUTO_INCREMENT,'
            . '`id_loyalty` int(10) NOT NULL,'
            . '`email` VARCHAR(100),'
            . 'PRIMARY KEY (`id_reminder`)'
            . ")";

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

        if (Tools::isSubmit('submitLPR')) {
            $errors = array();
            $lpr_days = Tools::getValue('lpr_days');

            if ($lpr_days AND !Validate::isInt($lpr_days))
                $errors[] = $this->l('Days field accepts only numbers');

            if (!sizeof($errors)) {
                Configuration::updateValue('lpr_days', $lpr_days);

                $title = array();
                foreach (Language::getLanguages(true) AS $value) {
                    $title[$value['id_lang']] = Tools::getValue('LPR_TITLE_' . $value['id_lang']);
                }
                Configuration::updateValue('LPR_TITLE', $title);

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError(implode('<br />', $errors));
            }
        }
        return $output . $this->renderForm() . $this->cronJobUrl() . $this->checkforupdates(0, 1);
    }

    public function cronJobUrl()
    {
        $croonurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri() . 'cronjob.php?key=' . $this->secure_key;
        return '<div class="panel"><h3>' . $this->l('Setup a cron task to send reminders') . '</h3><div class="bootstrap">
            		<div class="alert alert-danger">
            			' . $this->l('Add this url to your cron job table to send reminders automatically') . '<br /><br />
                        ' . $croonurl . '
            		</div>
            	</div></div>';
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Days'),
                        'name' => 'lpr_days',
                        'desc' => $this->l('Module will send reminder X days before points will expire'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email title'),
                        'name' => 'LPR_TITLE',
                        'lang' => true,
                        'desc' => $this->l('Module will use this as a title of an email that is sent to customer'),
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
        $helper->submit_action = 'submitLPR';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function cronJob()
    {
        $validity_period = Configuration::get('lpr_days');
        $sql_period = '';

        if ((int)$validity_period > 0)
            $sql_period = ' AND datediff(ADDDATE(a.date_add, INTERVAL ' . Configuration::get('PS_LOYALTY_VALIDITY_PERIOD') . ' DAY),NOW()) <= ' . $validity_period;

        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'loyalty` AS a WHERE a.id_loyalty_state=2' . $sql_period . ' AND a.id_loyalty NOT IN (SELECT id_loyalty FROM `' . _DB_PREFIX_ . 'lpr`)');
        if (!$query) {
            echo "no points reminder";
        } else {
            foreach ($query AS $loyalty => $details) {
                $this->sendReminder($details);
            }
        }
    }

    public function sendReminder($details)
    {
        $order = new Order($details['id_order']);
        $customer = new Customer($details['id_customer']);
        $points = $details['points'];
        $value = Tools::convertPrice($details['points'] * Configuration::get('PS_LOYALTY_POINT_VALUE'), Configuration::get('PS_CURRENCY_DEFAULT'), Configuration::get('PS_CURRENCY_DEFAULT'));
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $expiry_date = @date("Y-m-d", date("U") + strtotime("+" . Configuration::get('PS_LOYALTY_VALIDITY_PERIOD') . " day", $details['date_add']));

        $templateVars = array();
        $templateVars['{points}'] = $points;
        $templateVars['{order_reference}'] = $order->reference;
        $templateVars['{points_value}'] = $value . " " . $currency->sign;
        $templateVars['{start_date}'] = Tools::displayDate($details['date_add']);
        $templateVars['{expiry_date}'] = Tools::displayDate($expiry_date);
        $templateVars['{first_name}'] = $customer->firstname;
        $templateVars['{last_name}'] = $customer->lastname;

        if (Mail::Send($order->id_lang, 'lpr_reminder', Configuration::get('LPR_TITLE', (int)$order->id_lang), $templateVars, strval($customer->email), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $order->id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $order->id_shop)), null, null, dirname(__file__) . '/mails/', false, $order->id_shop)) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'lpr` (id_loyalty,email) VALUES (\'' . $details['id_loyalty'] . '\',\'' . $customer->email . '\')');
        }
    }

    public function getConfigFieldsValues()
    {
        $title = array();
        foreach (Language::getLanguages(true) AS $value) {
            $title[$value['id_lang']] = Configuration::get('LPR_TITLE', $value['id_lang']);
        }
        return array(
            'lpr_days' => Tools::getValue('lpr_days', Configuration::get('lpr_days')),
            'LPR_TITLE' => $title,
        );
    }
}

class lpreminderUpdate extends lpreminder
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