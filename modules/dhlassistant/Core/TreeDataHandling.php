<?php

namespace DhlAssistant\Core;

use DhlAssistant\Core\Interfaces;

class TreeDataHandling
{
    /**
     * @param $aTreeData
     * @param string $sPrefix
     * @return array
     */
    public static function TreeData2FlatData($aTreeData, $sPrefix = '')
    {
        $result = [];

        if (is_array($aTreeData) && $aTreeData) {
            foreach ($aTreeData as $key => $value) {
                if (is_array($value)) {
                    $result = array_merge($result, self::TreeData2FlatData($value, $sPrefix . $key . ':'));
                } else {
                    $result[$sPrefix . $key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param $aFlatData
     * @return array
     */
    public static function FlatData2TreeData($aFlatData)
    {
        $nodes = [];
        $result = [];

        if (is_array($aFlatData) && $aFlatData) {
            foreach ($aFlatData as $key => $value) {
                $pos = strpos($key, ':');

                if ($pos !== false) {
                    $prefix = substr($key, 0, $pos);
                    $new_key = substr($key, $pos + 1);

                    if (!isset($nodes[$prefix])) {
                        $nodes[$prefix] = array();
                    }

                    $nodes[$prefix][$new_key] = $value;
                } else {
                    $result[$key] = $value;
                }
            }

            if ($nodes) {
                foreach ($nodes as $prefix => $values) {
                    $result[$prefix] = self::FlatData2TreeData($values);
                }
            }
        }

        return $result;
    }
}

?>