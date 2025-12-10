<?php

namespace DhlAssistant\Core;

/**
 *
 */
class Validators
{
    /**
     * @param $mValue
     * @param $mValidators
     */
    public static function Apply($mValue, $mValidators)
    {
//        $value = $mValue;
//        if (!is_array($mFilters)) {
//            $mFilters = array($mFilters);
//        }
//        foreach ($mFilters as $filter) {
//            $exploded = explode('|', $filter);
//            $func_name = $exploded[0];
//            $func_args = $exploded;
//            $func_args[0] = $value;
//            if (!method_exists(__CLASS__, $func_name)) {
//                throw new Exceptions\LoggedException("Próba użycia niezdefiniowanego filtra '{$func_name}'!");
//            }
//            $value = call_user_func_array('self::' . $func_name, $func_args);
//        }
//        return $value;
    }

    /**
     * @param $sValue
     * @param $iMaxLength
     * @return bool
     */
    public static function MaxLen($sValue, $iMaxLength)
    {
        return mb_strlen($sValue, 'utf-8') <= $iMaxLength;
    }

    /**
     * @param $mValue
     * @return false|int
     */
    public static function IsInt($mValue)
    {
        return preg_match('/^\d+$/', $mValue);
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsFloat($mValue)
    {
        $test_value = str_replace(',', '.', $mValue);

        if (!is_scalar($test_value)) {
            return false;
        }

        $type = gettype($test_value);

        if ($type === "float") {
            return true;
        } else {
            return preg_match("/^[+-]?(([0-9]+)|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*))$/", $test_value) === 1;
        }
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNInt($mValue)
    {
        return (self::IsNullOrEmpty($mValue) || self::IsInt($mValue));
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNFloat($mValue)
    {
        return (self::IsNullOrEmpty($mValue) || self::IsFloat($mValue));
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNullOrEmpty($mValue)
    {
        return ($mValue === null || $mValue === '');
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNotNullOrEmpty($mValue)
    {
        return !self::IsNullOrEmpty($mValue);
    }

    /**
     * @param $sValue
     * @return false|int
     */
    public static function IsDateString($sValue)
    {
        return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $sValue);
    }

    /**
     * @param $sValue
     * @return bool
     */
    public static function IsNDateString($sValue)
    {
        return (self::IsNullOrEmpty($sValue) || self::IsDateString($sValue));
    }

    /**
     * @param $sValue
     * @return false|int
     */
    public static function IsHMString($sValue)
    {
        return preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $sValue);
    }

    /**
     * @param $mValue
     * @param $aArray
     * @return bool
     */
    public static function IsInArray($mValue, $aArray)
    {
        return is_array($aArray) && $aArray && in_array($mValue, $aArray);
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsZeroOrGreater($mValue)
    {
        return ((float)$mValue) >= 0;
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNZeroOrGreater($mValue)
    {
        return (self::IsNullOrEmpty($mValue) || self::IsZeroOrGreater($mValue));
    }

    public static function IsGreaterThanZero($mValue)
    {
        return ((float)$mValue) > 0;
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNGreaterThanZero($mValue)
    {
        return (self::IsNullOrEmpty($mValue) || self::IsGreaterThanZero($mValue));
    }

    /**
     * @param $sValue
     * @return false|int
     */
    public static function IsPlZipCode($sValue)
    {
        return preg_match('/^[0-9]{2}-?[0-9]{3}$/', $sValue);
    }

    /**
     * @param $mValue
     * @return bool
     */
    public static function IsNPlZipCode($mValue)
    {
        return (self::IsNullOrEmpty($mValue) || self::IsPlZipCode($mValue));
    }
}

?>