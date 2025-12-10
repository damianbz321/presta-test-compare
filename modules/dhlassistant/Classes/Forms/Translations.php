<?php

namespace DhlAssistant\Classes\Forms;

use Context;

class Translations extends \Module
{
    /**
     * @param null $name
     * @param Context|null $context
     */
    public function __construct($name = null, Context $context = null)
    {
        $this->name = 'dhlassistant';

        parent::__construct($name, $context);
    }
}
