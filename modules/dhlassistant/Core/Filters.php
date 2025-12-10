<?php

namespace DhlAssistant\Core;

use DhlAssistant\Core\Exceptions;
use DhlAssistant;

/**
 *
 */
class Filters
{
    /**
     * @param $mValue
     * @param $mFilters
     * @return false|mixed
     * @throws Exceptions\LoggedException
     */
    public static function Apply($mValue, $mFilters)
    {
        $value = $mValue;

        if (!is_array($mFilters)) {
            $mFilters = array($mFilters);
        }

        foreach ($mFilters as $filter) {
            $exploded = explode('|', $filter);
            $func_name = $exploded[0];
            $func_args = $exploded;
            $func_args[0] = $value;

            if (!method_exists(__CLASS__, $func_name)) {
                throw new Exceptions\LoggedException("Próba użycia niezdefiniowanego filtra '{$func_name}'!");
            }

            $value = call_user_func_array('self::' . $func_name, $func_args);
        }

        return $value;
    }

    /**
     * @param $sValue
     * @param $iMaxLength
     * @return string
     */
    public static function MaxLen($sValue, $iMaxLength)
    {
        return mb_substr($sValue, 0, $iMaxLength, 'utf-8');
    }

    /**
     * @param $sValue
     * @param $iMaxLenght
     * @param $sName
     * @return string
     */
    public static function LoggedMaxLen($sValue, $iMaxLenght, $sName)
    {
        $len = mb_strlen($sValue, 'utf-8');

        if ($len > $iMaxLenght) {
            \DhlAssistant\Core::Log(
                "'{$sName}' zostało skrócone ({$len}/{$iMaxLenght})",
                'Filters::LoggedMaxLen'
            );
        }

        return self::MaxLen($sValue, $iMaxLenght);
    }

    /**
     * @param $mValue
     * @return int
     */
    public static function ToInt($mValue)
    {
        return (int)$mValue;
    }

    /**
     * @param $mValue
     * @return float
     */
    public static function ToFloat($mValue)
    {
        return (float)str_replace(',', '.', $mValue);
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function ToBool($mValue)
    {
        return (bool)$mValue;
    }

    /**
     * @param $mValue
     * @return int|null
     */
    public static function ToNInt($mValue)
    {
        if (($mValue == null) || ($mValue == '')) {
            return null;
        }

        return (int)$mValue;
    }

    /**
     * @param $mValue
     * @return float|null
     */
    public static function ToNFloat($mValue)
    {
        if (($mValue == null) || ($mValue == '')) {
            return null;
        }

        return self::ToFloat($mValue);
    }

    /**
     * @param $mValue
     * @return bool|null
     */
    public static function ToNBool($mValue)
    {
        if (($mValue == null) || ($mValue == '')) {
            return null;
        }

        return (bool)$mValue;
    }

    /**
     * @param $mValue
     * @return mixed|null
     */
    public static function ToNullIfEmpty($mValue)
    {
        if ($mValue == '') {
            return null;
        }

        return $mValue;
    }

    /**
     * @param $mValue
     * @return \DateTime|null
     * @throws \Exception
     */
    public static function ToNDateTime($mValue)
    {
        if (($mValue == null) || ($mValue == '')) {
            return null;
        }

        return new \DateTime($mValue);
    }

    /**
     * @param $oValue
     * @return string|null
     * @throws Exceptions\LoggedException
     */
    public static function ToSqlDateTimeString($oValue)
    {
        if ($oValue == null) {
            return null;
        }

        if (!$oValue instanceof \DateTime) {
            throw new Exceptions\LoggedException('Niewłaściwy typ parametru dla filtra!');
        }

        return $oValue->format('Y-m-d H:i:s');
    }

    /**
     * @param $oValue
     * @return string|null
     * @throws Exceptions\LoggedException
     */
    public static function ToDateString($oValue)
    {
        if ($oValue == null) {
            return null;
        }

        if (!$oValue instanceof \DateTime) {
            throw new Exceptions\LoggedException('Niewłaściwy typ parametru dla filtra!');
        }

        return $oValue->format('Y-m-d');
    }

    /**
     * @param $oValue
     * @return string|null
     * @throws Exceptions\LoggedException
     */
    public static function ToHMString($oValue)
    {
        if ($oValue == null) {
            return null;
        }

        if (!$oValue instanceof \DateTime) {
            throw new Exceptions\LoggedException('Niewłaściwy typ parametru dla filtra!');
        }

        return $oValue->format('H:i');
    }

    /**
     * @param $fValue
     * @return string
     */
    public static function FloatToStringWith2Dec($fValue)
    {
        return number_format($fValue, 2, '.', '');
    }

    /**
     * @param $mValue
     * @return string|null
     */
    public static function FloatToNStringWith2Dec($mValue)
    {
        if (($mValue == null) || ($mValue == '')) {
            return null;
        }

        return self::FloatToStringWith2Dec($mValue);
    }
}

?>