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

class PopUpModel extends ObjectModel
{
    public $id_shop;
    public $active = 0;
    public $npc = 0;
    public $npc_cms;

    public $show_title = 1;
    public $width_popup = 650;
    public $height_content = 298;
    public $height_content_newsletter = 140;
    public $width_hidden = 670;
    public $height_hidden = 470;
    public $page = '';
    public $newval = 0;
    public $cookie = 10;
    public $txt_color = '#333333';
    public $bg_color = '#ffffff';
    public $newsletter = 1;
    public $bg_image = '';
    public $bg_repeat = 0;
    public $new_txt_color = '#ffffff';
    public $new_bg_color = '#3F3F3F';
    public $input_txt_color = '#ffffff';
    public $input_bg_color = '#3C3C3C';
    public $new_bg_image = '';
    public $new_bg_repeat = 0;
    public $content;
    public $content_newsletter;
    public $name;
    public $from;
    public $to;
    public $time_to_close = 0;
    public $time_to_open = 0;

    /* Voucher */
    public $voucher_enabled = 0;
    public $voucher_value = 0;
    public $voucher_type = 0;
    public $voucher_date_validity = 7;
    public $voucher_min_order = 0;

    public $voucher_min_order_currency;
    public $voucher_reduction_tax;
    public $voucher_reduction_currency;
    public $voucher_code_prefix;

    public $selected_categories = '';

    public $selected_manufacturers = '';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'pdpopupspro',
        'primary' => 'id_pdpopupspro',
        'multilang' => true,
        'fields' => array(
            'id_shop' =>                     array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'active' =>                      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'npc' =>                         array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'npc_cms' =>                     array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'show_title' =>                  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'width_popup' =>                 array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'height_content' =>              array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'height_content_newsletter' =>   array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'width_hidden' =>                array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'height_hidden' =>               array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'page' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => false),
            'selected_categories' =>         array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => false),
            'newval' =>                      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'cookie' =>                      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'time_to_close' =>               array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'time_to_open' =>                array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'txt_color' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'bg_color' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'newsletter' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isunsignedInt', 'required' => false),
            'bg_image' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => false),
            'bg_repeat' =>                   array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'new_txt_color' =>               array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'new_bg_color' =>                array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'input_txt_color' =>             array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'new_bg_image' =>                array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => false),
            'input_bg_color' =>              array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => false),
            'new_bg_repeat' =>               array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'from' =>                        array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'to' =>                          array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'voucher_enabled' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'voucher_value' =>               array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'voucher_type' =>                array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'voucher_date_validity' =>       array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'voucher_min_order' =>           array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'voucher_min_order_currency' =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'voucher_reduction_tax' =>       array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'voucher_reduction_currency' =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'voucher_code_prefix' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false),
            

            /* Lang fields */
            'name' =>                        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128, 'required' => false),
            'content' =>                     array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 800, 'required' => false),
            'content_newsletter' =>                    array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 800, 'required' => false),
            'selected_manufacturers' =>             array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => false)
        )
    );

    public function __construct($id_pdpopupspro = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_pdpopupspro, $id_lang, $id_shop);
    }

    public function add($autodate = false, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::delete();
    }

    public function update($null_values = false)
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::update($null_values);
    }

    /**
     * Creates tables
     */
    public static function createTables()
    {
        /* PopUps configuration */
        $res = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdpopupspro` (
				`id_pdpopupspro` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `npc` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `npc_cms` int(10) unsigned NOT NULL DEFAULT \'3\',

				`show_title` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
				`width_popup` int(10) unsigned NOT NULL DEFAULT \'650\',
				`height_content` int(10) unsigned NOT NULL DEFAULT \'450\',
				`height_content_newsletter` int(10) unsigned NOT NULL DEFAULT \'190\',
				`width_hidden` int(10) unsigned NOT NULL DEFAULT \'690\',
				`height_hidden` int(10) unsigned NOT NULL DEFAULT \'560\',
				`newval` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
				`page` text,
                `selected_categories` text,
                `selected_manufacturers` text,
				`cookie` int(10) unsigned NOT NULL DEFAULT \'10\',
				`time_to_close` int(10) unsigned NOT NULL DEFAULT \'10\',
				`time_to_open` int(10) unsigned NOT NULL DEFAULT \'10\',
				`txt_color` varchar(10) NOT NULL DEFAULT \'#777777\',
				`newsletter` tinyint(1) NOT NULL DEFAULT \'0\',
				`bg_color` varchar(10) NOT NULL DEFAULT \'#ffffff\',
				`bg_image` varchar(256) NOT NULL DEFAULT \'0\',
				`bg_repeat` int(10) unsigned NOT NULL DEFAULT \'0\',
				`new_txt_color` varchar(10) NOT NULL DEFAULT \'#ffffff\',
				`new_bg_color` varchar(10) NOT NULL DEFAULT \'#777777\',
				`input_txt_color` varchar(10) NOT NULL DEFAULT \'#ffffff\',
				`input_bg_color` varchar(10) NOT NULL DEFAULT \'#777777\',
				`new_bg_image` varchar(256) NOT NULL DEFAULT \'0\',
				`new_bg_repeat` int(10) unsigned NOT NULL DEFAULT \'0\',
				`from` datetime,
				`to` datetime,
				`voucher_enabled` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`voucher_value` int(10) unsigned NOT NULL DEFAULT \'0\',
				`voucher_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`voucher_date_validity` int(10) unsigned NOT NULL DEFAULT \'7\',
				`voucher_min_order` int(10) unsigned NOT NULL DEFAULT \'0\',
                `voucher_min_order_currency` int(10) unsigned NOT NULL,
                `voucher_reduction_tax` int(10) unsigned NOT NULL DEFAULT \'0\',
                `voucher_reduction_currency` int(10) unsigned NOT NULL,
                `voucher_code_prefix` varchar(240) NOT NULL DEFAULT \'NEWSLETTER-SUB-\',
				PRIMARY KEY (`id_pdpopupspro`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');


        /* PopUps lang configuration */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdpopupspro_lang` (
			  `id_pdpopupspro` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `name` varchar(128) NOT NULL,
			  `content` varchar(800) NOT NULL,
			  `content_newsletter` varchar(800) NOT NULL,
			  PRIMARY KEY (`id_pdpopupspro`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

        return $res;
    }

    public static function dropTables()
    {
        $sql = 'DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'pdpopupspro`,
			`'._DB_PREFIX_.'pdpopupspro_lang`';

        return Db::getInstance()->execute($sql);
    }
}
