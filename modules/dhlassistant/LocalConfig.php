<?php

use DhlAssistant\Core;

$local_config['ModuleName'] = 'dhlassistant';
$local_config['Version'] = '1.7';
$local_config['Author'] = 'DHL Parcel Polska';
$local_config['FullName'] = 'Asystent DHL dla Presta Shop';
$local_config['Description'] = 'DHL Parcel  moduł wysyłek';
$local_config['CarrierName'] = 'Wysyłka DHL';
$local_config['UninstallQuestion'] = 'Czy na pewno chcesz odinstalować moduł?';
$local_config['DefaultLang'] = 'PL';

$local_config['DefaultParcelShopInPL'] = true;
$local_config['ConnectionIdent'] = 'PS_SII_17';
$local_config['ShipmentHoursStart'] = new \DateTime('09:00');
$local_config['ShipmentHoursEnd'] = new \DateTime('19:00');
$local_config['FileNameSalt'] = 'Ya1l$rGe';
$local_config['ReportsPath'] = 'Var/Reports/';
$local_config['ScansPath'] = 'Var/Scans/';
$local_config['ReportsDir'] = Core::$BASEDIR . $local_config['ReportsPath'];
$local_config['ScansDir'] = Core::$BASEDIR . $local_config['ScansPath'];
$local_config['TrackLinkTemplate'] = 'http://www.dhl.com.pl/sledzenieprzesylkikrajowej/szukaj.aspx?m=0&sn=%s';
$local_config['DefaultSeverity'] = 2;
$local_config['AllowDuplicateLogs'] = true;

?>