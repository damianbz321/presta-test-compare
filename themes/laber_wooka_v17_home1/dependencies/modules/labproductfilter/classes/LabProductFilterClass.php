<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class LabProductFilterClass extends ObjectModel
{
	public $shownew;
	public $showfeature;
	public $showsale;
	public $showspecail;
	public $id_hook;
	public $type_display;
	public $num_show;
	public $num_row;
	public $num_column;
	public $use_slider;
	public $active;
	public static $definition = array(
		'table' => 'labproductfilter',
		'primary' => 'id_labproductfilter',
		'multilang_shop' => true,
		'fields' => array(
			'shownew' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'showfeature' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'showsale' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'showspecail' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'id_hook' =>	array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isCleanHtml', 'required' => true),
			'type_display' =>	array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isCleanHtml', 'required' => true),
			'num_show' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'num_row' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'num_column' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'use_slider' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isunsignedInt', 'required' => true),
			'active' =>	array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool', 'required' => true)
		)
	);

	public	function __construct($id_slide = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_slide, $id_lang, $id_shop);
		Shop::addTableAssociation('labproductfilter', array('type' => 'shop'));
	}

	public function add($autodate = true, $null_values = false)
	{
		$res = parent::add($autodate, $null_values);
		return $res;
	}

	public function delete()
	{
		$res = true;
		$id_shop = Context::getContext()->shop->id;
		$res &= parent::delete();
		return $res;
	}
	
	public function getBlockByHook($hook_name)
	{
		$this->context = Context::getContext();
		$id_shop = (int)Context::getContext()->shop->id;
		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT lab.*
			FROM '._DB_PREFIX_.'labproductfilter_shop lab
			WHERE lab.active = 1 AND lab.id_hook = "'.$hook_name.'" AND lab.id_shop = '.$id_shop);
	}
}