<?php

$flock = fopen(__FILE__, 'r');
if (!flock($flock, LOCK_EX | LOCK_NB)) {
    die('Synchronization pending');
}

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/../../../config/config.inc.php';

set_time_limit(0);



function synchronize($hash){
    if($hash == AdminFreshmailWizardController::NEW_LIST_KEY){
        foreach (Shop::getShops() as $shop ) {
            if($shop['domain'] == Tools::getHttpHost()){
                $fs = (new FreshmailSettings(Db::getInstance()))->findForShop((int)$shop['id_shop']);
                $hash = $_GET['hash'] = $fs->subscriber_list_hash;
                break;
            }
        }
    }
    $fs = (new FreshmailSettings(Db::getInstance()))->getByHash($hash);

    if (!Validate::isLoadedObject($fs)) {
        die('List doesn\'t  exists');
    }

    \FreshMail\Tools::addEmailsToSynchronize($fs->id_shop, $fs->subscriber_list_hash);

    require_once __DIR__.'/send_subscribers.php';

    $fm = new \FreshMail\Freshmail($fs->api_token);
    $fm->triggerExport($fs->subscriber_list_hash);

    sleep(5);

    require_once __DIR__.'/synchronize_subscribers.php';
}

if(!Module::isInstalled('freshmail') || !($module = Module::getInstanceByName('freshmail')) ){
    die('Module isn\'t installed');
}


if(!\FreshMail\Tools::is_cli() && Tools::getValue('token') != $module->getCronToken() ) {
    die('Bad token');
}

if(\FreshMail\Tools::is_cli()){
    $query = new DbQuery();
    $query->select('*')
        ->from(\Freshmail\Entity\FreshmailSetting::$definition['table'])
        ->where('synchronize = 1')
        ->where('wizard_completed = 1')
        ->where('subscriber_list_hash != ""');

    foreach (Db::getInstance()->executeS($query) as $fs){
        $_GET['hash'] = $fs['subscriber_list_hash'];
        synchronize($fs['subscriber_list_hash']);
    }
} else {
    synchronize(Tools::getValue('hash'));
}


echo "synchronization done\n";
