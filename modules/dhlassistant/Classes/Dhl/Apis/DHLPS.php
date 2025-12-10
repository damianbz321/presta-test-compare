<?php

use DhlAssistant\Wrappers\ConfigWrapper;
use DhlAssistant\Classes\DataModels\DhlApi;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Core;
use DhlAssistant\Wrappers\DbWrapper;

/**
 * @param $values
 * @param $settings_form
 * @return mixed
 */
function setLinkPs($values, $settings_form)
{
    $DHlPsLinkDB = DbWrapper::GetDhlPsLink();

    $accountType = $values['DhlUser:AccountType'];

    if (($DHlPsLinkDB == '') || ($DHlPsLinkDB == null)) {
        $DhlPsLink = $settings_form->Fields['DhlUser:DhlPsLink']->PossibleValues[$accountType];
    } else {
        $DhlPsLink = $DHlPsLinkDB;
    }

    $values['DhlUser:DhlPsLink'] = $DhlPsLink;

    return $values;
}

$api = new DhlApi();
$api->Code = Enums\DhlWebApi::DHLPS;
$api->Name = 'Dhl Parcelshop';
$api->FullName = Enums\DhlWebApi::$Descriptions[Enums\DhlWebApi::DHLPS];
$api->SupportedServiceCodes = [Enums\ServiceType::LM];

$dhl_user = new DataModels\DhlUser();
$settings_form = new Forms\Settings();

$values = $settings_form->PopulateWithPostValues()->GetValues();

$tree_values = Core\TreeDataHandling::FlatData2TreeData($values);

$DHlPsLinkForm = $tree_values['DhlUser']['DhlPsLink'];
$DHlPsLinkDB = DbWrapper::GetDhlPsLink();

if (($DHlPsLinkForm == '') || ($DHlPsLinkForm == null)) {
    $api->WsdlUrl = $DHlPsLinkDB;
} else {
    $api->WsdlUrl = $DHlPsLinkForm;
}

$api->ConnectionIdent = ConfigWrapper::Get('ConnectionIdent');
$api->ApiWrapperName = 'DhlPsWebApiWrapper';
$api->ApiClassContainer = 'DhlPsWebApiClasses';
$api->SupportedFeatures = [
    'Send',
    'Delete',
    'GetLabel',
    'GetPnpReport',
    'GetNearestServicePoints',
    'TestAuthData',
    'GetTrackLink'
];
