<?php

namespace DhlAssistant\Classes\Forms;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Controllers;

Core\Storage::Add(
    'Js',
    Wrappers\ConfigWrapper::Get('BaseUrl') . 'Media/Js/Settings.js',
    true,
    true
);

class Settings extends Models\Form
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Settings');

        $this->GetUserApi();
        $this->GetUserShipper();
        $this->SettingAndAction();
    }

    /**
     * @return Enums\SettingsUserData
     */
    public function GetSettingsUserData()
    {
        return new Enums\SettingsUserData();
    }

    /**
     *
     */
    public function GetUserApi()
    {

        $typeAccount = new Enums\AccountType();

        if (isset($_POST['DhlUser:AccountType'])) {
            $currentTypeAccount = $_POST['DhlUser:AccountType'];
        } elseif (Wrappers\ConfigWrapper::Get('IsModuleConfigured')) {
            $dhlUser = Wrappers\ConfigWrapper::Get('DefaultDhlUser');
            $currentDhl24Link = $dhlUser->Dhl24Link;
            $linkOptions = $typeAccount->getDHL24Link();
            if (array_key_exists($currentDhl24Link, $linkOptions)) {
                $currentTypeAccount =  $linkOptions[$currentDhl24Link];
            }
        } else {
            $currentTypeAccount = \DhlAssistant\Classes\Dhl\Enums\AccountType::PRODUCTION;
        }

        $DhlUser = new Models\Form('DhlUser');
        $DhlUser->AddField(
            new Models\FormField(
                'DhlUser',
                $this->GetSettingsUserData()->ValueFieldApi('DhlUserValue'),
                'Section'
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'DhlLinks',
                $this->GetSettingsUserData()->ValueFieldApi('DhlLinksValue'),
                'SubSection'
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'AccountType',
                $this->GetSettingsUserData()->ValueFieldApi('AccountTypeValue'),
                'Select',
                $currentTypeAccount,
                $typeAccount->SelectAccount()
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'Dhl24Link',
                $this->GetSettingsUserData()->ValueFieldApi('Dhl24LinkValue'),
                'Select',
                null,
                $typeAccount->getDHL24Link(),
                120
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'DhlPsLink',
                $this->GetSettingsUserData()->ValueFieldApi('Dhl24Value'),
                'Select',
                null,
                $typeAccount->getDHLPSLink(),
                120
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'Dhl24',
                $this->GetSettingsUserData()->ValueFieldApi('Dhl24Value'),
                'SubSection'
            )
        );

        $DhlUser->AddField(
            (new Models\FormField(
                'Dhl24Login',
                $this->GetSettingsUserData()->ValueFieldApi('Dhl24LoginValue'),
                'Text',
                null,
                null,
                32)
            )->AddValiadtor(
                '|MaxLen',
                32,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain32')
            )
        );

        $DhlUser->AddField(
            (new Models\FormField(
                'Dhl24Password',
                $this->GetSettingsUserData()->ValueFieldApi('Dhl24PasswordValue'),
                'Password',
                null,
                null,
                32)
            )->AddValiadtor(
                '|MaxLen',
                32,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain32')
            )
        );

        $DhlUser->AddField(
            new Models\FormField(
                'DhlPs',
                $this->GetSettingsUserData()->ValueFieldApi('DhlPsValue'),
                'SubSection'
            )
        );

        $DhlUser->AddField(
            (new Models\FormField('DhlPsLogin',
                $this->GetSettingsUserData()->ValueFieldApi('DhlPsLoginValue'),
                'Text',
                null,
                null,
                32)
            )->AddValiadtor(
                '|MaxLen',
                32,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain32')
            )
        );

        $DhlUser->AddField(
            (new Models\FormField('DhlPsPassword',
                $this->GetSettingsUserData()->ValueFieldApi('DhlPsPasswordValue'),
                'Password',
                null,
                null,
                32)
            )->AddValiadtor(
                '|MaxLen',
                32,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain32')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain32')
            )
        );

        $this->Merge($DhlUser, 'DhlUser');
    }

    /**
     * Get user shipper.
     */
    public function GetUserShipper()
    {
        $ShipperPreset = new Models\Form('ShipperPreset');

        $ShipperPreset->AddField(
            new Models\FormField(
                'ShipperPreset',
                $this->GetSettingsUserData()->ValueFieldShipper('SP_Value'),
                'Section'
            )
        );

        $Shipper = new Models\Form('Shipper');

        $ShipperAddress = new Models\Form('Address');
        $ShipperAddress->AddField(
            new Models\FormField('ShipperPresetAddress',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_AddressValue'),
                'SubSection'
            )
        );

        $ShipperAddress->AddField(
            (new Models\FormField('Name',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_NameOrCompanyAddressValue'),
                'Text',
                null,
                null,
                60)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidIsNotEmptyValue')
            )->AddValiadtor(
                '|MaxLen',
                60,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'Required',
                true
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue'))
        );

        $ShipperAddress->AddField(
            (new Models\FormField('Street',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_StreetValue'),
                'Text',
                null,
                null,
                22)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidIsNotEmptyValue')
            )->AddValiadtor(
                '|MaxLen',
                22,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'Required',
                true
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )
        );

        $ShipperAddress->AddField(
            (new Models\FormField(
                'HouseNumber',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_HouseNumberValue'),
                'Text',
                null,
                null,
                7)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddValiadtor(
                '|MaxLen',
                7,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam('Required',
                true
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )
        );

        $ShipperAddress->AddField(
            (new Models\FormField(
                'ApartmentNumber',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_ApartmentNumberValue'),
                'Text',
                null,
                null,
                7)
            )->AddValiadtor(
                '|MaxLen',
                7,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )
        );

        $ShipperAddress->AddField(
            (new Models\FormField('PostalCode',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_PostCodeContactValue'),
                'Text',
                null,
                null,
                6)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidIsNotEmptyValue')
            )->AddValiadtor(
                '|IsPlZipCode',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidWrongvalue')
            )->AddParam(
                'Required',
                true
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidWrongPostcodevalue')
            )
        );

        $ShipperAddress->AddField(
            (new Models\FormField(
                'City',
                $this->GetSettingsUserData()->ValueFieldShipper('SA_CityContactValue'),
                'Text',
                null,
                null,
                17)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidIsNotEmptyValue')
            )->AddValiadtor(
                '|MaxLen',
                17,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'Required',
                true
            )
        );

        $Shipper->Merge($ShipperAddress, 'Address');

        $ShipperContact = new Models\Form('Contact');

        $ShipperContact->AddField(
            new Models\FormField('ShipperPresetContact',
                $this->GetSettingsUserData()->ValueFieldShipper('SC_ShipperPresetContactValue'),
                'SubSection'
            )
        );

        $ShipperContact->AddField(
            (new Models\FormField(
                'Name',
                $this->GetSettingsUserData()->ValueFieldShipper('SC_ContactNameOrCompanyValue'),
                'Text',
                null,
                null,
                50)
            )->AddValiadtor(
                '|MaxLen',
                50,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )
        );

        $ShipperContact->AddField(
            (new Models\FormField(
                'Phone',
                $this->GetSettingsUserData()->ValueFieldShipper('SC_ContactPhoneValue'),
                'Text',
                null,
                null,
                9)
            )->AddValiadtor(
                '|MaxLen',
                9,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddValiadtor(
                '|IsNInt',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidMustBeNumber')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain9')
            )
        );

        $ShipperContact->AddField(
            (new Models\FormField(
                'Email',
                $this->GetSettingsUserData()->ValueFieldShipper('SC_ContactEmail'),
                'Text',
                null,
                null,
                100)
            )->AddValiadtor(
                '|MaxLen',
                100,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain100')
            )

        );

        $Shipper->Merge($ShipperContact, 'Contact');
        $ShipperPreset->Merge($Shipper, 'Shipper');





        $ShipperPreset->AddField((new Models\FormField('Others', 'Inne', 'Section')));
        $ShipperPreset->AddField(
            (new Models\FormField(
                'BillingAccountNumber',
                $this->GetSettingsUserData()->ValueFieldShipper('SP_BillingAccountNumberValue'),
                'Text',
                null,
                null,
                7)
            )->AddValiadtor(
                '|IsNotNullOrEmpty',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidIsNotEmptyValue')
            )->AddValiadtor(
                '|MaxLen',
                7,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddValiadtor(
                '|IsNInt',
                null,
                $this->GetSettingsUserData()->ValueFieldApi('ValidWrongvalue')
            )->AddParam(
                'Required',
                true
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain7')
            )
        );
        $ShipperPreset->AddField(
            (new Models\FormField(
                'CostsCenter',
                $this->GetSettingsUserData()->ValueFieldShipper('SP_CostsCenterValue'),
                'Text',
                null,
                null,
                20)
            )->AddValiadtor(
                '|MaxLen',
                20,
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValue')
            )->AddParam(
                'tooltip',
                $this->GetSettingsUserData()->ValueFieldApi('ValidTooLongValueContain20')
            )
        );
        $ShipperPreset->AddField(
            (new Models\FormField(
                'LabelType',
                'Rodzaj etykiety',
                'Select',
                null,
                Enums\LabelType::$Descriptions
            ))
                ->AddValiadtor(
                    '|IsInArray',
                    array(array_keys(Enums\LabelType::$Descriptions)),
                    'Nieprawidłowa wartość'
                )
        ); //string: typ etykiety przewozowej; Enums\LabelType
        $ShipperPreset->AddField(
            (new Models\FormField(
                'DropOffType',
                'Odbiór',
                'Select',
                null,
                Enums\DropOffType::$Descriptions)
            )
                ->AddValiadtor(
                    '|IsInArray',
                    array(array_keys(Enums\DropOffType::$Descriptions)),
                    'Nieprawidłowa wartość')
        ); //string: Typ odbioru; Enums\DropOffType

        $ShipperPreset->AddField((new Models\FormField('Package', 'Parametry paczki', 'Section')));
        $ShipperPreset->AddField((new Models\FormField('Weight', 'Waga', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('Required', true)
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Waga paczki(w kilogramach), nie wymagane dla type = ENVELOPE
        $ShipperPreset->AddField((new Models\FormField('Width', 'Szerokość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('Required', true)
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Szerokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
        $ShipperPreset->AddField((new Models\FormField('Height', 'Wysokość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('Required', true)
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Wysokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
        $ShipperPreset->AddField((new Models\FormField('Length', 'Długość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('Required', true)
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Długość paczki (w centymetrach), nie wymagane dla type = ENVELOPE


        $this->Merge($ShipperPreset, 'ShipperPreset');
    }

    /**
     * @throws Core\Exceptions\LoggedException
     */
    public function SettingAndAction()
    {
        $Settings = new Models\Form('Settings');

        $Settings->AddField((new Models\FormField('CodSettings', $this->GetSettingsUserData()->ValueFieldShipper('COD_Module'), 'Section')));
        $payment_modules = Wrappers\SourceWrapper::GetPaymentModules();
        if ($payment_modules) {
            foreach ($payment_modules as $module_name => $module_display_name) {
                $Settings->AddField((new Models\FormField('Setting_Cod_' . $module_name, $module_display_name, 'CheckBox', 0)));  //bool: poszczegolne moduly platnosci
            }
        }

        $this->Merge($Settings, 'Settings');

        $Action = new Models\Form('Action');
        $Action->AddField(
            (new Models\FormField(
                'panel-footer',
                '',
                'SectionStart')
            )->AddParam(
                'silent',
                true
            )
        );

        $Action->AddField(
            (new Models\FormField(
                'Save',
                $this->GetSettingsUserData()->ButtonsAndValidations('BtnSaveValue'),
                'Button')
            )->AddParam(
                'class',
                'pull-righ'
            )
        );

        $Action->AddField(
            (new Models\FormField(
                'Cancel',
                $this->GetSettingsUserData()->ButtonsAndValidations('BtnCancelValue'),
                'ButtonHref')
            )->AddParam(
                'href', (new Controllers\Index())->GetLink()
            )
        );

        $Action->AddField(
            new Models\FormField(
                'End',
                '',
                'EndDiv'
            )
        );

        $this->Merge($Action, 'Action');
    }
}

?>