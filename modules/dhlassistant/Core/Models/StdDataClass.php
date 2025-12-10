<?php

namespace DhlAssistant\Core\Models;

use DhlAssistant\Core;
use DhlAssistant\Core\Interfaces;

/**
 *
 */
abstract class StdDataClass implements Interfaces\TreeDataObject
{
    private static $S_oTreeDataObjectInfo = [];

    /**
     * @return TreeDataObjectInfo
     */
    public static function GetTreeDataObjectInfo()
    {
        $classname = get_called_class();
        $temp = explode('\\', $classname);
        $tablename = strtolower(end($temp));

        if (!isset(self::$S_oTreeDataObjectInfo[$classname])) {
            self::$S_oTreeDataObjectInfo[$classname] = new TreeDataObjectInfo($classname, $tablename, 'Id');
        }

        return self::$S_oTreeDataObjectInfo[$classname];
    }

    /**
     * @param $sContextName
     * @param false $bWithKeys
     * @return array|mixed
     */
    public static function GetTreeDataFields($sContextName, $bWithKeys = false)
    {
        $result = [];

        if ($bWithKeys) {
            $result[] = static::GetTreeDataObjectInfo()->sIdFieldName;
        }

        if (isset(static::$S_aDataFields[$sContextName])) {
            $result = array_merge($result, static::$S_aDataFields[$sContextName]);
        }

        if ($bWithKeys && static::GetTreeDataObjectInfo()->aDependedObjectsKeys) {
            $result = array_merge($result, array_values(static::GetTreeDataObjectInfo()->aDependedObjectsKeys));
        }

        return $result;
    }

    /**
     * @param $sContextName
     * @param false $bWithDependedObjects
     * @param false $bWithKeys
     * @return array
     */
    public function GetTreeDataValues($sContextName, $bWithDependedObjects = false, $bWithKeys = false)
    {
        $result = [];
        $obj_info = static::GetTreeDataObjectInfo();

        if ($bWithKeys) {
            $key_name = $obj_info->sIdFieldName;
            $result[$key_name] = $this->GetObjectId();
        }

        if (isset(static::$S_aDataFields[$sContextName]) && static::$S_aDataFields[$sContextName]) {
            foreach (static::$S_aDataFields[$sContextName] as $field_name) {
                $result[$field_name] = $this->GetFieldInContext($sContextName, $field_name);
            }
        }

        if (($bWithDependedObjects || $bWithKeys) && $obj_info->aDependedObjects) {
            /* @var $sub_obj_info TreeDataObjectInfo */
            foreach ($obj_info->aDependedObjects as $field_name => $sub_obj_info) {
                if ($bWithDependedObjects) {
                    $result[$field_name] = $this->$field_name->GetTreeDataValues(
                        $sContextName,
                        $bWithDependedObjects,
                        $bWithKeys
                    );
                }

                if ($bWithKeys && isset ($obj_info->aDependedObjectsKeys[$field_name])) {
                    $result[$obj_info->aDependedObjectsKeys[$field_name]] = $this->$field_name->GetObjectId();
                }
            }
        }

        return $result;
    }

    /**
     * @param $sContextName
     * @param $aValues
     * @param false $bWithDependedObjects
     * @return mixed|void
     */
    public function SetTreeDataValues($sContextName, $aValues, $bWithDependedObjects = false)
    {
        /* @var $obj_info TreeDataObjectInfo */
        $obj_info = static::GetTreeDataObjectInfo();

        if ($aValues && (isset(static::$S_aDataFields[$sContextName]) && static::$S_aDataFields[$sContextName])) {
            foreach (static::$S_aDataFields[$sContextName] as $field_name) {
                if (isset($aValues[$field_name])) {
                    $this->SetFieldInContext($sContextName, $field_name, $aValues[$field_name]);
                }
            }
        }

        if ($bWithDependedObjects && $aValues && $obj_info->aDependedObjects) {
            foreach ($obj_info->aDependedObjects as $field_name => $sub_obj_info) {
                if (isset($aValues[$field_name])) {
                    /* @var $sub_obj Interfaces\TreeDataObject */
                    $sub_obj = $this->$field_name;

                    $sub_obj->SetTreeDataValues($sContextName, $aValues[$field_name], $bWithDependedObjects);
                }
            }
        }
    }

    /**
     * @return mixed|null
     */
    public function GetObjectId()
    {
        $obj_info = static::GetTreeDataObjectInfo();
        $id_field_name = $obj_info->sIdFieldName;

        if (!$id_field_name || !isset($this->$id_field_name)) {
            return null;
        }

        return $this->$id_field_name;
    }

    /**
     * @param $sContextName
     * @param $sFieldName
     * @return false|mixed
     */
    public function GetFieldInContext($sContextName, $sFieldName)
    {
        if (isset (static::$S_GetFilters[$sContextName][$sFieldName])) {
            $func = static::$S_GetFilters[$sContextName][$sFieldName];

            if (is_string($func) && substr($func, 0, 1) === '|') {
                $func = str_replace('|', '\DhlAssistant\Core\Filters::', $func);
            }

            return call_user_func($func, $this->$sFieldName);
        } else {
            return $this->$sFieldName;
        }
    }

    /**
     * @param $sContextName
     * @param $sFieldName
     * @param $mValue
     * @return mixed|void
     */
    public function SetFieldInContext($sContextName, $sFieldName, $mValue)
    {
        if (isset (static::$S_SetFilters[$sContextName][$sFieldName])) {
            $func = static::$S_SetFilters[$sContextName][$sFieldName];

            if (is_string($func) && substr($func, 0, 1) === '|') {
                $func = str_replace('|', '\DhlAssistant\Core\Filters::', $func);
            }

            $this->$sFieldName = call_user_func($func, $mValue);
        } else {
            $this->$sFieldName = $mValue;
        }
    }
}
