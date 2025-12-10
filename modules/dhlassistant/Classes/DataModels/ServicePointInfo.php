<?php

namespace DhlAssistant\Classes\DataModels;

class ServicePointInfo
{
    public $Name = '';
    public $Description = '';

    /**
     * @var Address
     */
    public $Address = null;
    public $Lat = '';
    public $Lon = '';
    public $ParcelIdent = '';
    public $OpeningHours = [];
    public $WorkInHoliday = false;
}

?>