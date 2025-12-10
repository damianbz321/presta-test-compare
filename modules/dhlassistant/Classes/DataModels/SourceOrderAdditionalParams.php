<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Classes\Managers;

class SourceOrderAdditionalParams extends Models\StdDataClass
{
    public $Id = null;
    public $IdSourceObject = null;
    public $CountryCode = "";
    public $SendToParcelShop = false;
    public $SendToParcelLocker = false;
    public $ParcelIdent = "";
    public $Postnummer = "";
    public $ParcelPostalCode = "";

    protected static $S_aDataFields = [
        'Db' => [
            'IdSourceObject',
            'CountryCode',
            'SendToParcelShop',
            'SendToParcelLocker',
            'ParcelIdent',
            'Postnummer',
            'ParcelPostalCode'
        ],
    ];

    protected static $S_GetFilters = [
        'Db' => [
            'SendToParcelShop' => '|ToInt',
            'SendToParcelLocker' => '|ToInt'
        ],
    ];

    protected static $S_SetFilters = [
        'Db' => [
            'SendToParcelShop' => '|ToBool',
            'SendToParcelLocker' => '|ToBool'
        ]
    ];

    /**
     * @return bool
     */
    public function IsSaved()
    {
        return ($this->Id !== null);
    }
}

?>