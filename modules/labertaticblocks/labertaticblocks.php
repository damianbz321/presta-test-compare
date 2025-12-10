<?php

// Security
if (!defined('_PS_VERSION_'))
    exit;

// Checking compatibility with older PrestaShop and fixing it
if (!defined('_MYSQL_ENGINE_'))
    define('_MYSQL_ENGINE_', 'MyISAM');

// Loading Models
require_once(_PS_MODULE_DIR_ . 'labertaticblocks/models/LaberStaticblock.php');
include_once(dirname(__FILE__).'/sql/SampleDataHtml.php');
class labertaticblocks extends Module {
    public $_staticModel =  "";
    public function __construct() {
        $this->name = 'labertaticblocks';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'laberthemes';
        $this->need_instance = 0;
        $this->_staticModel = new LaberStaticblock();
        parent::__construct();

        $this->displayName = $this->l('LABER Custom HTML');
        $this->description = $this->l('Laber Custom html news');
        $this->admin_tpl_path = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/';
       
    }

    public function install() {

        // Install SQL
        include(dirname(__FILE__) . '/sql/install.php');
        foreach ($sql as $s)
            if (!Db::getInstance()->execute($s))
                return false;		
		$sample_data = new SampleDataHtml();
		$base_url = _PS_BASE_URL_.__PS_BASE_URI__;
		$sample_data->initData($base_url);
		
		$tab = new Tab();
		$tab->active = 1;
        // Need a foreach for the language
		$tab->name = array();
		$tab->class_name = 'AdminCmsBlock';
		foreach (Language::getLanguages() as $language) {
				$tab->name[$language['id_lang']] = $this->l('LABER Custom HTML');
		}
        
      
        $tab->module = $this->name;
        $tab->add();
        // Set some defaults
        return parent::install() &&
				$this->installTab()&&
                $this->registerHook('displayNav') &&
                $this->registerHook('displayNav1') &&
                $this->registerHook('displayNav2') &&
                $this->registerHook('displayTop') &&
                $this->registerHook('displaySearch') &&
                $this->registerHook('displayImageSliderTop') &&
                $this->registerHook('displayImageSlider') &&
                $this->registerHook('displayImageSliderLeft') &&
                $this->registerHook('displayImageSliderRight') &&
                $this->registerHook('displayImageSliderBottom') &&
                $this->registerHook('displayLeftColumn') &&
                $this->registerHook('displayLeftColumnProduct') &&
                $this->registerHook('displayRightColumn') &&
                $this->registerHook('displayRightColumnProduct') &&
                $this->registerHook('displayWrapperTop') &&
                $this->registerHook('displayHome') &&
                $this->registerHook('displayTopColumn') &&
                $this->registerHook('displayPosition1') &&
                $this->registerHook('displayPosition2') &&
                $this->registerHook('displayPosition3') &&
                $this->registerHook('displayPosition4') &&
                $this->registerHook('displayPosition5') &&
                $this->registerHook('displayPosition6') &&
                $this->registerHook('displayBlog') &&
				$this->registerHook('displayFooterBefore') &&
                $this->registerHook('displayFooter') &&
                $this->registerHook('displayFooter2') &&
                $this->registerHook('displayFooter3') &&
                $this->registerHook('displayFooterAfter')&&
                $this->registerHook('logoFooter');
    }

    public function uninstall() {

        Configuration::deleteByName('POSSTATICBLOCKS');

        $this->uninstallTab();
		
        $sql = array();
        include (dirname(__file__) . '/sql/uninstall_sql.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return FALSE;
            }
        }
		
