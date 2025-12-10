<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
/*if (!defined('_MYSQL_ENGINE_'))
    define('_MYSQL_ENGINE_', 'MyISAM');*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Labthemeoptions extends Module implements WidgetInterface
{
    var $prefix = '';
    var $amounts = 4;
    var $base_config_url = '';
    var $overrideHooks = array();

    private $templates = array (
        'hookcolortool' => 'views/templates/front/colortool.tpl',
        'hookfontend' => 'views/templates/front/labthemeoptions.tpl',
    );
    public function __construct()
    {
        global $currentIndex;
        $this->name = 'labthemeoptions';
        $this->tab = 'front_office_features';
        $this->version = '1.7.x';
        $this->bootstrap = true ;
        $this->author = 'labersthemes';
        $this->need_instance = 0;
        $this->currentIndex = $currentIndex;
        parent::__construct();
        $this->displayName = $this->l('LABER Theme Option');
        $this->description = $this->l('Manage theme skins');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->base_config_url = $this->currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
      //  $this->templateFile = 'module:labthemeoptions/views/templates/front/labthemeoptions.tpl';
        
        if (!Configuration::get('THEMEOPTIONS'))
            $this->warning = $this->l('No name provided');
    }

   public function install()
    {
		$this->installTab();	
        if (parent::install()  && $this->registerHook([
                'displaylabthemeoptions', // Standard hook
                'displayHeader', //
                'displayAdminlabthemeoptions', //
            ]) ) {
            $res = Configuration::updateValue('lab_hbgnav','');
            $res &= Configuration::updateValue('lab_htextnav','');
            $res &= Configuration::updateValue('lab_maincolor','');
            $res &= Configuration::updateValue('lab_hbgheadercolor','');
            $res &= Configuration::updateValue('lab_htextheader','');
            $res &= Configuration::updateValue('lab_hhovertext','');
            $res &= Configuration::updateValue('lab_hhovertextnav','');

            $res &= Configuration::updateValue('lab_mbgcolor','');
            $res &= Configuration::updateValue('lab_mbghover','');
            $res &= Configuration::updateValue('lab_mtext','');
            $res &= Configuration::updateValue('lab_mtexthover','');
            $res &= Configuration::updateValue('lab_mbgsubmenu','');
            $res &= Configuration::updateValue('lab_mtextsub','');
            $res &= Configuration::updateValue('lab_mhovertextsub','');

            $res &= Configuration::updateValue('lab_cbgcolor','');
            $res &= Configuration::updateValue('lab_ccolorlink','');
            $res &= Configuration::updateValue('lab_chovercolorlink','');
            $res &= Configuration::updateValue('lab_ccolortext','');
            $res &= Configuration::updateValue('lab_ccolorprice','');
            $res &= Configuration::updateValue('lab_ccoloroldprice','');
            $res &= Configuration::updateValue('lab_ciconcolor','');
            $res &= Configuration::updateValue('lab_cbgbuttom','');
            $res &= Configuration::updateValue('lab_ctextbuttom','');
            $res &= Configuration::updateValue('lab_cbgbuttomhover','');
            $res &= Configuration::updateValue('lab_ctextbuttomhover','');

            $res &= Configuration::updateValue('lab_fbgcolor','');
            $res &= Configuration::updateValue('lab_fcolortext','');
            $res &= Configuration::updateValue('lab_flinkcolor','');
            $res &= Configuration::updateValue('lab_flinkcolorhover','');
            $res &= Configuration::updateValue('labthemecolor','');
            $res &= Configuration::updateValue('labshowthemecolor','');
            $res &= Configuration::updateValue('labskin','');
            $res &= Configuration::updateValue('show_color','0');
            $res &= Configuration::updateValue('lab_EnableFTM','1');
            $res &= Configuration::updateValue('lab_EnableQv','1');
            $res &= Configuration::updateValue('lab_EnableW','1');
            $res &= Configuration::updateValue('lab_EnableC','1');
            $res &= Configuration::updateValue('lab_EnablesubCate','0');
            $res &= Configuration::updateValue('show_fontend','0');
            $res &= Configuration::updateValue('show_labskin',0);
            $res &= Configuration::updateValue('lab_mode_theme','labwide');
        return (bool)$res;
        }
    }

    public function uninstall()
    {
        // Uninstall Module
		$this->uninstallTab();
        if (!parent::uninstall()||
            !Configuration::deleteByName('labbgcolor')||!Configuration::deleteByName('lab_mode_theme')|| !Configuration::deleteByName('lablinkcolor')||!Configuration::deleteByName('labmaincolor')||!Configuration::deleteByName('labpntool')
        )
            return false;
        return true;
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
        $tab->class_name = "AdminLabthemeoptions";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Laber Theme Options";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('AdminLabthemeoptions');
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

		
    function getContent()
    {
        $this->context->controller->addCSS(_PS_BASE_URL_ . __PS_BASE_URI__ . "modules/" . $this->name . "/views/templates/front/colortool/css/lab.cltool.css");
        $errors = array();
        $this->_html = '<h2>' . $this->displayName . '</h2>';
        if (Tools::isSubmit('submitUpdate')) {

            Configuration::updateValue('lab_maincolor',Tools::getValue('lab_maincolor'));
            Configuration::updateValue('lab_cbgcolor',Tools::getValue('lab_cbgcolor'));
            Configuration::updateValue('lab_ccolorlink',Tools::getValue('lab_ccolorlink'));
            Configuration::updateValue('lab_chovercolorlink',Tools::getValue('lab_chovercolorlink'));
            Configuration::updateValue('lab_ccolortext',Tools::getValue('lab_ccolortext'));
            Configuration::updateValue('lab_ccolorprice',Tools::getValue('lab_ccolorprice'));
            Configuration::updateValue('lab_ccoloroldprice',Tools::getValue('lab_ccoloroldprice'));
            Configuration::updateValue('lab_cbgbuttom',Tools::getValue('lab_cbgbuttom'));
            Configuration::updateValue('lab_fbgcolor',Tools::getValue('lab_fbgcolor'));
            Configuration::updateValue('lab_fcolortext',Tools::getValue('lab_fcolortext'));
            Configuration::updateValue('lab_flinkcolor',Tools::getValue('lab_flinkcolor'));
            Configuration::updateValue('show_color',Tools::getValue('show_color'));
            Configuration::updateValue('lab_EnableFTM',Tools::getValue('lab_EnableFTM'));
            Configuration::updateValue('lab_EnableQv',Tools::getValue('lab_EnableQv'));
            Configuration::updateValue('lab_EnableW',Tools::getValue('lab_EnableW'));
            Configuration::updateValue('lab_EnableC',Tools::getValue('lab_EnableC'));
            Configuration::updateValue('lab_EnablesubCate',Tools::getValue('lab_EnablesubCate'));
            Configuration::updateValue('show_fontend',Tools::getValue('show_fontend'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }
        if(Tools::isSubmit('submitUpdateheader')){
            Configuration::updateValue('lab_hbgnav',Tools::getValue('lab_hbgnav'));
            Configuration::updateValue('lab_htextnav',Tools::getValue('lab_htextnav'));
            Configuration::updateValue('lab_hbgheadercolor',Tools::getValue('lab_hbgheadercolor'));
            Configuration::updateValue('lab_htextheader',Tools::getValue('lab_htextheader'));
            Configuration::updateValue('lab_hhovertext',Tools::getValue('lab_hhovertext'));
            Configuration::updateValue('lab_hhovertextnav',Tools::getValue('lab_hhovertextnav'));
        }
        if(Tools::isSubmit('submitUpdateMegamenu')){
            Configuration::updateValue('lab_mbghover',Tools::getValue('lab_mbghover'));
            Configuration::updateValue('lab_mbgcolor',Tools::getValue('lab_mbgcolor'));
            Configuration::updateValue('lab_mtext',Tools::getValue('lab_mtext'));
            Configuration::updateValue('lab_mtexthover',Tools::getValue('lab_mtexthover'));
            Configuration::updateValue('lab_mbgsubmenu',Tools::getValue('lab_mbgsubmenu'));
            Configuration::updateValue('lab_mtextsub',Tools::getValue('lab_mtextsub'));
            Configuration::updateValue('lab_mhovertextsub',Tools::getValue('lab_mhovertextsub'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }
        if(Tools::isSubmit('submitUpdateMaincontent')){
             Configuration::updateValue('lab_cbgcolor',Tools::getValue('lab_cbgcolor'));
             Configuration::updateValue('lab_ccolorlink',Tools::getValue('lab_ccolorlink'));
             Configuration::updateValue('lab_chovercolorlink',Tools::getValue('lab_chovercolorlink'));
             Configuration::updateValue('lab_ccolortext',Tools::getValue('lab_ccolortext'));
             Configuration::updateValue('lab_ccolorprice',Tools::getValue('lab_ccolorprice'));
             Configuration::updateValue('lab_ccoloroldprice',Tools::getValue('lab_ccoloroldprice'));
             Configuration::updateValue('lab_ciconcolor',Tools::getValue('lab_ciconcolor'));
             Configuration::updateValue('lab_cbgbuttom',Tools::getValue('lab_cbgbuttom'));
             Configuration::updateValue('lab_ctextbuttom',Tools::getValue('lab_ctextbuttom'));
             Configuration::updateValue('lab_cbgbuttomhover',Tools::getValue('lab_cbgbuttomhover'));
             Configuration::updateValue('lab_ctextbuttomhover',Tools::getValue('lab_ctextbuttomhover'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));

        }
        if(Tools::isSubmit('submitUpdateFooter')){
            Configuration::updateValue('lab_fbgcolor',Tools::getValue('lab_fbgcolor'));
            Configuration::updateValue('lab_fcolortext',Tools::getValue('lab_fcolortext'));
            Configuration::updateValue('lab_flinkcolor',Tools::getValue('lab_flinkcolor'));
            Configuration::updateValue('lab_flinkcolorhover',Tools::getValue('lab_flinkcolorhover'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }
        if(Tools::isSubmit('submitUpdateThemeskin')){
            if(Tools::getValue('labskin')!=''){
                Configuration::updateValue('labskin',Tools::getValue('labskin'));
                
            }
            if(Tools::getValue('labskin') =='resetlabskin'){
                Configuration::updateValue('labskin','');
            }
            if (Tools::getValue('labthemecolor')!=''){
                Configuration::updateValue('labthemecolor',Tools::getValue('labthemecolor'));
            }
            if (Tools::getValue('labthemecolor') =='resetlabthemecolor'){
                Configuration::updateValue('labthemecolor','');
            }
            Configuration::updateValue('lab_mode_theme',Tools::getValue('lab_mode_theme'));
            Configuration::updateValue('show_labskin',Tools::getValue('show_labskin'));
            Configuration::updateValue('labshowthemecolor',Tools::getValue('labshowthemecolor'));
			Configuration::updateValue('lab_cbgcolor',Tools::getValue('lab_cbgcolor'));
			Configuration::updateValue('lab_ccolorlink',Tools::getValue('lab_ccolorlink'));
            Configuration::updateValue('show_color',Tools::getValue('show_color'));
            Configuration::updateValue('lab_EnableFTM',Tools::getValue('lab_EnableFTM'));
            Configuration::updateValue('lab_EnableQv',Tools::getValue('lab_EnableQv'));
            Configuration::updateValue('lab_EnableW',Tools::getValue('lab_EnableW'));
            Configuration::updateValue('lab_EnableC',Tools::getValue('lab_EnableC'));
            Configuration::updateValue('lab_EnablesubCate',Tools::getValue('lab_EnablesubCate'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }
        if (sizeof($errors)) {
            foreach ($errors AS $err) {
                $this->_html .= '<div class="alert error">' . $err . '</div>';
            }
        }
        $this->_html .= $this->renderForm();
        return $this->_html;
    }


    public  function renderForm(){
        $this->context->controller->addJqueryPlugin('colorpicker');
        $action = 'index.php?controller=AdminModules&configure='.$this->name.'&tab_module=front_office_features&module_name='.$this->name.'&token='.Tools::getValue('token').' ';
		$this->context->controller->addCSS(__PS_BASE_URI__ . "modules/" . $this->name . "/views/templates/admin/style.css");
        $lab_hbgnav = Tools::getValue('lab_hbgnav',Configuration::get('lab_hbgnav'));
        $lab_htextnav = Tools::getValue('lab_htextnav',Configuration::get('lab_htextnav'));
        $lab_hbgheadercolor = Tools::getValue('lab_hbgheadercolor', Configuration::get('lab_hbgheadercolor'));
        $lab_htextheader =  Tools::getValue('lab_htextheader',Configuration::get('lab_htextheader'));


        $lab_hhovertext = Tools::getValue('lab_hhovertext',Configuration::get('lab_hhovertext'));
        $lab_hhovertextnav = Tools::getValue('lab_hhovertextnav',Configuration::get('lab_hhovertextnav'));
        //megamenu
        $lab_mbghover = Tools::getValue('lab_mbghover',Configuration::get('lab_mbghover'));
        $lab_mbgcolor = Tools::getValue('lab_mbgcolor',Configuration::get('lab_mbgcolor'));
        $lab_mtext = Tools::getValue('lab_mtext',Configuration::get('lab_mtext'));
        $lab_mtexthover = Tools::getValue('lab_mtexthover',Configuration::get('lab_mtexthover'));
        $lab_mbgsubmenu = Tools::getValue('lab_mbgsubmenu',Configuration::get('lab_mbgsubmenu'));
        $lab_mtextsub = Tools::getValue('lab_mtextsub',Configuration::get('lab_mtextsub'));
        $lab_mhovertextsub = Tools::getValue('lab_mhovertextsub',Configuration::get('lab_mhovertextsub'));
        //maincontetn
        $lab_cbgcolor = Tools::getValue('lab_cbgcolor', Configuration::get('lab_cbgcolor'));
        $lab_ccolorlink = Tools::getValue('lab_ccolorlink',Configuration::get('lab_ccolorlink'));
        $lab_chovercolorlink = Tools::getValue('lab_chovercolorlink',Configuration::get('lab_chovercolorlink'));
        $lab_ccolortext = Tools::getValue('lab_ccolortext',Configuration::get('lab_ccolortext'));
        $lab_ccolorprice = Tools::getValue('lab_ccolorprice',Configuration::get('lab_ccolorprice'));
        $lab_ccoloroldprice = Tools::getValue('lab_ccoloroldprice',Configuration::get('lab_ccoloroldprice'));
        $lab_ciconcolor = Tools::getValue('lab_ciconcolor',Configuration::get('lab_ciconcolor'));
        $lab_cbgbuttom = Tools::getValue('lab_cbgbuttom',Configuration::get('lab_cbgbuttom'));
        $lab_ctextbuttom = Tools::getValue('lab_ctextbuttom',Configuration::get('lab_ctextbuttom'));
        $lab_ctextbuttomhover = Tools::getValue('lab_ctextbuttomhover',Configuration::get('lab_ctextbuttomhover'));
        $lab_cbgbuttomhover = Tools::getValue('lab_cbgbuttomhover',Configuration::get('lab_cbgbuttomhover'));
        // footer
        $labskin = Tools::getValue('labskin',Configuration::get('labskin'));
        $lab_mode_theme = Tools::getValue('lab_mode_theme',Configuration::get('lab_mode_theme'));
        $labshowskin = Tools::getValue('show_labskin',Configuration::get('show_labskin'));
        $show_color = Tools::getValue('show_color',Configuration::get('show_color'));
        $lab_EnableFTM = Tools::getValue('lab_EnableFTM',Configuration::get('lab_EnableFTM'));
        $lab_EnableQv = Tools::getValue('lab_EnableQv',Configuration::get('lab_EnableQv'));
        $lab_EnableW = Tools::getValue('lab_EnableW',Configuration::get('lab_EnableW'));
        $lab_EnableC = Tools::getValue('lab_EnableC',Configuration::get('lab_EnableC'));
        $lab_EnablesubCate = Tools::getValue('lab_EnablesubCate',Configuration::get('lab_EnablesubCate'));
        $lab_showthemecolor = Tools::getValue('labshowthemecolor',Configuration::get('labshowthemecolor'));
        $labthemecolor = Tools::getValue('labthemecolor',Configuration::get('labthemecolor'));
        $lab_fbgcolor = Tools::getValue('lab_fbgcolor',Configuration::get('lab_fbgcolor'));
        $lab_fcolortext = Tools::getValue('lab_fcolortext',Configuration::get('lab_fcolortext'));
        $lab_flinkcolor = Tools::getValue('lab_flinkcolor',Configuration::get('lab_flinkcolor'));
        $lab_flinkcolorhover = Tools::getValue('lab_flinkcolorhover',Configuration::get('lab_flinkcolorhover'));
        $this->smarty->assign(array(
                 'lab_hbgnav' =>$lab_hbgnav, // header
                 'lab_htextnav' =>$lab_htextnav,
                 'lab_hbgheadercolor' =>$lab_hbgheadercolor,
                 'lab_htextheader' =>$lab_htextheader,
                 'lab_hhovertext' =>$lab_hhovertext,
                 'lab_hhovertextnav' =>$lab_hhovertextnav,
                 'lab_mbghover' =>$lab_mbghover, //  megamenu
                 'lab_mbgcolor' =>$lab_mbgcolor,
                 'lab_mtext' =>$lab_mtext,
                 'lab_mtexthover' =>$lab_mtexthover,
                 'lab_mbgsubmenu' =>$lab_mbgsubmenu,
                 'lab_mtextsub' =>$lab_mtextsub,
                 'lab_mhovertextsub' =>$lab_mhovertextsub,
                 //maincontent
                 'lab_cbgcolor' =>$lab_cbgcolor,
                 'lab_ccolorlink' =>$lab_ccolorlink,
                 'lab_chovercolorlink' =>$lab_chovercolorlink,
                 'lab_ccolortext' =>$lab_ccolortext,
                 'lab_ccolorprice' =>$lab_ccolorprice,
                 'lab_ccoloroldprice' =>$lab_ccoloroldprice,
                 'lab_ciconcolor' =>$lab_ciconcolor,
                 'lab_cbgbuttom' =>$lab_cbgbuttom,
                 'lab_ctextbuttom' =>$lab_ctextbuttom,
                 'lab_ctextbuttomhover' =>$lab_ctextbuttomhover,
                 'lab_cbgbuttomhover' =>$lab_cbgbuttomhover,
                    //footer
                 'lab_fbgcolor' => $lab_fbgcolor,
                 'lab_fcolortext'=>$lab_fcolortext ,
                 'lab_flinkcolor'=>$lab_flinkcolor ,
                 'lab_flinkcolorhover'=>$lab_flinkcolorhover ,
                 //skin
                  'labskin' => $labskin,
                  'lab_mode_theme' => $lab_mode_theme,
                  'labthemecolor' => $labthemecolor,
                  'labshowthemecolor' => $lab_showthemecolor,
                  'labshowcolor' => $show_color,
                  'labEnableFTM' => $lab_EnableFTM,
                  'labEnableQv' => $lab_EnableQv,
                  'labEnableW' => $lab_EnableW,
                  'labEnableC' => $lab_EnableC,
                  'labEnablesubCate' => $lab_EnablesubCate,
                  'labshowskin' => $labshowskin,
                 'action'=> $action,

                 'search_query' => (string)Tools::getValue('search_query')
            )
        );
		return $this->display(__FILE__, 'views/templates/admin/adminform.tpl');
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJS($this->_path. "js/jquery.cookie.js");
        $this->context->controller->addJS($this->_path. "js/colorpicker.js");
        $this->context->controller->addJS($this->_path. "js/carousel.js");
        $this->context->controller->addJS($this->_path. "js/slick.min.js");
        $this->context->controller->addJS($this->_path. "js/wow.min.js");
        $this->context->controller->addJS($this->_path. "js/jquery.lettering.js");
        $this->context->controller->addJS($this->_path. "js/laberthemes.js");

        $this->context->controller->addCSS($this->_path. '/views/templates/front/colortool/css/lab.cltool.css' );
        $this->context->controller->addCSS($this->_path. '/views/templates/front/colortool/css/colorpicker.css' );
       // $this->context->controller->addCSS($this->_path. 'css/elegant-icons-master.css' );
		$this->context->controller->addCSS($this->_path. 'css/font-awesome.min.css' );
        //$this->context->controller->addCSS($this->_path. 'css/fontawesome-all.min.css' );
        //$this->context->controller->addCSS($this->_path. 'css/pe-icon-7-stroke.css' );
        //$this->context->controller->addCSS($this->_path. 'css/ionicons.min.css' );
        //$this->context->controller->addCSS($this->_path. 'css/materialdesignicons.min.css' );
        $this->context->controller->addCSS($this->_path. 'css/animate.css' );	
        $this->context->controller->addCSS($this->_path. 'css/owl.carousel.css' );
        $this->context->controller->addCSS($this->_path. 'css/slick.css' );
        //$this->context->controller->addCSS($this->_path. 'css/simple-line-icons.css');
        //$this->context->controller->addCSS($this->_path. 'css/linearicons-Free.css' );
        $this->context->controller->addCSS($this->_path. 'css/IcoMoon.css' );
        $this->context->controller->addCSS($this->_path. 'css/laberthemes.css' );
    }


    function renderWidget($hookName = null, array $configuration = [])
    {

        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }
        if (preg_match('/^displaylabthemeoptions\d*$/', $hookName)) {
            $template_file = $this->templates['hookcolortool'];
        } elseif($hookName == 'displayAdminlabthemeoptions') {
          //  die('eeaee');
        //   var_dump($this->name.'/'.$template_file ); ;
        $template_file = $this->templates['hookfontend'];
        }
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch('module:'.$this->name.'/'.$template_file);
    }



    public function getWidgetVariables($hookName, array $configuration =[])
    {


        // TODO: Implement getWidgetVariables() method.

        $lab_htextnav = Configuration::get('lab_htextnav');
        $lab_hbgheadercolor = Configuration::get('lab_hbgheadercolor');
        $lab_htextheader = Configuration::get('lab_htextheader');
        $lab_hhovertext = Configuration::get('lab_hhovertext');
        $lab_hhovertextnav =Configuration::get('lab_hhovertextnav');
        //megamenu
        $lab_mbghover = Configuration::get('lab_mbghover');
        $lab_mbgcolor = Configuration::get('lab_mbgcolor');
        $lab_mtext = Configuration::get('lab_mtext');
        $lab_mtexthover = Configuration::get('lab_mtexthover');
        $lab_mbgsubmenu = Configuration::get('lab_mbgsubmenu');
        $lab_mtextsub =Configuration::get('lab_mtextsub');
        $lab_mhovertextsub =Configuration::get('lab_mhovertextsub');
        //maincontetn
        $lab_cbgcolor = Configuration::get('lab_cbgcolor');
        $lab_ccolorlink = Configuration::get('lab_ccolorlink');
        $lab_chovercolorlink = Configuration::get('lab_chovercolorlink');
        $lab_ccolortext = Configuration::get('lab_ccolortext');
        $lab_ccolorprice = Configuration::get('lab_ccolorprice');
        $lab_ccoloroldprice = Configuration::get('lab_ccoloroldprice');
        $lab_ciconcolor = Configuration::get('lab_ciconcolor');
        $lab_cbgbuttom = Configuration::get('lab_cbgbuttom');
        $lab_ctextbuttom = Configuration::get('lab_ctextbuttom');
        $lab_ctextbuttomhover = Configuration::get('lab_ctextbuttomhover');
        $lab_cbgbuttomhover = Configuration::get('lab_cbgbuttomhover');
        // ctooll fontend
        $labpaneltool = (int)Configuration::get('show_fontend');
        $lab_hbgnav = Configuration::get('lab_hbgnav');

        // footer
        $labskin = Configuration::get('labskin');
        $labshowskin = Configuration::get('show_labskin');
        $lab_showsolor = Configuration::get('show_color');
        $lab_EnableFTM = Configuration::get('lab_EnableFTM');
        $lab_EnableQv = Configuration::get('lab_EnableQv');
        $lab_EnableW = Configuration::get('lab_EnableW');
        $lab_EnableC = Configuration::get('lab_EnableC');
        $lab_EnablesubCate = Configuration::get('lab_EnablesubCate');
        $labshowthemecolor = Configuration::get('labshowthemecolor');
        $labthemecolor = Configuration::get('labthemecolor');
        $lab_fbgcolor = Configuration::get('lab_fbgcolor');
        $lab_fcolortext = Configuration::get('lab_fcolortext');
        $lab_flinkcolor = Configuration::get('lab_flinkcolor');
        $lab_flinkcolorhover = Configuration::get('lab_flinkcolorhover');
        $lab_mode_theme = Configuration::get('lab_mode_theme');
        $ps = [
            'LAB_THEMENAME' => _THEME_NAME_,
            'PS_BASE_URL' => _PS_BASE_URL_,
            'PS_BASE_URI' => __PS_BASE_URI__,
            'modules_dir' => _MODULE_DIR_,
            //start color
            'lab_hbgnav' =>$lab_hbgnav, // header
            'lab_htextnav' =>$lab_htextnav,
            'lab_hbgheadercolor' =>$lab_hbgheadercolor,
            'lab_htextheader' =>$lab_htextheader,
            'lab_hhovertext' =>$lab_hhovertext,
            'lab_showskin' =>$labshowskin,
            'lab_hhovertextnav' =>$lab_hhovertextnav,
            'lab_mbghover' =>$lab_mbghover, //  megamenu
            'lab_mbgcolor' =>$lab_mbgcolor,
            'lab_mtext' =>$lab_mtext,
            'lab_mtexthover' =>$lab_mtexthover,
            'lab_mbgsubmenu' =>$lab_mbgsubmenu,
            'lab_mtextsub' =>$lab_mtextsub,
            'lab_mhovertextsub' =>$lab_mhovertextsub,
            //maincontent
            'lab_cbgcolor' =>$lab_cbgcolor,
            'lab_ccolorlink' =>$lab_ccolorlink,
            'lab_chovercolorlink' =>$lab_chovercolorlink,
            'lab_ccolortext' =>$lab_ccolortext,
            'lab_ccolorprice' =>$lab_ccolorprice,
            'lab_ccoloroldprice' =>$lab_ccoloroldprice,
            'lab_ciconcolor' =>$lab_ciconcolor,
            'lab_cbgbuttom' =>$lab_cbgbuttom,
            'lab_ctextbuttom' =>$lab_ctextbuttom,
            'lab_ctextbuttomhover' =>$lab_ctextbuttomhover,
            'lab_cbgbuttomhover' =>$lab_cbgbuttomhover,
            //footer
            'lab_fbgcolor' => $lab_fbgcolor,
            'lab_fcolortext'=>$lab_fcolortext ,
            'lab_flinkcolor'=>$lab_flinkcolor ,
            'lab_flinkcolorhover'=>$lab_flinkcolorhover ,
            // end color
            'lab_show_color' =>(int)$lab_showsolor, //show color
            'lab_paneltool' => $labpaneltool,
            'lab_EnableFTM' =>(int)$lab_EnableFTM, //show color
            'lab_EnableQv' =>(int)$lab_EnableQv, //show color
            'lab_EnableW' =>(int)$lab_EnableW, //show color
            'lab_EnableC' =>(int)$lab_EnableC, //show color
            'lab_EnablesubCate' =>(int)$lab_EnablesubCate, //show color
            'labshowthemecolor' => $labshowthemecolor, // color skin
            'labthemecolor' => $labthemecolor, // color skin
            'lab_mode_theme' => $lab_mode_theme, // mode theme
            'labskin' => Configuration::get('labskin'), // bachground skin
        ];

        return $ps;
    }
}
