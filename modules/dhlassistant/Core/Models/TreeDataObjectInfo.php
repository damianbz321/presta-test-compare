<?php

namespace DhlAssistant\Core\Models;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Interfaces;

/**
 *
 */
class TreeDataObjectInfo
{
    public $sClassName = '';
    public $sTableName = null;
    public $sIdFieldName = null;
    public $aDependedObjects = [];
    public $aDependedObjectsKeys = [];

    /**
     * @param $sClassName
     * @param $sTableName
     * @param $sIdFieldName
     */
    public function __construct($sClassName, $sTableName, $sIdFieldName)
    {
        $this->sClassName = $sClassName;
        $this->sTableName = $sTableName;
        $this->sIdFieldName = $sIdFieldName;
    }

    /**
     * @param $oDependObject
     * @param $sDepenedObjectName
     * @param null $sKeyName
     * @return $this
     * @throws Exceptions\LoggedException
     */
    public function AddDependedObject($oDependObject, $sDepenedObjectName, $sKeyName = null)
    {
        if ($oDependObject instanceof TreeDataObjectInfo) {
            $this->aDependedObjects[$sDepenedObjectName] = $oDependObject;
        } elseif ($oDependObject instanceof Interfaces\TreeDataObject) {
            $this->aDependedObjects[$sDepenedObjectName] = $oDependObject->GetTreeDataObjectInfo();
        } else {
            throw new Exceptions\LoggedException('Nieprawidłowy typ parametru!');
        }

        if ($sKeyName !== null) {
            $this->aDependedObjectsKeys[$sDepenedObjectName] = $sKeyName;
        }

        return $this;
    }

    /**
     * @param $sContextName
     * @param false $bWithKeys
     * @return mixed
     */
    public function GetTreeDataFields($sContextName, $bWithKeys = false)
    {
        /* @var $obj Interfaces\TreeDataObject */
        $obj = $this->sClassName;

        return $obj::GetTreeDataFields($sContextName, $bWithKeys);
    }
}

?>