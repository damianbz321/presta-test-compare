<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!function_exists('fd')) {
    function fd($var)
    {
        return (Tools::fd($var));
    }
}
if (!function_exists('p')) {
    function p($var)
    {
        return (Tools::p($var));
    }
}
if (!function_exists('d')) {
    function d($var)
    {
        Tools::d($var);
    }
}
if (!function_exists('ppp')) {
    function ppp($var)
    {
        return (Tools::p($var));
    }
}
if (!function_exists('ppp')) {
    function ppp($var)
    {
        Tools::d($var);
    }
}
if (!function_exists('epr')) {
    function epr($var, $message_type = null, $destination = null, $extra_headers = null)
    {
        return Tools::error_log($var, $message_type, $destination, $extra_headers);
    }
}
if (!function_exists('pSQL')) {
    /**
     * Sanitize data which will be injected into SQL query
     *
     * @param string $string SQL data which will be injected into SQL query
     * @param bool $htmlOK Does data contain HTML code ? (optional)
     * @return string Sanitized data
     */
    function pSQL($string, $htmlOK = false)
    {
        return Db::getInstance()->escape($string, $htmlOK);
    }
}
if (!function_exists('bqSQL')) {
    function bqSQL($string)
    {
        return str_replace('`', '\`', pSQL($string));
    }
}
if (!function_exists('displayFatalError')) {
    function displayFatalError()
    {
        $error = null;
        if (function_exists('error_get_last')) {
            $error = error_get_last();
        }
        if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR))) {
            echo '[PrestaShop] Fatal error in module file :' . $error['file'] . ':<br />' . $error['message'];
        }
    }
}
if (!function_exists('nl2br2')) {
    /**
     * @deprecated
     */
    function nl2br2($string)
    {
        Tools::displayAsDeprecated();
        return Tools::nl2br($string);
    }
}
