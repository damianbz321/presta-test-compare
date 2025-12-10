<?php

namespace FreshMail;

use Db;
use FreshMail\Entity\Hook;
use FreshMail\Installer\Tabs;
use Language;
use Module;
use Tab;

class Installer implements Interfaces\Installer
{
    use Hooks;
    use HooksForms;

    private $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }


    public function install(): bool
    {
        $return = $this->installMenuAdmin();
        foreach ($this->getHooks() as $hook) {
            $return &= $this->module->registerHook($hook);
        }
        foreach ($this->getHooksForm() as $hook) {
            $return &= $this->module->registerHook($hook);
        }

        return $return
            && $this->loadSQLFile(__DIR__ . '/../../install/install.sql')
        //    && $this->initData()
        ;
    }


    public function uninstall(): bool
    {
        $result = true;
        foreach (Tabs::getTabs('extended') as $tab) {
            $result &= $this->uninstallTab($tab['controller']);
        }

        return $result
            && $this->uninstallTab('AdminFreshmailWizard')
            && $this->uninstallTab('AdminFreshmail')
            && $this->loadSQLFile(__DIR__ . '/../../install/uninstall.sql');

    }

    public function installMenuAdmin()
    {
        return $this->installTab('AdminFreshmail', 'FRESHMAIL')
            && $this->installTab('AdminFreshmailWizard', 'AdminFreshmailWizard', false, true)
            && Tabs::install($this->module);
    }

    public function installTab($controllerClassName, $tabName, $tabParentControllerName = false, $withoutTab = false, $icon = '')
    {
        $idTab = (int)Tab::getIdFromClassName($controllerClassName);
        if(!empty($idTab)){
            return true;
        }

        $tab = new Tab();

        $tab->active = 1;
        $tab->class_name = $controllerClassName;
        $tab->name = [];
        $tab->icon = $icon;

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->module->l($tabName);
        }

        if ($withoutTab) {
            $tab->id_parent = -1;
        } elseif ($tabParentControllerName) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tabParentControllerName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->module->name;

        return $tab->add();
    }

    /**
     * @param $controllerClassName
     * @return bool
     */
    public function uninstallTab($controllerClassName)
    {
        $idTab = (int)Tab::getIdFromClassName($controllerClassName);

        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }

    public function loadSQLFile($sqlFile)
    {
        $sqlContent = file_get_contents($sqlFile);
        $sqlContent = str_replace('PREFIX_', _DB_PREFIX_, $sqlContent);
        $sqlRequests = preg_split("/;\s*[\r\n]+/", $sqlContent);

        $result = true;

        foreach ($sqlRequests as $request) {
            if (!empty($request)) {
                $result &= Db::getInstance()->execute(trim($request));
            }
        }

        return $result;
    }

    public function initData(){
        $hooks = [
            'displayFooterProduct' => [
                'title' => 'Product footer',
                'description' => 'This hook adds new blocks under the product\'s description'
            ],
            'displayHeader' => [
                'title' => 'Added in the header of every page',
                'description' => 'This hook adds new blocks in header'
            ],
            'displayHome' => 'Displayed on the content of the home page.',
            [
                'title' => 'Homepage content',
                'description' => 'This hook displays new elements on the homepage'
            ],
            'displayRightColumnProduct' => [
                'title' => 'New elements on the product page (right column)',
                'description' => 'This hook displays new elements in the right-hand column of the product page'
            ],
            'displayLeftColumnProduct' => [
                'title' => 'New elements on the product page (left column)',
                'description' => 'This hook displays new elements in the left-hand column of the product page'
            ],
            'displayTop' => [
                'title' => 'Top of pages',
                'description' => 'This hook displays additional elements at the top of your pages'
            ],
            'pop_up' => [
                'title' => 'Pop up form',
                'description' => 'It\'s only for pop up forms'
            ],
            'freshmail_form' => [
                'title' => 'Custom hook',
                'description' => 'You can add this hook in every place You want'
            ]
        ];
        $return = true;

        foreach ($hooks as $hook => $item){
            $obj = new Hook();
            $obj->hook_name = $hook;
            $obj->title = $this->module->l($item['title']);
            $obj->description = $this->module->l($item['description']);
            $return &= $obj->save();
        }

        return $return;
    }
}

