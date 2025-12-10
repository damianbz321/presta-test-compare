<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;

class ShipmentPreset extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Name = '';
    public $DropOffType = '';
    public $ServiceType = '';
    public $LabelType = null;
    public $Content = '';
    public $Comment = '';
    public $SpecialServices;
    public $ShipmentStartHour = null;
    public $ShipmentEndHour = null;
    public $Package;

    protected static $S_aDataFields = [
        'Db' => [
            'Name',
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'ShipmentStartHour',
            'ShipmentEndHour'
        ],
        'PostGet' => [
            'Id',
            'Name',
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'ShipmentStartHour',
            'ShipmentEndHour'
        ],
        'PostSet' => [
            'Name',
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'ShipmentStartHour',
            'ShipmentEndHour'
        ],
    ];

    protected static $S_GetFilters = [
        'Db' => [
            'ShipmentStartHour' => '|ToHMString',
            'ShipmentEndHour' => '|ToHMString',
        ],
        'PostGet' => [
            'ShipmentStartHour' => '|ToHMString',
            'ShipmentEndHour' => '|ToHMString',
        ],
    ];

    protected static $S_SetFilters = [
        'Db' => [
            'ShipmentStartHour' => '|ToNDateTime',
            'ShipmentEndHour' => '|ToNDateTime',
        ],
        'PostSet' => [
            'ShipmentStartHour' => '|ToNDateTime',
            'ShipmentEndHour' => '|ToNDateTime',
        ],
    ];

    /**
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public function __construct()
    {
        $this->SpecialServices = new ShipmentSpecialServices();
        $this->Package = new PackageItem();

        $this->ShipmentStartHour = Wrappers\ConfigWrapper::Get('ShipmentHoursStart');
        $this->ShipmentEndHour = Wrappers\ConfigWrapper::Get('ShipmentHoursEnd');
    }

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
        $this->SpecialServices = clone $this->SpecialServices;
        $this->Package = clone $this->Package;
    }

    /**
     * @return Models\TreeDataObjectInfo
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public static function GetTreeDataObjectInfo()
    {
        $info = parent::GetTreeDataObjectInfo();
        $info->AddDependedObject(
            ShipmentSpecialServices::GetTreeDataObjectInfo(),
            'SpecialServices',
            'IdShipmentSpecialServices'
        );
        $info->AddDependedObject(
            PackageItem::GetTreeDataObjectInfo(),
            'Package',
            'IdPackageItem'
        );

        return $info;
    }

    /**
     * @return Models\ValidationResult
     */
    public function Validate()
    {
        $result = new Models\ValidationResult();

        $result->Merge($this->SpecialServices->ValidatePreset(), 'SpecialServices');
        $result->Merge($this->Package->ValidatePreset(), 'Package');

        return $result;
    }
}

?>