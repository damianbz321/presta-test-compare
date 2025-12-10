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

/**
 * Backward function compatibility
 * Need to be called for each module in 1.4
 */

// Get out if the context is already defined
if (!in_array('Context', get_declared_classes()))
    require_once(dirname(__FILE__) . '/Context.php');

if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
    require_once(dirname(__FILE__) . '/alias.php');
}
// If not under an object we don't have to set the context
if (!isset($this))
    return;
else if (isset($this->context)) {
    // If we are under an 1.5 version and backoffice, we have to set some backward variable
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && isset($this->context->employee->id) && $this->context->employee->id) {
        global $currentIndex;
        $currentIndex = AdminController::$currentIndex;
    }
    return;
}

$this->context = Context::getContext();
$this->smarty = $this->context->smarty;