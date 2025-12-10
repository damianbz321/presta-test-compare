<?php

namespace FreshMail\Sender;

class Email{
    public $email;

    public $name;

    public $lastname;

    public function __construct($email, $name = '', $lastname = '')
    {
        $this->email = $email;
        $this->name = $name;
        $this->lastname = $lastname;
    }
}
