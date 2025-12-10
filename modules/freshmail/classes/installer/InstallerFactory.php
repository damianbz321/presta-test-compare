<?php

namespace FreshMail\Installer;

use FreshMail\Interfaces\Installer;
use Module;

class InstallerFactory
{
    public static function getInstaller(Module $module): Installer
    {
        return new \FreshMail\Installer($module);
    }
}
