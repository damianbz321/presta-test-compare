<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2018 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('ppb.php');
$thismodule = new ppb();
if (Tools::getValue('action') == 'updateSlidesPosition') {
    $slides = Tools::getValue('ppbp');
    foreach ($slides as $position => $idb) {
        $res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ppbp_block` SET `position` = '.(int)$position.' WHERE `id` = '.(int)$idb);
    }
}
if (Tools::getValue('search','false') != 'false') {
    $result = $thismodule->searchproduct(Tools::getValue('search'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">#'.$value['id_product'].' - <strong>'.$value['name'].'</strong> - ' . $value['reference'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ppb_products\').val($(\'.ppb_products\').val()+\''.$value['id_product'].',\')">'.$thismodule->addproduct.'</span></p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}

if (Tools::getValue('search_feature','false') != 'false') {
    $result = $thismodule->searchfeature(Tools::getValue('search_feature'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">'.$value['name'].'<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ppb_features\').val($(\'.ppb_features\').val()+\''.$value['id_feature_value'].',\')">'.$thismodule->addproduct.'</span></p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}

if (Tools::getValue('search_feature_type','false') != 'false') {
    $result = $thismodule->searchfeatureName(Tools::getValue('search_feature_type'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">'.$value['feature'].' 
            <span class="addbtn" onclick="$(\'.type_feature_selected\').append(addinputName(\''.$value['feature'].'\',\''.$value['id_feature'].'\'));">
            '.$thismodule->addproduct.'
            </span>
            </p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}

if (Tools::getValue('search_feature_cat','false') != 'false') {
    $result = $thismodule->searchfeature(Tools::getValue('search_feature_cat'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">'.$value['feature'].': '.$value['name'].' 
            <span class="addbtn" onclick="$(\'.cat_feature_selected\').append(addinput(\''.$value['feature'].': '.$value['name'].'\',\''.$value['id_feature_value'].'\'));">
            '.$thismodule->addproduct.'
            </span>
            </p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}


if (Tools::getValue('action') == 'removeTab' && Tools::getValue('id')) {
    $extratab = new PpbBlock(Tools::getValue('id'));
    $extratab->delete();
}

if (Tools::getValue('action') == 'duplicateTab' && Tools::getValue('id')) {
    $extratab = new PpbBlock(Tools::getValue('id'));
    $newtab = $extratab->duplicateObject();
    $newtab->internal_name = "[duplicated] ".$extratab->internal_name;
    $newtab->save();
    echo "location.reload();";
}


if (Tools::getValue('action') == 'toggleTab' && Tools::getValue('id')) {
    $id = Tools::getValue('id');
    $res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ppbp_block` SET `active` = !active
    WHERE `id` = '.(int)$id.'');
    $res = Db::getInstance()->executeS('SELECT active  FROM `'._DB_PREFIX_.'ppbp_block` WHERE `id` = '.(int)$id.'');
    if ($res[0]['active'] == 1) {
        echo "$(\"#ppbp_$id span.off\").attr('class','on');";
    } else {
        echo "$(\"#ppbp_$id span.on\").attr('class','off');";
    }
}