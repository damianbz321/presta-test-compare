<?php
namespace DhlAssistant\Classes\Forms;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\Controllers;

class ShipmentPreset extends Models\Form
{
    public function __construct()
    {
        parent::__construct('ShipmentPreset');

        $this->AddField((new Models\FormField('Id', '', 'Hidden')));
        $this->AddField((new Models\FormField('Name', 'Nazwa', 'Text', null, null, 32))
            ->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
            ->AddValiadtor('|MaxLen', 32, 'Wartość zbyt długa')
            ->AddParam('Required', true)
            ->AddParam('tooltip','Pole może zawierać maksymalnie 32 znaki'));
        $this->AddField((new Models\FormField('ServiceType', 'Usługa' ,'Select', null, Enums\ServiceType::$Descriptions))
            ->AddValiadtor('|IsInArray', array(array_keys(Enums\ServiceType::$Descriptions)), 'Nieprawidłowa wartość')); //string: Typ usługi; Enums\ServiceType
       $this->AddField((new Models\FormField('LabelType', 'Rodzaj etykiety', 'Select', null, Enums\LabelType::$Descriptions))
            ->AddValiadtor('|IsInArray', array(array_keys(Enums\LabelType::$Descriptions)), 'Nieprawidłowa wartość')); //string: typ etykiety przewozowej; Enums\LabelType
        $this->AddField((new Models\FormField('Content', 'Zawartość', 'Text', null, null, 30))
            ->AddValiadtor('|MaxLen', 30, 'Wartość zbyt długa')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 30 znaków')); //string: zawartość przesyłki, max. len. 30
        $this->AddField((new Models\FormField('Comment', 'Komentarz', 'Text', null, null, 100))
            ->AddValiadtor('|MaxLen', 100, 'Wartość zbyt długa')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 100 znaków')); //string: komentarz do przesyłki, pierwsze 50 zn. widać na liście przewozowym, max. len. 100
         //Package
        $Package = new Models\Form('Package'); //PackageItem: Informacje o paczce
        $Package->AddField((new Models\FormField('Package', 'Parametry paczki', 'Section')));
        $Package->AddField((new Models\FormField('Type', 'Typ przesyłki', 'Select', null, Enums\PackageType::$Descriptions))
            ->AddValiadtor('|IsInArray', array(array_keys(Enums\PackageType::$Descriptions)), 'Nieprawidłowa wartość')); //string(*16): typ paczki; Dhl\Enums\PackageType
        $Package->AddField((new Models\FormField('Weight', 'Waga', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość pola musi być liczbą')); //int?: Waga paczki(w kilogramach), nie wymagane dla type = ENVELOPE
        $Package->AddField((new Models\FormField('Width', 'Szerokość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość pola musi być liczbą')); //int?: Szerokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
        $Package->AddField((new Models\FormField('Height', 'Wysokość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość pola musi być liczbą')); //int?: Wysokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
        $Package->AddField((new Models\FormField('Length', 'Długość', 'Text', null, null, 5))
            ->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
            ->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość pola musi być liczbą')); //int?: Długość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
        $Package->AddField((new Models\FormField('Quantity', 'Ilość', 'Text', 1, null, 2))
            ->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
            ->AddValiadtor('|IsInt', null, 'Nieprawidłowa wartość')
            ->AddValiadtor('|IsGreaterThanZero', null, 'Musi być większe od zera')
            ->AddParam('Required', true)); //int: Ilość paczek tego typu
        $Package->AddField((new Models\FormField('NonStandard', 'NST', 'CheckBox', 0))); //bool: Oznaczenie, że paczka jest niestandardowa
        $Package->AddField((new Models\FormField('EuroReturn', 'Zwrot EuroPalety', 'CheckBox', 0))); //bool: Oznaczenie, że palety w przesyłce są do zwrotu
        $this->Merge($Package, 'Package');
        //SpecialServices
        $SpecialServices = new Models\Form('SpecialServices'); //ShipmentSpecialServices: usługi specjalne
        $SpecialServices->AddField((new Models\FormField('SpecialServices', 'Usługi specjalne', 'Section')));
        $SpecialServices->AddField((new Models\FormField('S_1722', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_1722], 'CheckBox', 0)));  //bool: Doręczenie w godzinach 18-22
        $SpecialServices->AddField((new Models\FormField('S_SOBOTA', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_SOBOTA], 'CheckBox', 0)));  //bool: Doręczenie w sobotę
        $SpecialServices->AddField((new Models\FormField('S_NAD_SOBOTA', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_NAD_SOBOTA], 'CheckBox', 0)));  //bool: Nadanie w sobotę
        $SpecialServices->AddField((new Models\FormField('S_UBEZP', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_UBEZP], 'CheckBox', 0)));  //bool: Ubezpieczenie przesyłki
        $SpecialServices->AddField((new Models\FormField('UBEZP_Value', 'Wartość ubezpieczenia', 'Text', null, null, 8))
            ->AddValiadtor('|IsNFloat', null, 'Nieprawidłowa wartość')
            ->AddParam('after_html', '<span class="currency_code">PLN</span>')
            ->AddParam('tooltip','Wartość pola musi być poprawną liczbą rzeczywistą')); //float?: Deklarowana wartość, wymagana podczas zamawiania usług ubezpieczenie
        $SpecialServices->AddField((new Models\FormField('S_COD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_COD], 'CheckBox', 0)));  //bool: Zwrot pobrania
        $SpecialServices->AddField((new Models\FormField('COD_Value', 'Wartość pobrania', 'Text', null, null, 8))
            ->AddValiadtor('|IsNFloat', null, 'Nieprawidłowa wartość')
            ->AddParam('tooltip','Wartość pola musi być poprawną liczbą rzeczywistą')); //float?: Deklarowana wartość, zwrot pobrania
        $SpecialServices->AddField((new Models\FormField('S_PDI', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_PDI], 'CheckBox', 0)));  //bool: Informacje przed doręczeniem
        $SpecialServices->AddField((new Models\FormField('S_ROD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_ROD], 'CheckBox', 0)));  //bool: Zwrot potwierdzonych dokumentów
        $SpecialServices->AddField((new Models\FormField('ROD_Instruction', 'Dokumenty do zwrotu', 'Text', null, null, 32))
            ->AddValiadtor('|MaxLen', 32, 'Wartość zbyt długa')
            ->AddParam('tooltip','Pole może zawierać maksymalnie 32 znaki')); //string: Nazwa dokumentu zwrotnego w usłudze ROD, max. len. 32
        $SpecialServices->AddField((new Models\FormField('S_POD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_POD], 'CheckBox', 0)));  //bool: Potwierdzenie doręczenia
        $SpecialServices->AddField((new Models\FormField('S_ODB', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_ODB], 'CheckBox', 0)));  //bool: Odbiór własny
        $SpecialServices->AddField((new Models\FormField('S_UTIL', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_UTIL], 'CheckBox', 0)));  //bool: Utylizacja (nieodebranej) przesyłki [virtual: używa POD]
        $SpecialServices->AddField((new Models\FormField('S_SAS', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_SAS], 'CheckBox', 0)));  //bool: Doręczenie do sąsiada
        $this->Merge($SpecialServices, 'SpecialServices');

        //Action
        $Action = new Models\Form('Action'); //Akcje formularza
        $Action->AddField((new Models\FormField('panel-footer', '', 'SectionStart'))
            ->AddParam('silent', true));
        $Action->AddField((new Models\FormField('Save', 'Zapisz', 'Button'))
            ->AddParam('class', 'pull-right'));
        $Action->AddField((new Models\FormField('Cancel', 'Anuluj', 'ButtonHref'))
            ->AddParam('href', (new Controllers\ShipmentPresetsList())->GetLink()));
        $Action->AddField((new Models\FormField('End', '', 'EndDiv')));
        $this->Merge($Action, 'Action');
    }

    public function Validate()
    {
        $result = parent::Validate();
        foreach (array('Id') as $field_name)
        {
            if ($this->Fields[$field_name]->Errors){
                foreach ($this->Fields[$field_name]->Errors as $error_msg)
                    $this->AddGeneralError($error_msg);
            }
            if ($this->Fields[$field_name]->Notices)
                foreach ($this->Fields[$field_name]->Notices as $notice_msg)
                    $this->AddGeneralNotice($notice_msg);

        }
        return $result;
    }
}

?>