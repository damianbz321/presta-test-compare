<?php if (!isset($is_template)) {
    die();
} ?><!DOCTYPE html>
<?php

use DhlAssistant\Core;
use DhlAssistant\Wrappers;

$enums = new DhlAssistant\Classes\Dhl\Enums\SettingsUserData();
?>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php
        echo Wrappers\ConfigWrapper::Get('FullName'); ?>
        - <?php $enums->OtherTranslation('GeneralErrorValue'); ?>
    </title>
    <link
            rel="stylesheet"
            href="<?php echo Wrappers\ConfigWrapper::Get('BaseUrl') . 'Media/Css/Styles.css'; ?>">
    <script
            type="text/javascript"
            src="<?php echo Wrappers\ConfigWrapper::Get('BaseUrl') . 'Media/Js/jquery-1.11.0.min.js'; ?>">
    </script>
</head>
<body>
<?php
//
/* @var $exception \Exception */

$exception = $aVars['Exception'];
echo '<div class="general_errors bootstrap">' . "\n";
echo '<div class="error alert alert-danger">' .
    $enums->OtherTranslation('GeneralErrorValue')
    . htmlspecialchars($exception->getMessage()) .
    '</div>'
    . "\n";

if (Core::$DEBUG) {
    echo '<div class="trace">' . htmlspecialchars($exception->getTraceAsString()) . '</div>' . "\n";
}
echo '</div>' . "\n";
?>
<script type="text/javascript"
        src="<?php echo Wrappers\ConfigWrapper::Get('BaseUrl'); ?>
        Media/Js/iframeResizer.contentWindow.min.js">
</script>
</body>
</html>
