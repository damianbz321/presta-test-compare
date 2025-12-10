<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

require_once _PS_MODULE_DIR_ . 'pla/models/plaac.php';

class pla extends Module
{
    function __construct()
    {

        $this->name = 'pla';
        $this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/product-list-attributes-combinations.html';
        $this->version = '2.0.6';
        $this->bootstrap = true;
        $this->module_key = '017a3a03323e89c09c6bbed8bda2eaf6';
        parent::__construct();

        $this->displayName = $this->l('Products list attributes table');
        $this->description = $this->l('Module displays list of all available product attributes on list of products');
        $this->checkforupdates();
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
                        $actual_version = plaUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (plaUpdate::version($this->version) < plaUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = plaUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (plaUpdate::version($this->version) < plaUpdate::version(plaUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function inconsistency($ret = false)
    {

    }

    function install()
    {
        if (parent::install() == false OR !$this->changeCore() OR
            !Configuration::updateValue('update_' . $this->name, '0') OR
            !$this->installdb() OR
            !$this->registerHook('actionAdminControllerSetMedia') OR
            !$this->registerHook('displayProductListReviews') OR
            !$this->registerHook('displayProductDeliveryTime') OR
            !$this->registerHook('header')) {
            return false;
        }
        return true;
    }

    public function installdb()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $statements = array();
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}plaac` (" . '`id_plaac` int(10) NOT NULL AUTO_INCREMENT,' . '`id_attribute` int(10) NOT NULL, `id_category` int(10) NOT NULL, `active` int(10) NOT NULL,' . ' ' . 'PRIMARY KEY (`id_plaac`)' . ")";
        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        return true;
    }

    public function uninstall()
    {
        parent::uninstall();
        $this->changeCoreUninstall();
        return true;
    }

    public function psversion($part = 1)
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

    public function getContent()
    {
        /**
         * $date_now = date("Y-m-d"); // this format is string comparable
         * if ($date_now > '2021-02-24') {
         * return 'demo expired';
         * }
         **/

        if (Tools::getValue('searchType', 'false') != 'false' && Tools::getValue('ajax') == 1) {
            if (Tools::getValue('searchType') == 'attribute') {
                echo Tools::jsonEncode($this->searchForID('attribute', 'name', trim(Tools::getValue('q')), false));
                die();
            }
        }

        $output = '';

        if (Tools::isSubmit('submitPlaSettings')) {
            $errors = array();
            if (!sizeof($errors)) {
                $this->_clearCache('*');
                Configuration::updateValue('pla_prices_tax', Tools::getValue('pla_prices_tax'));
                Configuration::updateValue('pla_ajax', Tools::getValue('pla_ajax'));
                Configuration::updateValue('pla_price', Tools::getValue('pla_price'));
                Configuration::updateValue('pla_price_logonly', Tools::getValue('pla_price_logonly'));
                Configuration::updateValue('pla_image', Tools::getValue('pla_image'));
                Configuration::updateValue('pla_defimg', Tools::getValue('pla_defimg'));
                Configuration::updateValue('pla_imagetype', Tools::getValue('pla_imagetype'));
                Configuration::updateValue('pla_addtocart', Tools::getValue('pla_addtocart'));
                Configuration::updateValue('pla_quantity', Tools::getValue('pla_quantity'));
                Configuration::updateValue('pla_control_quantity', Tools::getValue('pla_control_quantity'));
                Configuration::updateValue('pla_noattr', Tools::getValue('pla_noattr'));
                Configuration::updateValue('pla_desc', Tools::getValue('pla_desc'));
                Configuration::updateValue('pla_bulk', Tools::getValue('pla_bulk'));
                Configuration::updateValue('pla_dropdown', Tools::getValue('pla_dropdown'));
                Configuration::updateValue('pla_comb', Tools::getValue('pla_comb'));
                Configuration::updateValue('pla_comb_label', Tools::getValue('pla_comb_label'));
                Configuration::updateValue('pla_oos', Tools::getValue('pla_oos'));
                Configuration::updateValue('pla_position', Tools::getValue('pla_position'));
                Configuration::updateValue('pla_lab', Tools::getValue('pla_lab'));
                Configuration::updateValue('pla_price_before', Tools::getValue('pla_price_before'));
                Configuration::updateValue('pla_ean', Tools::getValue('pla_ean'));
                Configuration::updateValue('pla_upc', Tools::getValue('pla_upc'));
                Configuration::updateValue('pla_stock', Tools::getValue('pla_stock'));
                Configuration::updateValue('pla_reference', Tools::getValue('pla_reference'));
                Configuration::updateValue('pla_popup', Tools::getValue('pla_popup'));
                Configuration::updateValue('pla_show_th', Tools::getValue('pla_show_th'));
                Configuration::updateValue('pla_quantity_v', Tools::getValue('pla_quantity_v'));
                Configuration::updateValue('pla_addtocart_hide', Tools::getValue('pla_addtocart_hide'));
                Configuration::updateValue('pla_color_attr', Tools::getValue('pla_color_attr'));
                Configuration::updateValue('pla_color_attr_name', Tools::getValue('pla_color_attr_name'));
                Configuration::updateValue('pla_sort_by_attr_group', Tools::getValue('pla_sort_by_attr_group'));
                Configuration::updateValue('pla_sort_by_attr', Tools::getValue('pla_sort_by_attr'));

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError(implode('<br />', $errors));
            }
        }

        if (Tools::isSubmit('submitPlaDescSettings')) {
            Foreach (AttributeGroup::getAttributesGroups($this->context->language->id) AS $key => $value) {
                $field = array();
                foreach (Language::getLanguages(false) AS $key_lang => $language) {
                    $field[$language['id_lang']] = Tools::getValue('pla_desc_' . $value['id_attribute_group'] . '_' . $language['id_lang']);
                }
                Configuration::updateValue('pla_desc_' . $value['id_attribute_group'], $field);
            }
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        if (Tools::isSubmit('submitPlaVisibilitySettings')) {
            $errors = array();
            if (!sizeof($errors)) {
                $this->_clearCache('*');
                Configuration::updateValue('pla_visibility_rule', Tools::getValue('pla_visibility_rule'));
                Configuration::updateValue('pla_visibility_products', implode(",", Tools::getValue('pla_products', array())));
                Configuration::updateValue('pla_visibility_cat', implode(",", Tools::getValue('categoryBox', array())));
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError(implode('<br />', $errors));
            }
        }

        if (Tools::isSubmit('submitPlaAcSettings')) {
            $this->_clearCache('*');
            foreach (Tools::getValue('pla_hide_ca') AS $kpla => $pla) {
                foreach (Tools::getValue('categoryBox') AS $kcategory => $category) {
                    $plaac = new plaac();
                    $plaac->id_attribute = $pla;
                    $plaac->id_category = $category;
                    $plaac->active = Tools::getValue('pla_hide_ca_enabled');
                    $plaac->add();
                }
            }
            $output .= $this->displayConfirmation($this->l('New conditions created'));
        }

        if (Tools::isSubmit('submitPlaacEnable')) {
            $this->_clearCache('*');
            Configuration::updateValue('pla_hide_ca_feature', Tools::getValue('pla_hide_ca_feature'));
            $output .= $this->displayConfirmation($this->l('New conditions created'));
        }

        return $output . $this->renderForm() . $this->checkforupdates(0, 1);
    }

    public function getConfigFieldsValues()
    {
        $return = array(
            'pla_price' => Tools::getValue('pla_price', Configuration::get('pla_price')),
            'pla_price_logonly' => Tools::getValue('pla_price_logonly', Configuration::get('pla_price_logonly')),
            'pla_price_before' => Tools::getValue('pla_price_before', Configuration::get('pla_price_before')),
            'pla_image' => Tools::getValue('pla_image', Configuration::get('pla_image')),
            'pla_defimg' => Tools::getValue('pla_defimg', Configuration::get('pla_defimg')),
            'pla_imagetype' => Tools::getValue('pla_imagetype', Configuration::get('pla_imagetype')),
            'pla_ajax' => Tools::getValue('pla_ajax', Configuration::get('pla_ajax')),
            'pla_prices_tax' => Tools::getValue('pla_prices_tax', Configuration::get('pla_prices_tax')),
            'pla_addtocart' => Tools::getValue('pla_addtocart', Configuration::get('pla_addtocart')),
            'pla_quantity' => Tools::getValue('pla_quantity', Configuration::get('pla_quantity')),
            'pla_control_quantity' => Tools::getValue('pla_control_quantity', Configuration::get('pla_control_quantity')),
            'pla_noattr' => Tools::getValue('pla_noattr', Configuration::get('pla_noattr')),
            'pla_desc' => Tools::getValue('pla_desc', Configuration::get('pla_desc')),
            'pla_bulk' => Tools::getValue('pla_bulk', Configuration::get('pla_bulk')),
            'pla_dropdown' => Tools::getValue('pla_dropdown', Configuration::get('pla_dropdown')),
            'pla_comb' => Tools::getValue('pla_comb', Configuration::get('pla_comb')),
            'pla_comb_label' => Tools::getValue('pla_comb_label', Configuration::get('pla_comb_label')),
            'pla_oos' => Tools::getValue('pla_oos', Configuration::get('pla_oos')),
            'pla_position' => Tools::getValue('pla_position', Configuration::get('pla_position')),
            'pla_lab' => Tools::getValue('pla_lab', Configuration::get('pla_lab')),
            'pla_reference' => Tools::getValue('pla_reference', Configuration::get('pla_reference')),
            'pla_ean' => Tools::getValue('pla_ean', Configuration::get('pla_ean')),
            'pla_upc' => Tools::getValue('pla_upc', Configuration::get('pla_upc')),
            'pla_stock' => Tools::getValue('pla_stock', Configuration::get('pla_stock')),
            'pla_popup' => Tools::getValue('pla_popup', Configuration::get('pla_popup')),
            'pla_show_th' => Tools::getValue('pla_show_th', Configuration::get('pla_show_th')),
            'pla_quantity_v' => Tools::getValue('pla_quantity_v', Configuration::get('pla_quantity_v')),
            'pla_addtocart_hide' => Tools::getValue('pla_addtocart_hide', Configuration::get('pla_addtocart_hide')),
            'pla_color_attr' => Tools::getValue('pla_color_attr', Configuration::get('pla_color_attr')),
            'pla_color_attr_name' => Tools::getValue('pla_color_attr_name', Configuration::get('pla_color_attr_name')),
            'pla_visibility_rule' => Tools::getValue('pla_visibility_rule', Configuration::get('pla_visibility_rule')),
            'pla_hide_ca' => Tools::getValue('pla_hide_ca', Configuration::get('pla_hide_ca')),
            'pla_hide_ca_enabled' => Tools::getValue('pla_hide_ca_enabled', Configuration::get('pla_hide_ca_enabled')),
            'pla_hide_ca_feature' => Tools::getValue('pla_hide_ca_feature', Configuration::get('pla_hide_ca_feature')),
            'pla_sort_by_attr' => Tools::getValue('pla_sort_by_attr', Configuration::get('pla_sort_by_attr')),
            'pla_sort_by_attr_group' => Tools::getValue('pla_sort_by_attr_group', Configuration::get('pla_sort_by_attr_group')),
        );

        $array_of_attribute_groups = array();
        Foreach (AttributeGroup::getAttributesGroups($this->context->language->id) AS $key => $value) {
            $field = array();
            foreach (Language::getLanguages(false) AS $key_lang => $language) {
                $field[$language['id_lang']] = Tools::getValue('pla_desc_' . $value['id_attribute_group'] . '_' . $language['id_lang'], Configuration::get('pla_desc_' . $value['id_attribute_group'], $language['id_lang']));
            }
            $return = array_merge($return, array(
                'pla_desc_' . $value['id_attribute_group'] => $field
            ));
        }
        return $return;
    }

    public function renderForm()
    {
        // SETTINGS FORM
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Where to display?'),
                        'name' => 'pla_position',
                        'required' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => '1',
                                    'name' => $this->l('displayProductListReviews - default PrestaShop 1.7 hook')
                                ),
                                array(
                                    'value' => '2',
                                    'name' => $this->l('displayProductDeliveryTime - non-default PrestaShop 1.7 hook (see documentation)')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                        'desc' => '
                        <div class="alert alert-info">
                        ' . $this->l('displayProductListReviews - this is default position available in PrestaShop 1.7.') . '<br/>
                        ' . $this->l('displayProductDeliveryTime - it is not default position in PrestaShop 1.7 but it it frequently used by many themes') . '<br/>
                        </div>'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Dropdown effect / open button'),
                        'name' => 'pla_dropdown',
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
                        'desc' => $this->l('Enable this feature if you want to display table inside dropdown or if you want to show it inside popup window'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show in popup'),
                        'name' => 'pla_popup',
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
                        'desc' => $this->l('Feature when someone will try to roll out dropdown select box will open combination table in a modal popup window'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Hide popup after add to cart'),
                        'name' => 'pla_addtocart_hide',
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
                        'desc' => $this->l('Turn this option of if show available combinations inside popup and if you want to hide it (popup) once someone will add products to cart'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Display table always '),
                        'name' => 'pla_noattr',
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
                        'desc' => $this->l('Enable this feature if you want to display table even if product does not have combinations. For products without combinations module will show table with only one row (with product details)'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show table headings'),
                        'name' => 'pla_show_th',
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
                        'desc' => $this->l('Decide if you want to show or hide table\'s heading for each column in table'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Hide out of stock'),
                        'name' => 'pla_oos',
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
                        'desc' => $this->l('Enable if you want to hide combinations that are out of stock'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show reference'),
                        'name' => 'pla_reference',
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
                        'type' => 'select',
                        'label' => $this->l('Color miniature'),
                        'desc' => $this->l('If combination will be based on color attribute - module will display column with color miniature'),
                        'name' => 'pla_color_attr',
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
                        'type' => 'select',
                        'label' => $this->l('Color name instead miniature'),
                        'desc' => $this->l('When color miniature column will be enabled module will display name of the color instead of the color'),
                        'name' => 'pla_color_attr_name',
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
                        'type' => 'select',
                        'label' => $this->l('Show EAN-13'),
                        'name' => 'pla_ean',
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
                        'type' => 'select',
                        'label' => $this->l('Show UPC'),
                        'name' => 'pla_upc',
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
                        'type' => 'select',
                        'label' => $this->l('Show stock information'),
                        'name' => 'pla_stock',
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
                        'desc' => $this->l('Enable this feature if you want to display number of available items (stock)'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show combination name'),
                        'name' => 'pla_comb',
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
                        'desc' => $this->l('Enable this feature if you want to display name of the combination (for example: Size: S, Color: Blue, Material: cotton). Option if disabled will hide also attribute description'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show combination name label'),
                        'name' => 'pla_comb_label',
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
                        'desc' => $this->l('Combination label is a name of an attribute.') . '<br/>' . $this->l('Option when enabled will show combination in this way: Color: green, Material: cotton') . '<br/>' . $this->l('Option when disabled will show combination in this way: green, cotton'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show attribute description'),
                        'name' => 'pla_desc',
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
                        'desc' => $this->l('This option if enabled will show the description of attributes used in combination. You can define descriptions below this form.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Display price'),
                        'name' => 'pla_price',
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
                        'type' => 'select',
                        'label' => $this->l('Display price for logged users'),
                        'name' => 'pla_price_logonly',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Option when active will display price for logged users only'),
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
                        'label' => $this->l('Display price before reduction'),
                        'name' => 'pla_price_before',
                        'required' => true,
                        'desc' => $this->l('If product will have a special price (discount) this option decides if you want to display price before reduction (striked price)'),
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
                        'label' => $this->l('Price with tax?'),
                        'name' => 'pla_prices_tax',
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
                                ),
                                array(
                                    'value' => '3',
                                    'name' => $this->l('Use group settings')
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Enable this feature if you want include tax to prices that module will show'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show price tax label'),
                        'name' => 'pla_lab',
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
                        'desc' => $this->l('Show price type label (tax incl / tax excl)'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Add to cart'),
                        'name' => 'pla_addtocart',
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
                        'desc' => $this->l('Enable this feature if you want to enable "add to cart" button'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Bulk add to cart'),
                        'name' => 'pla_bulk',
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
                        'desc' => $this->l('If enabled customer will see only one "add to cart" button below the table. This button will add all selected combinations to cart.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Ajax add to cart'),
                        'name' => 'pla_ajax',
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
                        'desc' => $this->l('Enable this feature if you want to add to cart with AJAX feature (add to cart in background)'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Quantity field'),
                        'name' => 'pla_quantity',
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
                        'desc' => $this->l('Enable feature if you want to display quantity input field near "add to cart" button'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Quantity field default value'),
                        'name' => 'pla_quantity_v',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Set the default value of quantity input field'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Don\'t control quantity'),
                        'name' => 'pla_control_quantity',
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
                        'desc' => $this->l('Option when enabled will allow to increase quantity value above current number of available items (stock)'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Sort list of combinations by attribute'),
                        'name' => 'pla_sort_by_attr',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Sort attribute'),
                        'desc' => $this->l('Attribute can have specific order of attribute values. Select here attribute group that will be used to sort list of combinations. Order of combinations will be the same as order of selected attribute values'),
                        'name' => 'pla_sort_by_attr_group',
                        'options' => array(
                            'query' => AttributeGroup::getAttributesGroups(Context::getContext()->language->id),
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        ),
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Display product image'),
                        'name' => 'pla_image',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Select YES if you want to display product image'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Use default product image'),
                        'name' => 'pla_defimg',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('If product\'s combination will not have any image, module will show default product image')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Product image size:'),
                        'name' => 'pla_imagetype',
                        'required' => true,
                        'options' => array(
                            'query' => ImageType::getImagesTypes('products'),
                            'id' => 'name',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Select type of image that module will display'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitPlaSettings',
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        // DESCRIPTIONS FORM
        $array_of_attribute_groups = array();
        Foreach (AttributeGroup::getAttributesGroups($this->context->language->id) AS $key => $value) {
            $array_of_attribute_groups[] = array(
                'type' => 'text',
                'label' => $this->l($value['public_name']),
                'name' => 'pla_desc_' . $value['id_attribute_group'],
                'lang' => true,
            );
        }
        $fields_form2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Attributes description'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $array_of_attribute_groups,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitPlaDescSettings',
                )
            ),
        );
        $helper2 = new HelperForm();
        $helper2->show_toolbar = false;
        $helper2->default_form_language = $this->context->language->id;
        $helper2->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper2->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper2->token = Tools::getAdminTokenLite('AdminModules');
        $helper2->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        // VISIBILITY FORM
        $fields_form3 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Visibility conditions'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Module visibility for selected products'),
                        'name' => 'pla_visibility_rule',
                        'desc' => $this->returnSelectedProducts(),
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Show module for selected products')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('Show module for all products')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitPlaVisibilitySettings',
                )
            ),
        );
        $helper3 = new HelperForm();
        $helper3->show_toolbar = false;
        $helper3->default_form_language = $this->context->language->id;
        $helper3->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper3->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper3->token = Tools::getAdminTokenLite('AdminModules');
        $helper3->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        // HIDE FORM
        $fields_form4 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Hide some combinations in categories'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Condition active'),
                        'name' => 'pla_hide_ca_enabled',
                        'desc' => $this->returnSelectedCa(),
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Add'),
                    'name' => 'submitPlaAcSettings',
                )
            ),
        );
        $helper4 = new HelperForm();
        $helper4->show_toolbar = false;
        $helper4->default_form_language = $this->context->language->id;
        $helper4->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper4->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper4->token = Tools::getAdminTokenLite('AdminModules');
        $helper4->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        // HIDE FORM
        $fields_form5 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Hide some combinations in categories'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Activate feature'),
                        'name' => 'pla_hide_ca_feature',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitPlaacEnable',
                )
            ),
        );
        $helper5 = new HelperForm();
        $helper5->show_toolbar = false;
        $helper5->default_form_language = $this->context->language->id;
        $helper5->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper5->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper5->token = Tools::getAdminTokenLite('AdminModules');
        $helper5->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        $this->context->smarty->assign('pla_settings_form', $helper->generateForm(array($fields_form)));
        $this->context->smarty->assign('pla_descriptions_form', $helper2->generateForm(array($fields_form2)));
        $this->context->smarty->assign('pla_visibility_form', $helper3->generateForm(array($fields_form3)));
        $this->context->smarty->assign('pla_hide_form', $helper4->generateForm(array($fields_form4)));
        $this->context->smarty->assign('pla_hide_ca_feature', $helper5->generateForm(array($fields_form5)));
        $this->context->smarty->assign('pla_ca_list', $this->PlaacRenderList());
        $this->context->smarty->assign('pla_scripts', $this->LoadBoScripts());

        return $this->display(__file__, 'views/templates/admin/admin.tpl');
    }

    public function PlaacRenderList()
    {
        if (Tools::getValue('statusplaac', 'false') != 'false' && Tools::getValue('id_plaac', 'false') != 'false') {
            $plaacObject = new plaac(Tools::getValue('id_plaac'));
            $plaacObject->toggleStatus();
        }
        if (Tools::getValue('deleteplaac', 'false') != 'false' && Tools::getValue('id_plaac', 'false') != 'false') {
            $plaacObject = new plaac(Tools::getValue('id_plaac'));
            $plaacObject->delete();
        }

        $helper = new HelperList();
        $helper->_default_pagination = 10;
        $helper->table = 'plaac';
        $helper->no_link = true;
        $helper->actions = array('delete');
        $helper->title = $this->l('Hide combinations created with selected attributes');
        $helper->shopLinkType = '';
        $helper->module = $this;
        $helper->list_id = 'plaac';
        $helper->identifier = 'id_plaac';
        $helper->_pagination = array(10, 20, 50, 100, 300, 1000);
        $helper->orderWay = strtoupper(Tools::getValue($helper->list_id . 'Orderway', 'ASC'));
        $helper->orderBy = Tools::getValue($helper->list_id . 'Orderby', $helper->identifier);
        $alias = 'sa';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->title = $this->displayName;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&' . $helper->list_id . 'Orderway=' . Tools::getValue($helper->list_id . 'Orderway', 'asc') . '&' . $helper->list_id . 'Orderby=' . Tools::getValue($helper->list_id . 'Orderby', $helper->identifier);

        $alias_image = 'image_shop';
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : 'a.id_shop_default';
        $pagination = $helper->_default_pagination;
        if (in_array((int)Tools::getValue($helper->list_id . '_pagination'), $helper->_pagination)) {
            $pagination = (int)Tools::getValue($helper->list_id . '_pagination');
        } elseif (isset($this->context->cookie->{$helper->list_id . '_pagination'}) && $this->context->cookie->{$helper->list_id . '_pagination'}) {
            $pagination = $this->context->cookie->{$helper->list_id . '_pagination'};
        }


        /* Determine current page number */
        $page = (int)(Tools::getValue('submitFilter' . $helper->list_id, 1) > 0 ? Tools::getValue('submitFilter' . $helper->list_id, 1) - 1 : 0) * $pagination;
        if ($page == NULL) {
            $page = 0;
        }

        $helper_fields = new StdClass();
        $helper_fields->fields_list = array();
        $helper_fields->fields_list['id_plaac'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'filter' => false,
            'search' => false,
            'orderby' => false,
        );
        $helper_fields->fields_list['id_attribute'] = array(
            'title' => $this->l('Attribute'),
            'align' => 'center',
            'type' => 'int',
            'filter' => false,
            'search' => false,
            'orderby' => false,
            'callback_object' => $this,
            'callback' => 'getAttributeName',
        );
        $helper_fields->fields_list['id_category'] = array(
            'title' => $this->l('Category'),
            'align' => 'center',
            'type' => 'int',
            'filter' => false, 'search' => false, 'orderby' => false,
            'callback_object' => $this,
            'callback' => 'getCategoryName',
        );
        $helper_fields->fields_list['active'] = array(
            'title' => $this->l('Active'),
            'width' => 50,
            'orderby' => false,
            'search' => false,
            'filter' => false,
            'type' => 'bool',
            'active' => 'status',
        );

        $filters = '';
        /*
        foreach ($helper_fields->fields_list AS $name => $field) {
            if (!Tools::isSubmit('submitReset' . $helper->list_id) && $field['filter'] == true && Tools::getValue($helper->list_id . 'Filter_' . $field['filter_key']) && Tools::getValue($helper->list_id . 'Filter_' . $field['filter_key']) != '') {
                $filters .= ' AND ' . str_replace("!", ".", $field['filter_key']) . ' LIKE "%' . Tools::getValue($helper->list_id . 'Filter_' . $field['filter_key']) . '%"';
            }
        }
        */

        if (Tools::isSubmit('submitReset' . $helper->list_id)) {
            $helper->orderWay = '';
            $helper->orderBy = '';

        }

        //$orderBy = (Tools::getValue($helper->list_id . 'Orderby', $helper_fields->fields_list[$helper->identifier]['filter_key']));
        //$orderBy = str_replace("!", ".", $orderBy);

        $query = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'plaac` a GROUP BY a.id_plaac  LIMIT ' . $page . ',' . $pagination);
        $listTotal = Db::getInstance()->executeS('SELECT COUNT(*) AS count FROM `' . _DB_PREFIX_ . 'plaac` a GROUP BY a.id_plaac');

        $helper->listTotal = count($listTotal);


        return $helper->generateList($query, $helper_fields->fields_list);
    }

    public function getAttributeName($group, $row)
    {
        $attribute = new Attribute($row['id_attribute'], $this->context->language->id);
        $attributeGroup = new AttributeGroup($attribute->id_attribute_group, $this->context->language->id);
        return $attributeGroup->name . ': ' . $attribute->name;
    }

    public function getCategoryName($group, $row)
    {
        $category = new Category($row['id_category'], $this->context->language->id);
        return $category->name;
    }

    public function searchForID($table, $field, $term, $shop = false)
    {
        if ($table == 'attribute') {
            $sql = 'SELECT al.id_attribute, CONCAT(agl.name, " (", agl.public_name, "): ", al.name) AS ComputedName from ' . _DB_PREFIX_ . 'attribute_lang al inner join ' . _DB_PREFIX_ . 'attribute a ON (a.id_attribute = al.id_attribute) inner join ' . _DB_PREFIX_ . 'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group) where (al.name like "%' . psql($term) . '%" OR agl.name like "%' . psql($term) . '%" OR al.name like "%' . psql($term) . '%") AND agl.id_lang = 1 GROUP BY al.id_attribute';
            $result = Db::getInstance()->ExecuteS($sql);
            return $result;
        }
    }

    public static function returnProductName($id)
    {
        $product = new Product($id, true, context::getContext()->language->id);
        return $product->name . ' (ref: ' . $product->reference . ')';
    }

    public function returnSelectedProducts()
    {
        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('associated-categories-tree', $this->l('Categories'));
        $tree->setRootCategory($root->id);
        $tree->setUseCheckBox(true);
        $tree->setUseSearch(true);
        $tree->setSelectedCategories(explode(',', Configuration::get('pla_visibility_cat')));
        $category_tree = $tree->render();
        $this->context->smarty->assign('categoryTree', $category_tree);
        $this->context->smarty->assign('selectedProducts', (Configuration::get('pla_visibility_products') == "" || Configuration::get('pla_visibility_products') == false ? false : explode(",", Configuration::get('pla_visibility_products'))));
        return $this->display(__file__, 'views/templates/admin/selectedProducts.tpl');
    }

    public function returnSelectedCa()
    {
        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('associated-categories-tree-ca', $this->l('Categories'));
        $tree->setRootCategory($root->id);
        $tree->setUseCheckBox(true);
        $tree->setUseSearch(true);
        $tree->setSelectedCategories(explode(',', Configuration::get('pla_hide_sca')));
        $category_tree = $tree->render();
        $this->context->smarty->assign('pla_bo_link', $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&ajax=1');
        $this->context->smarty->assign('categoryTreeCa', $category_tree);
        return $this->display(__file__, 'views/templates/admin/selectedCa.tpl');
    }

    public function LoadBoScripts()
    {
        return $this->display(__file__, 'views/templates/js/pla_admin.tpl');
    }

    public function hookdisplayProductDeliveryTime($params)
    {
        /**
         * $date_now = date("Y-m-d"); // this format is string comparable
         * if ($date_now > '2021-02-24') {
         * return 'demo expired';
         * }
         **/

        if (Configuration::get('pla_position') == 2 && $_GET['controller'] != "product" && $_GET['controller'] != "order" && $_GET['controller'] != "orderopc") {
            return $this->prepareTableOptions($params);
        }
    }

    function returnTable($product)
    {
        $params = array();
        $params['product'] = $product;
        $params['product']['id_product'] = $product['id'];
        echo $this->prepareTableOptions($params);
    }

    public function changeCore()
    {
        return true;
        if (file_exists('../themes/classic/templates/catalog/_partials/miniatures/product.tpl')) {
            $data = file('../themes/classic/templates/catalog/_partials/miniatures/product.tpl');
            function replace_a_line($data)
            {
                if (stristr($data, '{hook h=\'displayProductListReviews\' product=$product}')) {
                    return "{* here original code *}\n";
                }
                return $data;
            }

            $data = array_map('replace_a_line', $data);
            file_put_contents('../themes/classic/templates/catalog/_partials/miniatures/product.tpl', implode('', $data));

            function replace_a_line_two($data)
            {
                if (stristr($data, '</article>')) {
                    return '{* new *} {hook h=\'displayProductListReviews\' product=$product} {* new *} </article> {* end *}' . "\n";
                }
                return $data;
            }

            $data = array_map('replace_a_line_two', $data);
            file_put_contents('../themes/classic/templates/catalog/_partials/miniatures/product.tpl', implode('', $data));

        }
        return true;
    }

    public function changeCoreUninstall()
    {
        return true;
        if (file_exists('../themes/classic/templates/catalog/_partials/miniatures/product.tpl')) {
            $data = file('../themes/classic/templates/catalog/_partials/miniatures/product.tpl');
            function replace_a_line($data)
            {
                if (stristr($data, "{* here original code *}")) {
                    return '{hook h=\'displayProductListReviews\' product=$product}' . "\n";
                }
                return $data;
            }

            $data = array_map('replace_a_line', $data);
            file_put_contents('../themes/classic/templates/catalog/_partials/miniatures/product.tpl', implode('', $data));

            function replace_a_line_two($data)
            {
                if (stristr($data, '{* new *} {hook h=\'displayProductListReviews\' product=$product} {* new *} </article> {* end *}')) {
                    return '</article>' . "\n";
                }
                return $data;
            }

            $data = array_map('replace_a_line_two', $data);
            file_put_contents('../themes/classic/templates/catalog/_partials/miniatures/product.tpl', implode('', $data));
        }
        return true;
    }

    public function hookdisplayProductListReviews($params)
    {
        /**
         * $date_now = date("Y-m-d"); // this format is string comparable
         * if ($date_now > '2021-02-24') {
         * return 'demo expired';
         * }
         **/

        if (Configuration::get('pla_position') == 1 && $_GET['controller'] != "product" && $_GET['controller'] != "order" && $_GET['controller'] != "orderopc") {
            return $this->prepareTableOptions($params);
        }
    }

    public function hookHeader($params)
    {
        Media::addJsDef(array(
            'cart_url' => $this->context->link->getPageLink('cart'),
            'static_token' => Tools::getToken(false),
            'pla_addtocart_hide' => (Configuration::get('pla_addtocart_hide') == 1 ? (Configuration::get('pla_popup') == 1 ? 1 : 0) : 0)
        ));
        $this->context->controller->addJS(($this->_path) . 'views/templates/js/pla.js', 'all');
        $this->context->controller->addCSS($this->_path . 'views/templates/css/' . $this->name . '.css');
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->smarty->assign('pla_addtocart_hide', Configuration::get('pla_addtocart_hide'));
        $this->context->smarty->assign('pla_popup', Configuration::get('pla_popup'));
    }

    public function prepareTableOptions($params)
    {
        $plaac_restrictions = array();
        if (Tools::getValue('controller') == 'category' && Configuration::get('pla_hide_ca_feature') == 1) {
            if (Tools::getValue('id_category', 'false') != 'false') {
                $plaac_restrictions = plaac::getAllConditions(Tools::getValue('id_category'));
            }
        }

        if (isset($params['product']['id_product'])) {
            if (Configuration::get('pla_visibility_rule') == 1) {
                $associated = true;
                if (!in_array(($params['product']['id_product']), explode(",", Configuration::get('pla_visibility_products')))) {
                    $associated = false;
                }

                if (Configuration::get('pla_visibility_cat') != '' ||
                    Configuration::get('pla_visibility_cat') != false ||
                    Configuration::get('pla_visibility_cat') != null) {
                    $exploded_categories = explode(",", Configuration::get('pla_visibility_cat'));
                    $associated_with_cat = false;
                    foreach (Product::getProductCategories($params['product']['id_product']) AS $pkey => $pval) {
                        foreach ($exploded_categories AS $exk => $exv) {
                            if ($pval == $exv) {
                                $associated_with_cat = true;
                            }
                        }
                    }
                } else {
                    $associated_with_cat = false;
                }

                if (!$associated_with_cat && !$associated) {
                    return;
                }
            }

            $id_product = $params['product']['id_product'];
            $product = new Product($id_product, true, $this->context->language->id);
            $combination_images = $product->getCombinationImages($this->context->language->id);
            $combinations = array();
            $matrix_attributes = array();
            if ($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7) {
                $fpget = $product->getAttributeCombinations($this->context->language->id);
            }

            $group_public_name = array();
            foreach ($fpget as $attr) {
                if (!isset($group_public_name[$attr['id_attribute_group']])) {
                    $group_public = new AttributeGroup($attr['id_attribute_group'], $this->context->language->id);
                    $group_public_name[$attr['id_attribute_group']] = $group_public->public_name;
                }
                $combinations[$attr['id_product_attribute']]['combination'] = $attr;
                if (!isset($combinations[$attr['id_product_attribute']]['combination_name'])) {
                    $combinations[$attr['id_product_attribute']]['combination_name'] = array();
                    $combinations[$attr['id_product_attribute']]['plaac_attributes'] = array();
                }
                $gr = new AttributeGroupCore($attr['id_attribute_group']);

                $combinations[$attr['id_product_attribute']]['plaac_attributes'][] = $attr['id_attribute'];

                if (Configuration::get('pla_desc') == 1) {
                    $desc = Configuration::get('pla_desc_' . $attr['id_attribute_group'], $this->context->language->id);
                    $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] = "<div class='pla_attr_div'>";
                    if (Configuration::get('pla_comb_label') == 1) {
                        $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] .= "<span class='pla_atr_name'><strong>" . $group_public_name[$attr['id_attribute_group']] . ":</strong> ";
                        $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] .= $attr['attribute_name'] . "</span>";
                    } else {
                        $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] .= "<span class='pla_atr_value'>" . $attr['attribute_name'] . "</span>";
                    }
                    $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] .= "<span class='pla_atr_desc'>" . ($desc != '' ? $desc : $this->l('-')) . "</span>";
                    $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] .= "</div>";
                } else {
                    if (Configuration::get('pla_comb_label') == 1) {
                        $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] = "<span class='pla_atr_name'>" . $group_public_name[$attr['id_attribute_group']] . ":</span> " . "<span class='pla_atr_value'>" . $attr['attribute_name'] . "</span>";
                    } else {
                        $combinations[$attr['id_product_attribute']]['combination_name'][$gr->position] = "<span class='pla_atr_name'>" . $attr['attribute_name'] . "</span>";
                    }
                }


                if (isset($combination_images[$attr['id_product_attribute']]['0'])) {
                    $combinations[$attr['id_product_attribute']]['image'] = $combination_images[$attr['id_product_attribute']]['0'];
                } else {
                    if (Configuration::get('pla_defimg') == 1) {
                        $img_product = $this->getImagesByID($id_product, 1);
                        if ($img_product != false) {
                            $combinations[$attr['id_product_attribute']]['image']['id_image'] = $img_product;
                        } else {
                            $combinations[$attr['id_product_attribute']]['image'] = 0;
                        }
                    } else {
                        $combinations[$attr['id_product_attribute']]['image'] = 0;
                    }
                }


                $gr = new AttributeGroupCore($attr['id_attribute_group']);
                $gr_atr = new Attribute($attr['id_attribute']);

                $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['id'] = $gr_atr->id_attribute_group;
                $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['public_name'] = $gr->public_name[$this->context->language->id];
                $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['type'] = $gr->group_type;
                $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['color'] = $gr_atr->color;
                $combinations[$attr['id_product_attribute']]['reference'] = $attr['reference'];
                $combinations[$attr['id_product_attribute']]['ean'] = $attr['ean13'];
                $combinations[$attr['id_product_attribute']]['upc'] = $attr['upc'];
                if (Module::isEnabled('minqc') == true) {
                    $combinations[$attr['id_product_attribute']]['minqc_multiply'] = self::returnMinqcMultiply($params['product']['id_product']);
                    $combinations[$attr['id_product_attribute']]['minqc_value'] = Module::getInstanceByName('minqc')::returnProductMinqty($params['product']['id_product'], $attr['id_product_attribute']);
                } else {
                    $combinations[$attr['id_product_attribute']]['minqc_multiply'] = 0;
                    $combinations[$attr['id_product_attribute']]['minqc_value'] = 1;
                }

                $combinations_sort = 0;
                $has_combinations_sort = 0;
                if (Configuration::get('pla_sort_by_attr') == 1) {
                    $combinations_sort = 1;
                    if ($attr['id_attribute_group'] == Configuration::get('pla_sort_by_attr_group')) {
                        $has_combinations_sort = 1;
                        $combinations[$attr['id_product_attribute']]['position'] = $gr_atr->position;
                        $combinations[$attr['id_product_attribute']]['position_name'] = $gr_atr->name[$this->context->language->id];
                    }
                }


                //$matrix_attributes[$gr->position][$attr['group_name']] = 1;
                //$matrix_attributes[$gr->position][$gr->public_name[$this->context->language->id]] = $gr_atr->id_attribute_group;
                $matrix_attributes[$gr->position]['name'] = $gr->public_name[$this->context->language->id];
                $matrix_attributes[$gr->position]['id'] = $gr_atr->id_attribute_group;
                $matrix_attributes[$gr->position]['id_value'] = $gr_atr->id;
                ksort($combinations[$attr['id_product_attribute']]['attributes']);
                ksort($matrix_attributes);
            }


            foreach ($combinations AS $c => $v) {
                if (is_array($combinations[$c]['combination_name'])) {
                    ksort($combinations[$c]['combination_name']);
                    $combinations[$c]['combination_name'] = implode((Configuration::get('pla_desc') == 1 ? "" : ","), $combinations[$c]['combination_name']);
                }

                if (Tools::getValue('controller') == 'category' && Configuration::get('pla_hide_ca_feature') == 1) {
                    foreach ($plaac_restrictions as $restriction_key => $restriction_value) {
                        if (in_array($restriction_value['id_attribute'], $combinations[$c]['plaac_attributes'])) {
                            unset($combinations[$c]);
                        }
                    }
                }
            }


            if (Configuration::get('pla_sort_by_attr') == 1 && $has_combinations_sort == 1) {
                usort($combinations, function ($a, $b) {
                    return $a['position'] > $b['position'];
                });
            }


            $image_size = Image::getSize(Configuration::get('pla_imagetype'));
            $this->context->smarty->assign('pla_amazzingfilter', (isset($this->context->controller->module->name) ? $this->context->controller->module->name : false));
            $this->context->smarty->assign('priceDisplay', Product::getTaxCalculationMethod((int)$this->context->customer->id));
            $this->context->smarty->assign('module', $this);
            $this->context->smarty->assign('minqc', Module::getInstanceByName('minqc'));
            $this->context->smarty->assign('link', $this->context->link);
            $this->context->smarty->assign('id_product', $id_product);
            $this->context->smarty->assign('pla_image', Configuration::get('pla_image'));
            $this->context->smarty->assign('pla_imagetype', Configuration::get('pla_imagetype'));
            $this->context->smarty->assign('pla_price', Configuration::get('pla_price'));
            $this->context->smarty->assign('pla_price_logonly', Configuration::get('pla_price_logonly'));
            $this->context->smarty->assign('pla_customer_logged', $this->context->customer->isLogged());
            $this->context->smarty->assign('pla_ver_price_customer', (Configuration::get('pla_price_logonly') ? ($this->context->customer->isLogged() ? true : false) : false));
            $this->context->smarty->assign('pla_product', $params['product']);
            $this->context->smarty->assign('allow_oosp', $product->isAvailableWhenOutOfStock((int)$product->out_of_stock));
            $this->context->smarty->assign('pla_matrix', $combinations);
            $this->context->smarty->assign('pla_ajax', Configuration::get('pla_ajax'));
            $this->context->smarty->assign('pla_prices_tax', Configuration::get('pla_prices_tax'));
            $this->context->smarty->assign('pla_addtocart', Configuration::get('pla_addtocart'));
            $this->context->smarty->assign('pla_quantity', Configuration::get('pla_quantity'));
            $this->context->smarty->assign('pla_noattr', Configuration::get('pla_noattr'));
            $this->context->smarty->assign('pla_bulk', Configuration::get('pla_bulk'));
            $this->context->smarty->assign('pla_dropdown', Configuration::get('pla_dropdown'));
            $this->context->smarty->assign('pla_comb', Configuration::get('pla_comb'));
            $this->context->smarty->assign('pla_comb_label', Configuration::get('pla_comb_label'));
            $this->context->smarty->assign('pla_oos', Configuration::get('pla_oos'));
            $this->context->smarty->assign('pla_reference', Configuration::get('pla_reference'));
            $this->context->smarty->assign('pla_ean', Configuration::get('pla_ean'));
            $this->context->smarty->assign('pla_upc', Configuration::get('pla_upc'));
            $this->context->smarty->assign('pla_stock', Configuration::get('pla_stock'));
            $this->context->smarty->assign('pla_popup', Configuration::get('pla_popup'));
            $this->context->smarty->assign('pla_show_th', Configuration::get('pla_show_th'));
            $this->context->smarty->assign('ajax', Tools::getValue('ajax'));
            $this->context->smarty->assign('pla_image_width', (isset($image_size['width']) ? $image_size['width'] : 0));
            $this->context->smarty->assign('pla_image_height', (isset($image_size['height']) ? $image_size['height'] : 0));
            $this->context->smarty->assign('pla_quantity_v', Configuration::get('pla_quantity_v'));
            $this->context->smarty->assign('pla_addtocart_hide', Configuration::get('pla_addtocart_hide'));
            $this->context->smarty->assign('pla_color_attr', Configuration::get('pla_color_attr'));
            $this->context->smarty->assign('pla_color_attr_name', Configuration::get('pla_color_attr_name'));
            $this->context->smarty->assign('pla_matrix_attributes', $matrix_attributes);
            $this->context->smarty->assign(array('content_dir' => $this->context->shop->getBaseURL(true, true)));
            $this->context->smarty->assign('col_img_dir', _PS_COL_IMG_DIR_);
            $this->context->smarty->assign('theme_col_img_dir', _THEME_COL_DIR_);


            $id_shop = $this->context->shop->id;
            $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
            return $this->context->smarty->fetch('module:pla/views/templates/hook/pla.tpl', $this->getCacheId('pla' . $product->id . '-' . $id_shop . '-' . $id_customer . '-' . $this->context->language->id . '-' . $this->context->currency->id));
        }
    }

    public static function returnMinqcMultiply($id_product)
    {

        if (Configuration::get('minqc_multiply_all') == 1) {
            return 1;
        }
        $value = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'minqc_multiply` WHERE id_product = ' . (int)$id_product . ' AND id_shop = ' . Context::getContext()->shop->id . ' LIMIT 1');
        if (is_array($value)) {
            if (count($value) > 0) {
                if (isset($value[0])) {
                    if ($value[0]['active'] == 1) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    public function getImagesByID($id_product, $limit = 1)
    {
        $id_image = Db::getInstance()->ExecuteS('SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE `id_product` = ' . (int)($id_product) . ' AND `cover` = 1 ORDER BY position ASC LIMIT 0, ' . $limit);
        $toReturn = array();

        if (!$id_image) {
            return false;
        } else {
            foreach ($id_image as $image) {
                return $image['id_image'];
            }
        }
    }
}

class plaUpdate extends pla
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

?>
