<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    PrestaHelp.com / Rafał Przybylski
 *  @copyright 2021 PrestaHelp
 *  @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/classes/AuthDSDescription.php';
require_once __DIR__.'/classes/Description.php';

class phdescription extends Module
{

    private $hookName = 'displayModularDESC';

    public function __construct()
    {
        $this->name = 'phdescription';
        $this->tab = 'other';
        $this->version = '1.1.0';
        $this->author = 'PrestaHelp';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Modułowy opis produktu');
        $this->description = $this->l('Dzięki temu modułowi Twój opis produktu jeszcze nie był tak oryginalny!');
        $this->confirmUninstall = $this->l('Odinstalowanie modułu nie powoduje utraty żadnych danych.');
        ini_set('max_input_vars', 20000);
    }

    public function install()
    {
        if (!$this->setInst()) {
            return false;
        }
        $this->installSql();
        if(!parent::install() ||
            !$this->installHook() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayAdminProductsExtra') ||
            !$this->registerHook('actionProductSave') ||
            !$this->registerHook('displayModularDESC') ||
            !$this->registerHook('displayFooterProduct')
        ) {
            return false;
        } else {
            return true;
        }
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    private function setInst() {
        $shop = new Shop($this->context->shop->id);
        $auth = new AuthDSDescription($this->name);
        if ($auth->makeTools($this->displayName, $this->version, $shop->getBaseURL())){
            return true;
        }
        return false;
    }

    private function isActiveModule($showLicence = null)
    {
        $shop = new Shop((int)$this->context->shop->id);
        $auth = new AuthDSDescription($this->name, $shop->getBaseURL());
        return $auth->checkLicence();
    }

    private function installSql()
    {
        require_once __DIR__.'/sql/install.php';
    }

    private function installHook()
    {
        $issetHook = Hook::getIdByName($this->hookName);
        $res = true;
        if (empty($issetHook)) {
            $hook = new Hook();
            $hook->name = $this->hookName;
            $hook->title = $this->hookName;
            $hook->description = 'display module ModularDescription Description';
            $res &= $hook->add();
        }
        return $res;
    }

    public function getContent()
    {
        $output = '';
        $this->adminSubmit();
        if ($this->isActiveModule()) {
            $this->prestahelp();
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/phelp-top.tpl');
            $output .= $this->updateModule();
            $this->context->smarty->assign(array(
                'edit' => (int)Configuration::get('PHDESCRIPTION_EDIT'),
                'type' => (int)Configuration::get('PHDESCRIPTION_CURRENT_TYPE'),
            ));
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/module.tpl');
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/phelp-bottom.tpl');
        } else {
            if (Tools::getIsset('submitCheckLicence')) {
                $shop = new Shop((int)$this->context->shop->id);
                $auth = new AuthDSDescription($this->name, $shop->getBaseURL());
                $auth->checkMainLicence();
                Tools::redirectLink($_SERVER['HTTP_REFERER']);
            }
            $this->context->smarty->assign(array(
                'moduleDomain' => $this->context->shop->domain,
                'moduleName' => $this->displayName,
                'moduleName2' => $this->name,
            ));
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/nolic.tpl');
        }
        return $output;
    }

    private function adminSubmit()
    {
        if (Tools::getIsset('submitSaveSettings')) {
            Configuration::updateValue('PHDESCRIPTION_EDIT', (int)Tools::getValue('PHDESCRIPTION_EDIT'));
            Configuration::updateValue('PHDESCRIPTION_CURRENT_TYPE', (int)Tools::getValue('PHDESCRIPTION_CURRENT_TYPE'));
            Tools::redirect($_SERVER['HTTP_REFERER']);
        }
        if (Tools::getIsset('checkUpdate')) {
            $this->update();
        }
    }

    private function prestahelp()
    {
        $this->context->controller->addCSS($this->_path . 'views/assets/css/phelp.css');

        $module = Module::getInstanceByName($this->name);
        $mversion = $module->version;

        $ssl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $shop = new Shop((int)$this->context->shop->id);
        $auth = new AuthDSDescription($this->name, $shop->getBaseURL());
        $productsShow = $auth->getBaners();
        $authorInfo = $auth->getAuthor();
        $chlogInfo = $auth->getChangelog();
        $chlogInfoOther = $auth->getChangelogOther();
        $currentVersion = $auth->getCurrentModuleVersion();
        /* prestahelp API - do not remove! */
        $phelpBtm = __DIR__ . '/views/templates/admin/phelp-bottom.tpl';
        $phelpTop = __DIR__ . '/views/templates/admin/phelp-top.tpl';
        $moduleAssets = $ssl . $this->context->shop->domain . $this->context->shop->physical_uri . 'modules/' . $this->name . '/views/assets/';
        $lastestVersion = $currentVersion['version'] == $mversion ? true : false;
        $updateLink = 'https://modules.prestahelp.com/moduly/' . $this->name . '/' . $this->name . $currentVersion['version'] . '.zip';
        $indexLink = 'https://modules.prestahelp.com/moduly/' . $this->name . '/';
        $banersHtml = AuthDSDescription::getBanersHtml();

        $licence = $auth->getLicence();

        $this->context->smarty->assign(array(
            'moduleVersion' => $mversion,
            'moduleAssets' => $moduleAssets,
            'phelpBtm' => $phelpBtm,
            'phelpTop' => $phelpTop,
            'productsShow' => (array)$productsShow,
            'authorInfo' => $authorInfo,
            'chlogInfo' => $chlogInfo,
            'moduleName' => $module->displayName,
            'moduleNameInfo' => $module->name,
            'currentModuleVersion' => $currentVersion['version'],
            'lastestVersion' => $lastestVersion,
            'updateLink' => $updateLink,
            'chlogInfoOther' => $chlogInfoOther,
            'indexLink' => $indexLink,
            'banersHtml' => $banersHtml,
            'psVersion' => _PS_VERSION_,
            'themeName' => $this->context->shop->theme_name,

            'updated_date' => date('d-m-Y', strtotime($licence['licence']->date_expire_update)),
            'licence_update' => $licence['licence']->licence_update,
            'support_date' =>date('d-m-Y', strtotime($licence['licence']->date_expire_support)),
            'licence_date' => date('d-m-Y', strtotime($licence['licence']->date_expire)),
            'activeted' => $licence,
            'licence' => $licence['licence'],
        ));
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->isActiveModule()) {
            if (self::ps17()) {
                $this->context->controller->addJqueryUI('ui.sortable');
                $this->context->controller->addJS('https://kit.fontawesome.com/1a0ce11d41.js');
                $this->context->controller->addJS($this->_path . 'views/js/back.js');
                $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            } else {
                $this->context->controller->addJS('https://kit.fontawesome.com/1a0ce11d41.js');
                $this->context->controller->addJquery();
                $this->context->controller->addJqueryUI('ui.sortable');
                $this->context->controller->addJS($this->_path . 'views/js/back_16.js');
                $this->context->controller->addCSS($this->_path . 'views/css/back_16.css');
            }
        }
    }

