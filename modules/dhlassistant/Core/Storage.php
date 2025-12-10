<?php

namespace DhlAssistant\Core;

class Storage
{
    public static $Shelves = array();

    /**
     * @param $sShelfName
     * @param $mValue
     * @param bool $bToArray
     * @param false $bOnlyIfUnique
     */
    public static function Add($sShelfName, $mValue, $bToArray = true, $bOnlyIfUnique = false)
    {
        if (!isset(self::$Shelves[$sShelfName])) {
            self::$Shelves[$sShelfName] = [];
        } elseif ($bToArray && $bOnlyIfUnique && in_array($mValue, self::$Shelves[$sShelfName])) {
            return;
        }

        if ($bToArray) {
            self::$Shelves[$sShelfName][] = $mValue;
        } else {
            self::$Shelves[$sShelfName] = $mValue;
        }
    }

    /**
     * @param $sShelfName
     * @return array|mixed
     */
    public static function Get($sShelfName)
    {
        if (isset (self::$Shelves[$sShelfName])) {
            return self::$Shelves[$sShelfName];
        }

        return [];
    }

    /**
     * @param $sShelfName
     */
    public static function Clean($sShelfName)
    {
        if (isset(self::$Shelves[$sShelfName])) {
            unset (self::$Shelves[$sShelfName]);
        }
    }
}
