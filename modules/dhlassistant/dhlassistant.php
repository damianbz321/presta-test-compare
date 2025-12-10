<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    require 'dhlassistant_wrapped.php';
} else {
    require 'dhlassistant_phpincompat.php';
}
