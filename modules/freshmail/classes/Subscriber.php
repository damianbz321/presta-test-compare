<?php

namespace FreshMail;

use Validate;

class Subscriber
{
    public $email = '';

    public $custom_fields = [];

    public $state = 1;

    public $list = '';

    public function __construct($email)
    {
        if (Validate::isEmail($email)) {
            $this->email = $email;
        }
    }

    public function addCustomField($name, $value)
    {
        $this->custom_fields[$name] = $value;
    }

}