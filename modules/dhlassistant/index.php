<?php

namespace DhlAssistant;

require_once 'Core.php';

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Classes\Controllers;

$controller_name = isset($_GET['controller']) && strlen($_GET['controller']) > 0 ? $_GET['controller'] : 'Index';
$controller_class = __NAMESPACE__ . '\Classes\Controllers\\' . $controller_name;

if (!class_exists($controller_class)) {
    http_response_code(404);
    echo '404';
    exit();
}

/* @var $controller Models\Controller */
$controller = new $controller_class();
Core\Storage::Add('RulingController', $controller, false);

try {
    Wrappers\SourceWrapper::CheckIsModuleActive();
    Wrappers\SourceWrapper::CheckIsUserAuthenticated();
} catch (\Exception $Ex) {
    http_response_code(403);
    echo '403';
    exit();
}

try {
    $controller->Go();
} catch (\Exception $Ex) {
    echo Core\Template::Render('GeneralError', array('Exception' => $Ex));
// 	echo 'General Error: '.$Ex->getMessage()."\n";
}

?>