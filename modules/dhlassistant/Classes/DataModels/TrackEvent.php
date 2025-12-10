<?php

namespace DhlAssistant\Classes\DataModels;

class TrackEvent
{
    public $Description;
    public $Terminal;
    public $Timestamp;

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}

?>