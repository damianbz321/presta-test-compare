<?php

namespace FreshMail\Interfaces;

interface Installer
{

    public function install(): bool;

    public function uninstall(): bool;
}