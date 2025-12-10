<?php
namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Classes\Dhl\Enums;

class ShipmentSpecialServices extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null; //int?: Id lokalne
    public $S_1722 = false; //bool: Doręczenie w godzinach 18-22
    public $S_SOBOTA = false; //bool: Doręczenie w sobotę
    public $S_NAD_SOBOTA = false; //bool: Nadanie w sobotę
    public $S_UBEZP = false; //bool: Ubezpieczenie przesyłki
    public $S_COD = false; //bool: Zwrot pobrania
    public $S_PDI = false; //bool: Informacje przed doręczeniem
    public $S_ROD = false; //bool: Zwrot potwierdzonych dokumentów
    public $S_POD = false; //bool: Potwierdzenie doręczenia
    public $S_SAS = false; //bool: Doręczenie do sąsiada
    public $S_ODB = false; //bool: Odbiór własny
    public $S_UTIL = false; //bool: Utylizacja (nieodebranej) przesyłki [virtual: używa POD]

    public $UBEZP_Value = null; //float?: Deklarowana wartość, wymagana podczas zamawiania usług ubezpieczenie
    public $COD_Value = null; //float?: Deklarowana wartość, zwrot pobrania
    public $ROD_Instruction = ''; //string: Nazwa dokumentu zwrotnego w usłudze ROD, max. len. 32

    public $OriginalCODValue = ''; //string(16): oryginalna wartość pobrania, max. len. 16
    public $OriginalUBEZPValue = ''; //string(16): oryginalna wartość ubezpieczenia, max. len. 16
    public $OriginalCurrencyUnit = ''; //string(3): oryginalna waluta w której wyrażono COD i UBEZP, max. len. 16

    public $UBEZP_CurrencyUnitAlert = false; //bool: czy jest problem z konwersją kwoty ubezpieczenia
    public $COD_CurrencyUnitAlert = false; //bool: czy jest problem z konwersją kwoty pobrania

    protected static $S_aDataFields = array
    (
        'Db' => array('S_1722', 'S_SOBOTA', 'S_NAD_SOBOTA', 'S_UBEZP', 'S_COD', 'S_PDI', 'S_ROD', 'S_POD', 'S_SAS', 'S_ODB', 'S_UTIL', 'UBEZP_Value', 'COD_Value', 'ROD_Instruction', 'OriginalCODValue', 'OriginalUBEZPValue', 'OriginalCurrencyUnit', 'UBEZP_CurrencyUnitAlert', 'COD_CurrencyUnitAlert'),
        'PostGet' => array('S_1722', 'S_SOBOTA', 'S_NAD_SOBOTA', 'S_UBEZP', 'S_COD', 'S_PDI', 'S_ROD', 'S_POD', 'S_SAS', 'S_ODB', 'S_UTIL', 'UBEZP_Value', 'COD_Value', 'ROD_Instruction', 'OriginalCODValue', 'OriginalUBEZPValue', 'OriginalCurrencyUnit', 'UBEZP_CurrencyUnitAlert', 'COD_CurrencyUnitAlert'),
        'PostSet' => array('S_1722', 'S_SOBOTA', 'S_NAD_SOBOTA', 'S_UBEZP', 'S_COD', 'S_PDI', 'S_ROD', 'S_POD', 'S_SAS', 'S_ODB', 'S_UTIL', 'UBEZP_Value', 'COD_Value', 'ROD_Instruction'), //'UBEZP_CurrencyUnitAlert', 'COD_CurrencyUnitAlert' ?
    );
    protected static $S_GetFilters = array
    (
        'Db' => array
        (
            'S_1722' => '|ToInt',
            'S_SOBOTA' => '|ToInt',
            'S_NAD_SOBOTA' => '|ToInt',
            'S_UBEZP' => '|ToInt',
            'S_COD' => '|ToInt',
            'S_PDI' => '|ToInt',
            'S_ROD' => '|ToInt',
            'S_POD' => '|ToInt',
            'S_SAS' => '|ToInt',
            'S_ODB' => '|ToInt',
            'S_UTIL' => '|ToInt',
            'UBEZP_CurrencyUnitAlert' => '|ToInt',
            'COD_CurrencyUnitAlert' => '|ToInt',
        ),
        'PostGet' => array
        (
            'UBEZP_Value' => '|FloatToNStringWith2Dec',
            'COD_Value' => '|FloatToNStringWith2Dec',
        ),
    );
    protected static $S_SetFilters = array
    (
        'Db' => array
        (
            'S_1722' => '|ToBool',
            'S_SOBOTA' => '|ToBool',
            'S_NAD_SOBOTA' => '|ToBool',
            'S_UBEZP' => '|ToBool',
            'S_COD' => '|ToBool',
            'S_PDI' => '|ToBool',
            'S_ROD' => '|ToBool',
            'S_POD' => '|ToBool',
            'S_SAS' => '|ToBool',
            'S_ODB' => '|ToBool',
            'S_UTIL' => '|ToBool',
            'UBEZP_Value' => '|ToNFloat', //?
            'COD_Value' => '|ToNFloat', //?
            'UBEZP_CurrencyUnitAlert' => '|ToBool',
            'COD_CurrencyUnitAlert' => '|ToBool',
        ),
        'PostSet' => array
        (
            'S_1722' => '|ToBool',
            'S_SOBOTA' => '|ToBool',
            'S_NAD_SOBOTA' => '|ToBool',
            'S_UBEZP' => '|ToBool',
            'S_COD' => '|ToBool',
            'S_PDI' => '|ToBool',
            'S_ROD' => '|ToBool',
            'S_POD' => '|ToBool',
            'S_SAS' => '|ToBool',
            'S_ODB' => '|ToBool',
            'S_UTIL' => '|ToBool',
            'UBEZP_Value' => '|ToNFloat',
            'COD_Value' => '|ToNFloat',
        ),
    );

    public function __clone()
    {
        $this->Id = null;
    }

    public function SetServices($aServices)
    {
        if ($aServices)
        {
            foreach (array_keys(Enums\SpecialService::$Descriptions) as $service_code)
            {
                $service_name = 'S_'.$service_code;
                $this->$service_name = in_array($service_code, $aServices);
            }
        }
    }
    public function ValidateCheckLegal(Shipment $oShipment)
    {
        $result = new Models\ValidationResult();
        $dcs = $oShipment->GetTargetCountryService();
        foreach (array_keys(Enums\SpecialService::$Descriptions) as $service_code)
        {
            $service_name = 'S_'.$service_code;
            if ($this->$service_name && !$dcs->AvailableSpecialServices->$service_name)
                $result->AddError($service_name, "Wybrana usługa specjalna nie jest dostępna")->Fail();
        }
        if ($this->S_COD && $dcs->CodRequireUbezp && !$this->S_UBEZP)
            $result->AddError('S_COD', "Wybrana usługa specjalna wymaga ubezpieczenia")->Fail();
        if ($oShipment->SendToParcelShop && $this->S_COD && !$dcs->AllowCodForParcelShop)
            $result->AddError('S_COD', "Wybrana usługa specjalna nie jest dostępna przy wysyłce do Parcelshop")->Fail();
        if ($oShipment->SendToParcelLocker && $this->S_COD && !$dcs->AllowCodForParcelLocker)
            $result->AddError('S_COD', "Wybrana usługa specjalna nie jest dostępna przy wysyłce do Parcelstation")->Fail();
        return $result;
    }
    public function ValidatePreset()
    {
        $result = new Models\ValidationResult();

        return $result;
    }
}

?>