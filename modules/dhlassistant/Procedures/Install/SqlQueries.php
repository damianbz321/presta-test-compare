<?php

use DhlAssistant\Wrappers;

$prefix = Wrappers\ConfigWrapper::Get('DbPrefix');

$sql_queries = [];

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}setting`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Key` varchar(32) NOT NULL,
		`Value` varchar(250) NOT NULL DEFAULT '',
		PRIMARY KEY (`Id`),
		CONSTRAINT `UQ_{$prefix}setting_Key` UNIQUE(`Key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}dhluser`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Name` varchar(32) NOT NULL DEFAULT '',
		`Dhl24Login` varchar(32) NULL DEFAULT NULL,
		`Dhl24Password` varchar(32) NULL DEFAULT NULL,
		`DhlPsLogin` varchar(32) NULL DEFAULT NULL,
		`DhlPsPassword` varchar(32) NULL DEFAULT NULL,
        `Dhl24Link` varchar(120) NULL DEFAULT NULL,
		`DhlPsLink` varchar(120) NULL DEFAULT NULL,
		PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}address`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Name` varchar(60) NOT NULL DEFAULT '',
		`PostalCode` varchar(255) NOT NULL DEFAULT '',
		`City` varchar(255) NOT NULL DEFAULT '',
		`Street` varchar(255) NOT NULL DEFAULT '',
		`HouseNumber` varchar(255) NOT NULL DEFAULT '',
		`ApartmentNumber` varchar(255) NOT NULL DEFAULT '',
		`Phone` varchar(255) NOT NULL DEFAULT '',
		`Email` varchar(255) NOT NULL DEFAULT '',
		`Country` varchar(255) NOT NULL DEFAULT 'PL',
		`OriginalAddressString` varchar(255) NOT NULL DEFAULT '',
		`ParseAlert` TINYINT(1) NOT NULL DEFAULT 0,
		PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";


$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}contact`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Name` varchar(255) NOT NULL DEFAULT '',
		`Phone` varchar(255) NOT NULL DEFAULT '',
		`Email` varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}shipmentside`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`IdAddress` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdContact` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdPreaviso` int(10) UNSIGNED NULL DEFAULT NULL,
		PRIMARY KEY (`Id`),
		CONSTRAINT `FK_{$prefix}shipmentside_Address` FOREIGN KEY(`IdAddress`) REFERENCES `{$prefix}address` (`Id`),
		CONSTRAINT `FK_{$prefix}shipmentside_Contact` FOREIGN KEY(`IdContact`) REFERENCES `{$prefix}contact` (`Id`),
		CONSTRAINT `FK_{$prefix}shipmentside_Preaviso` FOREIGN KEY(`IdPreaviso`) REFERENCES `{$prefix}contact` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}shipmentspecialservices`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`S_1722` TINYINT(1) NOT NULL DEFAULT 0,
		`S_SOBOTA` TINYINT(1) NOT NULL DEFAULT 0,
		`S_NAD_SOBOTA` TINYINT(1) NOT NULL DEFAULT 0,
		`S_UBEZP` TINYINT(1) NOT NULL DEFAULT 0,
		`S_COD` TINYINT(1) NOT NULL DEFAULT 0,
		`S_PDI` TINYINT(1) NOT NULL DEFAULT 0,
		`S_ROD` TINYINT(1) NOT NULL DEFAULT 0,
		`S_POD` TINYINT(1) NOT NULL DEFAULT 0,
		`S_SAS` TINYINT(1) NOT NULL DEFAULT 0,
		`S_ODB` TINYINT(1) NOT NULL DEFAULT 0,
		`S_UTIL` TINYINT(1) NOT NULL DEFAULT 0,
		`UBEZP_Value` DECIMAL(10,2) NULL DEFAULT NULL,
		`COD_Value` DECIMAL(10,2) NULL DEFAULT NULL,
		`COD_PaymentType` varchar(16) NOT NULL DEFAULT '',
		`ROD_Instruction` varchar(32) NOT NULL DEFAULT '',
		`OriginalCODValue` varchar(16) NOT NULL DEFAULT '',
		`OriginalUBEZPValue` varchar(16) NOT NULL DEFAULT '',
		`OriginalCurrencyUnit` varchar(16) NOT NULL DEFAULT '',
		`UBEZP_CurrencyUnitAlert` TINYINT(1) NOT NULL DEFAULT 0,
		`COD_CurrencyUnitAlert` TINYINT(1) NOT NULL DEFAULT 0,
		PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}packageitem`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Type` varchar(16) NOT NULL DEFAULT '',
		`Weight` int(10) NULL DEFAULT NULL,
		`Width` int(10) NULL DEFAULT NULL,
		`Height` int(10) NULL DEFAULT NULL,
		`Length` int(10) NULL DEFAULT NULL,
		`Quantity` int(10) NOT NULL DEFAULT 1,
		`NonStandard` TINYINT(1) NOT NULL DEFAULT 0,
		`EuroReturn` TINYINT(1) NOT NULL DEFAULT 0,
		PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}shipmentpreset`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Name` varchar(32) NOT NULL DEFAULT '',
		`DropOffType` varchar(16) NOT NULL DEFAULT '',
		`ServiceType` varchar(3) NOT NULL DEFAULT '',
		`LabelType` varchar(4) NOT NULL DEFAULT '',
		`Content` varchar(255) NOT NULL DEFAULT '',
		`Comment` varchar(255) NOT NULL DEFAULT '',
		`IdShipmentSpecialServices` int(10) unsigned NULL DEFAULT NULL,
		`ShipmentStartHour` varchar(5) NOT NULL DEFAULT '',
		`ShipmentEndHour` varchar(5) NOT NULL DEFAULT '',
		`IdPackageItem` int(10) unsigned NULL DEFAULT NULL,
		PRIMARY KEY (`Id`),
		CONSTRAINT `FK_{$prefix}shipmentpreset_ShipmentSpecialServices` FOREIGN KEY(`IdShipmentSpecialServices`) REFERENCES `{$prefix}shipmentspecialservices` (`Id`),
		CONSTRAINT `FK_{$prefix}shipmentpreset_PackageItem` FOREIGN KEY(`IdPackageItem`) REFERENCES `{$prefix}packageitem` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}shipperpreset`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`Name` varchar(32) NOT NULL DEFAULT '',
		`BillingAccountNumber` int NULL DEFAULT NULL,
		`CostsCenter` varchar(255) NOT NULL DEFAULT '',
		`DropOffType` varchar(255) NOT NULL DEFAULT '',
		`LabelType` varchar(20) NOT NULL DEFAULT '',
		`Weight` varchar(20) NOT NULL DEFAULT '',
		`Width` varchar(20) NOT NULL DEFAULT '',
		`Height` varchar(20) NOT NULL DEFAULT '',
		`Length` varchar(20) NOT NULL DEFAULT '',
		`IdShipper` int(10) unsigned NULL DEFAULT NULL,
		PRIMARY KEY (`Id`),
		CONSTRAINT `FK_{$prefix}shipperpreset_Shipper` FOREIGN KEY(`IdShipper`) REFERENCES `{$prefix}shipmentside` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}shipment`(
		`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`IdDhlUser` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdSource` int NULL DEFAULT NULL,
		`DhlShipmentId` varchar(32) NULL DEFAULT NULL,
		`DhlOrderId` varchar(32) NULL DEFAULT NULL,
		`DhlShipmentCreationDateTime` DateTime NULL DEFAULT NULL,
		`DropOffType` varchar(16) NOT NULL DEFAULT '',
		`ServiceType` varchar(3) NULL DEFAULT '',
		`LabelType` varchar(4) NOT NULL DEFAULT '',
		`Content` varchar(255) NOT NULL DEFAULT '',
		`Comment` varchar(255) NOT NULL DEFAULT '',
		`Reference` varchar(20) NOT NULL DEFAULT '',
		`ShippingPaymentType` varchar(16) NOT NULL DEFAULT '',
		`BillingAccountNumber` int NULL DEFAULT NULL,
        `SenderName` varchar(255) NULL DEFAULT '',
        `SenderCompany` varchar(255) NULL DEFAULT '',
		`SenderPostalCode` varchar(255) NULL DEFAULT '',
		`SenderCity` varchar(255) NULL DEFAULT '',
		`SenderStreet` varchar(255) NULL DEFAULT '',
		`SenderHouseNumber` varchar(255) NULL DEFAULT '',
		`SenderApartmentNumber` varchar(255) NULL DEFAULT '',
        `SenderPhone` varchar(255) NULL DEFAULT '',
        `SenderEmail` varchar(255) NULL DEFAULT '',
		`PaymentType` varchar(16) NOT NULL DEFAULT '',
		`CostsCenter` varchar(255) NOT NULL DEFAULT '',
		`IdShipmentSpecialServices` int(10) UNSIGNED NULL DEFAULT NULL,
		`ShipmentDate` varchar(11) NOT NULL DEFAULT '',
		`ShipmentStartHour` varchar(5) NOT NULL DEFAULT '',
		`ShipmentEndHour` varchar(5) NOT NULL DEFAULT '',
		`IdShipper` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdReceiver` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdNeighbour` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdPackageItem` int(10) UNSIGNED NULL DEFAULT NULL,
		`CreationDateTime` DateTime NULL DEFAULT NULL,
		`ModificationDateTime` DateTime NULL DEFAULT NULL,
		`ReceiverNick` varchar(255) NOT NULL DEFAULT '',
		`SendToParcelShop` TINYINT(1) NOT NULL DEFAULT 0,
		`SendToParcelLocker` TINYINT(1) NOT NULL DEFAULT 0,
		`ParcelIdent` varchar(255) NOT NULL DEFAULT '',
		`Postnummer` varchar(255) NOT NULL DEFAULT '',
		`ParcelPostalCode` varchar(255) NOT NULL DEFAULT '',
		`HasError` TINYINT(1) NOT NULL DEFAULT 0,
		`ErrorMessage` text NOT NULL,
		`IdShipperPreset` int(10) UNSIGNED NULL DEFAULT NULL,
		`IdShipmentPreset` int(10) UNSIGNED NULL DEFAULT NULL,
		PRIMARY KEY (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_DhlUser` FOREIGN KEY(`IdDhlUser`) REFERENCES `{$prefix}dhluser` (`Id`) ON DELETE CASCADE,
		CONSTRAINT `FK_{$prefix}shipment_ShipmentSpecialServices` FOREIGN KEY(`IdShipmentSpecialServices`) REFERENCES `{$prefix}shipmentspecialservices` (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_Shipper` FOREIGN KEY(`IdShipper`) REFERENCES `{$prefix}shipmentside` (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_Receiver` FOREIGN KEY(`IdReceiver`) REFERENCES `{$prefix}shipmentside` (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_Neighbour` FOREIGN KEY(`IdNeighbour`) REFERENCES `{$prefix}address` (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_Package` FOREIGN KEY(`IdPackageItem`) REFERENCES `{$prefix}packageitem` (`Id`),
		CONSTRAINT `FK_{$prefix}shipment_ShipperPreset` FOREIGN KEY(`IdShipperPreset`) REFERENCES `{$prefix}shipperpreset` (`Id`) ON DELETE SET NULL,
		CONSTRAINT `FK_{$prefix}shipment_ShipmentPreset` FOREIGN KEY(`IdShipmentPreset`) REFERENCES `{$prefix}shipmentpreset` (`Id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}sourceorderadditionalparams`(
	`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`IdSourceObject` int NULL DEFAULT NULL,
	`CountryCode` varchar(2) NOT NULL DEFAULT '',
	`SendToParcelShop` TINYINT(1) NOT NULL DEFAULT 0,
	`SendToParcelLocker` TINYINT(1) NOT NULL DEFAULT 0,
	`ParcelIdent` varchar(255) NOT NULL DEFAULT '',
	`Postnummer` varchar(255) NOT NULL DEFAULT '',
	`ParcelPostalCode` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql_queries[] = "CREATE TABLE IF NOT EXISTS `{$prefix}carrier_codes`(
   `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `carrier_id` int NOT NULL,
   `carrier_code` varchar(255) NOT NULL DEFAULT '',
   PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

?>