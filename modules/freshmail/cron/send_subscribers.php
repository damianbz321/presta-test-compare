<?php

$flock = fopen(__FILE__, 'r');
if( !flock($flock, LOCK_EX | LOCK_NB  )){
    die('Synchronization pending');
}

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/../../../config/config.inc.php';


if(!Module::isInstalled('freshmail') || !($module = Module::getInstanceByName('freshmail')) ){
    die('Module isn\'t installed');
}

if(!\FreshMail\Tools::is_cli() && Tools::getValue('token') != $module->getCronToken() ) {
    die('Bad token');
}
// 1. utwórz listę
// 2. paczkuj na elementy
// 3. wyślij
// 4. usuń listy

$hash = Tools::getValue('hash');
$fs = (new FreshmailSettings(Db::getInstance()))->getByHash($hash);

$ets = new \FreshMail\Repository\EmailToSynchronize(Db::getInstance());
$es = new \FreshMail\Repository\EmailsSynchronized(Db::getInstance());

if (Validate::isLoadedObject($fs)) {
    set_time_limit(0);

    $fm = new \FreshMail\Freshmail($fs->api_token);
    $hash = Tools::getValue('hash');
    while($ets->getCount($hash) > 0){
        $subscribers = \FreshMail\Tools::convertToSubcriberCollection(\FreshMail\Tools::getEmailsToSynchronize($hash));
        try {
            $response = $fm->addSubscribers($hash, $subscribers, 1, false);
            $es->addSubscribers($hash, $subscribers);

            if(!empty($response['data']['not_inserted'])){
                foreach ($response['data']['errors'] as $err ){
                    if($err['code'] != \FreshMail\FreshmailCode::ALREADY_SUBSCRIBED){
                        var_dump($err);
                        //save email
                    }
                }
            }

        } catch (Exception $e){

        }
         \FreshMail\Tools::removeEmailsFromSynchronization($hash, $subscribers);

    }
    // \FreshMail\Tools::addEmailsToSynchronize($fs->id_shop, $hash);

}

echo ('Sending emails success');
