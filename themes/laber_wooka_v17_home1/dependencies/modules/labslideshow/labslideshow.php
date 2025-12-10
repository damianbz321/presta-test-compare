<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since   1.5.0
 * @version 1.3 (2012-03-14)
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'labslideshow/labslidesho.php');

class labslideshow extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'labslideshow';
		$this->tab = 'front_office_features';
		$this->version = '1.7.x';
		$this->author = 'labersthemes';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('LABER nivo slider');
		$this->description = $this->l('Adds an image slider to your homepage.');
		$this->ps_versions_compliancy = array('min' => '1.6.0.4', 'max' => _PS_VERSION_);
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		$this->installTab();	
		/* Adds Module */
		if (parent::install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayImageSlider') &&
			$this->registerHook('actionShopDataDuplication')
		)
		{
			/* Sets up configuration */
			$res = Configuration::updateValue('LAB_SPEED', '500');
			$res &= Configuration::updateValue('LAB_PAUSE', '6000');
			$res &= Configuration::updateValue('LAB_E_N_P', '1');
			$res &= Configuration::updateValue('LAB_E_CONTROL', '1');
			$res &= Configuration::updateValue('LAB_TITLE_SLIDERSHOW', '0');
			/* Creates tables */
			$res &= $this->createTables();
			return (bool)$res;
		}

		return false;
	}


	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		$this->uninstallTab();
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();
			/* Unsets configuration */
			$res &= Configuration::deleteByName('labslideshow_WIDTH');
			$res &= Configuration::deleteByName('LAB_SPEED');
			$res &= Configuration::deleteByName('LAB_PAUSE');
			$res &= Configuration::deleteByName('LAB_E_N_P');
			$res &= Configuration::deleteByName('LAB_E_CONTROL');
			$res &= Configuration::deleteByName('LAB_TITLE_SLIDERSHOW');
			return (bool)$res;
		}

		return false;
	}
       /* ------------------------------------------------------------- */

        /*  CREATE THE TAB MENU

        /* ------------------------------------------------------------- */

       public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminMenuFirst');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        }
        else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminMenuFirst";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "FRAMEWORK";
            }
            $parentTab->id_parent = 0;
            $parentTab->module ='';
            $response &= $parentTab->add();
        }

			// Check for parent tab2
			$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');
			if ($parentTab_2ID) {
				$parentTab_2 = new Tab($parentTab_2ID);
			}
			else {
				$parentTab_2 = new Tab();
				$parentTab_2->active = 1;
				$parentTab_2->name = array();
				$parentTab_2->class_name = "AdminMenuSecond";
				foreach (Language::getLanguages() as $lang) {
					$parentTab_2->name[$lang['id_lang']] = "Module Configure";
				}
				$parentTab_2->id_parent = $parentTab->id;
				$parentTab_2->module = '';
				$response &= $parentTab_2->add();
			}
		// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "Adminlabslideshow";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Laber Slideshow";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Adminlabslideshow');
		$parentTabID = Tab::getIdFromClassName('AdminMenuFirst');
        $tab = new Tab($id_tab);
        $tab->delete();

		// Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');
		$tabCount_2 = Tab::getNbTabs($parentTab_2ID);
        if ($tabCount_2 == 0) {
            $parentTab_2 = new Tab($parentTab_2ID);
            $parentTab_2->delete();
        }
        // Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
        $tabCount = Tab::getNbTabs($parentTabID);
        if ($tabCount == 0) {
            $parentTab = new Tab($parentTabID);
            $parentTab->delete();
        }

        return true;
    }
	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Slides */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labslideshow` (
				`id_labslideshow_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_labslideshow_slides`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labslideshow_slides` (
			  `id_labslideshow_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `margin` varchar(255) NOT NULL,
			  `style` varchar(255) NOT NULL,
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_labslideshow_slides`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labslideshow_slides_lang` (
			  `id_labslideshow_slides` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,

			  `legend` varchar(255) NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `image` varchar(255) NOT NULL,
			  PRIMARY KEY (`id_labslideshow_slides`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
		$sql =  "INSERT INTO `"._DB_PREFIX_."labslideshow_slides` (`id_labslideshow_slides`, `position`, `margin`, `style`, `active`) VALUES
			('1','0','left','style1','1'),
			('2','0','left','style1','1'),
			('3','0','left','style1','1')
	
			";
        $sql1 = "INSERT INTO `"._DB_PREFIX_."labslideshow_slides_lang` (`id_labslideshow_slides`, `id_lang`, `title`, `description`, `legend`, `url`, `image`) VALUES
			('1','1','banner','<p>Eren with a tool that makes design simple for everyone.</p>','New Arrivals 2019','#','17d81c29a20da77023fc5b96603f21cfcb59a162_banner_13_1.jpg'),
			('1','2','banner','','banner','#','1f33b82b9395585481b663116b5c809d945aad8b_10_1.jpg'),
			('2','1','banner','<p>Eren with a tool that makes design simple for everyone.</p>','Great In Your Home','#','8e9c00e3388fb4a5a6e06ded99550e4448c44c17_banner_13_2.jpg'),
			('2','2','banner','','banner','#','d7e4bbc9e88c7dddcfa167960bc80225088e5ae2_10_2.jpg'),
			('3','1','banner','<p>Eren with a tool that makes design simple for everyone.</p>','Trending 2019','#','12c7205c4573433d804a8c3897a9a6a8daff2c09_banner_13_3.jpg'),
			('3','2','banner','','banner','#','c40e31bc68de1a8898c2cbd16ad7da94ebf35137_10_3.jpg')
			
			";
        $sql2 = "INSERT INTO `"._DB_PREFIX_."labslideshow` (`id_labslideshow_slides`, `id_shop`) VALUES
			('1','1'),
			('2','1'),
			('3','1')
			
			";
        
		if ($res){
              $res &=  Db::getInstance()->Execute($sql);
              $res &=  Db::getInstance()->Execute($sql1);
              $res &=  Db::getInstance()->Execute($sql2);
        }
		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		$slides = $this->getSlides();
		foreach ($slides as $slide)
		{
			$to_del = new labslidesho($slide['id_slide']);
			$to_del->delete();
		}

		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'labslideshow`, `'._DB_PREFIX_.'labslideshow_slides`, `'._DB_PREFIX_.'labslideshow_slides_lang`;
		');
	}

	public function getContent()
	{
		$this->_html .= $this->headerHTML();

		/* Validate & process */
		if (Tools::isSubmit('submitSlide') || Tools::isSubmit('delete_id_slide') ||
			Tools::isSubmit('submitSlider') ||
			Tools::isSubmit('changeStatus')
		)
		{
			if ($this->_postValidation())
			{
				$this->_postProcess();
				$this->_html .= $this->renderForm();
				$this->_html .= $this->renderList();
			}
			else
				$this->_html .= $this->renderAddForm();

			$this->clearCache();
		}
		elseif (Tools::isSubmit('addSlide') || (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))))
			$this->_html .= $this->renderAddForm();
		else
		{
			$this->_html .= $this->renderForm();
			$this->_html .= $this->renderList();
		}

		return $this->_html;
	}

	private function _postValidation()
	{
		$errors = array();

		/* Validation for Slider configuration */
		if (Tools::isSubmit('submitSlider'))
		{

			if (!Validate::isInt(Tools::getValue('LAB_SPEED')) || !Validate::isInt(Tools::getValue('LAB_PAUSE')) ||
				!Validate::isInt(Tools::getValue('labslideshow_WIDTH'))
			)
				$errors[] = $this->l('Invalid values');
		} /* Validation for status */
		elseif (Tools::isSubmit('changeStatus'))
		{
			if (!Validate::isInt(Tools::getValue('id_slide')))
				$errors[] = $this->l('Invalid slide');
		}
		/* Validation for Slide */
		elseif (Tools::isSubmit('submitSlide'))
		{
			/* Checks state (active) */
			if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1))
				$errors[] = $this->l('Invalid slide state.');
			/* Checks position */
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->l('Invalid slide position.');
			/* If edit : checks id_slide */
			if (Tools::isSubmit('id_slide'))
			{

				//d(var_dump(Tools::getValue('id_slide')));
				if (!Validate::isInt(Tools::getValue('id_slide')) && !$this->slideExists(Tools::getValue('id_slide')))
					$errors[] = $this->l('Invalid id_slide');
			}
			/* Checks title/url/legend/description/image */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('title_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
				if (Tools::strlen(Tools::getValue('legend_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The caption is too long.');
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The URL is too long.');
				if (Tools::strlen(Tools::getValue('description_'.$language['id_lang'])) > 4000)
					$errors[] = $this->l('The description is too long.');
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_'.$language['id_lang'])))
					$errors[] = $this->l('The URL format is not correct.');
				if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename.');
				if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename.');
			}

			/* Checks title/url/legend/description for default lang */
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('title_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The title is not set.');
			if (Tools::strlen(Tools::getValue('legend_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The caption is not set.');
			if (Tools::strlen(Tools::getValue('url_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The URL is not set.');
			if (!Tools::isSubmit('has_picture') && (!isset($_FILES['image_'.$id_lang_default]) || empty($_FILES['image_'.$id_lang_default]['tmp_name'])))
				$errors[] = $this->l('The image is not set.');
			if (Tools::getValue('image_old_'.$id_lang_default) && !Validate::isFileName(Tools::getValue('image_old_'.$id_lang_default)))
				$errors[] = $this->l('The image is not set.');
		} /* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_slide') && (!Validate::isInt(Tools::getValue('delete_id_slide')) || !$this->slideExists((int)Tools::getValue('delete_id_slide'))))
			$errors[] = $this->l('Invalid id_slide');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			return false;
		}

		/* Returns if validation is ok */

		return true;
	}

	private function _postProcess()
	{
		$errors = array();

		/* Processes Slider */
		if (Tools::isSubmit('submitSlider'))
		{
			$res = Configuration::updateValue('labslideshow_WIDTH', (int)Tools::getValue('labslideshow_WIDTH'));
			$res &= Configuration::updateValue('LAB_SPEED', (int)Tools::getValue('LAB_SPEED'));
			$res &= Configuration::updateValue('LAB_PAUSE', (int)Tools::getValue('LAB_PAUSE'));
			$res &= Configuration::updateValue('LAB_E_N_P', (int)Tools::getValue('LAB_E_N_P'));
			$res &= Configuration::updateValue('LAB_E_CONTROL', (int)Tools::getValue('LAB_E_CONTROL'));
			$res &= Configuration::updateValue('LAB_TITLE_SLIDERSHOW', (int)Tools::getValue('LAB_TITLE_SLIDERSHOW'));
			$this->clearCache();
			if (!$res)
				$errors[] = $this->displayError($this->l('The configuration could not be updated.'));
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		} /* Process Slide status */
		elseif (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_slide'))
		{
			$slide = new labslidesho((int)Tools::getValue('id_slide'));
			if ($slide->active == 0)
				$slide->active = 1;
			else
				$slide->active = 0;
			$res = $slide->update();
			$this->clearCache();
			$this->_html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
		}
		/* Processes Slide */
		elseif (Tools::isSubmit('submitSlide'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_slide'))
			{
				$slide = new labslidesho((int)Tools::getValue('id_slide'));
				if (!Validate::isLoadedObject($slide))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_slide'));

					return false;
				}
			}
			else
				$slide = new labslidesho();
           // echo"<pre>".print_r($slide->margin,1);die;
			/* Sets position */
			$slide->position = (int)Tools::getValue('position');
			/* Sets active */
			$slide->active = (int)Tools::getValue('active_slide');
			$slide->margin = Tools::getValue('margin');
			$slide->style = Tools::getValue('style');
//echo "<pre>".print_r($_POST,1);die;
			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$slide->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
				$slide->url[$language['id_lang']] = Tools::getValue('url_'.$language['id_lang']);
				$slide->legend[$language['id_lang']] = Tools::getValue('legend_'.$language['id_lang']);
				$slide->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
             //  echo "<pre>".print_r($_POST,1);die;
				/* Uploads image and sets slide */
				$type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
				$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
				if (isset($_FILES['image_'.$language['id_lang']]) &&
					isset($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($imagesize) &&
					in_array(
						Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array(
							'jpg',
							'gif',
							'jpeg',
							'png'
						)
					) &&
					in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
				)
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
						$errors[] = $error;
					elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
						return false;
					elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$_FILES['image_'.$language['id_lang']]['name'], null, null, $type))
						$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
					if (isset($temp_name))
						@unlink($temp_name);
					$slide->image[$language['id_lang']] = $salt.'_'.$_FILES['image_'.$language['id_lang']]['name'];
				}
				elseif (Tools::getValue('image_old_'.$language['id_lang']) != '')
					$slide->image[$language['id_lang']] = Tools::getValue('image_old_'.$language['id_lang']);
			}

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_slide'))
				{
					if (!$slide->add())
						$errors[] = $this->displayError($this->l('The slide could not be added.'));
				}
				/* Update */
				elseif (!$slide->update())
					$errors[] = $this->displayError($this->l('The slide could not be updated.'));
				$this->clearCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_slide'))
		{
			$slide = new labslidesho((int)Tools::getValue('delete_id_slide'));
			$res = $slide->delete();
			$this->clearCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitSlide') && Tools::getValue('id_slide'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitSlide'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}

	private function _prepareHook()
	{

		if (!$this->isCached('labslideshow.tpl', $this->getCacheId()))
		{
			$slides = $this->getSlides(true);
         //   echo "<pre>".print_r($slides,1);die;
			if (is_array($slides))
				foreach ($slides as &$slide)
				{
					$slide['sizes'] = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$slide['image']));
					if (isset($slide['sizes'][3]) && $slide['sizes'][3])
						$slide['size'] = $slide['sizes'][3];
				}

			if (!$slides)
				return false;

			$this->smarty->assign(array('labslideshow_slides' => $slides));
		}

		return true;
	}

	public function hookdisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'css/labslideshow.css');
		//$this->context->controller->addJS($this->_path.'js/jquery.nivo.slider.js');
		$this->context->controller->addJS($this->_path.'js/jquery.nivo.slider.pack.js');
/* 		$slider = array(
			'width' => Configuration::get('labslideshow_WIDTH'),
			'speed' => Configuration::get('LAB_SPEED'),
			'pause' => Configuration::get('LAB_PAUSE'),
			'loop' => (bool)Configuration::get('LAB_E_N_P'),
		);
		$this->smarty->assign('labslideshow', $slider);
		return $this->display(__FILE__, 'header.tpl'); */
	}

	public function hookdisplayImageSlider()
	{
		if (!$this->_prepareHook())
			return false;
		$languages = Language::getLanguages(true, $this->context->shop->id);
		$slider = array(
			'languages' => $languages,
			'LAB_PAUSE' => (int)Configuration::get('LAB_PAUSE'),
			'LAB_SPEED' => (int)Configuration::get('LAB_SPEED'),
			'LAB_E_N_P' => (bool)Configuration::get('LAB_E_N_P'),
			'LAB_E_CONTROL' => (bool)Configuration::get('LAB_E_CONTROL'),
			'LAB_TITLE_SLIDERSHOW' => (bool)Configuration::get('LAB_TITLE_SLIDERSHOW'),
			'js_file' => $this->_path.'js/labslideshow.js',
		);
		$this->smarty->assign('labslideshow', $slider);
		return $this->display(__FILE__, 'labslideshow.tpl', $this->getCacheId());
	}

	public function clearCache()
	{
		$this->_clearCache('labslideshow.tpl');
	}

	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'labslideshow (id_labslideshow_slides, id_shop)
			SELECT id_labslideshow_slides, '.(int)$params['new_id_shop'].'
			FROM '._DB_PREFIX_.'labslideshow
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
		$this->clearCache();
	}

	public function headerHTML()
	{
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addJqueryUI('ui.sortable');
		/* Style & js for fieldset 'slides configuration' */
		$html = '<script type="text/javascript">
			$(function() {
				var $mySlides = $("#slides");
				$mySlides.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
						}
					});
				$mySlides.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';

		return $html;
	}

	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT MAX(hss.`position`) AS `next_position`
			FROM `'._DB_PREFIX_.'labslideshow_slides` hss, `'._DB_PREFIX_.'labslideshow` hs
			WHERE hss.`id_labslideshow_slides` = hs.`id_labslideshow_slides` AND hs.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}

	public function getSlides($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_labslideshow_slides` as id_slide, hssl.`image`, hss.`position`, hss.`active`, hssl.`title`,
			hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`image`,hss.`margin`,hss.`style`
			FROM '._DB_PREFIX_.'labslideshow hs
			LEFT JOIN '._DB_PREFIX_.'labslideshow_slides hss ON (hs.id_labslideshow_slides = hss.id_labslideshow_slides)
			LEFT JOIN '._DB_PREFIX_.'labslideshow_slides_lang hssl ON (hss.id_labslideshow_slides = hssl.id_labslideshow_slides)
			WHERE id_shop = '.(int)$id_shop.'
			AND hssl.id_lang = '.(int)$id_lang.
			($active ? ' AND hss.`active` = 1' : ' ').'
			ORDER BY hss.position'
		);
	}

	public function getAllImagesBySlidesId($id_slides, $active = null, $id_shop = null)
	{
		$this->context = Context::getContext();
		$images = array();

		if (!isset($id_shop))
			$id_shop = $this->context->shop->id;

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hssl.`image`, hssl.`id_lang`
			FROM '._DB_PREFIX_.'labslideshow hs
			LEFT JOIN '._DB_PREFIX_.'labslideshow_slides hss ON (hs.id_labslideshow_slides = hss.id_labslideshow_slides)
			LEFT JOIN '._DB_PREFIX_.'labslideshow_slides_lang hssl ON (hss.id_labslideshow_slides = hssl.id_labslideshow_slides)
			WHERE hs.`id_labslideshow_slides` = '.(int)$id_slides.' AND hs.`id_shop` = '.(int)$id_shop.
			($active ? ' AND hss.`active` = 1' : ' ')
		);

		foreach ($results as $result)
			$images[$result['id_lang']] = $result['image'];

		return $images;
	}

	public function displayStatus($id_slide, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_slide='.(int)$id_slide.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}

	public function slideExists($id_slide)
	{
		$req = 'SELECT hs.`id_labslideshow_slides` as id_slide
				FROM `'._DB_PREFIX_.'labslideshow` hs
				WHERE hs.`id_labslideshow_slides` = '.(int)$id_slide;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}

	public function renderList()
	{
		$slides = $this->getSlides();

		foreach ($slides as $key => $slide)
			$slides[$key]['status'] = $this->displayStatus($slide['id_slide'], $slide['active']);

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'slides' => $slides,
				'image_baseurl' => $this->_path.'images/'
			)
		);

		return $this->display(__FILE__, 'list.tpl');
	}

	public function renderAddForm()
	{
        $tabEffect = array();
        $tabEffect = array(
            array( 'id'=>'left','mode'=>'left'),
            array('id'=>'right','mode'=>'right'),
            array('id'=>'center','mode'=>'center'),
        );
        $modestyle = array();
        $modestyle = array(
           array( 'id'=>'style1','mode'=>'style1'),
            array('id'=>'style2','mode'=>'style2'),
            /* array('id'=>'style3','mode'=>'style3'), */
       );
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Slide informations'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Select a file'),
						'name' => 'image',
						'lang' => true,
						'desc' => $this->l(sprintf('Max image size %s', ini_get('upload_max_filesize')))
					),
					array(
						'type' => 'text',
						'label' => $this->l('Title'),
						'name' => 'title',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('URL'),
						'name' => 'url',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Caption'),
						'name' => 'legend',
						'lang' => true,
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description',
						'autoload_rte' => true,
						'lang' => true,
					),
                    array(
                        'type' => 'select',
                        'label' => 'Text position: ',
                        'name' => 'margin',
                        'options' => array(                                  // This is only useful if type == select
                            'query' => $tabEffect,                           // $array_of_rows must contain an array of arrays, inner arrays (rows) being mode of many fields
                            'id' => 'id',                           // The key that will be used for each option "value" attribute
                            'name'=>'mode',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Style Select : ',
                        'name' => 'style',
                        'options' => array(                                  // This is only useful if type == select
                            'query' => $modestyle,                           // $array_of_rows must contain an array of arrays, inner arrays (rows) being mode of many fields
                            'id' => 'id',                           // The key that will be used for each option "value" attribute
                            'name'=>'mode',
                        ),
                    ),
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active_slide',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))
		{
			$slide = new labslidesho((int)Tools::getValue('id_slide'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
			$fields_form['form']['images'] = $slide->image;

			$has_picture = true;

			foreach (Language::getLanguages(false) as $lang)
				if (!isset($slide->image[$lang['id_lang']]))
					$has_picture &= false;

			if ($has_picture)
				$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSlide';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'images/'
		);
		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
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
						'label' => $this->l('animSpeed'),
						'name' => 'LAB_SPEED',
						'suffix' => 'ms'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Pause'),
						'name' => 'LAB_PAUSE',
						'suffix' => 'ms'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enabled Prev Next'),
						'name' => 'LAB_E_N_P',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled Prev Next')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enabled Control'),
						'name' => 'LAB_E_CONTROL',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enabled Text'),
						'name' => 'LAB_TITLE_SLIDERSHOW',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
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
		$this->fields_form = array();
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSlider';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}
	public function getConfigFieldsValues()
	{
		return array(
			'labslideshow_WIDTH' => Tools::getValue('labslideshow_WIDTH', Configuration::get('labslideshow_WIDTH')),
			'LAB_SPEED' => Tools::getValue('LAB_SPEED', Configuration::get('LAB_SPEED')),
			'LAB_PAUSE' => Tools::getValue('LAB_PAUSE', Configuration::get('LAB_PAUSE')),
			'LAB_E_N_P' => Tools::getValue('LAB_E_N_P', Configuration::get('LAB_E_N_P')),
			'LAB_E_CONTROL' => Tools::getValue('LAB_E_CONTROL', Configuration::get('LAB_E_CONTROL')),
			'LAB_TITLE_SLIDERSHOW' => Tools::getValue('LAB_TITLE_SLIDERSHOW', Configuration::get('LAB_TITLE_SLIDERSHOW')),
		);
	}

	public function getAddFieldsValues()
	{

		$fields = array();

		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))
		{
			$slide = new labslidesho((int)Tools::getValue('id_slide'));
			$fields['id_slide'] = (int)Tools::getValue('id_slide', $slide->id);
		}
		else
			$slide = new labslidesho();

		$fields['active_slide'] = Tools::getValue('active_slide', $slide->active);
		$fields['margin'] = Tools::getValue('margin', $slide->margin);
		$fields['style'] = Tools::getValue('style', $slide->style);
		$fields['has_picture'] = true;
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
		{
			$fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang']);
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $slide->title[$lang['id_lang']]);
			$fields['url'][$lang['id_lang']] = Tools::getValue('url_'.(int)$lang['id_lang'], $slide->url[$lang['id_lang']]);
			$fields['legend'][$lang['id_lang']] = Tools::getValue('legend_'.(int)$lang['id_lang'], $slide->legend[$lang['id_lang']]);
			$fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $slide->description[$lang['id_lang']]);
		}

		return $fields;
	}
}
