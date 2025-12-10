<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;

class ShipperPreset extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Name = 'Default';
    public $Shipper;
    public $BillingAccountNumber = null;
    public $CostsCenter = '';
    public $DropOffType = '';
    public $LabelType = '';
    public $Weight = '';
    public $Width = '';
    public $Height = '';
    public $Length = '';

    protected static $S_aDataFields = [
        'Db' => ['Name', 'BillingAccountNumber', 'CostsCenter', 'DropOffType', 'LabelType', 'Weight', 'Width', 'Height', 'Length'],
        'PostGet' => ['BillingAccountNumber', 'CostsCenter', 'DropOffType', 'LabelType', 'Weight', 'Width', 'Height', 'Length'],
        'PostSet' => ['BillingAccountNumber', 'CostsCenter', 'DropOffType', 'LabelType', 'Weight', 'Width', 'Height', 'Length']
    ];

    protected static $S_GetFilters = [];
    protected static $S_SetFilters = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->Shipper = new ShipmentSide();
    }

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
        $this->Shipper = clone $this->Shipper;
    }

    /**
     * @return Models\TreeDataObjectInfo
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public static function GetTreeDataObjectInfo()
    {
        $info = parent::GetTreeDataObjectInfo();
        $info->AddDependedObject(
            ShipmentSide::GetTreeDataObjectInfo(),
            'Shipper',
            'IdShipper'
        );

        return $info;
    }

    /**
     * @return Models\ValidationResult
     */
    public function Validate()
    {
        $result = new Models\ValidationResult();
        $result->Merge($this->Shipper->ValidateShipper(), 'Shipper');

        return $result;
    }
}

?>