<?php
namespace DhlAssistant\Classes\Forms;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\Controllers;

class Shipment extends Models\Form
{
	public function __construct()
	{
		parent::__construct('Shipment');
		$this->AddField((new Models\FormField('ViewMode', 'Informacje o przesyłce', 'SectionStart'))
				->AddParam('hidden', true));
		$this->AddField((new Models\FormField('DhlShipmentId', 'Id przesyłki', 'Text')));
		$this->AddField((new Models\FormField('DhlOrderId', 'Id zamówienia', 'Text')));
		$this->AddField((new Models\FormField('DhlShipmentCreationDateTime', 'Data zlecenia', 'Text')));
		$this->AddField((new Models\FormField('DhlTrackingLink', 'Link śledzenia', 'LinkHref'))
				->AddParam('target_blank', true));
		$this->AddField((new Models\FormField('End', '', 'EndDiv')));
		$ShipmentPreset = new Models\Form('ShipmentPreset'); //Wybór presetu przesyłki
		$ShipmentPreset->AddField((new Models\FormField('Start', '', 'SectionStart'))
				->AddParam('silent', true));
		$ShipmentPreset->AddField((new Models\FormField('ShipmentPresetId', 'Predefinicja przesyłki', 'Select')));
		$ShipmentPreset->AddField((new Models\FormField('Apply', 'Użyj', 'Submit')));
		$ShipmentPreset->AddField((new Models\FormField('End', '', 'EndDiv')));
		$this->Merge($ShipmentPreset, 'ShipmentPreset');
		
		$this->AddField((new Models\FormField('Shipment', 'Dane przesyłki', 'Section')));
		$this->AddField((new Models\FormField('Id', '', 'Hidden')));
		$this->AddField((new Models\FormField('ServiceType', 'Usługa' ,'Select', null, Enums\ServiceType::$Descriptions))
				->AddValiadtor('|IsInArray', array(array_keys(Enums\ServiceType::$Descriptions)), 'Nieprawidłowa wartość')); //string: Typ usługi; Enums\ServiceType
		$this->AddField((new Models\FormField('DropOffType', 'Odbiór' ,'Select', null, Enums\DropOffType::$Descriptions))
				->AddValiadtor('|IsInArray', array(array_keys(Enums\DropOffType::$Descriptions)), 'Nieprawidłowa wartość')); //string: Typ odbioru; Enums\DropOffType
		$this->AddField((new Models\FormField('LabelType', 'Rodzaj etykiety', 'Select', null, Enums\LabelType::$Descriptions))
				->AddValiadtor('|IsInArray', array(array_keys(Enums\LabelType::$Descriptions)), 'Nieprawidłowa wartość')); //string: typ etykiety przewozowej; Enums\LabelType
		$this->AddField((new Models\FormField('Content', 'Zawartość', 'Text', null, null, null))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddParam('Required', true));
		$this->AddField(new Models\FormField('Comment', 'Komentarz', 'Text', null, null, null));
		$this->AddField((new Models\FormField('Reference', 'Nr. ref.', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('tooltip','Pole może zawierać maksymalnie 255 znaków')); //string: numer referencyjny przesyłki, max. len. 20
        $this->AddField((new Models\FormField('PaymentType', 'Rodzaj rozliczenia', 'Select', null, Enums\PaymentType::$Descriptions))
        ); //string: sposób płatności; Enums\PaymentType; max. len. 16
        $this->AddField((new Models\FormField('ShippingPaymentType', 'Płatnik za usługę', 'Select', null, Enums\ShippingPaymentType::$Descriptions))
				->AddValiadtor('|IsInArray', array(array_keys(Enums\ShippingPaymentType::$Descriptions)), 'Nieprawidłowa wartość')); //string: Strona płacąca za przesyłkę ;Enums\ShippingPaymentType; max. len. 16


        $this->AddField(
            (new Models\FormField('SenderName',
                'Imię i Naziwsko',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField('SenderCompany',
                'Firma',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField('SenderStreet',
                'Ulica',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField(
                'SenderHouseNumber',
                'Numer domu',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField(
                'SenderApartmentNumber',
                'Numer lokalu',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField('SenderPostalCode',
                'Kod pocztowy',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField(
                'SenderCity',
                'Miasto',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField(
                'SenderPhone',
                'Telefon',
                'Text',
                null,
                null,
                255)
            )
        );

        $this->AddField(
            (new Models\FormField(
                'SenderEmail',
                'Email',
                'Text',
                null,
                null,
                255)
            )
        );


		$this->AddField((new Models\FormField('CostsCenter', 'Centrum kosztów', 'Text', null, null, null))
				);
		$this->AddField((new Models\FormField('ShipmentDate', 'Data wysyłki', 'DatePicker'))
				->AddValiadtor('|IsDateString', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Wartość pola musi być poprawną datą')); //DateTime?: Data nadania w formacie RRRR-MM-DD

        $this->AddField((new Models\FormField('NearestAvailableShipmentDate', '', 'Hidden')));
        $this->AddField((new Models\FormField('CurrentShipmentDate', '', 'Hidden')));

		$this->AddField((new Models\FormField('ShipmentStartHour', 'Odbiór od', 'TimePicker'))
				->AddValiadtor('|IsHMString', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Wartość pola musi być poprawną godziną')); //DateTime?: Początek zakresu godzinowego, w którym kurier ma odebrać przesyłkę; w formacie GG:MM, max. len. 5
		$this->AddField((new Models\FormField('ShipmentEndHour', 'Odbiór do', 'TimePicker'))
				->AddValiadtor('|IsHMString', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Wartość pola musi być poprawną godziną')); //DateTime?: Koniec zakresu godzinowego, w którym kurier ma odebrać przesyłkę; w formacie GG:MM, max. len. 5
		$this->AddField((new Models\FormField('SendToParcelShop', 'Wysyłka do Parcelshop', 'CheckBox', 0))); //bool: Wysyłka do ParcelShopu
		$this->AddField((new Models\FormField('SendToParcelLocker', 'Wysyłka do Parcelstation', 'CheckBox', 0))); //bool: Wysyłka do ParcelLockera (Parcelstation)
		$this->AddField((new Models\FormField('ParcelIdent', 'Nr. Parcelshop', 'Text', null, null, null))
				->AddParam('Required', true)
				); //string: Numer SAP ParcelShop'u, ParcelLocker'a lub nr. plac. pocztowej (DE), max. len. 7
		$this->AddField((new Models\FormField('Postnummer', 'Postnummer', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 255 znaków')); //string: numer klienta; wymagane przy wysyłce do DE (w zal. od ustawień DhlCountryService), max. len. 10
		$this->AddField((new Models\FormField('ParcelPostalCode', 'Kod pocztowy Parcelshop', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Prawidłowy kod pocztowy dla danego kraju docelowego')); //string: kod pocztowy; wymagane przy wysyłkach do Parceli(Shop/Locker) za granicą (w zal. od ustawień DhlCountryService), max. len. 10 
		//Receiver
		$Receiver = new Models\Form('Receiver'); //ShipmentSide: dane odbiorcy
		$ReceiverAddress = new Models\Form('ReceiverAddress');
		$ReceiverAddress->AddField((new Models\FormField('ReceiverAddress', 'Adres odbiorcy', 'Section')));
		$ReceiverAddress->AddField((new Models\FormField('Name', 'Nazwa firmy', 'Text', null, null, null))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddParam('Required', true)
				);
		$ReceiverAddress->AddField((new Models\FormField('Street', 'Ulica', 'Text', null, null, null))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddParam('Required', true)
				);
		$ReceiverAddress->AddField((new Models\FormField('HouseNumber', 'Numer domu', 'Text', null, null, null))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddParam('Required', true)
				);
		$ReceiverAddress->AddField((new Models\FormField('ApartmentNumber', 'Numer mieszkania', 'Text', null, null, null))
				);
		$ReceiverAddress->AddField((new Models\FormField('OriginalAddressString', 'Adres źródłowy', 'Text'))
				->AddParam('tooltip','Adres dostawy zamówienia na podstawie którego został ustalony adres odbiorcy przesyłki DHL')
				->AddParam('readonly', true)); //string(??):adres przed parsowaniem
		$ReceiverAddress->AddField((new Models\FormField('ParseAlert', '', 'Hidden'))); //bool: czy był problem z parsowaniem adresu
		$ReceiverAddress->AddField((new Models\FormField('PostalCode', 'Kod pocztowy', 'Text', null, null, 255))
				->AddParam('tooltip','Prawidłowy kod pocztowy dla danego kraju docelowego'));
        $ReceiverAddress->AddField((new Models\FormField('City', 'Miasto', 'Text', null, null, null))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddParam('Required', true)
				);
		$ReceiverAddress->AddField((new Models\FormField('Country', 'Kraj', 'Select', null, Core\Tools::ArraySortWithExampleByKey (Enums\Country::$Descriptions, Wrappers\ConfigWrapper::Get('AvailableCountryCodes'), false)))
				->AddValiadtor('|IsInArray', array(Wrappers\ConfigWrapper::Get('AvailableCountryCodes')), 'Nieprawidłowa wartość')); //string: kraj, max. len. 2?
		$Receiver->Merge($ReceiverAddress, 'Address');
		$ReceiverContact = new Models\Form('ReceiverContact');
		$ReceiverContact->AddField((new Models\FormField('ReceiverContact', 'Dane kontaktowe odbiorcy', 'Section')));
		$ReceiverContact->AddField((new Models\FormField('Name', 'Imię i nazwisko', 'Text', null, null, null))
				);
		$ReceiverContact->AddField((new Models\FormField('Phone', 'Telefon', 'Text', null, null, null))
				->AddValiadtor('|IsNInt', null, 'Wartość musi być numerem')
				->AddParam('tooltip','Wartość musi być liczbą'));
		$ReceiverContact->AddField((new Models\FormField('Email', 'Email', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('tooltip','Pole może zawierać maksymalnie 100 znaków'));
		$Receiver->Merge($ReceiverContact, 'Contact');
		$this->Merge($Receiver, 'Receiver');
		//Package
		$Package = new Models\Form('Package'); //PackageItem: Informacje o paczce
		$Package->AddField((new Models\FormField('Package', 'Parametry paczki', 'Section')));
		$Package->AddField((new Models\FormField('Type', 'Typ przesyłki', 'Select', null, Enums\PackageType::$Descriptions))
				->AddValiadtor('|IsInArray', array(array_keys(Enums\PackageType::$Descriptions)), 'Nieprawidłowa wartość')); //string(*16): typ paczki; Dhl\Enums\PackageType
		$Package->AddField((new Models\FormField('Weight', 'Waga', 'Text', null, null, 10))
				->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
				->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Waga paczki(w kilogramach), nie wymagane dla type = ENVELOPE
		$Package->AddField((new Models\FormField('Width', 'Szerokość', 'Text', null, null, 10))
				->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
				->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Szerokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
		$Package->AddField((new Models\FormField('Height', 'Wysokość', 'Text', null, null, 10))
				->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
				->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Wysokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
		$Package->AddField((new Models\FormField('Length', 'Długość', 'Text', null, null, 10))
				->AddValiadtor('|MaxLen', 5, 'Wartość zbyt długa')
				->AddValiadtor('|IsNInt', null, 'Nieprawidłowa wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 5 znaków i jego wartość musi być liczbą')); //int?: Długość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
		$Package->AddField((new Models\FormField('Quantity', 'Ilość', 'Text', 1, null, 5))
				->AddValiadtor('|IsNotNullOrEmpty', null, 'Nie może być puste')
				->AddValiadtor('|IsInt', null, 'Nieprawidłowa wartość')
				->AddValiadtor('|IsGreaterThanZero', null, 'Musi być większe od zera')
				->AddParam('Required', true)); //int: Ilość paczek tego typu
		$Package->AddField((new Models\FormField('NonStandard', 'NST', 'CheckBox', 0))); //bool: Oznaczenie, że paczka jest niestandardowa (tylko dla PACKAGE i bez Parcela dla zagranicy)
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
				->AddValiadtor('|IsNGreaterThanZero', null, 'Musi być większe od zera')
				->AddParam('after_html', '<span class="currency_code">PLN</span>')
				->AddParam('Required', true)
				->AddParam('tooltip','Wartość pola musi być poprawną liczbą rzeczywistą')); //float?: Deklarowana wartość, wymagana podczas zamawiania usług ubezpieczenie
		$SpecialServices->AddField((new Models\FormField('OriginalUBEZPValue', 'Oryginalna wartość ubezpieczenia', 'Info'))
				->AddParam('after_html', '<span class="currency_code original"></span>')); //string(16): oryginalna wartość ubezpieczenia, max. len. 16
		$SpecialServices->AddField((new Models\FormField('UBEZP_CurrencyUnitAlert', '', 'Hidden'))); //bool: czy jest problem z konwersją kwoty ubezpieczenia
		$SpecialServices->AddField((new Models\FormField('S_COD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_COD], 'CheckBox', 0)));  //bool: Zwrot pobrania
		$SpecialServices->AddField((new Models\FormField('COD_Value', 'Wartość pobrania', 'Text', null, null, 8))
				->AddValiadtor('|IsNFloat', null, 'Nieprawidłowa wartość')
				->AddValiadtor('|IsNGreaterThanZero', null, 'Musi być większe od zera')
				->AddParam('after_html', '<span class="currency_code country"></span>')
				->AddParam('Required', true)
				->AddParam('tooltip','Wartość pola musi być poprawną liczbą rzeczywistą')); //float?: Deklarowana wartość, zwrot pobrania
		$SpecialServices->AddField((new Models\FormField('OriginalCODValue', 'Oryginalna wartość pobrania', 'Info'))
				->AddParam('after_html', '<span class="currency_code original"></span>')); //string(16): oryginalna wartość pobrania, max. len. 16
		$SpecialServices->AddField((new Models\FormField('COD_CurrencyUnitAlert', '', 'Hidden'))); //bool: czy jest problem z konwersją kwoty pobrania
		$SpecialServices->AddField((new Models\FormField('S_PDI', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_PDI], 'CheckBox', 0)));  //bool: Informacje przed doręczeniem
		$SpecialServices->AddField((new Models\FormField('S_ROD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_ROD], 'CheckBox', 0)));  //bool: Zwrot potwierdzonych dokumentów
		$SpecialServices->AddField((new Models\FormField('ROD_Instruction', 'Dokumenty do zwrotu', 'Text', null, null, 32))
				->AddValiadtor('|MaxLen', 32, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 32 znaki')); //string: Nazwa dokumentu zwrotnego w usłudze ROD, max. len. 32
		$SpecialServices->AddField((new Models\FormField('S_POD', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_POD], 'CheckBox', 0)));  //bool: Potwierdzenie doręczenia
		$SpecialServices->AddField((new Models\FormField('S_ODB', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_ODB], 'CheckBox', 0)));  //bool: Odbiór własny
		$SpecialServices->AddField((new Models\FormField('S_UTIL', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_UTIL], 'CheckBox', 0)));  //bool: Utylizacja (nieodebranej) przesyłki [virtual: używa POD]
		$SpecialServices->AddField((new Models\FormField('S_SAS', Enums\SpecialService::$Descriptions[Enums\SpecialService::S_SAS], 'CheckBox', 0)));  //bool: Doręczenie do sąsiada
		$SpecialServices->AddField((new Models\FormField('OriginalCurrencyUnit', 'Oryginalna waluta', 'Hidden'))); //string(3): oryginalna waluta w której wyrażono COD i UBEZP, max. len. 16
		$this->Merge($SpecialServices, 'SpecialServices');
		//Neighbour
		$Neighbour = new Models\Form('Neighbour'); //Address: dane doręczenia do sąsiada
		$Neighbour->AddField((new Models\FormField('Start', 'Dane sąsiada', 'SectionStart')));
		$Neighbour->AddField((new Models\FormField('Name', 'Imię, nazwisko lub nazwa firmy', 'Text', null, null, 60))
				->AddValiadtor('|MaxLen', 60, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 60 znaków')); //string: Nazwa (imię i nazwisko bądź nazwa firmy), max. len. 60
		$Neighbour->AddField((
		    new Models\FormField(
		        'Street',
                'Ulica',
                'Text',
                null,
                null,
                22)
        )
				->AddParam('Required', true)
			);
		$Neighbour->AddField((new Models\FormField('HouseNumber', 'Nr. domu', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 255 znaków')); //string:numer mieszkania, max. len. 255
		$Neighbour->AddField((new Models\FormField('ApartmentNumber', 'Nr. mieszkania', 'Text', null, null, 7))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('tooltip','Pole może zawierać maksymalnie 7 znaków')); //string:numer lokalu, max. len. 255
		$Neighbour->AddField((new Models\FormField('PostalCode', 'Kod pocztowy', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddValiadtor('|IsNPlZipCode', null, 'Niepoprawna wartość')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 6 znaków'));
		$Neighbour->AddField((new Models\FormField('City', 'Miasto', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('Required', true)
				->AddParam('tooltip','Pole może zawierać maksymalnie 17 znaków')); //string:miasto, max. len. 17
		$Neighbour->AddField((new Models\FormField('Phone', 'Telefon', 'Text', null, null, 9))
				->AddValiadtor('|IsNInt', null, 'Wartość musi być numerem')
				);
		$Neighbour->AddField((new Models\FormField('Email', 'Email', 'Text', null, null, 255))
				->AddValiadtor('|MaxLen', 255, 'Wartość zbyt długa')
				->AddParam('tooltip','Pole może zawierać maksymalnie 100 znaków')); //string:adres email, max. len. 100
		$Neighbour->AddField((new Models\FormField('End', '', 'EndDiv')));
		
		$this->Merge($Neighbour, 'Neighbour');
		//Action
		$Action = new Models\Form('Action'); //Akcje formularza
		$Action->AddField((new Models\FormField('panel-footer', '', 'SectionStart'))
				->AddParam('silent', true));
		$Action->AddField((new Models\FormField('Cancel', 'Anuluj', 'ButtonHref'))
				->AddParam('href', (new Controllers\ShipmentsList())->GetLink()));
		$Action->AddField((new Models\FormField('DeleteShipment', 'Usuń', 'ButtonHref'))
				->AddParam('class', 'pull-right')
				->AddParam('icon_class', 'icon-trash')
				->AddParam('onclick', 'return confirm(\'Czy na pewno usunąć zlecenie przesyłki?\nW przypadku zleconych przesyłek nie powoduje to ich wycofania.\')'));
		$Action->AddField((new Models\FormField('Send', 'Wyślij', 'Button'))
				->AddParam('class', 'pull-right')
				->AddParam('icon_class', 'icon-arrow-up'));
		$Action->AddField((new Models\FormField('Save', 'Zapisz', 'Button'))
				->AddParam('class', 'pull-right'));
		$Action->AddField((new Models\FormField('CancelShipment', 'Wycofaj', 'ButtonHref'))
				->AddParam('hidden', true)
				->AddParam('class', 'pull-right')
				->AddParam('icon_class', 'icon-arrow-left')
				->AddParam('onclick', 'return confirm(\'Czy na pewno wycofać zlecenie?\')'));
		$Action->AddField((new Models\FormField('GetLabel_LBLP', 'Etykieta LBLP', 'ButtonHref'))
				->AddParam('hidden', true)
				->AddParam('class', 'pull-right')
				->AddParam('target_blank', true)
				->AddParam('icon_class', 'icon-file-text'));
		$Action->AddField((new Models\FormField('GetLabel_ZBLP', 'Etykieta ZBLP', 'ButtonHref'))
				->AddParam('hidden', true)
				->AddParam('class', 'pull-right')
				->AddParam('target_blank', true)
				->AddParam('icon_class', 'icon-file-text'));
		$Action->AddField((new Models\FormField('GetLabel_BLP', 'Etykieta BLP', 'ButtonHref'))
				->AddParam('hidden', true)
				->AddParam('class', 'pull-right')
				->AddParam('target_blank', true)
				->AddParam('icon_class', 'icon-file-text'));
		$Action->AddField((new Models\FormField('GetLabel_LP', 'Etykieta LP', 'ButtonHref'))
				->AddParam('hidden', true)
				->AddParam('class', 'pull-right')
				->AddParam('target_blank', true)
				->AddParam('icon_class', 'icon-file-text'));
		$Action->AddField((new Models\FormField('End', '', 'EndDiv')));
		$this->Merge($Action, 'Action');
	}
	
	public function Validate()
	{
		$result = parent::Validate();
		//hack
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