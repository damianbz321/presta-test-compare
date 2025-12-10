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

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) &&
Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) {
    exit;
}

if (Tools::isSubmit('printLabel')) {
    if (Tools::isSubmit('bulk')) {
        $arr = RuchApi::getLabelPdf(Tools::getValue('ids'), true);
        $naz = 'ruch_labels.pdf';
    } else {
        $arr = RuchApi::getLabelPdf(Tools::getValue('id_label'));
        $naz = 'ruch_label_'. sprintf("%07d", $arr['num']) .'.pdf';
    }

    if ($arr == null) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    ob_end_clean();
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $naz . '"');
    echo $arr['data'];
}