        // Uninstall Module
        if (!parent::uninstall())
            return false;
        return true;
    }

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
        $tab->class_name = "AdminLaberStaticblock";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Laber Custom HTML";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('AdminLaberStaticblock');
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

	
      public function getContent()
    {
		
				
		$tab = new Tab((int) Tab::getIdFromClassName('AdminCmsBlock'));
        $tab->delete();


        $tab = new Tab();
		$tab->active = 1;
        // Need a foreach for the language
		$tab->name = array();
		$tab->class_name = 'AdminCmsBlock';
		foreach (Language::getLanguages() as $language) {
				$tab->name[$language['id_lang']] = $this->l('LABER Custom HTML');
		}
        $tab->module = $this->name;
        $tab->add();
		
		
		$url  = 'index.php?controller=AdminCmsBlock';
		$url .= '&token='.Tools::getAdminTokenLite('AdminCmsBlock');
		Tools::redirectAdmin($url);
	}
    
    public function hookdisplayNav($param) {
       $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayNav');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayNav1($param) {
       $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayNav1');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    public function hookdisplayNav2($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayNav2');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	
	public function hookdisplayTop($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayTop');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplaySearch($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displaySearch');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
    public function hookdisplayLeftColumn($param) {
       $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayLeftColumn');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayLeftColumnProduct($param) {
       $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayLeftColumnProduct');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
     public function hookdisplayRightColumn($param) { 
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayRightColumn');
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    } 
	public function hookdisplayRightColumnProduct($param) { 
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayRightColumnProduct');
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
    public function hookdisplayImageSliderTop($param) { 
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayImageSliderTop');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
    public function hookdisplayImageSlider($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayImageSlider');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
    public function hookdisplayImageSliderRight($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayImageSliderRight');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    public function hookdisplayImageSliderLeft($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayImageSliderLeft');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }

    public function hookdisplayImageSliderBottom($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayImageSliderBottom');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    
    public function hookdisplayWrapperTop($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayWrapperTop');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    } 
	public function hookdisplayHome($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayHome');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	
	public function hookdisplayPosition1($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition1');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayPosition2($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition2');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayPosition3($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition3');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayPosition4($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition4');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayPosition5($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition5');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayPosition6($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayPosition6');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayBlog($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayBlog');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayFooterBefore($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayFooterBefore');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayFooter($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayFooter');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayFooter2($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayFooter2');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayFooter3($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayFooter3');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hookdisplayFooterAfter($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'displayFooterAfter');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
	public function hooklogoFooter($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticblockLists($id_shop,'logoFooter');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'views/templates/block.tpl');
    }
    public function hookHeader($params){
    }
	public function hookDisplayBackOfficeHeader($params) {
    }	
    
    
    public function getModulById($id_module) {
        return Db::getInstance()->getRow('
            SELECT m.*
            FROM `' . _DB_PREFIX_ . 'module` m
            JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.`id_shop` = ' . (int) ($this->context->shop->id) . ')
            WHERE m.`id_module` = ' . $id_module);
    }

   public function getHooksByModuleId($id_module) {
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'hook_module AS `ps` LEFT JOIN '._DB_PREFIX_.'hook AS `ph` ON `ps`.`id_hook` = `ph`.`id_hook`  WHERE `ps`.`id_module`='.$id_module.' AND `ps`.`id_shop` = '.$id_shop ;
		$hooks = array();
	     if($object = Db::getInstance()->ExecuteS($sql)){
			 if(count($object)>0) {
				 foreach($object as $module_hook) {
					if(isset($module_hook['name']))
						$hooks[] = $module_hook['name'];
				 }
			 }
			
		 }
		 return $hooks; 
   }

   

    public static function getHookByArrName($arrName) {
        $result = Db::getInstance()->ExecuteS('
		SELECT `id_hook`, `name`
		FROM `' . _DB_PREFIX_ . 'hook` 
		WHERE `name` IN (\'' . implode("','", $arrName) . '\')');
        return $result;
    }
  //$hooks = $this->getHooksByModuleId(10);
    public function getListModuleInstalled() {
        $mod = new labertaticblocks();
        $modules = $mod->getModulesInstalled(0);
        $arrayModule = array();
        foreach($modules as $key => $module) {
            if($module['active']==1) {
                $arrayModule[0] = array('id_module'=>0, 'name'=>'Chose Module');
                $arrayModule[$key] = $module;
            }
        }
        if ($arrayModule)
            return $arrayModule;
        return array();
    }
    
    private function _installHookCustomer(){
		$hookspos = array(
				'displayBlockPosition1',
				'displayBlockPosition2',
				'displayBlockPosition3',
				'displayBlockPosition4',
				'displayBlockPosition5',
				'productExtraRight',
				'displayNav',
				'displayNav1',
				'displayBannerSequence'
			); 
		foreach( $hookspos as $hook ){
			if( Hook::getIdByName($hook) ){
				
			} else {
				$new_hook = new Hook();
				$new_hook->name = pSQL($hook);
				$new_hook->title = pSQL($hook);
				$new_hook->add();
				$id_hook = $new_hook->id;
			}
		}
		return true;
	}

}