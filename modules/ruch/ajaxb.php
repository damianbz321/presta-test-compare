<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

if (file_exists('ajax_conf.php')) {
    include_once('ajax_conf.php');
} else {
    die(json_encode(array(
        'error' => 'Brak pliku konfiguracyjnego. Sprawdź na liście modułów ostrzeżenia dotyczące tego modułu.'
    )));
}
    
include_once(_PS_ADMIN_DIR_.'/../config/config.inc.php');
require_once(_PS_ADMIN_DIR_.'/init.php');

$module_instance = Module::getInstanceByName('ruch');

$json = Tools::jsonDecode(Tools::file_get_contents('php://input'), true);
if (!isset($json['token']) || $json['token'] != sha1(_COOKIE_KEY_.$module_instance->name)) {
    exit;
}

if (isset($json['action']) && $json['action'] == 'createLabel') {
    $resp = RuchLib::createLabelAjax($json['data']);
    if ($resp === false) {
        die(Tools::jsonEncode(array(
            'error' => reset(Ruch::$errors)
        )));
    }

    die(Tools::jsonEncode(array(
            'error' => false,
            'data' => $resp
    )));
}

if (isset($json['action']) && $json['action'] == 'newLabel') {
    $resp = RuchLib::createLabelDef($json['id_order']);
    if ($resp === false) {
        die(Tools::jsonEncode(array(
               'error' => reset(Ruch::$errors)
        )));
    }

    die(Tools::jsonEncode(array(
        'error' => false,
        'data' => $resp
    )));
}

if (isset($json['action']) && $json['action'] == 'test1') {
    $resp = RuchApi::test($json, true);
    if ($resp != 'OK') {
        die(Tools::jsonEncode(array(
            'error' => $resp
        )));
    }
    else {
        die(Tools::jsonEncode(array(
            'ok' => $module_instance->l('Połączenie OK'),
        )));
    }
}

if (isset($json['action']) && $json['action'] == 'test2') {
    $resp = RuchApi::test($json, false);
    if ($resp != 'OK') {
        die(Tools::jsonEncode(array(
            'error' => $resp
        )));
    }
    else {
        die(Tools::jsonEncode(array(
            'ok' => $module_instance->l('Połączenie OK'),
        )));
    }
}

if (isset($json['action']) && $json['action'] == 'test3') {
    $resp = RuchApi::testCurl($json);
    if ($resp != 'OK') {
        die(Tools::jsonEncode(array(
            'error' => $resp
        )));
    }
    else {
        die(Tools::jsonEncode(array(
            'ok' => $module_instance->l('Połączenie OK'),
        )));
    }
}
