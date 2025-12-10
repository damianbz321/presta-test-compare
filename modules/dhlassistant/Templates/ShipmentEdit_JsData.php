<?php if(!isset($is_template)) die(); ?>
<?php
	use DhlAssistant\Core;
	use DhlAssistant\Wrappers;
	use DhlAssistant\Classes\DataModels;
	use DhlAssistant\Classes\Managers;
	use DhlAssistant\Classes\Dhl\Enums;
	
	$dhl_user_countries = array();
	/* @var $dhl_user DataModels\DhlUser */
	
	$available_service_codes = array();
	foreach (Managers\DhlUserManager::GetList() as $dhl_user_id => $dhl_user)
		$dhl_user_countries[$dhl_user_id] = $dhl_user->GetAvailableCountries();
	echo 'DhlUserCountries = '.json_encode($dhl_user_countries).";\n";

	//ENUMS
	$enums_result = array();
	$req_enums = array('Country', 'DropOffType', 'LabelType', 'PackageType', 'PaymentType', 'ServiceType', 'ShippingPaymentType', 'SpecialService');
	foreach ($req_enums as $enum_name)
	{
		$enum = 'DhlAssistant\Classes\Dhl\Enums\\'.$enum_name;
		$enums_result[$enum_name] = $enum::$Descriptions;
	}
	echo 'DhlEnums = '.json_encode($enums_result).";\n";
	echo 'ShipmentPresets = '.json_encode(Wrappers\DbWrapper::LoadAll(DataModels\ShipmentPreset::GetTreeDataObjectInfo())).";\n";
	echo 'DhlServiceOrder ='.json_encode(array_keys(Enums\ServiceType::$Descriptions)).";\n";
?>