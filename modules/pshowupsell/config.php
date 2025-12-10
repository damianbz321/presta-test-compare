<?php

/**
 * File from https://prestashow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @authors     PrestaShow.pl <kontakt@prestashow.pl>
 * @copyright   2018 PrestaShow.pl
 * @license     https://prestashow.pl/license
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

function smarty_function_getCrossSellRuleName($params, Smarty_Internal_Template $template)
{
    return \Prestashow\PShowUpsell\Repository\CrossSell\RuleRepository::getNameById($params['rule_id']);
}

function smarty_function_appendToArray($params, Smarty_Internal_Template $template)
{
    $array = $template->getTemplateVars($params['array_name']);
    if (isset($params['append_array'])) {
        $array[] = $template->getTemplateVars($params['append_array']);
    } else {
        $array[$params['key']] = $params['value'];
    }
    $template->assign($params['array_name'], $array);
}