    public function hookDisplayHeader()
    {
        if ($this->isActiveModule()) {
            $this->context->controller->addCSS($this->_path . 'views/css/front.css');
            $this->context->controller->addJS($this->_path . 'views/js/front.js');
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if ($this->isActiveModule()) {
            $output = '';
            $id_product = (int)Tools::getValue('id_product');
            if ($id_product == 0) {
                $id_product = (int)$params['id_product'];
            }
            $ssl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $list = Description::getProductDescriptionByLang((int)$id_product, (int)$this->context->shop->id);
            $ssl_opt = null;
            if ($ssl == 'http://') {
                $ssl_opt = false;
            }
            $this->context->smarty->assign(array(
                'id_product' => $id_product,
                'id_shop' => $this->context->shop->id,
                'addDescriptionUrl' => $this->context->link->getModuleLink($this->name, 'addDescription', array(), $ssl_opt),
                'deleteDescription' => $this->context->link->getModuleLink($this->name, 'deleteDescription', array(), $ssl_opt),
                'changePositionUrl' => $this->context->link->getModuleLink($this->name, 'changePosition', array(), $ssl_opt),
                'getDescriptionUrl' => $this->context->link->getModuleLink($this->name, 'getDescription', array(), $ssl_opt),
                'addFileUrl' => $this->context->link->getModuleLink($this->name, 'addFile', array(), $ssl_opt),
                'list' => $list,
                'languages' => Language::getLanguages(false, $this->context->shop->id),
                'edit' => (int)Configuration::get('PHDESCRIPTION_EDIT'),
                'type' => (int)Configuration::get('PHDESCRIPTION_CURRENT_TYPE'),
            ));
            if (self::ps17()) {
                $output .= $this->hookDisplayAdminProductsExtra_17($params);
            } else {
                $output .= $this->hookDisplayAdminProductsExtra_16($params);
            }
            return $output;
        }
    }

    public function hookDisplayAdminProductsExtra_17($params)
    {
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/product.tpl');
    }

    public function hookDisplayAdminProductsExtra_16($params)
    {
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/product_16.tpl');
    }

    private static function ps17()
    {
        return Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    public function hookActionProductSave($params)
    {
        if ($this->isActiveModule()) {
            $phdescription = (array)Tools::getValue('phdescriptionE');
            if (!empty($phdescription)) {
                foreach ($phdescription as $item) {
//                    $languages = Language::getLanguages(false, (int)$this->context->shop->id);
                    $id_product = (int)$item['id_product'];
                    $id_description = (int)$item['id_description'];
                    $type = (int)$item['type'];
                    $text = array();
                    $text2 = array();
                    $image = array();
                    $image2 = array();
                    if ($id_product > 0 && $id_description > 0) {
                        switch ($item['type']) {
                            case 1:
                                $image = $item['image'];
                                break;
                            case 2:
                                $text = $item['text'];
                                break;
                            case 3:
                                $text = $item['text3'];
                                $image = $item['image3'];
                                break;
                            case 4:
                                $text = $item['text4'];
                                $image = $item['image4'];
                                break;
                            case 5:
                                // 2 small image
                                $image = $item['image5'];
                                $image2 = $item['image6'];
                                break;
                            case 6:
                                $text = $item['text6'];
                                $text2 = $item['text7'];
                                break;
                        }
                        if (!empty($text)) {
                            foreach ($text as $id_lang => $textItem) {
                                $description_lang = Description::getProductDescriptionLang((int)$id_description, (int)$id_product, (int)$id_lang);
                                $textItem2 = '';
                                if (!empty($text2) && isset($text2[$id_lang])) {
                                    $textItem2 = $text2[$id_lang];
                                }
                                $imageItem = '';
                                if (!empty($image) && isset($image[$id_lang])) {
                                    $imageItem = $image[$id_lang];
                                }
                                $imageItem2 = '';
                                if (!empty($image2) && isset($image2[$id_lang])) {
                                    $imageItem2 = $image2[$id_lang];
                                }
                                if (empty($description_lang)) {
                                    Description::addDescriptionLang((int)$id_description, (int)$id_product, $textItem, $textItem2, $imageItem, $imageItem2, (int)$id_lang);
                                } else {
                                    Description::updateDescriptionLang((int)$id_description, (int)$id_product, $textItem, $textItem2, $imageItem, $imageItem2, (int)$id_lang);
                                }
                            }
                        } else {
                            if (!empty($image)) {
                                foreach ($image as $id_lang => $imageItem) {
                                    $description_lang = Description::getProductDescriptionLang((int)$id_description, (int)$id_product, (int)$id_lang);
                                    $textItem = '';
                                    if (!empty($text) && isset($text[$id_lang])) {
                                        $textItem = $text[$id_lang];
                                    }
                                    $textItem2 = '';
                                    if (!empty($text2) && isset($text2[$id_lang])) {
                                        $textItem2 = $text2[$id_lang];
                                    }
                                    $imageItem2 = '';
                                    if (!empty($image2) && isset($image2[$id_lang])) {
                                        $imageItem2 = $image2[$id_lang];
                                    }
                                    if (empty($description_lang)) {
                                        Description::addDescriptionLang((int)$id_description, (int)$id_product, $textItem, $textItem2, $imageItem, $imageItem2, (int)$id_lang);
                                    } else {
                                        Description::updateDescriptionLang((int)$id_description, (int)$id_product, $textItem, $textItem2, $imageItem, $imageItem2, (int)$id_lang);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookDisplayFooterProduct($params)
    {
        if ($this->isActiveModule()) {
            $output = '';
            $id_product = (int)Tools::getValue('id_product');
            if ($id_product == 0) {
                $id_product = (int)$params['id_product'];
            }
            $list = Description::getProductDescription((int)$id_product, (int)$this->context->cookie->id_lang);
            $this->context->smarty->assign(array(
                'id_product' => $id_product,
                'id_shop' => $this->context->shop->id,
                'addDescriptionUrl' => $this->context->link->getModuleLink($this->name, 'addDescription'),
                'addFileUrl' => $this->context->link->getModuleLink($this->name, 'addFile'),
                'list' => $list,
            ));
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/hook/product.tpl');
            return $output;
        }
    }

    public function hookDisplayModularDESC($params)
    {
        $params['id_product'] = $params['pro']->id;
        return $this->hookDisplayFooterProduct($params);
    }

    private function updateModule()
    {
        $update = false;
        $updateVersion = array();
        $issetHook = Hook::getIdByName($this->hookName);
        if (empty($issetHook)) {
            $update = true;
            $updateVersion[] = 'hook';
        }
        $this->context->smarty->assign(array(
            'update' => $update,
            'updateVersion' => $updateVersion,
        ));
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/updateInfo.tpl');
    }

    private function update()
    {
        $issetHook = Hook::getIdByName($this->hookName);
        if (empty($issetHook)) {
            $hook = new Hook();
            $hook->name = $this->hookName;
            $hook->title = $this->hookName;
            $hook->description = 'display module ModularDescription Description';
            $hook->add();
        }
        Tools::redirect($_SERVER['HTTP_REFERER']);
    }

}
