<?php

use DhlAssistant\Wrappers\ConfigWrapper;
use DhlAssistant\Classes\DataModels\DhlApi;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Core;
use DhlAssistant\Wrappers\DbWrapper;

/**
 * @param $settings_form
 * @return mixed
 */
function setLink24($settings_form)
{
    $DHl24LinkDB = DbWrapper::GetDhl24Link();
    $accountType = $settings_form->Fields['DhlUser:AccountType']->Value;

    if (($DHl24LinkDB == '') || ($DHl24LinkDB == null)) {
        $possibleValues = $settings_form->Fields['DhlUser:Dhl24Link']->PossibleValues;
        $Dhl24Link = array_search($accountType, $possibleValues);
    } else {
        $Dhl24Link = $DHl24LinkDB;
    }

    $settings_form->Fields['DhlUser:Dhl24Link']->Value = $Dhl24Link;

    return $settings_form;
}

$api = new DhlApi();
$api->Code = Enums\DhlWebApi::DHL24;
$api->Name = 'Dhl 24';
$api->FullName = Enums\DhlWebApi::$Descriptions[Enums\DhlWebApi::DHL24];
$api->SupportedServiceCodes = [
    Enums\ServiceType::AH,
    Enums\ServiceType::PR,
    Enums\ServiceType::D09,
    Enums\ServiceType::D12,
    Enums\ServiceType::EK,
    Enums\ServiceType::CP,
    Enums\ServiceType::CM,
    Enums\ServiceType::PI,
];

$dhl_user = new DataModels\DhlUser();
$settings_form = new Forms\Settings();
$settings_form = setLink24($settings_form);
$values = $settings_form->PopulateWithPostValues()->GetValues();
$tree_values = Core\TreeDataHandling::FlatData2TreeData($values);

$DHl24LinkForm = $tree_values['DhlUser']['Dhl24Link'];
$DHl24LinkDB = DbWrapper::GetDhl24Link();

if (($DHl24LinkForm == '') || ($DHl24LinkForm == null)) {
    $api->WsdlUrl = $DHl24LinkDB;
} else {
    $api->WsdlUrl = $DHl24LinkForm;
}

$api->ConnectionIdent = ConfigWrapper::Get('ConnectionIdent');
$api->ApiWrapperName = 'Dhl24WebApiWrapper';
$api->ApiClassContainer = 'Dhl24WebApiClasses';
$api->SupportedFeatures = [
    'Send',
    'Delete',
    'BookCourier',
    'CancelCourierBooking',
    'GetLabel',
    'GetPnpReport',
    'GetShipmentScan',
    'GetPostalCodeServices',
    'GetTrackAndTraceInfo',
    'GetNearestServicePoints',
    'TestAuthData',
    'PnpAdditionalOptions',
    'GetTrackLink',
];
