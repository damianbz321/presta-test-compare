<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_4_6($module)
{
    $module->id; // For validator
    $tab = new Tab();
    $tab->active = 1;
    $languages = Language::getLanguages(false);
    if (is_array($languages)) {
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = 'pminpostpaczkomaty';
        }
    }
    $tab->class_name = 'PmInpostPaczkomatyOrder';
    $tab->module = 'pminpostpaczkomaty';
    $tab->id_parent = - 1;
    $tab->add();

    $tab = new Tab();
    $tab->active = 1;
    $languages = Language::getLanguages(false);
    if (is_array($languages)) {
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = 'Inpost';
        }
    }
    $tab->class_name = 'PmInpostPaczkomatyList';
    $tab->module = 'pminpostpaczkomaty';
    $sql = 'SELECT id_tab FROM `'._DB_PREFIX_.'tab` WHERE class_name = "AdminParentShipping"';
    $tab->id_parent = (int)Db::getInstance()->getValue($sql);
    $tab->add();

    return true;
}
