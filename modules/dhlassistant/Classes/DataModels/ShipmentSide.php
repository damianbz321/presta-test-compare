<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;

class ShipmentSide extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;

    /**
     * @var Address
     */
    public $Address;

    /**
     * @var Contact
     */
    public $Preaviso;

    /**
     * @var Contact
     */
    public $Contact;

    protected static $S_aDataFields = [];
    protected static $S_GetFilters = [];
    protected static $S_SetFilters = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->Address = new Address();
        $this->Preaviso = new Contact();
        $this->Contact = new Contact();
    }

    /**
     * Clone method
     */
    public function __clone()
    {
        $this->Id = null;
        $this->Address = clone $this->Address;
        $this->Preaviso = clone $this->Preaviso;
        $this->Contact = clone $this->Contact;
    }

    /**
     * @return Models\TreeDataObjectInfo
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public static function GetTreeDataObjectInfo()
    {
        $info = parent::GetTreeDataObjectInfo();
        $info->AddDependedObject(Address::GetTreeDataObjectInfo(), 'Address', 'IdAddress');
        $info->AddDependedObject(Contact::GetTreeDataObjectInfo(), 'Preaviso', 'IdPreaviso');
        $info->AddDependedObject(Contact::GetTreeDataObjectInfo(), 'Contact', 'IdContact');

        return $info;
    }

    /**
     * @return Models\ValidationResult
     */
    public function ValidateShipper()
    {
        $result = new Models\ValidationResult();
        $result->Merge($this->Address->ValidateShipper(), 'Address');
        $result->Merge($this->Contact->Validate(), 'Contact');

        return $result;
    }

    /**
     * @param Shipment $oShipment
     * @return Models\ValidationResult
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     * @throws \DhlAssistant\Core\Exceptions\SourceLoggedException
     */
    public function ValidateCheckLegalReceiver(Shipment $oShipment)
    {
        $result = new Models\ValidationResult();
        $result->Merge($this->Address->ValidateCheckLegalReceiver($oShipment), 'Address');
        $result->Merge($this->Contact->Validate(), 'Contact');

        return $result;
    }

    /**
     * @param $sContextName
     * @param $aValues
     * @param false $bWithDependedObjects
     */
    public function SetTreeDataValues($sContextName, $aValues, $bWithDependedObjects = false)
    {
        parent::SetTreeDataValues($sContextName, $aValues, $bWithDependedObjects);

        if ($sContextName === 'PostSet') {
            $preaviso_id = $this->Preaviso->Id;
            $this->Preaviso = clone $this->Contact;
            $this->Preaviso->Id = $preaviso_id;
        }
    }
}

?>