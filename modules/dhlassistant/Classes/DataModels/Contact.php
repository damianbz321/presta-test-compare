<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;

class Contact extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Name = '';
    public $Phone = '';
    public $Email = '';

    protected static $S_aDataFields = [
        'Db' => ['Name', 'Phone', 'Email'],
        'PostGet' => ['Name', 'Phone', 'Email'],
        'PostSet' => ['Name', 'Phone', 'Email'],
    ];

    protected static $S_GetFilters = [];
    protected static $S_SetFilters = [];

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
    }

    /**
     * @return bool
     */
    public function IsFilled()
    {
        return (strlen($this->Name) > 0) || (strlen($this->Phone) > 0) || (strlen($this->Email) > 0);
    }

    /**
     * @return Models\ValidationResult
     */
    public function Validate()
    {
        $result = new Models\ValidationResult();

        return $result;
    }
}

?>