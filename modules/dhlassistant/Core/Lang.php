<?php

namespace DhlAssistant\Core;

use DhlAssistant;

class Lang
{
    protected static $S_aTranslationTables = array();

    /**
     * @param $sLangCode
     * @return bool
     */
    protected static function _LoadLang($sLangCode)
    {
        if (isset(self::$S_aTranslationTables[$sLangCode])) {
            return true;
        }

        $filename = DhlAssistant\Core::$BASEDIR . 'Langs/' . $sLangCode . '.php';

        if (!file_exists($filename)) {
            return false;
        }

        $lang = [];
        require_once($filename);
        self::$S_aTranslationTables[$sLangCode] = $lang;
        unset($lang);

        return true;
    }

    /**
     * @param $sString
     * @param null $sLangCode
     * @return mixed
     * @throws Exceptions\LoggedException
     */
    public static function Translate($sString, $sLangCode = null)
    {
        if ($sLangCode === null) {
            $sLangCode = DhlAssistant\Wrappers\ConfigWrapper::Get('DefaultLang');
        }

        if (!self::_LoadLang($sLangCode)) {
            return $sString;
        }

        if (!isset(self::$S_aTranslationTables[$sLangCode][$sString])) {
            return $sString;
        }

        return self::$S_aTranslationTables[$sLangCode][$sString];
    }

    public static function T($sString, $sLangCode = null)
    {
        return self::Translate($sString, $sLangCode);
    }
}

?>