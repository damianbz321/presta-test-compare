<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*/

include_once dirname(__FILE__).'/../../models/PopUpModel.php';

class AdminPopUpsProNewController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'pdpopupspro';
        $this->className = 'PopUpModel';
        $this->lang = true;
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = false;

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;
        $this->uri_location = _MODULE_DIR_.$this->table.'/';

        $this->_select = 's.name shop_name';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = a.id_shop)';

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
            'enableSelection' => array('text' => $this->l('Enable selection')),
            'disableSelection' => array('text' => $this->l('Disable selection'))
        );

        $this->list_page_type = array(
            'all' => $this->l('All pages'),
            'index' => $this->l('Home page'),
            'product' => $this->l('Product page'),
            'category' => $this->l('Category page'),
            'order-opc' => $this->l('Order OPC page'),
            'order' => $this->l('Order page'),
            'contact' => $this->l('Contact page'),
            'prices-drop' => $this->l('Prices drop page'),
            'new-products' => $this->l('New products page'),
            'best-sales' => $this->l('Best sales page'),
            'authentication' => $this->l('Authentication page'),
            'cms' => $this->l('Cms page'),
            'address' => $this->l('Address page'),
            'addresses' => $this->l('Addresses page'),
            'my-account' => $this->l('My-account page'),
            'order-confirmation' => $this->l('Order-confirmation page'),
            'manufacturer' => $this->l('Manufacturer page'),
            'sitemap' => $this->l('Sitemap page'),
            'history' => $this->l('History page'),
            'search' => $this->l('Search page'),
            'products-comparison' => $this->l('Products-comparison page'),
            'pagenotfound' => $this->l('Pagenotfound page'),
            'password' => $this->l('Password page')
        );

        $this->fields_list = array(
            'id_pdpopupspro' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'shop_name' => array(
                'title' => $this->l('Shop'),
                'filter_key' => 's!name'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'b!name'
            ),
            'width_popup' => array(
                'title' => $this->l('Width'),
                'width' => 'auto',
                'suffix' => $this->l('px'),
            ),
            'page' => array(
                'title' => $this->l('Page'),
                'width' => 'auto',
                'type' => 'select',
                'filter_key' => 'a!page',
                'list' => $this->list_page_type,
            ),
            'cookie' => array(
                'title' => $this->l('Cookie time'),
                'width' => 25,
                'suffix' => $this->l('days'),
            ),
            'show_title' => array(
                'title' => $this->l('Title'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'show_title',
            ),
            'newsletter' => array(
                'title' => $this->l('Newsletter'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'newsletter',
            ),
            'voucher_enabled' => array(
                'title' => $this->l('Voucher'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'voucher_enabled',
            ),
            'newval' => array(
                'title' => $this->l('Update'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'newval',
            ),

            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'width' => 25
            ),
            'from' => array(
                'title' => $this->l('Beginning of display'),
                'align' => 'right',
                'type' => 'datetime',
            ),
            'to' => array(
                'title' => $this->l('End of display'),
                'align' => 'right',
                'type' => 'datetime'
            ),
        );
    }
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);



        foreach ($this->_list as $k => $list) {
            if ($list['page'] == 1 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['all'];
            } elseif ($list['page'] == 2 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['index'];
            } elseif ($list['page'] == 3 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['product'];
            } elseif ($list['page'] == 4 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['category'];
            } elseif ($list['page'] == 5 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['order-opc'];
            } elseif ($list['page'] == 6 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['order'];
            } elseif ($list['page'] == 7 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['contact'];
            } elseif ($list['page'] == 8 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['prices-drop'];
            } elseif ($list['page'] == 9 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['new-products'];
            } elseif ($list['page'] == 10 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['best-sales'];
            } elseif ($list['page'] == 11 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['authentication'];
            } elseif ($list['page'] == 12 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['cms'];
            } elseif ($list['page'] == 13 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['address'];
            } elseif ($list['page'] == 14 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['addresses'];
            } elseif ($list['page'] == 15 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['my-account'];
            } elseif ($list['page'] == 16 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['order-confirmation'];
            } elseif ($list['page'] == 17 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['manufacturer'];
            } elseif ($list['page'] == 18 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['sitemap'];
            } elseif ($list['page'] == 19 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['history'];
            } elseif ($list['page'] == 20 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['search'];
            } elseif ($list['page'] == 21 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['products-comparison'];
            } elseif ($list['page'] == 22 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['pagenotfound'];
            } elseif ($list['page'] == 23 && count(explode(',', $list['page'])) == 1) {
                $this->_list[$k]['page'] = $this->list_page_type['password'];
            } elseif (count(explode(',', $list['page'])) > 1) {
                $this->_list[$k]['page'] = count(explode(',', $list['page'])).' '.$this->l('Pages selected');
            }
        }
    }


    public function renderList()
    {
        $this->displayInformation('&nbsp;<b>'.$this->l('How do I create a popup?').'</b>
			<br />
			<ul>
				<li>'.$this->l('Click "Add new pop-up" button.').'<br /></li>
				<li>'.$this->l('Fill in the fields and click "Save."').'<br /></li>
			</ul>
			<br />
			<b>'.$this->l('Configurations hints').'</b>
			<br />
			<ul>
				<li>'.$this->l('Every popup configuration combination of options: shop (multistore configuration), page type and given date range can have only one active pop-up configuration other way highest id of pop-up will be displayed.').'<br /></li>
				<li>'.$this->l('Display prioryty for pop-ups configurations is as follows: date range and page type specified other than "All pages" if there is no results then takes date range and type "all pages".').'<br /></li>
				
			</ul>

			');

        return parent::renderList();
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(array(
            $this->module->uri_location.'views/js/back.js',
        ));
    }



    public function initToolbar()
    {
        parent::initToolbar();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_customer'] = array(
                'href' => self::$currentIndex.'&addpdpopupspro&token='.$this->token,
                'desc' => $this->l('Add new pop-up', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        $cms_pages = CMS::getCMSPages($id_lang, null, true, $id_shop);
        $cms_pages_array = array();
        $cms_pages_array[] = array('id_cms' => 0,  'name'=> $this->l('Not selected'));

        foreach ($cms_pages as $page) {
            $cms_pages_array[] = array('id_cms' => $page['id_cms'], 'name' => $page['meta_title']);
        }

        // Categories form
        $root_category = Category::getRootCategory();
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);

        if (Tools::getValue('categoryBox')) {
            $selected_categories = Tools::getValue('categoryBox');
        } else {
            $selected_categories = isset($obj->selected_categories) ? explode(',', $obj->selected_categories) : array();
        }

        $this->fields_value['selected_manufacturers[]'] = isset($obj->selected_manufacturers) ? explode(',', $obj->selected_manufacturers) : 0;

        $currency = new Currency((int)(Configuration::get('PS_CURRENCY_DEFAULT')));

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('new popup'),
                'icon' => 'icon-user'
            ),
            'tabs' => array(
                    'configMain' => $this->l('Display configuration'),
                    'configMainContent' => $this->l('Content configuration'),
                    'configNewsletter' => $this->l('Newsletter configuration'),
                    'configNewsletterVoucher' => $this->l('Newsletter voucher configuration'),
            ),
            'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title / name'),
                        'desc' => $this->l('Title header / name for pop-up'),
                        'lang' => true,
                        'name' => 'name',
                        'tab' => 'configMainContent',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show title / name'),
                        'name' => 'show_title',
                        'is_bool' => true,
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Show title / name in pop-up header '),
                        'values' => array(
                            array(
                                'id' => 'show_title_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'show_title_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'tab' => 'configMain',
                        'desc' => $this->l('Set if pop-up should be active'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Shop'),
                        'name' => 'id_shop',
                        'desc' => $this->l('Select shop for that pop-up configuration'),
                        'tab' => 'configMain',
                        'options' => array(
                            'query' => Shop::getShops(true),
                            'id' => 'id_shop',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Starts from'),
                        'tab' => 'configMain',
                        'desc' => $this->l('Date pop-up start (leave empty if You dont want to use date range)'),
                        'name' => 'from'
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Ends on'),
                        'tab' => 'configMain',
                        'desc' => $this->l('Date pop-up ends (leave empty if You dont want to use date range)'),
                        'name' => 'to'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Page type'),
                        'desc' => $this->l('Select page or all pages on which pop-ups will be shown'),
                        'name' => 'page[]',
                        'tab' => 'configMain',
                        'multiple' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                'id_option' => 1,
                                'name' => $this->l('All pages')
                                ),
                                array(
                                'id_option' => 2,
                                'name' => $this->l('Index only (homepage)')
                                ),
                                array(
                                'id_option' => 3,
                                'name' => $this->l('Product page')
                                ),
                                array(
                                'id_option' => 4,
                                'name' => $this->l('Category page')
                                ),
                                array(
                                'id_option' => 5,
                                'name' => $this->l('Order OPC (1 step)')
                                ),
                                array(
                                'id_option' => 6,
                                'name' => $this->l('Order page (5 steps)')
                                ),
                                array(
                                'id_option' => 7,
                                'name' => $this->l('Contact page')
                                ),
                                array(
                                'id_option' => 8,
                                'name' => $this->l('Prices drop page')
                                ),
                                array(
                                'id_option' => 9,
                                'name' => $this->l('New products page')
                                ),
                                array(
                                'id_option' => 10,
                                'name' => $this->l('Best sales page')
                                ),
                                array(
                                'id_option' => 11,
                                'name' => $this->l('Authentication page')
                                ),
                                array(
                                'id_option' => 12,
                                'name' => $this->l('Cms page')
                                ),
                                array(
                                'id_option' => 13,
                                'name' => $this->l('Address page')
                                ),
                                array(
                                'id_option' => 14,
                                'name' => $this->l('Addresses page')
                                ),
                                array(
                                'id_option' => 15,
                                'name' => $this->l('My-account page')
                                ),
                                array(
                                'id_option' => 16,
                                'name' => $this->l('Order-confirmation page')
                                ),
                                array(
                                'id_option' => 17,
                                'name' => $this->l('Manufacturer page')
                                ),
                                array(
                                'id_option' => 18,
                                'name' => $this->l('Sitemap page')
                                ),
                                array(
                                'id_option' => 19,
                                'name' => $this->l('History page')
                                ),
                                array(
                                'id_option' => 20,
                                'name' => $this->l('Search page')
                                ),
                                array(
                                'id_option' => 21,
                                'name' => $this->l('Products-comparison page')
                                ),
                                array(
                                'id_option' => 22,
                                'name' => $this->l('Pagenotfound page')
                                ),
                                array(
                                'id_option' => 23,
                                'name' => $this->l('Password page')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Select category / categories'),
                        'name' => 'categoryBox',
                        'desc' => $this->l('If you choose Page type: Category then you can select on which ones popup will show up'),
                        'required' => false,
                        'tab' => 'configMain',
                        'tree' => array(
                            'use_search' => false,
                            'id' => 'categoryBox',
                            'use_checkbox' => true,
                            'selected_categories' => $selected_categories,
                        ),
                        //retro compat 1.5 for category tree
                        'values' => array(
                            'trads' => array(
                                'Root' => $root_category,
                                'selected' => $this->l('Selected'),
                                'Collapse All' => $this->l('Collapse All'),
                                'Expand All' => $this->l('Expand All'),
                                'Check All' => $this->l('Check All'),
                                'Uncheck All' => $this->l('Uncheck All')
                            ),
                            'selected_cat' => $selected_categories,
                            'input_name' => 'categoryBox[]',
                            'use_radio' => false,
                            'use_search' => false,
                            'disabled_categories' => array(),
                            'top_category' => Category::getTopCategory(),
                            'use_context' => true,
                        )
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Select manufacturers'),
                        'name' => 'selected_manufacturers[]',
                        'desc' => $this->l('If you choose Page type: Manufacturer then you can select on which ones popup will show up'),
                        'tab' => 'configMain',
                        'multiple' => true,
                        'options' => array(
                            'query' => self::getManufacturers($this->context->language->id, $this->context->shop->id),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Generate new cookie variable'),
                        'name' => 'newval',
                        'tab' => 'configMain',
                        'is_bool' => true,
                        'desc' => $this->l('If enabled cookie variable name will be generated, so after modification of pop-up will be showed to users even if old cookie life time do not end yet'),
                        'values' => array(
                            array(
                                'id' => 'newval_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'newval_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cookie time'),
                        'name' => 'cookie',
                        'tab' => 'configMain',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('Time in days of storing cookie. After that time windows will be showed again'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Time to close'),
                        'name' => 'time_to_close',
                        'tab' => 'configMain',
                        'suffix' => $this->l('seconds'),
                        'desc' => $this->l('Time to close a pop-up automaticly (enter 0 to disable this option)'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Time to open'),
                        'name' => 'time_to_open',
                        'tab' => 'configMain',
                        'suffix' => $this->l('seconds'),
                        'desc' => $this->l('Time to open a pop-up automaticly (enter 0 to disable this option)'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Window width when hidden'),
                        'name' => 'width_hidden',
                        'suffix' => 'px',
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Browser window width, below this width pop-up will be hidden (enter 0 to turn it off).'),
                        'size' => 20,
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Window height when hidden'),
                        'name' => 'height_hidden',
                        'suffix' => 'px',
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Browser window height, below this height pop-up will be hidden (enter 0 to turn it off).'),
                        'size' => 20,
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Width of pop-up box'),
                        'name' => 'width_popup',
                        'suffix' => 'px',
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Pop-up box width (height is calculated sum of newsletter + content + header heights).'),
                        'size' => 20,
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Height of pop-up content'),
                        'name' => 'height_content',
                        'suffix' => 'px',
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Pop-up main content height'),
                        'size' => 20,
                        ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Content background color'),
                        'name' => 'bg_color',
                        'tab' => 'configMainContent',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'background_image',
                        'label' => $this->l('Content  background image'),
                        'name' => 'bg_image',
                        'tab' => 'configMainContent',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Content background repeat'),
                        'name' => 'bg_repeat',
                        'tab' => 'configMainContent',
                        'options' => array(
                            'query' => array(array(
                                'id_option' => 3,
                                'name' => $this->l('Repeat XY')
                                ),
                            array(
                                'id_option' => 2,
                                'name' => $this->l('Repeat X')
                                ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Repeat Y')
                                ),
                            array(
                                'id_option' => 0,
                                'name' => $this->l('No repeat')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Content text color'),
                        'name' => 'txt_color',
                        'tab' => 'configMainContent',
                        'desc' => $this->l('Default text color. Can be modified in wysiwyg editor too.'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Content of popup module'),
                        'name' => 'content',
                        'tab' => 'configMainContent',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 60
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show newsletter form'),
                        'name' => 'newsletter',
                        'tab' => 'configNewsletter',
                        'is_bool' => true,
                        'desc' => $this->l('Make shure that you have instaled blocknewsletter module, is necesary for a guest subscriptions.'),
                        'values' => array(
                            array(
                                'id' => 'newsletter_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'newsletter_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Height of newsletter content'),
                        'name' => 'height_content_newsletter',
                        'tab' => 'configNewsletter',
                        'suffix' => 'px',
                        'desc' => $this->l('Height of newsletter content form.'),
                        'size' => 20,
                        ),

                    array(
                        'type' => 'color',
                        'label' => $this->l('Newsletter content background color'),
                        'name' => 'new_bg_color',
                        'tab' => 'configNewsletter',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'background_image',
                        'label' => $this->l('Newsletter background image'),
                        'name' => 'new_bg_image',
                        'tab' => 'configNewsletter',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Newsletter background repeat'),
                        'name' => 'new_bg_repeat',
                        'tab' => 'configNewsletter',
                        'options' => array(
                            'query' => array(array(
                                'id_option' => 3,
                                'name' => $this->l('Repeat XY')
                                ),
                            array(
                                'id_option' => 2,
                                'name' => $this->l('Repeat X')
                                ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Repeat Y')
                                ),
                            array(
                                'id_option' => 0,
                                'name' => $this->l('No repeat')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Newsletter text color'),
                        'name' => 'new_txt_color',
                        'tab' => 'configNewsletter',
                        'desc' => $this->l('Default text color for newsletter content.'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Newsletter input text color'),
                        'name' => 'input_txt_color',
                        'tab' => 'configNewsletter',
                        'desc' => $this->l('Text color of input field'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Newsletter input bg color'),
                        'name' => 'input_bg_color',
                        'tab' => 'configNewsletter',
                        'desc' => $this->l('Background color on input field'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Newsletter content'),
                        'name' => 'content_newsletter',
                        'tab' => 'configNewsletter',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 60
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Privacy policy'),
                        'name' => 'npc',
                        'tab' => 'configNewsletter',
                        'is_bool' => true,
                        'desc' => $this->l('Set if we should display privacy policy checkbox in popup'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('CMS page for privacy policy'),
                        'name' => 'npc_cms',
                        'tab' => 'configNewsletter',
                        'desc' => $this->l('Select CMS page for privacy policy checkbox link (aplicable only if checkbox was enabled)'),
                        'options' => array(
                            'query' => $cms_pages_array,
                            'id' => 'id_cms',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Newsletter voucher'),
                        'name' => 'voucher_enabled',
                        'tab' => 'configNewsletterVoucher',
                        'is_bool' => true,
                        'desc' => $this->l('Options allows to send voucher code for a newsletter subscription (all voucher parameters can be configured below).'),
                        'values' => array(
                            array(
                                'id' => 'voucher_enabled_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'voucher_enabled_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Voucher reduction type'),
                        'name' => 'voucher_type',
                        'tab' => 'configNewsletterVoucher',
                        'options' => array(
                            'query' => array(array(
                                'id_option' => 0,
                                'name' => $this->l('Amount')
                                ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Percent')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher value'),
                        'name' => 'voucher_value',
                        'tab' => 'configNewsletterVoucher',
                        'desc' => $this->l('Set voucher value depend on type selected'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select currency'),
                        'name' => 'voucher_reduction_currency',
                        'tab' => 'configNewsletterVoucher',
                        'desc' => $this->l('Select currency for voucher reduction value'),
                        'options' => array(
                            'query' => Currency::getCurrenciesByIdShop((int)$this->context->shop->id),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Voucher reduction amount tax'),
                        'tab' => 'configNewsletterVoucher',
                        'desc' => $this->l('Select if reduction value should be with tax or without tax'),
                        'name' => 'voucher_reduction_tax',
                        'options' => array(
                            'query' => array(array(
                                'id_option' => 0,
                                'name' => $this->l('Without tax')
                                ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('With tax')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                            )
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher validity'),
                        'name' => 'voucher_date_validity',
                        'tab' => 'configNewsletterVoucher',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('Set voucher validity value for which it can be used by customer'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher minimum amount'),
                        'name' => 'voucher_min_order',
                        'tab' => 'configNewsletterVoucher',
                        'desc' => $this->l('Set voucher minimum order amount value for which it can be used by ustomer when placing order'),
                        'size' => 30,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Voucher minimum amount currency'),
                        'name' => 'voucher_min_order_currency',
                        'tab' => 'configNewsletterVoucher',
                        'desc' => $this->l('Select currency for minimum order amount value'),
                        'options' => array(
                            'query' => Currency::getCurrenciesByIdShop((int)$this->context->shop->id),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher code prefix'),
                        'name' => 'voucher_code_prefix',
                        'tab' => 'configNewsletterVoucher',
                        'suffix' => 'prefix',
                        'desc' => $this->l('Generated unique voucher code can be prefixed by value entered here, leave empty for no prefix'),
                        'size' => 20,
                    ),
                )
        );


        $this->fields_value['page[]'] = isset($obj->page) ? explode(',', $obj->page) : 0;

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'icon' => 'process-icon-save',
            'class' => 'btn btn-default pull-right'
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }


    public static function getManufacturers($id_lang, $id_shop, $active = true)
    {
        $sql = '
        SELECT m.*
        FROM `'._DB_PREFIX_.'manufacturer` m
        LEFT JOIN `'._DB_PREFIX_.'manufacturer_shop` ms ON (ms.`id_manufacturer` = m.`id_manufacturer` AND ms.id_shop = '.(int)$id_shop.')
        left JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
        WHERE m.active = '.$active;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function postProcess()
    {
        Tools::clearSmartyCache();
        return parent::postProcess();
    }

    public function processAdd()
    {
        //d($_POST);
        if (Tools::isSubmit('submitAddpdpopupspro')) {
            if (!Tools::getValue('page') && !count(Tools::getValue('page'))) {
                $this->errors[] = $this->l('You need to select at least one page form po-pup to display.');
            }

            // Check lang fields
            // $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            // if (!Tools::getValue('name_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Name need to exist at least in default language: %1$s'), $default_language->name);
            // }
            
            // if (!Tools::getValue('content_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Main pop-up content need to exist at least in default language: %1$s'), $default_language->name);
            // }

            // if (!Tools::getValue('content_newsletter_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Content for newsletter need to exist at least in default language: %1$s'), $default_language->name);
            // }

            if (!count($this->errors)) {
                $object = new $this->className();
                $this->copyFromPost($object, $this->table);
                $object->page = count(Tools::getValue('page')) ? implode(',', Tools::getValue('page')) : '';
                $object->selected_categories = implode(',', Tools::getValue('categoryBox'));


                $object->selected_manufacturers = !empty(Tools::getValue('selected_manufacturers')) ? implode(',', Tools::getValue('selected_manufacturers')) : '';

                if (!$object->add()) {
                    $this->errors[] = $this->l('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                }
            }

            $this->errors = array_unique($this->errors);
            
            if (!empty($this->errors)) {
                // if we have errors, we stay on the form instead of going back to the list
                $this->display = 'edit';
                return false;
            }
        }
    }


    public function processUpdate()
    {
        //d($_POST);
        if (Tools::isSubmit('submitAddpdpopupspro')) {
            if (!Tools::getValue('page') && !count(Tools::getValue('page'))) {
                $this->errors[] = $this->l('You need to select at least one page form po-pup to display.');
            }

            // Check lang fields
            // $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            // if (!Tools::getValue('name_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Name need to exist at least in default language: %1$s'), $default_language->name);
            // }
            
            // if (!Tools::getValue('content_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Main pop-up content need to exist at least in default language: %1$s'), $default_language->name);
            // }

            // if (!Tools::getValue('content_newsletter_'.$default_language->id)) {
            //     $this->errors[] = sprintf($this->l('Content for newsletter need to exist at least in default language: %1$s'), $default_language->name);
            // }

            if (!sizeof($this->errors)) {
                $object = new $this->className((int)Tools::getValue('id_pdpopupspro'));
                $this->copyFromPost($object, $this->table);

                if (!empty(Tools::getValue('page'))) {
                    $object->page = sizeof(Tools::getValue('page')) ? implode(',', Tools::getValue('page')) : '';
                }

                if (!empty(Tools::getValue('categoryBox'))) {
                    $object->selected_categories = implode(',', Tools::getValue('categoryBox'));
                }

                $object->selected_manufacturers  = !empty(Tools::getValue('selected_manufacturers')) ? implode(',', Tools::getValue('selected_manufacturers')) : '';

                if (!$object->update()) {
                    $this->errors[] = $this->l('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                }
            }

            $this->errors = array_unique($this->errors);
            
            if (!empty($this->errors)) {
                // if we have errors, we stay on the form instead of going back to the list
                $this->display = 'edit';
                return false;
            }

            Tools::clearSmartyCache();
        }
    }

    /**
     * List actions
     */
    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('newsletterpdpopupspro')) {
            $id_pdpopupspro = (int)Tools::getValue('id_pdpopupspro');
            $popup = new PopUpModel($id_pdpopupspro);
            $popup->newsletter = $popup->newsletter ? 0 : 1;
            $popup->update();

            // update css
            $m = Module::getInstanceByName('pdpopupspro');
            $m->generateCss($id_pdpopupspro);
        } elseif (Tools::isSubmit('newvalpdpopupspro')) {
            $id_pdpopupspro = (int)Tools::getValue('id_pdpopupspro');
            $popup = new PopUpModel($id_pdpopupspro);
            $popup->newval = $popup->newval ? 0 : 1;
            $popup->update();
        } elseif (Tools::isSubmit('show_titlepdpopupspro')) {
            $id_pdpopupspro = (int)Tools::getValue('id_pdpopupspro');
            $popup = new PopUpModel($id_pdpopupspro);
            $popup->show_title = $popup->show_title ? 0 : 1;
            $popup->update();
        } elseif (Tools::isSubmit('voucher_enabledpdpopupspro')) {
            $id_pdpopupspro = (int)Tools::getValue('id_pdpopupspro');
            $popup = new PopUpModel($id_pdpopupspro);
            $popup->voucher_enabled = $popup->voucher_enabled ? 0 : 1;
            $popup->update();
        }
    }
}
