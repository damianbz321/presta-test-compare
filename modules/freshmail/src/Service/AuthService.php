<?php

namespace FreshMail\Service;

use Configuration;
use Context;
use Dispatcher;
use Tools;

class AuthService
{
    public static function check()
    {
        $key = Configuration::get('FRESHMAIL_API_KEY');
        $keySecrect = Configuration::get('FRESHMAIL_API_SECRECT_KEY');
        if (empty($key) || empty($keySecrect)) {
            $controller = 'FreshMailApi';
            $id_lang = Context::getContext()->language->id;
            $params = array('token' => Tools::getAdminTokenLite($controller));
            $url = Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, true);
            Tools::redirectAdmin($url);
        }
    }
}
