<?php

namespace DhlAssistant\Core\Interfaces;

interface TreeDataObject
{
    /**
     * @return mixed
     */
    public static function GetTreeDataObjectInfo();

    /**
     * @param $sContextName
     * @param false $bWithKeys
     * @return mixed
     */
    public static function GetTreeDataFields($sContextName, $bWithKeys = false);

    /**
     * @param $sContextName
     * @param false $bWithDependedObjects
     * @param false $bWithKeys
     * @return mixed
     */
    public function GetTreeDataValues(
        $sContextName,
        $bWithDependedObjects = false,
        $bWithKeys = false
    );

    /**
     * @param $sContextName
     * @param $aValues
     * @param false $bWithDependedObjects
     * @return mixed
     */
    public function SetTreeDataValues(
        $sContextName,
        $aValues,
        $bWithDependedObjects = false
    );

    /**
     * @return mixed
     */
    public function GetObjectId();

    /**
     * @param $sContextName
     * @param $sFieldName
     * @return mixed
     */
    public function GetFieldInContext($sContextName, $sFieldName);

    /**
     * @param $sContextName
     * @param $sFieldName
     * @param $mValue
     * @return mixed
     */
    public function SetFieldInContext($sContextName, $sFieldName, $mValue);
}

?>