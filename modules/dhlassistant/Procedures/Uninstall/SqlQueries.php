<?php

use DhlAssistant\Wrappers;

$prefix = Wrappers\ConfigWrapper::Get('DbPrefix');

$sql_queries = [];

$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}sourceorderadditionalparams`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}shipment`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}shipperpreset`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}shipmentpreset`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}packageitem`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}shipmentspecialservices`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}shipmentside`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}contact`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}address`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}dhluser`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}setting`;";
$sql_queries[] = "DROP TABLE IF EXISTS `{$prefix}carrier_codes`;";

?>