<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

$module_instance = Module::getInstanceByName('ruch');

$json = Tools::jsonDecode(Tools::file_get_contents('php://input'), true);
if (!isset($json['token']) || $json['token'] != sha1(_COOKIE_KEY_.$module_instance->name)) {
    exit;
}

if (isset($json['a']) && $json['a'] == 's') {
    $resp = RuchLib::setPoint($json['pkt'], $json['typ']);
    if ($resp != 'OK') {
        die(Tools::jsonEncode(array(
            'error' => $resp
        )));
    } else {
        die(Tools::jsonEncode(array(
            'ok' => 'OK',
        )));
    }
}

if (isset($json['a']) && $json['a'] == 'g') {
    $resp = RuchApi::getPts($json['cod']);
    die(Tools::jsonEncode($resp));
}
