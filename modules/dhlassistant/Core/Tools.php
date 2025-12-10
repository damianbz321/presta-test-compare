<?php

namespace DhlAssistant\Core;

class Tools
{
    /**
     * @param $sNamespaceClassString
     * @return string
     */
    public static function GetNamespaceString($sNamespaceClassString)
    {
        $exploded = explode('\\', $sNamespaceClassString);
        unset($exploded[count($exploded) - 1]);

        return implode('\\', $exploded);
    }

    /**
     * @param $sNamespaceClassString
     * @return mixed|string
     */
    public static function GetClassName($sNamespaceClassString)
    {
        $exploded = explode('\\', $sNamespaceClassString);

        return ($exploded[count($exploded) - 1]);
    }

    /**
     * @param $aArrayToSort
     * @param $aExampleArray
     * @param bool $bWithOverflow
     * @return array|mixed
     */
    public static function ArraySortWithExampleByValue($aArrayToSort, $aExampleArray, $bWithOverflow = true)
    {
        $result = $aExampleArray;

        if (!$aExampleArray || !$aArrayToSort) {
            return $result;
        }

        foreach ($aExampleArray as $key => $example_value) {
            if (!in_array($example_value, $aArrayToSort)) {
                unset ($result[$key]);
            }
        }


        if ($bWithOverflow) {
            $result = array_merge($result, array_diff($aArrayToSort, $result));
        }

        return $result;
    }

    /**
     * @param $aArrayToSort
     * @param $aExampleArray
     * @param bool $bWithOverflow
     * @return array
     */
    public static function ArraySortWithExampleByKey($aArrayToSort, $aExampleArray, $bWithOverflow = true)
    {
        $result = [];

        if (!$aExampleArray || !$aArrayToSort) {
            return $result;
        }

        foreach ($aExampleArray as $example_key) {
            if (isset($aArrayToSort[$example_key])) {
                $result[$example_key] = $aArrayToSort[$example_key];
                unset ($aArrayToSort[$example_key]);
            }
        }

        if ($bWithOverflow && $aArrayToSort) {
            foreach ($aArrayToSort as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param $sTarget
     * @param string $sCode
     */
    public static function Redirect($sTarget, $sCode = '302')
    {
        header("Location: $sTarget", true, $sCode);
        exit;
    }
}

?>