<?php
/**
 * 2007-2020 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <contact@etssoft.net>
 * @copyright  2007-2020 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

include(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/ph_instagram.php');
$module = Module::getInstanceByName('ph_instagram');
$token = Tools::getValue('secure', false);
$token_config = Configuration::getGlobalValue('PH_INSTAGRAM_CRONJOB_TOKEN');
if(!$token || $token !== $token_config){
    return die('Access denied');
}
@ini_set('display_errors', 'off');
$timeSaveToken = Configuration::get('PH_INSTAGRAM_TOKEN_SLT');
$dateDiff = 100;
if($timeSaveToken){
    $timeDiff = time() - (int)$timeSaveToken;
    $dateDiff = $timeDiff / (60*60*24);
}
if($dateDiff >= 80){
    $accessToken = $module->refreshToken();
    if($accessToken){
        die('Token refresh successfully');
    }
}
die('Not need to refresh access token now');