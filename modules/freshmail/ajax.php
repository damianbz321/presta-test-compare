<?php

// todo: change dir
require_once __DIR__ . '/../../config/config.inc.php';
#require_once __DIR__.'/../../config/config.inc.php';

$smarty = Context::getContext()->smarty;


$smarty->display(_PS_MODULE_DIR_ . 'freshmail/views/templates/plugin_info.tpl');