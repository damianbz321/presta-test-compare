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

set_time_limit(0);

$date = date('Y-m-d', strtotime(Tools::getValue('birthdate', date('Y-m-d'))));

$repository = new \FreshMail\Repository\Birthdays(Db::getInstance());

$birthdays = [];
if(Tools::getValue('id_shop')){
    $birthday = $repository->findForShop(Tools::getValue('id_shop'));
    if(Validate::isLoadedObject($birthday) && $birthday->enable){
        $birthdays[] = $birthday;
    }
} else {
    $key = \FreshMail\Entity\Birthday::$definition['primary'];
    foreach ($repository->getActive() as $item) {
        $birthdays[] = new \FreshMail\Entity\Birthday($item[$key]);
    }
}

$service = new \FreshMail\Service\BirthdayService();

foreach ($birthdays as $birthday){
    $query = new DbQuery();
    $query->select('*')
        ->from('customer')
        ->where(sprintf('MONTH(birthday) = MONTH("%s")', $date))
        ->where(sprintf('DAY(birthday) = DAY("%s")', $date))
        ->where('id_shop = '.(int)$birthday->id_shop)
    ;
    $customers = Db::getInstance()->executeS($query);
    foreach ($customers as $c){
        $service->sendNotifications(new Customer($c['id_customer']), $birthday);
    }

}

